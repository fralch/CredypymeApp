<template>
	<div>
		<div id="mdlVerParientesAvales" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-50 mdlVerParientesAvales">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>{{
								(frmParienteAval.tipo == "PARIENTE" ? "PARIENTES" : "AVALES") +
								" DEL CLIENTE"
							}}</strong>
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
										id="datosParienteAval1-tab"
										data-toggle="tab"
										href="#datosParienteAval1"
										role="tab"
										aria-controls="datosParienteAval1"
										aria-selected="true"
										>LISTA DE
										{{
											frmParienteAval.tipo == "PARIENTE"
												? "PARIENTES"
												: "AVALES"
										}}
									</a>
								</li>
								<li class="nav-item" role="presentation">
									<a
										class="nav-link tab-title"
										id="datosParienteAval2-tab"
										data-toggle="tab"
										href="#datosParienteAval2"
										role="tab"
										aria-controls="datosParienteAval2"
										aria-selected="false"
										>DATOS PRINCIPALES</a
									>
								</li>
								<li class="nav-item" role="presentation">
									<a
										class="nav-link tab-title"
										id="datosParienteAval3-tab"
										data-toggle="tab"
										href="#datosParienteAval3"
										role="tab"
										aria-controls="datosParienteAval3"
										aria-selected="false"
										>DIRECCIÓN Y TELÉFONOS</a
									>
								</li>
							</ul>

							<div class="tab-content" id="myTabContent">
								<div
									class="tab-pane fade show active"
									id="datosParienteAval1"
									role="tabpanel"
									aria-labelledby="datosParienteAval1-tab"
								>
									<div class="text-center mt-2">
										<button
											class="btn btn-action btn-icon-split"
											title="Nuevo"
											@click="NuevoParienteAval"
										>
											<span class="icon text-white">
												<i class="fas fa-plus"></i>
											</span>
											<span class="text">
												{{ "AÑADIR " + frmParienteAval.tipo }}</span
											>
										</button>
									</div>
									<table
										class="table"
										id="tblParientesAvales"
										width="100% !important"
									>
										<thead>
											<tr>
												<th style="max-width: 30px !important">VINC.</th>
												<th style="min-width: 125px !important">AP_PATERNO</th>
												<th style="min-width: 125px !important">AP_MATERNO</th>
												<th style="min-width: 200px !important">NOMBRES</th>
												<th style="min-width: 100px !important">
													{{
														frmParienteAval.tipo == "PARIENTE"
															? "PARENTESCO"
															: "C_RIESGO"
													}}
												</th>
												<th style="min-width: 75px !important">EXPEDIENTE</th>
												<th style="min-width: 100px !important">AGENCIA</th>
												<th style="min-width: 75px !important">DNI</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in lista_parientes_avales"
												:key="index"
												class="table-bordered"
												@dblclick="EditarParienteAval(item)"
												:class="[index % 2 == 0 ? 'verde-claro' : '']"
											>
												<td align="center">
													<div class="switch-button">
														<!-- Checkbox -->
														<input
															type="checkbox"
															name="switch-button"
															:id="'switch-label' + index"
															class="switch-button__checkbox"
															@change="VincularDesvincular(item)"
															:checked="item.vinculado"
															v-model="item.vinculado"
														/>
														<!-- Botón -->
														<label
															:for="'switch-label' + index"
															class="switch-button__label"
														></label>
													</div>
												</td>
												<td>
													{{ item.apellido_paterno }}
												</td>
												<td>
													{{ item.apellido_materno }}
												</td>
												<td>
													{{ item.nombres }}
												</td>

												<td align="center">
													{{
														frmParienteAval.tipo == "PARIENTE"
															? item.parentesco
															: item.central_riesgo
													}}
												</td>
												<td align="center">
													{{
														item.codigo_expediente == null
															? "-"
															: item.codigo_expediente
													}}
												</td>
												<td align="center">
													{{ item.agencia }}
												</td>
												<td align="center">
													{{ item.dni }}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div
									class="tab-pane fade"
									id="datosParienteAval2"
									role="tabpanel"
									aria-labelledby="datosParienteAval2-tab"
								>
									<fieldset>
										<legend>DATOS PERSONALES</legend>
										<div class="form-row">
											<div class="form-row col-md-12">
												<div class="form-group col-md-2 col-5">
													<label class="label-title text-center">DNI</label>

													<form autocomplete="off">
														<input
															type="text"
															class="form-control center"
															:value="frmParienteAval.dni"
															disabled
															readonly
															v-if="frmParienteAval.cantidad_creditos > 0"
														/>
														<input
															class="form-control center"
															:class="[
																submited
																	? $v.frmParienteAval.dni.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															type="text"
															maxlength="8"
															oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
															@keyup="BuscarParienteAval"
															v-model="frmParienteAval.dni"
															:disabled="
																frmParienteAval.modo == 'NO-EDITAR' ||
																frmParienteAval.modo == 'EDITAR-EXT'
															"
															v-if="frmParienteAval.cantidad_creditos == 0"
														/>
													</form>
												</div>

												<div class="form-group col-md-3 col-7">
													<label class="label-title">APELLIDO PATERNO</label>

													<form autocomplete="off">
														<textarea
															type="text"
															class="form-control"
															:value="frmParienteAval.apellido_paterno"
															disabled
															readonly
															v-if="frmParienteAval.cantidad_creditos > 0"
														>
														</textarea>

														<textarea
															rows="1"
															class="form-control mayus"
															:class="[
																submited
																	? $v.frmParienteAval.apellido_paterno.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmParienteAval.apellido_paterno"
															:disabled="
																frmParienteAval.modo == 'NO-EDITAR' ||
																frmParienteAval.modo == 'EDITAR-EXT'
															"
															v-if="frmParienteAval.cantidad_creditos == 0"
														></textarea>
													</form>
												</div>
												<div class="form-group col-md-3 col-5">
													<label class="label-title">APELLIDO MATERNO</label>

													<form autocomplete="off">
														<textarea
															type="text"
															class="form-control"
															:value="frmParienteAval.apellido_materno"
															disabled
															readonly
															v-if="frmParienteAval.cantidad_creditos > 0"
														>
														</textarea>
														<textarea
															rows="1"
															class="form-control mayus"
															:class="[
																submited
																	? $v.frmParienteAval.apellido_materno.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmParienteAval.apellido_materno"
															:disabled="
																frmParienteAval.modo == 'NO-EDITAR' ||
																frmParienteAval.modo == 'EDITAR-EXT'
															"
															v-if="frmParienteAval.cantidad_creditos == 0"
														></textarea>
													</form>
												</div>
												<div class="form-group col-md-4 col-7">
													<label class="label-title">NOMBRES</label>

													<form autocomplete="off">
														<textarea
															type="text"
															class="form-control"
															:value="frmParienteAval.nombres"
															disabled
															readonly
															v-if="frmParienteAval.cantidad_creditos > 0"
														>
														</textarea>

														<textarea
															rows="1"
															class="form-control mayus"
															:class="[
																submited
																	? $v.frmParienteAval.nombres.$invalid
																		? 'is-invalid'
																		: 'is-valid'
																	: '',
															]"
															v-model="frmParienteAval.nombres"
															:disabled="
																frmParienteAval.modo == 'NO-EDITAR' ||
																frmParienteAval.modo == 'EDITAR-EXT'
															"
															v-if="frmParienteAval.cantidad_creditos == 0"
														></textarea>
													</form>
												</div>
											</div>
											<div class="form-row col-md-12">
												<div class="form-group col-md-3 col-6">
													<label class="label-title">FECHA DE NACIMIENTO</label>

													<input
														type="date"
														class="form-control center"
														:class="[
															submited
																? $v.frmParienteAval.fecha_nacimiento.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmParienteAval.fecha_nacimiento"
														:disabled="
															frmParienteAval.modo == 'NO-EDITAR' ||
															frmParienteAval.modo == 'EDITAR-EXT'
														"
													/>
												</div>
												<div class="form-group col-md-3 col-6">
													<label class="label-title">ESTADO CIVIL</label>

													<select
														class="form-control center"
														:class="[
															submited
																? $v.frmParienteAval.estado_civil.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmParienteAval.estado_civil"
														:disabled="
															frmParienteAval.modo == 'NO-EDITAR' ||
															frmParienteAval.modo == 'EDITAR-EXT'
														"
													>
														<option value="0" selected disabled>
															Seleccione...
														</option>
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
																? $v.frmParienteAval.sexo.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmParienteAval.sexo"
														:disabled="
															frmParienteAval.modo == 'NO-EDITAR' ||
															frmParienteAval.modo == 'EDITAR-EXT'
														"
													>
														<option value="0" selected disabled>
															Seleccione...
														</option>
														<option value="F">FEMENINO</option>
														<option value="M">MASCULINO</option>
													</select>
												</div>

												<div class="form-group col-md-3 col-6">
													<label class="label-title">AGENCIA</label>

													<input
														type="text"
														class="form-control center"
														:value="
															frmParienteAval.id != 0
																? frmParienteAval.agencia
																: agencias.length > 0 &&
																  agencia_seleccionada != null
																? agencias.filter(
																		(item) => item.id == agencia_seleccionada
																  )[0].agencia
																: null
														"
														:disabled="true"
													/>
												</div>
											</div>
											<div class="form-row col-md-12">
												<div
													class="form-group col-md-3 col-6"
													v-if="frmParienteAval.tipo == 'PARIENTE'"
												>
													<label class="label-title">PARENTESCO</label>

													<select
														class="form-control center"
														:class="[
															submited
																? $v.frmParienteAval.parentesco.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														v-model="frmParienteAval.parentesco"
													>
														<option :value="0">Seleccione...</option>
														<option value="CONVIVIENTE">CONVIVIENTE</option>
														<option value="ESPOSO(A)">ESPOSO(a)</option>
														<option value="HERMANO(A)">HERMANO(a)</option>
														<option value="HIJO(A)">HIJO(a)</option>
														<option value="MADRE">MADRE</option>
														<option value="PADRE">PADRE</option>
														<option value="SOBRINO(A)">SOBRINO(a)</option>
														<option value="TIO(A)">TÍO(a)</option>
													</select>
												</div>
											</div>
										</div>
									</fieldset>
									<fieldset>
										<legend>INFORMACIÓN CREDITICIA</legend>
										<div class="form-row">
											<div class="form-group col-md-3 col-6">
												<label class="label-title">EXPEDIENTE</label>

												<input
													type="text"
													class="form-control center"
													:value="
														frmParienteAval.codigo_expediente == null
															? '-'
															: frmParienteAval.codigo_expediente
													"
													:disabled="true"
												/>
											</div>
											<div class="form-group col-md-3 col-6">
												<label class="label-title">C. RIESGO</label>

												<select
													class="form-control center"
													v-model="frmParienteAval.central_riesgo"
													:disabled="
														frmParienteAval.modo == 'NO-EDITAR' ||
														frmParienteAval.modo == 'EDITAR-EXT'
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

											<div class="form-group col-md-6 col-12">
												<label class="label-title"> NOTAS </label>

												<form autocomplete="off">
													<textarea
														class="form-control mayus text-row"
														rows="2"
														v-model="frmParienteAval.notas"
														:disabled="
															frmParienteAval.modo == 'NO-EDITAR' ||
															frmParienteAval.modo == 'EDITAR-EXT'
														"
													></textarea>
												</form>
											</div>
										</div>
									</fieldset>
									<hr />

									<div class="text-right">
										<div class="btn-group" role="group">
											<button
												class="btn btn-action btn-icon-split"
												@click="GuardarParienteAval"
												v-if="
													frmParienteAval.modo == 'EDITAR' ||
													frmParienteAval.cliente_vinculado_id > 0
												"
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
									id="datosParienteAval3"
									role="tabpanel"
									aria-labelledby="datosParienteAval3-tab"
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
																? $v.frmParienteAval.direccion.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														rows="1"
														v-model="frmParienteAval.direccion"
														:disabled="
															frmParienteAval.modo == 'NO-EDITAR' ||
															frmParienteAval.modo == 'EDITAR-EXT'
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
															? $v.frmParienteAval.departamento_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													@change="FiltrarProvincias"
													v-model="frmParienteAval.departamento_id"
													:disabled="
														frmParienteAval.modo == 'NO-EDITAR' ||
														frmParienteAval.modo == 'EDITAR-EXT'
													"
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
												<label class="label-title">PROVINCIA</label>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmParienteAval.provincia_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													@change="FiltrarDistritos"
													v-model="frmParienteAval.provincia_id"
													:disabled="
														frmParienteAval.modo == 'NO-EDITAR' ||
														frmParienteAval.modo == 'EDITAR-EXT'
													"
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
												<label class="label-title">DISTRITO</label>

												<select
													class="form-control center"
													:class="[
														submited
															? $v.frmParienteAval.distrito_id.$invalid
																? 'is-invalid'
																: 'is-valid'
															: '',
													]"
													v-model="frmParienteAval.distrito_id"
													:disabled="
														frmParienteAval.modo == 'NO-EDITAR' ||
														frmParienteAval.modo == 'EDITAR-EXT'
													"
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
												<label class="label-title">REFERENCIA</label>

												<form autocomplete="off">
													<textarea
														class="form-control text-row mayus"
														:class="[
															submited
																? $v.frmParienteAval.referencia_direccion
																		.$invalid
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														rows="3"
														v-model="frmParienteAval.referencia_direccion"
														:disabled="
															frmParienteAval.modo == 'NO-EDITAR' ||
															frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																:class="[
																	submited
																		? $v.frmParienteAval.telefonos.t1.$invalid
																			? 'is-invalid'
																			: 'is-valid'
																		: '',
																]"
																maxlength="9"
																min="0"
																oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
																v-model="frmParienteAval.telefonos.t1"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																:class="[
																	submited
																		? $v.frmParienteAval.telefonos.o1.$invalid
																			? 'is-invalid'
																			: 'is-valid'
																		: '',
																]"
																v-model="frmParienteAval.telefonos.o1"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
																"
															>
																<option :value="0" selected disabled>
																	Seleccione...
																</option>
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
																rows="1"
																class="form-control mayus"
																:class="[
																	submited
																		? $v.frmParienteAval.telefonos.n1.$invalid
																			? 'is-invalid'
																			: 'is-valid'
																		: '',
																]"
																v-model="frmParienteAval.telefonos.n1"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																min="0"
																maxlength="9"
																oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
																v-model="frmParienteAval.telefonos.t2"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																v-model="frmParienteAval.telefonos.o2"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
																"
															>
																<option value="0" selected disabled>
																	Seleccione...
																</option>
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
																rows="1"
																class="form-control mayus"
																v-model="frmParienteAval.telefonos.n2"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																maxlength="9"
																min="0"
																oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
																v-model="frmParienteAval.telefonos.t3"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																v-model="frmParienteAval.telefonos.o3"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
																"
															>
																<option value="0" selected disabled>
																	Seleccione...
																</option>
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
																rows="1"
																class="form-control mayus"
																v-model="frmParienteAval.telefonos.n3"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																maxlength="9"
																min="0"
																oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
																v-model="frmParienteAval.telefonos.t4"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
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
																class="form-control center"
																v-model="frmParienteAval.telefonos.o4"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
																"
															>
																<option value="0" selected disabled>
																	Seleccione...
																</option>
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
																rows="1"
																class="form-control mayus"
																v-model="frmParienteAval.telefonos.n4"
																:disabled="
																	frmParienteAval.modo == 'NO-EDITAR' ||
																	frmParienteAval.modo == 'EDITAR-EXT'
																"
															></textarea>
														</div>
													</div>
												</div>
											</fieldset>
											<!-- ----- -->
										</div>
									</form>
									<hr />

									<div class="text-right">
										<div class="btn-group" role="group">
											<button
												class="btn btn-action btn-icon-split"
												@click="GuardarParienteAval"
												v-if="
													frmParienteAval.modo == 'EDITAR' ||
													frmParienteAval.cliente_vinculado_id > 0
												"
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
	</div>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
