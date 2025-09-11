<template>
	<layout ref="layout">
		<div class="slot_body slot-creditos-garantias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'GARANTÍAS DE CRÉDITO'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-4 col-md-6 col-7">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscar"
										placeholder="Buscar..."
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>

							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										@change="getListaGarantias()"
										v-model="frmDatosGarantia.agencia_seleccionada"
									>
										<option
											v-for="(item, index) in agencias_permitidas"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group col-md-2">
								<button
									class="btn btn-action btn-icon-split"
									@click="Nuevo"
									title="Nueva GARANTÍA"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
								</button>
							</div>
						</div>
						<!-- <div class="row col-md-3 col-2 ml-1" style="float: left">
              <button
                class="btn btn-action btn-icon-split"
                @click="Nuevo"
                title="Nueva GARANTÍA"
              >
                <span class="icon text-white">
                  <i class="fas fa-plus"></i>
                </span>

              </button>
            </div> -->
						<div class="card-title">LISTA DE RESULTADOS</div>
						<table class="table table-hover" id="tblGarantias" width="100%">
							<thead>
								<tr>
									<th style="width: 75px !important">EDITAR</th>
									<th>GARANTÍA</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in garantias_seleccionados"
									:key="index"
								>
									<td class="table-bordered" align="center" width="75px">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar GARANTÍA"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="center">
										{{ item.garantia }}
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
			<div id="mdlDatosGarantia" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosGarantia">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosGarantia'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-6 col-6">
											<label class="label-title">GARANTÍA</label>
											<span
												v-if="
													submited && !$v.frmDatosGarantia.garantia.required
												"
												class="span-error-message"
											>
												*
											</span>
											<input
												type="text"
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosGarantia.garantia"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">DESCRIPCIÓN</label>
											<span
												v-if="
													submited && !$v.frmDatosGarantia.descripcion.required
												"
												class="span-error-message"
											>
												*
											</span>
											<textarea
												class="form-control mayus"
												maxlength="300"
												rows="2"
												v-model="frmDatosGarantia.descripcion"
											></textarea>
										</div>
										<div class="row" v-if="frmDatosGarantia.modo == 'EDITAR'">
											<div class="form-check">
												<input
													id="chbHabilitado"
													type="checkbox"
													v-model="frmDatosGarantia.habilitado"
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
										title="Guardar GARANTÍA"
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
		garantias: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: "NUEVA GARANTÍA",
			agencias_permitidas: [],
			garantias_seleccionados: this.garantias,
			frmDatosGarantia: {
				agencia_seleccionada: 0,
				modo: "",
				id: null,
				garantia: null,
				descripcion: null,
				habilitado: false,
			},
		};
	},
	validations: {
		frmDatosGarantia: {
			garantia: { required },
			descripcion: { required },
		},
	},

	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			this.frmDatosGarantia.agencia_seleccionada = agencia_id;
		},
		garantias_seleccionados() {
			$("#tblGarantias").DataTable().destroy();
			this.TablaGarantias();
		},
	},

	mounted() {
		this.TablaGarantias();
		this.ListarAgenciasPermitidas();
	},

	methods: {
		getListaGarantias() {
			let self = this;
			axios
				.post(route("man.cre_garantias.listar"), {
					id_agencia: this.frmDatosGarantia.agencia_seleccionada,
				})
				.then(function (response) {
					self.garantias_seleccionados = response.data;
				});
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_MANTENIMIENTO/CREDITO_GARANTIAS"
			);
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		TablaGarantias() {
			this.$nextTick(() => {
				var table = $("#tblGarantias").DataTable({
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
			this.titulo_modal = "NUEVO GARANTÍA";
			this.frmDatosGarantia.modo = "NUEVO";
			this.frmDatosGarantia.id = 0;
			this.frmDatosGarantia.garantia = null;
			this.frmDatosGarantia.descripcion = null;
			this.frmDatosGarantia.habilitado = true;

			$("#mdlDatosGarantia").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR GARANTÍA";
			this.frmDatosGarantia.modo = "EDITAR";
			this.frmDatosGarantia.id = item.id;
			this.frmDatosGarantia.garantia = item.garantia;
			this.frmDatosGarantia.descripcion = item.descripcion;
			this.frmDatosGarantia.habilitado = item.habilitado;

			$("#mdlDatosGarantia").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosGarantia.$invalid) {
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
							.post(route("man.cre_garantias.verificar"), self.frmDatosGarantia)
							.then(function (response) {
								if (response.data == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este GARANTÍA ya existe",
									});
									return false;
								} else {
									self.$inertia.post(
										route("man.cre_garantias.guardar"),
										self.frmDatosGarantia,
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
														$("#mdlDatosGarantia").css("display", "none");
														self.getListaGarantias();
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
.slot-creditos-garantias {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosGarantia {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-creditos-garantias {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosGarantia {
		margin-top: 20%;
	}
}
</style>
