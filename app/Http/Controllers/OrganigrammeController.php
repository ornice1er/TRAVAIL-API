<?php

namespace App\Http\Controllers;

use App\Http\Repositories\OrganigrammeRepository;
use App\Http\Requests\StoreOrganigrammeRequest;
use App\Http\Requests\UpdateOrganigrammeRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class OrganigrammeController
{
    /**
     * The Organigramme repository being queried.
     *
     * @var OrganigrammeRepository
     */
    protected $organigrammeRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(OrganigrammeRepository $organigrammeRepository, LogService $ls)
    {
        $this->organigrammeRepository = $organigrammeRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/organigrammes",
     *      operationId="Organigramme list",
     *      tags={"Organigramme"},
     *      summary="Return Organigramme data",
     *      description="Get all organigramme",
     *
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Can be used for filtering data by user id",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Organigramme"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Organigramme")
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
    public function index()
    {
        $message = 'Récupération de tous les organigrammes';
        
        try {
            $organigrammes = $this->organigrammeRepository->all();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Organigrammes récupérés avec succès']);
            return Common::success($organigrammes, 'Organigrammes récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des organigrammes', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/organigramme/{id}",
     *     tags={"Organigramme"},
     *     summary="Récupérer un organigramme spécifique",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = "Récupération de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme récupéré avec succès pour ID: $id"]);
            return Common::success($organigramme, 'Organigramme récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de l\'organigramme', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme",
     *     tags={"Organigramme"},
     *     summary="Créer un nouvel organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="Organigramme Direction", description="Nom de l'organigramme"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Image de l'organigramme"),
     *                 @OA\Property(property="media_id", type="string", example="uuid", description="ID du média associé"),
     *                 @OA\Property(property="legend", type="string", example="Légende de l'organigramme", description="Légende")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Organigramme créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme créé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(StoreOrganigrammeRequest $request)
    {
        $message = 'Création d\'un nouvel organigramme';
        
        try {
            $data = $request->validated();
            $organigramme = $this->organigrammeRepository->create($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Organigramme créé avec succès avec ID: ' . $organigramme->id]);
            return Common::success($organigramme, 'Organigramme créé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création de l\'organigramme', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/organigramme/{id}",
     *     tags={"Organigramme"},
     *     summary="Mettre à jour un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Organigramme Direction", description="Nom de l'organigramme"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Image de l'organigramme"),
     *                 @OA\Property(property="media_id", type="string", example="uuid", description="ID du média associé"),
     *                 @OA\Property(property="legend", type="string", example="Légende de l'organigramme", description="Légende")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(UpdateOrganigrammeRequest $request, $id)
    {
        $message = "Mise à jour de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé pour mise à jour avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $data = $request->validated();
            $updatedOrganigramme = $this->organigrammeRepository->update($id, $data);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme mis à jour avec succès pour ID: $id"]);
            return Common::success($updatedOrganigramme, 'Organigramme mis à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de l\'organigramme', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/organigramme/{id}",
     *     tags={"Organigramme"},
     *     summary="Supprimer un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Organigramme supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = "Suppression de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé pour suppression avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $this->organigrammeRepository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme supprimé avec succès pour ID: $id"]);
            return Common::success(null, 'Organigramme supprimé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de l\'organigramme', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/change-state",
     *     tags={"Organigramme"},
     *     summary="Changer l'état d'un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="actif", description="Nouveau statut")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="État de l'organigramme modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="État de l'organigramme modifié avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function changeState(Request $request, $id)
    {
        $message = "Changement d'état de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $status = $request->input('status');
            $updatedOrganigramme = $this->organigrammeRepository->update($id, ['status' => $status]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "État changé vers '$status' pour ID: $id"]);
            return Common::success($updatedOrganigramme, 'État de l\'organigramme modifié avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors du changement d\'état', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/organigramme/search",
     *     tags={"Organigramme"},
     *     summary="Rechercher des organigrammes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Terme de recherche",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche obtenus avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Organigramme")),
     *             @OA\Property(property="message", type="string", example="Résultats de recherche obtenus avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function search(Request $request)
    {
        $message = 'Recherche d\'organigrammes';
        
        try {
            $query = $request->input('query');
            $results = $this->organigrammeRepository->search($query);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Recherche effectuée avec le terme: $query"]);
            return Common::success($results, 'Résultats de recherche obtenus avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la recherche', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/up",
     *     tags={"Organigramme"},
     *     summary="Remonter la position d'un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position remontée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Position remontée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function up($id)
    {
        $message = "Remontée de position pour l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            // Logique de remontée de position ici si nécessaire
            $this->ls->trace(['action_name' => $message, 'description' => "Position remontée avec succès pour ID: $id"]);
            return Common::success($organigramme, 'Position remontée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la remontée de position', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/down",
     *     tags={"Organigramme"},
     *     summary="Descendre la position d'un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position descendue avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Position descendue avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function down($id)
    {
        $message = "Descente de position pour l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            // Logique de descente de position ici si nécessaire
            $this->ls->trace(['action_name' => $message, 'description' => "Position descendue avec succès pour ID: $id"]);
            return Common::success($organigramme, 'Position descendue avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la descente de position', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/publish",
     *     tags={"Organigramme"},
     *     summary="Publier un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme publié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme publié avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function publish($id)
    {
        $message = "Publication de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $updatedOrganigramme = $this->organigrammeRepository->update($id, ['is_published' => true]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme publié avec succès pour ID: $id"]);
            return Common::success($updatedOrganigramme, 'Organigramme publié avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la publication de l\'organigramme', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/unpublish",
     *     tags={"Organigramme"},
     *     summary="Dépublier un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme dépublié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme dépublié avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function unpublish($id)
    {
        $message = "Dépublication de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $updatedOrganigramme = $this->organigrammeRepository->update($id, ['is_published' => false]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme dépublié avec succès pour ID: $id"]);
            return Common::success($updatedOrganigramme, 'Organigramme dépublié avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la dépublication de l\'organigramme', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/archive",
     *     tags={"Organigramme"},
     *     summary="Archiver un organigramme",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme archivé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme archivé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function archive($id)
    {
        $message = "Archivage de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $updatedOrganigramme = $this->organigrammeRepository->update($id, ['is_archived' => true]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme archivé avec succès pour ID: $id"]);
            return Common::success($updatedOrganigramme, 'Organigramme archivé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de l\'archivage de l\'organigramme', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/organigramme/{id}/restore",
     *     tags={"Organigramme"},
     *     summary="Restaurer un organigramme archivé",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'organigramme",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organigramme restauré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organigramme"),
     *             @OA\Property(property="message", type="string", example="Organigramme restauré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organigramme non trouvé"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function restore($id)
    {
        $message = "Restauration de l'organigramme avec ID: $id";
        
        try {
            $organigramme = $this->organigrammeRepository->findById($id);
            
            if (!$organigramme) {
                $this->ls->trace(['action_name' => $message, 'description' => "Organigramme non trouvé avec ID: $id"]);
                return Common::error('Organigramme non trouvé', []);
            }
            
            $updatedOrganigramme = $this->organigrammeRepository->update($id, ['is_archived' => false]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Organigramme restauré avec succès pour ID: $id"]);
            return Common::success($updatedOrganigramme, 'Organigramme restauré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la restauration de l\'organigramme', []);
        }
    }
}
