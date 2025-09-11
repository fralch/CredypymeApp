<template>
	<layout ref="layout">
		<div class="slot_body slot-cobranza-negocio" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'COBRANZAS EN NEGOCIO'"></headerClose>
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
											class="form-control center bolder"
											v-model="agencia_seleccionada"
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
											v-if="agencia_seleccionada != 0"
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
									v-if="agencia_seleccionada != 0"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="input-group col-md-5 mb-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbPorAsesor"
											v-model="por_asesor"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbPorAsesor"
										style="font-size: 13px"
									>
										ASESOR
									</label>
								</div>

								<select
									class="form-control center"
									v-model="usuario_seleccionado"
									:disabled="!por_asesor"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in usuarios_filtrados"
										:key="index"
										:value="item.dni"
									>
										{{ item.usuario }}
									</option>
								</select>
								<div class="input-group-append">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbMostrarHabilitados"
											v-model="mostrar_habilitados"
											:disabled="!por_asesor"
											@change="FiltrarUsuarios"
										/>
										<label
											class="m-0 ml-1"
											for="chbMostrarHabilitados"
											v-if="windowWidth >= 900"
										>
											Habilitados</label
										>

										<label
											class="m-0 ml-1"
											for="chbMostrarHabilitados"
											v-if="windowWidth < 900"
										>
											Hab.</label
										>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="card-title">LISTA DE RESULTADOS</div>
								<div class="card-block">
									<ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item">
											<a
												class="nav-link active tab-title"
												id="detallado-tab"
												data-toggle="tab"
												href="#detallado"
												role="tab"
												aria-controls="detallado"
												aria-selected="true"
												>DETALLADO</a
											>
										</li>
										<li class="nav-item">
											<a
												class="nav-link tab-title"
												id="recibo-tab"
												data-toggle="tab"
												href="#recibo"
												role="tab"
												aria-controls="recibo"
												aria-selected="false"
												>POR RECIBO</a
											>
										</li>
									</ul>
									<div class="tab-content" id="myTabContent">
										<div
											class="tab-pane fade show active"
											id="detallado"
											role="tabpanel"
											aria-labelledby="detallado-tab"
										>
											<DataTable
												:value="lista_cobranzas"
												:row-class="rowClass"
												:scrollable="true"
												scrollDirection="both"
												scrollHeight="350px"
												:paginator="true"
												:rows="100"
												selectionMode="single"
												paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
												currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
											>
												<Column
													field="numero"
													header="N°"
													:styles="{ width: '40px', justifyContent: 'center' }"
												>
													<template #body="{ data }">
														{{ data.index + 1 }}
													</template>
												</Column>
												<Column
													field="fecha_pago"
													header="FECHA_REGISTRO"
													:styles="{ width: '120px', justifyContent: 'center' }"
												>
												</Column>
												<Column
													field="cliente"
													header="CLIENTE"
													:styles="{ width: '250px' }"
												>
												</Column>
												<Column
													field="usuario_asesor"
													header="ASESOR"
													:styles="{
														width: '100px',
														justifyContent: 'center',
													}"
												>
												</Column>
												<Column
													field="numero_recibo"
													header="N°_RECIBO"
													:styles="{
														width: '100px',
														justifyContent: 'center',
													}"
												>
												</Column>
												<Column
													field="numero_cuota"
													header="N°_CUOTA"
													:styles="{
														width: '100px',
														justifyContent: 'center',
													}"
												>
												</Column>
												<Column
													field="total_pago"
													header="TOTAL_PAGO"
													:styles="{
														width: '100px',
														justifyContent: 'flex-end',
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
													field="pago_cuotas"
													header="EN_CUOTAS"
													:styles="{
														width: '100px',
														justifyContent: 'flex-end',
													}"
												>
													<template #body="{ data }">
														{{
															data.pago_cuotas == 0
																? "-"
																: "S/ " + roundTo(data.pago_cuotas, 2)
														}}
													</template>
												</Column>
												<Column
													field="pago_moras"
													header="EN_MORAS"
													:styles="{
														width: '100px',
														justifyContent: 'flex-end',
													}"
												>
													<template #body="{ data }">
														{{
															data.pago_moras == 0
																? "-"
																: "S/ " + roundTo(data.pago_moras, 2)
														}}
													</template>
												</Column>
												<Column
													field="en_notificaciones"
													header="EN_NOTIF."
													:styles="{
														width: '100px',
														justifyContent: 'flex-end',
													}"
												>
													<template #body="{ data }">
														{{
															data.pago_notificaciones == 0
																? "-"
																: "S/ " + roundTo(data.pago_notificaciones, 2)
														}}
													</template>
												</Column>
												<!-- <Column
													field="dscto_mora"
													header="DSCTO_MORAS"
													:styles="{
														width: '100px',
														justifyContent: 'flex-end',
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
														justifyContent: 'flex-end',
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
													header="DSCTO_INTERES"
													:styles="{
														width: '100px',
														justifyContent: 'flex-end',
													}"
												>
													<template #body="{ data }">
														{{
															data.dscto_interes == 0
																? "-"
																: "S/ " + roundTo(data.dscto_interes, 2)
														}}
													</template>
												</Column> -->
												<Column
													field="usuario_cobrador"
													header="COBRADOR"
													:styles="{
														width: '100px',
														justifyContent: 'center',
													}"
												>
													<template #body="{ data }">
														{{
															data.usuario_cobrador == null
																? "-"
																: data.usuario_cobrador
														}}
													</template>
												</Column>

												<Column
													field="usuario_caja"
													header="CAJA"
													:styles="{
														width: '100px',
														justifyContent: 'center',
													}"
												>
												</Column>
												<ColumnGroup type="footer">
													<Row>
														<Column
															footer="TOTALES:"
															:footerStyle="{
																width: '710px',
																'text-align': 'right',
															}"
														/>
														<Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' + roundTo(totales.total_pago, 2)
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/>
														<Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' + roundTo(totales.total_cuotas, 2)
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/>
														<Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' + roundTo(totales.total_moras, 2)
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/>
														<Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' +
																	  roundTo(totales.total_notificaciones, 2)
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/>
														<!-- <Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' +
																	  roundTo(totales.total_dscto_moras, 2)
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/>
														<Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' +
																	  roundTo(
																			totales.total_dscto_notificaciones,
																			2
																	  )
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/>
														<Column
															:footer="
																Object.keys(totales).length === 0
																	? '-'
																	: 'S/ ' +
																	  roundTo(totales.total_dscto_interes, 2)
															"
															:footerStyle="{
																width: '100px',
																'text-align': 'right',
															}"
														/> -->
														<Column footer="" :colspan="3" />
													</Row>
												</ColumnGroup>
											</DataTable>
											<hr />
											<div class="text-right">
												<button
													class="btn btn-cancel btn-icon-split"
													title="Exportar"
													@click="Exportar()"
													:disabled="lista_cobranzas.length == 0"
												>
													<span class="icon text-white">
														<i class="fas fa-file-excel"></i>
													</span>
													<span class="text">EXPORTAR</span>
												</button>
											</div>
										</div>

										<div
											class="tab-pane fade"
											id="recibo"
											role="tabpanel"
											aria-labelledby="recibo-tab"
										>
											<fieldset
												class="form-group col-md-10 offset-1 mt-2"
												:style="
													windowWidth >= 900
														? ''
														: 'min-width: 100% !important; margin-left: 0% !important'
												"
											>
												<div class="form-row">
													<div class="input-group col-md-4 mb-1 mt-1 col-7">
														<div class="input-group-prepend">
															<span
																class="input-group-text prepend-title"
																v-if="windowWidth >= 900"
																>AGENCIA</span
															>
															<span
																class="input-group-text prepend-title"
																v-if="windowWidth < 900"
																>AG.</span
															>
														</div>
														<select
															class="form-control center"
															v-model="agencia_seleccionada_recibo"
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
													<div class="input-group col-md-4 mb-1 mt-1 col-5">
														<div class="input-group-prepend">
															<label
																class="input-group-text prepend-title"
																v-if="windowWidth >= 900"
															>
																NÚMERO DE RECIBO
															</label>
															<label
																class="input-group-text prepend-title"
																v-if="windowWidth < 900"
															>
																#
															</label>
														</div>
														<input
															class="form-control center"
															type="number"
															name="numero_recibo"
															autocomplete="false"
															v-model="frmRecibo.numero_recibo"
															@focus="hidenav()"
															@blur="shownav()"
														/>
													</div>

													<div
														class="col-md-2 mb-1"
														:style="
															windowWidth >= 900
																? ''
																: 'text-align: right !important'
														"
													>
														<button
															class="btn btn-action btn-icon-split mt-1"
															title="Buscar"
															@click="BuscarRecibo"
														>
															<span
																class="icon text-white"
																style="font-size: 10px"
															>
																<i class="fas fa-search"></i>
															</span>
														</button>
													</div>
												</div>
												<fieldset
													class="form-group col-md-12"
													style="min-width: 100%"
												>
													<legend>
														<label class="label-title">DATOS DEL CRÉDITO</label>
													</legend>

													<div
														class="form-group offset-2"
														:style="
															windowWidth >= 900
																? ''
																: 'margin-left: 0% !important'
														"
													>
														<div class="input-group col-md-10">
															<div class="input-group-prepend">
																<label class="input-group-text prepend-title">
																	CLIENTE
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.cliente"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>

														<div class="input-group col-md-7 mt-2">
															<div class="input-group-prepend">
																<label class="input-group-text prepend-title">
																	TIPO DE CRÉDITO
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.tipo_credito"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>

														<div class="input-group col-md-7 mt-2">
															<div class="input-group-prepend">
																<label class="input-group-text prepend-title">
																	ASESOR
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.usuario_asesor"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>
													</div>
												</fieldset>
												<fieldset
													class="form-group col-md-12"
													style="min-width: 100%"
												>
													<legend>
														<label class="label-title"
															>DATOS DE LA COBRANZA</label
														>
													</legend>

													<div
														class="form-group row offset-2"
														:style="
															windowWidth >= 900
																? ''
																: 'margin-left: 0% !important'
														"
													>
														<div
															class="input-group col-md-5 col-5"
															:style="
																windowWidth >= 900
																	? ''
																	: 'padding-right: 0.2rem !important'
															"
														>
															<div class="input-group-prepend">
																<label
																	class="input-group-text prepend-title"
																	v-if="windowWidth >= 900"
																>
																	NÚMERO DE CUOTA
																</label>
																<label
																	class="input-group-text prepend-title"
																	v-if="windowWidth < 900"
																>
																	CUOTA
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.numero_cuota"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>

														<div
															class="input-group col-md-8 mt-2 col-7"
															:style="
																windowWidth >= 900
																	? ''
																	: 'margin-top: 0rem !important; padding-left: 0.2rem !important'
															"
														>
															<div class="input-group-prepend">
																<label
																	class="input-group-text prepend-title"
																	v-if="windowWidth >= 900"
																>
																	FECHA DE REGISTRO
																</label>
																<label
																	class="input-group-text prepend-title"
																	v-if="windowWidth < 900"
																>
																	FECHA
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.fecha_registro"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>

														<div class="input-group col-md-7 mt-2">
															<div class="input-group-prepend">
																<label class="input-group-text prepend-title">
																	CAJA
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.usuario_caja"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>
														<div class="input-group col-md-7 mt-2">
															<div class="input-group-prepend">
																<label class="input-group-text prepend-title">
																	USUARIO COBRANZA
																</label>
															</div>
															<input
																class="form-control center"
																type="text"
																style="font-size: 13px"
																:value="lista_datos_recibo.usuario_cobranza"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>
														<div class="input-group col-md-7 mt-2">
															<div class="input-group-prepend">
																<label class="input-group-text prepend-title">
																	TOTAL PAGADO
																</label>
																<label class="input-group-text prepend-title"
																	>S/</label
																>
															</div>
															<input
																class="form-control text-right"
																type="text"
																style="font-size: 13px"
																:value="
																	roundTo(lista_datos_recibo.total_pagado, 2)
																"
																readonly
																@focus="hidenav()"
																@blur="shownav()"
															/>
														</div>

														<div class="form-group row mt-2">
															<div
																class="input-group col-md-8 offset-1"
																:style="
																	windowWidth >= 900
																		? ''
																		: 'margin-left: 4% !important ; margin-right: 4% !important'
																"
															>
																<div class="input-group-prepend">
																	<label class="input-group-text prepend-title">
																		EN CUOTAS
																	</label>
																	<label class="input-group-text prepend-title"
																		>S/</label
																	>
																</div>
																<input
																	class="form-control text-right"
																	type="text"
																	style="font-size: 13px"
																	:value="lista_datos_recibo.pago_cuotas"
																	readonly
																	@focus="hidenav()"
																	@blur="shownav()"
																/>
																<div class="input-group-prepend">
																	<label class="input-group-text prepend-title">
																		DSCTO INTERÉS
																	</label>
																	<label class="input-group-text prepend-title"
																		>S/</label
																	>
																</div>
																<input
																	class="form-control text-right"
																	type="text"
																	style="font-size: 13px"
																	:value="lista_datos_recibo.dscto_interes"
																	readonly
																	@focus="hidenav()"
																	@blur="shownav()"
																/>
															</div>
															<div
																class="input-group col-md-8 offset-1 mt-2"
																:style="
																	windowWidth >= 900
																		? ''
																		: 'margin-left: 4% !important ; margin-right: 4% !important'
																"
															>
																<div class="input-group-prepend">
																	<label class="input-group-text prepend-title">
																		EN MORAS
																	</label>
																	<label class="input-group-text prepend-title"
																		>S/</label
																	>
																</div>
																<input
																	class="form-control text-right"
																	type="text"
																	style="font-size: 13px"
																	:value="lista_datos_recibo.pago_moras"
																	readonly
																	@focus="hidenav()"
																	@blur="shownav()"
																/>
																<div class="input-group-prepend">
																	<label class="input-group-text prepend-title">
																		DSCTO MORAS
																	</label>
																	<label class="input-group-text prepend-title"
																		>S/</label
																	>
																</div>
																<input
																	class="form-control text-right"
																	type="text"
																	style="font-size: 13px"
																	:value="lista_datos_recibo.dscto_mora"
																	readonly
																	@focus="hidenav()"
																	@blur="shownav()"
																/>
															</div>
															<div
																class="input-group col-md-8 offset-1 mt-2"
																:style="
																	windowWidth >= 900
																		? ''
																		: 'margin-left: 4% !important ; margin-right: 4% !important'
																"
															>
																<div class="input-group-prepend">
																	<label class="input-group-text prepend-title">
																		EN NOTIFICACIONES
																	</label>
																	<label class="input-group-text prepend-title"
																		>S/</label
																	>
																</div>
																<input
																	class="form-control text-right"
																	type="text"
																	style="font-size: 13px"
																	:value="
																		lista_datos_recibo.pago_notificaciones
																	"
																	readonly
																	@focus="hidenav()"
																	@blur="shownav()"
																/>
																<div class="input-group-prepend">
																	<label class="input-group-text prepend-title">
																		DSCTO NOTIFICACIONES
																	</label>
																	<label class="input-group-text prepend-title"
																		>S/</label
																	>
																</div>
																<input
																	class="form-control text-right"
																	type="text"
																	style="font-size: 13px"
																	:value="
																		lista_datos_recibo.dscto_notificaciones
																	"
																	readonly
																	@focus="hidenav()"
																	@blur="shownav()"
																/>
															</div>
														</div>
													</div>
												</fieldset>
											</fieldset>
										</div>
									</div>
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
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

