<?php

namespace App\Http\Controllers;

use App\Http\Repositories\liensUtileRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LiensUtileController
{
    /**
     * The LiensUtile repository being queried.
     *
     * @var LiensUtileRepository
     */
    protected $liensUtileRepository;

    public function __construct(liensUtileRepository $liensUtileRepository)
    {
        $this->liensUtileRepository = $liensUtileRepository;
    }

    /** @OA\Get(
     *      path="/liensUtiles",
     *      operationId="LiensUtile list",
     *      tags={"LiensUtile"},
     *      summary="Return LiensUtile data",
     *      description="Get all liensUtile",
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
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/LiensUtile")
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
            $result = $this->liensUtileRepository->getAll($request);

            return Common::success('Journal des liensUtiles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
