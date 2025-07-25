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
            if(is_array($numbers)) {
                return implode(' - ', array_map(function($n) {
                    return str_pad(number_format((int)$n, 0, '', '.'), 6, '0', STR_PAD_LEFT);
                }, $numbers));
            }
            return str_pad(number_format((int)$numbers, 0, '', '.'), 6, '0', STR_PAD_LEFT);
        }
    }
@endphp

<style>
    input[disabled],select[disabled] {
        background-color: #cfcfcf !important;
    }
    .qr span {
        width: 100%;
        height: 100%;
        display: block;
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
                                                            <b>Medidas de cada ticket:</b> <span id="ticket-size">-</span><br>
                                                            <b>Cantidad de tickets por hoja:</b> <span id="ticket-count">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4 class="mb-0 mt-1">Configurar márgenes</h4>
                                                <br>
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
                                                                <input class="form-control" name="margin_custom" type="number" value="{{ old('margin_custom', $format->margin_custom ?? '') }}" step="0.1" placeholder="0.00" style="border-radius: 30px">
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
                                    <div class="format-box-btn" style="width: 200mm; height: 54px; margin: auto;">
                                        <br>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-dark add-text" data-id="2" type="button">Texto</button>
                                            <button class="btn btn-sm btn-dark add-image" data-id="2" type="button">Imagen</button>
                                            <button class="btn btn-sm btn-dark" id="open-bg-modal" type="button">Fondo ticket</button>
                                            <button class="btn btn-sm btn-dark toggle-guide" data-id="2" type="button">Guias</button>
                                            <label class="btn btn-sm btn-dark color-guide" style="position: relative;" data-id="2" type="button">
                                                Color Guias<input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                            </label>
                                        </div>
                                        <br>
                                        <div class="format-box" style="border:1px solid #c8c8c8; width: 200mm; height: 92mm; margin: auto; position: relative;">
                                            <div id="containment-wrapper2" style="width: 100%; height: calc(100% - 0mm); background-size: cover; background-position: center;">
                                                {!! $format->participation_html ?? '' !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-card fade bs d-none" id="step-3" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">Diseñar Portada</h4>
                                    <small><i>Edita el diseño de la portada</i></small>
                                    <br>
                                    <div class="format-box-btn" style="width: 200mm; height: 54px; margin: auto;">
                                        <br>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-dark add-text" data-id="3" type="button">Texto</button>
                                            <button class="btn btn-sm btn-dark add-image" data-id="3" type="button">Imagen</button>
                                            <button class="btn btn-sm btn-dark" id="open-bg-modal" type="button">Fondo ticket</button>
                                            <button class="btn btn-sm btn-dark add-top" data-id="3" type="button">Arriba</button>
                                            <button class="btn btn-sm btn-dark add-bottom" data-id="3" type="button">Abajo</button>
                                            <button class="btn btn-sm btn-dark toggle-guide" data-id="2" type="button">Guias</button>
                                            <label class="btn btn-sm btn-dark color-guide" style="position: relative;" data-id="2" type="button">
                                                Color Guias<input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                            </label>
                                        </div>
                                        <br>
                                        <div class="format-box" style="border:1px solid #c8c8c8; width: 200mm; height: 92mm; margin: auto; position: relative;">
                                            <div id="containment-wrapper3" style="width: 100%; height: calc(100% - 0mm); background-size: cover; background-position: center;">
                                                {!! $format->cover_html ?? '' !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-card fade bs d-none" id="step-4" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">Diseñar Trasera</h4>
                                    <small><i>Edita el diseño de la trasera</i></small>
                                    <br>
                                    <div class="format-box-btn" style="width: 200mm; height: 54px; margin: auto;">
                                        <br>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-dark add-text" data-id="4" type="button">Texto</button>
                                            <button class="btn btn-sm btn-dark add-image" data-id="4" type="button">Imagen</button>
                                            <button class="btn btn-sm btn-dark" id="open-bg-modal" type="button">Fondo ticket</button>
                                            <button class="btn btn-sm btn-dark add-top" data-id="4" type="button">Arriba</button>
                                            <button class="btn btn-sm btn-dark add-bottom" data-id="4" type="button">Abajo</button>
                                            <button class="btn btn-sm btn-dark toggle-guide" data-id="2" type="button">Guias</button>
                                            <label class="btn btn-sm btn-dark color-guide" style="position: relative;" data-id="2" type="button">
                                                Color Guias<input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                            </label>
                                        </div>
                                        <br>
                                        <div class="format-box" style="border:1px solid #c8c8c8; width: 200mm; height: 92mm; margin: auto; position: relative;">
                                            <div id="containment-wrapper4" style="width: 100%; height: calc(100% - 0mm); background-size: cover; background-position: center;">
                                                {!! $format->back_html ?? '' !!}
                                            </div>
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
                                                <input class="form-control" type="number" name="participation_to" value="{{ old('participation_to', $format->output['participation_to'] ?? 600) }}" id="participation_to" style="border-radius: 30px">
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
                            </div>
                        </div>
                        <div class="row mt-4">
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
        <h5 class="modal-title" id="backgroundModalLabel">Seleccionar fondo del ticket</h5>
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
    var contenidoHTML = $(this).html();
    actualElement = $(this);
    if (editor && CKEDITOR.instances['editor']) {
        CKEDITOR.instances['editor'].destroy(true);
    }
    $('#editor').html(contenidoHTML);
    editor = CKEDITOR.replace('editor', {});
    $('#ckeditor-modal').modal('show');
}
function deleteElements(event) {
    let element = $(this);
    if (confirm('¿Desea eliminar el elemento seleccionado?')) {
        element.remove();
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
    }
}
function changeImage(event) {
    actualElement = $(this);
    $('#imagen-modal').modal('show');
}
function setQRtext(event) {
    actualElement = $(this);
    $('#qr-modal').modal('show');
}
// --- Sincronizar step con la edición ---
var step = 1;
var editor;
var actualElement;

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
}

function addEventsElement() {
    $('.elements').unbind('contextmenu',changePositionElement);
    $('.elements').contextmenu(changePositionElement);
}

function changePositionElement(event) {
    event.preventDefault();
    actualElement = $(this);
    $('#position-modal').modal('show');
}

$('#save-step').click(function(event) {

    if (step != 1) {

      
      let html = $('#containment-wrapper'+step).html();

      localStorage.setItem('step'+step,html);

      $('#step').removeClass('d-none');
      $('#save-step').addClass('d-none');

    }
});

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
    horizontal_space,
    vertical_space,
    participation_html,
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

// --- Enviar datos al backend al guardar ---
$('#edit-format-form').on('submit', function(e) {
  // Prevenir submit normal
  e.preventDefault();
  const data = collectDesignData();
  // Puedes hacer un POST AJAX o poner los datos en un input hidden
  // Aquí ejemplo con AJAX:

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
      {{-- window.open('{{url('design?table=1')}}','_self'); --}}
      // Puedes redirigir o mostrar un mensaje aquí
    } else {
      alert('Error al guardar el diseño.');
    }
  })
  .catch(() => alert('Error al guardar el diseño.'));


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
            reapplyElementEvents();
        }else{
            $('#edit-format-form').submit();
        }
    });
    $('.prev-step').click(function(e) {
        e.preventDefault();
        if (step > 1) {
            step--;
            showStep(step);
            reapplyElementEvents();
        }else{
            window.open('{{url('design')}}','_self');
        }
    });
    $('.form-wizard-element').click(function() {
        const id = $(this).attr('id');
        const newStep = parseInt(id.replace('bc-step-', ''));
        step = newStep;
        showStep(step);
        reapplyElementEvents();
    });
    // --- Botones de agregar elementos ---
    $('.add-text').off('click').on('click', function (e) {
        e.preventDefault();
        $('#containment-wrapper'+step).append(`<div class="elements text" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 0"><span>Escribe aquí...</span></div>`);
        reapplyElementEvents();
    });
    $('.add-image').off('click').on('click', function (e) {
        e.preventDefault();
        $('#containment-wrapper'+step).append(`<div class="elements images" style="resize: both; overflow: hidden; position: absolute; top: 0"><span><img style="width: 100%; height: 100%" src="{{url('default.jpg')}}" alt=""></span></div>`);
        reapplyElementEvents();
    });
    $('.add-top').off('click').on('click', function (e) {
        e.preventDefault();
        $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; top: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);
        reapplyElementEvents();
    });
    $('.add-bottom').off('click').on('click', function (e) {
        e.preventDefault();
        $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; bottom: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);
        reapplyElementEvents();
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
    // --- Subir/bajar z-index ---
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
        if (confirm('¿Desea eliminar el elemento seleccionado?')) {
            if (actualElement) actualElement.remove();
            $('#step').addClass('d-none');
            $('#save-step').removeClass('d-none');
        }
    });
    $('.accept-text').off('click').on('click', function(event) {
        if (editor && CKEDITOR.instances['editor']) {
            var data = CKEDITOR.instances['editor'].getData();
            $(actualElement).find('span').html(data);
            CKEDITOR.instances['editor'].destroy(true);
        }
        $('#ckeditor-modal').modal('hide');
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
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
        fetch('/api/generarQr', {
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
        .catch(error => console.error('Error al subir la imagen:', error));
    });
    function uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        fetch('/api/upload-image', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                $(actualElement).find('img').attr('src', data.url);
                $('#imagen-modal').modal('hide');
                input.value = null;
                $('#step').addClass('d-none');
                $('#save-step').removeClass('d-none');
            }
        })
        .catch(error => console.error('Error al subir la imagen:', error));
    }
});

function reapplyElementEvents() {
    $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });    
    $('.elements.text').unbind('dblclick',editelements).dblclick(editelements);
    $('.elements.context').unbind('dblclick',deleteElements).dblclick(deleteElements);
    $('.elements.images').unbind('dblclick',changeImage).dblclick(changeImage);
    $('.elements.qr').unbind('dblclick',setQRtext).dblclick(setQRtext);
    addEventsElement();
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
    // Subir imagen al servidor
    const file = $('#background-image')[0].files[0];
    const formData = new FormData();
    formData.append('image', file);
    fetch('/api/upload-image', {
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
    });
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
    $cont.css('background-image', 'url('+img+')');
    $cont.css('background-size', 'cover');
    $cont.css('background-position', 'center');
  } else {
    $cont.css('background-image', 'none');
  }
}

$('.elements').each(function() {
  $(this).css('resize', 'both');
});
</script>
@endsection 