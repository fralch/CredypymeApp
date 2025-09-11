<template>
	<!-- ------ AREA IMPRIMIBLE --------- -->
	<div id="rptEvaluacionFinanciera">
		<h1
			align="center"
			style="font-size: 20px; color: black; font-weight: bolder"
		>
			EVALUACIÓN FINANCIERA
		</h1>
		<h2
			style="
				margin-top: 25px;
				font-size: 15px;
				color: blue;
				font-weight: bolder;
				text-transform: uppercase;
			"
		>
			{{ cliente }}
		</h2>
		<div class="row mt-3">
			<label class="label-title col-4">AGENCIA: {{ agencia }}</label>
			<label class="label-title col-4"
				>FECHA DE CREACIÓN: {{ fecha_creacion }}</label
			>
			<label class="label-title col-4"
				>FECHA DE ACT.: {{ fecha_actualizacion }}</label
			>
		</div>

		<div class="form-row mt-3">
			<div class="col-6">
				<h3
					align="center"
					style="color: black; font-size: 18px; font-weight: bolder"
				>
					BALANCE GENERAL
				</h3>

				<table width="100%">
					<tbody>
						<tr class="table-title">
							<td align="center">ACTIVO</td>
							<td align="right">S/ {{ activo.total }}</td>
						</tr>
						<tr class="table-sub-title">
							<td>ACTIVO CORRIENTE</td>
							<td align="right">S/ {{ activo.total_activo_corriente }}</td>
						</tr>
						<tr>
							<td class="with-border">CAJA (EFECTIVO)</td>
							<td class="with-border" align="right">
								S/ {{ isNullValue(activo_corriente.caja) }}
							</td>
						</tr>
						<tr>
							<td class="with-border">BANCOS</td>
							<td class="with-border" align="right">
								S/ {{ activo.total_bancos }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(activo_corriente.bancos)"
							:key="'a' + index"
						>
							<td class="with-border" style="padding-left: 30px !important">
								{{ item.descripcion }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>
						<tr>
							<td class="with-border">CUENTAS POR COBRAR A CLIENTES</td>
							<td class="with-border" align="right">
								S/ {{ activo.total_cuentas_cobrar }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(
								activo_corriente.cuentas_cobrar
							)"
							:key="'b' + index"
						>
							<td class="with-border" style="padding-left: 30px !important">
								{{ item.descripcion }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>
						<tr>
							<td class="with-border">ADELANTOS REALIZADOS A PROVEEDORES</td>
							<td class="with-border" align="right">
								S/ {{ isNullValue(activo_corriente.adelanto_proveedores) }}
							</td>
						</tr>
						<tr>
							<td class="with-border">VARIOS</td>
							<td class="with-border" align="right">
								S/ {{ isNullValue(activo_corriente.varios) }}
							</td>
						</tr>
					</tbody>
				</table>
				<br />
				<table width="100%">
					<tbody>
						<tr class="table-sub-title">
							<td>INVENTARIO</td>
							<td align="right">S/ {{ activo.total_inventario }}</td>
						</tr>
					</tbody>
				</table>

				<table width="100%">
					<thead>
						<tr>
							<th class="with-border title-blue">PRODUCTO/MATERIA PRIMA</th>
							<th class="with-border title-blue">CANT.</th>
							<th class="with-border title-blue">PREC. COMP.</th>
							<th class="with-border title-blue">SUB TOTAL</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(item, index) in isNullArray(
								activo_corriente.inventario_mercaderia
							)"
							:key="'a' + index"
						>
							<td class="with-border">
								{{ item.descripcion }}
							</td>
							<td class="with-border" align="center">
								{{ item.cantidad }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.precio_unitario, 2) }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.cantidad * item.precio_unitario, 2) }}
							</td>
						</tr>
					</tbody>
				</table>
				<br />
				<table width="100%">
					<tbody>
						<tr class="table-sub-title">
							<td>ACTIVO NO CORRIENTE</td>
							<td align="right">S/ {{ activo.total_activo_no_corriente }}</td>
						</tr>
						<tr>
							<td class="with-border">MUEBLES Y ENSERES</td>
							<td class="with-border" align="right">
								S/ {{ activo.total_muebles_enseres }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(
								activo_no_corriente.muebles_enseres
							)"
							:key="'a' + index"
						>
							<td class="with-border" style="padding-left: 30px !important">
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>

						<tr>
							<td class="with-border">INMUEBLE, MAQUINARIA Y EQUIPO</td>
							<td class="with-border" align="right">
								S/ {{ activo.total_inmueble_maquinaria }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(
								activo_no_corriente.inmueble_maquinaria_equipo
							).filter((item) => item.disponible)"
							:key="'b' + index"
						>
							<td class="with-border" style="padding-left: 30px !important">
								{{ item.cantidad + " " + item.descripcion }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.cantidad * item.precio_actual, 2) }}
							</td>
						</tr>
					</tbody>
				</table>

				<table style="margin-top: 150px" width="100%">
					<tbody>
						<tr class="table-title">
							<td align="center">PASIVO</td>
							<td align="right">S/ {{ pasivo.total }}</td>
						</tr>
						<tr class="table-sub-title">
							<td>PASIVO CORRIENTE</td>
							<td align="right">S/ {{ pasivo.total_pasivo_corriente }}</td>
						</tr>

						<tr>
							<td class="with-border">DEUDA A CORTO PLAZO MENOR A 1 AÑO</td>
							<td class="with-border" align="right">
								S/ {{ pasivo.total_deuda_corto_plazo }}
							</td>
						</tr>
						<tr>
							<td class="with-border">ADELANTO RECIBIDO DE PROVEEDORES</td>
							<td class="with-border" align="right">
								S/ {{ pasivo.total_adelanto_proveedores }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(
								pasivo_corriente.adelanto_proveedores
							)"
							:key="'a' + index"
						>
							<td class="with-border" style="padding-left: 30px !important">
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>

						<tr>
							<td class="with-border">OTROS</td>
							<td class="with-border" align="right">
								S/ {{ isNullValue(pasivo_corriente.otros) }}
							</td>
						</tr>
					</tbody>
				</table>

				<table style="margin-top: 150px" width="100%">
					<tbody>
						<tr class="table-sub-title">
							<td>PASIVO NO CORRIENTE</td>
							<td align="right">S/ {{ pasivo.total_pasivo_no_corriente }}</td>
						</tr>
						<tr>
							<td class="with-border">DEUDA A LARGO PLAZO MAYOR A 1 AÑO</td>
							<td class="with-border" align="right">
								S/ {{ pasivo.total_deuda_largo_plazo }}
							</td>
						</tr>
					</tbody>
				</table>
				<br />
				<br />
				<table width="100%">
					<tbody>
						<tr class="table-title">
							<td align="right">PATRIMONIO</td>
							<td align="right">S/ {{ patrimonio }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-6">
				<h3
					align="center"
					style="color: black; font-size: 18px; font-weight: bolder"
				>
					FLUJO DE CAJA
				</h3>

				<table width="100%">
					<tbody>
						<tr class="table-sub-title">
							<td colspan="3">VENTAS</td>
							<td align="right">S/ {{ ingresos_egresos.total_ventas }}</td>
						</tr>
						<tr>
							<th class="with-border title-blue">DESCRIPCIÓN</th>
							<th class="with-border title-blue">CANT.</th>
							<th class="with-border title-blue">PREC_UNIT</th>
							<th class="with-border title-blue">SUB TOTAL</th>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(flujo_caja.ventas_detallado)"
							:key="'a' + index"
						>
							<td class="with-border" style="padding-left: 15px !important">
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="center">
								{{ item.cantidad }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.precio_unitario, 2) }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.cantidad * item.precio_unitario, 2) }}
							</td>
						</tr>
						<tr class="table-sub-title">
							<td colspan="3">COSTO DE VENTAS</td>
							<td align="right">
								S/ {{ ingresos_egresos.total_costo_ventas }}
							</td>
						</tr>

						<tr>
							<th class="with-border title-blue">DESCRIPCIÓN</th>
							<th class="with-border title-blue">CANT.</th>
							<th class="with-border title-blue">PREC_UNIT</th>
							<th class="with-border title-blue">SUB TOTAL</th>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(flujo_caja.costo_ventas)"
							:key="'b' + index"
						>
							<td class="with-border" style="padding-left: 15px !important">
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="center">
								{{ item.cantidad }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.precio_unitario, 2) }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.cantidad * item.precio_unitario, 2) }}
							</td>
						</tr>
						<tr class="table-title">
							<td colspan="3" align="right">UTILIDAD BRUTA</td>
							<td align="right">
								S/ {{ ingresos_egresos.total_utilidad_bruta }}
							</td>
						</tr>
						<tr class="table-sub-title">
							<td colspan="3">COSTOS OPERATIVOS</td>
							<td align="right">
								S/ {{ ingresos_egresos.total_costos_operativos }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(flujo_caja.costos_operativos)"
							:key="'c' + index"
						>
							<td
								class="with-border"
								style="padding-left: 15px !important"
								colspan="3"
							>
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>
						<tr class="table-title">
							<td colspan="3" align="right">UTILIDAD NETA</td>
							<td align="right">
								S/ {{ ingresos_egresos.total_utilidad_neta }}
							</td>
						</tr>
						<tr class="table-sub-title">
							<td colspan="3">OTROS INGRESOS</td>
							<td align="right">
								S/ {{ ingresos_egresos.total_otros_ingresos }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(flujo_caja.otros_ingresos)"
							:key="'d' + index"
						>
							<td
								class="with-border"
								style="padding-left: 15px !important"
								colspan="3"
							>
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>

						<tr class="table-sub-title">
							<td colspan="3">GASTOS FAMILIARES</td>
							<td align="right">
								S/ {{ ingresos_egresos.total_gastos_familiares }}
							</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(flujo_caja.gastos_familiares)"
							:key="'e' + index"
						>
							<td
								class="with-border"
								style="padding-left: 15px !important"
								colspan="3"
							>
								{{ item.descripcion }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.monto, 2) }}
							</td>
						</tr>
						<tr class="table-title">
							<td colspan="3" align="right">EXCEDENTE</td>
							<td align="right">S/ {{ ingresos_egresos.total_excedente }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-row mt-3">
			<div class="col-12">
				<table width="100%">
					<tbody>
						<tr class="table-title">
							<td align="center" colspan="10">
								PRÉSTAMOS CON ENTIDADES FINANCIERAS
							</td>
						</tr>
						<tr class="table-sub-title">
							<td class="with-border" align="center">ENTIDAD</td>
							<td class="with-border" align="center">MONTO</td>
							<td class="with-border" align="center">TOTAL A PAG.</td>
							<td class="with-border" align="center">FRECUENCIA PAGO</td>
							<td class="with-border" align="center">CUOTA</td>
							<td class="with-border" align="center">PLAZO</td>
							<td class="with-border" align="center">CU. PAGA</td>
							<td class="with-border" align="center">CU. RES</td>
							<td class="with-border" align="center">SALDO</td>
							<td class="with-border" align="center">DÍA PAGO</td>
						</tr>
						<tr
							v-for="(item, index) in isNullArray(flujo_caja.prestamos)"
							:key="'a' + index"
						>
							<td class="with-border">
								{{ item.entidad }}
							</td>

							<td class="with-border" align="right">
								S/ {{ round(item.monto_prestamo, 2) }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.monto_cuota * item.plazo, 2) }}
							</td>
							<td class="with-border" align="center">
								{{ item.frecuencia_pago }}
							</td>
							<td class="with-border" align="right">
								S/ {{ round(item.monto_cuota, 2) }}
							</td>
							<td class="with-border" align="center">
								{{ item.plazo }}
							</td>
							<td class="with-border" align="center">
								{{ item.cuotas_pagadas }}
							</td>
							<td class="with-border" align="center">
								{{ item.plazo - item.cuotas_pagadas }}
							</td>
							<td class="with-border" align="right">
								S/
								{{
									round(
										(item.plazo - item.cuotas_pagadas) * item.monto_cuota,
										2
									)
								}}
							</td>
							<td class="with-border">
								{{ item.dia_pago }}
							</td>
						</tr>

						<tr>
							<td></td>
							<td class="with-border" align="right">
								S/ {{ round(prestamos.total_monto_prestamo, 2) }}
							</td>
							<td colspan="6"></td>
							<td class="with-border" align="right">
								S/ {{ round(prestamos.total_saldo, 2) }}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-row" style="margin-top: 150px">
			<div class="col-6">
				<hr class="line-sign" />
				<p align="center" style="color: black">ASESOR DE NEGOCIO</p>
			</div>
			<div class="col-6">
				<hr class="line-sign" />
				<p align="center" style="color: black">FIRMA DEL CLIENTE</p>
			</div>
		</div>
	</div>
	<!-- -----FIN IMPRIMIBLE ---------- -->
