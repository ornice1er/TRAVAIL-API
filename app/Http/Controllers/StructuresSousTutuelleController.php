<?php

namespace App\Http\Controllers;

use App\Http\Repositories\StructuresSousTutuelleRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StructuresSousTutuelleController
{
    /**
     * The StructuresSousTutuelle repository being queried.
     *
     * @var StructuresSousTutuelleRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(StructuresSousTutuelleRepository $structuresSousTutuelleRepository, LogService $ls)
    {
        $this->repository = $structuresSousTutuelleRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/structures-sous-tutelle",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Récupérer toutes les structures sous tutelle",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par nom"
     *     ),
     *     @OA\Parameter(
     *         name="structure_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filtrer par structure parente"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structures sous tutelle récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StructuresSousTutelles")),
     *             @OA\Property(property="message", type="string", example="Structures sous tutelle récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération des structures sous tutelle';
        
        try {
            $structuresSousTutelle = $this->repository->getAll($request);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structures sous tutelle récupérées avec succès']);
            return Common::success($structuresSousTutelle, 'Structures sous tutelle récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des structures sous tutelle', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/structures-sous-tutelle/structures",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Récupérer toutes les structures parentes",
     *     @OA\Response(
     *         response=200,
     *         description="Structures parentes récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Structure")),
     *             @OA\Property(property="message", type="string", example="Structures parentes récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getStructures()
    {
        $message = 'Récupération des structures parentes';
        
        try {
            $structures = $this->repository->getAllStructures();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structures parentes récupérées avec succès']);
            return Common::success($structures, 'Structures parentes récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des structures parentes', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/structures-sous-tutelle/{id}",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Récupérer une structure sous tutelle par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure sous tutelle"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structure sous tutelle récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/StructuresSousTutelles"),
     *             @OA\Property(property="message", type="string", example="Structure sous tutelle récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Structure sous tutelle non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération de la structure sous tutelle';
        
        try {
            $structureSousTutelle = $this->repository->findById($id);
            
            if (!$structureSousTutelle) {
                return Common::error('Structure sous tutelle non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure sous tutelle récupérée avec succès']);
            return Common::success($structureSousTutelle, 'Structure sous tutelle récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de la structure sous tutelle', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/structures-sous-tutelle/with-logo",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Créer une structure sous tutelle avec logo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Structure sous tutelle exemple"),
     *                 @OA\Property(property="structure_id", type="string", example="1"),
     *                 @OA\Property(property="description", type="string", example="Description de la structure"),
     *                 @OA\Property(property="contact", type="string", example="contact@example.com"),
     *                 @OA\Property(property="logo", type="string", format="binary", description="Logo de la structure")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Structure sous tutelle créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/StructuresSousTutelles"),
     *             @OA\Property(property="message", type="string", example="Structure sous tutelle créée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function createWithLogo(Request $request)
    {
        $message = 'Création de structure sous tutelle avec logo';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'structure_id' => 'string|nullable',
                'description' => 'string|nullable',
                'contact' => 'string|nullable',
            ], [
                'name.required' => 'Le nom de la structure est requis',
            ]);

            $data = $request->all();
            
            // Ajout du logo
            if ($request->file('logo')) {
                $data['logo'] = $request->file('logo');
            }
            
            $structureSousTutuelle = $this->repository->createWithLogo($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure sous tutelle créée avec succès']);
            return Common::success($structureSousTutuelle, 'Structure sous tutelle créée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création de la structure sous tutelle', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/structures-sous-tutelle/{id}/with-logo",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Mettre à jour une structure sous tutelle avec logo",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure sous tutelle"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Structure sous tutelle modifiée"),
     *                 @OA\Property(property="structure_id", type="string", example="2"),
     *                 @OA\Property(property="description", type="string", example="Nouvelle description"),
     *                 @OA\Property(property="contact", type="string", example="nouveau@example.com"),
     *                 @OA\Property(property="logo", type="string", format="binary", description="Nouveau logo (optionnel)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structure sous tutelle mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Structure sous tutelle mise à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Structure sous tutelle non trouvée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function updateWithLogo(Request $request, $id)
    {
        $message = 'Mise à jour de structure sous tutelle avec logo';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'structure_id' => 'string|nullable',
                'description' => 'string|nullable',
                'contact' => 'string|nullable',
            ], [
                'name.required' => 'Le nom de la structure est requis',
            ]);

            $data = $request->all();
            $logoFile = $request->file('logo');
            
            $result = $this->repository->updateWithLogo($id, $data, $logoFile);
            
            if (!$result) {
                return Common::error('Structure sous tutelle non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure sous tutelle mise à jour avec succès']);
            return Common::success($result, 'Structure sous tutelle mise à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de la structure sous tutelle', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/structures-sous-tutelle/{id}",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Supprimer une structure sous tutelle",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure sous tutelle"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structure sous tutelle supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Structure sous tutelle supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Structure sous tutelle non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de structure sous tutelle';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Structure sous tutelle non trouvée', []);
            }
            
            $result = $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure sous tutelle supprimée avec succès']);
            return Common::success($result, 'Structure sous tutelle supprimée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de la structure sous tutelle', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/structures-sous-tutelle/by-structure/{structureId}",
     *     tags={"StructuresSousTutuelle"},
     *     summary="Récupérer les structures sous tutelle par structure parente",
     *     @OA\Parameter(
     *         name="structureId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure parente"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structures sous tutelle récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StructuresSousTutelles")),
     *             @OA\Property(property="message", type="string", example="Structures sous tutelle récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getByStructure($structureId)
    {
        $message = 'Récupération des structures sous tutelle par structure';
        
        try {
            $structuresSousTutelle = $this->repository->getByStructure($structureId);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structures sous tutelle récupérées avec succès']);
            return Common::success($structuresSousTutelle, 'Structures sous tutelle récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des structures sous tutelle', []);
        }
    }
}