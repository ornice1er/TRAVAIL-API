<?php

namespace App\Http\Repositories;

use App\Models\Poster;
use App\Traits\Repository;
use App\Utilities\FileStorage;
use Illuminate\Support\Str;

class PosterRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Poster
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Poster::class);
    }

    /**
     * Récupère tous les posters.
     */
    public function all()
    {
        return Poster::orderBy('id', 'DESC')->paginate(10);
    }

    /**
     * Récupère un poster par ID.
     */
    public function findById($id)
    {
        return Poster::find($id);
    }

    /**
     * Crée un nouveau poster.
     */
    public function create($data)
    {
        $slug = Str::slug($data['title']);
        
        // Gestion du fichier image
        if (isset($data['image']) && $data['image']) {
            $data['photo'] = FileStorage::setFile('public', $data['image'], 'posters', $slug);
            unset($data['image']); // Supprimer la clé image car on utilise photo
        }

        return Poster::create($data);
    }

    /**
     * Met à jour un poster.
     */
    public function update($id, $data)
    {
        $poster = Poster::findOrFail($id);
        
        // Gestion du fichier image si présent
        if (isset($data['image']) && $data['image']) {
            $slug = Str::slug($data['title']);
            $data['photo'] = FileStorage::setFile('public', $data['image'], 'posters', $slug);
            unset($data['image']); // Supprimer la clé image car on utilise photo
        }

        return $poster->fill($data)->save();
    }

    /**
     * Supprime un poster.
     */
    public function delete($id)
    {
        $poster = Poster::findOrFail($id);
        return $poster->delete();
    }

    /**
     * Vérifie si un poster existe.
     */
    public function ifExist($id)
    {
        return Poster::where('id', $id)->exists();
    }

    /**
     * Récupère tous les posters avec pagination.
     */
    public function getAll($request)
    {
        $query = Poster::query();
        
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        return $query->orderBy('id', 'DESC')->paginate(10);
    }

    /**
     * Récupère un poster par ID.
     */
    public function getById($id)
    {
        return Poster::findOrFail($id);
    }

    /**
     * Crée un nouveau poster.
     */
    public function store($data)
    {
        return $this->create($data);
    }

    /**
     * Supprime un poster.
     */
    public function destroy($id)
    {
        return $this->delete($id);
    }

    /**
     * Récupère un poster.
     */
    public function get($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée un poster.
     */
    public function makeStore($data): Poster
    {
        return $this->create($data);
    }

    /**
     * Met à jour un poster.
     */
    public function makeUpdate($id, $data): Poster
    {
        $this->update($id, $data);
        return $this->findById($id);
    }

    /**
     * Supprime un poster.
     */
    public function makeDestroy($id)
    {
        return $this->delete($id);
    }
}