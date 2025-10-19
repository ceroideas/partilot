<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Entity;
use App\Models\Administration;
use App\Models\User;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceModern;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected $firebaseService;
    protected $firebaseServiceModern;

    public function __construct(FirebaseService $firebaseService, FirebaseServiceModern $firebaseServiceModern)
    {
        $this->firebaseService = $firebaseService;
        $this->firebaseServiceModern = $firebaseServiceModern;
    }
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $notifications = Notification::with(['sender', 'entity', 'administration'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification - Step 1: Type selection
     */
    public function create()
    {
        return view('notifications.create');
    }

    /**
     * Store notification type selection and redirect to appropriate step
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'notification_type' => 'required|in:administration,entity'
        ]);

        $request->session()->put('notification_type', $request->notification_type);

        if ($request->notification_type === 'entity') {
            return redirect()->route('notifications.select-entity');
        } else {
            return redirect()->route('notifications.select-administration');
        }
    }

    /**
     * Show entity selection form - Step 2a: Entity selection
     */
    public function selectEntity()
    {
        if (!session('notification_type')) {
            return redirect()->route('notifications.create');
        }

        $entities = Entity::with(['administration'])
            ->where('status', 1)
            ->get();

        return view('notifications.select-entity', compact('entities'));
    }

    /**
     * Store selected entity and redirect to message form
     */
    public function storeEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id'
        ]);

        $entity = Entity::with('administration')->findOrFail($request->entity_id);
        $request->session()->put('selected_entity', $entity);
        $request->session()->put('selected_entities', [$entity->id]);

        return redirect()->route('notifications.message');
    }

    /**
     * Show administration selection form - Step 2b: Administration selection
     */
    public function selectAdministration()
    {
        if (!session('notification_type') || session('notification_type') !== 'administration') {
            return redirect()->route('notifications.create');
        }

        $administrations = Administration::where('status', 1)->get();

        return view('notifications.select-administration', compact('administrations'));
    }

    /**
     * Store selected administration and redirect to entities selection
     */
    public function storeAdministration(Request $request)
    {
        $request->validate([
            'administration_id' => 'required|exists:administrations,id'
        ]);

        $administration = Administration::findOrFail($request->administration_id);
        $request->session()->put('selected_administration', $administration);

        return redirect()->route('notifications.select-administration-entities');
    }

    /**
     * Show entities of selected administration - Step 3: Administration entities selection
     */
    public function selectAdministrationEntities()
    {
        if (!session('selected_administration')) {
            return redirect()->route('notifications.create');
        }

        $administration = session('selected_administration');
        $entities = Entity::where('administration_id', $administration->id)
            ->where('status', 1)
            ->get();

        return view('notifications.select-administration-entities', compact('entities', 'administration'));
    }

    /**
     * Store selected entities and redirect to message form
     */
    public function storeAdministrationEntities(Request $request)
    {
        $request->validate([
            'entity_ids' => 'required|array|min:1',
            'entity_ids.*' => 'exists:entities,id',
            'send_to_all' => 'boolean'
        ]);

        if ($request->send_to_all) {
            $administration = session('selected_administration');
            $entityIds = Entity::where('administration_id', $administration->id)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
        } else {
            $entityIds = $request->entity_ids;
        }

        $entities = Entity::whereIn('id', $entityIds)->get();
        $request->session()->put('selected_entities', $entityIds);
        $request->session()->put('selected_entities_data', $entities);

        return redirect()->route('notifications.message');
    }

    /**
     * Show message composition form - Step 4: Message form
     */
    public function message()
    {
        if (!session('selected_entities')) {
            return redirect()->route('notifications.create');
        }

        $selectedEntities = session('selected_entities_data', []);
        if (empty($selectedEntities)) {
            $selectedEntities = Entity::whereIn('id', session('selected_entities'))->get();
        }

        return view('notifications.message', compact('selectedEntities'));
    }

    /**
     * Store and send notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $selectedEntities = session('selected_entities_data', []);
        if (empty($selectedEntities)) {
            $selectedEntities = Entity::whereIn('id', session('selected_entities'))->get();
        }

        $notification = null;
        $successCount = 0;
        $firebaseSuccess = false;
        $firebaseTokensCount = 0;

        DB::beginTransaction();
        try {
            // Crear notificaciones en la base de datos para cada entidad seleccionada
            foreach ($selectedEntities as $entity) {
                $notification = Notification::create([
                    'title' => $request->title,
                    'message' => $request->message,
                    'sender_id' => Auth::id(),
                    'entity_id' => $entity->id,
                    'administration_id' => $entity->administration_id,
                    'status' => 'sent',
                    'sent_at' => now()
                ]);
                $successCount++;
            }

            // TEMPORAL: Enviar notificación push a TODOS los usuarios con token FCM
            // independientemente de la selección de entidades/administraciones
            \Log::info('=== ENVIANDO NOTIFICACIÓN FIREBASE A TODOS LOS USUARIOS ===');
            
            $allUsersWithTokens = User::whereNotNull('fcm_token')->get();
            $firebaseTokensCount = $allUsersWithTokens->count();
            
            \Log::info("Usuarios con tokens FCM: {$firebaseTokensCount}");
            
            if ($firebaseTokensCount > 0) {
                $tokens = $allUsersWithTokens->pluck('fcm_token')->toArray();
                
                \Log::info('Tokens FCM:', ['tokens' => $tokens]);
                
                try {
                    // Intentar primero con el servicio moderno (API V1)
                    $firebaseSuccess = $this->firebaseServiceModern->sendToMultipleDevices(
                        $tokens,
                        $request->title,
                        $request->message,
                        [
                            'notification_id' => $notification ? $notification->id : null,
                            'sender_name' => Auth::user()->name,
                            'type' => 'notification',
                            'broadcast' => 'true'
                        ]
                    );
                    
                    if ($firebaseSuccess) {
                        \Log::info("✓ Notificación Firebase enviada exitosamente a {$firebaseTokensCount} usuario(s) usando API V1");
                    } else {
                        \Log::warning("✗ Error al enviar notificación Firebase con API V1, intentando con API Legacy...");
                        
                        // Fallback al servicio legacy si el moderno falla
                        $firebaseSuccess = $this->firebaseService->sendToMultipleDevices(
                            $tokens,
                            $request->title,
                            $request->message,
                            [
                                'notification_id' => $notification ? $notification->id : null,
                                'sender_name' => Auth::user()->name,
                                'type' => 'notification',
                                'broadcast' => 'true'
                            ]
                        );
                        
                        if ($firebaseSuccess) {
                            \Log::info("✓ Notificación Firebase enviada exitosamente a {$firebaseTokensCount} usuario(s) usando API Legacy");
                        } else {
                            \Log::warning("✗ Error al enviar notificación Firebase con ambas APIs");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Excepción al enviar notificación Firebase: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    $firebaseSuccess = false;
                }
            } else {
                \Log::warning('No hay usuarios con tokens FCM registrados');
            }

            DB::commit();

            // Clear session data
            $request->session()->forget([
                'notification_type',
                'selected_entity',
                'selected_entities',
                'selected_entities_data',
                'selected_administration'
            ]);

            return redirect()->route('notifications.success')
                ->with('success_count', $successCount)
                ->with('notification', $notification)
                ->with('firebase_success', $firebaseSuccess)
                ->with('firebase_tokens_count', $firebaseTokensCount);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al procesar notificación: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al enviar la notificación: ' . $e->getMessage()]);
        }
    }

    /**
     * Show success message
     */
    public function success()
    {
        $successCount = session('success_count', 0);
        $notification = session('notification');

        return view('notifications.success', compact('successCount', 'notification'));
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notificación eliminada correctamente');
    }

    /**
     * Get entities by administration (AJAX)
     */
    public function getEntitiesByAdministration(Request $request)
    {
        $administrationId = $request->administration_id;
        $entities = Entity::where('administration_id', $administrationId)
            ->where('status', 1)
            ->get();

        return response()->json($entities);
    }

    /**
     * Get Firebase configuration for frontend
     */
    public function getFirebaseConfig()
    {
        return response()->json($this->firebaseServiceModern->getConfig());
    }

    /**
     * Register FCM token for user
     */
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        // Store token in user's database
        $user = Auth::user();
        $user->fcm_token = $request->token;
        $user->save();

        \Log::info('FCM Token registered for user ' . Auth::id() . ': ' . $request->token);

        return response()->json(['success' => true]);
    }
}
