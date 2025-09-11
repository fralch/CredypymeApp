<template>
	<layout ref="layout">
		<div class="slot_body slot-indicador-objetivo" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'INDICADOR DE OBJETIVO'"></headerClose>
					<div class="card-title">OBJETIVO MENSUAL</div>
					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<fieldset class="form-group col-md-10">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
								</legend>
								<div class="form-row">
									<div class="input-group col-md-5">
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
												:style="'font-size: 13px !important'"
											>
												{{ item.agencia }}
											</option>
										</select>
									</div>
									<div class="input-group col-md-4 col-7">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">ASESOR</span>
										</div>
										<select
											class="form-control center"
											v-model="asesor_seleccionado"
										>
											<option :value="0" selected>Seleccione...</option>
											<option
												v-for="(item, index) in asesores_filtrados"
												:key="index"
												:value="item.dni"
											>
												{{ item.usuario }}
											</option>
										</select>
									</div>
									<div class="input-group col-md-3 col-5">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HIST.</span>
										</div>
										<select
											class="form-control center"
											v-model="meses_historial"
										>
											<option :value="3" selected>3 meses</option>
											<option :value="6">6 meses</option>
											<option :value="12">12 meses</option>
										</select>
									</div>

									<div class="col-md-1 text-center" v-if="windowWidth < 900">
										<button
											class="btn btn-action btn-icon-split mt-3"
											title="Procesar"
											@click="Procesar"
										>
											<span class="icon text-white" style="font-size: 15px">
												<i class="fas fa-chart-line"></i>
											</span>
										</button>
									</div>
								</div>
							</fieldset>
							<div class="col-md-1">
								<button
									class="btn btn-action btn-icon-split mt-3"
									title="Procesar"
									@click="Procesar"
									v-if="windowWidth >= 900"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-chart-line"></i>
									</span>
								</button>
							</div>
						</div>
						<hr />
						<div class="card-title">RESULTADOS</div>
						<div v-if="this.categoria_actual != null">
							<div
								id="indicator-0"
								:style="{
									marginLeft: indicador_0_posicion + '%',
								}"
							>
								<p class="legend-1">{{ porcentaje_avance }} %</p>
								<!-- <i class="pi pi-chevron-down"></i> -->
								<p class="line-mark">|</p>
							</div>
							<div
								id="indicator-1"
								:style="{
									marginLeft: indicador_1_posicion + '%',
									color: categoria_color,
								}"
							>
								<!-- <p class="legend-1">S/ 50.00</p> -->
								<!-- <p class="legend-1">
									{{ this.subcategoria_actual[0].peso_maximo }} %
								</p> -->
								<p class="line">|</p>
							</div>
							<div
								id="indicator-2"
								:class="'categoria_' + categoria_nivel"
								:style="{
									marginLeft: indicador_2_posicion + '%',
									color: categoria_color,
								}"
							>
								<!-- <p class="legend-1">S/ 75.00</p> -->
								<!-- <p class="legend-1">
									{{ this.subcategoria_actual[1].peso_maximo }} %
								</p> -->
								<p class="line">|</p>
							</div>
							<div class="contenedor-responsive">
								<div
									class="progress"
									style="margin-top: 60px; margin-bottom: 30px"
								>
									<div
										class="progress-bar"
										role="progressbar"
										style="width: 10%"
									>
										<div class="container-limits">
											<div class="left-text">0</div>
											<div class="right-text">67</div>
										</div>
									</div>

									<div
										class="progress-bar"
										:class="{
											'progress-bar-striped progress-bar-animated categoria-1':
												categoria_nivel == 1,
										}"
										role="progressbar"
										style="width: 15%"
									>
										<div class="container-limits">
											<div class="left-text">
												{{ parseInt(this.categorias[0].minimo) }}
											</div>
											<div class="right-text">
												{{ parseInt(this.categorias[0].maximo) }}
											</div>
										</div>
									</div>
									<div
										class="progress-bar"
										:class="{
											'progress-bar-striped progress-bar-animated categoria-2':
												categoria_nivel == 2,
										}"
										role="progressbar"
										style="width: 15%"
									>
										<div class="container-limits">
											<div class="left-text">
												{{ parseInt(this.categorias[1].minimo) }}
											</div>
											<div class="right-text">
												{{ parseInt(this.categorias[1].maximo) }}
											</div>
										</div>
									</div>
									<div
										class="progress-bar"
										:class="{
											'progress-bar-striped progress-bar-animated categoria-3':
												categoria_nivel == 3,
										}"
										role="progressbar"
										style="width: 15%"
									>
										<div class="container-limits">
											<div class="left-text">
												{{ parseInt(this.categorias[2].minimo) }}
											</div>
											<div class="right-text">
												{{ parseInt(this.categorias[2].maximo) }}
											</div>
										</div>
									</div>
									<div
										class="progress-bar"
										:class="{
											'progress-bar-striped progress-bar-animated categoria-4':
												categoria_nivel == 4,
										}"
										role="progressbar"
										style="width: 15%"
									>
										<div class="container-limits">
											<div class="left-text">
												{{ parseInt(this.categorias[3].minimo) }}
											</div>
											<div class="right-text">
												{{ parseInt(this.categorias[3].maximo) }}
											</div>
										</div>
									</div>
									<div
										class="progress-bar"
										:class="{
											'progress-bar-striped progress-bar-animated categoria-5':
												categoria_nivel == 5,
										}"
										role="progressbar"
										style="width: 15%"
									>
										<div class="container-limits">
											<div class="left-text">
												{{ parseInt(this.categorias[4].minimo) }}
											</div>
											<div class="right-text">
												{{ parseInt(this.categorias[4].maximo) }}
											</div>
										</div>
									</div>
									<div
										class="progress-bar"
										:class="{
											'progress-bar-striped progress-bar-animated categoria-6':
												categoria_nivel == 6,
										}"
										role="progressbar"
										style="width: 15%"
									>
										<div class="container-limits">
											<div class="left-text">
												{{ parseInt(this.categorias[5].minimo) }}
											</div>
											<div class="right-text">
												{{ parseInt(this.categorias[5].maximo) }}
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-6">
									<table class="table">
										<thead>
											<tr>
												<th colspan="3">OBJETIVOS Y BONOS</th>
											</tr>
											<tr>
												<th>DESDE</th>
												<th>HASTA</th>
												<th>BONO</th>
											</tr>
										</thead>
										<tbody>
											<tr
												class="table-bordered"
												:class="[
													porcentaje_avance >= parseFloat(item.peso_minimo) &&
													porcentaje_avance <= parseFloat(item.peso_maximo)
														? 'categoria-' + categoria_nivel
														: '',
												]"
												v-for="(item, index) in subcategoria_actual"
												:key="index"
											>
												<td align="center">
													{{ item.peso_minimo.toFixed(2) }}%
												</td>
												<td align="center">
													{{ item.peso_maximo.toFixed(2) }}%
												</td>
												<td align="center">S/ {{ item.bono }}</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-md-6">
									<DataTable
										:value="historial"
										:scrollable="true"
										scrollDirection="both"
										scrollHeight="150px"
										selectionMode="single"
									>
										<template #header>
											<div class="flex text-center">
												HISTORIAL DE {{ meses_historial }} MESES
											</div>
										</template>
										<Column
											field="fecha"
											header="FECHA"
											:styles="{
												width: '120px',
												justifyContent: 'center',
												fontWeight: 'bolder',
											}"
										>
											<template #body="{ data }">
												{{ data.fecha }}
											</template>
										</Column>
										<Column
											field="resultado"
											header="RESULTADO"
											:styles="{
												width: '120px',
												justifyContent: 'center',
												fontWeight: 'bolder',
											}"
										>
											<template #body="{ data }">
												{{ data.resultado.toFixed(2) }} %
											</template>
										</Column>
									</DataTable>
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
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	components: { layout, headerClose, DataTable, Column },
	props: { modo: String },
	data() {
		return {
			windowWidth: window.innerWidth,
			agencias_permitidas: [],
			agencia_busqueda: 0,

			asesores: [],
			asesores_filtrados: [],
			asesor_seleccionado: 0,

			categorias: [],
			categoria_actual: null,
			subcategoria_actual: null,

			interes_cobranzas: 0,
			interes_descuentos: 0,

			porcentaje_avance: 0,

			meses_historial: 3,
			historial: [],
		};
	},
	computed: {
		usuario() {
			return this.$page.props.user_session.usuario_dni;
		},
		categoria_nivel() {
			if (this.categoria_actual != null) {
				return this.categoria_actual.nivel;
			} else {
				return null;
			}
		},
		categoria_color() {
			let valor = this.categoria_nivel;
			let color = null;

			switch (valor) {
				case 1:
					color = "#b6d7a8";
					break;
				case 2:
					color = "#009e0f";
					break;
				case 3:
					color = "#ffff00";
					break;
				case 4:
					color = "#00ffff";
					break;
				case 5:
					color = "#ff9900";
					break;
				case 6:
					color = "#cf2a27";
					break;
			}

			return color;
		},
		indicador_0_posicion() {
			let valor = this.categoria_nivel;
			let ajuste = 2;

			if (this.windowWidth >= 900) {
				switch (valor) {
					case 1:
						ajuste = 1;
						break;
					case 2:
						ajuste = 0.9;
						break;
					case 3:
						ajuste = 0.6;
						break;
					case 4:
						ajuste = 0.4;
						break;
					case 5:
						ajuste = 0.25;
						break;
					case 6:
						ajuste = 0.05;
						break;
				}
			} else {
				switch (valor) {
					case 1:
						ajuste = 2.5;
						break;
					case 2:
						ajuste = 3;
						break;
					case 3:
						ajuste = 3.5;
						break;
					case 4:
						ajuste = 4;
						break;
					case 5:
						ajuste = 4.5;
						break;
					case 6:
						ajuste = 5;
						break;
				}
			}

			return this.porcentaje_avance - ajuste;
		},

		indicador_1_posicion() {
			let min = 0;
			let max = 0;

			if (this.windowWidth >= 900) {
				let ajuste = 2 * (this.categoria_nivel / 6);
				min = 9 + 14 * (this.categoria_nivel - 1) + ajuste;
				max = 23 + 14 * (this.categoria_nivel - 1) + ajuste;
			} else {
				min = 7 + 14 * (this.categoria_nivel - 1);
				max = 21 + 14 * (this.categoria_nivel - 1);
			}

			let calc = min + ((max - min) / 3) * 1;

			return calc.toFixed(2);
		},

		indicador_2_posicion() {
			let min = 0;
			let max = 0;

			if (this.windowWidth >= 900) {
				let ajuste = 2 * (this.categoria_nivel / 6);
				min = 9 + 14 * (this.categoria_nivel - 1) + ajuste;
				max = 23 + 14 * (this.categoria_nivel - 1) + ajuste;
			} else {
				min = 7 + 14 * (this.categoria_nivel - 1);
				max = 21 + 14 * (this.categoria_nivel - 1);
			}

			let calc = min + ((max - min) / 3) * 2;
			return calc.toFixed(2);
		},

		productividad_actual() {
			return this.roundTo(
				parseFloat(this.interes_cobranzas) -
					parseFloat(this.interes_descuentos),
				2
			);
		},
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
					this.agencia_filtro = value[0].id;
				} else {
					this.agencia_busqueda = null;
					this.agencia_filtro = null;
				}
			}
		},
		agencia_busqueda(value) {
			this.FiltrarAsesores();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});

		this.ListarAgenciasPermitidas();
		this.ListarRecursos();
	},

	methods: {
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(parseFloat(valor).toFixed(numero_decimales));
		},

		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_HERRAMIENTAS/MI_INDICADOR_OBJETIVO"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_HERRAMIENTAS/INDICADOR_OBJETIVO"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},

		async ListarRecursos() {
			let self = this;

			await axios
				.post(route("her.indicador_objetivo.listar_recursos"))
				.then(function (response) {
					self.asesores = response.data.asesores;
					self.FiltrarAsesores();
				});
		},

		FiltrarAsesores() {
			this.asesores_filtrados = [];
			this.asesor_seleccionado = 0;

			if (this.modo == "completo") {
				this.asesores_filtrados = this.asesores.filter(
					(item) => item.agencia_id == this.agencia_busqueda
				);
			} else if (this.modo == "personal") {
				this.asesores_filtrados = this.asesores.filter(
					(item) => item.dni == this.usuario
				);

				if (this.asesores_filtrados.length > 0) {
					this.asesor_seleccionado = this.asesores_filtrados[0].dni;
				}
			}
		},

		async Procesar() {
			let self = this;

			if (this.asesor_seleccionado == 0 || this.agencia_busqueda == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			}

			// let data = new FormData();
			// data.append("usuario_id", this.asesor_seleccionado);
			// data.append("agencia_id", this.agencia_busqueda);
			// data.append("meses_historial", this.meses_historial);

			// this.$inertia.post(route("her.indicador_objetivo.procesar"), data);
			// return false;

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				showConfirmButton: false,
				allowOutsideClick: false,

				willOpen: async () => {
					let data = new FormData();
					data.append("usuario_id", this.asesor_seleccionado);
					data.append("agencia_id", this.agencia_busqueda);
					data.append("meses_historial", this.meses_historial);

					Swal.showLoading();
					await axios
						.post(route("her.indicador_objetivo.procesar"), data)
						.then(function (response) {
							if (response.data.categoria_actual != null) {
								self.categorias = response.data.categorias;
								self.categoria_actual = response.data.categoria_actual;
								self.subcategoria_actual = response.data.subcategoria_actual;
								self.interes_cobranzas = response.data.interes_cobranzas;
								self.interes_descuentos = response.data.interes_descuentos;
								self.porcentaje_avance = response.data.porcentaje_avance;
								self.historial = response.data.historial;

								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
									timer: 1200,
									showConfirmButton: false,
								});
							} else {
								self.categoria_actual = null;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No hay registros encontrados",
									timer: 1200,
									showConfirmButton: false,
								});
							}
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-indicador-objetivo {
	width: 60% !important;
	margin-left: 20% !important;
	margin-top: 5% !important;
}

