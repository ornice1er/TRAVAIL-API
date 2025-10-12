<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PrestationRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class PrestationController
{
    /**
     * The Prestation repository being queried.
     *
     * @var PrestationRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(PrestationRepository $prestationRepository, LogService $ls)
    {
        $this->repository = $prestationRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/prestations",
     *     tags={"Prestation"},
     *     summary="Récupérer toutes les prestations",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par nom"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestations récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Prestation")),
     *             @OA\Property(property="message", type="string", example="Prestations récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération des prestations';
        
        try {
            $prestations = $this->repository->getAll($request);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestations récupérées avec succès']);
            return Common::success($prestations, 'Prestations récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des prestations', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/prestations/by-role",
     *     tags={"Prestation"},
     *     summary="Récupérer les prestations par rôle et structure",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Prestations récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Media")),
     *             @OA\Property(property="message", type="string", example="Prestations récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getByRole()
    {
        $message = 'Récupération des prestations par rôle';
        
        try {
            $user = Auth::user();
            $role = $user->roles()->first()->name;
            $structureId = $user->structure_id;
            $userId = $user->id;
            
            $medias = $this->repository->getByRoleAndStructure($structureId, $role, $userId);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestations récupérées par rôle avec succès']);
            return Common::success($medias, 'Prestations récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des prestations', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/prestations/{id}",
     *     tags={"Prestation"},
     *     summary="Récupérer une prestation par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Prestation"),
     *             @OA\Property(property="message", type="string", example="Prestation récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Prestation non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération de la prestation';
        
        try {
            $prestation = $this->repository->findById($id);
            
            if (!$prestation) {
                return Common::error('Prestation non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation récupérée avec succès']);
            return Common::success($prestation, 'Prestation récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/with-workflow",
     *     tags={"Prestation"},
     *     summary="Créer une prestation avec workflow complet",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Prestation exemple"),
     *                 @OA\Property(property="link", type="string", example="https://example.com"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="has_principal_access", type="boolean", example=true),
     *                 @OA\Property(property="image", type="string", format="binary", description="Fichier image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Prestation créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Prestation"),
     *             @OA\Property(property="message", type="string", example="Prestation créée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function createWithWorkflow(Request $request)
    {
        $message = 'Création de prestation avec workflow';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'link' => 'string|required',
                'status' => 'string|required',
            ]);

            $data = $request->all();
            if ($request->file('image')) {
                $data['image'] = $request->file('image');
            }
            
            $prestation = $this->repository->createWithWorkflow($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation créée avec workflow avec succès']);
            return Common::success($prestation, 'Prestation créée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création de la prestation', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/prestations/{id}/with-file",
     *     tags={"Prestation"},
     *     summary="Mettre à jour une prestation avec gestion de fichier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Prestation modifiée"),
     *                 @OA\Property(property="link", type="string", example="https://new-example.com"),
     *                 @OA\Property(property="status", type="string", example="inactive"),
     *                 @OA\Property(property="has_principal_access", type="boolean", example=false),
     *                 @OA\Property(property="image", type="string", format="binary", description="Nouveau fichier image (optionnel)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation mise à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Prestation non trouvée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function updateWithFile(Request $request, $id)
    {
        $message = 'Mise à jour de prestation avec fichier';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'link' => 'string|required',
                'status' => 'string|required',
            ]);

            $data = $request->all();
            $file = $request->file('image');
            
            $result = $this->repository->updateWithFile($id, $data, $file);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation mise à jour avec succès']);
            return Common::success($result, 'Prestation mise à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de la prestation', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/prestations/{id}",
     *     tags={"Prestation"},
     *     summary="Supprimer une prestation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Prestation non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de prestation';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Prestation non trouvée', []);
            }
            
            $result = $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation supprimée avec succès']);
            return Common::success($result, 'Prestation supprimée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/{id}/move-up",
     *     tags={"Prestation"},
     *     summary="Faire remonter une prestation vers validation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation remontée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation remontée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function moveUp($id)
    {
        $message = 'Remontée de prestation';
        
        try {
            $result = $this->repository->moveUp($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation remontée avec succès']);
            return Common::success($result, 'Prestation remontée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la remontée de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/{id}/move-down",
     *     tags={"Prestation"},
     *     summary="Faire redescendre une prestation vers saisie",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="motif", type="string", example="Corrections nécessaires")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation redescendue avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation redescendue avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function moveDown(Request $request, $id)
    {
        $message = 'Redescente de prestation';
        
        try {
            $motif = $request->input('motif');
            $result = $this->repository->moveDown($id, $motif);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation redescendue avec succès']);
            return Common::success($result, 'Prestation redescendue avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la redescente de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/{id}/publish",
     *     tags={"Prestation"},
     *     summary="Publier une prestation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation publiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation publiée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function publish($id)
    {
        $message = 'Publication de prestation';
        
        try {
            $result = $this->repository->publish($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation publiée avec succès']);
            return Common::success($result, 'Prestation publiée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la publication de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/{id}/unpublish",
     *     tags={"Prestation"},
     *     summary="Dépublier une prestation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation dépubliée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation dépubliée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function unpublish($id)
    {
        $message = 'Dépublication de prestation';
        
        try {
            $result = $this->repository->unpublish($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation dépubliée avec succès']);
            return Common::success($result, 'Prestation dépubliée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la dépublication de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/{id}/archive",
     *     tags={"Prestation"},
     *     summary="Archiver une prestation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation archivée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation archivée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function archive($id)
    {
        $message = 'Archivage de prestation';
        
        try {
            $result = $this->repository->archive($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation archivée avec succès']);
            return Common::success($result, 'Prestation archivée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de l\'archivage de la prestation', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prestations/{id}/restore",
     *     tags={"Prestation"},
     *     summary="Restaurer une prestation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la prestation"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prestation restaurée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prestation restaurée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function restore($id)
    {
        $message = 'Restauration de prestation';
        
        try {
            $result = $this->repository->restore($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Prestation restaurée avec succès']);
            return Common::success($result, 'Prestation restaurée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la restauration de la prestation', []);
        }
    }
}
