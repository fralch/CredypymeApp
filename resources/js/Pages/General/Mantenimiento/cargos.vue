<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LISTA DE CARGOS'"></headerClose>
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
								@click="NuevoCargo()"
							>
								<span class="icon text-white-50">
									<i class="fas fa-plus" style="color: white"></i>
								</span>
							</button>
						</div>
						<div id="tabla_listarCargos">
							<table class="table table-hover" id="tblListarCargos">
								<thead>
									<tr>
										<th style="width: 75px !important">EDITAR</th>
										<th>CARGO</th>
										<th>DESCRIPCIÓN</th>
										<th>HABILITADO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="cargo in cargos" :key="cargo.id">
										<td class="table-bordered" align="center">
											<button
												class="btn btn-action btn-icon-split"
												@click="EditarCargo(cargo)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-edit" style="color: white"></i>
												</span>
											</button>
										</td>
										<td class="table-bordered" align="center">
											{{ cargo.cargo }}
										</td>
										<td class="table-bordered" align="center">
											{{
												cargo.descripcion == null
													? "-"
													: cargo.descripcion == ""
													? "-"
													: cargo.descripcion
											}}
										</td>
										<td class="table-bordered" align="center">
											{{ cargo.habilitado == 1 ? "Si" : "No" }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- The Modal -->
				<div id="modalRegistrarCargo" class="modal">
					<div class="modal-content modalCargoListar w-40">
						<div class="content" style="display: block">
							<div class="card">
								<headerCloseModal
									:titulo_modal="this.title_modal"
									:nombre_modal="'modalRegistrarCargo'"
								>
								</headerCloseModal>
								<div class="card-title">DATOS DEL CARGO</div>
								<div class="card-body card-block">
									<form @submit.prevent="GuardarCargo">
										<input
											type="text"
											id="txtModo"
											v-model="frmRegistrarCargo.modo"
											hidden
										/>
										<input
											type="text"
											id="txtIdCargo"
											hidden
											v-model="frmRegistrarCargo.id"
										/>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label class="form-control-label label-title"
													>NOMBRE DEL CARGO</label
												>
												<textarea
													class="form-control"
													type="text"
													style="max-width: 400px"
													maxlength="150"
													id="txtNombreCargo"
													name="nombre_cargo"
													v-model="frmRegistrarCargo.cargo"
													placeholder="Ingrese el nombre del cargo"
												></textarea>
												<div
													v-if="
														submited &&
														!$v.frmRegistrarCargo.nombre_cargo.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label class="form-control-label label-title"
													>DESCRIPCIÓN</label
												>
												<textarea
													class="form-control"
													type="text"
													name="descripcion_cargo"
													style="max-width: 400px"
													maxlength="150"
													id="txtDescripcionCargo"
													v-model="frmRegistrarCargo.descripcion"
													placeholder="Ingrese una descripcióm"
												></textarea>
											</div>
										</div>
										<div class="input-group">
											<div
												class="col form-group form-check-inline"
												id="c_checkbox"
												v-if="frmRegistrarCargo.modo == 'EDITAR'"
											>
												<label
													class="form-control-label label-title"
													for="chbHabilitado"
													>HABILITADO</label
												>
												<div class="input-group-prepend">
													<div
														class="input-group-text"
														style="
															padding: 0 !important;
															border: 0px;
															background-color: transparent;
														"
													>
														<div class="checkbox">
															<label
																class="align-middle"
																style="
																	font-size: 1em;
																	margin-bottom: 0 !important;
																	height: 25px !important;
																"
																><input
																	type="checkbox"
																	name="chbCopiar"
																	v-model="frmRegistrarCargo.habilitado" /><span
																	class="cr"
																	style="margin-right: 0 !important"
																	><i class="cr-icon fa fa-check"></i></span
															></label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
									<hr />
									<div class="text-right">
										<button
											class="btn btn-action btn-icon-split mb-1"
											id="btnGuardarCambios"
											@click="GuardarCargo()"
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
						<!-- </div> -->
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
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		cargos: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: "NUEVO CARGO",
			frmRegistrarCargo: {
				modo: "",
				id: "",
				cargo: "",
				descripcion: "",
				habilitado: "",
			},
		};
	},
	validations: {
		frmRegistrarCargo: {
			cargo: {
				required,
			},
		},
	},
	mounted() {
		self = this;
		this.TablaListarCargos();
		if (screen.width < 1000) {
			document
				.getElementById("tblListarCargos")
				.classList.add("table-responsive");
		}
	},
	methods: {
		TablaListarCargos() {
			this.$nextTick(() => {
				var table = $("#tblListarCargos").DataTable({
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
							// title: 'Lista de Cargos',
							// customize: function(doc) {
							//   doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
							//   doc.styles.tableBodyEven.alignment = 'center';
							//   doc.styles.tableBodyOdd.alignment = 'center';
							// },
							// exportOptions:{
							//   columns:[1,2,3]
							// }
						},
						{
							extend: "print",
							text: '<i class="fa fa-print"></i> ',
							titleAttr: "Imprimir",
							className: "btn btn-action",
							// title: 'Lista de Cargos',
							// exportOptions:{
							//   columns:[1,2,3]
							// },
							// customize: function ( win ) {
							//       $(win.document.body)
							//           .css( 'font-size', '10pt' );

							//       $(win.document.body).find( 'table' )
							//           .addClass( 'compact','mx-auto','text-center')
							//           .css( {
							//              color: '#FF0000',
							//           } );
							//   }
						},
					],
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		ActualizarTabla() {
			$("#tblListarCargos").DataTable().destroy();
			this.TablaListarCargos();
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		NuevoCargo() {
			this.submited = false;
			this.title_modal = "NUEVO CARGO";
			this.frmRegistrarCargo.id = 0;
			this.frmRegistrarCargo.cargo = "";
			this.frmRegistrarCargo.descripcion = "";
			this.frmRegistrarCargo.habilitado = 1;
			this.frmRegistrarCargo.modo = "NUEVO";

			document.getElementById("modalRegistrarCargo").style.display = "block";
			parent.document.getElementById("footer-navigator").style.display = "none";
		},

		EditarCargo(cargo) {
			this.submited = false;
			this.title_modal = "EDITAR CARGO";
			this.frmRegistrarCargo.id = cargo.id;
			this.frmRegistrarCargo.cargo = cargo.cargo;
			this.frmRegistrarCargo.descripcion = cargo.descripcion;
			this.frmRegistrarCargo.habilitado = cargo.habilitado;
			this.frmRegistrarCargo.modo = "EDITAR";

			document.getElementById("modalRegistrarCargo").style.display = "block";
			parent.document.getElementById("footer-navigator").style.display = "none";
		},

		GuardarCargo() {
			this.submited = true;
			var self = this;

			if (this.$v.frmRegistrarCargo.$invalid) {
				return false;
			} else {
				axios
					.post(
						route("gen.car.listar_cargos.verificar"),
						self.frmRegistrarCargo
					)
					.then(function (response) {
						if (response.data == "EXISTE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Este CARGO ya EXISTE, intente con otro.",
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
										route("gen.car.listar_cargos.guardar"),
										self.frmRegistrarCargo,
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
														self.ActualizarTabla();
														// self.FiltrarPermisos();
														$("#modalRegistrarCargo").css("display", "none");
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
.slot-cargosListar {
	width: 60% !important;
	margin-left: 20% !important;
}
.modalCargoListar {
	margin-top: 2%;
}
@media (max-width: 900px) {
	.slot-cargosListar {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.modalCargoListar {
		margin-top: 20%;
	}
}
</style>
