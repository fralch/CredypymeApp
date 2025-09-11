<template>
	<!-- ------ AREA IMPRIMIBLE --------- -->
	<div id="rptCierreCaja" class="report-container">
		<br />
		<h1 align="left" style="font-size: 20px; color: black; font-weight: bolder">
			CIERRE DE OPERACIONES DIARIAS
		</h1>
		<hr />
		<div v-if="caja_usuario != null">
			<h2
				align="left"
				style="font-size: 15px; color: black; font-weight: bolder"
			>
				Usuario: {{ caja_usuario }}
			</h2>
			<hr />
			<div style="width: 50%; display: inline-block">
				<p>
					<b>Fecha apertura de sistema: {{ f_apertura_sistema }}</b>
				</p>
				<p>
					<b>Fecha cierre de sistema: {{ f_cierre_sistema }}</b>
				</p>
			</div>
			<div style="width: 50%; float: right; display: inline-block">
				<p>Fecha apertura de real: {{ f_apertura_real }}</p>
				<p>Fecha cierre de real: {{ f_cierre_real }}</p>
			</div>
		</div>

		<table style="width: 100%">
			<thead class="header-style with-border" align="center">
				<tr>
					<th>OPERACIÓN</th>
					<th style="width: 100px">INGRESOS</th>
					<th style="width: 100px">EGRESOS</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in datos_operacion" :key="index">
					<td align="center" class="with-border">{{ item.concepto }}</td>
					<td align="right" class="with-border">
						{{ roundTo(item.ingresos, 2) }}
					</td>
					<td align="right" class="with-border">
						{{ roundTo(item.egresos, 2) }}
					</td>
					<!-- <td align="center" class="with-border">{{ item.fecha_pago }}</td>
					<td align="center" class="with-border">{{ item.orden }}</td>
					<td align="right" class="with-border">
						{{ roundTo(item.monto_cuota, 2) }}
					</td> -->
				</tr>
			</tbody>
		</table>
		<table style="width: 100%">
			<tr>
				<td align="center" class="with-border">SUBTOTALES</td>
				<td width="100px" align="right" class="with-border">
					{{ roundTo(total_ingresos, 2) }}
				</td>
				<td width="100px" align="right" class="with-border">
					{{ roundTo(total_egresos, 2) }}
				</td>
			</tr>
		</table>
		<br />
		<table style="width: 100%">
			<tr>
				<td align="center" width="150px" class="with-border bolder">
					TOTAL EFECTIVO EN CAJA S/
					{{ roundTo(total_ingresos - total_egresos, 2) }}
				</td>
			</tr>
		</table>
		<br />

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
			f_apertura_sistema: null,
			f_cierre_sistema: null,
			f_apertura_real: null,
			f_cierre_real: null,
			efectivo_caja: null,
			total_ingresos: null,
			total_egresos: null,
			caja_usuario: null,
			fecha_hora_actual: null,
		};
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
#rptCierreCaja {
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
}
</style>
