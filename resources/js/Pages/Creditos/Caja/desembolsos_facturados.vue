<template>
	<layout ref="layout">
		<div class="slot_body slot-desembolsos-facturados" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'DESEMBOLSOS FACTURADOS'"></headerClose>
					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10 col-10">
								<legend>
									<label class="label-title">FILTROS DE BUSQUEDA</label>
								</legend>

								<div class="row">
									<div
										class="input-group col-md-4 col-6 offset-sm-3 offset-md-0"
									>
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
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

									<div class="input-group col-md-4 col-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="datos_fecha.fecha_desde"
											style="font-size: 15px"
											:class="
												submited
													? $v.datos_fecha.fecha_desde.$invalid
														? 'is-invalid'
														: ''
													: ''
											"
										/>
									</div>
									<div class="input-group col-md-4 col-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="datos_fecha.fecha_hasta"
											style="font-size: 15px"
											:class="
												submited
													? $v.datos_fecha.fecha_hasta.$invalid
														? 'is-invalid'
														: ''
													: ''
											"
										/>
									</div>
								</div>
							</fieldset>

							<div class="form-group col-md-1 mt-2 col-1">
								<button
									class="btn btn-action mt-2"
									@click="Buscar"
									title="Buscar entre fechas"
								>
									<span class="icon text-white" style="font-size: 20px">
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
									:disabled="lista_desembolsos.length == 0"
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
							:value="lista_desembolsos"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="375px"
							:paginator="true"
							:rows="20"
							:filters="filtros_tabla"
							selectionMode="single"
							paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
							currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
						>
							<Column
								field="imprimir"
								header="IMPRIMIR"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<div class="btn-group" role="group">
										<button
											class="btn btn-cancel btn-sm"
											title="Imprimir COMPROBANTE"
											@click="Imprimir(data.datos_comprobante)"
											v-if="data.facturado"
										>
											<i class="pi pi-print"></i>
										</button>
										<!-- <button
											class="btn btn-action btn-sm"
											title="Facturar"
											@click="Facturar(data.id)"
											v-if="!data.facturado"
										>
											<i class="pi pi-dollar"></i>
										</button> -->
									</div>
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
								field="numero"
								header="N°"
								:styles="{ width: '40px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.index + 1 }}
								</template>
							</Column>
							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.agencia }}
								</template>
							</Column>
							<Column
								field="cliente"
								header="CLIENTE"
								:styles="{ width: '300px' }"
							>
								<template #body="{ data }">
									{{ data.cliente }}
								</template>
							</Column>
							<Column
								field="numero_documento"
								header="NUMERO_DOCUMENTO"
								:styles="{ width: '120px' }"
							>
								<template #body="{ data }">
									{{ data.numero_documento }}
								</template>
							</Column>
							<Column
								field="interes_total"
								header="INTERÉS_TOTAL"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.interes_total, 2) }}
								</template>
							</Column>
							<Column
								field="porcentaje_igv"
								header="IGV_%"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ roundTo(data.porcentaje_igv, 2) }} %
								</template>
							</Column>
							<Column
								field="importe_igv"
								header="IMPORTE_IGV"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.importe_igv, 2) }}
								</template>
							</Column>
							<Column
								field="importe_gravado"
								header="IMPORTE_GRAVADO"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.importe_gravado, 2) }}
								</template>
							</Column>
							<Column
								field="datos_creacion"
								header="FECHA_DESEMBOLSO"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.fecha_desembolso }}
								</template>
							</Column>
							<Column
								field="usuario_caja"
								header="CAJA"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.usuario_caja }}
								</template>
							</Column>
							<Column
								field="monto"
								header="MONTO"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.monto, 2) }}
								</template>
							</Column>
							<Column
								field="plazo"
								header="PLAZO"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{
										roundTo(data.plazo, 0) +
										" " +
										periodo_medicion(data.periodo_pago)
									}}
								</template>
							</Column>
							<Column
								field="tasa_interes"
								header="TASA_INTERÉS"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ roundTo(data.tasa_interes, 2) }} %
								</template>
							</Column>
							<Column
								field="cuota"
								header="CUOTA"
								:styles="{ width: '120px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.cuota, 2) }}
								</template>
							</Column>
							<Column
								field="usuario_asesor"
								header="ASESOR"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.usuario_asesor }}
								</template>
							</Column>
						</DataTable>
						<hr />
						<div class="text-right">
							<!-- <button
								class="btn btn-cancel btn-icon-split"
								title="Descargar XML"
								@click="DescargarXMLCompleto()"
								:disabled="lista_desembolsos.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-download"></i>
								</span>
								<span class="text">DESCARGAR XML COMPLETO</span>
							</button> -->
							<button
								class="btn btn-cancel btn-icon-split"
								title="EXPORTAR EN EXCEL"
								@click="Exportar()"
								:disabled="lista_desembolsos.length == 0"
							>
								<span class="icon text-white">
									<i class="pi pi-file-excel"></i>
								</span>
								<span class="text">EXPORTAR PARA SISCONT</span>
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import Dropdown from "primevue/dropdown/dropdown.common";
import { FilterMatchMode, FilterOperator } from "primevue/api/";

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose, DataTable, Column, Dropdown },
	data() {
		return {
			submited: false,

			agencias_permitidas: [],
			agencia_seleccionada: 0,
			tipo_seleccionado: "TODOS",
			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},
			lista_desembolsos: [],
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

		agencia_seleccionada(value) {
			if (value != 0 && value != null && value != "TODAS") {
				this.FechaActual();
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
				"CREDITOS_CAJA/DESEMBOLSOS_FACTURADOS"
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
		async FechaActual() {
			if (this.agencia_seleccionada == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_seleccionada
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.datos_fecha.fecha_desde = fecha_actual;
				this.datos_fecha.fecha_hasta = fecha_actual;
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
					this.ListarDesembolsos();
				},
			});

			// this.ListarDesembolsos();
		},
		ListarDesembolsos() {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.datos_fecha.fecha_desde);
			data.append("fecha_hasta", this.datos_fecha.fecha_hasta);

			// this.$inertia.post(route("caj.desembolsos_facturados.buscar"), data);
			// return false;

			axios
				.post(route("caj.desembolsos_facturados.buscar"), data)
				.then(function (response) {
					self.lista_desembolsos = response.data.lista_desembolsos;

					return Swal.fire({
						icon: "success",
						title: "¡Listo!",
						timer: 1200,
						showConfirmButton: false,
					});
				});
		},
		Facturar(desembolso_id) {
			Swal.fire({
				icon: "question",
				text: "¿Desea FACTURAR esta operación?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("agencia_id", this.agencia_seleccionada);
					data.append("desembolso_id", desembolso_id);
					data.append("modulo", "desembolsos_facturados");
					this.$inertia.post(route("caj.desembolso.facturar"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "FACTURANDO",
								text: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							this.ListarDesembolsos();
							return true;
						},
					});
				}
			});
		},
		Imprimir(comprobante) {
			let datos_comprobante = JSON.parse(comprobante);

			var left = screen.width;
			window.open(
				datos_comprobante.enlace_del_pdf,
				"facturador",
				"resizable=no,width=500px,height=800px,left=" + left
			);
		},
		DescargarXML(comprobante) {
			let datos_comprobante = JSON.parse(comprobante);
			let source = datos_comprobante.enlace_del_xml;

			const link = document.createElement("a");
			link.href = source;
			link.click();
		},
		DescargarXMLCompleto() {
			let self = this;
			// Array of URLs you want to download

			let urls = [];
			this.desembolsos.forEach((element) => {
				let datos_comprobante = JSON.parse(element.datos_comprobante);
				if (datos_comprobante != null) {
					let name =
						datos_comprobante.serie + "_" + datos_comprobante.numero + ".xml";
					let source = datos_comprobante.enlace_del_xml;
					let object = {
						name: name,
						source: source,
					};
					urls.push(object);
				}
			});

			let data = new FormData();
			data.append("urls", JSON.stringify(urls));

			// this.$inertia.post(
			// 	route("caj.desembolsos_facturados.descargar_xml"),
			// 	data
			// );
			// return false;

			Swal.fire({
				title: "GENERANDO ARCHIVO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("caj.desembolsos_facturados.descargar_xml"), data)
						.then(function (response) {
							console.log(response.data.path_zip);
							let path_zip = response.data.path_zip;

							const link = document.createElement("a");
							link.href = origin + path_zip;
							link.download = "Facturados_XML.zip";
							link.click();
							return Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								timer: 2000,
								showConfirmButton: false,
							});
						});
				},
			});

			// axios
			// 	.post(route("caj.desembolsos_facturados.descargar_xml"), data)
			// 	.then(function (response) {
			// 		console.log(response.data);
			// 	});
		},
		Exportar() {
			let lista_datos = [];
			if (this.filtros_tabla.nueva_empresa.value != null) {
				lista_datos = this.lista_desembolsos.filter(
					(item) => item.nueva_empresa == this.filtros_tabla.nueva_empresa.value
				);
			} else {
				lista_datos = this.lista_desembolsos;
			}

			let data = new FormData();
			data.append("lista_desembolsos", JSON.stringify(lista_datos));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,

				didOpen: () => {
					// this.$inertia.post(
					// 	route("caj.desembolsos_facturados.exportar"),
					// 	data
					// );
					// return false;

					Swal.showLoading();

					axios
						.post(route("caj.desembolsos_facturados.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptDesembolsosFacturados.xlsx";
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
.slot-desembolsos-facturados {
	width: 70% !important;

	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-desembolsos-facturados {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

