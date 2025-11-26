<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SpanishDocument implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || trim($value) === '') {
            // Si es nullable, permitir vacío
            return;
        }

        $document = strtoupper(trim($value));

        // Intentar validar como NIF, NIE, DNI o CIF
        $isValid = $this->validateNif($document) 
                || $this->validateNie($document) 
                || $this->validateDni($document)
                || $this->validateCif($document);

        if (!$isValid) {
            $fail('El campo :attribute no es un NIF, NIE, DNI o CIF válido.');
        }
    }

    /**
     * Validar NIF (8 dígitos + 1 letra)
     */
    private function validateNif(string $document): bool
    {
        if (!preg_match('/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $document)) {
            return false;
        }

        $number = substr($document, 0, 8);
        $letter = substr($document, 8, 1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $expectedLetter = $letters[$number % 23];

        return $letter === $expectedLetter;
    }

    /**
     * Validar NIE (X/Y/Z + 7 dígitos + 1 letra)
     */
    private function validateNie(string $document): bool
    {
        if (!preg_match('/^[XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $document)) {
            return false;
        }

        $firstChar = substr($document, 0, 1);
        $number = substr($document, 1, 7);
        $letter = substr($document, 8, 1);

        // Reemplazar X, Y, Z por 0, 1, 2
        $number = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $firstChar) . $number;
        
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $expectedLetter = $letters[$number % 23];

        return $letter === $expectedLetter;
    }

    /**
     * Validar DNI (igual que NIF)
     */
    private function validateDni(string $document): bool
    {
        return $this->validateNif($document);
    }

    /**
     * Validar CIF (1 letra + 7 dígitos + 1 letra/dígito)
     */
    private function validateCif(string $document): bool
    {
        if (!preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $document)) {
            return false;
        }

        $firstChar = substr($document, 0, 1);
        $number = substr($document, 1, 7);
        $control = substr($document, 8, 1);

        // Calcular suma de posiciones pares e impares
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $digit = (int)$number[$i];
            if ($i % 2 === 0) {
                // Posiciones impares (0, 2, 4, 6)
                $doubled = $digit * 2;
                $sum += floor($doubled / 10) + ($doubled % 10);
            } else {
                // Posiciones pares (1, 3, 5)
                $sum += $digit;
            }
        }

        $units = $sum % 10;
        $checkDigit = (10 - $units) % 10;

        // Si el primer carácter es A, B, E o H, el dígito de control es numérico
        if (in_array($firstChar, ['A', 'B', 'E', 'H'])) {
            return $control === (string)$checkDigit;
        }

        // Para otros casos, el dígito de control es una letra
        $letters = 'JABCDEFGHI';
        $expectedLetter = $letters[$checkDigit];

        return $control === $expectedLetter;
    }
}
