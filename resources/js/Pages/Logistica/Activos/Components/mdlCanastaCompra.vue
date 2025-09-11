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
						<div class="text-center">
							<button
								class="btn btn-action btn-icon-split mb-1"
								title="Agregar ACTIVO"
								@click="Agregar"
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

						<table
							class="table table-hover"
							id="tblCanastaCompras"
							width="100%"
						>
							<thead>
								<tr>
									<th>ACCIONES</th>
									<th>CÓDIGO_DE_ACTIVO</th>
									<th>NOMBRE</th>
									<th>DESCRIPCIÓN</th>
									<th>CANTIDAD</th>
									<th>VALOR_UNITARIO(S/)</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in frmCanastaCompras.canasta"
									:key="index"
								>
									<td class="table-bordered" align="center">
										<div class="btn-group" role="group">
											<button
												class="btn btn-danger"
												type="button"
												@click="Quitar(index)"
												title="Quitar ACTIVO"
											>
												<span class="icon text-white">
													<i class="fas fa-trash-alt"></i>
												</span>
											</button>
											<button
												class="btn btn-cancel"
												type="button"
												@click="Editar(item, index)"
												title="Editar ACTIVO"
											>
												<span class="icon text-white">
													<i class="fas fa-edit"></i>
												</span>
											</button>
										</div>
									</td>
									<td class="table-bordered" align="center">
										{{ item.codigo.codigo }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.nombre }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.descripcion }}
									</td>
									<td class="table-bordered" align="right">
										{{ roundTo(item.cantidad, 2) }}
									</td>
									<td class="table-bordered" align="right">
										{{ roundTo(item.valor_compra, 2) }}
									</td>
								</tr>
							</tbody>
						</table>
						<br />
						<form>
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="label-title">COMPROBANTE</label>

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
									<span
										v-if="
											submited && !$v.frmCanastaCompras.usuario_compra.noZero
										"
										class="span-error-message"
									>
										*
									</span>

									<select
										class="form-control center mayus"
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

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value > 0;
export default {
	components: { headerCloseModal },
	props: {
		//tipo_modulo: String,
		usuarios: Array,
		agencias: Array,
		nombres: Array,
		tipos: Array,
		responsables: Array,
		ubicaciones: Array,
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
	},
	watch: {
		canasta() {
			$("#tblCanastaCompras").DataTable().destroy();
			this.TablaCanastaCompras();
		},
	},
	validations: {
		frmCanastaCompras: {
			canasta: { required },
			documento: { required },
			usuario_compra: { noZero },
		},
	},

	mounted() {
		this.TablaCanastaCompras();
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
		TablaCanastaCompras() {
			this.$nextTick(() => {
				let table = $("#tblCanastaCompras").DataTable({
					scrollY: "350px",
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

		ActualizarCodigos() {
			let self = this;

			axios
				.post(route("log.act.inventario.obtener_ultimo"))
				.then(async function (response) {
					let ultimo = response.data + 1;
					self.frmCanastaCompras.canasta.forEach((element) => {
						let tipo = self.tipos.filter(
							(item) => item.id == element.tipo_id
						)[0].abreviacion;

						let responsable = self.responsables.filter(
							(item) => item.id == element.responsable_id
						)[0].abreviacion;

						let ubicacion = self.ubicaciones.filter(
							(item) => item.id == element.ubicacion_id
						)[0].abreviacion;

						let nombre = self.nombres.filter(
							(item) => item.id == element.nombre_id
						)[0].nombre;

						let codigo =
							tipo +
							"-" +
							responsable +
							"-" +
							ubicacion +
							"-" +
							ultimo +
							"-" +
							nombre;

						let objeto = {
							orden: ultimo,
							codigo: codigo,
						};

						element.codigo = objeto;
						ultimo += 1;
					});
				});
		},

		Agregar() {
			let mdlDatosActivo = this.$parent.$parent.$refs.mdlDatosActivo;
			mdlDatosActivo.submited = false;
			mdlDatosActivo.title_modal = "NUEVA COMPRA";
			mdlDatosActivo.modo = "AGREGAR_NUEVO";
			mdlDatosActivo.no_editable = false;
			// La agencia es ADMINISTRATIVA por defecto, su id = 5
			let agencia = mdlDatosActivo.agencias.filter((item) => item.id == 5)[0];

			// El responsable es ADM-LOGISTICA por defecto
			let responsable = mdlDatosActivo.responsables.filter(
				(item) => item.abreviacion == "ADM-LOGIST"
			)[0];

			// La ubicación es ALM01 por defecto
			let ubicacion = mdlDatosActivo.ubicaciones.filter(
				(item) => item.abreviacion == "ALM01" && item.agencia_id == 5
			)[0];

			mdlDatosActivo.frmDatosActivo.agencia_id = agencia.id;
			mdlDatosActivo.frmDatosActivo.agencia = agencia.agencia;
			mdlDatosActivo.frmDatosActivo.responsable_id = responsable.id;
			mdlDatosActivo.frmDatosActivo.responsable =
				responsable.abreviacion + " - " + responsable.usuario;
			mdlDatosActivo.frmDatosActivo.ubicacion_id = ubicacion.id;
			mdlDatosActivo.frmDatosActivo.ubicacion =
				ubicacion.ubicacion + " - " + ubicacion.abreviacion;
			mdlDatosActivo.frmDatosActivo.nombre_id = 0;
			mdlDatosActivo.frmDatosActivo.fecha_compra = null;
			mdlDatosActivo.frmDatosActivo.tipo_id = 0;
			mdlDatosActivo.frmDatosActivo.descripcion = null;
			mdlDatosActivo.frmDatosActivo.marca = null;
			mdlDatosActivo.frmDatosActivo.modelo = null;
			mdlDatosActivo.frmDatosActivo.placa = null;
			mdlDatosActivo.frmDatosActivo.caracteristicas = null;
			mdlDatosActivo.frmDatosActivo.condicion_id = 0;
			mdlDatosActivo.frmDatosActivo.cantidad = 1;
			mdlDatosActivo.frmDatosActivo.color = null;
			mdlDatosActivo.frmDatosActivo.valor_compra = this.roundTo(0, 2);
			mdlDatosActivo.frmDatosActivo.igv = this.roundTo(18, 2);
			mdlDatosActivo.frmDatosActivo.vida_util = this.roundTo(0, 2);
			mdlDatosActivo.frmDatosActivo.depreciacion = this.roundTo(0, 2);

			mdlDatosActivo.GenerarCodigo();

			$("#mdlDatosActivo").css("display", "block");
			$("#datosActivo1-tab").tab("show");
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
					self.frmCanastaCompras.canasta.splice(index, 1);
					await self.ActualizarCodigos();
				} else {
					return false;
				}
			});
		},
		async Editar(item, index) {
			let mdlDatosActivo = this.$parent.$parent.$refs.mdlDatosActivo;
			mdlDatosActivo.submited = false;
			mdlDatosActivo.title_modal = "EDITAR COMPRA";
			mdlDatosActivo.modo = "EDITAR_NUEVO";
			mdlDatosActivo.no_editable = false;
			// La agencia es ADMINISTRATIVA por defecto, su id = 5
			let agencia = mdlDatosActivo.agencias.filter((item) => item.id == 5)[0];

			// El responsable es ADM-LOGISTICA por defecto
			let responsable = mdlDatosActivo.responsables.filter(
				(item) => item.abreviacion == "ADM-LOGIST"
			)[0];

			// La ubicación es ALM01 por defecto
			let ubicacion = mdlDatosActivo.ubicaciones.filter(
				(item) => item.abreviacion == "ALM01" && item.agencia_id == 5
			)[0];

			mdlDatosActivo.frmDatosActivo.index = index;
			mdlDatosActivo.frmDatosActivo.agencia_id = agencia.id;
			mdlDatosActivo.frmDatosActivo.agencia = agencia.nombre;
			mdlDatosActivo.frmDatosActivo.responsable_id = responsable.id;
			mdlDatosActivo.frmDatosActivo.responsable =
				responsable.abreviacion + " - " + responsable.usuario;
			mdlDatosActivo.frmDatosActivo.ubicacion_id = ubicacion.id;
			mdlDatosActivo.frmDatosActivo.ubicacion =
				ubicacion.ubicacion + " - " + ubicacion.abreviacion;

			mdlDatosActivo.frmDatosActivo.codigo = [];
			mdlDatosActivo.frmDatosActivo.codigo.push(item.codigo);
			await mdlDatosActivo.ActualizarTabla();
			mdlDatosActivo.frmDatosActivo.nombre_id = item.nombre_id;
			mdlDatosActivo.frmDatosActivo.fecha_compra = item.fecha_compra;
			mdlDatosActivo.frmDatosActivo.tipo_id = item.tipo_id;
			mdlDatosActivo.frmDatosActivo.descripcion = item.descripcion;
			mdlDatosActivo.frmDatosActivo.marca = item.marca;
			mdlDatosActivo.frmDatosActivo.modelo = item.modelo;
			mdlDatosActivo.frmDatosActivo.placa = item.placa;
			mdlDatosActivo.frmDatosActivo.caracteristicas = item.caracteristicas;
			mdlDatosActivo.frmDatosActivo.condicion_id = item.condicion_id;
			mdlDatosActivo.frmDatosActivo.cantidad = item.cantidad;
			mdlDatosActivo.frmDatosActivo.color = item.color;
			mdlDatosActivo.frmDatosActivo.valor_compra = item.valor_compra;
			mdlDatosActivo.frmDatosActivo.igv = item.igv;
			mdlDatosActivo.frmDatosActivo.vida_util = item.vida_util;
			mdlDatosActivo.frmDatosActivo.depreciacion = item.depreciacion;

			$("#mdlDatosActivo").css("display", "block");
			$("#datosActivo1-tab").tab("show");
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
		Registrar() {
			this.submited = true;
			var self = this;
			if (this.$v.frmCanastaCompras.$invalid) {
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
						data.append(
							"canasta",
							JSON.stringify(self.frmCanastaCompras.canasta)
						);
						data.append("documento", self.frmCanastaCompras.documento);
						data.append(
							"usuario_compra",
							self.frmCanastaCompras.usuario_compra
						);
						self.$inertia.post(route("log.act.inventario.comprar"), data, {
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "TRABAJANDO",
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
									preConfirm: async (result) => {
										self.frmCanastaCompras.canasta = [];
										$("#documento").val();
										self.frmCanastaCompras.documento = null;
										self.$parent.$parent.ListarActivos();
										$("#mdlCanastaCompra").css("display", "none");
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

.mdlCanastaCompra {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlCanastaCompra {
		margin-top: 20%;
	}
}
</style>
