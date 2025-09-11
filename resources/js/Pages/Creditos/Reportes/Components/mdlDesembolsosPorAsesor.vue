<template>
	<div id="mdlDesembolsosPorAsesor" class="modal">
		<div class="modal-content mdlDesembolsosPorAsesor">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="titulo_modal"
						:nombre_modal="'mdlDesembolsosPorAsesor'"
					>
					</headerCloseModal>

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
											style="font-size: 15px"
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
											style="font-size: 15px"
										/>
									</div>
								</div>
							</fieldset>

							<div class="col-md-1 ml-3">
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
						</div>
						<div class="form-row col-md-6" v-if="modo == 'completo'">
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									id="chbPorUsuario"
									v-model="filtro_usuario_check"
								/>
								<label class="label-title" for="chbPorUsuario"
									>Filtrar por ASESOR</label
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
						<div class="card-title mb-1">LISTA DE RESULTADOS</div>

						<fieldset class="form-group col-md-12">
							<legend>
								<label class="label-title">FILTRAR RESULTADOS</label>
							</legend>
							<div class="row col-md-12">
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<label class="input-group-text prepend-title">
											NÚMERO
										</label>
									</div>

									<select
										class="form-control center"
										v-model="filtros_tabla['numero_credito'].value"
										:disabled="lista_desembolsos.length == 0"
									>
										<option :value="null" selected>TODOS</option>
										<option value="NUEVO">NUEVO NRO 1</option>
										<option value="RECURRENTE">RECURRENTE</option>
										<option value="CONSIDERADO_1">CONSIDERADO 1</option>
									</select>
								</div>
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<label class="input-group-text prepend-title"> TIPO </label>
									</div>
									<div class="input-group-prepend"></div>
									<select
										class="form-control center"
										v-model="filtros_tabla['tipo_credito'].value"
										:disabled="lista_desembolsos.length == 0"
									>
										<option :value="null" selected>TODOS</option>
										<option
											v-for="(item, index) in tipos_filtrados"
											:key="index"
											:value="item.tipo"
										>
											{{ item.tipo }}
										</option>
									</select>
								</div>
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<label class="input-group-text prepend-title"> MODO </label>
									</div>
									<div class="input-group-prepend"></div>
									<select
										class="form-control center"
										v-model="filtros_tabla['modo_desembolso'].value"
										:disabled="lista_desembolsos.length == 0"
									>
										<option :value="null" selected>TODOS</option>
										<option value="OFICINA">OFICINA</option>
										<option value="DOMICILIO">DOMICILIO</option>
									</select>
								</div>
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">ESPECIAL</span>
									</div>
									<select
										class="form-control center"
										:disabled="lista_desembolsos.length == 0"
										v-model="filtros_tabla['es_especial'].value"
									>
										>
										<option :value="null" selected>TODOS</option>
										<option :value="1">SI</option>
										<option :value="0">NO</option>
									</select>
								</div>
							</div>
						</fieldset>

						<DataTable
							:value="lista_desembolsos_filtrado"
							:scrollable="true"
							scrollDirection="both"
							:scrollHeight="String(windowHeigth * 0.35) + 'px'"
							:paginator="true"
							:rows="100"
							selectionMode="single"
							paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
							currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
							showGridlines
						>
							<Column
								field="fecha_desembolso"
								header="FECHA"
								:styles="{ width: '140px', justifyContent: 'center' }"
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
								header="NÚMERO"
								:styles="{ width: '50px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="considerado_uno"
								header="CONSIDERADO_1"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="usuario_asesor"
								header="ASESOR"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="cliente"
								header="CLIENTE"
								:styles="{ width: '300px' }"
							>
							</Column>

							<Column
								field="capital"
								header="CAPITAL"
								:styles="{ width: '105px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.capital, 2) }}
								</template>
							</Column>
							<Column
								field="plazo"
								header="PLAZO"
								:styles="{ width: '110px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="tasa_interes"
								header="TASA"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ roundTo(data.tasa_interes, 2) }} %
								</template>
							</Column>
							<Column
								field="interes_total"
								header="INTERÉS"
								:styles="{ width: '100px', justifyContent: 'flex-end' }"
							>
								<template #body="{ data }">
									S/ {{ roundTo(data.interes_total, 2) }}
								</template>
							</Column>
							<Column
								field="tipo"
								header="TIPO"
								:styles="{ width: '140px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="modo_desembolso"
								header="MODO"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="es_especial"
								header="ESPECIAL"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.es_especial == 1 ? "SI" : "NO" }}
								</template>
							</Column>
							<Column
								field="usuario_aprobacion"
								header="APROBACIÓN"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="usuario_desembolso"
								header="CAJA"
								:styles="{ width: '80px', justifyContent: 'center' }"
							>
							</Column>

							<ColumnGroup type="footer">
								<Row>
									<Column
										:colspan="6"
										footer="(*)TOTALES:"
										:footerStyle="{
											width: '769px',
											fontSize: '13px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_capital, 2)
										"
										:footerStyle="{
											fontSize: '13px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="2"
										:footer="null"
										:footerStyle="{
											backgroundColor: 'transparent !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="
											Object.keys(totales).length === 0
												? '-'
												: 'S/ ' + roundTo(totales.total_interes, 2)
										"
										:footerStyle="{
											fontSize: '13px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="5"
										:footer="null"
										:footerStyle="{
											backgroundColor: 'transparent !important',
											textAlign: 'right',
										}"
									/>
								</Row>
							</ColumnGroup>
							<template #empty> No hay desembolsos encontrados.</template>
						</DataTable>

						<label class="label-title"
							>* No incluye créditos REPROGRAMADOS Y REFINANCIADOS</label
						>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar"
								:disabled="lista_desembolsos_filtrado.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-file-excel"></i>
								</span>
								<span class="text">EXPORTAR</span>
							</button>
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
	</div>
