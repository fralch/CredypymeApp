<template>
	<layout ref="layout">
		<div class="slot_body slot-gestion" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'GESTIÓN DE USUARIOS'"></headerClose>

					<div class="card-title">PANEL DE BÚSQUEDA</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group col-md-3">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">AGENCIA</span>
								</div>

								<select
									class="form-control center"
									id="slcAgencias"
									data-index="4"
								>
									<option value="0">TODAS</option>
									<option v-for="item in agencias" :key="item.agencia_id">
										{{ item.nombre }}
									</option>
								</select>
							</div>
							<div class="input-group col-md-5 col-3">
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

							<div class="col-md-1 col-2">
								<button
									class="btn btn-action btn-icon-split"
									@click="NuevoUsuario"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<span class="text">NUEVO</span>
								</button>
							</div>
						</div>
					</div>

					<div class="card-title">LISTA DE USUARIOS</div>
					<div class="card-body card-block">
						<table class="table table-hover" id="tblUsuarios" width="100%">
							<thead>
								<tr>
									<th style="width: 60px !important">EDITAR</th>
									<th style="width: 30px !important">USUARIO</th>
									<th style="min-width: 200px !important">APELLIDOS_NOMBRES</th>
									<th style="min-width: 40px !important">DNI</th>
									<th style="width: 50px !important">AGENCIA</th>
									<th style="min-width: 150px !important">DIRECCIÓN</th>
									<th style="width: 50px !important">TELÉFONO</th>
									<th style="width: 70px !important">CORREO</th>
									<th style="min-width: 160px !important">CARGO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(usuario, index) in usuarios" :key="index">
									<td class="table-bordered" align="center">
										<div class="row align-middle row-buttons">
											<div class="col-md-9 col-md-offset-9 col-xs-12">
												<div class="btn-group" role="group">
													<button
														class="btn btn-action"
														@click="EditarUsuario(usuario)"
													>
														<span class="icon text-white-50">
															<i class="fas fa-edit" style="color: white"></i>
														</span>
													</button>
													<button
														class="btn btn-cancel"
														type="button"
														title="Cambiar clave"
														v-if="clave_permiso == '1'"
														@click="CambiarClave(usuario)"
													>
														<span class="icon text-white">
															<i class="fas fa-lock"></i>
														</span>
													</button>
												</div>
											</div>
										</div>
									</td>
									<td class="table-bordered" align="center">
										{{ usuario.usuario }}
									</td>
									<td class="table-bordered">
										{{
											usuario.apellido_paterno +
											" " +
											usuario.apellido_materno +
											" " +
											usuario.nombres
										}}
									</td>
									<td class="table-bordered" align="center">
										{{ usuario.dni }}
									</td>

									<td class="table-bordered" align="center">
										{{ usuario.nombreAgencia }}
									</td>

									<td class="table-bordered">
										{{ usuario.direccion }}
									</td>
									<td class="table-bordered" align="center">
										{{ usuario.telefono }}
									</td>
									<td class="table-bordered">
										{{ usuario.correo_corporativo }}
									</td>

									<td class="table-bordered">
										{{ usuario.cargo }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- The Modal -->
			<div id="mdlDatosUsuario" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-35 mdlDatosUsuario">
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

							<div class="card-title">INFORMACIÓN PERSONAL</div>
							<div class="card-body card-block">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item" role="presentation">
										<a
											class="nav-link active tab-title"
											id="datosUsuario1-tab"
											data-toggle="tab"
											href="#datosUsuario1"
											role="tab"
											aria-controls="datosUsuario1"
											aria-selected="true"
											>DATOS PERSONALES</a
										>
									</li>
									<li class="nav-item" role="presentation">
										<a
											class="nav-link tab-title"
											id="datosUsuario2-tab"
											data-toggle="tab"
											href="#datosUsuario2"
											role="tab"
											aria-controls="datosUsuario2"
											aria-selected="false"
											><i class="fas fa-plus-square"></i
										></a>
									</li>
								</ul>
								<div class="tab-content" id="myTabContent">
									<div
										class="tab-pane fade show active"
										id="datosUsuario1"
										role="tabpanel"
										aria-labelledby="datosUsuario1-tab"
									>
										<div class="form-row">
											<div class="form-group col-sm-4">
												<label class="label-title">DNI</label>
												<span
													v-if="
														submited &&
														form_datos_usuario.modo == 'NUEVO' &&
														!$v.form_datos_usuario.dni.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													:type="txtdni_type"
													class="form-control center"
													maxlength="8"
													style="max-width: 200px"
													oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
													v-model="form_datos_usuario.dni"
													:disabled="form_datos_usuario.modo == 'EDITAR'"
												/>
											</div>
											<div class="form-group col-sm-4">
												<label class="label-title">USUARIO</label>
												<span
													v-if="
														submited && !$v.form_datos_usuario.usuario.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													type="text"
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.usuario"
												/>
											</div>
											<div class="form-group col-sm-4">
												<label class="label-title">FECHA DE NACIMIENTO</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.fecha_nacimiento.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													type="date"
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.fecha_nacimiento"
												/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm-4">
												<label class="label-title">APELLIDO PATERNO</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.apellido_paterno.required
													"
													class="span-error-message"
												>
													*
												</span>
												<textarea
													type="text"
													class="form-control mayus"
													style="max-width: 300px"
													rows="1"
													v-model="form_datos_usuario.apellido_paterno"
												></textarea>
											</div>
											<div class="form-group col-sm-4">
												<label class="label-title">APELLIDO MATERNO</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.apellido_materno.required
													"
													class="span-error-message"
												>
													*
												</span>
												<textarea
													type="text"
													class="form-control mayus"
													style="max-width: 300px"
													rows="1"
													v-model="form_datos_usuario.apellido_materno"
												></textarea>
											</div>
											<div class="form-group col-sm-4">
												<label class="label-title">NOMBRES</label>
												<span
													v-if="
														submited && !$v.form_datos_usuario.nombres.required
													"
													class="span-error-message"
												>
													*
												</span>
												<textarea
													type="text"
													class="form-control mayus"
													style="max-width: 300px"
													rows="1"
													v-model="form_datos_usuario.nombres"
												></textarea>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm-4">
												<label class="label-title">GÉNERO</label>
												<span
													v-if="submited && !$v.form_datos_usuario.sexo.noZero"
													class="span-error-message"
												>
													*
												</span>
												<select
													class="form-control center"
													style="max-width: 200px"
													v-model="form_datos_usuario.sexo"
												>
													<option value="0">Seleccione...</option>
													<option value="F">Femenino</option>
													<option value="M">Masculino</option>
												</select>
											</div>
											<div class="form-group col-sm-8">
												<label class="label-title">DIRECCIÓN</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.direccion.required
													"
													class="span-error-message"
												>
													*
												</span>
												<textarea
													rows="2"
													class="form-control mayus"
													v-model="form_datos_usuario.direccion"
												></textarea>
											</div>
										</div>
									</div>
									<div
										class="tab-pane fade"
										id="datosUsuario2"
										role="tabpanel"
										aria-labelledby="datosUsuario2-tab"
									>
										<div class="form-row">
											<div class="form-group col-sm-4">
												<label class="label-title">DEPARTAMENTO</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.departamento_id.noZero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													type="text"
													class="form-control center"
													style="max-width: 300px"
													@change="FiltrarProvincias"
													v-model="form_datos_usuario.departamento_id"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="departamento in departamentos"
														:key="departamento.id"
														:value="departamento.id"
													>
														{{ departamento.departamento }}
													</option>
												</select>
											</div>
											<div class="form-group col-sm-4">
												<label class="label-title">PROVINCIA</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.provincia_id.noZero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													type="text"
													class="form-control center"
													style="max-width: 300px"
													@change="FiltrarDistritos"
													v-model="form_datos_usuario.provincia_id"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="provincia in provincias_filtradas"
														:key="provincia.id"
														:value="provincia.id"
													>
														{{ provincia.provincia }}
													</option>
												</select>
											</div>
											<div class="form-group col-sm-4">
												<label class="label-title">DISTRITO</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.distrito_id.noZero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													type="text"
													class="form-control center"
													style="max-width: 300px"
													v-model="form_datos_usuario.distrito_id"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="distrito in distritos_filtrados"
														:key="distrito.id"
														:value="distrito.id"
													>
														{{ distrito.distrito }}
													</option>
												</select>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm-4">
												<label class="label-title">TELÉFONO</label>
												<span
													v-if="
														submited && !$v.form_datos_usuario.telefono.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													type="number"
													class="form-control center"
													style="max-width: 200px"
													maxlength="9"
													oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
													v-model="form_datos_usuario.telefono"
												/>
											</div>
											<div class="form-group col-sm-8">
												<label class="label-title">CORREO</label>
												<span
													v-if="
														submited &&
														!$v.form_datos_usuario.correo_corporativo.required
													"
													class="span-error-message"
												>
													*
												</span>
												<input
													type="text"
													class="form-control"
													v-model="form_datos_usuario.correo_corporativo"
												/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm-3">
												<label class="label-title">AGENCIA</label>
												<span
													v-if="
														submited && !$v.form_datos_usuario.agencia_id.noZero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													type="text"
													class="form-control center"
													style="max-width: 300px"
													v-model="form_datos_usuario.agencia_id"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="agencia in agencias"
														:key="agencia.id_agencia"
														:value="agencia.id_agencia"
													>
														{{ agencia.nombre }}
													</option>
												</select>
											</div>
											<div
												:class="{
													'form-group': true,
													'col-sm-4': form_datos_usuario.modo == 'NUEVO',
													'col-sm-5': form_datos_usuario.modo == 'EDITAR',
												}"
											>
												<label class="label-title">CARGO</label>
												<span
													v-if="
														submited && !$v.form_datos_usuario.cargo_id.noZero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													type="text"
													class="form-control"
													style="max-width: 300px"
													v-model="form_datos_usuario.cargo_id"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="cargo in cargos"
														:key="cargo.id"
														:value="cargo.id"
													>
														{{ cargo.cargo }}
													</option>
												</select>
											</div>
											<div
												class="form-group col-sm-4"
												v-if="form_datos_usuario.modo == 'NUEVO'"
											>
												<label class="label-title">HORARIO</label>
												<span
													v-if="
														submited && !$v.form_datos_usuario.horario_id.noZero
													"
													class="span-error-message"
												>
													*
												</span>
												<select
													type="text"
													class="form-control"
													style="max-width: 300px"
													v-model="form_datos_usuario.horario_id"
												>
													<option value="0">Seleccione...</option>
													<option
														v-for="horario in horarios"
														:key="horario.id"
														:value="horario.id"
													>
														{{ horario.horario }}
													</option>
												</select>
											</div>
											<div
												class="form-group col-sm-3 ml-4"
												v-if="form_datos_usuario.modo == 'EDITAR'"
											>
												<label class="label-title ml-2">USUARIO REAL</label>
												<div class="row ml-1">
													<div class="form-check mr-3">
														<input
															class="form-check-input"
															type="radio"
															:value="1"
															name="usuarioReal"
															id="si"
															v-model="form_datos_usuario.usuario_real"
														/>
														<label class="form-check-label" for="si">
															Si
														</label>
													</div>
													<div class="form-check">
														<input
															class="form-check-input"
															type="radio"
															:value="0"
															name="usuarioReal"
															id="no"
															v-model="form_datos_usuario.usuario_real"
														/>
														<label class="form-check-label" for="no">
															No
														</label>
													</div>
												</div>
											</div>
										</div>
										<div
											class="form-row"
											v-if="form_datos_usuario.modo == 'EDITAR'"
										>
											<div class="form-group col-sm-4">
												<label
													class="form-control-label label-title"
													style="color: var(--colorGth2)"
													>Acciones adicionales:</label
												>
												<button
													class="btn btn-cancel btn-icon-split"
													@click="CesarUsuario"
												>
													<span class="icon text-white">
														<i class="fas fa-user-slash text-white"></i>
													</span>
													<span class="text font-size-layout"
														>Cesar usuario</span
													>
												</button>
											</div>
										</div>
									</div>
									<hr />

									<div class="text-right">
										<button
											class="btn btn-action btn-icon-split"
											@click="ValidarUsuario"
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

			<div id="mdlCambiarClave" class="modal">
				<!-- Modal content -->
				<div class="modal-content mdlCambiarClave">
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
									@click="Cerrar('mdlCambiarClave')"
								>
									<span class="icon text-white">
										<i class="fas fa-times"></i>
									</span>
								</button>
							</div>

							<div class="card-body card-block">
								<div class="form-row">
									<div class="input-group col-md-4">
										<div class="input-group-prepend">
											<label
												class="input-group-text prepend-title"
												style="font-size: 13px"
											>
												DNI
											</label>
										</div>

										<input
											class="form-control center"
											type="text"
											v-model="frmDatosContraseña.dni"
											:disabled="true"
										/>
									</div>
									<div class="input-group col-md-8">
										<div class="input-group-prepend">
											<label class="input-group-text prepend-title">
												USUARIO
											</label>
										</div>

										<input
											class="form-control center"
											type="text"
											v-model="frmDatosContraseña.usuario"
											:disabled="true"
										/>
									</div>
								</div>

								<div class="form-group">
									<div class="input-group mt-4 col-md-8 offset-2">
										<div class="input-group-prepend">
											<label class="input-group-text prepend-title">
												CLAVE NUEVA
											</label>
										</div>

										<input
											class="form-control"
											type="text"
											v-model="frmDatosContraseña.clave_nueva"
										/>
									</div>

									<div class="input-group mt-3 col-md-8 offset-2">
										<div class="input-group-prepend">
											<label
												class="input-group-text prepend-title"
												style="font-size: 13px"
											>
												REPITA LA CLAVE
											</label>
										</div>

										<input
											class="form-control"
											type="text"
											v-model="frmDatosContraseña.clave_repetida"
										/>
									</div>
									<div class="form-group col-md-8 offset-4">
										<div class="form-row">
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
														v-model="frmDatosContraseña.habilitado" /><span
														class="cr"
														style="margin-right: 0 !important"
														><i class="cr-icon fa fa-check"></i></span
												></label>
											</div>
											<label for="chbHabilitado" class="label-title mt-1 ml-1">
												Debe cambiar clave</label
											>
										</div>
									</div>
								</div>
								<hr />

								<div class="text-right">
									<div class="btn-group" role="group">
										<button
											class="btn btn-cancel btn-icon-split"
											@click="RandomClave"
										>
											<span class="icon text-white">
												<i class="fas fa-sync"></i>
											</span>
											<span class="text">GENERAR ALEATORIO</span>
										</button>
										<button
											class="btn btn-action btn-icon-split"
											@click="GuardarContrasena"
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
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";

