<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-cancelados-parcialmente"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="'REPORTE DE CREDITOS CANCELADOS PARCIALMENTE'"
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
									v-if="agencia_busqueda != 0 && agencia_busqueda != null"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="form-group col-md-12">
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
								v-if="filtro_usuario"
							>
								<div class="input-group mt-2">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select class="form-control center" v-model="agencia_filtro">
										<option
											v-for="item in agencias_permitidas"
											:key="item.id"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>

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

							<div :class="filtro_usuario ? 'col-md-9 pl-3' : 'col-md-12'">
								<div class="card-title">LISTA DE RESULTADOS</div>

								<table class="table" id="tblCancelados" width="100%">
									<thead>
										<tr>
											<th>N°</th>
											<th style="min-width: 200px !important">CLIENTE</th>
											<th style="min-width: 100px !important">ASESOR</th>
											<th style="min-width: 70px !important">EXPEDIENTE</th>
											<th style="min-width: 100px !important">FECHA_DEMB</th>
											<th style="min-width: 100px !important">CAPITAL</th>
											<th style="min-width: 70px !important">ULT_PAG</th>
											<th style="min-width: 100px !important">F_CANCELACIÓN</th>
											<th style="min-width: 80px !important">MORA_TOTAL</th>
											<th style="min-width: 100px !important">DEUDA_MORA</th>
											<th style="min-width: 80px !important">NOTIF_TOTAL</th>
											<th style="min-width: 80px !important">DEUDA_TOTAL</th>
											<th style="min-width: 100px !important">TIPO_CRED</th>
											<th style="min-width: 100px !important">VIG/VENC</th>
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
												{{ index + 1 }}
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
												{{ item.usuario_asesor }}
											</td>
											<td align="center">
												{{ item.codigo_expediente }}
											</td>
											<td align="center">
												{{ formato_fecha(item.fecha_desembolso) }}
											</td>
											<td align="right">
												S/ {{ roundTo(item.capital_total, 2) }}
											</td>
											<td align="center">
												{{ formato_fecha(item.fecha_ultimo_pago) }}
											</td>
											<td align="center">
												{{ formato_fecha(item.fecha_hora_cancelado) }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.mora_total, 2) }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.mora_total - item.mora_pagada, 2) }}
											</td>
											<td align="right">
												S/ {{ roundTo(item.notificaciones_total, 2) }}
											</td>
											<td align="right">
												S/ {{ roundTo(item.deuda_total, 2) }}
											</td>
											<td align="center">
												{{ item.tipo }}
											</td>
											<td align="center">
												{{
													hoy > item.fecha_vencimiento ? "VENCIDA" : "VIGENTE"
												}}
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
								:disabled="lista_creditos.length == 0"
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
		modo: String,
		usuarios: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_busqueda: 0,
			agencia_filtro: 0,

			fecha_desde: null,
			fecha_hasta: null,
			hoy: null,
			filtro_usuario: false,
			mostrar_habilitados: true,

			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			lista_creditos: [],
			totales: {
				total_capital: 0,
				total_interes: 0,
				cantidad_registros: 0,
			},
		};
	},
	watch: {
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
		agencia_filtro() {
			(this.usuarios_seleccionados = []), this.FiltrarUsuarios();
		},
		filtro_usuario() {
			this.FiltrarUsuarios();
		},
		lista_creditos() {
			$("#tblCancelados").DataTable().destroy();
			this.TablaCancelados();
		},
		usuarios_filtrados() {
			$("#tblUsuarios").DataTable().destroy();
			this.TablaUsuarios();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaCancelados();
	},
	methods: {
		async FechaActual() {
			if (this.agencia_busqueda == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_busqueda
				);
				this.hoy = fecha_actual;
				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CREDITOS_CANCELADOS_PARCIAL"
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
		TablaUsuarios() {
			this.$nextTick(() => {
				var table = $("#tblUsuarios").DataTable({
					scrollY: "250px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
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
		TablaCancelados() {
			this.$nextTick(() => {
				var table = $("#tblCancelados").DataTable({
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
		FiltrarUsuarios() {
			this.usuarios_filtrados = [];

			if (this.mostrar_habilitados) {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_filtro && item.habilitado == 1
				);
			} else {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_filtro
				);
			}
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
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
					// this.$inertia.post(
					// 	route("rep.cre.cancelados_parcial.buscar"),
					// 	data
					// );
					axios
						.post(route("rep.cre.cancelados_parcial.buscar"), data)
						.then(function (response) {
							// return Swal.fire({
							// 	icon: "success",
							//  	title: "¡Listo!",
							// });

							if (response.data.length == 0) {
								self.lista_creditos = [];
								self.totales = 0;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_creditos = response.data;
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
			let self = this;
			let data = new FormData();
			data.append("datos_tabla", JSON.stringify(this.lista_creditos));
			data.append("hoy", self.hoy);
			// this.$inertia.post(route("rep.cre.dias_mora.exportar"), data);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(
					// 	route("rep.caj.desembolso_auxiliar.exportar"),
					// 	data
					// );

					axios
						.post(route("rep.cre.cancelados_parcial.exportar"), data)
						.then(function (response) {
							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptCanceladosParcial.xlsx";

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
.slot-reporte-cancelados-parcialmente {
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
	.slot-reporte-cancelados-parcialmente {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


