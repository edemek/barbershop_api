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
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('duration'); // Duration in minutes
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
