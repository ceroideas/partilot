<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Entity;
use App\Models\ParticipationCollection;
use App\Models\SepaPaymentOrder;
use App\Models\SepaPaymentBeneficiary;
use App\Models\Administration;

class ConfigurationController extends Controller
{
    /**
     * Mostrar la vista principal de configuración
     */
    public function index(Request $request)
    {
        $section = $request->get('section', 'datos-partilot');
        $step = (int) $request->get('step', 1);
        $entityId = $request->get('entity_id');

        $entities = collect();
        $entity = null;
        $collections = collect();
        $sepaOrders = collect();
        $provincias = collect();
        $localidades = collect();

        if ($section === 'ordenes-pago-entidades') {
            if ($step === 1 || !$entityId) {
                $user = $request->user();
                $provincias = Entity::forUser($user)->whereNotNull('province')->where('province', '!=', '')->distinct()->pluck('province')->sort()->values();
                $localidades = Entity::forUser($user)->whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city')->sort()->values();
                $query = Entity::with('administration')->forUser($request->user());
                if ($request->filled('provincia')) {
                    $query->where('province', $request->provincia);
                }
                if ($request->filled('localidad')) {
                    $query->where('city', $request->localidad);
                }
                if ($request->filled('busqueda')) {
                    $q = $request->busqueda;
                    $query->where(function ($qry) use ($q) {
                        $qry->where('name', 'like', '%' . $q . '%')
                            ->orWhere('province', 'like', '%' . $q . '%')
                            ->orWhere('city', 'like', '%' . $q . '%');
                    });
                }
                $entities = $query->orderBy('name')->get();
            }

            if ($entityId && in_array($step, [2, 3], true)) {
                $entity = Entity::with('administration')->forUser($request->user())->find($entityId);
                if (!$entity) {
                    return redirect()->route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 1]);
                }
            }

            if ($entity && $step === 2) {
                $sepaOrders = SepaPaymentOrder::where('administration_id', $entity->administration_id)
                    ->with('beneficiaries')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            if ($entity && $step === 3) {
                $collections = ParticipationCollection::whereHas('items.participation', function ($q) use ($entityId) {
                    $q->where('entity_id', $entityId);
                });
                if (Schema::hasColumn('participation_collections', 'sepa_payment_order_id')) {
                    $collections = $collections->pending();
                }
                $collections = $collections->with(['user', 'items'])->orderBy('created_at', 'desc')->get();
            }
        }

