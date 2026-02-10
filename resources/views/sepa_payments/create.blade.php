@extends('layouts.layout')

@section('title','Nueva Orden de Pago SEPA')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('sepa-payments.index')}}">Órdenes de Pago SEPA</a></li>
                        <li class="breadcrumb-item active">Nueva Orden</li>
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

                    @if(!old('debtor_name'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="ri-information-line me-2"></i>
                        <strong>Datos de ejemplo:</strong> Este formulario se ha pre-rellenado con datos de ejemplo para facilitar las pruebas. Puede modificar cualquier campo según sus necesidades.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{route('sepa-payments.store')}}" method="POST" id="sepa-payment-form">
                        @csrf

                        <!-- Datos de la Orden -->
                        <h5 class="mb-3">Datos de la Orden</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Administración <span class="text-danger">*</span></label>
                                <select name="administration_id" class="form-select" id="administration_id">
                                    <option value="">Seleccionar administración (opcional)</option>
                                    @foreach($administrations as $admin)
                                        <option value="{{$admin->id}}" {{old('administration_id') == $admin->id ? 'selected' : ''}}>{{$admin->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Ejecución <span class="text-danger">*</span></label>
                                <input type="date" name="execution_date" class="form-control" value="{{old('execution_date', date('Y-m-d'))}}" required min="{{date('Y-m-d')}}">
                            </div>
                        </div>

                        <!-- Datos del Deudor (Pagador) -->
                        <h5 class="mb-3 mt-4">Datos del Deudor (Pagador)</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="debtor_name" class="form-control" value="{{old('debtor_name', 'PARTILOT S.L.')}}" required maxlength="255">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIF/CIF</label>
                                <input type="text" name="debtor_nif_cif" id="debtor_nif_cif" class="form-control" value="{{old('debtor_nif_cif', 'B12345674')}}" maxlength="50">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">IBAN <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="font-weight: bold;">ES</span>
                                    <input type="text" name="debtor_iban" class="form-control" value="{{old('debtor_iban', '9121000418450200051332')}}" required maxlength="22" placeholder="1234567890123456789012">
                                </div>
                                <small class="text-muted">Ingrese los 22 dígitos (sin espacios). El prefijo ES se añadirá automáticamente.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="debtor_address" class="form-control" value="{{old('debtor_address', 'Calle Ejemplo 123, 28001 Madrid, España')}}" maxlength="500">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Agrupación en lote</label>
                                <div class="form-check">
                                    <input type="checkbox" name="batch_booking" value="1" class="form-check-input" {{old('batch_booking', true) ? 'checked' : ''}}>
                                    <label class="form-check-label">Agrupar pagos en lote</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2" maxlength="1000">{{old('notes', 'Orden de pago de prueba - Datos de ejemplo')}}</textarea>
                            </div>
                        </div>

                        <!-- Beneficiarios -->
                        <h5 class="mb-3 mt-4">Beneficiarios <span class="text-danger">*</span></h5>
                        <div id="beneficiaries-container">
                            <!-- Los beneficiarios se añadirán dinámicamente aquí -->
                        </div>
                        <button type="button" class="btn btn-success mb-3" id="add-beneficiary">
                            <i class="ri-add-line"></i> Añadir Beneficiario
                        </button>

                        <!-- Botones de acción -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{route('sepa-payments.index')}}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Crear Orden de Pago</button>
                            </div>
                        </div>

                    </form>
                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>
let beneficiaryIndex = 0;

// Datos de ejemplo para beneficiarios
// Nota: Se usa el mismo IBAN válido para todos (ES9121000418450200051332) para facilitar pruebas
// En producción, cada beneficiario debe tener su propio IBAN válido
const exampleBeneficiaries = [
    {
        creditor_name: 'Juan Pérez García',
        creditor_nif_cif: '12345678Z',
        creditor_iban: '9121000418450200051332',
        amount: '1250.50',
        currency: 'EUR',
        purpose_code: 'CASH',
        remittance_info: 'Pago de comisiones - Enero 2026'
    },
    {
        creditor_name: 'María López Martínez',
        creditor_nif_cif: '87654321X',
        creditor_iban: '9121000418450200051332',
        amount: '850.75',
        currency: 'EUR',
        purpose_code: 'CASH',
        remittance_info: 'Liquidación de participaciones'
    },
    {
        creditor_name: 'Empresa Ejemplo S.L.',
        creditor_nif_cif: '',
        creditor_iban: '9121000418450200051332',
        amount: '2500.00',
        currency: 'EUR',
        purpose_code: 'CASH',
        remittance_info: 'Factura 2026-001'
    }
];

// Template para un beneficiario
function getBeneficiaryTemplate(index, exampleData = null) {
    const data = exampleData || {};
    return `
        <div class="beneficiary-item card mb-3" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Beneficiario #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-beneficiary" data-index="${index}">
                        <i class="ri-delete-bin-line"></i> Eliminar
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del Beneficiario <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiaries[${index}][creditor_name]" class="form-control" value="${data.creditor_name || ''}" required maxlength="255">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIF/CIF del Beneficiario</label>
                        <input type="text" name="beneficiaries[${index}][creditor_nif_cif]" id="beneficiary-${index}-nif-cif" class="form-control creditor-nif-cif" value="${data.creditor_nif_cif || ''}" maxlength="50">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">IBAN del Beneficiario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-weight: bold;">ES</span>
                            <input type="text" name="beneficiaries[${index}][creditor_iban]" class="form-control" value="${data.creditor_iban || ''}" required maxlength="22" placeholder="1234567890123456789012">
                        </div>
                        <small class="text-muted">Ingrese los 22 dígitos (sin espacios). El prefijo ES se añadirá automáticamente.</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Importe <span class="text-danger">*</span></label>
                        <input type="number" name="beneficiaries[${index}][amount]" class="form-control beneficiary-amount" step="0.01" min="0.01" value="${data.amount || ''}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Moneda <span class="text-danger">*</span></label>
                        <select name="beneficiaries[${index}][currency]" class="form-select" required>
                            <option value="EUR" ${data.currency === 'EUR' ? 'selected' : ''}>EUR</option>
                            <option value="USD" ${data.currency === 'USD' ? 'selected' : ''}>USD</option>
                            <option value="GBP" ${data.currency === 'GBP' ? 'selected' : ''}>GBP</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Código de Propósito</label>
                        <input type="text" name="beneficiaries[${index}][purpose_code]" class="form-control" value="${data.purpose_code || 'CASH'}" maxlength="10" placeholder="CASH">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Información de Remesa</label>
                        <input type="text" name="beneficiaries[${index}][remittance_info]" class="form-control" value="${data.remittance_info || ''}" maxlength="500" placeholder="Ej: https://web.elbuholotero.es">
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Añadir beneficiario
document.getElementById('add-beneficiary').addEventListener('click', function() {
    const container = document.getElementById('beneficiaries-container');
    const template = getBeneficiaryTemplate(beneficiaryIndex);
    container.insertAdjacentHTML('beforeend', template);
    if (typeof initSpanishDocumentValidation === 'function') {
        initSpanishDocumentValidation('beneficiary-' + beneficiaryIndex + '-nif-cif', { showMessage: true });
    }
    beneficiaryIndex++;
});

// Eliminar beneficiario
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-beneficiary')) {
        const button = e.target.closest('.remove-beneficiary');
        const index = button.getAttribute('data-index');
        const item = document.querySelector(`.beneficiary-item[data-index="${index}"]`);
        if (item) {
            item.remove();
            updateBeneficiaryNumbers();
        }
    }
});

// Actualizar números de beneficiarios
function updateBeneficiaryNumbers() {
    const items = document.querySelectorAll('.beneficiary-item');
    items.forEach((item, index) => {
        const header = item.querySelector('h6');
        if (header) {
            header.textContent = `Beneficiario #${index + 1}`;
        }
    });
}

