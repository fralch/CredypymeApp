<template>
	<layout ref="layout">
		<div class="slot_body slotblCuentass" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'COMPROBANTES DE TRANSACCIÓN'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-4 col-md-6 col-7">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscar"
										placeholder="Buscar..."
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>

							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										@change="getListaComprobantes()"
										v-model="frmComprobante.agencia_seleccionada"
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
									@click="abrirMdlNuevaComprobante"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i
									></span>
									<span class="text font-size-layout">Nuevo</span>
								</button>
							</div>
						</div>
						<div class="card-title">LISTA DE RESULTADOS</div>
						<table class="table table-hover" id="tblComprobante" width="100%">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>COMPROBANTE</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="comprobante in comprobantes_selecionados"
									:key="comprobante.index"
								>
									<td class="table-bordered" align="center">
										<div class="text-center">
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													id="btnRecibirRechazar"
													title="Editar"
													@click="editarComprobante(comprobante)"
												>
													<span class="icon text-white">
														<i class="fas fa-edit"></i>
													</span>
												</button>
											</div>
										</div>
									</td>
									<td class="table-bordered center">
										{{ comprobante.comprobante }}
									</td>
									<td class="table-bordered center">
										{{
											comprobante.descripcion == ""
												? "-"
												: comprobante.descripcion
										}}
									</td>
									<td class="table-bordered center">
										{{ comprobante.habilitado == 1 ? "Si" : "No" }}
									</td>
								</tr>
							</tbody>
						</table>
						<hr />
						<!-- <div class="text-right">
              <button class="btn btn-action btn-icon-split" @click="abrirMdlNuevaComprobante">
                <span class="icon text-white">
                 <i class="fas fa-plus"></i></span>
                <span class="text font-size-layout">Nuevo</span>
              </button>
            </div> -->
					</div>
				</div>
			</div>
			<!-- ---- Modal modo  -->
			<div class="modal" id="mdlNuevaComprobante">
				<div class="modal-content w-40">
					<div class="content" style="display: block">
						<div class="card">
							<!-- ----- -->
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>{{ tituloMdl }} COMPROBANTE</strong>
								<button
									type="button"
									class="btn btn-green"
									style="border-radius: 50%"
									@click="cerrarmdlNuevaComprobante"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>
							<div class="card-title">DATOS DE COMPROBANTE</div>
							<div class="card-body card-block">
								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="label-title"> COMPROBANTE</label>

										<input
											class="form-control mayus"
											type="text"
											v-model="frmComprobante.comprobante"
										/>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md">
										<label class="label-title">DESCRIPCIÓN</label>
										<textarea
											type="text"
											rows="2"
											class="form-control mayus"
											v-model="frmComprobante.descripcion"
										></textarea>
									</div>
								</div>
								<!-- ---------------------- -->
								<div class="form-row ml-1 mt-1" v-if="frmComprobante.modo == 0">
									<label
										for="chbHabilitado"
										class="form-control-label label-title"
										>HABILITADO</label
									>
									<div class="checkbox">
										<label
											class="align-middle"
											style="
												font-size: 1em;
												margin-bottom: 0 !important;
												height: 1em !important;
											"
											for="chbHabilitado"
											><input
												type="checkbox"
												id="chbHabilitado"
												v-model="frmComprobante.habilitado" /><span
												class="cr"
												style="margin-right: 0 !important"
												><i class="cr-icon fa fa-check"></i></span
										></label>
									</div>
								</div>
								<!-- --------------------------- -->
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="guardarNuevaComprobante"
									>
										<span class="icon text-white">
											<i class="fas fa-save"></i>
										</span>
										<span class="text font-size-layout">GUARDAR</span>
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
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		comprobantes: Array,
	},
	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			comprobantes_selecionados: this.comprobantes,
			frmComprobante: {
				agencia_seleccionada: 0,
				comprobante: "",
				descripcion: "",
				habilitado: 1,
				modo: 1,
			},
			guardarMdl: "AGREGANDO COMPROBANTE",
			tituloMdl: "NUEVA",
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			this.frmComprobante.agencia_seleccionada = agencia_id;
		},
		comprobantes_actualizados() {
			$("#tblComprobante").DataTable().destroy();
			this.TablasComprobante();
		},
		agencias_permitidas() {
			$("#tblComprobante").DataTable().destroy();
			this.TablasComprobante();
		},
	},
	validations: {
		frmComprobante: {
			comprobante: { required },
			tipo: { noZero },
		},
	},
	mounted() {
		this.TablasComprobante();
		this.ListarAgenciasPermitidas();
	},
	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},
		getListaComprobantes() {
			let self = this;
			axios
				.post(route("mant.comprobantes.listar_comprobantes"), {
					id_agencia: this.frmComprobante.agencia_seleccionada,
				})
				.then(function (response) {
					self.comprobantes_selecionados = response.data;
				});
		},
		ListarAgenciasPermitidas() {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			this.frmComprobante.agencia_seleccionada = agencia_id;

			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_MANTENIMIENTO/TRANSACCION_COMPROBANTES"
			);
		},

		TablasComprobante() {
			this.$nextTick(() => {
				var table = $("#tblComprobante").DataTable({
					destroy: true,
					order: [[1, "asc"]],
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
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 mb-2"i><"col-sm-12 col-md-7 mb-2"p><"col-sm-12 col-md-5 mb-2"l>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
					],
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		// -------
		abrirMdlNuevaComprobante() {
			let self = this;
			this.submited = false;
			self.guardarMdl = "Guardar Comprobante";
			self.tituloMdl = "NUEVA";

			self.frmComprobante.modo = 1;
			self.frmComprobante.id = "";
			self.frmComprobante.comprobante = "";
			self.frmComprobante.descripcion = "";
			self.frmComprobante.habilitado = 1;
			$("#mdlNuevaComprobante").css("display", "block");
		},
		cerrarmdlNuevaComprobante() {
			$("#mdlNuevaComprobante").css("display", "none");
		},
		guardarNuevaComprobante() {
			let self = this;
			this.submited = true;

			if (self.$v.frmComprobante.$invalid) {
				return false;
			}
			axios
				.post(route("mant.comprobantes.validar_existe"), self.frmComprobante)
				.then(function (response) {
					if (response.data == "EXISTE" && self.tituloMdl == "NUEVA") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "No se pueden registrar duplicados.",
						});
						return false;
					} else if (
						response.data == "NO_EXISTE" ||
						self.tituloMdl == "EDITAR"
					) {
						// ----
						Swal.fire({
							title: self.guardarMdl,
							text: "¿Desea continuar?",
							confirmButtonText:
								'<i class="fas fa-check" style="color:white;"></i>   Si',
							confirmButtonColor: "var(--colorAlto)",
							showCancelButton: true,
							cancelButtonText: '<i class="fas fa-times"></i>   No',
							cancelButtonColor: "var(--plomoOscuroEmpresarial)",
							allowOutsideClick: false,
							preConfirm: (result) => {
								self.$inertia.post(
									route("mant.comprobantes.guardar"),
									self.frmComprobante,
									{
										preserveScroll: true,
										onStart: (visit) => {
											let timerInterval;
											Swal.fire({
												title: "CARGANDO",
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
												allowOutsideClick: true,
											});

											$("#mdlNuevaComprobante").css("display", "none");
											self.getListaComprobantes();
										},
									}
								);
							},
						});
						// ----
					}
				});
		},
		editarComprobante(comprobante) {
			$("#mdlNuevaComprobante").css("display", "block");
			let self = this;
			self.guardarMdl = "Editar Categoria";
			self.tituloMdl = "EDITAR";

			self.frmComprobante.id = comprobante.id;
			self.frmComprobante.modo = 0;
			self.frmComprobante.comprobante = comprobante.comprobante;
			self.frmComprobante.descripcion = comprobante.descripcion;
			self.frmComprobante.habilitado = comprobante.habilitado;
		},
	},
};
</script>

<style lang="css">
.slotblCuentass {
	width: 50% !important;
	margin-left: 25% !important;
}
#mdlNuevaComprobante .modal-content {
	width: 30% !important;
	margin-left: 35% !important;
}

@media only screen and (max-width: 900px) {
	.slotblCuentass {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>

