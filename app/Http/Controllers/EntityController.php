<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Administration;
use App\Models\Manager;
use App\Models\User;
use App\Mail\EntityManagerInvitationMail;
use App\Mail\EntityResponsibleManagerConfirmedMail;
use App\Services\CommunicationEmailService;
use Illuminate\Support\Facades\Hash;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entities = Entity::with(['administration', 'manager'])
            ->forUser(auth()->user())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('entities.index', compact('entities'));
    }

    /**
     * Show the form for creating a new resource - Paso 1: Seleccionar administración
     * Al iniciar una nueva entidad se limpia entity_information (y la imagen) para no arrastrar datos anteriores.
     */
    public function create()
    {
        request()->session()->forget('entity_information');
        $administrations = Administration::forUser(auth()->user())->get();
        return view('entities.add', compact('administrations'));
    }

    /**
     * Store administration selection and show entity information form - Paso 2: Datos de la entidad
     */
    public function store_administration(Request $request)
    {
        $request->validate([
            'administration_id' => 'required|integer|exists:administrations,id'
        ]);

        $administration = Administration::with('manager.user')
            ->forUser(auth()->user())
            ->findOrFail($request->administration_id);
        $request->session()->put('selected_administration', $administration);

        return redirect()->route('entities.add-information');
    }

    /**
     * Show entity information form - Paso 2: Datos de la entidad
     */
    public function create_information()
    {
        $administrationSession = session('selected_administration');

        if (!$administrationSession) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada. Por favor, seleccione una administración.');
        }

        // Recargar la administración con las relaciones necesarias
        $administration = Administration::with('manager.user')
            ->forUser(auth()->user())
            ->find($administrationSession->id ?? $administrationSession['id'] ?? null);

        if (!$administration || !auth()->user()->canAccessAdministration($administration->id)) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada. Por favor, seleccione una administración.');
        }

        // Actualizar la sesión con la administración recargada
        session(['selected_administration' => $administration]);

        return view('entities.add_information');
    }

    /**
     * Store entity information and show manager form - Paso 3: Datos del gestor
     */
    public function store_information(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:500',
            'nif_cif' => ['required', 'string', 'max:20', new \App\Rules\EntityDocument],
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'panel_password' => 'required|string|min:8',
            'comments' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|in:0,1'
        ]);

        // Manejo de imagen: nueva subida o marcar para quitar
        if ($request->boolean('remove_image')) {
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('uploads'), $filename);
            $validated['image'] = $filename;
        } else {
            // Mantener imagen previa de la sesión si no se sube ni se elimina
            $validated['image'] = session('entity_information.image');
        }
        unset($validated['remove_image']);

        $request->session()->put('entity_information', $validated);

        return redirect()->route('entities.add-manager');
    }

    /**
     * Show manager form - Paso 3: Datos del gestor (Invitar o Registrar)
     */
    public function create_manager()
    {
        $administration = session('selected_administration');
        $entityInformation = session('entity_information');

        if (!$administration || !auth()->user()->canAccessAdministration($administration->id) || !$entityInformation) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada. Por favor, vuelva a empezar.');
        }

        // Inicializar datos del gestor en sesión si no existen (persistencia como en administrations)
        if (!session()->has('entity_manager')) {
            session()->put('entity_manager', [
                'manager_name' => '',
                'manager_last_name' => '',
                'manager_last_name2' => '',
                'manager_nif_cif' => '',
                'manager_birthday' => '',
                'manager_email' => '',
                'manager_phone' => '',
            ]);
        }

        return view('entities.add_manager');
    }

    /**
     * Guardar borrador del formulario de gestor externo en sesión y volver a la elección Invitar/Registrar
     */
    public function save_manager_draft(Request $request)
    {
        $request->session()->put('entity_manager', [
            'manager_name' => $request->input('manager_name', ''),
            'manager_last_name' => $request->input('manager_last_name', ''),
            'manager_last_name2' => $request->input('manager_last_name2', ''),
            'manager_nif_cif' => $request->input('manager_nif_cif', ''),
            'manager_birthday' => $request->input('manager_birthday', ''),
            'manager_email' => $request->input('manager_email', ''),
            'manager_phone' => $request->input('manager_phone', ''),
        ]);

        return redirect()->route('entities.add-manager');
    }

    /**
     * Store manager information and create the complete entity - Paso final
     */
    public function store_manager(Request $request)
    {
        $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_last_name' => 'required|string|max:255',
            'manager_last_name2' => 'nullable|string|max:255',
            'manager_nif_cif' => ['nullable', 'string', 'max:20', new \App\Rules\SpanishDocument],
            'manager_birthday' => ['required', 'date', new \App\Rules\MinimumAge(18)],
            'manager_email' => 'required|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
        ]);

        $administration = $request->session()->get('selected_administration');
        $entityInformation = $request->session()->get('entity_information');

        if (! $administration || ! auth()->user()->canAccessAdministration($administration->id) || ! $entityInformation) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada o permisos insuficientes. Por favor, vuelva a empezar.');
        }

        $panelPassword = $entityInformation['panel_password'] ?? null;
        if (! $panelPassword) {
            return redirect()->route('entities.add-information')
                ->with('error', 'Debe definir la contraseña de acceso al panel en el paso de Datos Entidad.');
        }

        $email = $entityInformation['email'] ?? null;
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('entities.add-information')
                ->with('error', 'La entidad debe tener un email de contacto válido para el acceso al panel.');
        }

        if (User::where('email', $email)->exists()) {
            return back()->withErrors([
                'panel_password' => 'Ya existe un usuario con el email de la entidad. Cambie el email en el paso anterior.',
            ])->withInput();
        }

        if (strcasecmp((string) $request->input('manager_email'), (string) $email) === 0) {
            return back()->withErrors([
                'manager_email' => 'El email del gestor debe ser distinto al email de acceso del panel de la entidad.',
            ])->withInput();
        }

        $managerUser = User::where('email', $request->input('manager_email'))->first();
        if ($managerUser && $managerUser->isPanelAccount()) {
            return back()->withErrors([
                'manager_email' => 'Ese email corresponde a una cuenta de acceso de panel. Use otro email para el gestor.',
            ])->withInput();
        }

        if (! $managerUser) {
            $managerUser = User::create([
                'name' => $request->input('manager_name'),
                'last_name' => $request->input('manager_last_name'),
                'last_name2' => $request->input('manager_last_name2'),
                'email' => $request->input('manager_email'),
                'password' => bcrypt(12345678),
                'role' => User::ROLE_ENTITY,
                'status' => true,
                'phone' => $request->input('manager_phone') ?: null,
                'nif_cif' => $request->input('manager_nif_cif') ?: null,
                'birthday' => $request->input('manager_birthday') ?: null,
            ]);
        }

        $entityData = array_merge($entityInformation, [
            'administration_id' => is_object($administration) ? $administration->id : ($administration['id'] ?? null),
        ]);
        unset($entityData['panel_password']);

        $entity = Entity::create($entityData);

        $panelUser = User::create([
            'name' => trim((string) ($entityInformation['name'] ?? '')) ?: 'Entidad',
            'email' => $email,
            'password' => Hash::make($panelPassword),
            'role' => User::ROLE_ENTITY,
            'panel_account_type' => 'entity',
            'panel_account_id' => $entity->id,
            'status' => true,
            'phone' => $entityInformation['phone'] ?? null,
            'nif_cif' => $entityInformation['nif_cif'] ?? null,
        ]);

        Manager::firstOrCreate([
            'user_id' => $panelUser->id,
            'entity_id' => $entity->id,
        ], [
            'is_primary' => false,
            'permission_sellers' => true,
            'permission_design' => true,
            'permission_statistics' => true,
            'permission_payments' => true,
            'status' => 1,
        ]);

        Manager::firstOrCreate([
            'user_id' => $managerUser->id,
            'entity_id' => $entity->id,
        ], [
            'is_primary' => true,
            'permission_sellers' => true,
            'permission_design' => true,
            'permission_statistics' => true,
            'permission_payments' => true,
            'status' => 1,
        ]);

        $request->session()->forget(['selected_administration', 'entity_information', 'entity_manager']);

        return redirect()->route('entities.index')
            ->with('success', 'Entidad creada exitosamente.');
    }

    /**
     * Verificar si existe un gestor con el email proporcionado
     */
    public function check_manager_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        
        // Buscar si existe un usuario con ese email
        $user = User::where('email', $email)->first();
        
        if ($user && $user->isPanelAccount()) {
            return response()->json([
                'exists' => true,
                'user_id' => null,
                'is_panel_account' => true,
                'manager_name' => null,
            ]);
        }

        return response()->json([
            'exists' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
            'is_panel_account' => false,
            'manager_name' => $user ? trim(($user->name ?? '') . ' ' . ($user->last_name ?? '')) : null,
        ]);
    }

    /**
     * Invitar gestor existente a una entidad
     */
    public function invite_manager(Request $request)
    {
        $request->validate([
            'entity_id' => 'nullable|integer|exists:entities,id',
            'user_id' => 'required|integer|exists:users,id',
            'permission_sellers' => 'nullable|boolean',
            'permission_design' => 'nullable|boolean',
            'permission_statistics' => 'nullable|boolean',
            'permission_payments' => 'nullable|boolean',
        ]);

        $isCreationFlow = ! $request->filled('entity_id');
        $entity = null;
        if (! $isCreationFlow) {
            $entity = Entity::forUser(auth()->user())->findOrFail($request->entity_id);
        } else {
            $administration = $request->session()->get('selected_administration');
            $entityInformation = $request->session()->get('entity_information');

            if (! $administration || !auth()->user()->canAccessAdministration($administration->id) || ! $entityInformation) {
                return redirect()->route('entities.create')
                    ->with('error', 'Sesión expirada. Vuelva a iniciar la creación de la entidad.');
            }

            $panelPassword = $entityInformation['panel_password'] ?? null;
            $panelEmail = $entityInformation['email'] ?? null;
            if (! $panelPassword || ! $panelEmail) {
                return redirect()->route('entities.add-information')
                    ->with('error', 'Faltan datos de acceso de panel para crear la entidad.');
            }

            if (User::where('email', $panelEmail)->exists()) {
                return redirect()->route('entities.add-information')
                    ->with('error', 'Ya existe un usuario con el email de la entidad. Cambie el email en Datos Entidad.');
            }

            $entityData = array_merge($entityInformation, [
                'administration_id' => is_object($administration) ? $administration->id : ($administration['id'] ?? null),
            ]);
            unset($entityData['panel_password']);

            $entity = Entity::create($entityData);

            $panelUser = User::create([
                'name' => trim((string) ($entityInformation['name'] ?? '')) ?: 'Entidad',
                'email' => $panelEmail,
                'password' => Hash::make($panelPassword),
                'role' => User::ROLE_ENTITY,
                'panel_account_type' => 'entity',
                'panel_account_id' => $entity->id,
                'status' => true,
                'phone' => $entityInformation['phone'] ?? null,
                'nif_cif' => $entityInformation['nif_cif'] ?? null,
            ]);

            Manager::firstOrCreate([
                'user_id' => $panelUser->id,
                'entity_id' => $entity->id,
            ], [
                'is_primary' => false,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
                'status' => 1,
            ]);
        }

        $invited = User::findOrFail($request->user_id);
        if ($invited->isPanelAccount()) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'No se puede asignar como gestor a la cuenta de acceso al panel de una administración o entidad.');
        }

        // Verificar si ya existe un manager con este usuario para esta entidad
        $existingManager = Manager::where('user_id', $request->user_id)
            ->where('entity_id', $entity->id)
            ->first();

        if ($existingManager) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'Este usuario ya es gestor de esta entidad.');
        }

        // En creación inicial de entidad, el gestor invitado debe quedar como principal.
        if ($isCreationFlow) {
            Manager::where('entity_id', $entity->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        // Crear la relación manager-entity
        Manager::create([
            'user_id' => $request->user_id,
            'entity_id' => $entity->id,
            'is_primary' => $isCreationFlow ? true : false,
            'permission_sellers' => $request->boolean('permission_sellers'),
            'permission_design' => $request->boolean('permission_design'),
            'permission_statistics' => $request->boolean('permission_statistics'),
            'permission_payments' => $request->boolean('permission_payments'),
            'status' => null, // Pendiente por defecto
        ]);

        $user = User::find($request->user_id);
        if ($user && $user->role !== User::ROLE_ENTITY) {
            $user->update(['role' => User::ROLE_ENTITY]);
        }

        // Cadena de alta entidad/gestor: email al gestor invitado
        try {
            if ($user && !empty($user->email)) {
                app(CommunicationEmailService::class)->sendAndLog(
                    recipientEmail: (string) $user->email,
                    recipientRole: 'gestor_entidad',
                    recipientUser: $user,
                    messageType: 'entity_manager_invitation',
                    templateKey: null,
                    mailClass: EntityManagerInvitationMail::class,
                    mailPayload: ['entity_id' => $entity->id, 'user_id' => $user->id],
                    context: ['entity_id' => $entity->id],
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Fallo enviando invitación a gestor existente: '.$e->getMessage());
        }

        // Si venimos del wizard de creación, limpiar sesión al completar la invitación.
        if ($isCreationFlow) {
            $request->session()->forget(['selected_administration', 'entity_information', 'entity_manager']);
        }

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Gestor invitado exitosamente.');
    }

    /**
     * Registrar nuevo gestor para una entidad
     */
    public function register_manager(Request $request, $id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($id);

        if (User::where('email', $request->manager_email)->whereNotNull('panel_account_type')->exists()) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'Ese email corresponde a una cuenta de acceso al panel (administración o entidad) y no puede usarse como gestor.');
        }

        // Buscar usuario primero para excluirlo de la validación unique si existe
        $user = User::where('email', $request->manager_email)->first();
        if ($user && $user->isPanelAccount()) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'Ese usuario es una cuenta de acceso al panel y no puede añadirse como gestor.');
        }
        $userId = $user ? $user->id : null;

        $validated = $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_last_name' => 'required|string|max:255',
            'manager_last_name2' => 'nullable|string|max:255',
            'manager_nif_cif' => ['nullable', 'string', 'max:20', 'unique:users,nif_cif' . ($userId ? ',' . $userId : '')],
            'manager_birthday' => ['required', 'date', new \App\Rules\MinimumAge(18)],
            'manager_email' => 'required|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'permission_sellers' => 'nullable|boolean',
            'permission_design' => 'nullable|boolean',
            'permission_statistics' => 'nullable|boolean',
            'permission_payments' => 'nullable|boolean',
        ]);
        if (! $user) {
            $user = new User;
            $user->name = $validated['manager_name'] . ' ' . $validated['manager_last_name'];
            $user->email = $validated['manager_email'];
            $user->password = bcrypt(12345678);
            $user->role = User::ROLE_ENTITY;
            $user->save();
        }

        // Actualizar datos del usuario
        $user->update([
            'name' => $validated['manager_name'],
            'last_name' => $validated['manager_last_name'],
            'last_name2' => $validated['manager_last_name2'] ?? null,
            'nif_cif' => $validated['manager_nif_cif'] ?? null,
            'birthday' => $validated['manager_birthday'] ?? null,
            'phone' => $validated['manager_phone'] ?? null,
            'role' => User::ROLE_ENTITY,
        ]);

        // Verificar si ya existe un manager con este usuario para esta entidad
        $existingManager = Manager::where('user_id', $user->id)
            ->where('entity_id', $entity->id)
            ->first();

        if ($existingManager) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'Este usuario ya es gestor de esta entidad.');
        }

        // Crear la relación manager-entity (gestor secundario)
        Manager::create([
            'user_id' => $user->id,
            'entity_id' => $entity->id,
            'is_primary' => false,
            'permission_sellers' => $request->has('permission_sellers') ? true : false,
            'permission_design' => $request->has('permission_design') ? true : false,
            'permission_statistics' => $request->has('permission_statistics') ? true : false,
            'permission_payments' => $request->has('permission_payments') ? true : false,
            'status' => null, // Pendiente por defecto
        ]);

        // Cadena de alta entidad/gestor: email al gestor recién registrado/invitado
        try {
            if (!empty($user->email)) {
                app(CommunicationEmailService::class)->sendAndLog(
                    recipientEmail: (string) $user->email,
                    recipientRole: 'gestor_entidad',
                    recipientUser: $user,
                    messageType: 'entity_manager_invitation',
                    templateKey: null,
                    mailClass: EntityManagerInvitationMail::class,
                    mailPayload: ['entity_id' => $entity->id, 'user_id' => $user->id],
                    context: ['entity_id' => $entity->id],
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Fallo enviando invitación a gestor nuevo: '.$e->getMessage());
        }

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Gestor registrado exitosamente.');
    }

    /**
     * Crear entidad con gestor pendiente de registro
     */
    public function create_pending_entity(Request $request)
    {
        $request->validate([
            'invite_email' => 'required|email'
        ]);

        // Obtener datos de sesión
        $administration = $request->session()->get('selected_administration');
        $entityInformation = $request->session()->get('entity_information');

        if (!$administration || !auth()->user()->canAccessAdministration($administration->id) || !$entityInformation) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada. Por favor, vuelva a empezar.');
        }

        // Crear entidad sin manager (se asignará cuando se registre)
        $entityData = array_merge($entityInformation, [
            'administration_id' => $administration->id,
        ]);

        $entity = Entity::create($entityData);

        // Guardar el email de invitación en sesión para cuando se registre
        $request->session()->put('pending_manager_email', $request->invite_email);
        $request->session()->put('pending_entity_id', $entity->id);

        // Limpiar sesión de datos de entidad
        $request->session()->forget(['selected_administration', 'entity_information']);

        return redirect()->route('entities.index')
            ->with('success', 'Entidad creada. Se enviará una invitación al email proporcionado.');
    }

    /**
     * Método temporal para crear un gestor de prueba
     */
    public function create_test_manager()
    {
        // Crear usuario de prueba
        $user = User::firstOrCreate(
            ['email' => 'test@manager.com'],
            [
                'name' => 'Test Manager',
                'email' => 'test@manager.com',
                'password' => bcrypt(12345678)
            ]
        );

        // Crear entidad de prueba
        $entity = Entity::firstOrCreate(
            ['name' => 'Test Entity'],
            [
                'administration_id' => 1,
                'name' => 'Test Entity',
                'province' => 'Test Province',
                'city' => 'Test City',
                'postal_code' => '12345',
                'address' => 'Test Address',
                'nif_cif' => '12345678B',
                'phone' => '123456789',
                'email' => 'test@entity.com',
                'comments' => 'Test Comments',
            ]
        );

        // Crear la relación manager-entity
        $manager = Manager::firstOrCreate(
            ['user_id' => $user->id, 'entity_id' => $entity->id],
            [
                'user_id' => $user->id,
                'entity_id' => $entity->id
            ]
        );

        return response()->json([
            'message' => 'Gestor de prueba creado exitosamente',
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $entity = Entity::with(['administration', 'manager.user', 'managers.user'])
            ->forUser(auth()->user())
            ->findOrFail($id);

        $managersVisible = $entity->managers->filter(function ($m) use ($entity) {
            $u = $m->user;
            if (! $u) {
                return true;
            }

            // Mostrar siempre el gestor principal aunque sea la cuenta panel de la entidad.
            if ($m->is_primary) {
                return true;
            }

            return ! ($u->panel_account_type === 'entity' && (int) $u->panel_account_id === (int) $entity->id);
        })->values();

        $entityPanelUser = User::where('panel_account_type', 'entity')
            ->where('panel_account_id', $entity->id)
            ->first();

        return view('entities.show', compact('entity', 'managersVisible', 'entityPanelUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($id);
        $administrations = Administration::forUser(auth()->user())->get();
        $users = User::whereNull('panel_account_type')->orderBy('name')->get();

        return view('entities.edit', compact('entity', 'administrations', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($id);
        
        $validated = $request->validate([
            'administration_id' => 'nullable|integer|exists:administrations,id',
            'name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'nif_cif' => ['nullable', 'string', 'max:20', new \App\Rules\EntityDocument],
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'comments' => 'nullable|string|max:1000',
            'status' => 'nullable|in:-1,0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|in:0,1',
            'panel_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Convertir status: -1 = null (pendiente), 1 = activo, 0 = inactivo
        $validated['status'] = $validated['status'] === '-1' ? null : ($validated['status'] ?? null);

        // Eliminar imagen si el usuario pulsó "Eliminar imagen"
        if ($request->input('remove_image') === '1') {
            if ($entity->image && file_exists(public_path('uploads/' . $entity->image))) {
                unlink(public_path('uploads/' . $entity->image));
            }
            $validated['image'] = null;
        }
        // Sustituir por nueva imagen si se subió un fichero
        elseif ($request->hasFile('image')) {
            if ($entity->image && file_exists(public_path('uploads/' . $entity->image))) {
                unlink(public_path('uploads/' . $entity->image));
            }
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('uploads'), $filename);
            $validated['image'] = $filename;
        }

        unset($validated['remove_image'], $validated['panel_password']);

        $panelUser = User::where('panel_account_type', 'entity')
            ->where('panel_account_id', $entity->id)
            ->first();

        if ($panelUser && ! empty($validated['email']) && $validated['email'] !== $panelUser->email) {
            if (User::where('email', $validated['email'])->where('id', '!=', $panelUser->id)->exists()) {
                return back()->withInput()
                    ->withErrors(['email' => 'Ese email ya está en uso por otro usuario.']);
            }
        }

        $entity->update($validated);
        $entity->refresh();

        if ($panelUser) {
            $panelUser->update([
                'email' => $entity->email,
                'name' => trim((string) $entity->name) ?: 'Entidad',
                'phone' => $entity->phone,
                'nif_cif' => $entity->nif_cif,
            ]);
            if ($request->filled('panel_password')) {
                $panelUser->update(['password' => Hash::make($request->panel_password)]);
            }
        }

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Entidad actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        if (!auth()->user()->canAccessEntity($entity->id)) {
            abort(403, 'No tienes permisos para eliminar esta entidad.');
        }

        // Eliminar imagen si existe
        if ($entity->image && file_exists(public_path('uploads/' . $entity->image))) {
            unlink(public_path('uploads/' . $entity->image));
        }

        $entity->delete();

        return redirect()->route('entities.index')
            ->with('success', 'Entidad eliminada exitosamente.');
    }

    /**
     * Show the form for editing the manager of an entity.
     */
    public function edit_manager($id)
    {
        $entity = Entity::with(['administration', 'manager'])
            ->forUser(auth()->user())
            ->findOrFail($id);
        return view('entities.edit_manager', compact('entity'));
    }

    /**
     * Update the manager of an entity.
     */
    public function update_manager(Request $request, $id)
    {
        $entity = Entity::with('manager')
            ->forUser(auth()->user())
            ->findOrFail($id);
        
        // Buscar usuario primero para excluirlo de la validación unique si existe
        $user = User::where('email', $request->manager_email)->first();
        $userId = $user ? $user->id : null;
        
        $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_last_name' => 'required|string|max:255',
            'manager_last_name2' => 'nullable|string|max:255',
            'manager_nif_cif' => ['nullable', 'string', 'max:20', 'unique:users,nif_cif' . ($userId ? ',' . $userId : '')],
            'manager_birthday' => ['required', 'date', new \App\Rules\MinimumAge(18)],
            'manager_email' => 'required|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'manager_comment' => 'nullable|string|max:1000',
            'manager_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if (!$user) {
            $user = new User;
            $user->name = $request->manager_name . ' ' . $request->manager_last_name;
            $user->email = $request->manager_email;
            $user->password = bcrypt(12345678);
            $user->save();
        }

        // Actualizar datos del usuario
        $user->update([
            'name' => $request->manager_name,
            'last_name' => $request->manager_last_name,
            'last_name2' => $request->manager_last_name2,
            'nif_cif' => $request->manager_nif_cif,
            'birthday' => $request->manager_birthday,
            'phone' => $request->manager_phone,
            'comment' => $request->manager_comment,
        ]);

        // Manejo de imagen del manager
        if ($request->hasFile('manager_image')) {
            // Eliminar imagen anterior si existe
            if ($user->image && file_exists(public_path('manager/' . $user->image))) {
                unlink(public_path('manager/' . $user->image));
            }
            
            $file = $request->file('manager_image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $user->update(['image' => $filename]);
        }

        // Actualizar o crear relación manager-entity
        $manager = Manager::where('entity_id', $entity->id)->first();
        if ($manager) {
            $manager->update(['user_id' => $user->id]);
        } else {
            Manager::create([
                'user_id' => $user->id,
                'entity_id' => $entity->id,
                'is_primary' => true,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
                'status' => 1, // Activo por defecto para el gestor principal
            ]);
        }

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Gestor actualizado correctamente.');
    }

    /**
     * Show the form for editing manager permissions.
     * Si es gestor principal (is_primary), se muestra la vista en solo lectura con aviso.
     */
    public function edit_manager_permissions($entity_id, $manager_id)
    {
        $entity = Entity::with('managers.user')
            ->forUser(auth()->user())
            ->findOrFail($entity_id);

        $manager = Manager::with('user')
            ->where('id', $manager_id)
            ->where('entity_id', $entity_id)
            ->firstOrFail();

        return view('entities.edit_manager_permissions', compact('entity', 'manager'));
    }

    /**
     * Update manager permissions.
     * El gestor principal no puede tener permisos restringidos: se ignoran cambios y se mantienen todos en true.
     */
    public function update_manager_permissions(Request $request, $entity_id, $manager_id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($entity_id);

        $manager = Manager::where('id', $manager_id)
            ->where('entity_id', $entity_id)
            ->firstOrFail();

        if ($manager->is_primary) {
            return redirect()->route('entities.edit-manager-permissions', ['entity_id' => $entity->id, 'manager_id' => $manager->id])
                ->with('error', 'El gestor principal tiene todos los permisos y no se pueden restringir. Para cambiar los permisos de este gestor, primero debe asignar otro gestor como principal desde la ficha de la entidad.');
        }

        $request->validate([
            'permission_sellers' => 'nullable|boolean',
            'permission_design' => 'nullable|boolean',
            'permission_statistics' => 'nullable|boolean',
            'permission_payments' => 'nullable|boolean',
        ]);

        $manager->update([
            'permission_sellers' => $request->has('permission_sellers'),
            'permission_design' => $request->has('permission_design'),
            'permission_statistics' => $request->has('permission_statistics'),
            'permission_payments' => $request->has('permission_payments'),
        ]);

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Permisos del gestor actualizados correctamente.');
    }

    /**
     * Asignar otro gestor como principal. El actual principal deja de serlo.
     * Requiere que se envíe el ID del nuevo gestor principal (no puede quedar sin principal).
     */
    public function set_primary_manager(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id',
            'new_primary_manager_id' => 'required|integer|exists:managers,id',
        ]);

        $entity = Entity::forUser(auth()->user())->findOrFail($request->entity_id);

        $panelUser = User::where('panel_account_type', 'entity')
            ->where('panel_account_id', $entity->id)
            ->first();
        if ($panelUser) {
            $panelManager = Manager::where('entity_id', $entity->id)
                ->where('user_id', $panelUser->id)
                ->first();
            if ($panelManager && (int) $request->new_primary_manager_id !== (int) $panelManager->id) {
                return redirect()->route('entities.show', $entity->id)
                    ->with('error', 'El gestor principal es la cuenta de acceso al panel de la entidad. No puede sustituirse por otro; añada gestores secundarios con los permisos necesarios.');
            }
        }

        // Buscar gestor principal actual (puede no existir)
        $currentPrimary = Manager::where('entity_id', $entity->id)
            ->where('is_primary', true)
            ->first();

        $newPrimary = Manager::where('id', $request->new_primary_manager_id)
            ->where('entity_id', $entity->id)
            ->firstOrFail();
        
        // Si hay un gestor principal actual, verificar que no sea el mismo que el nuevo
        if ($currentPrimary && $newPrimary->id === $currentPrimary->id) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'El gestor seleccionado ya es el gestor principal.');
        }
        
        // Si hay un gestor principal actual, verificar que hay al menos otro gestor disponible
        if ($currentPrimary) {
            $otherManagers = Manager::where('entity_id', $entity->id)
                ->where('id', '!=', $currentPrimary->id)
                ->count();
            
            if ($otherManagers < 1) {
                return redirect()->route('entities.show', $entity->id)
                    ->with('error', 'No se puede quitar el gestor principal. Debe haber al menos otro gestor disponible para asignar como principal.');
            }
        }

        \DB::transaction(function () use ($entity, $newPrimary) {
            // Si hay un gestor principal actual, quitarlo primero
            Manager::where('entity_id', $entity->id)->update(['is_primary' => false]);
            // Asignar el nuevo gestor como principal
            $newPrimary->update([
                'is_primary' => true,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
            ]);
        });

        // Cuando se confirma/cambia gestor responsable: email de confirmación a la entidad
        try {
            $newPrimary->loadMissing('user');
            $entityContactUser = User::where('panel_account_type', 'entity')
                ->where('panel_account_id', $entity->id)
                ->first();
            if (!$entityContactUser) {
                $entityContactUser = $entity->manager?->user;
            }

            if ($entityContactUser && !empty($entityContactUser->email) && $newPrimary->user) {
                app(CommunicationEmailService::class)->sendAndLog(
                    recipientEmail: (string) $entityContactUser->email,
                    recipientRole: 'entidad',
                    recipientUser: $entityContactUser,
                    messageType: 'entity_responsible_manager_confirmed',
                    templateKey: null,
                    mailClass: EntityResponsibleManagerConfirmedMail::class,
                    mailPayload: ['entity_id' => $entity->id, 'responsible_manager_user_id' => $newPrimary->user->id],
                    context: ['entity_id' => $entity->id],
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Fallo enviando confirmación de gestor responsable: '.$e->getMessage());
        }

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Gestor principal actualizado correctamente. El gestor anterior ahora es gestor secundario.');
    }

    /**
     * Cambiar el status de un manager
     */
    public function toggle_manager_status(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|integer|exists:managers,id',
            'status' => 'required|integer|in:0,1',
        ]);

        $manager = Manager::findOrFail($request->manager_id);
        
        // Verificar que el manager pertenece a una entidad accesible por el usuario
        if ($manager->entity_id) {
            $entity = Entity::forUser(auth()->user())->find($manager->entity_id);
            if (!$entity) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para modificar este gestor'
                ], 403);
            }
        } elseif ($manager->administration_id) {
            $administration = Administration::forUser(auth()->user())->find($manager->administration_id);
            if (!$administration) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para modificar este gestor'
                ], 403);
            }
        }

        $manager->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status del gestor actualizado correctamente'
        ]);
    }

    /**
     * Eliminar un gestor (manager) de una entidad.
     * Solo elimina la relación manager-entity, NO elimina el usuario.
     * No se puede eliminar el gestor principal si es el único gestor.
     */
    public function destroy_manager($entity_id, $manager_id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($entity_id);
        
        $manager = Manager::where('id', $manager_id)
            ->where('entity_id', $entity_id)
            ->firstOrFail();

        $manager->load('user');
        if ($manager->user && $manager->user->panel_account_type === 'entity'
            && (int) $manager->user->panel_account_id === (int) $entity_id) {
            return redirect()->route('entities.show', $entity_id)
                ->with('error', 'No se puede eliminar la cuenta de acceso al panel de la entidad.');
        }

        // Verificar que no se está eliminando el gestor principal si es el único
        if ($manager->is_primary) {
            $totalManagers = Manager::where('entity_id', $entity_id)->count();
            
            if ($totalManagers <= 1) {
                return redirect()->route('entities.show', $entity_id)
                    ->with('error', 'No se puede eliminar el gestor principal. Debe haber al menos otro gestor disponible antes de eliminar el principal.');
            }
            
            // Si hay otros gestores, verificar que al menos uno no sea principal
            $otherManagers = Manager::where('entity_id', $entity_id)
                ->where('id', '!=', $manager_id)
                ->count();
            
            if ($otherManagers < 1) {
                return redirect()->route('entities.show', $entity_id)
                    ->with('error', 'No se puede eliminar el gestor principal. Debe asignar primero otro gestor como principal antes de eliminar este.');
            }
        }
        
        // Eliminar solo el manager (la relación), NO el usuario
        $manager->delete();
        
        return redirect()->route('entities.show', $entity_id)
            ->with('success', 'Gestor eliminado correctamente. El usuario asociado no ha sido eliminado.');
    }

    /**
     * Cambiar estado (Activo/Inactivo/Pendiente) de la entidad vía AJAX.
     */
    public function toggleStatus(Request $request, Entity $entity)
    {
        // Verificar permisos
        $entity = Entity::forUser(auth()->user())->findOrFail($entity->id);
        
        // Determinar el nuevo estado según el estado actual
        $currentStatus = $entity->status;
        
        // Lógica de toggle: null/-1 (Pendiente) -> 1 (Activo), 1 (Activo) -> 0 (Inactivo), 0 (Inactivo) -> 1 (Activo)
        $newStatus = match($currentStatus) {
            null, -1 => 1,  // Pendiente -> Activo
            1 => 0,         // Activo -> Inactivo
            0 => 1,         // Inactivo -> Activo
            default => 1
        };
        
        $entity->update(['status' => $newStatus]);
        
        // Obtener texto y clase del nuevo estado
        $statusValue = $entity->fresh()->status;
        if ($statusValue === null || $statusValue === -1) {
            $statusText = 'Pendiente';
            $statusClass = 'secondary';
        } elseif ($statusValue == 1) {
            $statusText = 'Activo';
            $statusClass = 'success';
        } else {
            $statusText = 'Inactivo';
            $statusClass = 'danger';
        }
        
        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'status_text' => $statusText,
            'status_class' => $statusClass,
        ]);
    }

    /**
     * Verificar si el email ya está en uso en entidades (para validación AJAX)
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'exclude_id' => 'nullable|integer'
        ]);

        $query = Entity::where('email', $request->email);
        
        // Excluir el ID actual si se está editando
        if ($request->exclude_id) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este email ya está en uso por otra entidad' : null
        ]);
    }
} 