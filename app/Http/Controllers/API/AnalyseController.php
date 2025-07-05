<?php

namespace App\Http\Controllers\Api;

use App\Models\Analyse;
use App\Models\Ordonnance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AnalyseController extends Controller
{
    public function index()
    {
        try {
            $analyses = Analyse::all();
            return response()->json($analyses);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des analyses : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des analyses'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_dossier_medical' => 'required|string',
                'id_medecin' => 'required|string',
                'id_patient' => 'required|string',
                'date' => 'required|date',
                'contenu' => 'required|string',
                'fichier_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            $ordonnance = new Ordonnance();
            $ordonnance->id_dossier_medical = $request->id_dossier_medical;
            $ordonnance->id_medecin = $request->id_medecin;
            $ordonnance->id_patient = $request->id_patient;
            $ordonnance->date = $request->date;
            $ordonnance->contenu = $request->contenu;

            if ($request->hasFile('fichier_pdf')) {
                $file = $request->file('fichier_pdf');
                $path = $file->store('ordonnances', 'public');
                $ordonnance->fichier_pdf = $path;
            }

            $ordonnance->save();

            return response()->json([
                'message' => 'Ordonnance enregistrée avec succès.',
                'ordonnance' => $ordonnance
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'error' => 'Données invalides',
                'messages' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l’ordonnance : ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la création de l’ordonnance'
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $analyse = Analyse::findOrFail($id);
            return response()->json($analyse);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l’analyse : ' . $e->getMessage());
            return response()->json(['error' => 'Analyse non trouvée'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'contenu' => 'required|string',
                'date' => 'nullable|date',
                'fichier_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // accepte PDF et images
            ]);

            $ordonnance = Ordonnance::findOrFail($id);

            if ($request->filled('contenu')) {
                $ordonnance->contenu = $request->contenu;
            }

            if ($request->filled('date')) {
                $ordonnance->date = $request->date;
            }

            if ($request->hasFile('fichier_pdf')) {
                // Supprimer l'ancien fichier s'il existe
                if ($ordonnance->fichier_pdf && Storage::disk('public')->exists($ordonnance->fichier_pdf)) {
                    Storage::disk('public')->delete($ordonnance->fichier_pdf);
                }

                // Stocker le nouveau fichier
                $file = $request->file('fichier_pdf');
                $path = $file->store('ordonnances', 'public');
                $ordonnance->fichier_pdf = $path;
            }

            $ordonnance->save();

            return response()->json([
                'message' => 'Ordonnance mise à jour avec succès.',
                'ordonnance' => $ordonnance
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'error' => 'Données invalides',
                'messages' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l’ordonnance : ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la mise à jour de l’ordonnance'
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $analyse = Analyse::findOrFail($id);

            if ($analyse->fichier_resultat && Storage::disk('public')->exists($analyse->fichier_resultat)) {
                Storage::disk('public')->delete($analyse->fichier_resultat);
            }

            $analyse->delete();

            return response()->json(['message' => 'Analyse supprimée avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l’analyse : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression de l’analyse'], 500);
        }
    }
}
