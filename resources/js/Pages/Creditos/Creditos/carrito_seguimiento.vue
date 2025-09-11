<template>
	<layout ref="layout">
		<div class="slot_body slot-carrito-seguimiento" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose title="SEGUIMIENTO DE CARRITOS"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10 col-12">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">FECHA</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="fecha"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>

									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
										</div>
										<select
											class="form-control center"
											v-model="agencia_busqueda"
										>
											<option
												v-for="item in agencias_permitidas"
												:key="item.id"
												:value="item.id"
											>
												{{ item.agencia }}
											</option>
										</select>
									</div>
								</div>
							</fieldset>
							<div
								class="col-md-1 text-right"
								style="margin-bottom: 0rem !important"
							>
								<button
									class="btn btn-action btn-icon-split mt-3"
									@click="Buscar"
								>
									<span class="icon text-white">
										<i class="fas fa-search" style="font-size: 25px"> </i
									></span>
								</button>
							</div>
						</div>

						<div class="card-title mt-2 mb-2">LISTA DE RESULTADOS</div>

						<TabView>
							<TabPanel header="CARRITOS">
								<DataTable
									:value="lista_carrito_seguimiento"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="380px"
									selectionMode="single"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
									showGridlines
								>
									<Column
										field="numero"
										header="N°"
										:styles="{ width: '20px', justifyContent: 'center' }"
									>
										<template #body="slotProps">
											{{ slotProps.index + 1 }}
										</template>
									</Column>
									<Column
										field="ver"
										header="VER"
										:styles="{ width: '50px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<button
												class="btn btn-action"
												title="VerCarrito"
												@click="Detalle(data)"
											>
												<span class="icon text-white">
													<i
														class="fas fa-eye"
														style="font-size: 9px !important"
													></i>
												</span>
											</button>
										</template>
									</Column>
									<Column
										field="hora_cierre"
										header="ESTADO"
										:styles="{ width: '70px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<div
												style="
													font-size: 14px !important;
													font-family: Roboto-Bold;
												"
												:class="['p-2', estado_carrito(data)]"
											>
												{{ data.hora_cierre == null ? "ABIERTO" : "CERRADO" }}
											</div>
										</template>
									</Column>
									<Column
										field="usuario"
										header="USUARIO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="total_cobrados"
										header="COBRADOS NEGOCIO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<div
												:style="{
													'font-size': '14px !important',
													'font-weight': 'bolder',
												}"
											>
												{{ data.total_cobrados }}
											</div>
										</template>
									</Column>
									<Column
										field="total_pagados"
										header="PAGADOS CAJA"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<div
												:style="{
													'font-size': '14px !important',
													'font-weight': 'bolder',
												}"
											>
												{{ data.total_pagados }}
											</div>
										</template>
									</Column>
									<Column
										field="hora_apertura"
										header="HORA APERTURA"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<div
												:style="{
													'font-size': '14px !important',
													'font-weight': 'bolder',
												}"
											>
												{{ data.hora_apertura }}
											</div>
										</template>
									</Column>
									<Column
										field="hora_cierre"
										header="HORA CIERRE"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<div
												:style="{
													'font-size': '14px !important',
													'font-weight': 'bolder',
												}"
											>
												{{ data.hora_cierre == null ? "-" : data.hora_cierre }}
											</div>
										</template>
									</Column>
									<template #empty> No hay CARRITOS para mostrar.</template>
								</DataTable></TabPanel
							>
							<template v-if="permiso_vouchers"
								><TabPanel header="VOUCHER NO ENVIADOS">
									<DataTable
										:value="lista_sin_voucher"
										:scrollable="true"
										scrollDirection="both"
										scrollHeight="380px"
										selectionMode="single"
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
											header="VOUCHER"
											:styles="{ width: '60px', justifyContent: 'center' }"
										>
											<template #body="{ data }">
												<button
													class="btn btn-action"
													title="EnvioVoucher"
													@click="EnvioVoucher(data)"
													v-if="!data.envio_voucher"
												>
													<span class="icon text-white">
														<i
															class="fas fa-paper-plane"
															style="font-size: 12px !important"
														></i>
													</span>
												</button>
											</template>
										</Column>
										<Column
											field="modo_envio"
											header="MODO"
											:styles="{ width: '80px', justifyContent: 'center' }"
										>
										</Column>
										<Column header="CARRITO" :styles="{ width: '100px' }">
											<template #body="{ data }">
												<div class="row text-center">
													<div class="col-12">{{ data.usuario_carrito }}</div>
													<div
														class="col-12"
														style="
															font-size: 10px;
															font-family: 'Roboto-BoldItalic';
														"
													>
														{{ data.nombre_agencia_carrito }}
													</div>
												</div>
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
														{{
															data.nombre_agencia_credito +
															" - " +
															data.usuario_asesor
														}}
													</div>
												</div>
											</template>
										</Column>

										<Column
											header="CUOTA"
											:styles="{ width: '90px', justifyContent: 'right' }"
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
											header="MORA"
											:styles="{ width: '90px', justifyContent: 'right' }"
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
											header="NOTIF."
											:styles="{ width: '90px', justifyContent: 'right' }"
										>
											<template #body="{ data }">
												<div class="celda-resaltada">
													{{
														data.pago_notificaciones_monto == 0
															? "-"
															: "S/ " +
															  roundTo(data.pago_notificaciones_monto, 2)
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
											header="FECHA COBRO"
											:styles="{ width: '150px', justifyContent: 'center' }"
										>
											<template #body="{ data }">
												<div class="celda-resaltada">
													{{
														data.fecha_cobro == null ? "-" : data.fecha_cobro
													}}
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
											header="FECHA PAGO"
											:styles="{ width: '150px', justifyContent: 'center' }"
										>
											<template #body="{ data }"
												><div class="celda-resaltada">
													{{ data.fecha_pago == null ? "-" : data.fecha_pago }}
												</div>
											</template>
										</Column>
										<Column
											header="CAJA_PAGO"
											:styles="{ width: '120px', justifyContent: 'center' }"
										>
											<template #body="{ data }"
												><div>
													{{
														data.usuario_caja == null ? "-" : data.usuario_caja
													}}
												</div>
											</template>
										</Column>

										<template #empty> No hay VOUCHERS para mostrar.</template>
									</DataTable>
								</TabPanel></template
							>
						</TabView>
					</div>
				</div>

				<mdlCarritoSeguimiento
					ref="mdlCarritoSeguimiento"
					:agencia_id="agencia_busqueda"
				></mdlCarritoSeguimiento>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import mdlCarritoSeguimiento from "@/Pages/Creditos/Creditos/Components/mdlCarritoSeguimiento.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import TabView from "primevue/tabview/tabview.common";
import TabPanel from "primevue/tabpanel/tabpanel.common";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DatePicker,
		mdlCarritoSeguimiento,

		DataTable,
		Column,
		TabView,
		TabPanel,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			agencia_busqueda: 0,
			agencias_permitidas: [],

			fecha: null,

			lista_carrito_seguimiento: [],
			lista_sin_voucher: [],
		};
	},
	computed: {
		permiso_vouchers() {
			let resultado = false;
			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CREDITO/ENVIAR_VOUCHER"
				);

			if (permiso_detalle.length != 0) {
				resultado = true;
			}
			return resultado;
		},
	},

	watch: {
		async agencia_busqueda() {
			await this.FechaActual();
		},

		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
				} else {
					this.agencia_busqueda = null;
				}
			}
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		async FechaActual() {
			if (this.agencia_busqueda == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_busqueda
				);

				fecha_actual = fecha_actual.substring(0, 10);
				this.fecha = fecha_actual;
			}
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			let resultado = parseFloat(valor).toLocaleString("es-PE", {
				minimumFractionDigits: numero_decimales,
				maximumFractionDigits: numero_decimales,
			});

			return resultado;
		},
		estado_carrito(data) {
			return data.hora_cierre == null ? "abierto" : "cerrado";
		},

		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CREDITO/CARRITO_SEGUIMIENTO"
			);
		},
		async Buscar() {
			const params = {
				fecha: this.fecha,
				agencia_id: this.agencia_busqueda,
			};

			const loadingAlert = Swal.fire({
				title: "CARGANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.get(
					// 	route("cre.carrito_seguimiento.listar_sin_voucher"),
					// 	params
					// );
					// return false;

					Swal.showLoading();
					// Espera a que se resuelva la promesa antes de continuar
					try {
						this.lista_carrito_seguimiento = await this.ListarCarritos(params);
						this.lista_sin_voucher = await this.ListarSinVoucher(params);

						// Verifica si se encontraron carritos
						if (
							!this.lista_carrito_seguimiento ||
							this.lista_carrito_seguimiento.length === 0
						) {
							this.lista_carrito_seguimiento = []; // Aseguramos que la lista sea un array vacío
							Swal.hideLoading();
							return Swal.fire({
								icon: "info",
								title: "¡Ups!",
								text: "No se encontraron carritos aperturados",
								allowOutsideClick: true,
							});
						} else {
							Swal.hideLoading();
							Swal.close(); // Cierra el cuadro de carga
							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								timer: 1200,
								showConfirmButton: false,
							});
						}
					} catch (error) {
						Swal.hideLoading();
						return Swal.fire({
							icon: "error",
							title: "Error",
							text: "Hubo un problema al buscar los carritos",
							allowOutsideClick: true,
						});
					}
				},
			});
		},

		async ListarCarritos(params) {
			return await axios
				.get(route("cre.carrito_seguimiento.buscar"), { params })
				.then((response) => {
					return response.data.lista_carrito_seguimiento;
				})
				.catch((error) => {
					console.error(error);
					throw new Error("Error al listar los carritos");
				});
		},
		async ListarSinVoucher(params) {
			return await axios
				.get(route("cre.carrito_seguimiento.listar_sin_voucher"), { params })
				.then((response) => {
					return response.data.lista_sin_voucher;
				})
				.catch((error) => {
					console.error(error);
					throw new Error("Error al listar los vouchers");
				});
		},

		async Detalle(item) {
			const params = {
				carrito_id: item.id,
				usuario_id: item.usuario_id,
				fecha: item.fecha,
				agencia_carrito: item.agencia_id,
			};

			// this.$inertia.get(route("cre.carrito_seguimiento.detalle"), params);
			// return false;

			await axios
				.get(route("cre.carrito_seguimiento.detalle"), { params })
				.then((response) => {
					const mdlCarritoSeguimiento = this.$refs.mdlCarritoSeguimiento;

					async function EnviarDatos() {
						mdlCarritoSeguimiento.lista_creditos = response.data.lista_creditos;

						mdlCarritoSeguimiento.detalle_item = item;
					}

					EnviarDatos().then(() => {
						$("#mdlCarritoSeguimiento").css("display", "block");
					});
				});
		},
		async EnvioVoucher(item) {
			// Primero ser verifica si el servicio está activo
			let servicio = "";
			let tipo_envio = "";

			if (item.modo_envio == "SMS") {
				servicio = "servicio_sms";
				tipo_envio = item.modo_envio;
			} else if (item.modo_envio == "WHATSAPP") {
				servicio = "servicio_whatsapp";
				tipo_envio = item.modo_envio;
			}

			// this.$inertia.get(
			// 	route("man.servicios.verificar", {
			// 		servicio: servicio,
			// 		agencia_id: item.agencia_id,
			// 	})
			// );
			// return false;

			await axios
				.get(
					route("man.servicios.verificar", {
						servicio: servicio,
						agencia_id: item.agencia_credito,
					})
				)
				.then((response) => {
					let resultado = response.data.resultado;

					if (resultado) {
						Swal.fire({
							title: "¿DESEA ENVIAR ESTE COMPROBANTE?",
							text: "TIPO: " + tipo_envio,
							confirmButtonText: "Si",
							showCancelButton: true,
							cancelButtonText: "No",
							allowOutsideClick: false,

							preConfirm: (result) => {
								Swal.fire({
									title: "CARGANDO",
									text: "Espere porfavor...",
									allowOutsideClick: false,

									didOpen: async () => {
										let data = new FormData();

										data.append("telefono_envio", item.telefono_envio);
										data.append("ticket", item.ticket);
										data.append("modo_envio", item.modo_envio);
										data.append("agencia_credito", item.agencia_credito);
										data.append("agencia_caja", item.agencia_caja);
										data.append("credito_id", item.credito_id);
										data.append("cliente", item.cliente);
										data.append("fecha_cobro", item.fecha_cobro);
										data.append("fecha_pago", item.fecha_pago);
										data.append("voucher_id", item.voucher_id);
										data.append("total_cobro", item.total_cobro);
										data.append("carrito_detalle_id", item.id);

										// this.$inertia.post(
										// 	route("cre.carrito_seguimiento.comprobante"),
										// 	data
										// );
										// return false;

										Swal.showLoading();
										await axios
											.post(route("cre.carrito_seguimiento.comprobante"), data)
											.then(async (response) => {
												const params = {
													fecha: this.fecha,
													agencia_id: this.agencia_busqueda,
												};
												this.lista_sin_voucher = await this.ListarSinVoucher(
													params
												);

												return Swal.fire({
													icon: "success",
													title: "¡ÉXITO!",
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
							},
						});
					} else {
						return Swal.fire({
							icon: "warning",
							title: "¡Ups!",
							text: "El SERVICIO para enviar el COMPROBANTE no está activo.",
						});
					}
				});
		},
	},
};
</script>

<style lang="css">
.slot-carrito-seguimiento {
	width: 64% !important;
	margin-left: 18% !important;
}

.abierto {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.cerrado {
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

@media (max-width: 900px) {
	.slot-carrito-seguimiento {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
	.abierto {
		background-color: var(--verdeOscuroEmpresarial) !important;
		color: white !important;
	}

	.cerrado {
		background-color: var(--azulOscuroEmpresarial) !important;
		color: white !important;
	}
}
</style>
