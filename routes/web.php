<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerCreditController;
use App\Http\Controllers\DailyRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OutletSwitcherController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

// Custom home route that redirects based on user role
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Dashboard route for regular users
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Admin Routes - All admin functionality protected by admin middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/access-control', [AdminController::class, 'accessControl'])->name('access-control');
    Route::get('/system-logs', [AdminController::class, 'systemLogs'])->name('system-logs');
    Route::get('/activity-log', [AdminController::class, 'activityLog'])->name('activity-log');
    Route::get('/database-backup', [AdminController::class, 'databaseBackup'])->name('database-backup');
    Route::post('/database-backup/create', [AdminController::class, 'createBackup'])->name('database-backup.create');
    Route::get('/database-backup/download/{filename}', [AdminController::class, 'downloadBackup'])->name('database-backup.download');
    Route::delete('/database-backup/delete/{filename}', [AdminController::class, 'deleteBackup'])->name('database-backup.delete');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
    Route::resource('outlets', \App\Http\Controllers\OutletController::class);
});

// M-Pesa Callback Route (must be accessible publicly)
Route::post('/mpesa/callback/stk', [POSController::class, 'mpesaCallback'])->name('mpesa.callback');

// Airtel Money Callback Route (must be accessible publicly)
Route::post('/airtel/callback', [POSController::class, 'airtelCallback'])->name('airtel.callback');

