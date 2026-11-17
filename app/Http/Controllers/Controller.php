<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API KUSI',
    version: '1.0.0',
    description: 'Documentación de la API del sistema KUSI - Sociedad de Apoyo Social'
)]
#[OA\Server(url: 'http://localhost/kusi/public', description: 'Servidor local')]
abstract class Controller
{
    //
}
