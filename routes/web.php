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

// Routes for Administrateur and Technicien
Route::middleware(['auth', 'role:Administrateur,Technicien'])
    ->group(function () {

    // Equipment management (RF-06 to RF-11 — coming soon)
    Route::get('/equipements', function () {
        return 'Gestion des équipements — Admin + Tech';
    })->name('equipements.index');

    // Assignments (RF-12 to RF-16 — coming soon)
    Route::get('/affectations', function () {
        return 'Affectations — Admin + Tech';
    })->name('affectations.index');

    // Maintenance (RF-21 to RF-23 — coming soon)
    Route::get('/maintenance', function () {
        return 'Maintenance — Admin + Tech';
    })->name('maintenance.index');

    // Category management
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);

    Route::resource('products', \App\Http\Controllers\ProductController::class);

    Route::resource('licenses', \App\Http\Controllers\LicenseController::class);

    // Assignment CRUD
    Route::resource('assignments',
        \App\Http\Controllers\AssignmentController::class);

    // Return asset — RF-14
    Route::post('assignments/{assignment}/return',
        [\App\Http\Controllers\AssignmentController::class, 'returnAsset'])
        ->name('assignments.return');

    // Maintenance CRUD
    Route::resource('maintenances',
        \App\Http\Controllers\MaintenanceController::class);

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