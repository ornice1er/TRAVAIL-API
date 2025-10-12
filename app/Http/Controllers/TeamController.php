<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TeamRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TeamController
{
    /**
     * The Team repository being queried.
     *
     * @var TeamRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(TeamRepository $teamRepository, LogService $ls)
    {
        $this->repository = $teamRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/teams",
     *     tags={"Team"},
     *     summary="Récupérer tous les membres de l'équipe",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par nom"
     *     ),
     *     @OA\Parameter(
     *         name="office",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par poste"
     *     ),
     *     @OA\Parameter(
     *         name="structure_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filtrer par structure"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membres de l'équipe récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Team")),
     *             @OA\Property(property="message", type="string", example="Membres de l'équipe récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération des membres de l\'équipe';
        
        try {
            $teams = $this->repository->getAll($request);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membres de l\'équipe récupérés avec succès']);
            return Common::success($teams, 'Membres de l\'équipe récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des membres de l\'équipe', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/teams/structures",
     *     tags={"Team"},
     *     summary="Récupérer toutes les structures",
     *     @OA\Response(
     *         response=200,
     *         description="Structures récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Structure")),
     *             @OA\Property(property="message", type="string", example="Structures récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getStructures()
    {
        $message = 'Récupération des structures';
        
        try {
            $structures = $this->repository->getAllStructures();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structures récupérées avec succès']);
            return Common::success($structures, 'Structures récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des structures', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/teams/{id}",
     *     tags={"Team"},
     *     summary="Récupérer un membre de l'équipe par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du membre de l'équipe"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membre de l'équipe récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Team"),
     *             @OA\Property(property="message", type="string", example="Membre de l'équipe récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Membre de l'équipe non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération du membre de l\'équipe';
        
        try {
            $team = $this->repository->findById($id);
            
            if (!$team) {
                return Common::error('Membre de l\'équipe non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membre de l\'équipe récupéré avec succès']);
            return Common::success($team, 'Membre de l\'équipe récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération du membre de l\'équipe', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/teams/with-photo",
     *     tags={"Team"},
     *     summary="Créer un membre de l'équipe avec photo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="office", type="string", example="Directeur"),
     *                 @OA\Property(property="structure_id", type="string", example="1"),
     *                 @OA\Property(property="bio", type="string", example="Biographie du membre"),
     *                 @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+33123456789"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Photo du membre")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Membre de l'équipe créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Team"),
     *             @OA\Property(property="message", type="string", example="Membre de l'équipe créé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function createWithPhoto(Request $request)
    {
        $message = 'Création de membre d\'équipe avec photo';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'office' => 'string|required',
                'structure_id' => 'string|required',
                'bio' => 'string|nullable',
                'email' => 'email|nullable',
                'phone' => 'string|nullable',
            ], [
                'name.required' => 'Le nom est requis',
                'office.required' => 'Le poste est requis',
                'structure_id.required' => 'La structure est requise',
                'email.email' => 'Un email valide est requis',
            ]);

            $data = $request->all();
            
            // Ajout de la photo
            if ($request->file('photo')) {
                $data['photo'] = $request->file('photo');
            }
            
            $team = $this->repository->createWithPhoto($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membre d\'équipe créé avec succès']);
            return Common::success($team, 'Membre de l\'équipe créé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création du membre de l\'équipe', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/teams/{id}/with-photo",
     *     tags={"Team"},
     *     summary="Mettre à jour un membre de l'équipe avec photo",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du membre de l'équipe"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Marie Martin"),
     *                 @OA\Property(property="office", type="string", example="Directrice adjointe"),
     *                 @OA\Property(property="structure_id", type="string", example="2"),
     *                 @OA\Property(property="bio", type="string", example="Nouvelle biographie"),
     *                 @OA\Property(property="email", type="string", format="email", example="marie@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+33987654321"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Nouvelle photo (optionnel)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membre de l'équipe mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Membre de l'équipe mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Membre de l'équipe non trouvé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function updateWithPhoto(Request $request, $id)
    {
        $message = 'Mise à jour de membre d\'équipe avec photo';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'office' => 'string|required',
                'structure_id' => 'string|required',
                'bio' => 'string|nullable',
                'email' => 'email|nullable',
                'phone' => 'string|nullable',
            ], [
                'name.required' => 'Le nom est requis',
                'office.required' => 'Le poste est requis',
                'structure_id.required' => 'La structure est requise',
                'email.email' => 'Un email valide est requis',
            ]);

            $data = $request->all();
            $photoFile = $request->file('photo');
            
            $result = $this->repository->updateWithPhoto($id, $data, $photoFile);
            
            if (!$result) {
                return Common::error('Membre de l\'équipe non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membre d\'équipe mis à jour avec succès']);
            return Common::success($result, 'Membre de l\'équipe mis à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour du membre de l\'équipe', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/teams/{id}",
     *     tags={"Team"},
     *     summary="Supprimer un membre de l'équipe",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du membre de l'équipe"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membre de l'équipe supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Membre de l'équipe supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Membre de l'équipe non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de membre d\'équipe';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Membre de l\'équipe non trouvé', []);
            }
            
            $result = $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membre d\'équipe supprimé avec succès']);
            return Common::success($result, 'Membre de l\'équipe supprimé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression du membre de l\'équipe', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/teams/by-structure/{structureId}",
     *     tags={"Team"},
     *     summary="Récupérer les membres de l'équipe par structure",
     *     @OA\Parameter(
     *         name="structureId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membres de l'équipe récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Team")),
     *             @OA\Property(property="message", type="string", example="Membres de l'équipe récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getByStructure($structureId)
    {
        $message = 'Récupération des membres par structure';
        
        try {
            $teams = $this->repository->getByStructure($structureId);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membres de l\'équipe récupérés avec succès']);
            return Common::success($teams, 'Membres de l\'équipe récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des membres de l\'équipe', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/teams/by-office/{office}",
     *     tags={"Team"},
     *     summary="Récupérer les membres de l'équipe par poste",
     *     @OA\Parameter(
     *         name="office",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Poste recherché"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Membres de l'équipe récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Team")),
     *             @OA\Property(property="message", type="string", example="Membres de l'équipe récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getByOffice($office)
    {
        $message = 'Récupération des membres par poste';
        
        try {
            $teams = $this->repository->getByOffice($office);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Membres de l\'équipe récupérés avec succès']);
            return Common::success($teams, 'Membres de l\'équipe récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des membres de l\'équipe', []);
        }
    }
}
