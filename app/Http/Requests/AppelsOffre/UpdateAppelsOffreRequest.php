<?php

namespace App\Http\Requests\AppelsOffre;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAppelsOffreRequest extends FormRequest
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
            'label.required' => 'Le libellé est requis.',
            'label.max' => 'Le libellé ne doit pas dépasser 191 caractères.',
            'slug.max' => 'Le slug ne doit pas dépasser 191 caractères.',
            'date_emission.required' => 'La date d\'émission est obligatoire.',
            'date_emission.date' => 'La date d\'émission doit être une date valide.',
            'date_cloture.required' => 'La date de clôture est obligatoire.',
            'date_cloture.date' => 'La date de clôture doit être une date valide.',
            'date_cloture.after_or_equal' => 'La date de clôture doit être égale ou postérieure à la date d\'émission.',
            'representant_offre.required' => 'Le représentant de l\'offre est requis.',
            'representant_offre.max' => 'Le nom du représentant ne doit pas dépasser 191 caractères.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            'media_id.required' => 'L’identifiant media est requis.',
            'media_id.exists' => 'Le media sélectionné est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
