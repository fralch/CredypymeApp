<template>
	<div id="mdlSimuladorRiesgo" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-30 mdlSimuladorRiesgo">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						ref="headerCloseModal"
						:titulo_modal="'SIMULADOR DE RIESGO CREDITICIO'"
						:nombre_modal="'mdlSimuladorRiesgo'"
					>
					</headerCloseModal>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-6 offset-md-3">
								<label class="label-title">MONTO</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span
											class="input-group-text prepend-title"
											style="font-size: 24px !important"
											>S/</span
										>
									</div>
									<input
										type="number"
										min="0.1"
										step="100"
										lang="es"
										class="form-control text-center"
										style="
											height: 45px;
											font-size: 24px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
										v-model="monto"
										@change="Redondear"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>

							<div
								class="form-group mt-3 col-md-5"
								:style="
									windowWidth >= 900
										? 'text-align: right'
										: 'text-align: center'
								"
							>
								<button
									class="btn btn-action btn-icon-split mt-3"
									@click="Calcular"
								>
									<span class="icon text-white">
										<i class="fas fa-calculator"></i>
									</span>
									<span class="text">CALCULAR</span>
								</button>
							</div>
							<div class="form-group text-center mt-3 col-md-6 offset-md-1">
								<div class="input-group">
									<div class="input-group-prepend">
										<span
											class="input-group-text prepend-title"
											style="font-size: 15px !important"
											>%</span
										>
									</div>
									<input
										type="text"
										class="form-control text-center"
										style="
											max-width: 150px;
											font-size: 15px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
										:value="porcentaje_riesgo"
										disabled
									/>
								</div>
								<div class="input-group mt-1">
									<div class="input-group-prepend">
										<span
											class="input-group-text prepend-title"
											style="font-size: 15px !important"
											>S/</span
										>
									</div>
									<input
										type="text"
										class="form-control text-center"
										style="
											max-width: 150px;
											font-size: 15px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
										:value="monto_riesgo"
										disabled
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: { headerCloseModal },
	data() {
		return {
			windowWidth: window.innerWidth,

			monto: this.roundTo(0, 2),
			porcentaje_riesgo: this.roundTo(0, 2),
			monto_riesgo: this.roundTo(0, 2),
			rangos: [],
		};
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		hidenav() {
			this.$parent.hide_nav();
		},

		shownav() {
			this.$parent.show_nav();
		},

		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			this.monto = this.roundTo(valor, numero_decimales);
		},
		Resetear() {
			this.monto = this.roundTo(0, 2);
			this.porcentaje_riesgo = this.roundTo(0, 2);
			this.monto_riesgo = this.roundTo(0, 2);
		},
		async ListarRangos() {
			let self = this;
			// this.$inertia.post(route("her.simulador_riesgo.rangos"));

			return await axios
				.post(route("her.simulador_riesgo.rangos"))
				.then(function (response) {
					self.rangos = response.data.rango_comisiones;
				});
		},
		Calcular() {
			let self = this;
			this.porcentaje_riesgo = this.roundTo(0, 2);
			this.monto_riesgo = this.roundTo(0, 2);

			this.rangos.forEach((element) => {
				if (
					self.monto >= parseFloat(element.monto_desde) &&
					self.monto <= parseFloat(element.monto_hasta)
				) {
					self.porcentaje_riesgo = self.roundTo(
						parseFloat(element.monto_cobrar),
						2
					);
					self.monto_riesgo = self.roundTo(
						(parseFloat(element.monto_cobrar) / 100) * parseFloat(self.monto),
						1
					);
				}
			});
		},
	},
};
</script>

<style lang="css">
.mdlSimuladorRiesgo {
	margin-top: 8%;
}

@media (max-width: 900px) {
	.mdlSimuladorRiesgo {
		width: 98% !important;
		margin-left: 1% !important;

		margin-top: 15% !important;
	}
}
</style>

