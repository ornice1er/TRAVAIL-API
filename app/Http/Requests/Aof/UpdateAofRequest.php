<?php

namespace App\Http\Requests\Aof;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
          'mission' => 'sometimes|nullable|string',
          'attribution' => 'sometimes|nullable|string',
          'aof' => 'sometimes|nullable|string',
          'media_id' => 'sometimes|required|exists:media,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'media_id.required' => 'Le média est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
