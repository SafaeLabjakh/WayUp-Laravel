<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompanySuggestionController extends Controller
{
    public function fetchCompanies(string $jobTitle): string
    {
        $apiKey = env('GEMINI_API_KEY');

        // Le prompt que tu veux envoyer à Gemini, avec String.format-like en PHP
        $prompt = sprintf(
            "List 9 companies hiring for the role of %s in Morocco.\nReturn ONLY a JSON array in this exact format:\n[\n  {\n    \"id\": 1,\n    \"name\": \"Company Name\",\n    \"description\": \"Brief company description\",\n    \"industry\": \"Industry sector\",\n    \"location\": \"City, Morocco\",\n    \"size\": \"Number of employees range\",\n    \"foundedYear\": 2020\n  },\n  ...\n]\nDo not return markdown or a single object. Return ONLY a pure JSON array.",
            $jobTitle
        );

        $requestBody = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey", $requestBody);

        if (!$response->ok()) {
            throw new \Exception("Erreur API Gemini: " . $response->status());
        }

        return $response->body();
    }

    public function suggestedCompanies(Request $request)
    {
        $jobTitle = $request->input('metier');

        if (!$jobTitle) {
            return response()->json(['error' => 'Le champ metier est requis'], 400);
        }

        try {
            $jsonResponse = $this->fetchCompanies($jobTitle);

            // Ici tu retournes directement la réponse JSON brute
            return response($jsonResponse, 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}