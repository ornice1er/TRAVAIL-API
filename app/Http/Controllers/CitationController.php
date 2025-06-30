<?php

namespace App\Http\Controllers;

use App\Http\Repositories\citationRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CitationController
{
    /**
     * The Citation repository being queried.
     *
     * @var CitationRepository
     */
    protected $citationRepository;

    public function __construct(citationRepository $citationRepository)
    {
        $this->citationRepository = $citationRepository;
    }

    /** @OA\Get(
     *      path="/citations",
     *      operationId="Citation list",
     *      tags={"Citation"},
     *      summary="Return Citation data",
     *      description="Get all citation",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
            $result = $this->citationRepository->getAll($request);

            return Common::success('Journal des citations', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
