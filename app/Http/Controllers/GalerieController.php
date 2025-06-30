<?php

namespace App\Http\Controllers;

use App\Http\Repositories\galerieRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class GalerieController
{
    /**
     * The Galerie repository being queried.
     *
     * @var GalerieRepository
     */
    protected $galerieRepository;

    public function __construct(galerieRepository $galerieRepository)
    {
        $this->galerieRepository = $galerieRepository;
    }

    /** @OA\Get(
     *      path="/galeries",
     *      operationId="Galerie list",
     *      tags={"Galerie"},
     *      summary="Return Galerie data",
     *      description="Get all galerie",
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
     *          @OA\JsonContent(ref="#/components/schemas/Galerie"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->getAll($request);

            return Common::success('Journal des galeries', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
