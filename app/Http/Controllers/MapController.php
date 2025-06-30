<?php

namespace App\Http\Controllers;

use App\Http\Repositories\mapRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MapController
{
    /**
     * The Map repository being queried.
     *
     * @var MapRepository
     */
    protected $mapRepository;

    public function __construct(mapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    /** @OA\Get(
     *      path="/maps",
     *      operationId="Map list",
     *      tags={"Map"},
     *      summary="Return Map data",
     *      description="Get all map",
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
     *          @OA\JsonContent(ref="#/components/schemas/Map"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->getAll($request);

            return Common::success('Journal des maps', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
