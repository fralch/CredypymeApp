<template>
	<div>
		<div id="mdlDetalleEnvio" class="modal">
			<div class="modal-content w-50 mdlDetalleEnvio">
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
										lista_suministros.filter(
											(item) => item.situacion == 'PENDIENTE'
										).length > 0
									"
								>
									<span class="icon text-white">
										<i
											class="fas fa-check"
											v-if="texto_seleccion == 'SELECCIONAR TODO'"
										></i>
										<i
											class="fas fa-times"
											v-if="texto_seleccion == 'BORRAR SELECCIÓN'"
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

							<DataTable
								:value="lista_suministros"
								:scrollable="true"
								scrollDirection="both"
								:scrollHeight="String(windowHeigth * 0.4) + 'px'"
								showGridlines
							>
								<!-- <Column
									field="situacion"
									header="SITUACIÓN"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
										<div
											:class="[
												data.situacion == 'PENDIENTE'
													? 'pendiente'
													: data.situacion == 'CONFIRMADO'
													? 'confirmado'
													: 'rechazado',
											]"
										>
											{{ data.situacion }}
										</div>
									</template>
								</Column> -->
								<Column
									header="SITUACIÓN"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data, index }">
										<div class="align-middle">
											<div class="checkbox">
												<label
													class="align-middle"
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													:for="index"
													v-if="data.situacion == 'PENDIENTE'"
													><input
														type="checkbox"
														:id="index"
														:value="data"
														v-model="frmConfirmacion.canasta_recepcion" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>

												<span
													class="ml-2"
													:class="[
														data.situacion == 'PENDIENTE'
															? 'pendiente'
															: data.situacion == 'CONFIRMADO'
															? 'confirmado'
															: 'rechazado',
													]"
													>{{ data.situacion }}</span
												>
											</div>
										</div>
									</template>
								</Column>
								<Column
									field="cantidad"
									header="CANTIDAD"
									:styles="{
										width: '70px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">{{
										parseFloat(data.cantidad).toFixed(2)
									}}</template>
								</Column>
								<Column
									field="suministro"
									header="SUMINISTRO"
									:styles="{
										width: '170px',
										justifyContent: 'left',
									}"
								>
								</Column>
								<Column
									field="tipo"
									header="TIPO"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								>
								</Column>
								<Column
									field="condicion"
									header="CONDICIÓN"
									:styles="{
										width: '80px',
										justifyContent: 'center',
									}"
								>
								</Column>
								<Column
									field="valor_unitario"
									header="VALOR_UN."
									:styles="{
										width: '100px',
										justifyContent: 'right',
									}"
								>
									<template #body="{ data }"
										>S/ {{ roundTo(data.valor_unitario, 2) }}</template
									>
								</Column>
							</DataTable>

							<br />
							<div class="form-row">
								<div class="form-group col-md-6">
									<label class="form-control-label label-title"
										>DOC. ENVÍO:</label
									>

									<button
										class="btn btn-action btn-icon-split"
										title="Ver DOCUMENTO DE ENVÍO"
										@click="VerDocumento('envio')"
									>
										<span class="icon text-white">
											<i class="fas fa-eye"></i>
										</span>
										<span class="text">VER</span>
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
										accept="image/*"
										id="documento_recepcion"
										@change="AgregarComprobante"
										v-if="modo == 'NO_VISTA'"
									/>
									<button
										class="btn btn-action btn-icon-split"
										title="Ver DOCUMENTO DE RECEPCIÓN"
										@click="VerDocumento('recepcion')"
										v-if="modo == 'VISTA'"
										:disabled="frmConfirmacion.documento_recepcion == null"
									>
										<span class="icon text-white">
											<i class="fas fa-eye"></i>
										</span>
										<span class="text">VER</span>
									</button>
								</div>
							</div>
							<hr />
							<div class="text-right">
								<div class="btn-group" role="group">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar('CONFIRMAR')"
										:disabled="
											frmConfirmacion.canasta_recepcion.length == 0 &&
											modo != 'VISTA'
										"
										v-if="modo == 'NO_VISTA'"
									>
										<span class="icon text-white">
											<i class="fas fa-check"></i>
										</span>
										<span class="text">CONFIRMAR</span>
									</button>

									<button
										class="btn btn-danger btn-icon-split"
										@click="Guardar('RECHAZAR')"
										v-if="
											modo == 'NO_VISTA' &&
											lista_suministros.filter(
												(item) => item.situacion == 'PENDIENTE'
											).length > 0
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
		</div>
		<div id="mdlDocumento" class="modal">
			<div class="modal-content w-40 mdlDocumento">
				<div class="content" style="display: block">
					<div class="card">
						<headerCloseModal
							ref="headerCloseModal"
							:titulo_modal="'DOCUMENTO'"
							:nombre_modal="'mdlDocumento'"
						>
						</headerCloseModal>
						<div class="card-body card-block">
							<div class="p-2">
								<div
									style="
										width: 100% !important;
										height: 300px !important;
										box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
									"
									v-if="documento_seleccionado != null"
								>
									<img
										:src="
											'/imagenes_server/logistica/suministros/envios_recepciones/' +
											documento_seleccionado
										"
										alt="ruta"
										width="100%"
										height="300px"
									/>
								</div>
							</div>

							<hr />
							<div class="text-center">
								<button
									class="btn btn-cancel btn-icon-split"
									@click="Descargar(documento_seleccionado)"
								>
									<span class="icon text-white">
										<i class="fas fa-download"></i>
									</span>
									<span class="text">DESCARGAR</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import { required } from "vuelidate/lib/validators";
