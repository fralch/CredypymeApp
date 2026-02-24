<?php

use Illuminate\Support\Facades\Route;
//--------CRÉDITOS------------------------------------------------------------------
use App\Http\Controllers\Creditos\CreditosController;
// ----------------CLIENTES--------------------------------------------
use App\Http\Controllers\Creditos\Clientes\ClientesController;
use App\Http\Controllers\Creditos\Clientes\GrupoController;
use App\Http\Controllers\Creditos\Clientes\ParienteController;
use App\Http\Controllers\Creditos\Clientes\AvalController;
use App\Http\Controllers\Creditos\Clientes\NegocioController;
use App\Http\Controllers\Creditos\Clientes\ClienteHistorialCrediticioController;
use App\Http\Controllers\Creditos\Clientes\ClientePrendasController;
use App\Http\Controllers\Creditos\Clientes\AlbumFotosController;
use App\Http\Controllers\Creditos\Clientes\ClienteMovimientoController;
use App\Http\Controllers\Creditos\Clientes\ClienteTransferenciaController;
// ----------------CRÉDITOS--------------------------------------------
use App\Http\Controllers\Creditos\Credito\EvaluacionFinancieraController;
use App\Http\Controllers\Creditos\Caja\DesembolsoController;
use App\Http\Controllers\Creditos\Credito\PropuestaController;
use App\Http\Controllers\Creditos\Credito\AprobacionController;
use App\Http\Controllers\Creditos\Credito\CreditoDocumentoFinancieroController;
use App\Http\Controllers\Creditos\Credito\CronogramaController;
use App\Http\Controllers\Creditos\Credito\CreditoPagoVoucherController;
use App\Http\Controllers\Creditos\Credito\CarritoController;
use App\Http\Controllers\Creditos\Credito\CarritoSeguimientoController;
// --------------------GRUPAL--------------------------------------------
use App\Http\Controllers\Creditos\Grupal\SolicitudController;

//-----------------------------INVERSIONES-------------------------------
use App\Http\Controllers\Creditos\Inversion\InversionController;
use App\Http\Controllers\Creditos\Inversion\InversionMetaController;

// ----------------CAJA--------------------------------------------
use App\Http\Controllers\Creditos\Caja\CajaController;
use App\Http\Controllers\Creditos\Caja\CajaTransaccionController;
use App\Http\Controllers\Creditos\Caja\CajaAdelantoHaberController;
use App\Http\Controllers\Creditos\Caja\CajaTransferenciaController;
use App\Http\Controllers\Creditos\Caja\CajaOperacionDiaController;
use App\Http\Controllers\Creditos\Caja\FacturacionController;
use App\Http\Controllers\Creditos\Caja\CajaCobranzaController;
use App\Http\Controllers\Creditos\Caja\CajaPagoCuotaController;
use App\Http\Controllers\Creditos\Caja\CajaPagoMoraController;
use App\Http\Controllers\Creditos\Caja\CajaPagoNotificacionController;
use App\Http\Controllers\Creditos\Caja\CajaFaltantesController;
use App\Http\Controllers\Creditos\Caja\CajaCierreDiaController;
use App\Http\Controllers\Creditos\Caja\CajaCierreController;
use App\Http\Controllers\Creditos\Caja\CajaTransferenciaPagoController;
use App\Http\Controllers\Creditos\Caja\CarritoCobranzaController;

// ----------------CUENTA--------------------------------------------
use App\Http\Controllers\Creditos\Cuenta\CuentaController;
use App\Http\Controllers\Creditos\Cuenta\CuentaMovimientoController;
use App\Http\Controllers\Creditos\Cuenta\CuentaTransferenciaController;
use App\Http\Controllers\Creditos\Cuenta\CuentaEnvioController;
use App\Http\Controllers\Creditos\Cuenta\CuentaBancariaController;

// ----------------HERRAMIENTAS--------------------------------------------
use App\Http\Controllers\Creditos\Herramientas\SimuladorCreditosController;
use App\Http\Controllers\Creditos\Herramientas\SimuladorRiesgoController;
use App\Http\Controllers\Creditos\Herramientas\CambioAgenciaController;
use App\Http\Controllers\Creditos\Herramientas\IndicadorObjetivoController;
use App\Http\Controllers\Creditos\Herramientas\MensajeriaController;
// -----------------REPORTES-------------------------
use App\Http\Controllers\Creditos\Reportes\ReporteAdelantoController;
use App\Http\Controllers\Creditos\Reportes\ReporteCobranzasController;
use App\Http\Controllers\Creditos\Reportes\ReporteCobranzasTipoController;
use App\Http\Controllers\Creditos\Reportes\ReporteClientesController;
use App\Http\Controllers\Creditos\Reportes\ReporteDesembolsoController;
use App\Http\Controllers\Creditos\Reportes\ReporteDiasMoraController;
use App\Http\Controllers\Creditos\Reportes\ReporteControlMoraController;
use App\Http\Controllers\Creditos\Reportes\ReporteTransaccionesController;
use App\Http\Controllers\Creditos\Reportes\ReporteCompromisoNotificacionesController;
use App\Http\Controllers\Creditos\Reportes\ReporteEquifaxController;
use App\Http\Controllers\Creditos\Reportes\ReporteCanceladosController;
use App\Http\Controllers\Creditos\Reportes\ReporteMoraController;
use App\Http\Controllers\Creditos\Reportes\ReporteMoraAgenciaController;
use App\Http\Controllers\Creditos\Reportes\ReporteCajaController;
use App\Http\Controllers\Creditos\Reportes\ReporteOperacionesController;
use App\Http\Controllers\Creditos\Reportes\ReporteEnvioController;
use App\Http\Controllers\Creditos\Reportes\ReporteFacturacionController;
use App\Http\Controllers\Creditos\Reportes\ReporteAprobacionController;
use App\Http\Controllers\Creditos\Reportes\ReporteProductividadController;
use App\Http\Controllers\Creditos\Reportes\ReporteInversionMetaController;
use App\Http\Controllers\Creditos\Reportes\ReporteClienteMovimientoController;
use App\Http\Controllers\Creditos\Reportes\ReporteClienteTransferenciaController;
use App\Http\Controllers\Creditos\Reportes\ReporteBancoController;

// ----------------MANTEMINIENTOS--------------------------------------------
use App\Http\Controllers\Creditos\Mantenimiento\TransaccionCategoriaController;
use App\Http\Controllers\Creditos\Mantenimiento\TransaccionSubcategoriaController;
use App\Http\Controllers\Creditos\Mantenimiento\TransaccionComprobanteController;
use App\Http\Controllers\Creditos\Mantenimiento\Credito\CreditoSectorController;
use App\Http\Controllers\Creditos\Mantenimiento\Credito\CreditoProductoController;
use App\Http\Controllers\Creditos\Mantenimiento\Credito\CreditoSubproductoController;
use App\Http\Controllers\Creditos\Mantenimiento\Credito\CreditoTipoController;
use App\Http\Controllers\Creditos\Mantenimiento\Credito\CreditoGarantiaController;
use App\Http\Controllers\Creditos\Mantenimiento\Credito\CreditoEstadoController;
use App\Http\Controllers\Creditos\Mantenimiento\FacturacionLimiteController;
use App\Http\Controllers\Creditos\Mantenimiento\ProductosMetaController;

// ----------------EXTERNA--------------------------------------------
use App\Http\Controllers\Creditos\Caja\CajaCobranzaExternaController;
use App\Http\Controllers\Creditos\Inversion\InversionMetaExternaController;

use Illuminate\Http\Request;
//--------SISTEMA-CRÉDITOS------------------------------------------------------------------
Route::POST('/fecha_hora_agencia/{agencia_id}', [CreditosController::class, 'fecha_larga_aplicacion'])
    ->name('fecha_hora_agencia');

Route::GET('/index', [CreditosController::class, 'index'])->name('cre.index');

//-------------------CLIENTES-------------------------------------------------------
Route::GET('/cli/listado_registro', [ClientesController::class, 'listado_registro'])
    ->name('cli.listado_registro');
Route::POST('/cli/listado_registro/verificar', [ClientesController::class, 'verificar_cliente'])
    ->name('cli.listado_registro.verificar');
Route::POST('/cli/listado_registro/buscar', [ClientesController::class, 'buscar_clientes'])
    ->name('cli.listado_registro.buscar');
Route::POST('/cli/listado_registro/guardar', [ClientesController::class, 'guardar_cliente'])
    ->name('cli.listado_registro.guardar');
Route::POST('/cli/listado_registro/parientes_avales_negocios/{cliente_id}/{agencia_id}', [ClientesController::class, 'parientes_avales_negocios'])
    ->name('cli.listado_registro.parientes_avales_negocios');
Route::POST('/cli/listado_registro/listar_parientes/{cliente_id}/{agencia_id}', [ParienteController::class, 'listar_parientes'])
    ->name('cli.listado_registro.listar_parientes');
Route::POST('/cli/listado_registro/asignar_pariente', [ParienteController::class, 'asignar_pariente'])
    ->name('cli.listado_registro.asignar_pariente');
Route::POST('/cli/listado_registro/vincular_desvincular_p', [ParienteController::class, 'vincular_desvincular'])
    ->name('cli.listado_registro.vincular_desvincular_p');
Route::POST('/cli/listado_registro/listar_avales/{cliente_id}/{agencia_id}', [AvalController::class, 'listar_avales'])
    ->name('cli.listado_registro.listar_avales');
