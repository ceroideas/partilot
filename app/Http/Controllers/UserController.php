<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'required|string|max:20|unique:users',
            'birthday' => 'required|date',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Procesar imagen si se subió
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            }

            // Crear usuario
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'image' => $imagePath,
                'status' => true,
            ]);

            // El UserObserver se encargará de vincular vendedores automáticamente
            Log::info("Usuario creado: {$user->id} - {$user->email}");

            DB::commit();

            return redirect()->route('users.show', $user->id)
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear usuario: " . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error al crear el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'required|string|max:20|unique:users,nif_cif,' . $user->id,
            'birthday' => 'required|date',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Procesar imagen si se subió
            $imagePath = $user->image;
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior si existe
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }
                $imagePath = $request->file('image')->store('users', 'public');
            }

            // Preparar datos de actualización
            $updateData = [
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'image' => $imagePath,
            ];

            // Actualizar contraseña solo si se proporcionó
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Actualizar vendedores vinculados si el email cambió
            if ($request->email !== $user->getOriginal('email')) {
                $this->updateLinkedSellers($user);
            }

            DB::commit();

            return redirect()->route('users.show', $user->id)
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar usuario: " . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // Eliminar imagen si existe
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Desvincular vendedores antes de eliminar el usuario
            Seller::where('user_id', $user->id)->update(['user_id' => 0]);

            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar usuario: " . $e->getMessage());
            
            return back()->with('error', 'Error al eliminar el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Obtener datos para DataTable
     */
    public function data(Request $request)
    {
        $query = User::query();

        // Aplicar filtros
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('entity')) {
            // Filtrar por entidad a través de vendedores vinculados
            $query->whereHas('sellers', function($q) use ($request) {
                $q->where('entity_id', $request->entity);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return datatables($query)
            ->addColumn('province', function($user) {
                // Obtener provincia desde vendedores vinculados
                $seller = $user->sellers()->first();
                return $seller ? $seller->entity->province ?? 'Sin provincia' : 'Sin provincia';
            })
            ->addColumn('city', function($user) {
                // Obtener ciudad desde vendedores vinculados
                $seller = $user->sellers()->first();
                return $seller ? $seller->entity->city ?? 'Sin localidad' : 'Sin localidad';
            })
            ->addColumn('pending_amount', function($user) {
                // Calcular importe pendiente (implementar lógica según necesidades)
                return 0.00;
            })
            ->addColumn('actions', function($user) {
                return view('users.actions', compact('user'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Actualizar vendedores vinculados cuando cambia el email del usuario
     */
    private function updateLinkedSellers(User $user)
    {
        $linkedSellers = Seller::where('user_id', $user->id)->get();
        
        foreach ($linkedSellers as $seller) {
            $seller->update([
                'email' => $user->email,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'last_name2' => $user->last_name2,
                'nif_cif' => $user->nif_cif,
                'birthday' => $user->birthday,
                'phone' => $user->phone,
                'image' => $user->image,
                'status' => $user->status,
            ]);
        }
        
        Log::info("Vendedores vinculados actualizados para usuario: {$user->id}");
    }
}
