<?php

namespace App\Http\Controllers;

use App\Http\Repositories\stageRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StageController
{
    /**
     * The Stage repository being queried.
     *
     * @var StageRepository
     */
    protected $stageRepository;

    public function __construct(stageRepository $stageRepository)
    {
        $this->stageRepository = $stageRepository;
    }

    /** @OA\Get(
     *      path="/stages",
     *      operationId="Stage list",
     *      tags={"Stage"},
     *      summary="Return Stage data",
     *      description="Get all stage",
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
     *          @OA\JsonContent(ref="#/components/schemas/Stage"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Stage")
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
            $result = $this->stageRepository->getAll($request);

            return Common::success('Journal des stages', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
