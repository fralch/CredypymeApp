<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-compromisos-notificaciones"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'completo' ? '' : 'MIS ') +
							'COMPROMISOS Y NOTIFICACIONES'
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
											v-model="agencia_seleccionada"
										>
											<option value="0" selected disabled>
												Seleccionar agencia
											</option>
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
											class="form-control center"
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
											class="form-control center"
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

							<div class="form-row col-md-12">
								<div class="form-check" v-if="modo == 'completo'">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbPorUsuario"
										v-model="mas_filtros"
									/>
									<label class="label-title" for="chbPorUsuario"
										>Más filtros</label
									>
								</div>
								<div class="input-group col-md-4" v-if="mas_filtros == true">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="radio"
												id="chbPorAsesor"
												v-model="tipo_filtro"
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
										:disabled="tipo_filtro != 'asesor'"
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

								<div
									class="input-group col-md-4"
									v-if="mas_filtros == true && modo == 'completo'"
								>
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="radio"
												id="chbPorEstado"
												v-model="tipo_filtro"
												value="usuario"
											/>
										</div>
										<label
											class="input-group-text prepend-title"
											for="chbPorEstado"
											style="font-size: 13px"
										>
											USUARIO REGISTRO
										</label>
									</div>

									<select
										class="form-control center"
										v-model="usuario_seleccionado"
										:disabled="tipo_filtro != 'usuario'"
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
						</div>
						<div class="card-title mt-2">LISTA DE RESULTADOS</div>
						<div class="card-body card-block">
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item" role="presentation">
									<a
										class="tab-title nav-link active"
										id="notificaciones-tab"
										data-toggle="tab"
										href="#notificaciones"
										role="tab"
										aria-controls="notificaciones"
										aria-selected="true"
										>NOTIFICACIONES</a
									>
								</li>
								<li class="nav-item" role="presentation">
									<a
										class="tab-title nav-link"
										id="compromisos-tab"
										data-toggle="tab"
										href="#compromisos"
										role="tab"
										aria-controls="compromisos"
										aria-selected="false"
										>COMPROMISOS</a
									>
								</li>
							</ul>
							<div class="tab-content" id="myTabContent">
								<div
									class="tab-pane fade show active"
									id="notificaciones"
									role="tabpanel"
									aria-labelledby="notificaciones-tab"
								>
									<table class="table" id="tblNotificaciones" width="100%">
										<thead>
											<tr>
												<th style="min-width: 30px !important">N°</th>
												<th style="min-width: 80px !important">EXPEDIENTE</th>
												<th style="min-width: 200px !important">CLIENTE</th>
												<th style="min-width: 75px !important">CAPITAL</th>
												<th style="min-width: 70px !important">PLAZO</th>
												<th style="min-width: 250px !important">
													TIPO_NOTIFICACIÓN
												</th>
												<th style="min-width: 70px !important">MONTO</th>
												<th style="min-width: 100px !important">FECHA_REG.</th>
												<th style="min-width: 70px !important">USUARIO_REG.</th>
												<th style="min-width: 70px !important">ATRASO</th>
												<th style="min-width: 70px !important">ASESOR</th>
												<th style="min-width: 100px !important">
													FECHA_DESEMBOLSO
												</th>
												<th style="min-width: 70px !important">MONTO_CUOTA</th>
												<th style="min-width: 70px !important">CU_x_PAG</th>
												<th style="min-width: 70px !important">CU_VENC</th>
												<th style="min-width: 70px !important">MCU_VEN</th>
												<th style="min-width: 70px !important">MORA_ACUM</th>
												<th style="min-width: 70px !important">SALDO_TOTAL</th>
												<th style="min-width: 100px !important">ULT_PAG</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in lista_notificaciones"
												:key="index"
												class="table-bordered"
												:class="index % 2 == 0 ? 'verde-claro' : ''"
											>
												<td align="center">
													{{ index + 1 }}
												</td>
												<td align="center">
													{{ item.codigo_expediente }} -
													{{ item.numero_credito }}
												</td>
												<!-- <td align="center">
														{{ formato_fecha(item.fecha_nacimiento) }}
													</td> -->
												<td>
													{{
														item.apellido_paterno +
														" " +
														item.apellido_materno +
														" " +
														item.nombres
													}}
												</td>
												<td align="right">
													S/ {{ roundTo(item.capital_total, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item.plazo, 0) }}
													{{ tipo_plazo(item.periodo_pago) }}
												</td>
												<td align="left">
													{{ item.tipo }}
												</td>
												<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
												<td align="center">
													{{
														formato_fecha(JSON.parse(item.datos_creacion).fecha)
													}}
												</td>
												<td align="center">
													{{ item.usuario_registro }}
												</td>
												<td align="center">{{ item.dias_atraso }} d</td>
												<td align="center">
													{{ item.usuario_asesor }}
												</td>
												<td align="center">
													{{
														formato_fecha(
															JSON.parse(item.fecha_desembolso).fecha
														)
													}}
												</td>
												<td align="right">
													S/ {{ roundTo(item.monto_cuota, 2) }}
												</td>
												<td align="center">
													{{ item.CuXpag }}
												</td>
												<td align="center">
													{{ item.CuVenc }}
												</td>
												<td align="right">S/ {{ roundTo(item.MCuVenc, 2) }}</td>
												<td align="right">
													S/ {{ roundTo(item.mora_acumulado, 2) }}
												</td>
												<td align="right">
													S/ {{ roundTo(item.saldo_total, 2) }}
												</td>
												<td align="center">
													{{ formato_fecha(item.fecha_ultimo_pago) }}
												</td>
											</tr>
										</tbody>
									</table>
									<hr />
									<div class="text-right">
										<button
											class="btn btn-cancel btn-icon-split"
											title="Exportar"
											@click="Exportar('notificaciones')"
											:disabled="lista_notificaciones.length == 0"
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
									id="compromisos"
									role="tabpanel"
									aria-labelledby="compromisos-tab"
								>
									<table class="table" id="tblCompromisos1" width="100%">
										<thead>
											<tr>
												<th style="min-width: 30px !important">N°</th>
												<th style="min-width: 200px !important">CLIENTE</th>
												<th style="min-width: 110px !important">EXPEDIENTE</th>
												<th style="min-width: 250px !important">COMENTARIO</th>
												<th style="min-width: 100px !important">FECHA_REG</th>
												<th style="min-width: 100px !important">USUARIO_REG</th>
												<th style="min-width: 70px !important">FECHA_VENCI</th>
												<th style="min-width: 100px !important">ASESOR</th>
												<th style="min-width: 70px !important">CANT_COMPRO</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, i) in lista_compromisos"
												:key="i"
												class="table-bordered"
												:class="i % 2 == 0 ? 'verde-claro' : ''"
											>
												<td align="center">
													{{ i + 1 }}
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
													{{ item.codigo_expediente }} -
													{{ item.numero_credito }}
												</td>
												<td align="left">
													{{ item.compromiso }}
												</td>
												<td align="center">
													{{
														formato_fecha(JSON.parse(item.datos_creacion).fecha)
													}}
												</td>
												<td align="center">
													{{ item.usuario_registro }}
												</td>
												<td align="center">
													{{ formato_fecha(item.fecha_vencimiento) }}
												</td>
												<td align="center">
													{{ item.usuario_asesor }}
												</td>
												<td align="center">
													{{ item.cantidad_compromisos }}
												</td>
											</tr>
										</tbody>
									</table>
									<hr />
									<div class="text-right">
										<button
											class="btn btn-cancel btn-icon-split"
											title="Exportar"
											@click="Exportar('compromisos')"
											:disabled="lista_compromisos.length == 0"
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

						<!-- ---- -->
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
		asesores: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			fecha_desde: null,
			fecha_hasta: null,

			mas_filtros: this.modo == "personal" ? true : false,
			tipo_filtro: "asesor",

			usuarios_filtrados: [],
			asesores_filtrados: [],

			usuario_seleccionado: 0,
			asesor_seleccionado: 0,

			lista_notificaciones: [],
			lista_compromisos: [],
		};
	},
	watch: {
		mas_filtros() {
			this.filtrar_usuarios();
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
		lista_compromisos() {
			$("#tblCompromisos1").DataTable().destroy();
			this.TablaCompromisos();
		},

		lista_notificaciones() {
			$("#tblNotificaciones").DataTable().destroy();
			this.TablaNotificaciones();
		},

		tipo_filtro() {
			if (this.tipo_filtro == "asesor") {
				this.usuario_seleccionado = 0;
				this.asesor_seleccionado = 0;
			}
			if (this.tipo_filtro == "usuario") {
				this.asesor_seleccionado = 0;
				this.usuario_seleccionado = 0;
			}
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaCompromisos();

		this.TablaNotificaciones();

		this.FechaActual();
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
					String(value).substring(0, 4) +
					" " +
					String(value).substring(11, 19)
				);
			} else {
				return null;
			}
		},
		filtrar_usuarios() {
			this.usuarios_filtrados = this.usuarios.filter(
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
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MIS_COMPROMISOS_NOTIFICACIONES"
				);
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_COMPROMISOS_NOTIFICACIONES"
				);
			}
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

		TablaCompromisos() {
			this.$nextTick(() => {
				var table = $("#tblCompromisos1").DataTable({
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
		TablaNotificaciones() {
			this.$nextTick(() => {
				var table = $("#tblNotificaciones").DataTable({
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
			data.append("mas_filtros", this.mas_filtros);

			if (this.mas_filtros) {
				data.append("tipo_filtro", this.tipo_filtro);
				if (this.tipo_filtro == "asesor") {
					data.append("asesor_id", this.asesor_seleccionado);
				} else if (this.tipo_filtro == "usuario") {
					data.append("usuario_registro", this.usuario_seleccionado);
				}
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(
					// 	route("rep.cre.compromisos_notificaciones.buscar"),
					// 	data
					// );
					axios
						.post(route("rep.cre.compromisos_notificaciones.buscar"), data)
						.then(function (response) {
							if (
								response.data.lista_notificaciones.length == 0 &&
								response.data.lista_compromisos.length == 0
							) {
								self.lista_notificaciones = [];
								self.lista_compromisos = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_notificaciones = response.data.lista_notificaciones;
								self.lista_compromisos = response.data.lista_compromisos;
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
			if (modo == "notificaciones") {
				let data = new FormData();
				data.append(
					"lista_notificaciones",
					JSON.stringify(this.lista_notificaciones)
				);
				data.append("fecha_desde", this.fecha_desde);
				data.append("fecha_hasta", this.fecha_hasta);
				data.append("agencia_id", this.agencia_seleccionada);

				data.append("tipo", "notificaciones");
				Swal.fire({
					title: "EXPORTANDO",
					text: "Espere porfavor...",
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();

						axios
							.post(route("rep.cre.compromisos_notificaciones.exportar"), data)
							.then(function (response) {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								link.download = "rptNotificaciones.xlsx";
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
			}
			if (modo == "compromisos") {
				let data = new FormData();
				data.append(
					"lista_compromisos",
					JSON.stringify(this.lista_compromisos)
				);
				data.append("fecha_desde", this.fecha_desde);
				data.append("fecha_hasta", this.fecha_hasta);
				data.append("agencia_id", this.agencia_seleccionada);
				data.append("tipo", "compromisos");
				Swal.fire({
					title: "EXPORTANDO",
					text: "Espere porfavor...",
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();

						axios
							.post(route("rep.cre.compromisos_notificaciones.exportar"), data)
							.then(function (response) {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								link.download = "rptCompromisos.xlsx";
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
			}
		},
	},
};
</script>

<style lang="css">
.slot-reporte-compromisos-notificaciones {
	width: 60% !important;
	margin-left: 20% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media only screen and (max-width: 900px) {
	.slot-reporte-compromisos-notificaciones {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


