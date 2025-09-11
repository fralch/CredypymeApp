<template>
	<layout ref="layout">
		<div
			class="slot_body slot-reporte-desembolsos-auxiliar"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MIS ' : '') +
							'DESEMBOLSOS' +
							(modo == 'personal' ? ' - ' : ' POR ') +
							'AUXILIAR'
						"
					></headerClose>

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
											class="form-control center bolder"
											v-model="fecha_desde"
											:style="
												windowWidth >= 900
													? 'font-size: 16px !important'
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
													? 'font-size: 16px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
								</div>

								<div class="col-md-1 text-right" v-if="windowWidth < 900">
									<button
										class="btn btn-action btn-icon-split mt-3"
										title="Buscar"
										@click="Buscar"
										v-if="agencia_busqueda != 0 && agencia_busqueda != null"
									>
										<span class="icon text-white" style="font-size: 15px">
											<i class="fas fa-search"></i>
										</span>
									</button>
								</div>
							</fieldset>

							<div class="col-md-1 ml-3" v-if="windowWidth >= 900">
								<button
									class="btn btn-action btn-icon-split mt-3"
									title="Buscar"
									@click="Buscar"
									v-if="agencia_busqueda != 0 && agencia_busqueda != null"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="form-group col-md-12" v-if="modo == 'completo'">
								<div class="form-check">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbPorUsuario"
										v-model="filtro_usuario"
									/>
									<label class="label-title" for="chbPorUsuario"
										>Filtrar por AUXILIAR</label
									>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div
								class="w-20"
								style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)"
								v-if="filtro_usuario"
							>
								<div
									class="col-md-12 input-group mt-2"
									v-if="modo == 'completo'"
								>
									<select class="form-control center" v-model="agencia_filtro">
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

								<div
									class="form-check text-center mt-1"
									v-if="modo == 'completo'"
								>
									<input
										class="form-check-input"
										type="checkbox"
										id="chbMostrarHabilitados"
										v-model="mostrar_habilitados"
										@change="FiltrarUsuarios"
									/>
									<label class="label-title" for="chbMostrarHabilitados"
										>Sólo habilitados</label
									>
								</div>

								<DataTable
									:value="usuarios_filtrados"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="380px"
									showGridlines
								>
									<Column
										field="usuario"
										header="USUARIO"
										:styles="{ width: '70px', padding: '6px !important' }"
									>
										<template #body="{ data, index }">
											<div
												class="custom-control custom-checkbox pl-3 m-0"
												style="min-height: auto !important"
											>
												<input
													type="checkbox"
													class="custom-control-input"
													:id="'chbUsuario_' + index"
													:value="data.dni"
													v-model="usuarios_seleccionados"
												/>
												<label
													class="custom-control-label ml-4"
													:for="'chbUsuario_' + index"
													style="cursor: pointer"
													>{{ data.usuario }}</label
												>
											</div>
										</template>
									</Column>
								</DataTable>
							</div>
							<!-- -------------------------------------------------------- -->

							<div :class="filtro_usuario ? 'pl-1 w-80' : 'w-100'">
								<div class="card-title">LISTA DE RESULTADOS</div>

								<fieldset class="p-0 pb-1 mb-1">
									<legend>
										<label class="label-title">FILTRAR RESULTADOS</label>
									</legend>
									<div
										class="row"
										:class="
											filtro_usuario ? 'col-md-12' : 'col-md-10 offset-md-1'
										"
									>
										<div class="input-group col-md-4">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title">MODO</span>
											</div>
											<select
												class="form-control center"
												v-model="filtros_tabla['modo_desembolso'].value"
												:disabled="lista_desembolsos.length == 0"
											>
												>
												<option :value="null" selected>TODOS</option>
												<option value="OFICINA">OFICINA</option>
												<option value="DOMICILIO">DOMICILIO</option>
											</select>
										</div>
									</div>
								</fieldset>

								<DataTable
									:value="lista_desembolsos"
									:filters="filtros_tabla"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="350px"
									selectionMode="single"
									:paginator="true"
									:rows="50"
									paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
									showGridlines
								>
									<Column
										field="fecha_desembolso"
										header="FECHA"
										:styles="{ width: '130px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="codigo_expediente"
										header="EXPEDIENTE"
										:styles="{ width: '80px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="numero_credito"
										header="N°_CRED"
										:styles="{ width: '60px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="cliente"
										header="CLIENTE"
										:styles="{ width: '250px' }"
									>
										<template #body="{ data }">
											{{
												data.apellido_paterno +
												" " +
												data.apellido_materno +
												" " +
												data.nombres
											}}
										</template>
									</Column>
									<Column
										field="modo_desembolso"
										header="MODO"
										:styles="{ width: '80px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="monto_desembolso"
										header="CAPITAL"
										:styles="{ width: '100px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.monto, 2) }}
										</template>
									</Column>
									<Column
										field="plazo"
										header="PLAZO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="usuario_desembolso"
										header="CAJA"
										:styles="{ width: '80px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="tasa_interes"
										header="TASA"
										:styles="{ width: '70px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ roundTo(data.tasa_interes, 2) }} %
										</template>
									</Column>
									<Column
										field="usuario_asesor"
										header="ASESOR"
										:styles="{ width: '80px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="interes_total"
										header="INTERÉS"
										:styles="{ width: '100px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.interes_total, 2) }}
										</template>
									</Column>

									<Column
										field="importe_igv"
										header="IGV (18%)"
										:styles="{ width: '100px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.importe_igv, 2) }}
										</template>
									</Column>
									<Column
										field="importe_gravado"
										header="GRAVADO"
										:styles="{ width: '100px', justifyContent: 'right' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.importe_gravado, 2) }}
										</template>
									</Column>
									<Column
										field="seguimiento"
										header="SEGUIMIENTO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<button
												class="btn btn-action btn-icon-split"
												@click="VerSeguimiento(data)"
												title="Ver SEGUIMIENTO al cliente"
												v-if="data.cantidad_comentarios > 0"
											>
												<span class="icon text-white">
													<i class="fas fa-clock"></i>
												</span>
											</button>
										</template>
									</Column>

									<ColumnGroup type="footer">
										<Row>
											<Column
												:colspan="4"
												footer="TOTAL"
												:footerStyle="{
													width: '600px',
													backgroundColor: '#244b9a !important',
													fontSize: '13px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="1"
												:footer="'S/ ' + roundTo(totales.total_capital, 2)"
												:footerStyle="{
													width: '100px',
													fontSize: '13px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="4"
												:footer="null"
												:footerStyle="{
													width: '330px',
													backgroundColor: 'transparent !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="1"
												:footer="'S/ ' + roundTo(totales.total_interes, 2)"
												:footerStyle="{
													width: '100px',
													fontSize: '13px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="2"
												:footer="null"
												:footerStyle="{
													width: '300px',
													backgroundColor: 'transparent !important',
													textAlign: 'right',
												}"
											/>
										</Row>
									</ColumnGroup>
									<template #empty> No hay desembolsos encontrados.</template>
								</DataTable>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<div class="btn-group" role="group">
								<button
									class="btn btn-action btn-icon-split"
									title="Exportar"
									@click="Exportar('PDF')"
									:disabled="lista_desembolsos.length == 0"
								>
									<span class="icon text-white">
										<i class="fas fa-print"></i>
									</span>
									<span class="text">IMPRIMIR</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split"
									title="Exportar"
									@click="Exportar('XLSX')"
									:disabled="lista_desembolsos.length == 0"
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

			<!-- The Modal -->
			<div id="mdlSeguimiento" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-40 mdlSeguimiento">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'SEGUIMIENTO DE INACTIVDAD'"
								:nombre_modal="'mdlSeguimiento'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<div class="card-title mb-1">LISTA DE RESULTADOS</div>

								<DataTable
									:value="lista_seguimiento"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="350px"
									selectionMode="single"
								>
									<Column
										field="fecha_comentario"
										header="FECHA"
										:styles="{
											width: '130px',
											justifyContent: 'center',
											fontWeight: 'bolder',
										}"
									>
									</Column>
									<Column
										field="usuario_registro"
										header="USUARIO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
									</Column>

									<Column
										field="comentario"
										header="COMENTARIO"
										:styles="{ width: '250px' }"
									>
									</Column>
								</DataTable>
							</div>
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";
import { FilterMatchMode } from "primevue/api";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DataTable,
		Column,
		Row,
		ColumnGroup,
	},
	props: {
		modo: String,
		usuarios: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,

			agencias_permitidas: [],
			agencia_busqueda: 0,
			agencia_filtro: 0,

			fecha_desde: null,
			fecha_hasta: null,

			filtro_usuario: false,
			mostrar_habilitados: true,

			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			lista_desembolsos: [],
			totales: {
				total_capital: 0,
				total_interes: 0,
				cantidad_registros: 0,
			},

			filtros_tabla: {},

			lista_seguimiento: [],
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
				this.agencia_filtro = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
					this.agencia_filtro = mi_agencia[0].id;
				} else {
					this.agencia_busqueda = null;
					this.agencia_filtro = null;
				}
			}
		},
		agencia_busqueda() {
			this.FechaActual();
		},
		agencia_filtro() {
			this.FiltrarUsuarios();
		},
		filtro_usuario() {
			this.FiltrarUsuarios();
			if (this.modo == "personal") {
				this.usuarios_filtrados = this.usuarios;
				this.usuarios_seleccionados.push(this.usuarios[0].dni);
			}
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},
	created() {
		this.filtros_tabla = {
			global: { value: null, matchMode: FilterMatchMode.CONTAINS },
			modo_desembolso: { value: null, matchMode: FilterMatchMode.CONTAINS },
		};
	},
	methods: {
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
		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CAJA_MIS_DESEMBOLSOS_AUXILIAR"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CAJA_DESEMBOLSOS_AUXILIAR"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
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
		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.usuarios_seleccionados = [];

				if (this.filtro_usuario) {
					this.usuarios_filtrados = [];

					if (this.mostrar_habilitados) {
						this.usuarios_filtrados = this.usuarios.filter(
							(item) =>
								item.agencia_id == this.agencia_filtro && item.habilitado == 1
						);
					} else {
						this.usuarios_filtrados = this.usuarios.filter(
							(item) => item.agencia_id == this.agencia_filtro
						);
					}
				}
			} else if (this.modo == "personal") {
				return false;
			}
		},

		async Buscar() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("filtro_usuario", this.filtro_usuario);
			data.append("modo", "por_auxiliar");

			if (this.filtro_usuario) {
				data.append("usuarios", JSON.stringify(this.usuarios_seleccionados));
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.post(
					// 	route("rep.caj.desembolsos_auxiliar.buscar"),
					// 	data
					// );
					// return false;

					Swal.showLoading();

					await axios
						.post(route("rep.caj.desembolsos_auxiliar.buscar"), data)
						.then((response) => {
							if (response.data.lista_desembolsos.length == 0) {
								this.lista_desembolsos = [];
								this.totales = 0;
								this.filtros_tabla["modo_desembolso"].value = null;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								this.lista_desembolsos = response.data.lista_desembolsos;
								this.totales = response.data.totales;
								this.filtros_tabla["modo_desembolso"].value = null;
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
		async VerSeguimiento(item) {
			const cliente_id = item.cliente_id;
			const params = {
				agencia_id: item.agencia_cliente,
				fecha_desembolso: item.fecha_desembolso,
			};

			// this.$inertia.get(
			// 	route("rep.caj.desembolsos_auxiliar.seguimiento", {
			// 		cliente_id: cliente_id,
			// 	}),
			// 	params
			// );
			// return false;

			Swal.fire({
				title: "LISTANDO...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					await axios
						.get(
							route("rep.caj.desembolsos_auxiliar.seguimiento", {
								cliente_id: cliente_id,
							}),
							{ params }
						)
						.then(async (response) => {
							this.lista_seguimiento = response.data.lista_seguimiento;

							Swal.close();
							$("#mdlSeguimiento").css("display", "block");
							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
								timer: 1200,
								showConfirmButton: false,
							});
						})
						.catch((error) => {
							console.log(error);
							Swal.showValidationMessage(
								`Se ha producido un ERROR. 
											Por favor, no realice más acciones en el sistema y
											 contacte al área de SOPORTE para su revisión.`
							);
						});
				},
			});

			return false;
		},
		Exportar(tipo) {
			let data = new FormData();
			data.append("modo", "por_auxiliar");
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			let modo_desembolso = this.filtros_tabla["modo_desembolso"].value;
			let lista_desembolsos = [];

			if (modo_desembolso != null) {
				lista_desembolsos = JSON.stringify(
					this.lista_desembolsos.filter(
						(item) => item.modo_desembolso == modo_desembolso
					)
				);
			} else {
				lista_desembolsos = JSON.stringify(this.lista_desembolsos);
			}

			data.append("lista_desembolsos", lista_desembolsos);
			data.append("totales", JSON.stringify(this.totales));

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
						.post(route("rep.caj.desembolsos_auxiliar.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								// link.download = "rptSeguimientoFacturados.xlsx";
								link.click();
							} else if (tipo == "PDF") {
								let origin = window.location.origin;
								let path_pdf = response.data.path_pdf;

								// Crear un IFrame
								let iframe = document.createElement("iframe");
								// Oculto el iframe
								iframe.style.display = "none";
								// Defino el source
								iframe.src = origin + path_pdf;
								// Añadir el Iframe a la vista
								document.body.appendChild(iframe);

								iframe.contentWindow.focus(); // Enfoca
								iframe.contentWindow.print(); // Imprime
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
.slot-reporte-desembolsos-auxiliar {
	width: 70% !important;
	margin-left: 15% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media only screen and (max-width: 900px) {
	.slot-reporte-desembolsos-auxiliar {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>