Route::POST('/cli/listado_registro/asignar_aval', [AvalController::class, 'asignar_aval'])
    ->name('cli.listado_registro.asignar_aval');
Route::POST('/cli/listado_registro/vincular_desvincular_a', [AvalController::class, 'vincular_desvincular'])
    ->name('cli.listado_registro.vincular_desvincular_a');
Route::POST('/cli/listado_registro/listar_negocios/{cliente_id}/{agencia_id}', [NegocioController::class, 'listar_negocios'])
    ->name('cli.listado_registro.listar_negocios');
Route::POST('/cli/listado_registro/asignar_negocio', [NegocioController::class, 'asignar_negocio'])
    ->name('cli.listado_registro.asignar_negocio');
Route::POST('/cli/listado_registro/vincular_negocio', [NegocioController::class, 'vincular_negocio'])
    ->name('cli.listado_registro.vincular_negocio');
Route::POST('/cli/listado_registro/buscar_ciiu', [NegocioController::class, 'buscar_ciiu'])
    ->name('cli.listado_registro.buscar_ciiu');
Route::POST('/cli/listado_registro/listar_parientes_dep/{cliente_id}/{agencia_id}', [ParienteController::class, 'listar_parientes_dependientes'])
    ->name('cli.listado_registro.listar_parientes_dep');
Route::POST('/cli/listado_registro/listar_avales_dep/{cliente_id}/{agencia_id}', [AvalController::class, 'listar_avales_dependientes'])
    ->name('cli.listado_registro.listar_avales_dep');
Route::POST('/cli/listado_registro/eliminar', [ClientesController::class, 'eliminar'])
    ->name('cli.listado_registro.eliminar');
Route::GET('/cli/listado_registro/cantidad_creditos/{agencia_id},{cliente_id}', [ClientesController::class, 'cantidad_creditos'])
    ->name('cli.listado_registro.cantidad_creditos');

Route::GET('cli/gru/listar_datos', [GrupoController::class, 'listar_datos'])
    ->name('cli.gru.listar_datos');
Route::GET('cli/gru/listar_grupos', [GrupoController::class, 'listar_grupos'])
    ->name('cli.gru.listar_grupos');
Route::GET('cli/gru/buscar_clientes', [GrupoController::class, 'buscar_clientes'])
    ->name('cli.gru.buscar_clientes');
Route::GET('cli/gru/listar_grupo_clientes', [GrupoController::class, 'listar_grupo_clientes'])
    ->name('cli.gru.listar_grupo_clientes');
Route::GET('cli/gru/verificar', [GrupoController::class, 'verificar'])
    ->name('cli.gru.verificar');
Route::POST('cli/gru/guardar', [GrupoController::class, 'guardar'])
    ->name('cli.gru.guardar');

Route::GET('/cli/album_fotos/{cliente_id}/{agencia_id}', [AlbumFotosController::class, 'album_fotos'])
    ->name('cli.album_fotos');
Route::POST('/cli/album_fotos/guardar_foto', [AlbumFotosController::class, 'guardar_foto'])
    ->name('cli.album_fotos.guardar_foto');
Route::POST('/cli/album_fotos/eliminar', [AlbumFotosController::class, 'eliminar'])
    ->name('cli.album_fotos.eliminar');

Route::GET('/cli/historial_crediticio/{cliente_id}/{agencia_id}', [ClienteHistorialCrediticioController::class, 'historial_crediticio'])
    ->name('cli.historial_crediticio');
Route::POST('/cli/historial_crediticio/listar_detalle', [ClienteHistorialCrediticioController::class, 'listar_detalle'])
    ->name('cli.historial_crediticio.listar_detalle');
Route::POST('/cli/historial_crediticio/calificar', [ClienteHistorialCrediticioController::class, 'calificar'])
    ->name('cli.historial_crediticio.calificar');
Route::POST('/cli/historial_crediticio_modal', [ClienteHistorialCrediticioController::class, 'historial_crediticio_modal'])
    ->name('cli.historial_crediticio_modal');

Route::GET('/cli/prendas/{cliente_id}/{agencia_id}', [ClientePrendasController::class, 'prendas'])
    ->name('cli.prendas');
Route::POST('/cli/prendas/guardar', [ClientePrendasController::class, 'guardar'])
    ->name('cli.prendas.guardar');
Route::POST('/cli/prendas/eliminar', [ClientePrendasController::class, 'eliminar'])
    ->name('cli.prendas.eliminar');
Route::POST('/cli/prendas/subir_acta', [ClientePrendasController::class, 'subir_acta'])
    ->name('cli.prendas.subir_acta');

Route::GET('/cli/movimiento', [ClienteMovimientoController::class, 'movimiento'])
    ->name('cli.movimiento');
Route::POST('/cli/movimiento/listar_clientes', [ClienteMovimientoController::class, 'listar_clientes'])
    ->name('cli.movimiento.listar_clientes');
Route::POST('/cli/movimiento/mover', [ClienteMovimientoController::class, 'mover'])
    ->name('cli.movimiento.mover');

Route::GET('/cli/transferencia', [ClienteTransferenciaController::class, 'transferencia'])
    ->name('cli.transferencia');
Route::POST('/cli/transferencia/listar_clientes', [ClienteTransferenciaController::class, 'listar_clientes'])
    ->name('cli.transferencia.listar_clientes');
Route::POST('/cli/transferencia/transferir', [ClienteTransferenciaController::class, 'transferir'])
    ->name('cli.transferencia.transferir');

//-------------------CRÉDITO---------------------------------------------------------
Route::GET('/cre/evaluacion_financiera/{cliente_id}/{agencia_id}', [EvaluacionFinancieraController::class, 'evaluacion_financiera'])
    ->name('cre.evaluacion_financiera');
Route::POST('/cre/evaluacion/activo_corriente/guardar', [EvaluacionFinancieraController::class, 'guardar_activo_corriente'])
    ->name('cre.evaluacion.activo_corriente.guardar');
Route::POST('/cre/evaluacion/activo_no_corriente/guardar', [EvaluacionFinancieraController::class, 'guardar_activo_no_corriente'])
    ->name('cre.evaluacion.activo_no_corriente.guardar');
Route::POST('/cre/evaluacion/pasivo_corriente/guardar', [EvaluacionFinancieraController::class, 'guardar_pasivo_corriente'])
    ->name('cre.evaluacion.pasivo_corriente.guardar');
Route::POST('/cre/evaluacion/flujo_caja/guardar', [EvaluacionFinancieraController::class, 'guardar_flujo_caja'])
    ->name('cre.evaluacion.flujo_caja.guardar');
Route::POST('/cre/evaluacion/comentarios/guardar', [EvaluacionFinancieraController::class, 'guardar_comentarios'])
    ->name('cre.evaluacion.comentarios.guardar');
Route::POST('/cre/evaluacion/convenio/guardar', [EvaluacionFinancieraController::class, 'guardar_convenio'])
    ->name('cre.evaluacion.convenio.guardar');
Route::POST('/cre/evaluacion/exportar', [EvaluacionFinancieraController::class, 'exportar'])
    ->name('cre.evaluacion.exportar');

Route::GET('/cre/propuesta/{cliente_id}/{propuesta_id?}/{agencia_id}', [PropuestaController::class, 'propuesta'])
    ->name('cre.propuesta');
Route::POST('/cre/propuesta/guardar', [PropuestaController::class, 'guardar'])
    ->name('cre.propuesta.guardar');
Route::POST('/cre/propuesta/exportar', [PropuestaController::class, 'exportar'])
    ->name('cre.propuesta.exportar');
Route::POST('/cre/propuesta/listar', [PropuestaController::class, 'listar'])
    ->name('cre.propuesta.listar');

Route::POST('/cre/propuesta/guardar_telefonos', [PropuestaController::class, 'guardar_telefonos'])
    ->name('cre.propuesta.guardar_telefonos');


Route::POST('/cre/calcular_cronograma/sin_redondeo', [CreditosController::class, 'cronograma_sin_redondeo'])
    ->name('cre.calcular_cronograma.sin_redondeo');
Route::POST('/cre/calcular_cronograma/con_redondeo', [CreditosController::class, 'cronograma_con_redondeo'])
    ->name('cre.calcular_cronograma.con_redondeo');

Route::GET('/cre/aprobacion/{propuesta_id}/{aprobacion_id?}/{agencia_id}', [AprobacionController::class, 'aprobacion'])
    ->name('cre.aprobacion');
Route::POST('/cre/aprobacion/guardar', [AprobacionController::class, 'guardar'])
    ->name('cre.aprobacion.guardar');
Route::POST('/cre/aprobacion/exportar', [AprobacionController::class, 'exportar'])
    ->name('cre.aprobacion.exportar');

Route::POST('/cre/aprobacion/riesgo_crediticio', [AprobacionController::class, 'riesgo_crediticio'])
    ->name('cre.riesgo_crediticio');
Route::POST('/cre/aprobacion/comisiones_previas', [AprobacionController::class, 'comisiones_previas'])
    ->name('cre.comisiones_previas');
Route::POST('/cre/aprobacion/actualizar_comision', [AprobacionController::class, 'actualizar_comision'])
    ->name('cre.actualizar_comision');

Route::POST('/cre/aprobacion/listar', [AprobacionController::class, 'listar'])
    ->name('cre.aprobacion.listar');
