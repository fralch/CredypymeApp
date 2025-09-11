<template>
	<layout ref="layout">
		<div
			class="slot_body slot-evaluacion-financiera-convenio"
			slot="component-view"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="'EVALUACIÓN FINANCIERA - CONVENIO'"
					></headerClose>

					<div class="card-title">RESÚMEN ECONÓMICO DEL CLIENTE</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-5">
								<label class="label-title"
									>Cliente: {{ nombre_completo }}</label
								>
							</div>
							<div class="form-group col-md-2 col-6">
								<label class="label-title"
									>DNI: {{ datos_personales.dni }}</label
								>
							</div>
							<div class="text-right col-md-5">
								<div class="btn-group flex-wrap" role="group">
									<button
										class="btn btn-action btn-icon-split"
										title="Imprimir EVALUACIÓN FINANCIERA"
										@click="Imprimir()"
									>
										<span class="icon text-white">
											<i class="fas fa-print"></i>
										</span>
										<span class="text">EVAL. FINANCIERA</span>
									</button>
								</div>
							</div>
						</div>

						<div class="row mt-3">
							<div class="col-md-6">
								<fieldset class="row">
									<legend class="main-fieldset">DATOS PLANILLA</legend>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												convenio.sueldo_neto != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="AbrirEvaluacionSimple('CONVENIO', 'SUELDO_NETO')"
										>
											Sueldo neto
										</button>
									</div>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												convenio.porcentaje_descuento != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple(
													'CONVENIO',
													'PORCENTAJE_DESCUENTO'
												)
											"
										>
											Porcentaje descuento
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.otros_ingresos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionLista('CONVENIO', 'OTROS_INGRESOS')
											"
										>
											Otros ingresos
										</button>
									</div>
								</fieldset>

								<fieldset class="row">
									<legend class="main-fieldset">PRÉSTAMOS</legend>

									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.prestamos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="AbrirEvaluacionPrestamos('CONVENIO', 'PRESTAMOS')"
										>
											Préstamos
										</button>
									</div>
								</fieldset>
							</div>

							<div class="col-md-6">
								<fieldset class="row">
									<legend class="main-fieldset">COMENTARIOS</legend>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.antecedentes_cliente != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'ANTECEDENTES_CLIENTE'
												)
											"
										>
											Antecedentes del cliente
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.referencias_laborales != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'REFERENCIAS_LABORALES'
												)
											"
										>
											Referencias laborales
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.referencias_domicilio != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'REFERENCIAS_DOMICILIO'
												)
											"
										>
											Referencias del domicilio
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.referencias_pariente_vecino != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'REFERENCIAS_PARIENTE_VECINO'
												)
											"
										>
											Referencias del pariente/vecinos
										</button>
									</div>

									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.referencias_aval != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'REFERENCIAS_AVAL'
												)
											"
										>
											Referencias del aval
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.destino_prestamo != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'DESTINO_PRESTAMO'
												)
											"
										>
											Destino del préstamo
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												convenio.otros_comentarios != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'CONVENIO',
													'OTROS_COMENTARIOS'
												)
											"
										>
											Otros comentarios
										</button>
									</div>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
			</div>
			<mdlEvaluacionSimple
				ref="mdlEvaluacionSimple"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionSimple>

			<mdlEvaluacionLista
				ref="mdlEvaluacionLista"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionLista>

			<mdlEvaluacionPrestamos
				ref="mdlEvaluacionPrestamos"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionPrestamos>

			<mdlEvaluacionComentarios
				ref="mdlEvaluacionComentarios"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionComentarios>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import mdlEvaluacionSimple from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionSimple.vue";
import mdlEvaluacionLista from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionLista.vue";
import mdlEvaluacionPrestamos from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionPrestamos.vue";
import mdlEvaluacionComentarios from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionComentarios.vue";

