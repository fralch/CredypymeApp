<template>
	<layout ref="layout">
		<div
			class="slot_body slot-cobranza"
			slot="component-view"
			v-if="mi_caja != null"
		>
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'COBRANZA'"></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-3">
								<!-- COLUMNA DE LA IZQUIERDA -->

								<div class="p-1 text-center">
									<button
										type="button"
										class="btn btn-outline-success font-14 bolder w-100"
									>
										Cuotas pend.
										<span class="badge badge-success font-13">{{
											datos_credito.cuotas_pendientes
										}}</span>
									</button>

									<button
										type="button"
										class="btn btn-outline-success mt-1 font-14 bolder w-100"
									>
										Saldo pend.
										<span class="badge badge-success font-13">
											S/ {{ RedondearVista(saldo_por_pagar, 2) }}</span
										>
									</button>
									<button
										type="button"
										class="btn btn-outline-danger mt-1 font-14 bolder w-100"
									>
										Mora pend.
										<span class="badge badge-danger font-13">
											S/
											{{
												RedondearVista(
													datos_credito.mora_total - datos_credito.mora_pagado,
													2
												)
											}}
										</span>
									</button>
									<button
										type="button"
										class="btn btn-outline-danger mt-1 font-14 bolder w-100"
									>
										Notif. pend.
										<span class="badge badge-danger font-13">
											S/
											{{
												RedondearVista(
													datos_credito.notificaciones_total -
														datos_credito.notificaciones_pagado,
													2
												)
											}}
										</span>
									</button>
									<hr />
									<button
										type="button"
										class="btn btn-action font-14 bolder w-100"
									>
										SALDO TOTAL
										<span class="badge badge-light font-13">
											S/ {{ RedondearVista(datos_credito.saldo_total, 2) }}
										</span>
									</button>
									<hr />
									<button
										type="button"
										class="btn btn-outline-dark font-14 bolder w-100"
									>
										Cuotas vencidas
										<span class="badge badge-dark font-13">
											{{ datos_credito.cuotas_vencidas }}
										</span>
									</button>
									<button
										type="button"
										class="btn btn-outline-dark mt-1 font-14 bolder w-100"
									>
										Importe vencido
										<span class="badge badge-dark font-13">
											S/ {{ RedondearVista(datos_credito.monto_vencido, 2) }}
										</span>
									</button>
								</div>

								<div class="bg-white" v-if="modo_cobranza != null">
									<hr />
									<div v-if="modo == 'cobrar' && cancelado == 0">
										<div v-if="!cancelar_credito">
											<button
												class="btn btn-action"
												title="Registrar PAGO"
												style="
													width: 85% !important;
													height: 50px !important;

													margin-left: 7.5%;
													font-weight: bolder;
													font-size: 20px;
												"
												:disabled="frmDatosCobranza.total_cobro == 0"
												@click="PagarCredito"
											>
												<span class="icon text-white">PAGAR</span>
											</button>
											<hr />
										</div>
									</div>
									<div v-if="!cancelar_credito && cancelado == 0">
										<p
											class="text-center bolder"
											style="font-size: 15px; color: #48494b; margin: 0"
										>
											{{
												modo == "imprimir" ? "TOTAL PAGADO" : "TOTAL A PAGAR"
											}}
										</p>
										<p
											class="text-center bolder mb-0"
											style="
												font-size: 24px;
												color: var(--colorAlto);
												font-weight: bolder;
											"
										>
											S/ {{ frmDatosCobranza.total_cobro }}
										</p>
									</div>
								</div>
								<!-- <div class="form-row">
									<div class="input-group col-md-12 col-7">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth >= 900"
												>COBRADOR</span
											>
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth < 900"
												>COBR.</span
											>
										</div>

										<select
											class="form-control center"
											v-model="frmDatosCobranza.usuario_cobrador"
											:disabled="
												modo != 'cobrar' ||
												cancelado != 0 ||
												datos_credito.estado == 'CANCELADO PARCIAL'
											"
										>
											<option :value="0">Ninguno</option>
											<option
												v-for="(item, index) in usuarios_cobradores"
												:key="index"
												:value="item.dni"
											>
												{{ item.usuario }}
											</option>
										</select>
									</div>

									<div class="recibo_style input-group mt-1 col-md-12 col-5">
										<div class="input-group-prepend">
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth >= 900"
												># RECIBO</span
											>
											<span
												class="input-group-text prepend-title"
												v-if="windowWidth < 900"
												># REC.</span
											>
										</div>

										<input
											type="text"
											class="form-control center"
											style="height: 32px !important"
											v-model="frmDatosCobranza.numero_recibo"
											spellcheck="false"
											autocomplete="off"
											:disabled="
												modo != 'cobrar' ||
												cancelado != 0 ||
												datos_credito.estado == 'CANCELADO PARCIAL'
											"
										/>
									</div>
								</div> -->
							</div>
							<div class="tabs_style col-md-9">
								<!-- COLUMNA DE LA DERECHA -->
								<ul class="nav nav-tabs" id="myTab" role="tablist">
									<li class="nav-item">
										<a
											class="nav-link active tab-title"
											id="cuotas-tab"
											data-toggle="tab"
											href="#cuotas"
											role="tab"
											aria-controls="cuotas"
											aria-selected="true"
											>CUOTAS</a
										>
									</li>
									<li class="nav-item" v-if="modo_cobranza != null">
										<a
											class="nav-link tab-title"
											id="cancelacion-tab"
											data-toggle="tab"
											href="#cancelacion"
											role="tab"
											aria-controls="cancelacion"
											aria-selected="false"
											>CANCELACIÓN</a
										>
									</li>
									<li class="nav-item" v-if="modo_cobranza != null">
										<a
											class="nav-link tab-title"
											id="notificaciones-tab"
											data-toggle="tab"
											href="#notificaciones"
											role="tab"
											aria-controls="notificaciones"
											aria-selected="false"
											>NOTIFICACIONES</a
										>
									</li>
									<li class="nav-item">
										<a
											class="nav-link tab-title"
											id="credito-asociado-tab"
											data-toggle="tab"
											href="#credito-asociado"
											role="tab"
											aria-controls="credito-asociado"
											aria-selected="false"
											>CRÉDITOS ASOCIADOS</a
										>
									</li>
								</ul>

								<div class="tab-content" id="myTabContent">
									<!-- tab pago de cuotas de credito -->
									<div
										class="tab-pane fade show active"
										id="cuotas"
										role="tabpanel"
										aria-labelledby="cuotas-tab"
									>
										<div class="form-row">
											<div class="input-group col-md-9 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>TITULAR</span
													>
												</div>
												<input
													type="text"
													class="form-control input-information input-highlight"
													:value="nombre_completo_titular"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-3 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>EXP.</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information input-highlight"
													:value="datos_credito.codigo_expediente"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-5 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>ASESOR</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information input-highlight"
													:value="datos_credito.usuario_asesor"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>

											<div class="input-group col-md-4 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>CAPITAL</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="
														'S/ ' +
														RedondearVista(datos_credito.capital_total, 2)
													"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-3 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>ATRASO</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information input-highlight"
													:value="datos_credito.dias_atraso + ' d'"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-5 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>PLAZO</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="
														roundTo(datos_credito.plazo, 0) +
														' ' +
														periodo_medicion(datos_credito.periodo_pago) +
														' - ' +
														roundTo(datos_credito.tasa_interes, 2) +
														'%'
													"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>

											<!-- <div class="input-group col-md-3 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>CUOTA</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information input-highlight"
													:value="'S/ ' + roundTo(datos_credito.cuota, 2)"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div> -->
											<div class="input-group col-md-4 mb-1 mt-1">
												<div class="input-group-prepend">
													<span
														class="input-group-text prepend-title span-highlight"
														>TIPO</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="datos_credito.tipo"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-3 mb-1 mt-1">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>MORA ESP.
													</span>
												</div>

												<input
													type="text"
													class="form-control input-information"
													:value="
														'S/ ' + roundTo(datos_credito.mora_adicional, 2)
													"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-5 mb-1 mt-1">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>DESEMBOLSO</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="
														Object.keys(datos_credito).length === 0
															? null
															: JSON.parse(datos_credito.datos_creacion).fecha
													"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-3 mb-1 mt-1">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>MODO</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="datos_credito.modo_desembolso"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-4 mb-1 mt-1">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>COBRANZA</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="
														datos_credito.pago_oficina == 1
															? 'OFICINA'
															: 'NEGOCIO'
													"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>
											<div class="input-group col-md-3 mb-1 mt-1">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>DÍAS GRACIA</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="
														datos_credito.dias_gracia_ci +
														datos_credito.dias_gracia_si
													"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>

											<div class="input-group col-md-6 mb-1 mt-1">
												<div class="input-group-prepend">
													<span class="input-group-text prepend-title"
														>PRODUCTO</span
													>
												</div>

												<input
													type="text"
													class="form-control center input-information"
													:value="datos_credito.producto"
													onkeydown="return false"
													spellcheck="false"
												/>
											</div>

											<div class="form-check col-md-3 text-right mt-2">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbModoAvanzado"
													v-model="vista_avanzada"
												/>
												<label class="label-title" for="chbModoAvanzado">
													Avanzado
												</label>
											</div>
										</div>

										<div id="tabla-cuotas">
											<table
												class="table"
												id="tblCuotas"
												style="width: 100% !important"
											>
												<thead>
													<tr>
														<th>N°</th>
														<th>FECHA_VENC</th>
														<th>FECHA_PAGO</th>
														<th style="min-width: 80px !important">CUOTA</th>
														<th
															v-show="vista_avanzada"
															style="background: var(--plomoClaroEmpresarial)"
														>
															CAPITAL
														</th>
														<th
															v-show="vista_avanzada"
															style="background: var(--plomoClaroEmpresarial)"
														>
															INTERÉS
														</th>

														<th>ESTADO</th>
														<th
															v-show="vista_avanzada"
															style="background: var(--plomoClaroEmpresarial)"
														>
															CAPITAL_PAGADO
														</th>
														<th
															v-show="vista_avanzada"
															style="background: var(--plomoClaroEmpresarial)"
														>
															INTERÉS_PAGADO
														</th>

														<th>ACUMULADO</th>
														<th style="min-width: 80px !important">RESTA</th>
														<th style="max-width: 80px !important">
															DÍAS_ATRASO
														</th>
													</tr>
												</thead>
												<tbody>
													<tr
														v-for="(item, index) in detalle_cuotas"
														:key="index"
														class="verde-claro text-dark"
														:class="[
															item.numero_cuota == datos_credito.cuota_actual
																? 'resaltado selected'
																: '',
														]"
														:id="'r_1_' + index"
														@dblclick="DetallePagos"
													>
														<td align="center">
															{{ item.numero_cuota }}
														</td>
														<td align="center">
															{{ item.fecha_vence }}
														</td>
														<td align="center">
															{{ item.fecha_pago }}
														</td>
														<td align="right">S/ {{ item.monto }}</td>
														<td align="right" v-show="vista_avanzada">
															S/ {{ item.capital }}
														</td>
														<td align="right" v-show="vista_avanzada">
															S/
															{{
																parseFloat(item.interes) +
																parseFloat(item.redondeo)
															}}
														</td>
														<td align="center">
															{{ item.estado }}
														</td>
														<td align="right" v-show="vista_avanzada">
															{{
																item.capital_pagado == 0 && item.estado != "CAN"
																	? ""
																	: "S/ " + item.capital_pagado
															}}
														</td>
														<td align="right" v-show="vista_avanzada">
															{{
																item.interes_pagado == 0 && item.estado != "CAN"
																	? ""
																	: "S/ " +
																	  parseFloat(
																			parseFloat(item.interes_pagado) +
																				parseFloat(item.redondeo_pagado)
																	  )
															}}
														</td>

														<td align="right">
															{{
																item.acumulado != null
																	? "S/ " + item.acumulado
																	: ""
															}}
														</td>
														<td align="right">
															{{ item.resta != null ? "S/ " + item.resta : "" }}
														</td>
														<td align="center">
															{{
																item.dias_atraso == 0 ? "" : item.dias_atraso
															}}
														</td>
													</tr>
												</tbody>
											</table>
										</div>

										<hr />

										<div
											class="area-eleccion"
											v-if="
												this.modo_cobranza == null &&
												this.permiso_cobranza_bancaria
											"
										>
											<div class="form-row" style="margin-top: 40px">
												<div class="col-md-6">
													<div class="text-right">
														<button
															class="btn btn-action"
															style="
																width: 200px;
																height: 100px;
																font-size: 20px;
															"
															@click="modo_cobranza = 'VENTANILLA'"
														>
															VENTANILLA
														</button>
													</div>
												</div>
												<div class="col-md-6">
													<button
														class="btn btn-action"
														style="width: 200px; height: 100px; font-size: 20px"
														@click="
															modo_cobranza = 'CUENTA_BANCARIA';
															banco_seleccionado = null;
														"
													>
														CUENTA BANCARIA
													</button>
												</div>
											</div>
										</div>

										<div class="form-row">
											<div class="col-md-7">
												<div class="text-right">
													<button
														class="btn btn-cancel btn-icon-split"
														title="Cancelar MODO de COBRANZA"
														v-if="
															modo_cobranza != null &&
															this.permiso_cobranza_bancaria
														"
														@click="modo_cobranza = null"
														:disabled="modo != 'cobrar' || cancelado != 0"
													>
														<span class="icon text-white">
															<i class="fa fa-backward"></i>
														</span>
														<span class="text">REGRESAR</span>
													</button>
												</div>
											</div>
											<div
												class="col-md-5"
												v-if="this.modo_cobranza == 'CUENTA_BANCARIA'"
											>
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text prepend-title"
															>BANCO</span
														>
													</div>

													<select
														class="form-control center"
														v-model="banco_seleccionado"
														:class="[
															submited
																? banco_seleccionado == null
																	? 'is-invalid'
																	: 'is-valid'
																: '',
														]"
														:disabled="modo != 'cobrar' || cancelado != 0"
													>
														<option :value="null" disabled selected>
															Seleccione...
														</option>
														<option
															v-for="(item, index) in bancos"
															:key="index"
															:value="item.id"
														>
															{{ item.banco }}
														</option>
													</select>
												</div>
											</div>
										</div>
										<div
											id="area-cobranza"
											v-if="
												this.modo_cobranza != null &&
												!this.frmDatosCancelacion.cancelar_credito
											"
										>
											<fieldset
												class="p-0 pt-1 pl-3 pr-2"
												style="background-color: #d8f1fd"
												:disabled="
													modo != 'cobrar' ||
													cancelado != 0 ||
													datos_credito.estado == 'CANCELADO PARCIAL'
												"
											>
												<legend>
													<div class="form-check">
														<input
															class="form-check-input"
															type="checkbox"
															id="chbPagoCuotas"
															:checked="frmDatosCobranza.pago_cuota"
															v-model="frmDatosCobranza.pago_cuota"
															:disabled="
																modo != 'cobrar' ||
																cancelado != 0 ||
																datos_credito.estado == 'CANCELADO PARCIAL'
															"
														/>
														<label
															class="label-title p-1"
															for="chbPagoCuotas"
															style="
																background: var(--colorMedio);
																color: white !important;
																font-size: 12px;
																border-radius: 3px;
															"
														>
															PAGO DE CUOTAS
														</label>
													</div>
												</legend>

												<div
													class="form-row"
													v-if="frmDatosCobranza.pago_cuota"
												>
													<div class="form-group col-md-7 mb-0">
														<div class="form-group">
															<div class="input-group mb-3">
																<div class="input-group-prepend">
																	<div class="input-group-text prepend-title">
																		<input
																			type="radio"
																			name="forma_pago"
																			id="rdbPorCuota"
																			value="por_cuota"
																			v-model="frmDatosCobranza.forma_pago"
																		/>
																	</div>
																</div>
																<div class="input-group-prepend">
																	<label
																		class="input-group-text"
																		for="rdbPorCuota"
																		style="font-size: 13px"
																	>
																		Por cuota
																	</label>
																</div>
																<input
																	type="number"
																	class="form-control center"
																	style="max-width: 70px; font-size: 15px"
																	min="0"
																	:max="datos_credito.cuotas_pendientes"
																	v-model.number="
																		frmDatosCobranza.pago_cuota_cantidad
																	"
																	@change="Redondear"
																	name="por_cuota"
																	step="1"
																	lang="en"
																	v-if="modo_pago == 'por_cuota'"
																	:disabled="modo != 'cobrar'"
																/>
																<div
																	class="input-group-append"
																	v-if="modo_pago == 'por_cuota'"
																>
																	<p
																		class="cuota_style input-group-text text-center"
																		style="
																			width: auto;
																			font-weight: bolder;
																			font-size: 17px;
																			color: var(--colorAlto);
																		"
																	>
																		S/ {{ frmDatosCobranza.pago_cuota_monto }}
																	</p>
																</div>
															</div>

															<div class="input-group">
																<div class="input-group-prepend">
																	<div class="input-group-text">
																		<input
																			type="radio"
																			name="forma_pago"
																			id="rdbPorMonto"
																			value="por_monto"
																			v-model="frmDatosCobranza.forma_pago"
																		/>
																	</div>
																</div>
																<div class="input-group-prepend">
																	<label
																		class="input-group-text"
																		for="rdbPorMonto"
																		style="font-size: 13px"
																	>
																		Por monto
																	</label>
																</div>
																<div
																	class="input-group-prepend"
																	v-if="modo_pago == 'por_monto'"
																>
																	<span
																		class="input-group-text"
																		style="font-size: 15px"
																		>S/
																	</span>
																</div>

																<input
																	type="number"
																	class="form-control center"
																	min="0"
																	step="0.01"
																	lang="en"
																	style="
																		max-width: 150px;
																		font-size: 17px;
																		font-weight: bolder;
																		color: var(--colorAlto);
																	"
																	:max="saldo_por_pagar"
																	@change="Redondear"
																	name="por_monto"
																	v-model.number="frmDatosCobranza.pago_monto"
																	v-if="modo_pago == 'por_monto'"
																	:disabled="modo != 'cobrar'"
																/>
															</div>
														</div>
													</div>
													<div class="form-group col-md-5">
														<label class="label-title">COMENTARIO</label>
														<textarea
															type="text"
															class="form-control text-row mayus"
															rows="2"
															v-model="frmDatosCobranza.comentario"
															:disabled="modo != 'cobrar'"
														></textarea>
													</div>
												</div>
											</fieldset>

											<div class="form-row col-md-12 ml-4 mt-1">
												<div class="form-check col-md-3 col-5">
													<input
														class="form-check-input"
														type="checkbox"
														id="chbPagoMora"
														:checked="frmDatosCobranza.pago_mora"
														v-model.number="frmDatosCobranza.pago_mora"
														:disabled="
															modo != 'cobrar' ||
															cancelado != 0 ||
															mora_pendiente == 0
														"
													/>
													<label
														class="label-title p-1"
														for="chbPagoMora"
														style="
															background: var(--colorMedio);
															color: white !important;
															font-size: 12px;
															border-radius: 3px;
														"
													>
														PAGO DE MORA
													</label>
												</div>
												<div
													class="input-group col-md-6 col-6"
													v-if="frmDatosCobranza.pago_mora"
												>
													<div class="input-group-prepend">
														<span
															class="input-group-text"
															style="font-size: 15px"
															>S/
														</span>
													</div>

													<input
														type="number"
														class="form-control center"
														min="0"
														step="0.1"
														lang="en"
														:max="mora_pendiente"
														v-model.number="frmDatosCobranza.pago_mora_monto"
														name="pago_mora"
														@change="Redondear"
														style="
															max-width: 120px;
															font-size: 17px;
															font-weight: bolder;
															color: var(--colorAlto);
														"
														:disabled="modo != 'cobrar' && cancelado == 0"
													/>
												</div>
												<div
													class="text-right col-md-9"
													v-if="modo == 'imprimir'"
												>
													<div class="btn-group" role="group">
														<button
															class="btn btn-action btn-icon-split"
															title="Nuevo COBRO"
															v-if="cancelado == 0"
															@click="NuevoCobro"
														>
															<span class="icon text-white">
																<i class="fas fa-plus"></i>
															</span>
															<span class="text">NUEVO COBRO</span>
														</button>
														<button
															class="btn btn-cancel btn-icon-split"
															title="Imprimir VOUCHER"
															@click="ImprimirVoucher('cobranza')"
														>
															<span class="icon text-white">
																<i class="fa fa-print"></i>
															</span>
															<span class="text">VOUCHER</span>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- tab de cancelacion de credito -->
									<div
										class="tab-pane fade"
										id="cancelacion"
										role="tabpanel"
										aria-labelledby="cancelacion-tab"
									>
										<fieldset
											style="background-color: #d8f1fd"
											:disabled="
												!frmDatosCancelacion.cancelar_credito || cancelado != 0
											"
										>
											<legend>
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														id="chbCancelacion"
														v-model="frmDatosCancelacion.cancelar_credito"
														:disabled="modo != 'cobrar' || cancelado != 0"
													/>
													<label
														class="label-title p-1"
														for="chbCancelacion"
														style="
															background: var(--colorMedio);
															color: white !important;
															font-size: 12px;
															border-radius: 3px;
														"
													>
														CANCELAR CRÉDITO
													</label>
												</div>
											</legend>

											<div class="form-row">
												<div class="col-md-7">
													<div class="input-group col-sm-11 mb-1 mt-1">
														<div class="input-group-prepend">
															<span
																class="input-group-text prepend-title"
																style="width: 150px"
																>DSCTO MORAS</span
															>
															<span class="input-group-text prepend-title"
																>S/</span
															>
														</div>
														<input
															type="number"
															class="form-control center"
															v-model.number="frmDatosCancelacion.dscto_moras"
															min="0"
															step="0.1"
															lang="en"
															:max="mora_pendiente"
															name="dscto_moras"
															@change="Redondear"
															style="
																font-size: 17px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														/>
													</div>

													<div class="input-group col-sm-11 mb-1 mt-1">
														<div class="input-group-prepend">
															<span
																class="input-group-text prepend-title float-right"
																style="width: 150px"
																>DSCTO NOTIFICACIONES</span
															>
															<span class="input-group-text prepend-title"
																>S/</span
															>
														</div>
														<input
															type="number"
															class="form-control center"
															min="0"
															step="0.1"
															lang="en"
															:max="notificaciones_pendiente"
															name="dscto_notificaciones"
															@change="Redondear"
															v-model.number="
																frmDatosCancelacion.dscto_notificaciones
															"
															style="
																font-size: 17px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														/>
													</div>
													<div class="input-group col-sm-11 mb-1 mt-1">
														<div class="input-group-prepend">
															<span
																class="input-group-text prepend-title"
																style="width: 150px"
																>DSCTO INTERÉS</span
															>
															<span class="input-group-text prepend-title"
																>S/</span
															>
														</div>
														<input
															type="number"
															class="form-control center"
															min="0"
															step="0.10"
															lang="en"
															:max="interes_pendiente"
															name="dscto_interes"
															@change="Redondear"
															v-model.number="frmDatosCancelacion.dscto_interes"
															style="
																font-size: 17px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														/>
													</div>

													<div class="input-group col-sm-12 mb-1 mt-1">
														<div class="input-group-prepend">
															<span class="input-group-text prepend-title"
																>DOCUMENTO</span
															>
														</div>
														<input
															class="btn btn-primary"
															style="
																background-color: var(--plomoOscuroEmpresarial);
																border: none;
																font-size: var(--tamañoLetraLabels);
															"
															type="file"
															accept="image/*"
															id="documento_cancelacion"
															v-if="
																parseFloat(
																	frmDatosCancelacion.total_cancelar
																) != parseFloat(datos_credito.saldo_total)
															"
															@change="AgregarDocumento"
														/>
														<label
															class="label-title ml-2 mt-1"
															v-if="
																parseFloat(
																	frmDatosCancelacion.total_cancelar
																) == parseFloat(datos_credito.saldo_total)
															"
															>No es necesario</label
														>
													</div>
												</div>
												<div
													class="col-md-5 text-center"
													style="display: table"
												>
													<div
														style="display: table-cell; vertical-align: middle"
													>
														<p
															class="label-title"
															style="font-size: 17px !important"
														>
															{{
																cancelado == 0
																	? "TOTAL PARA CANCELAR"
																	: "TOTAL CANCELADO"
															}}
														</p>
														<p
															class="text"
															style="
																font-size: 20px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														>
															S/
															{{ frmDatosCancelacion.total_cancelar }}
														</p>
														<div class="form-group col-md-12">
															<label class="label-title">COMENTARIO</label>
															<textarea
																type="text"
																class="form-control text-row mayus"
																rows="3"
																v-model="frmDatosCancelacion.comentario"
																:disabled="modo != 'cobrar'"
															></textarea>
														</div>
														<button
															class="btn btn-action btn-icon-split"
															title="Cancelar CRÉDITO"
															style="font-size: 11px; font-weight: bolder"
															@click="CancelarCredito"
															v-if="cancelado == 0"
														>
															<span class="icon text-white">S/ </span>
															<span class="text">CANCELAR CRÉDITO</span>
														</button>
													</div>
												</div>
											</div>
										</fieldset>
										<div class="text-right" v-if="modo == 'imprimir_cancelado'">
											<hr />
											<button
												class="btn btn-cancel btn-icon-split"
												title="Imprimir VOUCHER"
												@click="ImprimirVoucher('cancelacion')"
											>
												<span class="icon text-white">
													<i class="fa fa-print"></i>
												</span>
												<span class="text">VOUCHER</span>
											</button>
										</div>
										<fieldset style="min-width: 100%">
											<legend>
												<label class="label-title">CRONOGRAMA DE PAGOS</label>
											</legend>
											<div id="tabla-cronograma">
												<table
													class="table"
													id="tblCronograma"
													style="width: 100% !important"
												>
													<thead>
														<tr>
															<th style="min-width: 50px !important">FECHA</th>
															<th style="min-width: 20px !important">N°</th>
															<th style="min-width: 50px !important">CUOTA</th>
															<th style="max-width: 50px !important">
																CAPITAL
															</th>
															<th style="max-width: 50px !important">
																INTERÉS
															</th>
															<!-- <th style="max-width: 70px !important">
																REDONDEO
															</th> -->
															<th style="max-width: 70px !important">
																SALDO_CAP
															</th>
															<th style="max-width: 70px !important">
																SALDO_INT
															</th>
															<!-- <th style="max-width: 70px !important">
																SALDO_RED
															</th> -->
														</tr>
													</thead>
													<tbody>
														<tr
															v-for="(item, index) in detalle_cuotas"
															:key="index"
															:class="[
																item.numero_cuota == datos_credito.cuota_actual
																	? 'resaltado selected'
																	: index % 2 == 0
																	? 'verde-claro'
																	: '',
															]"
															:id="'r_2_' + index"
														>
															<td align="center">
																{{ item.fecha_vence }}
															</td>
															<td align="center">
																{{ item.numero_cuota }}
															</td>
															<td align="center">
																S/ {{ roundTo(item.monto, 2) }}
															</td>
															<td align="right">
																S/ {{ roundTo(item.capital, 2) }}
															</td>
															<td align="right">
																S/
																{{ roundTo(item.interes + item.redondeo, 2) }}
															</td>
															<!-- <td align="right">
																S/ {{ roundTo(item.redondeo, 2) }}
															</td> -->
															<td
																align="right"
																style="
																	font-size: 11px;
																	font-weight: bolder;
																	color: var(--colorAlto);
																"
															>
																S/ {{ roundTo(item.saldo_capital, 2) }}
															</td>
															<td
																align="right"
																style="
																	font-size: 11px;
																	font-weight: bolder;
																	color: var(--colorAlto);
																"
															>
																S/
																{{
																	roundTo(
																		item.saldo_interes + item.saldo_redondeo,
																		2
																	)
																}}
															</td>
															<!-- <td
																align="right"
																style="
																	font-size: 11px;
																	font-weight: bolder;
																	color: var(--colorAlto);
																"
															>
																S/ {{ roundTo(item.saldo_redondeo, 2) }}
															</td> -->
														</tr>
													</tbody>
												</table>
											</div>
										</fieldset>
									</div>
									<!-- tab de pagos -->
									<div
										class="tab-pane fade"
										id="notificaciones"
										role="tabpanel"
										aria-labelledby="notificaciones-tab"
									>
										<div class="form-row p-3">
											<div class="col-md-3">
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														v-model="ver_notificaciones_pendientes"
														id="chbNotificacionesPendientes"
													/>
													<label
														class="label-title"
														for="chbNotificacionesPendientes"
													>
														SÓLO PENDIENTES
													</label>
												</div>
											</div>
											<div class="col-md-6">
												<label class="label-title"
													>NOTIFICACIONES:
													<span
														class="bolder"
														style="font-size: 1rem; color: var(--colorAlto)"
													>
														S/
														{{
															roundTo(datos_credito.notificaciones_total, 2)
														}}</span
													></label
												>
												<label class="label-title"
													>ABONADO:
													<span
														class="bolder"
														style="font-size: 1rem; color: var(--colorAlto)"
													>
														S/
														{{
															roundTo(datos_credito.notificaciones_pagado, 2)
														}}</span
													></label
												>
											</div>
											<div class="col-md-3">
												<label class="label-title"
													>RESTA:
													<span
														class="bolder"
														style="font-size: 1rem; color: var(--colorAlto)"
													>
														S/
														{{
															roundTo(
																datos_credito.notificaciones_total -
																	datos_credito.notificaciones_pagado,
																2
															)
														}}</span
													></label
												>
											</div>
										</div>
										<table class="table" id="tblNotificaciones">
											<thead>
												<tr>
													<th>N°</th>
													<th style="min-width: 300px">NOTIFICACIÓN</th>
													<th style="min-width: 100px">FECHA_REGISTRO</th>
													<th style="min-width: 70px">MONTO</th>
													<th>ESTADO</th>
													<th style="min-width: 70px">ACUMULADO</th>
													<th style="min-width: 70px">RESTA</th>
													<th>N°_CUO</th>
													<th>APLICADO_POR</th>
													<th>USUARIO_ENVÍO</th>
													<th style="min-width: 300px">COMENTARIO</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in notificaciones"
													:key="index"
													class="table-bordered"
													:id="'not_' + item.id"
												>
													<td align="center">{{ index + 1 }}</td>
													<td>{{ item.tipo }}</td>
													<td align="center">
														{{ JSON.parse(item.datos_creacion).fecha }}
													</td>
													<td align="right">S/ {{ roundTo(item.monto, 2) }}</td>
													<td align="center">
														{{
															item.estado == "P"
																? "PEN"
																: item.estado == "C"
																? "CAN"
																: ""
														}}
													</td>
													<td align="right">
														S/ {{ roundTo(item.acumulado, 2) }}
													</td>
													<td align="right">
														S/ {{ roundTo(item.monto - item.acumulado, 2) }}
													</td>
													<td align="center">{{ item.numero_cuota }}</td>
													<td align="center">{{ item.usuario_registro }}</td>
													<td align="center">
														{{
															item.usuario_envio_usuario == null
																? "-"
																: item.usuario_envio_usuario
														}}
													</td>
													<td>
														{{
															item.descripcion_envio == null
																? "-"
																: item.descripcion_envio
														}}
													</td>
												</tr>
											</tbody>
										</table>
										<hr />
										<br />

										<div class="form-row col-md-12">
											<div class="form-check col-md-6 text-right col-7">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbPagoNotificacion"
													:checked="frmDatosCobranza.pago_notificaciones"
													v-model.number="frmDatosCobranza.pago_notificaciones"
													:disabled="
														modo != 'cobrar' ||
														cancelado != 0 ||
														notificaciones_pendiente == 0
													"
												/>
												<label
													class="label-title p-1"
													for="chbPagoNotificacion"
													style="
														background: var(--colorMedio);
														color: white !important;
														font-size: 12px;
														border-radius: 3px;
													"
												>
													PAGO DE NOTIFICACIONES
												</label>
											</div>
											<div
												class="input-group col-md-6 col-5"
												v-if="frmDatosCobranza.pago_notificaciones"
											>
												<div class="input-group-prepend">
													<span class="input-group-text" style="font-size: 15px"
														>S/
													</span>
												</div>

												<input
													type="number"
													class="form-control center"
													style="
														max-width: 120px;
														font-size: 17px;
														font-weight: bolder;
														color: var(--colorAlto);
													"
													min="0.1"
													:max="datos_credito.notificaciones_pendiente"
													v-model.number="
														frmDatosCobranza.pago_notificaciones_monto
													"
													name="pago_notificaciones"
													@change="Redondear"
													step="1"
													lang="en"
													:disabled="modo != 'cobrar'"
												/>
											</div>
										</div>
									</div>
									<!-- tab de creditos asociados -->
									<div
										class="tab-pane fade"
										id="credito-asociado"
										role="tabpanel"
										aria-labelledby="credito-asociado-tab"
									>
										<label class="label-title">CREDITOS ADICIONALES</label>
										<table
											class="table"
											id="tblCreditosAdicionales"
											width="100% !important"
										>
											<thead>
												<tr>
													<th>N°</th>
													<th>EXPEDIENTE</th>
													<th style="min-width: 140px">TIPO_CRÉDITO</th>
													<th style="min-width: 220px">APELLIDOS_NOMBRES</th>
													<th>MONTO(S/)</th>
													<th style="min-width: 80px">PLAZO</th>

													<th>ASESOR</th>
													<th>FECHA_DESEMBOLSO</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in credito_adicionales"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
													@dblclick="Redirigir(item)"
												>
													<td align="center">
														{{ index + 1 }}
													</td>
													<td align="center">
														{{
															item.codigo_expediente == null
																? "-"
																: item.codigo_expediente
														}}
													</td>

													<td align="center">
														{{ item.tipo }}
													</td>

													<td align="center">
														{{
															item.apellido_paterno +
															" " +
															item.apellido_materno +
															" " +
															item.nombres
														}}
													</td>
													<td align="center">
														{{ roundTo(item.capital_total, 2) }}
													</td>
													<td align="center">
														{{
															roundTo(item.plazo, 0) +
															" " +
															periodo_medicion(item.periodo_pago)
														}}
													</td>

													<td align="center">
														{{ item.usuario_asesor }}
													</td>
													<td align="center">
														{{ JSON.parse(item.datos_creacion).fecha }}
													</td>
												</tr>
											</tbody>
										</table>
										<hr />
										<label class="label-title">SU PARIENTE/AVAL</label>
										<table
											class="table"
											id="tblCreditosVinculadosDe"
											width="100% !important"
										>
											<thead>
												<tr>
													<th>N°</th>
													<th>EXPEDIENTE</th>
													<th>VÍNCULO</th>
													<th>APELLIDOS_NOMBRES</th>
													<th>MONTO(S/)</th>
													<th>PLAZO</th>
													<th>TIPO_CRÉDITO</th>
													<th>ASESOR</th>
													<th>FECHA_DESEMBOLSO</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in creditos_vinculados_de"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
													@dblclick="Redirigir(item)"
												>
													<td align="center">
														{{ index + 1 }}
													</td>
													<td align="center">
														{{ item.codigo_expediente }}
													</td>
													<td align="center">
														{{ item.vinculo }}
													</td>
													<td>
														{{
															item.apellido_paterno +
															" " +
															item.apellido_materno +
															" " +
															item.nombres
														}}
													</td>
													<td align="right">
														{{ roundTo(item.capital_total, 2) }}
													</td>
													<td align="center">
														{{
															roundTo(item.plazo, 0) +
															" " +
															periodo_medicion(item.periodo_pago)
														}}
													</td>
													<td align="center">
														{{ item.tipo }}
													</td>
													<td align="center">
														{{ item.usuario_asesor }}
													</td>
													<td align="center">
														{{ JSON.parse(item.datos_creacion).fecha }}
													</td>
												</tr>
											</tbody>
										</table>
										<hr />
										<label class="label-title">ES PARIENTE/AVAL DE</label>
										<table
											class="table"
											id="tblCreditosVinculadosA"
											width="100% !important"
										>
											<thead>
												<tr>
													<th>N°</th>
													<th>EXPEDIENTE</th>
													<th>VÍNCULO</th>
													<th style="min-width: 250px">APELLIDOS_NOMBRES</th>
													<th>MONTO(S/)</th>
													<th style="min-width: 80px">PLAZO</th>
													<th>TIPO_CRÉDITO</th>
													<th>ASESOR</th>
													<th>FECHA_DESEMBOLSO</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in creditos_vinculados_a"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
													@dblclick="Redirigir(item)"
												>
													<td align="center">
														{{ index + 1 }}
													</td>
													<td align="center">
														{{
															item.codigo_expediente == null
																? "-"
																: item.codigo_expediente
														}}
													</td>
													<td align="center">
														{{ item.vinculo }}
													</td>
													<td>
														{{
															item.apellido_paterno +
															" " +
															item.apellido_materno +
															" " +
															item.nombres
														}}
													</td>
													<td align="left">
														{{ roundTo(item.capital_total, 2) }}
													</td>
													<td align="center">
														{{
															roundTo(item.plazo, 0) +
															" " +
															periodo_medicion(item.periodo_pago)
														}}
													</td>
													<td align="center">
														{{ item.tipo }}
													</td>
													<td align="center">
														{{ item.usuario_asesor }}
													</td>
													<td align="center">
														{{ JSON.parse(item.datos_creacion).fecha }}
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
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

