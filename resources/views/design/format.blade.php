@extends('layouts.layout')

@section('title','Diseño e Impresión')

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
    .elements.text {
        position: relative;
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
        border: 1px solid #007bff !important;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
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
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e Impresión</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">

                        <div class="d-flex p-2" style=" align-items: center;justify-content: center;">

                            <div class="form-wizard-element active" style="width: 200px;" id="bc-step-1">
                                
                                <span style="top: -4px; margin-right: 8px;">
                                    1
                                </span>

                                <label>
                                    Configurar <br> Formato
                                </label>

                            </div>

                            <div class="form-wizard-element" style="width: 200px;" id="bc-step-2">
                                
                                <span style="top: -4px; margin-right: 8px;">
                                    2
                                </span>

                                <label>
                                    Diseñar <br> Participación
                                </label>

                            </div>

                            <div class="form-wizard-element" style="width: 200px;" id="bc-step-3">
                                
                                <span style="top: -4px; margin-right: 8px;">
                                    3
                                </span>

                                <label>
                                    Diseñar <br> Portada
                                </label>

                            </div>

                            <div class="form-wizard-element" style="width: 200px;" id="bc-step-4">
                                
                                <span style="top: -4px; margin-right: 8px;">
                                    4
                                </span>

                                <label>
                                    Diseñar <br> Trasera
                                </label>

                            </div>

                            <div class="form-wizard-element" style="width: 200px;" id="bc-step-5">
                                
                                <span style="top: -4px; margin-right: 8px;">
                                    5
                                </span>

                                <label>
                                    Configurar <br> Salida
                                </label>

                            </div>
                            
                        </div>

                    </h4>

                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-card fade show bs" id="step-1" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">
                                    Configuración de Formato
                                </h4>
                                <small><i>Configura el formato de la página y las participaciones</i></small>

                                <br>
                                <br>

                                <div style="min-height: 656px;">

                                    <h4 class="mb-0 mt-1">
                                        Formato de la página
                                    </h4>

                                    <div class="row">
                                        
                                        <div class="col-9">

                                            <div class="row">
                                                
                                                <div class="col-12">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Plantilla rápida</label>

                                                        <div class="input-group input-group-merge group-form">

                                                            <select class="form-control" name="" id="format" style="border-radius: 30px;">
                                                                <option value="a3-h-3x2">A3 - Apaisado - (3x2)</option>
                                                                <option value="a3-h-4x2">A3 - Apaisado - (4x2)</option>
                                                                <option value="a4-v-3x1">A4 - Vertical - (3x1)</option>
                                                                <option value="a4-v-4x1">A4 - Vertical - (4x1)</option>
                                                                <option value="custom">Personalizado</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Tamaño de la página</label>

                                                        <div class="input-group input-group-merge group-form">

                                                            <select class="form-control custom" disabled name="" id="page" style="border-radius: 30px;">
                                                                <option selected value="a3">A3 (297x420)</option>
                                                                <option value="a4">A4 (210x297)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-3">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Número de filas</label>

                                                        <div class="input-group input-group-merge group-form">

                                                            <input class="form-control custom" value="3" disabled type="number" id="rows" min="1" max="5" style="border-radius: 30px;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-3">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Número de columnas</label>

                                                        <div class="input-group input-group-merge group-form">

                                                            <input class="form-control custom" value="2" disabled type="number" id="cols" min="1" max="5" style="border-radius: 30px;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Orientación</label>

                                                        <div class="input-group input-group-merge group-form">

                                                            <select class="form-control custom" disabled name="" id="orientation" style="border-radius: 30px;">
                                                                <option selected value="h">Apaisado</option>
                                                                <option value="v">Vertical</option>
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

                                            <h4 class="mb-0 mt-1">
                                                Configurar márgenes
                                            </h4>

                                            <br>

                                            <div class="row">
                                                
                                                <div class="col-md-12">

                                                    <div class="row mb-3">
                                                        
                                                        <label class="col-form-label label-control col-4 text-end">Márgenes de la página (mm)</label>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="margin-up" value="9.75" step="0.1" placeholder="0,00" style="border-radius: 30px">
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="margin-right" value="10.00" step="0.1" placeholder="0,00" style="border-radius: 30px">
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="margin-left" value="10.00" step="0.1" placeholder="0,00" style="border-radius: 30px">
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="margin-top" value="10.00" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                        </div>

                                                    </div>

                                                    <div class="row mb-3">
                                                        
                                                        <label class="col-form-label label-control col-4 text-end">
                                                            Sangres de la imagen (mm)
                                                        </label>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="identation" value="2.50" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                        </div>

                                                    </div>

                                                    <div class="row mb-3">
                                                        
                                                        <label class="col-form-label label-control col-4 text-end">
                                                            Anchura de la matriz (mm)
                                                        </label>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="matrix-box" value="40.00" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <span class="d-block mt-1">
                                                                (Incluyendo sangres)
                                                            </span>
                                                        </div>

                                                    </div>

                                                    <div class="row mb-3">
                                                        
                                                        <label class="col-form-label label-control col-4 text-end">
                                                            Márgenes de la página (mm)
                                                        </label>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="margin-custom" value="12.50" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                        </div>

                                                    </div>

                                                    <div class="row mb-3">
                                                        
                                                        <label class="col-form-label label-control col-4 text-end">
                                                            Espacio horizontal entre participaciones (mm)
                                                        </label>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="page-rigth" value="0.00" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                        </div>

                                                    </div>

                                                    <div class="row mb-3">
                                                        
                                                        <label class="col-form-label label-control col-4 text-end">
                                                            Espacio vertical entre participaciones (mm)
                                                        </label>

                                                        <div class="col-sm-2">
                                                            <input class="form-control" type="number" id="page-bottom" value="0.00" step="0.1" placeholder="0.00" style="border-radius: 30px">
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
                                            <a href="javascript:;" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
                                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></a>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="form-card fade bs d-none" id="step-2" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">
                                    Configuración de Formato
                                </h4>
                                <small><i>Configura el formato de la página y las participaciones</i></small>

                                <br>

                                {{-- <div style="overflow: auto; height: 658px; width: 100%;"> --}}

                                <div class="format-box-btn" style="width: 250mm; height: 54px; margin: auto; padding-left: 20px;">

                                    <br>

                                    <div class="btn-group format-btn-group" style="width: 250mm; display: flex; justify-content: center; flex-wrap: wrap; gap: 1px;">
                                        <button title="Agregar texto" class="btn btn-sm btn-dark add-text" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-edit-line"></i></button>
                                        <button title="Agregar imagen" class="btn btn-sm btn-dark add-image" data-id="2" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-image-line"></i></button>
                                        {{-- <button class="btn btn-sm btn-dark add-qr" data-id="2" type="button">QR</button> --}}
                                        {{-- <label class="btn btn-sm btn-dark color" style="position: relative;" data-id="2" type="button">
                                            Fondo<input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                        </label> --}}
                                        <button title="Fondo del ticket" class="btn btn-sm btn-dark" id="open-bg-modal" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-palette-line"></i></button>
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

                                    <br>
                                    
                                    <div class="format-box" style="border:1px solid #c8c8c8; width: 200mm; height: 92mm; margin: auto; position: relative;">

                                        {{-- margen izquierdo --}}
                                        <div class="margen-izquierdo guide2" style="opacity: 1; z-index:1;position: absolute; height: 100%; border-left: 1px solid purple; left: 2.5mm;"></div>
                                        {{-- margen arriba --}}
                                        <div class="margen-arriba guide2" style="opacity: 1; z-index:1;position: absolute; width: 100%; border-top: 1px solid purple; top: 2.5mm;"></div>
                                        {{-- margen derecho --}}
                                        <div class="margen-derecho guide2" style="opacity: 1; z-index:1;position: absolute; height: 100%; border-right: 1px solid purple; right: 2.5mm;"></div>
                                        {{-- margen abajo --}}
                                        <div class="margen-abajo guide2" style="opacity: 1; z-index:1;position: absolute; width: 100%; border-bottom: 1px solid purple; bottom: 2.5mm;"></div>
                                        {{-- caja matriz --}}
                                        <div class="caja-matriz guide2" style="opacity: 1; z-index:1;position: absolute; width: 40mm; border-right: 1px solid purple; height: 100%; left: 2.5mm;"></div>

                                        <div id="containment-wrapper2" style="width: 100%; height: calc(100% - 0mm); background-size: cover; background-position: center;"> 



                                              

                                             <div class="elements number text ui-draggable" style="padding: 10px; width: 274px; height: 94px; resize: both; overflow: hidden; position: relative; left: 452px; top: 25.875px;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><h1><span style="color:hsl(0,0%,0%);font-size:{{ getNumberFontSize($reservation_numbers) }};" class="ui-draggable-handle"><strong>{{ is_array($reservation_numbers) ? implode(' - ', $reservation_numbers) : $reservation_numbers }}</strong></span></h1></span>
                                            </div>

                                            {{-- <div class="elements text ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 418px; top: 122.011px; width: 316px; height: 85px;">
                                                <span class="ui-draggable-handle"><h5 style="text-align:center;"><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">El portador de la presente participación juega DOS EUROS&nbsp;</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">en cada número arriba indicado para el sorteo de Loteria Nacional&nbsp;</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">que se celebrará el 22 de Diciembre de 2025&nbsp;</span><br><span style="font-size:10px;" class="ui-draggable-handle">&nbsp;</span></h5></span>
                                                
                                            </div> --}}
                                            
                                            
                                            <div class="elements text ui-draggable" style="padding: 10px; width: 144px; height: 94px; resize: both; overflow: hidden; position: absolute; top: 0px; left: 12px;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><h6 style="text-align:center;"><span style="font-size:20px;" class="ui-draggable-handle"><strong>LOTERÍA</strong></span><br><span style="font-size:20px;" class="ui-draggable-handle"><strong>NACIONAL</strong></span></h6></span>
                                            </div>
                                                <div class="elements text ui-draggable" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 144px; left: 158px;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><h5 style="text-align:center;"><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">DATOS DE LA EMPRESA</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">NOMBRE</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">C/NOMBRE DE LA VIA</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">TELEFONO</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">DATOS</span></h5></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 82px; height: 44px; resize: both; overflow: hidden; position: absolute; top: 144px; left: 42px;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0, 0%, 0%);" class="ui-draggable-handle"><strong>25/07/25</strong></span></p></span>
                                            </div><div class="elements text number mini ui-draggable" style="padding: 10px; width: 74px; height: 43px; resize: both; overflow: hidden; position: absolute; top: 180.797px; left: 51.7969px; z-index: 1001;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-family:Arial, Helvetica, sans-serif;font-size:14px;" class="ui-draggable-handle"><strong>{{ formatMini($reservation_numbers) }}</strong></span></p></span>
                                            </div>
                                                <div class="elements text ui-draggable" style="padding: 10px; width: 120px; height: 90px; resize: both; overflow: hidden; position: absolute; top: 214px; left: 26px;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><h4 style="text-align:center;"><span style="color:hsl(0, 0%, 0%);font-size:26px;" class="ui-draggable-handle"><strong>{{ number_format($set->played_amount, 2, ',', '.') }}€</strong></span><br><span style="color:hsl(0, 0%, 0%);font-size:14px;" class="ui-draggable-handle"><strong>Donativo:</strong></span><br><span style="color:hsl(0, 0%, 0%);font-size:18px;" class="ui-draggable-handle"><strong>{{ number_format($set->donation_amount, 2, ',', '.') }}€</strong></span></h4></span>
                                            </div>
                                                <div class="elements participation text ui-draggable" style="padding: 10px; width: 90px; height: 40px; resize: both; overflow: hidden; position: absolute; top: 300px; left: 94px;">
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Nº 1/0001</strong></span></p></span>
                                            </div>
                                            <div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 44.9659px; top: 68.9773px; height: 78px; width: 76px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="{{url('default.jpg')}}" alt=""></span><button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button></div><div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 184.884px; top: 15.9091px; height: 84px; width: 137px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="{{url('default.jpg')}}" alt=""></span><button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button></div><div class="elements text ui-draggable" style="padding: 10px; width: 298px; height: 78px; resize: both; overflow: hidden; position: absolute; top: 258.815px; left: 162.81px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Caduca a los 3 meses, Premios sujetos a la ley.</strong></span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Nota: Todo talón roto o enmendado será nulo</strong></span></p></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 558px; height: 42px; resize: both; overflow: hidden; position: absolute; top: 304px; left: 172px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%); font-size:6px"><strong>Premios sup. a 2500€ por décimo, tendrán una retención del 20% por encima del importe anterior, que será prorrateada en estas particip. en la proporción correspondiente a su valor nominal.</strong></span></p></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 220px; left: 332px;">
                                            <span class="ui-draggable-handle"><p style="text-align:center;"><span style="color:hsl(0,0%,0%);font-size:26px;" class="ui-draggable-handle"><strong>{{ number_format($set->played_amount, 2, ',', '.') }}€</strong></span><br><span style="color:hsl(0,0%,0%);font-size:14px;" class="ui-draggable-handle"><strong>Donativo:</strong></span><br><span style="color:hsl(0,0%,0%);font-size:18px;" class="ui-draggable-handle"><strong>{{ number_format($set->donation_amount, 2, ',', '.') }}€</strong></span></p></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div><div class="elements participation text ui-draggable" style="padding: 10px; width: 80px; height: 42px; resize: both; overflow: hidden; position: absolute; top: 218px; left: 662px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Nº 1/0001</strong></span></p></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 92px; height: 36px; resize: both; overflow: hidden; position: absolute; top: 247.797px; left: 490.781px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:12px;" class="ui-draggable-handle"><strong>DEPOSITARIO</strong></span></p></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div><div class="elements reference text ui-draggable" style="padding: 10px; width: 227px; height: 40px; resize: both; overflow: hidden; position: absolute; top: 278.688px; left: 459.703px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:12px;" class="ui-draggable-handle"><strong>Nº Ref: 00000000000000000000</strong></span></p></span>
                                            </div>
                                        <div class="elements qr ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 253.562px; left: 666.588px; width: 60px; height: 60px;"><span class="ui-draggable-handle"></span></div>
                                        
                                        </div>

                                    </div>
                                </div>

                                {{-- </div> --}}
                            </div>

                            <div class="form-card fade bs d-none" id="step-3" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">
                                    Configuración de Formato
                                </h4>
                                <small><i>Configura el formato de la página y las participaciones</i></small>

                                <br>

                                {{-- <div style="overflow: auto; height: 658px; width: 100%;"> --}}

                                <div class="format-box-btn" style="width: 250mm; height: 54px; margin: auto; padding-left: 20px;">

                                    <br>

                                    <div class="btn-group format-btn-group" style="width: 250mm; display: flex; justify-content: center; flex-wrap: wrap; gap: 1px;">
                                        <button title="Agregar texto" class="btn btn-sm btn-dark add-text" data-id="3" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-edit-line"></i></button>
                                        <button title="Agregar imagen" class="btn btn-sm btn-dark add-image" data-id="3" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-image-line"></i></button>
                                        {{-- <button class="btn btn-sm btn-dark add-qr" data-id="3" type="button">QR</button> --}}
                                        {{-- <label class="btn btn-sm btn-dark color" style="position: relative;" data-id="3" type="button">
                                            Fondo<input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                        </label> --}}
                                        <button title="Fondo del ticket" class="btn btn-sm btn-dark" id="open-bg-modal" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-palette-line"></i></button>
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

                                    <br>
                                    
                                    <div class="format-box" style="border:1px solid #c8c8c8; width: 200mm; height: 92mm; margin: auto; position: relative;">

                                        {{-- margen izquierdo --}}
                                        <div class="margen-izquierdo guide3" style="opacity: 1; z-index: 1; position: absolute; height: 100%; border-left: 1px solid purple; left: 2.5mm;"></div>
                                        {{-- margen arriba --}}
                                        <div class="margen-arriba guide3" style="opacity: 1; z-index: 1; position: absolute; width: 100%; border-top: 1px solid purple; top: 2.5mm;"></div>
                                        {{-- margen derecho --}}
                                        <div class="margen-derecho guide3" style="opacity: 1; z-index: 1; position: absolute; height: 100%; border-right: 1px solid purple; right: 2.5mm;"></div>
                                        {{-- margen abajo --}}
                                        <div class="margen-abajo guide3" style="opacity: 1; z-index: 1; position: absolute; width: 100%; border-bottom: 1px solid purple; bottom: 2.5mm;"></div>

                                        <div id="containment-wrapper3" style="width: 100%; height: calc(100% - 0mm); background-size: cover; background-position: center;"> 

                                            <div class="elements text ui-draggable" style="padding: 10px; width: 351px; height: 93px; resize: both; overflow: hidden; position: absolute; top: 59.8295px; left: 378.71px;">
                                                <span class="ui-draggable-handle"><h4><span style="color:hsl(0,0%,0%);" class="ui-draggable-handle"><u>&nbsp; Nombre: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></span></h4></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div><div class="elements context ui-draggable" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; inset: 294.67px 0px 20px 2.83209px; margin: auto; background-color: rgb(223, 223, 223);"><span style="padding: 20px; display: block;" class="ui-draggable-handle"></span></div><div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 49.7045px; left: 25.7074px; width: 90px; height: 36px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="{{url('logo.svg')}}" alt=""></span><button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button></div><div class="elements text ui-draggable" style="padding: 10px; width: 203px; height: 78px; resize: both; overflow: hidden; position: absolute; top: 29.4034px; left: 106.426px;">
                                                <span class="ui-draggable-handle"><h1><span style="font-size:38px;" class="ui-draggable-handle"><strong>PARTI</strong></span><span style="color:hsl(36,100%,48%);font-size:38px;" class="ui-draggable-handle"><strong>LOT</strong></span></h1></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 257px; height: 165px; resize: both; overflow: hidden; position: absolute; top: 107.724px; left: 24.7074px;">
                                                <span class="ui-draggable-handle"><h3><strong>Descargate la APP</strong><br><strong>PARTILOT</strong><br><strong>Y Comprueba&nbsp;</strong><br><strong>tu Participación</strong></h3></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div>
                                            
                                        </div>

                                    </div>
                                </div>

                                {{-- </div> --}}
                            </div>

                            <div class="form-card fade bs d-none" id="step-4" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">
                                    Configuración de Formato
                                </h4>
                                <small><i>Configura el formato de la página y las participaciones</i></small>

                                <br>

                                {{-- <div style="overflow: auto; height: 658px; width: 100%;"> --}}

                                <div class="format-box-btn" style="width: 250mm; height: 54px; margin: auto; padding-left: 20px;">

                                    <br>

                                    <div class="btn-group format-btn-group" style="width: 250mm; display: flex; justify-content: center; flex-wrap: wrap; gap: 1px;">
                                        <button title="Agregar texto" class="btn btn-sm btn-dark add-text" data-id="4" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-edit-line"></i></button>
                                        <button title="Agregar imagen" class="btn btn-sm btn-dark add-image" data-id="4" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-image-line"></i></button>
                                        {{-- <button class="btn btn-sm btn-dark add-qr" data-id="4" type="button">QR</button> --}}
                                        {{-- <label class="btn btn-sm btn-dark color" style="position: relative;" data-id="4" type="button">
                                            Fondo<input type="color" style="left: 0; opacity: 0; position: absolute; top: 0;">
                                        </label> --}}
                                        <button title="Fondo del ticket" class="btn btn-sm btn-dark" id="open-bg-modal" type="button" style="padding-left: 12px; padding-right: 12px;"><i class="ri-palette-line"></i></button>
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

                                    <br>
                                    
                                    <div class="format-box" style="border:1px solid #c8c8c8; width: 200mm; height: 92mm; margin: auto; position: relative;">

                                        {{-- margen izquierdo --}}
                                        <div class="margen-izquierdo guide4" style="opacity: 1; z-index: 1; position: absolute; height: 100%; border-left: 1px solid purple; left: 2.5mm;"></div>
                                        {{-- margen arriba --}}
                                        <div class="margen-arriba guide4" style="opacity: 1; z-index: 1; position: absolute; width: 100%; border-top: 1px solid purple; top: 2.5mm;"></div>
                                        {{-- margen derecho --}}
                                        <div class="margen-derecho guide4" style="opacity: 1; z-index: 1; position: absolute; height: 100%; border-right: 1px solid purple; right: 2.5mm;"></div>
                                        {{-- margen abajo --}}
                                        <div class="margen-abajo guide4" style="opacity: 1; z-index: 1; position: absolute; width: 100%; border-bottom: 1px solid purple; bottom: 2.5mm;"></div>
                                        {{-- caja matriz --}}
                                        {{-- <div class="caja-matriz-2 guide4" style="opacity: 1; z-index:1;position: absolute; width: 40mm; border-left: 1px solid purple; height: 100%; right: 2.5mm;"></div> --}}

                                        <div id="containment-wrapper4" style="width: 100%; height: calc(100% - 0mm); background-size: cover; background-position: center;"> 

                                            <div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 38.7969px; left: 44.8125px; width: 111px; height: 74px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="{{url('logo.svg')}}" alt=""></span><button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button></div><div class="elements text ui-draggable" style="padding: 10px; width: 380px; height: 140px; resize: both; overflow: hidden; position: absolute; top: 17.5938px; left: 173px;">
                                                <span class="ui-draggable-handle"><h3><strong>Descargate la APP</strong><br><strong>PARTILOT</strong><br><strong>Y Comprueba tu Participación</strong></h3></span>
                                                <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- </div> --}}
                            </div>

                            <div class="form-card fade bs d-none" id="step-5" style="min-height: 658px;">
                                <h4 class="mb-0 mt-1">
                                    Configurar salida
                                </h4>
                                <small><i>Configura el formato de salida de las participaciones</i></small>

                                <br>
                                <br>

                                <div>

                                    <h4 class="mb-0 mt-1">
                                        Formato de la página
                                    </h4>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mb-1">
                                                <div class="form-check form-switch mt-3">
                                                    <input style="float: left;" class="form-check-input bg-dark" type="checkbox" role="switch" id="guides" checked>
                                                    <label style="float: left; margin-left: 50px;" class="form-check-label" for="guides"><b>Dibujar las guías de corte</b></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="row mb-3">
                                                        
                                                <label class="col-form-label label-control col-6 text-start">
                                                    Color de las guías
                                                </label>

                                                <div class="col-sm-2">
                                                    <input class="form-control" type="color" id="guide_color" style="border-radius: 30px">
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="row mb-3">
                                                        
                                                <label class="col-form-label label-control col-6 text-start">
                                                    Grosor de las guías (mm):
                                                </label>

                                                <div class="col-sm-2">
                                                    <input class="form-control" type="number" id="guide_weight" value="0.3" step="0.1" placeholder="0.00" style="border-radius: 30px">
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="mb-0 mt-1">
                                        Participaciones por talonario
                                    </h4>
                                    <small><i>Elige la cantidad de participaciones por talonario</i></small>

                                    <div class="row mb-3">
                                                        
                                        <label class="col-form-label label-control col-3 text-start">
                                            Cantidad de participaciónes:
                                        </label>

                                        <div class="col-sm-1">
                                            <input class="form-control" type="number" value="50" id="participation_number" style="border-radius: 30px">
                                        </div>

                                    </div>

                                    <br>

                                    <h4 class="mb-0 mt-1">
                                        Participaciones a generar
                                    </h4>

                                    <div class="form-group mb-3">
                                        <div class="form-check form-switch mt-3">
                                            <input style="float: left;" class="form-check-input bg-dark" type="radio" name="generate" value="1" role="switch" id="generate1" checked>
                                            <label style="float: left; margin-left: 50px;" class="form-check-label" for="generate1"><b>Generar todas las participaciones (600)</b></label>
                                        </div>

                                        <div class="form-check form-switch mt-3">
                                            <input style="float: left;" class="form-check-input bg-dark" type="radio" name="generate" value="2" role="switch" id="generate">
                                            <label style="float: left; margin-left: 50px;" class="form-check-label" for="generate"><b>Seperar las participaciones en múltiples documentos</b></label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                                        
                                        <label class="col-form-label label-control col-3 text-start">
                                            Generar de la participación:
                                        </label>

                                        <div class="col-sm-1">
                                            <input class="form-control" type="number" value="1" id="participation_from" style="border-radius: 30px">
                                        </div>

                                        <label class="col-form-label label-control col-3 text-start">
                                            Hasta la participación:
                                        </label>
                                        <div class="col-sm-1">
                                            <input class="form-control" type="number" value="600" id="participation_to" style="border-radius: 30px">
                                        </div>

                                        <label class="col-form-label label-control col-4 text-start">
                                            (ambas incluidas)
                                        </label>

                                    </div>

                                    <br>

                                    <h4 class="mb-0 mt-1">
                                        Número de documentos
                                    </h4>

                                    <div class="form-group mb-3">
                                        <div class="form-check form-switch mt-3">
                                            <input style="float: left;" class="form-check-input bg-dark" type="radio" name="documents" value="1" role="switch" id="documents1" checked>
                                            <label style="float: left; margin-left: 50px;" class="form-check-label" for="documents1"><b>Generar un único documento</b></label>
                                        </div>

                                        <div class="form-check form-switch mt-3">
                                            <input style="float: left;" class="form-check-input bg-dark" type="radio" name="documents" value="2" role="switch" id="documents">
                                            <label style="float: left; margin-left: 50px;" class="form-check-label" for="documents"><b>Seperar las participaciones en múltiples documentos</b></label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                                        
                                        <label class="col-form-label label-control col-3 text-start">
                                            Número de páginas por documento:
                                        </label>

                                        <div class="col-sm-1">
                                            <input class="form-control" type="number" value="150" id="participation_page" style="border-radius: 30px">
                                        </div>
                                        <label class="col-form-label label-control col-8 text-start">
                                            (6 participaciones por página, 1 documento)
                                        </label>

                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-6 text-start">
                                    <a href="javascript:;" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2 prev-step">
                                        <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                                </div>
                                <div class="col-6 text-end">
                                    <button id="step" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2 next-step">Siguiente
                                        <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></button>

                                    <button id="save-step" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2 d-none">Guardar
                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                                </div>

                            </div>
                        </div>

                    </div>

                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

