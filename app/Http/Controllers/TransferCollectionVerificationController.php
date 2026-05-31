<?php

namespace App\Http\Controllers;

use App\Models\ParticipationCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransferCollectionVerificationController extends Controller
{
    public function confirm(string $token)
    {
        $collection = ParticipationCollection::where('confirmation_token', $token)->first();

        if (!$collection || !$collection->isPendingVerification()) {
            return view('transfer-collection.confirmation-result', [
                'success' => false,
                'title' => 'Enlace no válido',
                'message' => 'El enlace de confirmación no es válido, ha expirado o ya fue utilizado.',
            ]);
        }

        if ($collection->isExpired()) {
            $collection->markAsExpired();

            return view('transfer-collection.confirmation-result', [
                'success' => false,
                'title' => 'Solicitud expirada',
                'message' => 'Esta solicitud de cobro ha expirado. Vuelve a la app para crear una nueva solicitud.',
            ]);
        }

        try {
            $collection->confirmVerification();
        } catch (\Throwable $e) {
            Log::error('Error confirmando cobro por transferencia: ' . $e->getMessage(), [
                'collection_id' => $collection->id,
            ]);

            return view('transfer-collection.confirmation-result', [
                'success' => false,
                'title' => 'Error',
                'message' => 'No se pudo confirmar la solicitud. Inténtalo de nuevo o contacta con soporte.',
            ]);
        }

        return view('transfer-collection.confirmation-result', [
            'success' => true,
            'title' => 'Solicitud confirmada',
            'message' => 'Tu solicitud de cobro por transferencia ha sido confirmada. La entidad gestionará el pago en los próximos días.',
            'collection' => $collection,
        ]);
    }

    public function cancel(string $token)
    {
        $collection = ParticipationCollection::where('confirmation_token', $token)->first();

        if (!$collection || !$collection->isPendingVerification()) {
            return view('transfer-collection.confirmation-result', [
                'success' => false,
                'title' => 'Enlace no válido',
                'message' => 'El enlace no es válido o la solicitud ya fue procesada.',
            ]);
        }

        $collection->cancelVerification();

        return view('transfer-collection.confirmation-result', [
            'success' => true,
            'title' => 'Solicitud cancelada',
            'message' => 'Has cancelado la solicitud de cobro. Tus participaciones vuelven a estar disponibles en la app.',
        ]);
    }
}
