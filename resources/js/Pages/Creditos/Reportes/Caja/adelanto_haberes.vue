<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-adelanto-haberes" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ADELANTO DE HABERES'"></headerClose>

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
											class="form-control center bolder"
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
											class="form-control center bolder"
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
											v-if="
												agencia_seleccionada != 0 &&
												agencia_seleccionada != null
											"
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
									v-if="
										agencia_seleccionada != 0 && agencia_seleccionada != null
									"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="form-group col-md-12">
								<div class="form-check">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbPorUsuario"
										v-model="filtro_usuario_check"
									/>
									<label class="label-title" for="chbPorUsuario"
										>Filtrar por COLABORADOR</label
									>

									<label
										class="subrayado label-title ml-2"
										@click="VerUsuarios"
										v-if="usuarios_seleccionados.length > 0"
										>{{ usuarios_seleccionados.length }} usuario
										seleccionado(s)</label
									>
								</div>
							</div>
						</div>

						<div class="card-title">LISTA DE RESULTADOS</div>

						<DataTable
							:value="lista_adelantos"
							:scrollable="true"
							scrollDirection="both"
							:scrollHeight="String(windowHeigth * 0.47) + 'px'"
							:paginator="true"
							:rows="30"
							paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
							currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
							showGridlines
							stripedRows
						>
							<Column
								field="index"
								header="N°"
								:styles="{ width: '30px', justifyContent: 'center' }"
							/>

							<Column
								field="fecha_adelanto"
								header="FECHA"
								:styles="{ width: '140px', justifyContent: 'center' }"
							/>

							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{ width: '100px', justifyContent: 'center' }"
							/>

							<Column
								field="colaborador"
								header="COLABORADOR"
								:styles="{
									width: '220px',
									justifyContent: 'left',
									fontWeight: 'bolder',
								}"
							>
								<template #body="{ data }">
									{{
										data.nombres +
										" " +
										data.apellido_paterno +
										" " +
										data.apellido_materno
									}}
								</template>
							</Column>

							<Column
								field="monto"
								header="MONTO"
								:styles="{ width: '100px', justifyContent: 'right' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.monto, 2) }}
								</template>
							</Column>

							<Column
								field="usuario_registro"
								header="CAJA"
								:styles="{ width: '100px', justifyContent: 'center' }"
							/>

							<Column
								field="motivo"
								header="MOTIVO"
								:styles="{ width: '450px', justifyContent: 'left' }"
							>
								<template #body="{ data }">
									{{ data.descripcion == null ? "-" : data.descripcion }}
								</template>
							</Column>
							<ColumnGroup type="footer">
								<Row>
									<Column
										:colspan="4"
										footer="TOTAL"
										:footerStyle="{
											width: '490px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>
									<Column
										:footer="'S/ ' + roundTo(total_adelantos, 2)"
										:footerStyle="{
											width: '100px',
											'text-align': 'right',
											'font-size': '13px !important',
										}"
									/>

									<Column
										:colspan="2"
										footer=""
										:footerStyle="{
											width: '550px',
											backgroundColor: 'transparent !important',
											'text-align': 'right',
										}"
									/>
								</Row>
							</ColumnGroup>
							<template #empty> No hay COBRANZAS registradas.</template>
						</DataTable>

						<hr />
						<div class="text-right">
							<div class="btn-group" role="group">
								<button
									class="btn btn-cancel btn-icon-split"
									title="Exportar Agrupados"
									@click="ExportarAgrupado"
									:disabled="lista_adelantos.length == 0"
								>
									<span class="icon text-white">
										<i class="fas fa-file-excel"></i>
									</span>
									<span class="text" v-if="windowWidth >= 900">AGRUPADO</span>
									<span class="text" v-if="windowWidth < 900">EXPO. AGRU.</span>
								</button>
								<button
									class="btn btn-action btn-icon-split"
									title="Exportar"
									@click="Exportar"
									:disabled="lista_adelantos.length == 0"
								>
									<span class="icon text-white">
										<i class="fas fa-file-excel"></i>
									</span>
									<span class="text" v-if="windowWidth >= 900">DETALLADO</span>
									<span class="text" v-if="windowWidth < 900">EXPO. DETA.</span>
								</button>
							</div>
						</div>
					</div>
				</div>

				<FiltroUsuario
					ref="filtro_usuario"
					@usuarios-seleccionados="RecibirUsuarios"
					@agencia-seleccionada="RecibirAgencia"
					@habilitado-seleccionado="RecibirHabilitado"
					@cerrar-modal="CerrarModal"
				></FiltroUsuario>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import FiltroUsuario from "@/Pages/Creditos/Components/filtro_usuario.vue";
