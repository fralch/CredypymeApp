<template>
	<voucher
		ref="voucher"
		:concepto="concepto"
		:fecha_hora_actual="fecha_hora_actual"
		id="vchMovimiento"
		class="height:550px !important"
	>
		<div slot="voucher-content">
			<table width="100%" id="tblDetalleApertura">
				<tbody>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">CLIENTE:</td>
					</tr>
					<tr class="border-top">
						<td class="bolder" colspan="2" align="right">
							{{ datos_movimiento.cliente }}
						</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">
							PRODUCTO META - {{ datos_movimiento.producto }}
						</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder" align="left">CONCEPTO</td>
						<td class="bolder" align="right">IMPORTE</td>
					</tr>
					<tr class="border-top">
						<td align="left">
							{{ datos_movimiento.tipo }}
						</td>
						<td align="right">
							S/
							{{
								(datos_movimiento.tipo == "DEPÓSITO" ? "" : "- ") +
								roundTo(datos_movimiento.monto, 2)
							}}
						</td>
					</tr>
					<tr class="border" style="height: 40px">
						<td class="border" width="70%"></td>
						<td></td>
					</tr>
					<tr style="height: 30px">
						<td class="bolder">Apellidos y Nombres:</td>
					</tr>
					<tr style="height: 20px"></tr>
					<tr class="border-top">
						<td class="bolder" colspan="2">DNI:</td>
					</tr>
					<tr>
						<td class="bolder">Comentario:</td>
					</tr>
					<tr class="border" style="height: 40px; font-size: 9px">
						<td colspan="2" rowspan="2" style="font-size: 9px">
							{{ datos_movimiento.comentario }}
						</td>
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
		return { concepto: null, datos_movimiento: {}, fecha_hora_actual: null };
	},

	computed: {},

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
#vchMovimiento {
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
	height: 30px;
}

.border {
	border: 1px solid var(--plomoOscuroEmpresarial) !important;
}
</style>
