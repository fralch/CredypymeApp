<template>
	<layout ref="layout">
		<div class="slot_body slot-copia-voucher" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'COPIA VOUCHER'"></headerClose>
					<div class="card-title">DETALLE</div>
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="cuotas-tab"
									data-toggle="tab"
									href="#cuotas"
									role="tab"
									aria-controls="cuotas"
									aria-selected="true"
									>LISTA DE CRÉDITOS</a
								>
							</li>

							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="vouchers-tab"
									data-toggle="tab"
									href="#vouchers"
									role="tab"
									aria-controls="vouchers"
									aria-selected="false"
									>VOUCHERS GENERADOS</a
								>
							</li>
						</ul>

						<div class="tab-content" id="myTabContent">
							<!-- tab pago de cuotas de credito -->

							<div
								class="tab-pane fade show active"
								id="cuotas"
								role="tabpanel"
								aria-labelledby="cuotas-tab"
							>
								<div class="form-row">
									<div class="input-group col-md-6 mb-1 mt-1">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title span-highlight_voucher"
												>CLIENTE</span
											>
										</div>
										<input
											type="text"
											class="form-control input_information_voucher input-highlight_voucher"
											:value="nombre_completo_titular"
											onkeydown="return false"
											spellcheck="false"
										/>
									</div>
									<div class="input-group col-md-3 mb-1 mt-1">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title span-highlight_voucher"
												>DNI</span
											>
										</div>
										<input
											type="text"
											class="form-control input_information_voucher input-highlight_voucher"
											:value="datos_cliente.dni"
											onkeydown="return false"
											spellcheck="false"
										/>
									</div>
									<div class="input-group col-md-3 mb-1 mt-1">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title span-highlight_voucher"
												>ASESOR</span
											>
										</div>
										<input
											type="text"
											class="form-control input_information_voucher input-highlight_voucher"
											:value="datos_cliente.usuario_asesor"
											onkeydown="return false"
											spellcheck="false"
										/>
									</div>
								</div>
								<div class="form-row">
									<div class="input-group col-md-3">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title span-highlight_voucher"
												>AGENCIA</span
											>
										</div>
										<input
											type="text"
											class="form-control input_information_voucher input-highlight_voucher"
											:value="datos_cliente.agencia"
											onkeydown="return false"
											spellcheck="false"
										/>
									</div>
								</div>

								<table
									class="table"
									id="tblCreditosVoucher"
									style="width: 100% !important"
								>
									<thead>
										<tr>
											<th style="min-width: 30px !important">N°</th>
											<th style="min-width: 80px !important">MONTO</th>
											<th style="min-width: 80px !important">PLAZO</th>
											<th style="min-width: 80px !important">CUOTA</th>
											<th style="min-width: 120px !important">
												FECHA_DESEMBOLSO
											</th>
											<th style="min-width: 120px !important">TIPO</th>
											<th style="min-width: 150px !important">PRODUCTO</th>
											<th style="min-width: 100px !important">ESTADO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in datos_creditos"
											:key="index"
											@dblclick="ListarCuotas(item)"
											:id="'cre_' + item.id"
										>
											<td align="center">
												{{ index + 1 }}
											</td>
											<td align="center">S/ {{ roundTo(item.monto, 2) }}</td>

											<td align="center">
												{{
													roundTo(item.plazo, 0) +
													" " +
													periodo_medicion(item.periodo_pago)
												}}
											</td>
											<td align="center">S/ {{ roundTo(item.cuota, 2) }}</td>

											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="left">
												{{ item.tipo }}
											</td>
											<td align="left">
												{{ item.producto }}
											</td>
											<td align="center">
												{{ item.estado }}
											</td>
										</tr>
									</tbody>
								</table>
								<div class="card-title mt-1">DETALLE DE CUOTAS</div>

								<table class="table" id="tblPagos" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 75px !important">
												FECHA_VENCIMIENTO
											</th>
											<th style="min-width: 100px !important">FECHA_PAGO</th>
											<th style="min-width: 20px !important">ESTADO</th>
											<th style="min-width: 50px !important">CAPITAL</th>
											<th style="min-width: 50px !important">INTERÉS</th>
											<th style="min-width: 50px !important">REDONDEO</th>
											<!-- <th style="min-width: 50px !important">CUOTA</th> -->
											<th style="min-width: 50px !important">ACUMULADO</th>
											<th style="min-width: 20px !important">ATRASO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in cuotas_pagos"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
											@dblclick="DetallePagos(item.credito_id)"
										>
											<td align="center">
												{{ item.numero_cuota }}
											</td>
											<td align="center">
												{{ item.fecha_vencimiento }}
											</td>
											<td align="center">
												{{ item.fecha_ultimo_pago }}
											</td>
											<td align="center">
												{{
													item.estado == "P"
														? "PEN"
														: item.estado == "C"
														? "CAN"
														: "VEN"
												}}
											</td>
											<td align="right">S/ {{ roundTo(item.capital, 2) }}</td>
											<td align="right">S/ {{ roundTo(item.interes, 2) }}</td>
											<td align="right">S/ {{ roundTo(item.redondeo, 2) }}</td>
											<!-- <td align="right">S/ {{ roundTo(item.cuota, 2) }}</td> -->
											<td align="right">
												{{
													item.acumulado > 0
														? "S/ " + roundTo(item.acumulado, 2)
														: ""
												}}
											</td>
											<td align="center">
												{{ item.dias_atraso }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div
								class="tab-pane fade"
								id="vouchers"
								role="tabpanel"
								aria-labelledby="vouchers-tab"
							>
								<table class="table" id="tblVouchers" width="100%">
									<thead>
										<tr>
											<th style="min-width: 20px">N°</th>
											<th style="min-width: 70px">AGENCIA_CAJA</th>
											<th style="min-width: 80px">CAJA</th>
											<th style="min-width: 100px">FECHA_PAGO</th>
											<th style="min-width: 70px">HORA</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in pagos_realizados"
											:key="index"
											@dblclick="GenerarVoucher(item, 'PDF')"
										>
											<td align="center">{{ index + 1 }}</td>
											<td align="center">{{ item.agencia_caja }}</td>
											<td align="center">{{ item.usuario_caja }}</td>
											<td align="center">
												{{ item.fecha_dia }}
											</td>
											<td align="center">
												{{ item.hora }}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
								<iframe id="vista_previa"> </iframe>
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
		agencia_id: Number,
		cliente_id: Number,

		datos_creditos: Array,
		datos_cliente: Object,
	},
	data() {
		return {
			windowWidth: window.innerWidth,

			submited: false,

			frmDatosVoucher: {
				cliente: null,
				conceptos: [],
				monto_cuota: 0,
				cuota_actual: null,
				importe: null,
				titulo: null,
				importe: 0,
				usuario_asesor: null,
				usuario_caja: null,
				fecha_pago: null,
			},
			cuotas_pagos: [],
			vouchers: [],
		};
	},

	computed: {
		nombre_completo_titular() {
			return (
				this.datos_cliente.apellido_paterno +
				" " +
				this.datos_cliente.apellido_materno +
				" " +
				this.datos_cliente.nombres
			);
		},
		pagos_realizados() {
			const dias = [
				"Lunes",
				"Martes",
				"Miércoles",
				"Jueves",
				"Viernes",
				"Sábado",
				"Domingo",
			];
			let pagos_realizados = [];

			this.vouchers.forEach((element) => {
				let fecha_con_dia = new Date(element.fecha_voucher).getDay();
				let object = {
					fecha_dia: element.fecha_voucher + " " + dias[fecha_con_dia],
					hora: element.hora_voucher,
					fecha_hora_voucher: element.fecha_hora_voucher,
					datos_voucher: element.datos_voucher,
					datos_creacion: element.datos_creacion,
					apellido_paterno: element.apellido_paterno,
					apellido_materno: element.apellido_materno,
					nombres: element.nombres,
					numero_cuota: element.numero_cuota,
					agencia_caja: element.agencia_caja,
					usuario_caja: element.usuario_caja,
				};
				pagos_realizados.push(object);
			});

			return pagos_realizados;
		},
	},
	watch: {
		cuotas_pagos() {
			$("#tblPagos").DataTable().destroy();
			this.TablaPagos();
		},
		pagos_realizados() {
			$("#tblVouchers").DataTable().destroy();
			this.TablaVouchers();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		let self = this;
		this.TablaCreditos();
		// this.TablaVouchers();
	},
	methods: {
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
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
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
		},
		TablaCreditos() {
			this.$nextTick(() => {
				let scroll_height = "150px";
				if (this.windowWidth <= 900) {
					scroll_height = "100px";
				}
				var table = $("#tblCreditosVoucher").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					select: {
						style: "single",
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
		TablaPagos() {
			this.$nextTick(() => {
				$("#tblPagos").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
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
		TablaVouchers() {
			this.$nextTick(() => {
				var table = $("#tblVouchers").DataTable({
					scrollY: "150px",
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

		ListarCuotas(credito) {
			let self = this;

			let data = new FormData();
			data.append("credito_id", credito.id);
			data.append("agencia_id", this.agencia_id);

			// this.$inertia.post(route("cre.copia_voucher.listar_detalle"), data);
			// return false;

			axios
				.post(route("cre.copia_voucher.listar_detalle"), data)
				.then(function (response) {
					self.cuotas_pagos = response.data.cuotas_pagos;
					self.vouchers = response.data.vouchers;

					let iframe = document.getElementById("vista_previa");
					iframe.src = "";
				});
		},
		async GenerarVoucher(item, tipo) {
			this.frmDatosVoucher.titulo = "CONSTANCIA DE AMORTIZACIÓN - COPIA";
			this.frmDatosVoucher.cliente =
				item.apellido_paterno +
				" " +
				item.apellido_materno +
				" " +
				item.nombres;

			this.frmDatosVoucher.fecha_pago = item.fecha_hora_voucher;
			this.frmDatosVoucher.usuario_asesor = this.datos_cliente.usuario_asesor;
			this.frmDatosVoucher.agencia_caja = item.agencia_caja;
			this.frmDatosVoucher.usuario_caja = item.usuario_caja;
			this.frmDatosVoucher.nombre_dispositivo = JSON.parse(
				item.datos_creacion
			).nombre_dispositivo;
			this.frmDatosVoucher.monto_cuota = this.roundTo(
				this.cuotas_pagos[0].cuota,
				2
			);

			let detalle_cuota = " Próx:";

			detalle_cuota += String(item.numero_cuota);
			detalle_cuota += " Pend:";
			detalle_cuota += String(
				parseInt(this.cuotas_pagos.length) - parseInt(item.numero_cuota) + 1
			);

			this.frmDatosVoucher.cuota_actual = detalle_cuota;

			this.frmDatosVoucher.conceptos = JSON.parse(item.datos_voucher);

			let total = 0;
			this.frmDatosVoucher.conceptos.forEach((element) => {
				if (element.concepto != "Saldo total") {
					total += element.importe;
				}
			});

			this.frmDatosVoucher.importe = total;

			let data = new FormData();

			data.append("agencia_id", this.agencia_id);
			data.append("datos_voucher", JSON.stringify(this.frmDatosVoucher));
			data.append("tipo", tipo);

			// this.$inertia.post(route("caj.cobranza.voucher"), data);

			await axios
				.post(route("caj.cobranza.voucher"), data)
				.then(function (response) {
					if (tipo == "XLSX") {
						let linkSource =
							"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
							response.data;
						let downloadLink = document.createElement("a");
						let fileName = "vchCobranza.xlsx";

						downloadLink.href = linkSource;
						downloadLink.download = fileName;
						downloadLink.click();
					} else if (tipo == "PDF") {
						let origin = window.location.origin;
						let path_pdf = response.data.path_pdf;

						// Crear un IFrame
						let iframe = document.getElementById("vista_previa");
						// Defino el source
						iframe.src = origin + path_pdf;
					}
				});
		},
	},
};
</script>

<style lang="css">
.slot-copia-voucher {
	width: 60% !important;
	margin-left: 20% !important;
}

.input_information_voucher {
	height: 2em !important;
	color: black;
}

#vista_previa {
	width: 100%;
	height: 400px;
	border: 1px solid black;
	box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
}

@media (max-width: 900px) {
	.slot-copia-voucher {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
