<?php
/*
 * File name: QueryToModel.php
 * Last modified: 2025.01.30 at 14:22:50
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Traits;

use App\Criteria\CriteriaInterface;
use Illuminate\Support\Facades\App;
// use Prettus\Repository\Contracts\CriteriaInterface as PCriteriaInterface ;


trait QueryToModel
{
    /**
     * Applique des filtres à la requête en utilisant des closures.
     *
     * @param callable $callback La fonction de filtrage à appliquer.
     * @return void
     */
    public function pushCriteria($criteria)
    {
        $this->query = $criteria->apply($this->query, $this);
        
    }

    /**
     * Applique des filtres à la requête en utilisant des closures.
     *
     * @param callable $callback La fonction de filtrage à appliquer.
     * @return void
     */
    public function queryCriteria(callable $callback): void
    {
        // Exécute la closure pour appliquer le filtrage
        $this->query = $callback($this->query);
    }

     /**
     * Récupère toutes les réservations avec application des filtres.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = []): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->query->get(); // Retourne les réservations filtrées
        } catch (\Exception $e) {
            // Gère toutes les autres exceptions
            throw new \Exception('Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Trouve un enregistrement par son identifiant sans lever d'exception.
     *
     * @param int|string $id L'identifiant de l'enregistrement à rechercher.
     * @param array $columns Les colonnes à récupérer (par défaut, toutes les colonnes).
     * @return \Illuminate\Database\Eloquent\Model|null Retourne l'enregistrement trouvé ou null si non trouvé.
     */
    public function findWithoutFail($id, $columns = ['*'])
    {
        // Utilise la méthode find() pour récupérer l'enregistrement
        return $this->query->find($id, $columns);
    }

    /**
     * Trouve les enregistrements par une liste d'identifiants.
     *
     * @param string $column Le nom de la colonne à filtrer.
     * @param array $values Un tableau des valeurs à rechercher.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereIn($column, array $values , $columns = [])
    {
        // Utilise la méthode whereIn d'Eloquent pour récupérer les enregistrements
        return $this->query->whereIn($column, $values)->get();
    }

    /**
     * Delete  by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id): bool
    {
        $finded = $this->query->find($id);
        return $finded ? $finded->delete() : false;
    }


    /**
     * Crée un nouvel enregistrement dans la base de données.
     *
     * @param array $data Les données à insérer dans la table.
     * @return \App\Models\Booking Le modèle créé avec tous les attributs.
     */
    public function create(array $data)
    {
        // Utilise la méthode create d'Eloquent pour insérer les données
        return $this->query->create($data);
    }


    /**
     * Met à jour un enregistrement de réservation.
     *
     * @param array $data Les données à mettre à jour.
     * @param int $id L'identifiant de la réservation à mettre à jour.
     * @return \App\Models\Booking Le modèle mis à jour.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la réservation n'est pas trouvée.
     */
    public function update(array $data, $id)
    {
        // Trouver la réservation par ID
        $booking = $this->query->findOrFail($id); // Cela lèvera une exception si non trouvé

        // Mettre à jour les informations de la réservation
        $booking->update($data);

        // Retourner le modèle mis à jour
        return $booking;
    }

    


}