<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Notifications\RegistrationStatusNotification;
use Exception;
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
        try {
            // Validation de la requête pour vérifier les champs nécessaires
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => ['nullable', 'regex:/^\+228\d{8}$/', 'unique:users,phone_number'],
                'email' => ['nullable', 'email', 'unique:users,email'],
                'password' => 'required|string|min:8|confirmed',
                //Attribut pour utiliser le lien d'affiliation ou pas
                'ref' => 'nullable|exists:affiliate_links,link',
            ]);
        }catch (ValidationException $e) {
            // Return a custom JSON error response for validation errors
            return $this->sendError($e->validator->errors(),422); // HTTP status code 422 Unprocessable Entity
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        // Vérifiez si le numéro de téléphone ou l'email est fourni
        if (empty($validated['phone_number']) && empty($validated['email'])) {
            return response()->json(['error' => 'Le numéro de téléphone ou l\'adresse email est requis.'], 422);
        }

        $referralCode = $request->input('ref');

        // Trouver le lien d'affiliation
        $affiliateLink = AffiliateLink::where('link', $referralCode)->first();

        // Si le lien d'affiliation n'est pas trouvé, ne pas retourner d'erreur, simplement ignorer
        if ($affiliateLink) {
            $referrerId = $affiliateLink->user_id;

            // Création de l'utilisateur dans la base de données
            $user = User::create([
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'] ?? null, // Utiliser le téléphone s'il est fourni
                'email' => $validated['email'] ?? null, // Utiliser l'email s'il est fourni
                'password' => Hash::make($validated['password']),
                'total_points' => 0, // Initialize total_points
                'points' => 0, //Initialize Points
            ]);

            // Ajouter des points au nouvel utilisateur
            $user->addPoints(500);

            // Trouver le référent (l'utilisateur qui a partagé le lien)
            $referrer = User::find($referrerId);

            // Créer une commission pour le référent
            Commission::create([
                'user_id' => $referrerId,
                'amount' => 100,
            ]);

            // Vérifier le nombre de commissions existantes pour l'utilisateur référent
            $commissionCount = Commission::where('user_id', $referrerId)->count();

            if ($commissionCount < 10 && $referrer) {
                // Ajoute 100 comme commission si moins de 10 commissions existantes
                $referrer->addCommission(100);
            }

            // Génération d'un token d'authentification pour l'utilisateur créé
            $token = $user->createToken('auth_token')->plainTextToken;

            // Exemple : Notification pour dire que l'inscription a été reçue
            try {
                $user->notify(new RegistrationStatusNotification('Votre inscription a été effectuée avec succès.'));
            } catch (\Exception $e) {
                // Gérer l'erreur (journaliser ou retourner une réponse appropriée)
                Log::error('Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
            }

            // Retourne l'utilisateur et son token
            return response(['user' => $user, 'token' => $token], 201);


        } else {
            Log::warning('Lien d\'affiliation invalide fourni : ' . $referralCode);
        }

        // Création de l'utilisateur dans la base de données
        $user = User::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? null, // Utiliser le téléphone s'il est fourni
            'email' => $validated['email'] ?? null, // Utiliser l'email s'il est fourni
            'password' => Hash::make($validated['password']),
        ]);

        // Génération d'un token d'authentification pour l'utilisateur créé
        $token = $user->createToken('auth_token')->plainTextToken;

        // Envoi d'une notification de validation ou rejet (vous pouvez ajuster cela selon votre logique)
        // Exemple : Notification pour dire que l'inscription a été reçue
        try {
            $user->notify(new RegistrationStatusNotification('Votre inscription a été effectuée avec succès.'));
        } catch (\Exception $e) {
            // Gérer l'erreur (journaliser ou retourner une réponse appropriée)
            Log::error('Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
        }

        // Retourne l'utilisateur et son token
        return response(['user' => $user, 'token' => $token], 201);
    }


    public function sendOtp(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $otp = rand(1000, 9999);  // Générer un OTP à 4 chiffres

        // Envoi via Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new User($sid, $token);

        $message = $twilio->messages->create(
            $phoneNumber, // Numéro de téléphone de l'utilisateur
            [
                'from' => $from,
                'body' => "Votre code de vérification est : $otp"
            ]
        );

        return response()->json(['status' => 'OTP envoyé']);
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
            'phone_number' => ['required', 'regex:/^\+228\d{8}$/'],
            'password' => 'required|string|min:8',
        ]);

        // Recherche de l'utilisateur par téléphone
        $user = User::where('phone_number', $validated['phone_number'])->first();

        // Vérification du mot de passe
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone_number' => ['Les identifiants fournis sont incorrects.'],
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
            'phone_number' => ['required', 'regex:/^\+228\d{8}$/'],
        ]);

        // Mise à jour des informations de l'utilisateur authentifié
        $user = $request->user();
        $user->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
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
