@extends('layouts.layout')

@section('title','Sorteos')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item active">Resultados</li>
                    </ol>
                </div>
                <h4 class="page-title">Sorteos</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div>
                    	<div class="form-group mt-2 mb-3 admin-box bs">

            				<div class="row">
            					<div class="col-1">
            						
                    				<div class="photo-preview-2">
                    					
                    					<i class="ri-account-circle-fill"></i>

                    				</div>
                    				
                    				<div style="clear: both;"></div>
            					</div>

            					<div class="col-4 text-center">

            						<h4 class="mt-0 mb-0">El Buho Lotero</h4>

            						<small>Jorge Ruiz Ortega</small> <br>

            						<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> 05716
            						
            					</div>

            					<div class="col-4">

            						<div class="mt-2">
            							Provincia: La Rioja <br>
            							Dirección: Avd. Club Deportivo 28
            						</div>
            						
            					</div>

            					<div class="col-3">

            						<div class="mt-2">
            							Ciudad: Logroño <br>
            							Tel: 941 900 900
            						</div>
            						
            					</div>
            				</div>
            			</div>
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Número">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Tipo de Sorteo">
                                <input type="text" class="form-control" placeholder="Escrutado">
                            </div>

                            <a href="{{url('lottery/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <table id="example2" class="table table-striped nowrap w-100">
                            <thead class="filters">
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha Sorteo</th>
                                    <th>1º</th>
                                    <th>2º</th>
                                    <th>3º</th>
                                    <th>Fracción</th>
                                    <th>Serie</th>
                                    <th>Reintegro</th>
                                    <th>Escrutado</th>
                                    <th>Tipo de Sorteo</th>
                                    <th class="no-filter"></th>
                                </tr>
                            </thead>
                        
                        
                            <tbody>
                                <tr>
                                    <td>46/25</td>
                                    <td>07/06/2025</td>
                                    <td>18025</td>
                                    <td>71634</td>
                                    <td></td>
                                    <td>7</td>
                                    <td>9</td>
                                    <td>6-7</td>
                                    <td><label class="badge bg-danger">NO</label></td>
                                    <td>Sorteo Extraordinario 15€ Especial</td>
                                    <td class="text-end">
                                        <a href="{{url('lottery/scrutiny')}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/escrutinio.svg')}}" alt="" width="12"></a>
                                        <a href="#" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                        <a class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>45/25</td>
                                    <td>07/06/2025</td>
                                    <td>88905</td>
                                    <td>30316</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>6-8</td>
                                    <td><label class="badge bg-success">SI</label></td>
                                    <td>Sorteo Jueves</td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                        <a class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>44/25</td>
                                    <td>07/06/2025</td>
                                    <td>81322</td>
                                    <td>94836</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>4-8</td>
                                    <td><label class="badge bg-success">SI</label></td>
                                    <td>Sorteo Sábado</td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                        <a class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <br>

                        <a href="{{url('lottery_types?table=1')}}" style="border-radius: 30px; width: 180px; top: -12px; left: -12px; position: relative;" class="btn btn-md btn-dark">
                            <img src="{{url('icons/tipos_sorteos.svg')}}" alt="" width="18px" style="position: relative; top: -1px;">
                         Tipos de Sorteo</a>

                         <a href="{{url('lottery/administrations')}}" style="border-radius: 30px; width: 180px; top: -12px; left: -12px; position: relative; background-color: #e78307;" class="btn btn-md btn-light">
                            <img src="{{url('assets/form-groups/results.svg')}}" alt="" width="18px" style="position: relative; top: -1px;">
                         Lista Resultados</a>

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

</script>

@endsection