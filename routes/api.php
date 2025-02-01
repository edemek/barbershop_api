<?php

use App\Http\Controllers\API\AvailabilityHourAPIController;
use App\Http\Controllers\API\EServiceAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Barbershop\BarbershopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Api\BookingAPIController;
use App\Http\Controllers\API\SalonAPIController;
use App\Http\Controllers\API\AuthAPIController;

//------------------- user -------------------
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthAPIController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum', 'admin')->get('/users', [AuthController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::apiResource('barbershops', BarbershopController::class);
});

//------------------- e-services -------------------
Route::get('e_services', [EServiceAPIController::class, 'store']); // Creer un service

//------------------- OTP -------------------
Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('/resend-otp', [OtpController::class, 'resendOtp']);





//------------------- products -------------------
    // Afficher la liste des produits
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Afficher les détails d'un produit spécifique
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Créer un nouveau produit (Formulaire de création)
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    // Enregistrer un nouveau produit
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    // Éditer un produit existant (Formulaire d'édition)
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');

    // Mettre à jour un produit existant
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

    // Supprimer un produit existant
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');



//------------------- shopping-cart -------------------
Route::get('/shopping-cart', [ShoppingCartController::class, 'index']); // Liste des éléments du panier
Route::post('/shopping-cart', [ShoppingCartController::class, 'store']); // Ajouter un élément au panier
Route::get('/shopping-cart/{shoppingCart}', [ShoppingCartController::class, 'show']); // Détails d'un élément du panier
Route::put('/shopping-cart/{shoppingCart}', [ShoppingCartController::class, 'update']); // Mettre à jour un élément du panier
Route::delete('/shopping-cart/{shoppingCart}', [ShoppingCartController::class, 'destroy']); // Supprimer un élément du panier



//------------------- recommendations -------------------
Route::get('/recommendations', [RecommendationController::class, 'index']); // Liste des recommandations
Route::post('/recommendations', [RecommendationController::class, 'store']); // Ajouter une recommandation
Route::get('/recommendations/{recommendation}', [RecommendationController::class, 'show']); // Détails d'une recommandation
Route::delete('/recommendations/{recommendation}', [RecommendationController::class, 'destroy']); // Supprimer une recommandation



//------------------- documents -------------------
Route::get('/documents', [DocumentController::class, 'index']); // Liste des documents
Route::post('/documents', [DocumentController::class, 'store']); // Ajouter un document
Route::get('/documents/{document}', [DocumentController::class, 'show']); // Détails d'un document
Route::delete('/documents/{document}', [DocumentController::class, 'destroy']); // Supprimer un document



//------------------- notifications -------------------
Route::get('/notifications', [NotificationController::class, 'index']); // Liste des notifications
Route::post('/notifications', [NotificationController::class, 'store']); // Ajouter une notification
Route::get('/notifications/{notification}', [NotificationController::class, 'show']); // Détails d'une notification
Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']); // Supprimer une notification



//------------------- reviews -------------------
Route::get('/reviews', [ReviewController::class, 'index']); // Liste des avis
Route::post('/reviews', [ReviewController::class, 'store']); // Ajouter un avis
Route::get('/reviews/{review}', [ReviewController::class, 'show']); // Détails d'un avis
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']); // Supprimer un avis



//------------------- promotions -------------------
Route::get('/promotions', [PromotionController::class, 'index']); // Liste des promotions
Route::post('/promotions', [PromotionController::class, 'store']); // Ajouter une promotion
Route::get('/promotions/{promotion}', [PromotionController::class, 'show']); // Détails d'une promotion
Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy']); // Supprimer une promotion


//------------------- bookings -------------------
Route::resource('bookings', BookingAPIController::class );


//------------------- salons -------------------
Route::resource('salons', SalonAPIController::class );

//------------------- availability_hours (ouverture du salon) -------------------
Route::resource('availability_hours', AvailabilityHourAPIController::class)->only(['index', 'show']);
Route::resource('availability_hours', AvailabilityHourAPIController::class)->only(['store', 'update', 'destroy']);




// Route::get('/salons', [SalonController::class, 'index']); // Liste des avis
// Route::post('/salons', [SalonController::class, 'store']); // Ajouter un avis
// Route::get('/salons/{salon}', [SalonController::class, 'show']); // Détails d'un avis
// Route::delete('/salons/{salon}', [SalonController::class, 'destroy']); // Supprimer un avis




