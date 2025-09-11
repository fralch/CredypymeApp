<template>
	<layout ref="layout">
		<div class="slot_body slot-cierre-dia" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<div class="card-header">
						<strong>CIERRE DE DÍA</strong>
					</div>
					<div class="card-title">INFORMACIÓN</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-3">
								<label
									class="form-control-label label-title"
									for="inpFechaActual"
									>FECHA ACTUAL</label
								>
								<input
									id="inpFechaActual"
									class="form-control center"
									type="text"
									:disabled="true"
									:value="fecha_actual"
								/>
							</div>
						</div>
						<div class="text-center">
							<div class="btn-group" role="group">
								<button
									class="btn btn-action btn-icon-split"
									id="btnCerrarDia"
									@click="CerrarDia"
								>
									<span class="icon text-white">
										<i class="far fa-calendar-plus"></i>
									</span>
									<span class="text">CERRAR DÍA</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
export default {
	components: {
		layout,
	},
	data() {
		return {
			fecha_actual: null,
		};
	},
	watch: {},
	mounted() {
		this.ActualizarFecha();
	},
	methods: {
		CerrarDia() {
			self = this;
			Swal.fire({
				title: "CERRAR DÍA",
				text: "¿Desea continuar?",
				confirmButtonText: '<i class="fas fa-check"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					this.$inertia.post(
						route("gth.man.cierre_dia_gth.cerrar"),
						{},
						{
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "TRABAJANDO",
									html: "Espere porfavor...",
									timer: 5000,
									allowOutsideClick: false,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading();
										timerInterval = setInterval(() => {
											const content = Swal.getContent();
											if (content) {
												const b = content.querySelector("b");
												if (b) {
													b.textContent = Swal.getTimerLeft();
												}
											}
										}, 100);
									},
									willClose: () => {
										clearInterval(timerInterval);
									},
								});
							},
							onSuccess: () => {
								Swal.fire({
									icon: "success",
									title: "¡ÉXITO!",
									text: "Día cerrado",
									allowOutsideClick: false,
								}).then(function (response) {
									self.ActualizarFecha();
								});
							},
						}
					);
				},
			});
		},
		ActualizarFecha() {
			let parts = this.$page.props.application.data
				.filter((item) => item.descripcion == "FECHA_GTH")[0]
				.valorFecha.split("-");

			let options = {
				weekday: "long",
				year: "numeric",
				month: "long",
				day: "numeric",
			};

			let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

			this.fecha_actual = date.toLocaleDateString("es-ES", options);
		},
	},
};
</script>

<style >
.slot-cierre-dia {
	width: 60% !important;
	margin-left: 20% !important;
}

@media (max-width: 900px) {
	.slot-cierre-dia {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
