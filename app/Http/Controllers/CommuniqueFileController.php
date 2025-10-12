<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CommuniquesFileRepository;
use App\Http\Requests\CommuniquesFile\StoreCommuniquesFileRequest;
use App\Http\Requests\CommuniquesFile\UpdateCommuniquesFileRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CommuniquesFileController
{
    /**
     * The CommuniquesFile repository being queried.
     *
     * @var CommuniquesFileRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(CommuniquesFileRepository $communiquesFileRepository, LogService $ls)
    {
        $this->repository = $communiquesFileRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/communiquesFiles",
     *      operationId="CommuniquesFile list",
     *      tags={"CommuniquesFile"},
     *      summary="Return CommuniquesFile data",
     *      description="Get all communiquesFile",
     *
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Can be used for filtering data by user id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
            $result = $this->repository->getAll($request);
            $this->ls->trace(['action_name' => 'Journal des communiquesFiles', 'description' => json_encode($request->all())]);

            return Common::success('Journal des communiquesFiles', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des communiquesFiles', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiquesFiles/{id}",
     *      operationId="getCommuniquesFileById",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Return CommuniquesFile data",
     *      description="Get communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Récupération du fichier de communiqué';

        try {
            $result = $this->repository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles",
     *      operationId="storeCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Store CommuniquesFile data",
     *      description="Create a new communiquesFile",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="CommuniquesFile object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
    public function store(StoreCommuniquesFileRequest $request)
    {
        $message = 'Création du fichier de communiqué';

        try {
            $result = $this->repository->makeStore($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/communiquesFiles/{id}",
     *      operationId="updateCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Update CommuniquesFile data",
     *      description="Update communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="CommuniquesFile object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
    public function update(UpdateCommuniquesFileRequest $request, $id)
    {
        $message = 'Mise à jour du fichier de communiqué';

        try {
            $result = $this->repository->makeUpdate($id, $request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all()) . ' - ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/communiquesFiles/{id}",
     *      operationId="deleteCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Delete CommuniquesFile data",
     *      description="Delete communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Suppression du fichier de communiqué';

        try {
            $result = $this->repository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/state",
     *      operationId="changeCommuniquesFileState",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Change CommuniquesFile state",
     *      description="Change state of communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Changement de statut du fichier de communiqué';

        try {
            $result = $this->repository->setStatus($id, $request->input('state'));
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - State: ' . $request->input('state')]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiquesFiles/search",
     *      operationId="searchCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Search CommuniquesFile data",
     *      description="Search communiquesFiles",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Recherche de fichiers de communiqués';

        try {
            $result = $this->repository->search($request->input('keyword', ''));
            $this->ls->trace(['action_name' => $message, 'description' => 'Keyword: ' . $request->input('keyword', '')]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/up",
     *      operationId="moveCommuniquesFileUp",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Move CommuniquesFile up",
     *      description="Move communiquesFile up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Déplacement du fichier de communiqué vers le haut';

        try {
            $result = $this->repository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/down",
     *      operationId="moveCommuniquesFileDown",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Move CommuniquesFile down",
     *      description="Move communiquesFile down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Déplacement du fichier de communiqué vers le bas';

        try {
            $result = $this->repository->down(request(), $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/publish",
     *      operationId="publishCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Publish CommuniquesFile",
     *      description="Publish communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Publication du fichier de communiqué';

        try {
            $result = $this->repository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/unpublish",
     *      operationId="unpublishCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish CommuniquesFile",
     *      description="Unpublish communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Dépublication du fichier de communiqué';

        try {
            $result = $this->repository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/archive",
     *      operationId="archiveCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Archive CommuniquesFile",
     *      description="Archive communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Archivage du fichier de communiqué';

        try {
            $result = $this->repository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiquesFiles/{id}/restore",
     *      operationId="restoreCommuniquesFile",
     *      tags={"CommuniquesFile"},
     *      security={{"JWT":{}}},
     *      summary="Restore CommuniquesFile",
     *      description="Restore communiquesFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CommuniquesFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
        $message = 'Restauration du fichier de communiqué';

        try {
            $result = $this->repository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
