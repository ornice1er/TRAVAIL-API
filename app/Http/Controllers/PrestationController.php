<?php

namespace App\Http\Controllers;

use App\Http\Repositories\prestationRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PrestationController
{
    /**
     * The Prestation repository being queried.
     *
     * @var PrestationRepository
     */
    protected $prestationRepository;

    public function __construct(prestationRepository $prestationRepository)
    {
        $this->prestationRepository = $prestationRepository;
    }

    /** @OA\Get(
     *      path="/prestations",
     *      operationId="Prestation list",
     *      tags={"Prestation"},
     *      summary="Return Prestation data",
     *      description="Get all prestation",
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
     *          @OA\JsonContent(ref="#/components/schemas/Prestation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Prestation")
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
            $result = $this->prestationRepository->getAll($request);

            return Common::success('Journal des prestations', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
