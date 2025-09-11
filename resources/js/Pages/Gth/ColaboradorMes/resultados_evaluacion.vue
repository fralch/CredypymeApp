<template>
	<layout ref="layout">
		<div class="slot_body slot-resultados" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'RESULTADOS DE EVALUACIÓN'"></headerClose>
					<div class="card-title">FILTRO DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<fieldset class="form-group col-md-7">
								<legend>
									<label class="label-title"
										>SELECCIONE LA FECHA DE LA EVALUACIÓN</label
									>
								</legend>

								<div class="form-row">
									<div class="form-group col-md-6">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>AGENCIA</span
												>
											</div>
											<select
												class="form-control center"
												v-model="agencia_seleccionada"
											>
												<option :value="0" selected disabled>
													Seleccione...
												</option>
												<option
													v-for="(item, index) in agencias"
													:key="index"
													:value="item.id"
												>
													{{ item.agencia }}
												</option>
											</select>
										</div>
									</div>
									<div class="form-group col-md-6">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>FECHA</span
												>
											</div>

											<date-picker
												v-model="fecha_seleccionada"
												class="center"
												type="month"
												:editable="false"
												value-type="format"
												placeholder="Seleccione un mes"
												style="width: 200px !important"
											></date-picker>
										</div>
									</div>
								</div>
							</fieldset>
							<div class="col-md-1 mt-3">
								<button
									class="btn btn-action btn-icon-split"
									title="Buscar"
									@click="Buscar"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>
					</div>
					<div class="card-title">{{ titulo_tabla }}</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-7 col-8">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Buscar </span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										v-model="filtros_tabla['global'].value"
										:disabled="lista_resultados.length == 0"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										spellcheck="false"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
						</div>

						<DataTable
							:value="lista_resultados"
							:row-class="rowClass"
							responsiveLayout="scroll"
							sortMode="single"
							:sortOrder="1"
							scrollable
							scrollHeight="450px"
							:filters="filtros_tabla"
							:globalFilterFields="[
								'apellido_paterno',
								'apellido_materno',
								'nombres',
								'cargo',
							]"
							showGridlines
						>
							<Column
								field="index"
								header="N°"
								:styles="{
									width: '40px',
									justifyContent: 'center',
								}"
							>
								<template #body="{ index }">
									{{ index + 1 }}
								</template>
							</Column>

							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{ minWidth: '120px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.agencia }}
								</template>
							</Column>

							<Column
								field="apellido_paterno"
								header="APELLIDO_PATERNO"
								:styles="{ minWidth: '170px' }"
							>
								<template #body="{ data }">
									{{ data.apellido_paterno }}
								</template>
							</Column>
							<Column
								field="apellido_materno"
								header="APELLIDO_MATERNO"
								:styles="{ minWidth: '170px' }"
							>
								<template #body="{ data }">
									{{ data.apellido_materno }}
								</template>
							</Column>
							<Column
								field="nombres"
								header="NOMBRES"
								:styles="{ minWidth: '170px' }"
							>
								<template #body="{ data }">
									{{ data.nombres }}
								</template>
							</Column>
							<Column
								field="cargo"
								header="CARGO"
								:styles="{ minWidth: '250px' }"
							>
								<template #body="{ data }">
									{{ data.cargo }}
								</template>
							</Column>
							<Column
								field="promedio"
								header="PROMEDIO"
								:styles="{ minWidth: '70px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ roundTo(data.promedio, 2) }}
								</template>
							</Column>
							<Column
								field="ver"
								header="DETALLE"
								:styles="{ minWidth: '100px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn-cancel btn-icon-split btn-sm"
										title="VER RESULTADO"
										@click="VerResultado(data)"
									>
										<span class="icon text-white">
											<i class="pi pi-eye"></i>
										</span>
										<span class="text">VER</span>
									</button>
								</template>
							</Column>
						</DataTable>

						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar"
								:disabled="lista_resultados.length == 0"
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
			<!-- The Modal -->
			<div id="mdlDetalleResultado" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-60 mdlDetalleResultado">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="
									'DETALLE DE RESULTADO DE: ' +
									evaluado_seleccionado.usuario.toUpperCase()
								"
								:nombre_modal="'mdlDetalleResultado'"
							>
							</headerCloseModal>

							<div class="card-title">LISTA DE EVALUACIONES</div>
							<div class="card-body card-block">
								<DataTable
									:value="evaluaciones"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="400px"
									showGridlines
								>
									<Column
										field="index"
										header="N°"
										:styles="{
											width: '40px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ index }">
											{{ index + 1 }}
										</template>
									</Column>

									<Column
										field="evaluador"
										header="EVALUADOR"
										:styles="{ width: '120px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ data.evaluador }}
										</template>
									</Column>

									<Column
										field="equipo"
										header="EQUIPO"
										:styles="{ width: '170px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ data.equipo }}
										</template>
									</Column>
									<Column
										field="examen"
										header="EXAMEN"
										:styles="{ width: '170px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											{{ data.examen }}
										</template>
									</Column>
									<Column
										field="puntuacion_total"
										header="PUNTUACION"
										:styles="{
											width: '120px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											{{ roundTo(data.puntuacion_total, 2) }}
										</template>
									</Column>
									<Column
										field="evaluacion"
										header="EVALUACIÓN"
										:styles="{ width: '120px', justifyContent: 'center' }"
									>
										<template #body="{ data }">
											<button
												class="btn btn-cancel btn-icon-split btn-sm"
												title="VER EVALUACIÓN"
												@click="VerEvaluacion(data)"
											>
												<span class="icon text-white">
													<i class="pi pi-eye"></i>
												</span>
												<span class="text">VER</span>
											</button>
										</template>
									</Column>
									<ColumnGroup type="footer">
										<Row>
											<Column
												:colspan="4"
												footer="TOTAL:"
												:footerStyle="{ width: '500px', 'text-align': 'right' }"
											/>
											<Column
												:footer="roundTo(evaluado_seleccionado.promedio, 2)"
												:footerStyle="{
													width: '120px',
													'text-align': 'center',
												}"
											/>
											<Column
												footer=""
												:footerStyle="{ width: '120px', 'text-align': 'right' }"
											/>
										</Row>
									</ColumnGroup>
								</DataTable>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="mdlDetalleEvaluacion" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-60 mdlDetalleEvaluacion">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'DETALLE DE LA EVALUACIÓN REGISTRADA'"
								:nombre_modal="'mdlDetalleEvaluacion'"
							>
							</headerCloseModal>

							<div class="card-title">RESULTADOS</div>
							<div class="card-body card-block">
								<DataTable
									:value="lista_preguntas"
									rowGroupMode="subheader"
									groupRowsBy="categoria"
									responsiveLayout="scroll"
									sortMode="single"
									sortField="categoria"
									:sortOrder="1"
									scrollable
									scrollHeight="450px"
									showGridlines
								>
									<template #groupheader="slotProps">
										<span class="row-group-header">{{
											"CATEGORÍA: " + slotProps.data.categoria
										}}</span>
									</template>

									<Column
										field="pregunta"
										header="PREGUNTA"
										:styles="{
											minWidth: windowWidth > 992 ? '700px' : '150px',
											backgroundColor: 'var(--colorMedio)',
											color: 'black',
										}"
									>
										<template #body="{ data }">
											<div class="form-row col-md-12">
												<div
													class="col-md-3 text-center"
													style="
														background-color: var(--colorBajo) !important;
														font-weight: bolder;
														border-radius: 2px;
													"
												>
													<span class="text">{{ data.criterio }}</span>
												</div>
												<div class="col-md-9">
													{{ data.pregunta }}
												</div>
											</div>
										</template>
									</Column>
									<Column
										field="pregunta"
										:styles="{ minWidth: '180px', justifyContent: 'center' }"
									>
										<template #header>
											<span class="ms-1">{{
												evaluado_seleccionado.usuario.toUpperCase()
											}}</span>
										</template>

										<template #body="{ data }">
											<input
												type="text"
												class="form-control center"
												:value="data.escala_resultado"
												disabled
											/>
										</template>
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
import headerCloseModal from "@/Pages/Gth/Components/header_close_modal.vue";
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import Row from "primevue/row/row.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";

