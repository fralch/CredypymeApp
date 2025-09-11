<template>
	<layout ref="layout">
		<div class="slot_body slot-creditos-dias-mora" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							'LISTADO DE ' +
							(modo == 'completo'
								? 'CRÉDITOS POR DÍAS DE MORA'
								: 'MIS CRÉDITOS POR DÍAS DE MORA')
						"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<fieldset class="form-group col-md-5">
								<legend>
									<label class="label-title">DÍAS DE ATRASO</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text">DESDE</span>
										</div>
										<input
											type="number"
											class="form-control input-information center bolder"
											min="0"
											step="1"
											v-model="desde_dias"
											name="desde_dias"
											@change="Redondear"
											style="font-size: 18px; color: black"
										/>
										<div class="input-group-append">
											<span class="input-group-text" min="0" step="1">d</span>
										</div>
									</div>
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text">HASTA</span>
										</div>
										<input
											type="number"
											class="form-control input-information center bolder"
											min="0"
											step="1"
											v-model="hasta_dias"
											name="hasta_dias"
											@change="Redondear"
											style="font-size: 18px; color: black"
										/>
										<div class="input-group-append">
											<span class="input-group-text">d</span>
										</div>
									</div>
								</div>
							</fieldset>
							<div class="form-row col-md-6 ml-3">
								<div class="form-group col-md-5 mt-2 center">
									<select
										class="form-control"
										v-model="agencia_seleccionada"
										:disabled="agencias_permitidas.length == 0"
									>
										<option
											v-for="(item, index) in agencias_permitidas"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
									<div class="input-group mt-1" v-if="modo == 'completo'">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="checkbox"
													:value="true"
													id="chbPorAsesor"
													v-model="por_asesor"
												/>
											</div>
										</div>
										<div class="input-group-append">
											<label class="input-group-text" for="chbPorAsesor">
												POR ASESOR
											</label>
										</div>
									</div>
									<select
										class="form-control mt-1"
										v-model="asesor_seleccionado"
										v-if="por_asesor || modo == 'personal'"
									>
										<option
											v-for="(item, index) in asesores_agencia"
											:key="index"
											:value="item.dni"
										>
											{{ item.usuario }}
										</option>
									</select>
								</div>
								<div class="col-md-1 ml-3">
									<button
										class="btn btn-action btn-icon-split mt-3"
										title="Buscar"
										@click="ListarCreditos"
									>
										<span class="icon text-white" style="font-size: 25px">
											<i class="fas fa-search"></i>
										</span>
									</button>
								</div>
								<div class="form-group col-md-4">
									<label class="label-title mt-3">Ordenado por:</label>
									<select class="form-control" v-model="tipo_orden">
										<option value="dias_atraso">Días de atraso</option>
										<option value="fecha_desembolso">
											Fecha de desembolso
										</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div
								class="col-md-9"
								style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)"
							>
								<div class="form-row p-1">
									<div class="input-group col-md-8">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="radio"
													name="tipo_filtro"
													id="rdbPorNombre"
													value="por_nombre"
													v-model="tipo_filtro"
												/>
											</div>
										</div>
										<div class="input-group-prepend">
											<label class="input-group-text" for="rdbPorNombre">
												Por nombre
											</label>
										</div>
										<input
											type="text"
											class="form-control mayus"
											autocomplete="off"
											spellcheck="false"
											@focus="hidenav()"
											@blur="shownav()"
											:disabled="
												tipo_filtro != 'por_nombre' ||
												lista_creditos_filtrados.length == 0
											"
											v-model="filtros_tabla['cliente'].value"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="radio"
													name="tipo_filtro"
													id="rdbPorNumero"
													value="por_numero"
													v-model="tipo_filtro"
												/>
											</div>
										</div>
										<div class="input-group-prepend">
											<label class="input-group-text" for="rdbPorNumero">
												Por N° de crédito
											</label>
										</div>
										<input
											type="number"
											class="form-control center"
											min="1"
											step="1"
											value="1"
											@change="FiltrarCreditos"
											id="inpNumeroCredito"
											name="numero_credito"
											style="font-size: 13px"
											:disabled="
												tipo_filtro != 'por_numero' ||
												lista_creditos_filtrados.length == 0
											"
										/>
									</div>
								</div>

								<DataTable
									:value="lista_creditos_filtrados"
									:row-class="rowClass"
									:filters="filtros_tabla"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="380px"
									:paginator="true"
									:rows="100"
									selectionMode="single"
									@row-dblclick="DetalleCredito"
									paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
								>
									<Column
										field="numero"
										header="N°"
										:styles="{ width: '40px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ data.index + 1 }}
										</template>
									</Column>
									<Column
										field="expediente"
										header="EXP."
										:styles="{ width: '60px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="cliente"
										header="CLIENTE"
										:styles="{ width: '280px' }"
									>
									</Column>
									<Column
										field="dias_atraso"
										header="ATRASO"
										:styles="{ width: '60px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="capital"
										header="CAPITAL"
										:styles="{ width: '90px', justifyContent: 'flex-end' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.capital_total, 2, "money") }}
										</template>
									</Column>
									<Column
										field="plazo"
										header="PLAZO"
										:styles="{ width: '100px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="fecha_desembolso"
										header="FECHA_DESEMBOLSO"
										:styles="{ width: '130px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="modo_desembolso"
										header="MODO_DESEMBOLSO"
										:styles="{ width: '130px', justifyContent: 'center' }"
									>
									</Column>

									<Column
										field="cuota"
										header="MONTO_CUOTA"
										:styles="{ width: '100px', justifyContent: 'flex-end' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.cuota, 2, "money") }}
										</template>
									</Column>
									<Column
										field="usuario_asesor"
										header="ASESOR"
										:styles="{ width: '150px', justifyContent: 'center' }"
									>
									</Column>
									<Column
										field="saldo_capital"
										header="SALDO_CAPITAL"
										:styles="{ width: '100px', justifyContent: 'flex-end' }"
									>
										<template #body="{ data }">
											S/ {{ roundTo(data.saldo_capital, 2, "money") }}
										</template>
									</Column>
								</DataTable>
							</div>

							<div class="col-md-3" style="margin-top: 50px">
								<fieldset class="ml-2" v-if="lista_creditos.length > 0">
									<legend>
										<label class="label-title">FILTRAR POR TIPO</label>
									</legend>

									<div class="text-center" v-if="tipo_filtro != null">
										<button
											type="button"
											class="btn btn-action"
											title="Resetear"
											@click="tipo_filtro = null"
										>
											<span class="text">RESETEAR</span>
										</button>
										<hr />
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="radio"
													id="rdbRefinanciado"
													value="refinanciado"
													name="tipo_filtro"
													v-model="tipo_filtro"
												/>
											</div>
										</div>
										<div class="input-group-prepend">
											<label
												for="rdbRefinanciado"
												style="
													width: 20px !important;
													background: var(--red);
													margin: 0 !important;
												"
											></label>
										</div>
										<div class="input-group-append">
											<label
												class="input-group-text"
												for="rdbRefinanciado"
												style="background: white !important"
											>
												Refinanciado
											</label>
										</div>
									</div>
									<div class="input-group mt-1">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="radio"
													id="rdbReprogramado"
													value="reprogramado"
													name="tipo_filtro"
													v-model="tipo_filtro"
												/>
											</div>
										</div>
										<div
											class="input-group-prepend"
											style="width: 20px !important; background: var(--green)"
										></div>
										<div class="input-group-append">
											<label
												class="input-group-text"
												for="rdbReprogramado"
												style="background: white !important"
											>
												Reprogramado
											</label>
										</div>
									</div>
									<div class="input-group mt-1">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="radio"
													id="rdbVencidos"
													value="vencido"
													name="tipo_filtro"
													v-model="tipo_filtro"
												/>
											</div>
										</div>
										<div
											class="input-group-prepend"
											style="width: 20px !important; background: var(--cyan)"
										></div>
										<div class="input-group-append">
											<label
												class="input-group-text"
												for="rdbVencidos"
												style="background: white !important"
											>
												Vencidos
											</label>
										</div>
									</div>
									<div class="input-group mt-1">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="radio"
													id="rdbDiasFaltantes"
													value="dias_faltantes"
													name="tipo_filtro"
													v-model="tipo_filtro"
												/>
											</div>
										</div>
										<div
											class="input-group-prepend"
											style="width: 20px !important; background: var(--pink)"
										></div>
										<div class="input-group-append">
											<label
												class="input-group-text"
												for="rdbDiasFaltantes"
												style="background: white !important"
											>
												Días faltantes
											</label>
										</div>
									</div>

									<div
										class="input-group mt-1"
										v-if="tipo_filtro == 'dias_faltantes'"
									>
										<div class="input-group-prepend">
											<input
												type="number"
												class="form-control center"
												style="width: 70px"
												min="0"
												step="1"
												name="dias_faltantes"
												v-model.number="cantidad_faltante"
											/>
										</div>

										<div class="input-group-append">
											<select
												class="form-control input-group-text"
												v-model="medicion_faltante"
											>
												<option value="dias">Día(s)</option>
												<option value="semanas">Semana(s)</option>
												<option value="meses">Mese(s)</option>
											</select>
										</div>
									</div>
								</fieldset>
								<div class="form-row mt-2">
									<div class="col-md-12 text-center">
										<div class="btn-group" role="group">
											<div class="btn-group">
												<button
													type="button"
													class="btn btn-action dropdown-toggle"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false"
													title="Imprimir"
													:disabled="lista_creditos.length == 0"
												>
													<span class="text">IMPRIMIR</span>
												</button>
												<div class="dropdown-menu">
													<a
														class="dropdown-item"
														href="#"
														@click.prevent="Exportar('sin_telefonos', 'PDF')"
														>Sin Teléfonos</a
													>
													<a
														class="dropdown-item"
														href="#"
														@click.prevent="Exportar('con_telefonos', 'PDF')"
														>Con Teléfonos</a
													>
												</div>
											</div>

											<div class="btn-group">
												<button
													type="button"
													class="btn btn-cancel dropdown-toggle"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false"
													title="Exportar"
													:disabled="lista_creditos.length == 0"
												>
													<span class="text">EXPORTAR</span>
												</button>
												<div class="dropdown-menu">
													<a
														class="dropdown-item"
														href="#"
														@click.prevent="Exportar('sin_telefonos', 'XLSX')"
														>Sin Teléfonos</a
													>
													<a
														class="dropdown-item"
														href="#"
														@click.prevent="Exportar('con_telefonos', 'XLSX')"
														>Con Teléfonos</a
													>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<mdlDetalleCredito
					ref="mdlDetalleCredito"
					:usuarios_agencia="usuarios_agencia"
					:agencia_id="agencia_seleccionada"
				></mdlDetalleCredito>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import mdlDetalleCredito from "@/Pages/Creditos/Creditos/Components/mdlDetalleCredito.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import { FilterMatchMode } from "primevue/api";

