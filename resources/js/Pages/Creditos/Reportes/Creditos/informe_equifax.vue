<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-informe-equifax" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MI ' : '') + 'INFORME CENTRAL DE RIESGO'
						"
					></headerClose>

					<div class="card-body card-block">
						<fieldset class="form-group col-md-12">
							<legend>
								<span class="badge badge-success">FILTROS DE BÚSQUEDA</span>
							</legend>
							<div class="form-row form-inline">
								<div class="input-group col-md-3">
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

								<div class="input-group col-md-2">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbVigentes"
												v-model="vigentes"
											/>
										</div>
									</div>

									<div class="input-group-append">
										<label
											class="input-group-text prepend-title"
											for="chbVigentes"
										>
											CRÉD. VIGENTES
										</label>
									</div>
								</div>
								<div class="input-group col-md-2">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbCanceladoParcial"
												v-model="cancelado_parcial"
											/>
										</div>
									</div>

									<div class="input-group-append">
										<label
											class="input-group-text prepend-title"
											for="chbCanceladoParcial"
										>
											CANC. PARCIAL
										</label>
									</div>
								</div>
								<div class="input-group col-md-2">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbCanceladoTotal"
												v-model="cancelado_total"
											/>
										</div>
									</div>

									<div class="input-group-append">
										<label
											class="input-group-text prepend-title"
											for="chbCanceladoTotal"
										>
											CANC. TOTAL
										</label>
									</div>
								</div>
								<div class="input-group col-md-3" style="width: 250px">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbPorAsesor"
												v-model="por_asesor"
												:disabled="modo == 'personal'"
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
										v-model="asesor_seleccionado"
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
								</div>
							</div>
							<div class="form-row form-inline mt-3">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbPorDiasAtraso"
												v-model="por_dias_atraso"
											/>
										</div>
									</div>

									<div class="input-group-append">
										<label
											class="input-group-text prepend-title"
											for="chbPorDiasAtraso"
										>
											DÍAS DE ATRASO
										</label>
									</div>
								</div>

								<div class="input-group col-md-3" v-if="por_dias_atraso">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">DESDE</span>
									</div>
									<input
										type="number"
										class="form-control input-information center bolder"
										min="0"
										step="1"
										v-model="desde_dias"
										name="desde_dias"
										@change="Redondear"
										style="font-size: 14px; color: black"
									/>
									<div class="input-group-append">
										<span class="input-group-text" min="0" step="1">días</span>
									</div>
								</div>
								<div class="input-group col-md-3" v-if="por_dias_atraso">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">HASTA</span>
									</div>
									<input
										type="number"
										class="form-control input-information center bolder"
										min="0"
										step="1"
										v-model="hasta_dias"
										name="hasta_dias"
										@change="Redondear"
										style="font-size: 14px; color: black"
									/>
									<div class="input-group-append">
										<span class="input-group-text">días</span>
									</div>
								</div>
							</div>
						</fieldset>

						<fieldset class="form-group col-md-7">
							<legend>
								<span class="badge badge-success">FECHA DE DESEMBOLSO</span>
							</legend>
							<div class="form-row form-inline">
								<div class="input-group col-md-5">
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
								<div class="input-group col-md-5">
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
								<div class="col-md-1 ml-3">
									<button
										class="btn btn-action btn-icon-split"
										title="Buscar"
										@click="Buscar"
									>
										<span class="icon text-white" style="font-size: 20px">
											<i class="fas fa-search"></i>
										</span>
									</button>
								</div>
							</div>
						</fieldset>

						<div class="card-title">LISTA DE RESULTADOS</div>
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="resultados-tab"
									data-toggle="tab"
									href="#resultados"
									role="tab"
									aria-controls="resultados"
									aria-selected="true"
									>RESULTADOS DE BÚSQUEDA</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="reportar-tab"
									data-toggle="tab"
									href="#reportar"
									role="tab"
									aria-controls="reportar"
									aria-selected="false"
									>PERSONAS A REPORTAR</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="resultados"
								role="tabpanel"
								aria-labelledby="resultados-tab"
							>
								<div class="form-row mt-2 mb-2">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbBuscarCliente"
													v-model="buscar_cliente"
													:disabled="lista_creditos.length == 0"
												/>
											</div>
											<label
												class="input-group-text prepend-title"
												for="chbBuscarCliente"
												style="font-size: 13px"
											>
												BUSCAR CLIENTE
											</label>
										</div>

										<input
											type="text"
											class="form-control mayus"
											id="inpBusquedaEquifax"
											placeholder="Ingrese el nombre del cliente"
											:disabled="!buscar_cliente"
										/>
									</div>

									<div class="col-md-3">
										<button
											type="button"
											class="btn btn-action btn-icon-split"
											title="Exportar"
											@click="SeleccionarTodos"
											:disabled="lista_creditos.length == 0"
										>
											<span class="icon text-white">
												<i class="fas fa-check"></i
											></span>
											<span class="text">SELECCIONAR TODOS</span>
										</button>
									</div>
								</div>

								<table class="table mt-1" id="tblResultados" width="100%">
									<thead>
										<tr>
											<th style="min-width: 50px !important">N°_ESTADO</th>
											<th style="min-width: 220px !important">REPORTAR</th>
											<th style="min-width: 200px !important">CLIENTE</th>
											<th style="min-width: 70px !important">DNI</th>
											<th style="min-width: 100px !important">
												FECHA_DESEMBOLSO
											</th>
											<th style="min-width: 150px !important">ASESOR</th>
											<th style="min-width: 70px !important">ATRASO</th>
											<th style="min-width: 80px !important">SALDO_TOTAL</th>
											<th style="min-width: 100px !important">TIPO_CRED</th>
											<th style="min-width: 100px !important">CALIF.</th>
											<th style="min-width: 100px !important">DNI_AVAL</th>
											<th style="min-width: 200px !important">CLIENTE_AVAL</th>
											<th style="min-width: 100px !important">DNI_PARIENTE</th>
											<th style="min-width: 200px !important">
												CLIENTE_PARIENTE
											</th>
											<th style="min-width: 100px !important">DNI_PAR_AVAL</th>
											<th style="min-width: 200px !important">
												CLIENTE_PAR_AVAL
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_creditos"
											:key="index"
											class="table-bordered"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td align="center">
												{{
													index + 1 + " - " + abreviacion_estado(item.estado)
												}}
											</td>
											<td align="center">
												<div class="btn-group" role="group">
													<button
														class="btn btn-action"
														@click="Añadir(item, 'titular')"
														:disabled="
															lista_seleccionados.filter(
																(e) =>
																	e.cliente_id === item.cliente_id &&
																	e.credito_id === item.id
															).length > 0
														"
													>
														<span class="text">TIT.</span>
													</button>
													<button
														class="btn btn-cancel"
														style="margin-left: 1px"
														@click="Añadir(item, 'aval')"
														v-if="item.aval_id != null"
														:disabled="
															lista_seleccionados.filter(
																(e) =>
																	e.cliente_id === item.cliente_aval_id &&
																	e.credito_id === item.id
															).length > 0
														"
													>
														<span class="text">AVAL</span>
													</button>
													<button
														class="btn btn-cancel"
														style="margin-left: 1px"
														@click="Añadir(item, 'pariente')"
														v-if="item.pariente_id != null"
														:disabled="
															lista_seleccionados.filter(
																(e) =>
																	e.cliente_id === item.cliente_pariente_id &&
																	e.credito_id === item.id
															).length > 0
														"
													>
														<span class="text">PARI.</span>
													</button>
													<button
														class="btn btn-cancel"
														style="margin-left: 1px"
														@click="Añadir(item, 'pariente_aval')"
														v-if="item.pariente_aval_id != null"
														:disabled="
															lista_seleccionados.filter(
																(e) =>
																	e.cliente_id ===
																		item.cliente_pariente_aval_id &&
																	e.credito_id === item.id
															).length > 0
														"
													>
														<span class="text">P_AVA</span>
													</button>
												</div>
											</td>

											<td>
												{{
													item.apellido_paterno_titular +
													" " +
													item.apellido_materno_titular +
													" " +
													item.nombres_titular
												}}
											</td>
											<td align="center">
												{{ item.dni_titular }}
											</td>
											<td align="center">
												{{ item.fecha_desembolso }}
											</td>
											<td align="center">
												{{ item.usuario_asesor }}
											</td>
											<td align="center">
												{{ item.dias_atraso }}
											</td>
											<td align="right">
												S/
												{{ roundTo(item.saldo_total, 2) }}
											</td>
											<td align="center">
												{{ item.tipo }}
											</td>
											<td align="center">
												{{ item.tipo_riesgo }}
											</td>
											<td align="center">
												{{ item.aval_id == null ? "-" : item.dni_aval }}
											</td>
											<td>
												{{
													item.aval_id == null
														? "-"
														: item.apellido_paterno_aval +
														  " " +
														  item.apellido_materno_aval +
														  " " +
														  item.nombres_aval
												}}
											</td>

											<td align="center">
												{{ item.pariente_id == null ? "-" : item.dni_pariente }}
											</td>
											<td>
												{{
													item.pariente_id == null
														? "-"
														: item.apellido_paterno_pariente +
														  " " +
														  item.apellido_materno_pariente +
														  " " +
														  item.nombres_pariente
												}}
											</td>

											<td align="center">
												{{
													item.pariente_aval_id == null
														? "-"
														: item.dni_pariente_aval
												}}
											</td>
											<td>
												{{
													item.pariente_aval_id == null
														? "-"
														: item.apellido_paterno_pariente_aval +
														  " " +
														  item.apellido_materno_pariente_aval +
														  " " +
														  item.nombres_pariente_aval
												}}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div
								class="tab-pane fade"
								id="reportar"
								role="tabpanel"
								aria-labelledby="reportar-tab"
							>
								<button
									class="btn btn-danger mt-1"
									@click="QuitarTodo()"
									:disabled="lista_seleccionados.length == 0"
								>
									<span class="text">QUITAR TODOS</span>
								</button>

								<table class="table mt-1" id="tblSeleccionados" width="100%">
									<thead>
										<tr>
											<th style="min-width: 50px !important">N°_ESTADO</th>
											<th style="min-width: 80px !important">QUITAR</th>
											<th style="min-width: 80px !important">TIPO</th>
											<th style="min-width: 200px !important">CLIENTE</th>
											<th style="min-width: 70px !important">DNI</th>
											<th style="min-width: 150px !important">ASESOR</th>
											<th style="min-width: 70px !important">ATRASO</th>
											<th style="min-width: 80px !important">SALDO_TOTAL</th>
											<th style="min-width: 100px !important">TIPO_CRED</th>
											<th style="min-width: 100px !important">CALIF.</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_seleccionados"
											:key="index"
											class="table-bordered"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td align="center">
												{{
													index + 1 + " - " + abreviacion_estado(item.estado)
												}}
											</td>
											<td align="center">
												<button class="btn btn-danger" @click="Quitar(index)">
													<span class="text">QUITAR</span>
												</button>
											</td>
											<td align="center">
												{{ item.modo.toUpperCase() }}
											</td>
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
												{{ item.dni }}
											</td>
											<td align="center">
												{{ item.usuario_asesor }}
											</td>

											<td align="center">
												{{ item.dias_atraso }}
											</td>
											<td align="right">
												S/
												{{ roundTo(item.saldo_total, 2) }}
											</td>
											<td align="center">
												{{ item.tipo }}
											</td>
											<td align="center">
												{{ item.calificacion }}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />

								<div class="text-right">
									<div class="btn-group dropleft">
										<button
											type="button"
											class="btn btn-cancel dropdown-toggle"
											data-toggle="dropdown"
											aria-haspopup="true"
											aria-expanded="false"
											title="Exportar"
											:disabled="lista_seleccionados.length == 0"
										>
											<span class="text">EXPORTAR</span>
										</button>
										<div class="dropdown-menu">
											<a
												class="dropdown-item"
												href="#"
												@click.prevent="Exportar('EQUIFAX')"
												>EQUIFAX</a
											>
											<a
												class="dropdown-item"
												href="#"
												@click.prevent="Exportar('SENTINEL')"
												>SENTINEL</a
											>
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
	components: {
		layout,
		headerClose,
	},
	props: {
		modo: String,
		usuarios: Array,
	},

	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			usuarios_filtrados: [],

			vigentes: true,
			cancelado_parcial: false,
			cancelado_total: false,
			por_asesor: this.modo == "personal" ? true : false,
			asesor_seleccionado: 0,
			por_dias_atraso: false,
			desde_dias: 0,
			hasta_dias: 0,
			por_cliente: false,

			fecha_desde: null,
			fecha_hasta: null,

			buscar_cliente: false,

			lista_creditos: [],
			lista_seleccionados: [],
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
		agencia_seleccionada(value) {
			this.FechaActual();
		},
		por_asesor(value) {
			if (value) {
				this.FiltrarUsuarios();
			}
		},
		lista_creditos() {
			$("#tblResultados").DataTable().destroy();
			this.TablaResultados();
		},
		lista_seleccionados() {
			$("#tblSeleccionados").DataTable().destroy();
			this.TablaSeleccionados();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.FiltrarUsuarios();
		this.TablaResultados();
		this.TablaSeleccionados();
	},

	methods: {
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
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_INFORME_EQUIFAX"
				);
			} else if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MI_INFORME_EQUIFAX"
				);
			}
		},
		abreviacion_estado(value) {
			if (value == "DESEMBOLSADO") {
				return "VIG";
			} else if (value == "CANCELADO PARCIAL") {
				return "CPA";
			} else if (value == "CANCELADO TOTAL") {
				return "CTO";
			}
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			if (e.target.name == "desde_dias") {
				this.desde_dias = this.roundTo(valor, 0);
			} else if (e.target.name == "hasta_dias") {
				this.hasta_dias = this.roundTo(valor, 0);
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
		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.asesor_seleccionado = 0;

				if (this.por_asesor) {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) => item.agencia_id == this.agencia_seleccionada
					);
				}
			} else if (this.modo == "personal") {
				this.usuarios_filtrados = this.usuarios;
				this.asesor_seleccionado = this.usuarios[0].dni;
			}
		},
		TablaResultados() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblResultados").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: true,
					lengthChange: false,
					lengthMenu: [100],
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

				$("#inpBusquedaEquifax").keyup(function () {
					table.column(2).search(this.value).draw();
				});
			});
		},
		TablaSeleccionados() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblSeleccionados").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: true,
					lengthChange: false,
					lengthMenu: [100],
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
		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("modo", this.modo);
			data.append("vigentes", this.vigentes);
			data.append("cancelado_parcial", this.cancelado_parcial);
			data.append("cancelado_total", this.cancelado_total);
			data.append("por_asesor", this.por_asesor);
			data.append("por_dias_atraso", this.por_dias_atraso);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			if (this.por_asesor) {
				data.append(
					"asesor_seleccionado",
					JSON.stringify(this.asesor_seleccionado)
				);
			}

			if (this.por_dias_atraso) {
				data.append("desde_dias", this.desde_dias);
				data.append("hasta_dias", this.hasta_dias);
			}

			// this.$inertia.post(route("rep.cre.informe_equifax.buscar"), data);
			// return false;

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("rep.cre.informe_equifax.buscar"), data)
						.then(function (response) {
							self.lista_seleccionados = [];
							if (response.data.lista_creditos.length == 0) {
								self.lista_creditos = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_creditos = response.data.lista_creditos;
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
		Añadir(item, modo) {
			let object = {
				modo: modo,
				credito_id: item.id,
				estado: item.estado,
				usuario_asesor: item.usuario_asesor,
				dias_atraso: item.dias_atraso,
				saldo_total: item.saldo_total,
				tipo: item.tipo,
				calificacion: item.tipo_riesgo,
			};

			if (modo == "titular") {
				object.cliente_id = item.cliente_id;
				object.dni = item.dni_titular;
				object.apellido_paterno = item.apellido_paterno_titular;
				object.apellido_materno = item.apellido_materno_titular;
				object.nombres = item.nombres_titular;
				object.direccion = item.direccion_titular;
				object.distrito = item.distrito_titular;
				object.provincia = item.provincia_titular;
				object.departamento = item.departamento_titular;
			} else if (modo == "aval") {
				object.cliente_id = item.cliente_aval_id;
				object.dni = item.dni_aval;
				object.apellido_paterno = item.apellido_paterno_aval;
				object.apellido_materno = item.apellido_materno_aval;
				object.nombres = item.nombres_aval;
				object.direccion = item.direccion_aval;
				object.distrito = item.distrito_aval;
				object.provincia = item.provincia_aval;
				object.departamento = item.departamento_aval;
			} else if (modo == "pariente") {
				object.cliente_id = item.cliente_pariente_id;
				object.dni = item.dni_pariente;
				object.apellido_paterno = item.apellido_paterno_pariente;
				object.apellido_materno = item.apellido_materno_pariente;
				object.nombres = item.nombres_pariente;
				object.direccion = item.direccion_pariente;
				object.distrito = item.distrito_pariente;
				object.provincia = item.provincia_pariente;
				object.departamento = item.departamento_pariente;
			} else if (modo == "pariente_aval") {
				object.cliente_id = item.cliente_pariente_aval_id;
				object.dni = item.dni_pariente_aval;
				object.apellido_paterno = item.apellido_paterno_pariente_aval;
				object.apellido_materno = item.apellido_materno_pariente_aval;
				object.nombres = item.nombres_pariente_aval;
				object.direccion = item.direccion_pariente_aval;
				object.distrito = item.distrito_pariente_aval;
				object.provincia = item.provincia_pariente_aval;
				object.departamento = item.departamento_pariente_aval;
			}

			this.lista_seleccionados.push(object);
		},

		SeleccionarTodos() {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea SELECCIONAR todos los resultados?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let timerInterval;
					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						timer: 1000,
						timerProgressBar: true,
						willOpen: () => {
							Swal.showLoading();
							timerInterval = setInterval(() => {
								self.AñadirTodos();
							}, 100);
						},
						willClose: () => {
							clearInterval(timerInterval);
							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								timer: 1200,
								showConfirmButton: false,
							});
						},
					});
				}
			});
		},
		AñadirTodos() {
			this.lista_seleccionados = [];
			this.lista_creditos.forEach((item) => {
				let object_t = {
					modo: "titular",
					credito_id: item.id,
					estado: item.estado,
					usuario_asesor: item.usuario_asesor,
					dias_atraso: item.dias_atraso,
					saldo_total: item.saldo_total,
					tipo: item.tipo,
					calificacion: item.tipo_riesgo,
					cliente_id: item.cliente_id,
					dni: item.dni_titular,
					apellido_paterno: item.apellido_paterno_titular,
					apellido_materno: item.apellido_materno_titular,
					nombres: item.nombres_titular,
					direccion: item.direccion_titular,
					distrito: item.distrito_titular,
					provincia: item.provincia_titular,
					departamento: item.departamento_titular,
				};

				this.lista_seleccionados.push(object_t);

				if (item.aval_id != null) {
					let object_a = {
						modo: "aval",
						credito_id: item.id,
						estado: item.estado,
						usuario_asesor: item.usuario_asesor,
						dias_atraso: item.dias_atraso,
						saldo_total: item.saldo_total,
						tipo: item.tipo,
						calificacion: item.tipo_riesgo,

						cliente_id: item.cliente_aval_id,
						dni: item.dni_aval,
						apellido_paterno: item.apellido_paterno_aval,
						apellido_materno: item.apellido_materno_aval,
						nombres: item.nombres_aval,
						direccion: item.direccion_aval,
						distrito: item.distrito_aval,
						provincia: item.provincia_aval,
						departamento: item.departamento_aval,
					};

					this.lista_seleccionados.push(object_a);
				}

				if (item.pariente_id != null) {
					let object_p = {
						modo: "pariente",
						credito_id: item.id,
						estado: item.estado,
						usuario_asesor: item.usuario_asesor,
						dias_atraso: item.dias_atraso,
						saldo_total: item.saldo_total,
						tipo: item.tipo,
						calificacion: item.tipo_riesgo,

						cliente_id: item.cliente_pariente_id,
						dni: item.dni_pariente,
						apellido_paterno: item.apellido_paterno_pariente,
						apellido_materno: item.apellido_materno_pariente,
						nombres: item.nombres_pariente,
						direccion: item.direccion_pariente,
						distrito: item.distrito_pariente,
						provincia: item.provincia_pariente,
						departamento: item.departamento_pariente,
					};

					this.lista_seleccionados.push(object_p);
				}

				if (item.pariente_aval_id != null) {
					let object_p_a = {
						modo: "pariente_aval",
						credito_id: item.id,
						estado: item.estado,
						usuario_asesor: item.usuario_asesor,
						dias_atraso: item.dias_atraso,
						saldo_total: item.saldo_total,
						tipo: item.tipo,
						calificacion: item.tipo_riesgo,

						cliente_id: item.cliente_pariente_aval_id,
						dni: item.dni_pariente_aval,
						apellido_paterno: item.apellido_paterno_pariente_aval,
						apellido_materno: item.apellido_materno_pariente_aval,
						nombres: item.nombres_pariente_aval,
						direccion: item.direccion_pariente_aval,
						distrito: item.distrito_pariente_aval,
						provincia: item.provincia_pariente_aval,
						departamento: item.departamento_pariente_aval,
					};

					this.lista_seleccionados.push(object_p_a);
				}
			});
		},
		Quitar(index) {
			this.lista_seleccionados.splice(index, 1);
		},
		QuitarTodo() {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea QUITAR todos los créditos seleccionados?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let timerInterval;
					Swal.fire({
						title: "Espere por favor...",
						showConfirmButton: false,
						allowOutsideClick: false,
						timer: 500,
						timerProgressBar: true,
						willOpen: () => {
							Swal.showLoading();
							timerInterval = setInterval(() => {
								self.lista_seleccionados = [];
							}, 100);
						},
						willClose: () => {
							clearInterval(timerInterval);
							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								timer: 1200,
								showConfirmButton: false,
							});
						},
					});
				}
			});
		},
		Exportar(tipo) {
			let self = this;
			let data = new FormData();
			data.append("tipo", tipo);
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("lista_reporte", JSON.stringify(this.lista_seleccionados));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					// this.$inertia.post(route("rep.cre.informe_equifax.exportar"), data);
					// return false;

					Swal.showLoading();
					axios
						.post(route("rep.cre.informe_equifax.exportar"), data)
						.then(function (response) {
							let usuario = self.$page.props.user_session.usuario;
							let origin = window.location.origin;
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;

							if (tipo == "EQUIFAX") {
								link.download = "rptInformeEquifax_" + usuario + ".xlsx";
							} else if (tipo == "SENTINEL") {
								link.download = "rptInformeSentinel_" + usuario + ".xlsx";
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
.slot-reporte-informe-equifax {
	width: 75% !important;
	margin-left: 12.5% !important;
}

@media (max-width: 900px) {
	.slot-reporte-informe-equifax {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
