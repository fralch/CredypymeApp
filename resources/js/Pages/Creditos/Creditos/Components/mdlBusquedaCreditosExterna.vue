<template>
	<div id="mdlBusquedaCreditosExterna" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlBusquedaCreditosExterna">
			<div class="content contentBusquedaClientes" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>{{ "BÚSQUEDA DE CRÉDITOS - EXTERNO" }}</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarModal('mdlBusquedaCreditosExterna')"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>

					<div class="card-body card-block" id="contentBusquedaClientes">
						<div class="form-row row justify-content-md-center">
							<div class="form-group col-md-3 col-6">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="busqueda_creditos_filtro"
										id="rdbApellidosNombres"
										value="apellidos_nombres"
										v-model="tipo_filtro"
										checked
									/>
									<label class="label-title" for="rdbApellidosNombres"
										>APELLIDOS_NOMBRES
									</label>
								</div>
							</div>
							<div class="form-group col-md-3 col-6">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="busqueda_creditos_filtro"
										id="rdbDni"
										value="dni"
										v-model="tipo_filtro"
									/>
									<label class="label-title" for="rdbDni">DNI</label>
								</div>
							</div>
							<div class="form-group col-md-3 col-4">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="busqueda_creditos_filtro"
										id="rdbExpediente"
										value="expediente"
										v-model="tipo_filtro"
									/>
									<label class="label-title" for="rdbExpediente"
										>EXPEDIENTE
									</label>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-5 col-12 'col-md-5'">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											><i class="fas fa-search"></i
										></span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										ref="buscar_credito"
										v-model="texto_buscar"
									/>
								</div>
							</div>
							<div class="form-group col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_seleccionada"
										@change="ListarCreditos"
									>
										<option :value="1" selected>TAMBO</option>
										<option :value="6">CHILCA</option>
										<option :value="4">HUANCAVELICA</option>
									</select>
								</div>
							</div>
							<div class="input-group col-md-3">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbCanceladosParcial"
											v-model="cancelados_parcial"
										/>
									</div>
								</div>
								<div class="input-group-append">
									<label
										class="input-group-text prepend-title"
										for="chbCanceladosParcial"
										style="font-size: 13px"
									>
										PARCIALES
									</label>
								</div>
							</div>
						</div>

						<table
							class="table"
							id="tblCreditosResultadoExterna"
							width="100% !important"
						>
							<thead>
								<tr>
									<th style="min-width: 50px !important">EXP.</th>
									<th style="min-width: 200px !important">CLIENTE</th>
									<th style="min-width: 100px !important">
										FECHA_{{ titulo_header }}
									</th>
									<th style="min-width: 70px !important">MONTO</th>
									<th style="min-width: 70px !important">PLAZO</th>
									<th style="min-width: 70px !important">CUOTA</th>
									<th style="min-width: 70px !important">ATRASO</th>

									<th style="min-width: 120px !important">ASESOR</th>
									<th style="min-width: 120px !important">ESTADO</th>
									<th style="min-width: 100px !important">TIPO</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_creditos"
									:key="index"
									:id="item.id"
									:class="[index % 2 == 0 ? 'verde-claro' : '']"
									@dblclick="Redirigir(item)"
								>
									<td align="center">
										{{ item.numero_expediente + "-" + item.numero_credito }}
									</td>

									<td>
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td align="center">
										{{ JSON.parse(item.datos_creacion).fecha }}
									</td>
									<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
									<td align="center">
										{{
											roundTo(item.plazo, 0) +
											" " +
											periodo_medicion(item.periodo_pago)
										}}
									</td>
									<td align="right">S/ {{ roundTo(item.cuota, 2) }}</td>
									<td align="center">
										{{ item.dias_atraso + " d" }}
									</td>
									<td align="center">
										{{ item.usuario_asesor }}
									</td>
									<td align="center">
										{{ item.estado }}
									</td>
									<td align="center">
										{{ item.tipo }}
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
			windowWidth: window.innerWidth,

			texto_buscar: null,

			agencia_seleccionada: 1,
			tipo_filtro: "apellidos_nombres",
			lista_creditos: [],
			nombre_modulo: null,

			cancelados_parcial: false,
		};
	},
	watch: {
		texto_buscar(value) {
			this.BuscarCreditos();
		},

		lista_creditos() {
			$("#tblCreditosResultadoExterna").DataTable().destroy();
			this.TablaListaCreditos();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		this.TablaListaCreditos();
	},
	computed: {
		titulo_header() {
			return "DESEMBOLSO";
		},
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

		periodo_medicion(value) {
			if (value == "DIARIO") {
				return "(DÍAS)";
			} else if (value == "SEMANAL") {
				return "(SEMANAS)";
			} else if (value == "QUINCENAL") {
				return "(QUINCENAS)";
			} else if (value == "MENSUAL") {
				return "(MESES)";
			}
			return "(DÍAS)";
		},
		TablaListaCreditos() {
			this.$nextTick(() => {
				var table = $("#tblCreditosResultadoExterna").DataTable({
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

				$("#inpBuscarCredito").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		ListarCreditos() {
			this.BuscarCreditos();
		},
		async BuscarCreditos() {
			let self = this;

			if (this.agencia_seleccionada == null) {
				return false;
			}

			if (this.texto_buscar && this.texto_buscar.length >= 3) {
				const params = {
					texto_buscar: this.texto_buscar,
					tipo_filtro: this.tipo_filtro,
					cancelados_parcial: this.cancelados_parcial,
					agencia_id: this.agencia_seleccionada,
				};

				await axios
					.get(api_externa + "/api/caj/desembolso_externo/buscar", { params })
					.then((response) => {
						this.lista_creditos = response.data.lista_creditos;
					})
					.catch((error) => {
						console.log(error);
						self.$swal({
							icon: "error",
							title: "Error",
							text: "Ocurrió un error al buscar créditos.",
						});
					});
			} else {
				this.lista_creditos = [];
			}
		},
		Redirigir(credito) {
			let object = {};

			object = {
				credito_id: credito.id,
				agencia_id: this.agencia_seleccionada,
			};

			this.$inertia.get(route("caj.cobranza_externa", object));
		},
		CerrarModal(modal) {
			$("#" + modal).css("display", "none");
		},
	},
};
</script>

<style lang="css">
.mdlBusquedaCreditosExterna {
	/* position: fixed; */
	height: fit-content;
	margin-top: 5%;
	/* right: 0; */
	margin-bottom: 0;
}

#contentBusquedaClientes {
	height: auto;
}

.contentBusquedaClientes {
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

#tblCreditosResultadoExterna_length {
	display: none;
}
@media (max-width: 900px) {
	.mdlBusquedaCreditosExterna {
		margin-top: 15% !important;
	}
}
</style>
