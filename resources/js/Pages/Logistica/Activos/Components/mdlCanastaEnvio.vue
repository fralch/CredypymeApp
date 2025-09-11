<template>
	<div id="mdlCanastaEnvio" class="modal">
		<div class="modal-content w-40 mdlCanastaEnvio">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlCanastaEnvio'"
					>
					</headerCloseModal>
					<div class="card-body card-block">
						<label class="label-title">ACTIVOS A ENVIAR</label>
						<span
							v-if="submited && !$v.frmCanastaEnvio.canasta_envio.required"
							class="span-error-message"
						>
							*
						</span>
						<table class="table table-hover" id="tblCanastaEnvios">
							<thead>
								<tr>
									<th>QUITAR</th>
									<th>CÓDIGO_DE_ACTIVO</th>
									<th>DESCRIPCIÓN</th>
									<th>MARCA</th>
									<th>VALOR_ACTUAL</th>
									<th>CONDICIÓN</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in frmCanastaEnvio.canasta_envio"
									:key="index"
									style="height: 50px !important"
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
										{{ item.descripcion }}
									</td>
									<td class="table-bordered">
										{{ item.marca == null ? "-" : item.marca }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.valor_actual }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.condicion }}
									</td>
								</tr>
							</tbody>
						</table>

						<div class="form-row mt-3">
							<div class="form-group col-md-4 col-5">
								<label class="label-title">AGENCIA DESTINO</label>
								<span
									v-if="
										submited && !$v.frmCanastaEnvio.agencia_recepcion.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control center mayus"
									v-model="frmCanastaEnvio.agencia_recepcion"
									@change="Filtrar"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in agencias_fitradas"
										:key="index"
										:value="item.id"
									>
										{{ item.agencia }}
									</option>
								</select>
							</div>
							<div class="form-group col-md-8 col-7">
								<label class="label-title">DOCUMENTO</label>
								<span
									v-if="
										submited && !$v.frmCanastaEnvio.documento_envio.required
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
										font-size: var(--tamañoLetraLabels);
										width: 100% !important;
									"
									type="file"
									id="documento_envio"
									@change="AgregarDocumento"
								/>
							</div>
							<div class="form-group col-md-6 col-6">
								<label class="label-title">RESPONSABLE RECEPCIÓN</label>
								<span
									v-if="
										submited && !$v.frmCanastaEnvio.responsable_recepcion.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control mayus"
									v-model="frmCanastaEnvio.responsable_recepcion"
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
							<div class="form-group col-md-6 col-6">
								<label class="label-title">UBICACIÓN RECEPCIÓN</label>
								<span
									v-if="
										submited && !$v.frmCanastaEnvio.ubicacion_recepcion.noZero
									"
									class="span-error-message"
								>
									*
								</span>
								<select
									class="form-control mayus"
									v-model="frmCanastaEnvio.ubicacion_recepcion"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in ubicaciones_filtradas"
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
							<button class="btn btn-action btn-icon-split" @click="Registrar">
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

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value > 0;
export default {
	components: { headerCloseModal },
	props: {
		tipo_modulo: String,
		agencias: Array,
		agencia_seleccionada: Number,
		responsables: Array,
		ubicaciones: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			responsables_filtrados: [],
			ubicaciones_filtradas: [],
			frmCanastaEnvio: {
				canasta_envio: [],
				documento_envio: null,
				agencia_recepcion: 0,
				responsable_recepcion: 0,
				ubicacion_recepcion: 0,
			},
		};
	},
	validations: {
		frmCanastaEnvio: {
			canasta_envio: { required },
			documento_envio: { required },
			agencia_recepcion: { noZero },
			responsable_recepcion: { noZero },
			ubicacion_recepcion: { noZero },
		},
	},
	computed: {
		agencias_fitradas() {
			let self = this;
			let lista = [];
			this.agencias.forEach((element) => {
				if (element.id != self.agencia_seleccionada) {
					lista.push(element);
				}
			});
			return lista;
		},
	},
	mounted() {
		this.TablaCanastaEnvios();
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
			$("#tblCanastaEnvios").DataTable().destroy();
			this.TablaCanastaEnvios();
		},

		TablaCanastaEnvios() {
			this.$nextTick(() => {
				var table = $("#tblCanastaEnvios").DataTable({
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
					self.frmCanastaEnvio.canasta_envio.splice(index, 1);
					await self.ActualizarTabla();
				} else {
					return false;
				}
			});
		},
		AgregarDocumento(e) {
			this.frmCanastaEnvio.documento_envio = e.target.files[0];
		},
		Filtrar() {
			this.responsables_filtrados = this.responsables.filter(
				(item) =>
					item.agencia_id == this.frmCanastaEnvio.agencia_recepcion &&
					item.encargado_agencia == 1
			);
			this.frmCanastaEnvio.responsable_recepcion = 0;
			this.ubicaciones_filtradas = this.ubicaciones.filter(
				(item) => item.agencia_id == this.frmCanastaEnvio.agencia_recepcion
			);
			this.frmCanastaEnvio.ubicacion_recepcion = 0;
		},

		Registrar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmCanastaEnvio.$invalid) {
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
						data.append("agencia_envio", self.agencia_seleccionada);
						data.append(
							"agencia_recepcion",
							self.frmCanastaEnvio.agencia_recepcion
						);
						data.append(
							"responsable_recepcion",
							self.frmCanastaEnvio.responsable_recepcion
						);
						data.append(
							"ubicacion_recepcion",
							self.frmCanastaEnvio.ubicacion_recepcion
						);
						data.append(
							"documento_envio",
							self.frmCanastaEnvio.documento_envio
						);

						let lista_envio = [];
						self.frmCanastaEnvio.canasta_envio.forEach((element) => {
							let obj = {
								id: element.id,
								cantidad: element.cantidad,
								valor_actual: element.valor_actual,
							};
							lista_envio.push(obj);
						});

						data.append("canasta_envio", JSON.stringify(lista_envio));

						self.$inertia.post(route("log.act.inventario.enviar"), data, {
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									html: "ESPERE POR FAVOR...",
									timer: 5000,
									allowOutsideClick: false,
									timerProgressBar: true,
									width: "250px",
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
										self.$parent.$parent.canasta_envio = [];
										self.$parent.$parent.ListarActivos();
										$("#mdlCanastaEnvio").css("display", "none");
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

.mdlCanastaEnvio {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaEnvio {
		margin-top: 20%;
	}
}

.custom-file-input ~ .custom-file-label::after {
	display: none;
}
</style>
