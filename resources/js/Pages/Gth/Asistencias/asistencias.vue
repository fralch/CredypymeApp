<template>
	<layout ref="layout">
		<div class="slot_body slot-asistencias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="(modo == 'personal' ? 'MIS ' : '') + 'ASISTENCIAS'"
					></headerClose>

					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-8">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											class="form-control center"
											type="date"
											name="desde"
											id="dtpDesde"
											style="font-size: 15px"
										/>
									</div>
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											class="form-control center"
											type="date"
											name="hasta"
											id="dtpHasta"
											style="font-size: 15px"
										/>
									</div>
								</div>
							</fieldset>

							<div class="col-md-1">
								<button
									class="btn btn-action btn-icon-split mt-3"
									@click="Buscar"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
							<div class="input-group col-md-4 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">AGENCIA</span>
								</div>
								<select
									class="form-control center"
									name="slcAgencias"
									id="slcAgencias"
									data-index="5"
									v-model="agencia_busqueda"
									:disabled="
										modo == 'personal' ||
										lista_asistencias_filtrados.length == 0
									"
								>
									<option :value="0">TODAS</option>
									<option
										v-for="(item, index) in agencias"
										:key="index"
										:value="item.nombre"
									>
										{{ item.nombre }}
									</option>
								</select>
							</div>
							<div class="input-group col-md-4 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">TURNO</span>
								</div>
								<select
									class="form-control center"
									name="turnos"
									id="slcTurnos"
									v-model="turno_busqueda"
									:data-index="modo == 'personal' ? 5 : 6"
									:disabled="lista_asistencias_filtrados.length == 0"
								>
									<option value="0">TODOS</option>
									<option value="ingreso_mañana">Turno mañana</option>
									<option value="ingreso_tarde">Turno tarde</option>
								</select>
							</div>
							<div class="input-group col-md-4 mt-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbPorUsuario"
											v-model="por_asesor"
											:disabled="
												modo == 'personal' ||
												lista_asistencias_filtrados.length == 0
											"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbPorUsuario"
									>
										COLABORADOR
									</label>
								</div>
								<div class="input-group-prepend"></div>
								<select
									class="form-control center"
									v-model="usuario_seleccionado"
									id="slcUsuarios"
									:disabled="!por_asesor || modo == 'personal'"
									data-index="0"
								>
									<option :value="0">Seleccione...</option>
									<option
										v-for="(item, index) in usuarios_filtrados"
										:key="index"
										:value="item.dni"
									>
										{{ item.usuario }}
									</option>
								</select>
							</div>

							<div class="input-group col-md-2 mt-1 offset-7">
								<div class="form-check">
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
							</div>
						</div>

						<div class="card-title">LISTA DE RESULTADOS</div>

						<table
							class="table"
							id="t_reporte_asistencias"
							style="width: 100% !important"
						>
							<thead>
								<tr>
									<th style="min-width: 80px !important">DNI</th>
									<th
										style="min-width: 60px !important"
										v-if="modo == 'completo'"
									>
										DESCARGAR
									</th>
									<th style="min-width: 80px !important">FOTO</th>
									<th style="min-width: 200px !important">COLABORADOR</th>
									<th style="min-width: 100px !important">FECHA_MARCAJE</th>
									<th style="min-width: 100px !important">AGENCIA</th>
									<th style="min-width: 80px !important">TURNO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_asistencias_filtrados"
									:key="index"
									class="table-bordered"
								>
									<td align="center">
										{{ item.usuario_id }}
									</td>
									<td align="center" v-if="modo == 'completo'">
										<button
											class="btn btn-action"
											type="button"
											title="Descargar"
											@click="Descargar(item.foto)"
											v-if="item.foto != ''"
										>
											<span class="icon text-white">
												<i class="pi pi-download"></i>
											</span>
										</button>
									</td>
									<td align="center">
										<img
											class="fotoImage"
											:src="'/imagenes_server/gth/asistencias/' + item.foto"
											:alt="item.foto"
											width="50"
											height="60"
											v-if="item.foto != ''"
										/>
									</td>

									<td>
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td align="center">
										{{ item.hora_ingreso }}
									</td>
									<td align="center">
										{{ item.nombre_agencia }}
									</td>
									<td align="center">
										{{ item.turno }}
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
								:disabled="lista_asistencias_filtrados.length == 0"
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
export default {
	components: {
		layout,
		headerClose,
	},
	props: { modo: String, usuarios: Array, agencias: Array },
	data() {
		return {
			windowWidth: window.innerWidth,
			asistencias: [],
			lista_asistencias_filtrados: [],
			lista_asistencias: [],

			agencia_busqueda: 0,
			turno_busqueda: 0,

			usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],
			por_asesor: this.modo == "personal" ? true : false,
			usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,
			mostrar_habilitados: true,
		};
	},
	watch: {
		lista_asistencias_filtrados() {
			$("#t_reporte_asistencias").DataTable().destroy();
			this.TablaAsistencias();
		},

		agencia_busqueda() {
			this.FiltrarUsuarios();
		},
		por_asesor() {
			this.usuario_seleccionado = 0;
		},
	},
	mounted() {
		this.TablaAsistencias();
		this.DiaActual();
	},
	methods: {
		TablaAsistencias() {
			this.$nextTick(() => {
				var table = $("#t_reporte_asistencias").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,

					scrollCollapse: true,
					paging: false,
					fixedHeader: true,
					info: true,

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
				$("#slcTurnos").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#slcUsuarios").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#slcAgencias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});
			});
		},

		Exportar() {
			self = this;

			self.Filtrar();

			let data = new FormData();
			data.append("datos", JSON.stringify(this.lista_asistencias));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					//   this.$inertia.post(route("gth.asi.asistencias.exportar"), data);

					axios
						.post(route("gth.asi.asistencias.exportar"), data)
						.then(function (response) {
							// return Swal.fire({
							//   icon: "success",
							//   title: "¡Listo!",
							// });

							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptAsistencias.xlsx";

							downloadLink.href = linkSource;
							downloadLink.download = fileName;
							downloadLink.click();

							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
								timer: 2000,
								showConfirmButton: false,
							});
						});
				},
			});
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		DiaActual() {
			let fecha = new Date(); //Fecha actual
			let mes = fecha.getMonth() + 1; //obteniendo mes
			let dia = fecha.getDate(); //obteniendo dia
			let ano = fecha.getFullYear(); //obteniendo año
			if (dia < 10) dia = "0" + dia; //agrega cero si el menor de 10
			if (mes < 10) mes = "0" + mes;

			let desde = ano + "-" + mes + "-" + dia;
			let hasta = ano + "-" + mes + "-" + dia;

			$("#dtpDesde").val(desde);
			$("#dtpHasta").val(hasta);
			//   this.Buscar();
		},

		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.usuarios_seleccionados = [];

				if (this.mostrar_habilitados) {
					if (this.agencia_busqueda == 0) {
						this.usuarios_filtrados = this.usuarios.filter(
							(item) => item.habilitado == 1
						);
					} else {
						this.usuarios_filtrados = this.usuarios.filter(
							(item) =>
								item.nombre == this.agencia_busqueda && item.habilitado == 1
						);
					}
				} else {
					if (this.agencia_busqueda == 0) {
						this.usuarios_filtrados = this.usuarios;
					} else {
						this.usuarios_filtrados = this.usuarios.filter(
							(item) => item.nombre == this.agencia_busqueda
						);
					}
				}
			} else if (this.modo == "personal") {
				this.usuarios_filtrados = this.usuarios;
			}
		},

		Buscar() {
			self = this;
			let data = new FormData();
			data.append("f_desde", $("#dtpDesde").val());
			data.append("f_hasta", $("#dtpHasta").val());

			//   this.$inertia.post(route("gth.asi.asistencias.listar"), data);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("gth.asi.asistencias.listar"), data);
					axios
						.post(route("gth.asi.asistencias.listar"), data)
						.then(function (response) {
							if (response.data.length == 0) {
								self.lista_asistencias_filtrados = [];

								self.FiltrarUsuarios();

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.asistencias = response.data;

								self.FiltrarModo();
								self.FiltrarUsuarios();

								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
								});
							}
						});
				},
			});
		},
		FiltrarModo() {
			if (this.modo == "completo") {
				this.lista_asistencias_filtrados = this.asistencias;
			}

			if (this.modo == "personal") {
				let id_asesor = this.usuario_seleccionado;

				this.lista_asistencias_filtrados = this.asistencias.filter(
					(item) => item.usuario_id == id_asesor
				);
			}
		},

		Filtrar() {
			this.lista_asistencias = this.lista_asistencias_filtrados;

			let id_turno = this.turno_busqueda;
			let id_asesor = this.usuario_seleccionado;
			let id_agencia = this.agencia_busqueda;

			if (this.usuario_seleccionado != 0) {
				this.lista_asistencias = this.lista_asistencias.filter(
					(item) => item.usuario_id == id_asesor
				);
			}

			if (id_turno != 0) {
				this.lista_asistencias = this.lista_asistencias.filter(
					(item) => item.turno == id_turno
				);
			}
			if (id_agencia != 0) {
				this.lista_asistencias = this.lista_asistencias.filter(
					(item) => item.nombre_agencia == id_agencia
				);
			}
		},
		async Descargar(imagen) {
			let source = "/imagenes_server/gth/asistencias/" + imagen;

			try {
				const response = await axios.head(source); // Send a HEAD request to the URL
				if (response.status >= 200 && response.status < 300) {
					axios.get(source, { responseType: "blob" }).then((response) => {
						const blob = new Blob([response.data], {
							type: response.data.type,
						});
						const link = document.createElement("a");
						link.href = URL.createObjectURL(blob);
						link.download = imagen;
						link.click();
						URL.revokeObjectURL(link.href);
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "No se puede descargar esta foto.",
					});
				}
			} catch (error) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "No se puede descargar esta foto.",
				});
			}
		},
	},
};
</script>

<style >
.slot-asistencias {
	width: 60% !important;
	margin-left: 20% !important;
}

@media (max-width: 900px) {
	.slot-asistencias {
		width: 98% !important;
		margin-left: 1% !important;
	}
}

.box {
	position: relative;
}
</style>
