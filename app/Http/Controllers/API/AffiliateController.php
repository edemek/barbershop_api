<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class AffiliateController extends Controller
{

    // Génération du lien d'affiliation
    public function generateLink(Request $request)
    {
        // // // Correction de l'appel à l'utilisateur authentifié
        // $user = Auth::user(); // Utilisez auth()->user() au lieu de auth()->Auth::user()

        // // // // Vérifiez si l'utilisateur est authentifié
        // if (!$user) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        // Générer un code alphanumérique aléatoire de 6 caractères
        $referralCode = Str::random(6);

        // Crypter le mot de passe avant de l'assigner au lien
        $encryptedReferralCode = Hash::make($referralCode);

        // Génération du lien d'affiliation
        $link = 'affilate-link?ref=' . $encryptedReferralCode; //$user->id;


        // Enregistrer le lien dans la base de données
        AffiliateLink::create([
            'user_id' => 7, // $user->id
            'link' =>  $link,
        ]);

        return response()->json(['link' => $link]);
    }
}
