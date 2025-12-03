<?php

namespace App\Http\Controllers;

use App\Http\Repositories\DashRepository;
use App\Utilities\Common;
use App\Services\LogService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashController extends Controller
{

    protected $dashRepository;

    protected $ls;

    public function __construct(DashRepository $dashRepository, LogService $ls)
    {
        $this->dashRepository = $dashRepository;
        $this->ls = $ls;

        //$this->middleware('auth:api')->except(['getNotified', 'show']);

    }


    function getDash(Request $request){
            $message = 'Récupération de la liste des corps';

        try {
            $result = $this->dashRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}