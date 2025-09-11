<template>
	<voucher
		ref="voucher"
		:concepto="concepto"
		:fecha_hora_actual="fecha_hora_actual"
		id="vchAdelanto"
	>
		<div slot="voucher-content">
			<table width="100%" id="tblDetalleAdelanto">
				<tbody>
					<!-- <tr class="border-top bg-gray">
            <td class="bolder" width="100px !important">
              N° Operación
            </td>
            <td align="right">{{ datos_adelanto.comision_pago_id }}</td>
          </tr> -->
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">COLABORADOR</td>
					</tr>
					<tr class="border-top">
						<td class="bolder" colspan="2" align="right">
							{{ datos_adelanto.colaborador }}
						</td>
					</tr>
					<tr class="border-top bg-gray">
						<td class="bolder" colspan="2">CONCEPTO</td>
					</tr>
					<tr class="border-top">
						<td align="right" colspan="2">
							{{ datos_adelanto.concepto }}
						</td>
					</tr>
					<tr class="border-top border-bottom bg-gray">
						<td align="right" colspan="2">S/ {{ datos_adelanto.monto }}</td>
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
		return { concepto: null, datos_adelanto: {}, fecha_hora_actual: null };
	},
};
</script>

<style lang="css" >
#vchAdelanto {
	display: none;
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
