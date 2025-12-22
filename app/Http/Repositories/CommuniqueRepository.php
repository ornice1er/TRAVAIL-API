<?php

namespace App\Http\Repositories;

use App\Models\User;
use App\Models\Media;
use App\Models\Communique;
use App\Models\Transmission;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
 

class CommuniqueRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Communique
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Communique::class);
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

        $req = Communique::ignoreRequest(['page'])
             ->with('media','files')
            ->orderByDesc('id');

        if ($request->has('pageSize')) {
            $per_page = $request->get('pageSize');
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
        return $this->findOrFail($id)->load('files');
    }

    /**
     * Crée une nouvelle fête.
     */
    public function makeStore($data): Communique
    {
        // Version simplifiée pour éviter les dépendances complexes
        $model = new Communique($data);
        $model->save();
        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Communique
    {
        // Version simplifiée
        $model = Communique::findOrFail($id);
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
        $query = Communique::query();
        $attrs = ['nom', 'lieu', 'type_Communique'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    // Fonctions standardisées pour le controller
    public function getById($id)
    {
        return $this->findOrFail($id)->load('files');
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

    public function generateLink($id, $data)
    {
        $code = Core::generateUniqueCode(Communique::class, 10, 'COM');
        $link_token = Str::random(40);
        $url = env('APP_FRONT_URL').'/Communique/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto');
        // $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token'] = $link_token;
        $data['code'] = $code;
        $data['lien_unique'] = $url;
        $data['qr_code'] = ''; // base64_encode($qrCode);
        $data['status'] = 1;
        $model = Communique::findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function generateMediaLink($id)
    {
        $model = Communique::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40);
        $url = env('APP_FRONT_URL').'/Communique-gallery/'.$code.'/'.$media_token;
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
        return Communique::where('link_token', $code)
                         ->orWhere('media_token', $code)
                         ->first();
    }

    public function participate($id)
    {
        // TODO: Implement proper participation logic when needed
        return true;
    }
}
