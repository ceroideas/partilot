@extends('layouts.layout')

@section('title','Vendedores/Asignación')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('sellers.index') }}">Vendedores/Asignación</a></li>
                        <li class="breadcrumb-item active">Ver Vendedor</li>
                    </ol>
                </div>
                <h4 class="page-title">Vendedores/Asignación</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">
                        Detalles del Vendedor
                    </h4>

                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <div class="form-card bs mb-3">
                                <div class="form-wizard-element active">
                                    <span>
                                        &nbsp;&nbsp;
                                    </span>
                                    <img src="{{url('icons/vendedores.svg')}}" alt="">
                                    <label>
                                        Datos Vendedor
                                    </label>
                                </div>
                            </div>

                            <a href="{{ route('sellers.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>

                        <div class="col-md-9">
                            <div class="form-card bs" style="min-height: 658px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0 mt-1">
                                            Información del Vendedor
                                        </h4>
                                        <small><i>Detalles completos del vendedor</i></small>
                                    </div>
                                    <div>
                                        <a href="{{ route('sellers.edit', $seller->id) }}" class="btn btn-light" style="border: 1px solid silver; border-radius: 30px;"> 
                                            <img src="{{url('assets/form-groups/edit.svg')}}" alt="">
                                            Editar
                                        </a>
                                    </div>
                                </div>

                                <div class="form-group mt-2 mb-3 admin-box">
                                    <div class="row">
                                        <div class="col-1">
                                            <div class="photo-preview-3">
                                                <i class="ri-account-circle-fill"></i>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>

                                        <div class="col-4 text-center mt-3">
                                            <h4 class="mt-0 mb-0">{{ $seller->entity->name ?? 'Entidad' }}</h4>
                                            <small>{{ $seller->entity->province ?? 'Provincia' }}</small> <br>
                                            <small>{{ $seller->entity->administration->name ?? 'Administración' }}</small>
                                        </div>

                                        <div class="col-3">
                                            <div class="mt-3">
                                                Provincia: {{ $seller->entity->province ?? 'N/A' }} <br>
                                                Dirección: {{ $seller->entity->address ?? 'N/A' }}
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <div class="mt-3">
                                                Ciudad: {{ $seller->entity->city ?? 'N/A' }} <br>
                                                Tel: {{ $seller->entity->phone ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Nombre</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->name ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Primer Apellido</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->last_name ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Segundo Apellido</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->last_name2 ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">NIF/CIF</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->nif_cif ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">F. Nacimiento</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->birthday ? \Carbon\Carbon::parse($seller->birthday)->format('d/m/Y') : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Email</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/9.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="email" value="{{ $seller->email ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Teléfono</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/10.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->phone ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Order ID</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="#VN{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Estado</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->status_text }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($seller->comment)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Comentarios</label>
                                            <div class="input-group input-group-merge group-form">
                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                </div>
                                                <input class="form-control" type="text" value="{{ $seller->comment }}" style="border-radius: 0 30px 30px 0;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection 