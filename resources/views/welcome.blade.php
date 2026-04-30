@extends('layouts.layout')

@section('title','Panel')

@section('styles')
<style>
    .dashboard-panel {
        padding-bottom: 22px;
    }
    .dashboard-panel .page-title-box {
        margin-bottom: 12px;
    }
    .dashboard-panel .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2430;
        margin: 0;
    }
    .dashboard-panel .metric-card.card,
    .dashboard-panel .panel-card.card {
        border: 1px solid #d9dde8 !important;
        border-radius: 18px !important;
        box-shadow: none;
        background: #fff;
    }

    .content-page .content .container-fluid > .row > [class*="col-"] > .metric-card.card,
    .content-page .content .container-fluid > .row > [class*="col-"] > .panel-card.card {
        border-bottom: 1px solid #d9dde8 !important;
    }
    .dashboard-panel .metric-card .card-body {
        padding: 12px 14px;
    }
    .dashboard-panel .metric-title {
        font-size: .75rem;
        font-weight: 700;
        color: #2f3545;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .dashboard-panel .metric-value {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1.05;
        color: #1f2430;
        margin-bottom: 4px;
    }
    .dashboard-panel .metric-note {
        font-size: .78rem;
        color: #6f778a;
        margin-bottom: 8px;
    }
    .dashboard-panel .metric-wave {
        height: 18px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(252, 185, 65, .15) 0%, rgba(252, 185, 65, .7) 60%, rgba(252, 185, 65, .1) 100%);
    }
    .dashboard-panel .metric-wave.blue {
        background: linear-gradient(90deg, rgba(52, 124, 238, .15) 0%, rgba(52, 124, 238, .68) 60%, rgba(52, 124, 238, .12) 100%);
    }
    .dashboard-panel .metric-wave.red {
        background: linear-gradient(90deg, rgba(219, 72, 95, .15) 0%, rgba(219, 72, 95, .68) 60%, rgba(219, 72, 95, .12) 100%);
    }
    .dashboard-panel .metric-wave.green {
        background: linear-gradient(90deg, rgba(136, 186, 41, .15) 0%, rgba(136, 186, 41, .68) 60%, rgba(136, 186, 41, .12) 100%);
    }
    .dashboard-panel .panel-card {
        min-height: 260px;
    }
    .dashboard-panel .panel-card .card-body {
        padding: 14px 16px;
    }
    .dashboard-panel .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .dashboard-panel .panel-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #202635;
        margin: 0;
    }
    .dashboard-panel .panel-subtitle {
        font-size: .8rem;
        color: #7a8194;
        margin: 0;
    }
    .dashboard-panel .panel-link {
        font-size: .78rem;
        color: #4a5266;
        border: 1px solid #e0e4ef;
        border-radius: 999px;
        padding: 4px 10px;
        text-decoration: none;
        background: #fff;
    }
    .dashboard-panel .users-table {
        margin: 0;
        font-size: .83rem;
    }
    .dashboard-panel .users-table td {
        border-bottom: 1px solid #eef1f6;
        color: #3b4356;
        padding: 8px 0;
    }
    .dashboard-panel .users-table tr:last-child td {
        border-bottom: 0;
    }
    .dashboard-panel .panel-empty {
        min-height: 170px;
        border: 1px dashed #e6e9f2;
        border-radius: 12px;
        background: #fafbfd;
    }
    .content-page .footer {
        display: none !important;
    }
</style>
@endsection

@section('content')

<!-- Start Content-->
<div class="container-fluid dashboard-panel">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Panel</h4>
            </div>
        </div>
    </div>     

    <div class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="metric-card card">
                <div class="card-body">
                    <div class="metric-title">Total Usuarios</div>
                    <div class="metric-value">10.0K</div>
                    <div class="metric-note">+15,34% desde el mes pasado</div>
                    <div class="metric-wave"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card card">
                <div class="card-body">
                    <div class="metric-title">Total Entidades</div>
                    <div class="metric-value">100</div>
                    <div class="metric-note">+10,12% desde el mes pasado</div>
                    <div class="metric-wave blue"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card card">
                <div class="card-body">
                    <div class="metric-title">Total Vendedores</div>
                    <div class="metric-value">900</div>
                    <div class="metric-note">-5,44% desde el mes pasado</div>
                    <div class="metric-wave red"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card card">
                <div class="card-body">
                    <div class="metric-title">Total Participaciones</div>
                    <div class="metric-value">9.00M</div>
                    <div class="metric-note">+8,21% desde el mes pasado</div>
                    <div class="metric-wave green"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        
        <div class="col-xl-7">
            <div class="panel-card card">
                <div class="card-body">
                    <div class="panel-head">
                        <div>
                            <h5 class="panel-title">Entidades</h5>
                            <p class="panel-subtitle">Ultimas entidades registradas en PARTILOT</p>
                        </div>
                        <a href="{{ url('entities') }}" class="panel-link">Ver mas</a>
                    </div>
                    <div class="panel-empty"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="panel-card card">
                <div class="card-body">
                    <div class="panel-head">
                        <div>
                            <h5 class="panel-title">Usuarios</h5>
                            <p class="panel-subtitle">Ultimos usuarios registrados en PARTILOT</p>
                        </div>
                        <a href="{{ url('users') }}" class="panel-link">Ver mas</a>
                    </div>
                    <table class="table users-table">
                        <tbody>
                            <tr><td>#US9801</td><td>Jorge Ruiz Ortega</td><td>jorgeruiz@example.es</td></tr>
                            <tr><td>#US9802</td><td>El Gato Negro</td><td>CRM Admin pages</td></tr>
                            <tr><td>#US9803</td><td>La Marmita Dorada</td><td>Client Project</td></tr>
                            <tr><td>#US9804</td><td>Los Semillas de la Ilusion</td><td>Admin Dashboard</td></tr>
                            <tr><td>#US9805</td><td>El Duende Verde</td><td>App Landing Page</td></tr>
                            <tr><td>#US9801</td><td>La 13</td><td>Landing Page</td></tr>
                            <tr><td>#US9802</td><td>La Suertuda</td><td>CRM Admin pages</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1" style="height: auto !important;">
        <div class="col-12">
            <div class="panel-card card">
                <div class="card-body">
                    <div class="panel-head">
                        <div>
                            <h5 class="panel-title">Administraciones</h5>
                            <p class="panel-subtitle">Ultimas administraciones registradas en PARTILOT</p>
                        </div>
                        <a href="{{ url('administrations') }}" class="panel-link">Ver mas</a>
                    </div>
                    <div class="panel-empty"></div>
                </div>
            </div>
        </div>
    </div>
    
</div> <!-- container -->

@endsection