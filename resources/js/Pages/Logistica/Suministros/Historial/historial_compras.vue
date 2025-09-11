<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-compras" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HISTORIAL DE COMPRAS'"></headerClose>
					<!-- <div class="card-title">PANEL DE BUSQUEDA</div> -->
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
											v-model="datos_fecha.fecha_desde"
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
											v-model="datos_fecha.fecha_hasta"
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
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>

						<div class="card-title mt-1 mb-1">RESULTADOS DE BÚSQUEDA</div>

						<TabView class="mt-2">
							<TabPanel header="AGRUPADO">
								<DataTable
									:value="lista_agrupado"
									:scrollable="true"
									scrollDirection="both"
									:scrollHeight="String(windowHeigth * 0.46) + 'px'"
									showGridlines
								>
									<Column
										header="DETALLE"
										:styles="{ width: '70px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<button
												class="btn btn-action btn-icon-split"
												@click="VerDetalle(data)"
											>
												<span class="icon text-white">
													<i class="far fa-eye"></i>
												</span>
											</button>
										</template>
									</Column>

									<Column
										field="agencia"
										header="AGENCIA"
										:styles="{ width: '140px', justifyContent: 'center' }"
									/>

									<Column
										field="fecha_compra"
										header="FECHA_COMPRA"
										:styles="{ width: '130px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ JSON.parse(data.datos_creacion).fecha }}
										</template>
									</Column>
									<Column
										field="monto_total"
										header="MONTO_TOTAL"
										:styles="{
											width: '130px',
											justifyContent: 'right',
											fontWeight: 'bolder',
										}"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.monto_total, 2) }}
										</template>
									</Column>

									<Column
										field="usuario_compra"
										header="USUARIO_COMPRA"
										:styles="{ width: '160px', justifyContent: 'center' }"
									/>

									<Column
										field="usuario_registro"
										header="USUARIO_REGISTRO"
										:styles="{ width: '160px', justifyContent: 'center' }"
									/>
									<ColumnGroup type="footer">
										<Row>
											<Column
												:colspan="3"
												footer="TOTAL"
												:footerStyle="{
													width: '350px ',
													backgroundColor: '#244b9a !important',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="1"
												:footer="
													'S/ ' + roundTo(this.total_compras_agrupado, 2)
												"
												:footerStyle="{
													width: '130px',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="2"
												:footer="null"
												:footerStyle="{
													width: '320px',
													backgroundColor: 'transparent !important',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
										</Row>
									</ColumnGroup>
									<template #empty> No se encontraron resultados.</template>
								</DataTable>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-cancel btn-icon-split"
										title="Exportar"
										@click="Exportar('agrupado')"
									>
										<span class="icon text-white">
											<i class="fas fa-file-excel"></i>
										</span>
										<span class="text">EXPORTAR</span>
									</button>
								</div>
							</TabPanel>
							<TabPanel header="DETALLADO">
								<div class="row mb-1 justify-content-md-center">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text"
												><i class="fas fa-search"></i
											></span>
										</div>
										<input
											class="form-control mayus"
											type="text"
											autocomplete="off"
											spellcheck="false"
											placeholder="INGRESE EL NOMBRE DEL SUMINISTRO"
											v-model="filtros_tabla['suministro'].value"
											:disabled="lista_agrupado.length == 0"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text">CLASIF.</span>
										</div>
										<select
											class="form-control center"
											:disabled="lista_detallado.length == 0"
											v-model="filtros_tabla['clasificacion'].value"
										>
											<option :value="null">TODOS</option>
											<option :value="'SUMINISTRO'">SUMINISTRO</option>
											<option :value="'GASTO'">GASTO</option>
										</select>
									</div>
									<div class="col-md-2">
										<div class="text-right">
											<button
												class="btn btn-cancel btn-icon-split"
												title="Exportar"
												@click="Exportar('detallado')"
											>
												<span class="icon text-white">
													<i class="fas fa-file-excel"></i>
												</span>
												<span class="text">EXPORTAR</span>
											</button>
										</div>
									</div>
								</div>
								<DataTable
									:value="lista_detallado_filtrado"
									:scrollable="true"
									scrollDirection="both"
									:scrollHeight="String(windowHeigth * 0.4) + 'px'"
									showGridlines
									:paginator="true"
									:rows="100"
									paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
								>
									<Column
										field="agencia"
										header="AGENCIA"
										:styles="{ width: '100px', justifyContent: 'center' }"
									/>
									<Column
										header="FECHA_COMPRA"
										:styles="{ width: '120px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ JSON.parse(data.datos_creacion).fecha }}
										</template>
									</Column>

									<Column
										field="usuario_compra"
										header="USUARIO_COMPRA"
										:styles="{ width: '140px', justifyContent: 'center' }"
									/>
									<Column
										field="codigo"
										header="CÓDIGO"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									/>
									<Column
										field="suministro"
										header="SUMINISTRO"
										:styles="{ width: '200px', fontWeight: 'bolder' }"
									/>
									<Column
										field="tipo"
										header="TIPO_SUMINISTRO"
										:styles="{ width: '120px', justifyContent: 'center' }"
									/>

									<Column
										field="condicion"
										header="CONDICIÓN"
										:styles="{ width: '100px', justifyContent: 'center' }"
									/>

									<Column
										field="cantidad"
										header="CANTIDAD"
										:styles="{ width: '80px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ roundTo(data.cantidad, 2) }}
										</template>
									</Column>

									<Column
										field="valor_unitario"
										header="VALOR_UNITARIO"
										:styles="{ width: '110px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.valor_unitario, 2) }}
										</template>
									</Column>

									<Column
										header="VALOR_TOTAL"
										:styles="{ width: '120px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.valor_total, 2) }}
										</template>
									</Column>

									<Column
										field="usuario_registro"
										header="USUARIO_REGISTRO"
										:styles="{ width: '140px', justifyContent: 'center' }"
									/>
									<Column
										field="clasificacion"
										header="CLASIFICACIÓN"
										:styles="{ width: '100px', justifyContent: 'center' }"
									/>
									<ColumnGroup type="footer">
										<Row>
											<Column
												:colspan="9"
												footer="TOTAL"
												:footerStyle="{
													width: '1070px !important',
													backgroundColor: '#244b9a !important',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="1"
												:footer="
													'S/ ' + roundTo(this.total_compras_detallado, 2)
												"
												:footerStyle="{
													width: '120px',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="2"
												:footer="null"
												:footerStyle="{
													width: '240px',
													backgroundColor: 'transparent !important',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
										</Row>
									</ColumnGroup>
									<template #empty> No se encontraron resultados.</template>
								</DataTable>
								<hr />
								<div class="text-right"></div>
							</TabPanel>
						</TabView>
					</div>
				</div>
			</div>
			<!-- The Modal -->
			<div id="mdlCompraDetalle" class="modal">
				<div class="modal-content w-50 mdlCompraDetalle">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'DETALLE DE COMPRA'"
								:nombre_modal="'mdlCompraDetalle'"
							>
							</headerCloseModal>
							<div class="card-body card-block">
								<div class="row mb-1 justify-content-md-center">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text"
												><i class="fas fa-search"></i
											></span>
										</div>
										<input
											class="form-control mayus"
											type="text"
											autocomplete="off"
											spellcheck="false"
											placeholder="INGRESE EL NOMBRE DEL SUMINISTRO"
											v-model="filtros_tabla_2['suministro'].value"
											:disabled="compra_detalle.length == 0"
										/>
									</div>
								</div>

								<DataTable
									:value="compra_detalle_filtrado"
									:scrollable="true"
									scrollDirection="both"
									:scrollHeight="String(windowHeigth * 0.5) + 'px'"
									showGridlines
								>
									<Column
										field="codigo"
										header="CÓDIGO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									/>

									<Column
										field="suministro"
										header="SUMINISTRO"
										:styles="{ width: '200px', fontWeight: 'bolder' }"
									/>

									<Column
										field="tipo"
										header="TIPO_SUMINISTRO"
										:styles="{ width: '130px', justifyContent: 'center' }"
									/>

									<Column
										field="agencia"
										header="AGENCIA"
										:styles="{ width: '120px', justifyContent: 'center' }"
									/>

									<Column
										field="condicion"
										header="CONDICIÓN"
										:styles="{ width: '120px', justifyContent: 'center' }"
									/>

									<Column
										field="cantidad"
										header="CANTIDAD"
										:styles="{ width: '90px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ roundTo(data.cantidad, 2) }}
										</template>
									</Column>

									<Column
										field="valor_unitario"
										header="VALOR_UNITARIO"
										:styles="{ width: '110px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.valor_unitario, 2) }}
										</template>
									</Column>
									<Column
										field="valor_total"
										header="VALOR_TOTAL"
										:styles="{ width: '120px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.valor_total, 2) }}
										</template>
									</Column>
									<Column
										field="usuario_registro"
										header="USUARIO_REGISTRO"
										:styles="{ width: '140px', justifyContent: 'center' }"
									/>
								</DataTable>
								<hr />
								<div class="text-right">
									<label class="form-control-label label-title"
										>COMPROBANTE DE COMPRA:</label
									>
									<button
										class="btn btn-action btn-icon-split"
										title="Descargar DOCUMENTO DE COMPRA"
										@click="Descargar()"
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
	</layout>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import TabView from "primevue/tabview/tabview.common";