Route::POST('/cre/aprobacion/listar_detallado', [AprobacionController::class, 'listar_detallado'])
    ->name('cre.aprobacion.listar_detallado');
Route::POST('/cre/aprobacion/actualizar_comision', [AprobacionController::class, 'actualizar_comision'])
    ->name('cre.aprobacion.actualizar_comision');

Route::GET('/cre/documentos_financieros', [CreditoDocumentoFinancieroController::class, 'documentos_financieros'])
    ->name('cre.documentos_financieros');
Route::POST('/cre/documentos_financieros/generar', [CreditoDocumentoFinancieroController::class, 'generar'])
    ->name('cre.documentos_financieros.generar');

Route::POST('/cre/anular_aprobacion', [AprobacionController::class, 'anular_aprobacion'])
    ->name('cre.anular_aprobacion');
Route::POST('/cre/anular_aprobacion_todos', [AprobacionController::class, 'anular_aprobacion_todos'])
    ->name('cre.anular_aprobacion_todos');

Route::GET('/cre/cronograma/{cliente_id}/{credito_id}/{agencia_id}', [CronogramaController::class, 'copia_cronograma'])
    ->name('cre.copia_cronograma');
Route::GET('/cre/copia_cronograma/verificar_caja', [CronogramaController::class, 'verificar_caja'])
    ->name('cre.copia_cronograma.verificar_caja');
Route::POST('/cre/copia_cronograma/cambiar_modo', [CronogramaController::class, 'cambiar_modo'])
    ->name('cre.copia_cronograma.cambiar_modo');

Route::GET('/cre/copia_voucher/{cliente_id}/{agencia_id}', [CreditoPagoVoucherController::class, 'pago_voucher'])
    ->name('cre.copia_voucher');
Route::POST('/cre/copia_voucher/listar_detalle', [CreditoPagoVoucherController::class, 'listar_detalle'])
    ->name('cre.copia_voucher.listar_detalle');


Route::GET('/cre/carrito', [CarritoController::class, 'carrito_cobranza'])
    ->name('cre.carrito');
Route::GET('/cre/carrito/listar', [CarritoController::class, 'listar'])
    ->name('cre.carrito.listar');
Route::POST('/cre/carrito/aperturar', [CarritoController::class, 'aperturar'])
    ->name('cre.carrito.aperturar');
Route::POST('/cre/carrito/cerrar_carrito', [CarritoController::class, 'cerrar_carrito'])
    ->name('cre.carrito.cerrar_carrito');
Route::GET('/cre/carrito/buscar', [CarritoController::class, 'buscar'])
    ->name('cre.carrito.buscar');
Route::GET('/cre/carrito/agregar', [CarritoController::class, 'agregar'])
    ->name('cre.carrito.agregar');
Route::GET('/cre/carrito/detalle', [CarritoController::class, 'detalle'])
    ->name('cre.carrito.detalle');
Route::GET('/cre/carrito/verificar', [CarritoController::class, 'verificar'])
    ->name('cre.carrito.verificar');
Route::POST('/cre/carrito/pagar', [CarritoController::class, 'pagar'])
    ->name('cre.carrito.pagar');
Route::GET('/cre/carrito/verificar_tiempo', [CarritoController::class, 'verificar_tiempo'])
    ->name('cre.carrito.verificar_tiempo');
Route::POST('/cre/carrito/anular', [CarritoController::class, 'anular'])
    ->name('cre.carrito.anular');
Route::POST('/cre/carrito/actualizar_telefono', [CarritoController::class, 'actualizar_telefono'])
    ->name('cre.carrito.actualizar_telefono');
Route::POST('/cre/carrito/verificar_carritos/{agencia_id}', [CarritoController::class, 'verificar_carritos'])
    ->name('cre.carrito.verificar_carritos');

Route::GET('/cre/carrito_seguimiento', [CarritoSeguimientoController::class, 'carrito_seguimiento'])
    ->name('cre.carrito_seguimiento');
Route::GET('/cre/carrito_seguimiento/buscar', [CarritoSeguimientoController::class, 'carrito_seguimiento_buscar'])
    ->name('cre.carrito_seguimiento.buscar');
Route::GET('/cre/carrito_seguimiento/listar_sin_voucher', [CarritoSeguimientoController::class, 'listar_sin_voucher'])
    ->name('cre.carrito_seguimiento.listar_sin_voucher');
Route::GET('/cre/carrito_seguimiento/detalle', [CarritoSeguimientoController::class, 'carrito_seguimiento_detalle'])
    ->name('cre.carrito_seguimiento.detalle');
Route::GET('/cre/carrito_seguimiento/modificacion', [CarritoSeguimientoController::class, 'carrito_seguimiento_modificacion'])
    ->name('cre.carrito_seguimiento.modificacion');
Route::GET('/cre/carrito_seguimiento/anulacion', [CarritoSeguimientoController::class, 'carrito_seguimiento_anulacion'])
    ->name('cre.carrito_seguimiento.anulacion');
Route::POST('/cre/carrito_seguimiento/comprobante', [CarritoSeguimientoController::class, 'carrito_seguimiento_comprobante'])
    ->name('cre.carrito_seguimiento.comprobante');

//-------------------CRÉDITO---------------------------------------------------------
Route::GET('/gru/buscar_grupos', [GrupoController::class, 'buscar_grupos'])
    ->name('gru.buscar_grupos');

Route::GET('/gru/solicitud', [SolicitudController::class, 'index'])
    ->name('gru.solicitud');
Route::GET('/gru/solicitud/listar_datos', [SolicitudController::class, 'listar_datos'])
    ->name('gru.solicitud.listar_datos');




//------------------------------INVERSION----------------------------

Route::GET('/inv/crear_inversion_meta/{cliente_id}/{agencia_id}', [InversionMetaController::class, 'crear'])
    ->name('inv.meta.crear');
Route::POST('/inv/crear_inversion_meta/registrar', [InversionMetaController::class, 'registrar'])
    ->name('inv.meta.registrar');


Route::POST('/inv/listar', [InversionController::class, 'listar'])
    ->name('inv.listar');
Route::POST('/inv/meta/voucher', [InversionMetaController::class, 'voucher'])
    ->name('inv.meta.voucher');

Route::GET('/inv/meta/{agencia_id}/{inversion_id}', [InversionMetaController::class, 'inversion_meta'])
    ->name('inv.meta');
Route::GET('/inv/meta/listar_recursos', [InversionMetaController::class, 'listar_recursos'])
    ->name('inv.meta.listar_recursos');
Route::POST('/inv/meta/abonar_retirar', [InversionMetaController::class, 'abonar_retirar'])
    ->name('inv.meta.abonar_retirar');
Route::POST('/inv/meta/cerrar', [InversionMetaController::class, 'cerrar'])
    ->name('inv.meta.cerrar');


// -----------------------------CAJA --------------------------------

Route::GET('/caj/apertura_caja', [CajaController::class, 'apertura_caja'])
    ->name('caj.apertura_caja');
Route::POST('/caj/apertura_caja/aperturar', [CajaController::class, 'aperturar'])
    ->name('caj.apertura_caja.aperturar');

Route::GET('/caj/desembolso/{aprobacion_id}/{agencia_id}', [DesembolsoController::class, 'desembolso'])
    ->name('caj.desembolso');
Route::POST('/caj/desembolso/guardar', [DesembolsoController::class, 'guardar'])
    ->name('caj.desembolso.guardar');
Route::POST('/caj/desembolso/facturar', [FacturacionController::class, 'facturar'])
    ->name('caj.desembolso.facturar');
Route::POST('/caj/desembolso/voucher', [DesembolsoController::class, 'generar_voucher'])
    ->name('caj.desembolso.voucher');
Route::POST('/caj/desembolso/cronograma', [DesembolsoController::class, 'imprimir_cronograma'])
    ->name('caj.desembolso.cronograma');

Route::POST('/caj/desembolso/facturar_todos', [FacturacionController::class, 'facturar_todos'])
    ->name('caj.desembolso.facturar_todos');


Route::POST('/facturar_diario', [FacturacionController::class, 'facturar_diario'])
    ->name('facturar_diario');
Route::POST('/caj/desembolso/listar', [DesembolsoController::class, 'listar'])
    ->name('caj.desembolso.listar');
Route::POST('/caj/desembolso/buscar', [DesembolsoController::class, 'buscar'])
    ->name('caj.desembolso.buscar');

Route::GET('/caj/cobranza/{credito_id}/{agencia_id}', [CajaCobranzaController::class, 'cobranza'])
    ->name('caj.cobranza');
Route::GET('/caj/cobranza/listar_recursos', [CajaCobranzaController::class, 'listar_recursos'])
    ->name('caj.cobranza.listar_recursos');
Route::POST('/caj/cobranza/verificar_recibo', [CajaCobranzaController::class, 'verificar_recibo'])
    ->name('caj.cobranza.verificar_recibo');
Route::POST('/caj/cobranza/pagar', [CajaCobranzaController::class, 'pagar'])
    ->name('caj.cobranza.pagar');
Route::POST('/caj/cobranza/cancelar', [CajaCobranzaController::class, 'cancelar'])
    ->name('caj.cobranza.cancelar');
Route::POST('/caj/cobranza/voucher', [CreditoPagoVoucherController::class, 'generar_voucher'])
    ->name('caj.cobranza.voucher');
