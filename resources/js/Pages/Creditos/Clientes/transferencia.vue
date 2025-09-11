<template>
	<layout ref="layout">
		<div class="slot_body slot-transferencia-clientes" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'TRANSFERIR CLIENTES'"></headerClose>

					<div class="card-title">PANEL DE BÚSQUEDA</div>

					<div class="card-body card-block">
						<div class="form-row row justify-content-md-center">
							<div class="col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">DE</span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_origen"
										@change="FiltrarAsesores('origen')"
									>
										<option :value="0" disabled>Seleccione...</option>
										<option
											v-for="(item, index) in agencias_origen"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">PARA</span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_destino"
										@change="FiltrarAsesores('destino')"
										:disabled="agencia_origen == 0"
									>
										<option :value="0" disabled>Seleccione...</option>
										<option
											v-for="(item, index) in agencias_destino"
											:key="index"
											:value="item.id"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
						</div>

						<div class="form-row row justify-content-md-center mt-2">
							<div class="input-group col-md-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">DE ASESOR</span>
								</div>
								<select
									class="form-control center"
									v-model="asesor_origen"
									@change="BuscarClientes"
									:disabled="agencia_origen == 0"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in asesores_filtrados_origen"
										:key="index"
										:value="item.dni"
									>
										{{ item.usuario }}
									</option>
								</select>
								<div class="input-group-append">
									<div class="input-group-text prepend-title">
										<input
											type="checkbox"
											id="chbHabilitadosOrigen"
											v-model="solo_habilitados_origen"
											:disabled="agencia_origen == 0"
										/>
										<label class="m-0 ml-1" for="chbHabilitadosOrigen"
											>Habilitados</label
										>
									</div>
								</div>
							</div>

							<div class="input-group col-md-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title"
										>PARA ASESOR</span
									>
								</div>
								<select
									class="form-control center"
									v-model="asesor_destino"
									:disabled="agencia_destino == 0"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in asesores_filtrados_destino"
										:key="index"
										:value="item.dni"
									>
										{{ item.usuario }}
									</option>
								</select>
								<div class="input-group-prepend">
									<div class="input-group-text prepend-title">
										<input
											type="checkbox"
											id="chbHabilitadosDestino"
											v-model="solo_habilitados_destino"
											:disabled="agencia_destino == 0"
										/>
										<label class="m-0 ml-1" for="chbHabilitadosDestino"
											>Habilitados</label
										>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row row justify-content-md-center mt-2">
							<div class="input-group col-md-6">
								<input
									class="form-control mayus"
									type="text"
									placeholder="Buscar cliente..."
									autocomplete="off"
									@focus="hidenav()"
									@blur="shownav()"
									v-model="filtros_tabla['cliente'].value"
									spellcheck="false"
									:disabled="lista_clientes.length == 0"
								/>
							</div>
							<div
								class="col-md-3"
								:style="
									windowWidth >= 900
										? ''
										: 'margin-top: 12px !important; text-align: center !important'
								"
							>
								<button
									class="btn btn-action btn-icon-split mb-1"
									@click="Transferir()"
									title="Transferir clientes"
									:disabled="clientes_seleccionados.length == 0"
								>
									<span class="icon text-white">
										<i class="fas fa-paper-plane"></i>
									</span>
									<span class="text">TRANSFERIR</span>
								</button>
							</div>
						</div>
						<div class="form-row row justify-content-md-center mt-2 mb-2">
							<div class="col-md-2 col-6">
								<div
									class="form-check text-center bolder"
									style="background: var(--yellow) !important"
								>
									<input
										class="form-check-input"
										type="checkbox"
										id="chbEvaluacionFinanciera"
										value="evaluacion_financiera"
										v-model="datos_adicionales"
									/>
									<label
										class="label-title bolder"
										for="chbEvaluacionFinanciera"
										style="font-size: 12px !important"
										>Ev. Financiera</label
									>
								</div>
							</div>
							<div class="col-md-2 col-6">
								<div
									class="form-check text-center"
									style="background: var(--cyan) !important"
								>
									<input
										class="form-check-input"
										type="checkbox"
										id="chbAlbumFotos"
										value="album_fotos"
										v-model="datos_adicionales"
									/>
									<label
										class="label-title bolder"
										for="chbAlbumFotos"
										style="font-size: 12px !important"
										>Albúm fotos</label
									>
								</div>
							</div>
						</div>
						<DataTable
							:value="lista_clientes_filtrados"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="370px"
							showGridlines
							stripedRows
						>
							<Column
								field="index"
								header="N°"
								:styles="{ maxWidth: '70px', justifyContent: 'center' }"
							>
								<template #body="{ index }">
									{{ index + 1 }}
								</template>
							</Column>
							<Column
								field="cliente"
								header="CLIENTE"
								:styles="{ width: '300px', justifyContent: 'left' }"
							>
								<template #body="{ data, index }">
									<div class="custom-control custom-checkbox mt-2">
										<input
											type="checkbox"
											class="custom-control-input"
											:id="'chbClienteOrigen' + index"
											:value="data.id"
											v-model="clientes_seleccionados"
											style="cursor: pointer"
										/>

										<label
											class="custom-control-label"
											:for="'chbClienteOrigen' + index"
											style="cursor: pointer"
										>
											{{ data.cliente }}
										</label>
									</div>
								</template>
							</Column>
							<Column
								field="expediente"
								header="EXPEDIENTE"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{
										data.codigo_expediente == null
											? "-"
											: data.codigo_expediente
									}}
								</template>
							</Column>
							<Column
								field="usuario_asesor"
								header="ASESOR"
								:styles="{ width: '70px', justifyContent: 'center' }"
							>
							</Column>
							<template #empty> No se encontraron CLIENTES.</template>
						</DataTable>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	props: {
		asesores: Array,
	},
	components: {
		layout,
		headerClose,

		DataTable,
		Column,
	},

	data() {
		return {
			windowWidth: window.innerWidth,

			agencias_permitidas: [],
			agencias_origen: [],
			agencia_origen: 0,
			agencias_destino: [],
			agencia_destino: 0,

			asesores_filtrados_origen: [],
			asesor_origen: 0,
			solo_habilitados_origen: true,

			asesores_filtrados_destino: [],
			asesor_destino: 0,
			solo_habilitados_destino: true,

			lista_clientes: [],

			clientes_seleccionados: [],
			datos_adicionales: [],

			filtros_tabla: {
				cliente: { value: null },
			},
		};
	},
	computed: {
		lista_clientes_filtrados() {
			const filtro_cliente = this.filtros_tabla["cliente"].value?.toLowerCase();

			return this.lista_clientes.filter((item) => {
				if (filtro_cliente && filtro_cliente.length >= 3) {
					return item.cliente?.toLowerCase().includes(filtro_cliente);
				}
				return true;
			});
		},
	},
	watch: {
		agencia_destino(value) {
			this.agencias_origen = this.agencias_permitidas.filter(
				(item) => item.id != value
			);
		},

		agencia_origen(value) {
			if (value == 0) {
				this.agencias_destino = [];
			} else {
				this.agencias_destino = this.agencias_permitidas.filter(
					(item) => item.id != value
				);
				this.agencia_destino = 0;
				this.asesor_origen = 0;
				this.FiltrarAsesores("origen");
			}
		},
		solo_habilitados_origen() {
			this.FiltrarAsesores("origen");
		},
		solo_habilitados_destino() {
			this.FiltrarAsesores("destino");
		},
		agencia_destino() {
			this.asesor_destino = 0;
		},
	},
	mounted() {
		this.ListarAgencias();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		ListarAgencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CLIENTES/TRANSFERENCIA"
			);
			this.agencias_origen = this.agencias_permitidas;
		},

		FiltrarAsesores(tipo) {
			if (tipo == "origen") {
				if (this.solo_habilitados_origen) {
					this.asesores_filtrados_origen = this.asesores.filter(
						(item) =>
							item.agencia_id == this.agencia_origen && item.habilitado == 1
					);
				} else {
					this.asesores_filtrados_origen = this.asesores.filter(
						(item) => item.agencia_id == this.agencia_origen
					);
				}
			} else if (tipo == "destino") {
				if (this.solo_habilitados_destino) {
					this.asesores_filtrados_destino = this.asesores.filter(
						(item) =>
							item.agencia_id == this.agencia_destino && item.habilitado == 1
					);
				} else {
					this.asesores_filtrados_destino = this.asesores.filter(
						(item) => item.agencia_id == this.agencia_destino
					);
				}
			}
		},
		async BuscarClientes() {
			let data = new FormData();
			data.append("agencia_id", this.agencia_origen);
			data.append("asesor_id", this.asesor_origen);

			await axios
				.post(route("cli.transferencia.listar_clientes"), data)
				.then((response) => {
					this.clientes_seleccionados = [];
					this.lista_clientes = response.data.lista_clientes;
				});
		},
		async Transferir() {
			if (this.agencia_destino == 0) {
				Swal.fire({
					icon: "info",
					title: "¡Ups!",
					text: "Debe seleccionar la AGENCIA de DESTINO.",
				});
				return false;
			}
			if (this.asesor_destino == 0) {
				Swal.fire({
					icon: "info",
					title: "¡Ups!",
					text: "Debe seleccionar el ASESOR de DESTINO.",
				});
				return false;
			}

			Swal.fire({
				title: "TRANSFERIR CLIENTES",
				text: "¿Desea continuar?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("agencia_origen", this.agencia_origen);
					data.append("agencia_destino", this.agencia_destino);
					data.append("asesor_origen", this.asesor_origen);
					data.append("asesor_destino", this.asesor_destino);
					data.append(
						"clientes_seleccionados",
						JSON.stringify(this.clientes_seleccionados)
					);
					data.append(
						"datos_adicionales",
						JSON.stringify(this.datos_adicionales)
					);

					// this.$inertia.post(route("cli.transferencia.transferir"), data);
					// return false;

					Swal.fire({
						title: "TRANSFIRIENDO",
						text: "Espere porfavor...",
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
							return axios
								.post(route("cli.transferencia.transferir"), data)
								.then(async (response) => {
									this.BuscarClientes();
									this.datos_adicionales = [];

									return Swal.fire({
										icon: "success",
										title: "¡LISTO!",
										timer: 1200,
										showConfirmButton: false,
									});
								})
								.catch((error) => {
									Swal.showValidationMessage(`Ha ocurrido un error: ${error}`);
								});
						},
					});
				} else {
					return false;
				}
			});
		},
	},
};
</script>



<style lang="css">
.slot-transferencia-clientes {
	width: 60% !important;
	margin-left: 20% !important;
}
@media (max-width: 900px) {
	.slot-transferencia-clientes {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>

