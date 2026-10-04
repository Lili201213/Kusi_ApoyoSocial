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

        // Cada bloque solo se carga si el rol tiene permiso de verlo
        $puede = [
            'benef' => $u->tienePermiso('beneficiarios.ver'),
            'hist' => $u->tienePermiso('historial.ver'),
            'don' => $u->tienePermiso('donaciones.ver'),
            'inv' => $u->tienePermiso('inventario.ver'),
        ];

        $kpi = [];
        $necesidades = collect();
        $stockBajo = collect();
        $ultAyudas = collect();
        $ultDonaciones = collect();
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
            $ultAyudas = AyudaEntregada::with(['beneficiario', 'tipoAyuda'])
                ->orderByDesc('fecha_entrega')->orderByDesc('id')->limit(6)->get();

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
            $ultDonaciones = Donacion::with('donante')->withCount('detalles')
                ->orderByDesc('fecha_donacion')->orderByDesc('id')->limit(5)->get();
        }

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
            'necesidades' => $necesidades,
            'stockBajo' => $stockBajo,
            'ultAyudas' => $ultAyudas,
            'ultDonaciones' => $ultDonaciones,
            'serie' => $serie,
        ]);
    }
}
