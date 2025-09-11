<template>
	<div id="mdlEvaluacionSimple" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-30 mdlEvaluacionSimple">
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
						<label class="label-title" v-if="sub_grupo != 'OTROS'">{{
							message
						}}</label>
						<div class="form-row justify-content-center">
							<div
								class="col-4 text-right"
								:class="
									sub_grupo == 'DEUDA_CORTO_PLAZO' ||
									sub_grupo == 'DEUDA_LARGO_PLAZO'
										? 'col-md-6'
										: 'col-md-5'
								"
							>
								<label class="form-control-label">{{ label_text }}</label>
								<span
									v-if="submited && !$v.input_value.required"
									class="span-error-message"
								>
									*
								</span>
							</div>
							<div class="col-md-5 col-6">
								<input
									type="number"
									class="form-control center"
									style="font-weight: bolder; font-size: 15px"
									@change="Redondear"
									v-model.number="input_value"
									min="0"
									step="1"
									lang="en"
									:disabled="no_editable"
									autocomplete="off"
								/>
							</div>
						</div>
						<hr v-if="botones_visible" />
						<div class="text-right" v-if="botones_visible">
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
			message: null,
			label_text: null,
			input_value: null,
			no_editable: true,
			ruta_guardar: null,
			botones_visible: true,
		};
	},
	validations: {
		input_value: { required },
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
		sub_grupo(value) {
			if (value == "DEUDA_CORTO_PLAZO" || value == "DEUDA_LARGO_PLAZO") {
				this.botones_visible = false;
			} else {
				this.botones_visible = true;
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
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0 && e.target.value != null) {
				valor = e.target.value;
			}
			this.input_value = this.$parent.round(valor, numero_decimales);
		},
		CerrarModal() {
			$("#mdlEvaluacionSimple").css("display", "none");
		},
		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.input_value.$invalid) {
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
						data.append("valor", self.input_value);
						data.append("sub_grupo", self.sub_grupo);
						data.append("agencia_id", self.agencia_id);

						self.$inertia.post(route(this.ruta_guardar), data, {
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "CARGANDO",
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
								$("#mdlEvaluacionSimple").css("display", "none");
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
.mdlEvaluacionSimple {
	margin-top: 12% !important;
}

@media only screen and (max-width: 900px) {
	.mdlEvaluacionSimple {
		margin-top: 49% !important;
	}
}
</style>
