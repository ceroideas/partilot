<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('login',function() {
    return view('login');
});

Route::group(['prefix' => 'administrations'], function() {
    //
    Route::get('/', function() {return view('admins.index');});
    Route::get('/add', function() {return view('admins.add');});
    Route::get('/add/manager', function() {return view('admins.add_manager');});
    Route::get('/view/{id}', function() {return view('admins.show');});
    Route::get('/edit/{id}', function() {return view('admins.edit');});
    Route::get('/edit/manager/{id}', function() {return view('admins.edit_manager');});
    Route::get('/edit/api/{id}', function() {return view('admins.edit_api');});
});

Route::group(['prefix' => 'entities'], function() {
    //
    Route::get('/', function() {return view('entities.index');});
    Route::get('/add', function() {return view('entities.add');});
    Route::get('/add/information', function() {return view('entities.add_information');});
    Route::get('/add/manager', function() {return view('entities.add_manager');});

    Route::get('/view/{id}', function() {return view('entities.show');});
    Route::get('/edit/{id}', function() {return view('entities.edit');});
    Route::get('/edit/manager/{id}', function() {return view('entities.edit_manager');});
    
});

Route::get('entities',function() {
    return view('entities.index');
});
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
    Route::get('/', function() {return view('lottery.index');});
    Route::get('/add', function() {return view('lottery.add');});
    Route::get('/edit/{id}', function() {return view('lottery.edit');});
    Route::get('/administrations', function() {return view('lottery.administrations');});
    Route::get('/results', function() {return view('lottery.lottery_results');});
    Route::get('/scrutiny/{id}', function() {return view('lottery.scrutiny');});
    Route::get('/results/edit/{id}', function() {return view('lottery.edit_lottery_results');});
});

Route::group(['prefix' => 'lottery_types'], function() {
    //
    Route::get('/', function() {return view('lottery_types.index');});
    Route::get('/add', function() {return view('lottery_types.add');});
    Route::get('/edit/{id}', function() {return view('lottery_types.edit');});
});

Route::group(['prefix' => 'reserves'], function() {
    //
    Route::get('/', function() {return view('reserves.index');});
    Route::get('/add', function() {return view('reserves.add');});
    Route::get('/add/lottery', function() {return view('reserves.add_lottery');});
    Route::get('/add/information', function() {return view('reserves.add_information');});
    Route::get('/edit/{id}', function() {return view('reserves.edit');});
});

Route::group(['prefix' => 'sets'], function() {
    //
    Route::get('/', function() {return view('sets.index');});
    Route::get('/add', function() {return view('sets.add');});
    Route::get('/add/reserve', function() {return view('sets.add_reserve');});
    Route::get('/add/information', function() {return view('sets.add_information');});
    Route::get('/edit/{id}', function() {return view('sets.edit');});
});

Route::group(['prefix' => 'participations'], function() {
    //
    Route::get('/', function() {return view('participations.index');});
    Route::get('/add', function() {return view('participations.add');});
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