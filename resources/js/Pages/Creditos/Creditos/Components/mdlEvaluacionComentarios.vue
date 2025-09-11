<template>
	<div id="mdlEvaluacionComentarios" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-40 mdlEvaluacionComentarios">
			<div class="content" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>{{ title_modal }}</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%; float: right !important"
							@click="CerrarModal"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>

					<div class="card-body card-block">
						<textarea
							class="form-control mayus"
							v-model="comentario"
							:disabled="no_editable"
							style="height: 200px"
							autocomplete="off"
							@focus="hidenav()"
							@blur="shownav()"
						></textarea>

						<hr />
						<div class="text-right">
							<div class="btn-group" role="group">
								<button
									class="btn btn-action btn-icon-split"
									@click="no_editable = false"
									:disabled="!no_editable"
								>
									<span class="icon text-white">
										<i class="fas fa-edit"></i>
									</span>
									<span class="text">EDITAR</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split"
									@click="Guardar"
									:disabled="no_editable"
								>
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
export default {
	props: { evaluacion_id: Number, cliente_id: Number, agencia_id: Number },
	data() {
		return {
			submited: false,
			grupo: null,
			sub_grupo: null,
			title_modal: null,
			comentario: null,
			no_editable: true,
			ruta_guardar: null,
		};
	},
	validations: {
		comentario: { required },
	},
	watch: {
		grupo(value) {
			if (value == "ACTIVO_CORRIENTE") {
				this.ruta_guardar = "cre.evaluacion.activo_corriente.guardar";
			} else if (value == "ACTIVO_NO_CORRIENTE") {
				this.ruta_guardar = "cre.evaluacion.activo_no_corriente.guardar";
			} else if (value == "PASIVO_CORRIENTE") {
				this.ruta_guardar = "cre.evaluacion.pasivo_corriente.guardar";
			} else if (value == "FLUJO_CAJA") {
				this.ruta_guardar = "cre.evaluacion.flujo_caja.guardar";
			} else if (value == "COMENTARIOS") {
				this.ruta_guardar = "cre.evaluacion.comentarios.guardar";
			} else if (value == "CONVENIO") {
				this.ruta_guardar = "cre.evaluacion.convenio.guardar";
			}
		},
	},

	methods: {
		hidenav() {
			return this.$parent.hide_nav();
		},
		shownav() {
			return this.$parent.show_nav();
		},
		CerrarModal() {
			$("#mdlEvaluacionComentarios").css("display", "none");
		},
		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.comentario.$invalid) {
				return false;
			} else {
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
					preConfirm: (result) => {
						let data = new FormData();
						data.append("evaluacion_id", self.evaluacion_id);
						data.append("cliente_id", self.cliente_id);
						data.append("valor", self.comentario.toUpperCase());
						data.append("sub_grupo", self.sub_grupo);
						data.append("agencia_id", self.agencia_id);

						self.$inertia.post(route(this.ruta_guardar), data, {
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "CARGANDO",
									html: "Espere porfavor...",
									timer: 300,
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
								$("#mdlEvaluacionComentarios").css("display", "none");
							},
						});
					},
				});
			}
		},
	},
};
</script>

<style lang="css">
.mdlEvaluacionComentarios {
	margin-top: 5%;
}

@media only screen and (max-width: 900px) {
	.mdlEvaluacionComentarios {
		margin-top: 35%;
	}
}
</style>
