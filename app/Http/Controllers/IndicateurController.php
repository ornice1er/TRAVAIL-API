<?php

namespace App\Http\Controllers;

use App\Http\Repositories\IndicateurRepository;
use App\Http\Requests\Indicateur\StoreIndicateurRequest;
use App\Http\Requests\Indicateur\UpdateIndicateurRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class IndicateurController extends Controller
{
    /**
     * The Indicateur repository being queried.
     *
     * @var IndicateurRepository
     */
    protected $indicateurRepository;

    protected $ls;

    public function __construct(IndicateurRepository $indicateurRepository, LogService $ls)
    {
        $this->indicateurRepository = $indicateurRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *      path="/indicateurs",
     *      operationId="Indicateur list",
     *      tags={"Indicateur"},
     *      security={{"JWT":{}}},
     *      summary="Return Indicateur data",
     *      description="Get all indicateurs",
     *
     *      @OA\Parameter(
     *          name="libelle",
     *          in="query",
     *          description="Can be used for filtering data by libelle",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Parameter(
     *          name="structure_id",
     *          in="query",
     *          description="Can be used for filtering data by structure",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not found"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération de la liste des indicateurs';

        try {
            $result = $this->indicateurRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/indicateurs/{id}",
     *      operationId="Indicateur show",
     *      tags={"Indicateur"},
     *      security={{"JWT":{}}},
     *      summary="Return one Indicateur data",
     *      description="Get Indicateur by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Indicateur ID",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not found"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show(Request $request, $id)
    {
        $message = 'Récupération d\'un indicateur';

        try {
            $result = $this->indicateurRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => "ID: $id"]);

            return Common::success('Indicateur trouvé', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/indicateurs",
     *      operationId="Indicateur store",
     *      tags={"Indicateur"},
     *      security={{"JWT":{}}},
     *      summary="Store Indicateur data",
     *      description="Create a new Indicateur",
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"libelle","valeur","structure_id"},
     *              @OA\Property(property="libelle", type="string", example="Nombre d'employés"),
     *              @OA\Property(property="valeur", type="string", example="150"),
     *              @OA\Property(property="structure_id", type="integer", example=1)
     *          )
     *      ),
     *
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreIndicateurRequest $request)
    {
        $message = 'Enregistrement d\'un indicateur';

        try {
            $result = $this->indicateurRepository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Indicateur créé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Put(
     *      path="/indicateurs/{id}",
     *      operationId="Indicateur update",
     *      tags={"Indicateur"},
     *      security={{"JWT":{}}},
     *      summary="Update one Indicateur data",
     *      description="Update Indicateur by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Indicateur ID",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="libelle", type="string"),
     *              @OA\Property(property="valeur", type="string"),
     *              @OA\Property(property="structure_id", type="integer")
     *          )
     *      ),
     *
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not found"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateIndicateurRequest $request, $id)
    {
        $message = 'Mise à jour d\'un indicateur';

        try {
            $result = $this->indicateurRepository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour de l\'indicateur effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Delete(
     *      path="/indicateurs/{id}",
     *      operationId="Indicateur Delete",
     *      tags={"Indicateur"},
     *      security={{"JWT":{}}},
     *      summary="Delete Indicateur data",
     *      description="Delete Indicateur by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Indicateur ID",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(response=204, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not found"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function destroy($id)
    {
        $message = 'Suppression d\'un indicateur';

        try {
            $result = $this->indicateurRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => "ID: $id"]);

            return Common::successDelete('Indicateur supprimé avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/indicateurs-search",
     *      operationId="Indicateur searching",
     *      tags={"Indicateur"},
     *      security={{"JWT":{}}},
     *      summary="Return list of Indicateur respecting term",
     *      description="Get all filtered indicateurs using term",
     *
     *      @OA\RequestBody(
     *          description="Body request",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"term"},
     *              @OA\Property(property="term", type="string", example="employés")
     *          )
     *      ),
     *
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function search(Request $request)
    {
        $message = 'Filtrage des indicateurs';

        try {
            $term = $request->input('term');
            $result = $this->indicateurRepository->search($term);
            $this->ls->trace(['action_name' => $message, 'description' => "Term: $term"]);

            return Common::success('Filtrage effectué avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }
}