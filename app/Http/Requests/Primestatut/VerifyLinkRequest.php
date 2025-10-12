<?php

namespace App\Http\Requests\Primestatut;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'link_token' => 'nullable|string|size:40',
            'media_token' => 'nullable|string|size:40'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'link_token.string' => 'Le token de lien doit être une chaîne de caractères.',
            'link_token.size' => 'Le token de lien doit faire exactement 40 caractères.',
            'media_token.string' => 'Le token média doit être une chaîne de caractères.',
            'media_token.size' => 'Le token média doit faire exactement 40 caractères.'
        ];
    }

    protected function prepareForValidation() 
    {
        // S'assurer qu'au moins un token est fourni
        if (!$this->filled('link_token') && !$this->filled('media_token')) {
            $this->merge([
                'link_token' => $this->input('link_token', ''),
                'media_token' => $this->input('media_token', '')
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->filled('link_token') && !$this->filled('media_token')) {
                $validator->errors()->add('tokens', 'Au moins un token (link_token ou media_token) doit être fourni.');
            }
        });
    }
}