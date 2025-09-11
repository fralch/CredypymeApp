<template>
	<layout ref="layout">
		<div class="slot_body slot-mis-activos" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MIS ACTIVOS'"></headerClose>
					<div class="card-title">DATOS DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row col-md-9">
							<div class="form-group col-md-4 col-6">
								<label class="label-title">RESPONSABLE</label>
								<select
									class="form-control center mayus"
									@change="ListarSuministros"
									v-model.number="responsable_seleccionado"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in responsables"
										:key="index"
										:value="item.id"
									>
										{{ item.abreviacion + " - " + item.usuario }}
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div id="row">
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
						</div>

						<table class="table table-hover" id="tblActivos" width="100%">
							<thead>
								<tr>
									<th>CÓDIGO_DE_ACTIVO</th>
									<th>DESCRIPCIÓN</th>
									<th>RESPONSABLE</th>
									<th>UBICACIÓN</th>
									<th>CANTIDAD</th>
									<th>CONDICIÓN</th>
									<th>FECHA_ASIGNACIÓN</th>
									<th>ASIGNADO_POR</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in activos" :key="index">
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
										{{ roundTo(item.cantidad, 0) }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.condicion }}
									</td>
									<td class="table-bordered" align="center">
										{{ JSON.parse(item.datos_creacion).fecha }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.usuario_asignador }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		responsables: Array,
	},
	data() {
		return {
			submited: false,
			responsable_seleccionado: 0,
			activos: [],
		};
	},
	watch: {
		activos() {
			$("#tblActivos").DataTable().destroy();
			this.TablaActivos();
		},
	},

	mounted() {
		this.TablaActivos();
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
					order: [[6, "desc"]],
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

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		ListarSuministros() {
			let self = this;
			let responsable_id = this.responsable_seleccionado;

			// this.$inertia.post(
			//   route("log.act_asignados.por_responsable", {
			//     responsable_id: responsable_id,
			//   })
			// );
			axios
				.post(
					route("log.act_asignados.por_responsable", {
						responsable_id: responsable_id,
					})
				)
				.then((response) => {
					self.activos = response.data;
				});
		},
	},
};
</script>

<style lang="css">
.slot-mis-activos {
	width: 70% !important;
	margin-left: 15% !important;
}

.asignado {
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

.devuelto {
	background-color: var(--plomoOscuroEmpresarial) !important;
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

@media (max-width: 900px) {
	.slot-mis-activos {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>



