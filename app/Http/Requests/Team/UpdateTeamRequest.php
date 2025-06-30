<?php

namespace App\Http\Requests\Team;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'type' => 'sometimes|required|in:adjoint,assistant,autre,',
            'name' => 'sometimes|required|string|max:191',
            'office' => 'sometimes|required|string|max:191',
            'photo' => 'sometimes|required|string|max:191',
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
           'type.required' => 'Le type est requis.',
            'type.in' => 'Le type doit être adjoint, assistant, autre ou vide.',
            'name.required' => 'Le nom est requis.',
            'office.required' => 'Le bureau est requis.',
            'photo.required' => 'La photo est requise.',
            'structure_id.exists' => 'La structure spécifiée est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
