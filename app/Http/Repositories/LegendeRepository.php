<?php

namespace App\Http\Repositories;

use App\Models\Legende;
use App\Traits\Repository;

class LegendeRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Legende
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Legende::class);
    }

    /**
     * Vérifie si la légende existe.
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère toutes les légendes, avec pagination optionnelle.
     */
    public function getAll($request)
    {
        $req = Legende::query()->orderByDesc('created_at');

        if ($request->has('pageSize')) {
            return $req->paginate((int) $request->input('pageSize'));
        }

        return $req->get();
    }

    /**
     * Récupère une légende spécifique.
     */
    public function get($id)
    {
        return $this->findOrFail($id);
    }
}
