<?php

namespace App\Http\Repositories;

use App\Models\User;
use App\Models\Media;
use App\Models\Parcour;
use App\Utilities\Core;
use App\Models\Category;
use App\Models\Actualite;
use App\Models\Structures;

use App\Traits\Repository;
use Illuminate\Support\Str;
use App\Models\Transmission;
use Illuminate\Http\Request;
use App\Utilities\FileStorage;
use Illuminate\Support\Facades\Auth;

 

class ActualiteRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Actualite
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Actualite::class);
    }

    /**
     * Vérifie si la fête existe.
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère toutes les fêtes avec pagination et filtres.
     */
    public function getAll($request)
    {
                $user = Auth::user();
                $user_id = $user->id;
                $role = $user->roles()->first()->name;
                $per_page = 10;

                // Construction de la requête Media selon le rôle
                $mediaQuery = Media::where('type', 'actualite')
                    ->whereHas('transmissions', function ($q) use ($user_id) {
                        $q->where('to', $user_id)
                        ->where('is_last', true);
                    });

                if ($role === 'saisie' || $role === 'validation') {
                    $mediaQuery->where('structure_id', $user->structure_id)
                            ->where('is_published', false);
                } elseif ($role === 'ccom') {
                    $mediaQuery->where('is_archived', false)
                            ->with('structure');
                } else {
                    return response()->json(['message' => 'Rôle non autorisé.'], 403);
                }

                // Récupération des IDs des medias filtrés
                $mediaIds = $mediaQuery->pluck('id');

                // Requête sur Actualite liée aux medias filtrés
                $req = Actualite::ignoreRequest(['per_page'])
                    ->whereIn('media_id', $mediaIds)
                    ->filter(array_filter($request->all(), function ($k) {
                        return $k !== 'page';
                    }, ARRAY_FILTER_USE_KEY))
                    ->orderByDesc('created_at');

                // Pagination si demandée
                if ($request->has('per_page')) {
                    $per_page = $request->input('per_page');
                    return $req->paginate($per_page);
                } else {
                    return $req->get();
                }
    }

    /**
     * Récupère une fête spécifique.
     */
    public function get($id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Crée une nouvelle fête.
     */
    public function makeStore($data): Actualite
    {

        
                // Création du media
            $media = new Media();
            $media->code = Str::uuid();
            $media->structure_id = Auth::user()->structure_id;
            $media->has_principal_access = $data['has_principal_access'];
            $media->type = 'actualite';
            $media->save();

            // Génération du slug unique
            $slug = Str::slug($data['title']);
            $count = Actualite::where('slug', $slug)->count();

            if ($count > 0) {
                $slug .= '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;

            // Liaison avec le media
            $data['media_id'] = $media->id;

            // Traitement des fichiers photo et big_photo
            if (request()->hasFile('photo')) {
                $data['photo'] = FileStorage::setFile('public', request()->file('photo'), 'actualites', Str::slug($data['title']));
            }

            if (request()->hasFile('big_photo')) {
                $data['big_photo'] = FileStorage::setFile('public', request()->file('big_photo'), 'actualites/big', Str::slug($data['title']) . '-big');
            }

            // Création de l’actualité
            $actualite = Actualite::create($data);

            // Historique dans Parcours
            Parcour::create([
                'media_id' => $media->id,
                'libelle' => "Création de l’actualité : " . $data['title']
            ]);

            // Transmission initiale
            Transmission::create([
                'from' => Auth::id(),
                'to' => Auth::id(),
                'media_id' => $media->id,
                'is_last' => true
            ]);

            return $actualite;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Actualite
    {

          $media=Media::findOrFail($id);   
          $actualite=$media->actualite;
        $media->has_principal_access=$data['has_principal_access'];
        $media->structure_id=$data['structure_id'];
        $media->save();
        $data['slug']=Str::slug($data['title']);

        if(request()->file('photo')){
            FileStorage::deleteFile('public',$actualite->photo,'actualites');
            $data['photo']=FileStorage::setFile('public',request()->file('photo'),'actualites', Str::slug($data['title']));
        }
        if(request()->file('big_photo')){
            FileStorage::deleteFile('public',$actualite->big_photo,'actualites/big');
            $data['big_photo']=FileStorage::setFile('public',request()->file('big_photo'),'actualites/big',Str::slug($data['title'])."-big");
        }
        $model=$actualite->fill($data)->save();

        return $model;
    }

    /**
     * Supprime une fête.
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Récupère les fêtes les plus récentes.
     */
    public function getlatest()
    {
        return $this->latest()->get();
    }

    /**
     * Modifie le statut d'une fête.
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['status' => $status]);
    }

    /**
     * Recherche dans les fêtes (par nom, lieu...).
     */
    public function search($term)
    {
        $query = Actualite::query();
        $attrs = ['nom', 'lieu', 'type_Actualite'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }
 

       public function up($id)
    {
         Media::find($id)->transmissions->last()->update(['is_last'=>false]);
            $to=User::role('ccom')->first()->id;
        Transmission::create([
            'from'=>Auth::id(),
            'to'=>$to,
            'media_id'=>$id,
            'is_last'=>true,
            ]);

            return true;
    }

    public function down($request, $id)
    {     
        Media::find($id)->update(['motif'=>$request->motif]);
        Media::find($id)->transmissions->last()->update(['is_last'=>false]);
      
            $to=User::where('structure_id',Media::find($id)->structure_id)->role('saisie')->first()->id;
        Transmission::create([
            'from'=>Auth::id(),
            'to'=>$to,
            'media_id'=>$id,
            'is_last'=>true,
            ]);

            return true;
    }

          
          public function publish($id)
    {
        Media::find($id)->update(['is_published'=>true]);
        
        return true;

    }

    public function unpublish($id)
    {
        Media::find($id)->update(['is_published'=>false]);
        
        return true;
    }

    public function archive($id)
    {
        Media::find($id)->update(['is_archived'=>true]);
        
        return true;
    }

    public function restore($id)
    {
        Media::find($id)->update(['is_archived'=>false]);
        
        return true;
    }
    

}
