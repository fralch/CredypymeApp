<template>
	<layout ref="layout">
		<div class="slot_body slot-mostrar-cuentas" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MOSTRAR CUENTAS'"></headerClose>

					<div class="card-title">LISTA DE RESULTADOS</div>
					<div class="card-body card-block">
						<div class="form-row mb-2">
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text">AGENCIA</span>
								</div>
								<select
									class="form-control center"
									v-model="agencia_seleccionada"
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
						</div>

						<DataTable
							:value="cuentas"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="300px"
							selectionMode="single"
							:rows="30"
						>
							<Column
								field="numero"
								header="N°"
								:styles="{
									width: '120px',
									justifyContent: 'center',
								}"
							>
								<template #body="slotProps">
									{{ slotProps.index + 1 }}
								</template>
							</Column>
							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{
									width: '120px',
									justifyContent: 'center',
								}"
							>
							</Column>
							<Column
								field="usuario"
								header="USUARIO"
								:styles="{
									width: '120px',
									justifyContent: 'center',
								}"
							>
							</Column>
							<Column
								field="monto"
								header="MONTO"
								:styles="{
									width: '120px',
									justifyContent: 'right',
								}"
							>
								<template #body="{ data }">
									<div class="bolder" style="font-size: 15px">
										S/ {{ RedondearVista(data.monto, 2) }}
									</div>
								</template>
							</Column>
							<Column
								field="acciones"
								header="ACCIONES"
								:styles="{
									width: '120px',
									justifyContent: 'center',
								}"
							>
								<template #body="{ data }">
									<button
										class="btn btn-cancel btn-icon-split"
										@click="VerMovimientos(data)"
									>
										<span class="icon text-white"> MOVIMIENTOS </span>
									</button>
								</template>
							</Column>
						</DataTable>

						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="Actualizar"
								title="Actualizar LISTA"
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
			<!-- ---- Modal Movimientos  -->
			<div class="modal" id="mdlDetalleCuenta">
				<div class="modal-content w-45">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'MOVIMIENTOS EN LA CUENTA'"
								:nombre_modal="'mdlDetalleCuenta'"
							>
							</headerCloseModal>
							<mdlDetalleCuenta :tipo="'modal'" ref="mdlDetalleCuenta">
							</mdlDetalleCuenta>
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import mdlDetalleCuenta from "@/Pages/Creditos/Cuenta/Components/mdlDetalleCuenta.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	components: {
		layout,
		mdlDetalleCuenta,
		headerClose,
		headerCloseModal,
		DataTable,
		Column,
	},

	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: null,
			cuentas: [],
		};
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
		agencia_seleccionada(value) {
			this.ListarCuentas(value);
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
	},
	methods: {
		ListarAgenciasPermitidas() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CUENTA/MOSTRAR_CUENTAS"
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

			let resultado = parseFloat(valor).toLocaleString("es-PE", {
				minimumFractionDigits: numero_decimales,
				maximumFractionDigits: numero_decimales,
			});

			return resultado;
		},

		ListarCuentas(agencia_id) {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", agencia_id);
			axios
				.post(route("cue.mostrar_cuentas.listar"), data)
				.then(function (response) {
					self.cuentas = response.data;
				});
		},

		Actualizar() {
			this.$inertia.get(
				route("cue.mostrar_cuentas"),
				{},
				{
					preserveScroll: true,
					onStart: () => {
						Swal.fire({
							title: "ACTUALIZANDO",
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
		VerMovimientos(cuenta) {
			let mdlDetalleCuenta = this.$refs.mdlDetalleCuenta;

			mdlDetalleCuenta.detalle_cuenta = cuenta;

			$("#mdlDetalleCuenta").css("display", "block");
		},
	},
};
</script>

<style lang="css">
.slot-mostrar-cuentas {
	width: 50% !important;
	margin-left: 25% !important;
	margin-top: 5% !important;
}

@media only screen and (max-width: 900px) {
	.slot-mostrar-cuentas {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

