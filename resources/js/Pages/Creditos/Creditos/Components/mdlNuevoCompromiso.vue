<template>
	<div id="mdlNuevoCompromiso" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-50 mdlNuevoCompromiso">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'NUEVO COMPROMISO'"
						:nombre_modal="'mdlNuevoCompromiso'"
					>
					</headerCloseModal>

					<div class="card-body card-block">
						<span
							v-if="submited && !$v.frmDatosCompromiso.compromiso.required"
							class="span-error-message"
							style="font-size: 10px"
						>
							* Ingrese el compromiso
						</span>
						<textarea
							class="form-control mayus"
							v-model="frmDatosCompromiso.compromiso"
							style="height: 200px"
							autocomplete="off"
							@focus="hidenav()"
							@blur="shownav()"
						></textarea>

						<br />
						<div class="form-row">
							<div class="input-group col-md-7">
								<div class="input-group-prepend">
									<label class="input-group-text prepend-title"
										>FECHA Y HORA DE VISITA</label
									>
								</div>

								<input
									type="date"
									class="form-control center"
									v-model="frmDatosCompromiso.fecha_visita"
								/>
								<span
									v-if="
										submited && !$v.frmDatosCompromiso.fecha_visita.required
									"
									class="span-error-message"
								>
									*
								</span>
								<input
									type="time"
									class="form-control center"
									v-model="frmDatosCompromiso.hora_visita"
								/>
								<span
									v-if="submited && !$v.frmDatosCompromiso.hora_visita.required"
									class="span-error-message"
								>
									*
								</span>
							</div>
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<label class="input-group-text prepend-title"
										>FECHA VENCIMIENTO</label
									>
								</div>

								<input
									type="date"
									class="form-control center"
									v-model="frmDatosCompromiso.fecha_vencimiento"
								/>
								<span
									v-if="
										submited &&
										!$v.frmDatosCompromiso.fecha_vencimiento.required
									"
									class="span-error-message"
								>
									*
								</span>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<div class="btn-group" role="group">
								<button class="btn btn-cancel btn-icon-split" @click="Guardar">
									<span class="icon text-white">
										<i class="fas fa-save"></i>
									</span>
									<span class="text">GUARDAR</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
import { required } from "vuelidate/lib/validators";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: { headerCloseModal },
	props: { agencia_id: Number },
	data() {
		return {
			submited: false,

			frmDatosCompromiso: {
				credito_id: null,
				compromiso: null,
				fecha_visita: null,
				hora_visita: null,
				fecha_vencimiento: null,
			},
		};
	},
	validations: {
		frmDatosCompromiso: {
			compromiso: { required },
			fecha_visita: { required },
			hora_visita: { required },
			fecha_vencimiento: { required },
		},
	},

	methods: {
		hidenav() {
			return this.$parent.$parent.hide_nav();
		},
		shownav() {
			return this.$parent.$parent.show_nav();
		},

		Guardar() {
			let self = this;
			this.submited = true;

			if (this.$v.frmDatosCompromiso.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos",
					allowOutsideClick: true,
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿Desea guardar los cambios?",
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
						"frmDatosCompromiso",
						JSON.stringify(self.frmDatosCompromiso)
					);

					Swal.fire({
						title: "CARGANDO",
						text: "Espere porfavor...",
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();

							axios
								.post(route("rep.cre.dias_mora.asignar_compromiso"), data)
								.then(function (response) {
									self.$parent.datos_credito = response.data.datos_credito;
									self.$parent.datos_cuotas = response.data.datos_cuotas;
									self.$parent.datos_titular = response.data.datos_titular;
									self.$parent.compromisos = response.data.compromisos;
									self.$parent.notificaciones = response.data.notificaciones;
									self.$parent.notificaciones_tipos =
										response.data.notificaciones_tipos;

									$("#mdlNuevoCompromiso").css("display", "none");
									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										allowOutsideClick: false,
									});
								});
						},
					});
				}
			});
		},
	},
};
</script>

<style lang="css">
.mdlNuevoCompromiso {
	margin-top: 5%;
}

@media only screen and (max-width: 900px) {
	.mdlNuevoCompromiso {
		margin-top: 35%;
	}
}
</style>
