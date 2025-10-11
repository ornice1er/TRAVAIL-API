<?php

namespace App\Http\Controllers;

use App\Http\Repositories\AofRepository;
use App\Http\Requests\Aof\StoreAofRequest;
use App\Http\Requests\Aof\UpdateAofRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AofController extends Controller
{
    /**
     * The Aof repository being queried.
     *
     * @var AofRepository
     */
    protected $aofRepository;

    protected $ls;

    public function __construct(AofRepository $aofRepository, LogService $ls)
    {
        $this->aofRepository = $aofRepository;
        $this->ls = $ls;

        //$this->middleware('auth:api')->except(['getNotified', 'show']);

    }

    /** @OA\Get(
     *      path="/agents",
     *      operationId="Aof list",
     *      tags={"Aof"},
     *       security={{"JWT":{}}},
     *      summary="Return Aof data",
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
     *          @OA\JsonContent(ref="#/components/schemas/Aof"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Aof")
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
        $message = 'Récupération de la liste des Aof';

        try {
            $result = $this->aofRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/agents/{id}",
     *      operationId="Aof show",
     *      tags={"Aof"},
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
     *          description="Aof ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Return one Aof data",
     *      description="Get Aof by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Aof"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Aof")
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
        $message = 'Récupération d\'un Aof';

        try {
            $result = $this->aofRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Aof trouvé', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/agents",
     *      operationId="Aof store",
     *      tags={"Aof"},
     *       security={{"JWT":{}}},
     *      summary="Store Aof data",
     *      description="Create a new Aof",
     *
     *       @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/AofCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Aof"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Aof")
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
    public function store(StoreAofRequest $request)
    {
        $message = 'Enregistrement d\'un Aof';

        try {
            $result = $this->aofRepository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Aof créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/agents/{id}",
     *      operationId="Aof update",
     *      tags={"Aof"},
     *       security={{"JWT":{}}},
     *      summary="Update one Aof data",
     *      description="Update Aof by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Aof ID",
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
     *          @OA\JsonContent(ref="#/components/schemas/AofCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Aof"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Aof")
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
    public function update(UpdateAofRequest $request, $id)
    {
        $message = 'Mise à jour d\'un Aof';

        try {
            $result = $this->aofRepository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour de Aof effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/agents/{id}",
     *      operationId="Aof Delete",
     *      tags={"Aof"},
     *       security={{"JWT":{}}},
     *      summary="Delete Aof data",
     *      description="Delete Aof by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Aof ID",
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
        $message = 'Suppression de Aof';

        try {
            $recup = $this->aofRepository->get($id);

            $result = $this->aofRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($recup)]);

            return Common::successDelete('Aof supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/agents/{id}/state/{state}",
     *      operationId="Aof change state",
     *      tags={"Aof"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Aof ID",
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
     *          description="Aof state",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Change Aof state",
     *      description="Change Aof state by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Aof"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Aof")
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
        $message = 'Changement de l\'état d\'un Aof';

        try {
            $result = $this->aofRepository->setStatus($id, $state);
            $statusMessage = $state == 1 ? 'activé' : 'désactivé';
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Aof $statusMessage avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }

    }

    /** @OA\Post(
     *      path="/agents-search",
     *      operationId="Aof searching",
     *      tags={"Aof"},
     *       security={{"JWT":{}}},
     *      summary="Return list of Aof respecting term",
     *      description="Get all filtered agents using term",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Aof"),
     *
     *         @OA\XmlContent(ref="#/components/schemas/Aof")
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
        $message = 'Filtrage des Aof';

        try {
            $term = $request->term;
            $result = $this->aofRepository->search($term);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Filtrage effectué avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }


     public function up($id)
    {
        $message = 'transmission de l\'aof';

        try {
            $result = $this->aofRepository->up($id);

            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Transmission d'aof avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Fait redescendre un AOF dans le workflow.
     */
    #[OA\Patch(
        path: "/aofs/{id}/down",
        operationId: "downAof",
        tags: ["Aof"],
        security: [["JWT" => []]],
        summary: "Fait redescendre un AOF",
        description: "Fait passer l'AOF au niveau inférieur dans le workflow",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'AOF",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "AOF redescendu avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Aof")
            ),
            new OA\Response(response: 404, description: "AOF non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function down($id)
    {
        $message = 'Redescente d\'un AOF dans le workflow';

        try {
            $result = $this->aofRepository->down($id);
            $this->ls->trace(['action_name' => $message, 'description' => "AOF ID: {$id}"]);

            return Common::success('AOF redescendu avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Publie un AOF.
     */
    #[OA\Patch(
        path: "/aofs/{id}/publish",
        operationId: "publishAof",
        tags: ["Aof"],
        security: [["JWT" => []]],
        summary: "Publie un AOF",
        description: "Rend l'AOF visible au public",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'AOF",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "AOF publié avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Aof")
            ),
            new OA\Response(response: 404, description: "AOF non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function publish($id)
    {
        $message = 'Publication d\'un AOF';

        try {
            $result = $this->aofRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => "AOF ID: {$id}"]);

            return Common::success('AOF publié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Dépublie un AOF.
     */
    #[OA\Patch(
        path: "/aofs/{id}/unpublish",
        operationId: "unpublishAof",
        tags: ["Aof"],
        security: [["JWT" => []]],
        summary: "Dépublie un AOF",
        description: "Retire l'AOF de la vue publique",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'AOF",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "AOF dépublié avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Aof")
            ),
            new OA\Response(response: 404, description: "AOF non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function unpublish($id)
    {
        $message = 'Dépublication d\'un AOF';

        try {
            $result = $this->aofRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => "AOF ID: {$id}"]);

            return Common::success('AOF dépublié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Archive un AOF.
     */
    #[OA\Patch(
        path: "/aofs/{id}/archive",
        operationId: "archiveAof",
        tags: ["Aof"],
        security: [["JWT" => []]],
        summary: "Archive un AOF",
        description: "Archive l'AOF et le marque comme inactif",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'AOF",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "AOF archivé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Aof")
            ),
            new OA\Response(response: 404, description: "AOF non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function archive($id)
    {
        $message = 'Archivage d\'un AOF';

        try {
            $result = $this->aofRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => "AOF ID: {$id}"]);

            return Common::success('AOF archivé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * Restaure un AOF archivé.
     */
    #[OA\Patch(
        path: "/aofs/{id}/restore",
        operationId: "restoreAof",
        tags: ["Aof"],
        security: [["JWT" => []]],
        summary: "Restaure un AOF",
        description: "Restaure un AOF archivé et le remet en circulation",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de l'AOF",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "AOF restauré avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Aof")
            ),
            new OA\Response(response: 404, description: "AOF non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function restore($id)
    {
        $message = 'Restauration d\'un AOF';

        try {
            $result = $this->aofRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => "AOF ID: {$id}"]);

            return Common::success('AOF restauré avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

}
