<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class CompanySuggestionController extends Controller
{
 public function fetchCompanies(string $jobTitle): string
    {
        $apiKey = env('GEMINI_API_KEY');

        $question = "Liste les entreprises pertinentes pour le métier '$jobTitle' en JSON avec 'name' et 'url'.";

        $requestBody = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $question]
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

            // Tu peux parser ici ou retourner direct le JSON brut selon ta logique
            return response($jsonResponse, 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
