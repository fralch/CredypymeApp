<template>
	<div class="modal" id="mdlCanastaVenta">
		<div class="modal-content w-40 mdlCanastaVenta">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlCanastaVenta'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLE</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-12">
								<label class="label-title">ACTIVOS SELECCIONADOS</label>
								<span
									v-if="submited && !$v.frmCanastaVenta.canasta_venta.required"
									class="span-error-message"
								>
									*
								</span>
								<table class="table table-hover" id="tblCanastaVenta">
									<thead>
										<tr>
											<th>QUITAR</th>
											<th>VALOR_VENTA</th>
											<th>CÓDIGO_DE_ACTIVO</th>
											<th>RESPONSABLE</th>
											<th>UBICACIÓN</th>
											<th>VALOR_ACTUAL(S/)</th>
											<th>CONDICIÓN</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in frmCanastaVenta.canasta_venta"
											:key="index"
										>
											<td class="table-bordered" align="center" width="75px">
												<button
													class="btn btn-danger btn-icon-split"
													@click="Quitar(index)"
													title="Quitar ACTIVO"
												>
													<span class="icon text-white">
														<i class="fas fa-trash-alt"></i>
													</span>
												</button>
											</td>
											<td class="table-bordered" align="center">
												<input
													class="form-control ml-1 center"
													type="number"
													step="0.1"
													min="0.1"
													style="width: 100px !important"
													v-model.number="item.valor_venta"
													@change="Redondear"
													:id="item.id"
												/>
											</td>
											<td class="table-bordered">
												{{ item.codigo }}
											</td>
											<td class="table-bordered" align="center">
												{{
													item.abreviacion_responsable +
													" - " +
													item.usuario_responsable
												}}
											</td>

											<td class="table-bordered" align="center">
												{{
													item.abreviacion_ubicacion +
													" - " +
													item.agencia_ubicacion
												}}
											</td>
											<td class="table-bordered" align="center">
												{{ roundTo(item.valor_actual, 2) }}
											</td>
											<td class="table-bordered" align="center">
												{{ item.condicion }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="form-row mt-3">
							<div class="form-group col-md-4 col-5">
								<label class="label-title">AGENCIA</label>
								<input
									type="text"
									class="form-control center mayus"
									:value="frmCanastaVenta.agencia"
									readonly
								/>
							</div>
							<div class="form-group col-md-4 col-7">
								<label class="label-title">APELLIDO PATERNO</label>
								<span
									v-if="
										submited &&
										!$v.frmCanastaVenta.comprador.apellido_paterno.required
									"
									class="span-error-message"
								>
									*
								</span>
								<input
									type="text"
									class="form-control mayus"
									maxlength="20"
									v-model="frmCanastaVenta.comprador.apellido_paterno"
								/>
							</div>
							<div class="form-group col-md-4 col-7">
								<label class="label-title">APELLIDO MATERNO</label>
								<span
									v-if="
										submited &&
										!$v.frmCanastaVenta.comprador.apellido_materno.required
									"
									class="span-error-message"
								>
									*
								</span>
								<input
									type="text"
									class="form-control mayus"
									maxlength="20"
									v-model="frmCanastaVenta.comprador.apellido_materno"
								/>
							</div>
							<div class="form-group col-md-8 col-5">
								<label class="label-title">NOMBRES</label>
								<span
									v-if="
										submited && !$v.frmCanastaVenta.comprador.nombres.required
									"
									class="span-error-message"
								>
									*
								</span>
								<input
									type="text"
									class="form-control mayus"
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
								<label class="label-title">DOCUMENTO(opcional)</label>

								<input
									class="btn btn-primary"
									style="
										background-color: var(--plomoOscuroEmpresarial);
										border: none;
										font-size: var(--tamañoLetraLabels);
									"
									type="file"
									id="documento"
									@change="AgregarDocumento"
								/>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button class="btn btn-action btn-icon-split" @click="Guardar">
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

import { required } from "vuelidate/lib/validators";

export default {
	components: { headerCloseModal },
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
			canasta_venta: { required },
			comprador: {
				apellido_paterno: { required },
				apellido_materno: { required },
				nombres: { required },
			},
		},
	},

	mounted() {
		this.TablaCanastaVenta();
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

		Redondear(e) {
			let id = e.target.id;
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value) {
				valor = e.target.value;
			}

			this.frmCanastaVenta.canasta_venta.filter(
				(item) => item.id == id
			)[0].valor_venta = parseFloat(valor).toFixed(numero_decimales);
		},
		ActualizarTabla() {
			$("#tblCanastaVenta").DataTable().destroy();
			this.TablaCanastaVenta();
		},

		TablaCanastaVenta() {
			this.$nextTick(() => {
				let table = $("#tblCanastaVenta").DataTable({
					scrollY: "300px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,

					order: [],
					fixedHeader: true,
					info: false,
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
				});
			});
		},

		Quitar(index) {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿DESEA QUITAR ESTE ELEMENTO?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then(async (result) => {
				if (result.isConfirmed) {
					self.frmCanastaVenta.canasta_venta.splice(index, 1);
					await self.ActualizarTabla();
				} else {
					return false;
				}
			});
		},
		AgregarDocumento(e) {
			this.frmCanastaVenta.documento = e.target.files[0];
		},

		Guardar() {
			let self = this;
			this.submited = true;

			if (this.$v.frmCanastaVenta.$invalid) {
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
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						let data = new FormData();

						data.append("agencia_id", self.frmCanastaVenta.agencia_id);
						data.append(
							"comprador",
							JSON.stringify(self.frmCanastaVenta.comprador)
						);
						data.append("documento", self.frmCanastaVenta.documento);
						let lista_venta = [];
						self.frmCanastaVenta.canasta_venta.forEach((element) => {
							let obj = {
								id: element.id,
								valor_actual: element.valor_actual,
								valor_venta: element.valor_venta,
							};
							lista_venta.push(obj);
						});

						data.append("canasta_venta", JSON.stringify(lista_venta));

						self.$inertia.post(route("log.act.venta.vender"), data, {
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "ESPERE POR FAVOR...",
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
										self.$parent.$parent.canasta_venta = [];
										self.$parent.$parent.ListarActivos();
										$("#mdlCanastaVenta").css("display", "none");
									},
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

.mdlCanastaVenta {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaVenta {
		margin-top: 20%;
	}
}
</style>
