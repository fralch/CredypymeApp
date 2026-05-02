<?php

use Illuminate\Support\Facades\Route;

//--------LOGISTICA-----------------------------------------------------------------
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\Logistica\Presentation\Controllers\LogisticaResponsableController;
use Modules\Logistica\Presentation\Controllers\LogisticaEstadoController;
use Modules\Logistica\Presentation\Controllers\LogisticaCondicionController;
// -------------------GESTIÓN DE ACTIVOS--------------------------------------------
use Modules\Logistica\Presentation\Controllers\Activos\ActivoInventarioController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoAsignacionController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoCategoriaController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoNombreController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoTipoController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoEnvioController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoUbicacionController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoCompraController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoRecordController;
use Modules\Logistica\Presentation\Controllers\Activos\ActivoVentaController;
// -------------------GESTIÓN DE SUMINISTROS-----------------------------------------
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroAlmacenController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroAsignacionController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroDevolucionController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroCompraController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroEnvioController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroTipoController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroProveedorController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroMedicionController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroOperacionesController;
use Modules\Logistica\Presentation\Controllers\Suministros\SuministroVentaController;

//--------LOGISTICA-----------------------------------------------------------------
Route::GET('/index', [LogisticaController::class, 'index'])->name('log.index');
// -------------------ACTIVOS--------------------------------------------
Route::GET('/act/inventario', [ActivoInventarioController::class, 'inventario'])
    ->name('log.act.inventario');
Route::POST('/act/inventario/obtener_ultimo', [ActivoInventarioController::class, 'obtener_ultimo'])
    ->name('log.act.inventario.obtener_ultimo');
Route::POST('/act/inventario/listar/{agencia_id}/{estado}', [ActivoInventarioController::class, 'listar'])
    ->name('log.act.inventario.listar');
Route::POST('/act/inventario/comprar', [ActivoCompraController::class, 'comprar'])
    ->name('log.act.inventario.comprar');
Route::POST('/act/inventario/editar', [ActivoInventarioController::class, 'editar'])
    ->name('log.act.inventario.editar');
Route::POST('/act/inventario/enviar', [ActivoEnvioController::class, 'enviar'])
    ->name('log.act.inventario.enviar');
Route::POST('/act_asi/por_responsable/{responsable_id}', [ActivoAsignacionController::class, 'filtrar_por_responsable'])
    ->name('log.act_asignados.por_responsable');
Route::GET('/act/asignacion', [ActivoAsignacionController::class, 'asignacion'])
    ->name('log.act.asignacion');
Route::POST('/act/asignacion/asignar', [ActivoAsignacionController::class, 'asignar'])
    ->name('log.act.asignacion.asignar');
Route::GET('/act/venta', [ActivoVentaController::class, 'venta'])
    ->name('log.act.venta');
Route::POST('/act/venta/vender', [ActivoVentaController::class, 'vender'])
    ->name('log.act.venta.vender');
Route::GET('/act/envios_recepciones', [ActivoEnvioController::class, 'envios_recepciones'])
    ->name('log.act.envios_recepciones');
Route::POST('/act/envios_recepciones/por_fecha', [ActivoEnvioController::class, 'filtrar_por_fecha'])
    ->name('log.act.envios_recepciones.por_fecha');
Route::POST('/act/envios_recepciones/confirmar', [ActivoEnvioController::class, 'confirmar'])
    ->name('log.act.envios_recepciones.confirmar');
Route::GET('/act/mis_activos', [ActivoAsignacionController::class, 'mis_activos'])
    ->name('log.act.mis_activos');
// ----------------------------ACTIVOS HISTORIAL--------------------------------------------
Route::GET('/act/historial_compras', [ActivoCompraController::class, 'historial_compras'])
    ->name('log.act.historial_compras');
Route::POST('/act/historial_compras/buscar', [ActivoCompraController::class, 'buscar'])
    ->name('log.act.historial_compras.buscar');
Route::GET('/act/historial_envios', [ActivoEnvioController::class, 'historial_envios'])
    ->name('log.act.historial_envios');
