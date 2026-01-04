<?php

namespace App\Http\Repositories;

use App\Models\Newsletter;
use App\Models\Structure;
use App\Models\Structures;
use App\Models\TypeStructure;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class NewsletterRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Newsletter
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Newsletter::class);
    }

    /**
     * Récupère toutes les inscriptions newsletter.
     */
    public function all()
    {
        return Newsletter::with(['user', 'structure'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère une inscription newsletter par ID.
     */
    public function findById($id)
    {
        return Newsletter::with(['user', 'structure'])->find($id);
    }

    /**
     * Crée une nouvelle inscription newsletter.
     */
    public function create($data)
    {
        return Newsletter::create($data);
    }

    /**
     * Met à jour une inscription newsletter.
     */
    public function update($id, $data)
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->update($data);
        return $newsletter->fresh();
    }

    /**
     * Supprime une inscription newsletter.
     */
    public function delete($id)
    {
        $newsletter = Newsletter::findOrFail($id);
        return $newsletter->delete();
    }

    /**
     * Recherche dans les inscriptions newsletter.
     */
    public function search($query)
    {
        return Newsletter::with(['user', 'structure'])
            ->where('titre', 'like', "%{$query}%")
            ->orWhereHas('structure', function ($q) use ($query) {
                $q->where('nom', 'like', "%{$query}%");
            })
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Change l'état d'une inscription newsletter.
     */
    public function changeState($status, $id)
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->update(['status' => $status]);
        return $newsletter->fresh();
    }

    /**
     * Publie une inscription newsletter.
     */
    public function publish($id)
    {
        return $this->changeState('actif', $id);
    }

    /**
     * Dépublie une inscription newsletter.
     */
    public function unpublish($id)
    {
        return $this->changeState('inactif', $id);
    }

    /**
     * Archive une inscription newsletter.
     */
    public function archive($id)
    {
        return $this->changeState('archivé', $id);
    }

    /**
     * Restaure une inscription newsletter.
     */
    public function restore($id)
    {
        return $this->changeState('actif', $id);
    }

    /**
     * Remonte la position d'une inscription newsletter.
     */
    public function up($id)
    {
        return true; // Logique de remontée si nécessaire
    }

    /**
     * Descend la position d'une inscription newsletter.
     */
    public function down($request, $id)
    {
        return true; // Logique de descente si nécessaire
    }

    /**
     * Vérifie si un email est déjà inscrit.
     */
    public function isSubscribed($email)
    {
        return Newsletter::where('titre', $email)->exists();
    }

    /**
     * Inscription depuis le front-end.
     */
    public function subscribe()
    {
        $email = request()->input('email');

        $type = TypeStructure::where('is_parent', true)->first();
        $structure = Structure::where('type_structure_id', $type->id)->first();

        if (!$this->isSubscribed($email)) {
            $newsletter = Newsletter::create([
                "titre" => $email,
                "structure_id" => $structure->id,
                "status" => "actif"
            ]);

            if ($newsletter) {
                try {
                    Mail::send("email.newsletter", [], function ($message) use ($email) {
                        $message->from(env('MAIL_FROM_ADDRESS'), env("MAIL_FROM_NAME"))
                            ->subject("Newsletter MTFP")
                            ->to($email, "Abonné MTFP");
                    });

                    return response()->json([
                        'success' => true,
                        'message' => 'Inscrit! Veuillez consulter vos mails.'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Inscription réussie mais erreur lors de l\'envoi de l\'email.'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'inscription.'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Déjà inscrit !'
            ]);
        }
    }

    /**
     * Récupère toutes les inscriptions avec pagination et filtres.
     */
    public function getAll($request)
    {
        $per_page = 10;

        $req = Newsletter::ignoreRequest(['per_page','pageSize','page'])
            ->filter(array_filter($request->all(), function ($k) {
                return $k != 'page';
            }, ARRAY_FILTER_USE_KEY))
            ->with(['user', 'structure'])
            ->orderByDesc('created_at');

        if (array_key_exists('per_page', $request->all())) {
            $per_page = $request['per_page'];
            return $req->paginate($per_page);
        } else {
            return $req->get();
        }
    }

    /**
     * Récupère une inscription spécifique.
     */
    public function getById($id)
    {
        return Newsletter::with(['user', 'structure'])->findOrFail($id);
    }

    /**
     * Crée une nouvelle inscription (alias pour store).
     */
    public function store($data)
    {
        return Newsletter::create($data);
    }

    /**
     * Supprime une inscription (alias pour destroy).
     */
    public function destroy($id)
    {
        $newsletter = Newsletter::findOrFail($id);
        return $newsletter->delete();
    }
}
