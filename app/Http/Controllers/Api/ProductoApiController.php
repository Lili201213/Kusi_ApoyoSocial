<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use OpenApi\Attributes as OA;

class ProductoApiController extends Controller
{
    #[OA\Get(
        path: '/api/productos',
        summary: 'Inventario de productos (paginado)',
        tags: ['Inventario'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista obtenida correctamente'),
        ]
    )]
    public function index()
    {
        return Producto::with('categoria')->orderBy('nombre')->paginate(10);
    }

    #[OA\Get(
        path: '/api/productos/{id}',
        summary: 'Detalle de un producto',
        tags: ['Inventario'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del producto', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto encontrado'),
            new OA\Response(response: 404, description: 'No existe'),
        ]
    )]
    public function show(int $id)
    {
        return Producto::with('categoria')->findOrFail($id);
    }
}
