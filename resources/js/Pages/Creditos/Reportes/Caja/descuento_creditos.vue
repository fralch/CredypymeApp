<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-descuentos" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="'LISTADO DE DESCUENTOS DE CRÉDITOS'"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
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
											<option value="0" selected disabled>
												Seleccionar agencia
											</option>
											<option
												v-for="item in agencias_permitidas"
												:key="item.id"
												:value="item.id"
												:style="
													windowWidth >= 900
														? 'font-size: 15px !important'
														: 'font-size: 13px !important'
												"
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

							<div class="form-row col-md-12">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbPorAsesor"
												v-model="chbPorAsesor"
												value="asesor"
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
									<div class="input-group-prepend"></div>
									<select
										class="form-control center"
										v-model="asesor_seleccionado"
										:disabled="chbPorAsesor == false"
									>
										<option :value="0" disabled selected>Seleccione...</option>
										<option
											v-for="(item, index) in asesores_filtrados"
											:key="index"
											:value="item.dni"
										>
											{{ item.usuario }}
										</option>
									</select>
								</div>

								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbCaja"
												v-model="chbCaja"
												value="usuario"
											/>
										</div>
										<label
											class="input-group-text prepend-title"
											for="chbCaja"
											style="font-size: 13px"
										>
											CAJA
										</label>
									</div>

									<select
										class="form-control center"
										v-model="caja_seleccionada"
										:disabled="chbCaja == false"
									>
										<option :value="0" disabled selected>Seleccione...</option>
										<option
											v-for="(item, index) in caja_filtrada"
											:key="index"
											:value="item.dni"
										>
											{{ item.usuario }}
										</option>
									</select>
								</div>
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbTipoDescuento"
												v-model="checked_tipo_descuento"
											/>
										</div>
										<label
											class="input-group-text prepend-title"
											for="chbTipoDescuento"
											style="font-size: 13px"
										>
											TIPO
										</label>
									</div>

									<select
										class="form-control center"
										v-model="tipo_descuento_seleccionado"
										:disabled="!checked_tipo_descuento != false"
									>
										<option value="0" disabled selected>Seleccione...</option>
										<option value="mora">Mora</option>
										<option value="notificacion">Notificación</option>
										<option value="interes">Interés</option>
									</select>
								</div>
							</div>
						</div>
						<div class="card-title mt-2">LISTA DE RESULTADOS</div>

						<table class="table table-hover" id="tblDescuentos" width="100%">
							<thead>
								<tr>
									<th style="min-width: 30px !important">N°</th>
									<th style="min-width: 70px !important">EXPEDIENTE</th>
									<th style="min-width: 200px !important">CLIENTE</th>
									<th style="min-width: 100px !important">ASESOR</th>
									<th style="min-width: 70px !important">TASA_INT.</th>
									<th style="min-width: 150px !important">PLAZO</th>
									<th style="min-width: 70px !important">MONTO</th>
									<th style="min-width: 120px !important">CAJA</th>
									<th style="min-width: 70px !important">DSCT_MORA.</th>
									<th style="min-width: 70px !important">DSCT_INTERES.</th>
									<th style="min-width: 70px !important">DSCT_NOTIF.</th>
									<th style="min-width: 70px !important">FECHA</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(descuento, index) in datos_tabla_descuentos"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
								>
									<td align="center">
										{{ index + 1 }}
									</td>
									<td align="center">
										{{ descuento.codigo_expediente }}
									</td>
									<td>
										{{
											descuento.apellido_paterno +
											" " +
											descuento.apellido_materno +
											" " +
											descuento.nombres
										}}
									</td>
									<td align="center">
										<!-- S/ {{ roundTo(descuento.capital_total,2) }} -->
										{{ descuento.asesor }}
									</td>
									<td align="center">
										{{ roundTo(descuento.tasa_interes, 2) }} %
									</td>
									<td align="center">
										{{ roundTo(descuento.plazo, 0) }}
										{{ tipo_plazo(descuento.periodo_pago) }}
									</td>
									<td align="right">S/ {{ roundTo(descuento.monto, 2) }}</td>
									<td align="center">
										{{ descuento.caja }}
									</td>
									<td align="right">
										{{
											roundTo(descuento.dscto_mora_cancelado, 2) == 0.0
												? "-"
												: "S/ " + roundTo(descuento.dscto_mora_cancelado, 2)
										}}
									</td>
									<td align="right">
										{{
											roundTo(descuento.dscto_interes_cancelado, 2) == 0.0
												? "-"
												: "S/ " + roundTo(descuento.dscto_interes_cancelado, 2)
										}}
									</td>
									<td align="right">
										{{
											roundTo(descuento.dscto_notificaciones_cancelado, 2) ==
											0.0
												? "-"
												: "S/ " +
												  roundTo(descuento.dscto_notificaciones_cancelado, 2)
										}}
									</td>
									<td align="right">
										{{ formato_fecha(descuento.fecha_hora_cancelado) }}
									</td>
								</tr>
							</tbody>
						</table>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar"
								:disabled="datos_tabla_descuentos.length == 0"
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

