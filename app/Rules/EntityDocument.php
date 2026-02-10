<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validación de documento de identificación para entidades.
 * Comprueba en este orden: 1) NIF, 2) NIE, 3) TIE, 4) CIF.
 * El CIF de entidades no es el mismo formato que el de sociedades/administraciones;
 * aquí se aceptan NIF, NIE, TIE y CIF con sus respectivas reglas.
 */
class EntityDocument implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || trim($value) === '') {
            return;
        }

        $document = strtoupper(trim(preg_replace('/\s+/', '', $value)));

        // Orden: 1º NIF, 2º NIE, 3º TIE, 4º CIF
        if ($this->validateNif($document)) {
            return;
        }
        if ($this->validateNie($document)) {
            return;
        }
        if ($this->validateTie($document)) {
            return;
        }
        if ($this->validateCif($document)) {
            return;
        }

        $fail('El campo :attribute debe ser un NIF, NIE, TIE o CIF válido.');
    }

    /**
     * NIF: 8 dígitos + 1 letra de control.
     */
    private function validateNif(string $document): bool
    {
        if (!preg_match('/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $document)) {
            return false;
        }
        $number = (int) substr($document, 0, 8);
        $letter = substr($document, 8, 1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        return $letter === $letters[$number % 23];
    }

    /**
     * NIE: X/Y/Z + 7 dígitos + 1 letra de control.
     */
    private function validateNie(string $document): bool
    {
        if (!preg_match('/^[XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $document)) {
            return false;
        }
        $firstChar = substr($document, 0, 1);
        $number = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $firstChar) . substr($document, 1, 7);
        $letter = substr($document, 8, 1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        return $letter === $letters[(int) $number % 23];
    }

    /**
     * TIE: mismo formato que NIE (la TIE utiliza el número NIE como identificador).
     */
    private function validateTie(string $document): bool
    {
        return $this->validateNie($document);
    }

    /**
     * CIF: 1 letra (A–Z excepto I,O) + 7 dígitos + 1 dígito o letra de control.
     * Para tipo G (asociaciones, clubes) se aceptan dos variantes de cálculo del dígito de control.
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

        if (in_array($firstChar, ['A', 'B', 'E', 'H'], true)) {
            return $control === (string) $checkStandard;
        }
        if ($firstChar === 'G') {
            // G: número o letra; además se acepta variante con doble solo en posiciones 0,2,4 (algunos CIF G la usan)
            $checkAlternate = $this->cifControlDigit($number, [0, 2, 4]);
            $validAlternate = $control === (string) $checkAlternate || $control === $letters[$checkAlternate];
            return $validStandard || $validAlternate;
        }
        return $control === $letters[$checkStandard];
    }

    /**
     * Calcula el dígito de control CIF: se duplican los dígitos en las posiciones $doublePositions (0-6).
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
