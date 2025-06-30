<?php

namespace App\Http\Requests\Communique;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommuniqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'slug' => 'required|string',
            'media_id' => 'required|exists:media,id',
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
            'description.required' => 'La description est obligatoire.',
            'slug.required' => 'Le slug est requis.',
            'media_id.required' => 'Le média est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',        ];
    }

    protected function prepareForValidation() {}
}
