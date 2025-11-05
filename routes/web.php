<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BackController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\LotteryController;
use App\Http\Controllers\LotteryTypeController;
use App\Http\Controllers\LotteryScrutinyController;
use App\Http\Controllers\ScrutinyController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\ParticipationActivityLogController;
use App\Http\Controllers\DevolutionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
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

Route::get('comprobar-participacion', [App\Http\Controllers\ApiController::class, 'showParticipationTicket']);
Route::get('/participation-ticket', [ApiController::class, 'showParticipationTicket']);

// Rutas de autenticación
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Ruta para crear usuario administrador por defecto (solo en desarrollo)
Route::get('create-admin', [AuthController::class, 'createDefaultAdmin']);

// Rutas públicas de Firebase (necesarias para inicializar notificaciones)
Route::get('notifications/firebase-config', [NotificationController::class, 'getFirebaseConfig'])->name('notifications.firebase-config-public');

// Ruta para servir el service worker de Firebase
Route::get('firebase-messaging-sw.js', function () {
    $path = public_path('firebase-messaging-sw.js');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/javascript',
        'Service-Worker-Allowed' => '/'
    ]);
})->name('firebase-service-worker');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

Route::group(['prefix' => 'administrations'], function() {
    //
    Route::get('/', function() {return view('admins.index');})->name('administrations.index');
    Route::get('/add', function() {return view('admins.add');})->name('administrations.create');
    Route::get('/add/manager', function() {return view('admins.add_manager');})->name('administrations.add-manager');
    Route::post('/add/manager', [AdministratorController::class, 'store_information']);
    Route::post('/store', [AdministratorController::class, 'store']);

    Route::get('/view/{id}', function($id) { $administration = Administration::find($id); return view('admins.show', compact('administration'));})->name('administrations.show');
    Route::get('/edit/{id}', [AdministratorController::class, 'edit'])->name('administrations.edit');
    Route::put('/update/{id}', [AdministratorController::class, 'update'])->name('administrations.update');
    Route::get('/edit/manager/{id}', function($id) { 
        $administration = Administration::with('manager')->findOrFail($id); 
        return view('admins.edit_manager', compact('administration')); 
    })->name('administrations.edit-manager');
    Route::get('/edit/api/{id}', function() {return view('admins.edit_api');});
});

Route::group(['prefix' => 'entities'], function() {
    //
    Route::get('/', [EntityController::class, 'index'])->name('entities.index');
    Route::get('/add', [EntityController::class, 'create']);
    Route::post('/store-administration', [EntityController::class, 'store_administration']);
    Route::get('/add/information', [EntityController::class, 'create_information'])->name('entities.add-information');
    Route::post('/store-information', [EntityController::class, 'store_information']);
    Route::get('/add/manager', [EntityController::class, 'create_manager'])->name('entities.add-manager');
    Route::post('/store-manager', [EntityController::class, 'store_manager']);

    // Nuevas rutas para invitación de gestores
    Route::post('/check-manager-email', [EntityController::class, 'check_manager_email'])->name('entities.check-manager-email');
    Route::post('/invite-manager', [EntityController::class, 'invite_manager'])->name('entities.invite-manager');
    Route::post('/create-pending-entity', [EntityController::class, 'create_pending_entity'])->name('entities.create-pending-entity');
    
    // Ruta temporal para crear gestor de prueba
    Route::get('/create-test-manager', [EntityController::class, 'create_test_manager'])->name('entities.create-test-manager');

    Route::get('/view/{id}', [EntityController::class, 'show'])->name('entities.show');
    Route::get('/edit/{id}', [EntityController::class, 'edit']);
    Route::put('/update/{id}', [EntityController::class, 'update'])->name('entities.update');
    Route::delete('/destroy/{id}', [EntityController::class, 'destroy']);
    Route::get('/delete/{id}', [EntityController::class, 'destroy']);
    
    // Rutas para editar manager
    Route::get('/edit/manager/{id}', [EntityController::class, 'edit_manager'])->name('entities.edit-manager');
    Route::put('/update/manager/{id}', [EntityController::class, 'update_manager'])->name('entities.update-manager');
    
});

