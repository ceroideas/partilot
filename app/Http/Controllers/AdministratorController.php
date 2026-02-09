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
        $administration = Administration::with('manager')
            ->forUser(auth()->user())
            ->findOrFail($id);
        return view('admins.edit', compact('administration'));
    }

    public function update(Request $request, $id)
    {
        $administration = Administration::forUser(auth()->user())->findOrFail($id);
        
        // Saneamiento IBAN: quitar espacios, prefijo ES duplicado y dejar solo dígitos
        $accountValue = $this->sanitizeIbanAccount($request->account);
        $request->merge(['account' => $accountValue ?? '']);
        
        // Validar formato básico primero
        $request->validate([
            'web' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'receiving' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'society' => 'required|string|max:255',
            'nif_cif' => ['required', 'string', 'max:255', new \App\Rules\SpanishDocument],
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'account' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if ($digits !== '' && strlen($digits) !== 22) {
                        $fail('La cuenta bancaria debe estar vacía o tener exactamente 22 dígitos.');
                    }
                },
            ],
            'status' => 'nullable|in:-1,0,1',
        ]);

        // Validar IBAN completo solo si se proporciona cuenta
        if ($accountValue) {
            $iban = 'ES' . $accountValue;
            $validator = \Validator::make(['iban' => $iban], [
                'iban' => [new \App\Rules\SpanishIban]
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        $data = [
            "web" => $request->web ?? '',
            "name" => $request->name,
            "receiving" => $request->receiving,
            "admin_number" => $request->admin_number ?? null,
            "society" => $request->society,
            "nif_cif" => $request->nif_cif,
            "province" => $request->province,
            "city" => $request->city,
            "postal_code" => $request->postal_code,
            "address" => $request->address,
            "email" => $request->email,
            "phone" => $request->phone,
            "account" => $accountValue ? ('ES' . $accountValue) : null,
            "status" => $request->status === '-1' ? null : ($request->status ?? null),
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
        // Saneamiento IBAN: quitar espacios, prefijo ES duplicado y dejar solo dígitos
        $accountValue = $this->sanitizeIbanAccount($request->account);
        
        $data = [
            "web" => isset($request->web) ? $request->validated()['web'] : '',
            "name" => $request->validated()['name'],
            "receiving" => $request->validated()['receiving'],
            "admin_number" => $request->validated()['admin_number'] ?? null,
            "society" => $request->validated()['society'],
            "nif_cif" => $request->validated()['nif_cif'],
            "province" => $request->validated()['province'],
            "city" => $request->validated()['city'],
            "postal_code" => $request->validated()['postal_code'],
            "address" => $request->validated()['address'],
            "email" => $request->validated()['email'],
            "phone" => $request->validated()['phone'],
            "account" => $accountValue ? ('ES' . $accountValue) : null,
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

        // Buscar usuario primero para excluirlo de la validación unique si existe
        $existingUser = User::where('email', $request->email)->first();
        $userId = $existingUser ? $existingUser->id : null;
        
        // Validar manualmente para manejar errores
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "last_name2" => "nullable|string|max:255",
            "nif_cif" => ["nullable", "string", "max:255", new \App\Rules\SpanishDocument, "unique:users,nif_cif" . ($userId ? ',' . $userId : '')],
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
            $u->role = User::ROLE_ADMINISTRATION;
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
            'role' => User::ROLE_ADMINISTRATION,
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
                'administration_id' => null, // Se asignará después de crear la administración
                'is_primary' => true,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
                'status' => 1, // Activo por defecto para el gestor principal
            ]);
        }

        $administration = $request->session()->get("administration");
        // La relación manager-administration se maneja a través de entities
        
        // Establecer status como pendiente por defecto
        $administration['status'] = null; // null = Pendiente

        $newAdministration = Administration::create($administration);

        // Actualizar el manager con el administration_id
        $manager->update(['administration_id' => $newAdministration->id]);

        $request->session()->forget(['administration', 'manager']);

        return redirect('administrations')->with('success', 'Administración creada exitosamente.');
    }

    /**
     * Sanea el valor de cuenta/IBAN: quita espacios, prefijo ES duplicado y deja solo dígitos.
     */
    private function sanitizeIbanAccount($value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $raw = preg_replace('/\s+/', '', trim($value));
        $raw = preg_replace('/^ES/i', '', $raw); // quitar prefijo ES si el usuario lo pegó
        $digits = preg_replace('/\D/', '', $raw);
        return $digits !== '' ? $digits : null;
    }

    /**
     * Cambiar estado (Activo/Inactivo/Pendiente) de la administración vía AJAX.
     */
    public function toggleStatus(Request $request, Administration $administration)
    {
        // Verificar permisos
        $administration = Administration::forUser(auth()->user())->findOrFail($administration->id);
        
        // Determinar el nuevo estado según el estado actual
        $currentStatus = $administration->status;
        
        // Lógica de toggle: null/-1 (Pendiente) -> 1 (Activo), 1 (Activo) -> 0 (Inactivo), 0 (Inactivo) -> 1 (Activo)
        $newStatus = match($currentStatus) {
            null, -1 => 1,  // Pendiente -> Activo
            1 => 0,         // Activo -> Inactivo
            0 => 1,         // Inactivo -> Activo
            default => 1
        };
        
        $administration->update(['status' => $newStatus]);
        
        // Obtener texto y clase del nuevo estado
        $statusValue = $administration->fresh()->status;
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
     * Verificar si el email ya está en uso en administraciones (para validación AJAX)
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'exclude_id' => 'nullable|integer'
        ]);

        $query = Administration::where('email', $request->email);
        
        // Excluir el ID actual si se está editando
        if ($request->exclude_id) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este email ya está en uso por otra administración' : null
        ]);
    }
}
