<template>
	<div id="mdlDatosActivo" class="modal">
		<div class="modal-content w-40 mdlDatosActivo">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlDatosActivo'"
					>
					</headerCloseModal>
					<div class="card-title">INFORMACIÓN DEl ACTIVO</div>
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item" role="presentation">
								<a
									class="nav-link active tab-title"
									id="datosActivo1-tab"
									data-toggle="tab"
									href="#datosActivo1"
									role="tab"
									aria-controls="datosActivo1"
									aria-="true"
									>Datos básicos</a
								>
							</li>
							<li class="nav-item" role="presentation">
								<a
									class="nav-link tab-title"
									id="datosActivo2-tab"
									data-toggle="tab"
									href="#datosActivo2"
									role="tab"
									aria-controls="datosActivo2"
									aria-="false"
									><i class="fas fa-plus"></i
								></a>
							</li>
						</ul>
						<form>
							<div class="tab-content" id="myTabContent">
								<div
									class="tab-pane fade show active"
									id="datosActivo1"
									role="tabpanel"
									aria-labelledby="datosActivo1-tab"
								>
									<div class="form-row">
										<div class="form-group col-md-6 offset-md-3 col-6 offset-3">
											<label class="label-title">AGENCIA</label>
											<input
												type="text"
												class="form-control center"
												:value="frmDatosActivo.agencia"
												disabled
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">RESPONSABLE</label>
											<input
												type="text"
												class="form-control"
												:value="frmDatosActivo.responsable"
												disabled
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">UBICACIÓN</label>
											<input
												type="text"
												class="form-control"
												:value="frmDatosActivo.ubicacion"
												disabled
											/>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">NOMBRE</label>
											<span
												v-if="submited && !$v.frmDatosActivo.nombre_id.noZero"
												class="span-error-message"
											>
												*
											</span>

											<select
												class="form-control"
												v-model="frmDatosActivo.nombre_id"
												@change="GenerarCodigo"
												:disabled="modo == 'VER_EXISTENTE'"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in nombres"
													:key="index"
													:value="item.id"
												>
													{{ item.nombre }}
												</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">FECHA COMPRA</label>
											<span
												v-if="
													submited && !$v.frmDatosActivo.fecha_compra.required
												"
												class="span-error-message"
											>
												*
											</span>
											<input
												class="form-control center"
												type="date"
												onkeydown="return false"
												v-model="frmDatosActivo.fecha_compra"
												:disabled="
													modo == 'VER_EXISTENTE' || modo == 'EDITAR_EXISTENTE'
												"
											/>
										</div>
										<div class="form-group col-md-4 col-5">
											<label class="label-title">TIPO</label>
											<span
												v-if="submited && !$v.frmDatosActivo.tipo_id.noZero"
												class="span-error-message"
											>
												*
											</span>
											<select
												class="form-control"
												v-model="frmDatosActivo.tipo_id"
												@change="GenerarCodigo"
												:disabled="
													modo == 'VER_EXISTENTE' || modo == 'EDITAR_EXISTENTE'
												"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in tipos"
													:key="index"
													:value="item.id"
												>
													{{ item.tipo }}
												</option>
											</select>
										</div>

										<div class="form-group col-md-12 col-7">
											<label class="label-title">DESCRIPCIÓN</label>
											<span
												v-if="
													submited && !$v.frmDatosActivo.descripcion.required
												"
												class="span-error-message"
											>
												*
											</span>
											<textarea
												class="form-control mayus"
												rows="2"
												v-model="frmDatosActivo.descripcion"
												:disabled="modo == 'VER_EXISTENTE'"
											></textarea>
										</div>

										<div class="form-group col-md-12">
											<table
												class="table table-hover"
												id="tblCodigos"
												width="100%"
											>
												<thead>
													<tr>
														<th>CÓDIGOS</th>
													</tr>
												</thead>
												<tbody>
													<tr
														v-for="(item, index) in frmDatosActivo.codigo"
														:key="index"
													>
														<td class="table-bordered" align="center">
															{{ item.codigo }}
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>

								<div
									class="tab-pane fade"
									id="datosActivo2"
									role="tabpanel"
									aria-labelledby="datosActivo2-tab"
								>
									<div class="form-row">
										<div class="form-group col-md-4 col-6">
											<label class="label-title">MARCA</label>
											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosActivo.marca"
												:disabled="modo == 'VER_EXISTENTE'"
											/>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">MODELO</label>
											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosActivo.modelo"
												:disabled="modo == 'VER_EXISTENTE'"
											/>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">PLACA</label>
											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosActivo.placa"
												:disabled="modo == 'VER_EXISTENTE'"
											/>
										</div>

										<div class="form-group col-md-8 col-6">
											<label class="label-title">CARACTERÍSTICAS</label>
											<textarea
												class="form-control mayus"
												rows="1"
												v-model="frmDatosActivo.caracteristicas"
												:disabled="modo == 'VER_EXISTENTE'"
											></textarea>
										</div>

										<div class="form-group col-md-4 col-6">
											<label class="label-title">CONDICIÓN</label>
											<span
												v-if="
													submited && !$v.frmDatosActivo.condicion_id.noZero
												"
												class="span-error-message"
											>
												*
											</span>
											<select
												class="form-control center"
												v-model="frmDatosActivo.condicion_id"
												:disabled="modo == 'VER_EXISTENTE'"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in condiciones"
													:key="index"
													:value="item.id"
												>
													{{ item.condicion }}
												</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title mayus">COLOR</label>
											<span
												v-if="submited && !$v.frmDatosActivo.color.required"
												class="span-error-message"
											>
												*
											</span>
											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosActivo.color"
												:disabled="modo == 'VER_EXISTENTE'"
											/>
										</div>
										<div class="form-group col-md-4 col-4">
											<label class="label-title">CANTIDAD</label>
											<span
												v-if="
													submited &&
													(!$v.frmDatosActivo.cantidad.required ||
														!$v.frmDatosActivo.cantidad.noZero)
												"
												class="span-error-message"
											>
												*
											</span>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text" style="font-size: 11px">
														UNID.
													</div>
												</div>
												<input
													type="number"
													class="form-control center"
													name="cantidad"
													min="1"
													v-model.number="frmDatosActivo.cantidad"
													@change="GenerarCodigo"
													:disabled="modo != 'AGREGAR_NUEVO'"
												/>
											</div>
										</div>

										<div class="form-group col-md-4 col-4">
											<label class="label-title">VALOR COMPRA</label>
											<span
												v-if="
													submited &&
													modo != 'EDITAR_EXISTENTE' &&
													(!$v.frmDatosActivo.valor_compra.required ||
														!$v.frmDatosActivo.valor_compra.noZero)
												"
												class="span-error-message"
											>
												*
											</span>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text" style="font-size: 11px">
														S/
													</div>
												</div>
												<input
													type="number"
													class="form-control center"
													name="valor_compra"
													min="1"
													step="0.01"
													lang="en"
													:disabled="
														modo == 'VER_EXISTENTE' ||
														modo == 'EDITAR_EXISTENTE'
													"
													v-model.number="frmDatosActivo.valor_compra"
													@change="Redondear"
												/>
											</div>
										</div>
										<div
											class="form-group col-md-4 col-4"
											v-if="modo == 'AGREGAR_NUEVO' || modo == 'EDITAR_NUEVO'"
										>
											<label class="label-title">IGV</label>
											<span
												v-if="
													submited &&
													(!$v.frmDatosActivo.igv.required ||
														!$v.frmDatosActivo.igv.noZero)
												"
												class="span-error-message"
											>
												*
											</span>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text" style="font-size: 11px">
														%
													</div>
												</div>
												<input
													type="number"
													class="form-control center"
													name="igv"
													min="1"
													step="0.01"
													lang="en"
													:disabled="modo == 'VER_EXISTENTE'"
													v-model.number="frmDatosActivo.igv"
													@change="Redondear"
												/>
											</div>
										</div>
										<div
											class="form-group col-md-4 col-4"
											v-if="
												modo == 'VER_EXISTENTE' || modo == 'EDITAR_EXISTENTE'
											"
										>
											<label class="label-title">VALOR ACTUAL</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text" style="font-size: 11px">
														S/
													</div>
												</div>
												<input
													type="text"
													class="form-control center"
													:value="frmDatosActivo.valor_actual"
													disabled
												/>
											</div>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">VIDA ÚTIL</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text" style="font-size: 11px">
														AÑOS
													</div>
												</div>
												<input
													type="text"
													class="form-control center"
													:value="this.roundTo(frmDatosActivo.vida_util, 2)"
													disabled
												/>
											</div>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">DEPRECIACIÓN</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text" style="font-size: 11px">
														%
													</div>
												</div>
												<input
													type="text"
													class="form-control center"
													:value="frmDatosActivo.depreciacion"
													disabled
												/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>

						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="Guardar()"
								v-if="modo != 'VER_EXISTENTE'"
							>
								<span class="icon text-white">
									<i class="fas fa-save"></i>
								</span>
								<span class="text">{{
									modo == "EDITAR_EXISTENTE" || modo == "EDITAR_NUEVO"
										? "GUARDAR"
										: "REGISTRAR"
								}}</span>
							</button>
							<button
								class="btn btn-cancel btn-icon-split"
								@click="Editar()"
								v-if="
									modo == 'VER_EXISTENTE' &&
									$page.props.user_permissions.permisos.includes(
										'LOGISTICA_ACTIVOS/INVENTARIO_EDITAR'
									)
								"
							>
								<span class="icon text-white">
									<i class="fas fa-edit"></i>
								</span>
								<span class="text">EDITAR</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: { headerCloseModal },
	props: {
		tipo_modulo: String,
		agencias: Array,
		nombres: Array,
		tipos: Array,
		responsables: Array,
		ubicaciones: Array,
		condiciones: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			modo: null,
			no_editable: true,

			frmDatosActivo: {
				modo: null,
				item: null,
				id: null,
				agencia: null,
				responsable: null,
				ubicacion: null,
				nombre_id: null,
				fecha_compra: null,
				tipo_id: null,
				descripcion: null,
				codigo: [],
				marca: null,
				modelo: null,
				placa: null,
				caracteristicas: null,
				condicion_id: null,
				cantidad: 0,
				color: null,
				valor_compra: 0,
				igv: 0,
				vida_util: null,
				depreciacion: null,
			},
		};
	},
	validations() {
		if (this.modo == "EDITAR_EXISTENTE") {
			return {
				frmDatosActivo: {
					nombre_id: { noZero },
					fecha_compra: { required },
					tipo_id: { noZero },
					descripcion: { required },
					condicion_id: { noZero },
					cantidad: { required, noZero },
					color: { required },
				},
			};
		} else {
			return {
				frmDatosActivo: {
					nombre_id: { noZero },
					fecha_compra: { required },
					tipo_id: { noZero },
					descripcion: { required },
					condicion_id: { noZero },
					cantidad: { required, noZero },
					color: { required },
					valor_compra: { required, noZero },
					igv: { required, noZero },
				},
			};
		}
	},

	mounted() {
		this.TablaCodigos();
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

		Redondear(e) {
			let valor = 0;

			if (e.target.value) {
				valor = e.target.value;
			}

			if (e.target.name == "cantidad") {
				if (valor == 0) {
					valor = 1;
				}
				this.frmDatosActivo.cantidad = this.roundTo(valor, 0);
			} else if (e.target.name == "valor_compra") {
				this.frmDatosActivo.valor_compra = this.roundTo(valor, 2);
			} else if (e.target.name == "igv") {
				this.frmDatosActivo.igv = this.roundTo(valor, 2);
			}
		},

		ActualizarTabla() {
			$("#tblCodigos").DataTable().destroy();
			this.TablaCodigos();
		},
		TablaCodigos() {
			this.$nextTick(() => {
				let table = $("#tblCodigos").DataTable({
					scrollY: "170px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,

					fixedHeader: true,
					info: false,
					order: [],
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

		GenerarCodigo(e) {
			let self = this;
			if (e) {
				this.Redondear(e);
			}

			let mdlCanastaCompra = this.$parent.$parent.$refs.mdlCanastaCompra;
			axios
				.post(route("log.act.inventario.obtener_ultimo"))
				.then(async function (response) {
					let tipo = "";
					if (self.frmDatosActivo.tipo_id != 0) {
						tipo = self.tipos.filter(
							(item) => item.id == self.frmDatosActivo.tipo_id
						)[0].abreviacion;
					}

					let responsable = self.responsables.filter(
						(item) => item.id == self.frmDatosActivo.responsable_id
					)[0].abreviacion;

					let ubicacion = self.ubicaciones.filter(
						(item) => item.id == self.frmDatosActivo.ubicacion_id
					)[0].abreviacion;

					let nombre_filtrado = [];
					let nombre = "";
					if (self.frmDatosActivo.nombre_id != 0) {
						nombre_filtrado = self.nombres.filter(
							(item) => item.id == self.frmDatosActivo.nombre_id
						);
						nombre = nombre_filtrado[0].nombre;
						let vida_util = null;
						vida_util = nombre_filtrado[0].vida_util;
						self.frmDatosActivo.vida_util = self.roundTo(vida_util, 2);
						self.frmDatosActivo.depreciacion = self.roundTo(1 / vida_util, 2);
					}

					let ultimo = response.data + 1;
					let cantidad_en_canasta =
						mdlCanastaCompra.frmCanastaCompras.canasta.length;
					ultimo += cantidad_en_canasta;

					if (self.modo == "AGREGAR_NUEVO") {
						self.frmDatosActivo.codigo = [];

						for (let index = 0; index < self.frmDatosActivo.cantidad; index++) {
							let codigo =
								tipo +
								"-" +
								responsable +
								"-" +
								ubicacion +
								"-" +
								ultimo +
								"-" +
								nombre;

							let objeto = {
								orden: ultimo,
								codigo: codigo,
							};

							self.frmDatosActivo.codigo.push(objeto);
							ultimo += 1;
						}
					} else if (
						self.modo == "EDITAR_NUEVO" ||
						self.modo == "EDITAR_EXISTENTE"
					) {
						let codigo =
							tipo +
							"-" +
							responsable +
							"-" +
							ubicacion +
							"-" +
							self.frmDatosActivo.codigo[0].orden +
							"-" +
							nombre;

						self.frmDatosActivo.codigo[0].codigo = codigo;
					}
					await self.ActualizarTabla();
				});
		},
		Editar() {
			this.modo = "EDITAR_EXISTENTE";
		},
		Guardar() {
			this.submited = true;
			let self = this;

			if (this.$v.frmDatosActivo.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});
				return false;
			} else {
				Swal.fire({
					icon: "question",
					text: "¿DESEA CONTINUAR?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
				}).then(async (result) => {
					if (result.isConfirmed) {
						let mdlCanastaCompra = this.$parent.$parent.$refs.mdlCanastaCompra;

						if (self.modo == "AGREGAR_NUEVO") {
							self.frmDatosActivo.codigo.forEach((element, index) => {
								let nombre = self.nombres.filter(
									(item) => item.id == self.frmDatosActivo.nombre_id
								)[0].nombre;

								let objeto = {
									orden: self.frmDatosActivo.orden,
									agencia_id: self.frmDatosActivo.agencia_id,
									nombre_id: self.frmDatosActivo.nombre_id,
									nombre: nombre,
									codigo: element,
									tipo_id: self.frmDatosActivo.tipo_id,
									responsable_id: self.frmDatosActivo.responsable_id,
									ubicacion_id: self.frmDatosActivo.ubicacion_id,
									descripcion: self.frmDatosActivo.descripcion.toUpperCase(),
									cantidad: 1,
									marca: self.frmDatosActivo.marca,
									modelo: self.frmDatosActivo.modelo,
									placa: self.frmDatosActivo.placa,
									caracteristicas: self.frmDatosActivo.caracteristicas,
									color: self.frmDatosActivo.color,
									condicion_id: self.frmDatosActivo.condicion_id,
									fecha_compra: self.frmDatosActivo.fecha_compra,
									valor_compra: self.frmDatosActivo.valor_compra,
									igv: self.frmDatosActivo.igv,
									vida_util: self.frmDatosActivo.vida_util,
									depreciacion: self.frmDatosActivo.depreciacion,
								};
								mdlCanastaCompra.frmCanastaCompras.canasta.push(objeto);
							});

							$("#mdlDatosActivo").css("display", "none");
							self.submited = false;
							// await mdlCanastaCompra.ActualizarTabla();
						} else if (self.modo == "EDITAR_NUEVO") {
							let index = self.frmDatosActivo.index;

							let nombre = self.nombres.filter(
								(item) => item.id == self.frmDatosActivo.nombre_id
							)[0].nombre;

							let item = mdlCanastaCompra.frmCanastaCompras.canasta[index];

							item.agencia_id = self.frmDatosActivo.agencia_id;
							item.nombre_id = self.frmDatosActivo.nombre_id;
							item.nombre = nombre;
							item.codigo = self.frmDatosActivo.codigo[0];
							item.tipo_id = self.frmDatosActivo.tipo_id;
							item.responsable_id = self.frmDatosActivo.responsable_id;
							item.ubicacion_id = self.frmDatosActivo.ubicacion_id;
							item.descripcion = self.frmDatosActivo.descripcion.toUpperCase();
							item.cantidad = 1;
							item.marca = self.frmDatosActivo.marca;
							item.modelo = self.frmDatosActivo.modelo;
							item.placa = self.frmDatosActivo.placa;
							item.caracteristicas = self.frmDatosActivo.caracteristicas;
							item.color = self.frmDatosActivo.color;
							item.condicion_id = self.frmDatosActivo.condicion_id;
							item.fecha_compra = self.frmDatosActivo.fecha_compra;
							item.valor_compra = self.frmDatosActivo.valor_compra;
							item.igv = self.frmDatosActivo.igv;
							item.vida_util = self.frmDatosActivo.vida_util;
							item.depreciacion = self.frmDatosActivo.depreciacion;

							await mdlCanastaCompra.ActualizarTabla();

							$("#mdlDatosActivo").css("display", "none");
							self.submited = false;
						} else if (self.modo == "EDITAR_EXISTENTE") {
							let data = new FormData();
							data.append("tipo_modulo", self.tipo_modulo);
							data.append("activo_id", self.frmDatosActivo.id);
							data.append("nombre_id", self.frmDatosActivo.nombre_id);
							data.append("descripcion", self.frmDatosActivo.descripcion);
							data.append("codigo", JSON.stringify(self.frmDatosActivo.codigo));
							data.append("marca", self.frmDatosActivo.marca);
							data.append("modelo", self.frmDatosActivo.modelo);
							data.append("placa", self.frmDatosActivo.placa);
							data.append(
								"caracteristicas",
								self.frmDatosActivo.caracteristicas
							);
							data.append("condicion_id", self.frmDatosActivo.condicion_id);
							data.append("color", self.frmDatosActivo.color);

							self.$inertia.post(route("log.act.inventario.editar"), data, {
								preserveScroll: true,
								onStart: (visit) => {
									let timerInterval;
									Swal.fire({
										title: "TRABAJANDO",
										html: "Espere porfavor...",
										timer: 5000,
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
										preConfirm: (result) => {
											self.$parent.$parent.ListarActivos();
											$("#mdlDatosActivo").css("display", "none");
										},
									});
								},
							});
						}
					} else {
						return false;
					}
				});
			}
		},
	},
};
</script>

<style lang="css">
.mdlDatosActivo {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlDatosActivo {
		margin-top: 20%;
	}
}
</style>


