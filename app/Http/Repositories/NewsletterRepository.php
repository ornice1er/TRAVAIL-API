<?php

namespace App\Http\Repositories;

use App\Models\Newsletter;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
 

class NewsletterRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Newsletter
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Newsletter::class);
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

        $req = Newsletter::ignoreRequest(['per_page'])
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
    public function makeStore($data): Newsletter
    {
        $model = new Newsletter($data);
        /*$aws= new AwsService();
        if(request()->file('file'))  $model->file = $aws->upload(request()->file('file'),"Newsletters")['full_url'];*/
        $model->save();

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Newsletter
    {
        $model = Newsletter::findOrFail($id);
        /*$aws= new AwsService();
        if(request()->file('file'))  $data['file'] = $aws->upload(request()->file('file'),"Newsletters")['full_url'];*/
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
        $query = Newsletter::query();
        $attrs = ['nom', 'lieu', 'type_Newsletter'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Newsletter::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Newsletter/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Newsletter::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Newsletter::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Newsletter-gallery/'.$code.'/'.$media_token;
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
            return Newsletter::where('link_token', )->first();
        }else{
            return Newsletter::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Newsletter_id', $data['Newsletter_id'])
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
    } */

public function subscribe()
{
    // Récupérer l'email depuis la requête globale
    $email = request()->input('email');

    $type = TypesStructure::where('is_parent', true)->first();
    $structure = Structures::where('type_structure_id', $type->id)->first();

    if (!Newsletters::isSubscribed($email)) {
        $status = Newsletters::create([
            "titre" => $email,
            "structure_id" => $structure->id
        ]);

        if ($status) {
            Mail::send("email.newsletter", [], function ($message) use ($email) {
                $message->from(env('MAIL_FROM_ADDRESS'), env("MAIL_FROM_NAME"))
                    ->subject("Newsletter MTFP");
                $message->to($email, "Abonné MTFP");
            });

            request()->session()->flash('success', 'Inscrit! Veuillez consulter vos mails.');
            return back();
        } else {
            Newsletters::getLastError();
            return back()->with('error', "Quelque chose s'est passé! veuillez réessayez.");
        }
    } else {
        request()->session()->flash('error', 'Déja Inscrit !');
        return back();
    }
}


}
