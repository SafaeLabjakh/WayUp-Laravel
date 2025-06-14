<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\MetierController; 


Route::post('/getFormations', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
Route::post('/resultat/{userId}', [MetierController::class, 'generateCareer']);
