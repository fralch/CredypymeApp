<template>
	<layout ref="layout">
		<div
			class="slot_body slot-inversion-meta mx-auto"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'INVERSIÓN - PRODUCTO META'"></headerClose>

					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="detalle-tab"
									data-toggle="tab"
									href="#detalle"
									role="tab"
									aria-controls="detalle"
									aria-selected="true"
									>DETALLE</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="cierre-tab"
									data-toggle="tab"
									href="#cierre"
									role="tab"
									aria-controls="cierre"
									aria-selected="false"
									>CIERRE</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="detalle"
								role="tabpanel"
								aria-labelledby="detalle-tab"
							>
								<div class="form-row">
									<div class="input-group col-md-7 mb-1 mt-1">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title span-highlight"
												>CLIENTE</span
											>
										</div>
										<input
											type="text"
											class="form-control input-information input-highlight"
											onkeydown="return false"
											spellcheck="false"
											:value="nombre_completo_titular"
										/>
									</div>

									<div class="input-group col-md-5 mb-1 mt-1">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title span-highlight"
												>PRODUCTO META</span
											>
										</div>

										<input
											type="text"
											class="form-control center input-information input-highlight"
											onkeydown="return false"
											spellcheck="false"
											:value="datos_inversion.producto"
										/>
									</div>

									<div class="input-group col-md-4 mb-1 mt-1">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>VALOR META</span
											>

											<span class="input-group-text prepend-title">S/</span>
										</div>

										<input
											type="text"
											class="form-control center input-information"
											onkeydown="return false"
											spellcheck="false"
											:value="this.roundTo(datos_inversion.valor_meta, 2)"
										/>
									</div>
									<div class="input-group col-md-4 mb-1 mt-1 col-6">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth >= 900"
												>ACUMULADO</span
											>
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth < 900"
												>ACUM.</span
											>

											<span class="input-group-text prepend-title">S/</span>
										</div>

										<input
											type="text"
											class="form-control center input-information"
											onkeydown="return false"
											spellcheck="false"
											:value="roundTo(datos_inversion.acumulado, 2)"
										/>
									</div>
									<div class="input-group col-md-4 mb-1 mt-1 col-6">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth >= 900"
												>RESTA</span
											>
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth < 900"
												>RES.</span
											>
											<span class="input-group-text prepend-title">S/</span>
										</div>

										<input
											type="text"
											class="form-control center input-information"
											onkeydown="return false"
											spellcheck="false"
											:value="roundTo(monto_resta, 2)"
										/>
									</div>
									<div class="input-group col-md-4 mb-1 mt-1">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>ÚLTIMO ABONO</span
											>
										</div>

										<input
											type="text"
											class="form-control center input-information"
											onkeydown="return false"
											spellcheck="false"
											:value="
												datos_inversion.fecha_movimiento == null
													? '-'
													: datos_inversion.fecha_movimiento
											"
										/>
									</div>
									<div class="input-group col-md-5 mb-1 mt-1">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>USUARIO DE APERTURA</span
											>
										</div>

										<input
											type="text"
											class="form-control center input-information"
											onkeydown="return false"
											spellcheck="false"
											:value="datos_inversion.usuario_registro"
										/>
									</div>
								</div>
								<hr />
								<div id="tabla_Movimientos" class="mb-3">
									<table
										class="table"
										id="tblMovimientos"
										style="width: 100% !important"
									>
										<thead>
											<tr>
												<th style="min-width: 20px !important">N°</th>
												<th style="min-width: 90px !important">
													FECHA_REGISTRO
												</th>
												<th style="min-width: 50px !important">MONTO</th>
												<th style="min-width: 20px !important">TIPO</th>
												<th style="min-width: 70px !important">CAJA</th>
												<th style="min-width: 300px !important">COMENTARIO</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in lista_movimientos"
												:key="index"
												:class="[index % 2 == 0 ? 'verde-claro' : '']"
											>
												<td align="center">{{ index + 1 }}</td>
												<td align="center">{{ item.fecha_registro }}</td>
												<td align="right">
													S/
													{{
														(item.tipo == "E" ? "- " : "") +
														roundTo(item.monto, 2)
													}}
												</td>
												<td align="center">{{ item.tipo }}</td>
												<td align="center">
													<div
														class="col-12"
														style="
															font-size: 10px;
															font-family: 'Roboto-BoldItalic';
														"
													>
														{{ item.agencia_caja + " - " + item.usuario_caja }}
													</div>
												</td>
												<td>
													{{ item.comentario == null ? "-" : item.comentario }}
												</td>
											</tr>
										</tbody>
									</table>
								</div>

								<fieldset
									class="p-0 pt-2 pl-3 pr-2"
									style="background-color: #d8f1fd"
								>
									<div class="form-row">
										<div class="form-group col-md-5">
											<div class="form-group">
												<div class="input-group mb-3">
													<div class="input-group-prepend">
														<div class="input-group-text prepend-title">
															<input
																type="radio"
																name="forma_pago"
																id="rdbPorCuota"
																value="I"
																v-model="frmDatosMovimiento.tipo"
																:disabled="modo == 'VOUCHER' || cerrado == true"
															/>
														</div>
													</div>
													<div class="input-group-prepend">
														<label
															class="input-group-text"
															for="rdbPorCuota"
															style="font-size: 13px"
														>
															Abonar
														</label>
													</div>
													<div
														class="input-group-prepend"
														v-if="frmDatosMovimiento.tipo == 'I'"
													>
														<span
															class="input-group-text"
															style="font-size: 15px"
															>S/
														</span>
													</div>

													<input
														type="number"
														class="form-control center"
														:class="[
															submited
																? $v.frmDatosMovimiento.monto.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														style="
															max-width: 150px;
															font-size: 17px;
															font-weight: bolder;
															color: var(--colorAlto);
														"
														step="0.01"
														min="0"
														@change="Redondear"
														name="abonar"
														:disabled="modo == 'VOUCHER' || cerrado == true"
														v-model.number="frmDatosMovimiento.monto"
														v-if="frmDatosMovimiento.tipo == 'I'"
														@focus="hidenav()"
														@blur="shownav()"
													/>
												</div>

												<div class="input-group mb-3">
													<div class="input-group-prepend">
														<div class="input-group-text">
															<input
																type="radio"
																name="forma_pago"
																id="rdbPorMonto"
																value="E"
																@change="Redondear"
																:disabled="modo == 'VOUCHER' || cerrado == true"
																v-model="frmDatosMovimiento.tipo"
															/>
														</div>
													</div>
													<div class="input-group-prepend">
														<label
															class="input-group-text"
															for="rdbPorMonto"
															style="font-size: 13px"
														>
															Retirar
														</label>
													</div>
													<div
														class="input-group-prepend"
														v-if="frmDatosMovimiento.tipo == 'E'"
													>
														<span
															class="input-group-text"
															style="font-size: 15px"
															>S/
														</span>
													</div>

													<input
														type="number"
														step="0.01"
														min="0"
														class="form-control center"
														:class="[
															submited
																? $v.frmDatosMovimiento.monto.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														lang="en"
														style="
															max-width: 150px;
															font-size: 17px;
															font-weight: bolder;
															color: var(--colorAlto);
														"
														@change="Redondear"
														name="retirar"
														:disabled="modo == 'VOUCHER' || cerrado == true"
														v-model.number="frmDatosMovimiento.monto"
														v-if="frmDatosMovimiento.tipo == 'E'"
														@focus="hidenav()"
														@blur="shownav()"
													/>
												</div>
											</div>
										</div>
										<div class="form-group col-md-5">
											<div
												class="input-group"
												v-if="frmDatosMovimiento.tipo == 'I'"
											>
												<div class="input-group-prepend">
													<div class="input-group-text">
														<input
															type="checkbox"
															:value="true"
															id="chbPorBanco"
															v-model="por_banco"
															:disabled="modo == 'VOUCHER' || cerrado == true"
														/>
													</div>
												</div>
												<div class="input-group-prepend">
													<label
														class="input-group-text prepend-title"
														for="chbPorBanco"
														>POR BANCO</label
													>
												</div>

												<select
													class="form-control center"
													v-model="frmDatosMovimiento.banco_id"
													:class="[
														submited && por_banco
															? frmDatosMovimiento.banco_id == null
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													:disabled="
														modo == 'VOUCHER' || !por_banco || cerrado == true
													"
												>
													<option :value="null" disabled selected>
														Seleccione...
													</option>
													<option
														v-for="(item, index) in bancos"
														:key="index"
														:value="item.id"
													>
														{{ item.banco }}
													</option>
												</select>
											</div>
											<label class="label-title">COMENTARIO</label>
											<textarea
												type="text"
												class="form-control text-row mayus"
												rows="3"
												v-model="frmDatosMovimiento.comentario"
												:disabled="modo == 'VOUCHER' || cerrado == true"
												@focus="hidenav()"
												@blur="shownav()"
											></textarea>
										</div>

										<div class="form-group col-md-2">
											<div class="text-center">
												<button
													class="btn btn-action btn-icon-split mb-1"
													title="Nueva"
													v-if="modo == 'VOUCHER'"
													@click="NuevaOperacion"
													:disabled="cerrado == true"
												>
													<span class="icon text-white">
														<i class="fas fa-plus"></i>
													</span>
													<span class="text">NUEVO</span>
												</button>
												<button
													v-if="modo == 'GUARDAR'"
													class="btn btn-action btn-icon-split mb-1"
													title="Guardar"
													@click="Guardar"
													:disabled="cerrado == true"
												>
													<span class="icon text-white">
														<i class="fas fa-save"></i>
													</span>
													<span class="text">GUARDAR</span>
												</button>
												<button
													v-else
													class="btn btn-cancel btn-icon-split mb-1"
													@click="ImprimirVoucher('movimiento')"
													:disabled="cerrado == true"
												>
													<span class="icon text-white">
														<i class="fas fa-print"></i>
													</span>
													<span class="text">VOUCHER</span>
												</button>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
							<div
								class="tab-pane fade"
								id="cierre"
								role="tabpanel"
								aria-labelledby="cierre-tab"
							>
								<div class="form-row">
									<div class="row ml-2 col-12">
										<div class="form-check">
											<input
												class="form-check-input"
												id="chbHabilitado"
												type="checkbox"
												v-model="cerrar_inversion"
												:disabled="cerrado == true"
											/>
											<label
												class="form-check-label label-title"
												for="chbHabilitado"
											>
												CERRAR INVERSIÓN
											</label>
										</div>
									</div>
									<hr />
									<div
										class="row col-12 mx-auto"
										style="background-color: #f1fff1"
									>
										<div class="form-row col-md-8 col-12 mx-auto">
											<div
												class="input-group col-md-12 mb-1 mt-3"
												style="align-items: center"
											>
												<label class="label-title col-md-5 col-6">
													MONTO ACUMULADO
												</label>
												<label
													class="my-auto col-6"
													style="
														font-size: 22px;
														color: var(--colorAlto);
														font-weight: bolder;
													"
												>
													S/ {{ acumulado }}
												</label>
											</div>
											<hr />
											<div class="form-group col-md-12">
												<label class="label-title col-md-5"
													>COMENTARIO DEL RETIRO
													<label style="color: #83bede">(OPCIONAL)</label>
												</label>
												<textarea
													class="form-control mayus text-row"
													maxlength="200"
													rows="4"
													:disabled="cerrar_inversion == false"
													v-model="frmDatosCierre.comentario"
													@focus="hidenav()"
													@blur="shownav()"
												></textarea>
											</div>
											<div class="mx-auto mt-2">
												<button
													class="btn btn-action btn-icon-split mb-1"
													title="Guardar"
													:disabled="cerrar_inversion == false"
													@click="CerrarInversion"
													v-if="cerrado == false"
												>
													<span class="icon text-white">
														<i class="fas fa-save"></i>
													</span>
													<span class="text">CERRAR</span>
												</button>
												<button
													class="btn btn-cancel btn-icon-split mb-1"
													title="Guardar"
													v-else
													@click="ImprimirVoucher('cierre')"
												>
													<span class="icon text-white">
														<i class="fas fa-print"></i>
													</span>
													<span class="text">VOUCHER</span>
												</button>
											</div>
										</div>
									</div>
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

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		inversion_id: Number,
		agencia_id: Number,
	},
	data() {
		return {
			submited: false,
			windowWidth: window.innerWidth,

			datos_inversion: {
				inversion_id: null,
			},
			bancos: [],
			lista_movimientos: [],
			cerrado: false,

			por_banco: false,

			frmDatosMovimiento: {
				tipo: "I",
				monto: this.roundTo(0, 2),
				comentario: null,
				agencia_caja: 0,
				caja_id: 0,
				banco_id: null,
			},

			modo: "GUARDAR",
			cerrar_inversion: false,
			frmDatosCierre: {
				tipo: "E",
				comentario: null,
				agencia_caja: 0,
				caja_id: 0,
				acumulado: 0,
			},

			datos_voucher: {},
		};
	},

	validations: {
		frmDatosMovimiento: {
			monto: { noZero, required },
		},
	},
	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},

		nombre_completo_titular() {
			let nombre_completo_titular =
				this.datos_inversion.apellido_paterno +
				" " +
				this.datos_inversion.apellido_materno +
				" " +
				this.datos_inversion.nombres;
			return nombre_completo_titular;
		},

		acumulado() {
			let valor = parseFloat(this.datos_inversion.acumulado).toFixed(2);
			return valor;
		},

		monto_resta() {
			let monto_resta;
			monto_resta =
				this.datos_inversion.valor_meta - this.datos_inversion.acumulado;
			return monto_resta;
		},
	},

	watch: {
		lista_movimientos() {
			$("#tblMovimientos").DataTable().destroy();
			this.TablaMovimientos();
		},
		por_banco(value) {
			this.frmDatosMovimiento.banco_id = null;
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});

		this.ListarRecursos();
		this.TablaMovimientos();

		if (this.mi_caja == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Primero debe aperturar CAJA",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		}
	},
	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
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
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			if (
				this.frmDatosMovimiento.tipo == "E" &&
				valor > parseFloat(this.acumulado)
			) {
				valor = this.acumulado;
			} else if (
				this.frmDatosMovimiento.tipo == "I" &&
				valor > parseFloat(this.monto_resta)
			) {
				valor = this.monto_resta;
			}

			this.frmDatosMovimiento.monto = this.roundTo(valor, numero_decimales);
		},
		async ListarRecursos() {
			const params = {
				agencia_id: this.agencia_id,
				inversion_id: this.inversion_id,
			};

			// this.$inertia.get(route("inv.meta.listar_recursos"), params);
			// return false;

			return await axios
				.get(route("inv.meta.listar_recursos"), { params })
				.then((response) => {
					this.bancos = response.data.bancos;
					this.datos_inversion = response.data.datos_inversion;
					this.lista_movimientos = response.data.lista_movimientos;
				});
		},
		TablaMovimientos() {
			this.$nextTick(() => {
				var table = $("#tblMovimientos").DataTable({
					scrollY: "200px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
					},
					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
				});
			});
		},
		async Guardar() {
			this.submited = true;

			if (this.mi_caja == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Primero debe aperturar CAJA",
					confirmButtonText: "Ok",
					allowOutsideClick: true,
				});
				return this.$inertia.get(route("cre.index"));
			}

			let monto = parseFloat(this.frmDatosMovimiento.monto);
			if (this.frmDatosMovimiento.monto == 0) {
				let mensaje = "";
				if (this.frmDatosMovimiento.tipo == "I") {
					mensaje = "abonar";
				} else {
					mensaje = "retirar";
				}

				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "El monto a " + mensaje + " no puede ser 0",
				});
				return false;
			}

			Swal.fire({
				title: "GUARDAR CAMBIOS",
				text: "¿Desea continuar?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.frmDatosMovimiento.agencia_caja = this.mi_caja.agencia_id;
					this.frmDatosMovimiento.caja_id = this.mi_caja.id;
					this.frmDatosMovimiento.monto = monto;
					this.frmDatosMovimiento.acumulado = this.acumulado;

					let data = new FormData();

					data.append("agencia_id", this.agencia_id);
					data.append("inversion_id", this.inversion_id);
					data.append("por_banco", this.por_banco);
					data.append(
						"frmDatosInversion",
						JSON.stringify(this.frmDatosMovimiento)
					);

					// this.$inertia.post(route("inv.meta.abonar_retirar"), data);
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("inv.meta.abonar_retirar"), data)
								.then((response) => {
									this.submited = false;
									this.modo = "VOUCHER";
									this.datos_voucher = response.data.datos_voucher;
									this.ListarRecursos();
									return Swal.fire({
										icon: "success",
										title: response.data.message,
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
				} else {
					return false;
				}
			});
		},

		NuevaOperacion() {
			this.modo = "GUARDAR";
			this.submited = false;
			this.frmDatosMovimiento.tipo = "I";
			this.por_banco = false;
			this.frmDatosMovimiento.monto = this.roundTo(0, 2);
			this.frmDatosMovimiento.comentario = null;
		},
		async CerrarInversion() {
			if (this.mi_caja == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Primero debe aperturar CAJA",
					confirmButtonText: "Ok",
					allowOutsideClick: true,
				});
				return this.$inertia.get(route("cre.index"));
			}

			Swal.fire({
				title: "GUARDAR CAMBIOS",
				text: "¿Desea continuar?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.frmDatosCierre.agencia_caja = this.mi_caja.agencia_id;
					this.frmDatosCierre.caja_id = this.mi_caja.id;
					this.frmDatosCierre.acumulado = this.acumulado;

					let data = new FormData();

					data.append("agencia_id", this.agencia_id);
					data.append("inversion_id", this.inversion_id);
					data.append("frmDatosCierre", JSON.stringify(this.frmDatosCierre));

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("inv.meta.cerrar"), data)
								.then((response) => {
									this.cerrar_inversion = false;
									this.modo = "VOUCHER";
									this.datos_voucher = response.data.datos_voucher;
									this.ListarRecursos();
									this.ImprimirVoucher("cierre");
									return Swal.fire({
										icon: "success",
										title: response.data.message,
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
				} else {
					return false;
				}
			});
		},
		async ImprimirVoucher(concepto) {
			Swal.fire({
				title: "GENERANDO",
				showConfirmButton: false,
				allowOutsideClick: false,
				willOpen: async () => {
					Swal.showLoading();

					let data = new FormData();

					data.append("titulo", "PRODUCTO META");
					data.append("agencia_id", this.agencia_id);
					data.append("agencia", this.$page.props.user_session.nombre_agencia);
					data.append("usuario", this.$page.props.user_session.usuario);
					data.append(
						"dispositivo",
						this.$page.props.user_session.dispositivo.nombre
					);

					let frmDatosVoucher = {};

					if (concepto == "movimiento") {
						let tipo = this.frmDatosMovimiento.tipo;
						if (tipo == "I") {
							data.append("concepto", "ABONO");
						} else {
							data.append("concepto", "RETIRO");
						}
						frmDatosVoucher = {
							cliente: this.nombre_completo_titular,
							producto: this.datos_inversion.producto,
							monto: this.frmDatosMovimiento.monto,
							comentario: this.datos_voucher.comentario,
						};
					} else if (concepto == "cierre") {
						data.append("concepto", "RETIRO - CIERRE");
						frmDatosVoucher = {
							cliente: this.nombre_completo_titular,
							producto: this.datos_inversion.producto,
							monto: this.frmDatosCierre.acumulado,
							comentario: this.datos_voucher.comentario,
						};
					}

					// this.$inertia.post(route("inv.meta.voucher"), data);
					data.append("frmDatosVoucher", JSON.stringify(frmDatosVoucher));

					return await axios
						.post(route("inv.meta.voucher"), data)
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
						})
						.catch((error) => {
							Swal.showValidationMessage(
								`Ha ocurrido un error, comunicar a TI: ${error}`
							);
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-inversion-meta {
	width: 50% !important;
}
.input-information {
	height: 2em !important;
	color: black;
}

.input-highlight {
	font-weight: bolder;
	color: var(--colorAlto);
}

.span-highlight {
	font-weight: bolder;
	color: white;
	background-color: var(--verdeOscuroEmpresarial);
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

/* @media only screen and (max-width: 1600px) {
	.slot-inversion-meta {
		width: 40% !important;
	}
} */

@media only screen and (max-width: 1600px) {
	.slot-inversion-meta {
		width: 60% !important;
	}
}

@media only screen and (max-width: 900px) {
	.slot-inversion-meta {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>

