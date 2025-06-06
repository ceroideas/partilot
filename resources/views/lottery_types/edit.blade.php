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

                    <div class="row">
                    	
                    	<div class="col-md-9">
                    		
                    		<div class="row">
                    			<div class="col-12">
                    				<div class="form-card bs mb-3">

                                        <h4 class="mb-0 mt-1">
                                            Editión tipos de Sorteo
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

                                                        <input class="form-control" value="Sorteo Sabado" type="text" placeholder="Nombre del Tipo de Sorteo" style="border-radius: 0 30px 30px 0;">
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

                                                        <input class="form-control" value="6.00" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;">
                                                    </div>
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
                                                        <input class="form-control" type="text" placeholder="Categoria de Premio" style="">
                                                    </div>

                                                    <div class="bs p-2" style="height: 200px; overflow: auto; border-top: 1px solid silver;">

                                                        <li style="list-style: none;" class="mb-2">
                                                            <label style="position: relative; top: 4px;">Primer premio</label>
                                                            <div class="float-end">
                                                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize" disabled>Añadir</button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </li>

                                                        <li style="list-style: none;" class="mb-2">
                                                            <label style="position: relative; top: 4px;">Primer premio</label>
                                                            <div class="float-end">
                                                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize" disabled>Añadir</button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </li>

                                                        <li style="list-style: none;" class="mb-2">
                                                            <label style="position: relative; top: 4px;">Primer premio</label>
                                                            <div class="float-end">
                                                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize" disabled>Añadir</button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </li>

                                                        <li style="list-style: none;" class="mb-2">
                                                            <label style="position: relative; top: 4px;">Primer premio</label>
                                                            <div class="float-end">
                                                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize">Añadir</button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </li>

                                                        <li style="list-style: none;" class="mb-2">
                                                            <label style="position: relative; top: 4px;">Primer premio</label>
                                                            <div class="float-end">
                                                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize">Añadir</button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </li>

                                                        <li style="list-style: none;" class="mb-2">
                                                            <label style="position: relative; top: 4px;">Primer premio</label>
                                                            <div class="float-end">
                                                                <button style="border-radius: 30px; width: 100px; background-color: #e78307; color: #333; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-light add-prize">Añadir</button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </li>

                                                        
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

                                <div class="d-none" id="empty-prizes">
                                    <div class="empty-prizes">

                                        <div>
                                            <img src="{{url('icons/premios.svg')}}" alt="" width="36px">
                                        </div>

                                        <h5 class="mb-2">No hay Categoría de <br> premios asignados</h5>

                                        <small style="line-height: 1.3; display: block;">Añadre Categoría de premios <br> desde la tabla</small>

                                        <br>
                                    </div>
                                </div>

                                <div class="" id="added-prizes">

                                    <div class="form-group mt-2 mb-3">

                                        <div class="input-group input-group-merge group-form">
                                            <input class="form-control" type="text" placeholder="Categoria de Premio" style="">
                                        </div>

                                        <div class="p-2" style="overflow: auto; border-top: 1px solid silver;" id="prizes-selected">

                                        	<li style="list-style: none;" class="mb-2">
						                        <label style="position: relative; top: 4px;">Primer premio</label>
						                        <div class="float-end">
						                            <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize"><i class="ri-delete-bin-6-line"></i></button>
						                        </div>
						                        <div class="clearfix"></div>
						                    </li>

						                    <li style="list-style: none;" class="mb-2">
						                        <label style="position: relative; top: 4px;">Primer premio</label>
						                        <div class="float-end">
						                            <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize"><i class="ri-delete-bin-6-line"></i></button>
						                        </div>
						                        <div class="clearfix"></div>
						                    </li>

						                    <li style="list-style: none;" class="mb-2">
						                        <label style="position: relative; top: 4px;">Primer premio</label>
						                        <div class="float-end">
						                            <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize"><i class="ri-delete-bin-6-line"></i></button>
						                        </div>
						                        <div class="clearfix"></div>
						                    </li>
                                            
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                    		
                    	</div>

                        <div class="col-12 text-end">
                            <a href="{{url('lottery_types?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></a>
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

    $('.add-prize').click(function (e) {
        e.preventDefault();

        $(this).prop('disabled', true);

        $('#added-prizes').removeClass('d-none');
        $('#empty-prizes').addClass('d-none');

        let html = `<li style="list-style: none;" class="mb-2">
                        <label style="position: relative; top: 4px;">Primer premio</label>
                        <div class="float-end">
                            <button style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-prize"><i class="ri-delete-bin-6-line"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </li>`;

        $('#prizes-selected').append(html);

        $('.remove-prize').unbind('click',removePrize);
        $('.remove-prize').bind('click',removePrize);
    });

    function removePrize()
    {
        $(this).parents('li').remove();

        $('.add-prize:disabled:last').prop('disabled',false);

        if ($('.remove-prize').length == 0) {
            $('#added-prizes').addClass('d-none');
            $('#empty-prizes').removeClass('d-none');
        }
    }

    $('.remove-prize').bind('click',removePrize);

</script>

@endsection