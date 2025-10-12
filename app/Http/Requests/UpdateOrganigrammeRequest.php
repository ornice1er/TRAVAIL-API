<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganigrammeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'media_id' => 'nullable|exists:media,id',
            'legend' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Le nom de l\'organigramme est obligatoire.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'photo.max' => 'L\'image ne doit pas dépasser 2MB.',
            'media_id.exists' => 'Le média sélectionné n\'existe pas.',
        ];
    }
}