<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-mora-asesor" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="(modo == 'personal' ? 'MI ' : '') + 'MORA POR ASESOR'"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-4">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-12">
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
								</div>
							</fieldset>

							<div class="col-md-1 ml-3">
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
							<div class="form-group col-md-12" v-if="modo == 'completo'">
								<div class="form-check">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbPorUsuario"
										v-model="filtro_usuario"
									/>
									<label class="label-title" for="chbPorUsuario"
										>Filtrar por asesor</label
									>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div
								class="col-md-3"
								style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)"
								v-if="filtro_usuario && modo != 'personal'"
							>
								<div class="form-check text-center mt-1">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbMostrarHabilitados"
										v-model="mostrar_habilitados"
										@change="FiltrarUsuarios"
									/>
									<label class="label-title" for="chbMostrarHabilitados"
										>Sólo habilitados</label
									>
								</div>
								<table class="table" id="tblUsuarios" width="100%">
									<thead>
										<tr>
											<th>USUARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in usuarios_filtrados"
											:key="index"
										>
											<td style="padding: 0px !important">
												<div class="custom-control custom-checkbox mt-2">
													<input
														type="checkbox"
														class="custom-control-input"
														:id="'chbUsuario_' + index"
														:value="item.dni"
														v-model="usuarios_seleccionados"
													/>
													<label
														class="custom-control-label ml-4"
														:for="'chbUsuario_' + index"
														>{{ item.usuario }}</label
													>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- -------------------------------------------------------- -->

							<div
								:class="
									filtro_usuario && modo != 'personal'
										? 'col-md-9 pl-3'
										: 'col-md-12'
								"
							>
								<div class="card-title">LISTA DE RESULTADOS</div>
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
											id="resumen-tab"
											data-toggle="tab"
											href="#resumen"
											role="tab"
											aria-controls="resumen"
											aria-selected="false"
											>RESUMEN</a
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
										<table class="table" id="tblMoraDetalle" width="100%">
											<thead>
												<tr>
													<th style="min-width: 30px !important">N°</th>
													<th style="min-width: 100px !important">ASESOR</th>
													<th style="min-width: 100px !important">
														TIPO_RIESGO
													</th>
													<th style="min-width: 50px !important">ATRASO</th>
													<th style="min-width: 250px !important">CLIENTE</th>
													<th style="min-width: 70px !important">TASA_INT</th>
													<th style="min-width: 80px !important">CAPITAL</th>
													<th style="min-width: 80px !important">
														SALDO_CAPITAL
													</th>
													<th style="min-width: 80px !important">
														SALDO_TOTAL
													</th>
													<th style="min-width: 100px !important">
														FECHA_ÚLTIMO_PAGO
													</th>
													<th style="min-width: 100px !important">
														PERIODO_PAGO
													</th>
													<th style="min-width: 100px !important">PLAZO</th>
												</tr>
											</thead>
											<tbody>
												<tr
													class="table-bordered"
													v-for="(item, index) in lista_detalle"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
												>
													<td align="center">{{ index + 1 }}</td>
													<td align="center">{{ item.usuario_asesor }}</td>
													<td align="center">{{ item.tipo_riesgo }}</td>
													<td align="center">{{ item.dias_atraso + " d" }}</td>
													<td>
														{{
															item.apellido_paterno +
															" " +
															item.apellido_materno +
															" " +
															item.nombres
														}}
													</td>
													<td align="center">
														{{ roundTo(item.tasa_interes, 2) }} %
													</td>

													<td align="right">
														S/ {{ roundTo(item.capital_total, 2) }}
													</td>
													<td align="right">
														S/
														{{
															roundTo(
																item.capital_total - item.capital_pagado,
																2
															)
														}}
													</td>
													<td align="right">
														S/ {{ roundTo(item.saldo_total, 2) }}
													</td>
													<td align="center">
														{{
															item.fecha_ultimo_pago == null
																? "-"
																: item.fecha_ultimo_pago
														}}
													</td>
													<td align="center">
														{{ item.periodo_pago }}
													</td>
													<td align="center">
														{{
															roundTo(item.plazo, 0) +
															" " +
															periodo_medicion(item.periodo_pago)
														}}
													</td>
												</tr>
											</tbody>
										</table>
										<hr />
										<div class="form-row">
											<div class="col-md-12 text-right">
												<button
													class="btn btn-cancel btn-icon-split"
													title="Exportar"
													@click="Exportar('detalle')"
													:disabled="lista_detalle.length == 0"
												>
													<span class="icon text-white">
														<i class="fas fa-file-excel"></i>
													</span>
													<span class="text">EXPORTAR</span>
												</button>
											</div>
										</div>
									</div>
									<div
										class="tab-pane fade"
										id="resumen"
										role="tabpanel"
										aria-labelledby="resumen-tab"
									>
										<table class="table" id="tblMoraResumen" width="100%">
											<thead>
												<tr>
													<th style="min-width: 30px !important">N°</th>
													<th style="min-width: 100px !important">ASESOR</th>
													<th style="min-width: 100px !important">
														TIPO_CARTERA
													</th>
													<th style="min-width: 70px !important">
														CANT_CRÉDITOS
													</th>

													<th style="min-width: 70px !important">TASA_PROM</th>
													<th style="min-width: 80px !important">
														CAPITAL_DESEMB
													</th>
													<th style="min-width: 80px !important">
														SALDO_CAPITAL
													</th>
													<th style="min-width: 80px !important">
														SALDO_TOTAL
													</th>
													<th style="min-width: 80px !important">% CARTERA</th>
												</tr>
											</thead>
											<tbody>
												<tr
													class="table-bordered"
													v-for="(item, index) in lista_resumen"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
												>
													<td align="center">{{ index + 1 }}</td>
													<td align="center">{{ item.usuario_asesor }}</td>
													<td align="center">{{ item.tipo_riesgo }}</td>
													<td align="center">{{ item.cantidad_creditos }}</td>
													<td align="center">
														{{ roundTo(item.tasa_promedio, 2) }} %
													</td>

													<td align="right">
														S/ {{ roundTo(item.capital_total, 2) }}
													</td>
													<td align="right">
														S/
														{{
															roundTo(
																item.capital_total - item.capital_pagado,
																2
															)
														}}
													</td>
													<td align="right">
														S/ {{ roundTo(item.saldo_total, 2) }}
													</td>

													<td align="center">
														{{ roundTo(item.porcentaje_cartera, 2) }} %
													</td>
												</tr>
											</tbody>
										</table>
										<hr />
										<div class="form-row">
											<div class="col-md-12 text-right">
												<button
													class="btn btn-cancel btn-icon-split"
													title="Exportar"
													@click="Exportar('resumen')"
													:disabled="lista_resumen.length == 0"
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
		modo: String,
		usuarios: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_busqueda: 0,

			filtro_usuario: this.modo == "personal" ? true : false,
			mostrar_habilitados: true,

			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			lista_detalle: [],
			lista_resumen: [],
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

		agencia_busqueda() {
			this.FiltrarUsuarios();
		},
		filtro_usuario() {
			this.FiltrarUsuarios();
		},
		lista_detalle() {
			$("#tblMoraDetalle").DataTable().destroy();
			this.TablaMoraDetalle();
		},
		lista_resumen() {
			$("#tblMoraResumen").DataTable().destroy();
			this.TablaMoraResumen();
		},
		usuarios_filtrados() {
			$("#tblUsuarios").DataTable().destroy();
			this.TablaUsuarios();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		this.TablaMoraDetalle();
		this.TablaMoraResumen();
	},
	methods: {
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
		ListarAgenciasPermitidas() {
			if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MORA_ASESOR"
				);
			} else if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MI_MORA_ASESOR"
				);
			}
		},

		TablaUsuarios() {
			this.$nextTick(() => {
				var table = $("#tblUsuarios").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
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
			});
		},
		TablaMoraDetalle() {
			this.$nextTick(() => {
				var table = $("#tblMoraDetalle").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: true,
					lengthChange: false,
					pageLength: 500,
					ordering: false,
					fixedHeader: true,
					info: true,
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
			});
		},
		TablaMoraResumen() {
			this.$nextTick(() => {
				var table = $("#tblMoraResumen").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: true,
					lengthChange: false,
					pageLength: 100,
					ordering: false,
					fixedHeader: true,
					info: true,
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
			});
		},

		FiltrarUsuarios() {
			this.usuarios_filtrados = [];
			this.usuarios_seleccionados = [];

			if (this.filtro_usuario) {
				this.usuarios_filtrados = [];

				if (this.mostrar_habilitados) {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) =>
							item.agencia_id == this.agencia_busqueda && item.habilitado == 1
					);
				} else {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) => item.agencia_id == this.agencia_busqueda
					);
				}

				if (this.modo == "personal") {
					this.usuarios_seleccionados.push(this.usuarios[0].dni);
				}
			}
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("filtro_usuario", this.filtro_usuario);

			if (this.filtro_usuario) {
				data.append("usuarios", JSON.stringify(this.usuarios_seleccionados));
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("rep.cre.mora_asesor.buscar"), data);
					// return false
					axios
						.post(route("rep.cre.mora_asesor.buscar"), data)
						.then(function (response) {
							if (response.data.lista_detalle.length == 0) {
								self.lista_detalle = [];
								self.lista_resumen = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_detalle = response.data.lista_detalle;
								self.lista_resumen = response.data.lista_resumen;
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
			let data = new FormData();
			data.append("modo", modo);

			if (modo == "detalle") {
				data.append("lista_datos", JSON.stringify(this.lista_detalle));
				data.append("agencia_id", this.agencia_busqueda);
				data.append("tipo", this.modo);
			} else if (modo == "resumen") {
				data.append("lista_datos", JSON.stringify(this.lista_resumen));
				data.append("agencia_id", this.agencia_busqueda);
				data.append("tipo", this.modo);
			}

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(route("rep.cre.mora_asesor.exportar"), data);
					// return false

					axios
						.post(route("rep.cre.mora_asesor.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							if (modo == "detalle") {
								link.download = "rptMoraAsesorDetalle.xlsx";
							} else if (modo == "resumen") {
								link.download = "rptMoraAsesorResumen.xlsx";
							}

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
.slot-reporte-mora-asesor {
	width: 60% !important;
	margin-left: 20% !important;
}

@media only screen and (max-width: 900px) {
	.slot-reporte-mora-asesor {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