Route::GET('/caj/cobranza/verificar_carrito', [CajaCobranzaController::class, 'verificar_carrito'])
    ->name('caj.cobranza.verificar_carrito');

Route::POST('/caj/pago_cuotas/listar', [CajaPagoCuotaController::class, 'listar_pagos'])
    ->name('caj.pago_cuotas.listar');
Route::POST('/caj/pago_moras/listar', [CajaPagoMoraController::class, 'listar_pagos'])
    ->name('caj.pago_moras.listar');
Route::POST('/caj/pago_notificaciones/listar', [CajaPagoNotificacionController::class, 'listar_pagos'])
    ->name('caj.pago_notificaciones.listar');

Route::GET('/caj/transferencia/{tipo?}', [CajaTransferenciaController::class, 'transferencia'])
    ->name('caj.transferencia');
Route::POST('/caj/transferencia/registrar', [CajaTransferenciaController::class, 'registrar'])
    ->name('caj.transferencia.registrar');

Route::GET('/caj/mis_transferencias', [CajaTransferenciaController::class, 'mis_transferencias'])
    ->name('caj.mis_transferencias');
Route::POST('/caj/mis_transferencias/confirmar', [CajaTransferenciaController::class, 'confirmar'])
    ->name('caj.mis_transferencias.confirmar');

Route::POST('/caj/clave_transaccion/generar', [CajaController::class, 'generar_clave'])
    ->name('caj.clave_transaccion.generar');
Route::POST('/caj/clave_transaccion/verificar/{clave}', [CajaController::class, 'verificar_clave'])
    ->name('caj.clave_transaccion.verificar');

Route::GET('/caj/adelanto_haberes/', [CajaAdelantoHaberController::class, 'adelanto_haberes'])
    ->name('caj.adelanto_haberes');
Route::GET('/caj/adelanto_haberes/listar_recursos', [CajaAdelantoHaberController::class, 'listar_recursos'])
    ->name('caj.adelanto_haberes.listar_recursos');
Route::GET('/caj/adelanto_haberes/revisar', [CajaAdelantoHaberController::class, 'revisar'])
    ->name('caj.adelanto_haberes.revisar');
Route::POST('/caj/adelanto_haberes/registrar', [CajaAdelantoHaberController::class, 'registrar'])
    ->name('caj.adelanto_haberes.registrar');
Route::POST('/caj/adelanto_haberes/exportar', [CajaAdelantoHaberController::class, 'exportar'])
    ->name('caj.adelanto_haberes.exportar');

Route::GET('/caj/desembolsos_facturados', [ReporteFacturacionController::class, 'desembolsos_facturados'])
    ->name('caj.desembolsos_facturados');
Route::POST('/caj/desembolsos_facturados/buscar', [ReporteFacturacionController::class, 'buscar_facturados'])
    ->name('caj.desembolsos_facturados.buscar');
Route::POST('/caj/desembolsos_facturados/descargar_xml', [ReporteFacturacionController::class, 'descargar_xml'])
    ->name('caj.desembolsos_facturados.descargar_xml');
Route::POST('/caj/desembolsos_facturados/exportar', [ReporteFacturacionController::class, 'exportar_facturados'])
    ->name('caj.desembolsos_facturados.exportar');

Route::GET('/caj/transaccion/{transaccion_id?}', [CajaTransaccionController::class, 'transaccion'])
    ->name('caj.transaccion');
Route::POST('/caj/transaccion/registrar', [CajaTransaccionController::class, 'registrar'])
    ->name('caj.transaccion.registrar');

Route::GET('/caj/operaciones_dia', [CajaOperacionDiaController::class, 'operaciones_dia'])
    ->name('caj.operaciones_dia');
Route::POST('/caj/operaciones_dia/listar', [CajaOperacionDiaController::class, 'listar'])
    ->name('caj.operaciones_dia.listar');
Route::POST('/caj/operaciones_dia/exportar', [CajaOperacionDiaController::class, 'exportar'])
    ->name('caj.operaciones_dia.exportar');

Route::GET('/caj/cierre_caja', [CajaController::class, 'cierre_caja'])
    ->name('caj.cierre_caja');
Route::POST('/caj/cierre_caja/guardar_billeteo', [CajaController::class, 'guardar_billeteo'])
    ->name('caj.guardar_billeteo');
Route::POST('/caj/cierre_caja/operaciones_caja/{caja_id}/{agencia_id}', [CajaOperacionDiaController::class, 'listar_operaciones'])
    ->name('caj.operaciones_caja');
Route::POST('/caj/cierre_caja/operaciones_agencia/{caja_id}/{agencia_id}/{id_cierre}', [CajaOperacionDiaController::class, 'listar_operaciones_agencia'])
    ->name('caj.operaciones_agencia');
Route::POST('/caj/cierre_caja/cerrar', [CajaController::class, 'cerrar'])
    ->name('caj.cierre_caja.cerrar');
Route::POST('/caj/cierre_caja/declarar', [CajaController::class, 'declarar'])
    ->name('caj.cierre_caja.declarar');
Route::POST('/caj/cierre_caja/declaracion_jurada', [CajaController::class, 'declaracion_jurada'])
    ->name('caj.cierre_caja.declaracion_jurada');
Route::POST('/caj/cierre_caja/imprimir', [CajaCierreController::class, 'imprimir'])
    ->name('caj.cierre_caja.imprimir');

Route::GET('/cre/caj/revision_faltantes', [CajaFaltantesController::class, 'revision_faltantes'])
    ->name('cre.caj.revision_faltantes');
Route::POST('/cre/caj/revision_faltantes/buscar', [CajaFaltantesController::class, 'revision_faltantes_buscar'])
    ->name('cre.caj.revision_faltantes.buscar');
Route::POST('/cre/caj/revision_faltantes/guardar', [CajaFaltantesController::class, 'revision_faltantes_guardar'])
    ->name('cre.caj.revision_faltantes.guardar');
Route::POST('/cre/caj/revision_faltantes/exportar', [CajaFaltantesController::class, 'revision_faltantes_exportar'])
    ->name('cre.caj.revision_faltantes.exportar');

Route::GET('/caj/cierre_dia', [CreditosController::class, 'cierre_dia'])
    ->name('caj.cierre_dia');
Route::POST('/caj/cierre_dia/verificar/{agencia_id}', [CajaCierreDiaController::class, 'verificar_cierre_dia'])
    ->name('caj.cierre_dia.verificar');
Route::POST('/caj/cierre_dia/anular_aprobaciones/{agencia_id}', [CajaCierreDiaController::class, 'anular_aprobaciones'])
    ->name('caj.cierre_dia.anular_aprobaciones');
Route::POST('/caj/cierre_dia/cerrar/{agencia_id}', [CajaCierreDiaController::class, 'cerrar_dia'])
    ->name('caj.cierre_dia.cerrar');


Route::GET('/caj/envio_pagos', [CajaTransferenciaPagoController::class, 'envioPagos'])
    ->name('caj.envio_pagos');
Route::POST('/caj/envio_pagos/registrar', [CajaTransferenciaPagoController::class, 'registrar_envio'])
    ->name('caj.envio_pagos.registrar');

Route::GET('/caj/recepcion_pagos', [CajaTransferenciaPagoController::class, 'recepcionPagos'])
    ->name('caj.recepcion_pagos');
Route::POST('/caj/recepcion_pagos/registrar', [CajaTransferenciaPagoController::class, 'registrar_recepcion'])
    ->name('caj.recepcion_pagos.registrar');

Route::GET('/caj/listar_transferencia_pagos', [CajaTransferenciaPagoController::class, 'listarTransferenciaPagos'])
    ->name('caj.listar_transferencia_pagos');
Route::POST('/caj/listar_transferencia_pagos_get', [CajaTransferenciaPagoController::class, 'listarTransferenciasPagosGet'])
    ->name('caj.listar_transferencia_pagos_get');


Route::GET('/caj/carrito_cobranzas', [CarritoCobranzaController::class, 'carrito_cobranzas'])
    ->name('caj.carrito_cobranzas');
Route::GET('/caj/carrito_cobranzas/buscar', [CarritoCobranzaController::class, 'carrito_cobranzas_buscar'])
    ->name('caj.carrito_cobranzas.buscar');
Route::GET('/caj/carrito_cobranzas/creditos', [CarritoCobranzaController::class, 'carrito_cobranzas_creditos'])
    ->name('caj.carrito_cobranzas.creditos');
Route::POST('/caj/carrito_cobranzas/pagar', [CarritoCobranzaController::class, 'carrito_cobranzas_pagar'])
    ->name('caj.carrito_cobranzas.pagar');
Route::POST('/caj/carrito_cobranzas/voucher', [CarritoCobranzaController::class, 'carrito_cobranzas_voucher'])
    ->name('caj.carrito_cobranzas.voucher');

//-------------------CUENTA---------------------------------------------------------
Route::GET('/cue/usuarios_cuenta', [CuentaController::class, 'usuarios_cuenta'])
    ->name('cue.usuarios_cuenta');
Route::GET('/cue/usuarios_cuenta/listar', [CuentaController::class, 'listar_usuarios'])
    ->name('cue.usuarios_cuenta.listar');
Route::POST('/cue/usuarios_cuenta/habilitar', [CuentaController::class, 'habilitar_deshabilitar'])
    ->name('cue.usuarios_cuenta.habilitar');

