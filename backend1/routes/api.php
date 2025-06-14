<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\CompanySuggestionController;

Route::post('/getFormations/{userId}', [FormationController::class, 'getFormations']);
Route::get('/formations/{userId}', [FormationController::class, 'getUserFormations']);
use App\Http\Controllers\CompanyController;

Route::post('/companies/suggestions', [CompanySuggestionController::class, 'suggestedCompanies']);
