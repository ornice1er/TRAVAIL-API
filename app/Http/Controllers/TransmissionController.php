<?php

namespace App\Http\Controllers;

use App\Http\Repositories\transmissionRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TransmissionController
{
    /**
     * The Transmission repository being queried.
     *
     * @var TransmissionRepository
     */
    protected $transmissionRepository;

    public function __construct(transmissionRepository $transmissionRepository)
    {
        $this->transmissionRepository = $transmissionRepository;
    }

    /** @OA\Get(
     *      path="/transmissions",
     *      operationId="Transmission list",
     *      tags={"Transmission"},
     *      summary="Return Transmission data",
     *      description="Get all transmission",
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
     *          @OA\JsonContent(ref="#/components/schemas/Transmission"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Transmission")
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
            $result = $this->transmissionRepository->getAll($request);

            return Common::success('Journal des transmissions', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
