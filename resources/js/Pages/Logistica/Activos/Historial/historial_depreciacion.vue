<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-depreciacion" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HISTORIAL DE DEPRECIACIÓN'"></headerClose>
					<div class="card-title">PANEL DE BUSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row col-md-11">
							<div class="form-group col-md-3 col-6 offset-3">
								<label class="label-title">AGENCIA</label>

								<select
									class="form-control center mayus"
									v-model="agencia_seleccionada"
								>
									<option value="TODAS" selected>TODAS</option>
									<option
										v-for="(item, index) in agencias_permitidas"
										:key="index"
										:value="item.id"
									>
										{{ item.agencia }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-3 col-5">
								<label class="label-title">DESDE</label>
								<span
									v-if="submited && !$v.datos_fecha.fecha_desde.required"
									class="span-error-message"
								>
									*
								</span>

								<input
									class="form-control center"
									type="date"
									v-model="datos_fecha.fecha_desde"
									onkeydown="return false"
								/>
							</div>
							<div class="form-group col-md-3 col-5">
								<label class="label-title">HASTA</label>
								<span
									v-if="submited && !$v.datos_fecha.fecha_hasta.required"
									class="span-error-message"
								>
									*
								</span>

								<input
									class="form-control center"
									type="date"
									v-model="datos_fecha.fecha_hasta"
									onkeydown="return false"
								/>
							</div>
							<div class="form-group col-md-1 mt-2 col-2">
								<button
									class="btn btn-action mt-2"
									@click="Buscar"
									title="Buscar entre fechas"
								>
									<span class="icon text-white">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
							<div class="form-group col-md-1 mt-2 col-2">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="rdbModoVista"
										checked
										id="radioAgrupado"
										@change="ModoAgrupado"
									/>
									<label
										class="form-check-label label-title"
										for="radioAgrupado"
									>
										AGRUPADO
									</label>
								</div>
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="rdbModoVista"
										id="radioDetallado"
										@change="ModoDetallado"
									/>
									<label
										class="form-check-label label-title"
										for="radioDetallado"
									>
										DETALLADO
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="card-title">RESULTADOS DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="row mb-2">
							<div class="form-group col-md-4 col-4">
								<label class="label-title totales">{{
									"TOTAL INICIAL: S/ " + roundTo(totales.inicial, 2)
								}}</label>
							</div>
							<div class="form-group col-md-4 col-4">
								<label class="label-title totales">{{
									"TOTAL FINAL: S/ " + roundTo(totales.final, 2)
								}}</label>
							</div>
							<div class="form-group col-md-4 col-4">
								<label class="label-title totales">{{
									"TOTAL DEPRECIADO: S/ " + roundTo(totales.depreciado, 2)
								}}</label>
							</div>
						</div>
						<div id="ModoAgrupado">
							<div class="input-group row col-md-9 col-7" style="float: left">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									id="inpBuscar_1"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<table
								class="table table-hover"
								id="tblDepreciaciones"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th>FECHA_DESDE</th>
										<th>FECHA_HASTA</th>
										<th>AGENCIA</th>
										<th>TOTAL_INICIAL(S/)</th>
										<th>TOTAL_FINAL(S/)</th>
										<th>TOTAL_DEPRECIADO(S/)</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(item, index) in lista_depreciacion" :key="index">
										<td class="table-bordered" align="center">
											{{ item.fecha_desde }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.fecha_hasta }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.agencia }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.total_inicial, 2) }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.total_final, 2) }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.total_inicial - item.total_final, 2) }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="ModoDetallado">
							<div class="input-group row col-md-9 col-9" style="float: left">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									id="inpBuscar_2"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<table
								class="table table-hover"
								id="tblDepreciacionesDetalles"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th>FECHA_DESDE</th>
										<th>FECHA_HASTA</th>
										<th>AGENCIA</th>
										<th>CÓDIGO_DE_ACTIVO</th>
										<th>DESCRIPCIÓN</th>
										<th>CONDICIÓN</th>
										<th>VALOR_INICIAL(S/)</th>
										<th>VALOR_FINAL(S/)</th>
										<th>TOTAL_DEPRECIADO(S/)</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(item, index) in lista_depreciacion_detalle"
										:key="index"
									>
										<td class="table-bordered" align="center">
											{{ item.fecha_desde }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.fecha_hasta }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.agencia }}
										</td>
										<td class="table-bordered">
											{{ item.codigo }}
										</td>
										<td class="table-bordered">
											{{ item.descripcion }}
										</td>
										<td class="table-bordered">
											{{ item.condicion }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.valor_inicial, 2) }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.valor_final, 2) }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.valor_inicial - item.valor_final, 2) }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: {},
	data() {
		return {
			submited: false,
			agencia_seleccionada: "TODAS",
			lista_depreciacion: [],
			lista_depreciacion_detalle: [],
			totales: {
				inicial: 0,
				final: 0,
				depreciado: 0,
			},
			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},
			agencias: [],
			agencias_permitidas: [],
		};
	},
	validations: {
		agencia_seleccionada: { noZero },
		datos_fecha: {
			fecha_desde: { required },
			fecha_hasta: { required },
		},
	},
	mounted() {
		this.listar_agencias();
		this.MesActual();
		this.ModoAgrupado();
		this.TablaDepreciaciones();
		this.TablaDepreciacionesDetalles();
	},
	watch: {
		lista_depreciacion(value) {
			$("#tblDepreciaciones").DataTable().destroy();
			this.TablaDepreciaciones();

			const total_inicial = value
				.map((item) => item.total_inicial)
				.reduce((prev, curr) => parseFloat(prev) + parseFloat(curr), 0);

			const total_final = value
				.map((item) => item.total_final)
				.reduce((prev, curr) => parseFloat(prev) + parseFloat(curr), 0);

			this.totales.inicial = total_inicial;
			this.totales.final = total_final;
			this.totales.depreciado = total_inicial - total_final;
		},

		lista_depreciacion_detalle() {
			$("#tblDepreciacionesDetalles").DataTable().destroy();
			this.TablaDepreciacionesDetalles();
		},
	},
	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_ACTIVOS/HISTORIAL_DEPRECIACION"
			);
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, places) {
			return parseFloat(value).toFixed(places);
		},
		TablaDepreciaciones() {
			this.$nextTick(() => {
				var table = $("#tblDepreciaciones").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[2, "asc"]],
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
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
					],
				});
				$("#inpBuscar_2").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		TablaDepreciacionesDetalles() {
			this.$nextTick(() => {
				var table = $("#tblDepreciacionesDetalles").DataTable({
					scrollY: "250px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[2, "asc"]],
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
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
					],
				});
				$("#inpBuscar_2").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		MesActual() {
			let fecha_actual = this.$inertia.page.props.application.data.filter(
				(item) => item.descripcion == "FECHA_LOGISTICA"
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

			this.datos_fecha.fecha_desde = primer_dia;
			this.datos_fecha.fecha_hasta = ultimo_dia;
		},
		Buscar() {
			let self = this;

			this.submited = true;

			if (
				this.$v.agencia_seleccionada.$invalid ||
				this.$v.datos_fecha.$invalid
			) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});

				return false;
			} else {
				let data = new FormData();
				data.append("agencia_id", this.agencia_seleccionada);
				data.append("fecha_desde", this.datos_fecha.fecha_desde);
				data.append("fecha_hasta", this.datos_fecha.fecha_hasta);
				// this.$inertia.post(
				//   route("log.act.historial_depreciacion.buscar"),
				//   data
				// );
				axios
					.post(route("log.act.historial_depreciacion.buscar"), data)
					.then(function (response) {
						self.lista_depreciacion = response.data.lista_depreciacion;
						self.lista_depreciacion_detalle =
							response.data.lista_depreciacion_detalle;
						self.totales.inicial = 0;
						self.totales.final = 0;
						self.totales.depreciado = 0;
					});
			}
		},
		ModoAgrupado() {
			$("#ModoAgrupado").show();
			$("#ModoDetallado").hide();
		},
		ModoDetallado() {
			$("#ModoAgrupado").hide();
			$("#ModoDetallado").show();
		},
	},
};
</script>

<style lang="css">
.slot-historial-depreciacion {
	width: 70% !important;
	margin-left: 15% !important;
}

.totales {
	padding: 5px;
	background-color: var(--plomoOscuroEmpresarial);
	color: white;
	border-radius: 10px;
}
/* Para corregir bug de datatable */
.dataTable {
	width: 100% !important;
}
.dataTables_scrollHeadInner {
	width: 100% !important;
}
.DTFC_ScrollWrapper {
	height: auto !important;
}
/* --------------------------------- */

@media (max-width: 900px) {
	.slot-historial-depreciacion {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
