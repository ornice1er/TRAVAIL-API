<?php

namespace App\Http\Repositories;

use App\Models\Stage;
use App\Models\Media;
use App\Models\User;
use App\Models\Transmission;
use App\Models\Parcours;
use App\Traits\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class StageRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Stage
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Stage::class);
    }

    /**
     * Récupère tous les stages.
     */
    public function all()
    {
        return Stage::with(['media'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère un stage par ID.
     */
    public function findById($id)
    {
        return Stage::with(['media'])->find($id);
    }

    /**
     * Crée un nouveau stage.
     */
    public function create($data)
    {
        return Stage::create($data);
    }

    /**
     * Met à jour un stage.
     */
    public function update($id, $data)
    {
        $stage = Stage::findOrFail($id);
        return $stage->update($data);
    }

    /**
     * Supprime un stage.
     */
    public function delete($id)
    {
        $stage = Stage::findOrFail($id);
        return $stage->delete();
    }

    /**
     * Récupère les stages par rôle et structure.
     */
    public function getByRoleAndStructure($structureId, $role, $userId)
    {
        if ($role == "saisie") {
            return Media::with(['stage'])->where("type", "stage")
                ->where('structure_id', $structureId)
                ->where('is_published', false)
                ->whereHas('transmissions', function($q) use($userId) {
                    $q->where('to', "=", $userId)->where('is_last', "=", true);
                })->get();
        } elseif ($role == "validation") {
            return Media::with(['stage'])->where("type", "stage")
                ->where('structure_id', $structureId)
                ->whereHas('transmissions', function($q) use($userId) {
                    $q->where('to', "=", $userId)->where('is_last', "=", true);
                })->get();
        }
        
        return collect();
    }

    /**
     * Crée un stage avec Media, Parcours et Transmission.
     */
    public function createWithWorkflow($data)
    {
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = "stage";
        $media->save();

        $slug = Str::slug($data['title'] ?? $data['name']);
        
        // Traitement des dates
        $stageData = collect($data)->only([
            'name', 'resume', 'period_start', 'period_end', 
            'structure', 'status', 'year', 'country', 'city'
        ])->toArray();
        
        if (isset($data['closing_date'])) {
            $stageData['closing_date'] = date_create($data['closing_date']);
        }

        // Vérifier l'unicité du slug
        $count = Stage::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        
        $stageData['slug'] = $slug;
        $stageData['media_id'] = $media->id;
        $stage = Stage::create($stageData);

        // Création du parcours
        Parcours::create([
            'media_id' => $media->id,
            "libelle" => "Création d'une annonce de stage: " . ($data['label'] ?? $data['name'] ?? 'Sans nom')
        ]);

        // Création de la transmission
        Transmission::create([
            'from' => Auth::id(),
            'to' => Auth::id(),
            'media_id' => $media->id,
            'is_last' => true,
        ]);

        return $stage;
    }

    /**
     * Met à jour un stage avec gestion des dates.
     */
    public function updateWithDates($id, $data)
    {
        $media = Media::findOrFail($id);   
        $stage = $media->stage;
        
        $media->fill(array_intersect_key($data, array_flip(['has_principal_access'])))->save();
        
        $updateData = collect($data)->only([
            'name', 'resume', 'structure', 'period_end', 
            'period_start', 'status', 'year', 'country', 'city'
        ])->toArray();
        
        // Traitement des dates
        if (isset($data['closing_date'])) {
            $updateData['closing_date'] = date_create($data['closing_date']);
        }
        if (isset($data['period_end'])) {
            $updateData['period_end'] = date_create($data['period_end']);
        }
        if (isset($data['period_start'])) {
            $updateData['period_start'] = date_create($data['period_start']);
        }

        $slug = Str::slug($data['title'] ?? $data['name']);
        $count = Stage::where('slug', $slug)->where('id', '!=', $stage->id)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $updateData['slug'] = $slug;

        return $stage->fill($updateData)->save();
    }

    /**
     * Faire remonter un stage (transmission vers validation).
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
     * Faire redescendre un stage (transmission vers saisie).
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
     * Publier un stage.
     */
    public function publish($id)
    {
        return Media::find($id)->update(['is_published' => true]);
    }

    /**
     * Dépublier un stage.
     */
    public function unpublish($id)
    {
        return Media::find($id)->update(['is_published' => false]);
    }

    /**
     * Archiver un stage.
     */
    public function archive($id)
    {
        return Media::find($id)->update(['is_archived' => true]);
    }

    /**
     * Restaurer un stage.
     */
    public function restore($id)
    {
        return Media::find($id)->update(['is_archived' => false]);
    }

    /**
     * Vérifie si un stage existe.
     */
    public function ifExist($id)
    {
        return Stage::where('id', $id)->exists();
    }

    /**
     * Récupère tous les stages avec filtres.
     */
    public function getAll($request)
    {
        $query = Stage::with(['media']);
        
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->has('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Récupère un stage par ID.
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée un stage.
     */
    public function store($data)
    {
        return $this->create($data);
    }

    /**
     * Supprime un stage.
     */
    public function destroy($id)
    {
        return $this->delete($id);
    }

    /**
     * Récupère un stage.
     */
    public function get($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée un stage.
     */
    public function makeStore($data): Stage
    {
        return $this->create($data);
    }

    /**
     * Met à jour un stage.
     */
    public function makeUpdate($id, $data): Stage
    {
        $this->update($id, $data);
        return $this->findById($id);
    }

    /**
     * Supprime un stage.
     */
    public function makeDestroy($id)
    {
        return $this->delete($id);
    }
}