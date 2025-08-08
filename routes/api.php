<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\PrintTemplateController;
use App\Http\Controllers\InventoryHistoryController;
use App\Http\Controllers\CashRegisterTransactionController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\CashRequestController;
use App\Http\Controllers\CashReportController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'me');
        Route::post('/logout', 'logout');
        Route::get('/validate-token', 'validateToken');
    });

    Route::controller(OutletController::class)->group(function(){
        Route::get('/outlets/{outlet}', 'show');
    });

    Route::controller(ShiftController::class)->group(function(){
        Route::get('/shifts/{shift}', 'show');
    });

    Route::get('/products/barcode-image/{code}', [ProductController::class, 'generateBarcodeImage']);
    
    Route::middleware('role:admin,supervisor')->group(function () {
        
        Route::controller(AuthController::class)->prefix('user')->group(function () {
            Route::post('/register', 'register')->middleware('role:admin');
            Route::get('/all/{outletId}', 'getAllUsers');
            Route::put('/update/{user}', 'update')->middleware('role:admin');
            Route::delete('/delete/{user}', 'destroy')->middleware('role:admin');
        });

        Route::controller(OutletController::class)->group(function () {
            Route::get('/outlets', 'index');
            Route::post('/outlets', 'store')->middleware('role:admin');
            Route::post('/outlets/{outlet}', 'update')->middleware('role:admin'); 
            Route::delete('/outlets/{outlet}', 'destroy')->middleware('role:admin');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index');
            Route::post('/categories', 'store')->middleware('role:admin');
            Route::get('/categories/{category}', 'show');
            Route::put('/categories/{category}', 'update')->middleware('role:admin');
            Route::delete('/categories/{category}', 'destroy')->middleware('role:admin');
        });

        Route::controller(MemberController::class)->prefix('members')->group(function () {
            // Route::get('/', 'index');
            Route::post('/', 'store')->middleware('role:admin');
            Route::put('/{member}', 'update')->middleware('role:admin');
            Route::delete('/{member}', 'destroy')->middleware('role:admin');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/products', 'index');
            Route::post('/products', 'store')->middleware('role:admin');
            Route::get('/products/{product}', 'show');
            Route::post('/products/{product}', 'update')->middleware('role:admin');
            Route::delete('/products/{product}', 'destroy')->middleware('role:admin');
            Route::get('/products/outlet/{outletId}', 'getOutletProducts');
            Route::get('/products/barcode/{barcode}', 'findByBarcode')->middleware('role:kasir');
            Route::get('/products/generate-barcode', 'generateBarcode')->middleware('role:admin');
            Route::get('/outlets/{outletId}/products/barcode/{barcode}', 'posFindByBarcode')->middleware('role:kasir');
            Route::get('products/{product}/detail', [ProductController::class, 'getProductDetail']);
        });

        Route::controller(InventoryController::class)->group(function () {
            Route::get('/inventories', 'index');
            Route::post('/inventories/transfer', 'transferStock')->middleware('role:admin');
            Route::get('/inventories/listAll', 'listAllInventories');
            Route::post('/inventories', 'store')->middleware('role:admin');
            Route::get('/inventories/{inventory}', 'show');
            Route::put('/inventories/{inventory}', 'update')->middleware('role:admin');
            Route::delete('/inventories/{inventory}', 'destroy')->middleware('role:admin');
        });

        Route::controller(InventoryHistoryController::class)->group(function () {
            Route::get('/inventory-histories', 'index');
            Route::post('/inventory-histories', 'store')->middleware('role:admin');
            Route::post('/inventory-histories/approval', 'adminApprovStock')->middleware('role:admin');
            Route::post('/inventory-histories/reject', 'adminRejectStock')->middleware('role:admin');
            Route::get('/inventory-histories/{inventoryHistory}', 'show');
            Route::put('/inventory-histories/{inventoryHistory}', 'update')->middleware('role:admin');
            Route::delete('/inventory-histories/{inventoryHistory}', 'destroy')->middleware('role:admin');
            Route::get('/inventory-histories/stock/{outletId}', 'getStock');
            Route::get('/inventory-histories/outlet/{outletId}', 'getHistoryByOutlet');
            Route::get('/inventory-histories/type/{outletId}', 'getInventoryHistoryByType');
            Route::get('/notifications/stock-adjustments/{outletId}', 'getPendingStockAdjustments');
        });

        Route::controller(ShiftController::class)->group(function () {
            Route::get('/shifts', 'index');
            Route::post('/shifts', 'store')->middleware('role:admin');
            Route::put('/shifts/{shift}', 'update')->middleware('role:admin');
            Route::delete('/shifts/{shift}', 'destroy')->middleware('role:admin');
        });

        Route::controller(ReportController::class)->prefix('reports')->group(function () {
            Route::get('/daily-sales/{outlet}', 'dailySales');
            Route::get('/monthly-sales/{outlet}', 'listProductsByDateRange');
            Route::get('/monthly-inventory/{outlet}', 'inventoryReport');
            Route::get('/inventory-by-date/{outlet}', 'inventoryByDate');
            Route::get('/shift-report/{outlet}', 'shiftReport');
            Route::get('/dashboard-summary/{outlet}', 'dashboardSummary');

            Route::get('/sales-by-category/{outlet}', 'salesByCategory');
            Route::get('/inventory-approvals/{outlet}',  'inventoryApprovals');
            Route::get('/sales-by-member/{outlet}', 'listProductByMember');
        });

        Route::post('/print-template', [PrintTemplateController::class, 'store']);

        // Bonus management routes (Admin/Supervisor only)
        Route::controller(BonusController::class)->prefix('bonus')->group(function () {
            Route::get('/rules', 'getBonusRules'); // Get bonus rules for outlet
            Route::post('/rules', 'createBonusRule')->middleware('role:admin'); // Create bonus rule (admin only)
            Route::put('/rules/{id}', 'updateBonusRule')->middleware('role:admin'); // Update bonus rule (admin only)
            Route::delete('/rules/{id}', 'deleteBonusRule')->middleware('role:admin'); // Delete bonus rule (admin only)
            Route::get('/pending', 'getPendingBonuses'); // Get pending bonuses for approval
            Route::post('/approve/{id}', 'approveBonus'); // Approve bonus transaction
            Route::post('/reject/{id}', 'rejectBonus'); // Reject bonus transaction
            Route::get('/stats', 'getBonusStats'); // Get bonus statistics
        });

        // Order approval routes (Admin/Supervisor only)
        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('/pending', 'getPendingOrders'); // Get pending orders for approval
            Route::post('/approve/{id}', 'approveOrder'); // Approve order
            Route::post('/reject/{id}', 'rejectOrder'); // Reject order
            
            // Cancellation/Refund approval routes
            Route::get('/cancellation/pending', 'getPendingCancellations'); // Get pending cancellation/refund requests
            Route::post('/cancellation/approve/{id}', 'approveCancellation'); // Approve cancellation/refund
            Route::post('/cancellation/reject/{id}', 'rejectCancellation'); // Reject cancellation/refund
        });

        // Cash request approval routes (Admin/Supervisor only)
        Route::controller(CashRequestController::class)->prefix('cash-requests')->group(function () {
            Route::get('/pending', 'getPendingRequests'); // Get pending cash requests for approval
            Route::post('/approve/{id}', 'approveRequest'); // Approve cash request
            Route::post('/reject/{id}', 'rejectRequest'); // Reject cash request
            Route::get('/history', 'getRequests'); // Get cash requests history
        });

        // Cash reporting routes (Admin/Supervisor only)
        Route::controller(CashReportController::class)->prefix('cash-reports')->group(function () {
            Route::get('/current-balance', 'getCurrentBalance'); // Get current cash balance
            Route::get('/balance-trend', 'getBalanceTrend'); // Get balance trend for dashboard
            Route::get('/cash-flow-report', 'getCashFlowReport'); // Generate comprehensive cash flow report
            Route::get('/daily-snapshots', 'getDailySnapshots'); // Get daily snapshots with pagination
            Route::post('/generate-snapshot', 'generateSnapshot'); // Generate daily snapshot manually
            Route::post('/reconcile-balance', 'reconcileBalance'); // Reconcile cash balance with physical count
            Route::get('/dashboard-summary', 'getDashboardSummary'); // Get cash summary for dashboard
        });

        Route::get('/admin', function () {
            return response()->json([
                'message' => 'Ini untuk admin'
            ]);
        });
    }); 
    
    Route::middleware('role:kasir,admin,supervisor')->group(function () {
        
        Route::post('/upload/payment-proofs', [OrderController::class, 'uploadPaymentProofs']);

        Route::get('/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->middleware('role:kasir');

        Route::get('/print-template/{outlet_id}', [PrintTemplateController::class, 'show']);
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
        Route::get('/members', [MemberController::class, 'index']);
        Route::post('/print-receipt', [PrintController::class, 'printReceipt']);
        Route::post('/test-printer', [PrintController::class, 'testPrinter']);

        Route::get('/kasir-admin', function () {
            return response()->json([
                'message' => 'Ini untuk kasir dan admin'
            ]);
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/products/outlet/pos/{outletId}', 'getOutletProductsPos');
            Route::get('/products/outlet/{outletId}', 'getOutletProducts');
            Route::get('/outlets/{outletId}/products/barcode/{barcode}', 'posFindByBarcode');
        });

        Route::get('/categories', [CategoryController::class, 'index']);

        Route::controller(OrderController::class)->group(function () {
            Route::post('/orders', 'store');
            Route::get('/orders/revenue/{outletId}', 'oneMonthRevenue');
            Route::post('/orders/cancel/{orderId}', 'cancelOrder'); // Legacy method
            Route::get('/orders/history', 'orderHistory');
            Route::get('/orders/history/admin', 'orderAdmin');
            
            // Cancellation request route (for cashiers)
            Route::post('/orders/cancellation/request/{id}', 'requestCancellation'); // Request cancellation/refund
        });

        Route::controller(CashRegisterController::class)->group(function () {
            Route::get('/cash-registers', 'index');
            Route::get('/cash-registers/{outlet_id}', 'show');
        });

        Route::controller(InventoryHistoryController::class)->group(function() {
            Route::post('/adjust-inventory', 'cashierAdjustStock');
            Route::get('/adjust-inventory/{outlet_id}', 'showCashierInventoryHistories');
        });

        Route::controller(CashRegisterTransactionController::class)->prefix('cash-register-transactions')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::get('/cash-register/{id}', 'getByCashRegister');
            Route::get('/shift/{shiftId}', 'getByShift');
            Route::get('/type/{type}', 'getType');
            Route::get('/balance/{id}', 'getBalance');
            Route::post('/add-cash', 'addCash');
            Route::post('/subtract-cash', 'subtractCash');
        });

        // Bonus routes for cashiers, admin, and supervisors
        Route::controller(BonusController::class)->prefix('bonus')->group(function () {
            Route::post('/manual', 'createManualBonus'); // Create manual bonus transaction
            Route::post('/calculate-auto', 'calculateAutomaticBonus'); // Calculate automatic bonus for cart
            Route::get('/history', 'getBonusHistory'); // Get bonus history for outlet
        });

        // Cash request routes (for cashiers, admin, and supervisors)
        Route::controller(CashRequestController::class)->prefix('cash-requests')->group(function () {
            Route::post('/request', 'requestCash'); // Request cash addition/subtraction (cashiers)
            Route::get('/my-requests', 'getRequests'); // Get user's own cash requests
        });
    });

    Route::get('/print-template', function () {
        $outlet = auth()->user()->outlet; // Asumsi user terkait dengan outlet
        $template = [
            'company_name' => config('app.name'),
            'company_slogan' => 'Pelayanan Terbaik untuk Anda',
            'logo_url' => $outlet->logo_url ?? asset('images/logo.png'),
            'footer_message' => 'Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan',
        ];
        
        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    });
});
