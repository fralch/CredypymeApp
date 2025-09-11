<template>
	<layout ref="layout">
		<div class="slot_body slot-examenes" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LISTA DE EXÁMENES'"></headerClose>
					<div class="card-title">LISTA DE RESULTADOS</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-10 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									v-model="filtros_tabla['examen'].value"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="col-md-1 col-2 ml-1">
								<button
									class="btn btn-action btn-icon-split"
									@click="NuevoExamen"
									title="NUEVO EXAMEN"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>

						<div class="mt-2">
							<DataTable
								:value="examenes"
								:scrollable="true"
								scrollDirection="both"
								scrollHeight="400px"
								:filters="filtros_tabla"
								:globalFilterFields="['examen']"
								showGridlines
							>
								<Column
									field="acciones"
									header="ACCIONES"
									:styles="{ maxWidth: '120px', justifyContent: 'center' }"
								>
									<template #body="{ data }">
										<div class="btn-group" role="group">
											<button
												class="btn btn-action"
												type="button"
												title="VER EXAMEN"
												@click="VerExamen(data)"
											>
												<span class="icon text-white">
													<i class="pi pi-eye"></i>
												</span>
											</button>

											<button
												class="btn btn-cancel"
												type="button"
												title="EDITAR EXAMEN"
												@click="Editar(data)"
											>
												<span class="icon text-white">
													<i class="pi pi-pencil"></i>
												</span>
											</button>
										</div>
									</template>
								</Column>
								<Column
									field="examen"
									header="EXAMEN"
									:styles="{
										width: '300px',
									}"
								>
								</Column>
								<Column
									field="examen"
									header="HABILITADO"
									:styles="{
										maxWidth: '80px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
										{{ data.habilitado == 1 ? "SI" : "NO" }}
									</template>
								</Column>
							</DataTable>
						</div>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlVerExamen" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-46 mdlVerExamen">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="title_modal"
								:nombre_modal="'mdlVerExamen'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="input-group col-md-12 mb-1">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>NOMBRE DEL EXAMEN:
												</span>
											</div>

											<input
												class="form-control center"
												type="text"
												v-model="nombre_examen_seleccionado"
												:disabled="true"
											/>
										</div>
									</div>
									<table class="table" id="tblVerExamen" width="100%">
										<thead>
											<tr>
												<th style="min-width: 30px !important">CRITERIOS</th>
												<th style="min-width: 325px !important">DEFINICIÓN</th>
												<th style="min-width: 75px !important">PESO</th>
											</tr>
										</thead>
										<tbody v-for="(item, index) in examen_detalle" :key="index">
											<tr
												class="fila"
												style="background-color: var(--colorMedio) !important"
											>
												<td
													colspan="3"
													class="state text-white"
													align="center"
													style="font-size: 12px !important"
												>
													{{ item.nombre_categoria }}
												</td>
											</tr>
											<tr
												v-for="(item_1, index_1) in item.datos"
												:key="index_1"
												class="fila table-bordered"
												:style="
													index_1 % 2 == 0
														? 'background-color: var(--colorBajo) !important'
														: ''
												"
											>
												<td class="state table-bordered" align="left">
													{{ item_1.criterio }}
												</td>

												<td class="state table-bordered" align="left">
													{{ item_1.pregunta }}
												</td>
												<td class="state table-bordered" align="center">-</td>
											</tr>

											<tr
												class="fila"
												style="background-color: var(--gray) !important"
											>
												<td class="state table-bordered" align="left"></td>
												<td class="state text-white font-11" align="left">
													SUBTOTAL
												</td>
												<td class="state text-white font-11" align="center">
													{{ roundTo(item.peso_categoria, 2) }}
													%
												</td>
											</tr>
										</tbody>
										<tfoot class="table-bordered">
											<tr>
												<th class="black" align="left"></th>
												<th class="text-left font-11" align="left">TOTAL</th>
												<th class="text-center font-11">
													{{ roundTo(totales.t_peso_total, 2) }}
													%
												</th>
											</tr>
										</tfoot>
									</table>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosExamen" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-70 mdlDatosExamen">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="title_modal"
								:nombre_modal="'mdlDatosExamen'"
							>
							</headerCloseModal>

							<div class="card-title">INFORMACIÓN</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="input-group col-md-12">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>NOMBRE DEL EXAMEN</span
												>
											</div>
											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosExamen.examen"
												:class="[
													submited
														? $v.frmDatosExamen.examen.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											/>
											<div
												class="input-group-append"
												v-if="frmDatosExamen.modo == 'EDITAR'"
											>
												<div class="input-group-text">
													<div class="form-check">
														<input
															class="form-check-input"
															type="checkbox"
															id="chbHabilitado"
															v-model="frmDatosExamen.habilitado"
														/>
														<label class="label-title" for="chbHabilitado">
															Habilitado
														</label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="card-title mt-2">LISTA DE PREGUNTAS</div>
									<div class="form-row mt-1">
										<div class="input-group col-md-7 col-5">
											<div class="input-group-prepend">
												<span class="input-group-text"
													><i class="fas fa-search"></i
												></span>
											</div>
											<input
												class="form-control mayus"
												type="text"
												id="inpBuscarPreguntas"
												autocomplete="off"
												spellcheck="false"
												@focus="hidenav()"
												@blur="shownav()"
											/>
										</div>
										<div class="input-group col-md-5">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>CATEGORÍAS</span
												>
											</div>
											<select
												class="form-control"
												name="categorias"
												id="slcCategorias"
												data-index="3"
												:disabled="preguntas.length == 0"
											>
												<option :value="0">TODAS</option>
												<option v-for="item in categorias" :key="item.id">
													{{ item.categoria }}
												</option>
											</select>
										</div>
									</div>
									<table class="table" id="tblPreguntas" width="100%">
										<thead>
											<tr>
												<th style="min-width: 30px !important">CHECK</th>

												<th style="min-width: 50px !important">CRITERIO</th>
												<th style="min-width: 300px !important">PREGUNTA</th>
												<th style="min-width: 50px !important">CATEGORIA</th>
											</tr>
										</thead>
										<tbody>
											<tr
												class="table-bordered"
												v-for="(item, index) in preguntas"
												:key="index"
											>
												<td align="center">
													<div class="align-middle">
														<div class="checkbox">
															<label
																class="align-middle"
																style="
																	font-size: 2em;
																	margin-bottom: 0 !important;
																	height: 28.6px !important;
																"
																:for="index"
																><input
																	type="checkbox"
																	:id="index"
																	:value="item.id"
																	v-model="
																		frmDatosExamen.preguntas_seleccionadas
																	" /><span
																	class="cr"
																	style="margin-right: 0 !important"
																	><i class="cr-icon fa fa-check"></i></span
															></label>
														</div>
													</div>
												</td>

												<td>
													{{ item.criterio }}
												</td>
												<td>
													{{ item.pregunta }}
												</td>
												<td align="center">
													{{ item.nombre_categoria }}
												</td>
											</tr>
										</tbody>
									</table>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar"
										title="GUARDAR EXAMEN"
										:disabled="
											frmDatosExamen.preguntas_seleccionadas.length == 0
										"
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import { FilterMatchMode } from "primevue/api";

