<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\MetierController; 
use App\Http\Controllers\SkillController;


Route::post('/getFormations', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
Route::post('/resultat/{userId}', [MetierController::class, 'generateCareer']);
Route::get('/skills/{userId}', [SkillController::class, 'getSkillsByUserId']);
Route::put('/skills/{skillId}', [SkillController::class, 'updateSkill']);