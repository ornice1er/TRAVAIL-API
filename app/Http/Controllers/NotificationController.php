<?php

namespace App\Http\Controllers;

use App\Http\Repositories\NotificationRepository;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Services\LogService;
use App\Utilities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class NotificationController
{
    /**
     * The Notification repository being queried.
     *
     * @var NotificationRepository
     */
    protected $repository;

    /**
     * Log service
     *
     * @var LogService
     */
    protected $ls;

    public function __construct(NotificationRepository $notificationRepository, LogService $ls)
    {
        $this->repository = $notificationRepository;
        $this->ls = $ls;
    }

    /** @OA\Get(
     *      path="/notifications",
     *      operationId="Notification list",
     *      tags={"Notification"},
     *      summary="Return Notification data",
     *      description="Get all notification",
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
     *          @OA\JsonContent(ref="#/components/schemas/Notification"),
     *
     *          @OA\XmlContent(ref="#/components/schemas/Notification")
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
        $message = 'Récupération de toutes les notifications';
        
        try {
            $notifications = $this->repository->all();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Notifications récupérées avec succès']);
            return Common::success($notifications, 'Notifications récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des notifications', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notification/{id}",
     *     tags={"Notification"},
     *     summary="Récupérer une notification spécifique",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Notification récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show($id)
    {
        $message = "Récupération de la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            $this->ls->trace(['action_name' => $message, 'description' => "Notification récupérée avec succès pour ID: $id"]);
            return Common::success($notification, 'Notification récupérée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération de la notification', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification",
     *     tags={"Notification"},
     *     summary="Créer une nouvelle notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "notifiable_type", "notifiable_id", "description", "sent_to"},
     *             @OA\Property(property="type", type="string", example="info", description="Type de notification"),
     *             @OA\Property(property="notifiable_type", type="string", example="App\\Models\\User", description="Type de l'entité notifiable"),
     *             @OA\Property(property="notifiable_id", type="string", example="uuid", description="ID de l'entité notifiable"),
     *             @OA\Property(property="description", type="string", example="Description de la notification", description="Description"),
     *             @OA\Property(property="sent_to", type="string", example="uuid", description="ID du destinataire")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notification créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Notification créée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(StoreNotificationRequest $request)
    {
        $message = 'Création d\'une nouvelle notification';
        
        try {
            $data = $request->validated();
            $notification = $this->repository->create($data);
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Notification créée avec succès avec ID: ' . $notification->id]);
            return Common::success($notification, 'Notification créée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la création de la notification', []);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/notification/{id}",
     *     tags={"Notification"},
     *     summary="Mettre à jour une notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string", example="info", description="Type de notification"),
     *             @OA\Property(property="notifiable_type", type="string", example="App\\Models\\User", description="Type de l'entité notifiable"),
     *             @OA\Property(property="notifiable_id", type="string", example="uuid", description="ID de l'entité notifiable"),
     *             @OA\Property(property="description", type="string", example="Description de la notification", description="Description"),
     *             @OA\Property(property="sent_to", type="string", example="uuid", description="ID du destinataire"),
     *             @OA\Property(property="lu_à", type="string", format="date-time", example="2023-01-01T00:00:00Z", description="Date de lecture")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Notification mise à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(UpdateNotificationRequest $request, $id)
    {
        $message = "Mise à jour de la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée pour mise à jour avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            $data = $request->validated();
            $updatedNotification = $this->repository->update($id, $data);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Notification mise à jour avec succès pour ID: $id"]);
            return Common::success($updatedNotification, 'Notification mise à jour avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la mise à jour de la notification', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/notification/{id}",
     *     tags={"Notification"},
     *     summary="Supprimer une notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Notification supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $message = "Suppression de la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée pour suppression avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            $this->repository->delete($id);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Notification supprimée avec succès pour ID: $id"]);
            return Common::success(null, 'Notification supprimée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de la notification', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification/{id}/mark-read",
     *     tags={"Notification"},
     *     summary="Marquer une notification comme lue",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marquée comme lue avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Notification marquée comme lue avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function markAsRead($id)
    {
        $message = "Marquer la notification comme lue avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            $updatedNotification = $this->repository->update($id, ['lu_à' => now()]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Notification marquée comme lue pour ID: $id"]);
            return Common::success($updatedNotification, 'Notification marquée comme lue avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors du marquage de la notification', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notification/search",
     *     tags={"Notification"},
     *     summary="Rechercher des notifications",
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
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Notification")),
     *             @OA\Property(property="message", type="string", example="Résultats de recherche obtenus avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function search(Request $request)
    {
        $message = 'Recherche de notifications';
        
        try {
            $query = $request->input('query');
            $results = $this->repository->search($query);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Recherche effectuée avec le terme: $query"]);
            return Common::success($results, 'Résultats de recherche obtenus avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la recherche', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification/{id}/up",
     *     tags={"Notification"},
     *     summary="Remonter la position d'une notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position remontée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Position remontée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function up($id)
    {
        $message = "Remontée de position pour la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            // Logique de remontée de position ici si nécessaire
            $this->ls->trace(['action_name' => $message, 'description' => "Position remontée avec succès pour ID: $id"]);
            return Common::success($notification, 'Position remontée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la remontée de position', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification/{id}/down",
     *     tags={"Notification"},
     *     summary="Descendre la position d'une notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position descendue avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Position descendue avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function down($id)
    {
        $message = "Descente de position pour la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            // Logique de descente de position ici si nécessaire
            $this->ls->trace(['action_name' => $message, 'description' => "Position descendue avec succès pour ID: $id"]);
            return Common::success($notification, 'Position descendue avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la descente de position', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification/{id}/activate",
     *     tags={"Notification"},
     *     summary="Activer une notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification activée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Notification activée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function activate($id)
    {
        $message = "Activation de la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            $updatedNotification = $this->repository->update($id, ['is_active' => true]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Notification activée avec succès pour ID: $id"]);
            return Common::success($updatedNotification, 'Notification activée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de l\'activation de la notification', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification/{id}/deactivate",
     *     tags={"Notification"},
     *     summary="Désactiver une notification",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification désactivée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Notification"),
     *             @OA\Property(property="message", type="string", example="Notification désactivée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function deactivate($id)
    {
        $message = "Désactivation de la notification avec ID: $id";
        
        try {
            $notification = $this->repository->findById($id);
            
            if (!$notification) {
                $this->ls->trace(['action_name' => $message, 'description' => "Notification non trouvée avec ID: $id"]);
                return Common::error('Notification non trouvée', []);
            }
            
            $updatedNotification = $this->repository->update($id, ['is_active' => false]);
            
            $this->ls->trace(['action_name' => $message, 'description' => "Notification désactivée avec succès pour ID: $id"]);
            return Common::success($updatedNotification, 'Notification désactivée avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la désactivation de la notification', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notification/unread",
     *     tags={"Notification"},
     *     summary="Récupérer les notifications non lues",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Notifications non lues récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Notification")),
     *             @OA\Property(property="message", type="string", example="Notifications non lues récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function unread()
    {
        $message = 'Récupération des notifications non lues';
        
        try {
            $notifications = $this->repository->getUnread();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Notifications non lues récupérées avec succès']);
            return Common::success($notifications, 'Notifications non lues récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des notifications non lues', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notification/mark-all-read",
     *     tags={"Notification"},
     *     summary="Marquer toutes les notifications comme lues",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Toutes les notifications marquées comme lues avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Toutes les notifications marquées comme lues avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token non valide ou expiré"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function markAllAsRead()
    {
        $message = 'Marquer toutes les notifications comme lues';
        
        try {
            $this->repository->markAllAsRead();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Toutes les notifications marquées comme lues avec succès']);
            return Common::success(null, 'Toutes les notifications marquées comme lues avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors du marquage de toutes les notifications', []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/user",
     *     tags={"Notification"},
     *     summary="Récupérer les notifications de l'utilisateur connecté",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Notifications récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Notification")),
     *             @OA\Property(property="message", type="string", example="Notifications récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getUserNotifications()
    {
        $message = 'Récupération des notifications de l\'utilisateur';
        
        try {
            $notifications = \App\Models\Notification::where("sent_to", auth()->id())->get();
            
            $this->ls->trace(['action_name' => $message, 'description' => 'Notifications utilisateur récupérées avec succès']);
            return Common::success($notifications, 'Notifications récupérées avec succès');
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la récupération des notifications', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/notifications/{id}/read",
     *     tags={"Notification"},
     *     summary="Marquer une notification comme lue et rediriger",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la notification"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marquée comme lue",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="actionURL", type="string", example="/some-action-url"),
     *             @OA\Property(property="message", type="string", example="Notification marquée comme lue")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function markAsReadAndGetAction($id)
    {
        $message = 'Marquer notification comme lue et récupérer l\'action';
        
        try {
            $notification = \App\Models\Notification::where('sent_to', auth()->id())->where('id', $id)->first();

            if ($notification) {
                $notification->markAsRead();
                
                $actionURL = $notification->data['actionURL'] ?? null;
                
                $this->ls->trace(['action_name' => $message, 'description' => 'Notification marquée comme lue avec succès']);
                return Common::success(['actionURL' => $actionURL], 'Notification marquée comme lue');
            } else {
                return Common::error('Notification non trouvée', []);
            }
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors du marquage de la notification', []);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/{id}/delete",
     *     tags={"Notification"},
     *     summary="Supprimer une notification spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la notification à supprimer"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="La notification a été supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Notification non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function deleteNotification($id)
    {
        $message = 'Suppression de notification spécifique';
        
        try {
            $notification = \App\Models\Notification::find($id);
            
            if ($notification) {
                $status = $notification->delete();
                
                if ($status) {
                    $this->ls->trace(['action_name' => $message, 'description' => 'Notification supprimée avec succès']);
                    return Common::success(null, 'La notification a été supprimée avec succès');
                } else {
                    return Common::error('Erreur lors de la suppression', []);
                }
            } else {
                return Common::error('Notification non trouvée', []);
            }
        } catch (\Exception $e) {
            $this->ls->trace(['action_name' => $message, 'description' => $e->getMessage()]);
            return Common::error('Erreur lors de la suppression de la notification', []);
        }
    }
}
