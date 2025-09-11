<template>
	<div class="modal" id="mdlCanastaAsignacion">
		<div class="modal-content w-50 mdlCanastaAsignacion">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'ASIGNAR SUMINISTROS'"
						:nombre_modal="'mdlCanastaAsignacion'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLES</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-12">
								<label class="label-title">SUMINISTROS SELECCIONADOS</label>
								<span
									v-if="
										submited &&
										frmCanastaAsignacion.canasta_asignacion.length == 0
									"
									class="span-error-message"
								>
									*
								</span>

								<DataTable
									:value="frmCanastaAsignacion.canasta_asignacion"
									:scrollable="true"
									scrollDirection="both"
									:scrollHeight="String(windowHeigth * 0.49) + 'px'"
									showGridlines
								>
									<Column
										field="codigo"
										header="CÓDIGO"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="suministro"
										header="SUMINISTRO"
										:styles="{
											width: '200px',
											justifyContent: 'left',
											fontWeight: 'bolder',
										}"
									>
									</Column>
									<Column
										field="cantidad"
										header="CANTIDAD"
										:styles="{
											width: '160px',
											justifyContent: 'center',
										}"
									>
										<template #body="{ data }">
											<InputNumber
												v-model.number="data.cantidad_asignar"
												showButtons
												buttonLayout="horizontal"
												decrementButtonClass="p-button-danger"
												incrementButtonClass="p-button-success"
												incrementButtonIcon="pi pi-plus"
												decrementButtonIcon="pi pi-minus"
												mode="decimal"
												locale="en-US"
												:maxFractionDigits="1"
												:minFractionDigits="1"
												:step="parseFloat(data.escala)"
												:min="1"
												:max="parseFloat(data.cantidad)"
											/>
										</template>
									</Column>

									<Column
										field="medicion"
										header="MEDICIÓN"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									></Column>
									<Column
										field="cantidad"
										header="STOCK"
										:styles="{
											width: '70px',
											justifyContent: 'center',
										}"
										><template #body="{ data }">{{
											parseFloat(data.cantidad).toFixed(2)
										}}</template>
									</Column>

									<Column
										field="condicion"
										header="CONDICIÓN"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									>
									</Column>
									<Column
										field="clasificacion"
										header="CLASIFICACION"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									>
									</Column>
								</DataTable>
							</div>
						</div>

						<div class="form-row mt-3">
							<div class="form-group col-md-4 col-5">
								<label class="label-title">AGENCIA</label>
								<input
									type="text"
									class="form-control center mayus"
									:value="frmCanastaAsignacion.agencia"
									readonly
								/>
							</div>
							<div class="form-group col-md-6 col-7">
								<label class="label-title">RESPONSABLE</label>
								<span
									v-if="
										submited && !$v.frmCanastaAsignacion.responsable_id.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control center"
									v-model.number="frmCanastaAsignacion.responsable_id"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in responsables_filtrados"
										:key="index"
										:value="item.id"
									>
										{{ item.abreviacion + " - " + item.usuario }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-12 mt-2 col-12">
								<label class="label-title" for="documento">DOCUMENTO</label>
								<span
									v-if="submited && !$v.frmCanastaAsignacion.documento.required"
									class="span-error-message"
								>
									*
								</span>
								<input
									class="btn btn-primary ml-2"
									style="
										background-color: var(--plomoOscuroEmpresarial);
										border: none;
										font-size: 12px;
									"
									type="file"
									id="documento"
									@change="AgregarDocumento"
								/>
							</div>
						</div>

						<hr />
						<div class="text-right">
							<button class="btn btn-action btn-icon-split" @click="Asignar">
								<span class="icon text-white">
									<i class="fas fa-user-check"></i>
								</span>
								<span class="text">ASIGNAR</span>
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import InputNumber from "primevue/inputnumber/inputnumber.common";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value > 0;
export default {
	components: { headerCloseModal, DataTable, Column, InputNumber },
	props: {
		windowWidth: Number,
		windowHeigth: Number,
	},

	data() {
		return {
			submited: false,
			title_modal: null,
			agencias_fitradas: [],
			responsables_filtrados: [],
			frmCanastaAsignacion: {
				canasta_asignacion: [],
				agencia_id: 0,
				agencia: null,
				responsable_id: 0,
				documento: null,
			},
		};
	},
	validations: {
		frmCanastaAsignacion: {
			responsable_id: { noZero },
			documento: { required },
		},
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

		async FiltrarResponsables() {
			const params = {
				agencia_id: this.frmCanastaAsignacion.agencia_id,
				encargado_agencia: false,
			};

			await axios
				.get(route("log.responsables.por_agencia"), { params })
				.then((response) => {
					this.responsables_filtrados = response.data.responsables;
					this.frmCanastaAsignacion.responsable_id = 0;
				});
		},
		AgregarDocumento(e) {
			this.frmCanastaAsignacion.documento = e.target.files[0];
		},

		async Asignar() {
			this.submited = true;

			if (this.$v.frmCanastaAsignacion.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});
				return false;
			}
			Swal.fire({
				icon: "question",
				text: "¿DESEA CONTINUAR?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("agencia_id", this.frmCanastaAsignacion.agencia_id);
					data.append(
						"responsable_id",
						this.frmCanastaAsignacion.responsable_id
					);
					data.append("documento", this.frmCanastaAsignacion.documento);
					data.append(
						"canasta_asignacion",
						JSON.stringify(this.frmCanastaAsignacion.canasta_asignacion)
					);

					// this.$inertia.post(route("log.sum.asignacion.asignar"), data);
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("log.sum.asignacion.asignar"), data)
								.then((response) => {
									this.$parent.canasta_asignacion = [];
									this.$parent.ListarSuministros();
									$("#mdlCanastaAsignacion").css("display", "none");

									return Swal.fire({
										icon: "success",
										title: response.data.message,
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
		},
	},
};
</script>

<style lang="css">
.mdlCanastaAsignacion {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaAsignacion {
		margin-top: 20%;
	}
}
</style>
