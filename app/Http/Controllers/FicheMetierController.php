<?php

namespace App\Http\Controllers;

use App\Http\Repositories\FicheMetierRepository;
use App\Http\Requests\FicheMetier\StoreFicheMetierRequest;
use App\Http\Requests\FicheMetier\UpdateFicheMetierRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FicheMetierController extends Controller
{
    /**
     * The FicheMetier repository being queried.
     *
     * @var FicheMetierRepository
     */
    protected $ficheMetierRepository;

    protected $ls;

    public function __construct(FicheMetierRepository $ficheMetierRepository, LogService $ls)
    {
        $this->ficheMetierRepository = $ficheMetierRepository;
        $this->ls = $ls;
    }

    /**
     * @OA\Get(
     *      path="/fiche-metiers",
     *      operationId="FicheMetier list",
     *      tags={"FicheMetier"},
     *      security={{"JWT":{}}},
     *      summary="Return FicheMetier data",
     *      description="Get all fiche metiers",
     *
     *      @OA\Parameter(
     *          name="titre",
     *          in="query",
     *          description="Can be used for filtering data by titre",
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
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not found"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index(Request $request)
    {
        $message = 'Récupération de la liste des fiches métiers';

        try {
            $result = $this->ficheMetierRepository->getAll($request);
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->all())]);

            return Common::success($message, $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Get(
     *      path="/fiche-metiers/{id}",
     *      operationId="FicheMetier show",
     *      tags={"FicheMetier"},
     *      security={{"JWT":{}}},
     *      summary="Return one FicheMetier data",
     *      description="Get FicheMetier by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="FicheMetier ID",
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
        $message = 'Récupération d\'une fiche métier';

        try {
            $result = $this->ficheMetierRepository->get($id);
            $this->ls->trace(['action_name' => $message, 'description' => "ID: $id"]);

            return Common::success('Fiche métier trouvée', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/fiche-metiers",
     *      operationId="FicheMetier store",
     *      tags={"FicheMetier"},
     *      security={{"JWT":{}}},
     *      summary="Store FicheMetier data",
     *      description="Create a new FicheMetier",
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"titre","resume","description","structure_id","thematique"},
     *              @OA\Property(property="titre", type="string", example="Développeur Web"),
     *              @OA\Property(property="resume", type="string", example="Développement d'applications web"),
     *              @OA\Property(property="description", type="string", example="Description détaillée du métier"),
     *              @OA\Property(property="structure_id", type="integer", example=1),
     *              @OA\Property(property="thematique", type="array", @OA\Items(type="string"), example={"Web", "Frontend", "Backend"})
     *          )
     *      ),
     *
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreFicheMetierRequest $request)
    {
        $message = 'Enregistrement d\'une fiche métier';

        try {
            $result = $this->ficheMetierRepository->makeStore($request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::successCreate('Fiche métier créée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Put(
     *      path="/fiche-metiers/{id}",
     *      operationId="FicheMetier update",
     *      tags={"FicheMetier"},
     *      security={{"JWT":{}}},
     *      summary="Update one FicheMetier data",
     *      description="Update FicheMetier by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="FicheMetier ID",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\RequestBody(
     *          description="body request",
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="resume", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="structure_id", type="integer"),
     *              @OA\Property(property="thematique", type="array", @OA\Items(type="string"))
     *          )
     *      ),
     *
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not found"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateFicheMetierRequest $request, $id)
    {
        $message = 'Mise à jour d\'une fiche métier';

        try {
            $result = $this->ficheMetierRepository->makeUpdate($id, $request->validated());
            $this->ls->trace(['action_name' => $message, 'description' => json_encode($request->validated())]);

            return Common::success('Mise à jour de la fiche métier effectuée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Delete(
     *      path="/fiche-metiers/{id}",
     *      operationId="FicheMetier Delete",
     *      tags={"FicheMetier"},
     *      security={{"JWT":{}}},
     *      summary="Delete FicheMetier data",
     *      description="Delete FicheMetier by ID",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="FicheMetier ID",
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
        $message = 'Suppression d\'une fiche métier';

        try {
            $result = $this->ficheMetierRepository->makeDestroy($id);
            $this->ls->trace(['action_name' => $message, 'description' => "ID: $id"]);

            return Common::successDelete('Fiche métier supprimée avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }

    /**
     * @OA\Post(
     *      path="/fiche-metiers-search",
     *      operationId="FicheMetier searching",
     *      tags={"FicheMetier"},
     *      security={{"JWT":{}}},
     *      summary="Return list of FicheMetier respecting term",
     *      description="Get all filtered fiche metiers using term",
     *
     *      @OA\RequestBody(
     *          description="Body request",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"term"},
     *              @OA\Property(property="term", type="string", example="développeur")
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
        $message = 'Filtrage des fiches métiers';

        try {
            $term = $request->input('term');
            $result = $this->ficheMetierRepository->search($term);
            $this->ls->trace(['action_name' => $message, 'description' => "Term: $term"]);

            return Common::success('Filtrage effectué avec succès', $result);
        } catch (\Throwable $th) {
            $this->ls->trace(['action_name' => $message, 'description' => $th->getMessage()]);
            return Common::error($th->getMessage(), []);
        }
    }
}