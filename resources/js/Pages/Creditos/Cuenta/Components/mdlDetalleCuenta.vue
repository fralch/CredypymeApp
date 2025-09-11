<template>
	<div>
		<div class="card-title" style="color: var(--azulOscuroEmpresarial)">
			{{
				detalle_cuenta != null
					? detalle_cuenta.usuario + " - " + detalle_cuenta.agencia
					: ""
			}}
		</div>
		<div class="card-body card-block">
			<div class="form-group col-md-6 offset-md-3 col-12">
				<label class="label-title">MONTO ACTUAL</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">S/</span>
					</div>
					<input
						type="text"
						class="form-control text-center"
						style="height: 50px; font-size: 35px; color: var(--colorAlto)"
						:value="
							detalle_cuenta != null
								? RedondearVista(detalle_cuenta.monto, 2)
								: ''
						"
						readonly
					/>
				</div>
			</div>

			<div class="form-row justify-content-md-center">
				<fieldset class="form-group col-md-10">
					<legend>
						<label class="label-title">FILTROS DE BÚSQUEDA</label>
					</legend>
					<div class="row">
						<div class="input-group col-md-6">
							<div class="input-group-prepend">
								<span class="input-group-text prepend-title">DESDE</span>
							</div>
							<input
								type="date"
								class="form-control input-information center"
								v-model="fecha_desde"
								style="font-size: 15px !important"
							/>
						</div>
						<div class="input-group col-md-6">
							<div class="input-group-prepend">
								<span class="input-group-text prepend-title">HASTA</span>
							</div>
							<input
								type="date"
								class="form-control input-information center"
								v-model="fecha_hasta"
								style="font-size: 15px !important"
							/>
						</div>
						<div class="col-md-1 text-right" v-if="windowWidth < 900">
							<button
								class="btn btn-action btn-icon-split mt-3"
								title="Buscar"
								@click="Buscar"
							>
								<span class="icon text-white" style="font-size: 15px">
									<i class="fas fa-search"></i>
								</span>
							</button>
						</div>
					</div>
				</fieldset>
				<div class="col-md-1" v-if="windowWidth >= 900">
					<button
						class="btn btn-action btn-icon-split mt-2"
						title="Buscar"
						@click="Buscar"
					>
						<span class="icon text-white" style="font-size: 25px">
							<i class="fas fa-search"></i>
						</span>
					</button>
				</div>
			</div>

			<div class="form-row">
				<div class="input-group col-md-4">
					<div class="input-group-prepend">
						<span class="input-group-text prepend-title">INGRESOS</span>
					</div>
					<input
						type="text"
						class="form-control center bolder ingreso"
						onkeydown="return false"
						spellcheck="false"
						:value="'S/ ' + RedondearVista(this.total_ingresos, 2)"
						style="font-size: 14px"
					/>
				</div>
				<div class="input-group col-md-4">
					<div class="input-group-prepend">
						<span class="input-group-text prepend-title">EGRESOS</span>
					</div>
					<input
						type="text"
						class="form-control center bolder egreso"
						onkeydown="return false"
						spellcheck="false"
						:value="'S/ ' + RedondearVista(this.total_egresos, 2)"
						style="font-size: 14px"
					/>
				</div>
				<div class="input-group col-md-4">
					<div class="input-group-prepend">
						<span class="input-group-text prepend-title">DIFERENCIA</span>
					</div>
					<input
						type="text"
						class="form-control center bolder diferencia"
						onkeydown="return false"
						spellcheck="false"
						:value="
							'S/ ' +
							RedondearVista(this.total_ingresos - this.total_egresos, 2)
						"
						style="font-size: 14px"
					/>
				</div>
			</div>

			<hr />

			<DataTable
				:value="lista_movimientos"
				:scrollable="true"
				scrollDirection="both"
				scrollHeight="340px"
				selectionMode="single"
				:rows="30"
			>
				<Column
					field="fecha_movimiento"
					header="FECHA"
					:styles="{
						width: '120px',
						justifyContent: 'center',
					}"
				>
				</Column>

				<Column
					field="tipo"
					header="TIPO"
					:styles="{
						width: '70px',
						justifyContent: 'center',
					}"
				>
					<template #body="{ data }">
						<div
							class="p-2 bolder text-center"
							:class="[
								data.tipo == 'I' ? 'ingreso' : data.tipo == 'E' ? 'egreso' : '',
							]"
							style="width: 100% !important"
						>
							{{ data.tipo }}
						</div>
					</template>
				</Column>
				<Column
					field="descripcion"
					header="DESCRIPCIÓN"
					:styles="{
						width: '225px',
						justifyContent: 'left',
					}"
				>
				</Column>
				<Column
					field="monto"
					header="MONTO (S/)"
					:styles="{
						width: '100px',
						justifyContent: 'right',
					}"
				>
					<template #body="{ data }">
						<div class="bolder" style="font-size: 15px">
							{{ data.tipo == "I" ? "+ " : "- "
							}}{{ RedondearVista(data.monto, 2) }}
						</div>
					</template>
				</Column>
			</DataTable>
			<hr />
			<div class="text-right">
				<button
					class="btn btn-action btn-icon-split"
					@click="Exportar"
					title="Exportar MOVIMIENTOS"
				>
					<span class="icon text-white">
						<i class="fas fa-file-excel"></i
					></span>
					<span class="text">EXPORTAR</span>
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
	props: {
		tipo: String,
	},
	components: {
		headerCloseModal,
		DataTable,
		Column,
	},
	data() {
		return {
			detalle_cuenta: [],

			windowWidth: window.innerWidth,

			lista_movimientos: [],

			fecha_desde: null,
			fecha_hasta: null,
			title_modal: null,
		};
	},
	computed: {
		total_ingresos() {
			let lista_ingresos = this.lista_movimientos.filter(
				(item) => item.tipo == "I"
			);

			let sumatoria = lista_ingresos.reduce(
				(total, obj) => total + parseFloat(obj.monto),
				0
			);

			return sumatoria;
		},
		total_egresos() {
			let lista_ingresos = this.lista_movimientos.filter(
				(item) => item.tipo == "E"
			);

			let sumatoria = lista_ingresos.reduce(
				(total, obj) => total + parseFloat(obj.monto),
				0
			);

			return sumatoria;
		},
	},

	watch: {
		detalle_cuenta(value) {
			this.UltimosMovimientos(value);
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		// RedondearValor(value, decimal_places) {
		// 	let valor = 0;
		// 	let numero_decimales = decimal_places;

		// 	if (value) {
		// 		valor = value;
		// 	}
		// 	return parseFloat(valor).toFixed(numero_decimales);
		// },

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

		async UltimosMovimientos(cuenta) {
			await axios
				.post(
					route("cue.ultimos_movimientos", {
						cuenta_id: cuenta.id,
						agencia_id: cuenta.agencia_id,
					})
				)
				.then((response) => {
					this.fecha_desde = response.data.fecha_desde;
					this.fecha_hasta = response.data.fecha_hasta;
					this.lista_movimientos = response.data.ultimos_movimientos;
				});
		},

		async Buscar() {
			let data = new FormData();

			data.append("detalle_cuenta", JSON.stringify(this.detalle_cuenta));

			data.append("fecha_desde", this.fecha_desde);

			data.append("fecha_hasta", this.fecha_hasta);

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					Swal.showLoading();
					// this.$inertia.post(route("cue.listar_movimientos"), data);
					// return false;

					await axios
						.post(route("cue.listar_movimientos"), data)
						.then((response) => {
							if (response.data.lista_movimientos.length == 0) {
								this.lista_movimientos = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								this.lista_movimientos = response.data.lista_movimientos;

								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
									timer: 1200,
									showConfirmButton: false,
								});
							}
						});
				},
			});
		},

		async Exportar() {
			let data = new FormData();

			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("detalle_cuenta", JSON.stringify(this.detalle_cuenta));

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.post(route("cue.movimientos.exportar"), data);
					// return false;

					Swal.showLoading();

					await axios
						.post(route("cue.movimientos.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.click();
							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
								timer: 2000,
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
.ingreso {
	color: white !important;
	background: var(--verdeOscuroEmpresarial) !important;
}
.egreso {
	color: white !important;
	background: var(--red) !important;
}
.diferencia {
	color: white !important;
	background: var(--azulOscuroEmpresarial) !important;
}
</style>

