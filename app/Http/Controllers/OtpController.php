<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    // Normaliser le numéro de téléphone
    private function normalizePhoneNumber($phoneNumber)
    {
        // Ajouter le "+" si absent et s'assurer que le numéro est au format attendu
        if (!str_starts_with($phoneNumber, '+')) {
            $phoneNumber = '+' . $phoneNumber;
        }
        return $phoneNumber;
    }

    // Envoyer un OTP
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
        ]);

        if ($validator->fails()) {
            Log::error('Validation échouée pour l\'envoi d\'OTP', ['errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Normaliser le numéro de téléphone
        $phoneNumber = $this->normalizePhoneNumber($request->phone_number);

        $otp = rand(1000, 9999); // Générer un OTP aléatoire

        // Enregistrer ou mettre à jour l'OTP
        OTP::updateOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(),
                'used' => false,
            ]
        );

        Log::info('OTP généré et enregistré', ['phone_number' => $phoneNumber, 'otp' => $otp]);

        // Simuler l'envoi de l'OTP (dans un vrai système, utiliser un service SMS)
        return response()->json([
            'message' => 'OTP envoyé avec succès.',
            'otp' => $otp, // Afficher l'OTP pour les tests
        ]);
    }

    // Vérifier un OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            Log::error('Validation échouée pour la vérification d\'OTP', ['errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Normaliser le numéro de téléphone
        $phoneNumber = $this->normalizePhoneNumber($request->phone_number);

        // Rechercher le numéro de téléphone dans la base de données
        $otpRecord = OTP::where('phone_number', $phoneNumber)->first();

        if (!$otpRecord) {
            Log::error('Numéro de téléphone introuvable', ['phone_number' => $phoneNumber]);
            return response()->json(['error' => 'Numéro de téléphone introuvable.'], 404);
        }

        if ($otpRecord->otp !== $request->otp) {
            Log::warning('OTP incorrect', [
                'phone_number' => $phoneNumber,
                'provided_otp' => $request->otp,
                'expected_otp' => $otpRecord->otp,
            ]);
            return response()->json(['error' => 'OTP incorrect.'], 400);
        }

        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            Log::warning('OTP expiré', [
                'phone_number' => $phoneNumber,
                'expires_at' => $otpRecord->expires_at,
                'current_time' => Carbon::now(),
            ]);
            return response()->json(['error' => 'OTP expiré.'], 400);
        }

        // Marquer l'OTP comme utilisé
        $otpRecord->update(['used' => true]);

        Log::info('OTP vérifié avec succès', ['phone_number' => $phoneNumber]);
        return response()->json(['message' => 'OTP vérifié avec succès.'], 200);
    }

    // Renvoyer un OTP
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
        ]);

        if ($validator->fails()) {
            Log::error('Validation échouée pour le renvoi d\'OTP', ['errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()], 400);
        }

        return $this->sendOtp($request);
    }
}
