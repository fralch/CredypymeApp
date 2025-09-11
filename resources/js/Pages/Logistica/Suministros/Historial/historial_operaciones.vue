<template>
	<layout ref="layout">
		<div class="slot_body slot-historial-operaciones" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'HISTORIAL DE OPERACIONES'"></headerClose>
					<div class="card-title">PANEL DE BUSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<fieldset class="form-group col-md-6">
								<legend>
									<label class="label-title">FILTROS DE BÚSQUEDA</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="datos_fecha.fecha_desde"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
									<div class="input-group col-md-6">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											type="date"
											class="form-control center bolder"
											v-model="datos_fecha.fecha_hasta"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
									<div class="col-md-1 text-right" v-if="windowWidth < 900">
										<button
											class="btn btn-action btn-icon-split mt-3"
											title="Buscar"
											@click="Buscar"
										>
											<span class="icon text-white" style="font-size: 15px">
												<i class="fas fa-search"></i>
											</span>
										</button>
									</div>
								</div>
							</fieldset>
							<div class="col-md-1 ml-3" v-if="windowWidth >= 900">
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
						</div>
					</div>
					<div class="card-title">RESULTADOS DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<DataTable
							:value="lista_operaciones"
							:scrollable="true"
							scrollDirection="both"
							:scrollHeight="String(windowHeigth * 0.5) + 'px'"
							showGridlines
						>
							<Column
								field="nombre_agencia"
								header="AGENCIA"
								:styles="{
									width: '110px',
									justifyContent: 'center',
									fontWeight: 'bolder',
								}"
								frozen
							>
							</Column>

							<Column
								field="total_inicial"
								header="TOTAL_INICIAL"
								:styles="{
									width: '100px',
									justifyContent: 'right',
									backgroundColor: 'var(--plomoOscuroEmpresarial)',
									color: 'white',
								}"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px">
										{{ roundTo(data.total_inicial, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_compras"
								header="COMPRAS (+)"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px; color: green">
										{{ roundTo(data.total_compras, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_asignaciones"
								header="ASIGNA. (-)"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px; color: red">
										{{ roundTo(data.total_asignaciones, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_devoluciones"
								header="DEVOLUC. (+)"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px; color: green">
										{{ roundTo(data.total_devoluciones, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_envios"
								header="ENVÍOS (-)"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px; color: red">
										{{ roundTo(data.total_envios, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_recepciones"
								header="RECEPC. (+)"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px; color: green">
										{{ roundTo(data.total_recepciones, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_ventas"
								header="VENTAS (-)"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px; color: red">
										{{ roundTo(data.total_ventas, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="total_final"
								header="TOTAL_FINAL"
								:styles="{
									width: '100px',
									justifyContent: 'right',
									backgroundColor: 'blue',
									color: 'white',
								}"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px">
										{{ roundTo(data.total_final, 2) }}
									</div>
								</template>
							</Column>

							<ColumnGroup type="footer">
								<Row>
									<Column
										:colspan="1"
										footer="TOTAL"
										:footerStyle="{
											width: '110px ',
											backgroundColor: '#244b9a !important',

											fontSize: '14px !important',
											textAlign: 'center',
										}"
									/>
									<Column
										:colspan="1"
										:footer="roundTo(this.sumatoria.inicial, 2)"
										:footerStyle="{
											backgroundColor:
												'var(--plomoClaroEmpresarial) !important',

											width: '100px',
											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'+ ' + roundTo(this.sumatoria.compras, 2)"
										:footerStyle="{
											backgroundColor: 'green !important',
											width: '100px',

											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'- ' + roundTo(this.sumatoria.asignaciones, 2)"
										:footerStyle="{
											backgroundColor: 'red !important',
											width: '100px',

											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'+ ' + roundTo(this.sumatoria.devoluciones, 2)"
										:footerStyle="{
											backgroundColor: 'green !important',
											width: '100px',

											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'- ' + roundTo(this.sumatoria.envios, 2)"
										:footerStyle="{
											backgroundColor: 'red !important',
											width: '100px',

											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'+ ' + roundTo(this.sumatoria.recepciones, 2)"
										:footerStyle="{
											backgroundColor: 'green !important',
											width: '100px',

											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="'- ' + roundTo(this.sumatoria.ventas, 2)"
										:footerStyle="{
											backgroundColor: 'red !important',
											width: '100px',

											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
									<Column
										:colspan="1"
										:footer="roundTo(this.sumatoria.final, 2)"
										:footerStyle="{
											backgroundColor: 'var(--azulClaroEmpresarial) !important',
											width: '100px',
											color: 'black !important',
											fontSize: '14px !important',
											textAlign: 'right',
										}"
									/>
								</Row>
							</ColumnGroup>
							<template #empty> No se encontraron resultados.</template>
						</DataTable>
						<hr />
						<!-- <div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								title="Exportar"
								@click="Exportar()"
							>
								<span class="icon text-white">
									<i class="fas fa-download"></i>
								</span>
								<span class="text">EXPORTAR</span>
							</button>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>
 
<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

import { required } from "vuelidate/lib/validators";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,

		DataTable,
		Column,
		ColumnGroup,
		Row,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			submited: false,

			lista_operaciones: [],
			sumatoria: {},

			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},
		};
	},
	validations: {
		datos_fecha: {
			fecha_desde: { required },
			fecha_hasta: { required },
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
		});

		this.ListarRecursos();
	},

	methods: {
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			if (valor == 0) {
				return "-";
			} else {
				return (
					"S/ " +
					parseFloat(valor).toLocaleString("es-PE", {
						minimumFractionDigits: numero_decimales,
						maximumFractionDigits: numero_decimales,
					})
				);
			}
		},

		async ListarRecursos() {
			// this.$inertia.get(route("log.sum.envios_recepciones.listar_recursos"));
			// return false;

			await axios
				.get(route("log.sum.envios_recepciones.listar_recursos"))
				.then((response) => {
					this.datos_fecha.fecha_desde = response.data.fecha_desde;
					this.datos_fecha.fecha_hasta = response.data.fecha_hasta;
				});
		},
		async Buscar() {
			this.submited = true;

			if (this.$v.datos_fecha.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos que faltan completar, verifique.",
				});

				return false;
			} else {
				const params = {
					fecha_desde: this.datos_fecha.fecha_desde,
					fecha_hasta: this.datos_fecha.fecha_hasta,
				};

				// this.$inertia.get(
				// 	route("log.sum.historial_operaciones.buscar"),
				// 	params
				// );
				// return false;

				Swal.fire({
					title: "BUSCANDO...",
					showConfirmButton: false,
					allowOutsideClick: false,
					didOpen: async () => {
						Swal.showLoading();

						try {
							const response = await axios.get(
								route("log.sum.historial_operaciones.buscar"),
								{
									params,
								}
							);

							$("#slcAgencias").val(0);

							this.lista_operaciones = response.data.lista_operaciones;
							this.sumatoria = response.data.sumatoria;

							// Espera un poco para asegurar cierre suave del primer modal
							await Swal.close();

							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								showConfirmButton: false,
								timer: 1500,
							});
						} catch (error) {
							console.error(error);
							await Swal.close(); // cierra primero el anterior

							Swal.fire({
								icon: "error",
								title: "Error",
								text: `Ha ocurrido un error. Comunicar a SOPORTE: ${error.message}`,
							});
						}
					},
				});
			}
		},
	},
};
</script>

<style lang="css">
.slot-historial-operaciones {
	width: 50%;
	margin-left: 25%;
}

@media (max-width: 1536px) {
	.slot-historial-operaciones {
		width: 60%;
		margin-left: 20%;
	}
}

@media (max-width: 1366px) {
	.slot-historial-operaciones {
		width: 70%;
		margin-left: 15%;
	}
}
@media (max-width: 900px) {
	.slot-historial-operaciones {
		width: 98%;
		margin-left: 2%;
	}
}
</style>