import TabPanel from "primevue/tabpanel/tabpanel.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,

		DataTable,
		Column,
		TabView,
		TabPanel,
		ColumnGroup,
		Row,
	},
	props: {},
	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			agencias: [],
			agencias_permitidas: [],
			agencia_busqueda: 0,

			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},

			lista_agrupado: [],
			lista_detallado: [],

			compra_detalle: [],
			documento_compra: null,

			filtros_tabla: {
				suministro: { value: null },
				clasificacion: { value: null },
			},

			filtros_tabla_2: {
				suministro: { value: null },
			},
		};
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
	},
	computed: {
		lista_detallado_filtrado() {
			const filtro_suministro = this.filtros_tabla["suministro"].value;
			const filtro_clasificacion = this.filtros_tabla["clasificacion"].value;

			return this.lista_detallado.filter((item) => {
				if (filtro_suministro && filtro_suministro.length >= 3) {
					if (
						!item.suministro
							.toLowerCase()
							.includes(filtro_suministro.toLowerCase())
					) {
						return false;
					}
				}

				if (filtro_clasificacion) {
					if (item.clasificacion !== filtro_clasificacion) {
						return false;
					}
				}

				return true;
			});
		},
		compra_detalle_filtrado() {
			const filtro_suministro = this.filtros_tabla_2["suministro"].value;

			return this.compra_detalle.filter((item) => {
				if (filtro_suministro && filtro_suministro.length >= 3) {
					if (
						!item.suministro
							.toLowerCase()
							.includes(filtro_suministro.toLowerCase())
					) {
						return false;
					}
				}

				return true;
			});
		},

		total_compras_agrupado() {
			return this.lista_agrupado.reduce(
				(total, item) => total + item.monto_total,
				0
			);
		},

		total_compras_detallado() {
			return this.lista_detallado_filtrado.reduce(
				(total, item) => total + parseFloat(item.valor_total),
				0
			);
		},
	},

	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
		});

		this.listar_agencias();

		this.ListarRecursos();
	},

	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_SUMINISTROS/HISTORIAL_COMPRAS"
			);
		},

		roundTo(value, decimal_places) {
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

		async ListarRecursos() {
			// this.$inertia.get(route("log.sum.envios_recepciones.listar_recursos"));
			// return false;

			await axios
				.get(route("log.sum.envios_recepciones.listar_recursos"))
				.then((response) => {
					this.datos_fecha.fecha_desde = response.data.fecha_desde;
					this.datos_fecha.fecha_hasta = response.data.fecha_hasta;
				});
		},

		async Buscar() {
			const params = {
				agencia_id: this.agencia_busqueda,
				fecha_desde: this.datos_fecha.fecha_desde,
				fecha_hasta: this.datos_fecha.fecha_hasta,
			};

			// this.$inertia.get(route("log.sum.historial_compras.buscar"), params);
			// return false;

			Swal.fire({
				title: "BUSCANDO...",
				showConfirmButton: false,
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					try {
						const response = await axios.get(
							route("log.sum.historial_compras.buscar"),
							{
								params,
							}
						);

						this.lista_agrupado = response.data.lista_agrupado;
						this.lista_detallado = response.data.lista_detallado;
						// Espera un poco para asegurar cierre suave del primer modal
						await Swal.close();

						return Swal.fire({
							icon: "success",
							title: "¡Listo!",
							showConfirmButton: false,
							timer: 1500,
						});
					} catch (error) {
						console.error(error);
						await Swal.close(); // cierra primero el anterior

						Swal.fire({
							icon: "error",
							title: "Error",
							text: `Ha ocurrido un error. Comunicar a SOPORTE: ${error.message}`,
						});
					}
				},
			});
		},

		async VerDetalle(item) {
			this.compra_detalle = this.lista_detallado.filter(
				(item_1) => item_1.compra_id == item.id
			);

			this.documento_compra = item.documento;

			$("#mdlCompraDetalle").css("display", "block");
		},
		Descargar() {
			let self = this;
			let source =
				"/imagenes_server/logistica/suministros/compras/" +
				this.documento_compra +
				"";
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], { type: response.data.type });
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = self.documento_compra;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},

		async Exportar(modo) {
			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("modo", modo);

			if (modo == "agrupado") {
				data.append("lista_agrupado", JSON.stringify(this.lista_agrupado));
			} else if (modo == "detallado") {
				data.append(
					"lista_detallado",
					JSON.stringify(this.lista_detallado_filtrado)
				);
			}

			// this.$inertia.post(route("log.sum.historial_compras.exportar"), data);
			// return false;

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("log.sum.historial_compras.exportar"), data)
						.then(async (response) => {
							const path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.click();

							// Espera un poco para asegurar cierre suave del primer modal
							await Swal.close();

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
.slot-historial-compras {
	width: 50%;
	margin-left: 25%;
}

@media (max-width: 1536px) {
	.slot-historial-compras {
		width: 60%;
		margin-left: 20%;
	}
}

@media (max-width: 1366px) {
	.slot-historial-compras {
		width: 70%;
		margin-left: 15%;
	}
}
@media (max-width: 900px) {
	.slot-historial-compras {
		width: 98%;
		margin-left: 2%;
	}
}
</style>
