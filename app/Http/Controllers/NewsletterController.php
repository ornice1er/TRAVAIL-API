<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Http\Repositories\NewsletterRepository;
use App\Services\LogService;
use App\Http\Requests\Newsletter\StoreNewsletterRequest;
use App\Http\Requests\Newsletter\UpdateNewsletterRequest;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class NewsletterController
{
    /**
     * The Newsletter repository being queried.
     *
     * @var NewsletterRepository
     */
    protected $newsletterRepository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(NewsletterRepository $newsletterRepository, LogService $ls)
    {
        $this->newsletterRepository = $newsletterRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/newsletters",
     *      operationId="Newsletter list",
     *      tags={"Newsletter"},
     *      summary="Return Newsletter data",
     *      description="Get all newsletter",
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
     *          @OA\JsonContent(ref="#/components/schemas/Newsletter"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Newsletter")
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
    public function index()
    {
        $message = 'Récupération de toutes les inscriptions newsletter';
        
        try {
            $newsletters = $this->newsletterRepository->all();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Inscriptions newsletter récupérées avec succès']);
            return Common::success($newsletters, 'Inscriptions newsletter récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des inscriptions newsletter', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/newsletter/{id}",
     *     tags={"Newsletter"},
     *     summary="Récupérer une inscription newsletter spécifique",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = "Récupération de l'inscription newsletter avec ID: $id";
        
        try {
            $newsletter = $this->newsletterRepository->findById($id);
            
            if (!$newsletter) {
                $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter non trouvée avec ID: $id"]);
                return Common::error('Inscription newsletter non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter récupérée avec succès pour ID: $id"]);
            return Common::success($newsletter, 'Inscription newsletter récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de l\'inscription newsletter', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter",
     *     tags={"Newsletter"},
     *     summary="Créer une nouvelle inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titre", "structure_id"},
     *             @OA\Property(property="titre", type="string", example="email@example.com", description="Adresse email"),
     *             @OA\Property(property="structure_id", type="string", example="uuid", description="ID de la structure")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inscription newsletter créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter créée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(StoreNewsletterRequest $request)
    {
        $message = 'Création d\'une nouvelle inscription newsletter';
        
        try {
            $data = $request->validated();
            $data['ajouté_par'] = Auth::id();
            $data['status'] = 'actif';
            
            $newsletter = $this->newsletterRepository->create($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Inscription newsletter créée avec succès avec ID: ' . $newsletter->id]);
            return Common::success($newsletter, 'Inscription newsletter créée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création de l\'inscription newsletter', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/newsletter/{id}",
     *     tags={"Newsletter"},
     *     summary="Mettre à jour une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="titre", type="string", example="email@example.com", description="Adresse email"),
     *             @OA\Property(property="structure_id", type="string", example="uuid", description="ID de la structure"),
     *             @OA\Property(property="status", type="string", example="actif", description="Statut de l'inscription")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter mise à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(UpdateNewsletterRequest $request, $id)
    {
        $message = "Mise à jour de l'inscription newsletter avec ID: $id";
        
        try {
            $newsletter = $this->newsletterRepository->findById($id);
            
            if (!$newsletter) {
                $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter non trouvée pour mise à jour avec ID: $id"]);
                return Common::error('Inscription newsletter non trouvée', []);
            }
            
            $data = $request->validated();
            $updatedNewsletter = $this->newsletterRepository->update($id, $data);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter mise à jour avec succès pour ID: $id"]);
            return Common::success($updatedNewsletter, 'Inscription newsletter mise à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de l\'inscription newsletter', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/newsletter/{id}",
     *     tags={"Newsletter"},
     *     summary="Supprimer une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = "Suppression de l'inscription newsletter avec ID: $id";
        
        try {
            $newsletter = $this->newsletterRepository->findById($id);
            
            if (!$newsletter) {
                $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter non trouvée pour suppression avec ID: $id"]);
                return Common::error('Inscription newsletter non trouvée', []);
            }
            
            $this->newsletterRepository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter supprimée avec succès pour ID: $id"]);
            return Common::success(null, 'Inscription newsletter supprimée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de l\'inscription newsletter', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/change-state",
     *     tags={"Newsletter"},
     *     summary="Changer l'état d'une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="actif", description="Nouveau statut")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="État de l'inscription newsletter modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="État de l'inscription newsletter modifié avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function changeState(Request $request, $id)
    {
        $message = "Changement d'état de l'inscription newsletter avec ID: $id";
        
        try {
            $newsletter = $this->newsletterRepository->findById($id);
            
            if (!$newsletter) {
                $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter non trouvée avec ID: $id"]);
                return Common::error('Inscription newsletter non trouvée', []);
            }
            
            $status = $request->input('status');
            $updatedNewsletter = $this->newsletterRepository->update($id, ['status' => $status]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "État changé vers '$status' pour ID: $id"]);
            return Common::success($updatedNewsletter, 'État de l\'inscription newsletter modifié avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors du changement d\'état', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/newsletter/search",
     *     tags={"Newsletter"},
     *     summary="Rechercher des inscriptions newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Terme de recherche",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche obtenus avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Newsletter")),
     *             @OA\Property(property="message", type="string", example="Résultats de recherche obtenus avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function search(Request $request)
    {
        $message = 'Recherche d\'inscriptions newsletter';
        
        try {
            $query = $request->input('query');
            $results = $this->newsletterRepository->search($query);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Recherche effectuée avec le terme: $query"]);
            return Common::success($results, 'Résultats de recherche obtenus avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la recherche', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/publish",
     *     tags={"Newsletter"},
     *     summary="Publier une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter publiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter publiée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function publish($id)
    {
        return $this->changeState(new Request(['status' => 'actif']), $id);
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/unpublish",
     *     tags={"Newsletter"},
     *     summary="Dépublier une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter dépubliée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter dépubliée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function unpublish($id)
    {
        return $this->changeState(new Request(['status' => 'inactif']), $id);
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/archive",
     *     tags={"Newsletter"},
     *     summary="Archiver une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter archivée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter archivée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function archive($id)
    {
        return $this->changeState(new Request(['status' => 'archivé']), $id);
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/restore",
     *     tags={"Newsletter"},
     *     summary="Restaurer une inscription newsletter archivée",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription newsletter restaurée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Inscription newsletter restaurée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function restore($id)
    {
        return $this->changeState(new Request(['status' => 'actif']), $id);
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/up",
     *     tags={"Newsletter"},
     *     summary="Remonter la position d'une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position remontée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Position remontée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function up($id)
    {
        $message = "Remontée de position pour l'inscription newsletter avec ID: $id";
        
        try {
            $newsletter = $this->newsletterRepository->findById($id);
            
            if (!$newsletter) {
                $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter non trouvée avec ID: $id"]);
                return Common::error('Inscription newsletter non trouvée', []);
            }
            
            // Logique de remontée de position ici si nécessaire
            $this->ls->trace(['action_name' => $message, 'description' => "Position remontée avec succès pour ID: $id"]);
            return Common::success($newsletter, 'Position remontée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la remontée de position', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/newsletter/{id}/down",
     *     tags={"Newsletter"},
     *     summary="Descendre la position d'une inscription newsletter",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'inscription newsletter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position descendue avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Newsletter"),
     *             @OA\Property(property="message", type="string", example="Position descendue avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inscription newsletter non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function down($id)
    {
        $message = "Descente de position pour l'inscription newsletter avec ID: $id";
        
        try {
            $newsletter = $this->newsletterRepository->findById($id);
            
            if (!$newsletter) {
                $this->ls->trace(['action_name' => $message, 'description' => "Inscription newsletter non trouvée avec ID: $id"]);
                return Common::error('Inscription newsletter non trouvée', []);
            }
            
            // Logique de descente de position ici si nécessaire
            $this->ls->trace(['action_name' => $message, 'description' => "Position descendue avec succès pour ID: $id"]);
            return Common::success($newsletter, 'Position descendue avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la descente de position', []);
        }
    }
}
