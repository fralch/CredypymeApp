<?php

use Illuminate\Support\Facades\Route;


//--------GTH-----------------------------------------------------------------------
use Modules\Gth\Presentation\Controllers\GthController;
// ----------------USUARIOS Y ASISTENCIA--------------------------------------------
use Modules\Gth\Presentation\Controllers\Usuarios\UsuarioController;
use Modules\Gth\Presentation\Controllers\Usuarios\HorarioAsignadoController;
use Modules\Gth\Presentation\Controllers\Usuarios\CesadoController;
use Modules\Gth\Presentation\Controllers\Asistencias\AsistenciaController;
use Modules\Gth\Presentation\Controllers\Asistencias\TokenController;

use Modules\Gth\Presentation\Controllers\Asistencias\TardanzaController;
use Modules\Gth\Presentation\Controllers\Asistencias\FaltaController;
use Modules\Gth\Presentation\Controllers\Asistencias\AsistenciaGeneralController;
use Modules\Gth\Presentation\Controllers\Asistencias\JustificacionController;
use Modules\Gth\Presentation\Controllers\Asistencias\LicenciaController;
use Modules\Gth\Presentation\Controllers\Asistencias\PermisoController;

use Modules\Gth\Presentation\Controllers\Mantenimiento\HorarioController;
use Modules\Gth\Presentation\Controllers\Mantenimiento\PlanillaSistPensioneController;
use Modules\Gth\Presentation\Controllers\Mantenimiento\CerrarDiaGthController;
use Modules\Gth\Presentation\Controllers\Mantenimiento\LicenciaCategoriaController;


// -----------------COLABORADOR DEL MES---------------------------------------------
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesPreguntaController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesCategoriaController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesNivelController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesEquipoController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesEquipoIntegranteController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesExamenController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesExamenPreguntaController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesResultadosController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesSeguimientoController;



use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesEvaluacionController;
use Modules\Gth\Presentation\Controllers\ColaboradorMes\ColaboradorMesEvaluacionPreguntaController;
//-------------------PLANILLAS -----------------------------------------------------
use Modules\Gth\Presentation\Controllers\Planillas\PlanillaGeneraleController;
use Modules\Gth\Presentation\Controllers\Planillas\PlanillaUsuarioController;
use Modules\Gth\Presentation\Controllers\Planillas\PlanillaVacacioneController;

use Modules\Gth\Presentation\Controllers\Planillas\PlanillaVacacionePeriodoController;
use Modules\Gth\Presentation\Controllers\Planillas\PlanillaVacacioneDetalleController;

use Modules\Gth\Presentation\Controllers\Apis\ApiSolicitudController;

// -----------------MIPANELGTH------------------------------------------------------
use Modules\Gth\Presentation\Controllers\MiPanelGth\MiPanelGthController;
//----------------------------------------------------------------------------------


//--------GTH------------------------------------------------------------------

Route::GET('/index', [GthController::class, 'index'])
    ->name('gth.index');

// --------------------ASISTENCIA---------------------------------------------------

Route::GET('/asi/validar/asistencia', [AsistenciaController::class, 'validar_asistencia'])->name('gth.asi.validar_asistencia');
Route::POST('/asi/validacion_token/verificar', [TokenController::class, 'validar_token'])->name('gth.asi.validar_token');
Route::GET('/asi/marcado_asistencia', [AsistenciaController::class, 'marcado_asistencia'])->name('gth.asi.marcado_asistencia');
Route::POST('/asi/marcado_asistencia/verificar_usuario_asistencia', [AsistenciaController::class, 'verificar_usuario_asistencia'])->name('gth.asi.verificar_usuario_asistencia');
Route::POST('/asi/marcado_asistencia/registrar_asistencia', [AsistenciaController::class, 'registrar_asistencia'])->name('gth.asi.registrar_asistencia');

// Route::get('/asi/asistencia/fecha_servidor', [AsistenciaController::class, 'fecha_servidor'])->name('fecha_servidor');


// ----------------USUARIOS Y ASISTENCIA--------------------------------------------

Route::GET('/usuarios', [UsuarioController::class, 'index'])->name('gth.usu.index');
Route::GET('/usu/gestion', [UsuarioController::class, 'usuarios_gestion'])->name('gth.usu.usuarios_gestion');
Route::POST('/usu/usuarios_gestion/verificar', [UsuarioController::class, 'verificar_usuario'])->name('gth.usu.usuarios_gestion.verificar');
Route::POST('/usu/usuarios_gestion/guardar', [UsuarioController::class, 'guardar_usuario'])->name('gth.usu.usuarios_gestion.guardar');

