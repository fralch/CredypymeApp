<template>
	<layout ref="layout">
		<div class="slot_body slot-carrito-cobranza" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose title="CARRITO DE COBRANZA"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-4 col-8">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">FECHA</span>
								</div>
								<input
									type="date"
									class="form-control center bolder"
									v-model="fecha_carrito"
									:style="
										windowWidth >= 900
											? 'font-size: 15px !important'
											: 'font-size: 13px !important'
									"
									disabled
								/>
							</div>

							<div
								class="form-group col-md-1 col-4"
								v-if="mi_carrito == null"
								style="margin-bottom: 0rem !important"
							>
								<button
									class="btn btn-action btn-icon-split"
									title="Aperturar"
									@click="Aperturar"
								>
									<span class="text">APERTURAR</span>
								</button>
							</div>

							<div
								class="form-group col-md-2 col-2"
								v-if="mi_carrito != null"
								style="margin-bottom: 0rem !important"
							>
								<button class="btn btn-action btn-icon-split" @click="Listar">
									<span class="icon text-white">
										<i
											class="fas fa-sync-alt"
											:style="
												windowWidth < 900 ? 'font-size: 16px !important' : ''
											"
										>
										</i
									></span>
									<span class="text" v-if="windowWidth >= 900">ACTUALIZAR</span>
								</button>
							</div>

							<div
								class="form-group col-md-3 col-7"
								style="
									margin-bottom: 0rem !important;
									margin-left: 1rem !important;
								"
								:style="
									windowWidth >= 900
										? 'margin-top: 0px !important'
										: ' margin: auto; display: flex; flex-direction: column; justify-content: center;margin-top: 5px !important'
								"
								v-if="mi_carrito != null"
							>
								<button
									class="btn btn-cancel btn-icon-split"
									title="AgregarCredito"
									@click="AgregarCredito"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">AGREGAR CRÉDITO</span>
								</button>
							</div>
						</div>

						<div class="card-title mt-2 mb-2">LISTA DE RESULTADOS</div>

						<DataTable
							:value="lista_creditos"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							selectionMode="single"
							currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
							:rowClass="row_class_externo"
							showGridlines
						>
							<Column
								field="numero"
								header="N°"
								:styles="{ width: '40px', justifyContent: 'center' }"
							>
								<template #body="slotProps">
									{{ slotProps.index + 1 }}
								</template>
							</Column>
							<Column
								field="accion"
								header="ACCIÓN"
								:styles="{ width: '50px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn1"
										title="COBRAR CRÉDITO"
										@click="DetalleCredito('COBRAR', data)"
										v-if="data.total_cobro == 0"
									>
										<span class="icon text-white">
											<i
												class="fas fa-dollar-sign"
												style="font-size: 15px !important"
											></i>
										</span>
									</button>

									<button
										class="btn btn2"
										title="VER COBRANZA"
										@click="DetalleCredito('VER', data)"
										v-if="data.total_cobro != 0"
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
								field="total_cobro"
								header="TOTAL_COBRO"
								:styles="{
									width: '100px',
									justifyContent: 'right',
								}"
							>
								<template #body="{ data }">
									<div class="celda-resaltada">
										{{
											data.total_cobro == 0
												? "-"
												: "S/ " + roundTo(data.total_cobro, 2)
										}}
									</div>
								</template>
							</Column>
							<Column header="CLIENTE" :styles="{ width: '250px' }">
								<template #body="{ data }">
									<div class="row">
										<div class="col-12">{{ data.cliente }}</div>
										<div
											class="col-12"
											style="font-size: 10px; font-family: 'Roboto-BoldItalic'"
										>
											{{ data.agencia + " - " + data.asesor }}
										</div>
									</div>
								</template>
							</Column>
							<Column
								field="dias_atraso"
								header="ATRASO"
								:styles="{ width: '80px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.dias_atraso + " d" }}
								</template>
							</Column>

							<Column
								field="numero_cuota"
								header="INF_CUOTA"
								:styles="{ width: '80px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.numero_cuota == null ? "-" : data.numero_cuota }}
								</template>
							</Column>
							<Column
								field="cuota"
								header="SALDO_CUOTA"
								:styles="{ width: '100px', justifyContent: 'right' }"
							>
								<template #body="{ data }">
									{{
										data.saldo_cuota == 0
											? "-"
											: "S/ " + roundTo(data.saldo_cuota, 2)
									}}
								</template>
							</Column>
							<Column
								field="mora"
								header="SALDO_MORA"
								:styles="{ width: '100px', justifyContent: 'right' }"
							>
								<template #body="{ data }">{{
									data.saldo_mora == 0
										? "-"
										: "S/ " + roundTo(data.saldo_mora, 2)
								}}</template>
							</Column>
							<Column
								field="notificacion"
								header="SALDO_NOTIF."
								:styles="{ width: '100px', justifyContent: 'right' }"
							>
								<template #body="{ data }">{{
									data.saldo_notificaciones == 0
										? "-"
										: "S/ " + roundTo(data.saldo_notificaciones, 2)
								}}</template>
							</Column>
							<Column
								field="anular"
								header="ANULAR"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn-danger btn-icon-split"
										title="ANULAR cobranza"
										@click="Anular(data)"
										v-if="data.total_cobro != 0"
									>
										<span class="icon text-white">
											<i
												class="fa fa-trash"
												style="font-size: 9px !important"
											></i>
										</span>
									</button>
								</template>
							</Column>

							<ColumnGroup type="footer">
								<Row>
									<Column
										:colspan="2"
										footer="TOTAL"
										:footerStyle="{
											width: '90px',
											backgroundColor: '#244b9a !important',
											fontSize: '15px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'S/ ' + roundTo(total_carrito, 2)"
										:footerStyle="{
											width: '100px',
											fontSize: '15px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="8"
										:footer="null"
										:footerStyle="{
											width: '780px',
											backgroundColor: 'transparent !important',
											textAlign: 'right',
										}"
									/>
								</Row>
							</ColumnGroup>

							<template #empty> No hay créditos en el CARRITO.</template>
						</DataTable>

						<hr />
						<div
							class="text-right"
							v-if="this.mi_carrito != null && this.total_carrito == 0"
							style="margin-bottom: 0rem !important"
						>
							<button class="btn btn-danger btn-icon-split" @click="Cerrar">
								<span class="icon text-white">
									<i class="fa fa-trash" style="font-size: 9px !important"> </i
								></span>
								<span class="text">CERRAR</span>
							</button>
						</div>
					</div>
				</div>

				<div id="mdlAgregarCredito" class="modal">
					<!-- Modal content -->
					<div class="modal-content w-60 mdlAgregarCredito">
						<div class="content" style="display: block">
							<div class="card">
								<headerCloseModal
									:titulo_modal="'AGREGAR CRÉDITO'"
									:nombre_modal="'mdlAgregarCredito'"
								></headerCloseModal>
								<div class="card-body card-block">
									<div class="form-row row justify-content-md-center">
										<div class="form-group col-md-2 col-6">
											<div class="form-check">
												<input
													class="form-check-input"
													type="radio"
													name="listado_agregar_credito"
													id="rdbApellidosNombresAgregarCredito"
													value="apellidos_nombres"
													v-model="tipo_filtro"
												/>
												<label
													class="label-title"
													for="rdbApellidosNombresAgregarCredito"
													>APELLIDOS_NOMBRES
												</label>
											</div>
										</div>
										<div class="form-group col-md-2 col-6">
											<div class="form-check">
												<input
													class="form-check-input"
													type="radio"
													name="listado_agregar_credito"
													id="rdbDniAgregarCredito"
													value="dni"
													v-model="tipo_filtro"
												/>
												<label class="label-title" for="rdbDniAgregarCredito"
													>DNI</label
												>
											</div>
										</div>
									</div>

									<div class="form-row row mb-2">
										<div class="col-md-6 col-12">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"
														><i class="fas fa-search"></i
													></span>
												</div>
												<input
													class="form-control mayus"
													type="text"
													placeholder="Ingrese 3 caractéres como mínimo..."
													autocomplete="off"
													@focus="hidenav()"
													@blur="shownav()"
													ref="buscar_credito"
													v-model="texto_buscar"
												/>
											</div>
										</div>
										<div class="col-md-4 col-12">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">AGENCIA</span>
												</div>
												<select
													class="form-control center"
													v-model="agencia_seleccionada"
													@change="BuscarCreditos"
												>
													<option
														v-for="(item, index) in agencias_permitidas"
														:key="index"
														:value="item.id"
														:selected="index === 0"
													>
														{{ item.agencia }}
													</option>
												</select>
											</div>
										</div>
										<div class="input-group col-md-2 col-6">
											<div class="input-group-prepend">
												<div class="input-group-text">
													<input
														type="checkbox"
														id="chbCanceladosParcial"
														v-model="cancelados_parcial"
													/>
												</div>
											</div>
											<div class="input-group-append">
												<label
													class="input-group-text prepend-title"
													for="chbCanceladosParcial"
													style="font-size: 13px"
												>
													PARCIALES
												</label>
											</div>
										</div>
									</div>

									<DataTable
										:value="lista_agregar_creditos"
										:scrollable="true"
										scrollDirection="both"
										scrollHeight="200px"
										selectionMode="single"
										showGridlines
										:rowClass="row_class_interlineado"
										@row-dblclick="Agregar"
									>
										<Column
											field="numero"
											header="N°"
											:styles="{ width: '40px', justifyContent: 'center' }"
										>
											<template #body="slotProps">
												{{ slotProps.index + 1 }}
											</template>
										</Column>

										<Column
											field="cliente"
											header="CLIENTE"
											:styles="{ width: '300px', justifyContent: 'left' }"
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
														{{ data.agencia + " - " + data.asesor }}
													</div>
												</div>
											</template>
										</Column>

										<Column
											field="capital_total"
											header="CAPITAL"
											:styles="{ width: '100px', justifyContent: 'right' }"
										>
											<template #body="{ data }">{{
												"S/ " + data.capital_total
											}}</template>
										</Column>
										<Column
											field="plazo_periodo"
											header="PLAZO"
											:styles="{ width: '100px', justifyContent: 'center' }"
										>
										</Column>
										<Column
											field="cuota_actual"
											header="N°_CUOTA"
											:styles="{ width: '70px', justifyContent: 'center' }"
										>
										</Column>
										<Column
											field="tipo"
											header="TIPO"
											:styles="{ width: '150px', justifyContent: 'center' }"
										>
										</Column>
										<Column
											field="dias_atraso"
											header="ATRASO"
											:styles="{ width: '80px', justifyContent: 'center' }"
										>
										</Column>
										<template #empty> No hay resultados encontrados.</template>
									</DataTable>
								</div>
							</div>
						</div>
					</div>
				</div>

				<mdlDetalleCreditoCarrito
					ref="mdlDetalleCreditoCarrito"
				></mdlDetalleCreditoCarrito>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import mdlDetalleCreditoCarrito from "@/Pages/Creditos/Creditos/Components/mdlDetalleCreditoCarrito.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		mdlDetalleCreditoCarrito,
		DatePicker,

		DataTable,
		Column,
		ColumnGroup,
		Row,
	},
	props: {
		fecha_carrito: String,
		usuario_id: Number,
		agencia_id: Number,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			lista_creditos: [],
			lista_agregar_creditos: [],
			tipo_filtro: "apellidos_nombres",
			texto_buscar: null,

			creditos_cobrados: 0,

			agencia_seleccionada: null,
			cancelados_parcial: false,
			agencias_permitidas: [],
		};
	},
	computed: {
		mi_carrito() {
			return this.$inertia.page.props.creditos_datos.datos_carrito;
		},

		total_carrito() {
			let total = 0;

			this.lista_creditos.forEach((element) => {
				if (element.total_cobro != 0) {
					total += parseFloat(element.total_cobro);
				}
			});

			return total;
		},
	},
	watch: {
		texto_buscar() {
			this.BuscarCreditos();
		},

		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
				} else {
					this.agencia_seleccionada = null;
				}
			}
		},
	},

	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});

		this.listar_agencias();
	},

	created() {
		if (this.mi_carrito != null) {
			this.Listar();
		}
	},

	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CREDITO/CARRITO"
			);
		},

		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},

		AgregarCredito() {
			this.texto_buscar = "";
			this.tipo_filtro = "apellidos_nombres";
			this.agencia_seleccionada =
				this.$inertia.page.props.user_session.id_agencia;
			this.cancelados_parcial = false;

			$("#mdlAgregarCredito").css("display", "block");
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

		row_class_externo(data) {
			return data.asesor_id != this.usuario_id ? "class_externo" : null;
		},

		row_class_interlineado(data) {
			return data.index % 2 == 0 ? "verde-claro" : null;
		},
		async Listar() {
			const params = {
				fecha_carrito: this.fecha_carrito,
				usuario_id: this.usuario_id,
				agencia_carrito: this.mi_carrito.agencia_id,
				carrito_id: this.mi_carrito.id,
			};

			const loadingAlert = Swal.fire({
				title: "CARGANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.get(route("cre.carrito.listar"), params);
					// return false;

					Swal.showLoading();

					await axios
						.get(route("cre.carrito.listar"), { params })
						.then((response) => {
							if (response.data.lista_creditos.length == 0) {
								this.lista_creditos = [];
								this.creditos_cobrados = 0;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No hay créditos en este carrito",
									allowOutsideClick: true,
								});
							} else {
								this.lista_creditos = response.data.lista_creditos;
								loadingAlert.close();
							}
						});
				},
			});
		},

		Aperturar() {
			Swal.fire({
				title: "¿DESEA INICIAR CON LA COBRANZA?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("usuario_id", this.usuario_id);
					data.append("agencia_id", this.agencia_id);

					//  this.$inertia.post(route("cre.carrito.aperturar"), data);
					//   return false

					return axios
						.post(route("cre.carrito.aperturar"), data)
						.then((response) => {
							Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								text: response.data.message,
								timer: 2000,
								showConfirmButton: false,
							});
							return this.$inertia.get(route("cre.carrito"));
						})
						.catch((error) => {
							console.log(error);
							if (error.response.status === 422) {
								Swal.fire({
									icon: "warning",
									title: "¡Ups!",
									text: error.response.data.message,
									allowOutsideClick: false,
								}).then((result) => {
									if (result.isConfirmed) {
										return this.$inertia.get(route("cre.carrito"));
									}
								});
							} else {
								Swal.fire({
									icon: "error",
									title: "HA OCURRIDO UN ERROR",
									text: "NO realizar ninguna acción y comunicar a TI.",
									showConfirmButton: false,
									allowOutsideClick: false,
								});
							}
						});
				} else {
					return false;
				}
			});
		},

		async Agregar(event) {
			let params = {
				agencia_id: event.data.agencia_id,
				credito_id: event.data.id,
			};

			await axios
				.get(route("cre.carrito.agregar"), { params })
				.then((response) => {
					let credito_id = response.data.credito.id;

					let existe = this.lista_creditos.some(
						(item) => item.id == credito_id
					);

					if (!existe) {
						this.lista_creditos.push(response.data.credito);
						$("#mdlAgregarCredito").css("display", "none");
						return Swal.fire({
							icon: "success",
							title: "¡CRÉDITO AGREGADO!",
							timer: 1500,
							showConfirmButton: false,
						});
					} else {
						$("#mdlAgregarCredito").css("display", "none");
						return Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Este Crédito ya se encuentra en su carrito, intente con otro.",
						});
					}
				});
		},

		async BuscarCreditos() {
			if (this.texto_buscar && this.texto_buscar.length >= 3) {
				const params = {
					texto_buscar: this.texto_buscar,
					tipo_filtro: this.tipo_filtro,
					agencia_id: this.agencia_seleccionada,
					cancelados_parcial: this.cancelados_parcial,
				};

				// this.$inertia.get(route("cre.carrito.buscar"), params);
				// return false;

				await axios
					.get(route("cre.carrito.buscar"), { params })
					.then((response) => {
						return (this.lista_agregar_creditos = response.data.creditos);
					});
			} else {
				return (this.lista_agregar_creditos = []);
			}
		},

		async DetalleCredito(modo, item) {
			let params = {
				agencia_carrito: this.agencia_id,
				agencia_credito: item.agencia_id,
				credito_id: item.id,
				carrito_id: this.mi_carrito.id,
			};

			// this.$inertia.get(route("cre.carrito.verificar"), params);
			// return false;

			await axios
				.get(route("cre.carrito.verificar"), { params })
				.then(async (response) => {
					let resultado = response.data;

					if (resultado == "CARRITO_CERRADO") {
						Swal.fire({
							icon: "warning",
							title: "¡Ups!",
							text: "Este CARRITO ya está CERRADO.",
							allowOutsideClick: false,
						}).then((result) => {
							if (result.isConfirmed) {
								return this.$inertia.get(route("cre.carrito"));
							}
						});
					} else if (resultado == "PENDIENTE_PAGO") {
						Swal.fire({
							icon: "warning",
							title: "¡Ups!",
							text: "Este crédito tiene una cobranza PENDIENTE de pago en CAJA.",
							allowOutsideClick: false,
						}).then((result) => {
							if (result.isConfirmed) {
								return this.$inertia.get(route("cre.carrito"));
							}
						});
					} else {
						params.carrito_detalle_id = item.carrito_detalle_id;

						// this.$inertia.get(route("cre.carrito.detalle"), params);
						//return false;

						await axios
							.get(route("cre.carrito.detalle"), { params })
							.then((response) => {
								const mdlDetalleCreditoCarrito =
									this.$refs.mdlDetalleCreditoCarrito;

								async function EnviarDatos() {
									mdlDetalleCreditoCarrito.editar_numero = false;

									mdlDetalleCreditoCarrito.credito_id =
										response.data.datos_credito.agencia_id;
									mdlDetalleCreditoCarrito.credito_id = item.id;
									mdlDetalleCreditoCarrito.datos_credito =
										response.data.datos_credito;
									mdlDetalleCreditoCarrito.datos_cuotas =
										response.data.datos_cuotas;
									mdlDetalleCreditoCarrito.notificaciones =
										response.data.notificaciones;
									mdlDetalleCreditoCarrito.carrito_detalle =
										response.data.carrito_detalle;

									if (response.data.carrito_detalle == null) {
										mdlDetalleCreditoCarrito.nuevo_telefono_principal =
											mdlDetalleCreditoCarrito.telefono_principal = JSON.parse(
												response.data.datos_credito.telefonos
											).t1;
									} else {
										mdlDetalleCreditoCarrito.nuevo_telefono_principal =
											mdlDetalleCreditoCarrito.telefono_principal =
												response.data.carrito_detalle.telefono_envio;
									}

									if (modo == "COBRAR") {
										mdlDetalleCreditoCarrito.modo = "NUEVO";
									} else if (modo == "VER") {
										mdlDetalleCreditoCarrito.modo = "EDITAR";
									}
								}
								EnviarDatos().then(() => {
									$("#mdlDetalleCreditoCarrito").css("display", "block");

									mdlDetalleCreditoCarrito.ResetearCampos();
								});
							});
					}
				});
		},

		async Anular(item) {
			Swal.fire({
				title: "¿DESEA ANULAR ESTA COBRANZA?",
				confirmButtonText: "SI",
				showCancelButton: true,
				cancelButtonText: "NO",
				allowOutsideClick: false,
				preConfirm: (result) => {
					Swal.fire({
						title: "Ingrese el motivo de la anulación",
						input: "text",
						customClass: {
							input: "mayus",
						},
						confirmButtonText: "Si",
						showCancelButton: true,
						cancelButtonText: "No",
						inputValidator: (value) => {
							const text = value.trim();
							if (!text) {
								return "*Obligatorio";
							}
							if (text.length < 15) {
								return "*Debe ingresar al menos 15 caracteres";
							}
						},
					}).then(async (result) => {
						if (result.isConfirmed) {
							let data = new FormData();
							data.append("carrito_detalle_id", item.carrito_detalle_id);
							data.append("motivo_anulacion", result.value);
							data.append("agencia_carrito", this.agencia_id);
							data.append("agencia_credito", item.agencia_id);
							data.append("credito_id", item.id);
							data.append("cliente_id", item.cliente_id);

							// this.$inertia.post(route("cre.carrito.anular"), data);
							// return false;

							await axios
								.post(route("cre.carrito.anular"), data)
								.then((response) => {
									let resultado = response.data;
									if (resultado == "EXITO") {
										Swal.fire({
											icon: "success",
											title: "¡ÉXITO!",
											timer: 2000,
											showConfirmButton: false,
										});

										this.$inertia.get(route("cre.carrito"));
									} else {
										Swal.fire({
											icon: "error",
											title: "¡Ups!",
											text: "Algo salió mal",
										});
									}
								});
						} else {
							return false;
						}
					});
				},
			});
		},

		Cerrar() {
			var self = this;

			let data = new FormData();

			data.append("carrito_id", this.mi_carrito.id);
			data.append("agencia_id", self.agencia_id);

			Swal.fire({
				title: "¿DESEA CERRAR ESTE CARRITO?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
				preConfirm: (result) => {
					Swal.fire({
						title: "CARGANDO",
						text: "Espere porfavor...",
						allowOutsideClick: false,

						didOpen: () => {
							Swal.showLoading();

							//   this.$inertia.post(route("cre.carrito.anular_carrito"), data);
							//   return false;

							axios
								.post(route("cre.carrito.cerrar_carrito"), data)
								.then((response) => {
									Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										timer: 1200,
										showConfirmButton: false,
									});
									return self.$inertia.get(route("cre.carrito"));
									//   this.listar();
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
		},
	},
};
</script>

<style lang="css">
.slot-carrito-cobranza {
	width: 60% !important;
	margin-left: 20% !important;
}
.mdlAgregarCredito {
	margin-top: 2.8%;
}

.btn1 {
	color: #fff;
	background-color: #6d9720;
	border-color: #6d9720;
	font-size: 10px !important;
}

.btn1:hover {
	color: #fff;
	background-color: #89bd29;
	border-color: #89bd29;
}

.btn2 {
	color: #fff;
	background-color: var(--azulOscuroEmpresarial);
	border-color: var(--azulOscuroEmpresarial);
	font-size: 10px !important;
}

.btn2:hover {
	color: #fff;
	background-color: #385efb;
	border-color: #385efb;
}

.class_externo {
	background-color: #d8f1fd !important;
}

.celda-resaltada {
	font-size: 14px !important;
	font-weight: bolder;
}

@media (max-width: 900px) {
	.slot-carrito-cobranza {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
	.btn1 {
		color: #fff;
		background-color: #6d9720;
		border-color: #6d9720;
		font-size: 10px !important;
	}

	.btn1:hover {
		color: #fff;
		background-color: #89bd29;
		border-color: #89bd29;
	}

	.btn2 {
		color: #fff;
		background-color: var(--azulOscuroEmpresarial);
		border-color: var(--azulOscuroEmpresarial);
		font-size: 10px !important;
	}

	.btn2:hover {
		color: #fff;
		background-color: #385efb;
		border-color: #385efb;
	}
}
</style>
