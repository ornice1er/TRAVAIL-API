<?php

namespace App\Http\Controllers;

use App\Http\Repositories\newsletterRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NewsletterController
{
    /**
     * The Newsletter repository being queried.
     *
     * @var NewsletterRepository
     */
    protected $newsletterRepository;

    public function __construct(newsletterRepository $newsletterRepository)
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    /** @OA\Get(
     *      path="/newsletters",
     *      operationId="Newsletter list",
     *      tags={"Newsletter"},
     *      summary="Return Newsletter data",
     *      description="Get all newsletter",
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
     *          @OA\JsonContent(ref="#/components/schemas/Newsletter"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Newsletter")
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
            $result = $this->newsletterRepository->getAll($request);

            return Common::success('Journal des newsletters', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
