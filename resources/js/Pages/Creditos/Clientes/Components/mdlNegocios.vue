<template>
	<div class="">
		<div id="mdlNegocios" class="modal">
			<!-- Modal content -->
			<div class="modal-content mdlNegocios w-50">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>NEGOCIOS DEL CLIENTE</strong>
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
							<ul class="nav nav-tabs" id="myTab" role="tablist">
								<li class="nav-item" role="presentation">
									<a
										class="nav-link active tab-title"
										id="datosNegocio1-tab"
										data-toggle="tab"
										href="#datosNegocio1"
										role="tab"
										aria-controls="datosNegocio1"
										aria-selected="true"
										>LISTA DE NEGOCIOS
									</a>
								</li>
								<li class="nav-item" role="presentation">
									<a
										class="nav-link tab-title"
										id="datosNegocio2-tab"
										data-toggle="tab"
										href="#datosNegocio2"
										role="tab"
										aria-controls="datosNegocio2"
										aria-selected="false"
										>DATOS PRINCIPALES</a
									>
								</li>
								<li class="nav-item" role="presentation">
									<a
										class="nav-link tab-title"
										id="datosNegocio3-tab"
										data-toggle="tab"
										href="#datosNegocio3"
										role="tab"
										aria-controls="datosNegocio3"
										aria-selected="false"
										>DIRECCIÓN Y TELÉFONOS</a
									>
								</li>
							</ul>

							<div class="tab-content" id="myTabContent">
								<div
									class="tab-pane fade show active"
									id="datosNegocio1"
									role="tabpanel"
									aria-labelledby="datosNegocio1-tab"
								>
									<div class="text-center mt-2">
										<button
											class="btn btn-action btn-icon-split"
											title="Nuevo"
											@click="NuevoNegocio"
										>
											<span class="icon text-white">
												<i class="fas fa-plus"></i>
											</span>
											<span class="text">AÑADIR NEGOCIO</span>
										</button>
									</div>
									<table class="table" id="tblNegocios" width="100% !important">
										<thead>
											<tr>
												<th style="min-width: 20px !important">VINC.</th>
												<th style="min-width: 150px !important">NOMBRE</th>
												<th style="min-width: 150px !important">ACTIVIDAD</th>
												<th style="min-width: 150px !important">
													DIRECCIÓN_COMPLETA
												</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in lista_negocios_actuales"
												:key="index"
												class="table-bordered"
												:class="[index % 2 == 0 ? 'verde-claro' : '']"
												@dblclick="EditarNegocio(item)"
											>
												<td align="center">
													<input
														class="custom-radio"
														type="radio"
														:id="'rdb_negocio_' + index"
														:value="item"
														@change="VincularNegocio"
														v-model="negocio_activo"
													/>
												</td>
												<td>
													{{ item.nombre }}
												</td>
												<td>
													{{ item.actividad }}
												</td>
												<td>
													{{
														item.direccion +
														" - " +
														item.distrito +
														" / " +
														item.provincia +
														" / " +
														item.departamento
													}}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div
									class="tab-pane fade"
									id="datosNegocio2"
									role="tabpanel"
									aria-labelledby="datosNegocio2-tab"
								>
									<div class="form-row">
										<div class="form-group col-md-6 col-12">
											<label class="form-control-label label-title"
												>NOMBRE NEGOCIO</label
											>

											<form autocomplete="off">
												<textarea
													rows="1"
													class="form-control mayus"
													:class="[
														submited
															? $v.frmNegocio.nombre.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmNegocio.nombre"
													:disabled="frmNegocio.modo == 'NO-EDITAR'"
												></textarea>
											</form>
										</div>
										<div class="form-group col-md-6 col-12">
											<label class="form-control-label label-title"
												>ACTIVIDAD</label
											>

											<form autocomplete="off">
												<textarea
													rows="1"
													class="form-control mayus"
													:class="[
														submited
															? $v.frmNegocio.actividad.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmNegocio.actividad"
													:disabled="frmNegocio.modo == 'NO-EDITAR'"
												></textarea>
											</form>
										</div>
										<div class="form-group col-md-12 col-12">
											<label class="form-control-label label-title">CIIU</label>
											<span
												class="btn btn-action btn-icon-split mb-2"
												title="Agregar ciiu"
												@click="AgregarCiiu"
												v-if="frmNegocio.modo != 'NO-EDITAR '"
											>
												<span class="icon text-white">
													<i class="fas fa-plus"></i>
												</span>
												<span class="text">{{
													frmNegocio.modo == "EDITAR" ? "CAMBIAR" : "AÑADIR"
												}}</span>
											</span>

											<textarea
												rows="2"
												class="form-control text-row mayus"
												:class="[
													submited
														? $v.frmNegocio.descripcion_ciiu.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmNegocio.descripcion_ciiu"
												:disabled="true"
											></textarea>
										</div>
									</div>
									<hr />

									<div class="text-right">
										<div class="btn-group" role="group">
											<button
												class="btn btn-action btn-icon-split"
												@click="GuardarNegocio"
												v-if="frmNegocio.modo != 'NO-EDITAR'"
											>
												<span class="icon text-white">
													<i class="fas fa-save"></i>
												</span>
												<span class="text">GUARDAR</span>
											</button>
										</div>
									</div>
								</div>
								<div
									class="tab-pane fade"
									id="datosNegocio3"
									role="tabpanel"
									aria-labelledby="datosNegocio3-tab"
								>
									<form autocomplete="off">
										<div class="form-row">
											<div class="form-group col-md-12">
												<label class="form-control-label label-title"
													>DIRECCIÓN</label
												>

												<form autocomplete="off">
													<textarea
														class="form-control mayus"
														:class="[
															submited
																? $v.frmNegocio.direccion.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														rows="1"
														v-model="frmNegocio.direccion"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													></textarea>
												</form>
											</div>
											<div class="form-group col-md-4 col-4">
												<label class="form-control-label label-title"
													>DEPARTAMENTO</label
												>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmNegocio.departamento_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													@change="FiltrarProvincias"
													v-model="frmNegocio.departamento_id"
													:disabled="frmNegocio.modo == 'NO-EDITAR'"
												>
													<option value="0" selected disabled>
														Seleccione...
													</option>
													<option
														v-for="departamento in departamentos"
														:key="departamento.id"
														:value="departamento.id"
													>
														{{ departamento.departamento }}
													</option>
												</select>
											</div>
											<div class="form-group col-md-4 col-4">
												<label class="form-control-label label-title"
													>PROVINCIA</label
												>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmNegocio.provincia_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													@change="FiltrarDistritos"
													v-model="frmNegocio.provincia_id"
													:disabled="frmNegocio.modo == 'NO-EDITAR'"
												>
													<option value="0" selected disabled>
														Seleccione...
													</option>
													<option
														v-for="provincia in provincias_filtradas"
														:key="provincia.id"
														:value="provincia.id"
													>
														{{ provincia.provincia }}
													</option>
												</select>
											</div>
											<div class="form-group col-md-4 col-4">
												<label class="form-control-label label-title"
													>DISTRITO</label
												>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmNegocio.distrito_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmNegocio.distrito_id"
													:disabled="frmNegocio.modo == 'NO-EDITAR'"
												>
													<option value="0" selected disabled>
														Seleccione...
													</option>
													<option
														v-for="distrito in distritos_filtrados"
														:key="distrito.id"
														:value="distrito.id"
													>
														{{ distrito.distrito }}
													</option>
												</select>
											</div>

											<div class="form-group col-md-12 col-12">
												<label class="form-control-label label-title"
													>REFERENCIA</label
												>

												<form autocomplete="off">
													<textarea
														class="form-control text-row mayus"
														:class="[
															submited
																? $v.frmNegocio.referencia_direccion.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														rows="3"
														v-model="frmNegocio.referencia_direccion"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													/>
												</form>
											</div>

											<div class="form-group col-md-4 col-4">
												<label class="form-control-label label-title"
													>NÚMERO</label
												>

												<div class="input-group">
													<div
														class="input-group-prepend"
														v-if="windowWidth > 1000"
													>
														<span class="input-group-text">1</span>
													</div>

													<input
														type="number"
														class="form-control center"
														:class="[
															submited
																? $v.frmNegocio.telefonos.t1.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														min="0"
														maxlength="9"
														oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
														v-model="frmNegocio.telefonos.t1"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													/>
												</div>
											</div>

											<div class="form-group col-md-4 col-4">
												<label class="form-control-label label-title"
													>OPERADOR</label
												>

												<div class="input-group">
													<div
														class="input-group-prepend"
														v-if="windowWidth > 1000"
													>
														<span class="input-group-text">1</span>
													</div>

													<select
														class="form-control center"
														:class="[
															submited
																? $v.frmNegocio.telefonos.o1.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmNegocio.telefonos.o1"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													>
														<option :value="0" selected disabled>
															Seleccione...
														</option>
														<option value="Movistar">Movistar</option>
														<option value="Claro">Claro</option>
														<option value="Bitel">Bitel</option>
														<option value="Entel">Entel</option>
													</select>
												</div>
											</div>

											<div class="form-group col-md-4 col-4">
												<label class="form-control-label label-title"
													>NOTAS</label
												>

												<div class="input-group">
													<div
														class="input-group-prepend"
														v-if="windowWidth > 1000"
													>
														<span class="input-group-text">1</span>
													</div>

													<textarea
														rows="1"
														class="form-control mayus"
														:class="[
															submited
																? $v.frmNegocio.telefonos.n1.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmNegocio.telefonos.n1"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													></textarea>
												</div>
											</div>

											<div class="form-group col-md-4 col-4">
												<div class="input-group">
													<div
														class="input-group-prepend"
														v-if="windowWidth > 1000"
													>
														<span class="input-group-text">2</span>
													</div>

													<input
														type="number"
														class="form-control center"
														style="max-width: 400px"
														min="0"
														maxlength="9"
														oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
														v-model="frmNegocio.telefonos.t2"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													/>
												</div>
											</div>
											<div class="form-group col-md-4 col-4">
												<div class="input-group">
													<div
														class="input-group-prepend"
														v-if="windowWidth > 1000"
													>
														<span class="input-group-text">2</span>
													</div>
													<select
														class="form-control center"
														v-model="frmNegocio.telefonos.o2"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													>
														<option value="0" selected disabled>
															Seleccione...
														</option>
														<option value="Movistar">Movistar</option>
														<option value="Claro">Claro</option>
														<option value="Bitel">Bitel</option>
														<option value="Entel">Entel</option>
													</select>
												</div>
											</div>

											<div class="form-group col-md-4 col-4">
												<div class="input-group">
													<div
														class="input-group-prepend"
														v-if="windowWidth > 1000"
													>
														<span class="input-group-text">2</span>
													</div>

													<textarea
														rows="1"
														class="form-control mayus"
														style="max-width: 500px"
														v-model="frmNegocio.telefonos.n2"
														:disabled="frmNegocio.modo == 'NO-EDITAR'"
													></textarea>
												</div>
											</div>
										</div>
									</form>
									<hr />

									<div class="text-right">
										<div class="btn-group" role="group">
											<button
												class="btn btn-action btn-icon-split"
												@click="GuardarNegocio"
												v-if="frmNegocio.modo != 'NO-EDITAR'"
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

		<div id="mdlCiiu" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-50">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>CIIU</strong>
							<button
								type="button"
								class="btn btn-green"
								style="border-radius: 50%"
								@click="CerrarCiiu"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-title">BUSCAR ACTIVIDAD ECONÓMICA</div>
						<div class="card-body card-block">
							<div class="form-group col-md-12">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">BUSCAR</span>
									</div>
									<input
										type="text"
										class="form-control mayus"
										v-model="texto_buscar"
										@keyup="BuscarCiiu"
										spellcheck="false"
										autocomplete="off"
									/>
								</div>
							</div>

							<table class="table" id="tblCiiu" width="100% !important">
								<thead>
									<tr>
										<th style="width: 20px !important">CÓDIGO</th>
										<th>NOMBRE</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="(item, index) in lista_ciiu"
										:key="index"
										class="table-bordered"
										:class="[index % 2 == 0 ? 'verde-claro' : '']"
										@dblclick="SeleccionarCiiu(item)"
									>
										<td align="center">
											{{ item.codigo }}
										</td>
										<td>
											{{ item.nombre }}
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
</template>

