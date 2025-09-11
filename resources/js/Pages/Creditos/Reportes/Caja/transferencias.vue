<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-transferencias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose title="TRANSFERENCIAS"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10">
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
											v-model="agencia_seleccionada"
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

							<div class="input-group col-md-6">
								<div class="input-group-prepend">
									<label
										class="input-group-text prepend-title"
										style="font-size: 13px"
									>
										DE
									</label>
								</div>

								<select
									class="form-control center"
									v-model="de_estado_seleccionado"
								>
									<option :value="'CUENTA'" v-if="windowWidth >= 900">
										CUENTA
									</option>
									<option :value="'CUENTA'" v-if="windowWidth < 900">
										Cuenta
									</option>

									<option :value="'CAJA'" v-if="windowWidth >= 900">
										CAJA
									</option>
									<option :value="'CAJA'" v-if="windowWidth < 900">Caja</option>
								</select>

								<div class="input-group-prepend">
									<div
										class="input-group-text"
										style="padding-right: 0rem !important"
									>
										<input
											type="checkbox"
											id="chbDeUsuario"
											v-model="de_usuario"
										/>
										<label
											class="input-group-text prepend-title"
											for="chbDeUsuario"
											style="border: none"
										>
											USUARIO
										</label>
									</div>
								</div>

								<select
									class="form-control center"
									v-model="de_usuario_seleccionado"
									:disabled="!de_usuario"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in de_usuarios_filtrados"
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
											id="chbMostrarHabilitadosDe"
											v-model="de_mostrar_habilitados"
											:disabled="!de_usuario"
											@change="FiltrarUsuarios"
										/>
										<label
											class="m-0 ml-1"
											for="chbMostrarHabilitadosDe"
											v-if="windowWidth >= 900"
											>Habilitados</label
										>
									</div>
								</div>
							</div>

							<div class="input-group col-md-6">
								<div class="input-group-prepend">
									<label
										class="input-group-text prepend-title"
										style="font-size: 13px"
									>
										A
									</label>
								</div>

								<select
									class="form-control center"
									v-model="a_estado_seleccionado"
								>
									<option :value="'CUENTA'" v-if="windowWidth >= 900">
										CUENTA
									</option>
									<option :value="'CUENTA'" v-if="windowWidth < 900">
										Cuenta
									</option>

									<option :value="'CAJA'" v-if="windowWidth >= 900">
										CAJA
									</option>
									<option :value="'CAJA'" v-if="windowWidth < 900">Caja</option>
								</select>

								<div class="input-group-prepend">
									<div
										class="input-group-text"
										style="padding-right: 0rem !important"
									>
										<input
											type="checkbox"
											id="chbAUsuario"
											v-model="a_usuario"
										/>
										<label
											class="input-group-text prepend-title"
											for="chbAUsuario"
											style="border: none"
										>
											USUARIO
										</label>
									</div>
								</div>

								<select
									class="form-control center"
									v-model="a_usuario_seleccionado"
									:disabled="!a_usuario"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in a_usuarios_filtrados"
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
											id="chbMostrarHabilitadosA"
											v-model="a_mostrar_habilitados"
											:disabled="!a_usuario"
											@change="FiltrarUsuarios"
										/>
										<label
											class="m-0 ml-1"
											for="chbMostrarHabilitadosA"
											v-if="windowWidth >= 900"
											>Habilitados</label
										>
									</div>
								</div>
							</div>
						</div>

						<div class="card-title mt-2">LISTA DE RESULTADOS</div>
						<table class="table" id="tblTransferencias" width="100%">
							<thead>
								<tr>
									<th style="min-width: 20px !important">N°</th>
									<th style="min-width: 125px !important">EMISOR</th>
									<th style="min-width: 125px !important">RECEPTOR</th>
									<th style="min-width: 90px !important">MONTO</th>
									<th style="min-width: 75px !important">ESTADO</th>
									<th style="min-width: 350px !important">DESCRIPCION</th>
									<th style="min-width: 125px !important">FECHA_ENVIO</th>
									<th style="min-width: 50px !important">FECHA_CONFIRMADO</th>
									<th style="min-width: 200px !important">
										COMENTARIO_RECHAZO
									</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_transferencias"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
								>
									<td align="center">{{ index + 1 }}</td>

									<td align="center">
										{{ item.usuario_emisor }}
									</td>
									<td align="center">
										{{ item.usuario_receptor }}
									</td>
									<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>

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
										{{ item.descripcion }}
									</td>

									<td align="center">
										{{ item.fecha_envio }}
									</td>

									<td align="center">
										{{ item.fecha_recepcion }}
									</td>
									<td align="left">
										{{
											item.comentario_rechazo == null
												? "-"
												: item.comentario_rechazo
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
								:disabled="lista_transferencias.length == 0"
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		usuarios: Array,
	},

	data() {
		return {
			submited: false,

			agencias_permitidas: [],
			agencia_seleccionada: null,

			windowWidth: window.innerWidth,

			fecha_desde: null,
			fecha_hasta: null,

			de_usuario: false,
			a_usuario: false,

			por_estado: false,

			de_usuarios_filtrados: [],
			a_usuarios_filtrados: [],

			de_usuario_seleccionado: 0,
			a_usuario_seleccionado: 0,

			de_mostrar_habilitados: true,
			a_mostrar_habilitados: true,

			de_estado_seleccionado: "CUENTA",
			a_estado_seleccionado: "CUENTA",

			lista_transferencias: [],
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
			this.de_usuario_seleccionado = 0;
			this.a_usuario_seleccionado = 0;

			this.de_mostrar_habilitados = true;
			this.a_mostrar_habilitados = true;

			this.FiltrarUsuarios();
		},

		de_usuario() {
			this.de_usuario_seleccionado = 0;
		},

		a_usuario() {
			this.a_usuario_seleccionado = 0;
		},

		lista_transferencias() {
			$("#tblTransferencias").DataTable().destroy();
			this.TablaTransferencias();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaTransferencias();

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
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			if (valor == 0) {
				return "-";
			} else {
				return parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
		},

		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_TRANSFERENCIAS"
			);
		},
		FiltrarUsuarios() {
			if (this.de_mostrar_habilitados) {
				this.de_usuarios_filtrados = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_seleccionada && item.habilitado == 1
				);
			} else {
				this.de_usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			}

			if (this.a_mostrar_habilitados) {
				this.a_usuarios_filtrados = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_seleccionada && item.habilitado == 1
				);
			} else {
				this.a_usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			}
		},

		TablaTransferencias() {
			this.$nextTick(() => {
				let scroll_height = "300px";
				if (this.windowWidth <= 900) {
					scroll_height = "170px";
				}
				var table = $("#tblTransferencias").DataTable({
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
			});
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			data.append("de_usuario", this.de_usuario);
			data.append("a_usuario", this.a_usuario);

			data.append("de_estado_seleccionado", this.de_estado_seleccionado);
			data.append("a_estado_seleccionado", this.a_estado_seleccionado);

			if (this.de_usuario) {
				data.append("de_usuario_id", this.de_usuario_seleccionado);
			}
			if (this.a_usuario) {
				data.append("a_usuario_id", this.a_usuario_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					//   this.$inertia.post(route("rep.caj.transferencias.buscar"), data);
					axios
						.post(route("rep.caj.transferencias.buscar"), data)
						.then(function (response) {
							if (response.data.lista_transferencias.length == 0) {
								self.lista_transferencias = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_transferencias = response.data.lista_transferencias;
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
			data.append(
				"lista_transferencias",
				JSON.stringify(this.lista_transferencias)
			);
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			data.append("de_estado_seleccionado", this.de_estado_seleccionado);
			data.append("a_estado_seleccionado", this.a_estado_seleccionado);

			//   this.$inertia.post(route("rep.caj.transferencias.exportar"), data);
			//   return false;

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("rep.caj.transferencias.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptTransferencias.xlsx";
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
.pendiente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.confirmado {
	background-color: var(--plomoOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	background-color: var(--red) !important;
	color: white !important;
}
.slot-reporte-transferencias {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-reporte-transferencias {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
