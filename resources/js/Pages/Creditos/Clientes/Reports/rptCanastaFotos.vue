<template>
	<!-- ------ AREA IMPRIMIBLE --------- -->
	<div id="rptCanastaFotos">
		<div
			v-for="(item_1, index_1) in fotos_bloques"
			:key="index_1"
			class="report-container"
		>
			<table
				:style="
					formato_impresion == 'evaluacion'
						? 'margin-bottom: 155px !important'
						: 'margin-bottom: 215px !important'
				"
				width="100% !important"
			>
				<tbody>
					<tr>
						<h5>
							<b>{{ "Resumen de Fotografías".toUpperCase() }}</b> -
							<b style="font-size: 11px">{{ cliente }}</b>
						</h5>
					</tr>
					<hr />

					<tr class="form-row col-12">
						<td
							class="form-group"
							:class="
								formato_impresion == 'evaluacion' ? 'col-3 mt-1' : 'col-6 mt-3'
							"
							v-for="(item_2, index_2) in item_1"
							:key="index_2"
						>
							<p class="text center" v-if="formato_impresion == 'cliente'">
								{{
									index_2 % 2 == 0
										? tipo_cliente == "titular"
											? "FOTO TITULAR"
											: "FOTO AVAL"
										: tipo_cliente == "titular"
										? "FOTO TITULAR PARIENTE"
										: "FOTO AVAL PARIENTE"
								}}
							</p>
							<img
								v-bind:src="
									'/imagenes_server/creditos/clientes/album/' +
									agencia_id +
									'/' +
									item_2.imagen.substring(0, 4) +
									'/' +
									item_2.imagen
								"
								width="100%"
								:height="formato_impresion == 'evaluacion' ? 130 : 400"
							/>
							<p
								class="text m-0 p-0"
								style="
									font-size: 10px;
									color: black;
									-webkit-print-color-adjust: exact;
								"
							>
								{{ item_2.categoria }}
							</p>
							<p
								class="text m-0 p-0"
								style="
									font-size: 10px;
									color: black;
									-webkit-print-color-adjust: exact;
								"
							>
								{{ item_2.descripcion }}
							</p>
							<p
								class="text m-0 p-0"
								style="
									font-size: 10px;
									color: black;
									-webkit-print-color-adjust: exact;
								"
							>
								{{ item_2.comentario }}
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<reportFooter :fecha_hora_actual="fecha_hora_actual">
				<table width="100% !important" slot="sign-content">
					<tbody>
						<tr class="form-row col-12">
							<td
								class="mr-4"
								width="22.5%"
								style="border-top: 1px solid black"
								align="center"
							>
								Firma Cliente
							</td>
							<td
								class="mr-4"
								width="22.5%"
								style="border-top: 1px solid black"
								align="center"
							>
								Firma Asesor
							</td>
							<td
								class="mr-4"
								width="22.5%"
								style="border-top: 1px solid black"
								align="center"
							>
								Firma Jefe Créditos
							</td>
							<td
								class="mr-4"
								width="22.5%"
								style="border-top: 1px solid black"
								align="center"
							>
								Firma Jefe Operaciones
							</td>
						</tr>
					</tbody>
				</table>
			</reportFooter>
		</div>
	</div>
	<!-- -----FIN IMPRIMIBLE ---------- -->
</template>

<script>
import reportFooter from "@/Pages/Creditos/Components/report_footer.vue";
export default {
	components: { reportFooter },
	props: { agencia_id: Number },
	data() {
		return {
			cliente: {},
			formato_impresion: null,
			tipo_cliente: null,
			fotos: [],
			fotos_bloques: [],
			fecha_hora_actual: null,
		};
	},

	watch: {
		fotos() {
			this.Paginar();
		},
		formato_impresion() {
			this.Paginar();
		},
	},
	methods: {
		Paginar() {
			let items = this.fotos.length;
			this.fotos_bloques = [];
			if (this.formato_impresion == "evaluacion") {
				let tablas = 0;

				if (items % 9 != 0) {
					tablas = items / 9 + 1;
				} else {
					tablas = items / 9;
				}

				// let cantidad_tablas = 1;
				let cantidad_tablas = parseInt(Math.floor(tablas));

				let i_1 = 0;
				let i_2 = 0;
				let bloque = [];
				let limite = 9;
				let inicio = 0;
				for (i_1 = 0; i_1 < cantidad_tablas; i_1++) {
					if (limite > items) {
						limite = items;
					}
					for (i_2 = inicio; i_2 < limite; i_2++) {
						bloque.push(this.fotos[i_2]);
						inicio += 1;
					}
					this.fotos_bloques.push(bloque);
					bloque = [];

					limite += 9;
				}
			} else if (this.formato_impresion == "cliente") {
				let tablas = 0;

				if (items % 2 != 0) {
					tablas = items / 2 + 1;
				} else {
					tablas = items / 2;
				}

				// let cantidad_tablas = 1;
				let cantidad_tablas = parseInt(Math.floor(tablas));

				let i_1 = 0;
				let i_2 = 0;
				let bloque = [];
				let limite = 2;
				let inicio = 0;
				for (i_1 = 0; i_1 < cantidad_tablas; i_1++) {
					if (limite > items) {
						limite = items;
					}
					for (i_2 = inicio; i_2 < limite; i_2++) {
						bloque.push(this.fotos[i_2]);
						inicio += 1;
					}
					this.fotos_bloques.push(bloque);
					bloque = [];

					limite += 2;
				}
			}
		},
	},
};
</script>

<style lang="css" media="print">
#rptCanastaFotos {
	/* margin: 5mm !important; */
	size: landscape !important;
	width: 100% !important;
	position: absolute;
	display: none;
}
.report-container {
	/* background-color: var(--green); */
	position: relative;
	width: 100% !important;
	height: 100% !important;
	-webkit-print-color-adjust: exact;
}
</style>
