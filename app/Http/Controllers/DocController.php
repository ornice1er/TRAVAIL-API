<?php

namespace App\Http\Controllers;

use App\Http\Repositories\docRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DocController
{
    /**
     * The Doc repository being queried.
     *
     * @var DocRepository
     */
    protected $docRepository;

    public function __construct(docRepository $docRepository)
    {
        $this->docRepository = $docRepository;
    }

    /** @OA\Get(
     *      path="/docs",
     *      operationId="Doc list",
     *      tags={"Doc"},
     *      summary="Return Doc data",
     *      description="Get all doc",
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
     *          @OA\JsonContent(ref="#/components/schemas/Doc"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Doc")
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
            $result = $this->docRepository->getAll($request);

            return Common::success('Journal des docs', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
