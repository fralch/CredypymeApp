<template>
	<layout ref="layout">
		<div
			class="slot_body slot-crear-inversion-meta mx-auto"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'INVERSIÓN - CREAR PRODUCTO META'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-12 mb-1 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>TITULAR</span
									>
								</div>
								<input
									type="text"
									class="form-control input-information input-highlight"
									onkeydown="return false"
									spellcheck="false"
									:value="nombre_completo_titular"
								/>
							</div>

							<div class="input-group col-md-5 mb-1 mt-1 col-5">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title span-highlight"
										>DNI</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information input-highlight"
									onkeydown="return false"
									spellcheck="false"
									:value="datos_titular.dni"
								/>
							</div>

							<div class="input-group col-md-7 mb-1 mt-1 col-7">
								<div class="input-group-prepend">
									<span
										class="input-group-text prepend-title"
										v-if="windowWidth >= 900"
										>FECHA DE REGISTRO</span
									>
									<span
										class="input-group-text prepend-title"
										v-if="windowWidth < 900"
										>FEC. REG.</span
									>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									onkeydown="return false"
									spellcheck="false"
									:value="fecha_registro"
								/>
							</div>
						</div>
						<hr />
						<div class="form-row">
							<div class="form-group col-8 mx-auto my-1">
								<label class="label-title">PRODUCTO META</label>
								<select
									class="form-control"
									:class="[
										submited
											? $v.frmDatosInversion.producto_meta_id.$invalid
												? 'is-invalid'
												: 'is-valid'
											: '',
									]"
									v-model="frmDatosInversion.producto_meta_id"
									:disabled="datos_voucher != null"
								>
									<option :value="0" disabled>Seleccione...</option>
									<option
										v-for="(item, index) in productos_meta"
										:key="index"
										:value="item.id"
									>
										{{ item.producto }}
									</option>
								</select>
							</div>
							<div class="form-group col-4 mx-auto my-1">
								<label class="label-title">VALOR META (S/)</label>
								<input
									type="text"
									class="form-control center"
									onkeydown="return false"
									spellcheck="false"
									:value="valor_meta"
									disabled
								/>
							</div>
							<div class="form-group col-12">
								<label class="label-title">COMENTARIO</label>
								<textarea
									:disabled="datos_voucher != null"
									class="text-row form-control mayus"
									maxlength="200"
									rows="2"
									v-model="frmDatosInversion.comentario"
								></textarea>
							</div>
						</div>
						<div class="text-center my-1">
							<button
								v-if="datos_voucher == null"
								class="btn btn-action btn-icon-split mb-1"
								title="Guardar"
								@click="Guardar"
							>
								<span class="icon text-white">
									<i class="fas fa-save"></i>
								</span>
								<span class="text">REGISTRAR</span>
							</button>
							<button
								v-else
								class="btn btn-cancel btn-icon-split mb-1"
								@click="ImprimirVoucher"
							>
								<span class="icon text-white">
									<i class="fas fa-print"></i>
								</span>
								<span class="text">VOUCHER</span>
							</button>
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

const noZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		datos_titular: Object,
		productos_meta: Array,
		agencia_id: Number,
		datos_voucher: Object,
	},
	data() {
		return {
			windowWidth: window.innerWidth,

			submited: false,
			frmDatosInversion: {
				cliente_id: this.datos_titular.id,
				producto_meta_id: 0,
				comentario: null,
			},
		};
	},
	validations: {
		frmDatosInversion: {
			producto_meta_id: { noZero },
		},
	},

	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		if (this.mi_caja == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Primero debe aperturar CAJA",
				confirmButtonText: " Ok",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		}
	},

	computed: {
		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},

		nombre_completo_titular() {
			let nombre_completo_titular =
				this.datos_titular.apellido_paterno +
				" " +
				this.datos_titular.apellido_materno +
				" " +
				this.datos_titular.nombres;
			return nombre_completo_titular;
		},

		fecha_registro() {
			let datos_creacion = JSON.parse(this.datos_titular.datos_creacion);
			return datos_creacion.fecha;
		},

		valor_meta() {
			let producto_meta_id = this.frmDatosInversion.producto_meta_id;
			let valor_meta = 0;
			if (producto_meta_id != 0) {
				valor_meta = this.productos_meta.filter(
					(item) => item.id == producto_meta_id
				)[0].valor_meta;
			}

			return this.roundTo(valor_meta, 2);
		},
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
		Guardar() {
			this.submited = true;

			if (this.frmDatosInversion.producto_meta_id == 0) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Selecciona un producto",
				});
				return false;
			} else {
				Swal.fire({
					title: "GUARDAR CAMBIOS",
					text: "¿Desea continuar?",
					confirmButtonText: "Si",
					showCancelButton: true,
					cancelButtonText: "No",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						let data = new FormData();

						this.frmDatosInversion.valor_meta = parseFloat(this.valor_meta);
						data.append("agencia_id", this.agencia_id);
						data.append(
							"frmDatosInversion",
							JSON.stringify(this.frmDatosInversion)
						);
						data.append("caja", JSON.stringify(this.mi_caja));

						this.$inertia.post(route("inv.meta.registrar"), data, {
							preserveScroll: true,
							onStart: () => {
								Swal.fire({
									title: "GUARDANDO",
									text: "Espere porfavor...",
									showConfirmButton: false,
									allowOutsideClick: false,
									willOpen: () => {
										Swal.showLoading();
									},
								});
							},
							onSuccess: () => {
								this.submited = false;
								Swal.fire({
									icon: "success",
									title: "¡ÉXITO!",
									timer: 1200,
									showConfirmButton: false,
								});
								return this.ImprimirVoucher();
							},
						});
					} else {
						return false;
					}
				});
			}
		},
		async ImprimirVoucher() {
			let data = new FormData();

			data.append("titulo", "APERTURA - PRODUCTO META");
			data.append("agencia_id", this.agencia_id);
			data.append("agencia", this.$page.props.user_session.nombre_agencia);
			data.append("usuario", this.$page.props.user_session.usuario);
			data.append(
				"dispositivo",
				this.$page.props.user_session.dispositivo.nombre
			);

			data.append("concepto", "APERTURA");

			let frmDatosVoucher = {};
			let producto_meta_id = this.frmDatosInversion.producto_meta_id;
			frmDatosVoucher = {
				cliente: this.nombre_completo_titular,
				producto: this.productos_meta.filter(
					(item) => item.id == producto_meta_id
				)[0].producto,
				monto: 0,
				comentario: this.frmDatosInversion.comentario,
			};
			data.append("frmDatosVoucher", JSON.stringify(frmDatosVoucher));
			// this.$inertia.post(route("inv.meta.voucher"), data);

			await axios
				.post(route("inv.meta.voucher"), data)
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
	},
};
</script>

<style lang="css">
.slot-crear-inversion-meta {
	width: 30% !important;
	margin-left: 35% !important;
}

.input-information {
	height: 2em !important;
	color: black;
}

.input-highlight {
	font-weight: bolder;
	color: var(--colorAlto);
}

.span-highlight {
	font-weight: bolder;
	color: white;
	background-color: var(--verdeOscuroEmpresarial);
}

@media only screen and (max-width: 1600px) {
	.slot-crear-inversion-meta {
		width: 40% !important;
	}
}

@media only screen and (max-width: 900px) {
	.slot-crear-inversion-meta {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>

