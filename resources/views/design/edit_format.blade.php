@extends('layouts.layout')

@section('title','Editar Formato')

@section('content')

@php
    if (!function_exists('getNumberFontSize')) {
        function getNumberFontSize($numbers) {
            $count = is_array($numbers) ? count($numbers) : 1;
            $maxDigits = 0;
            if(is_array($numbers)) {
                foreach($numbers as $n) {
                    $maxDigits = max($maxDigits, strlen($n));
                }
            } else {
                $maxDigits = strlen($numbers);
            }
            if($count == 1 && $maxDigits <= 5) return '72px';
            if($count == 2 || $maxDigits > 5) return '56px';
            if($count >= 3 || $maxDigits > 8) return '40px';
            return '32px';
        }
    }
    if (!function_exists('formatMini')) {
        function formatMini($numbers) {
            $doFormat = function($n) {
                $n = (string) $n;
                // Quitar cualquier carácter no dígito
                $n = preg_replace('/\D/', '', $n);
                // Asegúrate de que tenga exactamente 5 dígitos
                $n = str_pad($n, 5, '0', STR_PAD_LEFT);
                // Coloca el punto antes de los 3 últimos
                return substr($n, 0, 2) . '.' . substr($n, 2);
            };
            if(is_array($numbers)) {
                return implode(' - ', array_map($doFormat, $numbers));
            }
            return $doFormat($numbers);
        }
    }
@endphp