import { required } from "vuelidate/lib/validators";
const noEmpty = (value) => value.length != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,

		DataTable,
		Column,
	},

	data() {
		return {
			examenes: [],
			examenes_preguntas: [],
			categorias: [],
			preguntas: [],

			filtros_tabla: {},

			submited: false,
			title_modal: null,
			frmDatosExamen: {
				id: null,
				modo: null,
				examen: null,
				habilitado: null,

				preguntas_seleccionadas: [],
			},
			nombre_examen_seleccionado: null,

			examen_detalle: [],
			totales: [],
		};
	},
	validations: {
		frmDatosExamen: {
			examen: { required },
			preguntas_seleccionadas: { noEmpty },
		},
	},

	watch: {
		preguntas() {
			$("#tblPreguntas").DataTable().destroy();
			this.TablaPreguntas();
		},
		
	},
	created() {
		this.filtros_tabla = {
			global: { value: null, matchMode: FilterMatchMode.CONTAINS },
			examen: { value: null, matchMode: FilterMatchMode.CONTAINS },
		};
	},
	mounted() {
		this.TablaPreguntas();
		this.ListarRecursos();
	},
	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		async ListarRecursos() {
			let self = this;
			await axios
				.get(route("col.examenes.listar_recursos"))
				.then(function (response) {
					self.examenes = response.data.examenes;
					self.examenes_preguntas = response.data.examenes_preguntas;
					self.categorias = response.data.categorias;
					self.preguntas = response.data.preguntas;
				});
		},
		roundTo(value, numero_decimales) {
			let valor = 0;
			let numero_decimales_value = numero_decimales;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales_value);
		},

		TablaPreguntas() {
			this.$nextTick(() => {
				var table = $("#tblPreguntas").DataTable({
					scrollY: "300px",
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

				$("#slcCategorias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscarPreguntas").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		NuevoExamen() {
			this.submited = false;
			this.frmDatosExamen.preguntas_seleccionadas = [];
			this.frmDatosExamen.examen = null;
			this.frmDatosExamen.modo = "NUEVO";
			this.frmDatosExamen.habilitado = 1;

			this.title_modal = "NUEVO EXAMEN";

			$("#tblPreguntas").DataTable().destroy();
			this.TablaPreguntas();
			$("#mdlDatosExamen").css("display", "block");
		},

		VerExamen(examen) {
			let self = this;

			this.examen_detalle = [];
			this.nombre_examen_seleccionado = examen.examen;
			this.title_modal = "VISTA PREVIA DEL EXAMEN";

			let data = new FormData();
			data.append("examen_id", examen.id);

			Swal.fire({
				title: "ELABORANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("gth.col_mes.ver_examen"), data);
					// return false;
					axios.post(route("col.examenes.ver"), data).then(function (response) {
						if (response.data.examen_detalle.length == 0) {
							self.examen_detalle = [];
							self.totales = [];

							return Swal.fire({
								icon: "info",
								title: "¡Ups!",
								text: "No se encontraron datos",
								allowOutsideClick: true,
							});
						} else {
							self.examen_detalle = response.data.examen_detalle;
							self.totales = self.CalcularTotales(response.data.examen_detalle);

							$("#mdlVerExamen").css("display", "block");

							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
								timer: 1200,
								showConfirmButton: false,
							});
						}
					});
				},
			});
		},

		CalcularTotales(array) {
			let obj = {
				t_peso_total: 0,
			};
			array.forEach((element_1) => {
				obj.t_peso_total += parseFloat(element_1.peso_categoria);
			});

			return obj;
		},

		Editar(item) {
			let self = this;
			this.title_modal = "EDITAR EXÁMEN";

			this.frmDatosExamen.preguntas_seleccionadas = [];
			this.frmDatosExamen.modo = "EDITAR";

			this.frmDatosExamen.id = item.id;
			this.frmDatosExamen.examen = item.examen;
			this.frmDatosExamen.habilitado = item.habilitado;

			let preguntas_actuales = this.examenes_preguntas.filter(
				(item) => item.examen_id == self.frmDatosExamen.id
			);
			preguntas_actuales.forEach(function callback(currentValue, index) {
				self.frmDatosExamen.preguntas_seleccionadas.push(
					currentValue.pregunta_id
				);
			});

			$("#mdlDatosExamen").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosExamen.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe ingresar el nombre del examen, verifique.",
				});
				return false;
			} else {
				let data = new FormData();
				data.append("modo", self.frmDatosExamen.modo);
				data.append("nombre", self.frmDatosExamen.examen);
				data.append("id", self.frmDatosExamen.id);

				axios
					.post(route("col.examenes.verificar"), data)
					.then(function (response) {
						let resultado = response.data;
						if (resultado == "EXISTE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Este EXAMEN ya está registrado, intente nuevamente.",
							});
							return false;
						} else {
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
													route("col.examenes.guardar"),
													self.frmDatosExamen
												)
												.then((response) => {
													$("#mdlDatosExamen").css("display", "none");
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

<style lang="css">
.slot-examenes {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosExamen {
	margin-top: 2%;
}
.mdlVerExamen {
	width: 56% !important;
	margin-left: 22% !important;
	margin-top: 2%;
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

tr.fila:hover td {
	background-color: #ccc;
}

tr.fila:hover td.state {
	background-color: transparent !important;
}

@media (max-width: 900px) {
	.slot-examenes {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosExamen {
		margin-top: 20%;
	}
	.mdlVerExamen {
		margin-top: 20%;
	}
}
</style>
