<template>
	<layout ref="layout">
		<div class="slot_body slot-facturacion-dia" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'SEGUIMIENTO DEL DÍA'"></headerClose>
					<div class="card-title">PROGRESO ACTUAL</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-5 col-6">
								<label class="label-title">AGENCIA</label>
								<select
									class="form-control center mayus"
									v-model="agencia_seleccionada"
								>
									<option
										v-for="(item, index) in agencias_permitidas"
										:key="index"
										:value="item.id"
									>
										{{ item.agencia }}
									</option>
								</select>
							</div>

							<div
								class="form-group col-md-4 offset-md-3 col-6"
								:style="windowWidth < 900 ? 'offset: none' : ''"
							>
								<label class="label-title">FECHA</label>
								<input
									type="text"
									class="form-control center"
									:value="fecha_actual"
									readonly
								/>
							</div>
						</div>

						<br />
						<div class="form-row">
							<div class="col-md-6">
								<label class="label-title"
									>Total Facturado: S/ {{ roundTo(total_facturado, 2) }}</label
								>
							</div>
							<div class="col-md-6 text-right">
								<label class="label-title"
									>Límite máx: S/ {{ roundTo(limite_maximo, 2) }}</label
								>
							</div>
						</div>
						<div class="progress">
							<div
								class="progress-bar progress-bar-striped progress-bar-animated"
								role="progressbar"
								:aria-valuenow="total_facturado"
								:aria-valuemin="limite_minimo"
								:aria-valuemax="limite_maximo"
								:style="progreso"
							>
								{{ roundTo((total_facturado / limite_maximo) * 100, 2) }}
								%
							</div>
						</div>
						<br />
						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								type="button"
								title="Actualizar"
								@click="CalcularSeguimiento(agencia_seleccionada)"
							>
								<span class="icon text-white">
									<i class="fas fa-sync-alt"></i>
								</span>
								<span class="text">ACTUALIZAR</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

export default {
	components: {
		layout,
		headerClose,
	},
	props: {},

	data() {
		return {
			agencia_seleccionada: 0,
			agencias: [],
			agencias_permitidas: [],
			total_facturado: 0,
			limite_minimo: 0,
			limite_maximo: 0,
			fecha_actual: 0,
			progreso: 0,

			windowWidth: window.innerWidth,
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
				} else {
					this.agencia_seleccionada = 0;
				}
			}
		},
		agencia_seleccionada(value) {
			this.CalcularSeguimiento(value);
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.ListarAgenciasPermitidas();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_HERRAMIENTAS/FACTURACION_DIA"
			);
		},

		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		CalcularSeguimiento(agencia_id) {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", agencia_id);

			// this.$inertia.post(route("her.facturacion_dia.calcular"), data);

			axios
				.post(route("her.facturacion_dia.calcular"), data)
				.then(function (response) {
					self.limite_maximo = response.data.limite_maximo;
					self.total_facturado = response.data.total_facturado;
					self.fecha_actual = response.data.fecha_actual;

					if (self.limite_maximo == 0 && self.total_facturado == 0) {
						self.progreso = "width: 0%";
					} else {
						self.progreso =
							"width: " +
							(self.total_facturado / self.limite_maximo) * 100 +
							"%";
					}
				});
		},
	},
};
</script>

<style lang="css">
.slot-facturacion-dia {
	width: 30% !important;
	margin-left: 35% !important;
}

@media (max-width: 900px) {
	.slot-facturacion-dia {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
