<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donacion;
use OpenApi\Attributes as OA;

class DonacionApiController extends Controller
{
    #[OA\Get(
        path: '/api/donaciones',
        summary: 'Lista de donaciones (paginada)',
        tags: ['Donaciones'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista obtenida correctamente'),
        ]
    )]
    public function index()
    {
        return Donacion::select('id', 'donante_id', 'fecha_donacion', 'estado', 'observacion')
            ->withCount('detalles')
            ->orderByDesc('fecha_donacion')
            ->paginate(10);
    }

    #[OA\Get(
        path: '/api/donaciones/{id}',
        summary: 'Detalle de una donación',
        tags: ['Donaciones'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID de la donación', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Donación encontrada'),
            new OA\Response(response: 404, description: 'No existe'),
        ]
    )]
    public function show(int $id)
    {
        return Donacion::select('id', 'donante_id', 'fecha_donacion', 'estado', 'observacion')
            ->with('detalles')
            ->findOrFail($id);
    }
}
