<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-envios-agencia" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose title="ENVÍOS ENTRE AGENCIAS"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-9">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
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
											v-model="agencia_busqueda"
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
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_desde"
											style="font-size: 16px !important"
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
											style="font-size: 16px !important"
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
							<div class="input-group col-md-8">
								<div class="input-group-prepend">
									<span class="input-group-text">Buscar </span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									id="inpBuscarEstado"
									:disabled="lista_envios_agencia.length == 0"
									placeholder="Ingrese 3 caractéres como mínimo..."
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
							<div class="input-group col-md-3">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">ESTADO</span>
								</div>
								<select
									class="form-control center"
									name="slcEstado"
									id="slcEstado"
									v-model="estado_busqueda"
									data-index="6"
									:disabled="lista_envios_agencia.length == 0"
								>
									<option value="0">TODOS</option>
									<option value="CONFIRMADO">CONFIRMADO</option>
									<option value="PENDIENTE">PENDIENTE</option>
									<option value="RECHAZADO">RECHAZADO</option>
								</select>
							</div>
						</div>
						<table class="table" id="tblEnviosAgencia" width="100%">
							<thead>
								<tr>
									<th style="min-width: 10px !important">N°</th>
									<th style="min-width: 70px !important">AGENCIA_ENVÍO</th>
									<th style="min-width: 70px !important">USUARIO_ENVÍO</th>
									<th style="min-width: 70px !important">AGENCIA_RECEPCIÓN</th>
									<th style="min-width: 70px !important">USUARIO_RECEPCIÓN</th>
									<th style="min-width: 70px !important">MONTO</th>
									<th style="min-width: 70px !important">ESTADO</th>
									<th style="min-width: 250px !important">CONCEPTO</th>
									<th style="min-width: 120px !important">FECHA_ENVÍO</th>
									<th style="min-width: 120px !important">FECHA_RECEPCIÓN</th>
									<th style="min-width: 70px !important">TIPO</th>
									<th style="min-width: 70px !important">USUARIO_GESTIÓN</th>
									<th style="min-width: 150px !important">ENTIDAD</th>
									<th style="min-width: 120px !important">COMPROBANTES</th>
									<th style="min-width: 250px !important">
										COMENTARIO_RECHAZO
									</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_envios_agencia"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
								>
									<td align="center">{{ index + 1 }}</td>

									<td align="center">
										{{ item.agencia_envio }}
									</td>
									<td align="center">
										{{ item.usuario_envio }}
									</td>
									<td align="center">
										{{ item.agencia_recepcion }}
									</td>
									<td align="center">
										{{ item.usuario_recepcion }}
									</td>
									<td align="right">
										S/
										{{ parseFloat(item.monto).toFixed(2) }}
									</td>
									<td
										align="center"
										:class="[
											item.estado == 'PENDIENTE'
												? 'pendiente'
												: item.estado == 'CONFIRMADO'
												? 'confirmado'
												: 'rechazado',
										]"
									>
										{{ item.estado }}
									</td>
									<td align="left">
										{{ item.concepto }}
									</td>
									<td align="center">
										{{ item.fecha_envio }}
									</td>
									<td align="center">
										{{
											item.fecha_recepcion != null ? item.fecha_recepcion : "-"
										}}
									</td>
									<td align="center">
										{{ item.tipo }}
									</td>
									<td align="center">
										{{ item.usuario_gestion }}
									</td>
									<td align="left">
										{{ item.entidad != null ? item.entidad : "-" }}
									</td>
									<td align="center" style="min-width: 100px">
										<div class="text-center">
											<button
												class="btn btn-cancel btn-icon-split"
												title="Comprobante de ENVÍO"
												@click="
													VerComprobante(
														item.agencia_remitente_id,
														item.comprobante_envio,
														'ENVÍO'
													)
												"
											>
												<span class="icon text-white">
													<i class="far fa-eye" style="color: white"></i>
												</span>
											</button>
											<div
												class="btn-group"
												role="group"
												v-if="item.estado == 'CONFIRMADO'"
											>
												<button
													class="btn btn-action btn-icon-split"
													title="Comprobante de RECEPCIÓN"
													@click="
														VerComprobante(
															item.agencia_remitente_id,
															item.comprobante_recepcion,
															'RECEPCIÓN'
														)
													"
												>
													<span class="icon text-white">
														<i class="far fa-eye" style="color: white"></i>
													</span>
												</button>
											</div>
										</div>
									</td>
									<td align="left">
										{{
											item.comentario_rechazo != null
												? item.comentario_rechazo
												: "-"
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
								:disabled="lista_envios_agencia.length == 0"
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

			<div id="mdlComprobante" class="modal">
				<div class="modal-content w-40 mdlComprobante">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								ref="headerCloseModal"
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlComprobante'"
							>
							</headerCloseModal>
							<div class="card-body card-block">
								<div class="p-2">
									<div
										style="
											width: 100% !important;
											height: 300px !important;
											box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
										"
									>
										<img
											:src="
												'/imagenes_server/creditos/cuenta/envios/' +
												agencia_comprobante +
												'/' +
												comprobante_vista.substring(0, 4) +
												'/' +
												comprobante_vista
											"
											v-if="comprobante_vista != null"
											alt="ruta"
											width="100%"
											height="300px"
										/>
									</div>
								</div>

								<hr />
								<div class="text-center">
									<button
										class="btn btn-cancel btn-icon-split"
										@click="Descargar"
									>
										<span class="icon text-white">
											<i class="fas fa-download"></i>
										</span>
										<span class="text">DESCARGAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {},

	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			agencia_busqueda: 0,

			windowWidth: window.innerWidth,
			lista_envios_agencia: [],

			titulo_modal: null,
			comprobante_vista: null,

			agencia_comprobante: null,

			estado_busqueda: 0,

			fecha_desde: null,
			fecha_hasta: null,
		};
	},
	validations: {},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
				} else {
					this.agencia_busqueda = null;
				}
			}
		},

		agencia_busqueda() {
			this.FechaActual();
		},

		lista_envios_agencia() {
			$("#tblEnviosAgencia").DataTable().destroy();
			this.TablaEnviosAgencia();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaEnviosAgencia();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_ENVIOS_AGENCIAS"
			);
		},
		async FechaActual() {
			if (this.agencia_busqueda == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_busqueda
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		VerComprobante(agencia_remitente_id, comprobante, tipo) {
			this.titulo_modal = "COMPROBANTE DE " + tipo;
			this.agencia_comprobante = agencia_remitente_id;
			this.comprobante_vista = comprobante;
			$("#mdlComprobante").css("display", "block");
		},
		Descargar() {
			let agencia_id = this.agencia_comprobante;
			let año = this.comprobante_vista.substring(0, 4);

			let source =
				"/imagenes_server/creditos/cuenta/envios/" +
				agencia_id +
				"/" +
				año +
				"/" +
				this.comprobante_vista;
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = this.comprobante_vista;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},

		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},
		Buscar() {
			let self = this;
			let data = new FormData();

			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("rep.caj.envios_agencias.buscar"), data);
					// return false;
					axios
						.post(route("rep.caj.envios_agencias.buscar"), data)
						.then(function (response) {
							if (response.data.lista_envios_agencia.length == 0) {
								self.lista_envios_agencia = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_envios_agencia = response.data.lista_envios_agencia;
								self.estado_busqueda = 0;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
								});
							}
						});
				},
			});
		},
		TablaEnviosAgencia() {
			this.$nextTick(() => {
				let scroll_height = "300px";
				if (this.windowWidth <= 900) {
					scroll_height = "160px";
				}
				var table = $("#tblEnviosAgencia").DataTable({
					scrollY: scroll_height,
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

				$("#inpBuscarEstado").keyup(function () {
					table.search(this.value).draw();
				});

				$("#slcEstado").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});
			});
		},
		Filtrar() {
			this.lista_envios_agencia_filtrado = this.lista_envios_agencia;

			let estado_busqueda_filtrado = this.estado_busqueda;

			if (estado_busqueda_filtrado != 0) {
				this.lista_envios_agencia_filtrado = this.lista_envios_agencia.filter(
					(item) => item.estado == estado_busqueda_filtrado
				);
			} else {
				this.lista_envios_agencia_filtrado = this.lista_envios_agencia;
			}
		},
		Exportar() {
			self = this;

			self.Filtrar();
			let data = new FormData();
			data.append(
				"datos_tabla",
				JSON.stringify(this.lista_envios_agencia_filtrado)
			);
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			let titulo = "EXPORTANDO";

			// this.$inertia.post(route("rep.caj.envios_agencias.exportar"), data);
			// return false;

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.caj.envios_agencias.exportar"), data)
						.then(function (response) {
							let origin = window.location.origin;
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptEnviosAgencia.xlsx";
							link.click();
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
	},
};
</script>

<style lang="css">
.slot-reporte-envios-agencia {
	width: 70% !important;
	margin-left: 15% !important;
}
.pendiente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.confirmado {
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	background-color: var(--red) !important;
	color: white !important;
}

@media (max-width: 900px) {
	.slot-reporte-envios-agencia {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
