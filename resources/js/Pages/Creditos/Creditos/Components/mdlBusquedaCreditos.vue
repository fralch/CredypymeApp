<template>
	<div id="mdlBusquedaCreditos" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlBusquedaCreditos">
			<div class="content contentBusquedaClientes" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>{{ "BÚSQUEDA DE CRÉDITOS " + titulo_modal }}</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarModal('mdlBusquedaCreditos')"
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
							<div
								class="form-group col-md-5 col-12"
								:class="nombre_modulo == 'cobranza' ? 'col-md-5' : 'col-md-7'"
							>
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
										@focus="hidenav()"
										@blur="shownav()"
										ref="buscar_credito"
										v-model="texto_buscar"
									/>
								</div>
							</div>
							<div
								class="form-group"
								:class="nombre_modulo == 'cobranza' ? 'col-md-4' : 'col-md-5'"
							>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_seleccionada"
										@change="ListarCreditos"
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
							<div
								class="input-group col-md-3"
								v-if="nombre_modulo == 'cobranza'"
							>
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
							id="tblCreditosResultado"
							width="100% !important"
						>
							<thead>
								<tr>
									<th
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
										style="min-width: 50px !important"
									>
										EXP.
									</th>
									<th style="min-width: 200px !important">CLIENTE</th>
									<th style="min-width: 100px !important">
										FECHA_{{ titulo_header }}
									</th>
									<th style="min-width: 70px !important">MONTO</th>
									<th style="min-width: 70px !important">PLAZO</th>
									<th
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
										style="min-width: 70px !important"
									>
										CUOTA
									</th>
									<th
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
										style="min-width: 70px !important"
									>
										ATRASO
									</th>

									<th style="min-width: 120px !important">ASESOR</th>
									<th
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
										style="min-width: 120px !important"
									>
										ESTADO
									</th>
									<th
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
										style="min-width: 100px !important"
									>
										TIPO
									</th>
									<th
										v-show="
											nombre_modulo == 'propuesta' ||
											nombre_modulo == 'copia_propuesta' ||
											nombre_modulo == 'aprobacion' ||
											nombre_modulo == 'copia_aprobacion'
										"
										style="min-width: 80px !important"
									>
										USUARIO_REGISTRO
									</th>
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
									<td
										align="center"
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
									>
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
									<td
										align="right"
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
									>
										S/ {{ roundTo(item.cuota, 2) }}
									</td>
									<td
										align="center"
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
									>
										{{ item.dias_atraso + " d" }}
									</td>
									<td align="center">
										{{ item.usuario_asesor }}
									</td>
									<td
										align="center"
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
									>
										{{ item.estado }}
									</td>
									<td
										align="center"
										v-show="
											nombre_modulo == 'cobranza' ||
											nombre_modulo == 'copia_cronograma'
										"
									>
										{{ item.tipo }}
									</td>
									<td
										align="center"
										v-show="
											nombre_modulo == 'propuesta' ||
											nombre_modulo == 'copia_propuesta' ||
											nombre_modulo == 'aprobacion' ||
											nombre_modulo == 'copia_aprobacion'
										"
									>
										{{ item.usuario_registro }}
									</td>
								</tr>
							</tbody>
						</table>
						<div class="text-right">
							<button
								class="btn btn-danger btn-icon-split"
								v-if="nombre_modulo == 'anular_aprobacion'"
								@click="anular_aprobacion"
								:disabled="lista_creditos.length == 0"
							>
								<span class="icon text-white">
									<i class="fa fa-trash"></i
								></span>
								<span class="text">ANULAR</span>
							</button>
						</div>
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
			windowWidth: window.innerWidth,

			texto_buscar: null,

			agencias_permitidas: [],
			agencia_seleccionada: null,
			tipo_filtro: "apellidos_nombres",
			lista_creditos: [],
			ruta: "cre.index",
			ruta_busqueda: null,
			nombre_modulo: null,

			cancelados_parcial: false,
		};
	},
	watch: {
		texto_buscar(value) {
			if (
				this.nombre_modulo == "cobranza" ||
				this.nombre_modulo == "copia_cronograma"
			) {
				this.BuscarCreditos();
			}
		},

		lista_creditos() {
			$("#tblCreditosResultado").DataTable().destroy();
			this.TablaListaCreditos();
		},
		nombre_modulo(value) {
			if (value == "copia_propuesta") {
				this.ruta = "cre.propuesta";
				this.ruta_busqueda = "cre.propuesta.listar";
			} else if (value == "aprobacion") {
				this.ruta = "cre.aprobacion";
				this.ruta_busqueda = "cre.propuesta.listar";
			} else if (value == "copia_aprobacion") {
				this.ruta = "cre.aprobacion";
				this.ruta_busqueda = "cre.aprobacion.listar";
			} else if (value == "desembolso") {
				this.ruta = "caj.desembolso";
				this.ruta_busqueda = "cre.aprobacion.listar";
			} else if (value == "cobranza") {
				this.ruta = "caj.cobranza";
				this.ruta_busqueda = "caj.desembolso.listar";
			} else if (value == "copia_cronograma") {
				this.ruta = "cre.copia_cronograma";
				this.ruta_busqueda = "caj.desembolso.listar";
			} else if (value == "anular_aprobacion") {
				this.ruta = "cre.anular_aprobacion";
				this.ruta_busqueda = "cre.aprobacion.listar";
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
			if (this.nombre_modulo != "cobranza") {
				this.ListarCreditos();
			}
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		this.TablaListaCreditos();
	},
	computed: {
		titulo_modal() {
			if (
				this.nombre_modulo == "copia_propuesta" ||
				this.nombre_modulo == "aprobacion"
			) {
				return "PROPUESTOS";
			} else if (
				this.nombre_modulo == "copia_aprobacion" ||
				this.nombre_modulo == "anular_aprobacion" ||
				this.nombre_modulo == "desembolso"
			) {
				return "APROBADOS";
			} else if (
				this.nombre_modulo == "copia_desembolso" ||
				this.nombre_modulo == "cobranza" ||
				this.nombre_modulo == "copia_cronograma"
			) {
				return "DESEMBOLSADOS";
			}
		},
		titulo_header() {
			if (
				this.nombre_modulo == "copia_propuesta" ||
				this.nombre_modulo == "aprobacion"
			) {
				return "PROPUESTA";
			} else if (
				this.nombre_modulo == "copia_aprobacion" ||
				this.nombre_modulo == "desembolso" ||
				this.nombre_modulo == "anular_aprobacion"
			) {
				return "APROBACIÓN";
			} else if (
				this.nombre_modulo == "copia_desembolso" ||
				this.nombre_modulo == "cobranza" ||
				this.nombre_modulo == "copia_cronograma"
			) {
				return "DESEMBOLSO";
			}
		},
	},
	methods: {
		anular_aprobacion() {
			let row = document
				.getElementById("tblCreditosResultado")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Seleccione una aprobación.",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let aprobacion_id = row.id;
				Swal.fire({
					title: "INGRESE COMENTARIO DE ANULACIÓN",
					text: "",
					input: "text",
					inputAttributes: {
						autocapitalize: "off",
					},
					customClass: {
						input: "mayus",
					},
					confirmButtonText: "Aceptar",
					showCancelButton: true,
					cancelButtonText: "Cancelar",
					allowOutsideClick: false,
					preConfirm: (result) => {
						result = result.toUpperCase();

						if (result.length < 15) {
							Swal.showValidationMessage(
								"El comentario debe tener al menos 15 caracteres."
							);
							return false;
						}

						this.$inertia.post(
							route(this.ruta),
							{
								agencia_id: this.agencia_seleccionada,
								aprobacion_id: aprobacion_id,
								comentario_anulacion: result,
							},
							{
								preserveScroll: true,
								onStart: (visit) => {
									let timerInterval;
									Swal.fire({
										title: "EN PROGRESO",
										html: "Espere porfavor...",
										timer: 5000,
										allowOutsideClick: false,
										timerProgressBar: true,
										didOpen: () => {
											Swal.showLoading();
											timerInterval = setInterval(() => {
												const content = Swal.getContent();
												if (content) {
													const b = content.querySelector("b");
													if (b) {
														b.textContent = Swal.getTimerLeft();
													}
												}
											}, 100);
										},
										willClose: () => {
											clearInterval(timerInterval);
										},
									});
								},

								onSuccess: () => {
									Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										text: "Aprobacion ANULADA con éxito.",
										allowOutsideClick: false,
										preConfirm: (result) => {
											this.ListarCreditos();
										},
									});
								},
							}
						);
					},
				});
			}
		},

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
		hidenav() {
			return this.$parent.hide_nav();
		},
		shownav() {
			return this.$parent.show_nav();
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
				var table = $("#tblCreditosResultado").DataTable({
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
			if (
				this.nombre_modulo == "copia_cronograma" ||
				this.nombre_modulo == "cobranza"
			) {
				this.BuscarCreditos();
			} else {
				if (this.nombre_modulo != "cobranza") {
					let self = this;
					let data = new FormData();
					data.append("agencia_id", self.agencia_seleccionada);

					axios.post(route(self.ruta_busqueda), data).then(function (response) {
						self.lista_creditos = response.data;
					});
				} else {
					this.BuscarCreditos();
				}
			}
		},
		BuscarCreditos() {
			let self = this;

			if (this.agencia_seleccionada == null) {
				return false;
			}

			if (this.texto_buscar && this.texto_buscar.length >= 3) {
				let data = new FormData();
				data.append("texto_buscar", this.texto_buscar);
				data.append("tipo_filtro", this.tipo_filtro);
				data.append("cancelados_parcial", this.cancelados_parcial);
				data.append("agencia_id", this.agencia_seleccionada);

				axios
					.post(route("caj.desembolso.buscar"), data)
					.then(function (response) {
						self.lista_creditos = response.data;
					});
			} else {
				this.lista_creditos = [];
			}
		},
		Redirigir(credito) {
			let object = {};
			if (this.nombre_modulo == "copia_propuesta") {
				object = {
					cliente_id: credito.cliente_id,
					propuesta_id: credito.propuesta_id,
					agencia_id: this.agencia_seleccionada,
				};
			} else if (
				this.nombre_modulo == "copia_aprobacion" ||
				this.nombre_modulo == "aprobacion"
			) {
				object = {
					propuesta_id: credito.propuesta_id,
					aprobacion_id: credito.aprobacion_id,
					agencia_id: this.agencia_seleccionada,
				};
			} else if (this.nombre_modulo == "desembolso") {
				object = {
					aprobacion_id: credito.aprobacion_id,
					agencia_id: this.agencia_seleccionada,
				};
			} else if (this.nombre_modulo == "cobranza") {
				object = {
					credito_id: credito.id,
					agencia_id: this.agencia_seleccionada,
				};
			} else if (this.nombre_modulo == "copia_cronograma") {
				object = {
					cliente_id: credito.cliente_id,
					credito_id: credito.id,
					agencia_id: this.agencia_seleccionada,
				};
			} else if (this.nombre_modulo == "anular_aprobacion") {
				return false;
			}
			this.$inertia.get(route(this.ruta, object));
		},
		CerrarModal(modal) {
			$("#" + modal).css("display", "none");
		},
	},
};
</script>

<style lang="css">
.mdlBusquedaCreditos {
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

#tblCreditosResultado_length {
	display: none;
}
@media (max-width: 900px) {
	.mdlBusquedaCreditos {
		margin-top: 15% !important;
	}
}
</style>
