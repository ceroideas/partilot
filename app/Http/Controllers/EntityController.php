<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Administration;
use App\Models\Manager;
use App\Models\User;

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
        // Guardar en sesión para persistencia (como en administrations)
        $request->session()->put('entity_manager', [
            'manager_name' => $request->input('manager_name', ''),
            'manager_last_name' => $request->input('manager_last_name', ''),
            'manager_last_name2' => $request->input('manager_last_name2', ''),
            'manager_nif_cif' => $request->input('manager_nif_cif', ''),
            'manager_birthday' => $request->input('manager_birthday', ''),
            'manager_email' => $request->input('manager_email', ''),
            'manager_phone' => $request->input('manager_phone', ''),
        ]);

        // Buscar usuario primero para excluirlo de la validación unique si existe
        $user = User::where('email', $request->manager_email)->first();
        $userId = $user ? $user->id : null;

        $validated = $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_last_name' => 'required|string|max:255',
            'manager_last_name2' => 'nullable|string|max:255',
            'manager_nif_cif' => ['nullable', 'string', 'max:20', new \App\Rules\EntityDocument, 'unique:users,nif_cif' . ($userId ? ',' . $userId : '')],
            'manager_birthday' => ['required', 'date', new \App\Rules\MinimumAge(18)],
            'manager_email' => 'required|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'manager_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if (!$user) {
            $user = new User;
            $user->name = $validated['manager_name'] . ' ' . $validated['manager_last_name'];
            $user->email = $validated['manager_email'];
            $user->password = bcrypt(12345678);
            $user->save();
        }

        // Actualizar datos del usuario si es necesario
        $user->update([
            'name' => $validated['manager_name'],
            'last_name' => $validated['manager_last_name'],
            'last_name2' => $validated['manager_last_name2'],
            'nif_cif' => $validated['manager_nif_cif'],
            'birthday' => $validated['manager_birthday'],
            'phone' => $validated['manager_phone'],
            'role' => User::ROLE_ENTITY,
        ]);

        // Manejo de imagen del manager
        if ($request->hasFile('manager_image')) {
            $file = $request->file('manager_image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $user->update(['image' => $filename]);
        }

        // Obtener datos de sesión
        $administration = $request->session()->get('selected_administration');
        $entityInformation = $request->session()->get('entity_information');

        if (!$administration || !auth()->user()->canAccessAdministration($administration->id) || !$entityInformation) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada o permisos insuficientes. Por favor, vuelva a empezar.');
        }

        // Crear entidad
        $entityData = array_merge($entityInformation, [
            'administration_id' => $administration->id,
        ]);

        $entity = Entity::create($entityData);

        // Crear la relación manager-entity (primer gestor es siempre principal con todos los permisos)
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

        // Limpiar sesión
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
        
        return response()->json([
            'exists' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
            'manager_name' => $user ? $user->name . ' ' . $user->last_name : null
        ]);
    }

    /**
     * Invitar gestor existente a una entidad
     */
    public function invite_manager(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id',
            'user_id' => 'required|integer|exists:users,id',
            'permission_sellers' => 'nullable|boolean',
            'permission_design' => 'nullable|boolean',
            'permission_statistics' => 'nullable|boolean',
            'permission_payments' => 'nullable|boolean',
        ]);

        $entity = Entity::forUser(auth()->user())->findOrFail($request->entity_id);

        // Verificar si ya existe un manager con este usuario para esta entidad
        $existingManager = Manager::where('user_id', $request->user_id)
            ->where('entity_id', $entity->id)
            ->first();

        if ($existingManager) {
            return redirect()->route('entities.show', $entity->id)
                ->with('error', 'Este usuario ya es gestor de esta entidad.');
        }

        // Crear la relación manager-entity (gestor secundario)
        Manager::create([
            'user_id' => $request->user_id,
            'entity_id' => $entity->id,
            'is_primary' => false,
            'permission_sellers' => $request->has('permission_sellers') ? true : false,
            'permission_design' => $request->has('permission_design') ? true : false,
            'permission_statistics' => $request->has('permission_statistics') ? true : false,
            'permission_payments' => $request->has('permission_payments') ? true : false,
            'status' => null, // Pendiente por defecto
        ]);

        $user = User::find($request->user_id);
        if ($user && $user->role !== User::ROLE_ENTITY) {
            $user->update(['role' => User::ROLE_ENTITY]);
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

        // Buscar usuario primero para excluirlo de la validación unique si existe
        $user = User::where('email', $request->manager_email)->first();
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
        if (!$user) {
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
        return view('entities.show', compact('entity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($id);
        $administrations = Administration::forUser(auth()->user())->get();
        $users = User::all();
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
            'remove_image' => 'nullable|in:0,1'
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

        unset($validated['remove_image']);
        $entity->update($validated);

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
            return redirect()->route('entities.show', $entity->id)
                ->with('info', 'El gestor principal tiene todos los permisos y no se pueden restringir. Para quitar como principal, asigne primero otro gestor como principal.');
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
        $newPrimary = Manager::where('id', $request->new_primary_manager_id)
            ->where('entity_id', $entity->id)
            ->firstOrFail();

        \DB::transaction(function () use ($entity, $newPrimary) {
            Manager::where('entity_id', $entity->id)->update(['is_primary' => false]);
            $newPrimary->update([
                'is_primary' => true,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
            ]);
        });

        return redirect()->route('entities.show', $entity->id)
            ->with('success', 'Gestor principal actualizado correctamente.');
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