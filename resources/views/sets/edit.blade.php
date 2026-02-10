@extends('layouts.layout')

@section('title','Editar Set de Participaciones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('sets') }}">Sets</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Set de Participaciones</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Editar Set</h4>
                    <br>
                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <div class="form-card bs mb-3">
                                <div class="form-wizard-element active">
                                    <span>3</span>
                                    <img src="{{url('icons_/sets.svg')}}" alt="" width="26px">
                                    <label>Config. Set</label>
                                </div>
                            </div>
                            <a href="{{url('sets/view',$set->id)}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>
                        <div class="col-md-9">
                            <div class="form-card bs">
                                <div style="min-height: 658px;">
                                    {{-- Formulario para importar XML --}}
                                    <form action="{{ route('sets.importXml', $set->id) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="xml_file" class="form-label">Importar archivo XML de participaciones</label>
                                            <input type="file" name="xml_file" id="xml_file" class="form-control" accept=".xml" required>
                                        </div>
                                        <button type="submit" class="btn btn-dark">Importar XML</button>
                                    </form>
                                    <h4 class="mb-0 mt-1">Reserva en la que se generó el Set</h4>
                                    <small><i>Datos de la reserva asociada</i></small>
                                    <br>
                                    <div class="row show-content">
                                        <div class="col-3 offset-2">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Número del Sorteo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{$set->reserve && $set->reserve->lottery ? $set->reserve->lottery->name : 'Sin número'}}" placeholder="46/25" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Nombre del Sorteo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{$set->reserve && $set->reserve->lottery ? $set->reserve->lottery->description : 'Sin nombre'}}" placeholder="Nombre del Sorteo" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row show-content">
                                        <div class="col-3">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Fecha Sorteo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{$set->reserve && $set->reserve->lottery ? \Carbon\Carbon::parse($set->reserve->lottery->draw_date)->format('d-m-Y') : 'Sin fecha'}}" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Números</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{is_array($set->reserve->reservation_numbers ?? null) ? implode(' - ', $set->reserve->reservation_numbers) : ''}}" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Décimos TOTALES</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <input class="form-control" readonly type="number" value="{{$set->reserve->reservation_tickets ?? ''}}" style="border-radius: 30px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Importe TOTAL</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="number" step="0.01" value="{{$set->reserve->reservation_amount ?? ''}}" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ url('sets/update/' . $set->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <h4 class="mb-0 mt-1">Configuración del Set</h4>
                                        <small><i>Datos editables del set</i></small>
                                        <br>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Nombre del Set</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/19.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" name="set_name" type="text" value="{{$set->set_name}}" style="border-radius: 0 30px 30px 0;" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Importe Jugado (Número)</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" id="played_amount" name="played_amount" type="number" step="0.01" value="{{$set->played_amount}}" style="border-radius: 0 30px 30px 0;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Importe Donativo</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" id="donation_amount" name="donation_amount" type="number" step="0.01" value="{{$set->donation_amount}}" style="border-radius: 0 30px 30px 0;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Importe Total Participación</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" id="total_participation_amount" name="total_participation_amount" type="number" step="0.01" value="{{$set->total_participation_amount}}" style="border-radius: 0 30px 30px 0;" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Participaciones Totales</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/20.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" id="total_participations" name="total_participations" type="number" value="{{$set->total_participations}}" style="border-radius: 0 30px 30px 0;" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Importe TOTAL</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" id="total_amount" name="total_amount" type="number" step="0.01" value="{{$set->total_amount}}" style="border-radius: 0 30px 30px 0;" readonly max="{{ $availableAmount }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Fecha Límite</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                        </div>
                                                        <input class="form-control" name="deadline_date" type="date" value="{{$set->deadline_date ? \Carbon\Carbon::parse($set->deadline_date)->format('Y-m-d') : ''}}" style="border-radius: 0 30px 30px 0;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="mb-0 mt-1">Tipo Participaciones</h4>
                                        <small><i>Cantidad de participaciones físicas o digitales</i></small>
                                        <br>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Tipo de Participación</label>
                                                    
                                                    <div class="form-check mt-3">
                                                        <input class="form-check-input" type="radio" name="participation_type" id="participation_type_physical" value="physical" {{ $set->physical_participations > 0 ? 'checked' : '' }} disabled>
                                                        <label class="form-check-label" for="participation_type_physical">
                                                            <strong>Participaciones Físicas</strong> ({{ $set->physical_participations }})
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="radio" name="participation_type" id="participation_type_digital" value="digital" {{ $set->digital_participations > 0 ? 'checked' : '' }} disabled>
                                                        <label class="form-check-label" for="participation_type_digital">
                                                            <strong>Participaciones Digitales</strong> ({{ $set->digital_participations }})
                                                        </label>
                                                    </div>
                                                    
                                                    <input type="hidden" name="physical_participations" value="{{ $set->physical_participations }}">
                                                    <input type="hidden" name="digital_participations" value="{{ $set->digital_participations }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Guardar
                                                    <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

