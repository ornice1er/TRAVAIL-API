<?php

namespace App\Http\Controllers;

use App\Http\Repositories\communiqueRepository;
use App\Http\Requests\Communique\StoreCommuniqueRequest;
use App\Http\Requests\Communique\UpdateCommuniqueRequest;
use App\Services\LogService;
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

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(communiqueRepository $communiqueRepository, LogService $ls)
    {
        $this->communiqueRepository = $communiqueRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/communiques",
     *      operationId="Communique list",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Return Communique data",
     *      description="Get all communique",
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
        $message = 'Récupération de la liste des communiqués';

        try {
            $result = $this->communiqueRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiques/{id}",
     *      operationId="getCommuniqueById",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Return Communique data",
     *      description="Get communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function show($id)
    {
        $message = 'Récupération du communiqué';

        try {
            $result = $this->communiqueRepository->getById($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques",
     *      operationId="storeCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Store Communique data",
     *      description="Create a new communique",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Communique object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Communique"),
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
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function store(StoreCommuniqueRequest $request)
    {
        $message = 'Création du communiqué';

        try {
            $result = $this->communiqueRepository->store($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/communiques/{id}",
     *      operationId="updateCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Update Communique data",
     *      description="Update communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Communique object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Communique"),
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
    public function update(UpdateCommuniqueRequest $request, $id)
    {
        $message = 'Mise à jour du communiqué';

        try {
            $result = $this->communiqueRepository->update($request->all(), $id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all()) . ' - ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/communiques/{id}",
     *      operationId="deleteCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Delete Communique data",
     *      description="Delete communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function destroy($id)
    {
        $message = 'Suppression du communiqué';

        try {
            $result = $this->communiqueRepository->destroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/state",
     *      operationId="changeCommuniqueState",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Change Communique state",
     *      description="Change state of communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="State to change to",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="state",
     *                  type="string",
     *                  description="New state"
     *              )
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
    public function changeState(Request $request, $id)
    {
        $message = 'Changement de statut du communiqué';

        try {
            $result = $this->communiqueRepository->changeState($request->state, $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - État: ' . $request->state]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiques/search",
     *      operationId="searchCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Search Communique data",
     *      description="Search communiques",
     *
     *      @OA\Parameter(
     *          name="keyword",
     *          in="query",
     *          description="Search keyword",
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
    public function search(Request $request)
    {
        $message = 'Recherche de communiqués';

        try {
            $result = $this->communiqueRepository->search($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/up",
     *      operationId="moveCommuniqueUp",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Move Communique up",
     *      description="Move communique up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function up($id)
    {
        $message = 'Déplacement du communiqué vers le haut';

        try {
            $result = $this->communiqueRepository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/down",
     *      operationId="moveCommuniqueDown",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Move Communique down",
     *      description="Move communique down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function down($id)
    {
        $message = 'Déplacement du communiqué vers le bas';

        try {
            $result = $this->communiqueRepository->down([], $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/publish",
     *      operationId="publishCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Publish Communique",
     *      description="Publish communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function publish($id)
    {
        $message = 'Publication du communiqué';

        try {
            $result = $this->communiqueRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/unpublish",
     *      operationId="unpublishCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish Communique",
     *      description="Unpublish communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function unpublish($id)
    {
        $message = 'Dépublication du communiqué';

        try {
            $result = $this->communiqueRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/archive",
     *      operationId="archiveCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Archive Communique",
     *      description="Archive communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function archive($id)
    {
        $message = 'Archivage du communiqué';

        try {
            $result = $this->communiqueRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/restore",
     *      operationId="restoreCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Restore Communique",
     *      description="Restore communique by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
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
    public function restore($id)
    {
        $message = 'Restauration du communiqué';

        try {
            $result = $this->communiqueRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiques/{id}/generate-link",
     *      operationId="generateCommuniqueLink",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Generate Communique Link",
     *      description="Generate QR code link for communique",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function generateLink($id)
    {
        $message = 'Génération du lien QR Code pour le communiqué';

        try {
            $result = $this->communiqueRepository->generateLink($id, []);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiques/{id}/generate-media-link",
     *      operationId="generateCommuniqueMediaLink",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Generate Communique Media Link",
     *      description="Generate QR code media link for communique",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function generateMediaLink($id)
    {
        $message = 'Génération du lien média QR Code pour le communiqué';

        try {
            $result = $this->communiqueRepository->generateMediaLink($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/communiques/verify-link/{code}",
     *      operationId="verifyCommuniqueLink",
     *      tags={"Communique"},
     *      summary="Verify Communique Link",
     *      description="Verify QR code link for communique",
     *
     *      @OA\Parameter(
     *          name="code",
     *          in="path",
     *          description="Verification code",
     *          required=true,
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
     *          response=404,
     *          description="Not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function verifyLink($code)
    {
        $message = 'Vérification du lien QR Code';

        try {
            $result = $this->communiqueRepository->verifyLink($code);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/communiques/{id}/participate",
     *      operationId="participateCommunique",
     *      tags={"Communique"},
     *      security={{"JWT":{}}},
     *      summary="Participate in Communique",
     *      description="Record participation for communique",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Communique id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function participate($id)
    {
        $message = 'Participation au communiqué';

        try {
            $result = $this->communiqueRepository->participate($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
