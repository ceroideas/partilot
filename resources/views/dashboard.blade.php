@extends('layouts.layout')

@section('title','Dashboard - PARTILOT')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Inicio</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Bienvenido al Panel de Administración</h4>
                    <p class="text-muted">Has iniciado sesión correctamente como: <strong>{{ Auth::user()->name }}</strong></p>
                    
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Entidades</h5>
                                    <p class="card-text">Gestionar entidades</p>
                                    <a href="{{ url('entities') }}" class="btn btn-light btn-sm">Ver Entidades</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Loterías</h5>
                                    <p class="card-text">Gestionar loterías</p>
                                    <a href="{{ url('lottery') }}" class="btn btn-light btn-sm">Ver Loterías</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Reservas</h5>
                                    <p class="card-text">Gestionar reservas</p>
                                    <a href="{{ url('reserves') }}" class="btn btn-light btn-sm">Ver Reservas</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Sets</h5>
                                    <p class="card-text">Gestionar sets</p>
                                    <a href="{{ url('sets') }}" class="btn btn-light btn-sm">Ver Sets</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="{{ url('logout') }}" class="btn btn-danger">
                                <i class="ri-logout-box-r-line"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->

@endsection 