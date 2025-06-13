<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;


Route::post('/getFormations', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
