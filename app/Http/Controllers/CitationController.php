<?php

namespace App\Http\Controllers;

use App\Http\Repositories\citationRepository;
use App\Http\Requests\Citation\StoreCitationRequest;
use App\Http\Requests\Citation\UpdateCitationRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CitationController
{
    /**
     * The Citation repository being queried.
     *
     * @var CitationRepository
     */
    protected $citationRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(citationRepository $citationRepository, LogService $ls)
    {
        $this->citationRepository = $citationRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/citations",
     *      operationId="Citation list",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Return Citation data",
     *      description="Get all citation",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Récupération de la liste des citations';

        try {
            $result = $this->citationRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/citations/{id}",
     *      operationId="getCitationById",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Return Citation data",
     *      description="Get citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Récupération de la citation';

        try {
            $result = $this->citationRepository->getById($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations",
     *      operationId="storeCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Store Citation data",
     *      description="Create a new citation",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Citation object that needs to be stored",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
    public function store(StoreCitationRequest $request)
    {
        $message = 'Création de la citation';

        try {
            $result = $this->citationRepository->store($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/citations/{id}",
     *      operationId="updateCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Update Citation data",
     *      description="Update citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Citation object that needs to be updated",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
    public function update(UpdateCitationRequest $request, $id)
    {
        $message = 'Mise à jour de la citation';

        try {
            $result = $this->citationRepository->update($request->all(), $id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all()) . ' - ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/citations/{id}",
     *      operationId="deleteCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Delete Citation data",
     *      description="Delete citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Suppression de la citation';

        try {
            $result = $this->citationRepository->destroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/state",
     *      operationId="changeCitationState",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Change Citation state",
     *      description="Change state of citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Changement de statut de la citation';

        try {
            $result = $this->citationRepository->changeState($request->state, $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id . ' - État: ' . $request->state]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/citations/search",
     *      operationId="searchCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Search Citation data",
     *      description="Search citations",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Recherche de citations';

        try {
            $result = $this->citationRepository->search($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/up",
     *      operationId="moveCitationUp",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Move Citation up",
     *      description="Move citation up in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Déplacement de la citation vers le haut';

        try {
            $result = $this->citationRepository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/down",
     *      operationId="moveCitationDown",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Move Citation down",
     *      description="Move citation down in order",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Déplacement de la citation vers le bas';

        try {
            $result = $this->citationRepository->down([], $id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/publish",
     *      operationId="publishCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Publish Citation",
     *      description="Publish citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Publication de la citation';

        try {
            $result = $this->citationRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/unpublish",
     *      operationId="unpublishCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Unpublish Citation",
     *      description="Unpublish citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Dépublication de la citation';

        try {
            $result = $this->citationRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/archive",
     *      operationId="archiveCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Archive Citation",
     *      description="Archive citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Archivage de la citation';

        try {
            $result = $this->citationRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/restore",
     *      operationId="restoreCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Restore Citation",
     *      description="Restore citation by id",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
        $message = 'Restauration de la citation';

        try {
            $result = $this->citationRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/citations/{id}/generate-link",
     *      operationId="generateCitationLink",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Generate Citation Link",
     *      description="Generate QR code link for citation",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
        $message = 'Génération du lien QR Code pour la citation';

        try {
            $result = $this->citationRepository->generateLink($id, []);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/citations/{id}/generate-media-link",
     *      operationId="generateCitationMediaLink",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Generate Citation Media Link",
     *      description="Generate QR code media link for citation",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
        $message = 'Génération du lien média QR Code pour la citation';

        try {
            $result = $this->citationRepository->generateMediaLink($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/citations/verify-link/{code}",
     *      operationId="verifyCitationLink",
     *      tags={"Citation"},
     *      summary="Verify Citation Link",
     *      description="Verify QR code link for citation",
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
     *          @OA\JsonContent(ref="#/components/schemas/Citation"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Citation")
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
            $result = $this->citationRepository->verifyLink($code);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/citations/{id}/participate",
     *      operationId="participateCitation",
     *      tags={"Citation"},
     *      security={{"JWT":{}}},
     *      summary="Participate in Citation",
     *      description="Record participation for citation",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Citation id",
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
        $message = 'Participation à la citation';

        try {
            $result = $this->citationRepository->participate($id);
            $this->ls->trace(['action_name' => $message, 'description' => 'ID: ' . $id]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
