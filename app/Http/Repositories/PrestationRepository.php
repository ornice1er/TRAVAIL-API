<?php

namespace App\Http\Repositories;

use App\Models\Prestation;
use App\Models\Media;
use App\Models\User;
use App\Models\Transmission;
use App\Models\Parcours;
use App\Traits\Repository;
use App\Utilities\FileStorage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PrestationRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Prestation
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Prestation::class);
    }

    /**
     * Récupère toutes les prestations.
     */
    public function all()
    {
        return Prestation::with(['media'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère une prestation par ID.
     */
    public function findById($id)
    {
        return Prestation::with(['media'])->find($id);
    }

    /**
     * Crée une nouvelle prestation.
     */
    public function create($data)
    {
        return Prestation::create($data);
    }

    /**
     * Met à jour une prestation.
     */
    public function update($id, $data)
    {
        $prestation = Prestation::findOrFail($id);
        return $prestation->update($data);
    }

    /**
     * Supprime une prestation.
     */
    public function delete($id)
    {
        $prestation = Prestation::findOrFail($id);
        return $prestation->delete();
    }

    /**
     * Récupère les prestations par rôle et structure.
     */
    public function getByRoleAndStructure($structureId, $role, $userId)
    {
        if ($role == "saisie") {
            return Media::with(['prestation'])->where("type", "prestation")
                ->where('structure_id', $structureId)
                ->where('is_published', false)
                ->whereHas('transmissions', function($q) use($userId) {
                    $q->where('to', "=", $userId)->where('is_last', "=", true);
                })->get();
        } elseif ($role == "validation") {
            return Media::with(['prestation'])->where("type", "prestation")
                ->where('structure_id', $structureId)
                ->whereHas('transmissions', function($q) use($userId) {
                    $q->where('to', "=", $userId)->where('is_last', "=", true);
                })->get();
        }
        
        return collect();
    }

    /**
     * Crée une prestation avec Media, Parcours et Transmission.
     */
    public function createWithWorkflow($data)
    {
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = "prestation";
        $media->save();

        $slug = Str::slug($data['title'] ?? $data['name']);
        
        // Gestion de l'image si présente
        if (isset($data['image']) && $data['image']) {
            $data['image'] = FileStorage::setFile('public', $data['image'], 'prestations', $slug);
        }

        // Vérifier l'unicité du slug
        $count = Prestation::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        
        $data['slug'] = $slug;
        $data['media_id'] = $media->id;
        $prestation = Prestation::create($data);

        // Création du parcours
        Parcours::create([
            'media_id' => $media->id,
            "libelle" => "Création de la prestation " . ($data['name'] ?? 'Sans nom')
        ]);

        // Création de la transmission
        Transmission::create([
            'from' => Auth::id(),
            'to' => Auth::id(),
            'media_id' => $media->id,
            'is_last' => true,
        ]);

        return $prestation;
    }

    /**
     * Met à jour une prestation avec gestion de fichier.
     */
    public function updateWithFile($id, $data, $file = null)
    {
        $media = Media::findOrFail($id);   
        $prestation = $media->prestation;
        
        $media->fill(array_intersect_key($data, array_flip(['has_principal_access'])))->save();
        
        $updateData = array_intersect_key($data, array_flip(['name', 'link', 'status']));
        $slug = Str::slug($data['title'] ?? $data['name']);
        
        $count = Prestation::where('slug', $slug)->where('id', '!=', $prestation->id)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $updateData['slug'] = $slug;

        if ($file) {
            $updateData['image'] = FileStorage::setFile('public', $file, 'prestations', $slug);
        }

        return $prestation->fill($updateData)->save();
    }

    /**
     * Faire remonter une prestation (transmission vers validation).
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
     * Faire redescendre une prestation (transmission vers saisie).
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
     * Publier une prestation.
     */
    public function publish($id)
    {
        return Media::find($id)->update(['is_published' => true]);
    }

    /**
     * Dépublier une prestation.
     */
    public function unpublish($id)
    {
        return Media::find($id)->update(['is_published' => false]);
    }

    /**
     * Archiver une prestation.
     */
    public function archive($id)
    {
        return Media::find($id)->update(['is_archived' => true]);
    }

    /**
     * Restaurer une prestation.
     */
    public function restore($id)
    {
        return Media::find($id)->update(['is_archived' => false]);
    }

    /**
     * Vérifie si une prestation existe.
     */
    public function ifExist($id)
    {
        return Prestation::where('id', $id)->exists();
    }

    /**
     * Récupère toutes les prestations avec filtres.
     */
    public function getAll($request)
    {
        $query = Prestation::with(['media']);
        
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Récupère une prestation par ID.
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée une prestation.
     */
    public function store($data)
    {
        return $this->create($data);
    }

    /**
     * Supprime une prestation.
     */
    public function destroy($id)
    {
        return $this->delete($id);
    }

    /**
     * Récupère une prestation.
     */
    public function get($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée une prestation.
     */
    public function makeStore($data): Prestation
    {
        return $this->create($data);
    }

    /**
     * Met à jour une prestation.
     */
    public function makeUpdate($id, $data): Prestation
    {
        $this->update($id, $data);
        return $this->findById($id);
    }

    /**
     * Supprime une prestation.
     */
    public function makeDestroy($id)
    {
        return $this->delete($id);
    }
}