// Añadir beneficiarios de ejemplo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('beneficiaries-container');
    
    // Si no hay datos antiguos (old input), añadir beneficiarios de ejemplo
    @if(!old('beneficiaries'))
        // Añadir 2 beneficiarios de ejemplo
        exampleBeneficiaries.slice(0, 2).forEach((example, idx) => {
            const template = getBeneficiaryTemplate(beneficiaryIndex, example);
            container.insertAdjacentHTML('beforeend', template);
            if (typeof initSpanishDocumentValidation === 'function') {
                initSpanishDocumentValidation('beneficiary-' + beneficiaryIndex + '-nif-cif', { showMessage: true });
            }
            beneficiaryIndex++;
        });
    @else
        // Si hay datos antiguos, añadir un beneficiario vacío
        document.getElementById('add-beneficiary').click();
    @endif

    // Validación NIF/CIF del deudor
    if (typeof initSpanishDocumentValidation === 'function') {
        initSpanishDocumentValidation('debtor_nif_cif', { showMessage: true });
    }
});

// Validación del formulario
document.getElementById('sepa-payment-form').addEventListener('submit', function(e) {
    const beneficiaries = document.querySelectorAll('.beneficiary-item');
    if (beneficiaries.length === 0) {
        e.preventDefault();
        alert('Debe añadir al menos un beneficiario.');
        return false;
    }
    
    // Validar NIF/CIF si la función está disponible (deudor y beneficiarios)
    if (typeof validateSpanishDocument === 'function') {
        const debtorNif = document.getElementById('debtor_nif_cif');
        if (debtorNif && debtorNif.value.trim()) {
            const r = validateSpanishDocument(debtorNif.value.trim());
            if (!r.valid) {
                e.preventDefault();
                debtorNif.focus();
                alert('NIF/CIF del deudor: ' + r.message);
                return false;
            }
        }
        const creditorInputs = document.querySelectorAll('.creditor-nif-cif');
        for (let i = 0; i < creditorInputs.length; i++) {
            const input = creditorInputs[i];
            if (input.value.trim()) {
                const r = validateSpanishDocument(input.value.trim());
                if (!r.valid) {
                    e.preventDefault();
                    input.focus();
                    alert('NIF/CIF de beneficiario: ' + r.message);
                    return false;
                }
            }
        }
    }

    // Validar que todos los beneficiarios tengan datos requeridos
    let valid = true;
    beneficiaries.forEach(function(item) {
        const name = item.querySelector('[name*="[creditor_name]"]');
        const iban = item.querySelector('[name*="[creditor_iban]"]');
        const amount = item.querySelector('[name*="[amount]"]');
        
        if (!name.value || !iban.value || !amount.value || parseFloat(amount.value) <= 0) {
            valid = false;
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert('Por favor, complete todos los campos requeridos de los beneficiarios.');
        return false;
    }
});
</script>

@endsection


