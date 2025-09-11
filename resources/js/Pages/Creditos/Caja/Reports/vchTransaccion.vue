<template>
	<voucher
		ref="voucher"
		:concepto="concepto"
		:fecha_hora_actual="fecha_hora_actual"
		id="vchTransaccion"
	>
		<div slot="voucher-content">
			<table width="100%" id="tblDetalleTrasnsaccion">
				<tbody>
					<!-- <tr class="border-top bg-gray">
            <td class="bolder" width="100px !important">
              N° Operación
            </td>
            <td align="right">{{ datos_transaccion.comision_pago_id }}</td>
          </tr> -->
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">TIPO</td>
					</tr>
					<tr class="border-top">
						<td class="bolder" colspan="2" align="right">
							{{ datos_transaccion.tipo }}
						</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">CONCEPTO</td>
					</tr>
					<tr class="border-top">
						<td align="right" colspan="2">
							{{ datos_transaccion.concepto }}
						</td>
					</tr>
					<tr class="border-top border-bottom bg-gray">
						<td align="right" colspan="2">S/ {{ datos_transaccion.monto }}</td>
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
		return { concepto: null, datos_transaccion: {}, fecha_hora_actual: null };
	},
};
</script>

<style lang="css" >
#vchTransaccion {
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
