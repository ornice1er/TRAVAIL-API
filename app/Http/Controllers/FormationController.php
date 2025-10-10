<?php

namespace App\Http\Controllers;

use App\Http\Repositories\formationRepository;
use App\Http\Requests\Formation\StoreFormationRequest;
use App\Http\Requests\Formation\UpdateFormationRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FormationController
{
    /**
     * The Formation repository being queried.
     *
     * @var FormationRepository
     */
    protected $formationRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(formationRepository $formationRepository, LogService $ls)
    {
        $this->formationRepository = $formationRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/formations",
     *      operationId="Formation list",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Return Formation data",
     *      description="Get all formation",
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
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        try {
            $result = $this->formationRepository->getAll($request);
            $this->ls->trace(['action_name' => 'Journal des formations', 'description' => json_encode($request->all())]);

            return Common::success('Journal des formations', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des formations', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/formations/{id}",
     *      operationId="getFormationById",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Return Formation data",
     *      description="Get formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
    public function show($id)
    {
        $message = 'Récupération de la formation';

        try {
            $result = $this->formationRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations",
     *      operationId="storeFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Store Formation data",
     *      description="Create a new formation",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Formation object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function store(StoreFormationRequest $request)
    {
        $message = 'Création de la formation';

        try {
            $result = $this->formationRepository->makeStore($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/formations/{id}",
     *      operationId="updateFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Update Formation data",
     *      description="Update formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Formation object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
    public function update(UpdateFormationRequest $request, $id)
    {
        $message = 'Mise à jour de la formation';

        try {
            $result = $this->formationRepository->makeUpdate($id, $request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all()) . ' - ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/formations/{id}",
     *      operationId="deleteFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Delete Formation data",
     *      description="Delete formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        $message = 'Suppression de la formation';

        try {
            $result = $this->formationRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/state",
     *      operationId="changeFormationState",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Change Formation state",
     *      description="Change state of formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="State to change to",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="state",
     *                  type="string",
     *                  description="New state"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
    public function changeState(Request $request, $id)
    {
        $message = 'Changement de statut de la formation';

        try {
            $result = $this->formationRepository->setStatus($id, $request->input('state'));
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - State: ' . $request->input('state')]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/formations/search",
     *      operationId="searchFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Search Formation data",
     *      description="Search formations",
     *
     *      @OA\Parameter(
     *          name="keyword",
     *          in="query",
     *          description="Search keyword",
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
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
    public function search(Request $request)
    {
        $message = 'Recherche de formations';

        try {
            $result = $this->formationRepository->search($request->input('keyword', ''));
            $this->ls->trace(['action_name' => $message, 'description' => 'Keyword: ' . $request->input('keyword', '')]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/up",
     *      operationId="moveFormationUp",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Move Formation up",
     *      description="Move formation up in workflow (transmit to validation)",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        $message = 'Transmission de la formation vers validation';

        try {
            $result = $this->formationRepository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/down",
     *      operationId="moveFormationDown",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Move Formation down",
     *      description="Move formation down in workflow (return to saisie)",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=false,
     *          description="Optional motif for rejection",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="motif",
     *                  type="string",
     *                  description="Reason for rejection"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
    public function down(Request $request, $id)
    {
        $message = 'Retour de la formation vers saisie';

        try {
            $result = $this->formationRepository->down($request, $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - Motif: ' . $request->input('motif', '')]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/publish",
     *      operationId="publishFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Publish Formation",
     *      description="Publish formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        $message = 'Publication de la formation';

        try {
            $result = $this->formationRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/unpublish",
     *      operationId="unpublishFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish Formation",
     *      description="Unpublish formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        $message = 'Dépublication de la formation';

        try {
            $result = $this->formationRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/archive",
     *      operationId="archiveFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Archive Formation",
     *      description="Archive formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        $message = 'Archivage de la formation';

        try {
            $result = $this->formationRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/formations/{id}/restore",
     *      operationId="restoreFormation",
     *      tags={"Formation"},
     *      security={{"JWT":{}}},
     *      summary="Restore Formation",
     *      description="Restore formation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Formation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
        $message = 'Restauration de la formation';

        try {
            $result = $this->formationRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
