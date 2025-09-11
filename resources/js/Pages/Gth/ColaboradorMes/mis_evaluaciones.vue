<template>
	<layout ref="layout">
		<div class="slot_body slot-mis-evaluaciones" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MIS EVALUACIONES PENDIENTES'"></headerClose>

					<div class="card-body card-block">
						<div v-if="modo == 'VISTA'">
							<div class="card-title mb-1">LISTA DE EVALUACIONES</div>
							<DataTable
								:value="evaluaciones"
								:scrollable="true"
								scrollDirection="both"
								scrollHeight="400px"
								showGridlines
							>
								<Column
									field="generar"
									header="ACCIÓN"
									:styles="{ width: '100px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										<button
											class="btn btn-cancel btn-icon-split"
											title="EMPEZAR EVALUACIÓN"
											@click="GenerarEvaluacion(data)"
										>
											<span class="icon text-white">
												<i class="fa fa-angle-double-down"></i>
											</span>
											<span class="text">EMPEZAR</span>
										</button>
									</template>
								</Column>
								<Column
									field="anio"
									header="AÑO"
									:styles="{ width: '100px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										{{ data.año }}
									</template>
								</Column>
								<Column
									field="mes"
									header="MES"
									:styles="{ width: '100px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										{{ numberToMonth(data.mes) }}
									</template>
								</Column>
								<Column
									field="nombre"
									header="NOMBRE"
									:styles="{ width: '250px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										{{ data.nombre }}
									</template>
								</Column>
								<Column
									field="equipo"
									header="EQUIPO"
									:styles="{ width: '250px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										{{ data.equipo }}
									</template>
								</Column>
							</DataTable>
						</div>

						<div v-if="modo == 'EVALUACION'">
							<div class="card-title mb-1">{{ titulo_evaluacion }}</div>

							<DataTable
								:value="preguntas"
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
										minWidth: windowWidth > 992 ? '530px' : '150px',
										backgroundColor: 'var(--colorMedio)',
										color: 'black',
									}"
									frozen
								>
									<template #body="{ data }">
										<div class="form-row col-md-12">
											<div
												class="col-md-2 text-center"
												style="
													background-color: var(--colorBajo) !important;
													font-weight: bolder;
													border-radius: 2px;
												"
											>
												<span class="text">{{ data.criterio }}</span>
											</div>
											<div class="col-md-10">
												{{ data.pregunta }}
											</div>
										</div>
									</template>
								</Column>
								<Column
									v-for="(item_1, index) in evaluados"
									:key="index"
									:styles="{ minWidth: '180px', justifyContent: 'center' }"
								>
									<template #header>
										<span class="ms-1">{{ item_1.usuario.toUpperCase() }}</span>
									</template>

									<template #body="{ data }">
										<select
											class="form-control center"
											style="font-size: 11px !important; max-width: 200px"
											:ref="'slc_' + item_1.dni + '_' + data.id"
											@change="ActualizarResultado"
											:class="[
												submited
													? lista_resultados.find(
															(item) =>
																item.usuario_id == item_1.dni &&
																item.pregunta_id == data.id
													  ).escala == null
														? 'is-invalid'
														: 'is-valid'
													: '',
											]"
										>
											<option :value="null" selected disabled>
												Seleccione....
											</option>
											<option
												v-for="(item_2, index) in JSON.parse(data.escalas)"
												:key="index"
												:value="item_1.dni + '_' + data.id + '_' + item_2.valor"
											>
												{{ item_2.titulo }}
											</option>
										</select>
									</template>
								</Column>
							</DataTable>

							<hr />
							<div class="form-row" v-if="preguntas.length != 0">
								<div class="col-md-3 text-left">
									<button
										class="btn btn-danger btn-icon-split"
										@click="modo = 'VISTA'"
									>
										<span class="icon text-white">
											<i class="fas fa-times"></i>
										</span>
										<span class="text">CANCELAR</span>
									</button>
								</div>
								<div class="col-md-9 text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-cancel btn-icon-split"
											@click="GuardarAvance"
										>
											<span class="icon text-white">
												<i class="pi pi-clock"></i>
											</span>
											<span class="text">GUARDAR AVANCE</span>
										</button>
										<button
											class="btn btn-action btn-icon-split"
											@click="TerminarEvaluacion"
										>
											<span class="icon text-white">
												<i class="pi pi-check-circle"></i>
											</span>
											<span class="text">TERMINAR EVALUACIÓN</span>
										</button>
									</div>
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
	components: {
		layout,
		headerClose,
		DataTable,
		Column,
		Row,
		ColumnGroup,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			evaluaciones: [],

			modo: "VISTA",
			titulo_evaluacion: null,
			evaluacion_seleccionada: {},
			preguntas: [],
			evaluados: [],
			preguntas_avance: [],
			lista_resultados: [],
			submited: false,
		};
	},
	mounted() {
		this.ListarRecursos();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},
	watch: {
		async evaluados(value) {
			let self = this;
			this.lista_resultados = [];
			let obj = null;
			await value.forEach((element) => {
				self.preguntas.forEach((element_1) => {
					let pregunta = self.preguntas_avance.find(
						(item) =>
							item.evaluado_id == element.dni &&
							item.pregunta_id == element_1.id
					);

					obj = {
						usuario_id: element.dni,
						pregunta_id: element_1.id,
						valor: pregunta.puntuacion,
						escala: pregunta.escala_resultado,
						peso: parseFloat(element_1.peso) / 100,
					};

					self.lista_resultados.push(obj);
				});
			});

			return self.RellenarAvance();
		},
	},
	methods: {
		async Ubicar() {
			await console.log(this.$refs.slc_1);
		},
		async ListarRecursos() {
			let self = this;

			return await axios
				.get(route("col.mis_evaluaciones.listar_recursos"))
				.then(function (response) {
					self.evaluaciones = response.data.evaluaciones;
				});
		},

		numberToMonth(n) {
			var month = [
				"Enero",
				"Febrero",
				"Marzo",
				"Abril",
				"Mayo",
				"Junio",
				"Julio",
				"Agosto",
				"Septiembre",
				"Octubre",
				"Noviembre",
				"Diciembre",
			];
			return month[n - 1];
		},

		async GenerarEvaluacion(evaluacion) {
			let self = this;

			this.submited = false;

			this.titulo_evaluacion =
				"EVALUACIÓN DEL MES DE " +
				this.numberToMonth(evaluacion.mes) +
				" " +
				evaluacion.año;

			let data = new FormData();

			data.append("examen_id", evaluacion.examen_id);
			data.append("equipo_id", evaluacion.equipo_id);
			data.append("anio", evaluacion.año);
			data.append("mes", evaluacion.mes);

			// this.$inertia.post(route("col.mis_evaluaciones.generar"), data);
			// return false;

			this.evaluacion_seleccionada = evaluacion;

			await axios
				.post(route("col.mis_evaluaciones.generar"), data)
				.then((response) => {
					self.preguntas = response.data.preguntas;
					self.evaluados = response.data.evaluados;
					self.preguntas_avance = response.data.preguntas_avance;
				});

			this.modo = "EVALUACION";
		},
		ActualizarResultado(e) {
			let value = e.target.value.split("_");

			let usuario_id = value[0];
			let pregunta_id = value[1];
			let valor = value[2];
			let escala = e.target.options[e.target.selectedIndex].text;

			let resultado = this.lista_resultados.find(
				(obj) => obj.usuario_id == usuario_id && obj.pregunta_id == pregunta_id
			);

			resultado.valor = valor;
			resultado.escala = escala;
		},
		async RellenarAvance() {
			this.lista_resultados.forEach((element) => {
				if (element.escala != null) {
					let slc = "slc_" + element.usuario_id + "_" + element.pregunta_id;
					let valor =
						element.usuario_id +
						"_" +
						element.pregunta_id +
						"_" +
						element.valor;
					this.$refs[slc][0].value = valor;
				}
			});
		},
		TerminarEvaluacion_2() {
			let self = this;
			self.submited = true;
			let datos_organizados = [];
			self.preguntas.forEach((element) => {
				element.datos.forEach((item) => {
					item.seleccion.forEach((item3) => {
						if (parseFloat(item3.puntuacion_total) < 0.1) {
							self.listo_enviar = false;
						} else {
							self.listo_enviar = true;
						}
						let existe_usuario = datos_organizados.some(
							(item2) => item2.evaluado_id == item3.evaluado_id
						);

						if (!existe_usuario) {
							datos_organizados.push({
								evaluado_id: item3.evaluado_id,
								evaluacion_id: item.evaluacion_id,
								examen_id: item.examen_id,
								calculo_puntuacion: 0,
								pregunta: [],
							});
						}

						let existe_pregunta = datos_organizados.some(
							(item2) =>
								item2.evaluado_id == item3.evaluado_id &&
								item2.pregunta.some((item4) => item4.id == item.pregunta_id)
						);

						if (!existe_pregunta) {
							datos_organizados.forEach((item2) => {
								if (item2.evaluado_id == item3.evaluado_id) {
									item2.pregunta.push({
										id: item.pregunta_id,
										nombre: item.pregunta,
										nombre_categoria: element.nombre_categoria,
										categoria_id: element.categoria_id,
										peso_categoria: element.peso_categoria,
										respuesta: parseFloat(item3.puntuacion_total),
									});

									item2.calculo_puntuacion +=
										parseFloat(item3.puntuacion_total) *
										(parseFloat(element.peso_categoria) * 0.01);
								}
							});
						} else {
							datos_organizados.forEach((item2) => {
								if (item2.evaluado_id == item3.evaluado_id) {
									item2.pregunta.forEach((item4) => {
										if (item4.id == item.pregunta_id) {
											item4.respuesta = parseFloat(item3.puntuacion_total);
										}
									});
								}
							});
						}
					});
				});
			});
			// console.log(datos_organizados);

			if (self.listo_enviar) {
				axios
					.post(route("gth.col_mes.mis_evaluaciones_pendientes.guardar"), {
						datos: datos_organizados,
					})
					.then((response) => {
						if (response.data) {
							Swal.fire({
								title: "Éxito!",
								text: "Evaluación registrada correctamente",
								icon: "success",
								confirmButtonText: "Aceptar",
							}).then((result) => {
								if (result.value) {
									self.evaluaciones_filtrado = response.data;
									self.submited = false;
									self.listo_enviar = false;
									self.preguntas = [];
									self.evaluados_ = [];
									self.titulo_evaluacion = "Evaluaciones";
									$("#div_evaluaciones").css("display", "block");
									$("#div_preguntas").css("display", "none");
								}
							});
						}
					});
			} else {
				Swal.fire({
					title: "Error!",
					text: "Debe calificar todas las preguntas",
					icon: "error",
					confirmButtonText: "Aceptar",
				});
			}
		},
		GuardarAvance() {
			let self = this;

			Swal.fire({
				title: "GUARDAR AVANCE",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();

					data.append(
						"evaluacion",
						JSON.stringify(self.evaluacion_seleccionada)
					);
					data.append(
						"lista_resultados",
						JSON.stringify(self.lista_resultados)
					);

					// this.$inertia.post(
					// 	route("col.mis_evaluaciones.guardar_avance"),
					// 	data
					// );
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("col.mis_evaluaciones.guardar_avance"), data)
								.then((response) => {
									self.modo = "VISTA";

									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										timer: 1200,
										showConfirmButton: false,
									});
								})
								.catch((error) => {
									Swal.showValidationMessage(
										`Ha ocurrido un error, comunicar a TI: ${error}`
									);
								});
						},
					});
				}
			});
		},
		TerminarEvaluacion() {
			let self = this;
			this.submited = true;
			if (
				this.lista_resultados.filter((item) => item.escala == null).length > 0
			) {
				Swal.fire({
					title: "¡Ups!",
					text: "Debe calificar todas las preguntas",
					icon: "error",
					confirmButtonText: "Aceptar",
				});

				return false;
			}
			Swal.fire({
				title: "TERMINAR EVALUACIÓN",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();

					data.append(
						"evaluacion",
						JSON.stringify(self.evaluacion_seleccionada)
					);
					data.append(
						"lista_resultados",
						JSON.stringify(self.lista_resultados)
					);

					data.append("anio", self.evaluacion_seleccionada.año);
					data.append("mes", self.evaluacion_seleccionada.mes);

					// this.$inertia.post(route("col.mis_evaluaciones.terminar"), data);
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("col.mis_evaluaciones.terminar"), data)
								.then((response) => {
									self.modo = "VISTA";
									self.ListarRecursos();

									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										timer: 1200,
										showConfirmButton: false,
									});
								})
								.catch((error) => {
									Swal.showValidationMessage(
										`Ha ocurrido un error, comunicar a TI: ${error}`
									);
								});
						},
					});
				}
			});
		},
	},
};
</script>

<style >
.slot-mis-evaluaciones {
	width: 70% !important;
	margin-left: 15% !important;
}

.p-rowgroup-header {
	background: var(--plomoOscuroEmpresarial) !important;
	color: white !important;
	text-align: center !important;
	justify-content: center !important;
	font-weight: bolder !important;
}

@media (max-width: 900px) {
	.slot-mis-evaluaciones {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