import { FilterMatchMode } from "primevue/api";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DatePicker,

		DataTable,
		Column,
		Row,
		ColumnGroup,
	},

	data: () => ({
		windowWidth: window.innerWidth,
		titulo_tabla: "COLABORADOR DEL MES",

		fecha_seleccionada: null,
		agencia_seleccionada: 0,

		nombre_mes: null,
		año_seleccionado: 0,

		lista_resultados: [],
		filtros_tabla: {},

		evaluado_seleccionado: {
			usuario: "",
		},
		evaluaciones: [],
		evaluaciones_detalle: [],
		lista_preguntas: [],
	}),
	computed: {
		agencias() {
			return this.$page.props.application.agencias;
		},
	},
	created() {
		this.filtros_tabla = {
			global: { value: null, matchMode: FilterMatchMode.CONTAINS },
			evaluador: { value: null, matchMode: FilterMatchMode.CONTAINS },
			examen: { value: null, matchMode: FilterMatchMode.CONTAINS },
		};
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},
	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		rowClass(data) {
			let index = this.lista_resultados.findIndex(
				(item) => item.dni == data.dni && item.promedio == data.promedio
			);

			return index < 3 ? "resaltado" : null;
		},
		roundTo(value, numero_decimales) {
			let valor = 0;
			let numero_decimales_value = numero_decimales;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales_value);
		},

		Buscar() {
			let self = this;

			if (this.agencia_seleccionada == 0 || this.fecha_seleccionada == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Por favor rellene los campos de AGENCIA y FECHA",
				});
				return false;
			} else {
				let data = new FormData();

				data.append("agencia_seleccionada", this.agencia_seleccionada);
				data.append("fecha_seleccionada", this.fecha_seleccionada);

				Swal.fire({
					title: "BUSCANDO",
					text: "Espere porfavor...",
					allowOutsideClick: false,
					didOpen: () => {
						// this.$inertia.post(route("col.resultados.buscar"), data);
						// return false;

						Swal.showLoading();
						axios
							.post(route("col.resultados.buscar"), data)
							.then(function (response) {
								if (response.data.lista_resultados.length == 0) {
									self.lista_resultados = [];
									self.titulo_tabla = "COLABORADOR DEL MES";

									return Swal.fire({
										icon: "info",
										title: "¡Ups!",
										text: "No se encontraron datos",
										allowOutsideClick: true,
									});
								} else {
									self.lista_resultados = response.data.lista_resultados;

									self.nombre_mes = response.data.nombre_mes;
									self.año_seleccionado = response.data.año_seleccionado;

									self.titulo_tabla =
										"COLABORADOR DEL MES DE " +
										self.nombre_mes +
										" - " +
										self.año_seleccionado;

									return Swal.fire({
										icon: "success",
										title: "¡Listo!",
										timer: 2000,
										showConfirmButton: false,
									});
								}
							});
					},
				});
			}
		},

		async VerResultado(item) {
			let self = this;
			this.evaluado_seleccionado = item;

			let data = new FormData();
			data.append("fecha_seleccionada", this.fecha_seleccionada);
			data.append("evaluado_id", item.dni);

			// this.$inertia.post(route("col.resultados.detalle"), data);
			// return false;

			await axios
				.post(route("col.resultados.detalle"), data)
				.then(function (response) {
					self.evaluaciones = response.data.evaluaciones;
					self.evaluaciones_detalle = response.data.evaluaciones_detalle;

					$("#mdlDetalleResultado").css("display", "block");
				});
		},

		async VerEvaluacion(data) {
			let evaluacion_id = data.id;

			this.lista_preguntas = this.evaluaciones_detalle.filter(
				(item) => item.evaluacion_id == evaluacion_id
			);

			$("#mdlDetalleEvaluacion").css("display", "block");
		},

		Exportar() {
			self = this;
			let data = new FormData();
			data.append("datos", JSON.stringify(this.lista_resultados));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(route("gth.col_mes.resultado_evaluacion.exportar"), data);
					// return false;

					axios
						.post(route("col.resultados.exportar"), data)
						.then(function (response) {
							let origin = window.location.origin;
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptResultadosEvaluacion.xlsx";
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
.slot-resultados {
	width: 70% !important;
	margin-left: 15% !important;
}

.resaltado {
	background-color: var(--colorBajo) !important;
	font-weight: 700 !important;
}

.mdlDatosPregunta {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-resultados {
		width: 98% !important;
		margin-left: 1% !important;
	}

	.mdlDatosPregunta {
		margin-top: 2%;
	}
}
</style>
