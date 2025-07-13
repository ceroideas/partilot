<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BackController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\LotteryController;
use App\Http\Controllers\LotteryTypeController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\AuthController;
use App\Models\Administration;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas de autenticación
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Ruta para crear usuario administrador por defecto (solo en desarrollo)
Route::get('create-admin', [AuthController::class, 'createDefaultAdmin']);

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

Route::group(['prefix' => 'administrations'], function() {
    //
    Route::get('/', function() {return view('admins.index');});
    Route::get('/add', function() {return view('admins.add');});
    Route::post('/add/manager', [AdministratorController::class, 'store_information']);
    Route::post('/store', [AdministratorController::class, 'store']);

    Route::get('/view/{id}', function($id) { $administration = Administration::find($id); return view('admins.show', compact('administration'));});
    Route::get('/edit/{id}', function() {return view('admins.edit');});
    Route::get('/edit/manager/{id}', function() {return view('admins.edit_manager');});
    Route::get('/edit/api/{id}', function() {return view('admins.edit_api');});
});

Route::group(['prefix' => 'entities'], function() {
    //
    Route::get('/', [EntityController::class, 'index'])->name('entities.index');
    Route::get('/add', [EntityController::class, 'create']);
    Route::post('/store-administration', [EntityController::class, 'store_administration']);
    Route::post('/store-information', [EntityController::class, 'store_information']);
    Route::post('/store-manager', [EntityController::class, 'store_manager']);

    Route::get('/view/{id}', [EntityController::class, 'show']);
    Route::get('/edit/{id}', [EntityController::class, 'edit']);
    Route::put('/update/{id}', [EntityController::class, 'update']);
    Route::delete('/destroy/{id}', [EntityController::class, 'destroy']);
    Route::get('/delete/{id}', [EntityController::class, 'destroy']);
    
});

/*Route::get('entities',function() {
    return view('entities.index');
});*/
Route::group(['prefix' => 'sellers'], function() {
    //
    Route::get('/', function() {return view('sellers.index');});
    Route::get('/add', function() {return view('sellers.add');});
    Route::get('/add/information', function() {return view('sellers.add_information');});
    Route::get('/edit/{id}', function() {return view('sellers.edit');});
});
Route::get('users',function() {
    return view('users.index');
});

Route::group(['prefix' => 'lottery'], function() {
    //
    Route::get('/', [LotteryController::class, 'index'])->name('lotteries.index');
    Route::get('/add', [LotteryController::class, 'create'])->name('lotteries.create');
    Route::post('/store', [LotteryController::class, 'store'])->name('lotteries.store');
    Route::get('/view/{lottery}', [LotteryController::class, 'show'])->name('lotteries.show');
    Route::get('/edit/{lottery}', [LotteryController::class, 'edit'])->name('lotteries.edit');
    Route::put('/update/{lottery}', [LotteryController::class, 'update'])->name('lotteries.update');
    Route::delete('/destroy/{lottery}', [LotteryController::class, 'destroy'])->name('lotteries.destroy');
    Route::get('/delete/{lottery}', [LotteryController::class, 'destroy'])->name('lotteries.delete');
    Route::post('/change-status/{lottery}', [LotteryController::class, 'changeStatus'])->name('lotteries.change-status');
    Route::delete('/delete-image/{lottery}', [LotteryController::class, 'deleteImage'])->name('lotteries.delete-image');
    
    // Rutas adicionales para funcionalidades específicas
    Route::get('/administrations', function() {return view('lottery.administrations');});
    Route::get('/results', function() {return view('lottery.lottery_results');});
    Route::get('/scrutiny/{id}', function() {return view('lottery.scrutiny');});
    Route::get('/results/edit/{id}', function() {return view('lottery.edit_lottery_results');});
});

Route::group(['prefix' => 'lottery_types'], function() {
    //
    Route::get('/', [LotteryTypeController::class, 'index'])->name('lottery-types.index');
    Route::get('/add', [LotteryTypeController::class, 'create'])->name('lottery-types.create');
    Route::post('/store', [LotteryTypeController::class, 'store'])->name('lottery-types.store');
    Route::get('/view/{lotteryType}', [LotteryTypeController::class, 'show'])->name('lottery-types.show');
    Route::get('/edit/{lotteryType}', [LotteryTypeController::class, 'edit'])->name('lottery-types.edit');
    Route::put('/update/{lotteryType}', [LotteryTypeController::class, 'update'])->name('lottery-types.update');
    Route::delete('/destroy/{lotteryType}', [LotteryTypeController::class, 'destroy'])->name('lottery-types.destroy');
    Route::get('/delete/{lotteryType}', [LotteryTypeController::class, 'destroy'])->name('lottery-types.delete');
    Route::post('/change-status/{lotteryType}', [LotteryTypeController::class, 'changeStatus'])->name('lottery-types.change-status');
    
    // Ruta para obtener categorías disponibles
    Route::get('/available-categories', [LotteryTypeController::class, 'getAvailablePrizeCategories'])->name('lottery-types.available-categories');
});

