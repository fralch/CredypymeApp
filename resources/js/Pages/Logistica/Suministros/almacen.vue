<template>
	<div>
		<layout ref="layout">
			<div class="slot_body slot-almacen" slot="component-view">
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'ALMACÉN - SUMINISTROS'"></headerClose>

						<div class="card-body card-block">
							<div class="row justify-content-md-center">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center mayus"
										v-model="agencia_busqueda"
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
							</div>

							<div class="card-title mt-1 mb-1">RESULTADOS DE BÚSQUEDA</div>

							<fieldset class="p-0 pl-1 pr-1 mb-1">
								<legend>
									<label class="label-title">FILTRAR RESULTADOS</label>
								</legend>
								<div class="form-row">
									<div class="form-group col-md-6">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"
													><i class="fas fa-search"></i
												></span>
											</div>
											<input
												class="form-control mayus"
												type="text"
												autocomplete="off"
												spellcheck="false"
												placeholder="INGRESE EL NOMBRE DEL SUMINISTRO"
												v-model="filtros_tabla['suministro'].value"
											/>
										</div>
									</div>
									<div class="form-group col-md-6">
										<div class="row">
											<div class="text-left col-md-6">
												<div class="btn-group" role="group">
													<button
														class="btn btn-action btn-icon-split"
														@click="CanastaCompras"
														title="Comprar SUMINISTROS"
														:disabled="agencia_busqueda == 0"
														v-if="
															$page.props.user_permissions.permisos.includes(
																'LOGISTICA_SUMINISTROS/ALMACEN_COMPRAR'
															)
														"
													>
														<span class="icon text-white">
															<i class="fas fa-shopping-cart"></i>
														</span>
													</button>
													<button
														class="btn btn-cancel btn-icon-split"
														@click="CanastaEnvios"
														title="Enviar SUMINISTROS"
														:disabled="canasta_envio.length == 0"
														v-if="
															$page.props.user_permissions.permisos.includes(
																'LOGISTICA_SUMINISTROS/ALMACEN_ENVIAR'
															)
														"
													>
														<span class="icon text-white">
															<i class="fas fa-paper-plane"></i>
														</span>
													</button>
												</div>
											</div>
											<div class="text-right col-md-6">
												<button
													class="btn btn-cancel btn-icon-split"
													@click="Exportar"
													title="Exportar ALMACÉN"
													:disabled="lista_suministros_filtrado.length == 0"
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

								<div class="form-row">
									<div class="form-group col-md-3">
										<div class="input-group">
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
									<div class="form-group col-md-3">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>ESTADO</span
												>
											</div>
											<select
												class="form-control center"
												:disabled="lista_suministros.length == 0"
												v-model="filtros_tabla['estado_id'].value"
											>
												<option :value="null">TODOS</option>
												<option
													v-for="(item, index) in estados"
													:key="index"
													:value="item.id"
												>
													{{ item.estado }}
												</option>
											</select>
										</div>
									</div>
									<div class="form-group col-md-3">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>CONDICIÓN</span
												>
											</div>
											<select
												class="form-control center"
												:disabled="lista_suministros.length == 0"
												v-model="filtros_tabla['condicion_id'].value"
											>
												<option :value="null">TODOS</option>
												<option
													v-for="(item, index) in condiciones"
													:key="index"
													:value="item.id"
												>
													{{ item.condicion }}
												</option>
											</select>
										</div>
									</div>
									<div class="form-group col-md-3">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>CLASIF.</span
												>
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
								</div>
							</fieldset>

							<DataTable
								:value="lista_suministros_filtrado"
								:scrollable="true"
								scrollDirection="both"
								:scrollHeight="String(windowHeigth * 0.49) + 'px'"
								showGridlines
								:paginator="true"
								:rows="100"
								paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
								currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
							>
								<Column
									field="ver"
									header="VER"
									:styles="{
										width: '50px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
										<div class="btn-group" role="group">
											<button
												class="btn btn-cancel"
												type="button"
												title="Ver SUMINISTRO"
												@click="Ver(data)"
											>
												<span class="icon text-white">
													<i class="fas fa-eye"></i>
												</span>
											</button>
										</div>
									</template>
								</Column>
								<Column
									field="enviar"
									header="ENVIAR"
									:styles="{
										width: '50px',
										justifyContent: 'center',
									}"
									v-if="
										$page.props.user_permissions.permisos.includes(
											'LOGISTICA_SUMINISTROS/ALMACEN_ENVIAR'
										)
									"
								>
									<template #body="{ data, index }">
										<div class="align-middle" v-if="data.cantidad > 0">
											<div class="checkbox">
												<label
													class="align-middle"
													style="
														font-size: 2em;
														margin-bottom: 0 !important;
														height: 28.6px !important;
													"
													:for="index"
													><input
														type="checkbox"
														:id="index"
														:value="data"
														v-model="canasta_envio" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
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
										><div
											:class="[
												data.estado == 'DISPONIBLE'
													? 'disponible'
													: data.estado == 'AGOTADO'
													? 'agotado'
													: '',
											]"
										>
											{{ data.estado }}
										</div></template
									></Column
								>
								<Column
									field="agencia"
									header="AGENCIA"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								></Column>
								<Column
									field="suministro"
									header="SUMINISTRO"
									:styles="{
										width: '200px',
										justifyContent: 'left',
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
									field="marca"
									header="MARCA"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
									><template #body="{ data }">{{
										data.marca ? data.marca : "-"
									}}</template></Column
								>
								<Column
									field="condicion"
									header="CONDICIÓN"
									:styles="{
										width: '100px',
										justifyContent: 'center',
										fontWeight: 'bolder',
									}"
								></Column>
								<Column
									field="cantidad"
									header="CANTIDAD"
									:styles="{
										width: '100px',
										justifyContent: 'center',
									}"
									><template #body="{ data }">{{
										data.cantidad == 0 ? "-" : RedondearVista(data.cantidad, 2)
									}}</template></Column
								>
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
									header="VALOR_UNIT."
									:styles="{
										width: '100px',
										justifyContent: 'right',
									}"
									><template #body="{ data }"
										>S/ {{ RedondearVista(data.valor_unitario, 2) }}</template
									></Column
								>
								<Column
									field="valor_total"
									header="VALOR TOTAL"
									:styles="{
										width: '100px',
										justifyContent: 'right',
									}"
									><template #body="{ data }"
										>S/ {{ RedondearVista(data.valor_total, 2) }}</template
									></Column
								>
								<Column
									field="fecha_compra"
									header="FECHA_COMPRA"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
									><template #body="{ data }">{{
										data.fecha_compra ? data.fecha_compra : "-"
									}}</template></Column
								>
								<Column
									field="clasificacion"
									header="CLASIFICACIÓN"
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

		<!-- The Modal -->
		<mdlCanastaCompra
			:agencias="agencias_permitidas"
			:agencia_id="agencia_busqueda"
			:usuarios="usuarios"
			:tipos="tipos"
			ref="mdlCanastaCompra"
		></mdlCanastaCompra>

		<mdlDatosSuministro
			:agencias="agencias"
			:usuarios="usuarios"
			:tipos="tipos"
			:proveedores="proveedores"
			:condiciones="condiciones"
			:estados="estados"
			:mediciones="mediciones"
			ref="mdlDatosSuministro"
		></mdlDatosSuministro>

		<mdlCanastaEnvio
			:agencias="agencias"
			:agencia_seleccionada="agencia_busqueda"
			:windowWidth="windowWidth"
			:windowHeigth="windowHeigth"
			ref="mdlCanastaEnvio"
		></mdlCanastaEnvio>
	</div>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import mdlCanastaCompra from "@/Pages/Logistica/Suministros/Components/mdlCanastaCompra.vue";