<style>
    input[disabled],select[disabled] {
        background-color: #cfcfcf !important;
    }
    /* Límites y borde visibles al seleccionar y redimensionar */
    .elements {
        box-sizing: border-box !important;
        min-width: 20px;
        min-height: 20px;
    }
    .elements.text {
        position: relative;
        box-sizing: border-box !important;
    }
    .elements.text:hover .edit-btn,
    .elements.images:hover .edit-btn {
        display: block;
    }
    .edit-btn {
        display: none;
        position: absolute;
        top: 5px;
        right: 5px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        z-index: 10;
        font-size: 12px;
    }
    .edit-btn:hover {
        background: #0056b3;
    }
    .elements.selected {
        border: 2px solid #007bff !important;
        outline: 2px solid rgba(0, 123, 255, 0.35);
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }
    .elements.element-critical {
        z-index: 10000 !important;
    }
    .qr span {
        width: 100%;
        height: 100%;
        display: block;
    }
    .format-box-btn .btn i {
        font-size: 14px;
        position: relative;
        top: 2px;
    }
    .text-style-btn {
        display: inline-block;
    }
    .text-bold { font-weight: bold; }
    .text-italic { font-style: italic; }
    .text-underline { text-decoration: underline; }
    .text-strike { text-decoration: line-through; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }

    .format-btn-group button, .format-btn-group label {
      max-width: 55px;
    }
    
    /* Centrar el formato */
    .format-box {
        margin: auto !important;
        display: block;
    }
    
    .design-zoom-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        transition: transform 0.2s ease;
    }
    /* Contenedor con scroll cuando el zoom > 100% */
    .design-zoom-scroll {
        overflow: auto;
        max-height: calc(100vh - 300px);
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    
    /* Mejorar visualización de imágenes de fondo */
    [id*="containment-wrapper"] {
        background-size: cover !important;
        background-repeat: no-repeat !important;
        background-position: center center !important;
        background-attachment: scroll !important;
        min-height: 200px;
        position: relative;
    }
    
    /* Asegurar que las imágenes de fondo se muestren correctamente */
    [id*="containment-wrapper"]:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-size: inherit;
        background-repeat: inherit;
        background-position: inherit;
        background-image: inherit;
        z-index: -1;
        pointer-events: none;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item active">Editar Formato</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Formato</h4>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('design.updateFormat', $format->id) }}" id="edit-format-form">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">
                            <div class="d-flex p-2" style=" align-items: center;justify-content: center;">
                                <div class="form-wizard-element active" style="width: 200px;" id="bc-step-1">
                                    <span style="top: -4px; margin-right: 8px;">1</span>
                                    <label>Configurar <br> Formato</label>
                                </div>
                                <div class="form-wizard-element" style="width: 200px;" id="bc-step-2">
                                    <span style="top: -4px; margin-right: 8px;">2</span>
                                    <label>Diseñar <br> Participación</label>
                                </div>
                                <div class="form-wizard-element" style="width: 200px;" id="bc-step-3">
                                    <span style="top: -4px; margin-right: 8px;">3</span>
                                    <label>Diseñar <br> Portada</label>
                                </div>
                                <div class="form-wizard-element" style="width: 200px;" id="bc-step-4">
                                    <span style="top: -4px; margin-right: 8px;">4</span>
                                    <label>Diseñar <br> Trasera</label>
                                </div>
                                <div class="form-wizard-element" style="width: 200px;" id="bc-step-5">
                                    <span style="top: -4px; margin-right: 8px;">5</span>
                                    <label>Configurar <br> Salida</label>
                                </div>
                            </div>
                        </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-card fade show bs" id="step-1" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">Configuración de Formato</h4>
                                    <small><i>Configura el formato de la página y las participaciones</i></small>
                                    <br><br>
                                    <div style="min-height: 656px;">
                                        <h4 class="mb-0 mt-1">Formato de la página</h4>
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group mt-2 mb-3">
                                                            <label class="label-control">Plantilla rápida</label>
                                                            <div class="input-group input-group-merge group-form">
                                                                <select class="form-control" name="format" id="format" style="border-radius: 30px;">
                                                                    <option value="a3-h-3x2" @if($format->format == 'a3-h-3x2') selected @endif>A3 - Apaisado - (3x2)</option>
                                                                    <option value="a3-h-4x2" @if($format->format == 'a3-h-4x2') selected @endif>A3 - Apaisado - (4x2)</option>
                                                                    <option value="a4-v-3x1" @if($format->format == 'a4-v-3x1') selected @endif>A4 - Vertical - (3x1)</option>
                                                                    <option value="a4-v-4x1" @if($format->format == 'a4-v-4x1') selected @endif>A4 - Vertical - (4x1)</option>
                                                                    <option value="custom" @if($format->format == 'custom') selected @endif>Personalizado</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mt-2 mb-3">
                                                            <label class="label-control">Tamaño de la página</label>
                                                            <div class="input-group input-group-merge group-form">
                                                                <select class="form-control custom" name="page" id="page" style="border-radius: 30px;" @if($format->format != 'custom') disabled @endif>
                                                                    <option value="a3" @if($format->page == 'a3') selected @endif>A3 (297x420)</option>
                                                                    <option value="a4" @if($format->page == 'a4') selected @endif>A4 (210x297)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group mt-2 mb-3">
                                                            <label class="label-control">Número de filas</label>
                                                            <div class="input-group input-group-merge group-form">
                                                                <input class="form-control custom" name="rows" value="{{ old('rows', $format->rows) }}" @if($format->format != 'custom') disabled @endif type="number" id="rows" min="1" max="5" style="border-radius: 30px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group mt-2 mb-3">
                                                            <label class="label-control">Número de columnas</label>
                                                            <div class="input-group input-group-merge group-form">
                                                                <input class="form-control custom" name="cols" value="{{ old('cols', $format->cols) }}" @if($format->format != 'custom') disabled @endif type="number" id="cols" min="1" max="5" style="border-radius: 30px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mt-2 mb-3">
                                                            <label class="label-control">Orientación</label>
                                                            <div class="input-group input-group-merge group-form">
                                                                <select class="form-control custom" name="orientation" id="orientation" style="border-radius: 30px;" @if($format->format != 'custom') disabled @endif>
                                                                    <option value="h" @if($format->orientation == 'h') selected @endif>Apaisado</option>
                                                                    <option value="v" @if($format->orientation == 'v') selected @endif>Vertical</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="alert alert-info" id="ticket-info" style="margin-top: 10px;">
                                                            <b>Medidas de la hoja:</b> <span id="sheet-size">-</span><br>
                                                            <b>Medidas de cada participación:</b> <span id="ticket-size">-</span><br>
                                                            <b>Cantidad de participaciones por hoja:</b> <span id="ticket-count">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4 class="mb-0 mt-1 d-flex align-items-center">
                                                    Configurar márgenes
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="btn-desplegar-margenes" data-bs-toggle="collapse" data-bs-target="#marginsCollapse" aria-expanded="false" aria-controls="marginsCollapse">
                                                        Desplegar
                                                    </button>
                                                </h4>

                                                <div class="collapse mt-2" id="marginsCollapse">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row mb-3">
                                                            <label class="col-form-label label-control col-4 text-end">Márgenes de la página (mm)</label>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="margin_up" type="number" id="margin-up" value="{{ old('margin_up', $format->margins['up'] ?? '') }}" step="0.1" placeholder="0,00" style="border-radius: 30px">
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="margin_right" type="number" id="margin-right" value="{{ old('margin_right', $format->margins['right'] ?? '') }}" step="0.1" placeholder="0,00" style="border-radius: 30px">
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="margin_left" type="number" id="margin-left" value="{{ old('margin_left', $format->margins['left'] ?? '') }}" step="0.1" placeholder="0,00" style="border-radius: 30px">
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="margin_top" type="number" id="margin-top" value="{{ old('margin_top', $format->margins['top'] ?? '') }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-form-label label-control col-4 text-end">Sangres de la imagen (mm)</label>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="identation" type="number" id="identation" value="{{ old('identation', $format->identation) }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-form-label label-control col-4 text-end">Anchura de la matriz (mm)</label>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="matrix_box" type="number" id="matrix-box" value="{{ old('matrix_box', $format->matrix_box) }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <span class="d-block mt-1">(Incluyendo sangres)</span>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-form-label label-control col-4 text-end">Márgenes de la página (mm)</label>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" id="margin-custom" name="margin_custom" type="number" value="{{ old('margin_custom', $format->margin_custom ?? '') }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-form-label label-control col-4 text-end">Espacio horizontal entre participaciones (mm)</label>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="horizontal_space" type="number" id="page-rigth" value="{{ old('horizontal_space', $format->horizontal_space) }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-form-label label-control col-4 text-end">Espacio vertical entre participaciones (mm)</label>
                                                            <div class="col-sm-2">
                                                                <input class="form-control" name="vertical_space" type="number" id="page-bottom" value="{{ old('vertical_space', $format->vertical_space) }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="preview-design">
                                                    <div class="a3">
                                                        <div style="height: 72px;"></div>
                                                        <div style="height: 72px;"></div>
                                                        <div style="height: 72px;"></div>
                                                        <div style="height: 72px;"></div>
                                                        <div style="height: 72px;"></div>
                                                        <div style="height: 72px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
                                                    <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="form-card fade bs d-none" id="step-2" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">Diseñar Participación</h4>
                                <small><i>Edita el diseño de la participación</i></small>
                                <br>
                                <div class="format-box-btn" style="width: 270mm; height: 54px; margin: auto; padding-left: 20px;">
                                    <br>
                                    <div class="btn-group format-btn-group" style="width: 270mm; display: flex; justify-content: center; flex-wrap: wrap; gap: 1px;">
                                        <button type="button" class="btn btn-sm btn-secondary design-zoom-out" title="Alejar" data-step="2"><i class="ri-zoom-out-line"></i></button>
                                        <button type="button" class="btn btn-sm btn-secondary design-zoom-in" title="Acercar" data-step="2"><i class="ri-zoom-in-line"></i></button>
                                        <span class="align-self-center px-1 design-zoom-label" style="font-size: 12px;">100%</span>
                                        <button title="Agregar texto" class="btn btn-sm btn-dark add-text" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-edit-line"></i></button>
                                        <button title="Agregar imagen" class="btn btn-sm btn-dark add-image" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-image-line"></i></button>
                                        <button title="Fondo de la participación" class="btn btn-sm btn-dark" id="open-bg-modal" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-palette-line"></i></button>
                                        <button title="Mostrar/ocultar guías" class="btn btn-sm btn-dark toggle-guide" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-ruler-line"></i></button>
                                        <label title="Color de guías" class="btn btn-sm btn-dark color-guide" style="position: relative; padding-left: 12px; padding-right: 12px;" data-id="2" type="button">
                                            <i class="ri-palette-line"></i><input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                        </label>
                                        <button class="btn btn-sm btn-warning undo-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Deshacer"><i class="ri-arrow-go-back-line"></i></button>
                                        <button class="btn btn-sm btn-success redo-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Rehacer"><i class="ri-arrow-go-forward-line"></i></button>
                                        <button class="btn btn-sm btn-danger delete-element-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Eliminar elemento"><i class="ri-delete-bin-6-line"></i></button>
                                        <button class="btn btn-sm btn-dark up-layer" disabled style="padding-left: 12px; padding-right: 12px;" title="Subir capa"><i class="ri-arrow-up-line"></i></button>
                                        <button class="btn btn-sm btn-dark down-layer" disabled style="padding-left: 12px; padding-right: 12px;" title="Bajar capa"><i class="ri-arrow-down-line"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn bold-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Negrita"><i class="ri-bold"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn italic-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Cursiva"><i class="ri-italic"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn underline-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Subrayado"><i class="ri-underline"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn strike-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Tachado"><i class="ri-strikethrough"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn align-left-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Alinear izquierda"><i class="ri-align-left"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn align-center-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Centrar"><i class="ri-align-center"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn align-right-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Alinear derecha"><i class="ri-align-right"></i></button>
                                        <button class="btn btn-sm btn-dark text-style-btn font-size-up-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Aumentar tamaño"><i class="ri-font-size"></i>+</button>
                                        <button class="btn btn-sm btn-dark text-style-btn font-size-down-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Disminuir tamaño"><i class="ri-font-size"></i>-</button>
                                    </div>
                                </div>
                                <div class="design-zoom-scroll">
                                    <div class="design-zoom-container" id="design-zoom-wrapper-2" style="transform-origin: top center;">
                                        {!! $format->participation_html ?? '' !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-card fade bs d-none" id="step-3" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">Diseñar Portada</h4>
                                <small><i>Edita el diseño de la portada</i></small>
                                <br>
                                <div class="format-box-btn" style="width: 270mm; height: 54px; margin: auto; padding-left: 20px;">
                                    <br>
                                    <div class="btn-group format-btn-group" style="width: 270mm; display: flex; justify-content: center; flex-wrap: wrap; gap: 1px;">
                                            <button type="button" class="btn btn-sm btn-secondary design-zoom-out" title="Alejar" data-step="3"><i class="ri-zoom-out-line"></i></button>
                                            <button type="button" class="btn btn-sm btn-secondary design-zoom-in" title="Acercar" data-step="3"><i class="ri-zoom-in-line"></i></button>
                                            <span class="align-self-center px-1 design-zoom-label" style="font-size: 12px;">100%</span>
                                            <button title="Agregar texto" class="btn btn-sm btn-dark add-text" data-id="3" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-edit-line"></i></button>
                                            <button title="Agregar imagen" class="btn btn-sm btn-dark add-image" data-id="3" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-image-line"></i></button>
                                            <button title="Fondo de la participación" class="btn btn-sm btn-dark" id="open-bg-modal" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-palette-line"></i></button>
                                            <button title="Agregar barra superior" class="btn btn-sm btn-dark add-top" data-id="3" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-layout-top-line"></i></button>
                                            <button title="Agregar barra inferior" class="btn btn-sm btn-dark add-bottom" data-id="3" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-layout-bottom-line"></i></button>
                                            <button title="Mostrar/ocultar guías" class="btn btn-sm btn-dark toggle-guide" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-ruler-line"></i></button>
                                            <label title="Color de guías" class="btn btn-sm btn-dark color-guide" style="position: relative; padding-left: 12px; padding-right: 12px;" data-id="2" type="button">
                                                <i class="ri-palette-line"></i><input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                            </label>
                                            <button class="btn btn-sm btn-warning undo-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Deshacer"><i class="ri-arrow-go-back-line"></i></button>
                                            <button class="btn btn-sm btn-success redo-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Rehacer"><i class="ri-arrow-go-forward-line"></i></button>
                                            <button class="btn btn-sm btn-danger delete-element-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Eliminar elemento"><i class="ri-delete-bin-6-line"></i></button>
                                            <button class="btn btn-sm btn-dark up-layer" disabled style="padding-left: 12px; padding-right: 12px;" title="Subir capa"><i class="ri-arrow-up-line"></i></button>
                                            <button class="btn btn-sm btn-dark down-layer" disabled style="padding-left: 12px; padding-right: 12px;" title="Bajar capa"><i class="ri-arrow-down-line"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn bold-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Negrita"><i class="ri-bold"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn italic-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Cursiva"><i class="ri-italic"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn underline-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Subrayado"><i class="ri-underline"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn strike-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Tachado"><i class="ri-strikethrough"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn align-left-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Alinear izquierda"><i class="ri-align-left"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn align-center-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Centrar"><i class="ri-align-center"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn align-right-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Alinear derecha"><i class="ri-align-right"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn font-size-up-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Aumentar tamaño"><i class="ri-font-size"></i>+</button>
                                            <button class="btn btn-sm btn-dark text-style-btn font-size-down-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Disminuir tamaño"><i class="ri-font-size"></i>-</button>
                                    </div>
                                </div>
                                <div class="design-zoom-scroll">
                                    <div class="design-zoom-container" id="design-zoom-wrapper-3" style="transform-origin: top center;">
                                        {!! $format->cover_html ?? '' !!}
                                    </div>
                                </div>
                            </div>
                                <div class="form-card fade bs d-none" id="step-4" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">Diseñar Trasera</h4>
                                    <small><i>Edita el diseño de la trasera</i></small>
                                    <br>
                                    <div class="format-box-btn" style="width: 250mm; height: 54px; margin: auto; padding-left: 20px;">
                                        <br>
                                        <div class="btn-group format-btn-group" style="width: 250mm; display: flex; justify-content: center; flex-wrap: wrap; gap: 1px;">
                                            <button type="button" class="btn btn-sm btn-secondary design-zoom-out" title="Alejar" data-step="4"><i class="ri-zoom-out-line"></i></button>
                                            <button type="button" class="btn btn-sm btn-secondary design-zoom-in" title="Acercar" data-step="4"><i class="ri-zoom-in-line"></i></button>
                                            <span class="align-self-center px-1 design-zoom-label" style="font-size: 12px;">100%</span>
                                            <button title="Agregar texto" class="btn btn-sm btn-dark add-text" data-id="4" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-edit-line"></i></button>
                                            <button title="Agregar imagen" class="btn btn-sm btn-dark add-image" data-id="4" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-image-line"></i></button>
                                            <button title="Fondo de la participación" class="btn btn-sm btn-dark" id="open-bg-modal" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-palette-line"></i></button>
                                            <button title="Agregar barra superior" class="btn btn-sm btn-dark add-top" data-id="4" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-layout-top-line"></i></button>
                                            <button title="Agregar barra inferior" class="btn btn-sm btn-dark add-bottom" data-id="4" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-layout-bottom-line"></i></button>
                                            <button title="Mostrar/ocultar guías" class="btn btn-sm btn-dark toggle-guide" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-ruler-line"></i></button>
                                            <label title="Color de guías" class="btn btn-sm btn-dark color-guide" style="position: relative; padding-left: 12px; padding-right: 12px;" data-id="2" type="button">
                                                <i class="ri-palette-line"></i><input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                            </label>
                                            <button class="btn btn-sm btn-warning undo-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Deshacer"><i class="ri-arrow-go-back-line"></i></button>
                                            <button class="btn btn-sm btn-success redo-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Rehacer"><i class="ri-arrow-go-forward-line"></i></button>
                                            <button class="btn btn-sm btn-danger delete-element-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Eliminar elemento"><i class="ri-delete-bin-6-line"></i></button>
                                            <button class="btn btn-sm btn-dark up-layer" disabled style="padding-left: 12px; padding-right: 12px;" title="Subir capa"><i class="ri-arrow-up-line"></i></button>
                                            <button class="btn btn-sm btn-dark down-layer" disabled style="padding-left: 12px; padding-right: 12px;" title="Bajar capa"><i class="ri-arrow-down-line"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn bold-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Negrita"><i class="ri-bold"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn italic-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Cursiva"><i class="ri-italic"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn underline-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Subrayado"><i class="ri-underline"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn strike-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Tachado"><i class="ri-strikethrough"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn align-left-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Alinear izquierda"><i class="ri-align-left"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn align-center-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Centrar"><i class="ri-align-center"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn align-right-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Alinear derecha"><i class="ri-align-right"></i></button>
                                            <button class="btn btn-sm btn-dark text-style-btn font-size-up-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Aumentar tamaño"><i class="ri-font-size"></i>+</button>
                                            <button class="btn btn-sm btn-dark text-style-btn font-size-down-btn" disabled style="padding-left: 12px; padding-right: 12px;" title="Disminuir tamaño"><i class="ri-font-size"></i>-</button>
                                    </div>
                                </div>
                                <div class="design-zoom-scroll">
                                    <div class="design-zoom-container" id="design-zoom-wrapper-4" style="transform-origin: top center;">
                                        {!! $format->back_html ?? '' !!}
                                    </div>
                                </div>
                            </div>
                                <div class="form-card fade bs d-none" id="step-5" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">Configurar salida</h4>
                                    <small><i>Configura el formato de salida de las participaciones</i></small>
                                    <br><br>
                                    <div>
                                        <h4 class="mb-0 mt-1">Formato de la página</h4>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mb-1">
                                                    <div class="form-check form-switch mt-3">
                                                        <input style="float: left;" class="form-check-input bg-dark" type="checkbox" role="switch" id="guides" name="draw_guides" @if($format->output['draw_guides'] ?? false) checked @endif>
                                                        <label style="float: left; margin-left: 50px;" class="form-check-label" for="guides"><b>Dibujar las guías de corte</b></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="row mb-3">
                                                    <label class="col-form-label label-control col-6 text-start">Color de las guías</label>
                                                    <div class="col-sm-2">
                                                        <input class="form-control" type="color" id="guide_color" name="guide_color" value="{{ old('guide_color', $format->output['guide_color'] ?? '#000000') }}" style="border-radius: 30px">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="row mb-3">
                                                    <label class="col-form-label label-control col-6 text-start">Grosor de las guías (mm):</label>
                                                    <div class="col-sm-2">
                                                        <input class="form-control" type="number" id="guide_weight" name="guide_weight" value="{{ old('guide_weight', $format->output['guide_weight'] ?? '0.3') }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="mb-0 mt-1">Participaciones por talonario</h4>
                                        <small><i>Elige la cantidad de participaciones por talonario</i></small>
                                        <div class="row mb-3">
                                            <label class="col-form-label label-control col-3 text-start">Cantidad de participaciónes:</label>
                                            <div class="col-sm-1">
                                                <input class="form-control" type="number" name="participations_per_book" value="{{ old('participations_per_book', $format->output['participations_per_book'] ?? 50) }}" id="participation_number" style="border-radius: 30px">
                                            </div>
                                        </div>
                                        <br>
                                        <h4 class="mb-0 mt-1">Participaciones a generar</h4>
                                        <div class="form-group mb-3">
                                            <div class="form-check form-switch mt-3">
                                                <input style="float: left;" class="form-check-input bg-dark" type="radio" name="generate_mode" value="1" role="switch" id="generate1" @if(($format->output['generate_mode'] ?? '1') == '1') checked @endif>
                                                <label style="float: left; margin-left: 50px;" class="form-check-label" for="generate1"><b>Generar todas las participaciones (600)</b></label>
                                            </div>
                                            <div class="form-check form-switch mt-3">
                                                <input style="float: left;" class="form-check-input bg-dark" type="radio" name="generate_mode" value="2" role="switch" id="generate" @if(($format->output['generate_mode'] ?? '1') == '2') checked @endif>
                                                <label style="float: left; margin-left: 50px;" class="form-check-label" for="generate"><b>Seperar las participaciones en múltiples documentos</b></label>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-form-label label-control col-3 text-start">Generar de la participación:</label>
                                            <div class="col-sm-1">
                                                <input class="form-control" type="number" name="participation_from" value="{{ old('participation_from', $format->output['participation_from'] ?? 1) }}" id="participation_from" style="border-radius: 30px">
                                            </div>
                                            <label class="col-form-label label-control col-3 text-start">Hasta la participación:</label>
                                            <div class="col-sm-1">
                                                <input class="form-control" type="number" name="participation_to" value="{{ old('participation_to', $format->output['participation_to'] ?? $set->total_participations ?? '') }}" id="participation_to" style="border-radius: 30px">
                                            </div>
                                            <label class="col-form-label label-control col-4 text-start">(ambas incluidas)</label>
                                        </div>
                                        <br>
                                        <h4 class="mb-0 mt-1">Número de documentos</h4>
                                        <div class="form-group mb-3">
                                            <div class="form-check form-switch mt-3">
                                                <input style="float: left;" class="form-check-input bg-dark" type="radio" name="documents_mode" value="1" role="switch" id="documents1" @if(($format->output['documents_mode'] ?? '1') == '1') checked @endif>
                                                <label style="float: left; margin-left: 50px;" class="form-check-label" for="documents1"><b>Generar un único documento</b></label>
                                            </div>
                                            <div class="form-check form-switch mt-3">
                                                <input style="float: left;" class="form-check-input bg-dark" type="radio" name="documents_mode" value="2" role="switch" id="documents" @if(($format->output['documents_mode'] ?? '1') == '2') checked @endif>
                                                <label style="float: left; margin-left: 50px;" class="form-check-label" for="documents"><b>Seperar las participaciones en múltiples documentos</b></label>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-form-label label-control col-3 text-start">Número de páginas por documento:</label>
                                            <div class="col-sm-1">
                                                <input class="form-control" type="number" name="pages_per_document" value="{{ old('pages_per_document', $format->output['pages_per_document'] ?? 150) }}" id="participation_page" style="border-radius: 30px">
                                            </div>
                                            <label class="col-form-label label-control col-8 text-start">(6 participaciones por página, 1 documento)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                  <div class="col-6 text-start">
                                      <a href="javascript:;" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2 prev-step">
                                          <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                                  </div>
                                  <div class="col-6 text-end">
                                      <button id="step" type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2 next-step">Siguiente
                                          <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></button>
                                      <button id="save-step" type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2 d-none">Guardar
                                          <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                                  </div>
                              </div>
                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
    </form>
</div> <!-- container -->

<!-- Modales para edición visual -->
<div class="modal fade" id="ckeditor-modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Texto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="editor-container__editor"><div id="editor" style="height: 200px;"></div></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger deleteElements" data-bs-dismiss="modal">Eliminar elemento</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-sm btn-primary accept-text">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="imagen-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group mt-2 mb-3">
            <label class="label-control">Subir imagen</label>
            <div class="input-group input-group-merge group-form">
                <input class="" id="imageInput" type="file">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger deleteElements" data-bs-dismiss="modal">Eliminar elemento</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-sm btn-primary accept-image">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="qr-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generar QR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group mt-2 mb-3">
            <label class="label-control">Texto para el QR</label>
            <div class="input-group input-group-merge group-form">
                <input class="form-control" type="text" id="qr-text" placeholder="Texto para el QR" style="border-radius: 30px">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger deleteElements" data-bs-dismiss="modal">Eliminar elemento</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-sm btn-primary accept-qr">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<!-- Overlay de carga -->
<div id="design-loading-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
  <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Cargando...</span></div>
  <p class="text-white mt-2 mb-0" id="design-loading-text">Procesando...</p>
</div>

<div class="modal fade" id="position-modal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar posición</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <button class="btn btn-sm btn-info up-z">Subir</button>
        <button class="btn btn-sm btn-info dw-z">Bajar</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- === MODAL FONDO DE TICKET === -->
<div class="modal fade" id="background-modal" tabindex="-1" aria-labelledby="backgroundModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="backgroundModalLabel">Seleccionar fondo de la participación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="background-color" class="form-label">Color de fondo</label>
          <input type="color" class="form-control form-control-color" id="background-color" value="#dfdfdf" title="Elige un color">
        </div>
        <div class="mb-3">
          <label for="background-image" class="form-label">Imagen de fondo</label>
          <input class="form-control" type="file" id="background-image" accept="image/*">
        </div>
        <div class="mb-3">
          <button class="btn btn-secondary" id="remove-bg-image">Quitar imagen de fondo</button>
        </div>
        <div id="bg-preview" style="width:100%;height:80px;border:1px solid #ccc;background-size:cover;background-position:center;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="apply-bg">Aplicar fondo</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// --- Funciones de edición visual (copiadas de la vista original) ---
function editelements(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    actualElement = $(this).closest('.elements.text');
    
    // Obtener el contenido del span (sin el botón de editar)
    var contenidoHTML = $(actualElement).find('span').html() || '';

    // Destruir instancia previa si existe
    if (editor && CKEDITOR.instances['editor']) {
        CKEDITOR.instances['editor'].destroy(true);
    }

    // Limpiar el contenido del div
    $('#editor').html('');

    addEventsElement();

    // Inicializar CKEditor
    editor = CKEDITOR.replace('editor', {
        enterMode: CKEDITOR.ENTER_BR,
        shiftEnterMode: CKEDITOR.ENTER_P,
        // Toolbar básico
        toolbar: [
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
            { name: 'paragraph', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'styles', items: [ 'FontSize' ] }
        ],
        on: {
            instanceReady: function() {
                // Establecer el contenido cuando CKEditor esté listo
                this.setData(contenidoHTML);
            }
        }
    });

    $('#ckeditor-modal').modal('show');
    return false;
}
function deleteElements(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    let element = $(this);
    if (element.hasClass('element-critical')) {
        alert('Este elemento es obligatorio y no se puede eliminar.');
        return false;
    }
    if (confirm('¿Desea eliminar el elemento seleccionado?')) {
        element.remove();
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
        saveHistoryState();
        updateUndoRedoButtons();
    }
    return false;
}
function changeImage(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    actualElement = $(this).closest('.elements.images');
    $('#imagen-modal').modal('show');
    return false;
}
function setQRtext(event) {
    actualElement = $(this);
    $('#qr-modal').modal('show');
}
// --- Sincronizar step con la edición ---

function getCustomDimensions() {
    let page = $('#page').val();
    let cols = parseInt($('#cols').val());
    let rows = parseInt($('#rows').val());
    let orientation = $('#orientation').val();
    let w, h;
    if (page == 'a3') {
        w = 400 / cols;
        h = 276 / rows;
    } else if (page == 'a4') {
        w = 190 / cols;
        h = 277 / rows;
    }
    {{-- if (orientation == 'v') {
        let aux = w;
        w = h;
        h = aux;
    } --}}
    return {w, h};
}

function recalculateDesign() {
    let cols = $('#cols').val();
    let rows = $('#rows').val();
    let orientation = $('#orientation').val();
    let page = $('#page').val();

    if (orientation == 'h') {
        $('.preview-design > div').css('width','100%');
    }else{
        $('.preview-design > div').css('width','60%');
    }

    let h = 216 / rows;
    let html = "";
    let percent = 100 / cols;
    let margin = 1 / cols;
    for (var i = 0; i < cols*rows; i++) {
        html+=`<div style="height: ${h}px; width: ${percent-1}%; margin-left: ${margin}%"></div>`;
    }
    $('.preview-design > div').html(html);

    // Eliminado: cambio de tamaño de .format-box aquí
    // if($('#format').val() === 'custom') {
    //     const {w, h} = getCustomDimensions();
    //     $('.format-box').css({width: w+'mm', height: h+'mm'});
    // }
}

$('#cols,#rows').change(function (e) {
    e.preventDefault();
    recalculateDesign();
});

$('#page').change(function (e) {
    e.preventDefault();
    let clase = $(this).val();
    $('.preview-design > div').removeClass('a3 a4');
    $('.preview-design > div').addClass(clase);
    recalculateDesign();
});

$('#orientation').change(function(event) {
    recalculateDesign();
});

$('#format').change(function (e) {
    e.preventDefault();
    let html = "";
    restoreValues();
    if($(this).val() == 'a3-h-3x2') {
        $('.custom').prop('disabled', true);
        html = `<div class="a3">
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
            </div>`;
    } else if($(this).val() == 'a3-h-4x2') {
        $('.custom').prop('disabled', true);
        html = `<div class="a3">
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
            </div>`;
    } else if($(this).val() == 'a4-v-3x1') {
        $('.custom').prop('disabled', true);
        html = `<div class="a4">
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
                <div style="height: 72px;"></div>
            </div>`;
    } else if($(this).val() == 'a4-v-4x1') {
        $('.custom').prop('disabled', true);
        html = `<div class="a4">
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
                <div style="height: 54px;"></div>
            </div>`;
    } else if($(this).val() == 'custom') {
        $('.custom').prop('disabled', false);
        html = `<div class="a3">
                    <div style="height: 72px;"></div>
                    <div style="height: 72px;"></div>
                    <div style="height: 72px;"></div>
                    <div style="height: 72px;"></div>
                    <div style="height: 72px;"></div>
                    <div style="height: 72px;"></div>
                </div>`;
        // Actualizar el tamaño del format-box en tiempo real para personalizado
        const {w, h} = getCustomDimensions();
        console.log(w,h)
        {{-- $('.format-box').css({width: w+'mm', height: h+'mm'}); --}}
    }
    $('.preview-design').html(html);
});

  function restoreValues()
  {
    $('#page').prop('selectedIndex',0);
    $('#rows').val(3);
    $('#cols').val(2);
    $('#orientation').prop('selectedIndex',0);
  }

var step = 1;
var editor;
var actualElement;
var selectedElement = null;

// Sistema de Undo/Redo limitado
var historyStates = [];
var currentHistoryIndex = -1;
var maxHistoryStates = 10;
var isRestoringState = false;
var resizeTimeout;

// Zoom del diseño (pasos 2, 3, 4)
var designZoom = 1;
var designZoomSteps = [0.5, 0.75, 1, 1.25, 1.5];
function applyDesignZoom() {
  var s = designZoom;
  // Aplicar zoom solo al contenedor del diseño, no a las herramientas
  $('.design-zoom-container').css('transform', 'scale(' + s + ')');
  $('.design-zoom-label').text(Math.round(s * 100) + '%');
  try { localStorage.setItem('designZoom', s); } catch (e) {}
}
try { designZoom = parseFloat(localStorage.getItem('designZoom')) || 1; } catch (e) {}
designZoom = designZoomSteps.indexOf(designZoom) >= 0 ? designZoom : 1;
applyDesignZoom();

// Funciones del sistema de Undo/Redo
function saveHistoryState() {
  if (isRestoringState) return;
  const canvasHtml = $('#containment-wrapper' + step).html();
  const canvasState = {
    html: canvasHtml,
    step: step,
    timestamp: Date.now()
  };
  if (currentHistoryIndex < historyStates.length - 1) {
    historyStates = historyStates.slice(0, currentHistoryIndex + 1);
  }
  historyStates.push(canvasState);
  currentHistoryIndex++;
  if (historyStates.length > maxHistoryStates) {
    historyStates.shift();
    currentHistoryIndex--;
  }
  updateUndoRedoButtons();
}

function restoreHistoryState(targetIndex) {
  if (targetIndex < 0 || targetIndex >= historyStates.length) return;
  isRestoringState = true;
  const targetState = historyStates[targetIndex];
  if (targetState.step === step) {
    $('#containment-wrapper' + step).html(targetState.html);
    reapplyElementEvents();
    currentHistoryIndex = targetIndex;
    updateUndoRedoButtons();
  }
  isRestoringState = false;
}

function undo() {
  if (canUndo()) {
    restoreHistoryState(currentHistoryIndex - 1);
  }
}

function redo() {
  if (canRedo()) {
    restoreHistoryState(currentHistoryIndex + 1);
  }
}

function canUndo() {
  return currentHistoryIndex > 0 && historyStates.length > 1;
}

function canRedo() {
  return currentHistoryIndex < historyStates.length - 1;
}

function updateUndoRedoButtons() {
  $('.undo-btn').prop('disabled', !canUndo());
  $('.redo-btn').prop('disabled', !canRedo());
}

function uploadImage(file) {
  const formData = new FormData();
  formData.append('image', file);
  showDesignLoading('Subiendo imagen...');
  fetch('{{url('api/upload-image')}}', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.url) {
      $(actualElement).find('img').attr('src', data.url);
      $('#imagen-modal').modal('hide');
      const input = document.getElementById('imageInput');
      if (input) input.value = null;
      $('#step').addClass('d-none');
      $('#save-step').removeClass('d-none');
      saveHistoryState();
      updateUndoRedoButtons();
      // Re-vincular eventos después de cambiar imagen
      reapplyElementEvents();
    }
  })
  .catch(error => console.error('Error al subir la imagen:', error))
  .finally(() => hideDesignLoading());
}

function showStep(newStep) {
    $('.form-card[id*="step-"]').addClass('d-none').removeClass('show');
    $(`#step-${newStep}`).removeClass('d-none fade').addClass('show');
    $('.form-wizard-element').removeClass('active');
    $(`#bc-step-${newStep}`).addClass('active');
    if (newStep === 5) {
        {{-- $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none'); --}}
    } else {
        $('#step').removeClass('d-none');
        $('#save-step').addClass('d-none');
    }
    // Aplicar zoom al cambiar de paso
    if (typeof applyDesignZoom === 'function') {
        applyDesignZoom();
    }
}

function addEventsElement() {
    $('.elements').unbind('contextmenu',changePositionElement);
    $('.elements').contextmenu(changePositionElement);
    $('.elements').unbind('click.select');
    $('.elements').bind('click.select', function(e) {
      e.stopPropagation();
      $('.elements').removeClass('selected');
      $(this).addClass('selected');
      selectedElement = $(this);
      actualElement = $(this); // Mantener compatibilidad
      $('.up-layer, .down-layer, .delete-element-btn').prop('disabled', false);
      if ($(this).hasClass('text')) {
        $('.text-style-btn').prop('disabled', false);
      } else {
        $('.text-style-btn').prop('disabled', true);
      }
      updateUndoRedoButtons();
    });
    
    // Deseleccionar al hacer clic fuera
    $('body').unbind('click.deselect');
    $('body').bind('click.deselect', function(e) {
      if (!$(e.target).closest('.elements').length && !$(e.target).closest('.up-layer, .down-layer, .text-style-btn, .delete-element-btn, .undo-btn').length) {
        $('.elements').removeClass('selected');
        selectedElement = null;
        actualElement = null;
        $('.up-layer, .down-layer, .text-style-btn, .delete-element-btn').prop('disabled', true);
      }
    });
}

function changePositionElement(event) {
    event.preventDefault();
    actualElement = $(this);
    $('#position-modal').modal('show');
}

{{-- $('#save-step').click(function(event) {

    if (step != 1) {

      
      let html = $('#containment-wrapper'+step).html();

      localStorage.setItem('step'+step,html);

      $('#step').removeClass('d-none');
      $('#save-step').addClass('d-none');

    }
}); --}}

var snapshot_path = null;
  $('#save-step').click(function(event) {

    if (step != 1) {

        let guardarSnapshotTriggered = false;
        
        if(step == 2 && !guardarSnapshotTriggered) {
            guardarSnapshotTriggered = true;
            html2canvas(document.querySelector('#step-2 .format-box')).then(function(canvas) {
                let imageData = canvas.toDataURL('image/png');
                
                var formData = new FormData();

                formData.append('design_id', {{ $set->id }});
                formData.append('snapshot', imageData);

                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/api/design/save-snapshot",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        
                        snapshot_path = response.path;
                        console.log(snapshot_path);

                        guardarSnapshotTriggered = false;
                        // Ahora sí avanza al submit normal/flujo de guardar! Activa el clic de nuevo.
                        let html = $('#containment-wrapper'+step).html();

                        localStorage.setItem('step'+step,html);

                        $('#step').removeClass('d-none');
                        $('#save-step').addClass('d-none');
                    },error: function (response) {
                        console.log(response);
                        guardarSnapshotTriggered = false;
                        alert('Error al guardar snapshot');
                    }
                });
            });
        } else {
            let html = $('#containment-wrapper'+step).html();

            localStorage.setItem('step'+step,html);

            $('#step').removeClass('d-none');
            $('#save-step').addClass('d-none');
        }

    }
  });

/**/

function configMargins()
  {
    let identation = $('#identation').val() ?? 2.5;
    let matrix = $('#matrix-box').val() ?? 40;
    $('.margen-izquierdo').css('left',identation+'mm')
    $('.margen-arriba').css('top',identation+'mm')
    $('.margen-derecho').css('right',identation+'mm')
    $('.margen-abajo').css('bottom',identation+'mm')
    $('.caja-matriz').css('left',identation+'mm')
    $('.caja-matriz').css('width',matrix+'mm')
    $('.caja-matriz-2').css('right',identation+'mm')
    $('.caja-matriz-2').css('width',matrix+'mm')
  }

  $('.up-z').click(function (e) {
      e.preventDefault();
      let zindex = $(actualElement).css('z-index');
      zindex = parseInt(zindex)+1;
      $(actualElement).css('z-index',zindex);
  });
  $('.dw-z').click(function (e) {
      e.preventDefault();
      let zindex = $(actualElement).css('z-index');
      zindex = parseInt(zindex)-1;
      $(actualElement).css('z-index',zindex);
  });

   $('.toggle-guide').click(function (e) {
       e.preventDefault();

       let opacity = $('.guide'+step).css('opacity');

       $('.guide'+step).css('opacity', opacity == 1 ? 0 : 1);
   });
   $('.color-guide input').change(function (e) {
      e.preventDefault();

      localStorage.setItem('guide-step'+step,$(this).val());

      let opacity = $('.guide'+step).css('border-color',$(this).val());
  });

  // === INICIO BLOQUE NUEVO ===
  // Tabla de medidas de ticket para todas las combinaciones posibles
  const ticketSizes = {
    a3: {
      h: {},
      v: {}
    },
    a4: {
      h: {},
      v: {}
    }
  };
  // Medidas útiles de hoja (márgenes de 10mm por lado)
  const sheetUsable = {
    a3: { h: { width: 400, height: 277 }, v: { width: 277, height: 400 } },
    a4: { h: { width: 277, height: 190 }, v: { width: 190, height: 277 } }
  };
  // Generar todas las combinaciones
  for (const page of ['a3', 'a4']) {
    for (const orientation of ['h', 'v']) {
      const usable = sheetUsable[page][orientation];
      for (let rows = 1; rows <= 5; rows++) {
        for (let cols = 1; cols <= 5; cols++) {
          const w = (usable.width / cols).toFixed(2);
          const h = (usable.height / rows).toFixed(2);
          ticketSizes[page][orientation][`${cols}x${rows}`] = { w, h };
        }
      }
    }
  }
  // === FIN BLOQUE NUEVO ===

  function updateTicketInfo() {
      // Definir plantillas rápidas
      const quickTemplates = {
          'a3-h-3x2': { page: 'a3', orientation: 'h', cols: 3, rows: 2, ticket: '200mm x 92mm' },
          'a3-h-4x2': { page: 'a3', orientation: 'h', cols: 4, rows: 2, ticket: '200mm x 68.88mm' },
          'a4-v-3x1': { page: 'a4', orientation: 'v', cols: 3, rows: 1, ticket: '190mm x 92mm' },
          'a4-v-4x1': { page: 'a4', orientation: 'v', cols: 4, rows: 1, ticket: '190mm x 69.38mm' }
      };
      let page = $('#page').val();
      let orientation = $('#orientation').val();
      let cols = parseInt($('#cols').val());
      let rows = parseInt($('#rows').val());
      let format = $('#format').val();

      // Medidas de hoja
      const sheetSizes = {
          'a3': { h: { width: 420, height: 297 }, v: { width: 297, height: 420 } },
          'a4': { h: { width: 297, height: 210 }, v: { width: 210, height: 297 } }
      };
      let sheet = sheetSizes[page][orientation];
      let sheetText = `${sheet.width}mm x ${sheet.height}mm`;

      // Medidas de ticket: buscar en la tabla
      let key = `${cols}x${rows}`;
      let ticketObj = ticketSizes[page][orientation][key];
      let ticketText = ticketObj ? `${ticketObj.w}mm x ${ticketObj.h}mm` : '-';

      // Casos especiales de plantillas rápidas
      if(format === 'a3-h-3x2') { ticketText = '200mm x 92mm'; }
      else if(format === 'a3-h-4x2') { ticketText = '200mm x 68.88mm'; }
      else if(format === 'a4-v-3x1') { ticketText = '190mm x 92mm'; }
      else if(format === 'a4-v-4x1') { ticketText = '190mm x 69.38mm'; }
      else if(format === 'custom') {
          // Si la selección personalizada coincide con una plantilla rápida, usar la medida fija
          for (const keyTpl in quickTemplates) {
              const tpl = quickTemplates[keyTpl];
              if (tpl.page === page && tpl.orientation === orientation && tpl.cols === cols && tpl.rows === rows) {
                  ticketText = tpl.ticket;
                  break;
              }
          }
      }

      // Cantidad de tickets
      let ticketCount = cols * rows;

      $('#sheet-size').text(sheetText);
      $('#ticket-size').text(ticketText);
      $('#ticket-count').text(ticketCount);

      // === NUEVO: Calcular y mostrar medidas reales según orientación ===
      let key__ = `${cols}x${rows}`;
      let ticketObj__ = ticketSizes[page][orientation][key__];
      let ticketW = ticketObj__ ? parseFloat(ticketObj__.w) : null;
      let ticketH = ticketObj__ ? parseFloat(ticketObj__.h) : null;
      let ticketText__ = (ticketW && ticketH) ? `${ticketW}mm x ${ticketH}mm` : '-';

      // Casos especiales de plantillas rápidas
      if(format === 'a3-h-3x2') { ticketText__ = '200mm x 92mm'; ticketW = 200; ticketH = 92; }
      else if(format === 'a3-h-4x2') { ticketText__ = '200mm x 68.88mm'; ticketW = 200; ticketH = 68.88; }
      else if(format === 'a4-v-3x1') { ticketText__ = '190mm x 92mm'; ticketW = 190; ticketH = 92; }
      else if(format === 'a4-v-4x1') { ticketText__ = '190mm x 69.38mm'; ticketW = 190; ticketH = 69.38; }
      else if(format === 'custom') {
          for (const keyTpl in quickTemplates) {
              const tpl = quickTemplates[keyTpl];
              if (tpl.page === page && tpl.orientation === orientation && tpl.cols === cols && tpl.rows === rows) {
                  ticketText__ = tpl.ticket;
                  [ticketW, ticketH] = tpl.ticket.split('x').map(v => parseFloat(v));
                  break;
              }
          }
      }

      $('#ticket-size').text(ticketText__);

      // Actualizar tamaño de la caja de diseño
      console.log(ticketW,ticketH);
      if (ticketW && ticketH) {
          $('.format-box').css({width: ticketW+'mm', height: ticketH+'mm'});
          $('.format-box-btn').css({width: Math.max(ticketW + 20, 270)+'mm'});
      }
  }

  // Llamar al cargar y al cambiar cualquier campo relevante
  $(document).ready(function() {
      updateTicketInfo();
      $('#format,#page,#rows,#cols,#orientation').on('change keyup', updateTicketInfo);
  });
  // === FIN BLOQUE NUEVO ===

// === FUNCIÓN PARA GUARDAR TODO EL DISEÑO ===
function collectDesignData() {
  // Paso 1: Configuración de formato
  const format = $('#format').val();
  const page = $('#page').val();
  const rows = parseInt($('#rows').val());
  const cols = parseInt($('#cols').val());
  const orientation = $('#orientation').val();
  const margin_up = parseFloat($('#margin-up').val());
  const margin_right = parseFloat($('#margin-right').val());
  const margin_left = parseFloat($('#margin-left').val());
  const margin_top = parseFloat($('#margin-top').val());
  const identation = parseFloat($('#identation').val());
  const matrix_box = parseFloat($('#matrix-box').val());
  const margin_custom = parseFloat($('#margin-custom').val());
  const horizontal_space = parseFloat($('#page-rigth').val());
  const vertical_space = parseFloat($('#page-bottom').val());

  // Quitar 'resize: both;' antes de guardar
  $('#step-2 .format-box .elements').each(function() {
    $(this).css('resize', '');
  });
  $('#step-3 .format-box .elements').each(function() {
    $(this).css('resize', '');
  });
  $('#step-4 .format-box .elements').each(function() {
    $(this).css('resize', '');
  });

  // Paso 2, 3, 4: HTML de los diseños (guardar el elemento .format-box completo)
  const participation_html = $('#step-2 .format-box')[0]?.outerHTML || '';
  const cover_html = $('#step-3 .format-box')[0]?.outerHTML || '';
  const back_html = $('#step-4 .format-box')[0]?.outerHTML || '';

  // Fondos y colores de cada paso
  const backgrounds = {
    step2: {
      color: localStorage.getItem('bg-step2') || '#dfdfdf',
      image: localStorage.getItem('bgimg-step2') || ''
    },
    step3: {
      color: localStorage.getItem('bg-step3') || '#dfdfdf',
      image: localStorage.getItem('bgimg-step3') || ''
    },
    step4: {
      color: localStorage.getItem('bg-step4') || '#dfdfdf',
      image: localStorage.getItem('bgimg-step4') || ''
    }
  };

  // Paso 5: Configuración de salida
  const draw_guides = $('#guides').is(':checked');
  const guide_color = $('#guide_color').val();
  const guide_weight = parseFloat($('#guide_weight').val());
  const participations_per_book = parseInt($('#participation_number').val());
  const generate_mode = $('input[name="generate_mode"]:checked').val();
  const participation_from = parseInt($('#participation_from').val());
  const participation_to = parseInt($('#participation_to').val());
  const documents_mode = $('input[name="documents_mode"]:checked').val();
  const pages_per_document = parseInt($('#participation_page').val());

  return {
    set_id: {{ $set->id ?? 'null' }},
    format,
    page,
    rows,
    cols,
    orientation,
    margins: {
      up: margin_up,
      right: margin_right,
      left: margin_left,
      top: margin_top
    },
    identation,
    matrix_box,
    margin_custom,
    horizontal_space,
    vertical_space,
    participation_html,
    snapshot_path,
    cover_html,
    back_html,
    backgrounds,
    output: {
      draw_guides,
      guide_color,
      guide_weight,
      participations_per_book,
      generate_mode,
      participation_from,
      participation_to,
      documents_mode,
      pages_per_document
    }
  };
}

function showDesignLoading(msg) {
  $('#design-loading-text').text(msg || 'Procesando...');
  $('#design-loading-overlay').css('display', 'flex').show();
}
function hideDesignLoading() {
  $('#design-loading-overlay').hide();
}

// --- Enviar datos al backend al guardar ---
$('#edit-format-form').on('submit', function(e) {
  e.preventDefault();
  const data = collectDesignData();

  showDesignLoading('Guardando diseño...');
  fetch($(this).attr('action'), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    if(result.success) {
      alert('Diseño guardado correctamente.');
    } else {
      alert('Error al guardar el diseño.');
    }
  })
  .catch(() => alert('Error al guardar el diseño.'))
  .finally(() => hideDesignLoading());


  {{-- $.ajax({
    url: $(this).attr('action'),
    method: 'POST',
    data: {
      _token: $('input[name="_token"]').val(),
      _method: 'PUT',
      data: JSON.stringify(data)
    },
    success: function(resp) {
      // Redirigir o mostrar mensaje
        alert('Diseño guardado correctamente');
      if(resp.success) {
        window.location.href = resp.redirect || window.location.href;
      } else {
        alert('Error al guardar el diseño.');
      }
    },
    error: function() {
      alert('Error al guardar el diseño.');
    }
  }); --}}
});

