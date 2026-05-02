<?php

namespace Modules\General\Presentation\Controllers;

use Modules\General\Infrastructure\Persistence\Eloquent\Usuarios_permiso;
use Modules\General\Infrastructure\Persistence\Eloquent\Permiso;
use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\GeneralController;
use Modules\General\Infrastructure\Persistence\Eloquent\Feriado;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MantenimientosController extends Controller
{

    // --------------------FUNCIÓN PARA VERIFICAR PERMISO-----------------------
    public function verificarPermiso($dni, $modulo, $area)
    {

        $band = 0;

        $id_permiso = Permiso::select('id')->where('modulo', $modulo)->where('area', $area)->get();

        $result = Usuarios_permiso::select('id')->where('usuarios_permisos.permiso_id', $id_permiso[0]['id'])->where('usuarios_permisos.usuario_id', $dni)->get();

        if (empty($result[0]['id'])) {
            $band = 0;
        } else {
            $band = 1;
        }

        return $band;
    }
    // --------------------------------------------------------------------------


    //----------------------------------FUNCIONES QUE DEVUELVEN UNA VISTA----------------------
    public function listar_feriados()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = $this->verificarPermiso($x['usuario_dni'], 'FERIADOS', 'GENERAL_MANTENIMIENTO');

            if ($band == 1) {
                $motivos = Feriado::select('motivo')->distinct('id', 'motivo')->orderBy('motivo', 'asc')->get();
                $feriados = Feriado::select('id', 'fecha', 'motivo', 'agencias')->orderby('fecha', 'asc')->get();
                $agencias = Agencia::select('id_agencia', 'nombre')->orderBy('nombre', 'asc')->get();


                $acceso_agencias = [];

                foreach ($feriados as $feriadoactual) {
                    $jsonaccesoagencias = json_decode($feriadoactual->agencias, true);
                    if (!is_null($jsonaccesoagencias)) {
                        foreach ($jsonaccesoagencias as $agencia) {
                            $acceso_agencias[] = ['feriado_id' => $feriadoactual->id, 'agencia_id' => $agencia['agencia_id']];
                        }
                    }
                }

                foreach ($agencias as $agencia) {
                    if ($agencia->nombre == 'EL TAMBO') {
                        $agencia->nombre = 'TAMBO';
                    }
                }

                return Inertia::render('General/Mantenimiento/feriados', [
                    'feriados' => $feriados, 'motivos' => $motivos, 'agencias' => $agencias, 'acceso_agencias' => $acceso_agencias,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    //----------------------------------FUNCIONES QUE NO DEVUELVEN VISTAS-----------------------
    public function guardar_feriado(Request $request)
    {
        // dd($request);
        $modo = $request->modo;
        $datos_registro = (new GeneralController)->datos_registro();
        $motivo = mb_strtoupper($request->motivo);
        $fecha = $request->fecha;
        $habilitado = $request->habilitado;
        $acceso_agencias = [];
        if ($modo == 'NUEVO') {
            Feriado::create(array(
                'fecha' => $fecha,
                'motivo' => $motivo,
                'agencias' => json_encode($acceso_agencias),
                'datos_creacion' => $datos_registro,
            ));
        } else if ($modo == 'EDITAR') {

            $id_feriado = $request->id;
            Feriado::where('id', $id_feriado)
                ->update([
                    'fecha' => $fecha,
                    'motivo' => $motivo,
                    'agencias' => json_encode($acceso_agencias),
                    'datos_actualizacion' => $datos_registro,
                ]);
        }

        return redirect()->route('gen.man.listar_feriados');
    }

    public function eliminar_feriado(Request $request)
    {
        $feriado_id = $request->id;
        Feriado::where('id', $feriado_id)->delete();

        return redirect()->back();
    }

    public function verificar_feriado(Request $request)
    {
        //  dd($request);
        $motivo = $request->motivo;
        $fecha = $request->fecha;
        $id_feriado = $request->id;
        $existe_feriado = Feriado::select('id', 'fecha')->where([['fecha', $fecha]])
            ->get();


        if ($id_feriado == 0) {
            if (count($existe_feriado) == 0) {
                $resultado = "NO EXISTE;                                                                           ISTE";
            } else {
                $resultado = "EXISTE";
            }
        } else {
            if (count($existe_feriado) == 0) {
                $resultado = 'NO EXISTE';
            } else if ($existe_feriado[0]->id == $id_feriado) {
                $resultado = 'NO EXISTE';
            } else {
                $resultado = 'EXISTE';
            }
        }

        // dd($resultado);
        return $resultado;
    }

    public function editar_permiso_agencia(Request $request)
    {
        $acceso_agencias = array();
        $lista_agencias_editar = $request->lista_agencias_editar;
        $id_feriado = $request->id;
        $datos_registro = (new GeneralController)->datos_registro();

        foreach ($lista_agencias_editar as $agencia) {
            $acceso_agencias[]['agencia_id'] = $agencia;
            json_encode($acceso_agencias);
        }


        Feriado::where('id', $id_feriado)
            ->update([
                'agencias' =>  $acceso_agencias,
                'datos_actualizacion' => $datos_registro,
            ]);
        return redirect()->back();
    }
}
