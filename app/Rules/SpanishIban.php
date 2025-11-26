<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SpanishIban implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            $fail('El campo :attribute es obligatorio.');
            return;
        }

        // Eliminar espacios y convertir a mayúsculas
        $iban = strtoupper(str_replace(' ', '', trim($value)));

        // Debe empezar con ES
        if (!str_starts_with($iban, 'ES')) {
            $fail('El IBAN debe empezar con ES.');
            return;
        }

        // Debe tener exactamente 24 caracteres (ES + 22 dígitos)
        if (strlen($iban) !== 24) {
            $fail('El IBAN español debe tener 24 caracteres (ES + 22 dígitos).');
            return;
        }

        // Verificar que después de ES solo hay dígitos
        $digits = substr($iban, 2);
        if (!ctype_digit($digits)) {
            $fail('El IBAN solo debe contener letras ES seguidas de dígitos.');
            return;
        }

        // Validar el checksum del IBAN
        if (!$this->validateIbanChecksum($iban)) {
            $fail('El IBAN no es válido. Por favor, verifique los dígitos de control.');
            return;
        }
    }

    /**
     * Validar el checksum del IBAN usando el algoritmo MOD-97-10
     */
    private function validateIbanChecksum(string $iban): bool
    {
        // Mover los 4 primeros caracteres (código país + dígitos de control) al final
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Convertir letras a números (A=10, B=11, ..., Z=35)
        $numeric = '';
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numeric .= (ord($char) - ord('A') + 10);
            } else {
                $numeric .= $char;
            }
        }

        // Calcular MOD 97
        $remainder = '';
        for ($i = 0; $i < strlen($numeric); $i++) {
            $remainder = ($remainder . $numeric[$i]) % 97;
        }

        // El resto debe ser 1 para un IBAN válido
        return $remainder == 1;
    }
}
