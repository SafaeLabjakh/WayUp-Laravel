<?php

namespace App\Http\Controllers;

use App\Services\InternshipSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InternshipSuggestionController extends Controller
{
    protected $internshipService;

    public function __construct(InternshipSuggestionService $internshipService)
    {
        $this->internshipService = $internshipService;
    }

    public function getInternshipsForAuthenticatedUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $jobTitle = $user->metier_sugg;

        if (empty($jobTitle)) {
            return response()->json(['error' => "Le métier suggéré n'est pas défini pour cet utilisateur"], 400);
        }

        Log::info("Fetching internships for user {$user->email} and job title $jobTitle");

        $internships = $this->internshipService->fetchInternships($jobTitle);

        return response()->json($internships);
    }

    // Pour tester avec un métier donné (sans auth)
    public function testInternshipApi(string $jobTitle)
    {
        $internships = $this->internshipService->fetchInternships($jobTitle);

        return response()->json($internships);
    }
}