Route::POST('/act/historial_envios/buscar', [ActivoEnvioController::class, 'buscar'])
    ->name('log.act.historial_envios.buscar');
Route::GET('/act/historial_asignaciones', [ActivoAsignacionController::class, 'historial_asignaciones'])
    ->name('log.act.historial_asignaciones');
Route::POST('/act/historial_asignaciones/buscar', [ActivoAsignacionController::class, 'buscar'])
    ->name('log.act.historial_asignaciones.buscar');
Route::GET('/act/historial_ventas', [ActivoVentaController::class, 'historial_ventas'])
    ->name('log.act.historial_ventas');
Route::POST('/act/historial_ventas/buscar', [ActivoVentaController::class, 'buscar'])
    ->name('log.act.historial_ventas.buscar');
Route::GET('/act/historial_depreciacion', [ActivoRecordController::class, 'historial_depreciacion'])
    ->name('log.act.historial_depreciacion');
Route::POST('/act/historial_depreciacion/buscar', [ActivoRecordController::class, 'buscar_depreciacion'])
    ->name('log.act.historial_depreciacion.buscar');
// -------------------SUMINISTROS--------------------------------------------
Route::GET('/sum/almacen', [SuministroAlmacenController::class, 'almacen'])
    ->name('log.sum.almacen');
Route::GET('/sum/almacen/listar_recursos', [SuministroAlmacenController::class, 'listar_recursos'])
    ->name('log.sum.almacen.listar_recursos');
Route::GET('/sum/almacen/buscar', [SuministroAlmacenController::class, 'buscar'])
    ->name('log.sum.almacen.buscar');
Route::GET('/sum/almacen/obtener_codigo', [SuministroAlmacenController::class, 'obtener_codigo'])
    ->name('log.sum.almacen.obtener_codigo');
Route::POST('/sum/almacen/comprar', [SuministroCompraController::class, 'comprar'])
    ->name('log.sum.almacen.comprar');
Route::POST('/sum/almacen/editar', [SuministroAlmacenController::class, 'editar'])
    ->name('log.sum.almacen.editar');
Route::DELETE('/sum/almacen/eliminar/{id}', [SuministroAlmacenController::class, 'eliminar'])
    ->name('log.sum.almacen.eliminar');
Route::POST('/sum/almacen/exportar', [SuministroAlmacenController::class, 'exportar'])
    ->name('log.sum.almacen.exportar');

Route::POST('/sum/almacen/enviar', [SuministroEnvioController::class, 'enviar'])
    ->name('log.sum.almacen.enviar');
Route::GET('/res/por_agencia', [LogisticaResponsableController::class, 'filtrar_por_agencia'])
    ->name('log.responsables.por_agencia');

Route::GET('/sum_asi/por_responsable', [SuministroAsignacionController::class, 'filtrar_por_responsable'])
    ->name('log.sum_asignados.por_responsable');
Route::GET('/sum/asignacion', [SuministroAsignacionController::class, 'asignacion'])
    ->name('log.sum.asignacion');
Route::POST('/sum/asignacion/asignar', [SuministroAsignacionController::class, 'asignar'])
    ->name('log.sum.asignacion.asignar');

Route::GET('/sum/devolucion', [SuministroDevolucionController::class, 'devolucion'])
    ->name('log.sum.devolucion');
Route::POST('/sum/devolucion/devolver', [SuministroDevolucionController::class, 'devolver'])
    ->name('log.sum.devolucion.devolver');

Route::GET('/sum/venta', [SuministroVentaController::class, 'venta'])
    ->name('log.sum.venta');
Route::POST('/sum/venta/vender', [SuministroVentaController::class, 'vender'])
    ->name('log.sum.venta.vender');

Route::GET('/sum/mis_suministros', [SuministroAsignacionController::class, 'mis_suministros'])
    ->name('log.sum.mis_suministros');

Route::GET('/sum/envios_recepciones', [SuministroEnvioController::class, 'envios_recepciones'])
    ->name('log.sum.envios_recepciones');
