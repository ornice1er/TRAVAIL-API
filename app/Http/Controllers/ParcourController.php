<?php

namespace App\Http\Controllers;

use App\Http\Repositories\parcourRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ParcourController
{
    /**
     * The Parcour repository being queried.
     *
     * @var ParcourRepository
     */
    protected $parcourRepository;

    public function __construct(parcourRepository $parcourRepository)
    {
        $this->parcourRepository = $parcourRepository;
    }

    /** @OA\Get(
     *      path="/parcours",
     *      operationId="Parcour list",
     *      tags={"Parcour"},
     *      summary="Return Parcour data",
     *      description="Get all parcour",
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
     *          @OA\JsonContent(ref="#/components/schemas/Parcour"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Parcour")
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
            $result = $this->parcourRepository->getAll($request);

            return Common::success('Journal des parcours', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
