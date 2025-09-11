<template>
	<layout ref="layout">
		<div class="slot_body slot-sistema-pensiones" slot="component-view">
			<div class="card">
				<headerClose :title="'SISTEMA DE PENSIONES'"></headerClose>

				<div class="card-title">LISTA SISTEMA DE PENSIONES</div>
				<div class="card-body card-block">
					<div class="center">
						<button class="btn btn-action btn-icon-split mb-2" @click="Nuevo">
							<span class="icon text-white">
								<i class="fas fa-plus"></i>
							</span>
							<span class="text">NUEVO</span>
						</button>
					</div>
					<div class="input-group row col-md-10 col-7">
						<div class="input-group-prepend">
							<span class="input-group-text"
								><i class="fas fa-search"></i
							></span>
						</div>
						<input
							class="form-control mayus"
							type="text"
							id="inpBuscar"
							autocomplete="off"
							spellcheck="false"
							@focus="hidenav()"
							@blur="shownav()"
						/>
					</div>

					<table
						class="table table-hover"
						id="tblSistemaPensiones"
						width="100%"
					>
						<thead>
							<tr>
								<th>EDITAR</th>
								<th>TIPO</th>
								<th>NOMBRE</th>
								<th>TIPO DE COMISIÓN</th>
								<th>PORCENTAJE</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(sistema_pension, index) in sistema_pensiones"
								:key="index"
							>
								<td class="table-bordered" align="center">
									<button
										class="btn btn-action btn-icon-split"
										@click="Editar(sistema_pension)"
									>
										<span class="icon text-white">
											<i class="far fa-edit" style="color: white"></i>
										</span>
									</button>
								</td>
								<td class="table-bordered" align="center">
									{{ sistema_pension.tipo }}
								</td>
								<td class="table-bordered" align="center">
									{{ sistema_pension.nombre }}
								</td>
								<td class="table-bordered" align="center">
									{{
										sistema_pension.tipo == "ONP"
											? "-"
											: sistema_pension.tipo_comision
									}}
								</td>
								<td class="table-bordered" align="center">
									{{ (sistema_pension.porcentaje * 100).toFixed(2) }} %
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal" id="mdlEditar">
				<div class="modal-content w-25 mdlEditar">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>{{ title_modal }}</strong>
								<button
									type="button"
									class="btn btn-action"
									style="border-radius: 50%; float: right !important"
									@click="Cerrar"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-title">INFORMACIÓN</div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-md-4 col-4">
											<label for="sTipo" class="form-control-label label-title"
												>TIPO</label
											>
											<select
												class="form-control center"
												id="sTipo"
												v-model="frmSistemaPension.tipo"
												@change="frmSistemaPension.tipo_comision = '0'"
											>
												<option value="0">Seleccione...</option>
												<option value="AFP">AFP</option>
												<option value="ONP">ONP</option>
											</select>
											<div
												v-if="submited && !$v.frmSistemaPension.tipo.noZero"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<div class="form-group col-md-8 col-8">
											<label
												for="inpNomApe"
												class="form-control-label label-title"
												>NOMBRE</label
											>
											<textarea
												type="text"
												class="form-control"
												style="max-width: 300px"
												id="txtNombreSistemaPension"
												rows="1"
												v-model="frmSistemaPension.nombre"
											></textarea>
											<div
												v-if="submited && !$v.frmSistemaPension.nombre.required"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4 col-6">
											<label
												for="inpRemReal"
												class="form-control-label label-title"
												>COMISIÓN</label
											>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">%</div>
												</div>
												<input
													type="number"
													class="form-control center"
													id="inpRemReal"
													name="valorActivo"
													min="0"
													lang="en"
													step="0.01"
													v-model="frmSistemaPension.comision"
												/>
												<div
													v-if="
														submited && !$v.frmSistemaPension.comision.required
													"
													style="color: red; font-size: 12px"
												>
													*Campo obligatorio
												</div>
											</div>
										</div>
										<div class="form-group col-md-8 col-6">
											<label
												for="sTipoComision"
												class="form-control-label label-title"
												>TIPO DE COMISIÓN</label
											>
											<select
												class="form-control center"
												id="slcTipoComisiones"
												v-model="frmSistemaPension.tipo_comision"
												:disabled="frmSistemaPension.tipo == 'ONP'"
											>
												<option value="0">Seleccione...</option>
												<option value="Mixto">Mixto</option>
												<option value="Flujo">Flujo</option>
											</select>
											<div
												v-if="
													submited &&
													frmSistemaPension.tipo == 'AFP' &&
													!$v.frmSistemaPension.tipo_comision.noZero
												"
												style="color: red; font-size: 12px"
											>
												*Campo obligatorio
											</div>
										</div>
										<!-- ----------- -->
									</div>
								</form>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar"
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		sistema_pensiones: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			title_modal: "",
			frmSistemaPension: {
				modo: "NUEVO",
				id_sis_pensiones: null,
				tipo: null,
				nombre: null,
				comision: null,
				tipo_comision: null,
			},
		};
	},
	validations() {
		if (this.frmSistemaPension.tipo == "ONP") {
			return {
				frmSistemaPension: {
					tipo: { noZero },
					nombre: { required },
					comision: { required },
				},
			};
		} else if (this.frmSistemaPension.tipo == "AFP") {
			return {
				frmSistemaPension: {
					tipo: { noZero },
					nombre: { required },
					comision: { required },
					tipo_comision: { noZero },
				},
			};
		}
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaSistemaPensiones();
	},

	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 500) {
		//     if (!$("#tblSistemaPensiones").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblSistemaPensiones")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblSistemaPensiones").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblSistemaPensiones")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, places) {
			if (value > 0 && value != "Infinity") {
				var power = Math.pow(10, places);
				return parseFloat(Math.round(value * power) / power).toFixed(2);
			} else {
				return 0;
			}
		},
		TablaSistemaPensiones() {
			this.$nextTick(() => {
				var table = $("#tblSistemaPensiones").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},

					scrollCollapse: true,
					paging: false,
					order: [1, "asc"],
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
					responsive: true,
				});
				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		Nuevo() {
			this.submited = false;
			this.title_modal = "NUEVO SISTEMA DE PENSIÓN";
			this.frmSistemaPension.modo = "NUEVO";
			this.frmSistemaPension.id_sis_pensiones = null;
			this.frmSistemaPension.tipo = 0;
			this.frmSistemaPension.nombre = null;
			this.frmSistemaPension.comision = null;
			this.frmSistemaPension.tipo_comision = 0;

			$("#mdlEditar").css("display", "block");

			$("#btnCancelar").click(function () {
				$("#mdlEditar").css("display", "none");
			});
		},
		Editar(sistema_pension) {
			this.submited = false;
			this.title_modal = "EDITAR SISTEMA DE PENSIÓN";
			this.frmSistemaPension.modo = "EDITAR";
			this.frmSistemaPension.id_sis_pensiones =
				sistema_pension.id_sis_pensiones;
			this.frmSistemaPension.tipo = sistema_pension.tipo;
			this.frmSistemaPension.nombre = sistema_pension.nombre;
			this.frmSistemaPension.comision = this.roundTo(
				sistema_pension.porcentaje * 100,
				2
			);
			this.frmSistemaPension.tipo_comision = sistema_pension.tipo_comision;

			$("#mdlEditar").css("display", "block");

			$("#btnCancelar").click(function () {
				$("#mdlEditar").css("display", "none");
			});
		},
		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmSistemaPension.$invalid) {
				return false;
			} else {
				Swal.fire({
					title: "GUARDAR",
					text: "¿Desea continuar?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
					preConfirm: (result) => {
						self.$inertia.post(
							route("gth.pla.sistema_pensiones.guardar"),
							self.frmSistemaPension,
							{
								preserveScroll: true,
								onSuccess: () => {
									Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										text: "Datos guardados",
										allowOutsideClick: false,
										preConfirm: (result) => {
											$("#mdlEditar").css("display", "none");
										},
									});
								},
							}
						);
					},
				});
			}
		},

		Cerrar() {
			$("#mdlEditar").css("display", "none");
		},
	},
};
</script>

<style >
.slot-sistema-pensiones {
	width: 60% !important;
	margin-left: 20% !important;
}
.mdlEditar {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-sistema-pensiones {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlEditar {
		margin-top: 20%;
	}
}
</style>
