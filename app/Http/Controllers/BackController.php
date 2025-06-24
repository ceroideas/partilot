<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class BackController extends Controller
{
    //

   public function generarQr(Request $r)
   {
       $texto = $r->text;
       $nombreArchivo = uniqid().'.png';
       $rutaImagen = 'qrcodes/' . $nombreArchivo;

       QrCode::size(300)
           ->format('png')
           ->generate($texto, public_path($rutaImagen));

       return ['url' => url($rutaImagen)];
   }
}
