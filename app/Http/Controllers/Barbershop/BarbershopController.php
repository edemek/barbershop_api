<?php

namespace App\Http\Controllers\Barbershop;

use App\Http\Controllers\Controller; 
use App\Models\Barbershop;
use Illuminate\Http\Request;

class BarbershopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barbershops = auth()->user()->barbershops()->latest()->get();
        return response(['barbershops' => $barbershops], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
        $barbershop = auth()->user()->barbershops()->create($validated);
        return response(['barbershop' => $barbershop], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Barbershop $barbershop)
    {
        dd('$request->input()') ;
        if ($barbershop->user_id != auth()->user()->id) {
            return response(['message' => 'Ce salon ne vous appartient pas.'], 403);
        }
        return response(['barbershop' => $barbershop], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request ,Barbershop $barbershop)
    {
       
        if ($barbershop->user_id != auth()->user()->id) {
            return response(['message' => 'Ce salon ne vous appartient pas.'], 403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
       
        $barbershop->update($validated);
        return response(['barbershop' => $barbershop], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barbershop $barbershop)
    {
        if ($barbershop->user_id != auth()->user()->id) {
            return response(['message' => 'Ce salon ne vous appartient pas.'], 403);
        }
        
        $barbershop->delete();
        return response(['message' => 'Salon supprimé.'], 200);
    }

}
