<?php

namespace App\Http\Repositories;

use App\Models\Citations;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use Illuminate\Support\Str;
 

class CitationRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Citations
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Citations::class);
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

        $req = Citations::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
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
    public function makeStore($data): Citations
    {
        $model = new Citations($data);

        /*
        $aws = new AwsService();
        if (request()->file('file')) {
            $model->file = $aws->upload(request()->file('file'), "Citations")['full_url'];
        }
        */

        $model->save();

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Citations
    {
        // Récupère la citation à modifier
        $model = Citations::findOrFail($id);

        /*
        $aws = new AwsService();
        if (request()->file('file')) {
            $data['file'] = $aws->upload(request()->file('file'), "Citations")['full_url'];
        }
        */

        // Met à jour les champs
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
        $query = Citations::query();
        $attrs = ['nom', 'lieu', 'type_Citation'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    // Fonctions standardisées pour le controller
    public function getById($id)
    {
        return $this->findOrFail($id);
    }

    public function store($data)
    {
        return $this->makeStore($data);
    }

    public function update($data, $id)
    {
        return $this->makeUpdate($id, $data);
    }

    public function destroy($id)
    {
        return $this->makeDestroy($id);
    }

    public function changeState($state, $id)
    {
        return $this->setStatus($id, $state);
    }

    public function up($id)
    {
        return true;
    }

    public function down($request, $id)
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

    public function generateLink($id, $data)
    {
        $code = Core::generateUniqueCode(Citations::class, 10, 'CIT');
        $link_token = Str::random(40);
        $url = env('APP_FRONT_URL').'/Citation/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto');
        // $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token'] = $link_token;
        $data['code'] = $code;
        $data['lien_unique'] = $url;
        $data['qr_code'] = ''; // base64_encode($qrCode);
        $data['status'] = 1;
        $model = Citations::findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function generateMediaLink($id)
    {
        $model = Citations::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40);
        $url = env('APP_FRONT_URL').'/Citation-gallery/'.$code.'/'.$media_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto');
        // $qrCodeMedia = QrCode::format('png')->size(300)->generate($url);
        $data['media_token'] = $media_token;
        $data['lien_unique_media'] = $url;
        $data['qr_code_media'] = ''; // base64_encode($qrCodeMedia);
        $data['status'] = 2;
        $model->update($data);
        return $model;
    }

    public function verifyLink($code)
    {
        return Citations::where('link_token', $code)
                       ->orWhere('media_token', $code)
                       ->first();
    }

    public function participate($id)
    {
        // TODO: Implement proper participation logic when needed
        return true;
    }

   /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Citation::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Citation/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Citation::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Citation::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Citation-gallery/'.$code.'/'.$media_token;
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
            return Citation::where('link_token', )->first();
        }else{
            return Citation::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Citation_id', $data['Citation_id'])
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

         public function down($request, $id)
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
     */
    
}