// ========== AUTH PROTECTED ROUTES ==========
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ========== POS Routes ==========
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.add');
    Route::post('/pos/update-cart', [POSController::class, 'updateCart'])->name('pos.update');
    Route::get('/pos/get-cart', [POSController::class, 'getCart'])->name('pos.cart');
    Route::post('/pos/clear-cart', [POSController::class, 'clearCart'])->name('pos.clear');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{id}', [POSController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/print/{id}', [POSController::class, 'printReceipt'])->name('pos.print');
    Route::get('/pos/search-product', [POSController::class, 'searchProduct'])->name('pos.search');
    Route::post('/pos/hold-ticket', [POSController::class, 'holdTicket'])->name('pos.hold');
    Route::get('/pos/held-tickets', [POSController::class, 'getHeldTickets'])->name('pos.held-tickets');
    Route::post('/pos/resume-ticket/{id}', [POSController::class, 'resumeTicket'])->name('pos.resume-ticket');
    Route::delete('/pos/cancel-ticket/{id}', [POSController::class, 'cancelTicket'])->name('pos.cancel-ticket');
    Route::post('/pos/apply-discount', [POSController::class, 'applyDiscount'])->name('pos.apply-discount');
    Route::post('/pos/remove-discount', [POSController::class, 'removeDiscount'])->name('pos.remove-discount');

    // Daily Register Routes
    Route::middleware('auth')->group(function () {
        Route::get('/daily-register', [DailyRegisterController::class, 'index'])->name('daily-register.index');
        Route::post('/daily-register/open', [DailyRegisterController::class, 'open'])->name('daily-register.open');
        Route::post('/daily-register/close', [DailyRegisterController::class, 'close'])->name('daily-register.close');
        Route::get('/daily-register/print/{date?}', [DailyRegisterController::class, 'print'])->name('daily-register.print');
    });

    // ========== Return/Exchange Routes ==========
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/search', [ReturnController::class, 'searchSale'])->name('returns.search');
    Route::get('/returns/{id}', [ReturnController::class, 'show'])->name('returns.show');
    Route::get('/returns/{id}/edit', [ReturnController::class, 'edit'])->name('returns.edit');
    Route::put('/returns/{id}', [ReturnController::class, 'update'])->name('returns.update');
    Route::delete('/returns/{id}', [ReturnController::class, 'destroy'])->name('returns.destroy');
    Route::get('/returns/{id}/print', [ReturnController::class, 'printReturn'])->name('returns.print');

    // ========== Transaction status routes ==========
    Route::get('/transaction/{id}/status', [POSController::class, 'checkTransactionStatus'])->name('transaction.status');
    Route::post('/transaction/{id}/process', [POSController::class, 'processMobilePayment'])->name('transaction.process');

    // ========== Product Management Routes ==========
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/get/{id}', [ProductController::class, 'getProduct'])->name('products.get');
    Route::post('/products/add-stock', [ProductController::class, 'addStock'])->name('products.add-stock');
    Route::get('/products/import/form', [ProductController::class, 'importForm'])->name('products.import.form');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class)->except(['show']);

    // ========== Category management routes ==========
    Route::resource('categories', CategoryController::class);

    // ========== Customer management routes ==========
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/ajax', [CustomerController::class, 'storeAjax'])->name('customers.storeAjax');

    // ========== Customer Credit Routes ==========
    Route::resource('credits', CustomerCreditController::class);
    Route::post('/credits/{credit}/payment', [CustomerCreditController::class, 'payment'])->name('credits.payment');
    Route::get('/credits/customer/{customerId}', [CustomerCreditController::class, 'getCustomerCredits'])->name('credits.customer');
    Route::get('/credits/customer-summary/{customerId}', [CustomerCreditController::class, 'customerSummary'])->name('credits.customer-summary');

    // ========== Outlet Switcher Route ==========
    Route::post('/admin/outlet/switch', [OutletSwitcherController::class, 'switch'])->name('admin.outlet.switch');

    // ========== Stub Routes for Upcoming Features ==========
    Route::get('/daily-register', [DailyRegisterController::class, 'index'])->name('daily-register.index');
    Route::post('/daily-register/open', [DailyRegisterController::class, 'open'])->name('daily-register.open');
    Route::post('/daily-register/close', [DailyRegisterController::class, 'close'])->name('daily-register.close');
    Route::get('/sales/all', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/sales/pos-list', [POSController::class, 'posList'])->name('sales.pos-list');
    Route::get('/sales/mpesa-transactions', [ReportController::class, 'mpesaTransactions'])->name('sales.mpesa-transactions');
    Route::get('/sales/drafts/create', function () {
        return 'Add Draft view coming soon';
    })->name('sales.drafts.create');
    Route::get('/sales/quotations', function () {
        return 'List Quotations view coming soon';
    })->name('sales.quotations.index');
    Route::get('/sales/quotations/create', function () {
        return redirect()->route('quotations.create');
    })->name('sales.quotations.create');
    Route::get('/sales/discounts', [DiscountController::class, 'index'])->name('sales.discounts');
    Route::get('/sales/import', function () {
        return 'Import Sale view coming soon';
    })->name('sales.import');

    // ========== Purchase Routes ==========
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    // Purchase Returns (must be before {id} wildcard)
    Route::get('/purchases/returns', [PurchaseReturnController::class, 'index'])->name('purchases.returns');
    Route::get('/purchases/returns/create', [PurchaseReturnController::class, 'create'])->name('purchases.returns.create');
    Route::post('/purchases/returns', [PurchaseReturnController::class, 'store'])->name('purchases.returns.store');
    Route::get('/purchases/returns/{id}', [PurchaseReturnController::class, 'show'])->name('purchases.returns.show');
    Route::delete('/purchases/returns/{id}', [PurchaseReturnController::class, 'destroy'])->name('purchases.returns.destroy');
    // Purchase wildcard (after specific routes)
    Route::post('/purchases/suppliers/store', [PurchaseController::class, 'storeSupplier'])->name('purchases.suppliers.store');
    Route::post('/purchases/products/store', [PurchaseController::class, 'storeProduct'])->name('purchases.products.store');
    Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/products/import-assigned', function () {
        return 'Import Assigned Products view coming soon';
    })->name('products.import-assigned');
    Route::get('/products/stock-breaking', function () {
        return 'Stock Breaking view coming soon';
    })->name('products.stock-breaking');
    Route::get('/products/price-groups', function () {
        return 'Selling Price Groups view coming soon';
    })->name('products.price-groups');
    Route::get('/products/units', function () {
        return 'Units view coming soon';
    })->name('products.units');
    // ========== Brand Routes ==========
    Route::get('/products/brands', [\App\Http\Controllers\BrandController::class, 'index'])->name('products.brands');
    Route::get('/products/brands/create', [\App\Http\Controllers\BrandController::class, 'create'])->name('brands.create');
    Route::post('/products/brands', [\App\Http\Controllers\BrandController::class, 'store'])->name('brands.store');
    Route::get('/products/brands/{brand}/edit', [\App\Http\Controllers\BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/products/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'update'])->name('brands.update');
    Route::delete('/products/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'destroy'])->name('brands.destroy');

    // ========== Warranty Routes ==========
    Route::middleware('auth')->group(function () {
        Route::get('/warranties', [WarrantyController::class, 'index'])->name('warranties.index');
        Route::get('/warranties/create', [WarrantyController::class, 'create'])->name('warranties.create');
        Route::post('/warranties', [WarrantyController::class, 'store'])->name('warranties.store');
        Route::get('/warranties/{warranty}', [WarrantyController::class, 'show'])->name('warranties.show');
        Route::get('/warranties/{warranty}/edit', [WarrantyController::class, 'edit'])->name('warranties.edit');
        Route::put('/warranties/{warranty}', [WarrantyController::class, 'update'])->name('warranties.update');
        Route::delete('/warranties/{warranty}', [WarrantyController::class, 'destroy'])->name('warranties.destroy');
        Route::get('/warranties/{warranty}/claims/create', [WarrantyController::class, 'createClaim'])->name('warranties.claims.create');
        Route::post('/warranties/{warranty}/claims', [WarrantyController::class, 'storeClaim'])->name('warranties.claims.store');
        Route::put('/claims/{claim}', [WarrantyController::class, 'updateClaim'])->name('claims.update');
        Route::get('/warranties/{warranty}/print', [WarrantyController::class, 'printWarranty'])->name('warranties.print');
        Route::get('/warranties/check', [WarrantyController::class, 'checkWarranty'])->name('warranties.check');
    });

    // ========== Discount Routes ==========
    Route::middleware('auth')->group(function () {
        Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
        Route::get('/discounts/create', [DiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{discount}/edit', [DiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update');
        Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
        Route::patch('/discounts/{discount}/toggle-status', [DiscountController::class, 'toggleStatus'])->name('discounts.toggle-status');
    });
    // ========== Stock Adjustment Routes ==========
    Route::middleware('auth')->group(function () {
        Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])->name('stock-adjustments.index');
        Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stock-adjustments.create');
        Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('stock-adjustments.store');
        Route::get('/stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'show'])->name('stock-adjustments.show');
        Route::get('/stock-adjustments/{stockAdjustment}/edit', [StockAdjustmentController::class, 'edit'])->name('stock-adjustments.edit');
        Route::put('/stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'update'])->name('stock-adjustments.update');
        Route::get('/stock-adjustments/product-stock/{id}', [StockAdjustmentController::class, 'getProductStock'])->name('stock-adjustments.product-stock');
        Route::put('/stock-adjustments/{stockAdjustment}/complete', [StockAdjustmentController::class, 'complete'])->name('stock-adjustments.complete');
        Route::put('/stock-adjustments/{stockAdjustment}/cancel', [StockAdjustmentController::class, 'cancel'])->name('stock-adjustments.cancel');
    });

    Route::get('/stock-transfers', [StockTransferController::class, 'index'])->name('stock-transfers.index');
    Route::get('/stock-transfers/create', [StockTransferController::class, 'create'])->name('stock-transfers.create');
    Route::post('/stock-transfers', [StockTransferController::class, 'store'])->name('stock-transfers.store');
    Route::get('/stock-transfers/products', [StockTransferController::class, 'products'])->name('stock-transfers.products');
    Route::get('/stock-transfers/{stockTransfer}', [StockTransferController::class, 'show'])->name('stock-transfers.show');

    // ========== Expense Routes ==========
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    // ========== Quotation Routes ==========
    Route::middleware('auth')->group(function () {
        Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
        Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
        Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
        Route::post('/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.status');
        Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToSale'])->name('quotations.convert');
        Route::get('/quotations/{quotation}/print', [QuotationController::class, 'printQuotation'])->name('quotations.print');
    });

    // Category routes must be registered before /expenses/{expense}
    Route::get('/expenses/categories', [ExpenseController::class, 'categories'])->name('expenses.categories');
    Route::get('/expenses/categories/{category}/edit', [ExpenseController::class, 'editCategory'])->name('expenses.categories.edit');
    Route::post('/expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
    Route::put('/expenses/categories/{category}', [ExpenseController::class, 'updateCategory'])->name('expenses.categories.update');
    Route::delete('/expenses/categories/{category}', [ExpenseController::class, 'deleteCategory'])->name('expenses.categories.delete');

    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');

    // ========== REPORTS ROUTES ==========
    // Existing Reports
    Route::get('/reports/sales', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [DashboardController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/low-stock', [DashboardController::class, 'lowStockAlert'])->name('reports.low-stock');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/reports/tax', [ReportController::class, 'taxReport'])->name('reports.tax');
    Route::get('/reports/employee-performance', [ReportController::class, 'employeePerformance'])->name('reports.employee-performance');

    // NEW: Product Sell Report
    Route::get('/reports/product-sell', [ReportController::class, 'productSell'])->name('reports.product-sell');

    // NEW: Sell Payment Report
    Route::get('/reports/sell-payment', [ReportController::class, 'sellPayment'])->name('reports.sell-payment');

    // NEW: Purchase and Sale Report
    Route::get('/reports/purchase-sale', [ReportController::class, 'purchaseSale'])->name('reports.purchase-sale');

    // Other reports
    Route::get('/reports/items', function () {
        return view('reports.items');
    })->name('reports.items');

    Route::get('/reports/stock', function () {
        return view('reports.stock');
    })->name('reports.stock');

    // ========== EXPORT ROUTES ==========
    // Existing Export Routes
    Route::get('/reports/export/sales', [ReportController::class, 'exportSales'])->name('reports.export.sales');
    Route::get('/reports/export/profit-loss', [ReportController::class, 'exportProfitLoss'])->name('reports.export.profit-loss');
    Route::get('/reports/export/tax', [ReportController::class, 'exportTaxReport'])->name('reports.export.tax');
    Route::get('/reports/export-pdf/{type}', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // NEW: Export Routes for new reports
    Route::get('/reports/export/product-sell', [ReportController::class, 'exportProductSell'])->name('reports.export.product-sell');
    Route::get('/reports/export/sell-payment', [ReportController::class, 'exportSellPayment'])->name('reports.export.sell-payment');
    Route::get('/reports/export/purchase-sale', [ReportController::class, 'exportPurchaseSale'])->name('reports.export.purchase-sale');

    // ========== User Management Routes (Admin Only) ==========
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');
    });
});

require __DIR__.'/auth.php';
