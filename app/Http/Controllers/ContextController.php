<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContextController extends Controller
{
    /**
     * Cambiar el rol/contexto activo del gestor (administración / entidad).
     */
    public function setRole(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $contextRole = $request->input('context_role');

        // Solo permitimos valores válidos o vacío (modo por defecto)
        if (!in_array($contextRole, ['administration', 'entity', null, ''], true)) {
            return back();
        }

        // Comprobamos qué roles puede asumir realmente este usuario según managers
        $canBeAdministration = $user->managers()
            ->whereNotNull('administration_id')
            ->exists();

        $canBeEntity = $user->managers()
            ->whereNotNull('entity_id')
            ->exists();

        if ($contextRole === 'administration' && !$canBeAdministration) {
            // No puede entrar como administración
            $contextRole = null;
        } elseif ($contextRole === 'entity' && !$canBeEntity) {
            // No puede entrar como entidad
            $contextRole = null;
        }

        if ($contextRole) {
            session(['context_role' => $contextRole]);
        } else {
            session()->forget('context_role');
        }

        return back();
    }
}

