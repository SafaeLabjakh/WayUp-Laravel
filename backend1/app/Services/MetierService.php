<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetierService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function getGeminiResult($prompt)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $this->apiKey;

        $body = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $body);

        if (!$response->ok()) {
            throw new \Exception("Erreur API Gemini: " . $response->status());
        }

        return $response->json();
    }
}
