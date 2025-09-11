<template>
	<div>
		<layout ref="layout">
			<div class="slot_body slot-devolucion" slot="component-view">
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'DEVOLUCIÓN - SUMINISTROS'"></headerClose>
						<div class="card-body card-block">
							<div class="form-row">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center mayus"
										v-model="agencia_seleccionada"
										@change="FiltrarResponsables"
									>
										<option :value="0" disabled>Seleccione...</option>
										<option
											v-for="(item, index) in agencias_permitidas"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
								<div class="input-group col-md-5">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>RESPONSABLE</span
										>
									</div>
									<select
										class="form-control center"
										:disabled="agencia_seleccionada == 0"
										@change="ListarSuministros"
										v-model.number="responsable_seleccionado"
									>
										<option :value="0">Seleccione...</option>
										<option
											v-for="(item, index) in responsables_filtrados"
											:key="index"
											:value="item.id"
										>
											{{ item.abreviacion + " - " + item.usuario }}
										</option>
									</select>
								</div>
								<div class="input-group col-md-3">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">TIPO</span>
									</div>
									<select
										class="form-control center"
										:disabled="lista_suministros.length == 0"
										v-model="filtros_tabla['tipo_id'].value"
									>
										<option :value="null">TODOS</option>
										<option
											v-for="(item, index) in tipos"
											:key="index"
											:value="item.id"
										>
											{{ item.tipo }}
										</option>
									</select>
								</div>
							</div>

							<div class="card-title mb-1 mt-1">LISTA DE RESULTADOS</div>

							<div class="form-row mb-1 justify-content-md-center">
								<div class="input-group col-md-6 col-7">
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
										placeholder="INGRESE EL NOMBRE DEL SUMINISTRO"
										v-model="filtros_tabla['suministro'].value"
										:disabled="lista_suministros.length == 0"
									/>
								</div>
								<div class="col-md-3 col-2 ml-1">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="CanastaDevolucion"
											title="Devolver SUMINISTROS"
											:disabled="canasta_devolucion.length == 0"
										>
											<span class="icon text-white">
												<i class="fas fa-undo"></i>
											</span>
											<span class="text">DEVOLVER</span>
										</button>

										<button
											class="btn btn-danger btn-icon-split"
											@click="CanastaDevolucionBaja"
											title="Bajar SUMINISTROS"
											:disabled="canasta_devolucion.length == 0"
										>
											<span class="icon text-white">
												<i class="fas fa-times"></i>
											</span>
											<span class="text">BAJA</span>
										</button>
									</div>
								</div>
							</div>

							<DataTable
								:value="lista_suministros_filtrado"
								:scrollable="true"
								scrollDirection="both"
								:scrollHeight="String(windowHeigth * 0.53) + 'px'"
								showGridlines
								:paginator="true"
								:rows="100"
								paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
								currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
							>
								<Column
									field="seleccionar"
									header="CHECK"
									:styles="{
										width: '50px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data, index }">
										<div class="checkbox">
											<label
												style="
													font-size: 1.7em;
													margin-bottom: 0 !important;
													height: 28.6px !important;
													margin-left: 5px;
													margin-top: 5px;
												"
												><input
													type="checkbox"
													:value="data"
													:id="'chb' + index"
													v-model="canasta_devolucion" /><span
													class="cr"
													style="margin-right: 0 !important"
													><i class="cr-icon fa fa-check"></i></span
											></label>
										</div>
									</template>
								</Column>

								<Column
									field="fecha"
									header="FECHA_ASIGNADO"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
									><template #body="{ data }">
										<div class="bolder">
											{{ JSON.parse(data.datos_creacion).fecha }}
										</div>
									</template></Column
								>
								<Column
									field="agencia"
									header="AGENCIA"
									:styles="{
										width: '200px',
										justifyContent: 'left',
									}"
								></Column>
								<Column
									field="suministro"
									header="SUMINISTRO"
									:styles="{
										width: '200px',
										justifyContent: 'left',
										fontWeight: 'bolder',
									}"
								></Column>
								<Column
									field="codigo"
									header="CÓDIGO"
									:styles="{
										width: '70px',
										justifyContent: 'center',
									}"
								></Column>
								<Column
									field="cantidad"
									header="CANTIDAD"
									:styles="{
										width: '70px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
										{{ roundTo(data.cantidad, 2) }}</template
									>
								</Column>

								<Column
									field="medicion"
									header="MEDICIÓN"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
								></Column>
								<Column
									field="usuario_responsable"
									header="RESPONSABLE"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								>
								</Column>

								<Column
									field="usuario_asignador"
									header="ASIGNADO_POR"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								></Column>
								<template #empty> No hay SUMINISTROS encontrados.</template>
							</DataTable>
						</div>
					</div>
				</div>
			</div>
		</layout>

		<!-- Modal -->
		<mdlCanastaDevolucion
			:condiciones="condiciones"
			ref="mdlCanastaDevolucion"
		></mdlCanastaDevolucion>
	</div>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import mdlCanastaDevolucion from "@/Pages/Logistica/Suministros/Components/mdlCanastaDevolucion.vue";
