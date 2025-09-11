<template>
	<layout ref="layout">
		<div class="slot_body slot-venta" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'VENTA'"></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row col-md-11">
							<div class="form-group col-md-4 col-6">
								<label class="label-title">AGENCIA</label>
								<select
									class="form-control center mayus"
									v-model="agencia_seleccionada"
									@change="ListarActivos"
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
							<div class="form-group col-md-4 col-6">
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
							<div class="form-group col-md-4 col-6">
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
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
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
										@click="CanastaVenta"
										title="Vender ACTIVOS"
										:disabled="canasta_venta.length == 0"
									>
										<span class="icon text-white" style="font-weight: bold">
											S/
										</span>
									</button>
								</div>
							</div>
						</div>

						<table class="table table-hover" id="tblActivos" width="100%">
							<thead>
								<tr>
									<th style="width: 70px !important">VENDER</th>
									<th>CÓDIGO_DE_ACTIVO</th>
									<th>DESCRIPCIÓN</th>
									<th>RESPONSABLE</th>
									<th>UBICACIÓN</th>
									<th>VALOR_ACTUAL(S/)</th>
									<th>CONDICIÓN</th>
									<th>ESTADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in activos" :key="index">
									<td class="table-bordered" align="center" width="70px">
										<div class="checkbox">
											<label
												style="
													font-size: 1.7em;
													margin-bottom: 0 !important;
													height: 28.6px !important;
													margin-left: 5px;
													margin-top: 5px;
												"
												><input
													type="checkbox"
													:value="item"
													:id="'chb' + item.id"
													v-model="canasta_venta" /><span
													class="cr"
													style="margin-right: 0 !important"
													><i class="cr-icon fa fa-check"></i></span
											></label>
										</div>
									</td>
									<td class="table-bordered">
										{{ item.codigo }}
									</td>
									<td class="table-bordered">
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
										{{ roundTo(item.valor_actual, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.condicion }}
									</td>
									<td
										class="table-bordered"
										style="background: var(--green)"
										align="center"
									>
										{{ item.estado }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<mdlCanastaVenta ref="mdlCanastaVenta"></mdlCanastaVenta>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import mdlCanastaVenta from "@/Pages/Logistica/Activos/Components/mdlCanastaVenta.vue";
export default {
	components: {
		layout,
		headerClose,
		mdlCanastaVenta,
	},
	props: {
		//tipo_modulo: String,
		responsables: Array,
		ubicaciones: Array,
	},

	data() {
		return {
			submited: false,
			agencia_seleccionada: 0,
			activos: [],
			responsables_filtrados: [],
			ubicaciones_filtradas: [],
			canasta_venta: [],
			agencias: [],
			agencias_permitidas: [],
		};
	},
	watch: {
		activos() {
			$("#tblActivos").DataTable().destroy();
			this.TablaActivos();
		},
	},
	mounted() {
		this.listar_agencias();
		this.TablaActivos();
	},
	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_ACTIVOS/VENTA"
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
					if (this.value == 0) {
						table.column(3).search("").draw();
					} else {
						table.column(3).search(this.value).draw();
					}
				});

				$("#slcUbicaciones").change(function () {
					if (this.value == 0) {
						table.column(4).search("").draw();
					} else {
						table.column(4).search(this.value).draw();
					}
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		ListarActivos() {
			let self = this;
			this.canasta_venta = [];
			let agencia_id = this.agencia_seleccionada;

			if (agencia_id == 0) {
				this.activos = [];
			} else {
				axios
					.post(
						route("log.act.inventario.listar", {
							agencia_id: agencia_id,
							estado: "DISPONIBLE",
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
					});
			}
		},

		async CanastaVenta() {
			let mdlCanastaVenta = this.$refs.mdlCanastaVenta;
			mdlCanastaVenta.submited = false;
			mdlCanastaVenta.title_modal = "VENTA ACTIVOS";
			mdlCanastaVenta.frmCanastaVenta.canasta_venta = this.canasta_venta;
			mdlCanastaVenta.frmCanastaVenta.agencia_id = this.agencia_seleccionada;
			mdlCanastaVenta.frmCanastaVenta.agencia = this.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			)[0].agencia;
			mdlCanastaVenta.frmCanastaVenta.comprador.apellido_paterno = null;
			mdlCanastaVenta.frmCanastaVenta.comprador.apellido_materno = null;
			mdlCanastaVenta.frmCanastaVenta.comprador.nombres = null;
			mdlCanastaVenta.frmCanastaVenta.comprador.dni = null;
			$("#documento").val("");
			mdlCanastaVenta.frmCanastaVenta.documento = null;
			await mdlCanastaVenta.ActualizarTabla();
			$("#mdlCanastaVenta").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-venta {
	width: 68% !important;
	margin-left: 16% !important;
}

@media (max-width: 900px) {
	.slot-venta {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>

