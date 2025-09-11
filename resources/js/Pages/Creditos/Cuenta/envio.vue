<template>
	<layout ref="layout">
		<div
			class="slot_body slot-cuenta-envio"
			slot="component-view"
			v-if="mi_cuenta != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ENVÍO DE EFECTIVO'"></headerClose>

					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="datos-tab"
									data-toggle="tab"
									href="#datos"
									role="tab"
									aria-controls="datos"
									aria-selected="true"
									>DATOS DEL ENVÍO</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="comprobante-tab"
									data-toggle="tab"
									href="#comprobante"
									role="tab"
									aria-controls="comprobante"
									aria-selected="false"
									>COMPROBANTE</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="datos"
								role="tabpanel"
								aria-labelledby="datos-tab"
							>
								<div class="card">
									<div class="card-body card-block">
										<div class="form-row">
											<div class="form-group col-md-6 col-6">
												<label class="label-title">DE</label>
												<input
													class="form-control text-center"
													type="text"
													:value="mi_usuario.nombre_agencia"
													disabled
												/>
											</div>

											<div class="form-group col-md-6 col-6">
												<label class="label-title">CUENTA ORIGEN</label>

												<input
													class="form-control text-center"
													type="text"
													:value="mi_usuario.usuario"
													disabled
												/>
											</div>

											<div class="form-group col-md-6 col-6">
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
													v-model="frmDatosEnvio.agencia_destinatario_id"
													@change="FiltrarCuentas"
												>
													<option :value="0" selected disabled>
														Seleccionar...
													</option>
													<option
														v-for="(item, index) in agencias"
														:key="index"
														:value="item.id"
													>
														{{ item.agencia }}
													</option>
												</select>
											</div>
											<div class="form-group col-md-6 col-6">
												<label class="label-title">CUENTA DESTINO</label>
												<span
													v-if="
														submited && !$v.frmDatosEnvio.destinatario_id.noZero
													"
													class="span-error-message"
													>*</span
												>
												<select
													class="form-control center"
													v-model="frmDatosEnvio.destinatario_id"
												>
													<option :value="0" selected disabled>
														Seleccionar...
													</option>
													<option
														v-for="(item, index) in cuentas_filtradas"
														:key="index"
														:value="item.id"
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

											<div class="form-group col-md-4 col-6">
												<label class="label-title">TIPO DE ENVIO</label>
												<span
													v-if="submited && !$v.frmDatosEnvio.tipo.required"
													class="span-error-message"
													>*</span
												>
												<select
													class="form-control center"
													v-model="frmDatosEnvio.tipo"
												>
													<option value="PERSONAL">Personal</option>
													<option value="BANCO">Por banco</option>
												</select>
											</div>

											<div
												class="form-group col-md-4 col-6"
												v-if="frmDatosEnvio.tipo == 'BANCO'"
											>
												<label class="label-title">BANCO</label>
												<span
													v-if="submited && frmDatosEnvio.entidad_id.noZero"
													class="span-error-message"
													>*</span
												>
												<select
													class="form-control center"
													v-model="frmDatosEnvio.entidad_id"
												>
													<option :value="0" selected disabled>
														Seleccionar...
													</option>
													<option
														v-for="(item, index) in bancos"
														:key="index"
														:value="item.id"
													>
														{{ item.nombre }}
													</option>
												</select>
											</div>

											<div class="form-group col-md-4 col-6">
												<label class="label-title">USUARIO DE GESTIÓN</label>
												<span
													v-if="
														submited &&
														!$v.frmDatosEnvio.usuario_gestion_id.noZero
													"
													class="span-error-message"
													>*</span
												>
												<select
													class="form-control center"
													v-model="frmDatosEnvio.usuario_gestion_id"
												>
													<option :value="0" selected disabled>
														Seleccionar...
													</option>

													<option
														v-for="(item, index) in usuarios_filtrados"
														:key="index"
														:value="item.dni"
													>
														{{ item.usuario }}
													</option>
												</select>

												<div class="col-md-12">
													<div class="form-check">
														<input
															class="form-check-input"
															type="checkbox"
															v-model="mostrar_todos"
															id="chbMostrarTodos"
														/>
														<label class="label-title" for="chbMostrarTodos">
															Todos
														</label>
													</div>
												</div>
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
									</div>
								</div>
								<!-- ------------------------------- -->
							</div>
							<div
								class="tab-pane fade"
								id="comprobante"
								role="tabpanel"
								aria-labelledby="comprobante-tab"
							>
								<div class="p-2">
									<div
										id="previzualizar"
										style="
											width: 100% !important;
											height: 300px !important;
											box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
										"
									></div>
								</div>

								<!-- ----**** ---- -->
								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="label-title">ADJUNTAR COMPROBANTE</label>
										<span
											v-if="
												submited && !$v.frmDatosEnvio.comprobante_envio.required
											"
											class="span-error-message"
											>*</span
										>
										<input
											class="btn btn-primary"
											style="
												background-color: var(--plomoOscuroEmpresarial);
												border: none;
												max-width: 400px;
												font-size: 12px;
											"
											type="file"
											accept="image/*"
											name="comprobante_envio"
											id="inpComprobanteEnvio"
											@change="AgregarComprobante"
										/>
									</div>
								</div>

								<!-- ------------------------------- -->
							</div>
							<hr />

							<div class="text-right">
								<button
									class="btn btn-action btn-icon-split"
									@click="Registrar"
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

			agencias: [],

			cuentas_filtradas: [],
			usuarios_filtrados: [],

			mostrar_todos: false,

			frmDatosEnvio: {
				monto: parseFloat(20).toFixed(2),
				destinatario_id: 0,
				agencia_destinatario_id: 0,
				concepto: null,
				tipo: "PERSONAL",
				entidad_id: 0,
				usuario_gestion_id: 0,
				comprobante_envio: null,
			},
		};
	},
	validations() {
		if (this.frmDatosEnvio.tipo == "BANCO") {
			return {
				frmDatosEnvio: {
					monto: { noZero, required },
					destinatario_id: { noZero },
					agencia_destinatario_id: { noZero },
					concepto: { required },
					tipo: { required },
					entidad_id: { noZero },
					usuario_gestion_id: { noZero },
					comprobante_envio: { required },
				},
			};
		} else {
			return {
				frmDatosEnvio: {
					monto: { noZero, required },
					destinatario_id: { noZero },
					agencia_destinatario_id: { noZero },
					concepto: { required },
					tipo: { required },
					usuario_gestion_id: { noZero },
					comprobante_envio: { required },
				},
			};
		}
	},

	computed: {
		mi_cuenta() {
			return this.$inertia.page.props.creditos_datos.datos_cuenta;
		},
		mi_usuario() {
			return this.$inertia.page.props.user_session;
		},
	},
	watch: {
		mostrar_todos() {
			this.FiltrarUsuarios();
		},
	},
	mounted() {
		if (this.mi_cuenta == null) {
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
			this.agencias = this.$page.props.application.agencias.filter(
				(item) => item.id != this.mi_usuario.id_agencia
			);
			this.FiltrarUsuarios();
		}
	},

	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},

		FiltrarCuentas(e) {
			let self = this;
			let agencia_id = e.target.value;
			axios
				.post(route("cue.envio.listar_cuentas", agencia_id))
				.then(function (response) {
					self.cuentas_filtradas = response.data;
					self.frmDatosEnvio.destinatario_id = 0;
				});
		},
		FiltrarUsuarios() {
			if (this.mostrar_todos) {
				this.usuarios_filtrados = this.usuarios;
			} else {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.mi_usuario.id_agencia
				);
			}

			this.frmDatosEnvio.usuario_gestion_id = 0;
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

		Registrar() {
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
				parseFloat(this.mi_cuenta.monto) < parseFloat(this.frmDatosEnvio.monto)
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
					data.append("remitente_id", this.mi_cuenta.id);
					data.append("frmDatosEnvio", JSON.stringify(this.frmDatosEnvio));
					data.append("documento", this.frmDatosEnvio.comprobante_envio);

					this.$inertia.post(route("cue.envio.registrar"), data, {
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

		AgregarComprobante(e) {
			let previo = $("#previzualizar img");
			previo.remove();

			this.frmDatosEnvio.comprobante_envio = e.target.files[0];

			let reader = new FileReader();
			reader.readAsDataURL(e.target.files[0]);
			reader.onload = function () {
				let preview = document.getElementById("previzualizar"),
					image = document.createElement("img");

				image.src = reader.result;
				image.style.border = "1px solid #ffff";

				image.style.width = "100%";
				image.style.height = "300px";

				preview.innerHTML = "";
				preview.append(image);
			};
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
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
