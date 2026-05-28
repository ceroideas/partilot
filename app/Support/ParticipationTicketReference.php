<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Referencia numérica de participación (21 dígitos) para tickets/QR.
 *
 * Estructura:
 *   [1-4]   ID entidad (4 dígitos, ceros a la izquierda)
 *   [5-8]   ID reserva (4 dígitos)
 *   [9-18]  Bloque aleatorio de 10 dígitos (sustituye el antiguo timestamp Unix)
 *   [19-20] Extensión aleatoria (2 dígitos)
 *   [21]    Dígito de control (suma ponderada 2-1 + módulo 10, como en NIF/CIF)
 *
 * El número de participación dentro del set (campo tickets.n) no se incluye en la
 * referencia para evitar secuencias adivinables (…001, …002).
 */
class ParticipationTicketReference
{
    public const LENGTH = 21;

    public const ENTITY_LEN = 4;

    public const RESERVE_LEN = 4;

    public const RANDOM_BLOCK_LEN = 10;

    public const RANDOM_EXT_LEN = 2;

    /**
     * Genera una referencia válida de 21 dígitos.
     */
    public static function generate(int $entityId, int $reserveId): string
    {
        $entityId = max(0, min(9999, $entityId));
        $reserveId = max(0, min(9999, $reserveId));

        $payload = str_pad((string) $entityId, self::ENTITY_LEN, '0', STR_PAD_LEFT)
            . str_pad((string) $reserveId, self::RESERVE_LEN, '0', STR_PAD_LEFT)
            . self::randomDigits(self::RANDOM_BLOCK_LEN)
            . self::randomDigits(self::RANDOM_EXT_LEN);

        return $payload . (string) self::computeCheckDigit($payload);
    }

    /**
     * Valida formato y dígito de control.
     */
    public static function isValid(string $reference): bool
    {
        $reference = self::normalize($reference);
        if ($reference === null || strlen($reference) !== self::LENGTH) {
            return false;
        }

        if (!ctype_digit($reference)) {
            return false;
        }

        $payload = substr($reference, 0, self::LENGTH - 1);
        $check = (int) substr($reference, self::LENGTH - 1, 1);

        return self::computeCheckDigit($payload) === $check;
    }

    /**
     * Normaliza entrada (solo dígitos). Devuelve null si queda vacío.
     */
    public static function normalize(?string $reference): ?string
    {
        if ($reference === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', trim($reference));

        return ($digits === '' || $digits === null) ? null : $digits;
    }

    /**
     * Dígito de control sobre 20 dígitos: pesos alternos 2 y 1 (de derecha a izquierda),
     * suma de dígitos del producto, módulo 10 (equivalente al control de documentos españoles).
     */
    public static function computeCheckDigit(string $twentyDigits): int
    {
        if (!preg_match('/^\d{20}$/', $twentyDigits)) {
            throw new InvalidArgumentException('Se requieren exactamente 20 dígitos para calcular el control.');
        }

        $sum = 0;
        $digits = str_split($twentyDigits);
        $length = count($digits);

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int) $digits[$i];
            $positionFromRight = $length - $i;
            if ($positionFromRight % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = intdiv($digit, 10) + ($digit % 10);
                }
            }
            $sum += $digit;
        }

        return (10 - ($sum % 10)) % 10;
    }

    private static function randomDigits(int $count): string
    {
        $out = '';
        for ($i = 0; $i < $count; $i++) {
            $out .= (string) random_int(0, 9);
        }

        return $out;
    }
}