Route::POST('/usu/usuarios_gestion/guardar_clave', [UsuarioController::class, 'guardar_clave_usuario'])->name('gth.usu.usuarios_gestion.guardar_clave');
Route::POST('/usu/usuarios_gestion/generar_clave', [UsuarioController::class, 'generar_clave'])->name('gth.usu.usuarios_gestion.generar_clave');


Route::POST('/usu/usuarios_gestion/cesar', [UsuarioController::class, 'cesar_usuario'])->name('gth.usu.usuarios_gestion.cesar');
Route::GET('/usu/usuarios_horarios', [HorarioAsignadoController::class, 'usuarios_horarios'])->name('gth.usu.usuarios_horarios');
Route::POST('/usu/usuarios_horarios/asignar_horario', [HorarioAsignadoController::class, 'asignar_horario'])->name('gth.usu.usuarios_horarios.asignar_horario');
Route::POST('/usu/usuarios_horarios/asignar_tolerancia', [HorarioAsignadoController::class, 'asignar_tolerancia'])->name('gth.usu.usuarios_horarios.asignar_tolerancia');
Route::GET('/usu/usuarios_cesados', [CesadoController::class, 'usuarios_cesados'])->name('gth.usu.usuarios_cesados');
Route::POST('/usu/usuarios_cesados/habilitar', [CesadoController::class, 'habilitar_cesado'])->name('gth.usu.usuarios_cesados.habilitar');
Route::GET('/usu/mis_datospersonales', [UsuarioController::class, 'mis_datos_personales'])->name('gth.usu.mis_datos_personales');
Route::POST('/usu/mis_datospersonales/verificar', [UsuarioController::class, 'verificar_mi_usuario'])->name('gth.usu.mis_datos_personales.verificar');
Route::POST('/usu/mis_datospersonales/guardar', [UsuarioController::class, 'guardar_mi_usuario'])->name('gth.usu.mis_datos_personales.guardar');

Route::POST('/usu/mis_datospersonales/guardar_clave', [UsuarioController::class, 'guardar_clave'])->name('gth.usu.mis_datos_personales.guardar_clave');


Route::GET('/asistencias/{modo}', [AsistenciaController::class, 'asistencias'])->name('gth.asi.index');
Route::POST('/asi/asistencias/listar', [AsistenciaController::class, 'listar_asistencias'])->name('gth.asi.asistencias.listar');
Route::POST('/asi/asistencias/mis_asistencias', [AsistenciaController::class, 'listar_mis_asistencias']);
Route::POST('/asi/asistencias/exportar', [AsistenciaController::class, 'exportar_asistencias'])->name('gth.asi.asistencias.exportar');
Route::POST('/asi/asistencias/version', [AsistenciaController::class, 'versiones_aplicacion']);



Route::GET('/asi/tardanzas/{modo}/', [TardanzaController::class, 'tardanzas'])->name('gth.asi.tardanzas');
Route::POST('/asi/tardanzas/listar', [TardanzaController::class, 'listar_tardanzas'])->name('gth.asi.tardanzas.listar');
Route::POST('/asi/tardanzas/justificar', [TardanzaController::class, 'justificar_tardanza'])->name('gth.asi.tardanzas.justificar');
Route::POST('/asi/tardanzas/exportar', [TardanzaController::class, 'exportar_tardanzas'])->name('gth.asi.tardanzas.exportar');


Route::GET('/asi/faltas/{modo}/', [FaltaController::class, 'faltas'])->name('gth.asi.faltas');
Route::POST('/asi/faltas/listar', [FaltaController::class, 'listar_faltas'])->name('gth.asi.faltas.listar');
Route::POST('/asi/faltas/justificar', [FaltaController::class, 'justificar_faltas'])->name('gth.asi.faltas.justificar');
Route::POST('/asi/faltas/exportar', [FaltaController::class, 'exportar_faltas'])->name('gth.asi.faltas.exportar');


Route::GET('/asi/justificaciones/{modo}/', [JustificacionController::class, 'justificaciones'])->name('gth.asi.justificaciones');
Route::POST('/asi/justificaciones/listar', [JustificacionController::class, 'listar_justificaciones'])->name('gth.asi.justificaciones.listar');
Route::POST('/asi/justificaciones/exportar', [JustificacionController::class, 'exportar_justificaciones'])->name('gth.asi.justificaciones.exportar');
Route::POST('/asi/justificaciones/listar_api', [JustificacionController::class, 'listar_mis_justificaciones'])->name('gth.asi.justificaciones.listar_api');

