<?php

namespace App\Http\Controllers;

use App\Http\Repositories\communiqueRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CommuniqueController
{
    /**
     * The Communique repository being queried.
     *
     * @var CommuniqueRepository
     */
    protected $communiqueRepository;

    public function __construct(communiqueRepository $communiqueRepository)
    {
        $this->communiqueRepository = $communiqueRepository;
    }

    /** @OA\Get(
     *      path="/communiques",
     *      operationId="Communique list",
     *      tags={"Communique"},
     *      summary="Return Communique data",
     *      description="Get all communique",
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
     *          @OA\JsonContent(ref="#/components/schemas/Communique"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Communique")
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
            $result = $this->communiqueRepository->getAll($request);

            return Common::success('Journal des communiques', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
