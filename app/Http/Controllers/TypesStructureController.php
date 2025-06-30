<?php

namespace App\Http\Controllers;

use App\Http\Repositories\typesStructureRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TypesStructureController
{
    /**
     * The TypesStructure repository being queried.
     *
     * @var TypesStructureRepository
     */
    protected $typesStructureRepository;

    public function __construct(typesStructureRepository $typesStructureRepository)
    {
        $this->typesStructureRepository = $typesStructureRepository;
    }

    /** @OA\Get(
     *      path="/typesStructures",
     *      operationId="TypesStructure list",
     *      tags={"TypesStructure"},
     *      summary="Return TypesStructure data",
     *      description="Get all typesStructure",
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
     *          @OA\JsonContent(ref="#/components/schemas/TypesStructure"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TypesStructure")
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
            $result = $this->typesStructureRepository->getAll($request);

            return Common::success('Journal des typesStructures', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
