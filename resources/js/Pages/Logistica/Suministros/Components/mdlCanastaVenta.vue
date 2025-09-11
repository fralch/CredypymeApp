<template>
	<div class="modal" id="mdlCanastaVenta">
		<div class="modal-content w-50 mdlCanastaVenta">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'VENDER SUMINISTROS'"
						:nombre_modal="'mdlCanastaVenta'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLES</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-12">
								<label class="label-title">SUMINISTROS SELECCIONADOS</label>

								<DataTable
									:value="frmCanastaVenta.canasta_venta"
									:scrollable="true"
									scrollDirection="both"
									:scrollHeight="String(windowHeigth * 0.49) + 'px'"
									showGridlines
								>
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
												v-model.number="data.cantidad_vender"
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
										field="valor_venta"
										header="VALOR_VENTA"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
										><template #body="{ data }">
											<InputNumber
												v-model.number="data.valor_venta"
												mode="decimal"
												locale="en-US"
												:maxFractionDigits="2"
												:minFractionDigits="2"
												:step="0.1"
												:min="0.1"
											/>
										</template>
									</Column>
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
										field="medicion"
										header="MEDICIÓN"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									></Column>

									<Column
										field="condicion"
										header="CONDICIÓN"
										:styles="{
											width: '100px',
											justifyContent: 'center',
										}"
									>
									</Column
									><Column
										field="codigo"
										header="CÓDIGO"
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
									:value="frmCanastaVenta.agencia"
									disabled
								/>
							</div>
							<div class="form-group col-md-4 col-7">
								<label class="label-title">APELLIDO PATERNO</label>

								<input
									type="text"
									class="form-control mayus"
									:class="[
										submited
											? $v.frmCanastaVenta.comprador.apellido_paterno.$invalid
												? 'is-invalid'
												: 'is-valid'
											: '',
									]"
									maxlength="20"
									v-model="frmCanastaVenta.comprador.apellido_paterno"
								/>
							</div>
							<div class="form-group col-md-4 col-7">
								<label class="label-title">APELLIDO MATERNO</label>

								<input
									type="text"
									class="form-control mayus"
									:class="[
										submited
											? $v.frmCanastaVenta.comprador.apellido_materno.$invalid
												? 'is-invalid'
												: 'is-valid'
											: '',
									]"
									maxlength="20"
									v-model="frmCanastaVenta.comprador.apellido_materno"
								/>
							</div>
							<div class="form-group col-md-8 col-5">
								<label class="label-title">NOMBRES</label>

								<input
									type="text"
									class="form-control mayus"
									:class="[
										submited
											? $v.frmCanastaVenta.comprador.nombres.$invalid
												? 'is-invalid'
												: 'is-valid'
											: '',
									]"
									maxlength="50"
									v-model="frmCanastaVenta.comprador.nombres"
								/>
							</div>
							<div class="form-group col-md-4 col-4">
								<label class="label-title">DNI</label>
								<input
									type="text"
									class="form-control center"
									maxlength="8"
									v-model="frmCanastaVenta.comprador.dni"
								/>
							</div>

							<div class="form-group col-md-12 col-8">
								<label class="label-title" for="documento"
									>DOCUMENTO(opcional)
								</label>

								<input
									class="btn btn-primary"
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
							<button class="btn btn-action btn-icon-split" @click="Registrar">
								<span class="icon text-white">
									<i class="fas fa-check"></i>
								</span>
								<span class="text">VENDER</span>
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

			frmCanastaVenta: {
				canasta_venta: [],
				agencia_id: 0,
				agencia: null,
				comprador: {
					apellido_paterno: null,
					apellido_materno: null,
					nombres: null,
					dni: null,
				},
				documento: null,
			},
		};
	},

	validations: {
		frmCanastaVenta: {
			comprador: {
				apellido_paterno: { required },
				apellido_materno: { required },
				nombres: { required },
			},
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

		AgregarDocumento(e) {
			this.frmCanastaVenta.documento = e.target.files[0];
		},

		async Registrar() {
			this.submited = true;

			if (this.$v.frmCanastaVenta.$invalid) {
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

					data.append("agencia_id", this.frmCanastaVenta.agencia_id);
					data.append(
						"comprador",
						JSON.stringify(this.frmCanastaVenta.comprador)
					);
					data.append("documento", this.frmCanastaVenta.documento);
					data.append(
						"canasta_venta",
						JSON.stringify(this.frmCanastaVenta.canasta_venta)
					);

					//this.$inertia.post(route("log.sum.venta.vender"), data);
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("log.sum.venta.vender"), data)
								.then((response) => {
									this.$parent.canasta_venta = [];
									this.$parent.ListarSuministros();
									$("#mdlCanastaVenta").css("display", "none");

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
.mdlCanastaVenta {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaVenta {
		margin-top: 20%;
	}
}
</style>
