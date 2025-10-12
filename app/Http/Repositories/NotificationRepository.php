<?php

namespace App\Http\Repositories;

use App\Models\Notification;
use App\Traits\Repository;

class NotificationRepository
{
    use Repository;

    /**
     * The model being queried.
     *
     * @var Notification
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = app(Notification::class);
    }

    /**
     * Récupère toutes les notifications.
     */
    public function all()
    {
        return Notification::with(['notifiable', 'destinataire'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère une notification par ID.
     */
    public function findById($id)
    {
        return Notification::with(['notifiable', 'destinataire'])->find($id);
    }

    /**
     * Crée une nouvelle notification.
     */
    public function create($data)
    {
        return Notification::create($data);
    }

    /**
     * Met à jour une notification.
     */
    public function update($id, $data)
    {
        $notification = Notification::findOrFail($id);
        $notification->update($data);
        return $notification->fresh();
    }

    /**
     * Supprime une notification.
     */
    public function delete($id)
    {
        $notification = Notification::findOrFail($id);
        return $notification->delete();
    }

    /**
     * Recherche dans les notifications.
     */
    public function search($query)
    {
        return Notification::with(['notifiable', 'destinataire'])
            ->where('description', 'like', "%{$query}%")
            ->orWhere('type', 'like', "%{$query}%")
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère les notifications non lues.
     */
    public function getUnread()
    {
        return Notification::with(['notifiable', 'destinataire'])
            ->whereNull('lu_à')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Marque toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        return Notification::whereNull('lu_à')->update(['lu_à' => now()]);
    }

    /**
     * Vérifie si existe
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère toutes les notifications avec pagination et filtres.
     */
    public function getAll($request)
    {
        $per_page = 10;

        $req = Notification::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->with(['notifiable', 'destinataire'])
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }

    /**
     * Récupère une notification spécifique.
     */
    public function getById($id)
    {
        return Notification::with(['notifiable', 'destinataire'])->findOrFail($id);
    }

    /**
     * Crée une nouvelle notification (alias pour store).
     */
    public function store($data)
    {
        return Notification::create($data);
    }

    /**
     * Supprime une notification (alias pour destroy).
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        return $notification->delete();
    }

    /**
     * Récupère une notification.
     */
    public function get($id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Crée une nouvelle notification.
     */
    public function makeStore($data): Notification
    {
        $model = new Notification($data);
        $model->save();
        return $model;
    }

    /**
     * Met à jour une notification.
     */
    public function makeUpdate($id, $data): Notification
    {
        $model = Notification::findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * Supprime une notification.
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Récupère les plus récentes.
     */
    public function getlatest()
    {
        return $this->latest()->get();
    }

    /**
     * Modifie le statut d'une notification.
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['is_active' => $status]);
    }
}
