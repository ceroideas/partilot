<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParticipationActivityLog;
use App\Models\Participation;
use App\Models\Seller;
use App\Models\Entity;

class ParticipationActivityLogController extends Controller
{
    /**
     * Obtener el historial de actividades de una participación
     */
    public function getParticipationHistory($participationId)
    {
        try {
            $participation = Participation::findOrFail($participationId);
            
            $activities = ParticipationActivityLog::with(['user', 'seller', 'entity', 'oldSeller', 'newSeller'])
                ->forParticipation($participationId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'activity_type' => $activity->activity_type,
                        'activity_type_text' => $activity->activity_type_text,
                        'activity_badge' => $activity->activity_badge,
                        'description' => $activity->description,
                        'user' => $activity->user ? $activity->user->name : 'Sistema',
                        'seller' => $activity->seller ? $activity->seller->name : null,
                        'entity' => $activity->entity ? $activity->entity->name : null,
                        'old_status' => $activity->old_status,
                        'new_status' => $activity->new_status,
                        'old_seller' => $activity->oldSeller ? $activity->oldSeller->name : null,
                        'new_seller' => $activity->newSeller ? $activity->newSeller->name : null,
                        'metadata' => $activity->metadata,
                        'created_at' => $activity->created_at->format('d/m/Y H:i:s'),
                        'ip_address' => $activity->ip_address,
                    ];
                });

            return response()->json([
                'success' => true,
                'participation' => [
                    'code' => $participation->participation_code,
                    'number' => $participation->participation_number,
                    'status' => $participation->status,
                ],
                'activities' => $activities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener el historial de actividades de un vendedor
     */
    public function getSellerHistory(Request $request, $sellerId)
    {
        try {
            $seller = Seller::findOrFail($sellerId);
            
            $query = ParticipationActivityLog::with(['participation', 'user', 'entity'])
                ->where(function($q) use ($sellerId) {
                    $q->where('seller_id', $sellerId)
                      ->orWhere('old_seller_id', $sellerId)
                      ->orWhere('new_seller_id', $sellerId);
                })
                ->orderBy('created_at', 'desc');

            // Filtros opcionales
            if ($request->has('activity_type')) {
                $query->where('activity_type', $request->activity_type);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $activities = $query->paginate(50);

            return response()->json([
                'success' => true,
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                ],
                'activities' => $activities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial del vendedor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener el historial de actividades de una entidad
     */
    public function getEntityHistory(Request $request, $entityId)
    {
        try {
            $entity = Entity::findOrFail($entityId);
            
            $query = ParticipationActivityLog::with(['participation', 'user', 'seller'])
                ->byEntity($entityId)
                ->orderBy('created_at', 'desc');

            // Filtros opcionales
            if ($request->has('activity_type')) {
                $query->where('activity_type', $request->activity_type);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $activities = $query->paginate(50);

            return response()->json([
                'success' => true,
                'entity' => [
                    'id' => $entity->id,
                    'name' => $entity->name,
                ],
                'activities' => $activities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de la entidad: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de actividad
     */
    public function getActivityStats(Request $request)
    {
        try {
            $query = ParticipationActivityLog::query();

            // Filtros opcionales
            if ($request->has('seller_id')) {
                $query->where('seller_id', $request->seller_id);
            }

            if ($request->has('entity_id')) {
                $query->where('entity_id', $request->entity_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $stats = [
                'total' => $query->count(),
                'by_type' => $query->selectRaw('activity_type, count(*) as count')
                    ->groupBy('activity_type')
                    ->get()
                    ->pluck('count', 'activity_type'),
                'recent_7_days' => ParticipationActivityLog::recent(7)->count(),
                'recent_30_days' => ParticipationActivityLog::recent(30)->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar vista del historial de una participación
     */
    public function show($participationId)
    {
        $participation = Participation::with(['activityLogs.user', 'activityLogs.seller', 'entity', 'set'])
            ->findOrFail($participationId);

        return view('participations.activity_log', compact('participation'));
    }

    /**
     * Obtener actividades recientes (últimos 7 días)
     */
    public function getRecentActivities(Request $request)
    {
        try {
            $days = $request->get('days', 7);
            $limit = $request->get('limit', 50);

            $activities = ParticipationActivityLog::with(['participation', 'user', 'seller', 'entity'])
                ->recent($days)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'activity_type' => $activity->activity_type,
                        'activity_type_text' => $activity->activity_type_text,
                        'activity_badge' => $activity->activity_badge,
                        'description' => $activity->description,
                        'participation_code' => $activity->participation ? $activity->participation->participation_code : null,
                        'user' => $activity->user ? $activity->user->name : 'Sistema',
                        'seller' => $activity->seller ? $activity->seller->name : null,
                        'entity' => $activity->entity ? $activity->entity->name : null,
                        'created_at' => $activity->created_at->format('d/m/Y H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'activities' => $activities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener actividades recientes: ' . $e->getMessage(),
            ], 500);
        }
    }
}
