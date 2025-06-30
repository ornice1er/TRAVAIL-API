<?php

namespace App\Http\Requests\Doc;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'name' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|required|string|max:191|unique:docs,slug,' . $this->route('doc'),
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
            'type' => 'sometimes|required|string|max:255',
            'filename' => 'nullable|string|max:255',
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
            'name.required' => 'Le nom est requis.',
            'slug.required' => 'Le slug est requis.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            'type.required' => 'Le type est requis.',
            'media_id.required' => 'Le média associé est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
