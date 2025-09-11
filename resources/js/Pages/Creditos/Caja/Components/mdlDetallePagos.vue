<template>
	<div id="mdlDetallePagos" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-40 mdlDetallePagos">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'PAGOS REALIZADOS'"
						:nombre_modal="'mdlDetallePagos'"
					>
					</headerCloseModal>

					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item" role="presentation">
								<a
									class="nav-link active tab-title"
									id="pagoCuotas-tab"
									data-toggle="tab"
									href="#pagoCuotas"
									role="tab"
									aria-controls="pagoCuotas"
									aria-selected="true"
									>CUOTAS
								</a>
							</li>
							<li class="nav-item" role="presentation">
								<a
									class="nav-link tab-title"
									id="pagoMoras-tab"
									data-toggle="tab"
									href="#pagoMoras"
									role="tab"
									aria-controls="pagoMoras"
									aria-selected="false"
									>MORAS</a
								>
							</li>
							<li class="nav-item" role="presentation">
								<a
									class="nav-link tab-title"
									id="pagoNotificaciones-tab"
									data-toggle="tab"
									href="#pagoNotificaciones"
									role="tab"
									aria-controls="pagoNotificaciones"
									aria-selected="false"
									>NOTIFICACIONES</a
								>
							</li>
						</ul>

						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="pagoCuotas"
								role="tabpanel"
								aria-labelledby="pagoCuotas-tab"
							>
								<table class="table" id="tblPagoCuotas" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 100px !important">FECHA_PAGO</th>
											<th style="min-width: 50px !important">MONTO</th>
											<th style="min-width: 20px !important">CUOTA</th>
											<th style="min-width: 50px !important">CAJA</th>
											<th style="min-width: 110px !important">COMENTARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in pago_cuotas"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td align="center">
												{{ index + 1 }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">
												{{ item.numero_cuota }}
											</td>
											<td align="center">
												{{ item.usuario_caja }}
											</td>

											<td align="left">
												{{ item.comentario == null ? "-" : item.comentario }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div
								class="tab-pane fade"
								id="pagoMoras"
								role="tabpanel"
								aria-labelledby="datosParienteAval2-tab"
							>
								<table class="table" id="tblPagoMoras" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 100px !important">FECHA_PAGO</th>
											<th style="min-width: 50px !important">MONTO</th>
											<th style="min-width: 50px !important">CAJA</th>
											<th style="min-width: 110px !important">COMENTARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in pago_moras"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td align="center">
												{{ index + 1 }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">
												{{ item.usuario_caja }}
											</td>
											<td align="left">
												{{ item.comentario == null ? "-" : item.comentario }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div
								class="tab-pane fade"
								id="pagoNotificaciones"
								role="tabpanel"
								aria-labelledby="pagoNotificaciones-tab"
							>
								<table
									class="table"
									id="tblPagoNotificaciones"
									width="100% !important"
								>
									<thead>
										<tr>
											<th style="min-width: 20px !important">N°</th>
											<th style="min-width: 100px !important">FECHA_PAGO</th>
											<th style="min-width: 50px !important">MONTO</th>
											<th style="min-width: 50px !important">CAJA</th>
											<th style="min-width: 110px !important">COMENTARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in pago_notificaciones"
											:key="index"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td align="center">
												{{ index + 1 }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
											<td align="center">
												{{ item.usuario_caja }}
											</td>
											<td align="left">
												{{ item.comentario == null ? "-" : item.comentario }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
export default {
	components: { headerCloseModal },
	data() {
		return {
			pago_cuotas: [],
			pago_moras: [],
			pago_notificaciones: [],
		};
	},
	mounted() {
		this.TablaPagoCuotas();
		this.TablaPagoMoras();
		this.TablaPagoNotificaciones();
	},
	watch: {
		pago_cuotas() {
			$("#tblPagoCuotas").DataTable().destroy();
			this.TablaPagoCuotas();
		},
		pago_moras() {
			$("#tblPagoMoras").DataTable().destroy();
			this.TablaPagoMoras();
		},
		pago_notificaciones() {
			$("#tblPagoNotificaciones").DataTable().destroy();
			this.TablaPagoNotificaciones();
		},
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
		TablaPagoCuotas() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblPagoCuotas").DataTable({
					scrollY: "200px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
					},
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
		TablaPagoMoras() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblPagoMoras").DataTable({
					scrollY: "200px",

					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
					},
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
		TablaPagoNotificaciones() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblPagoNotificaciones").DataTable({
					scrollY: "200px",

					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
					},
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
	},
};
</script>

<style lang="css">
.mdlDetallePagos {
	margin-top: 8%;
}
@media only screen and (max-width: 900px) {
	.mdlDetallePagos {
		margin-top: 27%;
	}
}
</style>

