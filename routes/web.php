<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\EmployeeController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/login', [HomeController::class, 'index'])->name('login');
Route::middleware(['web'])->group(function () {

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/users', [UserController::class, 'index'])->name('users');
    // vissa
    // Route::get('/visa/pakistan/afghanistan/family', [VisaController::class, 'family_index'])->name('visa.pakistan.afghanistan.family');
    // Route::get('/visa/pakistan/afghanistan/individual', [VisaController::class, 'individual_index'])->name('visa.pakistan.afghanistan.individual');

    Route::get('/visa/pakistan/afghanistan/individual', [VisaController::class, 'individual_index'])
        ->name('visa.individual');

    Route::get('/visa/pakistan/afghanistan/family', [VisaController::class, 'family_index'])
        ->name('visa.family');

    Route::get('/referrals', [VisaController::class, 'referrals'])
        ->name('referrals');
        Route::get('/referral/details/{id}', [VisaController::class, 'referral_details'])->name('referral.details');

// employees
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
Route::get('/employee/details/{id}', [EmployeeController::class, 'employee_details'])->name('employee.details');
     

});
