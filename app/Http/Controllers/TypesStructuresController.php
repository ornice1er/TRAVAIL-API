<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TypesStructuresRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TypesStructuresController
{
    /**
     * The TypesStructures repository being queried.
     *
     * @var TypesStructuresRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(TypesStructuresRepository $typesStructuresRepository, LogService $ls)
    {
        $this->repository = $typesStructuresRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/types-structures",
     *     tags={"TypesStructures"},
     *     summary="Récupérer tous les types de structures",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par titre"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Types de structures récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TypesStructures")),
     *             @OA\Property(property="message", type="string", example="Types de structures récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération des types de structures';
        
        try {
            if ($request->has('title') && $request->title) {
                $typesStructures = $this->repository->searchByTitle($request->title);
            } else {
                $typesStructures = $this->repository->getAll($request);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Types de structures récupérés avec succès']);
            return Common::success($typesStructures, 'Types de structures récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des types de structures', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/types-structures/hierarchy",
     *     tags={"TypesStructures"},
     *     summary="Récupérer les types de structures avec hiérarchie",
     *     @OA\Response(
     *         response=200,
     *         description="Hiérarchie des types de structures récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TypesStructures")),
     *             @OA\Property(property="message", type="string", example="Hiérarchie récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getHierarchy()
    {
        $message = 'Récupération de la hiérarchie des types de structures';
        
        try {
            $hierarchy = $this->repository->getWithChildren();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Hiérarchie récupérée avec succès']);
            return Common::success($hierarchy, 'Hiérarchie des types de structures récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de la hiérarchie', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/types-structures/parents",
     *     tags={"TypesStructures"},
     *     summary="Récupérer les types de structures parents",
     *     @OA\Response(
     *         response=200,
     *         description="Types de structures parents récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TypesStructures")),
     *             @OA\Property(property="message", type="string", example="Types parents récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getParents()
    {
        $message = 'Récupération des types de structures parents';
        
        try {
            $parents = $this->repository->getParentTypes();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Types parents récupérés avec succès']);
            return Common::success($parents, 'Types de structures parents récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des types parents', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/types-structures/{id}",
     *     tags={"TypesStructures"},
     *     summary="Récupérer un type de structure par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du type de structure"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type de structure récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TypesStructures"),
     *             @OA\Property(property="message", type="string", example="Type de structure récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Type de structure non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération du type de structure';
        
        try {
            $typeStructure = $this->repository->findById($id);
            
            if (!$typeStructure) {
                return Common::error('Type de structure non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Type de structure récupéré avec succès']);
            return Common::success($typeStructure, 'Type de structure récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération du type de structure', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/types-structures",
     *     tags={"TypesStructures"},
     *     summary="Créer un nouveau type de structure",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Direction"),
     *             @OA\Property(property="is_parent", type="boolean", example=true, description="Si c'est un type parent"),
     *             @OA\Property(property="parent_id", type="integer", example=null, description="ID du parent si type enfant"),
     *             @OA\Property(property="description", type="string", example="Description du type de structure")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Type de structure créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TypesStructures"),
     *             @OA\Property(property="message", type="string", example="Type de structure créé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $message = 'Création de type de structure';
        
        try {
            $request->validate([
                'title' => 'string|required',
                'is_parent' => 'boolean|nullable',
                'parent_id' => 'integer|nullable',
                'description' => 'string|nullable',
            ], [
                'title.required' => 'Le titre est requis',
            ]);

            $data = $request->all();
            
            $typeStructure = $this->repository->create($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Type de structure créé avec succès']);
            return Common::success($typeStructure, 'Type de structure créé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création du type de structure', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/types-structures/{id}",
     *     tags={"TypesStructures"},
     *     summary="Mettre à jour un type de structure",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du type de structure"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Direction Modifiée"),
     *             @OA\Property(property="is_parent", type="boolean", example=false),
     *             @OA\Property(property="parent_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Description mise à jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type de structure mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Type de structure mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Type de structure non trouvé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $message = 'Mise à jour de type de structure';
        
        try {
            $request->validate([
                'title' => 'string|required',
                'is_parent' => 'boolean|nullable',
                'parent_id' => 'integer|nullable',
                'description' => 'string|nullable',
            ], [
                'title.required' => 'Le titre est requis',
            ]);

            $data = $request->all();
            
            $result = $this->repository->update($id, $data);
            
            if (!$result) {
                return Common::error('Type de structure non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Type de structure mis à jour avec succès']);
            return Common::success($result, 'Type de structure mis à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour du type de structure', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/types-structures/{id}",
     *     tags={"TypesStructures"},
     *     summary="Supprimer un type de structure",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du type de structure"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type de structure supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Type de structure supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Impossible de supprimer - structures liées existantes"),
     *     @OA\Response(response=404, description="Type de structure non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de type de structure';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Type de structure non trouvé', []);
            }
            
            if (!$this->repository->canBeDeleted($id)) {
                return Common::error('Impossible de supprimer - structures ou types enfants liés', []);
            }
            
            $result = $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Type de structure supprimé avec succès']);
            return Common::success($result, 'Type de structure supprimé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression du type de structure', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/types-structures/children/{parentId}",
     *     tags={"TypesStructures"},
     *     summary="Récupérer les types enfants d'un type parent",
     *     @OA\Parameter(
     *         name="parentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du type parent"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Types enfants récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TypesStructures")),
     *             @OA\Property(property="message", type="string", example="Types enfants récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getChildren($parentId)
    {
        $message = 'Récupération des types enfants';
        
        try {
            $children = $this->repository->getChildrenByParent($parentId);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Types enfants récupérés avec succès']);
            return Common::success($children, 'Types enfants récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des types enfants', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/types-structures/dropdown",
     *     tags={"TypesStructures"},
     *     summary="Récupérer les types de structures pour dropdown",
     *     @OA\Response(
     *         response=200,
     *         description="Types pour dropdown récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Direction")
     *             )),
     *             @OA\Property(property="message", type="string", example="Types récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getForDropdown()
    {
        $message = 'Récupération des types pour dropdown';
        
        try {
            $types = $this->repository->getForDropdown();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Types pour dropdown récupérés avec succès']);
            return Common::success($types, 'Types de structures récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des types', []);
        }
    }
}