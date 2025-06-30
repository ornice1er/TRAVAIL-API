<?php

namespace App\Http\Requests\CommuniqueFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommuniqueFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'type' => 'sometimes|required|string|max:191',
            'nom' => 'sometimes|required|string|max:191',
            'reference' => 'sometimes|required|string|max:191',
            'filename' => 'sometimes|required|string',
            'communiques_id' => 'sometimes|required|exists:communiques,id',
             ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
           'type' => 'sometimes|required|string|max:191',
            'nom' => 'sometimes|required|string|max:191',
            'reference' => 'sometimes|required|string|max:191',
            'filename' => 'sometimes|required|string',
            'communiques_id' => 'sometimes|required|exists:communiques,id',
        ];
    }

    protected function prepareForValidation() {}
}
