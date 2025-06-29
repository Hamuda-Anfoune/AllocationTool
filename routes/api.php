<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organisation\OrganisationController;


Route::get('/', function () {
    return json_encode(['message' => 'Welcome to the API!'], JSON_PRETTY_PRINT);
});

// Route collection for organisation data
Route::prefix('organisations')->group(function () {
    // User API resource instead of listing all routes
    // Route::apiResource('/', OrganisationController::class);

    Route::get('/', [OrganisationController::class, 'index']);
    Route::get('/{organisation}', [OrganisationController::class, 'show']);
    Route::post('/', [OrganisationController::class, 'store']);
    Route::put('/{organisation}', [OrganisationController::class, 'update']);
    Route::delete('/{organisation}', [OrganisationController::class, 'destroy']);
});