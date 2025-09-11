<template>
	<layout ref="layout">
		<div
			class="slot_body slot-mis-transferencias"
			slot="component-view"
			v-if="mi_cuenta != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MIS TRANSFERENCIAS - CUENTA'"></headerClose>

					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="enviadas-tab"
									data-toggle="tab"
									href="#enviadas"
									role="tab"
									aria-controls="enviadas"
									aria-selected="true"
									>ENVIADAS</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="recibidas-tab"
									data-toggle="tab"
									href="#recibidas"
									role="tab"
									aria-controls="recibidas"
									aria-selected="false"
									>RECIBIDAS</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="enviadas"
								role="tabpanel"
								aria-labelledby="enviadas-tab"
							>
								<table
									class="table"
									id="tblTransferenciasEnviadas"
									style="width: 100% !important"
								>
									<thead>
										<tr>
											<th style="min-width: 70px">ESTADO</th>
											<th style="min-width: 70px">TIPO</th>
											<th style="min-width: 200px">DESCRIPCIÓN</th>
											<th>MONTO(S/)</th>
											<th>FECHA_HORA_ENVÍO</th>
											<th>DESTINATARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in transferencias_enviadas"
											:key="index"
											class="table-bordered"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td
												align="center"
												:class="[
													item.estado == 'PENDIENTE'
														? 'pendiente'
														: item.estado == 'CONFIRMADO'
														? 'confirmado'
														: 'rechazado',
												]"
											>
												{{ item.estado }}
											</td>
											<td align="center">
												{{ item.tipo_envio }}
											</td>
											<td>
												{{ item.descripcion }}
											</td>
											<td align="right">
												{{ roundTo(item.monto, 2) }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="center">
												{{ item.usuario_destinatario }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div
								class="tab-pane fade"
								id="recibidas"
								role="tabpanel"
								aria-labelledby="recibidas-tab"
							>
								<table
									class="table"
									id="tblTransferenciasRecibidas"
									style="width: 100% !important"
								>
									<thead>
										<tr>
											<th style="min-width: 70px">ESTADO</th>
											<th>TIPO</th>
											<th style="min-width: 200px">DESCRIPCIÓN</th>
											<th>MONTO(S/)</th>
											<th>FECHA_HORA_ENVÍO</th>
											<th>REMITENTE</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in transferencias_recibidas"
											:key="index"
											:id="item.tipo_envio + '-' + item.id"
											class="table-bordered"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
										>
											<td
												align="center"
												:class="[
													item.estado == 'PENDIENTE'
														? 'pendiente'
														: item.estado == 'CONFIRMADO'
														? 'confirmado'
														: 'rechazado',
												]"
											>
												{{ item.estado }}
											</td>
											<td align="center">
												{{ item.tipo_envio }}
											</td>
											<td>
												{{ item.descripcion }}
											</td>
											<td align="right">
												{{ roundTo(item.monto, 2) }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="center">
												{{ item.usuario_remitente }}
											</td>
										</tr>
									</tbody>
								</table>

								<div class="text-right mt-2">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											title="Aceptar"
											@click="Confirmar('ACEPTAR')"
										>
											<span class="icon text-white">
												<i class="fas fa-check"></i>
											</span>
											<span class="text">ACEPTAR</span>
										</button>
										<button
											class="btn btn-danger btn-icon-split"
											title="Rechazar"
											@click="Confirmar('RECHAZAR')"
										>
											<span class="icon text-white">
												<i class="fas fa-times"></i>
											</span>
											<span class="text">RECHAZAR</span>
										</button>
									</div>
								</div>
							</div>
							<hr />
							<div class="text-left">
								<button
									class="btn btn-cancel btn-icon-split"
									@click="Actualizar"
								>
									<span class="icon text-white">
										<i class="fas fa-sync-alt"></i
									></span>
									<span class="text">ACTUALIZAR</span>
								</button>
							</div>
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
	components: { layout, headerClose },
	props: {
		agencia_id: Number,
		transferencias_enviadas: Array,
		transferencias_recibidas: Array,
	},
	data() {
		return {
			submited: false,
			mi_cuenta: null,
		};
	},
	mounted() {
		let mi_cuenta = this.$inertia.page.props.creditos_datos.datos_cuenta;

		if (mi_cuenta == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "No tiene una CUENTA aperturada",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return false;
		} else {
			this.mi_cuenta = mi_cuenta;
		}

		this.TablasTransferencias();
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
		TablasTransferencias() {
			this.$nextTick(() => {
				var table_1 = $("#tblTransferenciasEnviadas").DataTable({
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
					select: {
						style: "single",
						info: false,
					},
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
				var table_1 = $("#tblTransferenciasRecibidas").DataTable({
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
					select: {
						style: "single",
						info: false,
					},
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
			});
		},
		Cerrar() {
			this.$inertia.get(route("cre.index"));
		},
		Confirmar(modo) {
			let row = document
				.getElementById("tblTransferenciasRecibidas")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione una transferencia",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let self = this;
				let row_parts = row.id.split("-");
				let tipo_envio = row_parts[0];
				let id = row_parts[1];

				let transferencia = this.transferencias_recibidas.filter(
					(item) => item.id == id && item.tipo_envio == tipo_envio
				)[0];

				if (transferencia.estado != "PENDIENTE") {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Esta transferencia ya fue CONFIRMADA.",
						allowOutsideClick: true,
					});
					return false;
				}

				Swal.fire({
					title: modo.toUpperCase() + " TRANSFERENCIA",
					text: "¿Desea continuar?",
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
						data.append("modo", modo);

						if (modo == "ACEPTAR") {
							data.append("agencia_id", this.agencia_id);
							data.append("transferencia", JSON.stringify(transferencia));
							self.RutaConfirmar(data);
						} else if (modo == "RECHAZAR") {
							Swal.fire({
								text: "COMENTARIO DE RECHAZO",
								confirmButtonText:
									'<div style="font-size:13px"><i class="fas fa-check"></i>   Aceptar</div>',
								confirmButtonColor: "var(--colorAlto)",
								showCancelButton: true,
								cancelButtonText:
									'<div style="font-size:13px"><i class="fas fa-times"></i>   Cancelar</div>',
								cancelButtonColor: "var(--plomoOscuroEmpresarial)",
								allowOutsideClick: false,
								input: "text",
								customClass: {
									input: "mayus",
								},
								inputValidator: (value) => {
									if (!value) {
										return "*Obligatorio";
									}
								},
							}).then((result) => {
								if (result.isConfirmed) {
									data.append("agencia_id", this.agencia_id);
									data.append("transferencia_id", transferencia.id);
									data.append("tipo_envio", transferencia.tipo_envio);
									data.append("comentario", result.value);
									self.RutaConfirmar(data);
								} else {
									return false;
								}
							});
						}
					} else {
						return false;
					}
				});
			}
		},

		RutaConfirmar(data) {
			this.$inertia.post(route("cue.mis_transferencias.confirmar"), data, {
				preserveScroll: true,
				onStart: () => {
					Swal.fire({
						title: "REGISTRANDO",
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
					});
				},
			});
		},

		Actualizar() {
			this.$inertia.get(
				route("cue.mis_transferencias"),
				{},
				{
					preserveScroll: true,
					onStart: () => {
						Swal.fire({
							title: "BUSCANDO",
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
							title: "¡ACTUALIZADO!",
							allowOutsideClick: true,
						});
					},
				}
			);
		},
	},
};
</script>

<style lang="css">
.slot-mis-transferencias {
	width: 50% !important;
	margin-top: 10% !important;
	margin-left: 25% !important;
}
.pendiente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.confirmado {
	background-color: var(--plomoOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	background-color: var(--red) !important;
	color: white !important;
}

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
@media (max-width: 900px) {
	.slot-mis-transferencias {
		width: 90% !important;
		margin-left: 5% !important;
	}
}
</style>

