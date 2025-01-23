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
    Schema::create('promotions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('salon_id')->constrained('salons')->onDelete('cascade');
        $table->string('title');
        $table->text('description')->nullable();
        $table->decimal('discount_percentage', 5, 2);
        $table->date('start_date');
        $table->date('end_date');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
