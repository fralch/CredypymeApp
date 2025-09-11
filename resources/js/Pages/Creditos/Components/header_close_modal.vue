<template>
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong>{{ titulo_modal }}</strong>
		<button
			type="button"
			class="btn btn-green"
			style="border-radius: 50%"
			@click="CerrarModal(nombre_modal)"
		>
			<span class="icon text-white">
				<i class="fas fa-times"></i>
			</span>
		</button>
	</div>
</template>

<script>
export default {
	props: {
		titulo_modal: String,
		nombre_modal: String,
	},

	methods: {
		CerrarModal(nombre_modal) {
			$("#" + nombre_modal).css("display", "none");
		},
	},
};
</script>

<style lang="css"></style>
