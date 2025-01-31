<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('shopping_cart', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->integer('quantity');
        $table->timestamps();
    });
    
    /*
    Schema::create('shopping_cart', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id'); // Doit correspondre au type de 'id' dans 'products'
        $table->integer('quantity');
        $table->timestamps();
    
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
    */
    
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts');
    }
};
