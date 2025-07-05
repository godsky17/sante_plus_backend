<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RegisterPatientRequest;
use App\Http\Requests\RegisterMedecinRequest;
use App\Http\Requests\RegisterMedecinIndeRequest;
use App\Http\Requests\RegisterHopital;
use App\Models\User;
use App\Models\Hopital;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\UserRegistered;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Inscription + génération token Sanctum
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => null,
                'api_token' => hash('sha256', Str::random(60))
            ]);

            $user->save();


            return response()->json([
                'status' => true,
                'message' => 'Inscription réussie.',
                'data' => [
                    'user' => [
                        'id' => $user->_id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'api_token' => $user->api_token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur inscription utilisateur : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    public function registerPatient(RegisterPatientRequest $request)
    {
        $data = $request->validated();

        try {
            $patient = new User([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' =>  Hash::make($data['mot_de_passe']),
                'contact' => $data['contact'],
                'adresse' => $data['adresse'],
                'genre' => $data['genre'],
                'photo_profil' => $data['photo_profil'],
                'date_naissance' => $data['date_naissance'],
                'type_utilisateur' => $data['type_utilisateur'],
                'statut' => $data['statut'],
                'preferences_notification' => $data['preferences_notification'],
                'api_token' => hash('sha256', Str::random(60)),
            ]);

            $patient->save();
            $patient->notify(new UserRegistered($patient));
            $patient->sendEmailVerificationNotification();

            return response()->json([
                'status' => true,
                'message' => 'Inscription réussie.',
                'data' => [
                    'user' => [
                        'id' => $patient->_id,
                        'nom' => $patient->nom,
                        'prenom' => $patient->prenom,
                        'photo_profil' => $patient->photo_profil,
                        'email' => $patient->email,
                    ],
                    'api_token' => $patient->api_token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur inscription utilisateur : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    public function registerMedecinAffilie(RegisterMedecinRequest $request)
    {
        $data = $request->validated();

        try {
            $medecin = new User([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' =>  Hash::make($data['mot_de_passe']),
                'contact' => $data['contact'],
                'adresse' => $data['adresse'],
                'genre' => $data['genre'],
                'photo_profil' => $data['photo_profil'],
                'date_naissance' => $data['date_naissance'],
                'type_utilisateur' => $data['type_utilisateur'],
                'statut' => $data['statut'],
                'preferences_notification' => $data['preferences_notification'],
                // spécification
                'est_independant' => false,
                'specialites' => $data['specialites'],
                'id_hopital' => $data['id_hopital'],
                'documents_justificatifs' => $data['documents_justificatifs'],
                'planning_id' => $data['planning_id'] ?? null,
                'api_token' => hash('sha256', Str::random(60)),
            ]);

            $medecin->save();
            $medecin->notify(new UserRegistered($medecin));
            $medecin->sendEmailVerificationNotification();

            return response()->json([
                'status' => true,
                'message' => 'Inscription réussie.',
                'data' => [
                    'user' => [
                        'id' => $medecin->_id,
                        'nom' => $medecin->nom,
                        'prenom' => $medecin->prenom,
                        'photo_profil' => $medecin->photo_profil,
                        'specialites' => $medecin->specialites,
                        'id_hopital' => $medecin->id_hopital,
                        'email' => $medecin->email,
                    ],
                    'api_token' => $medecin->api_token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur inscription utilisateur : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    public function registerMedecinInde(RegisterMedecinIndeRequest $request)
    {
        $data = $request->validated();

        try {
            $medecin = new User([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' =>  Hash::make($data['mot_de_passe']),
                'contact' => $data['contact'],
                'adresse' => $data['adresse'],
                'genre' => $data['genre'],
                'photo_profil' => $data['photo_profil'],
                'date_naissance' => $data['date_naissance'],
                'type_utilisateur' => $data['type_utilisateur'],
                'statut' => $data['statut'],
                'preferences_notification' => $data['preferences_notification'],
                // spécification
                'est_independant' => true,
                'specialites' => $data['specialites'],
                'coordonnees' => $data['specialites'],
                'documents_justificatifs' => $data['documents_justificatifs'],
                'planning_id' => $data['planning_id'] ?? null,
                'api_token' => hash('sha256', Str::random(60)),
            ]);

            $medecin->save();
            $medecin->notify(new UserRegistered($medecin));
            $medecin->sendEmailVerificationNotification();

            return response()->json([
                'status' => true,
                'message' => 'Inscription réussie.',
                'data' => [
                    'user' => [
                        'id' => $medecin->_id,
                        'nom' => $medecin->nom,
                        'prenom' => $medecin->prenom,
                        'photo_profil' => $medecin->photo_profil,
                        'specialites' => $medecin->specialites,
                        'est_independant' => $medecin->est_independant,
                        'coordonnees' => $medecin->coordonnees,
                        'email' => $medecin->email,
                    ],
                    'api_token' => $medecin->api_token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur inscription utilisateur : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    public function registerHopital(RegisterHopital $request)
    {
        $data = $request->validated();

        try {
            $hopital = new Hopital([
                'nom' => $data['nom_hopital'],
                'services' => $data['services'] ?? [],
                'localisation' => $data['coordonnees'] ?? null,
                'medecins_affilies_ids' => [],
            ]);

            $hopital->save();

            $admin = new User([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' => Hash::make($data['mot_de_passe']),
                'contact' => $data['contact'],
                'adresse' => $data['adresse'] ?? null,
                'genre' => $data['genre'] ?? null,
                'photo_profil' => $data['photo_profil'] ?? null,
                'date_naissance' => $data['date_naissance'] ?? null,
                'type_utilisateur' => $data['type_utilisateur'] ?? 'admin',
                'statut' => $data['statut'] ?? 'actif',
                'preferences_notification' => $data['preferences_notification'] ?? [],
                'est_independant' => $data['est_independant'] ?? false,
                'specialites' => $data['specialites'],
                'documents_justificatifs' => $data['documents_justificatifs'],
                'planning_id' => $data['planning_id'] ?? null,
                'id_hopital' => $hopital->_id,
                'api_token' => hash('sha256', Str::random(60)),
            ]);

            $admin->save();
            $admin->notify(new UserRegistered($admin));
            $admin->sendEmailVerificationNotification();

            return response()->json([
                'status' => true,
                'message' => 'Inscription réussie.',
                'data' => [
                    'hopital' => $hopital,
                    'admin' => $admin,
                    'token_type' => 'Bearer',
                    'token' => $admin->api_token,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur inscription hopital : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    public function login(Request $request)
    {


        // Validation avec Validator pour gérer les erreurs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'mot_de_passe' => 'required|string',
        ]);

        if ($validator->fails()) {
            // Renvoie les erreurs de validation en JSON
            return response()->json([
                'status' => false,
                'message' => 'Données invalides.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Utilisateur non trouvé.',
                ], 404);
            }

            if (!Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mot de passe incorrect.',
                ], 401);
            }

            if (empty($user->email_verified_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email non vérifié. Veuillez vérifier votre boîte mail.',
                ], 403);
            }

            $token = hash('sha256', \Str::random(60));
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Connexion réussie.',
                'token_type' => 'Bearer',
                'api_token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            // Log l'erreur pour debug serveur
            \Log::error('Erreur login : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur interne est survenue, veuillez réessayer plus tard.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Authentifier l'utilisateur via api_token
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token non fourni.',
                ], 401);
            }

            // Trouver l'utilisateur via le token
            $user = \App\Models\User::where('api_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Utilisateur non authentifié.',
                ], 401);
            }

            // Invalider le token (supprimer ou réinitialiser)
            $user->api_token = null;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Déconnexion réussie.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la déconnexion : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la déconnexion.',
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $token = $request->bearerToken();

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'statut' => false,
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'ancien_mot_de_passe' => 'required|string',
            'nouveau_mot_de_passe' => 'required|string|min:8|confirmed'
        ], [
            'ancien_mot_de_passe.required' => 'L\'ancien mot de passe est requis.',
            'nouveau_mot_de_passe.required' => 'Le nouveau mot de passe est requis.',
            'nouveau_mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'nouveau_mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statut' => false,
                'message' => 'Erreurs de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->ancien_mot_de_passe, $user->mot_de_passe)) {
            return response()->json([
                'statut' => false,
                'message' => 'Ancien mot de passe incorrect.'
            ], 403);
        }

        $user->mot_de_passe = Hash::make($request->nouveau_mot_de_passe);
        $user->save();

        return response()->json([
            'statut' => true,
            'message' => 'Mot de passe mis à jour avec succès.'
        ]);
    }
}
