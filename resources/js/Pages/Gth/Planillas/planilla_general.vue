<template>
	<layout ref="layout">
		<div class="slot_body slot-planilla-general" slot="component-view">
			<div class="card">
				<headerClose :title="'PLANILLA GENERAL'"></headerClose>

				<div class="card-title">DATOS DE LA PLANILLA</div>

				<div class="card-body card-block">
					<div class="form-row col-md-9">
						<div class="form-group col-md-2">
							<label class="form-control-label label-title" for="slcMeses"
								>MES</label
							>
							<select
								class="form-control center"
								id="slcMeses"
								style="max-width: 200px"
								v-model.number="frmDatosPlanilla.mes"
								:disabled="frmDatosPlanilla.datos_planilla.length > 0"
							>
								<option value="0">Seleccione...</option>
								<option :value="1">Enero</option>
								<option :value="2">Febrero</option>
								<option :value="3">Marzo</option>
								<option :value="4">Abril</option>
								<option :value="5">Mayo</option>
								<option :value="6">Junio</option>
								<option :value="7">Julio</option>
								<option :value="8">Agosto</option>
								<option :value="9">Septiembre</option>
								<option :value="10">Octubre</option>
								<option :value="11">Noviembre</option>
								<option :value="12">Diciembre</option>
							</select>
							<div
								v-if="submited && !$v.frmDatosPlanilla.mes.noZero"
								style="color: red; font-size: 12px"
							>
								*Campo obligatorio
							</div>
						</div>
						<div class="form-group col-md-2">
							<label class="form-control-label label-title" for="slcAnios"
								>AÑO</label
							>
							<select
								class="form-control center"
								id="slcAnios"
								style="max-width: 200px"
								v-model.number="frmDatosPlanilla.año"
								:disabled="frmDatosPlanilla.datos_planilla.length > 0"
							>
								<option value="0">Seleccione...</option>
								<option :value="2020">2020</option>
								<option :value="2021">2021</option>
								<option :value="2022">2022</option>
								<option :value="2023">2023</option>
								<option :value="2024">2024</option>
								<option :value="2025">2025</option>
							</select>
							<div
								v-if="submited && !$v.frmDatosPlanilla.año.noZero"
								style="color: red; font-size: 12px"
							>
								*Campo obligatorio
							</div>
						</div>
						<div class="form-group col-md-2">
							<label class="form-control-label label-title" for="slcAgencias"
								>AGENCIA</label
							>
							<select
								class="form-control center"
								id="slcAgencias"
								style="max-width: 250px"
								v-model.number="frmDatosPlanilla.id_agencia"
								:disabled="frmDatosPlanilla.datos_planilla.length > 0"
							>
								<option value="0">Seleccione...</option>
								<option
									v-for="agencia in agencias"
									:key="agencia.id_agencia"
									:value="agencia.id_agencia"
								>
									{{ agencia.nombre }}
								</option>
							</select>

							<div
								v-if="submited && !$v.frmDatosPlanilla.id_agencia.noZero"
								style="color: red; font-size: 12px"
							>
								*Campo obligatorio
							</div>
						</div>
					</div>

					<div class="text-center">
						<div class="btn-group" role="group">
							<button
								class="btn btn-cancel btn-icon-split"
								id="btnGenerarPlantilla"
								title="Generar nueva plantilla"
								@click="GenerarPlantilla"
								:disabled="frmDatosPlanilla.datos_planilla.length > 0"
							>
								<span class="icon text-white">
									<i class="fas fa-chevron-circle-down"></i>
								</span>
								<span class="text font-size-layout">Generar</span>
							</button>
						</div>
					</div>

					<br />
					<div class="input-group row col-md-10 col-7">
						<div class="input-group-prepend">
							<span class="input-group-text"
								><i class="fas fa-search"></i
							></span>
						</div>
						<input
							class="form-control mayus"
							type="text"
							id="inpBuscar"
							autocomplete="off"
							spellcheck="false"
							@focus="hidenav()"
							@blur="shownav()"
						/>
					</div>

					<table class="table table-hover" id="tblPlantilla" width="100%">
						<thead>
							<tr>
								<th>COLABORADOR</th>
								<th>AGENCIA</th>
								<th>DÍAS COMPUTABLES</th>
								<th>DÍAS VACACIONES</th>
								<th>DÍAS FALTAS</th>
								<th>DÍAS NO LAB.</th>
								<th>REM. VAC.</th>
								<th>
									PROR. REM.<br />
									BÁSICA
								</th>
								<th>PROR. COND. TRABAJO</th>
								<th>RIESGO CAJA</th>
								<th>BONIF.</th>
								<th>COMIS.</th>
								<th>TOTAL REM.<br />REAL</th>
								<th>TOTAL REM. COMPUTABLE</th>
								<th>DSCTO SIS. PENS.</th>
								<th>ADELANTOS</th>
								<th>DSCTO FALTAS</th>
								<th>DSCTO TARDANZAS</th>
								<th>DSCTO RIESGO CAJA</th>
								<th>DSCTO CRÉDITOS</th>
								<th>DSCTO OTROS</th>
								<th>TOTAL DSCTOS</th>
								<th>TOTAL NETO</th>
								<th>ESSALUD</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(
									dato_planilla, index
								) in frmDatosPlanilla.datos_planilla"
								:key="index"
							>
								<td
									class="table-bordered"
									style="
										text-transform: uppercase;
										background: var(--colorMedio);
									"
								>
									{{
										dato_planilla.apellido_paterno +
										" " +
										dato_planilla.apellido_materno +
										" " +
										dato_planilla.nombres
									}}
								</td>
								<td class="table-bordered" align="center">
									{{ dato_planilla.agencia }}
								</td>
								<td class="table-bordered" align="center">
									{{ dato_planilla.dias_computables }}
								</td>
								<td class="table-bordered" align="center">
									{{ dato_planilla.vacaciones }}
								</td>
								<td class="table-bordered" align="center">
									{{ dato_planilla.faltas }}
								</td>
								<td class="table-bordered" align="center">
									{{ dato_planilla.dias_no_laborados }}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.remuneracion_vacaciones)
											? 0
											: roundTo(dato_planilla.remuneracion_vacaciones, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.prorrateo_remuneracion_basica)
											? 0
											: roundTo(dato_planilla.prorrateo_remuneracion_basica, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.prorrateo_condiciones_trabajo)
											? 0
											: roundTo(dato_planilla.prorrateo_condiciones_trabajo, 2)
									}}
								</td>
								<td
									class="table-bordered"
									align="right"
									style="width: 150px !important"
								>
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.riesgoCaja"
										:id="'riesgoCaja_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.bonificaci"
										:id="'bonificaci_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.comisiones"
										:id="'comisiones_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.TotalmasComisiones)
											? 0
											: roundTo(dato_planilla.TotalmasComisiones, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.remuneracion_basica)
											? 0
											: roundTo(dato_planilla.remuneracion_basica, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.des_siste_pensiones)
											? 0
											: roundTo(dato_planilla.des_siste_pensiones, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.adelantoss"
										:id="'adelantoss_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.descFaltas)
											? 0
											: roundTo(dato_planilla.descFaltas, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.tardanzas)
											? 0
											: roundTo(dato_planilla.tardanzas, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.descRiesCj"
										:id="'descRiesCj_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.descCredit"
										:id="'descCredit_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:value="dato_planilla.descuOtros"
										:id="'descuOtros_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.Totaldesc)
											? 0
											: roundTo(dato_planilla.Totaldesc, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.total_neto)
											? 0
											: roundTo(dato_planilla.total_neto, 2)
									}}
								</td>
								<td class="table-bordered" align="right">
									S/
									{{
										isNaN(dato_planilla.essalud)
											? 0
											: roundTo(dato_planilla.essalud, 2)
									}}
								</td>
							</tr>
						</tbody>
					</table>
					<div class="text-center">
						<div class="btn-group" role="group">
							<button
								class="btn btn-action btn-icon-split"
								id="btnRegistrar"
								title="Registrar planilla"
								@click="RegistrarPlanilla"
								:disabled="frmDatosPlanilla.datos_planilla.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-save"></i>
								</span>
								<span class="text">REGISTRAR</span>
							</button>
							<button
								class="btn btn-cancel btn-icon-split"
								id="btnCancelar"
								title="Cancelar Edición"
								@click="CancelarRegistro"
								:disabled="frmDatosPlanilla.datos_planilla.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
								<span class="text">CANCELAR</span>
							</button>
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

