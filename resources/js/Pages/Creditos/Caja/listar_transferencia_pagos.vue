<template>
	<layout ref="layout">
		<div
			class="slot_body slot-cuenta-lista"
			slot="component-view"
			v-if="mi_caja.monto_apertura != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'ENVÍO Y RECEPCIÓN DE PAGOS'"></headerClose>
					<div class="card">
						<div class="card-title">BUSCAR CLIENTES</div>
						<div class="card-body card-block">
							<div class="form-check col-md-12 text-center">
								<div class="radios" style="display: inline-block">
									<input
										class="form-check-input"
										type="radio"
										name="tipo_transferencia"
										id="rdbEnvios"
										value="E"
										v-model="consulta.tipo"
									/>
									<label class="form-check-label bolder" for="rdbIngreso"
										>ENVÍOS</label
									>
								</div>
								<div
									class="radios"
									style="display: inline-block; margin-left: 30px"
								>
									<input
										class="form-check-input"
										type="radio"
										name="tipo_transferencia"
										id="rdbRecepcion"
										value="R"
										v-model="consulta.tipo"
									/>
									<label class="form-check-label bolder" for="rdbEgreso"
										>RECEPCIÓN</label
									>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for="fecha_desde" class="form-control-label">
											Desde
										</label>
										<input
											type="date"
											class="form-control"
											id="fecha_desde"
											v-model="consulta.fecha_desde"
										/>
									</div>
									<div class="form-group">
										<label for="fecha_hasta" class="form-control-label">
											Hasta
										</label>
										<input
											type="date"
											class="form-control"
											id="fecha_hasta"
											v-model="consulta.fecha_hasta"
										/>
									</div>
								</div>

								<div class="col-md-1">
									<div style="margin: 15px 15px">
										<input
											class="form-check-input"
											type="checkbox"
											id="chbFiltroAgencias"
											v-model="chbFiltroAgencias"
										/>
										<label class="label-title"> Agencia </label>
									</div>
									<div style="margin: 15px 15px">
										<input
											class="form-check-input"
											type="checkbox"
											id="chbFiltroMotivo"
											v-model="chbFiltroMotivo"
										/>
										<label class="label-title" for="chbFiltroCategoria">
											Motivo
										</label>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group" style="margin-top: 10px">
										<select
											class="form-control center"
											:disabled="chbFiltroAgencias != true"
											v-model="consulta.agencia"
										>
											<option :value="0" selected disabled>
												Seleccione...
											</option>
											<option
												v-for="(item, index) in agencias"
												:key="index"
												:value="item.id"
											>
												{{ item.agencia }}
											</option>
										</select>
									</div>
									<div class="form-group">
										<select
											class="form-control center"
											:disabled="chbFiltroMotivo != true"
											v-model="consulta.motivo"
										>
											<option :value="0" selected disabled>
												Seleccione...
											</option>
											<option value="CLIENTES">Clientes</option>
											<option value="OTROS">Otros</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="row">
										<div class="col-md-9">
											<button
												class="btn btn-action btn-icon-split"
												style="margin: 50px 0 0 5px; width: 150px; height: 40px"
												@click="consultar()"
											>
												<span class="icon text-white">
													<i class="fas fa-search"></i>
												</span>
												<span class="text">BUSCAR</span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- -------table---------- -->
						<div class="card-body card-block">
							<table
								class="table table-hover"
								id="tblTransacciones"
								width="100%"
							>
								<thead>
									<tr>
										<th style="width: 70px !important">N°</th>
										<th>CONCEPTO</th>
										<th>FECHA</th>
										<th>CAJA</th>
										<th>MONTO</th>
										<th>
											{{ consulta.tipo == "E" ? "AG._RECEPTOR" : "AG._EMISOR" }}
										</th>
										<th>{{ consulta.tipo == "E" ? "PARA" : "DE" }}</th>
										<th>MOTIVO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(data, index) in datos_tabla" :key="index">
										<td class="table-bordered" align="center">
											{{ index + 1 }}
										</td>
										<td class="table-bordered" align="left">
											{{ data.concepto }}
										</td>
										<td class="table-bordered" align="center">
											{{ JSON.parse(data.datos_creacion).fecha }}
										</td>
										<td class="table-bordered" align="center">
											{{ data.caja }}
										</td>
										<td class="table-bordered" align="right">
											S/ {{ parseFloat(data.monto).toFixed(2) }}
										</td>
										<td class="table-bordered" align="center">
											{{ data.agencia }}
										</td>
										<td class="table-bordered" align="center">
											{{ data.usuario }}
										</td>
										<td class="table-bordered" align="center">
											{{ data.motivo }}
										</td>
									</tr>
								</tbody>
							</table>
							<br />
						</div>
					</div>
				</div>
			</div>
			<!-- ---------------------- -->
		</div>
	</layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";

import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		fecha_larga: String,
	},
	data() {
		return {
			submited: false,
			agencias: [],
			datos_tabla: [],
			chbFiltroAgencias: false,
			chbFiltroMotivo: false,
			consulta: {
				fecha_desde: this.fecha_larga,
				fecha_hasta: this.fecha_larga,
				agencia: 0,
				motivo: "0",
				tipo: "E",
			},
		};
	},
	validations() {
		return {
			frmDatosEnvio: {
				monto: { noZero, required },
				usuario_remitente: { noZero },
				agencia_remitente_id: { noZero },
				concepto: { required },
				motivo: { required },
			},
		};
	},

	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		mi_usuario() {
			return this.$inertia.page.props.user_session;
		},
	},
	watch: {
		datos_tabla() {
			$("#tblTransacciones").DataTable().destroy();
			this.Tablaslista();
		},
	},
	mounted() {
		if (this.mi_caja == null) {
			Swal.fire({
				title: "¡Ups!",
				text: "Usted no tiene una cuenta habilitada",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			let agencias_filtradras = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/ENVIO_PAGOS"
			);

			this.agencias = agencias_filtradras.filter((item) => {
				return item.id != this.mi_caja.agencia_id;
			});
		}
		this.Tablaslista();
	},

	methods: {
		Tablaslista() {
			this.$nextTick(() => {
				var table = $("#tblTransacciones").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					order: false,
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
			});
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},
		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0.1) {
				valor = e.target.value;
			}

			this.frmDatosEnvio.monto = this.$refs.layout.round(
				valor,
				numero_decimales
			);
		},
		consultar() {
			let self = this;
			axios
				.post(route("caj.listar_transferencia_pagos_get"), this.consulta)
				.then((response) => {
					self.datos_tabla = response.data;
				});
		},
	},
};
</script>

<style lang="css">
.slot-cuenta-lista {
	width: 60% !important;
	margin-left: 20% !important;
}

@media only screen and (max-width: 900px) {
	.slot-cuenta-lista {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>


