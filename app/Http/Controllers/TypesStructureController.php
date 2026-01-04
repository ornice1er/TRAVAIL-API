<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TypeStructureRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TypeStructureController
{
    /**
     * The TypeStructure repository being queried.
     *
     * @var TypeStructureRepository
     */
    protected $TypeStructureRepository;

    public function __construct(TypeStructureRepository $TypeStructureRepository)
    {
        $this->TypeStructureRepository = $TypeStructureRepository;
    }

    /** @OA\Get(
     *      path="/TypeStructures",
     *      operationId="TypeStructure list",
     *      tags={"TypeStructure"},
     *      summary="Return TypeStructure data",
     *      description="Get all TypeStructure",
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
     *          @OA\JsonContent(ref="#/components/schemas/TypeStructure"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/TypeStructure")
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
            $result = $this->TypeStructureRepository->getAll($request);

            return Common::success('Journal des TypeStructures', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