Route::GET('/cue/mi_cuenta', [CuentaController::class, 'mi_cuenta'])
    ->name('cue.mi_cuenta');

Route::GET('/cue/mostrar_cuentas', [CuentaController::class, 'mostrar_cuentas'])
    ->name('cue.mostrar_cuentas');
Route::POST('/cue/mostrar_cuentas/listar', [CuentaController::class, 'listar_cuentas'])
    ->name('cue.mostrar_cuentas.listar');
Route::POST('/cue/ultimos_movimientos/{cuenta_id}/{agencia_id}', [CuentaMovimientoController::class, 'ultimos_movimientos'])
    ->name('cue.ultimos_movimientos');
Route::POST('/cue/listar_movimientos', [CuentaMovimientoController::class, 'listar_movimientos'])
    ->name('cue.listar_movimientos');
Route::POST('/cue/movimientos/exportar', [CuentaMovimientoController::class, 'exportar'])
    ->name('cue.movimientos.exportar');

Route::GET('/cue/cuentas_bancarias', [CuentaBancariaController::class, 'index'])
    ->name('cue.cuentas_bancarias');
Route::GET('/cue/cuentas_bancarias/{agencia_id}/listar', [CuentaBancariaController::class, 'listar'])
    ->name('cue.cuentas_bancarias.listar');
Route::GET('/cue/cuentas_bancarias/{banco_id}/movimientos_agrupado', [CuentaBancariaController::class, 'movimientos_agrupado'])
    ->name('cue.cuentas_bancarias.movimientos_agrupado');
Route::GET('/cue/cuentas_bancarias/{banco_id}/movimientos_detalle', [CuentaBancariaController::class, 'movimientos_detalle'])
    ->name('cue.cuentas_bancarias.movimientos_detalle');
Route::POST('/cue/cuentas_bancarias/registrar_movimiento', [CuentaBancariaController::class, 'registrar_movimiento'])
    ->name('cue.cuentas_bancarias.registrar_movimiento');

Route::GET('/cue/transferencia/{tipo}', [CuentaTransferenciaController::class, 'transferencia'])
    ->name('cue.transferencia');
Route::POST('/cue/transferencia/registrar', [CuentaTransferenciaController::class, 'registrar'])
    ->name('cue.transferencia.registrar');


Route::GET('/cue/mis_transferencias', [CuentaTransferenciaController::class, 'mis_transferencias'])
    ->name('cue.mis_transferencias');
Route::POST('/cue/mis_transferencias/confirmar', [CuentaTransferenciaController::class, 'confirmar'])
    ->name('cue.mis_transferencias.confirmar');

Route::GET('/cue/envio', [CuentaEnvioController::class, 'envio'])
    ->name('cue.envio');
Route::POST('/cue/envio/listar_cuentas/{agencia_id}', [CuentaEnvioController::class, 'listar_cuentas'])
    ->name('cue.envio.listar_cuentas');
Route::POST('/cue/envio/registrar', [CuentaEnvioController::class, 'registrar'])
    ->name('cue.envio.registrar');

Route::GET('/cue/mis_envios', [CuentaEnvioController::class, 'mis_envios'])
    ->name('cue.mis_envios');
Route::POST('/cue/mis_envios/confirmar', [CuentaEnvioController::class, 'confirmar'])
    ->name('cue.mis_envios.confirmar');
Route::POST('/cue/mis_envios/imprimir', [CuentaEnvioController::class, 'imprimir'])
    ->name('cue.mis_envios.imprimir');

//--------------- CREDITOS HERRAMIENTAS - ---------------------
Route::GET('/her/simulador_creditos', [SimuladorCreditosController::class, 'simuladorCreditos'])
    ->name('her.simulador_creditos');
Route::POST('/her/simulador_riesgo/rangos', [SimuladorCreditosController::class, 'rangos'])
    ->name('her.simulador_riesgo.rangos');

Route::GET('/her/facturacion_dia', [FacturacionLimiteController::class, 'facturacion_dia'])
    ->name('her.facturacion_dia');
Route::POST('/her/facturacion_dia/calcular', [FacturacionLimiteController::class, 'calcular_diario'])
    ->name('her.facturacion_dia.calcular');

Route::GET('/her/cambio_agencia', [CambioAgenciaController::class, 'cambio_agencia'])
    ->name('her.cambio_agencia');
Route::POST('/her/cambio_agencia/guardar', [CambioAgenciaController::class, 'guardar'])
    ->name('her.cambio_agencia.guardar');

Route::GET('/her/indicador_objetivo/{modo}', [IndicadorObjetivoController::class, 'indicador_objetivo'])
    ->name('her.indicador_objetivo');
Route::POST('/her/indicador_objetivo/listar_recursos', [IndicadorObjetivoController::class, 'listar_recursos'])
    ->name('her.indicador_objetivo.listar_recursos');
Route::POST('/her/indicador_objetivo/procesar', [IndicadorObjetivoController::class, 'procesar'])
    ->name('her.indicador_objetivo.procesar');

Route::GET('/her/mensajeria', [MensajeriaController::class, 'mensajeria'])
    ->name('her.mensajeria');
Route::GET('/her/mensajeria/listar_creditos/{agencia_id}', [MensajeriaController::class, 'listar_creditos'])
    ->name('her.mensajeria.listar_creditos');
Route::POST('/her/mensajeria/enviar', [MensajeriaController::class, 'enviar'])
    ->name('her.mensajeria.enviar');

// --------------- REPORTES -----------------
// ---------------CAJA-----------------

Route::GET('/reportes/caja/adelanto_haberes', [ReporteAdelantoController::class, 'adelanto_haberes'])->name('rep.caj.adelanto_haberes');
Route::POST('/reportes/caja/adelanto_haberes/buscar', [ReporteAdelantoController::class, 'buscar'])->name('rep.caj.adelanto_haberes.buscar');
Route::POST('/reportes/caja/adelanto_haberes/exportar', [ReporteAdelantoController::class, 'exportar'])->name('rep.caj.adelanto_haberes.exportar');
Route::POST('/reportes/caja/adelanto_haberes/exportar_agrupado', [ReporteAdelantoController::class, 'exportarAgrupado'])->name('rep.caj.adelanto_haberes.exportar_agrupado');

Route::GET('/reportes/caja/desembolsos_auxiliar/{modo}', [ReporteDesembolsoController::class, 'desembolsos_auxiliar'])
    ->name('rep.caj.desembolsos_auxiliar');
Route::POST('/reportes/caja/desembolsos_auxiliar/buscar', [ReporteDesembolsoController::class, 'buscar'])
    ->name('rep.caj.desembolsos_auxiliar.buscar');
Route::GET('/reportes/caja/desembolsos_auxiliar/{cliente_id}/seguimiento', [ReporteDesembolsoController::class, 'seguimiento'])
    ->name('rep.caj.desembolsos_auxiliar.seguimiento');
Route::POST('/reportes/caja/desembolsos_auxiliar/exportar', [ReporteDesembolsoController::class, 'exportar'])
    ->name('rep.caj.desembolsos_auxiliar.exportar');

Route::GET('/reportes/caja/cobranzas_auxiliar/{modo}', [ReporteCobranzasController::class, 'cobranzas_auxiliar'])
    ->name('rep.caj.cobranzas_auxiliar');
Route::GET('/reportes/caja/cobranzas_auxiliar/{modo}/listar_recursos', [ReporteCobranzasController::class, 'listar_recursos'])
    ->name('rep.caj.cobranzas_auxiliar.listar_recursos');
Route::GET('/reportes/caja/cobranzas_auxiliar/{modo}/buscar', [ReporteCobranzasController::class, 'buscar'])
    ->name('rep.caj.cobranzas_auxiliar.buscar');
Route::POST('/reportes/caja/cobranzas_auxiliar/exportar', [ReporteCobranzasController::class, 'exportar'])
    ->name('rep.caj.cobranzas_auxiliar.exportar');

Route::GET('/reportes/caja/descuentos_creditos', [ReporteCobranzasController::class, 'descuentos_creditos'])
    ->name('rep.caj.descuentos_creditos');
Route::POST('/reportes/caja/descuentos_creditos/buscar', [ReporteCobranzasController::class, 'descuentos_creditos_buscar'])
    ->name('rep.caj.descuentos_creditos.buscar');
Route::POST('/reportes/caja/descuentos_creditos/exportar', [ReporteCobranzasController::class, 'exportar_descuento'])
    ->name('rep.caj.descuentos_creditos.exportar');

Route::GET('/reportes/caja/cobranzas_tipo', [ReporteCobranzasTipoController::class, 'cobranzas_tipo'])
    ->name('rep.caj.cobranzas_tipo');
Route::GET('/reportes/caja/cobranzas_tipo/listar_recursos', [ReporteCobranzasTipoController::class, 'listar_recursos'])
    ->name('rep.caj.cobranzas_tipo.listar_recursos');
Route::GET('/reportes/caja/cobranzas_tipo/buscar', [ReporteCobranzasTipoController::class, 'buscar'])
    ->name('rep.caj.cobranzas_tipo.buscar');
Route::POST('/reportes/caja/cobranzas_tipo/exportar', [ReporteCobranzasTipoController::class, 'exportar'])
    ->name('rep.caj.cobranzas_tipo.exportar');

Route::GET('/reportes/caja/transacciones/{modo}', [ReporteTransaccionesController::class, 'transacciones'])
    ->name('rep.caj.transacciones');
