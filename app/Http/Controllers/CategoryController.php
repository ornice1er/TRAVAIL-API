<?php

namespace App\Http\Controllers;

use App\Http\Repositories\categoryRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryController
{
    /**
     * The Category repository being queried.
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(categoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /** @OA\Get(
     *      path="/categorys",
     *      operationId="Category list",
     *      tags={"Category"},
     *      summary="Return Category data",
     *      description="Get all category",
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
        try {
            $result = $this->categoryRepository->getAll($request);

            return Common::success('Journal des categorys', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
