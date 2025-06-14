<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Skill;
use App\Services\MetierService;
use Illuminate\Support\Facades\DB;

class MetierController extends Controller
{
    protected $metierService;

    public function __construct(MetierService $metierService)
    {
        $this->metierService = $metierService;
    }

    public function generateCareer($userId, Request $request)
    {
        $questions = $request->input('questions');
        $answers = $request->input('answers');

        $prompt = $this->buildPrompt($questions, $answers);
        \Log::info("Prompt envoyé:\n" . $prompt);

        $resultJson = $this->metierService->getGeminiResult($prompt);
        $metierSugg = $this->extractMetierSugg($resultJson);

        \Log::info("metierSugg :\n" . $metierSugg);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 400);
        }

        DB::transaction(function () use ($user, $metierSugg) {
            $user->skills()->delete();
            $user->metier_sugg = $metierSugg;
            $user->test_done = true;
            $user->save();
        });

        $skillsPrompt = "Liste les 10 compétences principales nécessaires pour devenir un " . $metierSugg . ". Réponds uniquement avec une liste simple, une compétence par ligne.";
        $skillsJson = $this->metierService->getGeminiResult($skillsPrompt);
        $skills = $this->extractSkillsFromJson($skillsJson, $user);

        Skill::insert($skills);

$message = "Nouveau métier : $metierSugg avec compétences mises à jour.";
$message = mb_convert_encoding($message, 'UTF-8', 'UTF-8');

return response()->json(['message' => $message]);
    }

    private function extractMetierSugg($json)
    {
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (preg_match('/\*\*(.*?)\*\*/', $text, $matches)) {
            return trim($matches[1]);
        }

        return explode("\n", $text)[0];
    }

    private function extractSkillsFromJson($json, $user)
    {
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $lines = explode("\n", $text);
        $skills = [];

        foreach ($lines as $line) {
            $cleaned = trim(preg_replace('/^[0-9\-\*\.\s]+/', '', $line));
            if (!empty($cleaned)) {
                $skills[] = [
                    'name' => $cleaned,
                    'acquired' => false,
                    'user_id' => $user->id
                ];
            }
        }

        return $skills;
    }

    private function buildPrompt($questions, $answers)
    {
        $prompt = "Based on the following answers to a career aptitude test, suggest only one profession for the user. Return only the name of the profession clearly marked in **bold**:\n";

        for ($i = 0; $i < count($questions); $i++) {
            $prompt .= $questions[$i] . " => " . $answers[$i] . "\n";
        }

        return $prompt;
    }
}

