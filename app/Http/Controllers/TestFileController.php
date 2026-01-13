<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TestFileRepository;
use App\Http\Requests\TestFile\StoreTestFileRequest;
use App\Http\Requests\TestFile\UpdateTestFileRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TestFileController
{
    /**
     * The TestFile repository being queried.
     *
     * @var TestFileRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(TestFileRepository $testFileRepository, LogService $ls)
    {
        $this->repository = $testFileRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/testFiles",
     *      operationId="TestFile list",
     *      tags={"TestFile"},
     *      summary="Return TestFile data",
     *      description="Get all testFile",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
            $this->ls->trace(['action_name' => 'Journal des testFiles', 'description' => json_encode($request->all())]);

            return Common::success('Journal des testFiles', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des testFiles', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/testFiles/{id}",
     *      operationId="getTestFileById",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Return TestFile data",
     *      description="Get testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles",
     *      operationId="storeTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Store TestFile data",
     *      description="Create a new testFile",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="TestFile object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
    public function store(StoreTestFileRequest $request)
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
     *      path="/testFiles/{id}",
     *      operationId="updateTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Update TestFile data",
     *      description="Update testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="TestFile object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
    public function update(UpdateTestFileRequest $request, $id)
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
     *      path="/testFiles/{id}",
     *      operationId="deleteTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Delete TestFile data",
     *      description="Delete testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/state",
     *      operationId="changeTestFileState",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Change TestFile state",
     *      description="Change state of testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/search",
     *      operationId="searchTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Search TestFile data",
     *      description="Search testFiles",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/up",
     *      operationId="moveTestFileUp",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Move TestFile up",
     *      description="Move testFile up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/down",
     *      operationId="moveTestFileDown",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Move TestFile down",
     *      description="Move testFile down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/publish",
     *      operationId="publishTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Publish TestFile",
     *      description="Publish testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/unpublish",
     *      operationId="unpublishTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish TestFile",
     *      description="Unpublish testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/archive",
     *      operationId="archiveTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Archive TestFile",
     *      description="Archive testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
     *      path="/testFiles/{id}/restore",
     *      operationId="restoreTestFile",
     *      tags={"TestFile"},
     *      security={{"JWT":{}}},
     *      summary="Restore TestFile",
     *      description="Restore testFile by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="TestFile id",
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
     *          @OA\JsonContent(ref="#/components/schemas/TestFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TestFile")
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
