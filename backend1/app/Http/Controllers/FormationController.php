<?php
namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FormationController extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

public function getFormations($userId, Request $request)
    {
        try {
            // Récupère le contenu brut de la requête (par exemple : "développeur web")
            $metier = $request->getContent();

            $apiKey = env('GEMINI_API_KEY'); // Clé API Gemini dans .env

    $question = "Quelles sont les technologies essentielles pour devenir $metier ? "
        . "Merci de répondre uniquement avec un tableau JSON listant ces technologies, "
        . "chaque élément contenant deux champs : 'name' (nom de la technologie, ex. CSS, HTML, Git) "
        . "et 'url' (lien vers une ressource d'apprentissage). Ne donne pas d'autres informations. donne moi 15 formation.";

            $requestBody = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $question]
                        ]
                    ]
                ]
            ];

            // Appel HTTP à l'API Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey", $requestBody);

            if (!$response->ok()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur lors de l’appel à l’API Gemini.'
                ], $response->status());
            }

            $data = $response->json();

            // Extraction du texte JSON dans la réponse
            $jsonText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$jsonText) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucune formation trouvée dans la réponse de l’API.'
                ], 404);
            }

      $cleanJson = trim(str_replace(["```json", "```"], '', $jsonText));

        // Décoder JSON (parfois JSON peut être double-encodé)
        $formations = json_decode($cleanJson, true);
        if (is_string($formations)) {
            $formations = json_decode($formations, true);
        }

        if (!is_array($formations)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format JSON reçu invalide.'
            ], 400);
        }

        // Valider la structure : tableau simple avec objets contenant 'name' et 'url'
        foreach ($formations as $formation) {
            if (!isset($formation['name']) || !isset($formation['url'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Les formations doivent contenir les champs "name" et "url".'
                ], 400);
            }
        }

        // Vérifier que l'utilisateur existe
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }

        // Insertion en base
        foreach ($formations as $formationData) {
            Formation::create([
                'formationName' => $formationData['name'],
                'url' => $formationData['url'],
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Formations enregistrées avec succès.',
            'data' => $formations,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur : ' . $e->getMessage()
        ], 500);
    }}
    public function getUserFormations($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Utilisateur non trouvé.'], 404);
        }

        $formations = Formation::where('user_id', $userId)->get();
        return response()->json($formations);
    }
}
