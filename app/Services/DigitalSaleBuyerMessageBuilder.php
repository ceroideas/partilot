<?php

namespace App\Services;

use App\Models\PendingDigitalSale;

class DigitalSaleBuyerMessageBuilder
{
    public static function build(PendingDigitalSale $pending): string
    {
        $pending->ensureLinkCode();
        $pending->loadMissing(['entity', 'lottery']);

        $qty = (int) $pending->quantity;
        $participacionesTexto = $qty === 1
            ? '1 participación digital'
            : "{$qty} participaciones digitales";

        $entidad = $pending->entity?->name ?? '—';
        $sorteo = self::lotteryLabel($pending);

        $lines = [
            "Hola. Te he vendido {$participacionesTexto} de {$entidad} ({$sorteo}).",
            '',
            'El código para reclamarlas en la app Partilot es: '.$pending->link_code,
            '',
            'En la app: Cartera → Vincular con código.',
            'Puedes registrarte desde aqui: '.$pending->registrationUrlForShare(),
        ];

        return implode("\n", $lines);
    }

    public static function lotteryLabel(PendingDigitalSale $pending): string
    {
        $lottery = $pending->lottery;
        if (! $lottery) {
            return '—';
        }

        $name = trim((string) ($lottery->name ?? ''));
        if ($name !== '') {
            return $name;
        }

        $lottery->loadMissing('lotteryType');
        $typeName = trim((string) ($lottery->lotteryType->name ?? ''));

        return $typeName !== '' ? $typeName : '—';
    }
}
