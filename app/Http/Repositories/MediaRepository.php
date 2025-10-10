<?php

namespace App\Http\Repositories;

use App\Models\Media;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

 

class MediaRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Media
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Media::class);
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

        $req = Media::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->orderByDesc('created_at');

        // Si un filtre "category" est passé dans la requête, on l’applique
        if ($request->has('category') && $request->category) {
            $req = $req->where('type', $request->category);
        }

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }


    public function index2(Request $request)
{
    $per_page = 10;

    $query = Media::whereNotIn('type', ['stage', 'offre'])->orderBy('id', 'desc');

    // Filtrer par type si demandé (par exemple via ?type=communique)
    if ($request->has('type') && $request->type) {
        $query->where('type', $request->type);
    }

    if ($request->has('pageSize')) {
        $per_page = (int) $request->per_page;
        $medias = $query->paginate($per_page);
    } else {
        $medias = $query->get();
    }

    // Grouper par type uniquement si c'est une collection (pas une pagination)
    if (!$medias instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $medias = $medias->groupBy('type');
    }

    return view('admin.journal', compact('medias'));
}



public function download()
{
    $medias = Media::whereNotIn('type', ['stage', 'offre'])
        ->orderBy('id', 'desc')
        ->get()
        ->groupBy('type');

    $pdf = Pdf::loadView('pdf.journal', ['medias' => $medias])->setPaper('a4', 'landscape');

    return $pdf->download('journal.pdf');
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
    public function makeStore($data): Media
    {
        $model = new Media($data);
        /*$aws= new AwsService();
        if(request()->file('file'))  $model->file = $aws->upload(request()->file('file'),"Medias")['full_url'];*/
        $model->save();

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Media
    {
        $model = Media::findOrFail($id);
        /*$aws= new AwsService();
        if(request()->file('file'))  $data['file'] = $aws->upload(request()->file('file'),"Medias")['full_url'];*/
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
        $query = Media::query();
        $attrs = ['nom', 'lieu', 'type_Media'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Media::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Media/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Media::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Media::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Media-gallery/'.$code.'/'.$media_token;
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
            return Media::where('link_token', )->first();
        }else{
            return Media::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Media_id', $data['Media_id'])
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
