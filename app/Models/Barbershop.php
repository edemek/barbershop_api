<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barbershop extends Model
{
    // Utilisation du trait HasFactory pour permettre la création d'instances via les usines de modèles.
    use HasFactory;

    // Propriétés qui peuvent être massivement assignées (c'est-à-dire, qui peuvent être définies via des tableaux).
    protected $fillable = [
        'title',      // Titre du salon de coiffure
        'description',// Description du salon
        'completed',  // Statut du salon (ex : ouvert, fermé)
        'user_id',    // Identifiant de l'utilisateur (propriétaire du salon)
    ];

    // Cast les attributs pour les convertir dans un type spécifique. Par exemple, 'completed' devient un booléen.
    protected $casts = [
        'completed' => 'boolean',  // Le champ 'completed' est traité comme un booléen.
    ];

    /**
     * Définition de la relation entre le modèle Barbershop et le modèle User.
     * Chaque salon de coiffure appartient à un utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        // Cette fonction définit la relation inverse "belongsTo", indiquant qu'un salon de coiffure
        // appartient à un utilisateur. La clé étrangère est 'user_id'.
        return $this->belongsTo(User::class);
    }
}