export default {
	props: { windowHeigth: Number, windowWidth: Number },
	components: {
		headerCloseModal,

		DataTable,
		Column,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			lista_suministros: [],
			modo: null,
			texto_seleccion: "SELECCIONAR TODO",
			documento_seleccionado: null,
			frmConfirmacion: {
				modo: "CONFIRMAR",
				envio_id: null,
				canasta_recepcion: [],
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

	methods: {
		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				let power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},

		async Descargar(documento) {
			let source =
				"/imagenes_server/logistica/suministros/envios_recepciones/" +
				documento;
			await axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = documento;
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
				this.frmConfirmacion.canasta_recepcion = this.lista_suministros.filter(
					(item) => item.situacion == "PENDIENTE"
				);
				this.texto_seleccion = "BORRAR SELECCIÓN";
			} else {
				this.frmConfirmacion.canasta_recepcion = [];
				this.texto_seleccion = "SELECCIONAR TODO";
			}
		},

		async Guardar(modo) {
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
				}

				Swal.fire({
					icon: "question",
					text: "¿DESEA CONTINUAR?",
					confirmButtonText: "Si",
					showCancelButton: true,
					cancelButtonText: "No",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						let data = new FormData();

						data.append("modo", this.frmConfirmacion.modo);
						data.append("envio_id", this.frmConfirmacion.envio_id);
						data.append(
							"canasta_recepcion",
							JSON.stringify(this.frmConfirmacion.canasta_recepcion)
						);
						data.append(
							"documento_recepcion",
							this.frmConfirmacion.documento_recepcion
						);

						// this.$inertia.post(
						// 	route("log.sum.envios_recepciones.confirmar"),
						// 	data
						// );
						// return false;

						Swal.fire({
							title: "REGISTRANDO",
							showConfirmButton: false,
							allowOutsideClick: false,
							willOpen: async () => {
								Swal.showLoading();

								return await axios
									.post(route("log.sum.envios_recepciones.confirmar"), data)
									.then((response) => {
										this.frmConfirmacion.canasta_recepcion = [];
										$("#mdlDetalleEnvio").css("display", "none");
										this.$parent.Buscar();

										return Swal.fire({
											icon: "success",
											title: response.data.message,
											timer: 1200,
											showConfirmButton: false,
										});
									})
									.catch((error) => {
										Swal.showValidationMessage(
											`Ha ocurrido un error, comunicar a TI: ${error}`
										);
									});
							},
						});
					} else {
						return false;
					}
				});
			}
		},

		VerDocumento(tipo) {
			if (tipo == "envio") {
				this.documento_seleccionado = this.frmConfirmacion.documento_envio;
			} else if (tipo == "recepcion") {
				this.documento_seleccionado = this.frmConfirmacion.documento_recepcion;
			}

			if (this.documento_seleccionado == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Este DOCUMENTO no existe",
				});
				return false;
			}
			$("#mdlDocumento").css("display", "block");
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

.custom-control {
	cursor: pointer !important;
}

@media (max-width: 900px) {
	.mdlDetalleEnvio {
		margin-top: 20%;
	}
}
</style>
