<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;

/**
 * Gera cobrança PIX no formato "Copia e Cola" (BR Code / EMV),
 * seguindo o padrão do Banco Central — não usa nenhuma API de banco,
 * é só codificação de dados. Funciona com qualquer chave PIX válida
 * (CPF, CNPJ, e-mail, telefone ou chave aleatória).
 *
 * A confirmação do pagamento é manual: o cliente clica "Já paguei"
 * (auto-declaração) e o lojista confere no extrato do próprio banco
 * antes de confirmar o pedido no admin.
 */
class PixManualGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, StorePaymentMethod $method): array
    {
        $credentials = $method->credentials ?? [];

        $pixKey = $credentials['pix_key'] ?? '';
        $holderName = $credentials['holder_name'] ?? $order->store->name ?? 'LOJA';
        $city = $credentials['city'] ?? 'SAO PAULO';

        $txid = 'PED' . str_pad((string) $order->id, 8, '0', STR_PAD_LEFT);

        $payload = $this->buildPayload(
            pixKey: $pixKey,
            amount: (float) $order->total,
            merchantName: $holderName,
            merchantCity: $city,
            txid: $txid,
        );

        return [
            'provider' => 'pix_manual',
            'pix_key' => $pixKey,
            'holder_name' => $holderName,
            'amount' => $order->total,
            'txid' => $txid,
            'copy_paste' => $payload,
        ];
    }

    /**
     * Monta o payload EMV do PIX (BR Code), incluindo o CRC16 final.
     */
    protected function buildPayload(
        string $pixKey,
        float $amount,
        string $merchantName,
        string $merchantCity,
        string $txid,
    ): string {

        $merchantAccount =
            $this->field('00', 'br.gov.bcb.pix') .
            $this->field('01', $pixKey);

        $additionalData = $this->field('05', substr($txid, 0, 25));

        $payload =
            $this->field('00', '01') .
            $this->field('26', $merchantAccount) .
            $this->field('52', '0000') .
            $this->field('53', '986') .
            $this->field('54', number_format($amount, 2, '.', '')) .
            $this->field('58', 'BR') .
            $this->field('59', $this->sanitize($merchantName, 25)) .
            $this->field('60', $this->sanitize($merchantCity, 15)) .
            $this->field('62', $additionalData);

        // ID 63 (CRC16) — o valor do CRC é calculado sobre a string
        // já incluindo "6304" no final (id + tamanho fixo do CRC).
        $payload .= '6304';

        $crc = strtoupper(str_pad(dechex($this->crc16($payload)), 4, '0', STR_PAD_LEFT));

        return $payload . $crc;
    }

    protected function field(string $id, string $value): string
    {
        return $id . str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    /**
     * Remove acentos e caracteres especiais — o padrão EMV do PIX
     * espera texto simples (ASCII), leitores de QR mais rígidos
     * podem rejeitar acentuação.
     */
    protected function sanitize(string $value, int $maxLength): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value) ?: $value;
        $value = preg_replace('/[^A-Za-z0-9 ]/', '', $value);

        return strtoupper(substr($value, 0, $maxLength));
    }

    /**
     * CRC16-CCITT (polinômio 0x1021, valor inicial 0xFFFF) — exigido
     * pelo padrão EMV para validar a integridade do payload.
     */
    protected function crc16(string $payload): int
    {
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($payload); $i++) {

            $crc ^= (ord($payload[$i]) << 8);

            for ($j = 0; $j < 8; $j++) {

                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return $crc;
    }
}
