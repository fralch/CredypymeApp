<template>
	<layout ref="layout">
		<div class="slot_body slot_evaluacion_financiera" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'EVALUACIÓN FINANCIERA'"></headerClose>

					<div class="card-title">RESÚMEN ECONÓMICO DEL CLIENTE</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-5">
								<label class="label-title"
									>CLIENTE: {{ nombre_completo }}</label
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
										@click="Imprimir('EVALUACION_FINANCIERA')"
									>
										<span class="icon text-white">
											<i class="fas fa-print"></i>
										</span>
										<span class="text">EVAL. FINANCIERA</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										title="Imprimir COMENTARIOS"
										@click="Imprimir('COMENTARIOS')"
									>
										<span class="icon text-white">
											<i class="fas fa-print"></i>
										</span>
										<span class="text">COMENTARIOS</span>
									</button>
								</div>
							</div>
						</div>

						<div class="row mt-3">
							<div class="col-md-6">
								<fieldset class="row">
									<legend class="main-fieldset">ACTIVO CORRIENTE</legend>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												activo_corriente.caja != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="AbrirEvaluacionSimple('ACTIVO_CORRIENTE', 'CAJA')"
										>
											Caja (Efectivo)
										</button>
									</div>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												activo_corriente.adelantos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple(
													'ACTIVO_CORRIENTE',
													'ADELANTO_PROVEEDORES'
												)
											"
										>
											Adelantos realizados a proveedores
										</button>
									</div>
									<div class="w-100 mt-1"></div>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												activo_corriente.bancos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionLista('ACTIVO_CORRIENTE', 'BANCOS')
											"
										>
											Bancos
										</button>
									</div>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												activo_corriente.varios != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple('ACTIVO_CORRIENTE', 'VARIOS')
											"
										>
											Varios
										</button>
									</div>
									<div class="w-100 mt-1"></div>
									<div class="col col-md-6">
										<button
											class="btn btn-action"
											:class="[
												activo_corriente.cuentas_cobrar != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionLista(
													'ACTIVO_CORRIENTE',
													'CUENTAS_COBRAR'
												)
											"
										>
											Cuentas por cobrar a clientes
										</button>
									</div>
									<div class="col col-md-12 mt-1">
										<button
											class="btn btn-action"
											:class="[
												activo_corriente.inventario_mercaderia != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionDetalle(
													'ACTIVO_CORRIENTE',
													'INVENTARIO_MERCADERIA'
												)
											"
										>
											Inventario y mercadería
										</button>
									</div>
								</fieldset>
								<fieldset class="row">
									<legend class="main-fieldset">ACTIVO NO CORRIENTE</legend>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												activo_no_corriente.muebles_enseres != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionLista(
													'ACTIVO_NO_CORRIENTE',
													'MUEBLES_ENSERES'
												)
											"
										>
											Muebles y enseres
										</button>
									</div>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												activo_no_corriente.inmueble_maquinaria_equipo != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionBienes(
													'ACTIVO_NO_CORRIENTE',
													'INMUEBLE_MAQUINARIA_EQUIPO'
												)
											"
										>
											Inmueble, maquinaria y equipo
										</button>
									</div>
									<div class="w-100 mt-1"></div>
								</fieldset>
								<fieldset class="row">
									<legend class="main-fieldset">PASIVO CORRIENTE</legend>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												pasivo_corriente.adelanto_proveedores != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionLista(
													'PASIVO_CORRIENTE',
													'ADELANTO_PROVEEDORES'
												)
											"
										>
											Adelanto recibido de proveedores
										</button>
									</div>
									<div class="col">
										<button
											class="btn btn-action"
											:class="[
												pasivo_corriente.otros != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple('PASIVO_CORRIENTE', 'OTROS')
											"
										>
											Otros
										</button>
									</div>
									<div class="w-100 mt-1"></div>
									<div class="col col-md-12">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.prestamos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple(
													'PASIVO_CORRIENTE',
													'DEUDA_CORTO_PLAZO'
												)
											"
										>
											Deuda a corto plazo menor a 1 año
										</button>
									</div>
								</fieldset>
								<fieldset class="row">
									<legend class="main-fieldset">PASIVO NO CORRIENTE</legend>
									<div class="col col-md-12">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.prestamos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple(
													'PASIVO_NO_CORRIENTE',
													'DEUDA_LARGO_PLAZO'
												)
											"
										>
											Deuda a largo plazo mayor a 1 año
										</button>
									</div>
								</fieldset>
							</div>
							<div class="col-md-3">
								<fieldset class="row">
									<legend class="main-fieldset">VENTAS POR</legend>
									<div class="col col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.ventas_detallado != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionDetalle('FLUJO_CAJA', 'VENTAS_DETALLADO')
											"
										>
											Detallado
										</button>
									</div>
									<div class="col col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.ventas_monto != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionSimple('FLUJO_CAJA', 'VENTAS_MONTO')
											"
										>
											Monto
										</button>
									</div>
									<div class="w-100 mt-1"></div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.costo_ventas != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionDetalle('FLUJO_CAJA', 'COSTO_VENTAS')
											"
										>
											Costo de ventas
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.costos_operativos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionUnidades(
													'FLUJO_CAJA',
													'COSTOS_OPERATIVOS'
												)
											"
										>
											Costos operativos
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.otros_ingresos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionLista('FLUJO_CAJA', 'OTROS_INGRESOS')
											"
										>
											Otros ingresos
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.gastos_familiares != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionUnidades(
													'FLUJO_CAJA',
													'GASTOS_FAMILIARES'
												)
											"
										>
											Unidad familiar
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.prestamos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionPrestamos('FLUJO_CAJA', 'PRESTAMOS')
											"
										>
											Préstamos adicionales
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												flujo_caja.vehiculos != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionVehiculos('FLUJO_CAJA', 'VEHICULOS')
											"
										>
											Vehículo
										</button>
									</div>
								</fieldset>
							</div>
							<div class="col-md-3">
								<fieldset class="row">
									<legend class="main-fieldset">COMENTARIOS</legend>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												comentarios.antecedentes_cliente != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
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
												comentarios.referencias_negocio != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
													'REFERENCIAS_NEGOCIO'
												)
											"
										>
											Referencias del negocio
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												comentarios.referencias_domicilio != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
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
												comentarios.referencias_familiar_vecino != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
													'REFERENCIAS_FAMILIAR_VECINO'
												)
											"
										>
											Referencias del familiar o vecino
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												comentarios.referencias_pariente != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
													'REFERENCIAS_PARIENTE'
												)
											"
										>
											Referencias del pariente
										</button>
									</div>
									<div class="col col-md-12 mt-3 col-6">
										<button
											class="btn btn-action"
											:class="[
												comentarios.referencias_aval != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
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
												comentarios.destino_prestamo != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
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
												comentarios.otros_comentarios != null
													? 'button-filled'
													: 'button-empty',
											]"
											@click="
												AbrirEvaluacionComentarios(
													'COMENTARIOS',
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

			<mdlEvaluacionDetalle
				ref="mdlEvaluacionDetalle"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionDetalle>

			<mdlEvaluacionBienes
				ref="mdlEvaluacionBienes"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:datos_personales="datos_personales"
				:agencia_id="agencia_id"
			></mdlEvaluacionBienes>
			<mdlEvaluacionUnidades
				ref="mdlEvaluacionUnidades"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionUnidades>
			<mdlEvaluacionPrestamos
				ref="mdlEvaluacionPrestamos"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionPrestamos>
			<mdlEvaluacionVehiculos
				ref="mdlEvaluacionVehiculos"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionVehiculos>
			<mdlEvaluacionComentarios
				ref="mdlEvaluacionComentarios"
				:evaluacion_id="evaluacion_id"
				:cliente_id="cliente_id"
				:agencia_id="agencia_id"
			></mdlEvaluacionComentarios>

			<rptEvaluacionComentarios
				ref="rptEvaluacionComentarios"
				:agencia_id="agencia_id"
			>
			</rptEvaluacionComentarios>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import mdlEvaluacionSimple from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionSimple.vue";
import mdlEvaluacionLista from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionLista.vue";
import mdlEvaluacionDetalle from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionDetalle.vue";
import mdlEvaluacionBienes from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionBienes.vue";
import mdlEvaluacionUnidades from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionUnidades.vue";
import mdlEvaluacionPrestamos from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionPrestamos.vue";
import mdlEvaluacionVehiculos from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionVehiculos.vue";
import mdlEvaluacionComentarios from "@/Pages/Creditos/Creditos/Components/mdlEvaluacionComentarios.vue";
import rptEvaluacionComentarios from "@/Pages/Creditos/Creditos/Reports/rptEvaluacionComentarios.vue";

export default {
	components: {
		layout,
		headerClose,
		mdlEvaluacionSimple,
		mdlEvaluacionLista,
		mdlEvaluacionDetalle,
		mdlEvaluacionBienes,
		mdlEvaluacionUnidades,
		mdlEvaluacionPrestamos,
		mdlEvaluacionVehiculos,
		mdlEvaluacionComentarios,
		rptEvaluacionComentarios,
	},
	props: {
		agencia_id: Number,
		cliente_id: Number,
		datos_personales: Object,
		datos_evaluacion: Object,
		evaluacion_id: Number,
		activo_corriente: Object,
		activo_no_corriente: Object,
		pasivo_corriente: Object,
		flujo_caja: Object,
		comentarios: Object,
	},
	data() {
		return {
			pariente: [],
			aval: [],
			totales: {
				activo: {},
				pasivo: {},
				patrimonio: {},
				ingresos_egresos: {},
				prestamos: {},
			},
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
		isEmpty(obj) {
			return Object.keys(obj).length === 0;
		},
		isNullValue(property) {
			if (property == null) {
				return this.roundTo(0, 2);
			} else {
				return this.roundTo(property, 2);
			}
		},
		isNullArray(property) {
			if (property == null) {
				return [];
			} else {
				return JSON.parse(property);
			}
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
		AbrirEvaluacionSimple(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionSimple;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "CAJA") {
				modal.title_modal = "CAJA (Efectivo)";
				modal.label_text = "MONTO S/";
				modal.input_value =
					this.activo_corriente.caja == null
						? null
						: this.roundTo(this.activo_corriente.caja, 2);
			} else if (sub_grupo == "ADELANTO_PROVEEDORES") {
				modal.title_modal = "ADELANTO A PROVEEDORES";
				modal.label_text = "MONTO S/";
				modal.input_value =
					this.activo_corriente.adelantos == null
						? null
						: this.roundTo(this.activo_corriente.adelantos, 2);
			} else if (sub_grupo == "VARIOS") {
				modal.title_modal = "VARIOS";
				modal.label_text = "MONTO S/";
				modal.input_value =
					this.activo_corriente.varios == null
						? null
						: this.roundTo(this.activo_corriente.varios, 2);
			} else if (sub_grupo == "OTROS") {
				modal.title_modal = "OTROS";
				modal.label_text = "MONTO S/";
				modal.input_value =
					this.pasivo_corriente.otros == null
						? null
						: this.roundTo(this.pasivo_corriente.otros, 2);
			} else if (sub_grupo == "VENTAS_MONTO") {
				modal.message =
					"Recuerde que si ingresa un monto de ventas, automáticamente se quitarán las ventas por detalle";
				modal.title_modal = "VENTAS (monto)";
				modal.label_text = "MONTO S/";
				modal.input_value =
					this.flujo_caja.ventas_monto == null
						? null
						: this.roundTo(this.flujo_caja.ventas_monto, 2);
			} else if (sub_grupo == "DEUDA_CORTO_PLAZO") {
				modal.message =
					"El siguiente monto fue calculado en base a los préstamos adicionales del cliente";
				modal.title_modal = "PASIVO CORRIENTE";
				modal.label_text = "Deuda a corto plazo menor a 1 año S/ ";

				let deuda_corto_plazo = 0;
				if (!(this.flujo_caja.prestamos == null)) {
					let prestamos = JSON.parse(this.flujo_caja.prestamos);
					let prestamos_vigentes = prestamos.filter(
						(item) => item.plazo > item.cuotas_pagadas
					);
					prestamos_vigentes.forEach((element) => {
						if (element.plazo - element.cuotas_pagadas > 12) {
							deuda_corto_plazo += parseFloat(element.monto_cuota * 12);
						} else {
							deuda_corto_plazo += parseFloat(
								(element.plazo - element.cuotas_pagadas) * element.monto_cuota
							);
						}
					});
				}
				modal.input_value =
					deuda_corto_plazo == null ? null : this.roundTo(deuda_corto_plazo, 2);
			} else if (sub_grupo == "DEUDA_LARGO_PLAZO") {
				modal.message =
					"El siguiente monto fue calculado en base a los préstamos adicionales del cliente";
				modal.title_modal = "PASIVO NO CORRIENTE";
				modal.label_text = "Deuda a largo plazo mayor a 1 año S/ ";

				let deuda_largo_plazo = 0;
				if (!(this.flujo_caja.prestamos == null)) {
					let prestamos = JSON.parse(this.flujo_caja.prestamos);
					let prestamos_vigentes = prestamos.filter(
						(item) => item.plazo > item.cuotas_pagadas
					);
					prestamos_vigentes.forEach((element) => {
						if (element.plazo - element.cuotas_pagadas > 12) {
							deuda_largo_plazo += parseFloat(
								(element.plazo - element.cuotas_pagadas - 12) *
									element.monto_cuota
							);
						}
					});
				}
				modal.input_value =
					deuda_largo_plazo == null ? null : this.roundTo(deuda_largo_plazo, 2);
			}

			$("#mdlEvaluacionSimple").css("display", "block");
		},
		AbrirEvaluacionLista(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionLista;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "BANCOS") {
				modal.title_modal = "BANCOS";

				let lista_actual =
					this.activo_corriente.bancos == null
						? []
						: JSON.parse(this.activo_corriente.bancos);
				let lista =
					this.activo_corriente.bancos == null
						? []
						: JSON.parse(this.activo_corriente.bancos);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			} else if (sub_grupo == "CUENTAS_COBRAR") {
				modal.title_modal = "CUENTAS POR COBRAR A CLIENTES";
				let lista_actual =
					this.activo_corriente.cuentas_cobrar == null
						? []
						: JSON.parse(this.activo_corriente.cuentas_cobrar);
				let lista =
					this.activo_corriente.cuentas_cobrar == null
						? []
						: JSON.parse(this.activo_corriente.cuentas_cobrar);
				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			} else if (sub_grupo == "MUEBLES_ENSERES") {
				modal.title_modal = "MUEBLES Y ENSERES";
				let lista_actual =
					this.activo_no_corriente.muebles_enseres == null
						? []
						: JSON.parse(this.activo_no_corriente.muebles_enseres);
				let lista =
					this.activo_no_corriente.muebles_enseres == null
						? []
						: JSON.parse(this.activo_no_corriente.muebles_enseres);
				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			} else if (sub_grupo == "ADELANTO_PROVEEDORES") {
				modal.title_modal = "ADELANTO RECIBIDO DE PROVEEDORES";
				let lista_actual =
					this.pasivo_corriente.adelanto_proveedores == null
						? []
						: JSON.parse(this.pasivo_corriente.adelanto_proveedores);
				let lista =
					this.pasivo_corriente.adelanto_proveedores == null
						? []
						: JSON.parse(this.pasivo_corriente.adelanto_proveedores);
				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			} else if (sub_grupo == "OTROS_INGRESOS") {
				modal.title_modal = "OTROS INGRESOS";
				let lista_actual =
					this.flujo_caja.otros_ingresos == null
						? []
						: JSON.parse(this.flujo_caja.otros_ingresos);
				let lista =
					this.flujo_caja.otros_ingresos == null
						? []
						: JSON.parse(this.flujo_caja.otros_ingresos);
				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			}

			$("#mdlEvaluacionLista").css("display", "block");
		},
		AbrirEvaluacionDetalle(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionDetalle;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "INVENTARIO_MERCADERIA") {
				modal.title_modal = "INVENTARIO Y MERCADERÍA";

				let lista_actual =
					this.activo_corriente.inventario_mercaderia == null
						? []
						: JSON.parse(this.activo_corriente.inventario_mercaderia);

				let lista =
					this.activo_corriente.inventario_mercaderia == null
						? []
						: JSON.parse(this.activo_corriente.inventario_mercaderia);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
			} else if (sub_grupo == "VENTAS_DETALLADO") {
				modal.title_modal = "VENTAS (Detalles)";

				let lista_actual =
					this.flujo_caja.ventas_detallado == null
						? []
						: JSON.parse(this.flujo_caja.ventas_detallado);
				let lista =
					this.flujo_caja.ventas_detallado == null
						? []
						: JSON.parse(this.flujo_caja.ventas_detallado);
				let lista_vinculada =
					this.flujo_caja.costo_ventas == null
						? []
						: JSON.parse(this.flujo_caja.costo_ventas);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos_vinculado = lista_vinculada;
				modal.lista_datos = lista;
			} else if (sub_grupo == "COSTO_VENTAS") {
				if (this.flujo_caja.ventas_detallado == null) {
					Swal.fire({
						icon: "warning",
						title: "¡Ups!",
						text: "Primero debe registrar el detalle de VENTAS",
						allowOutsideClick: true,
					});
					return false;
				}
				modal.title_modal = "COSTO DE VENTAS";

				let lista_actual =
					this.flujo_caja.costo_ventas == null
						? []
						: JSON.parse(this.flujo_caja.costo_ventas);
				let lista =
					this.flujo_caja.costo_ventas == null
						? []
						: JSON.parse(this.flujo_caja.costo_ventas);
				let lista_vinculada =
					this.flujo_caja.ventas_detallado == null
						? []
						: JSON.parse(this.flujo_caja.ventas_detallado);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos_vinculado = lista_vinculada;
				modal.lista_datos = lista;
			}

			$("#mdlEvaluacionDetalle").css("display", "block");
		},
		AbrirEvaluacionBienes(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionBienes;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "INMUEBLE_MAQUINARIA_EQUIPO") {
				modal.title_modal = "BIENES REGISTRADOS PARA LA DECLARACIÓN JURADA";

				let lista_actual =
					this.activo_no_corriente.inmueble_maquinaria_equipo == null
						? []
						: JSON.parse(this.activo_no_corriente.inmueble_maquinaria_equipo);

				let lista =
					this.activo_no_corriente.inmueble_maquinaria_equipo == null
						? []
						: JSON.parse(this.activo_no_corriente.inmueble_maquinaria_equipo);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
				modal.modo = "BLOQUEADO";
			}
			$("#mdlEvaluacionBienes").css("display", "block");
		},
		AbrirEvaluacionUnidades(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionUnidades;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;
			if (sub_grupo == "COSTOS_OPERATIVOS") {
				modal.title_modal = "COSTOS OPERATIVOS";
				let lista =
					this.flujo_caja.costos_operativos == null
						? []
						: JSON.parse(this.flujo_caja.costos_operativos);

				modal.lista_datos = lista;
			} else if (sub_grupo == "GASTOS_FAMILIARES") {
				modal.title_modal = "GASTOS FAMILIARES";
				let lista =
					this.flujo_caja.gastos_familiares == null
						? []
						: JSON.parse(this.flujo_caja.gastos_familiares);

				modal.lista_datos = lista;
			}
			$("#mdlEvaluacionUnidades").css("display", "block");
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
					this.flujo_caja.prestamos == null
						? []
						: JSON.parse(this.flujo_caja.prestamos);

				let lista =
					this.flujo_caja.prestamos == null
						? []
						: JSON.parse(this.flujo_caja.prestamos);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
				modal.modo = "BLOQUEADO";
			}
			$("#mdlEvaluacionPrestamos").css("display", "block");
		},
		AbrirEvaluacionVehiculos(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionVehiculos;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "VEHICULOS") {
				modal.title_modal = "VEHÍCULOS";

				let lista_actual =
					this.flujo_caja.vehiculos == null
						? []
						: JSON.parse(this.flujo_caja.vehiculos);

				let lista =
					this.flujo_caja.vehiculos == null
						? []
						: JSON.parse(this.flujo_caja.vehiculos);

				modal.lista_datos_actuales = lista_actual;
				modal.lista_datos = lista;
				modal.modo = "BLOQUEADO";
			}
			$("#mdlEvaluacionVehiculos").css("display", "block");
		},
		AbrirEvaluacionComentarios(grupo, sub_grupo) {
			let modal = this.$refs.mdlEvaluacionComentarios;

			modal.grupo = grupo;
			modal.sub_grupo = sub_grupo;
			modal.no_editable = true;
			modal.submited = false;

			if (sub_grupo == "ANTECEDENTES_CLIENTE") {
				modal.title_modal = "ANTECEDENTES DEL CLIENTE";
				modal.comentario = this.comentarios.antecedentes_cliente;
			} else if (sub_grupo == "REFERENCIAS_NEGOCIO") {
				modal.title_modal = "REFERENCIAS DEL NEGOCIO";
				modal.comentario = this.comentarios.referencias_negocio;
			} else if (sub_grupo == "REFERENCIAS_DOMICILIO") {
				modal.title_modal = "REFERENCIAS DEL DOMICILIO";
				modal.comentario = this.comentarios.referencias_domicilio;
			} else if (sub_grupo == "REFERENCIAS_FAMILIAR_VECINO") {
				modal.title_modal = "REFERENCIAS FAMILIARES O VECINOS";
				modal.comentario = this.comentarios.referencias_familiar_vecino;
			} else if (sub_grupo == "REFERENCIAS_PARIENTE") {
				modal.title_modal = "REFERENCIAS DEL PARIENTE";
				modal.comentario = this.comentarios.referencias_pariente;
			} else if (sub_grupo == "REFERENCIAS_AVAL") {
				modal.title_modal = "REFERENCIAS DEL AVAL";
				modal.comentario = this.comentarios.referencias_aval;
			} else if (sub_grupo == "DESTINO_PRESTAMO") {
				modal.title_modal = "DESTINO DEL PRÉSTAMO";
				modal.comentario = this.comentarios.destino_prestamo;
			} else if (sub_grupo == "OTROS_COMENTARIOS") {
				modal.title_modal = "OTROS COMENTARIOS";
				modal.comentario = this.comentarios.otros_comentarios;
			}
			$("#mdlEvaluacionComentarios").css("display", "block");
		},
		Exportar(nombre_reporte) {
			let self = this;
			let texto = "";
			if (nombre_reporte == "EVALUACION_FINANCIERA") {
				texto = "la EVALUACIÓN FINANCIERA";
			} else if (nombre_reporte == "COMENTARIOS") {
				texto = "los COMENTARIOS";
			} else {
				return false;
			}
			Swal.fire({
				icon: "question",
				text: "¿Desea imprimir " + texto + "?",
				confirmButtonText:
					'<i class="fas fa-print" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					async function EnviarDatos() {
						if (nombre_reporte == "EVALUACION_FINANCIERA") {
							let reporte = self.$refs.rptEvaluacionFinanciera;
							reporte.datos_personales = self.datos_personales;
							reporte.datos_evaluacion = self.datos_evaluacion;
							reporte.activo_corriente = self.activo_corriente;
							reporte.activo_no_corriente = self.activo_no_corriente;
							reporte.pasivo_corriente = self.pasivo_corriente;
							reporte.flujo_caja = self.flujo_caja;

							document.title =
								"rptEvaluacionFinanciera_" + self.datos_personales.dni;
							return reporte;
						} else if (nombre_reporte == "COMENTARIOS") {
							let reporte = self.$refs.rptEvaluacionComentarios;
							reporte.datos_personales = self.datos_personales;
							reporte.comentarios = self.comentarios;

							await reporte.ListarParientes();
							await reporte.ListarAvales();

							document.title =
								"rptEvaluacionComentarios_" + self.datos_personales.dni;
							return reporte;
						}
					}

					EnviarDatos().then(() => {
						if (nombre_reporte == "EVALUACION_FINANCIERA") {
							$("#rptEvaluacionFinanciera").css("display", "block");

							var css = "@page { size: portrait ; }",
								head =
									document.head || document.getElementsByTagName("head")[0],
								style = document.createElement("style");

							style.type = "text/css";
							style.media = "print";

							if (style.styleSheet) {
								style.styleSheet.cssText = css;
							} else {
								style.appendChild(document.createTextNode(css));
							}

							head.appendChild(style);

							$("#rptEvaluacionFinanciera").print();

							head.removeChild(style);

							$("#rptEvaluacionFinanciera").css("display", "none");
						} else if (nombre_reporte == "COMENTARIOS") {
							$("#rptEvaluacionComentarios").css("display", "block");

							var css = "@page { size: portrait ; }",
								head =
									document.head || document.getElementsByTagName("head")[0],
								style = document.createElement("style");

							style.type = "text/css";
							style.media = "print";

							if (style.styleSheet) {
								style.styleSheet.cssText = css;
							} else {
								style.appendChild(document.createTextNode(css));
							}

							head.appendChild(style);

							$("#rptEvaluacionComentarios").print();

							head.removeChild(style);
							$("#rptEvaluacionComentarios").css("display", "none");
						}
					});
				} else {
					return false;
				}
			});
		},

		Imprimir(nombre_reporte) {
			let self = this;
			let texto = "";
			if (nombre_reporte == "EVALUACION_FINANCIERA") {
				texto = "la EVALUACIÓN FINANCIERA";
			} else if (nombre_reporte == "COMENTARIOS") {
				texto = "los COMENTARIOS";
			} else {
				return false;
			}
			Swal.fire({
				icon: "question",
				text: "¿Desea imprimir " + texto + "?",
				confirmButtonText:
					'<i class="fas fa-print" style="color:white;"></i>   Si',
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
						"datos_personales",
						JSON.stringify(this.datos_personales)
					);
					data.append("datos_pariente", JSON.stringify(this.pariente));
					data.append("datos_aval", JSON.stringify(this.aval));
					data.append(
						"datos_evaluacion",
						JSON.stringify(this.datos_evaluacion)
					);

					if (nombre_reporte == "EVALUACION_FINANCIERA") {
						this.CalcularTotales();

						let evaluacion_financiera = {
							activo_corriente: this.activo_corriente,
							activo_no_corriente: this.activo_no_corriente,
							pasivo_corriente: this.pasivo_corriente,
							flujo_caja: this.flujo_caja,
						};
						data.append("modo", "microempresarial");
						data.append("totales", JSON.stringify(this.totales));
						data.append(
							"evaluacion_financiera",
							JSON.stringify(evaluacion_financiera)
						);
					} else if (nombre_reporte == "COMENTARIOS") {
						data.append("modo", "comentarios");
						data.append("comentarios", JSON.stringify(this.comentarios));
					}

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

									let path_xlsx = response.data.path_xlsx;

									// let path_xlsx = response.data.path_xlsx;

									// const link = document.createElement("a");
									// link.href = origin + path_xlsx;
									// link.download = "rptDesembolsosFacturados.xlsx";
									// link.click();
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
				}
			});
		},

		CalcularTotales() {
			this.Calcular_Activo();
			this.Calcular_Pasivo();
			this.Calcular_Patrimonio();
			this.Calcular_Ingresos_Egresos();
			this.Calcular_Prestamos();
		},

		Calcular_Activo() {
			let total = 0;
			let total_activo_corriente = 0;
			let total_bancos = 0;
			let total_cuentas_cobrar = 0;

			let total_inventario = 0;
			let total_activo_no_corriente = 0;
			let total_muebles_enseres = 0;
			let total_inmueble_maquinaria = 0;

			if (
				!this.isEmpty(this.activo_corriente) ||
				!this.isEmpty(this.activo_no_corriente)
			) {
				let ac = this.activo_corriente;
				let anc = this.activo_no_corriente;
				this.isNullArray(ac.bancos).forEach((element) => {
					total_bancos += parseFloat(element.monto);
				});

				this.isNullArray(ac.cuentas_cobrar).forEach((element) => {
					total_cuentas_cobrar += parseFloat(element.monto);
				});

				this.isNullArray(ac.inventario_mercaderia).forEach((element) => {
					total_inventario += parseFloat(
						element.cantidad * element.precio_unitario
					);
				});
				this.isNullArray(anc.muebles_enseres).forEach((element) => {
					total_muebles_enseres += parseFloat(element.monto);
				});

				this.isNullArray(anc.inmueble_maquinaria_equipo).forEach((element) => {
					if (element.disponible) {
						total_inmueble_maquinaria += parseFloat(
							element.cantidad * element.precio_actual
						);
					}
				});

				total_activo_corriente =
					parseFloat(this.isNullValue(ac.caja)) +
					total_bancos +
					total_cuentas_cobrar +
					parseFloat(this.isNullValue(ac.adelantos)) +
					parseFloat(this.isNullValue(ac.varios));

				total_activo_no_corriente = parseFloat(
					total_muebles_enseres + total_inmueble_maquinaria
				);

				total = parseFloat(
					total_activo_corriente + total_inventario + total_activo_no_corriente
				);
			}

			let obj = {
				total: this.roundTo(total, 2),
				total_activo_corriente: this.roundTo(total_activo_corriente, 2),
				total_bancos: this.roundTo(total_bancos, 2),
				total_cuentas_cobrar: this.roundTo(total_cuentas_cobrar, 2),
				total_inventario: this.roundTo(total_inventario, 2),
				total_activo_no_corriente: this.roundTo(total_activo_no_corriente, 2),
				total_muebles_enseres: this.roundTo(total_muebles_enseres, 2),
				total_inmueble_maquinaria: this.roundTo(total_inmueble_maquinaria, 2),
			};

			this.totales.activo = obj;
		},
		Calcular_Pasivo() {
			let total = 0;
			let total_pasivo_corriente = 0;
			let total_deuda_corto_plazo = 0;
			let total_adelanto_proveedores = 0;
			let total_pasivo_no_corriente = 0;
			let total_deuda_largo_plazo = 0;

			if (!this.isEmpty(this.pasivo_corriente)) {
				let pc = this.pasivo_corriente;

				let prestamos_vigentes = this.isNullArray(
					this.flujo_caja.prestamos
				).filter((item) => item.plazo > item.cuotas_pagadas);

				prestamos_vigentes.forEach((element) => {
					if (element.plazo - element.cuotas_pagadas > 12) {
						total_deuda_corto_plazo += parseFloat(element.monto_cuota * 12);
					} else {
						total_deuda_corto_plazo += parseFloat(
							(element.plazo - element.cuotas_pagadas) * element.monto_cuota
						);
					}
				});

				this.isNullArray(pc.adelanto_proveedores).forEach((element) => {
					total_adelanto_proveedores += parseFloat(element.monto);
				});

				prestamos_vigentes.forEach((element) => {
					if (element.plazo - element.cuotas_pagadas > 12) {
						total_deuda_largo_plazo += parseFloat(
							(element.plazo - element.cuotas_pagadas - 12) *
								element.monto_cuota
						);
					}
				});

				total_pasivo_corriente =
					total_deuda_corto_plazo +
					total_adelanto_proveedores +
					parseFloat(this.isNullValue(pc.otros));

				total_pasivo_no_corriente = total_deuda_largo_plazo;

				total = parseFloat(total_pasivo_corriente + total_pasivo_no_corriente);
			}

			let obj = {
				total: this.roundTo(total, 2),
				total_pasivo_corriente: this.roundTo(total_pasivo_corriente, 2),
				total_deuda_corto_plazo: this.roundTo(total_deuda_corto_plazo, 2),
				total_adelanto_proveedores: this.roundTo(total_adelanto_proveedores, 2),
				total_pasivo_no_corriente: this.roundTo(total_pasivo_no_corriente, 2),
				total_deuda_largo_plazo: this.roundTo(total_deuda_largo_plazo, 2),
			};

			this.totales.pasivo = obj;
		},
		Calcular_Patrimonio() {
			this.totales.patrimonio = this.roundTo(
				parseFloat(this.totales.activo.total) -
					parseFloat(this.totales.pasivo.total),
				2
			);
		},
		Calcular_Ingresos_Egresos() {
			let total_ventas = 0;
			let total_costo_ventas = 0;
			let total_utilidad_bruta = 0;
			let total_costos_operativos = 0;
			let total_utilidad_neta = 0;
			let total_otros_ingresos = 0;
			let total_gastos_familiares = 0;
			let total_prestamos = 0;
			let total_excedente = 0;

			if (!this.isEmpty(this.flujo_caja)) {
				let fc = this.flujo_caja;

				this.isNullArray(fc.ventas_detallado).forEach((element) => {
					total_ventas +=
						parseFloat(element.cantidad) * parseFloat(element.precio_unitario);
				});

				this.isNullArray(fc.costo_ventas).forEach((element) => {
					total_costo_ventas +=
						parseFloat(element.cantidad) * parseFloat(element.precio_unitario);
				});

				this.isNullArray(fc.costos_operativos).forEach((element) => {
					total_costos_operativos += parseFloat(element.monto);
				});
				this.isNullArray(fc.otros_ingresos).forEach((element) => {
					total_otros_ingresos += parseFloat(element.monto);
				});
				this.isNullArray(fc.gastos_familiares).forEach((element) => {
					total_gastos_familiares += parseFloat(element.monto);
				});
				this.isNullArray(fc.prestamos).forEach((element) => {
					if (element.plazo > element.cuotas_pagadas) {
						total_prestamos += parseFloat(element.monto_cuota);
					}
				});

				total_utilidad_bruta = total_ventas - total_costo_ventas;
				total_utilidad_neta = total_utilidad_bruta - total_costos_operativos;
				total_excedente =
					total_utilidad_neta +
					total_otros_ingresos -
					total_gastos_familiares -
					total_prestamos;
			}

			let obj = {
				total_ventas: this.roundTo(total_ventas, 2),
				total_costo_ventas: this.roundTo(total_costo_ventas, 2),
				total_utilidad_bruta: this.roundTo(total_utilidad_bruta, 2),
				total_costos_operativos: this.roundTo(total_costos_operativos, 2),
				total_utilidad_neta: this.roundTo(total_utilidad_neta, 2),
				total_otros_ingresos: this.roundTo(total_otros_ingresos, 2),
				total_gastos_familiares: this.roundTo(total_gastos_familiares, 2),
				total_excedente: this.roundTo(total_excedente, 2),
			};

			this.totales.ingresos_egresos = obj;
		},
		Calcular_Prestamos() {
			let total_monto_prestamo = 0;
			let total_saldo = 0;
			if (!this.isEmpty(this.flujo_caja)) {
				let fc = this.flujo_caja;

				let prestamos_vigentes = this.isNullArray(
					this.flujo_caja.prestamos
				).filter((item) => item.plazo > item.cuotas_pagadas);

				prestamos_vigentes.forEach((element) => {
					total_monto_prestamo += parseFloat(element.monto_prestamo);
					total_saldo += parseFloat(
						(element.plazo - element.cuotas_pagadas) * element.monto_cuota
					);
				});
			}

			let obj = {
				total_monto_prestamo: this.roundTo(total_monto_prestamo, 2),
				total_saldo: this.roundTo(total_saldo, 2),
			};

			this.totales.prestamos = obj;
		},
	},
};
</script>

<style lang="css">
.slot_evaluacion_financiera {
	width: 70% !important;
	margin-left: 15% !important;
}

.button-empty {
	background: white;
	color: var(--colorAlto);
	width: 100% !important;
	height: 100% !important;
	font-size: 11px !important;
}

.button-filled {
	background: var(--colorMedio);
	color: white;
	border-color: var(--plomoOscuroEmpresarial);
	width: 100% !important;
	height: 100% !important;
	font-size: 11px !important;
}

.main-fieldset {
	font-size: 10px !important;
	background: var(--plomoOscuroEmpresarial);
	color: white;
}

@media only screen and (max-width: 900px) {
	.slot_evaluacion_financiera {
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

