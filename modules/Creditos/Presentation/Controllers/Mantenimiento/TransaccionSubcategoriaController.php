<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;


use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Subcategoria;

use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TransaccionSubcategoriaController extends Controller
{
    //
    public function subcategorias()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSACCION_SUBCATEGORIAS', 'CREDITOS_MANTENIMIENTO'); // no hacer merge
            // $band = 1; // No hacer merge de esto

            if ($band == 1) {
                $subcategorias = Subcategoria::on($conexion)->select(
                    'transaccion_subcategorias.id',
                    'transaccion_subcategorias.subcategoria',
                    'transaccion_subcategorias.descripcion',
                    'transaccion_subcategorias.habilitado',
                    'transaccion_subcategorias.datos_creacion',
                    'transaccion_subcategorias.datos_actualizacion',
                    'transaccion_categorias.categoria as categoria',
                    'transaccion_categorias.id as id_categoria'
                )
                    ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_subcategorias.categoria_id')
                    ->get();
                $categorias = Categoria::on($conexion)->where('habilitado', 1)->get();

                return Inertia::render('Creditos/Mantenimiento/Transacciones-Subcategorias', [
                    'subcategorias' => $subcategorias,
                    'categorias' => $categorias,
                ]);
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }

    public function validarExiste(Request $request)
    {
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $validar_duplicados =  Subcategoria::on($conexion)->where('subcategoria', $request->subcategoria)->where('categoria_id', $request->categoria)->get();

        if (count($validar_duplicados)) {
            return 'EXISTE';
        } else {
            return 'NO_EXISTE';
        }
    }

    public function listarSubcategorias(Request $request)
    {
        // return $request;
        $id_agencia = $request->id_agencia;
        $conexion = 'master_' .  $id_agencia;
        $subcategorias = Subcategoria::on($conexion)->select(
            'transaccion_subcategorias.id',
            'transaccion_subcategorias.subcategoria',
            'transaccion_subcategorias.descripcion',
            'transaccion_subcategorias.habilitado',
            'transaccion_subcategorias.datos_creacion',
            'transaccion_subcategorias.datos_actualizacion',
            'transaccion_categorias.categoria as categoria',
            'transaccion_categorias.id as id_categoria'
        )
            ->join('transaccion_categorias', 'transaccion_categorias.id', 'transaccion_subcategorias.categoria_id')
            ->get();
        return $subcategorias;
    }
    public function guardarSubcategoria(Request $request)
    {
        // return $request;
        $id_agencia = $request->agencia_seleccionada;
        $conexion = 'master_' .  $id_agencia;

        $datos_registro = (new CreditosController)->datos_registro(session('id_agencia'));

        if ($request->modo == 1) {
            Subcategoria::on($conexion)->create([
                'subcategoria' => strtoupper($request->subcategoria),
                'categoria_id' => $request->categoria,
                'descripcion' => strtoupper($request->descripcion),
                'habilitado' => 1,
                'datos_creacion' => $datos_registro
            ]);
        } elseif ($request->modo == 0) {

            Subcategoria::on($conexion)->where('id', $request->id)
                ->update([
                    'subcategoria' => strtoupper($request->subcategoria),
                    'descripcion' => strtoupper($request->descripcion),
                    'categoria_id' => $request->categoria,
                    'habilitado' => $request->habilitado,
                    'datos_actualizacion' => $datos_registro
                ]);
        }

        return redirect()->route('mant.transaccion_subcategoria');
    }
}
