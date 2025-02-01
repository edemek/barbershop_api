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
        Schema::table('salons', function (Blueprint $table) {
            if (!Schema::hasColumn('salons', 'phone_number')) {
                $table->string('phone_number', 50)->nullable();
            }


         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('salons', 'phone_number')) {
            Schema::table('salons', function (Blueprint $table) {
                $table->dropColumn('phone_number'); // Dropping the column if rolled back
            });
        }
    }
};
