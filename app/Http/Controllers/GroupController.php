<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Entity;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::with(['entity', 'sellers'])
            ->forUser(auth()->user())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource - Paso 1: Seleccionar entidad
     */
    public function create()
    {
        $entities = Entity::with('administration')
            ->forUser(auth()->user())
            ->get();
        return view('groups.add', compact('entities'));
    }

    /**
     * Store the selected entity in session and redirect to step 2
     */
    public function storeEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id'
        ]);

        $entity = Entity::with('administration')
            ->forUser(auth()->user())
            ->findOrFail($request->entity_id);
        session(['group_entity' => $entity]);

        return redirect()->route('groups.add-information');
    }

    /**
     * Show the form for step 2 - Datos del Grupo
     */
    public function addInformation()
    {
        if (!session('group_entity')) {
            return redirect()->route('groups.create');
        }

        $entityData = session('group_entity');
        
        // Si es un array (serializado), obtener la entidad de la BD
        if (is_array($entityData)) {
            $entity = Entity::with('administration')
                ->forUser(auth()->user())
                ->findOrFail($entityData['id']);
        } else {
            $entity = $entityData;
        }

        if (!auth()->user()->canAccessEntity($entity->id)) {
            return redirect()->route('groups.create')
                ->with('error', 'No tienes permisos para gestionar esta entidad.');
        }
        
        // Obtener vendedores de la entidad seleccionada que NO tienen grupo asignado
        $sellers = Seller::whereHas('entities', function($query) use ($entity) {
            $query->where('entities.id', $entity->id);
        })
        ->whereDoesntHave('groups')
        ->get();

        return view('groups.add_information', compact('entity', 'sellers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'entity_id' => 'required|exists:entities,id',
            'seller_ids' => 'nullable|array',
            'seller_ids.*' => 'exists:sellers,id'
        ]);

        try {
            DB::beginTransaction();

            if (!auth()->user()->canAccessEntity((int) $request->entity_id)) {
                abort(403, 'No tienes permisos para gestionar esta entidad.');
            }

            $entity = Entity::forUser(auth()->user())->findOrFail($request->entity_id);

            // Crear el grupo
            $group = Group::create([
                'name' => $request->name,
                'entity_id' => $request->entity_id,
                'province' => $entity->province
            ]);

            // Asociar vendedores al grupo
            if ($request->has('seller_ids') && !empty($request->seller_ids)) {
                $sellerIds = $request->seller_ids;
                // Asegurarse de que sea un array
                if (!is_array($sellerIds)) {
                    $sellerIds = is_string($sellerIds) ? json_decode($sellerIds, true) : [$sellerIds];
                }
                // Filtrar valores vacíos o nulos
                $sellerIds = array_filter($sellerIds, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($sellerIds)) {
                    $accessibleSellerIds = collect($sellerIds)
                        ->filter(fn ($id) => auth()->user()->canAccessSeller((int) $id))
                        ->values()
                        ->all();

                    if (!empty($accessibleSellerIds)) {
                        $group->sellers()->sync($accessibleSellerIds);
                    }
                }
            }

            DB::commit();

            session()->forget('group_entity');

            return redirect()->route('groups.index')->with('success', 'Grupo creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el grupo: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $group = Group::with(['entity', 'sellers'])
            ->forUser(auth()->user())
            ->findOrFail($id);
        $entity = $group->entity;
        
        // Obtener vendedores de la entidad del grupo que NO tienen grupo asignado
        // O que ya están en este grupo
        $sellersInGroup = $group->sellers->pluck('id')->toArray();
        $sellers = Seller::whereHas('entities', function($query) use ($entity) {
            $query->where('entities.id', $entity->id);
        })
        ->where(function($query) use ($sellersInGroup) {
            $query->whereDoesntHave('groups')
                  ->orWhereIn('id', $sellersInGroup);
        })
        ->get();

        return view('groups.edit', compact('group', 'entity', 'sellers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'seller_ids' => 'nullable|array',
            'seller_ids.*' => 'exists:sellers,id'
        ]);

        try {
            DB::beginTransaction();

            $group = Group::forUser(auth()->user())->findOrFail($id);

            // Actualizar el nombre del grupo
            $group->update([
                'name' => $request->name
            ]);

            // Actualizar vendedores del grupo
            if ($request->has('seller_ids') && !empty($request->seller_ids)) {
                $sellerIds = $request->seller_ids;
                // Asegurarse de que sea un array
                if (!is_array($sellerIds)) {
                    $sellerIds = is_string($sellerIds) ? json_decode($sellerIds, true) : [$sellerIds];
                }
                if (is_array($sellerIds) && !empty($sellerIds)) {
                    $accessibleSellerIds = collect($sellerIds)
                        ->filter(fn ($sellerId) => auth()->user()->canAccessSeller((int) $sellerId))
                        ->values()
                        ->all();

                    $group->sellers()->sync($accessibleSellerIds);
                } else {
                    $group->sellers()->detach();
                }
            } else {
                $group->sellers()->detach();
            }

            DB::commit();

            return redirect()->route('groups.index')->with('success', 'Grupo actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el grupo: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $group = Group::forUser(auth()->user())->findOrFail($id);
            $group->delete();

            return redirect()->route('groups.index')->with('success', 'Grupo eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el grupo: ' . $e->getMessage()]);
        }
    }
}
