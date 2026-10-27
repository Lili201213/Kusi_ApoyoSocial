<?php

namespace App\Http\Controllers;

use App\Models\AyudaEntregada;
use App\Models\Beneficiario;
use App\Models\Donacion;
use App\Models\EntregaAlimento;
use App\Models\NecesidadBeneficiario;
use App\Models\Producto;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $hoy = now();
        $iniMes = $hoy->copy()->startOfMonth()->toDateString();
        $finMes = $hoy->copy()->endOfMonth()->toDateString();

        // Mismo tramo del mes anterior (del 1 al día de hoy) para comparar de forma justa
        $iniAntC = $hoy->copy()->subMonthNoOverflow()->startOfMonth();
        $finAntC = $iniAntC->copy()->addDays($hoy->day - 1)->min($iniAntC->copy()->endOfMonth());
        $iniAnt = $iniAntC->toDateString();
        $finAnt = $finAntC->toDateString();

        // Cada bloque solo se carga si el rol tiene permiso de verlo
        $puede = [
            'benef' => $u->tienePermiso('beneficiarios.ver'),
            'hist' => $u->tienePermiso('historial.ver'),
            'don' => $u->tienePermiso('donaciones.ver'),
            'inv' => $u->tienePermiso('inventario.ver'),
        ];

        $kpi = [];
        $prev = []; // valores del mes anterior para comparar
        $necesidades = collect();
        $stockBajo = collect();
        $actividad = collect();
        $serie = null;

        if ($puede['benef']) {
            $kpi['benef'] = Beneficiario::where('estado', 'activo')->count();
            $kpi['pendientes'] = NecesidadBeneficiario::where('estado', 'pendiente')->count();
            $necesidades = NecesidadBeneficiario::with(['beneficiario', 'tipoAyuda'])
                ->where('estado', 'pendiente')->orderBy('fecha_registro')->limit(5)->get();
        }

        if ($puede['hist']) {
            $kpi['ayudasMes'] = AyudaEntregada::whereBetween('fecha_entrega', [$iniMes, $finMes])->count();
            $kpi['racionesMes'] = (int) EntregaAlimento::whereBetween('fecha', [$iniMes, $finMes])->sum('raciones');
            $prev['ayudasMes'] = AyudaEntregada::whereBetween('fecha_entrega', [$iniAnt, $finAnt])->count();
            $prev['racionesMes'] = (int) EntregaAlimento::whereBetween('fecha', [$iniAnt, $finAnt])->sum('raciones');

            $ultAyudas = AyudaEntregada::with(['beneficiario', 'tipoAyuda'])
                ->orderByDesc('fecha_entrega')->orderByDesc('id')->limit(6)->get();

            foreach ($ultAyudas as $a) {
                $actividad->push([
                    'tipo' => 'ayuda',
                    'titulo' => $a->beneficiario->nombres.' '.$a->beneficiario->apellidos,
                    'detalle' => 'Recibió: '.$a->tipoAyuda->nombre,
                    'fecha' => $a->fecha_entrega,
                    'enlace' => route('beneficiarios.show', $a->beneficiario_id),
                ]);
            }

            // Ayudas por mes (últimos 6 meses)
            $meses = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->startOfMonth()->subMonths($i);
                $meses[$m->format('Y-m')] = ucfirst($m->locale('es')->isoFormat('MMM'));
            }
            $porMes = AyudaEntregada::where('fecha_entrega', '>=', now()->startOfMonth()->subMonths(5)->toDateString())
                ->selectRaw("DATE_FORMAT(fecha_entrega,'%Y-%m') as mes, COUNT(*) as total")
                ->groupBy('mes')->pluck('total', 'mes');
            $serie = [
                'etiquetas' => array_values($meses),
                'valores' => array_map(fn ($k) => (int) ($porMes[$k] ?? 0), array_keys($meses)),
            ];
        }

        if ($puede['don']) {
            $kpi['donMes'] = Donacion::where('estado', 'registrada')->whereBetween('fecha_donacion', [$iniMes, $finMes])->count();
            $prev['donMes'] = Donacion::where('estado', 'registrada')->whereBetween('fecha_donacion', [$iniAnt, $finAnt])->count();

            $ultDonaciones = Donacion::with('donante')->withCount('detalles')
                ->orderByDesc('fecha_donacion')->orderByDesc('id')->limit(6)->get();

            foreach ($ultDonaciones as $d) {
                $actividad->push([
                    'tipo' => 'donacion',
                    'titulo' => $d->donante->nombre_razon_social,
                    'detalle' => 'Donó '.$d->detalles_count.' producto(s)'.($d->estaAnulada() ? ' · Anulada' : ''),
                    'fecha' => $d->fecha_donacion,
                    'enlace' => route('donaciones.show', $d),
                ]);
            }
        }

        // Mezcla ayudas y donaciones: lo más reciente primero
        $actividad = $actividad->sortByDesc('fecha')->take(6)->values();

        if ($puede['inv']) {
            $kpi['productos'] = Producto::where('activo', true)->count();
            $bajo = Producto::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo');
            $kpi['stockBajo'] = (clone $bajo)->count();
            $stockBajo = $bajo->orderBy('stock_actual')->limit(5)->get();
        }

        return view('dashboard', [
            'saludo' => $hoy->hour < 12 ? 'Buenos días' : ($hoy->hour < 19 ? 'Buenas tardes' : 'Buenas noches'),
            'fecha' => ucfirst($hoy->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')),
            'primerNombre' => explode(' ', trim($u->name))[0],
            'puede' => $puede,
            'kpi' => $kpi,
            'prev' => $prev,
            'necesidades' => $necesidades,
            'stockBajo' => $stockBajo,
            'actividad' => $actividad,
            'serie' => $serie,
        ]);
    }
}
