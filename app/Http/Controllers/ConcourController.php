<?php

namespace App\Http\Controllers;

use App\Http\Repositories\concourRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ConcourController
{
    /**
     * The Concour repository being queried.
     *
     * @var ConcourRepository
     */
    protected $concourRepository;

    public function __construct(concourRepository $concourRepository)
    {
        $this->concourRepository = $concourRepository;
    }

    /** @OA\Get(
     *      path="/concours",
     *      operationId="Concour list",
     *      tags={"Concour"},
     *      summary="Return Concour data",
     *      description="Get all concour",
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
     *          @OA\JsonContent(ref="#/components/schemas/Concour"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Concour")
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
            $result = $this->concourRepository->getAll($request);

            return Common::success('Journal des concours', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
