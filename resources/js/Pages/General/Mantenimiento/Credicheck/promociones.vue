<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body slot-promociones">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'PROMOCIONES'"></headerClose>
					<div class="card-title">FILTROS DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-7 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text"
										><i class="fas fa-search"></i
									></span>
								</div>
								<input
									class="form-control mayus"
									type="text"
									id="inpBuscar"
									autocomplete="off"
									spellcheck="false"
									@focus="hidenav()"
									@blur="shownav()"
								/>
							</div>

							<div class="input-group col-md-3 col-6">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<span class="prepend-title ml-1"> ESTADO </span>
									</div>
								</div>

								<select
									class="form-control center"
									id="cmbEstados"
									data-index="4"
								>
									<option :value="null" selected>Seleccione...</option>
									<option value="Si">VIGENTE</option>
									<option value="No">VENCIDO</option>
								</select>
							</div>

							<div class="col-md-1 col-2 ml-1">
								<button
									class="btn btn-action btn-icon-split mb-1"
									@click="NuevaPromocion()"
								>
									<span class="icon text-white-50">
										<i class="fas fa-plus" style="color: white"></i>
									</span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>
						<div class="card-title mt-1">LISTA DE RESULTADOS</div>

						<table
							class="table"
							id="tblPromociones"
							style="width: 100% !important"
						>
							<thead>
								<tr>
									<th style="min-width: 10px !important">N°</th>
									<th style="max-width: 50px !important">VER</th>
									<th style="min-width: 200px !important">TÍTULO</th>
									<th style="min-width: 75px !important">EMPRESA</th>
									<th style="min-width: 40px !important">HABILITADO</th>
									<th style="min-width: 75px !important">FECHA_REGISTRO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in lista_promociones" :key="index">
									<td align="center">{{ index + 1 }}</td>
									<td class="table-bordered" align="center">
										<button
											class="btn btn-action btn-icon-split"
											@click="verPromocion(item)"
										>
											<span class="icon text-white-50">
												<i class="fas fa-eye" style="color: white"></i>
											</span>
										</button>
									</td>
									<td class="table-bordered" align="left">
										{{ item.titulo_vista }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.empresa }}
									</td>
									<td class="table-bordered center">
										{{ item.habilitado == 1 ? "Si" : "No" }}
									</td>
									<td class="table-bordered" align="center">
										{{ item.fecha_registro }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal" id="mdlNuevaPromocion">
				<div class="modal-content w-40 mdlNuevaPromocion">
					<div class="content" style="display: block">
						<div class="card">
							<div
								class="card-header d-flex align-items-center justify-content-between"
							>
								<strong>{{ title_modal }}</strong>
								<button
									type="button"
									class="btn btn-action"
									style="border-radius: 50%; float: right !important"
									@click="Cerrar"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-body card-block">
								<ul class="nav nav-tabs" id="myTab" role="tablist">
									<li class="nav-item" role="presentation">
										<a
											class="nav-link active tab-title"
											id="datosPromocion1-tab"
											data-toggle="tab"
											href="#datosPromocion1"
											role="tab"
											aria-controls="datosPromocion1"
											aria-selected="false"
											>DATOS VISTA</a
										>
									</li>
									<li class="nav-item" role="presentation">
										<a
											class="nav-link tab-title"
											id="datosPromocion2-tab"
											data-toggle="tab"
											href="#datosPromocion2"
											role="tab"
											aria-controls="datosPromocion2"
											aria-selected="false"
											>DATOS DETALLE</a
										>
									</li>
								</ul>
								<div class="tab-content" id="myTabContent">
									<div
										class="tab-pane fade show active"
										id="datosPromocion1"
										role="tabpanel"
										aria-labelledby="datosPromocion1-tab"
									>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label
													for="txtTitulo_vista"
													class="form-control-label label-title"
													>TÍTULO</label
												>
												<textarea
													class="form-control text-row"
													maxlength="200"
													rows="2"
													v-model="frmPromocion.titulo_vista"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>

											<div class="form-group col-md-6">
												<label
													for="txtSubtitulo_vista"
													class="form-control-label label-title"
													>SUBTÍTULO</label
												>
												<textarea
													class="form-control text-row"
													maxlength="200"
													rows="2"
													v-model="frmPromocion.subtitulo_vista"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>

											<div class="form-group col-md-6">
												<label
													for="txtOferta_vista"
													class="form-control-label label-title"
													>OFERTA</label
												>
												<input
													type="text"
													id="txtOferta_vista"
													name="oferta_vista"
													class="form-control"
													v-model="frmPromocion.oferta_vista"
													:disabled="frmPromocion.modo == 'VER'"
												/>
											</div>

											<div class="form-group col-md-12">
												<label
													for="txtDescripcion"
													class="form-control-label label-title"
													>DESCRIPCIÓN</label
												>
												<textarea
													class="form-control text-row"
													maxlength="350"
													rows="3"
													v-model="frmPromocion.descripcion_vista"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>

											<div class="input-group justify-content-md-center">
												<div class="form-group mt-1" id="pf_contenido_1">
													<label
														for="foto_1"
														class="subir"
														v-if="frmPromocion.imagen_vista == null"
													>
														<i
															class="fas fa-plus-circle foto-icon_1"
															style="font-size: 180px"
														></i>
													</label>
													<input
														id="foto_1"
														type="file"
														accept="image/*"
														style="display: none"
														@change="AgregarPromocion"
														v-if="frmPromocion.imagen_vista == null"
													/>
													<img class="d-block w-100" id="img_1" />
													<button
														class="btn btn-danger btn-icon-split delete_1"
														title="Quitar FOTO"
														style="float: right"
														@click="QuitarFoto('foto_1')"
														v-if="
															frmPromocion.imagen_vista != null &&
															(frmPromocion.modo == 'NUEVA' ||
																frmPromocion.modo == 'EDITAR')
														"
														:disabled="frmPromocion.modo == 'VER'"
													>
														<span class="icon text-white">
															<i class="fas fa-trash-alt"></i>
														</span>
													</button>
												</div>
											</div>
										</div>
									</div>

									<div
										class="tab-pane fade"
										id="datosPromocion2"
										role="tabpanel"
										aria-labelledby="datosPromocion2-tab"
									>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label
													for="txtTitulo_detalle"
													class="form-control-label label-title"
													>TÍTULO</label
												>
												<textarea
													class="form-control text-row"
													maxlength="200"
													rows="2"
													v-model="frmPromocion.titulo_detalle"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>
											<div class="form-group col-md-6">
												<label
													for="txtSubtitulo_detalle"
													class="form-control-label label-title"
													>SUBTÍTULO</label
												>
												<textarea
													class="form-control text-row"
													maxlength="200"
													rows="2"
													v-model="frmPromocion.subtitulo_detalle"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>
											<div class="form-group col-md-6">
												<label
													for="txtVigencia"
													class="form-control-label label-title"
													>VIGENCIA</label
												>
												<textarea
													class="form-control text-row"
													maxlength="200"
													rows="2"
													v-model="frmPromocion.vigencia"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>

											<div class="form-group col-md-6">
												<label
													for="txtEmpresa"
													class="form-control-label label-title"
													>EMPRESA</label
												>
												<input
													type="text"
													id="txtEmpresa"
													name="empresa"
													class="form-control"
													v-model="frmPromocion.empresa"
													:disabled="frmPromocion.modo == 'VER'"
												/>
											</div>
											<div class="form-group col-md-6">
												<label
													for="txtOferta_detalle"
													class="form-control-label label-title"
													>OFERTA</label
												>
												<input
													type="text"
													id="txtOferta_detalle"
													name="oferta_detalle"
													class="form-control"
													v-model="frmPromocion.oferta_detalle"
													:disabled="frmPromocion.modo == 'VER'"
												/>
											</div>

											<div class="form-group col-md-6">
												<label
													for="txtLugar"
													class="form-control-label label-title"
													>LUGAR</label
												>
												<input
													type="text"
													id="txtLugar"
													name="lugar"
													class="form-control"
													v-model="frmPromocion.lugar"
													:disabled="frmPromocion.modo == 'VER'"
												/>
											</div>
											<div class="form-group col-md-5">
												<label
													for="txtStock"
													class="form-control-label label-title"
													>STOCK</label
												>
												<input
													type="text"
													id="txtStock"
													name="stock"
													class="form-control"
													v-model="frmPromocion.stock"
													:disabled="frmPromocion.modo == 'VER'"
												/>
											</div>

											<div class="form-group col-md-12">
												<label
													for="txtDescripcion"
													class="form-control-label label-title"
													>DESCRIPCIÓN</label
												>
												<textarea
													class="form-control text-row"
													maxlength="350"
													rows="3"
													v-model="frmPromocion.descripcion_detalle"
													:disabled="frmPromocion.modo == 'VER'"
												></textarea>
											</div>

											<div class="input-group justify-content-md-center">
												<div class="form-group mt-1" id="pf_contenido_2">
													<label
														for="foto_2"
														class="subir"
														v-if="frmPromocion.imagen_detalle == null"
													>
														<i
															class="fas fa-plus-circle foto-icon_2"
															style="font-size: 180px"
														></i>
													</label>
													<input
														id="foto_2"
														type="file"
														accept="image/*"
														style="display: none"
														@change="AgregarPromocion"
														v-if="frmPromocion.imagen_detalle == null"
													/>
													<img class="d-block w-100" id="img_2" />
													<button
														class="btn btn-danger btn-icon-split delete_2"
														title="Quitar FOTO"
														@click="QuitarFoto('foto_2')"
														v-if="
															frmPromocion.imagen_detalle != null &&
															(frmPromocion.modo == 'NUEVA' ||
																frmPromocion.modo == 'EDITAR')
														"
														:disabled="frmPromocion.modo == 'VER'"
													>
														<span class="icon text-white">
															<i class="fas fa-trash-alt"></i>
														</span>
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>

								<hr />
								<div class="form-row">
									<div class="form-group col-md-4 text-right">
										<div class="form-row">
											<label
												for="chbHabilitado"
												class="form-control-label label-title"
												>HABILITADO:</label
											>
											<div class="checkbox">
												<label
													class="align-middle"
													style="
														font-size: 1em;
														margin-bottom: 0 !important;
														height: 1em !important;
													"
													for="chbHabilitado"
													><input
														type="checkbox"
														id="chbHabilitado"
														:disabled="
															frmPromocion.modo == 'VER' ||
															frmPromocion.modo == 'NUEVA'
														"
														v-model="frmPromocion.habilitado" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
										</div>
									</div>

									<div
										class="form-group col-md-3 offset-5 text-right"
										v-if="
											frmPromocion.modo == 'NUEVA' ||
											frmPromocion.modo == 'EDITAR'
										"
									>
										<button
											class="btn btn-action btn-icon-split"
											title="Guardar"
											@click="Guardar"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i>
											</span>
											<span class="text">GUARDAR</span>
										</button>
									</div>
									<div
										class="form-group col-md-3 offset-5 text-right"
										v-if="frmPromocion.modo == 'VER'"
									>
										<button
											class="btn btn-action btn-icon-split"
											title="Editar"
											@click="Editar"
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
		</div>
	</layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";
import headerCloseModal from "@/Pages/General/Components/header_close_modal.vue";
export default {
	components: { layout, headerClose, headerCloseModal },

	data() {
		return {
			submited: false,
			title_modal: null,
			lista_promociones: [],

			frmPromocion: {
				id: null,
				titulo_vista: null,
				titulo_detalle: null,
				subtitulo_vista: null,
				subtitulo_detalle: null,
				oferta_vista: null,
				oferta_detalle: null,
				descripcion_vista: null,
				descripcion_detalle: null,
				empresa: null,
				lugar: null,
				stock: null,
				vigencia: null,
				habilitado: true,
				imagen_vista: null,
				imagen_detalle: null,
				modo: null,
			},
		};
	},

	validations: {
		frmPromocion: {
			titulo_vista: { required },
			titulo_detalle: { required },
			subtitulo_vista: { required },
			subtitulo_detalle: { required },
			oferta_vista: { required },
			oferta_detalle: { required },
			descripcion_vista: { required },
			descripcion_detalle: { required },
			empresa: { required },
			lugar: { required },
			stock: { required },
			vigencia: { required },
			imagen_vista: { required },
			imagen_detalle: { required },
		},
	},

	mounted() {
		this.ListarRecursos();
		this.TablaPromociones();
	},

	watch: {
		lista_promociones() {
			$("#tblPromociones").DataTable().destroy();
			this.TablaPromociones();
		},
	},

	methods: {
		async ListarRecursos() {
			let self = this;

			return await axios
				.get(route("gen.man.cre.listar_recursos"))
				.then(function (response) {
					self.lista_promociones = response.data.promociones;
				});
		},

		TablaPromociones() {
			this.$nextTick(() => {
				var table = $("#tblPromociones").DataTable({
					scrollY: "350px",
					scrollX: true,
					ordering: false,
					scrollCollapse: true,
					paging: false,
					fixedHeader: true,
					info: true,

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

				$("#cmbEstados").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});
				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},
		AgregarPromocion(e) {
			let img_id = e.target.id;

			let indice = img_id.substr(5, 1);
			let previo = $("#pf_contenido_" + indice + " img");

			previo.remove();

			if (img_id == "foto_1") {
				this.frmPromocion.imagen_vista = e.target.files[0];

				let reader = new FileReader();
				reader.readAsDataURL(e.target.files[0]); // leemos el archivo subido y se lo pasamos a nuestro fileReader
				reader.onload = function () {
					let preview = document.getElementById("pf_contenido_" + indice),
						image = document.createElement("img");

					image.src = reader.result;
					image.id = "img_" + indice;
					image.style.height = "180px";
					image.style.width = "250px";
					image.style.border = "2px solid #000000";
					preview.append(image);
				};
			}

			if (img_id == "foto_2") {
				this.frmPromocion.imagen_detalle = e.target.files[0];

				let reader = new FileReader();
				reader.readAsDataURL(e.target.files[0]); // leemos el archivo subido y se lo pasamos a nuestro fileReader
				reader.onload = function () {
					let preview = document.getElementById("pf_contenido_" + indice),
						image = document.createElement("img");

					image.src = reader.result;
					image.id = "img_" + indice;
					image.style.height = "200px";
					image.style.width = "350px";
					image.style.border = "2px solid #000000";
					preview.append(image);
				};
			}
		},

		NuevaPromocion() {
			this.submited = false;

			this.title_modal = "NUEVA PROMOCIÓN";

			let img = $("#pf_contenido_1 img");
			img.remove();
			let img_1 = $("#pf_contenido_2 img");
			img_1.remove();

			this.frmPromocion.titulo_vista = "";
			this.frmPromocion.titulo_detalle = "";
			this.frmPromocion.subtitulo_vista = "";
			this.frmPromocion.subtitulo_detalle = "";
			this.frmPromocion.oferta_vista = "";
			this.frmPromocion.oferta_detalle = "";
			this.frmPromocion.descripcion_vista = "";
			this.frmPromocion.descripcion_detalle = "";
			this.frmPromocion.empresa = "";
			this.frmPromocion.lugar = "";
			this.frmPromocion.stock = "";
			this.frmPromocion.vigencia = "";
			this.frmPromocion.habilitado = true;
			this.frmPromocion.imagen_vista = null;
			this.frmPromocion.imagen_detalle = null;
			this.frmPromocion.modo = "NUEVA";

			$("#mdlNuevaPromocion").css("display", "block");
			$("#datosPromocion1-tab").tab("show");
		},

		verPromocion(e) {
			let self = this;
			this.submited = false;

			self.title_modal = "VER PROMOCIÓN";

			self.frmPromocion.id = e.id;
			self.frmPromocion.titulo_vista = e.titulo_vista;
			self.frmPromocion.titulo_detalle = e.titulo_detalle;
			self.frmPromocion.subtitulo_vista = e.subtitulo_vista;
			self.frmPromocion.subtitulo_detalle = e.subtitulo_detalle;
			self.frmPromocion.oferta_vista = e.oferta_vista;
			self.frmPromocion.oferta_detalle = e.oferta_detalle;
			self.frmPromocion.descripcion_vista = e.descripcion_vista;
			self.frmPromocion.descripcion_detalle = e.descripcion_detalle;
			self.frmPromocion.empresa = e.empresa;
			self.frmPromocion.lugar = e.lugar;
			self.frmPromocion.stock = e.stock;
			self.frmPromocion.vigencia = e.vigencia;
			self.frmPromocion.habilitado = e.habilitado;
			self.frmPromocion.imagen_vista = e.imagen_vista;
			self.frmPromocion.imagen_detalle = e.imagen_detalle;

			self.frmPromocion.modo = "VER";

			let previo_1 = $("#pf_contenido_1 img");
			previo_1.remove();

			let previo_2 = $("#pf_contenido_2 img");
			previo_2.remove();

			let preview_1 = document.getElementById("pf_contenido_1"),
				image_1 = document.createElement("img");

			image_1.src =
				"/imagenes_server/general/credicheck/promociones/" + e.imagen_vista;
			image_1.style.height = "180px";

			image_1.style.width = "250px";

			image_1.style.border = "2px solid #000000";

			preview_1.append(image_1);

			let preview_2 = document.getElementById("pf_contenido_2"),
				image_2 = document.createElement("img");

			image_2.src =
				"/imagenes_server/general/credicheck/promociones/" + e.imagen_detalle;
			image_2.style.height = "200px";
			image_2.style.width = "350px";
			image_2.style.border = "2px solid #000000";
			// preview_2.innerHTML = "";

			preview_2.append(image_2);

			$("#mdlNuevaPromocion").css("display", "block");
			$("#datosPromocion1-tab").tab("show");
		},
		Cerrar() {
			$("#mdlNuevaPromocion").css("display", "none");
		},

		// ----------guardar--
		Guardar() {
			let self = this;
			this.submited = true;
			if (self.$v.frmPromocion.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay campos por rellenar",
				});
				return false;
			} else {
				Swal.fire({
					title: "GUARDAR CAMBIOS",
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
						this.$inertia.post(
							route("gen.man.cre.promociones.guardar"),
							self.frmPromocion,
							{
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
									self.ListarRecursos();

									$("#mdlNuevaPromocion").css("display", "none");
									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										allowOutsideClick: false,
									});
								},
							}
						);
					} else {
						return false;
					}
				});
			}
		},

		Editar() {
			let self = this;

			self.frmPromocion.modo = "EDITAR";
		},

		QuitarFoto(numero_foto) {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea eliminar esta foto?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					if (numero_foto == "foto_1") {
						self.frmPromocion.imagen_vista = null;
					}
					if (numero_foto == "foto_2") {
						self.frmPromocion.imagen_detalle = null;
					}
					let indice = numero_foto.substr(5, 1);
					let img = $("#pf_contenido_" + indice + " img");
					img.remove();
				}
			});
		},
	},
};
</script>

<style>
.slot-promociones {
	width: 60% !important;
	margin-left: 20% !important;
}

.subir {
	text-align: center;
	vertical-align: middle;
	width: 100% !important;
	height: 100% !important;

	background: #51515381;
	color: rgba(255, 255, 255, 0.575);
	border-radius: 5px;
	cursor: pointer;
}

.subir:hover {
	color: #fff;
	background: #515153a8;
}
.delete_1 {
	position: absolute;
	bottom: 5px;
	z-index: 1000;
}

.delete_2 {
	position: absolute;
	bottom: 5px;
	z-index: 1000;
}
#pf_contenido_1 {
	height: 180px;
	width: 250px;
}
#pf_contenido_2 {
	height: 200px;
	width: 350px;
}
.foto-icon_1 {
	margin-top: 0.5%;
	margin-bottom: 15%;
}
.foto-icon_2 {
	margin-top: 3%;
	margin-bottom: 15%;
}

#mdlNuevaPromocion .modal-content {
	width: 40% !important;
	margin-left: 30% !important;
}
</style>
