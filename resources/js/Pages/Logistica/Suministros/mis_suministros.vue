<template>
	<layout ref="layout">
		<div class="slot_body slot-mis-suministros" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MIS SUMINISTROS'"></headerClose>
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
							<div class="form-group col-md-4 col-6">
								<label for="slcTipos" class="label-title">TIPO</label>
								<select
									class="form-control center"
									id="slcTipos"
									:disabled="suministros.length == 0"
								>
									<option :value="0">TODOS</option>
									<option v-for="(item, index) in tipos" :key="index">
										{{ item.tipo }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-4 col-6">
								<label for="slcTipos" class="label-title">ESTADO</label>
								<select
									class="form-control center"
									id="slcEstados"
									:disabled="suministros.length == 0"
								>
									<option :value="0">TODOS</option>
									<option v-for="(item, index) in estados" :key="index">
										{{ item.estado }}
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

						<table
							class="table table-hover"
							id="tblSuministros"
							style="width: 100% !important"
						>
							<thead>
								<tr>
									<th>CÓDIGO</th>
									<th>SUMINISTRO</th>
									<th>CANTIDAD</th>
									<th>RESPONSABLE</th>
									<th>AGENCIA</th>
									<th>FECHA_ASIGNACIÓN</th>
									<th>ASIGNADO_POR</th>
									<th>ESTADO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in suministros" :key="index">
									<td class="table-bordered" align="center">
										{{ item.codigo }}
									</td>
									<td class="table-bordered">
										{{ item.suministro }}
									</td>
									<td class="table-bordered" align="center">
										{{ roundTo(item.cantidad, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.responsable + " - " + item.usuario_responsable }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.agencia }}
									</td>
									<td class="table-bordered" align="center">
										{{ JSON.parse(item.datos_creacion).fecha }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.usuario_asignador }}
									</td>
									<td
										class="table-bordered"
										:class="[
											item.estado == 'ASIGNADO'
												? 'asignado'
												: item.estado == 'DEVUELTO'
												? 'devuelto'
												: '',
										]"
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
		tipos: Array,
		estados: Array,
	},
	data() {
		return {
			submited: false,
			responsable_seleccionado: 0,
			suministros: [],
		};
	},
	watch: {
		suministros() {
			$("#tblSuministros").DataTable().destroy();
			this.TablaSuministros();
		},
	},

	mounted() {
		this.TablaSuministros();
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
		TablaSuministros() {
			this.$nextTick(() => {
				var table = $("#tblSuministros").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[0, "asc"]],
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

				$("#slcTipos").change(function () {
					if (this.value == 0) {
						table.column(0).search("").draw();
					} else {
						let primera_letra = this.value.substr(0, 3);
						table.column(0).search(primera_letra).draw();
					}
				});

				$("#slcEstados").change(function () {
					if (this.value == 0) {
						table.column(7).search("").draw();
					} else {
						table.column(7).search(this.value).draw();
					}
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		ListarSuministros() {
			let self = this;
			let responsable_id = this.responsable_seleccionado;

			axios
				.post(
					route("log.sum_asignados.por_responsable", {
						responsable_id: responsable_id,
						estado: "TODOS",
					})
				)
				.then((response) => {
					self.suministros = response.data;
				});
		},
	},
};
</script>

<style lang="css">
.slot-mis-suministros {
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
	.slot-mis-suministros {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>



