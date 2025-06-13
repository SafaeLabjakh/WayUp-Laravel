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

 public function getFormations(Request $request)
{
    $userId = $request->input('userId');
    $metier = $request->input('metier');

    if (!$userId) {
        return response()->json(['status' => 'error', 'message' => 'Le champ "userId" est requis.'], 400);
    }

    if (!$metier) {
        return response()->json(['status' => 'error', 'message' => 'Le champ "metier" est requis.'], 400);
    }

    $question = "Quelles sont les technologies et compétences nécessaires pour devenir $metier au niveau d'etude ? Merci de répondre sous forme de JSON avec les champs 'name' pour le nom de la technologie/compétence et 'url' pour le lien vers la ressource d'apprentissage.";

    $body = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $question]
                ]
            ]
        ]
    ];

    $response = Http::withHeaders([
        'Content-Type' => 'application/json'
    ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}", $body);

    if (!$response->ok()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de l\'appel à l\'API Gemini.'
        ], $response->status());
    }

    $data = $response->json();
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $cleanJson = trim(str_replace(["```json", "```"], '', $text));
    $formations = json_decode($cleanJson, true);

    if (!is_array($formations)) {
        return response()->json(['status' => 'error', 'message' => 'Format de réponse inattendu.'], 400);
    }

    $user = User::find($userId);
    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'Utilisateur non trouvé.'], 404);
    }

    foreach ($formations as $formationData) {
        Formation::create([
            'formationName' => $formationData['name'] ?? '',
            'url' => $formationData['url'] ?? '',
            'userId' => $user->id
        ]);
    }

    return response()->json(['status' => 'success', 'message' => 'Formations enregistrées avec succès.']);
}

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