export default {
	components: {
		layout,
		headerClose,

		DataTable,
		Column,

		mdlCanastaDevolucion,
	},
	props: {
		tipos: Array,
		condiciones: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			submited: false,
			agencia_seleccionada: 0,
			responsable_seleccionado: 0,
			responsables_filtrados: [],
			lista_suministros: [],
			canasta_devolucion: [],
			agencias: [],
			agencias_permitidas: [],

			filtros_tabla: {
				suministro: { value: null },
				tipo_id: { value: null },
			},
		};
	},
	computed: {
		lista_suministros_filtrado() {
			const filtro_suministro = this.filtros_tabla["suministro"].value;
			const filtro_tipo = this.filtros_tabla["tipo_id"].value;

			return this.lista_suministros.filter((item) => {
				if (filtro_suministro && filtro_suministro.length >= 3) {
					if (
						!item.suministro
							.toLowerCase()
							.includes(filtro_suministro.toLowerCase())
					) {
						return false;
					}
				}

				if (filtro_tipo) {
					if (item.tipo_id !== filtro_tipo) {
						return false;
					}
				}
				return true;
			});
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
		});

		this.listar_agencias();
	},

	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_SUMINISTROS/DEVOLUCION"
			);
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			return parseFloat(valor).toFixed(numero_decimales);
		},

		async FiltrarResponsables() {
			const params = {
				agencia_id: this.agencia_seleccionada,
				encargado_agencia: false,
			};

			return await axios
				.get(route("log.responsables.por_agencia"), { params })
				.then((response) => {
					this.responsables_filtrados = response.data.responsables;
					this.responsable_seleccionado = 0;
					this.canasta_devolucion = [];
				});
		},
		async ListarSuministros() {
			Swal.fire({
				title: "BUSCANDO...",
				showConfirmButton: false,
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					try {
						await this.Buscar();

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
		},
		async Buscar() {
			this.canasta_devolucion = [];
			const params = {
				responsable_id: this.responsable_seleccionado,
				estado: "ASIGNADO",
			};
			// this.$inertia.get(route("log.sum_asignados.por_responsable"), params);
			// return false;

			const response = await axios.get(
				route("log.sum_asignados.por_responsable"),
				{
					params,
				}
			);

			return (this.lista_suministros = response.data.lista_suministros);
		},

		async CanastaDevolucion() {
			const modal = this.$refs.mdlCanastaDevolucion;
			modal.submited = false;
			modal.title_modal = "DEVOLUCIÓN DE SUMINISTROS";
			modal.modo = "devolucion";
			modal.frmCanastaDevolucion.canasta_devolucion = this.canasta_devolucion;
			modal.frmCanastaDevolucion.agencia_id = this.agencia_seleccionada;
			modal.frmCanastaDevolucion.agencia = this.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			)[0].agencia;
			let responsable_filtrado = this.responsables_filtrados.filter(
				(item) => item.id == this.responsable_seleccionado
			);
			modal.frmCanastaDevolucion.responsable =
				responsable_filtrado[0].abreviacion +
				" - " +
				responsable_filtrado[0].usuario;

			$("#documento").val("");
			modal.frmCanastaDevolucion.documento = null;

			$("#mdlCanastaDevolucion").css("display", "block");
		},
		async CanastaDevolucionBaja() {
			const modal = this.$refs.mdlCanastaDevolucion;
			modal.submited = false;
			modal.title_modal = "BAJA DE SUMINISTROS";
			modal.modo = "baja";

			let arraytest = JSON.parse(JSON.stringify(this.canasta_devolucion));

			const condicion_devolucion = this.condiciones.find(
				(item) => item.condicion == "OBSOLETO"
			);

			arraytest.forEach((objeto) => {
				objeto.condicion_devolucion = condicion_devolucion.id;
				objeto.valor_devolucion = "0.00";
			});

			modal.frmCanastaDevolucion.canasta_devolucion = arraytest;

			modal.frmCanastaDevolucion.agencia_id = this.agencia_seleccionada;
			modal.frmCanastaDevolucion.agencia = this.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			)[0].agencia;
			let responsable_filtrado = this.responsables_filtrados.filter(
				(item) => item.id == this.responsable_seleccionado
			);
			modal.frmCanastaDevolucion.responsable =
				responsable_filtrado[0].abreviacion +
				" - " +
				responsable_filtrado[0].usuario;

			$("#documento").val("");
			modal.frmCanastaDevolucion.documento = null;

			$("#mdlCanastaDevolucion").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-devolucion {
	width: 60%;
	margin-left: 20%;
}

@media (max-width: 1366px) {
	.slot-devolucion {
		width: 70%;
		margin-left: 15%;
	}
}
@media (max-width: 900px) {
	.slot-devolucion {
		width: 98%;
		margin-left: 2%;
	}
}
</style>



