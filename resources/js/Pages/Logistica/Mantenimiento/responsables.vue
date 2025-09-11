<template>
	<layout ref="layout">
		<div class="slot_body slot-responsables" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'RESPONSABLES'"></headerClose>
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
								title="Nuevo RESPONSABLE"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
								<!-- <span class="text">Nuevo</span> -->
							</button>
						</div>
						<table class="table" width="100%" id="tblResponsables">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>RESPONSABLE</th>
									<th>ABREVIACIÓN</th>
									<th>AGENCIA</th>
									<th>USUARIO_ASIGNADO</th>
									<th>ENCARGADO_AGENCIA</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in responsables" :key="index">
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar RESPONSABLE"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered">
										{{ item.responsable }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.abreviacion }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.agencia }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.usuario == "0" ? "SIN ASIGNAR" : item.usuario }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.encargado_agencia == "0" ? "-" : "✔" }}
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

				<div class="modal" id="mdlDatosResponsable">
					<div class="modal-content w-45 mdlDatosResponsable">
						<div class="content" style="display: block">
							<div class="card">
								<headerCloseModal
									:titulo_modal="titulo_modal"
									:nombre_modal="'mdlDatosResponsable'"
								>
								</headerCloseModal>

								<div class="card-body card-block">
									<form autocomplete="off" @submit.prevent="Guardar">
										<div class="form-row">
											<div class="form-group col-md-7 col-7">
												<label class="label-title">NOMBRE</label>
												<span
													v-if="
														submited &&
														!$v.frmDatosResponsable.responsable.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													class="form-control mayus"
													maxlength="50"
													v-model="frmDatosResponsable.responsable"
												/>
											</div>
											<div class="form-group col-md-5 col-5">
												<label class="label-title">ABREVIACIÓN</label>
												<span
													v-if="
														submited &&
														!$v.frmDatosResponsable.abreviacion.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													class="form-control mayus center"
													maxlength="20"
													v-model="frmDatosResponsable.abreviacion"
												/>
											</div>

											<div class="form-group col-md-4 col-6">
												<label class="label-title">AGENCIA</label>
												<span
													v-if="
														submited &&
														!$v.frmDatosResponsable.agencia_id.nozero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													class="form-control center"
													v-model.number="frmDatosResponsable.agencia_id"
													@change="FiltrarUsuarios"
												>
													<option :value="0">Seleccione...</option>
													<option
														v-for="(item, index) in agencias"
														:key="index"
														:value="item.id_agencia"
													>
														{{ item.nombre }}
													</option>
												</select>
											</div>

											<div class="form-group col-md-3 col-6">
												<label class="label-title">USUARIO</label>

												<select
													class="form-control center"
													v-model.number="frmDatosResponsable.usuario_id"
												>
													<option :value="0" selected>Sin asignar</option>
													<option
														v-for="(item, index) in usuarios_filtrados"
														:key="index"
														:value="item.dni"
													>
														{{ item.usuario }}
													</option>
												</select>
												<div class="row mt-1">
													<div class="form-check">
														<input
															id="chbUsuariosHabilitados"
															type="checkbox"
															@change="FiltrarUsuarios"
															v-model="solo_habilitados"
														/>
														<label
															class="form-check-label label-title"
															for="chbUsuariosHabilitados"
														>
															sólo habilitados
														</label>
													</div>
												</div>
											</div>
											<div class="form-group col-md-5 col-12">
												<label class="label-title">DESCRIPCIÓN</label>

												<textarea
													class="form-control mayus"
													rows="2"
													maxlength="200"
													v-model="frmDatosResponsable.descripcion"
												/>
											</div>
											<div class="form-row">
												<div class="form-check">
													<input
														id="chbEncargado"
														type="checkbox"
														v-model="frmDatosResponsable.encargado_agencia"
													/>
													<label
														class="form-check-label label-title"
														for="chbEncargado"
													>
														ENCARGADO DE AGENCIA
													</label>
												</div>
											</div>
											<div
												class="form-row"
												v-if="frmDatosResponsable.modo == 'EDITAR'"
											>
												<div class="form-check">
													<input
														id="chbHabilitado"
														type="checkbox"
														v-model="frmDatosResponsable.habilitado"
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
											title="Guardar RESPONSABLE"
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
		responsables: Array,
		agencias: Array,
		usuarios: Array,
	},

	data() {
		return {
			submited: false,
			titulo_modal: null,
			usuarios_filtrados: [],
			solo_habilitados: true,
			frmDatosResponsable: {
				modo: null,
				id: null,
				responsable: null,
				abreviacion: null,
				descripcion: null,
				agencia_id: null,
				usuario_id: null,
				encargado_agencia: false,
				habilitado: null,
			},
		};
	},

	validations: {
		frmDatosResponsable: {
			responsable: { required },
			abreviacion: { required },
			agencia_id: { nozero },
		},
	},

	watch: {
		responsables() {
			$("#tblResponsables").DataTable().destroy();
			this.TablaResponsables();
		},
	},
	mounted() {
		this.TablaResponsables();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		TablaResponsables() {
			this.$nextTick(() => {
				var table = $("#tblResponsables").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[3, "asc"]],
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
		FiltrarUsuarios() {
			let agencia_id = this.frmDatosResponsable.agencia_id;

			if (agencia_id == 0) {
				this.usuarios_filtrados = [];
			} else {
				if (this.solo_habilitados) {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) => item.agencia_id == agencia_id && item.habilitado == 1
					);
				} else {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) => item.agencia_id == agencia_id
					);
				}
			}
			this.frmDatosResponsable.usuario_id = 0;
		},
		Nuevo() {
			this.submited = false;
			this.titulo_modal = "NUEVO RESPONSABLE";
			this.frmDatosResponsable.modo = "NUEVO";
			this.frmDatosResponsable.responsable = null;
			this.frmDatosResponsable.abreviacion = null;
			this.frmDatosResponsable.agencia_id = 0;
			this.frmDatosResponsable.usuario_id = 0;
			this.frmDatosResponsable.encargado_agencia = false;
			this.frmDatosResponsable.descripcion = null;
			this.frmDatosResponsable.habilitado = 1;

			$("#mdlDatosResponsable").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR RESPONSABLE";
			this.frmDatosResponsable.modo = "EDITAR";
			this.frmDatosResponsable.id = item.id;
			this.frmDatosResponsable.responsable = item.responsable;
			this.frmDatosResponsable.abreviacion = item.abreviacion;
			this.frmDatosResponsable.agencia_id = item.agencia_id;
			this.FiltrarUsuarios();
			this.frmDatosResponsable.usuario_id = item.usuario_id;
			this.frmDatosResponsable.encargado_agencia = item.encargado_agencia;
			this.frmDatosResponsable.descripcion = item.descripcion;
			this.frmDatosResponsable.habilitado = item.habilitado;

			$("#mdlDatosResponsable").css("display", "block");
		},

		Guardar() {
			this.submited = true;
			self = this;
			if (this.$v.frmDatosResponsable.$invalid) {
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
								route("log.man.responsables.verificar"),
								self.frmDatosResponsable
							)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este RESPONSABLE ya existe",
										allowOutsideClick: false,
									});
									return false;
								} else {
									self.$inertia.post(
										route("log.man.responsables.guardar"),
										self.frmDatosResponsable,
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
														$("#mdlDatosResponsable").css("display", "none");
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
.slot-responsables {
	width: 60% !important;
	margin-left: 20% !important;
}

.mdlDatosResponsable {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-responsables {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosResponsable {
		margin-top: 20%;
	}
}
</style>
