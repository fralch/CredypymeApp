<template>
	<layout ref="layout">
		<div
			class="slot_body slot_apertura_caja"
			slot="component-view"
			v-if="this.mi_cuenta != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'APERTURA DE CAJA'"></headerClose>
					<div class="card-title">INFORMACIÓN</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-6">
								<label class="label-title">FECHA</label>
								<input
									class="form-control text-center"
									type="text"
									:value="fecha_apertura"
									:disabled="true"
								/>
							</div>

							<div class="form-group col-md-6">
								<label class="label-title" for="text-input">USUARIO</label>
								<input
									class="form-control text-center"
									type="text"
									:value="
										mi_usuario.usuario + ' - ' + mi_usuario.nombre_agencia
									"
									:disabled="true"
								/>
							</div>

							<div class="form-group col-md-6">
								<label class="label-title">MONTO</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">S/</span>
									</div>
									<input
										type="number"
										min="0"
										:max="mi_cuenta.monto"
										step="1"
										lang="en"
										v-model.number="frmDatosApertura.monto"
										class="form-control text-center"
										style="
											height: 45px;
											font-size: 24px;
											font-weight: bolder;
											color: var(--colorAlto);
										"
										@change="Redondear"
										@focus="hidenav()"
										@blur="shownav()"
										:disabled="mi_caja != null"
									/>
								</div>
							</div>

							<div class="form-group col-md-6">
								<label class="label-title">NOTAS</label>
								<textarea
									type="text"
									rows="2"
									class="form-control mayus text-row"
									v-model="frmDatosApertura.notas"
									@focus="hidenav()"
									@blur="shownav()"
									:disabled="mi_caja != null"
								></textarea>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split bolder"
								style="font-size: 12px"
								@click="Aperturar"
								v-if="mi_caja == null"
							>
								<span class="icon"> S/ </span>
								<span class="text">APERTURAR</span>
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
	components: { layout, headerClose },

	data() {
		return {
			fecha_apertura: null,
			frmDatosApertura: {
				cuenta_id: null,
				monto: 0.0,
				notas: null,
			},
		};
	},
	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		mi_cuenta() {
			return this.$inertia.page.props.creditos_datos.datos_cuenta;
		},
		mi_usuario() {
			return this.$inertia.page.props.user_session;
		},
	},
	mounted() {
		if (this.mi_cuenta == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Usted no tiene una cuenta asignada.",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			let parts = "";
			this.frmDatosApertura.cuenta_id = this.mi_cuenta.id;
			if (this.mi_caja != null) {
				this.fecha_apertura = JSON.parse(this.mi_caja.datos_apertura).fecha;
				this.frmDatosApertura.monto = parseFloat(
					this.mi_caja.monto_apertura
				).toFixed(2);
				this.frmDatosApertura.notas = this.mi_caja.comentario_apertura;
			} else {
				parts = this.$page.props.application.data_local
					.filter((item) => item.descripcion == "FECHA_CREDITOS")[0]
					.valor_fecha.split("-");
				let options = {
					weekday: "long",
					year: "numeric",
					month: "long",
					day: "numeric",
				};

				let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

				this.fecha_apertura = date.toLocaleDateString("es-ES", options);
				this.frmDatosApertura.monto = parseFloat(this.mi_cuenta.monto).toFixed(
					2
				);
				this.frmDatosApertura.notas = "";
			}
		}
	},
	methods: {
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			this.frmDatosApertura.monto = this.$refs.layout.round(
				valor,
				numero_decimales
			);
		},

		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},

		Aperturar() {
			let self = this;

			if (
				parseFloat(this.frmDatosApertura.monto) >
				parseFloat(this.mi_cuenta.monto)
			) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "No cuenta con dinero suficiente en cuenta.",
				});
				return false;
			}

			Swal.fire({
				title: "¿Aperturar caja?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append(
						"frmDatosApertura",
						JSON.stringify(this.frmDatosApertura)
					);
					this.$inertia.post(route("caj.apertura_caja.aperturar"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "REGISTRANDO",
								text: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								allowOutsideClick: false,
								preConfirm: (result) => {},
							});
						},
					});
				} else {
					return false;
				}
			});
		},
	},
};
</script>

<style lang="css">
.slot_apertura_caja {
	width: 40% !important;
	margin-top: 10% !important;
	margin-left: 30% !important;
}
@media (max-width: 900px) {
	.slot_apertura_caja {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>
