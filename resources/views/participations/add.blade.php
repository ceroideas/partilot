@extends('layouts.layout')

@section('title','Participaciones')

@section('content')

<style>
    .part-information {
        transition: all 500ms;
    }
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Participaciones</li>
                    </ol>
                </div>
                <h4 class="page-title">Participaciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="col-2">
                                
                            <div class="form-card bs">
                                <div class="row">
                                    <div class="col-4">
                                        
                                        <div class="photo-preview-2">
                                            
                                            <i class="ri-account-circle-fill"></i>

                                        </div>
                                        
                                        <div style="clear: both;"></div>
                                    </div>

                                    <div class="col-8 text-center">

                                        <h4 class="mb-0 mt-2">
                                            Fademur
                                        </h4>
                                        <i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> La Rioja
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="col-10">
                            <div class="form-card bs">

                                <div class="row">
                                    
                                    <div class="col-6 d-flex">
                                        
                                        <div>
                                            <img src="{{url('icons/participaciones.svg')}}" alt="" width="50px" style="margin-top: 8px; margin-right: 8px;">
                                        </div>

                                        <div class="mt-1">
                                            <h4 class="mb-0 mt-1">
                                                Busqueda Participaciones
                                            </h4>
                                            <small class="mt-1 mb-1 d-block"><i>Introduce el número de participación</i></small>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        
                                        <div class="row">
                                            
                                            <div class="col-7">
                                                <div class="form-group mt-2">

                                                    <div class="input-group input-group-merge group-form bs">

                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
                                                        </div>

                                                        <input class="form-control" type="text" placeholder="Nº participación" style="border-radius: 0 30px 30px 0;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <a href="{{url('participations/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end mt-2">Buscar</a>
                                            </div>

                                        </div>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="{{isset($_GET['table']) ? '' : 'd-none'}}">

                        <br>
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                <input type="text" class="form-control" placeholder="Status">
                            </div>
                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <div style="min-height: 358px;">

                            <div class="form-card mb-2 p-0">
                                <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">
                                    <thead>
                                        <tr style="font-size: 10px;">
                                            <th rowspan="2" class="no-filter" style="border-color: transparent;">
                                                <img src="{{url('assets/participacion.png')}}" alt="" width="150px">
                                            </th>
                                            <th>Orden ID</th>
                                            <th>Nombre Set</th>
                                            <th>N. Sorteo</th>
                                            <th>Número/s</th>
                                            <th>Importe Jugado (Número)</th>
                                            <th>Importe Donativo</th>
                                            <th>Importe TOTAL</th>
                                            <th>Participaciones TOTAL</th>
                                            <th>Participaciones Físicas</th>
                                            <th>Participaciones Digitales</th>
                                            <th></th>
                                        </tr>
                                        <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                            <td>#SP9801</td>
                                            <td>Set de Pruebas</td>
                                            <td>46/25</td>
                                            <td>05716 - 52468 - 51235 - 69584</td>
                                            <td>2,00€</td>
                                            <td>2,00€</td>
                                            <td>10,00€</td>
                                            <td>750</td>
                                            <td>600</td>
                                            <td>150</td>
                                            <td>
                                                <a class="btn btn-sm btn-light show-information"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                            </td>
                                        </tr>
                                    </thead>
                                </table>

                                <div style="height: 0px; overflow: hidden;" class="part-information">

                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">
                                        <thead>
                                            <tr style="font-size: 10px;">
                                                <th rowspan="2" style="width: 182px; border-color: transparent;"></th>
                                                <th>Participaciones Vendidas</th>
                                                <th>Participaciones Devueltas</th>
                                                <th>Participaciones Anuladas</th>
                                                <th>Participaciones Disponibles</th>
                                                <th>Importe Lotería TOTAL</th>
                                                <th>Importe Donativo TOTAL</th>
                                                <th>Lotería + Donativo TOTAL</th>
                                            </tr>
                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                <td>750</td>
                                                <td>120</td>
                                                <td>120</td>
                                                <td>120</td>
                                                <td>6.000,00€</td>
                                                <td>1.500,00€</td>
                                                <td>7.500,00€</td>
                                            </tr>
                                        </thead>
                                    </table>

                                    <hr>

                                    <div class="p-3 pt-0 pb-0">
                                        
                                        <h4 class="header-title">

                                            <div class="float-start d-flex align-items-start">
                                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                                <input type="text" class="form-control" placeholder="Status">
                                            </div>
                                        </h4>

                                        <div style="clear: both;"></div>

                                        <br>

                                        <div style="height: 250px; overflow: auto;" id="details-participations" class="d-none">

                                            <div class="row">
                                                <div class="col-10 offset-2">
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Participación</th>
                                                                <th>Estado</th>
                                                                <th>Vendedor</th>
                                                                <th>Fecha Venta</th>
                                                                <th>Hora Venta</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td><label class="badge bg-success">Vendida</label></td>
                                                                <td>Jorge Ruiz Ortega</td>
                                                                <td>20/10/2025</td>
                                                                <td>21:00h</td>
                                                                <td>
                                                                    <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Participación</th>
                                                                <th>Estado</th>
                                                                <th>Vendedor</th>
                                                                <th>Fecha Venta</th>
                                                                <th>Hora Venta</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td><label class="badge bg-success">Vendida</label></td>
                                                                <td>Jorge Ruiz Ortega</td>
                                                                <td>20/10/2025</td>
                                                                <td>21:00h</td>
                                                                <td>
                                                                    <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Participación</th>
                                                                <th>Estado</th>
                                                                <th>Vendedor</th>
                                                                <th>Fecha Venta</th>
                                                                <th>Hora Venta</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td><label class="badge bg-success">Vendida</label></td>
                                                                <td>Jorge Ruiz Ortega</td>
                                                                <td>20/10/2025</td>
                                                                <td>21:00h</td>
                                                                <td>
                                                                    <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                        <div style="height: 250px; overflow: auto;" id="list-participations" class="">
                                            
                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                        </div>

                                    </div>
                                    
                                </div>
                            </div>

                            <div class="form-card mb-2 p-0">
                                <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">
                                    <thead>
                                        <tr style="font-size: 10px;">
                                            <th rowspan="2" class="no-filter" style="border-color: transparent;">
                                                <img src="{{url('assets/participacion.png')}}" alt="" width="150px">
                                            </th>
                                            <th>Orden ID</th>
                                            <th>Nombre Set</th>
                                            <th>N. Sorteo</th>
                                            <th>Número/s</th>
                                            <th>Importe Jugado (Número)</th>
                                            <th>Importe Donativo</th>
                                            <th>Importe TOTAL</th>
                                            <th>Participaciones TOTAL</th>
                                            <th>Participaciones Físicas</th>
                                            <th>Participaciones Digitales</th>
                                            <th></th>
                                        </tr>
                                        <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                            <td>#SP9801</td>
                                            <td>Set de Pruebas</td>
                                            <td>46/25</td>
                                            <td>05716 - 52468 - 51235 - 69584</td>
                                            <td>2,00€</td>
                                            <td>2,00€</td>
                                            <td>10,00€</td>
                                            <td>750</td>
                                            <td>600</td>
                                            <td>150</td>
                                            <td>
                                                <a class="btn btn-sm btn-light show-information"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                            </td>
                                        </tr>
                                    </thead>
                                </table>

                                <div style="height: 0px; overflow: hidden;" class="part-information">

                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">
                                        <thead>
                                            <tr style="font-size: 10px;">
                                                <th rowspan="2" style="width: 182px; border-color: transparent;"></th>
                                                <th>Participaciones Vendidas</th>
                                                <th>Participaciones Devueltas</th>
                                                <th>Participaciones Anuladas</th>
                                                <th>Participaciones Disponibles</th>
                                                <th>Importe Lotería TOTAL</th>
                                                <th>Importe Donativo TOTAL</th>
                                                <th>Lotería + Donativo TOTAL</th>
                                            </tr>
                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                <td>750</td>
                                                <td>120</td>
                                                <td>120</td>
                                                <td>120</td>
                                                <td>6.000,00€</td>
                                                <td>1.500,00€</td>
                                                <td>7.500,00€</td>
                                            </tr>
                                        </thead>
                                    </table>

                                    <hr>

                                    <div class="p-3 pt-0 pb-0">
                                        
                                        <h4 class="header-title">

                                            <div class="float-start d-flex align-items-start">
                                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                                <input type="text" class="form-control" placeholder="Status">
                                            </div>
                                        </h4>

                                        <div style="clear: both;"></div>

                                        <br>

                                        <div style="height: 250px; overflow: auto;" id="details-participations" class="d-none">

                                            <div class="row">
                                                <div class="col-10 offset-2">
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Participación</th>
                                                                <th>Estado</th>
                                                                <th>Vendedor</th>
                                                                <th>Fecha Venta</th>
                                                                <th>Hora Venta</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td><label class="badge bg-success">Vendida</label></td>
                                                                <td>Jorge Ruiz Ortega</td>
                                                                <td>20/10/2025</td>
                                                                <td>21:00h</td>
                                                                <td>
                                                                    <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Participación</th>
                                                                <th>Estado</th>
                                                                <th>Vendedor</th>
                                                                <th>Fecha Venta</th>
                                                                <th>Hora Venta</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td><label class="badge bg-success">Vendida</label></td>
                                                                <td>Jorge Ruiz Ortega</td>
                                                                <td>20/10/2025</td>
                                                                <td>21:00h</td>
                                                                <td>
                                                                    <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Participación</th>
                                                                <th>Estado</th>
                                                                <th>Vendedor</th>
                                                                <th>Fecha Venta</th>
                                                                <th>Hora Venta</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td><label class="badge bg-success">Vendida</label></td>
                                                                <td>Jorge Ruiz Ortega</td>
                                                                <td>20/10/2025</td>
                                                                <td>21:00h</td>
                                                                <td>
                                                                    <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                        <div style="height: 250px; overflow: auto;" id="list-participations" class="">
                                            
                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                            <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                <thead>
                                                    <tr style="font-size: 10px;">
                                                        <th rowspan="2" style="border-color: transparent;">
                                                            <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                            </div>
                                                        </th>
                                                        <th>Nº Taco</th>
                                                        <th>Participaciones</th>
                                                        <th>Nº Participaciones</th>
                                                        <th>Ventas Registradas</th>
                                                        <th>Participaciones Devueltas</th>
                                                        <th>Participaciones Disponibles</th>
                                                        <th>Estado</th>
                                                        <th>Vendedor</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                        <td>1/0001</td>
                                                        <td>50</td>
                                                        <td>1/00001 - 1/00050</td>
                                                        <td>10</td>
                                                        <td>5</td>
                                                        <td>35</td>
                                                        <td><label class="badge bg-success">Asignado</label></td>
                                                        <td>Jorge Ruíz Ortega</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                
                                            </table>

                                        </div>

                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 text-start">
                                <a href="{{url('participations')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                    <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                            </div>
                            
                            <div class="col-6 text-end">
                                <a href="{{url('participations/add?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Siguiente
                                    <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></a>
                            </div>
                        </div>

                    </div>

                    <div class="{{isset($_GET['table']) ? 'd-none' : ''}}">
                        
                        <div class="d-flex align-items-center gap-1">
                            
                            <div class="empty-tables">

                                <div>
                                    <img src="{{url('icons/participaciones.svg')}}" alt="" width="80px" style="margin-top: 10px;">
                                </div>

                                <h3 class="mb-0">No hay Participaciones</h3>

                                <small>Las participaciones se añadirán <br> automaticamente una vez <br> completado el diseño en la seccion <br> <b>Diseño e Impresión</b></small>

                                {{-- <br>

                                <a href="{{url('participations/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a> --}}
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6 text-start">
                                <a href="{{url('participations')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                    <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                            </div>
                            
                            <div class="col-6 text-end">
                                <a href="{{url('participations/add?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Siguiente
                                    <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></a>
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
    
  function initDatatable() 
  {
    $("#example2").DataTable({

      "ordering": false,
      "sorting": false,

      "scrollX": true, "scrollCollapse": true,
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function () {
            var api = this.api();
 
            // For each column
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    if ($(cell).hasClass('no-filter')) {
                      $(cell).addClass('sorting_disabled').html(title);
                    }else{
                      $(cell).addClass('sorting_disabled').html('<input type="text" class="inline-fields" placeholder="' + title + '" />');
                    }
 
                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('keyup change', function (e) {
                            e.stopPropagation();
 
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();
 
                            var cursorPosition = this.selectionStart;
                            // Search the column for that value

                            // console.log(val.replace(/<select[\s\S]*?<\/select>/,''));
                            let wSelect = false;
                            $.each(api.column(colIdx).data(), function(index, val) {
                               if (val.indexOf('<select') == -1) {
                                wSelect = false;
                               }else{
                                wSelect = true;
                               }
                            });

                            // $.each(api
                            //     .column(colIdx).data(), function(index, val) {
                            //     console.log(val)
                            // });

                            api
                                .column(colIdx)
                                .search(

                                  (wSelect ?
                                      (this.value != ''
                                        ? regexr.replace('{search}', '(((selected' + this.value + ')))')
                                        : '')
                                    :
                                      (this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '')),

                                    this.value != '',
                                    this.value == ''
                                ).draw()
 
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        }
    });
  }

  initDatatable();

  setTimeout(()=>{
    $('.filters .inline-fields:first').trigger('keyup');
  },100);

  $('.show-information').click(function (e) {
      e.preventDefault();

      if ($(this).parents('.form-card').find('.part-information').css('height') == '0px') {
        $(this).parents('.form-card').find('.part-information').css('height', '462px');
      }else{
        $(this).parents('.form-card').find('.part-information').css('height', '0px');
        setTimeout(()=>{
            $(this).parents('.form-card').find('#details-participations').addClass('d-none');
            $(this).parents('.form-card').find('#list-participations').removeClass('d-none');
        },500);
      }

  });

  $('.show-details').click(function(event) {
      $(this).parents('.form-card').find('#details-participations').removeClass('d-none');
      $(this).parents('.form-card').find('#list-participations').addClass('d-none');
  });
</script>

@endsection