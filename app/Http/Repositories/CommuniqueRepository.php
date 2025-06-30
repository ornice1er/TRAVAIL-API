<?php

namespace App\Http\Repositories;

use App\Models\Communique;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
 

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
        $user = Auth::user();
        $user_id = $user->id;
        $role = $user->roles()->first()->name;

        $req = Communique::with('media')
            ->whereHas('media', function ($q) use ($user, $user_id, $role) {
                $q->where('type', 'communique');

                if ($role == 'saisie') {
                    $q->where('structure_id', $user->structure_id)
                    ->where('is_published', false)
                    ->whereHas('transmissions', function ($query) use ($user_id) {
                        $query->where('to', $user_id)->where('is_last', true);
                    });
                } elseif ($role == 'ccom') {
                    $q->where('is_archived', false)
                    ->whereHas('transmissions', function ($query) use ($user_id) {
                        $query->where('to', $user_id)->where('is_last', true);
                    });
                } elseif ($role == 'validation') {
                    $q->where('structure_id', $user->structure_id);
                } else {
                    $q->whereRaw('1=0'); // retourne vide si rôle inconnu
                }
            })
            ->ignoreRequest(['per_page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->orderByDesc('created_at');

        $per_page = 10;

        if ($request->has('per_page')) {
            $per_page = $request->get('per_page');
            return $req->paginate($per_page);
        } else {
            return $req->get();
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
    public function makeStore($data): Communique
    {
         // Création du media associé
            $media = new Media();
            $media->code = Str::uuid();
            $media->structure_id = Auth::user()->structure_id;
            $media->has_principal_access = $data['has_principal_access'] ?? false;
            $media->type = "communique";
            $media->save();

            // Génération du slug unique
            $slug = Str::slug($data['title']);
            $count = Communiques::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }

            // Compléter les données pour le modèle Communiques
            $data['slug'] = $slug;
            $data['media_id'] = $media->id;

            // Création du communiqué
            $model = new Communiques($data);
            $model->save();

            // Création du parcours
            Parcours::create([
                'media_id' => $media->id,
                'libelle' => "Création du communiqué " . $data['title']
            ]);

            // Création de la première transmission
            Transmission::create([
                'from' => Auth::id(),
                'to' => Auth::id(),
                'media_id' => $media->id,
                'is_last' => true,
            ]);

            return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Communique
    {
        // Récupération du média et du communiqué associé
            $media = Media::findOrFail($id);
            $model = $media->communique;

            // Mise à jour du média si nécessaire
            if (array_key_exists('has_principal_access', $data)) {
                $media->has_principal_access = $data['has_principal_access'];
                $media->save();
            }

            // Mise à jour du communiqué
            /*
            $aws = new AwsService();
            if (request()->file('file')) {
                $data['file'] = $aws->upload(request()->file('file'), "Communiques")['full_url'];
            }
            */

            $model->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

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

   /*
    function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Communique::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Communique/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Communique::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Communique::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Communique-gallery/'.$code.'/'.$media_token;
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
            return Communique::where('link_token', )->first();
        }else{
            return Communique::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Communique_id', $data['Communique_id'])
            ->where('phone', $data['phone'])
            ->first();
            if ($check) {
                $check->update($data);
            }else{
                $model = new Invite($data);
                $model->save();
            }
   

        return $model;

    } */


     public function up($id)
    {
            $media = Media::findOrFail($id);

            // Marquer la dernière transmission comme inactive
            $lastTransmission = $media->transmissions->last();
            if ($lastTransmission) {
                $lastTransmission->update(['is_last' => false]);
            }

            // Récupérer l'utilisateur ayant le rôle "ccom"
            $toUser = User::role('ccom')->first();
            if (!$toUser) {
                return false; // ou tu peux lever une exception si nécessaire
            }

            // Créer la nouvelle transmission
            Transmission::create([
                'from' => Auth::id(),
                'to' => $toUser->id,
                'media_id' => $id,
                'is_last' => true,
            ]);

            return true;
    }

         public function down($request, $id)
    {  
            $media = Media::findOrFail($id);

            // Met à jour le motif avec la valeur dans $request
            $media->update(['motif' => $request->motif]);

            // Marque la dernière transmission comme non active
            $lastTransmission = $media->transmissions->last();
            if ($lastTransmission) {
                $lastTransmission->update(['is_last' => false]);
            }

            // Trouve un utilisateur avec le rôle 'saisie' dans la même structure
            $toUser = User::where('structure_id', $media->structure_id)->role('saisie')->first();
            if (!$toUser) {
                return false; // ou gérer l'erreur selon besoin
            }

            // Crée une nouvelle transmission
            Transmission::create([
                'from' => Auth::id(),
                'to' => $toUser->id,
                'media_id' => $id,
                'is_last' => true,
            ]);

            return true;
    }
          
          public function publish($id)
    {
         Media::findOrFail($id)->update(['is_published' => true]);
         return true;

    }

    public function unpublish($id)
    {
        Media::findOrFail($id)->update(['is_published' => false]);
         return true;
    }

    public function archive($id)
    {
         Media::findOrFail($id)->update(['is_archived' => true]);
         return true;
    }

    public function restore($id)
    {
        Media::findOrFail($id)->update(['is_archived' => false]);
        return true;
    }

}
