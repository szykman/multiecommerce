<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida CPF (11 dígitos) ou CNPJ (14 dígitos) com o algoritmo real
 * de dígito verificador — não é só checagem de tamanho. Aceita o
 * valor com ou sem máscara (o próprio rule remove pontuação antes
 * de validar).
 *
 * Usado no cadastro do cliente e na edição de perfil, pois o
 * documento é obrigatório para emissão de boleto (Mercado Pago
 * exige `identification.number` no payer).
 */
class ValidCpfCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) === 11) {

            if (! $this->isValidCpf($digits)) {
                $fail('CPF inválido.');
            }

            return;
        }

        if (strlen($digits) === 14) {

            if (! $this->isValidCnpj($digits)) {
                $fail('CNPJ inválido.');
            }

            return;
        }

        $fail('Informe um CPF (11 dígitos) ou CNPJ (14 dígitos) válido.');
    }

    protected function isValidCpf(string $cpf): bool
    {
        // Sequências repetidas (000.000.000-00, 111.111.111-11 etc.)
        // passam na conta dos dígitos verificadores mas não são CPFs
        // válidos de verdade.
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {

            $sum = 0;

            for ($i = 0; $i < $t; $i++) {
                $sum += ((int) $cpf[$i]) * (($t + 1) - $i);
            }

            $digit = (($sum * 10) % 11) % 10;

            if ((int) $cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }

    protected function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $calcDigit = function (string $base) {

            $weights = strlen($base) === 12
                ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
                : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            $sum = 0;

            foreach (str_split($base) as $i => $digit) {
                $sum += ((int) $digit) * $weights[$i];
            }

            $remainder = $sum % 11;

            return $remainder < 2 ? 0 : 11 - $remainder;
        };

        $base = substr($cnpj, 0, 12);

        $digit1 = $calcDigit($base);
        $digit2 = $calcDigit($base.$digit1);

        return $cnpj === $base.$digit1.$digit2;
    }
}
