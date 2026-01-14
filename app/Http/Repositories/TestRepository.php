<?php

namespace App\Http\Repositories;

use App\Models\User;
use App\Models\Media;
use App\Models\Test;
use App\Models\Transmission;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Parcours;
 

class TestRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Test
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Test::class);
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

        $req = Test::ignoreRequest(['page'])
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

 public function makeStore(array $data): Test
    {
        // Création du média
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = 'test';

        $media->save();

        // Génération du slug unique
        $slug = Str::slug($data['title']);
        $count = Test::where('slug', $slug)->count();

        if ($count > 0) {
            $slug .= '-' . now()->format('ymdis') . '-' . rand(0, 999);
        }

        // Création du concours
        $test = new Test();
        $test->fill($data);
        $test->slug = $slug;
        $test->communiques=$data['communiques'];
        $test->media_id = $media->id;
        $test->save();

        // Parcours
        Parcours::create([
            'media_id' => $media->id,
            'libelle'  => 'Création du concours ' . ($data['title'] ?? '')
        ]);

        // Transmission
        Transmission::create([
            'from'     => Auth::id(),
            'to'       => Auth::id(),
            'media_id' => $media->id,
            'is_last'  => true,
        ]);

        return $test;
    }
    /**
     * Met à jour une fête.
     */
   public function makeUpdate(int $mediaId, array $data): Test
    {

        // Récupération du média
        $media = Media::findOrFail($mediaId);

        // Récupération du concours lié
        $test = $media->test;

        // Mise à jour du média
        if (array_key_exists('has_principal_access', $data)) {
            $media->has_principal_access = $data['has_principal_access'];
            $media->save();
        }

        // Mise à jour du concours
        $test->fill([
            'title' => $data['title'] ?? $test->title,
            'description' => $data['description'] ?? $test->description,
            'communiques' => $data['communiques'] ?? $test->communiques,
        ]);

        $test->save();

        return $test;
    }

    /**
     * Supprime une fête.
     */
    public function makeDestroy($id)
    {
        $data=$this->findOrFail($id);
        $data->media?->delete();
        return $data->delete();
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
        $query = Test::query();
        $attrs = ['nom', 'lieu', 'type_Test'];
        
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
        $code = Core::generateUniqueCode(Test::class, 10, 'COM');
        $link_token = Str::random(40);
        $url = env('APP_FRONT_URL').'/Test/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto');
        // $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token'] = $link_token;
        $data['code'] = $code;
        $data['lien_unique'] = $url;
        $data['qr_code'] = ''; // base64_encode($qrCode);
        $data['status'] = 1;
        $model = Test::findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function generateMediaLink($id)
    {
        $model = Test::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40);
        $url = env('APP_FRONT_URL').'/Test-gallery/'.$code.'/'.$media_token;
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
        return Test::where('link_token', $code)
                         ->orWhere('media_token', $code)
                         ->first();
    }

    public function participate($id)
    {
        // TODO: Implement proper participation logic when needed
        return true;
    }
}