// Route::GET('/asi/faltas/generar_faltas', [FaltaController::class, 'generar_faltas'])->name('faltas.generar');
// Route::GET('/asistencia/faltas/generar', [FaltaController::class, 'generar_faltas'])->name('faltas.generar');

Route::GET('/asi/licencias', [LicenciaController::class, 'licencias'])->name('gth.asi.licencias');
Route::GET('/asi/licencias/listar/{fecha_desde}/{fecha_hasta}', [LicenciaController::class, 'listar_solicitud_licencias'])->name('gth.asi.listar_solicitud_licencias');
Route::POST('/asi/licencias/validar', [LicenciaController::class, 'validar_licencia'])
    ->name('gth.asi.licencias.validar');
Route::POST('/asi/licencias/invalidar', [LicenciaController::class, 'invalidar_licencia'])
    ->name('gth.asi.licencias.invalidar');


Route::POST('/asi/licencias/verificar', [LicenciaController::class, 'verificar_licencia'])
    ->name('gth.asi.licencias.verificar');
Route::POST('/asi/licencias/cancelar', [LicenciaController::class, 'cancelar_licencia'])
    ->name('gth.asi.licencias.cancelar');

Route::POST('/asi/licencias/exportar', [LicenciaController::class, 'exportar'])
    ->name('gth.asi.licencias.exportar');


Route::GET('/asi/permisos', [PermisoController::class, 'permisos'])->name('gth.asi.permisos');
Route::GET('/asi/permisos/listar/{fecha_desde}/{fecha_hasta}', [PermisoController::class, 'listar_solicitud_permisos'])->name('gth.asi.listar_solicitud_permisos');
Route::POST('/asi/permisos/validar', [PermisoController::class, 'validar_permiso'])
    ->name('gth.asi.permisos.validar');
Route::POST('/asi/permisos/invalidar', [PermisoController::class, 'invalidar_permiso'])
    ->name('gth.asi.permisos.invalidar');
Route::POST('/asi/permisos/cancelar', [PermisoController::class, 'cancelar_permiso'])
    ->name('gth.asi.permisos.cancelar');

Route::POST('/asi/permisos/exportar', [PermisoController::class, 'exportar'])
    ->name('gth.asi.permisos.exportar');


Route::POST('/asi/general/exportar_general', [LicenciaController::class, 'exportar_general'])
    ->name('gth.asi.general.exportar_general');


Route::GET('/us_as/general', [AsistenciaGeneralController::class, 'general'])->name('gth.us_as.general');
Route::POST('/us_as/general/listar', [AsistenciaGeneralController::class, 'listar'])->name('gth.us_as.general.listar');
Route::POST('/us_as/general/exportar', [AsistenciaGeneralController::class, 'exportar_general'])->name('gth.asi.general.exportar');

Route::GET('/us_as/horarios', [HorarioController::class, 'horarios'])->name('gth.us_as.horarios');
Route::POST('/us_as/horarios/guardar', [HorarioController::class, 'guardar_horario'])->name('gth.us_as.horarios.guardar');

// -----------------COLABORADOR DEL MES---------------------------------------------

Route::GET('/col/preguntas', [ColaboradorMesPreguntaController::class, 'preguntas'])
    ->name('col.preguntas');
Route::GET('/col/preguntas/listar_recursos', [ColaboradorMesPreguntaController::class, 'listar_recursos'])
    ->name('col.preguntas.listar_recursos');
Route::POST('/col/preguntas/verificar', [ColaboradorMesPreguntaController::class, 'verificar'])
    ->name('col.preguntas.verificar');
Route::POST('/col/preguntas/guardar', [ColaboradorMesPreguntaController::class, 'guardar'])
    ->name('col.preguntas.guardar');

Route::GET('/col/equipos', [ColaboradorMesEquipoController::class, 'equipos'])
    ->name('col.equipos');
Route::GET('/col/equipos/listar_recursos', [ColaboradorMesEquipoController::class, 'listar_recursos'])
    ->name('col.equipos.listar_recursos');
Route::POST('/col/equipos/verificar', [ColaboradorMesEquipoController::class, 'verificar'])
    ->name('col.equipos.verificar');
