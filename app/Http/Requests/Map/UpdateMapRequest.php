<?php

namespace App\Http\Requests\Map;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => 'sometimes|required|string|max:191',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'site_name.required' => 'Le nom du site est requis.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'latitude.numeric' => 'La latitude doit être un nombre.',
        ];
    }

    protected function prepareForValidation() {}
}
