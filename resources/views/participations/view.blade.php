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
                                                            <input class="form-control" type="text" value="{{ $participation->seller_id && $participation->status == 'disponible' ? 'Asignada' : ucfirst($participation->status ?? 'N/A') }}" style="border-radius: 0 30px 30px 0;" readonly>
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
                                                                     <input class="form-control" type="text" value="{{ $participation->status == 'vendida' ? 'Pagado' : (($participation->seller_id && $participation->status == 'disponible') || $participation->status == 'disponible' ? 'Pendiente' : 'N/A') }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                             @endif

                                            <!-- Historial de Actividades -->
                                            <div class="row show-content mt-4">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0">
                                                            <i class="mdi mdi-timeline-clock"></i> Historial de Actividades
                                                        </h5>
                                                        <button class="btn btn-sm btn-light" onclick="refreshActivityHistory()" title="Actualizar historial">
                                                            <i class="mdi mdi-refresh"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Loading state -->
                                                    <div id="activity-loading" class="text-center py-4">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Cargando...</span>
                                                        </div>
                                                        <p class="text-muted mt-2 mb-0">Cargando historial de actividades...</p>
                                                    </div>

                                                    <!-- No activities state -->
                                                    <div id="no-activities" class="text-center py-4" style="display: none;">
                                                        <i class="mdi mdi-information-outline" style="font-size: 48px; color: #ccc;"></i>
                                                        <p class="text-muted mt-2">No hay actividades registradas para esta participación</p>
                                                    </div>

                                                    <!-- Error state -->
                                                    <div id="activity-error" class="text-center py-4" style="display: none;">
                                                        <i class="mdi mdi-alert-circle-outline" style="font-size: 48px; color: #dc3545;"></i>
                                                        <p class="text-danger mt-2">Error al cargar el historial de actividades</p>
                                                    </div>

                                                    <!-- Activities list -->
                                                    <div id="activity-timeline" style="display: none;">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 150px;">Tipo</th>
                                                                        <th>Descripción</th>
                                                                        <th style="width: 120px;">Usuario</th>
                                                                        <th style="width: 120px;">Vendedor</th>
                                                                        <th style="width: 160px;">Fecha/Hora</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="activity-tbody">
                                                                    <!-- Las actividades se cargarán aquí -->
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

</div>

@endsection

