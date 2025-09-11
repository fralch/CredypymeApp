<template>
	<layout ref="layout">
		<div class="slot_body slot-seguimiento" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'SEGUIMIENTO DE EVALUACIONES'"></headerClose>
					<div class="card-title">FILTRO DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row justify-content-md-center mb-2">
							<fieldset class="form-group col-md-4">
								<legend>
									<label class="label-title"
										>SELECCIONE LA FECHA DE LA EVALUACIÓN</label
									>
								</legend>

								<div class="input-group justify-content-md-center">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">FECHA</span>
									</div>

									<date-picker
										v-model="fecha_seleccionada"
										class="center"
										type="month"
										:editable="false"
										value-type="format"
										placeholder="Seleccione un mes"
										style="width: 165px !important"
									></date-picker>
								</div>
							</fieldset>
							<div class="col-md-1 mt-3">
								<button
									class="btn btn-action btn-icon-split ml-2"
									title="BUSCAR EVALUACIONES"
									@click="Buscar"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-3">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">ESTADO</span>
									</div>
									<select
										class="form-control center"
										v-model="filtros_tabla['culminado'].value"
										:disabled="lista_evaluaciones.length == 0"
									>
										<option :value="null" selected>TODOS</option>
										<option :value="1">CULMINADO</option>
										<option :value="0">NO CULMINADO</option>
									</select>
								</div>
							</div>
							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										v-model="filtros_tabla['agencia'].value"
										:disabled="lista_evaluaciones.length == 0"
									>
										<option :value="null" selected>TODAS</option>
										<option
											v-for="(item, index) in agencias"
											:key="index"
											:value="item.agencia"
										>
											{{ item.agencia }}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group col-md-5">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Buscar </span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										v-model="filtros_tabla['global'].value"
										:disabled="lista_evaluaciones.length == 0"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										spellcheck="false"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE RESULTADOS</div>

					<div class="card-body card-block">
						<DataTable
							:value="lista_evaluaciones"
							responsiveLayout="scroll"
							sortMode="single"
							:sortOrder="1"
							scrollable
							scrollHeight="450px"
							:filters="filtros_tabla"
							:globalFilterFields="['evaluador', 'examen']"
							showGridlines
						>
							<Column
								field="culminado"
								header="CULMINADO"
								:styles="{ maxWidth: '80px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									<div
										:class="
											data.culminado == 1
												? 'state font-13 badge badge-success'
												: 'state font-12 badge badge-danger'
										"
									>
										<span align="center">
											<i
												:class="
													data.culminado == 1
														? 'cr-icon fa fa-check'
														: 'fas fa-times'
												"
											></i
											>{{ data.culminado == 1 ? " Si" : " No" }}</span
										>
									</div>
								</template>
							</Column>
							<Column
								field="agencia"
								header="AGENCIA"
								:styles="{ minWidth: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.agencia }}
								</template>
							</Column>
							<Column
								field="evaluador"
								header="EVALUADOR"
								:styles="{ minWidth: '150px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.evaluador }}
								</template>
							</Column>
							<Column
								field="examen"
								header="EXAMEN"
								:styles="{ minWidth: '300px' }"
							>
								<template #body="{ data }">
									{{ data.examen }}
								</template>
							</Column>
							<Column
								field="fecha_culminado"
								header="FECHA_CULMINADO"
								:styles="{ minWidth: '170px', justifyContent: 'center' }"
							>
								<template #body="{ data }">
									{{ data.culminado == 1 ? data.fecha : "-" }}
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import { FilterMatchMode } from "primevue/api";

export default {
	components: {
		layout,
		headerClose,
		DatePicker,

		DataTable,
		Column,
	},
	data: () => ({
		windowWidth: window.innerWidth,

		submited: false,
		fecha_seleccionada: null,

		filtros_tabla: {},
		lista_evaluaciones: [],
	}),

	computed: {
		agencias() {
			return this.$page.props.application.agencias;
		},
	},

	created() {
		this.filtros_tabla = {
			global: { value: null, matchMode: FilterMatchMode.CONTAINS },
			agencia: { value: null, matchMode: FilterMatchMode.CONTAINS },
			evaluador: { value: null, matchMode: FilterMatchMode.CONTAINS },
			examen: { value: null, matchMode: FilterMatchMode.CONTAINS },
			culminado: { value: null, matchMode: "equals" },
		};
	},
	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		Buscar() {
			let self = this;

			if (this.fecha_seleccionada == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe seleccionar un año y un mes, verifique.",
				});
				return false;
			} else {
				let data = new FormData();
				data.append("fecha_seleccionada", this.fecha_seleccionada);

				Swal.fire({
					title: "BUSCANDO",
					text: "Espere porfavor...",
					allowOutsideClick: false,
					didOpen: () => {
						// this.$inertia.post(route("col.seguimiento.buscar"), data);
						// return false;

						Swal.showLoading();
						axios
							.post(route("col.seguimiento.buscar"), data)
							.then(function (response) {
								if (response.data.lista_evaluaciones.length == 0) {
									self.lista_evaluaciones = [];

									return Swal.fire({
										icon: "info",
										title: "¡Ups!",
										text: "No se encontraron datos",
										allowOutsideClick: true,
									});
								} else {
									self.lista_evaluaciones = response.data.lista_evaluaciones;
									return Swal.fire({
										icon: "success",
										title: "¡Listo!",
									});
								}
							});
					},
				});
			}
		},
	},
};
</script>

<style lang="css">
.slot-seguimiento {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-seguimiento {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
