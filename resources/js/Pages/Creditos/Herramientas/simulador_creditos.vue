<template>
	<layout ref="layout">
		<div class="slot_body slot-simulador" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'SIMULADOR DE CRÉDITOS'"></headerClose>
					<div class="card-title">INFORMACIÓN DE PROPUESTA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-row col-md-7" style="max-height: 250px">
								<div class="col-md-12 col-12">
									<label class="label-title">MONTO</label>
									<input
										type="number"
										class="form-control center"
										min="0"
										step="0.01"
										lang="en"
										style="
											height: 50px;
											font-size: 30px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
										name="monto"
										v-model.number="frmPropuesta.monto"
										@change="Redondear"
									/>
								</div>
								<div class="col-md-6 col-6">
									<label class="label-title">TIPO</label>
									<span
										v-if="submited && !$v.frmPropuesta.tipo_id.noZero"
										class="span-error-message"
										>*</span
									>
									<select
										class="form-control"
										v-model.number="frmPropuesta.tipo_id"
									>
										<option :value="0" disabled>Seleccione...</option>
										<option
											v-for="(item, index) in tipos"
											:key="index"
											:value="item.id"
										>
											{{ item.tipo }}
										</option>
									</select>
								</div>
								<div class="col-md-6 col-6">
									<label class="label-title">PERIODO PAGO</label>
									<select
										class="form-control"
										v-model="frmPropuesta.periodo_pago"
										@change="
											datos_calendario = [];
											datos_cuota = [];
											frmPropuesta.cuota = 0.0;
										"
									>
										<option value="DIARIO" selected>DIARIO</option>
										<option value="SEMANAL">SEMANAL</option>
										<option value="QUINCENAL">QUINCENAL</option>
										<option value="PAGO_UNICO">PAGO ÚNICO</option>
										<option value="MENSUAL">MENSUAL</option>
									</select>
								</div>
								<div class="col-md-5 offset-md-1 col-4 offset-2">
									<label class="label-title" v-if="windowWidth >= 900"
										>PLAZO
										{{ periodo_medicion(frmPropuesta.periodo_pago) }}</label
									>
									<label class="label-title" v-if="windowWidth < 900"
										>PL.
										{{ periodo_medicion(frmPropuesta.periodo_pago) }}</label
									>

									<input
										type="number"
										class="form-control center"
										min="1"
										step="1"
										lang="en"
										name="plazo"
										v-model.number="frmPropuesta.plazo"
										@change="Redondear"
										style="
											font-size: 15px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
									/>
								</div>
								<div class="col-md-6 col-6">
									<label class="label-title">TASA INTERÉS (%)</label>
									<span
										v-if="
											submited &&
											(!$v.frmPropuesta.tasa_interes.required ||
												!$v.frmPropuesta.tasa_interes.noZero)
										"
										class="span-error-message"
										>*</span
									>
									<input
										type="number"
										class="form-control center"
										min="0"
										step="0.1"
										lang="en"
										name="tasa_interes"
										v-model.number="frmPropuesta.tasa_interes"
										@change="Redondear"
										style="
											font-size: 15px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
									/>
								</div>
							</div>

							<div class="form-group col-md-5" style="height: 280px">
								<div class="text-center">
									<button
										class="btn btn-action btn-icon-split mt-3"
										title="Genera calendario de pagos"
										@click="CalcularCronograma"
									>
										<span class="icon text-white">
											<i class="far fa-calendar-alt"></i
										></span>
										<span class="text">GENERAR CALENDARIO</span>
									</button>
								</div>
								<table
									class="table"
									id="tblCronograma"
									style="width: 100% !important"
								>
									<thead>
										<tr>
											<th>CUOTA</th>
											<th>FECHA_PAGO</th>
											<th>DÍA</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in datos_calendario"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td
												class="table-bordered"
												align="center"
												width="20px !important"
											>
												{{ item.orden }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.fecha_pago }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.dia_pago }}
											</td>
										</tr>
									</tbody>
								</table>
								<div class="col-md-12" style="position: absolute; bottom: 0">
									<label class="label-title"
										>CUOTA:
										<span
											style="
												font-size: 18px;
												font-weight: bolder;
												color: var(--colorAlto);
											"
											>S/ {{ this.roundTo(frmPropuesta.cuota, 2) }}</span
										>
									</label>
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

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		agencia_id: Number,
		tipos: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,

			datos_cuota: [],
			datos_calendario: [],
			submited: false,
			frmPropuesta: {
				monto: "500.00",
				sector_id: null,
				producto_id: null,
				subproducto_id: null,
				tipo_id: 0,
				periodo_pago: "DIARIO",
				plazo: 30,
				tasa_interes: "6.00",
				forma_pago: null,
				garantia_id: null,
				promotor_id: null,
				valor_garantia: null,
				cuota: null,
				fecha_propuesta: null,
				comentario_garantia: null,
				comentario_propuesta: null,
			},
		};
	},
	validations: {
		frmPropuesta: {
			monto: { required, noZero },
			plazo: { required, noZero },
			tasa_interes: { required, noZero },
		},
	},
	mounted() {
		let fecha_actual = this.$inertia.page.props.application.data_local.filter(
			(item) => item.descripcion == "FECHA_CREDITOS"
		)[0].valor_fecha;

		this.frmPropuesta.fecha_propuesta = fecha_actual;
		this.TablaCronograma();

		// this.ListarAgenciasPermitidas();

		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;

		// });
	},
	watch: {
		datos_cuota() {
			$("#tblCronograma").DataTable().destroy();
			this.TablaCronograma();
		},
	},
	methods: {
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			if (e.target.name == "monto") {
				this.frmPropuesta.monto = this.roundTo(valor, numero_decimales);
			} else if (e.target.name == "plazo") {
				this.frmPropuesta.plazo = this.roundTo(valor, 0);
			} else if (e.target.name == "tasa_interes") {
				this.frmPropuesta.tasa_interes = this.roundTo(valor, numero_decimales);
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

		TablaCronograma() {
			this.$nextTick(() => {
				var table = $("#tblCronograma").DataTable({
					scrollY: "150px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
						info: false,
					},
					language: {
						// select: {
						//   rows: "%d fila seleecionada",
						// },
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
			});
		},

		async CalcularCronograma() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_id);
			data.append("monto", this.frmPropuesta.monto);
			data.append("plazo", this.frmPropuesta.plazo);
			data.append("tasa_interes", this.frmPropuesta.tasa_interes);
			data.append("periodo_pago", this.frmPropuesta.periodo_pago);
			data.append("fecha_desembolso", this.frmPropuesta.fecha_propuesta);

			// this.$inertia.post(route("cre.calcular_cronograma.sin_redondeo"), data);
			// return false;

			await axios
				.post(route("cre.calcular_cronograma.sin_redondeo"), data)
				.then((response) => {
					this.datos_cuota = response.data.datos_cuota;
					this.frmPropuesta.cuota = response.data.datos_cuota.monto_cuota;
					this.datos_calendario = response.data.datos_calendario;
				});
		},
	},
};
</script>

<style lang="css">
.slot-simulador {
	width: 50% !important;
	margin-left: 25% !important;
	margin-top: 5% !important;
}

@media (max-width: 1280px) {
	.slot-simulador {
		width: 45% !important;
		margin-left: 27.5% !important;
	}
}
@media (max-width: 900px) {
	.slot-simulador {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

