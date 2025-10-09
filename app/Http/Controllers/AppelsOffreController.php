<?php

namespace App\Http\Controllers;

use App\Http\Repositories\appelsOffreRepository;
use App\Http\Requests\AppelsOffre\StoreAppelsOffreRequest;
use App\Http\Requests\AppelsOffre\UpdateAppelsOffreRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AppelsOffreController extends Controller
{
    /**
     * The AppelsOffre repository being queried.
     *
     * @var AppelsOffreRepository
     */
    protected $appelsOffreRepository;

    protected $ls;

    public function __construct(appelsOffreRepository $appelsOffreRepository, LogService $ls)
    {
        $this->appelsOffreRepository = $appelsOffreRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/appelsOffres",
     *      operationId="AppelsOffre list",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *      summary="Return AppelsOffre data",
     *      description="Get all appelsOffre",
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
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
        $message = 'Récupération de la liste des Appels d\'offre';

        try {
            $result = $this->appelsOffreRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/appelsOffres/{id}",
     *      operationId="AppelsOffre show",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Return one AppelsOffre data",
     *      description="Get AppelsOffre by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
        $message = 'Récupération d\'un Appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Appel d\'offre trouvé', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres",
     *      operationId="AppelsOffre store",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *      summary="Store AppelsOffre data",
     *      description="Create a new AppelsOffre",
     *
     *       @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
    public function store(StoreAppelsOffreRequest $request)
    {
        $message = 'Enregistrement d\'un Appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Appel d\'offre créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/appelsOffres/{id}",
     *      operationId="AppelsOffre update",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *      summary="Update one AppelsOffre data",
     *      description="Update AppelsOffre by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
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
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
    public function update(UpdateAppelsOffreRequest $request, $id)
    {
        $message = 'Mise à jour d\'un Appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour de l\'Appel d\'offre effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/appelsOffres/{id}",
     *      operationId="AppelsOffre Delete",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *      summary="Delete AppelsOffre data",
     *      description="Delete AppelsOffre by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
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
        $message = 'Suppression d\'un Appel d\'offre';

        try {
            $recup = $this->appelsOffreRepository->get($id);

            $result = $this->appelsOffreRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($recup)]);

            return Common::successDelete('Appel d\'offre supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/appelsOffres/{id}/state/{state}",
     *      operationId="AppelsOffre change state",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
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
     *          description="AppelsOffre state",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Change AppelsOffre state",
     *      description="Change AppelsOffre state by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
        $message = 'Changement de l\'état d\'un Appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->setStatus($id, $state);
            $statusMessage = $state == 1 ? 'activé' : 'désactivé';
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Appel d'offre $statusMessage avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres-search",
     *      operationId="AppelsOffre searching",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *      summary="Return list of AppelsOffre respecting term",
     *      description="Get all filtered appelsOffres using term",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *         @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
        $message = 'Filtrage des Appels d\'offre';

        try {
            $term = $request->term;
            $result = $this->appelsOffreRepository->search($term);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Filtrage effectué avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres/{id}/up",
     *      operationId="AppelsOffre transmission up",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Transmit AppelsOffre up",
     *      description="Transmit AppelsOffre to validation level",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function up($id)
    {
        $message = 'Transmission de l\'appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Transmission d'appel d'offre avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres/{id}/down",
     *      operationId="AppelsOffre transmission down",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Transmit AppelsOffre down",
     *      description="Transmit AppelsOffre back to saisie level",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function down($id)
    {
        $message = 'Retour de l\'appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->down($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Retour d'appel d'offre avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres/{id}/publish",
     *      operationId="AppelsOffre publish",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Publish AppelsOffre",
     *      description="Publish AppelsOffre",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function publish($id)
    {
        $message = 'Publication de l\'appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Appel d'offre publié avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres/{id}/unpublish",
     *      operationId="AppelsOffre unpublish",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Unpublish AppelsOffre",
     *      description="Unpublish AppelsOffre",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function unpublish($id)
    {
        $message = 'Dépublication de l\'appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Appel d'offre dépublié avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres/{id}/archive",
     *      operationId="AppelsOffre archive",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Archive AppelsOffre",
     *      description="Archive AppelsOffre",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function archive($id)
    {
        $message = 'Archivage de l\'appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Appel d'offre archivé avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffres/{id}/restore",
     *      operationId="AppelsOffre restore",
     *      tags={"AppelsOffre"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffre ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Restore AppelsOffre",
     *      description="Restore AppelsOffre from archive",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function restore($id)
    {
        $message = 'Restauration de l\'appel d\'offre';

        try {
            $result = $this->appelsOffreRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Appel d'offre restauré avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
