<?php

namespace Modules\General\Presentation\Controllers;

use App\Http\Controllers\Controller;

use Modules\General\Infrastructure\Persistence\Eloquent\Permiso;
use Modules\General\Infrastructure\Persistence\Eloquent\Usuarios_permiso;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargos_permiso;

use Illuminate\Http\Request;

use Inertia\Inertia;

class PermisosCargosController extends Controller
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

    public function permisos_cargos()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return view('welcome');
        } else {
            $band = $this->verificarPermiso($x['usuario_dni'], 'PERMISOS_CARGOS', 'GENERAL_PERMISOS');
            if ($band == 1) {
                $cargos = Cargo::select(
                    'id',
                    'cargo',
                )
                    ->orderBy('cargo', 'asc')
                    ->get();

                $agencias = Agencia::select(
                    'id_agencia',
                    'nombre as nombre_agencia',
                )
                    ->orderBy('nombre', 'asc')
                    ->get();
                $permisos = Permiso::select(
                    'id',
                    'area',
                    'modulo'
                )
                    ->orderBy('area', 'asc')
                    ->get();
                return Inertia::render(
                    'General/Permisos/permisos_cargos',
                    [
                        'cargos' => $cargos,
                        'agencias' => $agencias,
                        'permisos' => $permisos,
                    ]
                );
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function editar_permiso(Request $request)
    {
        $cargo_id = $request->id;
        $agencias = Agencia::select(
            'id_agencia',
            'nombre',
        )
            ->orderBy('nombre', 'asc')
            ->get();

        $cargos = Cargo::select(
            'id',
            'cargo',
        )
            ->where('id', $cargo_id)
            ->get();

        $permisosActuales = Permiso::from('permisos AS p')
            ->select(
                'p.modulo',
                'p.area',
                'cp.id',
                'cp.cargo_id',
                'cp.permiso_id',
                'cp.acceso_agencias',
                'c.cargo'
            )
            ->join('cargos_permisos AS cp', 'cp.permiso_id', '=', 'p.id')
            ->join('cargos AS c', 'cp.cargo_id', '=', 'c.id')
            ->where('cp.cargo_id', $cargo_id)
            ->orderBy('area', 'asc')
            ->orderBy('modulo', 'asc')
            ->get();

        $permisosDisponibles = Permiso::from('permisos AS p')
            ->select(
                'p.id',
                'p.modulo',
                'p.area'
            )
            ->whereNotIn('id', Cargos_permiso::from('cargos_permisos as cp')
                ->select('cp.permiso_id')
                ->where('cp.cargo_id', $cargo_id))
            ->get();

        $areasDispos = Permiso::from('permisos AS p')
            ->select('area')->distinct('id', 'area')
            ->whereNotIn('id', Cargos_permiso::from('cargos_permisos as cp')
                ->select('cp.permiso_id')
                ->where('cp.cargo_id', $cargo_id))
            ->get();

        $areasActuales = Permiso::from('permisos AS p')
            ->select('area')->distinct('id', 'area')
            ->join('cargos_permisos as cp', 'cp.permiso_id', '=', 'p.id')
            ->join('cargos AS c', 'c.id', '=', 'cp.cargo_id')
            ->where('cp.cargo_id', $cargo_id)
            ->orderBy('area', 'asc')->get();
        
        $acceso_agencias = []; 
        
        foreach($permisosActuales as $permisoactual){
            $jsonaccesoagencias = json_decode($permisoactual->acceso_agencias,true); 
            if(!is_null($jsonaccesoagencias)){
                foreach($jsonaccesoagencias as $agencia){
                    $acceso_agencias[]=['permiso_id'=>$permisoactual->permiso_id,'agencia_id'=>$agencia['agencia_id']];
                }
            }
        }

        foreach ($agencias as $agencia){
            if($agencia->nombre == 'EL TAMBO'){
                $agencia->nombre = 'TAMBO';
            }
        }

        return Inertia::render(
            'General/Permisos/permisos_cargos_editar',
            [
                'cargos' => $cargos,
                'agencias' => $agencias,
                'permisosActuales' => $permisosActuales,
                'permisosDisponibles' => $permisosDisponibles,
                'areasDispos' => $areasDispos,
                'areasActuales' => $areasActuales,
                'acceso_agencias' =>  $acceso_agencias,
            ]
        );
    }

    public function getArrayFiltered($aFilterKey, $aFilterValue, $array,$keyarray) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (isset($value[$aFilterKey])) {
                if ($value[$aFilterKey] == $aFilterValue) {
                    $filtered_array[][$keyarray] = $value[$keyarray];
                }
            }
        }
        return $filtered_array;
    }

    public function asignar_permiso(Request $request)
    {
        $id_cargo = $request->id_cargo;
        $permiso = new PermisosCargosController();
        $permisosagencias = $request->permisosAgencia;
        $permisosNuevos = $request->permisoSeleccionados;
        $datos_registro = (new GeneralController)->datos_registro();
        foreach ($permisosNuevos as $id_permiso) {
            // dd($id_permiso);
            $acceso_agencias = $permiso->getArrayFiltered('permiso_id',$id_permiso,$permisosagencias,'agencia_id');
            Cargos_permiso::create(
                array(
                    'cargo_id' => $id_cargo,
                    'permiso_id' => $id_permiso,
                    'acceso_agencias' =>json_encode($acceso_agencias),
                    'datos_creacion' => $datos_registro,
                )
            );
        }
        return redirect()->back();
    }

    public function editar_permiso_agencia(Request $request){
        $acceso_agencias = array();
        $lista_agencias_editar = $request->lista_agencias_editar;
        $id = $request->id;
        $datos_registro = (new GeneralController)->datos_registro();

        foreach ($lista_agencias_editar as $agencia){
            $acceso_agencias[]['agencia_id'] = $agencia;
            json_encode($acceso_agencias);
        }
        
        
        Cargos_permiso::where('id', $id)
        ->update([
            'acceso_agencias' =>  $acceso_agencias,
            'datos_actualizacion' => $datos_registro,
        ]);
        return redirect()->back();
    }

    public function eliminar_permiso(Request $request)
    {
        $id = $request->id;
        Cargos_permiso::where('id', $id)->delete();
        return redirect()->back();
    }
}
