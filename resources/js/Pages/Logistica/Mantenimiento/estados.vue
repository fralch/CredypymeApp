<template>
	<layout ref="layout">
		<div class="slot_body slot-estados" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ESTADOS'"></headerClose>
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
								title="Nuevo ESTADO"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<!-- <span class="text">Nuevo</span> -->
							</button>
						</div>

						<table class="table table-hover" id="tblEstados" width="100%">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>ESTADO</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in estados" :key="index">
									<td class="table-bordered" align="center" width="75px">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar ESTADO"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="center">
										{{ item.estado }}
									</td>
									<td class="table-bordered">
										{{ item.descripcion == null ? "-" : item.descripcion }}
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
			<div id="mdlDatosEstado" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosEstado">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosEstado'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-6 col-6">
											<label class="label-title">ESTADO</label>
											<span
												v-if="submited && !$v.frmDatosEstado.estado.required"
												class="span-error-message"
											>
												*
											</span>
											<input
												type="text"
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosEstado.estado"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus"
												maxlength="200"
												rows="2"
												v-model="frmDatosEstado.descripcion"
											></textarea>
										</div>
										<div class="row" v-if="frmDatosEstado.modo == 'EDITAR'">
											<div class="form-check">
												<input
													id="chbHabilitado"
													type="checkbox"
													v-model="frmDatosEstado.habilitado"
												/>
												<label
													class="form-check-label label-title"
													for="chbHabilitado"
												>
													HABILITADO
												</label>
											</div>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split mb-1"
										@click="Guardar()"
										title="Guardar ESTADO"
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
		estados: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: "NUEVO ESTADO",
			frmDatosEstado: {
				modo: "",
				id: null,
				estado: null,
				descripcion: null,
				habilitado: false,
			},
		};
	},
	validations: {
		frmDatosEstado: {
			estado: { required },
		},
	},

	watch: {
		estados() {
			$("#tblEstados").DataTable().destroy();
			this.TablaEstados();
		},
	},

	mounted() {
		this.TablaEstados();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		TablaEstados() {
			this.$nextTick(() => {
				var table = $("#tblEstados").DataTable({
					scrollY: "350px",
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

		Nuevo() {
			this.submited = false;
			this.titulo_modal = "NUEVO ESTADO";
			this.frmDatosEstado.modo = "NUEVO";
			this.frmDatosEstado.id = 0;
			this.frmDatosEstado.estado = null;
			this.frmDatosEstado.descripcion = null;
			this.frmDatosEstado.habilitado = true;

			$("#mdlDatosEstado").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR ESTADO";
			this.frmDatosEstado.modo = "EDITAR";
			this.frmDatosEstado.id = item.id;
			this.frmDatosEstado.estado = item.estado;
			this.frmDatosEstado.descripcion = item.descripcion;
			this.frmDatosEstado.habilitado = item.habilitado;

			$("#mdlDatosEstado").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosEstado.$invalid) {
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
							.post(route("log.man.estados.verificar"), self.frmDatosEstado)
							.then(function (response) {
								if (response.data == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este ESTADO ya existe",
									});
									return false;
								} else {
									self.$inertia.post(
										route("log.man.estados.guardar"),
										self.frmDatosEstado,
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
														$("#mdlDatosEstado").css("display", "none");
													},
												});
											},
										}
									);
								}
							});
					} else {
						return false;
					}
				});
			}
		},
	},
};
</script>

<style lang="css">
.slot-estados {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosEstado {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-estados {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosEstado {
		margin-top: 20%;
	}
}
</style>
