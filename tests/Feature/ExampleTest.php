<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_diagnostico_ruta_principal(): void
    {
        $this->withoutMiddleware();
        $this->withoutExceptionHandling();

        $response = $this->get('/');

        dd($response->status());
    }
}
