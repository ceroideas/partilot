@extends('layouts.layout')

@section('title','Sets de Participaciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Sets de Participaciones</li>
                    </ol>
                </div>
                <h4 class="page-title">Sets de Participaciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="{{$sets->count() > 0 ? '' : 'd-none'}}">
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                <input type="text" class="form-control" placeholder="Status">
                            </div>

                            <a href="{{url('sets/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <table id="example2" class="table table-striped nowrap w-100">
                            <thead class="filters">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Nombre Set</th>
                                    <th>N.Sorteo</th>
                                    <th>Número/s</th>
                                    <th>Importe Jugado (por Número)</th>
                                    <th>Importe Donativo</th>
                                    <th>Importe Total</th>
                                    <th>Participaciones TOTAL</th>
                                    <th>Entidad</th>
                                    <th>Provincia</th>
                                    <th class="no-filter"></th>
                                </tr>
                            </thead>
                        
                        
                            <tbody>
                                @foreach($sets as $set)
                                <tr>
                                    <td><a href="{{url('sets/view', $set->id)}}">#SP{{str_pad($set->id, 4, '0', STR_PAD_LEFT)}}</a></td>
                                    <td>{{$set->set_name}}</td>
                                    <td>{{$set->reserve->lottery ? $set->reserve->lottery->name : 'Sin sorteo'}}</td>
                                    <td>
                                        @if($set->reserve->reservation_numbers)
                                            @foreach($set->reserve->reservation_numbers as $number)
                                                {{$number}}{{!$loop->last ? ' - ' : ''}}
                                            @endforeach
                                        @else
                                            <span class="text-muted">Sin números</span>
                                        @endif
                                    </td>
                                    <td>{{number_format($set->played_amount ?? 0, 2)}}€</td>
                                    <td>{{number_format($set->donation_amount ?? 0, 2)}}€</td>
                                    <td><b>{{number_format($set->total_amount, 2)}}€</b></td>
                                    <td>{{$set->total_participations}}</td>
                                    <td>{{$set->entity->name ?? 'Sin entidad'}}</td>
                                    <td>{{$set->entity->province ?? 'Sin provincia'}}</td>
                                    <td>
                                        <a href="{{url('sets/download-xml', $set->id)}}" class="btn btn-sm btn-light" title="Descargar XML"><img src="{{url('icons/diseno.svg')}}" alt="" width="12"></a>
                                        <a href="{{url('sets/edit', $set->id)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{$set->id}}" data-name="set #{{$set->id}}"><i class="ri-delete-bin-6-line"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div class="{{$sets->count() > 0 ? 'd-none' : ''}}">
                        <div class="d-flex align-items-center gap-1">
                            
                            <div class="empty-tables">

                                <div>
                                    <img src="{{url('icons/sets.svg')}}" alt="" width="80px" style="margin-top: 10px;">
                                </div>

                                <h3 class="mb-0">No hay Sets de Participaciones</h3>

                                <small>Añade Sets</small>

                                <br>

                                <a href="{{url('sets/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
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
                            //         console.log(val)
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

</script>

@endsection