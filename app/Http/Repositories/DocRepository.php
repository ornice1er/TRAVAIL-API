<?php

namespace App\Http\Repositories;

use App\Models\Doc;
use App\Models\User;
use App\Models\Media;
use App\Models\Transmission;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
 

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

        $req = Doc::ignoreRequest(['per_page'])
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
    public function makeStore($data): Doc
    {
       $model = new Doc($data);

        /*$aws= new AwsService();
        if(request()->file('file'))  $model->file = $aws->upload(request()->file('file'),"Docs")['full_url'];*/

        // Gestion du fichier doc si présent dans $data
        if (request()->hasFile('doc')) {
            // Génère un slug pour nommer le fichier
            $slug = Str::slug($data['name'] ?? 'document') . '-' . date('ymdis') . '-' . rand(0, 999);
            // TODO: Implémenter le service de stockage de fichiers
            // $model->filename = FileStorage::setFile('public', request()->file('doc'), 'docs', $slug . ".pdf");
        }

        $model->save();

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Doc
    {
         $model = Doc::findOrFail($id);

        // Générer le slug à partir du nom (ou titre) dans $data
        if (isset($data['name'])) {
            $slug = Str::slug($data['name']);
            $count = Doc::where('slug', $slug)->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        /*$aws= new AwsService();
        if(request()->file('file'))  $data['file'] = $aws->upload(request()->file('file'),"Docs")['full_url'];*/

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
