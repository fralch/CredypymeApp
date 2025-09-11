<template>
	<div>
		<div id="mdlEvaluacionLista" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-40 mdlEvaluacionLista">
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
								@click="Cerrar('mdlEvaluacionLista')"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-body card-block">
							<div
								class="form-row"
								v-if="!no_editable"
								style="border-bottom: 2px solid var(--colorMedio)"
							>
								<div class="form-group row col-md-12">
									<div class="col-md-3 col-4 text-right">
										<label class="label-title m-0 mt-2">DESCRIPCIÓN </label>
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
											rows="2"
											v-model="frmElemento.descripcion"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>
								</div>
								<div class="form-group row col-md-12">
									<div class="col-md-3 col-3 text-right">
										<p
											class="m-0 mt-2"
											style="font-size: 1.5rem; font-weight: bolder"
										>
											S/
										</p>
										<span
											v-if="
												submited &&
												(!$v.frmElemento.monto.required ||
													!$v.frmElemento.monto.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-6 col-6">
										<input
											class="form-control center"
											type="number"
											min="0"
											step="0.10"
											lang="en"
											v-model.number="frmElemento.monto"
											style="
												height: 3rem !important;
												font-size: 1.3rem;
												font-weight: bolder;
												color: var(--azulOscuroEmpresarial);
											"
											autocomplete="off"
											@change="Redondear"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="col-md-3 col-3">
										<button
											class="btn btn-action btn-icon-split mt-2"
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
								<table class="table" id="tblDatos" width="100% !important">
									<thead>
										<tr>
											<th
												style="width: 20px !important"
												:hidden="no_editable"
											></th>
											<th>N°</th>
											<th>DESCRIPCIÓN</th>
											<th>MONTO_S/</th>
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
											<td class="table-bordered" align="right">
												{{ parseFloat(item.monto).toFixed(2) }}
											</td>
										</tr>
									</tbody>
								</table>
								<div class="text-right mt-1" v-if="!no_editable">
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
							</div>

							<hr />
							<div class="text-left">
								<div class="row">
									<div class="btn-group col-md-6 col-8" role="group">
										<button
											class="btn btn-action btn-icon-split col-md-6 col-7"
											@click="MostrarFormulario"
											v-if="no_editable"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
											<span class="text">MODIFICAR</span>
										</button>
										<button
											class="btn btn-action btn-icon-split"
											@click="Guardar"
											v-if="!no_editable"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text">GUARDAR</span>
										</button>
										<button
											class="btn btn-cancel btn-icon-split"
											@click="MostrarTabla"
											v-if="!no_editable"
										>
											<span class="icon text-white">
												<i class="fas fa-times"></i>
											</span>
											<span class="text">CANCELAR</span>
										</button>
									</div>
									<div class="col-md-3 col-4 offset-md-3" v-if="no_editable">
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
		<div id="mdlEditarLista" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-35 mdlEditarLista">
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
								@click="Cerrar('mdlEditarLista')"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-body card-block">
							<div class="form-row">
								<div class="form-group row col-md-12">
									<div class="col-md-3 col-3 text-right">
										<label class="label-title m-0 mt-2">DESCRIPCIÓN </label>
										<span
											v-if="submited && !$v.frmElemento.descripcion.required"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-9 col-9">
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
								<div class="form-group row col-md-12">
									<div class="col-md-3 col-3 text-right">
										<p
											class="m-0 mt-2"
											style="font-size: 1.5rem; font-weight: bolder"
										>
											S/
										</p>
										<span
											v-if="
												submited &&
												(!$v.frmElemento.monto.required ||
													!$v.frmElemento.monto.noZero)
											"
											class="span-error-message"
										>
											*
										</span>
									</div>
									<div class="col-md-6 col-6">
										<input
											class="form-control center"
											type="number"
											min="0"
											step="0.10"
											lang="en"
											v-model.number="frmElemento.monto"
											style="
												height: 3rem !important;
												font-size: 1.3rem;
												font-weight: bolder;
												color: var(--azulOscuroEmpresarial);
											"
											autocomplete="off"
											@change="Redondear"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="col-md-3 col-3">
										<button
											class="btn btn-action btn-icon-split mt-2"
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
			lista_datos_nuevos: [],
			frmElemento: {
				descripcion: null,
				monto: parseFloat(100).toFixed(2),
			},
			no_editable: true,
			ruta_guardar: null,
			total: 0.0,
		};
	},
	validations: {
		frmElemento: {
			descripcion: { required },
			monto: { noZero, required },
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
			} else if (value == "CONVENIO") {
				this.ruta_guardar = "cre.evaluacion.convenio.guardar";
			}
		},
		lista_datos() {
			$("#tblDatos").DataTable().destroy();
			this.TablaListaDatos();
		},
	},
	mounted() {
		this.TablaListaDatos();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
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
			this.frmElemento.monto = this.$parent.round(valor, numero_decimales);
		},
		TablaListaDatos() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblDatos").DataTable({
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

				self.total = 0;
				self.lista_datos.forEach((element) => {
					self.total += parseFloat(element.monto);
				});
			});
		},
		Cerrar(ventana) {
			if (ventana == "mdlEditarLista") {
				this.frmElemento.descripcion = null;
				this.frmElemento.monto = parseFloat(100).toFixed(2);
			}
			$("#" + ventana).css("display", "none");
		},
		RecrearTabla() {
			$("#tblDatos").DataTable().destroy();
			this.TablaListaDatos();
		},

		async MostrarTabla() {
			this.no_editable = true;
			await this.RecrearTabla();
			this.lista_datos = [];
			this.lista_datos_actuales.forEach((element) => {
				let object = {
					descripcion: element.descripcion,
					monto: element.monto,
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
				let descripcion = this.frmElemento.descripcion;
				let monto = this.frmElemento.monto;

				let object = { descripcion: descripcion.toUpperCase(), monto: monto };

				this.lista_datos.push(object);

				this.submited = false;
				this.frmElemento.descripcion = null;
				this.frmElemento.monto = parseFloat(100).toFixed(2);
			}
		},
		EditarLista(item, index) {
			let object = {
				descripcion: item.descripcion,
				monto: item.monto,
				index: index,
			};
			this.frmElemento = object;
			$("#mdlEditarLista").css("display", "block");
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
					this.lista_datos[nuevos_datos.index].monto = nuevos_datos.monto;

					this.frmElemento.descripcion = null;
					this.frmElemento.monto = parseFloat(100).toFixed(2);

					$("#mdlEditarLista").css("display", "none");
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
.mdlEditarLista {
	margin-top: 7.5% !important;
}
.mdlEvaluacionLista {
	margin-top: 5% !important;
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
	.mdlEvaluacionLista {
		margin-top: 49% !important;
	}
	.mdlEditarLista {
		margin-top: 58% !important;
	}
}
</style>
