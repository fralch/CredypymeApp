<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-clientes-movimientos"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MOVIMIENTOS DE CLIENTES'"></headerClose>

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
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_desde"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_hasta"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
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

							<div class="col-md-1 ml-3" v-if="windowWidth >= 900">
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

							<div class="form-row col-md-12">
								<div class="input-group col-md-5">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbPorAsesor"
												v-model="por_asesor"
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

									<select
										class="form-control center"
										v-model="asesor_seleccionado"
										:disabled="!por_asesor"
									>
										<option :value="0" disabled selected>Seleccione...</option>
										<option
											v-for="(item, index) in asesores_filtrados"
											:key="index"
											:value="item.dni"
										>
											{{ item.usuario }}
										</option>
									</select>
									<div class="input-group-append">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbHabilitados"
												v-model="solo_habilitados"
												:disabled="!por_asesor"
											/>
											<label class="m-0 ml-1" for="chbHabilitados"
												>Habilitados</label
											>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card-title mt-2">LISTA DE RESULTADOS</div>
						<table class="table" id="tblMovimientos" width="100%">
							<thead>
								<tr>
									<th style="min-width: 30px !important">N°</th>
									<th style="min-width: 70px !important">DNI</th>
									<th style="min-width: 200px !important">CLIENTE</th>
									<th style="min-width: 70px !important">ASESOR_ORIGEN</th>
									<th style="min-width: 70px !important">ASESOR_DESTINO</th>
									<th style="min-width: 100px !important">FECHA_REGISTRO</th>
									<th style="min-width: 100px !important">USUARIO_REGISTRO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_movimientos"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
								>
									<td align="center">
										{{ index + 1 }}
									</td>

									<td align="center">
										{{ item.dni }}
									</td>
									<td>
										{{ item.cliente }}
									</td>
									<td class="bolder" style="font-size: 12px" align="center">
										{{ item.usuario_asesor_origen }}
									</td>
									<td class="bolder" style="font-size: 12px" align="center">
										{{ item.usuario_asesor_destino }}
									</td>
									<td align="center">
										{{ item.fecha_movimiento }}
									</td>
									<td align="center">
										{{ item.usuario_registro }}
									</td>
								</tr>
							</tbody>
						</table>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar('XLSX')"
								:disabled="lista_movimientos.length == 0"
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
		usuarios: Array,
	},

	data() {
		return {
			submited: false,

			agencias_permitidas: [],
			agencia_seleccionada: null,

			fecha_desde: null,
			fecha_hasta: null,

			windowWidth: window.innerWidth,

			por_asesor: false,
			solo_habilitados: true,

			asesores_filtrados: [],
			asesor_seleccionado: 0,

			lista_movimientos: [],
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
			this.FechaActual();
			this.FiltrarUsuarios();
		},
		por_asesor() {
			this.asesor_seleccionado = 0;
		},

		lista_movimientos() {
			$("#tblMovimientos").DataTable().destroy();
			this.TablaMovimientos();
		},
		solo_habilitados() {
			this.FiltrarUsuarios();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaMovimientos();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		async FechaActual() {
			if (this.agencia_seleccionada == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_seleccionada
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CLIENTES_MOVIMIENTOS"
			);
		},
		TablaMovimientos() {
			this.$nextTick(() => {
				let scroll_height = "400px";
				if (this.windowWidth <= 900) {
					scroll_height = "200px";
				}
				var table = $("#tblMovimientos").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
					language: {
						retrieve: true,
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});
			});
		},

		FiltrarUsuarios() {
			this.asesores_filtrados = [];
			this.asesor_seleccionado = 0;

			if (this.solo_habilitados) {
				this.asesores_filtrados = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_seleccionada && item.habilitado == 1
				);
			} else {
				this.asesores_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			}
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
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
					// this.$inertia.post(route("rep.cli.movimientos.buscar"), data);
					axios
						.post(route("rep.cli.movimientos.buscar"), data)
						.then(function (response) {
							if (response.data.lista_movimientos.length == 0) {
								self.lista_movimientos = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_movimientos = response.data.lista_movimientos;
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

		Exportar(tipo) {
			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("lista_movimientos", JSON.stringify(this.lista_movimientos));
			data.append("tipo", tipo);

			let titulo = "";

			if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			} else if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			}

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("rep.cli.movimientos.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let linkSource =
									"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
									response.data;
								let downloadLink = document.createElement("a");
								let fileName = "rptClientesMovimientos.xlsx";

								downloadLink.href = linkSource;
								downloadLink.download = fileName;
								downloadLink.click();
							} else if (tipo == "PDF") {
								const contentType = "application/pdf",
									b64Data = response.data, //La información en base 64
									blob = b64toBlob(b64Data, contentType), //Decodificar la información
									blobUrl = URL.createObjectURL(blob);
								// Crear un IFrame.
								let iframe = document.createElement("iframe");
								// Ocultar el IFrame.
								iframe.style.display = "none";
								// Definir el source.
								iframe.src = blobUrl;
								// Añadir el IFrame a una página web.
								document.body.appendChild(iframe);
								iframe.contentWindow.focus();
								iframe.contentWindow.print(); // Imprimir.
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
.slot-reporte-clientes-movimientos {
	width: 60% !important;
	margin-left: 20% !important;
}

@media (max-width: 900px) {
	.slot-reporte-clientes-movimientos {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
