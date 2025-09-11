<template>
	<div>
		<layout ref="layout">
			<div class="slot_body slot-envios-recepciones" slot="component-view">
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'ENVÍOS Y RECEPCIONES'"></headerClose>
						<div class="card-title">PANEL DE BUSQUEDA</div>
						<div class="card-body card-block">
							<div class="form-row col-md-12 col-12 justify-content-md-center">
								<fieldset class="form-group col-md-6">
									<legend>
										<label class="label-title">FILTROS DE BÚSQUEDA</label>
									</legend>
									<div class="row">
										<div class="input-group col-md-6">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>DESDE</span
												>
											</div>
											<input
												type="date"
												class="form-control center bolder"
												v-model="datos_fecha.fecha_desde"
											/>
										</div>
										<div class="input-group col-md-6">
											<div class="input-group-prepend">
												<span class="input-group-text prepend-title"
													>HASTA</span
												>
											</div>
											<input
												type="date"
												class="form-control center bolder"
												v-model="datos_fecha.fecha_hasta"
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
								<div class="col-md-3">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">TIPO</span>
										</div>
										<select
											class="form-control center"
											:disabled="lista_operaciones.length == 0"
											v-model="filtros_tabla['tipo'].value"
										>
											<option :value="null" selected>TODOS</option>
											<option value="ENVÍO">ENVÍOS</option>
											<option value="RECEPCIÓN">RECEPCIONES</option>
										</select>
									</div>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>SITUACIÓN</span
											>
										</div>
										<select
											class="form-control center"
											:disabled="lista_operaciones.length == 0"
											v-model="filtros_tabla['situacion'].value"
										>
											<option :value="null">TODOS</option>
											<option value="PENDIENTE">PENDIENTE</option>
											<option value="CONFIRMADO">CONFIRMADO</option>
											<option value="RECHAZADO">RECHAZADO</option>
										</select>
									</div>
								</div>
							</div>

							<div class="card-title mb-1">RESULTADOS DE BÚSQUEDA</div>

							<DataTable
								:value="lista_operaciones_filtrado"
								:scrollable="true"
								scrollDirection="both"
								:scrollHeight="String(windowHeigth * 0.5) + 'px'"
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
												class="btn btn-action btn-icon-split"
												@click="VerDetalle(data)"
											>
												<span class="icon text-white">
													<i class="far fa-eye"></i>
												</span>
											</button>
										</div>
									</template>
								</Column>
								<Column
									field="tipo"
									header="TIPO"
									:styles="{
										width: '90px',
										justifyContent: 'center',
										fontWeight: 'bolder',
									}"
								>
									<template #body="{ data }">
										<span
											class="icon mr-1"
											v-show="data.tipo == 'RECEPCIÓN'"
											style="color: var(--azulOscuroEmpresarial)"
											><i class="fas fa-arrow-left"></i
										></span>
										{{ data.tipo }}

										<span
											class="icon ml-1"
											v-show="data.tipo == 'ENVÍO'"
											style="color: var(--verdeOscuroEmpresarial)"
										>
											<i class="fas fa-arrow-right"></i
										></span>
									</template>
								</Column>
								<Column
									field="situacion"
									header="SITUACIÓN"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
										<div
											:class="[
												data.situacion == 'PENDIENTE'
													? 'pendiente'
													: data.situacion == 'CONFIRMADO'
													? 'confirmado'
													: 'rechazado',
											]"
										>
											{{ data.situacion }}
										</div>
									</template>
								</Column>

								<Column
									field="fecha"
									header="FECHA"
									:styles="{
										width: '120px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">{{
										JSON.parse(data.datos_creacion).fecha
									}}</template>
								</Column>

								<Column
									field="usuario_envio"
									header="USUARIO_ENVÍO"
									:styles="{
										width: '150px',
										justifyContent: 'center',
									}"
									><template #body="{ data }">
										<div :class="[data.tipo == 'ENVÍO' ? 'bolder' : '']">
											{{
												data.nombre_agencia_envio + " - " + data.usuario_envio
											}}
										</div>
									</template>
								</Column>

								<Column
									field="responsable_recepcion"
									header="RESPONSABLE_RECEPCIÓN"
									:styles="{
										width: '150px',
										justifyContent: 'center',
									}"
								>
									<template #body="{ data }">
										<div :class="[data.tipo == 'RECEPCIÓN' ? 'bolder' : '']">
											{{ data.abreviacion + " - " + data.usuario_recepcion }}
										</div>
									</template>
								</Column>
								<template #empty> No hay RESULTADOS encontrados.</template>
							</DataTable>
						</div>
					</div>
				</div>
			</div>
		</layout>
		<mdlDetalleEnvio
			ref="mdlDetalleEnvio"
			:windowHeigth="windowHeigth"
			:windowWidth="windowWidth"
		></mdlDetalleEnvio>
	</div>
