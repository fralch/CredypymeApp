<template>
	<layout ref="layout">
		<div class="slot_body slot-suministros-proveedores" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'PROVEEDORES DE SUMINISTROS'"></headerClose>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div class="input-group row col-md-7 col-7" style="float: left">
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
						<div class="row col-md-3 col-2 ml-1" style="float: left">
							<button
								class="btn btn-action btn-icon-split"
								@click="Nuevo"
								title="Nuevo PROVEEDOR"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<!-- <span class="text">Nuevo</span> -->
							</button>
						</div>

						<table class="table table-hover" id="tblProveedores">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>PROVEEDOR</th>
									<th>REPRESENTANTE</th>
									<th>DIRECCIÓN</th>
									<th>RUC</th>
									<th>DNI</th>
									<th>TELEFONO</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in proveedores" :key="index">
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar PROVEEDORES"
										>
											<span class="icon text-white">
												<i class="fas fa-pen"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered">
										{{ item.proveedor }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.representante == null ? "-" : item.representante }}
									</td>
									<td class="table-bordered">
										{{ item.direccion }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.ruc == null ? "-" : item.ruc }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.dni == null ? "-" : item.dni }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.telefono == 1 ? "-" : item.telefono }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.habilitado == 1 ? "SI" : "NO" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosProveedor" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosProveedor">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosProveedor'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-6 col-6">
											<label class="label-title">PROVEEDOR</label>
											<span
												v-if="
													submited && !$v.frmDatosProveedor.proveedor.required
												"
												class="span-error-message"
											>
												*
											</span>
											<textarea
												class="form-control mayus"
												maxlength="100"
												rows="1"
												v-model="frmDatosProveedor.proveedor"
												autofocus
											></textarea>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">REPRESENTANTE</label>
											<textarea
												class="form-control mayus"
												maxlength="200"
												rows="1"
												v-model="frmDatosProveedor.representante"
											></textarea>
										</div>
										<div class="form-group col-md-12 col-12">
											<label for="txtDireccionProveedor" class="label-title"
												>DIRECCIÓN</label
											>
											<span
												v-if="
													submited && !$v.frmDatosProveedor.direccion.required
												"
												class="span-error-message"
											>
												*
											</span>
											<textarea
												class="form-control mayus"
												maxlength="100"
												rows="1"
												v-model="frmDatosProveedor.direccion"
											></textarea>
										</div>

										<div class="form-group col-md-4 col-4">
											<label class="label-title">RUC</label>
											<input
												type="number"
												class="form-control center"
												maxlength="11"
												min="0"
												step="1"
												v-model="frmDatosProveedor.ruc"
												placeholder="máx. 11 díg."
												oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
											/>
										</div>
										<div class="form-group col-md-4 col-4">
											<label class="label-title">DNI</label>
											<input
												type="number"
												class="form-control center"
												maxlength="8"
												min="0"
												step="1"
												placeholder="máx. 8 díg."
												v-model="frmDatosProveedor.dni"
												oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
											/>
										</div>
										<div class="form-group col-md-4 col-4">
											<label class="label-title">TELÉFONO</label>
											<input
												type="number"
												class="form-control center"
												maxlength="9"
												min="0"
												step="1"
												placeholder="máx. 9 díg."
												v-model="frmDatosProveedor.telefono"
												oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
											/>
										</div>
									</div>
									<div class="row" v-if="frmDatosProveedor.modo == 'EDITAR'">
										<div class="form-check">
											<input
												id="chbHabilitado"
												type="checkbox"
												v-model="frmDatosProveedor.habilitado"
											/>
											<label
												class="form-check-label label-title"
												for="chbHabilitado"
											>
												HABILITADO
											</label>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar()"
										title="Guardar PROVEEDOR"
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
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		proveedores: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: "NUEVO PROVEEDOR",
			frmDatosProveedor: {
				modo: null,
				id: null,
				proveedor: null,
				representante: null,
				direccion: null,
				ruc: null,
				dni: null,
				telefono: null,
				habilitado: null,
			},
		};
	},

	validations: {
		frmDatosProveedor: {
			proveedor: { required },
			direccion: { required },
		},
	},

	watch: {
		proveedores() {
			$("#tblProveedores").DataTable().destroy();
			this.TablaProveedores();
		},
	},

	mounted() {
		this.TablaProveedores();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		TablaProveedores() {
			this.$nextTick(() => {
				var table = $("#tblProveedores").DataTable({
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
		Nuevo() {
			this.submited = false;
			this.titulo_modal = "NUEVO PROVEEDOR";
			this.frmDatosProveedor.modo = "NUEVO";
			this.frmDatosProveedor.id = 0;
			this.frmDatosProveedor.proveedor = null;
			this.frmDatosProveedor.representante = null;
			this.frmDatosProveedor.direccion = null;
			this.frmDatosProveedor.ruc = null;
			this.frmDatosProveedor.dni = null;
			this.frmDatosProveedor.telefono = null;
			this.frmDatosProveedor.habilitado = 1;
			this.frmDatosProveedor.modo = "NUEVO";

			$("#mdlDatosProveedor").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR PROVEEDOR";
			this.frmDatosProveedor.modo = "EDITAR";
			this.frmDatosProveedor.id = item.id;
			this.frmDatosProveedor.proveedor = item.proveedor;
			this.frmDatosProveedor.direccion = item.direccion;
			this.frmDatosProveedor.representante = item.representante;
			this.frmDatosProveedor.ruc = item.ruc;
			this.frmDatosProveedor.dni = item.dni;
			this.frmDatosProveedor.telefono = item.telefono;
			this.frmDatosProveedor.habilitado = item.habilitado;

			$("#mdlDatosProveedor").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			} else {
				Swal.fire({
					icon: "question",
					text: "¿DESEA GUARDAR LOS CAMBIOS?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						axios
							.post(
								route("log.man.sum_proveedores.verificar"),
								self.frmDatosProveedor
							)
							.then(function (response) {
								if (response.data == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este PROVEEDOR ya existe.",
									});
									return false;
								} else {
									self.$inertia.post(
										route("log.man.sum_proveedores.guardar"),
										self.frmDatosProveedor,
										{
											preserveScroll: true,
											onStart: (visit) => {
												let timerInterval;
												Swal.fire({
													title: "ESPERE POR FAVOR...",
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
														$("#mdlDatosProveedor").css("display", "none");
													},
												});
											},
										}
									);
								}
							});
					}
				});
			}
		},
	},
};
</script>

<style lang="css">
.slot-suministros-proveedores {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosProveedor {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-suministros-proveedores {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosProveedor {
		margin-top: 20%;
	}
}
</style>
