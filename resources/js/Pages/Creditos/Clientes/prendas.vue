<template>
	<layout ref="layout">
		<div class="slot_body slot-prendas" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'PRENDAS'"></headerClose>

					<div class="card-title">INFORMACIÓN DEL CLIENTE</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-6 mb-1 mt-1">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">TITULAR</span>
								</div>
								<input
									type="text"
									class="form-control input-information"
									:value="cliente"
									disabled
								/>
							</div>

							<div class="input-group col-md-3 mb-1 mt-1 col-7">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">ASESOR</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="datos_cliente.usuario_asesor"
									disabled
								/>
							</div>

							<div class="input-group col-md-3 mb-1 mt-1 col-5">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">EXP.</span>
								</div>

								<input
									type="text"
									class="form-control center input-information"
									:value="
										datos_cliente.codigo_expediente == null
											? '-'
											: datos_cliente.codigo_expediente
									"
									disabled
								/>
							</div>
						</div>
					</div>
					<div class="card-title">LISTA DE PRENDAS</div>
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link active tab-title"
									id="inventario-tab"
									data-toggle="tab"
									href="#inventario"
									role="tab"
									aria-controls="inventario"
									aria-selected="true"
									>INVENTARIO ACTUAL</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="datos-tab"
									data-toggle="tab"
									href="#datos"
									role="tab"
									aria-controls="datos"
									aria-selected="false"
									:hidden="modo == 'BLOQUEADO'"
									>DATOS DEL BIEN</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="foto-tab"
									data-toggle="tab"
									href="#foto"
									role="tab"
									aria-controls="foto"
									aria-selected="false"
									:hidden="modo == 'BLOQUEADO'"
									>FOTOS</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="inventario"
								role="tabpanel"
								aria-labelledby="inventario-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-4">
										<div class="form-check mt-2">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbDisponibles"
												v-model="solo_disponibles"
												style="font-size: 1rem"
											/>
											<label
												class="form-check-label label-title"
												for="chbDisponibles"
												style="font-size: 1rem"
											>
												MOSTRAR SÓLO DISPONIBLES
											</label>
										</div>
									</div>
									<div class="col-md-4">
										<label class="label-title"> VALORIZACIÓN TOTAL </label>

										<label
											class="form-control-label"
											style="font-size: 1.2rem; color: var(--colorAlto)"
										>
											S/ {{ parseFloat(total).toFixed(2) }}</label
										>
									</div>
									<div class="col-md-4">
										<div class="alineamiento text-right">
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													@click="Nuevo"
												>
													<span class="icon text-white">
														<i class="fas fa-plus"></i
													></span>
													<span class="text">NUEVO</span>
												</button>
											</div>
										</div>
									</div>
								</div>

								<table class="table" id="tblPrendas" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 100px !important">ACCIONES</th>
											<th style="min-width: 50px !important">ENTREGA</th>
											<th style="min-width: 50px !important">DISP.</th>
											<th style="min-width: 200px !important">DESCRIPCIÓN</th>
											<th style="min-width: 70px !important">CANT.</th>
											<th style="min-width: 100px !important">VALOR</th>
											<th style="min-width: 150px !important">MARCA</th>
											<th style="min-width: 150px !important">MODELO</th>
											<th style="min-width: 150px !important">SERIE</th>
											<th style="min-width: 100px !important">ESTADO</th>
											<th style="min-width: 200px !important">COMENTARIO</th>
											<th style="min-width: 100px !important">
												FECHA_REGISTRO
											</th>
											<th style="min-width: 100px !important">
												USUARIO_REGISTRO
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_datos"
											:key="index"
											class="table-bordered"
										>
											<td align="center">
												<div class="btn-group" role="group">
													<button
														class="btn btn-cancel btn-icon-split"
														title="Editar PRENDA"
														@click="VerPrenda(item, 'EDITAR')"
														v-if="item.disponible == 1"
													>
														<span class="icon text-white">
															<i class="fas fa-edit"></i>
														</span>
													</button>
													<!-- <button
														class="btn btn-danger btn-icon-split"
														title="Eliminar PRENDA"
														@click="Eliminar(item.id)"
														v-if="item.disponible == 1"
													>
														<span class="icon text-white">
															<i class="fas fa-trash"></i>
														</span>
													</button> -->
													<button
														class="btn btn-cancel btn-icon-split"
														title="Ver PRENDA"
														@click="VerPrenda(item, 'VER')"
														v-if="item.disponible == 0"
													>
														<span class="icon text-white">
															<i class="fas fa-eye"></i>
														</span>
													</button>
												</div>
											</td>
											<td align="center">
												<div
													class="page__toggle"
													v-show="item.disponible == 1 && item.entregado == 0"
												>
													<label class="toggle">
														<input
															class="toggle__input"
															type="checkbox"
															:id="'chbPrenda_' + index"
															:value="item"
															v-model="prendas_seleccionadas"
														/>
														<span class="toggle__label">
															<span class="toggle__text"></span>
														</span>
													</label>
												</div>

												<button
													class="btn btn-cancel btn-icon-split"
													title="Ver ACTA"
													@click="VerActa(item)"
													v-if="item.entregado == 1"
												>
													<span class="icon text-white">
														<i class="fas fa-eye"></i>
													</span>
												</button>
											</td>

											<td
												align="center"
												:class="[
													item.disponible == 1 ? 'disponible' : 'no-disponible',
												]"
											>
												{{ item.disponible == 1 ? "SI" : "NO" }}
											</td>
											<td>
												{{ item.descripcion }}
											</td>
											<td align="center">
												{{ item.cantidad }}
											</td>
											<td align="right">
												S/ {{ parseFloat(item.precio_actual).toFixed(2) }}
											</td>
											<td>
												{{ item.marca }}
											</td>
											<td>
												{{ item.modelo == null ? "-" : item.modelo }}
											</td>
											<td>
												{{ item.serie == null ? "-" : item.serie }}
											</td>
											<td align="center">
												{{ item.estado }}
											</td>

											<td>
												{{ item.comentario == null ? "-" : item.comentario }}
											</td>
											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="center">
												{{ item.usuario_registro }}
											</td>
										</tr>
									</tbody>
								</table>
								<hr />
								<div class="form-row">
									<div class="input-group col-md-6 col-12">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title"
												v-if="acta_entrega == null"
												>SUBIR ACTA</span
											>
											<button
												class="btn btn-action btn-icon-split"
												@click="GuardarActa"
												v-if="acta_entrega != null"
											>
												<span class="icon text-white">
													<i class="fas fa-save"></i
												></span>
											</button>
										</div>
										<div class="input-group-append">
											<input
												class="btn btn-cancel"
												type="file"
												id="fotoActaEntrega"
												accept="image/*"
												@change="AgregarActa"
												:disabled="prendas_seleccionadas.length == 0"
											/>
										</div>
										<div class="input-group-append">
											<button
												class="btn btn-danger btn-icon-split"
												@click="QuitarActa"
												v-if="acta_entrega != null"
											>
												<span class="icon text-white">
													<i class="fas fa-trash"></i
												></span>
											</button>
										</div>
									</div>

									<!-- <button
										class="btn btn-cancel btn-icon-split"
										@click="SubirActa"
										:disabled="prendas_seleccionadas.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-image"></i
										></span>
										<span class="text">SUBIR ACTA</span>
									</button> -->
									<div class="alineamiento col-md-6 text-right col-12">
										<button
											class="btn btn-action btn-icon-split"
											@click="GenerarActa"
											:disabled="prendas_seleccionadas.length == 0"
										>
											<span class="icon text-white">
												<i class="fas fa-file-word"></i
											></span>
											<span class="text">GENERAR ACTA</span>
										</button>
									</div>
								</div>
							</div>
							<div
								class="tab-pane fade"
								id="datos"
								role="tabpanel"
								aria-labelledby="datos-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-2 col-3">
										<label class="label-title">CANTIDAD</label>

										<input
											type="text"
											class="form-control center"
											v-model.number="frmDatosPrenda.cantidad"
											readonly
										/>
									</div>
									<div class="form-group col-md-7 col-9">
										<span
											v-if="submited && !$v.frmDatosPrenda.descripcion.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">DESCRIPCIÓN</label>

										<textarea
											class="form-control mayus"
											rows="2"
											v-model="frmDatosPrenda.descripcion"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosPrenda.marca.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">MARCA</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosPrenda.marca"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">MODELO</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosPrenda.modelo"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">SERIE</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosPrenda.serie"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosPrenda.color.required"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">COLOR</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosPrenda.color"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="submited && !$v.frmDatosPrenda.estado.noZero"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">ESTADO</label>

										<select
											class="form-control center"
											v-model="frmDatosPrenda.estado"
											:disabled="deshabilitado"
										>
											<option :value="0" disabled>Seleccione...</option>
											<option value="EXCELENTE">EXCELENTE</option>
											<option value="MUY BUENO">MUY BUENO</option>
											<option value="BUENO">BUENO</option>
											<option value="REGULAR">REGULAR</option>
											<option value="DETERIORADO">DETERIORADO</option>
										</select>
									</div>

									<div class="form-group col-md-3 col-6">
										<span
											v-if="
												submited && !$v.frmDatosPrenda.fecha_compra.required
											"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">FECHA COMPRA</label>

										<input
											type="date"
											class="form-control center"
											v-model="frmDatosPrenda.fecha_compra"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">N° COMPROBANTE</label>

										<input
											type="text"
											class="form-control mayus"
											v-model="frmDatosPrenda.numero_comprobante"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="
												submited && !$v.frmDatosPrenda.precio_compra.required
											"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">PRECIO COMPRA (S/)</label>

										<input
											type="number"
											min="0"
											step="0.1"
											class="form-control center bolder"
											style="font-size: 15px"
											v-model.number="frmDatosPrenda.precio_compra"
											@change="Redondear"
											name="precio_compra"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<span
											v-if="
												submited && !$v.frmDatosPrenda.precio_actual.required
											"
											class="span-error-message"
										>
											*
										</span>
										<label class="label-title">PRECIO ACTUAL (S/)</label>

										<input
											type="number"
											min="0"
											step="0.1"
											class="form-control center bolder"
											style="font-size: 15px"
											v-model.number="frmDatosPrenda.precio_actual"
											name="precio_actual"
											@change="Redondear"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										/>
									</div>
									<div class="form-group col-md-12">
										<label class="label-title">COMENTARIO</label>

										<textarea
											class="form-control mayus text-row"
											rows="3"
											v-model="frmDatosPrenda.comentario"
											:disabled="deshabilitado"
											autocomplete="off"
											@focus="hidenav()"
											@blur="shownav()"
										></textarea>
									</div>

									<div class="form-group col-md-4" v-if="modo == 'EDITAR'">
										<div class="form-check">
											<label
												class="form-check-label bolder"
												for="flexCheckChecked"
												style="font-size: 15px !important"
											>
												DISPONIBLE:
												{{ frmDatosPrenda.disponible == 1 ? "SI" : "NO" }}
											</label>
										</div>
									</div>
								</div>
								<hr />
								<div class="text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="Guardar"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i
											></span>
											<span class="text">GUARDAR</span>
										</button>
									</div>
								</div>
							</div>
							<div
								class="tab-pane fade"
								id="foto"
								role="tabpanel"
								aria-labelledby="foto-tab"
							>
								<span
									v-if="
										submited &&
										frmDatosPrenda.fotos.foto_1 == null &&
										frmDatosPrenda.fotos.foto_2 == null &&
										frmDatosPrenda.fotos.foto_3 == null &&
										frmDatosPrenda.fotos.foto_4 == null
									"
									class="span-error-message"
									style="font-size: var(--tamañoLetraLabels)"
								>
									* Debe agregar al menos una imagen
								</span>
								<div class="form-row">
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_1"
									>
										<label
											for="foto_1"
											class="subir"
											v-if="frmDatosPrenda.fotos.foto_1 == null"
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
											v-if="frmDatosPrenda.fotos.foto_1 == null"
											:disabled="deshabilitado"
										/>

										<img class="d-block w-100" id="img_1" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_1')"
											v-if="frmDatosPrenda.fotos.foto_1 != null"
											:disabled="deshabilitado"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>

									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_2"
									>
										<label
											for="foto_2"
											class="subir"
											v-if="frmDatosPrenda.fotos.foto_2 == null"
											:disabled="deshabilitado"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_2"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosPrenda.fotos.foto_2 == null"
											:disabled="deshabilitado"
										/>

										<img class="d-block w-100" id="img_2" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_2')"
											v-if="frmDatosPrenda.fotos.foto_2 != null"
											:disabled="deshabilitado"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_3"
									>
										<label
											for="foto_3"
											class="subir"
											v-if="frmDatosPrenda.fotos.foto_3 == null"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_3"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosPrenda.fotos.foto_3 == null"
											:disabled="deshabilitado"
										/>

										<img class="d-block w-100" id="img_3" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_3')"
											v-if="frmDatosPrenda.fotos.foto_3 != null"
											:disabled="deshabilitado"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_4"
									>
										<label
											for="foto_4"
											class="subir"
											v-if="frmDatosPrenda.fotos.foto_4 == null"
										>
											<i
												class="fas fa-plus-circle foto-icon"
												style="font-size: 100px"
											></i>
										</label>
										<input
											id="foto_4"
											type="file"
											accept="image/*"
											style="display: none"
											@change="AgregarFoto"
											v-if="frmDatosPrenda.fotos.foto_4 == null"
											:disabled="deshabilitado"
										/>

										<img class="d-block w-100" id="img_4" />
										<button
											class="btn btn-danger btn-icon-split delete"
											title="Quitar FOTO"
											@click="QuitarFoto('foto_4')"
											v-if="frmDatosPrenda.fotos.foto_4 != null"
											:disabled="deshabilitado"
										>
											<span class="icon text-white">
												<i class="fas fa-trash-alt"></i>
											</span>
										</button>
									</div>
								</div>

								<hr />
								<div class="text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-action btn-icon-split"
											@click="Guardar"
										>
											<span class="icon text-white">
												<i class="fas fa-save"></i
											></span>
											<span class="text">GUARDAR</span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<rptActaEntrega ref="rptActaEntrega"> </rptActaEntrega>
			<div id="mdlActaEntrega" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-40 mdlActaEntrega">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								ref="headerCloseModal"
								:titulo_modal="'ACTA DE ENTREGA'"
								:nombre_modal="'mdlActaEntrega'"
							>
							</headerCloseModal>

							<div class="card-body card-block">
								<div style="width: 100% !important; height: 600px !important">
									<img
										:src="acta_subida.ruta"
										:ref="acta_subida.nombre"
										width="100% !important"
										height="100% !important"
									/>
								</div>
								<hr />
								<div class="text-center">
									<button
										class="btn btn-action btn-icon-split"
										@click="DescargarActa"
									>
										<span class="icon text-white">
											<i class="fas fa-download"></i
										></span>
										<span class="text">DESCARGAR</span>
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
import rptActaEntrega from "@/Pages/Creditos/Clientes/Reports/rptActaEntrega.vue";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
		rptActaEntrega,
	},
	props: {
		cliente_id: Number,
		agencia_id: Number,
		datos_cliente: Object,
		datos_prendas: Array,
	},
	data() {
		return {
			submited: false,
			modo: "BLOQUEADO",
			lista_datos: this.datos_prendas,
			solo_disponibles: true,
			acta_entrega: null,
			acta_subida: {
				ruta: null,
				nombre: null,
			},
			frmDatosPrenda: {
				id: 0,
				cantidad: 1,
				descripcion: null,
				marca: null,
				modelo: null,
				serie: null,
				color: null,
				estado: 0,
				fecha_compra: null,
				numero_comprobante: null,
				precio_compra: parseFloat(0).toFixed(2),
				precio_actual: parseFloat(0).toFixed(2),
				disponible: 0,
				comentario: null,
				fotos: { foto_1: null, foto_2: null, foto_3: null, foto_4: null },
				fotos_nuevas: {
					nuevo_1: false,
					nuevo_2: false,
					nuevo_3: false,
					nuevo_4: false,
				},
			},
			contenido_foto: {
				border: "1px solid black",
				width: "90%",
				height: "250px",
				"margin-left": "5%",
			},

			total: 0.0,
			prendas_seleccionadas: [],
		};
	},

	computed: {
		cliente() {
			let cliente =
				this.datos_cliente.apellido_paterno +
				" " +
				this.datos_cliente.apellido_materno +
				" " +
				this.datos_cliente.nombres;
			return cliente;
		},
		deshabilitado() {
			if (this.modo == "BLOQUEADO" || this.modo == "VER") {
				return true;
			} else {
				return false;
			}
		},
	},
	validations: {
		frmDatosPrenda: {
			descripcion: { required },
			marca: { required },
			color: { required },
			estado: { noZero },
			fecha_compra: { required },
			precio_compra: { required },
			precio_actual: { required },
		},
	},
	watch: {
		lista_datos() {
			this.RecrearTabla();
		},
	},
	mounted() {
		this.TablaBienes();
	},
	methods: {
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
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value) {
				valor = e.target.value;
			}

			if (e.target.name == "precio_compra") {
				this.frmDatosPrenda.precio_compra = this.roundTo(
					valor,
					numero_decimales
				);
			} else if (e.target.name == "precio_actual") {
				this.frmDatosPrenda.precio_actual = this.roundTo(
					valor,
					numero_decimales
				);
			}
		},
		TablaBienes() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblPrendas").DataTable({
					scrollY: "250px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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

				$("#chbDisponibles").change(function () {
					self.total = 0;
					if (self.solo_disponibles) {
						table.column(2).search("SI").draw();
						self.lista_datos.forEach((element) => {
							if (element.disponible == true) {
								self.total += parseFloat(element.precio_actual);
							}
						});
					} else {
						table.column(2).search("").draw();
						self.lista_datos.forEach((element) => {
							self.total += parseFloat(element.precio_actual);
						});
					}
				});

				self.total = 0;
				if (self.solo_disponibles) {
					table.column(2).search("SI").draw();
					self.lista_datos.forEach((element) => {
						if (element.disponible == true) {
							self.total += parseFloat(element.precio_actual);
						}
					});
				} else {
					table.column(2).search("").draw();
					self.lista_datos.forEach((element) => {
						self.total += parseFloat(element.precio_actual);
					});
				}
			});
		},
		RecrearTabla() {
			$("#tblPrendas").DataTable().destroy();
			this.TablaBienes();
		},
		ResetearFormulario() {
			let formulario = this.frmDatosPrenda;
			formulario.id = 0;
			formulario.cantidad = 1;
			formulario.descripcion = null;
			formulario.marca = null;
			formulario.modelo = null;
			formulario.serie = null;
			formulario.color = null;
			formulario.estado = 0;
			formulario.fecha_compra = null;
			formulario.numero_comprobante = null;
			formulario.precio_compra = parseFloat(0).toFixed(2);
			formulario.precio_actual = parseFloat(0).toFixed(2);
			formulario.disponible = 0;
			formulario.comentario = null;

			for (let index = 1; index <= 4; index++) {
				let img = $("#contenido_" + index + " img");
				img.remove();
			}
			formulario.fotos.foto_1 = null;
			formulario.fotos_nuevas.nuevo_1 = false;
			formulario.fotos.foto_2 = null;
			formulario.fotos_nuevas.nuevo_2 = false;
			formulario.fotos.foto_3 = null;
			formulario.fotos_nuevas.nuevo_3 = false;
			formulario.fotos.foto_4 = null;
			formulario.fotos_nuevas.nuevo_4 = false;
		},
		Nuevo() {
			this.ResetearFormulario();
			this.modo = "NUEVO";
			$("#datos-tab").tab("show");
		},
		VerPrenda(bien, modo) {
			this.modo = modo;

			this.frmDatosPrenda.id = bien.id;
			this.frmDatosPrenda.cantidad = bien.cantidad;
			this.frmDatosPrenda.descripcion = bien.descripcion;
			this.frmDatosPrenda.marca = bien.marca;
			this.frmDatosPrenda.modelo = bien.modelo;
			this.frmDatosPrenda.serie = bien.serie;
			this.frmDatosPrenda.color = bien.color;
			this.frmDatosPrenda.estado = bien.estado;
			this.frmDatosPrenda.fecha_compra = bien.fecha_compra;
			this.frmDatosPrenda.numero_comprobante = bien.numero_comprobante;
			this.frmDatosPrenda.precio_compra = parseFloat(
				bien.precio_compra
			).toFixed(2);
			this.frmDatosPrenda.precio_actual = parseFloat(
				bien.precio_actual
			).toFixed(2);
			this.frmDatosPrenda.disponible = bien.disponible;
			this.frmDatosPrenda.comentario = bien.comentario;
			this.frmDatosPrenda.fotos = JSON.parse(bien.fotos);

			for (let index = 1; index <= 4; index++) {
				let img = $("#contenido_" + index + " img");
				img.remove();

				let foto = null;

				if (index == 1) {
					foto = this.frmDatosPrenda.fotos.foto_1;
				} else if (index == 2) {
					foto = this.frmDatosPrenda.fotos.foto_2;
				} else if (index == 3) {
					foto = this.frmDatosPrenda.fotos.foto_3;
				} else if (index == 4) {
					foto = this.frmDatosPrenda.fotos.foto_4;
				}

				if (foto != null) {
					let preview = document.getElementById("contenido_" + index),
						image = document.createElement("img");

					image.src =
						"/imagenes_server/creditos/clientes/prendas/fotos/" +
						this.agencia_id +
						"/" +
						foto.substring(0, 4) +
						"/" +
						foto;

					image.id = "img_" + index;
					image.style.height = "100%";
					image.style.width = "100%";
					image.style.border = "1px solid #ffff";
					preview.append(image);
				}
			}

			this.frmDatosPrenda.fotos_nuevas.nuevo_1 = false;
			this.frmDatosPrenda.fotos_nuevas.nuevo_2 = false;
			this.frmDatosPrenda.fotos_nuevas.nuevo_3 = false;
			this.frmDatosPrenda.fotos_nuevas.nuevo_4 = false;

			$("#datos-tab").tab("show");
		},
		AgregarFoto(e) {
			let img_id = e.target.id;

			let id = img_id.substr(5, 1);
			let previo = $("#contenido_" + id + " img");

			previo.remove();

			if (img_id == "foto_1") {
				this.frmDatosPrenda.fotos.foto_1 = e.target.files[0];
				this.frmDatosPrenda.fotos_nuevas.nuevo_1 = true;
			} else if (img_id == "foto_2") {
				this.frmDatosPrenda.fotos.foto_2 = e.target.files[0];
				this.frmDatosPrenda.fotos_nuevas.nuevo_2 = true;
			} else if (img_id == "foto_3") {
				this.frmDatosPrenda.fotos.foto_3 = e.target.files[0];
				this.frmDatosPrenda.fotos_nuevas.nuevo_3 = true;
			} else if (img_id == "foto_4") {
				this.frmDatosPrenda.fotos.foto_4 = e.target.files[0];
				this.frmDatosPrenda.fotos_nuevas.nuevo_4 = true;
			}

			let reader = new FileReader();
			reader.readAsDataURL(e.target.files[0]); // leemos el archivo subido y se lo pasamos a nuestro fileReader
			reader.onload = function () {
				let preview = document.getElementById("contenido_" + id),
					image = document.createElement("img");

				image.src = reader.result;
				image.id = "img_" + id;
				image.style.height = "100%";
				image.style.width = "100%";
				image.style.border = "1px solid #ffff";

				preview.append(image);
			};
		},
		QuitarFoto(numero_foto) {
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
						this.frmDatosPrenda.fotos.foto_1 = null;
						this.frmDatosPrenda.fotos_nuevas.nuevo_1 = true;
					} else if (numero_foto == "foto_2") {
						this.frmDatosPrenda.fotos.foto_2 = null;
						this.frmDatosPrenda.fotos_nuevas.nuevo_2 = true;
					} else if (numero_foto == "foto_3") {
						this.frmDatosPrenda.fotos.foto_3 = null;
						this.frmDatosPrenda.fotos_nuevas.nuevo_3 = true;
					} else if (numero_foto == "foto_4") {
						this.frmDatosPrenda.fotos.foto_4 = null;
						this.frmDatosPrenda.fotos_nuevas.nuevo_4 = true;
					}
					let id = numero_foto.substr(5, 1);
					let img = $("#contenido_" + id + " img");
					img.remove();
				}
			});
		},
		// Eliminar(id) {
		// 	Swal.fire({
		// 		icon: "question",
		// 		text: "¿Desea eliminar este bien?",
		// 		confirmButtonText:
		// 			'<i class="fas fa-check" style="color:white;"></i>   Si',
		// 		confirmButtonColor: "var(--colorAlto)",
		// 		showCancelButton: true,
		// 		cancelButtonText: '<i class="fas fa-times"></i>   No',
		// 		cancelButtonColor: "var(--plomoOscuroEmpresarial)",
		// 		allowOutsideClick: false,
		// 	}).then((result) => {
		// 		if (result.isConfirmed) {
		// 			let data = new FormData();
		// 			data.append("agencia_id", this.agencia_id);
		// 			data.append("cliente_id", this.cliente_id);
		// 			data.append("prenda_id", id);
		// 			this.$inertia.post(route("cli.prendas.eliminar"), data, {
		// 				preserveScroll: true,
		// 				onStart: () => {
		// 					Swal.fire({
		// 						title: "Espere porfavor...",
		// 						showConfirmButton: false,
		// 						allowOutsideClick: false,
		// 						willOpen: () => {
		// 							Swal.showLoading();
		// 						},
		// 					});
		// 				},
		// 				onSuccess: () => {
		// 					this.submited = false;
		// 					this.ResetearFormulario();
		// 					this.lista_datos = this.datos_prendas;
		// 					this.modo = "BLOQUEADO";
		// 					$("#inventario-tab").tab("show");
		// 					return Swal.fire({
		// 						icon: "success",
		// 						title: "ÉXITO",
		// 						allowOutsideClick: false,
		// 					});
		// 				},
		// 			});
		// 		} else {
		// 			return false;
		// 		}
		// 	});
		// },
		async GenerarActa() {
			let self = this;
			let rptActaEntrega = this.$refs.rptActaEntrega;

			let fecha_sistema = await this.$refs.layout.fecha_hora_actual(
				this.agencia_id
			);
			fecha_sistema = fecha_sistema.substr(0, 10);
			let parts = fecha_sistema.split("-");

			let options = {
				weekday: "long",
				year: "numeric",
				month: "long",
				day: "numeric",
			};

			let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

			let fecha_actual = date.toLocaleDateString("es-ES", options);

			async function EnviarDatos() {
				let cliente = self.cliente;
				let dni = self.datos_cliente.dni;
				let direccion = self.datos_cliente.direccion;
				let distrito = self.datos_cliente.distrito;
				let provincia = self.datos_cliente.provincia;
				let departamento = self.datos_cliente.departamento;

				let prendas = self.prendas_seleccionadas;
				let orden = 0;
				prendas.forEach((element) => {
					orden += 1;
					element.orden = orden;
					if (element.modelo == null) {
						element.modelo = "-";
					}

					if (element.serie == null) {
						element.serie = "-";
					}
				});

				fecha_actual = fecha_actual[0].toUpperCase() + fecha_actual.slice(1);

				let data = {
					cliente: cliente,
					dni: dni,
					direccion: direccion,
					distrito: distrito,
					provincia: provincia,
					departamento: departamento,
					prendas: prendas,
					fecha: fecha_actual,
				};

				rptActaEntrega.data = data;
				rptActaEntrega.document_title = "rptActaEntrega_" + self.cliente_id;
			}

			EnviarDatos().then(() => {
				rptActaEntrega.GenerarWord();
			});
		},
		AgregarActa(e) {
			this.acta_entrega = e.target.files[0];
		},
		QuitarActa() {
			Swal.fire({
				icon: "question",
				text: "¿Desea quitar esta imagen?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.acta_entrega = null;
					$("#fotoActaEntrega").val("");
				} else {
					return false;
				}
			});
		},
		GuardarActa() {
			let self = this;
			Swal.fire({
				icon: "question",
				text: "¿Desea registrar el acta de entrega?",
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
					let lista_prendas = [];
					this.prendas_seleccionadas.forEach((element) => {
						lista_prendas.push(element.id);
					});

					data.append("agencia_id", this.agencia_id);
					data.append("cliente_id", this.cliente_id);
					data.append("acta_entrega", this.acta_entrega);
					data.append("lista_prendas", JSON.stringify(lista_prendas));

					this.$inertia.post(route("cli.prendas.subir_acta"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							this.prendas_seleccionadas = [];
							this.acta_entrega = null;
							$("#fotoActaEntrega").val("");
							this.lista_datos = this.datos_prendas;
							return Swal.fire({
								icon: "success",
								title: "ÉXITO",
								allowOutsideClick: false,
							});
						},
					});
				} else {
					return false;
				}
			});
		},

		VerActa(item) {
			let acta_entrega = item.acta_entrega;
			let año = acta_entrega.substring(0, 4);

			this.acta_subida.ruta =
				"/imagenes_server/creditos/clientes/prendas/actas/" +
				this.agencia_id +
				"/" +
				año +
				"/" +
				acta_entrega;

			this.acta_subida.nombre = acta_entrega;

			$("#mdlActaEntrega").css("display", "block");
		},

		DescargarActa() {
			let source = this.acta_subida.ruta;
			let nombre = this.acta_subida.nombre;
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = nombre;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},

		Guardar() {
			this.submited = true;
			let validacion_fotos =
				this.frmDatosPrenda.fotos.foto_1 == null &&
				this.frmDatosPrenda.fotos.foto_2 == null &&
				this.frmDatosPrenda.fotos.foto_3 == null &&
				this.frmDatosPrenda.fotos.foto_4 == null;

			if (this.$v.frmDatosPrenda.$invalid || validacion_fotos) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos",
					allowOutsideClick: true,
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿Desea guardar los cambios?",
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
					data.append("agencia_id", this.agencia_id);
					data.append("cliente_id", this.cliente_id);
					data.append("modo", this.modo);

					let formulario = this.frmDatosPrenda;

					if (formulario.fotos_nuevas.nuevo_1) {
						data.append("foto_1", formulario.fotos.foto_1);
						data.append(
							"nombre_1",
							formulario.fotos.foto_1 != null
								? "_1" + "." + formulario.fotos.foto_1.name.split(".").pop()
								: null
						);
					}
					if (formulario.fotos_nuevas.nuevo_2) {
						data.append("foto_2", formulario.fotos.foto_2);
						data.append(
							"nombre_2",
							formulario.fotos.foto_2 != null
								? "_2" + "." + formulario.fotos.foto_2.name.split(".").pop()
								: null
						);
					}
					if (formulario.fotos_nuevas.nuevo_3) {
						data.append("foto_3", formulario.fotos.foto_3);
						data.append(
							"nombre_3",
							formulario.fotos.foto_3 != null
								? "_3" + "." + formulario.fotos.foto_3.name.split(".").pop()
								: null
						);
					}
					if (formulario.fotos_nuevas.nuevo_4) {
						data.append("foto_4", formulario.fotos.foto_4);
						data.append(
							"nombre_4",
							formulario.fotos.foto_4 != null
								? "_4" + "." + formulario.fotos.foto_4.name.split(".").pop()
								: null
						);
					}

					data.append("frmDatosPrenda", JSON.stringify(formulario));

					this.$inertia.post(route("cli.prendas.guardar"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							this.submited = false;
							this.ResetearFormulario();
							this.lista_datos = this.datos_prendas;
							this.modo = "BLOQUEADO";
							$("#inventario-tab").tab("show");
							return Swal.fire({
								icon: "success",
								title: "ÉXITO",
								allowOutsideClick: false,
							});
						},
					});
				} else {
					return false;
				}
			});
		},
	},
};
</script>



<style lang="css">
.slot-prendas {
	width: 60% !important;
	margin-left: 25% !important;
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
	right: 10px;
	z-index: 1000;
}

.foto-container {
	width: 400px;
	height: 250px;
}

.foto-icon {
	margin-top: 15%;
	margin-bottom: 15%;
}

.disponible {
	background: var(--green) !important;
	color: white !important;
}
.no-disponible {
	background: var(--red) !important;
	color: white !important;
}

/* Para establecer tamaño de checkbox */
.page__toggle {
	--toggleColor: var(--colorAlto) !important;
	--toggleSize: 25px !important;

	width: fit-content;
	height: fit-content;
}
@media only screen and (max-width: 900px) {
	.slot-prendas {
		width: 99% !important;
		margin-left: 0.5% !important;
		margin-top: 15% !important;
	}
	.alineamiento {
		text-align: center !important;
		margin-top: 7px !important;
	}
}
/* -------------- */
</style>