Route::POST('/reportes/caja/transacciones/buscar', [ReporteTransaccionesController::class, 'buscar'])
    ->name('rep.caj.transacciones.buscar');
Route::POST('/reportes/caja/transacciones/exportar', [ReporteTransaccionesController::class, 'exportar_transacciones'])
    ->name('rep.caj.transacciones.exportar');
Route::POST('/reportes/caja/transacciones/guardar', [ReporteTransaccionesController::class, 'guardar_transacciones'])
    ->name('rep.caj.transacciones.guardar');

Route::GET('/reportes/caja/cobranzas_negocio', [ReporteCobranzasController::class, 'cobranzas_negocio'])
    ->name('rep.caj.cobranzas_negocio');
Route::POST('/reportes/caja/cobranzas_negocio/{modo}/buscar', [ReporteCobranzasController::class, 'buscar'])
    ->name('rep.caj.cobranzas_negocio.buscar');
Route::POST('/reportes/caja/cobranzas_negocio/exportar', [ReporteCobranzasController::class, 'exportar'])
    ->name('rep.caj.cobranzas_negocio.exportar');

Route::GET('/reportes/caja/caja_cierres', [ReporteCajaController::class, 'caja_cierres'])
    ->name('rep.caj.caja_cierres');
Route::POST('/reportes/caja/caja_cierres/buscar', [ReporteCajaController::class, 'buscar_cierres_caja'])
    ->name('rep.caj.caja_cierres.buscar');
Route::POST('/reportes/caja/caja_cierres/exportar', [ReporteCajaController::class, 'exportar_cierres_caja'])
    ->name('rep.caj.caja_cierres.exportar');

Route::GET('/reportes/caja/cierres_dia', [ReporteCajaController::class, 'cierres_dia'])
    ->name('rep.caj.cierres_dia');
Route::POST('/reportes/caja/cierres_dia/buscar', [ReporteCajaController::class, 'buscar_cierres_dia'])
    ->name('rep.caj.cierres_dia.buscar');
Route::POST('/reportes/caja/cierres_dia/exportar', [ReporteCajaController::class, 'exportar_cierres'])
    ->name('rep.caj.cierres_dia.exportar');

Route::GET('/reportes/caja/operaciones_mes', [ReporteOperacionesController::class, 'operaciones_mes'])
    ->name('rep.caj.operaciones_mes');
Route::POST('/reportes/caja/operaciones_mes/buscar', [ReporteOperacionesController::class, 'buscar_operaciones_mes'])
    ->name('rep.caj.operaciones_mes.buscar');
Route::POST('/reportes/caja/operaciones_mes/exportar', [ReporteOperacionesController::class, 'exportar_operaciones_mes'])
    ->name('rep.caj.operaciones_mes.exportar');


Route::GET('/reportes/caja/transferencias', [ReporteCajaController::class, 'transferencias'])
    ->name('rep.caj.transferencias');
Route::POST('/reportes/caja/transferencias/buscar', [ReporteCajaController::class, 'buscar_transferencias'])
    ->name('rep.caj.transferencias.buscar');
Route::POST('/reportes/caja/transferencias/exportar', [ReporteCajaController::class, 'exportar_transferencias'])
    ->name('rep.caj.transferencias.exportar');

Route::GET('/reportes/caja/envios_agencias', [ReporteEnvioController::class, 'envios_agencias'])
    ->name('rep.caj.envios_agencias');
Route::POST('/reportes/caja/envios_agencias/buscar', [ReporteEnvioController::class, 'buscar_envios_agencias'])
    ->name('rep.caj.envios_agencias.buscar');
Route::POST('/reportes/caja/envios_agencias/exportar', [ReporteEnvioController::class, 'exportar_envios_agencias'])
    ->name('rep.caj.envios_agencias.exportar');

Route::GET('/reportes/caja/seguimiento_facturados', [ReporteFacturacionController::class, 'seguimiento_facturados'])
    ->name('rep.caj.seguimiento_facturados');
Route::POST('/reportes/caja/seguimiento_facturados/buscar', [ReporteFacturacionController::class, 'buscar_pagos'])
    ->name('rep.caj.seguimiento_facturados.buscar');
Route::POST('/reportes/caja/seguimiento_facturados/exportar', [ReporteFacturacionController::class, 'exportar_pagos'])
    ->name('rep.caj.seguimiento_facturados.exportar');

Route::GET('/reportes/caja/movimientos_bancarios', [ReporteBancoController::class, 'movimientos_bancarios'])
    ->name('rep.caj.movimientos_bancarios');
Route::GET('/reportes/caja/movimientos_bancarios/listar_recursos', [ReporteBancoController::class, 'listar_recursos'])
    ->name('rep.caj.movimientos_bancarios.listar_recursos');
Route::GET('/reportes/caja/movimientos_bancarios/buscar_general', [ReporteBancoController::class, 'buscar_general'])
    ->name('rep.caj.movimientos_bancarios.buscar_general');
Route::GET('/reportes/caja/movimientos_bancarios/buscar', [ReporteBancoController::class, 'buscar'])
    ->name('rep.caj.movimientos_bancarios.buscar');
Route::POST('/reportes/caja/movimientos_bancarios/exportar', [ReporteBancoController::class, 'exportar'])
    ->name('rep.caj.movimientos_bancarios.exportar');


// ---------------CLIENTES-----------------
Route::GET('/reportes/clientes/cumpleanios/{modo}', [ReporteClientesController::class, 'cumpleanios'])
    ->name('rep.cli.cumpleanios');
Route::POST('/reportes/clientes/cumpleanios/buscar', [ReporteClientesController::class, 'buscar'])
    ->name('rep.cli.cumpleanios.buscar');
Route::POST('/reportes/clientes/cumpleanios/comentar', [ReporteClientesController::class, 'comentar'])
    ->name('rep.cli.cumpleanios.comentar');
Route::POST('/reportes/clientes/cumpleanios/comentarios', [ReporteClientesController::class, 'comentarios'])
    ->name('rep.cli.cumpleanios.comentarios');
Route::POST('/reportes/clientes/cumpleanios/exportar', [ReporteClientesController::class, 'exportar_clientes_cumpleanios'])
    ->name('rep.cli.cumpleanios.exportar');

Route::GET('/reportes/clientes/clientes_activos/{modo}', [ReporteClientesController::class, 'clientes_activos'])
    ->name('rep.cli.clientes_activos');
Route::POST('/reportes/clientes/clientes_activos/buscar', [ReporteClientesController::class, 'buscar'])
    ->name('rep.cli.clientes_activos.buscar');
Route::POST('/reportes/clientes/clientes_activos/exportar', [ReporteClientesController::class, 'exportar_clientes_activos'])
    ->name('rep.cli.clientes_activos.exportar');
Route::POST('/reportes/clientes/clientes_activos/lista_Comentarios', [ReporteClientesController::class, 'comentar_activos'])
    ->name('rep.cli.clientes_activos.comentar');
Route::POST('/reportes/clientes/clientes_activos/comentar', [ReporteClientesController::class, 'listar_comentarios_activos'])
    ->name('rep.cli.clientes_activos.listar_comentarios');

Route::GET('/reportes/clientes/clientes_inactivos/{modo}', [ReporteClientesController::class, 'clientes_inactivos'])
    ->name('rep.cli.clientes_inactivos');
Route::POST('/reportes/clientes/clientes_inactivos/buscar', [ReporteClientesController::class, 'buscar'])
    ->name('rep.cli.clientes_inactivos.buscar');
Route::POST('/reportes/clientes/clientes_inactivos/exportar', [ReporteClientesController::class, 'exportar_clientes_inactivos'])
    ->name('rep.cli.clientes_inactivos.exportar');
Route::POST('/reportes/clientes/clientes_inactivos/lista_Comentarios', [ReporteClientesController::class, 'comentar_inactivos'])
    ->name('rep.cli.clientes_inactivos.comentar');
Route::POST('/reportes/clientes/clientes_inactivos/comentar', [ReporteClientesController::class, 'listar_comentarios_inactivos'])
    ->name('rep.cli.clientes_inactivos.listar_comentarios');

Route::GET('/reportes/clientes/pago_hoy/{modo}', [ReporteClientesController::class, 'pago_hoy'])
    ->name('rep.cli.pago_hoy');
Route::POST('/reportes/clientes/pago_hoy/buscar', [ReporteClientesController::class, 'buscar'])
    ->name('rep.cli.pago_hoy.buscar');
Route::POST('/reportes/clientes/pago_hoy/exportar', [ReporteClientesController::class, 'exportar'])
    ->name('rep.cli.pago_hoy.exportar');

Route::GET('/reportes/clientes/clientes_por_asesor/{modo}', [ReporteClientesController::class, 'clientes_por_asesor'])
    ->name('rep.cli.clientes_por_asesor');
Route::POST('/reportes/clientes/clientes_por_asesor/buscar', [ReporteClientesController::class, 'buscar'])
    ->name('rep.cli.clientes_por_asesor.buscar');
Route::POST('/reportes/clientes/clientes_por_asesor/exportar', [ReporteClientesController::class, 'exportar_clientes_asesor'])
    ->name('rep.cli.clientes_asesor.exportar');

Route::GET('/reportes/clientes/movimientos', [ReporteClienteMovimientoController::class, 'movimientos'])
    ->name('rep.cli.movimientos');
