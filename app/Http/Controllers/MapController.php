<?php

namespace App\Http\Controllers;

use App\Models\Map;
use App\Http\Repositories\MapRepository;
use App\Services\LogService;
use App\Http\Requests\Map\StoreMapRequest;
use App\Http\Requests\Map\UpdateMapRequest;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class MapController
{
    /**
     * The Map repository being queried.
     *
     * @var MapRepository
     */
    protected $mapRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(MapRepository $mapRepository, LogService $ls)
    {
        $this->mapRepository = $mapRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/maps",
     *      operationId="Map list",
     *      tags={"Map"},
     *      summary="Return Map data",
     *      description="Get all map",
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
     *          @OA\JsonContent(ref="#/components/schemas/Map"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->getAll($request);
            $this->ls->trace(['action_name' => 'Journal des maps', 'description' => json_encode($request->all())]);

            return Common::success('Journal des maps', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des maps', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/maps/{id}",
     *      operationId="getMapById",
     *      tags={"Map"},
     *      summary="Get map information",
     *      description="Returns map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->show($id);
            $this->ls->trace(['action_name' => 'Détail map', 'description' => $id]);

            return Common::success('Détail map', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Détail map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps",
     *      operationId="storeMap",
     *      tags={"Map"},
     *      summary="Store new map",
     *      description="Returns map data",
     *      security={{"JWT":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreMapRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
    public function store(StoreMapRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            
            $result = $this->mapRepository->create($data);
            $this->ls->trace(['action_name' => 'Création map', 'description' => json_encode($data)]);

            return Common::success('Map créée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Création map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Put(
     *      path="/maps/{id}",
     *      operationId="updateMap",
     *      tags={"Map"},
     *      summary="Update existing map",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateMapRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
    public function update(UpdateMapRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->mapRepository->update($id, $data);
            $this->ls->trace(['action_name' => 'Modification map', 'description' => json_encode($data)]);

            return Common::success('Map modifiée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Modification map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Delete(
     *      path="/maps/{id}",
     *      operationId="deleteMap",
     *      tags={"Map"},
     *      summary="Delete existing map",
     *      description="Deletes a record and returns no content",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
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
            $this->mapRepository->delete($id);
            $this->ls->trace(['action_name' => 'Suppression map', 'description' => $id]);

            return Common::success('Map supprimée avec succès', []);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Suppression map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/change-state",
     *      operationId="changeMapState",
     *      tags={"Map"},
     *      summary="Change map state",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->changeState($id, $request->state);
            $this->ls->trace(['action_name' => 'Changement état map', 'description' => json_encode(['id' => $id, 'state' => $request->state])]);

            return Common::success('État map modifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Changement état map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/maps/search",
     *      operationId="searchMaps",
     *      tags={"Map"},
     *      summary="Search maps",
     *      description="Returns maps matching search criteria",
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
     *              @OA\Items(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->search($request);
            $this->ls->trace(['action_name' => 'Recherche maps', 'description' => json_encode($request->all())]);

            return Common::success('Recherche maps', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Recherche maps', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/up",
     *      operationId="upMap",
     *      tags={"Map"},
     *      summary="Move map up",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->up($id);
            $this->ls->trace(['action_name' => 'Déplacer map vers le haut', 'description' => $id]);

            return Common::success('Map déplacée vers le haut avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer map vers le haut', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/down",
     *      operationId="downMap",
     *      tags={"Map"},
     *      summary="Move map down",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->down($id);
            $this->ls->trace(['action_name' => 'Déplacer map vers le bas', 'description' => $id]);

            return Common::success('Map déplacée vers le bas avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer map vers le bas', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/publish",
     *      operationId="publishMap",
     *      tags={"Map"},
     *      summary="Publish map",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->publish($id);
            $this->ls->trace(['action_name' => 'Publication map', 'description' => $id]);

            return Common::success('Map publiée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Publication map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/unpublish",
     *      operationId="unpublishMap",
     *      tags={"Map"},
     *      summary="Unpublish map",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->unpublish($id);
            $this->ls->trace(['action_name' => 'Dépublication map', 'description' => $id]);

            return Common::success('Map dépubliée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Dépublication map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/archive",
     *      operationId="archiveMap",
     *      tags={"Map"},
     *      summary="Archive map",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->archive($id);
            $this->ls->trace(['action_name' => 'Archivage map', 'description' => $id]);

            return Common::success('Map archivée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Archivage map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/maps/{id}/restore",
     *      operationId="restoreMap",
     *      tags={"Map"},
     *      summary="Restore map",
     *      description="Returns updated map data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Map id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Map")
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
            $result = $this->mapRepository->restore($id);
            $this->ls->trace(['action_name' => 'Restauration map', 'description' => $id]);

            return Common::success('Map restaurée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Restauration map', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
