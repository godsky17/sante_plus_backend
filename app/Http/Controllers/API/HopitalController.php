<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hopital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HopitalController extends Controller
{
    public function list()
    {
        $hopitals = Hopital::paginate(10);
        return response()->json([
            'status' => false,
            'data' => $hopitals
        ]);
    }

    public function show(Hopital $hopital)
    {
        try {
            $hopital = Hopital::findOrFail($hopital->_id);
            if (!$hopital) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hopital non valide.'
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => 'Hôpital trouvé.',
                'data' => $hopital
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur recherche hopital : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la recherche',
            ], 500);
        }
    }

    public function updateNom(Request $request)
    {
        //Récupération de l'objet hopital
        $token = $request->bearerToken();

        $hopital_admin = User::where('api_token', $token)->first();

        $hopital = $hopital_admin->hopital;

        // Validation des informations
        $validator = Validator::make($request->all(), [
            'nom' => 'required|min:3|unique:hopitals,nom'
        ], [
            "nom.required" => 'Le champs  nom est obligatoire.',
            "nom.min" => 'Le nom doit contenir au moins 3 caractères..',
            "nom.unique" => 'Ce nom existe déjà.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statut' => false,
                'message' => 'Erreur liées aux champs',
                'errors' => $validator->errors()
            ]);
        }

        // Modification de la donnée
        try {
            $hopital->update($request->all());
            return response()->json([
                'statut' => true,
                'message' => 'Nom modifier avec succès',
                'data' => $hopital
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur  : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lor de la modification du nom.',
            ], 500);
        }
    }

    public function ajouterServices(Request $request)
    {
        //Récupération de l'objet hopital
        $token = $request->bearerToken();

        $hopital_admin = User::where('api_token', $token)->first();
        /**
         * @var Hopital $hopital 
         */
        $hopital = $hopital_admin->hopital;

        // Validation des informations
        $validator = Validator::make($request->all(), [
            'services' => 'required|array'
        ], [
            "services.required" => 'Le champs  services est obligatoire.',
            "services.array" => 'Les services doivent être un tableau.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statut' => false,
                'message' => 'Erreur liées aux champs',
                'errors' => $validator->errors()
            ]);
        }

        // Ajout des services
        try {
            $services_hopital = $hopital->services ?? [];
            foreach ($request->services as $service) {
                if (!in_array($service, $services_hopital)) {
                    $services_hopital[] = $service;
                }
            }
            $hopital->services = $services_hopital;
            $hopital->save();
            return response()->json([
                'statut' => true,
                'message' => 'Service(s) ajouter avec succès',
                'data' => $hopital
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur  : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de l\'ajout de(s) service(s).',
            ], 500);
        }
    }

    public function modifierService(Request $request)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();
        $hopital = $admin->hopital;

        $validator = Validator::make($request->all(), [
            'ancien_service' => 'required|string',
            'nouveau_service' => 'required|string'
        ], [
            'ancien_service.required' => 'L\'ancien service est requis.',
            'nouveau_service.required' => 'Le nouveau service est requis.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statut' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $services = $hopital->services ?? [];
            $index = array_search($request->ancien_service, $services);

            if ($index === false) {
                return response()->json([
                    'statut' => false,
                    'message' => 'Service non trouvé dans la liste.'
                ], 404);
            }

            $services[$index] = $request->nouveau_service;
            $hopital->services = array_values($services);
            $hopital->save();

            return response()->json([
                'statut' => true,
                'message' => 'Service modifié avec succès.',
                'data' => $hopital
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur modification service : " . $e->getMessage());
            return response()->json([
                'statut' => false,
                'message' => 'Erreur lors de la modification du service.'
            ], 500);
        }
    }

    public function supprimerService(Request $request)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();
        $hopital = $admin->hopital;

        $validator = Validator::make($request->all(), [
            'service' => 'required|string'
        ], [
            'service.required' => 'Le nom du service est requis.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statut' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $services = $hopital->services ?? [];

            if (!in_array($request->service, $services)) {
                return response()->json([
                    'statut' => false,
                    'message' => 'Le service à supprimer n\'existe pas.'
                ], 404);
            }

            $hopital->services = array_values(array_filter($services, function ($item) use ($request) {
                return $item !== $request->service;
            }));

            $hopital->save();

            return response()->json([
                'statut' => true,
                'message' => 'Service supprimé avec succès.',
                'data' => $hopital
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur suppression service : " . $e->getMessage());
            return response()->json([
                'statut' => false,
                'message' => 'Erreur lors de la suppression du service.'
            ], 500);
        }
    }

    public function voirMedecinsAffilies(Request $request)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();

        if (!$admin || !$admin->hopital) {
            return response()->json([
                'statut' => false,
                'message' => 'Hôpital introuvable ou accès non autorisé.'
            ], 403);
        }

        $hopital = $admin->hopital;

        $medecins = User::whereIn('_id', $hopital->medecins_affilies_ids)
            ->where('type_utilisateur', 'medecin')
            ->get();

        return response()->json([
            'statut' => true,
            'message' => 'Liste des médecins affiliés récupérée.',
            'data' => $medecins
        ]);
    }

    public function validerMedecin(Request $request, $id_medecin)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();

        if (!$admin || !$admin->hopital) {
            return response()->json([
                'statut' => false,
                'message' => 'Accès non autorisé ou hôpital introuvable.'
            ], 403);
        }

        $medecin = User::where('_id', $id_medecin)
            ->where('id_hopital', $admin->id_hopital)
            ->where('type_utilisateur', 'medecin')
            ->first();

        if (!$medecin) {
            return response()->json([
                'statut' => false,
                'message' => 'Médecin introuvable ou non affilié à votre hôpital.'
            ], 404);
        }

        $medecin->statut = 'valide';
        $medecin->save();

        // Ajouter l'ID du médecin aux médecins affiliés de l'hôpital
        $hopital = $admin->hopital;
        $medecins = $hopital->medecins_affilies_ids ?? [];

        if (!in_array($id_medecin, $medecins)) {
            $medecins[] = $id_medecin;
            $hopital->medecins_affilies_ids = $medecins;
            $hopital->save();
        }

        return response()->json([
            'statut' => true,
            'message' => 'Médecin validé avec succès.',
            'data' => $medecin
        ], 200);
    }

    public function refuserMedecin(Request $request, $id_medecin)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();

        if (!$admin || !$admin->hopital) {
            return response()->json([
                'statut' => false,
                'message' => 'Accès non autorisé ou hôpital introuvable.'
            ], 403);
        }

        // Vérifie si le médecin appartient à cet hôpital et est en attente de validation
        $medecin = User::where('_id', $id_medecin)
            ->where('id_hopital', $admin->id_hopital)
            ->where('type_utilisateur', 'medecin')
            ->where('statut', 'en_attente_validation')
            ->first();

        if (!$medecin) {
            return response()->json([
                'statut' => false,
                'message' => 'Médecin introuvable, déjà validé ou non en attente.'
            ], 404);
        }

        // Changer son statut à "suspendu"
        $medecin->statut = 'suspendu';
        $medecin->save();

        return response()->json([
            'statut' => true,
            'message' => 'Le médecin a été refusé avec succès.',
            'data' => $medecin
        ], 200);
    }

    public function suspendreMedecin(Request $request, $id_medecin)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();

        if (!$admin || !$admin->hopital) {
            return response()->json([
                'statut' => false,
                'message' => 'Accès non autorisé ou hôpital introuvable.'
            ], 403);
        }

        $hopital = $admin->hopital;

        $medecin = User::where('_id', $id_medecin)
            ->where('id_hopital', $admin->id_hopital)
            ->where('type_utilisateur', 'medecin')
            ->where('statut', 'valide')
            ->first();

        if (!$medecin) {
            return response()->json([
                'statut' => false,
                'message' => 'Médecin introuvable ou non valide.'
            ], 404);
        }

        $medecin->statut = 'suspendu';
        $medecin->save();

        // Retirer l’ID du médecin affilié
        $hopital->medecins_affilies_ids = array_values(array_filter(
            $hopital->medecins_affilies_ids ?? [],
            fn($id) => (string) $id !== (string) $medecin->_id
        ));
        $hopital->save();

        return response()->json([
            'statut' => true,
            'message' => 'Médecin suspendu et retiré des affiliés.',
            'data' => $medecin
        ], 200);
    }

    public function reintegrerMedecin(Request $request, $id_medecin)
    {
        $token = $request->bearerToken();
        $admin = User::where('api_token', $token)->first();

        if (!$admin || !$admin->hopital) {
            return response()->json([
                'statut' => false,
                'message' => 'Accès non autorisé ou hôpital introuvable.'
            ], 403);
        }

        $hopital = $admin->hopital;

        $medecin = User::where('_id', $id_medecin)
            ->where('id_hopital', $admin->id_hopital)
            ->where('type_utilisateur', 'medecin')
            ->where('statut', 'suspendu')
            ->first();

        if (!$medecin) {
            return response()->json([
                'statut' => false,
                'message' => 'Médecin introuvable ou non suspendu.'
            ], 404);
        }

        $medecin->statut = 'valide';
        $medecin->save();

        // Ajouter l’ID du médecin si non présent
        $affilies = $hopital->medecins_affilies_ids ?? [];
        if (!in_array((string)$medecin->_id, $affilies)) {
            $affilies[] = (string)$medecin->_id;
            $hopital->medecins_affilies_ids = $affilies;
            $hopital->save();
        }

        return response()->json([
            'statut' => true,
            'message' => 'Médecin réintégré avec succès.',
            'data' => $medecin
        ], 200);
    }
}