const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		agencias: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			usuarios_filtros: [],
			frmDatosPlanilla: {
				año: 0,
				mes: 0,
				id_agencia: 0,
				datos_planilla: [],
			},
		};
	},
	validations: {
		frmDatosPlanilla: {
			año: { noZero },
			mes: { noZero },
			id_agencia: { noZero },
		},
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaPlantilla();
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblPlantilla").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblPlantilla")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblPlantilla").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblPlantilla")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				var power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},
		TablaPlantilla() {
			this.$nextTick(() => {
				var table = $("#tblPlantilla").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},

					scrollCollapse: true,
					paging: false,
					order: [1, "asc"],
					fixedHeader: true,
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
					responsive: true,
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});

				// ---------------
			});
		},

		GenerarPlantilla() {
			let self = this;
			this.submited = true;
			this.frmDatosPlanilla.datos_planilla = [];

			if (this.$v.frmDatosPlanilla.$invalid) {
				return false;
			} else {
				let año = this.frmDatosPlanilla.año;
				let mes = this.frmDatosPlanilla.mes;
				let id_agencia = this.frmDatosPlanilla.id_agencia;

				axios
					.post(route("gth.pla.planilla_general.generar"), {
						anio: año,
						mes: mes,
						id_agencia: id_agencia,
					})
					.then(function (response) {
						let resultado = response.data;
						if (resultado === 0) {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Esta planilla ya está generada, intente con otra fecha",
								allowOutsideClick: false,
							});
							return false;
						} else {
							self.usuarios_filtros = resultado;
							// ------------------------------***
							self.usuarios_filtros.forEach(function callback(
								currentValue,
								index
							) {
								let mes = self.frmDatosPlanilla.mes;
								let anio = self.frmDatosPlanilla.año;
								let agen = self.frmDatosPlanilla.id_agencia;
								let nombres = currentValue.nombres;
								let apellido_paterno = currentValue.apellido_paterno;
								let apellido_materno = currentValue.apellido_materno;
								let agencia = currentValue.agencia;
								let dni = currentValue.dni;
								let dias_computables = self.diasComputables(
									currentValue.fecha_ingreso
								);
								let vacaciones =
									currentValue.vacaciones == null ? 0 : currentValue.vacaciones;

								let faltas =
									currentValue.faltas == null ? 1 : currentValue.faltas;
								let dias_no_laborados = 30 - dias_computables; //
								let remuneracion_vacaciones = self.roundTo(
									(currentValue.remuneracion_real / 30) * vacaciones,
									2
								);
								let prorrateo_remuneracion_basica = 0;
								if (!isNaN(currentValue.remuneracion_basica)) {
									currentValue.remuneracion_basica -
										self.roundTo(
											(currentValue.remuneracion_real / 30) *
												currentValue.vacaciones,
											2
										);
								}
								let remuneracion_basica = 0;
								if (currentValue.remuneracion_basica) {
									remuneracion_basica = currentValue.remuneracion_basica;
								}

								let remuneracion_real = 0;
								if (currentValue.remuneracion_real) {
									remuneracion_real = currentValue.remuneracion_real;
								}
								let prorrateo_condiciones_trabajo =
									parseInt(remuneracion_real) - parseInt(remuneracion_basica);

								let riesgoCaja = 0;
								let bonificaci = 0;
								let comisiones = 0;
								let TotalmasComisiones = remuneracion_real;
								let des_siste_pensiones =
									currentValue.remuneracion_basica * currentValue.porcentaje;
								let adelantoss = 0;
								let descFaltas =
									currentValue.faltas * (currentValue.remuneracion_real / 30);
								let tardanzas = 0;
								if (currentValue.tardanzas) {
									tardanzas = currentValue.tardanzas;
								}
								let descRiesCj = 0;
								let descCredit = 0;
								let descuOtros = 0;
								let totaldescInicial =
									des_siste_pensiones + descFaltas + parseInt(tardanzas);
								let Totaldesc =
									des_siste_pensiones + descFaltas + parseInt(tardanzas);
								let essalud = 0;
								if (currentValue.essalud) {
									essalud = currentValue.essalud;
								}
								let total_neto = remuneracion_real - Totaldesc;

								let objeto = {
									nombres: nombres,
									apellido_paterno: apellido_paterno,
									apellido_materno: apellido_materno,
									Totaldesc: Totaldesc,
									agencia: agencia,
									dni: dni,
									riesgoCaja: riesgoCaja,
									dias_computables: dias_computables,
									vacaciones: vacaciones,
									faltas: faltas,
									dias_no_laborados: dias_no_laborados,
									remuneracion_vacaciones: remuneracion_vacaciones,
									prorrateo_remuneracion_basica: prorrateo_remuneracion_basica,
									prorrateo_condiciones_trabajo: prorrateo_condiciones_trabajo,
									remuneracion_basica: remuneracion_basica,
									remuneracion_real: currentValue.remuneracion_real,
									bonificaci: bonificaci,
									comisiones: comisiones,
									des_siste_pensiones: des_siste_pensiones,
									adelantoss: adelantoss,
									descFaltas: descFaltas,
									tardanzas: tardanzas,
									descRiesCj: descRiesCj,
									descCredit: descCredit,
									descuOtros: descuOtros,
									essalud: essalud,
									total_neto: total_neto,
									TotalmasComisiones: TotalmasComisiones,
									totaldescInicial: totaldescInicial,
									mes: mes,
									anio: anio,
									agen: agen,
								};

								self.frmDatosPlanilla.datos_planilla.push(objeto);
							});

							$("#tblPlantilla").DataTable().destroy();
							self.TablaPlantilla();
						}
					});
			}
		},

		Insertar(e) {
			self = this;
			let index = e.target.name;
			let column = e.target.id.substr(0, 10);
			if (column == "riesgoCaja") {
				self.frmDatosPlanilla.datos_planilla[index].riesgoCaja = e.target.value;
				let comisiones = self.frmDatosPlanilla.datos_planilla[index].comisiones;
				let bonificaciones =
					self.frmDatosPlanilla.datos_planilla[index].bonificaci;

				comisiones = comisiones == null ? 0 : comisiones;
				bonificaciones = bonificaciones == null ? 0 : bonificaciones;

				self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones =
					parseFloat(self.usuarios_filtros[index]["remuneracion_real"]) +
					parseFloat(comisiones) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].riesgoCaja) +
					parseFloat(bonificaciones);

				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) -
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					);
			} else if (column == "bonificaci") {
				self.frmDatosPlanilla.datos_planilla[index].bonificaci = e.target.value;

				let comisiones = self.frmDatosPlanilla.datos_planilla[index].comisiones;
				let riesgoCaja = self.frmDatosPlanilla.datos_planilla[index].riesgoCaja;

				comisiones = comisiones == null ? 0 : comisiones;
				riesgoCaja = riesgoCaja == null ? 0 : riesgoCaja;

				self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones =
					parseFloat(self.usuarios_filtros[index]["remuneracion_real"]) +
					parseFloat(comisiones) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].bonificaci) +
					parseFloat(riesgoCaja);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) -
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					);
			} else if (column == "comisiones") {
				self.frmDatosPlanilla.datos_planilla[index].comisiones = e.target.value;

				let riesgoCaja = self.frmDatosPlanilla.datos_planilla[index].riesgoCaja;
				let bonificaciones =
					self.frmDatosPlanilla.datos_planilla[index].bonificaci;

				riesgoCaja = riesgoCaja == null ? 0 : riesgoCaja;
				bonificaciones = bonificaciones == null ? 0 : bonificaciones;

				self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones =
					parseFloat(self.usuarios_filtros[index]["remuneracion_real"]) +
					parseFloat(bonificaciones) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].comisiones) +
					parseFloat(riesgoCaja);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) -
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					);
			} else if (column == "adelantoss") {
				self.frmDatosPlanilla.datos_planilla[index].adelantoss = e.target.value;
				self.frmDatosPlanilla.datos_planilla[index].descRiesCj =
					self.frmDatosPlanilla.datos_planilla[index].descRiesCj == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descRiesCj;
				self.frmDatosPlanilla.datos_planilla[index].descCredit =
					self.frmDatosPlanilla.datos_planilla[index].descCredit == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descCredit;
				self.frmDatosPlanilla.datos_planilla[index].descuOtros =
					self.frmDatosPlanilla.datos_planilla[index].descuOtros == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descuOtros;

				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantoss) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descRiesCj) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descCredit) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descuOtros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].remuneracion_real
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "descRiesCj") {
				self.frmDatosPlanilla.datos_planilla[index].descRiesCj = e.target.value;

				self.frmDatosPlanilla.datos_planilla[index].adelantoss =
					self.frmDatosPlanilla.datos_planilla[index].adelantoss == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].adelantoss;
				self.frmDatosPlanilla.datos_planilla[index].descCredit =
					self.frmDatosPlanilla.datos_planilla[index].descCredit == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descCredit;
				self.frmDatosPlanilla.datos_planilla[index].descuOtros =
					self.frmDatosPlanilla.datos_planilla[index].descuOtros == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descuOtros;

				// self.frmDatosPlanilla.datos_planilla[index].Totaldesc=parseFloat(self.frmDatosPlanilla.datos_planilla[index].totaldescInicial)+parseFloat(self.frmDatosPlanilla.datos_planilla[index].descRiesCj);
				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantoss) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descRiesCj) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descCredit) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descuOtros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].remuneracion_real
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "descCredit") {
				self.frmDatosPlanilla.datos_planilla[index].descCredit = e.target.value;
				self.frmDatosPlanilla.datos_planilla[index].descRiesCj =
					self.frmDatosPlanilla.datos_planilla[index].descRiesCj == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descRiesCj;
				self.frmDatosPlanilla.datos_planilla[index].adelantoss =
					self.frmDatosPlanilla.datos_planilla[index].adelantoss == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].adelantoss;
				self.frmDatosPlanilla.datos_planilla[index].descuOtros =
					self.frmDatosPlanilla.datos_planilla[index].descuOtros == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descuOtros;

				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantoss) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descRiesCj) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descCredit) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descuOtros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].remuneracion_real
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "descuOtros") {
				self.frmDatosPlanilla.datos_planilla[index].descuOtros = e.target.value;
				self.frmDatosPlanilla.datos_planilla[index].descRiesCj =
					self.frmDatosPlanilla.datos_planilla[index].descRiesCj == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descRiesCj;
				self.frmDatosPlanilla.datos_planilla[index].descCredit =
					self.frmDatosPlanilla.datos_planilla[index].descCredit == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].descCredit;
				self.frmDatosPlanilla.datos_planilla[index].adelantoss =
					self.frmDatosPlanilla.datos_planilla[index].adelantoss == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].adelantoss;

				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantoss) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descRiesCj) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descCredit) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].descuOtros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].remuneracion_real
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			}
		},

		diasComputables(fecha) {
			var f = new Date();
			let hoy = f.getFullYear() + "-" + (f.getMonth() + 1) + "-" + f.getDate();
			// let mes = f.getMonth() + 1;

			let fechaInicio = new Date(fecha).getTime();
			let fechaFin = new Date(hoy).getTime();

			let diferencia = fechaFin - fechaInicio;

			let dias_acumulado = diferencia / (1000 * 60 * 60 * 24);
			dias_acumulado = dias_acumulado.toFixed(0);

			if (dias_acumulado <= 30) {
				return dias_acumulado;
			} else {
				return 30;
			}
		},
		RegistrarPlanilla() {
			let self = this;
			Swal.fire({
				title: "REGISTRAR PLANILLA",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					self.frmDatosPlanilla.datos_planilla.forEach(function callback(
						currentValue,
						index
					) {
						axios.post(route("gth.pla.planilla_general.guardar"), currentValue);
					});

					Swal.fire({
						icon: "success",
						title: "¡ÉXITO!",
						text: "Planilla registrada",
						allowOutsideClick: false,
					}).then(function (response) {
						self.submited = false;
						self.frmDatosPlanilla.mes = 0;
						self.frmDatosPlanilla.año = 0;
						self.frmDatosPlanilla.id_agencia = 0;
						self.frmDatosPlanilla.datos_planilla = [];
						$("#tblPlantilla").DataTable().destroy();
						self.TablaPlantilla();
					});
				},
			});
		},
		CancelarRegistro() {
			this.submited = false;
			this.frmDatosPlanilla.datos_planilla = [];
			this.frmDatosPlanilla.mes = 0;
			this.frmDatosPlanilla.año = 0;
			this.frmDatosPlanilla.id_agencia = 0;
			this.usuarios_filtros = [];
			$("#tblPlantilla").DataTable().destroy();
			this.TablaPlantilla();
		},
	},
};
</script>

<style>
.slot-planilla-general {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-planilla-general {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>






