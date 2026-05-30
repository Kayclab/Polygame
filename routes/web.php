<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\OvertimeController as OwnerOvertimeController;
use App\Http\Controllers\FoodAllowanceController as OwnerFoodAllowanceController;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EvaluasiController;

use App\Http\Controllers\staff\DashboardController;
use App\Http\Controllers\staff\EvaluationController;
use App\Http\Controllers\staff\PayrollController;
use App\Http\Controllers\staff\FoodAllowanceController;
use App\Http\Controllers\staff\OvertimeController;
use App\Http\Controllers\staff\SettingsController;


/*
|--------------------------------------------------------------------------
| Login Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

Route::get('/login', [LoginController::class, 'view'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/

Route::prefix('owner')->group(function () {

    Route::get('/', [AnnouncementController::class, 'index'])
        ->name('owner.index');

    Route::post('/', [AnnouncementController::class, 'store'])
        ->name('owner.announcements.store');

    Route::put('/{id}', [AnnouncementController::class, 'update'])
        ->name('owner.announcements.update');

    Route::delete('/{id}', [AnnouncementController::class, 'destroy'])
        ->name('owner.announcements.destroy');

    Route::get('/management-karyawan', [KaryawanController::class, 'index'])
        ->name('owner.employees.index');

    Route::post('/management-karyawan', [KaryawanController::class, 'store'])
        ->name('owner.employees.store');

    Route::put('/management-karyawan/{id}', [KaryawanController::class, 'update'])
        ->name('owner.employees.update');

    Route::delete('/management-karyawan/{id}', [KaryawanController::class, 'destroy'])
        ->name('owner.employees.destroy');

    Route::get('/lembur', [OwnerOvertimeController::class, 'index'])
        ->name('owner.overtime.index');

    Route::post('/lembur/update-status/{id}', [OwnerOvertimeController::class, 'update'])
        ->name('owner.overtime.update');

    Route::get('/allowance', [OwnerFoodAllowanceController::class, 'index'])
        ->name('owner.allowance.index');

    Route::post('/allowance', [OwnerFoodAllowanceController::class, 'store'])
        ->name('owner.allowance.store');

    Route::put('/allowance/{id}', [OwnerFoodAllowanceController::class, 'update'])
        ->name('owner.allowance.update');

    Route::delete('/allowance/{id}', [OwnerFoodAllowanceController::class, 'destroy'])
        ->name('owner.allowance.destroy');

    Route::get('/payroll', [SlipGajiController::class, 'index'])
        ->name('owner.payroll.index');

    Route::post('/payroll', [SlipGajiController::class, 'store'])
        ->name('owner.payroll.store');

    Route::put('/payroll/{id}', [SlipGajiController::class, 'update'])
        ->name('payroll.update');

    Route::prefix('evaluasi')->name('owner.evaluasi.')->group(function () {

        Route::get('/', [EvaluasiController::class, 'index'])
            ->name('index');

        Route::post('/submit-audit', [EvaluasiController::class, 'submitEvaluation'])
            ->name('submit_audit');

        Route::post('/launch-audit', [EvaluasiController::class, 'launchAudit'])
            ->name('launch');

        Route::post('/metric/store', [EvaluasiController::class, 'storeMetric'])
            ->name('metrics.store');

        Route::put('/metric/{id}', [EvaluasiController::class, 'updateMetric'])
            ->name('metrics.update');

        Route::delete('/metric/{id}', [EvaluasiController::class, 'destroyMetric'])
            ->name('metrics.destroy');
    });

    Route::get('/notifications', [OwnerNotificationController::class,'index'])->name('owner.notifications.index');
    
    Route::post('/notifications/mark-read', [OwnerNotificationController::class,'markAllRead'])->name('owner.notifications.markAllRead');
});


/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('staff.settings.index');

    Route::put('/settings/update', [SettingsController::class, 'update'])
        ->name('settings.update');

});


/*
|--------------------------------------------------------------------------
| Staff Routes 
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'checkrole:staff'])->group(function () {

    Route::get('/staff', [DashboardController::class, 'index'])
        ->name('staff.index');

    Route::get('/evaluations', [EvaluationController::class, 'index'])
        ->name('staff.evaluations.index');

    Route::post('/evaluations/submit', [EvaluationController::class, 'submitEvaluation'])
        ->name('staff.evaluations.submit');

    Route::get('/payroll', [PayrollController::class, 'index'])
        ->name('staff.payroll.index');

    Route::get('/food-allowance', [FoodAllowanceController::class, 'index'])
        ->name('staff.food-allowance.index');

    Route::post('/food-allowance', [FoodAllowanceController::class, 'store'])
        ->name('staff.food-allowance.store');

    Route::get('/overtime', [OvertimeController::class, 'index'])
        ->name('staff.overtime.index');

    Route::post('/overtime', [OvertimeController::class, 'store'])
        ->name('staff.overtime.store');

    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsRead'])
        ->name('staff.notifications.readAll');

    Route::post('/notifications/read/{id}', [DashboardController::class, 'markNotificationRead'])
        ->name('staff.notifications.read');

    Route::delete('/notifications/{id}', [DashboardController::class, 'deleteNotification'])
        ->name('staff.notifications.delete');

});