$(document).ready(function() {
    showStep(step);
    
    // Vincular eventos inicialmente cuando se carga el contenido HTML
    setTimeout(function() {
        reapplyElementEvents();
        // Guardar estado inicial del paso
        if ($('#containment-wrapper'+step).length) {
            setTimeout(() => {
                saveHistoryState();
                updateUndoRedoButtons();
            }, 100);
        }
    }, 500);
    
    // --- Botones navegación ---
    $('.next-step').attr('type', 'button');
    $('.prev-step').attr('type', 'button');
    $('#step').attr('type', 'button');
    {{-- $('#save-step').attr('type', 'submit'); --}}
    $('.next-step').click(function(e) {
        e.preventDefault();
        if (step < 5) {
            step++;
            showStep(step);
            setTimeout(function() {
                reapplyElementEvents();
                if ($('#containment-wrapper'+step).length) {
                    setTimeout(() => {
                        saveHistoryState();
                        updateUndoRedoButtons();
                    }, 100);
                }
            }, 100);

            if (step == 2) {
                let format = $('#format').val();
                let w = 200;
                let h = 92;
                let orientation = $('#orientation').val();
                if (format != 'custom') {
                    if (format == 'a3-h-3x2') {
                        w = 200;
                        h = 92;
                    }
                    else if (format == 'a3-h-4x2') {
                        w = 200;
                        h = 68.88;
                    }
                    else if (format == 'a4-v-3x1') {
                        w = 190;
                        h = 92;
                    }
                    else if (format == 'a4-v-4x1') {
                        w = 190;
                        h = 69.38;
                    }

                    $('[id*="containment-wrapper"]').parent().css({
                        width: w+'mm',
                        height: h+'mm'
                    });
                    $('.format-box-btn').css('width', '250mm');
                }
                let matrix = $('#matrix-box').val() ?? 40;
                $('#containment-wrapper4').css('padding-right', matrix+'mm');
            }
        }else{
            $('#edit-format-form').submit();
        }
    });
    $('.prev-step').click(function(e) {
        e.preventDefault();
        if (step > 1) {
            step--;
            showStep(step);
            setTimeout(function() {
                reapplyElementEvents();
                if ($('#containment-wrapper'+step).length) {
                    setTimeout(() => {
                        saveHistoryState();
                        updateUndoRedoButtons();
                    }, 100);
                }
            }, 100);
        }else{
            window.open('{{url('design')}}','_self');
        }
    });
    $('.form-wizard-element').click(function() {
        const id = $(this).attr('id');
        const newStep = parseInt(id.replace('bc-step-', ''));
        step = newStep;
        showStep(step);
        setTimeout(function() {
            reapplyElementEvents();
            if ($('#containment-wrapper'+step).length) {
                setTimeout(() => {
                    saveHistoryState();
                    updateUndoRedoButtons();
                }, 100);
            }
        }, 100);
    });
    
    // Botón Desplegar/Ocultar márgenes
    $('#marginsCollapse').on('show.bs.collapse', function() {
        $('#btn-desplegar-margenes').text('Ocultar');
    }).on('hide.bs.collapse', function() {
        $('#btn-desplegar-margenes').text('Desplegar');
    });
    
    // Controles de zoom
    $(document).on('click', '.design-zoom-in', function() {
      var i = designZoomSteps.indexOf(designZoom);
      if (i < 0) { for (i = 0; i < designZoomSteps.length && designZoomSteps[i] < designZoom; i++); i = Math.min(i, designZoomSteps.length - 1); }
      if (i < designZoomSteps.length - 1) { designZoom = designZoomSteps[i + 1]; applyDesignZoom(); }
    });
    $(document).on('click', '.design-zoom-out', function() {
      var i = designZoomSteps.indexOf(designZoom);
      if (i < 0) { for (i = designZoomSteps.length - 1; i >= 0 && designZoomSteps[i] > designZoom; i--); i = Math.max(i, 0); }
      if (i > 0) { designZoom = designZoomSteps[i - 1]; applyDesignZoom(); }
    });
    
    // --- Botones de agregar elementos ---
    $('.add-text').off('click').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#containment-wrapper'+step).append(`<div class="elements text" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 0"><button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button><span>Escribe aquí...</span></div>`);
        reapplyElementEvents();
        saveHistoryState();
        updateUndoRedoButtons();
        return false;
    });
    $('.add-image').off('click').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#containment-wrapper'+step).append(`<div class="elements images" style="resize: both; overflow: hidden; position: absolute; top: 0"><span><img style="width: 100%; height: 100%" src="{{url('default.jpg')}}" alt=""></span><button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button></div>`);
        reapplyElementEvents();
        saveHistoryState();
        updateUndoRedoButtons();
        return false;
    });
    $('.add-top').off('click').on('click', function (e) {
        e.preventDefault();
        $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; top: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);
        $('.elements.context').unbind('dblclick', deleteElements);
        $('.elements.context').dblclick(deleteElements);
        reapplyElementEvents();
        saveHistoryState();
        updateUndoRedoButtons();
    });
    $('.add-bottom').off('click').on('click', function (e) {
        e.preventDefault();
        $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; bottom: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);
        $('.elements.context').unbind('dblclick', deleteElements);
        $('.elements.context').dblclick(deleteElements);
        reapplyElementEvents();
        saveHistoryState();
        updateUndoRedoButtons();
    });
    // --- Botón de fondo ---
    $(document).on('click', '#open-bg-modal', function() {
        const color = localStorage.getItem('bg-step'+step) || '#dfdfdf';
        const img = localStorage.getItem('bgimg-step'+step) || null;
        $('#background-color').val(color);
        $('#background-image').val('');
        if(img) {
          $('#bg-preview').css('background-image', 'url('+img+')');
        } else {
          $('#bg-preview').css('background-image', 'none');
        }
        $('#bg-preview').css('background-color', color);
        $('#background-modal').modal('show');
    });
    // --- Botón toggle-guide ---
    $('.toggle-guide').off('click').on('click', function (e) {
        e.preventDefault();
        let opacity = $('.guide'+step).css('opacity');
        $('.guide'+step).css('opacity', opacity == 1 ? 0 : 1);
    });
    // --- Botón color-guide ---
    $('.color-guide input').off('change').on('change', function (e) {
        e.preventDefault();
        localStorage.setItem('guide-step'+step,$(this).val());
        $('.guide'+step).css('border-color',$(this).val());
    });
    reapplyElementEvents();
    
    // Las funciones de undo/redo están definidas fuera de este bloque
    
    // Botones Undo/Redo
    $('.undo-btn').off('click').on('click', function(e) {
      e.preventDefault();
      undo();
    });
    
    $('.redo-btn').off('click').on('click', function(e) {
      e.preventDefault();
      redo();
    });
    
    // Botones de capas (up-layer, down-layer)
    $('.up-layer').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement) {
        if (selectedElement.hasClass('element-critical')) return;
        let zindex = parseInt(selectedElement.css('z-index')) || 0;
        if (zindex >= 9999) return;
        selectedElement.css('z-index', zindex + 1);
      }
    });
    
    $('.down-layer').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement) {
        let zindex = parseInt(selectedElement.css('z-index')) || 0;
        if (zindex > 0) selectedElement.css('z-index', zindex - 1);
      }
    });
    
    // Botón eliminar elemento
    $('.delete-element-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement) {
        if (selectedElement.hasClass('element-critical')) {
          alert('Este elemento es obligatorio y no se puede eliminar.');
          return;
        }
        selectedElement.remove();
        selectedElement = null;
        $('.up-layer, .down-layer, .delete-element-btn, .text-style-btn').prop('disabled', true);
        $('#save-step').removeClass('d-none');
        $('#step').addClass('d-none');
        saveHistoryState();
        updateUndoRedoButtons();
      }
    });
    
    // Botones de estilo de texto
    $('.bold-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.find('span').toggleClass('text-bold');
      }
    });
    
    $('.italic-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.find('span').toggleClass('text-italic');
      }
    });
    
    $('.underline-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.find('span').toggleClass('text-underline');
      }
    });
    
    $('.strike-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.find('span').toggleClass('text-strike');
      }
    });
    
    $('.align-left-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.removeClass('text-center text-right').addClass('text-left');
      }
    });
    
    $('.align-center-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.removeClass('text-left text-right').addClass('text-center');
      }
    });
    
    $('.align-right-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        selectedElement.removeClass('text-left text-center').addClass('text-right');
      }
    });
    
    $('.font-size-up-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        let span = selectedElement.find('span');
        let currentSize = parseInt(span.css('font-size')) || 14;
        span.css('font-size', (currentSize + 2) + 'px');
      }
    });
    
    $('.font-size-down-btn').off('click').on('click', function(e) {
      e.preventDefault();
      if (selectedElement && selectedElement.hasClass('text')) {
        let span = selectedElement.find('span');
        let currentSize = parseInt(span.css('font-size')) || 14;
        span.css('font-size', Math.max(8, currentSize - 2) + 'px');
      }
    });
    
    // --- Subir/bajar z-index (mantener compatibilidad con actualElement) ---
    $('.up-z').off('click').on('click', function (e) {
        e.preventDefault();
        let zindex = $(actualElement).css('z-index') || 1;
        zindex = parseInt(zindex)+1;
        $(actualElement).css('z-index',zindex);
    });
    $('.dw-z').off('click').on('click', function (e) {
        e.preventDefault();
        let zindex = $(actualElement).css('z-index') || 1;
        zindex = parseInt(zindex)-1;
        $(actualElement).css('z-index',zindex);
    });
    // --- Eventos y funciones de guardado visual (copiados de format.blade.php) ---
    $('.deleteElements').off('click').on('click', function (e) {
        e.preventDefault();
        if (actualElement && actualElement.hasClass('element-critical')) {
            alert('Este elemento es obligatorio y no se puede eliminar.');
            return;
        }
        if (confirm('¿Desea eliminar el elemento seleccionado?')) {
            if (actualElement) actualElement.remove();
            $('#step').addClass('d-none');
            $('#save-step').removeClass('d-none');
        }
    });
    // Suprimir / Backspace: eliminar elemento seleccionado (salvo en inputs)
    $(document).off('keydown.designDelete').on('keydown.designDelete', function(e) {
        if (e.key !== 'Delete' && e.key !== 'Backspace') return;
        if ($(e.target).closest('input, textarea, select, [contenteditable="true"]').length) return;
        if (!selectedElement || !selectedElement.length) return;
        if (selectedElement.hasClass('element-critical')) {
            e.preventDefault();
            alert('Este elemento es obligatorio y no se puede eliminar.');
            return;
        }
        e.preventDefault();
        if (confirm('¿Desea eliminar el elemento seleccionado?')) {
            selectedElement.remove();
            selectedElement = null;
            actualElement = null;
            $('.up-layer, .down-layer, .delete-element-btn, .text-style-btn').prop('disabled', true);
            $('#step').addClass('d-none');
            $('#save-step').removeClass('d-none');
            saveHistoryState();
            updateUndoRedoButtons();
        }
    });
    $('.accept-text').off('click').on('click', function(event) {
        if (editor && CKEDITOR.instances['editor']) {
            var data = CKEDITOR.instances['editor'].getData();
            data = data.replace(/<p>&nbsp;<\/p>/gi, '').replace(/<p><\/p>/gi, '');
            $(actualElement).find('span').html(data);
            CKEDITOR.instances['editor'].destroy(true);
        }
        $('#ckeditor-modal').modal('hide');
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
        saveHistoryState();
        updateUndoRedoButtons();
        // Re-vincular eventos después de editar
        reapplyElementEvents();
    });
    const input = document.getElementById('imageInput');
    $('.accept-image').off('click').on('click', function (e) {
        e.preventDefault();
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.type.startsWith('image/')) {
                uploadImage(file);
            } else {
                console.log("El archivo seleccionado no es una imagen.");
            }
        }
    });
    
    $('.accept-qr').off('click').on('click', function (e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append('text', $('#qr-text').val());
        showDesignLoading('Generando código QR...');
        fetch('{{url('api/generarQr')}}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                $(actualElement).find('img').attr('src', data.url);
                $('#qr-modal').modal('hide');
                $('#qr-text').val("");
                $('#step').addClass('d-none');
                $('#save-step').removeClass('d-none');
            }
        })
        .catch(error => console.error('Error al subir la imagen:', error))
        .finally(() => hideDesignLoading());
    });
});

