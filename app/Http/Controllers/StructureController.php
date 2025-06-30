<?php

namespace App\Http\Controllers;

use App\Http\Repositories\structureRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StructureController
{
    /**
     * The Structure repository being queried.
     *
     * @var StructureRepository
     */
    protected $structureRepository;

    public function __construct(structureRepository $structureRepository)
    {
        $this->structureRepository = $structureRepository;
    }

    /** @OA\Get(
     *      path="/structures",
     *      operationId="Structure list",
     *      tags={"Structure"},
     *      summary="Return Structure data",
     *      description="Get all structure",
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
     *          @OA\JsonContent(ref="#/components/schemas/Structure"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Structure")
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
            $result = $this->structureRepository->getAll($request);

            return Common::success('Journal des structures', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
