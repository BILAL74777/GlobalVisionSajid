<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// ✅ Public Routes (No Authentication Needed)
Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/login', [HomeController::class, 'index'])->name('login');

// ✅ Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // 📌 Profile & Dashboard (Available for ALL authenticated users)
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/show/{id}', [UserController::class, 'profile_show'])->name('api.profile.show');
    Route::post('/profile/update', [UserController::class, 'profile_update'])->name('api.profile.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ✅ Super Admin Routes (FULL ACCESS)
    Route::middleware(['role:super-admin'])->group(function () {
        // Route::get('/gmails', [UserController::class, 'gmails'])->name('gmails');
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/visa/pakistan/afghanistan/individual', [VisaController::class, 'individual_index'])->name('visa.individual');
        Route::get('/visa/pakistan/afghanistan/family', [VisaController::class, 'family_index'])->name('visa.family');
        Route::get('/referrals', [VisaController::class, 'referrals'])->name('referrals');
        Route::get('/referral/details/{id}', [VisaController::class, 'referral_details'])->name('referral.details');
        Route::get('/employee/details/{id}', [EmployeeController::class, 'employee_details'])->name('employee.details');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    });

    // ✅ Employee Routes (Employees & Super Admin)
    Route::middleware(['role:employee,super-admin'])->group(function () {
        Route::get('/gmails', [UserController::class, 'gmails'])->name('gmails');
        Route::get('/visa/pakistan/afghanistan/individual', [VisaController::class, 'individual_index'])->name('visa.individual');
        Route::get('/visa/pakistan/afghanistan/family', [VisaController::class, 'family_index'])->name('visa.family');
        // Route::get('/employee/details/{id}', [EmployeeController::class, 'employee_details'])->name('employee.details');
    });

    // ✅ Referral Routes (Referrals & Super Admin)
    Route::middleware(['role:referral,super-admin'])->group(function () {
        // Route::get('/referrals', [VisaController::class, 'referrals'])->name('referrals');
        // Route::get('/referral/details/{id}', [VisaController::class, 'referral_details'])->name('referral.details');
    });
});
