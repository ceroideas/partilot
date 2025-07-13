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
            'manager_comment' => 'nullable|string|max:1000',
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
            'comment' => $validated['manager_comment'],
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