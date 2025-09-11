<template>
	<layout ref="layout">
		<div class="slot_body slot-horarios" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'GESTIÓN DE HORARIOS'"></headerClose>

					<div class="card-title">HORARIOS DE LUNES A VIERNES</div>
					<div class="card-body card-block">
						<div class="text-center mb-1">
							<button
								class="btn btn-action btn-icon-split"
								id="inpNuevoHorario"
								@click="NuevoHorario"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<span class="text">NUEVO HORARIO</span>
							</button>
						</div>

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
						<table class="table table-hover" id="tblHorarios" width="100%">
							<thead>
								<tr>
									<th style="width: 70px !important">EDITAR</th>
									<th>NOMBRE</th>
									<th>INGRESO MAÑANA<br />(L-V)</th>
									<th>SALIDA MAÑANA<br />(L-V)</th>
									<th>INGRESO TARDE<br />(L-V)</th>
									<th>SALIDA TARDE<br />(L-V)</th>
									<th>INGRESO MAÑANA<br />(SAB)</th>
									<th>SALIDA MAÑANA<br />(SAB)</th>
									<th>TOLERANCIA MINUTOS</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(horario, index) in horarios" :key="index">
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											id="btnEditarHorario"
											@click.prevent="EditarHorario(horario)"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="center">
										{{ horario.horario }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.hora_entrada_mañana }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.hora_salida_mañana }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.hora_entrada_tarde }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.hora_salida_tarde }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.hora_entrada_mañana_s }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.hora_salida_mañana_s }}
									</td>
									<td class="table-bordered" align="center">
										{{ horario.tolerancia }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosHorario" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-50 mdlDatosHorario">
					<!-- <div class="modal-body"> -->
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
									@click="Cerrar"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-title">DATOS HORARIO</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-md-3">
											<label
												for="txtNombresHorario"
												class="form-control-label label-title"
												>NOMBRE</label
											>
											<input
												type="text"
												class="form-control"
												id="txtNombreHorario"
												name="nombreHorario"
												v-model="frmDatosHorario.horario"
											/>
											<div
												v-if="submited && !$v.frmDatosHorario.horario.required"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<div class="form-group col-md-1">
											<label
												for="inpTolerancia"
												class="form-control-label label-title"
												>TOLERANCIA</label
											>
											<input
												type="number"
												class="form-control center"
												style="max-width: 100px"
												id="inpTolerancia"
												min="0"
												name="tolerancia"
												v-model.number="frmDatosHorario.tolerancia"
											/>

											<div
												v-if="
													submited && !$v.frmDatosHorario.tolerancia.numeric
												"
												style="color: red; font-size: 12px"
											>
												*Sólo números
											</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-sm-12">
											<label
												for="tblDatosHorario"
												class="form-control-label label-title"
												>REGISTRO DE HORAS</label
											>
											<table
												class="table table-hover"
												id="tblDatosHorario"
												width="100%"
											>
												<thead>
													<tr>
														<th>INGRESO MAÑANA<br />(L-V)</th>
														<th>SALIDA MAÑANA<br />(L-V)</th>
														<th>INGRESO TARDE<br />(L-V)</th>
														<th>SALIDA TARDE<br />(L-V)</th>
														<th>INGRESO MAÑANA<br />(SAB)</th>
														<th>SALIDA MAÑANA<br />(SAB)</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td align="center">
															<input
																type="time"
																class="form-control"
																v-model="frmDatosHorario.hora_entrada_mañana"
															/>
														</td>
														<td align="center">
															<input
																type="time"
																class="form-control"
																v-model="frmDatosHorario.hora_salida_mañana"
															/>
														</td>
														<td align="center">
															<input
																type="time"
																class="form-control"
																v-model="frmDatosHorario.hora_entrada_tarde"
															/>
														</td>
														<td align="center">
															<input
																type="time"
																class="form-control"
																v-model="frmDatosHorario.hora_salida_tarde"
															/>
														</td>
														<td align="center">
															<input
																type="time"
																class="form-control"
																v-model="frmDatosHorario.hora_entrada_mañana_s"
															/>
														</td>
														<td align="center">
															<input
																type="time"
																class="form-control"
																v-model="frmDatosHorario.hora_salida_mañana_s"
															/>
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
										id="btnGuardarCambios"
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
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";
const isNumber = (value) => Number.isInteger(value);
export default {
	components: {
		layout,
		headerClose,
	},
	props: { horarios: Array },
	data: () => ({
		windowWidth: window.innerWidth,
		submited: null,
		title_modal: null,
		frmDatosHorario: {
			modo: null,
			id: null,
			horario: null,
			tolerancia: null,
			hora_entrada_mañana: null,
			hora_salida_mañana: null,
			hora_entrada_tarde: null,
			hora_salida_tarde: null,
			hora_entrada_mañana_s: null,
			hora_salida_mañana_s: null,
		},
	}),
	validations: {
		frmDatosHorario: {
			tolerancia: { numeric: isNumber },
			horario: { required },
		},
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaHorarios();
		this.tblDatosHorario();
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblHorarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblHorarios")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblHorarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblHorarios")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		TablaHorarios() {
			self = this;
			this.$nextTick(() => {
				var table = $("#tblHorarios").DataTable({
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

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		tblDatosHorario() {
			self = this;
			this.$nextTick(() => {
				$("#tblDatosHorario").DataTable({
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
			});
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		EditarHorario(horario) {
			this.submited = false;

			this.title_modal = "EDITAR HORARIO";
			this.frmDatosHorario.modo = "EDITAR";
			this.frmDatosHorario.id = horario.id;
			this.frmDatosHorario.horario = horario.horario;
			this.frmDatosHorario.tolerancia = horario.tolerancia;
			this.frmDatosHorario.hora_entrada_mañana = horario.hora_entrada_mañana;
			this.frmDatosHorario.hora_salida_mañana = horario.hora_salida_mañana;
			this.frmDatosHorario.hora_entrada_tarde = horario.hora_entrada_tarde;
			this.frmDatosHorario.hora_salida_tarde = horario.hora_salida_tarde;
			this.frmDatosHorario.hora_entrada_mañana_s =
				horario.hora_entrada_mañana_s;
			this.frmDatosHorario.hora_salida_mañana_s = horario.hora_salida_mañana_s;

			$("#mdlDatosHorario").css("display", "block");
			$("#btnCancelar").click(function () {
				$("#mdlDatosHorario").css("display", "none");
			});
		},
		NuevoHorario() {
			this.submited = false;

			this.title_modal = "NUEVO HORARIO";
			this.frmDatosHorario.modo = "NUEVO";
			this.frmDatosHorario.id = null;
			this.frmDatosHorario.horario = null;
			this.frmDatosHorario.tolerancia = 0;
			this.frmDatosHorario.hora_entrada_mañana = null;
			this.frmDatosHorario.hora_salida_mañana = null;
			this.frmDatosHorario.hora_entrada_tarde = null;
			this.frmDatosHorario.hora_salida_tarde = null;
			this.frmDatosHorario.hora_entrada_mañana_s = null;
			this.frmDatosHorario.hora_salida_mañana_s = null;

			$("#mdlDatosHorario").css("display", "block");
			$("#btnCancelar").click(function () {
				$("#mdlDatosHorario").css("display", "none");
			});
		},
		GuardarCambios() {
			let self = this;
			this.submited = true;

			if (this.$v.frmDatosHorario.$invalid) {
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
							route("gth.us_as.horarios.guardar"),
							self.frmDatosHorario,
							{
								preserveScroll: true,
								onSuccess: () => {
									Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										text: "Registro exitoso",
										allowOutsideClick: false,
										preConfirm: (result) => {
											$("#mdlDatosHorario").css("display", "none");
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
			$("#mdlDatosHorario").css("display", "none");
		},
	},
};
</script>

<style >
.slot-horarios {
	width: 60% !important;
	margin-left: 20% !important;
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
/* Para corregir bug de datatable */
.dataTable {
	width: 100% !important;
}
.dataTables_scrollHeadInner {
	width: 100% !important;
}
.DTFC_ScrollWrapper {
	height: auto !important;
}
/* --------------------------------- */
</style>