Route::POST('/reportes/clientes/movimientos/buscar', [ReporteClienteMovimientoController::class, 'buscar'])
    ->name('rep.cli.movimientos.buscar');
Route::POST('/reportes/clientes/movimientos/exportar', [ReporteClienteMovimientoController::class, 'exportar'])
    ->name('rep.cli.movimientos.exportar');

Route::GET('/reportes/clientes/transferencias', [ReporteClienteTransferenciaController::class, 'transferencias'])
    ->name('rep.cli.transferencias');
Route::POST('/reportes/clientes/transferencias/buscar', [ReporteClienteTransferenciaController::class, 'buscar'])
    ->name('rep.cli.transferencias.buscar');
Route::POST('/reportes/clientes/transferencias/exportar', [ReporteClienteTransferenciaController::class, 'exportar'])
    ->name('rep.cli.transferencias.exportar');

// ---------------CRÉDITOS-----------------


Route::GET('/reportes/creditos/desembolsos_asesor/{modo}', [ReporteDesembolsoController::class, 'desembolsos_asesor'])
    ->name('rep.cre.desembolsos_asesor');
Route::POST('/reportes/creditos/desembolsos_asesor/buscar', [ReporteDesembolsoController::class, 'buscar'])
    ->name('rep.cre.desembolsos_asesor.buscar');
Route::POST('/reportes/creditos/desembolsos_asesor/exportar', [ReporteDesembolsoController::class, 'exportar'])
    ->name('rep.cre.desembolsos_asesor.exportar');

Route::GET('/reportes/creditos/dias_mora/{modo}', [ReporteDiasMoraController::class, 'dias_mora'])
    ->name('rep.cre.dias_mora');
Route::POST('/reportes/creditos/dias_mora/listar', [ReporteDiasMoraController::class, 'listar'])
    ->name('rep.cre.dias_mora.listar');
Route::POST('/reportes/creditos/dias_mora/detalle/{credito_id}/{agencia_id}', [ReporteDiasMoraController::class, 'detalle'])
    ->name('rep.cre.dias_mora.detalle');
Route::POST('/reportes/creditos/dias_mora/asignar_compromiso', [ReporteDiasMoraController::class, 'asignar_compromiso'])
    ->name('rep.cre.dias_mora.asignar_compromiso');
Route::POST('/reportes/creditos/dias_mora/aplicar_notificacion', [ReporteDiasMoraController::class, 'aplicar_notificacion'])
    ->name('rep.cre.dias_mora.aplicar_notificacion');
Route::POST('/reportes/creditos/dias_mora/envio_notificacion', [ReporteDiasMoraController::class, 'envio_notificacion'])
    ->name('rep.cre.dias_mora.envio_notificacion');
Route::POST('/reportes/creditos/dias_mora/exportar', [ReporteDiasMoraController::class, 'exportar'])
    ->name('rep.cre.dias_mora.exportar');

Route::GET('/reportes/creditos/lugar_cobranza/{modo}', [ReporteCobranzasController::class, 'lugar_cobranza'])
    ->name('rep.cre.lugar_cobranza');
Route::POST('/reportes/creditos/lugar_cobranza/{modo}/buscar', [ReporteCobranzasController::class, 'buscar'])
    ->name('rep.cre.lugar_cobranza.buscar');
Route::POST('/reportes/caja/lugar_cobranza/exportar', [ReporteCobranzasController::class, 'exportar'])
    ->name('rep.cre.lugar_cobranza.exportar');

Route::GET('/reportes/creditos/control_mora/{modo}', [ReporteControlMoraController::class, 'control_mora'])
    ->name('rep.cre.control_mora');
Route::POST('/reportes/creditos/control_mora/buscar', [ReporteControlMoraController::class, 'buscar'])
    ->name('rep.cre.control_mora.buscar');
Route::POST('/reportes/creditos/control_mora/exportar', [ReporteControlMoraController::class, 'exportar'])
    ->name('rep.cre.control_mora.exportar');

Route::GET('/reportes/creditos/mora_agencia/listar_recursos', [ReporteMoraAgenciaController::class, 'listar_recursos'])
    ->name('rep.cre.mora_agencia.listar_recursos');
Route::POST('/reportes/creditos/mora_agencia/buscar', [ReporteMoraAgenciaController::class, 'buscar'])
    ->name('rep.cre.mora_agencia.buscar');
Route::POST('/reportes/creditos/mora_agencia/exportar', [ReporteMoraAgenciaController::class, 'exportar'])
    ->name('rep.cre.mora_agencia.exportar');

Route::POST('/reportes/creditos/mora_agencia/buscar_mensual', [ReporteMoraAgenciaController::class, 'buscar_mensual'])
    ->name('rep.cre.mora_agencia.buscar_mensual');
Route::GET('/reportes/creditos/mora_agencia/fecha_ultimo_mes', [ReporteMoraAgenciaController::class, 'fecha_ultimo_mes'])
    ->name('rep.cre.mora_agencia.fecha_ultimo_mes');
Route::POST('/reportes/creditos/mora_agencia/exportar_mensual', [ReporteMoraAgenciaController::class, 'exportar_mensual'])
    ->name('rep.cre.mora_agencia.exportar_mensual');


Route::GET('/reportes/creditos/compromisos/notificaciones/{modo}', [ReporteCompromisoNotificacionesController::class, 'compromisosNotificaciones'])
    ->name('rep.cre.compromisos_notificaciones');
Route::POST('/reportes/creditos/compromisos/notificaciones/buscar', [ReporteCompromisoNotificacionesController::class, 'buscar'])
    ->name('rep.cre.compromisos_notificaciones.buscar');
Route::POST('/reportes/creditos/compromisos/notificaciones/exportar', [ReporteCompromisoNotificacionesController::class, 'exportar_notificaciones'])
    ->name('rep.cre.compromisos_notificaciones.exportar');

Route::GET('/reportes/creditos/informe_equifax/{modo}', [ReporteEquifaxController::class, 'informe_equifax'])
    ->name('rep.cre.informe_equifax');
Route::POST('/reportes/creditos/informe_equifax/buscar', [ReporteEquifaxController::class, 'buscar'])
    ->name('rep.cre.informe_equifax.buscar');
Route::POST('/reportes/creditos/informe_equifax/exportar', [ReporteEquifaxController::class, 'exportar'])
    ->name('rep.cre.informe_equifax.exportar');

Route::GET('/reportes/creditos/productividad_asesor/{modo}', [ReporteProductividadController::class, 'productividad_asesor'])
    ->name('rep.cre.productividad_asesor');
Route::POST('/reportes/creditos/productividad_asesor/buscar', [ReporteProductividadController::class, 'buscar'])
    ->name('rep.cre.productividad_asesor.buscar');
Route::POST('/reportes/creditos/productividad_asesor/exportar', [ReporteProductividadController::class, 'exportar'])
    ->name('rep.cre.productividad_asesor.exportar');

Route::GET('/reportes/creditos/cancelados_parcial', [ReporteCanceladosController::class, 'cancelados_parcial'])->name('rep.cre.cancelados_parcial');
Route::POST('/reportes/creditos/cancelados_parcial/buscar', [ReporteCanceladosController::class, 'buscar_creditos_cancelados'])->name('rep.cre.cancelados_parcial.buscar');
Route::POST('/reportes/creditos/cancelados_parcial/exportar', [ReporteCanceladosController::class, 'exportar_cancelados'])->name('rep.cre.cancelados_parcial.exportar');

Route::GET('/reportes/creditos/mora_asesor/{modo}', [ReporteMoraController::class, 'mora_asesor'])
    ->name('rep.cre.mora_asesor');
Route::POST('/reportes/creditos/mora_asesor/buscar', [ReporteMoraController::class, 'buscar'])
    ->name('rep.cre.mora_asesor.buscar');
Route::POST('/reportes/creditos/mora_asesor/exportar', [ReporteMoraController::class, 'exportar'])
    ->name('rep.cre.mora_asesor.exportar');

Route::GET('/reportes/creditos/desembosos_tipo', [ReporteDesembolsoController::class, 'desembosos_tipo'])
    ->name('rep.cre.desembosos_tipo');
Route::post('/reportes/creditos/desembosos_tipo/buscar', [ReporteDesembolsoController::class, 'desembolsos_tipo_buscar'])
    ->name('rep.cre.desembosos_tipo.buscar');
Route::post('/reportes/creditos/desembosos_tipo/exportar', [ReporteDesembolsoController::class, 'exportar_tipo_credito'])
    ->name('rep.cre.desembosos_tipo.exportar');


Route::GET('/reportes/creditos/aprobaciones_anuladas', [ReporteAprobacionController::class, 'aprobaciones_anuladas'])->name('rep.cre.aprobaciones_anuladas');
Route::post('/reportes/creditos/aprobaciones_anuladas/buscar', [ReporteAprobacionController::class, 'obtener_anulaciones'])->name('rep.cre.aprobaciones_anuladas.buscar');
Route::post('/reportes/creditos/aprobaciones_anuladas/exportar', [ReporteAprobacionController::class, 'exportar_anulados'])->name('rep.cre.aprobaciones_anuladas.exportar_anulados');

// ---------------INVERSIONES-----------------
Route::GET('/reportes/inversiones/movimientos_meta', [ReporteInversionMetaController::class, 'movimientos_meta'])
    ->name('rep.inv.movimientos_meta');
Route::POST('/reportes/inversiones/movimientos_meta/buscar', [ReporteInversionMetaController::class, 'buscar'])
    ->name('rep.inv.movimientos_meta.buscar');
