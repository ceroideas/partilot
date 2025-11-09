@extends('layouts.layout')

@section('title','Vendedores/Asignación')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Vendedores/Asignación</li>
                    </ol>
                </div>
                <h4 class="page-title">Vendedores/Asignación</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif --}}

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="{{ count($sellers) > 0 ? '' : 'd-none' }}">
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Nombre">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Apellidos">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Email">
                                <input type="text" class="form-control" placeholder="Entidad">
                            </div>

                            <a href="{{ route('sellers.create') }}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

                        </h4>


                        <div style="clear: both;"></div>

                        <br>

                        <table id="example2" class="table table-striped nowrap w-100">
                            <thead class="filters">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>F. Nacimiento</th>
                                    <th>NIF/CIF</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Entidad</th>
                                    <th>Grupo</th>
                                    <th>Estado</th>
                                    <th class="no-filter">Acciones</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                @foreach($sellers as $seller)
                                <tr>
                                    <td><a href="{{ route('sellers.show', $seller->id) }}">#VN{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }}</a></td>
                                    <td>{{ $seller->name ?? 'N/A' }}</td>
                                    <td>{{ ($seller->last_name ?? '') . ' ' . ($seller->last_name2 ?? '') }}</td>
                                    <td>{{ $seller->birthday ? \Carbon\Carbon::parse($seller->birthday)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $seller->nif_cif ?? 'N/A' }}</td>
                                    <td>{{ $seller->email ?? 'N/A' }}</td>
                                    <td>{{ $seller->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($seller->entities->count() > 1)
                                            @foreach($seller->entities as $entity)
                                                <span class="badge bg-primary me-1">{{ $entity->name }}</span>
                                            @endforeach
                                        @else
                                            {{ $seller->getPrimaryEntity()?->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($seller->groups->count() > 0)
                                            @foreach($seller->groups as $group)
                                                <span class="badge bg-primary me-1">
                                                    {{ $group->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">Sin grupo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $seller->status_class }}">
                                            {{ $seller->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sellers.edit', $seller->id) }}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12"></a>
                                        <form action="{{ route('sellers.destroy', $seller->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este vendedor?')">
                                                <i class="ri-delete-bin-6-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <br>

                        <a href="{{url('groups?table=1')}}" style="border-radius: 30px; width: 180px; top: -12px; left: -12px; position: relative;" class="btn btn-md btn-dark">
                            <img src="{{url('icons_/grupos.svg')}}" alt="" width="18px" style="position: relative; top: -1px;">
                         Gestión Grupos</a>

                    </div>

                    <div class="{{ count($sellers) > 0 ? 'd-none' : '' }}">
                        <a href="{{url('groups')}}" style="border-radius: 30px; width: 180px; top: -12px; left: -12px; position: relative;" class="btn btn-md btn-dark float-start">
                            <img src="{{url('icons_/grupos.svg')}}" alt="" width="18px" style="position: relative; top: -1px;">
                         Gestión Grupos</a>
                         <div style="clear: both;"></div>
                        <div class="d-flex align-items-center gap-1">

                            
                            <div class="empty-tables">

                                <div>
                                    <img src="{{url('icons_/vendedores.svg')}}" alt="" width="80px">
                                </div>

                                <h3 class="mb-0">No hay Vendedores</h3>

                                <small>Añade Vendedores</small>

                                <br>

                                <a href="{{ route('sellers.create') }}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
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