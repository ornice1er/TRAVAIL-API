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

        $req = Organigramme::ignoreRequest(['per_page','pageSize','page'])
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

    /**
     * Récupère les organigrammes par rôle et structure.
     */
    public function getByRoleAndStructure($structureId, $role, $userId)
    {
        if ($role == "saisie") {
            return Media::with(['org'])->where("type", "organigramme")
                ->where('structure_id', $structureId)
                ->where('is_published', false)
                ->whereHas('transmissions', function($q) use($userId) {
                    $q->where('to', "=", $userId)->where('is_last', "=", true);
                })->get();
        } elseif ($role == "validation") {
            return Media::with(['org'])->where("type", "organigramme")
                ->where('structure_id', $structureId)
                ->whereHas('transmissions', function($q) use($userId) {
                    $q->where('to', "=", $userId)->where('is_last', "=", true);
                })->get();
        } elseif ($role == "ccom") {
            return Media::with(['org'])->where("type", "organigramme")
                ->where('structure_id', $structureId)->get();
        }
        
        return collect();
    }

    /**
     * Crée un organigramme avec Media, Parcours et Transmission.
     */
    public function createWithWorkflow($data)
    {
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = "organigramme";
        $media->save();

        $slug = Str::slug($data['name']).'-'.date('ymdis').'-'.rand(0,999);
        
        // Traitement du fichier photo si présent
        if (isset($data['photo'])) {
            $data['photo'] = \App\Utilities\FileStorage::setFile('public', $data['photo'], 'organigrammes', $slug);
        }
        
        $data['media_id'] = $media->id;
        $organigramme = Organigramme::create($data);

        // Création du parcours
        \App\Models\Parcours::create([
            'media_id' => $media->id,
            "libelle" => "Création de l'organigramme " . $data['name']
        ]);

        // Création de la transmission
        Transmission::create([
            'from' => Auth::id(),
            'to' => Auth::id(),
            'media_id' => $media->id,
            'is_last' => true,
        ]);

        return $organigramme;
    }

    /**
     * Met à jour un organigramme avec gestion du fichier.
     */
    public function updateWithFile($id, $data, $file = null)
    {
        $media = Media::findOrFail($id);   
        $org = $media->org;
        
        $media->fill(array_intersect_key($data, array_flip(['has_principal_access'])))->save();
        
        $updateData = array_intersect_key($data, array_flip(['name', 'legend']));
        $slug = Str::slug($data['name']).'-'.date('ymdis').'-'.rand(0,999);

        if ($file) {
            \App\Utilities\FileStorage::deleteFile('public', $file, $org->photo);
            $updateData['photo'] = \App\Utilities\FileStorage::setFile('public', $file, 'organigrammes', $slug);
        }

        return $org->fill($updateData)->save();
    }

    /**
     * Faire remonter un organigramme (transmission vers validation).
     */
    public function moveUp($id)
    {
        Media::find($id)->transmissions->last()->update(['is_last' => false]);
        
        $to = User::where('structure_id', Auth::user()->structure_id)
            ->role('validation')->first()->id;
            
        Transmission::create([
            'from' => Auth::id(),
            'to' => $to,
            'media_id' => $id,
            'is_last' => true,
        ]);

        return true;
    }

    /**
     * Faire redescendre un organigramme (transmission vers saisie).
     */
    public function moveDown($id, $motif = null)
    {
        if ($motif) {
            Media::find($id)->update(['motif' => $motif]);
        }
        
        Media::find($id)->transmissions->last()->update(['is_last' => false]);
      
        $to = User::where('structure_id', Media::find($id)->structure_id)
            ->role('saisie')->first()->id;
            
        Transmission::create([
            'from' => Auth::id(),
            'to' => $to,
            'media_id' => $id,
            'is_last' => true,
        ]);

        return true;
    }

    /**
     * Publier un organigramme.
     */
    public function publish($id)
    {
        return Media::find($id)->update(['is_published' => true]);
    }

    /**
     * Dépublier un organigramme.
     */
    public function unpublish($id)
    {
        return Media::find($id)->update(['is_published' => false]);
    }

    /**
     * Archiver un organigramme.
     */
    public function archive($id)
    {
        return Media::find($id)->update(['is_archived' => true]);
    }

    /**
     * Restaurer un organigramme.
     */
    public function restore($id)
    {
        return Media::find($id)->update(['is_archived' => false]);
    }
}