export default {
	components: { layout, headerClose },
	props: {
		// modo: String,
		usuarios_cuenta: Array,
		asesores: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			fecha_desde: null,
			fecha_hasta: null,

			windowWidth: window.innerWidth,

			chbPorAsesor: false,
			chbCaja: false,
			checked_tipo_descuento: false,

			caja_filtrada: [],
			asesores_filtrados: [],

			caja_seleccionada: 0,
			asesor_seleccionado: 0,
			tipo_descuento_seleccionado: "0",

			datos_tabla_descuentos: [],
		};
	},
	watch: {
		chbPorAsesor() {
			if (!this.chbPorAsesor) {
				this.asesor_seleccionado = 0;
			}
		},
		chbCaja() {
			if (!this.chbCaja) {
				this.caja_seleccionada = 0;
			}
		},

		checked_tipo_descuento() {
			if (!this.checked_tipo_descuento) {
				this.tipo_descuento_seleccionado = "0";
			}
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
		agencia_seleccionada() {
			this.FechaActual(this.agencia_seleccionada);
			this.filtrar_usuarios();
		},

		datos_tabla_descuentos() {
			$("#tblDescuentos").DataTable().destroy();
			this.TablaDescuentos();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		this.TablaDescuentos();
		this.FechaActual();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},
	methods: {
		tipo_plazo(tipo) {
			if (tipo == "DIARIO") {
				return "Días";
			}
			if (tipo == "SEMANAL") {
				return "Semanas";
			}
			if (tipo == "QUINCENAL") {
				return "Quincenas";
			}
			if (tipo == "MENSUAL") {
				return "Meses";
			}
		},
		formato_fecha(value) {
			if (value != null) {
				return (
					String(value).substring(8, 10) +
					"/" +
					String(value).substring(5, 7) +
					"/" +
					String(value).substring(0, 4)
				);
			} else {
				return null;
			}
		},
		filtrar_usuarios() {
			this.caja_filtrada = this.usuarios_cuenta.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
			this.asesores_filtrados = this.asesores.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
		},
		async FechaActual(agencia) {
			if (agencia == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(agencia);
				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_DESCUENTOS_CREDITOS"
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

			let resultado = parseFloat(valor).toLocaleString("es-PE", {
				minimumFractionDigits: numero_decimales,
				maximumFractionDigits: numero_decimales,
			});

			return resultado;
		},

		TablaDescuentos() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "170px";
				}
				var table = $("#tblDescuentos").DataTable({
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
			data.append("agencia_seleccionada", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("caja_seleccionada", this.caja_seleccionada);
			data.append("asesor_seleccionado", this.asesor_seleccionado);
			data.append(
				"tipo_descuento_seleccionado",
				this.tipo_descuento_seleccionado
			);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(
					// 	route("rep.caj.compromisos_notificaciones.buscar"),
					// 	data
					// );
					axios
						.post(route("rep.caj.descuentos_creditos.buscar"), data)
						.then(function (response) {
							// return Swal.fire({
							// 		icon: "success",
							// 		title: "¡Listo!",
							// 	});

							if (response.data == 0) {
								self.datos_tabla_descuentos = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.datos_tabla_descuentos = response.data;
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
		Exportar(modo) {
			let total_desc_interes = 0;
			let total_desc_mora = 0;
			let total_desc_nof = 0;
			this.datos_tabla_descuentos.forEach((element) => {
				total_desc_interes += parseFloat(element.dscto_interes_cancelado);
			});
			this.datos_tabla_descuentos.forEach((element) => {
				total_desc_mora += parseFloat(element.dscto_mora_cancelado);
			});
			this.datos_tabla_descuentos.forEach((element) => {
				total_desc_nof += parseFloat(element.dscto_notificaciones_cancelado);
			});

			let data = new FormData();
			// data.append("datos_tabla_descuentos", JSON.stringify(this.datos_tabla_descuentos));
			data.append(
				"datos_tabla_descuentos",
				JSON.stringify(this.datos_tabla_descuentos)
			);
			data.append(
				"totales",
				JSON.stringify({
					total_desc_mora: total_desc_mora,
					total_desc_interes: total_desc_interes,
					total_desc_nof: total_desc_nof,
				})
			);
			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.caj.descuentos_creditos.exportar"), data)
						.then(function (response) {
							// return Swal.fire({
							// 	icon: "success",
							// 	title: "¡Listo!",
							// });

							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptDescuentos.xlsx";

							downloadLink.href = linkSource;
							downloadLink.download = fileName;
							downloadLink.click();

							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
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
.slot-reporte-descuentos {
	width: 60% !important;
	margin-left: 20% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media only screen and (max-width: 1280px) {
	.slot-reporte-descuentos {
		width: 80% !important;
		margin-left: 10% !important;
	}
}
@media only screen and (max-width: 900px) {
	.slot-reporte-descuentos {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>


