<template>
	<layout ref="layout">
		<div
			class="slot_body slot-desembolso"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'DESEMBOLSO'"></headerClose>

					<div class="card-body card-block">
						<TabView>
							<TabPanel header="APROBACIÓN">
								<div class="form-row">
									<div class="input-group col-md-8 mt-1">
										<div class="input-group-prepend">
											<div
												class="input-group-text"
												style="font-size: 13px; height: 32px !important"
											>
												<label class="label-title">CLIENTE</label>
											</div>
										</div>
										<input
											class="form-control text-row"
											:value="nombre_completo"
											readonly
										/>
									</div>
									<div class="input-group col-md-4 mt-1">
										<div class="input-group-prepend">
											<div
												class="input-group-text"
												style="font-size: 13px; height: 32px !important"
											>
												<label class="label-title">TIPO</label>
											</div>
										</div>
										<input
											class="form-control center"
											:value="datos_aprobacion.tipo"
											readonly
										/>
									</div>

									<div class="form-group col-md-6 col-6">
										<label class="label-title">COMENTARIO PROPUESTA</label>
										<textarea
											class="form-control text-row"
											rows="2"
											:value="datos_aprobacion.comentario_propuesta"
											readonly
										></textarea>
									</div>
									<div class="form-group col-md-6 col-6">
										<label class="label-title">COMENTARIO APROBACIÓN</label>
										<textarea
											class="form-control text-row"
											rows="2"
											:value="datos_aprobacion.comentario_aprobacion"
											readonly
										></textarea>
									</div>
									<div class="form-row col-md-7">
										<div class="col-md-6 col-6">
											<label class="label-title">MONTO</label>

											<input
												type="text"
												class="form-control center"
												style="
													height: 50px;
													font-size: 25px;
													font-weight: bolder;
													color: var(--colorAlto);
												"
												:value="'S/ ' + roundTo(datos_aprobacion.monto, 2)"
												readonly
											/>
										</div>
										<div class="col-md-6 col-6">
											<label class="label-title">ASESOR</label>
											<input
												class="form-control center"
												:value="datos_aprobacion.usuario_asesor"
												type="text"
												readonly
											/>
										</div>

										<div class="input-group col-md-6 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">PLAZO</label>
												</div>
											</div>
											<input
												class="form-control center"
												:value="
													roundTo(datos_aprobacion.plazo, 0) +
													' ' +
													this.periodo_medicion(datos_aprobacion.periodo_pago)
												"
												type="text"
												readonly
											/>
										</div>
										<div class="input-group col-md-6 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">TASA INTERÉS</label>
												</div>
											</div>
											<input
												class="form-control center"
												:value="
													roundTo(datos_aprobacion.tasa_interes, 2) + ' %'
												"
												type="text"
												readonly
											/>
										</div>
										<div class="input-group col-md-6 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">CUOTA</label>
												</div>
											</div>
											<input
												class="form-control center"
												:value="'S/ ' + roundTo(datos_aprobacion.cuota, 2)"
												type="text"
												style="
													font-size: 15px;
													font-weight: bolder;
													color: var(--colorAlto);
												"
												readonly
											/>
										</div>
										<div class="input-group col-md-6 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">PERIODO</label>
												</div>
											</div>
											<input
												class="form-control center"
												:value="datos_aprobacion.periodo_pago"
												type="text"
												readonly
											/>
										</div>
										<div class="input-group col-md-12 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">FECHA APROBACIÓN</label>
												</div>
											</div>
											<input
												class="form-control center"
												:value="
													JSON.parse(datos_aprobacion.datos_creacion).fecha
												"
												type="text"
												readonly
											/>
										</div>
										<div class="input-group col-md-6 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">ESPECIAL</label>
												</div>
											</div>
											<input
												class="form-control center"
												:value="datos_aprobacion.es_especial ? 'SI' : 'NO'"
												type="text"
												readonly
											/>
										</div>
										<div class="input-group col-md-6 col-6">
											<div class="input-group-prepend">
												<div
													class="input-group-text"
													style="font-size: 13px; height: 32px !important"
												>
													<label class="label-title">DÍAS GRACIA</label>
												</div>
											</div>
											<input
												type="text"
												class="form-control center"
												:value="
													datos_aprobacion.dias_gracia_ci +
													datos_aprobacion.dias_gracia_si
												"
												readonly
											/>
										</div>

										<div
											class="col-md-12 text-center"
											v-if="datos_aprobacion.considerado_uno == 1"
										>
											<label
												class="label-title"
												style="color: orange; font-weight: bolder"
												>¡CRÉDITO CONSIDERADO 1!</label
											>
										</div>

										<div class="col-md-12 mt-2">
											<DataTable
												:value="lista_comisiones"
												:scrollable="true"
												scrollDirection="both"
												scrollHeight="250px"
												showGridlines
											>
												<Column
													field="voucher"
													header="VOUCHER"
													:styles="{ width: '40px', justifyContent: 'center' }"
													v-if="credito_id != 0"
												>
													<template #body="{ data }">
														<button
															class="btn btn-cancel btn-icon-split"
															title="Imprimir VOUCHER"
															v-if="credito_id != 0"
															@click="
																ImprimirVoucher('COMISION', data.comision_id)
															"
														>
															<span class="icon text-white">
																<i class="fa fa-print"></i
															></span>
														</button>
													</template>
												</Column>

												<Column
													field="comision"
													header="COMISIÓN"
													:styles="{
														width: '120px',
														justifyContent: 'center',
													}"
												>
													<template #body="{ data }">
														{{
															comisiones.filter(
																(item_1) => item_1.id == data.comision_id
															)[0].comision
														}}
													</template>
												</Column>
												<Column
													field="monto_cobrar"
													header="MONTO"
													:styles="{ width: '50px', justifyContent: 'center' }"
												>
													<template #body="{ data }">
														<div
															style="
																font-size: 15px;
																color: var(--green);
																font-weight: bolder;
															"
														>
															S/ {{ roundTo(data.monto_cobrar, 2) }}
														</div>
													</template>
												</Column>
												<Column
													field="editar"
													header="EDITAR"
													:styles="{ width: '30px', justifyContent: 'center' }"
													v-if="credito_id == 0"
												>
													<template #body="{ data }">
														<button
															class="btn btn-cancel btn-icon-split"
															title="Editar COMISIÓN"
															v-if="
																comisiones.filter(
																	(item_1) => item_1.id == data.comision_id
																)[0].comision == 'DESEMBOLSO'
															"
															@click="EditarComision(data)"
														>
															<span class="icon text-white">
																<i class="fa fa-edit"></i
															></span>
														</button>
													</template>
												</Column>
											</DataTable>
										</div>
									</div>
									<div class="col-md-5">
										<DataTable
											:value="cronograma_aprobacion"
											:scrollable="true"
											scrollDirection="both"
											scrollHeight="210px"
											stripedRows
											showGridlines
										>
											<Column
												field="orden"
												header="N°"
												:styles="{ width: '20px', justifyContent: 'center' }"
											>
												<template #body="{ data }">
													{{ data.orden }}
												</template>
											</Column>
											<Column
												field="fecha_pago"
												header="FECHA PAGO"
												:styles="{ width: '50px', justifyContent: 'center' }"
											>
												<template #body="{ data }">
													{{ data.fecha_pago }}
												</template>
											</Column>
											<Column
												field="dia_pago"
												header="DÍA"
												:styles="{ width: '50px', justifyContent: 'center' }"
											>
												<template #body="{ data }">
													{{ data.dia_pago }}
												</template>
											</Column>
										</DataTable>

										<div class="form-row col-md-12 mt-3">
											<div class="col-md-8 offset-md-2">
												<label class="label-title">MODO DESEMBOLSO</label>
												<select
													class="form-control center"
													v-model="modo_desembolso"
													:disabled="credito_id != 0"
												>
													<option :value="0" disabled>Seleccione...</option>
													<option value="OFICINA">OFICINA</option>
													<option value="DOMICILIO">DOMICILIO</option>
												</select>
											</div>

											<div
												class="col-md-8 offset-md-2"
												v-if="modo_desembolso == 'DOMICILIO'"
											>
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														id="chbComisionDomicilio"
														v-model="comision_domicilio"
														:disabled="credito_id != 0"
													/>
													<label for="chbComisionDomicilio"
														><span class="badge badge-primary"
															>APLICAR COMISIÓN</span
														></label
													>
												</div>
											</div>
											<div
												class="col-md-8 offset-md-2"
												v-if="
													modo_desembolso == 'DOMICILIO' && comision_domicilio
												"
											>
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text bolder">S/</span>
													</div>

													<input
														type="number"
														class="form-control center"
														min="0"
														step="0.01"
														lang="en"
														style="
															height: 30px;
															font-size: 15px;
															font-weight: bolder;
															color: var(--colorAlto);
														"
														v-model="monto_comision"
														@change="Redondear"
														:disabled="credito_id != 0"
														name="monto"
													/>
												</div>
											</div>
										</div>
									</div>
								</div>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										title="Desembolsar CRÉDITO"
										@click="Desembolsar"
										v-if="credito_id == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-dollar-sign"></i>
										</span>
										<span class="text">DESEMBOLSAR</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										title="Imprimir VOUCHER"
										v-if="credito_id != 0"
										@click="ImprimirVoucher('DESEMBOLSO')"
									>
										<span class="icon text-white">
											<i class="fa fa-print"></i
										></span>
										<span class="text">V. DESEMBOLSO</span>
									</button>
								</div>
							</TabPanel>
							<TabPanel header="CLIENTE">
								<div class="form-row">
									<div class="col-md-12">
										<label class="label-title"
											>EXPEDIENTE:
											<span
												class="text"
												style="
													font-size: 18px;
													color: var(--colorAlto);
													font-weight: bolder;
												"
												>{{
													datos_aprobacion.codigo_expediente != null
														? datos_aprobacion.codigo_expediente
														: "NUEVO"
												}}</span
											></label
										>
									</div>

									<div
										class="col-md-8"
										style="
											border: 2px solid var(--plomoClaroEmpresarial);
											border-radius: 5px;
										"
									>
										<div class="p-2 text-center" style="height: 300px">
											<img
												:src="
													'/imagenes_server/creditos/clientes/dni/' +
													agencia_id +
													'/' +
													datos_aprobacion.imagen_dni.substring(0, 4) +
													'/' +
													datos_aprobacion.imagen_dni
												"
												alt="DNI"
												width="100%"
												height="100%"
												v-if="datos_aprobacion.imagen_dni != null"
											/>
										</div>
									</div>
									<div class="form-row col-md-4 col-12">
										<div class="col-md-12 col-6">
											<label class="label-title">DNI</label>
											<input
												type="text"
												class="form-control center"
												:value="datos_aprobacion.dni"
												readonly
											/>
										</div>
										<div class="col-md-12 col-6">
											<label class="label-title">NOMBRES</label>
											<input
												type="text"
												class="form-control"
												:value="datos_aprobacion.nombres"
												readonly
											/>
										</div>
										<div class="col-md-12 col-6">
											<label class="label-title">AP. PATERNO</label>
											<input
												type="text"
												class="form-control"
												:value="datos_aprobacion.apellido_paterno"
												readonly
											/>
										</div>
										<div class="col-md-12 col-6">
											<label class="label-title">AP. MATERNO</label>
											<input
												type="text"
												class="form-control"
												:value="datos_aprobacion.apellido_materno"
												readonly
											/>
										</div>
										<div class="col-md-12">
											<label class="label-title">ASESOR</label>
											<input
												type="text"
												class="form-control center"
												:value="datos_aprobacion.usuario_asesor"
												readonly
											/>
										</div>
									</div></div
							></TabPanel>
							<TabPanel header="CRONOGRAMA">
								<div class="m-2">
									<DataTable
										:value="cronograma_aprobacion"
										:scrollable="true"
										scrollDirection="both"
										scrollHeight="400px"
										showGridlines
									>
										<Column
											field="orden"
											header="N°"
											:styles="{ width: '20px', justifyContent: 'center' }"
										>
											<template #body="{ data }">
												{{ data.orden }}
											</template>
										</Column>
										<Column
											field="fecha_pago"
											header="FECHA"
											:styles="{ width: '80px', justifyContent: 'center' }"
										>
										</Column>
										<Column
											field="monto_cuota"
											header="CUOTA"
											:styles="{ width: '50px', justifyContent: 'right' }"
										>
											<template #body="{ data }">
												{{ roundTo(data.monto_cuota, 2) }}
											</template>
										</Column>
										<Column
											field="monto_capital"
											header="CAPITAL"
											:styles="{ width: '50px', justifyContent: 'right' }"
										>
											<template #body="{ data }">
												{{ roundTo(data.monto_capital, 2) }}
											</template>
										</Column>
										<Column
											field="monto_interes"
											header="INTERÉS"
											:styles="{ width: '50px', justifyContent: 'right' }"
										>
											<template #body="{ data }">
												{{ roundTo(data.monto_interes, 2) }}
											</template>
										</Column>

										<Column
											field="saldo_pagar"
											header="SALDO_POR_PAGAR"
											:styles="{ width: '100px', justifyContent: 'right' }"
										>
											<template #body="{ data }">
												{{ roundTo(data.saldo_pagar, 2) }}
											</template>
										</Column>
									</DataTable>
								</div>

								<div class="text-left">
									<button
										class="btn btn-cancel btn-icon-split mt-3"
										title="Imprimir CRONOGRAMA"
										v-if="credito_id != 0"
										@click="Exportar('PDF')"
									>
										<span class="icon text-white">
											<i class="fa fa-print"></i
										></span>
										<span class="text">IMPRIMIR CRONOGRAMA</span>
									</button>
								</div>
							</TabPanel>
							<TabPanel header="GARANTÍA"
								><div class="form-row">
									<div class="col-md-6 col-6">
										<label class="label-title">GARANTÍA</label>
										<input
											type="text"
											class="form-control"
											:value="datos_aprobacion.garantia"
											readonly
										/>
									</div>
									<div class="col-md-6 col-6">
										<label class="label-title">VALOR</label>
										<input
											type="text"
											class="form-control center"
											:value="roundTo(datos_aprobacion.valor_garantia, 2)"
											readonly
										/>
									</div>
									<div class="col-md-12">
										<label class="label-title">COMENTARIO DE GARANTÍA</label>
										<textarea
											class="form-control text-row"
											rows="2"
											:value="datos_aprobacion.comentario_garantia"
											readonly
										></textarea>
									</div>
								</div>
							</TabPanel>
						</TabView>
					</div>
				</div>
			</div>

			<div id="mdlEditarComision" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-30 mdlEditarComision">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'EDITAR COMISIÓN'"
								:nombre_modal="'mdlEditarComision'"
							></headerCloseModal>
							<div class="card-body card-block">
								<div class="form-row">
									<div class="col-md-6">
										<label class="label-title">COMISIÓN</label>
										<input
											type="text"
											class="form-control center"
											:value="comision_seleccionada.nombre_comision"
											disabled
										/>
									</div>
									<div class="col-md-6">
										<label class="label-title">MONTO S/</label>
										<input
											type="number"
											class="form-control center"
											name="comision_desembolso"
											step="0.1"
											v-model.number="comision_seleccionada.monto_cobrar"
											:min="comision_seleccionada.monto_minimo"
											@change="Redondear"
											style="
												font-size: 15px;
												color: var(--green);
												font-weight: bolder;
											"
										/>
									</div>
								</div>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										title="Guardar COMISIÓN"
										@click="GuardarComision"
										v-if="credito_id == 0"
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
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import TabView from "primevue/tabview/tabview.common";
import TabPanel from "primevue/tabpanel/tabpanel.common";
import Checkbox from "primevue/checkbox/checkbox.common";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,

		DataTable,
		Column,
		TabView,
		TabPanel,
		Checkbox,
	},
	props: {
		agencia_id: Number,
		aprobacion_id: Number,
		credito_id: Number,
		datos_desembolso: Object,
		datos_titular: Object,
		datos_pariente: Object,
		datos_aval: Object,
		datos_pariente_aval: Object,
		datos_desembolso_comisiones: Array,
		datos_aprobacion: Object,
		comisiones: Array,
	},
	data() {
		return {
			submited: false,
			cronograma_aprobacion: [],
			cronograma: [],
			imagen_dni: this.datos_aprobacion.imagen_dni,
			modo_desembolso:
				this.datos_desembolso != null
					? this.datos_desembolso.modo_desembolso
					: 0,
			comision_domicilio: false,
			monto_comision: 5,
			nombre_completo: null,

			lista_comisiones: [],

			comision_seleccionada: {
				id: null,
				nombre_comision: null,
				monto_cobrar: 0,
				monto_minimo: 0,
			},
		};
	},

	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		nueva_empresa() {
			let agencia = this.$inertia.page.props.application.agencias.filter(
				(item) => item.id == this.agencia_id
			);

			return agencia[0].nueva_empresa;
		},

		nombre_completo_titular() {
			let nombre_completo_titular =
				this.datos_aprobacion.apellido_paterno +
				" " +
				this.datos_aprobacion.apellido_materno +
				" " +
				this.datos_aprobacion.nombres;
			return nombre_completo_titular;
		},

		nombre_completo_pariente() {
			if (this.datos_aprobacion.pariente_id != null) {
				let nombre_completo_pariente =
					this.datos_aprobacion.apellido_paterno +
					" " +
					this.datos_aprobacion.apellido_materno +
					" " +
					this.datos_aprobacion.nombres;
				return nombre_completo_pariente;
			} else {
				return null;
			}
		},

		nombre_completo_aval() {
			if (this.datos_aprobacion.aval_id != null) {
				let nombre_completo_aval =
					this.datos_aprobacion.apellido_paterno +
					" " +
					this.datos_aprobacion.apellido_materno +
					" " +
					this.datos_aprobacion.nombres;
				return nombre_completo_aval;
			} else {
				return null;
			}
		},

		nombre_completo_pariente_aval() {
			if (this.datos_aprobacion.pariente_aval_id != null) {
				let nombre_completo_pariente_aval =
					this.datos_aprobacion.apellido_paterno +
					" " +
					this.datos_aprobacion.apellido_materno +
					" " +
					this.datos_aprobacion.nombres;
				return nombre_completo_pariente_aval;
			} else {
				return null;
			}
		},
		datos_credito_redondeados() {
			let plazo_con_periodo = null;
			let plazo_redondeado = Math.round(this.datos_desembolso.plazo);

			switch (this.datos_desembolso.periodo_pago) {
				case "DIARIO":
					plazo_con_periodo = plazo_redondeado + " día(s)";
					break;
				case "SEMANAL":
					plazo_con_periodo = plazo_redondeado + " semana(s)";
					break;
				case "PAGO_UNICO":
					plazo_con_periodo = plazo_redondeado + " dia(s)";
					break;
				case "QUINCENAL":
					plazo_con_periodo = plazo_redondeado + " quincena(s)";
					break;
				case "MENSUAL":
					plazo_con_periodo = plazo_redondeado + " mes(s)";
					break;
			}

			let lista = {
				monto: this.roundTo(this.datos_desembolso.monto, 2),
				plazo: plazo_con_periodo,
				tasa_interes: this.roundTo(this.datos_desembolso.tasa_interes, 2),
				tipo: this.datos_desembolso.tipo,
				cuota: this.roundTo(this.datos_desembolso.cuota, 2),
				codigo_seguimiento: this.datos_desembolso.codigo_seguimiento,
				fecha_desembolso: JSON.parse(this.datos_desembolso.datos_creacion)
					.fecha,
			};

			return lista;
		},
	},
	watch: {
		cronograma_aprobacion(value) {
			this.cronograma = [];
			value.forEach((element) => {
				let object = {
					fecha_pago:
						element.fecha_pago +
						" " +
						element.dia_pago.substring(0, 3).toUpperCase(),
					orden: element.orden,
					monto_cuota: parseFloat(element.monto_cuota, 2),
					monto_capital: parseFloat(element.monto_capital, 2),
					monto_interes: parseFloat(element.monto_interes),
					saldo_pagar: parseFloat(element.saldo_pagar),
				};

				this.cronograma.push(object);
			});
		},

		modo_desembolso(value) {
			if (value == "DOMICILIO") {
				this.comision_domicilio = true;
				this.monto_comision = this.roundTo(5, 2);
				return true;
			}
		},
	},

	mounted() {
		this.nombre_completo =
			this.datos_aprobacion.apellido_paterno +
			" " +
			this.datos_aprobacion.apellido_materno +
			" " +
			this.datos_aprobacion.nombres;

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
			this.CalcularCronograma();

			// Verifica si se ha seleccionado cobrar la comisión DESEMBOLSO A DOMICILIO
			if (this.credito_id != 0) {
				this.datos_desembolso_comisiones.forEach((element) => {
					if (element.comision == "DESEMBOLSO A DOMICILIO") {
						this.comision_domicilio = true;
						this.monto_comision = element.monto;
						return true;
					}
				});
			}
		}

		if (this.credito_id == 0) {
			this.lista_comisiones = JSON.parse(
				this.datos_aprobacion.comisiones
			).filter((item) => item.cobrar_comision == 1);
		} else {
			let lista = [];
			this.datos_desembolso_comisiones.forEach((element) => {
				let object = {
					cobrar_comision: 1,
					comision_id: element.comision_id,
					monto_cobrar: element.monto,
				};

				lista.push(object);
			});

			this.lista_comisiones = lista;
		}
	},
	methods: {
		periodo_medicion(value) {
			if (value == "DIARIO") {
				return "(DÍAS)";
			} else if (value == "SEMANAL") {
				return "(SEMANAS)";
			} else if (value == "QUINCENAL") {
				return "(QUINCENAS)";
			} else if (value == "MENSUAL") {
				return "(MESES)";
			}
			return "(DÍAS)";
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			let name = e.target.name;

			if (name == "comision_desembolso") {
				if (
					e.target.value &&
					e.target.value >= this.comision_seleccionada.monto_minimo
				) {
					valor = e.target.value;
				} else {
					valor = this.comision_seleccionada.monto_minimo;
				}

				this.comision_seleccionada.monto_cobrar = this.roundTo(
					valor,
					numero_decimales
				);
			}
		},
		periodo_medicion(value) {
			if (value == "DIARIO") {
				return "(DÍAS)";
			} else if (value == "SEMANAL") {
				return "(SEMANAS)";
			} else if (value == "QUINCENAL") {
				return "(QUINCENAS)";
			} else if (value == "MENSUAL") {
				return "(MESES)";
			}
			return "(DÍAS)";
		},

		async CalcularCronograma() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_id);
			data.append("monto", this.datos_aprobacion.monto);
			data.append("plazo", this.datos_aprobacion.plazo);
			data.append("tasa_interes", this.datos_aprobacion.tasa_interes);
			data.append("periodo_pago", this.datos_aprobacion.periodo_pago);
			data.append("con_dias_gracia", this.datos_aprobacion.con_dias_gracia);
			data.append("es_especial", this.datos_aprobacion.es_especial);

			if (this.datos_aprobacion.con_dias_gracia) {
				data.append("dias_gracia_ci", this.datos_aprobacion.dias_gracia_ci);
				data.append("dias_gracia_si", this.datos_aprobacion.dias_gracia_si);
			}

			data.append("fecha_desembolso", this.datos_aprobacion.fecha_aprobacion);
			await axios
				.post(route("cre.calcular_cronograma.sin_redondeo"), data)
				.then((response) => {
					this.cronograma_aprobacion = response.data.datos_calendario;
				});
		},

		EditarComision(item) {
			let nombre_comision = this.comisiones.filter(
				(item_1) => item_1.id == item.comision_id
			)[0].comision;

			this.comision_seleccionada.id = item.comision_id;
			this.comision_seleccionada.nombre_comision = nombre_comision;
			this.comision_seleccionada.monto_cobrar = this.roundTo(
				item.monto_cobrar,
				2
			);
			this.comision_seleccionada.monto_minimo = this.roundTo(
				item.monto_cobrar,
				2
			);

			$("#mdlEditarComision").css("display", "block");
		},
		async GuardarComision() {
			Swal.fire({
				title: "¿Desea MODIFICAR la COMISIÓN?",
				confirmButtonText: "SI",
				showCancelButton: true,
				cancelButtonText: "NO",
				allowOutsideClick: false,
				backdrop: true,
			}).then((result) => {
				if (result.isConfirmed) {
					Swal.fire({
						title: "GUARDANDO...",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							const item = this.lista_comisiones.find(
								(item) => item.comision_id === this.comision_seleccionada.id
							);

							if (item) {
								item.monto_cobrar = parseFloat(
									this.comision_seleccionada.monto_cobrar
								);
							}

							$("#mdlEditarComision").css("display", "none");
							return Swal.fire({
								icon: "success",
								title: "Comisión ACTUALIZADA",
								timer: 1200,
								showConfirmButton: false,
							});
						},
					});
				}
			});
		},
		async Desembolsar() {
			let self = this;

			if (this.imagen_dni == null) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "El cliente no tiene imagen de DNI",
					allowOutsideClick: true,
				});
				$("#cliente-tab").tab("show");

				return false;
			}

			if (this.modo_desembolso == 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Debe seleccionar el MODO para el DESEMBOLSO.",
					allowOutsideClick: true,
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿Desea DESEMBOLSAR el crédito?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: " No",
				allowOutsideClick: false,

				preConfirm: (result) => {
					Swal.fire({
						title: "CARGANDO",
						text: "Espere porfavor...",
						allowOutsideClick: false,

						didOpen: async () => {
							const data = new FormData();

							data.append(
								"datos_aprobacion",
								JSON.stringify(this.datos_aprobacion)
							);
							data.append("caja_id", this.mi_caja.id);
							data.append("agencia_id", this.agencia_id);
							data.append("modo_desembolso", this.modo_desembolso);
							data.append(
								"lista_comisiones",
								JSON.stringify(this.lista_comisiones)
							);

							if (this.modo_desembolso == "DOMICILIO") {
								data.append("comision_domicilio", this.comision_domicilio);
								data.append("monto_comision", this.monto_comision);
							}

							// this.$inertia.post(route("caj.desembolso.guardar"), data);
							// return false;

							Swal.showLoading();
							await axios
								.post(route("caj.desembolso.guardar"), data)
								.then(async (response) => {
									await this.$inertia.get(
										route("caj.desembolso", {
											aprobacion_id: this.aprobacion_id,
											agencia_id: this.agencia_id,
										})
									);

									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										text:
											response.data.message ||
											"Desembolso realizado correctamente.",
										timer: 1200,
										showConfirmButton: false,
									});
								})
								.catch((error) => {
									console.log(error);
									Swal.showValidationMessage(
										`Ha ocurrido un error, comunicar a TI: ${error}`
									);
								});
						},
					});
				},
			});
		},
		Exportar(tipo) {
			let datos_titular = {
				titular: this.nombre_completo_titular,
				negocio:
					this.datos_titular.negocio_nombre +
					" - " +
					this.datos_titular.negocio_actividad,
				asesor: this.datos_aprobacion.usuario_asesor,
				pariente: this.nombre_completo_pariente,
				aval: this.nombre_completo_aval,
				pariente_aval: this.nombre_completo_pariente_aval,
			};

			let datos_credito = {
				monto: this.datos_desembolso.monto,
				interes_total: this.datos_desembolso.interes_total,
				plazo: this.datos_desembolso.plazo,
				periodo_pago: this.datos_desembolso.periodo_pago,
				cuota: this.datos_desembolso.cuota,
				tipo: this.datos_desembolso.tipo,
				tasa_interes: this.datos_desembolso.tasa_interes,
				fecha_desembolso: this.datos_desembolso.fecha_desembolso,
				codigo_seguimiento: this.nueva_empresa
					? this.datos_desembolso.codigo_seguimiento_2
					: this.datos_desembolso.codigo_seguimiento,
			};
			let data = new FormData();
			data.append("agencia_id", this.agencia_id);
			data.append("cronograma", JSON.stringify(this.cronograma));
			data.append("datos_titular", JSON.stringify(datos_titular));
			data.append("datos_credito", JSON.stringify(datos_credito));

			data.append("tipo", tipo);

			// this.$inertia.post(route("caj.desembolso.cronograma"), data);
			// return false;

			Swal.fire({
				title: "GENERANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("caj.desembolso.cronograma"), data)
						.then((response) => {
							let origin = window.location.origin;
							let path_pdf = response.data.path_pdf;

							// Crear un IFrame
							let iframe = document.createElement("iframe");
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
		async ImprimirVoucher(concepto, comision_id) {
			let data = new FormData();

			data.append("concepto", concepto);

			data.append("agencia_id", this.agencia_id);
			data.append("agencia", this.$page.props.user_session.nombre_agencia);
			data.append("usuario", this.$page.props.user_session.usuario);
			data.append(
				"dispositivo",
				this.$page.props.user_session.dispositivo.nombre
			);

			if (concepto == "DESEMBOLSO") {
				data.append("titulo", "CONSTANCIA DE DESEMBOLSO");
				data.append("datos_desembolso", JSON.stringify(this.datos_desembolso));
				data.append("datos_titular", JSON.stringify(this.datos_titular));
			} else if (concepto == "COMISION") {
				data.append("titulo", "CONSTANCIA DE COMISIÓN");
				data.append("datos_creacion", this.datos_desembolso.datos_creacion);

				let comison_pago = this.datos_desembolso_comisiones.filter(
					(item) => item.comision_id == comision_id
				)[0];
				let datos_comision = {
					comision_pago_id:
						Array(7 - String(comison_pago.id).length).join("0") +
						comison_pago.id,
					cliente:
						this.datos_titular.apellido_paterno +
						" " +
						this.datos_titular.apellido_materno +
						" " +
						this.datos_titular.nombres,
					tipo: comison_pago.comision,
					monto: this.roundTo(comison_pago.monto, 2),
				};

				data.append("datos_comision", JSON.stringify(datos_comision));
			}

			// this.$inertia.post(route("caj.desembolso.voucher"), data);
			// return false;

			await axios
				.post(route("caj.desembolso.voucher"), data)
				.then((response) => {
					let origin = window.location.origin;
					let path_pdf = response.data.path_pdf;

					// Crear un IFrame
					let iframe = document.createElement("iframe");
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
	},
};
</script>

<style lang="css">
.slot-desembolso {
	width: 50% !important;
	margin-left: 25% !important;
}

.checkbox .cr {
	/* Estilos para la clase específica */
	background: white !important;
}

.mdlEditarComision {
	margin-top: 15%;
}

@media (max-width: 900px) {
	.slot-desembolso {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
