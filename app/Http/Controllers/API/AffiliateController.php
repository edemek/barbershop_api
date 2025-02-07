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
        //$user = auth()->user();
        $user = Auth::user(); // Utilisez auth()->user() au lieu de auth()->Auth::user()
        //dd(auth()->$user());
        // // // // Vérifiez si l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Vérifier si l'utilisateur a déjà un lien d'affiliation
        $existingLink = AffiliateLink::where('user_id', $user->id)->first();

        if ($existingLink) {
            return response()->json(['link' => $existingLink->link]);
        }

        // Générer un code unique basé sur l'ID utilisateur
        $referralCode = 'REF' . $user->id . strtoupper(Str::random(4));

        // Crypter le mot de passe avant de l'assigner au lien
        $encryptedReferralCode = Hash::make($referralCode);

        // Génération du lien d'affiliation
        $link = 'affilate-link?ref=' . $encryptedReferralCode;

        // Enregistrer le lien dans la base de données
        AffiliateLink::create([
            'user_id' => $user->id,
            'link' =>  $link,
        ]);

        return response()->json(['link' => $link]);
    }
}