</template>

<script>
export default {
	props: { agencia_id: Number },
	data() {
		return {
			datos_personales: [],
			datos_evaluacion: [],
			activo_corriente: [],
			activo_no_corriente: [],
			flujo_caja: [],
			pasivo_corriente: [],
		};
	},
	computed: {
		cliente() {
			if (!this.isEmpty(this.datos_personales)) {
				let obj = this.datos_personales;
				return (
					obj.apellido_paterno +
					" " +
					obj.apellido_materno +
					" " +
					obj.nombres +
					" - " +
					obj.dni
				);
			}
			return "-";
		},
		agencia() {
			if (!this.isEmpty(this.datos_personales)) {
				return this.datos_personales.nombre_agencia.toUpperCase();
			}
			return "-";
		},
		fecha_creacion() {
			if (!this.isEmpty(this.datos_evaluacion)) {
				let fecha = JSON.parse(this.datos_evaluacion.datos_creacion).fecha;
				return fecha;
			}
			return "-";
		},
		fecha_actualizacion() {
			if (!this.isEmpty(this.datos_evaluacion)) {
				let fecha = "";
				if (this.datos_evaluacion.datos_actualizacion != null) {
					fecha = JSON.parse(this.datos_evaluacion.datos_actualizacion).fecha;
				} else {
					fecha = JSON.parse(this.datos_evaluacion.datos_creacion).fecha;
				}

				return fecha;
			}
			return "-";
		},
		activo() {
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
				total: this.round(total, 2),
				total_activo_corriente: this.round(total_activo_corriente, 2),
				total_bancos: this.round(total_bancos, 2),
				total_cuentas_cobrar: this.round(total_cuentas_cobrar, 2),
				total_inventario: this.round(total_inventario, 2),
				total_activo_no_corriente: this.round(total_activo_no_corriente, 2),
				total_muebles_enseres: this.round(total_muebles_enseres, 2),
				total_inmueble_maquinaria: this.round(total_inmueble_maquinaria, 2),
			};

			return obj;
		},

		pasivo() {
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
				total: this.round(total, 2),
				total_pasivo_corriente: this.round(total_pasivo_corriente, 2),
				total_deuda_corto_plazo: this.round(total_deuda_corto_plazo, 2),
				total_adelanto_proveedores: this.round(total_adelanto_proveedores, 2),
				total_pasivo_no_corriente: this.round(total_pasivo_no_corriente, 2),
				total_deuda_largo_plazo: this.round(total_deuda_largo_plazo, 2),
			};

			return obj;
		},
		patrimonio() {
			return this.round(
				parseFloat(this.activo.total) - parseFloat(this.pasivo.total),
				2
			);
		},
		ingresos_egresos() {
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
				total_ventas: this.round(total_ventas, 2),
				total_costo_ventas: this.round(total_costo_ventas, 2),
				total_utilidad_bruta: this.round(total_utilidad_bruta, 2),
				total_costos_operativos: this.round(total_costos_operativos, 2),
				total_utilidad_neta: this.round(total_utilidad_neta, 2),
				total_otros_ingresos: this.round(total_otros_ingresos, 2),
				total_gastos_familiares: this.round(total_gastos_familiares, 2),
				total_excedente: this.round(total_excedente, 2),
			};

			return obj;
		},
		prestamos() {
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
				total_monto_prestamo: this.round(total_monto_prestamo, 2),
				total_saldo: this.round(total_saldo, 2),
			};
			return obj;
		},
	},

	methods: {
		round(value, decimals) {
			return parseFloat(value).toFixed(decimals);
		},
		isEmpty(obj) {
			return Object.keys(obj).length === 0;
		},
		isNullValue(property) {
			if (property == null) {
				return this.round(0, 2);
			} else {
				return this.round(property, 2);
			}
		},
		isNullArray(property) {
			if (property == null) {
				return [];
			} else {
				return JSON.parse(property);
			}
		},
	},
};
</script>