Route::group(['prefix' => 'managers'], function() {
    //
    Route::get('/edit/{id}', [ManagerController::class, 'edit'])->name('managers.edit');
    Route::put('/update/{id}', [ManagerController::class, 'update'])->name('managers.update');
    Route::delete('/destroy/{id}', [ManagerController::class, 'destroy'])->name('managers.destroy');
    Route::get('/delete/{id}', [ManagerController::class, 'destroy'])->name('managers.delete');
});

/*Route::get('entities',function() {
    return view('entities.index');
});*/
Route::group(['prefix' => 'sellers'], function() {
    Route::get('/', [SellerController::class, 'index'])->name('sellers.index');
    Route::get('/add', [SellerController::class, 'create'])->name('sellers.create');
    Route::post('/store-entity', [SellerController::class, 'store_entity'])->name('sellers.store-entity');
    Route::get('/add-information', [SellerController::class, 'add_information'])->name('sellers.add-information');
    Route::post('/store-existing-user', [SellerController::class, 'store_existing_user'])->name('sellers.store-existing-user');
    Route::post('/store-new-user', [SellerController::class, 'store_new_user'])->name('sellers.store-new-user');
    Route::get('/view/{id}', [SellerController::class, 'show'])->name('sellers.show');
    Route::get('/edit/{id}', [SellerController::class, 'edit'])->name('sellers.edit');
    Route::put('/update/{id}', [SellerController::class, 'update'])->name('sellers.update');
    Route::delete('/destroy/{id}', [SellerController::class, 'destroy'])->name('sellers.destroy');
    Route::get('/delete/{id}', [SellerController::class, 'destroy'])->name('sellers.delete');
    Route::post('/check-user-email', [SellerController::class, 'check_user_email'])->name('sellers.check-user-email');
    Route::post('/get-sets-by-reserve', [SellerController::class, 'getSetsByReserve'])->name('sellers.get-sets-by-reserve');
    Route::post('/validate-participations', [SellerController::class, 'validateParticipations'])->name('sellers.validate-participations');
    Route::post('/save-assignments', [SellerController::class, 'saveAssignments'])->name('sellers.save-assignments');
    Route::post('/get-assigned-participations', [SellerController::class, 'getAssignedParticipations'])->name('sellers.get-assigned-participations');
    Route::post('/remove-assignment', [SellerController::class, 'removeAssignment'])->name('sellers.remove-assignment');
    Route::post('/get-participations-by-book', [SellerController::class, 'getParticipationsByBook'])->name('sellers.get-participations-by-book');
    
    // Rutas para grupos de vendedores
    Route::post('/{id}/update-group', [SellerController::class, 'updateGroup'])->name('sellers.update-group');
    Route::get('/by-group', [SellerController::class, 'getByGroup'])->name('sellers.by-group');
    Route::get('/group-stats', [SellerController::class, 'getGroupStats'])->name('sellers.group-stats');
    
    // Rutas de liquidación de vendedores
    Route::get('/settlement-summary', [SellerController::class, 'getSettlementSummary'])->name('sellers.settlement-summary');
    Route::post('/settlement', [SellerController::class, 'storeSettlement'])->name('sellers.settlement.store');
    Route::get('/settlement-history', [SellerController::class, 'getSettlementHistory'])->name('sellers.settlement-history');
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
    Route::post('/generate', [LotteryController::class, 'generate'])->name('lotteries.generate');
    
    // Rutas adicionales para funcionalidades específicas
    Route::get('/administrations', [LotteryController::class, 'showAdministrations'])->name('lottery.administrations');
    Route::post('/select-administration', [LotteryController::class, 'selectAdministration'])->name('lottery.select-administration');
    Route::get('/results', [LotteryController::class, 'showLotteryResults'])->name('lottery.results');
    // Rutas de escrutinio
    Route::get('/scrutiny/{lottery}', [LotteryScrutinyController::class, 'show'])->name('lottery.scrutiny');
    Route::post('/scrutiny/{lottery}/process', [LotteryScrutinyController::class, 'process'])->name('lottery.process-scrutiny');
    Route::post('/scrutiny/{lottery}/save', [LotteryScrutinyController::class, 'save'])->name('lottery.save-scrutiny');
    Route::get('/scrutiny/{lottery}/administration/{administration}', [LotteryScrutinyController::class, 'showResults'])->name('lottery.show-administration-scrutiny');
    Route::delete('/scrutiny/{lottery}/administration/{administration}', [LotteryScrutinyController::class, 'delete'])->name('lottery.delete-scrutiny');
    // Ruta para escrutinio por categoría (números individuales)
    Route::get('/scrutiny/{lottery}/category', [LotteryScrutinyController::class, 'showByCategory'])->name('lottery.scrutiny-category');
    Route::get('/results/edit/{id}', [LotteryController::class, 'editLotteryResults'])->name('lottery.edit-results');
    
    // Rutas para resultados de lotería
    Route::post('/fetch-results', [LotteryController::class, 'fetchAndSaveResults'])->name('lottery.fetch-results');
    Route::post('/fetch-specific-results', [LotteryController::class, 'fetchSpecificResults'])->name('lottery.fetch-specific-results');
    Route::post('/save-results', [LotteryController::class, 'saveResults'])->name('lottery.save-results');
    Route::get('/results/{lottery}', [LotteryController::class, 'showResults'])->name('lottery.show-results');
    Route::get('/results-table', [LotteryController::class, 'resultsTable'])->name('lottery.results-table');
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
    Route::get('/download-xml/{set}', [SetController::class, 'downloadXml'])->name('sets.download-xml');
    Route::post('sets/{set}/import-xml', [App\Http\Controllers\SetController::class, 'importXml'])->name('sets.importXml');
    Route::get('/get-price', [SetController::class, 'getPrice'])->name('sets.get-price');
});

