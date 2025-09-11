<template>
	<div id="mdlBusquedaInversionesExterna" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlBusquedaInversionesExterna">
			<div class="content contentBusquedaInversiones" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>{{ "BÚSQUEDA DE INVERSIONES - EXTERNA " }}</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarModal('mdlBusquedaInversionesExterna')"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>

					<div class="card-title">INFORMACIÓN PERSONAL</div>
					<div class="card-body card-block" id="contentBusquedaInversiones">
						<div class="form-row row justify-content-md-center">
							<div class="form-group col-md-8 col-12">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Buscar </span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										@focus="hidenav()"
										@blur="shownav()"
										ref="buscar_inversion"
										v-model="texto_buscar"
									/>
								</div>
							</div>
							<div class="form-group col-md-4 col-12">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Ag. </span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_seleccionada"
										@change="ListarInversiones"
									>
										<option :value="1" selected>TAMBO</option>
										<option :value="6">CHILCA</option>
										<option :value="4">HUANCAVELICA</option>
									</select>
								</div>
							</div>
						</div>

						<table
							class="table"
							id="tblInversionesExternaResultado"
							width="100% !important"
						>
							<thead>
								<tr>
									<th style="min-width: 200px !important">CLIENTE</th>
									<th style="min-width: 50px !important">DNI</th>
									<th style="min-width: 100px !important">FECHA_APERTURA</th>
									<th style="min-width: 30px !important">TIPO</th>
									<th style="min-width: 100px !important">TIPO_PRODUCTO</th>
									<th style="min-width: 50px !important">TASA</th>
									<th style="min-width: 50px !important">PLAZO</th>
									<th style="min-width: 70px !important">MONTO_INICIAL</th>
									<th style="min-width: 70px !important">ACUMULADO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_inversiones"
									:key="index"
									:id="item.id"
									:class="[index % 2 == 0 ? 'verde-claro' : '']"
									@dblclick="Redirigir(item)"
								>
									<td align="left">
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>

									<td align="center">
										{{ item.dni }}
									</td>
									<td align="center">
										{{ item.fecha_apertura }}
									</td>
									<td align="center">MET</td>
									<td align="center">
										{{ item.producto }}
									</td>
									<td align="center">0.00 %</td>
									<td align="center">-</td>
									<td align="center">-</td>
									<td align="right">
										S/ {{ item.acumulado == 0 ? "-" : item.acumulado }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

const api_externa = import.meta.env.VITE_S_API_EXTERNA;
export default {
	components: { headerClose },

	data() {
		return {
			texto_buscar: null,

			agencia_seleccionada: 1,
			lista_inversiones: [],

			nombre_modulo: null,
		};
	},
	watch: {
		texto_buscar() {
			this.ListarInversiones();
		},

		lista_inversiones() {
			$("#tblInversionesExternaResultado").DataTable().destroy();
			this.TablaListaInversiones();
		},
	},
	mounted() {
		this.TablaListaInversiones();
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
		hidenav() {
			return this.$parent.hide_nav();
		},
		shownav() {
			return this.$parent.show_nav();
		},
		TablaListaInversiones() {
			this.$nextTick(() => {
				var table = $("#tblInversionesExternaResultado").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
					select: {
						style: "single",
						info: false,
					},
					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						infoPostFix: "",
						thousands: ",",
						lengthMenu: "Agrupar por _MENU_ filas",
						loadingRecords: "Cargando...",
						processing: "Procesando...",
						search: "Buscar:",
						zeroRecords: "No se encontraron registros",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
						aria: {
							sortAscending: ": activar para ordenar de forma ascendente",
							sortDescending: ": activar para ordenar de forma descendente",
						},
					},
					responsive: true,
				});

				$("#inpBuscarInversion").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		async ListarInversiones() {
			console.log(this.texto_buscar);

			if (this.agencia_seleccionada == null) {
				return false;
			}

			if (this.texto_buscar && this.texto_buscar.length >= 3) {
				const params = {
					texto_buscar: this.texto_buscar,
					agencia_id: this.agencia_seleccionada,
				};

				await axios
					.get(api_externa + "/api/inv/meta_externa/buscar", { params })
					.then((response) => {
						console.log(response.data);
						this.lista_inversiones = response.data.lista_inversiones;
					})
					.catch((error) => {
						console.log(error);
						Swal.fire({
							icon: "error",
							title: "Error",
							text: "Ocurrió un error al buscar las inversiones.",
						});
					});
			} else {
				this.lista_inversiones = [];
			}
		},
		Redirigir(inversion) {
			let object = {};
			let ruta = null;
			console.log(inversion);

			ruta = "inv.meta_externa";
			const params = {
				agencia_id: this.agencia_seleccionada,
				inversion_id: inversion.id,
			};

			this.$inertia.get(route(ruta), params);
		},
		CerrarModal(modal) {
			$("#" + modal).css("display", "none");
		},
	},
};
</script>

<style lang="css">
.mdlBusquedaInversionesExterna {
	/* position: fixed; */
	height: fit-content;
	margin-top: 5%;
	/* right: 0; */
	margin-bottom: 0;
}

#contentBusquedaInversiones {
	height: auto;
}

.contentBusquedaInversiones {
	bottom: 0;
}
/* Para corregir bug de datatable */
.dataTable {
	width: 100% !important;
}
.dataTables_scrollHeadInner {
	width: 100% !important;
}
.DTFC_ScrollWrapper {
	height: auto !important;
}
/* --------------------------------- */

#tblInversionesExternaResultado_length {
	display: none;
}
</style>
