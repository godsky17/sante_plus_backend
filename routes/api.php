<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HopitalController;
use App\Http\Controllers\API\MedecinController;
use App\Http\Controllers\API\PatientController;
use App\Models\User;
use App\Http\Controllers\API\RdvController;
use App\Http\Controllers\API\PlanningController;
use App\Http\Controllers\Api\OrdonnanceController;
use App\Http\Controllers\Api\AnalyseController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\DossierMedicalController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// CONNEXION
Route::post('/login', [AuthController::class, 'login']);

// INSCRIPTION
Route::post('/register-patient', [AuthController::class, 'registerPatient']);
Route::post('/register-hopital', [AuthController::class, 'registerHopital']);
Route::post('/register-medecin-affilie', [AuthController::class, 'registerMedecinAffilie']);
Route::post('/register-medecin-independant', [AuthController::class, 'registerMedecinInde']);

// verification mail
Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = User::find($id);
    if (! $user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    // Vérifier la signature du lien
    if (! URL::hasValidSignature($request)) {
        return response()->json(['message' => 'Lien invalide ou expiré.'], 403);
    }

    // Vérifier que le hash correspond à l'email de l'utilisateur
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Hash invalide.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email déjà vérifié.']);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email vérifié avec succès.']);
})->name('verification.verify')->middleware(['signed']);

// DECONNECTION
Route::post('/logout', [AuthController::class, 'logout']);

// MODIFICATION MOT DE PASSE
Route::post('/update-password', [AuthController::class, 'updatePassword']);

Route::prefix('rdv')->group(function () {
    Route::post('/create', [RdvController::class, 'store']);        // Création d’un rendez-vous
    Route::get('/', [RdvController::class, 'index']);                // Liste des rendez-vous
    Route::get('/{id}', [RdvController::class, 'show']);            // Détail d’un rendez-vous
    Route::put('/{id}/modifier', [RdvController::class, 'modifierDateHeure']); // Modifier la date/heure
    Route::put('/{id}/reporter', [RdvController::class, 'reporter']); // Reporter à une nouvelle date
    Route::put('/{id}/annuler', [RdvController::class, 'annuler']);   // Annuler le rendez-vous
});

Route::prefix('planning')->group(function () {
    Route::post('/create', [PlanningController::class, 'store']);
    Route::post('{planningId}/ajouter-creneau', [PlanningController::class, 'ajouterCreneau']);
    Route::post('{planningId}/retirer-creneau', [PlanningController::class, 'retirerCreneau']);
    Route::get('{planningId}/verifier-disponibilite', [PlanningController::class, 'verifierDisponibilite']);
});

Route::prefix('patient')->group(function () {
    Route::get('/list', [PatientController::class, 'list']);
    Route::get('{patient}/show', [PatientController::class, 'show']);
    Route::put('{patient}/update', [PatientController::class, 'update']);
    Route::delete('/delete', [PatientController::class, 'deleteAccount'])->middleware('auth.token');
});

Route::prefix('medecin')->group(function () {
    Route::get('/list', [MedecinController::class, 'list']);
    Route::get('{medecin}/show', [MedecinController::class, 'show']);
    Route::put('{medecin}/update', [MedecinController::class, 'update']);
    Route::delete('/delete', [MedecinController::class, 'deleteAccount'])->middleware('auth.token');
});

Route::prefix('hopital')->group(function () {
    Route::get('/list', [HopitalController::class, 'list']);
    Route::get('{hopital}/show', [HopitalController::class, 'show']);
    Route::put('{hopital}/update-nom', [HopitalController::class, 'updateNom'])->middleware(['auth.token']);
    Route::put('/ajouter-service', [HopitalController::class, 'ajouterServices'])->middleware(['auth.token']);
    Route::put('/modifier-service', [HopitalController::class, 'modifierService'])->middleware(['auth.token']);
    Route::delete('/supprimer-service', [HopitalController::class, 'supprimerService'])->middleware(['auth.token']);
    Route::delete('/medecins', [HopitalController::class, 'voirMedecinsAffilies'])->middleware(['auth.token']);
    Route::post('/medecin/{id_medecin}/valider', [HopitalController::class, 'validerMedecin'])->middleware(['auth.token']);
    Route::post('/medecin/{id_medecin}/refuser', [HopitalController::class, 'refuserMedecin'])->middleware(['auth.token']);
    Route::patch('/medecins/{id}/suspendre', [HopitalController::class, 'suspendreMedecin']);
    Route::patch('/medecins/{id}/reintegrer', [HopitalController::class, 'reintegrerMedecin']);

    Route::delete('/delete', [HopitalController::class, 'deleteAccount'])->middleware('auth.token');
});


Route::prefix('dossiers')->group(function () {
    Route::get('/', [DossierMedicalController::class, 'index']);
    Route::post('/', [DossierMedicalController::class, 'store']);
    Route::get('/{id}', [DossierMedicalController::class, 'show']);
    Route::put('/{id}', [DossierMedicalController::class, 'update']);
    Route::patch('/{id}', [DossierMedicalController::class, 'update']);
    Route::delete('/{id}', [DossierMedicalController::class, 'destroy']);
});

Route::prefix('notes')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('/', [NoteController::class, 'store']);
    Route::get('/{id}', [NoteController::class, 'show']);
    Route::put('/{id}', [NoteController::class, 'update']);
    Route::delete('/{id}', [NoteController::class, 'destroy']);
});

Route::prefix('analyses')->group(function () {
    Route::get('/', [AnalyseController::class, 'index']);
    Route::post('/', [AnalyseController::class, 'store']);
    Route::get('/{id}', [AnalyseController::class, 'show']);
    Route::put('/{id}', [AnalyseController::class, 'update']);
    Route::delete('/{id}', [AnalyseController::class, 'destroy']);
});

Route::prefix('ordonnances')->group(function () {
    Route::get('/', [OrdonnanceController::class, 'index']);
    Route::post('/', [OrdonnanceController::class, 'store']);
    Route::get('/{id}', [OrdonnanceController::class, 'show']);
    Route::put('/{id}', [OrdonnanceController::class, 'update']);
    Route::delete('/{id}', [OrdonnanceController::class, 'destroy']);
});


Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API fonctionne 🎉',
        'status' => 'success'
    ]);
});
