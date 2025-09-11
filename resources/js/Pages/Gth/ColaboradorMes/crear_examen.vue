<template>
	<layout ref="layout">
		<div class="slot_body slot-crear-examen" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CREAR EXAMEN'"></headerClose>
					<div class="card-title">DATOS DE EXAMEN</div>
					<div class="card-body card-block">
						<div class="form-row col-md-12">
							<div class="form-group col-md-6">
								<label
									for="slcCategorias"
									class="form-control-label label-title"
									>CATEGORÍA</label
								>
								<select
									class="form-control"
									name="categorias"
									id="slcCategorias"
									style="max-width: 450px"
									data-index="3"
									:disabled="preguntas.length == 0"
								>
									<option :value="0">TODAS</option>
									<option v-for="categoria in categorias" :key="categoria.id">
										{{ categoria.categoria }}
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="card-title">PREGUNTAS DISPONIBLES</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-9 col-7">
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

							<div class="col-md-1 col-2 ml-1">
								<button
									class="btn btn-action btn-icon-split"
									id="btnCrearExamen"
									:disabled="frmDatosExamen.preguntas_seleccionadas.length == 0"
									@click="NuevoExamen"
									title="Nuevo EXAMEN"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
								</button>
							</div>
						</div>

						<table class="table table-hover" id="tblPreguntas" width="100%">
							<thead>
								<tr>
									<th style="width: 70px !important">CHECK</th>
									<th>PREGUNTA</th>
									<th>VALOR</th>
									<th>CATEGORÍA</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(pregunta, index) in preguntas" :key="index">
									<td class="table-bordered" align="center">
										<div class="align-middle">
											<div class="checkbox">
												<label
													class="align-middle"
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													:for="index"
													><input
														type="checkbox"
														:id="index"
														:value="pregunta.id"
														v-model="
															frmDatosExamen.preguntas_seleccionadas
														" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
										</div>
									</td>
									<td class="table-bordered">
										{{ pregunta.pregunta }}
									</td>
									<td class="table-bordered" align="center">
										{{ pregunta.valor }}
									</td>
									<td class="table-bordered" align="center">
										{{ pregunta.nombre_categoria }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div id="mdlNombreExamen" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-25 mdlNombreExamen">
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
							<div class="card-title"></div>
							<div class="card-body card-block">
								<form>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label
												for="slcCategorias"
												class="form-control-label label-title"
												>NOMBRE DEL EXÁMEN</label
											>
											<span
												v-if="submited && !$v.frmDatosExamen.nombre.required"
												class="span-error-message"
											>
												*
											</span>

											<textarea
												class="form-control mayus"
												type="text"
												id="inpNombreExamen"
												name="nombre_examen"
												maxlength="200"
												oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
												placeholder="ej. Examen para asesores de crédito"
												v-model="frmDatosExamen.nombre"
											></textarea>
										</div>
									</div>
								</form>

								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										id="btnGuardar"
										@click="CrearExamen"
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
const noEmpty = (value) => value.length != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: { preguntas: Array, categorias: Array },
	data() {
		return {
			windowWidth: window.innerWidth,
			submited: false,
			title_modal: null,
			frmDatosExamen: {
				nombre: null,
				preguntas_seleccionadas: [],
			},
		};
	},
	validations: {
		frmDatosExamen: {
			nombre: { required },
			preguntas_seleccionadas: { noEmpty },
		},
	},
	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaPreguntas();
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1500) {
		//     if (!$("#tblPreguntas").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblPreguntas")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblPreguntas").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblPreguntas")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		NuevoExamen() {
			this.submited = true;
			if (this.$v.frmDatosExamen.preguntas_seleccionadas.noEmpty) {
				this.submited = false;
				this.title_modal = "NUEVO EXÁMEN";

				$("#mdlNombreExamen").css("display", "block");
				$("#btnCancelar").click(function () {
					$("#mdlNombreExamen").css("display", "none");
				});
			} else {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe seleccionar almenos una pregunta, verifique.",
				});
				return false;
			}
		},
		TablaPreguntas() {
			self = this;
			this.$nextTick(() => {
				var table = $("#tblPreguntas").DataTable({
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

				$("#slcCategorias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		CrearExamen() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosExamen.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			} else {
				axios
					.post(route("gth.col_mes.crear_examen.verificar"), {
						nombre: self.frmDatosExamen.nombre,
					})
					.then(function (response) {
						let resultado = response.data;
						if (resultado == "EXISTE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Este EXAMEN ya está registrado, intente nuevamente.",
							});
							return false;
						} else {
							Swal.fire({
								title: "GUARDAR EXAMEN",
								text: "¿Desea continuar?",
								confirmButtonText:
									'<i class="fas fa-check" style="color:white;"></i>   Si',
								confirmButtonColor: "var(--colorAlto)",
								showCancelButton: true,
								cancelButtonText: '<i class="fas fa-times"></i>   No',
								cancelButtonColor: "var(--plomoOscuroEmpresarial)",
								allowOutsideClick: false,
								preConfirm: (result) => {
									axios
										.post(
											route("gth.col_mes.crear_examen.guardar"),
											self.frmDatosExamen
										)
										.then(function (response) {
											let resultado = response.data;
											if (resultado == "EXITO") {
												Swal.fire({
													icon: "success",
													title: "¡EXITO!",
													text: "Todo salió bien",
													allowOutsideClick: false,
													preConfirm: (result) => {
														self.$inertia.get(
															route("gth.col_mes.crear_examen")
														);
													},
												});
											} else {
												Swal.fire({
													icon: "error",
													title: "¡Ups!",
													text: "Algo salió mal",
												});
											}
										});
								},
							});
						}
					});
			}
		},
		Cerrar() {
			$("#mdlNombreExamen").css("display", "none");
		},
	},
};
</script>


<style >
.slot-crear-examen {
	width: 60% !important;
	margin-left: 20% !important;
}

.mdlDatosExamen {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-crear-examen {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosExamen {
		margin-top: 20%;
	}
}
</style>
