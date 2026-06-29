<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('approved')->group(function (): void {
        Route::resource('customers', CustomerController::class);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/stock-adjustments', [ProductController::class, 'adjustStock'])->name('products.stock-adjustments.store');
        Route::get('stock', [StockController::class, 'index'])->name('stock.index');

        Route::resource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::get('invoices/{invoice}/pdf', InvoicePdfController::class)->name('invoices.pdf');

        Route::resource('payments', PaymentController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::get('invoices/{invoice}/payments/create', [PaymentController::class, 'create'])->name('invoices.payments.create');

        Route::prefix('reports')->name('reports.')->group(function (): void {
            Route::get('sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('payments', [ReportController::class, 'payments'])->name('payments');
            Route::get('outstanding', [ReportController::class, 'outstanding'])->name('outstanding');
            Route::get('export/{type}', [ReportController::class, 'export'])->name('export');
        });

        Route::get('erp-settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('erp-settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('users', UserController::class)->except(['show', 'destroy']);

        Route::middleware('role:super_admin')->group(function (): void {
            Route::resource('roles', RoleController::class)->except(['show']);
        });
    });
});

require __DIR__.'/settings.php';
