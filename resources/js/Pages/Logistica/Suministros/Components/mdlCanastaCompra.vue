<template>
	<div id="mdlCanastaCompra" class="modal">
		<div class="modal-content w-40 mdlCanastaCompra">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlCanastaCompra'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLE</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-6">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<input
										type="text"
										class="form-control center"
										:value="agencia_compra"
										disabled
									/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split mb-1"
										data-toggle="modal"
										@click="Agregar()"
									>
										<span class="icon">
											<i class="fas fa-plus" style="color: white"></i>
										</span>
										<span class="text">AGREGAR</span>
									</button>
									<span
										v-if="submited && !$v.frmCanastaCompras.canasta.required"
										class="span-error-message"
									>
										*
									</span>
								</div>
							</div>
						</div>

						<DataTable
							:value="frmCanastaCompras.canasta"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="200px"
							showGridlines
						>
							<Column
								field="acciones"
								header="ACCIONES"
								:styles="{ width: '90px', justifyContent: 'center' }"
								frozen
							>
								<template #body="{ data, index }">
									<div class="btn-group" role="group">
										<button
											class="btn btn-danger"
											type="button"
											@click="Quitar(index)"
											title="Quitar SUMINISTRO"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
										<button
											class="btn btn-cancel"
											type="button"
											@click="Editar(data, index)"
											title="Editar SUMINISTRO"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</div>
								</template>
							</Column>

							<Column
								field="codigo"
								header="CÓDIGO"
								:styles="{ width: '90px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="suministro"
								header="SUMINISTRO"
								:styles="{ width: '150px' }"
							>
							</Column>
							<Column
								field="condicion"
								header="CONDICIÓN"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="cantidad_compra"
								header="CANTIDAD"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="medicion"
								header="MEDICIÓN"
								:styles="{ width: '90px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="valor_unitario"
								header="V_UNITARIO(S/)"
								:styles="{ width: '90px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ parseFloat(data.valor_unitario).toFixed(2) }}
								</template>
							</Column>
							<Column
								field="valor_total"
								header="V_TOTAL(S/)"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ parseFloat(data.valor_total).toFixed(2) }}
								</template>
							</Column>
							<Column
								field="clasificacion"
								header="CLASIFICACIÓN"
								:styles="{ width: '120px', justifyContent: 'center' }"
							>
							</Column>
							<template #empty> No hay SUMINISTROS añadidos.</template>
						</DataTable>
						<br />
						<form>
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="label-title" for="myfile">COMPROBANTE</label>

									<span
										v-if="submited && !$v.frmCanastaCompras.documento.required"
										class="span-error-message"
									>
										*
									</span>

									<input
										class="btn btn-primary"
										style="
											background-color: var(--plomoOscuroEmpresarial);
											border: none;
											max-width: 400px;
											font-size: var(--tamañoLetraLabels);
										"
										type="file"
										id="documentoCompra"
										accept="image/*"
										@change="AgregarDocumento"
									/>
								</div>
								<div class="form-group col-md-4 offset-md-4 col-6">
									<label class="label-title">USUARIO DE COMPRA</label>

									<select
										class="form-control center"
										id="slcAgenciasCompra"
										@change="FiltrarUsuarios"
									>
										<option :value="0" disabled selected>Seleccione...</option>
										<option
											v-for="(item, index) in agencias"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
									<select
										class="form-control center"
										v-model="frmCanastaCompras.usuario_compra"
										:class="[
											submited
												? $v.frmCanastaCompras.usuario_compra.$invalid
													? 'is-invalid'
													: 'is-valid'
												: '',
										]"
									>
										<option :value="0" disabled>Seleccione...</option>
										<option
											v-for="(item, index) in usuarios_filtrados"
											:key="index"
											:value="item.dni"
										>
											{{ item.usuario }}
										</option>
									</select>
								</div>
							</div>
						</form>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="Registrar"
								title="Registrar COMPRA"
							>
								<span class="icon text-white">
									<i class="fas fa-save"></i>
								</span>
								<span class="text">REGISTRAR</span>
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

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value > 0;
export default {
	components: { headerCloseModal, DataTable, Column },
	props: {
		tipo_modulo: String,
		usuarios: Array,
		agencias: Array,
		agencia_id: Number,
		tipos: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			usuarios_filtrados: [],

			frmCanastaCompras: {
				modo: "AGREGAR_NUEVO",
				canasta: [],
				documento: null,
				usuario_compra: null,
			},
		};
	},
	computed: {
		canasta() {
			return this.frmCanastaCompras.canasta;
		},
		agencia_compra() {
			if (this.agencia_id != 0) {
				const agencia = this.agencias.find(
					(item) => item.id == this.agencia_id
				);
				return agencia.agencia;
			} else {
				return null;
			}
		},
	},
	validations: {
		frmCanastaCompras: {
			canasta: { required },
			documento: { required },
			usuario_compra: { noZero },
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
		ActualizarCodigos() {
			let self = this;
			this.tipos.forEach((element_1) => {
				axios
					.post(route("log.sum.almacen.obtener_codigo"), {
						tipo_id: element_1.id,
					})
					.then(function (response) {
						let orden = response.data + 1;
						let numero_orden = String(orden);
						let primera_letra = element_1.tipo.substr(0, 3);
						let nuevo_codigo =
							primera_letra +
							Array(7 - numero_orden.length).join("0") +
							numero_orden;
						self.frmCanastaCompras.canasta.forEach((element_2) => {
							if (element_2.tipo_id == element_1.id) {
								element_2.codigo = nuevo_codigo;
								orden + 1;
							}
						});
					});
			});
		},
		Agregar() {
			let mdlDatosSuministro = this.$parent.$refs.mdlDatosSuministro;

			mdlDatosSuministro.submited = false;
			mdlDatosSuministro.title_modal = "NUEVA COMPRA";
			mdlDatosSuministro.frmDatosSuministro.modo = "AGREGAR_NUEVO";

			mdlDatosSuministro.frmDatosSuministro.codigo = null;
			mdlDatosSuministro.frmDatosSuministro.suministro = null;
			mdlDatosSuministro.frmDatosSuministro.marca = null;
			mdlDatosSuministro.frmDatosSuministro.detalle = null;
			mdlDatosSuministro.frmDatosSuministro.tipo_id = 0;
			mdlDatosSuministro.frmDatosSuministro.agencia_id = this.agencia_id;
			mdlDatosSuministro.frmDatosSuministro.condicion_id = 0;
			mdlDatosSuministro.frmDatosSuministro.clasificacion = "SUMINISTRO";
			mdlDatosSuministro.frmDatosSuministro.medicion_id = 0;
			mdlDatosSuministro.frmDatosSuministro.cantidad_actual = this.roundTo(
				0,
				2
			);

			mdlDatosSuministro.frmDatosSuministro.cantidad_compra = this.roundTo(
				1,
				2
			);
			mdlDatosSuministro.frmDatosSuministro.valor_total = this.roundTo(1, 2);
			mdlDatosSuministro.frmDatosSuministro.valor_unitario = this.roundTo(1, 2);
			mdlDatosSuministro.frmDatosSuministro.igv = this.roundTo(18, 2);

			mdlDatosSuministro.frmDatosSuministro.valor_total_sigv = this.roundTo(
				1 - 0.18,
				2
			);
			mdlDatosSuministro.frmDatosSuministro.valor_unitario_sigv = this.roundTo(
				1 - 0.18,
				2
			);

			$("#mdlDatosSuministro").css("display", "block");
			$("#datosSuministro1-tab").tab("show");
		},
		Quitar(index) {
			Swal.fire({
				icon: "question",
				text: "¿DESEA QUITAR ESTE ELEMENTO?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then(async (result) => {
				if (result.isConfirmed) {
					this.frmCanastaCompras.canasta.splice(index, 1);
					await this.ActualizarCodigos();
				} else {
					return false;
				}
			});
		},
		Editar(item, index) {
			let mdlDatosSuministro = this.$parent.$refs.mdlDatosSuministro;
			mdlDatosSuministro.submited = false;
			mdlDatosSuministro.title_modal = "EDITAR COMPRA";
			mdlDatosSuministro.frmDatosSuministro.modo = "EDITAR_NUEVO";
			mdlDatosSuministro.frmDatosSuministro.index = index;
			mdlDatosSuministro.frmDatosSuministro.codigo = item.codigo;
			mdlDatosSuministro.frmDatosSuministro.suministro = item.suministro;
			mdlDatosSuministro.frmDatosSuministro.marca = item.marca;
			mdlDatosSuministro.frmDatosSuministro.detalle = item.detalle;
			mdlDatosSuministro.frmDatosSuministro.medicion_id = item.medicion_id;
			mdlDatosSuministro.frmDatosSuministro.tipo_id = item.tipo_id;
			mdlDatosSuministro.frmDatosSuministro.agencia_id = item.agencia_id;
			mdlDatosSuministro.frmDatosSuministro.proveedor_id = item.proveedor_id;
			mdlDatosSuministro.frmDatosSuministro.condicion_id = item.condicion_id;
			mdlDatosSuministro.frmDatosSuministro.clasificacion = item.clasificacion;

			mdlDatosSuministro.frmDatosSuministro.cantidad_compra =
				item.cantidad_compra;
			mdlDatosSuministro.frmDatosSuministro.valor_total = item.valor_total;
			mdlDatosSuministro.frmDatosSuministro.igv = item.igv;

			mdlDatosSuministro.CalcularIGV(null);

			$("#mdlDatosSuministro").css("display", "block");
			$("#datosSuministro1-tab").tab("show");
		},
		AgregarDocumento(e) {
			this.frmCanastaCompras.documento = e.target.files[0];
		},
		FiltrarUsuarios() {
			let agencia_id = $("#slcAgenciasCompra").val();

			this.usuarios_filtrados = this.usuarios.filter(
				(item) => item.agencia_id == agencia_id
			);

			this.frmCanastaCompras.usuario_compra = 0;
		},

		async Registrar() {
			this.submited = true;
			if (this.$v.frmCanastaCompras.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});

				return false;
			}

			Swal.fire({
				title: "¿Desea continuar?",
				confirmButtonText: "SI",
				showCancelButton: true,
				cancelButtonText: "NO",
				allowOutsideClick: false,
				backdrop: true,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("modo", this.frmCanastaCompras.modo);
					data.append(
						"canasta",
						JSON.stringify(this.frmCanastaCompras.canasta)
					);
					data.append("documento", this.frmCanastaCompras.documento);
					data.append("usuario_compra", this.frmCanastaCompras.usuario_compra);

					// this.$inertia.post(route("log.sum.almacen.comprar"), data);
					// return false;

					Swal.fire({
						title: "REGISTRANDO",
						showConfirmButton: false,
						allowOutsideClick: false,
						willOpen: async () => {
							Swal.showLoading();

							return await axios
								.post(route("log.sum.almacen.comprar"), data)
								.then((response) => {
									this.frmCanastaCompras.canasta = [];
									$("#documentoCompra").val();
									this.frmCanastaCompras.documento = null;
									$("#slcAgenciasCompra").val(0);
									this.FiltrarUsuarios();

									this.$parent.ListarSuministros();
									$("#mdlCanastaCompra").css("display", "none");

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
.mdlCanastaCompra {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaCompra {
		margin-top: 20%;
	}
}
</style>
