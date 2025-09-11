<template>
	<div id="mdlCanastaEnvio" class="modal">
		<div class="modal-content w-50 mdlCanastaEnvio">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlCanastaEnvio'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLE</div>
					<div class="card-body card-block">
						<label class="label-title">SUMINISTROS A ENVIAR</label>
						<span
							v-if="submited && !$v.frmCanastaEnvios.canasta_envio.required"
							class="span-error-message"
						>
							*
						</span>

						<DataTable
							:value="frmCanastaEnvios.canasta_envio"
							:scrollable="true"
							scrollDirection="both"
							:scrollHeight="String(windowHeigth * 0.4) + 'px'"
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
										v-model.number="data.cantidad_envio"
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
								field="agencia"
								header="AGENCIA"
								:styles="{
									width: '120px',
									justifyContent: 'center',
								}"
							>
							</Column>
						</DataTable>

						<div class="form-row mt-3">
							<div class="form-group col-md-4 col-5">
								<label class="label-title">AGENCIA DESTINO</label>
								<span
									v-if="
										submited && !$v.frmCanastaEnvios.agencia_recepcion.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control center mayus"
									v-model="frmCanastaEnvios.agencia_recepcion"
									@change="FiltrarResponsables"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in agencias_filtradas"
										:key="index"
										:value="item.id"
									>
										{{ item.agencia }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-6 col-7">
								<label class="label-title">RESPONSABLE RECEPCIÓN</label>
								<span
									v-if="
										submited &&
										!$v.frmCanastaEnvios.responsable_recepcion.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control"
									v-model="frmCanastaEnvios.responsable_recepcion"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in responsables_filtrado"
										:key="index"
										:value="item.id"
									>
										{{ item.abreviacion + " - " + item.usuario }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-12 mt-2 col-12">
								<label class="label-title" for="documento_envio"
									>DOCUMENTO</label
								>
								<span
									v-if="
										submited && !$v.frmCanastaEnvios.documento_envio.required
									"
									class="span-error-message"
								>
									*
								</span>
								<input
									class="btn btn-primary"
									style="
										background-color: var(--plomoOscuroEmpresarial);
										border: none;
										font-size: 12px;
									"
									type="file"
									id="documento_envio"
									@change="AgregarDocumento"
								/>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button class="btn btn-action btn-icon-split" @click="Enviar">
								<span class="icon text-white">
									<i class="fas fa-paper-plane"></i>
								</span>
								<span class="text">ENVIAR</span>
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
		agencias: Array,
		agencia_seleccionada: Number,
		windowWidth: Number,
		windowHeigth: Number,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			responsables_filtrado: [],
			frmCanastaEnvios: {
				canasta_envio: [],
				documento_envio: null,
				agencia_recepcion: 0,
				responsable_recepcion: 0,
			},
		};
	},
	validations: {
		frmCanastaEnvios: {
			canasta_envio: { required },
			documento_envio: { required },
			agencia_recepcion: { noZero },
			responsable_recepcion: { noZero },
		},
	},
	computed: {
		agencias_filtradas() {
			let lista = [];
			this.agencias.forEach((element) => {
				if (element.id != this.agencia_seleccionada) {
					lista.push(element);
				}
			});
			return lista;
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
			this.frmCanastaEnvios.documento_envio = e.target.files[0];
		},
		async FiltrarResponsables() {
			const params = {
				agencia_id: this.frmCanastaEnvios.agencia_recepcion,
				encargado_agencia: true,
			};

			// this.$inertia.get(route("log.responsables.por_agencia", params));
			// return false;

			return await axios
				.get(route("log.responsables.por_agencia"), { params })
				.then((response) => {
					this.responsables_filtrado = response.data.responsables;
					this.frmCanastaEnvios.responsable_recepcion = 0;
				});
		},
		async Enviar() {
			this.submited = true;

			if (this.$v.frmCanastaEnvios.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});
				return false;
			} else {
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
						data.append("agencia_envio", this.agencia_seleccionada);
						data.append(
							"agencia_recepcion",
							this.frmCanastaEnvios.agencia_recepcion
						);
						data.append(
							"responsable_recepcion",
							this.frmCanastaEnvios.responsable_recepcion
						);
						data.append(
							"documento_envio",
							this.frmCanastaEnvios.documento_envio
						);
						data.append(
							"canasta_envio",
							JSON.stringify(this.frmCanastaEnvios.canasta_envio)
						);

						// this.$inertia.post(route("log.sum.almacen.enviar"), data);
						// return false;

						Swal.fire({
							title: "REGISTRANDO",
							showConfirmButton: false,
							allowOutsideClick: false,
							willOpen: async () => {
								Swal.showLoading();

								return await axios
									.post(route("log.sum.almacen.enviar"), data)
									.then((response) => {
										this.$parent.canasta_envio = [];
										this.$parent.ListarSuministros();
										$("#mdlCanastaEnvio").css("display", "none");
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
			}
		},
	},
};
</script>

<style lang="css">
.mdlCanastaEnvio {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaEnvio {
		margin-top: 20%;
	}
}
</style>
