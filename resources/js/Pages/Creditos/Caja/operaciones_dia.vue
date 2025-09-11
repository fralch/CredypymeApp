<template>
	<layout ref="layout">
		<div class="slot_body slot-operaciones-dia" slot="component-view">
			<div class="content" style="display: block">
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'OPERACIONES DEL DÍA'"></headerClose>
						<div class="card-title">DETALLE</div>
						<div class="card-body card-block">
							<div class="form-row">
								<div class="form-group col-md-5 mt-2 col-9">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
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
								<div class="boton_style col-md-1 ml-3 col-2">
									<button
										class="btn btn-action btn-icon-split"
										title="Buscar"
										@click="ListarOperaciones"
									>
										<span
											class="icon text-white"
											:style="
												windowWidth >= 900
													? 'font-size: 25px'
													: 'font-size: 12px '
											"
										>
											<i class="fas fa-sync-alt"></i>
										</span>
									</button>
								</div>
								<div
									class="form-group col-md-5 mt-1"
									:style="
										windowWidth >= 900
											? 'text-align: right !important'
											: 'text-align: center !important'
									"
								>
									<button
										class="btn btn-action btn-icon-split"
										@click="Exportar('PDF')"
										title="IMPRIMIR OPERACIONES"
									>
										<span class="icon text-white">
											<i class="fas fa-file-pdf"></i>
										</span>
										<span class="text">IMPRIMIR</span>
									</button>
									<button
										class="btn btn-cancel btn-icon-split"
										@click="Exportar('XLSX')"
										title="EXPORTAR OPERACIONES"
									>
										<span class="icon text-white">
											<i class="fas fa-file-excel"></i>
										</span>
										<span class="text">EXPORTAR</span>
									</button>
								</div>
							</div>
							<div class="card-title mt-1 mb-1">LISTA DE RESULTADOS</div>

							<table
								class="table"
								width="100%"
								v-for="(item_1, index_1) in cuentas_filtradas"
								:key="'cue_' + index_1"
							>
								<thead>
									<tr>
										<th
											class="text-center mayus"
											colspan="4"
											style="font-size: 12px"
										>
											{{ item_1.apellido_paterno }}
											{{ item_1.apellido_materno }}
											{{ item_1.nombres }}
										</th>
									</tr>
									<tr>
										<th
											class="bg-white text-dark"
											:style="
												windowWidth >= 900
													? 'width: 300px !important'
													: 'width: 100px !important'
											"
										>
											CONCEPTO
										</th>
										<th class="bg-white text-dark">INGRESOS</th>
										<th class="bg-white text-dark">EGRESOS</th>
										<th class="bg-white text-dark">TOTAL</th>
									</tr>
								</thead>
								<tbody>
									<tr class="table-bordered">
										<td
											class="bolder"
											style="font-size: 12px; padding-left: 20px !important"
										>
											**** CUENTA
										</td>
										<td class="bolder" align="right">
											S/
											{{
												roundTo(
													cuentas_filtradas.filter(
														(cuenta) => cuenta.dni == item_1.dni
													)[0].monto,
													2
												)
											}}
										</td>
										<td class="bolder" align="right">S/ 0.00</td>
										<td>{{ null }}</td>
									</tr>
									<tr
										v-for="(item_2, index_2) in lista_operaciones.filter(
											(ope) => ope.usuario_cuenta == item_1.dni
										)"
										:key="'dat_' + index_2"
										class="table-bordered"
										:class="[index_2 % 2 == 0 ? 'verde-claro' : '']"
									>
										<td style="font-size: 12px; padding-left: 30px !important">
											{{ item_2.operaciones_caja.concepto }}
										</td>
										<td align="right">
											{{
												item_2.operaciones_caja.ingresos == 0
													? "-"
													: "S/ " + roundTo(item_2.operaciones_caja.ingresos, 2)
											}}
										</td>
										<td align="right">
											{{
												item_2.operaciones_caja.egresos == 0
													? "-"
													: "S/ " + roundTo(item_2.operaciones_caja.egresos, 2)
											}}
										</td>
										<td>
											{{ null }}
										</td>
									</tr>
									<tr>
										<td class="font-11 blue text-white" align="right">
											SUB TOTAL
										</td>
										<td class="font-11 blue text-white" align="right">
											S/
											{{
												roundTo(
													lista_subtotales.length > 0
														? lista_subtotales.filter(
																(sub) => sub.usuario_cuenta == item_1.dni
														  )[0].subtotal_ingresos
														: 0,
													2
												)
											}}
										</td>
										<td class="font-11 blue text-white" align="right">
											S/
											{{
												roundTo(
													lista_subtotales.length > 0
														? lista_subtotales.filter(
																(sub) => sub.usuario_cuenta == item_1.dni
														  )[0].subtotal_egresos
														: 0,
													2
												)
											}}
										</td>
										<td class="font-11 blue text-white" align="right">
											S/
											{{
												roundTo(
													lista_subtotales.length > 0
														? lista_subtotales.filter(
																(sub) => sub.usuario_cuenta == item_1.dni
														  )[0].subtotal_ingresos -
																lista_subtotales.filter(
																	(sub) => sub.usuario_cuenta == item_1.dni
																)[0].subtotal_egresos
														: 0,
													2
												)
											}}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<!-- ---------------------- -->
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

