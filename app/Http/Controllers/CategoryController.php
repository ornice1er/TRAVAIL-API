<?php

namespace App\Http\Controllers;

use App\Http\Repositories\categoryRepository;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    /**
     * The Category repository being queried.
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    protected $ls;

    public function __construct(categoryRepository $categoryRepository, LogService $ls)
    {
        $this->categoryRepository = $categoryRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/categorys",
     *      operationId="Category list",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Return Category data",
     *      description="Get all category",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Récupération de la liste des catégories';

        try {
            $result = $this->categoryRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/categorys/{id}",
     *      operationId="getCategoryById",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Return Category data",
     *      description="Get category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Récupération de la catégorie';

        try {
            $result = $this->categoryRepository->getById($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys",
     *      operationId="storeCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Store Category data",
     *      description="Create a new category",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Category object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
    public function store(StoreCategoryRequest $request)
    {
        $message = 'Création de la catégorie';

        try {
            $result = $this->categoryRepository->store($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/categorys/{id}",
     *      operationId="updateCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Update Category data",
     *      description="Update category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Category object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
    public function update(UpdateCategoryRequest $request, $id)
    {
        $message = 'Mise à jour de la catégorie';

        try {
            $result = $this->categoryRepository->update($request->all(), $id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all()) . ' - ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/categorys/{id}",
     *      operationId="deleteCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Delete Category data",
     *      description="Delete category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Suppression de la catégorie';

        try {
            $result = $this->categoryRepository->destroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/state",
     *      operationId="changeCategoryState",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Change Category state",
     *      description="Change state of category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Changement de statut de la catégorie';

        try {
            $result = $this->categoryRepository->changeState($request->state, $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - État: ' . $request->state]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/categorys/search",
     *      operationId="searchCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Search Category data",
     *      description="Search categories",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Recherche de catégories';

        try {
            $result = $this->categoryRepository->search($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/up",
     *      operationId="moveCategoryUp",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Move Category up",
     *      description="Move category up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Déplacement de la catégorie vers le haut';

        try {
            $result = $this->categoryRepository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/down",
     *      operationId="moveCategoryDown",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Move Category down",
     *      description="Move category down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Déplacement de la catégorie vers le bas';

        try {
            $result = $this->categoryRepository->down([], $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/publish",
     *      operationId="publishCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Publish Category",
     *      description="Publish category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Publication de la catégorie';

        try {
            $result = $this->categoryRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/unpublish",
     *      operationId="unpublishCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish Category",
     *      description="Unpublish category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Dépublication de la catégorie';

        try {
            $result = $this->categoryRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/archive",
     *      operationId="archiveCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Archive Category",
     *      description="Archive category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Archivage de la catégorie';

        try {
            $result = $this->categoryRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/restore",
     *      operationId="restoreCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Restore Category",
     *      description="Restore category by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
        $message = 'Restauration de la catégorie';

        try {
            $result = $this->categoryRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/categorys/{id}/generate-link",
     *      operationId="generateCategoryLink",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Generate Category Link",
     *      description="Generate QR code link for category",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
        $message = 'Génération du lien QR Code pour la catégorie';

        try {
            $result = $this->categoryRepository->generateLink($id, []);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/categorys/{id}/generate-media-link",
     *      operationId="generateCategoryMediaLink",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Generate Category Media Link",
     *      description="Generate QR code media link for category",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
        $message = 'Génération du lien média QR Code pour la catégorie';

        try {
            $result = $this->categoryRepository->generateMediaLink($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/categorys/verify-link/{code}",
     *      operationId="verifyCategoryLink",
     *      tags={"Category"},
     *      summary="Verify Category Link",
     *      description="Verify QR code link for category",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Category")
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
            $result = $this->categoryRepository->verifyLink($code);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/categorys/{id}/participate",
     *      operationId="participateCategory",
     *      tags={"Category"},
     *      security={{"JWT":{}}},
     *      summary="Participate in Category",
     *      description="Record participation for category",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category id",
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
        $message = 'Participation à la catégorie';

        try {
            $result = $this->categoryRepository->participate($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
