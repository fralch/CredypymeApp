<template>
	<layout ref="layout">
		<div class="slot_body slot-seguimiento-facturados" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="'SEGUIMIENTO DE PAGOS EN CRÉDITOS FACTURADOS'"
					></headerClose>
					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<fieldset class="form-row col-md-6">
								<legend>
									<label class="label-title"
										>SELECCIONE LA FECHA DE FACTURACIÓN</label
									>
								</legend>

								<div class="input-group col-md-6 col-6">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
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
								<div
									class="input-group col-md-6 col-6 justify-content-md-center"
								>
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">FECHA</span>
									</div>

									<date-picker
										v-model="fecha_facturacion"
										class="center"
										type="month"
										:editable="false"
										value-type="format"
										placeholder="Seleccione un mes"
										style="width: 150px !important"
									></date-picker>
								</div>
							</fieldset>

							<fieldset class="form-row col-md-5 ml-2 col-10">
								<legend>
									<label class="label-title"
										>SELECCIONE LAS FECHAS DE LOS PAGOS</label
									>
								</legend>

								<div class="input-group col-md-6 col-6">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">DESDE</span>
									</div>
									<input
										type="date"
										class="form-control center bolder"
										v-model="datos_fecha.fecha_desde"
										:disabled="fecha_facturacion == null"
										style="font-size: 13.5px"
									/>
								</div>
								<div class="input-group col-md-6 col-6">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">HASTA</span>
									</div>
									<input
										type="date"
										class="form-control center bolder"
										v-model="datos_fecha.fecha_hasta"
										:disabled="fecha_facturacion == null"
										style="font-size: 13.5px"
									/>
								</div>
							</fieldset>
							<div class="mt-3 ml-3">
								<button
									class="btn btn-action btn-icon-split"
									title="BUSCAR PAGOS"
									@click="Buscar"
									:disabled="fecha_facturacion == null"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>
						<div class="form-row">
							<div class="input-group col-md-3 col-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title"
										>NUEVA EMPRESA</span
									>
								</div>
								<select
									class="form-control center"
									v-model="filtros_tabla['nueva_empresa'].value"
									:disabled="lista_facturados.length == 0"
								>
									<option :value="null" selected>TODOS</option>
									<option :value="1">SI</option>
									<option :value="0">NO</option>
								</select>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<DataTable
							:value="lista_facturados"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							:paginator="true"
							:rows="20"
							:filters="filtros_tabla"
							selectionMode="single"
							@row-dblclick="VerHistorial"
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
								field="nueva_empresa"
								header="NUEVA_EMPRESA"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.nueva_empresa ? "SI" : "NO" }}
								</template>
							</Column>
							<Column
								field="fecha_desembolso"
								header="FECHA_DESEMBOLSO"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.fecha_desembolso }}
								</template>
							</Column>
							<Column
								field="codigo_cliente"
								header="CODIGO_CLIENTE"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.codigo_cliente }}
								</template>
							</Column>
							<Column
								field="cliente"
								header="RAZON_SOCIAL"
								:styles="{ width: '300px' }"
							>
								<template #body="{ data }">
									{{ data.cliente }}
								</template>
							</Column>
							<Column
								field="boleta"
								header="N°_DOCUMENTO"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.numero_documento }}
								</template>
							</Column>
							<Column
								field="monto"
								header="CAPITAL_TOTAL"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.monto, 2) }}
								</template>
							</Column>
							<Column
								field="interes"
								header="INTERES_TOTAL"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.interes_total, 2) }}
								</template>
							</Column>
							<Column
								field="capital_pagado"
								header="CAPITAL"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.capital, 2) }}
								</template>
							</Column>
							<Column
								field="interes_pagado"
								header="INTERES"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.interes, 2) }}
								</template>
							</Column>
							<Column
								field="mora_pagado"
								header="MORA"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.moras, 2) }}
								</template>
							</Column>
							<Column
								field="notificaciones_pagado"
								header="NOTIFICACIONES"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.notificaciones, 2) }}
								</template>
							</Column>
							<Column
								field="descuento_interes"
								header="DSCTO_INTERES"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.dscto_interes, 2) }}
								</template>
							</Column>
							<Column
								field="descuento_mora"
								header="DSCTO_MORA"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.dscto_mora, 2) }}
								</template>
							</Column>
							<Column
								field="descuento_notificaciones"
								header="DSCTO_NOTIF"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.dscto_notificaciones, 2) }}
								</template>
							</Column>
							<Column
								field="estado"
								header="ESTADO"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.estado }}
								</template>
							</Column>
							<Column
								field="fecha_cancelado"
								header="ULTIMO_PAGO_(CUOTA)"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{
										data.ultimo_pago_cuota == null
											? "-"
											: data.ultimo_pago_cuota
									}}
								</template>
							</Column>
							<Column
								field="fecha_cancelado"
								header="ULTIMO_PAGO_(GENERAL)"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{
										data.ultimo_pago_general == null
											? "-"
											: data.ultimo_pago_general
									}}
								</template>
							</Column>
							<Column
								field="fecha_cancelado"
								header="FECHA_CANCELADO"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{
										data.fecha_hora_cancelado == null
											? "-"
											: data.fecha_hora_cancelado
									}}
								</template>
							</Column>
							<ColumnGroup type="footer">
								<Row>
									<Column
										footer="TOTALES:"
										:footerStyle="{ width: '850px', 'text-align': 'right' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_monto, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_interes, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_capital_p, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_interes_p, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_mora_p, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_notificaciones_p, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_dscto_interes, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_dscto_mora, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_dscto_notif, 2)
										"
										:footerStyle="{ width: '120px', 'text-align': 'center' }"
									/>
									<Column
										:footer="lista_facturados.length + ' registro(s)'"
										:footerStyle="{ width: '570px', 'text-align': 'center' }"
									/>
								</Row>
							</ColumnGroup>
						</DataTable>

						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="EXPORTAR REPORTE"
								@click="Exportar()"
								:disabled="lista_facturados.length == 0"
							>
								<span class="icon text-white">
									<i class="pi pi-file-excel"></i>
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
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import mdlHistorialCrediticio from "@/Pages/Creditos/Creditos/Components/mdlHistorialCrediticio.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";
import { FilterMatchMode, FilterOperator } from "primevue/api/";

