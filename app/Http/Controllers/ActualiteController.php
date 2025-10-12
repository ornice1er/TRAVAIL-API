<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ActualiteRepository;
use App\Http\Requests\Actualite\StoreActualiteRequest;
use App\Http\Requests\Actualite\UpdateActualiteRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ActualiteController extends Controller
{
    /**
     * The Actualite repository being queried.
     *
     * @var ActualiteRepository
     */
    protected $actualiteRepository;

    protected $ls;

    public function __construct(ActualiteRepository $actualiteRepository, LogService $ls)
    {
        $this->actualiteRepository = $actualiteRepository;
        $this->ls = $ls;

        //$this->middleware('auth:api')->except(['getNotified', 'show']);

    }

    /** @OA\Get(
     *      path="/agents",
     *      operationId="Actualite list",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Return Actualite data",
     *      description="Get all agents",
     *
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Can be used for filtering data by name",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
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
    public function index(Request $request)
    {
        $message = 'Récupération de la liste des Actualite';

        try {
            $result = $this->actualiteRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/agents/{id}",
     *      operationId="Actualite show",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *
     *  @OA\Parameter(
     *          name="project_id",
     *          in="query",
     *          description="Project ID",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Actualite ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Return one Actualite data",
     *      description="Get Actualite by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
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
    public function show(Request $request, $id)
    {
        $message = 'Récupération d\'un Actualite';

        try {
            $result = $this->actualiteRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Actualite trouvé', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/agents",
     *      operationId="Actualite store",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Store Actualite data",
     *      description="Create a new Actualite",
     *
     *       @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/ActualiteCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
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
    public function store(StoreActualiteRequest $request)
    {
        $message = 'Enregistrement d\'un Actualite';

        try {
            $result = $this->actualiteRepository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Actualite créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/agents/{id}",
     *      operationId="Actualite update",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Update one Actualite data",
     *      description="Update Actualite by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Actualite ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/ActualiteCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
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
    public function update(UpdateActualiteRequest $request, $id)
    {
        $message = 'Mise à jour d\'un Actualite';

        try {
            $result = $this->actualiteRepository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour de Actualite effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/agents/{id}",
     *      operationId="Actualite Delete",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Delete Actualite data",
     *      description="Delete Actualite by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Actualite ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/DeleteResponseData"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/DeleteResponseData")
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
    public function destroy($id)
    {
        $message = 'Suppression de Actualite';

        try {
            $recup = $this->actualiteRepository->get($id);

            $result = $this->actualiteRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($recup)]);

            return Common::successDelete('Actualite supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/agents/{id}/state/{state}",
     *      operationId="Actualite change state",
     *      tags={"Actualite"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Actualite ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="state",
     *          in="path",
     *          description="Actualite state",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Change Actualite state",
     *      description="Change Actualite state by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Actualite")
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
    public function changeState($id, $state)
    {
        $message = 'Changement de l\'état d\'un Actualite';

        try {
            $result = $this->actualiteRepository->setStatus($id, $state);
            $statusMessage = $state == 1 ? 'activé' : 'désactivé';
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Actualite $statusMessage avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }

    }

    /** @OA\Post(
     *      path="/agents-search",
     *      operationId="Actualite searching",
     *      tags={"Actualite"},
     *       security={{"JWT":{}}},
     *      summary="Return list of Actualite respecting term",
     *      description="Get all filtered agents using term",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Actualite"),
     *
     *         @OA\XmlContent(ref="#/components/schemas/Actualite")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Body request",
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/TermSearch")
     *     ),
     *
     * @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * @OA\Response(
     *         response=419,
     *         description="Expired session"
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     *)
     */
    public function search(Request $request)
    {
        $message = 'Filtrage des Actualite';

        try {
            $term = $request->term;
            $result = $this->actualiteRepository->search($term);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Filtrage effectué avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }


     public function up($id)
    {
        $message = 'transmission de l\'actualité';

        try {
            $result = $this->actualiteRepository->up($id);

            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Tranmission d'actualité avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Fait redescendre une actualité dans le workflow.
     */
    #[OA\Patch(
        path: "/actualites/{id}/down",
        operationId: "downActualite",
        tags: ["Actualite"],
        security: [["JWT" => []]],
        summary: "Fait redescendre une actualité",
        description: "Fait passer l'actualité au niveau inférieur dans le workflow",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'actualité",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: "Motif de redescente",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    "motif" => new OA\Property(property: "motif", type: "string", description: "Motif de redescente")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Actualité redescendue avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Actualite")
            ),
            new OA\Response(response: 404, description: "Actualité non trouvée"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function down(Request $request, $id)
    {
        $message = 'Redescente d\'une actualité dans le workflow';

        try {
            $result = $this->actualiteRepository->down($request, $id);
            $this->ls->trace(['action_name' => $message, 'description' => "Actualité ID: {$id}, Motif: " . $request->motif]);

            return Common::success('Actualité redescendue avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Publie une actualité.
     */
    #[OA\Patch(
        path: "/actualites/{id}/publish",
        operationId: "publishActualite",
        tags: ["Actualite"],
        security: [["JWT" => []]],
        summary: "Publie une actualité",
        description: "Rend l'actualité visible au public",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'actualité",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Actualité publiée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Actualite")
            ),
            new OA\Response(response: 404, description: "Actualité non trouvée"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function publish($id)
    {
        $message = 'Publication d\'une actualité';

        try {
            $result = $this->actualiteRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => "Actualité ID: {$id}"]);

            return Common::success('Actualité publiée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Dépublie une actualité.
     */
    #[OA\Patch(
        path: "/actualites/{id}/unpublish",
        operationId: "unpublishActualite",
        tags: ["Actualite"],
        security: [["JWT" => []]],
        summary: "Dépublie une actualité",
        description: "Retire l'actualité de la vue publique",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'actualité",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Actualité dépubliée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Actualite")
            ),
            new OA\Response(response: 404, description: "Actualité non trouvée"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function unpublish($id)
    {
        $message = 'Dépublication d\'une actualité';

        try {
            $result = $this->actualiteRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => "Actualité ID: {$id}"]);

            return Common::success('Actualité dépubliée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Archive une actualité.
     */
    #[OA\Patch(
        path: "/actualites/{id}/archive",
        operationId: "archiveActualite",
        tags: ["Actualite"],
        security: [["JWT" => []]],
        summary: "Archive une actualité",
        description: "Archive l'actualité et la marque comme inactive",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'actualité",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Actualité archivée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Actualite")
            ),
            new OA\Response(response: 404, description: "Actualité non trouvée"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function archive($id)
    {
        $message = 'Archivage d\'une actualité';

        try {
            $result = $this->actualiteRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => "Actualité ID: {$id}"]);

            return Common::success('Actualité archivée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Restaure une actualité archivée.
     */
    #[OA\Patch(
        path: "/actualites/{id}/restore",
        operationId: "restoreActualite",
        tags: ["Actualite"],
        security: [["JWT" => []]],
        summary: "Restaure une actualité",
        description: "Restaure une actualité archivée et la remet en circulation",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'actualité",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Actualité restaurée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Actualite")
            ),
            new OA\Response(response: 404, description: "Actualité non trouvée"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function restore($id)
    {
        $message = 'Restauration d\'une actualité';

        try {
            $result = $this->actualiteRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => "Actualité ID: {$id}"]);

            return Common::success('Actualité restaurée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

}
