<template>
	<voucher
		ref="voucher"
		:concepto="titulo"
		:fecha_hora_actual="fecha_hora_actual"
		id="vchApertura"
	>
		<div slot="voucher-content">
			<table width="100%" id="tblDetalleApertura">
				<tbody>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">CLIENTE:</td>
					</tr>
					<tr class="border-top">
						<td class="bolder" colspan="2" align="right">
							{{ datos_apertura.cliente }}
						</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">FECHA DE APERTURA:</td>
					</tr>
					<tr class="border-top">
						<td align="right" colspan="2">
							{{ datos_apertura.fecha_apertura }}
						</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">FECHA DE CIERRE:</td>
					</tr>
					<tr v-if="datos_apertura.fecha_cierre != null" class="border-top">
						<td align="right" colspan="2">
							{{ datos_apertura.fecha_cierre }}
						</td>
					</tr>
					<tr v-else class="border-top">
						<td align="right" colspan="2">Indefinido</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder">TASA DE INTERÉS:</td>
						<td v-if="datos_apertura.tasa_interes != 0" align="right">
							{{ roundTo(datos_apertura.tasa_interes, 2) + " %" }}
						</td>
						<td v-else align="right">0.00 %</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder">CAPITAL:</td>
						<td v-if="datos_apertura.capital != 0" align="right">
							{{ "S/ " + roundTo(datos_apertura.capital, 2) }}
						</td>
						<td v-else align="right">S/ 0.00</td>
					</tr>
				</tbody>
			</table>
		</div>
	</voucher>
</template>

<script>
import voucher from "@/Pages/Creditos/Components/voucher.vue";
export default {
	components: { voucher },
	data() {
		return { concepto: null, datos_apertura: {}, fecha_hora_actual: null };
	},

	computed: {
		titulo() {
			return "APERTURA INVERSIÓN - " + this.concepto;
		},
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
	},
};
</script>

<style lang="css" >
#vchApertura {
	display: none;
	color: black !important;
	font-weight: bolder;
}
#tblDetalleComision {
	font-size: 15px !important;
}

.bg-gray {
	background: rgb(240, 240, 240) !important;
	-webkit-print-color-adjust: exact;
}

.border-top {
	border-top: 1px solid var(--plomoOscuroEmpresarial) !important;
	height: 30px;
}
.border-bottom {
	border-bottom: 1px solid var(--plomoOscuroEmpresarial) !important;
}
</style>
