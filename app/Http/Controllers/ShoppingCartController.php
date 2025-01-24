<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    // Afficher la liste des éléments dans le panier
    public function index()
    {
        $cartItems = ShoppingCart::all(); // Récupérer tous les éléments du panier
        return response()->json($cartItems);
    }

    // Ajouter un élément au panier
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = ShoppingCart::create($validated);

        return response()->json($cartItem, 201);
    }

    // Afficher un élément spécifique du panier
    public function show(ShoppingCart $shoppingCart)
    {
        return response()->json($shoppingCart);
    }

    // Mettre à jour un élément du panier
    public function update(Request $request, ShoppingCart $shoppingCart)
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $shoppingCart->update($validated);

        return response()->json($shoppingCart);
    }

    // Supprimer un élément du panier
    public function destroy(ShoppingCart $shoppingCart)
    {
        $shoppingCart->delete();
        return response()->json(['message' => 'Élément supprimé du panier avec succès']);
    }
}
