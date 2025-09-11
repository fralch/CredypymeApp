<template>
	<div id="mdlEnvioNotificacion" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-30 mdlEnvioNotificacion">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'DETALLE DE ENVÍO'"
						:nombre_modal="'mdlEnvioNotificacion'"
					>
					</headerCloseModal>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-4 offset-md-4">
								<label class="label-title">USUARIO</label>
								<span
									v-if="
										submited && !$v.frmEnvioNotificacion.usuario_envio.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control center"
									v-model="frmEnvioNotificacion.usuario_envio"
								>
									<option :value="0" selected disabled>Seleccione...</option>
									<option
										v-for="(item, index) in usuarios_agencia"
										:key="index"
										:value="item.dni"
									>
										{{ item.usuario }}
									</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="label-title">DESCRIPCIÓN</label>

								<textarea
									class="form-control mayus text-row"
									v-model="frmEnvioNotificacion.descripcion_envio"
									rows="3"
									autocomplete="off"
									@focus="hidenav()"
									@blur="shownav()"
								></textarea>
							</div>
						</div>

						<hr />
						<div class="text-right">
							<div class="btn-group" role="group">
								<button class="btn btn-action btn-icon-split" @click="Guardar">
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
const noZero = (value) => value != 0;
export default {
	components: { headerCloseModal },
	props: { usuarios_agencia: Array, agencia_id: Number },
	data() {
		return {
			submited: false,

			frmEnvioNotificacion: {
				credito_id: null,
				notificacion_id: null,
				usuario_envio: null,
				descripcion_envio: null,
			},
		};
	},
	validations: {
		frmEnvioNotificacion: {
			usuario_envio: { noZero },
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

			if (this.$v.frmEnvioNotificacion.$invalid) {
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
						"frmEnvioNotificacion",
						JSON.stringify(self.frmEnvioNotificacion)
					);

					Swal.fire({
						title: "CARGANDO",
						text: "Espere porfavor...",
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();

							axios
								.post(route("rep.cre.dias_mora.envio_notificacion"), data)
								.then(function (response) {
									self.$parent.datos_credito = response.data.datos_credito;
									self.$parent.datos_cuotas = response.data.datos_cuotas;
									self.$parent.datos_titular = response.data.datos_titular;
									self.$parent.compromisos = response.data.compromisos;
									self.$parent.notificaciones = response.data.notificaciones;
									self.$parent.notificaciones_tipos =
										response.data.notificaciones_tipos;

									$("#mdlEnvioNotificacion").css("display", "none");
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
.mdlEnvioNotificacion {
	margin-top: 5%;
}

@media only screen and (max-width: 900px) {
	.mdlEnvioNotificacion {
		margin-top: 35%;
	}
}
</style>
