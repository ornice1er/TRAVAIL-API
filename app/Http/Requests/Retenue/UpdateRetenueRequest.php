<?php

namespace App\Http\Requests\Retenue;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRetenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'sometimes|required|string|max:50',
            'libelle' => 'sometimes|required|string|max:255',
            'observation' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Le nom est requis.',
            'libelle.required' => 'Le lieu est requis.',
            'observation.string' => 'L\'observation est requis.',
        ];
    }

    protected function prepareForValidation() {}
}
