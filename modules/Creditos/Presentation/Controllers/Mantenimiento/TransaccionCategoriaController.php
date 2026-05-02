<?php

namespace Modules\Creditos\Presentation\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;


use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\CuentaUsuario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones\Categoria;

use Modules\General\Infrastructure\Persistence\Eloquent\Datos_aplicacion;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TransaccionCategoriaController extends Controller
{
    // --------------------FUNCIONES QUE DEVUELVEN UNA VISTA---------------------
    public function categorias()
    {
        $x = session()->all();
        $conexion = 'master_' .  $x['id_agencia'];

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSACCION_CATEGORIAS', 'CREDITOS_MANTENIMIENTO');
            if ($band == 1) {

                return Inertia::render('Creditos/Mantenimiento/transaccion_categorias');
            } else {
                $mensajeTitulo = '¡Ups!';
                $mensajeContenido = 'No tienes permitido ver este contenido.';
                return view('cuatrocientoscuatro')->with('mensajeTitulo', $mensajeTitulo)->with('mensajeContenido', $mensajeContenido);
                die();
            }
        }
    }
    public function listar(Request $request)
    {
        $agencia_id = $request->input('agencia_id');
        $conexion = 'master_' .  $agencia_id;

        $categorias = Categoria::on($conexion)
            ->whereIn('tipo', ['I', 'E'])
            ->orderBy('categoria', 'asc')
            ->get();

        return response()->json(['categorias' => $categorias]);
    }
    public function verificar(Request $request)
    {
        $agencia_id = $request->input('agencia_seleccionada');
        $conexion = 'master_' .  $agencia_id;

        $modo = $request->input('modo');
        $categoria_id = $request->input('id');
        $categoria = $request->input('categoria');
        $tipo = $request->input('tipo');

        $validar_duplicados =  Categoria::on($conexion)
            ->where([
                ['categoria', $categoria],
                ['tipo', $tipo]
            ])
            ->get();

        $resultado = null;

        if ($modo == 'NUEVO') {
            if (count($validar_duplicados)) {
                $resultado = 'EXISTE';
            } else {
                $resultado = 'NO_EXISTE';
            }
        } else if ($modo == 'EDITAR') {
            if (count($validar_duplicados)) {
                if ($validar_duplicados[0]->id == $categoria_id) {
                    $resultado = 'NO_EXISTE';
                } else {
                    $resultado = 'EXISTE';
                }
            } else {
                $resultado = 'NO_EXISTE';
            }
        }

        return $resultado;
    }
    public function guardar(Request $request)
    {
        $frmDatosCategoria = json_decode($request->frmDatosCategoria);

        $agencia_id = $frmDatosCategoria->agencia_seleccionada;
        $conexion = 'master_' .  $agencia_id;
        $datos_registro = (new CreditosController)->datos_registro($agencia_id);

        $modo = $frmDatosCategoria->modo;
        $categoria = mb_strtoupper(trim($frmDatosCategoria->categoria));
        $descripcion = (new CreditosController)->verificar_nulo($frmDatosCategoria->descripcion);

        if ($descripcion != null) {
            $descripcion = mb_strtoupper(trim($descripcion));
        }

        $tipo = $frmDatosCategoria->tipo;
        $habilitado = $frmDatosCategoria->habilitado;

        if ($modo == 'NUEVO') {
            Categoria::on($conexion)->create([
                'categoria' => $categoria,
                'descripcion' => $descripcion,
                'tipo' => $tipo,
                'datos_creacion' => $datos_registro
            ]);
        } elseif ($modo == 'EDITAR') {
            $categoria_id = $frmDatosCategoria->id;

            $datos_categoria = Categoria::on($conexion)->find($categoria_id);
            $datos_categoria->categoria = $categoria;
            $datos_categoria->descripcion = $descripcion;
            $datos_categoria->tipo = $tipo;
            $datos_categoria->habilitado = $habilitado;
            $datos_categoria->datos_actualizacion = $datos_registro;

            $datos_categoria->save();
        }

        return response()->json([
            'message' => 'Registro realizado correctamente'
        ]);
    }
}
