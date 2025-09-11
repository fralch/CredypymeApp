<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-compras" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HISTORIAL DE COMPRAS'"></headerClose>
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
										id="radioAgrupado"
										checked
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
								id="tblCompras"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th style="width: 70px !important">DETALLE</th>
										<th>AGENCIA</th>
										<th>FECHA_COMPRA</th>
										<th>USUARIO_COMPRA</th>
										<th>USUARIO_REGISTRO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(item, index) in lista_compras" :key="index">
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
											{{ item.agencia }}
										</td>
										<td class="table-bordered" align="center">
											{{ JSON.parse(item.datos_creacion).fecha }}
										</td>

										<td class="table-bordered" align="center">
											{{ item.usuario_compra }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.usuario_registro }}
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
								id="tblComprasDetalles"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th>AGENCIA</th>
										<th>FECHA_COMPRA</th>
										<th>USUARIO_COMPRA</th>
										<th>USUARIO_REGISTRO</th>

										<th>CÓDIGO_DE_ACTIVO</th>
										<th>DESCRIPCIÓN</th>
										<th>MARCA</th>
										<th>MODELO</th>
										<th>PLACA</th>
										<th>CARACTERÍSTICAS</th>
										<th>COLOR</th>
										<th>CONDICIÓN</th>
										<th>CANTIDAD</th>
										<th title="Valor unitario con IGV">V.U.C.IGV (S/)</th>
										<th title="Valor unitario sin IGV">V.U.S.IGV (S/)</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(item, index) in lista_compras_detalles"
										:key="index"
									>
										<td class="table-bordered" align="center">
											{{ item.agencia }}
										</td>
										<td class="table-bordered" align="center">
											{{ JSON.parse(item.datos_creacion).fecha }}
										</td>

										<td class="table-bordered" align="center">
											{{ item.usuario_compra }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.usuario_registro }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.codigo }}
										</td>
										<td class="table-bordered">
											{{ item.descripcion }}
										</td>
										<td class="table-bordered">
											{{ item.marca == null ? "-" : item.marca }}
										</td>
										<td class="table-bordered">
											{{ item.modelo == null ? "-" : item.modelo }}
										</td>
										<td class="table-bordered">
											{{ item.placa == null ? "-" : item.placa }}
										</td>
										<td class="table-bordered">
											{{
												item.caracteristicas == null
													? "-"
													: item.caracteristicas
											}}
										</td>
										<td class="table-bordered" align="center">
											{{ item.color }}
										</td>
										<td class="table-bordered" align="center">
											{{ item.condicion }}
										</td>
										<td class="table-bordered" align="center">
											{{ roundTo(item.cantidad, 0) }}
										</td>
										<td class="table-bordered" align="right">
											{{ roundTo(item.valor_unitario, 2) }}
										</td>
										<td class="table-bordered" align="right">
											{{
												roundTo(
													item.valor_unitario -
														(item.igv / 100) * item.valor_unitario,
													2
												)
											}}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!-- The Modal -->
			<div id="mdlCompraDetalle" class="modal">
				<div class="modal-content w-50 mdlCompraDetalle">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'DETALLE DE COMPRA'"
								:nombre_modal="'mdlCompraDetalle'"
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
								<table class="table table-hover" id="tblCompraDetalle">
									<thead>
										<tr>
											<th>CÓDIGO_DE_ACTIVO</th>
											<th>DESCRIPCIÓN</th>
											<th>MARCA</th>
											<th>MODELO</th>
											<th>PLACA</th>
											<th>CARACTERÍSTICAS</th>
											<th>COLOR</th>
											<th>CONDICIÓN</th>
											<th>CANTIDAD</th>
											<th title="Valor unitario con IGV">V.U.C.IGV(S/)</th>
											<th title="Valor unitario sin IGV">V.U.S.IGV(S/)</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in compra_detalle" :key="index">
											<td class="table-bordered">
												{{ item.codigo }}
											</td>
											<td class="table-bordered">
												{{ item.descripcion }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.marca == null ? "-" : item.marca }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.modelo == null ? "-" : item.modelo }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.placa == null ? "-" : item.placa }}
											</td>
											<td class="table-bordered">
												{{
													item.caracteristicas == null
														? "-"
														: item.caracteristicas
												}}
											</td>
											<td class="table-bordered" align="center">
												{{ item.color }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.condicion }}
											</td>
											<td class="table-bordered" align="center">
												{{ roundTo(item.cantidad, 0) }}
											</td>
											<td class="table-bordered" align="right">
												{{ roundTo(item.valor_unitario, 2) }}
											</td>
											<td class="table-bordered" align="right">
												{{
													roundTo(
														item.valor_unitario -
															(item.igv / 100) * item.valor_unitario,
														2
													)
												}}
											</td>
										</tr>
									</tbody>
								</table>
								<div class="text-right">
									<label class="form-control-label label-title"
										>DOCUMENTO:</label
									>
									<button
										class="btn btn-action btn-icon-split"
										title="Descargar DOCUMENTO DE COMPRA"
										@click="Descargar()"
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
			lista_compras: [],
			compra_detalle: [],
			lista_compras_detalles: [],
			documento_compra: null,
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
		this.TablaCompras();
		this.TablaComprasDetalles();
	},
	watch: {
		lista_compras() {
			$("#tblCompras").DataTable().destroy();
			this.TablaCompras();
		},
		compra_detalle() {
			$("#tblCompraDetalle").DataTable().destroy();
			this.TablaCompraDetalle();
		},
		lista_compras_detalles() {
			$("#tblComprasDetalles").DataTable().destroy();
			this.TablaComprasDetalles();
		},
	},
	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_ACTIVOS/HISTORIAL_COMPRAS"
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
		TablaCompras() {
			this.$nextTick(() => {
				var table = $("#tblCompras").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[2, "desc"]],
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
		TablaCompraDetalle() {
			this.$nextTick(() => {
				var table = $("#tblCompraDetalle").DataTable({
					scrollY: "250px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [],
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
		TablaComprasDetalles() {
			this.$nextTick(() => {
				var table = $("#tblComprasDetalles").DataTable({
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
				axios
					.post(route("log.act.historial_compras.buscar"), data)
					.then(function (response) {
						self.lista_compras = response.data.lista_compras;
						self.lista_compras_detalles = response.data.lista_compras_detalles;
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
			this.compra_detalle = this.lista_compras_detalles.filter(
				(item_1) => item_1.compra_id == item.id
			);

			this.documento_compra = item.documento;

			$("#mdlCompraDetalle").css("display", "block");
		},
		Descargar() {
			let self = this;
			let source =
				"/imagenes_server/logistica/activos/compras/" +
				this.documento_compra +
				"";
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], { type: response.data.type });
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = self.documento_compra;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},
	},
};
</script>

<style lang="css">
.slot-historial-compras {
	width: 64% !important;
	margin-left: 18% !important;
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

.mdlCompraDetalle {
	margin-top: 2% !important;
}
@media (max-width: 900px) {
	.slot-historial-compras {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlCompraDetalle {
		margin-top: 20% !important;
	}
}
</style>
