<?php

namespace App\Services\Shipping;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Calcula frete via API dos Correios, usando o contrato ÚNICO da
 * plataforma (MultiEcommerce) — as credenciais ficam no .env, não
 * por loja. Cada loja só precisa habilitar (correios_enabled) e
 * informar o CEP de origem dela (de onde despacha).
 *
 * Formato confirmado contra a documentação oficial (Manual API
 * Preço dos Correios): o endpoint /preco/v1/nacional espera POST
 * com um "lote" — um idLote + um array parametrosProduto, podendo
 * pedir vários serviços (PAC/SEDEX) numa única chamada.
 *
 * IMPORTANTE: os nomes de campo da RESPOSTA (pcFinal, prazoEntrega,
 * etc.) ainda têm fallback para variações, pois não tivemos acesso
 * a um exemplo de resposta real do seu contrato ainda. Se o preço
 * não aparecer mesmo com a chamada retornando 200, cole a resposta
 * completa (response->body()) que a gente ajusta os nomes de campo
 * exatos.
 */
class CorreiosShippingCalculator implements ShippingCalculatorInterface
{
    protected const AUTH_URL = 'https://api.correios.com.br/token/v1/autentica/cartaopostagem';
    protected const PRICE_URL = 'https://api.correios.com.br/preco/v1/nacional';

    // Códigos de serviço mais comuns — confirme os que estão
    // liberados no seu contrato antes de usar em produção.
    protected array $services = [
        '04510' => 'PAC',
        '04014' => 'SEDEX',
    ];

    public function calculate(Store $store, string $destinationZipcode, string $destinationState, Collection $cartItems): array
    {
        $settings = StoreSetting::where('store_id', $store->id)->first();

        if (! $settings || ! $settings->correios_enabled || ! $settings->origin_zipcode) {
            return [];
        }

        $auth = $this->authenticate();

        if (! $auth) {
            return [];
        }

        [$weight, $dimensions] = $this->calculatePackage($cartItems);

        $originZip = preg_replace('/\D/', '', $settings->origin_zipcode);
        $destinationZip = preg_replace('/\D/', '', $destinationZipcode);

        // Monta o lote com um item por serviço (PAC + SEDEX numa
        // chamada só), cada um com um nuRequisicao único para
        // conseguirmos casar a resposta de volta com o serviço certo.
        $produtos = [];
        $index = 1;

        foreach ($this->services as $code => $label) {

            $produtos[] = [
                'coProduto' => $code,
                'nuRequisicao' => str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'nuDR' => $auth['dr'],
                'cepOrigem' => $originZip,
                'psObjeto' => (string) max(1, round($weight * 1000)), // gramas, nunca zero
                'tpObjeto' => '2', // 2 = pacote/caixa
                'comprimento' => (string) $dimensions['length'],
                'largura' => (string) $dimensions['width'],
                'altura' => (string) $dimensions['height'],
                'cepDestino' => $destinationZip,
            ];

            $index++;
        }

        try {

            $response = Http::withToken($auth['token'])
                ->post(self::PRICE_URL, [
                    'idLote' => '1',
                    'parametrosProduto' => $produtos,
                ]);

            if (! $response->successful()) {

                Log::warning('Correios - resposta não OK ao calcular preço', [
                    'store_id' => $store->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $data = $response->json();

            // Log de depuração temporário — deixa ligado até
            // confirmarmos os nomes de campo certos na resposta real,
            // depois pode remover.
            Log::info('Correios - resposta OK ao calcular preço', ['body' => $data]);

            return $this->parseResponse($data);

        } catch (\Throwable $e) {

            Log::warning('Erro ao calcular frete Correios', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Converte a resposta da API num array de opções pro checkout.
     * Aceita tanto uma resposta em formato de lista quanto um único
     * objeto (algumas versões da API retornam formatos diferentes
     * dependendo de erro parcial/total).
     */
    protected function parseResponse($data): array
    {
        $items = $data;

        // Se vier envelopado numa chave (ex: "resultado"), desembrulha
        if (isset($data['resultado']) && is_array($data['resultado'])) {
            $items = $data['resultado'];
        }

        // Se vier como objeto único em vez de lista, normaliza pra lista
        if (isset($items['coProduto'])) {
            $items = [$items];
        }

        $options = [];

        foreach ((array) $items as $item) {

            if (! is_array($item)) {
                continue;
            }

            $code = $item['coProduto'] ?? null;
            $label = $this->services[$code] ?? null;

            if (! $label) {
                continue;
            }

            // Se o item individual veio com erro (comum em resposta
            // parcial, status 206), pula em vez de quebrar a lista toda.
            if (! empty($item['txErro']) || ! empty($item['msgErro'])) {
                continue;
            }

            $price = $item['pcFinal']
                ?? $item['pcBase']
                ?? $item['valor']
                ?? null;

            if ($price === null) {
                continue;
            }

            $days = $item['prazoEntrega']
                ?? $item['prazoEntregaDias']
                ?? $item['prazo']
                ?? null;

            $options[] = [
                'provider' => 'correios',
                'name' => 'Correios - '.$label,
                'price' => (float) str_replace(',', '.', (string) $price),
                'estimated_days' => $days !== null ? (int) $days : null,
            ];
        }

        return $options;
    }

    /**
     * Autentica e devolve o token + número da DR (diretoria regional)
     * do cartão de postagem, que a API de preço exige por item.
     */
    protected function authenticate(): ?array
    {
        $user = config('services.correios.username');
        $accessCode = config('services.correios.access_code');
        $postcard = config('services.correios.posting_card');

        if (! $user || ! $accessCode || ! $postcard) {
            return null;
        }

        try {

            $response = Http::withBasicAuth($user, $accessCode)
                ->post(self::AUTH_URL, [
                    'numero' => $postcard,
                ]);

            if (! $response->successful()) {

                Log::warning('Correios - resposta não OK ao autenticar', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            return [
                'token' => $data['token'] ?? null,
                'dr' => $data['cartaoPostagem']['dr'] ?? 72,
            ];

        } catch (\Throwable $e) {

            Log::warning('Erro ao autenticar na API dos Correios', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Soma o peso do carrinho e estima uma caixa única simplificada
     * (empacotamento multi-item de verdade é mais complexo — isso
     * é uma aproximação razoável para MVP: soma alturas, usa a
     * maior largura/comprimento entre os itens).
     */
    protected function calculatePackage(Collection $cartItems): array
    {
        $totalWeight = 0.0;

        // Dimensões mínimas aceitas pelos Correios
        $length = 16.0;
        $width = 11.0;
        $height = 2.0;

        foreach ($cartItems as $item) {

            $product = Product::withoutGlobalScopes()->find($item['id']);

            if (! $product) {
                continue;
            }

            $qty = (int) $item['qty'];

            $totalWeight += (float) ($product->weight ?? 0) * $qty;

            $length = max($length, (float) ($product->length ?? 0));
            $width = max($width, (float) ($product->width ?? 0));
            $height += (float) ($product->height ?? 0) * $qty;
        }

        return [$totalWeight, [
            'length' => $length,
            'width' => $width,
            'height' => min($height, 100.0), // limite máximo dos Correios
        ]];
    }
}
