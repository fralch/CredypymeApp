<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body slot-permisoslistar">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LISTAR PERMISOS'"></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="col form-group">
							<label for="text-input" class="form-control-label label-title"
								>Por área</label
							>
							<select
								class="form-control center"
								style="width: 250px"
								id="cmbAreas"
								@change="FiltrarPermisos"
							>
								<option value="0" selected>TODOS</option>
								<option
									v-for="(modulo, index) in modulos"
									:key="index"
									:value="modulo.area"
								>
									{{ modulo.area }}
								</option>
							</select>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div class="input-group row col-md-9 col-7" style="float: left">
							<div class="input-group-prepend">
								<span class="input-group-text"
									><i class="fas fa-search"></i
								></span>
							</div>
							<input
								class="form-control mayus"
								type="text"
								id="inpBuscar_l"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>
						<div class="row col-md-1 col-2 ml-1" style="float: left">
							<button
								class="btn btn-action btn-icon-split mb-1"
								@click="NuevoPermiso()"
							>
								<span class="icon text-white-50">
									<i class="fas fa-plus" style="color: white"></i>
								</span>
							</button>
						</div>
						<div id="tabla_permisos">
							<table class="table table-hover" id="t_permisos">
								<thead>
									<tr>
										<th>EDITAR</th>
										<th>ÁREA</th>
										<th>MÓDULO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="permiso in permisos_filtrados" :key="permiso.id">
										<td
											class="table-bordered"
											align="center"
											style="width: 10%"
										>
											<button
												class="btn btn-action btn-icon-split"
												@click="EditarPermiso(permiso)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-edit" style="color: white"></i>
												</span>
											</button>
										</td>
										<td
											class="table-bordered"
											align="center"
											style="width: 40%"
										>
											{{ permiso.area }}
										</td>
										<td
											class="table-bordered"
											align="center"
											style="width: 50%"
										>
											{{ permiso.modulo }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- The Modal -->
				<div id="modalRegistrarPermiso" class="modal">
					<!-- Modal content -->
					<div class="modal-content modalPermisoListar w-40">
						<div class="content" style="display: block">
							<div class="card">
								<headerCloseModal
									:titulo_modal="this.title_modal"
									:nombre_modal="'modalRegistrarPermiso'"
								>
								</headerCloseModal>
								<div class="card-title">DATOS DEL PERMISO</div>
								<div class="card-body card-block">
									<form @submit.prevent="GuardarPermiso">
										<input
											type="text"
											id="txtModal"
											v-model="frmRegistrarPermiso.modal"
											hidden
										/>
										<input
											type="text"
											id="txtModo"
											v-model="frmRegistrarPermiso.modo"
											hidden
										/>
										<input
											type="text"
											id="txtIdPermiso"
											hidden
											v-model="frmRegistrarPermiso.permiso_id"
										/>
										<div class="col form-group">
											<label class="form-control-label label-title"
												>NOMBRE DEL PERMISO</label
											>
											<textarea
												class="form-control"
												type="text"
												name="permiso"
												style="max-width: 400px"
												maxlength="150"
												id="txtNombrePermiso"
												v-model="frmRegistrarPermiso.modulo"
												placeholder="Ingrese el nombre del permiso"
											></textarea>
											<div
												v-if="
													submited && !$v.frmRegistrarPermiso.modulo.required
												"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<div class="col form-group">
											<div class="custom-control custom-radio">
												<input
													type="radio"
													class="custom-control-input"
													id="rdbAreaExistente"
													name="radiobutton"
													@change="MostrarAreaExistente"
													checked
												/>
												<label
													class="custom-control-label label-title"
													for="rdbAreaExistente"
												>
													ÁREA EXISTENTE</label
												>
											</div>
											<select
												class="form-control form-xs-size center"
												id="cmbAreasmodal"
												name="areaExistente"
												v-model="frmRegistrarPermiso.areaExistente"
												style="max-width: 400px"
											>
												<option value="0" selected>
													Seleccione un área existente...
												</option>
												<option v-for="modulo in modulos" :key="modulo.id">
													{{ modulo.area }}
												</option>
											</select>
											<div
												v-if="
													submited &&
													frmRegistrarPermiso.modo == 'EXISTENTE' &&
													!$v.frmRegistrarPermiso.areaExistente.noZero
												"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<div
											class="col form-group form-check-inline"
											id="c_checkbox"
											v-if="frmRegistrarPermiso.modal == 'AGREGAR'"
										>
											<div class="col form-group">
												<div class="custom-control custom-radio">
													<input
														type="radio"
														class="custom-control-input"
														@change="MostrarNuevaArea"
														id="rdbNuevaArea"
														name="radiobutton"
													/>
													<label
														class="custom-control-label label-title"
														for="rdbNuevaArea"
														>ÁREA NUEVA
													</label>
												</div>
												<textarea
													class="form-control form-sm-size"
													type="text"
													id="txtAreaNueva"
													name="areaNueva"
													v-model="frmRegistrarPermiso.areaNueva"
													placeholder="Ingrese el nombre del area"
													style="display: none; max-width: 400px"
												>
												</textarea>
												<div
													v-if="
														submited &&
														frmRegistrarPermiso.modo == 'NUEVO' &&
														!$v.frmRegistrarPermiso.areaNueva.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>
									</form>
									<hr />
									<div class="text-right">
										<button
											class="btn btn-action btn-icon-split mb-1"
											id="btnGuardarCambios"
											@click="GuardarPermiso()"
										>
											<span class="icon text-white-50">
												<i class="fas fa-save" style="color: white"></i>
											</span>
											<span class="text">Guardar</span>
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
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";
import headerCloseModal from "@/Pages/General/Components/header_close_modal.vue";
const diferentThanZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		modulos: Array,
		listar: Array,
		permisos: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: "NUEVO PERMISO",
			permisos_filtrados: this.permisos,
			frmRegistrarPermiso: {
				permiso_id: "",
				modulo: "",
				areaExistente: "",
				areaNueva: "",
				modo: "",
				modal: "",
			},
		};
	},
	validations() {
		if (this.frmRegistrarPermiso.modal == "AGREGAR") {
			if (this.frmRegistrarPermiso.modo == "EXISTENTE") {
				return {
					frmRegistrarPermiso: {
						modulo: { required },
						areaExistente: { noZero: diferentThanZero },
					},
				};
			} else if (this.frmRegistrarPermiso.modo == "NUEVO") {
				return {
					frmRegistrarPermiso: {
						modulo: { required },
						areaNueva: { required },
					},
				};
			}
		} else if (this.frmRegistrarPermiso.modal == "EDITAR") {
			if (this.frmRegistrarPermiso.modo == "EXISTENTE") {
				return {
					frmRegistrarPermiso: {
						modulo: { required },
						areaExistente: { noZero: diferentThanZero },
					},
				};
			}
		}
	},

	mounted() {
		this.TablaListarPermisos();
	},
	watch: {
		permisos_filtrados() {
			$("#t_permisos").DataTable().destroy();
			this.TablaListarPermisos();
		},
	},

	methods: {
		TablaListarPermisos() {
			this.$nextTick(() => {
				var table = $("#t_permisos").DataTable({
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
					],
				});

				$("#cmbAreas").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar_l").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		FiltrarPermisos() {
			let slcAreas_value = $("#cmbAreas").val();

			if (slcAreas_value == 0) {
				this.permisos_filtrados = this.permisos;
			} else {
				this.permisos_filtrados = this.permisos.filter(
					(item) => item.area == slcAreas_value
				);
			}
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		MostrarNuevaArea() {
			document.getElementById("cmbAreasmodal").style.display = "none";
			document.getElementById("txtAreaNueva").style.display = "block";
			this.frmRegistrarPermiso.modo = "NUEVO";
		},
		MostrarAreaExistente() {
			document.getElementById("cmbAreasmodal").style.display = "block";
			document.getElementById("txtAreaNueva").style.display = "none";
			this.frmRegistrarPermiso.modo = "EXISTENTE";
		},
		NuevoPermiso() {
			this.submited = false;
			this.title_modal = "NUEVO PERMISO";
			this.frmRegistrarPermiso.permiso_id = 0;
			this.frmRegistrarPermiso.modulo = "";
			this.frmRegistrarPermiso.areaExistente = 0;
			this.frmRegistrarPermiso.areaNueva = "";
			this.frmRegistrarPermiso.modo = "EXISTENTE";
			this.frmRegistrarPermiso.modal = "AGREGAR";

			document.getElementById("modalRegistrarPermiso").style.display = "block";
			parent.document.getElementById("footer-navigator").style.display = "none";
		},
		EditarPermiso(permiso) {
			this.submited = false;
			this.title_modal = "EDITAR PERMISO";
			this.frmRegistrarPermiso.permiso_id = permiso.id;
			this.frmRegistrarPermiso.modulo = permiso.modulo;
			this.frmRegistrarPermiso.areaExistente = permiso.area;
			// this.frmRegistrarPermiso.areaExistente = permiso.area
			this.frmRegistrarPermiso.modo = "EXISTENTE";
			this.frmRegistrarPermiso.modal = "EDITAR";

			document.getElementById("modalRegistrarPermiso").style.display = "block";
			parent.document.getElementById("footer-navigator").style.display = "none";
		},

		GuardarPermiso() {
			this.submited = true;
			var self = this;
			if (this.$v.frmRegistrarPermiso.$invalid) {
				return false;
			} else {
				axios
					.post(
						route("gen.per.listar_permisos.verificar"),
						self.frmRegistrarPermiso
					)
					.then(function (response) {
						if (response.data == "EXISTE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Este PERMISO ya EXISTE, intente con otro.",
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
										route("gen.per.listar_permisos.guardar"),
										self.frmRegistrarPermiso,
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
														self.FiltrarPermisos();
														$("#modalRegistrarPermiso").css("display", "none");
													},
												});
											},
										}
									);
								},
							});
						}
					});
			}
		},
	},
};
</script>

<style>
.slot-permisoslistar {
	width: 60% !important;
	margin-left: 20% !important;
}

.modalPermisoListar {
	margin-top: 2%;
}
@media (max-width: 900px) {
	.slot-permisoslistar {
		width: 98% !important;
		margin-left: 1% !important;
	}

	.modalPermisoListar {
		margin-top: 20%;
	}
}
</style>
