<template>
	<layout ref="layout">
		<div
			class="slot_body slot-caja-adelanto-haberes"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block" id="cuerpo">
				<!-- ---------------------- -->
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'REGISTRAR ADELANTO DE HABERES'"></headerClose>

						<div class="card-title">DETALLE</div>
						<div class="card-body card-block">
							<div class="text-center">
								<label class="label-title"
									>ADELANTOS RECIBIDOS EN EL MES:

									<span
										style="font-size: 20px !important; color: var(--colorAlto)"
									>
										S/ {{ total_adelantos }}</span
									>
								</label>
							</div>

							<div class="form-row">
								<div class="form-group col-md-6 offset-md-3">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
										</div>
										<select
											class="form-control center"
											v-model="agencia_seleccionada"
											@change="FiltrarUsuarios"
											:disabled="adelanto_registrado != null"
										>
											<option
												v-for="(agencia, index) in agencias_permitidas"
												:key="index"
												:value="agencia.id"
											>
												{{ agencia.agencia }}
											</option>
										</select>
									</div>
								</div>

								<div class="form-group col-md-8 offset-md-2">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>COLABORADOR</span
											>
										</div>
										<select
											class="form-control center"
											:class="[
												submited
													? $v.frmDatosAdelanto.usuario_id.$invalid
														? 'is-invalid'
														: 'is-valid'
													: '',
											]"
											v-model="frmDatosAdelanto.usuario_id"
											:disabled="adelanto_registrado != null"
										>
											<option :value="0" selected disabled>Seleccionar</option>
											<option
												v-for="(item, index) in usuarios_filtrados"
												:key="index"
												:value="item.dni"
											>
												{{
													item.usuario +
													" - " +
													item.nombres.split(" ")[0] +
													" " +
													item.apellido_paterno
												}}
											</option>
										</select>
									</div>
								</div>

								<div class="form-group col-md-4 offset-md-4">
									<div class="input-group">
										<div class="input-group-prepend">
											<span
												class="input-group-text bolder"
												style="font-size: 20px"
												>S/</span
											>
										</div>
										<input
											type="number"
											min="0.1"
											step="1"
											class="form-control center"
											:class="[
												submited
													? $v.frmDatosAdelanto.monto.$invalid
														? 'is-invalid'
														: 'is-valid'
													: '',
											]"
											style="
												height: 45px;
												font-size: 24px;
												font-weight: bolder;
												color: var(--colorAlto);
											"
											v-model.number="frmDatosAdelanto.monto"
											@change="Redondear"
											:disabled="
												frmDatosAdelanto.usuario_id == 0 ||
												mes_cancelado ||
												adelanto_registrado != null
											"
										/>
									</div>
								</div>

								<div class="form-group col-md-2">
									<div class="form-check">
										<input
											class="form-check-input"
											type="radio"
											value="ADELANTO"
											name="tipo"
											id="rdbAdelanto"
											v-model="frmDatosAdelanto.tipo"
											:disabled="adelanto_registrado != null || mes_cancelado"
										/>
										<label class="label-title" for="rdbAdelanto">
											ADELANTO
										</label>
									</div>
									<div class="form-check">
										<input
											class="form-check-input"
											type="radio"
											value="CANCELACION"
											name="tipo"
											id="rdbCancelacion"
											v-model="frmDatosAdelanto.tipo"
											:disabled="adelanto_registrado != null || mes_cancelado"
										/>
										<label class="label-title" for="rdbCancelacion">
											CANCELACIÓN
										</label>
									</div>
								</div>
								<div class="form-group col-md-12">
									<label class="label-title">DESCRIPCIÓN</label>

									<textarea
										type="text"
										rows="3"
										class="form-control mayus text-row"
										:class="[
											submited
												? $v.frmDatosAdelanto.descripcion.$invalid
													? 'is-invalid'
													: 'is-valid'
												: '',
										]"
										v-model="frmDatosAdelanto.descripcion"
										:disabled="
											frmDatosAdelanto.usuario_id == 0 ||
											mes_cancelado ||
											adelanto_registrado != null
										"
									></textarea>
								</div>
							</div>

							<!-- ------------------------------- -->
							<hr />
							<div class="form-row">
								<div class="form-group col-md-8">
									<div class="btn-group" role="group">
										<button
											class="btn btn-cancel btn-icon-split"
											@click="Imprimir('voucher')"
											v-if="adelanto_registrado != null"
										>
											<span class="icon text-white">
												<i class="fas fa-print"></i>
											</span>
											<span class="text">VOUCHER</span>
										</button>
										<button
											class="btn btn-action btn-icon-split"
											@click="Imprimir('declaracion')"
											v-if="adelanto_registrado != null"
										>
											<span class="icon text-white">
												<i class="fas fa-print"></i>
											</span>
											<span class="text">DECLARACIÓN</span>
										</button>

										<button
											class="btn btn-cancel btn-icon-split"
											@click="AplicarPlantilla()"
											v-if="adelanto_registrado == null && !mes_cancelado"
										>
											<span class="icon text-white">
												<i class="fas fa-list"></i>
											</span>
											<span class="text">APLICAR PLANTILLA</span>
										</button>
									</div>
								</div>
								<div class="form-group col-md-4 text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="Registrar"
											v-if="adelanto_registrado == null && !mes_cancelado"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text">REGISTRAR</span>
										</button>
										<button
											class="btn btn-cancel btn-icon-split"
											@click="Nuevo"
											v-if="adelanto_registrado != null"
											:disabled="mes_cancelado"
										>
											<span class="icon text-white">
												<i class="fas fa-plus"></i>
											</span>
											<span class="text">NUEVO</span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- ---------------------- -->
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
	data() {
		return {
			agencia_seleccionada: 0,
			agencias_permitidas: [],
			agencias: [],

			usuarios: [],
			usuarios_filtrados: [],

			total_adelantos: this.round_to(0, 2),
			mes_cancelado: false,
			adelanto_registrado: null,

			submited: false,
			frmDatosAdelanto: {
				usuario_id: 0,
				tipo: "ADELANTO",
				monto: this.round_to(0, 2),
				descripcion: null,
			},
		};
	},
	validations: {
		frmDatosAdelanto: {
			usuario_id: { noZero },
			monto: { noZero, required },
			descripcion: { required },
		},
	},

	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		usuario() {
			return this.frmDatosAdelanto.usuario_id;
		},
		nombre_colaborador() {
			if (this.frmDatosAdelanto.usuario_id != 0) {
				let usuario = this.usuarios.filter(
					(item) => item.dni == this.frmDatosAdelanto.usuario_id
				)[0];

				return (
					usuario.nombres +
					" " +
					usuario.apellido_paterno +
					" " +
					usuario.apellido_materno
				);
			} else {
				return null;
			}
		},
	},

	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				if (this.adelanto_registrado != null) {
					this.agencia_seleccionada = this.adelanto_registrado.agencia_id;
				} else {
					this.agencia_seleccionada = mi_agencia[0].id;
				}
			} else {
				if (value.length > 0) {
					if (this.adelanto_registrado != null) {
						this.agencia_seleccionada = this.adelanto_registrado.agencia_id;
					} else {
						this.agencia_seleccionada = value[0].id;
					}
				} else {
					this.agencia_seleccionada = null;
				}
			}
			this.FiltrarUsuarios();
		},
		async usuario(value) {
			if (value != 0) {
				const params = {
					usuario_id: value,
					agencia_id: this.agencia_seleccionada,
				};

				// this.$inertia.get(route("caj.adelanto_haberes.revisar"), params);
				// return false;

				await axios
					.get(route("caj.adelanto_haberes.revisar"), { params })
					.then((response) => {
						// console.log(response.data);
						this.frmDatosAdelanto.monto = this.round_to(0, 2);
						this.frmDatosAdelanto.descripcion = null;
						this.total_adelantos = parseFloat(
							response.data.total_adelantos
						).toFixed(2);
						this.mes_cancelado = response.data.mes_cancelado;

						if (this.mes_cancelado) {
							return Swal.fire({
								icon: "error",
								title: "Ups!",
								text: "Ya se realizó la CANCELACIÓN del MES al COLABORADOR seleccionado.",
								showConfirmButton: true,
							});
						}
					});
			} else {
				this.total_adelantos = parseFloat(0).toFixed(2);
			}
		},
	},
	mounted() {
		if (this.mi_caja == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Primero debe aperturar CAJA",
				confirmButtonText: "Ok",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			this.ListarAgenciasPermitidas();
			this.ListarRecursos();
		}
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/ADELANTO_HABERES"
			);
		},

		round_to(value, decimalPlaces = 2) {
			if (isNaN(value)) return (0).toFixed(decimalPlaces);
			return parseFloat(value).toFixed(decimalPlaces);
		},
		Redondear(e) {
			const valor = parseFloat(e.target.value);
			this.frmDatosAdelanto.monto = this.round_to(valor);
		},
		async ListarRecursos() {
			return await axios
				.get(route("caj.adelanto_haberes.listar_recursos"))
				.then((response) => {
					this.usuarios = response.data.usuarios;
					this.FiltrarUsuarios();
				});
		},

		FiltrarUsuarios() {
			this.usuarios_filtrados = this.usuarios.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
			if (this.adelanto_registrado != null) {
				this.frmDatosAdelanto.usuario_id = this.adelanto_registrado.usuario_id;
			} else {
				this.frmDatosAdelanto.usuario_id = 0;
			}
		},
		AplicarPlantilla() {
			if (this.frmDatosAdelanto.usuario_id == 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Seleccione el colaborador.",
					allowOutsideClick: true,
				});
				return false;
			}

			let tipo_registro = null;

			if (this.frmDatosAdelanto.tipo == "ADELANTO") {
				tipo_registro = "ADELANTO ";
			} else if (this.frmDatosAdelanto.tipo == "CANCELACION") {
				tipo_registro = "CANCELACIÓN ";
			}

			let parts = this.$page.props.application.data_local
				.filter((item) => item.descripcion == "FECHA_CREDITOS")[0]
				.valor_fecha.split("-");

			let mes_numero = parseInt(parts[1]);
			let año = parseInt(parts[0]);

			let mi_fecha = new Date();
			mi_fecha.setMonth(mes_numero - 1);
			let mes = Intl.DateTimeFormat("es-ES", { month: "long" }).format(
				mi_fecha
			);

			this.frmDatosAdelanto.descripcion =
				tipo_registro +
				"DE HABERES DEL MES DE " +
				mes.toUpperCase() +
				" " +
				año +
				" A " +
				this.nombre_colaborador +
				".";
		},
		async Registrar() {
			this.submited = true;

			if (this.$v.frmDatosAdelanto.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos.",
				});
				return false;
			} else if (this.mi_caja == null) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Aperture CAJA para continuar.",
				});
				return false;
			}

			Swal.fire({
				title: "¿Desea continuar?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
				backdrop: true,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();

					data.append("caja_id", this.mi_caja.id);
					data.append("usuario_agencia_id", this.agencia_seleccionada);

					data.append(
						"frmDatosAdelanto",
						JSON.stringify(this.frmDatosAdelanto)
					);
					// this.$inertia.post(route("caj.adelanto_haberes.registrar"), data);
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();
							return await axios
								.post(route("caj.adelanto_haberes.registrar"), data)
								.then(async (response) => {
									this.submited = false;
									this.adelanto_registrado = response.data.adelanto_id;

									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										timer: 1200,
										showConfirmButton: false,
									});
								})
								.catch((error) => {
									Swal.showValidationMessage(
										`Ha ocurrido un error, comunicar a TI: ${error}`
									);
								});
						},
					});
				}
			});
		},
		Nuevo() {
			Swal.fire({
				title: "¿Desea registrar un nuevo adelanto?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.$inertia.get(route("caj.adelanto_haberes"));
				} else {
					return false;
				}
			});
		},
		async Imprimir(reporte) {
			let data = new FormData();

			data.append("reporte", reporte);
			data.append("adelanto_id", JSON.stringify(this.adelanto_registrado));
			data.append("agencia_id", this.agencia_seleccionada);

			// this.$inertia.post(route("caj.adelanto_haberes.exportar"), data);
			// return false;

			Swal.fire({
				title: "GENERANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("caj.adelanto_haberes.exportar"), data)
						.then((response) => {
							const origin = window.location.origin;
							const path_pdf = response.data.path_pdf;

							// Crear un IFrame
							const iframe = document.createElement("iframe");
							// Oculto el iframe
							iframe.style.display = "none";
							// Defino el source
							iframe.src = origin + path_pdf;
							// Añadir el Iframe a la vista
							document.body.appendChild(iframe);

							iframe.contentWindow.focus(); // Enfoca
							iframe.contentWindow.print(); // Imprime

							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
								timer: 1200,
								showConfirmButton: false,
							});
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-caja-adelanto-haberes {
	width: 50% !important;
	margin-left: 25% !important;
}

@media only screen and (max-width: 900px) {
	.slot-caja-adelanto-haberes {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>



