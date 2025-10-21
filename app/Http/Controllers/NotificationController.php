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

            // Enviar notificación push solo a usuarios de las entidades seleccionadas
            \Log::info('=== ENVIANDO NOTIFICACIÓN FIREBASE A USUARIOS DE ENTIDADES SELECCIONADAS ===');
            
            // Obtener IDs de entidades seleccionadas
            $selectedEntityIds = $selectedEntities->pluck('id')->toArray();
            \Log::info('Entidades seleccionadas: ' . implode(', ', $selectedEntityIds));
            
            // Obtener usuarios que son managers o sellers de estas entidades
            $relevantUsers = $this->getUsersFromEntities($selectedEntityIds);
            $firebaseTokensCount = $relevantUsers->count();
            
            \Log::info("Usuarios relacionados con las entidades: {$firebaseTokensCount}");
            
            if ($firebaseTokensCount > 0) {
                try {
                    \Log::info('🚀 Enviando notificaciones individuales a cada usuario...');
                    
                    $firebaseSuccessCount = 0;
                    $firebaseFailCount = 0;
                    
                    // Enviar a cada usuario individualmente
                    foreach ($relevantUsers as $user) {
                        try {
                            \Log::info("  📤 Enviando a: {$user->name} (ID: {$user->id}, Rol: {$user->role})");
                            
                            $sent = $this->firebaseServiceModern->sendToDevice(
                                $user->fcm_token,
                                $request->title,
                                $request->message,
                                [
                                    'notification_id' => (string)($notification ? $notification->id : ''),
                                    'sender_name' => Auth::user()->name,
                                    'type' => 'manual_notification',
                                    'user_id' => (string)$user->id,
                                    'user_role' => $user->role,
                                    'entity_ids' => implode(',', $selectedEntityIds) // Convertir array a string
                                ]
                            );
                            
                            if ($sent) {
                                $firebaseSuccessCount++;
                                \Log::info("  ✅ Enviado exitosamente a {$user->name}");
                            } else {
                                $firebaseFailCount++;
                                \Log::warning("  ⚠️ Falló el envío a {$user->name}");
                            }
                        } catch (\Exception $e) {
                            $firebaseFailCount++;
                            \Log::error("  ❌ Error enviando a usuario {$user->id}: " . $e->getMessage());
                        }
                    }
                    
                    $firebaseSuccess = $firebaseSuccessCount > 0;
                    
                    \Log::info("✅ Resultado: {$firebaseSuccessCount} enviadas, {$firebaseFailCount} fallidas");
                    
                } catch (\Exception $e) {
                    \Log::error('Excepción al enviar notificaciones Firebase: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    $firebaseSuccess = false;
                }
            } else {
                \Log::warning('No hay usuarios con tokens FCM en las entidades seleccionadas');
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

    /**
     * Show Firebase dashboard
     */
    public function dashboard()
    {
        $usersWithTokens = User::whereNotNull('fcm_token')->count();
        $users = User::all();
        $totalNotifications = Notification::count();
        $notificationsToday = Notification::whereDate('created_at', today())->count();
        $recentNotifications = Notification::with(['sender', 'entity'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Verificar configuración
        $config = [
            'credentials' => file_exists(storage_path('firebase-credentials.json')),
            'api_key' => !empty(config('firebase.api_key')),
            'project_id' => config('firebase.project_id'),
            'server_key' => !empty(config('firebase.server_key')),
            'service_worker' => file_exists(public_path('firebase-messaging-sw.js')),
        ];
        $config['all_configured'] = $config['credentials'] && $config['api_key'] && $config['project_id'];

        return view('notifications.dashboard', compact(
            'usersWithTokens',
            'users',
            'totalNotifications',
            'notificationsToday',
            'recentNotifications',
            'config'
        ));
    }

    /**
     * Obtener usuarios relevantes de las entidades seleccionadas
     */
    private function getUsersFromEntities($entityIds)
    {
        $users = collect();
        $processedUserIds = [];
        
        // Obtener managers de las entidades
        $managers = \App\Models\Manager::whereIn('entity_id', $entityIds)
            ->with('user')
            ->get();
        
        foreach ($managers as $manager) {
            if ($manager->user && $manager->user->fcm_token && !in_array($manager->user->id, $processedUserIds)) {
                $user = $manager->user;
                $user->role = 'manager';
                $users->push($user);
                $processedUserIds[] = $user->id;
            }
        }
        
        // Obtener sellers de las entidades
        $sellers = \App\Models\Seller::whereHas('entities', fn($q) => 
                $q->whereIn('entities.id', $entityIds)
            )
            ->where('seller_type', '!=', 'externo') // Solo sellers vinculados a usuarios
            ->whereNotNull('user_id')
            ->where('user_id', '>', 0)
            ->with('user')
            ->get();
        
        foreach ($sellers as $seller) {
            if ($seller->user && $seller->user->fcm_token && !in_array($seller->user->id, $processedUserIds)) {
                $user = $seller->user;
                $user->role = 'seller';
                $users->push($user);
                $processedUserIds[] = $user->id;
            }
        }
        
        return $users;
    }

    /**
     * Send test notification to all users
     */
    public function sendTest(Request $request)
    {
        try {
            $users = User::whereNotNull('fcm_token')->get();
            
            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay usuarios con tokens FCM registrados'
                ], 400);
            }

            \Log::info('📤 Iniciando envío de notificación de prueba a ' . $users->count() . ' usuario(s)');
            
            $successCount = 0;
            $failCount = 0;
            
            // Enviar a cada usuario individualmente (esto sí funciona)
            foreach ($users as $user) {
                try {
                    \Log::info('  Enviando a: ' . $user->name . ' (' . substr($user->fcm_token, 0, 50) . '...)');
                    
                    $sent = $this->firebaseServiceModern->sendToDevice(
                        $user->fcm_token,
                        '🔥 Notificación de Prueba',
                        'Firebase está funcionando correctamente. ¡Las notificaciones push están activas!',
                        [
                            'type' => 'test',
                            'timestamp' => now()->toIso8601String(),
                            'user_id' => (string)$user->id
                        ]
                    );
                    
                    if ($sent) {
                        $successCount++;
                        \Log::info('  ✅ Enviado exitosamente a ' . $user->name);
                    } else {
                        $failCount++;
                        \Log::warning('  ⚠️ Falló el envío a ' . $user->name);
                    }
                } catch (\Exception $e) {
                    $failCount++;
                    \Log::error('  ❌ Error enviando a ' . $user->name . ': ' . $e->getMessage());
                }
            }

            \Log::info('📊 Resultado: ' . $successCount . ' exitosos, ' . $failCount . ' fallidos');

            if ($successCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Notificación enviada a {$successCount} de {$users->count()} usuarios",
                    'success_count' => $successCount,
                    'fail_count' => $failCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo enviar ninguna notificación. Revisa los logs.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación de prueba: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
