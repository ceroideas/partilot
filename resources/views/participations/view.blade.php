@extends('layouts.layout')

@section('title', 'Ver Participación')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('participations.index') }}">Participaciones</a></li>
                        <li class="breadcrumb-item active">Ver Participación</li>
                    </ol>
                </div>
                <h4 class="page-title">Ver Participación</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">
                        Detalles de la Participación
                    </h4>

                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <ul class="form-card bs mb-3 nav">
                                <li class="nav-item">
                                    <div class="form-wizard-element active" data-bs-toggle="tab" data-bs-target="#detalles_participacion">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>Detalles</label>
                                    </div>
                                </li>
                            </ul>

                            <!-- Información de la Participación -->
                            <div class="form-card bs mb-3">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="photo-preview-3">
                                            <img src="{{url('assets/ticket.svg')}}" alt="" width="40px">
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                    <div class="col-8 text-center mt-2">
                                        <h3 class="mt-2 mb-0">{{ $participation->participation_code ?? 'N/A' }}</h3>
                                        <i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-ticket-line"></i> Participación
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('participations.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>

                        <div class="col-md-9">
                            <div class="tabbable">
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade active show" id="detalles_participacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center show-content">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Información de la Participación
                                                    </h4>
                                                    <small><i>Detalles completos de la participación</i></small>
                                                </div>
                                            </div>

                                                                                         <div class="row show-content mt-4">
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Código de Participación</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->participation_code ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Número de Participación</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->participation_number ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                             <div class="row show-content">
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Entidad</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->set && $participation->set->reserve && $participation->set->reserve->entity ? $participation->set->reserve->entity->name : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Administración</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->set && $participation->set->reserve && $participation->set->reserve->entity && $participation->set->reserve->entity->administration ? $participation->set->reserve->entity->administration->name : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                            <div class="row show-content">
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Estado</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ ucfirst($participation->status ?? 'N/A') }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Fecha de Venta</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $participation->sale_date ? \Carbon\Carbon::parse($participation->sale_date)->format('d/m/Y') : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row show-content">
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Hora de Venta</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/10.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $participation->sale_time ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Vendedor</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'Sin asignar' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                            </div>

                                            @if($participation->set)
                                            <div class="row show-content">
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Set</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $participation->set->set_name ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Reserva</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $participation->set->reserve ? '#RS' . str_pad($participation->set->reserve->id, 4, '0', STR_PAD_LEFT) : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                                                                         @if($participation->set && $participation->set->reserve && $participation->set->reserve->lottery)
                                             <div class="row show-content">
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Sorteo</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->set->reserve->lottery->name ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Tipo de Sorteo</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->set->reserve->lottery->lotteryType ? $participation->set->reserve->lottery->lotteryType->name : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="row show-content">
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Fecha del Sorteo</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->set->reserve->lottery->draw_date ? \Carbon\Carbon::parse($participation->set->reserve->lottery->draw_date)->format('d/m/Y') : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="form-group mt-2 mb-3">
                                                         <label class="label-control">Números Reservados</label>
                                                         <div class="input-group input-group-merge group-form">
                                                             <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                 <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                             </div>
                                                             <input class="form-control" type="text" value="{{ $participation->set->reserve->reservation_numbers ? implode(' - ', $participation->set->reserve->reservation_numbers) : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                                                                          @endif
                                             @endif

                                             <!-- Información Financiera -->
                                             @if($participation->set)
                                             <div class="row show-content mt-4">
                                                 <div class="col-12">
                                                     <h5 class="mb-3">Información Financiera</h5>
                                                     <div class="row">
                                                         <div class="col-md-6">
                                                             <div class="form-group mt-2 mb-3">
                                                                 <label class="label-control">Precio del Número</label>
                                                                 <div class="input-group input-group-merge group-form">
                                                                     <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                         <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                                     </div>
                                                                     <input class="form-control" type="text" value="{{ $participation->set->reserve && $participation->set->reserve->lottery ? number_format($participation->set->reserve->lottery->ticket_price, 2) . '€' : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <div class="form-group mt-2 mb-3">
                                                                 <label class="label-control">Importe Donativo</label>
                                                                 <div class="input-group input-group-merge group-form">
                                                                     <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                         <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                                     </div>
                                                                     <input class="form-control" type="text" value="{{ $participation->set ? number_format($participation->set->donation_amount, 2) . '€' : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                     <div class="row">
                                                         <div class="col-md-6">
                                                             <div class="form-group mt-2 mb-3">
                                                                 <label class="label-control">Importe Total</label>
                                                                 <div class="input-group input-group-merge group-form">
                                                                     <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                         <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                                     </div>
                                                                     <input class="form-control" type="text" value="{{ $participation->set ? number_format($participation->set->total_amount, 2) . '€' : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <div class="form-group mt-2 mb-3">
                                                                 <label class="label-control">Estado de Pago</label>
                                                                 <div class="input-group input-group-merge group-form">
                                                                     <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                         <img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
                                                                     </div>
                                                                     <input class="form-control" type="text" value="{{ $participation->status == 'vendida' ? 'Pagado' : ($participation->status == 'disponible' ? 'Pendiente' : 'N/A') }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                             @endif

                                             <!-- Historial de Estados -->
                                             <div class="row show-content mt-4">
                                                 <div class="col-12">
                                                     <h5 class="mb-3">Historial de Estados</h5>
                                                     <div class="table-responsive">
                                                         <table class="table table-striped">
                                                             <thead>
                                                                 <tr>
                                                                     <th>Estado</th>
                                                                     <th>Fecha</th>
                                                                     <th>Hora</th>
                                                                     <th>Vendedor</th>
                                                                     <th>Observaciones</th>
                                                                 </tr>
                                                             </thead>
                                                             <tbody>
                                                                 <tr>
                                                                     <td>
                                                                         <span class="badge bg-primary">Creada</span>
                                                                     </td>
                                                                     <td>{{ $participation->created_at ? \Carbon\Carbon::parse($participation->created_at)->format('d/m/Y') : 'N/A' }}</td>
                                                                     <td>{{ $participation->created_at ? \Carbon\Carbon::parse($participation->created_at)->format('H:i') : 'N/A' }}</td>
                                                                     <td>Sistema</td>
                                                                     <td>Participación creada automáticamente</td>
                                                                 </tr>
                                                                 @if($participation->seller && $participation->seller->user)
                                                                 <tr>
                                                                     <td>
                                                                         <span class="badge bg-warning">Asignada</span>
                                                                     </td>
                                                                     <td>{{ $participation->assigned_at ? \Carbon\Carbon::parse($participation->assigned_at)->format('d/m/Y') : ($participation->sale_date ? \Carbon\Carbon::parse($participation->sale_date)->format('d/m/Y') : 'N/A') }}</td>
                                                                     <td>{{ $participation->assigned_at ? \Carbon\Carbon::parse($participation->assigned_at)->format('H:i') : ($participation->sale_time ?? 'N/A') }}</td>
                                                                     <td>{{ $participation->seller->user->name ?? 'N/A' }}</td>
                                                                     <td>Asignada al vendedor {{ $participation->seller->user->name }}</td>
                                                                 </tr>
                                                                 @endif
                                                                 @if($participation->status == 'vendida')
                                                                 <tr>
                                                                     <td>
                                                                         <span class="badge bg-success">Vendida</span>
                                                                     </td>
                                                                     <td>{{ $participation->sale_date ? \Carbon\Carbon::parse($participation->sale_date)->format('d/m/Y') : 'N/A' }}</td>
                                                                     <td>{{ $participation->sale_time ?? 'N/A' }}</td>
                                                                     <td>{{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'N/A' }}</td>
                                                                     <td>Venta registrada por {{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'vendedor' }}</td>
                                                                 </tr>
                                                                 @endif
                                                                 @if($participation->status == 'devuelta')
                                                                 <tr>
                                                                     <td>
                                                                         <span class="badge bg-danger">Devuelta</span>
                                                                     </td>
                                                                     <td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('d/m/Y') : 'N/A' }}</td>
                                                                     <td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('H:i') : 'N/A' }}</td>
                                                                     <td>{{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'N/A' }}</td>
                                                                     <td>Participación devuelta por {{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'vendedor' }}</td>
                                                                 </tr>
                                                                 @endif
                                                                 @if($participation->status == 'disponible')
                                                                 <tr>
                                                                     <td>
                                                                         <span class="badge bg-info">Disponible</span>
                                                                     </td>
                                                                     <td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('d/m/Y') : 'N/A' }}</td>
                                                                     <td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('H:i') : 'N/A' }}</td>
                                                                     <td>Sistema</td>
                                                                     <td>Participación disponible para venta</td>
                                                                 </tr>
                                                                 @endif
                                                             </tbody>
                                                         </table>
                                                     </div>
                                                 </div>
                                             </div>

                                        </div>
                                    </div>
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