<style lang="css">
#rptEvaluacionFinanciera {
	width: 100% !important;
	position: absolute;
	display: none;
}

.line-sign {
	width: 50%;
	margin-left: 25%;
	background-color: black;
	-webkit-print-color-adjust: exact;
}

.with-border {
	padding-left: 5px !important;
	padding-right: 5px !important;
	padding-top: 1px !important;
	padding-bottom: 1px !important;
	border: 1px solid var(--plomoOscuroEmpresarial) !important;
	font-size: 11px !important;
	color: var(--plomoOscuroEmpresarial);
}

.title-blue {
	background: rgb(217, 233, 252);
	color: black;
	text-align: center;
	-webkit-print-color-adjust: exact;
}

.table-title {
	padding-left: 5px !important;
	padding-right: 5px !important;
	padding-top: 3px !important;
	padding-bottom: 3px !important;
	margin-bottom: 1rem;
	background: black;
	color: white;
	font-weight: bolder;
	font-size: 17px;
	-webkit-print-color-adjust: exact;
}
.table-sub-title {
	padding-left: 5px !important;
	padding-right: 5px !important;
	padding-top: 3px !important;
	padding-bottom: 3px !important;
	margin-bottom: 1rem;
	background: rgb(252, 248, 192);
	color: black;
	font-weight: bolder;
	-webkit-print-color-adjust: exact;
}
</style>
