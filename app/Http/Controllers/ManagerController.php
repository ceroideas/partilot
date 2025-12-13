<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\User;

class ManagerController extends Controller
{
    /**
     * Show the form for editing the specified manager.
     */
    public function edit($id)
    {
        $manager = Manager::with('user')->findOrFail($id);
        return view('managers.edit', compact('manager'));
    }

    /**
     * Update the specified manager in storage.
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
        $manager = Manager::findOrFail($id);
        
        // Buscar usuario primero para excluirlo de la validación unique si existe
        $user = User::where('email', $request->email)->first();
        $userId = $user ? $user->id : null;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => ['nullable', 'string', 'max:20', 'unique:users,nif_cif' . ($userId ? ',' . $userId : '')],
            'birthday' => ['nullable', 'date', new \App\Rules\MinimumAge(18)],
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'comment' => 'nullable|string|max:1000',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if (!$user) {
            $user = new User;
            $user->name = $request->name . ' ' . $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt(12345678);
            $user->save();
        }

        // Actualizar datos del usuario
        $user->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'last_name2' => $request->last_name2,
            'nif_cif' => $request->nif_cif,
            'birthday' => $request->birthday,
            'phone' => $request->phone,
            'comment' => $request->comment,
        ]);

        // Manejo de imagen del manager
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($user->image && file_exists(public_path('manager/' . $user->image))) {
                unlink(public_path('manager/' . $user->image));
            }
            
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $user->update(['image' => $filename]);
        }

        // Actualizar la relación manager-entity
        $manager->update(['user_id' => $user->id]);

        /*// Redirección según el origen
        if ($request->has('origin') && $request->origin === 'entities') {
            // Buscar la entidad asociada a este manager
            $entity = $manager->entity;
            if ($entity) {
                return redirect()->route('entities.show', $entity->id)
                    ->with('success', 'Gestor actualizado correctamente.');
            }
        } else {
            // Buscar la administración asociada a este manager a través de la entidad
            $entity = $manager->entity;
            if ($entity && $entity->administration) {
                return redirect()->route('administrations.show', $entity->administration->id)
                    ->with('success', 'Gestor actualizado correctamente.');
            }
        }*/
        
        return redirect()->back()
            ->with('success', 'Gestor actualizado correctamente.');
    }

    /**
     * Remove the specified manager from storage.
     */
    public function destroy($id)
    {
        $manager = Manager::findOrFail($id);
        
        // Eliminar imagen del usuario si existe
        $user = $manager->user;
        if ($user && $user->image && file_exists(public_path('manager/' . $user->image))) {
            unlink(public_path('manager/' . $user->image));
        }

        // Eliminar la relación manager-entity
        $manager->delete();

        return redirect()->back()
            ->with('success', 'Gestor eliminado exitosamente.');
    }
} 