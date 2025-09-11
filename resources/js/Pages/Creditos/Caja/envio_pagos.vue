<template>
	<layout ref="layout">
		<div
			class="slot_body slot-cuenta-envio"
			slot="component-view"
			v-if="mi_caja.monto_apertura != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ENVÍO DE PAGOS'"></headerClose>
					<div class="card">
						<div class="card-body card-block">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label class="label-title">DE</label>
									<input
										class="form-control text-center"
										type="text"
										:value="mi_usuario.nombre_agencia"
										disabled
									/>

									<label class="label-title">CAJA ORIGEN</label>

									<input
										class="form-control text-center"
										type="text"
										:value="mi_usuario.usuario"
										disabled
									/>
								</div>

								<div class="form-group col-md-6">
									<label class="label-title">PARA</label>
									<span
										v-if="
											submited &&
											!$v.frmDatosEnvio.agencia_destinatario_id.noZero
										"
										class="span-error-message"
										>*</span
									>
									<select
										class="form-control center"
										@change="obtenerCajasAbiertas"
										v-model="frmDatosEnvio.agencia_destinatario_id"
									>
										<option :value="0" selected disabled>Seleccionar...</option>
										<option
											v-for="(item, index) in agencias"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>

									<label class="label-title">USUARIO DESTINO</label>
									<span
										v-if="submited && !$v.frmDatosEnvio.destinatario_id.noZero"
										class="span-error-message"
										>*</span
									>
									<select
										class="form-control center"
										v-model="frmDatosEnvio.destinatario_id"
									>
										<option :value="0" selected disabled>Seleccionar...</option>
										<option
											v-for="(item, index) in cajas_filtrados"
											:key="index"
											:value="item.dni"
										>
											{{ item.usuario }}
										</option>
									</select>
								</div>

								<div class="form-group col-md-6 offset-md-3 col-12">
									<label class="label-title">MONTO</label>
									<span
										v-if="
											submited &&
											(!$v.frmDatosEnvio.monto.noZero ||
												!$v.frmDatosEnvio.monto.required)
										"
										class="span-error-message"
										>*</span
									>
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
											class="form-control text-center"
											style="
												height: 45px;
												font-size: 24px;
												font-weight: bolder;
												color: var(--colorAlto);
											"
											v-model="frmDatosEnvio.monto"
											@change="Redondear"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
								</div>

								<div class="form-group col-md-4">
									<label class="label-title">MOTIVO DE ENVÍO</label>
									<span
										v-if="submited && !$v.frmDatosEnvio.motivo.required"
										class="span-error-message"
										>*</span
									>
									<select
										class="form-control center"
										v-model="frmDatosEnvio.motivo"
									>
										<option value="CLIENTES">Clientes</option>
										<option value="OTROS">Otros</option>
									</select>
								</div>
								<div class="form-group col-md-12">
									<label class="label-title">CONCEPTO</label>
									<span
										v-if="submited && !$v.frmDatosEnvio.concepto.required"
										class="span-error-message"
										>*</span
									>
									<textarea
										type="text"
										rows="3"
										class="form-control mayus text-row"
										v-model="frmDatosEnvio.concepto"
										@focus="hidenav()"
										@blur="shownav()"
									></textarea>
								</div>
							</div>
							<div class="text-right">
								<button
									class="btn btn-action btn-icon-split"
									@click="enviarPagos()"
									title="Enviar EFECTIVO"
								>
									<span class="icon text-white">
										<i class="fas fa-paper-plane"></i>
									</span>
									<span class="text">ENVIAR</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ---------------------- -->
		</div>
	</layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		cuentas: Array,
		bancos: Array,
		usuarios: Array,
	},
	data() {
		return {
			submited: false,
			cuentas_filtradas: [],
			cajas_filtrados: [],
			agencias: [],
			frmDatosEnvio: {
				monto: parseFloat(20).toFixed(2),
				destinatario_id: 0,
				agencia_destinatario_id: 0,
				concepto: null,
				motivo: "CLIENTES",
			},
		};
	},
	validations() {
		return {
			frmDatosEnvio: {
				monto: { noZero, required },
				destinatario_id: { noZero },
				agencia_destinatario_id: { noZero },
				concepto: { required },
				motivo: { required },
			},
		};
	},

	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		mi_usuario() {
			return this.$inertia.page.props.user_session;
		},
	},
	watch: {},
	mounted() {
		if (this.mi_caja == null) {
			Swal.fire({
				title: "¡Ups!",
				text: "Usted no tiene una cuenta habilitada",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			let agencias_filtradras = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/ENVIO_PAGOS"
			);

			this.agencias = agencias_filtradras.filter((item) => {
				return item.id != this.mi_caja.agencia_id;
			});
		}
	},

	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},
		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0.1) {
				valor = e.target.value;
			}

			this.frmDatosEnvio.monto = this.$refs.layout.round(
				valor,
				numero_decimales
			);
		},
		obtenerCajasAbiertas() {
			let self = this;
			self.frmDatosEnvio.destinatario_id = 0;
			self.cajas_filtrados = self.usuarios.filter((item) => {
				return item.agencia_id == self.frmDatosEnvio.agencia_destinatario_id;
			});
		},
		enviarPagos() {
			this.submited = true;

			if (this.$v.frmDatosEnvio.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos",
				});
				return false;
			}

			if (
				parseFloat(this.mi_caja.monto) < parseFloat(this.frmDatosEnvio.monto)
			) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Su dinero en CUENTA es inferior a la cantidad que desea enviar",
				});

				this.frmDatosEnvio.monto = parseFloat(20).toFixed(2);

				return false;
			}

			Swal.fire({
				title: "REGISTRAR ENVÍO",
				text: "¿Desea continuar?",
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
					data.append("agencia_remitente_id", this.mi_usuario.id_agencia);
					data.append("remitente_id", this.mi_caja.id);
					data.append("frmDatosEnvio", JSON.stringify(this.frmDatosEnvio));

					this.$inertia.post(route("caj.envio_pagos.registrar"), data, {
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
								allowOutsideClick: true,
							});
						},
					});
				} else {
					return false;
				}
			});
		},
		Imprimir() {
			$("#AreaImprimible").print();
		},
	},
};
</script>

<style lang="css">
.slot-cuenta-envio {
	width: 40% !important;
	margin-left: 30% !important;
}

@media only screen and (max-width: 900px) {
	.slot-cuenta-envio {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


