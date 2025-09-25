<template>
	<div id="mdlDatosPersonales" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-55 mdlDatosPersonales">
			<div class="content" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>{{ title_modal }}</strong>

						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%"
							@click="CerrarModalRegistro"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>
					<!-- <div class="card-title">INFORMACIÓN PERSONAL</div> -->
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item" role="presentation">
								<a
									class="nav-link active tab-title"
									id="datosCliente1-tab"
									data-toggle="tab"
									href="#datosCliente1"
									role="tab"
									aria-controls="datosCliente1"
									aria-selected="true"
									>DATOS PRINCIPALES
								</a>
							</li>
							<li class="nav-item" role="presentation">
								<a
									class="nav-link tab-title"
									id="datosCliente2-tab"
									data-toggle="tab"
									href="#datosCliente2"
									role="tab"
									aria-controls="datosCliente2"
									aria-selected="false"
									>DIRECCIÓN Y TELÉFONOS</a
								>
							</li>
							<li class="nav-item" role="presentation">
								<a
									class="nav-link tab-title"
									id="datosCliente3-tab"
									data-toggle="tab"
									href="#datosCliente3"
									role="tab"
									aria-controls="datosCliente3"
									aria-selected="false"
									>DNI</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="datosCliente1"
								role="tabpanel"
								aria-labelledby="datosCliente1-tab"
							>
								<fieldset>
									<legend style="font-size: 10px !important">
										DATOS PERSONALES
									</legend>
									<div class="form-row">
										<div class="form-row col-md-12">
											<div class="form-group col-md-3 col-5">
												<label class="label-title">DNI</label>
												<span
													class="span-error-message"
													v-if="
														submited &&
														frmDatosCliente.modo == 'NUEVO' &&
														!$v.frmDatosCliente.dni.required
													"
												>
													*
												</span>
												<form autocomplete="off">
													<div class="input-group">
														<input
															type="number"
															class="form-control center"
															:class="[
																submited
																	? $v.frmDatosCliente.dni.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															min="0"
															step="1"
															lang="en"
															maxlength="8"
															oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
															v-model="frmDatosCliente.dni"
															:disabled="
																frmDatosCliente.apellido_paterno != null
															"
														/>
														<div class="input-group-append">
															<button
																class="btn btn-action"
																type="button"
																@click="BuscarReniec"
																:disabled="
																	frmDatosCliente.apellido_paterno != null
																"
															>
																<span class="icon text-white">
																	<i class="pi pi-search"></i>
																</span>
															</button>
														</div>
													</div>
												</form>
											</div>
											<div class="form-group col-md-3 col-7">
												<label class="label-title">APELLIDO PATERNO</label>
												<span
													v-if="
														submited &&
														!$v.frmDatosCliente.apellido_paterno.required
													"
													class="span-error-message"
												>
													*
												</span>
												<form autocomplete="off">
													<textarea
														type="text"
														class="form-control"
														:value="frmDatosCliente.apellido_paterno"
														disabled
														readonly
													>
													</textarea>

													<!-- <textarea
														type="text"
														rows="1"
														class="form-control mayus"
														:class="[
															submited
																? $v.frmDatosCliente.apellido_paterno.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmDatosCliente.apellido_paterno"
														:disabled="
															frmDatosCliente.modo == 'NO-EDITAR' ||
															frmDatosCliente.modo == 'PARIENTE_AVAL'
														"
														v-if="frmDatosCliente.cantidad_creditos == 0"
													></textarea> -->
												</form>
											</div>
											<div class="form-group col-md-3 col-5">
												<label class="label-title">APELLIDO MATERNO</label>
												<span
													v-if="
														submited &&
														!$v.frmDatosCliente.apellido_materno.required
													"
													class="span-error-message"
												>
													*
												</span>
												<form autocomplete="off">
													<textarea
														type="text"
														class="form-control"
														:value="frmDatosCliente.apellido_materno"
														disabled
														readonly
													>
													</textarea>
													<!-- <textarea
														type="text"
														rows="1"
														class="form-control mayus"
														:class="[
															submited
																? $v.frmDatosCliente.apellido_materno.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmDatosCliente.apellido_materno"
														:disabled="
															frmDatosCliente.modo == 'NO-EDITAR' ||
															frmDatosCliente.modo == 'PARIENTE_AVAL'
														"
														v-if="frmDatosCliente.cantidad_creditos == 0"
													></textarea> -->
												</form>
											</div>
											<div class="form-group col-md-3 col-7">
												<label class="label-title">NOMBRES</label>
												<span
													class="span-error-message"
													v-if="
														submited && !$v.frmDatosCliente.nombres.required
													"
												>
													*
												</span>
												<form autocomplete="off">
													<textarea
														type="text"
														class="form-control"
														:value="frmDatosCliente.nombres"
														disabled
														readonly
													>
													</textarea>
													<!-- <textarea
														type="text"
														rows="1"
														class="form-control mayus"
														:class="[
															submited
																? $v.frmDatosCliente.nombres.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmDatosCliente.nombres"
														:disabled="
															frmDatosCliente.modo == 'NO-EDITAR' ||
															frmDatosCliente.modo == 'PARIENTE_AVAL'
														"
														v-if="frmDatosCliente.cantidad_creditos == 0"
													></textarea> -->
												</form>
											</div>
										</div>
										<div class="form-row col-md-12">
											<div class="form-group col-md-4 col-6">
												<label class="label-title">FECHA DE NACIMIENTO</label>

												<input
													type="date"
													class="form-control center"
													:class="[
														submited
															? $v.frmDatosCliente.fecha_nacimiento.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmDatosCliente.fecha_nacimiento"
													:disabled="
														frmDatosCliente.modo == 'NO-EDITAR' ||
														frmDatosCliente.modo == 'PARIENTE_AVAL'
													"
												/>
											</div>
											<div class="form-group col-md-3 col-6">
												<label class="label-title">ESTADO CIVIL</label>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmDatosCliente.estado_civil.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmDatosCliente.estado_civil"
													:disabled="
														frmDatosCliente.modo == 'NO-EDITAR' ||
														frmDatosCliente.modo == 'PARIENTE_AVAL'
													"
												>
													<option value="0" disabled>Seleccione...</option>
													<option value="Soltero">SOLTERO</option>
													<option value="Casado">CASADO</option>
													<option value="Conviviente">CONVIVIENTE</option>
													<option value="Divorciado">DIVORCIADO</option>
													<option value="Viudo">VIUDO</option>
												</select>
											</div>
											<div class="form-group col-md-3 col-6">
												<label class="label-title">GÉNERO</label>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmDatosCliente.sexo.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmDatosCliente.sexo"
													:disabled="
														frmDatosCliente.modo == 'NO-EDITAR' ||
														frmDatosCliente.modo == 'PARIENTE_AVAL'
													"
												>
													<option value="0" disabled>Seleccione...</option>
													<option value="F">FEMENINO</option>
													<option value="M">MASCULINO</option>
												</select>
											</div>

											<div class="form-group col-md-2 col-6">
												<label class="label-title">HIJOS</label>
												<input
													type="number"
													min="0"
													step="1"
													class="form-control center"
													v-model.number="frmDatosCliente.hijos"
													onkeypress="return event.charCode >= 48 && event.charCode <= 57"
													:disabled="
														frmDatosCliente.modo == 'NO-EDITAR' ||
														frmDatosCliente.modo == 'PARIENTE_AVAL'
													"
												/>
											</div>
										</div>
										<div class="form-row col-md-12">
											<div class="form-group col-md-4 col-5">
												<label class="label-title">AGENCIA</label>
												<input
													type="text"
													class="form-control center"
													:value="frmDatosCliente.agencia"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-8 col-7">
												<label class="label-title">CORREO ELECTRÓNICO</label>
												<form autocomplete="off">
													<input
														type="text"
														class="form-control"
														v-model="frmDatosCliente.correo_electronico"
														:disabled="
															frmDatosCliente.modo == 'NO-EDITAR' ||
															frmDatosCliente.modo == 'PARIENTE_AVAL'
														"
													/>
												</form>
											</div>
										</div>
									</div>
								</fieldset>

								<fieldset>
									<legend style="font-size: 10px !important">
										INFORMACIÓN CREDITICIA
									</legend>
									<div class="form-row col-md-12">
										<div class="form-group col-md-4 col-6">
											<label class="label-title">EXPEDIENTE</label>

											<input
												type="text"
												class="form-control center"
												:value="
													frmDatosCliente.codigo_expediente == null
														? '-'
														: frmDatosCliente.codigo_expediente
												"
												:disabled="true"
											/>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">ASESOR</label>

											<select
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.asesor_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosCliente.asesor_id"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL' ||
													(frmDatosCliente.modo != 'NUEVO' &&
														!permiso_editar_asesor)
												"
											>
												<option value="0" disabled>Seleccione...</option>
												<option
													v-for="(asesor, index) in lista_asesores_promotores"
													:key="index"
													:value="asesor.dni"
													selected
												>
													{{
														asesor.usuario +
														" - " +
														asesor.nombres +
														" " +
														asesor.apellido_paterno
													}}
												</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">PROMOTOR</label>

											<select
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.promotor_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosCliente.promotor_id"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
											>
												<option value="0" disabled>Seleccione...</option>
												<option
													v-for="(promotor, index) in lista_asesores_promotores"
													:key="index"
													:value="promotor.dni"
													selected
												>
													{{
														promotor.usuario +
														" - " +
														promotor.nombres +
														" " +
														promotor.apellido_paterno
													}}
												</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">CANAL REFERENCIA</label>

											<select
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.canal_referencia.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosCliente.canal_referencia"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
											>
												<option :value="0" disabled>Seleccione...</option>
												<option value="REDES SOCIALES">Redes sociales</option>
												<option value="TV Y RADIO">TV y radio</option>
												<option value="AVISOS PUBLICITARIOS">
													Avisos publicitarios
												</option>
												<option value="RECOMENDACIÓN">Recomendación</option>
												<option value="AGENCIA">Agencia</option>
												<option value="ACTIVACIONES">Activaciones</option>
												<option value="PÁGINA WEB">Página web</option>
												<option value="OTROS">Otros</option>
											</select>
										</div>

										<div class="form-group col-md-4 col-6">
											<label class="label-title">MONTO MÁXIMO</label>

											<input
												type="number"
												min="0"
												step="100"
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.monto_maximo.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												style="
													font-size: 15px;
													color: var(--azulOscuroEmpresarial);
													font-weight: bolder;
												"
												v-model.number="frmDatosCliente.monto_maximo"
												@keyup="Redondear"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL' ||
													(frmDatosCliente.modo != 'NUEVO' &&
														!permiso_editar_monto)
												"
											/>
										</div>
										<div class="form-group col-md-4 col-6">
											<label class="label-title">CENTRAL DE RIESGO</label>

											<select
												class="form-control center"
												v-model="frmDatosCliente.central_riesgo"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
											>
												<option value="NORMAL">NORMAL</option>
												<option value="CPP">CPP</option>
												<option value="DEFICIENTE">DEFICIENTE</option>
												<option value="DUDOSO">DUDOSO</option>
												<option value="PÉRDIDA">PÉRDIDA</option>
												<option value="PÉRDIDA TOTAL">PÉRDIDA TOTAL</option>
											</select>
										</div>
									</div>

									<div class="form-group col-md-12 col-12">
										<label class="label-title"> NOTAS </label>

										<form autocomplete="off">
											<textarea
												type="text"
												class="form-control mayus text-row"
												rows="2"
												v-model="frmDatosCliente.notas"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
											></textarea>
										</form>
									</div>
									<div class="form-group col-md-12 col-12">
										<div class="form-check text-right">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbReporteEquifax"
												v-model="frmDatosCliente.reportar_equifax"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
											/>
											<label class="label-title" for="chbReporteEquifax">
												Reportar a Equifax
											</label>
										</div>
									</div>
								</fieldset>
							</div>
							<div
								class="tab-pane fade"
								id="datosCliente2"
								role="tabpanel"
								aria-labelledby="datosCliente2-tab"
							>
								<form autocomplete="off">
									<div class="form-row">
										<div class="form-group col-md-12">
											<label class="label-title">DIRECCIÓN</label>

											<form autocomplete="off">
												<textarea
													class="form-control mayus"
													:class="[
														submited
															? $v.frmDatosCliente.direccion.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													rows="1"
													v-model="frmDatosCliente.direccion"
													:disabled="
														frmDatosCliente.modo == 'NO-EDITAR' ||
														frmDatosCliente.modo == 'PARIENTE_AVAL'
													"
												></textarea>
											</form>
										</div>
										<div class="form-group col-md-4 col-4">
											<label class="label-title">DEPARTAMENTO</label>

											<select
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.departamento_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												@change="FiltrarProvincias"
												v-model="frmDatosCliente.departamento_id"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
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
										</div>
										<div class="form-group col-md-4 col-4">
											<label class="label-title">PROVINCIA</label>

											<select
												type="text"
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.provincia_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												@change="FiltrarDistritos"
												v-model="frmDatosCliente.provincia_id"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
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
										</div>
										<div class="form-group col-md-4 col-4">
											<label class="label-title">DISTRITO</label>

											<select
												type="text"
												class="form-control center"
												:class="[
													submited
														? $v.frmDatosCliente.distrito_id.$invalid
															? 'is-invalid'
															: 'is-valid'
														: '',
												]"
												v-model="frmDatosCliente.distrito_id"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
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
										</div>

										<div class="form-group col-md-12 col-12">
											<label class="label-title">REFERENCIA</label>

											<form autocomplete="off">
												<textarea
													class="form-control text-row mayus"
													:class="[
														submited
															? $v.frmDatosCliente.referencia_direccion.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													rows="3"
													v-model="frmDatosCliente.referencia_direccion"
													:disabled="
														frmDatosCliente.modo == 'NO-EDITAR' ||
														frmDatosCliente.modo == 'PARIENTE_AVAL'
													"
												/>
											</form>
										</div>

										<fieldset class="form-group col-md-12">
											<legend>
												<label class="label-title">NÚMERO PRINCIPAL</label>
											</legend>
											<div class="form-row">
												<div class="form-group col-md-4 col-4">
													<label class="label-title">NÚMERO</label>

													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">1</span>
														</div>

														<input
															type="number"
															min="0"
															lang="en"
															class="form-control center"
															:class="[
																submited
																	? $v.frmDatosCliente.telefonos.t1.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															maxlength="9"
															oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
															v-model="frmDatosCliente.telefonos.t1"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														/>
													</div>
												</div>

												<div class="form-group col-md-4 col-4">
													<label class="label-title">OPERADOR</label>
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">1</span>
														</div>

														<select
															type="text"
															class="form-control center"
															v-model="frmDatosCliente.telefonos.o1"
															:class="[
																submited
																	? $v.frmDatosCliente.telefonos.o1.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														>
															<option :value="0" disabled>Seleccione...</option>
															<option value="Movistar">MOVISTAR</option>
															<option value="Claro">CLARO</option>
															<option value="Bitel">BITEL</option>
															<option value="Entel">ENTEL</option>
														</select>
													</div>
												</div>

												<div class="form-group col-md-4 col-4">
													<label class="label-title">NOTAS</label>
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">1</span>
														</div>

														<textarea
															type="text"
															rows="1"
															class="form-control mayus"
															:class="[
																submited
																	? $v.frmDatosCliente.telefonos.n1.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmDatosCliente.telefonos.n1"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														></textarea>
													</div>
												</div>
											</div>
										</fieldset>

										<fieldset class="form-group col-md-12">
											<legend>
												<label class="label-title">NÚMEROS SECUNDARIOS</label>
											</legend>
											<div class="form-row">
												<div class="form-group col-md-4 col-4">
													<label class="label-title">NÚMERO</label>
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">2</span>
														</div>
														<input
															type="number"
															min="0"
															lang="en"
															class="form-control center"
															style="max-width: 400px"
															maxlength="9"
															oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
															v-model="frmDatosCliente.telefonos.t2"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														/>
													</div>
												</div>
												<div class="form-group col-md-4 col-4">
													<label class="label-title">OPERADOR</label>
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">2</span>
														</div>
														<select
															type="text"
															class="form-control center"
															v-model="frmDatosCliente.telefonos.o2"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														>
															<option value="0" disabled>Seleccione...</option>
															<option value="Movistar">MOVISTAR</option>
															<option value="Claro">CLARO</option>
															<option value="Bitel">BITEL</option>
															<option value="Entel">ENTEL</option>
														</select>
													</div>
												</div>

												<div class="form-group col-md-4 col-4">
													<label class="label-title">NOTAS</label>
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">2</span>
														</div>

														<textarea
															type="text"
															rows="1"
															class="form-control mayus"
															style="max-width: 500px"
															v-model="frmDatosCliente.telefonos.n2"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														></textarea>
													</div>
												</div>

												<div class="form-group col-md-4 col-4">
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">3</span>
														</div>
														<input
															type="number"
															min="0"
															lang="en"
															class="form-control center"
															style="max-width: 400px"
															maxlength="9"
															oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
															v-model="frmDatosCliente.telefonos.t3"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														/>
													</div>
												</div>
												<div class="form-group col-md-4 col-4">
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">3</span>
														</div>
														<select
															type="text"
															class="form-control center"
															v-model="frmDatosCliente.telefonos.o3"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														>
															<option value="0" disabled>Seleccione...</option>
															<option value="Movistar">MOVISTAR</option>
															<option value="Claro">CLARO</option>
															<option value="Bitel">BITEL</option>
															<option value="Entel">ENTEL</option>
														</select>
													</div>
												</div>
												<div class="form-group col-md-4 col-4">
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">3</span>
														</div>
														<textarea
															type="text"
															rows="1"
															class="form-control mayus"
															style="max-width: 500px"
															v-model="frmDatosCliente.telefonos.n3"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														></textarea>
													</div>
												</div>
												<div class="form-group col-md-4 col-4">
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">4</span>
														</div>
														<input
															type="number"
															min="0"
															lang="en"
															class="form-control center"
															style="max-width: 400px"
															maxlength="9"
															oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
															v-model="frmDatosCliente.telefonos.t4"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														/>
													</div>
												</div>
												<div class="form-group col-md-4 col-4">
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">4</span>
														</div>
														<select
															type="text"
															class="form-control center"
															v-model="frmDatosCliente.telefonos.o4"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														>
															<option value="0" disabled>Seleccione...</option>
															<option value="Movistar">MOVISTAR</option>
															<option value="Claro">CLARO</option>
															<option value="Bitel">BITEL</option>
															<option value="Entel">ENTEL</option>
														</select>
													</div>
												</div>
												<div class="form-group col-md-4 col-4">
													<div class="input-group">
														<div
															class="input-group-prepend"
															v-if="windowWidth > 1000"
														>
															<span class="input-group-text">4</span>
														</div>
														<textarea
															type="text"
															rows="1"
															class="form-control mayus"
															style="max-width: 500px"
															v-model="frmDatosCliente.telefonos.n4"
															:disabled="
																frmDatosCliente.modo == 'NO-EDITAR' ||
																frmDatosCliente.modo == 'PARIENTE_AVAL'
															"
														></textarea>
													</div>
												</div>
											</div>
										</fieldset>

										<!-- ----- -->
									</div>
								</form>
							</div>

							<div
								class="tab-pane fade"
								id="datosCliente3"
								role="tabpanel"
								aria-labelledby="datosCliente3-tab"
							>
								<div class="smartcenter form-row justify-content-md-center">
									<div class="smartcenter form-group justify-content-md-center">
										<div
											class="mt-3 mb-1"
											id="previzualizar"
											style="
												border: 1px solid #ffff;
												width: 600px;
												height: 300px;
											"
										></div>
									</div>
								</div>

								<!-- ----**** ---- -->
								<div class="text-center mb-3">
									<button
										class="btn btn-cancel btn-icon-split"
										@click="Descargar"
										v-if="
											(frmDatosCliente.modo == 'NO-EDITAR' ||
												frmDatosCliente.modo == 'PARIENTE_AVAL') &&
											frmDatosCliente.imagen_dni != null
										"
									>
										<span class="icon text-white">
											<i class="fas fa-download"></i>
										</span>
										<span class="text font-size-layout">Descargar</span>
									</button>
								</div>
								<!-- ---*********---- -->

								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="label-title" id="lblfotoDNI"
											>FOTO DE DNI</label
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
											name="fotoDNI"
											id="inpFoto"
											@change="AgregarDNI"
											:disabled="
												frmDatosCliente.modo == 'NO-EDITAR' ||
												frmDatosCliente.modo == 'PARIENTE_AVAL'
											"
										/>
									</div>

									<div class="form-group col-md-5 offset-md-1">
										<label class="label-title">OBSERVACIONES</label>
										<form autocomplete="off">
											<textarea
												type="text"
												rows="1"
												class="form-control mayus"
												v-model="frmDatosCliente.observaciones"
												:disabled="
													frmDatosCliente.modo == 'NO-EDITAR' ||
													frmDatosCliente.modo == 'PARIENTE_AVAL'
												"
											></textarea>
										</form>
									</div>
								</div>
							</div>

							<hr />

							<div class="text-right">
								<div class="btn-group" role="group">
									<button
										class="btn btn-action btn-icon-split"
										@click="Habilitar_editar"
										v-if="frmDatosCliente.modo == 'NO-EDITAR'"
									>
										<span class="icon text-white">
											<i class="fas fa-edit"></i>
										</span>
										<span class="text">EDITAR</span>
									</button>
									<button
										class="btn btn-action btn-icon-split"
										@click="GuardarCliente"
										v-if="
											frmDatosCliente.modo == 'EDITAR' ||
											frmDatosCliente.modo == 'NUEVO'
										"
									>
										<span class="icon text-white">
											<i class="fas fa-save"></i>
										</span>
										<span class="text">GUARDAR</span>
									</button>
									<button
										class="btn btn-action btn-icon-split"
										@click="SeleccionarParienteAval"
										v-if="frmDatosCliente.modo == 'PARIENTE_AVAL'"
									>
										<span class="icon text-white">
											<i class="fas fa-check"></i>
										</span>
										<span class="text">SELECCIONAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { required, numeric } from "vuelidate/lib/validators";
