<?php

namespace App\Http\Repositories;

use App\Models\Team;
use App\Models\Structure;
use App\Utilities\FileStorage;

class TeamRepository
{
    /**
     * Get all teams with optional filtering
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll($request = null)
    {

        $req = Team::ignoreRequest(['per_page','pageSize','page'])
        ->with('structure');

        if (array_key_exists('pageSize', $request->all())) {
            $per_page = $request['pageSize'];
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }

    /**
     * Find a team member by ID
     *
     * @param int $id
     * @return \App\Models\Team|null
     */
    public function findById($id)
    {
        return Team::with('structure')->find($id);
    }

    /**
     * Check if team member exists
     *
     * @param int $id
     * @return bool
     */
    public function ifExist($id)
    {
        return Team::where('id', $id)->exists();
    }

    /**
     * Create a new team member with photo
     *
     * @param array $data
     * @return \App\Models\Team
     */
    public function makeStore($data)
    {
        if (isset($data['photo']) && $data['photo']) {
            $data['photo'] = FileStorage::setFile(
                'public',
                $data['photo'],
                'teams',
                time() . $data['name']
            );
        }

        return Team::create($data);
    }

    /**
     * Update team member with photo management
     *
     * @param int $id
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $photoFile
     * @return bool
     */
    public function makeUpdate($id, $data)
    {
        $team = Team::find($id);
        
        if (!$team) {
            return false;
        }

        $photoFile= request()->file('photo');

        if ($photoFile) {
            // Supprimer l'ancienne photo s'il existe
            if ($team->photo) {
                FileStorage::deleteFile('public', $team->photo, 'teams');
            }
            
            // Ajouter la nouvelle photo
            $data['photo'] = FileStorage::setFile(
                'public',
                $photoFile,
                'teams',
                time() . $data['name']
            );
        }

        return $team->fill($data)->save();
    }

    /**
     * Delete a team member
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $team = Team::find($id);
        
        if (!$team) {
            return false;
        }

        // Supprimer la photo s'il existe
        if ($team->photo) {
            FileStorage::deleteFile('public', $team->photo, 'teams');
        }

        return $team->delete();
    }

    /**
     * Get all structures for dropdown
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStructures()
    {
        return Structure::all();
    }

    /**
     * Get team members by structure
     *
     * @param int $structureId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStructure($structureId)
    {
        return Team::where('structure_id', $structureId)
            ->with('structure')
            ->get();
    }

    /**
     * Create a new team member (simple version)
     *
     * @param array $data
     * @return \App\Models\Team
     */
    public function create($data)
    {
        return Team::create($data);
    }

    /**
     * Update a team member (simple version)
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $team = Team::find($id);
        
        if (!$team) {
            return false;
        }

        return $team->fill($data)->save();
    }

    /**
     * Get team members by office
     *
     * @param string $office
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByOffice($office)
    {
        return Team::where('office', 'like', '%' . $office . '%')
            ->with('structure')
            ->get();
    }
}