<template>
	<div class="bottom" id="report_footer">
		<slot name="sign-content"></slot>
		<h3 style="font-size: 11px">
			{{ fecha_hora_actual + " - " + equipo + " - " + usuario }}
		</h3>
	</div>
</template>

<script>
export default {
	props: { fecha_hora_actual: String },
	computed: {
		equipo() {
			return this.$page.props.user_session.dispositivo.nombre;
		},
		usuario() {
			return this.$page.props.user_session.usuario;
		},
	},
};
</script>

<style lang="css">
.bottom {
	width: 100%;
}
#report_footer {
	position: fixed;
	bottom: 0;
}
</style>
