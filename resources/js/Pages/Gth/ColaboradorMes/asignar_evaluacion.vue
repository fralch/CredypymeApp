<template>
	<layout ref="layout">
		<div class="slot_body slot-asignar-evaluacion" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ASIGNAR EVALUACIÓN'"></headerClose>
					<div class="card-title">INFORMACIÓN DE LA EVALUACIÓN</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-3">
								<label class="label-title">FECHA</label>
								<br />
								<date-picker
									v-model="fecha_seleccionada"
									@change="ComprobarEvaluacion"
									class="center"
									type="month"
									:editable="false"
									value-type="format"
									placeholder="Seleccione un mes"
									style="width: 200px !important"
								></date-picker>
							</div>

							<div class="form-group col-md-4">
								<label for="slcColaboradormes_equipos" class="label-title"
									>EQUIPO</label
								>

								<select
									class="form-control"
									name="equipos"
									id="slcColaboradormes_equipos"
									@change="FiltrarIntegrantes"
									v-model="equipo_seleccionado"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="equipo in equipos"
										:key="equipo.id"
										:value="equipo.id"
									>
										{{ equipo.equipo }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-4">
								<label for="inpResponsable" class="label-title"
									>RESPONSABLE(S)</label
								>
								<div
									v-for="(resposable, index) in responsable_seleccionado"
									:key="index"
								>
									<input
										type="text"
										class="form-control mb-2"
										id="inpResponsable"
										disabled
										:value="resposable"
									/>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12">
								<DataTable
									:value="integrantes_equipo_filtrado"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="250px"
									showGridlines
								>
									<Column
										field="nombre_integrante"
										header="INTEGRANTE(S)"
										:styles="{
											width: '300px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="agencia_integrante"
										header="AGENCIA"
										:styles="{
											width: '300px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="cargo_integrante"
										header="CARGO"
										:styles="{
											width: '300px',
											justifyContent: 'center',
										}"
									>
									</Column>
								</DataTable>
							</div>
						</div>
					</div>
					<div class="card-title">SELECCIÓN DE EXÁMENES</div>
					<div class="card-body card-block">
						<div class="form-row mb-3">
							<div class="form-group col-md-6">
								<label class="label-title"
									>EXAMEN QUE DARÁN LOS RESPONSABLES
								</label>

								<div class="form-row">
									<select
										class="form-control"
										style="max-width: 300px"
										v-model="frmEvaluacion_R_I.examen_id"
										:class="[
											submited_R_I
												? frmEvaluacion_R_I.examen_id == 0
													? 'is-invalid'
													: 'is-valid'
												: '',
										]"
										:disabled="frmEvaluacion_R_I.asignado"
									>
										<option :value="0">Seleccione...</option>
										<option
											v-for="examen in examenes"
											:key="examen.id"
											:value="examen.id"
										>
											{{ examen.examen }}
										</option>
									</select>
									<button
										class="btn btn-action btn-icon-split ml-2"
										@click="CrearEvaluacion('r_i')"
										:disabled="frmEvaluacion_R_I.asignado"
									>
										<span class="icon text-white">
											<i class="fas fa-check-double"></i>
										</span>
										<span class="text">CREAR</span>
									</button>
								</div>
							</div>

							<div class="form-group col-md-6">
								<label class="label-title"
									>EXAMEN QUE DARÁN LOS INTEGRANTES</label
								>

								<div class="form-row">
									<select
										class="form-control"
										style="max-width: 300px"
										v-model="frmEvaluacion_I_R.examen_id"
										:class="[
											submited_I_R
												? frmEvaluacion_I_R.examen_id == 0
													? 'is-invalid'
													: 'is-valid'
												: '',
										]"
										:disabled="frmEvaluacion_I_R.asignado"
									>
										<option :value="0">Seleccione...</option>
										<option
											v-for="examen in examenes"
											:key="examen.id"
											:value="examen.id"
										>
											{{ examen.examen }}
										</option>
									</select>
									<button
										class="btn btn-action btn-icon-split ml-2"
										@click="CrearEvaluacion('i_r')"
										:disabled="frmEvaluacion_I_R.asignado"
									>
										<span class="icon text-white">
											<i class="fas fa-check-double"></i>
										</span>
										<span class="text">CREAR</span>
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,

		DatePicker,

		DataTable,
		Column,
	},
	props: { equipos: Array, integrantes_equipo: Array, examenes: Array },
	data() {
		return {
			submited_R_I: false,
			submited_I_R: false,

			integrantes_equipo_filtrado: [],
			responsable_seleccionado: [],

			fecha_seleccionada: null,
			equipo_seleccionado: 0,
			responsables_equipo: [],

			frmEvaluacion_R_I: {
				tipo: "R_I",
				anio: 0,
				mes: 0,
				equipo_id: 0,
				examen_id: 0,
				asignado: false,
			},

			frmEvaluacion_I_R: {
				tipo: "I_R",
				anio: 0,
				mes: 0,
				equipo_id: 0,
				examen_id: 0,
				asignado: false,
			},
		};
	},
	validations: {
		frmEvaluacion_R_I: {
			anio: { noZero },
			mes: { noZero },
			equipo_id: { noZero },
			examen_id: { noZero },
		},
		frmEvaluacion_I_R: {
			anio: { noZero },
			mes: { noZero },
			equipo_id: { noZero },
			examen_id: { noZero },
		},
	},
	computed: {
		anio() {
			if (this.fecha_seleccionada != null) {
				let fecha_separada = this.fecha_seleccionada.split("-");
				return parseInt(fecha_separada[0]);
			} else {
				return 0;
			}
		},
		mes() {
			if (this.fecha_seleccionada != null) {
				let fecha_separada = this.fecha_seleccionada.split("-");
				return parseInt(fecha_separada[1]);
			} else {
				return 0;
			}
		},
	},
	watch: {
		fecha_seleccionada(val) {
			if (val != null) {
				this.frmEvaluacion_R_I.anio = this.anio;
				this.frmEvaluacion_R_I.mes = this.mes;
				this.frmEvaluacion_I_R.anio = this.anio;
				this.frmEvaluacion_I_R.mes = this.mes;
			}
		},
		equipo_seleccionado(val) {
			this.frmEvaluacion_R_I.equipo_id = val;
			this.frmEvaluacion_I_R.equipo_id = val;
		},
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		FiltrarIntegrantes(e) {
			let equipo_id = e.target.value;

			this.responsables_equipo = [];

			this.responsable_seleccionado = [];
			if (equipo_id == 0) {
				this.integrantes_equipo_filtrado = [];
				this.responsable_seleccionado = "-";
			} else {
				this.integrantes_equipo_filtrado = this.integrantes_equipo.filter(
					(item) => item.equipo_id == equipo_id
				);
				let { responsable_id } = this.equipos.filter(
					(item) => item.id == equipo_id
				)[0];

				let nombre_responsable = "";
				JSON.parse(responsable_id).forEach((item) => {
					nombre_responsable =
						item.nombres +
						" " +
						item.apellido_paterno +
						" " +
						item.apellido_materno;
					this.responsable_seleccionado.push(nombre_responsable);
					this.responsables_equipo.push(item.dni);
				});
			}

			this.ComprobarEvaluacion();
		},

		async ComprobarEvaluacion() {
			let self = this;
			if (this.fecha_seleccionada != null && this.equipo_seleccionado != 0) {
				this.frmEvaluacion_I_R.examen_id = 0;
				this.frmEvaluacion_R_I.examen_id = 0;

				await axios
					.post(route("gth.col_mes.asignar_evaluacion.comprobar"), {
						anio: self.anio,
						mes: self.mes,
						equipo_id: self.equipo_seleccionado,
						responsable: self.responsables_equipo[0],
					})
					.then(function (response) {
						let resultado = response.data;

						let evaluacion_asignada_r =
							resultado.evaluacion_asignada_r[0]?.examen_id;

						let evaluacion_asignada_i =
							resultado.evaluacion_asignada_i[0]?.examen_id;

						if (evaluacion_asignada_r) {
							self.frmEvaluacion_R_I.examen_id = evaluacion_asignada_r;
							self.frmEvaluacion_R_I.asignado = true;
						} else {
							self.frmEvaluacion_R_I.asignado = false;
						}

						if (evaluacion_asignada_i) {
							self.frmEvaluacion_I_R.examen_id = evaluacion_asignada_i;
							self.frmEvaluacion_I_R.asignado = true;
						} else {
							self.frmEvaluacion_I_R.asignado = false;
						}
					});
			}
		},

		async CrearEvaluacion(tipo) {
			let self = this;

			let data = new FormData();

			if (tipo == "r_i") {
				this.submited_R_I = true;
				this.submited_I_R = false;
				if (this.$v.frmEvaluacion_R_I.$invalid) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Hay uno o más campos vacíos, verifique.",
					});
					return false;
				} else {
					data.append("evaluacion", JSON.stringify(this.frmEvaluacion_R_I));
				}
			} else if (tipo == "i_r") {
				this.submited_I_R = true;
				this.submited_R_I = false;
				if (this.$v.frmEvaluacion_I_R.$invalid) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Hay uno o más campos vacíos, verifique.",
					});
					return false;
				} else {
					data.append("evaluacion", JSON.stringify(this.frmEvaluacion_I_R));
				}
			}

			await axios
				.post(route("gth.col_mes.asignar_evaluacion.verificar"), data)
				.then(function (response) {
					let resultado = response.data;
					if (resultado == "EXISTE") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Esta EVALUACIÓN ya está creada, intente nuevamente.",
						});
						return false;
					} else if (resultado == "NO EXISTE") {
						Swal.fire({
							title: "CREAR EVALUACIÓN",
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
								Swal.fire({
									title: "REGISTRANDO",
									showConfirmButton: false,
									allowOutsideClick: false,
									willOpen: async () => {
										Swal.showLoading();
										return await axios
											.post(
												route("gth.col_mes.asignar_evaluacion.guardar"),
												data
											)
											.then((response) => {
												self.submited_R_I = false;
												self.submited_I_R = false;

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
					}
				});
		},
	},
};
</script>

<style >
.slot-asignar-evaluacion {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-asignar-evaluacion {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
