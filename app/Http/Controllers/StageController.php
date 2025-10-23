<?php

namespace App\Http\Controllers;

use App\Http\Repositories\StageRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class StageController
{
    /**
     * The Stage repository being queried.
     *
     * @var StageRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(StageRepository $stageRepository, LogService $ls)
    {
        $this->repository = $stageRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/stages",
     *     tags={"Stage"},
     *     summary="Récupérer tous les stages",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par nom"
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par pays"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par statut"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stages récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Stage")),
     *             @OA\Property(property="message", type="string", example="Stages récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération des stages';
        
        try {
            $stages = $this->repository->getAll($request);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stages récupérés avec succès']);
            return Common::success($stages, 'Stages récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des stages', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/stages/by-role",
     *     tags={"Stage"},
     *     summary="Récupérer les stages par rôle et structure",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Stages récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Media")),
     *             @OA\Property(property="message", type="string", example="Stages récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getByRole()
    {
        $message = 'Récupération des stages par rôle';
        
        try {
            $user = Auth::user();
            $role = $user->roles()->first()->name;
            $structureId = $user->structure_id;
            $userId = $user->id;
            
            $medias = $this->repository->getByRoleAndStructure($structureId, $role, $userId);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stages récupérés par rôle avec succès']);
            return Common::success($medias, 'Stages récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des stages', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/stages/{id}",
     *     tags={"Stage"},
     *     summary="Récupérer un stage par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Stage"),
     *             @OA\Property(property="message", type="string", example="Stage récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Stage non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération du stage';
        
        try {
            $stage = $this->repository->findById($id);
            
            if (!$stage) {
                return Common::error('Stage non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage récupéré avec succès']);
            return Common::success($stage, 'Stage récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/with-workflow",
     *     tags={"Stage"},
     *     summary="Créer un stage avec workflow complet",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Stage développement web"),
     *             @OA\Property(property="resume", type="string", example="Description du stage"),
     *             @OA\Property(property="period_start", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="period_end", type="string", format="date", example="2024-06-15"),
     *             @OA\Property(property="closing_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="structure", type="string", example="Direction IT"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="year", type="string", example="2024"),
     *             @OA\Property(property="country", type="string", example="France"),
     *             @OA\Property(property="city", type="string", example="Paris"),
     *             @OA\Property(property="has_principal_access", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Stage créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Stage"),
     *             @OA\Property(property="message", type="string", example="Stage créé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function createWithWorkflow(Request $request)
    {
        $message = 'Création de stage avec workflow';
        
        try {
            $data = $request->all();
            
            $stage = $this->repository->createWithWorkflow($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage créé avec workflow avec succès']);
            return Common::success($stage, 'Stage créé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création du stage', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/stages/{id}/with-dates",
     *     tags={"Stage"},
     *     summary="Mettre à jour un stage avec gestion des dates",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Stage modifié"),
     *             @OA\Property(property="resume", type="string", example="Nouvelle description"),
     *             @OA\Property(property="period_start", type="string", format="date", example="2024-02-01"),
     *             @OA\Property(property="period_end", type="string", format="date", example="2024-07-01"),
     *             @OA\Property(property="closing_date", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="structure", type="string", example="Direction RH"),
     *             @OA\Property(property="status", type="string", example="inactive"),
     *             @OA\Property(property="year", type="string", example="2024"),
     *             @OA\Property(property="country", type="string", example="Belgique"),
     *             @OA\Property(property="city", type="string", example="Bruxelles"),
     *             @OA\Property(property="has_principal_access", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Stage non trouvé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function updateWithDates(Request $request, $id)
    {
        $message = 'Mise à jour de stage avec dates';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'status' => 'string|required',
            ]);

            $data = $request->all();
            
            $result = $this->repository->updateWithDates($id, $data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage mis à jour avec succès']);
            return Common::success($result, 'Stage mis à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour du stage', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/stages/{id}",
     *     tags={"Stage"},
     *     summary="Supprimer un stage",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Stage non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de stage';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Stage non trouvé', []);
            }
            
            $result = $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage supprimé avec succès']);
            return Common::success($result, 'Stage supprimé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/{id}/move-up",
     *     tags={"Stage"},
     *     summary="Faire remonter un stage vers validation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage remonté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage remonté avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function moveUp($id)
    {
        $message = 'Remontée de stage';
        
        try {
            $result = $this->repository->moveUp($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage remonté avec succès']);
            return Common::success($result, 'Stage remonté avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la remontée du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/{id}/move-down",
     *     tags={"Stage"},
     *     summary="Faire redescendre un stage vers saisie",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="motif", type="string", example="Informations manquantes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage redescendu avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage redescendu avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function moveDown(Request $request, $id)
    {
        $message = 'Redescente de stage';
        
        try {
            $motif = $request->input('motif');
            $result = $this->repository->moveDown($id, $motif);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage redescendu avec succès']);
            return Common::success($result, 'Stage redescendu avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la redescente du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/{id}/publish",
     *     tags={"Stage"},
     *     summary="Publier un stage",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage publié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage publié avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function publish($id)
    {
        $message = 'Publication de stage';
        
        try {
            $result = $this->repository->publish($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage publié avec succès']);
            return Common::success($result, 'Stage publié avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la publication du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/{id}/unpublish",
     *     tags={"Stage"},
     *     summary="Dépublier un stage",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage dépublié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage dépublié avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function unpublish($id)
    {
        $message = 'Dépublication de stage';
        
        try {
            $result = $this->repository->unpublish($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage dépublié avec succès']);
            return Common::success($result, 'Stage dépublié avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la dépublication du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/{id}/archive",
     *     tags={"Stage"},
     *     summary="Archiver un stage",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage archivé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage archivé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function archive($id)
    {
        $message = 'Archivage de stage';
        
        try {
            $result = $this->repository->archive($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage archivé avec succès']);
            return Common::success($result, 'Stage archivé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de l\'archivage du stage', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stages/{id}/restore",
     *     tags={"Stage"},
     *     summary="Restaurer un stage",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du stage"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage restauré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stage restauré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function restore($id)
    {
        $message = 'Restauration de stage';
        
        try {
            $result = $this->repository->restore($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Stage restauré avec succès']);
            return Common::success($result, 'Stage restauré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la restauration du stage', []);
        }
    }
}
