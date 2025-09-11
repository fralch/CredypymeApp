<template>
	<div id="mdlDetalleCredito" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-60 mdlDetalleCredito">
			<div class="content contentBusquedaClientes" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'DETALLE DE CRÉDITO'"
						:nombre_modal="'mdlDetalleCredito'"
					>
					</headerCloseModal>

					<div class="card-title">INFORMACIÓN</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-3">
								<div class="p-1 text-center">
									<button
										type="button"
										class="btn btn-outline-success font-14 bolder w-100"
									>
										Cuotas por pagar
										<span class="badge badge-success font-13">{{
											datos_credito.cuotas_pendientes
										}}</span>
									</button>

									<button
										type="button"
										class="btn btn-outline-success mt-1 font-14 bolder w-100"
									>
										Saldo por pagar
										<span class="badge badge-success font-13">
											S/ {{ roundTo(saldo_por_pagar, 2) }}</span
										>
									</button>
									<button
										type="button"
										class="btn btn-outline-danger mt-1 font-14 bolder w-100"
									>
										Mora por pagar
										<span class="badge badge-danger font-13">
											S/
											{{
												roundTo(
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
										Notif. por pagar
										<span class="badge badge-danger font-13">
											S/
											{{
												roundTo(
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
											S/ {{ roundTo(datos_credito.saldo_total, 2) }}
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
											S/ {{ roundTo(datos_credito.monto_vencido, 2) }}
										</span>
									</button>
									<br />
									<hr />
									<div v-if="!cancelar_credito">
										<p
											class="text center"
											style="
												font-size: 20px;
												color: #48494b;

												margin: 0;
											"
										>
											Total a pagar
										</p>
										<p
											class="text center"
											style="
												font-size: 22px;
												color: var(--colorAlto);
												font-weight: bolder;
											"
										>
											S/ {{ frmDatosCobranza.total_cobro }}
										</p>
									</div>
								</div>
							</div>

							<div class="col-md-9">
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
									<li class="nav-item">
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
									<li class="nav-item">
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
											id="compromisos-tab"
											data-toggle="tab"
											href="#compromisos"
											role="tab"
											aria-controls="compromisos"
											aria-selected="false"
											>COMPROMISOS</a
										>
									</li>
									<li class="nav-item">
										<a
											class="nav-link tab-title"
											id="titular-tab"
											data-toggle="tab"
											href="#titular"
											role="tab"
											aria-controls="titular"
											aria-selected="false"
											>TITULAR</a
										>
									</li>
									<li class="nav-item">
										<a
											class="nav-link tab-title"
											id="pariente-tab"
											data-toggle="tab"
											href="#pariente"
											role="tab"
											aria-controls="pariente"
											aria-selected="false"
											>PARIENTE</a
										>
									</li>
									<li class="nav-item">
										<a
											class="nav-link tab-title"
											id="aval-tab"
											data-toggle="tab"
											href="#aval"
											role="tab"
											aria-controls="aval"
											aria-selected="false"
											>AVAL</a
										>
									</li>
									<li class="nav-item">
										<a
											class="nav-link tab-title"
											id="negocio-tab"
											data-toggle="tab"
											href="#negocio"
											role="tab"
											aria-controls="negocio"
											aria-selected="false"
											>NEGOCIO</a
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
														'S/ ' + roundTo(datos_credito.capital_total, 2)
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
											<div class="input-group col-md-3 mb-1 mt-1">
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
											</div>
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

											<div class="input-group col-md-7 mb-1 mt-1">
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

											<div class="form-check col-md-5 text-right mt-2">
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
												id="tblCuotasDetalle"
												style="width: 100% !important"
											>
												<thead>
													<tr>
														<th>N°</th>
														<th>FECHA_VENC</th>
														<th>FECHA_PAGO</th>
														<th style="min-width: 70px !important">CUOTA</th>
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
														<th
															v-show="vista_avanzada"
															style="background: var(--plomoClaroEmpresarial)"
														>
															REDONDEO
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
														<th
															v-show="vista_avanzada"
															style="background: var(--plomoClaroEmpresarial)"
														>
															REDONDEO_PAGADO
														</th>
														<th>ACUMULADO</th>
														<th style="min-width: 80px !important">RESTA</th>
														<th style="max-width: 80px !important">ATRASO</th>
													</tr>
												</thead>
												<tbody>
													<tr
														v-for="(item, index) in detalle_cuotas"
														:key="index"
														class="text-dark"
														:class="[
															item.numero_cuota == datos_credito.cuota_actual
																? 'resaltado selected'
																: index % 2 == 0
																? 'verde-claro'
																: '',
														]"
														:id="'dc_1_' + index"
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
															S/ {{ item.interes }}
														</td>
														<td align="right" v-show="vista_avanzada">
															S/ {{ item.redondeo }}
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
																	: "S/ " + item.interes_pagado
															}}
														</td>
														<td align="right" v-show="vista_avanzada">
															{{
																item.redondeo_pagado == 0 &&
																item.estado != "CAN"
																	? ""
																	: "S/ " + item.redondeo_pagado
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

										<fieldset
											class="p-0 pt-2 pl-3 pr-2"
											style="background-color: #d8f1fd"
										>
											<legend>
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														id="chbPagoCuotas"
														:checked="frmDatosCobranza.pago_cuota"
														v-model="frmDatosCobranza.pago_cuota"
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

											<div class="form-row" v-if="frmDatosCobranza.pago_cuota">
												<div class="form-group col-md-7">
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
															/>
															<div
																class="input-group-append"
																v-if="modo_pago == 'por_cuota'"
															>
																<span
																	class="input-group-text text-right"
																	style="
																		width: 150px;
																		font-weight: bolder;
																		font-size: 17px;
																		color: var(--colorAlto);
																	"
																	>S/
																	{{ frmDatosCobranza.pago_cuota_monto }}</span
																>
															</div>
														</div>

														<div class="input-group mb-3">
															<div class="input-group-prepend">
																<div class="input-group-text prepend-title">
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
															/>
														</div>
													</div>
												</div>
												<!-- <div class="form-group col-md-5">
													<label class="label-title">COMENTARIO</label>
													<textarea
														type="text"
														class="form-control text-row mayus"
														rows="3"
														v-model="frmDatosCobranza.comentario"
													></textarea>
												</div> -->
											</div>
										</fieldset>

										<div class="form-row col-md-7 ml-4 mt-2">
											<div class="form-check col-md-4">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbPagoMora"
													:checked="frmDatosCobranza.pago_mora"
													v-model.number="frmDatosCobranza.pago_mora"
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
												class="input-group col-md-6"
												v-if="frmDatosCobranza.pago_mora"
											>
												<div class="input-group-prepend">
													<span class="input-group-text" style="font-size: 15px"
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
												/>
											</div>
										</div>
									</div>
									<div
										class="tab-pane fade"
										id="cancelacion"
										role="tabpanel"
										aria-labelledby="cancelacion-tab"
									>
										<fieldset
											class="pl-4"
											style="background-color: #d8f1fd"
											:disabled="!frmDatosCancelacion.cancelar_credito"
										>
											<legend>
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														id="chbCancelacion"
														v-model="frmDatosCancelacion.cancelar_credito"
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
													<div class="input-group col-sm-12 mb-1 mt-1">
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
																max-width: 150px;
																font-size: 17px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														/>
													</div>

													<div class="input-group col-sm-12 mb-1 mt-1">
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
																max-width: 150px;
																font-size: 17px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														/>
													</div>
													<div class="input-group col-sm-12 mb-1 mt-1">
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
															step="0.01"
															lang="en"
															:max="interes_pendiente"
															name="dscto_interes"
															@change="Redondear"
															v-model.number="frmDatosCancelacion.dscto_interes"
															style="
																max-width: 150px;
																font-size: 17px;
																font-weight: bolder;
																color: var(--colorAlto);
															"
														/>
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
															{{ "TOTAL PARA CANCELAR" }}
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
													</div>
												</div>
											</div>
										</fieldset>

										<fieldset>
											<legend>
												<label class="label-title">CRONOGRAMA DE PAGOS</label>
											</legend>
											<div id="tabla-cronograma">
												<table
													class="table"
													id="tblCronogramaDetalle"
													style="width: 100% !important"
												>
													<thead>
														<tr>
															<th style="min-width: 50px !important">FECHA</th>
															<th style="min-width: 20px !important">N°</th>
															<th style="min-width: 70px !important">CUOTA</th>
															<th style="max-width: 50px !important">
																CAPITAL
															</th>
															<th style="max-width: 50px !important">
																INTERÉS
															</th>
															<th style="max-width: 70px !important">
																REDONDEO
															</th>
															<th style="max-width: 70px !important">
																SALDO_CAP
															</th>
															<th style="max-width: 70px !important">
																SALDO_INT
															</th>
															<th style="max-width: 70px !important">
																SALDO_RED
															</th>
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
															:id="'dc_2_' + index"
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
																S/ {{ roundTo(item.interes, 2) }}
															</td>
															<td align="right">
																S/ {{ roundTo(item.redondeo, 2) }}
															</td>
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
																S/ {{ roundTo(item.saldo_interes, 2) }}
															</td>
															<td
																align="right"
																style="
																	font-size: 11px;
																	font-weight: bolder;
																	color: var(--colorAlto);
																"
															>
																S/ {{ roundTo(item.saldo_redondeo, 2) }}
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</fieldset>
									</div>

									<div
										class="tab-pane fade"
										id="notificaciones"
										role="tabpanel"
										aria-labelledby="notificaciones-tab"
									>
										<div class="text-center mt-2 mb-2">
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													title="Nueva NOTIFICACIÓN"
													@click="NuevaNotificacion"
												>
													<span class="icon text-white">
														<i class="fas fa-plus"></i>
													</span>
													<span class="text">NUEVO</span>
												</button>
												<button
													class="btn btn-cancel btn-icon-split"
													title="Generar NOTIFICACIÓN"
													@click="GenerarNotificacion"
												>
													<span class="icon text-white">
														<i class="fas fa-file-word"></i>
													</span>
													<span class="text">GENERAR</span>
												</button>
											</div>
										</div>
										<div class="form-row">
											<div class="col-md-3">
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														value="solo_pendientes"
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
															roundTo(datos_credito.notificaciones_acumulado, 2)
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
														{{ roundTo(notificaciones_pendiente, 2) }}</span
													></label
												>
											</div>
										</div>
										<table class="table" id="tblNotificacionesDetalle">
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
													<th>USUARIO_ENVÝO</th>
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
										<div class="text-right mt-2">
											<button
												class="btn btn-action btn-icon-split"
												title="Detallar ENVÝO"
												@click="DetallarEnvio"
											>
												<span class="icon text-white">
													<i class="fas fa-file-import"></i>
												</span>
												<span class="text">INDICAR DETALLE DE ENVÝO</span>
											</button>
										</div>
									</div>

									<div
										class="tab-pane fade"
										id="compromisos"
										role="tabpanel"
										aria-labelledby="compromisos-tab"
									>
										<div class="text-center mt-2 mb-1">
											<button
												class="btn btn-action btn-icon-split"
												title="Nuevo COMPROMISO"
												@click="NuevoCompromiso"
											>
												<span class="icon text-white">
													<i class="fas fa-plus"></i>
												</span>
												<span class="text">NUEVO</span>
											</button>
										</div>

										<table class="table" id="tblCompromisosDetalle">
											<thead>
												<tr>
													<th>N°</th>
													<th style="min-width: 300px">COMENTARIO</th>
													<th style="min-width: 100px">FECHA_REGISTRO</th>
													<th>USUARIO_REG</th>
													<th>FECHA_VENC</th>
													<th style="min-width: 100px">FECHA_VISITA</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in compromisos"
													:key="index"
													:class="[
														item.cargo == 'JEFE DE CRÉDITOS'
															? 'jefe'
															: item.cargo == 'ASESOR DE NEGOCIOS'
															? 'asesor'
															: 'otros',
													]"
												>
													<td
														align="center"
														:class="[
															item.cargo == 'JEFE DE CRÉDITOS'
																? 'jefe'
																: item.cargo == 'ASESOR DE NEGOCIOS'
																? 'asesor'
																: 'otros',
														]"
													>
														{{ index + 1 }}
													</td>
													<td
														:class="[
															item.cargo == 'JEFE DE CRÉDITOS'
																? 'jefe'
																: item.cargo == 'ASESOR DE NEGOCIOS'
																? 'asesor'
																: 'otros',
														]"
													>
														{{ item.compromiso }}
													</td>
													<td
														align="center"
														:class="[
															item.cargo == 'JEFE DE CRÉDITOS'
																? 'jefe'
																: item.cargo == 'ASESOR DE NEGOCIOS'
																? 'asesor'
																: 'otros',
														]"
													>
														{{ JSON.parse(item.datos_creacion).fecha }}
													</td>
													<td
														align="center"
														:class="[
															item.cargo == 'JEFE DE CRÉDITOS'
																? 'jefe'
																: item.cargo == 'ASESOR DE NEGOCIOS'
																? 'asesor'
																: 'otros',
														]"
													>
														{{ item.usuario_registro }}
													</td>
													<td
														align="center"
														:class="[
															item.cargo == 'JEFE DE CRÉDITOS'
																? 'jefe'
																: item.cargo == 'ASESOR DE NEGOCIOS'
																? 'asesor'
																: 'otros',
														]"
													>
														{{ item.fecha_vencimiento }}
													</td>
													<td
														align="center"
														:class="[
															item.cargo == 'JEFE DE CRÉDITOS'
																? 'jefe'
																: item.cargo == 'ASESOR DE NEGOCIOS'
																? 'asesor'
																: 'otros',
														]"
													>
														{{ item.fecha_hora_visita }}
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div
										class="tab-pane fade"
										id="titular"
										role="tabpanel"
										aria-labelledby="titular-tab"
									>
										<h1
											class="bolder m-0"
											style="font-size: 20px; color: var(--colorAlto)"
										>
											{{ nombre_completo_titular }}
										</h1>
										<table class="table" width="100%" id="tblTitular">
											<thead>
												<tr>
													<th></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in datos_titular"
													:key="index"
													class="table-bordered"
													:class="index % 2 == 0 ? 'verde-claro' : ''"
												>
													<td
														width="25%"
														class="bolder"
														style="font-size: 11px"
													>
														{{ item.propiedad }}
													</td>
													<td>{{ item.valor }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div
										class="tab-pane fade"
										id="pariente"
										role="tabpanel"
										aria-labelledby="pariente-tab"
									>
										<h1
											class="bolder m-0"
											style="font-size: 20px; color: var(--colorAlto)"
										>
											{{ nombre_completo_titular }}
										</h1>
										<table class="table" width="100%" id="tblPariente">
											<thead>
												<tr>
													<th></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in datos_pariente"
													:key="index"
													class="table-bordered"
													:class="index % 2 == 0 ? 'verde-claro' : ''"
												>
													<td
														width="25%"
														class="bolder"
														style="font-size: 11px"
													>
														{{ item.propiedad }}
													</td>
													<td>{{ item.valor }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div
										class="tab-pane fade"
										id="aval"
										role="tabpanel"
										aria-labelledby="aval-tab"
									>
										<h1
											class="bolder m-0"
											style="font-size: 20px; color: var(--colorAlto)"
										>
											{{ nombre_completo_titular }}
										</h1>
										<table class="table" width="100%" id="tblAval">
											<thead>
												<tr>
													<th></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in datos_aval"
													:key="index"
													class="table-bordered"
													:class="index % 2 == 0 ? 'verde-claro' : ''"
												>
													<td
														width="25%"
														class="bolder"
														style="font-size: 11px"
													>
														{{ item.propiedad }}
													</td>
													<td>{{ item.valor }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div
										class="tab-pane fade"
										id="negocio"
										role="tabpanel"
										aria-labelledby="negocio-tab"
									>
										<h1
											class="bolder m-0"
											style="font-size: 20px; color: var(--colorAlto)"
										>
											{{ nombre_completo_titular }}
										</h1>
										<table class="table" width="100%" id="tblNegocio">
											<thead>
												<tr>
													<th></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in datos_negocio"
													:key="index"
													class="table-bordered"
													:class="index % 2 == 0 ? 'verde-claro' : ''"
												>
													<td
														width="25%"
														class="bolder"
														style="font-size: 11px"
													>
														{{ item.propiedad }}
													</td>
													<td>{{ item.valor }}</td>
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

		<mdlNuevoCompromiso ref="mdlNuevoCompromiso" :agencia_id="agencia_id">
		</mdlNuevoCompromiso>
		<mdlNuevaNotificacion ref="mdlNuevaNotificacion" :agencia_id="agencia_id">
		</mdlNuevaNotificacion>
		<mdlEnvioNotificacion
			ref="mdlEnvioNotificacion"
			:usuarios_agencia="usuarios_agencia"
			:agencia_id="agencia_id"
		>
		</mdlEnvioNotificacion>

		<rptNotificacion ref="rptNotificacion"> </rptNotificacion>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import mdlNuevoCompromiso from "@/Pages/Creditos/Creditos/Components/mdlNuevoCompromiso.vue";
import mdlNuevaNotificacion from "@/Pages/Creditos/Creditos/Components/mdlNuevaNotificacion.vue";
import mdlEnvioNotificacion from "@/Pages/Creditos/Creditos/Components/mdlEnvioNotificacion.vue";

import rptNotificacion from "@/Pages/Creditos/Creditos/Reports/rptNotificacion.vue";

export default {
	components: {
		headerCloseModal,
		mdlNuevoCompromiso,
		mdlNuevaNotificacion,
		mdlEnvioNotificacion,
		rptNotificacion,
	},
	props: { usuarios_agencia: Array, agencia_id: Number },
	data() {
		return {
			credito_id: 0,

			datos_credito: {},
			datos_cuotas: [],
			notificaciones: [],
			notificaciones_tipos: [],
			compromisos: [],
			datos_titular: [],
			pariente: {},
			datos_pariente: [],
			aval: {},
			datos_aval: [],
			datos_negocio: [],

			vista_avanzada: false,
			detalle_cronograma: [],

			solo_notificaciones_pendientes: true,

			frmDatosCobranza: {
				pago_cuota_cantidad: 0,
				pago_cuota_monto: this.roundTo(0, 2),
				pago_monto: this.roundTo(0, 2),
				pago_cuota: true,
				pago_mora: false,
				pago_mora_monto: this.roundTo(0, 2),
				forma_pago: "por_cuota",
				comentario: null,
				total_cobro: this.roundTo(0, 2),
			},

			frmDatosCancelacion: {
				cancelar_credito: false,
				interes_moratorio_adicional: this.roundTo(0, 2),
				dscto_notificaciones: this.roundTo(0, 2),
				dscto_moras: this.roundTo(0, 2),
				dscto_interes: this.roundTo(0, 2),
				total_cancelar: this.roundTo(0, 2),
			},
		};
	},

	computed: {
		detalle_cuotas() {
			let lista = [];
			if (this.datos_credito != {}) {
				let saldo_interes = parseFloat(this.datos_credito.interes_total);
				let saldo_capital = parseFloat(this.datos_credito.capital_total);
				let saldo_redondeo = parseFloat(this.datos_credito.redondeo_total);

				this.datos_cuotas.forEach((element) => {
					saldo_capital -= parseFloat(element.capital);
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
					saldo_interes -= parseFloat(element.interes);

					if (saldo_interes < 0) {
						saldo_interes = 0;
					}

					lista.push(object);
				});
			}
			return lista;
		},
		nombre_completo_titular() {
			if (this.datos_credito == {}) {
				return null;
			} else {
				return (
					this.datos_credito.apellido_paterno +
					" " +
					this.datos_credito.apellido_materno +
					" " +
					this.datos_credito.nombres
				);
			}
		},

		nombre_completo_pariente() {
			if (this.datos_pariente.length == 0) {
				return "-";
			} else {
				return (
					this.pariente.apellido_paterno +
					" " +
					this.pariente.apellido_materno +
					" " +
					this.pariente.nombres
				);
			}
		},
		nombre_completo_aval() {
			if (this.datos_aval.length == 0) {
				return "-";
			} else {
				return (
					this.aval.apellido_paterno +
					" " +
					this.aval.apellido_materno +
					" " +
					this.aval.nombres
				);
			}
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
		capital_pendiente() {
			if (this.datos_credito == {}) {
				return 0;
			} else {
				let capital_total = parseFloat(this.datos_credito.capital_total);
				let capital_pagado = parseFloat(this.datos_credito.capital_pagado);

				let capital_pendiente = capital_total - capital_pagado;

				if (capital_pendiente < 0) {
					capital_pendiente = 0;
				}

				return capital_pendiente;
			}
		},
		interes_pendiente() {
			if (this.datos_credito == {}) {
				return 0;
			} else {
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
			}
		},
		redondeo_pendiente() {
			if (this.datos_credito == {}) {
				return 0;
			} else {
				let redondeo_total = parseFloat(this.datos_credito.redondeo_total);
				let redondeo_pagado = parseFloat(this.datos_credito.redondeo_pagado);

				let redondeo_pendiente = redondeo_total - redondeo_pagado;

				if (redondeo_pendiente < 0) {
					redondeo_pendiente = 0;
				}

				return redondeo_pendiente;
			}
		},
		mora_pendiente() {
			if (this.datos_credito == {}) {
				return 0;
			} else {
				let mora_total = parseFloat(this.datos_credito.mora_total);
				let mora_pagado = parseFloat(this.datos_credito.mora_pagado);

				let mora_pendiente = mora_total - mora_pagado;

				if (mora_pendiente < 0) {
					mora_pendiente = 0;
				}

				return parseFloat(mora_pendiente.toFixed(2));
			}
		},
		notificaciones_pendiente() {
			if (this.datos_credito == {}) {
				return 0;
			} else {
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
			}
		},
		saldo_por_pagar() {
			if (this.datos_credito == {}) {
				return null;
			} else {
				let saldo_por_pagar =
					parseFloat(this.capital_pendiente) +
					parseFloat(this.interes_pendiente) +
					parseFloat(this.redondeo_pendiente);
				return parseFloat(saldo_por_pagar);
			}
		},
		cancelar_credito() {
			return this.frmDatosCancelacion.cancelar_credito;
		},
	},
	watch: {
		detalle_cuotas() {
			let self = this;

			$("#tblCuotasDetalle").DataTable().destroy();
			$("#tblCronogramaDetalle").DataTable().destroy();
			this.TablaCuotas();
			this.TablaCronograma();

			// $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
			// 	$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
			// 	self.PosicionarTablas();
			// });
		},
		datos_titular() {
			$("#tblTitular").DataTable().destroy();
			this.TablaTitular();
		},
		datos_pariente() {
			$("#tblPariente").DataTable().destroy();
			this.TablaPariente();
		},
		datos_aval() {
			$("#tblAval").DataTable().destroy();
			this.TablaAval();
		},
		compromisos() {
			$("#tblCompromisosDetalle").DataTable().destroy();
			this.TablaCompromisos();
		},
		notificaciones() {
			$("#tblNotificacionesDetalle").DataTable().destroy();

			this.TablaNotificaciones();
		},

		vista_avanzada() {
			$("#tblCuotasDetalle").DataTable().destroy();
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
		cancelar_credito(value) {
			this.frmDatosCancelacion.dscto_moras = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_notificaciones = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_interes = this.roundTo(
				this.interes_pendiente,
				2
			);
			if (value) {
				this.ActualizarTotalCancelacion();
			} else {
				this.frmDatosCancelacion.total_cancelar = this.roundTo(0, 2);
			}
		},
	},

	mounted() {
		this.TablaCuotas();
		this.TablaCronograma();
		this.TablaNotificaciones();
		this.TablaCompromisos();
		this.TablaTitular();
		this.TablaPariente();
		this.TablaAval();
	},

	methods: {
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
			let posicion = cuota_actual - 5;

			if (posicion < 0) {
				posicion = 0;
			}

			let selection_1 = $("#tblCuotasDetalle #dc_1_" + posicion);
			let selection_2 = $("#tblCronogramaDetalle #dc_2_" + posicion);

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
		Resetear() {
			this.frmDatosCobranza.pago_cuota_cantidad = 0;
			this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
			this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);
			this.frmDatosCobranza.pago_cuota = false;
			this.frmDatosCobranza.pago_mora = false;
			this.frmDatosCobranza.pago_mora_monto = this.roundTo(0, 2);
			this.frmDatosCobranza.forma_pago = "por_cuota";
			this.frmDatosCobranza.comentario = null;
			this.frmDatosCobranza.total_cobro = this.roundTo(0, 2);

			this.frmDatosCancelacion.cancelar_credito = false;
			this.frmDatosCancelacion.interes_moratorio_adicional = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_notificaciones = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_moras = this.roundTo(0, 2);
			this.frmDatosCancelacion.dscto_interes = this.roundTo(0, 2);
			this.frmDatosCancelacion.total_cancelar = this.roundTo(0, 2);
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

				let monto = parseFloat(this.roundTo(valor * cuota, 2));

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
			}
		},

		ActualizarTotalCobro() {
			this.frmDatosCobranza.total_cobro = this.roundTo(
				parseFloat(this.frmDatosCobranza.pago_cuota_monto) +
					parseFloat(this.frmDatosCobranza.pago_monto) +
					parseFloat(this.frmDatosCobranza.pago_mora_monto),
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
				var table = $("#tblCuotasDetalle").DataTable({
					scrollY: "200px",
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
				self.PosicionarTablas();
			});
		},
		TablaCronograma() {
			this.$nextTick(() => {
				var table = $("#tblCronogramaDetalle").DataTable({
					scrollY: "350px",
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
		TablaNotificaciones() {
			this.$nextTick(() => {
				var table = $("#tblNotificacionesDetalle").DataTable({
					scrollY: "400px",
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

				$("#chbNotificacionesPendientes").click(function () {
					if ($(this).is(":checked")) {
						table.column(4).search("PEN").draw();
					} else {
						table.column(4).search("").draw();
					}
				});
			});
		},
		TablaCompromisos() {
			this.$nextTick(() => {
				var table = $("#tblCompromisosDetalle").DataTable({
					scrollY: "500px",
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

		TablaTitular() {
			this.$nextTick(() => {
				var table = $("#tblTitular").DataTable({
					scrollY: "500px",
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
		TablaPariente() {
			this.$nextTick(() => {
				var table = $("#tblPariente").DataTable({
					scrollY: "500px",
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
		TablaAval() {
			this.$nextTick(() => {
				var table = $("#tblAval").DataTable({
					scrollY: "500px",
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
		TablaNegocio() {
			this.$nextTick(() => {
				var table = $("#tblNegocio").DataTable({
					scrollY: "500px",
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

		DetallePagos() {
			let pago_cuotas = [];
			let pago_moras = [];
			let pago_notificaciones = [];

			let mdlDetallePagos = this.$parent.$refs.mdlDetallePagos;

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
		NuevoCompromiso() {
			let mdlNuevoCompromiso = this.$refs.mdlNuevoCompromiso;

			mdlNuevoCompromiso.submited = false;
			mdlNuevoCompromiso.frmDatosCompromiso.credito_id = this.credito_id;
			mdlNuevoCompromiso.frmDatosCompromiso.compromiso = null;
			mdlNuevoCompromiso.frmDatosCompromiso.fecha_visita = null;
			mdlNuevoCompromiso.frmDatosCompromiso.hora_visita = null;
			mdlNuevoCompromiso.frmDatosCompromiso.fecha_vencimiento = null;

			$("#mdlNuevoCompromiso").css("display", "block");
		},

		NuevaNotificacion() {
			let mdlNuevaNotificacion = this.$refs.mdlNuevaNotificacion;

			mdlNuevaNotificacion.submited = false;
			mdlNuevaNotificacion.datos_aval = this.datos_aval;
			mdlNuevaNotificacion.notificaciones_tipos = this.notificaciones_tipos;
			mdlNuevaNotificacion.frmDatosNotificacion.credito_id = this.credito_id;
			mdlNuevaNotificacion.frmDatosNotificacion.numero_cuota =
				this.datos_credito.cuota_actual;
			mdlNuevaNotificacion.frmDatosNotificacion.tipo_id = 0;
			mdlNuevaNotificacion.frmDatosNotificacion.monto = 0;

			mdlNuevaNotificacion.monto_minimo = 0;

			$("#mdlNuevaNotificacion").css("display", "block");
		},

		DetallarEnvio() {
			let row = document
				.getElementById("tblNotificacionesDetalle")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione una notificación",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let id = row.id.replace("not_", "");

				let notificacion = this.notificaciones.filter(
					(item) => item.id == id
				)[0];

				if (notificacion.usuario_envio == null) {
					let mdlEnvioNotificacion = this.$refs.mdlEnvioNotificacion;
					mdlEnvioNotificacion.submited = false;
					mdlEnvioNotificacion.frmEnvioNotificacion.credito_id =
						this.credito_id;
					mdlEnvioNotificacion.frmEnvioNotificacion.notificacion_id = id;
					mdlEnvioNotificacion.frmEnvioNotificacion.usuario_envio = 0;
					mdlEnvioNotificacion.frmEnvioNotificacion.descripcion_envio = null;
					$("#mdlEnvioNotificacion").css("display", "block");
				} else {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Esta notificación ya tiene datos de envío",
						allowOutsideClick: true,
					});
					return false;
				}
			}
		},

		GenerarNotificacion() {
			let row = document
				.getElementById("tblNotificacionesDetalle")
				.getElementsByClassName("selected")[0];

			if (row == undefined) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Seleccione una notificación",
					allowOutsideClick: true,
				});
				return false;
			} else {
				let self = this;
				let id = row.id.replace("not_", "");

				let notificacion = this.notificaciones.filter(
					(item) => item.id == id
				)[0];

				let parts = this.$page.props.application.data_local
					.filter((item) => item.descripcion == "FECHA_CREDITOS")[0]
					.valor_fecha.split("-");

				let options = {
					year: "numeric",
					month: "long",
					day: "numeric",
				};

				let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

				let agencia_actual = this.$page.props.application.agencias.filter(
					(item) => item.id == self.agencia_id
				)[0];

				let fecha_actual =
					agencia_actual.distrito +
					", " +
					date.toLocaleDateString("es-ES", options);

				let rptNotificacion = this.$refs.rptNotificacion;

				async function EnviarDatos() {
					let data = {
						notificacion_id: id.padStart(6, 0),
						año_actual: parts[0],
						fecha_actual: fecha_actual,
						titular: self.nombre_completo_titular,
						pariente:
							self.nombre_completo_pariente != null
								? self.nombre_completo_pariente
								: "-",
						aval:
							self.nombre_completo_aval != null
								? self.nombre_completo_aval
								: "-",
						direccion: self.datos_credito.direccion,
						agencia: "AGENCIA " + agencia_actual.agencia,
						codigo: self.datos_credito.codigo_seguimiento,
						dias_atraso: self.datos_credito.dias_atraso,
						monto_vencido: self.roundTo(self.datos_credito.monto_vencido, 2),
						capital_total: self.roundTo(self.datos_credito.capital_total, 2),
						cuota: self.roundTo(self.datos_credito.cuota, 2),
						cuotas_pagadas:
							self.datos_credito.plazo - self.datos_credito.cuotas_pendientes,
						cuotas_vencidas: self.datos_credito.cuotas_vencidas,
						saldo_total: self.roundTo(self.datos_credito.saldo_total, 2),
						cuotas_pendientes: self.datos_credito.cuotas_pendientes,
						fecha_ultimo_pago:
							self.datos_credito.fecha_ultimo_pago != null
								? self.datos_credito.fecha_ultimo_pago.substring(0, 10)
								: "-",
						fecha_vencimiento: self.datos_credito.fecha_vencimiento,
						direccion_agencia: agencia_actual.direccion,
						monto_notificacion: self.roundTo(notificacion.monto, 2),
					};

					rptNotificacion.data = data;
					rptNotificacion.document_title =
						notificacion.nombre_archivo + "_" + id;
					rptNotificacion.document_name = notificacion.nombre_archivo;
				}

				EnviarDatos().then(() => {
					rptNotificacion.GenerarWord();
				});
			}
		},
	},
};
</script>

<style lang="css">
.titulo_pagar {
	background-color: var(--plomoOscuroEmpresarial);
	color: white;
	width: 84%;
	margin-left: 8%;
	margin-bottom: -3px;
	text-align: center;
	border-radius: 5px;

	font-weight: bolder;
}
.texto_pagar {
	font-size: 1.2rem;
	font-weight: bold;
	margin-top: 5px;
	margin-bottom: 0;
	text-align: center;
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

.resaltado {
	background: var(--azulClaroEmpresarial) !important;
	font-weight: bolder;
	color: white !important;
}

.input-information {
	height: 2em !important;
	color: black;
	/* font-size: 12px; */
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

.jefe {
	background-color: var(--red) !important;
	color: white !important;
}

.asesor {
	background: var(--green) !important;
}

.otros {
	background-color: var(--yellow) !important;
}
</style>