function reapplyElementEvents() {
    // Asegurar que los botones edit-btn existan en los elementos
    $('.elements.text').each(function() {
      if ($(this).find('.edit-btn').length === 0) {
        $(this).prepend('<button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>');
      }
    });
    
    $('.elements.images').each(function() {
      if ($(this).find('.edit-btn').length === 0) {
        $(this).append('<button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button>');
      }
    });
    
    // Compensación de arrastre con zoom
    var dragClickOffsetX, dragClickOffsetY;
    $( ".elements" ).draggable({ 
      handle: 'span', 
      containment: "#containment-wrapper"+step, 
      scroll: false, 
      start: function(event, ui){
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
        if (typeof designZoom !== 'undefined' && designZoom !== 1) {
          var el = ui.helper[0];
          var r = el.getBoundingClientRect();
          dragClickOffsetX = (event.clientX - r.left) / designZoom;
          dragClickOffsetY = (event.clientY - r.top) / designZoom;
        }
        saveHistoryState();
        updateUndoRedoButtons();
      },
      drag: function(event, ui) {
        if (typeof designZoom !== 'undefined' && designZoom !== 1) {
          var containment = document.getElementById('containment-wrapper' + step);
          if (containment) {
            var cr = containment.getBoundingClientRect();
            var mouseLogicalX = (event.clientX - cr.left) / designZoom;
            var mouseLogicalY = (event.clientY - cr.top) / designZoom;
            ui.position.left = mouseLogicalX - dragClickOffsetX;
            ui.position.top = mouseLogicalY - dragClickOffsetY;
          }
        }
      },
      stop: function() {
        saveHistoryState();
        updateUndoRedoButtons();
      }
    });
    $('.elements.participation, .elements.reference, .elements.qr, .elements.number, .elements.mini').addClass('element-critical');
    
    // Vincular eventos de los botones edit-btn (con prevención de propagación)
    $('.elements.text .edit-btn').off('click', editelements).on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        editelements.call(this, e);
        return false;
    });
    $('.elements.images .edit-btn').off('click', changeImage).on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        changeImage.call(this, e);
        return false;
    });
    
    // Vincular eventos de doble clic
    $('.elements.text').off('dblclick', editelements).on('dblclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        editelements.call(this, e);
        return false;
    });
    $('.elements.context').off('dblclick', deleteElements).on('dblclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        deleteElements.call(this, e);
        return false;
    });
    $('.elements.images').off('dblclick', changeImage).on('dblclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        changeImage.call(this, e);
        return false;
    });
    $('.elements.qr').off('dblclick', setQRtext).on('dblclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        setQRtext.call(this, e);
        return false;
    });
    
    addEventsElement();
    configMargins();
}

