<template>
	<!-- ------ AREA IMPRIMIBLE --------- -->
	<div id="rptBilleteo" class="report-container">
		<br />
		<h1 align="left" style="font-size: 20px; color: black; font-weight: bolder">
			BILLETEO
		</h1>
		<hr />
		<h2 align="left" style="font-size: 15px; color: black; font-weight: bolder">
			Usuario: {{ caja_usuario }}
		</h2>
		<hr />

		<table style="width: 100%">
			<thead class="header-style with-border" align="center">
				<tr>
					<th style="width: 200px"></th>
					<th style="width: 100px">CANTIDAD</th>
					<th style="width: 100px">MONTO EQUIVALENTE</th>
					<th style="width: 100px">MONTO ACUMULADO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center" class="with-border">1 céntimo</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.un_centimo }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.un_centimo * 0.01, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.un_centimo, 2) }}
					</td>
				</tr>
				<tr>
					<td align="center" class="with-border">10 céntimos</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.diez_centimos }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.diez_centimos * 0.1, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.diez_centimos, 2) }}
					</td>
				</tr>
				<tr>
					<td align="center" class="with-border">20 céntimos</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.veinte_centimos }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.veinte_centimos * 0.2, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.veinte_centimos, 2) }}
					</td>
				</tr>
				<tr>
					<td align="center" class="with-border">50 céntimos</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.cincuenta_centimos }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.cincuenta_centimos * 0.5, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.cincuenta_centimos, 2) }}
					</td>
				</tr>
				<tr style="background-color: #f7f7f7">
					<td align="center" class="with-border">1 sol</td>
					<td align="center" class="with-border">{{ frmBilleteo.un_sol }}</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.un_sol * 1, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.un_sol, 2) }}
					</td>
				</tr>
				<tr style="background-color: #f7f7f7">
					<td align="center" class="with-border">2 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.dos_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.dos_soles * 2, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.dos_soles, 2) }}
					</td>
				</tr>
				<tr style="background-color: #f7f7f7">
					<td align="center" class="with-border">5 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.cinco_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.cinco_soles * 5, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.cinco_soles, 2) }}
					</td>
				</tr>
				<tr>
					<td align="center" class="with-border">10 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.diez_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.diez_soles * 10, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.diez_soles, 2) }}
					</td>
				</tr>
				<tr>
					<td align="center" class="with-border">20 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.veinte_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.veinte_soles * 20, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.veinte_soles, 2) }}
					</td>
				</tr>
				<tr>
					<td align="center" class="with-border">50 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.cincuenta_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.cincuenta_soles * 50, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.cincuenta_soles, 2) }}
					</td>
				</tr>
				<tr style="background-color: #f7f7f7">
					<td align="center" class="with-border">100 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.cien_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.cien_soles * 100, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.cien_soles, 2) }}
					</td>
				</tr>
				<tr style="background-color: #f7f7f7">
					<td align="center" class="with-border">200 soles</td>
					<td align="center" class="with-border">
						{{ frmBilleteo.doscientos_soles }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(frmBilleteo.doscientos_soles * 200, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(acumulado.doscientos_soles, 2) }}
					</td>
				</tr>
			</tbody>
		</table>

		<br />
		<p>
			Monto total
			<b style="font-size: 20px"
				>S/ {{ roundTo(frmBilleteo.total_billeteo, 2) }}</b
			>
		</p>

		<reportFooter :fecha_hora_actual="fecha_hora_actual"></reportFooter>
		<br />
	</div>

	<!-- -----FIN IMPRIMIBLE ---------- -->
</template>

