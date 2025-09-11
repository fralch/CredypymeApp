<template>
	<layout ref="layout">
		<div class="slot_body slot-categorias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CATEGORÍAS'"></headerClose>
					<div class="card-title">LISTA DE RESULTADOS</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-6 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									id="inpBuscarCategoria"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
							<div class="col-md-1 col-2 ml-1">
								<button
									class="btn btn-action btn-icon-split"
									@click="NuevaCategoria"
									title="NUEVA CATEGORIA"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVA</span>
								</button>
							</div>
						</div>

						<table class="table" id="tblCategorias" width="100%">
							<thead>
								<tr>
									<th style="min-width: 10px !important">EDITAR</th>
									<th style="min-width: 75px !important">NOMBRE</th>
									<th style="min-width: 120px !important">DESCRIPCIÓN</th>
									<th style="min-width: 10px !important">PESO</th>
									<th style="min-width: 75px !important">NIVEL</th>
									<th style="min-width: 10px !important">HAB.</th>
								</tr>
							</thead>
							<tbody>
								<tr
									class="table-bordered"
									v-for="(item, index) in categorias"
									:key="index"
								>
									<td align="center">
										<button
											class="btn btn-action"
											type="button"
											title="EDITAR CATEGORÍA"
											@click="EditarCategoria(item)"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td>
										{{ item.categoria }}
									</td>
									<td>
										{{ item.descripcion == "" ? "-" : item.descripcion }}
									</td>
									<td align="center">{{ roundTo(item.peso, 2) }}%</td>
									<td>
										{{ item.nivel }}
									</td>
									<td align="center">
										{{ item.habilitado == 1 ? "Si" : "No" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosCategoria" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-25 mdlDatosCategoria">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="title_modal"
								:nombre_modal="'mdlDatosCategoria'"
							>
							</headerCloseModal>

							<div class="card-title">DATOS DE LA CATEGORÍA</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-md-9">
											<label class="label-title mayus">CATEGORÍA</label>

											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosCategoria.categoria"
												:class="[
													submited
														? $v.frmDatosCategoria.categoria.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											/>
										</div>
										<div class="form-group col-md-3">
											<label class="label-title">PESO (%)</label>

											<input
												type="number"
												class="form-control center"
												min="0"
												step="0.1"
												placeholder="0"
												lang="en"
												v-model.number="frmDatosCategoria.peso"
												@change="redondear"
												:class="[
													submited
														? $v.frmDatosCategoria.peso.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											/>
										</div>
									</div>
									<div
										class="form-row"
										v-if="frmDatosCategoria.modo == 'EDITAR'"
									>
										<div class="form-group col-md-12">
											<div class="form-row">
												<label for="chbHabilitado" class="label-title"
													>Habilitado:</label
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
															v-model="frmDatosCategoria.habilitado" /><span
															class="cr"
															style="margin-right: 0 !important"
															><i class="cr-icon fa fa-check"></i></span
													></label>
												</div>
											</div>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label class="label-title">DESCRIPCIÓN</label>

											<textarea
												class="form-control mayus text-row"
												rows="2"
												maxlength="200"
												oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
												v-model="frmDatosCategoria.descripcion"
											></textarea>
										</div>
									</div>
									<div class="form-row">
										<div class="input-group col-md-12 mt-1">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>NIVEL</span
												>
											</div>
											<select
												class="form-control center"
												v-model="frmDatosCategoria.nivel_id"
												:class="[
													submited
														? $v.frmDatosCategoria.nivel_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											>
												<option :value="0" disabled selected>
													Seleccione...
												</option>
												<option
													v-for="(item, index) in niveles"
													:key="index"
													:value="item.id"
												>
													{{ item.nivel }}
												</option>
											</select>
										</div>
									</div>

									<div
										v-for="(escala, index) in this.lista_escalas"
										:key="index"
									>
										<div class="form-group offset-4">
											<div class="form-row col-md-12">
												<label class="label-title mayus"
													>{{ escala.valor }}:</label
												>

												<label class="label-title mayus">
													{{ escala.titulo }}</label
												>
											</div>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="GuardarCategoria"
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
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
import headerCloseModal from "@/Pages/Gth/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	data: () => ({
		categorias: [],
		niveles: [],
		submited: false,
		title_modal: null,

		frmDatosCategoria: {
			modo: null,
			id: null,
			categoria: null,
			descripcion: null,
			peso: null,
			nivel_id: 0,
			habilitado: 1,
		},
		lista_escalas: [],
		lista_escalas_hab: [],
	}),
	validations: {
		frmDatosCategoria: {
			categoria: { required },
			peso: { noZero: noZero },
			nivel_id: { noZero },
		},
	},

	watch: {
		nivel_seleccionado() {
			this.ListarEscalas();
		},
		categorias() {
			$("#tblCategorias").DataTable().destroy();
			this.TablaCategorias();
		},
	},
	computed: {
		nivel_seleccionado() {
			return this.frmDatosCategoria.nivel_id;
		},
	},
	mounted() {
		this.TablaCategorias();
		this.ListarRecursos();
	},
	methods: {
		roundTo(value, numero_decimales) {
			let valor = 0;
			let numero_decimales_value = numero_decimales;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales_value);
		},

		redondear(e) {
			let valor = 0;
			let numero_decimales = 2;
			valor = e.target.value;
			this.frmDatosCategoria.peso = this.roundTo(valor, numero_decimales);
		},

		async ListarRecursos() {
			let self = this;
			await axios
				.get(route("man.col.categorias.listar_recursos"))
				.then(function (response) {
					self.categorias = response.data.categorias;
					self.niveles = response.data.niveles;
				});
		},

		ListarEscalas() {
			this.lista_escalas = [];

			if (this.nivel_seleccionado == 0) {
				return false;
			} else {
				var test = [];
				this.niveles.map((element) => {
					test.push(element.id);
				});

				if (test.includes(this.nivel_seleccionado)) {
					let array_escala = this.niveles.filter(
						(item) => item.id == this.nivel_seleccionado
					);

					let json = JSON.parse(array_escala[0].descripcion);
					// console.log(json);
					json.map((element) => {
						this.lista_escalas.push(element);
					});
				} else {
					return false;
				}
			}
		},
		TablaCategorias() {
			self = this;
			this.$nextTick(() => {
				var table = $("#tblCategorias").DataTable({
					scrollX: true,
					scrollY: "300px",
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
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

				$("#inpBuscarCategoria").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		NuevaCategoria() {
			this.submited = false;
			this.title_modal = "NUEVA CATEGORÍA";
			this.frmDatosCategoria.id = null;
			this.frmDatosCategoria.modo = "NUEVO";
			this.frmDatosCategoria.categoria = null;
			this.frmDatosCategoria.descripcion = "";
			this.frmDatosCategoria.peso = 0;
			this.frmDatosCategoria.habilitado = 1;
			this.frmDatosCategoria.nivel_id = 0;
			this.lista_escalas = [];

			$("#mdlDatosCategoria").css("display", "block");
		},
		async EditarCategoria(item) {
			this.submited = false;
			this.title_modal = "EDITAR CATEGORÍA";
			this.frmDatosCategoria.id = item.id;
			this.frmDatosCategoria.modo = "EDITAR";
			this.frmDatosCategoria.categoria = item.categoria;
			this.frmDatosCategoria.descripcion = item.descripcion;
			this.frmDatosCategoria.peso = this.roundTo(item.peso, 2);
			this.frmDatosCategoria.habilitado = item.habilitado;
			this.frmDatosCategoria.nivel_id = item.nivel_id;

			await this.ListarEscalas();
			$("#mdlDatosCategoria").css("display", "block");
		},
		GuardarCategoria() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosCategoria.$invalid == true) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			} else {
				if (this.nivel_seleccionado == 0) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Debe seleccionar un nivel, verifique.",
					});
					return false;
				} else {
					let data = new FormData();
					data.append("modo", this.frmDatosCategoria.modo);
					data.append("id", this.frmDatosCategoria.id);
					data.append("categoria", this.frmDatosCategoria.categoria);
					axios
						.post(route("man.col.categorias.verificar"), data)
						.then(function (response) {
							let resultado = response.data;
							if (resultado == "EXISTE") {
								Swal.fire({
									icon: "error",
									title: "¡Ups!",
									text: "Esta Categoría ya se encuentra registrada, intente nuevamente.",
								});
								return false;
							} else if (resultado == "NO EXISTE") {
								Swal.fire({
									title: "GUARDAR CAMBIOS",
									text: "¿Desea continuar?",
									confirmButtonText:
										'<i class="fas fa-check" style="color:white;"></i>   Si',
									confirmButtonColor: "var(--colorAlto)",
									showCancelButton: true,
									cancelButtonText: '<i class="fas fa-times"></i>   No',
									cancelButtonColor: "var(--plomoOscuroEmpresarial)",
									allowOutsideClick: false,
								}).then((result) => {
									if (result.isConfirmed) {
										data.append("nivel_seleccionado", self.nivel_seleccionado);
										data.append(
											"descripcion",
											self.frmDatosCategoria.descripcion
										);
										data.append("peso", self.frmDatosCategoria.peso);
										data.append(
											"habilitado",
											self.frmDatosCategoria.habilitado
										);
										data.append("modo", self.frmDatosCategoria.modo);

										// self.$inertia.post(
										//   route("man.col.niveles.guardar"),
										//   data)
										//   return false

										Swal.fire({
											title: "REGISTRANDO",
											showConfirmButton: false,
											allowOutsideClick: false,
											willOpen: async () => {
												Swal.showLoading();

												return await axios
													.post(route("man.col.categorias.guardar"), data)
													.then((response) => {
														$("#mdlDatosCategoria").css("display", "none");
														self.ListarRecursos();
														return Swal.fire({
															icon: "success",
															title: "¡ÉXITO!",
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
									}
								});
							}
						});
				}
			}
		},
	},
};
</script>

<style>
.slot-categorias {
	width: 50% !important;
	margin-left: 25% !important;
}

@media (max-width: 900px) {
	.slot-categorias {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosCategoria {
		margin-top: 20%;
	}
}
</style>
