<template>
	<div id="mdlExonerarComision" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-50 mdlExonerarComision">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'EXONERAR COMISIÓN POR ' + titulo_modal"
						:nombre_modal="'mdlExonerarComision'"
					>
					</headerCloseModal>

					<div class="card-title">LISTA DE CRÉDITOS APROBADOS</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-8 col-6">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" v-if="windowWidth >= 900"
											>Buscar
										</span>

										<span class="input-group-text" v-if="windowWidth < 900"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										id="inpBuscar_ex_cr"
										class="form-control mayus"
										type="text"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										@focus="hidenav()"
										@blur="shownav()"
										ref="buscar_credito"
									/>
								</div>
							</div>
							<div class="form-group col-md-4 col-6">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Ag. </span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_seleccionada"
										@change="ListarCreditos()"
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
						</div>

						<table class="table" id="tblListaAprobados" width="100% !important">
							<thead>
								<tr>
									<th>N°</th>
									<th>COBRAR</th>
									<th style="min-height: 100px">EXPEDIENTE</th>
									<th style="min-width: 200px">CLIENTE</th>
									<th>FECHA_APROBACIÓN</th>
									<th style="min-width: 80px">MONTO</th>
									<th>PERIODO</th>
									<th>ASESOR</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in aprobados" :key="index">
									<td align="center">
										{{ index + 1 }}
									</td>
									<td align="center">
										<div class="align-middle">
											<div class="checkbox">
												<label
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													><input
														type="checkbox"
														class="form-control"
														:checked="VerificarComision(item.comisiones)"
														@change="Actualizar(item.id, item.comisiones)" />
													<span class="cr"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
										</div>
									</td>
									<td align="center">
										{{ item.codigo_expediente }}
									</td>
									<td>
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td align="center">
										{{ JSON.parse(item.datos_creacion).fecha }}
									</td>
									<td align="center">S/ {{ roundTo(item.monto, 2) }}</td>
									<td align="center">
										{{
											roundTo(item.plazo, 0) +
											" " +
											periodo_medicion(item.periodo_pago)
										}}
									</td>
									<td align="center">
										{{ item.usuario_asesor }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
export default {
	components: { headerClose, headerCloseModal },
	data() {
		return {
			agencia_seleccionada: null,
			agencias_permitidas: [],
			windowWidth: window.innerWidth,

			tipo_filtro: "apellidos_nombres",
			aprobados: [],
			nombre_modulo: "",
			comision_desembolso_id: null,
			comision_riesgo_c_id: null,
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
				} else {
					this.agencia_seleccionada = 0;
				}
			}
		},
		aprobados() {
			$("#tblListaAprobados").DataTable().destroy();
			this.TablaListaAprobados();
		},
		nombre_modulo() {
			this.ListarCreditos();
		},
	},
	computed: {
		titulo_modal() {
			if (this.nombre_modulo == "desembolso") {
				return "DESEMBOLSO";
			} else if (this.nombre_modulo == "riesgo_crediticio")
				return "RIESGO CREDITICIO";
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		periodo_medicion(value) {
			if (value == "DIARIO") {
				return "(DÍAS)";
			} else if (value == "SEMANAL") {
				return "(SEMANAS)";
			} else if (value == "QUINCENAL") {
				return "(QUINCENAS)";
			} else if (value == "MENSUAL") {
				return "(MESES)";
			}
			return "(DÍAS)";
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},
		hidenav() {
			return this.$parent.hide_nav();
		},
		shownav() {
			return this.$parent.show_nav();
		},
		TablaListaAprobados() {
			this.$nextTick(() => {
				var table = $("#tblListaAprobados").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,

					language: {
						// select: {
						//   rows: "%d fila seleecionada",
						// },
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

				$("#inpBuscar_ex_cr").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		Actualizar(aprobacion_id, comisiones) {
			let self = this;
			let lista_comisiones = JSON.parse(comisiones);

			let comision_id = null;
			if (this.nombre_modulo == "desembolso") {
				comision_id = 1;
			} else if (this.nombre_modulo == "riesgo_crediticio") {
				comision_id = 2;
			}

			lista_comisiones.forEach((element) => {
				if (element.comision_id == comision_id) {
					if (element.cobrar_comision == 0) {
						element.cobrar_comision = 1;
					} else {
						element.cobrar_comision = 0;
					}
				}
			});

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("aprobacion_id", aprobacion_id);
			data.append("comisiones", JSON.stringify(lista_comisiones));

			// this.$inertia.post(route("cre.aprobacion.actualizar_comision"), data);
			axios
				.post(route("cre.aprobacion.actualizar_comision"), data)
				.then(function () {
					return self.ListarCreditos();
				});
		},
		VerificarComision(comisiones) {
			let lista_comisiones = JSON.parse(comisiones);
			let comision_id = null;

			if (this.nombre_modulo == "desembolso") {
				comision_id = 1;
			} else if (this.nombre_modulo == "riesgo_crediticio") {
				comision_id = 2;
			}

			let activado = lista_comisiones.filter(
				(item) => item.comision_id == comision_id
			)[0].cobrar_comision;

			return activado;
		},

		ListarCreditos() {
			let self = this;
			axios
				.post(
					route("cre.aprobacion.listar", {
						agencia_id: self.agencia_seleccionada,
					})
				)
				.then(function (response) {
					self.aprobados = response.data;
				});
		},
	},
};
</script>

<style lang="css">
.mdlExonerarComision {
	/* position: fixed; */
	height: fit-content;
	margin-top: 5%;
	/* right: 0; */
	margin-bottom: 0;
}
@media (max-width: 500px) {
	.mdlExonerarComision {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>
