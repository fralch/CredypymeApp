<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-vacaciones" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HISTORIAL DE VACACIONES'"></headerClose>

					<div class="card-title">PANEL DE BUSQUEDA</div>
					<div class="card-body card-block">
						<div class="col form-group">
							<label for="text-input" class="form-control-label label-title"
								>POR FECHAS</label
							>
							<span
								class="btn btn-action btn-icon-split mb-1"
								@click="MesActual"
							>
								<span class="icon text-white">
									<i class="fas fa-search"></i>
								</span>
								<span class="text">VER ASIGNACIONES DEL MES</span></span
							>

							<div class="form-row">
								<div class="form-group col-xs-6">
									<label for="text-input" class="form-control-label label-title"
										>DESDE</label
									>
									<input
										class="form-control center"
										type="date"
										name="desde"
										id="dtpDesde"
										data-index="1"
										style="width: 150px"
										onkeydown="return false"
									/>
								</div>
								<div class="form-group col-xs-6">
									<label for="text-input" class="form-control-label label-title"
										>HASTA</label
									>
									<input
										class="form-control center"
										type="date"
										name="hasta"
										id="dtpHasta"
										data-index="1"
										style="width: 150px"
										onkeydown="return false"
									/>
								</div>
								<div class="form-group col-md-1 mt-4 col-2">
									<button
										class="btn btn-action btn-icon-split"
										@click="FiltrarFechas"
									>
										<span class="icon text-white">
											<i class="fas fa-search"></i>
										</span>
									</button>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-2">
									<label
										for="slcAgencias"
										class="form-control-label label-title"
										>AGENCIA</label
									>
									<select
										class="form-control center"
										name="slcAgencias"
										id="slcAgencias"
										style="max-width: 200px"
										data-index="2"
										:disabled="vacaciones_filtrados.length == 0"
									>
										<option :value="0">Todas</option>
										<option
											v-for="agencia in agencias"
											:key="agencia.id_agencia"
										>
											{{ agencia.nombre }}
										</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="card-title">RESULTADOS DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="input-group row col-md-10 col-7">
							<div class="input-group-prepend">
								<span class="input-group-text"
									><i class="fas fa-search"></i
								></span>
							</div>
							<input
								class="form-control mayus"
								type="text"
								id="inpBuscar"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>

						<table
							class="table table-hover"
							id="tblVacacionesAsignadas"
							width="100%"
						>
							<thead>
								<tr>
									<th style="width: 70px !important">VER</th>
									<th>COLABORADOR</th>
									<th>AGENCIA</th>
									<th>FECHA DESDE</th>
									<th>FECHA HASTA</th>
									<th>DÍAS TOMADOS</th>
									<th>FECHA ASIGNACIÓN</th>
									<th>USUARIO ASIGNACIÓN</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(asignacion, index) in vacaciones_filtrados"
									:key="index"
								>
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="VerDetalle(asignacion.periodo_id)"
										>
											<span class="icon text-white">
												<i class="far fa-eye" style="color: white"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="center">
										{{ asignacion.colaborador }}
									</td>
									<td class="table-bordered" align="center">
										{{ asignacion.nombre_agencia }}
									</td>
									<td class="table-bordered" align="center">
										{{ asignacion.desde }}
									</td>
									<td class="table-bordered" align="center">
										{{ asignacion.hasta }}
									</td>
									<td class="table-bordered" align="center">
										{{ roundTo(asignacion.dias_tomados, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{ asignacion.fecha_creacion }}
									</td>
									<td class="table-bordered" align="center">
										{{ asignacion.usuario_creacion }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- The Modal -->
			<div id="modalVacacionesDetalle" class="modal">
				<div class="modal-content w-50 modalVacacionesDetalle">
					<div class="content" style="display: block">
						<div class="card">
							<div class="card-header">
								<strong>DETALLE DE VACACIONES</strong>
							</div>
							<div class="card-body card-block">
								<label
									for="tblVacacionDetalle"
									class="form-control-label label-title"
									>PERIODOS INVOLUCRADOS</label
								>
								<table
									class="table table-hover"
									id="tblVacacionDetalle"
									width="100%"
								>
									<thead>
										<tr>
											<th>PERIODO DESDE</th>
											<th>PERIODO HASTA</th>
											<th>DÍAS TOMADOS</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(periodo, index) in periodo_filtrado"
											:key="index"
										>
											<td class="table-bordered" align="center">
												{{ periodo.periodo_desde }}
											</td>
											<td class="table-bordered" align="center">
												{{ periodo.periodo_hasta }}
											</td>
											<td class="table-bordered" align="center">
												{{ periodo.dias_tomados }}
											</td>
										</tr>
									</tbody>
								</table>

								<hr />
								<div class="text-right">
									<button class="btn btn-action btn-icon-split" id="btnAceptar">
										<span class="icon text-white">
											<i class="fas fa-check"></i>
										</span>
										<span class="text font-size-layout">Aceptar</span>
									</button>
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		agencias: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			vacaciones_filtrados: [],
			periodo_filtrado: [],
		};
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaVacacionesAsignadas();
		this.TablaVacacionesDetalle();
	},
	watch: {
		vacaciones_filtrados() {
			$("#tblVacacionesAsignadas").DataTable().destroy();
			this.TablaVacacionesAsignadas();
		},
		periodo_filtrado() {
			$("#tblVacacionDetalle").DataTable().destroy();
			this.TablaVacacionesDetalle();
		},
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblVacacionDetalle").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblVacacionDetalle")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblVacacionDetalle").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblVacacionDetalle")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				let power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},
		TablaVacacionesAsignadas() {
			this.$nextTick(() => {
				var table = $("#tblVacacionesAsignadas").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},

					scrollCollapse: true,
					paging: false,
					order: [1, "asc"],
					fixedHeader: true,
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
					responsive: true,
				});

				// Filter event handler

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
				$("#slcAgencias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});
			});
		},
		TablaVacacionesDetalle() {
			this.$nextTick(() => {
				var table = $("#tblVacacionDetalle").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},

					scrollCollapse: true,
					paging: false,
					order: [1, "asc"],
					fixedHeader: true,
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
					responsive: true,
				});
			});
		},

		MesActual() {
			let fecha_actual = this.$inertia.page.props.application.data.filter(
				(item) => item.descripcion == "FECHA_GTH"
			)[0].valorFecha;

			let fecha = new Date(fecha_actual); //Fecha actual
			fecha.setMinutes(fecha.getMinutes() + fecha.getTimezoneOffset()); //Ajustando hora de zona horaria

			let ano = fecha.getFullYear(); //obteniendo año
			let mes = fecha.getMonth() + 1; //obteniendo mes
			let dia_a = fecha.getUTCDate();
			if (mes < 10) mes = "0" + mes;

			let p_dia = 1; //obteniendo dia

			if (p_dia < 10) p_dia = "0" + p_dia;

			let u_dia = new Date(ano, mes, 0).getDate();
			if (u_dia < 10) u_dia = "0" + u_dia;

			if (dia_a < 10) dia_a = "0" + dia_a;

			let primer_dia = ano + "-" + mes + "-" + p_dia;
			let ultimo_dia = ano + "-" + mes + "-" + dia_a;

			$("#dtpDesde").val(primer_dia);
			$("#dtpHasta").val(ultimo_dia);

			//   this.FiltrarFechas();
		},
		FiltrarFechas() {
			self = this;
			let data = new FormData();
			data.append("dtpDesde", $("#dtpDesde").val());
			data.append("dtpHasta", $("#dtpHasta").val());

			axios
				.post(route("gth.pla.historial_vacaciones_listar"), data)
				.then(function (response) {
					self.vacaciones_filtrados = response.data;
				});
		},
		VerDetalle(periodo_id) {
			let self = this;
			this.periodo_filtrado = [];
			let periodos = JSON.parse(periodo_id);

			periodos.forEach((element) => {
				axios
					.post(route("gth.pla.historial_vacaciones.buscar_periodo"), {
						id_periodo: element.id,
					})
					.then(function (response) {
						let periodo_detalle = response.data;

						let object = {
							periodo_desde: periodo_detalle[0].periodo_desde,
							periodo_hasta: periodo_detalle[0].periodo_hasta,
							dias_tomados: element.dias_tomados,
						};
						self.periodo_filtrado.push(object);
					});
			});

			$("#modalVacacionesDetalle").css("display", "block");
			$("#btnAceptar").click(function () {
				$("#modalVacacionesDetalle").css("display", "none");
			});
		},
	},
};
</script>
<style >
.slot-historial-vacaciones {
	width: 60% !important;
	margin-left: 20% !important;
}
.modalVacacionesDetalle {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-historial-vacaciones {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.modalVacacionesDetalle {
		margin-top: 20%;
	}
}
</style>
