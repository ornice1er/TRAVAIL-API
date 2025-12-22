<?php

namespace App\Http\Requests\Communique;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommuniqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'title'=>'string|required',
            'description'=>'string|nullable', 
            'category'=>'string|in:Concours,Activité',
            'has_principal_access' =>'required'
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
        ];}

    protected function prepareForValidation() {}
}
