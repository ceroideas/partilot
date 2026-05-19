<?php

namespace App\Support;

use App\Models\PendingDigitalSale;

/**
 * Código alfanumérico para vincular ventas digitales pendientes sin depender del email.
 * Excluye 0, o y O para evitar confusiones al dictar o escribir el código.
 */
class PendingDigitalSaleLinkCode
{
    private const CHARSET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz';

    public static function generateUnique(?int $length = null): string
    {
        $length = $length ?? (int) config('digital_sale.link_code_length', 6);
        $length = max(5, min(8, $length));
        $charsetLen = strlen(self::CHARSET);

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= self::CHARSET[random_int(0, $charsetLen - 1)];
            }
        } while (PendingDigitalSale::where('link_code', $code)->exists());

        return $code;
    }

    public static function normalizeInput(string $code): string
    {
        return preg_replace('/[\s\-_]/', '', trim($code)) ?? '';
    }

    public static function isValidFormat(string $code): bool
    {
        $code = self::normalizeInput($code);
        $len = strlen($code);
        if ($len < 5 || $len > 8) {
            return false;
        }

        if (preg_match('/[0oO]/', $code)) {
            return false;
        }

        return (bool) preg_match('/^['.preg_quote(self::CHARSET, '/').']+$/', $code);
    }
}
