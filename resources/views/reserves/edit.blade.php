@extends('layouts.layout')

@section('title','Editar Reserva')

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{url('reserves')}}">Reservas</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Reserva</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Editar datos de la Reserva</h4>
                    <br>
                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <div class="form-card bs mb-3">
                                <div class="form-wizard-element active">
                                    <span>3</span>
                                    <img src="{{url('icons_/reservas.svg')}}" alt="">
                                    <label>Datos Reserva</label>
                                </div>
                            </div>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="photo-preview-3 logo-round" @if($reserve->entity->image ?? null) style="background-image: url('{{ asset('uploads/' . $reserve->entity->image) }}');" @endif>
                                            @if(!($reserve->entity->image ?? null))
                                                <i class="ri-account-circle-fill"></i>
                                            @endif
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                    <div class="col-8 text-center mt-2">
                                        <h3 class="mt-2 mb-0">{{$reserve->entity->name ?? 'Entidad'}}</h3>
                                        <i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{$reserve->entity->province ?? 'Sin provincia'}}
                                    </div>
                                </div>
                            </div>
                            <a href="{{url('reserves/view/' . $reserve->id)}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                        </div>
                        <div class="col-md-9">
                            <div class="form-card bs" style="min-height: 0px;">
                                <form action="{{ url('reserves/update/' . $reserve->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <h4 class="mb-0 mt-1 d-flex align-items-center justify-content-between">
                                        <span>Datos del Sorteo</span>
                                    </h4>
                                    <small><i>Información del sorteo asociado</i></small>
                                    <br>
                                    <div class="row show-content">
                                        <div class="col-3 offset-2">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Número del Sorteo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{$reserve->lottery->name ?? ''}}" placeholder="Número" style="border-radius: 0 30px 30px 0;">
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
                                                    <input class="form-control" readonly type="text" value="{{$reserve->lottery->description ?? ''}}" placeholder="Nombre del Sorteo" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row show-content">
                                        <div class="col-4 offset-2">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Tipo de Sorteo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{$reserve->lottery->lotteryType->name ?? 'Sin tipo'}}" placeholder="Tipo de Sorteo" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Precio décimo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="number" value="{{$reserve->lottery->ticket_price ?? 0}}" step="0.01" placeholder="0.00€" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Fecha Sorteo</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                    </div>
                                                    <input class="form-control" readonly type="text" value="{{$reserve->lottery->draw_date ? \Carbon\Carbon::parse($reserve->lottery->draw_date)->format('d/m/Y') : 'No definida'}}" placeholder="Fecha" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="mb-0 mt-1">Configuración de la Reserva</h4>
                                    <small><i>Solo puedes editar los números, el importe y los décimos</i></small>
                                    <br><br>
                                    <div style="min-height: 256px;">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-1 d-flex align-items-start">
                                                        <button type="button" id="add-number" style="border-radius: 30px; width: 46px; height: 46px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder;" class="btn btn-md btn-light mt-3">
                                                            <i style="top: 6px; font-size: 18px; color: #fff" class="ri-add-line"></i>
                                                        </button>
                                                    </div>
                                                    <div class="col-11">
                                                        <div class="row" id="numbers">
                                                            @if(is_array($reserve->reservation_numbers))
                                                                @foreach($reserve->reservation_numbers as $i => $num)
                                                                    <div class="col-3 number-input-group">
                                                                        <div class="form-group mt-2 mb-3">
                                                                            <label class="label-control">Número</label>
                                                                            <div class="input-group input-group-merge group-form">
                                                                                <input class="form-control reservation-number" type="text" name="reservation_numbers[]" value="{{$num}}" style="border-radius: 30px 0 0 30px;">
                                                                                <div class="input-group-text remove-number" style="border-radius: 0 30px 30px 0; cursor:pointer;">
                                                                                    <i class="ri-close-line"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Importe a Reservar</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <input class="form-control" id="reservation_amount" type="number" step="0.01" name="reservation_amount" value="{{$reserve->reservation_amount}}" style="border-radius: 30px;">
                                                    </div>
                                                    <small class="text-muted"><i>Por cada número seleccionado</i></small>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Cantidad de décimos</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <input class="form-control" id="reservation_tickets" type="number" name="reservation_tickets" value="{{$reserve->reservation_tickets}}" style="border-radius: 30px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3 offset-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Total</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <input class="form-control" id="total_amount" type="number" step="0.01" placeholder="0.00" style="border-radius: 30px;" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
                                                    <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
