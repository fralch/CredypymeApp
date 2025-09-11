<template>
	<div>
		<div id="mdlCarritoSeguimiento" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-70 mdlCarritoSeguimiento">
				<div class="content" style="display: block">
					<div class="card">
						<headerCloseModal
							:titulo_modal="'CARRITO ASESOR'"
							:nombre_modal="'mdlCarritoSeguimiento'"
						>
						</headerCloseModal>

						<div class="card-body card-block">
							<div class="card-title mt-2">LISTA DE RESULTADOS</div>
							<div class="input-group m-1 justify-content-md-center">
								<div class="input-group-prepend">
									<label
										style="
											width: 20px !important;
											background-color: LightSkyBlue;
											margin: 0 !important;
										"
									></label>
								</div>
								<div class="input-group-append">
									<label
										class="input-group-text"
										style="background: white !important"
									>
										Crédito ya pagado en caja
									</label>
								</div>
							</div>

							<DataTable
								:value="lista_creditos"
								:scrollable="true"
								scrollDirection="both"
								scrollHeight="380px"
								selectionMode="single"
								currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
								:rowClass="row_class_pagado"
								showGridlines
							>
								<Column
									header="N°"
									:styles="{ width: '30px', justifyContent: 'center' }"
								>
									<template #body="slotProps">
										{{ slotProps.index + 1 }}
									</template>
								</Column>
								<Column
									header="VER"
									:styles="{ width: '50px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										<button
											class="btn btn-action"
											title="VerDetalleCredito"
											@click="VerDetalleCredito(data)"
											v-show="data.observacion != null"
										>
											<span class="icon text-white">
												<i
													class="fas fa-eye"
													style="font-size: 9px !important"
												></i>
											</span>
										</button>

										{{ data.observacion == null ? "-" : "" }}
									</template>
								</Column>
								<Column
									header="OBSERVACIÓN"
									:styles="{ width: '100px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										<button
											class="btn btn-icon-split"
											:class="[
												data.observacion == 'MODIFICADO' ||
												data.observacion == 'PAGADO_MODIFICADO'
													? 'btn-cancel'
													: [data.observacion == 'ANULADO' ? 'btn-danger' : ''],
											]"
											title="VerObservacion"
											@click="VerObservacion(data)"
											v-show="
												data.observacion == 'MODIFICADO' ||
												data.observacion == 'ANULADO' ||
												data.observacion == 'PAGADO_MODIFICADO'
											"
										>
											<span class="icon text-white">
												{{
													data.observacion == "PAGADO_MODIFICADO"
														? "MODIFICADO"
														: data.observacion
												}}
											</span>
										</button>
										{{
											data.observacion == null ||
											data.observacion == "PAGADO" ||
											data.observacion == "COBRADO"
												? "-"
												: ""
										}}
									</template>
								</Column>
								<Column
									header="CLIENTE"
									:styles="{ width: '250px', justifyContent: 'left' }"
								>
									<template #body="{ data }">
										<div class="row">
											<div class="col-12">{{ data.cliente }}</div>
											<div
												class="col-12"
												style="
													font-size: 10px;
													font-family: 'Roboto-BoldItalic';
												"
											>
												{{ data.agencia_credito + " - " + data.usuario_asesor }}
											</div>
										</div>
									</template>
								</Column>

								<Column
									header="COBRO CUOTA"
									:styles="{ width: '100px', justifyContent: 'right' }"
								>
									<template #body="{ data }">
										<div class="celda-resaltada">
											{{
												data.pago_cuota_monto == 0
													? "-"
													: "S/ " + roundTo(data.pago_cuota_monto, 2)
											}}
										</div>
									</template>
								</Column>
								<Column
									header="COBRO MORA"
									:styles="{ width: '100px', justifyContent: 'right' }"
								>
									<template #body="{ data }">
										<div class="celda-resaltada">
											{{
												data.pago_mora_monto == 0
													? "-"
													: "S/ " + roundTo(data.pago_mora_monto, 2)
											}}
										</div>
									</template>
								</Column>
								<Column
									header="COBRO NOTIF."
									:styles="{ width: '100px', justifyContent: 'right' }"
								>
									<template #body="{ data }">
										<div class="celda-resaltada">
											{{
												data.pago_notificaciones_monto == 0
													? "-"
													: "S/ " + roundTo(data.pago_notificaciones_monto, 2)
											}}
										</div></template
									>
								</Column>
								<Column
									header="TOTAL COBRO"
									:styles="{ width: '100px', justifyContent: 'right' }"
								>
									<template #body="{ data }">
										<div class="celda-resaltada">
											{{
												data.total_cobro == 0
													? "-"
													: "S/ " + roundTo(data.total_cobro, 2)
											}}
										</div></template
									>
								</Column>
								<Column
									header="HORA COBRO"
									:styles="{ width: '100px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										<div class="celda-resaltada">
											{{ data.hora_cobro == null ? "-" : data.hora_cobro }}
										</div>
									</template>
								</Column>
								<Column
									header="TOTAL PAGO"
									:styles="{ width: '100px', justifyContent: 'right' }"
								>
									<template #body="{ data }"
										><div class="celda-resaltada">
											{{
												data.total_pago == 0
													? "-"
													: "S/ " + roundTo(data.total_pago, 2)
											}}
										</div>
									</template>
								</Column>
								<Column
									header="HORA PAGO"
									:styles="{ width: '100px', justifyContent: 'center' }"
								>
									<template #body="{ data }"
										><div class="celda-resaltada">
											{{ data.hora_pago == null ? "-" : data.hora_pago }}
										</div>
									</template>
								</Column>
								<Column
									header="CAJA_PAGO"
									:styles="{ width: '120px', justifyContent: 'center' }"
								>
									<template #body="{ data }"
										><div>
											{{ data.usuario_caja == null ? "-" : data.usuario_caja }}
										</div>
									</template>
								</Column>

								<ColumnGroup type="footer">
									<Row>
										<Column
											:colspan="4"
											footer="TOTAL"
											:footerStyle="{
												width: '430px',
												backgroundColor: '#244b9a !important',
												fontSize: '15px !important',
												textAlign: 'right',
											}"
										/>

										<Column
											:colspan="1"
											:footer="'S/ ' + roundTo(total_cuota, 2)"
											:footerStyle="{
												width: '100px',
												fontSize: '15px !important',
												textAlign: 'right',
											}"
										/>
										<Column
											:colspan="1"
											:footer="'S/ ' + roundTo(total_mora, 2)"
											:footerStyle="{
												width: '100px',
												fontSize: '15px !important',
												textAlign: 'right',
											}"
										/>
										<Column
											:colspan="1"
											:footer="'S/ ' + roundTo(total_notificacion, 2)"
											:footerStyle="{
												width: '100px',
												fontSize: '15px !important',
												textAlign: 'right',
											}"
										/>
										<Column
											:colspan="1"
											:footer="'S/ ' + roundTo(total_cobro, 2)"
											:footerStyle="{
												width: '100px',
												fontSize: '15px !important',
												textAlign: 'right',
											}"
										/>
										<Column
											:colspan="1"
											:footer="null"
											:footerStyle="{
												width: '100px',
												backgroundColor: 'transparent !important',
												textAlign: 'right',
											}"
										/>
										<Column
											:colspan="1"
											:footer="'S/ ' + roundTo(total_pago, 2)"
											:footerStyle="{
												width: '100px',
												fontSize: '15px !important',
												textAlign: 'right',
											}"
										/>
										<Column
											:colspan="2"
											:footer="null"
											:footerStyle="{
												width: '220px',
												backgroundColor: 'transparent !important',
												textAlign: 'right',
											}"
										/>
									</Row>
								</ColumnGroup>
							</DataTable>
						</div>
					</div>
					<mdlDetalleCreditoCarrito
						ref="mdlDetalleCreditoCarrito"
					></mdlDetalleCreditoCarrito>
					<mdlModificacionesCarrito
						ref="mdlModificacionesCarrito"
					></mdlModificacionesCarrito>
				</div>
			</div>
		</div>
		<div id="mdlAnulacionCredito" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-25 mdlAnulacionCredito">
				<div class="content" style="display: block">
					<div class="card">
						<headerCloseModal
							:titulo_modal="'ANULACIÓN '"
							:nombre_modal="'mdlAnulacionCredito'"
						></headerCloseModal>

						<div class="card-body card-block">
							<div class="input-group col-md-12">
								<div class="input-group-prepend">
									<label class="input-group-text" for="rdbPorNombre">
										FECHA
									</label>
								</div>
								<input
									type="text"
									class="form-control"
									spellcheck="false"
									:value="lista_datos_anulado.fecha_anulacion"
									readonly
								/>
							</div>

							<div class="form-group col-md-12 mt-1">
								<label class="form-control-label label-title"
									>COMENTARIO:</label
								>

								<textarea
									type="text"
									rows="3"
									class="form-control text-row"
									:value="lista_datos_anulado.motivo_anulacion"
									readonly
								></textarea>
							</div>
							<hr />
							<div class="text-right">
								<button
									class="btn btn-action btn-icon-split"
									@click="CerrarModal"
								>
									<span class="text">ACEPTAR</span>
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import mdlDetalleCreditoCarrito from "@/Pages/Creditos/Creditos/Components/mdlDetalleCreditoCarrito.vue";
import mdlModificacionesCarrito from "@/Pages/Creditos/Creditos/Components/mdlModificacionesCarrito.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
	components: {
		headerCloseModal,
		mdlDetalleCreditoCarrito,
		mdlModificacionesCarrito,

		DataTable,
		Column,
		ColumnGroup,
		Row,
	},
	props: { agencia_id: Number },
	data() {
		return {
			submited: false,
			windowWidth: window.innerWidth,
			lista_creditos: [],
			detalle_item: [],
			lista_datos_anulado: [],
			mostrar: null,
			total_cobro: this.roundTo(0, 2),
			total_pago: this.roundTo(0, 2),
			total_cuota: this.roundTo(0, 2),
			total_mora: this.roundTo(0, 2),
			total_notificacion: this.roundTo(0, 2),
		};
	},

	watch: {
		lista_creditos() {
			this.calcular_total();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		calcular_total() {
			let t_cobro_total = 0;
			let t_pago_total = 0;
			let t_cuota_total = 0;
			let t_mora_total = 0;
			let t_notificacion_total = 0;

			this.lista_creditos.forEach((element) => {
				if (element.total_cobro != null) {
					t_cobro_total += parseFloat(element.total_cobro);
				}
				if (element.total_pago != null) {
					t_pago_total += parseFloat(element.total_pago);
				}
				if (element.pago_cuota_monto != null) {
					t_cuota_total += parseFloat(element.pago_cuota_monto);
				}
				if (element.pago_mora_monto != null) {
					t_mora_total += parseFloat(element.pago_mora_monto);
				}
				if (element.pago_notificaciones_monto != null) {
					t_notificacion_total += parseFloat(element.pago_notificaciones_monto);
				}
			});

			this.total_cobro = t_cobro_total;
			this.total_pago = t_pago_total;
			this.total_cuota = t_cuota_total;
			this.total_mora = t_mora_total;
			this.total_notificacion = t_notificacion_total;
		},

		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
		},

		row_class_pagado(data) {
			return data.observacion == "PAGADO" ||
				data.observacion == "PAGADO_MODIFICADO"
				? "pagado"
				: null;
		},

		async VerDetalleCredito(item) {
			const self = this;

			let credito_id = item.id;

			let carrito_detalle_id = item.carrito_detalle_id;
			if (carrito_detalle_id == null) {
				carrito_detalle_id = 0;
			}

			if (this.windowWidth < 900) {
				this.mostrar = false;
			}
			if (this.windowWidth >= 900) {
				this.mostrar = true;
			}

			const params = {
				agencia_credito: item.agencia_id,
				credito_id: credito_id,
				carrito_detalle_id: carrito_detalle_id,
			};

			// this.$inertia.get(route("cre.carrito.detalle"), params);
			// return false;

			await axios
				.get(route("cre.carrito.detalle"), {
					params,
				})
				.then(function (response) {
					let mdlDetalleCreditoCarrito = self.$refs.mdlDetalleCreditoCarrito;
					async function EnviarDatos() {
						mdlDetalleCreditoCarrito.agencia_id =
							response.data.datos_credito.agencia_id;
						mdlDetalleCreditoCarrito.credito_id = credito_id;
						mdlDetalleCreditoCarrito.datos_credito =
							response.data.datos_credito;
						mdlDetalleCreditoCarrito.datos_cuotas = response.data.datos_cuotas;
						mdlDetalleCreditoCarrito.notificaciones =
							response.data.notificaciones;
						mdlDetalleCreditoCarrito.carrito_detalle =
							response.data.carrito_detalle;
						mdlDetalleCreditoCarrito.mostrar = self.mostrar;

						mdlDetalleCreditoCarrito.nuevo_telefono_principal =
							mdlDetalleCreditoCarrito.telefono_principal =
								response.data.carrito_detalle.telefono_envio;

						mdlDetalleCreditoCarrito.frmDatosCobranza.modo_envio =
							response.data.carrito_detalle.modo_envio;

						mdlDetalleCreditoCarrito.modo = "VER";
						mdlDetalleCreditoCarrito.modo_2 = "padre.padre";
					}
					EnviarDatos().then(() => {
						$("#mdlDetalleCreditoCarrito").css("display", "block");

						mdlDetalleCreditoCarrito.ResetearCampos();
					});
				});
		},
		async VerObservacion(item) {
			const self = this;

			const params = {
				carrito_detalle_id: item.carrito_detalle_id,
				agencia_credito: item.agencia_id,
			};

			if (item.observacion == "ANULADO") {
				// this.$inertia.get(route("cre.carrito_seguimiento.anulacion"), params);
				// return false;

				await axios
					.get(route("cre.carrito_seguimiento.anulacion"), { params })
					.then((response) => {
						async function EnviarDatos() {
							self.lista_datos_anulado = response.data.lista_datos_anulado;
						}
						return EnviarDatos().then(() => {
							$("#mdlAnulacionCredito").css("display", "block");
						});
					});
			} else {
				// this.$inertia.get(
				// 	route("cre.carrito_seguimiento.modificacion"),
				// 	params
				// );
				// return false;

				await axios
					.get(route("cre.carrito_seguimiento.modificacion"), { params })
					.then((response) => {
						let mdlModificacionesCarrito = this.$refs.mdlModificacionesCarrito;
						async function EnviarDatos() {
							mdlModificacionesCarrito.lista_credito_modificado =
								response.data.lista_credito_modificado;
						}
						return EnviarDatos().then(() => {
							$("#mdlModificacionesCarrito").css("display", "block");
						});
					});
			}
		},

		CerrarModal() {
			$("#mdlAnulacionCredito").css("display", "none");
		},
	},
};
</script>

<style lang="css">
.mdlCarritoSeguimiento {
	margin-top: 3%;
}

.mdlAnulacionCredito {
	margin-top: 6%;
}

.celda-resaltada {
	font-size: 14px !important;
	font-weight: bolder;
}

.pagado {
	background-color: rgb(188, 229, 255) !important;
}

@media only screen and (max-width: 900px) {
	.mdlCarritoSeguimiento {
		margin-top: 35%;
	}
}
</style>
