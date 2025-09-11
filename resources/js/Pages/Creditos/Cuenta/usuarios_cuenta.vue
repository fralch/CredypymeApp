<template>
	<layout ref="layout">
		<div class="slot_body slot-usuarios-cuenta" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'USUARIOS CON CUENTAS'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<div class="col-md-5 col-12">
								<div class="input-group">
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
						</div>

						<div class="card-title mt-1 mb-1">LISTA DE RESULTADOS</div>

						<DataTable
							:value="cuentas"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="500px"
							selectionMode="single"
							stripedRows
							showGridlines
						>
							<Column
								field="habilitar"
								header="HABILITAR"
								:styles="{
									width: '70px',
									justifyContent: 'center',
								}"
							>
								<template #body="{ data, index }">
									<div class="switch-button">
										<!-- Checkbox -->
										<input
											type="checkbox"
											name="switch-button"
											:id="'switch-label' + index"
											class="switch-button__checkbox"
											@change="HabilitarDeshabilitar(data)"
											:checked="data.con_cuenta"
										/>

										<label
											:for="'switch-label' + index"
											class="switch-button__label"
										></label>
									</div>
								</template>
							</Column>
							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{ width: '130px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="usuario"
								header="USUARIO"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
							</Column>
							<Column
								field="monto"
								header="MONTO"
								:styles="{ width: '100px', justifyContent: 'right' }"
								><template #body="{ data }">
									<div class="bolder" style="font-size: 13px">
										S/ {{ RedondearVista(data.monto, 2) }}
									</div></template
								>
							</Column>
							<Column
								field="cargo"
								header="CARGO"
								:styles="{ width: '200px', justifyContent: 'left' }"
							>
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
export default {
	components: { layout, headerClose, DataTable, Column },

	data() {
		return {
			agencias: [],
			agencias_permitidas: [],
			agencia_seleccionada: 0,

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
				"CREDITOS_CUENTA/GESTION"
			);
		},
		async ListarCuentas(agencia_id) {
			const params = {
				agencia_id: agencia_id,
			};
			await axios
				.get(route("cue.usuarios_cuenta.listar"), { params })
				.then((response) => {
					this.cuentas = response.data.cuentas;
				});
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
		HabilitarDeshabilitar(usuario) {
			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("dni", usuario.dni);

			if (usuario.con_cuenta == 0) {
				data.append("con_cuenta", 1);
			} else if (usuario.con_cuenta == 1) {
				data.append("con_cuenta", 0);
			}

			this.$inertia.post(route("cue.usuarios_cuenta.habilitar"), data, {
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
	},
};
</script>

<style lang="css">
:root {
	--color-green: var(--verdeOscuroEmpresarial);
	--color-red: var(--plomoOscuroEmpresarial);
	--color-button: #fdffff;
	--color-black: var(--plomoOscuroEmpresarial);
}

.slot-usuarios-cuenta {
	width: 50% !important;
	margin-left: 25% !important;
}

.switch-button {
	display: inline-block;
}
.switch-button__label:hover {
	cursor: pointer;
}
.switch-button .switch-button__checkbox {
	display: none;
}
.switch-button .switch-button__label {
	background-color: var(--color-red);
	width: 3rem;
	height: 1rem;
	border-radius: 3rem;
	display: inline-block;
	position: relative;
	margin: 0px;
}
.switch-button .switch-button__label:before {
	transition: 0.2s;
	display: block;
	position: absolute;
	width: 1rem;
	height: 1rem;
	background-color: var(--color-button);
	content: "";
	border-radius: 50%;
	box-shadow: inset 0px 0px 0px 1px var(--color-black);
}
.switch-button .switch-button__checkbox:checked + .switch-button__label {
	background-color: var(--color-green);
}
.switch-button .switch-button__checkbox:checked + .switch-button__label:before {
	transform: translateX(2rem);
}

/* --------------------------------- */
@media (max-width: 900px) {
	.slot-usuarios-cuenta {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>

