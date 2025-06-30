<?php

namespace App\Http\Requests\Mot;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'title' => 'required|string|max:191',
            'resume' => 'required|string',
            'structure_id' => 'nullable|exists:structures,id',
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
            'resume.required' => 'Le résumé est requis.',
            'structure_id.exists' => 'La structure spécifiée est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
