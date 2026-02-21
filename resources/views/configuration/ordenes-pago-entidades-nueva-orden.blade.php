@extends('layouts.layout')

@section('title', 'Nueva Orden SEPA - Ordenes Pago Entidades')

@section('content')
@php
    if (old('beneficiaries') && is_array(old('beneficiaries'))) {
        $initialBeneficiaries = collect(old('beneficiaries'))->map(function ($b) {
            $b = is_array($b) ? $b : [];
            $iban = preg_replace('/\s+/', '', $b['creditor_iban'] ?? '');
            $iban = str_starts_with(strtoupper($iban), 'ES') ? substr($iban, 2) : $iban;
            return [
                'collection_id' => $b['collection_id'] ?? null,
                'creditor_name' => $b['creditor_name'] ?? '',
                'creditor_nif_cif' => $b['creditor_nif_cif'] ?? '',
                'creditor_iban' => $iban,
                'amount' => (string) ($b['amount'] ?? ''),
                'currency' => $b['currency'] ?? 'EUR',
                'purpose_code' => $b['purpose_code'] ?? 'CASH',
                'remittance_info' => $b['remittance_info'] ?? '',
            ];
        })->values()->all();
    } else {
        $initialBeneficiaries = $collections->map(function ($c) {
            $iban = preg_replace('/\s+/', '', $c->iban ?? '');
            $iban = str_starts_with(strtoupper($iban), 'ES') ? substr($iban, 2) : $iban;
            return [
                'collection_id' => $c->id,
                'creditor_name' => trim(($c->nombre ?? '') . ' ' . ($c->apellidos ?? '')),
                'creditor_nif_cif' => $c->nif ?? '',
                'creditor_iban' => $iban,
                'amount' => (string) $c->importe_total,
                'currency' => 'EUR',
                'purpose_code' => 'CASH',
                'remittance_info' => '',
            ];
        })->values()->all();
    }
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 1]) }}">Ordenes Pago Entidades</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 3, 'entity_id' => $entity->id]) }}">{{ $entity->name }}</a></li>
                        <li class="breadcrumb-item active">Nueva Orden SEPA</li>
                    </ol>
                </div>
                <h4 class="page-title">Nueva Orden de Pago SEPA</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-3">
                        Los beneficiarios se han rellenado desde las peticiones de cobro pendientes de esta entidad. Puede editarlos o añadir más manualmente.
                    </p>

                    <form action="{{ route('ordenes-pago-entidades.store-sepa') }}" method="POST" id="sepa-payment-form">
                        @csrf
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <input type="hidden" name="redirect_to" value="step3">

                        <h5 class="mb-3">Datos de la Orden</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Administración</label>
                                <select name="administration_id" class="form-select" id="administration_id">
                                    <option value="">Seleccionar (opcional)</option>
                                    @foreach($administrations as $admin)
                                        <option value="{{ $admin->id }}" {{ (old('administration_id', $entity->administration_id) == $admin->id ? 'selected' : '') }}>{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Ejecución <span class="text-danger">*</span></label>
                                <input type="date" name="execution_date" class="form-control" value="{{ old('execution_date', date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <h5 class="mb-3 mt-4">Datos del Deudor (Pagador)</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="debtor_name" class="form-control" value="{{ old('debtor_name', $debtorName) }}" required maxlength="255">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIF/CIF</label>
                                <input type="text" name="debtor_nif_cif" id="debtor_nif_cif" class="form-control" value="{{ old('debtor_nif_cif', $debtorNif) }}" maxlength="50">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">IBAN <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="font-weight: bold;">ES</span>
                                    <input type="text" name="debtor_iban" class="form-control" value="{{ old('debtor_iban', $debtorIban) }}" required maxlength="22" placeholder="22 dígitos">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="debtor_address" class="form-control" value="{{ old('debtor_address', $debtorAddress) }}" maxlength="500">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Agrupación en lote</label>
                                <div class="form-check">
                                    <input type="checkbox" name="batch_booking" value="1" class="form-check-input" {{ old('batch_booking', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Agrupar pagos en lote</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2" maxlength="1000">{{ old('notes', 'Orden desde Ordenes Pago Entidades - ' . $entity->name) }}</textarea>
                            </div>
                        </div>

                        <h5 class="mb-3 mt-4">Beneficiarios <span class="text-danger">*</span></h5>
                        <div id="beneficiaries-container"></div>
                        <button type="button" class="btn btn-success mb-3" id="add-beneficiary">
                            <i class="ri-add-line"></i> Añadir Beneficiario (manual)
                        </button>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ route('configuration.index', ['section' => 'ordenes-pago-entidades', 'step' => 3, 'entity_id' => $entity->id]) }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Crear Orden de Pago</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function() {
    var beneficiaryIndex = 0;
    var initialBeneficiaries = @json($initialBeneficiaries);

    function getBeneficiaryTemplate(index, data, collectionId) {
        data = data || {};
        var collectionInput = collectionId !== undefined && collectionId !== null && collectionId !== ''
            ? '<input type="hidden" name="beneficiaries[' + index + '][collection_id]" value="' + collectionId + '">'
            : '';
        return ''
            + '<div class="beneficiary-item card mb-3" data-index="' + index + '">'
            + '<div class="card-body">'
            + '<div class="d-flex justify-content-between align-items-center mb-3">'
            + '<h6 class="mb-0">Beneficiario #' + (index + 1) + '</h6>'
            + '<button type="button" class="btn btn-sm btn-danger remove-beneficiary" data-index="' + index + '"><i class="ri-delete-bin-line"></i> Eliminar</button>'
            + '</div>'
            + collectionInput
            + '<div class="row">'
            + '<div class="col-md-6 mb-3"><label class="form-label">Nombre <span class="text-danger">*</span></label>'
            + '<input type="text" name="beneficiaries[' + index + '][creditor_name]" class="form-control" value="' + (data.creditor_name || '').replace(/"/g, '&quot;') + '" required maxlength="255"></div>'
            + '<div class="col-md-6 mb-3"><label class="form-label">NIF/CIF</label>'
            + '<input type="text" name="beneficiaries[' + index + '][creditor_nif_cif]" id="beneficiary-' + index + '-nif-cif" class="form-control creditor-nif-cif" value="' + (data.creditor_nif_cif || '').replace(/"/g, '&quot;') + '" maxlength="50"></div>'
            + '</div>'
            + '<div class="row">'
            + '<div class="col-md-6 mb-3"><label class="form-label">IBAN <span class="text-danger">*</span></label>'
            + '<div class="input-group"><span class="input-group-text" style="font-weight: bold;">ES</span>'
            + '<input type="text" name="beneficiaries[' + index + '][creditor_iban]" class="form-control" value="' + (data.creditor_iban || '').replace(/"/g, '&quot;') + '" required maxlength="22"></div></div>'
            + '<div class="col-md-3 mb-3"><label class="form-label">Importe <span class="text-danger">*</span></label>'
            + '<input type="number" name="beneficiaries[' + index + '][amount]" class="form-control beneficiary-amount" step="0.01" min="0.01" value="' + (data.amount || '') + '" required></div>'
            + '<div class="col-md-3 mb-3"><label class="form-label">Moneda <span class="text-danger">*</span></label>'
            + '<select name="beneficiaries[' + index + '][currency]" class="form-select" required>'
            + '<option value="EUR"' + (data.currency === 'EUR' ? ' selected' : '') + '>EUR</option>'
            + '<option value="USD"' + (data.currency === 'USD' ? ' selected' : '') + '>USD</option>'
            + '<option value="GBP"' + (data.currency === 'GBP' ? ' selected' : '') + '>GBP</option>'
            + '</select></div>'
            + '</div>'
            + '<div class="row">'
            + '<div class="col-md-6 mb-3"><label class="form-label">Código de Propósito</label>'
            + '<input type="text" name="beneficiaries[' + index + '][purpose_code]" class="form-control" value="' + (data.purpose_code || 'CASH').replace(/"/g, '&quot;') + '" maxlength="10"></div>'
            + '<div class="col-md-6 mb-3"><label class="form-label">Información de Remesa</label>'
            + '<input type="text" name="beneficiaries[' + index + '][remittance_info]" class="form-control" value="' + (data.remittance_info || '').replace(/"/g, '&quot;') + '" maxlength="500"></div>'
            + '</div>'
            + '</div></div>';
    }

    function addBeneficiary(data, collectionId) {
        var container = document.getElementById('beneficiaries-container');
        var html = getBeneficiaryTemplate(beneficiaryIndex, data || {}, collectionId);
        container.insertAdjacentHTML('beforeend', html);
        if (typeof initSpanishDocumentValidation === 'function') {
            initSpanishDocumentValidation('beneficiary-' + beneficiaryIndex + '-nif-cif', { showMessage: true });
        }
        beneficiaryIndex++;
    }

    document.getElementById('add-beneficiary').addEventListener('click', function() {
        addBeneficiary(null, null);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-beneficiary')) {
            var btn = e.target.closest('.remove-beneficiary');
            var item = document.querySelector('.beneficiary-item[data-index="' + btn.getAttribute('data-index') + '"]');
            if (item) {
                item.remove();
                var items = document.querySelectorAll('.beneficiary-item');
                items.forEach(function(el, i) {
                    var h = el.querySelector('h6');
                    if (h) h.textContent = 'Beneficiario #' + (i + 1);
                });
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var container = document.getElementById('beneficiaries-container');
        if (initialBeneficiaries && initialBeneficiaries.length) {
            initialBeneficiaries.forEach(function(b) {
                addBeneficiary(b, b.collection_id);
            });
        } else {
            addBeneficiary(null, null);
        }
        if (typeof initSpanishDocumentValidation === 'function') {
            initSpanishDocumentValidation('debtor_nif_cif', { showMessage: true });
        }
    });

    document.getElementById('sepa-payment-form').addEventListener('submit', function(e) {
        var beneficiaries = document.querySelectorAll('.beneficiary-item');
        if (beneficiaries.length === 0) {
            e.preventDefault();
            alert('Debe añadir al menos un beneficiario.');
            return false;
        }
        if (typeof validateSpanishDocument === 'function') {
            var debtorNif = document.getElementById('debtor_nif_cif');
            if (debtorNif && debtorNif.value.trim()) {
                var r = validateSpanishDocument(debtorNif.value.trim());
                if (!r.valid) {
                    e.preventDefault();
                    debtorNif.focus();
                    alert('NIF/CIF del deudor: ' + r.message);
                    return false;
                }
            }
            document.querySelectorAll('.creditor-nif-cif').forEach(function(input) {
                if (input.value.trim()) {
                    var r = validateSpanishDocument(input.value.trim());
                    if (!r.valid) {
                        e.preventDefault();
                        input.focus();
                        alert('NIF/CIF de beneficiario: ' + r.message);
                        return false;
                    }
                }
            });
        }
    });
})();
</script>
@endsection
