<?php

namespace App\Http\Controllers\Creditos\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Http\Controllers\Creditos\CreditosController;

use App\Models\Creditos\Clientes\Negocio;
use App\Models\General\Ciiu;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class NegocioController extends Controller
{
    public function listar_negocios($cliente_id, $agencia_id)
    {
        $conexion = 'master_' .  $agencia_id;
        return Negocio::on($conexion)->from('cliente_negocios as cli_neg')
            ->select(
                'cli_neg.id',
                'cli_neg.cliente_id',
                'cli_neg.nombre',
                'cli_neg.actividad',
                'cli_neg.ciiu_id',
                'cli_neg.direccion',
                'cli_neg.departamento_id',
                'dep.departamento',
                'cli_neg.provincia_id',
                'pro.provincia',
                'cli_neg.distrito_id',
                'dis.distrito',
                'cli_neg.referencia_direccion',
                'cli_neg.telefonos',
                'cli_neg.vinculado',
                'cli_neg.datos_creacion',
                DB::raw("CONCAT(ciiu.codigo,' - ',ciiu.nombre) as descripcion_ciiu")
            )
            ->join('solucion_master.departamentos as dep', 'dep.id', 'cli_neg.departamento_id')
            ->join('solucion_master.provincias as pro', 'pro.id', 'cli_neg.provincia_id')
            ->join('solucion_master.distritos as dis', 'dis.id', 'cli_neg.distrito_id')
            ->join('solucion_master.ciiu', 'ciiu.id', 'cli_neg.ciiu_id')
            ->where('cli_neg.cliente_id', $cliente_id)
            ->orderby('cli_neg.nombre', 'asc')
            ->get();
    }
    public function buscar_ciiu(Request $request)
    {
        $nombre_ciiu = $request->nombre_ciiu;

        return Ciiu::select(
            'id',
            'codigo',
            'nombre'
        )
            ->where([['nombre', 'like', "%$nombre_ciiu%"], ['habilitado', 1]])
            ->get();
    }
    public function asignar_negocio(Request $request)
    {

        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $frmNegocio = json_decode($request->frmNegocio);
        $modo = $frmNegocio->modo;
        $cliente_id = $frmNegocio->cliente_id;
        $nombre = mb_strtoupper($frmNegocio->nombre);
        $actividad = mb_strtoupper($frmNegocio->actividad);
        $ciiu_id = $frmNegocio->ciiu_id;
        $direccion = mb_strtoupper($frmNegocio->direccion);
        $departamento_id = $frmNegocio->departamento_id;
        $provincia_id = $frmNegocio->provincia_id;
        $distrito_id = $frmNegocio->distrito_id;
        $referencia_direccion = mb_strtoupper($frmNegocio->referencia_direccion);
        $telefonos = json_encode($frmNegocio->telefonos);

        if ($modo == 'NUEVO') {
            Negocio::on($conexion)->where('cliente_id', $cliente_id)
                ->update([
                    'vinculado' => 0,
                    'datos_actualizacion' => $datos_registro
                ]);
            Negocio::on($conexion)->create([
                'cliente_id' => $cliente_id,
                'nombre' => $nombre,
                'actividad' => $actividad,
                'ciiu_id' => $ciiu_id,
                'direccion' => $direccion,
                'departamento_id' => $departamento_id,
                'provincia_id' => $provincia_id,
                'distrito_id' => $distrito_id,
                'referencia_direccion' => $referencia_direccion,
                'telefonos' => $telefonos,
                'vinculado' => 1,
                'datos_creacion' => $datos_registro
            ]);
        } else if ($modo == 'EDITAR') {
            $id =  $frmNegocio->id;
            Negocio::on($conexion)->where('id', $id)->update([
                'nombre' => $nombre,
                'actividad' => $actividad,
                'ciiu_id' => $ciiu_id,
                'direccion' => $direccion,
                'departamento_id' => $departamento_id,
                'provincia_id' => $provincia_id,
                'distrito_id' => $distrito_id,
                'referencia_direccion' => $referencia_direccion,
                'telefonos' => $telefonos,
                'datos_actualizacion' => $datos_registro
            ]);
        }

        return redirect()->route('cli.listado_registro');
    }

    public function vincular_negocio(Request $request)
    {
        $agencia_id = $request->agencia_id;
        $conexion = 'master_' .  $agencia_id;

        $datos_registro = (new CreditosController)->datos_registro($agencia_id);
        $negocio_activo = json_decode($request->negocio_activo);
        $id = $negocio_activo->id;
        $cliente_id = $negocio_activo->cliente_id;

        Negocio::on($conexion)->where('cliente_id', $cliente_id)->update([
            'vinculado' => 0,
            'datos_actualizacion' => $datos_registro
        ]);

        Negocio::on($conexion)->where('id', $id)->update([
            'vinculado' => 1,
            'datos_actualizacion' => $datos_registro
        ]);

        return redirect()->route('cli.listado_registro');
    }
}
