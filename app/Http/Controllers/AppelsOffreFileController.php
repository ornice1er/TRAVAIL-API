<?php

namespace App\Http\Controllers;

use App\Http\Repositories\appelsOffreFileRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AppelsOffreFileController
{
    /**
     * The AppelsOffreFile repository being queried.
     *
     * @var AppelsOffreFileRepository
     */
    protected $appelsOffreFileRepository;

    public function __construct(appelsOffreFileRepository $appelsOffreFileRepository)
    {
        $this->appelsOffreFileRepository = $appelsOffreFileRepository;
    }

    /** @OA\Get(
     *      path="/appelsOffreFiles",
     *      operationId="AppelsOffreFile list",
     *      tags={"AppelsOffreFile"},
     *      summary="Return AppelsOffreFile data",
     *      description="Get all appelsOffreFile",
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
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
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
            $result = $this->appelsOffreFileRepository->getAll($request);

            return Common::success('Journal des appelsOffreFiles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
