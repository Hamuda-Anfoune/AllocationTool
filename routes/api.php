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
use App\Models\AcademicYear as AcademicYearModel;
use App\Models\Allocation;
use App\Models\Module as ModuleModel;
use App\Models\TaPreference;
use App\Models\UniversityUser;
use App\Models\User as UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:6,1');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::prefix('admin')->group(function () {
    Route::post('universities', [UniversityController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('university-users', [UniversityUsersController::class, 'index'])
            ->middleware('can:viewAny,'.UniversityUser::class);
        Route::post('university-users', [UniversityUsersController::class, 'store'])
            ->middleware('can:create,'.UniversityUser::class);

        Route::get('users', [UserController::class, 'index'])
            ->middleware('can:viewAny,'.UserModel::class);
        Route::get('users/admins', [UserController::class, 'showAllActiveAdmins'])
            ->middleware('can:viewAdmins,'.UserModel::class);
        Route::get('users/tas', [UserController::class, 'showAllActiveTas'])
            ->middleware('can:viewAny,'.UserModel::class);
        Route::get('users/convenors', [UserController::class, 'showAllActiveConvenors'])
            ->middleware('can:viewAny,'.UserModel::class);
        Route::get('users/tas/without-preferences/{academicYear}', [UserController::class, 'showTasWithoutPrefsForYear'])
            ->middleware('can:viewAny,'.UserModel::class);
        Route::get('users/convenors/without-preferences/{academicYear}', [UserController::class, 'showConvenorsWithoutPrefsForYear'])
            ->middleware('can:viewAny,'.UserModel::class);
        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->middleware('can:delete,user');

        Route::get('ta-preferences/{user}', [AdminTAController::class, 'show'])
            ->middleware('can:viewTaPreferences,user');

        Route::post('modules', [AdminModuleController::class, 'store'])
            ->middleware('can:create,'.ModuleModel::class);

        Route::put('academic-years', [AcademicYearController::class, 'update'])
            ->middleware('can:update,'.AcademicYearModel::class);

        Route::middleware('can:manage-configuration')->group(function () {
            Route::get('config', [ConfigurationController::class, 'index']);
            Route::put('config/module-priority-weights', [ConfigurationController::class, 'updateModulePriorityWeights']);
            Route::post('config/module-priority-weights/reset', [ConfigurationController::class, 'resetModulePriorityWeights']);
            Route::put('config/language-weights', [ConfigurationController::class, 'updateLanguageWeights']);
            Route::post('config/language-weights/reset', [ConfigurationController::class, 'resetLanguageWeights']);
        });

        Route::get('dashboard', [AllocationController::class, 'allocationData'])
            ->middleware('can:viewAny,'.Allocation::class);
        Route::post('allocations', [AllocationController::class, 'store'])
            ->middleware('can:create,'.Allocation::class);
        Route::get('allocations', [AllocationController::class, 'index'])
            ->middleware('can:viewAny,'.Allocation::class);
        Route::get('allocations/missing-preferences', [AllocationController::class, 'missingPrefs'])
            ->middleware('can:viewAny,'.Allocation::class);
        Route::get('allocations/{allocation}', [AllocationController::class, 'show'])
            ->middleware('can:viewAny,'.Allocation::class);
        Route::delete('allocations/current', [AllocationController::class, 'destroy'])
            ->middleware('can:delete,'.Allocation::class);
        Route::delete('allocations/{allocation}', [AllocationController::class, 'destroy'])
            ->middleware('can:delete,'.Allocation::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('module-preferences', [ModuleController::class, 'index'])
        ->middleware('can:viewAny,'.ModuleModel::class);
    // Ownership can't be checked until the module_id is resolved from the request body,
    // so this ability is authorized inside ModuleController::store() instead of here.
    Route::post('module-preferences', [ModuleController::class, 'store']);
    Route::get('module-preferences/{module}/{academicYear}', [ModuleController::class, 'show']);
    Route::put('module-preferences/{module}/{academicYear}', [ModuleController::class, 'update'])
        ->middleware('can:update,module');
    Route::delete('module-preferences/{module}/{academicYear}', [ModuleController::class, 'destroy'])
        ->middleware('can:delete,module');

    Route::get('convenor/modules', [ConvenorController::class, 'index'])
        ->middleware('can:viewAny,'.ModuleModel::class);

    Route::get('ta-preferences', [TAController::class, 'index']);
    Route::post('ta-preferences', [TAController::class, 'store'])
        ->middleware('can:create,'.TaPreference::class);
    Route::get('ta-preferences/{taPreference}', [TAController::class, 'show'])
        ->middleware('can:view,taPreference');
    Route::put('ta-preferences/{taPreference}', [TAController::class, 'update'])
        ->middleware('can:update,taPreference');
    Route::delete('ta-preferences/{taPreference}', [TAController::class, 'destroy'])
        ->middleware('can:delete,taPreference');
});