Route::POST('/col/equipos/guardar', [ColaboradorMesEquipoController::class, 'guardar'])
    ->name('col.equipos.guardar');
Route::GET('/col/equipos/integrantes/{equipo_id}', [ColaboradorMesEquipoController::class, 'listar_integrantes'])
    ->name('col.equipos.integrantes');
Route::POST('/col/equipos/integrantes/guardar', [ColaboradorMesEquipoController::class, 'guardar_integrantes'])
    ->name('col.equipos.integrantes.guardar');

Route::GET('/col/examenes', [ColaboradorMesExamenController::class, 'examenes'])
    ->name('col.examenes');
Route::GET('/col/examenes/listar_recursos', [ColaboradorMesExamenController::class, 'listar_recursos'])
    ->name('col.examenes.listar_recursos');
Route::POST('/col/examenes/ver', [ColaboradorMesExamenController::class, 'ver'])
    ->name('col.examenes.ver');
Route::POST('/col/examenes/verificar', [ColaboradorMesExamenController::class, 'verificar'])
    ->name('col.examenes.verificar');
Route::POST('/col/examenes/guardar', [ColaboradorMesExamenController::class, 'guardar'])
    ->name('col.examenes.guardar');

Route::GET('/col_mes/asignar_evaluacion', [ColaboradorMesEvaluacionController::class, 'asignar_evaluacion'])
    ->name('gth.col_mes.asignar_evaluacion');
Route::POST('/col_mes/asignar_evaluacion/comprobar', [ColaboradorMesEvaluacionController::class, 'comprobar_evaluacion_asignada'])
    ->name('gth.col_mes.asignar_evaluacion.comprobar');
Route::POST('/col_mes/asignar_evaluacion/verificar', [ColaboradorMesEvaluacionController::class, 'verificar_evaluacion'])
    ->name('gth.col_mes.asignar_evaluacion.verificar');
Route::POST('/col_mes/asignar_evaluacion/guardar', [ColaboradorMesEvaluacionController::class, 'guardar_evaluacion'])
    ->name('gth.col_mes.asignar_evaluacion.guardar');
// Route::GET('/gth/colaborador_mes', [ColaboradorMesController::class, 'index'])->name('colaborador_mes.index');

//-------------------PLANILLAS -----------------------------------------------------

Route::GET('/planillas', [PlanillaGeneraleController::class, 'index'])->name('gth.pla.index');
Route::GET('/pla/datos_usuarios', [PlanillaUsuarioController::class, 'datos_usuarios'])->name('gth.pla.datos_usuarios');
Route::POST('/pla/datos_usuarios/guardar', [PlanillaUsuarioController::class, 'guardar_datos_usuario'])->name('gth.pla.datos_usuarios.guardar');
Route::GET('/pla/vacaciones', [PlanillaVacacioneController::class, 'vacaciones'])->name('gth.pla.vacaciones');
Route::GET('/pla/planilla_general', [PlanillaGeneraleController::class, 'planilla_general'])->name('gth.pla.planilla_general');
Route::POST('/pla/planilla_general/generar', [PlanillaGeneraleController::class, 'generar_plantilla'])->name('gth.pla.planilla_general.generar');
Route::POST('/pla/planilla_general/guardar', [PlanillaGeneraleController::class, 'guardar_planilla'])->name('gth.pla.planilla_general.guardar');
Route::GET('/pla/historial_vacaciones', [PlanillaVacacioneDetalleController::class, 'historial_vacaciones'])->name('gth.pla.historial_vacaciones');
Route::POST('/pla/historial_vacaciones_listar', [PlanillaVacacioneDetalleController::class, 'historial_vacaciones_listar'])->name('gth.pla.historial_vacaciones_listar');
Route::POST('/pla/vacaciones/asignar', [PlanillaVacacioneDetalleController::class, 'asignar_vacaciones'])->name('gth.pla.vacaciones.asignar');
Route::POST('/pla/historial_vacaciones/buscar_periodo', [PlanillaVacacionePeriodoController::class, 'buscar_periodo'])->name('gth.pla.historial_vacaciones.buscar_periodo');
Route::GET('/pla/historial_planillas', [PlanillaGeneraleController::class, 'historial_planillas'])->name('gth.pla.historial_planillas');
Route::POST('/pla/historial_planillas/listar', [PlanillaGeneraleController::class, 'listar_historial_planilla'])->name('gth.pla.historial_planillas.listar');
Route::POST('/pla/historial_planillas/guardar', [PlanillaGeneraleController::class, 'guardar_planilla'])->name('gth.pla.historial_planillas.guardar');
Route::GET('/pla/sistema_pensiones', [PlanillaSistPensioneController::class, 'sistema_pensiones'])->name('gth.pla.sistema_pensiones');
Route::POST('/pla/sistema_pensiones/guardar', [PlanillaSistPensioneController::class, 'guardar_sistema_pension'])->name('gth.pla.sistema_pensiones.guardar');

