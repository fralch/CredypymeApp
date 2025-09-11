<template>
	<div>
		<div id="mdlEvaluacionDetalle" class="modal">
			<!-- Modal content -->
			<div
				class="modal-content mdlEvaluacionDetalle"
				:class="[grupo == 'FLUJO_CAJA' ? 'w-60' : 'w-50']"
			>
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
								@click="Cerrar('mdlEvaluacionDetalle')"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-body card-block">
							<div
								v-if="grupo == 'FLUJO_CAJA' && !no_editable"
								style="background: #d3d3d3"
							>
								<div class="card-title">
									{{
										sub_grupo == "VENTAS_DETALLADO"
											? "COSTO DE VENTAS INGRESADO"
											: "VENTAS INGRESADAS"
									}}
								</div>
								<table
									class="table"
									id="tblDatosVinculado"
									width="100% !important"
								>
									<thead>
										<tr>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 275px !important">DESCRIPCIÓN</th>
											<th style="min-width: 50px !important">CANT.</th>
											<th style="min-width: 50px !important">P_UNITARIO</th>
											<th style="min-width: 50px !important">SUBTOTAL</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_datos_vinculado"
											:key="index"
										>
											<td class="table-bordered" align="center">
												{{ index + 1 }}
											</td>
											<td class="table-bordered">
												{{ item.descripcion }}
											</td>
											<td class="table-bordered" align="center">
												{{ parseFloat(item.cantidad).toFixed(2) }}
											</td>
											<td class="table-bordered" align="right">
												{{ parseFloat(item.precio_unitario).toFixed(2) }}
											</td>
											<td class="table-bordered" align="right">
												{{
													(
														parseFloat(item.cantidad) *
														parseFloat(item.precio_unitario)
													).toFixed(2)
												}}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
							</div>
							<div
								class="row"
								v-if="!no_editable"
								style="border-bottom: 2px solid var(--colorMedio)"
							>
								<div class="form-group row col-md-12 pr-0">
									<div class="col-md-2 col-4 text-right">
										<label class="label-title m-0 mt-2">DESCRIPCIÓN</label>
										<span
											v-if="submited && !$v.frmElemento.descripcion.required"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-10 col-8">
										<textarea
											class="form-control mayus text-row"
											rows="2"
											v-model="frmElemento.descripcion"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>
								</div>
								<div class="form-group row col-md-12 pr-0">
									<div class="col-md-2 col-4 text-right">
										<label class="label-title m-0 mt-2">CANT.</label>

										<span
											v-if="
												submited &&
												(!$v.frmElemento.cantidad.required ||
													!$v.frmElemento.cantidad.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-3 col-5">
										<input
											class="form-control center"
											type="number"
											min="0"
											step="0.10"
											v-model.number="frmElemento.cantidad"
											style="
												font-size: 1.2rem;
												font-weight: bolder;
												color: var(--azulOscuroEmpresarial);
											"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="col-2" v-if="windowWidth < 1000"></div>
									<div class="col-md-2 col-4 text-right">
										<label class="label-title m-0 mt-2">P. UNITARIO</label>

										<span
											v-if="
												submited &&
												(!$v.frmElemento.precio_unitario.required ||
													!$v.frmElemento.precio_unitario.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-3 col-5">
										<input
											class="form-control center"
											type="number"
											min="0"
											step="0.10"
											v-model.number="frmElemento.precio_unitario"
											style="
												font-size: 1.2rem;
												font-weight: bolder;
												color: var(--azulOscuroEmpresarial);
											"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
											@change="Redondear"
										/>
									</div>
									<div class="col-md-2 col-2">
										<button
											class="btn btn-action btn-icon-split"
											@click="AgregarLista"
											title="Agregar a lista"
										>
											<span class="icon text-white">
												<i class="fas fa-arrow-down"></i>
											</span>
										</button>
									</div>
								</div>
							</div>

							<div>
								<table
									class="table"
									id="tblDatosDetalle"
									width="100% !important"
								>
									<thead>
										<tr>
											<th
												style="min-width: 75px !important"
												:hidden="no_editable"
											>
												ACCIONES
											</th>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 275px !important">
												{{
													grupo == "ACTIVO_CORRIENTE"
														? "DESCRIPCIÓN"
														: "PRODUCTO/MATERIA PRIMA"
												}}
											</th>
											<th style="min-width: 50px !important">CANT.</th>
											<th style="min-width: 50px !important">P_UNITARIO</th>
											<th style="min-width: 50px !important">SUBTOTAL</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in lista_datos" :key="index">
											<td
												class="table-bordered"
												align="center"
												:hidden="no_editable"
											>
												<div class="btn-group" role="group">
													<button
														class="btn btn-action btn-icon-split"
														title="Editar"
														@click="EditarLista(item, index)"
													>
														<span class="icon text-white">
															<i class="fas fa-edit"></i>
														</span>
													</button>
													<button
														class="btn btn-danger btn-icon-split"
														title="Quitar"
														@click="QuitarLista(index)"
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
											<td class="table-bordered">
												{{ item.descripcion }}
											</td>
											<td class="table-bordered" align="center">
												{{ parseFloat(item.cantidad).toFixed(2) }}
											</td>
											<td class="table-bordered" align="right">
												{{ parseFloat(item.precio_unitario).toFixed(2) }}
											</td>
											<td class="table-bordered" align="right">
												{{
													(
														parseFloat(item.cantidad) *
														parseFloat(item.precio_unitario)
													).toFixed(2)
												}}
											</td>
										</tr>
									</tbody>
								</table>

								<div class="text-right mt-2" v-if="!no_editable">
									<button
										class="btn btn-danger btn-icon-split"
										:disabled="lista_datos.length == 0"
										@click="QuitarTodo"
									>
										<span class="icon text-white">
											<i class="fas fa-trash"></i>
										</span>
										<span class="text">QUITAR TODO</span>
									</button>
								</div>

								<div
									class="text-right"
									v-if="no_editable && sub_grupo == 'VENTAS_DETALLADO'"
								>
									<label class="form-control-label" style="font-size: 1.2rem">
										S/ {{ parseFloat(total).toFixed(2) }}</label
									>
								</div>
							</div>

							<hr />
							<div class="text-left">
								<div class="row">
									<div
										class="btn-group col-md-7"
										role="group"
										:class="[
											sub_grupo == 'VENTAS_DETALLADO' ? 'col-12' : 'col-8',
										]"
									>
										<button
											class="btn btn-action btn-icon-split col-md-4"
											@click="MostrarFormulario"
											v-if="no_editable"
											:class="[
												sub_grupo == 'VENTAS_DETALLADO' ? 'col-5' : 'col-7',
											]"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
											<span class="text">MODIFICAR</span>
										</button>
										<button
											class="btn btn-cancel btn-icon-split col-md-6 col-7"
											@click="CopiarLista"
											v-if="no_editable && sub_grupo == 'VENTAS_DETALLADO'"
										>
											<span class="icon text-white">
												<i class="fas fa-share-square"></i>
											</span>
											<span class="text">COSTO DE VENTAS</span>
										</button>
										<button
											class="btn btn-action btn-icon-split col-md-4 col-6"
											@click="Guardar"
											v-if="!no_editable"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text">GUARDAR</span>
										</button>
										<button
											class="btn btn-cancel btn-icon-split col-md-4 col-6"
											@click="MostrarTabla"
											v-if="!no_editable"
										>
											<span class="icon text-white">
												<i class="fas fa-times"></i>
											</span>
											<span class="text">CANCELAR</span>
										</button>
									</div>

									<div
										class="col-md-3 col-4 offset-md-2"
										v-if="no_editable && sub_grupo != 'VENTAS_DETALLADO'"
									>
										<label
											class="form-control-label"
											:style="
												windowWidth >= 900
													? 'font-size: 1.2rem'
													: 'font-size: 1rem'
											"
										>
											S/ {{ parseFloat(total).toFixed(2) }}</label
										>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="mdlEditarDetalle" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-40 mdlEditarDetalle sub-model-middle">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>EDITAR</strong>

							<button
								type="button"
								class="btn btn-green"
								style="border-radius: 50%; float: right !important"
								@click="Cerrar('mdlEditarDetalle')"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-body card-block">
							<div class="form-row">
								<div class="form-group row col-md-12">
									<div class="col-md-3 col-4 text-right">
										<label class="label-title m-0 mt-2">DESCRIPCIÓN</label>
										<span
											v-if="submited && !$v.frmElemento.descripcion.required"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-9 col-8">
										<textarea
											class="form-control mayus text-row"
											rows="3"
											v-model="frmElemento.descripcion"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>
								</div>
								<div class="form-group row col-md-12">
									<div class="col-md-3 col-4 text-right">
										<label class="label-title m-0 mt-2">CANT.</label>

										<span
											v-if="
												submited &&
												(!$v.frmElemento.cantidad.required ||
													!$v.frmElemento.cantidad.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-3 col-5">
										<input
											class="form-control center"
											type="number"
											min="0"
											step="0.10"
											v-model.number="frmElemento.cantidad"
											style="
												font-size: 1.2rem;
												font-weight: bolder;
												color: var(--azulOscuroEmpresarial);
											"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="col-2" v-if="windowWidth < 1000"></div>
									<div class="col-md-3 col-4 text-right">
										<label class="label-title m-0 mt-2">P. UNITARIO</label>

										<span
											v-if="
												submited &&
												(!$v.frmElemento.precio_unitario.required ||
													!$v.frmElemento.precio_unitario.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-3 col-5">
										<input
											class="form-control center"
											type="number"
											min="0"
											step="0.10"
											v-model.number="frmElemento.precio_unitario"
											style="
												font-size: 1.2rem;
												font-weight: bolder;
												color: var(--azulOscuroEmpresarial);
											"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
											@change="Redondear"
										/>
									</div>
								</div>
							</div>
							<hr />
							<div class="text-right">
								<div class="btn-group" role="group">
									<button
										class="btn btn-action btn-icon-split"
										@click="GuardarEdicion"
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
	</div>
</template>


<script>
import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	props: { evaluacion_id: Number, cliente_id: Number, agencia_id: Number },
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			grupo: null,
			sub_grupo: null,
			title_modal: null,

			lista_datos: [],
			lista_datos_actuales: [],
			lista_datos_vinculado: [],
			frmElemento: {
				descripcion: null,
				cantidad: 1,
				precio_unitario: parseFloat(0.1).toFixed(2),
			},
			no_editable: true,
			ruta_guardar: null,
			total: 0.0,
		};
	},
	validations: {
		frmElemento: {
			descripcion: { required },
			cantidad: { noZero, required },
			precio_unitario: { noZero, required },
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
			$("#tblDatosDetalle").DataTable().destroy();
			this.TablaDatosDetalle();
		},
		lista_datos_vinculado() {
			$("#tblDatosVinculado").DataTable().destroy();
			this.TablaDatosVinculado();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		this.TablaDatosDetalle();
		this.TablaDatosVinculado();
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

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}
			this.frmElemento.precio_unitario = this.$parent.round(
				valor,
				numero_decimales
			);
		},
		TablaDatosDetalle() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblDatosDetalle").DataTable({
					scrollY: "200px",
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

				self.total = 0;
				self.lista_datos.forEach((element) => {
					self.total +=
						parseFloat(element.cantidad) * parseFloat(element.precio_unitario);
				});
			});
		},
		TablaDatosVinculado() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblDatosVinculado").DataTable({
					scrollY: "200px",
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
		Cerrar(ventana) {
			if (ventana == "mdlEditarDetalle") {
				this.frmElemento.descripcion = null;
				this.frmElemento.cantidad = 1;
				this.frmElemento.precio_unitario = parseFloat(0.1).toFixed(2);
			}
			$("#" + ventana).css("display", "none");
		},
		RecrearTabla() {
			$("#tblDatosDetalle").DataTable().destroy();
			this.TablaDatosDetalle();

			$("#tblDatosVinculado").DataTable().destroy();
			this.TablaDatosVinculado();
		},

		async MostrarTabla() {
			this.no_editable = true;
			await this.RecrearTabla();
			this.lista_datos = [];
			this.lista_datos_actuales.forEach((element) => {
				let object = {
					descripcion: element.descripcion,
					cantidad: element.cantidad,
					precio_unitario: element.precio_unitario,
				};

				this.lista_datos.push(object);
			});
		},
		async MostrarFormulario() {
			this.no_editable = false;
			await this.RecrearTabla();
		},
		AgregarLista() {
			this.submited = true;
			if (this.$v.frmElemento.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Complete todos los campos",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let descripcion = this.frmElemento.descripcion.toUpperCase();
				let cantidad = this.frmElemento.cantidad;
				let precio_unitario = this.frmElemento.precio_unitario;

				let object = {
					descripcion: descripcion,
					cantidad: cantidad,
					precio_unitario: precio_unitario,
				};

				this.lista_datos.push(object);

				this.submited = false;
				this.frmElemento.descripcion = null;
				this.frmElemento.cantidad = 1;
				this.frmElemento.precio_unitario = parseFloat(0.1).toFixed(2);
			}
		},
		EditarLista(item, index) {
			let object = {
				descripcion: item.descripcion,
				cantidad: item.cantidad,
				precio_unitario: item.precio_unitario,
				index: index,
			};
			this.frmElemento = object;
			$("#mdlEditarDetalle").css("display", "block");
		},

		QuitarLista(index) {
			Swal.fire({
				icon: "question",
				text: "¿Desea quitar este elemento?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.lista_datos.splice(index, 1);
				} else {
					return false;
				}
			});
		},
		CopiarLista() {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea copiar a COSTO DE VENTAS?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("evaluacion_id", self.evaluacion_id);
					data.append("cliente_id", self.cliente_id);

					data.append(
						"valor",
						self.lista_datos.length == 0
							? null
							: JSON.stringify(self.lista_datos)
					);
					data.append("sub_grupo", "COSTO_VENTAS");
					data.append("agencia_id", self.agencia_id);

					self.$inertia.post(route(this.ruta_guardar), data, {
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
									self.no_editable = true;
									await self.RecrearTabla();
									self.lista_datos_vinculado = self.lista_datos;
								},
							});
						},
					});
				} else {
					return false;
				}
			});
		},
		GuardarEdicion() {
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
			}).then((result) => {
				if (result.isConfirmed) {
					let nuevos_datos = this.frmElemento;
					this.lista_datos[nuevos_datos.index].descripcion =
						nuevos_datos.descripcion.toUpperCase();
					this.lista_datos[nuevos_datos.index].cantidad = nuevos_datos.cantidad;
					this.lista_datos[nuevos_datos.index].precio_unitario =
						nuevos_datos.precio_unitario;

					this.frmElemento.descripcion = null;
					this.frmElemento.cantidad = 1;
					this.frmElemento.precio_unitario = parseFloat(0.1).toFixed(2);

					$("#mdlEditarDetalle").css("display", "none");
				} else {
					return false;
				}
			});
		},
		QuitarTodo() {
			Swal.fire({
				icon: "warning",
				text: "¿Desea quitar todos los elementos?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.lista_datos = [];
				} else {
					return false;
				}
			});
		},
		Guardar() {
			let self = this;

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
				preConfirm: (result) => {
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
							self.no_editable = true;
							await self.RecrearTabla();
						},
					});
				},
			});
		},
	},
};
</script>

<style lang="css">
.sub-model-middle {
	margin-top: 15%;
}

/* Para corregir bug de datatable */
.dataTable {
	width: 100% !important;
}
.dataTables_scrollHeadInner {
	width: 100% !important;
}
.DTFC_ScrollWrapper {
	height: auto !important;
}
/* --------------------------------- */

#tblClientes_length {
	display: none;
}
@media only screen and (max-width: 900px) {
	.mdlEvaluacionDetalle {
		margin-top: 49% !important;
	}
	.mdlEditarDetalle {
		margin-top: 25% !important;
	}
}
</style>
