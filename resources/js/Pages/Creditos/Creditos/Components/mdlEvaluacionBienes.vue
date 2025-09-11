<template>
	<div id="mdlEvaluacionBienes" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlEvaluacionBienes">
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
							@click="Cerrar('mdlEvaluacionBienes')"
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
									id="inventario-tab"
									data-toggle="tab"
									href="#inventario"
									role="tab"
									aria-controls="inventario"
									aria-selected="true"
									>INVENTARIO ACTUAL</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="datos-tab"
									data-toggle="tab"
									href="#datos"
									role="tab"
									aria-controls="datos"
									aria-selected="false"
									:hidden="deshabilitado"
									>DATOS DEL BIEN</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="foto-tab"
									data-toggle="tab"
									href="#foto"
									role="tab"
									aria-controls="foto"
									aria-selected="false"
									:hidden="deshabilitado"
									>FOTOS</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="inventario"
								role="tabpanel"
								aria-labelledby="inventario-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-6">
										<div class="form-check mt-2">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbDisponibles"
												v-model="solo_disponibles"
												style="font-size: 1rem"
											/>
											<label
												class="form-check-label label-title"
												for="chbDisponibles"
												style="font-size: 1rem"
											>
												MOSTRAR SÓLO DISPONIBLES
											</label>
										</div>
									</div>
									<div class="col-md-6">
										<label class="label-title"> VALORIZACIÓN TOTAL </label>

										<label
											class="form-control-label"
											style="font-size: 1.2rem; color: var(--colorAlto)"
										>
											S/ {{ parseFloat(total).toFixed(2) }}</label
										>
									</div>
								</div>

								<table class="table" id="tblBienes" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 50px !important">ACCIONES</th>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 50px !important">DISP.</th>
											<th style="min-width: 100px !important">DESCRIPCIÓN</th>
											<th style="min-width: 70px !important">CANT.</th>
											<th style="min-width: 70px !important">VALOR</th>
											<th style="min-width: 100px !important">MARCA</th>
											<th style="min-width: 10px !important">MODELO</th>
											<th style="min-width: 100px !important">SERIE</th>
											<th style="min-width: 100px !important">ESTADO</th>
											<th style="min-width: 200px !important">COMENTARIO</th>
											<th style="min-width: 100px !important">
												FECHA_REGISTRO
											</th>
											<th style="min-width: 100px !important">
												USUARIO_REGISTRO
											</th>
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
											<td class="table-bordered" align="center">
												{{ index + 1 }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.disponible == 1 ? "SI" : "NO" }}
											</td>
											<td class="table-bordered">
												{{ item.descripcion }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.cantidad }}
											</td>
											<td class="table-bordered" align="right">
												S/ {{ parseFloat(item.precio_actual).toFixed(2) }}
											</td>
											<td class="table-bordered">
												{{ item.marca }}
											</td>
											<td class="table-bordered">
												{{ item.modelo == null ? "-" : item.modelo }}
											</td>
											<td class="table-bordered">
												{{ item.serie == null ? "-" : item.serie }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.estado }}
											</td>

											<td class="table-bordered">
												{{ item.comentario == null ? "-" : item.comentario }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.fecha_registro }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.usuario_registro }}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
								<div class="text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-cancel btn-icon-split"
											@click="Nuevo"
										>
											<span class="icon text-white">
												<i class="fas fa-plus"></i
											></span>
											<span class="text">NUEVO</span>
										</button>
										<button
											class="btn btn-action btn-icon-split"
											@click="
												GenerarDocumento(
													'rptEvaluacionBienes',
													'rptEvaluacionBienes' +
														'_' +
														cliente_id +
														'_' +
														evaluacion_id
												)
											"
										>
											<span class="icon text-white">
												<i class="fas fa-file-word"></i
											></span>
											<span class="text">VISTA PREVIA</span>
										</button>
									</div>
								</div>
								<rptEvaluacionBienes
									ref="rptEvaluacionBienes"
								></rptEvaluacionBienes>
							</div>
							<div
								class="tab-pane fade"
								id="datos"
								role="tabpanel"
								aria-labelledby="datos-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-2 col-3">
										<label class="label-title">CANTIDAD</label>

										<input
											type="number"
											min="1"
											step="1"
											class="form-control center"
											:value="frmDatosBien.cantidad"
											readonly
										/>
									</div>
									<div class="form-group col-md-7 col-9">
										<span
											v-if="submited && !$v.frmDatosBien.descripcion.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">DESCRIPCIÓN</label>

										<textarea
											class="form-control mayus"
											rows="2"
											v-model="frmDatosBien.descripcion"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosBien.marca.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">MARCA</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosBien.marca"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">MODELO</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosBien.modelo"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">SERIE</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosBien.serie"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosBien.color.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">COLOR</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosBien.color"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosBien.estado.noZero"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">ESTADO</label>

										<select
											class="form-control center"
											v-model="frmDatosBien.estado"
											:disabled="deshabilitado"
										>
											<option :value="0" disabled>Seleccione...</option>
											<option value="EXCELENTE">EXCELENTE</option>
											<option value="MUY BUENO">MUY BUENO</option>
											<option value="BUENO">BUENO</option>
											<option value="REGULAR">REGULAR</option>
											<option value="DETERIORADO">DETERIORADO</option>
										</select>
									</div>

									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosBien.fecha_compra.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">FECHA COMPRA</label>

										<input
											type="date"
											class="form-control center"
											v-model="frmDatosBien.fecha_compra"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">N° COMPROBANTE</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosBien.numero_comprobante"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosBien.precio_compra.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">P. COMPRA(S/)</label>

										<input
											type="number"
											min="0"
											step="0.1"
											class="form-control center"
											v-model.number="frmDatosBien.precio_compra"
											@change="Redondear"
											name="precio_compra"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosBien.precio_actual.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">P. ACTUAL(S/)</label>

										<input
											type="number"
											min="0"
											step="0.1"
											class="form-control center"
											v-model.number="frmDatosBien.precio_actual"
											name="precio_actual"
											@change="Redondear"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-12">
										<label class="label-title">COMENTARIO</label>

										<textarea
											class="form-control mayus text-row"
											rows="3"
											v-model="frmDatosBien.comentario"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>

									<div class="form-group col-md-4" v-if="modo == 'EDITAR'">
										<div class="form-check">
											<input
												class="form-check-input"
												type="checkbox"
												id="flexCheckChecked"
												style="font-size: 1rem"
												v-model="frmDatosBien.disponible"
												:disabled="deshabilitado"
											/>
											<label
												class="form-check-label label-title"
												for="flexCheckChecked"
												style="font-size: 1rem"
											>
												DISPONIBLE
											</label>
										</div>
									</div>
									<div
										class="form-group col-md-4 col-6"
										v-if="modo == 'EDITAR'"
									>
										<p class="label-title center">USUARIO REGISTRO</p>
										<p
											class="label-title center text-white"
											style="background: var(--plomoOscuroEmpresarial)"
										>
											{{ frmDatosBien.usuario_registro }}
										</p>
									</div>
									<div
										class="form-group col-md-4 col-6"
										v-if="modo == 'EDITAR'"
									>
										<p class="label-title center">FECHA REGISTRO</p>
										<p
											class="label-title center text-white"
											style="background: var(--plomoOscuroEmpresarial)"
										>
											{{ frmDatosBien.fecha_registro }}
										</p>
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
							<div
								class="tab-pane fade"
								id="foto"
								role="tabpanel"
								aria-labelledby="foto-tab"
							>
								<div class="form-row">
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_1"
									>
										<label
											for="foto_1"
											class="subir"
											v-if="frmDatosBien.foto.foto_1 == null"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_1"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosBien.foto.foto_1 == null"
										/>

										<img class="d-block w-100" id="img_1" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_1')"
											v-if="frmDatosBien.foto.foto_1 != null"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>

									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_2"
									>
										<label
											for="foto_2"
											class="subir"
											v-if="frmDatosBien.foto.foto_2 == null"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_2"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosBien.foto.foto_2 == null"
										/>

										<img class="d-block w-100" id="img_2" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_2')"
											v-if="frmDatosBien.foto.foto_2 != null"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_3"
									>
										<label
											for="foto_3"
											class="subir"
											v-if="frmDatosBien.foto.foto_3 == null"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_3"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosBien.foto.foto_3 == null"
										/>

										<img class="d-block w-100" id="img_3" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_3')"
											v-if="frmDatosBien.foto.foto_3 != null"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_4"
									>
										<label
											for="foto_4"
											class="subir"
											v-if="frmDatosBien.foto.foto_4 == null"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_4"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosBien.foto.foto_4 == null"
										/>

										<img class="d-block w-100" id="img_4" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_4')"
											v-if="frmDatosBien.foto.foto_4 != null"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
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
import rptEvaluacionBienes from "@/Pages/Creditos/Creditos/Reports/rptEvaluacionBienes.vue";
const noZero = (value) => value != 0;
export default {
	components: {
		rptEvaluacionBienes,
	},
	props: {
		evaluacion_id: Number,
		cliente_id: Number,
		datos_personales: Object,
		datos_agencia: Object,
		agencia_id: Number,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			grupo: null,
			sub_grupo: null,
			title_modal: null,
			modo: "BLOQUEADO",
			solo_disponibles: true,
			lista_datos: [],
			lista_datos_actuales: [],
			lista_datos_nuevos: [],
			frmDatosBien: {
				indice: null,
				cantidad: 1,
				descripcion: null,
				marca: null,
				modelo: null,
				serie: null,
				color: null,
				estado: 0,
				fecha_compra: null,
				numero_comprobante: null,
				precio_compra: parseFloat(0).toFixed(2),
				precio_actual: parseFloat(0).toFixed(2),
				disponible: 0,
				comentario: null,
				fecha_registro: null,
				usuario_registro: null,
				foto: { foto_1: null, foto_2: null, foto_3: null, foto_4: null },
				nueva_foto: {
					nuevo_1: false,
					nuevo_2: false,
					nuevo_3: false,
					nuevo_4: false,
				},
			},
			contenido_foto: {
				border: "1px solid black",
				width: "90%",
				height: "350px",
				"margin-left": "5%",
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
	validations: {
		frmDatosBien: {
			cantidad: { required, noZero },
			descripcion: { required },
			marca: { required },

			color: { required },
			estado: { noZero },
			fecha_compra: { required },

			precio_compra: { required },
			precio_actual: { required },
		},
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
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			if (this.windowWidth < 1000) {
				this.contenido_foto.height = "200px";
			} else {
				this.contenido_foto.height = "350px";
			}
		});
		if (this.windowWidth < 1000) {
			this.contenido_foto.height = "200px";
		} else {
			this.contenido_foto.height = "350px";
		}
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

			if (e.target.name == "precio_compra") {
				this.frmDatosBien.precio_compra = this.$parent.round(
					valor,
					numero_decimales
				);
			} else if (e.target.name == "precio_actual") {
				this.frmDatosBien.precio_actual = this.$parent.round(
					valor,
					numero_decimales
				);
			}
		},
		TablaBienes() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblBienes").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					language: {
						retrieve: true,
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});

				$("#chbDisponibles").change(function () {
					self.total = 0;
					if (self.solo_disponibles) {
						table.column(2).search("SI").draw();
						self.lista_datos.forEach((element) => {
							if (element.disponible == true) {
								self.total += parseFloat(element.precio_actual);
							}
						});
					} else {
						table.column(2).search("").draw();
						self.lista_datos.forEach((element) => {
							self.total += parseFloat(element.precio_actual);
						});
					}
				});

				self.total = 0;

				if (self.solo_disponibles) {
					table.column(2).search("SI").draw();
					self.lista_datos.forEach((element) => {
						if (element.disponible == true) {
							self.total += parseFloat(element.precio_actual);
						}
					});
				} else {
					table.column(2).search("").draw();
					self.lista_datos.forEach((element) => {
						self.total += parseFloat(element.precio_actual);
					});
				}
			});
		},
		RecrearTabla() {
			$("#tblBienes").DataTable().destroy();
			this.TablaBienes();
		},
		ResetearFormulario() {
			let formulario = this.frmDatosBien;
			formulario.indice = null;
			formulario.cantidad = 1;
			formulario.descripcion = null;
			formulario.marca = null;
			formulario.modelo = null;
			formulario.serie = null;
			formulario.color = null;
			formulario.estado = 0;
			formulario.fecha_compra = null;
			formulario.numero_comprobante = null;
			formulario.precio_compra = parseFloat(0).toFixed(2);
			formulario.precio_actual = parseFloat(0).toFixed(2);
			formulario.disponible = 0;
			formulario.comentario = null;
			formulario.fecha_registro = null;
			formulario.usuario_registro = null;

			for (let index = 1; index <= 4; index++) {
				let img = $("#contenido_" + index + " img");
				img.remove();
			}

			formulario.foto.foto_1 = null;
			formulario.nueva_foto.nuevo_1 = false;
			formulario.foto.foto_2 = null;
			formulario.nueva_foto.nuevo_2 = false;
			formulario.foto.foto_3 = null;
			formulario.nueva_foto.nuevo_3 = false;
			formulario.foto.foto_4 = null;
			formulario.nueva_foto.nuevo_4 = false;
		},
		Cerrar(ventana) {
			$("#" + ventana).css("display", "none");
			$("#inventario-tab").tab("show");
		},
		Nuevo() {
			this.ResetearFormulario();
			this.modo = "NUEVO";
			$("#datos-tab").tab("show");
		},
		Editar(bien) {
			this.modo = "EDITAR";

			this.frmDatosBien.indice = bien.indice;
			this.frmDatosBien.cantidad = bien.cantidad;
			this.frmDatosBien.descripcion = bien.descripcion;
			this.frmDatosBien.marca = bien.marca;
			this.frmDatosBien.modelo = bien.modelo;
			this.frmDatosBien.serie = bien.serie;
			this.frmDatosBien.color = bien.color;
			this.frmDatosBien.estado = bien.estado;
			this.frmDatosBien.fecha_compra = bien.fecha_compra;
			this.frmDatosBien.numero_comprobante = bien.numero_comprobante;
			this.frmDatosBien.precio_compra = parseFloat(bien.precio_compra).toFixed(
				2
			);
			this.frmDatosBien.precio_actual = parseFloat(bien.precio_actual).toFixed(
				2
			);
			this.frmDatosBien.disponible = bien.disponible;
			this.frmDatosBien.comentario = bien.comentario;
			this.frmDatosBien.foto = bien.foto;
			this.frmDatosBien.fecha_registro = bien.fecha_registro;
			this.frmDatosBien.usuario_registro = bien.usuario_registro;

			for (let index = 1; index <= 4; index++) {
				let img = $("#contenido_" + index + " img");
				img.remove();

				let foto = null;

				if (index == 1) {
					foto = this.frmDatosBien.foto.foto_1;
				} else if (index == 2) {
					foto = this.frmDatosBien.foto.foto_2;
				} else if (index == 3) {
					foto = this.frmDatosBien.foto.foto_3;
				} else if (index == 4) {
					foto = this.frmDatosBien.foto.foto_4;
				}

				if (foto != null) {
					let preview = document.getElementById("contenido_" + index),
						image = document.createElement("img");

					if (index == 1) {
						image.src =
							"/imagenes_server/creditos/evaluaciones/bienes/" +
							this.agencia_id +
							"/" +
							bien.foto.foto_1.substring(0, 4) +
							"/" +
							bien.foto.foto_1;
					} else if (index == 2) {
						image.src =
							"/imagenes_server/creditos/evaluaciones/bienes/" +
							this.agencia_id +
							"/" +
							bien.foto.foto_2.substring(0, 4) +
							"/" +
							bien.foto.foto_2;
					} else if (index == 3) {
						image.src =
							"/imagenes_server/creditos/evaluaciones/bienes/" +
							this.agencia_id +
							"/" +
							bien.foto.foto_3.substring(0, 4) +
							"/" +
							bien.foto.foto_3;
					} else if (index == 4) {
						image.src =
							"/imagenes_server/creditos/evaluaciones/bienes/" +
							this.agencia_id +
							"/" +
							bien.foto.foto_4.substring(0, 4) +
							"/" +
							bien.foto.foto_4;
					}

					image.id = "img_" + index;
					image.style.height = "100%";
					image.style.width = "100%";
					image.style.border = "1px solid #ffff";
					preview.append(image);
				}
			}

			this.frmDatosBien.nueva_foto.nuevo_1 = false;
			this.frmDatosBien.nueva_foto.nuevo_2 = false;
			this.frmDatosBien.nueva_foto.nuevo_3 = false;
			this.frmDatosBien.nueva_foto.nuevo_4 = false;

			$("#datos-tab").tab("show");
		},
		AgregarFoto(e) {
			let img_id = e.target.id;

			let indice = img_id.substr(5, 1);
			let previo = $("#contenido_" + indice + " img");

			previo.remove();

			if (img_id == "foto_1") {
				this.frmDatosBien.foto.foto_1 = e.target.files[0];
				this.frmDatosBien.nueva_foto.nuevo_1 = true;
			} else if (img_id == "foto_2") {
				this.frmDatosBien.foto.foto_2 = e.target.files[0];
				this.frmDatosBien.nueva_foto.nuevo_2 = true;
			} else if (img_id == "foto_3") {
				this.frmDatosBien.foto.foto_3 = e.target.files[0];
				this.frmDatosBien.nueva_foto.nuevo_3 = true;
			} else if (img_id == "foto_4") {
				this.frmDatosBien.foto.foto_4 = e.target.files[0];
				this.frmDatosBien.nueva_foto.nuevo_4 = true;
			}

			let reader = new FileReader();
			reader.readAsDataURL(e.target.files[0]); // leemos el archivo subido y se lo pasamos a nuestro fileReader
			reader.onload = function () {
				let preview = document.getElementById("contenido_" + indice),
					image = document.createElement("img");

				image.src = reader.result;
				image.id = "img_" + indice;
				image.style.height = "100%";
				image.style.width = "100%";
				image.style.border = "1px solid #ffff";

				preview.append(image);
			};
		},
		QuitarFoto(numero_foto) {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea eliminar esta foto?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					if (numero_foto == "foto_1") {
						self.frmDatosBien.foto.foto_1 = null;
						this.frmDatosBien.nueva_foto.nuevo_1 = true;
					} else if (numero_foto == "foto_2") {
						self.frmDatosBien.foto.foto_2 = null;
						this.frmDatosBien.nueva_foto.nuevo_2 = true;
					} else if (numero_foto == "foto_3") {
						self.frmDatosBien.foto.foto_3 = null;
						this.frmDatosBien.nueva_foto.nuevo_3 = true;
					} else if (numero_foto == "foto_4") {
						self.frmDatosBien.foto.foto_4 = null;
						this.frmDatosBien.nueva_foto.nuevo_4 = true;
					}
					let indice = numero_foto.substr(5, 1);
					let img = $("#contenido_" + indice + " img");
					img.remove();
				}
			});
		},
		Eliminar(indice) {
			Swal.fire({
				icon: "question",
				text: "¿Desea eliminar este bien?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.lista_datos.splice(indice, 1);
					let data = new FormData();
					data.append("evaluacion_id", this.evaluacion_id);
					data.append("cliente_id", this.cliente_id);
					data.append(
						"valor",
						this.lista_datos.length == 0
							? null
							: JSON.stringify(this.lista_datos)
					);
					data.append("nueva_foto", false);
					data.append("sub_grupo", this.sub_grupo);
					data.append("agencia_id", this.agencia_id);
					this.$inertia.post(route(this.ruta_guardar), data, {
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
									this.submited = false;
									this.ResetearFormulario();
									this.RecrearTabla();
									this.modo = "BLOQUEADO";
									$("#inventario-tab").tab("show");
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

			if (this.$v.frmDatosBien.$invalid) {
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

					let formulario = self.frmDatosBien;

					let indice =
						self.lista_datos.length == 0
							? 0
							: self.lista_datos[self.lista_datos.length - 1].indice + 1;

					let fecha_actual = await self.$parent.fecha_hora_actual(
						self.agencia_id
					);

					let año = fecha_actual.substring(0, 4);

					let object = {
						indice: self.modo == "NUEVO" ? indice : formulario.indice,
						cantidad: formulario.cantidad,
						descripcion:
							formulario.descripcion == null
								? null
								: formulario.descripcion.toUpperCase(),
						marca:
							formulario.marca == null ? null : formulario.marca.toUpperCase(),
						modelo:
							formulario.modelo == null
								? null
								: formulario.modelo.toUpperCase(),
						serie:
							formulario.serie == null ? null : formulario.serie.toUpperCase(),
						color:
							formulario.color == null ? null : formulario.color.toUpperCase(),
						estado: formulario.estado,
						fecha_compra: formulario.fecha_compra,
						numero_comprobante:
							formulario.numero_comprobante == null
								? null
								: formulario.numero_comprobante.toUpperCase(),
						precio_compra: formulario.precio_compra,
						precio_actual: formulario.precio_actual,
						disponible: self.modo == "NUEVO" ? true : formulario.disponible,
						comentario:
							formulario.comentario == null
								? null
								: formulario.comentario.toUpperCase(),

						fecha_registro: fecha_actual,
						usuario_registro: self.$inertia.page.props.user_session.usuario,
						foto: {
							foto_1: formulario.nueva_foto.nuevo_1
								? formulario.foto.foto_1 != null
									? año +
									  "_" +
									  self.evaluacion_id +
									  "_" +
									  (self.modo == "NUEVO" ? indice : formulario.indice) +
									  "_1" +
									  "." +
									  formulario.foto.foto_1.name.split(".").pop()
									: null
								: formulario.foto.foto_1,
							foto_2: formulario.nueva_foto.nuevo_2
								? formulario.foto.foto_2 != null
									? año +
									  "_" +
									  self.evaluacion_id +
									  "_" +
									  (self.modo == "NUEVO" ? indice : formulario.indice) +
									  "_2" +
									  "." +
									  formulario.foto.foto_2.name.split(".").pop()
									: null
								: formulario.foto.foto_2,
							foto_3: formulario.nueva_foto.nuevo_3
								? formulario.foto.foto_3 != null
									? año +
									  "_" +
									  self.evaluacion_id +
									  "_" +
									  (self.modo == "NUEVO" ? indice : formulario.indice) +
									  "_3" +
									  "." +
									  formulario.foto.foto_3.name.split(".").pop()
									: null
								: formulario.foto.foto_3,
							foto_4: formulario.nueva_foto.nuevo_4
								? formulario.foto.foto_4 != null
									? año +
									  "_" +
									  self.evaluacion_id +
									  "_" +
									  (self.modo == "NUEVO" ? indice : formulario.indice) +
									  "_4" +
									  "." +
									  formulario.foto.foto_4.name.split(".").pop()
									: null
								: formulario.foto.foto_4,
						},
					};

					if (self.modo == "NUEVO") {
						self.lista_datos.push(object);
					} else {
						let elemento = self.lista_datos.filter(
							(item) => item.indice == formulario.indice
						)[0];

						elemento.foto = object.foto;
						elemento.indice = object.indice;
						elemento.cantidad = object.cantidad;
						elemento.descripcion = object.descripcion;
						elemento.marca = object.marca;
						elemento.modelo = object.modelo;
						elemento.serie = object.serie;
						elemento.color = object.color;
						elemento.estado = object.estado;
						elemento.fecha_compra = object.fecha_compra;
						elemento.numero_comprobante = object.numero_comprobante;
						elemento.precio_actual = object.precio_actual;
						elemento.precio_compra = object.precio_compra;
						elemento.comentario = object.comentario;
						elemento.disponible = object.disponible;
						elemento.fecha_registro = object.fecha_registro;
						elemento.usuario_registro = object.usuario_registro;
					}

					data.append(
						"valor",
						self.lista_datos.length == 0
							? null
							: JSON.stringify(self.lista_datos)
					);

					if (formulario.nueva_foto.nuevo_1) {
						data.append("foto_1", formulario.foto.foto_1);
						data.append(
							"nombre_1",
							formulario.foto.foto_1 != null
								? año +
										"_" +
										self.evaluacion_id +
										"_" +
										(self.modo == "NUEVO" ? indice : formulario.indice) +
										"_1" +
										"." +
										formulario.foto.foto_1.name.split(".").pop()
								: null
						);
					}
					if (formulario.nueva_foto.nuevo_2) {
						data.append("foto_2", formulario.foto.foto_2);
						data.append(
							"nombre_2",
							formulario.foto.foto_2 != null
								? año +
										"_" +
										self.evaluacion_id +
										"_" +
										(self.modo == "NUEVO" ? indice : formulario.indice) +
										"_2" +
										"." +
										formulario.foto.foto_2.name.split(".").pop()
								: null
						);
					}
					if (formulario.nueva_foto.nuevo_3) {
						data.append("foto_3", formulario.foto.foto_3);
						data.append(
							"nombre_3",
							formulario.foto.foto_3 != null
								? año +
										"_" +
										self.evaluacion_id +
										"_" +
										(self.modo == "NUEVO" ? indice : formulario.indice) +
										"_3" +
										"." +
										formulario.foto.foto_3.name.split(".").pop()
								: null
						);
					}
					if (formulario.nueva_foto.nuevo_4) {
						data.append("foto_4", formulario.foto.foto_4);
						data.append(
							"nombre_4",
							formulario.foto.foto_4 != null
								? año +
										"_" +
										self.evaluacion_id +
										"_" +
										(self.modo == "NUEVO" ? indice : formulario.indice) +
										"_4" +
										"." +
										formulario.foto.foto_4.name.split(".").pop()
								: null
						);
					}

					data.append("nueva_foto", JSON.stringify(formulario.nueva_foto));
					data.append("sub_grupo", self.sub_grupo);
					data.append("agencia_id", self.agencia_id);
					self.$inertia.post(route(this.ruta_guardar), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							self.submited = false;
							self.ResetearFormulario();
							self.RecrearTabla();
							self.modo = "BLOQUEADO";
							$("#inventario-tab").tab("show");
							return Swal.fire({
								icon: "success",
								title: "ÉXITO",
								allowOutsideClick: false,
							});
						},
					});
				} else {
					return false;
				}
			});
		},

		async GenerarDocumento() {
			let self = this;
			let rptEvaluacionBienes = this.$refs.rptEvaluacionBienes;

			let fecha_sistema = await this.$parent.fecha_hora_actual(this.agencia_id);
			fecha_sistema = fecha_sistema.substr(0, 10);
			let parts = fecha_sistema.split("-");

			let options = {
				weekday: "long",
				year: "numeric",
				month: "long",
				day: "numeric",
			};

			let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

			let fecha_actual = date.toLocaleDateString("es-ES", options);

			async function EnviarDatos() {
				let cliente =
					self.datos_personales.apellido_paterno +
					" " +
					self.datos_personales.apellido_materno +
					", " +
					self.datos_personales.nombres;
				let dni = self.datos_personales.dni;
				let direccion = self.datos_personales.direccion;
				let distrito = self.datos_personales.distrito;
				let provincia = self.datos_personales.provincia;
				let departamento = self.datos_personales.departamento;

				let bienes_disponibles = self.lista_datos.filter(
					(item) => item.disponible == 1
				);
				let orden = 0;
				bienes_disponibles.forEach((element) => {
					orden += 1;
					element.orden = orden;
					if (element.modelo == null) {
						element.modelo = "-";
					}

					if (element.serie == null) {
						element.serie = "-";
					}
				});

				fecha_actual = fecha_actual[0].toUpperCase() + fecha_actual.slice(1);

				let data = {
					cliente: cliente,
					dni: dni,
					direccion: direccion,
					distrito: distrito,
					provincia: provincia,
					departamento: departamento,
					bienes: bienes_disponibles,
					fecha: fecha_actual,
				};

				rptEvaluacionBienes.data = data;
				rptEvaluacionBienes.document_title =
					"rptEvaluacionFinanciera_" +
					self.cliente_id +
					"_" +
					self.evaluacion_id;
			}

			EnviarDatos().then(() => {
				rptEvaluacionBienes.GenerarWord();
			});
		},
	},
};
</script>

<style lang="css">
.mdlEvaluacionBienes {
	margin-top: 3%;
}

.subir {
	text-align: center;
	vertical-align: middle;
	width: 100% !important;
	height: 100% !important;

	background: #51515381;
	color: rgba(255, 255, 255, 0.575);
	border-radius: 5px;
	cursor: pointer;
}

.subir:hover {
	color: #fff;
	background: #515153a8;
}

.delete {
	position: absolute;
	bottom: 5px;
	right: 10px;
	z-index: 1000;
}

.foto-container {
	width: 400px;
	height: 200px;
}

.foto-icon {
	margin-top: 15%;
	margin-bottom: 15%;
}
@media only screen and (max-width: 900px) {
	.mdlEvaluacionBienes {
		margin-top: 20%;
	}
}
</style>
