<?php

namespace App\Http\Repositories;

use App\Models\AppelsOffre;
use App\Models\Invite;
use App\Models\Media;
use App\Models\User;
use App\Models\Transmission;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use Illuminate\Support\Facades\Auth;
use QrCode;
use Illuminate\Support\Str;
 

class AppelsOffreRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var AppelsOffre
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(AppelsOffre::class);
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

        // Construction de la requête sur AppelsOffre avec filtres dynamiques
        $req = AppelsOffre::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->orderByDesc('created_at');

        // Ajout condition sur medias selon rôle et user
        if ($role === 'saisie') {
            $req->whereHas('media', function ($query) use ($user, $user_id) {
                $query->where('type', 'offre')
                    ->where('structure_id', $user->structure_id)
                    ->where('is_published', false)
                    ->whereHas('transmissions', function ($q) use ($user_id) {
                        $q->where('to', $user_id)->where('is_last', true);
                    });
            });
        } elseif ($role === 'validation') {
            $req->whereHas('media', function ($query) use ($user, $user_id) {
                $query->where('type', 'offre')
                    ->where('structure_id', $user->structure_id)
                    ->whereHas('transmissions', function ($q) use ($user_id) {
                        $q->where('to', $user_id)->where('is_last', true);
                    });
            });
        }

        // Pagination dynamique
        if ($request->has('pageSize')) {
            $per_page = (int) $request->input('pageSize');
            $offres = $req->paginate($per_page);
        } else {
            $offres = $req->get();
        }

        return $offres;
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
    public function makeStore($data): AppelsOffre
    {
         // Création du media associé
            $media = new Media();
            $media->code = Str::uuid();
            $media->structure_id = Auth::user()->structure_id;
            $media->has_principal_access = $data['has_principal_access'] ?? false;
            $media->type = 'offre';
            $media->save();

            // Préparation du slug unique
            $slug = Str::slug($data['title'] ?? $data['label'] ?? 'offre');
            if (AppelsOffre::where('slug', $slug)->exists()) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }

            // Conversion des dates au format string (Y-m-d)
            $dateEmission = isset($data['date_emission']) ? date('Y-m-d', strtotime($data['date_emission'])) : null;
            $dateCloture = isset($data['date_cloture']) ? date('Y-m-d', strtotime($data['date_cloture'])) : null;

            // Préparation des données pour AppelsOffre
            $offreData = [
                'label' => $data['label'] ?? null,
                'representant_offre' => $data['representant_offre'] ?? null,
                'status' => $data['status'] ?? null,
                'date_emission' => $dateEmission,
                'date_cloture' => $dateCloture,
                'slug' => $slug,
                'media_id' => $media->id,
            ];

            // Création de l'offre
            $offre = AppelsOffre::create($offreData);

            return $offre;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): AppelsOffre
    {
        $media = Media::findOrFail($id);
        $offre = $media->offre;

        /*
        $aws = new AwsService();
        if (request()->file('file')) {
            $data['file'] = $aws->upload(request()->file('file'), "AppelsOffres")['full_url'];
        }
        */

        // Mise à jour du media si besoin
        if (isset($data['has_principal_access'])) {
            $media->update(['has_principal_access' => $data['has_principal_access']]);
        }

        $offre->update($data);

        return $offre;
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
        $query = AppelsOffre::query();
        $attrs = ['nom', 'lieu', 'type_AppelsOffre'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(AppelsOffre::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/AppelsOffre/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = AppelsOffre::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = AppelsOffre::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/AppelsOffre-gallery/'.$code.'/'.$media_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCodeMedia = QrCode::format('png')->size(300)->generate($url);
        $data['media_token']=$media_token;
        $data['lien_unique_media']=$url ;
        $data['qr_code_media'] = base64_encode($qrCodeMedia);
        $data['status'] = 2;
        $model->update($data);
        return $model;
    }

    function verifyLink($data) {
        if (isset($data['link_token'])) {
            return AppelsOffre::where('link_token', )->first();
        }else{
            return AppelsOffre::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('AppelsOffre_id', $data['AppelsOffre_id'])
            ->where('phone', $data['phone'])
            ->first();
            if ($check) {
                $check->update($data);
            }else{
                $model = new Invite($data);
                $model->save();
            }
   

        return $model;

    }*/

    public function up($id)
    {
         $media = Media::findOrFail($id);

        $lastTransmission = $media->transmissions->last();
        if ($lastTransmission) {
            $lastTransmission->update(['is_last' => false]);
        }

        $toUser = User::where('structure_id', $media->structure_id)
                    ->role('validation')
                    ->first();

        if (!$toUser) {
            return false; // ou gérer autrement selon ton besoin
        }

        Transmission::create([
            'from' => Auth::id(),
            'to' => $toUser->id,
            'media_id' => $id,
            'is_last' => true,
        ]);

        return true;
    }

         public function down($id)
    {  
            $media = Media::findOrFail($id);

            // Marquer la dernière transmission comme non "is_last"
            $lastTransmission = $media->transmissions->last();
            if ($lastTransmission) {
                $lastTransmission->update(['is_last' => false]);
            }

            // Récupérer un utilisateur avec le rôle "saisie" dans la même structure
            $toUser = User::where('structure_id', $media->structure_id)
                        ->role('saisie')
                        ->first();

            // Créer une nouvelle transmission si l'utilisateur existe
            if ($toUser) {
                Transmission::create([
                    'from' => Auth::id(),
                    'to' => $toUser->id,
                    'media_id' => $id,
                    'is_last' => true,
                ]);
            }

            return true;
    }
          

          public function publish($id)
    { 
        $media = Media::findOrFail($id);
        $media->update(['is_published' => true]);

        return true;

    }

    public function unpublish($id)
    {
         $media = Media::findOrFail($id);
        $media->update(['is_published' => false]);

        return true;
    }

    public function archive($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_archived' => true]);

        return true;
    }

    public function restore($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_archived' => false]);

        return true;
    }
    
}