// Función para calcular el Importe Total Participación
function calculateTotalParticipationAmount() {
    const playedAmount = parseFloat($('#played_amount').val()) || 0;
    const donationAmount = parseFloat($('#donation_amount').val()) || 0;
    
    // Obtener la cantidad de números reservados
    const reservedNumbers = @json($set->reserve->reservation_numbers ?? []);
    const numbersCount = reservedNumbers.length;
    
    let totalParticipationAmount;
    
    if (numbersCount <= 1) {
        // Si hay 1 número o menos: Importe Jugado + Importe Donativo
        totalParticipationAmount = playedAmount + donationAmount;
    } else {
        // Si hay 2 o más números: (Importe Jugado × Cantidad de números) + Importe Donativo
        totalParticipationAmount = (playedAmount * numbersCount) + donationAmount;
    }
    
    $('#total_participation_amount').val(totalParticipationAmount.toFixed(2));
}

// Función para calcular el Importe Total
function calculateTotalAmount() {
    const totalParticipations = parseInt($('#total_participations').val()) || 0;
    const playedAmount = parseFloat($('#played_amount').val()) || 0;
    const totalAmount = totalParticipations * playedAmount;
    
    $('#total_amount').val(totalAmount.toFixed(2));
}

// Función para calcular participaciones digitales cuando cambian las físicas
function calculateDigitalParticipations() {
    const totalParticipations = parseInt($('#total_participations').val()) || 0;
    const physicalParticipations = parseInt($('#physical_participations').val()) || 0;
    
    if (physicalParticipations > totalParticipations) {
        $('#physical_participations').val(totalParticipations);
        $('#digital_participations').val(0);
    } else {
        const digitalParticipations = totalParticipations - physicalParticipations;
        $('#digital_participations').val(digitalParticipations);
    }
}

// Función para calcular participaciones físicas cuando cambian las digitales
function calculatePhysicalParticipations() {
    const totalParticipations = parseInt($('#total_participations').val()) || 0;
    const digitalParticipations = parseInt($('#digital_participations').val()) || 0;
    
    if (digitalParticipations > totalParticipations) {
        $('#digital_participations').val(totalParticipations);
        $('#physical_participations').val(0);
    } else {
        const physicalParticipations = totalParticipations - digitalParticipations;
        $('#physical_participations').val(physicalParticipations);
    }
}

// Event listeners para los cálculos automáticos
$(document).ready(function() {
    
    // Calcular Importe Total Participación cuando cambian Importe Jugado o Importe Donativo
    $('#played_amount, #donation_amount').on('input', function() {
        calculateTotalParticipationAmount();
    });
    
    // Calcular Importe Total cuando cambian Participaciones Totales o Importe Jugado
    $('#total_participations, #played_amount').on('input', function() {
        calculateTotalAmount();
        calculateDigitalParticipations();
        calculatePhysicalParticipations();
    });
    
    // Calcular participaciones digitales cuando cambian las físicas
    $('#physical_participations').on('input', function() {
        calculateDigitalParticipations();
    });
    
    // Calcular participaciones físicas cuando cambian las digitales
    $('#digital_participations').on('input', function() {
        calculatePhysicalParticipations();
    });
    
    // Validación adicional para Participaciones Totales
    $('#total_participations').on('input', function() {
        const totalParticipations = parseInt($(this).val()) || 0;
        const physicalParticipations = parseInt($('#physical_participations').val()) || 0;
        const digitalParticipations = parseInt($('#digital_participations').val()) || 0;
        
        // Si las participaciones físicas o digitales superan el total, ajustarlas
        if (physicalParticipations > totalParticipations) {
            $('#physical_participations').val(totalParticipations);
            $('#digital_participations').val(0);
        }
        if (digitalParticipations > totalParticipations) {
            $('#digital_participations').val(totalParticipations);
            $('#physical_participations').val(0);
        }
    });
    
    // Calcular valores iniciales
    calculateTotalParticipationAmount();
    calculateTotalAmount();
    
    // Fecha límite: máximo el día anterior al sorteo (23:59)
    const lotteryDate = @json($set->reserve->lottery->draw_date ?? null);
    if (lotteryDate) {
        const lotteryDateObj = new Date(lotteryDate);
        lotteryDateObj.setDate(lotteryDateObj.getDate() - 1);
        const maxDate = lotteryDateObj.toISOString().split('T')[0];
        $('input[name="deadline_date"]').attr('max', maxDate);
        
        $('input[name="deadline_date"]').on('change', function() {
            const selectedDate = new Date($(this).val());
            const maxDateObj = new Date(maxDate);
            if (selectedDate > maxDateObj) {
                alert('La fecha límite debe ser como máximo el día anterior al sorteo (23:59).');
                $(this).val('');
            }
        });
    }
    
});

// Validación de importe disponible antes de enviar
$('form').on('submit', function(e) {
    var maxAmount = parseFloat({{ $availableAmount }});
    var totalAmount = parseFloat($('#total_amount').val()) || 0;
    if (totalAmount > maxAmount) {
        alert('El importe total supera el disponible para esta reserva (máx: ' + maxAmount.toFixed(2) + ' €)');
        e.preventDefault();
        return false;
    }
});

</script>

@endsection