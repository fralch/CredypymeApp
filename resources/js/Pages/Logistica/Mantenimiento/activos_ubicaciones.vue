<template>
	<layout ref="layout">
		<div class="slot_body slot-activos-ubicaciones" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'UBICACIONES DE ACTIVOS'"></headerClose>
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
								title="Nueva UBICACIÓN"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<!-- <span class="text">Nuevo</span> -->
							</button>
						</div>
						<table class="table table-hover" width="100%" id="tblUbicaciones">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>UBICACIÓN</th>
									<th>ABREV.</th>
									<th>AGENCIA</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in ubicaciones" :key="index">
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar UBICACIÓN"
										>
											<span class="icon text-white">
												<i class="fas fa-pen"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered">
										{{ item.ubicacion }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.abreviacion }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.agencia }}
									</td>
									<td class="table-bordered">
										{{ item.descripcion == null ? "-" : item.descripcion }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.habilitado == "0" ? "NO" : "SI" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal" id="mdlDatosUbicacion">
				<div class="modal-content w-35 mdlDatosUbicacion">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosUbicacion'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-7 col-7">
											<label class="label-title">UBICACIÓN</label>
											<span
												v-if="
													submited && !$v.frmDatosUbicacion.ubicacion.required
												"
												class="span-error-message"
											>
												*
											</span>
											<input
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosUbicacion.ubicacion"
											/>
										</div>
										<div class="form-group col-md-5 col-5">
											<label class="label-title">ABREVIACIÓN</label>
											<span
												v-if="
													submited && !$v.frmDatosUbicacion.abreviacion.required
												"
												class="span-error-message"
											>
												*
											</span>
											<input
												class="form-control mayus center"
												maxlength="10"
												v-model="frmDatosUbicacion.abreviacion"
											/>
										</div>
										<div class="form-group col-md-5 col-5">
											<label class="label-title">AGENCIA</label>
											<span
												v-if="submited && !$v.frmDatosUbicacion.agencia.nozero"
												class="span-error-message"
											>
												*
											</span>
											<select
												class="form-control center"
												v-model="frmDatosUbicacion.agencia_id"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(agencia, index) in agencias"
													:key="index"
													:value="agencia.id_agencia"
												>
													{{ agencia.nombre }}
												</option>
											</select>
										</div>
										<div class="form-group col-md-7 col-7">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus"
												rows="2"
												maxlength="200"
												v-model="frmDatosUbicacion.descripcion"
											></textarea>
										</div>
										<div
											class="form-row"
											v-if="frmDatosUbicacion.modo == 'EDITAR'"
										>
											<div class="form-check">
												<input
													id="chbHabilitado"
													type="checkbox"
													v-model="frmDatosUbicacion.habilitado"
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
										class="btn btn-action btn-icon-split"
										@click="Guardar"
										title="Guardar UBICACIÓN"
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
		ubicaciones: Array,
		agencias: Array,
	},
	data() {
		return {
			submited: false,
			titulo_modal: null,

			frmDatosUbicacion: {
				modo: null,
				id: null,
				ubicacion: null,
				abreviacion: 0,
				agencia_id: null,
				descripcion: null,
				habilitado: null,
			},
		};
	},

	validations: {
		frmDatosUbicacion: {
			ubicacion: { required },
			abreviacion: { required },
			agencia: { nozero },
		},
	},

	watch: {
		ubicaciones() {
			$("#tblUbicaciones").DataTable().destroy();
			this.TablaUbicaciones();
		},
	},
	mounted() {
		this.TablaUbicaciones();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		TablaUbicaciones() {
			this.$nextTick(() => {
				var table = $("#tblUbicaciones").DataTable({
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
			this.titulo_modal = "NUEVA UBICACIÓN";
			this.frmDatosUbicacion.ubicacion = null;
			this.frmDatosUbicacion.abreviacion = null;
			this.frmDatosUbicacion.agencia_id = 0;
			this.frmDatosUbicacion.descripcion = null;
			this.frmDatosUbicacion.habilitado = 1;
			this.frmDatosUbicacion.modo = "NUEVO";
			$("#mdlDatosUbicacion").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR UBICACIÓN";
			this.frmDatosUbicacion.id = item.id;
			this.frmDatosUbicacion.ubicacion = item.ubicacion;
			this.frmDatosUbicacion.abreviacion = item.abreviacion;
			this.frmDatosUbicacion.agencia_id = item.agencia_id;
			this.frmDatosUbicacion.descripcion = item.descripcion;
			this.frmDatosUbicacion.habilitado = item.habilitado;
			this.frmDatosUbicacion.modo = "EDITAR";
			$("#mdlDatosUbicacion").css("display", "block");
		},

		Guardar() {
			this.submited = true;
			self = this;
			if (this.$v.frmDatosUbicacion.$invalid) {
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
								route("log.man.act_ubicaciones.verificar"),
								self.frmDatosUbicacion
							)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Esta UBICACIÓN ya existe",
										allowOutsideClick: false,
									});
									return false;
								} else {
									self.$inertia.post(
										route("log.man.act_ubicaciones.guardar"),
										self.frmDatosUbicacion,
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
														$("#mdlDatosUbicacion").css("display", "none");
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
.slot-activos-ubicaciones {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosUbicacion {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-activos-ubicaciones {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosUbicacion {
		margin-top: 20%;
	}
}
</style>