Route::GET('/sum/envios_recepciones/listar_recursos', [SuministroEnvioController::class, 'listar_recursos'])
    ->name('log.sum.envios_recepciones.listar_recursos');
Route::GET('/sum/envios_recepciones/por_fecha', [SuministroEnvioController::class, 'filtrar_por_fecha'])
    ->name('log.sum.envios_recepciones.por_fecha');
Route::GET('/sum/envios_recepciones/detalle', [SuministroEnvioController::class, 'detalle'])
    ->name('log.sum.envios_recepciones.detalle');
Route::POST('/sum/envios_recepciones/confirmar', [SuministroEnvioController::class, 'confirmar'])
    ->name('log.sum.envios_recepciones.confirmar');

// --------------------------SUMINISTROS HISTORIAL--------------------------------------------
Route::GET('/sum/historial_compras', [SuministroCompraController::class, 'historial_compras'])
    ->name('log.sum.historial_compras');
Route::GET('/sum/historial_compras/listar_recursos', [SuministroCompraController::class, 'listar_recursos'])
    ->name('log.sum.historial_compras.listar_recursos');
Route::GET('/sum/historial_compras/buscar', [SuministroCompraController::class, 'buscar'])
    ->name('log.sum.historial_compras.buscar');
Route::POST('/sum/historial_compras/exportar', [SuministroCompraController::class, 'exportar'])
    ->name('log.sum.historial_compras.exportar');

Route::GET('/sum/historial_envios', [SuministroEnvioController::class, 'historial_envios'])
    ->name('log.sum.historial_envios');
Route::POST('/sum/historial_envios/buscar', [SuministroEnvioController::class, 'buscar'])
    ->name('log.sum.historial_envios.buscar');

Route::GET('/sum/historial_asignaciones', [SuministroAsignacionController::class, 'historial_asignaciones'])
    ->name('log.sum.historial_asignaciones');
Route::POST('/sum/historial_asignaciones/buscar', [SuministroAsignacionController::class, 'buscar'])
    ->name('log.sum.historial_asignaciones.buscar');

Route::GET('/sum/historial_devoluciones', [SuministroDevolucionController::class, 'historial_devoluciones'])
    ->name('log.sum.historial_devoluciones');
Route::POST('/sum/historial_devoluciones/buscar', [SuministroDevolucionController::class, 'buscar'])
    ->name('log.sum.historial_devoluciones.buscar');

Route::GET('/sum/historial_bajas', [SuministroDevolucionController::class, 'historial_bajas'])
    ->name('log.sum.historial_bajas');
Route::POST('/sum/historial_bajas/buscar', [SuministroDevolucionController::class, 'buscar'])
    ->name('log.sum.historial_bajas.buscar');

Route::GET('/sum/historial_ventas', [SuministroVentaController::class, 'historial_ventas'])
    ->name('log.sum.historial_ventas');
Route::POST('/sum/historial_ventas/buscar', [SuministroVentaController::class, 'buscar'])
    ->name('log.sum.historial_ventas.buscar');

Route::GET('/sum/historial_operaciones', [SuministroOperacionesController::class, 'historial_operaciones'])
    ->name('log.sum.historial_operaciones');
Route::GET('/sum/historial_operaciones/buscar', [SuministroOperacionesController::class, 'buscar'])
    ->name('log.sum.historial_operaciones.buscar');

// -------------------MANTENIMIENTO--------------------------------------------
Route::GET('/man/activos_categorias', [ActivoCategoriaController::class, 'categorias'])
    ->name('log.man.act_categorias');
Route::POST('/man/activos_categorias/verificar', [ActivoCategoriaController::class, 'verificar'])
    ->name('log.man.act_categorias.verificar');
Route::POST('/man/activos_categorias/guardar', [ActivoCategoriaController::class, 'guardar'])
    ->name('log.man.act_categorias.guardar');
Route::GET('/man/activos_nombres', [ActivoNombreController::class, 'nombres'])
    ->name('log.man.act_nombres');
Route::POST('/man/activos_nombres/verificar', [ActivoNombreController::class, 'verificar'])
    ->name('log.man.act_nombres.verificar');
