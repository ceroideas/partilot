@extends('layouts.layout')

@section('title','Reservas')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Reservas</li>
                    </ol>
                </div>
                <h4 class="page-title">Reservas</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="{{$reserves->count() > 0 ? '' : 'd-none'}}">
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                <input type="text" class="form-control" placeholder="Status">
                            </div>

                            <a href="{{url('reserves/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <table id="example2" class="table table-striped nowrap w-100">
                            <thead class="filters">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Entidad</th>
                                    <th>Provincia</th>
                                    <th>N.Sorteo</th>
                                    <th>Fecha Sorteo</th>
                                    <th>Nombre Sorteo</th>
                                    <th>Números</th>
                                    <th>Importe (Números)</th>
                                    <th>Décimos (Números)</th>
                                    <th>Importe Total</th>
                                    <th class="no-filter"></th>
                                </tr>
                            </thead>
                        
                        
                            <tbody>
                                @foreach($reserves as $reserve)
                                <tr>
                                    <td><a href="{{url('reserves/view', $reserve->id)}}">#RS{{str_pad($reserve->id, 4, '0', STR_PAD_LEFT)}}</a></td>
                                    <td>{{$reserve->entity->name ?? 'Sin entidad'}}</td>
                                    <td>{{$reserve->entity->province ?? 'Sin provincia'}}</td>
                                    <td>{{$reserve->lottery->name ?? 'Sin sorteo'}}</td>
                                    <td>{{$reserve->lottery->draw_date ? \Carbon\Carbon::parse($reserve->lottery->draw_date)->format('d/m/Y') : 'No definida'}}</td>
                                    <td>{{$reserve->lottery->description ?? 'Sin descripción'}}</td>
                                    <td>
                                        @if($reserve->reservation_numbers)
                                            @foreach($reserve->reservation_numbers as $number)
                                                {{$number}}{{!$loop->last ? ' - ' : ''}}
                                            @endforeach
                                        @else
                                            <span class="text-muted">Sin números</span>
                                        @endif
                                    </td>
                                    <td>{{number_format($reserve->reservation_amount ?? 0, 2)}}€</td>
                                    <td>{{$reserve->reservation_tickets ?? 0}}</td>
                                    <td><b>{{number_format($reserve->total_amount, 2)}}€</b></td>
                                    <td>
                                        <a class="btn btn-sm btn-light"><img src="{{url('icons/participations.svg')}}" alt="" width="12"></a>
                                        <a href="{{url('reserves/edit', $reserve->id)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{$reserve->id}}" data-name="reserva #{{$reserve->id}}"><i class="ri-delete-bin-6-line"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div class="{{$reserves->count() > 0 ? 'd-none' : ''}}">
                        <div class="d-flex align-items-center gap-1">
                            
                            <div class="empty-tables">

                                <div>
                                    <img src="{{url('icons/reservas.svg')}}" alt="" width="80px">
                                </div>

                                <h3 class="mb-0">No hay Reservas</h3>

                                <small>Añade Reservas</small>

                                <br>

                                <a href="{{url('reserves/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
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

  // Eliminar reserva
  $('.delete-btn').on('click', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    
    if (confirm('¿Estás seguro de que quieres eliminar la reserva de "' + name + '"?')) {
      window.location.href = '{{url("reserves/delete")}}/' + id;
    }
  });

</script>

@endsection