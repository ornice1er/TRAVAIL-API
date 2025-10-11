<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PageRepository;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PageController
{
    /**
     * The Page repository being queried.
     *
     * @var PageRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(PageRepository $pageRepository, LogService $ls)
    {
        $this->repository = $pageRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *     path="/api/pages/gallery/{type}",
     *     tags={"Page"},
     *     summary="Récupérer les fichiers d'une galerie par type",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Type de galerie"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichiers récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ActualitesFiles")),
     *             @OA\Property(property="message", type="string", example="Fichiers de galerie récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Galerie non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getGalleryFiles($type)
    {
        $message = 'Récupération des fichiers de galerie';
        
        try {
            $files = $this->repository->getGalleryFiles($type);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Fichiers de galerie récupérés avec succès']);
            return Common::success($files, 'Fichiers de galerie récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des fichiers de galerie', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pages/communique/{slug}",
     *     tags={"Page"},
     *     summary="Récupérer un communiqué par slug",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Slug du communiqué"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Communiqué récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Communiques"),
     *             @OA\Property(property="message", type="string", example="Communiqué récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Communiqué non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getCommunique($slug)
    {
        $message = 'Récupération du communiqué';
        
        try {
            $communique = $this->repository->getCommuniqueBySlug($slug);
            
            if (!$communique) {
                return Common::error('Communiqué non trouvé', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Communiqué récupéré avec succès']);
            return Common::success($communique, 'Communiqué récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération du communiqué', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pages/actualite/{slug}",
     *     tags={"Page"},
     *     summary="Récupérer une actualité par slug",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Slug de l'actualité"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actualité récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Actualites"),
     *             @OA\Property(property="message", type="string", example="Actualité récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Actualité non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getActualite($slug)
    {
        $message = 'Récupération de l\'actualité';
        
        try {
            $actualite = $this->repository->getActualiteBySlug($slug);
            
            if (!$actualite) {
                return Common::error('Actualité non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Actualité récupérée avec succès']);
            return Common::success($actualite, 'Actualité récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de l\'actualité', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pages/ministre-word",
     *     tags={"Page"},
     *     summary="Récupérer le mot du ministre",
     *     @OA\Response(
     *         response=200,
     *         description="Mot du ministre récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="string", example="Mot du ministre..."),
     *             @OA\Property(property="message", type="string", example="Mot du ministre récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Mot du ministre non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getMinistreWord()
    {
        $message = 'Récupération du mot du ministre';
        
        try {
            $word = $this->repository->getMinistreWord();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Mot du ministre récupéré avec succès']);
            return Common::success($word, 'Mot du ministre récupéré avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération du mot du ministre', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pages/last-communiques",
     *     tags={"Page"},
     *     summary="Récupérer les derniers communiqués",
     *     @OA\Response(
     *         response=200,
     *         description="Derniers communiqués récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Media")),
     *             @OA\Property(property="message", type="string", example="Derniers communiqués récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getLastCommuniques()
    {
        $message = 'Récupération des derniers communiqués';
        
        try {
            $communiques = $this->repository->getLastCommuniques();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Derniers communiqués récupérés avec succès']);
            return Common::success($communiques, 'Derniers communiqués récupérés avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des derniers communiqués', []);
        }
    }
}