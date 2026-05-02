<?php

namespace Modules\General\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\General\Presentation\Controllers\PermisosController;
use Modules\General\Presentation\Controllers\GeneralController;


use Modules\General\Infrastructure\Persistence\Eloquent\Banco;

class BancoController extends Controller
{
    public function index()
    {
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = (new PermisosController)
                ->verificarPermiso($x['usuario_dni'], 'BANCOS', 'GENERAL_MANTENIMIENTO');
            if ($band == 1) {

                return Inertia('General/Mantenimiento/bancos');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function listar()
    {
        try {
            $lista_bancos = Banco::from('bancos as ban')
                ->select(
                    'ban.id',
                    'ban.agencia_id',
                    'ban.banco',
                    'ban.titular',
                    'ban.numero',
                    'ban.cci',
                    'ban.detalle',
                    'ban.habilitado',

                    'age.nombre as agencia'
                )
                ->join('agencias as age', 'ban.agencia_id', 'age.id_agencia')
                ->orderBy('agencia', 'asc')
                ->get();

            return response()->json(['lista_bancos' => $lista_bancos], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los bancos: ' . $e->getMessage()
            ], 500);
        }
    }
    public function guardar(Request $request)
    {
        $datos_registro = (new GeneralController)->fecha_larga();

        $modo = $request->input('modo');

        $agencia_id = $request->input('agencia_id');
        $banco = mb_strtoupper(trim($request->input('banco')));
        $titular = mb_strtoupper(trim($request->input('titular')));
        $numero = trim($request->input('numero'));
        $cci = (new GeneralController)->verificar_nulo(trim($request->input('cci')));
        $detalle = (new GeneralController)->verificar_nulo(trim($request->input('detalle')));

        if ($modo === 'NUEVO') {

            $id = Banco::create([
                'agencia_id' => $agencia_id,
                'banco' => $banco,
                'titular' => $titular,
                'numero' => $numero,
                'cci' => $cci,
                'detalle' => $detalle,
                'datos_creacion' => $datos_registro
            ]);

            return response()->json(['message' => 'BANCO creado correctamente', 'id' => $id->id]);
        } else if ($modo === 'EDITAR') {

            $id = $request->input('id');

            $registro = Banco::find($id);
            $registro->agencia_id = $agencia_id;
            $registro->banco = $banco;
            $registro->titular = $titular;
            $registro->numero = $numero;
            $registro->cci = $cci;
            $registro->detalle = $detalle;
            $registro->datos_actualizacion = $datos_registro;

            $registro->save();

            return response()->json(['message' => 'Datos actualizados correctamente', 'id' => $id]);
        }
    }
    public function alternar($id)
    {
        $id = $id;

        $datos_registro = (new GeneralController)->datos_registro();

        $banco = Banco::find($id);
        $banco->habilitado = !$banco->habilitado;
        $banco->datos_actualizacion = $datos_registro;
        $banco->save();

        return response()->json(['message' => 'Cambio realizado correctamente', 'id' => $id]);
    }
}
