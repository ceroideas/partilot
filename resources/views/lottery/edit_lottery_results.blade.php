@extends('layouts.layout')

@section('title','Administraciones')

@section('content')

<style>
	input:disabled, textarea:disabled {
		background-color: #e0e0e0 !important;
	}
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Resultados</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Sorteos</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                	<h4 class="header-title">

                    	Edición Resultados

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-12">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Datos del Sorteo
                    			</h4>
                    			<small><i>Revisa que los datos del sorteo sean los correctos</i></small>

                    			<div class="form-group mt-2 mb-3">

                                    <div class="row show-content">

                                    	<div class="col-2">

                                    		<div style="width: calc(100% - 20px); height: 80px; border-radius: 8px; background-color: silver; float: left; margin-right: 20px;">
                                            </div>
                                    		
                                    	</div>

                                    	<div class="col-2">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Número de Sorteo</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
				                                    </div>

				                                    <input class="form-control" value="46/25" type="text" placeholder="Provincia" style="border-radius: 0 30px 30px 0;">
				                                </div>
			                    			</div>
                    					</div>

                    					<div class="col-4">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Nombre del Sorteo</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
				                                    </div>

				                                    <input class="form-control" value="Sorteo Extraordinario Asociación Española Contra El Cáncer" type="text" placeholder="Provincia" style="border-radius: 0 30px 30px 0;">
				                                </div>
			                    			</div>
                    					</div>

                                    </div>

                                    <div class="row">
                                    	
                                    	<div class="col-4">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Tipo de Sorteo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
	                                                </div>

	                                                <select class="form-control" name="" id="" style="border-radius: 0 30px 30px 0;">
	                                                	<option value="" disabled>Sorteo de Ejemplo</option>
	                                                	<option value="sabado" selected>Sorteo Sábado</option>
	                                                	<option value="jueves">Sorteo Jueves</option>
	                                                </select>
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Precio décimo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Fecha Sorteo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" type="date" placeholder="15/01/2025" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

                                    </div>
                    				
                    			</div>

                                <h4 class="mb-0 mt-1">
                                    Datos del Sorteo
                                </h4>
                                <small><i>Dejar en blanco los datos que no proceden para este tipo de sorteo. <br> Para extracciones múltiples separar cada número por un guión</i></small>


                                <div style="/*min-height: 400px; height: 400px; overflow-y: unset; overflow-x: scroll;*/">

                                    <div class="row">
                                    	
                                    	<div class="col-2">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Primer Premio</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" placeholder="77512" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-1">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Serie</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" placeholder="3" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-1">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Fracción</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" placeholder="3" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-1">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Reintegros</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" placeholder="7-8" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias extracciones <br> de 1 cifra</i></small>
	                                        </div>
	                                    </div>

                                    </div>

                                    <div class="row">
                                    	
                                    	<div class="col-2">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Segundo Premio</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" placeholder="80330" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Una Extracción <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-2">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Tercer Premio</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Una Extracción <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-3">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Cuarto Premio</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-6">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Quinto Premio</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-6">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 5 Cifras</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>

	                                    <div class="col-12">
	                                    	<small><i>Solo en los casos en el que existen <b>VARIOS 3º/4º Premios</b> como en los sorteos del NIÑO, S.ILDEFONSO o VACACIONES</i></small>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-3">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 4 Cifras</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 4 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-6">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 3 Cifras</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 3 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-3">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 2 Cifras</label>

	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="number" disabled style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>

	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 2 cifras</i></small>
	                                        </div>
	                                    </div>

	                                </div>

	                                <div class="row">
                                    	
                                    	<div class="col-12">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Pedrea</label>

	                                            <div class="input-group input-group-merge group-form">
	                                            	<textarea class="form-control" name="" id="" disabled style="border-radius: 30px;" rows="6"></textarea>
	                                            </div>
	                                        </div>
	                                    </div>

	                                </div>

                                </div>

                                <div class="row">

                                    <div class="col-6 text-start">
                                        <a href="{{url('lottery/results')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                            <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                                    </div>
                                    
                                    <div class="col-6 text-end">
                                        <a href="{{url('lottery/results')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Guardar
                                            <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></a>
                                    </div>

                                </div>

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

</script>

@endsection