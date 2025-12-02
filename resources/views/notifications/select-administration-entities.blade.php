@extends('layouts.layout')

@section('title','Notificaciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Notificaciones</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                        <li class="breadcrumb-item active">Entidad</li>
                    </ol>
                </div>
                <h4 class="page-title">Notificaciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">
                        Selección
                    </h4>

                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <div class="form-card bs mb-3">

                                <div class="form-wizard-element">
                                    
                                    <span>
                                        1
                                    </span>

                                    <img src="{{url('assets/entidad.svg')}}" alt="">

                                    <label>
                                        Selección Tipo
                                    </label>

                                </div>

                                <div class="form-wizard-element">
                                    
                                    <span>
                                        2
                                    </span>

                                    <img width="26px" src="{{url('icons_/selec_sorteo.svg')}}" alt="">

                                    <label>
                                        Selecc. Administración
                                    </label>

                                </div>

                                <div class="form-wizard-element active">
                                    
                                    <span>
                                        3
                                    </span>

                                    <img src="{{url('assets/entidad.svg')}}" alt="">

                                    <label>
                                        Selección Entidad/es
                                    </label>

                                </div>

                                <div class="form-wizard-element">
                                    
                                    <span>
                                        4
                                    </span>

                                    <img src="{{url('assets/entidad.svg')}}" alt="">

                                    <label>
                                        Mensaje
                                    </label>

                                </div>
                                
                            </div>

                            <a href="{{route('notifications.select-administration')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                        </div>
                        <div class="col-md-9">
                            <div class="form-card bs" style="min-height: 658px;">
                                <form action="{{ route('notifications.store-administration-entities') }}" method="POST">
                                    @csrf
                                    <h4 class="mb-0 mt-1">
                                        Entidad
                                    </h4>
                                    <small><i>Selecciona la Entidad</i></small>

                                    <br>
                                    <br>

                                    <!-- Switch para seleccionar todas -->
                                    <div class="row mb-3">
                                        <div class="col-12 text-end">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="selectAllSwitch" name="send_to_all" value="1">
                                                <label class="form-check-label" for="selectAllSwitch">
                                                    Seleccionar todas
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="min-height: 656px;">
                                        <table id="example2" class="table table-striped nowrap w-100">
                                            <thead class="">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre Entidad</th>
                                                    <th>Provincia</th>
                                                    <th>Localidad</th>
                                                    <th>Estado</th>
                                                    <th>Seleccionar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($entities as $entity)
                                                <tr class="selectable-row" style="cursor: pointer;">
                                                    <td>#EN{{str_pad($entity->id, 4, '0', STR_PAD_LEFT)}}</td>
                                                    <td>{{$entity->name}}</td>
                                                    <td>{{$entity->province ?? 'Sin provincia'}}</td>
                                                    <td>{{$entity->city ?? 'Sin localidad'}}</td>
                                                    <td><label class="badge bg-success">Activo</label></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input entity-checkbox" type="checkbox" name="entity_ids[]" value="{{$entity->id}}" id="entity_{{$entity->id}}">
                                                            <label class="form-check-label" for="entity_{{$entity->id}}">
                                                                Seleccionar
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Seleccionar
                                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></button>
                                        </div>
                                    </div>
                                </form>
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
        fixedHeader: true
  });
  }

  $(document).ready(function() {
    initDatatable();

    // Hacer las filas clickeables para seleccionar el checkbox
    $(document).on('click', '#example2 tbody tr.selectable-row', function(e) {
      // No activar si se hace clic directamente en el checkbox o su label
      if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label') || $(e.target).closest('label').length) {
        return;
      }
      
      // Toggle del checkbox de la fila
      var checkbox = $(this).find('input[type="checkbox"]');
      checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
    });
    
    // Agregar efecto hover visual
    $(document).on('mouseenter', '#example2 tbody tr.selectable-row', function() {
      $(this).css('background-color', '#f8f9fa');
    }).on('mouseleave', '#example2 tbody tr.selectable-row', function() {
      if (!$(this).find('input[type="checkbox"]').is(':checked')) {
        $(this).css('background-color', '');
      }
    });
    
    // Mantener el color cuando está seleccionado
    $(document).on('change', '#example2 tbody tr.selectable-row input[type="checkbox"]', function() {
      if ($(this).is(':checked')) {
        $(this).closest('tr').css('background-color', '#e3f2fd');
      } else {
        $(this).closest('tr').css('background-color', '');
      }
    });

    // Manejar el switch de "Seleccionar todas"
    $('#selectAllSwitch').change(function() {
        if ($(this).is(':checked')) {
            // Deshabilitar todos los checkboxes individuales
            $('.entity-checkbox').prop('disabled', true).prop('checked', false);
            $('#example2 tbody tr.selectable-row').css('background-color', '');
        } else {
            // Habilitar todos los checkboxes individuales
            $('.entity-checkbox').prop('disabled', false);
        }
    });

    // Manejar cambios en checkboxes individuales
    $('.entity-checkbox').change(function() {
        if ($(this).is(':checked')) {
            // Desmarcar el switch de "Seleccionar todas"
            $('#selectAllSwitch').prop('checked', false);
        }
    });

    // Validar antes de enviar
    $('form').submit(function(e) {
        if (!$('#selectAllSwitch').is(':checked') && $('.entity-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Por favor selecciona al menos una entidad o marca "Seleccionar todas"');
        }
    });
});

</script>

@endsection
