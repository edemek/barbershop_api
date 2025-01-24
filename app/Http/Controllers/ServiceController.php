<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Afficher la liste des services
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    // Enregistrer un nouveau service
    public function store(Request $request)
    {
        $validated = $request->validate([
            'salon_id' => 'required|exists:salons,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
        ]);

        $service = Service::create($validated);

        return response()->json($service, 201);
    }

    // Afficher un service spécifique
    public function show(Service $service)
    {
        return response()->json($service);
    }

    // Mettre à jour un service
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'duration' => 'sometimes|required|integer',
        ]);

        $service->update($validated);

        return response()->json($service);
    }

    // Supprimer un service
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['message' => 'Service supprimé avec succès']);
    }
}