export default {
	components: { layout, headerClose },
	props: {
		agencia_id: Number,
		credito_id: Number,
		cancelado: Number,
		datos_credito: Object,
		datos_cuotas: Array,
		creditos_vinculados_a: Array,
		creditos_vinculados_de: Array,
		credito_adicionales: Array,
		notificaciones: Array,
		usuarios_cobradores: Array,
		datos_voucher: Array,
		bancos: Array,
	},
	data() {
		return {
			windowWidth: window.innerWidth,

			submited: false,
			vista_avanzada: false,
			detalle_cronograma: [],
			modo: "cobrar",
			ver_notificaciones_pendientes: true,

			modo_cobranza: null,
			banco_seleccionado: null,

			frmDatosCobranza: {
				pago_cuota_cantidad: 0,
				pago_cuota_monto: this.roundTo(0, 2),
				pago_monto: this.roundTo(0, 2),
				pago_cuota: this.datos_credito.estado != "CANCELADO PARCIAL",
				pago_mora: false,
				pago_mora_monto: this.roundTo(0, 2),
				pago_notificaciones: false,
				pago_notificaciones_monto: this.roundTo(0, 2),
				forma_pago: "por_cuota",
				usuario_cobrador: this.datos_credito.pago_oficina
					? 0
					: (this.datos_credito.cobrador_id = null
							? 0
							: this.datos_credito.cobrador_id),
				numero_recibo: null,
				comentario: null,
				total_cobro: this.roundTo(0, 2),
			},
			frmDatosCancelacion: {
				cancelar_credito: false,
				interes_moratorio_adicional: this.roundTo(0, 2),
				dscto_notificaciones: this.roundTo(0, 2),
				dscto_moras: this.roundTo(0, 2),
				dscto_interes: this.roundTo(0, 2),
				documento: null,
				comentario: null,
				total_cancelar: this.roundTo(0, 2),
			},
		};
	},

	computed: {
		permiso_cobranza_bancaria() {
			let permiso =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CAJA/COBRANZA_BANCARIA"
				);

			if (permiso.length > 0) {
				let agencia = JSON.parse(permiso[0].acceso_agencias).filter(
					(item) => item.agencia_id == this.agencia_id
				);

				if (agencia.length > 0) {
					return true;
				} else {
					this.modo_cobranza = "VENTANILLA";
					return false;
				}
			} else {
				this.modo_cobranza = "VENTANILLA";
				return false;
			}
		},

		mi_caja() {
			return this.$inertia.page.props.creditos_datos.datos_caja;
		},
		detalle_cuotas() {
			let lista = [];

			let saldo_interes = parseFloat(this.datos_credito.interes_total);
			let saldo_capital = parseFloat(this.datos_credito.capital_total);
			let saldo_redondeo = parseFloat(this.datos_credito.redondeo_total);

			this.datos_cuotas.forEach((element) => {
				saldo_capital -= parseFloat(element.capital);
				saldo_interes -= parseFloat(element.interes);
				saldo_redondeo -= parseFloat(element.redondeo);

				let fecha_pago = null;

				if (element.fecha_ultimo_pago != null) {
					if (element.estado == "C") {
						fecha_pago = element.fecha_ultimo_pago.substring(0, 10);
					}
				}

				let object = {
					numero_cuota: element.numero_cuota,
					fecha_vence: this.formato_fecha(element.fecha_vencimiento),
					fecha_pago: this.formato_fecha(fecha_pago),
					capital: this.roundTo(element.capital, 2),
					interes: this.roundTo(element.interes, 2),
					redondeo: this.roundTo(element.redondeo, 2),
					monto: this.roundTo(element.cuota, 2),
					estado:
						element.estado == "P"
							? "VIG"
							: element.estado == "C"
							? "CAN"
							: element.estado == "V"
							? "VEN"
							: "",

					acumulado:
						element.estado != "C" ? this.roundTo(element.acumulado, 2) : null,
					capital_pagado: this.roundTo(element.capital_pagado, 2),
					interes_pagado: this.roundTo(element.interes_pagado, 2),
					redondeo_pagado: this.roundTo(element.redondeo_pagado, 2),
					resta:
						element.estado != "C"
							? this.roundTo(
									parseFloat(element.cuota) - parseFloat(element.acumulado),
									2
							  )
							: null,
					dias_atraso: element.dias_atraso,
					saldo_capital: this.roundTo(saldo_capital, 2),
					saldo_interes: this.roundTo(saldo_interes, 2),
					saldo_redondeo: this.roundTo(saldo_redondeo, 2),
				};

				if (saldo_interes < 0) {
					saldo_interes = 0;
				}

				lista.push(object);
			});
			return lista;
		},
		nombre_completo_titular() {
			return (
				this.datos_credito.apellido_paterno +
				" " +
				this.datos_credito.apellido_materno +
				" " +
				this.datos_credito.nombres
			);
		},
		capital_pendiente() {
			let capital_total = parseFloat(this.datos_credito.capital_total);
			let capital_pagado = parseFloat(this.datos_credito.capital_pagado);

			let capital_pendiente = capital_total - capital_pagado;

			if (capital_pendiente < 0) {
				capital_pendiente = 0;
			}

			return capital_pendiente;
		},
		interes_pendiente() {
			let interes_total = parseFloat(this.datos_credito.interes_total);
			let interes_pagado = parseFloat(this.datos_credito.interes_pagado);

			let interes_pendiente = interes_total - interes_pagado;

			if (interes_pendiente < 0) {
				interes_pendiente = 0;
			}

			this.frmDatosCancelacion.dscto_interes = this.roundTo(
				interes_pendiente,
				2
			);

			return interes_pendiente;
		},
		redondeo_pendiente() {
			let redondeo_total = parseFloat(this.datos_credito.redondeo_total);
			let redondeo_pagado = parseFloat(this.datos_credito.redondeo_pagado);

			let redondeo_pendiente = redondeo_total - redondeo_pagado;

			if (redondeo_pendiente < 0) {
				redondeo_pendiente = 0;
			}

			return redondeo_pendiente;
		},
		saldo_por_pagar() {
			let saldo_por_pagar =
				parseFloat(this.capital_pendiente) +
				parseFloat(this.interes_pendiente) +
				parseFloat(this.redondeo_pendiente);
			return parseFloat(saldo_por_pagar);
		},
		modo_pago() {
			return this.frmDatosCobranza.forma_pago;
		},
		pago_cuota() {
			return this.frmDatosCobranza.pago_cuota;
		},
		pago_mora() {
			return this.frmDatosCobranza.pago_mora;
		},
		pago_notificaciones() {
			return this.frmDatosCobranza.pago_notificaciones;
		},
		mora_pendiente() {
			let mora_total = parseFloat(this.datos_credito.mora_total);
			let mora_pagado = parseFloat(this.datos_credito.mora_pagado);

			let mora_pendiente = mora_total - mora_pagado;

			if (mora_pendiente < 0) {
				mora_pendiente = 0;
			}

			return parseFloat(mora_pendiente.toFixed(2));
		},
		notificaciones_pendiente() {
			let notificaciones_total = parseFloat(
				this.datos_credito.notificaciones_total
			);
			let notificaciones_pagado = parseFloat(
				this.datos_credito.notificaciones_pagado
			);

			let notificaciones_pendiente =
				notificaciones_total - notificaciones_pagado;

			if (notificaciones_pendiente < 0) {
				notificaciones_pendiente = 0;
			}

			return parseFloat(notificaciones_pendiente.toFixed(2));
		},
		cancelar_credito() {
			return this.frmDatosCancelacion.cancelar_credito;
		},
	},
	watch: {
		vista_avanzada() {
			$("#tblCuotas").DataTable().destroy();
			this.TablaCuotas();
		},
		modo_pago() {
			this.frmDatosCobranza.pago_cuota_cantidad = 0;
			this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
			this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);

			this.ActualizarTotalCobro();
		},
		pago_cuota() {
			this.frmDatosCobranza.pago_cuota_cantidad = 0;
			this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
			this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);

			this.ActualizarTotalCobro();
		},
		pago_mora() {
			this.frmDatosCobranza.pago_mora_monto = this.roundTo(0, 2);

			this.ActualizarTotalCobro();
		},
		pago_notificaciones() {
			this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(0, 2);

			this.ActualizarTotalCobro();
		},
		cancelar_credito(value) {
			this.frmDatosCancelacion.dscto_moras = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_notificaciones = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_interes = this.roundTo(
				this.interes_pendiente,
				2
			);
			this.frmDatosCancelacion.comentario = null;

			if (value) {
				this.ActualizarTotalCancelacion();
			} else {
				this.frmDatosCancelacion.total_cancelar = this.roundTo(0, 2);
			}
		},
		modo(value) {
			if (value == "cobrar") {
				this.submited = false;

				if (this.permiso_cobranza_bancaria) {
					this.modo_cobranza = null;
				} else {
					this.modo_cobranza = "VENTANILLA";
				}

				this.frmDatosCancelacion.dscto_moras = this.roundTo(0, 2);
				this.frmDatosCancelacion.dscto_notificaciones = this.roundTo(0, 2);
				this.frmDatosCancelacion.dscto_interes = this.roundTo(
					this.interes_pendiente,
					2
				);
				if (this.cancelar_credito) {
					this.ActualizarTotalCancelacion();
				} else {
					this.frmDatosCancelacion.total_cancelar = this.roundTo(0, 2);
				}

				this.frmDatosCobranza.pago_cuota_cantidad = 0;
				this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
				this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);

				this.frmDatosCobranza.pago_mora_monto = this.roundTo(0, 2);
				this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(0, 2);

				this.ActualizarTotalCobro();

				this.frmDatosCobranza.pago_cuota =
					this.datos_credito.estado != "CANCELADO PARCIAL";

				this.frmDatosCobranza.forma_pago = "por_cuota";
				this.frmDatosCobranza.pago_mora = false;
				this.frmDatosCobranza.pago_notificaciones = false;
				this.frmDatosCobranza.comentario = null;
				this.frmDatosCobranza.usuario_cobrador = 0;
				this.frmDatosCobranza.numero_recibo = null;
			}
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
		if (this.mi_caja == null) {
			Swal.fire({
				icon: "error",
				title: "¡Ups!",
				text: "Primero debe aperturar CAJA",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Ok',
				confirmButtonColor: "var(--colorAlto)",
				allowOutsideClick: true,
			});
			return this.$inertia.get(route("cre.index"));
		} else {
			let self = this;
			this.TablaCuotas();
			this.TablaCronograma();
			this.TablaNotificaciones();
			this.TablaCreditosVinculadosDe();
			this.TablaCreditosVinculadosA();
			this.TablaCreditosAdicionales();

			$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
				$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();

				self.PosicionarTablas();
			});

			this.VerificarPermiso();
		}
	},
	methods: {
		VerificarPermiso() {
			let permiso =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CAJA/COBRANZA_BANCARIA"
				);

			if (permiso.length > 0) {
				let agencia = JSON.parse(permiso[0].acceso_agencias).filter(
					(item) => item.agencia_id == this.agencia_id
				);

				if (agencia.length > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		},
		Redirigir(credito) {
			let object = {};
			object = {
				credito_id: credito.id,
				agencia_id: credito.agencia_id,
			};
			Swal.fire({
				icon: "question",
				title: "¿Desea cambiar la cobranza a este crédito?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.$inertia.get(route("caj.cobranza", object));
				} else {
					return false;
				}
			});
		},

		formato_fecha(value) {
			if (value != null) {
				return (
					String(value).substring(8, 10) +
					"/" +
					String(value).substring(5, 7) +
					"/" +
					String(value).substring(0, 4)
				);
			} else {
				return null;
			}
		},
		PosicionarTablas() {
			let cuota_actual = parseInt(this.datos_credito.cuota_actual);
			let posicion = cuota_actual - 4;

			if (posicion < 0) {
				posicion = 0;
			}

			let selection_1 = $("#tblCuotas #r_1_" + posicion);
			let selection_2 = $("#tblCronograma #r_2_" + posicion);

			$("#tabla-cuotas .dataTables_scrollBody").scrollTo(selection_1);
			$("#tabla-cronograma .dataTables_scrollBody").scrollTo(selection_2);
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

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}

			let nombre = e.target.name;
			let cuota = parseFloat(this.datos_credito.cuota);
			let cuotas_pendientes = parseFloat(this.datos_credito.cuotas_pendientes);

			if (nombre == "por_cuota") {
				valor = parseFloat(this.roundTo(valor, 0));

				if (valor > cuotas_pendientes) {
					valor = cuotas_pendientes;
				}

				let monto = 0;
				if (valor > 0) {
					let acumulado = this.datos_cuotas.filter(
						(item) => item.numero_cuota == this.datos_credito.cuota_actual
					)[0].acumulado;

					monto = parseFloat(
						this.roundTo(valor * cuota - parseFloat(acumulado), 2)
					);
				}

				if (monto > this.saldo_por_pagar) {
					this.frmDatosCobranza.pago_cuota_monto = this.roundTo(
						this.saldo_por_pagar,
						2
					);
				} else {
					this.frmDatosCobranza.pago_cuota_monto = this.roundTo(monto, 2);
				}
				this.frmDatosCobranza.pago_cuota_cantidad = valor;

				this.ActualizarTotalCobro();
				return false;
			} else if (nombre == "por_monto") {
				if (valor > this.saldo_por_pagar) {
					this.frmDatosCobranza.pago_monto = this.roundTo(
						this.saldo_por_pagar,
						2
					);
				} else {
					this.frmDatosCobranza.pago_monto = this.roundTo(valor, 2);
				}
				this.ActualizarTotalCobro();
				return false;
			} else if (nombre == "pago_mora") {
				if (valor > this.mora_pendiente) {
					this.frmDatosCobranza.pago_mora_monto = this.roundTo(
						this.mora_pendiente,
						2
					);
				} else {
					this.frmDatosCobranza.pago_mora_monto = this.roundTo(valor, 2);
				}
				this.ActualizarTotalCobro();
				return false;
			} else if (nombre == "dscto_moras") {
				if (valor > this.mora_pendiente) {
					this.frmDatosCancelacion.dscto_moras = this.roundTo(
						this.mora_pendiente,
						2
					);
				} else {
					this.frmDatosCancelacion.dscto_moras = this.roundTo(valor, 2);
				}

				this.ActualizarTotalCancelacion();

				return false;
			} else if (nombre == "dscto_notificaciones") {
				if (valor > this.notificaciones_pendiente) {
					this.frmDatosCancelacion.dscto_notificaciones = this.roundTo(
						this.notificaciones_pendiente,
						2
					);
				} else {
					this.frmDatosCancelacion.dscto_notificaciones = this.roundTo(
						valor,
						2
					);
				}
				this.ActualizarTotalCancelacion();
				return false;
			} else if (nombre == "dscto_interes") {
				if (valor > this.interes_pendiente) {
					this.frmDatosCancelacion.dscto_interes = this.roundTo(
						this.interes_pendiente,
						2
					);
				} else {
					this.frmDatosCancelacion.dscto_interes = this.roundTo(valor, 2);
				}
				this.ActualizarTotalCancelacion();
				return false;
			} else if (nombre == "pago_notificaciones") {
				if (valor > this.notificaciones_pendiente) {
					this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(
						this.notificaciones_pendiente,
						2
					);
				} else {
					this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(
						valor,
						2
					);
				}
				this.ActualizarTotalCobro();
				return false;
			}
		},

		RedondearVista(value, decimal_places) {
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

		ActualizarTotalCobro() {
			let total_notificaciones =
				this.frmDatosCobranza.pago_notificaciones_monto;

			this.frmDatosCobranza.total_cobro = this.roundTo(
				parseFloat(this.frmDatosCobranza.pago_cuota_monto) +
					parseFloat(this.frmDatosCobranza.pago_monto) +
					parseFloat(this.frmDatosCobranza.pago_mora_monto) +
					parseFloat(total_notificaciones),
				2
			);
		},
		ActualizarTotalCancelacion() {
			this.frmDatosCancelacion.total_cancelar = this.roundTo(
				parseFloat(this.datos_credito.saldo_total) -
					parseFloat(this.frmDatosCancelacion.dscto_moras) -
					parseFloat(this.frmDatosCancelacion.dscto_notificaciones) -
					parseFloat(this.frmDatosCancelacion.dscto_interes),
				2
			);
		},
		TablaCuotas() {
			let self = this;
			this.$nextTick(() => {
				$("#tblCuotas").DataTable({
					scrollY: "150px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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
				self.PosicionarTablas();
			});
		},
		TablaCronograma() {
			this.$nextTick(() => {
				$("#tblCronograma").DataTable({
					scrollY: "200px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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
		TablaNotificaciones() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblNotificaciones").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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

				if (self.ver_notificaciones_pendientes) {
					table.column(4).search("PEN").draw();
				} else {
					table.column(4).search("").draw();
				}

				$("#chbNotificacionesPendientes").change(function () {
					if (self.ver_notificaciones_pendientes) {
						table.column(4).search("PEN").draw();
					} else {
						table.column(4).search("").draw();
					}
				});
			});
		},

		TablaCreditosVinculadosDe() {
			this.$nextTick(() => {
				$("#tblCreditosVinculadosDe").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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
		TablaCreditosVinculadosA() {
			this.$nextTick(() => {
				$("#tblCreditosVinculadosA").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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
		TablaCreditosAdicionales() {
			this.$nextTick(() => {
				$("#tblCreditosAdicionales").DataTable({
					scrollY: "350px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
					ordering: false,
					fixedHeader: true,
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
		AgregarDocumento(e) {
			this.frmDatosCancelacion.documento = e.target.files[0];
		},
		DetallePagos() {
			let pago_cuotas = [];
			let pago_moras = [];
			let pago_notificaciones = [];

			let mdlDetallePagos = this.$refs.layout.$refs.mdlDetallePagos;

			let data = new FormData();
			data.append("agencia_id", this.agencia_id);
			data.append("credito_id", this.credito_id);

			axios
				.post(route("caj.pago_cuotas.listar"), data)
				.then(function (response) {
					pago_cuotas = response.data;
					mdlDetallePagos.pago_cuotas = pago_cuotas;
				});

			axios
				.post(route("caj.pago_moras.listar"), data)
				.then(function (response) {
					pago_moras = response.data;
					mdlDetallePagos.pago_moras = pago_moras;
				});

			axios
				.post(route("caj.pago_notificaciones.listar"), data)
				.then(function (response) {
					pago_notificaciones = response.data;
					mdlDetallePagos.pago_notificaciones = pago_notificaciones;
				});

			$("#mdlDetallePagos").css("display", "block");
		},
		async PagarCredito() {
			this.submited = true;
			let mensaje = "";

			if (this.pago_cuota) {
				if (
					this.modo_pago == "por_cuota" &&
					this.frmDatosCobranza.pago_cuota_cantidad == 0
				) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "La cantidad de CUOTAS no puede ser 0",
						allowOutsideClick: true,
					});

					return false;
				} else if (
					this.modo_pago == "por_monto" &&
					this.frmDatosCobranza.pago_monto == 0
				) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "El pago por monto no puede ser 0",
						allowOutsideClick: true,
					});

					return false;
				} else {
					if (this.modo_pago == "por_cuota") {
						mensaje +=
							this.frmDatosCobranza.pago_cuota_cantidad +
							" CUOTA(S)=S/ " +
							this.roundTo(this.frmDatosCobranza.pago_cuota_monto, 2) +
							" ";
					} else if (this.modo_pago == "por_monto") {
						mensaje +=
							"MONTO=S/ " +
							this.roundTo(this.frmDatosCobranza.pago_monto, 2) +
							" ";
					}
				}
			}

			if (this.pago_mora) {
				if (this.frmDatosCobranza.pago_mora_monto == 0) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "El pago de MORA no puede ser 0",
						allowOutsideClick: true,
					});

					return false;
				} else {
					mensaje +=
						"MORAS=S/ " +
						this.roundTo(this.frmDatosCobranza.pago_mora_monto, 2) +
						" ";
				}
			}

			if (this.pago_notificaciones) {
				if (this.frmDatosCobranza.pago_notificaciones_monto == 0) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "El pago de NOTIFICACIONES no puede ser 0",
						allowOutsideClick: true,
					});

					return false;
				} else {
					mensaje +=
						"NOTIFICACIONES=S/ " +
						this.roundTo(this.frmDatosCobranza.pago_notificaciones_monto, 2) +
						" ";
				}
			}

			let verificacion = await this.VerificarEnCarrito();

			if (verificacion.esta_en_carrito) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					html:
						"No se puede pagar este crédito porque tiene una <span class='bolder'>COBRANZA</span> en el <span class='bolder'>CARRITO</span> de: " +
						'<label class="p-2 bg-dark text-white bolder">' +
						verificacion.usuario_carrito +
						"</label>",
					allowOutsideClick: true,
				});

				return false;
			}

			if (this.modo_cobranza == "CUENTA_BANCARIA") {
				if (this.banco_seleccionado == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Por favor, seleccione el BANCO donde se realizó el pago.",
						allowOutsideClick: true,
					});
					$('#myTab a[href="#cuotas"]').tab("show");
					return false;
				} else {
					this.frmDatosCobranza.pago_banco = true;
					this.frmDatosCobranza.banco_id = this.banco_seleccionado;
				}
			} else {
				this.frmDatosCobranza.pago_banco = false;
				this.frmDatosCobranza.banco_id = null;
			}

			// if (this.frmDatosCobranza.numero_recibo != null) {
			// 	let data = new FormData();
			// 	data.append("agencia_id", this.agencia_id);
			// 	data.append("numero_recibo", this.frmDatosCobranza.numero_recibo);

			// 	// this.$inertia.post(route("caj.cobranza.verificar_recibo"), data);

			// 	let resultado = await axios
			// 		.post(route("caj.cobranza.verificar_recibo"), data)
			// 		.then(function (response) {
			// 			return response.data;
			// 		});

			// 	if (resultado == "EXISTE") {
			// 		Swal.fire({
			// 			icon: "error",
			// 			title: "¡Ups!",
			// 			text: "El número de RECIBO ingresado, ya está registrado en otro pago",
			// 			allowOutsideClick: true,
			// 		});

			// 		return false;
			// 	}
			// }

			if (
				this.roundTo(this.frmDatosCobranza.total_cobro, 2) ==
				this.roundTo(this.datos_credito.saldo_total, 2)
			) {
				Swal.fire({
					title:
						"El monto a pagar es igual al SALDO TOTAL, ¿Desea cancelar el crédito?",
					confirmButtonText: "Si",
					showCancelButton: true,
					cancelButtonText: "No",
					allowOutsideClick: false,
				}).then(async (result) => {
					if (result.isConfirmed) {
						await (this.frmDatosCancelacion.cancelar_credito = true);

						this.frmDatosCancelacion.dscto_moras = 0;
						this.frmDatosCancelacion.dscto_notificaciones = 0;
						this.frmDatosCancelacion.dscto_interes = 0;
						this.frmDatosCancelacion.total_cancelar =
							this.datos_credito.saldo_total;

						this.frmDatosCancelacion.comentario =
							this.frmDatosCobranza.comentario;

						$("#cancelacion-tab").tab("show");

						this.CancelarCredito();
					} else {
						return false;
					}
				});
			} else {
				Swal.fire({
					title: "¿Desea REGISTRAR el pago?",
					html:
						'<label class="p-2 bg-warning" style="font-weight: bolder;">' +
						mensaje +
						'</label><br><label class="p-2 bg-dark text-white" style="font-weight: bolder;" > TOTAL = S/ ' +
						this.frmDatosCobranza.total_cobro +
						"</label>",

					confirmButtonText: "Si",
					showCancelButton: true,
					cancelButtonText: "No",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
							Swal.fire({
								icon: "error",
								title: "¡Ups!",
								text: "Primero debe aperturar CAJA",
								confirmButtonText: "Ok",
								allowOutsideClick: true,
							});
							return this.$inertia.get(route("cre.index"));
						}

						let data = new FormData();
						data.append("agencia_id", this.agencia_id);
						data.append("credito_id", this.credito_id);
						data.append(
							"caja_id",
							this.$inertia.page.props.creditos_datos.datos_caja.id
						);
						data.append("asesor_id", this.datos_credito.asesor_id);
						data.append(
							"datos_cobranza",
							JSON.stringify(this.frmDatosCobranza)
						);

						// this.$inertia.post(route("caj.cobranza.pagar"), data);
						// return false;

						this.$inertia.post(route("caj.cobranza.pagar"), data, {
							preserveScroll: true,
							onStart: () => {
								Swal.fire({
									title: "REGISTRANDO PAGO",
									text: "Espere porfavor...",
									showConfirmButton: false,
									allowOutsideClick: false,
									willOpen: () => {
										Swal.showLoading();
									},
								});
							},
							onSuccess: () => {
								this.modo = "imprimir";
								this.submited = false;
								Swal.fire({
									icon: "success",
									title: "¡ÉXITO!",
									timer: 1200,
									showConfirmButton: false,
								});

								return this.ImprimirVoucher("cobranza");
							},
						});
					}
				});
			}
		},
		async CancelarCredito() {
			this.submited = true;

			let dscto_moras = parseFloat(this.frmDatosCancelacion.dscto_moras);
			let dscto_notificaciones = parseFloat(
				this.frmDatosCancelacion.dscto_notificaciones
			);
			let dscto_interes = parseFloat(this.frmDatosCancelacion.dscto_interes);

			let total_descuento = dscto_moras + dscto_notificaciones + dscto_interes;

			let verificacion = await this.VerificarEnCarrito();

			if (verificacion.esta_en_carrito) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					html:
						"No se puede cancelar este crédito porque tiene una <span class='bolder'>COBRANZA</span> en el <span class='bolder'>CARRITO</span> de: " +
						'<label class="p-2 bg-dark text-white bolder">' +
						verificacion.usuario_carrito +
						"</label>",
					allowOutsideClick: true,
				});

				return false;
			}

			if (total_descuento > 0 && this.frmDatosCancelacion.documento == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe adjuntar un documento para justificar el descuento.",
					allowOutsideClick: true,
				});
				return false;
			}

			if (this.modo_cobranza == "CUENTA_BANCARIA") {
				if (this.banco_seleccionado == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Por favor, seleccione el BANCO donde se realizó el pago.",
						allowOutsideClick: true,
					});
					$('#myTab a[href="#cuotas"]').tab("show");
					return false;
				} else {
					this.frmDatosCancelacion.pago_banco = true;
					this.frmDatosCancelacion.banco_id = this.banco_seleccionado;
				}
			} else {
				this.frmDatosCancelacion.pago_banco = false;
				this.frmDatosCancelacion.banco_id = null;
			}

			Swal.fire({
				title: "¿Desea REGISTRAR el pago?",
				html:
					'<label class="p-2 bg-dark text-white" style="font-weight: bolder;" > TOTAL = S/ ' +
					this.frmDatosCancelacion.total_cancelar +
					"</label>",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Primero debe aperturar CAJA",
							confirmButtonText: "Ok",
							allowOutsideClick: true,
						});
						return this.$inertia.get(route("cre.index"));
					}

					let data = new FormData();
					data.append("agencia_id", this.agencia_id);
					data.append("credito_id", this.credito_id);
					data.append(
						"caja_id",
						this.$inertia.page.props.creditos_datos.datos_caja.id
					);
					data.append("asesor_id", this.datos_credito.asesor_id);
					data.append("documento", this.frmDatosCancelacion.documento);

					this.frmDatosCancelacion.usuario_cobrador =
						this.frmDatosCobranza.usuario_cobrador;
					this.frmDatosCancelacion.numero_recibo =
						this.frmDatosCobranza.numero_recibo;
					data.append(
						"datos_cancelacion",
						JSON.stringify(this.frmDatosCancelacion)
					);

					data.append("prendario", this.datos_credito.prendario);
					if (this.datos_credito.prendario) {
						data.append("prendas", this.datos_credito.prendas);
					}

					// this.$inertia.post(route("caj.cobranza.cancelar"), data);
					// return false;

					this.$inertia.post(route("caj.cobranza.cancelar"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "CANCELANDO CRÉDITO",
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
							this.modo = "imprimir_cancelado";
							this.PosicionarTablas();
							Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								timer: 1200,
								showConfirmButton: false,
							});

							return this.ImprimirVoucher("cancelacion");
						},
					});
				}
			});
		},

		async VerificarEnCarrito() {
			try {
				const params = {
					agencia_id: this.agencia_id,
					credito_id: this.credito_id,
				};

				const response = await axios.get(
					route("caj.cobranza.verificar_carrito"),
					{ params }
				);
				const usuario_carrito = response.data.usuario_carrito;
				const esta_en_carrito = usuario_carrito !== null;

				return {
					esta_en_carrito,
					usuario_carrito,
				};
			} catch (error) {
				console.error("Error al verificar en carrito:", error);
				return {
					esta_en_carrito: false,
					usuario_carrito: null,
				};
			}
		},
		NuevoCobro() {
			Swal.fire({
				icon: "question",
				title: "¿Desea REALIZAR un nuevo pago?",
				confirmButtonText: "Si",
				showCancelButton: true,
				cancelButtonText: "No",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.modo = "cobrar";
				} else {
					return false;
				}
			});
		},

		async ImprimirVoucher(tipo) {
			let frmDatosVoucher = {};

			if (tipo == "cobranza") {
				frmDatosVoucher.titulo = "CONSTANCIA DE AMORTIZACIÓN";
			} else if (tipo == "cancelacion") {
				frmDatosVoucher.titulo = "CONSTANCIA DE CANCELACIÓN";
			}

			frmDatosVoucher.agencia_id = this.agencia_id;
			frmDatosVoucher.cliente = this.nombre_completo_titular;

			let fecha_hora_actual = await this.$refs.layout.fecha_hora_actual(
				this.agencia_id
			);

			frmDatosVoucher.fecha_pago = fecha_hora_actual;
			frmDatosVoucher.usuario_asesor = this.datos_credito.usuario_asesor;
			frmDatosVoucher.agencia_caja =
				this.$page.props.user_session.nombre_agencia;
			frmDatosVoucher.usuario_caja = this.$page.props.user_session.usuario;
			frmDatosVoucher.nombre_dispositivo =
				this.$page.props.user_session.dispositivo.nombre;

			let detalle_cuota = " Próx:";
			if (tipo == "cobranza") {
				detalle_cuota += String(this.datos_credito.cuota_actual);
				detalle_cuota += " Pend:";
				detalle_cuota += String(
					parseInt(this.datos_cuotas.length) -
						parseInt(this.datos_credito.cuota_actual) +
						1
				);

				frmDatosVoucher.monto_cuota = this.roundTo(this.datos_credito.cuota, 2);
			} else if (tipo == "cancelacion") {
				detalle_cuota += "-";
				detalle_cuota += " Pend:";
				detalle_cuota += 0;

				frmDatosVoucher.monto_cuota = "-";
			}

			frmDatosVoucher.cuota_actual = detalle_cuota;

			frmDatosVoucher.conceptos = this.datos_voucher;

			let total = 0;
			frmDatosVoucher.conceptos.forEach((element) => {
				total += element.importe;
			});

			frmDatosVoucher.importe = total;

			let data = new FormData();

			data.append("agencia_id", this.agencia_id);
			data.append("datos_voucher", JSON.stringify(frmDatosVoucher));

			// this.$inertia.post(route("caj.cobranza.voucher"), data);

			await axios
				.post(route("caj.cobranza.voucher"), data)
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
.slot-cobranza {
	width: 60% !important;
	margin-left: 20% !important;
}

.titulo_pagar {
	background-color: var(--plomoOscuroEmpresarial);
	color: white;
	width: 86%;
	/* margin-left: 8%; */
	margin-bottom: -3px;
	text-align: center;
	border-radius: 5px;
	font-size: 12px !important;
	font-weight: bold;
	line-height: 2.5;
	/* text-transform: uppercase; */
}

.texto_pagar {
	font-size: 15px !important;
	font-weight: bold;
	margin-top: 5px;
	margin-bottom: 0;
	text-align: center;
}

.resaltado {
	background: var(--azulClaroEmpresarial) !important;
	font-weight: bolder;
	color: white !important;
}

.input-information {
	height: 2em !important;
	color: black;
}

.span-highlight {
	font-weight: bolder;
	color: white;
	background-color: var(--verdeOscuroEmpresarial);
}
.input-highlight {
	font-weight: bolder;
	color: var(--colorAlto);
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

@media (max-width: 900px) {
	.slot-cobranza {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
	.recibo_style {
		margin-top: 0px !important;
	}
	.tabs_style {
		margin-top: 10px !important;
	}
	.cuota_style {
		width: 90px !important;
	}
}
</style>
