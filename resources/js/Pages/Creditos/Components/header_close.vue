<template>
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong>{{ title }}</strong>
		<inertia-link
			:href="$route('cre.index')"
			style="float: right !important"
			id="btnSalir"
		>
			<button type="button" class="btn btn-green" style="border-radius: 50%">
				<span class="icon text-white">
					<i class="fas fa-times"></i>
				</span>
			</button>
		</inertia-link>
	</div>
</template>

<script>
export default {
	props: {
		title: String,
	},
};
</script>

<style lang="css">
</style>