Route::group(['prefix' => 'participations'], function() {
    //
    Route::get('/', [ParticipationController::class, 'index'])->name('participations.index');
    Route::get('/add', [ParticipationController::class, 'create'])->name('participations.create');
    Route::post('/view-entity', [ParticipationController::class, 'store_entity'])->name('participations.view-entity');
    Route::get('/view/{id}', [ParticipationController::class, 'show'])->name('participations.show');
    Route::get('/view/{id}/seller', [ParticipationController::class, 'show_seller'])->name('participations.show-seller');
    Route::get('/book/{set_id}/{book_number}/participations', [ParticipationController::class, 'getBookParticipations'])->name('participations.book-participations');
    
    // Rutas para historial de actividades
    Route::get('/{id}/activity-log', [ParticipationActivityLogController::class, 'show'])->name('participations.activity-log');
    Route::get('/{id}/history', [ParticipationActivityLogController::class, 'getParticipationHistory'])->name('participations.history');
});

// Rutas para historial de actividades
Route::group(['prefix' => 'activity-logs'], function() {
    Route::get('/seller/{id}', [ParticipationActivityLogController::class, 'getSellerHistory'])->name('activity-logs.seller');
    Route::get('/entity/{id}', [ParticipationActivityLogController::class, 'getEntityHistory'])->name('activity-logs.entity');
    Route::get('/stats', [ParticipationActivityLogController::class, 'getActivityStats'])->name('activity-logs.stats');
    Route::get('/recent', [ParticipationActivityLogController::class, 'getRecentActivities'])->name('activity-logs.recent');
});

Route::group(['prefix' => 'design'], function() {
    //
    Route::get('/', [\App\Http\Controllers\DesignController::class, 'index'])->name('design.index');
    // Nuevo flujo con controlador
    Route::get('/add', [\App\Http\Controllers\DesignController::class, 'selectEntity'])->name('design.selectEntity');
    
    Route::post('/store-entity', [\App\Http\Controllers\DesignController::class, 'storeEntity'])->name('design.storeEntity');
    Route::post('design/add/lottery', [\App\Http\Controllers\DesignController::class, 'storeLottery'])->name('design.storeLottery');

    Route::get('/add/lottery/{entity_id?}', [\App\Http\Controllers\DesignController::class, 'selectLottery'])->name('design.selectLottery');
    Route::get('/add/set', [\App\Http\Controllers\DesignController::class, 'selectSet'])->name('design.selectSet');
    
    Route::post('/add/format', [\App\Http\Controllers\DesignController::class, 'format'])->name('design.format');
    // Route::post('design/format', [App\Http\Controllers\DesignController::class, 'storeFormat'])->name('design.storeFormat');

});

