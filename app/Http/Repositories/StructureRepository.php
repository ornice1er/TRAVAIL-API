<?php

namespace App\Http\Repositories;

use App\Models\Structure;
use App\Models\TypeStructure;
use App\Traits\Repository;
use App\Utilities\FileStorage;
use Illuminate\Support\Str;

class StructureRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Structure
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Structure::class);
    }

    /**
     * Récupère toutes les structures.
     */
    public function all()
    {
        return Structure::with(['typeStructure'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère une structure par ID.
     */
    public function findById($id)
    {
        return Structure::with(['typeStructure'])->find($id);
    }

    /**
     * Crée une nouvelle structure.
     */
    public function create($data)
    {
        return Structure::create($data);
    }

    /**
     * Met à jour une structure.
     */
    public function update($id, $data)
    {
        $structure = Structure::findOrFail($id);
        return $structure->update($data);
    }

    /**
     * Supprime une structure.
     */
    public function delete($id)
    {
        $structure = Structure::findOrFail($id);
        
        // Vérifier s'il y a des médias associés
        if ($structure->medias && $structure->medias->count() > 0) {
            return false; // Impossible de supprimer
        }
        
        return $structure->delete();
    }

    /**
     * Crée une structure avec gestion des fichiers.
     */
    public function createWithFiles($data)
    {
        $slug = Str::slug($data['name']);
        $processedData = $data;
        
        // Traitement de la photo de structure
        if (isset($data['photo']) && $data['photo']) {
            $processedData['photo'] = FileStorage::setFile('public', $data['photo'], 'structures', $slug);
        }
        
        // Traitement de la photo du responsable
        if (isset($data['photo_responsable']) && $data['photo_responsable']) {
            $processedData['photo_responsable'] = FileStorage::setFile(
                'public', 
                $data['photo_responsable'], 
                'structures/respos', 
                time() . Str::slug($data['name_responsable'])
            );
        }
        
        // Traitement du fichier vision
        if (isset($data['vision_file']) && $data['vision_file']) {
            $processedData['vision_file'] = FileStorage::setFile(
                'public', 
                $data['vision_file'], 
                'structures/visions', 
                Str::slug($data['name_responsable'])
            );
        }
        
        $processedData['slug'] = $slug;
        
        return Structure::create($processedData);
    }

    /**
     * Met à jour une structure avec gestion des fichiers.
     */
    public function updateWithFiles($id, $data, $files = [])
    {
        $structure = Structure::findOrFail($id);
        $slug = Str::slug($data['name']);
        $processedData = $data;
        
        // Traitement de la photo de structure
        if (isset($files['photo']) && $files['photo']) {
            FileStorage::deleteFile('public', $structure->photo, 'structures');
            $processedData['photo'] = FileStorage::setFile('public', $files['photo'], 'structures', $slug);
        }
        
        // Traitement de la photo du responsable
        if (isset($files['photo_responsable']) && $files['photo_responsable']) {
            FileStorage::deleteFile('public', $structure->photo_responsable, 'structures/respos');
            $processedData['photo_responsable'] = FileStorage::setFile(
                'public', 
                $files['photo_responsable'], 
                'structures/respos', 
                time() . Str::slug($data['name_responsable'])
            );
        }
        
        // Traitement du fichier vision
        if (isset($files['vision_file']) && $files['vision_file']) {
            FileStorage::deleteFile('public', $structure->vision_file, 'structures/visions');
            $processedData['vision_file'] = FileStorage::setFile(
                'public', 
                $files['vision_file'], 
                'structures/visions', 
                Str::slug($data['name_responsable'])
            );
        }
        
        $processedData['slug'] = $slug;
        
        return $structure->update($processedData);
    }

    /**
     * Récupère la biographie du ministre.
     */
    public function getMinistreBiographie()
    {
        $type = TypeStructure::where('is_parent', true)->first();
        $structure = Structure::where('type_structure_id', $type->id)->first();
        
        return $structure;
    }

    /**
     * Met à jour la biographie d'une structure.
     */
    public function updateBiographie($id, $biographie)
    {
        return Structure::find($id)->update(['biographie_responsable' => $biographie]);
    }

    /**
     * Récupère tous les types de structures.
     */
    public function getAllTypeStructures()
    {
        return TypeStructure::all();
    }

    /**
     * Vérifie si une structure existe.
     */
    public function ifExist($id)
    {
        return Structure::where('id', $id)->exists();
    }

    /**
     * Récupère toutes les structures avec filtres.
     */
    public function getAll($request)
    {
        $query = Structure::with(['typeStructure']);
        
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->has('type_structure_id')) {
            $query->where('type_structure_id', $request->type_structure_id);
        }
        
        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Récupère une structure par ID.
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée une structure.
     */
    public function store($data)
    {
        return $this->create($data);
    }

    /**
     * Supprime une structure.
     */
    public function destroy($id)
    {
        return $this->delete($id);
    }

    /**
     * Récupère une structure.
     */
    public function get($id)
    {
        return $this->findById($id);
    }

    /**
     * Crée une structure.
     */
    public function makeStore($data): Structure
    {
        return $this->create($data);
    }

    /**
     * Met à jour une structure.
     */
    public function makeUpdate($id, $data): Structure
    {
        $this->update($id, $data);
        return $this->findById($id);
    }

    /**
     * Supprime une structure.
     */
    public function makeDestroy($id)
    {
        return $this->delete($id);
    }
}