<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							'PERMISOS POR USUARIO - ' +
							(modo == 'completo'
								? 'GENERAL'
								: modo == 'credito'
								? 'CRÉDITO'
								: modo == 'logistica'
								? 'LOGÍSTICA'
								: 'GTH')
						"
					></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-xs-4">
								<label for="text-input" class="form-control-label label-title"
									>Agencias</label
								>
								<select
									class="form-control center"
									style="width: 250px"
									id="cmbAgencias"
									data-index="1"
								>
									<option :value="0" selected>TODAS</option>
									<option
										v-for="agencia in agencias"
										v-bind:key="agencia.id_agencia"
										:value="agencia.id_agencia"
									>
										{{ agencia.nombre }}
									</option>
								</select>
							</div>
							<div class="form-group col-xs-4">
								<label for="text-input" class="form-control-label label-title"
									>Cargos</label
								>
								<select
									class="form-control center"
									style="width: 250px"
									id="cmbUsuarios"
									data-index="4"
								>
									<option value="0" selected>Todos</option>
									<option v-for="cargo in cargos" v-bind:key="cargo.id">
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
								id="inpBuscar_us"
								autocomplete="off"
								spellcheck="false"
								@focus="hidenav()"
								@blur="shownav()"
							/>
						</div>
						<div id="tabla_usuarios">
							<table class="table table-hover" id="tblUsuarios">
								<thead>
									<tr>
										<th style="width: 75px !important">EDITAR</th>
										<th hidden>IDAGENCIA</th>
										<th>AGENCIA</th>
										<th>NOMBRES Y APELLIDOS</th>
										<th>CARGO</th>
										<th>USUARIO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="t_usuario in usuarios" :key="t_usuario.dni">
										<td class="table-bordered" align="center">
											<button
												class="btn btn-action btn-icon-split"
												@click="EditarPermisos(t_usuario.dni)"
											>
												<span class="icon text-white-50">
													<i class="fas fa-edit" style="color: white"></i>
												</span>
											</button>
										</td>
										<td hidden table="table-bordered" align="center">
											{{ t_usuario.agencia_id }}
										</td>
										<td class="table-bordered" align="center">
											{{ t_usuario.nombreAgencia }}
										</td>
										<td class="table-bordered" align="left">
											{{
												t_usuario.nombres +
												" " +
												t_usuario.apellido_paterno +
												" " +
												t_usuario.apellido_materno
											}}
										</td>
										<td class="table-bordered" align="left">
											{{ t_usuario.cargo }}
										</td>
										<td class="table-bordered" align="center">
											{{ t_usuario.usuario }}
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
		modo: String,
		agencias: Array,
		usuarios: Array,
		dni: Array,
		cargos: Array,
	},
	data() {
		return {
			usuarios_filtrados: this.usuarios,
		};
	},
	mounted() {
		this.TablaPermisosUsuarios();
		if (screen.width < 1000) {
			document.getElementById("tblUsuarios").classList.add("table-responsive");
		}
	},
	methods: {
		TablaPermisosUsuarios() {
			this.$nextTick(() => {
				var table = $("#tblUsuarios").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[3, "asc"]],
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
					],
				});

				$("#cmbAgencias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#cmbUsuarios").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar_us").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		EditarPermisos(dni) {
			this.$inertia.get(
				route("gen.per.permisos_usuarios_editar", { dni: dni, modo: this.modo })
			);
		},

		FiltrarUsuarios(e) {
			let id_agencia = e.target.value;

			if (id_agencia == 0) {
				this.usuarios_filtrados = this.usuarios;
			} else {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.id_agencia == id_agencia
				);
			}
		},
	},
};
</script>

<style></style>
