<template>
	<div id="mdlEvaluacionVehiculos" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlEvaluacionVehiculos">
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
							@click="Cerrar('mdlEvaluacionVehiculos')"
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
									id="vehiculos-tab"
									data-toggle="tab"
									href="#vehiculos"
									role="tab"
									aria-controls="vehiculos"
									aria-selected="true"
									>LISTA DE VEHICULOS</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="datos-vehiculo-tab"
									data-toggle="tab"
									href="#datos-vehiculo"
									role="tab"
									aria-controls="datos-vehiculo"
									aria-selected="false"
									:hidden="deshabilitado"
									>DATOS DEL VEHÍCULO</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="vehiculos"
								role="tabpanel"
								aria-labelledby="vehiculos-tab"
							>
								<table class="table" id="tblVehiculos" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 50px !important">ACCIONES</th>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 80px !important">TIPO</th>
											<th style="min-width: 80px !important">MARCA</th>
											<th style="min-width: 80px !important">MODELO</th>
											<th style="min-width: 50px !important">AÑO_FABR</th>
											<th style="min-width: 50px !important">PLACA</th>
											<th style="min-width: 70px !important">NRO_MOTOR</th>
											<th style="min-width: 70px !important">NRO_CHASIS</th>
											<th style="min-width: 50px !important">COLOR</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in lista_datos" :key="index">
											<td class="table-bordered" align="center">
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
														class="btn btn-cancel btn-icon-split"
														title="Eliminar"
														@click="Eliminar(index)"
													>
														<span class="icon text-white">
															<i class="fas fa-trash"></i>
														</span>
													</button>
												</div>
											</td>
											<td class="table-bordered" align="center">
												{{ index + 1 }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.tipo }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.marca }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.modelo }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.año_fabricacion }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.placa }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.numero_motor }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.numero_serie_chasis }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.color }}
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
								id="datos-vehiculo"
								role="tabpanel"
								aria-labelledby="datos-vehiculo-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-3 col-6">
										<label class="label-title">TIPO DE VEHÍCULO</label>
										<span
											v-if="submited && !$v.frmDatosVehiculo.tipo.required"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.tipo"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">MARCA</label>
										<span
											v-if="submited && !$v.frmDatosVehiculo.marca.required"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.marca"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">MODELO</label>
										<span
											v-if="submited && !$v.frmDatosVehiculo.modelo.required"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.modelo"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">AÑO FABRICACIÓN</label>
										<span
											v-if="
												submited &&
												!$v.frmDatosVehiculo.año_fabricacion.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="number"
											min="0"
											step="10"
											class="form-control center"
											v-model.number="frmDatosVehiculo.año_fabricacion"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">PLACA</label>
										<span
											v-if="submited && !$v.frmDatosVehiculo.placa.required"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus center"
											v-model="frmDatosVehiculo.placa"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">N° MOTOR</label>
										<span
											v-if="
												submited && !$v.frmDatosVehiculo.numero_motor.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.numero_motor"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">N° SERIE/CHASIS</label>
										<span
											v-if="
												submited &&
												!$v.frmDatosVehiculo.numero_serie_chasis.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.numero_serie_chasis"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">COLOR</label>
										<span
											v-if="submited && !$v.frmDatosVehiculo.color.required"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus center"
											v-model="frmDatosVehiculo.color"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">CARROCERÍA</label>
										<span
											v-if="
												submited && !$v.frmDatosVehiculo.carroceria.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.carroceria"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">PARTIDA ELECTRÓNICA</label>
										<span
											v-if="
												submited &&
												!$v.frmDatosVehiculo.partida_electronica.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.partida_electronica"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">OFICINA REGISTRAL</label>
										<span
											v-if="
												submited &&
												!$v.frmDatosVehiculo.oficina_registral.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosVehiculo.oficina_registral"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">VALORIZACIÓN(S/)</label>
										<span
											v-if="
												submited && !$v.frmDatosVehiculo.valorizacion.required
											"
											class="span-error-message"
										>
											*
										</span>
										<input
											type="number"
											min="0"
											step="100"
											class="form-control center"
											v-model.number="frmDatosVehiculo.valorizacion"
											@change="Redondear"
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
			lista_datos: [],
			lista_datos_actuales: [],
			lista_datos_nuevos: [],
			frmDatosVehiculo: {
				indice: null,
				tipo: null,
				marca: null,
				modelo: null,
				año_fabricacion: 2000,
				placa: null,
				numero_motor: null,
				numero_serie_chasis: null,
				color: null,
				carroceria: null,
				partida_electronica: null,
				oficina_registral: null,
				valorizacion: parseFloat(1000).toFixed(2),
			},
			no_editable: true,
			ruta_guardar: null,
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
			}
		},
		lista_datos() {
			this.RecrearTabla();
		},
	},

	validations: {
		frmDatosVehiculo: {
			tipo: { required },
			marca: { required },
			modelo: { required },
			año_fabricacion: { required, noZero },
			placa: { required },
			numero_motor: { required },
			numero_serie_chasis: { required },
			color: { required },
			carroceria: { required },
			partida_electronica: { required },
			oficina_registral: { required },
			valorizacion: { required, noZero },
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

			if (e.target.name == "valorizacion") {
				this.frmDatosVehiculo.valorizacion = this.$parent.round(
					valor,
					numero_decimales
				);
			}
		},
		TablaVehiculos() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblVehiculos").DataTable({
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
			});
		},
		RecrearTabla() {
			$("#tblVehiculos").DataTable().destroy();
			this.TablaVehiculos();
		},
		ResetearFormulario() {
			let formulario = this.frmDatosVehiculo;
			formulario.indice = null;
			formulario.tipo = null;
			formulario.marca = null;
			formulario.modelo = null;
			formulario.año_fabricacion = 2000;
			formulario.placa = null;
			formulario.numero_motor = null;
			formulario.numero_serie_chasis = null;
			formulario.color = null;
			formulario.carroceria = null;
			formulario.partida_electronica = null;
			formulario.oficina_registral = null;
			formulario.valorizacion = parseFloat(1000).toFixed(2);
		},
		Cerrar(ventana) {
			$("#" + ventana).css("display", "none");
			$("#vehiculos-tab").tab("show");
		},
		Nuevo() {
			this.ResetearFormulario();
			this.modo = "NUEVO";
			$("#datos-vehiculo-tab").tab("show");
		},
		Editar(bien) {
			this.modo = "EDITAR";

			this.frmDatosVehiculo.indice = bien.indice;
			this.frmDatosVehiculo.tipo = bien.tipo;
			this.frmDatosVehiculo.marca = bien.marca;
			this.frmDatosVehiculo.modelo = bien.modelo;
			this.frmDatosVehiculo.año_fabricacion = bien.año_fabricacion;
			this.frmDatosVehiculo.placa = bien.placa;
			this.frmDatosVehiculo.numero_motor = bien.numero_motor;
			this.frmDatosVehiculo.numero_serie_chasis = bien.numero_serie_chasis;
			this.frmDatosVehiculo.color = bien.color;
			this.frmDatosVehiculo.carroceria = bien.carroceria;
			this.frmDatosVehiculo.partida_electronica = bien.partida_electronica;
			this.frmDatosVehiculo.oficina_registral = bien.oficina_registral;
			this.frmDatosVehiculo.valorizacion = parseFloat(
				bien.valorizacion
			).toFixed(2);

			$("#datos-vehiculo-tab").tab("show");
		},
		Eliminar(indice) {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea eliminar este vehículo?",
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
									$("#vehiculos-tab").tab("show");
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
			if (this.$v.frmDatosVehiculo.$invalid) {
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

					let formulario = self.frmDatosVehiculo;

					let indice =
						self.lista_datos.length == 0
							? 0
							: self.lista_datos[self.lista_datos.length - 1].indice + 1;

					let fecha_actual = await self.$parent.fecha_hora_actual(
						self.agencia_id
					);

					let object = {
						indice: self.modo == "NUEVO" ? indice : formulario.indice,
						tipo: formulario.tipo.toUpperCase(),
						marca: formulario.marca.toUpperCase(),
						modelo: formulario.modelo.toUpperCase(),
						año_fabricacion: formulario.año_fabricacion,
						placa: formulario.placa.toUpperCase(),
						numero_motor: formulario.numero_motor.toUpperCase(),
						numero_serie_chasis: formulario.numero_serie_chasis.toUpperCase(),
						color: formulario.color.toUpperCase(),
						carroceria: formulario.carroceria.toUpperCase(),
						partida_electronica: formulario.partida_electronica.toUpperCase(),
						oficina_registral: formulario.oficina_registral.toUpperCase(),
						valorizacion: formulario.valorizacion,

						fecha_registro: fecha_actual,

						usuario_registro: self.$inertia.page.props.user_session.usuario,
					};

					if (self.modo == "NUEVO") {
						self.lista_datos.push(object);
					} else {
						let elemento = self.lista_datos.filter(
							(item) => item.indice == formulario.indice
						)[0];

						elemento.tipo = object.tipo;
						elemento.marca = object.marca;
						elemento.modelo = object.modelo;
						elemento.año_fabricacion = object.año_fabricacion;
						elemento.placa = object.placa;
						elemento.numero_motor = object.numero_motor;
						elemento.numero_serie_chasis = object.numero_serie_chasis;
						elemento.color = object.color;
						elemento.carroceria = object.carroceria;
						elemento.partida_electronica = object.partida_electronica;
						elemento.oficina_registral = object.oficina_registral;
						elemento.valorizacion = object.valorizacion;
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
							$("#vehiculos-tab").tab("show");
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
.mdlEvaluacionVehiculos {
	margin-top: 5% !important;
}

@media only screen and (max-width: 900px) {
	.mdlEvaluacionVehiculos {
		margin-top: 15% !important;
	}
}
</style>
