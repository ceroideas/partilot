@extends('layouts.layout')

@section('title','Escrutinio de Sorteo')

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
                        <li class="breadcrumb-item active">Escrutinio</li>
                    </ol>
                </div>
                <h4 class="page-title">Escrutinio de Sorteo</h4>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ri-alert-line me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('lottery.process-scrutiny', $lottery->id) }}" method="POST">
                        @csrf
                        
                        <h4 class="header-title">
                            Realizar Escrutinio
                            <span class="badge bg-warning float-end">PENDIENTE</span>
                        </h4>

                        <br>

                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="form-card bs" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">
                                        Datos del Sorteo y Administración
                                    </h4>
                                    <small><i>Verificar que los datos sean correctos antes de procesar</i></small>

                                    <div class="form-group mt-2 mb-3">

                                        <div class="row">

                                            <div class="col-4">

                                                <div style="width: 150px; height: 80px; border-radius: 8px; background-color: silver; float: left; margin-right: 20px;">
                                                    @if($lottery->image)
                                                        <img src="{{ url('storage/' . $lottery->image) }}" alt="Sorteo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
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
                                                    @php
                                                        // Usar los datos del resumen calculado en el controlador
                                                        $summary = $scrutinyData['summary'] ?? null;
                                                        if ($summary) {
                                                            $totalWinning = $summary['unique_winning_numbers'];
                                                            $totalNonWinning = $summary['unique_non_winning_numbers'];
                                                            $totalPrizeAmount = $summary['total_prize_amount'];
                                                        } else {
                                                            // Fallback al cálculo anterior si no hay resumen
                                                            $totalWinning = 0;
                                                            $totalNonWinning = 0;
                                                            $totalPrizeAmount = 0;
                                                            
                                                            foreach($scrutinyData['entities'] ?? $scrutinyData as $data) {
                                                                $totalWinning += $data['result']->total_winning;
                                                                $totalNonWinning += count($data['result']->reserved_numbers) - $data['result']->total_winning;
                                                                $totalPrizeAmount += $data['result']->total_prize_amount;
                                                            }
                                                        }
                                                    @endphp
                                                    Participaciones Asignadas Premiadas: <b>{{ $totalWinning }} Números</b> <br>
                                                    Participaciones Asignadas No Premiadas: <b>{{ $totalNonWinning }} Números</b> <br>
                                                    Importe Premios Repartidos: <b>{{ number_format($totalPrizeAmount, 2) }}€</b>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <h4 class="mb-0 mt-1">
                                        Lista de entidades y sus participaciones asignadas
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
                                                @forelse(($scrutinyData['entities'] ?? $scrutinyData) as $data)
                                                    @php
                                                        $entity = $data['entity'];
                                                        $result = $data['result'];
                                                        $prizeBreakdown = $result->prize_breakdown;
                                                    @endphp
                                                    <tr>
                                                        <td><b>{{ $entity->name }}</b></td>

                                                        <td>
                                                            Emitidas: <b>{{ $result->total_issued }}</b> <br>
                                                            Vendidas: <b>{{ $result->total_reserved }}</b> <br>
                                                            Devueltas: <b>{{ $result->total_returned }}</b> <br>
                                                            <span class="badge bg-{{ $result->total_winning > 0 ? 'success' : 'secondary' }}">
                                                                Premiadas: {{ $result->total_winning }} Números
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <b>{{ number_format($result->total_prize_amount, 2) }}€</b>
                                                        </td>
                                                        <td>
                                                            <b>{{ number_format($result->prize_per_participation, 2) }}€</b>
                                                        </td>
                                                    </tr>
                                                    
                                                    @if($result->total_winning > 0)
                                                        {{-- Primer Premio --}}
                                                        @if(!empty($prizeBreakdown['primer_premio']['numbers']))
                                                            <tr>
                                                                <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                                    <b>Número: {{ implode(', ', $prizeBreakdown['primer_premio']['numbers']) }} - Premiado con {{ number_format($prizeBreakdown['primer_premio']['prize_per_ticket'], 0) }}€ × {{ $prizeBreakdown['primer_premio']['total_tickets'] }} décimos = {{ number_format($prizeBreakdown['primer_premio']['total_amount'], 0) }}€</b>
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        {{-- Segundo Premio --}}
                                                        @if(!empty($prizeBreakdown['segundo_premio']['numbers']))
                                                            <tr>
                                                                <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                                    <b>Segundo Premio: {{ implode(', ', $prizeBreakdown['segundo_premio']['numbers']) }} - {{ number_format($prizeBreakdown['segundo_premio']['amount'], 2) }}€</b>
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        {{-- Terceros Premios --}}
                                                        @if(!empty($prizeBreakdown['terceros_premios']['numbers']))
                                                            <tr>
                                                                <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                                    <b>Terceros Premios: {{ implode(', ', $prizeBreakdown['terceros_premios']['numbers']) }} - {{ number_format($prizeBreakdown['terceros_premios']['amount'], 2) }}€</b>
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        {{-- Reintegros --}}
                                                        @if(!empty($prizeBreakdown['reintegros']['numbers']))
                                                            <tr>
                                                                <td colspan="4" style="border-bottom: 1px solid #333; background-color: #f8f9fa;">
                                                                    <b>Reintegros: {{ count($prizeBreakdown['reintegros']['numbers']) }} números - {{ number_format($prizeBreakdown['reintegros']['amount'], 2) }}€</b>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif

                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <div class="alert alert-info">
                                                            <i class="ri-information-line me-2"></i>
                                                            No hay entidades con reservas para este sorteo
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                            
                                        </table>

                                    </div>

                                    <div class="mt-3">
                                        <label for="comments" class="form-label">Comentarios del escrutinio (opcional):</label>
                                        <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Ingrese cualquier observación o comentario sobre el escrutinio..."></textarea>
                                    </div>

                                    <div class="row">

                                        <div class="col-6 text-start">
                                            <a href="{{route('lottery.results')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Cancelar</span></a>
                                        </div>
                                        
                                        <div class="col-6 text-end">
                                            <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-warning mt-2">Procesar Escrutinio
                                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>

                    </form>
                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>
// Confirmación antes de procesar el escrutinio
document.querySelector('form').addEventListener('submit', function(e) {
    if (!confirm('¿Está seguro de que desea procesar el escrutinio? Esta acción no se puede deshacer.')) {
        e.preventDefault();
    }
});
</script>

@endsection