<?php

namespace App\Http\Repositories;

use QrCode;
use App\Models\Aof;
use App\Models\User;
use App\Models\Media;
use App\Models\Invite;
use App\Utilities\Core;
use App\Models\Parcours;
use App\Traits\Repository;
use Illuminate\Support\Str;
use App\Models\Transmission;
use App\Utilities\FileStorage;
use Illuminate\Support\Facades\Auth;

 

class AofRepository
{
    use Repository;

    /**
     * Le modèle utilisé.
     *
     * @var Aof
     */
    protected $model;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->model = app(Aof::class);
    }

    /**
     * Vérifie si la fête existe.
     */
    public function ifExist($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère toutes les fêtes avec pagination et filtres.
     */
    public function getAll($request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $per_page = 10;

        $query = Media::with(['aof'])
            ->where('type', 'aof')
            ->where('structure_id', $user->structure_id)
            ->whereHas('transmissions', function ($q) use ($user_id) {
                $q->where('to', $user_id)
                ->where('is_last', true);
            });

        // Gestion de la pagination personnalisée
        if ($request->has('pageSize')) {
            $per_page = $request->input('pageSize');
            $medias = $query->paginate($per_page);
        } else {
            return $query->get();
        }
    }

    /**
     * Récupère une fête spécifique.
     */
    public function get($id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Crée une nouvelle fête.
     */
    public function makeStore($data): Aof
    {
        // ✅ Validation
        $validator = validator($data, [
            'mission' => 'required|string',
            'aof' => 'required|file', // fichier obligatoire
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // 🛠 Création du media
        $media = new Media();
        $media->code = Str::uuid();
        $media->structure_id = Auth::user()->structure_id;
        $media->has_principal_access = $data['has_principal_access'] ?? false;
        $media->type = "aof";
        $media->save();

        // 📦 Préparer les données Aof
        $aofData = [];
        if (isset($data['mission'])) $aofData['mission'] = $data['mission'];
        if (isset($data['attribution'])) $aofData['attribution'] = $data['attribution'];
        
        $slug = Str::slug($data['name'] ?? 'aof') . '-' . date('ymdis') . '-' . rand(0, 999);

        if (isset($data['aof'])) {
            $aofData['aof'] = FileStorage::setFile('public', $data['aof'], 'aofs', $slug);
        }
        $aofData['media_id'] = $media->id;

        // 📝 Création de l'Aof
        $model = new Aof($aofData);
        $model->save();

        // 🧭 Historique
        Parcours::create([
            'media_id' => $media->id,
            'libelle' => "Création de l'AOF " . ($data['name'] ?? '[Sans nom]'),
        ]);

        // 🔁 Transmission
        Transmission::create([
            'from' => Auth::id(),
            'to' => Auth::id(),
            'media_id' => $media->id,
            'is_last' => true,
        ]);

        return $model;
    }

    /**
     * Met à jour une fête.
     */
    public function makeUpdate($id, $data): Aof
    {
        // ✅ Récupérer l'AOF et son media associé
        $model = Aof::findOrFail($id);
        $media = $model->media;

        // ✅ Validation
        $validator = validator($data, [
            'mission' => 'nullable|string',
            'has_principal_access' => 'nullable|boolean',
            'aof' => 'nullable|file',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // ✅ Mise à jour du média (si champ fourni)
        if ($media && isset($data['has_principal_access'])) {
            $media->has_principal_access = $data['has_principal_access'];
            $media->save();
        }

        // 📦 Préparer les données AOF à mettre à jour
        $updateData = [];
        if (isset($data['mission'])) $updateData['mission'] = $data['mission'];
        if (isset($data['attribution'])) $updateData['attribution'] = $data['attribution'];

        // 📁 Gestion du remplacement du fichier
        if (isset($data['aof'])) {
            // Supprimer l'ancien fichier
            FileStorage::deleteFile('public', $model->aof);

            // Générer un nouveau slug
            $slug = Str::slug($data['name'] ?? 'aof') . '-' . date('ymdis') . '-' . rand(0, 999);

            // Enregistrer le nouveau fichier
            $updateData['aof'] = FileStorage::setFile('public', $data['aof'], 'aofs', time() . $slug);
        }

        // 📝 Mettre à jour le modèle Aof
        $model->update($updateData);

        return $model;
    }

    /**
     * Supprime une fête.
     */
    public function makeDestroy($id)
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Récupère les fêtes les plus récentes.
     */
    public function getlatest()
    {
        return $this->latest()->get();
    }

    /**
     * Modifie le statut d'une fête.
     */
    public function setStatus($id, $status)
    {
        return $this->findOrFail($id)->update(['status' => $status]);
    }

    /**
     * Recherche dans les fêtes (par nom, lieu...).
     */
    public function search($term)
    {
        $query = Aof::query();
        $attrs = ['nom', 'lieu', 'type_Aof'];
        
        foreach ($attrs as $value) {
            $query->orWhere($value, 'like', '%'.$term.'%');
        }

        return $query->get();
    }

    /*function generateLink($id,$data) {
        $code = Core::generateUniqueCode(Aof::class, 10, 'FET');
        $link_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Aof/'.$code.'/'.$link_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $data['link_token']=$link_token;
        $data['code']=$code;
        $data['lien_unique']=$url ;
        $data['qr_code'] = base64_encode($qrCode);
        $data['status'] = 1;
        $model = Aof::findOrFail($id);
        $model->update($data);
        return $model;
    }

    function generateMediaLink($id) {
        $model = Aof::findOrFail($id);
        $code = $model->code;
        $media_token = Str::random(40); // Génère un token de 40 caractères
        $url = env('APP_FRONT_URL').'/Aof-gallery/'.$code.'/'.$media_token;
        $url = mb_convert_encoding($url, 'UTF-8', 'auto'); // Force l'encodage en UTF-8
        $qrCodeMedia = QrCode::format('png')->size(300)->generate($url);
        $data['media_token']=$media_token;
        $data['lien_unique_media']=$url ;
        $data['qr_code_media'] = base64_encode($qrCodeMedia);
        $data['status'] = 2;
        $model->update($data);
        return $model;
    }

    function verifyLink($data) {
        if (isset($data['link_token'])) {
            return Aof::where('link_token', )->first();
        }else{
            return Aof::where('media_token', $data['media_token'])->first();

        }

    }

    function participate($data){
        $check=Invite::where('Aof_id', $data['Aof_id'])
            ->where('phone', $data['phone'])
            ->first();
            if ($check) {
                $check->update($data);
            }else{
                $model = new Invite($data);
                $model->save();
            }
   

        return $model;

    }*/

    public function up($id)
    {      
            $user = Auth::user();
            $media = Media::with('transmissions')->findOrFail($id);

            $lastTransmission = $media->transmissions->last();
            if ($lastTransmission) {
                $lastTransmission->update(['is_last' => false]);
            }

            $toUser = User::where('structure_id', $user->structure_id)
                ->role('validation')
                ->first();

            if (!$toUser) {
                return response()->json(['error' => 'Aucun utilisateur avec le rôle "validation" trouvé.'], 404);
            }

            Transmission::create([
                'from' => $user->id,
                'to' => $toUser->id,
                'media_id' => $media->id,
                'is_last' => true,
            ]);

            return response()->json(['success' => true]);

    }

    public function down($id)
    {
                // Récupérer le media
            $media = Media::findOrFail($id);

            // Mettre à jour le motif (supposons que le motif provient d'une requête)
            // Si tu souhaites récupérer le motif depuis la requête, il faudra ajouter le paramètre Request $request
            // Ici, je mets un motif vide ou tu peux adapter selon ton besoin
            $media->update(['motif' => 'Retour vers saisie']);

            // Marquer la dernière transmission comme non active
            $lastTransmission = $media->transmissions->last();
            if ($lastTransmission) {
                $lastTransmission->update(['is_last' => false]);
            }

            // Trouver l'utilisateur avec le rôle 'saisie' dans la même structure
            $toUser = User::where('structure_id', $media->structure_id)
                ->role('saisie')
                ->first();

            if (!$toUser) {
                // Gestion d'erreur simplifiée, tu peux adapter
                return back()->withErrors(['Aucun utilisateur avec le rôle "saisie" trouvé dans cette structure.']);
            }

            // Créer la nouvelle transmission
            Transmission::create([
                'from' => Auth::id(),
                'to' => $toUser->id,
                'media_id' => $media->id,
                'is_last' => true,
            ]);

            // Retourner vrai (ou rediriger)
            return true;
    }

    public function publish($id)
    {      
        $media = Media::findOrFail($id);
        $media->update(['is_published' => true]);

        return true;
    }

    public function unpublish($id)
    {
         $media = Media::findOrFail($id);
         $media->update(['is_published' => false]);

        return true;
    }

    public function archive($id)
    {
         $media = Media::findOrFail($id);
         $media->update(['is_archived' => true]);

        return true;
    }

    public function restore($id)
    {
        $media = Media::findOrFail($id);
        $media->update(['is_archived' => false]);

        return true;
    }

}
