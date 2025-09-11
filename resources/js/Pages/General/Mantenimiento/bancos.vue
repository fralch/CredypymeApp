<template>
	<layout ref="layout">
		<div class="slot_body slot-bancos" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'BANCOS'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-6 col-7">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscarSectores"
										placeholder="Escriba el texto a buscar"
										autocomplete="off"
										spellcheck="false"
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
										v-model="filtros_tabla['agencia'].value"
									>
										<option :value="null" selected>TODAS</option>
										<option
											v-for="(item, index) in agencias"
											:key="index"
											:value="item.agencia"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group col-md-2">
								<button
									class="btn btn-action btn-icon-split"
									@click="Nuevo"
									title="Nuevo BANCO"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>
						<div class="card-title mb-1">LISTA DE RESULTADOS</div>

						<DataTable
							:value="lista_bancos"
							:filters="filtros_tabla"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							selectionMode="single"
							showGridlines
						>
							<Column
								field="numero"
								header="N°"
								:styles="{ width: '40px', justifyContent: 'center' }"
							>
								<template #body="slotProps">
									{{ slotProps.index + 1 }}
								</template>
							</Column>
							<Column
								header="EDITAR"
								:styles="{ width: '60px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn-action"
										title="Editar BANCO"
										@click="Editar(data)"
									>
										<span class="icon text-white">
											<i
												class="fas fa-edit"
												style="font-size: 15px !important"
											></i>
										</span>
									</button>
								</template>
							</Column>

							<Column
								field="habilitado"
								header="HABILITADO"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<InputSwitch
										:value="data.habilitado"
										@change="HabilitarDeshabilitar(data)"
									/>
								</template>
							</Column>
							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{
									width: '120px',
									justifyContent: 'center',
								}"
							>
							</Column>
							<Column
								field="banco"
								header="BANCO"
								:styles="{
									width: '200px',
									justifyContent: 'center',
								}"
							>
							</Column>
							<Column
								field="titular"
								header="TITULAR"
								:styles="{ width: '200px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="numero"
								header="NÚMERO_CUENTA"
								:styles="{ width: '200px', justifyContent: 'left' }"
							>
							</Column>
							<Column
								field="cci"
								header="CCI"
								:styles="{ width: '200px', justifyContent: 'left' }"
							>
								<template #body="{ data }">
									{{ data.cci ? data.cci : "-" }}
								</template>
							</Column>
							<Column
								field="detalle"
								header="DETALLE"
								:styles="{ width: '200px', justifyContent: 'left' }"
							>
								<template #body="{ data }">
									{{ data.detalle ? data.detalle : "-" }}
								</template>
							</Column>

							<template #empty> No hay BANCOS registrados.</template>
						</DataTable>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosBanco" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-50 mdlDatosBanco">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosBanco'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off">
									<div class="form-row">
										<div class="input-group col-md-6 offset-md-3">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>AGENCIA</span
												>
											</div>
											<select
												class="form-control center"
												v-model="frmDatosBanco.agencia_id"
											>
												<option
													v-for="(item, index) in agencias"
													:key="index"
													:value="item.id"
												>
													{{ item.agencia }}
												</option>
											</select>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">BANCO</label>
											<input
												type="text"
												class="form-control mayus"
												maxlength="100"
												v-model="frmDatosBanco.banco"
												:class="[
													submited
														? $v.frmDatosBanco.banco.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">TITULAR</label>
											<input
												type="text"
												class="form-control mayus"
												maxlength="100"
												v-model="frmDatosBanco.titular"
												:class="[
													submited
														? $v.frmDatosBanco.titular.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">NÚMERO</label>
											<input
												type="text"
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosBanco.numero"
												:class="[
													submited
														? $v.frmDatosBanco.numero.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">CCI</label>
											<input
												type="text"
												class="form-control mayus"
												maxlength="50"
												v-model="frmDatosBanco.cci"
											/>
										</div>
										<div class="form-group col-md-12 col-6">
											<label class="label-title">DETALLE</label>
											<textarea
												class="form-control text-row mayus"
												maxlength="200"
												rows="2"
												v-model="frmDatosBanco.detalle"
											></textarea>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar"
										title="Guardar BANCO"
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
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";
import headerCloseModal from "@/Pages/General/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import InputSwitch from "primevue/inputswitch/inputswitch.common";
import { FilterMatchMode } from "primevue/api";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DataTable,
		Column,
		InputSwitch,
	},
	data() {
		return {
			lista_bancos: [],
			submited: false,

			titulo_modal: "NUEVO BANCO",
			frmDatosBanco: {
				modo: "",
				id: null,
				agencia_id: null,
				banco: null,
				titular: null,
				numero: null,
				cci: null,
				detalle: null,

				habilitado: false,
			},

			filtros_tabla: {
				global: { value: null, matchMode: FilterMatchMode.CONTAINS },
				agencia: { value: null, matchMode: FilterMatchMode.CONTAINS },
			},
		};
	},
	validations: {
		frmDatosBanco: {
			banco: { required },
			titular: { required },
			numero: { required },
		},
	},
	computed: {
		agencias() {
			return this.$inertia.page.props.application.agencias;
		},
	},

	mounted() {
		this.Listar();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		async Listar() {
			// this.$inertia.get(route("man.bancos.listar"), params);
			// return false;

			await axios.get(route("man.bancos.listar")).then((response) => {
				this.lista_bancos = response.data.lista_bancos;
			});
		},

		Nuevo() {
			this.submited = false;
			this.titulo_modal = "NUEVO BANCO";

			this.frmDatosBanco.modo = "NUEVO";
			this.frmDatosBanco.id = null;
			this.frmDatosBanco.agencia_id = this.agencias[0].id;
			this.frmDatosBanco.banco = null;
			this.frmDatosBanco.titular = null;
			this.frmDatosBanco.numero = null;
			this.frmDatosBanco.cci = null;
			this.frmDatosBanco.detalle = null;

			this.frmDatosBanco.habilitado = false;

			$("#mdlDatosBanco").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR BANCO";

			this.frmDatosBanco.modo = "EDITAR";
			this.frmDatosBanco.id = item.id;
			this.frmDatosBanco.agencia_id = item.agencia_id;
			this.frmDatosBanco.banco = item.banco;
			this.frmDatosBanco.titular = item.titular;
			this.frmDatosBanco.numero = item.numero;
			this.frmDatosBanco.cci = item.cci;
			this.frmDatosBanco.detalle = item.detalle;

			$("#mdlDatosBanco").css("display", "block");
		},
		async HabilitarDeshabilitar(item) {
			let mensaje = "";
			if (item.habilitado) {
				mensaje = "DESHABILITAR";
			} else {
				mensaje = "HABILITAR";
			}

			if (item.acumulado > 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text:
						"No se puede " +
						mensaje +
						" este registro, porque tiene efectivo acumulado.",
					allowOutsideClick: true,
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿Desea " + mensaje + " este BANCO?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
				preConfirm: () => {
					Swal.fire({
						title: "REGISTRANDO...",
						allowOutsideClick: false,
						didOpen: async () => {
							Swal.showLoading();

							// this.$inertia.patch(
							// 	route("man.bancos.alternar", { id: item.id }),
							// 	data
							// );
							// return false;

							await axios
								.patch(route("man.bancos.alternar", { id: item.id }))
								.then(async (response) => {
									Swal.close();
									$("#mdlDatosBanco").css("display", "none");
									await this.Listar();
									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										text: response.data.message || "Cambio realizado.",
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
		},

		Guardar() {
			this.submited = true;

			if (this.$v.frmDatosBanco.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			} else {
				Swal.fire({
					icon: "question",
					text: "¿DESEA GUARDAR LOS CAMBIOS?",
					confirmButtonText: "Si",
					showCancelButton: true,
					cancelButtonText: "No",
					allowOutsideClick: false,
					preConfirm: () => {
						Swal.fire({
							title: "REGISTRANDO...",
							allowOutsideClick: false,
							didOpen: async () => {
								// this.$inertia.post(
								// 	route("man.bancos.guardar"),
								// 	this.frmDatosBanco
								// );
								// return false;

								Swal.showLoading();
								await axios
									.post(route("man.bancos.guardar"), this.frmDatosBanco)
									.then(async (response) => {
										Swal.close();
										$("#mdlDatosBanco").css("display", "none");
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
		},
	},
};
</script>

<style lang="css">
.slot-bancos {
	width: 60% !important;
	margin-left: 20% !important;
}

.mdlDatosBanco {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-bancos {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosBanco {
		margin-top: 20%;
	}
}
</style>
