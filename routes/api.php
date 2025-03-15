<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisaController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', [HomeController::class, 'login'])->name('api.login');
Route::middleware(['web'])->group(function () {
    Route::post('/logout', [HomeController::class, 'logout_user'])->name('api.logout');

    // Users Api
    Route::get('/users/fetch', [UserController::class, 'users_fetch'])->name('api.users.fetch');
    Route::post('/users/store', [UserController::class, 'store'])->name('api.users.store');
    Route::delete('/users/delete/{id}', [UserController::class, 'delete'])->name('api.users.delete');
    Route::get('/users/show/{id}', [UserController::class, 'show'])->name('api.users.show');
    Route::post('/users/update', [UserController::class, 'users_update'])->name('api.users.update');
    
    // Visa Api
    Route::post('/individual/customer/store', [VisaController::class, 'storeIndividual'])->name('api.individual.customer.store');
    Route::get('/individual/customers/fetch', [VisaController::class, 'getIndividualVisas'])->name('api.individual.customer.fetch');
    Route::post('/family/members/store', [VisaController::class, 'storeFamily'])->name('api.family.members.store');
    Route::get('/family/members/fetch', [VisaController::class, 'getFamilyVisas'])->name('api.family.members.fetch');
    Route::get('/customers/fetch', [VisaController::class, 'customers_fetch'])->name('api.customers.fetch');
    Route::delete('/customer/visa/record/delete/{id}', [VisaController::class, 'visa_record_delete'])->name('api.customer.visa.record.delete');
    Route::post('/family/member/update', [VisaController::class, 'updateFamilyRecord'])->name('api.family.member.update');
    
    
    Route::get('/referrals/pluck', [VisaController::class, 'refferrals_pluck'])->name('api.referrals.pluck');
    Route::get('/referrals/fetch', [VisaController::class, 'referrals_fetch'])->name('api.referrals.fetch');
    Route::post('/referrals/store', [VisaController::class, 'referral_store'])->name('api.referrals.store');

    // Employees
    Route::post('/employee/store', [EmployeeController::class, 'store'])->name('api.employee.store');
    Route::get('/employees/pluck', [EmployeeController::class, 'pluck'])->name('api.employees.pluck');
    Route::get('/employees/fetch', [EmployeeController::class, 'fetch'])->name('api.employees.fetch');
    // Route::delete('/customer/visa/record/delete/{id}', [VisaController::class, 'record_delete'])->name('api.referrals.delete');
    
});
