<?php

namespace App\Http\Controllers;

use App\Models\Mot;
use App\Http\Repositories\MotRepository;
use App\Services\LogService;
use App\Http\Requests\Mot\StoreMotRequest;
use App\Http\Requests\Mot\UpdateMotRequest;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class MotController
{
    /**
     * The Mot repository being queried.
     *
     * @var MotRepository
     */
    protected $motRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(MotRepository $motRepository, LogService $ls)
    {
        $this->motRepository = $motRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/mots",
     *      operationId="Mot list",
     *      tags={"Mot"},
     *      summary="Return Mot data",
     *      description="Get all mot",
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
     *          @OA\JsonContent(ref="#/components/schemas/Mot"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Mot")
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
            $result = $this->motRepository->getAll($request);
            $this->ls->trace(['action_name' => 'Journal des mots', 'description' => json_encode($request->all())]);

            return Common::success('Journal des mots', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des mots', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/mots/{id}",
     *      operationId="getMotById",
     *      tags={"Mot"},
     *      summary="Get mot information",
     *      description="Returns mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show($id)
    {
        try {
            $result = $this->motRepository->show($id);
            $this->ls->trace(['action_name' => 'Détail mot', 'description' => $id]);

            return Common::success('Détail mot', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Détail mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots",
     *      operationId="storeMot",
     *      tags={"Mot"},
     *      summary="Store new mot",
     *      description="Returns mot data",
     *      security={{"JWT":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreMotRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function store(StoreMotRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            
            $result = $this->motRepository->create($data);
            $this->ls->trace(['action_name' => 'Création mot', 'description' => json_encode($data)]);

            return Common::success('Mot créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Création mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Put(
     *      path="/mots/{id}",
     *      operationId="updateMot",
     *      tags={"Mot"},
     *      summary="Update existing mot",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateMotRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(UpdateMotRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->motRepository->update($id, $data);
            $this->ls->trace(['action_name' => 'Modification mot', 'description' => json_encode($data)]);

            return Common::success('Mot modifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Modification mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Delete(
     *      path="/mots/{id}",
     *      operationId="deleteMot",
     *      tags={"Mot"},
     *      summary="Delete existing mot",
     *      description="Deletes a record and returns no content",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->motRepository->delete($id);
            $this->ls->trace(['action_name' => 'Suppression mot', 'description' => $id]);

            return Common::success('Mot supprimé avec succès', []);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Suppression mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/change-state",
     *      operationId="changeMotState",
     *      tags={"Mot"},
     *      summary="Change mot state",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"state"},
     *              @OA\Property(property="state", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function changeState(Request $request, $id)
    {
        try {
            $result = $this->motRepository->changeState($id, $request->state);
            $this->ls->trace(['action_name' => 'Changement état mot', 'description' => json_encode(['id' => $id, 'state' => $request->state])]);

            return Common::success('État mot modifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Changement état mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/mots/search",
     *      operationId="searchMots",
     *      tags={"Mot"},
     *      summary="Search mots",
     *      description="Returns mots matching search criteria",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="q",
     *          description="Search query",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Mot")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function search(Request $request)
    {
        try {
            $result = $this->motRepository->search($request);
            $this->ls->trace(['action_name' => 'Recherche mots', 'description' => json_encode($request->all())]);

            return Common::success('Recherche mots', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Recherche mots', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/up",
     *      operationId="upMot",
     *      tags={"Mot"},
     *      summary="Move mot up",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function up($id)
    {
        try {
            $result = $this->motRepository->up($id);
            $this->ls->trace(['action_name' => 'Déplacer mot vers le haut', 'description' => $id]);

            return Common::success('Mot déplacé vers le haut avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer mot vers le haut', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/down",
     *      operationId="downMot",
     *      tags={"Mot"},
     *      summary="Move mot down",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function down($id)
    {
        try {
            $result = $this->motRepository->down($id);
            $this->ls->trace(['action_name' => 'Déplacer mot vers le bas', 'description' => $id]);

            return Common::success('Mot déplacé vers le bas avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer mot vers le bas', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/publish",
     *      operationId="publishMot",
     *      tags={"Mot"},
     *      summary="Publish mot",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function publish($id)
    {
        try {
            $result = $this->motRepository->publish($id);
            $this->ls->trace(['action_name' => 'Publication mot', 'description' => $id]);

            return Common::success('Mot publié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Publication mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/unpublish",
     *      operationId="unpublishMot",
     *      tags={"Mot"},
     *      summary="Unpublish mot",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function unpublish($id)
    {
        try {
            $result = $this->motRepository->unpublish($id);
            $this->ls->trace(['action_name' => 'Dépublication mot', 'description' => $id]);

            return Common::success('Mot dépublié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Dépublication mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/archive",
     *      operationId="archiveMot",
     *      tags={"Mot"},
     *      summary="Archive mot",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function archive($id)
    {
        try {
            $result = $this->motRepository->archive($id);
            $this->ls->trace(['action_name' => 'Archivage mot', 'description' => $id]);

            return Common::success('Mot archivé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Archivage mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/mots/{id}/restore",
     *      operationId="restoreMot",
     *      tags={"Mot"},
     *      summary="Restore mot",
     *      description="Returns updated mot data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Mot id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Mot")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function restore($id)
    {
        try {
            $result = $this->motRepository->restore($id);
            $this->ls->trace(['action_name' => 'Restauration mot', 'description' => $id]);

            return Common::success('Mot restauré avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Restauration mot', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
