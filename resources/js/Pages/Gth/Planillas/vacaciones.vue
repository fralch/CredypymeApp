<template>
	<layout ref="layout">
		<div class="slot_body slot-vacaciones" slot="component-view">
			<div class="card">
				<headerClose :title="'VACACIONES'"></headerClose>

				<div class="card-title">PANEL DE BÚSQUEDA</div>
				<div class="card-body card-block">
					<div class="form-row col-md-9 col-12">
						<div class="form-group col-md-3 col-4">
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
					</div>
				</div>
				<div class="card-title">DATOS VACACIONES</div>
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
					<table class="table table-hover" id="tblDatosVacaciones" width="100%">
						<thead>
							<tr>
								<th>COLABORADOR</th>
								<th>ASIGNAR</th>
								<th>DNI</th>
								<th>AGENCIA</th>
								<th>F. ING. PLANILLA</th>
								<th>VAC. VENCIDAS</th>
								<th>VAC. TRUNCADAS</th>
								<th>VAC. INDEMNIZADAS</th>
								<th>VAC. ADELANTADAS</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(vacacion_usuario, index) in vacaciones_usuarios"
								:key="index"
							>
								<td
									class="table-bordered"
									style="background: var(--colorMedio)"
								>
									{{
										vacacion_usuario.apellido_paterno +
										" " +
										vacacion_usuario.apellido_materno +
										" " +
										vacacion_usuario.nombres
									}}
								</td>
								<td class="table-bordered" align="center">
									<button
										class="btn btn-action btn-icon-split mb-2"
										data-toggle="modal"
										v-on:click="AsignarVacaciones(vacacion_usuario)"
									>
										<span class="icon">
											<i class="fas fa-plus" style="color: white"></i>
										</span>
									</button>
								</td>

								<td class="table-bordered" align="center">
									{{ vacacion_usuario.dni }}
								</td>
								<td class="table-bordered" align="center">
									{{ vacacion_usuario.agencia }}
								</td>
								<td class="table-bordered" align="center">
									{{
										vacacion_usuario.fecha_ingreso_planilla == null
											? "-"
											: vacacion_usuario.fecha_ingreso_planilla
									}}
								</td>
								<td class="table-bordered" align="center">
									{{
										vacacion_usuario.vencidas == null
											? "-"
											: roundTo(vacacion_usuario.vencidas, 2)
									}}
								</td>
								<td class="table-bordered" align="center">
									{{
										vacacion_usuario.truncadas == null
											? "-"
											: roundTo(vacacion_usuario.truncadas, 2)
									}}
								</td>
								<td class="table-bordered" align="center">
									{{
										vacacion_usuario.indemnizadas == null
											? "-"
											: roundTo(vacacion_usuario.indemnizadas, 2)
									}}
								</td>
								<td class="table-bordered" align="center">
									{{
										vacacion_usuario.adelantadas == null
											? "-"
											: roundTo(vacacion_usuario.adelantadas, 2)
									}}
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div id="mdlAsignarVacaciones" class="modal">
				<div class="modal-content w-35 mdlAsignarVacaciones">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>ASIGNAR VACACIONES</strong>
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

							<div class="card-title">INFORMACIÓN</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-md-8">
											<label
												for="inpNombre"
												class="form-control-label label-title"
												>NOMBRES Y APELLIDOS</label
											>
											<input
												type="text"
												class="form-control"
												id="inpNombre"
												:disabled="true"
												v-model="frmAsignarVacaciones.colaborador"
											/>
										</div>
										<div class="form-group col-md-4">
											<label
												for="inpAgencia"
												class="form-control-label label-title"
												>AGENCIA</label
											>
											<input
												type="text"
												class="form-control center"
												id="inpAgencia"
												:disabled="true"
												v-model="frmAsignarVacaciones.agencia"
											/>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-4">
											<label
												for="inpFechaDesde"
												class="form-control-label label-title"
												>FECHA DESDE</label
											>
											<input
												type="date"
												class="form-control center"
												id="inpFechaDesde"
												name="fecha_desde"
												onkeydown="return false"
												v-model="frmAsignarVacaciones.fecha_desde"
												@change="CalcularDias"
											/>
										</div>
										<div class="form-group col-md-4">
											<label
												for="inpFechaHasta"
												class="form-control-label label-title"
												>FECHA HASTA</label
											>
											<input
												type="date"
												class="form-control center"
												id="inpFechaHasta"
												name="fecha_hasta"
												onkeydown="return false"
												v-model="frmAsignarVacaciones.fecha_hasta"
												@change="CalcularDias"
											/>
										</div>

										<div class="form-group col-md-4">
											<label
												for="inpDiasTomados"
												class="form-control-label label-title"
												>DIAS A TOMAR</label
											>
											<input
												type="text"
												class="form-control center"
												id="inpDiasTomados"
												v-model="frmAsignarVacaciones.dias_a_tomar"
												readonly
											/>
										</div>
									</div>
									<!-- ------- PERIODO ------- -->

									<table class="table table-hover" id="tablaDatos">
										<thead>
											<tr>
												<th>PERIODO</th>
												<th>ACUMULADO</th>
												<th>DIAS A TOMAR</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(
													periodo, index
												) in frmAsignarVacaciones.periodos_filtrado"
												:key="index"
											>
												<td
													class="table-bordered"
													style="background: var(--colorMedio)"
													align="center"
												>
													{{
														periodo.periodo_desde +
														" / " +
														periodo.periodo_hasta
													}}
												</td>
												<td class="table-bordered" align="center">
													{{ roundTo(periodo.acumulado, 2) }}
												</td>
												<td class="table-bordered" align="center">
													{{ periodo.dias_a_tomar }}
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<td
													span="0"
													class="table-bordered"
													align="center"
													style="
														background: var(--plomoClaroEmpresarial);
														color: white;
													"
												>
													TOTAL
												</td>
												<td
													class="table-bordered"
													align="center"
													style="
														background: var(--plomoClaroEmpresarial);
														color: white;
													"
												>
													{{ frmAsignarVacaciones.total_acumulado }}
												</td>
												<td
													class="table-bordered"
													align="center"
													style="
														background: var(--plomoClaroEmpresarial);
														color: white;
													"
												>
													{{ frmAsignarVacaciones.dias_a_tomar }}
												</td>
											</tr>
										</tfoot>
									</table>
									<div
										v-if="frmAsignarVacaciones.mensaje != null"
										style="color: red; font-size: 12px"
									>
										*{{ frmAsignarVacaciones.mensaje }}
									</div>

									<hr />
								</form>
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										id="btnGuardarAsignacion"
										@click="GuardarAsignacion"
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
		</div>
	</layout>
