<?php

namespace Modules\Gth\Presentation\Controllers\Apis;


use Modules\Gth\Presentation\Controllers\GthController;
use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Licencia;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\Permiso;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias\SolicitudFecha;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\usuario;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Mantenimiento\Solicitudes\LicenciaCategoria;
use Modules\General\Infrastructure\Persistence\Eloquent\Feriado;



use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ApiSolicitudController extends Controller
{


    public function listar_aprobadores($dni)
    {

        $usuario_agencia = Usuario::select('agencia_id')->where('dni', $dni)->get()->last();
        $usuario_agencia = $usuario_agencia->agencia_id;

            $usuario_aprobadores = Usuario::from('usuarios as us')->select(
                'usuario','dni'
                )
                ->join('cargos as car','us.cargo_id','car.id')
                ->where([
                    ['us.agencia_id', '=', $usuario_agencia],
                    ['us.habilitado', '=', 1],
                    ['car.jefatura', '=', 1],
                    ['us.dni', '!=', $dni],
                ])
                ->get();



                $usuario_aprobadores_fijos = Usuario::from('usuarios as us')->select(
                    'usuario','dni'
                    )
                    ->join('cargos as car','us.cargo_id','car.id')
                    ->whereIn('car.cargo', ['GERENTE GENERAL', 'GERENTE DE CRÉDITOS', 'COORDINADOR DE OPERACIONES'])
                    ->where('us.habilitado', 1)
                    ->get();

                    $usuario_aprobadores = $usuario_aprobadores->merge($usuario_aprobadores_fijos);

                    $usuario_aprobadores = $usuario_aprobadores->sortBy('usuario')->values();

                    // dd($usuario_aprobadores_fijos, $usuario_aprobadores);


            return $usuario_aprobadores;


    }
    public function listar_categorias()
    {
        $categorias = LicenciaCategoria::where('habilitado',1)->orderBy('abreviacion', 'asc')->get();

        // dd($categorias);
        return ['categorias' => $categorias];
    }

    public function listar_feriados()
    {
        $año_actual = date("Y");

        $feriados = Feriado::select('fecha')->whereYear('fecha', $año_actual )->pluck('fecha')->toArray();


        // dd($categorias);
        return ['feriados' => $feriados];
    }

    public function listar_usuarios()
    {

        $usuarios = Usuario::select('usuario','dni')->where('habilitado', 1 )->where('usuario_real', 1 )->orderby('usuario','asc')->get();


        // dd($categorias);
        return ['usuarios' => $usuarios];
    }

    public function guardar(Request $request){

        date_default_timezone_set("America/Lima");



        $tipo = $request->tipo;

        $fecha_solicitud = date("Y-m-d H:i:s");
        $usuario = $request->usuario;

        $injustificado = $request->injustificado;


            if ($tipo == "LICENCIA") {


                $categoria_id = $request->categoria_id;
                $fecha_inicio = $request->fecha_inicio;
                $fecha_fin = $request->fecha_fin;
                $dias = $request->dias;
                $detalle = strtoupper($request->detalle);
                $aprobador_id = $request->aprobador_id;

                if($injustificado==0){
                    $licencia = Licencia::create(array(
                        'usuario_id' => $usuario,
                        'injustificado' => $injustificado,
                        'categoria_id' => $categoria_id,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin,
                        'dias' => $dias,
                        'detalle' => $detalle,
                        'aprobador_id' => $aprobador_id,
                    ));

                    $id = $licencia->id;

                    SolicitudFecha::create(array(
                        'licencia_id' => $id,
                        'fecha_solicitud' => $fecha_solicitud,
                    ));


                } else if($injustificado==1){
                    $usuario_gth = $request->usuario_gth;
                    $licencia = Licencia::create(array(
                        'usuario_id' => $usuario,
                        'injustificado' => $injustificado,
                        'categoria_id' => $categoria_id,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin,
                        'fecha_retorno' => $fecha_fin,
                        'dias' => $dias,
                        'detalle' => $detalle,
                        'comentario' => 'injustificado',
                        'observacion' => 'injustificado',

                        'aprobador_id' => $usuario_gth,
                        'goce' => 0,
                        'validador_id' => $usuario_gth,
                        'estado' => 'U',
                    ));

                    $id = $licencia->id;
                    SolicitudFecha::create(array(
                        'licencia_id' => $id,
                        'fecha_solicitud' => $fecha_solicitud,
                        'fecha_eliminacion' => $fecha_solicitud,
                        'fecha_aprobacion' => $fecha_solicitud,
                        'fecha_validacion' => $fecha_solicitud,
                        'fecha_verificacion' => $fecha_solicitud,
                        'usuario_ver' => $usuario_gth,

                    ));

                }


                if (!empty($_FILES['documento'])) {

                    $path_name = pathinfo($_FILES['documento']['name']);
                    $extension = "." . $path_name['extension'];
                    $nombre_documento = "sol_licencia_" . $id . $extension;
                    $archivo = $_FILES['documento']['tmp_name'];
                    $ruta = '/imagenes_server/gth/solicitud/licencias';
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
                    move_uploaded_file($archivo, $ruta);

                    Licencia::where('id', $id)
                        ->update([
                            'documento' => $nombre_documento
                        ]);
                }


                return response()->json([
                    'status' => 'success',
                    'data' => $licencia
                ], 201);

            }
            if ($tipo == "PERMISO") {


                $modo = $request->modo;
                $hora_inicio = $request->hora_inicio;
                $hora_fin = $request->hora_fin;
                $tiempo = $request->tiempo;
                $detalle = strtoupper($request->detalle);
                $aprobador_id = $request->aprobador_id;
                $fecha_permiso = $request->fecha_permiso;

                if($injustificado==0){

                  $permiso =  Permiso::create(array(
                        'fecha_permiso' => $fecha_permiso,
                        'usuario_id' => $usuario,
                        'injustificado' => $injustificado,
                        'modo' => $modo,
                        'hora_inicio' => $hora_inicio,
                        'hora_fin' => $hora_fin,
                        'tiempo' => $tiempo,
                        'detalle' => $detalle,
                        'aprobador_id' => $aprobador_id,

                    ));
                    $id = $permiso->id;

                    SolicitudFecha::create(array(
                        'permiso_id' => $id,
                        'fecha_solicitud' => $fecha_solicitud,
                    ));
                }else if($injustificado==1){
                    $usuario_gth = $request->usuario_gth;
                    $permiso =  Permiso::create(array(
                        'fecha_permiso' => $fecha_permiso,
                        'usuario_id' => $usuario,
                        'injustificado' => $injustificado,
                        'modo' => $modo,
                        'hora_inicio' => $hora_inicio,
                        'hora_fin' => $hora_fin,
                        'hora_retorno' => $hora_fin,
                        'tiempo' => $tiempo,
                        'detalle' => $detalle,
                        'comentario' => 'injustificado',
                        'observacion' => 'injustificado',

                        'aprobador_id' => $usuario_gth,
                        'goce' => 0,
                        'validador_id' => $usuario_gth,
                        'estado' => 'U',

                    ));
                    $id = $permiso->id;

                    SolicitudFecha::create(array(
                        'permiso_id' => $id,
                        'fecha_solicitud' => $fecha_solicitud,
                        'fecha_eliminacion' => $fecha_solicitud,
                        'fecha_aprobacion' => $fecha_solicitud,
                        'fecha_validacion' => $fecha_solicitud,
                        'fecha_verificacion' => $fecha_solicitud,
                        'usuario_ver' => $usuario_gth,
                    ));

                }




                if (!empty($_FILES['documento'])) {

                    $path_name = pathinfo($_FILES['documento']['name']);
                    $extension = "." . $path_name['extension'];
                    $nombre_documento = "sol_permiso_" . $id . $extension;
                    $archivo = $_FILES['documento']['tmp_name'];
                    $ruta = '/imagenes_server/gth/solicitud/permisos';
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
                    move_uploaded_file($archivo, $ruta);

                    Permiso::where('id', $id)
                        ->update([
                            'documento' => $nombre_documento
                        ]);
                }
                return response()->json([
                    'status' => 'success',
                    'data' => $permiso
                ], 201);





        }


    }

    public function editar(Request $request){

        date_default_timezone_set("America/Lima");

        $tipo = $request->tipo;
        $id = $request->id;

        if ($tipo == "LICENCIA") {


            $categoria_id = $request->categoria_id;
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin;
            $dias = $request->dias;
            $detalle = strtoupper($request->detalle);
            $aprobador_id = $request->aprobador_id;

            $licencia = Licencia::where('id', $id)->update([
                'categoria_id' => $categoria_id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'dias' => $dias,
                'detalle' => $detalle,
                'aprobador_id' => $aprobador_id,

            ]);

            if (!empty($_FILES['documento'])) {

                $path_name = pathinfo($_FILES['documento']['name']);
                $extension = "." . $path_name['extension'];
                $nombre_documento = "sol_licencia_" . $id . $extension;
                $archivo = $_FILES['documento']['tmp_name'];
                $ruta = '/imagenes_server/gth/solicitud/licencias';
                $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
                move_uploaded_file($archivo, $ruta);

                Licencia::where('id', $id)
                    ->update([
                        'documento' => $nombre_documento
                    ]);
            }


            return response()->json([
                'status' => 'success',
                'data' => $licencia
            ], 201);

        }

        else if ($tipo == "PERMISO"){

            $hora_inicio = $request->hora_inicio;
            $hora_fin = $request->hora_fin;
            $tiempo = $request->tiempo;
            $detalle = strtoupper($request->detalle);
            $aprobador_id = $request->aprobador_id;
            $fecha_permiso = $request->fecha_permiso;
            $modo = $request->modo;


                $permiso = Permiso::where('id', $id)->update([
                    'modo' => $modo,
                    'fecha_permiso' => $fecha_permiso,
                    'hora_inicio' => $hora_inicio,
                    'hora_fin' => $hora_fin,
                    'tiempo' => $tiempo,
                    'detalle' => $detalle,
                    'aprobador_id' => $aprobador_id,

                ]);



                if (!empty($_FILES['documento'])) {

                    $path_name = pathinfo($_FILES['documento']['name']);
                    $extension = "." . $path_name['extension'];
                    $nombre_documento = "sol_permiso_" . $id . $extension;
                    $archivo = $_FILES['documento']['tmp_name'];
                    $ruta = '/imagenes_server/gth/solicitud/permisos';
                    $ruta = $_SERVER['DOCUMENT_ROOT'] . $ruta . "/" . $nombre_documento;
                    move_uploaded_file($archivo, $ruta);

                    Permiso::where('id', $id)
                        ->update([
                            'documento' => $nombre_documento
                        ]);
                }


                return response()->json([
                    'status' => 'success',
                    'data' => $permiso
                ], 201);


        }


    }


    public function listar($tipo,$fecha_inicio,$fecha_fin,$usuario)
    {



        if ($tipo == "LICENCIA") {

            $lista_licencias =  Licencia::from('solicitud_licencias as sol_lic')
            ->select(

                'sol_lic.id',
                'lic_cat.nombre as categoria_nombre',
                'lic_cat.abreviacion as categoria_abreviacion',

                'sol_lic.fecha_inicio',
                'sol_lic.fecha_fin',
                'sol_lic.fecha_retorno',
                'sol_lic.dias',
                'sol_lic.detalle',
                'sol_lic.documento',
                'sol_lic.goce',
                'sol_lic.comentario',

                'sol_lic.observacion',
                'sol_lic.estado',
                'sol_fec.fecha_solicitud',

                DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre_aprobador"),
                'us.usuario as aprobador_usuario'

            )
            ->join('solicitud_fechas as sol_fec','sol_fec.licencia_id','sol_lic.id')
            ->join('usuarios as us','us.dni','sol_lic.aprobador_id')
            ->join('licencia_categorias as lic_cat','lic_cat.id','sol_lic.categoria_id')
            ->whereBetween('sol_fec.fecha_solicitud', [$fecha_inicio, $fecha_fin])
            ->where('usuario_id', $usuario)
            ->where('sol_lic.estado','!=', 'E')
            ->orderBy('sol_fec.fecha_solicitud', 'desc')
            ->get();
            // dd($lista_licencias);

            return $lista_licencias;

        } else if ($tipo == "PERMISO") {


            $lista_permisos = Permiso::from('solicitud_permisos as sol_per')
            ->select(

                'sol_per.id',
                'sol_per.fecha_permiso',

                'sol_per.hora_inicio',
                'sol_per.hora_fin',
                'sol_per.hora_retorno',
                'sol_per.tiempo',
                'sol_per.detalle',
                'sol_per.documento',
                'sol_per.goce',
                'sol_per.comentario',
                'sol_per.observacion',
                'sol_per.estado',

                'sol_fec.fecha_solicitud',

                DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre_aprobador"),
                'us.usuario as aprobador_usuario'
            )
            ->join('solicitud_fechas as sol_fec','sol_fec.permiso_id','sol_per.id')
            ->join('usuarios as us','us.dni','sol_per.aprobador_id')
            ->whereBetween('sol_fec.fecha_solicitud', [$fecha_inicio, $fecha_fin])
            ->where('usuario_id', $usuario)
            ->where('sol_per.estado','!=', 'E')
            ->orderBy('sol_fec.fecha_solicitud', 'desc')
            ->get();

            return $lista_permisos;

        }

    }

    public function listar_solicitudes($estado,$fecha_inicio,$fecha_fin,$usuario)
    {


            $lista_licencias =  Licencia::from('solicitud_licencias as sol_lic')
            ->select(

                'sol_lic.id',
                'lic_cat.nombre as categoria_nombre',
                'lic_cat.abreviacion as categoria_abreviacion',

                DB::raw('
                CASE
                WHEN sol_lic.estado = "A" THEN "APROBADO"
                WHEN sol_lic.estado = "P" THEN "PENDIENTE"
                WHEN sol_lic.estado = "D" THEN "DENEGADO"

                ELSE 0
            END as estado_aprobacion'
            ),

                'sol_lic.fecha_inicio',
                'sol_lic.fecha_fin',
                'sol_lic.fecha_retorno',
                'sol_lic.dias',
                'sol_lic.detalle',
                'sol_lic.documento',
                'sol_lic.goce',
                'sol_lic.comentario',

                'sol_lic.observacion',
                'sol_lic.estado',
                'sol_fec.fecha_solicitud',

                DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre_colaborador"),
                'us.usuario as usuario_colaborador',
                DB::raw('"LICENCIA" as tipo')


            )
            ->join('solicitud_fechas as sol_fec','sol_fec.licencia_id','sol_lic.id')
            ->join('usuarios as us','us.dni','sol_lic.usuario_id')
            ->join('licencia_categorias as lic_cat','lic_cat.id','sol_lic.categoria_id')
            ->whereBetween('sol_fec.fecha_solicitud', [$fecha_inicio, $fecha_fin])
            ->where('aprobador_id', $usuario)
            ->whereNotIn('sol_lic.estado', ['E','U','C','V','I'])
            ->orderBy('sol_fec.fecha_solicitud', 'desc')
            ->get();



            $lista_permisos = Permiso::from('solicitud_permisos as sol_per')
            ->select(

                'sol_per.id',
                'sol_per.fecha_permiso',

                DB::raw('
                CASE
                WHEN sol_per.estado = "A" THEN "APROBADO"
                WHEN sol_per.estado = "P" THEN "PENDIENTE"
                WHEN sol_per.estado = "D" THEN "DENEGADO"

                ELSE 0
            END as estado_aprobacion'
            ),

                'sol_per.hora_inicio',
                'sol_per.hora_fin',
                'sol_per.hora_retorno',
                'sol_per.tiempo',
                'sol_per.detalle',
                'sol_per.documento',
                'sol_per.goce',
                'sol_per.comentario',
                'sol_per.observacion',
                'sol_per.estado',
                'sol_fec.fecha_solicitud',

                DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre_colaborador"),
                'us.usuario as usuario_colaborador',
                DB::raw('"PERMISO" as tipo'),
            )
            ->join('solicitud_fechas as sol_fec','sol_fec.permiso_id','sol_per.id')
            ->join('usuarios as us','us.dni','sol_per.usuario_id')
            ->whereBetween('sol_fec.fecha_solicitud', [$fecha_inicio, $fecha_fin])
            ->where('aprobador_id', $usuario)
            ->whereNotIn('sol_per.estado', ['E','U','C','V','I'])
            ->orderBy('sol_fec.fecha_solicitud', 'desc')
            ->get();


            $lista_completa = $lista_licencias->concat($lista_permisos);

            // dd($lista_licencias,$lista_permisos,$lista_completa);

            if($estado == "T"){

                $lista_filtrada = $lista_completa->sortByDesc('fecha_solicitud');


            } else {

                $lista_filtrada = $lista_completa->filter(function ($item) use ($estado) {
                    return $item->estado === $estado;
                })->sortByDesc('fecha_solicitud');
            }


            $lista_filtrada = $lista_filtrada->values();

            return $lista_filtrada;

    }


    public function ver_licencia($id)
    {

            $licencia =  Licencia::from('solicitud_licencias as sol_lic')
            ->select('sol_lic.*','lic_cat.nombre as nombre_categoria','lic_cat.abreviacion as abreviacion_categoria')
            ->join('solicitud_fechas as sol_fec','sol_fec.licencia_id','sol_lic.id')
            ->join('licencia_categorias as lic_cat','lic_cat.id','sol_lic.categoria_id')
            ->where('sol_lic.id',$id)->get();
            return $licencia;

    }

    public function ver_permiso($id)
    {
        $permiso =  Permiso::from('solicitud_permisos as sol_per')
        ->join('solicitud_fechas as sol_fec','sol_fec.permiso_id','sol_per.id')
        ->where('sol_per.id',$id)->get();

            return $permiso;

    }


    public function aprobar_solicitud(Request $request)
    {


        $id = $request->id;
        $tipo = $request->tipo;
        $comentario = strtoupper($request->comentario);
        $accion = $request->accion;
        $fecha_aprobacion = date("Y-m-d H:i:s");


        if ($tipo == "LICENCIA"){
// A -> Aprobado
            if($accion =="A"){

                Licencia::where('id', $id)
                ->update([
                    'estado' => $accion ,
                    'comentario' => $comentario
                ]);

            } else if($accion =="D"){

// D -> Denegado

                Licencia::where('id', $id)
                ->update([
                    'estado' => $accion ,
                    'comentario' => $comentario
                ]);

            }

            SolicitudFecha::where('licencia_id', $id)
            ->update([
                'fecha_aprobacion' => $fecha_aprobacion
            ]);


        } else if ($tipo == "PERMISO"){

            if($accion =="A"){

                Permiso::where('id', $id)
                ->update([
                    'estado' => $accion ,
                    'comentario' => $comentario
                ]);

            } else if($accion =="D"){

                Permiso::where('id', $id)
                ->update([
                    'estado' => $accion ,
                    'comentario' => $comentario
                ]);

            }

            SolicitudFecha::where('permiso_id', $id)
            ->update([
                'fecha_aprobacion' => $fecha_aprobacion
            ]);

        }
        return response()->json([
            'status' => 'success'
        ], 201);


    }
    public function eliminar_licencia($id)
    {

        $fecha_eliminacion = date("Y-m-d H:i:s");

                Licencia::where('id', $id)
                ->update([
                    'estado' => 'E'
                ]);

                SolicitudFecha::where('licencia_id', $id)
                ->update([
                    'fecha_eliminacion' => $fecha_eliminacion
                ]);



            return response()->json([
                'status' => 'success'
            ], 201);



            }

            public function eliminar_permiso($id)
            {
                $fecha_eliminacion = date("Y-m-d H:i:s");


                        Permiso::where('id', $id)
                        ->update([
                            'estado' => 'E'
                        ]);
                        SolicitudFecha::where('permiso_id', $id)
                        ->update([
                            'fecha_eliminacion' => $fecha_eliminacion
                        ]);


            return response()->json([
                'status' => 'success'
            ], 201);



    }



    public function listar_licencias()
   {

    $lista_licencias =  Licencia::from('solicitud_licencias as sol_lic')
            ->join('solicitud_fechas as sol_fec','sol_fec.licencia_id','sol_lic.id')
            ->get();

        return $lista_licencias;

   }

    public function listar_permisos()
    {


        $lista_permisos =  Permiso::from('solicitud_permisos as sol_per')
        ->join('solicitud_fechas as sol_fec','sol_fec.permiso_id','sol_per.id')
        ->get();

          return $lista_permisos;
    }



     public function listar_permiso_retorno($fecha,$agencia_id)
     {

        $lista_permisos = Permiso::from('solicitud_permisos as sol_per')
        ->select(
            'sol_per.*', 'us.agencia_id',
            DB::raw("CONCAT(us.apellido_paterno, ' ', us.apellido_materno, ' ', us.nombres) AS nombre_colaborador"),
            )
        ->leftjoin('usuarios as us','us.dni','sol_per.usuario_id')
        ->where('us.agencia_id', $agencia_id)
        ->where('fecha_permiso', $fecha)
        ->whereIn('estado', ['V', 'U'])
        ->get();
           return $lista_permisos;
     }

     public function verificar_permiso_retorno(request $request)
     {

        date_default_timezone_set("America/Lima");

        // dd($request);

        $fecha_corta= date("Y-m-d");
        $fecha_permiso = $request->fecha_permiso;
        $modo = $request->modo;
        $response = new \stdClass();
        $permiso_id = $request->permiso_id;
        $fecha_verificacion = date("Y-m-d H:i:s");
        $usuario_verificador = $request->usuario_verificador;
        $dia_semana = $request->dia_semana;

        // dd($fecha_corta,$fecha_permiso );
        // dd($dia_semana,$modo);
        if($fecha_corta >= $fecha_permiso){


        if($modo=="HORAS"){

            // dd($modo);


                $forma = $request->forma;
                $hora = date("H:i:s");

                if($forma == "web"){

                    $hora_retorno = $request->hora_retorno;


                }else if($forma == "movil"){
                    $hora_retorno = date("H:i");

                }

                $hora_inicio = $request->hora_inicio_n;
                $hora_inicio = substr($hora_inicio, 0, 8);


                if($dia_semana!=7){

                    $hora_inicio_l = "08:15:00";
                    $hora_fin_l = "18:30:00";

                    $hora_receso_i = "13:00:00";
                    $hora_receso_f = "15:00:00";

                }else{

                    $hora_inicio_l = "08:30:00";
                    $hora_fin_l = "13:15:00";

                    $hora_receso_i = "13:15:00";
                    $hora_receso_f = "15:00:00";


                }

                // dd($dia_semana);

                if (((strtotime($hora_retorno) >= strtotime($hora_inicio_l))&&(strtotime($hora_retorno) <= strtotime($hora_receso_i))) ||
               ( (strtotime($hora_retorno) >= strtotime($hora_receso_f))  && (strtotime($hora_retorno) <= strtotime($hora_fin_l)))) {


                    list($hora_inicio_h, $hora_inicio_m) = explode(':', $hora_inicio);
                    list($hora_retorno_h, $hora_retorno_m) = explode(':', $hora_retorno);



                    $minutosInicio = ($hora_inicio_h * 60) + $hora_inicio_m;
                    $minutosFin = ($hora_retorno_h * 60) + $hora_retorno_m;

                    $diferenciaMinutos = $minutosFin - $minutosInicio;

                    $horas = floor($diferenciaMinutos / 60);

                    $minutos = $diferenciaMinutos % 60;

                    $totalMinutos = $horas * 60 + $minutos;

                    if(strtotime($hora_inicio)<=strtotime($hora_receso_i) && strtotime($hora_retorno) >= strtotime($hora_receso_f)){


                        $horas -= 2;

                    }

                    $tiempo =  "$horas hora" . ($horas !== 1 ? 's' : '') . " y $minutos minuto" . ($minutos !== 1 ? 's' : '');



                    if($hora_inicio ==$hora_receso_f && $hora_retorno ==$hora_fin_l ){


                        $response->success = "horario";

                    }else{
                        // dd($hora_inicio,$hora_receso_f,$hora_retorno,$hora_fin_l);

                                // dd($horas);
                    if($totalMinutos <= 240){
                        Permiso::where('id', $permiso_id)
                        ->update([
                            'modo' => $modo,
                            'hora_retorno' => $hora_retorno,
                            'tiempo' => $tiempo,
                            'estado' => 'U',

                        ]);
                        SolicitudFecha::where('permiso_id', $permiso_id)
                        ->update([
                            'fecha_verificacion' => $fecha_verificacion,
                            'usuario_ver' => $usuario_verificador,

                        ]);

                        $response->success = true;

                    }else{

                        $response->success = "tiempo";

                    }
                    }


                } else {

                    $response->success = "f_horario";

                }




        }else{



            if($modo=="TURNO-M"){



                if($dia_semana != 7){
                    Permiso::where('id', $permiso_id)
                    ->update([
                        'hora_inicio' => '08:15:00',
                        'hora_fin' => '13:00:00',
                        'hora_retorno' => '13:00:00',
                        'modo' => $modo,
                        'tiempo' => '0.5 dias',
                        'estado' => 'U',

                    ]);

                } else if($dia_semana == 7){

                    Permiso::where('id', $permiso_id)
                    ->update([
                        'hora_inicio' => '08:30:00',
                        'hora_fin' => '13:15:00',
                        'hora_retorno' => '13:15:00',
                        'modo' => $modo,
                        'tiempo' => '0.5 dias',
                        'estado' => 'U',

                    ]);

                }

                SolicitudFecha::where('permiso_id', $permiso_id)
                ->update([
                    'fecha_verificacion' => $fecha_verificacion,
                    'usuario_ver' => $usuario_verificador,

                ]);




            }

            if($modo=="TURNO-T"){
                Permiso::where('id', $permiso_id)
                ->update([
                    'hora_inicio' => '15:00:00',
                    'hora_fin' => '18:30:00',
                    'hora_retorno' => '18:30:00',
                    'modo' => $modo,
                    'tiempo' => '0.5 dias',
                    'estado' => 'U',

                ]);

                SolicitudFecha::where('permiso_id', $permiso_id)
                ->update([
                    'fecha_verificacion' => $fecha_verificacion,
                    'usuario_ver' => $usuario_verificador,

                ]);

            }


            $response->success = true;


        }

    } else {

        $response->success = "fecha";
    }

        // dd($dia_semana,$modo);

        return $response;


     }



}
