<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Notifications\RegistrationStatusNotification;
use Illuminate\Support\Facades\Log;




class AuthController extends Controller
{
    // ...

    /**
     * Méthode pour obtenir la liste de tous les utilisateurs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupération de tous les utilisateurs de la base de données
        $users = User::all();

        // Retourne la liste des utilisateurs
        return response(['users' => $users], 200);
    }

    /**
     * Méthode pour enregistrer un nouvel utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validation de la requête pour vérifier les champs nécessaires
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'regex:/^\+228\d{8}$/', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifiez si le numéro de téléphone ou l'email est fourni
        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json(['error' => 'Le numéro de téléphone ou l\'adresse email est requis.'], 422);
        }

        // Création de l'utilisateur dans la base de données
        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null, // Utiliser le téléphone s'il est fourni
            'email' => $validated['email'] ?? null, // Utiliser l'email s'il est fourni
            'password' => Hash::make($validated['password']),
        ]);

        // Génération d'un token d'authentification pour l'utilisateur créé
        $token = $user->createToken('auth_token')->plainTextToken;

        // Envoi d'une notification de validation ou rejet (vous pouvez ajuster cela selon votre logique)
        // Exemple : Notification pour dire que l'inscription a été reçue
        try {
            $user->notify(new RegistrationStatusNotification('Votre inscription a été reçue avec succès, veillez patienter pour la verification par un administrateur.'));
        } catch (\Exception $e) {
            // Gérer l'erreur (journaliser ou retourner une réponse appropriée)
            Log::error('Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
        }

        // Retourne l'utilisateur et son token
        return response(['user' => $user, 'token' => $token], 201);
    }


    /**
     * Méthode pour connecter un utilisateur existant.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validation de la requête
        $validated = $request->validate([
            'phone' => ['required', 'regex:/^\+228\d{8}$/'],
            'password' => 'required|string|min:8',
        ]);

        // Recherche de l'utilisateur par téléphone
        $user = User::where('phone', $validated['phone'])->first();

        // Vérification du mot de passe
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Génération d'un token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 201);
    }

    /**
     * Méthode pour déconnecter l'utilisateur actuellement authentifié.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Supprimer le token actuel pour déconnecter l'utilisateur
        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'Déconnexion réussie.'], 200);
    }

    /**
     * Méthode pour modifier les informations d'un utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validation de la requête
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^\+228\d{8}$/'],
        ]);

        // Mise à jour des informations de l'utilisateur authentifié
        $user = $request->user();
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        return response(['user' => $user], 200);
    }

    /**
     * Méthode pour supprimer un utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Récupération de l'utilisateur authentifié
        $user = $request->user();

        // Suppression de l'utilisateur
        $user->delete();

        return response(['message' => 'Compte utilisateur supprimé avec succès.'], 200);
    }
}
