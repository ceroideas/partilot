<?php

namespace App\Http\Controllers;

use App\Support\ActiveEntityContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PanelEntitySwitchController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! ActiveEntityContext::usesActiveEntityScope($user)) {
            abort(403);
        }

        $validated = $request->validate([
            'entity_id' => ['required', 'integer', 'min:1'],
        ]);

        if (! ActiveEntityContext::setActiveEntity($request, $user, (int) $validated['entity_id'])) {
            abort(403, 'No tienes acceso a esa entidad.');
        }

        return redirect()->route('dashboard');
    }
}
