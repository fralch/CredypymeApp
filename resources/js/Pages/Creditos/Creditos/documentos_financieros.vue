<template>
	<layout ref="layout">
		<div class="slot_body slot_historial_crediticio" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'DOCUMENTOS FINANCIEROS'"></headerClose>

					<div class="card-title">CRÉDITOS DESEMBOLSADOS</div>
					<div class="card-body card-block">
						<div class="form-row row justify-content-md-center">
							<div class="form-group col-md-4 col-12">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Ag. </span>
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
						</div>
						<div class="row">
							<div class="col-md-3">
								<fieldset class="col-md-12 p-2" style="background: white">
									<legend>
										<label
											class="label-title p-1"
											style="
												background: var(--colorAlto);
												color: white !important;
												font-size: 12px;
												border-radius: 3px;
											"
											>Opciones
										</label>
									</legend>
									<div class="col-md-12 text-left">
										<div class="form-check">
											<input
												class="form-check-input"
												type="checkbox"
												value="codigo"
												id="chbCodigoSeguimiento"
												v-model="opciones"
												checked
											/>
											<label class="label-title" for="chbCodigoSeguimiento">
												Código seguimiento
											</label>
										</div>
										<div class="form-check">
											<input
												class="form-check-input"
												type="checkbox"
												value="fecha"
												id="chbFechaElaboracion"
												v-model="opciones"
												checked
											/>
											<label class="label-title" for="chbFechaElaboracion">
												Fecha de elaboración
											</label>
										</div>
										<div class="form-check">
											<input
												class="form-check-input"
												type="checkbox"
												value="datos"
												id="chbDatosHojaResumen"
												v-model="opciones"
												checked
											/>
											<label class="label-title" for="chbDatosHojaResumen">
												Datos (sólo Hoja Resumen)
											</label>
										</div>
									</div>
								</fieldset>
								<fieldset class="col-md-12 p-2" style="background: white">
									<legend>
										<label
											class="label-title p-1"
											style="
												background: var(--colorMedio);
												color: white !important;
												font-size: 12px;
												border-radius: 3px;
											"
											>Jefe de crédito
										</label>
									</legend>
									<div class="form-row col-md-12">
										<div class="form-group col-md-12 col-4">
											<button
												class="btn btn-action btn-icon-split mt-1"
												title="Imprimir HOJA RESÚMEN"
												style="width: 100% !important"
												@click="Imprimir('hoja_resumen')"
											>
												<span class="text">HOJA RESÚMEN</span>
											</button>
										</div>
										<div class="form-group col-md-12 col-4">
											<button
												class="btn btn-action btn-icon-split mt-1"
												title="Imprimir PAGARÉ FIANZA"
												style="width: 100% !important"
												@click="Imprimir('pagare_fianza')"
											>
												<span class="text">PAGARÉ / FIANZA</span>
											</button>
										</div>
										<div class="form-group col-md-12 col-4">
											<button
												class="btn btn-action btn-icon-split mt-1"
												title="Imprimir RIESGO DE CRÉDITO"
												style="width: 100% !important"
												@click="Imprimir('riesgo_credito')"
											>
												<span class="text">RIESGO CRÉDITO</span>
											</button>
										</div>
									</div>
								</fieldset>
								<fieldset class="col-md-12 p-2" style="background: white">
									<legend>
										<label
											class="label-title p-1"
											style="
												background: var(--colorMedio);
												color: white !important;
												font-size: 12px;
												border-radius: 3px;
											"
											>Jefe de operaciones
										</label>
									</legend>
									<div class="col-md-12 text-center">
										<button
											class="btn btn-action btn-icon-split mt-1"
											title="Imprimir CONTRATO"
											style="width: 100% !important"
											@click="Imprimir('contrato')"
										>
											<span class="text">CONTRATO</span>
										</button>
									</div>
								</fieldset>
							</div>
							<div class="col-md-9">
								<table class="table" id="tblAprobados" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 200px !important">
												APELLIDOS_NOMBRES
											</th>
											<th style="min-width: 100px !important">
												FECHA_APROBACIÓN
											</th>
											<th style="min-width: 80px !important">MONTO</th>
											<th style="min-width: 100px !important">PERIODO_PAGO</th>
											<th style="min-width: 100px !important">ASESOR</th>
											<th style="min-width: 100px !important">
												USUARIO_REGISTRO
											</th>
											<th style="min-width: 100px !important">AGENCIA</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_creditos"
											:key="index"
											:id="'doc_' + item.id"
										>
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
											<td align="center">
												{{ item.usuario_asesor }}
											</td>
											<td align="center">
												{{ item.usuario_registro }}
											</td>
											<td align="center">
												{{ item.agencia }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

export default {
	props: {},
	components: {
		layout,
		headerClose,
	},

	data() {
		return {
			texto_buscar: null,
			agencias_permitidas: [],
			agencia_seleccionada: null,
			tipo_filtro: "apellidos_nombres",
			lista_creditos: [],
			fechas_vencimiento: [],
			aprobacion_seleccionada: null,
			opciones: ["codigo", "fecha", "datos"],
		};
	},
	computed: {
		nueva_empresa() {
			let agencia = this.$inertia.page.props.application.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			);
			return agencia[0].nueva_empresa;
		},
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
			this.ListarCreditos();
		},
		lista_creditos() {
			$("#tblAprobados").DataTable().destroy();
			this.TablaAprobados();
		},
	},
	mounted() {
		this.TablaAprobados();
		this.listar_agencias();
	},

	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CREDITO/DOCUMENTOS_FINANCIEROS"
			);
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
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
		TablaAprobados() {
			this.$nextTick(() => {
				var table = $("#tblAprobados").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
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
			});
		},

		ListarCreditos() {
			let self = this;
			let data = new FormData();
			data.append("agencia_id", self.agencia_seleccionada);

			axios
				.post(route("cre.aprobacion.listar_detallado"), data)
				.then(function (response) {
					self.lista_creditos = response.data.aprobaciones;
					self.fechas_vencimiento = response.data.fechas_vencimiento;
				});
		},

		async Imprimir(tipo) {
			let row = document
				.getElementById("tblAprobados")
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
				let id = row.id.replace("doc_", "");
				this.aprobacion_seleccionada = this.lista_creditos.filter(
					(item) => item.id == id
				)[0];
			}

			let codigo = null;
			let fecha_actual = null;

			if (this.opciones.includes("codigo")) {
				if (this.nueva_empresa) {
					codigo = this.aprobacion_seleccionada.codigo_seguimiento_2;
				} else {
					codigo = this.aprobacion_seleccionada.codigo_seguimiento;
				}
			}
			if (this.opciones.includes("fecha")) {
				let fecha_hora_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_seleccionada
				);

				let solo_fecha = fecha_hora_actual.substring(0, 10);

				let parts = solo_fecha.split("-");

				let options = {
					year: "numeric",
					month: "long",
					day: "numeric",
				};

				let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

				fecha_actual =
					this.$page.props.application.agencias.filter(
						(item) => item.id == this.$page.props.user_session.id_agencia
					)[0].distrito +
					", " +
					date.toLocaleDateString("es-ES", options);
			}

			let datos_documento = null;

			switch (tipo) {
				case "hoja_resumen":
					datos_documento = {
						codigo_seguimiento: codigo,
						monto: !this.opciones.includes("datos")
							? null
							: this.roundTo(this.aprobacion_seleccionada.monto, 2) + " SOLES",
						tasa_interes: !this.opciones.includes("datos")
							? null
							: this.roundTo(this.aprobacion_seleccionada.tasa_interes, 2) +
							  " %",
						tasa_interes_moratoria: !this.opciones.includes("datos")
							? null
							: "0.00 %",
						monto_interes: !this.opciones.includes("datos")
							? null
							: this.roundTo(
									this.aprobacion_seleccionada.cuota *
										this.aprobacion_seleccionada.plazo -
										this.aprobacion_seleccionada.monto,
									2
							  ) + " SOLES",
						producto: !this.opciones.includes("datos")
							? null
							: this.aprobacion_seleccionada.producto,
						tipo: !this.opciones.includes("datos")
							? null
							: this.aprobacion_seleccionada.tipo,
						frecuencia_pago: !this.opciones.includes("datos")
							? null
							: "CADA 1 " +
							  this.periodo_medicion(
									this.aprobacion_seleccionada.periodo_pago
							  ) +
							  " (PERIODO FIJO)",
						numero_cuotas: !this.opciones.includes("datos")
							? null
							: this.roundTo(this.aprobacion_seleccionada.plazo, 0) +
							  " CUOTA(S)",
						fecha_vencimiento: !this.opciones.includes("datos")
							? null
							: this.fechas_vencimiento.filter(
									(item) =>
										item.aprobacion_id ==
										this.aprobacion_seleccionada.aprobacion_id
							  )[0].fecha_vencimiento,
						fecha_lugar_elaboracion: fecha_actual,
					};

					break;
				case "pagare_fianza":
					datos_documento = {
						codigo_seguimiento: codigo,
						fecha_lugar_elaboracion: fecha_actual,
						titular_nombres:
							this.aprobacion_seleccionada.apellido_paterno +
							" " +
							this.aprobacion_seleccionada.apellido_materno +
							" " +
							this.aprobacion_seleccionada.nombres,
						titular_dni: this.aprobacion_seleccionada.dni,
						titular_direccion: this.aprobacion_seleccionada.direccion,

						pariente_nombres:
							this.aprobacion_seleccionada.dni_pariente == null
								? null
								: this.aprobacion_seleccionada.apellido_paterno_pariente +
								  " " +
								  this.aprobacion_seleccionada.apellido_materno_pariente +
								  " " +
								  this.aprobacion_seleccionada.nombres_pariente,
						pariente_dni:
							this.aprobacion_seleccionada.dni_pariente == null
								? null
								: this.aprobacion_seleccionada.dni_pariente,
						pariente_direccion:
							this.aprobacion_seleccionada.dni_pariente == null
								? null
								: this.aprobacion_seleccionada.direccion_pariente,

						aval_nombres:
							this.aprobacion_seleccionada.dni_aval == null
								? null
								: this.aprobacion_seleccionada.apellido_paterno_aval +
								  " " +
								  this.aprobacion_seleccionada.apellido_materno_aval +
								  " " +
								  this.aprobacion_seleccionada.nombres_aval,
						aval_dni:
							this.aprobacion_seleccionada.dni_aval == null
								? null
								: this.aprobacion_seleccionada.dni_aval,
						aval_direccion:
							this.aprobacion_seleccionada.dni_aval == null
								? null
								: this.aprobacion_seleccionada.direccion_aval,

						pariente_aval_nombres:
							this.aprobacion_seleccionada.dni_pariente_aval == null
								? null
								: this.aprobacion_seleccionada.apellido_paterno_pariente_aval +
								  " " +
								  this.aprobacion_seleccionada.apellido_materno_pariente_aval +
								  " " +
								  this.aprobacion_seleccionada.nombres_pariente_aval,
						pariente_aval_dni:
							this.aprobacion_seleccionada.dni_pariente_aval == null
								? null
								: this.aprobacion_seleccionada.dni_pariente_aval,
						pariente_aval_direccion:
							this.aprobacion_seleccionada.dni_pariente_aval == null
								? null
								: this.aprobacion_seleccionada.direccion_pariente_aval,
					};

					break;

				case "riesgo_credito":
					datos_documento = {
						fecha_lugar_elaboracion: fecha_actual,
						titular_nombres:
							this.aprobacion_seleccionada.apellido_paterno +
							" " +
							this.aprobacion_seleccionada.apellido_materno +
							" " +
							this.aprobacion_seleccionada.nombres,
						titular_dni: this.aprobacion_seleccionada.dni,
						titular_direccion: this.aprobacion_seleccionada.direccion,
						titular_distrito: this.aprobacion_seleccionada.distrito,
						titular_provincia: this.aprobacion_seleccionada.provincia,
						titular_departamento: this.aprobacion_seleccionada.departamento,

						// pariente:
						// 	this.aprobacion_seleccionada.dni_pariente == null
						// 		? null
						// 		: this.aprobacion_seleccionada.apellido_paterno_pariente +
						// 		  " " +
						// 		  this.aprobacion_seleccionada.apellido_materno_pariente +
						// 		  " " +
						// 		  this.aprobacion_seleccionada.nombres_pariente,
					};
					break;
				case "contrato":
					datos_documento = {
						codigo_seguimiento: codigo,
						agencia_id: this.agencia_seleccionada,
						fecha_lugar_elaboracion: fecha_actual,
						titular_nombres:
							this.aprobacion_seleccionada.apellido_paterno +
							" " +
							this.aprobacion_seleccionada.apellido_materno +
							" " +
							this.aprobacion_seleccionada.nombres,
						titular_dni: this.aprobacion_seleccionada.dni,
						titular_direccion: this.aprobacion_seleccionada.direccion,

						pariente_nombres:
							this.aprobacion_seleccionada.dni_pariente == null
								? null
								: this.aprobacion_seleccionada.apellido_paterno_pariente +
								  " " +
								  this.aprobacion_seleccionada.apellido_materno_pariente +
								  " " +
								  this.aprobacion_seleccionada.nombres_pariente,
						pariente_dni:
							this.aprobacion_seleccionada.dni_pariente == null
								? null
								: this.aprobacion_seleccionada.dni_pariente,
						pariente_direccion:
							this.aprobacion_seleccionada.dni_pariente == null
								? null
								: this.aprobacion_seleccionada.direccion_pariente,

						aval_nombres:
							this.aprobacion_seleccionada.dni_aval == null
								? null
								: this.aprobacion_seleccionada.apellido_paterno_aval +
								  " " +
								  this.aprobacion_seleccionada.apellido_materno_aval +
								  " " +
								  this.aprobacion_seleccionada.nombres_aval,
						aval_dni:
							this.aprobacion_seleccionada.dni_aval == null
								? null
								: this.aprobacion_seleccionada.dni_aval,
						aval_direccion:
							this.aprobacion_seleccionada.dni_aval == null
								? null
								: this.aprobacion_seleccionada.direccion_aval,

						pariente_aval_nombres:
							this.aprobacion_seleccionada.dni_pariente_aval == null
								? null
								: this.aprobacion_seleccionada.apellido_paterno_pariente_aval +
								  " " +
								  this.aprobacion_seleccionada.apellido_materno_pariente_aval +
								  " " +
								  this.aprobacion_seleccionada.nombres_pariente_aval,
						pariente_aval_dni:
							this.aprobacion_seleccionada.dni_pariente_aval == null
								? null
								: this.aprobacion_seleccionada.dni_pariente_aval,
						pariente_aval_direccion:
							this.aprobacion_seleccionada.dni_pariente_aval == null
								? null
								: this.aprobacion_seleccionada.direccion_pariente_aval,
					};

					break;
				default:
					break;
			}

			let data = new FormData();
			data.append("tipo", tipo);
			data.append("datos_documento", JSON.stringify(datos_documento));

			Swal.fire({
				title: "GENERANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					// this.$inertia.post(route("cre.documentos_financieros.generar"), data);
					// return false;
					Swal.showLoading();
					axios
						.post(route("cre.documentos_financieros.generar"), data)
						.then(function (response) {
							let origin = window.location.origin;
							let path_pdf = response.data.path_pdf;

							// Crear un IFrame
							let iframe = document.createElement("iframe");
							// Oculto el iframe
							iframe.style.display = "none";
							// Defino el source
							iframe.src = origin + path_pdf;
							// Añadir el Iframe a la vista
							document.body.appendChild(iframe);

							iframe.contentWindow.focus(); // Enfoca
							iframe.contentWindow.print(); // Imprime

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
.slot_historial_crediticio {
	width: 66% !important;
	margin-left: 17% !important;
}

@media only screen and (max-width: 900px) {
	.slot_historial_crediticio {
		width: 99% !important;
		margin-left: 0.5% !important;
		margin-top: 15% !important;
	}
}
</style>