// --- Funciones para el fondo de ticket (copiadas de format.blade.php) ---
$(document).on('input', '#background-color', function() {
  $('#bg-preview').css('background-color', $(this).val());
});
$(document).on('change', '#background-image', function(e) {
  if(this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = function(ev) {
      $('#bg-preview').css('background-image', 'url('+ev.target.result+')');
    };
    reader.readAsDataURL(this.files[0]);
  }
});
$(document).on('click', '#remove-bg-image', function() {
  $('#bg-preview').css('background-image', 'none');
  $('#background-image').val('');
  localStorage.removeItem('bgimg-step'+step);
});
$(document).on('click', '#apply-bg', function() {
  const color = $('#background-color').val();
  let img = '';
  if($('#background-image')[0].files && $('#background-image')[0].files[0]) {
    const file = $('#background-image')[0].files[0];
    const formData = new FormData();
    formData.append('image', file);
    showDesignLoading('Subiendo imagen...');
    fetch('{{url('api/upload-image')}}', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if(data.url) {
        img = data.url;
        localStorage.setItem('bgimg-step'+step, img);
        setBgToContainment(color, img);
        $('#background-modal').modal('hide');
      }
    })
    .finally(() => hideDesignLoading());
  } else {
    img = localStorage.getItem('bgimg-step'+step) || '';
    setBgToContainment(color, img);
    $('#background-modal').modal('hide');
  }
  localStorage.setItem('bg-step'+step, color);
});
function setBgToContainment(color, img) {
  const $cont = $('#containment-wrapper'+step);
  $cont.css('background-color', color);
  if(img) {
    // Asegurar que la URL de la imagen sea absoluta
    let imageUrl = img;
    if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
      imageUrl = '/' + imageUrl;
    }
    
    $cont.css('background-image', 'url('+imageUrl+')');
    $cont.css('background-size', 'cover');
    $cont.css('background-position', 'center');
    $cont.css('background-repeat', 'no-repeat');
    
    // Forzar repaint del elemento
    $cont[0].offsetHeight;
  } else {
    $cont.css('background-image', 'none');
  }
}

