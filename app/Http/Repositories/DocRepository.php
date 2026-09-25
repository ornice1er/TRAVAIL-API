<?php

namespace App\Http\Repositories;

use App\Models\Doc;
use App\Models\User;
use App\Models\Media;
use App\Models\Transmission;
use App\Models\Parcours;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Utilities\FileStorage;

 

class DocRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Doc
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Doc::class);
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

        $req = Doc::ignoreRequest(['pageSize','page'])
              ->with('media')
            ->orderByDesc('created_at');

        if (array_key_exists('pageSize', $request->all())) {
            $per_page = $request['pageSize'];
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
        return $this->findOrFail($id)->load('media');
    }

    /**
     * Crée une nouvelle fête.
     */
    public function makeStore($data)
    {

          $media=new Media();
        $media->code=Str::uuid();
        $media->structure_id=Auth::user()->structure_id;
        $media->has_principal_access=$data['has_principal_access'];
        $media->type="doc";
        $media->save();

        $uid=uniqId();
        $data['slug']=Str::substr(Str::slug($data['name']), 0, 150).' '.$uid;
        $data['media_id']=$media->id;

        // Nom de fichier plus court que le slug : limite de 255 octets (système de fichiers et colonne filename)
        if(request()->file('filename'))  $data['filename']=FileStorage::setFile('public',request()->file('filename'),'docs', Str::substr(Str::slug($data['name']), 0, 150).' '.$uid.".pdf");

        $status=Doc::create($data);

                Parcours::create([
            'media_id'=>$media->id,
            "libelle"=>"Création du document ".$data['name']
                ]);
                Transmission::create([
                    'from'=>Auth::id(),
                    'to'=>Auth::id(),
                    'media_id'=>$media->id,
                    'is_last'=>true,
                    ]);
    

        return true;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Doc
    {
         $model = Doc::findOrFail($id);

        // Générer le slug à partir du nom (ou titre) dans $data
        if (isset($data['name'])) {
            $slug = Str::substr(Str::slug($data['name']), 0, 150);
            $count = Doc::where('slug', $slug)->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        // Remplacement du fichier : même convention de nommage qu'à la création
        unset($data['filename']);
        if(request()->file('filename'))  $data['filename']=FileStorage::setFile('public',request()->file('filename'),'docs', Str::substr(Str::slug($data['name'] ?? $model->name), 0, 150).' '.uniqId().".pdf");

        if (array_key_exists('has_principal_access', $data) && $model->media) {
            $model->media->update(['has_principal_access' => $data['has_principal_access']]);
        }

        $model->update($data);

        return $model;
    }

    /**
     * Supprime une fête.
     */
    public function makeDestroy($id)
    {
        // Supprime aussi le média lié (et son circuit) : sinon les listes publiques basées sur Media
        // remontent un média orphelin dont la relation vaut null.
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $model = $this->findOrFail($id);
            if ($model->media) {
                $model->media->transmissions()->delete();
                $model->media->parcours()->delete();
                $model->media->delete();
            }

            return $model->delete();
        });
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
     * Recherche dans les documents (par nom, description...).
     */
    public function search($term)
    {
        $query = Doc::query();
        $attrs = ['name', 'description', 'type'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Doc::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Doc/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Doc::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Doc::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Doc-gallery/'.$code.'/'.$media_token;
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
            return Doc::where('link_token', $data['link_token'])->first();
        }else{
            return Doc::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Doc_id', $data['Doc_id'])
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
        Media::find($id)->transmissions->last()->update(['is_last'=>false]);
        $to=User::where('structure_id',Auth::user()->structure_id)->role('validation')->first()->id;
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
        Media::find($id)->update(['motif'=>$request['motif']]);
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
