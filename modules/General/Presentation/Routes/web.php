<?php

use Illuminate\Support\Facades\Route;

use Modules\General\Presentation\Controllers\GeneralController;
//-------------------PERMISOS-------------------------------------------------------
use Modules\General\Presentation\Controllers\PermisosController;
use Modules\General\Presentation\Controllers\PermisosCargosController;
//-------------------CARGOS-------------------------------------------------------
use Modules\General\Presentation\Controllers\CargosController;
//-------------------MANTENIMIENTOS-------------------------------------------------------
use Modules\General\Presentation\Controllers\MantenimientosController;
use Modules\General\Presentation\Controllers\AreaTrabajoController;
use Modules\General\Presentation\Controllers\Credicheck\PromocionesController;
use Modules\General\Presentation\Controllers\ServicioController;
use Modules\General\Presentation\Controllers\BancoController;

//----------------------------------------------------------------------------------

//--------GENERAL-------------------------------------------------------------------

Route::GET('/index', [PermisosController::class, 'index'])
    ->name('gen.index');

//-------------------PERMISOS-------------------------------------------------------

Route::GET('/per/listar_permisos', [PermisosController::class, 'listar_permisos'])
    ->name('gen.per.listar_permisos');
Route::POST('/per/listar_permisos/verificar', [PermisosController::class, 'verificar_permiso'])
    ->name('gen.per.listar_permisos.verificar');
Route::POST('/per/listar_permisos/guardar', [PermisosController::class, 'guardar_permiso'])
    ->name('gen.per.listar_permisos.guardar');
Route::GET('/per/permisos_usuarios/{modo}', [PermisosController::class, 'permisos_usuarios'])
    ->name('gen.per.permisos_usuarios');
Route::GET('/per/permisos_usuarios_editar/{dni}/{modo}', [PermisosController::class, 'editar_permiso'])
    ->name('gen.per.permisos_usuarios_editar');
Route::POST('/per/permisos_usuarios_editar/eliminar', [PermisosController::class, 'eliminar_permiso'])
    ->name('gen.per.permisos_usuarios_editar.eliminar');
Route::POST('/per/permisos_usuarios_editar/eliminarTodo', [PermisosController::class, 'eliminar_todos_permisos'])
    ->name('gen.per.permisos_usuarios_editar.eliminar_todo');
Route::POST('/per/permisos_usuarios_editar/asignar', [PermisosController::class, 'asignar_permiso'])
    ->name('gen.per.permisos_usuarios_editar.asignar');
Route::POST('/per/permisos_usuarios_editar/editarAgencia', [PermisosController::class, 'editar_permiso_agencia'])
    ->name('gen.per.permisos_usuarios_editar.editar_agencia');
Route::POST('/per/permisos_usuarios_editar/copiarPermiso', [PermisosController::class, 'copiar_permiso'])
    ->name('gen.per.permisos_usuarios_editar.copiar');
Route::POST('/per/permisos_usuarios_editar/copiarPermisoCargo', [PermisosController::class, 'copiar_permiso_cargo'])
    ->name('gen.per.permisos_usuarios_editar.copiar_cargo');
Route::GET('/per/permisos_cargos', [PermisosCargosController::class, 'permisos_cargos'])
    ->name('gen.per.permisos_cargos');
Route::GET('/per/permisos_cargos_editar/{id}', [PermisosCargosController::class, 'editar_permiso'])
    ->name('gen.per.permisos_cargos_editar');
Route::POST('/per/permisos_cargos_editar/asignar', [PermisosCargosController::class, 'asignar_permiso'])
    ->name('gen.per.permisos_cargos_editar.asignar');
Route::POST('/per/permisos_cargos_editar/EditarAgencia', [PermisosCargosController::class, 'editar_permiso_agencia'])
    ->name('gen.per.permisos_cargos_editar.editar_agencia');
Route::POST('/per/permisos_cargos_editar/eliminar', [PermisosCargosController::class, 'eliminar_permiso'])
    ->name('gen.per.permisos_cargos_editar.eliminar');

//-------------------CARGOS-------------------------------------------------------

Route::GET('/car/listar_cargos', [CargosController::class, 'listar_cargos'])
    ->name('gen.car.general.listar_cargos');
Route::POST('/car/listar_cargos/verificar', [CargosController::class, 'verificar_cargo'])
    ->name('gen.car.listar_cargos.verificar');
Route::POST('/car/listar_cargos/guardar', [CargosController::class, 'guardar_cargo'])
    ->name('gen.car.listar_cargos.guardar');

//-------------------MANTENIMIENTOS-------------------------------------------------------

Route::GET('/man/listar_feriados', [MantenimientosController::class, 'listar_feriados'])
    ->name('gen.man.listar_feriados');
Route::POST('/man/listar_feriados/guardar', [MantenimientosController::class, 'guardar_feriado'])
    ->name('gen.man.listar_feriados.guardar_feriados');
Route::POST('/man/listar_feriados/verificar', [MantenimientosController::class, 'verificar_feriado'])
    ->name('gen.man.listar_feriados.verificar_feriado');
Route::POST('/per/listar_feriados/eliminar', [MantenimientosController::class, 'eliminar_feriado'])
    ->name('gen.man.listar_feriados.eliminar_feriado');
Route::POST('/per/listar_feriados/editarAgencia', [MantenimientosController::class, 'editar_permiso_agencia'])
    ->name('gen.man.listar_feriados.editar_agencia');


Route::GET('/man/areas_trabajo', [AreaTrabajoController::class, 'areas_trabajo'])
    ->name('gen.man.areas_trabajo');
Route::POST('/man/areas_trabajo/verificar', [AreaTrabajoController::class, 'validarExiste'])
    ->name('gen.man.verificar_area');
Route::POST('/man/areas_trabajo/guardar', [AreaTrabajoController::class, 'guardarArea'])
    ->name('gen.man.guardar_area');

Route::GET('/man/cre/promociones', [PromocionesController::class, 'promociones'])
    ->name('gen.man.cre.promociones');
Route::GET('/man/cre/promociones/listar_recursos', [
    PromocionesController::class,
    'listar_recursos'
])->name('gen.man.cre.listar_recursos');
Route::POST('/man/cre/promociones/guardar', [PromocionesController::class, 'guardar'])
    ->name('gen.man.cre.promociones.guardar');

Route::GET('/man/cre/promociones/listar_recursos_api', [
    PromocionesController::class,
    'listar_recursos_api'
])->name('gen.man.cre.listar_recursos_api');

Route::GET('/man/servicios', [ServicioController::class, 'servicios'])
    ->name('man.servicios');
Route::GET('/man/servicios/listar', [ServicioController::class, 'listar'])
    ->name('man.servicios.listar');
Route::POST('/man/servicios/guardar', [ServicioController::class, 'guardar'])
    ->name('man.servicios.guardar');
Route::GET('/man/servicios/verificar/{servicio}{agencia_id}', [GeneralController::class, 'verificar_servicio'])
    ->name('man.servicios.verificar');

Route::GET('/man/bancos', [BancoController::class, 'index'])
    ->name('man.bancos');
Route::GET('/man/bancos/listar', [BancoController::class, 'listar'])
    ->name('man.bancos.listar');
Route::POST('/man/bancos/guardar', [BancoController::class, 'guardar'])
    ->name('man.bancos.guardar');
Route::PATCH('/man/bancos/{id}/alternar', [BancoController::class, 'alternar'])
    ->name('man.bancos.alternar');

//----------------------------------------------------------------------------------
