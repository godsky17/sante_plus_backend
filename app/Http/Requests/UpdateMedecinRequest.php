<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedecinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'string|max:100',
            'prenom' => 'string|max:100',
            'contact' => ['regex:/^(\+\d{1,3})?\d{8,}$/', 'min:8'],
            'adresse' => 'nullable|string',
            'genre' => 'nullable|string',
            'photo_profil' => 'nullable|string|url',
            'date_naissance' => 'nullable|date',
            'preferences_notification' => 'nullable|array',

            'specialites' => 'required|string',
            'id_hopital' => 'required|string|exists:hopitals,_id',
            'documents_justificatifs' => 'array',
            'documents_justificatifs.*' => 'string|url',
            'planning_id' => 'nullable|string|exists:plannings,_id',
        ];
    }

    public function messages()
    {
        return [
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 100 caractères.',

            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 100 caractères.',

            'contact.regex' => 'Le contact doit être un numéro valide avec indicatif.',
            'contact.min' => 'Le contact doit contenir au moins 8 chiffres.',

            'photo_profil.url' => 'La photo de profil doit être une URL valide.',

            'id_hopital.exists' => 'L\'hôpital spécifié n\'existe pas.',

            'documents_justificatifs.required' => 'Les documents justificatifs sont obligatoires.',
            'documents_justificatifs.array' => 'Les documents justificatifs doivent être un tableau.',
            'documents_justificatifs.*.string' => 'Chaque document justificatif doit être une chaîne de caractères.',
            'documents_justificatifs.*.url' => 'Chaque document justificatif doit être une URL valide.',

            'planning_id.exists' => 'Le planning spécifié n\'existe pas.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Les données fournies sont invalides.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
