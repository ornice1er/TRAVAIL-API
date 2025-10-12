<?php

namespace App\Http\Repositories;

use App\Models\Mot;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use App\Utilities\FileStorage;
 

class MotRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Mot
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Mot::class);
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

        $req = Mots::ignoreRequest(['per_page','pageSize','page'])
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
    public function makeStore($data): Mot
    {
        // Gestion des fichiers d'images si présents dans $data (attendu $data['first_image_file'], $data['second_image_file'])
            if (isset($data['first_image_file'])) {
                $data['first_image'] = FileStorage::setFile(
                    'public',
                    $data['first_image_file'],
                    'mots',
                    Str::slug($data['title']) . date("dmYhis")
                );
                unset($data['first_image_file']); // On retire l'objet fichier après traitement
            }

            if (isset($data['second_image_file'])) {
                $data['second_image'] = FileStorage::setFile(
                    'public',
                    $data['second_image_file'],
                    'mots',
                    Str::slug($data['title']) . date("dmYhis") . "-2"
                );
                unset($data['second_image_file']);
            }

            $model = new Mot($data);

            /*$aws= new AwsService();
            if(request()->file('file'))  $model->file = $aws->upload(request()->file('file'),"Mots")['full_url'];*/

            $model->save();

            return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Mot
    {
         $model = Mot::findOrFail($id);

        // Si le tableau $data contient les fichiers 'first_image' ou 'second_image', on les traite
        if (isset($data['first_image_file'])) {
            // Suppression de l'ancienne image
            FileStorage::deleteFile('public', $model->first_image, 'mots');
            // Enregistrement de la nouvelle image
            $data['first_image'] = FileStorage::setFile(
                'public',
                $data['first_image_file'],
                'mots',
                Str::slug($data['title']) . date("dmYhis")
            );
            unset($data['first_image_file']); // On supprime la clé 'first_image_file' pour éviter problème dans update
        }

        if (isset($data['second_image_file'])) {
            FileStorage::deleteFile('public', $model->second_image, 'mots');
            $data['second_image'] = FileStorage::setFile(
                'public',
                $data['second_image_file'],
                'mots',
                Str::slug($data['title']) . date("dmYhis") . "-2"
            );
            unset($data['second_image_file']);
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
     * Recherche dans les mots (par titre, résumé...).
     */
    public function search($request)
    {
        $term = $request->input('q', '');
        $query = Mot::query();
        
        if ($term) {
            $attrs = ['title', 'resume'];
            
            foreach ($attrs as $value) {
                $query->orWhere($value, 'like', '%'.$term.'%');
            }
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Mot::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Mot/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Mot::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Mot::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Mot-gallery/'.$code.'/'.$media_token;
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
            return Mot::where('link_token', )->first();
        }else{
            return Mot::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Mot_id', $data['Mot_id'])
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
    }*/
}
