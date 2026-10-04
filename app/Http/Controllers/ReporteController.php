<?php

namespace App\Http\Controllers;

use App\Models\AyudaEntregada;
use App\Models\Beneficiario;
use App\Models\CategoriaProducto;
use App\Models\Donacion;
use App\Models\EntregaAlimento;
use App\Models\Producto;
use App\Models\TipoAyuda;
use App\Services\InventarioService;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    private const TIPOS = ['beneficiarios', 'ayudas', 'alimentos', 'donaciones', 'inventario'];
    private const MAX_FILAS = 5000;

    public function index()
    {
        return view('reportes.index');
    }

    /** RF14: reporte en pantalla (imprimible) */
    public function ver(Request $request, string $tipo)
    {
        abort_unless(in_array($tipo, self::TIPOS, true), 404);

        return view('reportes.ver', $this->generar($tipo, $request) + [
            'tipo' => $tipo,
            'tiposAyuda' => TipoAyuda::orderBy('nombre')->get(),
            'categorias' => CategoriaProducto::orderBy('nombre')->get(),
            'tiposEntrega' => EntregaAlimentoController::TIPOS,
        ]);
    }

    /** RF14: exportar a Excel (CSV con UTF-8) */
    public function exportar(Request $request, string $tipo)
    {
        abort_unless(in_array($tipo, self::TIPOS, true), 404);

        $r = $this->generar($tipo, $request);
        $nombre = "reporte_{$tipo}_".now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($r) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // para que Excel respete tildes y ñ
            fputcsv($out, $r['columnas']);
            foreach ($r['filas'] as $fila) {
                fputcsv($out, array_map([$this, 'seguro'], $fila));
            }
            fclose($out);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Evita que Excel interprete un texto como fórmula (=, +, -, @) */
    private function seguro($valor)
    {
        if (is_string($valor) && $valor !== '' && in_array($valor[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$valor;
        }

        return $valor;
    }

    private function generar(string $tipo, Request $r): array
    {
        return match ($tipo) {
            'beneficiarios' => $this->beneficiarios($r),
            'ayudas' => $this->ayudas($r),
            'alimentos' => $this->alimentos($r),
            'donaciones' => $this->donaciones($r),
            'inventario' => $this->inventario($r),
        };
    }

    private function buscarBeneficiario($query, string $q)
    {
        return $query->where(function ($w) use ($q) {
            $w->where('dni', 'like', "%{$q}%")->orWhere('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
        });
    }

    private function beneficiarios(Request $r): array
    {
        $datos = Beneficiario::query()
            ->when($r->filled('q'), fn ($x) => $this->buscarBeneficiario($x, trim($r->q)))
            ->when(in_array($r->estado, ['activo', 'inactivo']), fn ($x) => $x->where('estado', $r->estado))
            ->when(in_array($r->sexo, ['M', 'F', 'Otro']), fn ($x) => $x->where('sexo', $r->sexo))
            ->when($r->filled('desde'), fn ($x) => $x->whereDate('created_at', '>=', $r->desde))
            ->when($r->filled('hasta'), fn ($x) => $x->whereDate('created_at', '<=', $r->hasta))
            ->orderBy('apellidos')->orderBy('nombres')
            ->limit(self::MAX_FILAS)->get();

        return [
            'titulo' => 'Reporte de beneficiarios',
            'campos' => ['q', 'estado', 'sexo', 'desde', 'hasta'],
            'columnas' => ['DNI', 'Apellidos y nombres', 'Sexo', 'Edad', 'Teléfono', 'Familiares', 'Estado', 'Registrado'],
            'filas' => $datos->map(fn ($b) => [
                $b->dni, $b->apellidos.', '.$b->nombres, $b->sexo ?: '—',
                $b->fecha_nacimiento ? $b->fecha_nacimiento->age : '—',
                $b->telefono ?: '—', $b->num_familiares, ucfirst($b->estado), $b->created_at->format('d/m/Y'),
            ])->all(),
            'resumen' => [
                'Total' => $datos->count(),
                'Activos' => $datos->where('estado', 'activo')->count(),
                'Inactivos' => $datos->where('estado', 'inactivo')->count(),
            ],
            'truncado' => $datos->count() >= self::MAX_FILAS,
        ];
    }

    private function ayudas(Request $r): array
    {
        $datos = AyudaEntregada::with(['beneficiario', 'tipoAyuda', 'usuario'])
            ->when($r->filled('q'), fn ($x) => $x->whereHas('beneficiario', fn ($b) => $this->buscarBeneficiario($b, trim($r->q))))
            ->when($r->filled('tipo_ayuda_id'), fn ($x) => $x->where('tipo_ayuda_id', $r->tipo_ayuda_id))
            ->when($r->filled('desde'), fn ($x) => $x->whereDate('fecha_entrega', '>=', $r->desde))
            ->when($r->filled('hasta'), fn ($x) => $x->whereDate('fecha_entrega', '<=', $r->hasta))
            ->orderByDesc('fecha_entrega')->orderByDesc('id')
            ->limit(self::MAX_FILAS)->get();

        return [
            'titulo' => 'Reporte de ayudas entregadas',
            'campos' => ['q', 'tipo_ayuda', 'desde', 'hasta'],
            'columnas' => ['Fecha', 'DNI', 'Beneficiario', 'Tipo de ayuda', 'Descripción', 'Registró'],
            'filas' => $datos->map(fn ($a) => [
                $a->fecha_entrega->format('d/m/Y'), $a->beneficiario->dni,
                $a->beneficiario->apellidos.', '.$a->beneficiario->nombres,
                $a->tipoAyuda->nombre, $a->descripcion ?: '—', $a->usuario->name,
            ])->all(),
            'resumen' => [
                'Ayudas entregadas' => $datos->count(),
                'Beneficiarios atendidos' => $datos->pluck('beneficiario_id')->unique()->count(),
            ],
            'truncado' => $datos->count() >= self::MAX_FILAS,
        ];
    }

    private function alimentos(Request $r): array
    {
        $datos = EntregaAlimento::with(['beneficiario', 'usuario'])
            ->when($r->filled('q'), fn ($x) => $x->whereHas('beneficiario', fn ($b) => $this->buscarBeneficiario($b, trim($r->q))))
            ->when(in_array($r->tipo_entrega, EntregaAlimentoController::TIPOS), fn ($x) => $x->where('tipo_entrega', $r->tipo_entrega))
            ->when($r->filled('desde'), fn ($x) => $x->whereDate('fecha', '>=', $r->desde))
            ->when($r->filled('hasta'), fn ($x) => $x->whereDate('fecha', '<=', $r->hasta))
            ->orderByDesc('fecha')->orderByDesc('id')
            ->limit(self::MAX_FILAS)->get();

        return [
            'titulo' => 'Reporte de alimentos y almuerzos',
            'campos' => ['q', 'tipo_entrega', 'desde', 'hasta'],
            'columnas' => ['Fecha', 'DNI', 'Beneficiario', 'Tipo', 'Raciones', 'Registró'],
            'filas' => $datos->map(fn ($e) => [
                $e->fecha->format('d/m/Y'), $e->beneficiario->dni,
                $e->beneficiario->apellidos.', '.$e->beneficiario->nombres,
                ucfirst($e->tipo_entrega), $e->raciones, $e->usuario->name,
            ])->all(),
            'resumen' => [
                'Entregas' => $datos->count(),
                'Raciones' => (int) $datos->sum('raciones'),
                'Beneficiarios atendidos' => $datos->pluck('beneficiario_id')->unique()->count(),
            ],
            'truncado' => $datos->count() >= self::MAX_FILAS,
        ];
    }

    private function donaciones(Request $r): array
    {
        $datos = Donacion::with(['donante', 'usuario', 'detalles.producto'])
            ->when($r->filled('q'), fn ($x) => $x->whereHas('donante', fn ($d) => $d->where('nombre_razon_social', 'like', '%'.trim($r->q).'%')))
            ->when(in_array($r->estado, ['registrada', 'anulada']), fn ($x) => $x->where('estado', $r->estado))
            ->when($r->filled('desde'), fn ($x) => $x->whereDate('fecha_donacion', '>=', $r->desde))
            ->when($r->filled('hasta'), fn ($x) => $x->whereDate('fecha_donacion', '<=', $r->hasta))
            ->orderByDesc('fecha_donacion')->orderByDesc('id')
            ->limit(self::MAX_FILAS)->get();

        return [
            'titulo' => 'Reporte de donaciones',
            'campos' => ['q_donante', 'estado_don', 'desde', 'hasta'],
            'columnas' => ['N.º', 'Fecha', 'Donante', 'Productos donados', 'Estado', 'Registró'],
            'filas' => $datos->map(fn ($d) => [
                '#'.$d->id, $d->fecha_donacion->format('d/m/Y'), $d->donante->nombre_razon_social,
                $d->detalles->map(fn ($x) => $x->producto->nombre.' ('.InventarioService::fmt($x->cantidad).' '.$x->producto->unidad_medida.')')->implode('; '),
                $d->estaAnulada() ? 'Anulada' : 'Registrada', $d->usuario->name,
            ])->all(),
            'resumen' => [
                'Registradas' => $datos->where('estado', 'registrada')->count(),
                'Anuladas' => $datos->where('estado', 'anulada')->count(),
            ],
            'truncado' => $datos->count() >= self::MAX_FILAS,
        ];
    }

    private function inventario(Request $r): array
    {
        $vista = (string) $r->input('stock', '');

        $datos = Producto::with('categoria')
            ->when($r->filled('categoria_id'), fn ($x) => $x->where('categoria_id', $r->categoria_id))
            ->when(in_array($vista, ['', 'bajo']), fn ($x) => $x->where('activo', true))
            ->when($vista === 'bajo', fn ($x) => $x->whereColumn('stock_actual', '<=', 'stock_minimo'))
            ->when($vista === 'inactivos', fn ($x) => $x->where('activo', false))
            ->orderBy('nombre')->limit(self::MAX_FILAS)->get();

        return [
            'titulo' => 'Reporte de inventario',
            'campos' => ['categoria', 'stock'],
            'columnas' => ['Producto', 'Categoría', 'Unidad', 'Stock actual', 'Stock mínimo', 'Situación', 'Estado'],
            'filas' => $datos->map(fn ($p) => [
                $p->nombre, $p->categoria->nombre ?? '—', $p->unidad_medida,
                InventarioService::fmt($p->stock_actual), InventarioService::fmt($p->stock_minimo),
                $p->stock_bajo ? 'Stock bajo' : 'Normal', $p->activo ? 'Activo' : 'Inactivo',
            ])->all(),
            'resumen' => [
                'Productos' => $datos->count(),
                'Con stock bajo' => $datos->filter(fn ($p) => $p->stock_bajo)->count(),
            ],
            'truncado' => false,
        ];
    }
}
