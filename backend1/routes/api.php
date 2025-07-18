<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\MetierController; 
use App\Http\Controllers\SkillController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CompanySuggestionController;
use App\Http\Controllers\InternshipSuggestionController;

Route::post('/getFormations/{userId}', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
Route::post('/resultat/{userId}', [MetierController::class, 'generateCareer']);
Route::get('/skills/{userId}', [SkillController::class, 'getSkillsByUserId']);
Route::put('/skills/{skillId}', [SkillController::class, 'updateSkill']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/questions', [TestController::class, 'getAllQuestions']);
Route::post('/companies/suggestions', [CompanySuggestionController::class, 'suggestedCompanies']);
Route::post('/getFormations/{userId}', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
Route::post('/resultat/{userId}', [MetierController::class, 'generateCareer']);
Route::get('/skills/{userId}', [SkillController::class, 'getSkillsByUserId']);
Route::put('/skills/{skillId}', [SkillController::class, 'updateSkill']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/questions', [TestController::class, 'getAllQuestions']);
Route::post('/companies/suggestions', [CompanySuggestionController::class, 'suggestedCompanies']);


Route::middleware('auth:sanctum')->get('/internships/me', [InternshipSuggestionController::class, 'getInternshipsForAuthenticatedUser']);
