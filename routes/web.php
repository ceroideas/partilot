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
Route::get('sellers',function() {
    return view('sellers.index');
});
Route::get('users',function() {
    return view('users.index');
});
Route::get('lottery',function() {
    return view('lottery.index');
});
Route::get('reserves',function() {
    return view('reserves.index');
});
Route::get('participations',function() {
    return view('participations.index');
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