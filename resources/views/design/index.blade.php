@extends('layouts.layout')

@section('title','Diseño e Impresión')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Diseño e Impresión</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e Impresión</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="{{count($designs) ? '' : 'd-none'}}">
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Entidad">
                                <input type="text" class="form-control" placeholder="Status">
                            </div>

                            <a href="{{url('design/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <table id="example2" class="table table-striped nowrap w-100">
                            <thead class="filters">
                                <tr>
                                    <th>Order ID</th>
                                    <th>N.Set</th>
                                    <th>Nombre Set</th>
                                    <th>N.Sorteo</th>
                                    <th>Fecha Sorteo</th>
                                    <th>Número/s</th>
                                    <th>Nº Particip.</th>
                                    <th>Provincia</th>
                                    <th>Localidad</th>
                                    <th>Status</th>
                                    <th class="no-filter"></th>
                                </tr>
                            </thead>
                        
                        
                            <tbody>
                            @foreach($designs as $design)
                            <tr class="row-clickable" data-href="{{ route('design.editFormat', $design->id) }}" style="cursor: pointer;">
                                <td><a href="{{ route('design.editFormat', $design->id) }}">#DS{{ str_pad($design->id,5,'0',STR_PAD_LEFT) }}</a></td>
                                <td>{{ $design->set ? $design->set->id : '-' }}</td>
                                <td>{{ $design->set ? $design->set->set_name : '-' }}</td>
                                <td>{{ $design->lottery ? $design->lottery->name : '-' }}</td>
                                <td>{{ $design->lottery ? ($design->lottery->draw_date->format('d-m-Y') ?? $design->lottery->deadline_date) : '-' }}</td>
                                <td>
                                    @if($design->set && $design->set->reserve && $design->set->reserve->reservation_numbers)
                                        {{ is_array($design->set->reserve->reservation_numbers) ? implode(' - ', $design->set->reserve->reservation_numbers) : $design->set->reserve->reservation_numbers }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $design->set ? $design->set->total_participations : '-' }}</td>
                                <td>{{ $design->entity ? $design->entity->province : '-' }}</td>
                                <td>{{ $design->entity ? $design->entity->city : '-' }}</td>
                                <td><label class="badge bg-success">Pendiente</label></td>
                                <td class="no-click" style="cursor: default;">
                                    <a href="{{ route('design.editFormat', $design->id) }}" class="btn btn-sm btn-light" title="Editar diseño"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                    @php
                                        $hasCover = !empty($design->cover_html);
                                        $hasBack = !empty($design->back_html);
                                    @endphp
                                    @if($hasCover && $hasBack)
                                    <a target="_blank" href="{{ route('design.exportCoverAndBackPdf', $design->id) }}" class="btn btn-sm btn-light" title="Descargar PDF de portada y trasera"><i class="ri-file-pdf-line"></i></a>
                                    @endif
                                    <a target="_blank" href="{{ url('design/pdf/participation', $design->id) }}" class="btn btn-sm btn-light" title="Descargar PDF de participaciones"><img src="{{url('printer.svg')}}" alt="" width="12"></a>
                                    {{-- <a href="{{ route('design.editFormat', $design->id) }}" class="btn btn-sm btn-light"><img src="{{url('assets/design_1.svg')}}" alt="" width="12"></a> --}}
                                    <a href="#" class="btn btn-sm btn-danger delete-design" data-design-id="{{ $design->id }}" data-design-name="{{ $design->set ? $design->set->set_name : 'Diseño #' . $design->id }}" title="Eliminar diseño"><i class="ri-delete-bin-6-line"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>

                        <br>

                    </div>

                    <div class="{{count($designs) ? 'd-none' : ''}}">
                        <div class="d-flex align-items-center gap-1">

                            
                            <div class="empty-tables">

                                <div>
                                    <img src="{{url('icons_/diseno.svg')}}" alt="" width="80px">
                                </div>

                                <h3 class="mb-0">No hay Diseños</h3>

                                <small>Añade Diseños</small>

                                <br>

                                <a href="{{url('design/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
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
  
  // Hacer las filas clickeables (excepto la última columna de acciones)
  $(document).on('click', '#example2 tbody tr.row-clickable', function(e) {
    // No activar si se hace clic en la última columna o en sus elementos
    if ($(e.target).closest('td.no-click').length || $(e.target).closest('td.no-click').length) {
      return;
    }
    
    // No activar si se hace clic directamente en un enlace o botón
    if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('a').length || $(e.target).closest('button').length) {
      return;
    }
    
    // Redirigir a la URL de la fila
    var href = $(this).data('href');
    if (href) {
      window.location.href = href;
    }
  });
  
  // Agregar efecto hover visual
  $(document).on('mouseenter', '#example2 tbody tr.row-clickable', function() {
    $(this).css('background-color', '#f8f9fa');
  }).on('mouseleave', '#example2 tbody tr.row-clickable', function() {
    $(this).css('background-color', '');
  });

  // Eliminar diseño/trabajo
  $(document).on('click', '.delete-design', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const designId = $(this).data('design-id');
    const designName = $(this).data('design-name');
    
    const confirmMessage = '¿Está seguro de eliminar este trabajo de diseño?\n\n' +
                          'Trabajo: ' + designName + '\n\n' +
                          'Esta acción eliminará el diseño y todas las participaciones asociadas.\n' +
                          'Si hay participaciones vendidas, no se podrá eliminar.\n\n' +
                          'Esta acción no se puede deshacer.';
    
    if (!confirm(confirmMessage)) {
      return false;
    }
    
    // Crear formulario para enviar DELETE request
    const form = $('<form>', {
      'method': 'POST',
      'action': '{{ url("design/format") }}/' + designId
    });
    
    form.append($('<input>', {
      'type': 'hidden',
      'name': '_token',
      'value': '{{ csrf_token() }}'
    }));
    
    form.append($('<input>', {
      'type': 'hidden',
      'name': '_method',
      'value': 'DELETE'
    }));
    
    $('body').append(form);
    form.submit();
  });

</script>

@endsection