export default {
	components: { layout, headerClose },
	props: { cuentas: Array },
	data() {
		return {
			agencias_permitidas: [],
			agencia_seleccionada: 0,
			windowWidth: window.innerWidth,

			cuentas_filtradas: [],
			lista_operaciones: [],
			lista_subtotales: [],
			totales: {
				total_ingresos: 0,
				total_egresos: 0,
			},
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
					this.agencia_seleccionada = null;
				}
			}
		},

		agencia_seleccionada() {
			this.FiltrarCuentas();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		roundTo(value, decimal_places) {
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
		FiltrarCuentas() {
			this.cuentas_filtradas = this.cuentas.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
			this.CalcularTotales();
		},
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/OPERACIONES_DIA"
			);
		},
		async ListarOperaciones() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("cuentas", JSON.stringify(this.cuentas_filtradas));

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.post(route("caj.operaciones_dia.listar"), data);
					// return false;

					Swal.showLoading();
					await axios
						.post(route("caj.operaciones_dia.listar"), data)
						.then(function (response) {
							self.lista_operaciones = response.data.lista_operaciones;

							self.CalcularTotales();
							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								timer: 1200,
								showConfirmButton: false,
							});
						});
				},
			});
		},

		CalcularTotales() {
			let operaciones = [];
			this.lista_subtotales = [];

			this.cuentas_filtradas.forEach((element_1) => {
				operaciones = this.lista_operaciones.filter(
					(item) => item.usuario_cuenta == element_1.dni
				);

				let subtotal_ingresos = parseFloat(element_1.monto);
				let subtotal_egresos = 0;

				operaciones.forEach((element_2) => {
					subtotal_ingresos += parseFloat(element_2.operaciones_caja.ingresos);
					subtotal_egresos += parseFloat(element_2.operaciones_caja.egresos);
				});

				let object = {
					usuario_cuenta: element_1.dni,
					subtotal_ingresos: subtotal_ingresos,
					subtotal_egresos: subtotal_egresos,
				};

				this.lista_subtotales.push(object);
			});

			let total_ingresos = 0;
			let total_egresos = 0;
			this.lista_subtotales.forEach((element) => {
				total_ingresos += parseFloat(element.subtotal_ingresos);
				total_egresos += parseFloat(element.subtotal_egresos);

				this.totales.total_ingresos = total_ingresos;
				this.totales.total_egresos = total_egresos;
			});
		},

		Exportar(tipo) {
			let data = new FormData();

			data.append("agencia_id", this.agencia_seleccionada);
			data.append("cuentas_filtradas", JSON.stringify(this.cuentas_filtradas));
			data.append("lista_operaciones", JSON.stringify(this.lista_operaciones));
			data.append("lista_subtotales", JSON.stringify(this.lista_subtotales));
			data.append("totales", JSON.stringify(this.totales));

			data.append("tipo", tipo);

			let titulo = "";

			if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			} else if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			}

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(route("caj.operaciones_dia.exportar"), data);

					axios
						.post(route("caj.operaciones_dia.exportar"), data)
						.then(function (response) {
							if (tipo == "XLSX") {
								let linkSource =
									"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
									response.data;
								let downloadLink = document.createElement("a");
								let fileName = "rptOperacionesDia.xlsx";

								downloadLink.href = linkSource;
								downloadLink.download = fileName;
								downloadLink.click();
							} else if (tipo == "PDF") {
								const contentType = "application/pdf",
									b64Data = response.data, //La información en base 64
									blob = b64toBlob(b64Data, contentType), //Decodificar la información
									blobUrl = URL.createObjectURL(blob);
								// Crear un IFrame.
								let iframe = document.createElement("iframe");
								// Ocultar el IFrame.
								iframe.style.display = "none";
								// Definir el source.
								iframe.src = blobUrl;
								// Añadir el IFrame a una página web.
								document.body.appendChild(iframe);
								iframe.contentWindow.focus();
								iframe.contentWindow.print(); // Imprimir.
							}

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
.slot-operaciones-dia {
	width: 50% !important;
	margin-left: 25% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}

@media only screen and (max-width: 900px) {
	.slot-operaciones-dia {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.boton_style {
		margin-top: 8px !important;
	}
}
</style>



