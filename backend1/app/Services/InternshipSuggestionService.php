<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InternshipSuggestionService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key'); // à configurer dans config/services.php et .env
    }

    public function fetchInternships(string $jobTitle): array
    {
        Log::info('Clé API Gemini : ' . $this->apiKey);
        Log::info("Fetching internships for job title: $jobTitle");

        $prompt = <<<EOT
Liste 10 offres de stage disponibles pour le poste de $jobTitle au Maroc.
Retourne la réponse dans ce format JSON exact :
[
  {
    "id": 1,
    "title": "Intitulé du stage",
    "company": "Nom de l'entreprise",
    "description": "Brève description du stage",
    "location": "Ville, Maroc",
    "duration": "Durée du stage",
    "startDate": "Date de début (format YYYY-MM-DD)"
  },
  ... (9 autres stages)
]
Retourne uniquement le tableau JSON sans texte supplémentaire.
EOT;

        $requestBody = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 2048, // Augmentation pour plus de marge
            ],
        ];

        if (empty($this->apiKey)) {
            Log::error('API key Gemini manquante');
            return [];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}",
            $requestBody
        );

        if (!$response->successful()) {
            Log::error('Erreur API Gemini: ' . $response->body());
            return [];
        }

        $json = $response->json();

        // Vérifier la présence des données dans la réponse
        if (!isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Réponse API Gemini mal formée');
            Log::error('Réponse brute: ' . $response->body());
            return [];
        }

        $textContent = $json['candidates'][0]['content']['parts'][0]['text'];

        // Nettoyer le texte : retirer les backticks et balises markdown éventuelles
        $textContent = trim($textContent);
        $textContent = preg_replace('/^```json\s*/', '', $textContent); // supprime ```json au début
        $textContent = preg_replace('/\s*```$/', '', $textContent);     // supprime ``` à la fin

        Log::info("Réponse nettoyée Gemini : " . $textContent);

        // Extraction du JSON entre crochets
        $start = strpos($textContent, '[');
        $end = strrpos($textContent, ']');

        if ($start === false || $end === false || $end <= $start) {
            Log::error('Format JSON invalide : positions des crochets incorrectes');
            Log::error('Réponse brute Gemini : ' . $textContent);
            return [];
        }

        $jsonString = substr($textContent, $start, $end - $start + 1);

        Log::info("Extraction JSON brute : " . $jsonString);

        $internships = json_decode($jsonString, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $internships;
        }

        // Tentative de correction simple si JSON incomplet (fermeture des tableaux)
        $openBrackets = substr_count($jsonString, '[');
        $closeBrackets = substr_count($jsonString, ']');
        if ($openBrackets > $closeBrackets) {
            $jsonString .= str_repeat(']', $openBrackets - $closeBrackets);
            $internships = json_decode($jsonString, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                Log::warning('JSON corrigé automatiquement');
                return $internships;
            }
        }

        Log::error('Erreur décodage JSON : ' . json_last_error_msg());
        Log::error('JSON reçu : ' . $jsonString);

        return [];
    }
}
