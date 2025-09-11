<template>
	<layout ref="layout">
		<div class="slot_body slot-creditos-sectores" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'SECTORES DE CRÉDITO'"></headerClose>

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
										id="inpBuscarSectores"
										placeholder="Escriba el texto a buscar"
										autocomplete="off"
										spellcheck="false"
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
										v-model="frmDatosSector.agencia_seleccionada"
										@change="Listar"
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
									title="Nuevo SECTOR"
									:disabled="frmDatosSector.agencia_seleccionada == 0"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>
						<div class="card-title">LISTA DE RESULTADOS</div>
						<table class="table table-hover" id="tblSectores" width="100%">
							<thead>
								<tr>
									<th style="width: 75px !important">EDITAR</th>
									<th>SECTOR</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in sectores" :key="index">
									<td class="table-bordered" align="center" width="75px">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar SECTOR"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="center">
										{{ item.sector }}
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
			<div id="mdlDatosSector" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosSector">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosSector'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-6 col-6">
											<label class="label-title">SECTOR</label>
											<span
												v-if="submited && !$v.frmDatosSector.sector.required"
												class="span-error-message"
											>
												*
											</span>
											<input
												type="text"
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosSector.sector"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus"
												maxlength="200"
												rows="2"
												v-model="frmDatosSector.descripcion"
											></textarea>
										</div>
										<div class="row" v-if="frmDatosSector.modo == 'EDITAR'">
											<div class="form-check">
												<input
													id="chbHabilitado"
													type="checkbox"
													v-model="frmDatosSector.habilitado"
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
										title="Guardar SECTOR"
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

	data() {
		return {
			agencias_permitidas: [],
			sectores: [],
			submited: false,

			titulo_modal: "NUEVO SECTOR",
			frmDatosSector: {
				agencia_seleccionada: 0,
				modo: "",
				id: null,
				sector: null,
				descripcion: null,
				habilitado: false,
			},
		};
	},
	validations: {
		frmDatosSector: {
			sector: { required },
		},
	},

	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.frmDatosSector.agencia_seleccionada = mi_agencia[0].id;
				this.Listar();
			} else {
				if (value.length > 0) {
					this.frmDatosSector.agencia_seleccionada = value[0].id;
					this.Listar();
				} else {
					this.frmDatosSector.agencia_seleccionada = 0;
				}
			}
		},
		sectores() {
			$("#tblSectores").DataTable().destroy();
			this.TablaSectores();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaSectores();
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_MANTENIMIENTO/CREDITO_SECTORES"
			);
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		TablaSectores() {
			this.$nextTick(() => {
				var table = $("#tblSectores").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
					info: false,

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
				});

				$("#inpBuscarSectores").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		Listar() {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", this.frmDatosSector.agencia_seleccionada);

			// this.$inertia.post(route("man.cre_sectores.listar"), data);
			axios
				.post(route("man.cre_sectores.listar"), data)
				.then(function (response) {
					self.sectores = response.data.sectores;
				});
		},

		Nuevo() {
			this.submited = false;
			this.titulo_modal = "NUEVO SECTOR";
			this.frmDatosSector.modo = "NUEVO";
			this.frmDatosSector.id = 0;
			this.frmDatosSector.sector = null;
			this.frmDatosSector.descripcion = null;
			this.frmDatosSector.habilitado = true;

			$("#mdlDatosSector").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR SECTOR";
			this.frmDatosSector.modo = "EDITAR";
			this.frmDatosSector.id = item.id;
			this.frmDatosSector.sector = item.sector;
			this.frmDatosSector.descripcion = item.descripcion;
			this.frmDatosSector.habilitado = item.habilitado;

			$("#mdlDatosSector").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosSector.$invalid) {
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
							.post(route("man.cre_sectores.verificar"), self.frmDatosSector)
							.then(function (response) {
								if (response.data == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este SECTOR ya existe",
									});
									return false;
								} else {
									self.$inertia.post(
										route("man.cre_sectores.guardar"),
										self.frmDatosSector,
										{
											preserveScroll: true,

											onStart: () => {
												Swal.fire({
													title: "GUARDANDO",
													text: "Espere porfavor...",
													showConfirmButton: false,
													allowOutsideClick: false,
													willOpen: () => {
														Swal.showLoading();
													},
												});
											},
											onSuccess: () => {
												Swal.fire({
													icon: "success",
													title: "¡ÉXITO!",
													allowOutsideClick: false,
												}).then((result) => {
													if (result.isConfirmed) {
														self.submited = false;
														self.Listar();
														$("#mdlDatosSector").css("display", "none");
													}
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
.slot-creditos-sectores {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosSector {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-creditos-sectores {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosSector {
		margin-top: 20%;
	}
}
</style>
