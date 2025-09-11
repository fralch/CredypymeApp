<?php

namespace App\Http\Controllers\Creditos\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\General\PermisosController;
use App\Models\Creditos\Caja\PagoCuota;
use App\Models\General\Cargo;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Herramientas\ObjetivoCategoria;
use App\Models\Creditos\Herramientas\ObjetivoSubcategoria;
use App\Models\Creditos\Credito\Records\CreditoResumenRecord;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Illuminate\Support\Facades\DB;

class IndicadorObjetivoController extends Controller
{
    public function indicador_objetivo($modo)
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = 0;

            if ($modo == 'completo') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'INDICADOR_OBJETIVO', 'CREDITOS_HERRAMIENTAS');
            } else if ($modo == 'personal') {
                $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MI_INDICADOR_OBJETIVO', 'CREDITOS_HERRAMIENTAS');
            }


            if ($band == 1) {

                return Inertia::render('Creditos/Herramientas/indicador_objetivo', ['modo' => $modo]);
            } else {
                return redirect('/');
            }
        }
    }

    public function listar_recursos()
    {

        $cargos = Cargo::select('id')->whereIn('cargo', [
            'ASESOR DE NEGOCIOS',
            'JEFE DE CRÉDITOS',
            'COORDINADOR DE CRÉDITOS'
        ])->get();

        $usuarios_adicionales = Usuario::whereIn('usuario', ['esthefani_rm'])
            ->pluck('dni')
            ->toArray();

        $asesores = Usuario::select(
            'usuario',
            'dni',
            'agencia_id'
        )
            ->where([
                ['habilitado', 1],
                ['usuario_real', 1]
            ])
            ->whereIn('cargo_id', $cargos)
            ->orWhere('dni', session('usuario_dni'))
            ->orWhereIn('dni', $usuarios_adicionales)
            ->get();

        return ['asesores' => $asesores];
    }

    public function procesar(Request $request)
    {

        $usuario_id = $request->usuario_id;

        $usuario = Usuario::find($usuario_id);
        $cargo_id = $usuario->cargo_id;

        $cargos_asesores = Cargo::select('id')->whereIn('cargo', [
            'ASESOR DE NEGOCIOS'
        ])->pluck('id')->toArray();

        $cargos_jefes = Cargo::select('id')->whereIn('cargo', [
            'JEFE DE CRÉDITOS',
            'COORDINADOR DE CRÉDITOS',
            'JEFE DE OPERACIONES'
        ])->pluck('id')->toArray();

        $request->cargo_id = $cargo_id;

        if (in_array($cargo_id, $cargos_asesores)) {
            return $this->objetivo_asesor($request);
        } else if (in_array($cargo_id, $cargos_jefes)) {

            $request->cargos_asesores = $cargos_asesores;
            return $this->objetivo_jefe($request);
        }
    }

    public function objetivo_asesor($request)
    {
        $agencia_id = $request->agencia_id;
        $usuario_id = $request->usuario_id;
        $cargo_id = $request->cargo_id;

        $conexion_master = 'master_' . $agencia_id;
        $conexion_records = 'records_' . $agencia_id;
        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $fecha_inicio = date('Y-m-01', strtotime($fecha_actual));
        $fecha_hasta = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));

        $inicio_mes = CreditoResumenRecord::on($conexion_records)
            ->where([
                ['asesor_id', $usuario_id],
                ['fecha_cartera', $fecha_inicio]
            ])->get();

        $cantidad_creditos = $inicio_mes->max('cantidad_clientes_activos');

        // Identificar la CATEGORÍA de los Objetivos ----------------------

        $categorias = ObjetivoCategoria::on($conexion_master)
            ->where('cargos', 'like', '%' . $cargo_id . '%')->get();

        $categoria_actual = ObjetivoCategoria::on($conexion_master)->where([
            ['minimo', '<=', $cantidad_creditos],
            ['maximo', '>=', $cantidad_creditos]
        ])->get()->last();

        if ($categoria_actual != null) {
            // Identificar la SUBCATEGORÍA de los Objetivos ----------------------
            $categoria_id = $categoria_actual->id;


            $subcategoria_actual = ObjetivoSubcategoria::on($conexion_master)
                ->where('categoria_id', $categoria_id)
                ->get();


            $minimo = ObjetivoSubcategoria::on($conexion_master)->min('minimo');
            $maximo = ObjetivoSubcategoria::on($conexion_master)->max('maximo');

            $porcentaje_minimo = 0.10;
            $porcentaje_maximo = 0.95;

            $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($maximo - $minimo);

            foreach ($subcategoria_actual as $item) {
                $item->peso_minimo = round((0.10 + ($item->minimo - $minimo) * $intervalo) * 100, 2);
                $item->peso_maximo = round((0.10 + ($item->maximo - $minimo) * $intervalo) * 100, 2);
            };

            // Obteniendo PRODUCTIVIDAD actual ----------------------

            $interes_cobranzas = PagoCuota::on($conexion_master)
                ->where('asesor_id', $usuario_id)
                ->whereBetween('fecha_pago', [$fecha_inicio, $fecha_hasta])
                ->sum('interes_pagado');

            $interes_descuentos = Credito::on($conexion_master)
                ->where('asesor_id', $usuario_id)
                ->whereBetween('fecha_hora_cancelado', [$fecha_inicio, $fecha_hasta])
                ->sum('dscto_interes_cancelado');


            $productividad_actual = $interes_cobranzas - $interes_descuentos;

            if ($productividad_actual < $minimo) {
                $porcentaje_minimo = 0;
                $porcentaje_maximo = 0.10;

                $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($minimo - 0);
                $porcentaje_avance = round(($porcentaje_minimo + ($productividad_actual - 0) * $intervalo) * 100, 2);
            } else {
                $porcentaje_minimo = 0.10;
                $porcentaje_maximo = 0.95;

                $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($maximo - $minimo);
                $porcentaje_avance = round(($porcentaje_minimo + ($productividad_actual - $minimo) * $intervalo) * 100, 2);
            }

            // Obteniendo HISTORIAL DE PRODUCTIVIDAD -------

            $meses_historial = $request->meses_historial;
            $fecha = Carbon::parse($fecha_actual);
            $rango_fechas = [];

            for ($i = 1; $i <= intval($meses_historial); $i++) {
                // Obtener la fecha de inicio del mes anterior
                $fecha_inicio = $fecha->copy()->subMonths($i)->startOfMonth()->format('Y-m-d');
                $fecha_inicio;

                // Obtener la fecha de fin del mes anterior
                $fecha_fin = $fecha->copy()->subMonths($i)->endOfMonth()->format('Y-m-d');
                $fecha_fin = date("Y-m-d", strtotime($fecha_fin . "+ 1 days"));
                $fecha_fin;

                $rango = (object)[
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin
                ];
                $rango_fechas[] = $rango;
            }

            $historial = [];
            foreach ($rango_fechas as  $value) {

                $interes_cobranzas = PagoCuota::on($conexion_master)
                    ->where('asesor_id', $usuario_id)
                    ->whereBetween('fecha_pago', [$value->fecha_inicio, $value->fecha_fin])
                    ->sum('interes_pagado');

                $interes_descuentos = Credito::on($conexion_master)
                    ->where('asesor_id', $usuario_id)
                    ->whereBetween('fecha_hora_cancelado', [$value->fecha_inicio, $value->fecha_fin])
                    ->sum('dscto_interes_cancelado');

                $productividad_actual = $interes_cobranzas - $interes_descuentos;

                if ($productividad_actual < $minimo) {
                    $porcentaje_minimo = 0;
                    $porcentaje_maximo = 0.10;

                    $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($minimo - 0);
                    $avance_historico = round(($porcentaje_minimo + ($productividad_actual - 0) * $intervalo) * 100, 2);
                } else {
                    $porcentaje_minimo = 0.10;
                    $porcentaje_maximo = 0.95;

                    $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($maximo - $minimo);
                    $avance_historico = round(($porcentaje_minimo + ($productividad_actual - $minimo) * $intervalo) * 100, 2);
                }
                Carbon::setLocale('es');
                $fecha_texto = Carbon::parse($value->fecha_inicio)->copy()->isoFormat('MMMM YYYY');

                $resultado = (object)[
                    'fecha' => $fecha_texto,
                    'resultado' => $avance_historico
                ];

                $historial[] = $resultado;
            }
        } else {
            $subcategoria_actual = null;
            $interes_cobranzas = 0;
            $interes_descuentos = 0;
            $porcentaje_avance = 0;
            $historial = [];
        }

        return [
            'categorias' => $categorias,
            'categoria_actual' => $categoria_actual,
            'subcategoria_actual' => $subcategoria_actual,
            'interes_cobranzas' => $interes_cobranzas,
            'interes_descuentos' => $interes_descuentos,
            'porcentaje_avance' => $porcentaje_avance,
            'historial' => $historial
        ];
    }

    public function objetivo_jefe($request)
    {

        $agencia_id = $request->agencia_id;
        $usuario_id = $request->usuario_id;
        $cargo_id = $request->cargo_id;

        $conexion_master = 'master_' . $agencia_id;
        $conexion_records = 'records_' . $agencia_id;
        $fecha_actual = (new CreditosController)->fecha_corta_aplicacion($agencia_id);
        $fecha_inicio = date('Y-m-01', strtotime($fecha_actual));
        $fecha_hasta = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));

        $cargos = $request->cargos_asesores;

        $asesores = Usuario::select('dni')->whereIn('cargo_id', $cargos)->where(
            [
                ['agencia_id', $agencia_id],
                ['usuario_real', 1],
                ['habilitado', 1]
            ]
        )->pluck('dni')->toArray();

        $inicio_mes = CreditoResumenRecord::on($conexion_records)
            ->select(
                'asesor_id',
                DB::raw("MAX(cantidad_clientes_activos) as cantidad_clientes")
            )
            ->where([
                ['fecha_cartera', $fecha_inicio]
            ])
            ->whereIn('asesor_id', $asesores)
            ->groupBy('asesor_id')
            ->get();

        $cantidad_creditos = $inicio_mes->sum('cantidad_clientes');

        // Identificar la CATEGORÍA de los Objetivos ----------------------

        $categorias = ObjetivoCategoria::on($conexion_master)
            ->where('cargos', 'like', '%' . $cargo_id . '%')->get();


        $categoria_actual = ObjetivoCategoria::on($conexion_master)->where([
            ['minimo', '<=', $cantidad_creditos],
            ['maximo', '>=', $cantidad_creditos],
            ['cargos', 'like', '%' . $cargo_id . '%']
        ])->get()->last();

        if ($categoria_actual != null) {
            // Identificar la SUBCATEGORÍA de los Objetivos ----------------------
            $categoria_id = $categoria_actual->id;

            $subcategoria_actual = ObjetivoSubcategoria::on($conexion_master)
                ->where('categoria_id', $categoria_id)
                ->get();

            $categorias_id = ObjetivoCategoria::on($conexion_master)
                ->select('id')
                ->where('cargos', 'like', '%' . $cargo_id . '%')
                ->pluck('id')
                ->toArray();

            $minimo = ObjetivoSubcategoria::on($conexion_master)
                ->whereIn('categoria_id', $categorias_id)
                ->min('minimo');
            $maximo = ObjetivoSubcategoria::on($conexion_master)
                ->whereIn('categoria_id', $categorias_id)
                ->max('maximo');

            $porcentaje_minimo = 0.10;
            $porcentaje_maximo = 0.95;

            $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($maximo - $minimo);

            foreach ($subcategoria_actual as $item) {
                $item->peso_minimo = round((0.10 + ($item->minimo - $minimo) * $intervalo) * 100, 2);
                $item->peso_maximo = round((0.10 + ($item->maximo - $minimo) * $intervalo) * 100, 2);
            };

            // Obteniendo PRODUCTIVIDAD actual ----------------------

            $interes_cobranzas = PagoCuota::on($conexion_master)
                ->whereIn('asesor_id', $asesores)
                ->whereBetween('fecha_pago', [$fecha_inicio, $fecha_hasta])
                ->sum('interes_pagado');

            $interes_descuentos = Credito::on($conexion_master)
                ->whereIn('asesor_id', $asesores)
                ->whereBetween('fecha_hora_cancelado', [$fecha_inicio, $fecha_hasta])
                ->sum('dscto_interes_cancelado');


            $productividad_actual = $interes_cobranzas - $interes_descuentos;

            if ($productividad_actual < $minimo) {
                $porcentaje_minimo = 0;
                $porcentaje_maximo = 0.10;

                $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($minimo - 0);
                $porcentaje_avance = round(($porcentaje_minimo + ($productividad_actual - 0) * $intervalo) * 100, 2);
            } else {
                $porcentaje_minimo = 0.10;
                $porcentaje_maximo = 0.95;

                $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($maximo - $minimo);
                $porcentaje_avance = round(($porcentaje_minimo + ($productividad_actual - $minimo) * $intervalo) * 100, 2);
            }

            // Obteniendo HISTORIAL DE PRODUCTIVIDAD -------

            $meses_historial = $request->meses_historial;
            $fecha = Carbon::parse($fecha_actual);
            $rango_fechas = [];

            for ($i = 1; $i <= intval($meses_historial); $i++) {
                // Obtener la fecha de inicio del mes anterior
                $fecha_inicio = $fecha->copy()->subMonths($i)->startOfMonth()->format('Y-m-d');
                $fecha_inicio;

                // Obtener la fecha de fin del mes anterior
                $fecha_fin = $fecha->copy()->subMonths($i)->endOfMonth()->format('Y-m-d');
                $fecha_fin = date("Y-m-d", strtotime($fecha_fin . "+ 1 days"));
                $fecha_fin;

                $rango = (object)[
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin
                ];
                $rango_fechas[] = $rango;
            }

            $historial = [];

            foreach ($rango_fechas as  $value) {

                $interes_cobranzas_hist = PagoCuota::on($conexion_master)
                    ->whereIn('asesor_id', $asesores)
                    ->whereBetween('fecha_pago', [$value->fecha_inicio, $value->fecha_fin])
                    ->sum('interes_pagado');

                $interes_descuentos_hist = Credito::on($conexion_master)
                    ->whereIn('asesor_id', $asesores)
                    ->whereBetween('fecha_hora_cancelado', [$value->fecha_inicio, $value->fecha_fin])
                    ->sum('dscto_interes_cancelado');

                $productividad_actual = $interes_cobranzas_hist - $interes_descuentos_hist;

                if ($productividad_actual < $minimo) {
                    $porcentaje_minimo = 0;
                    $porcentaje_maximo = 0.10;

                    $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($minimo - 0);
                    $avance_historico = round(($porcentaje_minimo + ($productividad_actual - 0) * $intervalo) * 100, 2);
                } else {
                    $porcentaje_minimo = 0.10;
                    $porcentaje_maximo = 0.95;

                    $intervalo = ($porcentaje_maximo - $porcentaje_minimo) / ($maximo - $minimo);
                    $avance_historico = round(($porcentaje_minimo + ($productividad_actual - $minimo) * $intervalo) * 100, 2);
                }
                Carbon::setLocale('es');
                $fecha_texto = Carbon::parse($value->fecha_inicio)->copy()->isoFormat('MMMM YYYY');

                $resultado = (object)[
                    'fecha' => $fecha_texto,
                    'resultado' => $avance_historico
                ];

                $historial[] = $resultado;
            }
        } else {
            $subcategoria_actual = null;
            $interes_cobranzas = 0;
            $interes_descuentos = 0;
            $porcentaje_avance = 0;
            $historial = [];
        }


        // dd([
        //     'categorias' => $categorias,
        //     'categoria_actual' => $categoria_actual,
        //     'subcategoria_actual' => $subcategoria_actual,
        //     'interes_cobranzas' => $interes_cobranzas,
        //     'interes_descuentos' => $interes_descuentos,
        //     'porcentaje_avance' => $porcentaje_avance,
        //     'historial' => $historial
        // ]);

        return [
            'categorias' => $categorias,
            'categoria_actual' => $categoria_actual,
            'subcategoria_actual' => $subcategoria_actual,
            'interes_cobranzas' => $interes_cobranzas,
            'interes_descuentos' => $interes_descuentos,
            'porcentaje_avance' => $porcentaje_avance,
            'historial' => $historial
        ];
    }
}
