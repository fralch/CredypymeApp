<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-clientes-por-asesor"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="(modo == 'personal' ? 'MIS ' : '') + 'CLIENTES POR ASESOR'"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-8">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-5">
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
												:style="
													windowWidth >= 900
														? 'font-size: 15px !important'
														: 'font-size: 13px !important'
												"
											>
												{{ item.agencia }}
											</option>
										</select>
									</div>

									<div class="input-group col-md-5">
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

										<select
											class="form-control center"
											v-model="usuario_seleccionado"
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
									<div
										class="form-check col-md-2"
										:style="
											windowWidth >= 900 ? '' : 'margin-left: 15px !important'
										"
									>
										<input
											class="form-check-input"
											type="checkbox"
											id="chbMostrarHabilitados"
											v-model="mostrar_habilitados"
											:disabled="!por_asesor || modo == 'personal'"
											@change="FiltrarUsuarios"
										/>
										<label class="label-title" for="chbMostrarHabilitados"
											>Sólo habilitados</label
										>
									</div>

									<div class="col-md-1 text-right" v-if="windowWidth < 900">
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

							<div class="col-md-1" v-if="windowWidth >= 900">
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
						</div>

						<div class="card-title mt-2">LISTA DE RESULTADOS</div>

						<div class="form-row col-md-12 mt-2">
							<div class="form-group col-md-8 col-12">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Buscar</span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscarClienteAs"
										:disabled="lista_clientes.length == 0"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										spellcheck="false"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
						</div>
						<table class="table" id="tblClientesAsesor" width="100%">
							<thead>
								<tr>
									<th style="min-width: 10px !important">N°</th>
									<th style="min-width: 150px !important">CLIENTE</th>
									<th style="min-width: 30px !important">EXPEDIENTE</th>
									<th style="min-width: 30px !important">DNI</th>
									<th style="min-width: 30px !important">FECHA_NACIMIENTO</th>
									<th style="min-width: 50px !important">ASESOR</th>
									<th style="min-width: 40px !important">TELÉFONO_01</th>
									<th style="min-width: 40px !important">OPERADOR</th>
									<th style="min-width: 250px !important">NOTAS_TELÉFONO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_clientes"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
								>
									<td align="center">{{ index + 1 }}</td>
									<td align="left">
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td align="center">
										{{ item.numero_expediente }}
									</td>
									<td align="center">
										{{ item.dni }}
									</td>
									<td align="center">
										{{ item.fecha_nacimiento }}
									</td>
									<td align="center">
										{{ item.usuario }}
									</td>

									<td align="center">
										{{
											JSON.parse(item.telefonos).t1 == null
												? "-"
												: JSON.parse(item.telefonos).t1
										}}
									</td>
									<td align="center">
										{{
											JSON.parse(item.telefonos).o1 == null
												? "-"
												: JSON.parse(item.telefonos).o1
										}}
									</td>
									<td align="center">
										{{
											JSON.parse(item.telefonos).n1 == null
												? "-"
												: JSON.parse(item.telefonos).n1
										}}
									</td>
								</tr>
							</tbody>
						</table>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar"
								:disabled="lista_clientes.length == 0"
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
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		modo: String,
		usuarios: Array,
	},

	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: null,

			usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],

			por_asesor: this.modo == "personal" ? true : false,
			usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,
			mostrar_habilitados: true,

			windowWidth: window.innerWidth,

			lista_clientes: [],
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

		por_asesor() {
			this.usuario_seleccionado = 0;
		},

		lista_clientes() {
			$("#tblClientesAsesor").DataTable().destroy();
			this.TablaClientesAsesor();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaClientesAsesor();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},
		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CLIENTES_POR_MI_ASESOR"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CLIENTES_POR_ASESOR"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},
		TablaClientesAsesor() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "200px";
				}
				var table = $("#tblClientesAsesor").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
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
				});

				$("#inpBuscarClienteAs").keyup(function () {
					table.column(1).search(this.value).draw();
				});
			});
		},

		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.usuarios_seleccionados = [];

				if (this.mostrar_habilitados) {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) =>
							item.agencia_id == this.agencia_seleccionada &&
							item.habilitado == 1
					);
				} else {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) => item.agencia_id == this.agencia_seleccionada
					);
				}
			} else if (this.modo == "personal") {
				this.usuarios_filtrados = this.usuarios;
			}
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("por_asesor", this.por_asesor);
			data.append("modo", "por_asesor");

			if (this.por_asesor) {
				data.append("asesor_id", this.usuario_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("rep.cli.clientes_por_asesor.buscar"), data);
					axios
						.post(route("rep.cli.clientes_por_asesor.buscar"), data)
						.then(function (response) {
							if (response.data.lista_clientes.length == 0) {
								self.lista_clientes = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_clientes = response.data.lista_clientes;
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

		Exportar() {
			let data = new FormData();
			data.append("lista_clientes", JSON.stringify(this.lista_clientes));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					//   this.$inertia.post(route("rep.cli.clientes_asesor.exportar"), data);
					axios
						.post(route("rep.cli.clientes_asesor.exportar"), data)
						.then(function (response) {
							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptClientesAsesor.xlsx";

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
.slot-reporte-clientes-por-asesor {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-reporte-clientes-por-asesor {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
