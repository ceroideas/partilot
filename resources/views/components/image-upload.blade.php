@props([
    'name' => 'image',
    'label' => 'Imagen',
    'currentImage' => null,
    'sessionKey' => null,
    'previewId' => 'image-preview',
    'inputId' => 'image-input',
    'required' => false
])

<div class="form-group mt-2 mb-3">
    <div class="photo-preview" id="{{ $previewId }}" 
         @if($currentImage || ($sessionKey && session($sessionKey)))
             style="background-image: url('{{ $currentImage ? asset('uploads/' . $currentImage) : (session($sessionKey) ? Storage::disk('public')->url(session($sessionKey)) : '') }}');"
         @endif>
        @if(!$currentImage && !($sessionKey && session($sessionKey)))
            <i class="ri-image-add-line"></i>
        @endif
    </div>
    
    <div>
        <small><i>{{ $label }}</i></small>
        <br>
        <b>Avatar</b>
        <br>
        <label style="border-radius: 30px; width: 150px; background-color: #333;" class="btn btn-md btn-dark mt-2" for="{{ $inputId }}">
            <small>Subir Imagen</small>
        </label>
        <label style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2" onclick="removeImagePreview('{{ $previewId }}', '{{ $inputId }}')">
            <small>Eliminar Imagen</small>
        </label>
        <input type="file" 
               id="{{ $inputId }}" 
               name="{{ $name }}" 
               style="display: none;" 
               accept="image/*" 
               onchange="previewImageFile(this, '{{ $previewId }}')"
               @if($required) required @endif>
        
        @if($sessionKey)
            <input type="hidden" name="temp_image_key" value="{{ $sessionKey }}">
        @endif
    </div>
    <div style="clear: both;"></div>
</div>

@push('scripts')
<script>
function previewImageFile(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.style.backgroundImage = `url(${e.target.result})`;
            preview.innerHTML = ''; // Ocultar icono
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImagePreview(previewId, inputId) {
    document.getElementById(inputId).value = '';
    const preview = document.getElementById(previewId);
    preview.style.backgroundImage = 'none';
    preview.innerHTML = '<i class="ri-image-add-line"></i>';
}
</script>
@endpush
