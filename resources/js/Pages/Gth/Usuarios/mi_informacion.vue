<template>
	<layout ref="layout">
		<div class="slot_body slot-mi-informacion" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MIS DATOS PERSONALES'"></headerClose>
					<div class="card-title">INFORMACIÓN DE USUARIO</div>
					<div class="card-body card-block">
						<div class="text-left">
							<button
								class="btn btn-action btn-icon-split"
								id="btnEditarDatos"
								@click="ModoEditar"
								:disabled="frmDatosUsuario.modo == 'EDITAR'"
							>
								<span class="icon text-white-50">
									<i class="fas fa-edit" style="color: white"></i>
								</span>
								<span class="text">EDITAR DATOS</span>
							</button>
							<button
								class="btn btn-action btn-icon-split"
								id="btnCambiarClave"
								@click="CambiarClave"
							>
								<span class="icon text-white-50">
									<i class="fas fa-lock" style="color: white"></i>
								</span>
								<span class="text">CAMBIAR CLAVE</span>
							</button>
						</div>
						<br />
						<div class="card-body card-block">
							<div class="form-row">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">DNI</span>
									</div>
									<input
										type="text"
										class="form-control center"
										id="inpdni"
										v-model="frmDatosUsuario.dni"
										:disabled="true"
									/>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">USUARIO</span>
									</div>
									<input
										type="text"
										id="inpUsuario"
										name="usuario"
										class="form-control center"
										v-model="frmDatosUsuario.usuario"
										:disabled="true"
										@focus="hidenav()"
										@blur="shownav()"
									/>
									<div
										v-if="submited && !$v.frmDatosUsuario.usuario.required"
										style="color: red; font-size: 12px"
									>
										*Campo obligatorio
									</div>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>FECHA NACIMIENTO</span
										>
									</div>
									<span
										v-if="
											submited && !$v.frmDatosUsuario.fecha_nacimiento.required
										"
										class="span-error-message"
									>
										*
									</span>
									<input
										type="date"
										id="dtpFechaNacimiento"
										name="fechaNacimiento"
										class="form-control center"
										v-model="frmDatosUsuario.fecha_nacimiento"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
							<div class="form-row mt-2">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>AP. PATERNO</span
										>
									</div>

									<textarea
										type="text"
										id="inpapellido_paterno"
										name="apellido_paterno"
										class="form-control"
										v-model="frmDatosUsuario.apellido_paterno"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									></textarea>
									<span
										v-if="
											submited && !$v.frmDatosUsuario.apellido_paterno.required
										"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>AP. MATERNO</span
										>
									</div>

									<textarea
										type="text"
										class="form-control"
										id="inpapellido_materno"
										name="apellido_materno"
										v-model="frmDatosUsuario.apellido_materno"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									></textarea>
									<span
										v-if="
											submited && !$v.frmDatosUsuario.apellido_materno.required
										"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">NOMBRES</span>
									</div>

									<textarea
										type="text"
										class="form-control"
										id="inpNombre"
										name="nombres"
										v-model="frmDatosUsuario.nombres"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									></textarea>
									<span
										v-if="submited && !$v.frmDatosUsuario.nombres.required"
										class="span-error-message"
									>
										*
									</span>
								</div>
							</div>
							<div class="form-row mt-2">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">GÉNERO</span>
									</div>

									<select
										class="form-control center"
										id="slcGenero"
										name="genero"
										v-model="frmDatosUsuario.sexo"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
									>
										<option value="0">Seleccione...</option>
										<option value="F">Femenino</option>
										<option value="M">Masculino</option>
									</select>
									<span
										v-if="submited && !$v.frmDatosUsuario.sexo.noZero"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>DIRECCIÓN</span
										>
									</div>

									<textarea
										type="text"
										id="txtDireccion"
										name="direccion"
										class="form-control center"
										v-model="frmDatosUsuario.direccion"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									></textarea>
									<span
										v-if="submited && !$v.frmDatosUsuario.direccion.required"
										class="span-error-message"
									>
										*
									</span>
								</div>
							</div>

							<div class="form-row mt-2">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>DEPARTAMENTO</span
										>
									</div>

									<select
										type="text"
										class="form-control center"
										id="slcDepartamentos"
										name="departamento"
										@change="FiltrarProvincias"
										v-model="frmDatosUsuario.departamento_id"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
									>
										<option value="0" disabled>Seleccione...</option>
										<option
											v-for="departamento in departamentos"
											:key="departamento.id"
											:value="departamento.id"
										>
											{{ departamento.departamento }}
										</option>
									</select>
									<span
										v-if="
											submited && !$v.frmDatosUsuario.departamento_id.noZero
										"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title"
											>PROVINCIA</span
										>
									</div>

									<select
										type="text"
										class="form-control center"
										id="slcProvincias"
										name="provincia"
										@change="FiltrarDistritos"
										v-model="frmDatosUsuario.provincia_id"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
									>
										<option value="0" disabled>Seleccione...</option>
										<option
											v-for="provincia in provincias_filtradas"
											:key="provincia.id"
											:value="provincia.id"
										>
											{{ provincia.provincia }}
										</option>
									</select>
									<span
										v-if="submited && !$v.frmDatosUsuario.provincia_id.noZero"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">DISTRITO</span>
									</div>

									<select
										type="text"
										class="form-control center"
										id="slcDistritos"
										name="distrito"
										v-model="frmDatosUsuario.distrito_id"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
									>
										<option value="0" disabled>Seleccione...</option>
										<option
											v-for="distrito in distritos_filtrados"
											:key="distrito.id"
											:value="distrito.id"
										>
											{{ distrito.distrito }}
										</option>
									</select>
									<span
										v-if="submited && !$v.frmDatosUsuario.distrito_id.noZero"
										class="span-error-message"
									>
										*
									</span>
								</div>
							</div>
							<div class="form-row mt-2">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">TELÉFONO</span>
									</div>

									<input
										type="number"
										class="form-control center"
										maxlength="9"
										id="inpTelefono"
										name="telefono"
										oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
										v-model="frmDatosUsuario.telefono"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									/>
									<span
										v-if="submited && !$v.frmDatosUsuario.telefono.required"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">CORREO</span>
									</div>

									<input
										type="text"
										id="inpCorreo_principal"
										name="correoPrincipal"
										class="form-control"
										v-model="frmDatosUsuario.correo_corporativo"
										:disabled="frmDatosUsuario.modo == 'VISTA'"
										@focus="hidenav()"
										@blur="shownav()"
									/>
									<span
										v-if="
											submited &&
											!$v.frmDatosUsuario.correo_corporativo.required
										"
										class="span-error-message"
									>
										*
									</span>
								</div>
							</div>
							<div class="form-row mt-2">
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">AGENCIA</span>
									</div>
									<input
										type="text"
										class="form-control center"
										id="inpAgencia"
										name="agencia"
										v-model="frmDatosUsuario.nombre_agencia"
										:disabled="true"
									/>
								</div>
								<div class="input-group col-md-4">
									<div class="input-group-prepend">
										<span class="input-group-text prepend-title">CARGO</span>
									</div>
									<input
										type="text"
										class="form-control"
										id="inpCargo"
										name="cargo"
										v-model="frmDatosUsuario.cargo"
										:disabled="true"
									/>
								</div>
							</div>
						</div>

						<br />
						<div class="text-center">
							<div class="btn-group" role="group">
								<button
									class="btn btn-action btn-icon-split"
									id="btnGuardarCambios"
									@click="GuardarCambios"
									:disabled="frmDatosUsuario.modo == 'VISTA'"
								>
									<span class="icon text-white-50">
										<i class="fas fa-save" style="color: white"></i>
									</span>
									<span class="text">GUARDAR</span>
								</button>

								<button
									class="btn btn-cancel btn-icon-split"
									id="btnCancelar"
									@click="CancelarCambios"
									:disabled="frmDatosUsuario.modo == 'VISTA'"
								>
									<span class="icon text-white-50">
										<i class="fas fa-times" style="color: white"></i>
									</span>
									<span class="text">CANCELAR</span>
								</button>
							</div>
						</div>
					</div>

					<div class="card-title">HORARIO ASIGNADO</div>
					<div class="card-body card-block">
						<table
							class="table table-hover"
							id="tblHorarioAsignado"
							width="100%"
						>
							<thead>
								<tr>
									<th style="width: 70px !important">NOMBRE_HORARIO</th>
									<th>INGRESO_MAÑANA<br />(L-V)</th>
									<th>SALIDA_MAÑANA<br />(L-V)</th>
									<th>INGRESO_TARDE<br />(L-V)</th>
									<th>SALIDA_TARDE<br />(L-V)</th>
									<th>INGRESO_MAÑANA<br />(SAB)</th>
									<th>SALIDA_MAÑANA<br />(SAB)</th>
									<th>TOLERANCIA_HORARIO</th>
									<th>TOLERANCIA_PERSONAL</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].horario }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].hora_entrada_mañana }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].hora_salida_mañana }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].hora_entrada_tarde }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].hora_salida_tarde }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].hora_entrada_mañana_s }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].hora_salida_mañana_s }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].toleranciaHorario }}
									</td>
									<td class="table-bordered" align="center">
										{{ mi_usuario[0].tolerancia_personal }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="mdlCambiarClave" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-20 mdlCambiarClave">
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
								<div class="form-group">
									<label class="label-title">CLAVE ACTUAL</label>
									<span
										v-if="
											submited && !$v.frmDatosContraseña.clave_actual.required
										"
										class="span-error-message"
									>
										*
									</span>

									<input
										type="text"
										class="form-control pw"
										v-model="frmDatosContraseña.clave_actual"
									/>
								</div>

								<div class="form-group">
									<label class="label-title">CLAVE NUEVA</label>
									<span
										v-if="
											submited && !$v.frmDatosContraseña.clave_nueva.required
										"
										class="span-error-message"
									>
										*
									</span>
									<input
										type="text"
										class="form-control pw"
										v-model="frmDatosContraseña.clave_nueva"
									/>
								</div>

								<div class="form-group">
									<label class="label-title">REPITA LA CLAVE</label>
									<span
										v-if="
											submited && !$v.frmDatosContraseña.clave_repetida.required
										"
										class="span-error-message"
									>
										*
									</span>
									<input
										type="text"
										class="form-control pw"
										v-model="frmDatosContraseña.clave_repetida"
									/>
								</div>
								<hr />
								<div class="text-right">
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
	</layout>
</template>

<script>
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";
const diferentThanZero = (value) => value != 0;
export default {
	components: { layout, headerClose },
	props: {
		mi_usuario: Array,
		distritos: Array,
		provincias: Array,
		departamentos: Array,
	},
	data() {
		return {
			submited: false,
			distritos_filtrados: this.distritos,
			provincias_filtradas: this.provincias,
			frmDatosContraseña: {
				clave_actual: null,

				clave_nueva: null,
				clave_repetida: null,
			},
			modo_clave: null,
			modo_validar: null,

			title_modal: null,

			frmDatosUsuario: {
				modo: "VISTA",
				dni: this.mi_usuario[0].dni,
				usuario: this.mi_usuario[0].usuario,
				nombres: this.mi_usuario[0].nombres,
				apellido_paterno: this.mi_usuario[0].apellido_paterno,
				apellido_materno: this.mi_usuario[0].apellido_materno,
				sexo: this.mi_usuario[0].sexo,
				direccion: this.mi_usuario[0].direccion,
				distrito_id: this.mi_usuario[0].distrito_id,
				provincia_id: this.mi_usuario[0].provincia_id,
				departamento_id: this.mi_usuario[0].departamento_id,
				fecha_nacimiento: this.mi_usuario[0].fecha_nacimiento,
				telefono: this.mi_usuario[0].telefono,
				correo_corporativo: this.mi_usuario[0].correo_corporativo,
				cargo: this.mi_usuario[0].cargo,
				nombre_agencia: this.mi_usuario[0].nombre_agencia,
			},
		};
	},
	validations: {
		frmDatosContraseña: {
			clave_actual: { required },
			clave_nueva: { required },
			clave_repetida: { required },
		},
		frmDatosUsuario: {
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
		},
	},
	mounted() {
		self = this;
		// if (screen.width < 1000) {
		//   $("#tblHorarioAsignado").addClass("table-responsive");
		// }
		this.TablaDatosHorario();

		this.provincias_filtradas = this.provincias.filter(
			(item) => item.departamento_id == self.frmDatosUsuario.departamento_id
		);
		this.distritos_filtrados = this.distritos.filter(
			(item) => item.provincia_id == self.frmDatosUsuario.provincia_id
		);
	},
	methods: {
		TablaDatosHorario() {
			self = this;
			this.$nextTick(() => {
				var table = $("#tblHorarioAsignado").DataTable({
					scrollY: "300px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,

					language: {
						retrieve: true,
						decimal: "",
						emptyTable: "No hay datos disponibles en la tabla",
						info: "",
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
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
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

			this.frmDatosUsuario.provincia_id = 0;
			this.frmDatosUsuario.distrito_id = 0;
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

			this.frmDatosUsuario.distrito_id = 0;
		},
		ModoEditar() {
			this.frmDatosUsuario.modo = "EDITAR";
		},

		CambiarClave() {
			this.title_modal = "CAMBIAR CLAVE DE ACCESO";
			this.frmDatosContraseña.clave_actual = "";
			this.frmDatosContraseña.clave_nueva = "";

			this.frmDatosContraseña.clave_repetida = "";

			$("#mdlCambiarClave").css("display", "block");
		},

		GuardarContrasena() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosContraseña.$invalid) {
				return false;
			} else {
				let data = new FormData();
				data.append("clave_actual", this.frmDatosContraseña.clave_actual);
				data.append("usuario", this.frmDatosUsuario.usuario);
				data.append("clave_nueva", this.frmDatosContraseña.clave_nueva);
				data.append("clave_repetida", this.frmDatosContraseña.clave_repetida);
				data.append("dni", this.frmDatosUsuario.dni);

				//   this.$inertia.post(
				//     route("gth.usu.mis_datos_personales.guardar_clave"),
				//     data
				//   );
				axios
					.post(route("gth.usu.mis_datos_personales.guardar_clave"), data)
					.then(function (response) {
						let resultado = response.data;
						if (resultado == "INCORRECTO_CLAVE") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "La contraseña actual es incorrecta",
							});
							self.frmDatosContraseña.clave_actual = "";
							self.frmDatosContraseña.clave_nueva = "";

							self.frmDatosContraseña.clave_repetida = "";

							return false;
						} else if (resultado == "INCORRECTO_REPETICION") {
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
								self.$inertia.get(route("gth.usu.mis_datos_personales"));
							});
						}
					});
			}
		},
		Cerrar(modal) {
			$("#" + modal).css("display", "none");
		},
		GuardarCambios() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosUsuario.$invalid) {
				return false;
			} else {
				axios
					.post(route("gth.usu.mis_datos_personales.verificar"), {
						usuario: self.frmDatosUsuario.usuario,
					})
					.then(function (response) {
						let resultado = response.data;
						if (resultado == "INCORRECTO") {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "El USUARIO ingresado, ya está registrado, intente nuevamente.",
							});
							return false;
						} else if (resultado == "CORRECTO") {
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
									axios
										.post(
											route("gth.usu.mis_datos_personales.guardar"),
											self.frmDatosUsuario
										)
										.then(function (response) {
											let resultado = response.data;
											if (resultado == "EXITO") {
												Swal.fire({
													icon: "success",
													title: "¡EXITO!",
													text: "Información registrada",
													allowOutsideClick: false,
													preConfirm: (result) => {
														self.$inertia.get(
															route("gth.usu.mis_datos_personales")
														);
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
								},
							});
						}
					});
			}
		},
		CancelarCambios() {
			this.frmDatosUsuario.modo = "VISTA";
		},
	},
};
</script>

<style >
.slot-mi-informacion {
	width: 62% !important;
	margin-left: 19% !important;
}

.mdlCambiarClave {
	margin-top: 2%;
}

@media (max-width: 900px) {
	.slot-mi-informacion {
		width: 98% !important;
		margin-left: 1% !important;
	}
	.mdlCambiarClave {
		margin-top: 20%;
	}
}
input.pw {
	-webkit-text-security: disc;
}
</style>
