<?php

namespace App\Http\Repositories;

use App\Models\Galerie;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use App\Utilities\FileStorage;
use QrCode;
use Illuminate\Support\Str;
 

class GalerieRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Galerie
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Galerie::class);
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

        $req = Galerie::ignoreRequest(['per_page'])
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
    public function makeStore($data): Galerie
    {
         // Gestion du slug à partir du nom
        $slug = Str::slug($data['name']);
        // Si tu veux assurer l'unicité du slug, tu peux ajouter un suffixe ici (optionnel)
        /*
        $count = Galerie::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        */
        $data['slug'] = $slug;

        // Gestion du fichier photo
        if (request()->hasFile('photo')) {
            $data['photo'] = FileStorage::setFile('public', request()->file('photo'), 'galeries', $slug);
        }

        // Création du modèle
        $model = new Galerie($data);

        /* Si tu utilises AWS ou autre service, tu peux gérer ici l'upload externe
        $aws= new AwsService();
        if(request()->file('file'))  $model->file = $aws->upload(request()->file('file'),"Galeries")['full_url'];
        */

        $model->save();

        return $model;
        }

        /**
         * Met à jour une fête.
         */
        public function makeUpdate($id, $data): Galerie
        {
            $model = Galerie::findOrFail($id);

            // Suppression de l'ancienne photo si une nouvelle est fournie dans $data
            if (isset($data['photo']) && $data['photo']) {
                FileStorage::deleteFile('public', $model->photo, 'galeries');
            }

            /* $aws= new AwsService();
            if(request()->file('file'))  $data['file'] = $aws->upload(request()->file('file'),"Galeries")['full_url']; */

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
     * Recherche dans les galeries (par nom, description...).
     */
    public function search($request)
    {
        $term = $request->input('q', '');
        $query = Galerie::query();
        
        if ($term) {
            $attrs = ['name', 'description'];
            
            foreach ($attrs as $value) {
                $query->orWhere($value, 'like', '%'.$term.'%');
            }
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Galerie::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Galerie/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Galerie::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Galerie::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Galerie-gallery/'.$code.'/'.$media_token;
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
            return Galerie::where('link_token', $data['link_token'])->first();
        }else{
            return Galerie::where('media_token', $data['media_token'])->first();
        }
    }

    function participate($data){
        $check=Invite::where('Galerie_id', $data['Galerie_id'])
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


     public function up($id)
    {
            return true;
    }

         public function down($id)
    {  
            return true;
    }
          
          public function publish($id)
    {
        return true;

    }

    public function unpublish($id)
    {
        return true;
    }

    public function archive($id)
    {
        return true;
    }

    public function restore($id)
    {
        return true;
    }

    public function changeState($id, $state)
    {
        $model = $this->find($id);
        $model->status = $state;
        $model->save();
        return $model;
    }
}
        */

}
