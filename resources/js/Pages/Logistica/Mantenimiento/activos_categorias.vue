<template>
	<layout ref="layout">
		<div class="slot_body slot-activos-categorias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CATEGORÍAS DE ACTIVOS'"></headerClose>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div id="row">
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
						</div>
						<table class="table table-hover" width="100%" id="tblCategorias">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>CATEGORÍA</th>
									<th>DESCRIPCIÓN</th>
									<th>VIDA_ÚTIL</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in categorias" :key="index">
									<td class="table-bordered" align="center" width="75px">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar CATEGORÍA"
										>
											<span class="icon text-white">
												<i class="fas fa-pen"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered">
										{{ item.categoria }}
									</td>
									<td class="table-bordered">
										{{ item.descripcion == null ? "-" : item.descripcion }}
									</td>
									<td class="table-bordered" align="center">
										{{ roundTo(item.vida_util, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.habilitado == 0 ? "NO" : "SI" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal" id="mdlDatosCategoria">
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
										<div class="form-group col-md-8 col-7">
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
												maxlength="50"
												class="form-control mayus"
												v-model="frmDatosCategoria.categoria"
											/>
										</div>
										<div class="form-group col-md-4 col-5">
											<label class="label-title">VIDA ÚTIL</label>
											<span
												v-if="
													submited &&
													(!$v.frmDatosCategoria.vida_util.onlyNumbers ||
														!$v.frmDatosCategoria.vida_util.noZero ||
														!$v.frmDatosCategoria.vida_util.required)
												"
												class="span-error-message"
											>
												*
											</span>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">AÑOS</div>
												</div>
												<input
													type="number"
													class="form-control center"
													v-model.number="frmDatosCategoria.vida_util"
													min="0"
													step="0.1"
													lang="en"
													placeholder="mín. 0.1"
													@change="
														frmDatosCategoria.vida_util = roundTo(
															frmDatosCategoria.vida_util,
															2
														)
													"
												/>
											</div>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus"
												maxlength="200"
												rows="2"
												v-model="frmDatosCategoria.descripcion"
											></textarea>
										</div>
									</div>
									<div
										class="form-row"
										v-if="frmDatosCategoria.modo == 'EDITAR'"
									>
										<div class="form-check">
											<input
												id="chbHabilitado"
												type="checkbox"
												v-model="frmDatosCategoria.habilitado"
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
										@click="Guardar"
										title="Guardar CATEGORÍA"
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
const onlyNumbers = (value) => Number(value) != "NaN";
const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose, headerCloseModal },
	props: {
		categorias: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: null,
			frmDatosCategoria: {
				modo: null,
				id: null,
				categoria: null,
				descripcion: null,
				vida_util: null,
				habilitado: null,
			},
		};
	},

	validations: {
		frmDatosCategoria: {
			categoria: { required },
			vida_util: { onlyNumbers, noZero, required },
		},
	},

	watch: {
		categorias() {
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
		roundTo(value, decimal_places) {
			return parseFloat(value).toFixed(decimal_places);
		},
		TablaCategorias() {
			// $("#tblCategorias").DataTable().destroy();
			this.$nextTick(() => {
				var table = $("#tblCategorias").DataTable({
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
			this.titulo_modal = "NUEVA CATEGORÍA";
			this.frmDatosCategoria.modo = "NUEVO";
			this.frmDatosCategoria.categoria = null;
			this.frmDatosCategoria.vida_util = null;
			this.frmDatosCategoria.descripcion = null;
			this.frmDatosCategoria.habilitado = 1;

			$("#mdlDatosCategoria").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR CATEGORÍA";
			this.frmDatosCategoria.modo = "EDITAR";
			this.frmDatosCategoria.id = item.id;
			this.frmDatosCategoria.categoria = item.categoria;
			this.frmDatosCategoria.descripcion = item.descripcion;
			this.frmDatosCategoria.vida_util = this.roundTo(item.vida_util, 2);
			this.frmDatosCategoria.habilitado = item.habilitado;

			$("#mdlDatosCategoria").css("display", "block");
		},

		Guardar() {
			this.submited = true;
			self = this;
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
								route("log.man.act_categorias.verificar"),
								self.frmDatosCategoria
							)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Esta CATEGORÍA ya existe",
										allowOutsideClick: false,
									});
									return false;
								} else {
									self.$inertia.post(
										route("log.man.act_categorias.guardar"),
										self.frmDatosCategoria,
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
.slot-activos-categorias {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosCategoria {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-activos-categorias {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosCategoria {
		margin-top: 20%;
	}
}
</style>
