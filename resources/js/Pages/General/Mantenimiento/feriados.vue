<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body slot-listarFeriados">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LISTA DE FERIADOS'"></headerClose>
					<div class="card-title">LISTA DE RESULTADOS</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-9 col-7">
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
							<div class="row col-md-1 col-2 ml-1">
								<button
									class="btn btn-action btn-icon-split mb-1"
									@click="NuevoFeriado()"
								>
									<span class="icon text-white-50">
										<i class="fas fa-plus" style="color: white"></i>
									</span>
								</button>
							</div>
						</div>
						<table
							class="table"
							id="tblFeriados"
							style="width: 100% !important"
						>
							<thead>
								<tr>
									<th style="min-width: 120px !important">EDITAR</th>
									<th style="min-width: 50px !important">FECHA</th>
									<th style="min-width: 250px !important">MOTIVO</th>
									<th v-for="agencia in agencias" :key="agencia.id_agencia">
										AG_{{ agencia.nombre }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="feriado in feriados_filtrados" :key="feriado.id">
									<td align="center">
										<button
											class="btn btn-danger btn-icon-split"
											@click="QuitarFeriado(feriado.id)"
										>
											<span class="icon text-white-50">
												<i class="fas fa-times" style="color: white"></i>
											</span>
										</button>
										<button
											class="btn btn-action btn-icon-split"
											@click="EditarFecha(feriado)"
										>
											<span class="icon text-white-50">
												<i class="fas fa-edit" style="color: white"></i>
											</span>
										</button>
										<button
											class="btn btn-cancel btn-icon-split"
											v-show="!feriadoEditar.includes(feriado.id)"
											@click="feriadoEditar.push(feriado.id)"
										>
											<span class="icon text-white-50">
												<i class="fas fa-check-double" style="color: white"></i>
											</span>
										</button>
										<button
											v-show="feriadoEditar.includes(feriado.id)"
											class="btn btn-action btn-icon-split"
											@click="EditarAgencias(feriado.id)"
										>
											<span class="icon text-white-50">
												<i class="fas fa-save" style="color: white"></i>
											</span>
										</button>
									</td>
									<td align="center">
										{{ feriado.fecha }}
									</td>
									<td align="left">
										{{ feriado.motivo }}
									</td>

									<td
										v-for="agencia in agencias"
										:key="agencia.id_agencia"
										align="center"
									>
										<div class="align-middle">
											<div class="checkbox">
												<label
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													><input
														:disabled="!feriadoEditar.includes(feriado.id)"
														type="checkbox"
														class="form-control"
														:value="{
															feriado_id: feriado.id,
															agencia_id: agencia.id_agencia,
														}"
														v-model="acceso_agencias_editar" />

													<span class="cr"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="modalRegistrarFeriados" class="modal">
				<!-- Modal content -->
				<div class="modal-content modalFeriadoCrear w-30">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="this.title_modal"
								:nombre_modal="'modalRegistrarFeriados'"
							>
							</headerCloseModal>
							<div class="card-title">DATOS DE LA FECHA</div>
							<div class="card-body card-block">
								<form @submit.prevent="GuardarFeriado">
									<input
										type="text"
										id="txtModo"
										v-model="frmRegistrarFeriados.modo"
										hidden
									/>
									<input
										type="text"
										id="txtIdFeriado"
										hidden
										v-model="frmRegistrarFeriados.id"
									/>
									<div class="form-row">
										<div class="form-group col-md">
											<label
												class="form-control-label label-title"
												for="slcFeriado"
												>ELEGIR FECHA</label
											>
											<input
												class="form-control center"
												type="date"
												id="slcFeriado"
												style="max-width: 250px"
												v-model="frmRegistrarFeriados.fecha"
											/>
											<div
												v-if="
													submited && !$v.frmRegistrarFeriados.fecha.required
												"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<div class="form-group col-md">
											<label class="form-control-label label-title"
												>MOTIVO</label
											>
											<textarea
												class="form-control"
												type="text"
												style="max-width: 250px"
												maxlength="150"
												id="txtMotivo"
												v-model="frmRegistrarFeriados.motivo"
												placeholder="Ingrese el motivo del feriado"
											></textarea>
											<div
												v-if="
													submited && !$v.frmRegistrarFeriados.motivo.required
												"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
									</div>
									<div
										v-if="frmRegistrarFeriados.modo == 'EDITAR'"
										class="form-row"
									></div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split mb-1"
										id="btnGuardarCambios"
										@click="GuardarFeriado()"
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
	components: { layout, headerClose, headerCloseModal },
	props: {
		feriados: Array,
		motivos: Array,
		agencias: Array,
		acceso_agencias: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: "NUEVO FERIADO",
			feriados_filtrados: this.feriados,
			frmRegistrarFeriados: {
				id: "",
				modo: "NUEVO",
				fecha: "",
				motivo: true,
				FeriadoAgencias: [],
			},
			frmAsignarFeriados: [],
			feriadoEditar: [],
			acceso_agencias_editar: this.acceso_agencias,
		};
	},
	validations: {
		frmRegistrarFeriados: {
			fecha: { required },
			motivo: { required },
		},
	},
	mounted() {
		this.TablaListarFeriados();
	},
	watch: {
		feriados_filtrados() {
			$("#tblFeriados").DataTable().destroy();
			this.TablaListarFeriados();
		},
	},
	methods: {
		FiltrarFeriados() {
			this.feriados_filtrados = this.feriados;
		},
		TablaListarFeriados() {
			let self = this;

			this.$nextTick(() => {
				var table = $("#tblFeriados").DataTable({
					scrollY: "350px",
					scrollX: true,
					ordering: false,
					scrollCollapse: true,
					paging: false,
					fixedHeader: true,
					info: true,

					language: {
						retrieve: true,
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		NuevoFeriado() {
			this.submited = false;
			this.title_modal = "NUEVO FERIADO";
			this.frmRegistrarFeriados.id = 0;
			this.frmRegistrarFeriados.fecha = "";
			this.frmRegistrarFeriados.motivo = "";
			this.frmRegistrarFeriados.modo = "NUEVO";

			document.getElementById("modalRegistrarFeriados").style.display = "block";
			parent.document.getElementById("footer-navigator").style.display = "none";
		},
		EditarFecha(feriado) {
			this.submited = false;
			this.title_modal = "EDITAR FERIADO";
			this.frmRegistrarFeriados.id = feriado.id;
			this.frmRegistrarFeriados.fecha = feriado.fecha;
			this.frmRegistrarFeriados.motivo = feriado.motivo;
			this.frmRegistrarFeriados.modo = "EDITAR";

			document.getElementById("modalRegistrarFeriados").style.display = "block";
			parent.document.getElementById("footer-navigator").style.display = "none";
		},
		EditarAgencias(id_feriado) {
			self = this;
			let lista_agencias_actual = [];
			let lista_agencias_editar = [];
			let index = self.feriadoEditar.indexOf(id_feriado);
			self.feriadoEditar.splice(index, 1);

			self.acceso_agencias.forEach(function (element) {
				if (element["feriado_id"] == id_feriado) {
					lista_agencias_actual.push(element["agencia_id"]);
				}
			});

			self.acceso_agencias_editar.forEach(function (element) {
				if (element["feriado_id"] == id_feriado) {
					lista_agencias_editar.push(element["agencia_id"]);
				}
			});

			if (!_.isEqual(lista_agencias_editar, lista_agencias_actual)) {
				Swal.fire({
					title: "EDITAR FERIADO",
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
							route("gen.man.listar_feriados.editar_agencia"),
							{
								lista_agencias_editar: lista_agencias_editar,
								id: id_feriado,
							},
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
										},
									});
								},
							}
						);
					},
				});
			}
		},

		QuitarFeriado(id) {
			self = this;
			Swal.fire({
				title: "ELIMINAR FERIADO",
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
						route("gen.man.listar_feriados.eliminar_feriado"),
						{ id: id },
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
										self.FiltrarFeriados();
									},
								});
							},
						}
					);
				},
			});
		},
		GuardarFeriado() {
			this.submited = true;
			var self = this;
			if (this.$v.frmRegistrarFeriados.$invalid) {
				return false;
			} else {
				axios
					.post(
						route("gen.man.listar_feriados.verificar_feriado"),
						self.frmRegistrarFeriados
					)
					.then(function (response) {
						if (response.data == "EXISTE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Esta Fecha ya EXISTE, intente con otro.",
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
										route("gen.man.listar_feriados.guardar_feriados"),
										self.frmRegistrarFeriados,
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
														self.FiltrarFeriados();
														//this.feriados_filtrados = this.feriados;
														$("#modalRegistrarFeriados").css("display", "none");
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
.slot-listarFeriados {
	width: 70% !important;
	margin-left: 15% !important;
}
.modalFeriadoCrear {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-listarFeriados {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.modalFeriadoCrear {
		margin-top: 20%;
	}
}
</style>
