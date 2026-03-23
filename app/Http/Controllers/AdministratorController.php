<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Http\Requests\CreateAdmin;
use App\Models\Administration;
use App\Models\User;
use App\Models\Manager;
use App\Mail\AdministrationWelcomeMail;
use App\Services\CommunicationEmailService;

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
            'panel_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Validar IBAN completo solo si se proporciona cuenta
        if ($accountValue) {
            $iban = 'ES' . $accountValue;
            $validator = \Validator::make(['iban' => $iban], [
                'iban' => [new \App\Rules\SpanishIban],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        $newEmail = $request->email;
        $panelUser = User::query()
            ->where('panel_account_type', 'administration')
            ->where('panel_account_id', $administration->id)
            ->first();

        if ($panelUser && strcasecmp((string) $panelUser->email, (string) $newEmail) !== 0) {
            if (User::where('email', $newEmail)->where('id', '!=', $panelUser->id)->exists()) {
                return back()->withErrors(['email' => 'Este email ya está en uso por otro usuario.'])->withInput();
            }
        }

        $data = [
            'web' => $request->web ?? '',
            'name' => $request->name,
            'receiving' => $request->receiving,
            'admin_number' => $request->admin_number ?? null,
            'society' => $request->society,
            'nif_cif' => $request->nif_cif,
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'email' => $newEmail,
            'phone' => $request->phone,
            'account' => $accountValue ? ('ES' . $accountValue) : null,
            'status' => $request->status === '-1' ? null : ($request->status ?? null),
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('images'), $filename);
            $data['image'] = $filename;
        }

        $administration->update($data);

        if ($panelUser) {
            $u = [
                'email' => $newEmail,
                'name' => Administration::panelDisplayNameFromParts($data['name'] ?? '', $data['society'] ?? ''),
                'phone' => $request->phone,
                'nif_cif' => $request->nif_cif,
            ];
            if ($request->filled('panel_password')) {
                $u['password'] = $request->panel_password;
            }
            $panelUser->update($u);
        }

        // Contraseña de panel definida: si la administración seguía pendiente, pasar a activa.
        if ($request->filled('panel_password')) {
            $administration->refresh();
            if ($administration->status === null || $administration->status === -1) {
                $administration->update(['status' => 1]);
            }
        }

        return redirect()->route('administrations.show', $administration->id)
            ->with('success', 'Administración actualizada correctamente');
    }

    /**
     * Envío manual (superadmin): correo con usuario de panel y enlace mágico para establecer contraseña.
     */
    public function sendPanelAccessEmail(Administration $administration)
    {
        $administration = Administration::forUser(auth()->user())->findOrFail($administration->id);

        $panelUser = User::query()
            ->where('panel_account_type', 'administration')
            ->where('panel_account_id', $administration->id)
            ->firstOrFail();

        try {
            app(CommunicationEmailService::class)->sendAndLog(
                recipientEmail: (string) $panelUser->email,
                recipientRole: 'gestor_administracion',
                recipientUser: $panelUser,
                messageType: 'administration_welcome',
                templateKey: null,
                mailClass: AdministrationWelcomeMail::class,
                mailPayload: ['administration_id' => $administration->id, 'user_id' => $panelUser->id],
                context: ['administration_id' => $administration->id],
            );
        } catch (\Throwable $e) {
            \Log::warning('Fallo enviando acceso panel administración: '.$e->getMessage());

            return back()->with('error', 'No se pudo enviar el correo. Inténtelo más tarde o revise la configuración de correo.');
        }

        return back()->with('success', 'Se ha enviado el correo con el usuario de acceso y el enlace para establecer la contraseña.');
    }

    public function store_information(CreateAdmin $request)
    {
        // Saneamiento IBAN: quitar espacios, prefijo ES duplicado y dejar solo dígitos
        $accountValue = $this->sanitizeIbanAccount($request->account);

        $data = [
            'web' => isset($request->web) ? $request->validated()['web'] : '',
            'name' => $request->validated()['name'],
            'receiving' => $request->validated()['receiving'],
            'admin_number' => $request->validated()['admin_number'] ?? null,
            'society' => $request->validated()['society'],
            'nif_cif' => $request->validated()['nif_cif'],
            'province' => $request->validated()['province'],
            'city' => $request->validated()['city'],
            'postal_code' => $request->validated()['postal_code'],
            'address' => $request->validated()['address'],
            'email' => $request->validated()['email'],
            'phone' => $request->validated()['phone'],
            'account' => $accountValue ? ('ES' . $accountValue) : null,
        ];
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('images'), $filename);
            $data['image'] = $filename;
        }

        $request->session()->put('administration', $data);

        return redirect()->route('administrations.add-manager');
    }

    public function store(Request $request)
    {
        if ($request->filled('web')) {
            $administrationSession = $request->session()->get('administration', []);
            $administrationSession['web'] = $request->input('web');
            $request->session()->put('administration', $administrationSession);
        }

        $administrationData = $request->session()->get('administration');
        if (! $administrationData || empty($administrationData['email'])) {
            return redirect()->route('administrations.create')
                ->with('error', 'Sesión expirada. Vuelva a introducir los datos de la administración.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => ['nullable', 'string', 'max:20', new \App\Rules\SpanishDocument],
            'birthday' => ['nullable', 'date', new \App\Rules\MinimumAge(18)],
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $email = $administrationData['email'];
        if (User::where('email', $email)->exists()) {
            return back()->withErrors([
                'email' => 'Ya existe un usuario con el email de esta administración. Use otro email en el paso anterior o contacte con soporte.',
            ])->withInput();
        }

        $administrationData['status'] = null;

        $newAdministration = Administration::create($administrationData);

        $panelLoginBase = Administration::panelLoginUsernameFromParts(
            $newAdministration->receiving,
            $newAdministration->admin_number
        );
        $panelLoginUsername = Administration::ensureUniquePanelLoginUsername($panelLoginBase, null);

        if (strcasecmp((string) $request->input('email'), (string) $email) === 0) {
            return back()->withErrors([
                'email' => 'El email del gestor debe ser distinto al email de acceso del panel de la administración.',
            ])->withInput();
        }

        $managerUser = User::where('email', $request->input('email'))->first();
        if ($managerUser && $managerUser->isPanelAccount()) {
            return back()->withErrors([
                'email' => 'Ese email corresponde a una cuenta de acceso de panel. Use otro email para el gestor.',
            ])->withInput();
        }

        if (! $managerUser) {
            $managerUser = User::create([
                'name' => $request->input('name'),
                'last_name' => $request->input('last_name'),
                'last_name2' => $request->input('last_name2'),
                'email' => $request->input('email'),
                'password' => bcrypt(12345678),
                'role' => User::ROLE_ADMINISTRATION,
                'status' => true,
                'phone' => $request->input('phone') ?: null,
                'nif_cif' => $request->input('nif_cif') ?: null,
                'birthday' => $request->input('birthday') ?: null,
            ]);
        }

        $panelUser = User::create([
            'name' => Administration::panelDisplayNameFromParts($administrationData['name'] ?? '', $administrationData['society'] ?? ''),
            'email' => $email,
            'password' => Str::password(32),
            'role' => User::ROLE_ADMINISTRATION,
            'panel_account_type' => 'administration',
            'panel_account_id' => $newAdministration->id,
            'panel_login_username' => $panelLoginUsername,
            'status' => true,
            'phone' => $administrationData['phone'] ?? null,
            'nif_cif' => $administrationData['nif_cif'] ?? null,
        ]);

        Manager::firstOrCreate([
            'user_id' => $panelUser->id,
            'administration_id' => $newAdministration->id,
            'entity_id' => null,
        ], [
            'is_primary' => false,
            'permission_sellers' => true,
            'permission_design' => true,
            'permission_statistics' => true,
            'permission_payments' => true,
            'status' => 1,
        ]);

        Manager::firstOrCreate([
            'user_id' => $managerUser->id,
            'administration_id' => $newAdministration->id,
            'entity_id' => null,
        ], [
            'is_primary' => true,
            'permission_sellers' => true,
            'permission_design' => true,
            'permission_statistics' => true,
            'permission_payments' => true,
            'status' => 1,
        ]);

        $request->session()->forget(['administration', 'manager']);

        return redirect('administrations')->with(
            'success',
            'Administración creada correctamente. Envíe el correo de acceso al panel desde la ficha de la administración cuando corresponda.'
        );
    }

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
        $newStatus = match ($currentStatus) {
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
            'exclude_id' => 'nullable|integer',
        ]);

        $query = Administration::where('email', $request->email);

        if ($request->exclude_id) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este email ya está en uso por otra administración' : null,
        ]);
    }
}
