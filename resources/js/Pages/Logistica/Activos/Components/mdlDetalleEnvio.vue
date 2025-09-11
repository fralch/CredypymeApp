<template>
	<div id="mdlDetalleEnvio" class="modal">
		<div class="modal-content w-40 mdlDetalleEnvio">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlDetalleEnvio'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLE</div>
					<div class="card-body card-block">
						<div class="text-left">
							<button
								class="btn btn-action btn-icon-split"
								@click="Seleccionar_Deseleccionar"
								v-if="
									modo == 'NO_VISTA' &&
									lista_activos.filter((item) => item.situacion == 'PENDIENTE')
										.length > 0
								"
							>
								<span class="icon text-white">
									<i
										class="fas fa-check"
										v-if="texto_seleccion == 'SELECCIONAR TODO'"
									></i>
									<i
										class="fas fa-times"
										v-if="texto_seleccion == 'DESELECCIONAR TODO'"
									></i>
								</span>
								<span class="text">{{ texto_seleccion }}</span>
							</button>
							<span
								v-if="
									submited &&
									!$v.frmConfirmacion.canasta_recepcion.required &&
									frmConfirmacion.modo == 'CONFIRMAR'
								"
								class="span-error-message"
							>
								*
							</span>
						</div>

						<table class="table table-hover" id="tblDetalleEnvio" width="100%">
							<thead>
								<tr>
									<th>SITUACIÓN</th>
									<th :hidden="modo == 'VISTA'"></th>
									<th>CÓDIGO_DE_ACTIVO</th>
									<th>DESCRIPCIÓN</th>
									<th>CONDICIÓN</th>
									<th>VALOR_ACTUAL(S/)</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in lista_activos" :key="index">
									<td
										class="table-bordered"
										align="center"
										:class="[
											item.situacion == 'PENDIENTE'
												? 'pendiente'
												: item.situacion == 'CONFIRMADO'
												? 'confirmado'
												: 'rechazado',
										]"
									>
										{{ item.situacion }}
									</td>
									<td
										class="table-bordered"
										align="center"
										:hidden="modo == 'VISTA'"
									>
										<div
											class="align-middle"
											v-if="item.situacion == 'PENDIENTE'"
										>
											<div class="checkbox">
												<label
													class="align-middle"
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													:for="index"
													><input
														type="checkbox"
														:id="index"
														:value="item"
														v-model="frmConfirmacion.canasta_recepcion" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
										</div>
									</td>
									<td class="table-bordered">
										{{ item.codigo }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.descripcion }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.condicion }}
									</td>
									<td class="table-bordered" align="center">
										S/ {{ roundTo(item.valor_actual, 2) }}
									</td>
								</tr>
							</tbody>
						</table>
						<br />
						<div class="form-row">
							<div class="form-group col-md-6">
								<label class="form-control-label label-title"
									>DOC. ENVÍO:</label
								>

								<button
									class="btn btn-action btn-icon-split"
									title="Descargar DOCUMENTO DE ENVÍO"
									@click="Descargar('documento_envio')"
								>
									<span class="icon text-white">
										<i class="fas fa-download"></i>
									</span>
									<span class="text">DESCARGAR</span>
								</button>
							</div>
							<div class="form-group col-md-6">
								<label class="form-control-label label-title"
									>DOC. RECEPCIÓN:</label
								>
								<span
									v-if="
										submited &&
										frmConfirmacion.modo == 'CONFIRMAR' &&
										!$v.frmConfirmacion.documento_recepcion.required
									"
									class="span-error-message"
								>
									*
								</span>
								<input
									class="btn btn-primary"
									style="
										background-color: var(--plomoOscuroEmpresarial);
										border: none;
										font-size: 12px;
										max-width: 270px;
									"
									type="file"
									id="documento_recepcion"
									@change="AgregarComprobante"
									v-if="modo == 'NO_VISTA'"
								/>
								<button
									class="btn btn-action btn-icon-split"
									title="Descargar DOCUMENTO DE RECEPCIÓN"
									@click="Descargar('documento_recepcion')"
									v-if="modo == 'VISTA'"
									:disabled="frmConfirmacion.documento_recepcion == null"
								>
									<span class="icon text-white">
										<i class="fas fa-download"></i>
									</span>
									<span class="text">DESCARGAR</span>
								</button>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="Guardar('CONFIRMAR')"
								:disabled="
									frmConfirmacion.canasta_recepcion.length == 0 &&
									modo != 'VISTA'
								"
							>
								<span class="icon text-white">
									<i class="fas fa-check"></i>
								</span>
								<span class="text">{{
									modo == "NO_VISTA" ? "CONFIRMAR" : "ACEPTAR"
								}}</span>
							</button>
							<button
								class="btn btn-cancel btn-icon-split"
								@click="Guardar('RECHAZAR')"
								v-if="
									modo == 'NO_VISTA' &&
									lista_activos.filter((item) => item.situacion == 'PENDIENTE')
										.length > 0
								"
								:disabled="frmConfirmacion.canasta_recepcion.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
								<span class="text">RECHAZAR</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
