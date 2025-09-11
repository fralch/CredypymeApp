<template>
	<layout ref="layout">
		<div class="slot_body slot-cobranza-tipo" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						title="
							COBRANZAS POR TIPO DE CRÉDITO
						"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-4">
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
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="fecha_desde"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="fecha_hasta"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
									<div class="col-md-1 text-right" v-if="windowWidth < 900">
										<button
											class="btn btn-action btn-icon-split mt-3"
											title="Buscar"
											@click="Buscar"
											v-if="agencia_busqueda != 0 && agencia_busqueda != null"
										>
											<span class="icon text-white" style="font-size: 15px">
												<i class="fas fa-search"></i>
											</span>
										</button>
									</div>
								</div>
							</fieldset>
							<div class="col-md-1 ml-3" v-if="windowWidth >= 900">
								<button
									class="btn btn-action btn-icon-split mt-3"
									title="Buscar"
									@click="Buscar"
									v-if="agencia_busqueda != 0 && agencia_busqueda != null"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>
						<div class="form-row">
							<!-- -------------------------------------------------------- -->

							<div class="col-md-12">
								<div class="card-title">LISTA DE RESULTADOS</div>

								<fieldset class="p-0 pb-1 mb-1">
									<legend>
										<label class="label-title">FILTRAR RESULTADOS</label>
									</legend>
									<div class="form-row col-md-12">
										<div class="input-group col-md-4">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title">TIPO</span>
											</div>
											<select
												class="form-control center"
												:disabled="lista_cobranzas.length == 0"
												v-model="filtros_tabla['tipo_id'].value"
											>
												>
												<option :value="null" selected>TODOS</option>
												<option
													v-for="(item, index) in tipos"
													:key="index"
													:value="item.id"
												>
													{{ item.tipo }}
												</option>
											</select>
										</div>
										<div class="input-group col-md-5">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>PRODUCTO</span
												>
											</div>
											<select
												class="form-control center"
												:disabled="lista_cobranzas.length == 0"
												v-model="filtros_tabla['producto_id'].value"
											>
												>
												<option :value="null" selected>TODOS</option>
												<option
													v-for="(item, index) in productos"
													:key="index"
													:value="item.id"
												>
													{{ item.producto }}
												</option>
											</select>
										</div>
										<div class="input-group col-md-3">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>ESPECIAL</span
												>
											</div>
											<select
												class="form-control center"
												:disabled="lista_cobranzas.length == 0"
												v-model="filtros_tabla['es_especial'].value"
											>
												>
												<option :value="null" selected>TODOS</option>
												<option :value="1">SI</option>
												<option :value="0">NO</option>
											</select>
										</div>
									</div>
								</fieldset>

								<DataTable
									:value="lista_cobranzas_filtrado"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="350px"
									selectionMode="single"
									:selection="cobranza_seleccionada"
									:paginator="true"
									:rows="30"
									paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
									showGridlines
									stripedRows
								>
									<Column
										field="expediente"
										header="EXP."
										:styles="{
											width: '60px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											{{ data.numero_expediente + "-" + data.numero_credito }}
										</template>
									</Column>
									<Column
										field="numero_cuota"
										header="CUOTA"
										:styles="{
											width: '50px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="cliente"
										header="CLIENTE"
										:styles="{
											width: '250px',
										}"
									>
									</Column>
									<Column
										field="usuario_asesor"
										header="ASESOR"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="fecha_pago"
										header="FECHA"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="capital"
										header="CAPITAL"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.capital == 0
													? "-"
													: "S/ " + RedondearVista(data.capital, 2)
											}}
										</template>
									</Column>
									<Column
										field="interes"
										header="INTERES"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.interes == 0
													? "-"
													: "S/ " + RedondearVista(data.interes, 2)
											}}
										</template>
									</Column>
									<Column
										field="redondeo"
										header="REDONDEO"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.redondeo == 0
													? "-"
													: "S/ " + RedondearVista(data.redondeo, 2)
											}}
										</template>
									</Column>
									<Column
										field="moras"
										header="MORAS"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.moras == 0
													? "-"
													: "S/ " + RedondearVista(data.moras, 2)
											}}
										</template>
									</Column>
									<Column
										field="notificaciones"
										header="NOTIF."
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.notificaciones == 0
													? "-"
													: "S/ " + RedondearVista(data.notificaciones, 2)
											}}
										</template>
									</Column>

									<Column
										field="dscto_mora"
										header="DSCTO_MORAS"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.dscto_mora == 0
													? "-"
													: "S/ " + RedondearVista(data.dscto_mora, 2)
											}}
										</template>
									</Column>
									<Column
										field="dscto_notificaciones"
										header="DSCTO_NOTIF"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.dscto_notificaciones == 0
													? "-"
													: "S/ " + RedondearVista(data.dscto_notificaciones, 2)
											}}
										</template>
									</Column>
									<Column
										field="dscto_interes"
										header="DSCTO_INTER"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.dscto_interes == 0
													? "-"
													: "S/ " + RedondearVista(data.dscto_interes, 2)
											}}
										</template>
									</Column>
									<Column
										field="total_pago"
										header="TOTAL_PAGADO"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											{{
												data.total_pago == 0
													? "-"
													: "S/ " + RedondearVista(data.total_pago, 2)
											}}
										</template>
									</Column>
									<Column
										field="comentario"
										header="COMENTARIO"
										:styles="{
											width: '150px',
										}"
									>
										<template #body="{ data }">
											{{ data.comentario == null ? "-" : data.comentario }}
										</template>
									</Column>
									<Column
										field="usuario_caja"
										header="CAJA"
										:styles="{
											width: '120px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="usuario_agencia"
										header="AGENCIA"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="tipo"
										header="TIPO"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="producto"
										header="PRODUCTO"
										:styles="{
											width: '200px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="es_especial"
										header="ESPECIAL"
										:styles="{
											width: '70px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											{{ data.es_especial == 1 ? "SI" : "NO" }}
										</template>
									</Column>

									<ColumnGroup type="footer">
										<Row>
											<Column
												:colspan="5"
												footer="TOTAL"
												:footerStyle="{
													width: '620px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' + RedondearVista(totales.total_capital, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' + RedondearVista(totales.total_interes, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' + RedondearVista(totales.total_redondeo, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="'S/ ' + RedondearVista(totales.total_mora, 2)"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' +
													RedondearVista(totales.total_notificaciones, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' + RedondearVista(totales.total_dscto_mora, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' +
													RedondearVista(totales.total_dscto_notificaciones, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="
													'S/ ' + RedondearVista(totales.total_dscto_interes, 2)
												"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:footer="'S/ ' + RedondearVista(totales.total_pago, 2)"
												:footerStyle="{
													width: '100px',
													'text-align': 'right',
													'font-size': '13px !important',
												}"
											/>
											<Column
												:colspan="6"
												footer=""
												:footerStyle="{
													width: '770px',
													'text-align': 'right',
												}"
											/>
										</Row>
									</ColumnGroup>
									<template #empty> No hay COBRANZAS registradas.</template>
								</DataTable>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar"
								:disabled="lista_cobranzas_filtrado.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-file-excel"></i>
								</span>
								<span class="text">EXPORTAR</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
	components: {
		layout,
		headerClose,

		DataTable,
		Column,
		ColumnGroup,
		Row,
	},
	props: { modo: String, usuarios: Array },

	data() {
		return {
			agencias_permitidas: [],
			agencia_busqueda: 0,

			windowWidth: window.innerWidth,

			fecha_desde: null,
			fecha_hasta: null,

			tipos: [],
			productos: [],

			lista_cobranzas: [],
			cobranza_seleccionada: null,

			filtros_tabla: {
				tipo_id: { value: null },
				producto_id: { value: null },
				es_especial: { value: null },
			},
		};
	},
	computed: {
		lista_cobranzas_filtrado() {
			const filtro_tipo_credito = this.filtros_tabla["tipo_id"].value;
			const filtro_producto_credito = this.filtros_tabla["producto_id"].value;
			const filtro_es_especial = this.filtros_tabla["es_especial"].value;

			return this.lista_cobranzas.filter((item) => {
				if (filtro_tipo_credito) {
					if (item.tipo_id !== filtro_tipo_credito) {
						return false;
					}
				}
				if (filtro_producto_credito) {
					if (item.producto_id !== filtro_producto_credito) {
						return false;
					}
				}
				if (filtro_es_especial) {
					if (item.es_especial !== filtro_es_especial) {
						return false;
					}
				}
				return true;
			});
		},

		totales() {
			let total_capital = 0;
			let total_interes = 0;
			let total_redondeo = 0;
			let total_mora = 0;
			let total_notificaciones = 0;
			let total_dscto_mora = 0;
			let total_dscto_notificaciones = 0;
			let total_dscto_interes = 0;
			let total_pago = 0;

			if (this.lista_cobranzas_filtrado.length > 0) {
				total_capital = this.lista_cobranzas_filtrado.reduce((total, item) => {
					return parseFloat(total) + parseFloat(item.capital);
				}, 0);

				total_interes = this.lista_cobranzas_filtrado.reduce((total, item) => {
					return parseFloat(total) + parseFloat(item.interes);
				}, 0);
				total_redondeo = this.lista_cobranzas_filtrado.reduce((total, item) => {
					return parseFloat(total) + parseFloat(item.redondeo);
				}, 0);
				total_mora = this.lista_cobranzas_filtrado.reduce((total, item) => {
					return parseFloat(total) + parseFloat(item.moras);
				}, 0);
				total_notificaciones = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.notificaciones);
					},
					0
				);
				total_dscto_mora = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.dscto_mora);
					},
					0
				);
				total_dscto_notificaciones = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.dscto_notificaciones);
					},
					0
				);
				total_dscto_interes = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.dscto_interes);
					},
					0
				);
				total_pago = this.lista_cobranzas_filtrado.reduce((total, item) => {
					return parseFloat(total) + parseFloat(item.total_pago);
				}, 0);
			}

			return {
				total_capital: total_capital,
				total_interes: total_interes,
				total_redondeo: total_redondeo,
				total_mora: total_mora,
				total_notificaciones: total_notificaciones,
				total_dscto_mora: total_dscto_mora,
				total_dscto_notificaciones: total_dscto_notificaciones,
				total_dscto_interes: total_dscto_interes,
				total_pago: total_pago,
			};
		},
	},
	watch: {
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
		agencia_busqueda() {
			this.FechaActual();
			this.ListarRecursos();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},
		async FechaActual() {
			if (this.agencia_busqueda == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_busqueda
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_COBRANZAS_TIPO"
			);
		},
		async ListarRecursos() {
			const params = { agencia_id: this.agencia_busqueda };

			await axios
				.get(route("rep.caj.cobranzas_tipo.listar_recursos"), { params })
				.then((response) => {
					this.tipos = response.data.tipos;
					this.productos = response.data.productos;
				});
		},
		RedondearVista(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			if (valor == 0) {
				return "-";
			} else {
				return parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
		},

		async Buscar() {
			// Verificar que no se selecciones rango de fechas mayores a 3 meses
			const fecha_inicio = moment(this.fecha_desde);
			const fecha_fin = moment(this.fecha_hasta);
			const meses_diferencia = fecha_fin.diff(fecha_inicio, "months");

			if (meses_diferencia > 3) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "No se pueden realizar busquedas entre fechas mayores a 3 MESES, intente nuevamente",
					confirmButtonText: "Ok",
					allowOutsideClick: true,
				});
				return false;
			}

			const params = {
				agencia_id: this.agencia_busqueda,
				fecha_desde: this.fecha_desde,
				fecha_hasta: this.fecha_hasta,
			};

			// this.$inertia.get(route("rep.caj.cobranzas_tipo.buscar"), params);
			// return false;

			Swal.fire({
				title: "BUSCANDO...",
				showConfirmButton: false,
				allowOutsideClick: false,
				willOpen: async () => {
					Swal.showLoading();

					return await axios
						.get(route("rep.caj.cobranzas_tipo.buscar"), { params })
						.then((response) => {
							this.lista_cobranzas = response.data.lista_cobranzas;
							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								timer: 1200,
								showConfirmButton: false,
							});
						})
						.catch((error) => {
							console.log(error);
							Swal.showValidationMessage(
								`Ha ocurrido un error, comunicar a SOPORTE: ${error}`
							);
						});
				},
			});
		},
		async Exportar() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append(
				"lista_cobranzas",
				JSON.stringify(this.lista_cobranzas_filtrado)
			);

			// this.$inertia.post(route("rep.caj.cobranzas_tipo.exportar"), data);
			// return false;

			Swal.fire({
				title: "EXPORTANDO...",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("rep.caj.cobranzas_tipo.exportar"), data)
						.then(function (response) {
							const path_xlsx = response.data.path_xlsx;
							const link = document.createElement("a");

							link.href = origin + path_xlsx;
							link.click();

							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
								timer: 1200,
								showConfirmButton: false,
							});
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-cobranza-tipo {
	width: 70% !important;
	margin-left: 15% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media (max-width: 900px) {
	.slot-cobranza-tipo {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