Route::get('/design/pdf/participation/{id}', [App\Http\Controllers\DesignController::class, 'exportParticipationPdf']);
Route::get('/design/pdf/participation-async/{id}', [App\Http\Controllers\DesignController::class, 'exportParticipationPdfAsync'])->name('design.exportParticipationPdfAsync');
Route::get('/design/pdf/status/{job_id}', [App\Http\Controllers\DesignController::class, 'checkPdfStatus'])->name('design.checkPdfStatus');
Route::get('/design/pdf/download/{job_id}', [App\Http\Controllers\DesignController::class, 'downloadPdf'])->name('design.downloadPdf');
Route::get('/design/pdf/cover/{id}', [App\Http\Controllers\DesignController::class, 'exportCoverPdf']);
Route::get('/design/pdf/cover-async/{id}', [App\Http\Controllers\DesignController::class, 'exportCoverPdfAsync'])->name('design.exportCoverPdfAsync');
Route::get('/design/pdf/back/{id}', [App\Http\Controllers\DesignController::class, 'exportBackPdf']);
Route::get('/design/pdf/back-async/{id}', [App\Http\Controllers\DesignController::class, 'exportBackPdfAsync'])->name('design.exportBackPdfAsync');
Route::get('/design/pdf/export-async', [App\Http\Controllers\DesignController::class, 'exportPdf'])->name('design.exportPdfAsync');
Route::post('/design/export-pdf', [App\Http\Controllers\DesignController::class, 'exportPdf']);
Route::get('/design/format/edit/{id}', [App\Http\Controllers\DesignController::class, 'editFormat'])->name('design.editFormat');
Route::put('/design/format/update/{id}', [App\Http\Controllers\DesignController::class, 'updateFormat'])->name('design.updateFormat');

Route::group(['prefix' => 'social'], function() {
    Route::get('/', [App\Http\Controllers\SocialWebController::class, 'index'])->name('social.index');
    Route::get('/add', [App\Http\Controllers\SocialWebController::class, 'create'])->name('social.create');
    Route::post('/add/design', [App\Http\Controllers\SocialWebController::class, 'storeEntity'])->name('social.store-entity');
    Route::get('/add/design/{entity_id}', [App\Http\Controllers\SocialWebController::class, 'addDesign'])->name('social.add-design');
    Route::post('/', [App\Http\Controllers\SocialWebController::class, 'store'])->name('social.store');
    Route::get('/edit/{id}', [App\Http\Controllers\SocialWebController::class, 'edit'])->name('social.edit');
    Route::put('/{id}', [App\Http\Controllers\SocialWebController::class, 'update'])->name('social.update');
    Route::delete('/{id}', [App\Http\Controllers\SocialWebController::class, 'destroy'])->name('social.destroy');
    Route::post('/{id}/change-status', [App\Http\Controllers\SocialWebController::class, 'changeStatus'])->name('social.change-status');
});
Route::get('requests',function() {
    return view('requests.index');
});
Route::get('communications',function() {
    return view('communications.index');
});

// Rutas de Escrutinio
Route::get('scrutiny', [ScrutinyController::class, 'index'])->name('scrutiny.index');
Route::post('scrutiny/generate', [ScrutinyController::class, 'generateScrutiny'])->name('scrutiny.generate');
Route::post('scrutiny/export', [ScrutinyController::class, 'exportScrutiny'])->name('scrutiny.export');