import { required } from "vuelidate/lib/validators";

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
			agencia_seleccionada: 0,
			agencia_seleccionada_recibo: 0,

			windowWidth: window.innerWidth,

			fecha_desde: null,
			fecha_hasta: null,

			lista_cobranzas: [],
			lista_datos_recibo: [],
			totales: {},

			frmRecibo: {
				numero_recibo: null,
			},

			usuarios_filtrados: [],
			usuario_seleccionado: 0,

			por_asesor: false,
			mostrar_habilitados: true,
		};
	},
	validations: {
		frmRecibo: { numero_recibo: { required } },
	},
	watch: {
		por_asesor() {
			this.usuario_seleccionado = 0;
		},
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
				this.agencia_seleccionada_recibo = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
					this.agencia_seleccionada_recibo = mi_agencia[0].id;
				} else {
					this.agencia_seleccionada = null;
					this.agencia_seleccionada_recibo = mi_agencia[0].id;
				}
			}
		},
		agencia_seleccionada() {
			this.FiltrarUsuarios();
			this.FechaActual();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		FiltrarUsuarios() {
			this.usuarios_filtrados = [];
			this.usuarios_seleccionados = [];
			this.usuario_seleccionado = 0;

			if (this.mostrar_habilitados) {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_seleccionada && item.habilitado == 1
				);
			} else {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			}
		},

		async FechaActual() {
			if (this.agencia_seleccionada == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_seleccionada
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_COBRANZAS_NEGOCIO"
			);
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
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

		rowClass(data) {
			let index = data.index;
			return index % 2 == 0 ? "verde-claro" : "";
		},

		Buscar() {
			let self = this;

			let resultado = this.$refs.layout.ValidarMesesBusqueda(
				this.fecha_desde,
				this.fecha_hasta,
				3
			);

			if (resultado == "NO_VALIDADO") {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Sólo se puede realizar busquedas de 3 meses de antiguedad. Gracias",
				});

				return false;
			}

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("modo", "en_negocio");
			data.append("por_asesor", this.por_asesor);

			if (this.por_asesor) {
				data.append("asesor", this.usuario_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					// this.$inertia.post(route("rep.caj.cobranzas_negocio.buscar"), data);
					// return false;

					Swal.showLoading();
					axios
						.post(
							route("rep.caj.cobranzas_negocio.buscar", { modo: "en_negocio" }),
							data
						)
						.then(function (response) {
							if (response.data.lista_cobranzas.length == 0) {
								self.lista_cobranzas = [];
								self.totales = {};
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_cobranzas = response.data.lista_cobranzas;
								self.totales = response.data.totales;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
									timer: 1200,
									showConfirmButton: false,
								});
							}
						});
				},
			});
		},

		BuscarRecibo() {
			let self = this;

			if (this.$v.frmRecibo.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe ingresar un número de recibo",
				});
				return false;
			} else {
				let data = new FormData();

				data.append("numero_recibo", this.frmRecibo.numero_recibo);
				data.append("agencia_id", this.agencia_seleccionada_recibo);

				data.append("modo", "por_recibo");
				Swal.fire({
					title: "BUSCANDO",
					text: "Espere porfavor...",
					allowOutsideClick: false,
					didOpen: () => {
						// this.$inertia.post(route("rep.caj.cobranzas_negocio.buscar"), data);
						// return false;

						Swal.showLoading();
						axios
							.post(
								route("rep.caj.cobranzas_negocio.buscar", {
									modo: "por_recibo",
								}),
								data
							)
							.then(function (response) {
								if (response.data.resultado == null) {
									self.lista_datos_recibo = [];
									return Swal.fire({
										icon: "info",
										title: "¡Ups!",
										text: "No se encontraron datos",
										allowOutsideClick: true,
									});
								} else {
									self.lista_datos_recibo = response.data.resultado;
									return Swal.fire({
										icon: "success",
										title: "¡Listo!",
										timer: 1200,
										showConfirmButton: false,
									});
								}
							});
					},
				});
			}
		},
		Exportar() {
			let data = new FormData();
			data.append("lista_cobranzas", JSON.stringify(this.lista_cobranzas));
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("modo", "por_negocio");

			// this.$inertia.post(route("rep.caj.cobranzas_negocio.exportar"), data);
			// return false;

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("rep.caj.cobranzas_negocio.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptCobranzaNegocio.xlsx";
							link.click();
							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
								timer: 2000,
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
.slot-cobranza-negocio {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-cobranza-negocio {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
