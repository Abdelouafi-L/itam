<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public routes — no login required
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Show forms
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

// Handle form submissions
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Protected routes — login required
|--------------------------------------------------------------------------
*/

// Routes accessible by ALL authenticated users
Route::middleware('auth')->group(function () {

    Route::get('/dashboard',
        [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

});

// Routes for Administrateur only
Route::middleware(['auth', 'role:Administrateur'])->group(function () {

    // User management
    Route::resource('users',
        \App\Http\Controllers\UserController::class)
        ->except(['show']);

    Route::get('/configuration', function () {
        return view('admin.configuration');
    })->name('configuration');

    // RF-23 — Retire asset — Admin only
    Route::post('hardware/{hardware}/retire',
        [\App\Http\Controllers\MaintenanceController::class, 'retire'])
        ->name('hardware.retire');

});

/*
|--------------------------------------------------------------------------
| Admin + Technicien + Manager — read access for Manager
|--------------------------------------------------------------------------
*/

// Assignments — Admin + Tech (full) + Manager (read) + Employé (own)
Route::middleware(['auth', 'role:Administrateur,Technicien,Manager,Employé'])
    ->group(function () {
        Route::get('/assignments',
            [\App\Http\Controllers\AssignmentController::class, 'index'])
            ->name('assignments.index');

        Route::get('/assignments/{assignment}',
            [\App\Http\Controllers\AssignmentController::class, 'show'])
            ->name('assignments.show');
    });

// Assignments — create/store/return — Admin + Tech only
Route::middleware(['auth', 'role:Administrateur,Technicien'])
    ->group(function () {
        Route::get('/assignments/create',
            [\App\Http\Controllers\AssignmentController::class, 'create'])
            ->name('assignments.create');

        Route::post('/assignments',
            [\App\Http\Controllers\AssignmentController::class, 'store'])
            ->name('assignments.store');

        Route::post('assignments/{assignment}/return',
            [\App\Http\Controllers\AssignmentController::class, 'returnAsset'])
            ->name('assignments.return');

        Route::delete('/assignments/{assignment}',
            [\App\Http\Controllers\AssignmentController::class, 'destroy'])
            ->name('assignments.destroy');

        // Categories
        Route::resource('categories',
            \App\Http\Controllers\CategoryController::class);

        // Products
        Route::resource('products',
            \App\Http\Controllers\ProductController::class);

        // Licenses
        Route::resource('licenses',
            \App\Http\Controllers\LicenseController::class);

        // Maintenance
        Route::resource('maintenances',
            \App\Http\Controllers\MaintenanceController::class);
    });

// Licenses index + show — Manager read access
Route::middleware(['auth', 'role:Administrateur,Technicien,Manager'])
    ->group(function () {
        Route::get('/licenses',
            [\App\Http\Controllers\LicenseController::class, 'index'])
            ->name('licenses.index');

        Route::get('/licenses/{license}',
            [\App\Http\Controllers\LicenseController::class, 'show'])
            ->name('licenses.show');

        Route::get('/maintenances',
            [\App\Http\Controllers\MaintenanceController::class, 'index'])
            ->name('maintenances.index');

        Route::get('/maintenances/{maintenance}',
            [\App\Http\Controllers\MaintenanceController::class, 'show'])
            ->name('maintenances.show');

        Route::get('/products',
            [\App\Http\Controllers\ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/products/{product}',
            [\App\Http\Controllers\ProductController::class, 'show'])
            ->name('products.show');
    });

// Routes for Administrateur and Manager
Route::middleware(['auth', 'role:Administrateur,Manager'])
    ->group(function () {

    // Reports — RF-25, RF-26, RF-27, RF-28
    Route::prefix('rapports')->name('rapports.')->group(function () {

        // Replace the placeholder route with real ones
        Route::get('inventaire',
            [\App\Http\Controllers\ReportController::class, 'inventory'])
            ->name('inventory');

        Route::get('inventaire/pdf',
            [\App\Http\Controllers\ReportController::class, 'inventoryPdf'])
            ->name('inventory.pdf');

        Route::get('inventaire/csv',
            [\App\Http\Controllers\ReportController::class, 'inventoryCsv'])
            ->name('inventory.csv');

        Route::get('licences',
            [\App\Http\Controllers\ReportController::class, 'licenses'])
            ->name('licenses');

        Route::get('licences/pdf',
            [\App\Http\Controllers\ReportController::class, 'licensesPdf'])
            ->name('licenses.pdf');

        Route::get('licences/csv',
            [\App\Http\Controllers\ReportController::class, 'licensesCsv'])
            ->name('licenses.csv');

        Route::get('maintenances',
            [\App\Http\Controllers\ReportController::class, 'maintenances'])
            ->name('maintenances');

        Route::get('maintenances/pdf',
            [\App\Http\Controllers\ReportController::class,
            'maintenancesPdf'])
            ->name('maintenances.pdf');

        Route::get('maintenances/csv',
            [\App\Http\Controllers\ReportController::class,
            'maintenancesCsv'])
            ->name('maintenances.csv');
    });

});

// Employé — own assignments only
Route::middleware(['auth', 'role:Employé'])->group(function () {
    Route::get('/mes-affectations', function () {
        return redirect()->route('assignments.index');
    })->name('mes-affectations');
});