// Rutas de Devoluciones
// Rutas específicas ANTES del resource para evitar conflictos
Route::get('devolutions-data', [DevolutionsController::class, 'data'])->name('devolutions.data');
Route::get('devolutions/entities', [DevolutionsController::class, 'getEntities'])->name('devolutions.entities');
Route::get('devolutions/lotteries', [DevolutionsController::class, 'getLotteriesByEntity'])->name('devolutions.lotteries');
Route::get('devolutions/sellers', [DevolutionsController::class, 'getSellersByEntity'])->name('devolutions.sellers');
Route::get('devolutions/sets', [DevolutionsController::class, 'getSetsBySellerAndLottery'])->name('devolutions.sets');
Route::get('devolutions/sets-by-entity', [DevolutionsController::class, 'getSetsByEntityAndLottery'])->name('devolutions.sets-by-entity');
Route::get('devolutions/participations', [DevolutionsController::class, 'getParticipationsBySellerAndLottery'])->name('devolutions.participations');
Route::post('devolutions/validate', [DevolutionsController::class, 'validateParticipations'])->name('devolutions.validate');
Route::get('devolutions/liquidation-summary', [DevolutionsController::class, 'getLiquidationSummary'])->name('devolutions.liquidation-summary');

// Rutas para gestión de pagos de devoluciones
Route::get('devolutions/{id}/payments', [DevolutionsController::class, 'getPayments'])->name('devolutions.payments');
Route::post('devolutions/{id}/payments', [DevolutionsController::class, 'addPayment'])->name('devolutions.payments.store');
Route::put('devolutions/{devolutionId}/payments/{paymentId}', [DevolutionsController::class, 'updatePayment'])->name('devolutions.payments.update');
Route::delete('devolutions/{devolutionId}/payments/{paymentId}', [DevolutionsController::class, 'deletePayment'])->name('devolutions.payments.delete');

// Resource routes (deben ir AL FINAL para evitar conflictos)
Route::resource('devolutions', DevolutionsController::class);

// Rutas de Usuarios
Route::get('users-data', [UserController::class, 'data'])->name('users.data');
Route::resource('users', UserController::class);

// Rutas de Notificaciones
Route::group(['prefix' => 'notifications'], function() {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/dashboard', [NotificationController::class, 'dashboard'])->name('notifications.dashboard');
    Route::get('/create', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/store-type', [NotificationController::class, 'storeType'])->name('notifications.store-type');
    
    // Rutas para selección de entidad
    Route::get('/select-entity', [NotificationController::class, 'selectEntity'])->name('notifications.select-entity');
    Route::post('/store-entity', [NotificationController::class, 'storeEntity'])->name('notifications.store-entity');
    
    // Rutas para selección de administración
    Route::get('/select-administration', [NotificationController::class, 'selectAdministration'])->name('notifications.select-administration');
    Route::post('/store-administration', [NotificationController::class, 'storeAdministration'])->name('notifications.store-administration');
    
    // Rutas para selección de entidades de administración
    Route::get('/select-administration-entities', [NotificationController::class, 'selectAdministrationEntities'])->name('notifications.select-administration-entities');
    Route::post('/store-administration-entities', [NotificationController::class, 'storeAdministrationEntities'])->name('notifications.store-administration-entities');
    
    // Ruta para formulario de mensaje
    Route::get('/message', [NotificationController::class, 'message'])->name('notifications.message');
    Route::post('/store', [NotificationController::class, 'store'])->name('notifications.store');
    
    // Ruta para mensaje de éxito
    Route::get('/success', [NotificationController::class, 'success'])->name('notifications.success');
    
    // Ruta AJAX para obtener entidades por administración
    Route::get('/entities-by-administration', [NotificationController::class, 'getEntitiesByAdministration'])->name('notifications.entities-by-administration');
    
    // Ruta para registrar token FCM (requiere autenticación)
    Route::post('/register-token', [NotificationController::class, 'registerToken'])->name('notifications.register-token');
    
    // Ruta para enviar notificación de prueba
    Route::post('/send-test', [NotificationController::class, 'sendTest'])->name('notifications.send-test');
    
    // Ruta para eliminar notificación (al final para evitar conflictos)
    Route::delete('/delete/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

}); // Cierre del middleware auth