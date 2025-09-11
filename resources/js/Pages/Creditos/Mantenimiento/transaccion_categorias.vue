<template>
	<layout ref="layout">
		<div class="slot_body slot-transaccion-categorias" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CATEGORÍAS DE TRANSACCIÓN'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										@change="Listar()"
										v-model="frmDatosCategoria.agencia_seleccionada"
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
								<button class="btn btn-action btn-icon-split" @click="Nuevo">
									<span class="icon text-white">
										<i class="fas fa-plus"></i
									></span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>
						<div class="card-title mb-1">LISTA DE RESULTADOS</div>

						<fieldset class="p-0 pb-1 mb-1">
							<legend>
								<label class="label-title">FILTRAR RESULTADOS</label>
							</legend>
							<div class="form-row col-md-12">
								<div class="input-group col-md-6">
									<div class="input-group-prepend">
										<span class="input-group-text"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										v-model="filtros_tabla['categoria'].value"
										placeholder="Buscar..."
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>

								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">TIPO</span>
									</div>
									<select
										class="form-control center"
										v-model="filtros_tabla['tipo'].value"
										:disabled="this.lista_categorias.length == 0"
									>
										>
										<option :value="null" selected>TODOS</option>
										<option value="I">INGRESO</option>
										<option value="E">EGRESO</option>
									</select>
								</div>
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">HAB.</span>
									</div>
									<select
										class="form-control center"
										v-model="filtros_tabla['habilitado'].value"
										:disabled="this.lista_categorias.length == 0"
									>
										>
										<option :value="null" selected>TODOS</option>
										<option :value="true">SI</option>
										<option :value="false">NO</option>
									</select>
								</div>
							</div>
						</fieldset>

						<DataTable
							:value="lista_categorias"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							selectionMode="single"
							:filters="filtros_tabla"
							showGridlines
						>
							<Column
								field="editar"
								header="EDITAR"
								:styles="{ width: '50px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn-action btn-icon-split"
										id="btnRecibirRechazar"
										title="Editar"
										@click="Editar(data)"
									>
										<span class="icon text-white">
											<i class="fas fa-edit"></i>
										</span>
									</button>
								</template>
							</Column>
							<Column
								field="categoria"
								header="CATEGORÍA"
								:styles="{ width: '200px' }"
							></Column>
							<Column
								field="descripcion"
								header="DESCRIPCIÓN"
								:styles="{ width: '100px' }"
								><template #body="{ data }">
									{{ data.descripcion != null ? data.descripcion : "-" }}
								</template>
							</Column>
							<Column
								field="tipo"
								header="TIPO"
								:styles="{ width: '50px', justifyContent: 'center' }"
							></Column>
							<Column
								field="habilitado"
								header="HABILITADO"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.habilitado == 1 ? "SI" : "NO" }}
								</template>
							</Column>
							<template #empty> No hay CATEGORÍAS encontradas.</template>
						</DataTable>
					</div>
				</div>
			</div>
			<!-- ---- Modal modo  -->
			<div id="mdlDatosCategoria" class="modal modal-right">
				<div class="modal-content w-40 mdlDatosCategoria">
					<div class="content" style="display: block">
						<div class="card">
							<!-- ----- -->
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosCategoria'"
							>
							</headerCloseModal>
							<div class="card-title">DATOS DE CATEGORÍA</div>
							<div class="card-body card-block">
								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="label-title"> CATEGORÍA</label>

										<input
											class="form-control mayus"
											type="text"
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

									<div class="form-group col-md-6">
										<label class="label-title" for="text-input">TIPO</label>

										<select
											class="form-control center"
											v-model="frmDatosCategoria.tipo"
											:class="[
												submited
													? $v.frmDatosCategoria.tipo.$invalid
														? 'is-invalid'
														: 'is-valid'
													: '',
											]"
										>
											<option :value="0" selected disabled>Seleccionar</option>
											<option value="I">INGRESO</option>
											<option value="E">EGRESO</option>
										</select>
									</div>
									<div class="form-group col-md">
										<label class="label-title">DESCRIPCIÓN</label>
										<textarea
											class="form-control mayus text-row"
											type="text "
											rows="2"
											v-model="frmDatosCategoria.descripcion"
										></textarea>
									</div>
								</div>
								<!-- ---------------------- -->
								<div
									class="form-row ml-1 mt-1"
									v-if="frmDatosCategoria.modo == 'EDITAR'"
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
												v-model="frmDatosCategoria.habilitado" /><span
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
										@click="Guardar"
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
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import { FilterMatchMode } from "primevue/api";

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose, headerCloseModal, DataTable, Column },

	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			lista_categorias: [],

			titulo_modal: "NUEVA",

			frmDatosCategoria: {
				id: null,
				modo: "NUEVO",
				agencia_seleccionada: 0,
				categoria: null,
				descripcion: null,
				tipo: 0,
				habilitado: 1,
			},

			filtros_tabla: {
				global: { value: null, matchMode: FilterMatchMode.CONTAINS },
				categoria: { value: null, matchMode: FilterMatchMode.CONTAINS },
				tipo: { value: null, matchMode: FilterMatchMode.CONTAINS },
				habilitado: { value: null, matchMode: FilterMatchMode.CONTAINS },
			},
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			this.frmDatosCategoria.agencia_seleccionada = agencia_id;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.frmDatosCategoria.agencia_seleccionada = mi_agencia[0].id;
				this.Listar();
			} else {
				if (value.length > 0) {
					this.frmDatosCategoria.agencia_seleccionada = value[0].id;
					this.Listar();
				} else {
					this.frmDatosCategoria.agencia_seleccionada = 0;
				}
			}
		},
	},
	validations: {
		frmDatosCategoria: {
			categoria: { required },
			tipo: { noZero },
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
	},
	methods: {
		ListarAgenciasPermitidas() {
			// this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_MANTENIMIENTO/TRANSACCION_CATEGORIAS"
			);
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},
		async Listar() {
			const params = {
				agencia_id: this.frmDatosCategoria.agencia_seleccionada,
			};
			return await axios
				.get(route("man.tra.categorias.listar"), {
					params,
				})
				.then((response) => {
					this.lista_categorias = response.data.categorias;
				});
		},

		Nuevo() {
			this.submited = false;

			this.titulo_modal = "NUEVA CATEGORÍA";

			this.frmDatosCategoria.id = null;
			this.frmDatosCategoria.modo = "NUEVO";
			this.frmDatosCategoria.categoria = null;
			this.frmDatosCategoria.descripcion = null;
			this.frmDatosCategoria.tipo = 0;

			$("#mdlDatosCategoria").css("display", "block");
		},
		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR CATEGORÍA";

			this.frmDatosCategoria.id = item.id;
			this.frmDatosCategoria.modo = "EDITAR";
			this.frmDatosCategoria.categoria = item.categoria;
			this.frmDatosCategoria.descripcion = item.descripcion;
			this.frmDatosCategoria.tipo = item.tipo;
			this.frmDatosCategoria.habilitado = item.habilitado;

			$("#mdlDatosCategoria").css("display", "block");
		},
		Cerrar() {
			$("#mdlDatosCategoria").css("display", "none");
		},
		async Guardar() {
			this.submited = true;

			if (this.$v.frmDatosCategoria.$invalid) {
				return false;
			}

			const params = this.frmDatosCategoria;

			// this.$inertia.get(route("man.tra.categoria.verificar"), params);
			// return false;

			await axios
				.get(route("man.tra.categorias.verificar"), { params })
				.then((response) => {
					if (response.data === "EXISTE") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Esta CATEGORÍA ya EXISTE.",
						});
						return false;
					} else if (response.data === "NO_EXISTE") {
						// ----
						Swal.fire({
							title: this.titulo_modal,
							text: "¿Desea continuar?",
							confirmButtonText: "Si",
							showCancelButton: true,
							cancelButtonText: "No",
							allowOutsideClick: false,
							preConfirm: () => {
								Swal.fire({
									title: "REGISTRANDO...",
									allowOutsideClick: false,
									didOpen: async () => {
										let data = new FormData();

										data.append(
											"frmDatosCategoria",
											JSON.stringify(this.frmDatosCategoria)
										);

										// this.$inertia.post(route("man.tra.categoria.guardar"), data);
										// Swal.close();
										// return false;

										Swal.showLoading();
										await axios
											.post(route("man.tra.categorias.guardar"), data)
											.then(async (response) => {
												Swal.close();
												$("#mdlDatosCategoria").css("display", "none");
												await this.Listar();
												return Swal.fire({
													icon: "success",
													title: "¡ÉXITO!",
													text: response.data.message || "Registro realizado.",
													timer: 1200,
													showConfirmButton: false,
												});
											})
											.catch((error) => {
												console.log(error);
												Swal.showValidationMessage(
													`Se ha producido un ERROR. 
											Por favor, no realice más acciones en el sistema y
											 contacte al área de SOPORTE para su revisión.`
												);
											});
									},
								});
							},
						});
					}
				});
		},
	},
};
</script>

<style lang="css">
.slot-transaccion-categorias {
	width: 55% !important;
	margin-left: 22.5% !important;
}

@media only screen and (max-width: 900px) {
	.slot-transaccion-categorias {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>

