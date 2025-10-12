<?php

namespace App\Http\Controllers;

use App\Http\Repositories\RecrutementRepository;
use App\Http\Requests\Recrutement\StoreRecrutementRequest;
use App\Http\Requests\Recrutement\UpdateRecrutementRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RecrutementController extends Controller
{
    /**
     * Repository pour la gestion des recrutements.
     */
    protected RecrutementRepository $recrutementRepository;

    /**
     * Service de logging.
     */
    protected LogService $logService;

    /**
     * Constructeur du contrôleur.
     */
    public function __construct(RecrutementRepository $recrutementRepository, LogService $logService)
    {
        $this->recrutementRepository = $recrutementRepository;
        $this->logService = $logService;

        // $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * Récupère la liste des recrutements.
     */
    #[OA\Get(
        path: "/recrutements",
        operationId: "getRecrutements",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Récupère la liste des recrutements",
        description: "Retourne la liste paginée des recrutements avec possibilité de filtrage selon les rôles",
        parameters: [
            new OA\Parameter(
                name: "title",
                in: "query",
                description: "Filtrer par titre",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                description: "Filtrer par statut",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                description: "Nombre d'éléments par page",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste récupérée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 400, description: "Requête invalide"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function index(Request $request)
    {
        $message = 'Récupération de la liste des recrutements';

        try {
            $result = $this->recrutementRepository->getAll($request);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Affiche un recrutement spécifique.
     */
    #[OA\Get(
        path: "/recrutements/{id}",
        operationId: "getRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Affiche un recrutement",
        description: "Retourne les détails d'un recrutement spécifique",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement trouvé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function show(Request $request, $id)
    {
        $message = 'Récupération d\'un recrutement';

        try {
            $result = $this->recrutementRepository->get($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Recrutement trouvé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Crée un nouveau recrutement.
     */
    #[OA\Post(
        path: "/recrutements",
        operationId: "createRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Crée un nouveau recrutement",
        description: "Enregistre un nouveau recrutement en base de données",
        requestBody: new OA\RequestBody(
            description: "Données du recrutement à créer",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/RecrutementCreate")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Recrutement créé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 422, description: "Données de validation invalides"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function store(StoreRecrutementRequest $request)
    {
        $message = 'Création d\'un recrutement';

        try {
            $result = $this->recrutementRepository->makeStore($request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Recrutement créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Met à jour un recrutement existant.
     */
    #[OA\Put(
        path: "/recrutements/{id}",
        operationId: "updateRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Met à jour un recrutement",
        description: "Modifie les données d'un recrutement existant",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: "Données à mettre à jour",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/RecrutementCreate")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement mis à jour avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 422, description: "Données de validation invalides"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function update(UpdateRecrutementRequest $request, $id)
    {
        $message = 'Mise à jour d\'un recrutement';

        try {
            $result = $this->recrutementRepository->makeUpdate($id, $request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Recrutement mis à jour avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Supprime un recrutement.
     */
    #[OA\Delete(
        path: "/recrutements/{id}",
        operationId: "deleteRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Supprime un recrutement",
        description: "Supprime définitivement un recrutement de la base de données",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement supprimé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/DeleteResponseData")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function destroy($id)
    {
        $message = 'Suppression d\'un recrutement';

        try {
            $recup = $this->recrutementRepository->get($id);
            $result = $this->recrutementRepository->makeDestroy($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($recup)]);

            return Common::successDelete('Recrutement supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Fait remonter un recrutement dans le workflow.
     */
    #[OA\Patch(
        path: "/recrutements/{id}/up",
        operationId: "upRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Fait remonter un recrutement",
        description: "Fait passer le recrutement au niveau supérieur dans le workflow",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement remonté avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function up($id)
    {
        $message = 'Remontée d\'un recrutement dans le workflow';

        try {
            $result = $this->recrutementRepository->up($id);
            $this->logService->trace(['action_name' => $message, 'description' => "Recrutement ID: {$id}"]);

            return Common::success('Recrutement remonté avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Fait redescendre un recrutement dans le workflow.
     */
    #[OA\Patch(
        path: "/recrutements/{id}/down",
        operationId: "downRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Fait redescendre un recrutement",
        description: "Fait passer le recrutement au niveau inférieur dans le workflow",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement redescendu avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function down($id)
    {
        $message = 'Redescente d\'un recrutement dans le workflow';

        try {
            $result = $this->recrutementRepository->down($id);
            $this->logService->trace(['action_name' => $message, 'description' => "Recrutement ID: {$id}"]);

            return Common::success('Recrutement redescendu avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Publie un recrutement.
     */
    #[OA\Patch(
        path: "/recrutements/{id}/publish",
        operationId: "publishRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Publie un recrutement",
        description: "Rend le recrutement visible au public",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement publié avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function publish($id)
    {
        $message = 'Publication d\'un recrutement';

        try {
            $result = $this->recrutementRepository->publish($id);
            $this->logService->trace(['action_name' => $message, 'description' => "Recrutement ID: {$id}"]);

            return Common::success('Recrutement publié avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Dépublie un recrutement.
     */
    #[OA\Patch(
        path: "/recrutements/{id}/unpublish",
        operationId: "unpublishRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Dépublie un recrutement",
        description: "Retire le recrutement de la vue publique",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement dépublié avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function unpublish($id)
    {
        $message = 'Dépublication d\'un recrutement';

        try {
            $result = $this->recrutementRepository->unpublish($id);
            $this->logService->trace(['action_name' => $message, 'description' => "Recrutement ID: {$id}"]);

            return Common::success('Recrutement dépublié avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Archive un recrutement.
     */
    #[OA\Patch(
        path: "/recrutements/{id}/archive",
        operationId: "archiveRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Archive un recrutement",
        description: "Archive le recrutement et le marque comme inactif",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement archivé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function archive($id)
    {
        $message = 'Archivage d\'un recrutement';

        try {
            $result = $this->recrutementRepository->archive($id);
            $this->logService->trace(['action_name' => $message, 'description' => "Recrutement ID: {$id}"]);

            return Common::success('Recrutement archivé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Restaure un recrutement archivé.
     */
    #[OA\Patch(
        path: "/recrutements/{id}/restore",
        operationId: "restoreRecrutement",
        tags: ["Recrutement"],
        security: [["JWT" => []]],
        summary: "Restaure un recrutement",
        description: "Restaure un recrutement archivé et le remet en circulation",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du recrutement",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recrutement restauré avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Recrutement")
            ),
            new OA\Response(response: 404, description: "Recrutement non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function restore($id)
    {
        $message = 'Restauration d\'un recrutement';

        try {
            $result = $this->recrutementRepository->restore($id);
            $this->logService->trace(['action_name' => $message, 'description' => "Recrutement ID: {$id}"]);

            return Common::success('Recrutement restauré avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
