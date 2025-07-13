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
        $entities = Entity::with(['administration', 'manager'])->get();
        return view('entities.index', compact('entities'));
    }

    /**
     * Show the form for creating a new resource - Paso 1: Seleccionar administración
     */
    public function create()
    {
        $administrations = Administration::all();
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

        $administration = Administration::find($request->administration_id);
        $request->session()->put('selected_administration', $administration);

        return view('entities.add_information');
    }

    /**
     * Store entity information and show manager form - Paso 3: Datos del gestor
     */
    public function store_information(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'nif_cif' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|string|max:255',
            'comments' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Manejo de imagen
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('uploads'), $filename);
            $validated['image'] = $filename;
        }

        $request->session()->put('entity_information', $validated);

        return view('entities.add_manager');
    }

    /**
     * Store manager information and create the complete entity - Paso final
     */
    public function store_manager(Request $request)
    {
        $validated = $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_last_name' => 'required|string|max:255',
            'manager_last_name2' => 'nullable|string|max:255',
            'manager_nif_cif' => 'nullable|string|max:20',
            'manager_birthday' => 'nullable|date',
            'manager_email' => 'required|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            // 'manager_comment' => 'nullable|string|max:1000',
            'manager_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Crear o encontrar usuario
        $user = User::where('email', $validated['manager_email'])->first();
        if (!$user) {
            $user = new User;
            $user->name = $validated['manager_name'] . ' ' . $validated['manager_last_name'];
            $user->email = $validated['manager_email'];
            $user->password = bcrypt(12345678);
            $user->save();
        }

        // Crear manager
        $managerData = [
            'name' => $validated['manager_name'],
            'last_name' => $validated['manager_last_name'],
            'last_name2' => $validated['manager_last_name2'],
            'nif_cif' => $validated['manager_nif_cif'],
            'birthday' => $validated['manager_birthday'],
            'email' => $validated['manager_email'],
            'phone' => $validated['manager_phone'],
            // 'comment' => $validated['manager_comment'],
            'user_id' => $user->id
        ];

        // Manejo de imagen del manager
        if ($request->hasFile('manager_image')) {
            $file = $request->file('manager_image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $managerData['image'] = $filename;
        }

        $manager = Manager::create($managerData);

        // Obtener datos de sesión
        $administration = $request->session()->get('selected_administration');
        $entityInformation = $request->session()->get('entity_information');

        // Crear entidad
        $entityData = array_merge($entityInformation, [
            'administration_id' => $administration->id,
            'manager_id' => $manager->id
        ]);

        Entity::create($entityData);

        // Limpiar sesión
        $request->session()->forget(['selected_administration', 'entity_information']);

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
        
        // Buscar si existe un manager con ese email
        $manager = Manager::where('email', $email)->first();
        
        return response()->json([
            'exists' => $manager ? true : false,
            'manager_id' => $manager ? $manager->id : null,
            'manager_name' => $manager ? $manager->name . ' ' . $manager->last_name : null
        ]);
    }

    /**
     * Invitar gestor existente y crear entidad
     */
    public function invite_manager(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|integer|exists:managers,id',
            'invite_email' => 'required|email'
        ]);

        // Obtener datos de sesión
        $administration = $request->session()->get('selected_administration');
        $entityInformation = $request->session()->get('entity_information');

        if (!$administration || !$entityInformation) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada. Por favor, vuelva a empezar.');
        }

        // Crear entidad con el manager existente
        $entityData = array_merge($entityInformation, [
            'administration_id' => $administration->id,
            'manager_id' => $request->manager_id
        ]);

        Entity::create($entityData);

        // Limpiar sesión
        $request->session()->forget(['selected_administration', 'entity_information']);

        return redirect()->route('entities.index')
            ->with('success', 'Entidad creada exitosamente con gestor invitado.');
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

        if (!$administration || !$entityInformation) {
            return redirect()->route('entities.create')
                ->with('error', 'Sesión expirada. Por favor, vuelva a empezar.');
        }

        // Crear entidad sin manager (se asignará cuando se registre)
        $entityData = array_merge($entityInformation, [
            'administration_id' => $administration->id,
            'manager_id' => null // Se asignará cuando el usuario se registre
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

        // Crear manager de prueba
        $manager = Manager::firstOrCreate(
            ['email' => 'test@manager.com'],
            [
                'name' => 'Test',
                'last_name' => 'Manager',
                'last_name2' => 'Apellido2',
                'nif_cif' => '12345678A',
                'birthday' => '1990-01-01',
                'email' => 'test@manager.com',
                'phone' => '123456789',
                'user_id' => $user->id
            ]
        );

        return response()->json([
            'message' => 'Gestor de prueba creado exitosamente',
            'manager_id' => $manager->id,
            'email' => $manager->email
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        $entity->load(['administration', 'manager']);
        return view('entities.show', compact('entity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        $administrations = Administration::all();
        $managers = Manager::all();
        return view('entities.edit', compact('entity', 'administrations', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'administration_id' => 'nullable|integer|exists:administrations,id',
            'manager_id' => 'nullable|integer|exists:managers,id',
            'name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'nif_cif' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'comments' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Manejo de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($entity->image && file_exists(public_path('uploads/' . $entity->image))) {
                unlink(public_path('uploads/' . $entity->image));
            }
            
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('uploads'), $filename);
            $validated['image'] = $filename;
        }

        $entity->update($validated);

        return redirect()->route('entities.index')
            ->with('success', 'Entidad actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        // Eliminar imagen si existe
        if ($entity->image && file_exists(public_path('uploads/' . $entity->image))) {
            unlink(public_path('uploads/' . $entity->image));
        }

        $entity->delete();

        return redirect()->route('entities.index')
            ->with('success', 'Entidad eliminada exitosamente.');
    }
} 