const api_externa = import.meta.env.VITE_S_API_EXTERNA;
const noZero = (value) => value != 0;
export default {
	components: { headerCloseModal },
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
			submited: false,
			texto_buscar: null,

			lista_parientes_avales: [],
			distritos_filtrados: this.distritos,
			provincias_filtradas: this.provincias,
			frmParienteAval: {
				modo: null,
				tipo: "PARIENTE",
				id: 0,
				cliente_id: 0,
				cliente_vinculado_id: null,

				agencia_id: 0,
				dni: null,
				apellido_paterno: null,
				apellido_materno: null,
				nombres: null,
				fecha_nacimiento: null,
				estado_civil: 0,
				sexo: 0,
				parentesco: 0,

				codigo_expediente: null,
				central_riesgo: "NORMAL",
				notas: null,

				direccion: null,
				departamento_id: 0,
				provincia_id: 0,
				distrito_id: 0,
				referencia_direccion: null,

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
				cantidad_creditos: 0,
			},
		};
	},
	validations() {
		if (this.frmParienteAval.tipo == "PARIENTE") {
			return {
				frmParienteAval: {
					dni: { required },
					apellido_paterno: { required },
					apellido_materno: { required },
					nombres: { required },
					fecha_nacimiento: { required },
					estado_civil: { noZero },
					sexo: { noZero },
					parentesco: { noZero },

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
			};
		} else if (this.frmParienteAval.tipo == "AVAL") {
			return {
				frmParienteAval: {
					dni: { required },
					apellido_paterno: { required },
					apellido_materno: { required },
					nombres: { required },
					fecha_nacimiento: { required },
					estado_civil: { noZero },
					sexo: { noZero },

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
			};
		}
	},
	watch: {
		lista_parientes_avales() {
			$("#tblParientesAvales").DataTable().destroy();
			this.TablaListaParientesAvales();
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		this.TablaListaParientesAvales();
	},
	methods: {
		TablaListaParientesAvales() {
			this.$nextTick(() => {
				var table = $("#tblParientesAvales").DataTable({
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

		FiltrarProvincias(e) {
			let departamento_id = e.target.value;

			if (!departamento_id == 0) {
				this.provincias_filtradas = this.provincias.filter(
					(item) => item.departamento_id == departamento_id
				);
			} else {
				this.provincias_filtradas = this.provincias;
			}

			this.frmParienteAval.provincia_id = 0;
			this.frmParienteAval.distrito_id = 0;
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

			this.frmParienteAval.distrito_id = 0;
		},
		NuevoParienteAval() {
			this.frmParienteAval.modo = "EDITAR";
			this.submited = false;
			$("#datosParienteAval2-tab").tab("show");

			this.ResetearFrmParienteAval();
		},
		async EditarParienteAval(cliente) {
			let datos_cliente = cliente;

			this.frmParienteAval.cliente_vinculado_id =
				datos_cliente.pariente_aval_id;

			this.frmParienteAval.id = cliente.id;
			this.frmParienteAval.agencia_id = cliente.agencia_id;
			this.frmParienteAval.agencia = cliente.agencia;

			this.frmParienteAval.dni = cliente.dni;
			this.frmParienteAval.apellido_paterno = cliente.apellido_paterno;
			this.frmParienteAval.apellido_materno = cliente.apellido_materno;
			this.frmParienteAval.nombres = cliente.nombres;
			this.frmParienteAval.fecha_nacimiento = cliente.fecha_nacimiento;
			this.frmParienteAval.estado_civil = cliente.estado_civil;
			this.frmParienteAval.sexo = cliente.sexo;
			this.frmParienteAval.parentesco = cliente.parentesco;

			this.frmParienteAval.codigo_expediente = cliente.codigo_expediente;
			this.frmParienteAval.central_riesgo = cliente.central_riesgo;
			this.frmParienteAval.notas = cliente.notas;

			this.frmParienteAval.direccion = cliente.direccion;
			this.frmParienteAval.departamento_id = cliente.departamento_id;
			this.frmParienteAval.provincia_id = cliente.provincia_id;
			this.frmParienteAval.distrito_id = cliente.distrito_id;
			this.frmParienteAval.referencia_direccion = cliente.referencia_direccion;

			let telefonos = [];

			if (typeof datos_cliente.telefonos === "string") {
				telefonos = JSON.parse(datos_cliente.telefonos);
			} else {
				telefonos = datos_cliente.telefonos;
			}

			this.frmParienteAval.telefonos = telefonos;

			if ([1, 4, 6].includes(cliente.agencia_id)) {
				this.frmParienteAval.modo = "EDITAR-EXT";
				this.frmParienteAval.cantidad_creditos = 1;
			} else {
				this.frmParienteAval.modo = "EDITAR";
				await axios
					.get(
						route("cli.listado_registro.cantidad_creditos", {
							agencia_id: cliente.agencia_id,
							cliente_id: datos_cliente.pariente_aval_id,
						})
					)
					.then((response) => {
						let cantidad_creditos = response.data.cantidad_creditos;

						this.frmParienteAval.cantidad_creditos = cantidad_creditos;
					});
			}

			$("#datosParienteAval2-tab").tab("show");
		},
		async DatosPersonales(cliente) {
			let mdlDatosPersonales =
				this.$parent.$parent.$parent.$refs.mdlDatosPersonales;

			if ([1, 4, 6].includes(cliente.agencia_id)) {
				this.frmParienteAval.cantidad_creditos = 1;
			} else {
				await axios
					.get(
						route("cli.listado_registro.cantidad_creditos", {
							agencia_id: cliente.agencia_id,
							cliente_id: cliente.id,
						})
					)
					.then((response) => {
						let cantidad_creditos = response.data.cantidad_creditos;

						mdlDatosPersonales.frmDatosCliente.cantidad_creditos =
							cantidad_creditos;
					});
			}

			mdlDatosPersonales.submited = false;
			mdlDatosPersonales.title_modal = "CLIENTE ENCONTRADO";
			mdlDatosPersonales.frmDatosCliente = cliente;
			mdlDatosPersonales.frmDatosCliente.modo = "PARIENTE_AVAL";

			let telefonos = [];

			if (typeof cliente.telefonos === "string") {
				telefonos = JSON.parse(cliente.telefonos);
			} else {
				telefonos = cliente.telefonos;
			}

			mdlDatosPersonales.frmDatosCliente.telefonos = telefonos;

			let data = new FormData();
			data.append(
				"cargos",
				JSON.stringify([
					"ASESOR DE NEGOCIOS",
					"JEFE DE CRÉDITOS",
					"PROMOTOR DE CRÉDITOS",
					"GERENTE DE CRÉDITOS",
					"COORDINADOR DE CRÉDITOS",
				])
			);
			data.append("agencia", this.agencia_seleccionada);
			axios
				.post(route("usuarios.listar_por_cargos_agencia"), data)
				.then(function (response) {
					mdlDatosPersonales.lista_asesores_promotores = response.data;
				});

			$("#mdlDatosPersonales").css("display", "block");

			if (cliente.imagen_dni != null) {
				let img_prev = $("#previzualizar img");
				img_prev.remove();

				let preview = document.getElementById("previzualizar"),
					image = document.createElement("img");

				image.src =
					"/imagenes_server/creditos/clientes/dni/" + cliente.imagen_dni;

				if (screen.width < 1000) {
					image.style.width = "400px";
					image.style.height = "200px";
				} else {
					image.style.width = "600px";
					image.style.height = "300px";
				}
				image.style.border = "1px solid #ffff";
				preview.innerHTML = "";
				preview.append(image);
			}
		},

		ResetearFrmParienteAval() {
			let formulario = this.frmParienteAval;

			formulario.id = 0;
			formulario.agencia_id = this.agencia_seleccionada;
			formulario.cliente_vinculado_id = 0;
			formulario.dni = null;
			formulario.apellido_paterno = null;
			formulario.apellido_materno = null;
			formulario.nombres = null;
			formulario.fecha_nacimiento = null;
			formulario.estado_civil = 0;
			formulario.sexo = 0;
			formulario.parentesco = 0;

			formulario.codigo_expediente = null;
			formulario.central_riesgo = "NORMAL";
			formulario.notas = null;

			formulario.direccion = null;
			formulario.departamento_id = 0;
			formulario.provincia_id = 0;
			formulario.distrito_id = 0;
			formulario.referencia_direccion = null;

			formulario.telefonos.t1 = null;
			formulario.telefonos.t2 = null;
			formulario.telefonos.t3 = null;
			formulario.telefonos.t4 = null;
			formulario.telefonos.n1 = null;
			formulario.telefonos.n2 = null;
			formulario.telefonos.n3 = null;
			formulario.telefonos.n4 = null;
			formulario.telefonos.o1 = 0;
			formulario.telefonos.o2 = 0;
			formulario.telefonos.o3 = 0;
			formulario.telefonos.o4 = 0;

			formulario.cantidad_creditos = 0;
		},

		async BuscarParienteAval() {
			let texto_buscar = this.frmParienteAval.dni;

			if (texto_buscar && texto_buscar.length == 8) {
				let data = new FormData();

				data.append("texto_buscar", texto_buscar);
				data.append("tipo_filtro", "dni");
				data.append("agencia", "TODAS");

				await axios
					.post(route("cli.listado_registro.buscar"), data)
					.then((response) => {
						if (response.data.length > 0) {
							if (
								this.frmParienteAval.cliente_id == response.data[0].id &&
								this.agencia_seleccionada == response.data[0].agencia_id
							) {
								Swal.fire({
									icon: "error",
									title: "¡Ups!",
									text: "Usted no puede ser su propio pariente o aval",
								});
								$("#datosParienteAval2-tab").tab("show");
								this.frmParienteAval.dni = null;
								return false;
							} else if (
								this.lista_parientes_avales.filter(
									(item) => item.dni == texto_buscar
								).length > 0
							) {
								let mensaje = "";
								if (this.frmParienteAval.tipo == "PARIENTE") {
									mensaje =
										"El DNI ingresado ya está registrado como su pariente";
								} else if (this.frmParienteAval.tipo == "AVAL") {
									mensaje = "El DNI ingresado ya está registrado como su aval";
								}

								Swal.fire({
									icon: "error",
									title: "¡Ups!",
									text: mensaje,
								});
								$("#datosParienteAval1-tab").tab("show");
								this.ResetearFrmParienteAval();
								return false;
							} else if (
								this.agencia_seleccionada != response.data[0].agencia_id
							) {
								let agencia = response.data[0].agencia;

								return Swal.fire({
									icon: "warning",
									title: "Se encontró este cliente en la agencia " + agencia,
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
										let modo = "PARIENTE_AVAL";
										this.DatosPersonales(response.data[0], modo);
									} else {
										$("#datosParienteAval2-tab").tab("show");
										this.frmParienteAval.dni = null;
										return false;
									}
								});
							} else {
								let modo = "PARIENTE_AVAL";
								this.DatosPersonales(response.data[0], modo);
							}
						}
					});

				// VERIFICACIÓN EXTERNA --------------------------------------------------------
				const params = {
					dni: texto_buscar,
				};

				await axios
					.get(api_externa + "/api/cli/listado_externa/verificar", {
						params,
					})
					.then((response) => {
						const lista_clientes = response.data.lista_clientes;
						if (lista_clientes.length > 0) {
							if (this.agencia_seleccionada != lista_clientes[0].agencia_id) {
								let agencia = lista_clientes[0].agencia;

								return Swal.fire({
									icon: "warning",
									title: "Se encontró este cliente en la agencia " + agencia,
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
										let modo = "PARIENTE_AVAL";
										this.DatosPersonales(lista_clientes[0], modo);
									} else {
										$("#datosParienteAval2-tab").tab("show");
										this.frmParienteAval.dni = null;
										return false;
									}
								});
							}
						}
					});

				// ------------------------------------------
			}
		},

		VincularDesvincular(item) {
			let self = this;
			let message = "";
			let route_name = "";

			let valor_vinculado = !item.vinculado;

			if (this.frmParienteAval.tipo == "PARIENTE") {
				route_name = "cli.listado_registro.vincular_desvincular_p";
				if (valor_vinculado == false) {
					message = "VINCULAR PARIENTE";
				} else if (valor_vinculado == true) {
					message = "DESVINCULAR PARIENTE";
				}
			} else if (this.frmParienteAval.tipo == "AVAL") {
				route_name = "cli.listado_registro.vincular_desvincular_a";
				if (valor_vinculado == false) {
					message = "VINCULAR AVAL";
				} else if (valor_vinculado == true) {
					message = "DESVINCULAR AVAL";
				}
			}

			Swal.fire({
				title: message,
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

					data.append("id", item.id);
					data.append("cliente_id", item.cliente_id);
					data.append("vinculado", valor_vinculado);
					data.append("agencia_id", this.agencia_seleccionada);

					this.$inertia.post(route(route_name), data, {
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
									let route_name_2 = "";
									if (self.frmParienteAval.tipo == "PARIENTE") {
										route_name_2 = "cli.listado_registro.listar_parientes";
									} else if (self.frmParienteAval.tipo == "AVAL") {
										route_name_2 = "cli.listado_registro.listar_avales";
									}

									axios
										.post(
											route(route_name_2, {
												cliente_id: self.frmParienteAval.cliente_id,
												agencia_id: self.agencia_seleccionada,
											})
										)
										.then(function (response) {
											self.lista_parientes_avales = response.data;
										});

									$("#datosParienteAval1-tab").tab("show");
								},
							});
						},
					});
				} else {
					item.vinculado = valor_vinculado;
				}
			});
		},

		GuardarParienteAval() {
			this.submited = true;

			if (this.$v.frmParienteAval.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				$("#datosParienteAval2-tab").tab("show");
				return false;
			}
			if (this.frmParienteAval.telefonos.t1.length < 9) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "El número principal no puede tener menos de 9 dígitos.",
				});

				return false;
			}

			Swal.fire({
				title: "REGISTRAR " + this.frmParienteAval.tipo,
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					let route_name = "";
					let data = new FormData();

					data.append("agencia_id", this.agencia_seleccionada);
					if (this.frmParienteAval.tipo == "PARIENTE") {
						if (this.frmParienteAval.modo == "NO-EDITAR") {
							data.append("modo_asignacion", "EXISTENTE");
							data.append("cliente_id", this.frmParienteAval.cliente_id);
							data.append("agencia_pariente", this.frmParienteAval.agencia_id);
							data.append(
								"cliente_vinculado_id",
								this.frmParienteAval.cliente_vinculado_id
							);
							data.append("parentesco", this.frmParienteAval.parentesco);
						} else if (this.frmParienteAval.modo == "EDITAR") {
							data.append("modo_asignacion", "NUEVO-EDITAR");
							data.append(
								"datos_cliente",
								JSON.stringify(this.frmParienteAval)
							);
						} else if (this.frmParienteAval.modo == "EDITAR-EXT") {
							data.append("modo_asignacion", "EDITAR-EXT");
							data.append(
								"datos_cliente",
								JSON.stringify(this.frmParienteAval)
							);
						}
						route_name = "cli.listado_registro.asignar_pariente";
					} else if (this.frmParienteAval.tipo == "AVAL") {
						if (this.frmParienteAval.modo == "NO-EDITAR") {
							data.append("modo_asignacion", "EXISTENTE");
							data.append("cliente_id", this.frmParienteAval.cliente_id);
							data.append("agencia_aval", this.frmParienteAval.agencia_id);
							data.append(
								"cliente_vinculado_id",
								this.frmParienteAval.cliente_vinculado_id
							);
						} else if (this.frmParienteAval.modo == "EDITAR") {
							data.append("modo_asignacion", "NUEVO-EDITAR");
							data.append(
								"datos_cliente",
								JSON.stringify(this.frmParienteAval)
							);
						}
						route_name = "cli.listado_registro.asignar_aval";
					}

					this.$inertia.post(route(route_name), data, {
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
									this.frmParienteAval.modo = "NO-EDITAR";
									this.submited = false;
									this.frmParienteAval.cliente_vinculado_id = null;
									let route_name_2 = "";
									if (this.frmParienteAval.tipo == "PARIENTE") {
										route_name_2 = "cli.listado_registro.listar_parientes";
									} else if (this.frmParienteAval.tipo == "AVAL") {
										route_name_2 = "cli.listado_registro.listar_avales";
									}
									axios
										.post(
											route(route_name_2, {
												cliente_id: this.frmParienteAval.cliente_id,
												agencia_id: this.agencia_seleccionada,
											})
										)
										.then((response) => {
											this.lista_parientes_avales = response.data;
										});
									this.ResetearFrmParienteAval();
									$("#datosParienteAval1-tab").tab("show");
								},
							});
						},
					});
				},
			});
		},
		CerrarModal() {
			$("#datosParienteAval1-tab").tab("show");
			$("#mdlVerParientesAvales").css("display", "none");

			this.$parent.ListarParientesAvalesNegocios();
		},
	},
};
</script>

<style lang="css">
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
.mdlVerParientesAvales {
	margin-top: 4% !important;
}

@media (max-width: 900px) {
	.mdlVerParientesAvales {
		width: 99% !important;
		margin-left: 0.5% !important;

		margin-top: 25% !important;
	}
}
/* --------------------------------- */
</style>
