<?php

namespace App\Http\Controllers;

use App\Http\Repositories\mediaRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MediaController
{
    /**
     * The Media repository being queried.
     *
     * @var MediaRepository
     */
    protected $mediaRepository;

    public function __construct(mediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /** @OA\Get(
     *      path="/medias",
     *      operationId="Media list",
     *      tags={"Media"},
     *      summary="Return Media data",
     *      description="Get all media",
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
     *          @OA\JsonContent(ref="#/components/schemas/Media"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Media")
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
            $result = $this->mediaRepository->getAll($request);

            return Common::success('Journal des medias', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
