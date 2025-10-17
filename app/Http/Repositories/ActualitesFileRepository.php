<?php

namespace App\Http\Repositories;

use App\Models\ActualitesFiles;
use App\Traits\Repository;
use Illuminate\Support\Str;
use App\Utilities\FileStorage;

class ActualitesFileRepository
{
    use Repository;

    protected $model;

    public function __construct()
    {
        $this->model = app(ActualitesFiles::class);
    }

    public function ifExist($id)
    {
        return $this->find($id);
    }

    public function getAll($request)
    {
        $per_page = 10;

        $req = ActualitesFiles::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            // ->with('invites')
            ->orderByDesc('created_at');

        if ($request->has('per_page')) {
            $per_page = $request->input('per_page');
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }

    public function get($id)
    {
        return $this->findOrFail($id);
    }

    public function makeStore($data): ActualitesFiles
    {

        $slug = Str::slug($data['reference'] ?? 'actualite-file');
        $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);

        if (isset($data['file'])) {
            $mime = $data['file']->getClientMimeType();

            if (
                ($data['type'] === 'image' && !in_array($mime, ['image/png', 'image/jpg', 'image/jpeg', 'image/jfif'])) ||
                ($data['type'] === 'video' && !in_array($mime, ['video/mp4', 'application/octet-stream'])) ||
                ($data['type'] === 'pdf' && $mime !== 'application/pdf')
            ) {
                throw new \InvalidArgumentException('Le média sélectionné ne correspond pas au type de fichier choisi');
            }

            $data['filename'] = FileStorage::setFile('public', $data['file'], 'actualites', $slug);
            unset($data['file']);
        }

        $model = new ActualitesFiles($data);
        $model->save();

        return $model;
    }

    public function makeUpdate($id, $data): ActualitesFiles
    {
        $model = ActualitesFiles::findOrFail($id);
        $model->update($data);

        return $model;
    }

    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    public function getlatest()
    {
        return $this->latest()->get();
    }

    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['status' => $status]);
    }

    public function search($term)
    {
        $query = ActualitesFiles::query();
        $attrs = ['nom', 'type'];

        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }
}