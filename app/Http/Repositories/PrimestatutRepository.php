<?php

namespace App\Http\Repositories;

use App\Models\Primestatut;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
 

class PrimestatutRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Primestatut
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Primestatut::class);
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

        $req = Primestatut::ignoreRequest(['per_page'])
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
    public function makeStore($data): Primestatut
    {
        $model = new Primestatut($data);
        $model->save();

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Primestatut
    {
        $model = Primestatut::findOrFail($id);
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
        $query = Primestatut::query();
        $attrs = ['nom', 'lieu', 'type_Primestatut'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Primestatut::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Primestatut/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        // TODO: Implémenter la génération de QR Code
        // $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        // $data['qr_code'] = base64_encode($qrCode);
        $data['qr_code'] = null; // Temporaire jusqu'à implémentation du QR Code
        $data['status'] = 1;
        $model = Primestatut::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Primestatut::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Primestatut-gallery/'.$code.'/'.$media_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        // TODO: Implémenter la génération de QR Code
        // $qrCodeMedia = QrCode::format('png')->size(300)->generate($url);
        $data['media_token']=$media_token;
        $data['lien_unique_media']=$url ;
        // $data['qr_code_media'] = base64_encode($qrCodeMedia);
        $data['qr_code_media'] = null; // Temporaire jusqu'à implémentation du QR Code
        $data['status'] = 2;
        $model->update($data);
        return $model;
    }

    function verifyLink($data) {
        if (isset($data['link_token'])) {
            return Primestatut::where('link_token', $data['link_token'])->first();
        } else {
            return Primestatut::where('media_token', $data['media_token'])->first();
        }
    }

    function participate($data){
        $check = Invite::where('Primestatut_id', $data['Primestatut_id'])
            ->where('phone', $data['phone'])
            ->first();
            
        if ($check) {
            $check->update($data);
            return $check;
        } else {
            $model = new Invite($data);
            $model->save();
            return $model;
        }
    }

    /**
     * Restaure un primestatut supprimé.
     */
    public function restore($id)
    {
        $model = Primestatut::withTrashed()->findOrFail($id);
        $model->restore();
        return $model;
    }

    /**
     * Supprime définitivement un primestatut.
     */
    public function forceDelete($id)
    {
        $model = Primestatut::withTrashed()->findOrFail($id);
        return $model->forceDelete();
    }

    /**
     * Suppression en masse de primestatuts.
     */
    public function massDestroy(array $ids)
    {
        return Primestatut::whereIn('id', $ids)->delete();
    }

    /**
     * Active/désactive le statut d'un primestatut.
     */
    public function toggleStatus($id)
    {
        $model = Primestatut::findOrFail($id);
        $newStatus = $model->status == 1 ? 0 : 1;
        $model->update(['status' => $newStatus]);
        return $model;
    }
}
