<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    // GET /api/questions
    public function getAllQuestions(): JsonResponse
    {
        // Récupérer toutes les questions
        $questions = Question::all();

        // Extraire seulement le texte des questions (supposons que la colonne est 'text')
        $questionTexts = $questions->pluck('text');

        return response()->json($questionTexts);
    }
}
