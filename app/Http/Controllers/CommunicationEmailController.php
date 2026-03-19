<?php

namespace App\Http\Controllers;

use App\Models\EmailCommunicationLog;
use App\Services\CommunicationEmailService;

class CommunicationEmailController extends Controller
{
    public function __construct(
        private readonly CommunicationEmailService $communicationEmailService,
    ) {
    }

    public function index()
    {
        $logs = EmailCommunicationLog::query()
            ->orderByDesc('created_at')
            ->limit(300)
            ->get();

        if (auth()->check() && !auth()->user()?->isSuperAdmin()) {
            $accessibleEntityIds = auth()->user()->accessibleEntityIds();

            $logs = $logs->filter(function (EmailCommunicationLog $log) use ($accessibleEntityIds) {
                $contextEntityId = (int) (($log->context['entity_id'] ?? 0) ?: 0);
                return $contextEntityId > 0 && in_array($contextEntityId, $accessibleEntityIds, true);
            })->values();
        }

        return view('communications.index', compact('logs'));
    }

    public function resend(int $id)
    {
        $log = EmailCommunicationLog::query()->findOrFail($id);

        $this->communicationEmailService->resendLog($log);

        return redirect()->route('communications.index')
            ->with('success', 'Email reenviado correctamente (si estaba soportado para reenviar).');
    }

    public function destroy(int $id)
    {
        $log = EmailCommunicationLog::query()->findOrFail($id);
        $log->delete(); // “delete normal” (borrado real)

        return redirect()->route('communications.index')
            ->with('success', 'Registro de comunicación eliminado.');
    }
}

