<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-pago-hoy" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MIS ' : '') + 'CLIENTES QUE PAGAN HOY'
						"
					></headerClose>
					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-6">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
										</div>
										<select
											class="form-control center"
											v-model="agencia_seleccionada"
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

									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbPorAsesor"
													v-model="por_asesor"
													:disabled="modo == 'personal'"
												/>
											</div>
											<label
												class="input-group-text prepend-title"
												for="chbPorAsesor"
											>
												ASESOR
											</label>
										</div>
										<div class="input-group-prepend"></div>
										<select
											class="form-control center"
											v-model="asesor_seleccionado"
											:disabled="!por_asesor"
										>
											<option :value="0" disabled selected>
												Seleccione...
											</option>
											<option
												v-for="(item, index) in usuarios_filtrados"
												:key="index"
												:value="item.dni"
											>
												{{ item.usuario }}
											</option>
										</select>
									</div>

									<div class="col-sm-1 text-right" v-if="windowWidth < 900">
										<button
											class="btn btn-action btn-icon-split mt-3"
											title="Buscar"
											@click="Buscar"
										>
											<span class="icon text-white" style="font-size: 15px">
												<i class="fas fa-search"></i>
											</span>
										</button>
									</div>
								</div>
							</fieldset>

							<div class="col-sm-1" v-if="windowWidth >= 900">
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
							<fieldset class="form-group col-md-5">
								<legend>
									<label class="label-title">CONTROL</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-4 col-4">
										<div class="input-group-prepend">
											<label class="input-group-text prepend-title">
												TOTAL
											</label>
										</div>
										<input
											class="form-control center"
											type="text"
											readonly
											:value="totales.total"
											style="
												background: var(--verdeClaroEmpresarial) !important;
											"
										/>
									</div>
									<div class="input-group col-md-4 col-4">
										<div class="input-group-prepend">
											<label class="input-group-text prepend-title">
												PAG.
											</label>
										</div>
										<input
											class="form-control center"
											type="text"
											readonly
											:value="totales.pagados"
											style="
												background: var(--green) !important;
												color: white !important;
											"
										/>
									</div>
									<div class="input-group col-md-4 col-4">
										<div class="input-group-prepend">
											<label class="input-group-text prepend-title">
												REST.
											</label>
										</div>
										<input
											class="form-control center"
											type="text"
											readonly
											:value="totales.restantes"
											style="background: white !important"
										/>
									</div>
								</div>
							</fieldset>
						</div>
					</div>

					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<table class="table" id="tblPagoHoy" width="100%">
							<thead>
								<tr>
									<th style="min-width: 30px !important">N°</th>
									<th style="min-width: 200px !important">CLIENTE</th>
									<th style="min-width: 120px !important">ASESOR</th>
									<th style="min-width: 100px !important">EXP.</th>
									<th style="min-width: 50px !important">ATRASO</th>
									<th style="min-width: 80px !important">CAPITAL</th>
									<th style="min-width: 100px !important">PLAZO</th>
									<th style="min-width: 80px !important">CUOTA</th>
									<th style="min-width: 50px !important">N°_CUOTA</th>
									<th style="min-width: 50px !important">ESTADO</th>
									<th style="min-width: 100px !important">ÚLTIMO_PAGO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_pago_hoy"
									:key="index"
									class="table-bordered"
									:class="[
										item.estado == 'C'
											? 'pagado'
											: index % 2 == 0
											? 'verde-claro'
											: '',
									]"
									@dblclick="DetalleCredito(item.id)"
								>
									<td align="center">{{ index + 1 }}</td>
									<td>
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td align="center">{{ item.usuario_asesor }}</td>
									<td align="center">
										{{ item.numero_expediente + " - " + item.numero_credito }}
									</td>
									<td align="center">{{ item.dias_atraso }}</td>

									<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
									<td align="center">
										{{
											roundTo(item.plazo, 0) +
											" " +
											periodo_medicion(item.periodo_pago)
										}}
									</td>
									<td align="right">S/ {{ roundTo(item.cuota, 2) }}</td>
									<td align="center">{{ item.numero_cuota }}</td>
									<td align="center">
										{{ item.estado == "P" ? "VIG" : "CAN" }}
									</td>
									<td align="center">{{ item.fecha_ultimo_pago }}</td>
								</tr>
							</tbody>
						</table>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar('pago_hoy')"
								:disabled="lista_pago_hoy.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-file-excel"></i>
								</span>
								<span class="text">EXPORTAR</span>
							</button>
						</div>
					</div>
				</div>
				<mdlDetalleCredito
					ref="mdlDetalleCredito"
					:usuarios_agencia="usuarios_filtrados"
					:agencia_id="agencia_seleccionada"
				></mdlDetalleCredito>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import mdlDetalleCredito from "@/Pages/Creditos/Creditos/Components/mdlDetalleCredito.vue";

