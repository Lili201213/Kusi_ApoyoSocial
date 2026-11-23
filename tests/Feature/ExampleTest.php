<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test de la ruta principal: redirige al portal de transparencia público.
     */
    public function test_la_ruta_principal_redirige_al_portal_publico(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('publico'));
    }

    /**
     * Test de cambio de idioma válido.
     */
    public function test_cambio_de_idioma_a_ingles_y_frances(): void
    {
        $responseEn = $this->get('/idioma/en');
        $responseEn->assertSessionHas('locale', 'en');

        $responseFr = $this->get('/idioma/fr');
        $responseFr->assertSessionHas('locale', 'fr');

        $responseEs = $this->get('/idioma/es');
        $responseEs->assertSessionHas('locale', 'es');
    }

    /**
     * Test de idioma no soportado debe retornar 404.
     */
    public function test_idioma_invalido_retorna_404(): void
    {
        $response = $this->get('/idioma/invalido');

        $response->assertStatus(404);
    }
}
