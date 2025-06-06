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
                <h4 class="page-title">Selección Administración</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                	<div>
	                    <h4 class="header-title">

	                    	{{-- <div class="float-start d-flex align-items-start">
	                    		<input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
	                    		<input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
	                    		<input type="text" class="form-control" placeholder="Status">
	                    	</div> --}}

	                    	<a href="{{url('administrations/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

	                    </h4>

	                    <div style="clear: both;"></div>

	                    <br>

	                    <table id="example2" class="table table-striped nowrap w-100">
	                        <thead class="filters">
	                            <tr>
	                                <th>Order ID</th>
	                                <th>Administración</th>
	                                <th>Nº Receptor</th>
	                                <th>Provincia</th>
	                                <th>Localidad</th>
	                                <th>Status</th>
	                            </tr>
	                        </thead>
	                    
	                    
	                        <tbody>
	                            <tr>
	                                <td>#AD9801</td>
	                                <td>El Buho Lotero</td>
	                                <td>06716</td>
	                                <td>La Rioja</td>
	                                <td>Logroño</td>
	                                <td><label class="badge bg-success">Activo</label></td>
	                            </tr>

	                            <tr>
	                                <td>#AD9801</td>
	                                <td>El Gato Nego</td>
	                                <td>06425</td>
	                                <td>Madrid</td>
	                                <td>Madrid</td>
	                                <td><label class="badge bg-success">Activo</label></td>
	                            </tr>
	                        </tbody>
	                    </table>

	                    <br>

	                    <div class="row">

            				<div class="col-6 text-start">
            					<a href="{{url('lottery?table=1')}}" style="border-radius: 30px; width: 200px; padding: 8px; font-weight: bolder; position: relative; background-color: #333; color: #fff;" class="btn btn-md btn-dark mt-2"><i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
            				</div>

            				<div class="col-6 text-end">
            					<a href="{{url('lottery/results')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Siguiente
            						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></a>
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

      "select":{style:"single"},

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