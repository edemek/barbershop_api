<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          // Créer un utilisateur par défaut
          User::create([
            'name'=> 'user_1',
            'phone' => '567456767',
            'password' => Hash::make('password'), // Assurez-vous de hacher le mot de passe
            // 'role'=>'coiffeur';

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
    }
};
