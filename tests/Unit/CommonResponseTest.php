<?php

namespace Tests\Unit;

use App\Utilities\Common;
use Tests\TestCase;

/**
 * Plusieurs contrôleurs appelaient Common::success() avec les arguments
 * inversés : le front recevait le message à la place des données.
 */
class CommonResponseTest extends TestCase
{
    public function test_success_place_le_message_et_les_donnees_dans_le_bon_ordre(): void
    {
        $response = Common::success('Actualités récupérées', ['id' => 1]);
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($payload['status']);
        $this->assertSame('Actualités récupérées', $payload['message']);
        $this->assertSame(['id' => 1], $payload['data']);
    }

    public function test_not_found_renvoie_un_404(): void
    {
        $response = Common::notFound();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['status']);
    }

    public function test_error_renvoie_un_500_sans_donnees(): void
    {
        $response = Common::error('Erreur serveur', []);
        $payload = $response->getData(true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($payload['status']);
        $this->assertNull($payload['data']);
    }
}
