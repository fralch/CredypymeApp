<template>
    <div id="mdlDetalleCreditoCarrito" class="modal">
        <!-- Modal content -->
        <div class="modal-content mdlDetalleCreditoCarrito">
            <div class="content" style="display: block">
                <div class="card">
                    <headerCloseModal
                        :titulo_modal="'DETALLE DE CRÉDITO'"
                        :nombre_modal="'mdlDetalleCreditoCarrito'"
                    >
                    </headerCloseModal>

                    <div class="card-title">INFORMACIÓN DE CUOTAS</div>
                    <div class="card-body card-block">
                        <div class="form-row">
                            <div class="input-group col-md-7 mb-1 mt-1">
                                <div class="input-group-prepend">
                                    <span
                                        class="input-group-text prepend-title span-highlight"
                                        >TITULAR</span
                                    >
                                </div>
                                <input
                                    type="text"
                                    class="form-control input-information input-highlight"
                                    :value="datos_credito.cliente"
                                    onkeydown="return false"
                                    spellcheck="false"
                                />
                            </div>

                            <div class="input-group col-md-2 mb-1 mt-1 col-6">
                                <div class="input-group-prepend">
                                    <span
                                        class="input-group-text prepend-title span-highlight"
                                        >ATRASO</span
                                    >
                                </div>

                                <input
                                    type="text"
                                    class="form-control center input-information input-highlight"
                                    :value="datos_credito.dias_atraso + ' días'"
                                    onkeydown="return false"
                                    spellcheck="false"
                                />
                            </div>

                            <div class="input-group col-md-3 mb-1 mt-1 col-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >CAP.</span
                                    >
                                    <span class="input-group-text prepend-title"
                                        >S/</span
                                    >
                                </div>

                                <input
                                    type="text"
                                    class="form-control center input-information"
                                    :value="
                                        roundTo(datos_credito.capital_total, 2)
                                    "
                                    onkeydown="return false"
                                    spellcheck="false"
                                />
                            </div>
                            <div class="input-group col-md-4 mb-1 mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >FECHA DES.</span
                                    >
                                </div>

                                <input
                                    type="text"
                                    class="form-control center input-information"
                                    :value="
                                        Object.keys(datos_credito).length === 0
                                            ? null
                                            : JSON.parse(
                                                  datos_credito.datos_creacion
                                              ).fecha
                                    "
                                    onkeydown="return false"
                                    spellcheck="false"
                                />
                            </div>
                            <div class="input-group col-md-4 mb-1 mt-1 col-7">
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >PLA.</span
                                    >
                                </div>

                                <input
                                    type="text"
                                    class="form-control center input-information"
                                    :value="
                                        roundTo(datos_credito.plazo, 0) +
                                        ' ' +
                                        periodo_medicion(
                                            datos_credito.periodo_pago
                                        ) +
                                        ' - ' +
                                        roundTo(datos_credito.tasa_interes, 2) +
                                        '%'
                                    "
                                    onkeydown="return false"
                                    spellcheck="false"
                                />
                            </div>
                            <div
                                class="input-group col-md-2 mb-1 mt-1"
                                v-if="windowWidth >= 900"
                            >
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >FRE.</span
                                    >
                                </div>

                                <input
                                    type="text"
                                    class="form-control center input-information"
                                    :value="datos_credito.periodo_pago"
                                    onkeydown="return false"
                                    spellcheck="false"
                                />
                            </div>

                            <div class="input-group col-md-2 mb-1 mt-1 col-5">
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >TIP.</span
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

                            <div
                                class="input-group col-md-5 mb-1 mt-1"
                                v-if="windowWidth >= 900"
                            >
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

                            <div
                                class="input-group col-md-3 mb-1 mt-1"
                                v-if="windowWidth >= 900"
                            >
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
                            <div
                                class="input-group col-md-3 mb-1 mt-1"
                                v-if="windowWidth >= 900"
                            >
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >COBRANZA EN</span
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

                            <div class="input-group col-md-3 col-12 mb-1 mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >TEL.:</span
                                    >
                                </div>

                                <input
                                    type="number"
                                    min="0"
                                    maxlength="9"
                                    lang="en"
                                    style="height: 32px !important"
                                    class="form-control center input-information input-highlight"
                                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    v-model="nuevo_telefono_principal"
                                    :disabled="!editar_numero"
                                />
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-action"
                                        title="EDITAR"
                                        v-if="!editar_numero"
                                        @click="EditarNumero"
                                        :disabled="!edicion"
                                    >
                                        <span class="icon">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    </button>
                                    <button
                                        class="btn btn-cancel"
                                        title="GUARDAR"
                                        v-if="editar_numero"
                                        @click="GuardarNumero"
                                        :disabled="!edicion"
                                    >
                                        <span class="icon">
                                            <i class="fas fa-save"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="input-group col-md-4 col-12 mb-1 mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text prepend-title"
                                        >ENVIAR A:</span
                                    >
                                </div>

                                <select
                                    class="form-control center"
                                    v-model="frmDatosCobranza.modo_envio"
                                    :disabled="!edicion"
                                >
                                    <option value="WHATSAPP">WHATSAPP</option>
                                </select>
                            </div>

                            <div
                                class="col-md-1 col-2 offset-5"
                                v-if="boton_panel"
                            >
                                <button
                                    class="btn btn-action btn-icon-split"
                                    @click="VerMasInformacion"
                                    title="VER MÁS INFORMACIÓN"
                                    v-if="!mostrar_panel"
                                >
                                    <span class="icon text-white">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </button>
                                <button
                                    class="btn btn-action btn-icon-split"
                                    @click="VerMenosInformacion"
                                    title="VER MENOS INFORMACIÓN"
                                    v-if="mostrar_panel"
                                >
                                    <span class="icon text-white">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="form-row mt-1">
                            <div class="col-md-4" v-if="this.mostrar_panel">
                                <div class="p-1 text-center">
                                    <button
                                        type="button"
                                        class="btn btn-outline-success font-14 bolder w-100"
                                    >
                                        Cuotas por pagar
                                        <span
                                            class="badge badge-success font-13"
                                            >{{
                                                datos_credito.cuotas_pendientes
                                            }}</span
                                        >
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-success mt-1 font-14 bolder w-100"
                                    >
                                        Saldo por pagar
                                        <span
                                            class="badge badge-success font-13"
                                        >
                                            S/
                                            {{
                                                roundTo(saldo_por_pagar, 2)
                                            }}</span
                                        >
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger mt-1 font-14 bolder w-100"
                                    >
                                        Mora por pagar
                                        <span
                                            class="badge badge-danger font-13"
                                        >
                                            S/
                                            {{
                                                roundTo(
                                                    datos_credito.mora_total -
                                                        datos_credito.mora_pagado,
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
                                        <span
                                            class="badge badge-danger font-13"
                                        >
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
                                            S/
                                            {{
                                                roundTo(
                                                    datos_credito.saldo_total,
                                                    2
                                                )
                                            }}
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
                                            S/
                                            {{
                                                roundTo(
                                                    datos_credito.monto_vencido,
                                                    2
                                                )
                                            }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div id="tabla-cuotas">
                                    <table class="table" id="tblCuotasDetalle">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>FECHA_VENC</th>
                                                <th>FECHA_PAGO</th>
                                                <th
                                                    style="
                                                        min-width: 50px !important;
                                                    "
                                                >
                                                    CUOTA
                                                </th>
                                                <th>ESTADO</th>
                                                <th>ACUMULADO</th>
                                                <th
                                                    style="
                                                        min-width: 80px !important;
                                                    "
                                                >
                                                    RESTA
                                                </th>
                                                <th
                                                    style="
                                                        max-width: 80px !important;
                                                    "
                                                >
                                                    DÍAS_ATRASO
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in detalle_cuotas"
                                                :key="index"
                                                class="text-dark"
                                                :class="[
                                                    item.numero_cuota ==
                                                    datos_credito.cuota_actual
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
                                                <td align="right">
                                                    S/ {{ item.monto }}
                                                </td>

                                                <td align="center">
                                                    {{ item.estado }}
                                                </td>

                                                <td align="right">
                                                    {{
                                                        item.acumulado != null
                                                            ? "S/ " +
                                                              item.acumulado
                                                            : ""
                                                    }}
                                                </td>
                                                <td align="right">
                                                    {{
                                                        item.resta != null
                                                            ? "S/ " + item.resta
                                                            : ""
                                                    }}
                                                </td>
                                                <td align="center">
                                                    {{
                                                        item.dias_atraso == 0
                                                            ? ""
                                                            : item.dias_atraso
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <hr />
                                <div class="form-check text-center">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="chbCancelacion"
                                        v-model="cancelacion"
                                        :disabled="!edicion"
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
                                                :checked="
                                                    frmDatosCobranza.pago_cuota
                                                "
                                                v-model="
                                                    frmDatosCobranza.pago_cuota
                                                "
                                                :disabled="
                                                    !edicion || this.cancelacion
                                                "
                                            />
                                            <label
                                                class="label-title p-1"
                                                for="chbPagoCuotas"
                                                style="
                                                    color: white !important;
                                                    font-size: 12px;
                                                    border-radius: 3px;
                                                    background: #71bc9c;
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
                                        <div class="input-group col-md-8 mb-3">
                                            <div class="input-group-prepend">
                                                <div
                                                    class="input-group-text prepend-title"
                                                >
                                                    <input
                                                        type="radio"
                                                        name="forma_pago"
                                                        id="rdbPorCuota"
                                                        value="por_cuota"
                                                        v-model="
                                                            frmDatosCobranza.forma_pago
                                                        "
                                                        :disabled="
                                                            !edicion ||
                                                            this.cancelacion
                                                        "
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
                                                style="
                                                    max-width: 70px;
                                                    font-size: 15px;
                                                "
                                                min="0"
                                                :max="
                                                    datos_credito.cuotas_pendientes
                                                "
                                                v-model.number="
                                                    frmDatosCobranza.pago_cuota_cantidad
                                                "
                                                @change="Redondear"
                                                name="por_cuota"
                                                step="1"
                                                lang="en"
                                                v-if="
                                                    this.frmDatosCobranza
                                                        .forma_pago ==
                                                    'por_cuota'
                                                "
                                                :disabled="!edicion"
                                            />
                                            <div
                                                class="input-group-append"
                                                v-if="
                                                    this.frmDatosCobranza
                                                        .forma_pago ==
                                                    'por_cuota'
                                                "
                                            >
                                                <span
                                                    class="input-group-text text-right"
                                                    style="
                                                        width: 150px;
                                                        font-weight: bolder;
                                                        font-size: 17px;
                                                        color: var(--colorAlto);
                                                    "
                                                    :style="
                                                        windowWidth >= 900
                                                            ? 'width: 150px;font-weight: bolder;font-size: 17px;color: var(--colorAlto);'
                                                            : 'width: 130px;font-weight: bolder;font-size: 17px;color: var(--colorAlto);'
                                                    "
                                                    :disabled="!edicion"
                                                    >S/
                                                    {{
                                                        frmDatosCobranza.pago_cuota_monto
                                                    }}</span
                                                >
                                            </div>
                                        </div>

                                        <div class="input-group col-md-8 mb-3">
                                            <div class="input-group-prepend">
                                                <div
                                                    class="input-group-text prepend-title"
                                                >
                                                    <input
                                                        type="radio"
                                                        name="forma_pago"
                                                        id="rdbPorMonto"
                                                        value="por_monto"
                                                        v-model="
                                                            frmDatosCobranza.forma_pago
                                                        "
                                                        :disabled="
                                                            !edicion ||
                                                            this.cancelacion
                                                        "
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
                                                v-if="
                                                    this.frmDatosCobranza
                                                        .forma_pago ==
                                                    'por_monto'
                                                "
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
                                                v-model.number="
                                                    frmDatosCobranza.pago_monto
                                                "
                                                v-if="
                                                    this.frmDatosCobranza
                                                        .forma_pago ==
                                                    'por_monto'
                                                "
                                                :disabled="
                                                    !edicion || this.cancelacion
                                                "
                                            />
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="form-row">
                                    <div class="form-group col-md-8 col-12">
                                        <div
                                            class="form-row col-md-12 ml-4 mt-2"
                                        >
                                            <div
                                                class="form-check col-md-5 col-5"
                                            >
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="chbPagoMora"
                                                    :checked="
                                                        frmDatosCobranza.pago_mora
                                                    "
                                                    v-model="
                                                        frmDatosCobranza.pago_mora
                                                    "
                                                    :disabled="
                                                        !edicion ||
                                                        this.cancelacion
                                                    "
                                                />
                                                <label
                                                    class="label-title p-1"
                                                    for="chbPagoMora"
                                                    style="
                                                        background: #71bc9c;
                                                        color: white !important;
                                                        font-size: 12px;
                                                        border-radius: 3px;
                                                    "
                                                >
                                                    PAGO DE MORA
                                                </label>
                                            </div>
                                            <div
                                                class="input-group col-md-6 col-7"
                                                v-if="
                                                    frmDatosCobranza.pago_mora
                                                "
                                            >
                                                <div
                                                    class="input-group-prepend"
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
                                                    step="0.1"
                                                    lang="en"
                                                    :max="mora_pendiente"
                                                    v-model.number="
                                                        frmDatosCobranza.pago_mora_monto
                                                    "
                                                    name="pago_mora"
                                                    @change="Redondear"
                                                    style="
                                                        max-width: 120px;
                                                        font-size: 17px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    :disabled="
                                                        !edicion ||
                                                        this.cancelacion
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div
                                            class="form-row col-md-12 ml-4 mt-2"
                                            v-if="notificaciones.length != 0"
                                        >
                                            <div
                                                class="form-check col-md-5 col-5"
                                            >
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="chbPagoNotificacion"
                                                    :checked="
                                                        frmDatosCobranza.pago_notificaciones
                                                    "
                                                    v-model="
                                                        frmDatosCobranza.pago_notificaciones
                                                    "
                                                    :disabled="
                                                        !edicion ||
                                                        this.cancelacion
                                                    "
                                                />
                                                <label
                                                    class="label-title p-1"
                                                    for="chbPagoNotificacion"
                                                    style="
                                                        background: var(
                                                            --colorMedio
                                                        );
                                                        color: white !important;
                                                        font-size: 12px;
                                                        border-radius: 3px;
                                                    "
                                                >
                                                    PAGO DE NOTIF.
                                                </label>
                                            </div>
                                            <div
                                                class="input-group col-md-6 col-7"
                                                v-if="
                                                    frmDatosCobranza.pago_notificaciones
                                                "
                                            >
                                                <div
                                                    class="input-group-prepend"
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
                                                    step="0.1"
                                                    lang="en"
                                                    :max="
                                                        datos_credito.notificaciones_pendiente
                                                    "
                                                    v-model.number="
                                                        frmDatosCobranza.pago_notificaciones_monto
                                                    "
                                                    name="pago_notificaciones"
                                                    @change="Redondear"
                                                    style="
                                                        max-width: 120px;
                                                        font-size: 17px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    :disabled="
                                                        !edicion ||
                                                        this.cancelacion
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4 col-12">
                                        <fieldset class="p-0 pt-2 pl-3 pr-2">
                                            <div>
                                                <p
                                                    class="text center"
                                                    style="
                                                        font-size: 15px;
                                                        color: #48494b;
                                                        font-weight: bold;

                                                        margin: 0;
                                                    "
                                                    v-if="edicion"
                                                >
                                                    TOTAL A PAGAR
                                                </p>
                                                <p
                                                    class="text center"
                                                    style="
                                                        font-size: 15px;
                                                        color: #48494b;
                                                        font-weight: bold;

                                                        margin: 0;
                                                    "
                                                    v-if="!edicion"
                                                >
                                                    TOTAL PAGADO
                                                </p>
                                                <p
                                                    class="text center"
                                                    style="
                                                        font-size: 22px;
                                                        color: var(--colorAlto);
                                                        font-weight: bolder;
                                                        margin-bottom: 0rem;
                                                    "
                                                >
                                                    S/
                                                    {{
                                                        frmDatosCobranza.total_cobro
                                                    }}
                                                </p>
                                            </div>
                                            <div
                                                class="text-center"
                                                v-if="!edicion && modo != 'VER'"
                                            >
                                                <button
                                                    class="btn btn-action btn-icon-split"
                                                    style="
                                                        height: 35px !important;
                                                        font-weight: bolder;
                                                        font-size: 25px;
                                                        margin-bottom: 2px;
                                                    "
                                                    @click="EditarPagoCredito"
                                                >
                                                    <span class="text"
                                                        >EDITAR</span
                                                    >
                                                </button>
                                            </div>

                                            <div
                                                class="text-center"
                                                v-if="edicion && modo != 'VER'"
                                            >
                                                <button
                                                    class="btn btn-action btn-icon-split"
                                                    style="
                                                        height: 35px !important;
                                                        font-weight: bolder;
                                                        font-size: 25px;
                                                        margin-bottom: 2px;
                                                    "
                                                    :disabled="
                                                        frmDatosCobranza.total_cobro ==
                                                        0
                                                    "
                                                    @click="PagarCredito"
                                                >
                                                    <span class="text"
                                                        >PAGAR</span
                                                    >
                                                </button>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div
                                    v-show="
                                        this.frmDatosCobranza
                                            .pago_notificaciones == true
                                    "
                                >
                                    <div class="form-row">
                                        <div class="col-md-4 col-7 text-center">
                                            <label class="label-title"
                                                >TOTAL NOTIF.:
                                                <span
                                                    class="bolder"
                                                    style="
                                                        font-size: 1rem;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    S/
                                                    {{
                                                        roundTo(
                                                            datos_credito.notificaciones_total,
                                                            2
                                                        )
                                                    }}</span
                                                ></label
                                            >
                                        </div>
                                        <div class="col-md-4 col-5 text-center">
                                            <label class="label-title"
                                                >ABONADO:
                                                <span
                                                    class="bolder"
                                                    style="
                                                        font-size: 1rem;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    S/
                                                    {{
                                                        roundTo(
                                                            datos_credito.notificaciones_acumulado,
                                                            2
                                                        )
                                                    }}</span
                                                ></label
                                            >
                                        </div>
                                        <div
                                            class="col-md-4 col-12 text-center"
                                        >
                                            <label class="label-title"
                                                >RESTA:
                                                <span
                                                    class="bolder"
                                                    style="
                                                        font-size: 1rem;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    S/
                                                    {{
                                                        roundTo(
                                                            notificaciones_pendiente,
                                                            2
                                                        )
                                                    }}</span
                                                ></label
                                            >
                                        </div>
                                    </div>
                                    <table
                                        class="table"
                                        id="tblNotificacionesDetalle"
                                    >
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th style="min-width: 300px">
                                                    NOTIFICACIÓN
                                                </th>
                                                <th style="min-width: 100px">
                                                    FECHA_REGISTRO
                                                </th>
                                                <th style="min-width: 70px">
                                                    MONTO
                                                </th>
                                                <th>ESTADO</th>
                                                <th style="min-width: 70px">
                                                    ACUMULADO
                                                </th>
                                                <th style="min-width: 70px">
                                                    RESTA
                                                </th>
                                                <th>N°_CUO</th>
                                                <th>APLICADO_POR</th>
                                                <th>USUARIO_ENVÝO</th>
                                                <th style="min-width: 300px">
                                                    COMENTARIO
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in notificaciones"
                                                :key="index"
                                                class="table-bordered"
                                                :id="'not_' + item.id"
                                            >
                                                <td align="center">
                                                    {{ index + 1 }}
                                                </td>
                                                <td>{{ item.tipo }}</td>
                                                <td align="center">
                                                    {{
                                                        JSON.parse(
                                                            item.datos_creacion
                                                        ).fecha
                                                    }}
                                                </td>
                                                <td align="right">
                                                    S/
                                                    {{ roundTo(item.monto, 2) }}
                                                </td>
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
                                                    S/
                                                    {{
                                                        roundTo(
                                                            item.acumulado,
                                                            2
                                                        )
                                                    }}
                                                </td>
                                                <td align="right">
                                                    S/
                                                    {{
                                                        roundTo(
                                                            item.monto -
                                                                item.acumulado,
                                                            2
                                                        )
                                                    }}
                                                </td>
                                                <td align="center">
                                                    {{ item.numero_cuota }}
                                                </td>
                                                <td align="center">
                                                    {{ item.usuario_registro }}
                                                </td>
                                                <td align="center">
                                                    {{
                                                        item.usuario_envio_usuario ==
                                                        null
                                                            ? "-"
                                                            : item.usuario_envio_usuario
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        item.descripcion_envio ==
                                                        null
                                                            ? "-"
                                                            : item.descripcion_envio
                                                    }}
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
</template>

<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import { defaultsDeep } from "lodash";

export default {
    components: {
        headerCloseModal,
    },
    data() {
        return {
            windowWidth: window.innerWidth,
            agencia_id: null,
            credito_id: 0,
            datos_credito: {},
            datos_cuotas: [],
            notificaciones: [],
            carrito_detalle: [],
            carrito_detalle_aud: [],
            carrito_detalle_id: 0,

            editar_numero: false,

            frmDatosCobranza: {
                pago_cuota_cantidad: 0,
                pago_cuota_monto: this.roundTo(0, 2),
                pago_monto: this.roundTo(0, 2),
                pago_cuota: true,

                pago_mora: false,
                pago_mora_monto: this.roundTo(0, 2),
                pago_notificaciones: false,
                pago_notificaciones_monto: this.roundTo(0, 2),
                forma_pago: "por_cuota",

                total_cobro: this.roundTo(0, 2),

                modo_envio: "WHATSAPP",
            },

            edicion: true,
            telefono_principal: null,
            nuevo_telefono_principal: null,
            modo: null,
            modo_2: null,
            cancelacion: false,
            boton_panel: false,
            mostrar_panel: false,
        };
    },

    computed: {
        mi_carrito() {
            return this.$inertia.page.props.creditos_datos.datos_carrito;
        },

        detalle_cuotas() {
            let lista = [];
            if (this.datos_credito != {}) {
                let saldo_interes = parseFloat(
                    this.datos_credito.interes_total
                );
                let saldo_capital = parseFloat(
                    this.datos_credito.capital_total
                );
                let saldo_redondeo = parseFloat(
                    this.datos_credito.redondeo_total
                );

                this.datos_cuotas.forEach((element) => {
                    saldo_capital -= parseFloat(element.capital);
                    saldo_redondeo -= parseFloat(element.redondeo);

                    let fecha_pago = null;

                    if (element.fecha_ultimo_pago != null) {
                        if (element.estado == "C") {
                            fecha_pago = element.fecha_ultimo_pago.substring(
                                0,
                                10
                            );
                        }
                    }

                    let object = {
                        numero_cuota: element.numero_cuota,
                        fecha_vence: this.formato_fecha(
                            element.fecha_vencimiento
                        ),
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
                            element.estado != "C"
                                ? this.roundTo(element.acumulado, 2)
                                : null,
                        capital_pagado: this.roundTo(element.capital_pagado, 2),
                        interes_pagado: this.roundTo(element.interes_pagado, 2),
                        redondeo_pagado: this.roundTo(
                            element.redondeo_pagado,
                            2
                        ),
                        resta:
                            element.estado != "C"
                                ? this.roundTo(
                                      parseFloat(element.cuota) -
                                          parseFloat(element.acumulado),
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

        pago_cuota() {
            return this.frmDatosCobranza.pago_cuota;
        },
        pago_mora() {
            return this.frmDatosCobranza.pago_mora;
        },
        pago_notificaciones() {
            return this.frmDatosCobranza.pago_notificaciones;
        },

        forma_pago() {
            return this.frmDatosCobranza.forma_pago;
        },

        capital_pendiente() {
            if (this.datos_credito == {}) {
                return 0;
            } else {
                let capital_total = parseFloat(
                    this.datos_credito.capital_total
                );
                let capital_pagado = parseFloat(
                    this.datos_credito.capital_pagado
                );

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
                let interes_total = parseFloat(
                    this.datos_credito.interes_total
                );
                let interes_pagado = parseFloat(
                    this.datos_credito.interes_pagado
                );

                let interes_pendiente = interes_total - interes_pagado;

                if (interes_pendiente < 0) {
                    interes_pendiente = 0;
                }

                return interes_pendiente;
            }
        },
        redondeo_pendiente() {
            if (this.datos_credito == {}) {
                return 0;
            } else {
                let redondeo_total = parseFloat(
                    this.datos_credito.redondeo_total
                );
                let redondeo_pagado = parseFloat(
                    this.datos_credito.redondeo_pagado
                );

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
    },
    watch: {
        detalle_cuotas() {
            let self = this;

            $("#tblCuotasDetalle").DataTable().destroy();
            this.TablaCuotas();
        },

        notificaciones() {
            $("#tblNotificacionesDetalle").DataTable().destroy();

            this.TablaNotificaciones();
        },

        forma_pago() {
            if (this.carrito_detalle == null) {
                this.frmDatosCobranza.pago_cuota_cantidad = 0;
                this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
                this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);

                if (this.cancelacion == true) {
                    this.frmDatosCobranza.pago_monto = this.roundTo(
                        this.saldo_por_pagar,
                        2
                    );
                }

                this.ActualizarTotalCobro();
            }
        },
        pago_cuota() {
            if (this.carrito_detalle == null) {
                this.frmDatosCobranza.pago_cuota_cantidad = 0;
                this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
                this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);

                if (this.cancelacion == true) {
                    this.frmDatosCobranza.pago_monto = this.roundTo(
                        this.saldo_por_pagar,
                        2
                    );
                }

                this.ActualizarTotalCobro();
            }
        },
        pago_mora() {
            if (this.carrito_detalle == null) {
                this.frmDatosCobranza.pago_mora_monto = this.roundTo(0, 2);
            }
            if (this.cancelacion == true) {
                this.frmDatosCobranza.pago_mora_monto = this.roundTo(
                    this.mora_pendiente,
                    2
                );
            }
            this.ActualizarTotalCobro();
        },
        pago_notificaciones() {
            if (this.carrito_detalle == null) {
                this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(
                    0,
                    2
                );
            }
            if (this.cancelacion == true) {
                this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(
                    this.notificaciones_pendiente,
                    2
                );
            }
            this.ActualizarTotalCobro();
        },

        cancelacion() {
            if (this.cancelacion == true) {
                if (this.saldo_por_pagar == 0) {
                    this.frmDatosCobranza.pago_cuota = false;
                } else {
                    this.frmDatosCobranza.pago_cuota = true;
                    this.frmDatosCobranza.forma_pago = "por_monto";
                    this.frmDatosCobranza.pago_monto = this.roundTo(
                        this.saldo_por_pagar,
                        2
                    );
                }

                if (this.mora_pendiente != 0) {
                    this.frmDatosCobranza.pago_mora = true;
                    this.frmDatosCobranza.pago_mora_monto = this.roundTo(
                        this.mora_pendiente,
                        2
                    );
                } else {
                    this.frmDatosCobranza.pago_mora = false;
                }
                if (this.notificaciones_pendiente != 0) {
                    this.frmDatosCobranza.pago_notificaciones = true;
                    this.frmDatosCobranza.pago_notificaciones_monto =
                        this.roundTo(this.notificaciones_pendiente, 2);
                } else {
                    this.frmDatosCobranza.pago_notificaciones = false;
                }
                this.ActualizarTotalCobro();
            }
            //   } else if (this.carrito_detalle == null)  {

            //     this.frmDatosCobranza.pago_cuota = true;
            //     this.frmDatosCobranza.forma_pago = "por_cuota";
            //     this.frmDatosCobranza.pago_cuota_cantidad = 0;
            //     this.frmDatosCobranza.pago_mora = false;
            //     this.frmDatosCobranza.pago_notificaciones = false;
            //     this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
            //     this.frmDatosCobranza.total_cobro = this.roundTo(0, 2);
            //   }
        },
    },

    mounted() {
        window.addEventListener("resize", () => {
            this.windowWidth = window.innerWidth;
        });

        if (this.windowWidth < 900) {
            this.mostrar_panel = false;
        } else {
            this.mostrar_panel = true;
        }

        this.TablaCuotas();
        this.TablaNotificaciones();
    },

    methods: {
        VerMasInformacion() {
            this.mostrar_panel = true;
        },
        VerMenosInformacion() {
            this.mostrar_panel = false;
        },
        async VerificarTiempo() {
            const params = {
                carrito_detalle_id: this.carrito_detalle.id,
                agencia_id: this.datos_credito.agencia_id,
            };

            // this.$inertia.get(route("cre.carrito.verificar_tiempo"), params);
            // return false;

            await axios
                .get(route("cre.carrito.verificar_tiempo"), { params })
                .then((response) => {
                    let resultado = response.data;
                    if (resultado) {
                        this.edicion = true;
                        this.carrito_detalle_id = this.carrito_detalle.id;

                        this.carrito_detalle_aud = [];

                        let object = {
                            pago_por_cuota: this.carrito_detalle.pago_por_cuota,
                            pago_cantidad_cuota:
                                this.carrito_detalle.pago_cantidad_cuota,
                            pago_cuota_monto:
                                this.carrito_detalle.pago_cuota_monto,
                            pago_por_monto: this.carrito_detalle.pago_por_monto,
                            pago_monto: this.carrito_detalle.pago_monto,
                            pago_mora: this.carrito_detalle.pago_mora,
                            pago_mora_monto:
                                this.carrito_detalle.pago_mora_monto,
                            pago_notificaciones:
                                this.carrito_detalle.pago_notificaciones,
                            pago_notificaciones_monto:
                                this.carrito_detalle.pago_notificaciones_monto,
                            total_cobro: this.carrito_detalle.total_cobro,
                            telefono_envio: this.carrito_detalle.telefono_envio,
                            fecha_cobro: this.carrito_detalle.fecha_cobro,
                            modo_envio: this.carrito_detalle.modo_envio,
                        };
                        this.carrito_detalle_aud.push(object);

                        this.carrito_detalle = null;
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "¡Ups!",
                            text: "El tiempo asignado para modificar el cobro, ya ha sido superado",
                            showConfirmButton: true,
                        });
                        this.edicion = false;
                        return false;
                    }
                });
        },

        ResetearCampos() {
            if (this.carrito_detalle != null) {
                this.edicion = false;

                if (
                    this.carrito_detalle.pago_por_cuota == 0 &&
                    this.carrito_detalle.pago_por_monto == 0
                ) {
                    this.frmDatosCobranza.pago_cuota = false;
                } else {
                    this.frmDatosCobranza.pago_cuota = true;
                }

                this.frmDatosCobranza.pago_cuota_cantidad =
                    this.carrito_detalle.pago_cantidad_cuota;
                this.frmDatosCobranza.pago_cuota_monto =
                    this.carrito_detalle.pago_cuota_monto;
                this.frmDatosCobranza.pago_monto =
                    this.carrito_detalle.pago_monto;
                this.frmDatosCobranza.pago_mora_monto =
                    this.carrito_detalle.pago_mora_monto;
                this.frmDatosCobranza.pago_notificaciones_monto =
                    this.carrito_detalle.pago_notificaciones_monto;
                this.frmDatosCobranza.total_cobro =
                    this.carrito_detalle.total_cobro;

                this.frmDatosCobranza.forma_pago = "por_cuota";

                if (this.carrito_detalle.pago_por_monto == 1) {
                    this.frmDatosCobranza.forma_pago = "por_monto";
                }

                if (this.carrito_detalle.pago_mora == 1) {
                    this.frmDatosCobranza.pago_mora = true;
                } else {
                    this.frmDatosCobranza.pago_mora = false;
                }

                if (this.carrito_detalle.pago_notificaciones == 1) {
                    this.frmDatosCobranza.pago_notificaciones = true;
                } else {
                    this.frmDatosCobranza.pago_notificaciones = false;
                }

                this.frmDatosCobranza.modo_envio =
                    this.carrito_detalle.modo_envio;
            } else if (this.carrito_detalle == null) {
                this.edicion = true;

                this.frmDatosCobranza.pago_cuota = true;
                this.frmDatosCobranza.forma_pago = "por_cuota";
                this.frmDatosCobranza.pago_cuota_cantidad = 0;
                this.frmDatosCobranza.pago_mora = false;
                this.frmDatosCobranza.pago_notificaciones = false;
                this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
                this.frmDatosCobranza.total_cobro = this.roundTo(0, 2);
                this.frmDatosCobranza.modo_envio = "WHATSAPP";
            }
            this.cancelacion = false;

            if (this.windowWidth < 900) {
                this.mostrar_panel = false;
                this.boton_panel = true;
            } else {
                this.mostrar_panel = true;
                this.boton_panel = false;
            }
        },
        async DetallePagos() {
            let pago_cuotas = [];
            let pago_moras = [];
            let pago_notificaciones = [];
            let mdlDetallePagos = "";

            if (this.modo_2 == "padre.padre") {
                mdlDetallePagos = this.$parent.$parent.$refs.mdlDetallePagos;
            } else {
                mdlDetallePagos = this.$parent.$refs.mdlDetallePagos;
            }

            let data = new FormData();
            data.append("agencia_id", this.agencia_id);
            data.append("credito_id", this.credito_id);

            console.log(this.agencia_id, this.credito_id);

            await axios
                .post(route("caj.pago_cuotas.listar"), data)
                .then((response) => {
                    pago_cuotas = response.data;
                    mdlDetallePagos.pago_cuotas = pago_cuotas;
                });

            //    this.$inertia.post(route("caj.pago_moras.listar"),data);
            // return false

            await axios
                .post(route("caj.pago_moras.listar"), data)
                .then((response) => {
                    pago_moras = response.data;
                    mdlDetallePagos.pago_moras = pago_moras;
                });

            await axios
                .post(route("caj.pago_notificaciones.listar"), data)
                .then((response) => {
                    pago_notificaciones = response.data;
                    mdlDetallePagos.pago_notificaciones = pago_notificaciones;
                });

            $("#mdlDetallePagos").css("display", "block");
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

            let selection_1 = $("#tblCuotasDetalle #dc_1_" + posicion);

            $("#tabla-cuotas .dataTables_scrollBody").scrollTo(selection_1);
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
            let cuotas_pendientes = parseFloat(
                this.datos_credito.cuotas_pendientes
            );

            if (nombre == "por_cuota") {
                valor = parseFloat(this.roundTo(valor, 0));
                if (valor > cuotas_pendientes) {
                    valor = cuotas_pendientes;
                }

                let monto = 0;

                if (valor > 0) {
                    let acumulado = this.datos_cuotas.filter(
                        (item) =>
                            item.numero_cuota == this.datos_credito.cuota_actual
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
                    this.frmDatosCobranza.pago_cuota_monto = this.roundTo(
                        monto,
                        2
                    );
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
                    this.frmDatosCobranza.pago_mora_monto = this.roundTo(
                        valor,
                        2
                    );
                }
                this.ActualizarTotalCobro();
                return false;
            } else if (nombre == "pago_notificaciones") {
                if (valor > this.notificaciones_pendiente) {
                    this.frmDatosCobranza.pago_notificaciones_monto =
                        this.roundTo(this.notificaciones_pendiente, 2);
                } else {
                    this.frmDatosCobranza.pago_notificaciones_monto =
                        this.roundTo(valor, 2);
                }
                this.ActualizarTotalCobro();
                return false;
            }
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

        TablaCuotas() {
            let self = this;

            let scroll_height = "130px";
            if (this.windowWidth <= 900) {
                scroll_height = "110px";
            }
            this.$nextTick(() => {
                var table = $("#tblCuotasDetalle").DataTable({
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
                            sortAscending:
                                ": activar para ordenar de forma ascendente",
                            sortDescending:
                                ": activar para ordenar de forma descendente",
                        },
                    },
                });
                self.PosicionarTablas();
            });
        },
        TablaNotificaciones() {
            this.$nextTick(() => {
                var table = $("#tblNotificacionesDetalle").DataTable({
                    scrollY: "100px",
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
                            sortAscending:
                                ": activar para ordenar de forma ascendente",
                            sortDescending:
                                ": activar para ordenar de forma descendente",
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

        EditarNumero() {
            this.editar_numero = true;
        },
        GuardarNumero() {
            let self = this;
            if (this.nuevo_telefono_principal != this.telefono_principal) {
                if (String(this.nuevo_telefono_principal).length != 9) {
                    Swal.fire({
                        icon: "error",
                        title: "¡Ups!",
                        text: "Teléfono NO VÁLIDO, debe contener 9 dígitos.",
                        allowOutsideClick: true,
                    });

                    return false;
                }

                Swal.fire({
                    icon: "warning",
                    text: "¿Desea ACTUALIZAR el NÚMERO PRINCIPAL?",
                    confirmButtonText: "Si",
                    showCancelButton: true,
                    cancelButtonText: "No",
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        let data = new FormData();

                        data.append(
                            "cliente_id",
                            this.datos_credito.cliente_id
                        );
                        data.append(
                            "agencia_id",
                            this.datos_credito.agencia_id
                        );
                        data.append(
                            "nuevo_telefono",
                            this.nuevo_telefono_principal
                        );

                        // this.$inertia.post(route("cre.carrito.actualizar_telefono"), data);
                        // return false;

                        return axios
                            .post(
                                route("cre.carrito.actualizar_telefono"),
                                data
                            )
                            .then(() => {
                                self.editar_numero = false;
                                self.telefono_principal =
                                    self.nuevo_telefono_principal;
                            })
                            .catch((error) => {
                                console.log(error.response.data.error);
                                Swal.showValidationMessage(
                                    `Ha ocurrido un error: COMUNICAR AL ÁREA DE SOPORTE`
                                );
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: "success",
                            title: "¡ACTUALIZADO!",
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    }
                });
            } else {
                this.editar_numero = false;
            }
        },

        async PagarCredito() {
            if (String(this.telefono_principal).length != 9) {
                Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Teléfono NO VÁLIDO, debe contener 9 dígitos.",
                    allowOutsideClick: true,
                });

                return false;
            }

            let mensaje = "";

            let params = {
                agencia_carrito: this.mi_carrito.agencia_id,
                credito_id: this.mi_carrito.id,
                agencia_credito: this.datos_credito.agencia_id,
                carrito_id: this.datos_credito.id,
            };

            // this.$inertia.get(route("cre.carrito.verificar"),data);
            // return false

            await axios
                .get(route("cre.carrito.verificar"), { params })
                .then((response) => {
                    let resultado = response.data;
                    if (resultado == "CARRITO_CERRADO") {
                        Swal.fire({
                            icon: "warning",
                            title: "¡Ups!",
                            text: "Este CARRITO ya está CERRADO.",
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                return this.$inertia.get(route("cre.carrito"));
                            }
                        });
                    } else if (resultado == "PENDIENTE_PAGO") {
                        Swal.fire({
                            icon: "warning",
                            title: "¡Ups!",
                            text: "Este crédito tiene una cobranza PENDIENTE de pago en CAJA.",
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                return this.$inertia.get(route("cre.carrito"));
                            }
                        });
                    }
                });

            // return false;
            if (this.carrito_detalle != null) {
                await this.VerificarTiempo();
            }

            if (this.edicion == true) {
                if (this.pago_cuota) {
                    if (
                        this.frmDatosCobranza.forma_pago == "por_cuota" &&
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
                        this.frmDatosCobranza.forma_pago == "por_monto" &&
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
                        if (this.frmDatosCobranza.forma_pago == "por_cuota") {
                            mensaje +=
                                this.frmDatosCobranza.pago_cuota_cantidad +
                                " CUOTA(S)=S/ " +
                                this.roundTo(
                                    this.frmDatosCobranza.pago_cuota_monto,
                                    2
                                ) +
                                " ";
                        } else if (
                            this.frmDatosCobranza.forma_pago == "por_monto"
                        ) {
                            mensaje +=
                                "MONTO=S/ " +
                                this.roundTo(
                                    this.frmDatosCobranza.pago_monto,
                                    2
                                ) +
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
                            this.roundTo(
                                this.frmDatosCobranza.pago_mora_monto,
                                2
                            ) +
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
                            this.roundTo(
                                this.frmDatosCobranza.pago_notificaciones_monto,
                                2
                            ) +
                            " ";
                    }
                }

                if (this.editar_numero) {
                    Swal.fire({
                        icon: "error",
                        title: "¡Ups!",
                        text: "Debe guardar el nuevo NÚMERO PRINCIPAL para continuar con el pago.",
                        allowOutsideClick: true,
                    });

                    return false;
                }
                let titulo = null;
                if (
                    this.roundTo(this.frmDatosCobranza.total_cobro, 2) ==
                    this.roundTo(this.datos_credito.saldo_total, 2)
                ) {
                    titulo =
                        "El monto a pagar es igual al SALDO TOTAL, ¿Desea cancelar el crédito?";
                } else {
                    titulo = "¿Desea REGISTRAR el pago?";
                }

                Swal.fire({
                    title: titulo,
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
                        let data = new FormData();

                        if (this.modo == "EDITAR") {
                            data.append(
                                "carrito_detalle_id",
                                this.carrito_detalle_id
                            );
                            data.append(
                                "carrito_detalle_aud",
                                JSON.stringify(this.carrito_detalle_aud)
                            );
                        }

                        data.append(
                            "agencia_carrito",
                            this.mi_carrito.agencia_id
                        );
                        data.append("carrito_id", this.mi_carrito.id);
                        data.append(
                            "agencia_credito",
                            this.datos_credito.agencia_id
                        );
                        data.append("credito_id", this.credito_id);
                        data.append(
                            "cliente_id",
                            this.datos_credito.cliente_id
                        );
                        data.append(
                            "telefono_principal",
                            this.telefono_principal
                        );
                        data.append(
                            "modo_envio",
                            this.frmDatosCobranza.modo_envio
                        );
                        data.append("modo", this.modo);
                        data.append(
                            "datos_cobranza",
                            JSON.stringify(this.frmDatosCobranza)
                        );

                        Swal.fire({
                            title: "REGISTRANDO PAGO",
                            text: "Espere porfavor...",
                            allowOutsideClick: false,
                            didOpen: async () => {
                                // this.$inertia.post(route("cre.carrito.pagar"), data);
                                // return false;

                                Swal.showLoading();

                                await axios
                                    .post(route("cre.carrito.pagar"), data)
                                    .then((response) => {
                                        let resultado = response.data;
                                        if (resultado == "ENVIADO") {
                                            Swal.fire({
                                                icon: "success",
                                                title: "¡ÉXITO!",
                                                timer: 2000,
                                                showConfirmButton: false,
                                            });
                                            this.$inertia.get(
                                                route("cre.carrito")
                                            );
                                        }
                                    });
                            },
                        });
                    }
                });
            }
        },
        EditarPagoCredito() {
            this.VerificarTiempo();
            // this.edicion = true;
        },
    },
};
</script>

<style lang="css">
.mdlDetalleCreditoCarrito {
    width: 60% !important;
    margin-left: 20% !important;
}

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

@media (max-width: 900px) {
    .mdlDetalleCreditoCarrito {
        width: 98% !important;
        margin-left: 1% !important;
        margin-top: 1px !important;
    }
}
</style>
