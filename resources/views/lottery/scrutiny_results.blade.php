@extends('layouts.layout')

@section('title','Resultados del Escrutinio')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lottery.results') }}">Resultados</a></li>
                        <li class="breadcrumb-item active">Escrutinio Realizado</li>
                    </ol>
                </div>
                <h4 class="page-title">Escrutinio de Sorteo</h4>
            </div>
        </div>
    </div>
{{-- 
    @if(session('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif --}}

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">
                        Escrutinio Completado
                        <span class="badge bg-success float-end">ESCRUTADO</span>
                    </h4>

                    <br>

                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-card bs" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">
                                    Resultados del Escrutinio
                                </h4>
                                <small><i>Escrutinio realizado el {{ $scrutiny->scrutiny_date->format('d/m/Y H:i') }}</i></small>

                                <div class="form-group mt-2 mb-3">

                                    <div class="row">

                                        <div class="col-4">

                                            <div class="photo-preview" style="width: 200px; background-image: url({{ $lottery->image ? url('uploads/' . $lottery->image) : '' }});">
                                                @if(!$lottery->image)
                                                    <i class="ri-image-add-line"></i>
                                                @endif
                                            </div>

                                            <div style="float: left; margin-top: .5rem">
                                                Sorteo: {{ $lottery->name }} <br>
                                                
                                                <h4 class="mt-0 mb-0">
                                                    {{ $lottery->description ?? $lottery->lotteryType->name }}
                                                </h4>

                                            </div>

                                            <div class="clearfix"></div>
                                            
                                        </div>

                                        <div class="col-2">

                                            <div style="float: left; margin-top: .5rem">
                                                Fecha Sorteo <br>
                                                
                                                <h5 class="mb-0">
                                                   {{ $lottery->draw_date ? $lottery->draw_date->format('d/m/Y') : 'N/A' }}
                                                </h5>

                                            </div>
                                            
                                        </div>

                                        <div class="col-6">

                                                                                         <div class="mt-2">
                                                 Participaciones Premiadas: <b>{{ $scrutiny->scrutiny_summary['total_winning_participations'] ?? 0 }} Números</b> <br>
                                                 Participaciones No Premiadas: <b>{{ $scrutiny->scrutiny_summary['total_non_winning_participations'] ?? 0 }} Números</b> <br>
                                                 Importe Premios Repartidos: <b>{{ number_format($scrutiny->scrutiny_summary['total_prize_amount'] ?? 0, 2) }}€</b> <br>
                                                 <small><i>Escrutado por: {{ $scrutiny->scrutinizedBy->name ?? 'N/A' }}</i></small>
                                             </div>
                                            
                                        </div>
                                    </div>
                                    
                                </div>

                                <h4 class="mb-0 mt-1">
                                    Lista de participaciones por entidad
                                </h4>

                                <div style="min-height: 400px; height: 400px; overflow: auto;">

                                    <table class="table">

                                        <thead>
                                            <tr>
                                                <th>Entidad</th>
                                                <th class="text-center">Participaciones</th>
                                                <th class="text-center">Premio Total</th>
                                                <th class="text-center">Premio por Participación</th>
                                            </tr>
                                        </thead>

                                        <tbody class="text-center">
                                            @forelse($scrutiny->entityResults as $entityResult)
                                            <tr>
                                                <td><b>{{ $entityResult->entity->name }}</b></td>

                                                <td>
                                                    Reservadas: <b>{{ $entityResult->total_reserved }}</b> <br>
                                                    Vendidas: <b>{{ $entityResult->total_sold + ($entityResult->total_non_winning ?? 0) }}</b> <br>
                                                    Devueltas: <b>{{ $entityResult->total_returned }}</b> <br>
                                                                                                         <span class="badge bg-{{ $entityResult->total_winning > 0 ? 'success' : 'secondary' }}">
                                                         Premiadas: {{ $entityResult->total_winning }} Números
                                                     </span>
                                                </td>
                                                <td>
                                                    <b>{{ number_format($entityResult->total_prize_amount, 2) }}€</b>
                                                </td>
                                                <td>
                                                    <b>{{ number_format($entityResult->prize_per_participation, 2) }}€</b>
                                                </td>
                                            </tr>
                                            
                                                                                         @if($entityResult->total_winning > 0)
                                                @php
                                                    $prizeBreakdown = $entityResult->prize_breakdown;
                                                @endphp
                                                
                                                {{-- Premio Especial --}}
                                                @if(!empty($prizeBreakdown['otros_premios']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #fff3cd;">
                                                            <b>Premio Especial: {{ implode(', ', $prizeBreakdown['otros_premios']['numbers']) }} - {{ number_format($prizeBreakdown['otros_premios']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                                                                 {{-- Primer Premio --}}
                                                 @if(!empty($prizeBreakdown['primer_premio']['numbers']))
                                                     <tr>
                                                         <td colspan="4" style="border-bottom: 1px solid #333; background-color: #d1ecf1;">
                                                             <b>Número: {{ implode(', ', $prizeBreakdown['primer_premio']['numbers']) }} - Premiado con {{ number_format($prizeBreakdown['primer_premio']['prize_per_ticket'], 0) }}€ × {{ $prizeBreakdown['primer_premio']['total_tickets'] }} décimos = {{ number_format($prizeBreakdown['primer_premio']['total_amount'], 0) }}€</b>
                                                         </td>
                                                     </tr>
                                                 @endif

                                                {{-- Segundo Premio --}}
                                                @if(!empty($prizeBreakdown['segundo_premio']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #d1ecf1;">
                                                            <b>Segundo Premio: {{ implode(', ', $prizeBreakdown['segundo_premio']['numbers']) }} - {{ number_format($prizeBreakdown['segundo_premio']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Terceros Premios --}}
                                                @if(!empty($prizeBreakdown['terceros_premios']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                            <b>Terceros Premios: {{ implode(', ', $prizeBreakdown['terceros_premios']['numbers']) }} - {{ number_format($prizeBreakdown['terceros_premios']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Cuartos Premios --}}
                                                @if(!empty($prizeBreakdown['cuartos_premios']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                            <b>Cuartos Premios: {{ implode(', ', $prizeBreakdown['cuartos_premios']['numbers']) }} - {{ number_format($prizeBreakdown['cuartos_premios']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Quintos Premios --}}
                                                @if(!empty($prizeBreakdown['quintos_premios']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                            <b>Quintos Premios: {{ implode(', ', $prizeBreakdown['quintos_premios']['numbers']) }} - {{ number_format($prizeBreakdown['quintos_premios']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Extracciones de Cinco Cifras --}}
                                                @if(!empty($prizeBreakdown['extracciones_cinco_cifras']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #e2e3e5;">
                                                            <b>Extracciones 5 Cifras: {{ count($prizeBreakdown['extracciones_cinco_cifras']['numbers']) }} números - {{ number_format($prizeBreakdown['extracciones_cinco_cifras']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Extracciones de Cuatro Cifras --}}
                                                @if(!empty($prizeBreakdown['extracciones_cuatro_cifras']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #e2e3e5;">
                                                            <b>Extracciones 4 Cifras: {{ count($prizeBreakdown['extracciones_cuatro_cifras']['numbers']) }} números - {{ number_format($prizeBreakdown['extracciones_cuatro_cifras']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Extracciones de Tres Cifras --}}
                                                @if(!empty($prizeBreakdown['extracciones_tres_cifras']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #e2e3e5;">
                                                            <b>Extracciones 3 Cifras: {{ count($prizeBreakdown['extracciones_tres_cifras']['numbers']) }} números - {{ number_format($prizeBreakdown['extracciones_tres_cifras']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Extracciones de Dos Cifras --}}
                                                @if(!empty($prizeBreakdown['extracciones_dos_cifras']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #e2e3e5;">
                                                            <b>Extracciones 2 Cifras: {{ count($prizeBreakdown['extracciones_dos_cifras']['numbers']) }} números - {{ number_format($prizeBreakdown['extracciones_dos_cifras']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Reintegros --}}
                                                @if(!empty($prizeBreakdown['reintegros']['numbers']))
                                                    <tr>
                                                        <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                            <b>Reintegros: {{ count($prizeBreakdown['reintegros']['numbers']) }} números - {{ number_format($prizeBreakdown['reintegros']['total_amount'], 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif

                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <div class="alert alert-info">
                                                        <i class="ri-information-line me-2"></i>
                                                        No hay resultados de entidades para mostrar
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        
                                    </table>

                                </div>

                                @if($scrutiny->comments)
                                <div class="mt-3">
                                    <h5>Comentarios del escrutinio:</h5>
                                    <div class="alert alert-light">
                                        {{ $scrutiny->comments }}
                                    </div>
                                </div>
                                @endif

                                <div class="row">

                                    <div class="col-6 text-start">
                                        <a href="{{route('lottery.results')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                            <i style="top: 5px; left: 10%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Volver a Resultados</span></a>
                                    </div>
                                    
                                    <div class="col-6 text-end">
                                        <button onclick="window.print()" style="border-radius: 30px; width: 200px; background-color: #17a2b8; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-info mt-2">Imprimir Escrutinio
                                            <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-printer-line"></i></button>
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
// Funcionalidad adicional para la vista de resultados
</script>

@endsection
