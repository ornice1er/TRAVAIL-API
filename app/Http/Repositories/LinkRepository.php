<?php

namespace App\Http\Repositories;

use App\Models\LiensUtile;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
 

class LinkRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var LiensUtile
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(LiensUtile::class);
    }

    /**
     * Vérifie si le lien utile existe.
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère tous les liens utiles avec pagination et filtres.
     */
    public function getAll($request)
    {
        $per_page = 10;

        $req = Link::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            return $req->paginate($request['per_page']);
        } else {
            return $req->get();
        }
    }

    /**
     * Récupère un lien utile spécifique.
     */
    public function get($id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Crée un nouveau lien utile.
     */
    public function makeStore($data): LiensUtile
    {
        // Création du modèle
        $model = LiensUtile::create($data);
        return $model;
    }

    /**
     * Met à jour un lien utile.
     */
    public function makeUpdate($id, $data): LiensUtile
    {
        $model = LiensUtile::findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * Supprime un lien utile.
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Récupère les liens utiles les plus récents.
     */
    public function getlatest()
    {
        return $this->latest()->get();
    }

    /**
     * Modifie le statut d'un lien utile.
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['status' => $status]);
    }

    /**
     * Recherche dans les liens utiles.
     */
    public function search($term)
    {
        $query = LiensUtile::query();
        $attrs = ['title', 'link'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }
}