export default {
	components: {
		layout,
		headerClose,
		mdlDetalleCredito,

		DataTable,
		Column,
	},
	props: {
		modo: String,
		asesores: Array,
		usuarios: Array,
	},

	data() {
		return {
			agencias_permitidas: [],
			asesores_agencia: [],

			agencia_seleccionada: 0,
			asesor_seleccionado: 0,

			desde_dias: 0,
			hasta_dias: 10,
			por_asesor: 0,
			tipo_orden: "dias_atraso",

			lista_creditos: [],
			lista_creditos_filtrados: [],
			filtros_tabla: {},

			tipo_filtro: null,
			cantidad_faltante: 1,
			medicion_faltante: "dias",
		};
	},
	computed: {
		usuarios_agencia() {
			return this.usuarios.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
		},
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
		agencia_seleccionada(value) {
			this.asesores_agencia = this.asesores.filter(
				(item) => item.agencia_id == value
			);
		},
		asesores_agencia(value) {
			if (this.modo == "personal") {
				this.asesor_seleccionado = this.$page.props.user_session.usuario_dni;
			} else if (this.modo == "completo") {
				this.asesor_seleccionado = value[0].dni;
			}
		},

		tipo_filtro(value) {
			let self = this;
			$("#inpBusquedaDiasMora").val("");
			$("#inpNumeroCredito").val(1);

			if (value == "por_nombre" || value == "por_numero") {
				this.lista_creditos_filtrados = this.lista_creditos;
			} else if (value == "refinanciado") {
				this.lista_creditos_filtrados = this.lista_creditos.filter(
					(item) => item.tipo == "REFINANCIADO"
				);
			} else if (value == "reprogramado") {
				this.lista_creditos_filtrados = this.lista_creditos.filter(
					(item) => item.tipo == "REPROGRAMADO"
				);
			} else if (value == "vencido") {
				axios
					.post(route("fecha_hora_agencia", self.agencia_seleccionada))
					.then(function (response) {
						let fecha_sistema_hoy = response.data;

						self.lista_creditos_filtrados = self.lista_creditos.filter(
							(item) => item.fecha_vencimiento < fecha_sistema_hoy
						);
					});
			} else if (value == "dias_faltantes") {
				axios
					.post(route("fecha_hora_agencia", self.agencia_seleccionada))
					.then(function (response) {
						let fecha_sistema_hoy = response.data;

						if (self.medicion_faltante == "dias") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"days"
									) == self.cantidad_faltante
							);
						} else if (self.medicion_faltante == "semanas") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"weeks"
									) == self.cantidad_faltante
							);
						} else if (self.medicion_faltante == "meses") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"months"
									) == self.cantidad_faltante
							);
						}
					});
			} else if (value == null) {
				this.lista_creditos_filtrados = this.lista_creditos;
			}
		},
		cantidad_faltante(value) {
			let self = this;
			if (this.tipo_filtro == "dias_faltantes") {
				return axios
					.post(route("fecha_hora_agencia", self.agencia_seleccionada))
					.then(function (response) {
						let fecha_sistema_hoy = response.data;

						if (self.medicion_faltante == "dias") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"days"
									) +
										1 ==
									value
							);
						} else if (self.medicion_faltante == "semanas") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"days"
									) +
										1 ==
									value * 7
							);
						} else if (self.medicion_faltante == "meses") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"days"
									) +
										1 ==
									value * 30
							);
						}
					});
			} else {
				return false;
			}
		},
		medicion_faltante(value) {
			let self = this;
			if (this.tipo_filtro == "dias_faltantes") {
				return axios
					.post(route("fecha_hora_agencia", self.agencia_seleccionada))
					.then(function (response) {
						let fecha_sistema_hoy = response.data;

						if (value == "dias") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"days"
									) == self.cantidad_faltante
							);
						} else if (value == "semanas") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"weeks"
									) == self.cantidad_faltante
							);
						} else if (value == "meses") {
							self.lista_creditos_filtrados = self.lista_creditos.filter(
								(item) =>
									moment(item.fecha_vencimiento).diff(
										moment(fecha_sistema_hoy),
										"months"
									) == self.cantidad_faltante
							);
						}
					});
			} else {
				return false;
			}
		},
	},
	created() {
		this.filtros_tabla = {
			cliente: { value: null, matchMode: FilterMatchMode.CONTAINS },
		};
	},
	mounted() {
		this.listar_agencias();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		rowClass(data) {
			let index = this.lista_creditos_filtrados.findIndex(
				(item) => item.id == data.id
			);

			return data.tipo == "REFINANCIADO"
				? "refinanciado"
				: data.tipo == "REPROGRAMADO"
				? "reprogramado"
				: index % 2 == 0
				? "verde-claro"
				: "";
		},

		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MIS_DIAS_MORA"
				);
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_DIAS_MORA"
				);
			}
		},
		roundTo(value, decimal_places, type) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			let resultado = null;

			if (type == "number") {
				resultado = parseFloat(valor).toFixed(numero_decimales);
			} else if (type == "money") {
				resultado = parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
			return resultado;
		},
		Redondear(e) {
			let valor = 0;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			if (e.target.name == "desde_dias") {
				this.desde_dias = this.roundTo(valor, 0, "number");
			} else if (e.target.name == "hasta_dias") {
				this.hasta_dias = this.roundTo(valor, 0, "number");
			}
		},
		periodo_medicion(value) {
			if (value == "DIARIO") {
				return "(DÍAS)";
			} else if (value == "SEMANAL") {
				return "(SEMANAS)";
			} else if (value == "QUINCENAL") {
				return "(QUINCENAS)";
			} else if (value == "MENSUAL") {
				return "(MESES)";
			}
			return "(DÍAS)";
		},

		ListarCreditos() {
			let self = this;
			let data = new FormData();
			data.append("desde_dias", this.desde_dias);
			data.append("hasta_dias", this.hasta_dias);
			data.append("agencia_id", this.agencia_seleccionada);

			if (this.modo == "completo" && !this.por_asesor) {
				data.append("asesor_id", 0);
			} else {
				data.append("asesor_id", this.asesor_seleccionado);
			}

			data.append("tipo_orden", this.tipo_orden);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					// this.$inertia.post(route("rep.cre.dias_mora.listar"), data);
					// return false;

					Swal.showLoading();
					axios
						.post(route("rep.cre.dias_mora.listar"), data)
						.then(function (response) {
							if (response.data.lista_creditos.length == 0) {
								self.lista_creditos = [];
								self.lista_creditos_filtrados = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_creditos = response.data.lista_creditos;
								self.lista_creditos_filtrados = self.lista_creditos;

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
		FiltrarCreditos(e) {
			let numero_credito = e.target.value;

			this.lista_creditos_filtrados = this.lista_creditos.filter(
				(item) => item.numero_credito == numero_credito
			);
		},
		async DetalleCredito(e) {
			let self = this;

			let credito_id = e.data.id;

			//   this.$inertia.post(
			//     route("rep.cre.dias_mora.detalle", {
			//       credito_id: credito_id,
			//       agencia_id: self.agencia_seleccionada,
			//     })
			//   );
			//   return false;

			await axios
				.post(
					route("rep.cre.dias_mora.detalle", {
						credito_id: credito_id,
						agencia_id: self.agencia_seleccionada,
					})
				)
				.then(function (response) {
					let mdlDetalleCredito = self.$refs.mdlDetalleCredito;
					async function EnviarDatos() {
						mdlDetalleCredito.credito_id = credito_id;
						mdlDetalleCredito.datos_credito = response.data.datos_credito;
						mdlDetalleCredito.datos_cuotas = response.data.datos_cuotas;
						mdlDetalleCredito.notificaciones = response.data.notificaciones;
						mdlDetalleCredito.notificaciones_tipos =
							response.data.notificaciones_tipos;
						mdlDetalleCredito.compromisos = response.data.compromisos;
						mdlDetalleCredito.datos_titular = response.data.datos_titular;
						mdlDetalleCredito.pariente = response.data.pariente;
						mdlDetalleCredito.datos_pariente = response.data.datos_pariente;
						mdlDetalleCredito.aval = response.data.aval;
						mdlDetalleCredito.datos_aval = response.data.datos_aval;
						mdlDetalleCredito.datos_negocio = response.data.datos_negocio;

						mdlDetalleCredito.Resetear();
					}
					EnviarDatos().then(() => {
						$("#mdlDetalleCredito").css("display", "block");
						$("#cuotas-tab").tab("show");
					});
				});
		},
		async Exportar(modo, tipo) {
			let data = new FormData();
			data.append(
				"creditos_filtrados",
				JSON.stringify(this.lista_creditos_filtrados)
			);
			data.append("modo", modo);
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
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("rep.cre.dias_mora.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								link.click();
								return Swal.fire({
									icon: "success",
									title: "¡EXPORTADO!",
									timer: 2000,
									showConfirmButton: false,
								});
							} else if (tipo == "PDF") {
								const origin = window.location.origin;
								const path_pdf = response.data.path_pdf;

								const iframe = document.createElement("iframe");
								iframe.style.display = "none";
								iframe.src = origin + path_pdf;
								document.body.appendChild(iframe);

								iframe.contentWindow.focus();
								iframe.contentWindow.print();
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
.slot-creditos-dias-mora {
	width: 80% !important;
	margin-left: 10% !important;
}

.refinanciado {
	background: var(--red) !important;
}

.refinanciado td {
	color: white !important;
}

.reprogramado {
	background: var(--green) !important;
}

.reprogramado td {
	color: white !important;
}
@media (max-width: 900px) {
	.slot-creditos-dias-mora {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
