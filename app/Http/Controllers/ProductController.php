<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Afficher la liste des produits
    public function index()
    {
        $products = Product::all(); // Récupérer tous les produits
        return response()->json($products);
    }

    // Enregistrer un nouveau produit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'salon_id' => 'required|exists:salons,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    // Afficher un produit spécifique
    public function show(Product $product)
    {
        return response()->json($product);
    }

    // Mettre à jour un produit
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'stock_quantity' => 'sometimes|required|integer',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    // Supprimer un produit
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }
}