export default {
	components: {
		layout,
		headerClose,
		mdlDetalleCredito,
	},
	props: {
		modo: String,
		usuarios: Array,
	},

	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: null,

			por_asesor: this.modo == "personal" ? true : false,

			windowWidth: window.innerWidth,

			usuarios_filtrados: [],
			asesor_seleccionado: 0,

			lista_pago_hoy: [],
			totales: {},
		};
	},
	watch: {
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
			this.FiltrarUsuarios();
		},
		lista_pago_hoy() {
			$("#tblPagoHoy").DataTable().destroy();
			this.TablaPagoHoy();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaPagoHoy();
		this.FiltrarUsuarios();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		ListarAgenciasPermitidas() {
			if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CLIENTES_PAGO_HOY"
				);
			} else if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CLIENTES_MI_PAGO_HOY"
				);
			} else {
				this.agencias_permitidas = [];
			}
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
		TablaPagoHoy() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "200px";
				}
				var table = $("#tblPagoHoy").DataTable({
					scrollY: scroll_height,
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
			data.append("modo", "pago_hoy");
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("por_asesor", this.por_asesor);

			if (this.por_asesor) {
				data.append("asesor_id", this.asesor_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					//   this.$inertia.post(route("rep.cli.pago_hoy.buscar"), data);
					axios
						.post(route("rep.cli.pago_hoy.buscar"), data)
						.then(function (response) {
							if (response.data.lista_clientes.length == 0) {
								self.lista_pago_hoy = [];
								self.totales = {};

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_pago_hoy = response.data.lista_clientes;
								self.totales = response.data.totales;
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
		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.asesor_seleccionado = 0;
				this.cobrador_seleccionado = 0;

				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			} else if (this.modo == "personal") {
				this.asesor_seleccionado = this.usuarios[0].dni;
				this.cobrador_seleccionado = 0;
				this.usuarios_filtrados = this.usuarios;
			}
		},
		DetalleCredito(credito_id) {
			let self = this;

			axios
				.post(
					route("rep.cre.dias_mora.detalle", {
						credito_id: credito_id,
						agencia_id: self.agencia_seleccionada,
					})
				)
				.then(function (response) {
					let mdlDetalleCredito = self.$refs.mdlDetalleCredito;
					async function EnviarDatos() {
						mdlDetalleCredito.credito_id = credito_id;
						mdlDetalleCredito.datos_credito = response.data.datos_credito;
						mdlDetalleCredito.datos_cuotas = response.data.datos_cuotas;
						mdlDetalleCredito.notificaciones = response.data.notificaciones;
						mdlDetalleCredito.notificaciones_tipos =
							response.data.notificaciones_tipos;
						mdlDetalleCredito.compromisos = response.data.compromisos;
						mdlDetalleCredito.datos_titular = response.data.datos_titular;
						mdlDetalleCredito.pariente = response.data.pariente;
						mdlDetalleCredito.datos_pariente = response.data.datos_pariente;
						mdlDetalleCredito.aval = response.data.aval;
						mdlDetalleCredito.datos_aval = response.data.datos_aval;
						mdlDetalleCredito.datos_negocio = response.data.datos_negocio;
					}
					EnviarDatos().then(() => {
						$("#mdlDetalleCredito").css("display", "block");
						$("#cuotas-tab").tab("show");
					});
				});
		},
		Exportar(tipo) {
			let data = new FormData();
			data.append("modo", "pago_hoy");

			data.append("lista_clientes", JSON.stringify(this.lista_pago_hoy));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(route("rep.cli.pago_hoy.exportar"), data);

					axios
						.post(route("rep.cli.pago_hoy.exportar"), data)
						.then(function (response) {
							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptClientesPagoHoy.xlsx";

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
.slot-reporte-pago-hoy {
	width: 75% !important;
	margin-left: 12.5% !important;
}

.pagado {
	background: var(--green) !important;
}

@media (max-width: 900px) {
	.slot-reporte-pago-hoy {
		width: 99% !important;
		margin-left: 0.5% !important;
		margin-top: 15% !important;
	}
}
</style>
