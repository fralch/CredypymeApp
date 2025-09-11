<template>
	<!-- ------ AREA IMPRIMIBLE --------- -->
	<div id="AreaImprimible">
		<div
			style="
				width: 54mm !important;
				height: 73mm;
				font-size: 13px;
				margin-top: 5mm;
				margin-left: 15mm;
			"
		>
			<div class="text-center">
				<p style="font-weight: bold; margin-bottom: 0px; font-size: 14px">
					CREDIPYMESOLUCIÓN
				</p>
				<p style="font-weight: bold; margin-bottom: 0px; font-size: 14px">
					RUC:20601618282
				</p>
				<p style="font-weight: bold; margin-bottom: 0px; font-size: 12px">
					RECEP. DE DINERO DE AGENCIA
				</p>
			</div>
			<div class="text-left">
				<p style="font-weight: bold; margin-bottom: 0px">FECHA DE EMISIÓN</p>
				<hr style="margin: 0px" color="black" size="3" />
				<p style="margin-bottom: 0px">{{ fecha }}</p>
				<hr style="margin: 0px" color="black" size="3" />
				<p style="font-weight: bold; margin-bottom: 0px">CONCEPTO</p>
				<hr style="margin: 0px" color="black" size="3" />
				<p style="margin-bottom: 0px">
					Recepcion de Agencia {{ agencia_destino }} de {{ remitente }} a
					{{ destinatario }}
				</p>
				<hr style="margin: 0px" color="black" size="3" />
			</div>
			<div class="text-right">
				<p style="margin-bottom: 0px">S/.{{ monto }}</p>
				<hr style="margin: 0px" />
			</div>
			<div class="text-center">
				<p style="font-weight: bold; margin-bottom: 0px">
					AGENCIA {{ agencia_recepcion }}
				</p>
				<p style="font-weight: bold; margin-bottom: 0px">
					*** GRACIAS POR SU PREFERENCIA ***
				</p>
				<p style="font-weight: bold; margin-bottom: 0px">
					Lo unico imposible es aquello que no intentas...
				</p>
				<p style="font-weight: bold; margin-bottom: 0px">
					{{ nombre_dispositivo }} - {{ remitente }}
				</p>
			</div>
		</div>
	</div>
	<!-- -----FIN IMPRIMIBLE ---------- -->
</template>

<script>
export default {
	props: {},
	data() {
		return {
			fecha: null,
			agencia_destino: null,
			remitente: null,
			destinatario: null,
			monto: null,
			agencia_recepcion: null,
			nombre_dispositivo: null,
		};
	},
};
</script>

<style lang="css">
#AreaImprimible {
	position: absolute;
	z-index: -5;
}
@page {
	size: portrait;
}
</style>
