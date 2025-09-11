<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-cierres-dia" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CIERRES DE DÍA'"></headerClose>

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
											class="form-control input-information center"
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
											class="form-control input-information center"
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
						<div class="card-title">LISTA DE RESULTADOS</div>

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
									>LISTA DE OPERACIONES</a
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
								<table
									class="table"
									id="tblListaCierres"
									style="width: 100% !important"
								>
									<thead>
										<tr>
											<th>N°</th>
											<th>USUARIO</th>
											<th>EQUIPO</th>
											<th>FECHA_SISTEMA</th>
											<th>FECHA_REAL</th>
											<th>MONTO_FINAL</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_cierres"
											:key="index"
											class="table-bordered"
											:class="index % 2 == 0 ? 'verde-claro' : ''"
											@dblclick="VerDetalle(item)"
										>
											<td align="center">{{ index + 1 }}</td>
											<td align="center">
												{{ item.usuario }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).nombre_dispositivo }}
											</td>
											<td align="center">
												{{ item.fecha_sistema }}
											</td>
											<td align="center">
												{{ item.fecha_real }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.total_monto_final, 2) }}
											</td>
										</tr>
									</tbody>
								</table>
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
									v-show="cierre_seleccionado != null"
								>
									<div class="form-row">
										<div class="col-md-12 text-center">
											<label class="label-title">LISTA DE OPERACIONES</label>
										</div>
									</div>
									<hr />
									<table class="table" id="tblOperacionesCaja" width="100%">
										<thead>
											<tr>
												<th>OPERACIÓN</th>
												<th>INGRESOS</th>
												<th>EGRESOS</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in lista_operaciones"
												:key="index"
											>
												<td align="right" class="with-border">
													{{ item.concepto }}
												</td>
												<td align="right" class="with-border">
													{{
														item.ingresos == 0 ? "-" : roundTo(item.ingresos, 2)
													}}
												</td>
												<td align="right" class="with-border">
													{{
														item.egresos == 0 ? "-" : roundTo(item.egresos, 2)
													}}
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<th style="font-size: 11px">SUBTOTAL</th>
												<th
													style="font-size: 11px; min-width: 100px !important"
												>
													S/
													{{ roundTo(total_ingresos, 2) }}
												</th>
												<th
													style="font-size: 11px; min-width: 100px !important"
												>
													S/
													{{ roundTo(total_egresos, 2) }}
												</th>
											</tr>
											<tr>
												<th class="gray" style="font-size: 11px">
													TOTAL EFECTIVO CIERRE
												</th>
												<th class="gray" colspan="2" style="font-size: 11px">
													S/
													{{ roundTo(total_ingresos - total_egresos, 2) }}
												</th>
											</tr>
										</tfoot>
									</table>
								</div>

								<hr />

								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										title="Imprimir"
										@click="ImprimirCierre()"
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
										@click="Exportar()"
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
			<rptCierreCaja ref="rptCierreCaja"> </rptCierreCaja>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import rptCierreCaja from "@/Pages/Creditos/Caja/Reports/rptCierreCaja.vue";

export default {
	components: { layout, headerClose, rptCierreCaja },
	props: {
		usuarios: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			windowWidth: window.innerWidth,

			fecha_desde: null,
			fecha_hasta: null,
			fecha_agencia: null,

			lista_cierres: [],

			cierre_seleccionado: null,
			lista_operaciones: [],
			total_ingresos: 0,
			total_egresos: 0,
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

		lista_cierres() {
			$("#tblListaCierres").DataTable().destroy();
			this.TablaCierres();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaCierres();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},
	methods: {
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_CIERRES_DIA"
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
		TablaCierres() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "300px";
				}
				var table = $("#tblListaCierres").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
					},
					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
				});
			});
		},
		TablaOperaciones() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "300px";
				}
				var table = $("#tblOperaciones").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
						info: false,
					},
					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
				});
			});
		},
		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("fecha_agencia", this.fecha_agencia);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					//   this.$inertia.post(route("rep.caj.dia_cierres.buscar"), data);
					axios
						.post(route("rep.caj.cierres_dia.buscar"), data)
						.then(function (response) {
							if (response.data.lista_cierres.length == 0) {
								self.lista_cierres = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_cierres = response.data.lista_cierres;
								return Swal.fire({
									icon: "success",
									title: "¡ÉXITO!",
									allowOutsideClick: true,
								});
							}
						});
				},
			});
		},

		VerDetalle(cierre_dia) {
			let self = this;
			this.cierre_seleccionado = cierre_dia;

			// this.$inertia.post(route("caj.operaciones_agencia", {
			// 			caja_id: cierre_dia.fecha_sistema,
			// 			agencia_id: self.agencia_seleccionada,
			// 			id_cierre: cierre_dia.id_cierre,
			// 		})
			// )

			// return false

			axios
				.post(
					route("caj.operaciones_agencia", {
						caja_id: cierre_dia.fecha_sistema,
						agencia_id: self.agencia_seleccionada,
						id_cierre: cierre_dia.id_cierre,
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
					$("#preview").css("display", "block");
					$("#vista-tab").tab("show");
				});
		},
		ImprimirCierre() {
			let self = this;
			this.submited = false;
			let cierreCaja = this.$refs.rptCierreCaja;

			async function EnviarDatos() {
				cierreCaja.datos_operacion = self.lista_operaciones;
				cierreCaja.efectivo_caja = self.roundTo(
					self.total_ingresos - self.total_egresos,
					2
				);
				cierreCaja.total_ingresos = self.total_ingresos;
				cierreCaja.total_egresos = self.total_egresos;
				cierreCaja.caja_usuario = null;
				cierreCaja.f_apertura_sistema = null;
				cierreCaja.f_cierre_sistema = null;
				cierreCaja.f_apertura_real = null;
				cierreCaja.f_cierre_real = null;
			}
			EnviarDatos().then(() => {
				$("#rptCierreCaja").css("display", "block");
				$("#rptCierreCaja").print();
				$("#rptCierreCaja").css("display", "none");
			});
		},
		Exportar() {
			let data = new FormData();
			let total_resta = parseFloat(this.total_ingresos - this.total_egresos);
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


			// this.$inertia.post(route("rep.cre.dias_mora.exportar"), data);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.caj.cierres_dia.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptOperacionesCierreDia.xlsx";
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
.slot-reporte-cierres-dia {
	width: 60% !important;
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

@media only screen and (max-width: 900px) {
	.slot-reporte-cierres-dia {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>

