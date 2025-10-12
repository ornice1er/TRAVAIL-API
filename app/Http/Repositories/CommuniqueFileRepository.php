<?php

namespace App\Http\Repositories;

use App\Models\CommuniquesFile;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
 

class CommuniquesFileRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var CommuniquesFile
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(CommuniquesFile::class);
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

        $req = CommuniquesFile::ignoreRequest(['per_page','pageSize','page'])
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
    public function makeStore($data): CommuniquesFile
    {
       $model = new CommuniquesFile($data);
        /* $aws = new AwsService();
        if (request()->file('file')) {
            $model->file = $aws->upload(request()->file('file'), "CommuniquesFiles")['full_url'];
        } */
        $model->save();

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): CommuniquesFile
    {
      $model = CommuniquesFile::findOrFail($id);
        /* $aws = new AwsService();
        if (request()->file('file')) {
            $data['file'] = $aws->upload(request()->file('file'), "CommuniquesFiles")['full_url'];
        } */
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
        $query = CommuniquesFile::query();
        $attrs = ['nom', 'lieu', 'type_CommuniquesFile'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(CommuniquesFile::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/CommuniquesFile/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = CommuniquesFile::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = CommuniquesFile::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/CommuniquesFile-gallery/'.$code.'/'.$media_token;
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
            return CommuniquesFile::where('link_token', $data['link_token'])->first();
        }else{
            return CommuniquesFile::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('CommuniquesFile_id', $data['CommuniquesFile_id'])
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
        // Implement actual logic for moving up in order
        return $this->findOrFail($id);
    }

    public function down($request, $id)
    {  
        // Implement actual logic for moving down in order
        return $this->findOrFail($id);
    }
          
    public function publish($id)
    {
        return $this->findOrFail($id)->update(['status' => 'published']);
    }

    public function unpublish($id)
    {
        return $this->findOrFail($id)->update(['status' => 'draft']);
    }

    public function archive($id)
    {
        return $this->findOrFail($id)->update(['status' => 'archived']);
    }

    public function restore($id)
    {
        return $this->findOrFail($id)->update(['status' => 'active']);
    }

}
