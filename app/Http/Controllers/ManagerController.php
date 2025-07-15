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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'comment' => 'nullable|string|max:1000',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Actualizar o crear usuario
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $user = new User;
            $user->name = $request->name . ' ' . $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt(12345678);
            $user->save();
        } else {
            // Actualizar nombre del usuario si es diferente
            $user->name = $request->name . ' ' . $request->last_name;
            $user->save();
        }

        // Preparar datos del manager
        $managerData = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'last_name2' => $request->last_name2,
            'nif_cif' => $request->nif_cif,
            'birthday' => $request->birthday,
            'email' => $request->email,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'user_id' => $user->id
        ];

        // Manejo de imagen del manager
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($manager->image && file_exists(public_path('manager/' . $manager->image))) {
                unlink(public_path('manager/' . $manager->image));
            }
            
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $managerData['image'] = $filename;
        }

        $manager->update($managerData);

        // Redirección según el origen
        if ($request->has('origin') && $request->origin === 'entity') {
            // Buscar la entidad asociada a este manager
            $entity = \App\Models\Entity::where('manager_id', $manager->id)->first();
            if ($entity) {
                return redirect()->route('entities.show', $entity->id)
                    ->with('success', 'Gestor actualizado correctamente.');
            }
        } else {
            // Buscar la administración asociada a este manager
            $administration = \App\Models\Administration::where('manager_id', $manager->id)->first();
            if ($administration) {
                return redirect()->route('administrations.show', $administration->id)
                    ->with('success', 'Gestor actualizado correctamente.');
            }
        }
        
        return redirect()->back()
            ->with('success', 'Gestor actualizado correctamente.');
    }

    /**
     * Remove the specified manager from storage.
     */
    public function destroy($id)
    {
        $manager = Manager::findOrFail($id);
        
        // Eliminar imagen si existe
        if ($manager->image && file_exists(public_path('manager/' . $manager->image))) {
            unlink(public_path('manager/' . $manager->image));
        }

        $manager->delete();

        return redirect()->back()
            ->with('success', 'Gestor eliminado exitosamente.');
    }
} 