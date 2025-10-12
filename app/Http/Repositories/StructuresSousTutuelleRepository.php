<?php

namespace App\Http\Repositories;

use App\Models\StructuresSousTutelles;
use App\Models\Structures;
use App\Utilities\FileStorage;
use Illuminate\Support\Str;

class StructuresSousTutuelleRepository
{
    /**
     * Get all structures sous tutelle with optional filtering
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll($request = null)
    {
        $query = StructuresSousTutelles::with('structure');
        
        if ($request) {
            if ($request->has('name') && $request->name) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            
            if ($request->has('structure_id') && $request->structure_id) {
                $query->where('structure_id', $request->structure_id);
            }
        }
        
        return $query->get();
    }

    /**
     * Find a structure sous tutelle by ID
     *
     * @param int $id
     * @return \App\Models\StructuresSousTutelles|null
     */
    public function findById($id)
    {
        return StructuresSousTutelles::with('structure')->find($id);
    }

    /**
     * Check if structure sous tutelle exists
     *
     * @param int $id
     * @return bool
     */
    public function ifExist($id)
    {
        return StructuresSousTutelles::where('id', $id)->exists();
    }

    /**
     * Create a new structure sous tutelle with logo
     *
     * @param array $data
     * @return \App\Models\StructuresSousTutelles
     */
    public function createWithLogo($data)
    {
        if (isset($data['logo']) && $data['logo']) {
            $data['logo'] = FileStorage::setFile(
                'public',
                $data['logo'],
                'sts',
                Str::slug($data['name'])
            );
        }

        return StructuresSousTutelles::create($data);
    }

    /**
     * Update structure sous tutelle with logo management
     *
     * @param int $id
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $logoFile
     * @return bool
     */
    public function updateWithLogo($id, $data, $logoFile = null)
    {
        $structureSousTutelle = StructuresSousTutelles::find($id);
        
        if (!$structureSousTutelle) {
            return false;
        }

        if ($logoFile) {
            // Supprimer l'ancien logo s'il existe
            if ($structureSousTutelle->logo) {
                FileStorage::deleteFile('public', $structureSousTutelle->logo, 'sts');
            }
            
            // Ajouter le nouveau logo
            $data['logo'] = FileStorage::setFile(
                'public',
                $logoFile,
                'sts',
                Str::slug($data['name'])
            );
        }

        return $structureSousTutelle->fill($data)->save();
    }

    /**
     * Delete a structure sous tutelle
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $structureSousTutelle = StructuresSousTutelles::find($id);
        
        if (!$structureSousTutelle) {
            return false;
        }

        // Supprimer le logo s'il existe
        if ($structureSousTutelle->logo) {
            FileStorage::deleteFile('public', $structureSousTutelle->logo, 'sts');
        }

        return $structureSousTutelle->delete();
    }

    /**
     * Get all structures for dropdown
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStructures()
    {
        return Structures::all();
    }

    /**
     * Get structures sous tutelle by structure parent
     *
     * @param int $structureId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStructure($structureId)
    {
        return StructuresSousTutelles::where('structure_id', $structureId)
            ->with('structure')
            ->get();
    }

    /**
     * Create a new structure sous tutelle (simple version)
     *
     * @param array $data
     * @return \App\Models\StructuresSousTutelles
     */
    public function create($data)
    {
        return StructuresSousTutelles::create($data);
    }

    /**
     * Update a structure sous tutelle (simple version)
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $structureSousTutelle = StructuresSousTutelles::find($id);
        
        if (!$structureSousTutuelle) {
            return false;
        }

        return $structureSousTutelle->fill($data)->save();
    }
}