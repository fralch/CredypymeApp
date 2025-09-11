<template>
	<layout ref="layout">
		<div class="slot_body slot-productividad-asesor" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MI ' : '') + 'PRODUCTIVIDAD COMO ASESOR'
						"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-10">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
										</div>
										<select
											class="form-control center"
											v-model="agencia_seleccionada"
										>
											<option
												v-for="item in agencias_permitidas"
												:key="item.id"
												:value="item.id"
											>
												{{ item.agencia }}
											</option>
										</select>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_desde"
											style="font-size: 15px"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_hasta"
											style="font-size: 15px"
										/>
									</div>
								</div>
							</fieldset>

							<div class="col-md-1 ml-3">
								<button
									class="btn btn-action btn-icon-split mt-3"
									title="Buscar"
									@click="Buscar"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="form-row col-md-12">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input
												type="checkbox"
												id="chbPorAsesor"
												v-model="por_asesor"
												:disabled="modo == 'personal'"
											/>
										</div>
										<label
											class="input-group-text prepend-title"
											for="chbPorAsesor"
											style="font-size: 13px"
										>
											ASESOR
										</label>
									</div>
									<div class="input-group-prepend"></div>
									<select
										class="form-control center"
										v-model="asesor_seleccionado"
										:disabled="!por_asesor"
									>
										<option :value="0" disabled selected>Seleccione...</option>
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
						</div>

						<div class="card-title mt-2">LISTA DE RESULTADOS</div>

						<table class="table" id="tblProductividadAgrupado" width="100%">
							<thead>
								<tr>
									<th style="min-width: 100px !important">ASESOR</th>
									<th style="min-width: 30px !important">CANT.</th>
									<th style="min-width: 100px !important">SALDO_CAPITAL</th>
									<th style="min-width: 80px !important">CAPITAL</th>
									<th style="min-width: 80px !important">INTERES</th>
									<th style="min-width: 80px !important">REDONDEO</th>
									<th style="min-width: 80px !important">MORA</th>
									<th style="min-width: 80px !important">NOTIF.</th>
									<th style="min-width: 80px !important">DSCTO_MORAS</th>
									<th style="min-width: 80px !important">DSCTO_NOTIF</th>
									<th style="min-width: 80px !important">DSCTO_INTER</th>
									<th style="min-width: 80px !important">TOTAL_PAGADO</th>
									<th style="min-width: 100px !important">PRODUCTIVIDAD</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_cobranzas_agrupado"
									:key="index"
									class="table-bordered"
									:class="[index % 2 == 0 ? 'verde-claro' : '']"
								>
									<td align="center">
										{{ item.usuario_asesor }}
									</td>
									<td align="center">
										{{ item.cantidad }}
									</td>
									<td align="right">S/ {{ roundTo(item.saldo_capital, 2) }}</td>
									<td align="right">S/ {{ roundTo(item.capital, 2) }}</td>
									<td align="right">S/ {{ roundTo(item.interes, 2) }}</td>
									<td align="right">S/ {{ roundTo(item.redondeo, 2) }}</td>
									<td align="right">S/ {{ roundTo(item.moras, 2) }}</td>
									<td align="right">
										S/ {{ roundTo(item.notificaciones, 2) }}
									</td>

									<td align="right">- S/ {{ roundTo(item.dscto_mora, 2) }}</td>
									<td align="right">
										- S/ {{ roundTo(item.dscto_notificaciones, 2) }}
									</td>
									<td align="right">
										- S/ {{ roundTo(item.dscto_interes, 2) }}
									</td>

									<td align="right">S/ {{ roundTo(item.total_pago, 2) }}</td>
									<td align="right">
										S/ {{ roundTo(item.total_productividad, 2) }}
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th class="font-11 text-right blue" colspan="2">TOTAL</th>

									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_saldo_capital, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_capital, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_interes, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_redondeo, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_mora, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_notificaciones, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_dscto_mora, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_dscto_notificaciones, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_dscto_interes, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_pago, 2) }}
									</th>
									<th class="font-11 text-right blue">
										S/ {{ roundTo(totales.total_productividad, 2) }}
									</th>
								</tr>
							</tfoot>
						</table>

						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar('AGRUPADO', 'XLSX')"
								:disabled="lista_cobranzas_agrupado.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-file-excel"></i>
								</span>
								<span class="text">EXPORTAR</span>
							</button>
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

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		modo: String,
		usuarios: Array,
	},

	data() {
		return {
			submited: false,

			agencias_permitidas: [],
			agencia_seleccionada: null,

			fecha_desde: null,
			fecha_hasta: null,

			por_asesor: this.modo == "personal" ? true : false,

			usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],
			asesor_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,

			lista_cobranzas_agrupado: [],
			totales: {},

			datos: [],
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
					this.agencia_seleccionada = null;
				}
			}
		},
		agencia_seleccionada() {
			this.FechaActual();
			this.FiltrarUsuarios();
		},
		por_asesor() {
			this.asesor_seleccionado = 0;
		},

		lista_cobranzas_agrupado() {
			$("#tblProductividadAgrupado").DataTable().destroy();
			this.TablaProductividadAgrupado();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();

		this.TablaProductividadAgrupado();
	},

	methods: {
		async FechaActual() {
			if (this.agencia_seleccionada == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_seleccionada
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			if (valor == 0) {
				return "-";
			} else {
				return parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
		},
		formato_fecha(value) {
			if (value != null) {
				return (
					String(value).substring(8, 10) +
					"/" +
					String(value).substring(5, 7) +
					"/" +
					String(value).substring(0, 4)
				);
			} else {
				return null;
			}
		},
		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_MI_PRODUCTIVIDAD_ASESOR"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CREDITOS_PRODUCTIVIDAD_ASESOR"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},

		TablaProductividadAgrupado() {
			this.$nextTick(() => {
				$("#tblProductividadAgrupado").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
					language: {
						retrieve: true,
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});
			});
		},

		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.asesor_seleccionado = 0;

				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			} else if (this.modo == "personal") {
				return false;
			}
		},

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("por_asesor", this.por_asesor);

			if (this.por_asesor) {
				data.append("asesor_id", this.asesor_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					// Swal.showLoading();
					// this.$inertia.post(
					// 	route("rep.cre.productividad_asesor.buscar"),
					// 	data
					// );
					axios
						.post(route("rep.cre.productividad_asesor.buscar"), data)
						.then(function (response) {
							if (response.data.lista_cobranzas_agrupado.length == 0) {
								self.lista_cobranzas_agrupado = [];
								self.totales = 0;
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_cobranzas_agrupado =
									response.data.lista_cobranzas_agrupado;
								self.totales = response.data.totales;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
									timer: 1200,
									showConfirmButton: false,
								});
							}
						});
				},
			});
		},

		Exportar(modo, tipo) {
			let data = new FormData();

			data.append("datos", JSON.stringify(this.lista_cobranzas_agrupado));
			data.append("totales", JSON.stringify(this.totales));
			data.append("modo", modo);
			data.append("tipo", tipo);

			let titulo = "";

			if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			} else if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			}

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(
					// 	route("rep.cre.productividad_asesor.exportar"),
					// 	data
					// );
					axios
						.post(route("rep.cre.productividad_asesor.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let linkSource =
									"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
									response.data;
								let downloadLink = document.createElement("a");
								let fileName = "rptProductividadAgrupado.xlsx";

								downloadLink.href = linkSource;
								downloadLink.download = fileName;
								downloadLink.click();
							} else if (tipo == "PDF") {
								const contentType = "application/pdf",
									b64Data = response.data, //La información en base 64
									blob = b64toBlob(b64Data, contentType), //Decodificar la información
									blobUrl = URL.createObjectURL(blob);
								// Crear un IFrame.
								let iframe = document.createElement("iframe");
								// Ocultar el IFrame.
								iframe.style.display = "none";
								// Definir el source.
								iframe.src = blobUrl;
								// Añadir el IFrame a una página web.
								document.body.appendChild(iframe);
								iframe.contentWindow.focus();
								iframe.contentWindow.print(); // Imprimir.
							}

							return Swal.fire({
								icon: "success",
								title: "¡LISTO!",
								timer: 1200,
								showConfirmButton: false,
							});
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-productividad-asesor {
	width: 60% !important;
	margin-left: 20% !important;
}

@media (max-width: 900px) {
	.slot-productividad-asesor {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
