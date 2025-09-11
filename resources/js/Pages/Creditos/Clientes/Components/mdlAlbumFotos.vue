<template>
	<div class="">
		<div id="mdlAlbumFotos" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-80">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>ALBÚM DE FOTOS</strong>
							<button
								type="button"
								class="btn btn-green"
								style="border-radius: 50%"
								@click="CerrarModal"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-title">INFORMACIÓN PERSONAL</div>
						<div class="card-body card-block">
							<div class="form-row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-8">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>CLIENTE</span
													>
												</div>
												<input
													type="text"
													class="form-control center"
													style="font-weight: bolder"
													:value="nombre_completo_cliente"
													readonly
												/>
											</div>
										</div>
										<div class="col-md-4 text-right mt-2">
											<div class="form-check">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbVerAntiguas"
													v-model="ver_antiguas"
												/>
												<label class="label-title" for="chbVerAntiguas">
													Ver fotos antiguas
												</label>
											</div>
										</div>

										<div class="col-md-6 mt-2">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>CAT. NUEVAS</span
													>
												</div>
												<select
													class="form-control"
													v-model="categoria_nueva_seleccionada"
													@change="FiltrarFotos"
													:disabled="ver_antiguas"
												>
													<option :value="0" selected disabled>
														Seleccione...
													</option>
													<option
														v-for="(item, index) in categorias_nuevas"
														:key="index"
														:value="item.id"
													>
														{{ item.categoria }}
													</option>
												</select>
											</div>
										</div>
										<div class="col-md-6 mt-2">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>CAT. ANTIGUAS</span
													>
												</div>
												<select
													class="form-control"
													v-model="categoria_antigua_seleccionada"
													@change="FiltrarFotos"
													:disabled="!ver_antiguas"
												>
													<option :value="0" selected disabled>
														Seleccione...
													</option>
													<option
														v-for="(item, index) in categorias_antiguas"
														:key="index"
														:value="item.id"
													>
														{{ item.categoria }}
													</option>
												</select>
											</div>
										</div>
									</div>

									<div style="height: 150px">
										<table
											class="table"
											id="tblAlbumFotos"
											style="width: 100% !important"
										>
											<thead>
												<tr>
													<th style="min-width: 20px !important">N°</th>
													<th style="min-width: 200px !important">
														DESCRIPCIÓN
													</th>
													<th style="min-width: 200px !important">
														COMENTARIO
													</th>
													<th style="min-width: 100px !important">
														FECHA_REGISTRO
													</th>
													<th style="min-width: 100px !important">
														FECHA_REAL
													</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in fotos_filtradas"
													:id="'foto_' + item.id"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
													@click="VerFoto(item, 'album')"
													@dblclick="EnviarACanasta_1(item)"
												>
													<td align="center">
														{{ index + 1 }}
													</td>
													<td>
														{{ item.descripcion }}
													</td>
													<td>
														{{ item.comentario }}
													</td>
													<td align="center">
														{{ JSON.parse(item.datos_creacion).fecha }}
													</td>
													<td align="center">
														{{ item.created_at }}
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<div class="form-row mb-2 mt-2">
										<div class="col-md-12 text-center">
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													@click="SubirFoto"
													title="Subir FOTO"
												>
													<span class="icon text-white">
														<i class="fas fa-upload"></i
													></span>
													<span class="text">SUBIR</span>
												</button>

												<button
													class="btn btn-cancel btn-icon-split"
													@click="Descargar"
													title="Descargar FOTO"
												>
													<span class="icon text-white">
														<i class="fas fa-download"></i
													></span>
													<span class="text">DESCARGAR</span>
												</button>

												<button
													class="btn btn-danger btn-icon-split"
													@click="Eliminar()"
													title="Eliminar FOTO"
													v-if="permiso_eliminar_foto"
												>
													<span class="icon text-white">
														<i class="fas fa-trash-alt"></i>
													</span>
													<span class="text">ELIMINAR</span>
												</button>
											</div>
										</div>
									</div>

									<div
										style="
											height: 300px !important;
											box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
										"
									>
										<img
											:src="
												'/imagenes_server/creditos/clientes/album/' +
												agencia_id +
												'/' +
												+foto_album.imagen.substring(0, 4) +
												'/' +
												foto_album.imagen
											"
											width="100%"
											height="300px"
											v-if="foto_album.imagen != null"
										/>
									</div>
								</div>
								<div class="margen col-md-6">
									<div class="text-center">
										<p class="h5">Fotografías para imprimir</p>
										<div class="form-row" style="margin-top: 16px">
											<div class="col-md-4 col-6" style="height: 32px">
												<input
													class="form-check-input"
													type="radio"
													value="evaluacion"
													name="tipo_impresion"
													id="rdbFormatoEvaluacion"
													v-model="formato_impresion"
												/>
												<label class="label-title" for="rdbFormatoEvaluacion"
													>Formato evaluación</label
												>
											</div>
											<div class="col-md-4 col-6" style="height: 32px">
												<input
													class="form-check-input"
													type="radio"
													value="cliente"
													name="tipo_impresion"
													id="rdbFormatoCliente"
													v-model="formato_impresion"
												/>
												<label class="label-title" for="rdbFormatoCliente"
													>Formato foto cliente</label
												>
											</div>
											<div class="col-md-3">
												<select
													class="form-control center"
													v-if="formato_impresion == 'cliente'"
													v-model="tipo_cliente"
												>
													<option :value="'titular'">TITULAR</option>
													<option :value="'aval'">AVAL</option>
												</select>
											</div>
										</div>
									</div>

									<div style="height: 150px">
										<table
											class="table"
											id="tblCanastaFotos"
											style="width: 100% !important"
										>
											<thead>
												<tr>
													<th style="max-width: 20px !important">N°</th>
													<th style="min-width: 200px !important">
														DESCRIPCIÓN
													</th>
													<th style="min-width: 200px !important">
														COMENTARIO
													</th>
													<th style="min-width: 100px !important">
														FECHA_REGISTRO
													</th>
													<th style="min-width: 100px !important">
														FECHA_REAL
													</th>
													<th style="min-width: 100px !important">CATEGORÍA</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in canasta_impresion"
													:key="index"
													:id="'canasta_' + item.id"
													class="table-bordered"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
													@click="VerFoto(item, 'canasta')"
												>
													<td align="center">
														{{ index + 1 }}
													</td>
													<td align="center" width="20px !important">
														{{ item.descripcion }}
													</td>
													<td align="center">
														{{ item.comentario }}
													</td>
													<td align="center">
														{{ JSON.parse(item.datos_creacion).fecha }}
													</td>
													<td align="center">
														{{ item.created_at }}
													</td>
													<td align="center">
														{{ item.categoria }}
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<div class="form-row m-2">
										<div class="col-md-4 col-6">
											<button
												class="btn btn-action btn-icon-split"
												@click="QuitarDeCanasta"
												title="Quitar de CANASTA"
											>
												<span class="icon text-white">
													<i class="fas fa-times"></i>
												</span>
												<span class="text">QUITAR</span>
											</button>
										</div>

										<div class="col-md-4 offset-md-4 col-6 text-right">
											<button
												class="btn btn-cancel btn-icon-split"
												@click="Imprimir"
												title="Imprimir CANATA"
											>
												<span class="icon text-white">
													<i class="fas fa-print"></i>
												</span>
												<span class="text">IMPRIMIR</span>
											</button>
										</div>
									</div>
									<div
										style="
											height: 300px !important;
											box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial);
										"
									>
										<img
											:src="
												'/imagenes_server/creditos/clientes/album/' +
												agencia_id +
												'/' +
												+foto_canasta.imagen.substring(0, 4) +
												'/' +
												foto_canasta.imagen
											"
											width="100%"
											height="300px"
											v-if="foto_canasta.imagen != null"
										/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- The Modal -->
			<div id="mdlSubirFoto" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-50 mdlSubirFoto">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'SUBIR FOTO'"
								:nombre_modal="'mdlSubirFoto'"
							>
							</headerCloseModal>
							<div class="card-title">INFORMACIÓN</div>
							<div class="card-body card-block">
								<div class="form-row">
									<div class="form-group col-md-7 col-12">
										<label class="form-control-label label-title" id="lblFotos"
											>ADJUNTAR FOTO</label
										>
										<span
											v-if="submited && !$v.frmDatosFoto.foto_nueva.required"
											class="span-error-message"
											>*</span
										>

										<div class="form-group col-md-12" id="af_contenido_1">
											<label
												for="foto_1"
												class="subir"
												v-if="frmDatosFoto.foto_nueva == null"
											>
												<i
													class="fas fa-plus-circle foto-icon"
													style="font-size: 100px"
												></i>
											</label>
											<input
												id="foto_1"
												type="file"
												accept="image/*"
												style="display: none"
												@change="AgregarFoto"
												v-if="frmDatosFoto.foto_nueva == null"
											/>

											<img class="d-block w-100" id="img_1" />
											<button
												class="btn btn-danger btn-icon-split delete"
												title="Quitar FOTO"
												@click="QuitarFoto('foto_1')"
												v-if="frmDatosFoto.foto_nueva != null"
											>
												<span class="icon text-white">
													<i class="fas fa-trash-alt"></i>
												</span>
											</button>
										</div>
									</div>
									<div class="form-group col-md-5 col-12">
										<label class="label-title">CATEGORÍA</label>
										<span
											v-if="submited && !$v.frmDatosFoto.categoria_id.noZero"
											class="span-error-message"
											>*</span
										>
										<select
											class="form-control"
											v-model="frmDatosFoto.categoria_id"
										>
											<option value="0" disabled>Seleccione una opción</option>
											<option
												v-for="(item, index) in categorias_nuevas"
												:key="index"
												:value="item.id"
											>
												{{ item.categoria }}
											</option>
										</select>
										<label class="label-title">DESCRIPCIÓN</label>
										<span
											v-if="submited && !$v.frmDatosFoto.descripcion.required"
											class="span-error-message"
											>*</span
										>
										<textarea
											class="form-control mayus text-row"
											maxlength="200"
											rows="3"
											v-model="frmDatosFoto.descripcion"
										>
										</textarea>
										<label class="label-title">COMENTARIO</label>
										<span
											v-if="submited && !$v.frmDatosFoto.comentario.required"
											class="span-error-message"
											>*</span
										>
										<textarea
											class="form-control mayus text-row"
											maxlength="200"
											rows="3"
											v-model="frmDatosFoto.comentario"
										>
										</textarea>
									</div>
									<!-- <div class="form-group col-md-6 col-6">

										</div>
										<div class="form-group col-md-6 col-6">

										</div> -->
								</div>

								<hr />
								<div class="text-right">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar()"
										title="Guardar FOTO"
									>
										<span class="icon text-white">
											<i class="fas fa-save"></i>
										</span>
										<span class="text">GUARDAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<rptCanastaFotos ref="rptCanastaFotos" :agencia_id="agencia_id">
			</rptCanastaFotos>
		</div>
	</div>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import rptCanastaFotos from "@/Pages/Creditos/Clientes/Reports/rptCanastaFotos.vue";