<script>
import reportFooter from "@/Pages/Creditos/Components/report_footer.vue";
export default {
	components: { reportFooter },
	data() {
		return {
			datos_operacion: [],
			caja_usuario: null,
			frmBilleteo: {},
			fecha_hora_actual: null,
			acumulado: {},
		};
	},
	watch: {
		frmBilleteo() {
			let self = this;
			async function creando_acumulado() {
				self.acumulado.un_centimo = self.frmBilleteo.un_centimo * 0.01;
				self.acumulado.diez_centimos =
					self.frmBilleteo.diez_centimos * 0.1 + self.acumulado.un_centimo;
				self.acumulado.veinte_centimos =
					self.frmBilleteo.veinte_centimos * 0.2 + self.acumulado.diez_centimos;
				self.acumulado.cincuenta_centimos =
					self.frmBilleteo.cincuenta_centimos * 0.5 +
					self.acumulado.veinte_centimos;
				self.acumulado.un_sol =
					self.frmBilleteo.un_sol * 1 + self.acumulado.cincuenta_centimos;
				self.acumulado.dos_soles =
					self.frmBilleteo.dos_soles * 2 + self.acumulado.un_sol;
				self.acumulado.cinco_soles =
					self.frmBilleteo.cinco_soles * 5 + self.acumulado.dos_soles;
				self.acumulado.diez_soles =
					self.frmBilleteo.diez_soles * 10 + self.acumulado.cinco_soles;
				self.acumulado.veinte_soles =
					self.frmBilleteo.veinte_soles * 20 + self.acumulado.diez_soles;
				self.acumulado.cincuenta_soles =
					self.frmBilleteo.cincuenta_soles * 50 + self.acumulado.veinte_soles;
				self.acumulado.cien_soles =
					self.frmBilleteo.cien_soles * 100 + self.acumulado.cincuenta_soles;
				self.acumulado.doscientos_soles =
					self.frmBilleteo.doscientos_soles * 200 + self.acumulado.cien_soles;
			}
			creando_acumulado().then(() => {});
		},
	},
	mounted() {
		this.fechas();
	},
	methods: {
		fechas() {
			let parts = this.$page.props.application.data_local
				.filter((item) => item.descripcion == "FECHA_CREDITOS")[0]
				.valor_fecha.split("-");

			let options = {
				weekday: "long",
				year: "numeric",
				month: "long",
				day: "numeric",
			};

			let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

			this.fecha_hora_actual = date.toLocaleDateString("es-ES", options);
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		isEmpty(value) {
			if (value === "" || value === null || value === undefined) {
				return "_____";
			} else {
				return value;
			}
		},
	},
};
</script>

<style lang="css" >
#rptBilleteo {
	padding: 10mm !important;
	width: 100% !important;
	position: absolute;
	display: none;
}

.report-container {
	position: relative;
	width: 100% !important;
	height: 100% !important;
}

/* .bottom {
	width: 100%;
	position: absolute !important;
	bottom: 0 !important;
} */

.line-sign {
	width: 50%;
	margin-left: 25%;
	background-color: black;
	-webkit-print-color-adjust: exact;
}

.with-border {
	padding-left: 5px !important;
	padding-right: 5px !important;
	padding-top: 1px !important;
	padding-bottom: 1px !important;
	border: 0.2px dotted rgb(211, 208, 208) !important;
	/* font-size: 11px !important; */
}

.indent {
	text-indent: 15px !important;
	color: black !important;
}

.header-style {
	background: rgb(228, 228, 228) !important;
	-webkit-print-color-adjust: exact;
}

.table-title {
	padding-left: 5px !important;
	padding-right: 5px !important;
	padding-top: 3px !important;
	padding-bottom: 3px !important;
	margin-bottom: 1rem;
	background: black;
	color: white;
	font-weight: bolder;
	font-size: 17px;
	-webkit-print-color-adjust: exact;
}
.table-sub-title {
	padding-left: 5px !important;
	padding-right: 5px !important;
	padding-top: 3px !important;
	padding-bottom: 3px !important;
	margin-bottom: 1rem;
	background: rgb(252, 248, 192);
	color: black;
	font-weight: bolder;
	-webkit-print-color-adjust: exact;
}
</style>
