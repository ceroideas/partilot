<?php

namespace App\Http\Controllers;

use App\Models\SepaPaymentOrder;
use App\Models\SepaPaymentBeneficiary;
use App\Models\Administration;
use App\Services\SepaXmlGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SepaPaymentOrderController extends Controller
{
    protected $xmlGenerator;

    public function __construct(SepaXmlGeneratorService $xmlGenerator)
    {
        $this->xmlGenerator = $xmlGenerator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = SepaPaymentOrder::with(['administration', 'beneficiaries'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sepa_payments.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $administrations = Administration::all();
        return view('sepa_payments.create', compact('administrations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validar IBANs completos después de agregar ES
        $debtorIban = 'ES' . $validated['debtor_iban'];
        $validator = \Validator::make(['iban' => $debtorIban], [
            'iban' => [new \App\Rules\SpanishIban]
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors(['debtor_iban' => 'El IBAN del deudor no es válido.'])->withInput();
        }

        foreach ($validated['beneficiaries'] as $index => $beneficiary) {
            $creditorIban = 'ES' . $beneficiary['creditor_iban'];
            $validator = \Validator::make(['iban' => $creditorIban], [
                'iban' => [new \App\Rules\SpanishIban]
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors(["beneficiaries.{$index}.creditor_iban" => 'El IBAN del beneficiario no es válido.'])->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Calcular totales
            $totalAmount = collect($validated['beneficiaries'])->sum('amount');
            $numberOfTransactions = count($validated['beneficiaries']);

            // Normalizar IBANs (agregar ES si no lo tienen)
            $debtorIban = $validated['debtor_iban'];
            if (!str_starts_with(strtoupper($debtorIban), 'ES')) {
                $debtorIban = 'ES' . $debtorIban;
            }

            // Crear orden de pago
            $order = SepaPaymentOrder::create([
                'administration_id' => $validated['administration_id'] ?? null,
                'message_id' => SepaPaymentOrder::generateMessageId(),
                'creation_date' => now(),
                'execution_date' => $validated['execution_date'],
                'number_of_transactions' => $numberOfTransactions,
                'control_sum' => $totalAmount,
                'payment_info_id' => SepaPaymentOrder::generateMessageId(),
                'batch_booking' => $validated['batch_booking'] ?? false,
                'charge_bearer' => 'SLEV',
                'debtor_name' => $validated['debtor_name'],
                'debtor_nif_cif' => $validated['debtor_nif_cif'] ?? null,
                'debtor_iban' => $debtorIban,
                'debtor_address' => $validated['debtor_address'] ?? null,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Crear beneficiarios
            foreach ($validated['beneficiaries'] as $index => $beneficiary) {
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
            }

            DB::commit();

            return redirect()->route('sepa-payments.index')
                ->with('success', 'Orden de pago creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear la orden de pago: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SepaPaymentOrder $sepaPaymentOrder)
    {
        $sepaPaymentOrder->load(['administration', 'beneficiaries']);
        return view('sepa_payments.show', compact('sepaPaymentOrder'));
    }

    /**
     * Generar XML para una orden de pago
     */
    public function generateXml(SepaPaymentOrder $sepaPaymentOrder)
    {
        try {
            $sepaPaymentOrder->load('beneficiaries');
            
            if ($sepaPaymentOrder->beneficiaries->isEmpty()) {
                return back()->withErrors(['error' => 'La orden no tiene beneficiarios.']);
            }

            $filePath = $this->xmlGenerator->generateAndSave($sepaPaymentOrder);

            return response()->download($filePath, $sepaPaymentOrder->xml_filename, [
                'Content-Type' => 'application/xml',
            ])->deleteFileAfterSend(false);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al generar el XML: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar una orden de pago
     */
    public function destroy(SepaPaymentOrder $sepaPaymentOrder)
    {
        try {
            // Eliminar archivo XML si existe
            if ($sepaPaymentOrder->xml_filename) {
                $filePath = storage_path('app/sepa_payments/' . $sepaPaymentOrder->xml_filename);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $sepaPaymentOrder->delete();

            return redirect()->route('sepa-payments.index')
                ->with('success', 'Orden de pago eliminada correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la orden: ' . $e->getMessage()]);
        }
    }
}
