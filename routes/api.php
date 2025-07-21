<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BackController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('upload-image', function(Request $request) {
    //
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        $file = $request->file('image');

        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');

        // Asegúrate de que la carpeta exista
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        $url = url("uploads/{$filename}");
        return response()->json(['url' => $url]);
    }

    return response()->json(['error' => 'Imagen no válida'], 422);
});

Route::post('generarQr', [BackController::class,'generarQr']);

Route::get('test', [ApiController::class,'test']);

Route::get('comprobar-participacion', [ApiController::class,'checkParticipation']);