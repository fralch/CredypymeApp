<template>
	<layout ref="layout">
		<div class="slot_body slot-cuentas-bancarias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CUENTAS BANCARIAS'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_seleccionada"
										@change="Listar"
									>
										<option
											v-for="(item, index) in agencias_permitidas"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group col-md-2">
								<button
									class="btn btn-action btn-icon-split"
									@click="Listar"
									title="Listar CUENTAS"
								>
									<span class="icon text-white">
										<i class="fas fa-sync"></i>
									</span>
									<span class="text">ACTUALIZAR</span>
								</button>
							</div>
						</div>
						<div class="card-title mb-1">LISTA DE CUENTAS</div>

						<DataTable
							:value="lista_cuentas"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							selectionMode="single"
							showGridlines
							@row-dblclick="VerMovimientosAgrupado"
						>
							<Column
								field="numero"
								header="N°"
								:styles="{ width: '40px', justifyContent: 'center' }"
							>
								<template #body="slotProps">
									{{ slotProps.index + 1 }}
								</template>
							</Column>

							<Column
								field="banco"
								header="BANCO"
								:styles="{
									width: '200px',
									justifyContent: 'center',
								}"
							>
								<template #body="{ data }">
									<div class="celda-resaltada">
										{{ data.banco }}
									</div>
								</template>
							</Column>
							<Column
								field="titular"
								header="TITULAR"
								:styles="{ width: '200px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="numero"
								header="NÚMERO_CUENTA"
								:styles="{ width: '200px', justifyContent: 'left' }"
							>
							</Column>
							<Column
								field="acumulado"
								header="ACUMULADO"
								:styles="{ width: '100px', justifyContent: 'right' }"
							>
								<template #body="{ data }">
									<div class="celda-resaltada">
										S/ {{ RedondearVista(data.acumulado, 2) }}
									</div>
								</template>
							</Column>
							<ColumnGroup type="footer">
								<Row>
									<Column
										:colspan="4"
										footer="TOTAL"
										:footerStyle="{
											width: '640px',
											backgroundColor: '#244b9a !important',
											fontSize: '15px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'S/ ' + RedondearVista(total_cuentas, 2)"
										:footerStyle="{
											width: '100px',
											fontSize: '15px !important',
											textAlign: 'right',
										}"
									/>
								</Row>
							</ColumnGroup>
							<template #empty> No hay CUENTAS BANCARIAS activas.</template>
						</DataTable>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlMovimientosAgrupado" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-50 mdlMovimientosAgrupado">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'MOVIMIENTOS AGRUPADOS POR DÍA'"
								:nombre_modal="'mdlMovimientosAgrupado'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<div class="form-row mb-3">
									<div class="input-group col-md-5">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">BANCO</span>
										</div>
										<input
											type="text"
											class="form-control center bolder"
											onkeydown="return false"
											spellcheck="false"
											:value="this.banco_seleccionado.banco"
										/>
									</div>
									<div class="input-group col-md-7">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>TITULAR</span
											>
										</div>
										<input
											type="text"
											class="form-control center bolder"
											onkeydown="return false"
											spellcheck="false"
											:value="this.banco_seleccionado.titular"
										/>
									</div>
								</div>
								<div class="form-row mb-2">
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">ABONOS</span>
										</div>
										<input
											type="text"
											class="form-control center bolder ingreso"
											onkeydown="return false"
											spellcheck="false"
											:value="'S/ ' + RedondearVista(this.total_abonos, 2)"
											style="font-size: 14px"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>RETIROS</span
											>
										</div>
										<input
											type="text"
											class="form-control center bolder egreso"
											onkeydown="return false"
											spellcheck="false"
											:value="'S/ ' + RedondearVista(this.total_retiros, 2)"
											style="font-size: 14px"
										/>
									</div>

									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>ACUMULADO</span
											>
										</div>
										<input
											type="text"
											class="form-control center bolder acumulado"
											onkeydown="return false"
											spellcheck="false"
											:value="
												'S/ ' +
												RedondearVista(this.banco_seleccionado.acumulado, 2)
											"
											style="font-size: 15px"
										/>
									</div>
								</div>

								<DataTable
									:value="lista_movimientos_agrupado"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="300px"
									selectionMode="single"
									:paginator="true"
									:rows="30"
									paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
									currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
									@row-dblclick="VerMovimientosDetallado"
								>
									<Column
										field="fecha"
										header="FECHA"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											<div class="bolder" style="font-size: 14px">
												{{ data.fecha }}
											</div>
										</template>
									</Column>

									<Column
										field="descripcion"
										header="DESCRIPCIÓN"
										:styles="{
											width: '200px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											<div
												class="p-2 bolder text-center"
												:class="[data.tipo == 'I' ? 'ingreso' : 'egreso']"
												style="width: 100% !important"
											>
												{{ data.tipo == "I" ? "ABONOS" : "RETIROS" }}
											</div>
										</template>
									</Column>
									<Column
										field="total"
										header="TOTAL"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											<div class="bolder" style="font-size: 15px">
												S/ {{ RedondearVista(data.total, 2) }}
											</div>
										</template>
									</Column>

									<template #empty>
										No hay MOVIMIENTOS en el BANCO seleccionado.</template
									>
								</DataTable>

								<hr />

								<fieldset class="form-group col-md-12">
									<legend>
										<label class="label-title">NUEVO MOVIMIENTO</label>
									</legend>
									<div class="row">
										<div class="col-md-5">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<div class="input-group-text prepend-title">
														<input
															type="radio"
															name="tipo_movimiento"
															id="rdbRetirar"
															value="RETIRAR"
															v-model="frmDatosMovimiento.tipo"
														/>
													</div>
												</div>
												<div
													:class="[
														frmDatosMovimiento.tipo == 'RETIRAR'
															? 'input-group-prepend'
															: 'input-group-append',
													]"
												>
													<label
														class="input-group-text bolder"
														for="rdbRetirar"
														style="font-size: 13px"
													>
														RETIRAR
													</label>
												</div>

												<select
													class="form-control center"
													v-model="frmDatosMovimiento.modo"
													v-if="frmDatosMovimiento.tipo == 'RETIRAR'"
												>
													<option value="A_CAJA">A CAJA</option>
													<option value="A_CUENTA">A CUENTA</option>
												</select>
											</div>
											<div class="input-group mb-3" v-if="permiso_abonar">
												<div class="input-group-prepend">
													<div class="input-group-text prepend-title">
														<input
															type="radio"
															name="tipo_movimiento "
															id="rdbAbonar"
															value="ABONAR"
															v-model="frmDatosMovimiento.tipo"
														/>
													</div>
												</div>
												<div
													:class="[
														frmDatosMovimiento.tipo == 'ABONAR'
															? 'input-group-prepend'
															: 'input-group-append',
													]"
												>
													<label
														class="input-group-text bolder"
														for="rdbAbonar"
														style="font-size: 13px"
													>
														ABONAR
													</label>
												</div>
												<select
													class="form-control center"
													v-if="frmDatosMovimiento.tipo == 'ABONAR'"
												>
													<option>COMISIONES</option>
												</select>
											</div>
											<div class="input-group mb-1">
												<div class="input-group-prepend">
													<div class="input-group-text prepend-title">
														<input
															type="radio"
															name="tipo_movimiento"
															id="rdbTransferir"
															value="TRANSFERIR"
															v-model="frmDatosMovimiento.tipo"
														/>
													</div>
												</div>
												<div class="input-group-append">
													<label
														class="input-group-text bolder"
														for="rdbTransferir"
														style="font-size: 13px"
													>
														TRANSFERIR
													</label>
												</div>
											</div>
											<div
												class="input-group mb-1"
												v-if="frmDatosMovimiento.tipo == 'TRANSFERIR'"
											>
												<div class="input-group-prepend">
													<label
														class="input-group-text bolder"
														style="font-size: 13px"
													>
														A
													</label>
												</div>
												<select
													class="form-control center"
													v-model="frmDatosMovimiento.banco_transferencia_id"
												>
													<option :value="null" selected>Seleccione...</option>
													<option
														v-for="(item, index) in bancos_transferencias"
														:key="index"
														:value="item.id"
													>
														{{ item.banco }}
													</option>
												</select>
											</div>
										</div>
										<div class="form-row col-md-7">
											<div class="col-md-7">
												<div>
													<label class="label-title">MONTO</label>

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
																font-size: 20px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
															v-model.number="frmDatosMovimiento.monto"
															name="monto"
															@change="Redondear"
														/>
													</div>
												</div>
											</div>
											<div class="col-md-5">
												<button
													class="btn btn-action btn-icon-split"
													title="REGISTRAR"
													style="font-size: 13px; height: 50px"
													@click="Registrar"
													:disabled="frmDatosMovimiento.monto <= 0"
												>
													<span class="icon text-white">
														<i class="fa fa-check"></i>
													</span>
													<span class="text">REGISTRAR</span>
												</button>
											</div>

											<div class="col-md-12">
												<label class="label-title">DESCRIPCIÓN</label>

												<textarea
													class="form-control mayus text-row"
													rows="3"
													v-model="frmDatosMovimiento.descripcion"
													:class="{
														'is-invalid':
															submited &&
															(frmDatosMovimiento.descripcion == null ||
																frmDatosMovimiento.descripcion.trim() === ''),
														'is-valid':
															submited &&
															frmDatosMovimiento.descripcion != null &&
															frmDatosMovimiento.descripcion.trim() !== '',
													}"
												>
												</textarea>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlMovimientosDetalle" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-50 mdlMovimientosDetalle">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'DETALLE DE MOVIMIENTOS'"
								:nombre_modal="'mdlMovimientosDetalle'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<div class="form-row mb-3">
									<div class="input-group col-md-4 offset-md-2">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">FECHA</span>
										</div>
										<input
											type="text"
											class="form-control center bolder"
											onkeydown="return false"
											spellcheck="false"
											:value="this.fecha_seleccionada.fecha"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">TIPO</span>
										</div>
										<input
											type="text"
											class="form-control center bolder"
											:class="[
												this.fecha_seleccionada.tipo == 'I'
													? 'ingreso'
													: 'egreso',
											]"
											onkeydown="return false"
											spellcheck="false"
											:value="
												this.fecha_seleccionada.tipo == 'I'
													? 'ABONOS'
													: 'RETIROS'
											"
										/>
									</div>
								</div>

								<div class="card-title mb-1">
									LISTA DE MOVIMIENTOS DETALLADO
								</div>

								<DataTable
									:value="lista_movimientos_detalle"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="300px"
									selectionMode="single"
									:rows="30"
									showGridlines
								>
									<Column
										field="index"
										header="N°"
										:styles="{
											width: '40px',
											justifyContent: 'center',
										}"
									>
										<template #body="slotProps">
											{{ slotProps.index + 1 }}
										</template>
									</Column>
									<Column
										field="fecha_movimiento"
										header="FECHA"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											<div class="bolder">
												{{ data.fecha_movimiento }}
											</div>
										</template>
									</Column>
									<Column
										field="operacion"
										header="OPERACIÓN"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="descripcion"
										header="DESCRIPCIÓN"
										:styles="{
											width: '250px',
										}"
									>
									</Column>
									<Column
										field="monto"
										header="MONTO"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
									>
										<template #body="{ data }">
											<div class="bolder" style="font-size: 13px">
												S/ {{ RedondearVista(data.monto, 2) }}
											</div>
										</template>
									</Column>
									<Column
										field="usuario"
										header="USUARIO_OPER"
										:styles="{
											width: '130px',
											justifyContent: 'center',
										}"
									>
									</Column>

									<ColumnGroup type="footer">
										<Row>
											<Column
												:colspan="3"
												footer="TOTAL"
												:footerStyle="{
													width: '550px',

													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="1"
												:footer="
													'S/ ' +
													RedondearVista(this.fecha_seleccionada.total, 2)
												"
												:footerStyle="{
													width: '100px',
													fontSize: '15px !important',
													textAlign: 'right',
												}"
											/>
											<Column
												:colspan="2"
												:footer="null"
												:footerStyle="{
													width: '130px',
													backgroundColor: 'transparent !important',
													textAlign: 'right',
												}"
											/>
										</Row>
									</ColumnGroup>
								</DataTable>
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

