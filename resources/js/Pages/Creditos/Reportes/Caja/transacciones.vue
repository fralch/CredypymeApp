<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-transacciones" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="(modo == 'personal' ? 'MIS ' : '') + 'TRANSACCIONES'"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md-12 text-center">
								<div class="form-check">
									<div class="radios" style="display: inline-block">
										<input
											class="form-check-input"
											type="radio"
											name="tipo_transaccion"
											id="rdbIngreso"
											v-model="tipo_transaccion"
											value="I"
										/>
										<label class="form-check-label bolder" for="rdbIngreso"
											>INGRESOS</label
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
											v-model="tipo_transaccion"
											value="E"
										/>
										<label class="form-check-label bolder" for="rdbEgreso"
											>EGRESOS</label
										>
									</div>
								</div>
							</div>

							<fieldset class="form-group col-md-10">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title"
												>AGENCIA</span
											>
										</div>
										<select
											class="form-control center"
											v-model="agencia_busqueda"
										>
											<option
												v-for="item in agencias_permitidas"
												:key="item.id"
												:value="item.id"
											>
												{{ item.agencia }}
											</option>
										</select>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">DESDE</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_desde"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<span class="input-group-text prepend-title">HASTA</span>
										</div>
										<input
											type="date"
											class="form-control input-information center"
											v-model="fecha_hasta"
											:style="
												windowWidth >= 900
													? 'font-size: 15px !important'
													: 'font-size: 13px !important'
											"
										/>
									</div>

									<div class="col-md-1 text-right" v-if="windowWidth < 900">
										<button
											class="btn btn-action mt-3"
											title="Buscar"
											@click="Buscar"
										>
											<span class="icon text-white" style="font-size: 15px">
												<i class="fas fa-search"></i>
											</span>
										</button>
									</div>
								</div>
							</fieldset>

							<div class="col-md-1 ml-3" v-if="windowWidth >= 900">
								<button
									class="btn btn-action mt-3"
									title="Buscar"
									@click="Buscar"
								>
									<span class="icon text-white" style="font-size: 25px">
										<i class="fas fa-search"></i>
									</span>
								</button>
							</div>

							<div class="form-group col-md-2" v-if="modo == 'completo'">
								<div class="form-check">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbPorUsuario"
										v-model="filtro_usuario"
									/>
									<label class="label-title" for="chbPorUsuario"
										>Filtrar por usuario</label
									>
								</div>
							</div>

							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbPorCategoria"
											v-model="por_categoria"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbPorCategoria"
									>
										CATEGORÍA
									</label>
								</div>
								<div class="input-group-prepend"></div>
								<select
									class="form-control center"
									v-model="categoria_seleccionada"
									:disabled="!por_categoria"
									@change="SeleccionSubcategoria"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in categorias_filtradas"
										:key="index"
										:value="item.id"
									>
										{{ item.categoria }}
									</option>
								</select>
							</div>

							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbPorSubcategoria"
											v-model="por_subcategoria"
										/>
									</div>
									<label
										class="input-group-text prepend-title"
										for="chbPorSubcategoria"
									>
										SUBCATEGORÍA
									</label>
								</div>

								<select
									class="form-control center"
									v-model="subcategoria_seleccionada"
									:disabled="!por_subcategoria"
								>
									<option :value="0" disabled selected>Seleccione...</option>
									<option
										v-for="(item, index) in subcategorias_filtradas"
										:key="index"
										:value="item.id"
									>
										{{ item.subcategoria }}
									</option>
								</select>
							</div>
						</div>

						<div class="form-row mt-2">
							<div
								class="col-md-3"
								style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)"
								v-if="filtro_usuario"
							>
								<div
									class="form-check text-center mt-1"
									v-if="modo == 'completo'"
								>
									<input
										class="form-check-input"
										type="checkbox"
										id="chbMostrarHabilitados"
										v-model="mostrar_habilitados"
										@change="FiltrarUsuarios"
									/>
									<label class="label-title" for="chbMostrarHabilitados"
										>Sólo habilitados</label
									>
								</div>
								<table class="table" id="tblUsuarios" width="100%">
									<thead>
										<tr>
											<th>USUARIO</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in usuarios_filtrados"
											:key="index"
										>
											<td style="padding: 0px !important">
												<div class="custom-control custom-checkbox mt-2">
													<input
														type="checkbox"
														class="custom-control-input"
														:id="'chbUsuario_' + index"
														:value="item.dni"
														v-model="usuarios_seleccionados"
													/>
													<label
														class="custom-control-label ml-4"
														:for="'chbUsuario_' + index"
														>{{ item.usuario }}</label
													>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- -------------------------------------------------------- -->

							<div :class="filtro_usuario ? 'col-md-9 ' : 'col-md-12'">
								<div class="card-title">LISTA DE RESULTADOS</div>

								<table class="table" id="tblTransacciones" width="100%">
									<thead>
										<tr>
											<th style="max-width: 20px !important">N°</th>
											<th v-show="validacion" style="width: 20px !important">
												EDITAR
											</th>
											<th style="min-width: 200px !important">CATEGORÍA</th>
											<th style="min-width: 200px !important">SUBCATEGORÍA</th>
											<th style="min-width: 450px !important">CONCEPTO</th>

											<th style="min-width: 120px !important">
												FECHA_REGISTRO
											</th>
											<th style="min-width: 70px !important">A_USUARIO</th>

											<th style="min-width: 70px !important">MONTO</th>

											<th style="min-width: 150px !important">COMPROBANTE</th>
											<th style="min-width: 150px !important">ÁREA</th>
											<th style="min-width: 70px !important">
												USUARIO_REGISTRO
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_transacciones"
											:key="index"
											class="table-bordered"
											:class="[index % 2 == 0 ? 'verde-claro' : '']"
											@dblclick="VerComprobante(item)"
										>
											<td align="center">
												{{ parseInt(index) + 1 }}
											</td>
											<td
												class="table-bordered"
												align="center"
												v-show="validacion"
											>
												<div class="row align-middle row-buttons">
													<button
														class="btn btn-action"
														@click="EditarTransaccion(item)"
													>
														<span class="icon text-white-50">
															<i class="fas fa-edit" style="color: white"></i>
														</span>
													</button>
												</div>
											</td>
											<td>
												{{ item.categoria }}
											</td>
											<td>
												{{ item.subcategoria }}
											</td>
											<td>
												{{ item.concepto }}
											</td>

											<td align="center">
												{{ item.fecha_transaccion }}
											</td>
											<td align="center">
												{{ item.usuario }}
											</td>
											<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>

											<td align="center" id="hover_comprobante">
												{{ item.comprobante }}
											</td>
											<td align="center">
												{{ item.area }}
											</td>
											<td align="center">
												{{ item.usuario_registro }}
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th class="font-11 text-right blue" colspan="5"></th>
											<th class="font-11 text-center blue">TOTAL</th>
											<th class="font-11 text-center blue">
												S/ {{ roundTo(totales.total_monto, 2) }}
											</th>
											<th class="font-11 text-center blue" colspan="3">
												{{ lista_transacciones.length + " " }} registro(s)
											</th>
										</tr>
									</tfoot>
								</table>
								<hr />
								<div class="text-right">
									<button
										class="btn btn-cancel btn-icon-split"
										title="Exportar"
										@click="Exportar('pago_no_hoy')"
										:disabled="lista_transacciones.length == 0"
									>
										<span class="icon text-white">
											<i class="fas fa-file-excel"></i>
										</span>
										<span class="text">EXPORTAR</span>
									</button>
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
										:titulo_modal="'COMPROBANTE'"
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
												v-if="comprobante_seleccionado != null"
											>
												<img
													:src="
														'/imagenes_server/creditos/caja/transacciones/' +
														agencia_busqueda +
														'/' +
														comprobante_seleccionado.substring(0, 4) +
														'/' +
														comprobante_seleccionado
													"
													alt="ruta"
													width="100%"
													height="300px"
													v-if="comprobante_seleccionado != null"
												/>
											</div>
										</div>

										<hr />
										<div class="text-center">
											<button
												class="btn btn-cancel btn-icon-split"
												@click="descargar(comprobante_seleccionado)"
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

					<div id="mdlTransaccion" class="modal">
						<div class="modal-content w-50 mdlComprobante">
							<div class="content" style="display: block">
								<div class="card">
									<headerCloseModal
										ref="headerCloseModal"
										:titulo_modal="'EDITAR TRANSACCIÓN'"
										:nombre_modal="'mdlTransaccion'"
									>
									</headerCloseModal>
									<div class="card-body card-block">
										<div class="col-md-12">
											<div class="form-row">
												<div class="form-group col-md-6">
													<label class="label-title" for="text-input"
														>CATEGORÍA</label
													>

													<div class="input-group InputGroup col-md-12">
														<select
															class="form-control center"
															:class="[
																submited
																	? $v.frmDatosTransaccion.categoria_id.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															@change="FiltrarSubcategoriasHab"
															v-model="frmDatosTransaccion.categoria_id"
														>
															<option :value="0" selected disabled>
																Seleccionar...
															</option>
															<option
																v-for="(
																	item, index
																) in categorias_filtradas_edicion"
																:key="index"
																:value="item.id"
															>
																{{ item.categoria }}
															</option>
														</select>
														<div class="input-group-append">
															<div class="input-group-text">
																<input
																	type="checkbox"
																	id="chbCategoriasHab"
																	v-model="mostrar_categorias_hab"
																/>
																<label class="m-0 ml-1" for="chbCategoriasHab">
																	Hab.</label
																>
															</div>
														</div>
													</div>
												</div>
												<div class="form-group col-md-6">
													<label class="label-title" for="text-input"
														>SUBCATEGORÍA</label
													>
													<div class="input-group InputGroup col-md-12">
														<select
															class="form-control center"
															:class="[
																submited
																	? $v.frmDatosTransaccion.subcategoria_id
																			.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmDatosTransaccion.subcategoria_id"
														>
															<option :value="0" selected disabled>
																Seleccionar...
															</option>

															<option
																v-for="(
																	item, index
																) in subcategorias_filtradas_edicion"
																:key="index"
																:value="item.id"
															>
																{{ item.subcategoria }}
															</option>
														</select>
														<div class="input-group-append">
															<div class="input-group-text">
																<input
																	type="checkbox"
																	id="chbSubcategoriasHab"
																	v-model="mostrar_subcategorias_hab"
																	@change="FiltrarSubcategoriasHab"
																/>
																<label
																	class="m-0 ml-1"
																	for="chbSubcategoriasHab"
																>
																	Hab.</label
																>
															</div>
														</div>
													</div>
												</div>

												<div class="col-md-6 col-6">
													<label class="label-title" for="text-input"
														>PARA AGENCIA
													</label>

													<select
														class="form-control center"
														v-model="frmDatosTransaccion.agencia_id"
														@change="FiltrarUsuariosHab"
													>
														<option
															v-for="(
																item, index
															) in agencias_permitidas_edicion"
															:key="index"
															:value="item.id"
														>
															{{ item.agencia }}
														</option>
													</select>
												</div>
												<div class="form-group col-md-6">
													<label class="label-title" for="text-input"
														>PARA EL USUARIO
													</label>
													<div class="input-group InputGroup col-md-12">
														<select
															class="form-control center"
															:class="[
																submited
																	? $v.frmDatosTransaccion.usuario_id.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmDatosTransaccion.usuario_id"
														>
															<option :value="0" selected disabled>
																Seleccionar...
															</option>
															<option
																v-for="(
																	item, index
																) in usuarios_filtrados_edicion"
																:key="index"
																:value="item.dni"
															>
																{{ item.usuario }}
															</option>
														</select>
														<div class="input-group-append">
															<div class="input-group-text">
																<input
																	type="checkbox"
																	id="chbUsuariosHab"
																	v-model="mostrar_usuarios_hab"
																/>
																<label class="m-0 ml-1" for="chbUsuariosHab">
																	Hab.</label
																>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<label class="label-title" for="text-input"
														>PARA EL ÁREA
													</label>

													<select
														class="form-control center"
														v-model="frmDatosTransaccion.area_trabajo_id"
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

												<div class="form-group col-md-6">
													<label class="label-title" for="text-input"
														>TIPO DE COMPROBANTE</label
													>
													<div class="input-group InputGroup col-md-12">
														<select
															class="form-control center"
															:class="[
																submited
																	? $v.frmDatosTransaccion.comprobante_id
																			.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmDatosTransaccion.comprobante_id"
														>
															<option :value="0" selected disabled>
																Seleccionar...
															</option>

															<option
																v-for="(
																	item, index
																) in comprobantes_filtrados_edicion"
																:key="index"
																:value="item.id"
															>
																{{ item.comprobante }}
															</option>
														</select>
														<div class="input-group-append">
															<div class="input-group-text">
																<input
																	type="checkbox"
																	id="chbComprobantesHab"
																	v-model="mostrar_comprobantes_hab"
																	@change="FiltrarComprobantesHab"
																/>
																<label
																	class="m-0 ml-1"
																	for="chbComprobantesHab"
																>
																	Hab.</label
																>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<label class="label-title">CONCEPTO</label>

											<textarea
												class="form-control mayus text-row"
												:class="[
													submited
														? $v.frmDatosTransaccion.concepto.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												type="text"
												rows="3"
												@focus="hidenav()"
												@blur="shownav()"
												v-model="frmDatosTransaccion.concepto"
											></textarea>
										</div>
										<hr />
										<div class="text-right">
											<button
												class="btn btn-action btn-icon-split"
												title="GUARDAR TRANSACCION"
												@click="Guardar()"
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
				</div>
			</div>
		</div>
	</layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		modo: String,
		usuarios_cuenta: Array,
		datos_usuario: Array,
		usuarios_edicion: Array,

		categorias: Array,
		subcategorias: Array,

		areas_trabajo: Array,
		comprobantes: Array,
		agencias_totales: Array,
	},

	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			agencias_permitidas_edicion: [],
			agencia_busqueda: 0,
			agencia_busqueda_edicion: 0,
			agencia_agregada_id: 0,

			windowWidth: window.innerWidth,

			tipo_transaccion: "E",
			fecha_desde: null,
			fecha_hasta: null,

			filtro_usuario: false,
			mostrar_habilitados: true,

			mostrar_categorias_hab: true,
			mostrar_subcategorias_hab: true,
			mostrar_usuarios_hab: true,
			mostrar_comprobantes_hab: true,

			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			usuarios_filtrados_edicion: [],

			por_categoria: false,
			categorias_filtradas: [],
			categoria_seleccionada: 0,

			comprobante_seleccionado: null,

			comprobantes_filtrados_edicion: [],

			por_subcategoria: false,
			subcategorias_filtradas: [],
			subcategoria_seleccionada: 0,

			categorias_filtradas_edicion: [],
			subcategorias_filtradas_edicion: [],

			lista_transacciones: [],
			totales: {},

			tipo_transaccion_actual: null,

			frmDatosTransaccion: {
				id: 0,
				tipo_transaccion: null,
				categoria_id: 0,
				subcategoria_id: 0,
				usuario_id: 0,
				area_trabajo_id: 0,
				comprobante_id: 0,
				concepto: null,
				agencia_id: 0,
			},
		};
	},

	validations: {
		frmDatosTransaccion: {
			categoria_id: { noZero },
			subcategoria_id: { noZero },
			usuario_id: { noZero },
			comprobante_id: { noZero },
			concepto: { required },
		},
	},

	computed: {
		validacion() {
			let resultado = false;

			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CAJA/EDITAR_TRANSACCION"
				);

			if (permiso_detalle.length != 0) {
				let acceso_agencias = permiso_detalle[0].acceso_agencias;

				if (acceso_agencias != null) {
					acceso_agencias = JSON.parse(acceso_agencias);

					let agencia_autorizada = acceso_agencias.filter(
						(item) => item.agencia_id == this.agencia_busqueda
					);

					if (agencia_autorizada.length != 0) {
						resultado = true;
					}
				}
			}

			return resultado;
		},
	},
	watch: {
		mostrar_categorias_hab() {
			this.FiltrarCategoriasHab();
			this.FiltrarSubcategoriasHab();
		},

		mostrar_subcategorias_hab() {
			this.FiltrarSubcategoriasHab();
		},

		mostrar_usuarios_hab() {
			this.FiltrarUsuariosHab();
		},
		mostrar_comprobantes_hab() {
			this.FiltrarComprobantesHab();
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

		tipo_transaccion() {
			this.categoria_seleccionada = 0;
			this.subcategoria_seleccionada = 0;

			this.FiltrarCategorias();
			this.FiltrarSubcategorias();
		},

		agencia_busqueda() {
			this.lista_transacciones = [];

			this.FechaActual();
			this.FiltrarUsuarios();
			this.FiltrarCategorias();
			this.FiltrarSubcategorias();
		},

		categoria_seleccionada() {
			this.FiltrarSubcategorias();
		},
		filtro_usuario() {
			this.FiltrarUsuarios();
			if (this.modo == "personal") {
				this.usuarios_filtrados = this.datos_usuario;
				this.usuarios_seleccionados.push(this.datos_usuario[0].dni);
			}
		},

		por_categoria(value) {
			this.categoria_seleccionada = 0;
			if (value) {
				this.FiltrarCategorias();
			}
		},
		por_subcategoria(value) {
			this.subcategoria_seleccionada = 0;
			if (value) {
				this.FiltrarSubcategorias();
			}
		},
		lista_transacciones() {
			$("#tblTransacciones").DataTable().destroy();
			this.TablaTransacciones();
		},
		usuarios_filtrados() {
			$("#tblUsuarios").DataTable().destroy();
			this.TablaUsuarios();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		ListarAgenciasPermitidasEdicion() {
			this.agencias_permitidas_edicion = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/TRANSACCION"
			);
		},

		FiltrarCategoriasHab() {
			if (this.mostrar_categorias_hab) {
				this.categorias_filtradas_edicion = this.categorias.filter(
					(item) =>
						item.tipo == this.frmDatosTransaccion.tipo_transaccion &&
						item.agencia_id == this.agencia_busqueda_edicion &&
						item.habilitado == 1
				);
			} else {
				this.categorias_filtradas_edicion = this.categorias.filter(
					(item) =>
						item.tipo == this.frmDatosTransaccion.tipo_transaccion &&
						item.agencia_id == this.agencia_busqueda_edicion
				);
			}

			this.frmDatosTransaccion.categoria_id = 0;
		},
		FiltrarSubcategoriasHab() {
			if (this.mostrar_subcategorias_hab) {
				this.subcategorias_filtradas_edicion = this.subcategorias.filter(
					(item) =>
						item.tipo == this.frmDatosTransaccion.tipo_transaccion &&
						item.agencia_id == this.agencia_busqueda_edicion &&
						item.categoria_id == this.frmDatosTransaccion.categoria_id &&
						item.habilitado == 1
				);
			} else {
				this.subcategorias_filtradas_edicion = this.subcategorias.filter(
					(item) =>
						item.tipo == this.frmDatosTransaccion.tipo_transaccion &&
						item.agencia_id == this.agencia_busqueda_edicion &&
						item.categoria_id == this.frmDatosTransaccion.categoria_id
				);
			}

			this.frmDatosTransaccion.subcategoria_id = 0;
		},
		FiltrarUsuariosHab() {
			if (this.mostrar_usuarios_hab) {
				this.usuarios_filtrados_edicion = this.usuarios_edicion.filter(
					(item) =>
						item.agencia_id == this.frmDatosTransaccion.agencia_id &&
						item.habilitado == 1
				);
			} else {
				this.usuarios_filtrados_edicion = this.usuarios_edicion.filter(
					(item) => item.agencia_id == this.frmDatosTransaccion.agencia_id
				);
			}

			this.frmDatosTransaccion.usuario_id = 0;
		},
		FiltrarComprobantesHab() {
			if (this.mostrar_comprobantes_hab) {
				this.comprobantes_filtrados_edicion = this.comprobantes.filter(
					(item) =>
						item.agencia_id == this.agencia_busqueda_edicion &&
						item.habilitado == 1
				);
			} else {
				this.comprobantes_filtrados_edicion = this.comprobantes.filter(
					(item) => item.agencia_id == this.agencia_busqueda_edicion
				);
			}

			this.frmDatosTransaccion.comprobante_id = 0;
		},

		async EditarTransaccion(item) {
			this.submited = false;
			await this.ListarAgenciasPermitidasEdicion();

			let array = [];
			let contador = this.agencias_permitidas_edicion.filter(
				(item1) => item1.id == item.agencia_id
			);
			if (contador.length == 0) {
				array = this.agencias_totales.filter(
					(item2) => item2.id == item.agencia_id
				)[0];
				this.agencias_permitidas_edicion.push(array);

				this.agencia_agregada_id = array.id;
			}

			if (item.categoria_hab == 1) {
				this.mostrar_categorias_hab = true;
			} else if (item.categoria_hab == 0) {
				this.mostrar_categorias_hab = false;
			}
			if (item.subcategoria_hab == 1) {
				this.mostrar_subcategorias_hab = true;
			} else if (item.subcategoria_hab == 0) {
				this.mostrar_subcategorias_hab = false;
			}

			if (item.usuario_hab == 1) {
				this.mostrar_usuarios_hab = true;
			} else if (item.usuario_hab == 0) {
				this.mostrar_usuarios_hab = false;
			}

			if (item.comprobante_hab == 1) {
				this.mostrar_comprobantes_hab = true;
			} else if (item.comprobante_hab == 0) {
				this.mostrar_comprobantes_hab = false;
			}

			(this.frmDatosTransaccion.id = item.id),
				(this.frmDatosTransaccion.tipo_transaccion = item.tipo),
				(this.frmDatosTransaccion.agencia_id = item.agencia_id),
				(this.frmDatosTransaccion.area_trabajo_id = item.area_trabajo_id),
				(this.frmDatosTransaccion.concepto = item.concepto),
				await this.FiltrarCategoriasHab();

			(this.frmDatosTransaccion.categoria_id = item.categoria_id),
				await this.FiltrarSubcategoriasHab();
			(this.frmDatosTransaccion.subcategoria_id = item.subcategoria_id),
				await this.FiltrarUsuariosHab();

			//    for (a = 0; a < this.usuarios_filtrados_edicion.length; a++) {
			//   if (this.usuarios_filtrados_edicion[a].dni == item.usuario_id) {
			//     contador_2 += 1;
			//   }
			// }

			let contador_2 = this.usuarios_filtrados_edicion.filter(
				(item3) => item3.dni == item.usuario_id
			);

			if (contador_2.length == 1) {
				this.frmDatosTransaccion.usuario_id = item.usuario_id;
			} else {
				this.frmDatosTransaccion.usuario_id = 0;
			}

			await this.FiltrarComprobantesHab();
			(this.frmDatosTransaccion.comprobante_id = item.comprobante_id),
				$("#mdlTransaccion").css("display", "block");
		},

		Guardar() {
			let self = this;

			self.submited = true;

			if (self.$v.frmDatosTransaccion.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			}

			if (this.frmDatosTransaccion.agencia_id == this.agencia_agregada_id) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "No puede editar los datos para esta agencia, seleccione otra",
				});
				return false;
			} else {
				Swal.fire({
					icon: "question",
					text: "¿DESEA EDITAR ESTA TRANSACCIÓN?",
					confirmButtonText:
						'<i class="fas fa-check" style="color:white;"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							title: "REGISTRANDO",
							showConfirmButton: false,
							allowOutsideClick: false,
							willOpen: async () => {
								Swal.showLoading();

								let data = new FormData();

								data.append(
									"agencia_busqueda_edicion",
									self.agencia_busqueda_edicion
								);
								data.append(
									"frmDatosTransaccion",
									JSON.stringify(self.frmDatosTransaccion)
								);

								return await axios
									.post(route("rep.caj.transacciones.guardar"), data)
									.then((response) => {
										$("#mdlTransaccion").css("display", "none");
										self.ListarTransacciones();
										return Swal.fire({
											icon: "success",
											title: "¡ÉXITO!",
											timer: 1200,
											showConfirmButton: false,
										});
									})
									.catch((error) => {
										Swal.showValidationMessage(
											`Ha ocurrido un error, comunicar a TI: ${error}`
										);
									});
							},
						});
					} else {
						return false;
					}
				});
			}
		},

		SeleccionSubcategoria() {
			this.subcategoria_seleccionada = 0;
		},
		VerComprobante(item) {
			this.comprobante_seleccionado = item.documento;
			if (this.comprobante_seleccionado == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Esta Transaccion no tiene comprobante",
				});
				return false;
			}
			$("#mdlComprobante").css("display", "block");
		},

		descargar() {
			let self = this;
			let source =
				"/imagenes_server/creditos/caja/transacciones/" +
				this.agencia_busqueda +
				"/" +
				this.comprobante_seleccionado.substring(0, 4) +
				"/" +
				this.comprobante_seleccionado;
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], { type: response.data.type });
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = this.comprobante_seleccionado;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},
		async FechaActual() {
			if (this.agencia_busqueda == null) {
				return false;
			} else {
				let fecha_actual = await this.$refs.layout.fecha_hora_actual(
					this.agencia_busqueda
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},
		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CAJA_MIS_TRANSACCIONES"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CAJA_TRANSACCIONES"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
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
		TablaUsuarios() {
			this.$nextTick(() => {
				let scroll_height = "300px";
				if (this.windowWidth <= 900) {
					scroll_height = "100px";
				}
				var table = $("#tblUsuarios").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
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
				});
			});
		},
		TablaTransacciones() {
			this.$nextTick(() => {
				let scroll_height = "320px";
				if (this.windowWidth <= 900) {
					scroll_height = "200px";
				}
				var table = $("#tblTransacciones").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
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
				});
			});
		},

		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.usuarios_seleccionados = [];

				if (this.filtro_usuario) {
					this.usuarios_filtrados = [];

					if (this.mostrar_habilitados) {
						this.usuarios_filtrados = this.usuarios_cuenta.filter(
							(item) =>
								item.agencia_id == this.agencia_busqueda &&
								item.habilitado == 1 &&
								item.con_cuenta == 1
						);
					} else {
						this.usuarios_filtrados = this.usuarios_cuenta.filter(
							(item) =>
								item.agencia_id == this.agencia_busqueda && item.con_cuenta == 1
						);
					}
				}
			} else if (this.modo == "personal") {
				return false;
			}
		},

		FiltrarCategorias() {
			this.categoria_id = 0;
			if (this.por_categoria) {
				this.categorias_filtradas = this.categorias.filter(
					(item) =>
						item.tipo == this.tipo_transaccion &&
						item.agencia_id == this.agencia_busqueda
				);
			} else {
				return false;
			}
		},
		FiltrarSubcategorias() {
			this.subcategoria_id = 0;

			if (this.por_subcategoria) {
				if (this.por_categoria && this.categoria_seleccionada != 0) {
					this.subcategorias_filtradas = this.subcategorias.filter(
						(item) =>
							item.tipo == this.tipo_transaccion &&
							item.agencia_id == this.agencia_busqueda &&
							item.categoria_id == this.categoria_seleccionada
					);
				} else {
					this.subcategorias_filtradas = this.subcategorias.filter(
						(item) =>
							item.tipo == this.tipo_transaccion &&
							item.agencia_id == this.agencia_busqueda
					);
				}
			} else {
				return false;
			}
		},

		ListarTransacciones() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("filtro_usuario", this.filtro_usuario);
			data.append("tipo_transaccion", this.tipo_transaccion);
			data.append("por_categoria", this.por_categoria);
			data.append("por_subcategoria", this.por_subcategoria);

			if (this.por_categoria) {
				data.append("categoria_id", this.categoria_seleccionada);
			}
			if (this.por_subcategoria) {
				data.append("subcategoria_id", this.subcategoria_seleccionada);
			}

			if (this.filtro_usuario) {
				data.append(
					"usuarios_cuenta",
					JSON.stringify(this.usuarios_seleccionados)
				);
			}

			// this.$inertia.post(route("rep.caj.transacciones.buscar"), data);
			// return false;

			axios
				.post(route("rep.caj.transacciones.buscar"), data)
				.then(function (response) {
					if (response.data.lista_transacciones.length == 0) {
						self.lista_transacciones = [];
						self.totales = 0;

						if (self.submited == false) {
							return Swal.fire({
								icon: "info",
								title: "¡Ups!",
								text: "No se encontraron datos",
								allowOutsideClick: true,
							});
						}
					} else {
						self.lista_transacciones = response.data.lista_transacciones;
						self.totales = response.data.totales;
						self.tipo_transaccion_actual =
							response.data.tipo_transaccion_actual;

						self.agencia_busqueda_edicion = self.agencia_busqueda;

						if (self.submited == false) {
							return Swal.fire({
								icon: "success",
								title: "¡Listo!",
								timer: 1200,
								showConfirmButton: false,
							});
						}
					}
				});
		},

		Buscar() {
			let self = this;

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					self.ListarTransacciones();
				},
			});
		},
		Exportar() {
			let data = new FormData();
			data.append(
				"lista_transacciones",
				JSON.stringify(this.lista_transacciones)
			);
			data.append("agencia_id", this.agencia_busqueda);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("tipo_transaccion_actual", this.tipo_transaccion_actual);

			data.append("totales", this.totales.total_monto);

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					//   this.$inertia.post(route("rep.caj.transacciones.exportar"), data);

					axios
						.post(route("rep.caj.transacciones.exportar"), data)
						.then(function (response) {
							let origin = window.location.origin;

							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptTransacciones.xlsx";
							link.click();

							return Swal.fire({
								icon: "success",
								title: "¡EXPORTADO!",
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
.slot-reporte-transacciones {
	width: 70% !important;
	margin-left: 15% !important;
}

.blue {
	background: var(--blue) !important;
}

.font-11 {
	font-size: 11px !important;
}
.mdlComprobante {
	margin-top: 2%;
}
.mdlTransaccion {
	margin-top: 2%;
}

.InputGroup {
	padding-left: 0;
}

@media only screen and (max-width: 900px) {
	.slot-reporte-transacciones {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
	.mdlComprobante {
		margin-top: 2%;
	}
	.mdlTransaccion {
		margin-top: 2%;
	}
	.InputGroup {
		padding-left: 0;
	}
}
</style>


