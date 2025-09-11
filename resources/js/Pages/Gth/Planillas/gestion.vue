<template>
	<layout ref="layout">
		<div class="slot_body slot-gestion" slot="component-view">
			<div class="card">
				<headerClose :title="'DATOS DE USUARIOS-PLANILLAS'"></headerClose>
				<div class="card-title">PANEL DE BÚSQUEDA</div>
				<div class="card-body card-block">
					<div class="form-row col-md-9 col-12">
						<div class="form-group col-md-3 col-6">
							<label class="form-control-label label-title" for="slcAgencias"
								>POR AGENCIA</label
							>
							<select
								class="form-control center"
								id="slcAgencias"
								data-index="3"
							>
								<option value="0">Todas</option>
								<option v-for="agencia in agencias" :key="agencia.id_agencia">
									{{ agencia.nombre }}
								</option>
							</select>
						</div>
						<div class="form-group col-md-3 col-4">
							<label class="form-control-label label-title" for="slcEnPlanilla"
								>EN PLANILLA</label
							>
							<select
								id="slcEnPlanilla"
								class="form-control center"
								data-index="6"
							>
								<option :value="0">Todos</option>
								<option>Si</option>
								<option>No</option>
							</select>
						</div>
						<div class="form-group col-md-3 col-4">
							<label
								class="form-control-label label-title"
								for="slcSistemaPensiones"
								>SIS. PENSIÓN
							</label>
							<select
								id="slcSistemaPensiones"
								class="form-control center"
								data-index="7"
							>
								<option :value="0">Todas</option>
								<option>AFP</option>
								<option>ONP</option>
							</select>
						</div>
					</div>
				</div>
				<div class="card-title">RESULTADOS DE BÚSQUEDA</div>
				<div class="card-body card-block">
					<div class="input-group row col-md-10 col-7">
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
					<div id="tabla">
						<table class="table table-hover" id="tblDatosUsuarios" width="100%">
							<thead>
								<tr>
									<th>NOMBRES Y APELLIDOS</th>
									<th>DNI</th>
									<th style="width: 70px !important">EDITAR</th>
									<th>AGENCIA</th>
									<th>REMUNERACIÓN BÁSICA</th>
									<th>REMUNERACIÓN REAL</th>
									<th>PLANILLA</th>
									<th>SISTEMA PENSIÓN</th>
									<th>NOMBRE SISTEMA</th>
									<th>TIPO DE COMISIÓN</th>
									<th>CUSPP</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(planillas_dato, index) in planillas_datos"
									:key="index"
								>
									<td
										class="table-bordered"
										style="background: var(--colorMedio)"
									>
										{{
											planillas_dato.apellido_paterno +
											" " +
											planillas_dato.apellido_materno +
											" " +
											planillas_dato.nombres
										}}
									</td>
									<td class="table-bordered" align="center">
										{{ planillas_dato.dni }}
									</td>
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="EditarDatosPlanilla(planillas_dato)"
										>
											<span class="icon text-white">
												<i class="far fa-edit" style="color: white"></i>
											</span>
										</button>
									</td>

									<td class="table-bordered" align="center">
										{{ planillas_dato.agencia }}
									</td>
									<td class="table-bordered" align="center">
										S/ {{ roundTo(planillas_dato.remuneracion_basica, 2) }}
									</td>
									<td class="table-bordered" align="center">
										S/ {{ roundTo(planillas_dato.remuneracion_real, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{ planillas_dato.planilla ? "Si" : "No" }}
									</td>
									<td class="table-bordered" align="center">
										{{ !planillas_dato.planilla ? "-" : planillas_dato.tipo }}
									</td>
									<td class="table-bordered" align="center">
										{{
											!planillas_dato.planilla
												? "-"
												: planillas_dato.nombre_sis_pen
										}}
									</td>
									<td class="table-bordered" align="center">
										{{
											!planillas_dato.planilla || planillas_dato.tipo == "ONP"
												? "-"
												: planillas_dato.tipo_comision
										}}
									</td>
									<td class="table-bordered" align="center">
										{{
											!planillas_dato.planilla || planillas_dato.tipo == "ONP"
												? "-"
												: planillas_dato.cuspp
										}}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="mdlEditarDatos" class="modal">
				<div class="modal-content w-40 mdlEditarDatos">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>EDITAR DATOS</strong>
								<button
									type="button"
									class="btn btn-action"
									style="border-radius: 50%; float: right !important"
									@click="Cerrar"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-title">INFORMACIÓN PERSONAL</div>
							<div class="card-body card-block">
								<form @submit.prevent="GuardarCambios">
									<div class="form-row">
										<div class="form-group col-md-4 col-4">
											<label for="inpDni" class="form-control-label label-title"
												>DNI</label
											>
											<input
												type="text"
												class="form-control center"
												id="inpDni"
												v-model="frmDatosPlanilla.dni"
												:disabled="true"
											/>
										</div>
										<div class="form-group col-md-4 col-8">
											<label
												for="inpNomApe"
												class="form-control-label label-title"
												>COLABORADOR</label
											>
											<textarea
												type="text"
												class="form-control"
												id="txtNombreCompleto"
												rows="1"
												v-model="frmDatosPlanilla.nombrecompleto"
												:disabled="true"
											></textarea>
										</div>
										<div class="form-group col-md-4 col-7">
											<label
												for="slcAgenciasModal"
												class="form-control-label label-title"
												>AGENCIA</label
											>
											<input
												class="form-control center"
												id="inpAgencia"
												v-model="frmDatosPlanilla.agencia"
												:disabled="true"
											/>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-3 col-6">
											<label
												for="inpFechaIngreso"
												class="form-control-label label-title"
												>FECHA DE INGRESO</label
											>
											<input
												type="date"
												class="form-control center"
												id="inpFechaIngreso"
												v-model="frmDatosPlanilla.fecha_ingreso"
											/>
											<div
												v-if="
													submited &&
													!$v.frmDatosPlanilla.fecha_ingreso.required
												"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<div class="form-group col-md-3 col-6">
											<label
												for="inpRemBasic"
												class="form-control-label label-title"
												>REM. BÁSICA</label
											>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">S/</div>
												</div>
												<input
													type="number"
													class="form-control center"
													id="inpRemBasic"
													min="1"
													step="0.01"
													lang="en"
													v-model="frmDatosPlanilla.remuneracion_basica"
												/>
												<div
													v-if="
														submited &&
														!$v.frmDatosPlanilla.remuneracion_basica.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>

										<div class="form-group col-md-3 col-6">
											<label
												for="inpRemReal"
												class="form-control-label label-title"
												>REM. REAL</label
											>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">S/</div>
												</div>
												<input
													type="number"
													class="form-control center"
													id="inpRemReal"
													min="1"
													step="0.01"
													lang="en"
													v-model="frmDatosPlanilla.remuneracion_real"
												/>
												<div
													v-if="
														submited &&
														!$v.frmDatosPlanilla.remuneracion_real.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>
										<div class="form-group col-md-3 col-6">
											<label
												for="slcEnPlanillaModal"
												class="form-control-label label-title"
												>EN PLANILLA</label
											>
											<select
												class="form-control center"
												id="slcEnPlanillaModal"
												v-model.number="frmDatosPlanilla.planilla"
											>
												<option value="1">Si</option>
												<option value="0">No</option>
											</select>
										</div>
									</div>
									<div
										id="planilla_content"
										v-if="frmDatosPlanilla.planilla == 1"
									>
										<div class="form-row">
											<div class="form-group col-md-4 col-8">
												<label
													for="inpFechaPlanilla"
													class="form-control-label label-title"
													>FECHA INGRESO PLANILLA</label
												>
												<input
													type="date"
													class="form-control center"
													id="inpFechaPlanilla"
													v-model="frmDatosPlanilla.fecha_ingreso_planilla"
													:disabled="frmDatosPlanilla.planilla == 0"
												/>
												<div
													v-if="
														submited &&
														frmDatosPlanilla.planilla == 1 &&
														!$v.frmDatosPlanilla.fecha_ingreso_planilla.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
											<div class="form-group col-md-4 col-4">
												<label
													for="slcSistemasPensionesModal"
													class="form-control-label label-title"
													>SIS. PENSIÓN</label
												>
												<select
													class="form-control center"
													id="slcSistemasPensionesModal"
													v-model="frmDatosPlanilla.tipo"
													:disabled="frmDatosPlanilla.planilla == 0"
													@change="FiltrarSistemaPensiones"
												>
													<option value="ONP">ONP</option>
													<option value="AFP">AFP</option>
												</select>
											</div>

											<div class="form-group col-md-4">
												<label
													for="slcNombresSistemasPensiones"
													class="form-control-label label-title"
													>NOMBRE SIS. PENSIONES</label
												>
												<select
													class="form-control center"
													id="slcNombresSistemasPensiones"
													v-model.number="frmDatosPlanilla.id_sis_pensiones"
													:disabled="frmDatosPlanilla.planilla == 0"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="nom_sis_pen in sistemas_pensiones_filtros"
														:key="nom_sis_pen.id_sis_pensiones"
														:value="nom_sis_pen.id_sis_pensiones"
													>
														{{
															frmDatosPlanilla.tipo == "ONP"
																? nom_sis_pen.nombre
																: nom_sis_pen.nombre +
																  " - " +
																  nom_sis_pen.tipo_comision
														}}
													</option>
												</select>
												<div
													v-if="
														submited &&
														frmDatosPlanilla.planilla == 1 &&
														!$v.frmDatosPlanilla.id_sis_pensiones.noZero
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-4 col-4">
												<label
													for="inpCuspp"
													class="form-control-label label-title"
													>CUSPP</label
												>
												<input
													type="text"
													class="form-control center"
													id="inpCuspp"
													v-model="frmDatosPlanilla.cuspp"
													:disabled="frmDatosPlanilla.tipo != 'AFP'"
												/>
												<div
													v-if="
														submited &&
														frmDatosPlanilla.planilla == 1 &&
														frmDatosPlanilla.tipo == 'AFP' &&
														!$v.frmDatosPlanilla.cuspp.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>
									</div>
									<hr />
									<div class="text-right">
										<button
											class="btn btn-action btn-icon-split"
											title="Guardar cambios"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text">GUARDAR</span>
										</button>
									</div>
								</form>
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
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		planillas_datos: Array,
		agencias: Array,
		siste_pensiones: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			sistemas_pensiones_filtros: this.siste_pensiones,
			frmDatosPlanilla: {
				id_planillas_usuarios: null,
				dni: null,
				nombrecompleto: null,
				agencia: null,
				fecha_ingreso: null,
				remuneracion_basica: null,
				remuneracion_real: null,
				planilla: null,
				fecha_ingreso_planilla: null,
				tipo: null,
				id_sis_pensiones: null,
				cuspp: null,
			},
		};
	},
	validations() {
		if (this.frmDatosPlanilla.planilla == 1) {
			if (this.frmDatosPlanilla.tipo == "AFP") {
				return {
					frmDatosPlanilla: {
						fecha_ingreso: { required },
						remuneracion_basica: { required },
						remuneracion_real: { required },
						fecha_ingreso_planilla: { required },
						id_sis_pensiones: { noZero },
						cuspp: { required },
					},
				};
			} else if (this.frmDatosPlanilla.tipo == "ONP") {
				return {
					frmDatosPlanilla: {
						fecha_ingreso: { required },
						remuneracion_basica: { required },
						remuneracion_real: { required },
						fecha_ingreso_planilla: { required },
						id_sis_pensiones: { noZero },
					},
				};
			}
		} else {
			return {
				frmDatosPlanilla: {
					fecha_ingreso: { required },
					remuneracion_basica: { required },
					remuneracion_real: { required },
				},
			};
		}
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaDatosUsuarios();
	},

	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblDatosUsuarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblDatosUsuarios")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblDatosUsuarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblDatosUsuarios")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				var power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},
		TablaDatosUsuarios() {
			this.$nextTick(() => {
				var table = $("#tblDatosUsuarios").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},

					scrollCollapse: true,
					paging: false,
					order: [1, "asc"],
					fixedHeader: true,
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
					responsive: true,
				});

				// Filter event handler

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});

				$("#slcAgencias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#slcSistemaPensiones").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#slcEnPlanilla").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});
			});
		},

		FiltrarSistemaPensiones(e) {
			let nombre_sis_pensiones = e.target.value;
			this.sistemas_pensiones_filtros = this.siste_pensiones.filter(
				(item) => item.tipo == nombre_sis_pensiones
			);
			this.frmDatosPlanilla.id_sis_pensiones = 0;
			if (nombre_sis_pensiones == "ONP") {
				this.frmDatosPlanilla.cuspp = null;
			}
		},

		EditarDatosPlanilla(planillas_dato) {
			this.submited = false;
			this.frmDatosPlanilla.dni = planillas_dato.dni;
			this.frmDatosPlanilla.nombrecompleto =
				planillas_dato.apellido_paterno +
				" " +
				planillas_dato.apellido_materno +
				" " +
				planillas_dato.nombres;
			this.frmDatosPlanilla.agencia = planillas_dato.agencia;
			this.frmDatosPlanilla.fecha_ingreso = planillas_dato.fecha_ingreso;
			this.frmDatosPlanilla.remuneracion_basica =
				planillas_dato.remuneracion_basica;
			this.frmDatosPlanilla.remuneracion_real =
				planillas_dato.remuneracion_real;
			this.frmDatosPlanilla.planilla = planillas_dato.planilla;

			if (planillas_dato.planilla == 1) {
				this.frmDatosPlanilla.fecha_ingreso_planilla =
					planillas_dato.fecha_ingreso_planilla;
				this.frmDatosPlanilla.tipo = planillas_dato.tipo;
				this.sistemas_pensiones_filtros = this.siste_pensiones.filter(
					(item) => item.tipo == this.frmDatosPlanilla.tipo
				);
				this.frmDatosPlanilla.id_sis_pensiones =
					planillas_dato.id_sis_pensiones;
				this.frmDatosPlanilla.cuspp = planillas_dato.cuspp;
			} else {
				this.frmDatosPlanilla.fecha_ingreso_planilla = null;
				this.frmDatosPlanilla.tipo = "ONP";
				this.sistemas_pensiones_filtros = this.siste_pensiones.filter(
					(item) => item.tipo == this.frmDatosPlanilla.tipo
				);
				this.frmDatosPlanilla.id_sis_pensiones = 0;
				this.frmDatosPlanilla.cuspp = null;
			}

			$("#mdlEditarDatos").css("display", "block");

			$("#btnCancelar").click(function () {
				$("#mdlEditarDatos").css("display", "none");
			});
		},

		GuardarCambios() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosPlanilla.$invalid) {
				return false;
			} else {
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
						self.$inertia.post(
							route("gth.pla.datos_usuarios.guardar"),
							self.frmDatosPlanilla,
							{
								preserveScroll: true,
								onStart: (visit) => {
									let timerInterval;
									Swal.fire({
										title: "CARGANDO",
										html: "Espere porfavor...",
										timer: 5000,
										allowOutsideClick: false,
										timerProgressBar: true,
										didOpen: () => {
											Swal.showLoading();
											timerInterval = setInterval(() => {
												const content = Swal.getContent();
												if (content) {
													const b = content.querySelector("b");
													if (b) {
														b.textContent = Swal.getTimerLeft();
													}
												}
											}, 100);
										},
										willClose: () => {
											clearInterval(timerInterval);
										},
									});
								},
								onSuccess: () => {
									Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										allowOutsideClick: false,
										preConfirm: (result) => {
											$("#mdlEditarDatos").css("display", "none");
										},
									});
								},
							}
						);
					},
				});
			}
		},
		Cerrar() {
			$("#mdlEditarDatos").css("display", "none");
		},
	},
};
</script>

<style >
.slot-gestion {
	width: 70% !important;
	margin-left: 15% !important;
}
.mdlEditarDatos {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-gestion {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlEditarDatos {
		margin-top: 20%;
	}
}
</style>


