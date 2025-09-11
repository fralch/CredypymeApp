<template>
	<layout ref="layout">
		<div class="slot_body slotblCuentass" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'SUBCATEGORÍAS DE TRANSACCIÓN'"></headerClose>

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
										@change="getListaSubCategorias()"
										v-model="frmSubcategoria.agencia_seleccionada"
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
									@click="abrirMdlNuevaCategoria"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i
									></span>
									<span class="text font-size-layout">Nuevo</span>
								</button>
							</div>
						</div>
						<div class="card-title">LISTA DE RESULTADOS</div>
						<table class="table table-hover" id="tblCategorias" width="100%">
							<thead>
								<tr>
									<th>EDITAR</th>
									<th>SUBCATEGORÍA</th>
									<th>CATEGORÍA</th>
									<th>DESCRIPCIÓN</th>
									<th>HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="subcategoria in subcategorias_selecionada"
									:key="subcategoria.index"
								>
									<td class="table-bordered" align="center">
										<div class="text-center">
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													id="btnRecibirRechazar"
													title="Editar"
													@click="editarSubcategoria(subcategoria)"
												>
													<span class="icon text-white">
														<i class="fas fa-edit"></i>
													</span>
												</button>
											</div>
										</div>
									</td>
									<td class="table-bordered center">
										{{ subcategoria.subcategoria }}
									</td>
									<td class="table-bordered center">
										{{ subcategoria.categoria }}
									</td>
									<td class="table-bordered center">
										{{
											subcategoria.descripcion == ""
												? "-"
												: subcategoria.descripcion
										}}
									</td>
									<td class="table-bordered center">
										{{ subcategoria.habilitado == 1 ? "Si" : "No" }}
									</td>
								</tr>
							</tbody>
						</table>
						<!-- <hr />
            <div class="text-right">
              <button class="btn btn-action btn-icon-split" @click="abrirMdlNuevaCategoria">
                <span class="icon text-white">
                 <i class="fas fa-plus"></i></span>
                <span class="text font-size-layout">Nuevo</span>
              </button>
            </div> -->
					</div>
				</div>
			</div>
			<!-- ---- Modal modo  -->
			<div class="modal" id="mdlNuevaCategoria">
				<div class="modal-content w-40">
					<div class="content" style="display: block">
						<div class="card">
							<!-- ----- -->
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>{{ tituloMdl }} SUBCATEGORÍA</strong>
								<button
									type="button"
									class="btn btn-green"
									style="border-radius: 50%"
									@click="cerrarMdlNuevaCategoria"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>
							<div class="card-title">DATOS DE SUBCATEGORÍA</div>
							<div class="card-body card-block">
								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="label-title"> SUBCATEGORÍA</label>
										<span
											v-if="
												submited && !$v.frmSubcategoria.subcategoria.required
											"
											class="span-error-message"
											>*</span
										>
										<input
											class="form-control mayus"
											type="text"
											v-model="frmSubcategoria.subcategoria"
										/>
									</div>

									<div class="form-group col-md-6">
										<label class="label-title" for="text-input"
											>CATEGORÍA</label
										>
										<span
											v-if="submited && !$v.frmSubcategoria.subcategoria.noZero"
											class="span-error-message"
											>*</span
										>
										<select
											class="form-control center"
											name="cmbAgencias"
											id="cmbAgencias"
											v-model="frmSubcategoria.categoria"
										>
											<option :value="0" selected disabled>Seleccionar</option>
											<option
												v-for="categoria in categorias"
												:key="categoria.id"
												:value="categoria.id"
											>
												{{ categoria.categoria }}
											</option>
										</select>
									</div>
									<div class="form-group col-md">
										<label class="label-title">DESCRIPCIÓN</label>
										<textarea
											type="text"
											rows="2"
											class="form-control mayus"
											v-model="frmSubcategoria.descripcion"
										></textarea>
									</div>
								</div>
								<!-- ---------------------- -->
								<div
									class="form-row ml-1 mt-1"
									v-if="frmSubcategoria.modo == 0"
								>
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
												v-model="frmSubcategoria.habilitado" /><span
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
										@click="guardarNuevaSubcategoria"
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
		subcategorias: Array,
		categorias: Array,
	},
	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			subcategorias_selecionada: this.subcategorias,
			frmSubcategoria: {
				agencia_seleccionada: 0,
				subcategoria: "",
				descripcion: "",
				categoria: 0,
				habilitado: 1,
				modo: 1,
			},
			guardarMdl: "AGREGANDO CATEGORIA",
			tituloMdl: "NUEVA",
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			this.frmSubcategoria.agencia_seleccionada = agencia_id;
		},
		subcategorias_selecionada() {
			$("#tblCategorias").DataTable().destroy();
			this.TablasCategorias();
		},
	},
	validations: {
		frmSubcategoria: {
			subcategoria: { required },
			categoria: { noZero },
		},
	},
	mounted() {
		this.TablasCategorias();
		this.ListarAgenciasPermitidas();
	},
	methods: {
		getListaSubCategorias() {
			let self = this;
			axios
				.post(route("mant.subcategoria.listar_subcategoria"), {
					id_agencia: this.frmSubcategoria.agencia_seleccionada,
				})
				.then(function (response) {
					self.subcategorias_selecionada = response.data;
				});
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_MANTENIMIENTO/TRANSACCION_SUBCATEGORIAS"
			);
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},

		TablasCategorias() {
			this.$nextTick(() => {
				var table = $("#tblCategorias").DataTable({
					destroy: true,
					lengthMenu: [
						[10, 50, 100, -1],
						[10, 50, 100, "Todas"],
					],
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
		abrirMdlNuevaCategoria() {
			let self = this;
			this.submited = false;
			self.guardarMdl = "Guardar Categoria";
			self.tituloMdl = "NUEVA";

			self.frmSubcategoria.modo = 1;
			self.frmSubcategoria.id = "";
			self.frmSubcategoria.subcategoria = "";
			self.frmSubcategoria.descripcion = "";
			self.frmSubcategoria.categoria = 0;
			self.frmSubcategoria.habilitado = 1;
			$("#mdlNuevaCategoria").css("display", "block");
		},
		cerrarMdlNuevaCategoria() {
			$("#mdlNuevaCategoria").css("display", "none");
		},
		guardarNuevaSubcategoria() {
			let self = this;
			this.submited = true;

			if (self.$v.frmSubcategoria.$invalid) {
				return false;
			}

			axios
				.post(route("mant.subcategoria.validar_existe"), self.frmSubcategoria)
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
									route("mant.subcategoria.guardar"),
									self.frmSubcategoria,
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
											$("#mdlNuevaCategoria").css("display", "none");
											self.getListaSubCategorias();
										},
									}
								);
							},
						});
						// ----
					}
				});
		},
		editarSubcategoria(subcategoria) {
			$("#mdlNuevaCategoria").css("display", "block");
			let self = this;
			self.guardarMdl = "Editar Categoria";
			self.tituloMdl = "EDITAR";
			// if (categoria.tipo=='I') {
			//   subcategoria.tipo=1;
			// } else if(categoria.tipo=='E'){
			//   subcategoria.tipo=2;
			// }

			self.frmSubcategoria.id = subcategoria.id;
			self.frmSubcategoria.modo = 0;
			self.frmSubcategoria.subcategoria = subcategoria.subcategoria;
			self.frmSubcategoria.descripcion = subcategoria.descripcion;
			self.frmSubcategoria.categoria = subcategoria.id_categoria;
			self.frmSubcategoria.habilitado = subcategoria.habilitado;
		},
	},
};
</script>

<style lang="css">
.slotblCuentass {
	width: 50% !important;
	margin-left: 25% !important;
}

@media only screen and (max-width: 900px) {
	.slotblCuentass {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>

