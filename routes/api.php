<?php

use App\Http\Controllers\Api\V1\EmpruntController;
use App\Http\Controllers\Api\V1\LivreController;
use App\Http\Controllers\Api\V1\MembreController;
use Illuminate\Support\Facades\Route;

//Routes livres yi
Route::prefix('v1')->group(function () {
    Route::get('livres', [LivreController::class, 'index']);
    Route::post('livres', [LivreController::class, 'store']);
    Route::get('livres/{livre}', [LivreController::class, 'show']);
    Route::patch('livres/{livre}', [LivreController::class, 'update']);
    Route::delete('livres/{livre}', [LivreController::class, 'destroy']);
    //Routes abb katt yi
    Route::post('emprunts', [EmpruntController::class, 'store']);
    Route::post('emprunts/{emprunt}/retour', [EmpruntController::class, 'retour']);
    //Route membre bi
    Route::get('membres/{membre}/emprunts', [MembreController::class, 'emprunts']);
});