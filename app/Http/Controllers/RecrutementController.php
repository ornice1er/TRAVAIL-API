<?php

namespace App\Http\Controllers;

use App\Http\Repositories\recrutementRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RecrutementController
{
    /**
     * The Recrutement repository being queried.
     *
     * @var RecrutementRepository
     */
    protected $recrutementRepository;

    public function __construct(recrutementRepository $recrutementRepository)
    {
        $this->recrutementRepository = $recrutementRepository;
    }

    /** @OA\Get(
     *      path="/recrutements",
     *      operationId="Recrutement list",
     *      tags={"Recrutement"},
     *      summary="Return Recrutement data",
     *      description="Get all recrutement",
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
     *          @OA\JsonContent(ref="#/components/schemas/Recrutement"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Recrutement")
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
            $result = $this->recrutementRepository->getAll($request);

            return Common::success('Journal des recrutements', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
