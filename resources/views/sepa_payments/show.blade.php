@extends('layouts.layout')

@section('title','Detalles Orden de Pago SEPA')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('sepa-payments.index')}}">Órdenes de Pago SEPA</a></li>
                        <li class="breadcrumb-item active">Detalles</li>
                    </ol>
                </div>
                <h4 class="page-title">Detalles Orden de Pago SEPA #{{$sepaPaymentOrder->id}}</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Información General -->
                    <h5 class="mb-3">Información General</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>Message ID:</strong><br>
                            <span>{{$sepaPaymentOrder->message_id}}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Administración:</strong><br>
                            <span>{{$sepaPaymentOrder->administration->name ?? 'N/A'}}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha de Creación:</strong><br>
                            <span>{{$sepaPaymentOrder->creation_date->format('d/m/Y H:i:s')}}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha de Ejecución:</strong><br>
                            <span>{{$sepaPaymentOrder->execution_date->format('d/m/Y')}}</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>Número de Transacciones:</strong><br>
                            <span>{{$sepaPaymentOrder->number_of_transactions}}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Importe Total:</strong><br>
                            <span class="h5">{{number_format($sepaPaymentOrder->control_sum, 2, ',', '.')}} €</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Estado:</strong><br>
                            @if($sepaPaymentOrder->status == 'draft')
                                <label class="badge bg-warning">Borrador</label>
                            @elseif($sepaPaymentOrder->status == 'generated')
                                <label class="badge bg-success">Generado</label>
                            @elseif($sepaPaymentOrder->status == 'uploaded')
                                <label class="badge bg-info">Subido</label>
                            @else
                                <label class="badge bg-secondary">{{$sepaPaymentOrder->status}}</label>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <strong>Archivo XML:</strong><br>
                            @if($sepaPaymentOrder->xml_filename)
                                <a href="{{route('sepa-payments.generate-xml', $sepaPaymentOrder->id)}}" class="btn btn-sm btn-primary">
                                    <i class="ri-download-line"></i> Descargar XML
                                </a>
                            @else
                                <a href="{{route('sepa-payments.generate-xml', $sepaPaymentOrder->id)}}" class="btn btn-sm btn-success">
                                    <i class="ri-file-add-line"></i> Generar XML
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($sepaPaymentOrder->notes)
                    <div class="row mb-4">
                        <div class="col-12">
                            <strong>Notas:</strong><br>
                            <span>{{$sepaPaymentOrder->notes}}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Datos del Deudor -->
                    <h5 class="mb-3 mt-4">Datos del Deudor (Pagador)</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>Nombre:</strong><br>
                            <span>{{$sepaPaymentOrder->debtor_name}}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>NIF/CIF:</strong><br>
                            <span>{{$sepaPaymentOrder->debtor_nif_cif ?? 'N/A'}}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>IBAN:</strong><br>
                            <span>{{$sepaPaymentOrder->debtor_iban}}</span>
                        </div>
                    </div>
                    @if($sepaPaymentOrder->debtor_address)
                    <div class="row mb-4">
                        <div class="col-12">
                            <strong>Dirección:</strong><br>
                            <span>{{$sepaPaymentOrder->debtor_address}}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Beneficiarios -->
                    <h5 class="mb-3 mt-4">Beneficiarios</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>End to End ID</th>
                                    <th>Nombre</th>
                                    <th>NIF/CIF</th>
                                    <th>IBAN</th>
                                    <th>Importe</th>
                                    <th>Moneda</th>
                                    <th>Código Propósito</th>
                                    <th>Remesa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sepaPaymentOrder->beneficiaries as $beneficiary)
                                <tr>
                                    <td>{{$beneficiary->end_to_end_id}}</td>
                                    <td>{{$beneficiary->creditor_name}}</td>
                                    <td>{{$beneficiary->creditor_nif_cif ?? 'N/A'}}</td>
                                    <td>{{$beneficiary->creditor_iban}}</td>
                                    <td>{{number_format($beneficiary->amount, 2, ',', '.')}}</td>
                                    <td>{{$beneficiary->currency}}</td>
                                    <td>{{$beneficiary->purpose_code}}</td>
                                    <td>{{$beneficiary->remittance_info ?? 'N/A'}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th>{{number_format($sepaPaymentOrder->control_sum, 2, ',', '.')}} €</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{route('sepa-payments.index')}}" class="btn btn-secondary">Volver</a>
                            @if($sepaPaymentOrder->xml_filename)
                                <a href="{{route('sepa-payments.generate-xml', $sepaPaymentOrder->id)}}" class="btn btn-primary">
                                    <i class="ri-download-line"></i> Descargar XML
                                </a>
                            @else
                                <a href="{{route('sepa-payments.generate-xml', $sepaPaymentOrder->id)}}" class="btn btn-success">
                                    <i class="ri-file-add-line"></i> Generar XML
                                </a>
                            @endif
                            <form action="{{route('sepa-payments.destroy', $sepaPaymentOrder->id)}}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta orden de pago?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection









