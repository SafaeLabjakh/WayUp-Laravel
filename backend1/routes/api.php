<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\MetierController; 
use App\Http\Controllers\SkillController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;


Route::post('/getFormations', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
Route::post('/resultat/{userId}', [MetierController::class, 'generateCareer']);
Route::get('/skills/{userId}', [SkillController::class, 'getSkillsByUserId']);
Route::put('/skills/{skillId}', [SkillController::class, 'updateSkill']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/questions', [TestController::class, 'getAllQuestions']);
