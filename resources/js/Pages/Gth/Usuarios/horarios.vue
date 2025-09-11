<template>
	<layout ref="layout">
		<div class="slot_body slot-horarios" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HORARIOS'"></headerClose>

					<div class="card-title">PANEL DE BÚSQUEDA</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-3">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">HORARIO</span>
								</div>
								<select
									class="form-control"
									id="cmbHorarios"
									style="max-width: 250px"
									data-index="3"
								>
									<option value="0">TODAS</option>
									<option v-for="horario in horarios" v-bind:key="horario.id">
										{{ horario.horario }}
									</option>
								</select>
							</div>

							<div class="input-group col-md-7 col-5">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									id="inpBuscar"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE USUARIOS Y HORARIOS</div>
					<div class="card-body card-block">
						<div id="tabla_horarios">
							<table
								class="table table-hover"
								id="t_usuarios_horarios"
								width="100%"
							>
								<thead>
									<tr>
										<th style="min-width: 100px !important">ACCIONES</th>
										<th style="min-width: 200px !important">
											APELLIDOS_NOMBRES
										</th>
										<th style="min-width: 30px !important">MARCAJE</th>
										<th style="min-width: 80px !important">HORARIO</th>
										<th style="min-width: 60px !important">INGRESO_MAÑANA</th>
										<th style="min-width: 60px !important">SALIDA_MAÑANA</th>
										<th style="min-width: 60px !important">INGRESO_TARDE</th>
										<th style="min-width: 60px !important">SALIDA_TARDE</th>
										<th style="min-width: 60px !important">
											SÁBADO_INGRESO_MAÑANA
										</th>
										<th style="min-width: 60px !important">
											SÁBADO_SALIDA_MAÑANA
										</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(usuario_horario, index) in usuarios_horarios"
										v-bind:key="index"
									>
										<td class="table-bordered" align="center" width="100px">
											<div class="row align-middle row-buttons">
												<div class="col-md-9 col-md-offset-9 col-xs-12">
													<div class="btn-group" role="group">
														<button
															class="btn btn-action"
															@click="CambiarHorario(usuario_horario)"
														>
															<span class="icon text-white-50">
																<i class="fas fa-edit" style="color: white"></i>
															</span>
														</button>
														<button
															class="btn btn-cancel"
															type="button"
															title="Asignar tolerancia personal"
															@click="AsignarTolerancia(usuario_horario)"
														>
															<span class="icon text-white">
																<i class="far fa-clock"></i>
															</span>
														</button>
													</div>
												</div>
											</div>
										</td>
										<td class="table-bordered" width="350px">
											{{
												usuario_horario.apellido_paterno +
												" " +
												usuario_horario.apellido_materno +
												" " +
												usuario_horario.nombres
											}}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.marca_asistencia == 1 ? "Si" : "No" }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.horario }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.hora_entrada_mañana }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.hora_salida_mañana }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.hora_entrada_tarde }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.hora_salida_tarde }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.hora_entrada_mañana_s }}
										</td>
										<td class="table-bordered" align="center">
											{{ usuario_horario.hora_salida_mañana_s }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlCambiarHorario" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-60 mdlDatosHorario">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>{{ title_modal }}</strong>
								<button
									type="button"
									class="btn btn-action"
									style="border-radius: 50%; float: right !important"
									@click="Cerrar('mdlCambiarHorario')"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-title">HORARIO ASIGNADO</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-md-4">
											<label class="label-title">COLABORADOR</label>
											<input
												type="text"
												class="form-control"
												v-model="frmUsuarioHorario.nombre_completo"
												:disabled="true"
											/>
										</div>
										<div class="form-group col-md-3">
											<label for="txtAgenciaHorario" class="label-title"
												>AGENCIA</label
											>
											<input
												type="text"
												class="form-control center"
												style="max-width: 250px"
												v-model="frmUsuarioHorario.agencia"
												:disabled="true"
											/>
										</div>
										<div class="form-group col-md-3">
											<div class="form-row mt-4">
												<label
													for="chbMarcaje"
													class="form-control-label label-title"
													>MARCAJE:</label
												>
												<div class="checkbox">
													<label
														class="align-middle"
														style="
															font-size: 1em;
															margin-bottom: 0 !important;
															height: 1em !important;
														"
														for="chbMarcaje"
														><input
															type="checkbox"
															id="chbMarcaje"
															v-model="frmUsuarioHorario.habilitado" /><span
															class="cr"
															style="margin-right: 0 !important"
															><i class="cr-icon fa fa-check"></i></span
													></label>
												</div>
											</div>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label class="label-title">HORARIOS DISPONIBLES</label>
											<table class="table table-hover" id="t_datos_horarios">
												<thead>
													<tr>
														<th style="min-width: 20px !important">ASIGNAR</th>
														<th style="min-width: 80px !important">NOMBRE</th>
														<th style="min-width: 40px !important">
															INGRESO_MAÑANA
														</th>
														<th style="min-width: 40px !important">
															SALIDA_MAÑANA
														</th>
														<th style="min-width: 40px !important">
															INGRESO_TARDE
														</th>
														<th style="min-width: 40px !important">
															SALIDA_TARDE
														</th>
														<th style="min-width: 40px !important">
															SÁBADO_INGRESO_MAÑANA
														</th>
														<th style="min-width: 40px !important">
															SÁBADO_SALIDA_MAÑANA
														</th>
														<th style="min-width: 20px !important">
															TOLERANCIA
														</th>
													</tr>
												</thead>
												<tbody>
													<tr
														v-for="(horario, index) in horarios"
														v-bind:key="index"
													>
														<td align="center">
															<input
																class="custom-radio"
																type="radio"
																name="radiobtn"
																:id="index"
																:value="horario.id"
																:ref="'opt_h_' + index"
																style="font-size: 20px"
																v-model="frmUsuarioHorario.horario_id"
															/>
														</td>
														<td align="left">
															{{ horario.horario }}
														</td>
														<td align="center">
															{{ horario.hora_entrada_mañana }}
														</td>
														<td align="center">
															{{ horario.hora_salida_mañana }}
														</td>
														<td align="center">
															{{ horario.hora_entrada_tarde }}
														</td>
														<td align="center">
															{{ horario.hora_salida_tarde }}
														</td>
														<td align="center">
															{{ horario.hora_entrada_mañana_s }}
														</td>
														<td align="center">
															{{ horario.hora_salida_mañana_s }}
														</td>
														<td align="center">
															{{ horario.tolerancia }}
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="GuardarCambios"
									>
										<span class="icon text-white">
											<i class="fas fa-save"></i>
										</span>
										<span class="text">GUARDAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="mdlAsignarTolerancia" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-30 mdlDatosHorario">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>{{ title_modal }}</strong>
								<button
									type="button"
									class="btn btn-action"
									style="border-radius: 50%; float: right !important"
									@click="Cerrar('mdlAsignarTolerancia')"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-title">DATOS TOLERANCIA</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-sm-8">
											<label class="label-title">COLABORADOR</label>
											<input
												type="text"
												class="form-control"
												v-model="frmUsuarioHorario.nombre_completo"
												:disabled="true"
											/>
										</div>
										<div class="form-group col-sm-4">
											<label class="label-title">AGENCIA</label>
											<input
												type="text"
												class="form-control center"
												style="max-width: 300px"
												v-model="frmUsuarioHorario.agencia"
												:disabled="true"
											/>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-sm-4">
											<label class="label-title">TOLERANCIA HORARIO</label>
											<input
												type="text"
												class="form-control center"
												style="max-width: 150px"
												v-model="frmUsuarioHorario.toleranciaHorario"
												:disabled="true"
											/>
										</div>
										<div class="form-group col-sm-4">
											<span
												v-if="
													!Number.isInteger(
														this.frmUsuarioHorario.toleranciaPersonal
													)
												"
												class="span-error-message"
											>
												*
											</span>
											<label class="label-title">TOLERANCIA PERSONAL</label>

											<input
												type="number"
												class="form-control center"
												style="max-width: 150px"
												min="0"
												v-model.number="frmUsuarioHorario.toleranciaPersonal"
											/>
										</div>
										<div class="form-group col-sm-4">
											<label class="label-title">TOLERANCIA TOTAL</label>
											<input
												type="text"
												class="form-control center"
												style="max-width: 150px"
												:value="
													String(
														parseInt(frmUsuarioHorario.toleranciaHorario) +
															parseInt(frmUsuarioHorario.toleranciaPersonal)
													) + ' minutos'
												"
												:disabled="true"
											/>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="GuardarTolerancia"
									>
										<span class="icon text-white">
											<i class="fas fa-check"></i>
										</span>
										<span class="text">ASIGNAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		horarios: Array,
		usuarios_horarios: Array,
		toleranciaPersonal: Number,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			title_modal: null,

			frmUsuarioHorario: {
				dni: null,
				nombre_completo: null,
				agencia: null,
				horario_id: null,
				habilitado: null,

				toleranciaHorario: null,
				toleranciaPersonal: this.toleranciaPersonal,
			},
		};
	},

	validations: {
		frmUsuarioHorario: { toleranciaPersonal: { required } },
	},

	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaHorariosUsuario();
		this.TablaDatosHorario();
	},

	methods: {
		Cerrar(modal) {
			$("#" + modal).css("display", "none");
		},
		// AñadirResponsive() {
		//   if (this.windowWidth < 1200) {
		//     if (!$("#t_usuarios_horarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("t_usuarios_horarios")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#t_usuarios_horarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("t_usuarios_horarios")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		TablaHorariosUsuario() {
			this.$nextTick(() => {
				var table = $("#t_usuarios_horarios").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,

					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
				});

				$("#cmbHorarios").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		TablaDatosHorario() {
			self = this;
			this.$nextTick(() => {
				var table = $("#t_datos_horarios").DataTable({
					scrollY: "300px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,

					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
				});
			});
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		CambiarHorario(usuario_horario) {
			this.submited = false;
			this.title_modal = "CAMBIAR HORARIO";

			this.frmUsuarioHorario.dni = usuario_horario.dni;
			this.frmUsuarioHorario.nombre_completo =
				usuario_horario.nombres +
				" " +
				usuario_horario.apellido_paterno +
				" " +
				usuario_horario.apellido_materno;
			this.frmUsuarioHorario.horario_id = usuario_horario.horario_id;
			this.frmUsuarioHorario.agencia = usuario_horario.agencia;
			this.frmUsuarioHorario.habilitado = usuario_horario.marca_asistencia;

			$("#mdlCambiarHorario").css("display", "block");
			$("#btnCancelar").click(function () {
				$("#mdlCambiarHorario").css("display", "none");
			});
		},
		GuardarCambios() {
			self = this;

			Swal.fire({
				title: "GUARDAR CAMBIOS",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					axios
						.post(
							route("gth.usu.usuarios_horarios.asignar_horario"),
							self.frmUsuarioHorario
						)
						.then(function (response) {
							let resultado = response.data;
							if (resultado == "EXITO") {
								Swal.fire({
									icon: "success",
									title: "¡EXITO!",
									text: "Horario asignado",
									allowOutsideClick: false,
									preConfirm: (result) => {
										$("#mdlCambiarHorario").css("display", "none");

										self.$inertia.get(route("gth.usu.usuarios_horarios"));
									},
								});
							} else {
								Swal.fire({
									icon: "error",
									title: "¡Ups!",
									text: "Algo salió mal",
								});
							}
						});
				},
			});
		},
		AsignarTolerancia(usuario_horario) {
			this.submited = false;
			this.title_modal = "ASIGNAR TOLERANCIA";

			this.frmUsuarioHorario.dni = usuario_horario.dni;
			this.frmUsuarioHorario.nombre_completo =
				usuario_horario.nombres +
				" " +
				usuario_horario.apellido_paterno +
				" " +
				usuario_horario.apellido_materno;
			this.frmUsuarioHorario.agencia = usuario_horario.agencia;
			this.frmUsuarioHorario.toleranciaHorario =
				usuario_horario.toleranciaHorario;
			this.frmUsuarioHorario.toleranciaPersonal =
				usuario_horario.tolerancia_personal;

			$("#mdlAsignarTolerancia").css("display", "block");
			$("#btnCancelarTolerancia").click(function () {
				$("#mdlAsignarTolerancia").css("display", "none");
			});
		},
		GuardarTolerancia() {
			self = this;
			this.submited = true;

			if (this.$v.frmUsuarioHorario.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			}

			if (!Number.isInteger(this.frmUsuarioHorario.toleranciaPersonal)) {
				return false;
			} else {
				Swal.fire({
					title: "ASIGNAR TOLERANCIA",
					text: "¿Desea continuar?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
					preConfirm: (result) => {
						let data = new FormData();
						data.append("dni", self.frmUsuarioHorario.dni);
						data.append(
							"toleranciaPersonal",
							self.frmUsuarioHorario.toleranciaPersonal
						);
						axios
							.post(route("gth.usu.usuarios_horarios.asignar_tolerancia"), data)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXITO") {
									Swal.fire({
										icon: "success",
										title: "¡EXITO!",
										text: "Tolerancia asignada",
										allowOutsideClick: false,
										preConfirm: (result) => {
											$("#mdlCambiarHorario").css("display", "none");

											self.$inertia.get(route("gth.usu.usuarios_horarios"));
										},
									});
								} else {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Algo salió mal",
									});
								}
							});
					},
				});
			}
		},
	},
};
</script>

<style >
.slot-horarios {
	width: 66% !important;
	margin-left: 17% !important;
}

.mdlDatosHorario {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-horarios {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosHorario {
		margin-top: 20%;
	}
}
.dataTable {
	width: 100% !important;
}
.dataTables_scrollHeadInner {
	width: 100% !important;
}
.DTFC_ScrollWrapper {
	height: auto !important;
}
</style>

