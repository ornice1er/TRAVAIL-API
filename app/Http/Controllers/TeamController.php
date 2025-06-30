<?php

namespace App\Http\Controllers;

use App\Http\Repositories\teamRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TeamController
{
    /**
     * The Team repository being queried.
     *
     * @var TeamRepository
     */
    protected $teamRepository;

    public function __construct(teamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /** @OA\Get(
     *      path="/teams",
     *      operationId="Team list",
     *      tags={"Team"},
     *      summary="Return Team data",
     *      description="Get all team",
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
     *          @OA\JsonContent(ref="#/components/schemas/Team"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Team")
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
            $result = $this->teamRepository->getAll($request);

            return Common::success('Journal des teams', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
