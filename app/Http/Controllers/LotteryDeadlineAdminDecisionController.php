<?php

namespace App\Http\Controllers;

use App\Models\LotteryDeadlineAdminDecision;
use App\Services\LotteryDeadlineReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LotteryDeadlineAdminDecisionController extends Controller
{
    public function assumeDebt(Request $request, LotteryDeadlineReminderService $reminderService): JsonResponse
    {
        return $this->storeDecision(
            $request,
            $reminderService,
            LotteryDeadlineAdminDecision::DECISION_ASSUME_DEBT
        );
    }

    public function annul(Request $request, LotteryDeadlineReminderService $reminderService): JsonResponse
    {
        return $this->storeDecision(
            $request,
            $reminderService,
            LotteryDeadlineAdminDecision::DECISION_ANNUL
        );
    }

    private function storeDecision(
        Request $request,
        LotteryDeadlineReminderService $reminderService,
        string $decision
    ): JsonResponse {
        $user = $request->user();

        if (! $user->isAdministration() || $user->isSuperAdmin()) {
            return response()->json(['ok' => false, 'message' => 'No autorizado.'], 403);
        }

        $validated = $request->validate([
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'lottery_id' => ['required', 'integer', 'exists:lotteries,id'],
            'confirm' => ['accepted'],
        ]);

        $entityId = (int) $validated['entity_id'];
        $lotteryId = (int) $validated['lottery_id'];

        if (! $user->canAccessEntity($entityId)) {
            return response()->json(['ok' => false, 'message' => 'Sin acceso a esta entidad.'], 403);
        }

        if (! $reminderService->isAdminDecisionRequired($entityId, $lotteryId)) {
            return response()->json(['ok' => false, 'message' => 'No hay decisión pendiente para este sorteo hoy.'], 422);
        }

        if (LotteryDeadlineAdminDecision::hasDecision($entityId, $lotteryId)) {
            return response()->json(['ok' => false, 'message' => 'Ya existe una decisión registrada.'], 422);
        }

        LotteryDeadlineAdminDecision::create([
            'entity_id' => $entityId,
            'lottery_id' => $lotteryId,
            'decision' => $decision,
            'user_id' => $user->id,
        ]);

        $message = $decision === LotteryDeadlineAdminDecision::DECISION_ASSUME_DEBT
            ? 'Decisión registrada: se asume la deuda. Las participaciones siguen activas.'
            : 'Decisión registrada: anulación de participaciones. El procesamiento se completará en una fase posterior.';

        return response()->json(['ok' => true, 'message' => $message]);
    }
}
