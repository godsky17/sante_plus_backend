<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NoteController extends Controller
{
    /**
     * Affiche la liste des notes.
     */
    public function index()
    {
        try {
            $notes = Note::all();
            return response()->json($notes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des notes : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crée une nouvelle note.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_medecin' => 'required|string',
                'contenu' => 'required|string',
                'date' => 'required|date',
                'id_dossier_medical' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $note = Note::create($request->only(['id_medecin', 'contenu', 'date', 'id_dossier_medical']));

            return response()->json(['message' => 'Note créée avec succès.', 'note' => $note], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création de la note : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Affiche une note spécifique.
     */
    public function show($id)
    {
        try {
            $note = Note::findOrFail($id);
            return response()->json($note);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Note non trouvée : ' . $e->getMessage()], 404);
        }
    }

    /**
     * Met à jour une note existante.
     */
    public function update(Request $request, $id)
    {
        try {
            $note = Note::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'id_medecin' => 'sometimes|string',
                'contenu' => 'sometimes|string',
                'date' => 'sometimes|date',
                'id_dossier_medical' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $note->update($request->only(['id_medecin', 'contenu', 'date', 'id_dossier_medical']));

            return response()->json(['message' => 'Note mise à jour avec succès.', 'note' => $note]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprime une note.
     */
    public function destroy($id)
    {
        try {
            $note = Note::findOrFail($id);
            $note->delete();

            return response()->json(['message' => 'Note supprimée avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }
}
