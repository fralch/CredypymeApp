<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body slot-permisosCargosEditar">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'EDITAR PERMISOS DE LOS CARGOS'"></headerClose>
					<div class="card-title">USUARIO</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-xs-4">
								<label for="lblCargo" class="form-control-label label-title"
									>CARGO</label
								>
								<input
									type="text"
									id="inpCargo"
									name="cargos"
									class="form-control center"
									style="width: 250px"
									v-for="cargo in cargos"
									:key="cargo.id"
									:value="cargo.cargo"
									disabled
								/>
							</div>
						</div>
					</div>

					<div class="card-title">PERMISOS DISPONIBLES</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-ms-6">
								<label for="text-input" class="form-control-label label-title"
									>Por área</label
								>
								<select
									class="form-control center"
									style="width: 250px"
									id="slcAreadisponibles"
									@change="FiltrarAreasDisponibles"
								>
									<option value="0" selected>TODAS</option>
									<option
										v-for="(areaDispo, index) in areasDispos"
										:key="index"
										:value="areaDispo.area"
									>
										{{ areaDispo.area }}
									</option>
								</select>
							</div>
						</div>
						<div class="input-group row col-md-10 col-9" style="float: left">
							<div class="input-group-prepend">
								<span class="input-group-text"
									><i class="fas fa-search"></i
								></span>
							</div>
							<input
								class="form-control mayus"
								type="text"
								id="inpBuscar_ud"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>
						<div id="tabla_permisos_disponibles">
							<table class="table table-hover" id="tblAñadirPermiso">
								<thead>
									<tr>
										<th style="width: 75px !important">
											TODO
											<div class="align-middle">
												<div class="checkbox">
													<label
														style="
															font-size: 1.5em;
															margin-bottom: 0 !important;
															height: 5px !important;
														"
													>
														<input
															type="checkbox"
															name="chbCheck"
															@change="selectAll"
															v-model="frmAsignarPermisos.allSelected"
														/>
														<span class="cr"
															><i
																class="cr-icon fa fa-check"
																style="color: white"
																important
															></i
														></span>
													</label>
												</div>
											</div>
										</th>
										<th>ÁREA</th>
										<th>MÓDULO</th>
										<th v-for="agencia in agencias" :key="agencia.id_agencia">
											AG_{{ agencia.nombre }}
										</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(
											permisoDisponible, index
										) in permisos_disp_filtrados"
										v-bind:key="index"
									>
										<td class="table-bordered" align="center">
											<div class="align-middle">
												<div class="checkbox">
													<label
														style="
															font-size: 2em;
															margin-bottom: 0 !important;
															height: 28.6px !important;
														"
														><input
															type="checkbox"
															class="form-control"
															name="chbDisponible"
															:value="permisoDisponible.id"
															:id="'chb' + permisoDisponible.id"
															v-model="frmAsignarPermisos.permisoSeleccionados"
															@change="
																eliminarPermisosAgencias(permisoDisponible.id)
															" />
														<span class="cr"
															><i class="cr-icon fa fa-check"></i></span
													></label>
												</div>
											</div>
										</td>
										<td class="table-bordered" align="center">
											{{ permisoDisponible.area }}
										</td>
										<td class="table-bordered" align="center">
											{{ permisoDisponible.modulo }}
										</td>
										<td
											v-for="agencia in agencias"
											:key="agencia.id_agencia"
											class="table-bordered"
											align="center"
										>
											<div class="align-middle">
												<div class="checkbox">
													<label
														style="
															font-size: 2em;
															margin-bottom: 0 !important;
															height: 28.6px !important;
														"
														><input
															:disabled="
																!frmAsignarPermisos.permisoSeleccionados.includes(
																	permisoDisponible.id
																)
															"
															type="checkbox"
															class="form-control"
															v-model="frmAsignarPermisos.permisosAgencia"
															:value="{
																permiso_id: permisoDisponible.id,
																agencia_id: agencia.id_agencia,
															}" />
														<span class="cr"
															><i class="cr-icon fa fa-check"></i></span
													></label>
												</div>
											</div>
										</td>
									</tr>
								</tbody>

								<div
									v-if="
										submited &&
										frmAsignarPermisos.permisoSeleccionados.length == 0
									"
									style="color: red; font-size: 12px"
								>
									*Seleccionar un permiso
								</div>
							</table>

							<div class="text-center">
								<button
									class="btn btn-action btn-icon-split mb-1"
									@click="AgregarPermisos()"
								>
									<span class="icon text-white-50">
										<i class="fas fa-plus" style="color: white"></i>
									</span>
									<span class="text font-size-layout">Agregar Permisos</span>
								</button>
							</div>
						</div>
					</div>

					<div class="card-title">PERMISOS ACTUALES</div>
					<div class="card-body card-block">
						<br />
						<div class="form-row">
							<div class="form-group col-ms-6">
								<label
									for="slcAreaActual"
									class="form-control-label label-title"
									>Por área</label
								>
								<select
									type="text"
									class="form-control center"
									style="width: 250px"
									id="slcAreaActual"
									name="areas"
									data-index="1"
									@change="FiltrarAreasActuales"
								>
									<option value="0">TODAS</option>
									<option
										v-for="areaActual in areasActuales"
										:key="areaActual.id_cargo"
									>
										{{ areaActual.area }}
									</option>
								</select>
							</div>
						</div>
						<div class="input-group row col-md-10 col-9" style="float: left">
							<div class="input-group-prepend">
								<span class="input-group-text"
									><i class="fas fa-search"></i
								></span>
							</div>
							<input
								class="form-control mayus"
								type="text"
								id="inpBuscar_ua"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>
						<div id="tabla_permisos_actuales">
							<table class="table table-hover" id="tblQuitarPermiso">
								<thead>
									<tr>
										<th style="width: 75px !important">ACCIONES</th>
										<th>ÁREA</th>
										<th>MÓDULO</th>
										<th v-for="agencia in agencias" :key="agencia.id_agencia">
											AG_{{ agencia.nombre }}
										</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(
											permisoActual, index
										) in permisos_actuales_filtrados"
										v-bind:key="index"
									>
										<td class="table-bordered" align="center">
											<button
												class="btn btn-danger btn-icon-split"
												@click="QuitarPermisos(permisoActual.id)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-times" style="color: white"></i>
												</span>
											</button>
											<button
												class="btn btn-cancel btn-icon-split"
												v-show="
													!permisosEditar.includes(permisoActual.permiso_id)
												"
												@click="permisosEditar.push(permisoActual.permiso_id)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-edit" style="color: white"></i>
												</span>
											</button>
											<button
												v-show="
													permisosEditar.includes(permisoActual.permiso_id)
												"
												class="btn btn-action btn-icon-split"
												@click="
													EditarPermisos(
														permisoActual.permiso_id,
														permisoActual.id
													)
												"
											>
												<span class="icon text-white-50">
													<i class="fas fa-save" style="color: white"></i>
												</span>
											</button>
										</td>
										<td class="table-bordered" align="center">
											{{ permisoActual.area }}
										</td>
										<td class="table-bordered" align="center">
											{{ permisoActual.modulo }}
										</td>
										<td
											v-for="agencia in agencias"
											:key="agencia.id_agencia"
											class="table-bordered"
											align="center"
										>
											<div class="align-middle">
												<div class="checkbox">
													<label
														style="
															font-size: 2em;
															margin-bottom: 0 !important;
															height: 28.6px !important;
														"
														><input
															:disabled="
																!permisosEditar.includes(
																	permisoActual.permiso_id
																)
															"
															type="checkbox"
															class="form-control"
															:value="{
																permiso_id: permisoActual.permiso_id,
																agencia_id: agencia.id_agencia,
															}"
															v-model="acceso_agencias_editar" />
														<span class="cr"
															><i class="cr-icon fa fa-check"></i></span
													></label>
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							<div id="tabla"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";
const diferentThanZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		cargos: Array,
		permisosActuales: Array,
		permisosDisponibles: Array,
		areasDispos: Array,
		areasActuales: Array,
		agencias: Array,
		acceso_agencias: Array,
	},
	data() {
		return {
			submitedC: false,
			submited: false,
			permisos_disp_filtrados: this.permisosDisponibles,
			permisos_actuales_filtrados: this.permisosActuales,
			frmAsignarPermisos: {
				id_cargo: this.cargos[0].id,
				permisoSeleccionados: [],
				permisosAgencia: [],
				allSelected: false,
			},
			permisosEditar: [],
			acceso_agencias_editar: this.acceso_agencias,
		};
	},
	validations: {
		// usuarioSeleccionado: { noZero: diferentThanZero },
	},
	mounted() {
		self = this;
		// this.agencias.forEach(
		//   function(agencia){
		//     if (agencia.nombre == 'EL TAMBO'){
		//       agencia.nombre = agencia.nombre.substr(3);
		//     }
		//   }
		// );
		this.TablaAñadirPermisos();
		if (screen.width < 1000) {
			document
				.getElementById("tblAñadirPermiso")
				.classList.add("table-responsive");
		}

		this.TablaQuitarPermiso();
		if (screen.width < 1000) {
			document
				.getElementById("tblQuitarPermiso")
				.classList.add("table-responsive");
		}
	},
	watch: {
		permisos_disp_filtrados() {
			$("#tblAñadirPermiso").DataTable().destroy();
			this.TablaAñadirPermisos();
		},
		permisos_actuales_filtrados() {
			$("#tblQuitarPermiso").DataTable().destroy();
			this.TablaQuitarPermiso();
		},
	},
	methods: {
		EditarPermisos(permiso_id, id) {
			this.submited = true;
			self = this;
			let lista_agencias_actual = [];
			let lista_agencias_editar = [];
			let index = self.permisosEditar.indexOf(permiso_id);
			self.permisosEditar.splice(index, 1);
			self.acceso_agencias.forEach(function (element) {
				if (element["permiso_id"] == permiso_id) {
					lista_agencias_actual.push(element["agencia_id"]);
				}
			});

			self.acceso_agencias_editar.forEach(function (element) {
				if (element["permiso_id"] == permiso_id) {
					lista_agencias_editar.push(element["agencia_id"]);
				}
			});

			if (!_.isEqual(lista_agencias_editar, lista_agencias_actual)) {
				Swal.fire({
					title: "EDITAR PERMISO",
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
							route("gen.per.permisos_cargos_editar.editar_agencia"),
							{
								lista_agencias_editar: lista_agencias_editar,
								id: id,
							},
							{
								preserveScroll: true,
								onStart: (visit) => {
									let timerInterval;
									Swal.fire({
										title: "EN PROGRESO",
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
											self.submited = false;
										},
									});
								},
							}
						);
					},
				});
			}
		},
		eliminarPermisosAgencias(permiso_id) {
			_.remove(this.frmAsignarPermisos.permisosAgencia, function (permiso) {
				return permiso["permiso_id"] == permiso_id;
			});
		},

		selectAll() {
			self = this;

			if (this.frmAsignarPermisos.allSelected == false) {
				self.frmAsignarPermisos.permisoSeleccionados = [];
			} else if (this.frmAsignarPermisos.allSelected == true) {
				self.frmAsignarPermisos.permisoSeleccionados = [];
				this.permisos_disp_filtrados.forEach(function callback(
					currentValue //se utiliza como un iterador = item -> item in items
				) {
					self.frmAsignarPermisos.permisoSeleccionados.push(currentValue.id);
				});
			}
		},
		FiltrarAreasDisponibles() {
			let slcAreas_value = $("#slcAreadisponibles").val();

			if (slcAreas_value == 0) {
				this.permisos_disp_filtrados = this.permisosDisponibles;
			} else {
				this.permisos_disp_filtrados = this.permisosDisponibles.filter(
					(item) => item.area == slcAreas_value
				);
			}
			// this.TablaAñadirPermisos();
		},
		FiltrarAreasActuales() {
			this.permisos_actuales_filtrados = this.permisosActuales;
		},
		FiltrarAccesoAgencias() {
			this.acceso_agencias_editar = this.acceso_agencias;
		},
		TablaAñadirPermisos() {
			this.$nextTick(() => {
				var table = $("#tblAñadirPermiso").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[1, "asc"]],
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
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
						{
							extend: "pdfHtml5",
							text: '<i class="fas fa-file-pdf"></i> ',
							titleAttr: "Exportar a PDF",
							className: "btn btn-cancel",
						},
						{
							extend: "print",
							text: '<i class="fa fa-print"></i> ',
							titleAttr: "Imprimir",
							className: "btn btn-action",
						},
					],
				});
				$("#slcAreadisponibles").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar_ud").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		TablaQuitarPermiso() {
			this.$nextTick(() => {
				var table = $("#tblQuitarPermiso").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[1, "asc"]],
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
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
						{
							extend: "pdfHtml5",
							text: '<i class="fas fa-file-pdf"></i> ',
							titleAttr: "Exportar a PDF",
							className: "btn btn-cancel",
						},
						{
							extend: "print",
							text: '<i class="fa fa-print"></i> ',
							titleAttr: "Imprimir",
							className: "btn btn-action",
						},
					],
				});
				$("#slcAreaActual").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});
				$("#inpBuscar_ua").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		AgregarPermisos() {
			this.submited = true;
			self = this;
			if (self.frmAsignarPermisos.permisoSeleccionados.length == 0) {
				Swal.fire({
					icon: "error",
					title: "Olvidaste elegir los permisos!",
					text: "selecciona algun permiso",
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
						self.$inertia.post(
							route("gen.per.permisos_cargos_editar.asignar"),
							self.frmAsignarPermisos,
							{
								preserveScroll: true,
								onStart: (visit) => {
									let timerInterval;
									Swal.fire({
										title: "EN PROGRESO",
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
											self.submited = false;
											self.frmAsignarPermisos.permisoSeleccionados = [];

											this.FiltrarAreasDisponibles();
											this.FiltrarAreasActuales();
											this.FiltrarAccesoAgencias();
										},
									});
								},
							}
						);
					},
				});
			}
		},
		QuitarPermisos(id) {
			self = this;
			Swal.fire({
				title: "ELIMINAR PERMISO",
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
						route("gen.per.permisos_cargos_editar.eliminar"),
						{ id: id },
						{
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "EN PROGRESO",
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
										self.submited = false;
										self.FiltrarAreasDisponibles();
										self.FiltrarAreasActuales();
									},
								});
							},
						}
					);
				},
			});
		},
	},
};
</script>

<style>
</style>