Route::POST('/reportes/inversiones/movimientos_meta/exportar', [ReporteInversionMetaController::class, 'exportar'])
    ->name('rep.inv.movimientos_meta.exportar');

Route::GET('/reportes/inversiones/avance_meta/{modo}', [ReporteInversionMetaController::class, 'avance_meta'])
    ->name('rep.inv.avance_meta');

Route::POST('/reportes/inversiones/avance_meta/buscar', [ReporteInversionMetaController::class, 'buscar_avance'])
    ->name('rep.inv.avance_meta.buscar');

Route::POST('/reportes/inversiones/avance_meta/exportar', [ReporteInversionMetaController::class, 'exportar_avance'])
    ->name('rep.inv.avance_meta.exportar');

Route::GET('/reportes/contabilidad/transacciones_reales', [ReporteTransaccionesController::class, 'transacciones_reales'])
    ->name('rep.con.transacciones_reales');

Route::POST('/reportes/contabilidad/transacciones_reales/buscar', [ReporteTransaccionesController::class, 'buscar_transacciones_reales'])
    ->name('rep.con.transacciones_reales.buscar');

Route::POST('/reportes/contabilidad/transacciones_reales/enviar', [ReporteTransaccionesController::class, 'enviar_transacciones_reales'])
    ->name('rep.con.transacciones_reales.enviar');


// -----------------------------MANTENIMIENTO --------------------------------
Route::GET('/man/transaccion/categorias', [TransaccionCategoriaController::class, 'categorias'])
    ->name('man.tra.categorias');
Route::GET('/man/transaccion/categoria/listar', [TransaccionCategoriaController::class, 'listar'])
    ->name('man.tra.categorias.listar');
Route::GET('/man/transaccion/categoria/verificar', [TransaccionCategoriaController::class, 'verificar'])
    ->name('man.tra.categorias.verificar');
Route::POST('/man/transaccion/categoria/guardar', [TransaccionCategoriaController::class, 'guardar'])
    ->name('man.tra.categorias.guardar');

Route::GET('/mant/transaccion-subcategoria', [TransaccionSubcategoriaController::class, 'subcategorias'])
    ->name('mant.transaccion_subcategoria');
Route::POST('/mant/transaccion-subcategoria/validar', [TransaccionSubcategoriaController::class, 'validarExiste'])
    ->name('mant.subcategoria.validar_existe');
Route::POST('/mant/transaccion-subcategoria/listar_subcategoria', [TransaccionSubcategoriaController::class, 'listarSubcategorias'])
    ->name('mant.subcategoria.listar_subcategoria');
Route::POST('/mant/transaccion-subcategoria/guardar', [TransaccionSubcategoriaController::class, 'guardarSubcategoria'])
    ->name('mant.subcategoria.guardar');

Route::GET('/mant/transaccion-comprobante', [TransaccionComprobanteController::class, 'comprobantes'])
    ->name('mant.transaccion_comprobantes');
Route::POST('/mant/transaccion-comprobante/listar_comprobantes', [TransaccionComprobanteController::class, 'listarComprobantes'])
    ->name('mant.comprobantes.listar_comprobantes');
Route::POST('/mant/transaccion-comprobante/validar', [TransaccionComprobanteController::class, 'validarExiste'])
    ->name('mant.comprobantes.validar_existe');
Route::POST('/mant/transaccion-comprobante/guardar', [TransaccionComprobanteController::class, 'guardarComprobante'])
    ->name('mant.comprobantes.guardar');

Route::GET('/man/credito_sectores/{agencia_id?}', [CreditoSectorController::class, 'sectores'])
    ->name('man.cre_sectores');
Route::POST('/man/credito_sectores/listar', [CreditoSectorController::class, 'listar'])
    ->name('man.cre_sectores.listar');
Route::POST('/man/credito_sectores/verificar', [CreditoSectorController::class, 'verificar'])
    ->name('man.cre_sectores.verificar');
Route::POST('/man/credito_sectores/guardar', [CreditoSectorController::class, 'guardar'])
    ->name('man.cre_sectores.guardar');

Route::GET('/man/credito_productos', [CreditoProductoController::class, 'productos'])->name('man.cre_productos');
Route::POST('/man/credito_productos/listar_productos', [CreditoProductoController::class, 'listarProductos'])->name('man.cre_productos.listar_productos');
Route::POST('/man/credito_productos/verificar', [CreditoProductoController::class, 'verificar'])->name('man.cre_productos.verificar');
Route::POST('/man/credito_productos/guardar', [CreditoProductoController::class, 'guardar'])->name('man.cre_productos.guardar');

Route::GET('/man/credito_subproductos', [CreditoSubproductoController::class, 'subproductos'])->name('man.cre_subproductos');
Route::POST('/man/credito_subproductos/listar_subproductos', [CreditoSubproductoController::class, 'listarSubproductos'])->name('man.cre_subproductos.listar_subproductos');
Route::POST('/man/credito_subproductos/verificar', [CreditoSubproductoController::class, 'verificar'])->name('man.cre_subproductos.verificar');
Route::POST('/man/credito_subproductos/guardar', [CreditoSubproductoController::class, 'guardar'])->name('man.cre_subproductos.guardar');

Route::GET('/man/credito_tipos', [CreditoTipoController::class, 'tipos'])->name('man.cre_tipos');
Route::POST('/man/credito_tipos/listar', [CreditoTipoController::class, 'listarSubproductos'])->name('man.cre_tipos.lista');
Route::POST('/man/credito_tipos/verificar', [CreditoTipoController::class, 'verificar'])->name('man.cre_tipos.verificar');
Route::POST('/man/credito_tipos/guardar', [CreditoTipoController::class, 'guardar'])->name('man.cre_tipos.guardar');

Route::GET('/man/credito_garantias', [CreditoGarantiaController::class, 'garantias'])->name('man.cre_garantias');
Route::POST('/man/credito_garantias/listar', [CreditoGarantiaController::class, 'listarGarantias'])->name('man.cre_garantias.listar');
Route::POST('/man/credito_garantias/verificar', [CreditoGarantiaController::class, 'verificar'])->name('man.cre_garantias.verificar');
Route::POST('/man/credito_garantias/guardar', [CreditoGarantiaController::class, 'guardar'])->name('man.cre_garantias.guardar');

Route::GET('/man/credito_estados', [CreditoEstadoController::class, 'estados'])->name('man.cre_estados');
Route::POST('/man/credito_estados/listar', [CreditoEstadoController::class, 'listarEstados'])->name('man.cre_estados.listar');
Route::POST('/man/credito_estados/verificar', [CreditoEstadoController::class, 'verificar'])->name('man.cre_estados.verificar');
Route::POST('/man/credito_estados/guardar', [CreditoEstadoController::class, 'guardar'])->name('man.cre_estados.guardar');

Route::GET('/man/album_categorias', [AlbumFotosController::class, 'album_categorias'])->name('man.cli_album_categorias');
Route::POST('/man/album_categorias/verificar', [AlbumFotosController::class, 'verificar'])->name('man.cli_album_categorias.verificar');
Route::POST('/man/album_categorias/guardar', [AlbumFotosController::class, 'guardar'])->name('man.cli_album_categorias.guardar');

Route::GET('/man/facturacion/limites', [FacturacionLimiteController::class, 'limites'])
    ->name('man.fac_limites');
Route::POST('/man/facturacion/limites/verificar', [FacturacionLimiteController::class, 'verificar'])
    ->name('man.fac_limites.verificar');
Route::POST('/man/facturacion/limites/guardar', [FacturacionLimiteController::class, 'guardar'])
    ->name('man.fac_limites.guardar');

Route::GET('/man/inversion/productos_meta/{agencia_id?}', [ProductosMetaController::class, 'productos_meta'])
    ->name('man.inversion.productos_meta');
Route::POST('/man/inversion/productos_meta/listar', [ProductosMetaController::class, 'listar'])
    ->name('man.inversion.productos_meta.listar');
Route::POST('/man/inversion/productos_meta/verificar', [ProductosMetaController::class, 'verificar'])
    ->name('man.inversion.productos_meta.verificar');
Route::POST('/man/inversion/productos_meta/guardar', [ProductosMetaController::class, 'guardar'])
    ->name('man.inversion.productos_meta.guardar');

// Rutas como herramientas adicionales

Route::POST('/eliminar_archivo', [CreditosController::class, 'eliminar_archivo'])
    ->name('eliminar_archivo');


// RUTAS PARA CONSULTAS EXTERNAS ------------------
Route::GET('/caj/cobranza_externa/{credito_id}/{agencia_id}', [CajaCobranzaExternaController::class, 'cobranza'])
    ->name('caj.cobranza_externa');
Route::POST('/caj/cobranza_externa/pagar', [CajaCobranzaExternaController::class, 'pagar'])
    ->name('caj.cobranza_externa.pagar');
Route::POST('/caj/cobranza_externa/cancelar', [CajaCobranzaExternaController::class, 'cancelar'])
    ->name('caj.cobranza_externa.cancelar');

Route::GET('/inv/meta_externa', [InversionMetaExternaController::class, 'inversion_meta'])
    ->name('inv.meta_externa');
Route::POST('/inv/meta_externa/abonar', [InversionMetaExternaController::class, 'abonar'])
    ->name('inv.meta_externa.abonar');