const diferentThanZero = (value) => value != 0;
export default {
	components: {
		layout,
		headerClose,
	},
	props: {
		usuarios: Array,
		agencias: Array,
		cargos: Array,
		distritos: Array,
		provincias: Array,
		horarios: Array,
		departamentos: Array,
		clave_permiso: Number,
	},

	data() {
		return {
			windowWidth: window.innerWidth,

			submited: false,
			submited2: false,

			clave_transaccion: null,

			title_modal: null,
			txtdni_type: "number",
			distritos_filtrados: this.distritos,
			provincias_filtradas: this.provincias,
			form_datos_usuario: {
				modo: null,
				dni: null,
				usuario: null,
				nombres: null,
				apellido_paterno: null,
				apellido_materno: null,
				sexo: null,
				direccion: null,
				distrito_id: null,
				provincia_id: null,
				departamento_id: null,
				fecha_nacimiento: null,
				telefono: null,
				correo_corporativo: null,
				cargo_id: null,
				agencia_id: null,
				horario_id: null,
				usuario_real: null,
			},

			modo_2: null,

			frmDatosContraseña: {
				dni: null,
				usuario: null,
				clave_nueva: null,
				clave_repetida: null,
				habilitado: true,
			},
		};
	},

	validations() {
		if (this.form_datos_usuario.modo == "NUEVO") {
			return {
				form_datos_usuario: {
					dni: { required },
					usuario: { required },
					nombres: { required },
					apellido_paterno: { required },
					apellido_materno: { required },
					sexo: { noZero: diferentThanZero },
					direccion: { required },
					distrito_id: { noZero: diferentThanZero },
					provincia_id: { noZero: diferentThanZero },
					departamento_id: { noZero: diferentThanZero },
					fecha_nacimiento: { required },
					telefono: { required },
					correo_corporativo: { required },
					cargo_id: { noZero: diferentThanZero },
					agencia_id: { noZero: diferentThanZero },
					horario_id: { noZero: diferentThanZero },
				},
			};
		} else if (this.form_datos_usuario.modo == "EDITAR") {
			return {
				form_datos_usuario: {
					usuario: { required },
					nombres: { required },
					apellido_paterno: { required },
					apellido_materno: { required },
					sexo: { noZero: diferentThanZero },
					direccion: { required },
					distrito_id: { noZero: diferentThanZero },
					provincia_id: { noZero: diferentThanZero },
					departamento_id: { noZero: diferentThanZero },
					fecha_nacimiento: { required },
					telefono: { required },
					correo_corporativo: { required },
					cargo_id: { noZero: diferentThanZero },
					agencia_id: { noZero: diferentThanZero },
				},
			};
		} else if (this.form_datos_usuario.modo == "CLAVE") {
			return {
				frmDatosContraseña: {
					clave_nueva: { required },
					clave_repetida: { required },
				},
			};
		}
	},

	mounted() {
		// window.addEventListener("resize", () => {
		//   this.windowWidth = window.innerWidth;
		//   this.AñadirResponsive();
		// });
		// this.AñadirResponsive();
		this.TablaGestionUsuarios();
	},

	watch: {
		title_modal(new_val) {
			if (new_val == "EDITAR USUARIO") {
				this.txtdni_type = "text";
			} else if (new_val == "NUEVO USUARIO") {
				this.txtdni_type = "number";
			} else {
				this.txtdni_type = "text";
			}
		},
	},
	methods: {
		// AñadirResponsive() {
		//   if (this.windowWidth < 1534) {
		//     if (!$("#tblUsuarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblUsuarios")
		//         .classList.add("table-responsive");
		//     }
		//   } else {
		//     if ($("#tblUsuarios").hasClass("table-responsive")) {
		//       document
		//         .getElementById("tblUsuarios")
		//         .classList.remove("table-responsive");
		//     }
		//   }
		// },
		TablaGestionUsuarios() {
			this.$nextTick(() => {
				var table = $("#tblUsuarios").DataTable({
					scrollY: "350px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,

					scrollCollapse: true,
					paging: false,
					fixedHeader: true,
					info: true,

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

				$("#slcAgencias").change(function () {
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

		NuevoUsuario() {
			this.submited = false;
			this.submited2 = false;

			this.title_modal = "NUEVO USUARIO";
			this.form_datos_usuario.dni = null;
			this.form_datos_usuario.usuario = null;
			this.form_datos_usuario.nombres = null;
			this.form_datos_usuario.apellido_paterno = null;
			this.form_datos_usuario.apellido_materno = null;
			this.form_datos_usuario.sexo = 0;
			this.form_datos_usuario.direccion = null;
			this.form_datos_usuario.distrito_id = 0;
			this.form_datos_usuario.provincia_id = 0;
			this.form_datos_usuario.departamento_id = 0;
			this.form_datos_usuario.fecha_nacimiento = null;
			this.form_datos_usuario.telefono = null;
			this.form_datos_usuario.correo_corporativo = null;
			this.form_datos_usuario.cargo_id = 0;
			this.form_datos_usuario.agencia_id = 0;
			this.form_datos_usuario.horario_id = 0;

			this.form_datos_usuario.modo = "NUEVO";

			$("#mdlDatosUsuario").css("display", "block");
			$("#datosUsuario1-tab").tab("show");
		},
		EditarUsuario(usuario) {
			this.submited = false;
			this.submited2 = false;

			this.title_modal = "EDITAR USUARIO";
			this.form_datos_usuario.dni = usuario.dni;
			this.form_datos_usuario.usuario = usuario.usuario;
			this.form_datos_usuario.nombres = usuario.nombres;
			this.form_datos_usuario.apellido_paterno = usuario.apellido_paterno;
			this.form_datos_usuario.apellido_materno = usuario.apellido_materno;
			this.form_datos_usuario.sexo = usuario.sexo;
			this.form_datos_usuario.direccion = usuario.direccion;
			this.form_datos_usuario.distrito_id = usuario.distrito_id;
			this.form_datos_usuario.provincia_id = usuario.provincia_id;
			this.form_datos_usuario.departamento_id = usuario.departamento_id;
			this.form_datos_usuario.fecha_nacimiento = usuario.fecha_nacimiento;
			this.form_datos_usuario.telefono = usuario.telefono;
			this.form_datos_usuario.correo_corporativo = usuario.correo_corporativo;
			this.form_datos_usuario.cargo_id = usuario.cargo_id;
			this.form_datos_usuario.agencia_id = usuario.agencia_id;
			this.form_datos_usuario.usuario_real = usuario.usuario_real;

			this.form_datos_usuario.modo = "EDITAR";
			$("#mdlDatosUsuario").css("display", "block");
			$("#datosUsuario1-tab").tab("show");
			$("#btnCancelar").click(function () {
				$("#mdlDatosUsuario").css("display", "none");

				$("#datosUsuario1-tab").tab("show");
			});
		},
		CambiarClave(usuario) {
			this.submited = false;
			this.submited2 = false;

			this.title_modal = "CAMBIAR CLAVE DE USUARIO";
			this.form_datos_usuario.modo = "CLAVE";

			this.frmDatosContraseña.dni = usuario.dni;
			this.frmDatosContraseña.usuario = usuario.usuario;
			this.frmDatosContraseña.clave_nueva = "";
			this.frmDatosContraseña.clave_repetida = "";

			$("#mdlCambiarClave").css("display", "block");
		},
		RandomClave() {
			let self = this;
			axios
				.post(route("gth.usu.usuarios_gestion.generar_clave"))
				.then(function (response) {
					self.clave_transaccion = response.data;
					self.frmDatosContraseña.clave_nueva = self.clave_transaccion;
					self.frmDatosContraseña.clave_repetida = self.clave_transaccion;
				});
		},
		GuardarContrasena() {
			let self = this;

			this.submited2 = true;
			if (this.$v.frmDatosContraseña.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
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
					preConfirm: (result) => {
						let data = new FormData();

						data.append("clave_nueva", this.frmDatosContraseña.clave_nueva);
						data.append("habilitado", this.frmDatosContraseña.habilitado);
						data.append(
							"clave_repetida",
							this.frmDatosContraseña.clave_repetida
						);
						data.append("dni", this.frmDatosContraseña.dni);

						// this.$inertia.post(
						//   route("gth.usu.usuarios_gestion.guardar_clave"),
						//   data
						// );
						axios
							.post(route("gth.usu.usuarios_gestion.guardar_clave"), data)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "INCORRECTO_REPETICION") {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Las contraseñas no coinciden",
									});

									self.frmDatosContraseña.clave_repetida = "";

									return false;
								} else if (resultado == "CORRECTO") {
									Swal.fire({
										icon: "success",
										title: "Contraseña Restablecida",
										text: "Tu contraseña ha sido cambiada satisfactoriamente!",
										allowOutsideClick: false,
										confirmButtonText: "Aceptar",
									}).then(function () {
										self.$inertia.get(route("gth.usu.usuarios_gestion"));
									});
								}
							});
					},
				});
			}
		},
		FiltrarProvincias(e) {
			let id_departamento = e.target.value;

			if (!id_departamento == 0) {
				this.provincias_filtradas = this.provincias.filter(
					(item) => item.departamento_id == id_departamento
				);
				this.distritos_filtrados = this.distritos.filter(
					(item) => item.department_id == id_departamento
				);
			} else {
				this.provincias_filtradas = this.provincias;
				this.distritos_filtrados = this.distritos;
			}

			this.form_datos_usuario.provincia_id = 0;
			this.form_datos_usuario.distrito_id = 0;
		},
		FiltrarDistritos(e) {
			let id_provincia = e.target.value;

			if (!id_provincia == 0) {
				this.distritos_filtrados = this.distritos.filter(
					(item) => item.provincia_id == id_provincia
				);
			} else {
				this.distritos_filtrados = this.distritos;
			}

			this.form_datos_usuario.distrito_id = 0;
		},
		CesarUsuario() {
			self = this;
			Swal.fire({
				title: "CESAR USUARIO",
				text: "Ingrese el motivo de cese",
				input: "text",
				inputAttributes: {
					autocapitalize: "off",
				},
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				showLoaderOnConfirm: true,
				preConfirm: (motivo_cese) => {
					if (motivo_cese == "") {
						Swal.showValidationMessage(`*Campo obligatorio`);
						return false;
					} else {
						let data = new FormData();
						data.append("dni", self.form_datos_usuario.dni);
						data.append("motivo_cese", motivo_cese);

						axios
							.post(route("gth.usu.usuarios_gestion.cesar"), data)
							.then(function (response) {
								let resultado = response.data;
								if (resultado == "EXITO") {
									Swal.fire({
										icon: "success",
										title: "¡EXITO!",
										text: "Usuario cesado",
										allowOutsideClick: false,
										preConfirm: (result) => {
											$("#mdlDatosUsuario").css("display", "none");

											self.$inertia.get(route("gth.usu.usuarios_gestion"));
										},
									});
								} else {
									Swal.fire({
										icon: "error",
										title: "¡Ups!",
										text: "Algo salió mal",
									});
								}
							});
					}
				},
			});
		},
		async ValidarUsuario() {
			this.submited = true;
			let self = this;
			if (this.$v.form_datos_usuario.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			}
			let data = new FormData();

			// console.log(this.form_datos_usuario);

			data.append("modo", this.form_datos_usuario.modo);
			data.append("dni", this.form_datos_usuario.dni);
			data.append("usuario", this.form_datos_usuario.usuario);

			await axios
				// this.$inertia
				.post(route("gth.usu.usuarios_gestion.verificar"), data)
				.then(function (response) {
					var resultado = response.data;

					self.modo_2 = "NORMAL";

					if (resultado == "INCORRECTO_D") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "El DNI ingresado, ya está registrado, intente nuevamente.",
						});
						return false;
					} else if (resultado == "INCORRECTO_U") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "El USUARIO ingresado, ya está registrado, intente nuevamente.",
						});
						return false;
					} else {
						self.GuardarUsuario();
					}
				});

			//   self.GuardarUsuario();
		},

		GuardarUsuario() {
			let self = this;

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
					let data = new FormData();

					// console.log(this.form_datos_usuario);

					data.append(
						"form_datos_usuario",
						JSON.stringify(self.form_datos_usuario)
					);

					data.append("modo_2", this.modo_2);

					self.$inertia.post(route("gth.usu.usuarios_gestion.guardar"), data, {
						preserveScroll: true,
						onStart: (visit) => {
							let timerInterval;
							Swal.fire({
								title: "ESPERE POR FAVOR...",
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
								preConfirm: (result) => {
									self.submited = false;
									$("#mdlDatosUsuario").css("display", "none");
								},
							});
						},
					});
				} else {
					return false;
				}
			});
		},

		Cerrar() {
			$("#mdlDatosUsuario").css("display", "none");
			$("#datosUsuario1-tab").tab("show");
			$("#mdlCambiarClave").css("display", "none");
		},
	},
};
</script>

<style >
.slot-gestion {
	width: 70% !important;
	margin-left: 15% !important;
}

.mdlDatosUsuario {
	margin-top: 2%;
}
.mdlCambiarClave {
	width: 30% !important;
	margin-left: 35% !important;

	margin-top: 2%;
}
@media (max-width: 900px) {
	.slot-gestion {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlDatosUsuario {
		margin-top: 20%;
	}
	.mdlCambiarClave {
		margin-top: 20%;
	}
}
</style>
