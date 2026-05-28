<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Lottery;
use App\Models\Reserve;
use App\Models\Set;
use App\Services\LotteryDrawDateGuardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait HandlesLotteryDrawDateGuard
{
    protected function lotteryDrawDateGuard(): LotteryDrawDateGuardService
    {
        return app(LotteryDrawDateGuardService::class);
    }

    protected function redirectIfLotteryDrawDateBlocked(
        ?Lottery $lottery,
        ?string $fallbackRoute = null
    ): ?RedirectResponse {
        $message = $this->lotteryDrawDateGuard()->mutationDeniedMessage($lottery);
        if ($message === null) {
            return null;
        }

        if ($fallbackRoute) {
            return redirect()->route($fallbackRoute)->with('error', $message);
        }

        return redirect()->back()->with('error', $message);
    }

    protected function jsonIfLotteryDrawDateBlocked(?Lottery $lottery): ?JsonResponse
    {
        $message = $this->lotteryDrawDateGuard()->mutationDeniedMessage($lottery);
        if ($message === null) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => 'LOTTERY_DRAW_DATE_PASSED',
        ], 422);
    }

    protected function redirectIfReserveLotteryBlocked(
        ?Reserve $reserve,
        ?string $fallbackRoute = null
    ): ?RedirectResponse {
        $reserve?->loadMissing('lottery');

        return $this->redirectIfLotteryDrawDateBlocked($reserve?->lottery, $fallbackRoute);
    }

    protected function redirectIfSetLotteryBlocked(
        ?Set $set,
        ?string $fallbackRoute = null
    ): ?RedirectResponse {
        $set?->loadMissing('reserve.lottery');

        return $this->redirectIfLotteryDrawDateBlocked($set?->reserve?->lottery, $fallbackRoute);
    }
}
