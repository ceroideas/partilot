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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tipo de Sorteo</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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

                    <form action="{{ route('lottery-types.update', $lotteryType->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                    	
                    	<div class="col-md-9">
                    		
                    		<div class="row">
                    			<div class="col-12">
                    				<div class="form-card bs mb-3">

                                        <h4 class="mb-0 mt-1">
                                            Edición tipos de Sorteo
                                        </h4>
                                        <small><i>Todos los campos son obligatorios</i></small>

                                        <div class="row">
                                            
                                            <div class="col-2">
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Nombre del Tipo de Sorteo</label>

                                                    <div class="input-group input-group-merge group-form">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" name="name" value="{{ $lotteryType->name }}" type="text" placeholder="Nombre del Tipo de Sorteo" style="border-radius: 0 30px 30px 0;" required>
                                                    </div>
                                                    @error('name')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Identificador</label>

                                                    <div class="input-group input-group-merge group-form">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" name="identificador" value="{{ $lotteryType->identificador }}" type="text" placeholder="ID" style="border-radius: 0 30px 30px 0;" maxlength="2" required>
                                                    </div>
                                                    @error('identificador')
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

                                                        <input class="form-control" name="ticket_price" value="{{ $lotteryType->ticket_price }}" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;" required>
                                                    </div>
                                                    @error('ticket_price')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!-- NUEVOS CAMPOS -->
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Series</label>

                                                    <div class="input-group input-group-merge group-form">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" name="series" value="{{ $lotteryType->series }}" type="number" placeholder="Ej: 100" style="border-radius: 0 30px 30px 0;" required>
                                                    </div>
                                                    @error('series')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Billetes por serie</label>

                                                    <div class="input-group input-group-merge group-form">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" name="billetes_serie" value="{{ $lotteryType->billetes_serie }}" type="number" placeholder="Ej: 1000" style="border-radius: 0 30px 30px 0;" required>
                                                    </div>
                                                    @error('billetes_serie')
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
                                            <img src="{{url('icons_/premios.svg')}}" alt="" width="36px">
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
        // Primero cargar premios ya guardados
        loadSavedPrizes();
        // Luego cargar categorías disponibles
        loadAvailablePrizes();
    });

    // Función para cargar las categorías disponibles
    function loadAvailablePrizes() {
        $.ajax({
            url: '{{ route("lottery-types.available-categories") }}',
            method: 'GET',
            success: function(response) {
                availablePrizes = response;
                console.log('Categorías disponibles cargadas:', availablePrizes);
                displayAvailablePrizes();
            },
            error: function() {
                console.error('Error al cargar categorías');
            }
        });
    }

    // Función para cargar premios ya guardados
    function loadSavedPrizes() {
        @if($lotteryType->prize_categories && is_array($lotteryType->prize_categories) && count($lotteryType->prize_categories) > 0)
            selectedPrizes = @json($lotteryType->prize_categories);
            console.log('Premios guardados cargados:', selectedPrizes);
            
            if (selectedPrizes && selectedPrizes.length > 0) {
                $('#added-prizes').removeClass('d-none');
                $('#empty-prizes').addClass('d-none');
                
                selectedPrizes.forEach(function(category) {
                    // Manejar tanto formato antiguo (string) como nuevo (objeto)
                    let nombre = typeof category === 'string' ? category : category.nombre;
                    let key = typeof category === 'string' ? category : category.key;
                    
                    let html = `<li style="list-style: none;" class="mb-2">
                                    <label style="position: relative; top: 4px;">${nombre}</label>
                                    <div class="float-end">
                                        <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize" data-key="${key}"><i class="ri-delete-bin-6-line"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>`;
                    $('#prizes-selected').append(html);
                });
                
                updateHiddenField();
                console.log('Premios mostrados en la interfaz');
            }
        @else
            console.log('No hay premios guardados o el campo está vacío');
            // Verificar si hay datos en formato JSON string
            @if($lotteryType->prize_categories)
                try {
                    let savedData = @json($lotteryType->prize_categories);
                    if (savedData && typeof savedData === 'string') {
                        selectedPrizes = JSON.parse(savedData);
                        console.log('Premios cargados desde JSON string:', selectedPrizes);
                        if (selectedPrizes && selectedPrizes.length > 0) {
                            $('#added-prizes').removeClass('d-none');
                            $('#empty-prizes').addClass('d-none');
                            
                            selectedPrizes.forEach(function(category) {
                                // Manejar tanto formato antiguo (string) como nuevo (objeto)
                                let nombre = typeof category === 'string' ? category : category.nombre;
                                let key = typeof category === 'string' ? category : category.key;
                                
                                let html = `<li style="list-style: none;" class="mb-2">
                                                <label style="position: relative; top: 4px;">${nombre}</label>
                                                <div class="float-end">
                                                    <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize" data-key="${key}"><i class="ri-delete-bin-6-line"></i></button>
                                                </div>
                                                <div class="clearfix"></div>
                                            </li>`;
                                $('#prizes-selected').append(html);
                            });
                            
                            updateHiddenField();
                            console.log('Premios mostrados en la interfaz desde JSON string');
                        }
                    }
                } catch (e) {
                    console.log('Error al parsear premios guardados:', e);
                }
            @endif
        @endif
    }

    // Función para mostrar las categorías disponibles
    function displayAvailablePrizes() {
        let html = '';
        console.log('Mostrando categorías disponibles. Premios seleccionados:', selectedPrizes);
        
        availablePrizes.forEach(function(category) {
            if (!selectedPrizes.some(selected => selected.key === category.key)) {
                html += `<li style="list-style: none;" class="mb-2">
                            <label style="position: relative; top: 4px;">${category.nombre}</label>
                            <div class="float-end">
                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize" data-nombre="${category.nombre}" data-key="${category.key}">Añadir</button>
                            </div>
                            <div class="clearfix"></div>
                        </li>`;
            } else {
                console.log(`Premio "${category.nombre}" ya está seleccionado, no se muestra en disponibles`);
            }
        });
        $('#availablePrizes').html(html);
        console.log('Categorías disponibles mostradas en la interfaz');
    }

    // Función para agregar premio
    $(document).on('click', '.add-prize', function(e) {
        e.preventDefault();
        
        let nombre = $(this).data('nombre');
        let key = $(this).data('key');
        let category = { nombre: nombre, key: key };
        
        selectedPrizes.push(category);
        
        $('#added-prizes').removeClass('d-none');
        $('#empty-prizes').addClass('d-none');

        let html = `<li style="list-style: none;" class="mb-2">
                        <label style="position: relative; top: 4px;">${nombre}</label>
                        <div class="float-end">
                            <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize" data-key="${key}"><i class="ri-delete-bin-6-line"></i></button>
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
        let key = $(this).data('key');
        let index = selectedPrizes.findIndex(item => item.key === key);
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