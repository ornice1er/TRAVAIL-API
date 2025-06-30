<?php

namespace App\Http\Requests\Role;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'name' => 'sometimes|required|string|max:125|unique:roles,name,' . $this->route('role'),
            'guard_name' => 'sometimes|required|string|max:125',    
            ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
           'name.required' => 'Le nom du rôle est requis.',
            'name.unique' => 'Ce rôle existe déjà.',
            'guard_name.required' => 'Le nom du garde est requis.',
        ];
    }

    protected function prepareForValidation() {}
}
