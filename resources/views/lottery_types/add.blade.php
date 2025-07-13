@extends('layouts.layout')

@section('title','Tipos de Sorteo')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tipos de Sorteo</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Tipos de Sorteo</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Datos Tipo de Sorteo

                    </h4>

                    <br>

                    <form action="{{ route('lottery-types.store') }}" method="POST">
                        @csrf
                        <div class="row">
                    	
                    	<div class="col-md-9">
                    		
                    		<div class="row">
                    			<div class="col-12">
                    				<div class="form-card bs mb-3">

                                        <h4 class="mb-0 mt-1">
                                            Crear Tipo de Sorteo
                                        </h4>
                                        <small><i>Todos los campos son obligatorios</i></small>

                                        <div class="row">
                                            
                                            <div class="col-3">
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Nombre del Tipo de Sorteo</label>

                                                    <div class="input-group input-group-merge group-form">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" type="text" name="name" placeholder="Nombre del Tipo de Sorteo" style="border-radius: 0 30px 30px 0;" value="{{ old('name') }}" required>
                                                    </div>
                                                    @error('name')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Precio décimo</label>

                                                    <div class="input-group input-group-merge group-form">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" type="number" step="0.01" name="ticket_price" placeholder="6.00€" style="border-radius: 0 30px 30px 0;" value="{{ old('ticket_price') }}" required>
                                                    </div>
                                                    @error('ticket_price')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                    			</div>
                    			<div class="col-12">
                    				<div class="form-card bs">
                                        <h4 class="mb-0 mt-1">
                                            Selección Categoria de premios
                                        </h4>
                                        <small><i>Selecciona la categoria de premio deseada</i></small>

                                        <br>

                                        <div class="row">

                                            <div class="col-8 offset-2">
                                                <div class="form-group mt-2 mb-3">

                                                    <div class="input-group input-group-merge group-form">
                                                        <input class="form-control" type="text" id="prizeSearch" placeholder="Buscar categoría de premio" style="">
                                                    </div>

                                                    <div class="bs p-2" style="height: 200px; overflow: auto; border-top: 1px solid silver;" id="availablePrizes">

                                                        <!-- Las categorías se cargarán dinámicamente aquí -->
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                    			</div>
                    		</div>

                    	</div>
                    	<div class="col-md-3">

        					<div class="form-card bs" style="height: 100%;">
                                <h4 class="mb-0 mt-1">
                                    Premios del sorteo Seleccionados
                                </h4>
                                <small><i>Categoria de premios Seleccionadas</i></small>

                                <div class="" id="empty-prizes">
                                    <div class="empty-prizes">

                                        <div>
                                            <img src="{{url('icons/premios.svg')}}" alt="" width="36px">
                                        </div>

                                        <h5 class="mb-2">No hay Categoría de <br> premios asignados</h5>

                                        <small style="line-height: 1.3; display: block;">Añadre Categoría de premios <br> desde la tabla</small>

                                        <br>
                                    </div>
                                </div>

                                <div class="d-none" id="added-prizes">

                                    <div class="form-group mt-2 mb-3">

                                        <div class="input-group input-group-merge group-form">
                                            <input class="form-control" type="text" placeholder="Categoria de Premio" style="">
                                        </div>

                                        <div class="p-2" style="overflow: auto; border-top: 1px solid silver;" id="prizes-selected">
                                            
                                        </div>
                                        
                                        <!-- Campo oculto para enviar las categorías seleccionadas -->
                                        <input type="hidden" name="prize_categories" id="prize_categories_input">
                                    </div>
                                    
                                </div>
                            </div>
                    		
                    	</div>

                        <div class="col-12 text-end">
                            <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                        </div>
                    </form>

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
    let selectedPrizes = [];
    let availablePrizes = [];

    // Cargar categorías disponibles al cargar la página
    $(document).ready(function() {
        loadAvailablePrizes();
    });

    // Función para cargar las categorías disponibles
    function loadAvailablePrizes() {
        $.ajax({
            url: '{{ route("lottery-types.available-categories") }}',
            method: 'GET',
            success: function(response) {
                availablePrizes = response;
                displayAvailablePrizes();
            },
            error: function() {
                console.error('Error al cargar categorías');
            }
        });
    }

    // Función para mostrar las categorías disponibles
    function displayAvailablePrizes() {
        let html = '';
        availablePrizes.forEach(function(prize) {
            if (!selectedPrizes.includes(prize)) {
                html += `<li style="list-style: none;" class="mb-2">
                            <label style="position: relative; top: 4px;">${prize}</label>
                            <div class="float-end">
                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize" data-prize="${prize}">Añadir</button>
                            </div>
                            <div class="clearfix"></div>
                        </li>`;
            }
        });
        $('#availablePrizes').html(html);
    }

    // Función para agregar premio
    $(document).on('click', '.add-prize', function(e) {
        e.preventDefault();
        
        let prize = $(this).data('prize');
        selectedPrizes.push(prize);
        
        $('#added-prizes').removeClass('d-none');
        $('#empty-prizes').addClass('d-none');

        let html = `<li style="list-style: none;" class="mb-2">
                        <label style="position: relative; top: 4px;">${prize}</label>
                        <div class="float-end">
                            <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize" data-prize="${prize}"><i class="ri-delete-bin-6-line"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </li>`;

        $('#prizes-selected').append(html);
        
        // Actualizar el campo oculto
        updateHiddenField();
        
        // Actualizar la lista de disponibles
        displayAvailablePrizes();
    });

    // Función para remover premio
    $(document).on('click', '.remove-prize', function() {
        let prize = $(this).data('prize');
        let index = selectedPrizes.indexOf(prize);
        if (index > -1) {
            selectedPrizes.splice(index, 1);
        }
        
        $(this).parents('li').remove();

        if (selectedPrizes.length == 0) {
            $('#added-prizes').addClass('d-none');
            $('#empty-prizes').removeClass('d-none');
        }
        
        // Actualizar el campo oculto
        updateHiddenField();
        
        // Actualizar la lista de disponibles
        displayAvailablePrizes();
    });

    // Función para actualizar el campo oculto
    function updateHiddenField() {
        $('#prize_categories_input').val(JSON.stringify(selectedPrizes));
    }

    // Búsqueda de categorías
    $('#prizeSearch').on('input', function() {
        let searchTerm = $(this).val().toLowerCase();
        let html = '';
        
        availablePrizes.forEach(function(prize) {
            if (!selectedPrizes.includes(prize) && prize.toLowerCase().includes(searchTerm)) {
                html += `<li style="list-style: none;" class="mb-2">
                            <label style="position: relative; top: 4px;">${prize}</label>
                            <div class="float-end">
                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize" data-prize="${prize}">Añadir</button>
                            </div>
                            <div class="clearfix"></div>
                        </li>`;
            }
        });
        $('#availablePrizes').html(html);
    });

    // Validación del formulario
    $('form').on('submit', function(e) {
        if (selectedPrizes.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos una categoría de premio');
            return false;
        }
    });

</script>

@endsection