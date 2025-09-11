<template>
	<layout ref="layout">
		<div class="slot_body slot-cronograma" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CRONOGRAMA'"></headerClose>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-9 mb-1 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>TITULAR</span
									>
								</div>
								<input
									type="text"
									class="form-control input-information input-highlight"
									:value="nombre_completo_titular"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-3 mb-1 mt-1 col-5">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>ATRASO</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information input-highlight"
									:value="datos_credito_redondeados.dias_atraso + ' días'"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-3 mb-1 mt-1 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">CAPITAL</span>
									<span class="input-group-text prepend-title">S/</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="roundTo(datos_credito_redondeados.monto, 2)"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-5 mb-1 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title"
										>FECHA DESEMB.</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									onkeydown="return false"
									spellcheck="false"
									:value="datos_credito_redondeados.fecha_desembolso"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-4 mb-1 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">PLAZO</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="
										datos_credito_redondeados.plazo +
										' - ' +
										datos_credito.tasa_interes +
										'%'
									"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-3 mb-1 mt-1 col-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>CUOTA</span
									>
									<span class="input-group-text prepend-title span-highlight"
										>S/</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information input-highlight"
									:value="roundTo(datos_credito_redondeados.cuota, 2)"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-5 mb-1 mt-1 col-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>ASESOR</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information input-highlight"
									:value="datos_credito_redondeados.analista"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-4 mb-1 mt-1 col-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>EXP.</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information input-highlight"
									:value="datos_credito_redondeados.codigo_expediente"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-6 mb-1 mt-1 col-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">SECTOR</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="datos_credito_redondeados.sector"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-6 mb-1 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">PRODUCTO</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="datos_credito_redondeados.producto"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-4 mb-1 mt-1 col-5">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">TIPO</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="datos_credito_redondeados.tipo"
									onkeydown="return false"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
							<div class="input-group col-md-4 mb-1 mt-1 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title"
										>COBRANZA EN</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									onkeydown="return false"
									spellcheck="false"
									:value="
										datos_credito_redondeados.pago_oficina == 1
											? 'OFICINA'
											: 'NEGOCIO'
									"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>
							<div class="input-group col-md-4 mb-1 mt-1 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title"
										>MODO DESEMBOLSO</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									onkeydown="return false"
									spellcheck="false"
									:value="datos_desembolso.modo_desembolso"
									@focus="hidenav()"
									@blur="shownav()"
								/>
								<div class="input-group-append" v-if="permiso_cambiar_modo">
									<button
										class="btn btn-action p-0 pr-2 pl-2"
										@click="CambiarModo()"
									>
										<span class="icon text-white">
											<i class="fa fa-edit"></i
										></span>
									</button>
								</div>
							</div>
						</div>
						<div id="tabla_cronograma">
							<table
								class="table"
								id="tblCronograma"
								style="width: 100% !important"
							>
								<thead>
									<tr>
										<th style="min-width: 90px !important">FECHA</th>
										<th>N°_CUOTA</th>
										<th>CUOTA</th>
										<th>CAPITAL</th>
										<th>INTERÉS</th>
										<th>SALDO_PAGAR</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(item, index) in cuotas_credito_rendondeados"
										:key="index"
										:class="[index % 2 == 0 ? 'verde-claro' : '']"
									>
										<td align="center">{{ item.fecha_pago }}</td>
										<td align="center">{{ item.orden }}</td>
										<td align="center">{{ item.monto_cuota }}</td>
										<td align="center">{{ item.monto_capital }}</td>
										<td align="center">{{ item.monto_interes_redondeado }}</td>
										<td align="center">{{ item.saldo_pagar }}</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr />
						<div class="text-center">
							<div class="btn-group" role="group">
								<button
									class="btn btn-cancel btn-icon-split"
									title="Imprimir CRONOGRAMA"
									@click="Imprimir('CRONOGRAMA')"
								>
									<span class="icon text-white">
										<i class="fa fa-print"></i
									></span>
									<span class="text">CRONOGRAMA</span>
								</button>
								<button
									class="btn btn-action btn-icon-split"
									title="Imprimir V. DESEMBOLSO"
									@click="Imprimir('DESEMBOLSO')"
								>
									<span class="icon text-white">
										<i class="fa fa-print"></i
									></span>
									<span class="text">V. DESEMBOLSO</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split"
									title="Imprimir C. DESEMBOLSO"
									@click="Imprimir('C_DESEMBOLSO')"
									v-if="comision_desembolso.length != 0"
								>
									<span class="icon text-white">
										<i class="fa fa-print"></i
									></span>
									<span class="text">C. DESEMBOLSO</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split"
									title="Imprimir C. RIESGO"
									@click="Imprimir('C_RIESGO')"
									v-if="comision_riesgo.length != 0"
								>
									<span class="icon text-white">
										<i class="fa fa-print"></i
									></span>
									<span class="text">C. RIESGO</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split"
									title="Imprimir C. DOMICILIO"
									@click="Imprimir('C_DOMICILIO')"
									v-if="comision_domicilio.length != 0"
								>
									<span class="icon text-white">
										<i class="fa fa-print"></i
									></span>
									<span class="text">C. DOMICILIO</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- The Modal -->
			<div id="mdlCambiarModo" class="modal modal-right">
				<!-- Modal content -->
				<div class="modal-content w-20 mdlCambiarModo">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								titulo_modal="CAMBIAR MODO"
								:nombre_modal="'mdlCambiarModo'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<div class="form-row">
									<div class="input-group col-md-12">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>CAMBIAR A</span
											>
										</div>

										<input
											type="text"
											class="form-control center input-information"
											onkeydown="return false"
											spellcheck="false"
											:value="frmCambioModo.modo_desembolso"
											disabled
										/>
									</div>
									<div
										class="col-md-8 offset-md-2"
										v-if="frmCambioModo.modo_desembolso == 'DOMICILIO'"
									>
										<div class="form-check">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbComisionDomicilio"
												v-model="frmCambioModo.comision_domicilio"
											/>
											<label for="chbComisionDomicilio"
												><span class="badge badge-primary"
													>APLICAR COMISIÓN</span
												></label
											>
										</div>
									</div>
									<div
										class="col-md-8 offset-md-2"
										v-if="
											frmCambioModo.modo_desembolso == 'DOMICILIO' &&
											frmCambioModo.comision_domicilio
										"
									>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text bolder">S/</span>
											</div>

											<input
												type="number"
												class="form-control center"
												min="0"
												step="0.01"
												lang="en"
												style="
													height: 30px;
													font-size: 15px;
													font-weight: bolder;
													color: var(--colorAlto);
												"
												v-model="frmCambioModo.monto_comision"
												@change="Redondear"
												name="monto"
											/>
										</div>
									</div>
								</div>
								<hr />
								<div class="text-center">
									<button
										class="btn btn-action btn-icon-split"
										title="Aplicar Cambio"
										@click="AplicarCambio"
									>
										<span class="icon text-white">
											<i class="fa fa-check"></i
										></span>
										<span class="text">APLICAR</span>
									</button>
								</div>
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: { layout, headerClose, headerCloseModal },
	props: {
		agencia_id: Number,
		datos_titular: Object,
		cuotas_credito: Array,
		datos_credito: Object,
		comision_desembolso: Array,
		comision_riesgo: Array,
		comision_domicilio: Array,
		analista: String,
		datos_pariente: Object,
		datos_aval: Object,
		datos_pariente_aval: Object,
		datos_desembolso: Object,
	},
	data() {
		return {
			cronograma: [],
			ruta: null,
			concepto: null,

			frmCambioModo: {
				modo_desembolso: "OFICINA",
				comision_domicilio: false,
				monto_comision: 0,
			},
		};
	},

	computed: {
		nombre_completo_titular() {
			let nombre_completo_titular =
				this.datos_titular.apellido_paterno +
				" " +
				this.datos_titular.apellido_materno +
				" " +
				this.datos_titular.nombres;
			return nombre_completo_titular;
		},
		nueva_empresa() {
			let agencia = this.$inertia.page.props.application.agencias.filter(
				(item) => item.id == this.agencia_id
			);

			return agencia[0].nueva_empresa;
		},
		nombre_completo_pariente() {
			if (this.datos_pariente != null) {
				let nombre_completo_pariente =
					this.datos_pariente.apellido_paterno +
					" " +
					this.datos_pariente.apellido_materno +
					" " +
					this.datos_pariente.nombres;
				return nombre_completo_pariente;
			} else {
				return null;
			}
		},

		nombre_completo_aval() {
			if (this.datos_aval != null) {
				let nombre_completo_aval =
					this.datos_aval.apellido_paterno +
					" " +
					this.datos_aval.apellido_materno +
					" " +
					this.datos_aval.nombres;
				return nombre_completo_aval;
			} else {
				return null;
			}
		},

		nombre_completo_pariente_aval() {
			if (this.datos_pariente_aval != null) {
				let nombre_completo_pariente_aval =
					this.datos_pariente_aval.apellido_paterno +
					" " +
					this.datos_pariente_aval.apellido_materno +
					" " +
					this.datos_pariente_aval.nombres;
				return nombre_completo_pariente_aval;
			} else {
				return null;
			}
		},

		datos_credito_redondeados() {
			let plazo_con_periodo = null;
			let plazo_redondeado = Math.round(this.datos_credito.plazo);

			switch (this.datos_credito.periodo_pago) {
				case "DIARIO":
					plazo_con_periodo = plazo_redondeado + " día(s)";
					break;
				case "SEMANAL":
					plazo_con_periodo = plazo_redondeado + " semana(s)";
					break;
				case "QUINCENAL":
					plazo_con_periodo = plazo_redondeado + " quincena(s)";
					break;
				case "MENSUAL":
					plazo_con_periodo = plazo_redondeado + " mes(s)";
					break;
			}

			let lista = {
				monto: this.roundTo(this.datos_credito.monto, 2),
				plazo: plazo_con_periodo,
				tasa_interes: this.roundTo(this.datos_credito.tasa_interes, 2),
				tipo: this.datos_credito.tipo,
				cuota: this.roundTo(this.datos_credito.cuota, 2),
				analista: this.analista,
				codigo_seguimiento: this.nueva_empresa
					? this.datos_credito.codigo_seguimiento_2
					: this.datos_credito.codigo_seguimiento,
				fecha_desembolso: this.datos_credito.fecha_desembolso,
				sector: this.datos_credito.sector,
				producto: this.datos_credito.producto,
				dias_atraso: this.datos_credito.dias_atraso,
				pago_oficina: this.datos_credito.pago_oficina,
				codigo_expediente: this.datos_titular.codigo_expediente,
			};

			return lista;
		},
		cuotas_credito_rendondeados() {
			const dias = ["LUN", "MAR", "MIÉ", "JUE", "VIE", "SÁB", "DOM"];

			let cuotas_credito_rendondeados = [];
			let saldo = this.datos_credito.monto;

			this.cuotas_credito.forEach((element) => {
				let fecha_con_dia = new Date(element.fecha_vencimiento).getDay();
				let object = {
					fecha_pago:
						moment(element.fecha_vencimiento).format("DD/MM/YYYY") +
						" " +
						dias[fecha_con_dia],
					orden: element.numero_cuota,
					monto_cuota: this.roundTo(element.cuota, 2),
					monto_capital: this.roundTo(element.capital, 2),
					monto_interes: parseFloat(element.interes),
					monto_redondeo: parseFloat(element.redondeo),
					monto_interes_redondeado: this.roundTo(
						parseFloat(element.interes) + parseFloat(element.redondeo),
						2
					),
					saldo_pagar: this.roundTo(saldo - parseFloat(element.capital), 2),
				};
				saldo = object.saldo_pagar;
				cuotas_credito_rendondeados.push(object);
			});
			this.cronograma = cuotas_credito_rendondeados;
			return cuotas_credito_rendondeados;
		},

		permiso_cambiar_modo() {
			let permiso =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CREDITO/CAMBIAR_MODO_DESEMBOLSO"
				);

			if (permiso.length > 0) {
				let agencia = JSON.parse(permiso[0].acceso_agencias).filter(
					(item) => item.agencia_id == this.agencia_id
				);

				if (agencia.length > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		},
	},

	mounted() {
		this.TablaCronograma();
	},
	methods: {
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		TablaCronograma() {
			$("#tblCronograma").DataTable({
				paging: false,
				scrollY: "30vh",
				scrollX: true,
				paging: false,
				fixedColumns: {
					leftColumns: 0,
				},
				responsive: true,
				fixedHeader: true,
				ordering: false,
				scrollCollapse: true,
				info: false,
			});
		},

		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}
			return parseFloat(valor).toFixed(numero_decimales);
		},
		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0.1) {
				valor = e.target.value;
			}

			this.frmCambioModo.monto_comision = this.roundTo(valor, numero_decimales);
		},
		async CambiarModo() {
			const params = {
				agencia_caja: this.datos_desembolso.agencia_caja,
				caja_id: this.datos_desembolso.caja_id,
			};

			//this.$inertia.get(route("cre.copia_cronograma.verificar_caja"), params);
			// return false

			await axios
				.get(route("cre.copia_cronograma.verificar_caja"), { params })
				.then((response) => {
					const estado = response.data.estado;
					if (estado == "CAJA_CERRADA") {
						Swal.fire({
							icon: "warning",
							title: "¡Ups!",
							text: "La CAJA que realizó la operación ya está cerrada.",
						});
						return false;
					} else if (estado == "CAJA_ABIERTA") {
						this.frmCambioModo.modo_desembolso =
							this.datos_desembolso.modo_desembolso == "OFICINA"
								? "DOMICILIO"
								: "OFICINA";

						this.frmCambioModo.comision_domicilio = true;
						this.frmCambioModo.monto_comision = this.roundTo(5, 2);

						return $("#mdlCambiarModo").css("display", "block");
					}
				});
		},

		async AplicarCambio() {
			Swal.fire({
				icon: "question",
				title: "¿Desea continuar con el cambio?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append("agencia_credito", this.agencia_id);
					data.append("agencia_caja", this.datos_desembolso.agencia_caja);
					data.append("caja_id", this.datos_desembolso.caja_id);
					data.append("desembolso_id", this.datos_desembolso.id);
					data.append("frmCambioModo", JSON.stringify(this.frmCambioModo));

					this.$inertia.post(route("cre.copia_cronograma.cambiar_modo"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "CAMBIANDO",
								text: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							$("#mdlCambiarModo").css("display", "none");
							return Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								timer: 2000,
								showConfirmButton: false,
							});
						},
					});
				}
			});
		},
		Imprimir(tipo) {
			let data = new FormData();
			let self = this;

			data.append("agencia", this.$page.props.user_session.nombre_agencia);
			data.append("usuario", this.$page.props.user_session.usuario);
			data.append(
				"dispositivo",
				this.$page.props.user_session.dispositivo.nombre
			);

			if (tipo == "CRONOGRAMA") {
				this.ruta = "caj.desembolso.cronograma";

				let datos_titular = {
					titular: this.nombre_completo_titular,
					negocio:
						this.datos_titular.negocio_nombre +
						" - " +
						this.datos_titular.negocio_actividad,
					asesor: this.analista,
					pariente: this.nombre_completo_pariente,
					aval: this.nombre_completo_aval,
					pariente_aval: this.nombre_completo_pariente_aval,
				};

				let datos_credito = {
					monto: this.datos_credito_redondeados.monto,
					interes_total: this.datos_credito.interes_total,
					plazo: this.datos_credito_redondeados.plazo,
					periodo_pago: this.datos_credito.periodo_pago,
					cuota: this.datos_credito_redondeados.cuota,
					tipo: this.datos_credito_redondeados.tipo,
					tasa_interes: this.datos_credito_redondeados.tasa_interes,
					fecha_desembolso: this.datos_credito_redondeados.fecha_desembolso,
					codigo_seguimiento: this.datos_credito_redondeados.codigo_seguimiento,
				};

				data.append("agencia_id", this.agencia_id);
				data.append("cronograma", JSON.stringify(this.cronograma));
				data.append("datos_titular", JSON.stringify(datos_titular));
				data.append("datos_credito", JSON.stringify(datos_credito));
			}

			if (tipo == "DESEMBOLSO") {
				let datos_desembolso = {
					monto: this.datos_credito.monto,
					fecha_desembolso: this.datos_credito.fecha_desembolso,
				};
				this.ruta = "caj.desembolso.voucher";
				this.concepto = "DESEMBOLSO";

				data.append("titulo", "CONSTANCIA DE DESEMBOLSO");
				data.append("datos_desembolso", JSON.stringify(datos_desembolso));
				data.append("datos_titular", JSON.stringify(this.datos_titular));
				data.append("concepto", this.concepto);
			}
			if (tipo == "C_DESEMBOLSO") {
				this.ruta = "caj.desembolso.voucher";
				this.concepto = "COMISION";
				data.append("titulo", "CONSTANCIA DE COMISIÓN");
				data.append("datos_creacion", this.datos_credito.datos_creacion);
				data.append("concepto", this.concepto);

				let comison_pago = this.comision_desembolso[0];
				let datos_comision = {
					comision_pago_id:
						Array(7 - String(comison_pago.id).length).join("0") +
						comison_pago.id,
					cliente:
						this.datos_titular.apellido_paterno +
						" " +
						this.datos_titular.apellido_materno +
						" " +
						this.datos_titular.nombres,
					tipo: comison_pago.comision,
					monto: this.roundTo(comison_pago.monto, 2),
				};

				data.append("datos_comision", JSON.stringify(datos_comision));
			}
			if (tipo == "C_RIESGO") {
				this.ruta = "caj.desembolso.voucher";
				this.concepto = "COMISION";
				data.append("titulo", "CONSTANCIA DE COMISIÓN");
				data.append("datos_creacion", this.datos_credito.datos_creacion);
				data.append("concepto", this.concepto);

				let comison_pago = this.comision_riesgo[0];
				let datos_comision = {
					comision_pago_id:
						Array(7 - String(comison_pago.id).length).join("0") +
						comison_pago.id,
					cliente:
						this.datos_titular.apellido_paterno +
						" " +
						this.datos_titular.apellido_materno +
						" " +
						this.datos_titular.nombres,
					tipo: comison_pago.comision,
					monto: this.roundTo(comison_pago.monto, 2),
				};

				data.append("datos_comision", JSON.stringify(datos_comision));
			}
			if (tipo == "C_DOMICILIO") {
				this.ruta = "caj.desembolso.voucher";
				this.concepto = "COMISION";
				data.append("titulo", "CONSTANCIA DE COMISIÓN");
				data.append("datos_creacion", this.datos_credito.datos_creacion);
				data.append("concepto", this.concepto);

				let comison_pago = this.comision_domicilio[0];
				let datos_comision = {
					comision_pago_id:
						Array(7 - String(comison_pago.id).length).join("0") +
						comison_pago.id,
					cliente:
						this.datos_titular.apellido_paterno +
						" " +
						this.datos_titular.apellido_materno +
						" " +
						this.datos_titular.nombres,
					tipo: comison_pago.comision,
					monto: this.roundTo(comison_pago.monto, 2),
				};

				data.append("datos_comision", JSON.stringify(datos_comision));
			}

			Swal.fire({
				title: "CARGANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(route(this.ruta), data);
					// return false

					axios.post(route(this.ruta), data).then(function (response) {
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
.slot-cronograma {
	width: 60% !important;
	margin-left: 20% !important;
}

.mdlCambiarModo {
	margin-top: 2%;
}

.titulo_pagar {
	background-color: var(--plomoOscuroEmpresarial);
	color: white;
	width: 86%;
	margin-left: 8%;
	margin-bottom: -3px;
	text-align: center;
	border-radius: 5px;

	font-weight: bolder;
}

.texto_pagar {
	font-size: 1.2rem;
	font-weight: bold;
	margin-top: 5px;
	margin-bottom: 0;
	text-align: center;
}

.resaltado {
	background: var(--azulClaroEmpresarial) !important;
	font-weight: bolder;
	color: white !important;
}

.input-information {
	height: 2em !important;
	color: black;
}

.span-highlight {
	font-weight: bolder;
	color: white;
	background-color: var(--verdeOscuroEmpresarial);
}
.input-highlight {
	font-weight: bolder;
	color: var(--colorAlto);
}

@media (max-width: 900px) {
	.slot-cronograma {
		width: 99% !important;
		margin-left: 0.5% !important;
	}

	.mdlCambiarModo {
		margin-top: 20%;
	}
}
</style>
