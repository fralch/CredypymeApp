<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-caja-cierres" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CIERRES DE CAJA'"></headerClose>

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
											v-if="
												agencia_seleccionada != 0 &&
												agencia_seleccionada != null
											"
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
									v-if="
										agencia_seleccionada != 0 && agencia_seleccionada != null
									"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>
						<div class="card-title mb-2">LISTA DE RESULTADOS</div>

						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="lista-tab"
									data-toggle="tab"
									href="#lista"
									role="tab"
									aria-controls="lista"
									aria-selected="true"
									>LISTA DE CIERRES</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="vista-tab"
									data-toggle="tab"
									href="#vista"
									role="tab"
									aria-controls="vista"
									aria-selected="false"
									>VISTA PREVIA</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="lista"
								role="tabpanel"
								aria-labelledby="lista-tab"
							>
								<DataTable
									:row-class="rowClass"
									:value="lista_caja_cierres"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="400px"
									selectionMode="single"
									@row-dblclick="VerDetalle"
									:paginator="true"
									:rows="50"
									paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
								>
									<Column
										field="numero"
										header="N°"
										:styles="{ minWidth: '50px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ data.index + 1 }}
										</template>
									</Column>
									<Column
										field="usuario"
										header="USUARIO"
										:styles="{ minWidth: '120px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="fecha_apertura"
										header="FECHA_APERTURA"
										:styles="{ minWidth: '120px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="fecha_cierre"
										header="FECHA_CIERRE"
										:styles="{ minWidth: '120px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="observacion"
										header="OBSERVACIÓN"
										:styles="{ minWidth: '150px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="comentario_apertura"
										header="NOTAS_APERTURA"
										:styles="{ minWidth: '200px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="comentario_cierre"
										header="NOTAS_CIERRE"
										:styles="{ minWidth: '200px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="fecha_apertura_real"
										header="FECHA_APERTURA_REAL"
										:styles="{ minWidth: '120px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="fecha_cierre_real"
										header="FECHA_CIERRE_REAL"
										:styles="{ minWidth: '120px', justifyContent: 'center' }"
									>
									</Column>
								</DataTable>
							</div>
							<div
								class="tab-pane fade"
								id="vista"
								role="tabpanel"
								aria-labelledby="vista-tab"
							>
								<div
									:style="
										windowWidth >= 900
											? 'width: 70%; margin-left: 15%; background: white'
											: 'width: 100%; background: white'
									"
									v-show="caja_seleccionada.usuario != null"
								>
									<div class="form-row">
										<div class="col-md-12 text-center">
											<label class="label-title"
												>CIERRE DE OPERACIONES DIARIAS</label
											>
										</div>
										<div class="col-md-12 text-center">
											<label class="label-title">
												Usuario: {{ caja_seleccionada.usuario }}</label
											>
										</div>
										<div class="col-md-6 text-center">
											<label class="label-title"
												>Fecha apertura de sistema:
												{{ caja_seleccionada.apertura_sistema }}</label
											>
										</div>
										<div class="col-md-6 text-center">
											<label class="label-title"
												>Fecha cierre de sistema:
												{{ caja_seleccionada.cierre_sistema }}</label
											>
										</div>
										<div class="col-md-6 text-center">
											<label class="label-title"
												>Fecha apertura de real:
												{{ caja_seleccionada.apertura_real }}</label
											>
										</div>
										<div class="col-md-6 text-center">
											<label class="label-title"
												>Fecha cierre de real:
												{{ caja_seleccionada.cierre_real }}</label
											>
										</div>
									</div>
									<hr />

									<DataTable
										:value="lista_operaciones"
										:scrollable="true"
										scrollDirection="both"
										scrollHeight="300px"
									>
										<Column
											field="concepto"
											header="CONCEPTO"
											:styles="{
												width: '300px',
												justifyContent: 'right',
											}"
										>
										</Column>
										<Column
											field="ingresos"
											header="INGRESOS"
											:styles="{
												width: '150px',
												justifyContent: 'right',
											}"
										>
											<template #body="{ data }">
												{{
													data.ingresos == 0 ? "-" : roundTo(data.ingresos, 2)
												}}
											</template>
										</Column>
										<Column
											field="egresos"
											header="EGRESOS"
											:styles="{
												width: '150px',
												justifyContent: 'right',
											}"
										>
											<template #body="{ data }">
												{{ data.egresos == 0 ? "-" : roundTo(data.egresos, 2) }}
											</template>
										</Column>

										<ColumnGroup type="footer">
											<Row>
												<Column
													footer="SUBTOTAL"
													:footerStyle="{
														width: '300px',
														'text-align': 'right',
													}"
												/>
												<Column
													:footer="'S/ ' + roundTo(total_ingresos, 2)"
													:footerStyle="{
														width: '150px',
														'text-align': 'right',
														'font-size': '13px !important',
													}"
												/>
												<Column
													:footer="'S/ ' + roundTo(total_egresos, 2)"
													:footerStyle="{
														width: '150px',
														'text-align': 'right',
														'font-size': '13px !important',
													}"
												/>
											</Row>
											<Row>
												<Column
													footer="TOTAL EFECTIVO CIERRE"
													:footerStyle="{
														width: '300px',
														'text-align': 'right',
													}"
												/>
												<Column
													:colspan="2"
													:footer="
														'S/ ' + roundTo(total_ingresos - total_egresos, 2)
													"
													:footerStyle="{
														width: '300px',
														'text-align': 'center',
														'font-size': '14px !important',
													}"
												/>
											</Row>
										</ColumnGroup>
									</DataTable>
									<div
										class="form-row bolder bg-warning"
										v-if="caja_seleccionada.observacion != null"
									>
										<div class="col-md-12 text-center">
											<label class="label-title"
												>***
												{{
													caja_seleccionada.observacion +
													": " +
													caja_seleccionada.detalle_observacion.concepto +
													" - S/ " +
													roundTo(
														caja_seleccionada.detalle_observacion.monto,
														2
													)
												}}</label
											>
										</div>
									</div>
								</div>

								<hr />

								<div class="text-right">
									<div class="text-right btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											title="Imprimir"
											@click="Exportar('PDF')"
											:disabled="!lista_operaciones.length"
										>
											<span class="icon text-white">
												<i class="fa fa-print"></i>
											</span>
											<span class="text">IMPRIMIR</span>
										</button>
										<button
											class="btn btn-cancel btn-icon-split"
											title="Exportar"
											@click="Exportar('XLSX')"
											:disabled="!lista_operaciones.length"
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
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			windowWidth: window.innerWidth,

			fecha_desde: null,
			fecha_hasta: null,

			lista_caja_cierres: [],

			total_egresos: 0,
			total_ingresos: 0,

			caja_seleccionada: {
				usuario: null,
				apertura_sistema: null,
				cierre_sistema: null,
				apertura_real: null,
				cierre_real: null,
				caja: null,
			},
			lista_operaciones: [],
		};
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
		agencia_seleccionada() {
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
		rowClass(data) {
			let index = data.index;
			return index % 2 == 0 ? "verde-claro" : "";
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_CIERRES"
			);
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
			if (this.agencia_seleccionada == 0) {
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

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("rep.caj.caja_cierres.buscar"), data);
					axios
						.post(route("rep.caj.caja_cierres.buscar"), data)
						.then(function (response) {
							if (response.data.lista_caja_cierres.length == 0) {
								self.lista_caja_cierres = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_caja_cierres = response.data.lista_caja_cierres;
								self.caja_seleccionada.usuario = null;
								self.lista_operaciones = [];
								$("#lista-tab").tab("show");
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

		VerDetalle(event) {
			let self = this;
			let caja = event.data;

			this.caja_seleccionada.caja = caja.id;

			axios
				.post(
					route("caj.operaciones_caja", {
						caja_id: caja.id,
						agencia_id: self.agencia_seleccionada,
					})
				)
				.then((response) => {
					self.lista_operaciones = response.data;
				})
				.then(() => {
					self.total_egresos = self.lista_operaciones.reduce(
						(t, { egresos }) => t + parseFloat(egresos),
						0
					);
					self.total_ingresos = self.lista_operaciones.reduce(
						(t, { ingresos }) => t + parseFloat(ingresos),
						0
					);
				})
				.then(() => {
					self.caja_seleccionada.usuario = (
						caja.apellido_paterno +
						" " +
						caja.apellido_materno +
						" " +
						caja.nombres
					).toUpperCase();
					self.caja_seleccionada.apertura_sistema = caja.fecha_apertura;
					self.caja_seleccionada.cierre_sistema = caja.fecha_cierre;
					self.caja_seleccionada.apertura_real = caja.fecha_apertura_real;
					self.caja_seleccionada.cierre_real = caja.fecha_cierre_real;
					self.caja_seleccionada.observacion = caja.observacion;
					self.caja_seleccionada.detalle_observacion = caja.detalle_observacion;
				})
				.then(() => {
					$("#preview").css("display", "block");
					$("#vista-tab").tab("show");
				});
		},

		Exportar(tipo) {
			let data = new FormData();
			let total_resta = parseFloat(this.total_ingresos - this.total_egresos);

			data.append(
				"data_usuario",
				JSON.stringify({
					usuario: "Usuario: " + this.caja_seleccionada.usuario,
				})
			);
			data.append(
				"data_sistema",
				JSON.stringify({
					apertura_sistema:
						"Fecha apertura de sistema: " +
						this.caja_seleccionada.apertura_sistema,
					cierre_sistema:
						"Fecha cierre de sistema: " + this.caja_seleccionada.cierre_sistema,
				})
			);
			data.append(
				"data_real",
				JSON.stringify({
					apertura_real:
						"Fecha apertura real: " + this.caja_seleccionada.apertura_real,
					cierre_real:
						"Fecha cierre real: " + this.caja_seleccionada.cierre_real,
				})
			);

			data.append("lista_operaciones", JSON.stringify(this.lista_operaciones));

			data.append(
				"subtotales",
				JSON.stringify({
					subtotales: "Subtotales",
					total_ingresos: this.total_ingresos.toFixed(2),
					total_egresos: this.total_egresos.toFixed(2),
				})
			);

			data.append(
				"totales",
				JSON.stringify({
					totales: "TOTAL",
					total: parseFloat(total_resta).toFixed(2),
				})
			);

			data.append("observacion", this.caja_seleccionada.observacion);

			if (this.caja_seleccionada.observacion != null) {
				data.append(
					"detalle_observacion",
					JSON.stringify(this.caja_seleccionada.detalle_observacion)
				);
			}

			data.append("tipo", tipo);

			let titulo = "";

			if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			} else if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			}

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,

				didOpen: () => {
					// this.$inertia.post(route("rep.caj.caja_cierres.exportar"), data);
					// return false;

					Swal.showLoading();
					axios
						.post(route("rep.caj.caja_cierres.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								link.download = "rptCierreCaja.xlsx";
								link.click();
							} else if (tipo == "PDF") {
								let origin = window.location.origin;
								let path_pdf = response.data.path_pdf;

								// Crear un IFrame
								let iframe = document.createElement("iframe");
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
	},
};
</script>

<style lang="css">
.slot-reporte-caja-cierres {
	width: 70% !important;
	margin-left: 20% !important;
}

.gray {
	background: var(--plomoOscuroEmpresarial) !important;
}

/* Para corregir bug de datatable */
.dataTable {
	width: 100% !important;
}
.dataTables_scrollFootInner {
	width: 100% !important;
}
.DTFC_ScrollWrapper {
	height: auto !important;
}
/* --------------------------------- */

@media only screen and (max-width: 1280px) {
	.slot-reporte-caja-cierres {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>

