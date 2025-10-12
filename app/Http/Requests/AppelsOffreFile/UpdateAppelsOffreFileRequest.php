<?php

namespace App\Http\Requests\AppelsOffreFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAppelsOffreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:191',
            'file_path' => 'sometimes|nullable|string|max:255',
            'file_size' => 'sometimes|nullable|integer',
            'file_type' => 'sometimes|nullable|string|max:50',
            'appels_offre_id' => 'sometimes|required|exists:appels_offres,id',
            'status' => 'sometimes|in:active,inactive',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du fichier est requis.',
            'name.max' => 'Le nom du fichier ne doit pas dépasser 191 caractères.',
            'file_path.max' => 'Le chemin du fichier ne doit pas dépasser 255 caractères.',
            'file_size.integer' => 'La taille du fichier doit être un nombre entier.',
            'file_type.max' => 'Le type de fichier ne doit pas dépasser 50 caractères.',
            'appels_offre_id.required' => 'L\'identifiant de l\'appel d\'offre est requis.',
            'appels_offre_id.exists' => 'L\'appel d\'offre sélectionné est introuvable.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
        ];
    }
}