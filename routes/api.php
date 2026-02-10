<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BackController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LotteryController;
use App\Http\Controllers\DevolutionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ManagerController;

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

Route::post('/design/save-format', [App\Http\Controllers\DesignController::class, 'saveFormat']);

Route::get('test', [ApiController::class,'test']);

Route::get('/check-delete/{type}/{id}', [ApiController::class, 'checkDelete']);
Route::delete('/delete/{type}/{id}', [ApiController::class, 'deleteItem']);

Route::post('/design/save-snapshot', [\App\Http\Controllers\DesignController::class, 'saveSnapshot']);

// ============================================================================
// RUTAS PÚBLICAS (Sin autenticación)
// ============================================================================

// Verificar participación por referencia (pública)
Route::get('/participation/check', [ApiController::class, 'checkParticipation']);
Route::get('/participation-ticket', [ApiController::class, 'showParticipationTicket']);

// Configuración de Firebase (pública para inicialización)
Route::get('/notifications/firebase-config', [NotificationController::class, 'getFirebaseConfig']);

// ============================================================================
// RUTAS DE AUTENTICACIÓN
// ============================================================================

Route::prefix('auth')->group(function () {
    // Login
    Route::post('/login', [AuthController::class, 'apiLogin']);
    
    // Registro (si aplica)
    Route::post('/register', [AuthController::class, 'apiRegister']);
    
    // Obtener usuario autenticado
    Route::middleware('auth.api')->get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });
    
    // Logout
    Route::middleware('auth.api')->post('/logout', [AuthController::class, 'apiLogout']);
    
    // Refresh token
    Route::middleware('auth.api')->post('/refresh', [AuthController::class, 'apiRefresh']);
    
    // Verificar token
    Route::middleware('auth.api')->get('/verify', function (Request $request) {
        return response()->json(['valid' => true, 'user' => $request->user()]);
    });
});

// ============================================================================
// RUTAS PROTEGIDAS (Requieren autenticación)
// ============================================================================

