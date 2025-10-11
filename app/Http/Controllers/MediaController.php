<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Http\Repositories\MediaRepository;
use App\Services\LogService;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class MediaController
{
    /**
     * The Media repository being queried.
     *
     * @var MediaRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(MediaRepository $mediaRepository, LogService $ls)
    {
        $this->repository = $mediaRepository;
        $this->ls = $ls;
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
            $result = $this->repository->getAll($request);
            $this->ls->trace(['action_name' => 'Journal des medias', 'description' => json_encode($request->all())]);

            return Common::success('Journal des medias', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Journal des medias', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/medias/{id}",
     *      operationId="getMediaById",
     *      tags={"Media"},
     *      summary="Get media information",
     *      description="Returns media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->show($id);
            $this->ls->trace(['action_name' => 'Détail media', 'description' => $id]);

            return Common::success('Détail media', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Détail media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias",
     *      operationId="storeMedia",
     *      tags={"Media"},
     *      summary="Store new media",
     *      description="Returns media data",
     *      security={{"JWT":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreMediaRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
    public function store(StoreMediaRequest $request)
    {
        try {
            $data = $request->validated();
            $data['adding_by'] = Auth::id();
            
            $result = $this->repository->create($data);
            $this->ls->trace(['action_name' => 'Création media', 'description' => json_encode($data)]);

            return Common::success('Media créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Création media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Put(
     *      path="/medias/{id}",
     *      operationId="updateMedia",
     *      tags={"Media"},
     *      summary="Update existing media",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateMediaRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
    public function update(UpdateMediaRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->repository->update($id, $data);
            $this->ls->trace(['action_name' => 'Modification media', 'description' => json_encode($data)]);

            return Common::success('Media modifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Modification media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Delete(
     *      path="/medias/{id}",
     *      operationId="deleteMedia",
     *      tags={"Media"},
     *      summary="Delete existing media",
     *      description="Deletes a record and returns no content",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
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
            $this->repository->delete($id);
            $this->ls->trace(['action_name' => 'Suppression media', 'description' => $id]);

            return Common::success('Media supprimé avec succès', []);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Suppression media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/change-state",
     *      operationId="changeMediaState",
     *      tags={"Media"},
     *      summary="Change media state",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->changeState($id, $request->state);
            $this->ls->trace(['action_name' => 'Changement état media', 'description' => json_encode(['id' => $id, 'state' => $request->state])]);

            return Common::success('État media modifié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Changement état media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/medias/search",
     *      operationId="searchMedias",
     *      tags={"Media"},
     *      summary="Search medias",
     *      description="Returns medias matching search criteria",
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
     *              @OA\Items(ref="#/components/schemas/Media")
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
            $result = $this->repository->search($request);
            $this->ls->trace(['action_name' => 'Recherche medias', 'description' => json_encode($request->all())]);

            return Common::success('Recherche medias', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Recherche medias', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/up",
     *      operationId="upMedia",
     *      tags={"Media"},
     *      summary="Move media up",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->up($id);
            $this->ls->trace(['action_name' => 'Déplacer media vers le haut', 'description' => $id]);

            return Common::success('Media déplacé vers le haut avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer media vers le haut', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/down",
     *      operationId="downMedia",
     *      tags={"Media"},
     *      summary="Move media down",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->down($id);
            $this->ls->trace(['action_name' => 'Déplacer media vers le bas', 'description' => $id]);

            return Common::success('Media déplacé vers le bas avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Déplacer media vers le bas', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/publish",
     *      operationId="publishMedia",
     *      tags={"Media"},
     *      summary="Publish media",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->publish($id);
            $this->ls->trace(['action_name' => 'Publication media', 'description' => $id]);

            return Common::success('Media publié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Publication media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/unpublish",
     *      operationId="unpublishMedia",
     *      tags={"Media"},
     *      summary="Unpublish media",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->unpublish($id);
            $this->ls->trace(['action_name' => 'Dépublication media', 'description' => $id]);

            return Common::success('Media dépublié avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Dépublication media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/archive",
     *      operationId="archiveMedia",
     *      tags={"Media"},
     *      summary="Archive media",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->archive($id);
            $this->ls->trace(['action_name' => 'Archivage media', 'description' => $id]);

            return Common::success('Media archivé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Archivage media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/medias/{id}/restore",
     *      operationId="restoreMedia",
     *      tags={"Media"},
     *      summary="Restore media",
     *      description="Returns updated media data",
     *      security={{"JWT":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Media id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Media")
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
            $result = $this->repository->restore($id);
            $this->ls->trace(['action_name' => 'Restauration media', 'description' => $id]);

            return Common::success('Media restauré avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => 'Restauration media', 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
