<template>
	<layout ref="layout">
		<div class="slot_body slot-envios-recepciones" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ENVÍOS Y RECEPCIONES'"></headerClose>
					<div class="card-title">PANEL DE BUSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row col-md-10">
							<div class="form-group col-md-3 col-5">
								<label for="text-input" class="label-title">DESDE</label>
								<input
									class="form-control center"
									type="date"
									v-model="datos_fecha.fecha_desde"
								/>
							</div>
							<div class="form-group col-md-3 col-5">
								<label for="text-input" class="label-title">HASTA</label>
								<div class="form-row">
									<input
										class="form-control center"
										type="date"
										v-model="datos_fecha.fecha_hasta"
									/>
								</div>
							</div>
							<div class="form-group col-md-1 col-1 mb-2 ml-1">
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
							<div class="form-group col-md-3">
								<div class="row">
									<div class="form-check">
										<input
											id="chbEnvios"
											type="checkbox"
											v-model="envios_check"
										/>
										<label class="form-check-label label-title" for="chbEnvios">
											ENVÍOS
										</label>
									</div>
								</div>
								<div class="row">
									<div class="form-check">
										<input
											id="chbRecepciones"
											type="checkbox"
											v-model="recepciones_check"
										/>
										<label
											class="form-check-label label-title"
											for="chbRecepciones"
										>
											RECEPCIONES
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-title">RESULTADOS DE BÚSQUEDA</div>
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
								id="inpBuscar"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>

						<table
							class="table table-hover"
							id="tblEnviosRecepciones"
							style="width: 100% !important"
						>
							<thead>
								<tr>
									<th style="width: 70px !important">DETALLE</th>
									<th>SITUACIÓN</th>
									<th>TIPO</th>
									<th>FECHA_ENVÍO</th>
									<th>AGENCIA_ENVÍO</th>
									<th>USUARIO_ENVÍO</th>
									<th>AGENCIA_RECEPCIÓN</th>
									<th>RESPONSABLE_RECEPCIÓN</th>
									<th>UBICACIÓN_RECEPCIÓN</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in envios_recepciones" :key="index">
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
										{{ item.tipo }}
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
									<td class="table-bordered" align="center">
										{{ item.nombre_agencia_recepcion }}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.abreviacion_responsable +
											" - " +
											item.usuario_recepcion
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
				</div>
			</div>

			<mdlDetalleEnvio ref="mdlDetalleEnvio"></mdlDetalleEnvio>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";
import mdlDetalleEnvio from "@/Pages/Logistica/Activos/Components/mdlDetalleEnvio.vue";

import { required } from "vuelidate/lib/validators";
export default {
	components: {
		layout,
		headerClose,
		mdlDetalleEnvio,
	},

	data() {
		return {
			envios_check: true,
			recepciones_check: true,
			envios_recepciones: [],
			envios_recepciones_detalle: [],
			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},
		};
	},
	validations: {
		datos_fecha: {
			fecha_desde: { required },
			fecha_hasta: { required },
		},
	},
	watch: {
		envios_recepciones() {
			$("#tblEnviosRecepciones").DataTable().destroy();
			this.TablaEnviosRecepciones();
		},
	},
	mounted() {
		this.MesActual();
		this.TablaEnviosRecepciones();
	},
	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		TablaEnviosRecepciones() {
			let self = this;

			this.$nextTick(() => {
				var table = $("#tblEnviosRecepciones").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[3, "desc"]],
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

				$("#chbEnvios").change(function () {
					if (self.envios_check && self.recepciones_check) {
						table.column(2).search("").draw();
					} else if (self.envios_check && !self.recepciones_check) {
						table.column(2).search("ENVÍO").draw();
					} else if (!self.envios_check && self.recepciones_check) {
						table.column(2).search("RECEPCIÓN").draw();
					} else {
						table.column(2).search("0").draw();
					}
				});

				$("#chbRecepciones").change(function () {
					if (self.envios_check && self.recepciones_check) {
						table.column(2).search("").draw();
					} else if (self.envios_check && !self.recepciones_check) {
						table.column(2).search("ENVÍO").draw();
					} else if (!self.envios_check && self.recepciones_check) {
						table.column(2).search("RECEPCIÓN").draw();
					} else {
						table.column(2).search("0").draw();
					}
				});

				$("#inpBuscar").keyup(function () {
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
			if (this.$v.datos_fecha.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Falta ingresar una de las fechas, verifique.",
				});
				return false;
			} else {
				let data = new FormData();
				data.append("fecha_desde", self.datos_fecha.fecha_desde);
				data.append("fecha_hasta", self.datos_fecha.fecha_hasta);
				data.append("usuario", "MI_USUARIO");

				axios
					.post(route("log.act.envios_recepciones.por_fecha"), data)
					.then(function (response) {
						self.envios_recepciones = response.data.envios_recepciones;
						self.envios_recepciones_detalle =
							response.data.envios_recepciones_detalle;
					});
			}
		},
		VerDetalle(item) {
			this.submited = false;
			let lista_activos = this.envios_recepciones_detalle.filter(
				(item_1) => item_1.envio_id == item.id
			);
			let mdlDetalleEnvio = this.$refs.mdlDetalleEnvio;
			if (item.tipo == "ENVÍO") {
				mdlDetalleEnvio.title_modal = "ENVÍO DE ACTIVOS";
			} else if (item.tipo == "RECEPCIÓN") {
				mdlDetalleEnvio.title_modal = "RECEPCIÓN DE ACTIVOS";
			}
			mdlDetalleEnvio.submited = false;
			mdlDetalleEnvio.frmConfirmacion.modo = "CONFIRMAR";
			mdlDetalleEnvio.lista_activos = lista_activos;
			mdlDetalleEnvio.frmConfirmacion.envio_id = item.id;
			if (item.tipo == "ENVÍO" || item.situacion == "RECHAZADO") {
				mdlDetalleEnvio.modo = "VISTA";
			} else if (item.tipo == "RECEPCIÓN") {
				if (item.situacion == "PENDIENTE") {
					mdlDetalleEnvio.modo = "NO_VISTA";
				} else if (
					item.situacion == "CONFIRMADO" ||
					item.situacion == "RECHAZADO"
				) {
					mdlDetalleEnvio.modo = "VISTA";
				}
			}
			mdlDetalleEnvio.frmConfirmacion.agencia_recepcion =
				item.agencia_recepcion;
			mdlDetalleEnvio.frmConfirmacion.responsable_recepcion =
				item.responsable_recepcion;
			mdlDetalleEnvio.frmConfirmacion.ubicacion_recepcion =
				item.ubicacion_recepcion;
			mdlDetalleEnvio.frmConfirmacion.documento_envio = item.documento_envio;
			if (item.situacion == "CONFIRMADO") {
				mdlDetalleEnvio.frmConfirmacion.documento_recepcion =
					item.documento_recepcion;
			} else {
				$("#documento_recepcion").val("");
				mdlDetalleEnvio.frmConfirmacion.documento_recepcion = null;
			}
			$("#mdlDetalleEnvio").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-envios-recepciones {
	width: 60% !important;
	margin-left: 20% !important;
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

@media (max-width: 900px) {
	.slot-envios-recepciones {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
