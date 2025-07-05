<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RdvController extends Controller
{
    public function index()
    {
        try {
            $rdvs = RendezVous::all();
            return response()->json($rdvs);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des rendez-vous : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des rendez-vous.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $rdv = RendezVous::findOrFail($id);
            return response()->json($rdv);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Rendez-vous introuvable.'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_patient' => 'required|string|exists:users,_id',
                'id_medecin' => 'required|string',
                'date_heure' => 'required|date',
                'type_consultation' => 'required|in:Présentiel,Téléconsultation',
                'motif' => 'required|string',
            ],  [ // ← ici les messages personnalisés
                'id_patient.required' => 'Le champ patient est obligatoire.',
                'id_patient.exists' => 'Le patient sélectionné est introuvable.',
                'id_medecin.required' => 'Le champ médecin est requis.',
                'date_heure.required' => 'La date et l\'heure sont obligatoires.',
                'date_heure.date' => 'Le format de la date/heure est invalide.',
                'type_consultation.required' => 'Le type de consultation est requis.',
                'type_consultation.in' => 'Le type de consultation doit être Présentiel ou Téléconsultation.',
                'motif.required' => 'Le motif de la consultation est requis.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $rdv = RendezVous::create([
                'id_patient' => $request->id_patient,
                'id_medecin' => $request->id_medecin,
                'date_heure' => $request->date_heure,
                'type_consultation' => $request->type_consultation,
                'statut' => 'EnAttente',
                'motif' => $request->motif,
                'est_payé' => false,
            ]);

            return response()->json(['message' => 'Rendez-vous créé avec succès.', 'rdv' => $rdv], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création du rdv : ' . $e->getMessage()], 500);
        }
    }

    public function modifierDateHeure(Request $request, RendezVous $id)
    {
        try {

            $validator = Validator::make($request->all(),[
                'nouvelle_date' => 'required|date',
            ], [
                'nouvelle_date.required' => 'Le champ date est obligatoire.',
                'nouvelle_date.date' => 'Le champ date doit être de type date.',

            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Champ(s) invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $id->date_heure = $request->nouvelle_date;
            $id->save();

            return response()->json(['message' => 'Date et heure mises à jour.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la modification : ' . $e->getMessage()], 500);
        }
    }

    public function reporter(Request $request, string $id)
    {
        try {

           $validator = Validator::make($request->all(),[
                'nouvelle_date' => 'required|date',
            ], [
                'nouvelle_date.required' => 'Le champ date est obligatoire.',
                'nouvelle_date.date' => 'Le champ date doit être de type date.',

            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Champ(s) invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $id = RendezVous::findOrFail($id);
            $id->date_heure = $request->nouvelle_date;
            $id->statut = 'Reporté';
            $id->save();

            return response()->json(['message' => 'Rendez-vous reporté avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du report : ' . $e->getMessage()], 500);
        }
    }

    public function annuler(string $id)
    {
        try {
            dd($id);
            $rdv = RendezVous::findOrFail($id); // Récupération manuelle
             // Pour déboguer, supprimer cette ligne en production
            $rdv->statut = 'Annulé';
            $rdv->save();

            return response()->json(['message' => 'Rendez-vous annulé.', 'rendezVous' => $rdv]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l’annulation : ' . $e->getMessage()], 500);
        }
    }

}
