<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DossierMedical;
use Illuminate\Http\Request;

class DossierMedicalController extends Controller
{
    // Liste tous les dossiers médicaux
    public function index()
    {
        try {
            $dossiers = DossierMedical::all();
            return response()->json($dossiers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des dossiers : ' . $e->getMessage()], 500);
        }
    }

    // Crée un nouveau dossier médical
    public function store(Request $request)
    {
        try {

            $request->validate([
                'id_patient' => 'required|string|exists:patients,_id', // adapte selon ta clé primaire MongoDB
                'consultable_par_hopital' => 'required|boolean',
            ]);

            $dossier = new DossierMedical();
            $dossier->id_patient = $request->id_patient;
            $dossier->consultable_par_hopital = $request->consultable_par_hopital;
            $dossier->save();

            return response()->json(['message' => 'Dossier médical créé avec succès.', 'dossier' => $dossier], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création du dossier : ' . $e->getMessage()], 500);
        }
    }

    // Affiche un dossier médical spécifique
    public function show($id)
    {
        try {
            $dossier = DossierMedical::findOrFail($id);
            return response()->json($dossier);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Dossier médical non trouvé : ' . $e->getMessage()], 404);
        }
    }

    // Met à jour un dossier médical
    public function update(Request $request, $id)
    {
        $request->validate([
            'consultable_par_hopital' => 'nullable|boolean',
        ]);

        try {
            $dossier = DossierMedical::findOrFail($id);
            if ($request->has('consultable_par_hopital')) {
                $dossier->consultable_par_hopital = $request->consultable_par_hopital;
            }
            $dossier->save();

            return response()->json(['message' => 'Dossier médical mis à jour.', 'dossier' => $dossier]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()], 500);
        }
    }

    // Supprime un dossier médical
    public function destroy($id)
    {
        try {
            $dossier = DossierMedical::findOrFail($id);
            $dossier->delete();

            return response()->json(['message' => 'Dossier médical supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }
}
