<?php

namespace Modules\Creditos\Presentation\Controllers\Clientes;

use App\Http\Controllers\Controller;
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\Creditos\Presentation\Controllers\CreditosController;
use Modules\Creditos\Presentation\Controllers\Credito\AprobacionController;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Auditoria\CarritoDetalleAUD;
use Modules\General\Infrastructure\Persistence\Eloquent\Cargo;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\Desembolso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoCuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoMora;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoNotificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\ComisionPago;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja\PagoVoucher;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Cliente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Prenda;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Pariente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Aval;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Negocio;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Comentario;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Visita;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\AlbumFoto;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Transferencia;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Propuesta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Aprobacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Credito;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Notificacion;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Facturado;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Compromiso;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Cuota;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Evaluacion_financiera;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_activo_corriente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_activo_no_corriente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_comentarios;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_flujo_caja;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Ef_pasivo_corriente;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\CarritoDetalle;

use Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito\Records\CreditoRegistroRecord;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta\BancoMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMeta;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Inversion\InversionMetaMovimiento;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Inversion\ProductosMeta;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ClienteTransferenciaController extends Controller
{

    public function transferencia()
    {
        $x = session()->all();

        if (empty($x['usuario_dni'])) {
            return redirect('/');
        } else {

            $band = (new PermisosController)->verificarPermiso($x['usuario_dni'], 'TRANSFERENCIA', 'CREDITOS_CLIENTES');

            if ($band == 1) {


                $cargos = Cargo::select('id')->whereIn('cargo', [
                    'ASESOR DE NEGOCIOS',
                    'JEFE DE CRÉDITOS',
                    'COORDINADOR DE CRÉDITOS'
                ])->get();

                $asesores = Usuario::select(
                    'dni',
                    'usuario',
                    'agencia_id',
                    'habilitado'
                )
                    ->whereIn('cargo_id', $cargos)
                    ->orderBy('usuario', 'asc')
                    ->get();

                return Inertia::render('Creditos/Clientes/transferencia')->with(['asesores' => $asesores]);
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


        $lista_clientes = Cliente::on($conexion)->from('cliente_registros as cli_reg')->select(
            'cli_reg.id',
            DB::raw("CONCAT(cli_reg.apellido_paterno, ' ',cli_reg.apellido_materno,' ',cli_reg.nombres) as cliente"),
            'cli_reg.codigo_expediente',

            'usu.usuario as usuario_asesor'
        )
            ->join('solucion_master.usuarios as usu', 'cli_reg.asesor_id', 'usu.dni')
            ->where('asesor_id', $asesor_id)
            ->orderBy('cli_reg.apellido_paterno', 'asc')
            ->get();


        return response()->json([
            'lista_clientes' => $lista_clientes
        ], 200);
    }

    public function transferir(Request $request)
    {

        $agencia_origen = $request->agencia_origen;
        $asesor_origen = $request->asesor_origen;

        $agencia_destino = $request->agencia_destino;
        $asesor_destino = $request->asesor_destino;

        $conexion_origen = 'master_' . $agencia_origen;
        $conexion_destino = 'master_' . $agencia_destino;

        $clientes_seleccionados = json_decode($request->clientes_seleccionados);
        $datos_adicionales = json_decode($request->datos_adicionales);

        $datos_registro = (new CreditosController)->datos_registro($agencia_origen);

        foreach ($clientes_seleccionados as $cliente_id) {

            // Transferir CLIENTE_REGISTROS-------------------------
            $datos_personales = Cliente::on($conexion_origen)->where('id', $cliente_id)->get()->last();

            if ($datos_personales->numero_expediente != null) {

                $nuevo_expediente = (new AprobacionController)->generar_expediente($agencia_destino);
                $datos_personales->numero_expediente = $nuevo_expediente['numero_expediente'];
                $datos_personales->codigo_expediente = $nuevo_expediente['codigo_expediente'];
            }

            $datos_personales->agencia_id = $agencia_destino;
            $datos_personales->asesor_id = $asesor_destino;
            $datos_personales->promotor_id = $asesor_destino;
            $imagen_dni = $datos_personales->imagen_dni;
            $datos_personales->datos_actualizacion = $datos_registro;

            $datos_personales = $datos_personales->toArray();
            unset($datos_personales['id']);

            $nuevo_cliente_id = Cliente::on($conexion_destino)->insertGetId($datos_personales);

            // MOVIENDO IMAGEN_DNI
            if ($imagen_dni != null) {
                $año = substr($imagen_dni, 0, 4);
                $path_folder =  '/imagenes_server/creditos/clientes/dni/';
                $source_file = $path_folder . $agencia_origen . '/' . $año . '/' . $imagen_dni;
                $existe = Storage::disk('public')->exists($source_file);
                if ($existe) {
                    $destination_path = $path_folder . $agencia_destino . '/' . $año . '/' . $imagen_dni;
                    $existe_destino = Storage::disk('public')->exists($destination_path);
                    if ($existe_destino) {
                        Storage::disk('public')->delete($destination_path);
                    }
                    Storage::disk('public')->move($source_file, $destination_path);
                }
            }

            // Transferir CLIENTE_NEGOCIOS--------------------------
            $negocios = Negocio::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
            $lista_negocios = [];
            foreach ($negocios as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $item->datos_actualizacion = $datos_registro;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $item = $item->toArray();

                unset($item['id']);
                $nuevo_negocio_id = Negocio::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nuevo_negocio_id;
                $lista_negocios[] = $relacion_id;
            }

            // Transferir CLIENTE_PARIENTES--------------------------
            $parientes = Pariente::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
            $lista_parientes = [];
            foreach ($parientes as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $item->datos_actualizacion = $datos_registro;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $item = $item->toArray();
                unset($item['id']);
                $nuevo_pariente_id =  Pariente::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nuevo_pariente_id;
                $lista_parientes[] = $relacion_id;
            }

            // Transferir CLIENTE_AVALES--------------------------
            $avales = Aval::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
            $lista_avales = [];
            foreach ($avales as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $item->datos_actualizacion = $datos_registro;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $item = $item->toArray();
                unset($item['id']);
                $nuevo_aval_id = Aval::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nuevo_aval_id;
                $lista_avales[] = $relacion_id;
            }

            // Actualizar PARIENTES Y AVALES dependientes --------------------------
            $agencias = Agencia::all();
            foreach ($agencias as $item) {

                $conexion_verificar = 'master_' . $item->id_agencia;

                Pariente::on($conexion_verificar)->where([
                    ['agencia_pariente', $agencia_origen],
                    ['pariente_id', $cliente_id]
                ])
                    ->update([
                        'agencia_pariente' => $agencia_destino,
                        'pariente_id' => $nuevo_cliente_id
                    ]);
                Aval::on($conexion_verificar)->where([
                    ['agencia_aval', $agencia_origen],
                    ['aval_id', $cliente_id]
                ])
                    ->update([
                        'agencia_aval' => $agencia_destino,
                        'aval_id' => $nuevo_cliente_id
                    ]);
            }

            // Transferir CLIENTE_PRENDAS--------------------------
            $prendas = Prenda::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
            $lista_prendas = [];
            foreach ($prendas as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $fotos = $item->fotos;
                $acta_entrega = $item->acta_entrega;
                $item = $item->toArray();

                // MOVIENDO FOTO_PRENDAS

                unset($item['id']);
                $nueva_prenda_id = Prenda::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nueva_prenda_id;
                $lista_prendas[] = $relacion_id;

                $fotos = json_decode($fotos);
                $nuevas_fotos = [
                    'foto_1' => null,
                    'foto_2' => null,
                    'foto_3' => null,
                    'foto_4' => null,
                ];

                foreach ($fotos as $key => $item) {
                    if ($item != null) {
                        $numero_foto = substr($key, 5, 1);
                        $año = substr($item, 0, 4);
                        $path_folder =  '/imagenes_server/creditos/clientes/prendas/fotos/';
                        $source_file = $path_folder . $agencia_origen . '/' . $año . '/' . $item;

                        $extension = explode('.', $item)[1];
                        $nuevo_nombre = $año . '_' . $nuevo_cliente_id . '_' . $nueva_prenda_id . '_' . $numero_foto . '.' . $extension;
                        $existe = Storage::disk('public')->exists($source_file);
                        if ($existe) {
                            $destination_path = $path_folder . $agencia_destino . '/' . $año . '/' . $nuevo_nombre;
                            $existe_destino = Storage::disk('public')->exists($destination_path);
                            if ($existe_destino) {
                                Storage::disk('public')->delete($destination_path);
                            }
                            Storage::disk('public')->move($source_file, $destination_path);
                        }
                        $nuevas_fotos[$key] = $nuevo_nombre;
                    }
                }

                $nuevas_fotos = json_encode($nuevas_fotos);

                if ($acta_entrega != null) {
                    $año = substr($acta_entrega, 0, 4);
                    $path_folder =  '/imagenes_server/creditos/clientes/prendas/actas/';
                    $source_file = $path_folder . $agencia_origen . '/' . $año . '/' . $acta_entrega;

                    $extension = explode('.', $acta_entrega)[1];

                    $nuevo_nombre = $año . '_' . $nueva_prenda_id . '.' . $extension;
                    $existe = Storage::disk('public')->exists($source_file);
                    if ($existe) {
                        $destination_path = $path_folder . $agencia_destino . '/' . $año . '/' . $nuevo_nombre;
                        $existe_destino = Storage::disk('public')->exists($destination_path);
                        if ($existe_destino) {
                            Storage::disk('public')->delete($destination_path);
                        }
                        Storage::disk('public')->move($source_file, $destination_path);
                    }
                    $acta_entrega = $nuevo_nombre;
                }

                Prenda::on($conexion_destino)->where('id', $nueva_prenda_id)->update([
                    'acta_entrega' => $acta_entrega,
                    'fotos' => $nuevas_fotos
                ]);
            }

            // Transferir CREDITO_PROPUESTAS--------------------------
            $propuestas = Propuesta::on($conexion_origen)->where(
                'cliente_id',
                $cliente_id
            )->get();
            $lista_propuestas_antiguas = [];
            $lista_propuestas = [];

            foreach ($propuestas as $item) {

                $item->agencia_id = $agencia_destino;
                $item->cliente_id = $nuevo_cliente_id;

                $negocio_id = null;
                $pariente_id = null;
                $aval_id = null;

                $negocio_filtrado = array_filter($lista_negocios, function ($var) use ($item) {
                    $negocio = $var->id_antiguo;
                    return  $negocio == $item->negocio_id;
                });

                if (count($negocio_filtrado) > 0) {
                    foreach ($negocio_filtrado as $item_1) {
                        $negocio_id = $item_1->id_nuevo;
                    }
                }

                $prendas = json_decode($item->prendas);

                if ($prendas != null) {
                    $nuevas_prendas = [];
                    foreach ($prendas as $value) {

                        foreach ($lista_prendas as $item_1) {
                            if ($value == $item_1->id_antiguo) {
                                $nuevas_prendas[] = $item_1->id_nuevo;
                            }
                        }
                    }

                    $item->prendas = json_encode($nuevas_prendas);
                } else {
                    $item->prendas = null;
                }

                $item->negocio_id = $negocio_id;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $lista_propuestas_antiguas[] = $item->id;
                $item = $item->toArray();
                unset($item['id']);
                $nueva_propuesta_id = Propuesta::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nueva_propuesta_id;
                $lista_propuestas[] = $relacion_id;
            }

            // Transferir CREDITO_APROBACIONES--------------------------
            $aprobaciones = Aprobacion::on($conexion_origen)->whereIn('propuesta_id', $lista_propuestas_antiguas)->get();
            $lista_aprobaciones_antiguas = [];
            $lista_aprobaciones = [];
            foreach ($aprobaciones as $item) {
                $propuesta_id = null;

                $propuesta_filtrada = array_filter($lista_propuestas, function ($var) use ($item) {
                    $propuesta = $var->id_antiguo;
                    return  $propuesta == $item->propuesta_id;
                });

                if (count($propuesta_filtrada) > 0) {
                    foreach ($propuesta_filtrada as $item_1) {
                        $propuesta_id = $item_1->id_nuevo;
                    }
                }

                $item->propuesta_id = $propuesta_id;

                $codigo_seguimiento = (new AprobacionController)->generar_codigo_seguimiento(
                    $agencia_destino,
                    $nuevo_expediente['codigo_expediente'],
                    $item->numero_credito,
                    false
                );

                $item->codigo_seguimiento = $codigo_seguimiento;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $lista_aprobaciones_antiguas[] = $item->id;
                $item = $item->toArray();
                unset($item['id']);
                $nueva_aprobacion_id = Aprobacion::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nueva_aprobacion_id;
                $lista_aprobaciones[] = $relacion_id;
            }

            // Transferir CREDITO_REGISTROS--------------------------
            $creditos = Credito::on($conexion_origen)->whereIn('aprobacion_id', $lista_aprobaciones_antiguas)->get();
            $lista_creditos_antiguos = [];
            $lista_creditos = [];
            foreach ($creditos as $item) {
                $aprobacion_id = null;

                $aprobacion_filtrada = array_filter($lista_aprobaciones, function ($var) use ($item) {
                    $aprobacion = $var->id_antiguo;
                    return  $aprobacion == $item->aprobacion_id;
                });

                if (count($aprobacion_filtrada) > 0) {
                    foreach ($aprobacion_filtrada as $item_1) {
                        $aprobacion_id = $item_1->id_nuevo;
                    }
                }

                $documento_cancelado = $item->documento_cancelado;
                $item->aprobacion_id = $aprobacion_id;
                $item->agencia_id = $agencia_destino;
                $item->cliente_id = $nuevo_cliente_id;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $lista_creditos_antiguos[] = $item->id;
                $item = $item->toArray();
                unset($item['id']);
                $nuevo_credito_id = Credito::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nuevo_credito_id;
                $lista_creditos[] = $relacion_id;

                if ($documento_cancelado != null) {
                    $año = substr($documento_cancelado, 0, 4);
                    $path_folder =  '/imagenes_server/creditos/caja/cancelaciones/';
                    $source_file = $path_folder . $agencia_origen . '/' . $año . '/' . $documento_cancelado;

                    $extension = explode('.', $documento_cancelado)[1];

                    $nuevo_nombre = $año . '_' . $nuevo_credito_id . '.' . $extension;
                    $existe = Storage::disk('public')->exists($source_file);
                    if ($existe) {
                        $destination_path = $path_folder . $agencia_destino . '/' . $año . '/' . $nuevo_nombre;
                        $existe_destino = Storage::disk('public')->exists($destination_path);
                        if ($existe_destino) {
                            Storage::disk('public')->delete($destination_path);
                        }
                        Storage::disk('public')->move($source_file, $destination_path);
                    }

                    $documento_cancelado = $nuevo_nombre;
                }

                Credito::on($conexion_destino)->where('id', $nuevo_credito_id)->update([
                    'documento_cancelado' => $documento_cancelado
                ]);
            }

            // Transferir CREDITO_CUOTAS--------------------------
            $cuotas = Cuota::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            foreach ($cuotas as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });

                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $item = $item->toArray();
                unset($item['id']);
                Cuota::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CREDITO_NOTIFICACIONES--------------------------
            $notificaciones = Notificacion::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            $lista_notificaciones_antiguas = [];
            $lista_notificaciones = [];
            foreach ($notificaciones as $item) {
                $credito_id = null;

                $notificacion_filtrada = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });

                if (count($notificacion_filtrada) > 0) {
                    foreach ($notificacion_filtrada as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $lista_notificaciones_antiguas[] = $item->id;
                $item = $item->toArray();
                unset($item['id']);
                $nueva_notificacion_id = Notificacion::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nueva_notificacion_id;
                $lista_notificaciones[] = $relacion_id;
            }

            // Actualizar BANCO_MOVIMIENTOS--------------------------
            $movimientos = BancoMovimiento::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            foreach ($movimientos as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });


                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                BancoMovimiento::on($conexion_origen)->where('id', $item->id)
                    ->update([
                        'agencia_credito' => $agencia_destino,
                        'credito_id' => $credito_id
                    ]);
            }

            // Transferir CAJA_DESEMBOLSOS--------------------------
            $desembolsos = Desembolso::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            $lista_desembolsos_antiguos = [];
            $lista_desembolsos = [];
            foreach ($desembolsos as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });

                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $lista_desembolsos_antiguos[] = $item->id;
                $item = $item->toArray();
                unset($item['id']);
                $nuevo_desembolso_id = Desembolso::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nuevo_desembolso_id;
                $lista_desembolsos[] = $relacion_id;
            }

            // Transferir CAJA_COMISIONES_PAGOS--------------------------
            $pago_comisiones = ComisionPago::on($conexion_origen)->whereIn('desembolso_id', $lista_desembolsos_antiguos)->get();
            foreach ($pago_comisiones as $item) {
                $desembolso_id = null;

                $desembolso_filtrado = array_filter($lista_desembolsos, function ($var) use ($item) {
                    $desembolso = $var->id_antiguo;
                    return  $desembolso == $item->desembolso_id;
                });

                if (count($desembolso_filtrado) > 0) {
                    foreach ($desembolso_filtrado as $item_1) {
                        $desembolso_id = $item_1->id_nuevo;
                    }
                }

                $item->desembolso_id = $desembolso_id;

                $item = $item->toArray();
                unset($item['id']);
                ComisionPago::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CREDITO_FACTURADOS--------------------------
            $facturados = Facturado::on($conexion_origen)->whereIn('desembolso_id', $lista_desembolsos_antiguos)->get();
            foreach ($facturados as $item) {
                $desembolso_id = null;

                $desembolso_filtrado = array_filter($lista_desembolsos, function ($var) use ($item) {
                    $desembolso = $var->id_antiguo;
                    return  $desembolso == $item->desembolso_id;
                });

                if (count($desembolso_filtrado) > 0) {
                    foreach ($desembolso_filtrado as $item_1) {
                        $desembolso_id = $item_1->id_nuevo;
                    }
                }

                $item->desembolso_id = $desembolso_id;

                $item = $item->toArray();
                unset($item['id']);
                Facturado::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CAJA_PAGO_CUOTAS--------------------------
            $pagos_cuota = PagoCuota::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            foreach ($pagos_cuota as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });

                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $item = $item->toArray();
                unset($item['id']);
                PagoCuota::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CAJA_PAGO_MORAS--------------------------
            $pagos_mora = PagoMora::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            foreach ($pagos_mora as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });

                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $item = $item->toArray();
                unset($item['id']);
                PagoMora::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CAJA_PAGO_NOTIFICACIONES--------------------------
            $pagos_notificaciones = PagoNotificacion::on($conexion_origen)->whereIn('notificacion_id', $lista_notificaciones_antiguas)->get();
            foreach ($pagos_notificaciones as $item) {
                $notificacion_id = null;

                $notificacion_filtrada = array_filter($lista_notificaciones, function ($var) use ($item) {
                    $notificacion = $var->id_antiguo;
                    return  $notificacion == $item->notificacion_id;
                });

                if (count($notificacion_filtrada) > 0) {
                    foreach ($notificacion_filtrada as $item_1) {
                        $notificacion_id = $item_1->id_nuevo;
                    }
                }

                $item->notificacion_id = $notificacion_id;

                $item = $item->toArray();
                unset($item['id']);
                PagoNotificacion::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CAJA_PAGO_VOUCHERS--------------------------
            $pagos_vouchers = PagoVoucher::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            $lista_vouchers = [];

            foreach ($pagos_vouchers as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $credito = $var->id_antiguo;
                    return  $credito == $item->credito_id;
                });

                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $voucher = (object)['antiguo_id' => $item->id];

                $item = $item->toArray();
                unset($item['id']);
                $nuevo_voucher_id = PagoVoucher::on($conexion_destino)->insertGetId($item);

                $voucher->nuevo_id = $nuevo_voucher_id;

                $lista_vouchers[] = $voucher;
            }

            // Transferir CREDITO_CARRITO_DETALLES--------------------------

            foreach ($lista_creditos as $item) {

                $creditos_carrito = CarritoDetalle::on($conexion_origen)
                    ->where('credito_id', $item->id_antiguo)
                    ->get();

                foreach ($creditos_carrito as $item_1) {

                    $item_1->credito_id = $item->id_nuevo;

                    // Para actualizar el voucher relacionado al pago de la cobranza en carrito
                    if ($item_1->voucher_id != null) {
                        $antiguo_voucher_id = $item_1->voucher_id;
                        $voucher = array_filter($lista_vouchers, function ($item_2) use ($antiguo_voucher_id) {
                            return $item_2->antiguo_id == $antiguo_voucher_id;
                        });

                        if (count($voucher) > 0) {
                            $voucher = array_values($voucher);
                            $voucher_nuevo_id = $voucher[0]->nuevo_id;
                            $item_1->voucher_id = $voucher_nuevo_id;
                        }
                    }

                    $detalle_antiguo_id  = $item_1->id;

                    $item_1 = $item_1->toArray();
                    unset($item_1['id']);

                    $detalle_nuevo_id = CarritoDetalle::on($conexion_destino)->insertGetId($item_1);

                    $historial = CarritoDetalleAUD::on($conexion_origen)
                        ->where('registro_id', $detalle_antiguo_id)
                        ->get();

                    foreach ($historial as $item_2) {
                        $item_2->registro_id = $detalle_nuevo_id;

                        $item_2 = $item_2->toArray();
                        unset($item_2['id']);

                        CarritoDetalleAUD::on($conexion_destino)
                            ->insertGetId($item_2);
                    }
                }
            }

            // Transferir CREDITO_COMPROMISOS--------------------------
            $compromisos = Compromiso::on($conexion_origen)->whereIn('credito_id', $lista_creditos_antiguos)->get();
            foreach ($compromisos as $item) {
                $credito_id = null;

                $credito_filtrado = array_filter($lista_creditos, function ($var) use ($item) {
                    $compromiso = $var->id_antiguo;
                    return  $compromiso == $item->credito_id;
                });

                if (count($credito_filtrado) > 0) {
                    foreach ($credito_filtrado as $item_1) {
                        $credito_id = $item_1->id_nuevo;
                    }
                }

                $item->credito_id = $credito_id;

                $item = $item->toArray();
                unset($item['id']);
                Compromiso::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CLIENTE_COMENTARIOS--------------------------
            $comentarios = Comentario::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
            foreach ($comentarios as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $item = $item->toArray();
                unset($item['id']);
                Comentario::on($conexion_destino)->insertGetId($item);
            }

            // Transferir CLIENTE_VISITAS--------------------------
            $visitas = Visita::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
            foreach ($visitas as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $item = $item->toArray();
                unset($item['id']);
                Visita::on($conexion_destino)->insertGetId($item);
            }

            // Transferir INVERSION_META_REGISTROS--------------------------
            $inversiones_meta = InversionMeta::on($conexion_origen)
                ->where('cliente_id', $cliente_id)
                ->get();
            $lista_inversiones_meta_antiguos = [];
            $lista_inversiones_meta = [];

            foreach ($inversiones_meta as $item) {
                $item->cliente_id = $nuevo_cliente_id;

                $producto_origen = ProductosMeta::on($conexion_origen)->select('producto')
                    ->where('id', $item->producto_meta_id)->get()->last();
                $producto_destino = ProductosMeta::on($conexion_destino)->select('id')
                    ->where('producto', $producto_origen)->get()->last();


                $producto_destino_id = $producto_destino != null ? $producto_destino->id : null;

                $item->producto_meta_id = $producto_destino_id;

                $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

                $lista_inversiones_meta_antiguos[] = $item->id;

                $item = $item->toArray();
                unset($item['id']);
                $nueva_inversion_meta_id = InversionMeta::on($conexion_destino)->insertGetId($item);
                $relacion_id->id_nuevo = $nueva_inversion_meta_id;
                $lista_inversiones_meta[] = $relacion_id;
            }

            // Transferir INVERSION_META_MOVIMIENTOS--------------------------
            $inversiones_meta_movimientos = InversionMetaMovimiento::on($conexion_origen)
                ->whereIn('inversion_id', $lista_inversiones_meta_antiguos)
                ->get();

            foreach ($inversiones_meta_movimientos as $item) {
                $inversion_id = null;

                $inversion_filtrada = array_filter($lista_inversiones_meta, function ($var) use ($item) {
                    $inversion = $var->id_antiguo;
                    return  $inversion == $item->inversion_id;
                });

                if (count($inversion_filtrada) > 0) {
                    foreach ($inversion_filtrada as $item_1) {
                        $inversion_id = $item_1->id_nuevo;
                    }
                }

                $item->inversion_id = $inversion_id;

                $item = $item->toArray();
                unset($item['id']);
                InversionMetaMovimiento::on($conexion_destino)->insertGetId($item);
            }

            $datos_transferencia = (object)[
                'cliente_id' => $cliente_id,
                'nuevo_cliente_id' => $nuevo_cliente_id,
                'agencia_origen' => $agencia_origen,
                'agencia_destino' => $agencia_destino,
            ];

            $evaluacion_financiera = 0;
            $album_fotos = 0;

            // VERIFICAR SI TRANSFIERE EVALUACION_FINANCIERA-------
            if (in_array('evaluacion_financiera', $datos_adicionales)) {
                $evaluacion_financiera = 1;
                $this->transferir_evaluacion_financiera($datos_transferencia);
            }

            // VERIFICAR SI TRANSFIERE ALBUM_FOTOS-------
            if (in_array('album_fotos', $datos_adicionales)) {
                $album_fotos = 1;
                $this->transferir_album_fotos($datos_transferencia);
            }

            // ELIMINAR LOS DATOS DE LA AGENCIA ORIGEN-------
            $this->eliminar_datos_origen($datos_transferencia);

            // INSERTAR EN CLIENTE_TRANSFERENCIAS-------

            $dni = $datos_personales['dni'];
            $cliente = $datos_personales['apellido_paterno'] . ' ' .
                $datos_personales['apellido_materno'] . ' ' .
                $datos_personales['nombres'];

            Transferencia::on($conexion_origen)->create([
                'dni' => $dni,
                'cliente' => $cliente,
                'agencia_origen' => $agencia_origen,
                'asesor_origen' => $asesor_origen,
                'agencia_destino' => $agencia_destino,
                'asesor_destino' => $asesor_destino,
                'evaluacion_financiera' => $evaluacion_financiera,
                'album_fotos' => $album_fotos,
                'datos_creacion' => $datos_registro
            ]);
        }

        // dd('exito');
        return response()->json(['message' => 'Cliente transferido correctamente'], 201);
    }

    public function transferir_evaluacion_financiera($datos_transferencia)
    {

        $cliente_id = $datos_transferencia->cliente_id;
        $nuevo_cliente_id = $datos_transferencia->nuevo_cliente_id;
        $agencia_origen = $datos_transferencia->agencia_origen;
        $conexion_origen = 'master_' . $agencia_origen;
        $agencia_destino = $datos_transferencia->agencia_destino;
        $conexion_destino = 'master_' . $agencia_destino;

        // Transferir CLIENTE_EVALUACION_FINANCIERA-------------------------

        $evaluacion = Evaluacion_financiera::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
        $antigua_evaluacion_id = null;
        $nueva_evaluacion_id = null;
        foreach ($evaluacion as $item) {
            $item->cliente_id = $nuevo_cliente_id;

            $relacion_id = (object)['id_antiguo' => $item->id, 'id_nuevo' => null];

            $antigua_evaluacion_id = $item->id;
            $item = $item->toArray();
            unset($item['id']);
            $nueva_evaluacion_id = Evaluacion_financiera::on($conexion_destino)->insertGetId($item);
            $relacion_id->id_nuevo = $nueva_evaluacion_id;
        }

        // Transferir CLIENTE_EVALUACION_FINANCIERA_ACTIVO_CORRIENTE-------------------------
        $ef_activo_corriente = Ef_activo_corriente::on($conexion_origen)->where('evaluacion_id', $antigua_evaluacion_id)->get();
        foreach ($ef_activo_corriente as $item) {

            $item->evaluacion_id = $nueva_evaluacion_id;

            $item = $item->toArray();
            unset($item['id']);
            Ef_activo_corriente::on($conexion_destino)->insertGetId($item);
        }

        // Transferir CLIENTE_EVALUACION_FINANCIERA_ACTIVO_NO_CORRIENTE-------------------------
        $ef_activo_no_corriente = Ef_activo_no_corriente::on($conexion_origen)->where('evaluacion_id', $antigua_evaluacion_id)->get();
        foreach ($ef_activo_no_corriente as $item) {

            $item->evaluacion_id = $nueva_evaluacion_id;

            $bienes = $item->inmueble_maquinaria_equipo;

            $item = $item->toArray();
            unset($item['id']);
            $nuevo_activo_no_corriente_id = Ef_activo_no_corriente::on($conexion_destino)->insertGetId($item);

            if ($bienes != null) {
                $bienes = json_decode($bienes);

                foreach ($bienes as $item_1) {
                    $nuevas_fotos = [
                        'foto_1' => null,
                        'foto_2' => null,
                        'foto_3' => null,
                        'foto_4' => null,
                    ];
                    foreach ($item_1->foto as $key => $item_2) {

                        if ($item_2 != null) {
                            $numero_foto = substr($key, 5, 1);
                            $año = substr($item_2, 0, 4);
                            $path_folder =  '/imagenes_server/creditos/evaluaciones/bienes/';
                            $source_file = $path_folder . $agencia_origen . '/' . $año . '/' . $item_2;

                            $extension = explode('.', $item_2)[1];
                            $nuevo_nombre = $año . '_' . $nuevo_activo_no_corriente_id . '_' . $item_1->indice . '_' . $numero_foto . '.' . $extension;
                            $existe = Storage::disk('public')->exists($source_file);
                            if ($existe) {
                                $destination_path = $path_folder . $agencia_destino . '/' . $año . '/' . $nuevo_nombre;
                                $existe_destino = Storage::disk('public')->exists($destination_path);
                                if ($existe_destino) {
                                    Storage::disk('public')->delete($destination_path);
                                }
                                Storage::disk('public')->move($source_file, $destination_path);
                            }
                            $nuevas_fotos[$key] = $nuevo_nombre;
                        }
                    }
                    $item_1->foto = $nuevas_fotos;
                }

                $bienes = json_encode($bienes);

                Ef_activo_no_corriente::on($conexion_destino)->where('id', $nuevo_activo_no_corriente_id)
                    ->update(['inmueble_maquinaria_equipo' => $bienes]);
            }
        }

        // Transferir CLIENTE_EVALUACION_FINANCIERA_PASIVO_CORRIENTE-------------------------
        $ef_pasivo_corriente = Ef_pasivo_corriente::on($conexion_origen)->where('evaluacion_id', $antigua_evaluacion_id)->get();
        foreach ($ef_pasivo_corriente as $item) {

            $item->evaluacion_id = $nueva_evaluacion_id;

            $item = $item->toArray();
            unset($item['id']);
            Ef_pasivo_corriente::on($conexion_destino)->insertGetId($item);
        }

        // Transferir CLIENTE_EVALUACION_FINANCIERA_FLUJO_CAJA-------------------------
        $ef_flujo_caja = Ef_flujo_caja::on($conexion_origen)->where('evaluacion_id', $antigua_evaluacion_id)->get();
        foreach ($ef_flujo_caja as $item) {

            $item->evaluacion_id = $nueva_evaluacion_id;

            $item = $item->toArray();
            unset($item['id']);
            Ef_flujo_caja::on($conexion_destino)->insertGetId($item);
        }

        // Transferir CLIENTE_EVALUACION_FINANCIERA_COMENTARIOS-------------------------
        $ef_comentarios = Ef_comentarios::on($conexion_origen)->where('evaluacion_id', $antigua_evaluacion_id)->get();
        foreach ($ef_comentarios as $item) {

            $item->evaluacion_id = $nueva_evaluacion_id;

            $item = $item->toArray();
            unset($item['id']);
            Ef_comentarios::on($conexion_destino)->insertGetId($item);
        }
    }

    public function transferir_album_fotos($datos_transferencia)
    {
        $cliente_id = $datos_transferencia->cliente_id;
        $nuevo_cliente_id = $datos_transferencia->nuevo_cliente_id;
        $agencia_origen = $datos_transferencia->agencia_origen;
        $conexion_origen = 'master_' . $agencia_origen;
        $agencia_destino = $datos_transferencia->agencia_destino;
        $conexion_destino = 'master_' . $agencia_destino;

        // Transferir CLIENTE_ALBUM_FOTOS-------------------------

        $fotos = AlbumFoto::on($conexion_origen)->where('cliente_id', $cliente_id)->get();

        foreach ($fotos as $item) {
            $item->cliente_id = $nuevo_cliente_id;

            $categoria_id = $item->categoria_id;
            $imagen = $item->imagen;
            $item = $item->toArray();
            unset($item['id']);
            $nueva_foto_id = AlbumFoto::on($conexion_destino)->insertGetId($item);

            $año = substr($imagen, 0, 4);
            $dni = substr($imagen, 5, 8);
            $path_folder =  '/imagenes_server/creditos/clientes/album/';
            $source_file = $path_folder . $agencia_origen . '/' . $año . '/' . $imagen;
            $extension = explode('.', $imagen)[1];
            $nuevo_nombre = $año . '_' . $dni . '_' . $categoria_id . '_' . $nueva_foto_id . '.' . $extension;
            $existe = Storage::disk('public')->exists($source_file);
            if ($existe) {
                $destination_path = $path_folder . $agencia_destino . '/' . $año . '/' . $nuevo_nombre;
                $existe_destino = Storage::disk('public')->exists($destination_path);
                if ($existe_destino) {
                    Storage::disk('public')->delete($destination_path);
                }
                Storage::disk('public')->move($source_file, $destination_path);
            }
            AlbumFoto::on($conexion_destino)->where('id', $nueva_foto_id)
                ->update(['imagen' => $nuevo_nombre]);
        }
    }

    public function eliminar_datos_origen($datos_transferencia)
    {
        $cliente_id = $datos_transferencia->cliente_id;
        $agencia_origen = $datos_transferencia->agencia_origen;
        $conexion_origen = 'master_' . $agencia_origen;
        $conexion_record_origen = 'records_' . $agencia_origen;

        $creditos = Credito::on($conexion_origen)->select('id')->where('cliente_id', $cliente_id)->get();

        foreach ($creditos as $item) {
            $credito_id = $item->id;

            // ELIMINAR CREDITO_CUOTAS--------------------------
            Cuota::on($conexion_origen)->where('credito_id', $credito_id)->delete();

            // NOTIFICACIONES
            $notificaciones = Notificacion::on($conexion_origen)->where('credito_id', $credito_id)->get();
            foreach ($notificaciones as $item_1) {
                // ELIMINAR CAJA_PAGO_NOTIFICACIONES--------------------------
                PagoNotificacion::on($conexion_origen)->where('notificacion_id', $item_1->id)->delete();
                // ELIMINAR CREDITO_NOTIFICACIONES--------------------------
                Notificacion::on($conexion_origen)->where('id', $item_1->id)->delete();
            }

            // ELIMINAR CREDITO_COMPROMISOS--------------------------
            Compromiso::on($conexion_origen)->where('credito_id', $credito_id)->delete();

            // ELIMINAR CAJA_PAGO_CUOTAS--------------------------
            PagoCuota::on($conexion_origen)->where('credito_id', $credito_id)->delete();

            // ELIMINAR CAJA_PAGO_MORAS--------------------------
            PagoMora::on($conexion_origen)->where('credito_id', $credito_id)->delete();

            //ELIMINAR CREDITO_CARRITO_DETALLES ---------------------
            $detalles_carrito = CarritoDetalle::on($conexion_origen)->where('credito_id', $credito_id)->get();
            foreach ($detalles_carrito as $item_1) {
                CarritoDetalleAUD::on($conexion_origen)->where('registro_id', $item_1->id)->delete();
                CarritoDetalle::on($conexion_origen)->where('id', $item_1->id)->delete();
            }

            // ELIMINAR CAJA_PAGO_VOUCHERS--------------------------
            PagoVoucher::on($conexion_origen)->where('credito_id', $credito_id)->delete();

            // DESEMBOLSOS
            $desembolsos = Desembolso::on($conexion_origen)->where('credito_id', $credito_id)->get();
            foreach ($desembolsos as $item_1) {
                // ELIMINAR CAJA_COMISIONES_PAGOS--------------------------
                ComisionPago::on($conexion_origen)->where('desembolso_id', $item_1->id)->delete();
                // ELIMINAR CREDITO_FACTURADOS--------------------------
                Facturado::on($conexion_origen)->where('desembolso_id', $item_1->id)->delete();
                // ELIMINAR CAJA_DESEMBOLSOS--------------------------
                Desembolso::on($conexion_origen)->where('id', $item_1->id)->delete();
            }

            // CRÉDITOS
            $aprobacion = Credito::on($conexion_origen)->select('aprobacion_id')->where('id', $credito_id)->get()->last();
            $aprobacion_id = $aprobacion->id;

            // ELIMINAR CREDITO_APROBACIONES--------------------------
            Aprobacion::on($conexion_origen)->where('id', $aprobacion_id)->delete();
        }

        // ELIMINAR CREDITO_REGISTROS_RECORDS--------------------------
        CreditoRegistroRecord::on($conexion_record_origen)->whereIn('credito_id', $creditos)->delete();

        // ELIMINAR CREDITO_REGISTROS--------------------------
        Credito::on($conexion_origen)->whereIn('id', $creditos)->delete();

        // ELIMINAR CREDITO_PROPUESTAS_APROBACION--------------------------
        $propuestas = Propuesta::on($conexion_origen)->select('id')->where('cliente_id', $cliente_id)->get();
        $aprobaciones = Aprobacion::on($conexion_origen)->select('id')->whereIn('propuesta_id', $propuestas)->get();
        Aprobacion::on($conexion_origen)->whereIn('id', $aprobaciones)->delete();
        Propuesta::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_COMENTARIOS--------------------------
        Comentario::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_VISITAS--------------------------
        Visita::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_NEGOCIOS--------------------------
        Negocio::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_PARIENTES--------------------------
        Pariente::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_AVALES--------------------------
        Aval::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_PRENDAS--------------------------
        Prenda::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // EVALUACIONES--------------------------
        $evaluaciones = Evaluacion_financiera::on($conexion_origen)->where('cliente_id', $cliente_id)->get();
        foreach ($evaluaciones as $item) {
            $evaluacion_id = $item->id;
            // EF_ACTIVO_CORRIENTE--------------------------
            Ef_activo_corriente::on($conexion_origen)->where('evaluacion_id', $evaluacion_id)->delete();
            // EF_ACTIVO_NO_CORRIENTE--------------------------
            Ef_activo_no_corriente::on($conexion_origen)->where('evaluacion_id', $evaluacion_id)->delete();
            // EF_COMENTARIOS--------------------------
            Ef_comentarios::on($conexion_origen)->where('evaluacion_id', $evaluacion_id)->delete();
            // EF_FLUJO_CAJA--------------------------
            Ef_flujo_caja::on($conexion_origen)->where('evaluacion_id', $evaluacion_id)->delete();
            // EF_PASIVO_CORRIENTE--------------------------
            Ef_pasivo_corriente::on($conexion_origen)->where('evaluacion_id', $evaluacion_id)->delete();
            // EF_REGISTROS--------------------------
            Evaluacion_financiera::on($conexion_origen)->where('id', $evaluacion_id)->delete();
        }

        // ELIMINAR CLIENTE_ALBUM_FOTOS--------------------------
        AlbumFoto::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // INVERSION META--------------------------
        $inversiones_meta = InversionMeta::on($conexion_origen)->select('id')->where('cliente_id', $cliente_id)->get();
        InversionMetaMovimiento::on($conexion_origen)->whereIn('inversion_id', $inversiones_meta)->delete();
        InversionMeta::on($conexion_origen)->where('cliente_id', $cliente_id)->delete();

        // ELIMINAR CLIENTE_REGISTROS-------------------------
        Cliente::on($conexion_origen)->where('id', $cliente_id)->delete();
    }
}