export default {
	components: {
		layout,
		headerClose,
		mdlEvaluacionSimple,
		mdlEvaluacionLista,
		mdlEvaluacionPrestamos,
		mdlEvaluacionComentarios,
	},
	props: {
		agencia_id: Number,
		cliente_id: Number,
		datos_personales: Object,
		datos_evaluacion: Object,
		evaluacion_id: Number,
		convenio: Object,
	},
	data() {
		return {
			pariente: [],
			aval: [],
		};
	},
	computed: {
		nombre_completo: function () {
			return (
				this.datos_personales.apellido_paterno +
				" " +
				this.datos_personales.apellido_materno +
				" " +
				this.datos_personales.nombres
			);
		},
	},
	mounted() {
		this.ListarParientes();
		this.ListarAvales();
	},
	methods: {
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		AbrirEvaluacionSimple(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionSimple;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "SUELDO_NETO") {
				modal.title_modal = "SUELDO NETO";
				modal.label_text = "MONTO S/";
				modal.input_value =
					this.convenio.sueldo_neto == null
						? null
						: this.roundTo(this.convenio.sueldo_neto, 2);
			} else if (sub_grupo == "PORCENTAJE_DESCUENTO") {
				modal.title_modal = "PORCENTAJE DE DESCUENTO";
				modal.label_text = "DESCUENTO %";
				modal.input_value =
					this.convenio.porcentaje_descuento == null
						? null
						: this.roundTo(this.convenio.porcentaje_descuento, 2);
			}
			$("#mdlEvaluacionSimple").css("display", "block");
		},

		AbrirEvaluacionLista(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionLista;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "OTROS_INGRESOS") {
				modal.title_modal = "OTROS INGRESOS";

				let lista_actual =
					this.convenio.otros_ingresos == null
						? []
						: JSON.parse(this.convenio.otros_ingresos);

				let lista =
					this.convenio.otros_ingresos == null
						? []
						: JSON.parse(this.convenio.otros_ingresos);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			}

			$("#mdlEvaluacionLista").css("display", "block");
		},

		AbrirEvaluacionPrestamos(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionPrestamos;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "PRESTAMOS") {
				modal.title_modal = "PRÉSTAMOS ADICIONALES";

				let lista_actual =
					this.convenio.prestamos == null
						? []
						: JSON.parse(this.convenio.prestamos);

				let lista =
					this.convenio.prestamos == null
						? []
						: JSON.parse(this.convenio.prestamos);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
				modal.modo = "BLOQUEADO";
			}
			$("#mdlEvaluacionPrestamos").css("display", "block");
		},

		AbrirEvaluacionComentarios(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionComentarios;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "ANTECEDENTES_CLIENTE") {
				modal.title_modal = "ANTECEDENTES DEL CLIENTE";
				modal.comentario = this.convenio.antecedentes_cliente;
			} else if (sub_grupo == "REFERENCIAS_LABORALES") {
				modal.title_modal = "REFERENCIAS LABORALES";
				modal.comentario = this.convenio.referencias_negocio;
			} else if (sub_grupo == "REFERENCIAS_DOMICILIO") {
				modal.title_modal = "REFERENCIAS DEL DOMICILIO";
				modal.comentario = this.convenio.referencias_domicilio;
			} else if (sub_grupo == "REFERENCIAS_PARIENTE_VECINO") {
				modal.title_modal = "REFERENCIAS PARIENTE O VECINOS";
				modal.comentario = this.convenio.referencias_familiar_vecino;
			} else if (sub_grupo == "REFERENCIAS_AVAL") {
				modal.title_modal = "REFERENCIAS DEL AVAL";
				modal.comentario = this.convenio.referencias_aval;
			} else if (sub_grupo == "DESTINO_PRESTAMO") {
				modal.title_modal = "DESTINO DEL PRÉSTAMO";
				modal.comentario = this.convenio.destino_prestamo;
			} else if (sub_grupo == "OTROS_COMENTARIOS") {
				modal.title_modal = "OTROS COMENTARIOS";
				modal.comentario = this.convenio.otros_comentarios;
			}
			$("#mdlEvaluacionComentarios").css("display", "block");
		},
		ListarParientes() {
			let self = this;

			return axios
				.post(
					route("cli.listado_registro.listar_parientes", {
						cliente_id: self.datos_personales.id,
						agencia_id: self.agencia_id,
					})
				)
				.then(function (response) {
					let resultado = response.data;
					if (resultado.length > 0) {
						self.pariente = resultado.filter((item) => item.vinculado == 1);
					} else {
						self.pariente = resultado;
					}
				});
		},
		ListarAvales() {
			let self = this;

			return axios
				.post(
					route("cli.listado_registro.listar_avales", {
						cliente_id: self.datos_personales.id,
						agencia_id: self.agencia_id,
					})
				)
				.then(function (response) {
					let resultado = response.data;
					if (resultado.length > 0) {
						self.aval = resultado.filter((item) => item.vinculado == 1);
					} else {
						self.aval = resultado;
					}
				});
		},
		Imprimir() {
			let data = new FormData();
			data.append("modo", "convenio");
			data.append("agencia_id", this.agencia_id);
			data.append("datos_personales", JSON.stringify(this.datos_personales));
			data.append("datos_pariente", JSON.stringify(this.pariente));
			data.append("datos_aval", JSON.stringify(this.aval));
			data.append("convenio", JSON.stringify(this.convenio));

			// this.$inertia.post(route("cre.evaluacion.exportar"), data);
			// return false;
			Swal.fire({
				title: "GENERANDO ARCHIVO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("cre.evaluacion.exportar"), data)
						.then(function (response) {
							let origin = window.location.origin;

							let path_pdf = response.data.path_pdf;

							// Crear un IFrame.
							let iframe = document.createElement("iframe");
							// Ocultar el IFrame.
							iframe.style.display = "none";
							// Definir el source.
							iframe.src = origin + path_pdf;
							// Añadir el IFrame a una página web.
							document.body.appendChild(iframe);
							iframe.contentWindow.focus();
							iframe.contentWindow.print(); // Imprimir.
						})
						.finally(() => {
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
.slot-evaluacion-financiera-convenio {
	width: 50% !important;
	margin-left: 25% !important;
}

.button-empty {
	background: white;
	color: var(--colorAlto);
	width: 100% !important;
	height: 100% !important;
	font-size: 11.5px !important;
}

.button-filled {
	background: var(--colorMedio);
	color: white;
	border-color: var(--plomoOscuroEmpresarial);
	width: 100% !important;
	height: 100% !important;
	font-size: 11.5px !important;
}

.main-fieldset {
	font-size: 10px !important;
	background: var(--plomoOscuroEmpresarial);
	color: white;
}

@media only screen and (max-width: 900px) {
	.slot-evaluacion-financiera-convenio {
		width: 99% !important;
		margin-left: 0.5% !important;
		margin-top: 15% !important;
	}
	.mdlEvaluacionBienes {
		width: 99% !important;
		margin-left: 0.5% !important;
		margin-top: 25% !important;
	}
}
</style>

