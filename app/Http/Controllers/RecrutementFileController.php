<?php

namespace App\Http\Controllers;

use App\Http\Repositories\recrutementFileRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RecrutementFileController
{
    /**
     * The RecrutementFile repository being queried.
     *
     * @var RecrutementFileRepository
     */
    protected $recrutementFileRepository;

    public function __construct(recrutementFileRepository $recrutementFileRepository)
    {
        $this->recrutementFileRepository = $recrutementFileRepository;
    }

    /** @OA\Get(
     *      path="/recrutementFiles",
     *      operationId="RecrutementFile list",
     *      tags={"RecrutementFile"},
     *      summary="Return RecrutementFile data",
     *      description="Get all recrutementFile",
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
     *          @OA\JsonContent(ref="#/components/schemas/RecrutementFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/RecrutementFile")
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
            $result = $this->recrutementFileRepository->getAll($request);

            return Common::success('Journal des recrutementFiles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