const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		mdlHistorialCrediticio,

		DatePicker,
		DataTable,
		Column,
		ColumnGroup,
		Row,
	},
	data() {
		return {
			submited: false,

			agencias_permitidas: [],
			agencia_seleccionada: 0,

			fecha_facturacion: null,

			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},
			lista_facturados: [],
			totales: {},

			filtros_tabla: {},
		};
	},
	validations: {
		agencia_seleccionada: { noZero, required },
		datos_fecha: {
			fecha_desde: { required },
			fecha_hasta: { required },
		},
	},
	watch: {
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

		fecha_facturacion(value) {
			if (value != null) {
				let fecha = value.split("-");

				// Establecer mes y año
				const mes = parseInt(fecha[1] - 1);
				const anio = parseInt(fecha[0]);

				const primerDiaMes = moment().year(anio).month(mes).startOf("month");
				const ultimoDiaMes = moment().year(anio).month(mes).endOf("month");

				// Asignar fechas
				this.datos_fecha.fecha_desde = primerDiaMes.format("YYYY-MM-DD");
				this.datos_fecha.fecha_hasta = ultimoDiaMes.format("YYYY-MM-DD");
			} else {
				this.datos_fecha.fecha_desde = null;
				this.datos_fecha.fecha_hasta = null;
			}
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
	},
	created() {
		this.filtros_tabla = {
			global: { value: null, matchMode: FilterMatchMode.CONTAINS },
			nueva_empresa: { value: null, matchMode: FilterMatchMode.CONTAINS },
		};
	},
	methods: {
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_SEGUIMIENTO_FACTURADOS"
			);

			let agencias = this.$inertia.page.props.application.agencias;

			if (this.agencias_permitidas.length == agencias.length) {
				let obj = {
					agencia: "TODAS",
					direccion: null,
					distrito: null,
					id: "TODAS",
				};

				this.agencias_permitidas.push(obj);
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

		Buscar() {
			this.submited = true;

			if (
				this.$v.agencia_seleccionada.$invalid ||
				this.$v.datos_fecha.$invalid
			) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});

				return false;
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				showConfirmButton: false,
				allowOutsideClick: false,

				willOpen: () => {
					Swal.showLoading();
					this.ListarFacturados();
				},
			});

			// this.ListarFacturados();
		},
		VerHistorial(event) {
			let modulo = this.$refs.layout.$refs.mdlHistorialCrediticio;

			modulo.agencia_id = event.data.agencia_id;
			modulo.cliente_id = event.data.cliente_id;
			modulo.ActualizarInformacion();

			return $("#mdlHistorialCrediticio").css("display", "block");
		},
		ListarFacturados() {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_facturacion", this.fecha_facturacion);
			data.append("fecha_desde", this.datos_fecha.fecha_desde);
			data.append("fecha_hasta", this.datos_fecha.fecha_hasta);

			// this.$inertia.post(route("rep.caj.seguimiento_facturados.buscar"), data);
			// return false;

			axios
				.post(route("rep.caj.seguimiento_facturados.buscar"), data)
				.then(function (response) {
					let lista_facturados = response.data.lista_facturados;
					let totales = response.data.totales;

					if (lista_facturados.length > 0) {
						self.lista_facturados = lista_facturados;
						self.totales = totales;
						return Swal.fire({
							icon: "success",
							title: "¡Listo!",
							timer: 1200,
							showConfirmButton: false,
						});
					} else {
						return Swal.fire({
							icon: "info",
							title: "¡Ups!",
							text: "No hay registros encontrados",
							timer: 1200,
							showConfirmButton: false,
						});
					}
				});
		},
		Exportar() {
			let lista_datos = [];
			if (this.filtros_tabla.nueva_empresa.value != null) {
				lista_datos = this.lista_facturados.filter(
					(item) => item.nueva_empresa == this.filtros_tabla.nueva_empresa.value
				);
			} else {
				lista_datos = this.lista_facturados;
			}

			let data = new FormData();
			data.append("fecha_facturacion", this.fecha_facturacion);
			data.append("lista_facturados", JSON.stringify(lista_datos));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,

				didOpen: () => {
					// this.$inertia.post(
					// 	route("rep.caj.seguimiento_facturados.exportar"),
					// 	data
					// );
					// return false;

					Swal.showLoading();

					axios
						.post(route("rep.caj.seguimiento_facturados.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptSeguimientoFacturados.xlsx";
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
.slot-seguimiento-facturados {
	width: 70% !important;

	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-seguimiento-facturados {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

