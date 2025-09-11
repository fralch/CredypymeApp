<template>
	<layout ref="layout">
		<div class="slot_body slot-facturacion-limites" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LÍMITE ANUAL'"></headerClose>
					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div class="input-group row col-md-4 col-7" style="float: left">
							<div class="input-group-prepend">
								<span class="input-group-text">AÑO</span>
							</div>
							<select class="form-control center" @change="FiltrarLimite">
								<option :value="0" disabled selected>Seleccione...</option>
								<option
									v-for="(item, index) in años"
									:key="index"
									:value="item.año"
								>
									{{ item.año }}
								</option>
							</select>
						</div>
						<div class="row col-md-1 col-2 ml-1" style="float: left">
							<button
								class="btn btn-action btn-icon-split"
								@click="Nuevo"
								title="Nuevo LÍMITE"
							>
								<span class="icon text-white">
									<i class="fas fa-plus"></i>
								</span>
							</button>
						</div>
						<div class="col-md-5" style="float: left">
							<label
								class="label-title mt-1"
								style="font-size: 14px !important"
							>
								TOTAL LÍMITE: S/ {{ roundTo(total_limite, 2) }}</label
							>
						</div>

						<table class="table table-hover" id="tblLimites" width="100%">
							<thead>
								<tr>
									<th>AÑO</th>
									<th>MES</th>
									<th>LÍMITE_IDEAL</th>
									<th>TOTAL_EMITIDO</th>
									<th>RESTANTE</th>
									<th>LÍMITE_REAL</th>
									<th>COMPROBANTES_EMITIDOS</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in limites_filtrados" :key="index">
									<td class="table-bordered" align="center">
										{{ item.año }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.mes }}
									</td>
									<td class="table-bordered" align="right">
										S/ {{ roundTo(item.limite_ideal, 2) }}
									</td>
									<td class="table-bordered" align="right">
										S/ {{ roundTo(item.total_emitido, 2) }}
									</td>
									<td class="table-bordered" align="right">
										S/ {{ roundTo(item.restante, 2) }}
									</td>
									<td class="table-bordered" align="right">
										S/ {{ roundTo(item.limite_real, 2) }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.comprobantes_emitidos }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal" id="mdlNuevoLimite">
				<div class="modal-content w-20 mdlNuevoLimite">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'NUEVO LÍMITE'"
								:nombre_modal="'mdlNuevoLimite'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-md-6 col-6">
											<label class="label-title">AÑO</label>
											<span
												v-if="submited && !$v.frmDatosLimite.año.required"
												class="span-error-message"
											>
												*
											</span>
											<date-picker
												v-model="frmDatosLimite.año"
												class="center"
												type="year"
												:editable="false"
												value-type="format"
												format="YYYY"
												placeholder="Seleccione un año"
											></date-picker>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">MONTO</label>
											<span
												v-if="
													submited &&
													(!$v.frmDatosLimite.monto.required ||
														!$v.frmDatosLimite.monto.noZero)
												"
												class="span-error-message"
											>
												*
											</span>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">S/ </span>
												</div>
												<input
													class="form-control center"
													type="number"
													min="1"
													step="100"
													lang="en"
													v-model.number="frmDatosLimite.monto"
												/>
											</div>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar"
										title="Guardar LÍMITE"
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
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		DatePicker,
	},
	props: { limites: Array, años: Array },

	data() {
		return {
			submited: false,
			limites_filtrados: [],
			total_limite: 0,
			frmDatosLimite: {
				año: null,
				monto: null,
			},
		};
	},
	validations: {
		frmDatosLimite: {
			año: { required, noZero },
			monto: { required, noZero },
		},
	},

	watch: {
		limites_filtrados(value) {
			let self = this;
			$("#tblLimites").DataTable().destroy();
			this.TablaLimites();

			this.total_limite = 0;

			value.forEach((element) => {
				self.total_limite += parseFloat(element.limite_real);
			});
		},
	},

	mounted() {
		this.TablaLimites();
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
		TablaLimites() {
			this.$nextTick(() => {
				var table = $("#tblLimites").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					order: [[1, "asc"]],
					fixedHeader: true,
					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
					dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
					],
				});
			});
		},
		FiltrarLimite(e) {
			let año = e.target.value;

			this.limites_filtrados = this.limites.filter((item) => item.año == año);
		},
		Nuevo() {
			this.submited = false;
			this.frmDatosLimite.año = null;
			this.frmDatosLimite.monto = null;
			$("#mdlNuevoLimite").css("display", "block");
		},
		Guardar() {
			this.submited = true;
			self = this;
			if (this.$v.frmDatosLimite.$invalid) {
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
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						axios
							.post(route("man.fac_limites.verificar"), self.frmDatosLimite)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Los límites para este año, ya están registrados.",
										allowOutsideClick: false,
									});
									return false;
								} else {
									self.$inertia.post(
										route("man.fac_limites.guardar"),
										self.frmDatosLimite,
										{
											preserveScroll: true,
											onStart: (visit) => {
												let timerInterval;
												Swal.fire({
													title: "EN PROGRESO",
													html: "Espere porfavor...",
													timer: 5000,
													allowOutsideClick: false,
													timerProgressBar: true,
													didOpen: () => {
														Swal.showLoading();
														timerInterval = setInterval(() => {
															const content = Swal.getContent();
															if (content) {
																const b = content.querySelector("b");
																if (b) {
																	b.textContent = Swal.getTimerLeft();
																}
															}
														}, 100);
													},
													willClose: () => {
														clearInterval(timerInterval);
													},
												});
											},
											onSuccess: () => {
												Swal.fire({
													icon: "success",
													title: "¡ÉXITO!",
													allowOutsideClick: false,
													preConfirm: (result) => {
														self.submited = false;
														$("#mdlNuevoLimite").css("display", "none");
													},
												});
											},
										}
									);
								}
							});
					} else {
						return false;
					}
				});
			}
		},
	},
};
</script>

<style lang="css">
.slot-facturacion-limites {
	width: 40% !important;
	margin-left: 30% !important;
}

@media (max-width: 900px) {
	.slot-facturacion-limites {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
