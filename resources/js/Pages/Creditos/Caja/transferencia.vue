<template>
	<layout ref="layout">
		<div
			class="slot_body slot-caja-transferencia"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							'TRANSFERIR DINERO A ' +
							(this.tipo == 'A_CAJA' ? 'CAJA' : 'CUENTA')
						"
					></headerClose>

					<div class="card-title">DESDE CAJA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-7 col-7">
								<label class="label-title">MONTO</label>
								<span
									v-if="
										submited &&
										(!$v.frmDatosTransferencia.monto.required ||
											!$v.frmDatosTransferencia.monto.noZero)
									"
									class="span-error-message"
									style="font-size: 10px"
								>
									* Mayor a 0
								</span>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">S/</span>
									</div>
									<input
										type="number"
										min="0.10"
										step="0.10"
										class="form-control text-center"
										style="
											height: 45px;
											font-size: 24px;
											color: var(--colorAlto);
											font-weight: bolder;
										"
										@change="Redondear"
										v-model.number="frmDatosTransferencia.monto"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
							<div class="form-group col-md-5 col-5">
								<label class="label-title">{{
									"A " + (this.tipo == "A_CAJA" ? "CAJA" : "CUENTA")
								}}</label>
								<span
									v-if="
										submited && !$v.frmDatosTransferencia.destinatario_id.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control center"
									v-model="frmDatosTransferencia.destinatario_id"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in destinatarios"
										:key="index"
										:value="item.id"
									>
										{{ item.usuario }}
									</option>
								</select>
							</div>

							<div class="form-group col-md-12 col-12">
								<label class="label-title">DESCRIPCIÓN</label>
								<span
									v-if="
										submited && !$v.frmDatosTransferencia.descripcion.required
									"
									class="span-error-message"
								>
									*
								</span>
								<textarea
									type="text"
									rows="3"
									class="form-control mayus text-row"
									v-model="frmDatosTransferencia.descripcion"
									@focus="hidenav()"
									@blur="shownav()"
								></textarea>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="Transferir"
								title="Transferir DINERO"
							>
								<span class="icon text-white">
									<i class="fas fa-paper-plane"></i>
								</span>
								<span class="text">TRANSFERIR</span>
							</button>
						</div>
					</div>
				</div>
			</div>
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
		agencia_id: Number,
		tipo: String,
		destinatarios: Array,
	},
	data() {
		return {
			submited: false,
			frmDatosTransferencia: {
				monto: parseFloat(20).toFixed(2),
				remitente_id: null,
				destinatario_id: 0,
				descripcion: null,
			},
		};
	},

	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
	},
	mounted() {
		if (this.mi_caja == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Primero debe aperturar CAJA",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			this.frmDatosTransferencia.remitente_id = this.mi_caja.id;
		}
	},
	validations: {
		frmDatosTransferencia: {
			monto: { required, noZero },
			destinatario_id: { noZero },
			descripcion: { required },
		},
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			this.frmDatosTransferencia.monto = this.$refs.layout.round(
				valor,
				numero_decimales
			);
		},

		Transferir() {
			let self = this;
			this.submited = true;

			if (this.$v.frmDatosTransferencia.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos",
				});
				return false;
			}

			let mensaje = "";

			if (this.tipo == "A_CAJA") {
				mensaje = "A CAJA";
			} else if (this.tipo == "A_CUENTA") {
				mensaje = "A CUENTA";
			}

			Swal.fire({
				title: "TRANSFERIR " + mensaje,
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
					data.append("agencia_id", this.agencia_id);
					data.append(
						"frmDatosTransferencia",
						JSON.stringify(this.frmDatosTransferencia)
					);
					data.append("tipo", this.tipo);

					this.$inertia.post(route("caj.transferencia.registrar"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "TRANSFIRIENDO",
								text: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							return Swal.fire({
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
	},
};
</script>

<style lang="css">
.slot-caja-transferencia {
	width: 30% !important;
	margin-top: 10% !important;
	margin-left: 35% !important;
}

@media only screen and (max-width: 900px) {
	.slot-caja-transferencia {
		width: 96% !important;
		margin-left: 2% !important;
		margin-top: 15% !important;
	}
}
</style>


