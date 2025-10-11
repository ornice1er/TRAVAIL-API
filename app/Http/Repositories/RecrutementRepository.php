<?php

namespace App\Http\Repositories;

use App\Models\Recrutement;
use App\Models\Media;
use App\Models\Parcours;
use App\Models\Transmission;
use App\Models\User;
use App\Traits\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RecrutementRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Recrutement
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Recrutement::class);
    }

    /**
     * Vérifie si le recrutement existe.
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère tous les recrutements avec pagination et filtres.
     * Basé sur la logique du site avec gestion des rôles.
     */
    public function getAll($request)
    {
        $per_page = 10;
        $user_id = Auth::id();
        $user = Auth::user();

        // Construction de la requête selon le rôle de l'utilisateur
        $query = Media::with(['recrutement'])->where("type", "recrutement");

        // Pour l'instant, filtrer simplement par structure de l'utilisateur si disponible
        if ($user && isset($user->structure_id)) {
            $query->where('structure_id', $user->structure_id);
        }

        // Application des filtres de la requête
        if ($request->has('title')) {
            $query->whereHas('recrutement', function($q) use($request) {
                $q->where('title', 'like', '%' . $request->title . '%');
            });
        }

        if ($request->has('status')) {
            $query->whereHas('recrutement', function($q) use($request) {
                $q->where('status', $request->status);
            });
        }

        $query->orderBy('created_at', 'desc');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return $query->paginate($per_page);
        } else {
            return $query->get();
        }
    }

    /**
     * Récupère un recrutement spécifique.
     */
    public function get($id)
    {
        return Media::with(['recrutement'])->findOrFail($id);
    }

    /**
     * Crée un nouveau recrutement.
     * Basé sur la méthode store du site.
     */
    public function makeStore($data)
    {
        // Création du Media
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id ?? null;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = "recrutement";
        $media->save();

        // Préparation des données du recrutement
        $recrutementData = [
            'title' => $data['title'],
            'announcement_date' => isset($data['announcement_date']) ? date_create($data['announcement_date']) : null,
            'test_date' => $data['test_date'] ?? null,
            'exam_place' => $data['exam_place'] ?? null,
            'resume' => $data['resume'] ?? null,
            'status' => $data['status'] ?? 0,
            'media_id' => $media->id
        ];

        // Génération du slug unique
        $slug = Str::slug($data['title']);
        $count = Recrutement::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $recrutementData['slug'] = $slug;

        // Création du recrutement
        $recrutement = Recrutement::create($recrutementData);

        // Création du parcours
        Parcours::create([
            'media_id' => $media->id,
            "libelle" => "Création d'un recrutement: " . $data['title']
        ]);

        // Création de la transmission
        Transmission::create([
            'from' => Auth::id(),
            'to' => Auth::id(),
            'media_id' => $media->id,
            'is_last' => true,
        ]);

        return $media->load('recrutement');
    }

    /**
     * Met à jour un recrutement.
     * Basé sur la méthode update du site.
     */
    public function makeUpdate($id, $data)
    {
        $media = Media::findOrFail($id);
        $recrutement = $media->recrutement;

        // Mise à jour du Media
        $media->fill([
            'has_principal_access' => $data['has_principal_access'] ?? $media->has_principal_access
        ])->save();

        // Préparation des données du recrutement
        $recrutementData = [
            'title' => $data['title'] ?? $recrutement->title,
            'announcement_date' => isset($data['announcement_date']) ? date_create($data['announcement_date']) : $recrutement->announcement_date,
            'test_date' => $data['test_date'] ?? $recrutement->test_date,
            'exam_place' => $data['exam_place'] ?? $recrutement->exam_place,
            'resume' => $data['resume'] ?? $recrutement->resume,
            'status' => $data['status'] ?? $recrutement->status
        ];

        // Mise à jour du slug si le titre a changé
        if (isset($data['title']) && $data['title'] !== $recrutement->title) {
            $slug = Str::slug($data['title']);
            $count = Recrutement::where('slug', $slug)->where('id', '!=', $recrutement->id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $recrutementData['slug'] = $slug;
        }

        $recrutement->fill($recrutementData)->save();

        return $media->load('recrutement');
    }

    /**
     * Supprime un recrutement.
     */
    public function makeDestroy($id)
    {
        $media = Media::findOrFail($id);
        $recrutement = $media->recrutement;
        
        if ($recrutement) {
            $recrutement->delete();
        }
        
        return $media->delete();
    }

    /**
     * Remonte un recrutement dans le workflow.
     * Basé sur la méthode up du site.
     */
    public function up($id)
    {
        $media = Media::findOrFail($id);
        
        // Marquer la dernière transmission comme non-dernière
        $media->transmissions()->where('is_last', true)->update(['is_last' => false]);
        
        // Trouver l'utilisateur de validation (simplifié pour l'instant)
        $currentUser = Auth::user();
        $to = User::where('structure_id', $currentUser->structure_id ?? null)
                  ->where('id', '!=', Auth::id())
                  ->first();
        
        if ($to) {
            Transmission::create([
                'from' => Auth::id(),
                'to' => $to->id,
                'media_id' => $id,
                'is_last' => true,
            ]);
        }

        return $media;
    }

    /**
     * Redescend un recrutement dans le workflow.
     * Basé sur la méthode down du site.
     */
    public function down($id, $motif = null)
    {
        $media = Media::findOrFail($id);
        
        // Mettre à jour le motif
        if ($motif) {
            $media->update(['motif' => $motif]);
        }
        
        // Marquer la dernière transmission comme non-dernière
        $media->transmissions()->where('is_last', true)->update(['is_last' => false]);
        
        // Trouver l'utilisateur de saisie (simplifié pour l'instant)
        $currentUser = Auth::user();
        $to = User::where('structure_id', $media->structure_id)
                  ->where('id', '!=', Auth::id())
                  ->first();
        
        if ($to) {
            Transmission::create([
                'from' => Auth::id(),
                'to' => $to->id,
                'media_id' => $id,
                'is_last' => true,
            ]);
        }

        return $media;
    }

    /**
     * Publie un recrutement.
     */
    public function publish($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_published' => true]);
        return $media;
    }

    /**
     * Dépublie un recrutement.
     */
    public function unpublish($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_published' => false]);
        return $media;
    }

    /**
     * Archive un recrutement.
     */
    public function archive($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_archived' => true]);
        return $media;
    }

    /**
     * Restaure un recrutement archivé.
     */
    public function restore($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_archived' => false]);
        return $media;
    }

    /**
     * Recherche dans les recrutements.
     */
    public function search($term)
    {
        return Media::with(['recrutement'])
            ->where('type', 'recrutement')
            ->whereHas('recrutement', function($query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%')
                      ->orWhere('resume', 'like', '%' . $term . '%')
                      ->orWhere('exam_place', 'like', '%' . $term . '%');
            })
            ->get();
    }

    /**
     * Modifie le statut d'un recrutement.
     */
    public function setStatus($id, $status)
    {
        $media = Media::findOrFail($id);
        $recrutement = $media->recrutement;
        
        if ($recrutement) {
            $recrutement->update(['status' => $status]);
        }
        
        return $media->load('recrutement');
    }

    /**
     * Suppression en masse de recrutements.
     */
    public function massDestroy(array $ids)
    {
        return Media::whereIn('id', $ids)->where('type', 'recrutement')->delete();
    }

    /**
     * Supprime définitivement un recrutement.
     */
    public function forceDelete($id)
    {
        $media = Media::withTrashed()->findOrFail($id);
        $recrutement = $media->recrutement()->withTrashed()->first();
        
        if ($recrutement) {
            $recrutement->forceDelete();
        }
        
        return $media->forceDelete();
    }

    /**
     * Active/désactive le statut d'un recrutement.
     */
    public function toggleStatus($id)
    {
        $media = Media::findOrFail($id);
        $recrutement = $media->recrutement;
        
        if ($recrutement) {
            $newStatus = $recrutement->status == 1 ? 0 : 1;
            $recrutement->update(['status' => $newStatus]);
        }
        
        return $media->load('recrutement');
    }
}