Route::middleware('auth.api')->group(function () {
    
    // ========================================================================
    // PERFIL Y USUARIO
    // ========================================================================
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'apiGetProfile']);
        Route::put('/', [UserController::class, 'apiUpdateProfile']);
        Route::post('/change-password', [UserController::class, 'apiChangePassword']);
        Route::post('/upload-avatar', [UserController::class, 'apiUploadAvatar']);
    });
    
    // ========================================================================
    // PARTICIPACIONES
    // ========================================================================
    Route::prefix('participations')->group(function () {
        // Listar participaciones
        Route::get('/', [ParticipationController::class, 'apiIndex']);
        
        // Obtener participación específica
        Route::get('/{id}', [ParticipationController::class, 'apiShow']);
        
        // Crear/Asignar participación
        Route::post('/', [ParticipationController::class, 'apiStore']);
        
        // Vender participación
        Route::post('/{id}/sell', [ParticipationController::class, 'apiSell']);
        
        // Digitalizar participación (escanear QR)
        Route::post('/digitalize', [ParticipationController::class, 'apiDigitalize']);
        
        // Regalar participación
        Route::post('/{id}/gift', [ParticipationController::class, 'apiGift']);
        
        // Obtener participaciones por vendedor
        Route::get('/seller/{sellerId}', [ParticipationController::class, 'apiGetBySeller']);
        
        // Obtener participaciones por set/libro
        Route::get('/set/{setId}/book/{bookNumber}', [ParticipationController::class, 'getBookParticipations']);
        
        // Historial de participación
        Route::get('/{id}/history', [ParticipationController::class, 'apiGetHistory']);
        
        // Buscar participación por código/referencia
        Route::get('/search/{code}', [ParticipationController::class, 'apiSearch']);
    });
    
    // ========================================================================
    // VENTAS
    // ========================================================================
    Route::prefix('sales')->group(function () {
        // Venta por QR
        Route::post('/qr', [ParticipationController::class, 'apiSellByQr']);
        
        // Venta manual
        Route::post('/manual', [ParticipationController::class, 'apiSellManual']);
        
        // Historial de ventas del vendedor autenticado (para app móvil)
        Route::get('/me', [ParticipationController::class, 'apiGetMySales']);
        
        // Obtener ventas del vendedor (por ID)
        Route::get('/seller/{sellerId}', [ParticipationController::class, 'apiGetSalesBySeller']);
        
        // Estadísticas de ventas
        Route::get('/stats', [ParticipationController::class, 'apiGetSalesStats']);
    });
    
    // ========================================================================
    // VENDEDORES
    // ========================================================================
    Route::prefix('sellers')->group(function () {
        // Reservas y sets del vendedor autenticado (para app móvil)
        Route::get('/me/reserves', [SellerController::class, 'apiGetMyReserves']);
        Route::post('/me/validate-sale', [SellerController::class, 'apiValidateSale']);
        
        // Participaciones asignadas del vendedor autenticado
        Route::get('/me/entities', [SellerController::class, 'apiGetMyEntities']);
        Route::get('/me/tacos', [SellerController::class, 'apiGetMyTacos']);
        Route::get('/me/tacos/{setId}/{bookNumber}/participations', [SellerController::class, 'apiGetTacoParticipations']);

        // Listar vendedores
        Route::get('/', [SellerController::class, 'apiIndex']);
        
        // Obtener vendedor específico
        Route::get('/{id}', [SellerController::class, 'apiShow']);
        
        // Asignar participaciones a vendedor
        Route::post('/{id}/assign-participations', [SellerController::class, 'apiAssignParticipations']);
        
        // Obtener participaciones asignadas
        Route::get('/{id}/participations', [SellerController::class, 'apiGetParticipations']);
        
        // Obtener participaciones por libro
        Route::post('/get-participations-by-book', [SellerController::class, 'getParticipationsByBook']);
        
        // Remover asignación
        Route::post('/remove-assignment', [SellerController::class, 'removeAssignment']);
        
        // Validar participaciones
        Route::post('/validate-participations', [SellerController::class, 'validateParticipations']);
        
        // Guardar asignaciones
        Route::post('/save-assignments', [SellerController::class, 'saveAssignments']);
        
        // Obtener sets por reserva
        Route::post('/get-sets-by-reserve', [SellerController::class, 'getSetsByReserve']);
        
        // Liquidación de vendedor
        Route::get('/{id}/settlement-summary', [SellerController::class, 'getSettlementSummary']);
        Route::post('/{id}/settlement', [SellerController::class, 'storeSettlement']);
        Route::get('/{id}/settlement-history', [SellerController::class, 'getSettlementHistory']);
        
        // Estadísticas del vendedor
        Route::get('/{id}/stats', [SellerController::class, 'apiGetStats']);
        
        // Grupos de vendedores
        Route::get('/by-group', [SellerController::class, 'getByGroup']);
        Route::get('/group-stats', [SellerController::class, 'getGroupStats']);
        Route::post('/{id}/update-group', [SellerController::class, 'updateGroup']);
    });
    
    // ========================================================================
    // NOTIFICACIONES
    // ========================================================================
    Route::prefix('notifications')->group(function () {
        // Listar notificaciones
        Route::get('/', [NotificationController::class, 'apiIndex']);
        
        // Obtener notificación específica
        Route::get('/{id}', [NotificationController::class, 'apiShow']);
        
        // Marcar como leída
        Route::put('/{id}/read', [NotificationController::class, 'apiMarkAsRead']);
        
        // Marcar todas como leídas
        Route::post('/mark-all-read', [NotificationController::class, 'apiMarkAllAsRead']);
        
        // Contar no leídas
        Route::get('/unread/count', [NotificationController::class, 'apiUnreadCount']);
        
        // Registrar token FCM para push notifications
        Route::post('/register-token', [NotificationController::class, 'registerToken']);
        
        // Eliminar notificación
        Route::delete('/{id}', [NotificationController::class, 'apiDestroy']);
    });
    
    // ========================================================================
    // LOTERÍAS Y SORTEOS
    // ========================================================================
    Route::prefix('lotteries')->group(function () {
        // Listar loterías
        Route::get('/', [LotteryController::class, 'apiIndex']);
        
        // Obtener lotería específica
        Route::get('/{id}', [LotteryController::class, 'apiShow']);
        
        // Obtener resultados de lotería
        Route::get('/{id}/results', [LotteryController::class, 'apiGetResults']);
        
        // Obtener resultados por administración
        Route::get('/{id}/results/administration/{administrationId}', [LotteryController::class, 'apiGetResultsByAdministration']);
        
        // Loterías disponibles para venta
        Route::get('/available', [LotteryController::class, 'apiGetAvailable']);
        
        // Tipos de lotería
        Route::get('/types', [LotteryController::class, 'apiGetTypes']);
    });
    
    // ========================================================================
    // RESULTADOS Y ESCRUTINIO
    // ========================================================================
    Route::prefix('results')->group(function () {
        // Verificar si participación ganó
        Route::post('/check-winning', [ApiController::class, 'apiCheckWinning']);
        
        // Obtener resultados de participación
        Route::get('/participation/{participationId}', [ApiController::class, 'apiGetParticipationResults']);
        
        // Obtener resultados de sorteo
        Route::get('/lottery/{lotteryId}', [LotteryController::class, 'apiGetResults']);
    });
    
    // ========================================================================
    // CARTERA Y MOVIMIENTOS
    // ========================================================================
    Route::prefix('wallet')->group(function () {
        // Obtener cartera del usuario
        Route::get('/', [UserController::class, 'apiGetWallet']);
        
        // Obtener movimientos
        Route::get('/movements', [UserController::class, 'apiGetMovements']);
        
        // Obtener historial
        Route::get('/history', [UserController::class, 'apiGetHistory']);
        
        // Obtener participaciones en cartera
        Route::get('/participations', [ParticipationController::class, 'apiGetWalletParticipations']);
    });
    
    // ========================================================================
    // COBROS Y PAGOS
    // ========================================================================
    Route::prefix('payments')->group(function () {
        // Listar cobros disponibles
        Route::get('/available', [UserController::class, 'apiGetAvailablePayments']);
        
        // Solicitar cobro
        Route::post('/request', [UserController::class, 'apiRequestPayment']);
        
        // Historial de cobros
        Route::get('/history', [UserController::class, 'apiGetPaymentHistory']);
        
        // Obtener detalles de cobro
        Route::get('/{id}', [UserController::class, 'apiGetPaymentDetails']);
    });
    
    // ========================================================================
    // GESTIÓN (Para gestores)
    // ========================================================================
    Route::prefix('management')->middleware('role:super_admin,administration,entity')->group(function () {
        
        // Participaciones
        Route::prefix('participations')->group(function () {
            Route::get('/', [ParticipationController::class, 'apiManagementIndex']);
            Route::get('/stats', [ParticipationController::class, 'apiGetManagementStats']);
            Route::post('/bulk-assign', [ParticipationController::class, 'apiBulkAssign']);
        });
        
        // Vendedores
        Route::prefix('sellers')->group(function () {
            Route::get('/', [SellerController::class, 'apiManagementIndex']);
            Route::post('/', [SellerController::class, 'apiStore']);
            Route::put('/{id}', [SellerController::class, 'apiUpdate']);
            Route::delete('/{id}', [SellerController::class, 'apiDestroy']);
            Route::post('/{id}/toggle-status', [SellerController::class, 'toggleStatus']);
        });
        
        // Devoluciones
        Route::prefix('devolutions')->group(function () {
            Route::get('/', [DevolutionsController::class, 'apiIndex']);
            Route::post('/', [DevolutionsController::class, 'apiStore']);
            Route::get('/{id}', [DevolutionsController::class, 'apiShow']);
            Route::put('/{id}', [DevolutionsController::class, 'apiUpdate']);
            Route::delete('/{id}', [DevolutionsController::class, 'apiDestroy']);
            
            // Obtener entidades, loterías, vendedores, sets para devoluciones
            Route::get('/entities', [DevolutionsController::class, 'getEntities']);
            Route::get('/lotteries', [DevolutionsController::class, 'getLotteriesByEntity']);
            Route::get('/sellers', [DevolutionsController::class, 'getSellersByEntity']);
            Route::get('/sets', [DevolutionsController::class, 'getSetsBySellerAndLottery']);
            Route::get('/sets-by-entity', [DevolutionsController::class, 'getSetsByEntityAndLottery']);
            Route::get('/participations', [DevolutionsController::class, 'getParticipationsBySellerAndLottery']);
            Route::post('/validate', [DevolutionsController::class, 'validateParticipations']);
            Route::get('/liquidation-summary', [DevolutionsController::class, 'getLiquidationSummary']);
            
            // Pagos de devoluciones
            Route::get('/{id}/payments', [DevolutionsController::class, 'getPayments']);
            Route::post('/{id}/payments', [DevolutionsController::class, 'addPayment']);
            Route::put('/{devolutionId}/payments/{paymentId}', [DevolutionsController::class, 'updatePayment']);
            Route::delete('/{devolutionId}/payments/{paymentId}', [DevolutionsController::class, 'deletePayment']);
        });
        
        // Pagos de gestor
        Route::prefix('payments')->group(function () {
            Route::get('/', [ManagerController::class, 'apiGetPayments']);
            Route::post('/', [ManagerController::class, 'apiCreatePayment']);
            Route::get('/{id}', [ManagerController::class, 'apiGetPaymentDetails']);
        });
    });
    
    // ========================================================================
    // ENTIDADES Y ADMINISTRACIONES
    // ========================================================================
    Route::prefix('entities')->group(function () {
        Route::get('/', [EntityController::class, 'apiIndex']);
        Route::get('/{id}', [EntityController::class, 'apiShow']);
        Route::get('/{id}/lotteries', [EntityController::class, 'apiGetLotteries']);
        Route::get('/{id}/sellers', [EntityController::class, 'apiGetSellers']);
    });
    
    // ========================================================================
    // RESERVAS Y SETS
    // ========================================================================
    Route::prefix('reserves')->group(function () {
        Route::get('/', [ReserveController::class, 'apiIndex']);
        Route::get('/{id}', [ReserveController::class, 'apiShow']);
        Route::get('/{id}/sets', [ReserveController::class, 'apiGetSets']);
    });
    
    Route::prefix('sets')->group(function () {
        Route::get('/', [SetController::class, 'apiIndex']);
        Route::get('/{id}', [SetController::class, 'apiShow']);
        Route::get('/{id}/participations', [SetController::class, 'apiGetParticipations']);
        Route::get('/{id}/price', [SetController::class, 'getPrice']);
    });
    
    // ========================================================================
    // UTILIDADES Y ARCHIVOS (Versiones para app móvil con autenticación)
    // ========================================================================
    Route::prefix('utils')->group(function () {
        // Subir imagen (versión para app móvil)
        Route::post('/upload-image', function(Request $request) {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('uploads');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $url = url("uploads/{$filename}");
                
                return response()->json(['url' => $url]);
            }
            
            return response()->json(['error' => 'Imagen no válida'], 422);
        });
        
        // Generar QR (versión para app móvil)
        Route::post('/generate-qr', [BackController::class, 'generarQr']);
        
        // Verificar si se puede eliminar (versión para app móvil)
        Route::get('/check-delete/{type}/{id}', [ApiController::class, 'checkDelete']);
        
        // Eliminar item (versión para app móvil)
        Route::delete('/delete/{type}/{id}', [ApiController::class, 'deleteItem']);
    });
});