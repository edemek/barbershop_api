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
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('notifiable_id')->after('id'); // Ajoute notifiable_id sans auto_increment
            $table->string('notifiable_type')->after('notifiable_id'); // Ajoute notifiable_type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->dropColumn(['notifiable_id', 'notifiable_type']);
        });
    }
};
