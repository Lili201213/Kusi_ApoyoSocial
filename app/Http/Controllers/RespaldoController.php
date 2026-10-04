<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RespaldoController extends Controller
{
    /** Tablas técnicas que no hace falta respaldar */
    private const OMITIR = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

    public function index()
    {
        $tablas = collect($this->tablas())->map(fn ($t) => ['nombre' => $t, 'filas' => DB::table($t)->count()]);

        return view('respaldos.index', [
            'tablas' => $tablas,
            'baseDatos' => DB::connection()->getDatabaseName(),
        ]);
    }

    /** RNF10: genera y descarga un archivo .sql con toda la información (se restaura desde phpMyAdmin) */
    public function descargar()
    {
        $bd = DB::connection()->getDatabaseName();
        $nombre = 'respaldo_kusi_'.now()->format('Ymd_His').'.sql';

        return response()->streamDownload(function () use ($bd) {
            $pdo = DB::connection()->getPdo();

            echo "-- ===============================================\n";
            echo "-- Respaldo de KUSI - Base de datos: {$bd}\n";
            echo '-- Generado: '.now()->format('d/m/Y H:i:s')."\n";
            echo "-- ===============================================\n\n";
            echo "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($this->tablas() as $tabla) {
                $crear = (array) DB::selectOne("SHOW CREATE TABLE `{$tabla}`");

                echo "-- Tabla: {$tabla}\n";
                echo "DROP TABLE IF EXISTS `{$tabla}`;\n";
                echo $crear['Create Table'].";\n\n";

                $columnas = null;
                $lote = [];

                foreach (DB::table($tabla)->cursor() as $fila) {
                    $fila = (array) $fila;
                    $columnas ??= '`'.implode('`,`', array_keys($fila)).'`';
                    $lote[] = '('.implode(',', array_map(
                        fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v),
                        $fila
                    )).')';

                    if (count($lote) >= 200) {
                        echo "INSERT INTO `{$tabla}` ({$columnas}) VALUES\n".implode(",\n", $lote).";\n";
                        $lote = [];
                    }
                }

                if ($lote) {
                    echo "INSERT INTO `{$tabla}` ({$columnas}) VALUES\n".implode(",\n", $lote).";\n";
                }
                echo "\n";
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, $nombre, ['Content-Type' => 'application/sql; charset=UTF-8']);
    }

    private function tablas(): array
    {
        return collect(DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'"))
            ->map(fn ($r) => array_values((array) $r)[0])
            ->reject(fn ($t) => in_array($t, self::OMITIR, true))
            ->values()
            ->all();
    }
}
