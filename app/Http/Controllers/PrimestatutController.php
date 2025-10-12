<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PrimestatutRepository;
use App\Http\Requests\Primestatut\StorePrimestatutRequest;
use App\Http\Requests\Primestatut\UpdatePrimestatutRequest;
use App\Http\Requests\Primestatut\GenerateLinkRequest;
use App\Http\Requests\Primestatut\VerifyLinkRequest;
use App\Http\Requests\Primestatut\ParticipateRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PrimestatutController extends Controller
{
    /**
     * Repository pour la gestion des primestatuts.
     */
    protected PrimestatutRepository $primestatutRepository;

    /**
     * Service de logging.
     */
    protected LogService $logService;

    /**
     * Constructeur du contrôleur.
     */
    public function __construct(PrimestatutRepository $primestatutRepository, LogService $logService)
    {
        $this->primestatutRepository = $primestatutRepository;
        $this->logService = $logService;

        // $this->middleware('auth:api')->except(['getNotified', 'show']);
    }

    /**
     * Récupère la liste des primestatuts.
     */
    #[OA\Get(
        path: "/primestatuts",
        operationId: "getPrimestatuts",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Récupère la liste des primestatuts",
        description: "Retourne la liste paginée des primestatuts avec possibilité de filtrage",
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                description: "Filtrer par nom",
                required: false,
                schema: new OA\Schema(type: "string")
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
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 400, description: "Requête invalide"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function index(Request $request)
    {
        $message = 'Récupération de la liste des primestatuts';

        try {
            $result = $this->primestatutRepository->getAll($request);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Affiche un primestatut spécifique.
     */
    #[OA\Get(
        path: "/primestatuts/{id}",
        operationId: "getPrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Affiche un primestatut",
        description: "Retourne les détails d'un primestatut spécifique",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut trouvé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function show(Request $request, $id)
    {
        $message = 'Récupération d\'un primestatut';

        try {
            $result = $this->primestatutRepository->get($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Primestatut trouvé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Crée un nouveau primestatut.
     */
    #[OA\Post(
        path: "/primestatuts",
        operationId: "createPrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Crée un nouveau primestatut",
        description: "Enregistre un nouveau primestatut en base de données",
        requestBody: new OA\RequestBody(
            description: "Données du primestatut à créer",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/PrimestatutCreate")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Primestatut créé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 422, description: "Données de validation invalides"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function store(StorePrimestatutRequest $request)
    {
        $message = 'Création d\'un primestatut';

        try {
            $result = $this->primestatutRepository->makeStore($request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Primestatut créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Met à jour un primestatut existant.
     */
    #[OA\Put(
        path: "/primestatuts/{id}",
        operationId: "updatePrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Met à jour un primestatut",
        description: "Modifie les données d'un primestatut existant",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: "Données à mettre à jour",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/PrimestatutCreate")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut mis à jour avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 422, description: "Données de validation invalides"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function update(UpdatePrimestatutRequest $request, $id)
    {
        $message = 'Mise à jour d\'un primestatut';

        try {
            $result = $this->primestatutRepository->makeUpdate($id, $request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Primestatut mis à jour avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Supprime un primestatut.
     */
    #[OA\Delete(
        path: "/primestatuts/{id}",
        operationId: "deletePrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Supprime un primestatut",
        description: "Supprime définitivement un primestatut de la base de données",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut supprimé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/DeleteResponseData")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function destroy($id)
    {
        $message = 'Suppression d\'un primestatut';

        try {
            $recup = $this->primestatutRepository->get($id);
            $result = $this->primestatutRepository->makeDestroy($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($recup)]);

            return Common::successDelete('Primestatut supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Change l'état d'un primestatut.
     */
    #[OA\Get(
        path: "/primestatuts/{id}/state/{state}",
        operationId: "changePrimestatutState",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Change l'état d'un primestatut",
        description: "Active ou désactive un primestatut",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "state",
                in: "path",
                description: "Nouvel état (1 pour actif, 0 pour inactif)",
                required: true,
                schema: new OA\Schema(type: "integer", enum: [0, 1])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "État modifié avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function changeState($id, $state)
    {
        $message = 'Changement de l\'état d\'un primestatut';

        try {
            $result = $this->primestatutRepository->setStatus($id, $state);
            $statusMessage = $state == 1 ? 'activé' : 'désactivé';
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Primestatut $statusMessage avec succès", $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Recherche dans les primestatuts.
     */
    #[OA\Post(
        path: "/primestatuts-search",
        operationId: "searchPrimestatuts",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Recherche dans les primestatuts",
        description: "Filtre les primestatuts selon un terme de recherche",
        requestBody: new OA\RequestBody(
            description: "Terme de recherche",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/TermSearch")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Recherche effectuée avec succès",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Primestatut")
                )
            ),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function search(Request $request)
    {
        $message = 'Recherche dans les primestatuts';

        try {
            $term = $request->term;
            $result = $this->primestatutRepository->search($term);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Recherche effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Restaure un primestatut supprimé.
     */
    #[OA\Post(
        path: "/primestatuts/{id}/restore",
        operationId: "restorePrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Restaure un primestatut supprimé",
        description: "Restaure un primestatut depuis la corbeille",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut restauré avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function restore($id)
    {
        $message = 'Restauration d\'un primestatut';

        try {
            $result = $this->primestatutRepository->restore($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Primestatut restauré avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Supprime définitivement un primestatut.
     */
    #[OA\Delete(
        path: "/primestatuts/{id}/force-delete",
        operationId: "forceDeletePrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Supprime définitivement un primestatut",
        description: "Supprime de façon permanente un primestatut de la base de données",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut supprimé définitivement",
                content: new OA\JsonContent(ref: "#/components/schemas/DeleteResponseData")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function forceDelete($id)
    {
        $message = 'Suppression définitive d\'un primestatut';

        try {
            $result = $this->primestatutRepository->forceDelete($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::successDelete('Primestatut supprimé définitivement', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Suppression en masse des primestatuts.
     */
    #[OA\Delete(
        path: "/primestatuts/mass-destroy",
        operationId: "massDestroyPrimestatuts",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Suppression en masse des primestatuts",
        description: "Supprime plusieurs primestatuts à la fois",
        requestBody: new OA\RequestBody(
            description: "IDs des primestatuts à supprimer",
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    "ids" => new OA\Property(
                        property: "ids",
                        type: "array",
                        items: new OA\Items(type: "integer"),
                        description: "Tableau des IDs à supprimer"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatuts supprimés avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/DeleteResponseData")
            ),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function massDestroy(Request $request)
    {
        $message = 'Suppression en masse de primestatuts';

        try {
            $ids = $request->ids;
            $result = $this->primestatutRepository->massDestroy($ids);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($ids)]);

            return Common::successDelete('Primestatuts supprimés avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Active un primestatut.
     */
    #[OA\Post(
        path: "/primestatuts/{id}/enable",
        operationId: "enablePrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Active un primestatut",
        description: "Met le statut du primestatut à actif",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut activé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function enable($id)
    {
        $message = 'Activation d\'un primestatut';

        try {
            $result = $this->primestatutRepository->setStatus($id, 1);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Primestatut activé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Désactive un primestatut.
     */
    #[OA\Post(
        path: "/primestatuts/{id}/disable",
        operationId: "disablePrimestatut",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Désactive un primestatut",
        description: "Met le statut du primestatut à inactif",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Primestatut désactivé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function disable($id)
    {
        $message = 'Désactivation d\'un primestatut';

        try {
            $result = $this->primestatutRepository->setStatus($id, 0);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Primestatut désactivé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Bascule le statut d'un primestatut.
     */
    #[OA\Post(
        path: "/primestatuts/{id}/toggle-status",
        operationId: "togglePrimestatutStatus",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Bascule le statut d'un primestatut",
        description: "Inverse le statut actuel du primestatut (actif/inactif)",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Statut basculé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function toggleStatus($id)
    {
        $message = 'Basculement du statut d\'un primestatut';

        try {
            $result = $this->primestatutRepository->toggleStatus($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Statut basculé avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Génère un lien unique pour un primestatut.
     */
    #[OA\Post(
        path: "/primestatuts/{id}/generate-link",
        operationId: "generatePrimestatutLink",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Génère un lien unique pour un primestatut",
        description: "Crée un lien unique et un QR code pour partager un primestatut",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: "Données pour générer le lien",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/GenerateLinkRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Lien généré avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function generateLink(GenerateLinkRequest $request, $id)
    {
        $message = 'Génération de lien unique pour primestatut';

        try {
            $result = $this->primestatutRepository->generateLink($id, $request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Lien généré avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Vérifie un lien unique.
     */
    #[OA\Post(
        path: "/primestatuts/verify-link",
        operationId: "verifyPrimestatutLink",
        tags: ["Primestatut"],
        summary: "Vérifie un lien unique",
        description: "Valide un token de lien unique pour accéder à un primestatut",
        requestBody: new OA\RequestBody(
            description: "Token de vérification",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/VerifyLinkRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Lien vérifié avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Lien invalide ou expiré"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function verifyLink(VerifyLinkRequest $request)
    {
        $message = 'Vérification de lien unique';

        try {
            $result = $this->primestatutRepository->verifyLink($request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Lien vérifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Participe à un primestatut.
     */
    #[OA\Post(
        path: "/primestatuts/participate",
        operationId: "participatePrimestatut",
        tags: ["Primestatut"],
        summary: "Participe à un primestatut",
        description: "Enregistre la participation d'un utilisateur à un primestatut",
        requestBody: new OA\RequestBody(
            description: "Données de participation",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ParticipateRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Participation enregistrée avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        "message" => new OA\Property(property: "message", type: "string"),
                        "data" => new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Données de validation invalides"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function participate(ParticipateRequest $request)
    {
        $message = 'Participation à un primestatut';

        try {
            $result = $this->primestatutRepository->participate($request->validated());
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Participation enregistrée avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Génère un lien média pour un primestatut.
     */
    #[OA\Post(
        path: "/primestatuts/{id}/generate-media-link",
        operationId: "generatePrimestatutMediaLink",
        tags: ["Primestatut"],
        security: [["JWT" => []]],
        summary: "Génère un lien média pour un primestatut",
        description: "Crée un lien unique pour partager les médias d'un primestatut",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID du primestatut",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lien média généré avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Primestatut")
            ),
            new OA\Response(response: 404, description: "Primestatut non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function generateMediaLink($id)
    {
        $message = 'Génération de lien média';

        try {
            if (!$this->primestatutRepository->get($id)) {
                return Common::error('Aucun primestatut n\'existe à cette référence.', []);
            }
            
            $result = $this->primestatutRepository->generateMediaLink($id);
            $this->logService->trace(['action_name' => $message, 'description' => json_encode($id)]);

            return Common::success('Lien média généré avec succès', $result);
        } catch (\Throwable $th) {
            $this->logService->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
