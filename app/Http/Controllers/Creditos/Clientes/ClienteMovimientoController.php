<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\General\Cargo;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\Creditos\Clientes\Cliente;
use App\Models\Creditos\Clientes\Movimiento;
use App\Models\Creditos\Credito\Credito;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ClienteMovimientoController extends Controller
{

    public function movimiento()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'MOVIMIENTO', 'CREDITOS_CLIENTES');

            if ($band == 1) {

                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                $usuarios = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )->whereIn('cargo_id', $cargos)
                    ->orderBy('usuario', 'asc')
                    ->get();

                return Inertia::render('Creditos/Clientes/movimiento', [
                    'usuarios' => $usuarios,

                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar_clientes(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $asesor_id = $request->asesor_id;

        $lista_clientes = Cliente::on($conexion)
            ->select(
                'id',
                'dni',
                'apellido_paterno',
                'apellido_materno',
                'nombres'
            )
            ->where('asesor_id', $asesor_id)
            ->orderBy('apellido_paterno', 'asc')
            ->get();

        return ['lista_clientes' => $lista_clientes];
    }
    public function mover(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $asesor_origen = $request->asesor_origen;
        $asesor_destino = $request->asesor_destino;
        $escoger_clientes = filter_var($request->escoger_clientes, FILTER_VALIDATE_BOOLEAN);

        $estado = Estado::on($conexion)->select('id')->where('estado', 'DESEMBOLSADO')->get()->last();
        $estado_id = $estado->id;

        if ($escoger_clientes) {
            $clientes_seleccionados = json_decode($request->clientes_seleccionados);
            foreach ($clientes_seleccionados as  $item) {

                $cliente_id = $item->cliente_id;
                $dni = $item->dni;
                $cliente = $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres;

                Cliente::on($conexion)->where('id', $cliente_id)
                    ->update([
                        'asesor_id' =>  $asesor_destino,
                        'promotor_id' =>  $asesor_destino,
                        'datos_actualizacion' => $datos_registro
                    ]);

                Credito::on($conexion)->where([
                    ['cliente_id', $cliente_id],
                    ['estado_id', $estado_id]
                ])
                    ->update([
                        'asesor_id' =>  $asesor_destino,
                        'cobrador_id' =>  $asesor_destino,
                        'datos_actualizacion' => $datos_registro
                    ]);

                Movimiento::on($conexion)->create([
                    'dni' => $dni,
                    'cliente' => $cliente,
                    'asesor_origen' => $asesor_origen,
                    'asesor_destino' => $asesor_destino,
                    'datos_creacion' => $datos_registro
                ]);
            }
        } else {

            $clientes = Cliente::on($conexion)->select(
                'dni',
                'apellido_paterno',
                'apellido_materno',
                'nombres'
            )->where('asesor_id', $asesor_origen)->get();

            Cliente::on($conexion)->where('asesor_id', $asesor_origen)
                ->update([
                    'asesor_id' =>  $asesor_destino,
                    'datos_actualizacion' => $datos_registro
                ]);

            Credito::on($conexion)->where([
                ['asesor_id', $asesor_origen],
                ['estado_id', $estado_id]
            ])
                ->update([
                    'asesor_id' =>  $asesor_destino,
                    'cobrador_id' =>  $asesor_destino,
                    'datos_actualizacion' => $datos_registro
                ]);


            foreach ($clientes as $item) {
                $dni = $item->dni;
                $cliente = $item->apellido_paterno . ' ' . $item->apellido_materno . ' ' . $item->nombres;

                Movimiento::on($conexion)->create([
                    'dni' => $dni,
                    'cliente' => $cliente,
                    'asesor_origen' => $asesor_origen,
                    'asesor_destino' => $asesor_destino,
                    'datos_creacion' => $datos_registro
                ]);
            }
        }

        return redirect()->route('cli.movimiento');
    }
}
