<template>
	<layout ref="layout">
		<div class="slot_body slot-inventario" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'INVENTARIO'"></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row col-md-11 col-12">
							<div class="form-group col-md-3 col-6">
								<label class="label-title">AGENCIA</label>
								<select
									class="form-control center mayus"
									@change="ListarActivos"
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
							<div class="form-group col-md-3 col-6">
								<label class="label-title">RESPONSABLE</label>
								<select
									id="slcResponsables"
									class="form-control"
									:disabled="activos.length == 0"
								>
									<option :value="0" selected>TODOS</option>
									<option
										v-for="(item, index) in responsables_filtrados"
										:key="index"
									>
										{{ item.abreviacion + " - " + item.usuario }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-3 col-6">
								<label class="label-title">UBICACIÓN</label>
								<select
									id="slcUbicaciones"
									class="form-control"
									:disabled="activos.length == 0"
								>
									<option :value="0" selected>TODAS</option>
									<option
										v-for="(item, index) in ubicaciones_filtradas"
										:key="index"
									>
										{{ item.abreviacion + " - " + item.agencia }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-3 col-6">
								<label class="label-title">TIPO</label>
								<select
									id="slcTipos"
									class="form-control"
									:disabled="activos.length == 0"
								>
									<option :value="0" selected>TODOS</option>
									<option
										v-for="(item, index) in tipos"
										:key="index"
										:value="item.abreviacion"
									>
										{{ item.tipo }}
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADO</div>
					<div class="card-body card-block">
						<!-- <div v-if="tipo_modulo == 'GENERAL'">
              <div class="text-center mb-2"></div>
            </div> -->

						<div id="row">
							<div class="input-group row col-md-7 col-6" style="float: left">
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
							<div class="row col-md-3 col-2 ml-1" style="float: left">
								<div class="btn-group" role="group">
									<button
										class="btn btn-action btn-icon-split"
										@click="CanastaCompras"
										title="Comprar ACTIVO(S)"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/INVENTARIO_COMPRAR'
											)
										"
									>
										<span class="icon text-white">
											<i class="fas fa-shopping-cart"></i>
										</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										@click="CanastaEnvios"
										:disabled="canasta_envio.length == 0"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/INVENTARIO_ENVIAR'
											)
										"
										title="Enviar ACTIVO(S)"
									>
										<span class="icon text-white">
											<i class="fas fa-paper-plane"></i>
										</span>
									</button>
								</div>
							</div>
						</div>

						<table class="table table-hover" id="tblActivos" width="100%">
							<thead>
								<tr>
									<th>ACCIONES</th>
									<th
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/INVENTARIO_ENVIAR'
											)
										"
									>
										ENVIAR
									</th>
									<th>CÓDIGO_DE_ACTIVO</th>
									<th>DESCRIPCIÓN</th>
									<th>RESPONSABLE</th>
									<th>UBICACIÓN</th>
									<th>FECHA_COMPRA</th>
									<th>VALOR_COMPRA(S/)</th>
									<th>VALOR_REAL(S/)</th>
									<th>VIDA_ÚTIL</th>
									<th>DEPRECIACIÓN</th>
									<th>ESTADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in activos" :key="index">
									<td class="table-bordered" align="center">
										<div class="text-center">
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													@click="Ver(item)"
													title="Ver ACTIVO"
												>
													<span class="icon text-white">
														<i class="fas fa-eye"></i>
													</span>
												</button>
											</div>
										</div>
									</td>
									<td
										class="table-bordered align-middle"
										align="center"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/INVENTARIO_ENVIAR'
											)
										"
									>
										<div
											class="align-middle"
											v-if="item.estado == 'DISPONIBLE'"
										>
											<div class="checkbox">
												<label
													class="align-middle"
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													:for="index"
													><input
														type="checkbox"
														:id="index"
														:value="item"
														v-model="canasta_envio" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
										</div>
									</td>
									<td class="table-bordered" style="font-weight: bold">
										{{ item.codigo }}
									</td>

									<td class="table-bordered" align="center">
										{{ item.descripcion }}
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
											item.agencia_ubicacion
										}}
									</td>

									<td class="table-bordered" align="center">
										{{ item.fecha_compra }}
									</td>
									<td class="table-bordered" align="right">
										{{ roundTo(item.valor_compra, 2) }}
									</td>
									<td class="table-bordered" align="right">
										{{ roundTo(item.valor_actual, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.vida_util > 1
												? roundTo(item.vida_util, 2) + " años"
												: roundTo(item.vida_util, 2) + " año"
										}}
									</td>
									<td class="table-bordered" align="center">
										{{ roundTo(item.porcentaje_depreciacion, 2) + " %" }}
									</td>
									<td
										class="table-bordered"
										align="center"
										:class="[
											item.estado == 'DISPONIBLE'
												? 'disponible'
												: 'no-disponible',
										]"
										:title="
											item.estado == 'NO_DISPONIBLE'
												? 'El ACTIVO ha sido seleccionado en alguna operación, verifique'
												: ''
										"
									>
										{{ item.estado }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<mdlCanastaCompra
				:agencias="agencias_permitidas"
				:usuarios="usuarios"
				:nombres="nombres"
				:tipos="tipos"
				:responsables="responsables"
				:ubicaciones="ubicaciones"
				ref="mdlCanastaCompra"
			>
			</mdlCanastaCompra>

			<mdlDatosActivo
				:agencias="agencias"
				:nombres="nombres"
				:tipos="tipos"
				:responsables="responsables"
				:ubicaciones="ubicaciones"
				:condiciones="condiciones"
				ref="mdlDatosActivo"
			>
			</mdlDatosActivo>

			<mdlCanastaEnvio
				:agencias="agencias"
				:agencia_seleccionada="agencia_seleccionada"
				:responsables="responsables"
				:ubicaciones="ubicaciones"
				ref="mdlCanastaEnvio"
			>
			</mdlCanastaEnvio>

			<!-- <mdlHistorial ref="mdlHistorial"></mdlHistorial> -->
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import mdlCanastaCompra from "@/Pages/Logistica/Activos/Components/mdlCanastaCompra.vue";
import mdlDatosActivo from "@/Pages/Logistica/Activos/Components/mdlDatosActivo.vue";
import mdlCanastaEnvio from "@/Pages/Logistica/Activos/Components/mdlCanastaEnvio.vue";

export default {
	components: {
		layout,
		headerClose,
		mdlCanastaCompra,
		mdlDatosActivo,

		mdlCanastaEnvio,
	},
	props: {
		tipos: Array,
		nombres: Array,
		responsables: Array,
		ubicaciones: Array,
		usuarios: Array,
		condiciones: Array,
	},

	data() {
		return {
			submited: false,
			activos: [],
			agencia_seleccionada: 0,
			responsables_filtrados: this.responsables,
			ubicaciones_filtradas: this.ubicaciones,
			canasta_envio: [],
			agencias: [],
			agencias_permitidas: [],
		};
	},
	mounted() {
		this.listar_agencias();
		this.TablaActivos();
	},

	watch: {
		activos() {
			$("#tblActivos").DataTable().destroy();
			this.TablaActivos();
		},
	},

	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_ACTIVOS/INVENTARIO"
			);
		},
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
		TablaActivos() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblActivos").DataTable({
					scrollY: "350px",
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

				$("#slcResponsables").change(function () {
					let column = 3;

					if (
						self.$page.props.user_permissions.permisos.includes(
							"LOGISTICA_ACTIVOS/INVENTARIO_ENVIAR"
						)
					) {
						column = 4;
					}

					if (this.value == 0) {
						table.column(column).search("").draw();
					} else {
						table.column(column).search(this.value).draw();
					}
				});

				$("#slcUbicaciones").change(function () {
					let column = 4;

					if (
						self.$page.props.user_permissions.permisos.includes(
							"LOGISTICA_ACTIVOS/INVENTARIO_ENVIAR"
						)
					) {
						column = 5;
					}

					if (this.value == 0) {
						table.column(column).search("").draw();
					} else {
						table.column(column).search(this.value).draw();
					}
				});
				$("#slcTipos").change(function () {
					let column = 1;
					if (
						self.$page.props.user_permissions.permisos.includes(
							"LOGISTICA_ACTIVOS/INVENTARIO_ENVIAR"
						)
					) {
						column = 2;
					}

					if (this.value == 0) {
						table.column(column).search("").draw();
					} else {
						table.column(column).search(this.value).draw();
					}
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		ListarActivos() {
			let self = this;
			let agencia_id = this.agencia_seleccionada;

			axios
				.post(
					route("log.act.inventario.listar", {
						agencia_id: agencia_id,
						estado: "TODOS",
					})
				)
				.then((response) => {
					self.activos = response.data;
					self.responsables_filtrados = this.responsables.filter(
						(item) => item.agencia_id == agencia_id
					);
					self.ubicaciones_filtradas = this.ubicaciones.filter(
						(item) => item.agencia_id == agencia_id
					);
					$("#slcResponsables").val(0);
					$("#slcUbicaciones").val(0);
					$("#slcTipos").val(0);
				});
		},

		CanastaCompras() {
			let mdlCanastaCompra = this.$refs.mdlCanastaCompra;
			mdlCanastaCompra.title_modal = "CANASTA DE COMPRAS";
			mdlCanastaCompra.submited = false;
			$("#documentoCompra").val("");
			mdlCanastaCompra.frmCanastaCompras.documento = null;
			mdlCanastaCompra.frmCanastaCompras.usuario_compra = 0;
			$("#mdlCanastaCompra").css("display", "block");
		},
		async Ver(item) {
			let mdlDatosActivo = this.$refs.mdlDatosActivo;
			mdlDatosActivo.submited = false;
			mdlDatosActivo.title_modal = "VER ACTIVO";
			mdlDatosActivo.modo = "VER_EXISTENTE";
			mdlDatosActivo.no_editable = false;
			mdlDatosActivo.frmDatosActivo.id = item.id;
			mdlDatosActivo.frmDatosActivo.agencia_id = item.agencia_id;
			mdlDatosActivo.frmDatosActivo.agencia = item.agencia;
			mdlDatosActivo.frmDatosActivo.responsable_id = item.responsable_id;
			mdlDatosActivo.frmDatosActivo.responsable =
				item.abreviacion_responsable + " - " + item.usuario_responsable;
			mdlDatosActivo.frmDatosActivo.ubicacion_id = item.ubicacion_id;
			mdlDatosActivo.frmDatosActivo.ubicacion =
				item.abreviacion_ubicacion + " - " + item.agencia_ubicacion;

			mdlDatosActivo.frmDatosActivo.codigo = [];
			let objeto = { orden: item.id, codigo: item.codigo };
			mdlDatosActivo.frmDatosActivo.codigo.push(objeto);
			await mdlDatosActivo.ActualizarTabla();
			mdlDatosActivo.frmDatosActivo.nombre_id = item.nombre_id;
			mdlDatosActivo.frmDatosActivo.fecha_compra = item.fecha_compra;
			mdlDatosActivo.frmDatosActivo.tipo_id = item.tipo_id;
			mdlDatosActivo.frmDatosActivo.descripcion = item.descripcion;
			mdlDatosActivo.frmDatosActivo.marca = item.marca;
			mdlDatosActivo.frmDatosActivo.modelo = item.modelo;
			mdlDatosActivo.frmDatosActivo.placa = item.placa;
			mdlDatosActivo.frmDatosActivo.caracteristicas = item.caracteristicas;
			mdlDatosActivo.frmDatosActivo.condicion_id = item.condicion_id;
			mdlDatosActivo.frmDatosActivo.cantidad = item.cantidad;
			mdlDatosActivo.frmDatosActivo.color = item.color;
			mdlDatosActivo.frmDatosActivo.valor_compra = this.roundTo(
				item.valor_compra,
				2
			);
			mdlDatosActivo.frmDatosActivo.valor_actual = this.roundTo(
				item.valor_actual,
				2
			);
			mdlDatosActivo.frmDatosActivo.vida_util = this.roundTo(item.vida_util, 2);
			mdlDatosActivo.frmDatosActivo.depreciacion = this.roundTo(
				item.porcentaje_depreciacion,
				2
			);

			$("#mdlDatosActivo").css("display", "block");
			$("#datosActivo1-tab").tab("show");
		},
		async CanastaEnvios() {
			let mdlCanastaEnvio = this.$refs.mdlCanastaEnvio;
			mdlCanastaEnvio.submited = false;
			mdlCanastaEnvio.title_modal = "ENVIAR ACTIVOS";
			mdlCanastaEnvio.frmCanastaEnvio.canasta_envio = this.canasta_envio;
			mdlCanastaEnvio.frmCanastaEnvio.agencia_recepcion = 0;
			mdlCanastaEnvio.frmCanastaEnvio.responsable_recepcion = 0;
			mdlCanastaEnvio.frmCanastaEnvio.ubicacion_recepcion = 0;
			$("#documento_envio").val("");
			mdlCanastaEnvio.frmCanastaEnvio.documento_envio = null;
			await mdlCanastaEnvio.ActualizarTabla();
			$("#mdlCanastaEnvio").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-inventario {
	width: 70% !important;
	margin-left: 15% !important;
}

.disponible {
	background-color: var(--green) !important;
	color: white !important;
}

.no-disponible {
	background-color: var(--red) !important;
	color: white !important;
}

@media (max-width: 900px) {
	.slot-inventario {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
