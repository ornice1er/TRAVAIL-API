<?php

namespace App\Http\Requests\Newsletter;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:191',
            'ajouté_par' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'structure_id' => 'required|exists:structures,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
           'titre.required' => 'Le titre est requis.',
            'ajouté_par.exists' => 'L\'utilisateur spécifié est introuvable.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            'structure_id.required' => 'La structure associée est requise.',
            'structure_id.exists' => 'La structure spécifiée est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
