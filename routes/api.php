<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Admin\TAController as AdminTAController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\UniversityUsersController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Allocation\AllocationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Prefs\ConvenorController;
use App\Http\Controllers\Prefs\ModuleController;
use App\Http\Controllers\Prefs\TAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::prefix('admin')->group(function () {
    Route::post('universities', [UniversityController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('university-users', [UniversityUsersController::class, 'index']);
        Route::post('university-users', [UniversityUsersController::class, 'store']);

        Route::get('users', [UserController::class, 'index']);
        Route::get('users/admins', [UserController::class, 'showAllActiveAdmins']);
        Route::get('users/tas', [UserController::class, 'showAllActiveTas']);
        Route::get('users/convenors', [UserController::class, 'showAllActiveConvenors']);
        Route::get('users/tas/without-preferences/{academicYear}', [UserController::class, 'showTasWithoutPrefsForYear']);
        Route::get('users/convenors/without-preferences/{academicYear}', [UserController::class, 'showConvenorsWithoutPrefsForYear']);
        Route::delete('users/{email}', [UserController::class, 'destroy']);

        Route::get('ta-preferences/{email}', [AdminTAController::class, 'show']);

        Route::post('modules', [AdminModuleController::class, 'store']);

        Route::put('academic-years', [AcademicYearController::class, 'update']);

        Route::get('config', [ConfigurationController::class, 'index']);
        Route::post('config', [ConfigurationController::class, 'store']);
        Route::put('config/module-priority-weights', [ConfigurationController::class, 'updateModulePriorityWeights']);
        Route::post('config/module-priority-weights/reset', [ConfigurationController::class, 'resetModulePriorityWeights']);
        Route::put('config/language-weights', [ConfigurationController::class, 'updateLanguageWeights']);
        Route::post('config/language-weights/reset', [ConfigurationController::class, 'resetLanguageWeights']);

        Route::get('dashboard', [AllocationController::class, 'allocationData']);
        Route::post('allocations', [AllocationController::class, 'store']);
        Route::get('allocations', [AllocationController::class, 'index']);
        Route::get('allocations/missing-preferences', [AllocationController::class, 'missingPrefs']);
        Route::get('allocations/{allocation}', [AllocationController::class, 'show']);
        Route::delete('allocations/current', [AllocationController::class, 'destroy']);
        Route::delete('allocations/{allocation}', [AllocationController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('module-preferences', [ModuleController::class, 'index']);
    Route::post('module-preferences', [ModuleController::class, 'store']);
    Route::get('module-preferences/{module}/{academicYear}', [ModuleController::class, 'show']);
    Route::put('module-preferences/{module}/{academicYear}', [ModuleController::class, 'update']);
    Route::delete('module-preferences/{module}/{academicYear}', [ModuleController::class, 'destroy']);

    Route::get('convenor/modules', [ConvenorController::class, 'index']);

    Route::get('ta-preferences', [TAController::class, 'index']);
    Route::post('ta-preferences', [TAController::class, 'store']);
    Route::get('ta-preferences/{taPreference}', [TAController::class, 'show']);
    Route::put('ta-preferences/{taPreference}', [TAController::class, 'update']);
    Route::delete('ta-preferences/{taPreference}', [TAController::class, 'destroy']);
});
