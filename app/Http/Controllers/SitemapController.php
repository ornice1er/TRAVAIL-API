<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Communique;
use Illuminate\Http\Response;

/**
 * Plan du site pour les moteurs de recherche.
 *
 * Le site public est une application monopage : sans ce fichier, les pages
 * de détail (actualités, communiqués) ne sont découvrables que par les liens
 * internes. Les URL pointent vers le site public (APP_FRONT_URL).
 */
class SitemapController extends Controller
{
    /** Pages fixes du site public, avec leur fréquence de mise à jour. */
    private array $staticPages = [
        ['', 'daily', '1.0'],
        ['actualites', 'daily', '0.9'],
        ['communiques', 'daily', '0.9'],
        ['concours', 'daily', '0.9'],
        ['services', 'weekly', '0.8'],
        ['textes-lois', 'weekly', '0.7'],
        ['fiches-metiers', 'monthly', '0.6'],
        ['contact', 'yearly', '0.5'],
        ['ministere', 'monthly', '0.7'],
        ['ministere/le-ministre', 'monthly', '0.6'],
        ['ministere/le-cabinet', 'monthly', '0.5'],
        ['ministere/notre-vision', 'yearly', '0.5'],
        ['ministere/secretariat-general', 'monthly', '0.5'],
        ['ministere/inspection-generale', 'monthly', '0.5'],
        ['ministere/direction-planification', 'monthly', '0.5'],
        ['ministere/direction-systemes-information', 'monthly', '0.5'],
        ['ministere/direction-generale-travail', 'monthly', '0.5'],
        ['ministere/direction-generale-fonction-publique', 'monthly', '0.5'],
        ['ministere/direction-budget', 'monthly', '0.5'],
        ['ministere/direction-renforcement-capacites', 'monthly', '0.5'],
        ['ministere/cellule-suivi-reformes', 'monthly', '0.5'],
        ['ministere/directions-departementales', 'monthly', '0.5'],
        ['ministere/structures-sous-tutelle', 'monthly', '0.5'],
    ];

    public function index(): Response
    {
        $base = rtrim(config('app.front_url') ?: config('app.url'), '/');
        $urls = [];

        foreach ($this->staticPages as [$path, $frequency, $priority]) {
            $urls[] = [
                'loc' => $path === '' ? $base.'/' : $base.'/'.$path,
                'changefreq' => $frequency,
                'priority' => $priority,
            ];
        }

        foreach ($this->publishedSlugs(Actualite::class, 'actualite') as $item) {
            $urls[] = [
                'loc' => $base.'/actualites/'.$item->slug,
                'lastmod' => optional($item->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        foreach ($this->publishedSlugs(Communique::class, 'communique') as $item) {
            $urls[] = [
                'loc' => $base.'/communiques/'.$item->slug,
                'lastmod' => optional($item->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Contenus publiés uniquement : un brouillon ne doit pas être indexé.
     */
    private function publishedSlugs(string $model, string $mediaType)
    {
        return $model::query()
            ->whereHas('media', function ($query) use ($mediaType) {
                $query->where('type', $mediaType)
                    ->where('is_published', true)
                    ->where('is_archived', false);
            })
            ->orderByDesc('created_at')
            ->get(['slug', 'updated_at']);
    }
}
