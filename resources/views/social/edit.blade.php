@extends('layouts.layout')

@section('title','Editar Web Social')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Web Social</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Web Social</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Editar Web Social

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-12">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form id="editSocialWebForm">
                    				@csrf
                    				@method('PUT')
                    				
                    			<h4 class="mb-0 mt-1">
                    				Información de la Web Social
                    			</h4>
                    			<small><i>Modifica los datos de la Web Social</i></small>

                    			<br>
                    			<br>

                    			<div style="min-height: 656px;">

                    				<!-- Banner e Imagen en la parte superior -->
                    				<div class="row mb-4">
                    					<div class="col-md-8">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Banner</label>
                    							<div class="banner-upload-area" id="bannerUploadArea" style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center; min-height: 200px; background-color: #f8f9fa; cursor: pointer;">
                    								@if($socialWeb->banner_image)
                    									<div id="bannerCurrent" style="display: block;">
                    										<img src="{{Storage::url($socialWeb->banner_image)}}" alt="Banner actual" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                    										<button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeBanner()">Eliminar</button>
                    									</div>
                    								@else
                    									<div id="bannerPlaceholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    										<i class="ri-camera-line" style="font-size: 48px; color: #6c757d; margin-bottom: 10px;"></i>
                    										<span style="color: #6c757d;">Haz clic para subir banner</span>
                    									</div>
                    								@endif
                    								<div id="bannerPreview" style="display: none;">
                    									<img id="bannerImg" src="" alt="Banner Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                    									<button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeBanner()">Eliminar</button>
                    								</div>
                    							</div>
                    							<input class="form-control" type="file" name="banner_image" id="banner_image" accept="image/*" style="display: none;">
                    						</div>
                    					</div>
                    					<div class="col-md-4">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Imagen</label>
                    							<div class="image-upload-area" id="imageUploadArea" style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center; min-height: 200px; background-color: #f8f9fa; cursor: pointer;">
                    								@if($socialWeb->small_image)
                    									<div id="imageCurrent" style="display: block;">
                    										<img src="{{Storage::url($socialWeb->small_image)}}" alt="Imagen actual" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                    										<button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">Eliminar</button>
                    									</div>
                    								@else
                    									<div id="imagePlaceholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    										<i class="ri-camera-line" style="font-size: 48px; color: #6c757d; margin-bottom: 10px;"></i>
                    										<span style="color: #6c757d;">Haz clic para subir imagen</span>
                    									</div>
                    								@endif
                    								<div id="smallImagePreview" style="display: none;">
                    									<img id="smallImg" src="" alt="Small Image Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                    									<button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">Eliminar</button>
                    								</div>
                    							</div>
                    							<input class="form-control" type="file" name="small_image" id="small_image" accept="image/*" style="display: none;">
                    						</div>
                    					</div>
                    				</div>

                    				<!-- Título debajo del banner e imagen -->
                    				<div class="row">
                    					<div class="col-md-8">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Título</label>
                    							<input class="form-control" type="text" name="title" id="title" value="{{$socialWeb->title}}" required style="border-radius: 30px; font-size: 18px; padding: 12px;">
                    						</div>
                    					</div>
                    					<div class="col-md-4">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Estado</label>
                    							<select class="form-control" name="status" id="-status" style="border-radius: 30px; display: block !important;">
                    								<option value="draft" {{$socialWeb->status == 'draft' ? 'selected' : ''}}>Borrador</option>
                    								<option value="published" {{$socialWeb->status == 'published' ? 'selected' : ''}}>Publicado</option>
                    							</select>
                    						</div>
                    					</div>
                    				</div>

                    				<!-- Entidad -->
                    				<div class="row">
                    					<div class="col-md-12">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Entidad</label>
                    							<select class="form-control" name="entity_id" id="entity_id" required style="border-radius: 30px;">
                    								@foreach($entities as $entity)
                    									<option value="{{$entity->id}}" {{$socialWeb->entity_id == $entity->id ? 'selected' : ''}}>{{$entity->name}}</option>
                    								@endforeach
                    							</select>
                    						</div>
                    					</div>
                    				</div>

                    				<div class="form-group mb-3">
                    					<label class="label-control">Descripción</label>
                    					<div id="editor" style="height: 300px;">{!! $socialWeb->description !!}</div>
                    					<textarea name="description" id="description" style="display: none;">{{$socialWeb->description}}</textarea>
                    				</div>

		                        </div>

                    			<div class="row">

                    				<div class="col-6 text-start">
                    					<a href="{{url('social')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                    					</a>
                    				</div>

                    				<div class="col-6 text-end">
                    					<button type="button" id="updateSocialWeb" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Guardar
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
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
    
  // CKEditor initialization
  let editor;
  
  $(document).ready(function() {
    // Force select to be visible
    $('#status').css('display', 'block');
    
    // Initialize CKEditor
    editor = CKEDITOR.replace('editor', {
      height: 300,
      toolbar: [
        { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
        { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
        { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
        '/',
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
        { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak', 'Iframe' ] },
        '/',
        { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
      ]
    });

    // Banner upload handler
    $('#bannerUploadArea').click(function() {
      $('#banner_image').click();
    });

    $('#banner_image').change(function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#bannerImg').attr('src', e.target.result);
          $('#bannerCurrent, #bannerPlaceholder').hide();
          $('#bannerPreview').show();
        };
        reader.readAsDataURL(file);
      }
    });

    // Image upload handler
    $('#imageUploadArea').click(function() {
      $('#small_image').click();
    });

    $('#small_image').change(function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#smallImg').attr('src', e.target.result);
          $('#imageCurrent, #imagePlaceholder').hide();
          $('#smallImagePreview').show();
        };
        reader.readAsDataURL(file);
      }
    });

    // Remove functions
    window.removeBanner = function() {
      $('#banner_image').val('');
      $('#bannerPreview, #bannerCurrent').hide();
      $('#bannerPlaceholder').show();
    };

    window.removeImage = function() {
      $('#small_image').val('');
      $('#smallImagePreview, #imageCurrent').hide();
      $('#imagePlaceholder').show();
    };

    // Update social web
    $('#updateSocialWeb').click(function() {
      if (editor) {
        $('#description').val(editor.getData());
      }

      const formData = new FormData($('#editSocialWebForm')[0]);

      $.ajax({
        url: '/social/{{$socialWeb->id}}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'X-HTTP-Method-Override': 'PUT'
        },
        success: function(response) {
          if (response.success) {
            alert('Web Social actualizada exitosamente');
            window.location.href = '/social';
          } else {
            alert('Error al actualizar la Web Social');
          }
        },
        error: function() {
          alert('Error al actualizar la Web Social');
        }
      });
    });
  });

</script>

@endsection
