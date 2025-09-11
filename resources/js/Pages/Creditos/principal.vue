<template>
	<layout ref="layout" :agencias="agencias">
		<div class="slot_body" slot="component-view">
			<!-- <button class="btn btn-action btn-icon-split" @click="facturar()">
				FACTURAR
			</button> -->
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
export default {
	props: { agencias: Array },
	components: { layout },
	methods: {
		// facturar() {
		// 	Swal.fire({
		// 		icon: "question",
		// 		text: "¿Desea FACTURAR esta operación?",
		// 		confirmButtonText:
		// 			'<i class="fas fa-check" style="color:white;"></i>   Si',
		// 		confirmButtonColor: "var(--colorAlto)",
		// 		showCancelButton: true,
		// 		cancelButtonText: '<i class="fas fa-times"></i>   No',
		// 		cancelButtonColor: "var(--plomoOscuroEmpresarial)",
		// 		allowOutsideClick: false,
		// 	}).then((result) => {
		// 		if (result.isConfirmed) {
		// 			let data = new FormData();
		// 			this.$inertia.post(route("caj.desembolso.facturar_todos"), data, {
		// 				preserveScroll: true,
		// 				onStart: () => {
		// 					Swal.fire({
		// 						title: "FACTURANDO",
		// 						text: "Espere porfavor...",
		// 						showConfirmButton: false,
		// 						allowOutsideClick: false,
		// 						willOpen: () => {
		// 							Swal.showLoading();
		// 						},
		// 					});
		// 				},
		// 				onSuccess: () => {
		// 					return Swal.fire({
		// 						icon: "success",
		// 						title: "ÉXITO!",
		// 						timer: 1200,
		// 						showConfirmButton: false,
		// 					});
		// 				},
		// 			});
		// 		}
		// 	});
		// },
	},
};
</script>

<style lang="css">
</style>
