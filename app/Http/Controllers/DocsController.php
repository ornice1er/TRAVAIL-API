<?php

namespace App\Http\Controllers;

use App\Http\Repositories\DocRepository;
use App\Http\Requests\Doc\StoreDocRequest;
use App\Http\Requests\Doc\UpdateDocRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DocsController
{
    /**
     * The Doc repository being queried.
     *
     * @var DocRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(DocRepository $docRepository, LogService $ls)
    {
        $this->repository = $docRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/docs",
     *      operationId="Doc list",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Return Doc data",
     *      description="Get all doc",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
            $this->ls->trace(['action_name' => 'Journal des docs', 'description' => json_encode($request->all())]);

            return Common::success('Journal des docs', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des docs', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/docs/{id}",
     *      operationId="getDocById",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Return Doc data",
     *      description="Get doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Récupération du document';

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
     *      path="/docs",
     *      operationId="storeDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Store Doc data",
     *      description="Create a new doc",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Doc object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
    public function store(StoreDocRequest $request)
    {
        $message = 'Création du document';

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
     *      path="/docs/{id}",
     *      operationId="updateDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Update Doc data",
     *      description="Update doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Doc object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
    public function update(UpdateDocRequest $request, $id)
    {
        $message = 'Mise à jour du document';

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
     *      path="/docs/{id}",
     *      operationId="deleteDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Delete Doc data",
     *      description="Delete doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Suppression du document';

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
     *      path="/docs/{id}/state",
     *      operationId="changeDocState",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Change Doc state",
     *      description="Change state of doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Changement de statut du document';

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
     *      path="/docs/search",
     *      operationId="searchDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Search Doc data",
     *      description="Search docs",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Recherche de documents';

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
     *      path="/docs/{id}/up",
     *      operationId="moveDocUp",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Move Doc up",
     *      description="Move doc up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Déplacement du document vers le haut';

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
     *      path="/docs/{id}/down",
     *      operationId="moveDocDown",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Move Doc down",
     *      description="Move doc down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Déplacement du document vers le bas';

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
     *      path="/docs/{id}/publish",
     *      operationId="publishDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Publish Doc",
     *      description="Publish doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Publication du document';

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
     *      path="/docs/{id}/unpublish",
     *      operationId="unpublishDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish Doc",
     *      description="Unpublish doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Dépublication du document';

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
     *      path="/docs/{id}/archive",
     *      operationId="archiveDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Archive Doc",
     *      description="Archive doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Archivage du document';

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
     *      path="/docs/{id}/restore",
     *      operationId="restoreDoc",
     *      tags={"Doc"},
     *      security={{"JWT":{}}},
     *      summary="Restore Doc",
     *      description="Restore doc by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Doc id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
        $message = 'Restauration du document';

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
