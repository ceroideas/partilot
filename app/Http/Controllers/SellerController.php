<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::with('entity')->get();
        return view('sellers.index', compact('sellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entities = Entity::with('administration')->get();
        return view('sellers.add', compact('entities'));
    }

    /**
     * Store the selected entity in session
     */
    public function store_entity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id'
        ]);

        $entity = Entity::with('administration')->findOrFail($request->entity_id);
        session(['selected_entity' => $entity]);

        return redirect()->route('sellers.add-information');
    }

    /**
     * Show the add information form
     */
    public function add_information()
    {
        if (!session('selected_entity')) {
            return redirect()->route('sellers.create');
        }

        return view('sellers.add_information');
    }

    /**
     * Store a seller with existing user
     */
    public function store_existing_user(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'entity_id' => 'required|exists:entities,id'
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->first();
            $entity = Entity::findOrFail($request->entity_id);

            // Crear el vendedor con los datos del usuario existente
            $seller = Seller::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'last_name2' => $user->last_name2,
                'nif_cif' => $user->nif_cif,
                'birthday' => $user->birthday,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => 1, // Activo por defecto
                'entity_id' => $entity->id
            ]);

            DB::commit();

            session()->forget('selected_entity');
            return redirect()->route('sellers.index')->with('success', 'Vendedor creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a seller with new user
     */
    public function store_new_user(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'entity_id' => 'required|exists:entities,id'
        ]);

        try {
            DB::beginTransaction();

            $entity = Entity::findOrFail($request->entity_id);

            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('password123'), // Contraseña temporal
            ]);

            // Crear el vendedor
            $seller = Seller::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 1, // Activo por defecto
                'entity_id' => $entity->id
            ]);

            DB::commit();

            session()->forget('selected_entity');
            return redirect()->route('sellers.index')->with('success', 'Vendedor creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if user email exists
     */
    public function check_user_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $seller = Seller::with(['entity.administration'])->findOrFail($id);
        return view('sellers.show', compact('seller'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $seller = Seller::findOrFail($id);
        return view('sellers.edit', compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'email' => 'required|email|unique:users,email,' . ($seller->user_id ?? 0),
            'phone' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el vendedor
            $seller->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            // Actualizar el usuario si existe
            if ($seller->user_id) {
                $user = User::find($seller->user_id);
                if ($user) {
                    $user->update([
                        'name' => $request->name,
                        'last_name' => $request->last_name,
                        'last_name2' => $request->last_name2,
                        'nif_cif' => $request->nif_cif,
                        'birthday' => $request->birthday,
                        'email' => $request->email,
                        'phone' => $request->phone
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sellers.index')->with('success', 'Vendedor actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $seller = Seller::findOrFail($id);
            $seller->delete();

            return redirect()->route('sellers.index')->with('success', 'Vendedor eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el vendedor: ' . $e->getMessage()]);
        }
    }
} 