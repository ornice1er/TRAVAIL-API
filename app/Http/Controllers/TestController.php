<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TestRepository;
use App\Http\Requests\Test\StoreTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TestController
{
    /**
     * The Test repository being queried.
     *
     * @var TestRepository
     */
    private TestRepository $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(TestRepository $testRepository, LogService $ls)
    {
        $this->repository = $testRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/tests",
     *      operationId="Test list",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Return Test data",
     *      description="Get all test",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Récupération de la liste des concourss';

        try {
            $result = $this->repository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/tests/{id}",
     *      operationId="getTestById",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Return Test data",
     *      description="Get test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Récupération du concours';

        try {
            $result = $this->repository->getById($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/tests",
     *      operationId="storeTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Store Test data",
     *      description="Create a new test",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Test object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
    public function store(StoreTestRequest $request)
    {
        $message = 'Création du concours';

        try {

                    info('uduud');

            $result = $this->repository->store($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/tests/{id}",
     *      operationId="updateTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Update Test data",
     *      description="Update test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Test object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
    public function update(UpdateTestRequest $request, $id)
    {
        $message = 'Mise à jour du concours';

        try {
            $result = $this->repository->update($request->all(), $id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all()) . ' - ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/tests/{id}",
     *      operationId="deleteTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Delete Test data",
     *      description="Delete test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Suppression du concours';

        try {
            $result = $this->repository->destroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/tests/{id}/state",
     *      operationId="changeTestState",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Change Test state",
     *      description="Change state of test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Changement de statut du concours';

        try {
            $result = $this->repository->changeState($request->state, $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - État: ' . $request->state]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/tests/search",
     *      operationId="searchTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Search Test data",
     *      description="Search tests",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Recherche de concourss';

        try {
            $result = $this->repository->search($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/tests/{id}/up",
     *      operationId="moveTestUp",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Move Test up",
     *      description="Move test up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Déplacement du concours vers le haut';

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
     *      path="/tests/{id}/down",
     *      operationId="moveTestDown",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Move Test down",
     *      description="Move test down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Déplacement du concours vers le bas';

        try {
            $result = $this->repository->down([], $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/tests/{id}/publish",
     *      operationId="publishTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Publish Test",
     *      description="Publish test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Publication du concours';

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
     *      path="/tests/{id}/unpublish",
     *      operationId="unpublishTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish Test",
     *      description="Unpublish test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Dépublication du concours';

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
     *      path="/tests/{id}/archive",
     *      operationId="archiveTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Archive Test",
     *      description="Archive test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Archivage du concours';

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
     *      path="/tests/{id}/restore",
     *      operationId="restoreTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Restore Test",
     *      description="Restore test by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
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
        $message = 'Restauration du concours';

        try {
            $result = $this->repository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/tests/{id}/generate-link",
     *      operationId="generateTestLink",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Generate Test Link",
     *      description="Generate QR code link for test",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
    public function generateLink($id)
    {
        $message = 'Génération du lien QR Code pour le concours';

        try {
            $result = $this->repository->generateLink($id, []);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/tests/{id}/generate-media-link",
     *      operationId="generateTestMediaLink",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Generate Test Media Link",
     *      description="Generate QR code media link for test",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
    public function generateMediaLink($id)
    {
        $message = 'Génération du lien média QR Code pour le concours';

        try {
            $result = $this->repository->generateMediaLink($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/tests/verify-link/{code}",
     *      operationId="verifyTestLink",
     *      tags={"Test"},
     *      summary="Verify Test Link",
     *      description="Verify QR code link for test",
     *
     *      @OA\Parameter(
     *          name="code",
     *          in="path",
     *          description="Verification code",
     *          required=true,
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
     *          @OA\JsonContent(ref="#/components/schemas/Test"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Test")
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
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
    public function verifyLink($code)
    {
        $message = 'Vérification du lien QR Code';

        try {
            $result = $this->repository->verifyLink($code);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/tests/{id}/participate",
     *      operationId="participateTest",
     *      tags={"Test"},
     *      security={{"JWT":{}}},
     *      summary="Participate in Test",
     *      description="Record participation for test",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Test id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
    public function participate($id)
    {
        $message = 'Participation au concours';

        try {
            $result = $this->repository->participate($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
