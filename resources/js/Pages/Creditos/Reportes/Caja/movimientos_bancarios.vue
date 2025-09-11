<template>
	<layout ref="layout">
		<div class="slot_body slot-rpt-movimientos-bancarios" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						title="
							MOVIMIENTOS BANCARIOS
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
													? 'font-size: 16px !important'
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
													? 'font-size: 16px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
								</div>
							</fieldset>

							<div class="col-md-1 ml-3 mt-1" v-if="windowWidth >= 900">
								<button
									class="btn btn-action btn-icon-split"
									title="Buscar"
									@click="BuscarGeneral"
									v-if="agencia_busqueda != 0 && agencia_busqueda != null"
								>
									<span class="icon text-white">
										<i class="fas fa-search"></i>
									</span>
									<span class="text">GENERAL</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split mt-1"
									title="Buscar"
									@click="BuscarDetallado('entre_fechas')"
									v-if="agencia_busqueda != 0 && agencia_busqueda != null"
								>
									<span class="icon text-white">
										<i class="fas fa-search"></i>
									</span>
									<span class="text">DETALLADO</span>
								</button>
							</div>
						</div>
						<div class="form-row">
							<!-- <div
								class="w-20"
								style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)"
								v-if="filtro_caja && tab_index == 1"
							>
								<div class="col-md-12 input-group mt-2">
									<select class="form-control center" v-model="agencia_filtro">
										<option
											v-for="item in agencias_permitidas"
											:key="item.id"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>

								<div class="form-check text-center mt-1">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbHabilitados"
										v-model="mostrar_habilitados"
										@change="FiltrarCajas"
									/>
									<label class="label-title" for="chbHabilitados"
										>Sólo habilitados</label
									>
								</div>

								<DataTable
									:value="cajas_filtradas"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="160px"
									showGridlines
									stripedRows
								>
									<Column
										field="usuario"
										header="USUARIO"
										:styles="{ width: '70px', padding: '6px !important' }"
									>
										<template #body="{ data, index }">
											<div
												class="custom-control custom-checkbox pl-3 m-0"
												style="min-height: auto !important"
											>
												<input
													type="checkbox"
													class="custom-control-input"
													:id="'chbUsuario_' + index"
													:value="data.dni"
													v-model="cajas_seleccionadas"
												/>
												<label
													class="custom-control-label ml-4"
													:for="'chbUsuario_' + index"
													style="cursor: pointer"
													>{{ data.usuario }}</label
												>
											</div>
										</template>
									</Column>
								</DataTable>
							</div> -->
							<!-- -------------------------------------------------------- -->
							<!-- 
							<div
								:class="filtro_caja && tab_index == 1 ? 'pl-1 w-80' : 'w-100'"
							> -->
							<div class="w-100">
								<div class="card-title mb-1">LISTA DE RESULTADOS</div>
								<!-- <hr /> -->

								<TabView :activeIndex.sync="tab_index">
									<TabPanel header="GENERAL">
										<DataTable
											:value="lista_movimientos_general"
											class="tblMovimientosGenerales"
											:rowClass="getRowClass"
											rowGroupMode="rowspan"
											groupRowsBy="fecha"
											scrollDirection="both"
											scrollHeight="400px"
											showGridlines
											stripedRows
										>
											<Column
												field="fecha"
												header="FECHA"
												rowGroup
												:styles="{
													width: '130px',
													textAlign: 'center',
												}"
											>
												<template #body="{ data }">
													<button
														class="btn btn-link bolder"
														style="font-size: 15px"
														@click="BuscarDetallado('por_fecha', data)"
													>
														{{ data.fecha }}
													</button>
												</template>
											</Column>
											<Column
												field="banco"
												header="BANCO"
												:styles="{ width: '200px', textAlign: 'center' }"
											>
											</Column>
											<Column
												field="monto_inicial"
												header="MONTO_INICIAL"
												:styles="{ width: '100px', textAlign: 'right' }"
											>
												<template #body="{ data }">
													<div class="bolder" style="font-size: 13px">
														S/ {{ RedondearVista(data.monto_inicial, 2) }}
													</div>
												</template>
											</Column>
											<Column
												field="monto_final"
												header="MONTO_FINAL"
												:styles="{ width: '100px', textAlign: 'right' }"
											>
												<template #body="{ data }">
													<div class="bolder" style="font-size: 13px">
														S/ {{ RedondearVista(data.monto_final, 2) }}
													</div>
												</template>
											</Column>
											<Column
												field="diferencia"
												header="DIFERENCIA"
												:styles="{ width: '100px', textAlign: 'center' }"
											>
												<template #body="{ data }">
													<div
														class="bolder"
														:class="[data.diferencia >= 0 ? 'subio' : 'bajo']"
														style="font-size: 13px"
													>
														{{
															data.diferencia > 0
																? "+"
																: data.diferencia < 0
																? "-"
																: ""
														}}
														{{
															data.diferencia != 0
																? "S/ " +
																  RedondearVista(Math.abs(data.diferencia), 2)
																: "-"
														}}
													</div>
												</template>
											</Column>

											<template #empty>
												No hay RESULTADOS encontradoss.</template
											>
										</DataTable>

										<hr />
										<div class="text-right">
											<div class="btn-group" role="group">
												<button
													class="btn btn-cancel btn-icon-split"
													title="Exportar"
													@click="Exportar('general')"
													:disabled="lista_movimientos_general.length == 0"
												>
													<span class="icon text-white">
														<i class="fas fa-file-excel"></i>
													</span>
													<span class="text">EXPORTAR</span>
												</button>
											</div>
										</div>
									</TabPanel>
									<TabPanel header="DETALLADO">
										<div class="form-row col-md-12 justify-content-md-center">
											<!-- <div class="form-group col-md-2">
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														id="chbPorUsuario"
														v-model="filtro_caja"
													/>
													<label class="label-title" for="chbPorUsuario"
														>Filtrar por CAJA</label
													>
												</div>
											</div> -->

											<div class="input-group col-md-3">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>TIPO</span
													>
												</div>
												<select
													class="form-control center"
													v-model="filtros_tabla['tipo'].value"
													:disabled="lista_movimientos_detallado.length == 0"
												>
													>
													<option :value="null" selected>TODOS</option>
													<option value="I">INGRESO</option>
													<option value="E">EGRESO</option>
												</select>
											</div>
											<div class="input-group col-md-4">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>BANCO</span
													>
												</div>
												<select
													class="form-control center"
													v-model="filtros_tabla['banco_id'].value"
													:disabled="lista_movimientos_detallado.length == 0"
												>
													>
													<option :value="null" selected>TODOS</option>
													<option
														v-for="(item, index) in bancos_filtrados"
														:key="index"
														:value="item.id"
													>
														{{ item.banco }}
													</option>
												</select>
											</div>

											<div class="input-group col-md-3">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>OPER.
													</span>
												</div>
												<select
													class="form-control center"
													v-model="filtros_tabla['operacion'].value"
													:disabled="lista_movimientos_detallado.length == 0"
												>
													>
													<option :value="null" selected>TODOS</option>
													<option value="RETIRO">RETIRO</option>
													<option value="COMISION">COMISIÓN</option>
													<option value="TRANSFERENCIA">TRANSFERENCIA</option>
													<option value="COBRANZA">COBRANZA</option>
												</select>
											</div>
										</div>
										<hr />
										<DataTable
											:value="lista_movimientos_filtrados"
											:scrollable="true"
											scrollDirection="both"
											scrollHeight="320px"
											selectionMode="single"
											:paginator="true"
											:rows="100"
											paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
											currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
											showGridlines
											stripedRows
										>
											<Column
												field="index"
												header="N°"
												:styles="{ width: '40px', justifyContent: 'center' }"
											>
												<template #body="{ data }">
													{{ data.index + 1 }}
												</template>
											</Column>
											<Column
												field="fecha_movimiento"
												header="FECHA"
												:styles="{ width: '130px', justifyContent: 'center' }"
											>
											</Column>
											<Column
												field="tipo"
												header="TIPO"
												:styles="{ width: '50px', justifyContent: 'center' }"
											>
											</Column>
											<Column
												field="banco"
												header="BANCO"
												:styles="{ width: '120px', justifyContent: 'center' }"
											>
											</Column>

											<Column
												field="operacion"
												header="OPERACIÓN"
												:styles="{ width: '100px', justifyContent: 'center' }"
											>
											</Column>
											<Column
												field="modo"
												header="MODO"
												:styles="{ width: '100px', justifyContent: 'center' }"
											>
											</Column>
											<Column
												field="descripcion"
												header="DESCRIPCIÓN"
												:styles="{ width: '200px', justifyContent: 'left' }"
											>
											</Column>
											<Column
												field="monto"
												header="MONTO"
												:styles="{ width: '100px', justifyContent: 'right' }"
											>
												<template #body="{ data }">
													<div class="bolder">
														{{ data.tipo == "E" ? "-" : "" }} S/
														{{ RedondearVista(data.monto, 2) }}
													</div>
												</template>
											</Column>

											<Column
												field="usuario_operacion"
												header="USUARIO_REG"
												:styles="{ width: '120px', justifyContent: 'center' }"
											>
											</Column>
											<Column
												field="banco_transferencia"
												header="BANCO_TRANSFERENCIA"
												:styles="{ width: '150px', justifyContent: 'center' }"
											>
												<template #body="{ data }">
													{{
														data.banco_transferencia
															? data.banco_transferencia
															: "-"
													}}
												</template>
											</Column>

											<ColumnGroup type="footer">
												<Row>
													<Column
														:colspan="7"
														footer="TOTAL"
														:footerStyle="{
															width: '740px',
															backgroundColor: '#244b9a !important',
															fontSize: '13px !important',
															textAlign: 'right',
														}"
													/>
													<Column
														:colspan="1"
														:footer="totales.total_monto"
														:footerStyle="{
															width: '100px',
															fontSize: '13px !important',
															textAlign: 'right',
														}"
													/>
													<Column
														:colspan="3"
														:footer="null"
														:footerStyle="{
															width: '270px',
															backgroundColor: 'transparent !important',
															textAlign: 'right',
														}"
													/>
												</Row>
											</ColumnGroup>
											<template #empty>
												No hay MOVIMIENTOS registrados.</template
											>
										</DataTable>
										<hr />
										<div class="text-right">
											<div class="btn-group" role="group">
												<button
													class="btn btn-cancel btn-icon-split"
													title="Exportar"
													@click="Exportar('detallado')"
													:disabled="lista_movimientos_filtrados.length == 0"
												>
													<span class="icon text-white">
														<i class="fas fa-file-excel"></i>
													</span>
													<span class="text">EXPORTAR</span>
												</button>
											</div>
										</div></TabPanel
									>
								</TabView>
							</div>
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";
import TabView from "primevue/tabview/tabview.common";
import TabPanel from "primevue/tabpanel/tabpanel.common";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DataTable,
		Column,
		Row,
		ColumnGroup,
		TabView,
		TabPanel,
	},
	data() {
		return {
			windowWidth: window.innerWidth,

			agencias_permitidas: [],
			agencia_busqueda: 0,
			// agencia_filtro: 0,

			fecha_desde: null,
			fecha_hasta: null,

			tab_index: 0,

			lista_movimientos_general: [],
			lista_movimientos_detallado: [],

			// filtro_caja: false,
			// mostrar_habilitados: true,
			// usuarios_cajas: [],
			// cajas_filtradas: [],
			// cajas_seleccionadas: [],

			total_monto_movimientos: 0,
			bancos: [],
			bancos_filtrados: [],

			filtros_tabla: {
				banco_id: { value: null },
				tipo: { value: null },
				operacion: { value: null },
			},
		};
	},
	computed: {
		lista_movimientos_filtrados() {
			const filtro_banco = this.filtros_tabla["banco_id"].value;
			const filtro_tipo = this.filtros_tabla["tipo"].value;
			const filtro_operacion = this.filtros_tabla["operacion"].value;

			return this.lista_movimientos_detallado.filter((item) => {
				if (filtro_banco) {
					if (item.banco_id !== filtro_banco) {
						return false;
					}
				}
				if (filtro_tipo) {
					if (item.tipo !== filtro_tipo) {
						return false;
					}
				}
				if (filtro_operacion) {
					if (item.operacion !== filtro_operacion) {
						return false;
					}
				}
				return true;
			});
		},
		totales() {
			let total_monto = 0;

			if (this.lista_movimientos_filtrados.length > 0) {
				total_monto = this.lista_movimientos_filtrados.reduce((total, item) => {
					let monto = item.monto * (item.tipo == "E" ? -1 : 1);
					return parseFloat(total) + parseFloat(monto);
				}, 0);
			}

			if (total_monto < 0) {
				total_monto = "- S/ " + this.RedondearVista(Math.abs(total_monto), 2);
			} else {
				total_monto = "S/ " + this.RedondearVista(total_monto, 2);
			}

			return {
				total_monto: total_monto,
			};
		},
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
				// this.agencia_filtro = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
					// this.agencia_filtro = mi_agencia[0].id;
				} else {
					this.agencia_busqueda = null;
					// this.agencia_filtro = null;
				}
			}
		},
		agencia_busqueda() {
			this.FechaActual();
			this.FiltrarBancos();
		},
		// agencia_filtro() {
		// 	this.FiltrarCajas();
		// },
		// filtro_caja() {
		// 	this.FiltrarCajas();
		// },
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.ListarRecursos();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		getRowClass(rowData) {
			const index = this.lista_movimientos_general.findIndex(
				(item) => item.id === rowData.id
			);
			const current = rowData;
			const next = this.lista_movimientos_general[index + 1];

			if (!next || next.fecha !== current.fecha) {
				return "ultima-fila-grupo";
			}
			return "";
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
				"CREDITOS_REPORTES/CAJA_MOVIMIENTOS_BANCARIOS"
			);
		},
		async ListarRecursos() {
			// this.$inertia.get(route("rep.caj.movimientos_bancarios.listar_recursos"));
			// return false;

			await axios
				.get(route("rep.caj.movimientos_bancarios.listar_recursos"))
				.then((response) => {
					this.usuarios_cajas = response.data.usuarios;
					this.bancos = response.data.bancos;
				});
		},
		RedondearVista(value, decimal_places) {
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

		async BuscarGeneral() {
			const params = {
				agencia_id: this.agencia_busqueda,
				fecha_desde: this.fecha_desde,
				fecha_hasta: this.fecha_hasta,
			};

			// this.$inertia.get(
			// 	route("rep.caj.movimientos_bancarios.buscar_general"),
			// 	params
			// );
			// return false;

			Swal.fire({
				title: "BUSCANDO...",
				showConfirmButton: false,
				allowOutsideClick: false,
				willOpen: async () => {
					Swal.showLoading();

					return await axios
						.get(route("rep.caj.movimientos_bancarios.buscar_general"), {
							params,
						})
						.then((response) => {
							this.lista_movimientos_general = response.data.lista_movimientos;
							this.tab_index = 0;
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

		async BuscarDetallado(tipo, registro) {
			let params = {};
			if (tipo == "por_fecha") {
				params = {
					agencia_id: registro.agencia_id,
					fecha_desde: registro.fecha,
					fecha_hasta: registro.fecha,
				};
			} else {
				params = {
					agencia_id: this.agencia_busqueda,
					fecha_desde: this.fecha_desde,
					fecha_hasta: this.fecha_hasta,
					// filtro_caja: this.filtro_caja,
				};
			}

			// if (this.filtro_caja) {
			// 	params.usuarios_cajas = JSON.stringify(this.cajas_seleccionadas);
			// }

			// this.$inertia.get(route("rep.caj.movimientos_bancarios.buscar"), params);
			// return false;

			Swal.fire({
				title: "BUSCANDO...",
				showConfirmButton: false,
				allowOutsideClick: false,
				willOpen: async () => {
					Swal.showLoading();

					return await axios
						.get(route("rep.caj.movimientos_bancarios.buscar"), { params })
						.then((response) => {
							this.lista_movimientos_detallado =
								response.data.lista_movimientos;

							this.tab_index = 1;
							// this.totales = response.data.totales;
							// this.filtros_tabla["modo_desembolso"].value = null;
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
		// FiltrarCajas() {
		// 	this.cajas_filtradas = [];
		// 	this.cajas_seleccionadas = [];

		// 	if (this.filtro_caja) {
		// 		this.cajas_filtradas = [];
		// 		this.cajas_seleccionadas = [];

		// 		if (this.filtro_caja) {
		// 			this.cajas_filtradas = [];

		// 			if (this.mostrar_habilitados) {
		// 				this.cajas_filtradas = this.usuarios_cajas.filter(
		// 					(item) =>
		// 						item.agencia_id == this.agencia_filtro && item.habilitado == 1
		// 				);
		// 			} else {
		// 				this.cajas_filtradas = this.usuarios_cajas.filter(
		// 					(item) => item.agencia_id == this.agencia_filtro
		// 				);
		// 			}
		// 		}
		// 	}
		// },
		FiltrarBancos() {
			this.bancos_filtrados = this.bancos.filter(
				(item) => item.agencia_id == this.agencia_busqueda
			);
		},

		async Exportar(modo) {
			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("modo", modo);
			if (modo == "general") {
				data.append(
					"lista_movimientos",
					JSON.stringify(this.lista_movimientos_general)
				);
			} else if (modo == "detallado") {
				data.append(
					"lista_movimientos",
					JSON.stringify(this.lista_movimientos_filtrados)
				);
			}

			//this.$inertia.post(route("rep.caj.movimientos_bancarios.exportar"), data);
			//return false;

			Swal.fire({
				title: "EXPORTANDO...",
				text: "Espere por favor",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("rep.caj.movimientos_bancarios.exportar"), data)
						.then((response) => {
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
.slot-rpt-movimientos-bancarios {
	width: 70% !important;
	margin-left: 15% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

.tblMovimientosGenerales {
	max-height: 400px;
	overflow-y: auto;
	display: block;
	width: 100%;
}

.tblMovimientosGenerales table {
	width: 100%; /* Asegura que la tabla ocupe todo el espacio disponible */
	border-collapse: collapse;
}

.tblMovimientosGenerales thead th {
	position: sticky;
	top: 0;
	background-color: white;
	z-index: 2;
	text-align: center !important; /* Forzamos el centrado del texto */
}

.tblMovimientosGenerales td {
	text-align: center; /* Centrar los datos dentro de las celdas */
}

.tblMovimientosGenerales .p-column-header-content {
	display: block !important; /* Sobrescribimos display: -webkit-box; */
	text-align: center !important; /* Aseguramos que el texto esté centrado */
}
.ultima-fila-grupo {
	border-bottom: 2px solid var(--azulOscuroEmpresarial); /* Borde azul más grueso */
}

.subio {
	color: var(--azulOscuroEmpresarial);
}

.bajo {
	color: red;
}
@media only screen and (max-width: 900px) {
	.slot-rpt-movimientos-bancarios {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>


