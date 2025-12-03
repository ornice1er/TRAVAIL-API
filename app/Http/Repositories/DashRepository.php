<?php

namespace App\Http\Repositories;

use App\Models\User;
use App\Models\Media;
use App\Models\Communique;
use App\Models\Transmission;
use App\Models\Doc;
use App\Traits\Repository;
use App\Services\AwsService;
use App\Utilities\Core;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
 

class DashRepository
{
    use Repository;

  

    /**
     * Constructeur
     */
    public function __construct()
    {
    }


        /**
     * Récupère toutes les fêtes avec pagination et filtres.
     */
    public function getAll($request)
    {
      

        $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->count();
        $communiques=Media::with('communique')->where('type','communique')->where('is_published',true)->where('is_archived',false)->count();
        $members=User::count();
        $docs=Doc::count();
        return [
            "stats"=>[
            "actualites"=>$actualites,
            "communiques"=>$communiques,
            "members"=>$members,
            "docs"=>$docs

        ]
        ];

    }


}