        return view('configuration.index', compact(
            'section', 'step', 'entityId', 'entities', 'entity', 'collections', 'sepaOrders', 'provincias', 'localidades'
        ));
    }

    /**
     * Eliminar una participation_collection (orden de transferencia)
     */
    public function destroyCollection(Request $request, ParticipationCollection $participationCollection)
    {
        $participationCollection->delete();
        $entityId = $request->query('entity_id') ?: $request->input('entity_id');
        $redirect = $entityId
            ? redirect()->route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 3, 'entity_id' => $entityId])
            : redirect()->route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 1]);
        return $redirect->with('success', 'Orden de transferencia eliminada.');
    }

    /**
     * Crear orden SEPA desde las participation_collections de la entidad y ofrecer descarga XML
     */
    public function crearSepa(Request $request)
    {
        $request->validate(['entity_id' => 'required|exists:entities,id']);
        $entity = Entity::with('administration')->forUser($request->user())->findOrFail($request->entity_id);
        $collections = ParticipationCollection::whereHas('items.participation', function ($q) use ($entity) {
            $q->where('entity_id', $entity->id);
        });
        if (Schema::hasColumn('participation_collections', 'sepa_payment_order_id')) {
            $collections = $collections->pending();
        }
        $collections = $collections->with('user')->get();

        if ($collections->isEmpty()) {
            return redirect()->route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 3, 'entity_id' => $entity->id])
                ->with('error', 'No hay peticiones de cobro para generar la orden SEPA.');
        }

        $administration = $entity->administration;
        $debtorName = $administration ? $administration->name : $entity->name;
        $debtorNif = $administration ? $administration->nif_cif : $entity->nif_cif ?? null;
        $debtorAddress = $administration ? $administration->address : $entity->address ?? null;
        $debtorIban = $administration && $administration->account ? $this->normalizeIban($administration->account) : null;
        if (!$debtorIban) {
            return redirect()->route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 3, 'entity_id' => $entity->id])
                ->with('error', 'La administración o entidad no tiene cuenta bancaria configurada.');
        }

        try {
            DB::beginTransaction();
            $totalAmount = $collections->sum('importe_total');
            $order = SepaPaymentOrder::create([
                'administration_id' => $entity->administration_id,
                'message_id' => SepaPaymentOrder::generateMessageId(),
                'creation_date' => now(),
                'execution_date' => now()->addDays(1),
                'number_of_transactions' => $collections->count(),
                'control_sum' => $totalAmount,
                'payment_info_id' => SepaPaymentOrder::generateMessageId(),
                'batch_booking' => true,
                'charge_bearer' => 'SLEV',
                'debtor_name' => $debtorName,
                'debtor_nif_cif' => $debtorNif,
                'debtor_iban' => $debtorIban,
                'debtor_address' => $debtorAddress,
                'status' => 'draft',
                'notes' => 'Generado desde Ordenes Pago Entidades - ' . $entity->name,
            ]);

            foreach ($collections as $col) {
                $iban = $this->normalizeIban($col->iban);
                $creditorName = trim($col->nombre . ' ' . $col->apellidos);
                SepaPaymentBeneficiary::create([
                    'sepa_payment_order_id' => $order->id,
                    'end_to_end_id' => SepaPaymentBeneficiary::generateEndToEndId(),
                    'amount' => $col->importe_total,
                    'currency' => 'EUR',
                    'creditor_name' => $creditorName ?: 'Beneficiario',
                    'creditor_nif_cif' => $col->nif ?? null,
                    'creditor_iban' => $iban,
                    'purpose_code' => 'CASH',
                    'remittance_info' => 'Cobro participaciones',
                ]);
                if (Schema::hasColumn('participation_collections', 'sepa_payment_order_id')) {
                    $col->update(['sepa_payment_order_id' => $order->id]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 3, 'entity_id' => $entity->id])
                ->with('error', 'Error al crear la orden SEPA: ' . $e->getMessage());
        }

        return redirect()->route('sepa-payments.generate-xml', $order->id)
            ->with('success', 'Orden SEPA creada. Descargando XML.');
    }

    private function normalizeIban(string $iban): string
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));
        if (!str_starts_with($iban, 'ES')) {
            $iban = 'ES' . $iban;
        }
        return $iban;
    }

    /**
     * Formulario para crear nueva orden SEPA desde Ordenes Pago Entidades.
     * Los beneficiarios salen de participation_collections pendientes (no vinculadas a ninguna orden SEPA) de la entidad elegida.
     */
    public function nuevaOrdenSepa(Request $request)
    {
        $request->validate(['entity_id' => 'required|exists:entities,id']);
        $entity = Entity::with('administration')->forUser($request->user())->findOrFail($request->entity_id);

        $query = ParticipationCollection::whereHas('items.participation', function ($q) use ($entity) {
            $q->where('entity_id', $entity->id);
        });
        if (Schema::hasColumn('participation_collections', 'sepa_payment_order_id')) {
            $query->pending();
        }
        $collections = $query->with('user')->orderBy('created_at', 'desc')->get();

        return $collections;

        $administrations = Administration::all();
        $debtorName = $entity->administration->name ?? $entity->name;
        $debtorNif = $entity->administration->nif_cif ?? $entity->nif_cif ?? '';
        $debtorAddress = $entity->administration->address ?? $entity->address ?? '';
        $debtorIban = '';
        if ($entity->administration && $entity->administration->account) {
            $debtorIban = preg_replace('/\s+/', '', $entity->administration->account);
            $debtorIban = str_starts_with(strtoupper($debtorIban), 'ES') ? substr($debtorIban, 2) : $debtorIban;
        }

        return view('configuration.ordenes-pago-entidades-nueva-orden', compact(
            'entity', 'collections', 'administrations', 'debtorName', 'debtorNif', 'debtorAddress', 'debtorIban'
        ));
    }

    /**
     * Guardar orden SEPA desde el formulario de Ordenes Pago Entidades (con opción de vincular participation_collections).
     */
    public function storeOrdenSepa(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'administration_id' => 'nullable|exists:administrations,id',
            'execution_date' => 'required|date|after_or_equal:today',
            'debtor_name' => 'required|string|max:255',
            'debtor_nif_cif' => ['nullable', 'string', 'max:50', new \App\Rules\SpanishDocument],
            'debtor_iban' => ['required', 'string', 'max:22', 'regex:/^[0-9]{22}$/'],
            'debtor_address' => 'nullable|string|max:500',
            'batch_booking' => 'nullable|boolean',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.creditor_name' => 'required|string|max:255',
            'beneficiaries.*.creditor_nif_cif' => ['nullable', 'string', 'max:50', new \App\Rules\SpanishDocument],
            'beneficiaries.*.creditor_iban' => ['required', 'string', 'max:22', 'regex:/^[0-9]{22}$/'],
            'beneficiaries.*.amount' => 'required|numeric|min:0.01',
            'beneficiaries.*.currency' => 'required|string|size:3',
            'beneficiaries.*.purpose_code' => 'nullable|string|max:10',
            'beneficiaries.*.remittance_info' => 'nullable|string|max:500',
            'beneficiaries.*.collection_id' => 'nullable|integer|exists:participation_collections,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $entity = Entity::with('administration')->forUser($request->user())->findOrFail($validated['entity_id']);

        $debtorIban = $validated['debtor_iban'];
        if (!str_starts_with(strtoupper($debtorIban), 'ES')) {
            $debtorIban = 'ES' . $debtorIban;
        }
        $validator = \Validator::make(['iban' => $debtorIban], ['iban' => [new \App\Rules\SpanishIban]]);
        if ($validator->fails()) {
            return back()->withErrors(['debtor_iban' => 'El IBAN del deudor no es válido.'])->withInput();
        }
        foreach ($validated['beneficiaries'] as $index => $beneficiary) {
            $creditorIban = 'ES' . $beneficiary['creditor_iban'];
            $v = \Validator::make(['iban' => $creditorIban], ['iban' => [new \App\Rules\SpanishIban]]);
            if ($v->fails()) {
                return back()->withErrors(["beneficiaries.{$index}.creditor_iban" => 'El IBAN del beneficiario no es válido.'])->withInput();
            }
        }

        try {
            DB::beginTransaction();
            $totalAmount = collect($validated['beneficiaries'])->sum('amount');
            $order = SepaPaymentOrder::create([
                'administration_id' => $validated['administration_id'] ?? $entity->administration_id,
                'message_id' => SepaPaymentOrder::generateMessageId(),
                'creation_date' => now(),
                'execution_date' => $validated['execution_date'],
                'number_of_transactions' => count($validated['beneficiaries']),
                'control_sum' => $totalAmount,
                'payment_info_id' => SepaPaymentOrder::generateMessageId(),
                'batch_booking' => (bool) ($validated['batch_booking'] ?? false),
                'charge_bearer' => 'SLEV',
                'debtor_name' => $validated['debtor_name'],
                'debtor_nif_cif' => $validated['debtor_nif_cif'] ?? null,
                'debtor_iban' => $debtorIban,
                'debtor_address' => $validated['debtor_address'] ?? null,
                'status' => 'draft',
                'notes' => ($validated['notes'] ?? null) ?: 'Orden desde Ordenes Pago Entidades - ' . $entity->name,
            ]);

            foreach ($validated['beneficiaries'] as $beneficiary) {
                $creditorIban = $beneficiary['creditor_iban'];
                if (!str_starts_with(strtoupper($creditorIban), 'ES')) {
                    $creditorIban = 'ES' . $creditorIban;
                }
                SepaPaymentBeneficiary::create([
                    'sepa_payment_order_id' => $order->id,
                    'end_to_end_id' => SepaPaymentBeneficiary::generateEndToEndId(),
                    'amount' => $beneficiary['amount'],
                    'currency' => $beneficiary['currency'] ?? 'EUR',
                    'creditor_name' => $beneficiary['creditor_name'],
                    'creditor_nif_cif' => $beneficiary['creditor_nif_cif'] ?? null,
                    'creditor_iban' => $creditorIban,
                    'purpose_code' => $beneficiary['purpose_code'] ?? 'CASH',
                    'remittance_info' => $beneficiary['remittance_info'] ?? null,
                ]);
                if (!empty($beneficiary['collection_id']) && Schema::hasColumn('participation_collections', 'sepa_payment_order_id')) {
                    $collection = ParticipationCollection::where('id', $beneficiary['collection_id'])
                        ->whereNull('sepa_payment_order_id')
                        ->first();
                    if ($collection) {
                        $collection->update(['sepa_payment_order_id' => $order->id]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear la orden: ' . $e->getMessage()]);
        }

        $redirectTo = $request->input('redirect_to', 'step3');
        if ($redirectTo === 'show') {
            return redirect()->route('sepa-payments.show', $order->id)->with('success', 'Orden de pago creada.');
        }
        return redirect()->route('configuration.index', [
            'section' => 'ordenes-pago-entidades',
            'step' => 3,
            'entity_id' => $entity->id,
        ])->with('success', 'Orden de pago creada. Puede verla en el paso 2 o generar el XML desde Órdenes de Pago SEPA.');
    }
}
