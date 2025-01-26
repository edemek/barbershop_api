<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Colonne ID auto-incrémentée
            $table->foreignId('salon_id') // Clé étrangère liée à la table salons
                ->constrained('salons') // Référence la table salons
                ->onDelete('cascade'); // Suppression en cascade si un salon est supprimé
            $table->string('name'); // Nom du produit
            $table->string('category'); // Catégorie du produit
            $table->decimal('price', 10, 2); // Prix (avec deux décimales)
            $table->integer('stock_quantity'); // Quantité en stock
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products'); // Supprime la table si elle existe
    }
};
