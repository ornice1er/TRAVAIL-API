<?php

namespace App\Http\Requests\Poster;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePosterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'title' => 'required|string|max:191',
            'photo' => 'nullable|string|max:191',
            'type' => 'required|string|in:image,video',
            'url' => 'nullable|string',
            'status' => 'required|in:active,inactive',
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
            'type.required' => 'Le type est requis.',
            'type.in' => 'Le type doit être "image" ou "video".',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
        ];
    }

    protected function prepareForValidation() {}
}
