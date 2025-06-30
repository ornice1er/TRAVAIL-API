<?php

namespace App\Http\Requests\AppelsOffre;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAppelsOffreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'name' => 'required|string|max:191',
            'photo' => 'nullable|string',
            'legend' => 'nullable|string',
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
            'name.required' => 'Le nom est requis.',
            'media_id.required' => 'Le média associé est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
