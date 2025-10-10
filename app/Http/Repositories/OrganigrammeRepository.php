<?php

namespace App\Http\Repositories;

use App\Models\Organigramme;
use App\Models\Media;
use App\Models\User;
use App\Models\Transmission;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class OrganigrammeRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Organigramme
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Organigramme::class);
    }

    /**
     * Récupère tous les organigrammes.
     */
    public function all()
    {
        return Organigramme::with(['media', 'legendes'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère un organigramme par ID.
     */
    public function findById($id)
    {
        return Organigramme::with(['media', 'legendes'])->find($id);
    }

    /**
     * Crée un nouvel organigramme.
     */
    public function create($data)
    {
        // Gestion de l'upload de fichier si présent
        if (request()->hasFile('photo')) {
            // TODO: Implémenter l'upload de fichier
            // $aws = new AwsService();
            // $data['photo'] = $aws->upload(request()->file('photo'), "Organigrammes")['full_url'];
        }
        
        return Organigramme::create($data);
    }

    /**
     * Met à jour un organigramme.
     */
    public function update($id, $data)
    {
        $organigramme = Organigramme::findOrFail($id);
        
        // Gestion de l'upload de fichier si présent
        if (request()->hasFile('photo')) {
            // TODO: Implémenter l'upload de fichier
            // $aws = new AwsService();
            // $data['photo'] = $aws->upload(request()->file('photo'), "Organigrammes")['full_url'];
        }
        
        $organigramme->update($data);
        return $organigramme->fresh();
    }

    /**
     * Supprime un organigramme.
     */
    public function delete($id)
    {
        $organigramme = Organigramme::findOrFail($id);
        return $organigramme->delete();
    }

    /**
     * Recherche dans les organigrammes.
     */
    public function search($query)
    {
        return Organigramme::with(['media', 'legendes'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('legend', 'like', "%{$query}%")
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Vérifie si l'organigramme existe.
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère tous les organigrammes avec pagination et filtres.
     */
    public function getAll($request)
    {
        $per_page = 10;

        $req = Organigramme::ignoreRequest(['per_page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->with(['media', 'legendes'])
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }

    /**
     * Récupère un organigramme spécifique.
     */
    public function getById($id)
    {
        return Organigramme::with(['media', 'legendes'])->findOrFail($id);
    }

    /**
     * Crée un nouvel organigramme (alias pour store).
     */
    public function store($data)
    {
        return $this->create($data);
    }

    /**
     * Supprime un organigramme (alias pour destroy).
     */
    public function destroy($id)
    {
        return $this->delete($id);
    }

    /**
     * Récupère un organigramme.
     */
    public function get($id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Crée un nouvel organigramme.
     */
    public function makeStore($data): Organigramme
    {
        return $this->create($data);
    }

    /**
     * Met à jour un organigramme.
     */
    public function makeUpdate($id, $data): Organigramme
    {
        $organigramme = Organigramme::findOrFail($id);
        
        if (request()->hasFile('photo')) {
            // TODO: Implémenter l'upload de fichier
            // $aws = new AwsService();
            // $data['photo'] = $aws->upload(request()->file('photo'), "Organigrammes")['full_url'];
        }
        
        $organigramme->update($data);
        return $organigramme;
    }

    /**
     * Supprime un organigramme.
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Récupère les plus récents.
     */
    public function getlatest()
    {
        return $this->latest()->get();
    }

    /**
     * Modifie le statut d'un organigramme.
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['status' => $status]);
    }
}