<script>
import { required } from "vuelidate/lib/validators";

const noZero = (value) => value != 0;
export default {
	props: {
		agencias: Array,
		distritos: Array,
		provincias: Array,
		departamentos: Array,
		agencia_seleccionada: Number,
	},
	data() {
		return {
			windowWidth: window.innerWidth,
			texto_buscar: null,
			submited: false,
			lista_negocios_actuales: [],
			lista_ciiu: [],
			distritos_filtrados: this.distritos,
			provincias_filtradas: this.provincias,

			negocio_activo: [],
			frmNegocio: {
				modo: null,
				id: 0,
				cliente_id: 0,
				negocio_vinculado_id: 0,
				nombre: null,
				actividad: null,
				ciiu_id: 0,
				descripcion_ciiu: null,
				direccion: null,
				distrito_id: 0,
				provincia_id: 0,
				departamento_id: 0,
				referencia_direccion: null,
				telefonos: {
					t1: null,
					t2: null,
					o1: null,
					o2: null,
					n1: null,
					n2: null,
				},
			},
		};
	},
	validations: {
		frmNegocio: {
			nombre: { required },
			actividad: { required },
			ciiu_id: { required, noZero },
			descripcion_ciiu: { required },
			direccion: { required },
			departamento_id: { noZero },
			provincia_id: { noZero },
			distrito_id: { noZero },
			referencia_direccion: { required },
			telefonos: {
				t1: { required },
				o1: { noZero },
				n1: { required },
			},
		},
	},
	watch: {
		lista_negocios_actuales() {
			$("#tblNegocios").DataTable().destroy();
			this.TablaListaNegocios();
		},
		lista_ciiu() {
			$("#tblCiiu").DataTable().destroy();
			this.TablaListaCiiu();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		this.TablaListaNegocios();
		this.TablaListaCiiu();
	},
	methods: {
		TablaListaNegocios() {
			this.$nextTick(() => {
				var table = $("#tblNegocios").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
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
					responsive: true,
				});
			});
		},
		TablaListaCiiu() {
			this.$nextTick(() => {
				var table = $("#tblCiiu").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					order: [[0, "asc"]],
					fixedHeader: true,
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
					responsive: true,
				});
			});
		},

		ResetearFrmNegocio() {
			this.frmNegocio.id = 0;
			this.frmNegocio.negocio_vinculado_id = 0;
			this.frmNegocio.nombre = null;
			this.frmNegocio.actividad = null;

			this.frmNegocio.ciiu_id = 0;
			this.frmNegocio.descripcion_ciiu = null;

			this.frmNegocio.direccion = null;
			this.frmNegocio.referencia_direccion = null;
			this.frmNegocio.distrito_id = 0;
			this.frmNegocio.provincia_id = 0;
			this.frmNegocio.departamento_id = 0;
			this.frmNegocio.telefonos.t1 = null;
			this.frmNegocio.telefonos.t1 = null;
			this.frmNegocio.telefonos.t2 = null;
			this.frmNegocio.telefonos.o1 = 0;
			this.frmNegocio.telefonos.o2 = 0;
			this.frmNegocio.telefonos.n1 = null;
			this.frmNegocio.telefonos.n2 = null;
		},
		FiltrarProvincias(e) {
			let departamento_id = e.target.value;

			if (!departamento_id == 0) {
				this.provincias_filtradas = this.provincias.filter(
					(item) => item.departamento_id == departamento_id
				);
			} else {
				this.provincias_filtradas = this.provincias;
			}

			this.frmNegocio.provincia_id = 0;
			this.frmNegocio.distrito_id = 0;
		},
		FiltrarDistritos(e) {
			let provincia_id = e.target.value;

			if (!provincia_id == 0) {
				this.distritos_filtrados = this.distritos.filter(
					(item) => item.provincia_id == provincia_id
				);
			} else {
				this.distritos_filtrados = this.distritos;
			}

			this.frmNegocio.distrito_id = 0;
		},
		NuevoNegocio() {
			this.frmNegocio.modo = "NUEVO";
			this.submited = false;
			this.ResetearFrmNegocio();
			$("#datosNegocio2-tab").tab("show");
		},
		EditarNegocio(negocio) {
			this.frmNegocio.modo = "EDITAR";
			this.frmNegocio.id = negocio.id;
			this.frmNegocio.cliente_id = negocio.cliente_id;
			this.frmNegocio.negocio_vinculado_id = negocio.negocio_vinculado_id;
			this.frmNegocio.nombre = negocio.nombre;
			this.frmNegocio.actividad = negocio.actividad;
			this.frmNegocio.ciiu_id = negocio.ciiu_id;
			this.frmNegocio.descripcion_ciiu = negocio.descripcion_ciiu;
			this.frmNegocio.direccion = negocio.direccion;
			this.frmNegocio.distrito_id = negocio.distrito_id;
			this.frmNegocio.provincia_id = negocio.provincia_id;
			this.frmNegocio.departamento_id = negocio.departamento_id;
			this.frmNegocio.referencia_direccion = negocio.referencia_direccion;

			let telefonos = [];

			if (typeof negocio.telefonos === "string") {
				telefonos = JSON.parse(negocio.telefonos);
			} else {
				telefonos = negocio.telefonos;
			}

			this.frmNegocio.telefonos = telefonos;

			$("#datosNegocio2-tab").tab("show");
		},
		AgregarCiiu() {
			this.texto_buscar = null;
			this.lista_ciiu = [];

			$("#mdlCiiu").css("display", "block");
		},
		BuscarCiiu() {
			let self = this;
			if (this.texto_buscar && this.texto_buscar.length >= 3) {
				axios
					.post(route("cli.listado_registro.buscar_ciiu"), {
						nombre_ciiu: self.texto_buscar,
					})
					.then(function (response) {
						self.lista_ciiu = response.data;
					});
			}
		},
		SeleccionarCiiu(item) {
			this.frmNegocio.ciiu_id = item.id;
			this.frmNegocio.descripcion_ciiu = item.codigo + " - " + item.nombre;
			$("#mdlCiiu").css("display", "none");
		},
		GuardarNegocio() {
			let self = this;
			this.submited = true;

			if (this.$v.frmNegocio.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				$("#datosNegocio2-tab").tab("show");
				return false;
			}

			Swal.fire({
				title: "GUARDAR NEGOCIO",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					let data = new FormData();

					data.append("frmNegocio", JSON.stringify(self.frmNegocio));
					data.append("agencia_id", self.agencia_seleccionada);
					this.$inertia.post(
						route("cli.listado_registro.asignar_negocio"),
						data,
						{
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "ESPERE POR FAVOR...",
									timer: 1200,
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
									preConfirm: (result) => {
										axios
											.post(
												route("cli.listado_registro.listar_negocios", {
													cliente_id: self.frmNegocio.cliente_id,
													agencia_id: self.agencia_seleccionada,
												})
											)
											.then(function (response) {
												self.lista_negocios_actuales = response.data;
												self.negocio_activo =
													self.lista_negocios_actuales.filter(
														(item) => item.vinculado == 1
													)[0];
											});
										self.ResetearFrmNegocio();
										$("#datosNegocio1-tab").tab("show");
										self.frmNegocio.modo = "NO-EDITAR";
										self.submited = false;
										self.frmNegocio.negocio_vinculado_id = 0;
									},
								});
							},
						}
					);
				},
			});
		},
		CerrarModal() {
			$("#mdlNegocios").css("display", "none");
			$("#datosNegocio1-tab").tab("show");
			this.$parent.ListarParientesAvalesNegocios();
		},
		CerrarCiiu() {
			$("#mdlCiiu").css("display", "none");
			$("#datosNegocio2-tab").tab("show");
		},

		VincularNegocio() {
			let self = this;
			Swal.fire({
				title: "VINCULAR NEGOCIO",
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

					data.append("negocio_activo", JSON.stringify(self.negocio_activo));
					data.append("agencia_id", self.agencia_seleccionada);
					this.$inertia.post(
						route("cli.listado_registro.vincular_negocio"),
						data,
						{
							preserveScroll: true,
							onStart: (visit) => {
								let timerInterval;
								Swal.fire({
									title: "ESPERE POR FAVOR...",
									timer: 1200,
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
									preConfirm: (result) => {
										axios
											.post(
												route("cli.listado_registro.listar_negocios", {
													cliente_id: self.frmNegocio.cliente_id,
													agencia_id: self.agencia_seleccionada,
												})
											)
											.then(function (response) {
												self.lista_negocios_actuales = response.data;
												self.negocio_activo =
													self.lista_negocios_actuales.filter(
														(item) => item.vinculado == 1
													)[0];
											});

										$("#datosNegocio1-tab").tab("show");
									},
								});
							},
						}
					);
				} else {
					self.negocio_activo = self.lista_negocios_actuales.filter(
						(item) => item.vinculado == 1
					)[0];
				}
			});
		},
	},
};
</script>

<style>
.mdlNegocios {
	margin-top: 4% !important;
}
@media (max-width: 900px) {
	.mdlNegocios {
		width: 99% !important;
		margin-left: 0.5% !important;

		margin-top: 25% !important;
	}
}
</style>
