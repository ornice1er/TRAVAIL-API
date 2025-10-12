<?php

namespace App\Http\Controllers;

use App\Http\Repositories\LinkRepository;
use App\Utilities\Common;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LinkController
{
    /**
     * The Link repository being queried.
     *
     * @var LinkRepository
     */
    protected $repository;

    public function __construct(LinkRepository $linkRepository)
    {
        $this->repository = $linkRepository;
    }

    /** @OA\Get(
     *      path="/links",
     *      operationId="Link list",
     *      tags={"Link"},
     *      summary="Return Link data",
     *      description="Get all links",
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
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/LiensUtile")
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

            return Common::success('Liste des liens utiles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/links/{id}",
     *      operationId="Link show",
     *      tags={"Link"},
     *      summary="Get Link information",
     *      description="Returns Link data",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Link id",
     *          required=true,
     *          in="path",
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
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile")
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
    public function show($id)
    {
        try {
            $result = $this->repository->get($id);

            return Common::success('Détails du lien utile', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/links",
     *      operationId="Link store",
     *      tags={"Link"},
     *      summary="Store new Link",
     *      description="Returns Link data",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile")
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
    public function store(Request $request)
    {
        try {
            $result = $this->repository->makeStore($request->all());

            return Common::success('Lien utile créé avec succès', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Put(
     *      path="/links/{id}",
     *      operationId="Link update",
     *      tags={"Link"},
     *      summary="Update existing Link",
     *      description="Returns updated Link data",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Link id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile")
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
    public function update(Request $request, $id)
    {
        try {
            $result = $this->repository->makeUpdate($id, $request->all());

            return Common::success('Lien utile mis à jour avec succès', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Delete(
     *      path="/links/{id}",
     *      operationId="Link delete",
     *      tags={"Link"},
     *      summary="Delete existing Link",
     *      description="Deletes a record and returns no content",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Link id",
     *          required=true,
     *          in="path",
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
     *          @OA\JsonContent()
     *      ),
     *
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
        try {
            $this->repository->makeDestroy($id);

            return Common::success('Lien utile supprimé avec succès', []);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Post(
     *      path="/links/{id}/status",
     *      operationId="Link change status",
     *      tags={"Link"},
     *      summary="Change Link status",
     *      description="Returns updated Link data",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Link id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="state", type="string", example="active")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/LiensUtile")
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
    public function changeState(Request $request, $id)
    {
        try {
            $result = $this->repository->setStatus($id, $request->input('state'));

            return Common::success('Statut du lien utile modifié avec succès', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }

    /** @OA\Get(
     *      path="/links/search",
     *      operationId="Link search",
     *      tags={"Link"},
     *      summary="Search Links",
     *      description="Returns search results",
     *
     *      @OA\Parameter(
     *          name="keyword",
     *          description="Search keyword",
     *          required=true,
     *          in="query",
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
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/LiensUtile"))
     *          )
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
    public function search(Request $request)
    {
        try {
            $result = $this->repository->search($request->input('keyword', ''));

            return Common::success('Résultats de recherche des liens utiles', $result);
        } catch (\Throwable $th) {
            return Common::error($th->getMessage(), []);
        }
    }
}
