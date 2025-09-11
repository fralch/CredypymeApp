<template>
	<layout ref="layout">
		<div class="slot_body slot-activos-nombres" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'NOMBRES DE ACTIVOS'"></headerClose>
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
								title="Nuevo NOMBRE"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<!-- <span class="text">Nuevo</span> -->
							</button>
						</div>

						<table class="table table-hover" width="100%" id="tblNombres">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>NOMBRE</th>
									<th>CATEGORÍA</th>
									<th>HABILITADO</th>
									<th>DESCRIPCIÓN</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in nombres" :key="index">
									<td class="table-bordered" align="center" width="75px">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar NOMBRE"
										>
											<span class="icon text-white">
												<i class="fas fa-pen"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered">
										{{ item.nombre }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.categoria }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.habilitado == "0" ? "NO" : "SI" }}
									</td>
									<td class="table-bordered">
										{{ item.descripcion == null ? "-" : item.descripcion }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal" id="mdlDatosNombre">
				<div class="modal-content w-35 mdlDatosNombre">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosNombre'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-7 col-6">
											<label class="label-title">NOMBRE</label>
											<span
												v-if="submited && !$v.frmDatosNombre.nombre.required"
												class="span-error-message"
											>
												*
											</span>
											<input
												type="text"
												maxlength="50"
												class="form-control mayus"
												v-model="frmDatosNombre.nombre"
											/>
										</div>
										<div class="form-group col-md-5 col-6">
											<label class="label-title">CATEGORÍA</label>
											<span
												v-if="
													submited && !$v.frmDatosNombre.categoria_id.nozero
												"
												class="span-error-message"
											>
												*
											</span>
											<select
												class="form-control"
												v-model="frmDatosNombre.categoria_id"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in categorias"
													:key="index"
													:value="item.id"
												>
													{{ item.categoria }}
												</option>
											</select>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus"
												rows="2"
												maxlength="200"
												v-model="frmDatosNombre.descripcion"
											></textarea>
										</div>
									</div>
									<div class="form-row" v-if="frmDatosNombre.modo == 'EDITAR'">
										<div class="form-check">
											<input
												id="chbHabilitado"
												type="checkbox"
												v-model="frmDatosNombre.habilitado"
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
										title="Guardar NOMBRE"
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
const nozero = (value) => value != 0;
export default {
	components: { layout, headerClose, headerCloseModal },
	props: {
		nombres: Array,
		categorias: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: null,
			frmDatosNombre: {
				modo: null,
				id: null,
				nombre: null,
				categoria_id: null,
				habilitado: null,
				descripcion: null,
			},
		};
	},

	validations: {
		frmDatosNombre: {
			nombre: { required },
			categoria_id: { nozero },
		},
	},

	watch: {
		nombres() {
			$("#tblNombres").DataTable().destroy();
			this.TablaNombres();
		},
	},
	mounted() {
		this.TablaNombres();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		TablaNombres() {
			this.$nextTick(() => {
				var table = $("#tblNombres").DataTable({
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
			this.titulo_modal = "NUEVO NOMBRE";
			this.frmDatosNombre.id = null;
			this.frmDatosNombre.nombre = null;
			this.frmDatosNombre.categoria_id = 0;
			this.frmDatosNombre.descripcion = null;
			this.frmDatosNombre.habilitado = 1;
			this.frmDatosNombre.modo = "NUEVO";
			$("#mdlDatosNombre").css("display", "block");
		},

		Editar(nombre) {
			this.submited = false;
			this.titulo_modal = "EDITAR NOMBRE";
			this.frmDatosNombre.id = nombre.id;
			this.frmDatosNombre.nombre = nombre.nombre;
			this.frmDatosNombre.categoria_id = nombre.categoria_id;
			this.frmDatosNombre.descripcion = nombre.descripcion;
			this.frmDatosNombre.habilitado = nombre.habilitado;
			this.frmDatosNombre.modo = "EDITAR";
			$("#mdlDatosNombre").css("display", "block");
		},

		Guardar() {
			this.submited = true;
			self = this;
			if (this.$v.frmDatosNombre.$invalid) {
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
							.post(route("log.man.act_nombres.verificar"), self.frmDatosNombre)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este NOMBRE ya existe.",
										allowOutsideClick: false,
									});
									return false;
								} else {
									self.$inertia.post(
										route("log.man.act_nombres.guardar"),
										self.frmDatosNombre,
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
														$("#mdlDatosNombre").css("display", "none");
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
.slot-activos-nombres {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosNombre {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-activos-nombres {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosNombre {
		margin-top: 20%;
	}
}
</style>

