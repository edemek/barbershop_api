<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $otp = rand(1000, 9999);  // Générer un OTP à 4 chiffres

        // Envoi via Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($sid, $token);

        $message = $twilio->messages->create(
            $phoneNumber, // Numéro de téléphone de l'utilisateur
            [
                'from' => $from,
                'body' => "Votre code de vérification est : $otp"
            ]
        );

        return response()->json(['status' => 'OTP envoyé']);
    }
}


