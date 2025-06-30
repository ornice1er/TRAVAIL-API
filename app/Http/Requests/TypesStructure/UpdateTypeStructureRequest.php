<?php

namespace App\Http\Requests\TypesStructure;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTypesStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'title' => 'sometimes|required|string|max:191',
            'is_parent' => 'sometimes|required|boolean',
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
            'is_parent.required' => 'Le champ is_parent est requis.',
            'is_parent.boolean' => 'Le champ is_parent doit être un booléen.',
        ];
    }

    protected function prepareForValidation() {}
}