#indicator-0 {
	position: absolute;
	margin: 0;
	margin-top: -19px;
	/* margin-left: 8.5%; */
}

#indicator-0 i {
	color: #244b9a;
	font-weight: bolder;
}

.line-mark {
	color: #244b9a;
	font-size: 60px;
	line-height: 1;
}

#indicator-1 {
	position: absolute;
	margin: 0;
	margin-top: -25px;
	/* margin-left: 9%; */
}

#indicator-2 {
	position: absolute;
	margin: 0;
	margin-top: -25px;
	/* margin-left: 19%; */
}

.legend-1 {
	color: black;
	font-size: 12px;
	margin: 0;
	font-weight: bold;
	line-height: 0;

	transform: rotate(-45deg);
	transform-origin: left top;
}
.line {
	/* color: #b6d7a8; */
	font-size: 50px;
	margin: 0;
}
.legend-2 {
	color: black;
	font-size: 12px;
	margin: 0;
	font-weight: bold;
	line-height: 0;
}

.progress {
	height: 2rem;
}

.progress-bar {
	background-color: rgb(245, 245, 245);
	color: black;
	/* border-radius: 1px; */
	border: 1px solid gray;
}
.categoria-1 {
	background-color: #9fce8b;
	color: black;
}
.categoria-2 {
	background-color: #009e0f;
	color: white;
}
.categoria-3 {
	background-color: #d8d802;
	color: black;
}
.categoria-4 {
	background-color: #01d6d6;
	color: black;
}
.categoria-5 {
	background-color: #dd8602;
	color: white;
}
.categoria-6 {
	background-color: #bb0300;
	color: white;
}

.container-limits {
	display: flex;
	justify-content: space-between;
	width: 100%;
	max-width: 90%; /* Opcional, ajusta según sea necesario */
	margin-left: 5px;
	margin-right: 5px;
	font-weight: bolder;
}
.left-text {
	text-align: left;
}
.right-text {
	text-align: right;
}

@media (max-width: 1280px) {
	.slot-indicador-objetivo {
		width: 45% !important;
		margin-left: 27.5% !important;
	}
}
@media (max-width: 900px) {
	.slot-indicador-objetivo {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

