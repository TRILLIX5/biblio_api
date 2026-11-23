<?php

use App\Http\Controllers\Api\V1\LivreController;
use Illuminate\Support\Facades\Route;

// Préfixe automatique /api+ ma v1 /api/v1/...
Route::prefix('v1')->group(function () {
    Route::get('livres', [LivreController::class, 'index']);
    Route::post('livres', [LivreController::class, 'store']);
    Route::get('livres/{livre}', [LivreController::class, 'show']);
    Route::patch('livres/{livre}', [LivreController::class, 'update']);
    Route::delete('livres/{livre}', [LivreController::class, 'destroy']);
});