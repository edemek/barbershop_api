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
    Schema::create('salons', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('address');
        $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
        $table->integer('seats_available');
        $table->json('opening_hours')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salons');
    }
};