import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DataTable,
		Column,
		ColumnGroup,
		Row,
	},
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			lista_cuentas: [],
			submited: false,

			lista_movimientos_agrupado: [],
			banco_seleccionado: {},
			fecha_seleccionada: {},
			lista_movimientos_detalle: [],

			frmDatosMovimiento: {
				tipo: null,
				modo: null,
				monto: 0,
				descripcion: null,
				banco_transferencia_id: null,
			},
		};
	},

	computed: {
		total_cuentas() {
			let total_acumulado = 0;

			if (this.lista_cuentas.length > 0) {
				total_acumulado = this.lista_cuentas.reduce((total, item) => {
					return parseFloat(total) + parseFloat(item.acumulado);
				}, 0);
			}

			return total_acumulado;
		},
		total_abonos() {
			let acumulado_abonos = 0;

			if (this.lista_movimientos_agrupado.length > 0) {
				acumulado_abonos = this.lista_movimientos_agrupado.reduce(
					(total, item) => {
						if (item.tipo == "I") {
							return parseFloat(total) + parseFloat(item.total);
						}
						return total;
					},
					0
				);
			}

			return acumulado_abonos;
		},
		total_retiros() {
			let acumulado_retiros = 0;

			if (this.lista_movimientos_agrupado.length > 0) {
				acumulado_retiros = this.lista_movimientos_agrupado.reduce(
					(total, item) => {
						if (item.tipo == "E") {
							return parseFloat(total) + parseFloat(item.total);
						}

						return total;
					},
					0
				);
			}

			return acumulado_retiros;
		},

		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		mi_cuenta() {
			return this.$inertia.page.props.creditos_datos.datos_cuenta;
		},
		mi_agencia() {
			return this.$inertia.page.props.user_session.id_agencia;
		},

		tipo_movimiento() {
			return this.frmDatosMovimiento.tipo;
		},

		bancos_transferencias() {
			const lista = this.lista_cuentas.filter(
				(item) => item.id != this.banco_seleccionado.id
			);
			return lista;
		},

		permiso_abonar() {
			let resultado = false;
			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CUENTA/BANCO_ABONAR"
				);

			if (permiso_detalle.length != 0) {
				let acceso_agencias = permiso_detalle[0].acceso_agencias;

				if (acceso_agencias != null) {
					acceso_agencias = JSON.parse(acceso_agencias);

					let agencia_autorizada = acceso_agencias.filter(
						(item) => item.agencia_id == this.agencia_seleccionada
					);

					if (agencia_autorizada.length != 0) {
						resultado = true;
					}
				}
			}

			return resultado;
		},
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
				this.Listar();
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
					this.Listar();
				} else {
					this.agencia_seleccionada = 0;
				}
			}
		},
		tipo_movimiento(value) {
			this.frmDatosMovimiento.monto = this.RedondearValor(0, 2);

			if (value == "ABONAR") {
				this.frmDatosMovimiento.modo = "DE_CAJA";
			} else if (value == "RETIRAR") {
				this.frmDatosMovimiento.modo = "A_CAJA";
			} else if (value == "TRANSFERIR") {
				this.frmDatosMovimiento.modo = "DE_BANCO";
				this.frmDatosMovimiento.banco_transferencia_id = null;
			}
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CUENTA/CUENTAS_BANCARIAS"
			);
		},

		RedondearVista(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			let resultado = parseFloat(valor).toLocaleString("es-PE", {
				minimumFractionDigits: numero_decimales,
				maximumFractionDigits: numero_decimales,
			});

			return resultado;
		},
		RedondearValor(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			if (this.frmDatosMovimiento.tipo == "RETIRAR") {
				if (
					parseFloat(this.frmDatosMovimiento.monto) <=
					parseFloat(this.banco_seleccionado.acumulado)
				) {
					this.frmDatosMovimiento.monto = this.RedondearValor(
						valor,
						numero_decimales
					);
				} else {
					this.frmDatosMovimiento.monto = this.RedondearValor(
						this.banco_seleccionado.acumulado,
						numero_decimales
					);
				}
			} else {
				this.frmDatosMovimiento.monto = this.RedondearValor(
					valor,
					numero_decimales
				);
			}
		},

		async Listar() {
			// this.$inertia.get(
			// 	route("cue.cuentas_bancarias.listar", {
			// 		agencia_id: this.agencia_seleccionada,
			// 	})
			// );
			// return false;

			Swal.fire({
				title: "ACTUALIZANDO...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					await axios
						.get(
							route("cue.cuentas_bancarias.listar", {
								agencia_id: this.agencia_seleccionada,
							})
						)

						.then(async (response) => {
							this.lista_cuentas = response.data.lista_cuentas;

							Swal.close();
							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
								timer: 1200,
								showConfirmButton: false,
							});
						})
						.catch((error) => {
							console.log(error);
							Swal.showValidationMessage(
								`Se ha producido un ERROR. 
											Por favor, no realice más acciones en el sistema y
											 contacte al área de SOPORTE para su revisión.`
							);
						});
				},
			});
		},

		Resetear() {
			this.submited = false;
			this.frmDatosMovimiento.monto = this.RedondearVista(0, 2);
			this.frmDatosMovimiento.tipo = "RETIRAR";
			this.frmDatosMovimiento.modo = "A_CAJA";
			this.frmDatosMovimiento.banco_transferencia_id = null;
			this.frmDatosMovimiento.descripcion = null;
		},
		async VerMovimientosAgrupado(event) {
			this.banco_seleccionado = event.data;

			const params = {
				agencia_id: this.agencia_seleccionada,
			};

			// this.$inertia.get(
			// 	route("cue.cuentas_bancarias.movimientos_agrupado", {
			// 		banco_id: this.banco_seleccionado.id,
			// 	}),
			// 	params
			// );
			// return false;

			Swal.fire({
				title: "LISTANDO...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					await this.ListarMovimientosAgrupado(params);

					Swal.close();
					$("#mdlMovimientosAgrupado").css("display", "block");
					return Swal.fire({
						icon: "success",
						title: "¡LISTO!",
						timer: 1200,
						showConfirmButton: false,
					});
				},
			});
		},

		async ListarMovimientosAgrupado(params) {
			await axios
				.get(
					route("cue.cuentas_bancarias.movimientos_agrupado", {
						banco_id: this.banco_seleccionado.id,
					}),
					{ params }
				)
				.then(async (response) => {
					this.Resetear();
					this.lista_movimientos_agrupado =
						response.data.lista_movimientos_agrupado;
				})
				.catch((error) => {
					console.log(error);
					Swal.showValidationMessage(
						`Se ha producido un ERROR. 
											Por favor, no realice más acciones en el sistema y
											 contacte al área de SOPORTE para su revisión.`
					);
				});
		},

		async VerMovimientosDetallado(event) {
			this.fecha_seleccionada = event.data;

			const params = {
				agencia_id: this.agencia_seleccionada,
				fecha: this.fecha_seleccionada.fecha,
				tipo: this.fecha_seleccionada.tipo,
			};

			// this.$inertia.get(
			// 	route("cue.cuentas_bancarias.movimientos_detalle", {
			// 		banco_id: this.banco_seleccionado.id,
			// 	}),
			// 	params
			// );
			// return false;

			Swal.fire({
				title: "LISTANDO...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					await this.ListarMovimientosDetalle(params);

					Swal.close();
					$("#mdlMovimientosDetalle").css("display", "block");
					return Swal.fire({
						icon: "success",
						title: "¡LISTO!",
						timer: 1200,
						showConfirmButton: false,
					});
				},
			});
		},

		async ListarMovimientosDetalle(params) {
			// this.$inertia.get(
			// 	route("cue.cuentas_bancarias.movimientos_detalle", {
			// 		banco_id: this.banco_seleccionado.id,
			// 	}),
			// 	params
			// );
			// return false;

			await axios
				.get(
					route("cue.cuentas_bancarias.movimientos_detalle", {
						banco_id: this.banco_seleccionado.id,
					}),
					{ params }
				)
				.then(async (response) => {
					this.Resetear();
					this.lista_movimientos_detalle =
						response.data.lista_movimientos_detalle;
				})
				.catch((error) => {
					console.log(error);
					Swal.showValidationMessage(
						`Se ha producido un ERROR. 
											Por favor, no realice más acciones en el sistema y
											 contacte al área de SOPORTE para su revisión.`
					);
				});
		},

		async Registrar() {
			this.submited = true;

			if (
				this.frmDatosMovimiento.modo == "A_CAJA" ||
				this.frmDatosMovimiento.modo == "DE_CAJA"
			) {
				if (this.mi_caja == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Primero debe aperturar CAJA",
						confirmButtonText: "Ok",
						allowOutsideClick: true,
					});
					return false;
				}
			} else {
				if (this.mi_cuenta == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Usted no tiene una CUENTA habilitada",
						confirmButtonText: "Ok",
						allowOutsideClick: true,
					});
					return false;
				}
			}

			if (
				this.frmDatosMovimiento.descripcion == null ||
				this.frmDatosMovimiento.descripcion.trim() == ""
			) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe ingresar la descripción del movimiento",
					confirmButtonText: "Ok",
					allowOutsideClick: true,
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿DESEA REGISTRAR ESTE MOVIMIENTO?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
				preConfirm: () => {
					Swal.fire({
						title: "REGISTRANDO...",
						allowOutsideClick: false,
						didOpen: async () => {
							let data = new FormData();

							data.append("agencia_banco", this.agencia_seleccionada);
							data.append("banco_id", this.banco_seleccionado.id);
							data.append("tipo", this.frmDatosMovimiento.tipo);
							data.append(
								"frmDatosMovimiento",
								JSON.stringify(this.frmDatosMovimiento)
							);

							data.append("agencia_operacion", this.mi_agencia);
							if (
								this.frmDatosMovimiento.modo == "A_CAJA" ||
								this.frmDatosMovimiento.modo == "DE_CAJA"
							) {
								data.append("caja_operacion", this.mi_caja.id);
							} else if (this.frmDatosMovimiento.modo == "A_CUENTA") {
								data.append("cuenta_operacion", this.mi_cuenta.id);
							}

							// this.$inertia.post(
							// 	route("cue.cuentas_bancarias.registrar_movimiento"),
							// 	data
							// );
							// Swal.close();
							// return false;

							Swal.showLoading();

							await axios
								.post(route("cue.cuentas_bancarias.registrar_movimiento"), data)
								.then(async (response) => {
									let acumulado = response.data.acumulado;
									this.banco_seleccionado.acumulado = acumulado;

									const params = {
										agencia_id: this.agencia_seleccionada,
									};

									this.ListarMovimientosAgrupado(params);

									Swal.close();

									return this.Listar();
								})
								.catch((error) => {
									console.log(error);
									Swal.showValidationMessage(
										`Se ha producido un ERROR. 
								Por favor, no realice más acciones en el sistema y
								 contacte al área de SOPORTE para su revisión.`
									);
								});
						},
					});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-cuentas-bancarias {
	width: 60% !important;
	margin-left: 20% !important;
}

/* .mdlMovimientosAgrupado {
	margin-top: 2%;
} */

.mdlMovimientosDetalle {
	margin-top: 2%;
}

.celda-resaltada {
	font-size: 14px !important;
	font-weight: bolder;
}
.acumulado {
	color: white !important;
	background-color: var(--azulOscuroEmpresarial) !important;
}
.ingreso {
	color: white !important;
	background-color: var(--verdeOscuroEmpresarial) !important;
}
.egreso {
	color: white !important;
	background-color: var(--red) !important;
}
@media (max-width: 900px) {
	.slot-cuentas-bancarias {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlMovimientosAgrupado {
		margin-top: 20%;
	}
	.mdlMovimientosDetalle {
		margin-top: 20%;
	}
}
</style>
