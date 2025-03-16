<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

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

// ✅ Authentication Routes
Route::post('login', [HomeController::class, 'login'])->name('api.login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [HomeController::class, 'logout_user'])->name('api.logout');

    // ✅ Dashboard (Super Admin & Employees)
    Route::middleware(['role:super-admin,employee'])->group(function () {
        Route::get('/dashboard/transaction/fetch', [DashboardController::class, 'dashboard_fetch'])
            ->name('api.dashboard.transaction.fetch');
    });
    Route::get('/referrals/pluck', [VisaController::class, 'refferrals_pluck'])->name('api.referrals.pluck');
    Route::get('/employees/pluck', [EmployeeController::class, 'pluck'])->name('api.employees.pluck');


    // ✅ Super Admin Routes (FULL API ACCESS)
    Route::middleware(['role:super-admin'])->group(function () {
        // 📌 Users API
        Route::get('/users/fetch', [UserController::class, 'users_fetch'])->name('api.users.fetch');
        Route::post('/users/store', [UserController::class, 'store'])->name('api.users.store');
        Route::delete('/users/delete/{id}', [UserController::class, 'delete'])->name('api.users.delete');
        Route::get('/users/show/{id}', [UserController::class, 'show'])->name('api.users.show');
        Route::post('/users/update', [UserController::class, 'users_update'])->name('api.users.update');

        // 📌 Employees API
        Route::post('/employee/store', [EmployeeController::class, 'store'])->name('api.employee.store');
        Route::get('/employees/fetch', [EmployeeController::class, 'fetch'])->name('api.employees.fetch');

        // 📌 Visa APIs (Super Admin can access everything)
        Route::post('/individual/customer/store', [VisaController::class, 'storeIndividual'])
            ->name('api.individual.customer.store');
        Route::get('/individual/customers/fetch', [VisaController::class, 'getIndividualVisas'])
            ->name('api.individual.customer.fetch');
        Route::post('/family/members/store', [VisaController::class, 'storeFamily'])
            ->name('api.family.members.store');
        Route::get('/family/members/fetch', [VisaController::class, 'getFamilyVisas'])
            ->name('api.family.members.fetch');
        Route::get('/customers/fetch', [VisaController::class, 'customers_fetch'])
            ->name('api.customers.fetch');
        Route::delete('/customer/visa/record/delete/{id}', [VisaController::class, 'visa_record_delete'])
            ->name('api.customer.visa.record.delete');
        Route::post('/family/member/update', [VisaController::class, 'updateFamilyRecord'])
            ->name('api.family.member.update');

        // 📌 Referrals API
        Route::get('/referrals/fetch', [VisaController::class, 'referrals_fetch'])->name('api.referrals.fetch');
        Route::post('/referrals/store', [VisaController::class, 'referral_store'])->name('api.referrals.store');
    });

    // ✅ Employee Routes (Visa Management)
    Route::middleware(['role:employee'])->group(function () {
        Route::post('/individual/customer/store', [VisaController::class, 'storeIndividual'])
            ->name('api.individual.customer.store');
        Route::get('/individual/customers/fetch', [VisaController::class, 'getIndividualVisas'])
            ->name('api.individual.customer.fetch');
        Route::post('/family/members/store', [VisaController::class, 'storeFamily'])
            ->name('api.family.members.store');
        Route::get('/family/members/fetch', [VisaController::class, 'getFamilyVisas'])
            ->name('api.family.members.fetch');
        Route::get('/customers/fetch', [VisaController::class, 'customers_fetch'])
            ->name('api.customers.fetch');
        Route::delete('/customer/visa/record/delete/{id}', [VisaController::class, 'visa_record_delete'])
            ->name('api.customer.visa.record.delete');
        Route::post('/family/member/update', [VisaController::class, 'updateFamilyRecord'])
            ->name('api.family.member.update');
    });

    // ✅ Referral Routes (Referral Management)
    Route::middleware(['role:referral'])->group(function () {
         Route::get('/referrals/fetch', [VisaController::class, 'referrals_fetch'])->name('api.referrals.fetch');
        Route::post('/referrals/store', [VisaController::class, 'referral_store'])->name('api.referrals.store');
    });
});
