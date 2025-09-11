<template>
	<div class="modal" id="mdlCanastaAsignacion">
		<div class="modal-content w-40 mdlCanastaAsignacion">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'ASIGNAR ACTIVOS'"
						:nombre_modal="'mdlCanastaAsignacion'"
					>
					</headerCloseModal>
					<div class="card-title">DETALLES</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-12">
								<label class="label-title">ACTIVOS SELECCIONADOS</label>
								<span
									v-if="
										submited &&
										!$v.frmCanastaAsignacion.canasta_asignacion.required
									"
									class="span-error-message"
								>
									*
								</span>
								<table class="table table-hover" id="tblCanastaAsignacion">
									<thead>
										<tr>
											<th>QUITAR</th>
											<th>CÓDIGO_DE_ACTIVO</th>
											<th>RESPONSABLE</th>
											<th>UBICACIÓN</th>
											<th>VALOR_ACTUAL(S/)</th>
											<th>CONDICIÓN</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(
												item, index
											) in frmCanastaAsignacion.canasta_asignacion"
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
									:value="frmCanastaAsignacion.agencia"
									readonly
								/>
							</div>
							<div class="form-group col-md-8 col-7">
								<label class="label-title" for="documento"
									>DOCUMENTO(opcional)</label
								>

								<input
									class="btn btn-primary"
									style="
										background-color: var(--plomoOscuroEmpresarial);
										border: none;
										font-size: 12px;
										width: 100% !important;
									"
									type="file"
									id="documento"
									@change="AgregarDocumento"
								/>
							</div>
							<div class="form-group col-md-4 col-5">
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
									class="form-control"
									v-model.number="frmCanastaAsignacion.responsable_id"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in responsables"
										:key="index"
										:value="item.id"
									>
										{{ item.abreviacion + " - " + item.usuario }}
									</option>
								</select>
							</div>

							<div class="form-group col-md-4 col-7">
								<label class="label-title">UBICACIÓN</label>
								<span
									v-if="
										submited && !$v.frmCanastaAsignacion.ubicacion_id.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control"
									v-model.number="frmCanastaAsignacion.ubicacion_id"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in ubicaciones"
										:key="index"
										:value="item.id"
									>
										{{ item.abreviacion + " - " + item.agencia }}
									</option>
								</select>
							</div>
						</div>

						<hr />
						<div class="text-right">
							<button class="btn btn-action btn-icon-split" @click="Guardar">
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

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: { headerCloseModal },
	props: {
		tipo_modulo: String,
		responsables: Array,
		ubicaciones: Array,
	},

	data() {
		return {
			submited: false,
			title_modal: null,
			agencias_fitradas: [],
			frmCanastaAsignacion: {
				canasta_asignacion: [],
				agencia_id: 0,
				agencia: null,
				responsable_id: 0,
				ubicacion_id: 0,
				documento: null,
			},
		};
	},

	validations: {
		frmCanastaAsignacion: {
			canasta_asignacion: { required },
			responsable_id: { noZero },
			ubicacion_id: { noZero },
		},
	},

	mounted() {
		this.TablaCanastaAsignacion();
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

		ActualizarTabla() {
			$("#tblCanastaAsignacion").DataTable().destroy();
			this.TablaCanastaAsignacion();
		},

		TablaCanastaAsignacion() {
			this.$nextTick(() => {
				let table = $("#tblCanastaAsignacion").DataTable({
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
					self.frmCanastaAsignacion.canasta_asignacion.splice(index, 1);
					await self.ActualizarTabla();
				} else {
					return false;
				}
			});
		},
		AgregarDocumento(e) {
			this.frmCanastaAsignacion.documento = e.target.files[0];
		},

		Guardar() {
			let self = this;
			this.submited = true;

			if (this.$v.frmCanastaAsignacion.$invalid) {
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
						data.append("tipo_modulo", self.tipo_modulo);
						data.append("agencia_id", self.frmCanastaAsignacion.agencia_id);
						data.append(
							"responsable_id",
							self.frmCanastaAsignacion.responsable_id
						);
						data.append("ubicacion_id", self.frmCanastaAsignacion.ubicacion_id);
						data.append("documento", self.frmCanastaAsignacion.documento);
						let lista_asignacion = [];
						self.frmCanastaAsignacion.canasta_asignacion.forEach((element) => {
							let tipo = element.abreviacion_tipo;
							let responsable = self.responsables.filter(
								(item) => item.id == self.frmCanastaAsignacion.responsable_id
							)[0].abreviacion;
							let ubicacion = self.ubicaciones.filter(
								(item) => item.id == self.frmCanastaAsignacion.ubicacion_id
							)[0].abreviacion;
							let orden = element.id;
							let nombre = element.nombre;

							let nuevo_codigo =
								tipo +
								"-" +
								responsable +
								"-" +
								ubicacion +
								"-" +
								orden +
								"-" +
								nombre;

							let obj = {
								id: element.id,
								nuevo_codigo: nuevo_codigo,
								condicion_id: element.condicion_id,
							};
							lista_asignacion.push(obj);
						});

						data.append("canasta_asignacion", JSON.stringify(lista_asignacion));

						self.$inertia.post(route("log.act.asignacion.asignar"), data, {
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
										self.$parent.$parent.canasta_asignacion = [];
										self.$parent.$parent.ListarActivos();
										$("#mdlCanastaAsignacion").css("display", "none");
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

.plus {
	display: block;
	width: 100%;
	padding: 0.1rem 0.5rem;
	clear: both;
	font-weight: 400;
	color: #3a3b45;
	text-align: inherit;
	white-space: nowrap;
	background-color: transparent;
	border: 0;
}

.plus:hover,
.plus:focus {
	color: white !important;
	text-decoration: none;
	background-color: var(--colorAlto);
}

.plus.active,
.plus:active {
	color: #fff;
	text-decoration: none;
	background-color: var(--colorMedio);
}

.plus.disabled,
.plusdisabled {
	color: #858796;
	pointer-events: none;
	background-color: transparent;
}

.minus {
	display: block;
	width: 100%;
	padding: 0.1rem 0.5rem;
	clear: both;
	font-weight: 400;
	color: #3a3b45;
	text-align: inherit;
	white-space: nowrap;
	background-color: transparent;
	border: 0;
}

.minus:hover,
.minus:focus {
	color: white !important;
	text-decoration: none;
	background-color: var(--plomoOscuroEmpresarial);
}

.minus.active,
.minus:active {
	color: #fff;
	text-decoration: none;
	background-color: var(--plomoClaroEmpresarial);
}

.minus.disabled,
.minusdisabled {
	color: #858796;
	pointer-events: none;
	background-color: transparent;
}

.dropdown-plus-minus {
	padding: 0 !important;
	transform: translate3d(30px, -13px, 0px) !important;
	z-index: 1000 !important;
}

.mdlCanastaAsignacion {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaAsignacion {
		margin-top: 20%;
	}
}
</style>
