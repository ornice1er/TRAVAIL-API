<?php

namespace App\Http\Controllers;

use App\Http\Repositories\legendeRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LegendeController
{
    /**
     * The Legende repository being queried.
     *
     * @var LegendeRepository
     */
    protected $legendeRepository;

    public function __construct(legendeRepository $legendeRepository)
    {
        $this->legendeRepository = $legendeRepository;
    }

    /** @OA\Get(
     *      path="/legendes",
     *      operationId="Legende list",
     *      tags={"Legende"},
     *      summary="Return Legende data",
     *      description="Get all legende",
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
     *          @OA\JsonContent(ref="#/components/schemas/Legende"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Legende")
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
            $result = $this->legendeRepository->getAll($request);

            return Common::success('Journal des legendes', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
