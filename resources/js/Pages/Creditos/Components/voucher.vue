<template>
	<div
		style="
			position: relative;
			width: 300px !important;
			height: 500px !important;
			-webkit-print-color-adjust: exact;
		"
		id="voucher"
	>
		<div class="text-center">
			<p style="margin-bottom: 0px">CORPORACIÓN CREDIPYME</p>
			<p style="margin-bottom: 0px">RUC: 20601618282</p>
			<p style="margin-bottom: 0px">
				{{ concepto }}
			</p>
		</div>
		<br />

		<div>
			<slot name="voucher-content"></slot>
		</div>

		<div class="bottom text-center">
			<p style="margin-bottom: 0px">¡ GRACIAS POR SU PREFERENCIA !</p>
			<p style="margin-bottom: 0px">AGENCIA - {{ agencia }}</p>

			<!-- <p style=" margin-bottom: 0px">
        {{ frase }}
      </p> -->
			<p style="margin-bottom: 0px">
				{{ fecha_hora_actual }}
			</p>
			<p style="margin-bottom: 0px">{{ equipo }} - {{ usuario }}</p>
		</div>
	</div>

	<!-- -----FIN IMPRIMIBLE ---------- -->
</template>

<script>
export default {
	props: { concepto: String, fecha_hora_actual: String },
	data() {
		return {};
	},
	computed: {
		frase() {
			return "Lo único imposible es aquello que no intentas...";
		},
		agencia() {
			return this.$page.props.user_session.nombre_agencia;
		},
		equipo() {
			return this.$page.props.user_session.dispositivo.nombre;
		},
		usuario() {
			return this.$page.props.user_session.usuario;
		},
	},
};
</script>

<style lang="css" >
#voucher {
	size: auto;
	padding: 4mm !important;
	font-family: Verdana !important;
	font-size: 16px !important;
}
/* .bg-gray {
  background: rgb(228, 228, 228) !important;
  -webkit-print-color-adjust: exact;
} */
.bottom {
	position: absolute;
	width: 98% !important;
	left: 1% !important;
	bottom: 0;
}
</style>
