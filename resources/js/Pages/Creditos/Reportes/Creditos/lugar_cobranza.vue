<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-lugar-cobranza" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MIS ' : '') +
							'CRÉDITOS' +
							(modo == 'personal' ? ' - ' : ' POR ') +
							'LUGAR DE COBRANZA'
						"
					></headerClose>
					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
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

									<div class="input-group col-md-4">
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
												style="font-size: 13px"
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

									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbPorCobrador"
													v-model="por_cobrador"
												/>
											</div>
											<label
												class="input-group-text prepend-title"
												for="chbPorCobrador"
												style="font-size: 13px"
											>
												COBRADOR
											</label>
										</div>

										<select
											class="form-control center"
											v-model="cobrador_seleccionado"
											:disabled="!por_cobrador"
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
								</div>
							</fieldset>

							<div class="col-md-1 ml-3">
								<button
									class="btn btn-action btn-icon-split mt-3"
									title="Buscar"
									@click="Buscar('pago_hoy')"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>
					</div>

					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="hoy-tab"
									data-toggle="tab"
									href="#hoy"
									role="tab"
									aria-controls="hoy"
									aria-selected="true"
									>PARA COBRAR HOY</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="todos-tab"
									data-toggle="tab"
									href="#todos"
									role="tab"
									aria-controls="todos"
									aria-selected="false"
									>TODOS LOS CRÉDITOS</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="no-hoy-tab"
									data-toggle="tab"
									href="#no-hoy"
									role="tab"
									aria-controls="no-hoy"
									aria-selected="false"
									>CRÉDITOS QUE NO PAGAN HOY</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="hoy"
								role="tabpanel"
								aria-labelledby="hoy-tab"
							>
								<DataTable
									:value="lista_pago_hoy"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="350px"
									selectionMode="single"
									:rows="50"
									showGridlines
								>
									<Column
										field="numero"
										header="N°"
										:styles="{ width: '40px', justifyContent: 'center' }"
									>
										<template #body="slotProps">
											{{ slotProps.index + 1 }}
										</template>
									</Column>
									<Column
										field="cliente"
										header="CLIENTE"
										:styles="{ width: '300px' }"
									>
										<template #body="{ data }">
											{{ data.cliente }}
										</template>
									</Column>
									<Column
										field="capital"
										header="CAPITAL"
										:styles="{ width: '100px', justifyContent: 'right' }"
										><template #body="{ data }">
											S/ {{ roundTo(data.capital, 2) }}
										</template>
									</Column>
									<Column
										field="plazo"
										header="PLAZO"
										:styles="{ width: '100px', justifyContent: 'center' }"
										><template #body="{ data }">
											{{ data.plazo }}
										</template>
									</Column>
									<Column
										field="numero_cuota"
										header="N°_CUOTA"
										:styles="{ width: '70px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="cuota"
										header="CUOTA"
										:styles="{ width: '80px', justifyContent: 'right' }"
										><template #body="{ data }">
											S/ {{ roundTo(data.cuota, 2) }}
										</template>
									</Column>
									<Column
										field="dias_atraso"
										header="ATRASO"
										:styles="{ width: '60px', justifyContent: 'center' }"
										><template #body="{ data }">
											{{ data.dias_atraso }}
										</template>
									</Column>
									<Column
										field="usuario_asesor"
										header="ASESOR"
										:styles="{ width: '150px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="usuario_cobrador"
										header="COBRADOR"
										:styles="{ width: '150px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="direccion"
										header="DIRECCIÓN"
										:styles="{ width: '300px' }"
									>
									</Column>
									<Column
										field="referencia_direccion"
										header="REFERENCIA"
										:styles="{ width: '400px' }"
									>
									</Column>
									<Column
										field="ubicacion"
										header="UBICACIÓN"
										:styles="{ width: '300px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ data.ubicacion }}
										</template>
									</Column>
									<template #empty> No hay CRÉDITOS encontrados.</template>
								</DataTable>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										title="Imprimir"
										@click="Exportar('pago_hoy', 'PDF')"
										:disabled="lista_pago_hoy.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-file-pdf"></i>
										</span>
										<span class="text">IMPRIMIR</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										title="Exportar"
										@click="Exportar('pago_hoy', 'XLSX')"
										:disabled="lista_pago_hoy.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-file-excel"></i>
										</span>
										<span class="text">EXPORTAR</span>
									</button>
								</div>
							</div>
							<div
								class="tab-pane fade"
								id="todos"
								role="tabpanel"
								aria-labelledby="todos-tab"
							>
								<!-- -------table---------- -->
								<div class="text-center mt-2">
									<button
										class="btn btn-action btn-icon-split"
										title="Buscar"
										@click="Buscar('pago_todos')"
									>
										<span class="icon text-white">
											<i class="fas fa-search"></i>
										</span>
										<span class="text">BUSCAR</span>
									</button>
								</div>
								<table class="table" id="tblPagoTodos" width="100%">
									<thead>
										<tr>
											<th style="min-width: 25px !important">N°</th>
											<th style="min-width: 200px !important">CLIENTE</th>
											<th style="min-width: 80px !important">CAPITAL</th>
											<th style="min-width: 100px !important">PLAZO</th>
											<th style="min-width: 70px !important">N°_CUOTA</th>
											<th style="min-width: 80px !important">CUOTA</th>
											<th style="min-width: 100px !important">TIPO</th>
											<th style="min-width: 50px !important">ATRASO</th>
											<th style="min-width: 120px !important">ASESOR</th>
											<th style="min-width: 120px !important">COBRADOR</th>
											<th style="min-width: 300px !important">DIRECCIÓN</th>
											<th style="min-width: 400px !important">REFERENCIA</th>
											<th style="min-width: 250px !important">UBICACIÓN</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_pago_todos"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
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
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">
												{{
													roundTo(item.plazo, 0) +
													" " +
													periodo_medicion(item.periodo_pago)
												}}
											</td>
											<td align="center">{{ item.numero_cuota }}</td>
											<td align="right">S/ {{ roundTo(item.cuota, 2) }}</td>
											<td align="center">{{ item.tipo }}</td>
											<td align="center">{{ item.dias_atraso }}</td>
											<td align="center">{{ item.usuario_asesor }}</td>
											<td align="center">{{ item.usuario_cobrador }}</td>
											<td>{{ item.direccion }}</td>
											<td>{{ item.referencia_direccion }}</td>
											<td align="center">
												{{
													item.distrito +
													" - " +
													item.provincia +
													" - " +
													item.departamento
												}}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										title="Imprimir"
										@click="Exportar('pago_todos', 'PDF')"
										:disabled="lista_pago_todos.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-file-pdf"></i>
										</span>
										<span class="text">IMPRIMIR</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										title="Exportar"
										@click="Exportar('pago_todos', 'XLSX')"
										:disabled="lista_pago_todos.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-file-excel"></i>
										</span>
										<span class="text">EXPORTAR</span>
									</button>
								</div>
							</div>
							<div
								class="tab-pane fade"
								id="no-hoy"
								role="tabpanel"
								aria-labelledby="no-hoy-tab"
							>
								<!-- -------table---------- -->
								<div class="text-center mt-2">
									<button
										class="btn btn-action btn-icon-split"
										title="Buscar"
										@click="Buscar('pago_no_hoy')"
									>
										<span class="icon text-white">
											<i class="fas fa-search"></i>
										</span>
										<span class="text">BUSCAR</span>
									</button>
								</div>
								<table class="table" id="tblPagoNoHoy" width="100%">
									<thead>
										<tr>
											<th style="min-width: 25px !important">N°</th>
											<th style="min-width: 200px !important">CLIENTE</th>
											<th style="min-width: 80px !important">CAPITAL</th>
											<th style="min-width: 100px !important">PLAZO</th>
											<th style="min-width: 70px !important">N°_CUOTA</th>
											<th style="min-width: 80px !important">CUOTA</th>
											<th style="min-width: 100px !important">TIPO</th>
											<th style="min-width: 50px !important">ATRASO</th>
											<th style="min-width: 120px !important">ASESOR</th>
											<th style="min-width: 120px !important">COBRADOR</th>
											<th style="min-width: 300px !important">DIRECCIÓN</th>
											<th style="min-width: 400px !important">REFERENCIA</th>
											<th style="min-width: 250px !important">UBICACIÓN</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_pago_no_hoy"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
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
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">
												{{
													roundTo(item.plazo, 0) +
													" " +
													periodo_medicion(item.periodo_pago)
												}}
											</td>
											<td align="center">{{ item.numero_cuota }}</td>
											<td align="right">S/ {{ roundTo(item.cuota, 2) }}</td>
											<td align="center">{{ item.tipo }}</td>
											<td align="center">{{ item.dias_atraso }}</td>
											<td align="center">{{ item.usuario_asesor }}</td>
											<td align="center">{{ item.usuario_cobrador }}</td>
											<td>{{ item.direccion }}</td>
											<td>{{ item.referencia_direccion }}</td>
											<td align="center">
												{{
													item.distrito +
													" - " +
													item.provincia +
													" - " +
													item.departamento
												}}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										title="Imprimir"
										@click="Exportar('pago_no_hoy', 'PDF')"
										:disabled="lista_pago_no_hoy.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-file-pdf"></i>
										</span>
										<span class="text">IMPRIMIR</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										title="Exportar"
										@click="Exportar('pago_no_hoy', 'XLSX')"
										:disabled="lista_pago_no_hoy.length == 0"
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	components: {
		layout,
		headerClose,
		mdlDetalleCredito,
		DataTable,
		Column,
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
			por_cobrador: false,

			usuarios_filtrados: [],
			asesor_seleccionado: 0,
			cobrador_seleccionado: 0,

			lista_pago_hoy: [],
			lista_pago_todos: [],
			lista_pago_no_hoy: [],
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

		lista_pago_todos() {
			$("#tblPagoTodos").DataTable().destroy();
			this.TablaPagoTodos();
		},
		lista_pago_no_hoy() {
			$("#tblPagoNoHoy").DataTable().destroy();
			this.TablaPagoNoHoy();
		},
		por_asesor() {
			this.asesor_seleccionado = 0;
		},
		por_cobrador() {
			this.cobrador_seleccionado = 0;
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		this.TablaPagoTodos();
		this.TablaPagoNoHoy();
		this.FiltrarUsuarios();
	},

	methods: {
		ListarAgenciasPermitidas() {
			if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_LUGAR_COBRANZA"
				);
			} else if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MI_LUGAR_COBRANZA"
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

		row_class_interlineado(data) {
			return data.index % 2 != 0 ? "verde-claro" : null;
		},

		TablaPagoTodos() {
			this.$nextTick(() => {
				var table = $("#tblPagoTodos").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
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
		TablaPagoNoHoy() {
			this.$nextTick(() => {
				var table = $("#tblPagoNoHoy").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
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
		async Buscar(tipo) {
			let self = this;

			let data = new FormData();
			data.append("modo", "por_lugar_cobranza");
			data.append("tipo", tipo);
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("por_asesor", this.por_asesor);
			data.append("por_cobrador", this.por_cobrador);

			if (this.por_asesor) {
				data.append("asesor_id", this.asesor_seleccionado);
			}

			if (this.por_cobrador) {
				data.append("cobrador_id", this.cobrador_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.post(route("rep.cre.lugar_cobranza.buscar"), data);
					// return false;

					Swal.showLoading();
					await axios
						.post(
							route("rep.cre.lugar_cobranza.buscar", {
								modo: "por_lugar_cobranza",
							}),
							data
						)
						.then(function (response) {
							if (response.data.lista_creditos.length == 0) {
								if (tipo == "pago_hoy") {
									self.lista_pago_hoy = [];
								} else if (tipo == "pago_todos") {
									self.lista_pago_todos = [];
								} else if (tipo == "pago_no_hoy") {
									self.lista_pago_no_hoy = [];
								}

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								if (tipo == "pago_hoy") {
									self.lista_pago_hoy = response.data.lista_creditos;
								} else if (tipo == "pago_todos") {
									self.lista_pago_todos = response.data.lista_creditos;
								} else if (tipo == "pago_no_hoy") {
									self.lista_pago_no_hoy = response.data.lista_creditos;
								}

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
		Exportar(forma_pago, tipo) {
			let data = new FormData();
			data.append("modo", "por_lugar_cobranza");

			if (forma_pago == "pago_hoy") {
				data.append("lista_creditos", JSON.stringify(this.lista_pago_hoy));
			} else if (forma_pago == "pago_todos") {
				data.append("lista_creditos", JSON.stringify(this.lista_pago_todos));
			} else if (forma_pago == "pago_no_hoy") {
				data.append("lista_creditos", JSON.stringify(this.lista_pago_no_hoy));
			}

			data.append("tipo", tipo);

			let titulo = "";

			if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			} else if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			}

			// this.$inertia.post(route("rep.cre.lugar_cobranza.exportar"), data);
			// return false;

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("rep.cre.lugar_cobranza.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								// link.download = "rptDesembolsosFacturados.xlsx";
								link.click();
								return Swal.fire({
									icon: "success",
									title: "¡EXPORTADO!",
									timer: 2000,
									showConfirmButton: false,
								});
							} else if (tipo == "PDF") {
								const origin = window.location.origin;
								const path_pdf = response.data.path_pdf;

								// Crear un IFrame
								const iframe = document.createElement("iframe");
								// Oculto el iframe
								iframe.style.display = "none";
								// Defino el source
								iframe.src = origin + path_pdf;
								// Añadir el Iframe a la vista
								document.body.appendChild(iframe);

								iframe.contentWindow.focus(); // Enfoca
								iframe.contentWindow.print(); // Imprime
							}

							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
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
.slot-reporte-lugar-cobranza {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-reporte-lugar-cobranza {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
