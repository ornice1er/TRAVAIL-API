<?php

namespace App\Http\Requests\Legende;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLegendeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:191',
            'organigramme_id' => 'sometimes|required|exists:organigrammes,id',            ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
           'title.required' => 'Le titre est requis.',
            'organigramme_id.required' => "L'organigramme associé est requis.",
            'organigramme_id.exists' => "L'organigramme spécifié est introuvable.",
        ];
    }

    protected function prepareForValidation() {}
}
