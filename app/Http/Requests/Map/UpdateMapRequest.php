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
            'longitude' => 'nullable|string|max:191',
            'latitude' => 'sometimes|required|string|max:191',        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'site_name.required' => 'Le nom du site est requis.',
            'latitude.required' => 'La latitude est requise.',
        ];
    }

    protected function prepareForValidation() {}
}