</template>


<script>
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import FiltroUsuario from "@/Pages/Creditos/Components/filtro_usuario.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import Row from "primevue/row/row.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import { FilterMatchMode } from "primevue/api";

export default {
	components: {
		headerClose,
		headerCloseModal,
		DataTable,
		Column,
		Row,
		ColumnGroup,
		FiltroUsuario,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			titulo_modal: null,

			modo: null,
			usuarios: [],
			tipos: [],

			agencias_permitidas: [],
			agencia_seleccionada: 0,

			fecha_desde: null,
			fecha_hasta: null,

			mostrar_habilitados: true,
			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			tipos_filtrados: [],

			lista_desembolsos: [],

			filtro_usuario_check: false,
			filtro_usuario_check_personal: false,

			filtros_tabla: {
				numero_credito: { value: null, matchMode: "contains" },
				tipo_credito: { value: null, matchMode: "contains" },
				modo_desembolso: { value: null, matchMode: "contains" },
				es_especial: { value: null, matchMode: "contains" },
			},
		};
	},

	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
		});
	},
	computed: {
		lista_desembolsos_filtrado() {
			const filtro_numero_credito = this.filtros_tabla["numero_credito"].value;
			const filtro_tipo_credito = this.filtros_tabla["tipo_credito"].value;
			const filtro_modo_desembolso =
				this.filtros_tabla["modo_desembolso"].value;

			const filtro_es_especial = this.filtros_tabla["es_especial"].value;

			return this.lista_desembolsos.filter((item) => {
				// Filtro por número de crédito
				if (filtro_numero_credito) {
					if (
						(filtro_numero_credito === "NUEVO" && item.numero_credito !== 1) ||
						(filtro_numero_credito === "RECURRENTE" &&
							item.numero_credito <= 1) ||
						(filtro_numero_credito === "CONSIDERADO_1" &&
							item.considerado_uno !== "SI")
					) {
						return false;
					}
				}

				if (filtro_tipo_credito) {
					if (item.tipo !== filtro_tipo_credito) {
						return false;
					}
				}

				if (filtro_modo_desembolso) {
					if (item.modo_desembolso !== filtro_modo_desembolso) {
						return false;
					}
				}
				if (filtro_es_especial !== null && filtro_es_especial !== undefined) {
					if (item.es_especial !== filtro_es_especial) {
						return false;
					}
				}

				return true;
			});
		},

		totales() {
			let total_capital = 0;
			let total_interes = 0;

			if (this.lista_desembolsos_filtrado.length > 0) {
				total_capital = this.lista_desembolsos_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.capital);
					},
					0
				);

				total_interes = this.lista_desembolsos_filtrado.reduce(
					(total, item) => {
						return parseFloat(total) + parseFloat(item.interes_total);
					},
					0
				);
			}

			return {
				total_capital: total_capital,
				total_interes: total_interes,
			};
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

		agencia_seleccionada() {
			this.FechaActual();
			this.FiltrarTipos();
			this.FiltrarUsuarios();
			this.filtro_usuario_check = false;
		},

		filtro_usuario_check() {
			const self = this;

			this.usuarios_seleccionados = [];

			if (this.filtro_usuario_check) {
				let filtro_usuarios = this.$refs.filtro_usuario;
				async function EnviarDatos() {
					filtro_usuarios.usuarios = self.usuarios_filtrados;
					filtro_usuarios.mostrar_habilitados = true;
					filtro_usuarios.modo_agencias = false;

					filtro_usuarios.agencias_permitidas = self.agencias_permitidas;
					filtro_usuarios.agencia_seleccionada = self.agencia_seleccionada;
				}

				EnviarDatos().then(() => {
					$("#filtro_usuario").css("display", "block");

					filtro_usuarios.FiltrarUsuarios();
				});
			}
		},
	},

	methods: {
		async ListarRecursos() {
			let self = this;

			//   this.$inertia.get(route("rep.cre.desembolsos_asesor", "completo"));
			//   return false;

			await axios
				.get(route("rep.cre.desembolsos_asesor", this.modo))
				.then(function (response) {
					self.usuarios = response.data.usuarios;
					self.modo = response.data.modo;
					self.tipos = response.data.tipos;
				});

			if (this.agencia_seleccionada != null) {
				this.FiltrarTipos();
				this.FiltrarUsuarios();
			}

			if (this.modo == "personal") {
				this.usuarios_seleccionados = [];
				this.usuarios_seleccionados.push(
					this.$page.props.user_session.usuario_dni
				);

				this.filtro_usuario_check_personal = true;
			}
		},
		VerUsuarios() {
			const self = this;
			let filtro_usuarios = this.$refs.filtro_usuario;
			async function EnviarDatos() {
				filtro_usuarios.usuarios = self.usuarios_filtrados;
				filtro_usuarios.agencia_seleccionada = self.agencia_seleccionada;
				filtro_usuarios.mostrar_habilitados = self.mostrar_habilitados;
			}

			EnviarDatos().then(() => {
				$("#filtro_usuario").css("display", "block");
				filtro_usuarios.usuarios_seleccionados = self.usuarios_seleccionados;
			});
		},
		RecibirUsuarios(usuarios) {
			this.usuarios_seleccionados = usuarios;
			this.filtro_usuario_check = true;
		},
		RecibirAgencia(agencia) {
			this.agencia_seleccionada = agencia;
		},
		RecibirHabilitado(habilitado) {
			this.mostrar_habilitados = habilitado;
		},

		async FechaActual() {
			if (this.agencia_seleccionada == null) {
				return false;
			} else {
				let fecha_actual = await this.$parent.fecha_hora_actual(
					this.agencia_seleccionada
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		async FiltrarTipos() {
			this.tipos_filtrados = [];
			this.tipos_filtrados = await this.tipos.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
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
			this.usuarios_filtrados = [];
			this.usuarios_filtrados = this.usuarios.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
		},

		async Buscar() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("modo", "por_asesor");

			if (this.modo == "completo") {
				data.append("filtro_usuario", this.filtro_usuario_check);
				if (this.filtro_usuario_check) {
					data.append("usuarios", JSON.stringify(this.usuarios_seleccionados));
				}
			}
			if (this.modo == "personal") {
				data.append("filtro_usuario", this.filtro_usuario_check_personal);
				data.append("usuarios", JSON.stringify(this.usuarios_seleccionados));
			}

			await Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					//   this.$inertia.post(route("rep.cre.desembolsos_asesor.buscar"), data);
					//   return false;

					Swal.showLoading();
					await axios
						.post(route("rep.cre.desembolsos_asesor.buscar"), data)
						.then((response) => {
							if (response.data.lista_desembolsos.length == 0) {
								this.lista_desembolsos = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								this.lista_desembolsos = response.data.lista_desembolsos;

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
		Exportar(modo) {
			let data = new FormData();
			data.append("modo", "por_asesor");
			data.append("tipo", this.modo);

			data.append(
				"lista_desembolsos",
				JSON.stringify(this.lista_desembolsos_filtrado)
			);
			data.append("totales", JSON.stringify(this.totales));

			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("agencia_id", this.agencia_seleccionada);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					//   this.$inertia.post(
					//     route("rep.cre.desembolsos_asesor.exportar"),
					//     data
					//   );
					//   return false;

					Swal.showLoading();

					await axios
						.post(route("rep.cre.desembolsos_asesor.exportar"), data)
						.then(function (response) {
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
						});
				},
			});
		},
		CerrarModal() {
			if (this.usuarios_seleccionados.length == 0) {
				this.filtro_usuario_check = false;
			}
		},
	},
};
</script>

<style lang="css">
.mdlDesembolsosPorAsesor {
	width: 70% !important;
	margin-left: 15% !important;
}
.subrayado {
	color: blue !important;
	text-decoration: underline !important;
	cursor: pointer !important;
}

@media only screen and (max-width: 900px) {
	.mdlDesembolsosPorAsesor {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


