<?php

namespace App\Http\Controllers;

use App\Http\Repositories\organigrammeRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrganigrammeController
{
    /**
     * The Organigramme repository being queried.
     *
     * @var OrganigrammeRepository
     */
    protected $organigrammeRepository;

    public function __construct(organigrammeRepository $organigrammeRepository)
    {
        $this->organigrammeRepository = $organigrammeRepository;
    }

    /** @OA\Get(
     *      path="/organigrammes",
     *      operationId="Organigramme list",
     *      tags={"Organigramme"},
     *      summary="Return Organigramme data",
     *      description="Get all organigramme",
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
     *          @OA\JsonContent(ref="#/components/schemas/Organigramme"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Organigramme")
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
            $result = $this->organigrammeRepository->getAll($request);

            return Common::success('Journal des organigrammes', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
