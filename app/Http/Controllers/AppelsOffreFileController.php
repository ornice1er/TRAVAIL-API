<?php

namespace App\Http\Controllers;

use App\Http\Repositories\appelsOffreFileRepository;
use App\Http\Requests\AppelsOffreFile\StoreAppelsOffreFileRequest;
use App\Http\Requests\AppelsOffreFile\UpdateAppelsOffreFileRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AppelsOffreFileController extends Controller
{
    /**
     * The AppelsOffreFile repository being queried.
     *
     * @var AppelsOffreFileRepository
     */
    protected $appelsOffreFileRepository;

    protected $ls;

    public function __construct(appelsOffreFileRepository $appelsOffreFileRepository, LogService $ls)
    {
        $this->appelsOffreFileRepository = $appelsOffreFileRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/appelsOffreFiles",
     *      operationId="AppelsOffreFile list",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *      summary="Return AppelsOffreFile data",
     *      description="Get all appelsOffreFile",
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
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
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
        $message = 'Récupération de la liste des fichiers d\'appels d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/appelsOffreFiles/{id}",
     *      operationId="AppelsOffreFile show",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Return one AppelsOffreFile data",
     *      description="Get AppelsOffreFile by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
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
    public function show(Request $request, $id)
    {
        $message = 'Récupération d\'un fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success('Fichier d\'appel d\'offre trouvé', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles",
     *      operationId="AppelsOffreFile store",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *      summary="Store AppelsOffreFile data",
     *      description="Create a new AppelsOffreFile",
     *
     *       @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFileCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
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
    public function store(StoreAppelsOffreFileRequest $request)
    {
        $message = 'Enregistrement d\'un fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Fichier d\'appel d\'offre créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/appelsOffreFiles/{id}",
     *      operationId="AppelsOffreFile update",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *      summary="Update one AppelsOffreFile data",
     *      description="Update AppelsOffreFile by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFileCreate")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
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
    public function update(UpdateAppelsOffreFileRequest $request, $id)
    {
        $message = 'Mise à jour d\'un fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour du fichier d\'appel d\'offre effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/appelsOffreFiles/{id}",
     *      operationId="AppelsOffreFile Delete",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *      summary="Delete AppelsOffreFile data",
     *      description="Delete AppelsOffreFile by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/DeleteResponseData"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/DeleteResponseData")
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
        $message = 'Suppression d\'un fichier d\'appel d\'offre';

        try {
            $recup = $this->appelsOffreFileRepository->get($id);

            $result = $this->appelsOffreFileRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($recup)]);

            return Common::successDelete('Fichier d\'appel d\'offre supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/appelsOffreFiles/{id}/state/{state}",
     *      operationId="AppelsOffreFile change state",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="state",
     *          in="path",
     *          description="AppelsOffreFile state",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Change AppelsOffreFile state",
     *      description="Change AppelsOffreFile state by ID",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
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
    public function changeState($id, $state)
    {
        $message = 'Changement de l\'état d\'un fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->setStatus($id, $state);
            $statusMessage = $state == 1 ? 'activé' : 'désactivé';
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Fichier d'appel d'offre $statusMessage avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles-search",
     *      operationId="AppelsOffreFile searching",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *      summary="Return list of AppelsOffreFile respecting term",
     *      description="Get all filtered appelsOffreFiles using term",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AppelsOffreFile"),
     *
     *         @OA\XmlContent(ref="#/components/schemas/AppelsOffreFile")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Body request",
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/TermSearch")
     *     ),
     *
     * @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * @OA\Response(
     *         response=419,
     *         description="Expired session"
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     *)
     */
    public function search(Request $request)
    {
        $message = 'Filtrage des fichiers d\'appels d\'offre';

        try {
            $term = $request->term;
            $result = $this->appelsOffreFileRepository->search($term);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success('Filtrage effectué avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/up",
     *      operationId="AppelsOffreFile transmission up",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Transmit AppelsOffreFile up",
     *      description="Transmit AppelsOffreFile to validation level",
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
    public function up($id)
    {
        $message = 'Transmission du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->up($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Transmission du fichier d'appel d'offre avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/down",
     *      operationId="AppelsOffreFile transmission down",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Transmit AppelsOffreFile down",
     *      description="Transmit AppelsOffreFile back to saisie level",
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
    public function down(Request $request, $id)
    {
        $message = 'Retour du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->down($request, $id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Retour du fichier d'appel d'offre avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/publish",
     *      operationId="AppelsOffreFile publish",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Publish AppelsOffreFile",
     *      description="Publish AppelsOffreFile",
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
    public function publish($id)
    {
        $message = 'Publication du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->publish($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Fichier d'appel d'offre publié avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/unpublish",
     *      operationId="AppelsOffreFile unpublish",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Unpublish AppelsOffreFile",
     *      description="Unpublish AppelsOffreFile",
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
    public function unpublish($id)
    {
        $message = 'Dépublication du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->unpublish($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Fichier d'appel d'offre dépublié avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/archive",
     *      operationId="AppelsOffreFile archive",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Archive AppelsOffreFile",
     *      description="Archive AppelsOffreFile",
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
    public function archive($id)
    {
        $message = 'Archivage du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->archive($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Fichier d'appel d'offre archivé avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/restore",
     *      operationId="AppelsOffreFile restore",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Restore AppelsOffreFile",
     *      description="Restore AppelsOffreFile from archive",
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
    public function restore($id)
    {
        $message = 'Restauration du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->restore($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Fichier d'appel d'offre restauré avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/generateLink",
     *      operationId="AppelsOffreFile generate link",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Generate unique link for AppelsOffreFile",
     *      description="Generate unique link and QR code for AppelsOffreFile",
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
    public function generateLink(Request $request, $id)
    {
        $message = 'Génération du lien unique pour le fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->generateLink($id, $request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Lien unique généré avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/{id}/generateMediaLink",
     *      operationId="AppelsOffreFile generate media link",
     *      tags={"AppelsOffreFile"},
     *      security={{"JWT":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="AppelsOffreFile ID",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      summary="Generate media link for AppelsOffreFile",
     *      description="Generate media link and QR code for AppelsOffreFile gallery",
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
        $message = 'Génération du lien média pour le fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->generateMediaLink($id);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($result)]);

            return Common::success("Lien média généré avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/verifyLink",
     *      operationId="AppelsOffreFile verify link",
     *      tags={"AppelsOffreFile"},
     *      summary="Verify AppelsOffreFile link",
     *      description="Verify AppelsOffreFile link using token",
     *
     *      @OA\RequestBody(
     *          description="Link verification data",
     *          required=true,
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="link_token", type="string", description="Link token"),
     *              @OA\Property(property="media_token", type="string", description="Media token")
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
     *          response=404,
     *          description="Not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function verifyLink(Request $request)
    {
        $message = 'Vérification du lien du fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->verifyLink($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            if ($result) {
                return Common::success("Lien vérifié avec succès", $result);
            } else {
                return Common::error("Lien invalide ou expiré", []);
            }
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/appelsOffreFiles/participate",
     *      operationId="AppelsOffreFile participate",
     *      tags={"AppelsOffreFile"},
     *      summary="Participate in AppelsOffreFile",
     *      description="Register participation for AppelsOffreFile",
     *
     *      @OA\RequestBody(
     *          description="Participation data",
     *          required=true,
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="AppelsOffreFile_id", type="integer", description="AppelsOffreFile ID"),
     *              @OA\Property(property="phone", type="string", description="Phone number"),
     *              @OA\Property(property="name", type="string", description="Participant name"),
     *              @OA\Property(property="email", type="string", description="Email address")
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
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function participate(Request $request)
    {
        $message = 'Participation au fichier d\'appel d\'offre';

        try {
            $result = $this->appelsOffreFileRepository->participate($request->all());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success("Participation enregistrée avec succès", $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);

            return Common::error($th->getMessage(), []);
        }
    }
}
