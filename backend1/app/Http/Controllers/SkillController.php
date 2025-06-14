<?php
namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    // GET /api/skills/{userId}
    public function getSkillsByUserId($userId)
    {
        $skills = Skill::where('user_id', $userId)->get();
        return response()->json($skills);
    }

    // PUT /api/skills/{skillId}
    public function updateSkill(Request $request, $skillId)
    {
        $skill = Skill::find($skillId);
        if (!$skill) {
            return response()->json(['message' => 'Skill not found'], 404);
        }

        // Suppose que dans la requête JSON on a { "acquired": true/false }
        $skill->acquired = $request->input('acquired', $skill->acquired);
        $skill->save();

        return response()->json($skill);
    }
}
