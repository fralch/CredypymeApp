<template>
	<div id="mdlDatosSuministro" class="modal">
		<div class="modal-content w-40 mdlDatosSuministro">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="title_modal"
						:nombre_modal="'mdlDatosSuministro'"
					>
					</headerCloseModal>
					<div class="card-title">DATOS DEL SUMINISTRO</div>
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item" role="presentation">
								<a
									class="nav-link active tab-title"
									id="datosSuministro1-tab"
									data-toggle="tab"
									href="#datosSuministro1"
									role="tab"
									aria-controls="datosSuministro1"
									aria-selected="true"
									>INFORMACIÓN</a
								>
							</li>
							<li
								class="nav-item"
								role="presentation"
								v-if="
									frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
									frmDatosSuministro.modo == 'EDITAR_NUEVO'
								"
							>
								<a
									class="nav-link tab-title"
									id="datosSuministro2-tab"
									data-toggle="tab"
									href="#datosSuministro2"
									role="tab"
									aria-controls="datosSuministro2"
									aria-selected="false"
									>VALOR</a
								>
							</li>
						</ul>
						<form autocomplete="off">
							<div class="tab-content" id="myTabContent">
								<div
									class="tab-pane fade show active"
									id="datosSuministro1"
									role="tabpanel"
									aria-labelledby="datosSuministro1-tab"
								>
									<div class="form-row">
										<div class="form-group col-md-3 col-6">
											<label id="lblCodigo" class="label-title">CÓDIGO</label>
											<input
												type="text"
												class="form-control center"
												:value="frmDatosSuministro.codigo"
												disabled
											/>
										</div>
										<div class="form-group col-md-5 col-6">
											<label class="label-title">SUMINISTRO</label>

											<textarea
												type="text"
												rows="2"
												class="form-control text-row mayus"
												:class="[
													submited &&
													(frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_EXISTENTE')
														? $v.frmDatosSuministro.suministro.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosSuministro.suministro"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
												maxlength="200"
											></textarea>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">TIPO</label>

											<select
												class="form-control"
												:class="[
													submited &&
													(frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_EXISTENTE')
														? $v.frmDatosSuministro.tipo_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												@change="GenerarCodigo"
												v-model="frmDatosSuministro.tipo_id"
												:disabled="
													frmDatosSuministro.modo == 'VER_EXISTENTE' ||
													frmDatosSuministro.modo == 'EDITAR_EXISTENTE'
												"
											>
												>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in tipos"
													:key="index"
													:value="item.id"
												>
													{{ item.tipo }}
												</option>
											</select>
										</div>

										<div class="form-group col-md-4 col-6">
											<label class="label-title">AGENCIA</label>

											<select
												class="form-control center mayus"
												v-model="frmDatosSuministro.agencia_id"
												disabled
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in agencias"
													:key="index"
													:value="item.id"
												>
													{{ item.agencia }}
												</option>
											</select>
										</div>

										<div class="form-group col-md-4 col-6">
											<label class="label-title">CONDICIÓN</label>

											<select
												class="form-control center"
												:class="[
													submited &&
													(frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_EXISTENTE')
														? $v.frmDatosSuministro.condicion_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosSuministro.condicion_id"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in condiciones"
													:key="index"
													:value="item.id"
												>
													{{ item.condicion }}
												</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">CLASIFICACIÓN</label>
											<select
												class="form-control center"
												:class="[
													submited &&
													(frmDatosSuministro.clasificacion ==
														'AGREGAR_NUEVO' ||
														frmDatosSuministro.clasificacion ==
															'EDITAR_NUEVO' ||
														frmDatosSuministro.clasificacion ==
															'EDITAR_EXISTENTE')
														? $v.frmDatosSuministro.condicion_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosSuministro.clasificacion"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
											>
												<option :value="'SUMINISTRO'">SUMINISTRO</option>
												<option :value="'GASTO'">GASTO</option>
											</select>
										</div>
										<div
											class="form-group col-md-3 col-6"
											v-if="
												frmDatosSuministro.modo == 'VER_EXISTENTE' ||
												frmDatosSuministro.modo == 'EDITAR_EXISTENTE'
											"
										>
											<label class="label-title">ESTADO</label>

											<input
												type="text"
												class="form-control center"
												:value="frmDatosSuministro.estado"
												disabled
											/>
										</div>

										<div
											class="form-group col-md-3 col-6"
											v-if="
												frmDatosSuministro.modo == 'VER_EXISTENTE' ||
												frmDatosSuministro.modo == 'EDITAR_EXISTENTE'
											"
										>
											<label id="lblMedicion" class="label-title"
												>MEDICIÓN</label
											>
											<input
												type="text"
												class="form-control center"
												:value="frmDatosSuministro.medicion"
												disabled
											/>
										</div>

										<div
											class="form-group col-md-3 col-6"
											v-if="
												frmDatosSuministro.modo == 'VER_EXISTENTE' ||
												frmDatosSuministro.modo == 'EDITAR_EXISTENTE'
											"
										>
											<label class="label-title">STOCK</label>

											<input
												type="text"
												class="form-control center"
												disabled
												:value="frmDatosSuministro.cantidad_actual"
											/>
										</div>

										<div
											class="form-group col-md-3 col-6"
											v-if="
												frmDatosSuministro.modo == 'VER_EXISTENTE' ||
												frmDatosSuministro.modo == 'EDITAR_EXISTENTE'
											"
										>
											<label class="label-title">VALOR UNITARIO</label>
											<input
												type="text"
												class="form-control center"
												disabled
												:value="frmDatosSuministro.valor_unitario"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">MARCA</label>

											<input
												type="text"
												class="form-control mayus"
												v-model="frmDatosSuministro.marca"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
												maxlength="100"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">DETALLE</label>

											<textarea
												type="text"
												rows="2"
												class="form-control text-row mayus"
												v-model="frmDatosSuministro.detalle"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
												maxlength="200"
											></textarea>
										</div>
									</div>
								</div>

								<div
									class="tab-pane fade"
									id="datosSuministro2"
									role="tabpanel"
									aria-labelledby="datosSuministro2-tab"
								>
									<div class="form-row">
										<div class="form-group col-md-3 col-6">
											<label class="label-title">STOCK ACTUAL</label>

											<input
												type="text"
												class="form-control center"
												:disabled="true"
												v-model="frmDatosSuministro.cantidad_actual"
											/>
										</div>
										<div
											class="form-group col-md-3 col-6"
											v-if="
												frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
												frmDatosSuministro.modo == 'EDITAR_NUEVO'
											"
										>
											<label class="label-title">CANT. COMPRA</label>

											<input
												type="number"
												min="0.1"
												step="0.1"
												class="form-control center"
												:class="[
													submited &&
													(frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_EXISTENTE')
														? $v.frmDatosSuministro.cantidad_compra.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												name="cantidad_compra"
												placeholder="mín. 0.1"
												@change="CalcularIGV"
												v-model.number="frmDatosSuministro.cantidad_compra"
											/>
										</div>
										<div class="form-group col-md-6 col-6">
											<label class="label-title">MEDICIÓN</label>

											<select
												class="form-control center"
												:class="[
													submited &&
													(frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_EXISTENTE')
														? $v.frmDatosSuministro.medicion_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosSuministro.medicion_id"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option
													v-for="(item, index) in mediciones"
													:key="index"
													:value="item.id"
												>
													{{ item.medicion }}
												</option>
											</select>
										</div>

										<div
											class="form-group col-md-3 col-4"
											v-if="
												frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
												frmDatosSuministro.modo == 'EDITAR_NUEVO'
											"
										>
											<label class="label-title">VALOR TOTAL</label>
											<input
												type="number"
												class="form-control center"
												name="valor_total"
												min="0.1"
												step="0.01"
												placeholder="S/"
												v-model.number="frmDatosSuministro.valor_total"
												@change="CalcularIGV"
												:disabled="frmDatosSuministro.modo == 'VER_EXISTENTE'"
											/>
										</div>
										<div class="form-group col-md-3 col-6">
											<label class="label-title">VALOR UNITARIO</label>

											<input
												type="text"
												class="form-control center"
												placeholder="S/"
												v-model.number="frmDatosSuministro.valor_unitario"
												:disabled="true"
											/>
										</div>

										<div
											class="form-group col-md-3 col-4"
											v-if="
												frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
												frmDatosSuministro.modo == 'EDITAR_NUEVO'
											"
										>
											<label class="label-title">V.T.(SIGV)</label>
											<input
												type="text"
												class="form-control center"
												placeholder="S/"
												v-model.number="frmDatosSuministro.valor_total_sigv"
												:disabled="true"
											/>
										</div>
										<div
											class="form-group col-md-3 col-4"
											v-if="
												frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
												frmDatosSuministro.modo == 'EDITAR_NUEVO'
											"
										>
											<label class="label-title">V.U.(SIGV)</label>
											<input
												type="text"
												class="form-control center"
												placeholder="S/"
												v-model.number="frmDatosSuministro.valor_unitario_sigv"
												:disabled="true"
											/>
										</div>

										<div
											class="form-group col-md-3 col-6"
											v-if="
												frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
												frmDatosSuministro.modo == 'EDITAR_NUEVO'
											"
										>
											<label class="label-title">IGV(%)</label>
											<span
												v-if="
													submited &&
													(frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
														frmDatosSuministro.modo == 'EDITAR_NUEVO') &&
													(!$v.frmDatosSuministro.igv.noZero ||
														!$v.frmDatosSuministro.igv.required)
												"
												class="span-error-message"
											>
												*
											</span>

											<input
												type="number"
												class="form-control center"
												name="igv"
												min="1"
												step="1"
												placeholder="%"
												@change="CalcularIGV"
												v-model.number="frmDatosSuministro.igv"
											/>
										</div>
										<div class="col-md-9">
											<ul
												class="m-0 pl-3"
												v-if="
													frmDatosSuministro.modo == 'AGREGAR_NUEVO' ||
													frmDatosSuministro.modo == 'EDITAR_NUEVO'
												"
											>
												<li style="font-size: 11px">
													VALOR TOTAL, VALOR UNITARIO: CON IGV.
												</li>
												<li style="font-size: 11px">
													V.T.(SIGV): Valor total SIN IGV.
												</li>
												<li style="font-size: 11px">
													V.U.(SIGV): Valor unitario SIN IGV.
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</form>

						<hr />
						<div class="form-row m-2">
							<div class="col-md-4 col-6">
								<button
									class="btn btn-danger btn-icon-split"
									@click="Eliminar"
									title="Eliminar Suministro"
									v-if="
										frmDatosSuministro.modo == 'VER_EXISTENTE' &&
										frmDatosSuministro.cantidad_actual == 0
									"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
									<span class="text">ELIMINAR</span>
								</button>
							</div>

							<div class="col-md-4 offset-md-4 col-6 text-right">
								<button
									class="btn btn-action btn-icon-split"
									@click="Guardar"
									v-if="frmDatosSuministro.modo != 'VER_EXISTENTE'"
								>
									<span class="icon text-white">
										<i class="fas fa-save"></i>
									</span>
									<span class="text">GUARDAR</span>
								</button>
								<button
									class="btn btn-cancel btn-icon-split"
									@click="Editar"
									v-if="
										frmDatosSuministro.modo == 'VER_EXISTENTE' &&
										$page.props.user_permissions.permisos.includes(
											'LOGISTICA_SUMINISTROS/ALMACEN_EDITAR'
										)
									"
								>
									<span class="icon text-white">
										<i class="fas fa-edit"></i>
									</span>
									<span class="text">EDITAR</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value > 0;
export default {
	components: { headerCloseModal },
	props: {
		tipo_modulo: String,
		agencias: Array,
		usuarios: Array,
		tipos: Array,
		condiciones: Array,
		mediciones: Array,
		estados: Array,
	},
	data() {
		return {
			submited: false,
			title_modal: null,
			usuarios_filtrados: this.usuarios,
			frmDatosSuministro: {
				modo: null,
				id: null,
				agencia_id: 0,
				codigo: null,
				suministro: null,
				marca: null,
				detalle: null,
				tipo_id: 0,
				condicion_id: 0,
				clasificacion: "SUMINISTRO",
				medicion_id: 0,
				cantidad_actual: 0,
				cantidad_compra: 0,
				valor_total: 0,
				valor_unitario: 0,
				estado: null,
				igv: 0,
			},
		};
	},
	validations() {
		if (
			this.frmDatosSuministro.modo == "AGREGAR_NUEVO" ||
			this.frmDatosSuministro.modo == "EDITAR_NUEVO"
		) {
			return {
				frmDatosSuministro: {
					suministro: { required },
					marca: { required },
					tipo_id: { noZero },
					condicion_id: { noZero },
					medicion_id: { noZero },
					cantidad_compra: { noZero, required },
					valor_unitario: { noZero, required },
					igv: { noZero, required },
				},
			};
		} else if (this.frmDatosSuministro.modo == "EDITAR_EXISTENTE") {
			return {
				frmDatosSuministro: {
					suministro: { required },
					marca: { required },
					condicion_id: { noZero },
				},
			};
		}
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

		CalcularIGV(e) {
			let cantidad_compra = this.frmDatosSuministro.cantidad_compra;
			let valor_total = this.frmDatosSuministro.valor_total;
			let igv = this.frmDatosSuministro.igv;

			let valor_total_sigv = this.roundTo(
				valor_total - (igv / 100) * valor_total,
				2
			);

			let valor_unitario = this.roundTo(valor_total / cantidad_compra, 2);

			let valor_unitario_sigv = this.roundTo(
				valor_unitario - (igv / 100) * valor_unitario,
				2
			);

			if (e != null) {
				if (e.target.name == "cantidad_compra") {
					this.frmDatosSuministro.cantidad_compra = this.roundTo(
						cantidad_compra,
						2
					);
				} else if (e.target.name == "valor_total") {
					this.frmDatosSuministro.valor_total = this.roundTo(valor_total, 2);
				} else if (e.target.name == "igv") {
					this.frmDatosSuministro.igv = this.roundTo(igv, 2);
				}
			}

			this.frmDatosSuministro.valor_unitario = valor_unitario;
			this.frmDatosSuministro.valor_total_sigv = valor_total_sigv;
			this.frmDatosSuministro.valor_unitario_sigv = valor_unitario_sigv;
		},

		async GenerarCodigo() {
			let mdlCanastaCompra = this.$parent.$refs.mdlCanastaCompra;
			let tipo_id = this.frmDatosSuministro.tipo_id;

			if (tipo_id == 0) {
				this.frmDatosSuministro.codigo = null;
			} else {
				let tipo = this.tipos.filter((item) => item.id == tipo_id)[0].tipo;

				const params = {
					tipo_id: tipo_id,
					agencia_id: this.frmDatosSuministro.agencia_id,
				};

				await axios
					.get(route("log.sum.almacen.obtener_codigo"), {
						params,
					})
					.then((response) => {
						let ultimo_suministro = response.data.cantidad + 1;

						let cantidad_en_canasta =
							mdlCanastaCompra.frmCanastaCompras.canasta.filter(
								(item) => item.tipo_id == tipo_id
							).length;
						let numero_orden = String(ultimo_suministro + cantidad_en_canasta);

						let primera_letra = tipo.substr(0, 3);
						let nuevo_codigo =
							primera_letra +
							Array(7 - numero_orden.length).join("0") +
							numero_orden;
						this.frmDatosSuministro.codigo = nuevo_codigo;
					});
			}
		},

		Editar() {
			this.frmDatosSuministro.modo = "EDITAR_EXISTENTE";
			this.title_modal = "EDITAR SUMINISTRO";
		},

		async Guardar() {
			this.submited = true;

			if (this.$v.frmDatosSuministro.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿DESEA CONTINUAR?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then(async (result) => {
				if (result.isConfirmed) {
					if (
						this.frmDatosSuministro.modo == "AGREGAR_NUEVO" ||
						this.frmDatosSuministro.modo == "EDITAR_NUEVO"
					) {
						let objeto = {
							codigo: this.frmDatosSuministro.codigo,
							suministro: this.frmDatosSuministro.suministro.toUpperCase(),
							marca: this.frmDatosSuministro.marca.trim().toUpperCase(),
							detalle: this.frmDatosSuministro.detalle.trim().toUpperCase(),
							tipo_id: this.frmDatosSuministro.tipo_id,

							agencia_id: this.frmDatosSuministro.agencia_id,
							condicion_id: this.frmDatosSuministro.condicion_id,
							condicion: this.condiciones.find(
								(item) => item.id == this.frmDatosSuministro.condicion_id
							).condicion,
							clasificacion: this.frmDatosSuministro.clasificacion,
							medicion_id: this.frmDatosSuministro.medicion_id,
							medicion: this.mediciones.find(
								(item) => item.id == this.frmDatosSuministro.medicion_id
							).medicion,
							cantidad_compra: this.frmDatosSuministro.cantidad_compra,
							valor_total: this.frmDatosSuministro.valor_total,
							valor_unitario: this.frmDatosSuministro.valor_unitario,
							igv: this.frmDatosSuministro.igv,
						};

						let mdlCanastaCompra = this.$parent.$refs.mdlCanastaCompra;
						if (this.frmDatosSuministro.modo == "AGREGAR_NUEVO") {
							mdlCanastaCompra.frmCanastaCompras.canasta.push(objeto);
						} else {
							let item =
								mdlCanastaCompra.frmCanastaCompras.canasta[
									this.frmDatosSuministro.index
								];
							item.codigo = objeto.codigo;
							item.suministro = objeto.suministro;
							item.marca = objeto.marca;
							item.detalle = objeto.detalle;

							item.agencia_id = objeto.agencia_id;
							item.condicion_id = objeto.condicion_id;
							item.clasificacion = objeto.clasificacion;
							item.condicion = objeto.condicion;
							item.medicion_id = objeto.medicion_id;
							item.medicion = objeto.medicion;
							item.cantidad_compra = objeto.cantidad_compra;
							item.valor_total = objeto.valor_total;
							item.valor_unitario = objeto.valor_unitario;
							item.igv = objeto.igv;
						}

						$("#mdlDatosSuministro").css("display", "none");
						mdlCanastaCompra.submited = false;
					} else if (this.frmDatosSuministro.modo == "EDITAR_EXISTENTE") {
						Swal.fire({
							title: "BUSCANDO...",
							showConfirmButton: false,
							allowOutsideClick: false,
							didOpen: async () => {
								Swal.showLoading();

								let data = new FormData();
								data.append("tipo_modulo", this.tipo_modulo);
								data.append("suministro_id", this.frmDatosSuministro.id);
								data.append("suministro", this.frmDatosSuministro.suministro);
								data.append("marca", this.frmDatosSuministro.marca.trim());
								data.append(
									"clasificacion",
									this.frmDatosSuministro.clasificacion
								);
								data.append("detalle", this.frmDatosSuministro.detalle.trim());

								data.append(
									"condicion_id",
									this.frmDatosSuministro.condicion_id
								);

								try {
									await axios
										.post(route("log.sum.almacen.editar"), data)
										.then(async (response) => {
											this.$parent.ListarSuministros();
											$("#mdlDatosSuministro").css("display", "none");

											await Swal.close();

											return Swal.fire({
												icon: "success",
												title: "¡Listo!",
												showConfirmButton: false,
												timer: 1500,
											});
										});
								} catch (error) {
									console.error(error);
									await Swal.close(); // cierra primero el anterior

									Swal.fire({
										icon: "error",
										title: "Error",
										text: `Ha ocurrido un error. Comunicar a SOPORTE: ${error.message}`,
									});
								}
							},
						});
					}
				} else {
					return false;
				}
			});
		},
		async Eliminar() {
			let id = this.frmDatosSuministro.id;

			Swal.fire({
				icon: "question",
				text: "¿DESEA ELIMINAR ESTE SUMINISTRO?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
				showLoaderOnConfirm: true,
				preConfirm: async () => {
					// this.$inertia.delete(route("log.sum.almacen.eliminar", id));
					// return false;

					await axios
						.delete(route("log.sum.almacen.eliminar", id))
						.then(async (response) => {
							$("#mdlDatosSuministro").css("display", "none");
							this.$parent.Buscar();

							return Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								text: response.data.message,
								timer: 500,
								showConfirmButton: false,
							});
						})
						.catch((error) => {
							Swal.showValidationMessage(`Ha ocurrido un error: ${error}`);
						});
				},
			});
		},
	},
};
</script>

<style>
.mdlDatosSuministro {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.mdlDatosSuministro {
		margin-top: 20%;
	}
}
</style>
