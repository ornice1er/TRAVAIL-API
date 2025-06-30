<?php

namespace App\Http\Requests\Stage;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'name' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|required|string|max:191|unique:stages,slug,' . $this->route('stage'),
            'resume' => 'nullable|string',
            'delay' => 'nullable|string|max:191',
            'structure' => 'nullable|string|max:191',
            'year' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'status' => 'sometimes|required|in:active,inactive',
            'closing_date' => 'nullable|date',
            'media_id' => 'sometimes|required|exists:media,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
        ];
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
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            'media_id.required' => 'Le média est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',
            'closing_date.date' => 'La date de clôture doit être une date valide.',
            'period_end.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
        ];
    }

    protected function prepareForValidation() {}
}
