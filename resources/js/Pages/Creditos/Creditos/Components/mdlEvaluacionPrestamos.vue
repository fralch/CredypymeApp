<template>
	<div id="mdlEvaluacionPrestamos" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlEvaluacionPrestamos">
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
							@click="Cerrar('mdlEvaluacionPrestamos')"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>

					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="prestamos-tab"
									data-toggle="tab"
									href="#prestamos"
									role="tab"
									aria-controls="prestamos"
									aria-selected="true"
									>PRÉSTAMOS ACTUALES</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="datos-prestamo-tab"
									data-toggle="tab"
									href="#datos-prestamo"
									role="tab"
									aria-controls="datos-prestamo"
									aria-selected="false"
									:hidden="deshabilitado"
									>DATOS DEL PRÉSTAMO</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="prestamos"
								role="tabpanel"
								aria-labelledby="prestamos-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-4">
										<div class="form-check mt-2">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbVigentes"
												v-model="solo_vigentes"
												style="font-size: 1rem"
											/>
											<label
												class="form-check-label label-title"
												for="chbVigentes"
												style="font-size: 1rem"
											>
												MOSTRAR SÓLO VIGENTES
											</label>
										</div>
									</div>
								</div>

								<table class="table" id="tblPrestamos" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 50px !important">ACCIONES</th>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 50px !important">ESTADO</th>
											<th style="min-width: 100px !important">ENTIDAD</th>
											<th style="min-width: 70px !important">MONTO(S/)</th>
											<th style="min-width: 80px !important">
												TOTAL_PAGAR(S/)
											</th>
											<th style="min-width: 50px !important">PLAZO</th>
											<th style="min-width: 70px !important">FRECUENCUA</th>
											<th style="min-width: 70px !important">CUOTA(S/)</th>
											<th style="min-width: 50px !important">CUO_PAGAD</th>
											<th style="min-width: 50px !important">CUO_PENDI</th>
											<th style="min-width: 80px !important">
												SALDO_PENDI(S/)
											</th>
											<th style="min-width: 100px !important">
												FECHA_REGISTRO
											</th>
											<th style="min-width: 100px !important">
												USUARIO_REGISTRO
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_datos"
											:key="index"
											class="table-bordered"
										>
											<td align="center">
												<div class="btn-group" role="group">
													<button
														class="btn btn-action btn-icon-split"
														title="Editar"
														@click="Editar(item)"
													>
														<span class="icon text-white">
															<i class="fas fa-edit"></i>
														</span>
													</button>
													<button
														class="btn btn-danger btn-icon-split"
														title="Eliminar"
														@click="Eliminar(index)"
													>
														<span class="icon text-white">
															<i class="fas fa-trash"></i>
														</span>
													</button>
												</div>
											</td>
											<td align="center">
												{{ index + 1 }}
											</td>
											<td
												:class="[
													item.cuotas_pagadas == item.plazo
														? 'cancelada'
														: 'vigente',
												]"
												align="center"
											>
												{{
													item.cuotas_pagadas == item.plazo
														? "CANCELADA"
														: "VIGENTE"
												}}
											</td>
											<td>
												{{ item.entidad }}
											</td>
											<td align="right">
												{{ parseFloat(item.monto_prestamo).toFixed(2) }}
											</td>
											<td align="right">
												{{
													parseFloat(item.monto_cuota * item.plazo).toFixed(2)
												}}
											</td>
											<td align="center">
												{{ item.plazo }}
											</td>
											<td align="center">
												{{
													item.frecuencia_pag == 1
														? "MENSUAL"
														: item.frecuencia_pag == 2
														? "QUINCENAL"
														: item.frecuencia_pago == 4
														? "SEMANAL"
														: item.frecuencia_pago == 30
														? "DIARIO"
														: "OTROS"
												}}
											</td>
											<td align="right">
												{{ parseFloat(item.monto_cuota).toFixed(2) }}
											</td>
											<td align="center">
												{{ item.cuotas_pagadas }}
											</td>
											<td align="center">
												{{ item.plazo - item.cuotas_pagadas }}
											</td>
											<td align="right">
												{{
													parseFloat(
														(item.plazo - item.cuotas_pagadas) *
															item.monto_cuota
													).toFixed(2)
												}}
											</td>
											<td align="center">
												{{ item.fecha_registro }}
											</td>
											<td align="center">
												{{ item.usuario_registro }}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
								<div class="text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="Nuevo"
										>
											<span class="icon text-white">
												<i class="fas fa-plus"></i
											></span>
											<span class="text">NUEVO</span>
										</button>
									</div>
								</div>
							</div>
							<div
								class="tab-pane fade"
								id="datos-prestamo"
								role="tabpanel"
								aria-labelledby="datos-prestamo-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-5 col-12">
										<label class="label-title">ENTIDAD</label>
										<span
											v-if="submited && !$v.frmDatosPrestamo.entidad.required"
											class="span-error-message"
										>
											*
										</span>
										<textarea
											class="form-control mayus"
											v-model="frmDatosPrestamo.entidad"
											rows="1"
											maxlength="100"
											oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										>
										</textarea>
									</div>
									<div class="form-group col-md-2 col-4">
										<label class="label-title">MONTO (S/)</label>
										<span
											v-if="
												submited &&
												(!$v.frmDatosPrestamo.monto_prestamo.required ||
													!$v.frmDatosPrestamo.monto_prestamo.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="number"
											min="1"
											step="0.1"
											name="monto_prestamo"
											class="form-control center bolder"
											@change="Redondear"
											v-model.number="frmDatosPrestamo.monto_prestamo"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-4">
										<label class="label-title">FRECUENCIA</label>
										<span
											v-if="
												submited &&
												(!$v.frmDatosPrestamo.frecuencia_pago.required ||
													!$v.frmDatosPrestamo.frecuencia_pago.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
										<select
											class="form-control center"
											v-model.number="frmDatosPrestamo.frecuencia_pago"
										>
											<option :value="0">Seleccione...</option>
											<option :value="1">MENSUAL</option>
											<option :value="2">QUINCENAL</option>
											<option :value="4">SEMANAL</option>
											<option :value="30">DIARIO</option>
										</select>
									</div>
									<div class="form-group col-md-2 col-4">
										<label class="label-title">CUOTA (S/)</label>
										<span
											v-if="
												submited &&
												(!$v.frmDatosPrestamo.monto_cuota.required ||
													!$v.frmDatosPrestamo.monto_cuota.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="number"
											class="form-control center bolder"
											min="0.1"
											step="0.1"
											name="monto_cuota"
											@change="Redondear"
											v-model.number="frmDatosPrestamo.monto_cuota"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-5 col-8">
										<label class="label-title">COMENTARIO</label>
										<span
											v-if="
												submited && !$v.frmDatosPrestamo.comentario.required
											"
											class="span-error-message"
										>
											*
										</span>
										<textarea
											type="text"
											rows="2"
											class="form-control mayus text-row"
											v-model="frmDatosPrestamo.comentario"
											maxlength="200"
											oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>
									<div class="form-group col-md-2 col-4">
										<label class="label-title">PLAZO</label>
										<span
											v-if="
												submited &&
												(!$v.frmDatosPrestamo.plazo.required ||
													!$v.frmDatosPrestamo.plazo.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="number"
											min="1"
											step="1"
											class="form-control center"
											v-model.number="frmDatosPrestamo.plazo"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-2 col-4">
										<label class="label-title">PAGADAS</label>
										<span
											v-if="
												submited && !$v.frmDatosPrestamo.cuotas_pagadas.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="number"
											min="1"
											step="1"
											class="form-control center"
											v-model.number="frmDatosPrestamo.cuotas_pagadas"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-8">
										<label class="label-title">DÍA DE PAGO</label>
										<span
											v-if="submited && !$v.frmDatosPrestamo.dia_pago.required"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosPrestamo.dia_pago"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
								</div>
								<hr />
								<div class="text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="Guardar"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i
											></span>
											<span class="text">GUARDAR</span>
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
</template>

<script>
import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	props: { evaluacion_id: Number, cliente_id: Number, agencia_id: Number },
	data() {
		return {
			submited: false,
			grupo: null,
			sub_grupo: null,
			title_modal: null,
			modo: "BLOQUEADO",
			solo_vigentes: true,
			lista_datos: [],
			lista_datos_actuales: [],
			lista_datos_nuevos: [],
			frmDatosPrestamo: {
				indice: null,
				entidad: null,
				monto_prestamo: parseFloat(0).toFixed(2),
				frecuencia_pago: 0,
				monto_cuota: parseFloat(0).toFixed(2),
				plazo: 0,
				cuotas_pagadas: 0,
				dia_pago: null,
				comentario: null,
				fecha_registro: 0,
				usuario_registro: 0,
			},
			no_editable: true,
			ruta_guardar: null,
			total: 0.0,
		};
	},
	computed: {
		deshabilitado() {
			if (this.modo == "BLOQUEADO") {
				return true;
			} else {
				return false;
			}
		},
	},
	mounted() {
		this.ResetearFormulario();
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
		lista_datos() {
			this.RecrearTabla();
		},
	},

	validations: {
		frmDatosPrestamo: {
			entidad: { required },
			monto_prestamo: { required, noZero },
			frecuencia_pago: { required, noZero },
			monto_cuota: { required, noZero },
			plazo: { required, noZero },
			cuotas_pagadas: { required, noZero },
			dia_pago: { required },
			comentario: { required },
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

			if (e.target.value) {
				valor = e.target.value;
			}

			if (e.target.name == "monto_prestamo") {
				this.frmDatosPrestamo.monto_prestamo = this.$parent.round(
					valor,
					numero_decimales
				);
			} else if (e.target.name == "monto_cuota") {
				this.frmDatosPrestamo.monto_cuota = this.$parent.round(
					valor,
					numero_decimales
				);
			}
		},
		TablaPrestamos() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblPrestamos").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
					select: {
						style: "single",
						info: false,
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
					responsive: true,
				});

				$("#chbVigentes").change(function () {
					if (self.solo_vigentes) {
						table.column(2).search("VIGENTE").draw();
					} else {
						table.column(2).search("").draw();
					}
				});

				if (self.solo_vigentes) {
					table.column(2).search("VIGENTE").draw();
				} else {
					table.column(2).search("").draw();
				}
			});
		},
		RecrearTabla() {
			$("#tblPrestamos").DataTable().destroy();
			this.TablaPrestamos();
		},
		ResetearFormulario() {
			let formulario = this.frmDatosPrestamo;
			formulario.indice = null;
			formulario.entidad = null;
			formulario.monto_prestamo = parseFloat(0).toFixed(2);
			formulario.frecuencia_pago = 0;
			formulario.monto_cuota = parseFloat(0).toFixed(2);
			formulario.plazo = 0;
			formulario.cuotas_pagadas = 0;
			formulario.dia_pago = null;
			formulario.comentario = null;
			formulario.fecha_registro = 0;
			formulario.usuario_registro = 0;
		},
		Cerrar(ventana) {
			$("#" + ventana).css("display", "none");
			$("#prestamos-tab").tab("show");
		},
		Nuevo() {
			this.ResetearFormulario();
			this.modo = "NUEVO";
			$("#datos-prestamo-tab").tab("show");
		},
		Editar(bien) {
			this.modo = "EDITAR";

			this.frmDatosPrestamo.indice = bien.indice;
			this.frmDatosPrestamo.entidad = bien.entidad;
			this.frmDatosPrestamo.monto_prestamo = parseFloat(
				bien.monto_prestamo
			).toFixed(2);
			this.frmDatosPrestamo.frecuencia_pago = bien.frecuencia_pago;
			this.frmDatosPrestamo.monto_cuota = parseFloat(bien.monto_cuota).toFixed(
				2
			);
			this.frmDatosPrestamo.plazo = bien.plazo;
			this.frmDatosPrestamo.cuotas_pagadas = bien.cuotas_pagadas;
			this.frmDatosPrestamo.dia_pago = bien.dia_pago;
			this.frmDatosPrestamo.comentario = bien.comentario;
			this.frmDatosPrestamo.fecha_registro = bien.fecha_registro;
			this.frmDatosPrestamo.usuario_registro = bien.usuario_registro;

			$("#datos-prestamo-tab").tab("show");
		},
		Eliminar(indice) {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea eliminar este préstamo?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					self.lista_datos.splice(indice, 1);
					let data = new FormData();
					data.append("evaluacion_id", self.evaluacion_id);
					data.append("cliente_id", self.cliente_id);
					data.append(
						"valor",
						self.lista_datos.length == 0
							? null
							: JSON.stringify(self.lista_datos)
					);

					data.append("sub_grupo", self.sub_grupo);
					data.append("agencia_id", self.agencia_id);

					self.$inertia.post(route(self.ruta_guardar), data, {
						preserveScroll: true,
						onStart: (visit) => {
							let timerInterval;
							Swal.fire({
								title: "CARGANDO",
								html: "Espere porfavor...",
								timer: 1200,
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
							Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								allowOutsideClick: false,
								preConfirm: async (result) => {
									self.submited = false;
									self.ResetearFormulario();
									await self.RecrearTabla();
									self.modo = "BLOQUEADO";
									$("#prestamos-tab").tab("show");
								},
							});
						},
					});
				} else {
					return false;
				}
			});
		},
		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosPrestamo.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos",
					allowOutsideClick: true,
				});
				return false;
			}

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
			}).then(async (result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("evaluacion_id", self.evaluacion_id);
					data.append("cliente_id", self.cliente_id);

					let formulario = self.frmDatosPrestamo;

					let indice =
						self.lista_datos.length == 0
							? 0
							: self.lista_datos[self.lista_datos.length - 1].indice + 1;

					let fecha_actual = await self.$parent.fecha_hora_actual(
						self.agencia_id
					);

					let object = {
						indice: self.modo == "NUEVO" ? indice : formulario.indice,
						entidad: formulario.entidad.toUpperCase(),
						monto_prestamo: formulario.monto_prestamo,
						frecuencia_pago: formulario.frecuencia_pago,
						monto_cuota: formulario.monto_cuota,
						plazo: formulario.plazo,
						cuotas_pagadas: formulario.cuotas_pagadas,
						dia_pago: formulario.dia_pago,
						comentario: formulario.comentario.toUpperCase(),
						fecha_registro: fecha_actual,

						usuario_registro: self.$inertia.page.props.user_session.usuario,
					};

					if (self.modo == "NUEVO") {
						self.lista_datos.push(object);
					} else {
						let elemento = self.lista_datos.filter(
							(item) => item.indice == formulario.indice
						)[0];

						elemento.entidad = object.entidad;
						elemento.monto_prestamo = object.monto_prestamo;
						elemento.frecuencia_pago = object.frecuencia_pago;
						elemento.monto_cuota = object.monto_cuota;
						elemento.plazo = object.plazo;
						elemento.cuotas_pagadas = object.cuotas_pagadas;
						elemento.dia_pago = object.dia_pago;
						elemento.comentario = object.comentario;
						elemento.fecha_registro = object.fecha_registro;
						elemento.usuario_registro = object.usuario_registro;
					}

					data.append(
						"valor",
						self.lista_datos.length == 0
							? null
							: JSON.stringify(self.lista_datos)
					);

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
						onSuccess: async () => {
							self.submited = false;
							self.ResetearFormulario();
							await self.RecrearTabla();
							self.modo = "BLOQUEADO";
							$("#prestamos-tab").tab("show");
						},
					});
				} else {
					return false;
				}
			});
		},
	},
};
</script>

<style lang="css">
.vigente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.cancelada {
	background-color: var(--plomoOscuroEmpresarial) !important;
	color: white !important;
}

.mdlEvaluacionPrestamos {
	margin-top: 5% !important;
}

@media only screen and (max-width: 900px) {
	.mdlEvaluacionPrestamos {
		margin-top: 15% !important;
	}
}
</style>