</template>


<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		agencias: Array,
		usuarios: Array,
		vacaciones_usuarios: Array,
		periodos_usuarios: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			frmAsignarVacaciones: {
				dni: null,
				colaborador: null,
				agencia: null,
				fecha_desde: null,
				fecha_hasta: null,
				dias_a_tomar: null,
				periodos_filtrado: [],
				total_acumulado: null,
			},
		};
	},

	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaDatosVacaciones();
	},

	validations() {
		if (this.frmAsignarVacaciones.modo == "NUEVO") {
			return {
				frmAsignarVacaciones: {
					fecha_desde: { required },
					fecha_hasta: { required },
				},
			};
		}
	},

	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblDatosVacaciones").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblDatosVacaciones")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblDatosVacaciones").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblDatosVacaciones")
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
		TablaDatosVacaciones() {
			this.$nextTick(() => {
				var table = $("#tblDatosVacaciones").DataTable({
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
			});
		},
		AsignarVacaciones(vacacion_usuario) {
			let self = this;
			this.frmAsignarVacaciones.dni = vacacion_usuario.dni;
			this.frmAsignarVacaciones.colaborador =
				vacacion_usuario.apellido_paterno +
				" " +
				vacacion_usuario.apellido_materno +
				" " +
				vacacion_usuario.nombres;
			this.frmAsignarVacaciones.agencia = vacacion_usuario.agencia;
			this.frmAsignarVacaciones.fecha_desde = null;
			this.frmAsignarVacaciones.fecha_hasta = null;
			this.frmAsignarVacaciones.dias_a_tomar = 0;
			this.frmAsignarVacaciones.mensaje = null;
			this.frmAsignarVacaciones.periodos_filtrado =
				this.periodos_usuarios.filter(
					(item) => item.dni == vacacion_usuario.dni
				);

			this.frmAsignarVacaciones.total_acumulado = 0;
			this.frmAsignarVacaciones.periodos_filtrado.forEach(function (
				element,
				index
			) {
				self.frmAsignarVacaciones.total_acumulado += parseFloat(
					self.roundTo(element.acumulado, 2)
				);
			});

			$("#mdlAsignarVacaciones").css("display", "block");
			$("#btnCancelar").click(function () {
				$("#mdlAsignarVacaciones").css("display", "none");
			});
		},
		CalcularDias(e) {
			let self = this;
			let fecha_desde;
			if (this.frmAsignarVacaciones.fecha_desde) {
				fecha_desde = this.frmAsignarVacaciones.fecha_desde;
			} else {
				fecha_desde = null;
			}
			let fecha_hasta;
			if (this.frmAsignarVacaciones.fecha_hasta) {
				fecha_hasta = this.frmAsignarVacaciones.fecha_hasta;
			} else {
				fecha_hasta = null;
			}
			this.frmAsignarVacaciones.periodos_filtrado.forEach(function (
				element,
				index
			) {
				element.dias_a_tomar = 0;
			});
			if (fecha_desde == null || fecha_hasta == null) {
				this.frmAsignarVacaciones.dias_a_tomar = 0;
			} else if (fecha_hasta <= fecha_desde) {
				this.frmAsignarVacaciones.dias_a_tomar = 0;
			} else {
				let fecha_desde_1 = moment(fecha_desde, "YYYY.MM.DD");
				let fecha_hasta_1 = moment(fecha_hasta, "YYYY.MM.DD");
				let dias_a_tomar = fecha_hasta_1.diff(fecha_desde_1, "days");
				this.frmAsignarVacaciones.dias_a_tomar = dias_a_tomar;
				let restante = dias_a_tomar;
				let total_acumulado = 0;
				this.frmAsignarVacaciones.periodos_filtrado.forEach(function (
					element,
					index
				) {
					let acumulado = self.roundTo(element.acumulado, 2);
					total_acumulado += parseFloat(acumulado);
					if (restante == 0) {
						element.dias_a_tomar = 0;
					} else if (acumulado >= restante) {
						element.dias_a_tomar = self.roundTo(restante, 2);
						restante = 0;
						if (acumulado == restante) {
						}
					} else {
						element.dias_a_tomar = acumulado;
						restante -= acumulado;
					}
				});
				let diferencia_dias = total_acumulado - dias_a_tomar;
				if (diferencia_dias < 0) {
					this.frmAsignarVacaciones.mensaje =
						"La cantidad de días a tomar, es mayor a lo acumulado.";
				} else {
					this.frmAsignarVacaciones.mensaje = null;
				}
			}
		},
		GuardarAsignacion() {
			let self = this;
			this.submited = true;
			if (this.mensaje != null) {
				return false;
			} else {
				if (this.frmAsignarVacaciones.dias_a_tomar == 0) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "La cantidad de días a asignar no puede ser 0",
						allowOutsideClick: false,
					});
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
							axios
								.post(
									route("gth.pla.vacaciones.asignar"),
									self.frmAsignarVacaciones
								)
								.then(function (response) {
									let resultado = response.data;
									if (resultado == "EXITO") {
										Swal.fire({
											icon: "success",
											title: "¡EXITO!",
											text: "Información registrada",
											allowOutsideClick: false,
											preConfirm: (result) => {
												$("#mdlAsignarVacaciones").css("display", "none");
												self.$inertia.get(route("gth.pla.vacaciones"));
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
			}
		},
		Cerrar() {
			$("#mdlAsignarVacaciones").css("display", "none");
		},
	},
};
</script>

<style lang="css">
/* .DTFC_LeftHeadWrapper {
	height: 86px !important;
} */

.slot-vacaciones {
	width: 60% !important;
	margin-left: 20% !important;
}
.mdlAsignarVacaciones {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-vacaciones {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlAsignarVacaciones {
		margin-top: 20%;
	}
}
</style>