// -----------------MIPANELGTH------------------------------------------------------

// -----USUARIOS Y ASISTENCIA------



// ------COLABORADOR DEL MES-------

Route::GET('/col_mes/mis_evaluaciones_pendientes', [ColaboradorMesEvaluacionPreguntaController::class, 'mis_evaluaciones_pendientes'])->name('gth.col_mes.mis_evaluaciones_pendientes');
Route::POST('/col_mes/mis_evaluaciones_pendientes/guardar', [ColaboradorMesEvaluacionPreguntaController::class, 'guardar_mi_evaluacion'])->name('gth.col_mes.mis_evaluaciones_pendientes.guardar');
Route::POST('/col_mes/mis_evaluaciones_pendientes/preguntas', [ColaboradorMesEvaluacionPreguntaController::class, 'preguntas_evaluaciones'])->name('gth.col_mes.mis_evaluaciones_pendientes.preguntas');
Route::POST('/col_mes/mis_evaluaciones_pendientes/guardar', [ColaboradorMesEvaluacionPreguntaController::class, 'guarda_puntos_evaluacion'])->name('gth.col_mes.mis_evaluaciones_pendientes.guardar');

Route::GET('/col/mis_evaluaciones/listar_recursos', [ColaboradorMesEvaluacionPreguntaController::class, 'listar_mis_evaluaciones'])
    ->name('col.mis_evaluaciones.listar_recursos');
Route::POST('/col/mis_evaluaciones/generar', [ColaboradorMesEvaluacionPreguntaController::class, 'generar_evaluacion'])
    ->name('col.mis_evaluaciones.generar');
Route::POST('/col/mis_evaluaciones/guardar_avance', [ColaboradorMesEvaluacionPreguntaController::class, 'guardar_avance'])
    ->name('col.mis_evaluaciones.guardar_avance');
Route::POST('/col/mis_evaluaciones/terminar', [ColaboradorMesEvaluacionPreguntaController::class, 'terminar'])
    ->name('col.mis_evaluaciones.terminar');



Route::GET('/col/seguimiento', [ColaboradorMesSeguimientoController::class, 'seguimiento'])
    ->name('col.seguimiento');
Route::POST('/col/seguimiento/buscar', [ColaboradorMesSeguimientoController::class, 'buscar'])
    ->name('col.seguimiento.buscar');

Route::GET('/col/resultados', [ColaboradorMesResultadosController::class, 'resultados'])
    ->name('col.resultados');
Route::POST('/col/resultados/buscar', [ColaboradorMesResultadosController::class, 'buscar'])
    ->name('col.resultados.buscar');
Route::POST('/col/resultados/exportar', [ColaboradorMesResultadosController::class, 'exportar'])
    ->name('col.resultados.exportar');
Route::POST('/col/resultados/detalle', [ColaboradorMesResultadosController::class, 'detalle'])
    ->name('col.resultados.detalle');

// ------RUTAS API-------
// ------Generar Solicitud-------

Route::POST('/sol/guardar', [ApiSolicitudController::class, 'guardar'])
    ->name('sol.guardar');

Route::POST('/sol/editar', [ApiSolicitudController::class, 'editar'])
    ->name('sol.editar');

Route::GET('/sol/listar_aprobadores/{dni}', [ApiSolicitudController::class, 'listar_aprobadores'])
    ->name('sol.listar_aprobadores');

Route::GET('/sol/licencia/listar_categorias', [ApiSolicitudController::class, 'listar_categorias'])
    ->name('sol.licencia.listar_categorias');


// ------Ver Solicitud-------
Route::GET('/sol/listar/{tipo}/{fecha_inicio}/{fecha_fin}/{usuario}', [ApiSolicitudController::class, 'listar'])
    ->name('sol.listar');

Route::GET('/sol/licencia/ver/{id}', [ApiSolicitudController::class, 'ver_licencia'])
    ->name('sol.licencia.ver');
Route::GET('/sol/permiso/ver/{id}', [ApiSolicitudController::class, 'ver_permiso'])
    ->name('sol.permiso.ver');


