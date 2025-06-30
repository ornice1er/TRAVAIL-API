<?php

namespace App\Http\Requests\Prestation;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePrestationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'label' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|nullable|string|max:191',
            'date_emission' => 'sometimes|required|date',
            'date_cloture' => 'sometimes|required|date|after_or_equal:date_emission',
            'representant_offre' => 'sometimes|required|string|max:191',
            'status' => 'sometimes|in:active,inactive',
            'media_id' => 'sometimes|required|exists:media,id',        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
            'slug.required' => 'Le slug est requis.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'link.required' => 'Le lien est requis.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            'media_id.required' => 'Le média associé est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
