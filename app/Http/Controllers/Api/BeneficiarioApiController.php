<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiario;
use OpenApi\Attributes as OA;

class BeneficiarioApiController extends Controller
{
    private array $campos = ['id', 'nombres', 'apellidos', 'sexo', 'num_familiares', 'situacion', 'estado'];

    #[OA\Get(
        path: '/api/beneficiarios',
        summary: 'Lista de beneficiarios (paginada)',
        tags: ['Beneficiarios'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista obtenida correctamente'),
        ]
    )]
    public function index()
    {
        return Beneficiario::select($this->campos)->orderBy('id')->paginate(10);
    }

    #[OA\Get(
        path: '/api/beneficiarios/{id}',
        summary: 'Detalle de un beneficiario',
        tags: ['Beneficiarios'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del beneficiario', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Beneficiario encontrado'),
            new OA\Response(response: 404, description: 'No existe'),
        ]
    )]
    public function show(int $id)
    {
        return Beneficiario::select($this->campos)->findOrFail($id);
    }
}
