<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'contact' => ['required', 'regex:/^(\+\d{1,3})?\d{8,}$/', 'min:8'],
            'adresse' => 'nullable|string',
            'genre' => 'nullable|string',
            'photo_profil' => 'nullable|string|url',
            'date_naissance' => 'nullable|date',
            'preferences_notification' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',

            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 100 caractères.',

            'contact.required' => 'Le contact est obligatoire.',
            'contact.string' => 'Le contact doit être une chaîne de caractères.',
            'contact.min' => 'Le contact doit contenir au moins 8 caractères.',

            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',

            'genre.string' => 'Le genre doit être une chaîne de caractères.',

            'photo_profil.string' => 'La photo de profil doit être une URL valide.',
            'photo_profil.url' => 'La photo de profil doit être une URL valide.',

            'date_naissance.date' => 'La date de naissance doit être une date valide.',


            'statut.string' => 'Le statut doit être une chaîne de caractères.',

            'preferences_notification.array' => 'Les préférences de notification doivent être un tableau.',

        ];
    }
}
