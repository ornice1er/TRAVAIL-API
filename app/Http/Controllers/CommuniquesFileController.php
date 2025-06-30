<?php

namespace App\Http\Controllers;

use App\Http\Repositories\communiquesFileRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CommuniquesFileController
{
    /**
     * The CommuniquesFile repository being queried.
     *
     * @var CommuniquesFileRepository
     */
    protected $communiquesFileRepository;

    public function __construct(communiquesFileRepository $communiquesFileRepository)
    {
        $this->communiquesFileRepository = $communiquesFileRepository;
    }

    /** @OA\Get(
     *      path="/communiquesFiles",
     *      operationId="CommuniquesFile list",
     *      tags={"CommuniquesFile"},
     *      summary="Return CommuniquesFile data",
     *      description="Get all communiquesFile",
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
     *          @OA\JsonContent(ref="#/components/schemas/CommuniquesFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/CommuniquesFile")
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
            $result = $this->communiquesFileRepository->getAll($request);

            return Common::success('Journal des communiquesFiles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
