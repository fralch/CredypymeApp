<template>
	<div id="mdlParientesAvalesDependientes" class="modal">
		<div class="modal-content mdlParientesAvalesDependientes w-50">
			<div class="content" style="display: block">
				<div class="card">
					<div
						class="
							card-header
							d-flex
							align-items-center
							justify-content-between
						"
					>
						<strong>
							ES
							{{ frmParienteAval.tipo }}
							DE...</strong
						>
						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarModal"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>

					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<table
							class="table table-hover"
							id="tblParientesAvalesDependientes"
						>
							<thead>
								<tr>
									<th style="min-width: 20px !important">N°</th>
									<th style="min-width: 70px !important">ESTADO</th>
									<th style="min-width: 250px !important">APELLIDOS_NOMBRES</th>
									<th style="min-width: 70px !important">DNI</th>
									<th style="min-width: 100px !important">EXPEDIENTE</th>
									<th style="min-width: 70px !important">CALIFICACIÓN</th>
									<th style="min-width: 70px !important">C_RIESGO</th>
									<th style="min-width: 250px !important">DIRECCIÓN</th>
									<th style="min-width: 250px !important">UBICACIÓN</th>
									<th style="min-width: 300px !important">REFERENCIA</th>
									<th style="min-width: 100px !important">FECHA_REG</th>
									<th style="min-width: 70px !important">USUARIO_REG</th>
									<th style="min-width: 100px !important">FECHA_ACT</th>
									<th style="min-width: 70px !important">USUARIO_ACT</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_parientes_avales_dependientes"
									:key="index"
								>
									<td
										class="table-bordered"
										align="center"
										width="70px !important"
									>
										{{ index + 1 }}
									</td>
									<td
										class="table-bordered"
										align="center"
										:class="{
											vinculado: item.vinculado == 1,
											desvinculado: item.vinculado == 0,
										}"
									>
										{{ item.vinculado == 1 ? "Vinculado" : "Desvinculado" }}
									</td>
									<td class="table-bordered">
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td class="table-bordered" align="center">
										{{ item.dni }}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.codigo_expediente == null
												? "-"
												: item.codigo_expediente
										}}
									</td>
									<td class="table-bordered" align="center">-</td>
									<td class="table-bordered" align="center">-</td>
									<td class="table-bordered">
										{{ item.direccion }}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.distrito +
											" - " +
											item.provincia +
											" - " +
											item.departamento
										}}
									</td>
									<td class="table-bordered">
										{{ item.referencia_direccion }}
									</td>
									<td class="table-bordered" align="center">
										{{ JSON.parse(item.datos_creacion).fecha }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.usuario_creacion }}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.datos_actualizacion == null
												? "-"
												: JSON.parse(item.datos_actualizacion).fecha
										}}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.usuario_actualizacion == null
												? "-"
												: item.usuario_actualizacion
										}}
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
export default {
	data() {
		return {
			lista_parientes_avales_dependientes: [],
			frmParienteAval: {
				tipo: null,
			},
		};
	},
	watch: {
		lista_parientes_avales_dependientes() {
			$("#tblParientesAvalesDependientes").DataTable().destroy();
			this.TablaListaParientesAvalesDependientes();
		},
	},
	mounted() {
		this.TablaListaParientesAvalesDependientes();
	},
	methods: {
		TablaListaParientesAvalesDependientes() {
			this.$nextTick(() => {
				$("#tblParientesAvalesDependientes").DataTable({
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
		CerrarModal() {
			$("#mdlParientesAvalesDependientes").css("display", "none");
			this.$parent.ListarParientesAvalesNegocios();
		},
	},
};
</script>

<style lang="css">
.vinculado {
	font-size: 12px !important;
	color: white !important;
	background: var(--verdeOscuroEmpresarial) !important;
}
.desvinculado {
	font-size: 12px !important;
	color: white !important;
	background: var(--plomoOscuroEmpresarial) !important;
}
.mdlParientesAvalesDependientes {
	margin-top: 4% !important;
}
@media (max-width: 900px) {
	.mdlParientesAvalesDependientes {
		width: 99% !important;
		margin-left: 0.5% !important;

		margin-top: 25% !important;
	}
}
</style>
