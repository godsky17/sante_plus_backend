<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMedecinRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedecinController extends Controller
{
    public function list()
    {
        $users = User::where('type_utilisateur', 'medecin')->paginate(10);
        return response()->json([
            'status' => false,
            'data' => $users
        ]);
    }

    public function show(User $medecin)
    {
        try {
            $user = User::findOrFail($medecin->_id);
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
            Log::error('Erreur recherche medecin : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la recherche',
            ], 500);
        }
    }

    public function update(UpdateMedecinRequest $request, User $medecin)
    {
        $data = $request->validated();

        try {
            $medecin->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Modification réussie.',
                'data' => [
                    'user' => [
                        'id' => $medecin->_id,
                        'nom' => $medecin->nom,
                        'prenom' => $medecin->prenom,
                        'photo_profil' => $medecin->photo_profil,
                        'email' => $medecin->email,
                    ],
                    'api_token' => $medecin->api_token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            Log::error('Erreur modification medecin : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la modification.'
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
