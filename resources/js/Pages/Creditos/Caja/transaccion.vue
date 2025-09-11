<template>
	<layout ref="layout">
		<div
			class="slot_body slot-transaccion"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block">
				<!-- ---------------------- -->
				<div class="content" style="display: block">
					<div class="card">
						<headerClose :title="'REGISTRAR TRANSACCIÓN'"></headerClose>
						<div class="card-title">DETALLE</div>
						<div class="card-body card-block">
							<div class="form-row">
								<div class="col-md-4">
									<div class="form-check col-md-12 text-center">
										<div class="radios" style="display: inline-block">
											<input
												class="form-check-input"
												type="radio"
												name="tipo_transaccion"
												id="rdbIngreso"
												v-model="frmDatosTransaccion.tipo_transaccion"
												value="I"
												:disabled="transaccion_registrada != null"
											/>
											<label class="form-check-label bolder" for="rdbIngreso"
												>INGRESO</label
											>
										</div>
										<div
											class="radios"
											style="display: inline-block; margin-left: 30px"
										>
											<input
												class="form-check-input"
												type="radio"
												name="tipo_transaccion"
												id="rdbEgreso"
												v-model="frmDatosTransaccion.tipo_transaccion"
												value="E"
												:disabled="transaccion_registrada != null"
											/>
											<label class="form-check-label bolder" for="rdbEgreso"
												>EGRESO</label
											>
										</div>
									</div>
									<div class="col-md-12 mt-3">
										<label class="label-title">MONTO</label>
										<span
											v-if="
												submited &&
												(!$v.frmDatosTransaccion.monto.noZero ||
													!$v.frmDatosTransaccion.monto.required)
											"
											class="span-error-message"
											>*</span
										>
										<div class="input-group">
											<div class="input-group-prepend">
												<span
													class="input-group-text prepend-title"
													style="font-size: 20px !important"
													>S/</span
												>
											</div>
											<input
												type="number"
												min="0.1"
												step="1"
												class="form-control text-center"
												style="
													height: 65px;
													font-size: 30px;
													font-weight: bolder;
													color: var(--colorAlto);
												"
												@change="Redondear"
												@focus="hidenav()"
												@blur="shownav()"
												v-model="frmDatosTransaccion.monto"
												:disabled="transaccion_registrada != null"
											/>
										</div>
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-row">
										<div class="col-md-6">
											<label class="label-title" for="text-input"
												>CATEGORÍA</label
											>
											<span
												v-if="
													submited &&
													!$v.frmDatosTransaccion.categoria_id.noZero
												"
												class="span-error-message"
												>*</span
											>
											<select
												class="form-control center"
												@change="FiltrarSubcategorias"
												v-model="frmDatosTransaccion.categoria_id"
												:disabled="transaccion_registrada != null"
											>
												<option :value="0" selected disabled>
													Seleccionar...
												</option>
												<option
													v-for="(item, index) in categorias_filtradas"
													:key="index"
													:value="item.id"
												>
													{{ item.categoria }}
												</option>
											</select>
										</div>
										<div class="col-md-6">
											<label class="label-title" for="text-input"
												>SUBCATEGORÍA</label
											>
											<span
												v-if="
													submited &&
													!$v.frmDatosTransaccion.subcategoria_id.noZero
												"
												class="span-error-message"
												>*</span
											>
											<select
												class="form-control center"
												v-model="frmDatosTransaccion.subcategoria_id"
												:disabled="
													frmDatosTransaccion.categoria_id == 0 ||
													transaccion_registrada != null
												"
											>
												<option :value="0" selected disabled>
													Seleccionar...
												</option>
												<option
													v-for="(item, index) in subcategorias_filtradas"
													:key="index"
													:value="item.id"
												>
													{{ item.subcategoria }}
												</option>
											</select>
										</div>

										<div class="col-md-6 col-6">
											<label class="label-title" for="text-input"
												>PARA AGENCIA
											</label>

											<select
												class="form-control center"
												v-model="agencia_busqueda"
												:disabled="transaccion_registrada != null"
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
										<div class="col-md-6 col-6">
											<label class="label-title" for="text-input"
												>PARA EL USUARIO
											</label>
											<span
												v-if="
													submited && !$v.frmDatosTransaccion.usuario_id.noZero
												"
												class="span-error-message"
												>*</span
											>
											<select
												class="form-control center"
												v-model="frmDatosTransaccion.usuario_id"
												:disabled="
													agencia_busqueda == 0 ||
													transaccion_registrada != null
												"
											>
												<option :value="0" selected disabled>
													Seleccionar...
												</option>
												<option
													v-for="(item, index) in usuarios_filtrados"
													:key="index"
													:value="item.dni"
												>
													{{ item.usuario }}
												</option>
											</select>
										</div>
										<div class="col-md-6">
											<label class="label-title" for="text-input"
												>PARA EL ÁREA
											</label>
											<span
												v-if="
													submited &&
													!$v.frmDatosTransaccion.area_trabajo_id.noZero
												"
												class="span-error-message"
												>*</span
											>
											<select
												class="form-control center"
												v-model="frmDatosTransaccion.area_trabajo_id"
												:disabled="transaccion_registrada != null"
											>
												<option :value="0" selected disabled>
													Seleccionar...
												</option>
												<option
													v-for="(item, index) in areas_trabajo"
													:key="index"
													:value="item.id"
												>
													{{ item.area }}
												</option>
											</select>
										</div>

										<div class="col-md-6">
											<label class="label-title" for="text-input"
												>TIPO DE COMPROBANTE</label
											>
											<span
												v-if="
													submited &&
													!$v.frmDatosTransaccion.comprobante_id.noZero
												"
												class="span-error-message"
												>*</span
											>
											<select
												class="form-control center"
												v-model="frmDatosTransaccion.comprobante_id"
												:disabled="transaccion_registrada != null"
											>
												<option :value="0" selected disabled>
													Seleccionar...
												</option>
												<option
													v-for="(item, index) in comprobantes"
													:key="index"
													:value="item.id"
												>
													{{ item.comprobante }}
												</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-8">
									<label class="label-title">CONCEPTO</label>
									<span
										v-if="submited && !$v.frmDatosTransaccion.concepto.required"
										class="span-error-message"
										>*</span
									>
									<textarea
										type="text"
										rows="3"
										class="form-control mayus text-row"
										@focus="hidenav()"
										@blur="shownav()"
										v-model="frmDatosTransaccion.concepto"
										:disabled="transaccion_registrada != null"
									></textarea>
								</div>
								<div class="col-md-4">
									<div class="form-group col-md-12">
										<label class="label-title col-md-12">DOCUMENTO</label>
										<span
											v-if="
												submited &&
												!$v.frmDatosTransaccion.documento_transaccion.required
											"
											class="span-error-message"
											>*</span
										>
										<input
											class="btn btn-primary"
											style="
												background-color: var(--plomoOscuroEmpresarial);
												border: none;

												font-size: 12px;
											"
											type="file"
											accept="image/*"
											name="documento_transaccion"
											id="documento_transaccion"
											@change="AgregarComprobante"
											:disabled="transaccion_registrada != null"
										/>
									</div>
								</div>
							</div>

							<!-- ------------------------------- -->
							<hr />
							<div class="form-row">
								<div class="form-group col-md-6">
									<button
										class="btn btn-action btn-icon-split"
										title="Nueva TRANSACCIÓN"
										@click="Nuevo"
										v-if="transaccion_registrada != null"
									>
										<span class="icon text-white">
											<i class="fas fa-plus"></i>
										</span>
										<span class="text">NUEVA TRANSACCIÓN</span>
									</button>
								</div>
								<div class="form-group col-md-6 text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-cancel btn-icon-split"
											@click="Voucher"
											title="Voucher TRANSACCIÓN"
											v-if="transaccion_registrada != null"
										>
											<span class="icon text-white">
												<i class="fas fa-print"></i>
											</span>
											<span class="text">VOUCHER</span>
										</button>

										<button
											class="btn btn-action btn-icon-split"
											@click="Registrar"
											title="Registrar TRANSACCIÓN"
											v-if="transaccion_registrada == null"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text">REGISTRAR</span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- ---------------------- -->
			</div>
			<vchTransaccion ref="vchTransaccion"> </vchTransaccion>
		</div>
	</layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import vchTransaccion from "@/Pages/Creditos/Caja/Reports/vchTransaccion.vue";

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose, vchTransaccion },
	props: {
		agencia_id: Number,
		transaccion_registrada: Object,
		categorias: Array,
		subcategorias: Array,
		areas_trabajo: Array,
		comprobantes: Array,
		usuarios: Array,
	},
	data() {
		return {
			submited: false,
			agencia_busqueda: 0,

			agencias_permitidas: [],

			categorias_filtradas: [],
			subcategorias_filtradas: [],
			usuarios_filtrados: [],

			frmDatosTransaccion: {
				tipo_transaccion: "E",
				monto: parseFloat(20).toFixed(2),
				categoria_id: 0,
				subcategoria_id: 0,
				agencia_id: 0,
				usuario_id: 0,
				area_trabajo_id: 0,
				comprobante_id: 0,
				concepto: null,
				documento_transaccion: null,
			},
		};
	},
	validations: {
		frmDatosTransaccion: {
			monto: { noZero, required },
			categoria_id: { noZero },
			subcategoria_id: { noZero },
			usuario_id: { noZero },
			area_trabajo_id: { noZero },
			comprobante_id: { noZero },
			concepto: { required },
			documento_transaccion: { required },
		},
	},
	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		tipo_transaccion() {
			return this.frmDatosTransaccion.tipo_transaccion;
		},
	},

	watch: {
		tipo_transaccion() {
			this.FiltrarCategorias();
			this.FiltrarSubcategorias();
		},
		agencia_busqueda() {
			this.FiltrarUsuarios();
		},
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_busqueda = mi_agencia[0].id;
			} else {
				if (value.length > 0) {
					this.agencia_busqueda = value[0].id;
				} else {
					this.agencia_busqueda = null;
				}
			}
		},
	},
	mounted() {
		if (this.mi_caja == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Primero debe aperturar CAJA",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			this.ListarAgenciasPermitidas();

			this.FiltrarCategorias();

			if (this.transaccion_registrada != null) {
				this.frmDatosTransaccion.tipo_transaccion =
					this.transaccion_registrada.tipo;
				this.frmDatosTransaccion.monto = parseFloat(
					this.transaccion_registrada.monto
				).toFixed(2);
				this.FiltrarSubcategorias();

				this.frmDatosTransaccion.area_trabajo_id =
					this.transaccion_registrada.area_trabajo_id;
				this.frmDatosTransaccion.comprobante_id =
					this.transaccion_registrada.comprobante_id;
				this.frmDatosTransaccion.concepto =
					this.transaccion_registrada.concepto;
			}
		}
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/TRANSACCION"
			);
		},
		FiltrarUsuarios() {
			this.usuarios_filtrados = this.usuarios.filter(
				(item) => item.agencia_id == this.agencia_busqueda
			);
			if (this.transaccion_registrada != null) {
				this.frmDatosTransaccion.usuario_id =
					this.transaccion_registrada.usuario_id;
			} else {
				this.frmDatosTransaccion.usuario_id = 0;
			}
		},

		FiltrarCategorias() {
			this.categorias_filtradas = this.categorias.filter(
				(item) => item.tipo == this.tipo_transaccion
			);

			if (this.transaccion_registrada != null) {
				this.frmDatosTransaccion.categoria_id =
					this.transaccion_registrada.categoria_id;
			} else {
				this.frmDatosTransaccion.categoria_id = 0;
			}
		},
		FiltrarSubcategorias() {
			this.subcategorias_filtradas = this.subcategorias.filter(
				(item) => item.categoria_id == this.frmDatosTransaccion.categoria_id
			);
			if (this.transaccion_registrada != null) {
				this.frmDatosTransaccion.subcategoria_id =
					this.transaccion_registrada.subcategoria_id;
			} else {
				this.frmDatosTransaccion.subcategoria_id = 0;
			}
		},

		Redondear(e) {
			let valor = 0.1;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0.1) {
				valor = e.target.value;
			}

			this.frmDatosTransaccion.monto = this.$refs.layout.round(
				valor,
				numero_decimales
			);
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},

		AgregarComprobante(e) {
			this.frmDatosTransaccion.documento_transaccion = e.target.files[0];
		},

		// Imprimir() {
		// 	$("#AreaImprimible").print();
		// },

		Registrar() {
			let self = this;
			this.submited = true;

			let mi_caja = this.$inertia.page.props.creditos_datos.datos_caja;

			if (this.$v.frmDatosTransaccion.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos.",
				});
				return false;
			} else if (mi_caja == null) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Aperture CAJA para continuar.",
				});
				return false;
			}

			Swal.fire({
				title: "REGISTRAR TRANSACCIÓN",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					let data = new FormData();
					data.append(
						"frmDatosTransaccion",
						JSON.stringify(self.frmDatosTransaccion)
					);
					data.append("agencia_id", JSON.stringify(self.agencia_busqueda));

					data.append(
						"documento",
						self.frmDatosTransaccion.documento_transaccion
					);
					data.append("caja_id", mi_caja.id);

					self.$inertia.post(route("caj.transaccion.registrar"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "REGISTRANDO",
								text: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								allowOutsideClick: true,
							});
						},
					});
				} else {
					return false;
				}
			});
		},
		Nuevo() {
			Swal.fire({
				title: "¿Desea crear una nueva TRANSACCIÓN?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.$inertia.get(route("caj.transaccion"));
				} else {
					return false;
				}
			});
		},
		async Voucher() {
			let self = this;
			let fecha_hora_actual = await this.$refs.layout.fecha_hora_actual(
				this.agencia_id
			);
			let voucher = self.$refs.vchTransaccion;
			async function EnviarDatos() {
				let datos_transaccion = {
					tipo: self.transaccion_registrada.tipo == "I" ? "INGRESO" : "EGRESO",
					concepto: self.transaccion_registrada.concepto,
					monto: self.transaccion_registrada.monto,
				};

				voucher.concepto = "TRANSACCIÓN";
				voucher.datos_transaccion = datos_transaccion;
				voucher.fecha_hora_actual = fecha_hora_actual;
			}
			EnviarDatos().then(() => {
				$("#vchTransaccion").css("display", "block");

				var css = "@page { size: portrait ; }",
					head = document.head || document.getElementsByTagName("head")[0],
					style = document.createElement("style");

				style.type = "text/css";
				style.media = "print";

				if (style.styleSheet) {
					style.styleSheet.cssText = css;
				} else {
					style.appendChild(document.createTextNode(css));
				}

				head.appendChild(style);

				$("#vchTransaccion").print();

				head.removeChild(style);

				$("#vchTransaccion").css("display", "none");
			});
		},
	},
};
</script>

<style lang="css">
.slot-transaccion {
	width: 60% !important;
	margin-left: 20% !important;
}

@media only screen and (max-width: 900px) {
	.slot-transaccion {
		width: 96% !important;
		margin-left: 2% !important;
	}
}
</style>




