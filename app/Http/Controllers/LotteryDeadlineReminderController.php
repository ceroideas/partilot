<?php

namespace App\Http\Controllers;

use App\Services\LotteryDeadlineReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LotteryDeadlineReminderController extends Controller
{
    public function dismiss(Request $request, LotteryDeadlineReminderService $service): JsonResponse
    {
        $validated = $request->validate([
            'alerts' => ['required', 'array', 'min:1'],
            'alerts.*' => ['required', 'string', 'max:64'],
        ]);

        $service->dismissModalAlertsForUser($request->user(), $validated['alerts']);

        return response()->json(['ok' => true]);
    }
}
