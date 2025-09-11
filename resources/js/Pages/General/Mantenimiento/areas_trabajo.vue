<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ÁREAS DE TRABAJO'"></headerClose>
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
								id="inpBuscar"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>
						<div class="row col-md-1 col-2 ml-1" style="float: left">
							<button
								class="btn btn-action btn-icon-split mb-1"
								@click="abrirMdlNuevaArea()"
							>
								<span class="icon text-white-50">
									<i class="fas fa-plus" style="color: white"></i>
								</span>
							</button>
						</div>
						<div id="tabla_feriados">
							<table class="table table-hover" id="tblAreas">
								<thead>
									<tr>
										<th style="width: 75px !important">EDITAR</th>
										<th>ÁREA</th>
										<th>DESCRIPCION</th>
										<th>HABILITADO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="area in areas_trabajo_filtrados" :key="area.id">
										<td
											class="table-bordered"
											align="center"
											style="width: 10%"
										>
											<button
												class="btn btn-action btn-icon-split"
												@click="editarAreas(area)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-edit" style="color: white"></i>
												</span>
											</button>
										</td>
										<td
											class="table-bordered"
											style="width: 30%"
											align="center"
										>
											{{ area.area }}
										</td>
										<td
											class="table-bordered"
											style="width: 50%"
											align="center"
										>
											{{ area.descripcion }}
										</td>
										<td class="table-bordered center" style="width: 10%">
											{{ area.habilitado == 1 ? "Si" : "No" }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- ---- Modal modo  -->
				<div class="modal" id="mdlNuevaArea">
					<div class="modal-content w-40">
						<div class="content" style="display: block">
							<div class="card">
								<headerCloseModal
									:titulo_modal="this.tituloMdl + ' ÁREA DE TRABAJO'"
									:nombre_modal="'mdlNuevaArea'"
								>
								</headerCloseModal>
								<div class="card-title">DATOS DE ÁREA DE TRABAJO</div>
								<div class="card-body card-block">
									<div class="form-row">
										<div class="form-group col-md-6">
											<label class="label-title"> ÁREA</label>
											<span
												v-if="submited && !$v.frmRegistrarAreas.area.required"
												class="span-error-message"
												>*</span
											>
											<input
												class="form-control mayus"
												type="text"
												v-model="frmRegistrarAreas.area"
											/>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												type="text"
												rows="2"
												class="form-control mayus"
												v-model="frmRegistrarAreas.descripcion"
											></textarea>
										</div>
									</div>
									<!-- ---------------------- -->
									<div
										class="form-row ml-1 mt-1"
										v-if="frmRegistrarAreas.modo == 0"
									>
										<label
											for="chbHabilitado"
											class="form-control-label label-title"
											>HABILITADO</label
										>
										<div class="checkbox">
											<label
												class="align-middle"
												style="
													font-size: 1em;
													margin-bottom: 0 !important;
													height: 1em !important;
												"
												for="chbHabilitado"
												><input
													type="checkbox"
													id="chbHabilitado"
													v-model="frmRegistrarAreas.habilitado" /><span
													class="cr"
													style="margin-right: 0 !important"
													><i class="cr-icon fa fa-check"></i></span
											></label>
										</div>
									</div>
									<!-- --------------------------- -->
									<hr />
									<div class="text-right">
										<button
											class="btn btn-action btn-icon-split"
											@click="guardarNuevaArea()"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text font-size-layout">GUARDAR</span>
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
export default {
	components: { layout, headerClose, headerCloseModal },
	props: {
		areas_trabajo: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: "NUEVA ÁREA",
			areas_trabajo_filtrados: this.areas_trabajo,
			frmRegistrarAreas: {
				id: "",
				area: "",
				modo: 0,
				descripcion: "",
				habilitado: 1,
			},
			guardarMdl: "AGREGANDO ÁREA",
			tituloMdl: "NUEVA",
		};
	},
	validations: {
		frmRegistrarAreas: {
			area: { required },
		},
	},
	mounted() {
		self = this;
		this.TablaListarAreas();
		if (screen.width < 1000) {
			document.getElementById("tblAreas").classList.add("table-responsive");
		}
	},

	watch: {
		areas_trabajo_filtrados() {
			$("#tblAreas").DataTable().destroy();
			this.TablaListarAreas();
		},
	},

	methods: {
		FiltrarAreasTrabajo() {
			this.areas_trabajo_filtrados = this.areas_trabajo;
		},
		TablaListarAreas() {
			this.$nextTick(() => {
				var table = $("#tblAreas").DataTable({
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
				$("#inpBuscar").keyup(function () {
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

		abrirMdlNuevaArea() {
			let self = this;
			this.submited = false;
			self.guardarMdl = "Guardar Categoria";
			self.tituloMdl = "NUEVA";

			self.frmRegistrarAreas.modo = 1;
			self.frmRegistrarAreas.id = "";
			self.frmRegistrarAreas.area = "";
			self.frmRegistrarAreas.descripcion = "";
			self.frmRegistrarAreas.habilitado = 1;
			$("#mdlNuevaArea").css("display", "block");
		},
		cerrarMdlNuevaArea() {
			$("#mdlNuevaArea").css("display", "none");
		},

		// ----------guardar--
		guardarNuevaArea() {
			let self = this;
			this.submited = true;

			if (self.$v.frmRegistrarAreas.$invalid) {
				return false;
			}

			axios
				.post(route("gen.man.verificar_area"), self.frmRegistrarAreas)
				.then(function (response) {
					if (response.data == "EXISTE" && self.tituloMdl == "NUEVA") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "No se pueden registrar duplicados.",
						});
						return false;
					} else if (
						response.data == "NO_EXISTE" ||
						self.tituloMdl == "EDITAR"
					) {
						// ----
						Swal.fire({
							title: self.guardarMdl,
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
									route("gen.man.guardar_area"),
									self.frmRegistrarAreas,
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
													self.submited = false;
													self.FiltrarAreasTrabajo();
													$("#mdlNuevaArea").css("display", "none");
												},
											});
										},
									}
								);
							},
						});
						// ----
					}
				});
		},
		editarAreas(area) {
			$("#mdlNuevaArea").css("display", "block");
			let self = this;
			self.guardarMdl = "Editar";
			self.tituloMdl = "EDITAR";

			self.frmRegistrarAreas.id = area.id;
			self.frmRegistrarAreas.modo = 0;
			self.frmRegistrarAreas.area = area.area;
			self.frmRegistrarAreas.descripcion = area.descripcion;
			self.frmRegistrarAreas.habilitado = area.habilitado;
		},
	},
};
</script>

<style>
#mdlNuevaArea .modal-content {
	width: 30% !important;
	margin-left: 35% !important;
}
</style>
