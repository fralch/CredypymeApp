<template>
	<layout ref="layout">
		<div class="slot_body slot-preguntas" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LISTA DE PREGUNTAS'"></headerClose>
					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-4">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">CATEGORÍAS</span>
								</div>
								<select
									class="form-control"
									v-model="filtros_tabla['nombre_categoria'].value"
									:disabled="preguntas.length == 0"
								>
									<option :value="null">TODAS</option>
									<option v-for="item in categorias" :key="item.id">
										{{ item.categoria }}
									</option>
								</select>
							</div>
							<div class="input-group col-md-6 col-5">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									v-model="filtros_tabla['global'].value"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
							<div class="col-md-1 col-2">
								<button
									class="btn btn-action btn-icon-split"
									@click="Nuevo"
									title="NUEVA PREGUNTA"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVA</span>
									<!-- <span class="text">Nuevo</span> -->
								</button>
							</div>
						</div>
					</div>

					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<DataTable
							:value="preguntas"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="400px"
							:filters="filtros_tabla"
							:globalFilterFields="['nombre_categoria', 'criterio', 'pregunta']"
							showGridlines
						>
							<Column
								field="editar"
								header="EDITAR"
								:styles="{ width: '80px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn-cancel btn-icon-split"
										title="EDITAR PREGUNTA"
										@click="Editar(data)"
									>
										<span class="icon text-white">
											<i class="pi pi-pencil"></i>
										</span>
									</button>
								</template>
							</Column>
							<Column
								field="nombre_categoria"
								header="CATEGORÍA"
								:styles="{
									width: '150px',
									justifyContent: 'center',
								}"
							>
							</Column>
							<Column
								field="criterio"
								header="CRITERIO"
								:styles="{ width: '250px' }"
							>
							</Column>

							<Column
								field="pregunta"
								header="PREGUNTA"
								:styles="{ width: '300px' }"
							>
							</Column>
							<Column
								field="habilitado"
								header="HABILITADO"
								:styles="{ width: '80px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.habilitado == 1 ? "SI" : "NO" }}
								</template>
							</Column>
						</DataTable>
					</div>

					<!-- The Modal -->
					<div id="mdlDatosPregunta" class="modal">
						<!-- Modal content -->
						<div class="modal-content w-40 mdlDatosPregunta">
							<div class="content" style="display: block">
								<div class="card">
									<headerCloseModal
										:titulo_modal="title_modal"
										:nombre_modal="'mdlDatosPregunta'"
									>
									</headerCloseModal>

									<div class="card-title">DATOS DE LA PREGUNTA</div>
									<div class="card-body card-block">
										<div class="form-row">
											<div class="form-group col-md-7">
												<label class="label-title">CRITERIO</label>
												<input
													type="text"
													class="form-control mayus"
													v-model="frmDatosPregunta.criterio"
													:class="[
														submited
															? $v.frmDatosPregunta.criterio.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
												/>
											</div>

											<div class="form-group col-md-5">
												<label class="label-title">CATEGORÍA</label>

												<span
													v-if="
														submited &&
														$v.frmDatosPregunta.categoria_id.noZero.$invalid
													"
													class="span-error-message"
												>
													*
												</span>

												<select
													class="form-control"
													@change="CambiarPeso"
													v-model="frmDatosPregunta.categoria_id"
													:class="[
														submited
															? $v.frmDatosPregunta.categoria_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
												>
													<option :value="0" disabled selected>
														Seleccione...
													</option>
													<option
														v-for="item in categorias"
														:value="item.id"
														:key="item.id"
													>
														{{ item.categoria }}
													</option>
												</select>

												<span
													class="ml-1 badge badge-light align-middle"
													style="line-height: 2 !important"
													>PESO: {{ peso_categoria }}</span
												>
											</div>

											<div class="form-group col-md-12">
												<label class="label-title">PREGUNTA</label>

												<textarea
													class="form-control mayus text-row"
													rows="4"
													v-model="frmDatosPregunta.pregunta"
													:class="[
														submited
															? $v.frmDatosPregunta.pregunta.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
												></textarea>
											</div>
										</div>
										<div
											class="form-row"
											v-if="frmDatosPregunta.modo == 'EDITAR'"
										>
											<div class="form-group col-md-12">
												<div class="text-right">
													<div class="form-check">
														<input
															class="form-check-input"
															type="checkbox"
															id="chbHabilitado"
															v-model="frmDatosPregunta.habilitado"
														/>
														<label class="label-title" for="chbHabilitado">
															HABILITADO
														</label>
													</div>
												</div>
											</div>
										</div>

										<hr />
										<div class="text-right">
											<button
												class="btn btn-action btn-icon-split"
												@click="Guardar"
												title="GUARDAR PREGUNTA"
											>
												<span class="icon text-white">
													<i class="pi pi-save"></i>
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
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
import headerCloseModal from "@/Pages/Gth/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import { FilterMatchMode } from "primevue/api";

