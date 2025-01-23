<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    // Afficher la liste des salons
    public function index()
    {
        $salons = Salon::all(); // Récupérer tous les salons
        return response()->json($salons); // Retourner en JSON
    }

    // Afficher un formulaire pour créer un salon (optionnel pour les API)
    public function create()
    {
        //
    }

    // Enregistrer un nouveau salon
    public function store(Request $request)
    {
        // Valider les données entrantes
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'owner_id' => 'required|exists:users,id',
            'seats_available' => 'required|integer',
            'opening_hours' => 'nullable|json',
        ]);

        // Créer le salon
        $salon = Salon::create($validated);

        return response()->json($salon, 201); // Retourner le salon créé
    }

    // Afficher un salon spécifique
    public function show(Salon $salon)
    {
        return response()->json($salon);
    }

    // Afficher un formulaire pour éditer un salon (optionnel pour les API)
    public function edit(Salon $salon)
    {
        //
    }

    // Mettre à jour un salon
    public function update(Request $request, Salon $salon)
    {
        // Valider les données
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'owner_id' => 'sometimes|required|exists:users,id',
            'seats_available' => 'sometimes|required|integer',
            'opening_hours' => 'nullable|json',
        ]);

        // Mettre à jour le salon
        $salon->update($validated);

        return response()->json($salon);
    }

    // Supprimer un salon
    public function destroy(Salon $salon)
    {
        $salon->delete(); // Supprimer le salon
        return response()->json(['message' => 'Salon supprimé avec succès']);
    }
}
