<?php

namespace App\Http\Requests\Actualite;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreActualiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => 'required|string|max:255|unique:actualites,slug',
            'title' => 'required|string',
            'sub_description' => 'required|string',
            'description' => 'nullable|string',
            'author' => 'required|string',
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'big_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link' => 'nullable|url|max:255',
            'media_id' => 'required|exists:media,id',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
             'slug.required' => 'Le slug est obligatoire.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'title.required' => 'Le titre est obligatoire.',
            'sub_description.required' => 'La sous-description est obligatoire.',
            'author.required' => "L'auteur est obligatoire.",
            'photo.required' => 'La photo principale est obligatoire.',
            'photo.image' => 'La photo doit être une image valide.',
            'photo.mimes' => 'La photo doit être au format JPG, JPEG, PNG ou WEBP.',
            'photo.max' => 'La taille maximale de la photo est 2 Mo.',
            'big_photo.image' => 'La grande photo doit être une image valide.',
            'big_photo.mimes' => 'La grande photo doit être au format JPG, JPEG, PNG ou WEBP.',
            'big_photo.max' => 'La taille maximale de la grande photo est 4 Mo.',
            'link.url' => 'Le lien doit être une URL valide.',
            'media_id.required' => 'Le média est obligatoire.',
            'media_id.exists' => 'Le média sélectionné est invalide.',
            'category_id.required' => 'La catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée est invalide.'
        ];
    }

    protected function prepareForValidation() {}
}
