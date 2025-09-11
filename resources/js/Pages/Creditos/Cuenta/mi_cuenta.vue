<template>
	<layout ref="layout">
		<div
			class="slot_body slot_mi_cuenta"
			slot="component-view"
			v-if="this.datos_cuenta.con_cuenta == 1"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MI CUENTA'"></headerClose>

					<mdlDetalleCuenta :tipo="'vista'" ref="mdlDetalleCuenta">
					</mdlDetalleCuenta>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";

import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import mdlDetalleCuenta from "@/Pages/Creditos/Cuenta/Components/mdlDetalleCuenta.vue";

export default {
	components: { layout, headerClose, mdlDetalleCuenta },
	props: {
		agencia_id: Number,
		tu_cuenta: Object,
	},
	data() {
		return {
			datos_cuenta: this.tu_cuenta,
		};
	},

	mounted() {
		if (this.datos_cuenta.con_cuenta == 0) {
			Swal.fire({
				title: "¡Ups!",
				text: "Usted no tiene una cuenta aperturada",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
				preConfirm: (result) => {
					this.$inertia.get(route("cre.index"));
				},
			});

			return false;
		}

		let mdlDetalleCuenta = this.$refs.mdlDetalleCuenta;
		mdlDetalleCuenta.detalle_cuenta = this.tu_cuenta;
	},
};
</script>

<style lang="css">
.slot_mi_cuenta {
	width: 50% !important;
	margin-left: 25% !important;
}

@media only screen and (max-width: 900px) {
	.slot_mi_cuenta {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>


