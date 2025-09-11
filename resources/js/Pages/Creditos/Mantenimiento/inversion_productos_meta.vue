<template>
	<layout ref="layout">
		<div class="slot_body slot-productos-meta" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'INVERSIÓN - PRODUCTOS META'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-4 col-md-6 col-7">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscarProductos"
										placeholder="Escriba el texto a buscar"
										autocomplete="off"
										spellcheck="false"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>

							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										v-model="frmDatosProductoMeta.agencia_seleccionada"
										@change="Listar"
									>
										<option
											v-for="(item, index) in agencias_permitidas"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group col-md-2">
								<button
									class="btn btn-action btn-icon-split"
									@click="Nuevo"
									title="Nuevo PRODUCTO META"
									:disabled="frmDatosProductoMeta.agencia_seleccionada == 0"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>
						<div class="card-title mb-1">LISTA DE RESULTADOS</div>
						<table class="table table-hover" id="tblProductosMeta" width="100%">
							<thead>
								<tr>
									<th style="min-width: 75px !important">EDITAR</th>
									<th style="min-width: 30px !important">N°</th>
									<th style="min-width: 200px !important">PRODUCTO</th>
									<th style="min-width: 100px !important">VALOR_META</th>
									<th style="min-width: 250px !important">DESCRIPCIÓN</th>
									<th style="min-width: 80px !important">ORIENTACIÓN</th>
									<th style="min-width: 50px !important">HABILITADO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in productos_meta"
									:key="index"
									class="table-bordered"
									:class="[index % 2 == 0 ? 'verde-claro' : '']"
								>
									<td align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="Editar(item)"
											title="Editar PRODUCTO META"
										>
											<span class="icon text-white">
												<i class="fas fa-edit"></i>
											</span>
										</button>
									</td>
									<td align="center">
										{{ index + 1 }}
									</td>
									<td align="center">
										{{ item.producto }}
									</td>
									<td align="right">S/ {{ roundTo(item.valor_meta, 2) }}</td>
									<td align="center">
										{{ item.descripcion == null ? "-" : item.descripcion }}
									</td>
									<td align="center">
										{{ item.orientacion == null ? "-" : item.orientacion }}
									</td>
									<td align="center">
										{{ item.habilitado == 1 ? "SI" : "NO" }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosProductoMeta" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosProductoMeta">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlDatosProductoMeta'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<form autocomplete="off" @submit.prevent="Guardar">
									<div class="form-row">
										<div class="form-group col-8">
											<label class="label-title">PRODUCTO</label>
											<input
												type="text"
												class="form-control mayus"
												:class="[
													submited
														? $v.frmDatosProductoMeta.producto.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												maxlength="50"
												v-model="frmDatosProductoMeta.producto"
											/>
										</div>
										<div class="form-group col-4">
											<label class="label-title">VALOR META</label>

											<input
												type="number"
												class="form-control center bolder"
												:class="[
													submited
														? $v.frmDatosProductoMeta.valor_meta.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												min="0.1"
												step="100"
												lang="en"
												v-model="frmDatosProductoMeta.valor_meta"
												@change="Redondear"
											/>
										</div>
										<div class="form-group col-8">
											<label class="label-title">DESCRIPCIÓN</label>
											<textarea
												class="form-control mayus text-row"
												maxlength="200"
												rows="2"
												v-model="frmDatosProductoMeta.descripcion"
											></textarea>
										</div>
										<div class="form-group col-4">
											<label class="label-title">ORIENTACIÓN</label>
											<select
												class="form-control mayus center"
												v-model="frmDatosProductoMeta.orientacion"
											>
												<option value="EMPRESA">EMPRESA</option>
												<option value="CLIENTE">CLIENTE</option>
											</select>
										</div>
										<div
											class="row"
											v-if="frmDatosProductoMeta.modo == 'EDITAR'"
										>
											<div class="form-check">
												<input
													id="chbHabilitado"
													type="checkbox"
													v-model="frmDatosProductoMeta.habilitado"
												/>
												<label
													class="form-check-label label-title"
													for="chbHabilitado"
												>
													HABILITADO
												</label>
											</div>
										</div>
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split mb-1"
										@click="Guardar()"
										title="Guardar SECTOR"
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
import { required } from "vuelidate/lib/validators";

import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},

	data() {
		return {
			agencias_permitidas: [],
			productos_meta: [],
			submited: false,

			titulo_modal: "NUEVO PRODUCTO META",
			frmDatosProductoMeta: {
				agencia_seleccionada: 0,
				id: null,
				producto: null,
				modo: "",
				valor_meta: this.roundTo(100, 2),
				descripcion: null,
				orientacion: null,
				habilitado: false,
			},
		};
	},
	validations: {
		frmDatosProductoMeta: {
			valor_meta: { required },
			producto: { required },
		},
	},

	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.frmDatosProductoMeta.agencia_seleccionada = mi_agencia[0].id;
				this.Listar();
			} else {
				if (value.length > 0) {
					this.frmDatosProductoMeta.agencia_seleccionada = value[0].id;
					this.Listar();
				} else {
					this.frmDatosProductoMeta.agencia_seleccionada = 0;
				}
			}
		},
		productos_meta() {
			$("#tblProductosMeta").DataTable().destroy();
			this.TablaProductos();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaProductos();
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_MANTENIMIENTO/INVERSION_PRODUCTOS_META"
			);
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			if (e.target.value && e.target.value > 0) {
				valor = e.target.value;
			}

			this.frmDatosProductoMeta.valor_meta = this.roundTo(
				valor,
				numero_decimales
			);
		},
		TablaProductos() {
			this.$nextTick(() => {
				var table = $("#tblProductosMeta").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
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
					responsive: true,
				});

				$("#inpBuscarProductos").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		Listar() {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", this.frmDatosProductoMeta.agencia_seleccionada);

			// this.$inertia.post(route("cre.man.cre_sectores.listar"), data);
			axios
				.post(route("man.inversion.productos_meta.listar"), data)
				.then(function (response) {
					self.productos_meta = response.data.productos_meta;
				});
		},

		Nuevo() {
			this.submited = false;
			this.titulo_modal = "NUEVO PRODUCTO META";
			this.frmDatosProductoMeta.modo = "NUEVO";
			this.frmDatosProductoMeta.id = 0;
			this.frmDatosProductoMeta.producto = null;
			this.frmDatosProductoMeta.valor_meta = this.roundTo(100, 2);
			this.frmDatosProductoMeta.orientacion = "EMPRESA";
			this.frmDatosProductoMeta.descripcion = null;
			this.frmDatosProductoMeta.habilitado = true;

			$("#mdlDatosProductoMeta").css("display", "block");
		},

		Editar(item) {
			this.submited = false;
			this.titulo_modal = "EDITAR PRODUCTO META";
			this.frmDatosProductoMeta.modo = "EDITAR";
			this.frmDatosProductoMeta.id = item.id;
			this.frmDatosProductoMeta.producto = item.producto;
			this.frmDatosProductoMeta.valor_meta = this.roundTo(item.valor_meta, 2);
			this.frmDatosProductoMeta.descripcion = item.descripcion;
			this.frmDatosProductoMeta.orientacion = item.orientacion;
			this.frmDatosProductoMeta.habilitado = item.habilitado;

			$("#mdlDatosProductoMeta").css("display", "block");
		},

		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosProductoMeta.$invalid) {
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
							.post(
								route("man.inversion.productos_meta.verificar"),
								self.frmDatosProductoMeta
							)
							.then(function (response) {
								if (response.data == "EXISTE") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Este PRODUCTO ya existe",
									});
									return false;
								} else {
									self.$inertia.post(
										route("man.inversion.productos_meta.guardar"),
										self.frmDatosProductoMeta,
										{
											preserveScroll: true,

											onStart: () => {
												Swal.fire({
													title: "GUARDANDO",
													text: "Espere porfavor...",
													showConfirmButton: false,
													allowOutsideClick: false,
													willOpen: () => {
														Swal.showLoading();
													},
												});
											},
											onSuccess: () => {
												Swal.fire({
													icon: "success",
													title: "¡ÉXITO!",
													allowOutsideClick: false,
												}).then((result) => {
													if (result.isConfirmed) {
														self.submited = false;
														self.Listar();
														$("#mdlDatosProductoMeta").css("display", "none");
													}
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
.slot-productos-meta {
	width: 50% !important;
	margin-left: 25% !important;
}

.mdlDatosProductoMeta {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-productos-meta {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosProductoMeta {
		margin-top: 20%;
	}
}
</style>
