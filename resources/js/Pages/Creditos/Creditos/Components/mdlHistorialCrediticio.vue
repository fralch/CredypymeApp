
<template>
	<div id="mdlHistorialCrediticio" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-80">
			<div class="content" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>HISTORIAL CREDITICIO</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%; float: right !important"
							@click="CerrarModal"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>
					<div class="card">
						<div class="card-body card-block">
							<div class="row">
								<fieldset class="col-md-5 ml-3" style="background: #d8f1fd">
									<legend>
										<label
											class="label-title p-1"
											style="
												background: var(--colorMedio);
												color: white !important;
												font-size: 12px;
												border-radius: 3px;
											"
											>INFORMACIÓN GENERAL</label
										>
									</legend>
									<div class="form-row">
										<div class="input-group col-md-12 mb-1">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>TITULAR</span
												>
											</div>

											<input
												type="text"
												class="form-control input-information"
												:value="nombre_completo_titular"
												disabled
											/>
										</div>
										<div class="input-group col-md-4 col-6">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title">DNI</span>
											</div>

											<input
												type="text"
												class="form-control center input-information"
												:value="datos_cliente.dni"
												disabled
											/>
										</div>
										<div class="input-group col-md-5 col-6">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title">EXP.</span>
											</div>

											<input
												type="text"
												class="form-control center input-information"
												:value="datos_cliente.codigo_expediente"
												disabled
											/>
										</div>
										<div class="calif input-group col-md-3 col-4">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>CALIF.</span
												>
											</div>

											<input
												type="text"
												class="form-control center input-information"
												:value="datos_cliente.calificacion"
												disabled
											/>
										</div>
										<div class="input-group col-md-5 mt-1 col-8">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>ASESOR</span
												>
											</div>

											<input
												type="text"
												class="form-control center input-information"
												:value="datos_cliente.usuario_asesor"
												disabled
											/>
										</div>
										<div class="input-group col-md-7 mt-1 col-12">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>F. REGISTRO</span
												>
											</div>

											<!-- <input
											type="text"
											class="form-control center input-information"
											disabled
										/> -->
											<input
												type="text"
												class="form-control center input-information"
												:value="fecha_registro"
												disabled
											/>
										</div>
									</div>
								</fieldset>

								<fieldset class="col-md-4 ml-3" style="background: #d8f1fd">
									<legend>
										<label
											class="label-title p-1"
											style="
												background: var(--colorMedio);
												color: white !important;
												font-size: 12px;
												border-radius: 3px;
											"
											>CALIFICACIÓN Y LUGAR DE PAGO
										</label>
									</legend>
									<div class="form-row">
										<div class="input-group col-md-5 col-5">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>CALIF.</span
												>
											</div>

											<select
												class="form-control center input-information"
												v-model="frmCalificacionLugar.calificacion"
											>
												<option value="A" selected>A</option>
												<option value="B">B</option>
												<option value="C">C</option>
												<option value="D">D</option>
											</select>
										</div>

										<div class="input-group col-md-7 col-7">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>COBRO EN</span
												>
											</div>

											<select
												class="form-control center input-information"
												v-model="frmCalificacionLugar.forma_pago"
											>
												<option value="OFICINA" selected>OFICINA</option>
												<option value="NEGOCIO">NEGOCIO</option>
											</select>
										</div>

										<div
											class="input-group mt-1 col-md-8 offset-md-4"
											v-if="frmCalificacionLugar.forma_pago == 'NEGOCIO'"
										>
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>COBRADOR</span
												>
											</div>

											<select
												class="form-control center input-information"
												v-model="frmCalificacionLugar.usuario_cobrador"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in usuarios"
													:key="index"
													:value="item.dni"
												>
													{{ item.usuario }}
												</option>
											</select>
										</div>

										<div class="col-md-12 text-center mt-2">
											<button
												class="btn btn-action btn-icon-split"
												title="Calificar CRÉDITO"
												@click="Calificar"
											>
												<span class="icon text-white">
													<i class="fas fa-check"></i>
												</span>
												<span class="text">APLICAR CAMBIOS</span>
											</button>
										</div>
									</div>
								</fieldset>
								<fieldset class="col-md-2 p-2 ml-3" style="background: #d8f1fd">
									<legend>
										<label
											class="label-title p-1"
											style="
												background: var(--colorMedio);
												color: white !important;
												font-size: 12px;
												border-radius: 3px;
											"
											>IMPRIMIR
										</label>
									</legend>
									<div class="form-row">
										<div class="text-center col-md-12 col-4">
											<button
												class="btn btn-cancel btn-icon-split mt-1"
												title="Imprimir CONTRATO"
												style="font-size: 9px; width: 100% !important"
												@click="Imprimir('contrato')"
											>
												<span class="text">CONTRATO</span>
											</button>
										</div>
										<div class="text-center col-md-12 col-4">
											<button
												class="btn btn-cancel btn-icon-split mt-1"
												title="Imprimir HOJA RESÚMEN"
												style="font-size: 9px; width: 100% !important"
												@click="Imprimir('hoja_resumen')"
											>
												<span class="text">HOJA RESÚMEN</span>
											</button>
										</div>
										<div class="text-center col-md-12 col-4">
											<button
												class="btn btn-cancel btn-icon-split mt-1"
												title="Imprimir PAGARÉ"
												style="font-size: 9px; width: 100% !important"
												@click="Imprimir('pagare_fianza')"
											>
												<span class="text">PAGARÉ</span>
											</button>
										</div>
									</div>
								</fieldset>
							</div>

							<div id="tabla-creditos">
								<table class="table" id="tblCreditos" width="100% !important">
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
											<th style="min-width: 180px !important">PRODUCTO</th>
											<th style="min-width: 150px !important">SUBPRODUCTO</th>
											<th style="min-width: 100px !important">ESTADO</th>
											<th style="min-width: 150px !important">
												CÓDIGO_SEGUIMIENTO
											</th>
											<th style="max-width: 120px !important">
												TASA_INTERÉS_CALF
											</th>
											<th style="min-width: 100px !important">ASESOR</th>
											<th style="min-width: 100px !important">COBRANZA_EN</th>
											<th style="min-width: 100px !important">COBRADOR</th>
											<th style="min-width: 100px !important">MORA_TOTAL</th>
											<th style="min-width: 100px !important">MORA_PAGADA</th>
											<th style="min-width: 100px !important">NOTIF_TOTAL</th>
											<th style="min-width: 100px !important">NOTIF_PAGADA</th>
											<th style="min-width: 100px !important">MORA_NOTIF</th>
											<th style="min-width: 100px !important">
												MODO_DESEMBOLSO
											</th>
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
											<td align="center">
												{{ item.tipo }}
											</td>
											<td align="left">
												{{ item.producto }}
											</td>
											<td align="left">
												{{ item.subproducto == null ? "-" : item.subproducto }}
											</td>
											<td align="center">
												{{ item.estado }}
											</td>
											<td align="center">
												{{ item.codigo_seguimiento }}
											</td>
											<td
												align="center"
												:class="[
													item.calificacion == 'A'
														? 'clas_A'
														: item.calificacion == 'B'
														? 'clas_B'
														: item.calificacion == 'C'
														? 'clas_C'
														: item.calificacion == 'D'
														? 'clas_D'
														: '',
												]"
											>
												{{
													roundTo(item.tasa_interes, 2) +
													"%" +
													(item.calificacion == null
														? ""
														: " - " + item.calificacion)
												}}
											</td>
											<td align="center">
												{{ item.usuario_asesor }}
											</td>
											<td align="center">
												{{ item.pago_oficina == 1 ? "OFICINA" : "NEGOCIO" }}
											</td>
											<td align="center">
												{{ item.usuario_cobrador }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.mora_total, 2) }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.mora_pagado, 2) }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.notificaciones_total, 2) }}
											</td>
											<td align="center">
												S/ {{ roundTo(item.notificaciones_pagado, 2) }}
											</td>
											<td align="center">
												S/
												{{
													roundTo(
														parseFloat(item.mora_total) +
															parseFloat(item.notificaciones_total) -
															(parseFloat(item.mora_pagado) +
																parseFloat(item.notificaciones_pagado)),
														2
													)
												}}
											</td>
											<td align="center">
												{{ item.modo_desembolso }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<hr />
							<ul class="nav nav-tabs" id="myTab" role="tablist">
								<li class="nav-item">
									<a
										class="nav-link active tab-title"
										id="pagos-tab"
										data-toggle="tab"
										href="#pagos"
										role="tab"
										aria-controls="pagos"
										aria-selected="true"
										>DETALLE DE PAGO</a
									>
								</li>
								<li class="nav-item">
									<a
										class="nav-link tab-title"
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
									id="pagos"
									role="tabpanel"
									aria-labelledby="pagos-tab"
								>
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
												<th style="min-width: 50px !important">CUOTA</th>
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
													{{ item.estado == "C" ? item.fecha_ultimo_pago : "" }}
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
												<td align="right">
													S/ {{ roundTo(item.redondeo, 2) }}
												</td>
												<td align="right">S/ {{ roundTo(item.cuota, 2) }}</td>
												<td align="right">
													{{
														item.acumulado > 0
															? "S/ " + roundTo(item.acumulado, 2)
															: ""
													}}
												</td>
												<td align="center">
													{{ item.dias_atraso > 0 ? item.dias_atraso : "" }}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div
									class="tab-pane fade"
									id="compromisos"
									role="tabpanel"
									aria-labelledby="compromisos-tab"
								>
									<table class="table" id="tblCompromisos">
										<thead>
											<tr>
												<th style="max-width: 15px">N°</th>
												<th style="min-width: 250px">COMENTARIO</th>
												<th style="min-width: 80px">FECHA_REGISTRO</th>
												<th style="min-width: 80px">USUARIO_REG</th>
												<th style="min-width: 80px">FECHA_VENC</th>
												<th style="min-width: 80px">FECHA_VISITA</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in compromisos"
												:key="index"
												:class="[
													item.cargo == 'JEFE DE CRÉDITOS'
														? 'jefe'
														: item.cargo == 'ASESOR DE NEGOCIOS'
														? 'asesor'
														: 'otros',
												]"
											>
												<td
													align="center"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													{{ index + 1 }}
												</td>
												<td
													align="left"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													{{ item.compromiso }}
												</td>
												<td
													align="center"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													{{ JSON.parse(item.datos_creacion).fecha }}
												</td>
												<td
													align="center"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													{{ item.usuario_registro }}
												</td>
												<td
													align="center"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													{{ item.fecha_vencimiento }}
												</td>
												<td
													align="center"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													{{ item.fecha_hora_visita }}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import mdlDetallePagos from "@/Pages/Creditos/Caja/Components/mdlDetallePagos.vue";