<script>
    function addRemoveListeners() {
        document.querySelectorAll('.remove-number').forEach(function(btn) {
            btn.onclick = function() {
                const numbersDiv = document.getElementById('numbers');
                if(numbersDiv.querySelectorAll('.number-input-group').length > 1) {
                    const col = btn.closest('.number-input-group');
                    if(col) col.remove();
                }
            };
        });
    }
    document.getElementById('add-number').addEventListener('click', function(e) {
        e.preventDefault();
        const numbersDiv = document.getElementById('numbers');
        const newCol = document.createElement('div');
        newCol.className = 'col-3 number-input-group';
        newCol.innerHTML = `
            <div class=\"form-group mt-2 mb-3\">
                <label class=\"label-control\">Número</label>
                <div class=\"input-group input-group-merge group-form\">
                    <input class=\"form-control reservation-number\" type=\"text\" name=\"reservation_numbers[]\" placeholder=\"Número\" style=\"border-radius: 30px 0 0 30px;\">
                    <div class=\"input-group-text remove-number\" style=\"border-radius: 0 30px 30px 0; cursor:pointer;\"><i class=\"ri-close-line\"></i></div>
                </div>
            </div>
        `;
        numbersDiv.appendChild(newCol);
        addRemoveListeners();
    });
    // Inicializar listeners para los existentes
    addRemoveListeners();
    
    // Función para calcular el total
    function calculateTotal() {
        const numbers = document.querySelectorAll('.reservation-number');
        const reservationAmount = parseFloat(document.getElementById('reservation_amount').value) || 0;
        let totalNumbers = 0;
        
        numbers.forEach(function(numberInput) {
            if (numberInput.value.trim() !== '') {
                totalNumbers++;
            }
        });
        
        const total = totalNumbers * reservationAmount;
        document.getElementById('total_amount').value = total.toFixed(2);
    }
    
    // Importe: escribir libremente; recálculo (décimos e importe correcto) solo al salir del campo (blur)
    document.getElementById('reservation_amount').addEventListener('input', function() {
        calculateTotal();
    });
    document.getElementById('reservation_amount').addEventListener('blur', function() {
        calculateTicketsFromAmount();
        calculateTotal();
    });
    
    document.getElementById('reservation_tickets').addEventListener('input', function() {
        calculateAmountFromTickets();
        calculateTotal();
    });
    
    // Event listener para números (incluyendo los que se añadan dinámicamente)
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('reservation-number')) {
            calculateTotal();
        }
    });
    
    // Event listener para cuando se eliminen números
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-number')) {
            setTimeout(calculateTotal, 100); // Delay para que se elimine el elemento primero
        }
    });
    
    // Función para calcular décimos basado en el importe (siempre redondear al alza y ajustar importe al múltiplo)
    function calculateTicketsFromAmount() {
        const reservationAmount = parseFloat(document.getElementById('reservation_amount').value) || 0;
        const ticketPrice = {{$reserve->lottery->ticket_price ?? 0}};
        
        if (ticketPrice > 0 && reservationAmount > 0) {
            const tickets = Math.ceil(reservationAmount / ticketPrice); // Siempre al alza: no fracciones de décimo
            const amountRounded = tickets * ticketPrice; // Importe = múltiplo del precio
            document.getElementById('reservation_tickets').value = tickets;
            document.getElementById('reservation_amount').value = amountRounded.toFixed(2);
        }
    }
    
    // Función para calcular importe basado en los décimos
    function calculateAmountFromTickets() {
        const tickets = parseInt(document.getElementById('reservation_tickets').value) || 0;
        const ticketPrice = {{$reserve->lottery->ticket_price ?? 0}};
        
        const amount = tickets * ticketPrice;
        document.getElementById('reservation_amount').value = amount.toFixed(2);
    }
    
    // Calcular total inicial
    calculateTotal();
</script>
@endsection 