import { required } from "vuelidate/lib/validators";

const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,

		DataTable,
		Column,
	},

	data: () => ({
		submited: false,
		title_modal: null,

		preguntas: [],
		categorias: [],
		filtros_tabla: {},

		peso_categoria: "-",
		frmDatosPregunta: {
			modo: null,
			id: null,
			pregunta: null,
			criterio: null,
			categoria_id: 0,
			habilitado: 1,
		},
	}),
	validations: {
		frmDatosPregunta: {
			pregunta: { required },
			criterio: { required },
			categoria_id: { noZero },
		},
	},
	created() {
		this.filtros_tabla = {
			global: { value: null, matchMode: FilterMatchMode.CONTAINS },
			nombre_categoria: { value: null, matchMode: FilterMatchMode.CONTAINS },
			criterio: { value: null, matchMode: FilterMatchMode.CONTAINS },
			pregunta: { value: null, matchMode: FilterMatchMode.CONTAINS },
		};
	},
	mounted() {
		this.ListarRecursos();
	},
	methods: {
		async ListarRecursos() {
			let self = this;
			await axios
				.get(route("col.preguntas.listar_recursos"))
				.then(function (response) {
					self.preguntas = response.data.preguntas;
					self.categorias = response.data.categorias;
				});
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		CambiarPeso(e) {
			let categoria_id = e.target.value;
			if (categoria_id == 0) {
				this.peso_categoria = "-";
			} else {
				let numero_decimales = 2;
				this.peso_categoria = this.categorias.filter(
					(item) => item.id == categoria_id
				)[0].peso;
				this.peso_categoria = this.roundTo(
					this.peso_categoria,
					numero_decimales
				);
			}
		},
		roundTo(value, numero_decimales) {
			let valor = 0;
			let numero_decimales_value = numero_decimales;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales_value);
		},
		Nuevo() {
			this.submited = false;
			this.title_modal = "NUEVA PREGUNTA";
			this.peso_categoria = "-";
			this.frmDatosPregunta.id = null;
			this.frmDatosPregunta.modo = "NUEVO";
			this.frmDatosPregunta.pregunta = null;
			this.frmDatosPregunta.criterio = null;
			this.frmDatosPregunta.categoria_id = 0;

			$("#mdlDatosPregunta").css("display", "block");
		},
		Editar(item) {
			this.submited = false;
			this.title_modal = "EDITAR PREGUNTA";
			this.peso_categoria = this.roundTo(item.peso_categoria, 2);
			this.frmDatosPregunta.id = item.id;
			this.frmDatosPregunta.modo = "EDITAR";
			this.frmDatosPregunta.pregunta = item.pregunta;
			this.frmDatosPregunta.criterio = item.criterio;
			this.frmDatosPregunta.habilitado = item.habilitado;
			this.frmDatosPregunta.categoria_id = item.categoria_id;

			$("#mdlDatosPregunta").css("display", "block");
		},
		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosPregunta.$invalid == true) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			} else {
				let data = new FormData();
				data.append("id", this.frmDatosPregunta.id);
				data.append("modo", this.frmDatosPregunta.modo);
				data.append("pregunta", this.frmDatosPregunta.pregunta);

				axios
					.post(route("col.preguntas.verificar"), data)
					.then(function (response) {
						let resultado = response.data;
						if (resultado == "EXISTE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Esta Pregunta ya se encuentra registrada, intente nuevamente.",
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
									Swal.fire({
										title: "REGISTRANDO",
										showConfirmButton: false,
										allowOutsideClick: false,
										willOpen: async () => {
											Swal.showLoading();
											return await axios
												.post(
													route("col.preguntas.guardar"),
													self.frmDatosPregunta
												)
												.then((response) => {
													$("#mdlDatosPregunta").css("display", "none");
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
		},
	},
};
</script>

<style >
.slot-preguntas {
	width: 75% !important;
	margin-left: 12.5% !important;
}

.mdlDatosPregunta {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-preguntas {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosPregunta {
		margin-top: 20%;
	}
}
</style>
