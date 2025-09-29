<template>
	<div>
		<div class="collapse" id="multiCollapseExample1">
			<div
				class="card-body card-block"
				style="
					background-color: white !important ;
					padding-bottom: 0px !important;
					padding-right: 0px !important;
					padding-left: 0px !important;
					border-radius: 10px !important;
				"
				:style="
					windowWidth >= 900
						? ' background-color: white !important ; padding-bottom: 0px !important;padding-right: 0px !important;padding-left: 0px !important;border-radius: 10px !important;'
						: 'background-color: white !important ; padding-bottom: 0px !important;padding-right: 10px !important;padding-left: 0px !important;border-radius: 10px !important;'
				"
			>
				<div class="form-row">
					<div
						class="form-group text-center col-md-8 offset-2"
						style="padding-right: 0px !important"
						v-if="windowWidth >= 900"
					>
						<label
							class="label-title"
							style="font-size: 25px !important; margin-left: 5px !important"
							>¡Sí se puede!</label
						>
					</div>

					<div
						class="form-group text-center col-9 offset-1"
						style="padding-right: 0px !important"
						v-if="windowWidth < 900"
					>
						<label
							class="label-title"
							style="font-size: 18px !important; margin-left: 22px !important"
							>¡Sí se puede!</label
						>
					</div>

					<div
						class="col-md-2 col-2 text-center"
						style="padding-right: 0px !important"
					>
						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarChart()"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>
				</div>

				<div class="text-center">
					<div class="form-group col-md-12" v-if="datos == 'Si'">
						<label class="label-title" style="font-size: 12px !important"
							>Clientes activos:
						</label>
						<span
							class="label"
							style="
								font-size: 13px;
								color: var(--colorAlto);
								font-weight: bold;
							"
						>
							{{ lista_resumen.cliente_activos }}</span
						>
					</div>
					<div class="form-group col-md-12" v-if="datos == 'Si'">
						<label class="label-title" style="font-size: 12px !important"
							>Cantidad de créditos:
						</label>
						<span
							class="label"
							style="
								font-size: 13px;
								color: var(--colorAlto);
								font-weight: bold;
							"
						>
							{{ lista_resumen.cantidad_creditos }}</span
						>
					</div>
					<div class="form-group col-md-12" v-if="datos == 'Si'">
						<label class="label-title" style="font-size: 12px !important"
							>Saldo capital:
						</label>
						<span
							class="label"
							style="
								font-size: 13px;
								color: var(--colorAlto);
								font-weight: bold;
							"
						>
							S/ {{ roundTo(lista_resumen.saldo_capital_total, 2) }}</span
						>
					</div>
				</div>
				<div class="chart form-group">
					<canvas id="myChart"></canvas>
				</div>
			</div>
		</div>

		<div
			id="button"
			type="button"
			data-toggle="collapse"
			href="#multiCollapseExample1"
			data-target="#multiCollapseExample1"
			aria-expanded="false"
			@click="buscar_mora_resumen"
		>
			<a><i class="fa fa-chart-line" style="font-size: 30px"></i></a>
		</div>
	</div>
</template>

<script>
import * as jqueryUI from "@/assets/vendors/ChartJS/jquery-ui.min.js";
import * as jqueryUITouch from "@/assets/vendors/ChartJS/jquery.ui.touch-punch.min.js";
import * as chart from "@/assets/vendors/ChartJS/chart.js";
import * as chartDatalabel from "@/assets/vendors/ChartJS/chartjs-plugin-datalabels.min.js";