const noZero = (value) => value != 0;
export default {
	components: {
		headerClose,
		headerCloseModal,
		rptCanastaFotos,
	},

	data() {
		return {
			submited: false,

			agencia_id: 0,
			cliente_id: null,
			categorias: [],
			datos_cliente: null,
			fotos_cliente: [],

			ver_antiguas: false,
			categorias_nuevas: [],
			categoria_nueva_seleccionada: 0,
			categoria_antigua_seleccionada: 0,
			categorias_antiguas: [],
			fotos_filtradas: [],
			foto_album: {
				año: null,
				imagen: null,
			},
			foto_canasta: {
				año: null,
				imagen: null,
			},

			frmDatosFoto: {
				cliente_id: null,
				categoria_id: 0,
				descripcion: null,
				comentario: null,
				foto_nueva: null,
			},

			canasta_impresion: [],

			formato_impresion: "evaluacion",
			tipo_cliente: "titular",

			arreglo_six: [],
			arreglo_two: [],
		};
	},
	validations: {
		frmDatosFoto: {
			categoria_id: { noZero },
			descripcion: { required },
			comentario: { required },
			foto_nueva: { required },
		},
	},
	computed: {
		permiso_eliminar_foto() {
			let resultado = false;
			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CLIENTES/ELIMINAR_FOTO"
				);

			if (permiso_detalle.length != 0) {
				let acceso_agencias = permiso_detalle[0].acceso_agencias;

				if (acceso_agencias != null) {
					acceso_agencias = JSON.parse(acceso_agencias);

					let agencia_autorizada = acceso_agencias.filter(
						(item) => item.agencia_id == this.agencia_id
					);

					if (agencia_autorizada.length != 0) {
						resultado = true;
					}
				}
			}
			return resultado;
		},
		nombre_completo_cliente() {
			if (this.datos_cliente != null) {
				return (
					this.datos_cliente.apellido_paterno +
					" " +
					this.datos_cliente.apellido_materno +
					" " +
					this.datos_cliente.nombres
				);
			} else {
				return null;
			}
		},
	},
	watch: {
		fotos_filtradas() {
			$("#tblAlbumFotos").DataTable().destroy();
			this.TablaAlbumFotos();
		},

		canasta_impresion() {
			$("#tblCanastaFotos").DataTable().destroy();
			this.TablaCanastaFotos();
		},
		categorias(value) {
			this.categorias_nuevas = value.filter((item) => item.nuevo == 1);
			this.categorias_antiguas = value.filter((item) => item.nuevo == 0);
		},
	},
	mounted() {
		this.TablaAlbumFotos();
		this.TablaCanastaFotos();
	},
	methods: {
		Resetear() {
			this.categorias = [];
			this.datos_cliente = null;
			this.fotos_cliente = [];
			this.fotos_filtradas = [];
			this.canasta_impresion = [];
			this.ver_antiguas = false;
			this.categoria_nueva_seleccionada = 0;
			this.categoria_antigua_seleccionada = 0;

			this.foto_album.año = null;
			this.foto_album.imagen = null;

			this.foto_canasta.año = null;
			this.foto_canasta.imagen = null;
		},
		async ActualizarInformacion() {
			let object = {
				cliente_id: this.cliente_id,
				agencia_id: this.agencia_id,
			};

			await axios.get(route("cli.album_fotos", object)).then((response) => {
				this.categorias = response.data.categorias;
				this.datos_cliente = response.data.datos_cliente;
				this.fotos_cliente = response.data.fotos_cliente;
			});
		},
		TablaAlbumFotos() {
			this.$nextTick(() => {
				$("#tblAlbumFotos").DataTable({
					scrollY: "100px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
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
		TablaCanastaFotos() {
			this.$nextTick(() => {
				$("#tblCanastaFotos").DataTable({
					scrollY: "100px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
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

		async FiltrarFotos() {
			let categoria_id = 0;
			if (this.ver_antiguas) {
				categoria_id = this.categoria_antigua_seleccionada;
			} else {
				categoria_id = this.categoria_nueva_seleccionada;
			}

			this.fotos_filtradas = this.fotos_cliente.filter(
				(item) => item.categoria_id == categoria_id
			);
			this.foto_album.año = null;
			this.foto_album.imagen = null;
		},

		SubirFoto() {
			this.submited = false;

			let img = $("#af_contenido_1 img");
			img.remove();
			this.frmDatosFoto.foto_nueva = null;
			this.frmDatosFoto.categoria_id = 0;
			this.frmDatosFoto.descripcion = null;
			this.frmDatosFoto.comentario = null;

			$("#mdlSubirFoto").css("display", "block");
		},

		AgregarFoto(e) {
			let img_id = e.target.id;

			let indice = img_id.substr(5, 1);
			let previo = $("#af_contenido_" + indice + " img");

			previo.remove();

			if (img_id == "foto_1") {
				this.frmDatosFoto.foto_nueva = e.target.files[0];
			}

			let reader = new FileReader();
			reader.readAsDataURL(e.target.files[0]); // leemos el archivo subido y se lo pasamos a nuestro fileReader
			reader.onload = function () {
				let preview = document.getElementById("af_contenido_" + indice),
					image = document.createElement("img");

				image.src = reader.result;
				image.id = "img_" + indice;
				image.style.height = "100%";
				image.style.width = "100%";
				image.style.border = "1px solid #ffff";

				preview.append(image);
			};
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
						self.frmDatosFoto.foto_nueva = null;
					}
					let indice = numero_foto.substr(5, 1);
					let img = $("#af_contenido_" + indice + " img");
					img.remove();
				}
			});
		},

		Guardar() {
			let self = this;
			this.submited = true;

			if (this.$v.frmDatosFoto.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos.",
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿Desea guardar la FOTO?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				showLoaderOnConfirm: true,
				preConfirm: () => {
					let data = new FormData();
					data.append("dni", self.datos_cliente.dni);
					data.append("cliente_id", self.datos_cliente.id);
					data.append("agencia_id", self.agencia_id);
					data.append("categoria_id", self.frmDatosFoto.categoria_id);
					data.append("descripcion", self.frmDatosFoto.descripcion);
					data.append("comentario", self.frmDatosFoto.comentario);
					data.append("foto_nueva", self.frmDatosFoto.foto_nueva);

					return axios
						.post(route("cli.album_fotos.guardar_foto"), data)
						.then(async (response) => {
							await self.ActualizarInformacion();
							self.FiltrarFotos();
							$("#mdlSubirFoto").css("display", "none");
						})
						.catch((error) => {
							Swal.showValidationMessage(`Ha ocurrido un error: ${error}`);
						});
				},
				allowOutsideClick: () => !Swal.isLoading(),
			}).then((result) => {
				if (result.isConfirmed) {
					Swal.fire({
						icon: "success",
						title: "¡ÉXITO!",
						timer: 1200,
						showConfirmButton: false,
					});
				}
			});
		},

		VerFoto(foto, tipo) {
			if (tipo == "album") {
				this.foto_album.año = foto.imagen.substring(0, 4);
				this.foto_album.imagen = foto.imagen;
			} else if (tipo == "canasta") {
				this.foto_canasta.año = foto.imagen.substring(0, 4);
				this.foto_canasta.imagen = foto.imagen;
			}
		},

		Descargar() {
			let row = document
				.getElementById("tblAlbumFotos")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione una foto",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("foto_", "");

				let imagen = this.fotos_cliente.filter((item) => item.id == id)[0]
					.imagen;

				let source =
					"/imagenes_server/creditos/clientes/album/" +
					this.agencia_id +
					"/" +
					imagen.substring(0, 4) +
					"/" +
					imagen;
				axios
					.get(source, { responseType: "blob" })
					.then((response) => {
						const blob = new Blob([response.data], {
							type: response.data.type,
						});
						const link = document.createElement("a");
						link.href = URL.createObjectURL(blob);
						link.download = imagen;
						link.click();
						URL.revokeObjectURL(link.href);
					})
					.catch(console.error);
			}
		},

		Eliminar() {
			let self = this;
			let row = document
				.getElementById("tblAlbumFotos")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione una foto",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("foto_", "");

				Swal.fire({
					icon: "question",
					text: "¿Desea eliminar la FOTO?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
					showLoaderOnConfirm: true,
					preConfirm: () => {
						let data = new FormData();
						data.append("foto_id", id);
						data.append("cliente_id", self.datos_cliente.id);
						data.append("agencia_id", self.agencia_id);

						return axios
							.post(route("cli.album_fotos.eliminar"), data)
							.then(async (response) => {
								self.foto_album.año = null;
								self.foto_album.imagen = null;
								await self.ActualizarInformacion();
								self.FiltrarFotos();
							})
							.catch((error) => {
								Swal.showValidationMessage(`Ha ocurrido un error: ${error}`);
							});
					},
					allowOutsideClick: () => !Swal.isLoading(),
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							icon: "success",
							title: "¡ÉXITO!",
							timer: 1200,
							showConfirmButton: false,
						});
					}
				});
			}
		},

		EnviarACanasta_1(item) {
			let id = item.id;
			let existe = this.canasta_impresion.some((element) => element.id == id);

			if (existe) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "La foto ya está en la canasta",
				});
				return false;
			} else {
				this.canasta_impresion.push(item);
			}
		},

		EnviarACanasta_2() {
			let row = document
				.getElementById("tblAlbumFotos")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Seleccione una foto",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("foto_", "");
				let foto = this.fotos_cliente.filter((item) => item.id == id)[0];

				let existe = this.canasta_impresion.some((element) => element.id == id);

				if (existe) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "La foto ya está en la canasta",
					});
					return false;
				} else {
					this.canasta_impresion.push(foto);
				}
			}
		},

		QuitarDeCanasta() {
			let row = document
				.getElementById("tblCanastaFotos")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Seleccione una foto",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("canata_", "");
				let index = this.canasta_impresion.findIndex((item) => item.id == id);
				this.canasta_impresion.splice(index, 1);
				this.foto_canasta.imagen = null;
			}
		},

		async Imprimir() {
			let self = this;
			if (this.canasta_impresion.length == 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Agregue fotos a la CANASTA.",
					allowOutsideClick: true,
				});
				return false;
			}

			let fecha_hora_actual = await this.$parent.fecha_hora_actual(
				this.agencia_id
			);

			let rptCanastaFotos = this.$refs.rptCanastaFotos;
			async function EnviarDatos() {
				rptCanastaFotos.cliente = self.nombre_completo_cliente;
				rptCanastaFotos.formato_impresion = self.formato_impresion;
				rptCanastaFotos.tipo_cliente = self.tipo_cliente;
				rptCanastaFotos.fotos = self.canasta_impresion;
				rptCanastaFotos.fecha_hora_actual = fecha_hora_actual;
			}

			EnviarDatos(rptCanastaFotos).then(() => {
				$("#rptCanastaFotos").css("display", "block");

				var css = "@page { size: landscape ; }",
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

				$("#rptCanastaFotos").print();

				head.removeChild(style);

				$("#rptCanastaFotos").css("display", "none");
			});
		},
		CerrarModal() {
			$("#mdlAlbumFotos").css("display", "none");
		},
	},
};
</script>



<style lang="css">
.prepend-title {
	font-size: 8.5px;
	color: var(--plomoOscuroEmpresarial);
}

.mdlSubirFoto {
	margin-top: 8%;
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

.delete {
	position: absolute;
	bottom: 5px;
	right: 20px;
	z-index: 1000;
}

#af_contenido_1 {
	width: 100%;
	height: 250px;
}

.foto-icon {
	margin-top: 15%;
	margin-bottom: 15%;
}

@media only screen and (max-width: 900px) {
	#previzualizar {
		width: 400px !important;
		height: 200px !important;
	}

	.margen {
		margin-top: 20px;
	}
}
</style>