Route::PUT('/sol/licencia/eliminar/{id}', [ApiSolicitudController::class, 'eliminar_licencia'])
    ->name('sol.licencia.eliminar');
Route::PUT('/sol/permiso/eliminar/{id}', [ApiSolicitudController::class, 'eliminar_permiso'])
    ->name('sol.permiso.eliminar');

// ------Aprobar Solicitud-------
Route::GET('/sol/listar_solicitudes/{estado}/{fecha_inicio}/{fecha_fin}/{usuario}', [ApiSolicitudController::class, 'listar_solicitudes'])
    ->name('sol.listar_solicitudes');

Route::POST('/sol/aprobar_solicitud', [ApiSolicitudController::class, 'aprobar_solicitud'])
    ->name('sol.aprobar_solicitud');


// ------Datos para el desarrollador FrontEnd-------

Route::GET('/sol/licencia/listar', [ApiSolicitudController::class, 'listar_licencias'])
    ->name('sol.licencia.listar');

Route::GET('/sol/permiso/listar', [ApiSolicitudController::class, 'listar_permisos'])
    ->name('sol.permiso.listar');



Route::GET('/sol/permiso/listar_permiso_retorno/{fecha}/{agencia}', [ApiSolicitudController::class, 'listar_permiso_retorno'])
    ->name('sol.permiso.listar_permiso_retorno');


Route::POST('/sol/permiso/verificar_permiso_retorno', [ApiSolicitudController::class, 'verificar_permiso_retorno'])
    ->name('sol.permiso.verificar_permiso_retorno');




Route::GET('/sol/licencia/listar_feriados', [ApiSolicitudController::class, 'listar_feriados'])
    ->name('sol.licencia.listar_feriados');

Route::GET('/sol/licencia/listar_usuarios', [ApiSolicitudController::class, 'listar_usuarios'])
    ->name('sol.licencia.listar_usuarios');

// ------APLICACIÓN-------

Route::GET('/man/cierre_dia', [CerrarDiaGthController::class, 'cierre_dia'])->name('gth.man.cierre_dia');
Route::POST('/man/cierre_dia/cerrar', [CerrarDiaGthController::class, 'cerrar_dia'])->name('gth.man.cierre_dia_gth.cerrar');

Route::GET('/man/col/categorias', [ColaboradorMesCategoriaController::class, 'categorias'])
    ->name('man.col.categorias');
Route::GET('/man/col/categorias/listar_recursos', [ColaboradorMesCategoriaController::class, 'listar_recursos'])
    ->name('man.col.categorias.listar_recursos');
Route::POST('/man/col/categorias/verificar', [ColaboradorMesCategoriaController::class, 'verificar'])
    ->name('man.col.categorias.verificar');
Route::POST('/man/col/categorias/guardar', [ColaboradorMesCategoriaController::class, 'guardar'])
    ->name('man.col.categorias.guardar');

Route::GET('/man/col/niveles', [ColaboradorMesNivelController::class, 'niveles'])
    ->name('man.col.niveles');
Route::GET('/man/col/niveles/listar_recursos', [ColaboradorMesNivelController::class, 'listar_recursos'])
    ->name('man.col.niveles.listar_recursos');
Route::POST('/man/col/niveles/verificar', [ColaboradorMesNivelController::class, 'verificar'])
    ->name('man.col.niveles.verificar');
Route::POST('/man/col/niveles/giardar', [ColaboradorMesNivelController::class, 'guardar'])
    ->name('man.col.niveles.guardar');

Route::GET('/man/sol/licencia_categorias', [LicenciaCategoriaController::class, 'licencia_categorias'])
    ->name('man.sol.licencia_categorias');
Route::GET('/man/sol/licencia_categorias/listar_categorias', [LicenciaCategoriaController::class, 'listar_categorias'])
    ->name('man.sol.licencia_categorias.listar_categorias');
Route::POST('/man/sol/licencia_categorias/verificar', [LicenciaCategoriaController::class, 'verificar_categorias'])
    ->name('man.sol.licencia_categorias.verificar');
Route::POST('/man/sol/licencia_categorias/guardar', [LicenciaCategoriaController::class, 'guardar_categorias'])
    ->name('man.sol.licencia_categorias.guardar');


// ---------CAMBIAR PASSWORD--------

Route::get('/mipanelgth/mis_datospersonales/changepassword/', [UsuarioController::class, 'changepass']); //provisional
//-----------------------------------------------------------------------------------
