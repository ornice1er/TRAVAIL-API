<?php

namespace App\Http\Controllers;

use App\Models\Galerie;
use App\Http\Repositories\GalerieRepository;
use App\Services\LogService;
use App\Http\Requests\Galerie\StoreGalerieRequest;
use App\Http\Requests\Galerie\UpdateGalerieRequest;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class GalerieController
{
    /**
     * The Galerie repository being queried.
     *
     * @var GalerieRepository
     */
    protected $galerieRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(GalerieRepository $galerieRepository, LogService $ls)
    {
        $this->galerieRepository = $galerieRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/galeries",
     *      operationId="Galerie list",
     *      tags={"Galerie"},
     *      security={{"JWT":{}}},
     *      summary="Return Galerie data",
     *      description="Get all galerie",
     *
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Can be used for filtering data by name",
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
     *          @OA\JsonContent(ref="#/components/schemas/Galerie"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->getAll($request);
            $this->ls->trace(['action_name' => 'Journal des galeries', 'description' => json_encode($request->all())]);

            return Common::success('Journal des galeries', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des galeries', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/galeries/{id}",
     *      operationId="getGalerieById",
     *      tags={"Galerie"},
     *      summary="Get galerie information",
     *      description="Returns galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->show($id);
            $this->ls->trace(['action_name' => 'Détail galerie', 'description' => $id]);

            return Common::success('Détail galerie', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Détail galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries",
     *      operationId="storeGalerie",
     *      tags={"Galerie"},
     *      summary="Store new galerie",
     *      description="Returns galerie data",
     *      security={{"JWT":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreGalerieRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
    public function store(StoreGalerieRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            
            $result = $this->galerieRepository->create($data);
            $this->ls->trace(['action_name' => 'Création galerie', 'description' => json_encode($data)]);

            return Common::success('Galerie créée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Création galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Put(
     *      path="/galeries/{id}",
     *      operationId="updateGalerie",
     *      tags={"Galerie"},
     *      summary="Update existing galerie",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateGalerieRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
    public function update(UpdateGalerieRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->galerieRepository->update($id, $data);
            $this->ls->trace(['action_name' => 'Modification galerie', 'description' => json_encode($data)]);

            return Common::success('Galerie modifiée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Modification galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Delete(
     *      path="/galeries/{id}",
     *      operationId="deleteGalerie",
     *      tags={"Galerie"},
     *      summary="Delete existing galerie",
     *      description="Deletes a record and returns no content",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
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
            $this->galerieRepository->delete($id);
            $this->ls->trace(['action_name' => 'Suppression galerie', 'description' => $id]);

            return Common::success('Galerie supprimée avec succès', []);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Suppression galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/change-state",
     *      operationId="changeGalerieState",
     *      tags={"Galerie"},
     *      summary="Change galerie state",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->changeState($id, $request->state);
            $this->ls->trace(['action_name' => 'Changement état galerie', 'description' => json_encode(['id' => $id, 'state' => $request->state])]);

            return Common::success('État galerie modifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Changement état galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/galeries/search",
     *      operationId="searchGaleries",
     *      tags={"Galerie"},
     *      summary="Search galeries",
     *      description="Returns galeries matching search criteria",
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
     *              @OA\Items(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->search($request);
            $this->ls->trace(['action_name' => 'Recherche galeries', 'description' => json_encode($request->all())]);

            return Common::success('Recherche galeries', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Recherche galeries', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/up",
     *      operationId="upGalerie",
     *      tags={"Galerie"},
     *      summary="Move galerie up",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->up($id);
            $this->ls->trace(['action_name' => 'Déplacer galerie vers le haut', 'description' => $id]);

            return Common::success('Galerie déplacée vers le haut avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer galerie vers le haut', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/down",
     *      operationId="downGalerie",
     *      tags={"Galerie"},
     *      summary="Move galerie down",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->down($id);
            $this->ls->trace(['action_name' => 'Déplacer galerie vers le bas', 'description' => $id]);

            return Common::success('Galerie déplacée vers le bas avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer galerie vers le bas', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/publish",
     *      operationId="publishGalerie",
     *      tags={"Galerie"},
     *      summary="Publish galerie",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->publish($id);
            $this->ls->trace(['action_name' => 'Publication galerie', 'description' => $id]);

            return Common::success('Galerie publiée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Publication galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/unpublish",
     *      operationId="unpublishGalerie",
     *      tags={"Galerie"},
     *      summary="Unpublish galerie",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->unpublish($id);
            $this->ls->trace(['action_name' => 'Dépublication galerie', 'description' => $id]);

            return Common::success('Galerie dépubliée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Dépublication galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/archive",
     *      operationId="archiveGalerie",
     *      tags={"Galerie"},
     *      summary="Archive galerie",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->archive($id);
            $this->ls->trace(['action_name' => 'Archivage galerie', 'description' => $id]);

            return Common::success('Galerie archivée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Archivage galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/galeries/{id}/restore",
     *      operationId="restoreGalerie",
     *      tags={"Galerie"},
     *      summary="Restore galerie",
     *      description="Returns updated galerie data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Galerie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Galerie")
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
            $result = $this->galerieRepository->restore($id);
            $this->ls->trace(['action_name' => 'Restauration galerie', 'description' => $id]);

            return Common::success('Galerie restaurée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Restauration galerie', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
