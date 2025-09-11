<template>
	<layout ref="layout">
		<div class="slot_body slot-servicios" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'SERVICIOS'"></headerClose>

					<div class="card-body card-block">
						<DataTable
							:value="servicios"
							:scrollable="true"
							scrollDirection="both"
							scrollHeight="380px"
							showGridlines
						>
							<Column
								header="N°"
								:styles="{ width: '30px', justifyContent: 'center' }"
							>
								<template #body="slotProps">
									{{ slotProps.index + 1 }}
								</template>
							</Column>

							<Column
								header="SERVICIO"
								:styles="{ width: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.nombre }}
								</template>
							</Column>
							<Column
								header=""
								:styles="{ width: '50px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<button
										class="btn btn-cancel"
										v-if="!data.editar"
										@click="data.editar = true"
									>
										<span class="icon text-white">
											<i
												class="fas fa-cog"
												style="font-size: 9px !important"
											></i>
										</span>
									</button>
									<button
										class="btn btn-action"
										v-if="data.editar"
										@click="Guardar(data.nombre_columna)"
									>
										<span class="icon text-white">
											<i
												class="fas fa-save"
												style="font-size: 9px !important"
											></i>
										</span>
									</button>
								</template>
							</Column>
							<Column
								v-for="(item, index) in agencia_servicios"
								:key="index"
								:header="item.agencia"
								:styles="{ width: '100px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<div class="align-middle">
										<div class="checkbox">
											<label
												class="align-middle"
												style="
													font-size: 2em;
													margin-bottom: 0 !important;
													height: 28.6px !important;
												"
												:for="data.nombre_columna + '_' + item.id"
												><input
													type="checkbox"
													:id="data.nombre_columna + '_' + item.id"
													:value="{
														servicio: data.nombre_columna,
														agencia_activada: item.id,
													}"
													v-model="servicios_seleccionados"
													class="cr"
													style="margin-right: 0 !important"
													:checked="item.servicio_whatsapp"
													:disabled="!data.editar" />
												<span class="cr"
													><i class="cr-icon fa fa-check"></i></span
											></label>
										</div>
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
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	components: {
		layout,
		headerClose,

		DataTable,
		Column,
	},
	props: {},

	data() {
		return {
			submited: false,
			servicios: [],
			agencia_servicios: [],

			servicios_seleccionados: [],
		};
	},
	validations: {},

	watch: {
		agencia_servicios(value) {
			// Para rellenar los servicios que están activos
			this.servicios.forEach((element_1) => {
				let nombre_columna = element_1.nombre_columna;
				value.forEach((element_2) => {
					if (element_2[nombre_columna] == 1) {
						let obj = {};
						obj.servicio = nombre_columna;
						obj.agencia_activada = element_2.id;

						this.servicios_seleccionados.push(obj);
					}
				});
			});
		},
	},

	mounted() {
		this.Listar();
	},

	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		async Listar() {
			// this.$inertia.get(route("man.servicios.listar"));
			// return false;

			this.servicios = [
				{
					nombre: "WHATSAPP",
					nombre_columna: "servicio_whatsapp",
					editar: false,
				},
				{
					nombre: "SMS",
					nombre_columna: "servicio_sms",
					editar: false,
				},
				{
					nombre: "FACTURACIÓN",
					nombre_columna: "servicio_facturacion",
					editar: false,
				},
			];

			await axios.get(route("man.servicios.listar")).then((response) => {
				this.agencia_servicios = response.data.agencia_servicios;
			});
		},

		async Guardar(servicio) {
			let servicios_filtrados = this.servicios_seleccionados.filter(
				(item) => item.servicio == servicio
			);

			Swal.fire({
				icon: "question",
				text: "¿Desea guardar los cambios?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
				showLoaderOnConfirm: true,
				preConfirm: async () => {
					let data = new FormData();
					data.append("nombre_servicio", servicio);
					data.append("servicios", JSON.stringify(servicios_filtrados));

					// this.$inertia.post(route("man.servicios.guardar"), data);
					// return false;

					await axios
						.post(route("man.servicios.guardar"), data)
						.then((response) => {
							// Para desactivar el modo editar
							this.servicios.filter(
								(item) => item.nombre_columna == servicio
							)[0].editar = false;

							return Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								text: response.data.message,
								timer: 1200,
								showConfirmButton: false,
							});
						})
						.catch((error) => {
							console.log(error);
							return Swal.fire({
								icon: "error",
								title: "Error",
								text: "Ha ocurrido un error: COMUNICAR AL ÁREA DE SOPORTE",
								showConfirmButton: false,
								showCancelButton: false,
								allowOutsideClick: false,
							});
						});
				},
				allowOutsideClick: () => !Swal.isLoading(),
			});
		},
	},
};
</script>

<style lang="css">
.slot-servicios {
	width: 70% !important;
	margin-left: 15% !important;
}

.checkbox .cr {
	/* Estilos para la clase específica */
	background: white !important;
}

@media (max-width: 900px) {
	.slot-servicios {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
