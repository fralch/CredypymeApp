<template>
	<layout ref="layout">
		<div class="slot_body slot-cobranza-auxiliar" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MIS ' : '') +
							'COBRANZAS' +
							(modo == 'personal' ? ' - ' : ' POR ') +
							'AUXILIAR'
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
												v-for="(item, index) in agencias_permitidas"
												:key="index"
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
						<div class="form-row col-md-6" v-if="modo == 'completo'">
							<!-- <div class="form-row col-md-6"> -->
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									id="chbPorUsuario"
									v-model="filtro_caja_check"
								/>
								<label class="label-title" for="chbPorUsuario"
									>Buscar por CAJA</label
								>
								<label
									class="subrayado label-title ml-2"
									@click="VerUsuarios"
									v-if="cajas_seleccionadas.length > 0"
									>{{ cajas_seleccionadas.length }} usuario
									seleccionado(s)</label
								>
							</div>
						</div>
						<div class="card-title mb-1">
							LISTA DE RESULTADOS
							{{
								modo == "personal" ? " ( " + datos_sesion.usuario + " )" : ""
							}}
						</div>

						<fieldset class="p-0 pb-1 mb-1">
							<legend>
								<label class="label-title">FILTRAR RESULTADOS</label>
							</legend>
							<div class="form-row col-md-12 justify-content-md-center">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>FORMA DE PAGO</span
										>
									</div>
									<select
										class="form-control center"
										:disabled="lista_cobranzas.length == 0"
										v-model="filtros_tabla['forma_pago'].value"
									>
										>
										<option :value="null" selected>TODOS</option>
										<option value="ventanilla">VENTANILLA</option>
										<option value="cuenta_bancaria">CUENTA BANCARIA</option>
									</select>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>CON DESCUENTO</span
										>
									</div>
									<select
										class="form-control center"
										:disabled="lista_cobranzas.length == 0"
										v-model="filtros_tabla['con_descuento'].value"
									>
										>
										<option :value="null" selected>TODOS</option>
										<option value="SI">SI</option>
										<option value="NO">NO</option>
									</select>
								</div>
							</div>
						</fieldset>
						<DataTable
							:value="lista_cobranzas_filtrado"
							:scrollable="true"
							scrollDirection="both"
							:scrollHeight="String(windowHeigth * 0.38) + 'px'"
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
										data.capital == 0 ? "-" : "S/ " + roundTo(data.capital, 2)
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
										data.interes == 0 ? "-" : "S/ " + roundTo(data.interes, 2)
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
										data.redondeo == 0 ? "-" : "S/ " + roundTo(data.redondeo, 2)
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
									{{ data.moras == 0 ? "-" : "S/ " + roundTo(data.moras, 2) }}
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
											: "S/ " + roundTo(data.notificaciones, 2)
									}}
								</template>
							</Column>
							<Column
								field="comision_desembolso"
								header="COM_DES"
								:styles="{
									width: '100px',
									justifyContent: 'right',
								}"
							>
								<template #body="{ data }">
									{{
										data.comision_desembolso == 0
											? "-"
											: "S/ " + roundTo(data.comision_desembolso, 2)
									}}
								</template>
							</Column>
							<Column
								field="comision_riesgo"
								header="COM_RIE"
								:styles="{
									width: '100px',
									justifyContent: 'right',
								}"
							>
								<template #body="{ data }">
									{{
										data.comision_riesgo == 0
											? "-"
											: "S/ " + roundTo(data.comision_riesgo, 2)
									}}
								</template>
							</Column>
							<Column
								field="comision_domicilio"
								header="COM_DOM"
								:styles="{
									width: '100px',
									justifyContent: 'right',
								}"
							>
								<template #body="{ data }">
									{{
										data.comision_domicilio == 0
											? "-"
											: "S/ " + roundTo(data.comision_domicilio, 2)
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
											: "S/ " + roundTo(data.dscto_mora, 2)
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
											: "S/ " + roundTo(data.dscto_notificaciones, 2)
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
											: "S/ " + roundTo(data.dscto_interes, 2)
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
											: "S/ " + roundTo(data.total_pago, 2)
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
									<span
										v-if="data.documento"
										class="subrayado text-primary"
										@click="VerDocumento(data.ruta, data.documento)"
									>
										{{ data.comentario }}
									</span>
									<span v-else>
										{{ data.comentario == null ? "-" : data.comentario }}
									</span>
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
								field="boleta"
								header="BOLETA"
								:styles="{
									width: '70px',
									justifyContent: 'center',
								}"
								v-if="columna_boleta"
							>
								<template #body="{ data }">
									{{ data.boleta ? "SI" : "-" }}
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
										:footer="'S/ ' + roundTo(totales.total_capital, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_interes, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_redondeo, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_mora, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_notificaciones, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="
											'S/ ' + roundTo(totales.total_comision_desembolso, 2)
										"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_comision_riesgo, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="
											'S/ ' + roundTo(totales.total_comision_domicilio, 2)
										"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_dscto_mora, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="
											'S/ ' + roundTo(totales.total_dscto_notificaciones, 2)
										"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_dscto_interes, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(totales.total_pago, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:colspan="columna_boleta ? 4 : 3"
										footer=""
										:footerStyle="{
											width: columna_boleta ? '470px' : '400px',
											'text-align': 'right',
										}"
									/>
								</Row>
							</ColumnGroup>
							<template #empty> No hay COBRANZAS registradas.</template>
						</DataTable>

						<hr />
						<div class="text-right">
							<div class="btn-group dropleft m-1">
								<button
									type="button"
									class="btn btn-cancel dropdown-toggle"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false"
									title="Exportar"
									:disabled="lista_cobranzas_filtrado.length == 0"
								>
									<span class="text">POR AUXILIAR</span>
								</button>
								<div class="dropdown-menu">
									<a
										class="dropdown-item"
										href="#"
										@click.prevent="Exportar('por_resumen', 'PDF')"
										>Imprimir
										<span class="icon" style="float: right !important">
											<i class="fas fa-print"></i> </span
									></a>
									<a
										class="dropdown-item"
										href="#"
										@click.prevent="Exportar('por_resumen', 'XLSX')"
										>Exportar
										<span class="icon" style="float: right !important">
											<i class="fas fa-file-excel"></i>
										</span>
									</a>
								</div>
							</div>
							<div class="btn-group dropleft m-1">
								<button
									type="button"
									class="btn btn-cancel dropdown-toggle"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false"
									title="Exportar"
									:disabled="lista_cobranzas_filtrado.length == 0"
								>
									<span class="text">POR DÍA</span>
								</button>
								<div class="dropdown-menu">
									<a
										class="dropdown-item"
										href="#"
										@click.prevent="Exportar('por_dia', 'PDF')"
										>Imprimir
										<span class="icon" style="float: right !important">
											<i class="fas fa-print"></i> </span
									></a>
									<a
										class="dropdown-item"
										href="#"
										@click.prevent="Exportar('por_dia', 'XLSX')"
										>Exportar
										<span class="icon" style="float: right !important">
											<i class="fas fa-file-excel"></i>
										</span>
									</a>
								</div>
							</div>
							<div class="btn-group dropleft m-1">
								<button
									type="button"
									class="btn btn-cancel dropdown-toggle"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false"
									title="Exportar"
									:disabled="lista_cobranzas_filtrado.length == 0"
								>
									<span class="text">POR OPERACIÓN</span>
								</button>
								<div class="dropdown-menu">
									<a
										class="dropdown-item"
										href="#"
										@click.prevent="Exportar('por_operacion', 'PDF')"
										>Imprimir
										<span class="icon" style="float: right !important">
											<i class="fas fa-print"></i> </span
									></a>
									<a
										class="dropdown-item"
										href="#"
										@click.prevent="Exportar('por_operacion', 'XLSX')"
										>Exportar
										<span class="icon" style="float: right !important">
											<i class="fas fa-file-excel"></i>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="mdlDocumentoFoto" class="modal">
					<!-- Modal content -->
					<div class="modal-content w-50 mdlDocumentoFoto">
						<div class="content" style="display: block">
							<div class="card">
								<headerCloseModal
									:titulo_modal="'DOCUMENTO'"
									:nombre_modal="'mdlDocumentoFoto'"
								>
								</headerCloseModal>

								<div class="card-title">FOTO</div>

								<div class="card-body card-block">
									<div class="form-row justify-content-md-center">
										<div class="form-group justify-content-md-center">
											<div
												id="vizualizar"
												style="
													border: 1px solid #ffff;
													width: 660px;
													height: 460px;
												"
											></div>
										</div>
									</div>

									<div class="text-center">
										<button
											class="btn btn-cancel btn-icon-split"
											@click="Descargar"
										>
											<span class="icon text-white">
												<i class="fas fa-download"></i>
											</span>
											<span class="text font-size-layout">Descargar</span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<FiltroUsuario
					ref="filtro_usuario"
					@usuarios-seleccionados="RecibirUsuarios"
					@agencia-seleccionada="RecibirAgencia"
					@habilitado-seleccionado="RecibirHabilitado"
					@cerrar-modal="CerrarModal"
				></FiltroUsuario>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import FiltroUsuario from "@/Pages/Creditos/Components/filtro_usuario.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,

		DataTable,
		Column,
		ColumnGroup,
		Row,
		FiltroUsuario,
	},
	props: { modo: String, usuarios_caja: Array },

	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			agencias_permitidas: [],
			agencia_busqueda: 0,
			agencia_seleccionada: 0,
			agencia_filtro: 0,

			windowWidth: window.innerWidth,

			fecha_desde: null,
			fecha_hasta: null,

			filtro_caja_check: false,
			filtro_caja_check_personal: false,
			mostrar_habilitados: true,

			cajas_seleccionadas: [],

			lista_cobranzas: [],

			filtros_tabla: {
				forma_pago: { value: null },
				con_descuento: { value: null },
			},
			nombre_foto: null,
			source_foto: null,
		};
	},
	computed: {
		lista_cobranzas_filtrado() {
			const filtro_forma_pago = this.filtros_tabla["forma_pago"].value;
			const filtro_con_descuento = this.filtros_tabla["con_descuento"].value;

			return this.lista_cobranzas.filter((item) => {
				if (filtro_forma_pago) {
					if (filtro_forma_pago == "ventanilla") {
						if (item.pago_banco !== 0) {
							return false;
						}
					}
					if (filtro_forma_pago == "cuenta_bancaria") {
						if (item.pago_banco !== 1) {
							return false;
						}
					}
				}
				if (filtro_con_descuento) {
					if (filtro_con_descuento == "SI") {
						if (
							item.dscto_mora === 0 &&
							item.dscto_notificaciones === 0 &&
							item.dscto_interes === 0
						) {
							return false;
						}
					}
					if (filtro_con_descuento == "NO") {
						if (
							item.dscto_mora !== 0 ||
							item.dscto_notificaciones !== 0 ||
							item.dscto_interes !== 0
						) {
							return false;
						}
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
			let total_com_desembolso = 0;
			let total_com_riesgo = 0;
			let total_com_domicilio = 0;
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
				total_com_desembolso = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.comision_desembolso);
					},
					0
				);
				total_com_riesgo = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.comision_riesgo);
					},
					0
				);
				total_com_domicilio = this.lista_cobranzas_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.comision_domicilio);
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
				total_com_desembolso: total_com_desembolso,
				total_com_riesgo: total_com_riesgo,
				total_com_domicilio: total_com_domicilio,
				total_dscto_mora: total_dscto_mora,
				total_dscto_notificaciones: total_dscto_notificaciones,
				total_dscto_interes: total_dscto_interes,
				total_pago: total_pago,
			};
		},
		columna_boleta() {
			let agencia_sesion = this.$page.props.user_session.id_agencia;

			if (agencia_sesion == 5) {
				return true;
			} else {
				return false;
			}
		},
		datos_sesion() {
			return this.$inertia.page.props.user_session;
		},
		agencias() {
			return this.$inertia.page.props.application.agencias;
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
		},

		filtro_caja_check() {
			const self = this;

			this.cajas_seleccionadas = [];

			if (this.filtro_caja_check) {
				let filtro_usuarios = this.$refs.filtro_usuario;
				async function EnviarDatos() {
					filtro_usuarios.usuarios = self.usuarios_caja;
					filtro_usuarios.mostrar_habilitados = true;
					filtro_usuarios.modo_agencias = true;

					filtro_usuarios.agencias_permitidas = self.agencias_permitidas;
					filtro_usuarios.agencia_seleccionada = self.agencia_busqueda;
				}

				EnviarDatos().then(() => {
					$("#filtro_usuario").css("display", "block");
					filtro_usuarios.FiltrarUsuarios();
				});
			}
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		if (this.modo == "personal") {
			this.cajas_seleccionadas = [];
			this.cajas_seleccionadas.push(this.$page.props.user_session.usuario_dni);

			this.filtro_caja_check_personal = true;
		}

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
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

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		VerUsuarios() {
			const self = this;
			let filtro_usuarios = this.$refs.filtro_usuario;
			async function EnviarDatos() {
				filtro_usuarios.modo_agencias = true;
				filtro_usuarios.usuarios = self.usuarios_caja;
				filtro_usuarios.agencias_permitidas = self.agencias_permitidas;
				filtro_usuarios.agencia_seleccionada = self.agencia_filtro;
				filtro_usuarios.mostrar_habilitados = self.mostrar_habilitados;
			}

			EnviarDatos().then(() => {
				$("#filtro_usuario").css("display", "block");
				filtro_usuarios.usuarios_seleccionados = self.cajas_seleccionadas;
			});
		},
		RecibirUsuarios(usuarios) {
			this.cajas_seleccionadas = usuarios;
			this.filtro_caja_check = true;
		},

		RecibirAgencia(agencia) {
			this.agencia_filtro = agencia;
		},
		RecibirHabilitado(habilitado) {
			this.mostrar_habilitados = habilitado;
		},
		CerrarModal() {
			if (this.cajas_seleccionadas.length == 0) {
				this.filtro_caja_check = false;
			}
		},
		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CAJA_MIS_COBRANZAS_AUXILIAR"
				);
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CAJA_COBRANZAS_AUXILIAR"
				);
			} else {
				this.agencias_permitidas = [];
			}
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
		periodo_medicion(value) {
			if (value == "DIARIO") {
				return "(DÍAS)";
			} else if (value == "SEMANAL") {
				return "(SEMANAS)";
			} else if (value == "QUINCENAL") {
				return "(QUINCENAS)";
			} else if (value == "MENSUAL") {
				return "(MESES)";
			}
			return "(DÍAS)";
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

			if (this.filtro_caja_check) {
				params.agencia_filtro = this.agencia_filtro;
				params.usuarios_cajas = JSON.stringify(this.cajas_seleccionadas);
				params.filtro_caja_check = true;
			}

			if (this.filtro_caja_check_personal) {
				params.agencia_filtro = this.usuarios_caja[0].agencia_id;
				params.usuarios_cajas = JSON.stringify(this.cajas_seleccionadas);
				params.filtro_caja_check = true;
			}

			// this.$inertia.get(
			// 	route("rep.caj.cobranzas_auxiliar.buscar", { modo: "por_auxiliar" }),
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
						.get(
							route("rep.caj.cobranzas_auxiliar.buscar", {
								modo: "por_auxiliar",
							}),
							{ params }
						)
						.then(async (response) => {
							this.lista_cobranzas = response.data.lista_cobranzas;
							this.agencia_seleccionada = response.data.agencia_seleccionada;

							// // Espera un poco para asegurar cierre suave del primer modal
							await Swal.close();
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
		async Exportar(modo, tipo) {
			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append(
				"lista_cobranzas",
				JSON.stringify(this.lista_cobranzas_filtrado)
			);
			data.append("totales", JSON.stringify(this.totales));
			if (modo == "por_operacion") {
				data.append("modo", "por_auxiliar_operacion");
			} else if (modo == "por_dia") {
				data.append("modo", "por_auxiliar_dia");
			} else if (modo == "por_resumen") {
				data.append("modo", "por_auxiliar_resumen");
			}

			data.append("tipo", tipo);
			let titulo = "";

			if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			} else if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			}

			// this.$inertia.post(route("rep.caj.cobranzas_auxiliar.exportar"), data);
			// return false;

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("rep.caj.cobranzas_auxiliar.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								const path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								link.click();
							} else if (tipo == "PDF") {
								const origin = window.location.origin;
								const path_pdf = response.data.path_pdf;

								// Crear un IFrame
								const iframe = document.createElement("iframe");
								// Oculto el iframe
								iframe.style.display = "none";
								// Defino el source
								iframe.src = origin + path_pdf;
								// Añadir el Iframe a la vista
								document.body.appendChild(iframe);

								iframe.contentWindow.focus(); // Enfoca
								iframe.contentWindow.print(); // Imprime
							}

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
		VerDocumento(ruta, documento) {
			this.title_modal = "FOTO PERMISO";

			let img_prev = $("#vizualizar img");
			img_prev.remove();

			let preview = document.getElementById("vizualizar"),
				image = document.createElement("img");

			image.src = ruta;
			this.nombre_foto = documento;
			this.source_foto = image.src;

			image.style.width = "100%";
			image.style.height = "100%";
			image.style.border = "1px solid #ffff";

			preview.innerHTML = "";
			preview.append(image);
			$("#mdlDocumentoFoto").css("display", "block");
		},

		async Descargar() {
			let self = this;
			let source = this.source_foto;

			await axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = self.nombre_foto;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},
	},
};
</script>

<style lang="css">
.slot-cobranza-auxiliar {
	width: 70% !important;
	margin-left: 15% !important;
}

.subrayado {
	color: blue !important;
	text-decoration: underline !important;
	cursor: pointer !important;
}
.mdlDocumentoFoto {
	margin-top: 1% !important;
}

@media (max-width: 900px) {
	.slot-cobranza-auxiliar {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
