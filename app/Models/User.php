<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Utilisation des traits HasFactory, Notifiable et HasApiTokens
    // - HasFactory permet de créer des usines de modèles pour peupler la base de données avec des instances.
    // - Notifiable permet d'envoyer des notifications à l'utilisateur.
    // - HasApiTokens permet de générer des tokens d'authentification pour l'API (par exemple avec Sanctum).
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Les attributs qui peuvent être massivement assignés.
     * Ces champs peuvent être définis lors de la création ou de la mise à jour d'un utilisateur via un tableau.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',      // Nom de l'utilisateur
        'phone',     // Email de l'utilisateur
        'password',  // Mot de passe de l'utilisateur
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation (par exemple, lors de l'envoi de la réponse JSON).
     * Ici, le mot de passe et le token de rappel sont cachés pour des raisons de sécurité.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',        // Le mot de passe ne doit pas être exposé lors de la sérialisation
        'remember_token',  // Le token de rappel (utilisé pour se souvenir de l'utilisateur)
    ];

    /**
     * Les attributs qui doivent être castés dans un type spécifique.
     * Ici, nous convertissons 'email_verified_at' en un objet DateTime et 'password' est marqué comme étant haché.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',  // Conversion de 'email_verified_at' en objet DateTime
            'password' => 'hashed',             // Indication que le mot de passe est haché (ne pas le récupérer en clair)
        ];
    }

    /**
     * Relation "un utilisateur a plusieurs salons de coiffure" (HasMany).
     * Cela définit la relation entre le modèle User et le modèle Barbershop : un utilisateur peut avoir plusieurs salons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function barbershops(): HasMany
    {
        // Retourne la relation HasMany pour récupérer tous les salons de coiffure associés à cet utilisateur
        return $this->hasMany(Barbershop::class);
    }
}