import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import Row from "primevue/row/row.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";

export default {
	components: {
		layout,
		headerClose,
		FiltroUsuario,
		DataTable,
		Column,
		Row,
		ColumnGroup,
	},
	props: {
		usuarios: Array,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,
			agencia_filtro: 0,

			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			fecha_desde: null,
			fecha_hasta: null,

			filtro_usuario: false,
			mostrar_habilitados: true,

			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			lista_adelantos: [],
			total_adelantos: 0.0,

			filtro_usuario_check: false,
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
			this.filtro_usuario_check = false;
		},

		filtro_usuario_check() {
			const self = this;

			this.usuarios_seleccionados = [];

			if (this.filtro_usuario_check) {
				let filtro_usuarios = this.$refs.filtro_usuario;
				async function EnviarDatos() {
					filtro_usuarios.usuarios = self.usuarios;
					filtro_usuarios.mostrar_habilitados = true;
					filtro_usuarios.modo_agencias = true;

					filtro_usuarios.agencias_permitidas = self.agencias;
					filtro_usuarios.agencia_seleccionada = self.agencia_seleccionada;
				}

				EnviarDatos().then(() => {
					$("#filtro_usuario").css("display", "block");
				});
			}
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
		});
	},
	methods: {
		async FechaActual() {
			if (this.agencia_seleccionada == 0) {
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

			let resultado = parseFloat(valor).toLocaleString("es-PE", {
				minimumFractionDigits: numero_decimales,
				maximumFractionDigits: numero_decimales,
			});

			return resultado;
		},
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/CAJA_ADELANTO_HABERES"
			);
		},

		VerUsuarios() {
			const self = this;
			let filtro_usuarios = this.$refs.filtro_usuario;
			async function EnviarDatos() {
				filtro_usuarios.modo_agencias = true;
				filtro_usuarios.usuarios = self.usuarios;
				filtro_usuarios.agencias_permitidas = self.agencias;
				filtro_usuarios.agencia_seleccionada = self.agencia_filtro;
				filtro_usuarios.mostrar_habilitados = self.mostrar_habilitados;
			}

			EnviarDatos().then(() => {
				$("#filtro_usuario").css("display", "block");
				filtro_usuarios.usuarios_seleccionados = self.usuarios_seleccionados;
			});
		},
		RecibirUsuarios(usuarios_select) {
			this.usuarios_seleccionados = usuarios_select;
			this.filtro_usuario_check = true;
		},
		RecibirAgencia(agencia) {
			this.agencia_filtro = agencia;
		},
		RecibirHabilitado(habilitado) {
			this.mostrar_habilitados = habilitado;
		},
		CerrarModal() {
			if (this.usuarios_seleccionados.length == 0) {
				this.filtro_usuario_check = false;
			}
		},

		async Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("filtro_usuario", this.filtro_usuario_check);

			if (this.filtro_usuario_check) {
				data.append("usuarios", JSON.stringify(this.usuarios_seleccionados));
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.post(route("rep.caj.adelanto_haberes.buscar"), data);
					// return false;
					Swal.showLoading();
					await axios
						.post(route("rep.caj.adelanto_haberes.buscar"), data)
						.then(function (response) {
							if (response.data.adelantos.length == 0) {
								self.lista_adelantos = [];
								self.total_adelantos = 0;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_adelantos = response.data.adelantos;
								self.total_adelantos = response.data.total;
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
		async Exportar() {
			let data = new FormData();

			data.append("lista_adelantos", JSON.stringify(this.lista_adelantos));
			data.append("total_adelantos", this.total_adelantos);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					// this.$inertia.post(route("rep.caj.adelanto_haberes.exportar"), data);

					await axios
						.post(route("rep.caj.adelanto_haberes.exportar"), data)
						.then(function (response) {
							const path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.click();

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
		async ExportarAgrupado() {
			let data = new FormData();

			data.append("lista_adelantos", JSON.stringify(this.lista_adelantos));
			data.append(
				"fechas",
				`Desde ${this.fecha_desde} hasta ${this.fecha_hasta}`
			);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					await axios
						.post(route("rep.caj.adelanto_haberes.exportar_agrupado"), data)
						.then(function (response) {
							const path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.click();

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
.slot-reporte-adelanto-haberes {
	width: 68% !important;
	margin-left: 16% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media only screen and (max-width: 900px) {
	.slot-reporte-adelanto-haberes {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>

