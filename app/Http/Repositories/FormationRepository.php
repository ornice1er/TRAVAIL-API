<?php

namespace App\Http\Repositories;

use App\Models\Formation;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use App\Models\Media;
use App\Models\Parcours;
use App\Models\Transmission;
use App\Models\User;

 

class FormationRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Formation
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Formation::class);
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
       $per_page = 10;

        $req = Formation::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            /*->with('invites')*/
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
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
    public function makeStore($data): Formation
    {
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = "formation";
        $media->save();

        $slug = Str::slug($data['title']);
        $count = Formation::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }

        $data['slug'] = $slug;
        $data['media_id'] = $media->id;

        if (isset($data['cloture'])) {
            $data['cloture'] = date_create($data['cloture']);
        }

        $model = new Formation($data);

        /*$aws = new AwsService();
        if(request()->file('file')) {
            $model->file = $aws->upload(request()->file('file'), "Formations")['full_url'];
        }*/

        $model->save();

        Parcours::create([
            'media_id' => $media->id,
            'libelle' => "Création d'une formation: " . ($data['label'] ?? $data['libellé'] ?? 'Formation'),
        ]);

        Transmission::create([
            'from' => Auth::id(),
            'to' => Auth::id(),
            'media_id' => $media->id,
            'is_last' => true,
        ]);

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Formation
    {
        $model = Formation::findOrFail($id);

        // Récupération du média lié à la formation
        $media = $model->media;

        // Mise à jour du média si nécessaire
        if ($media && isset($data['has_principal_access'])) {
            $media->has_principal_access = $data['has_principal_access'];
            $media->save();
        }

        // Traitement de la date de clôture
        if (isset($data['cloture'])) {
            $data['cloture'] = date_create($data['cloture']);
        }

        // Génération du slug si 'title' est présent
        if (isset($data['title'])) {
            $slug = Str::slug($data['title']);
            $count = Formation::where('slug', $slug)->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        $model->update($data);

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
        $query = Formation::query();
        $attrs = ['nom', 'lieu', 'type_Formation'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Formation::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Formation/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Formation::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Formation::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Formation-gallery/'.$code.'/'.$media_token;
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
            return Formation::where('link_token', )->first();
        }else{
            return Formation::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Formation_id', $data['Formation_id'])
            ->where('phone', $data['phone'])
            ->first();
            if ($check) {
                $check->update($data);
            }else{
                $model = new Invite($data);
                $model->save();
            }
   

        return $model;

    }
*/

     public function up($id)
    {
            $media = Media::findOrFail($id);

            // Met à jour la dernière transmission pour qu'elle ne soit plus la dernière
            $media->transmissions->last()->update(['is_last' => false]);

            // Trouve l'utilisateur "validation" de la même structure
            $to = User::where('structure_id', Auth::user()->structure_id)
                    ->role('validation')
                    ->first()
                    ->id;

            // Crée une nouvelle transmission
            Transmission::create([
                'from' => Auth::id(),
                'to'   => $to,
                'media_id' => $id,
                'is_last'  => true,
            ]);

            return true;
    }

         public function down($request, $id)
    {  
            $media = Media::findOrFail($id);

            // Mise à jour du motif
            $media->update(['motif' => $request->motif]);

            // Marquer l'ancienne transmission comme non dernière
            $media->transmissions->last()->update(['is_last' => false]);

            // Trouver l'utilisateur "saisie" de la même structure
            $to = User::where('structure_id', $media->structure_id)
                    ->role('saisie')
                    ->first()
                    ->id;

            // Créer une nouvelle transmission
            Transmission::create([
                'from' => Auth::id(),
                'to' => $to,
                'media_id' => $id,
                'is_last' => true,
            ]);

            return true;
    }
          
          public function publish($id)
    {
         Media::findOrFail($id)->update(['is_published' => true]);

        return true;

    }

    public function unpublish($id)
    {
         Media::findOrFail($id)->update(['is_published' => false]);

        return true;
    }

    public function archive($id)
    {
        Media::findOrFail($id)->update(['is_archived' => true]);

        return true;
    }

    public function restore($id)
    {
        Media::findOrFail($id)->update(['is_archived' => false]);

        return true;
    }

}
