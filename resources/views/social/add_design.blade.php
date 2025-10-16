@extends('layouts.layout')

@section('title','Diseño Web Social')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Web Social</a></li>
                        <li class="breadcrumb-item active">Diseño Web</li>
                    </ol>
                </div>
                <h4 class="page-title">Web Social</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Diseño Web Social

                    </h4>

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					1
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="" width="26px">

                    				<label>
                    					Selec. Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('icons/globe.svg')}}" alt="" width="18px" style="margin: 0 12px;">

                    				<label>
                    					Diseño Web
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<!-- Mostrar entidad seleccionada -->
                    		<div class="form-card bs" style="margin-top: 20px;">
                    			<div class="d-flex align-items-center p-3">
                    				<img src="{{url('assets/entidad.svg')}}" alt="" width="40px" class="me-3">
                    				<div>
                    					<h6 class="mb-0">{{ $entity->name }}</h6>
                    					<small class="text-muted">{{ $entity->province ?? 'Sin provincia' }}</small>
                    				</div>
                    			</div>
                    		</div>

                    		<a href="{{url('social/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form id="socialWebForm" action="{{url('social')}}" method="POST" enctype="multipart/form-data">
                    				@csrf
                    				<input type="hidden" name="entity_id" value="{{ $entity->id }}">
                    				
                    			<h4 class="mb-0 mt-1">
                    				Diseño Web Social
                    			</h4>
                    			<small><i>Configura el contenido de la Web Social</i></small>

                    			<br>
                    			<br>

                    			<div style="min-height: 656px;">

                    				<!-- Banner e Imagen en la parte superior -->
                    				<div class="row mb-4">
                    					<div class="col-md-8">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Banner</label>
                    							<div class="banner-upload-area" id="bannerUploadArea" style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center; min-height: 200px; background-color: #f8f9fa; cursor: pointer;">
                    								<div id="bannerPlaceholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    									<i class="ri-camera-line" style="font-size: 48px; color: #6c757d; margin-bottom: 10px;"></i>
                    									<span style="color: #6c757d;">Haz clic para subir banner</span>
                    								</div>
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
                    								<div id="imagePlaceholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    									<i class="ri-camera-line" style="font-size: 48px; color: #6c757d; margin-bottom: 10px;"></i>
                    									<span style="color: #6c757d;">Haz clic para subir imagen</span>
                    								</div>
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
                    							<input class="form-control" type="text" name="title" id="title" required style="border-radius: 30px; font-size: 18px; padding: 12px;">
                    						</div>
                    					</div>
                    					<div class="col-md-4">
                    						<div class="form-group mb-3">
                    							<label class="label-control">Estado</label>
                    							<select class="form-control" name="status" id="-status" style="border-radius: 30px; display: block !important;">
                    								<option value="draft">Borrador</option>
                    								<option value="published">Publicado</option>
                    							</select>
                    						</div>
                    					</div>
                    				</div>

                    				<div class="form-group mb-3">
                    					<label class="label-control">Descripción</label>
                    					<div id="editor" style="height: 300px;"></div>
                    					<textarea name="description" id="description" style="display: none;"></textarea>
                    				</div>

		                        </div>

                    			<div class="row">

                    				<div class="col-12 text-end">
                    					<button type="submit" id="saveSocialWeb" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Guardar
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
          $('#bannerPlaceholder').hide();
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
          $('#imagePlaceholder').hide();
          $('#smallImagePreview').show();
        };
        reader.readAsDataURL(file);
      }
    });

    // Remove functions
    window.removeBanner = function() {
      $('#banner_image').val('');
      $('#bannerPreview').hide();
      $('#bannerPlaceholder').show();
    };

    window.removeImage = function() {
      $('#small_image').val('');
      $('#smallImagePreview').hide();
      $('#imagePlaceholder').show();
    };

    // Form submission
    $('#socialWebForm').submit(function(e) {
      e.preventDefault();
      
      if (editor) {
        $('#description').val(editor.getData());
      }

      const formData = new FormData(this);

      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            alert('Web Social creada exitosamente');
            window.location.href = '/social';
          } else {
            alert('Error al crear la Web Social');
          }
        },
        error: function() {
          alert('Error al crear la Web Social');
        }
      });
    });
  });

</script>

@endsection
