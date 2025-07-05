<?php

namespace App\Http\Controllers\Api;

use App\Models\Ordonnance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class OrdonnanceController extends Controller
{
    /**
     * Afficher la liste des ordonnances.
     */
    public function index()
    {
        try {
            $ordonnances = Ordonnance::all();
            return response()->json($ordonnances);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des ordonnances : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des ordonnances'], 500);
        }
    }

    /**
     * Créer une nouvelle ordonnance.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'id_dossier_medical' => 'required|string',
                'id_medecin' => 'required|string',
                'id_patient' => 'required|string',
                'date' => 'required|date',
                'contenu' => 'required|string',
                'fichier_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('fichier_pdf')) {
                $file = $request->file('fichier_pdf');
                // Stockage dans storage/app/public/ordonnances
                $path = $file->store('ordonnances', 'public');
                $data['fichier_pdf'] = $path; // Exemple : ordonnances/nomdufichier.pdf
            }

            $ordonnance = Ordonnance::create($data);

            return response()->json($ordonnance, 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['error' => 'Données invalides', 'messages' => $ve->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l’ordonnance : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l’ordonnance'], 500);
        }
    }

    /**
     * Afficher une ordonnance par ID.
     */
    public function show($id)
    {
        try {
            $ordonnance = Ordonnance::findOrFail($id);
            return response()->json($ordonnance);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l’ordonnance : ' . $e->getMessage());
            return response()->json(['error' => 'Ordonnance non trouvée'], 404);
        }
    }

    /**
     * Modifier une ordonnance.
     */
    public function update(Request $request, $id)
    {
        try {
            $ordonnance = Ordonnance::findOrFail($id);

            $data = $request->validate([
                'contenu' => 'sometimes|string',
                'date' => 'sometimes|date',
                'fichier_pdf' => 'nullable|file|mimes:pdf|max:2048',
            ]);

            if ($request->hasFile('fichier_pdf')) {
                // Supprimer l'ancien fichier si existant
                if ($ordonnance->fichier_pdf && Storage::disk('public')->exists($ordonnance->fichier_pdf)) {
                    Storage::disk('public')->delete($ordonnance->fichier_pdf);
                }

                $file = $request->file('fichier_pdf');
                $path = $file->store('ordonnances', 'public');
                $data['fichier_pdf'] = $path;
            }

            $ordonnance->update($data);

            return response()->json($ordonnance);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['error' => 'Données invalides', 'messages' => $ve->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de l’ordonnance : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la modification de l’ordonnance'], 500);
        }
    }

    /**
     * Supprimer une ordonnance.
     */
    public function destroy($id)
    {
        try {
            $ordonnance = Ordonnance::findOrFail($id);

            // Supprimer le fichier PDF associé si existant
            if ($ordonnance->fichier_pdf && Storage::disk('public')->exists($ordonnance->fichier_pdf)) {
                Storage::disk('public')->delete($ordonnance->fichier_pdf);
            }

            $ordonnance->delete();

            return response()->json(['message' => 'Ordonnance supprimée avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l’ordonnance : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression de l’ordonnance'], 500);
        }
    }
}
