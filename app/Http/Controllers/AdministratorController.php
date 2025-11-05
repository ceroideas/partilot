<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CreateAdmin;
use App\Http\Requests\CreateManager;
use App\Models\Administration;
use App\Models\User;
use App\Models\Manager;

class AdministratorController extends Controller
{
    //

    public function create()
    {

    }

    public function edit($id)
    {
        $administration = Administration::with('manager')->findOrFail($id);
        return view('admins.edit', compact('administration'));
    }

    public function update(Request $request, $id)
    {
        $administration = Administration::findOrFail($id);
        
        $request->validate([
            'web' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'receiving' => 'required|string|max:255',
            'society' => 'required|string|max:255',
            'nif_cif' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            /*'account' => 'required|array',
            'account.*' => 'required|string|max:4',*/
            'status' => 'nullable|boolean',
        ]);

        $data = [
            "web" => $request->web ?? '',
            "name" => $request->name,
            "receiving" => $request->receiving,
            "society" => $request->society,
            "nif_cif" => $request->nif_cif,
            "province" => $request->province,
            "city" => $request->city,
            "postal_code" => $request->postal_code,
            "address" => $request->address,
            "email" => $request->email,
            "phone" => $request->phone,
            "account" => implode(' ', $request->account),
            "status" => $request->status ?? true,
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('images'), $filename);
            $data["image"] = $filename;
        }

        $administration->update($data);

        return redirect()->route('administrations.show', $administration->id)
                        ->with('success', 'Administración actualizada correctamente');
    }

    public function store_information(CreateAdmin $request)
    {
        $data = [
            "web" => isset($request->web) ? $request->validated()['web'] : '',
            "name" => $request->validated()['name'],
            "receiving" => $request->validated()['receiving'],
            "society" => $request->validated()['society'],
            "nif_cif" => $request->validated()['nif_cif'],
            "province" => $request->validated()['province'],
            "city" => $request->validated()['city'],
            "postal_code" => $request->validated()['postal_code'],
            "address" => $request->validated()['address'],
            "email" => $request->validated()['email'],
            "phone" => $request->validated()['phone'],
            "account" => implode(' ', $request->account),
        ];
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('images'), $filename);
            $data["image"] = $filename;
        }

        $request->session()->put('administration', $data);

        // Inicializar datos del gestor vacíos si no existen
        if (!$request->session()->has('manager')) {
            $request->session()->put('manager', [
                'name' => '',
                'last_name' => '',
                'last_name2' => '',
                'nif_cif' => '',
                'birthday' => '',
                'email' => '',
                'phone' => '',
                'comment' => '',
                'image' => ''
            ]);
        }

        // Redirigir al GET para asegurar persistencia de sesión
        return redirect()->route('administrations.add-manager');
    }

    public function store(Request $request)
    {
        // Si viene el campo 'web' desde el paso 2, reflejarlo en la sesión de administración
        if ($request->filled('web')) {
            $administrationSession = $request->session()->get('administration', []);
            $administrationSession['web'] = $request->input('web');
            $request->session()->put('administration', $administrationSession);
        }

        // Guardar datos del gestor en sesión antes de validar
        $request->session()->put('manager', [
            'name' => $request->name ?? '',
            'last_name' => $request->last_name ?? '',
            'last_name2' => $request->last_name2 ?? '',
            'nif_cif' => $request->nif_cif ?? '',
            'birthday' => $request->birthday ?? '',
            'email' => $request->email ?? '',
            'phone' => $request->phone ?? '',
            'comment' => $request->comment ?? '',
            'image' => $request->hasFile('image') ? 'pending' : ''
        ]);

        // Validar manualmente para manejar errores
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "last_name2" => "nullable|string|max:255",
            "nif_cif" => "nullable|string|max:255",
            "birthday" => ["required", "date", new \App\Rules\MinimumAge(18)],
            "email" => "required|string|max:255",
            "phone" => "nullable|string|max:255",
            "comment" => "nullable|string|max:255",
        ]);

        $data = [
            "name" => $validated["name"],
            "last_name" => $validated["last_name"],
            "last_name2" => $validated["last_name2"] ?? null,
            "nif_cif" => $validated["nif_cif"] ?? null,
            "birthday" => $validated["birthday"],
            "email" => $validated["email"],
            "phone" => $validated["phone"] ?? null,
            "comment" => $validated["comment"] ?? null,
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $data["image"] = $filename;
        }

        $u = User::where('email', $validated["email"])->first();
        if (!$u) {
            $u = new User;
            $u->name = $validated["name"].' '.$validated["last_name"];
            $u->email = $validated["email"];
            $u->password = bcrypt(12345678);
            $u->save();
        }

        // Actualizar datos del usuario
        $u->update([
            'name' => $validated["name"],
            'last_name' => $validated["last_name"],
            'last_name2' => $validated["last_name2"] ?? null,
            'nif_cif' => $validated["nif_cif"] ?? null,
            'birthday' => $validated["birthday"],
            'phone' => $validated["phone"] ?? null,
            'comment' => $validated["comment"] ?? null,
        ]);

        // Manejo de imagen del manager
        if ($request->file('image')) {
            $u->update(['image' => $data["image"]]);
        }

        // Verificar si ya existe un manager con este usuario para esta administración
        $manager = Manager::where('user_id', $u->id)
                          ->where('administration_id', null)
                          ->first();

        if (!$manager) {
            // Crear el manager con user_id y administration_id
            $manager = Manager::create([
                'user_id' => $u->id,
                'administration_id' => null // Se asignará después de crear la administración
            ]);
        }

        $administration = $request->session()->get("administration");
        // La relación manager-administration se maneja a través de entities

        $newAdministration = Administration::create($administration);

        // Actualizar el manager con el administration_id
        $manager->update(['administration_id' => $newAdministration->id]);

        $request->session()->forget(['administration', 'manager']);

        return redirect('administrations')->with('success', 'Administración creada exitosamente.');
    }
}
