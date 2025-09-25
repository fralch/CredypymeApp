<?php

namespace App\Http\Controllers\Creditos\Herramientas;

use App\Http\Controllers\Controller;

use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Apis\ApiWhatsAppController;

use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use Illuminate\Http\Request;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MensajeriaController extends Controller
{
    public function mensajeria()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MENSAJERIA', 'CREDITOS_HERRAMIENTAS');
            if ($band == 1) {

                return Inertia('Creditos/Herramientas/mensajeria');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar_creditos($agencia_id)
    {
        $conexion = 'master_' . $agencia_id;

        $estados = Estado::on($conexion)
            ->select('id')
            ->whereIn('estado', ['DESEMBOLSADO'])
            ->get();

        $lista_creditos = Credito::on($conexion)
            ->select(
                DB::raw('YEAR(fecha_desembolso) as año'),
                DB::raw('MONTH(fecha_desembolso) as mes'),
                DB::raw('DAY(fecha_desembolso) as dia'),
                DB::raw('COUNT(id) as cantidad_creditos'),
                DB::raw('COUNT(DISTINCT(cliente_id)) as cantidad_clientes'),
                DB::raw('0 as enviado')

            )
            ->whereIn('estado_id', $estados)
            ->where(DB::raw('YEAR(fecha_desembolso)'), '2024')
            ->whereIn(DB::raw('MONTH(fecha_desembolso)'), [9, 10, 11])
            ->groupBy(
                DB::raw('YEAR(fecha_desembolso)'),
                DB::raw('MONTH(fecha_desembolso)'),
                DB::raw('DAY(fecha_desembolso)')
            )
            ->orderBy('mes', 'asc')
            ->orderBy('dia', 'asc')
            ->get();

        return [
            'lista_creditos' => $lista_creditos
        ];



        // $cantidad_creditos = $creditos->count();

        // $clientes = $creditos->distinct('cliente_id')->pluck('cliente_id');

        // $cantidad_clientes = $clientes->count();

        // return [
        //     'cantidad_creditos' => $cantidad_creditos,
        //     'cantidad_clientes' => $cantidad_clientes
        // ];
    }

    public function enviar(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $año = $request->año;
        $mes = $request->mes;
        $dia = $request->dia;

        $conexion = 'master_' . $request->agencia_id;

        $estados = Estado::on($conexion)
            ->select('id')
            ->whereIn('estado', ['DESEMBOLSADO'])->get();

        $clientes = Credito::on($conexion)
            ->whereIn('estado_id', $estados)
            ->where(DB::raw('YEAR(fecha_desembolso)'), $año)
            ->where(DB::raw('MONTH(fecha_desembolso)'), $mes)
            ->where(DB::raw('DAY(fecha_desembolso)'), $dia)
            ->distinct('cliente_id')
            ->pluck('cliente_id');

        $datos_clientes = Cliente::on($conexion)->whereIn('id', $clientes)->get();

        $telefonos_enviar = [];

        $mensaje = '*Estimado cliente:*' . "\n" .
            'Te saludamos 👋🏻 desde *CREDIPYME HUANCA* para informarte que nuestras cuentas en *Interbank* ya se encuentran operativas ✅ para recibir tus pagos. ¡Agradecemos tu preferencia! 😃';

        foreach ($datos_clientes as $item) {
            // Enviar mensaje a cliente aquí...

            $numero_principal = json_decode($item->telefonos);

            $numero_principal = $numero_principal->t1;

            // Eliminar espacios en blanco
            $numero_principal = str_replace(' ', '', $numero_principal);

            // Verificar que tenga 9 caracteres
            $cantidad_caracteres = strlen($numero_principal);

            if ($cantidad_caracteres == 9) {
                // Verificar que todos los carácteres sean números
                $es_numero = ctype_digit($numero_principal);

                if ($es_numero) {

                    // Verificar que el primer digito sea el número 9
                    if (substr($numero_principal, 0, 1) === '9') {

                        // ENVIAR MENSAJE

                        // $numero_principal = '955547121';
                        $respuesta = (new ApiWhatsAppController)->text_send($numero_principal, $mensaje);

                        // dd($respuesta);
                    }
                }
            }
        }

        return true;
    }
}
