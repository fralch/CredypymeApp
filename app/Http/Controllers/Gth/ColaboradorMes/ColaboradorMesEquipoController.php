<?php

namespace App\Http\Controllers\Gth\ColaboradorMes;

use App\Http\Controllers\Gth\GthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Models\Gth\ColaboradorMes\Equipo;
use App\Models\Gth\ColaboradorMes\EquipoIntegrante;
use App\Models\Gth\Usuarios\Usuario;
use App\Models\General\Agencia;
use App\Models\General\Cargo;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ColaboradorMesEquipoController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function equipos()
    {

        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {
            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'EQUIPOS', 'GTH_COLABORADOR_MES');
            if ($band == 1) {

                return Inertia('Gth/ColaboradorMes/equipos');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    // --------------------------------------------------------------------------

    // --------------------FUNCIONES QUE NO DEVUELVEN VISTAS --------------------

    public function listar_recursos()
    {
        $equipos = Equipo::from('colaboradormes_equipos as cm_eq')
            ->select(
                'cm_eq.id',
                'cm_eq.equipo',
                'cm_eq.responsable_id',
                'cm_eq.habilitado',
            )
            ->get();

        $usuarios = Usuario::from('usuarios as us')
            ->select(
                'us.dni',
                'us.usuario',
                'us.cargo_id',
                'us.agencia_id',
                'ca.cargo',
                'us.nombres',
                'us.apellido_paterno',
                'us.apellido_materno',
                'ag.nombre as nombre_agencia'
            )
            ->join('cargos as ca', 'us.cargo_id',  'ca.id')
            ->join('agencias as ag', 'us.agencia_id',  'ag.id_agencia')
            ->where('us.habilitado', 1)
            ->where('us.usuario_real', 1)
            ->orderBy('nombre_agencia', 'asc')
            ->orderBy('usuario', 'asc')->get();

        return [
            'equipos' => $equipos,
            'usuarios' => $usuarios
        ];
    }

    public function verificar(Request $request)
    {
        $modo = $request->modo;
        $nombre_equipo = $request->nombre_equipo;
        $resultado = 'EXISTE';
        if ($modo == 'NUEVO') {
            $existe = Equipo::select('id')->where('equipo', $nombre_equipo)->get();

            if (count($existe) > 0) {
                $resultado = 'EXISTE';
            } else {
                $resultado = 'NO EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            $id_equipo = $request->id_equipo;
            $existe = Equipo::select('id')->where('equipo', $nombre_equipo)->get();

            if (count($existe) > 0) {
                if ($existe[0]['id'] == $id_equipo) {
                    $resultado = 'NO EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            } else {
                $resultado = 'NO EXISTE';
            }
        }

        return $resultado;
    }

    public function guardar(Request $request)
    {

        $response = new \stdClass();

        $datos_registro = (new GthController)->datos_registro();
        $modo = $request->modo;
        $nombre = mb_strtoupper($request->nombre);
        $responsables = json_encode($request->responsable);

        if ($modo == 'NUEVO') {
            $equipo =  Equipo::create(array(
                'equipo' => $nombre,
                'responsable_id' => $responsables,
                'habilitado' => 1,
                'datos_creacion' => $datos_registro
            ));

            $id = $equipo->id;
        } else if ($modo == 'EDITAR') {
            $id = $request->id;
            $habilitado = $request->habilitado;
            Equipo::where('id', $id)->update([
                'equipo' => $nombre,
                'responsable_id' => $responsables,
                'habilitado' => $habilitado,
                'datos_actualizacion' => $datos_registro

            ]);
        }

        $integrantes = $request->integrantes;

        EquipoIntegrante::where('equipo_id', $id)->delete();

        foreach ($integrantes as $integrante) {
            EquipoIntegrante::create(array(
                'equipo_id' => $id,
                'usuario_id' => $integrante['dni'],
                'datos_creacion' => $datos_registro,
                'datos_actualizacion' => $datos_registro
            ));
        }

        $response->success = true;
        return $response;
    }

    public function listar_integrantes($equipo_id)
    {
        $integrantes = EquipoIntegrante::from('colaboradormes_integrantes as cm_eqi')
            ->select(
                'cm_eqi.id',
                'cm_eqi.equipo_id',
                'cm_eqi.usuario_id',
                'cm_eq.equipo',
                'cm_eq.responsable_id',

                'us_2.dni as dni',
                'us_2.usuario as usuario',
                'us_2.nombres as nombres',
                'us_2.apellido_paterno as apellido_paterno',
                'us_2.apellido_materno as apellido_materno',

                'ag_2.nombre as nombre_agencia',
                'ca_2.cargo as cargo',
            )
            ->join('colaboradormes_equipos as cm_eq', 'cm_eq.id', '=', 'cm_eqi.equipo_id')
            ->join('usuarios as us_2', 'cm_eqi.usuario_id', '=', 'us_2.dni')
            ->join('agencias as ag_2', 'us_2.agencia_id', '=', 'ag_2.id_agencia')
            ->join('cargos as ca_2', 'us_2.cargo_id', '=', 'ca_2.id')
            ->where('cm_eqi.equipo_id', $equipo_id)
            ->get();

        return $integrantes;
    }



    // --------------------------------------------------------------------------

    // -----------------------------------APIS-----------------------------------

    // --------------------------------------------------------------------------
}