import mdlDatosSuministro from "@/Pages/Logistica/Suministros/Components/mdlDatosSuministro.vue";
import mdlCanastaEnvio from "@/Pages/Logistica/Suministros/Components/mdlCanastaEnvio.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	components: {
		layout,
		headerClose,
		mdlCanastaCompra,
		mdlDatosSuministro,
		mdlCanastaEnvio,

		DataTable,
		Column,
	},
	props: {},
	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			agencia_busqueda: 0,

			estados: [],
			condiciones: [],
			tipos: [],

			usuarios: [],
			proveedores: [],
			mediciones: [],

			canasta_envio: [],
			agencias: [],
			agencias_permitidas: [],

			lista_suministros: [],

			filtros_tabla: {
				suministro: { value: null },
				tipo_id: { value: null },
				estado_id: { value: null },
				condicion_id: { value: null },
				clasificacion: { value: null },
			},
		};
	},
	computed: {
		lista_suministros_filtrado() {
			const filtro_suministro = this.filtros_tabla["suministro"].value;
			const filtro_tipo = this.filtros_tabla["tipo_id"].value;
			const filtro_estado = this.filtros_tabla["estado_id"].value;
			const filtro_condicion = this.filtros_tabla["condicion_id"].value;
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
				if (filtro_estado) {
					if (item.estado_id !== filtro_estado) {
						return false;
					}
				}
				if (filtro_condicion) {
					if (item.condicion_id !== filtro_condicion) {
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
		this.ListarRecursos();
	},
	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"LOGISTICA_SUMINISTROS/ALMACEN"
			);
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

			if (valor == 0) {
				return "-";
			} else {
				return parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
		},
		async ListarRecursos() {
			await axios
				.get(route("log.sum.almacen.listar_recursos"))
				.then((response) => {
					this.tipos = response.data.tipos;
					this.estados = response.data.estados;
					this.condiciones = response.data.condiciones;

					this.usuarios = response.data.usuarios;
					this.proveedores = response.data.proveedores;
					this.mediciones = response.data.mediciones;
				});
		},
		async ListarSuministros() {
			this.canasta_envio = [];
			const params = {
				agencia_id: this.agencia_busqueda,
				estado: "TODOS",
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
						this.filtros_tabla.estado_id.value = this.estados.filter(
							(item) => item.estado == "DISPONIBLE"
						)[0].id;
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

		CanastaCompras() {
			let mdlCanastaCompra = this.$refs.mdlCanastaCompra;
			mdlCanastaCompra.submited = false;
			mdlCanastaCompra.title_modal = "CANASTA DE COMPRAS";
			mdlCanastaCompra.frmCanastaCompras.modo = "AGREGAR_NUEVO";
			$("#mdlCanastaCompra #documentoCompra").val("");
			mdlCanastaCompra.frmCanastaCompras.documento = null;

			$("#slcAgenciasCompra").val(0);
			mdlCanastaCompra.frmCanastaCompras.usuario_compra = 0;

			$("#mdlCanastaCompra").css("display", "block");
		},

		Ver(item) {
			let mdlDatosSuministro = this.$refs.mdlDatosSuministro;
			mdlDatosSuministro.submited = false;
			mdlDatosSuministro.frmDatosSuministro.modo = "VER_EXISTENTE";
			mdlDatosSuministro.title_modal = "VER SUMINISTRO";
			mdlDatosSuministro.frmDatosSuministro.id = item.id;
			mdlDatosSuministro.frmDatosSuministro.codigo = item.codigo;
			mdlDatosSuministro.frmDatosSuministro.suministro = item.suministro;

			mdlDatosSuministro.frmDatosSuministro.marca = item.marca;
			mdlDatosSuministro.frmDatosSuministro.detalle = item.detalle;
			mdlDatosSuministro.frmDatosSuministro.tipo_id = item.tipo_id;
			mdlDatosSuministro.frmDatosSuministro.agencia_id = item.agencia_id;
			mdlDatosSuministro.frmDatosSuministro.proveedor_id = item.proveedor_id;
			mdlDatosSuministro.frmDatosSuministro.condicion_id = item.condicion_id;
			mdlDatosSuministro.frmDatosSuministro.estado = item.estado;
			mdlDatosSuministro.frmDatosSuministro.medicion = item.medicion;
			mdlDatosSuministro.frmDatosSuministro.cantidad_actual = this.roundTo(
				item.cantidad,
				2
			);
			mdlDatosSuministro.frmDatosSuministro.valor_unitario = this.roundTo(
				item.valor_unitario,
				2
			);

			$("#mdlDatosSuministro").css("display", "block");
			$("#datosSuministro1-tab").tab("show");
		},
		async CanastaEnvios() {
			let mdlCanastaEnvio = this.$refs.mdlCanastaEnvio;
			mdlCanastaEnvio.submited = false;
			mdlCanastaEnvio.title_modal = "ENVIAR SUMINISTROS";
			mdlCanastaEnvio.frmCanastaEnvios.canasta_envio = this.canasta_envio;
			mdlCanastaEnvio.frmCanastaEnvios.agencia_recepcion = 0;
			mdlCanastaEnvio.frmCanastaEnvios.responsable_recepcion = 0;
			$("#documento_envio").val("");
			mdlCanastaEnvio.frmCanastaEnvios.documento_envio = null;
			$("#mdlCanastaEnvio").css("display", "block");
		},
		async Exportar() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);

			data.append(
				"lista_suministros",
				JSON.stringify(this.lista_suministros_filtrado)
			);

			// this.$inertia.post(route("log.sum.almacen.exportar"), data);
			// return false;

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					await axios
						.post(route("log.sum.almacen.exportar"), data)
						.then(async (response) => {
							const path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.click();

							// Espera un poco para asegurar cierre suave del primer modal
							await Swal.close();

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
.slot-almacen {
	width: 60%;
	margin-left: 20%;
}
.disponible {
	background-color: var(--green) !important;
	color: white !important;
}

.agotado {
	background-color: var(--red) !important;
	color: white !important;
}

@media (max-width: 1366px) {
	.slot-almacen {
		width: 70%;
		margin-left: 15%;
	}
}
@media (max-width: 900px) {
	.slot-almacen {
		width: 98%;
		margin-left: 2%;
	}
}
</style>
