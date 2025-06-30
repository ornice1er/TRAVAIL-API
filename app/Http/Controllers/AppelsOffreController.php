<?php

namespace App\Http\Controllers;

use App\Http\Repositories\appelsOffreRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AppelsOffreController
{
    /**
     * The AppelsOffre repository being queried.
     *
     * @var AppelsOffreRepository
     */
    protected $appelsOffreRepository;

    public function __construct(appelsOffreRepository $appelsOffreRepository)
    {
        $this->appelsOffreRepository = $appelsOffreRepository;
    }

    /** @OA\Get(
     *      path="/appelsOffres",
     *      operationId="AppelsOffre list",
     *      tags={"AppelsOffre"},
     *      summary="Return AppelsOffre data",
     *      description="Get all appelsOffre",
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
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffre"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffre")
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
            $result = $this->appelsOffreRepository->getAll($request);

            return Common::success('Journal des appelsOffres', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
