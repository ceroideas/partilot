@extends('layouts.layout')

@section('title','Administraciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Resultados</a></li>
                        <li class="breadcrumb-item active">Escrutinio</li>
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

                    	Escrutinio Sorteo

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-12">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Datos resultados sorteo
                    			</h4>
                    			<small><i>Asegúrate de que los datos sean correctos</i></small>

                    			<div class="form-group mt-2 mb-3">

                                    <div class="row">

                                        <div class="col-4">

                                            <div style="width: 150px; height: 80px; border-radius: 8px; background-color: silver; float: left; margin-right: 20px;">
                                            </div>

                                            <div style="float: left; margin-top: .5rem">
                                                Sorteo: 46/25 <br>
                                                
                                                <h4 class="mt-0 mb-0">
                                                    Sorteo Extraordinario Asociación <br> Española Contra El Cáncer
                                                </h4>

                                            </div>

                                            <div class="clearfix"></div>
                                            
                                        </div>

                                        <div class="col-2">

                                            <div style="float: left; margin-top: .5rem">
                                                Fecha Sorteo <br>
                                                
                                                <h5 class="mb-0">
                                                   07/08/2025 
                                                </h5>

                                            </div>
                                            
                                        </div>

                                        <div class="col-6">

                                            <div class="mt-2">
                                                Participaciones Premiadas: Logroño: <b>12 Números</b> <br>
                                                Participaciones No Premiadas: Logroño: <b>56 Números</b> <br>
                                                Importe Premios Repartidos: <b>585.400€</b>
                                            </div>
                                            
                                        </div>
                                    </div>
                    				
                    			</div>

                                <h4 class="mb-0 mt-1">
                                    Lista de participaciones premiadas
                                </h4>

                                <div style="min-height: 400px; height: 400px; overflow: auto;">

                                    <table class="table">

                                        <thead>
                                            <tr>
                                                <th>Participaciones</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Premio Total</th>
                                                <th class="text-center">Premio Participaciones</th>
                                            </tr>
                                        </thead>

                                        <tbody class="text-center">
                                            <tr>
                                                <td><b>CSIF Madrid</b></td>

                                                <td>
                                                    Emitidas: <b>7400</b> <br>
                                                    Vendidas: <b>6450</b> <br>
                                                    Devueltas: <b>950</b> <br>
                                                </td>
                                                <td>
                                                    <b>161.250,00€</b>
                                                </td>
                                                <td>
                                                    <b>25,00€</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="border-bottom: 1px solid #333;">
                                                    <b>Número: 40083 - Premiado con 100 € X 1850 décimos = 185.000 €</b>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><b>Colaboración Animal</b></td>

                                                <td>
                                                    Emitidas: <b>2750</b> <br>
                                                    Vendidas: <b>2555</b> <br>
                                                    Devueltas: <b>195</b> <br>
                                                </td>
                                                <td>
                                                    <b>51.100,00€</b>
                                                </td>
                                                <td>
                                                    <b>20,00€</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="border-bottom: 1px solid #333;">
                                                    <b>Número: 91996 - Premiado con 100 € X 550 décimos = 55.000 €</b>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><b>Colaboración Animal</b></td>

                                                <td>
                                                    Emitidas: <b>2750</b> <br>
                                                    Vendidas: <b>2555</b> <br>
                                                    Devueltas: <b>195</b> <br>
                                                </td>
                                                <td>
                                                    <b>51.100,00€</b>
                                                </td>
                                                <td>
                                                    <b>20,00€</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="border-bottom: 1px solid #333;">
                                                    <b>Número: 91996 - Premiado con 100 € X 550 décimos = 55.000 €</b>
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                        
                                    </table>

                                </div>

                                <div class="row">

                                    <div class="col-6 text-start">
                                        <a href="{{url('lottery/results')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                            <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                                    </div>
                                    
                                    <div class="col-6 text-end">
                                        <a href="{{url('lottery/results')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Aceptar
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