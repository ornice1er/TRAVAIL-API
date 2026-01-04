<?php

namespace App\Http\Controllers;

use App\Http\Repositories\StructureRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class StructureController
{
    /**
     * The Structure repository being queried.
     *
     * @var StructureRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(StructureRepository $structureRepository, LogService $ls)
    {
        $this->repository = $structureRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/structures",
     *     tags={"Structure"},
     *     summary="Récupérer toutes les structures",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par nom"
     *     ),
     *     @OA\Parameter(
     *         name="type_structure_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filtrer par type de structure"
     *     ),
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
    public function index(Request $request)
    {
        $message = 'Récupération des structures';
        
     //   try {
            $structures = $this->repository->getAll($request);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structures récupérées avec succès']);
            return Common::success( 'Structures récupérées avec succès',$structures);
        // } catch (\Exception $e) {
        //     $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
        //     return Common::error('Erreur lors de la récupération des structures', []);
        // }
    }

    /**
     * @OA\Get(
     *     path="/api/structures/types",
     *     tags={"Structure"},
     *     summary="Récupérer tous les types de structures",
     *     @OA\Response(
     *         response=200,
     *         description="Types de structures récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TypeStructure")),
     *             @OA\Property(property="message", type="string", example="Types de structures récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getTypes()
    {
        $message = 'Récupération des types de structures';
        
        try {
            $types = $this->repository->getAllTypeStructures();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Types de structures récupérés avec succès']);
            return Common::success($types, 'Types de structures récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des types de structures', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/structures/{id}",
     *     tags={"Structure"},
     *     summary="Récupérer une structure par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structure récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Structure"),
     *             @OA\Property(property="message", type="string", example="Structure récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Structure non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération de la structure';
        
        try {
            $structure = $this->repository->findById($id);
            
            if (!$structure) {
                return Common::error('Structure non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure récupérée avec succès']);
            return Common::success($structure, 'Structure récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de la structure', []);
        }
    }


    

    /**
     * @OA\Post(
     *     path="/api/structures/with-files",
     *     tags={"Structure"},
     *     summary="Créer une structure avec gestion des fichiers",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="type_structure_id", type="string", example="1"),
     *                 @OA\Property(property="name", type="string", example="Direction Générale"),
     *                 @OA\Property(property="acronym", type="string", example="DG"),
     *                 @OA\Property(property="name_responsable", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="biographie_responsable", type="string", example="Biographie du responsable"),
     *                 @OA\Property(property="vision", type="string", example="Vision de la structure"),
     *                 @OA\Property(property="email", type="string", format="email", example="direction@example.com"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Photo de la structure"),
     *                 @OA\Property(property="photo_responsable", type="string", format="binary", description="Photo du responsable"),
     *                 @OA\Property(property="vision_file", type="string", format="binary", description="Fichier vision")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Structure créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Structure"),
     *             @OA\Property(property="message", type="string", example="Structure créée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function createWithFiles(Request $request)
    {
        $message = 'Création de structure avec fichiers';
        
        try {
            $request->validate([
                'type_structure_id' => 'string|required',
                'name' => 'string|required',
                'acronym' => 'string|required',
                'name_responsable' => 'string|required',
                'biographie_responsable' => 'string|nullable',
                'vision' => 'string|nullable',
                'email' => 'email|required',
            ], [
                'type_structure_id.required' => 'Le type structure est requis',
                'name.required' => 'Le nom de la structure est requise',
                'acronym.required' => 'Le sigle de la structure est requise',
                'name_responsable.required' => 'Le nom du responsable est requis',
                'email.email' => 'Un email valide est requis',
            ]);

            $data = $request->all();
            
            // Ajout des fichiers
            if ($request->file('photo')) {
                $data['photo'] = $request->file('photo');
            }
            if ($request->file('photo_responsable')) {
                $data['photo_responsable'] = $request->file('photo_responsable');
            }
            if ($request->file('vision_file')) {
                $data['vision_file'] = $request->file('vision_file');
            }
            
            $structure = $this->repository->createWithFiles($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure créée avec succès']);
            return Common::success($structure, 'Structure créée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création de la structure', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/structures/{id}/with-files",
     *     tags={"Structure"},
     *     summary="Mettre à jour une structure avec gestion des fichiers",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="type_structure_id", type="string", example="1"),
     *                 @OA\Property(property="name", type="string", example="Direction Générale Modifiée"),
     *                 @OA\Property(property="acronym", type="string", example="DGM"),
     *                 @OA\Property(property="name_responsable", type="string", example="Marie Martin"),
     *                 @OA\Property(property="biographie_responsable", type="string", example="Nouvelle biographie"),
     *                 @OA\Property(property="vision", type="string", example="Nouvelle vision"),
     *                 @OA\Property(property="email", type="string", format="email", example="nouvelle@example.com"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Nouvelle photo de la structure (optionnel)"),
     *                 @OA\Property(property="photo_responsable", type="string", format="binary", description="Nouvelle photo du responsable (optionnel)"),
     *                 @OA\Property(property="vision_file", type="string", format="binary", description="Nouveau fichier vision (optionnel)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structure mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Structure mise à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Structure non trouvée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function updateWithFiles(Request $request, $id)
    {
        $message = 'Mise à jour de structure avec fichiers';
        
        try {
            $request->validate([
                'name' => 'string|required',
                'acronym' => 'string|required',
                'name_responsable' => 'string|required',
                'biographie_responsable' => 'string|nullable',
                'vision' => 'string|nullable',
                'email' => 'email|nullable',
            ], [
                'name.required' => 'Le nom de la structure est requise',
                'acronym.required' => 'Le sigle de la structure est requise',
                'name_responsable.required' => 'Le nom du responsable est requis',
                'email.email' => 'Un email valide est requis',
            ]);

            $data = $request->all();
            $files = [];
            
            // Collecte des fichiers
            if ($request->file('photo')) {
                $files['photo'] = $request->file('photo');
            }
            if ($request->file('photo_responsable')) {
                $files['photo_responsable'] = $request->file('photo_responsable');
            }
            if ($request->file('vision_file')) {
                $files['vision_file'] = $request->file('vision_file');
            }
            
            $result = $this->repository->updateWithFiles($id, $data, $files);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure mise à jour avec succès']);
            return Common::success($result, 'Structure mise à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de la structure', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/structures/{id}",
     *     tags={"Structure"},
     *     summary="Supprimer une structure",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Structure supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Structure supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Impossible de supprimer - structure avec médias associés"),
     *     @OA\Response(response=404, description="Structure non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de structure';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Structure non trouvée', []);
            }
            
            $result = $this->repository->delete($id);
            
            if ($result === false) {
                return Common::error('Impossible de supprimer - structure avec médias associés', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Structure supprimée avec succès']);
            return Common::success($result, 'Structure supprimée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de la structure', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/structures/ministre/biographie",
     *     tags={"Structure"},
     *     summary="Récupérer la biographie du ministre",
     *     @OA\Response(
     *         response=200,
     *         description="Biographie du ministre récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Structure"),
     *             @OA\Property(property="message", type="string", example="Biographie du ministre récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Biographie non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getMinistreBiographie()
    {
        $message = 'Récupération de la biographie du ministre';
        
        try {
            $structure = $this->repository->getMinistreBiographie();
            
            if (!$structure) {
                return Common::error('Biographie du ministre non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Biographie du ministre récupérée avec succès']);
            return Common::success($structure, 'Biographie du ministre récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de la biographie du ministre', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/structures/user/info",
     *     tags={"Structure"},
     *     summary="Récupérer les informations de la structure de l'utilisateur connecté",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations de structure récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Structure"),
     *             @OA\Property(property="message", type="string", example="Informations de structure récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Structure non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getUserStructureInfo()
    {
        $message = 'Récupération des informations de structure de l\'utilisateur';
        
        try {
            $structure = Auth::user()->structure;
            
            if (!$structure) {
                return Common::error('Structure non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Informations de structure récupérées avec succès']);
            return Common::success($structure, 'Informations de structure récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des informations de structure', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/structures/{id}/biographie",
     *     tags={"Structure"},
     *     summary="Mettre à jour la biographie d'une structure",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la structure"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="biographie_responsable", type="string", example="Nouvelle biographie du responsable")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Biographie mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Biographie mise à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Structure non trouvée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function updateBiographie(Request $request, $id)
    {
        $message = 'Mise à jour de biographie';
        
        try {
            $request->validate([
                'biographie_responsable' => 'string|required',
            ]);

            $result = $this->repository->updateBiographie($id, $request->biographie_responsable);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Biographie mise à jour avec succès']);
            return Common::success($result, 'Biographie mise à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de la biographie', []);
        }
    }
}
