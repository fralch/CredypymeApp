<template>
	<layout ref="layout">
		<div class="slot_body slot-mensajeria" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MENSAJERÍA'"></headerClose>
					<div class="card-body card-block">
						<div class="form-group col-md-5 col-6">
							<label class="label-title">AGENCIA</label>
							<select
								class="form-control center mayus"
								v-model="agencia_seleccionada"
								@change="ListarCreditos"
							>
								<option
									v-for="(item, index) in agencias_permitidas"
									:key="index"
									:value="item.id"
								>
									{{ item.agencia }}
								</option>
							</select>
						</div>

						<div class="text-center">
							<label class="label-title"
								>CANTIDAD DE CRÉDITOS: {{ cantidad_creditos }}</label
							>
							<br />
							<label class="label-title"
								>CANTIDAD DE CLIENTES: {{ cantidad_clientes }}</label
							>
						</div>
						<br />
						<DataTable
							:value="lista_creditos"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							:rows="20"
						>
							<Column
								header="N°"
								:styles="{ width: '40px', justifyContent: 'center' }"
							>
								<template #body="slotProps">
									{{ slotProps.index + 1 }}
								</template>
							</Column>
							<Column
								field="año"
								header="AÑO"
								:styles="{ width: '50px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="mes"
								header="MES"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="dia"
								header="DÍA"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>

							<Column
								field="cantidad_creditos"
								header="CREDITOS ACTIVOS"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="cantidad_clientes"
								header="CLIENTES ACTIVOS"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
							</Column>

							<Column
								header="ACCIONES"
								:styles="{ width: '110px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											type="button"
											@click="Enviar(data)"
											v-if="data.enviado == 0"
										>
											<span class="icon">
												<i class="fas fa-paper-plane"></i>
											</span>
											<span class="text">ENVIAR</span>
										</button>
									</div>
								</template>
							</Column>
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
import axios, { Axios } from "axios";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	components: {
		layout,
		headerClose,

		DataTable,
		Column,
	},

	data() {
		return {
			windowWidth: window.innerWidth,

			agencia_seleccionada: 0,
			agencias: [],
			agencias_permitidas: [],

			lista_creditos: [],
		};
	},
	computed: {
		cantidad_creditos() {
			let total_creditos = this.lista_creditos.reduce((total, item) => {
				return total + item.cantidad_creditos;
			}, 0);

			return total_creditos;
		},
		cantidad_clientes() {
			let total_clientes = this.lista_creditos.reduce((total, item) => {
				return total + item.cantidad_clientes;
			}, 0);

			return total_clientes;
		},
	},
	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
				} else {
					this.agencia_seleccionada = 0;
				}
			}
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_HERRAMIENTAS/MENSAJERIA"
			);
		},
		async ListarCreditos() {
			// this.$inertia.get(
			// 	route("her.mensajeria.listar_creditos", this.agencia_seleccionada)
			// );
			// return false;

			return await axios
				.get(route("her.mensajeria.listar_creditos", this.agencia_seleccionada))
				.then((response) => {
					this.lista_creditos = response.data.lista_creditos;
				});
		},

		async Enviar(item) {
			let data = new FormData();

			data.append("agencia_id", this.agencia_seleccionada);
			data.append("año", item.año);
			data.append("mes", item.mes);
			data.append("dia", item.dia);

			// this.$inertia.post(route("her.mensajeria.enviar"), data);
			// return false;

			return await axios
				.post(route("her.mensajeria.enviar"), data)
				.then((response) => {
					item.enviado = 1;
				});
		},
	},
};
</script>

<style lang="css">
.slot-mensajeria {
	width: 50% !important;
	margin-left: 25% !important;
}

@media (max-width: 900px) {
	.slot-mensajeria {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