Route::POST('/man/activos_nombres/guardar', [ActivoNombreController::class, 'guardar'])
    ->name('log.man.act_nombres.guardar');
Route::GET('/man/activos_tipos', [ActivoTipoController::class, 'tipos'])
    ->name('log.man.act_tipos');
Route::POST('/man/activos_tipos/verificar', [ActivoTipoController::class, 'verificar'])
    ->name('log.man.act_tipos.verificar');
Route::POST('/man/activos_tipos/guardar', [ActivoTipoController::class, 'guardar'])
    ->name('log.man.act_tipos.guardar');
Route::GET('/man/activos_ubicaciones', [ActivoUbicacionController::class, 'ubicaciones'])
    ->name('log.man.act_ubicaciones');
Route::POST('/man/activos_ubicaciones/verificar', [ActivoUbicacionController::class, 'verificar'])
    ->name('log.man.act_ubicaciones.verificar');
Route::POST('/man/activos_ubicaciones/guardar', [ActivoUbicacionController::class, 'guardar'])
    ->name('log.man.act_ubicaciones.guardar');

Route::GET('/man/suministros_tipos', [SuministroTipoController::class, 'tipos'])
    ->name('log.man.sum_tipos');
Route::POST('/man/suministros_tipos/verificar', [SuministroTipoController::class, 'verificar'])
    ->name('log.man.sum_tipos.verificar');
Route::POST('/man/suministros_tipos/guardar', [SuministroTipoController::class, 'guardar'])
    ->name('log.man.sum_tipos.guardar');
Route::GET('/man/suministros_proveedores', [SuministroProveedorController::class, 'proveedores'])
    ->name('log.man.sum_proveedores');
Route::POST('/man/suministros_proveedores/verificar', [SuministroProveedorController::class, 'verificar'])
    ->name('log.man.sum_proveedores.verificar');
Route::POST('/man/suministros_proveedores/guardar', [SuministroProveedorController::class, 'guardar'])
    ->name('log.man.sum_proveedores.guardar');

Route::GET('/man/suministros_mediciones', [SuministroMedicionController::class, 'mediciones'])
    ->name('log.man.sum_mediciones');
Route::POST('/man/suministros_mediciones/verificar', [SuministroMedicionController::class, 'verificar'])
    ->name('log.man.sum_mediciones.verificar');
Route::POST('/man/suministros_mediciones/guardar', [SuministroMedicionController::class, 'guardar'])
    ->name('log.man.sum_mediciones.guardar');

Route::GET('/man/responsables', [LogisticaResponsableController::class, 'responsables'])
    ->name('log.man.responsables');
Route::POST('/man/responsables/verificar', [LogisticaResponsableController::class, 'verificar'])
    ->name('log.man.responsables.verificar');
Route::POST('/man/responsables/guardar', [LogisticaResponsableController::class, 'guardar'])
    ->name('log.man.responsables.guardar');
Route::GET('/man/estados', [LogisticaEstadoController::class, 'estados'])
    ->name('log.man.estados');
Route::POST('/man/estados/verificar', [LogisticaEstadoController::class, 'verificar'])
    ->name('log.man.estados.verificar');
Route::POST('/man/estados/guardar', [LogisticaEstadoController::class, 'guardar'])
    ->name('log.man.estados.guardar');
Route::GET('/man/condiciones', [LogisticaCondicionController::class, 'condiciones'])
    ->name('log.man.condiciones');
Route::POST('/man/condiciones/verificar', [LogisticaCondicionController::class, 'verificar'])
    ->name('log.man.condiciones.verificar');
Route::POST('/man/condiciones/guardar', [LogisticaCondicionController::class, 'guardar'])
    ->name('log.man.condiciones.guardar');

// -------------------APLICACIÓN--------------------------------------------

Route::GET('/apl/cierre_dia', [LogisticaController::class, 'cierre_dia'])
    ->name('log.apl.cierre_dia');
Route::POST('/apl/cierre_dia/cerrar', [LogisticaController::class, 'cerrar_dia'])
    ->name('log.apl.cierre_dia.cerrar');
