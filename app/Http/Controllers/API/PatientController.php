<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{

    public function list()
    {
        $users = User::where('type_utilisateur', 'patient')->paginate(10);
        return response()->json([
            'status' => false,
            'data' => $users
        ]);
    }

    public function show(User $patient)
    {
        try {
            $user = User::findOrFail($patient->_id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Utilisateur non valide.'
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur trouvé.',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur recherche patient : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la recherche',
            ], 500);
        }
    }

    public function update(UpdatePatientRequest $request, User $patient)
    {
        $data = $request->validated();

        try {

            $patient->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Modification réussie.',
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
            Log::error('Erreur modification utilisateur : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la modification.',
            ], 500);
        }
    }

    public function deleteAccount()
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Utilisateur non authentifié.',
                ], 401);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Compte supprimé avec succès.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur suppression compte : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la suppression du compte.',
            ], 500);
        }
    }
}
