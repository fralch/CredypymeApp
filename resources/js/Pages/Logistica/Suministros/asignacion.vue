<template>
	<div>
		<layout ref="layout">
			<div class="slot_body slot-asignacion" slot="component-view">
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'ASIGNACIÓN - SUMINISTROS'"></headerClose>

						<div class="card-body card-block">
							<div class="row col-md-10 justify-content-md-center offset-md-1">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center mayus"
										v-model="agencia_seleccionada"
										@change="ListarSuministros"
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
								<div class="input-group col-md-4">
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
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">CLASIF.</span>
									</div>
									<select
										class="form-control center"
										:disabled="lista_suministros.length == 0"
										v-model="filtros_tabla['clasificacion'].value"
									>
										<option :value="null">TODOS</option>
										<option :value="'SUMINISTRO'">SUMINISTRO</option>
										<option :value="'GASTO'">GASTO</option>
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
								<div class="col-md-2 col-2 ml-1">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="CanastaAsignacion"
											title="Asignar SUMINISTROS"
											:disabled="canasta_asignacion.length == 0"
										>
											<span class="icon text-white">
												<i class="fas fa-user-check"></i>
											</span>
											<span class="text">ASIGNAR</span>
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
									field="check"
									header="CHECK"
									:styles="{
										width: '70px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
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
													:id="'chb' + data.id"
													v-model="canasta_asignacion" /><span
													class="cr"
													style="margin-right: 0 !important"
													><i class="cr-icon fa fa-check"></i></span
											></label>
										</div>
									</template>
								</Column>
								<Column
									field="estado"
									header="ESTADO"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
									><template #body="{ data }"
										><div class="disponible">
											{{ data.estado }}
										</div></template
									></Column
								><Column
									field="agencia"
									header="AGENCIA"
									:styles="{
										width: '150px',
										justifyContent: 'left',
									}"
								></Column
								><Column
									field="suministro"
									header="SUMINISTRO"
									:styles="{
										width: '150px',
										justifyContent: 'left',
										fontWeight: 'bolder',
									}"
								></Column>
								<Column
									field="codigo"
									header="CÓDIGO"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
								></Column>
								<Column
									field="cantidad"
									header="CANTIDAD"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">{{
										roundTo(data.cantidad, 2)
									}}</template>
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
									field="valor_unitario"
									header="VALOR_UNIT(S/)"
									:styles="{
										width: '100px',
										justifyContent: 'right',
									}"
								>
									<template #body="{ data }"
										>S/ {{ RedondearVista(data.valor_unitario, 2) }}</template
									>
								</Column>
								<Column
									field="condicion"
									header="CONDICIÓN"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
								></Column>
								<Column
									field="clasificacion"
									header="CLASIFICACION"
									:styles="{
										width: '100px',
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
		<mdlCanastaAsignacion
			ref="mdlCanastaAsignacion"
			:windowWidth="windowWidth"
			:windowHeigth="windowHeigth"
		></mdlCanastaAsignacion>
	</div>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import mdlCanastaAsignacion from "@/Pages/Logistica/Suministros/Components/mdlCanastaAsignacion.vue";
export default {
	components: {
		layout,
		headerClose,

		DataTable,
		Column,

		mdlCanastaAsignacion,
	},
	props: {
		tipos: Array,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			submited: false,
			agencia_seleccionada: 0,
			lista_suministros: [],
			canasta_asignacion: [],
			agencias: [],
			agencias_permitidas: [],

			filtros_tabla: {
				suministro: { value: null },
				tipo_id: { value: null },
				clasificacion: { value: null },
			},
		};
	},
	computed: {
		lista_suministros_filtrado() {
			const filtro_suministro = this.filtros_tabla["suministro"].value;
			const filtro_tipo = this.filtros_tabla["tipo_id"].value;
			const filtro_clasificacion = this.filtros_tabla["clasificacion"].value;

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
				if (filtro_clasificacion) {
					if (item.clasificacion !== filtro_clasificacion) {
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
				"LOGISTICA_SUMINISTROS/ASIGNACION"
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
		RedondearVista(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			let resultado = parseFloat(valor).toLocaleString("es-PE", {
				minimumFractionDigits: numero_decimales,
				maximumFractionDigits: numero_decimales,
			});

			return resultado;
		},
		async ListarSuministros() {
			this.canasta_asignacion = [];
			const agencia_id = this.agencia_seleccionada;

			const params = {
				agencia_id: agencia_id,
				estado: "DISPONIBLE",
			};

			// this.$inertia.get(route("log.sum.almacen.buscar"), params);
			// return false;

			Swal.fire({
				title: "BUSCANDO...",
				showConfirmButton: false,
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();

					try {
						const response = await axios.get(route("log.sum.almacen.buscar"), {
							params,
						});

						this.lista_suministros = response.data.lista_suministros;

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

		async CanastaAsignacion() {
			let mdlCanastaAsignacion = this.$refs.mdlCanastaAsignacion;
			mdlCanastaAsignacion.submited = false;
			mdlCanastaAsignacion.title_modal = "ASIGNACIÓN SUMINISTROS";
			mdlCanastaAsignacion.frmCanastaAsignacion.canasta_asignacion =
				this.canasta_asignacion;
			mdlCanastaAsignacion.frmCanastaAsignacion.agencia_id =
				this.agencia_seleccionada;
			mdlCanastaAsignacion.frmCanastaAsignacion.agencia = this.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			)[0].agencia;

			$("#documento").val("");
			mdlCanastaAsignacion.frmCanastaAsignacion.documento = null;

			await mdlCanastaAsignacion.FiltrarResponsables();
			$("#mdlCanastaAsignacion").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-asignacion {
	width: 60%;
	margin-left: 20%;
}
.disponible {
	background-color: var(--green) !important;
	color: white !important;
}

@media (max-width: 1366px) {
	.slot-asignacion {
		width: 70%;
		margin-left: 15%;
	}
}
@media (max-width: 900px) {
	.slot-asignacion {
		width: 98%;
		margin-left: 2%;
	}
}
</style>