export default {
	components: {
		headerClose,
		mdlDetallePagos,
	},

	data() {
		return {
			agencia_id: Number,
			cliente_id: Number,

			datos_cliente: Object,
			datos_creditos: Array,
			usuarios: Array,

			cuotas_pagos: [],
			compromisos: [],
			frmCalificacionLugar: {
				credito_id: null,
				calificacion: "A",
				forma_pago: "OFICINA",
				usuario_cobrador: 0,
			},
			fecha_registro:
				this.cliente_id > 0
					? JSON.parse(this.datos_cliente.datos_creacion).fecha
					: null,
			cuotas_pagos: [],
			compromisos: [],
			frmCalificacionLugar: {
				credito_id: null,
				calificacion: "A",
				forma_pago: "OFICINA",
				usuario_cobrador: 0,
			},
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
		nueva_empresa() {
			let agencia = this.$inertia.page.props.application.agencias.filter(
				(item) => item.id == this.agencia_id
			);
			return agencia[0].nueva_empresa;
		},
	},
	watch: {
		datos_creditos() {
			$("#tblCreditos").DataTable().destroy();
			this.TablaCreditos();
		},
		cuotas_pagos() {
			$("#tblPagos").DataTable().destroy();
			this.TablaPagos();
		},
		compromisos() {
			$("#tblCompromisos").DataTable().destroy();
			this.TablaCompromisos();
		},
		datos_cliente() {
			this.fecha_registro = JSON.parse(this.datos_cliente.datos_creacion).fecha;
		},
	},
	mounted() {
		this.TablaCreditos();
		this.TablaPagos();
		this.TablaCompromisos();
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
		async ActualizarInformacion() {
			let self = this;
			let object = {
				cliente_id: this.cliente_id,
				agencia_id: this.agencia_id,
			};

			this.datos_creditos = [];
			this.cuotas_pagos = [];
			this.compromisos = [];

			// this.$inertia.get(route("cli.historial_crediticio", object));
			// return false;

			await axios
				.get(route("cli.historial_crediticio", object))
				.then(function (response) {
					self.datos_cliente = response.data.datos_cliente;
					self.datos_creditos = response.data.datos_creditos;
					self.usuarios = response.data.usuarios;
				});
		},
		PosicionarScroll(credito_id) {
			let posicion = $("#tblCreditos #cre_" + credito_id);
			$("#tabla-creditos .dataTables_scrollBody").scrollTo(posicion);
		},
		TablaCreditos() {
			this.$nextTick(() => {
				$("#tblCreditos").DataTable({
					scrollY: "190px",
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
		TablaPagos() {
			this.$nextTick(() => {
				$("#tblPagos").DataTable({
					scrollY: "190px",
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
		TablaCompromisos() {
			this.$nextTick(() => {
				var table = $("#tblCompromisos").DataTable({
					scrollY: "190px",
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

		ListarCuotas(credito) {
			let self = this;

			let data = new FormData();
			data.append("credito_id", credito.id);
			data.append("agencia_id", this.agencia_id);
			axios
				.post(route("cli.historial_crediticio.listar_detalle"), data)
				.then(function (response) {
					self.compromisos = response.data.compromisos;
					self.cuotas_pagos = response.data.cuotas_pagos;
				});
		},
		DetallePagos(credito_id) {
			let pago_cuotas = [];
			let pago_moras = [];
			let pago_notificaciones = [];

			let mdlDetallePagos = this.$parent.$refs.mdlDetallePagos;

			let data = new FormData();
			data.append("agencia_id", this.agencia_id);
			data.append("credito_id", credito_id);

			axios
				.post(route("caj.pago_cuotas.listar"), data)
				.then(function (response) {
					pago_cuotas = response.data;
					mdlDetallePagos.pago_cuotas = pago_cuotas;
				});

			axios
				.post(route("caj.pago_moras.listar"), data)
				.then(function (response) {
					pago_moras = response.data;
					mdlDetallePagos.pago_moras = pago_moras;
				});

			axios
				.post(route("caj.pago_notificaciones.listar"), data)
				.then(function (response) {
					pago_notificaciones = response.data;
					mdlDetallePagos.pago_notificaciones = pago_notificaciones;
				});

			$("#mdlDetallePagos").css("display", "block");
		},
		async Calificar() {
			let self = this;
			let row = document
				.getElementById("tblCreditos")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione un crédito",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("cre_", "");

				this.frmCalificacionLugar.credito_id = id;

				if (
					this.frmCalificacionLugar.forma_pago == "NEGOCIO" &&
					this.frmCalificacionLugar.usuario_cobrador == 0
				) {
					Swal.fire({
						icon: "warning",
						title: "¡Ups!",
						text: "Debe seleccionar el usuario de COBRANZA",
						allowOutsideClick: true,
					});
					return false;
				}

				Swal.fire({
					title: "¿Desea calificar el crédito?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
					showLoaderOnConfirm: true,
					preConfirm: () => {
						let data = new FormData();
						data.append("agencia_id", this.agencia_id);
						data.append(
							"frmCalificacionLugar",
							JSON.stringify(this.frmCalificacionLugar)
						);

						return axios
							.post(route("cli.historial_crediticio.calificar"), data)
							.then(async (response) => {
								self.frmCalificacionLugar.calificacion = "A";
								self.frmCalificacionLugar.forma_pago = "OFICINA";
								self.frmCalificacionLugar.usuario_cobrador = 0;

								await self.ActualizarInformacion();

								self.PosicionarScroll(self.frmCalificacionLugar.credito_id);
							})
							.catch((error) => {
								Swal.showValidationMessage(`Ha ocurrido un error: ${error}`);
							});
					},
					allowOutsideClick: () => !Swal.isLoading(),
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							icon: "success",
							title: "¡ÉXITO!",
							timer: 1200,
							showConfirmButton: false,
						});
					}
				});
			}
		},

		async Imprimir(tipo) {
			let row = document
				.getElementById("tblCreditos")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione un crédito",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("cre_", "");
				let credito = this.datos_creditos.filter((item) => item.id == id)[0];

				let fecha_actual = null;

				let parts = this.$page.props.application.data_local
					.filter((item) => item.descripcion == "FECHA_CREDITOS")[0]
					.valor_fecha.split("-");

				let options = {
					year: "numeric",
					month: "long",
					day: "numeric",
				};

				let date = new Date(+parts[0], parts[1] - 1, +parts[2]);
				fecha_actual =
					this.$page.props.application.agencias.filter(
						(item) => item.id == this.agencia_id
					)[0].distrito +
					", " +
					date.toLocaleDateString("es-ES", options);

				let datos_documento = null;
				let codigo = null;

				if (this.nueva_empresa) {
					codigo = credito.codigo_seguimiento_2;
				} else {
					codigo = credito.codigo_seguimiento;
				}

				switch (tipo) {
					case "hoja_resumen":
						datos_documento = {
							codigo_seguimiento: codigo,
							monto: this.roundTo(credito.monto, 2) + " SOLES",
							tasa_interes: this.roundTo(credito.tasa_interes, 2) + " %",
							tasa_interes_moratoria: "0.00 %",
							monto_interes:
								this.roundTo(credito.cuota * credito.plazo - credito.monto, 2) +
								" SOLES",
							producto: credito.producto,
							tipo: credito.tipo,
							frecuencia_pago:
								"CADA 1 " +
								this.periodo_medicion(credito.periodo_pago) +
								" (PERIODO FIJO)",
							numero_cuotas: this.roundTo(credito.plazo, 0) + " CUOTA(S)",
							fecha_vencimiento: credito.fecha_vencimiento,
							fecha_lugar_elaboracion: fecha_actual,
						};

						break;
					case "pagare_fianza":
						datos_documento = {
							codigo_seguimiento: codigo,
							fecha_lugar_elaboracion: fecha_actual,
							titular_nombres:
								credito.apellido_paterno +
								" " +
								credito.apellido_materno +
								" " +
								credito.nombres,
							titular_dni: credito.dni,
							titular_direccion: credito.direccion,

							pariente_nombres:
								credito.dni_pariente == null
									? null
									: credito.apellido_paterno_pariente +
									  " " +
									  credito.apellido_materno_pariente +
									  " " +
									  credito.nombres_pariente,
							pariente_dni:
								credito.dni_pariente == null ? null : credito.dni_pariente,
							pariente_direccion:
								credito.dni_pariente == null
									? null
									: credito.direccion_pariente,

							aval_nombres:
								credito.dni_aval == null
									? null
									: credito.apellido_paterno_aval +
									  " " +
									  credito.apellido_materno_aval +
									  " " +
									  credito.nombres_aval,
							aval_dni: credito.dni_aval == null ? null : credito.dni_aval,
							aval_direccion:
								credito.dni_aval == null ? null : credito.direccion_aval,
							pariente_aval_nombres:
								credito.dni_pariente_aval == null
									? null
									: credito.apellido_paterno_pariente_aval +
									  " " +
									  credito.apellido_materno_pariente_aval +
									  " " +
									  credito.nombres_pariente_aval,
							pariente_aval_dni:
								credito.dni_pariente_aval == null
									? null
									: credito.dni_pariente_aval,
							pariente_aval_direccion:
								credito.dni_pariente_aval == null
									? null
									: credito.direccion_pariente_aval,
						};

						break;

					case "contrato":
						datos_documento = {
							codigo_seguimiento: codigo,
							agencia_id: this.agencia_id,
							fecha_lugar_elaboracion: fecha_actual,
							titular_nombres:
								credito.apellido_paterno +
								" " +
								credito.apellido_materno +
								" " +
								credito.nombres,
							titular_dni: credito.dni,
							titular_direccion: credito.direccion,

							pariente_nombres:
								credito.dni_pariente == null
									? null
									: credito.apellido_paterno_pariente +
									  " " +
									  credito.apellido_materno_pariente +
									  " " +
									  credito.nombres_pariente,
							pariente_dni:
								credito.dni_pariente == null ? null : credito.dni_pariente,
							pariente_direccion:
								credito.dni_pariente == null
									? null
									: credito.direccion_pariente,

							aval_nombres:
								credito.dni_aval == null
									? null
									: credito.apellido_paterno_aval +
									  " " +
									  credito.apellido_materno_aval +
									  " " +
									  credito.nombres_aval,
							aval_dni: credito.dni_aval == null ? null : credito.dni_aval,
							aval_direccion:
								credito.dni_aval == null ? null : credito.direccion_aval,

							pariente_aval_nombres:
								credito.dni_pariente_aval == null
									? null
									: credito.apellido_paterno_pariente_aval +
									  " " +
									  credito.apellido_materno_pariente_aval +
									  " " +
									  credito.nombres_pariente_aval,
							pariente_aval_dni:
								credito.dni_pariente_aval == null
									? null
									: credito.dni_pariente_aval,
							pariente_aval_direccion:
								credito.dni_pariente_aval == null
									? null
									: credito.direccion_pariente_aval,
						};

						break;
					default:
						break;
				}

				let data = new FormData();
				data.append("tipo", tipo);
				data.append("datos_documento", JSON.stringify(datos_documento));

				Swal.fire({
					title: "GENERANDO",
					text: "Espere porfavor...",
					allowOutsideClick: false,
					didOpen: () => {
						// this.$inertia.post(route("cre.documentos_financieros.generar"), data);
						// return false;

						Swal.showLoading();
						axios
							.post(route("cre.documentos_financieros.generar"), data)
							.then(function (response) {
								let origin = window.location.origin;
								let path_pdf = response.data.path_pdf;

								// Crear un IFrame
								let iframe = document.createElement("iframe");
								// Oculto el iframe
								iframe.style.display = "none";
								// Defino el source
								iframe.src = origin + path_pdf;
								// Añadir el Iframe a la vista
								document.body.appendChild(iframe);

								iframe.contentWindow.focus(); // Enfoca
								iframe.contentWindow.print(); // Imprime

								return Swal.fire({
									icon: "success",
									title: "¡LISTO!",
									timer: 1200,
									showConfirmButton: false,
								});
							});
					},
				});
			}
		},

		CerrarModal() {
			$("#mdlHistorialCrediticio").css("display", "none");
		},
	},
};
</script>



<style lang="css">
.prepend-title {
	font-size: 8.5px;
	color: var(--plomoOscuroEmpresarial);
}

.input-information {
	height: 2em !important;
	color: black;
}
.clas_A {
	background-color: var(--green) !important;
}

.clas_B {
	background-color: var(--yellow) !important;
}

.clas_C {
	background-color: var(--orange) !important;
	color: black !important;
}
.clas_D {
	background-color: var(--red) !important;
	color: white !important;
}
.jefe {
	background-color: var(--red) !important;
	color: white !important;
}

.asesor {
	background: var(--green) !important;
}

.otros {
	background-color: var(--yellow) !important;
}
</style>

