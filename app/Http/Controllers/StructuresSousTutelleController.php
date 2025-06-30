<?php

namespace App\Http\Controllers;

use App\Http\Repositories\structuresSousTutelleRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StructuresSousTutelleController
{
    /**
     * The StructuresSousTutelle repository being queried.
     *
     * @var StructuresSousTutelleRepository
     */
    protected $structuresSousTutelleRepository;

    public function __construct(structuresSousTutelleRepository $structuresSousTutelleRepository)
    {
        $this->structuresSousTutelleRepository = $structuresSousTutelleRepository;
    }

    /** @OA\Get(
     *      path="/structuresSousTutelles",
     *      operationId="StructuresSousTutelle list",
     *      tags={"StructuresSousTutelle"},
     *      summary="Return StructuresSousTutelle data",
     *      description="Get all structuresSousTutelle",
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
     *          @OA\JsonContent(ref="#/components/schemas/StructuresSousTutelle"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/StructuresSousTutelle")
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
            $result = $this->structuresSousTutelleRepository->getAll($request);

            return Common::success('Journal des structuresSousTutelles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
