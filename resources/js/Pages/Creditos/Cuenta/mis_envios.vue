<template>
	<layout ref="layout">
		<div
			class="slot_body slot-mis-envios"
			slot="component-view"
			v-if="mi_cuenta != null"
		>
			<div class="content" style="display: block">
				<div class="card" id="contenido">
					<headerClose :title="'MIS ENVÍOS'"></headerClose>

					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="envios-tab"
									data-toggle="tab"
									href="#envios"
									role="tab"
									aria-controls="envios"
									aria-selected="true"
									>ENVÍOS</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="recepciones-tab"
									data-toggle="tab"
									href="#recepciones"
									role="tab"
									aria-controls="recepciones"
									aria-selected="false"
									>RECEPCIONES</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="envios"
								role="tabpanel"
								aria-labelledby="envios-tab"
							>
								<table class="table" id="tblEnvios" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 100px">ESTADO</th>
											<th style="min-width: 100px">COMPROBANTE_ENVIO</th>
											<th style="min-width: 300px !important">CONCEPTO</th>
											<th style="min-width: 80px">MONTO</th>
											<th>FECHA_HORA_ENVÍO</th>
											<th>AGENCIA_DESTINO</th>
											<th>DESTINATARIO</th>
											<th>COMENTARIO_RECHAZO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in envios"
											:key="index"
											class="table-bordered"
											:class="index % 2 == 0 ? 'verde-claro' : ''"
										>
											<td
												align="center"
												:class="[
													item.estado == 'PENDIENTE'
														? 'pendiente'
														: item.estado == 'CONFIRMADO'
														? 'confirmado'
														: 'rechazado',
												]"
											>
												{{ item.estado }}
											</td>
											<td align="center" style="min-width: 100px">
												<div class="text-center">
													<button
														class="btn btn-cancel btn-icon-split"
														title="Comprobante de ENVÍO"
														@click="
															VerComprobante(
																item.agencia_remitente_id,
																item.comprobante_envio,
																'ENVÍO'
															)
														"
													>
														<span class="icon text-white">
															<i class="far fa-eye" style="color: white"></i>
														</span>
													</button>
													<div
														class="btn-group"
														role="group"
														v-if="item.estado == 'CONFIRMADO'"
													>
														<button
															class="btn btn-action btn-icon-split"
															title="Comprobante de RECEPCIÓN"
															@click="
																VerComprobante(
																	item.agencia_remitente_id,
																	item.comprobante_recepcion,
																	'RECEPCIÓN'
																)
															"
														>
															<span class="icon text-white">
																<i class="far fa-eye" style="color: white"></i>
															</span>
														</button>
														<button
															class="btn btn-cancel btn-icon-split"
															title="Imprimir"
															@click="Imprimir(item, 'envio')"
														>
															<span class="icon text-white">
																<i class="fas fa-print"></i>
															</span>
														</button>
													</div>
												</div>
											</td>
											<td>
												{{ item.concepto }}
											</td>
											<td align="right">
												S/
												{{ parseFloat(item.monto).toFixed(2) }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="center">
												{{ item.agencia_destino }}
											</td>
											<td align="center">
												{{ item.usuario_destinatario }}
											</td>
											<td>
												{{
													item.comentario_rechazo == null
														? "-"
														: item.comentario_rechazo
												}}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div
								class="tab-pane fade"
								id="recepciones"
								role="tabpanel"
								aria-labelledby="recepciones-tab"
							>
								<table
									class="table"
									id="tblRecepciones"
									style="width: 100% !important"
								>
									<thead>
										<tr>
											<th style="min-width: 100px">ESTADO</th>
											<th style="min-width: 100px">COMPROBANTE_ENVÍO</th>
											<th style="min-width: 300px">CONCEPTO</th>
											<th style="min-width: 80px">MONTO</th>
											<th>FECHA_HORA_ENVÍO</th>
											<th>AGENCIA_REMITENTE</th>
											<th>REMITENTE</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in recepciones"
											:key="index"
											:id="'rec_' + item.agencia_remitente_id + '_' + item.id"
											class="table-bordered"
											:class="index % 2 == 0 ? 'verde-claro' : ''"
										>
											<td
												align="center"
												:class="[
													item.estado == 'PENDIENTE'
														? 'pendiente'
														: item.estado == 'CONFIRMADO'
														? 'confirmado'
														: 'rechazado',
												]"
											>
												{{ item.estado }}
											</td>

											<td align="center" style="min-width: 100px">
												<div class="text-center">
													<button
														class="btn btn-cancel btn-icon-split"
														title="Comprobante de ENVÍO"
														@click="
															VerComprobante(
																item.agencia_remitente_id,
																item.comprobante_envio,
																'ENVÍO'
															)
														"
													>
														<span class="icon text-white">
															<i class="far fa-eye" style="color: white"></i>
														</span>
													</button>
													<div
														class="btn-group"
														role="group"
														v-if="item.estado == 'CONFIRMADO'"
													>
														<button
															class="btn btn-action btn-icon-split"
															title="Comprobante de RECEPCIÓN"
															@click="
																VerComprobante(
																	item.agencia_remitente_id,
																	item.comprobante_recepcion,
																	'RECEPCIÓN'
																)
															"
														>
															<span class="icon text-white">
																<i class="far fa-eye" style="color: white"></i>
															</span>
														</button>
														<button
															class="btn btn-cancel btn-icon-split"
															title="Imprimir"
															@click="Imprimir(item, 'recepcion')"
														>
															<span class="icon text-white">
																<i class="fas fa-print"></i>
															</span>
														</button>
													</div>
												</div>
											</td>
											<!-- ------------ fin de comprobante -->
											<td>
												{{ item.concepto }}
											</td>
											<td align="right">
												S/
												{{ parseFloat(item.monto).toFixed(2) }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="center">
												{{ item.agencia_remitente }}
											</td>
											<td align="center">
												{{ item.usuario_remitente }}
											</td>
										</tr>
									</tbody>
								</table>

								<div class="text-right mt-2">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											title="Aceptar"
											@click="Aceptar"
										>
											<span class="icon text-white">
												<i class="fas fa-check"></i>
											</span>
											<span class="text">ACEPTAR</span>
										</button>
										<button
											class="btn btn-danger btn-icon-split"
											title="Rechazar"
											@click="Confirmar('RECHAZAR')"
										>
											<span class="icon text-white">
												<i class="fas fa-times"></i>
											</span>
											<span class="text">RECHAZAR</span>
										</button>
									</div>
								</div>
							</div>
							<hr />
							<div class="text-left">
								<button
									class="btn btn-cancel btn-icon-split"
									@click="Actualizar"
								>
									<span class="icon text-white">
										<i class="fas fa-sync-alt"></i
									></span>
									<span class="text">ACTUALIZAR</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="mdlComprobante" class="modal">
				<div class="modal-content w-40 mdlComprobante">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								ref="headerCloseModal"
								:titulo_modal="titulo_modal"
								:nombre_modal="'mdlComprobante'"
							>
							</headerCloseModal>
							<div class="card-body card-block">
								<div class="p-2">
									<div
										style="
											width: 100% !important;
											height: 300px !important;
											box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
										"
									>
										<img
											:src="
												'/imagenes_server/creditos/cuenta/envios/' +
												agencia_comprobante +
												'/' +
												comprobante_vista.substring(0, 4) +
												'/' +
												comprobante_vista
											"
											v-if="comprobante_vista != null"
											alt="ruta"
											width="100%"
											height="300px"
										/>
									</div>
								</div>

								<hr />
								<div class="text-center">
									<button
										class="btn btn-cancel btn-icon-split"
										@click="Descargar"
									>
										<span class="icon text-white">
											<i class="fas fa-download"></i>
										</span>
										<span class="text">DESCARGAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal" id="mdlAceptarEnvio">
				<div class="modal-content w-40 mdlAceptarEnvio">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								ref="headerCloseModal"
								:titulo_modal="'COMPROBANTE DE RECEPCIÓN'"
								:nombre_modal="'mdlAceptarEnvio'"
							>
							</headerCloseModal>

							<div class="card-body">
								<div class="p-2">
									<div
										id="previzualizar"
										style="
											width: 100% !important;
											height: 300px !important;
											box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
										"
									></div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="form-control-label label-title"
											>ADJUNTAR COMPROBANTE</label
										>
										<span
											v-if="submited && comprobante_recepcion == null"
											class="span-error-message"
											>*</span
										>
										<input
											class="btn btn-primary"
											style="
												background-color: var(--plomoOscuroEmpresarial);
												border: none;
												max-width: 400px;
												font-size: 12px;
											"
											type="file"
											accept="image/*"
											name="comprobante_recepcion"
											id="inpComprobanteRecepcion"
											@change="AgregarComprobante"
										/>
									</div>
								</div>

								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Confirmar('ACEPTAR')"
									>
										<span class="icon text-white">
											<i class="fas fa-check"></i>
										</span>
										<span class="text">CONFIRMAR</span>
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
		envios: Array,
		recepciones: Array,
	},
	data() {
		return {
			submited: false,

			titulo_modal: null,

			ruta_imagen: null,

			agencia_remitente: null,

			comprobante_recepcion: null,

			comprobante_vista: null,

			recepcion_seleccionada: {},
		};
	},

	computed: {
		mi_cuenta() {
			return this.$inertia.page.props.creditos_datos.datos_cuenta;
		},
	},

	mounted() {
		if (this.mi_cuenta == null) {
			Swal.fire({
				title: "¡Ups!",
				text: "Usted no tiene una cuenta habilitada",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
				preConfirm: (result) => {
					this.$inertia.get(route("cre.index"));
				},
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			this.TablasEnvios();
		}
	},
	methods: {
		TablasEnvios() {
			this.$nextTick(() => {
				var table_1 = $("#tblEnvios").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
					select: {
						style: "single",
						info: false,
					},
					language: {
						retrieve: true,
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});
				var table_1 = $("#tblRecepciones").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
					select: {
						style: "single",
						info: false,
					},
					language: {
						retrieve: true,
						emptyTable: "No hay datos disponibles en la tabla",
						info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
						infoEmpty: "No se encontraron registros",
						infoFiltered: "(filtrado de _MAX_ registros)",
						thousands: ",",
						paginate: {
							first: "Primera",
							last: "Ultima",
							next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
							previous:
								'<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
						},
					},
				});
			});
		},

		Aceptar() {
			let row = document
				.getElementById("tblRecepciones")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione un ENVÍO",
					allowOutsideClick: true,
				});
				this.recepcion_seleccionada = {};
				return false;
			} else {
				let datos_envio = row.id.replace("rec_", "");
				datos_envio = datos_envio.split("_");

				let agencia_remitente_id = datos_envio[0];
				let envio_id = datos_envio[1];

				let recepcion = this.recepciones.filter(
					(item) =>
						item.agencia_remitente_id == agencia_remitente_id &&
						item.id == envio_id
				)[0];

				if (recepcion.estado != "PENDIENTE") {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Esta transferencia ya fue CONFIRMADA.",
						allowOutsideClick: true,
					});
					return false;
				}

				this.submited = false;
				this.recepcion_seleccionada = recepcion;

				let previo = $("#previzualizar img");
				previo.remove();
				this.comprobante_recepcion = null;
				$("#inpComprobanteRecepcion").val("");

				$("#mdlAceptarEnvio").css("display", "block");
			}
		},

		AgregarComprobante(e) {
			let previo = $("#previzualizar img");
			previo.remove();

			this.comprobante_recepcion = e.target.files[0];

			let reader = new FileReader();
			reader.readAsDataURL(e.target.files[0]);
			reader.onload = function () {
				let preview = document.getElementById("previzualizar"),
					image = document.createElement("img");

				image.src = reader.result;
				image.style.border = "1px solid #ffff";

				image.style.width = "100%";
				image.style.height = "300px";

				preview.innerHTML = "";
				preview.append(image);
			};
		},

		VerComprobante(agencia_remitente_id, comprobante, tipo) {
			this.titulo_modal = "COMPROBANTE DE " + tipo;
			this.agencia_comprobante = agencia_remitente_id;
			this.comprobante_vista = comprobante;
			$("#mdlComprobante").css("display", "block");
		},
		Descargar() {
			let agencia_id = this.agencia_comprobante;
			let año = this.comprobante_vista.substring(0, 4);

			let source =
				"/imagenes_server/creditos/cuenta/envios/" +
				agencia_id +
				"/" +
				año +
				"/" +
				this.comprobante_vista;
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = this.comprobante_vista;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},

		Confirmar(modo) {
			let self = this;
			this.submited = true;

			if (modo == "RECHAZAR") {
				let row = document
					.getElementById("tblRecepciones")
					.getElementsByClassName("selected")[0];

				if (row == undefined) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Seleccione un ENVÍO",
						allowOutsideClick: true,
					});
					this.recepcion_seleccionada = {};
					return false;
				} else {
					let datos_envio = row.id.replace("rec_", "");
					datos_envio = datos_envio.split("_");

					let agencia_remitente_id = datos_envio[0];
					let envio_id = datos_envio[1];

					let recepcion = this.recepciones.filter(
						(item) =>
							item.agencia_remitente_id == agencia_remitente_id &&
							item.id == envio_id
					)[0];

					if (recepcion.estado != "PENDIENTE") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Esta transferencia ya fue CONFIRMADA.",
							allowOutsideClick: true,
						});
						return false;
					}
					this.recepcion_seleccionada = recepcion;
				}
			}

			if (modo == "ACEPTAR" && this.comprobante_recepcion == null) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Debe adjuntar el COMPROBANTE de recepción",
				});
				return false;
			}

			Swal.fire({
				title: modo + " ENVÍO",
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
					data.append("modo", modo);

					if (modo == "ACEPTAR") {
						data.append(
							"agencia_remitente",
							this.recepcion_seleccionada.agencia_remitente_id
						);
						data.append("documento", self.comprobante_recepcion);
						data.append("envio", JSON.stringify(this.recepcion_seleccionada));
						self.RutaConfirmar(data);
					} else if (modo == "RECHAZAR") {
						Swal.fire({
							text: "COMENTARIO DE RECHAZO",
							confirmButtonText:
								'<div style="font-size:13px"><i class="fas fa-check"></i>   Aceptar</div>',
							confirmButtonColor: "var(--colorAlto)",
							showCancelButton: true,
							cancelButtonText:
								'<div style="font-size:13px"><i class="fas fa-times"></i>   Cancelar</div>',
							cancelButtonColor: "var(--plomoOscuroEmpresarial)",
							allowOutsideClick: false,
							input: "text",
							customClass: {
								input: "mayus",
							},
							inputValidator: (value) => {
								if (!value) {
									return "*Obligatorio";
								}
							
							},
						}).then((result) => {
							if (result.isConfirmed) {
								data.append(
									"agencia_remitente",
									this.recepcion_seleccionada.agencia_remitente_id
								);
								data.append("envio_id", this.recepcion_seleccionada.id);
								data.append("comentario", result.value);
								self.RutaConfirmar(data);
							} else {
								return false;
							}
						});
					}
				} else {
					return false;
				}
			});
		},

		RutaConfirmar(data) {
			this.$inertia.post(route("cue.mis_envios.confirmar"), data, {
				preserveScroll: true,
				onStart: (visit) => {
					let timerInterval;
					Swal.fire({
						title: "CARGANDO",
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
						allowOutsideClick: false,
					});
					$("#mdlAceptarEnvio").css("display", "none");
				},
			});
		},

		Actualizar() {
			this.$inertia.get(
				route("cue.mis_envios"),
				{},
				{
					preserveScroll: true,
					onStart: (visit) => {
						let timerInterval;
						Swal.fire({
							title: "CARGANDO",
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
							title: "¡ACTUALIZADO!",
							allowOutsideClick: true,
						});
					},
				}
			);
		},

		async Imprimir(item, tipo) {
			let data = new FormData();

			data.append("datos_envio", JSON.stringify(item));
			data.append("tipo", tipo);

			// this.$inertia.post(route("cue.mis_envios.imprimir"), data);
			// return false;

			await axios
				.post(route("cue.mis_envios.imprimir"), data)
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
.slot-mis-envios {
	width: 50% !important;
	margin-left: 25% !important;
	margin-top: 10% !important;
}
.pendiente {
	background-color: var(--verdeOscuroEmpresarial) !important;
	color: white !important;
}

.confirmado {
	background-color: var(--azulOscuroEmpresarial) !important;
	color: white !important;
}

.rechazado {
	background-color: var(--red) !important;
	color: white !important;
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

.mdlAceptarEnvio {
	margin-top: 10%;
}
.mdlComprobante {
	margin-top: 15%;
}

@media (max-width: 900px) {
	.slot-mis-envios {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>
