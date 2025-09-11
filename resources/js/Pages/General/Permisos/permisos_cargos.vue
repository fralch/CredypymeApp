<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body slot-permisosCargos">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'PERMISOS POR CARGO'"></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-xs-4">
								<label for="text-input" class="form-control-label label-title"
									>Cargos</label
								>
								<select
									class="form-control center"
									style="width: 250px"
									id="slcCargos"
									@change="FiltrarCargos()"
								>
									<option value="0" selected>Todos</option>
									<option
										v-for="(cargo, index) in cargos"
										v-bind:key="index"
										:value="cargo.id"
									>
										{{ cargo.cargo }}
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div class="input-group row col-md-10 col-9" style="float: left">
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
						<div id="tabla_usuarios">
							<table class="table table-hover" id="tblCargos">
								<thead>
									<tr>
										<th style="width: 75px !important">EDITAR</th>
										<th>CARGO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(cargo, index) in cargos_filtrados" :key="index">
										<td class="table-bordered" align="center">
											<button
												class="btn btn-action btn-icon-split"
												@click="EditarPermiso(cargo.id)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-edit" style="color: white"></i>
												</span>
											</button>
										</td>
										<td class="table-bordered" align="center">
											{{ cargo.cargo }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		cargos: Array,
		agencias: Array,
		permisos: Array,
	},
	data() {
		return {
			cargos_filtrados: this.cargos,
		};
	},
	mounted() {
		this.TablaCargosPermisos();
	},
	methods: {
		TablaCargosPermisos() {
			this.$nextTick(() => {
				var table = $("#tblCargos").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[1, "asc"]],
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
					responsive: true,
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
						{
							extend: "pdfHtml5",
							text: '<i class="fas fa-file-pdf"></i> ',
							titleAttr: "Exportar a PDF",
							className: "btn btn-cancel",
						},
						{
							extend: "print",
							text: '<i class="fa fa-print"></i> ',
							titleAttr: "Imprimir",
							className: "btn btn-action",
						},
					],
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		FiltrarCargos() {
			let cargos_val = $("#slcCargos").val();

			if (cargos_val == "0") {
				this.cargos_filtrados = this.cargos;
			} else {
				this.cargos_filtrados = this.cargos.filter(
					(item) => item.id == cargos_val
				);
			}
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		EditarPermiso(id) {
			this.$inertia.get("gen.per.permisos_cargos_editar", { id: id });
		},
	},
};
</script>

<style>
.slot-permisosCargos {
	width: 60% !important;
	margin-left: 20% !important;
}
@media (max-width: 900px) {
	.slot-permisosCargos {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
