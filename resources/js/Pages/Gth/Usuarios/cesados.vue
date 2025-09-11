<template>
	<layout ref="layout">
		<div class="slot_body slot-cesados" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'USUARIOS CESADOS'"></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-4">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">AGENCIA</span>
								</div>
								<select
									class="form-control center"
									id="cmbAgenciasUC"
									data-index="3"
								>
									<option value="0">TODAS</option>
									<option
										v-for="agencia in agencias"
										v-bind:key="agencia.id_agencia"
									>
										{{ agencia.nombre }}
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
									id="inpBuscar_uc"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE USUARIOS</div>
					<div class="card-body card-block">
						<div id="tabla_cesados">
							<table
								class="table table-hover"
								id="t_usuarios_cesados"
								width="100%"
							>
								<thead>
									<tr>
										<th style="width: 70px !important">INFORMACIÓN</th>
										<th style="min-width: 40px !important">DNI</th>
										<th style="min-width: 150px !important">COLABORADOR</th>

										<th style="min-width: 40px !important">AGENCIA</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(item, index) in usuarios_cesados" :key="index">
										<td class="table-bordered" align="center">
											<button
												class="btn btn-action btn-icon-split"
												@click="VerInformacion(item)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-eye" style="color: white"></i>
												</span>
											</button>
										</td>
										<td class="table-bordered" align="center">
											{{ item.dni }}
										</td>

										<td class="table-bordered" width="350px">
											{{
												item.apellido_paterno +
												" " +
												item.apellido_materno +
												" " +
												item.nombres
											}}
										</td>

										<td class="table-bordered" align="center">
											{{ item.nombre_agencia }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="modalDatosCesado" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-35 modalDatosCesado">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="
									card-header
									d-flex
									align-items-center
									justify-content-between
								"
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

							<div class="card-title">INFORMACION PERSONAL</div>
							<div class="card-body card-block">
								<ul class="nav nav-tabs" id="myTab" role="tablist">
									<li class="nav-item" role="presentation">
										<a
											class="nav-link tab-title"
											id="datosCesado3-tab"
											data-toggle="tab"
											href="#datosCesado3"
											role="tab"
											aria-controls="datosCesado3"
											aria-selected="true"
											>HISTORIAL</a
										>
									</li>
									<li class="nav-item" role="presentation">
										<a
											class="nav-link active tab-title"
											id="datosCesado1-tab"
											data-toggle="tab"
											href="#datosCesado1"
											role="tab"
											aria-controls="datosCesado1"
											aria-selected="false"
											>DATOS PERSONALES</a
										>
									</li>
									<li class="nav-item" role="presentation">
										<a
											class="nav-link tab-title"
											id="datosCesado2-tab"
											data-toggle="tab"
											href="#datosCesado2"
											role="tab"
											aria-controls="datosCesado2"
											aria-selected="false"
											><i class="fas fa-plus-square"></i
										></a>
									</li>
								</ul>
								<div class="tab-content" id="myTabContent">
									<div
										class="tab-pane fade show active"
										id="datosCesado1"
										role="tabpanel"
										aria-labelledby="datosCesado1-tab"
									>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label
													for="txtDni"
													class="form-control-label label-title"
													>DNI</label
												>
												<input
													type="text"
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.dni_unlock"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtUsuario"
													class="form-control-label label-title"
													>USUARIO</label
												>
												<input
													type="text"
													id="txtUsuario"
													name="usuario"
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.usuario"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtFechaNacimiento"
													class="form-control-label label-title"
													>FECHA DE NACIMIENTO</label
												>
												<input
													type="date"
													id="txtFechaNacimiento"
													name="fechaNacimiento"
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.fecha_nacimiento"
													:disabled="true"
												/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label
													for="txtapellido_paterno"
													class="form-control-label label-title"
													>APELLIDO PATERNO</label
												>
												<textarea
													type="text"
													class="form-control"
													style="max-width: 300px"
													id="txtapellido_paterno"
													name="apellido_paterno"
													v-model="form_datos_usuario.apellido_paterno"
													:disabled="true"
												></textarea>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtapellido_materno"
													class="form-control-label label-title"
													>APELLIDO MATERNO</label
												>
												<textarea
													type="text"
													class="form-control"
													style="max-width: 300px"
													id="txtapellido_materno"
													name="apellido_materno"
													v-model="form_datos_usuario.apellido_materno"
													:disabled="true"
												></textarea>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtNombre"
													class="form-control-label label-title"
													>NOMBRES</label
												>
												<textarea
													type="text"
													class="form-control"
													style="max-width: 300px"
													id="txtNombre"
													name="nombres"
													v-model="form_datos_usuario.nombres"
													:disabled="true"
												></textarea>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label
													for="txtSexo"
													class="form-control-label label-title"
													>GÉNERO</label
												>
												<input
													type="text"
													id="txtSexo"
													name="sexo"
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.sexo"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-8">
												<label
													for="txtDireccion"
													class="form-control-label label-title"
													>DIRECCIÓN</label
												>
												<input
													type="text"
													id="txtDireccion"
													name="direccion"
													class="form-control"
													v-model="form_datos_usuario.direccion"
													:disabled="true"
												/>
											</div>
										</div>
									</div>

									<div
										class="tab-pane fade"
										id="datosCesado2"
										role="tabpanel"
										aria-labelledby="datosCesado2-tab"
									>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label
													for="txtDepartamento"
													class="form-control-label label-title"
													>DEPARTAMENTO</label
												>
												<input
													type="text"
													id="txtDepartamento"
													name="departamento"
													class="form-control center"
													v-model="form_datos_usuario.departamento"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtProvincia"
													class="form-control-label label-title"
													>PROVINCIA</label
												>
												<input
													type="text"
													id="txtProvincia"
													name="provincia"
													class="form-control center"
													v-model="form_datos_usuario.provincia"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtDistrito"
													class="form-control-label label-title"
													>DISTRITOS</label
												>
												<input
													type="text"
													id="txtDistrito"
													name="distrito"
													class="form-control center"
													v-model="form_datos_usuario.distrito"
													:disabled="true"
												/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label
													for="text-input"
													class="form-control-label label-title"
													>TELEFONO</label
												>
												<input
													type="number"
													class="form-control center"
													style="max-width: 200px"
													id="txtTelefono"
													name="telefono"
													v-model="form_datos_usuario.telefono"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-8">
												<label
													for="text-input"
													class="form-control-label label-title"
													>CORREO</label
												>
												<input
													type="text"
													id="txtCorreo_principal"
													name="correoPrincipal"
													class="form-control"
													v-model="form_datos_usuario.correo_corporativo"
													:disabled="true"
												/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label
													for="cmbAgenciasDatos"
													class="form-control-label label-title"
													>AGENCIA</label
												>
												<input
													type="text"
													id="txtAgencia"
													name="agencia"
													class="form-control center"
													v-model="form_datos_usuario.nombre_agencia"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtCargo"
													class="form-control-label label-title"
													>CARGO</label
												>
												<input
													type="text"
													id="txtCargo"
													name="cargo"
													class="form-control center"
													v-model="form_datos_usuario.cargo"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-4">
												<label
													for="txtHorario"
													class="form-control-label label-title"
													>HORARIO</label
												>
												<input
													type="text"
													id="txtHorario"
													name="horario"
													class="form-control center"
													v-model="form_datos_usuario.horario"
													:disabled="true"
												/>
											</div>
										</div>
									</div>

									<div
										class="tab-pane fade"
										id="datosCesado3"
										role="tabpanel"
										aria-labelledby="datosCesado3-tab"
									>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label class="label-title">HISTORIAL CESADO</label>
												<table
													class="table table-hover"
													id="t_datos_historial"
													width="100%"
												>
													<thead>
														<tr>
															<th>MOTIVO</th>
															<th>CESADOR</th>
															<th>FECHA</th>
														</tr>
													</thead>
													<tbody>
														<tr
															v-for="(item, index) in historial_cesado_filtrado"
															v-bind:key="index"
														>
															<td align="center">
																{{ item.motivo }}
															</td>
															<td align="center">
																{{ item.datos_creacion }}
															</td>
															<td align="center">
																{{ item.fecha_creacion }}
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<div class="text-right">
										<button
											class="btn btn-action btn-icon-split"
											id="btnHabilitarUsuario"
											@click="HabilitarUsuario"
										>
											<span class="icon text-white-50">
												<i class="fas fa-save" style="color: white"></i>
											</span>
											<span class="text">HABILITAR USUARIO</span>
										</button>
									</div>
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
		usuarios_cesados: Array,
		historial_cesados: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			title_modal: "USUARIO CESADO",

			form_datos_usuario: {
				dni_unlock: null,
				dni_lock: null,
				usuario: null,
				nombres: null,
				apellido_paterno: null,
				apellido_materno: null,
				sexo: null,
				direccion: null,
				distrito: null,
				provincia: null,
				departamento: null,
				fecha_nacimiento: null,
				telefono: null,
				correo_corporativo: null,
				cargo: null,
				nombre_agencia: null,
				horario: null,
			},
			historial_cesado_filtrado: [],
		};
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaCesados();
		this.TablaDatosHistorial();
	},
	watch: {
		historial_cesado_filtrado() {
			$("#t_datos_historial").DataTable().destroy();
			this.TablaDatosHistorial();
		},
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#t_usuarios_cesados").hasClass("table-responsive")) {
		//       document
		//         .getElementById("t_usuarios_cesados")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#t_usuarios_cesados").hasClass("table-responsive")) {
		//       document
		//         .getElementById("t_usuarios_cesados")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		TablaCesados() {
			this.$nextTick(() => {
				var table = $("#t_usuarios_cesados").DataTable({
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

				$("#cmbAgenciasUC").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar_uc").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		TablaDatosHistorial() {
			this.$nextTick(() => {
				$("#t_datos_historial").DataTable({
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
		VerInformacion(item) {
			this.form_datos_usuario.dni_unlock = item.dni;
			this.form_datos_usuario.dni_lock = item.dni;
			this.form_datos_usuario.usuario = item.usuario;
			this.form_datos_usuario.nombres = item.nombres;
			this.form_datos_usuario.apellido_paterno = item.apellido_paterno;
			this.form_datos_usuario.apellido_materno = item.apellido_materno;

			if (item.sexo == "M") {
				this.form_datos_usuario.sexo = "MASCULINO";
			} else {
				this.form_datos_usuario.sexo = "FEMENINO";
			}

			this.form_datos_usuario.direccion = item.direccion;
			this.form_datos_usuario.distrito = item.distrito;
			this.form_datos_usuario.provincia = item.provincia;
			this.form_datos_usuario.departamento = item.departamento;
			this.form_datos_usuario.fecha_nacimiento = item.fecha_nacimiento;
			this.form_datos_usuario.telefono = item.telefono;
			this.form_datos_usuario.correo_corporativo = item.correo_corporativo;
			this.form_datos_usuario.cargo = item.cargo;
			this.form_datos_usuario.nombre_agencia = item.nombre_agencia;
			this.form_datos_usuario.horario = item.horario;

			this.historial_cesado_filtrado = this.historial_cesados.filter(
				(item_1) => item_1.usuario_id == item.dni
			);

			$("#modalDatosCesado").css("display", "block");
			$("#datosCesado3-tab").tab("show");
			$("#btnCancelar").click(function () {
				$("#modalDatosCesado").css("display", "none");

				$("#datosCesado1-tab").tab("show");
			});
		},
		HabilitarUsuario() {
			self = this;
			Swal.fire({
				title: "HABILITAR USUARIO",
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
						.post(route("gth.usu.usuarios_cesados.habilitar"), {
							dni: self.form_datos_usuario.dni_unlock,
						})
						.then(function (response) {
							let resultado = response.data;
							if (resultado == "EXITO") {
								Swal.fire({
									icon: "success",
									title: "¡EXITO!",
									text: "Usuario habilitado",
									allowOutsideClick: false,
									preConfirm: (result) => {
										$("#modalDatosCesado").css("display", "none");

										self.$inertia.get(route("gth.usu.usuarios_cesados"));
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
		Cerrar() {
			$("#modalDatosCesado").css("display", "none");
			$("#datosCesado1-tab").tab("show");
		},
	},
};
</script>


<style>
.slot-cesados {
	width: 50% !important;
	margin-left: 25% !important;
}

.modalDatosCesado {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-cesados {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.modalDatosCesado {
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

