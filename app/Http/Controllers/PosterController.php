<?php

namespace App\Http\Controllers;

use App\Http\Repositories\posterRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PosterController
{
    /**
     * The Poster repository being queried.
     *
     * @var PosterRepository
     */
    protected $posterRepository;

    public function __construct(posterRepository $posterRepository)
    {
        $this->posterRepository = $posterRepository;
    }

    /** @OA\Get(
     *      path="/posters",
     *      operationId="Poster list",
     *      tags={"Poster"},
     *      summary="Return Poster data",
     *      description="Get all poster",
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
     *          @OA\JsonContent(ref="#/components/schemas/Poster"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Poster")
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
            $result = $this->posterRepository->getAll($request);

            return Common::success('Journal des posters', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
