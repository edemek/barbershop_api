<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        $recommendations = Recommendation::all();
        return response()->json($recommendations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'recommended_service' => 'required|string|max:255',
        ]);

        $recommendation = Recommendation::create($validated);

        return response()->json($recommendation, 201);
    }

    public function show(Recommendation $recommendation)
    {
        return response()->json($recommendation);
    }

    public function destroy(Recommendation $recommendation)
    {
        $recommendation->delete();
        return response()->json(['message' => 'Recommandation supprimée avec succès']);
    }
}
