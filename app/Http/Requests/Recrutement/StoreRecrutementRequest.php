<?php

namespace App\Http\Requests\Recrutement;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRecrutementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:recrutements,slug',
            'resume' => 'nullable|string',
            'has_result' => 'required|boolean',
            'status' => 'required|in:active,inactive',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
             'title.required' => 'Le titre est requis.',
            'slug.required' => 'Le slug est requis.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'has_result.required' => 'Le champ "a un résultat" est requis.',
            'has_result.boolean' => 'Le champ "a un résultat" doit être un booléen.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
        ];
    }

    protected function prepareForValidation() {}
}
