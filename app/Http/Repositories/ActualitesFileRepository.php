<?php

namespace App\Http\Repositories;

use App\Models\ActualitesFile;
use App\Models\Invite;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use App\Models\Media;
use App\Models\Parcours;
use App\Models\Transmission;
use App\Models\User;
use App\Models\Category;
use App\Models\Structures;
use App\Utilities\FileStorage;
 

class ActualitesFileRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var ActualitesFile
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(ActualitesFile::class);
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
        $all_files = ActualitesFiles::getAllActualitesFiles();

        $per_page = 10;

        $req = ActualitesFile::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            // ->with('invites')
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return [
                'files' => $req->paginate($per_page),
                'all_files' => $all_files
            ];
        } else {
            return [
                'files' => $req->get(),
                'all_files' => $all_files
            ];
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
    public function makeStore($data): ActualitesFile
    {
        $this->validate($request, [
        'type' => 'string|required',
        'nom' => 'string|required',
        'reference' => 'string|nullable',
        'actualites_id' => 'required|exists:actualites,id',
        ]);

        $data = $request->only('type', 'nom', 'reference', 'actualites_id');

        // Génération d’un nom de fichier unique
        $slug = Str::slug($request->reference);
        $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);

        // Validation du type MIME en fonction du type sélectionné
        $mime = $request->file('file')->getClientMimeType();

        if (
            ($request->type === 'image' && !in_array($mime, ['image/png', 'image/jpg', 'image/jpeg', 'image/jfif'])) ||
            ($request->type === 'video' && !in_array($mime, ['video/mp4', 'application/octet-stream'])) ||
            ($request->type === 'pdf' && $mime !== 'application/pdf')
        ) {
            return back()->with('danger', 'Le média sélectionné ne correspond pas au type de fichier choisi');
        }

        // Stockage du fichier
        $data['filename'] = FileStorage::setFile('public', $request->file('file'), 'actualites', $slug);

        // Enregistrement en base de données
        $model = new ActualitesFile($data);
        $model->save();

        return $model;

    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): ActualitesFile
    {
        $model = ActualitesFile::findOrFail($id);
        /*$aws= new AwsService();
        if(request()->file('file'))  $data['file'] = $aws->upload(request()->file('file'),"ActualitesFiles")['full_url'];*/
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
        $query = ActualitesFile::query();
        $attrs = ['nom', 'lieu', 'type_ActualitesFile'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }



}
