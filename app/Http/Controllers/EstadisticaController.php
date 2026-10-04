<?php

namespace App\Http\Controllers;

use App\Models\AyudaEntregada;
use App\Models\Beneficiario;
use App\Models\Donacion;
use App\Models\EntregaAlimento;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller
{
    /** RF15: estadísticas de beneficiarios, ayudas, donaciones y productos */
    public function index()
    {
        // Últimos 12 meses: ['2026-01' => 'ene. 26', ...]
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->startOfMonth()->subMonths($i);
            $meses[$m->format('Y-m')] = $m->locale('es')->isoFormat('MMM YY');
        }
        $desde = now()->startOfMonth()->subMonths(11)->toDateString();
        $serie = fn ($coleccion) => array_map(fn ($k) => (int) ($coleccion[$k] ?? 0), array_keys($meses));

        $benefMes = Beneficiario::where('created_at', '>=', $desde)
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as mes, COUNT(*) as total")->groupBy('mes')->pluck('total', 'mes');
        $ayudasMes = AyudaEntregada::where('fecha_entrega', '>=', $desde)
            ->selectRaw("DATE_FORMAT(fecha_entrega,'%Y-%m') as mes, COUNT(*) as total")->groupBy('mes')->pluck('total', 'mes');
        $racionesMes = EntregaAlimento::where('fecha', '>=', $desde)
            ->selectRaw("DATE_FORMAT(fecha,'%Y-%m') as mes, SUM(raciones) as total")->groupBy('mes')->pluck('total', 'mes');
        $donacionesMes = Donacion::where('estado', 'registrada')->where('fecha_donacion', '>=', $desde)
            ->selectRaw("DATE_FORMAT(fecha_donacion,'%Y-%m') as mes, COUNT(*) as total")->groupBy('mes')->pluck('total', 'mes');

        $ayudasPorTipo = DB::table('ayudas_entregadas')
            ->join('tipos_ayuda', 'tipos_ayuda.id', '=', 'ayudas_entregadas.tipo_ayuda_id')
            ->selectRaw('tipos_ayuda.nombre as nombre, COUNT(*) as total')
            ->groupBy('tipos_ayuda.id', 'tipos_ayuda.nombre')->orderByDesc('total')->get();

        $porSexo = Beneficiario::selectRaw('sexo, COUNT(*) as total')->groupBy('sexo')->get()
            ->map(fn ($f) => ['nombre' => match ($f->sexo) { 'M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro', default => 'Sin dato' }, 'total' => $f->total]);

        $topDonantes = DB::table('donaciones')
            ->join('donantes', 'donantes.id', '=', 'donaciones.donante_id')
            ->where('donaciones.estado', 'registrada')
            ->selectRaw('donantes.nombre_razon_social as nombre, COUNT(*) as total')
            ->groupBy('donantes.id', 'donantes.nombre_razon_social')->orderByDesc('total')->limit(5)->get();

        $masDonados = DB::table('detalle_donaciones')
            ->join('donaciones', 'donaciones.id', '=', 'detalle_donaciones.donacion_id')
            ->join('productos', 'productos.id', '=', 'detalle_donaciones.producto_id')
            ->where('donaciones.estado', 'registrada')
            ->selectRaw('productos.nombre as nombre, productos.unidad_medida as unidad, SUM(detalle_donaciones.cantidad) as total')
            ->groupBy('productos.id', 'productos.nombre', 'productos.unidad_medida')->orderByDesc('total')->limit(5)->get();

        $stockBajo = Producto::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')->limit(10)->get();

        $mesActual = now()->format('Y-m');

        return view('estadisticas.index', [
            'kpis' => [
                'benefActivos' => Beneficiario::where('estado', 'activo')->count(),
                'benefInactivos' => Beneficiario::where('estado', 'inactivo')->count(),
                'ayudasTotal' => AyudaEntregada::count(),
                'ayudasMes' => (int) ($ayudasMes[$mesActual] ?? 0),
                'racionesMes' => (int) ($racionesMes[$mesActual] ?? 0),
                'donacionesMes' => (int) ($donacionesMes[$mesActual] ?? 0),
                'productos' => Producto::where('activo', true)->count(),
                'stockBajo' => Producto::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
            ],
            'datos' => [
                'meses' => array_values($meses),
                'beneficiarios' => $serie($benefMes),
                'ayudas' => $serie($ayudasMes),
                'raciones' => $serie($racionesMes),
                'donaciones' => $serie($donacionesMes),
                'tipoNombres' => $ayudasPorTipo->pluck('nombre')->all(),
                'tipoTotales' => $ayudasPorTipo->pluck('total')->map(fn ($v) => (int) $v)->all(),
                'sexoNombres' => $porSexo->pluck('nombre')->all(),
                'sexoTotales' => $porSexo->pluck('total')->map(fn ($v) => (int) $v)->all(),
            ],
            'topDonantes' => $topDonantes,
            'masDonados' => $masDonados,
            'stockBajo' => $stockBajo,
        ]);
    }
}