const api_factiliza_token = import.meta.env.VITE_FACTILIZA_TOKEN;
const api_externa = import.meta.env.VITE_S_API_EXTERNA;
const noZero = (value) => value != 0;
export default {
	props: {
		agencias: Array,
		departamentos: Array,
		provincias: Array,
		distritos: Array,
		agencia_seleccionada: Number,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			mdlParientesAvales:
				this.$parent.$parent.$refs.mdlParientesAvalesNegocios.$refs
					.mdlParientesAvales,
			submited: false,
			title_modal: null,

			distritos_filtrados: this.distritos,
			provincias_filtradas: this.provincias,
			lista_asesores_promotores: [],

			telefonos_actualizados: false,

			frmDatosCliente: {
				modo: null,
				id: 0,
				dni: null,
				agencia: null,
				apellido_paterno: null,
				apellido_materno: null,
				nombres: null,
				fecha_nacimiento: null,
				estado_civil: null,
				sexo: null,
				hijos: 0,
				agencia_id: 0,
				correo_electronico: null,

				codigo_expediente: null,
				asesor_id: 0,
				promotor_id: 0,
				central_riesgo: "NORMAL",
				canal_referencia: 0,

				monto_maximo: 1000,
				notas: null,
				reportar_equifax: false,

				direccion: null,
				distrito_id: 0,
				provincia_id: 0,
				departamento_id: 0,
				referencia_direccion: null,

				imagen_dni: null,
				observaciones: null,
				telefonos: {
					t1: null,
					t2: null,
					t3: null,
					t4: null,
					o1: null,
					o2: null,
					o3: null,
					o4: null,
					n1: null,
					n2: null,
					n3: null,
					n4: null,
				},
				telefonos_original: [],
				cantidad_creditos: 0,
			},
			frmParienteAval: {
				tipo: "PARIENTE",
			},
		};
	},
	validations: {
		frmDatosCliente: {
			dni: { required },
			apellido_paterno: { required },
			apellido_materno: { required },
			nombres: { required },

			fecha_nacimiento: { required },
			estado_civil: { noZero },
			sexo: { noZero },

			asesor_id: { noZero },
			promotor_id: { noZero },
			canal_referencia: { noZero },

			monto_maximo: { required },

			direccion: { required },
			distrito_id: { noZero },
			provincia_id: { noZero },
			departamento_id: { noZero },

			referencia_direccion: { required },
			telefonos: {
				t1: {
					required,
					numeric,
					// Longitud exacta de 9 dígitos
					exactLength(value) {
						return value.length === 9;
					},
				},
				o1: { noZero },
				n1: { required },
			},
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},
	computed: {
		permiso_editar_monto() {
			let resultado = false;
			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CLIENTES/EDITAR_MONTO"
				);

			if (permiso_detalle.length != 0) {
				let acceso_agencias = permiso_detalle[0].acceso_agencias;

				if (acceso_agencias != null) {
					acceso_agencias = JSON.parse(acceso_agencias);

					let agencia_autorizada = acceso_agencias.filter(
						(item) => item.agencia_id == this.frmDatosCliente.agencia_id
					);

					if (agencia_autorizada.length != 0) {
						resultado = true;
					}
				}
			}
			return resultado;
		},
		permiso_editar_asesor() {
			return (
				this.$inertia.page.props.user_permissions.permisos.filter((item) =>
					item.includes("CREDITOS_CLIENTES/EDITAR_ASESOR")
				).length > 0
			);
		},
	},
	methods: {
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value) {
				valor = e.target.value;
			}

			this.frmDatosCliente.monto_maximo = this.$parent.round(
				valor,
				numero_decimales
			);
		},

		ResetearFrmDatosPersonales() {
			this.frmDatosCliente.id = 0;
			this.frmDatosCliente.modo = "NUEVO";
			this.frmDatosCliente.dni = null;
			this.frmDatosCliente.apellido_paterno = null;
			this.frmDatosCliente.apellido_materno = null;
			this.frmDatosCliente.nombres = null;
			this.frmDatosCliente.fecha_nacimiento = null;
			this.frmDatosCliente.estado_civil = 0;
			this.frmDatosCliente.sexo = 0;
			this.frmDatosCliente.hijos = 0;

			this.frmDatosCliente.agencia = this.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			)[0].agencia;
			this.frmDatosCliente.agencia_id = this.agencias.filter(
				(item) => item.id == this.agencia_seleccionada
			)[0].id;
			this.frmDatosCliente.correo_electronico = null;

			this.frmDatosCliente.codigo_expediente = null;
			this.frmDatosCliente.asesor_id = 0;
			this.frmDatosCliente.promotor_id = 0;
			this.frmDatosCliente.central_riesgo = "NORMAL";
			this.frmDatosCliente.canal_referencia = 0;

			this.frmDatosCliente.monto_maximo = this.$parent.round(1000, 2);
			this.frmDatosCliente.notas = null;
			this.frmDatosCliente.reportar_equifax = false;

			this.frmDatosCliente.direccion = null;
			this.frmDatosCliente.distrito_id = 0;
			this.frmDatosCliente.provincia_id = 0;
			this.frmDatosCliente.departamento_id = 0;

			this.frmDatosCliente.referencia_direccion = null;

			this.frmDatosCliente.imagen_dni = null;
			this.frmDatosCliente.observaciones = null;
			this.frmDatosCliente.telefonos.t1 = null;
			this.frmDatosCliente.telefonos.t2 = null;
			this.frmDatosCliente.telefonos.t3 = null;
			this.frmDatosCliente.telefonos.t4 = null;
			this.frmDatosCliente.telefonos.n1 = null;
			this.frmDatosCliente.telefonos.n2 = null;
			this.frmDatosCliente.telefonos.n3 = null;
			this.frmDatosCliente.telefonos.n4 = null;
			this.frmDatosCliente.telefonos.o1 = 0;
			this.frmDatosCliente.telefonos.o2 = 0;
			this.frmDatosCliente.telefonos.o3 = 0;
			this.frmDatosCliente.telefonos.o4 = 0;

			this.frmDatosCliente.cantidad_creditos = 0;
		},

		async BuscarReniec() {
			if (this.$v.frmDatosCliente.dni.$invalid == true) {
				Swal.fire({
					icon: "error",
					title: "¡Error!",
					text: "Debe ingresar el número de DNI.",
				});
				return false;
			}

			const response = await this.BusquedaExterna();

			if (response.resultado == "RESTRINGIDO") {
				const agencia = response.agencia;
				Swal.fire({
					icon: "error",
					title: "¡Error!",
					text: "El cliente está RESTRINGIDO por la agencia " + agencia,
				});
				return false;
			}

			Swal.fire({
				title: "BUSCANDO",
				showConfirmButton: false,
				allowOutsideClick: false,
				willOpen: async () => {
					Swal.showLoading();
					let dni = this.frmDatosCliente.dni;

					return axios
						.get("https://api.factiliza.com/pe/v1/dni/info/" + dni, {
							headers: {
								Authorization: `Bearer ${api_factiliza_token}`,
							},
						})
						.then(async (response) => {
							let inf_reniec = response.data.data;
							this.frmDatosCliente.apellido_paterno =
								inf_reniec.apellido_paterno;
							this.frmDatosCliente.apellido_materno =
								inf_reniec.apellido_materno;
							this.frmDatosCliente.nombres = inf_reniec.nombres;
							this.frmDatosCliente.direccion = inf_reniec.direccion;
							this.frmDatosCliente.departamento_id = inf_reniec.ubigeo[0];
							this.FiltrarProvincias();
							this.frmDatosCliente.provincia_id =
								this.provincias_filtradas.filter(
									(item) => item.ubigeo_provincia == inf_reniec.ubigeo[1]
								)[0].id;
							this.FiltrarDistritos();
							this.frmDatosCliente.distrito_id =
								this.distritos_filtrados.filter(
									(item) => item.ubigeo_distrito == inf_reniec.ubigeo[2]
								)[0].id;

							return Swal.fire({
								icon: "success",
								title: "¡Información obtenida!",
								timer: 1200,
								showConfirmButton: false,
							});
						})
						.catch((error) => {
							console.log(error);
							return Swal.fire({
								icon: "error",
								title: "¡Error!",
								text: "No se pudo obtener la información",
								showConfirmButton: true,
							});
						});
				},
			});
		},

		async BusquedaExterna() {
			const params = {
				dni: this.frmDatosCliente.dni,
			};

			return axios
				.get(api_externa + "/api/cli/listado_externa/buscar", { params })
				.then((response) => {
					console.log(response.data);
					return response.data;
				});
		},

		FiltrarProvincias() {
			let departamento_id = this.frmDatosCliente.departamento_id;

			if (!departamento_id == 0) {
				this.provincias_filtradas = this.provincias.filter(
					(item) => item.departamento_id == departamento_id
				);
			} else {
				this.provincias_filtradas = this.provincias;
			}

			this.frmDatosCliente.provincia_id = 0;
			this.frmDatosCliente.distrito_id = 0;
		},
		FiltrarDistritos() {
			let provincia_id = this.frmDatosCliente.provincia_id;

			if (!provincia_id == 0) {
				this.distritos_filtrados = this.distritos.filter(
					(item) => item.provincia_id == provincia_id
				);
			} else {
				this.distritos_filtrados = this.distritos;
			}

			this.frmDatosCliente.distrito_id = 0;
		},
		Habilitar_editar() {
			this.frmDatosCliente.modo = "EDITAR";
			this.title_modal = "EDITAR CLIENTE";
		},

		async GuardarCliente() {
			let self = this;
			self.submited = true;

			// console.log(self.frmDatosCliente.telefonos.t1.length<9);

			if (self.$v.frmDatosCliente.telefonos.t1.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "El número principal no puede tener menos de 9 dígitos.",
				});

				return false;
			}

			if (self.$v.frmDatosCliente.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			}

			self.telefonos_actualizados =
				JSON.stringify(self.frmDatosCliente.telefonos) !==
				JSON.stringify(self.frmDatosCliente.telefonos_original);
			// console.log(self.telefonos_actualizados);

			if (
				self.telefonos_actualizados == true &&
				self.frmDatosCliente.modo == "EDITAR"
			) {
				let fecha_actual = await self.$parent.fecha_hora_actual(
					self.frmDatosCliente.agencia_id
				);

				self.frmDatosCliente.telefonos["actualizado"] = fecha_actual;
			}

			let params = new FormData();

			params.append("id", this.frmDatosCliente.id);
			params.append("dni", this.frmDatosCliente.dni);
			params.append("modo", this.frmDatosCliente.modo);

			axios
				.post(route("cli.listado_registro.verificar"), params)
				.then(function (response) {
					if (response.data.resultado == "EXISTE") {
						let agencia = response.data.agencia;
						Swal.fire({
							icon: "warning",
							title: "¡Ups!",
							text: "Este cliente ya está registrado en la agencia " + agencia,
						});
						return false;
					} else if (response.data.resultado == "NO_EXISTE") {
						let titulo_confirmacion = "";
						if (self.frmDatosCliente.modo == "NUEVO") {
							titulo_confirmacion = "REGISTRAR CLIENTE";
						} else if (self.frmDatosCliente.modo == "EDITAR") {
							titulo_confirmacion = "GUARDAR CAMBIOS";
						}

						Swal.fire({
							title: titulo_confirmacion,
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

								data.append(
									"frmDatosCliente",
									JSON.stringify(self.frmDatosCliente)
								);

								data.append("imagen_dni", self.frmDatosCliente.imagen_dni);

								self.$inertia.post(
									route("cli.listado_registro.guardar"),
									data,
									{
										preserveScroll: true,
										onStart: (visit) => {
											let timerInterval;
											Swal.fire({
												title: "CARGANDO",
												html: "Espere porfavor...",
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
													$("#datosCliente1-tab").tab("show");
													$("#mdlDatosPersonales").css("display", "none");
													self.$parent.$parent.BuscarClientes();
												},
											});
										},
									}
								);
							},
						});
					}
				});
		},

		SeleccionarParienteAval() {
			let form_1 = this.mdlParientesAvales.frmParienteAval;
			let form_2 = this.frmDatosCliente;

			// form_1.dni = form_2.dni;
			form_1.apellido_paterno = form_2.apellido_paterno;
			form_1.apellido_materno = form_2.apellido_materno;
			form_1.nombres = form_2.nombres;
			form_1.fecha_nacimiento = form_2.fecha_nacimiento;
			form_1.estado_civil = form_2.estado_civil;
			form_1.sexo = form_2.sexo;
			form_1.agencia_id = form_2.agencia_id;
			form_1.parentesco = 0;

			form_1.codigo_expediente = form_2.codigo_expediente;
			form_1.central_riesgo = form_2.central_riesgo;
			form_1.notas = form_2.notas;

			form_1.direccion = form_2.direccion;
			form_1.departamento_id = form_2.departamento_id;
			form_1.provincia_id = form_2.provincia_id;
			form_1.distrito_id = form_2.distrito_id;
			form_1.referencia_direccion = form_2.referencia_direccion;

			form_1.imagen_dni = form_2.imagen_dni;
			form_1.telefonos = form_2.telefonos;

			form_1.cliente_vinculado_id = form_2.id;

			form_1.modo = "NO-EDITAR";

			// Esperar a que Vue reactive los datos antes de abrir modal/tab
			this.$nextTick(() => {
				$("#mdlDatosPersonales").css("display", "none");
				$("#datosParienteAval2-tab").tab("show");
			});
		},

		CerrarModalRegistro() {
			$("#datosParienteAval2-tab").tab("show");
			$("#datosCliente1-tab").tab("show");
			$("#mdlDatosPersonales").css("display", "none");
			let previo = $("#previzualizar img");
			previo.remove();

			if (this.frmDatosCliente.modo == "PARIENTE_AVAL") {
				this.mdlParientesAvales.frmParienteAval.dni = null;
			}
		},

		AgregarDNI(e) {
			let previo = $("#previzualizar img");
			previo.remove();

			this.frmDatosCliente.imagen_dni = e.target.files[0];

			let reader = new FileReader();
			reader.readAsDataURL(e.target.files[0]); // leemos el archivo subido y se lo pasamos a nuestro fileReader
			reader.onload = function () {
				let preview = document.getElementById("previzualizar"),
					image = document.createElement("img");

				image.src = reader.result;
				image.style.border = "1px solid #ffff";
				if (screen.width < 1000) {
					image.style.width = "400px";
					image.style.height = "200px";
				} else {
					image.style.width = "600px";
					image.style.height = "300px";
				}

				preview.innerHTML = "";
				preview.append(image);
			};
		},
		Descargar() {
			let self = this;
			let source =
				"/imagenes_server/creditos/clientes/dni/" +
				this.agencia_seleccionada +
				"/" +
				self.frmDatosCliente.imagen_dni.substring(0, 4) +
				"/" +
				self.frmDatosCliente.imagen_dni;
			axios
				.get(source, { responseType: "blob" })
				.then((response) => {
					const blob = new Blob([response.data], { type: response.data.type });
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = self.frmDatosCliente.imagen_dni;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},
	},
};
</script>

<style lang="css">
</style>
