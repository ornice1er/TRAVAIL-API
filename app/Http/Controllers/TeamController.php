<?php

namespace App\Http\Controllers;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
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
            return Common::success( 'Membres de l\'équipe récupérés avec succès',$teams);
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
   /** @OA\Post(
     *      path="/agents",
     *      operationId="Actualite store",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Store Actualite data",
     *      description="Create a new Actualite",
     *
     *       @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/ActualiteCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=419,
     *          description="Expired session"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function store(StoreTeamRequest $request)
    {
        $message = 'Enregistrement d\'un Actualite';

        try {
            $result = $this->repository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Actualite créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/agents/{id}",
     *      operationId="Actualite update",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Update one Actualite data",
     *      description="Update Actualite by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Actualite ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/ActualiteCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=419,
     *          description="Expired session"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function update(UpdateTeamRequest $request, $id)
    {
        $message = 'Mise à jour d\'un Actualite';

        try {
            $result = $this->repository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour de Actualite effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
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