</template>

<script>
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import mdlDetalleEnvio from "@/Pages/Logistica/Suministros/Components/mdlDetalleEnvio.vue";

import { required } from "vuelidate/lib/validators";

export default {
	components: {
		layout,
		headerClose,
		mdlDetalleEnvio,

		DataTable,
		Column,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,

			envios_check: true,
			recepciones_check: true,
			lista_operaciones: [],

			datos_fecha: {
				fecha_desde: null,
				fecha_hasta: null,
			},

			filtros_tabla: {
				tipo: { value: null },
				situacion: { value: "PENDIENTE" },
			},
		};
	},
	validations: {
		datos_fecha: {
			fecha_desde: { required },
			fecha_hasta: { required },
		},
	},
	computed: {
		lista_operaciones_filtrado() {
			const filtro_tipo = this.filtros_tabla["tipo"].value;
			const filtro_situacion = this.filtros_tabla["situacion"].value;

			return this.lista_operaciones.filter((item) => {
				if (filtro_tipo) {
					if (item.tipo !== filtro_tipo) {
						return false;
					}
				}
				if (filtro_situacion) {
					if (item.situacion !== filtro_situacion) {
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
		this.ListarRecursos();
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
					text: "Falta ingresar una de las fechas, verifique.",
				});
				return false;
			} else {
				const params = {
					fecha_desde: this.datos_fecha.fecha_desde,
					fecha_hasta: this.datos_fecha.fecha_hasta,
					usuario: "MI_USUARIO",
				};

				// this.$inertia.get(
				// 	route("log.sum.envios_recepciones.por_fecha"),
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
								route("log.sum.envios_recepciones.por_fecha"),
								{
									params,
								}
							);

							this.lista_operaciones = response.data.envios_recepciones;
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
		async VerDetalle(item) {
			const params = {
				envio_id: item.id,
			};

			// this.$inertia.get(
			// 	route("log.sum.envios_recepciones.detalle"),
			// 	params
			// );
			// return false;

			const response = await axios.get(
				route("log.sum.envios_recepciones.detalle"),
				{ params }
			);

			let mdlDetalleEnvio = this.$refs.mdlDetalleEnvio;

			if (item.tipo == "ENVÍO") {
				mdlDetalleEnvio.title_modal = "ENVÍO DE SUMINISTROS";
			} else if (item.tipo == "RECEPCIÓN") {
				mdlDetalleEnvio.title_modal = "RECEPCIÓN DE SUMINISTROS";
			}
			mdlDetalleEnvio.submited = false;
			mdlDetalleEnvio.frmConfirmacion.modo = "CONFIRMAR";
			mdlDetalleEnvio.lista_suministros = response.data.envio_detalle;
			mdlDetalleEnvio.frmConfirmacion.canasta_recepcion = [];
			mdlDetalleEnvio.frmConfirmacion.envio_id = item.id;

			if (item.tipo == "ENVÍO" || item.situacion == "RECHAZADO") {
				mdlDetalleEnvio.modo = "VISTA";
			} else if (item.tipo == "RECEPCIÓN") {
				if (item.situacion == "PENDIENTE") {
					mdlDetalleEnvio.modo = "NO_VISTA";
				} else if (
					item.situacion == "CONFIRMADO" ||
					item.situacion == "RECHAZADO"
				) {
					mdlDetalleEnvio.modo = "VISTA";
				}
			}

			mdlDetalleEnvio.frmConfirmacion.documento_envio = item.documento_envio;

			if (item.situacion == "CONFIRMADO") {
				mdlDetalleEnvio.frmConfirmacion.documento_recepcion =
					item.documento_recepcion;
			} else {
				mdlDetalleEnvio.frmConfirmacion.documento_recepcion = null;
			}

			$("#mdlDetalleEnvio").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-envios-recepciones {
	width: 60%;
	margin-left: 20%;
}
.confirmado {
	padding: 5px !important;
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

.pendiente {
	padding: 5px !important;
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	padding: 5px !important;
	background-color: var(--red) !important;
	color: white !important;
}

@media (max-width: 1366px) {
	.slot-envios-recepciones {
		width: 70%;
		margin-left: 15%;
	}
}
@media (max-width: 900px) {
	.slot-envios-recepciones {
		width: 98%;
		margin-left: 2%;
	}
}
</style>
