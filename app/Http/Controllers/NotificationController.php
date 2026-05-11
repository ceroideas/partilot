<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Entity;
use App\Models\Administration;
use App\Models\User;
use App\Models\UserFcmToken;
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
            'notification_type' => 'required|in:administration,entity,user',
        ]);

        $request->session()->put('notification_type', $request->notification_type);

        if ($request->notification_type === 'entity') {
            return redirect()->route('notifications.select-entity');
        }

        if ($request->notification_type === 'user') {
            return redirect()->route('notifications.push-to-user');
        }

        return redirect()->route('notifications.select-administration');
    }

    /**
     * Formulario: push FCM a un usuario concreto (gestores/vendedores de entidades a las que tienes acceso).
     */
    public function pushToUserForm()
    {
        $auth = auth()->user();
        $users = $auth->isSuperAdmin()
            ? User::query()->with('fcmTokens')->orderBy('name')->get()
            : $this->collectUsersLinkedToEntities($auth->accessibleEntityIds());

        return view('notifications.push-to-user', [
            'users' => $users,
        ]);
    }

    /**
     * Envía push FCM al usuario seleccionado (sin crear filas por entidad en `notifications`).
     */
    public function storeUserPush(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $auth = auth()->user();
        $target = User::query()->with('fcmTokens')->findOrFail((int) $request->user_id);

        if (! $this->authUserMaySendDirectPushTo($auth, $target)) {
            abort(403, 'No puedes enviar notificaciones a este usuario.');
        }

        if ($target->fcmTokens->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['user_id' => 'Este usuario no tiene dispositivos con token FCM registrados.']);
        }

        $sent = 0;
        $failed = 0;
        foreach ($target->fcmTokens as $device) {
            try {
                $ok = $this->firebaseServiceModern->sendToDevice(
                    $device->token,
                    $request->title,
                    $request->message,
                    [
                        'type' => 'direct_user_push',
                        'target_user_id' => (string) $target->id,
                        'sender_id' => (string) $auth->id,
                        'sender_name' => (string) $auth->name,
                        'platform' => (string) $device->platform,
                    ]
                );
                if ($ok) {
                    $sent++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
                \Log::error('storeUserPush FCM: '.$e->getMessage());
            }
        }

        if ($sent === 0) {
            return back()
                ->withInput()
                ->withErrors(['user_id' => 'No se pudo entregar el push. Revisa credenciales Firebase y los logs.']);
        }

        return redirect()
            ->route('notifications.push-to-user')
            ->with('success', "Push enviado: {$sent} dispositivo(s)".($failed > 0 ? "; {$failed} fallido(s)." : '.'));
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
            ->forUser(auth()->user())
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

        $entity = Entity::with('administration')
            ->forUser(auth()->user())
            ->findOrFail($request->entity_id);
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

        $administrations = Administration::forUser(auth()->user())
            ->where('status', 1)
            ->get();

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

        $administration = Administration::forUser(auth()->user())->findOrFail($request->administration_id);
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

        if (!$administration || !auth()->user()->canAccessAdministration($administration->id)) {
            return redirect()->route('notifications.create')
                ->with('error', 'Permisos insuficientes para la administración seleccionada.');
        }

        $entities = Entity::forUser(auth()->user())
            ->where('administration_id', $administration->id)
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

            if (!$administration || !auth()->user()->canAccessAdministration($administration->id)) {
                return redirect()->route('notifications.create')
                    ->with('error', 'Permisos insuficientes para la administración seleccionada.');
            }

            $entityIds = Entity::forUser(auth()->user())
                ->where('administration_id', $administration->id)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
        } else {
            $entityIds = collect($request->entity_ids)
                ->filter(fn ($id) => auth()->user()->canAccessEntity((int) $id))
                ->values()
                ->all();

            if (empty($entityIds)) {
                return back()->withErrors(['entity_ids' => 'Debes seleccionar al menos una entidad válida.']);
            }
        }

        $entities = Entity::forUser(auth()->user())
            ->whereIn('id', $entityIds)
            ->get();
        $request->session()->put('selected_entities', $entityIds);
        $request->session()->put('selected_entities_data', $entities);

        return redirect()->route('notifications.message');
    }

    /**
     * Show message composition form - Step 4: Message form
     */
    public function message()
    {
        $selectedEntityIds = collect(session('selected_entities', []))
            ->filter(fn ($id) => auth()->user()->canAccessEntity((int) $id))
            ->values()
            ->all();

        if (empty($selectedEntityIds)) {
            return redirect()->route('notifications.create');
        }

        $selectedEntities = Entity::forUser(auth()->user())
            ->whereIn('id', $selectedEntityIds)
            ->get();

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

        $selectedEntityIds = collect(session('selected_entities', []))
            ->filter(fn ($id) => auth()->user()->canAccessEntity((int) $id))
            ->values()
            ->all();

        if (empty($selectedEntityIds)) {
            return redirect()->route('notifications.create')
                ->with('error', 'No se han seleccionado entidades válidas para la notificación.');
        }

        $selectedEntities = Entity::forUser(auth()->user())
            ->whereIn('id', $selectedEntityIds)
            ->get();

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
            $firebaseTokensCount = $relevantUsers->sum(fn ($u) => $u->fcmTokens->count());

            \Log::info('Usuarios con dispositivos: ' . $relevantUsers->count() . ', tokens totales: ' . $firebaseTokensCount);
            
            if ($firebaseTokensCount > 0) {
                try {
                    \Log::info('🚀 Enviando notificaciones individuales a cada usuario...');
                    
                    $firebaseSuccessCount = 0;
                    $firebaseFailCount = 0;
                    
                    // Enviar a cada usuario individualmente
                    foreach ($relevantUsers as $user) {
                        foreach ($user->fcmTokens as $device) {
                            try {
                                \Log::info("  📤 Enviando a: {$user->name} (ID: {$user->id}, Rol: {$user->role}, {$device->platform})");

                                $sent = $this->firebaseServiceModern->sendToDevice(
                                    $device->token,
                                    $request->title,
                                    $request->message,
                                    [
                                        'notification_id' => (string) ($notification ? $notification->id : ''),
                                        'sender_name' => Auth::user()->name,
                                        'type' => 'manual_notification',
                                        'user_id' => (string) $user->id,
                                        'user_role' => $user->role,
                                        'entity_ids' => implode(',', $selectedEntityIds),
                                        'platform' => $device->platform,
                                    ]
                                );

                                if ($sent) {
                                    $firebaseSuccessCount++;
                                    \Log::info("  ✅ Enviado exitosamente a {$user->name} ({$device->platform})");
                                } else {
                                    $firebaseFailCount++;
                                    \Log::warning("  ⚠️ Falló el envío a {$user->name} ({$device->platform})");
                                }
                            } catch (\Exception $e) {
                                $firebaseFailCount++;
                                \Log::error("  ❌ Error enviando a usuario {$user->id}: " . $e->getMessage());
                            }
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

        if (!auth()->user()->canAccessAdministration((int) $administrationId)) {
            return response()->json([]);
        }

        $entities = Entity::forUser(auth()->user())
            ->where('administration_id', $administrationId)
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
     * Register FCM token for user (app móvil envía `fcm_token`; el panel puede enviar `token`).
     */
    public function registerToken(Request $request)
    {
        $token = $request->input('fcm_token') ?? $request->input('token');
        if (! is_string($token) || $token === '') {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere el token de dispositivo (campos aceptados: fcm_token o token).',
            ], 422);
        }

        $platform = strtolower((string) $request->input('platform', 'android'));
        if (! in_array($platform, ['android', 'ios', 'web'], true)) {
            $platform = 'android';
        }

        $user = Auth::user();
        UserFcmToken::updateOrCreate(
            ['token' => $token],
            ['user_id' => $user->id, 'platform' => $platform]
        );

        \Log::info('FCM Token registered for user ' . Auth::id() . ' (' . $platform . '): ' . substr($token, 0, 48) . '...');

        return response()->json(['success' => true]);
    }

    /**
     * Elimina el token FCM de este dispositivo para el usuario autenticado (evita notificaciones tras cerrar sesión).
     */
    public function unregisterToken(Request $request)
    {
        $token = $request->input('fcm_token') ?? $request->input('token');
        if (! is_string($token) || $token === '') {
            return response()->json(['success' => true]);
        }

        $deleted = UserFcmToken::query()
            ->where('user_id', Auth::id())
            ->where('token', $token)
            ->delete();

        \Log::info('FCM Token unregistered for user ' . Auth::id() . ', rows deleted: ' . $deleted);

        return response()->json(['success' => true]);
    }

    /**
     * Show Firebase dashboard
     */
    public function dashboard()
    {
        $usersWithTokens = User::has('fcmTokens')->count();
        $users = User::withCount('fcmTokens')->orderBy('name')->get();
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
     * Gestores y vendedores vinculados a las entidades indicadas (un usuario aparece una vez; prioridad rol gestor).
     *
     * @param  array<int|string>  $entityIds
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function collectUsersLinkedToEntities(array $entityIds): \Illuminate\Support\Collection
    {
        $entityIds = array_values(array_unique(array_filter(array_map('intval', $entityIds))));
        if ($entityIds === []) {
            return collect();
        }

        $byId = [];

        $managers = \App\Models\Manager::query()
            ->whereIn('entity_id', $entityIds)
            ->with('user.fcmTokens')
            ->get();

        foreach ($managers as $manager) {
            if ($manager->user) {
                $u = $manager->user;
                $u->role = 'manager';
                $byId[$u->id] = $u;
            }
        }

        $sellers = \App\Models\Seller::query()
            ->whereHas('entities', fn ($q) => $q->whereIn('entities.id', $entityIds))
            ->where('seller_type', '!=', 'externo')
            ->whereNotNull('user_id')
            ->where('user_id', '>', 0)
            ->with('user.fcmTokens')
            ->get();

        foreach ($sellers as $seller) {
            if ($seller->user && ! isset($byId[$seller->user->id])) {
                $u = $seller->user;
                $u->role = 'seller';
                $byId[$seller->user->id] = $u;
            }
        }

        return collect($byId)->sortBy('name')->values();
    }

    private function authUserMaySendDirectPushTo(User $auth, User $target): bool
    {
        if ($auth->isSuperAdmin()) {
            return true;
        }

        return $this->collectUsersLinkedToEntities($auth->accessibleEntityIds())
            ->contains('id', $target->id);
    }

    /**
     * Obtener usuarios relevantes de las entidades seleccionadas (solo con token FCM).
     */
    private function getUsersFromEntities($entityIds)
    {
        return $this->collectUsersLinkedToEntities(is_array($entityIds) ? $entityIds : [])
            ->filter(fn (User $u) => $u->fcmTokens->isNotEmpty())
            ->values();
    }

    /**
     * Send test notification to all users
     */
    public function sendTest(Request $request)
    {
        try {
            $devices = UserFcmToken::with('user')->get();

            if ($devices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay tokens FCM registrados en ningún dispositivo',
                ], 400);
            }

            \Log::info('📤 Iniciando envío de notificación de prueba a ' . $devices->count() . ' dispositivo(s)');

            $successCount = 0;
            $failCount = 0;

            foreach ($devices as $device) {
                $user = $device->user;
                $label = $user ? $user->name : ('user#' . $device->user_id);
                try {
                    \Log::info('  Enviando a: ' . $label . ' [' . $device->platform . '] (' . substr($device->token, 0, 50) . '...)');

                    $sent = $this->firebaseServiceModern->sendToDevice(
                        $device->token,
                        '🔥 Notificación de Prueba',
                        'Firebase está funcionando correctamente. ¡Las notificaciones push están activas!',
                        [
                            'type' => 'test',
                            'timestamp' => now()->toIso8601String(),
                            'user_id' => (string) $device->user_id,
                            'platform' => $device->platform,
                        ]
                    );

                    if ($sent) {
                        $successCount++;
                        \Log::info('  ✅ Enviado exitosamente a ' . $label);
                    } else {
                        $failCount++;
                        \Log::warning('  ⚠️ Falló el envío a ' . $label);
                    }
                } catch (\Exception $e) {
                    $failCount++;
                    \Log::error('  ❌ Error enviando a ' . $label . ': ' . $e->getMessage());
                }
            }

            \Log::info('📊 Resultado: ' . $successCount . ' exitosos, ' . $failCount . ' fallidos');

            if ($successCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Notificación enviada a {$successCount} de {$devices->count()} dispositivo(s)",
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
