<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-envios" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HISTORIAL DE ENVÍOS'"></headerClose>
					<div class="card-title">PANEL DE BUSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row col-md-12">
							<div class="form-group col-md-3 col-6 offset-3">
								<label class="label-title">AGENCIA</label>
								<span
									v-if="submited && !$v.agencia_seleccionada.noZero"
									class="span-error-message"
								>
									*
								</span>

								<select
									class="form-control center mayus"
									v-model="agencia_seleccionada"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in agencias_permitidas"
										:key="index"
										:value="item.id"
									>
										{{ item.agencia }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-2 col-5">
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
							<div class="form-group col-md-2 col-5">
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
										id="radioDetallado"
										name="rdbModoVista"
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
						<div id="ModoAgrupado">
							<div class="input-group row col-md-9 col-9" style="float: left">
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
								id="tblEnvios"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th style="width: 70px !important">DETALLE</th>
										<th>FECHA_ENVÍO</th>
										<th>AGENCIA_ENVÍO</th>
										<th>USUARIO_ENVÍO</th>
										<th>SITUACIÓN</th>
										<th>FECHA_RECEPCIÓN</th>
										<th>AGENCIA_RECEPCIÓN</th>
										<th>RESPONSABLE_RECEPCIÓN</th>
										<th>UBICACIÓN_RECEPCIÓN</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(item, index) in lista_envios" :key="index">
										<td class="table-bordered" align="center">
											<button
												class="btn btn-action btn-icon-split"
												@click="VerDetalle(item)"
											>
												<span class="icon text-white">
													<i class="far fa-eye"></i>
												</span>
											</button>
										</td>
										<td class="table-bordered" align="center">
											{{ JSON.parse(item.datos_creacion).fecha }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.nombre_agencia_envio }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.usuario_envio }}
										</td>
										<td
											class="table-bordered"
											:class="[
												item.situacion == 'PENDIENTE'
													? 'pendiente'
													: item.situacion == 'CONFIRMADO'
													? 'confirmado'
													: 'rechazado',
											]"
											align="center"
										>
											{{ item.situacion }}
										</td>
										<td class="table-bordered" align="center">
											{{
												item.situacion == "PENDIENTE"
													? "-"
													: JSON.parse(item.datos_actualizacion).fecha
											}}
										</td>

										<td class="table-bordered" align="center">
											{{ item.nombre_agencia_recepcion }}
										</td>
										<td class="table-bordered" align="center">
											{{
												item.abreviacion_responsable +
												" - " +
												item.usuario_responsable
											}}
										</td>
										<td class="table-bordered" align="center">
											{{
												item.abreviacion_ubicacion +
												" - " +
												item.nombre_agencia_recepcion
											}}
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
								id="tblEnviosDetalles"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th>FECHA_ENVÍO</th>
										<th>AGENCIA_ENVÍO</th>
										<th>USUARIO_ENVÍO</th>
										<th>FECHA_RECEPCIÓN</th>
										<th>AGENCIA_RECEPCIÓN</th>
										<th>RESPONSABLE_RECEPCIÓN</th>
										<th>UBICACIÓN_RECEPCIÓN</th>

										<th>SITUACIÓN</th>
										<th>CÓDIGO_DE_ACTIVO</th>
										<th>DESCRIPCIÓN</th>
										<th>CANTIDAD</th>
										<th>VALOR_UNIT(S/)</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(item, index) in lista_envios_detalles"
										:key="index"
									>
										<td class="table-bordered" align="center">
											{{ JSON.parse(item.datos_creacion).fecha }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.nombre_agencia_envio }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.usuario_envio }}
										</td>

										<td class="table-bordered" align="center">
											{{
												item.situacion == "PENDIENTE"
													? "-"
													: JSON.parse(item.datos_actualizacion).fecha
											}}
										</td>
										<td class="table-bordered" align="center">
											{{ item.nombre_agencia_recepcion }}
										</td>
										<td class="table-bordered" align="center">
											{{
												item.abreviacion_responsable +
												" - " +
												item.usuario_responsable
											}}
										</td>
										<td class="table-bordered" align="center">
											{{
												item.abreviacion_ubicacion +
												" - " +
												item.nombre_agencia_recepcion
											}}
										</td>
										<td
											class="table-bordered"
											:class="[
												item.situacion == 'PENDIENTE'
													? 'pendiente'
													: item.situacion == 'CONFIRMADO'
													? 'confirmado'
													: 'rechazado',
											]"
											align="center"
										>
											{{ item.situacion }}
										</td>
										<td class="table-bordered">
											{{ item.codigo }}
										</td>
										<td class="table-bordered">
											{{ item.descripcion }}
										</td>
										<td class="table-bordered" align="center">
											{{ roundTo(item.cantidad, 2) }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.valor_actual, 2) }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!-- The Modal -->
			<div id="mdlEnvioDetalle" class="modal">
				<div class="modal-content w-50 mdlEnvioDetalle">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'DETALLE DE ENVÍO'"
								:nombre_modal="'mdlEnvioDetalle'"
							>
							</headerCloseModal>
							<div class="card-body card-block">
								<div class="input-group row col-md-9 col-9" style="float: left">
									<div class="input-group-prepend">
										<span class="input-group-text"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscar_3"
										autocomplete="off"
										spellcheck="false"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
								<table class="table table-hover" id="tblEnvioDetalle">
									<thead>
										<tr>
											<th>SITUACIÓN</th>
											<th>FECHA_RECEPCIÓN</th>
											<th>CÓDIGO_DE_ACTIVO</th>
											<th>DESCRIPCIÓN</th>
											<th>CANTIDAD</th>
											<th>VALOR_UNIT(S/)</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in envio_detalle" :key="index">
											<td
												class="table-bordered"
												:class="[
													item.situacion == 'PENDIENTE'
														? 'pendiente'
														: item.situacion == 'CONFIRMADO'
														? 'confirmado'
														: 'rechazado',
												]"
												align="center"
											>
												{{ item.situacion }}
											</td>
											<td class="table-bordered" align="center">
												{{
													item.situacion == "PENDIENTE"
														? "-"
														: JSON.parse(item.datos_actualizacion).fecha
												}}
											</td>
											<td class="table-bordered">
												{{ item.codigo }}
											</td>
											<td class="table-bordered">
												{{ item.descripcion }}
											</td>
											<td class="table-bordered" align="center">
												{{ roundTo(item.cantidad, 2) }}
											</td>
											<td class="table-bordered" align="right">
												{{ roundTo(item.valor_actual, 2) }}
											</td>
										</tr>
									</tbody>
								</table>
								<div class="text-right">
									<label class="form-control-label label-title"
										>DOCUMENTO ENVÍO:</label
									>
									<button
										class="btn btn-action btn-icon-split"
										title="Descargar DOCUMENTO DE ENVÍO"
										@click="Descargar(documento_envio)"
									>
										<span class="icon text-white">
											<i class="fas fa-download"></i>
										</span>
										<span class="text">DESCARGAR</span>
									</button>
								</div>
								<div class="text-right mt-2">
									<label class="form-control-label label-title"
										>DOCUMENTO RECEPCIÓN:</label
									>
									<button
										class="btn btn-action btn-icon-split"
										title="Descargar DOCUMENTO DE RECEPCIÓN"
										@click="Descargar(documento_recepcion)"
										:disabled="documento_recepcion == null"
									>
										<span class="icon text-white">
											<i class="fas fa-download"></i>
										</span>
										<span class="text">DESCARGAR</span>
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
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {},
	data() {
		return {
			submited: false,
			agencia_seleccionada: 0,
			lista_envios: [],
			envio_detalle: [],
			lista_envios_detalles: [],
			documento_envio: null,
			documento_recepcion: null,
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
		this.TablaEnvios();
		this.TablaEnviosDetalles();
	},
	watch: {
		lista_envios() {
			$("#tblEnvios").DataTable().destroy();
			this.TablaEnvios();
		},
		envio_detalle() {
			$("#tblEnvioDetalle").DataTable().destroy();
			this.TablaEnvioDetalle();
		},
		lista_envios_detalles() {
			$("#tblEnviosDetalles").DataTable().destroy();
			this.TablaEnviosDetalles();
		},
	},
	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_ACTIVOS/HISTORIAL_ENVIOS"
			);
		},
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
		TablaEnvios() {
			this.$nextTick(() => {
				var table = $("#tblEnvios").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[1, "desc"]],
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
		TablaEnvioDetalle() {
			this.$nextTick(() => {
				var table = $("#tblEnvioDetalle").DataTable({
					scrollY: "250px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[1, "desc"]],
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
				$("#inpBuscar_3").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		TablaEnviosDetalles() {
			this.$nextTick(() => {
				var table = $("#tblEnviosDetalles").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[0, "desc"]],
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
				// this.$inertia.post(route("log.sum.historial_envios.buscar"), data);
				axios
					.post(route("log.act.historial_envios.buscar"), data)
					.then(function (response) {
						self.lista_envios = response.data.lista_envios;
						self.lista_envios_detalles = response.data.lista_envios_detalles;
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
		VerDetalle(item) {
			this.envio_detalle = this.lista_envios_detalles.filter(
				(item_1) => item_1.envio_id == item.id
			);

			this.documento_envio = item.documento_envio;
			this.documento_recepcion = item.documento_recepcion;

			$("#mdlEnvioDetalle").css("display", "block");
		},
		Descargar(documento) {
			let self = this;
			let source =
				"/imagenes_server/logistica/activos/envios_recepciones/" +
				documento +
				"";
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], { type: response.data.type });
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = documento;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},
	},
};
</script>

<style lang="css">
.slot-historial-envios {
	width: 70% !important;
	margin-left: 15% !important;
}

.confirmado {
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

.pendiente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	background-color: var(--red) !important;
	color: white !important;
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

.mdlEnvioDetalle {
	margin-top: 2% !important;
}

@media (max-width: 900px) {
	.slot-historial-envios {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlEnvioDetalle {
		margin-top: 20% !important;
	}
}
</style>
