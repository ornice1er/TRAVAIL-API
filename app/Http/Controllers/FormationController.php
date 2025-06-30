<?php

namespace App\Http\Controllers;

use App\Http\Repositories\formationRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FormationController
{
    /**
     * The Formation repository being queried.
     *
     * @var FormationRepository
     */
    protected $formationRepository;

    public function __construct(formationRepository $formationRepository)
    {
        $this->formationRepository = $formationRepository;
    }

    /** @OA\Get(
     *      path="/formations",
     *      operationId="Formation list",
     *      tags={"Formation"},
     *      summary="Return Formation data",
     *      description="Get all formation",
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
     *          @OA\JsonContent(ref="#/components/schemas/Formation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Formation")
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
            $result = $this->formationRepository->getAll($request);

            return Common::success('Journal des formations', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
