@extends('layouts.layout')

@section('title','Diseño e Impresión')

@section('content')

<style>
    input[disabled],select[disabled] {
        background-color: #cfcfcf !important;
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
                                                            <input class="form-control" type="number" value="12.50" step="0.1" placeholder="0.00" style="border-radius: 30px">
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

                                <div class="format-box-btn" style="width: 200mm; height: 54px; margin: auto;">

                                    <br>

                                    <div>
                                        <button class="btn btn-sm btn-info add-text" data-id="2">Texto</button>
                                        <button class="btn btn-sm btn-info add-image" data-id="2">Imagen</button>
                                        <button class="btn btn-sm btn-info add-qr" data-id="2">QR</button>
                                        <label class="btn btn-sm btn-info color" style="position: relative;" data-id="2">
                                            Fondo<input type="color" style="opacity: 0; position: absolute; top: 0;">
                                        </label>
                                        <button class="btn btn-sm btn-info toggle-guide" data-id="2">Guias</button>
                                        <label class="btn btn-sm btn-info color-guide" style="position: relative;" data-id="2">
                                            Color Guias<input type="color" style="opacity: 0; position: absolute; top: 0;">
                                        </label>
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

                                        <div id="containment-wrapper2" style="width: 100%; height: calc(100% - 0mm);"> 

                                             <div class="elements text ui-draggable" style="padding: 10px; width: 340px; height: 120px; resize: both; overflow: hidden; position: relative; left: 414.009px; top: 7.90909px;">
                                                <span class="ui-draggable-handle"><h1><span style="font-size:42px;" class="ui-draggable-handle"><strong>05.716-52.468</strong></span><br><span style="font-size:42px;" class="ui-draggable-handle"><strong>51.235-69.584</strong></span></h1></span>
                                            </div>

                                            <div class="elements text ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 420px; top: -2px; width: 316px; height: 85px;">
                                                <span class="ui-draggable-handle"><h5 style="text-align:center;"><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">El portador de la presente participación juega DOS EUROS&nbsp;</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">en cada número arriba indicado para el sorteo de Loteria Nacional&nbsp;</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">que se celebrará el 22 de Diciembre de 2025&nbsp;</span><br><span style="font-size:10px;" class="ui-draggable-handle">&nbsp;</span></h5></span>
                                                
                                            </div>
                                            
                                            <div class="elements text ui-draggable" style="padding: 10px; width: 144px; height: 94px; resize: both; overflow: hidden; position: absolute; top: 0px; left: 12px;">
                                                <span class="ui-draggable-handle"><h6 style="text-align:center;"><span style="font-size:20px;" class="ui-draggable-handle"><strong>LOTERÍA</strong></span><br><span style="font-size:20px;" class="ui-draggable-handle"><strong>NACIONAL</strong></span></h6></span>
                                            </div>
                                                <div class="elements text ui-draggable" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 144px; left: 158px;">
                                                <span class="ui-draggable-handle"><h5 style="text-align:center;"><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">DATOS DE LA EMPRESA</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">NOMBRE</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">C/NOMBRE DE LA VIA</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">TELEFONO</span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle">DATOS</span></h5></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 82px; height: 44px; resize: both; overflow: hidden; position: absolute; top: 134px; left: 42px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0, 0%, 0%);" class="ui-draggable-handle"><strong>22/07/25</strong></span></p></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 120px; height: 61px; resize: both; overflow: hidden; position: absolute; top: 170.798px; left: 27.8096px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-family:Arial, Helvetica, sans-serif;font-size:14px;" class="ui-draggable-handle"><strong>05.716-52.468</strong></span><br><span style="color:hsl(0,0%,0%);font-family:Arial, Helvetica, sans-serif;font-size:14px;" class="ui-draggable-handle"><strong>51.235-69.584</strong></span></p></span>
                                            </div>
                                                <div class="elements text ui-draggable" style="padding: 10px; width: 120px; height: 90px; resize: both; overflow: hidden; position: absolute; top: 214px; left: 26px;">
                                                <span class="ui-draggable-handle"><h4 style="text-align:center;"><span style="font-size:26px;" class="ui-draggable-handle"><strong>5,00€</strong></span><br><span style="font-size:14px;" class="ui-draggable-handle"><strong>Donativo:</strong></span><br><span style="font-size:18px;" class="ui-draggable-handle"><strong>1,00€</strong></span></h4></span>
                                            </div>
                                                <div class="elements participation text ui-draggable" style="padding: 10px; width: 90px; height: 40px; resize: both; overflow: hidden; position: absolute; top: 300px; left: 94px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Nº 1/0001</strong></span></p></span>
                                            </div>
                                            <div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 42px; top: -140px; height: 78px; width: 84px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/default.jpg" alt=""></span></div><div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 170.909px; top: -263.091px; height: 144px; width: 170px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/uploads/1750719384_173289460408li94ujyym5uhx0jbpu.png" alt=""></span></div><div class="elements text ui-draggable" style="padding: 10px; width: 298px; height: 78px; resize: both; overflow: hidden; position: absolute; top: 258.815px; left: 162.81px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Caduca a los 3 meses, Premios sujetos a la ley.</strong></span><br><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Nota: Todo talón roto o enmendado será nulo</strong></span></p></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 602px; height: 46px; resize: both; overflow: hidden; position: absolute; top: 300.909px; left: 152.92px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:6px;" class="ui-draggable-handle"><strong>Los premios superiores a 2500€ por décimo, tendrán una retención del 20% por encima del importe anterior, que será prorrateada en estas participaciones en la proporción correspondiente a su valor nominal.</strong></span></p></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 200px; height: 120px; resize: both; overflow: hidden; position: absolute; top: 220px; left: 332px;">
                                            <span class="ui-draggable-handle"><p style="text-align:center;"><span style="color:hsl(0, 0%, 0%);font-size:26px;" class="ui-draggable-handle"><strong>5,00€</strong></span><br><span style="color:hsl(0, 0%, 0%);font-size:14px;" class="ui-draggable-handle"><strong>Donativo:</strong></span><br><span style="color:hsl(0, 0%, 0%);font-size:18px;" class="ui-draggable-handle"><strong>1,00€</strong></span></p></span>
                                            </div><div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: relative; left: 600px; top: -187px; width: 56px; height: 44px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/uploads/1750725951_156098571_1876771692487612_4648103175506295823_n.jpg" alt=""></span></div><div class="elements participation text ui-draggable" style="padding: 10px; width: 80px; height: 42px; resize: both; overflow: hidden; position: absolute; top: 218px; left: 662px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:10px;" class="ui-draggable-handle"><strong>Nº 1/0001</strong></span></p></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 92px; height: 36px; resize: both; overflow: hidden; position: absolute; top: 235.81px; left: 504.795px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:12px;" class="ui-draggable-handle"><strong>DEPOSITARIO</strong></span></p></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 227px; height: 40px; resize: both; overflow: hidden; position: absolute; top: 280.699px; left: 461.707px;">
                                                <span class="ui-draggable-handle"><p><span style="color:hsl(0,0%,0%);font-size:12px;" class="ui-draggable-handle"><strong>Nº Ref: 88812543625849265321</strong></span></p></span>
                                            </div>
                                        <div class="elements qr ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 249.616px; left: 667.622px; width: 59px; height: 60px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/qrcodes/6859f429e67ba.png" alt=""></span></div>
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

                                <div class="format-box-btn" style="width: 200mm; height: 54px; margin: auto;">

                                    <br>

                                    <div>
                                        <button class="btn btn-sm btn-info add-text" data-id="3">Texto</button>
                                        <button class="btn btn-sm btn-info add-image" data-id="3">Imagen</button>
                                        <button class="btn btn-sm btn-info add-qr" data-id="3">QR</button>
                                        <label class="btn btn-sm btn-info color" style="position: relative;" data-id="3">
                                            Fondo<input type="color" style="opacity: 0; position: absolute; top: 0;">
                                        </label>
                                        <button class="btn btn-sm btn-info add-top" data-id="3">Arriba</button>
                                        <button class="btn btn-sm btn-info add-bottom" data-id="3">Abajo</button>
                                        <button class="btn btn-sm btn-info toggle-guide" data-id="2">Guias</button>
                                        <label class="btn btn-sm btn-info color-guide" style="position: relative;" data-id="2">
                                            Color Guias<input type="color" style="opacity: 0; position: absolute; top: 0;">
                                        </label>
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

                                        <div id="containment-wrapper3" style="width: 100%; height: calc(100% - 0mm);"> 

                                            <div class="elements text ui-draggable" style="padding: 10px; width: 351px; height: 93px; resize: both; overflow: hidden; position: absolute; top: 59.8295px; left: 378.71px;">
                                                <span class="ui-draggable-handle"><h4><span style="color:hsl(0,0%,0%);" class="ui-draggable-handle"><u>&nbsp; Nombre: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></span></h4></span>
                                            </div><div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 122.406px; left: 385.42px; width: 337px; height: 155px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/uploads/1750688007_loteria-blog1.jpg" alt=""></span></div><div class="elements context ui-draggable" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; inset: 294.67px 0px 20px 2.83209px; margin: auto; background-color: rgb(223, 223, 223);"><span style="padding: 20px; display: block;" class="ui-draggable-handle"></span></div><div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 49.7045px; left: 25.7074px; width: 90px; height: 36px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/uploads/1750687788_logo.svg" alt=""></span></div><div class="elements text ui-draggable" style="padding: 10px; width: 203px; height: 78px; resize: both; overflow: hidden; position: absolute; top: 29.4034px; left: 106.426px;">
                                                <span class="ui-draggable-handle"><h1><span style="font-size:38px;" class="ui-draggable-handle"><strong>PARTI</strong></span><span style="color:hsl(36,100%,48%);font-size:38px;" class="ui-draggable-handle"><strong>LOT</strong></span></h1></span>
                                            </div><div class="elements text ui-draggable" style="padding: 10px; width: 257px; height: 165px; resize: both; overflow: hidden; position: absolute; top: 107.724px; left: 24.7074px;">
                                                <span class="ui-draggable-handle"><h3><strong>Descargate la APP</strong><br><strong>PARTILOT</strong><br><strong>Y Comprueba&nbsp;</strong><br><strong>tu Participación</strong></h3></span>
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

                                <div class="format-box-btn" style="width: 200mm; height: 54px; margin: auto;">

                                    <br>

                                    <div>
                                        <button class="btn btn-sm btn-info add-text" data-id="4">Texto</button>
                                        <button class="btn btn-sm btn-info add-image" data-id="4">Imagen</button>
                                        <button class="btn btn-sm btn-info add-qr" data-id="4">QR</button>
                                        <label class="btn btn-sm btn-info color" style="position: relative;" data-id="4">
                                            Fondo<input type="color" style="opacity: 0; position: absolute; top: 0;">
                                        </label>
                                        <button class="btn btn-sm btn-info add-top" data-id="4">Arriba</button>
                                        <button class="btn btn-sm btn-info add-bottom" data-id="4">Abajo</button>
                                        <button class="btn btn-sm btn-info toggle-guide" data-id="2">Guias</button>
                                        <label class="btn btn-sm btn-info color-guide" style="position: relative;" data-id="2">
                                            Color Guias<input type="color" style="opacity: 0; position: absolute; top: 0;">
                                        </label>
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

                                        <div id="containment-wrapper4" style="width: 100%; height: calc(100% - 0mm);"> 

                                            <div class="elements images ui-draggable" style="resize: both; overflow: hidden; position: absolute; top: 28.8096px; left: 56.8154px; width: 111px; height: 74px;"><span class="ui-draggable-handle"><img style="width: 100%; height: 100%" src="http://127.0.0.1:8000/uploads/1750688309_logo.svg" alt=""></span></div><div class="elements text ui-draggable" style="padding: 10px; width: 544px; height: 172px; resize: both; overflow: hidden; position: absolute; top: 13.608px; left: 171.628px;">
                                                <span class="ui-draggable-handle"><h3><strong>Descargate la APP</strong><br><strong>PARTILOT</strong><br><strong>Y Comprueba tu Participación</strong></h3></span>
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
                                            Generar de la participación:
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
        
        <div class="editor-container__toolbar" id="editor-toolbar"></div>
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


  $('#format').change(function (e) {
  	e.preventDefault();

  	let html = "";

    restoreValues()

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
  	}

  	$('.preview-design').html(html);
  });

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

  function recalculateDesign()
  {
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
  }

  function restoreValues()
  {
    $('#page').prop('selectedIndex',0);
    $('#rows').val(3);
    $('#cols').val(2);
    $('#orientation').prop('selectedIndex',0);
  }

  var step = 1;

  $('.prev-step').click(function (e) {
      e.preventDefault();

      if (step == 1) {

        window.open('{{url('design/add/select')}}','_self');

      }else{

        step -=1;

        $('.form-card[id*="step-').addClass('d-none').removeClass('show');
        $('.form-card[id="step-'+step+'"]').removeClass('d-none fade').addClass('show');

        if (localStorage.getItem('step'+step)) {
            $('#containment-wrapper'+step).html(localStorage.getItem('step'+step));
        }
        
        $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });    

        $('.elements.text').dblclick(editelements);
        $('.elements.context').dblclick(deleteElements);
        $('.elements.images').dblclick(changeImage);
        $('.elements.qr').dblclick(setQRtext);

        if ($('#containment-wrapper'+step).length) {
            if(localStorage.getItem('bg-step'+step)){
                $('#containment-wrapper'+step).css('background-color', localStorage.getItem('bg-step'+step));
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

      }
  });

  $('.next-step').click(function (e) {
      e.preventDefault();

      if (step == 5) {

        window.open('{{url('design?table=1')}}','_self');

      }else{
          step +=1;

          $('.form-card[id*="step-').addClass('d-none').removeClass('show');
          $('.form-card[id="step-'+step+'"]').removeClass('d-none fade').addClass('show');

          if (localStorage.getItem('step'+step)) {
            $('#containment-wrapper'+step).html(localStorage.getItem('step'+step));
          }

          $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });
          $('.elements.text').dblclick(editelements);
          $('.elements.context').dblclick(deleteElements);
          $('.elements.images').dblclick(changeImage);
          $('.elements.qr').dblclick(setQRtext);

          if ($('#containment-wrapper'+step).length) {
              if(localStorage.getItem('bg-step'+step)){
                  $('#containment-wrapper'+step).css('background-color', localStorage.getItem('bg-step'+step));
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
      }

      if (step == 2) {

        let format = $('#format').val();
        let w = 200;
        let h = 92;

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

            let page = $('#page').val();

            let cols = $('#cols').val();
            let rows = $('#rows').val();

            let percent = 100 / cols;
            let margin = 1 / cols;

            if (page == 'a3') {
                w = 400 / cols;
                h = 276 / rows;
            } else if (page == 'a4') {
                w = 190 / cols;
                h = 277 / rows;
            }

            if (orientation == 'v') {
                let aux = w;
                w = h;
                h = aux;
            }

        }

        console.log(w,h);

        console.log($('[id*="containment-wrapper'),w,h)

        $('[id*="containment-wrapper').parent().css({
            width: w+'mm',
            height: h+'mm'
        });

        let matrix = $('#matrix-box').val() ?? 40;

        $('#containment-wrapper4').css('padding-right', matrix+'mm');

        $('.format-box-btn').css('width', w+'mm');

      }

  });

  var editor;
  var actualElement;

  function editelements(event) {

    var contenidoHTML = $(this).html();

    actualElement = $(this);

    if (editor) {
        editor.destroy();
        document.querySelector('#editor-toolbar').innerHTML = "";
    }

    DecoupledEditor.create(document.querySelector('#editor'), editorConfig).then(neweditor => {
        document.querySelector('#editor-toolbar').appendChild(neweditor.ui.view.toolbar.element);

        editor = neweditor;

        neweditor.setData(contenidoHTML);

        return neweditor;
    });

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

  $('.deleteElements').click(function (e) {
      e.preventDefault();

      if (confirm('¿Desea eliminar el elemento seleccionado?')) {
        actualElement.remove();
        $('#step').addClass('d-none');
        $('#save-step').removeClass('d-none');
    }
  });

  $('.accept-text').click(function(event) {
      /* Act on the event */
    $(actualElement).find('span').html(editor.getData());

    $('#ckeditor-modal').modal('hide');

    $('#step').addClass('d-none');
    $('#save-step').removeClass('d-none');
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
            <span>Escribe aquí...</span>
        </div>`);

      $('.elements.text').unbind('dblclick',editelements);
      $('.elements.text').dblclick(editelements);
      addEventsElement();

      $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });
  });
  $('.add-image').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements images" style="resize: both; overflow: hidden; position: absolute; top: 0"><span><img style="width: 100%; height: 100%" src="{{url('default.jpg')}}" alt=""></span></div>`);

      $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });

      $('.elements.images').unbind('dblclick',changeImage);
      $('.elements.images').dblclick(changeImage);
      addEventsElement();
  });

  $('.add-qr').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements qr" style="resize: both; overflow: hidden; position: absolute; top: 0"><span><img style="width: 100%; height: 100%" src="{{url('basicqr.jpg')}}" alt=""></span></div>`);

      $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });

      $('.elements.qr').unbind('dblclick',setQRtext);
      $('.elements.qr').dblclick(setQRtext);
      addEventsElement();
  });

  $('.add-top').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; top: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);

      $('.elements.context').unbind('dblclick',deleteElements);
      $('.elements.context').dblclick(deleteElements);
      addEventsElement();

      $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });
  });

  $('.add-bottom').click(function (e) {
      e.preventDefault();

      $('#containment-wrapper'+step).append(`<div class="elements context" style="width: calc(100% - 60px); border-radius: 10px; height: 10%; resize: both; overflow: hidden; position: absolute; bottom: 20px; left: 0; right: 0; margin: auto; background-color: #dfdfdf"><span style="padding: 20px; display: block;"></span></div>`);

      $('.elements.context').unbind('dblclick',deleteElements);
      $('.elements.context').dblclick(deleteElements);
      addEventsElement();

      $( ".elements" ).draggable({ handle: 'span', containment: "#containment-wrapper"+step, scroll: false, start: function(){$('#step').addClass('d-none');$('#save-step').removeClass('d-none');} });
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
  }

  function changePositionElement(event)
  {
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

</script>

{{-- {{url('design/add/select')}} --}}

@endsection