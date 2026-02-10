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
     * Validar CIF (1 letra + 7 dígitos + 1 letra/dígito).
     * Para tipo G (asociaciones, clubes, administraciones) se aceptan número o letra de control
     * y la variante de cálculo con doble solo en posiciones 0,2,4 (ej. G48123987).
     */
    private function validateCif(string $document): bool
    {
        if (!preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $document)) {
            return false;
        }

        $firstChar = substr($document, 0, 1);
        $number = substr($document, 1, 7);
        $control = substr($document, 8, 1);
        $letters = 'JABCDEFGHI';

        $checkStandard = $this->cifControlDigit($number, [0, 2, 4, 6]);
        $validStandard = $control === (string) $checkStandard || $control === $letters[$checkStandard];

        // A, B, E, H: solo dígito numérico
        if (in_array($firstChar, ['A', 'B', 'E', 'H'])) {
            return $control === (string) $checkStandard;
        }

        // G (asociaciones, clubes, etc.): número o letra; y variante con doble solo 0,2,4
        if ($firstChar === 'G') {
            $checkAlternate = $this->cifControlDigit($number, [0, 2, 4]);
            $validAlternate = $control === (string) $checkAlternate || $control === $letters[$checkAlternate];
            return $validStandard || $validAlternate;
        }

        // Resto: letra de control
        return $control === $letters[$checkStandard];
    }

    /**
     * Calcula el dígito de control CIF duplicando los dígitos en las posiciones indicadas (0-6).
     */
    private function cifControlDigit(string $number, array $doublePositions): int
    {
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $digit = (int) $number[$i];
            if (in_array($i, $doublePositions, true)) {
                $doubled = $digit * 2;
                $sum += (int) ($doubled / 10) + ($doubled % 10);
            } else {
                $sum += $digit;
            }
        }
        return (10 - ($sum % 10)) % 10;
    }
}
