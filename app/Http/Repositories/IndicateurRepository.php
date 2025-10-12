<?php

namespace App\Http\Repositories;

use App\Models\Indicateur;
use App\Traits\Repository;

class IndicateurRepository
{
    use Repository;

    /**
     * The model being queried.
     *
     * @var Indicateur
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = app(Indicateur::class);
    }

    /**
     * Check if indicateur exists
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Get all indicateurs with filtering, pagination, and sorting
     */
    public function getAll($request)
    {
        $per_page = 10;

        $req = Indicateur::ignoreRequest(['per_page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->with('structure')
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }

    /**
     * Get a specific indicateur by id
     */
    public function get($id)
    {
        try {
            return $this->with('structure')->findOrFail($id);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return null;
        }
    }

    /**
     * Store a new indicateur
     */
    public function makeStore($data)
    {
        $model = Indicateur::create($data);
        return $model;
    }

    /**
     * Update an existing indicateur
     */
    public function makeUpdate($id, $data)
    {
        $model = Indicateur::findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * Delete an indicateur
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Set status for indicateur
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['is_active' => $status]);
    }

    /**
     * Search for indicateurs by libelle or valeur
     */
    public function search($term)
    {
        $query = Indicateur::query();
        $attrs = ['libelle', 'valeur'];

        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->with('structure')->get();
    }
}