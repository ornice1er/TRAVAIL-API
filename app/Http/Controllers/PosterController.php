<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PosterRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PosterController
{
    /**
     * The Poster repository being queried.
     *
     * @var PosterRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(PosterRepository $posterRepository, LogService $ls)
    {
        $this->repository = $posterRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/posters",
     *     tags={"Poster"},
     *     summary="Récupérer tous les posters",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrer par titre"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Posters récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Posters récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération des posters';
        
        try {
            $posters = $this->repository->getAll($request);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Posters récupérés avec succès']);
            return Common::success($posters, 'Posters récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des posters', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/posters/{id}",
     *     tags={"Poster"},
     *     summary="Récupérer un poster par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du poster"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Poster récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Poster"),
     *             @OA\Property(property="message", type="string", example="Poster récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Poster non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = 'Récupération du poster';
        
        try {
            $poster = $this->repository->findById($id);
            
            if (!$poster) {
                return Common::error('Poster non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Poster récupéré avec succès']);
            return Common::success($poster, 'Poster récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération du poster', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/posters",
     *     tags={"Poster"},
     *     summary="Créer un nouveau poster",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Titre du poster", maxLength=50),
     *                 @OA\Property(property="image", type="string", format="binary", description="Image du poster")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Poster créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Poster"),
     *             @OA\Property(property="message", type="string", example="Poster créé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $message = 'Création de poster';
        
        try {
            $request->validate([
                'title' => 'string|required|max:50',
                'image' => 'file|required',
            ]);

            $data = $request->all();
            $data['image'] = $request->file('image');
            
            $poster = $this->repository->create($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Poster créé avec succès']);
            return Common::success($poster, 'Poster créé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création du poster', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/posters/{id}",
     *     tags={"Poster"},
     *     summary="Mettre à jour un poster",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du poster"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Titre modifié", maxLength=50),
     *                 @OA\Property(property="image", type="string", format="binary", description="Nouvelle image (optionnel)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Poster mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Poster mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Poster non trouvé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $message = 'Mise à jour de poster';
        
        try {
            $request->validate([
                'title' => 'string|required|max:50',
            ]);

            if (!$this->repository->ifExist($id)) {
                return Common::error('Poster non trouvé', []);
            }

            $data = $request->all();
            if ($request->file('image')) {
                $data['image'] = $request->file('image');
            }
            
            $result = $this->repository->update($id, $data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Poster mis à jour avec succès']);
            return Common::success($result, 'Poster mis à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour du poster', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/posters/{id}",
     *     tags={"Poster"},
     *     summary="Supprimer un poster",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du poster"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Poster supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Poster supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Poster non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression de poster';
        
        try {
            if (!$this->repository->ifExist($id)) {
                return Common::error('Poster non trouvé', []);
            }
            
            $result = $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Poster supprimé avec succès']);
            return Common::success($result, 'Poster supprimé avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression du poster', []);
        }
    }
}