@section('scripts')
<script>
    const participationId = {{ $participation->id }};

    // Cargar el historial al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        loadActivityHistory();
    });

    function loadActivityHistory() {
        // Mostrar loading
        document.getElementById('activity-loading').style.display = 'block';
        document.getElementById('activity-timeline').style.display = 'none';
        document.getElementById('no-activities').style.display = 'none';
        document.getElementById('activity-error').style.display = 'none';

        fetch(`/participations/${participationId}/history`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('activity-loading').style.display = 'none';

                if (data.success && data.activities && data.activities.length > 0) {
                    renderActivities(data.activities);
                    document.getElementById('activity-timeline').style.display = 'block';
                } else {
                    document.getElementById('no-activities').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error al cargar historial:', error);
                document.getElementById('activity-loading').style.display = 'none';
                document.getElementById('activity-error').style.display = 'block';
            });
    }

    function renderActivities(activities) {
        const tbody = document.getElementById('activity-tbody');
        tbody.innerHTML = '';

        activities.forEach((activity, index) => {
            const row = createActivityRow(activity);
            tbody.appendChild(row);
        });
    }

    function createActivityRow(activity) {
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.title = 'Click para ver detalles';
        
        // Tipo de actividad
        const tdType = document.createElement('td');
        tdType.innerHTML = `<span class="badge ${activity.activity_badge}">${activity.activity_type_text}</span>`;
        tr.appendChild(tdType);

        // Descripción
        const tdDesc = document.createElement('td');
        tdDesc.innerHTML = `
            <div>${activity.description}</div>
            ${activity.old_status && activity.new_status ? 
                `<small class="text-muted">
                    <span class="badge bg-secondary">${activity.old_status}</span> 
                    <i class="mdi mdi-arrow-right"></i> 
                    <span class="badge bg-primary">${activity.new_status}</span>
                </small>` : ''
            }
            ${activity.old_seller && activity.new_seller ? 
                `<small class="text-muted d-block mt-1">
                    <i class="mdi mdi-account-switch"></i> ${activity.old_seller} → ${activity.new_seller}
                </small>` : ''
            }
        `;
        tr.appendChild(tdDesc);

        // Usuario
        const tdUser = document.createElement('td');
        tdUser.innerHTML = `<small>${activity.user || 'Sistema'}</small>`;
        tr.appendChild(tdUser);

        // Vendedor
        const tdSeller = document.createElement('td');
        if (activity.seller || activity.new_seller || activity.old_seller) {
            const sellerName = activity.seller || activity.new_seller || activity.old_seller;
            tdSeller.innerHTML = `<small>${sellerName}</small>`;
        } else {
            tdSeller.innerHTML = `<small class="text-muted">-</small>`;
        }
        tr.appendChild(tdSeller);

        // Fecha y hora
        const tdDate = document.createElement('td');
        tdDate.innerHTML = `
            <small>
                <i class="mdi mdi-clock-outline"></i> ${activity.created_at}
            </small>
        `;
        tr.appendChild(tdDate);

        // Click para mostrar detalles
        tr.addEventListener('click', function() {
            showActivityDetails(activity);
        });

        return tr;
    }

    function showActivityDetails(activity) {
        let detailsHtml = `
            <div class="mb-3">
                <h6><span class="badge ${activity.activity_badge}">${activity.activity_type_text}</span></h6>
                <p class="mb-2">${activity.description}</p>
            </div>
        `;

        if (activity.metadata && Object.keys(activity.metadata).length > 0) {
            detailsHtml += '<div class="mb-2"><strong>Información adicional:</strong></div>';
            detailsHtml += '<div class="table-responsive"><table class="table table-sm table-bordered">';
            
            for (const [key, value] of Object.entries(activity.metadata)) {
                if (value !== null && value !== undefined && value !== '') {
                    const formattedKey = formatMetadataKey(key);
                    const formattedValue = formatMetadataValue(value);
                    detailsHtml += `
                        <tr>
                            <td style="width: 40%;"><strong>${formattedKey}</strong></td>
                            <td>${formattedValue}</td>
                        </tr>
                    `;
                }
            }
            
            detailsHtml += '</table></div>';
        }

        if (activity.ip_address) {
            detailsHtml += `<div class="mt-2"><small class="text-muted"><i class="mdi mdi-ip"></i> IP: <code>${activity.ip_address}</code></small></div>`;
        }

        // Mostrar en un modal usando SweetAlert2 o alert nativo
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Detalles de la Actividad',
                html: detailsHtml,
                width: 600,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#333'
            });
        } else {
            // Fallback a alert simple si no hay SweetAlert2
            alert('Detalles de la actividad:\n\n' + activity.description + '\n\nUsuario: ' + (activity.user || 'Sistema'));
        }
    }

    function formatMetadataKey(key) {
        const translations = {
            'participation_code': 'Código Participación',
            'book_number': 'Número de Taco',
            'set_id': 'ID Set',
            'design_format_id': 'ID Formato',
            'cancellation_reason': 'Razón de Anulación',
            'return_reason': 'Razón de Devolución',
            'sale_amount': 'Importe de Venta',
            'buyer_name': 'Nombre Comprador',
            'buyer_phone': 'Teléfono Comprador',
            'buyer_email': 'Email Comprador',
            'buyer_nif': 'NIF Comprador',
            'status': 'Estado',
            'seller_id': 'ID Vendedor',
            'old_status': 'Estado Anterior',
            'new_status': 'Estado Nuevo'
        };
        return translations[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatMetadataValue(value) {
        if (typeof value === 'object' && value !== null) {
            return '<pre style="font-size: 0.85em; margin: 0;">' + JSON.stringify(value, null, 2) + '</pre>';
        }
        return value;
    }

    function refreshActivityHistory() {
        loadActivityHistory();
    }
</script>
@endsection
