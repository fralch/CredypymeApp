<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-desembolsos-asesor"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CRÉDITOS ANULADOS'"></headerClose>

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

							<!-- ------ -->
						</div>
						<div class="form-row">
							<!-- -------------------------------------------------------- -->

							<div :class="'col-md-12'">
								<div class="card-title">LISTA DE RESULTADOS</div>

								<table class="table" id="tblAprobaciones" width="100%">
									<thead>
										<tr>
											<th style="min-width: 50px !important">N°</th>
											<th style="min-width: 300px !important">CLIENTE</th>
											<th style="min-width: 80px !important">MONTO</th>
											<th style="min-width: 80px !important">PLAZO</th>
											<th style="min-width: 80px !important">TASA</th>
											<th style="min-width: 100px !important">ASESOR</th>
											<th style="min-width: 80px !important">CUOTA</th>
											<th style="min-width: 150px !important">
												FECHA_APROBACIÓN
											</th>
											<th style="min-width: 150px !important">
												FECHA_ANULACIÓN
											</th>
											<th style="min-width: 150px !important">
												USUARIO_ANULACIÓN
											</th>
											<th style="min-width: 200px !important">
												COMENTARIO_ANULACIÓN
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_aprobaciones"
											:key="index"
											class="table-bordered"
											:class="index % 2 == 0 ? 'verde-claro' : ''"
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
											<td align="center">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">
												{{
													roundTo(item.plazo, 0) +
													" " +
													periodo_medicion(item.periodo_pago)
												}}
											</td>
											<td align="center">
												{{ roundTo(item.tasa_interes, 2) }} %
											</td>
											<td align="center">
												{{ item.usuario_asesor }}
											</td>
											<td align="center">S/ {{ roundTo(item.cuota, 2) }}</td>
											<td align="center">
												{{
													JSON.parse(item.datos_creacion)
														.fecha.toString()
														.substring(0, 10)
												}}
											</td>
											<td align="center">
												{{
													JSON.parse(item.datos_actualizacion)
														.fecha.toString()
														.substring(0, 10)
												}}
											</td>
											<td align="center">
												{{ item.usuario_registro }}
											</td>
											<td align="center">
												{{ item.comentario_anulacion }}
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
								:disabled="lista_aprobaciones.length == 0"
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
	props: {},
	data() {
		return {
			agencias_permitidas: [],
			agencia_busqueda: 0,
			agencia_filtro: 0,

			fecha_desde: null,
			fecha_hasta: null,

			lista_aprobaciones: [],
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

		lista_aprobaciones() {
			$("#tblAprobaciones").DataTable().destroy();
			this.TablaAprobaciones();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaAprobaciones();
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
				"CREDITOS_REPORTES/CREDITOS_ANULADOS"
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

		TablaAprobaciones() {
			this.$nextTick(() => {
				var table = $("#tblAprobaciones").DataTable({
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
				if (this.lista_aprobaciones.length > 0) {
					$("#tblAprobaciones .dataTables_empty").css("display", "none");
				}
			});
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.cre.aprobaciones_anuladas.buscar"), data)
						.then(function (response) {
							if (response.data.length == 0) {
								self.lista_aprobaciones = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_aprobaciones = response.data;
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
			data.append(
				"creditos_filtrados",
				JSON.stringify(this.lista_aprobaciones)
			);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(
							route("rep.cre.aprobaciones_anuladas.exportar_anulados"),
							data
						)
						.then(function (response) {
							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptCreditosAnulados.xlsx";

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
	.slot-reporte-desembolsos-asesor {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


