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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter une nouvelle colonne pour le lien d'affiliation
            $table->string('affiliate_link')->nullable()->after('email');

            // Modifier la colonne points pour qu'elle soit nullable
            $table->integer('points')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la colonne affiliate_link
            $table->dropColumn('affiliate_link');

            // Rendre la colonne points non nullable
            $table->integer('points')->nullable(false)->change();
        });
    }
};
