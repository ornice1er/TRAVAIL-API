<?php

namespace App\Http\Controllers;

use App\Http\Repositories\motRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MotController
{
    /**
     * The Mot repository being queried.
     *
     * @var MotRepository
     */
    protected $motRepository;

    public function __construct(motRepository $motRepository)
    {
        $this->motRepository = $motRepository;
    }

    /** @OA\Get(
     *      path="/mots",
     *      operationId="Mot list",
     *      tags={"Mot"},
     *      summary="Return Mot data",
     *      description="Get all mot",
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
     *          @OA\JsonContent(ref="#/components/schemas/Mot"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Mot")
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
            $result = $this->motRepository->getAll($request);

            return Common::success('Journal des mots', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