export default {
	components: {
		headerCloseModal,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			lista_activos: [],
			modo: null,
			texto_seleccion: "SELECCIONAR TODO",
			frmConfirmacion: {
				modo: "CONFIRMAR",
				envio_id: null,
				canasta_recepcion: [],
				agencia_recepcion: null,
				responsable_recepcion: null,
				ubicacion_recepcion: null,
				documento_envio: null,
				documento_recepcion: null,
			},
		};
	},
	validations() {
		if (this.frmConfirmacion.modo == "CONFIRMAR") {
			return {
				frmConfirmacion: {
					canasta_recepcion: { required },
					documento_recepcion: { required },
				},
			};
		} else if (this.frmConfirmacion.modo == "RECHAZAR") {
			return {
				frmConfirmacion: {
					canasta_recepcion: { required },
				},
			};
		}
	},
	watch: {
		lista_activos() {
			$("#tblDetalleEnvio").DataTable().destroy();
			this.TablaDetalleEnvio();
		},
	},
	mounted() {
		this.TablaDetalleEnvio();
	},
	methods: {
		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				let power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},
		TablaDetalleEnvio() {
			this.$nextTick(() => {
				var table = $("#tblDetalleEnvio").DataTable({
					scrollY: "200px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					order: [],
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
				});
			});
		},

		Descargar(documento) {
			let nombre_archivo = null;
			if (documento == "documento_envio") {
				nombre_archivo = this.frmConfirmacion.documento_envio;
			} else if (documento == "documento_recepcion") {
				nombre_archivo = this.frmConfirmacion.documento_recepcion;
			}

			let source =
				"/imagenes_server/logistica/activos/envios_recepciones/" +
				nombre_archivo;
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = nombre_archivo;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},
		AgregarComprobante(e) {
			this.frmConfirmacion.documento_recepcion = e.target.files[0];
		},

		Seleccionar_Deseleccionar() {
			if (this.texto_seleccion == "SELECCIONAR TODO") {
				this.frmConfirmacion.canasta_recepcion = this.lista_activos.filter(
					(item) => item.situacion == "PENDIENTE"
				);
				this.texto_seleccion = "DESELECCIONAR TODO";
			} else {
				this.frmConfirmacion.canasta_recepcion = [];
				this.texto_seleccion = "SELECCIONAR TODO";
			}
		},

		Guardar(modo) {
			let self = this;

			this.frmConfirmacion.modo = modo;

			if (this.modo == "VISTA") {
				$("#mdlDetalleEnvio").css("display", "none");
			} else if (this.modo == "NO_VISTA") {
				this.submited = true;
				if (this.$v.frmConfirmacion.$invalid) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Hay uno o más campos que faltan completar, verifique.",
					});

					return false;
				} else {
					Swal.fire({
						icon: "question",
						text: "¿DESEA CONTINUAR?",
						confirmButtonText:
							'<i class="fas fa-check" style="color:white;"></i>   Si',
						confirmButtonColor: "var(--colorAlto)",
						showCancelButton: true,
						cancelButtonText: '<i class="fas fa-times"></i>   No',
						cancelButtonColor: "var(--plomoOscuroEmpresarial)",
						allowOutsideClick: false,
					}).then((result) => {
						if (result.isConfirmed) {
							let data = new FormData();

							data.append("modo", self.frmConfirmacion.modo);
							data.append("envio_id", self.frmConfirmacion.envio_id);
							data.append(
								"agencia_recepcion",
								self.frmConfirmacion.agencia_recepcion
							);
							data.append(
								"responsable_recepcion",
								self.frmConfirmacion.responsable_recepcion
							);
							data.append(
								"ubicacion_recepcion",
								self.frmConfirmacion.ubicacion_recepcion
							);

							let lista_recepcion = [];
							self.frmConfirmacion.canasta_recepcion.forEach((element) => {
								let obj = {
									id: element.id,
									activo_id: element.activo_id,
									nombre: element.nombre,
									abreviacion_tipo: element.abreviacion_tipo,
								};
								lista_recepcion.push(obj);
							});

							data.append("canasta_recepcion", JSON.stringify(lista_recepcion));

							data.append(
								"documento_recepcion",
								self.frmConfirmacion.documento_recepcion
							);

							self.$inertia.post(
								route("log.act.envios_recepciones.confirmar"),
								data,
								{
									preserveScroll: true,
									onStart: (visit) => {
										let timerInterval;
										Swal.fire({
											title: "TRABAJANDO",
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
												$("#mdlDetalleEnvio").css("display", "none");
												self.frmConfirmacion.canasta_recepcion = [];
												self.$parent.$parent.Buscar();
											},
										});
									},
								}
							);
						} else {
							return false;
						}
					});
				}
			}
		},
	},
};
</script>

<style lang="css">
.confirmado {
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

.pendiente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	background-color: var(--red) !important;
	color: white !important;
}

.mdlDetalleEnvio {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlDetalleEnvio {
		margin-top: 20%;
	}
}
</style>

