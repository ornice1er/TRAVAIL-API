<?php

namespace App\Http\Repositories;

use App\Models\TypesStructures;

class TypesStructuresRepository
{
    /**
     * Get all types structures with hierarchical structure
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll($request = null)
    {
        return TypesStructures::getAllTypes();
    }

    /**
     * Get all types structures (simple version)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTypesStructures()
    {
        return TypesStructures::getAllTypes();
    }

    /**
     * Find a type structure by ID
     *
     * @param int $id
     * @return \App\Models\TypesStructures|null
     */
    public function findById($id)
    {
        return TypesStructures::find($id);
    }

    /**
     * Check if type structure exists
     *
     * @param int $id
     * @return bool
     */
    public function ifExist($id)
    {
        return TypesStructures::where('id', $id)->exists();
    }

    /**
     * Create a new type structure
     *
     * @param array $data
     * @return \App\Models\TypesStructures
     */
    public function create($data)
    {
        return TypesStructures::create($data);
    }

    /**
     * Update a type structure
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $typeStructure = TypesStructures::find($id);
        
        if (!$typeStructure) {
            return false;
        }

        return $typeStructure->fill($data)->save();
    }

    /**
     * Delete a type structure
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $typeStructure = TypesStructures::find($id);
        
        if (!$typeStructure) {
            return false;
        }

        // Vérifier s'il y a des structures liées
        if ($typeStructure->structures()->count() > 0) {
            return false; // Ne pas supprimer s'il y a des structures liées
        }

        return $typeStructure->delete();
    }

    /**
     * Get parent types structures
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentTypes()
    {
        return TypesStructures::where('is_parent', true)->get();
    }

    /**
     * Get child types structures by parent
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChildrenByParent($parentId)
    {
        return TypesStructures::where('parent_id', $parentId)->get();
    }

    /**
     * Get types structures with their children
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithChildren()
    {
        return TypesStructures::with('children')->whereNull('parent_id')->get();
    }

    /**
     * Check if type structure can be deleted
     *
     * @param int $id
     * @return bool
     */
    public function canBeDeleted($id)
    {
        $typeStructure = TypesStructures::find($id);
        
        if (!$typeStructure) {
            return false;
        }

        // Vérifier s'il y a des structures liées
        if ($typeStructure->structures()->count() > 0) {
            return false;
        }

        // Vérifier s'il y a des enfants
        if ($typeStructure->children()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Get types structures for dropdown/select
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDropdown()
    {
        return TypesStructures::select('id', 'title')->get();
    }

    /**
     * Search types structures by title
     *
     * @param string $title
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchByTitle($title)
    {
        return TypesStructures::where('title', 'like', '%' . $title . '%')->get();
    }
}