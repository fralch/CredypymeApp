<template>
	<div id="mdlBusquedaInversiones" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlBusquedaInversiones">
			<div class="content contentBusquedaInversiones" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>{{ "BÚSQUEDA DE INVERSIONES " }}</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarModal('mdlBusquedaInversiones')"
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
										<option
											v-for="(agencia, index) in agencias_permitidas"
											:key="index"
											:value="agencia.id"
										>
											{{ agencia.agencia }}
										</option>
									</select>
								</div>
							</div>
						</div>

						<table
							class="table"
							id="tblInversionesResultado"
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
export default {
	components: { headerClose },

	data() {
		return {
			texto_buscar: null,
			agencias_permitidas: [],
			agencia_seleccionada: null,
			lista_inversiones: [],
			ruta: "cre.index",
			ruta_busqueda: null,
			nombre_modulo: null,
		};
	},
	watch: {
		texto_buscar() {
			if (this.nombre_modulo == "buscar_inversiones") {
				this.ListarInversiones();
			}
		},

		lista_inversiones() {
			$("#tblInversionesResultado").DataTable().destroy();
			this.TablaListaInversiones();
		},
		nombre_modulo(value) {
			if (value == "buscar_inversiones") {
				this.ruta = "inv.meta";
				this.ruta_busqueda = "inv.listar";
			}
		},

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
	},
	mounted() {
		this.TablaListaInversiones();
	},
	computed: {},
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
				var table = $("#tblInversionesResultado").DataTable({
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

		ListarInversiones() {
			let self = this;

			if (this.agencia_seleccionada == null) {
				return false;
			}

			if (this.texto_buscar && this.texto_buscar.length >= 3) {
				let data = new FormData();
				data.append("texto_buscar", this.texto_buscar);
				data.append("agencia_id", this.agencia_seleccionada);

				// self.$inertia.post(route(self.ruta_busqueda),data);
				// return false

				axios.post(route(self.ruta_busqueda), data).then(function (response) {
					self.lista_inversiones = response.data;
				});
			} else {
				this.lista_inversiones = [];
			}
		},
		Redirigir(inversion) {
			let object = {};
			let ruta = null;
			console.log(inversion);
			if (inversion.tipo == "META") {
				ruta = "inv.meta";
				object = {
					agencia_id: this.agencia_seleccionada,
					inversion_id: inversion.id,
				};
			}

			this.$inertia.get(route(ruta, object));
		},
		CerrarModal(modal) {
			$("#" + modal).css("display", "none");
		},
	},
};
</script>

<style lang="css">
.mdlBusquedaInversiones {
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

#tblInversionesResultado_length {
	display: none;
}
</style>
