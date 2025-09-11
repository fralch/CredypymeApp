<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-desembolsos-asesor"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'DESEMBOLSOS POR TIPO DE CRÉDITO'"></headerClose>

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
											class="form-control input-information center"
											v-model="fecha_desde"
											style="font-size: 15px"
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
											style="font-size: 15px"
										/>
									</div>
								</div>
							</fieldset>

							<div class="col-md-1 ml-3">
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
							<!-- -------------- -->
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									id="chbVigentes"
									v-model="chbVigentes"
								/>
								<label class="label-title" for="chbPorUsuario"
									>Soló vigentes</label
								>
							</div>
							<!-- ------ -->

							<div class="input-group col-md-3 mb-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbTipo"
											v-model="chbTipoCredito"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbTipo"
										style="font-size: 13px"
									>
										TIPO
									</label>
								</div>

								<select
									class="form-control center"
									:disabled="!chbTipoCredito"
									v-model="tipo_credito"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in tipos"
										:key="index"
										:value="item.id"
									>
										{{ item.tipo }}
									</option>
								</select>
							</div>
							<div class="input-group col-md-3 mb-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbPorNumeroCredito"
											v-model="chbPeriodoCredito"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbPorNumeroCredito"
										style="font-size: 13px"
									>
										PERIODO
									</label>
								</div>

								<select
									class="form-control center"
									:disabled="!chbPeriodoCredito"
									v-model="periodo_credito"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option value="DIARIO">DIARIO</option>
									<option value="SEMANAL">SEMANAL</option>
									<option value="QUINCENAL">QUINCENAL</option>
									<option value="PAGO UNICO">PAGO UNICO</option>
									<option value="MENSUAL">MENSUAL</option>
								</select>
							</div>
							<div class="input-group col-md-4 mb-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbProducto"
											v-model="chbProductoCredito"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbProducto"
										style="font-size: 13px"
									>
										PRODUCTO
									</label>
								</div>
								<div class="input-group-prepend"></div>
								<select
									class="form-control center"
									:disabled="!chbProductoCredito"
									v-model="producto_credito"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in productos"
										:key="index"
										:value="item.id"
									>
										{{ item.producto }}
									</option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<!-- -------------------------------------------------------- -->

							<div :class="'col-md-12'">
								<div class="card-title">LISTA DE RESULTADOS</div>

								<table class="table" id="tblDesembolsosTipo" width="100%">
									<thead>
										<tr>
											<th style="max-width: 80px !important">EXPEDIENTE</th>
											<th style="max-width: 60px !important">NRO_CRED</th>
											<th style="max-width: 350px !important">CLIENTE</th>
											<th style="max-width: 100px !important">ASESOR</th>
											<th style="max-width: 80px !important">CAPITAL</th>
											<th style="max-width: 80px !important">INTERÉS</th>
											<th style="max-width: 100px !important">PLAZO</th>
											<th style="max-width: 70px !important">TASA</th>
											<th style="max-width: 100px !important">USUARIO</th>
											<th style="max-width: 100px !important">FECHA</th>
											<th style="max-width: 100px !important">HORA</th>
											<th style="max-width: 200px !important">ESTADO</th>
										</tr>
									</thead>
									<tbody
										v-for="(item_1, index_1) in lista_desembolsos"
										:key="index_1"
									>
										<tr>
											<td
												colspan="12"
												class="text-white"
												align="center"
												style="
													font-size: 13px !important;
													background-color: var(--blue) !important;
												"
											>
												FECHA DE DESEMBOLSO:
												{{ item_1.fecha }}
											</td>
										</tr>
										<tr
											v-for="(item_2, index_2) in item_1.desembolsos"
											:key="index_2"
											class="table-bordered"
											:class="index_2 % 2 == 0 ? 'verde-claro' : ''"
										>
											<td align="center">
												{{ item_2.codigo_expediente }}
											</td>
											<td align="center">
												{{ item_2.numero_credito }}
											</td>
											<td>
												{{
													item_2.apellido_paterno +
													" " +
													item_2.apellido_materno +
													" " +
													item_2.nombres
												}}
											</td>
											<td align="center">
												{{ item_2.usuario_asesor }}
											</td>
											<td align="right">
												S/
												{{ roundTo(item_2.capital_total, 2) }}
											</td>
											<td align="right">
												S/
												{{ roundTo(item_2.interes_total, 2) }}
											</td>
											<td align="center">
												{{
													roundTo(item_2.plazo, 0) +
													" " +
													periodo_medicion(item_2.periodo_pago)
												}}
											</td>
											<td align="center">
												{{ roundTo(item_2.tasa_interes, 2) }}
												%
											</td>
											<td align="center">
												{{ item_2.usuario }}
											</td>
											<td align="center">
												{{
													JSON.parse(item_2.datos_creacion)
														.fecha.toString()
														.substring(0, 10)
												}}
											</td>
											<td align="center">
												{{
													JSON.parse(item_2.datos_creacion)
														.fecha.toString()
														.substring(11, 20)
												}}
											</td>
											<td align="center">
												{{ item_2.estado }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar"
								:disabled="lista_desembolsos.length == 0"
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
		usuarios: Array,
		tipos: Array,
		productos: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_busqueda: 0,
			agencia_filtro: 0,

			fecha_desde: null,
			fecha_hasta: null,

			chbVigentes: false,
			chbTipoCredito: false,
			chbPeriodoCredito: false,
			chbProductoCredito: false,

			tipo_credito: 0,
			periodo_credito: 0,
			producto_credito: 0,

			lista_desembolsos: [],
		};
	},
	watch: {
		chbTipoCredito(val) {
			if (val == false) {
				this.tipo_credito = 0;
			}
		},
		chbPeriodoCredito(val) {
			if (val == false) {
				this.periodo_credito = 0;
			}
		},
		chbProductoCredito(val) {
			if (val == false) {
				this.producto_credito = 0;
			}
		},
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
				this.agencia_filtro = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
					this.agencia_filtro = mi_agencia[0].id;
				} else {
					this.agencia_busqueda = null;
					this.agencia_filtro = null;
				}
			}
		},

		agencia_busqueda() {
			this.FechaActual();
		},

		lista_desembolsos() {
			$("#tblDesembolsosTipo").DataTable().destroy();
			this.TablaDesembolsos();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaDesembolsos();
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

		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CREDITOS_DESEMBOLSOS_TIPO"
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

		TablaDesembolsos() {
			this.$nextTick(() => {
				$("#tblDesembolsosTipo").DataTable({
					scrollY: "300px",
					scrollX: true,
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
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});
				if (this.lista_desembolsos.length > 0) {
					$("#tblDesembolsosTipo .dataTables_empty").css("display", "none");
				}
			});
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("filtro_vigentes", this.chbVigentes);
			data.append("tipo", this.tipo_credito);
			data.append("periodo", this.periodo_credito);
			data.append("producto", this.producto_credito);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.cre.desembosos_tipo.buscar"), data)
						.then(function (response) {
							if (response.data.length == 0) {
								self.lista_desembolsos = [];
								self.totales = 0;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_desembolsos = response.data;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
								});
							}
						});
				},
			});
		},
		Exportar() {
			let data = new FormData();
			data.append("creditos_filtrados", JSON.stringify(this.lista_desembolsos));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.cre.desembosos_tipo.exportar"), data)
						.then(function (response) {
							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptDesembolsosTipo.xlsx";

							downloadLink.href = linkSource;
							downloadLink.download = fileName;
							downloadLink.click();

							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
								timer: 2000,
								allowOutsideClick: true,
							});
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-reporte-desembolsos-asesor {
	width: 70% !important;
	margin-left: 15% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media only screen and (max-width: 900px) {
	.slot-reporte-desembolsos-asesor {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>