$('.elements').each(function() {
  $(this).css('resize', 'both');
});

// Cargar imágenes de fondo existentes al inicializar
function loadExistingBackgrounds() {
  // Cargar fondos para cada paso
  for (let i = 2; i <= 4; i++) {
    const color = localStorage.getItem('bg-step' + i) || '#dfdfdf';
    const img = localStorage.getItem('bgimg-step' + i);
    
    if (img || color !== '#dfdfdf') {
      const $cont = $('#containment-wrapper' + i);
      if ($cont.length) {
        $cont.css('background-color', color);
        if (img) {
          let imageUrl = img;
          if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
            imageUrl = '/' + imageUrl;
          }
          $cont.css('background-image', 'url(' + imageUrl + ')');
          $cont.css('background-size', 'cover');
          $cont.css('background-position', 'center');
          $cont.css('background-repeat', 'no-repeat');
        }
      }
    }
  }
}

// Ejecutar al cargar la página
$(document).ready(function() {
  loadExistingBackgrounds();
});

// Función para debuggear problemas con imágenes de fondo
function debugBackgroundImage(step) {
  const $cont = $('#containment-wrapper' + step);
  const bgImage = $cont.css('background-image');
  const bgColor = $cont.css('background-color');
  const bgSize = $cont.css('background-size');
  
  console.log('Debug fondo paso ' + step + ':');
  console.log('- Imagen:', bgImage);
  console.log('- Color:', bgColor);
  console.log('- Tamaño:', bgSize);
  console.log('- Elemento:', $cont[0]);
}

// Agregar función de debug al modal de fondo
$(document).on('click', '#open-bg-modal', function() {
  debugBackgroundImage(step);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
@endsection 