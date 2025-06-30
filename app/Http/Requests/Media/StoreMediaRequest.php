<?php

namespace App\Http\Requests\Media;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:191',
            'is_published' => 'required|boolean',
            'has_principal_access' => 'required|boolean',
            'is_archived' => 'required|boolean',
            'structure_id' => 'required|exists:structures,id',
            'adding_by' => 'nullable|exists:users,id',
            'motif' => 'nullable|string',
            'type' => 'required|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Le code est requis.',
            'is_published.required' => 'Le statut de publication est requis.',
            'has_principal_access.required' => 'L\'accès principal est requis.',
            'is_archived.required' => 'Le statut d\'archivage est requis.',
            'structure_id.required' => 'La structure associée est requise.',
            'structure_id.exists' => 'La structure spécifiée est introuvable.',
            'adding_by.exists' => 'L\'utilisateur spécifié est introuvable.',
            'type.required' => 'Le type est requis.',
        ];
    }

    protected function prepareForValidation() {}
}