Route::group(['prefix' => 'reserves'], function() {
    //
    Route::get('/', [ReserveController::class, 'index'])->name('reserves.index');
    Route::get('/add', [ReserveController::class, 'create'])->name('reserves.create');
    Route::post('/store-entity', [ReserveController::class, 'store_entity'])->name('reserves.store-entity');
    Route::post('/store-entity-ajax', [ReserveController::class, 'store_entity_ajax'])->name('reserves.store-entity-ajax');
    Route::get('/add/lottery', [ReserveController::class, 'add_lottery'])->name('reserves.add-lottery');
    Route::post('/store-lottery', [ReserveController::class, 'store_lottery'])->name('reserves.store-lottery');
    Route::post('/store-lottery-ajax', [ReserveController::class, 'store_lottery_ajax'])->name('reserves.store-lottery-ajax');
    Route::get('/add/information', [ReserveController::class, 'add_information'])->name('reserves.add-information');
    Route::post('/store-information', [ReserveController::class, 'store_information'])->name('reserves.store-information');
    Route::get('/view/{reserve}', [ReserveController::class, 'show'])->name('reserves.show');
    Route::get('/edit/{reserve}', [ReserveController::class, 'edit'])->name('reserves.edit');
    Route::put('/update/{reserve}', [ReserveController::class, 'update'])->name('reserves.update');
    Route::delete('/destroy/{reserve}', [ReserveController::class, 'destroy'])->name('reserves.destroy');
    Route::get('/delete/{reserve}', [ReserveController::class, 'destroy'])->name('reserves.delete');
    Route::post('/change-status/{reserve}', [ReserveController::class, 'changeStatus'])->name('reserves.change-status');
    Route::get('/lotteries-by-entity', [ReserveController::class, 'getLotteriesByEntity'])->name('reserves.lotteries-by-entity');
});

Route::group(['prefix' => 'sets'], function() {
    //
    Route::get('/', [SetController::class, 'index'])->name('sets.index');
    Route::get('/add', [SetController::class, 'create'])->name('sets.create');
    Route::post('/store-entity', [SetController::class, 'store_entity'])->name('sets.store-entity');
    Route::post('/store-entity-ajax', [SetController::class, 'store_entity_ajax'])->name('sets.store-entity-ajax');
    Route::get('/add/reserve', [SetController::class, 'add_reserve'])->name('sets.add-reserve');
    Route::post('/store-reserve', [SetController::class, 'store_reserve'])->name('sets.store-reserve');
    Route::post('/store-reserve-ajax', [SetController::class, 'store_reserve_ajax'])->name('sets.store-reserve-ajax');
    Route::get('/add/information', [SetController::class, 'add_information'])->name('sets.add-information');
    Route::post('/store-information', [SetController::class, 'store_information'])->name('sets.store-information');
    Route::get('/view/{set}', [SetController::class, 'show'])->name('sets.show');
    Route::get('/edit/{set}', [SetController::class, 'edit'])->name('sets.edit');
    Route::put('/update/{set}', [SetController::class, 'update'])->name('sets.update');
    Route::delete('/destroy/{set}', [SetController::class, 'destroy'])->name('sets.destroy');
    Route::get('/delete/{set}', [SetController::class, 'destroy'])->name('sets.delete');
    Route::post('/change-status/{set}', [SetController::class, 'changeStatus'])->name('sets.change-status');
    Route::get('/reserves-by-entity', [SetController::class, 'getReservesByEntity'])->name('sets.reserves-by-entity');
});

Route::group(['prefix' => 'participations'], function() {
    //
    Route::get('/', function() {return view('participations.index');});
    Route::get('/add', function() {return view('participations.add');});
    Route::get('/view/{id}', function() {return view('participations.show');});
    Route::get('/view/{id}/seller', function() {return view('participations.show_seller');});
});

Route::group(['prefix' => 'design'], function() {
    //
    Route::get('/', function() {return view('design.index');});
    Route::get('/add', function() {return view('design.add');});
    Route::get('/add/lottery', function() {return view('design.add_lottery');});
    Route::get('/add/set', function() {return view('design.add_set');});
    Route::get('/add/select', function() {return view('design.add_design');});
    Route::get('/add/format', function() {return view('design.format');});
    Route::get('/add/draw', function() {return view('design.draw');});
});

Route::get('social',function() {
    return view('social.index');
});
Route::get('requests',function() {
    return view('requests.index');
});
Route::get('communications',function() {
    return view('communications.index');
});

}); // Cierre del middleware auth