<template>
	<div class="modal" id="mdlCanastaDevolucion">
		<div class="modal-content w-50 mdlCanastaDevolucion">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlCanastaDevolucion'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLES</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-12">
								<label class="label-title">SUMINISTROS SELECCIONADOS</label>

								<DataTable
									:value="frmCanastaDevolucion.canasta_devolucion"
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
												v-model.number="data.cantidad_devolucion"
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
										field="condicion_devolucion"
										header="CONDICIÓN"
										:styles="{
											width: '110px',
											justifyContent: 'center',
										}"
										><template #body="{ data, index }">
											<select
												class="form-control center"
												v-model="data.condicion_devolucion"
												@change="ValorUsado(data, index)"
												:disabled="modo == 'baja'"
											>
												<option
													v-for="(item_1, index) in condiciones"
													:key="index"
													:value="item_1.id"
												>
													{{ item_1.condicion }}
												</option>
											</select>
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
										header="CANTIDAD"
										:styles="{
											width: '70px',
											justifyContent: 'center',
										}"
										><template #body="{ data }">{{
											RedondearVista(data.cantidad, 2)
										}}</template>
									</Column>

									<Column
										field="valor_devolucion"
										header="VALOR_UNITARIO"
										:styles="{
											width: '100px',
											justifyContent: 'right',
										}"
										><template #body="{ data }"
											>S/
											{{ RedondearVista(data.valor_devolucion, 2) }}</template
										>
									</Column>
									<Column
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
									:value="frmCanastaDevolucion.agencia"
									disabled
								/>
							</div>
							<div class="form-group col-md-6 col-7">
								<label class="label-title">RESPONSABLE</label>
								<input
									type="text"
									class="form-control center"
									:value="frmCanastaDevolucion.responsable"
									disabled
								/>
							</div>
							<div class="form-group col-md-6 col-7" v-show="modo == 'baja'">
								<label class="label-title">OBSERVACIÓN DE BAJA</label>
								<textarea
									type="text"
									rows="2"
									class="form-control mayus text-row"
									v-model="frmCanastaDevolucion.observacion"
								>
								</textarea>
							</div>
							<div class="form-group col-md-12 mt-2 col-12">
								<label class="label-title">DOCUMENTO(opcional)</label>
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
							<button
								class="btn btn-action btn-icon-split"
								@click="Registrar"
								title="Devolver SUMINISTROS"
								v-if="modo == 'devolucion'"
							>
								<span class="icon text-white">
									<i class="fas fa-check"></i>
								</span>
								<span class="text">DEVOLVER</span>
							</button>
							<button
								class="btn btn-danger btn-icon-split"
								@click="Registrar"
								title="Bajar SUMINISTROS"
								v-if="modo == 'baja'"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
								<span class="text">DAR DE BAJA</span>
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
		condiciones: Array,
	},

	data() {
		return {
			submited: false,
			title_modal: null,
			agencia: null,
			responsable: null,
			modo: null,
			frmCanastaDevolucion: {
				canasta_devolucion: [],
				agencia_id: 0,
				documento: null,
				observacion: null,
			},
		};
	},

	validations: {
		frmCanastaDevolucion: {
			canasta_devolucion: { required },
		},
	},

	methods: {
		roundTo(value, decimal_places) {
			let valor = 0.1;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},

		RedondearVista(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			if (valor == 0) {
				return "-";
			} else {
				return parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
		},
		AgregarDocumento(e) {
			this.frmCanastaDevolucion.documento = e.target.files[0];
		},
		ValorUsado(item) {
			const condicion = this.condiciones.find(
				(e) => e.id === item.condicion_devolucion
			)?.condicion;

			switch (condicion) {
				case "BUENO":
					item.valor_devolucion = this.roundTo(item.valor_unitario, 2);
					break;
				case "REGULAR":
					item.valor_devolucion = this.roundTo(item.valor_unitario / 2, 2);
					break;
				case "MALO":
					item.valor_devolucion = this.roundTo(item.valor_unitario / 3, 2);
					break;
				case "OBSOLETO":
					item.valor_devolucion = this.roundTo(0.1, 2);
					break;
			}
		},
		async Registrar() {
			this.submited = true;

			if (this.$v.frmCanastaDevolucion.$invalid) {
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
						data.append("agencia_id", this.frmCanastaDevolucion.agencia_id);
						data.append("documento", this.frmCanastaDevolucion.documento);
						data.append("observacion", this.frmCanastaDevolucion.observacion);
						data.append("modo", this.modo);
						data.append(
							"canasta_devolucion",
							JSON.stringify(this.frmCanastaDevolucion.canasta_devolucion)
						);

						// this.$inertia.post(route("log.sum.devolucion.devolver"), data);
						// return false;

						Swal.fire({
							title: "REGISTRANDO",
							showConfirmButton: false,
							allowOutsideClick: false,
							willOpen: async () => {
								Swal.showLoading();

								try {
									const response = await axios.post(
										route("log.sum.devolucion.devolver"),
										data
									);

									await this.$parent.Buscar();
									$("#mdlCanastaDevolucion").css("display", "none");

									// Espera un poco para asegurar cierre suave del primer modal
									await Swal.close();

									return Swal.fire({
										icon: "success",
										title: "¡Listo!",
										showConfirmButton: false,
										timer: 1500,
									});
								} catch (error) {
									console.error(error);
									await Swal.close(); // cierra primero el anterior

									Swal.fire({
										icon: "error",
										title: "Error",
										text: `Ha ocurrido un error. Comunicar a SOPORTE: ${error.message}`,
									});
								}
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
.mdlCanastaDevolucion {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaDevolucion {
		margin-top: 20%;
	}
}
</style>