var prueba = null;
export default {
	props: { windowWidth: Number },
	data() {
		return {
			datos: null,

			cargo: this.$page.props.user_session.nombre_cargo,
		};
	},
	mounted() {
		if (this.windowWidth >= 900) {
			var rect = button.getBoundingClientRect();

			var left = rect.left;
			var top = rect.top;

			$("#multiCollapseExample1").css({
				left: left - 275,
				top: top - 410,
			});
		}

		if (
			this.cargo == "ASESOR DE NEGOCIOS" ||
			this.cargo == "JEFE DE CRÉDITOS" ||
			this.cargo == "COORDINADOR DE CRÉDITOS" ||
			this.cargo == "RECUPERADOR DE CRÉDITOS"
		) {
			this.buscar_mora_resumen();
			$("#button").show();
		} else {
			if (this.windowWidth > 900) {
				$("#multiCollapseExample1").css({
					left: left - 275,
					top: top - 55,
				});
			} else {
				$("#multiCollapseExample1").css({
					left: left - 110,
					top: top - 55,
				});
			}
			this.lista_resumen = [];

			this.datos = "No";
			$(".chart").hide();
			this.mostrar_chart();
		}

		window.addEventListener("resize", () => {
			var rect = button.getBoundingClientRect();
			var rect2 = multiCollapseExample1.getBoundingClientRect();

			var left = rect.left;
			var top = rect.top;

			if (this.datos == "Si") {
				if (left > 0) {
					$("#multiCollapseExample1").css({
						left: left - 275,
						top: top - 410,
					});
				}

				if (left == 0) {
					$("#multiCollapseExample1").css({
						left: this.windowWidth - 385,
						top: 473,
					});
				}
			}
			if (this.datos == "No") {
				if (left > 0) {
					$("#multiCollapseExample1").css({
						left: left - 275,
						top: top - 55,
					});
				}

				if (left == 0) {
					$("#multiCollapseExample1").css({
						left: this.windowWidth - 385,
						top: 828,
					});
				}
			}
		});
	},
	methods: {
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

		buscar_mora_resumen() {
			let self = this;

			var rect = button.getBoundingClientRect();

			var left = rect.left;
			var top = rect.top;

			let data = new FormData();

			//   this.$inertia.post(route("home.mora_resumen"), data);
			//   return false;

			axios.post(route("home.mora_resumen"), data).then(function (response) {
				if (response.data.lista_resumen.cantidad_creditos == 0) {
					if (self.windowWidth > 900) {
						$("#multiCollapseExample1").css({
							left: left - 275,
							top: top - 55,
						});
					} else {
						$("#multiCollapseExample1").css({
							left: left - 110,
							top: top - 55,
						});
					}
					self.lista_resumen = [];

					self.datos = "No";
					$(".chart").hide();
					self.mostrar_chart();
				} else {
					self.lista_resumen = response.data.lista_resumen;
					self.datos = "Si";
					self.mostrar_chart();
				}
			});
			$("#button").hide();
		},

		mostrar_chart() {
			let saldo_capital_normal = parseFloat(
				this.lista_resumen.saldo_capital_normal
			).toLocaleString("es-PE", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
			let saldo_capital_cpp = parseFloat(
				this.lista_resumen.saldo_capital_cpp
			).toLocaleString("es-PE", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
			let saldo_capital_deficiente = parseFloat(
				this.lista_resumen.saldo_capital_deficiente
			).toLocaleString("es-PE", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
			let saldo_capital_dudoso = parseFloat(
				this.lista_resumen.saldo_capital_dudoso
			).toLocaleString("es-PE", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
			let saldo_capital_perdida = parseFloat(
				this.lista_resumen.saldo_capital_perdida
			).toLocaleString("es-PE", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
			let saldo_capital_perdida_total = parseFloat(
				this.lista_resumen.saldo_capital_perdida_total
			).toLocaleString("es-PE", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});

			const saldoarray = [
				saldo_capital_normal,
				saldo_capital_cpp,
				saldo_capital_deficiente,
				saldo_capital_dudoso,
				saldo_capital_perdida,
				saldo_capital_perdida_total,
			];

			const creditosarray = [
				this.lista_resumen.normal,
				this.lista_resumen.cpp,
				this.lista_resumen.deficiente,
				this.lista_resumen.dudoso,
				this.lista_resumen.perdida,
				this.lista_resumen.perdida_total,
			];

			const data = {
				labels: [
					"Normal",
					"Cpp",
					"Deficiente",
					"Dudoso",
					"Pérdida",
					"Pérdida total",
				],
				datasets: [
					{
						data: [
							this.lista_resumen.porcentaje_normal,
							this.lista_resumen.porcentaje_cpp,
							this.lista_resumen.porcentaje_deficiente,
							this.lista_resumen.porcentaje_dudoso,
							this.lista_resumen.porcentaje_perdida,
							this.lista_resumen.porcentaje_perdida_total,
						],

						backgroundColor: [
							"rgba(120, 197, 255)",
							"rgba(144,221 , 144)",
							"rgba(255, 255, 0)",
							"rgba(255, 216, 177)",
							"rgba(255, 204, 203)",
							"rgba(130, 0, 0)",
						],
						borderColor: [
							"rgba(0, 0, 255)",
							"rgba(0, 128, 0)",
							"rgba(233, 189, 21)",
							"rgba(255, 128, 0)",
							"rgba(255, 0, 0)",
							"rgba(114, 47, 55)",
						],
					},
				],
			};

			const config = {
				type: "doughnut",
				data: data,
				options: {
					plugins: {
						tooltip: {
							titleAlign: "center",
							borderWidth: 0,
							titleFont: {
								size: 17,
							},
							footerFont: {
								size: 15,
							},

							bodyFont: {
								size: 15,
								weight: "bold",
							},
							cornerRadius: 3,
							padding: {
								top: 20,
								bottom: 20,
								left: 20,
								right: 20,
							},
							callbacks: {
								title: function (context) {
									return `${context[0].label}`;
								},
								label: (context) => {
									// console.log(context);
									return `Porcentaje: ${context.raw}%`;
								},

								beforeFooter: function (context) {
									return `Saldo: S/ ${saldoarray[context[0].dataIndex]}`;
								},
								afterFooter: function (context) {
									return `Créditos: ${creditosarray[context[0].dataIndex]}`;
								},
							},
						},
						datalabels: {
							display: function (context) {
								return context.dataset.data[context.dataIndex] !== 0;
							},
						},

						legend: {
							position: "bottom",
						},
					},
				},
			};

			if (prueba != null) {
				prueba.destroy();
			}
			var ctx = document.getElementById("myChart");

			var myChart = new Chart(ctx, config);

			prueba = myChart;
		},
		async CerrarChart() {
			await $("#button").click();
			if (prueba != null) {
				prueba.destroy();
			}

			$("#button").show();
		},
	},
};
</script>

<style lang="css">
#multiCollapseExample1 {
	width: 330px !important;

	position: absolute;
}

#button {
	width: 53px;
	height: 53px;
	border-radius: 50px;

	background: var(--colorAlto);
	border-bottom: var(--colorAlto);
	box-shadow: 6px 6px 6px #999;
	color: #fff;

	padding-top: 9px;

	text-align: center;
	position: absolute;
	bottom: 3.5%;
	right: 3.5%;
}
#button:hover {
	/* background-color: #89bd29; */
	background: var(--colorMedio);
}

#button:active {
	box-shadow: 2px 2px 2px #777, 0px 0px 35px 0px #feffff;
	border-bottom: 1px solid #0045a6;
	text-shadow: 0px 0px 5px #fff, 0px 0px 5px #fff;
}
@media only screen and (max-width: 1127px) {
	#button {
		width: 53px;
		height: 53px;
		border-radius: 50px;

		background: #244b9a;
		border-bottom: 5px solid #0045a6;
		box-shadow: 6px 6px 6px #999;
		color: #fff;

		padding-top: 9px;

		text-align: center;
		position: absolute;
		bottom: 3.5%;
		right: 3.5%;
	}

	#multiCollapseExample1 {
		width: 330px !important;

		position: absolute;
	}
}
@media only screen and (max-width: 900px) {
	#button {
		width: 53px;
		height: 53px;
		border-radius: 50px;

		background: #244b9a;
		border-bottom: 5px solid #0045a6;
		box-shadow: 6px 6px 6px #999;
		color: #fff;

		padding-top: 9px;

		text-align: center;
		position: absolute;
		bottom: 7%;
		left: 42%;
	}

	#multiCollapseExample1 {
		width: 270px !important;
		height: 280px !important;

		position: absolute;
		float: right;

		bottom: 21%;
		left: 17%;
	}
}
</style>
