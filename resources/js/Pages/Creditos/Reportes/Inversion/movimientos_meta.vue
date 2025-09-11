<template>
	<layout ref="layout">
		<div class="slot_body slot-movimientos-meta" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MOVIMIENTOS DE INVERSIÓN META'"></headerClose>

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
											v-model="agencia_busqueda"
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
									v-if="agencia_busqueda != 0 && agencia_busqueda != null"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="input-group col-md-6 mb-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbPorProductoMeta"
											v-model="por_producto_meta"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbPorProductoMeta"
										style="font-size: 13px"
										v-if="windowWidth >= 900"
									>
										PRODUCTO META
									</label>
									<label
										class="input-group-text prepend-title"
										for="chbPorProductoMeta"
										style="font-size: 13px"
										v-if="windowWidth < 900"
									>
										P. META
									</label>
								</div>

								<select
									class="form-control center"
									v-model="producto_meta_seleccionado"
									:disabled="!por_producto_meta"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in producto_meta_filtrados"
										:key="index"
										:value="item.id"
									>
										{{ item.producto }}
									</option>
								</select>
							</div>
							<div class="input-group col-md-3 mb-1">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbTipoMovimiento"
											v-model="por_tipo_movimiento"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbTipoMovimiento"
										style="font-size: 13px"
									>
										TIPO MOV.
									</label>
								</div>

								<select
									class="form-control center"
									v-model="tipo_movimiento_seleccionado"
									:disabled="!por_tipo_movimiento"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option value="I">ABONO</option>
									<option value="E">RETIRO</option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<!-- -------------------------------------------------------- -->

							<div class="col-md-12">
								<div class="card-title">LISTA DE RESULTADOS</div>
								<table class="table" id="tblMovimientos" width="100%">
									<thead>
										<tr>
											<th style="min-width: 30px !important">N°</th>
											<th style="min-width: 250px !important">CLIENTE</th>
											<th style="min-width: 250px !important">PRODUCTO_META</th>

											<th style="min-width: 70px !important">TIPO_MOV.</th>
											<th style="min-width: 80px !important">MONTO</th>
											<th style="min-width: 120px !important">
												FECHA_REGISTRO
											</th>
											<th style="min-width: 100px !important">CAJA</th>
											<th style="min-width: 250px !important">COMENTARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_movimientos"
											:key="index"
											class="table-bordered"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td align="center">{{ index + 1 }}</td>
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
												{{ item.producto }}
											</td>
											<td align="center">
												{{ item.tipo == "I" ? "ABONO" : "RETIRO" }}
											</td>
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">{{ item.fecha_movimiento }}</td>
											<td align="center">{{ item.usuario_caja }}</td>
											<td>{{ item.comentario }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar()"
								:disabled="lista_movimientos.length == 0"
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

export default {
	components: {
		layout,
		headerClose,
	},
	props: { lista_productos_meta: Array },

	data() {
		return {
			agencias_permitidas: [],
			agencia_busqueda: 0,

			fecha_desde: null,
			fecha_hasta: null,

			producto_meta_filtrados: [],
			por_producto_meta: false,
			producto_meta_seleccionado: 0,

			por_tipo_movimiento: false,
			tipo_movimiento_seleccionado: 0,

			lista_movimientos: [],
			totales: {},
			windowWidth: window.innerWidth,
		};
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
				} else {
					this.agencia_busqueda = null;
				}
			}
		},
		agencia_busqueda() {
			this.FechaActual();
			this.producto_meta_seleccionado = 0;

			this.FiltrarProductoMeta();
		},

		lista_movimientos() {
			$("#tblMovimientos").DataTable().destroy();
			this.TablaMovimientos();
		},
		por_tipo_movimiento() {
			this.tipo_movimiento_seleccionado = 0;
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaMovimientos();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		FiltrarProductoMeta() {
			this.producto_meta_filtrados = this.lista_productos_meta.filter(
				(item) => item.agencia_id == this.agencia_busqueda
			);
		},
		async FechaActual() {
			if (this.agencia_busqueda == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_busqueda
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_REPORTES/INVERSION_MOVIMIENTOS_META"
			);
			this.filtro_usuario = true;
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

		TablaMovimientos() {
			this.$nextTick(() => {
				var table = $("#tblMovimientos").DataTable({
					scrollY: "400px",
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

		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("por_tipo_movimiento", this.por_tipo_movimiento);
			data.append("por_producto_meta", this.por_producto_meta);

			if (this.por_producto_meta) {
				data.append("producto_meta_id", this.producto_meta_seleccionado);
			}

			if (this.por_tipo_movimiento) {
				data.append(
					"tipo_movimiento_seleccionado",
					this.tipo_movimiento_seleccionado
				);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("rep.inv.movimientos_meta.buscar"), data);
					axios
						.post(route("rep.inv.movimientos_meta.buscar"), data)
						.then(function (response) {
							if (response.data.lista_movimientos.length == 0) {
								self.lista_movimientos = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_movimientos = response.data.lista_movimientos;
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
		Exportar() {
			let data = new FormData();
			data.append("lista_movimientos", JSON.stringify(this.lista_movimientos));
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);

			//   this.$inertia.post(route("rep.inv.movimientos_meta.exportar"), data);
			//   return false;

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					axios
						.post(route("rep.inv.movimientos_meta.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptMovimientosMeta.xlsx";
							link.click();
							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
								timer: 2000,
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
.slot-movimientos-meta {
	width: 60% !important;
	margin-left: 20% !important;
}

@media (max-width: 1280px) {
	.slot-movimientos-meta {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
