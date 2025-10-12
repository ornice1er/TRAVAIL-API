<?php

namespace App\Http\Requests\Primestatut;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParticipateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Primestatut_id' => 'required|integer|exists:primestatuts,id',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'Primestatut_id.required' => 'L\'ID du primestatut est requis.',
            'Primestatut_id.integer' => 'L\'ID du primestatut doit être un nombre entier.',
            'Primestatut_id.exists' => 'Le primestatut spécifié n\'existe pas.',
            'nom.required' => 'Le nom est requis.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'prenom.required' => 'Le prénom est requis.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            'phone.required' => 'Le numéro de téléphone est requis.',
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'message.string' => 'Le message doit être une chaîne de caractères.',
            'status.integer' => 'Le statut doit être un nombre entier.',
            'status.in' => 'Le statut doit être 0 ou 1.'
        ];
    }

    protected function prepareForValidation() 
    {
        // Définir un statut par défaut si non fourni
        if (!$this->filled('status')) {
            $this->merge(['status' => 1]);
        }
    }
}