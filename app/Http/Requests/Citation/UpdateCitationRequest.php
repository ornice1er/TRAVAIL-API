<?php

namespace App\Http\Requests\Citation;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'title' => 'sometimes|required|string|max:191',
            'resume' => 'sometimes|required|string',
            'structure_id' => 'sometimes|nullable|exists:structures,id',
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
            'title.max' => 'Le titre ne doit pas dépasser 191 caractères.',
            'resume.required' => 'Le résumé est obligatoire.',
            'structure_id.exists' => 'La structure sélectionnée est introuvable.',        ];
    }

    protected function prepareForValidation() {}
}
