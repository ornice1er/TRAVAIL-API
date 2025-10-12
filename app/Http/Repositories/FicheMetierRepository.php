<?php

namespace App\Http\Repositories;

use App\Models\FicheMetier;
use App\Traits\Repository;

class FicheMetierRepository
{
    use Repository;

    /**
     * The model being queried.
     *
     * @var FicheMetier
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = app(FicheMetier::class);
    }

    /**
     * Check if fiche metier exists
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Get all fiche metiers with filtering, pagination, and sorting
     */
    public function getAll($request)
    {
        $per_page = 10;

        $req = FicheMetier::ignoreRequest(['per_page'])
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
     * Get a specific fiche metier by id
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
     * Store a new fiche metier
     */
    public function makeStore($data)
    {
        $model = FicheMetier::create($data);
        return $model;
    }

    /**
     * Update an existing fiche metier
     */
    public function makeUpdate($id, $data)
    {
        $model = FicheMetier::findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * Delete a fiche metier
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Set status for fiche metier
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['is_active' => $status]);
    }

    /**
     * Search for fiche metiers by titre, resume, or description
     */
    public function search($term)
    {
        $query = FicheMetier::query();
        $attrs = ['titre', 'resume', 'description'];

        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->with('structure')->get();
    }
}