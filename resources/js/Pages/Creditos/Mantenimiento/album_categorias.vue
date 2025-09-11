<template>
	<layout ref="layout">
		<div class="slot_body slot-creditos-productos" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CATEGORÍAS DE FOTOGRAFÍAS'"></headerClose>
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
								title="Nueva CATEGORÍA"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<!-- <span class="text">Nuevo</span> -->
							</button>
						</div>

						<table class="table table-hover" id="tblCategorias" width="100%">
							<thead>
								<tr>
									<th style="width: 75px !important">EDITAR</th>
									<th>CATEGORÍA</th>
									<th>DESCRIPCIÓN</th>
									<th>NUEVO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(categoria, index) in album_categorias" :key="index">
									<td class="table-bordered" align="center" width="75px">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(categoria)"
											title="Editar CATEGORIA"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="center">
										{{ categoria.categoria }}
									</td>
									<td class="table-bordered">
										{{
											categoria.descripcion == null
												? "-"
												: categoria.descripcion
										}}
									</td>
									<td class="table-bordered" align="center">
										{{ categoria.nuevo == 1 ? "SI" : "NO" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosCategoria" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosCategoria">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosCategoria'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-6 col-6">
											<label class="label-title">CATEGORÍA</label>
											<span
												v-if="
													submited && !$v.frmDatosCategoria.categoria.required
												"
												class="span-error-message"
											>
												*
											</span>
											<input
												type="text"
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosCategoria.categoria"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus"
												maxlength="200"
												rows="2"
												v-model="frmDatosCategoria.descripcion"
											></textarea>
										</div>
										<div class="row" v-if="frmDatosCategoria.modo == 'EDITAR'">
											<div class="form-check">
												<input
													id="chbNuevo"
													type="checkbox"
													v-model="frmDatosCategoria.nuevo"
												/>
												<label
													class="form-check-label label-title"
													for="chbNuevo"
												>
													NUEVO
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
										title="Guardar PRODUCTO"
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
import { required } from "vuelidate/lib/validators";

import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		album_categorias: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: "NUEVA CATEGORÍA",
			frmDatosCategoria: {
				modo: "",
				id: null,
				categoria: null,
				descripcion: null,
				nuevo: false,
			},
		};
	},
	validations: {
		frmDatosCategoria: {
			categoria: { required },
		},
	},

	watch: {
		productos() {
			$("#tblCategorias").DataTable().destroy();
			this.TablaCategorias();
		},
	},

	mounted() {
		this.TablaCategorias();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		TablaCategorias() {
			this.$nextTick(() => {
				var table = $("#tblCategorias").DataTable({
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
			this.titulo_modal = "NUEVO CATEGORÍA";
			this.frmDatosCategoria.modo = "NUEVO";
			this.frmDatosCategoria.id = 0;
			this.frmDatosCategoria.categoria = null;
			this.frmDatosCategoria.descripcion = null;
			this.frmDatosCategoria.nuevo = true;

			$("#mdlDatosCategoria").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR CATEGORÍA";
			this.frmDatosCategoria.modo = "EDITAR";
			this.frmDatosCategoria.id = item.id;
			this.frmDatosCategoria.categoria = item.categoria;
			this.frmDatosCategoria.descripcion = item.descripcion;
			this.frmDatosCategoria.nuevo = item.nuevo;

			$("#mdlDatosCategoria").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosCategoria.$invalid) {
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
								route("man.cli_album_categorias.verificar"),
								self.frmDatosCategoria
							)
							.then(function (response) {
								if (response.data == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este PRODUCTO ya existe",
									});
									return false;
								} else {
									self.$inertia.post(
										route("man.cli_album_categorias.guardar"),
										self.frmDatosCategoria,
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
														$("#mdlDatosCategoria").css("display", "none");
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
.slot-creditos-productos {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosCategoria {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-creditos-productos {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosCategoria {
		margin-top: 20%;
	}
}
</style>
