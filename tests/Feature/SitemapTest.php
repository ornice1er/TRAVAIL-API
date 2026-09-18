<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_plan_du_site_est_un_xml_valide(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Le plan du site doit être un XML valide.');
        $this->assertGreaterThan(10, $xml->count(), 'Les pages fixes doivent toutes être listées.');
    }

    public function test_le_plan_du_site_pointe_vers_le_site_public(): void
    {
        config(['app.front_url' => 'https://travail.gouv.bj']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('https://travail.gouv.bj/actualites', false)
            ->assertSee('<changefreq>', false);
    }
}
