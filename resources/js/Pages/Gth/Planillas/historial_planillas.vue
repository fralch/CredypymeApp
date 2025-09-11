<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-planillas" slot="component-view">
			<div class="card">
				<headerClose :title="'HISTORIAL PLANILLA GENERAL'"></headerClose>

				<div class="card-title">PANEL DE BÚSQUEDA</div>

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
								class="btn btn-action btn-icon-split"
								id="btnCargarPlanilla"
								title="Cargar planilla"
								@click="CargarPlanilla"
								:disabled="frmDatosPlanilla.datos_planilla.length > 0"
							>
								<span class="icon text-white">
									<i class="fas fa-chevron-circle-down"></i>
								</span>
								<span class="text">CARGAR</span>
							</button>
							<button
								class="btn btn-cancel btn-icon-split"
								id="btnCancelar"
								title="Cancelar edición"
								@click="CancelarEdicion"
								:disabled="frmDatosPlanilla.datos_planilla.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
								<span class="text">CANCELAR</span>
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
					<!-- ----------tabla general------------- -->
					<table class="table table-hover" id="tblDatosPlanilla" width="100%">
						<thead>
							<tr>
								<th>COLABORADOR</th>
								<th>EDITAR</th>
								<th>AGENCIA</th>
								<th>DÍAS_COMPUTABLES</th>
								<th>DÍAS_VACACIONES</th>
								<th>DÍAS_FALTAS</th>
								<th>DÍAS_NO_LAB.</th>
								<th>REM._VAC.</th>
								<th>
									PROR._REM.<br />
									BÁSICA
								</th>
								<th>PROR._COND._TRABAJO</th>
								<th>RIESGO_CAJA</th>
								<th>BONIF.</th>
								<th>COMIS.</th>
								<th>TOTAL_REM.<br />REAL</th>
								<th>TOTAL_REM._COMPUTABLE</th>
								<th>DSCTO_SIS._PENS.</th>
								<th>ADELANTOS</th>
								<th>DSCTO_FALTAS</th>
								<th>DSCTO_TARDANZAS</th>
								<th>DSCTO_RIESGO CAJA</th>
								<th>DSCTO_CRÉDITOS</th>
								<th>DSCTO_OTROS</th>
								<th>TOTAL_DSCTOS</th>
								<th>TOTAL_NETO</th>
								<th>ESSALUD</th>
								<th>GUARDAR</th>
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
									<div class="text-center">
										<div class="btn-group" role="group">
											<button
												class="btn btn-action btn-icon-split"
												:id="'btnEditar_' + index"
												title="Editar planilla"
												@click="HabilitarEdicion(index)"
												style="z-index: 0"
											>
												<span class="icon text-white">
													<i class="fas fa-edit"></i>
												</span>
											</button>
										</div>
									</div>
								</td>

								<td class="table-bordered" align="center">
									{{ dato_planilla.agencia }}
								</td>
								<td class="table-bordered" align="center">
									{{ roundTo(dato_planilla.dias_computables, 2) }}
								</td>
								<td class="table-bordered" align="center">
									{{ roundTo(dato_planilla.dias_vacaciones, 2) }}
								</td>
								<td class="table-bordered" align="center">
									{{ roundTo(dato_planilla.dias_faltas, 2) }}
								</td>
								<td class="table-bordered" align="center">
									{{ roundTo(dato_planilla.dias_no_laborados, 2) }}
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
								<td class="table-bordered" align="right">
									<input
										type="number"
										class="form-control input-table"
										placeholder="S/"
										min="0.00"
										step="0.01"
										lang="en"
										:id="'riesgoCaja_' + index"
										:value="roundTo(dato_planilla.riesgo_caja, 2)"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
										:id="'bonificaci_' + index"
										:value="roundTo(dato_planilla.bonificaciones, 2)"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
										:id="'comisiones_' + index"
										:value="roundTo(dato_planilla.comisiones, 2)"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
										:value="roundTo(dato_planilla.adelantos, 2)"
										:id="'adelantoss_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
										:value="roundTo(dato_planilla.desc_otros_riesgo_caja, 2)"
										:id="'descRiesCj_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
										:value="roundTo(dato_planilla.desc_otros_credito, 2)"
										:id="'descCredit_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
										:value="roundTo(dato_planilla.desc_otros, 2)"
										:id="'descuOtros_' + index"
										:name="index"
										@change="Insertar"
										@focus="hidenav()"
										@blur="shownav()"
										readonly
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
								<td class="table-bordered" align="center">
									<button
										class="btn btn-action btn-icon-split mb-1"
										:id="'btnGuardar' + index"
										@click="Guardar(index), DeshabilitarEdicion(index)"
										disabled
									>
										<span class="text">GUARDAR</span>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
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
			frmDatosPlanilla: {
				mes: 0,
				año: 0,
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
		// self = this;
		this.TablaDatosPlanilla();
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblDatosPlanilla").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblDatosPlanilla")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblDatosPlanilla").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblDatosPlanilla")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		TablaDatosPlanilla() {
			this.$nextTick(() => {
				var table = $("#tblDatosPlanilla").DataTable({
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

		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				var power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		CargarPlanilla() {
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
					.post(route("gth.pla.historial_planillas.listar"), {
						anio: año,
						mes: mes,
						id_agencia: id_agencia,
					})
					.then(function (response) {
						if (response.data.length === 0) {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "La planilla de esta fecha no está generada, intente con otra fecha",
								allowOutsideClick: false,
							});
						}
						self.usuarios_filtros = response.data;
						self.frmDatosPlanilla.datos_planilla = response.data;

						self.frmDatosPlanilla.datos_planilla.forEach(function (
							element,
							index
						) {
							// pushear nuevos elementos a objetos de un array
							element.historial = 1;
							element.mes = self.frmDatosPlanilla.mes;
							element.anio = self.frmDatosPlanilla.año;
							element.agen = self.frmDatosPlanilla.id_agencia;

							element.totaldescInicial = element.total_descuentos;
							element.TotalmasComisiones = element.total_remuneracion_real;

							element.Totaldesc = element.total_descuentos;
						});

						$("#tblDatosPlanilla").DataTable().destroy();
						self.TablaDatosPlanilla();
					});
			}
		},
		Insertar(e) {
			let self = this;
			let index = e.target.name;
			let column = e.target.id.substr(0, 10);

			if (column == "riesgoCaja") {
				self.frmDatosPlanilla.datos_planilla[index].riesgo_caja =
					e.target.value;
				let comisiones = self.frmDatosPlanilla.datos_planilla[index].comisiones;
				let bonificaciones =
					self.frmDatosPlanilla.datos_planilla[index].bonificaciones;

				comisiones = comisiones == null ? 0 : comisiones;
				bonificaciones = bonificaciones == null ? 0 : bonificaciones;

				self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones =
					parseFloat(self.usuarios_filtros[index].remuneracion_real) +
					parseFloat(comisiones) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].riesgo_caja) +
					parseFloat(bonificaciones);

				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "bonificaci") {
				self.frmDatosPlanilla.datos_planilla[index].bonificaciones =
					e.target.value;

				let comisiones = self.frmDatosPlanilla.datos_planilla[index].comisiones;
				let riesgo_caja =
					self.frmDatosPlanilla.datos_planilla[index].riesgo_caja;

				comisiones = comisiones == null ? 0 : comisiones;
				riesgo_caja = riesgo_caja == null ? 0 : riesgo_caja;

				self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones =
					parseFloat(self.usuarios_filtros[index].remuneracion_real) +
					parseFloat(comisiones) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].bonificaciones
					) +
					parseFloat(riesgo_caja);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "comisiones") {
				self.frmDatosPlanilla.datos_planilla[index].comisiones = e.target.value;

				let riesgo_caja =
					self.frmDatosPlanilla.datos_planilla[index].riesgo_caja;
				let bonificaciones =
					self.frmDatosPlanilla.datos_planilla[index].bonificaciones;

				riesgo_caja = riesgo_caja == null ? 0 : riesgo_caja;
				bonificaciones = bonificaciones == null ? 0 : bonificaciones;

				self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones =
					parseFloat(self.usuarios_filtros[index].remuneracion_real) +
					parseFloat(bonificaciones) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].comisiones) +
					parseFloat(riesgo_caja);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "adelantoss") {
				self.frmDatosPlanilla.datos_planilla[index].adelantos = e.target.value;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja ==
					null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index]
								.desc_otros_riesgo_caja;

				self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito;

				self.frmDatosPlanilla.datos_planilla[index].desc_otros =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].desc_otros;

				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantos) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja
					) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].desc_otros);

				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "descRiesCj") {
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja =
					e.target.value;

				self.frmDatosPlanilla.datos_planilla[index].adelantos =
					self.frmDatosPlanilla.datos_planilla[index].adelantos == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].adelantos;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].desc_otros;

				// self.frmDatosPlanilla.datos_planilla[index].Totaldesc=parseFloat(self.frmDatosPlanilla.datos_planilla[index].totaldescInicial)+parseFloat(self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja);
				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantos) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja
					) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].desc_otros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "descCredit") {
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito =
					e.target.value;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja ==
					null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index]
								.desc_otros_riesgo_caja;
				self.frmDatosPlanilla.datos_planilla[index].adelantos =
					self.frmDatosPlanilla.datos_planilla[index].adelantos == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].adelantos;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].desc_otros;

				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantos) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja
					) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].desc_otros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			} else if (column == "descuOtros") {
				self.frmDatosPlanilla.datos_planilla[index].desc_otros = e.target.value;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja ==
					null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index]
								.desc_otros_riesgo_caja;
				self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito =
					self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito;
				self.frmDatosPlanilla.datos_planilla[index].adelantos =
					self.frmDatosPlanilla.datos_planilla[index].adelantos == null
						? 0
						: self.frmDatosPlanilla.datos_planilla[index].adelantos;

				self.frmDatosPlanilla.datos_planilla[index].Totaldesc =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].totaldescInicial
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].adelantos) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_riesgo_caja
					) +
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].desc_otros_credito
					) +
					parseFloat(self.frmDatosPlanilla.datos_planilla[index].desc_otros);
				self.frmDatosPlanilla.datos_planilla[index].total_neto =
					parseFloat(
						self.frmDatosPlanilla.datos_planilla[index].TotalmasComisiones
					) - parseFloat(self.frmDatosPlanilla.datos_planilla[index].Totaldesc);
			}
		},

		// -------------

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
		Guardar(index) {
			let self = this;

			Swal.fire({
				title: "GUARDAR CAMBIOS",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					axios
						.post(
							route("gth.pla.historial_planillas.guardar"),
							self.frmDatosPlanilla.datos_planilla[index]
						)
						.then(function (response) {
							Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								allowOutsideClick: false,
							});
						});
				},
			});
		},

		DeshabilitarEdicion(index) {
			$("#btnEditar_" + index).prop("disabled", false);

			$("#riesgoCaja_" + index).prop("readonly", true);
			$("#bonificaci_" + index).prop("readonly", true);
			$("#comisiones_" + index).prop("readonly", true);
			$("#adelantoss_" + index).prop("readonly", true);
			$("#descRiesCj_" + index).prop("readonly", true);
			$("#descCredit_" + index).prop("readonly", true);
			$("#descuOtros_" + index).prop("readonly", true);
			$("#btnGuardar" + index).prop("disabled", true);
		},

		HabilitarEdicion(index) {
			$("#btnEditar_" + index).prop("disabled", true);

			$("#riesgoCaja_" + index).prop("readonly", false);
			$("#bonificaci_" + index).prop("readonly", false);
			$("#comisiones_" + index).prop("readonly", false);
			$("#adelantoss_" + index).prop("readonly", false);
			$("#descRiesCj_" + index).prop("readonly", false);
			$("#descCredit_" + index).prop("readonly", false);
			$("#descuOtros_" + index).prop("readonly", false);
			$("#btnGuardar" + index).prop("disabled", false);
		},
		CancelarEdicion() {
			this.submited = false;
			this.frmDatosPlanilla.mes = 0;
			this.frmDatosPlanilla.año = 0;
			this.frmDatosPlanilla.id_agencia = 0;
			this.frmDatosPlanilla.datos_planilla = [];

			$("#tblDatosPlanilla").DataTable().destroy();
			self.TablaDatosPlanilla();
		},
	},
};
</script>

<style>
.modal {
	padding: 0 !important;
}

.modal .modal-content {
	height: 95%;
	width: 100% !important;
	border: 0;
	border-radius: 0;
	margin: 0;
	max-width: none;
}
.modal .modal-body {
	overflow-y: auto;
}
.modal .modal-footer {
	position: absolute;
}
.slot-historial-planillas {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-historial-planillas {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>






