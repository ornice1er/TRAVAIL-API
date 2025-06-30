<?php

namespace App\Http\Controllers;

use App\Http\Repositories\notificationRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NotificationController
{
    /**
     * The Notification repository being queried.
     *
     * @var NotificationRepository
     */
    protected $notificationRepository;

    public function __construct(notificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /** @OA\Get(
     *      path="/notifications",
     *      operationId="Notification list",
     *      tags={"Notification"},
     *      summary="Return Notification data",
     *      description="Get all notification",
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
     *          @OA\JsonContent(ref="#/components/schemas/Notification"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Notification")
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
            $result = $this->notificationRepository->getAll($request);

            return Common::success('Journal des notifications', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