<div class="modal fade" id="ckeditor-modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Texto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <div class="editor-container__editor"><div id="editor" style="height: 200px;"></div></div>
        {{-- <div class="editor-container editor-container_document-editor" id="editor-container">
            <div class="editor-container__editor-wrapper">
            </div>
        </div> --}}

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
{{-- // ... existing code ...
// === BOTÓN PARA ABRIR EL MODAL DE FONDO ===
// Puedes ponerlo junto al botón de color de fondo actual: --}}

@endsection

@section('scripts')

<script>
// ... existing code ...
// === SCRIPTS PARA EL MODAL DE FONDO ===
$(document).ready(function() {
  // Botón para abrir el modal
  $(document).on('click', '#open-bg-modal', function() {
    // Cargar valores actuales
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

  // Previsualizar color
  $('#background-color').on('input', function() {
    $('#bg-preview').css('background-color', $(this).val());
  });

  // Previsualizar imagen
  $('#background-image').on('change', function(e) {
    if(this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        $('#bg-preview').css('background-image', 'url('+ev.target.result+')');
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  // Quitar imagen de fondo
  $('#remove-bg-image').on('click', function() {
    $('#bg-preview').css('background-image', 'none');
    $('#background-image').val('');
    localStorage.removeItem('bgimg-step'+step);
  });

  // Aplicar fondo
  $('#apply-bg').on('click', function() {
    const color = $('#background-color').val();
    let img = '';
    if($('#background-image')[0].files && $('#background-image')[0].files[0]) {
      // Subir imagen al servidor
      const file = $('#background-image')[0].files[0];
      const formData = new FormData();
      formData.append('image', file);
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
});
// ... existing code ...
</script>

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
        console.log(w,h);
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
  var selectedElement = null;

  $('.prev-step').click(function (e) {
      e.preventDefault();

      if (step == 1) {
        window.open('{{url('design/add/select')}}','_self');
      }else{
        step -=1;
        
        // Limpiar historial al cambiar de paso
        historyStates = [];
        currentHistoryIndex = -1;
        updateUndoRedoButtons();
        
        // Limpiar observers anteriores
        if (resizeObserver) {
          resizeObserver.disconnect();
          resizeObserver = null;
        }
        if (containerObserver) {
          containerObserver.disconnect();
          containerObserver = null;
        }

        $('.form-card[id*="step-"]').addClass('d-none').removeClass('show');
        $('.form-card[id="step-'+step+'"]').removeClass('d-none fade').addClass('show');

        if (localStorage.getItem('step'+step)) {
            $('#containment-wrapper'+step).html(localStorage.getItem('step'+step));
        }

        setupDraggable();
        setupResizeObserver();    

        $('.elements.text .edit-btn').click(editelements);
        $('.elements.context').dblclick(deleteElements);
        $('.elements.images .edit-btn').click(changeImage);
        {{-- $('.elements.qr').dblclick(setQRtext); --}}
        
        // Guardar estado inicial del paso
        setTimeout(() => {
          saveHistoryState();
          // Actualizar estado de botones undo/redo
          updateUndoRedoButtons();
        }, 100);

        if ($('#containment-wrapper'+step).length) {
            if(localStorage.getItem('bg-step'+step)){
                $('#containment-wrapper'+step).css('background-color', localStorage.getItem('bg-step'+step));
                $('#containment-wrapper'+step).css('background-image', 'url('+localStorage.getItem('bgimg-step'+step)+')');
            }else{
                $('#containment-wrapper'+step).css('background-color', '#dfdfdf');
            }
        }

        $('#step').removeClass('d-none');
        $('#save-step').addClass('d-none');

        $('.form-wizard-element').removeClass('active');
        $('#bc-step-'+step).addClass('active');

        configMargins();
        addEventsElement();
        setupDraggable();
        setupResizeObserver();

      }
  });

  $('.next-step').click(function (e) {
      e.preventDefault();

      if (step == 5) {
        e.preventDefault();
          const data = collectDesignData();
          console.log(data);
          fetch('{{url('/api/design/save-format')}}', {
            method: 'POST',
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
              window.open('{{url('design?table=1')}}','_self');
              // Puedes redirigir o mostrar un mensaje aquí
            } else {
              alert('Error al guardar el diseño.');
            }
          })
          .catch(() => alert('Error al guardar el diseño.'));

      }else{
          step +=1;
          
          // Limpiar historial al cambiar de paso
          historyStates = [];
          currentHistoryIndex = -1;
          updateUndoRedoButtons();
          
          // Limpiar observers anteriores
          if (resizeObserver) {
            resizeObserver.disconnect();
            resizeObserver = null;
          }
          if (containerObserver) {
            containerObserver.disconnect();
            containerObserver = null;
          }

          $('.form-card[id*="step-"]').addClass('d-none').removeClass('show');
          $('.form-card[id="step-'+step+'"]').removeClass('d-none fade').addClass('show');

          if (localStorage.getItem('step'+step)) {
            $('#containment-wrapper'+step).html(localStorage.getItem('step'+step));
            // Agregar botón de editar a elementos de texto existentes
            $('.elements.text').each(function() {
              if ($(this).find('.edit-btn').length === 0) {
                $(this).prepend('<button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>');
              }
            });
            // Agregar botón de cambiar imagen a elementos de imagen existentes
            $('.elements.images').each(function() {
              if ($(this).find('.edit-btn').length === 0) {
                $(this).append('<button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button>');
              }
            });
          }

          setupDraggable();
        setupResizeObserver();
          $('.elements.text .edit-btn').click(editelements);
          $('.elements.context').dblclick(deleteElements);
          $('.elements.images .edit-btn').click(changeImage);
          {{-- $('.elements.qr').dblclick(setQRtext); --}}
          
          // Guardar estado inicial del paso
          setTimeout(() => {
            saveHistoryState();
            updateUndoRedoButtons(); // Actualizar estado de botones
          }, 100); // Pequeño delay para asegurar que todo esté cargado

          if ($('#containment-wrapper'+step).length) {
              if(localStorage.getItem('bg-step'+step)){
                  $('#containment-wrapper'+step).css('background-color', localStorage.getItem('bg-step'+step));
                  $('#containment-wrapper'+step).css('background-image', 'url('+localStorage.getItem('bgimg-step'+step)+')');
              }else{
                  $('#containment-wrapper'+step).css('background-color', '#dfdfdf');
              }

              if(localStorage.getItem('guide-step'+step)){
                  $('.guide'+step).css('border-color', localStorage.getItem('guide-step'+step));
              }else{
                  $('.guide'+step).css('border-color', 'purple');
              }
          }

          $('.form-wizard-element').removeClass('active');
          $('#bc-step-'+step).addClass('active');

          configMargins();
          addEventsElement();

          $('.up-layer').unbind('click');
          $('.up-layer').click(function(e) {
            e.preventDefault();
            if (selectedElement) {
              let zindex = selectedElement.css('z-index') || 0;
              zindex = parseInt(zindex) + 1;
              selectedElement.css('z-index', zindex);
            }
          });
          $('.down-layer').unbind('click');
          $('.down-layer').click(function(e) {
            e.preventDefault();
            if (selectedElement) {
              let zindex = selectedElement.css('z-index') || 0;
              zindex = parseInt(zindex) - 1;
              selectedElement.css('z-index', zindex);
            }
          });
          $('.delete-element-btn').unbind('click');
          $('.delete-element-btn').click(function(e) {
            e.preventDefault();
            if (selectedElement) {
              selectedElement.remove();
              selectedElement = null;
              $('.up-layer, .down-layer, .delete-element-btn, .text-style-btn').prop('disabled', true);
              $('#save-step').removeClass('d-none');
              $('#step').addClass('d-none');
              
              saveHistoryState(); // Guardar estado después de eliminar
              updateUndoRedoButtons(); // Actualizar estado de botones
            }
          });
          $('.undo-btn').click(function(e) {
            e.preventDefault();
            undo();
          });
          
          $('.redo-btn').click(function(e) {
            e.preventDefault();
            redo();
          });

          // Deseleccionar al hacer clic fuera
          $('body').unbind('click.deselect');
          $('body').bind('click.deselect', function(e) {
            if (!$(e.target).closest('.elements').length && !$(e.target).closest('.up-layer, .down-layer, .text-style-btn, .delete-element-btn, .undo-btn').length) {
              $('.elements').removeClass('selected');
              selectedElement = null;
              $('.up-layer, .down-layer, .text-style-btn, .delete-element-btn').prop('disabled', true);
            }
          });
      }

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
            } else{
                // Personalizado
                {{-- const dims = getCustomDimensions();
                w = dims.w;
                h = dims.h; --}}
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

  });

  var editor;
  var actualElement;
  
  // Sistema de Undo/Redo limitado
  var historyStates = [];
  var currentHistoryIndex = -1;
  var maxHistoryStates = 10;
  var isRestoringState = false; // Flag para evitar guardar durante restauración
  var resizeTimeout; // Para debounce del ResizeObserver


  // Funciones del sistema de Undo/Redo
  function saveHistoryState() {
    if (isRestoringState) return; // Evitar guardar durante restauración
    
    console.log('saveHistoryState called, step:', step);
    
    const canvasHtml = $('#containment-wrapper' + step).html();
    const canvasState = {
      html: canvasHtml,
      step: step,
      timestamp: Date.now()
    };
    
    // Remover estados futuros si estamos en medio del historial
    if (currentHistoryIndex < historyStates.length - 1) {
      historyStates = historyStates.slice(0, currentHistoryIndex + 1);
    }
    
    // Agregar nuevo estado
    historyStates.push(canvasState);
    currentHistoryIndex++;
    
    // Mantener máximo de estados
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
    
    // Solo restaurar si es el mismo step
    if (targetState.step === step) {
      $('#containment-wrapper' + step).html(targetState.html);
      
      // Re-vincular eventos después de restaurar
      rebindEventsAfterRestore();
      
      currentHistoryIndex = targetIndex;
      updateUndoRedoButtons();
    }
    
    isRestoringState = false;
  }
  
  function undo() {
    console.log('Undo called, canUndo:', canUndo());
    if (canUndo()) {
      console.log('Restoring to index:', currentHistoryIndex - 1);
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
  
  // Función auxiliar para configurar draggable con guardado de estado
  function setupDraggable() {
    $( ".elements" ).draggable({ 
      handle: 'span', 
      containment: "#containment-wrapper"+step, 
      scroll: false, 
      start: function(){
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
        updateUndoRedoButtons(); // Actualizar estado de botones
      },
      stop: function() {
        console.log('Draggable stop - saving state');
        saveHistoryState(); // Guardar estado después de mover
      }
    });
  }

  // Variable para almacenar el observer
  var resizeObserver = null;
  var containerObserver = null;

  // Función para detectar redimensionamiento de elementos
  function setupResizeObserver() {
    // Limpiar observers anteriores si existen
    if (resizeObserver) {
      resizeObserver.disconnect();
    }
    if (containerObserver) {
      containerObserver.disconnect();
    }

    const container = document.getElementById('containment-wrapper' + step);
    if (!container) return;

    // Observer para detectar cambios en el atributo style (redimensionamiento)
    resizeObserver = new MutationObserver(function(mutations) {
      let shouldSave = false;
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
          // Solo guardar si el cambio es en width o height (redimensionamiento)
          const oldValue = mutation.oldValue || '';
          const newValue = mutation.target.getAttribute('style') || '';
          // Verificar si cambió width o height
          const widthChanged = (oldValue.match(/width:\s*[^;]+/) || [''])[0] !== (newValue.match(/width:\s*[^;]+/) || [''])[0];
          const heightChanged = (oldValue.match(/height:\s*[^;]+/) || [''])[0] !== (newValue.match(/height:\s*[^;]+/) || [''])[0];
          if (widthChanged || heightChanged) {
            shouldSave = true;
          }
        }
      });

      if (shouldSave) {
        // Debounce para evitar guardar demasiadas veces
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
          console.log('Element resized - saving state');
          saveHistoryState();
        }, 300); // Esperar 300ms después del último cambio
      }
    });

    // Observer para detectar cuando se agregan nuevos elementos
    containerObserver = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        mutation.addedNodes.forEach(function(node) {
          if (node.nodeType === 1 && node.classList && node.classList.contains('elements')) {
            // Observar el nuevo elemento
            resizeObserver.observe(node, {
              attributes: true,
              attributeFilter: ['style'],
              attributeOldValue: true
            });
          }
        });
      });
    });

    // Observar el contenedor para detectar nuevos elementos
    containerObserver.observe(container, {
      childList: true,
      subtree: true
    });

    // Observar todos los elementos existentes con clase .elements
    $(container).find('.elements').each(function() {
      resizeObserver.observe(this, {
        attributes: true,
        attributeFilter: ['style'],
        attributeOldValue: true // Necesario para comparar valores antiguos
      });
    });
  }

  function rebindEventsAfterRestore() {
    // Re-vincular todos los eventos después de restaurar el HTML
    setupDraggable();
    setupResizeObserver();
    
  // Hacer todos los elementos redimensionables
  // $('.elements').resizable({
  //   containment: "#containment-wrapper"+step,
  //   minWidth: 50,
  //   minHeight: 30,
  //   stop: function() {
  //     console.log('Resizable stop - saving state');
  //     saveHistoryState();
  //     $('.undo-btn').show();
  //   }
  // });
  
  $('.elements.text .edit-btn').unbind('click', editelements);
  $('.elements.text .edit-btn').click(editelements);
  
  $('.elements.context').unbind('dblclick', deleteElements);
  $('.elements.context').dblclick(deleteElements);
  
  $('.elements.images .edit-btn').unbind('click', changeImage);
  $('.elements.images .edit-btn').click(changeImage);
  
  addEventsElement();
}

  function editelements(event) {
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
  }

  function deleteElements(event) {

    let element = $(this);

    if (confirm('¿Desea eliminar el elemento seleccionado?')) {
        element.remove();
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
        saveHistoryState(); // Guardar estado después de eliminar
        updateUndoRedoButtons(); // Actualizar estado de botones
    }

  }

  function changeImage(event) {

    actualElement = $(this).closest('.elements.images');

    $('#imagen-modal').modal('show');

  }

  function setQRtext(event) {

    actualElement = $(this);

    $('#qr-modal').modal('show');

  }

  $('.deleteElements').click(function (e) {
      e.preventDefault();

      if (confirm('¿Desea eliminar el elemento seleccionado?')) {
        actualElement.remove();
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
        saveHistoryState(); // Guardar estado después de eliminar
        updateUndoRedoButtons(); // Actualizar estado de botones
    }
  });

  $('.accept-text').click(function(event) {
    /* Act on the event */
    if (editor && CKEDITOR.instances['editor']) {
        var data = CKEDITOR.instances['editor'].getData();
        // Limpiar párrafos vacíos
        data = data.replace(/<p>&nbsp;<\/p>/gi, '').replace(/<p><\/p>/gi, '');
        $(actualElement).find('span').html(data);
        CKEDITOR.instances['editor'].destroy(true);
    }
    $('#ckeditor-modal').modal('hide');
    $('#step').addClass('d-none');
    $('#save-step').removeClass('d-none');
    
    saveHistoryState(); // Guardar estado después de editar texto
    updateUndoRedoButtons(); // Actualizar estado de botones
  });

  const input = document.getElementById('imageInput');
  $('.accept-image').click(function (e) {
      e.preventDefault();

      if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.type.startsWith('image/')) {
                // Imagen válida
                uploadImage(file);
            } else {
                console.log("El archivo seleccionado no es una imagen.");
            }
        }
  });

  $('.accept-qr').click(function (e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('text', $('#qr-text').val());

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
    .catch(error => console.error('Error al subir la imagen:', error));
  });

  function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);

    fetch('{{url('api/upload-image')}}', {
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

  $('.add-text').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements text" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 0">
            <button class="edit-btn" title="Editar texto"><i class="ri-edit-line"></i></button>
            <span>Escribe aquí...</span>
        </div>`);

      // Hacer el elemento redimensionable con jQuery UI
      // const newElement = $('#containment-wrapper'+step + ' .elements').last();
      // newElement.resizable({
      //   containment: "#containment-wrapper"+step,
      //   minWidth: 50,
      //   minHeight: 30,
      //   stop: function() {
      //     saveHistoryState();
      //     $('.undo-btn').show();
      //   }
      // });

      $('.elements.text .edit-btn').unbind('click', editelements);
      $('.elements.text .edit-btn').click(editelements);
      addEventsElement();

      setupDraggable();
      setupResizeObserver();
      
      saveHistoryState(); // Guardar estado después de agregar
      updateUndoRedoButtons(); // Actualizar estado de botones
  });
  $('.add-image').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements images" style="resize: both; overflow: hidden; position: absolute; top: 0"><span><img style="width: 100%; height: 100%" src="{{url('default.jpg')}}" alt=""></span><button class="edit-btn" title="Cambiar imagen"><i class="ri-image-line"></i></button></div>`);

      setupDraggable();
      setupResizeObserver();

      $('.elements.images').unbind('dblclick',changeImage);
      $('.elements.images .edit-btn').click(changeImage);
      addEventsElement();
      
      setupDraggable();
      setupResizeObserver();
      
      saveHistoryState(); // Guardar estado después de agregar
      updateUndoRedoButtons(); // Actualizar estado de botones
  });

  $('.add-qr').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements qr" style="resize: both; overflow: hidden; position: absolute; top: 0"><span><img style="width: 100%; height: 100%" src="{{url('basicqr.jpg')}}" alt=""></span></div>`);

      setupDraggable();
      setupResizeObserver();

      $('.elements.qr').unbind('dblclick',setQRtext);
      {{-- $('.elements.qr').dblclick(setQRtext); --}}
      addEventsElement();
  });

  $('.add-top').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; top: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);

      $('.elements.context').unbind('dblclick',deleteElements);
      $('.elements.context').dblclick(deleteElements);
      addEventsElement();

      setupDraggable();
      setupResizeObserver();
  });

  $('.add-bottom').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; bottom: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);

      $('.elements.context').unbind('dblclick',deleteElements);
      $('.elements.context').dblclick(deleteElements);
      addEventsElement();

      setupDraggable();
      setupResizeObserver();
  });

  $('.color input').change(function (e) {
      e.preventDefault();

      localStorage.setItem('bg-step'+step,$(this).val());

      $('#containment-wrapper'+step).css('background-color', $(this).val());
  });

  function addEventsElement()
  {
    $('.elements').unbind('contextmenu',changePositionElement);
    $('.elements').contextmenu(changePositionElement);
    $('.elements').unbind('click.select');
    $('.elements').bind('click.select', function(e) {
      e.stopPropagation();
      $('.elements').removeClass('selected');
      $(this).addClass('selected');
      selectedElement = $(this);
      $('.up-layer, .down-layer, .delete-element-btn').prop('disabled', false);
      if ($(this).hasClass('text')) {
        $('.text-style-btn').prop('disabled', false);
      } else {
        $('.text-style-btn').prop('disabled', true);
      }
      // Habilitar undo si hay elementos modificables
      updateUndoRedoButtons();
    });
  }

  // Event listeners for text style buttons
  $('.bold-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.find('span').toggleClass('text-bold');
    }
  });
  $('.italic-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.find('span').toggleClass('text-italic');
    }
  });
  $('.underline-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.find('span').toggleClass('text-underline');
    }
  });
  $('.strike-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.find('span').toggleClass('text-strike');
    }
  });
  $('.align-left-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.removeClass('text-center text-right').addClass('text-left');
    }
  });
  $('.align-center-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.removeClass('text-left text-right').addClass('text-center');
    }
  });
  $('.align-right-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      selectedElement.removeClass('text-left text-center').addClass('text-right');
    }
  });
  $('.font-size-up-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      let span = selectedElement.find('span');
      let currentSize = parseInt(span.css('font-size'));
      span.css('font-size', (currentSize + 2) + 'px');
    }
  });
  $('.font-size-down-btn').click(function(e) {
    e.preventDefault();
    if (selectedElement && selectedElement.hasClass('text')) {
      let span = selectedElement.find('span');
      let currentSize = parseInt(span.css('font-size'));
      span.css('font-size', Math.max(8, currentSize - 2) + 'px');
    }
  });

  function changePositionElement(event)
  {
    event.preventDefault();

    actualElement = $(this);

    $('#position-modal').modal('show');
  }

  var snapshot_path = null;
  $('#save-step').click(function(event) {

    // Deseleccionar cualquier elemento seleccionado
    $('.elements').removeClass('selected');
    selectedElement = null;
    $('.up-layer, .down-layer, .text-style-btn').prop('disabled', true);

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
          $('.format-box-btn').css({width: ticketW+'mm'});
      }
  }

  // Llamar al cargar y al cambiar cualquier campo relevante
  $(document).ready(function() {
      updateTicketInfo();
      $('#format,#page,#rows,#cols,#orientation').on('change keyup', updateTicketInfo);
  });
  // === FIN BLOQUE NUEVO ===

  // ... existing code ...
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

    const design_lottery_id = '{{ session('design_lottery_id') }}';
    const design_entity_id = '{{ session('design_entity_id') }}';

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
    const generate_mode = $('input[name="generate"]:checked').val();
    const participation_from = parseInt($('#participation_from').val());
    const participation_to = parseInt($('#participation_to').val());
    const documents_mode = $('input[name="documents"]:checked').val();
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
      snapshot_path,
      design_lottery_id,
      design_entity_id,
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

  // Asegura que todos los .elements sean redimensionables al cargar la vista
</script>

{{-- {{url('design/add/select')}} --}}

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
</script>

@endsection