<template>
    <layout ref="layout">
        <div class="slot_body slot-aprobacion" slot="component-view">
            <div class="content" style="display: block">
                <div class="card">
                    <headerClose :title="'APROBACIÓN'"></headerClose>
                    <div class="card-title">INFORMACIÓN DE APROBACIÓN</div>

                    <div class="card-body card-block">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a
                                    class="nav-link active tab-title"
                                    id="propuesta-tab"
                                    data-toggle="tab"
                                    href="#propuesta"
                                    role="tab"
                                    aria-controls="propuesta"
                                    aria-selected="true"
                                    >PROPUESTA</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link tab-title"
                                    id="calendario-tab"
                                    data-toggle="tab"
                                    href="#calendario"
                                    role="tab"
                                    aria-controls="calendario"
                                    aria-selected="false"
                                    >CALENDARIO</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link tab-title"
                                    id="aprobacion-tab"
                                    data-toggle="tab"
                                    href="#aprobacion"
                                    role="tab"
                                    aria-controls="aprobacion"
                                    aria-selected="false"
                                    >APROBACIÓN</a
                                >
                            </li>
                            <li class="nav-item" v-if="frmAprobacion.prendario">
                                <a
                                    class="nav-link tab-title"
                                    id="prendas-tab"
                                    data-toggle="tab"
                                    href="#prendas"
                                    role="tab"
                                    aria-controls="prendas"
                                    aria-selected="false"
                                    >PRENDAS</a
                                >
                            </li>
                            <li
                                class="nav-item"
                                v-if="creditos_vinculados_de.length > 0"
                            >
                                <a
                                    class="nav-link tab-title"
                                    id="vinculado-de-tab"
                                    data-toggle="tab"
                                    href="#vinculado-de"
                                    role="tab"
                                    aria-controls="vinculado-de"
                                    aria-selected="false"
                                    >SU PARIENTE/AVAL DE</a
                                >
                            </li>
                            <li
                                class="nav-item"
                                v-if="creditos_vinculados_a.length > 0"
                            >
                                <a
                                    class="nav-link tab-title"
                                    id="vinculado-a-tab"
                                    data-toggle="tab"
                                    href="#vinculado-a"
                                    role="tab"
                                    aria-controls="vinculado-a"
                                    aria-selected="false"
                                    >ES PARIENTE/AVAL DE</a
                                >
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div
                                class="tab-pane fade show active"
                                id="propuesta"
                                role="tabpanel"
                                aria-labelledby="propuesta-tab"
                            >
                                <ul
                                    class="nav nav-tabs"
                                    id="myTab2"
                                    role="tablist"
                                >
                                    <li class="nav-item">
                                        <a
                                            class="nav-link active tab-title"
                                            id="cliente-tab"
                                            data-toggle="tab"
                                            href="#cliente"
                                            role="tab"
                                            aria-controls="cliente"
                                            aria-selected="false"
                                            >CLIENTE</a
                                        >
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            class="nav-link tab-title"
                                            id="credito-tab"
                                            data-toggle="tab"
                                            href="#credito"
                                            role="tab"
                                            aria-controls="credito"
                                            aria-selected="false"
                                            >DATOS DEL CRÉDITO</a
                                        >
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTab2Content">
                                    <div
                                        class="tab-pane fade show active"
                                        id="cliente"
                                        role="tabpanel"
                                        aria-labelledby="cliente-tab"
                                    >
                                        <div class="form-row">
                                            <div
                                                class="form-group col-md-9 col-12"
                                            >
                                                <label class="label-title"
                                                    >TITULAR</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    :value="
                                                        nombre_completo_titular
                                                    "
                                                    style="font-weight: bolder"
                                                    :disabled="true"
                                                />
                                            </div>

                                            <div
                                                class="form-group col-md-3 col-6"
                                            >
                                                <label
                                                    class="label-title center"
                                                    >ASESOR</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_cliente.usuario_asesor
                                                    "
                                                    style="font-weight: bolder"
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-3 col-6"
                                            >
                                                <label class="label-title"
                                                    >DNI</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="datos_cliente.dni"
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-3 col-7"
                                            >
                                                <label class="label-title"
                                                    >C. RIESGO</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_cliente.central_riesgo
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-3 col-5"
                                            >
                                                <label class="label-title"
                                                    >CALIFICACIÓN</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_cliente.calificacion
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-3 col-6"
                                            >
                                                <label class="label-title"
                                                    >AGENCIA</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_cliente.agencia
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-6 col-6"
                                            >
                                                <label class="label-title"
                                                    >DIRECCIÓN</label
                                                >
                                                <textarea
                                                    class="form-control text-row"
                                                    rows="2"
                                                    :value="
                                                        datos_cliente.direccion
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="form-group col-md-6 col-6"
                                            >
                                                <label class="label-title"
                                                    >REFERENCIA</label
                                                >
                                                <textarea
                                                    class="form-control text-row"
                                                    rows="2"
                                                    :value="
                                                        datos_cliente.referencia_direccion
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >PARIENTE</label
                                                >
                                                <textarea
                                                    class="form-control text-row"
                                                    rows="2"
                                                    :value="
                                                        nombre_completo_pariente
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >AVAL</label
                                                >
                                                <textarea
                                                    class="form-control text-row"
                                                    rows="2"
                                                    :value="
                                                        nombre_completo_aval
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >PARIENTE-AVAL</label
                                                >
                                                <textarea
                                                    class="form-control text-row"
                                                    rows="2"
                                                    :value="
                                                        nombre_completo_pariente_aval
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="form-group col-md-12 col-12"
                                            >
                                                <label class="label-title"
                                                    >NEGOCIO</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    :value="negocio"
                                                    :disabled="true"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="credito"
                                        role="tabpanel"
                                        aria-labelledby="credito-tab"
                                    >
                                        <div class="form-row">
                                            <div
                                                class="form-group col-md-9 col-8"
                                            >
                                                <label class="label-title"
                                                    >TITULAR</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    :value="
                                                        nombre_completo_titular
                                                    "
                                                    style="font-weight: bolder"
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-3 col-4"
                                            >
                                                <label
                                                    class="label-title center"
                                                    >ASESOR</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_cliente.usuario_asesor
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >MONTO
                                                    <span
                                                        class="text"
                                                        style="
                                                            font-size: 15px;
                                                            font-weight: bolder;
                                                            color: var(
                                                                --colorAlto
                                                            );
                                                        "
                                                        >S/
                                                        {{
                                                            this.roundTo(
                                                                datos_propuesta.monto,
                                                                2,
                                                            )
                                                        }}</span
                                                    ></label
                                                >
                                            </div>

                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="text1 label-title"
                                                    >CUOTA CALCULADA
                                                    <span
                                                        class="text"
                                                        style="
                                                            font-size: 15px;
                                                            font-weight: bolder;
                                                            color: var(
                                                                --colorAlto
                                                            );
                                                        "
                                                        >S/
                                                        {{
                                                            this.roundTo(
                                                                datos_propuesta.cuota,
                                                                2,
                                                            )
                                                        }}</span
                                                    ></label
                                                >
                                                <label class="text2 label-title"
                                                    >CUOTA CAL.
                                                    <span
                                                        class="text"
                                                        style="
                                                            font-size: 15px;
                                                            font-weight: bolder;
                                                            color: var(
                                                                --colorAlto
                                                            );
                                                        "
                                                        >S/
                                                        {{
                                                            this.roundTo(
                                                                datos_propuesta.cuota,
                                                                2,
                                                            )
                                                        }}</span
                                                    ></label
                                                >
                                            </div>

                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >PROMOTOR</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_propuesta.usuario_promotor
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>

                                            <div
                                                class="form-row col-md-12"
                                                style="
                                                    background: var(
                                                        --verdeClaroEmpresarial
                                                    );
                                                "
                                            >
                                                <div
                                                    class="form-group col-md-4 col-4"
                                                >
                                                    <label class="label-title"
                                                        >PERIODO:
                                                        <span
                                                            class="text"
                                                            style="
                                                                font-size: 11px;
                                                                font-weight: bolder;
                                                                color: var(
                                                                    --colorAlto
                                                                );
                                                            "
                                                        >
                                                            {{
                                                                datos_propuesta.periodo_pago
                                                            }}</span
                                                        ></label
                                                    >
                                                </div>
                                                <div
                                                    class="form-group col-md-4 col-4"
                                                >
                                                    <label class="label-title"
                                                        >PLAZO:
                                                        <span
                                                            class="text"
                                                            style="
                                                                text-transform: lowercase;
                                                                font-size: 11px;
                                                                font-weight: bolder;
                                                                color: var(
                                                                    --colorAlto
                                                                );
                                                            "
                                                        >
                                                            {{
                                                                this.roundTo(
                                                                    datos_propuesta.plazo,
                                                                    0,
                                                                ) +
                                                                " " +
                                                                periodo_medicion(
                                                                    datos_propuesta.periodo_pago,
                                                                )
                                                            }}</span
                                                        ></label
                                                    >
                                                </div>
                                                <div
                                                    class="form-group col-md-4 col-4"
                                                >
                                                    <label class="label-title"
                                                        >TASA INTERÉS:
                                                        <span
                                                            class="text"
                                                            style="
                                                                font-size: 11px;
                                                                font-weight: bolder;
                                                                color: var(
                                                                    --colorAlto
                                                                );
                                                            "
                                                        >
                                                            {{
                                                                this.roundTo(
                                                                    datos_propuesta.tasa_interes,
                                                                    2,
                                                                )
                                                            }}
                                                            %</span
                                                        ></label
                                                    >
                                                </div>
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >SECTOR</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_propuesta.sector
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >PRODUCTO</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    :value="
                                                        datos_propuesta.producto
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >SUBPRODUCTO</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_propuesta.subproducto ==
                                                        null
                                                            ? '-'
                                                            : datos_propuesta.subproducto
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-4 col-6"
                                            >
                                                <label class="label-title"
                                                    >TIPO</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_propuesta.tipo
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-5 col-6"
                                            >
                                                <label class="label-title"
                                                    >GARANTÍA</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_propuesta.garantia +
                                                        ' - S/ ' +
                                                        this.roundTo(
                                                            datos_propuesta.valor_garantia,
                                                            2,
                                                        )
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-3 col-6"
                                            >
                                                <label
                                                    class="label-title center"
                                                    >USUARIO REGISTRO</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control center"
                                                    :value="
                                                        datos_propuesta.usuario_registro
                                                    "
                                                    :disabled="true"
                                                />
                                            </div>
                                            <div
                                                class="form-group col-md-6 col-6"
                                            >
                                                <label class="label-title"
                                                    >COMENTARIO GARANTÍA</label
                                                >
                                                <textarea
                                                    rows="2"
                                                    class="form-control text-row"
                                                    :value="
                                                        datos_propuesta.comentario_garantia
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                            <div
                                                class="form-group col-md-6 col-6"
                                            >
                                                <label class="label-title"
                                                    >COMENTARIO PROPUESTA</label
                                                >
                                                <textarea
                                                    rows="2"
                                                    class="form-control text-row"
                                                    :value="
                                                        datos_propuesta.comentario_propuesta
                                                    "
                                                    :disabled="true"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="calendario"
                                role="tabpanel"
                                aria-labelledby="calendario-tab"
                            >
                                <div class="form-row mt-1 mb-1">
                                    <div
                                        class="input-group input-group col-md-7 col-12"
                                    >
                                        <div class="input-group-prepend">
                                            <label
                                                class="input-group-text prepend-title"
                                            >
                                                TITULAR
                                            </label>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="nombre_completo_titular"
                                            style="font-weight: bolder"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div
                                        class="form-group col-md-2 col-6"
                                        :style="
                                            this.windowWidth > 900
                                                ? 'margin-bottom: 0rem !important'
                                                : 'margin-bottom: 0rem ;margin-top: 5px !important'
                                        "
                                    >
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="VerHistorial('CLIENTE')"
                                        >
                                            <span class="icon text-white">
                                                <i class="far fa-clock"></i
                                            ></span>
                                            <span class="text">HISTORIAL</span>
                                        </button>
                                    </div>
                                    <div class="input-group col-md-3">
                                        <div class="input-group-prepend">
                                            <label
                                                class="input-group-text prepend-title"
                                            >
                                                ASESOR
                                            </label>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="
                                                datos_cliente.usuario_asesor
                                            "
                                            style="font-weight: bolder"
                                            :disabled="true"
                                        />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div
                                        class="col-md-4"
                                        :style="
                                            this.windowWidth > 900
                                                ? 'border-right: 5px solid; height: 400px'
                                                : 'border-bottom: 5px solid; height: 270px'
                                        "
                                    >
                                        <div class="col-md-12 text-center">
                                            <span
                                                class="label-title p-1"
                                                style="
                                                    font-size: 11px !important;
                                                    color: white;
                                                    background: var(
                                                        --plomoOscuroEmpresarial
                                                    );
                                                    border-radius: 5px;
                                                "
                                                >PROPUESTA</span
                                            >
                                        </div>

                                        <div
                                            class="col-md-12"
                                            :style="
                                                this.windowWidth > 900
                                                    ? 'height: 80px !important'
                                                    : ''
                                            "
                                        >
                                            <label class="label-title col-md-12"
                                                >Fecha propuesta:
                                                {{
                                                    this.datos_propuesta
                                                        .fecha_propuesta
                                                }}</label
                                            >
                                            <label class="label-title col-md-12"
                                                >Días de gracia:
                                                {{
                                                    this.datos_propuesta
                                                        .dias_gracia
                                                }}</label
                                            >
                                            <label class="label-title col-md-12"
                                                >Primer pago:
                                                {{
                                                    this.cronograma_propuesta
                                                        .length > 0
                                                        ? this
                                                              .cronograma_propuesta[0]
                                                              .fecha_pago
                                                        : "-"
                                                }}</label
                                            >
                                        </div>

                                        <div class="col-md-12">
                                            <DataTable
                                                :value="cronograma_propuesta"
                                                :scrollable="true"
                                                scrollDirection="both"
                                                scrollHeight="250px"
                                                selectionMode="single"
                                                showGridlines
                                                stripedRows
                                            >
                                                <Column
                                                    field="orden"
                                                    header="N°"
                                                    :styles="{
                                                        width: '40px',
                                                        justifyContent:
                                                            'center',
                                                    }"
                                                >
                                                    <template #body="slotProps">
                                                        {{
                                                            slotProps.index + 1
                                                        }}
                                                    </template>
                                                </Column>
                                                <Column
                                                    field="fecha_pago"
                                                    header="FECHA_PAGO"
                                                    :styles="{
                                                        width: '80px',
                                                        justifyContent:
                                                            'center',
                                                    }"
                                                >
                                                </Column>
                                                <Column
                                                    field="dia_pago"
                                                    header="DÍA_PAGO"
                                                    :styles="{
                                                        width: '80px',
                                                        justifyContent:
                                                            'center',
                                                    }"
                                                >
                                                </Column>
                                                <template #empty>
                                                    No hay CUOTAS
                                                    generadas.</template
                                                >
                                            </DataTable>
                                        </div>
                                        <div
                                            class="col-md-12 mt-2"
                                            style="
                                                position: absolute;
                                                bottom: 0;
                                            "
                                        >
                                            <label class="label-title"
                                                >CUOTA:
                                                <span
                                                    style="
                                                        font-size: 18px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    >S/
                                                    {{
                                                        this.roundTo(
                                                            this.datos_propuesta
                                                                .cuota,
                                                            2,
                                                        )
                                                    }}</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-row col-md-8">
                                        <div
                                            class="col-md-6"
                                            :style="
                                                this.windowWidth > 900
                                                    ? 'height: 400px'
                                                    : 'height: 280px'
                                            "
                                        >
                                            <div
                                                class="col-md-12 text-center"
                                                :class="
                                                    this.windowWidth <= 900
                                                        ? 'mt-1'
                                                        : ''
                                                "
                                            >
                                                <span
                                                    class="label-title p-1"
                                                    style="
                                                        font-size: 11px !important;
                                                        color: white;
                                                        background: var(
                                                            --plomoOscuroEmpresarial
                                                        );
                                                        border-radius: 5px;
                                                    "
                                                    >APROBACIÓN</span
                                                >
                                            </div>
                                            <div
                                                class="col-md-12"
                                                :style="
                                                    this.windowWidth > 900
                                                        ? 'height: 80px !important'
                                                        : ''
                                                "
                                            >
                                                <label class="label-title"
                                                    >MONTO
                                                    <span
                                                        class="text"
                                                        style="font-size: 9px"
                                                        >(máx.:
                                                        {{
                                                            datos_cliente.monto_maximo
                                                        }})</span
                                                    ></label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        (!$v.frmAprobacion.monto
                                                            .required ||
                                                            !$v.frmAprobacion
                                                                .monto.noZero)
                                                    "
                                                    class="span-error-message"
                                                >
                                                    *
                                                </span>
                                                <input
                                                    type="number"
                                                    class="form-control center"
                                                    min="0"
                                                    step="0.01"
                                                    lang="en"
                                                    style="
                                                        height: 50px;
                                                        font-size: 30px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    v-model.number="
                                                        frmAprobacion.monto
                                                    "
                                                    name="monto"
                                                    @change="Redondear"
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                />
                                            </div>

                                            <DataTable
                                                :value="cronograma_aprobacion"
                                                :scrollable="true"
                                                scrollDirection="both"
                                                scrollHeight="250px"
                                                selectionMode="single"
                                                showGridlines
                                                stripedRows
                                            >
                                                <Column
                                                    field="orden"
                                                    header="N°"
                                                    :styles="{
                                                        width: '40px',
                                                        justifyContent:
                                                            'center',
                                                    }"
                                                >
                                                    <template #body="slotProps">
                                                        {{
                                                            slotProps.index + 1
                                                        }}
                                                    </template>
                                                </Column>
                                                <Column
                                                    field="fecha_pago"
                                                    header="FECHA_PAGO"
                                                    :styles="{
                                                        width: '80px',
                                                        justifyContent:
                                                            'center',
                                                    }"
                                                >
                                                </Column>
                                                <Column
                                                    field="dia_pago"
                                                    header="DÍA_PAGO"
                                                    :styles="{
                                                        width: '80px',
                                                        justifyContent:
                                                            'center',
                                                    }"
                                                >
                                                </Column>
                                                <template #empty>
                                                    No hay CUOTAS
                                                    generadas.</template
                                                >
                                            </DataTable>

                                            <div
                                                class="col-md-12 mt-2"
                                                style="
                                                    position: absolute;
                                                    bottom: 0;
                                                "
                                            >
                                                <label class="label-title"
                                                    >CUOTA:
                                                    <span
                                                        style="
                                                            font-size: 18px;
                                                            font-weight: bolder;
                                                            color: var(
                                                                --colorAlto
                                                            );
                                                        "
                                                        >S/
                                                        {{
                                                            this.roundTo(
                                                                frmAprobacion.cuota,
                                                                2,
                                                            )
                                                        }}</span
                                                    >
                                                </label>
                                            </div>
                                        </div>
                                        <div
                                            class="form-row col-md-6"
                                            style="max-height: 450px"
                                        >
                                            <div class="col-md-12 col-6">
                                                <label class="label-title"
                                                    >SECTOR</label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        !$v.frmAprobacion
                                                            .sector_id.noZero
                                                    "
                                                    class="span-error-message"
                                                >
                                                    *
                                                </span>
                                                <select
                                                    class="form-control"
                                                    v-model.number="
                                                        frmAprobacion.sector_id
                                                    "
                                                >
                                                    <option :value="0" disabled>
                                                        Seleccione...
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            item, index
                                                        ) in sectores"
                                                        :key="index"
                                                        :value="item.id"
                                                    >
                                                        {{ item.sector }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 col-6">
                                                <label class="label-title"
                                                    >PRODUCTO</label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        !$v.frmAprobacion
                                                            .producto_id.noZero
                                                    "
                                                    class="span-error-message"
                                                >
                                                    *
                                                </span>
                                                <select
                                                    class="form-control"
                                                    v-model.number="
                                                        frmAprobacion.producto_id
                                                    "
                                                    @change="ListarSubproductos"
                                                >
                                                    <option :value="0" disabled>
                                                        Seleccione...
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            item, index
                                                        ) in productos"
                                                        :key="index"
                                                        :value="item.id"
                                                    >
                                                        {{ item.producto }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 col-6">
                                                <label class="label-title"
                                                    >SUBPRODUCTO</label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        subproductos_filtrados.length !=
                                                            0 &&
                                                        !$v.frmAprobacion
                                                            .subproducto_id
                                                            .noNull
                                                    "
                                                    class="span-error-message"
                                                >
                                                    *
                                                </span>
                                                <select
                                                    class="form-control"
                                                    v-model.number="
                                                        frmAprobacion.subproducto_id
                                                    "
                                                    :disabled="
                                                        subproductos_filtrados.length ==
                                                            0 ||
                                                        aprobacion_id != 0
                                                    "
                                                >
                                                    <option
                                                        :value="null"
                                                        disabled
                                                    >
                                                        {{
                                                            subproductos_filtrados.length ==
                                                            0
                                                                ? "Ninguno"
                                                                : "Seleccione..."
                                                        }}
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            item, index
                                                        ) in subproductos_filtrados"
                                                        :key="index"
                                                        :value="item.id"
                                                    >
                                                        {{ item.subproducto }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <label class="label-title"
                                                    >TIPO</label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        !$v.frmAprobacion
                                                            .tipo_id.noZero
                                                    "
                                                    class="span-error-message"
                                                >
                                                    *
                                                </span>
                                                <select
                                                    class="form-control"
                                                    v-model.number="
                                                        frmAprobacion.tipo_id
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                >
                                                    <option :value="0" disabled>
                                                        Seleccione...
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            item, index
                                                        ) in tipos"
                                                        :key="index"
                                                        :value="item.id"
                                                    >
                                                        {{ item.tipo }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <label class="label-title"
                                                    >PERIODO PAGO</label
                                                >
                                                <select
                                                    class="form-control"
                                                    v-model="
                                                        frmAprobacion.periodo_pago
                                                    "
                                                    @change="
                                                        datos_calendario = [];
                                                        datos_cuota = [];
                                                        frmAprobacion.cuota = 0.0;
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                >
                                                    <option
                                                        value="DIARIO"
                                                        selected
                                                    >
                                                        DIARIO
                                                    </option>
                                                    <option value="SEMANAL">
                                                        SEMANAL
                                                    </option>
                                                    <option value="QUINCENAL">
                                                        QUINCENAL
                                                    </option>
                                                    <option value="PAGO_UNICO">
                                                        PAGO ÚNICO
                                                    </option>
                                                    <option value="MENSUAL">
                                                        MENSUAL
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <label class="label-title"
                                                    >PLAZO
                                                    {{
                                                        this.periodo_medicion(
                                                            this.frmAprobacion
                                                                .periodo_pago,
                                                        )
                                                    }}</label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        (!$v.frmAprobacion.plazo
                                                            .required ||
                                                            !$v.frmAprobacion
                                                                .plazo.noZero)
                                                    "
                                                    class="span-error-message"
                                                >
                                                    *
                                                </span>
                                                <input
                                                    type="number"
                                                    class="form-control center"
                                                    min="1"
                                                    step="1"
                                                    lang="en"
                                                    v-model.number="
                                                        frmAprobacion.plazo
                                                    "
                                                    name="plazo"
                                                    @change="Redondear"
                                                    style="
                                                        font-size: 15px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                    @focus="hidenav()"
                                                    @blur="shownav()"
                                                />
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <label class="label-title"
                                                    >TASA INTERÉS (%)</label
                                                >
                                                <span
                                                    v-if="
                                                        submited &&
                                                        (!$v.frmAprobacion
                                                            .tasa_interes
                                                            .required ||
                                                            !$v.frmAprobacion
                                                                .tasa_interes
                                                                .noZero)
                                                    "
                                                    class="span-error-message"
                                                    >*</span
                                                >
                                                <input
                                                    type="number"
                                                    class="form-control center"
                                                    min="1"
                                                    step="0.1"
                                                    lang="en"
                                                    v-model.number="
                                                        frmAprobacion.tasa_interes
                                                    "
                                                    name="tasa_interes"
                                                    @change="Redondear"
                                                    style="
                                                        font-size: 15px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                    @focus="hidenav()"
                                                    @blur="shownav()"
                                                />
                                            </div>

                                            <div
                                                class="col-md-12 col-9 mt-2"
                                                v-if="
                                                    this.frmAprobacion
                                                        .periodo_pago !=
                                                    'PAGO_UNICO'
                                                "
                                            >
                                                <div class="input-group">
                                                    <div
                                                        class="input-group-prepend"
                                                    >
                                                        <div
                                                            class="input-group-text"
                                                            :class="[
                                                                frmAprobacion.es_especial
                                                                    ? 'input-especial'
                                                                    : '',
                                                            ]"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                id="chbEspecial"
                                                                v-model="
                                                                    frmAprobacion.es_especial
                                                                "
                                                                :disabled="
                                                                    aprobacion_id !=
                                                                    0
                                                                "
                                                            />
                                                        </div>
                                                        <label
                                                            class="input-group-text prepend-title"
                                                            :class="[
                                                                frmAprobacion.es_especial
                                                                    ? 'input-especial'
                                                                    : '',
                                                            ]"
                                                            for="chbEspecial"
                                                            style="
                                                                font-size: 13px;
                                                                height: 32px !important;
                                                            "
                                                            v-if="
                                                                frmAprobacion.es_especial
                                                            "
                                                        >
                                                            ESPECIAL
                                                        </label>
                                                        <label
                                                            class="input-group-text prepend-title"
                                                            style="
                                                                height: 32px !important;
                                                                font-size: 9px !important;
                                                            "
                                                            v-if="
                                                                frmAprobacion.es_especial
                                                            "
                                                        >
                                                            MORA
                                                            {{
                                                                periodo_pago !=
                                                                "DIARIO"
                                                                    ? " ADIC. S/"
                                                                    : null
                                                            }}
                                                        </label>
                                                    </div>

                                                    <div
                                                        class="input-group-append"
                                                    >
                                                        <label
                                                            class="input-group-text prepend-title"
                                                            :class="[
                                                                frmAprobacion.es_especial
                                                                    ? 'input-especial'
                                                                    : '',
                                                            ]"
                                                            for="chbEspecial"
                                                            style="
                                                                font-size: 13px;
                                                                height: 32px !important;
                                                            "
                                                            v-if="
                                                                !frmAprobacion.es_especial
                                                            "
                                                        >
                                                            ESPECIAL
                                                        </label>
                                                    </div>

                                                    <input
                                                        type="number"
                                                        class="form-control center"
                                                        min="0.5"
                                                        step="0.1"
                                                        lang="en"
                                                        v-model.number="
                                                            frmAprobacion.mora_adicional
                                                        "
                                                        name="mora_adicional"
                                                        @change="Redondear"
                                                        @focus="hidenav()"
                                                        @blur="shownav()"
                                                        style="
                                                            font-size: 15px;
                                                            font-weight: bolder;
                                                            color: var(
                                                                --colorAlto
                                                            );
                                                        "
                                                        v-if="
                                                            frmAprobacion.es_especial
                                                        "
                                                        :disabled="
                                                            aprobacion_id != 0
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                class="input-group col-md-12 mt-1"
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <div
                                                        class="input-group-text"
                                                        :class="[
                                                            frmAprobacion.con_dias_gracia
                                                                ? 'input-especial'
                                                                : '',
                                                        ]"
                                                        style="
                                                            font-size: 13px;
                                                            height: 32px !important;
                                                        "
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            id="chbDiasGracia"
                                                            v-model="
                                                                frmAprobacion.con_dias_gracia
                                                            "
                                                            :disabled="
                                                                aprobacion_id !=
                                                                0
                                                            "
                                                        />
                                                    </div>
                                                </div>

                                                <div class="input-group-append">
                                                    <label
                                                        class="input-group-text prepend-title"
                                                        :class="[
                                                            frmAprobacion.con_dias_gracia
                                                                ? 'input-especial'
                                                                : '',
                                                        ]"
                                                        for="chbDiasGracia"
                                                        style="
                                                            font-size: 13px;
                                                            height: 32px !important;
                                                        "
                                                    >
                                                        DÍAS DE GRACIA
                                                    </label>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="form-control center bolder"
                                                    :value="
                                                        parseInt(
                                                            frmAprobacion.dias_gracia_ci,
                                                        ) +
                                                        parseInt(
                                                            frmAprobacion.dias_gracia_si,
                                                        )
                                                    "
                                                    disabled
                                                />
                                            </div>
                                            <div
                                                class="input-group mt-1 col-md-6 col-7"
                                                v-if="
                                                    periodo_pago !=
                                                        'PAGO_UNICO' &&
                                                    frmAprobacion.con_dias_gracia
                                                "
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <label
                                                        class="input-group-text prepend-title gracia-ci"
                                                        for="chbDiasGraciaAprobacion"
                                                        v-if="
                                                            windowWidth >= 900
                                                        "
                                                    >
                                                        CON INTERÉS
                                                    </label>
                                                </div>

                                                <input
                                                    type="number"
                                                    class="form-control center"
                                                    min="0"
                                                    :max="maximo_dias_gracia"
                                                    name="dias_gracia_ci"
                                                    @change="Redondear"
                                                    v-model.number="
                                                        frmAprobacion.dias_gracia_ci
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                    @focus="hidenav()"
                                                    @blur="shownav()"
                                                />
                                            </div>

                                            <div
                                                class="input-group mt-1 col-md-6 col-7"
                                                v-if="
                                                    periodo_pago !=
                                                        'PAGO_UNICO' &&
                                                    frmAprobacion.con_dias_gracia
                                                "
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <label
                                                        class="input-group-text prepend-title gracia-si"
                                                        for="chbDiasGraciaAprobacion"
                                                        v-if="
                                                            windowWidth >= 900
                                                        "
                                                    >
                                                        SIN INTERÉS
                                                    </label>
                                                </div>

                                                <input
                                                    type="number"
                                                    class="form-control center"
                                                    min="0"
                                                    :max="maximo_dias_gracia"
                                                    name="dias_gracia_si"
                                                    @change="Redondear"
                                                    v-model.number="
                                                        frmAprobacion.dias_gracia_si
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                    @focus="hidenav()"
                                                    @blur="shownav()"
                                                />
                                            </div>

                                            <div class="col-md-12 text-center">
                                                <button
                                                    class="btn btn-action btn-icon-split mt-3"
                                                    title="Genera calendario de pagos"
                                                    @click="
                                                        CronogramaAprobacion
                                                    "
                                                    v-if="aprobacion_id == 0"
                                                >
                                                    <span
                                                        class="icon text-white"
                                                    >
                                                        <i
                                                            class="far fa-calendar-alt"
                                                        ></i
                                                    ></span>
                                                    <span class="text"
                                                        >GENERAR
                                                        CALENDARIO</span
                                                    >
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="aprobacion"
                                role="tabpanel"
                                aria-labelledby="aprobacion-tab"
                            >
                                <div class="form-row">
                                    <div class="form-group col-md-9 col-8">
                                        <label class="label-title"
                                            >TITULAR</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="nombre_completo_titular"
                                            style="font-weight: bolder"
                                            :disabled="true"
                                        />
                                    </div>

                                    <div class="form-group col-md-3 col-4">
                                        <label class="label-title center"
                                            >ASESOR</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="
                                                datos_cliente.usuario_asesor
                                            "
                                            style="font-weight: bolder"
                                            :disabled="true"
                                        />
                                    </div>

                                    <div class="form-group col-md-6 col-6">
                                        <label class="label-title"
                                            >MONTO
                                            <span
                                                class="text"
                                                style="
                                                    font-size: 15px;
                                                    font-weight: bolder;
                                                    color: var(--colorAlto);
                                                "
                                                >S/
                                                {{
                                                    this.roundTo(
                                                        frmAprobacion.monto,
                                                        2,
                                                    )
                                                }}</span
                                            ></label
                                        >
                                    </div>

                                    <div class="form-group col-md-6 col-6">
                                        <label class="text1 label-title"
                                            >CUOTA CALCULADA
                                            <span
                                                class="text"
                                                style="
                                                    font-size: 15px;
                                                    font-weight: bolder;
                                                    color: var(--colorAlto);
                                                "
                                                >S/
                                                {{
                                                    this.roundTo(
                                                        frmAprobacion.cuota,
                                                        2,
                                                    )
                                                }}</span
                                            ></label
                                        >
                                        <label class="text2 label-title"
                                            >CUOTA CAL.
                                            <span
                                                class="text"
                                                style="
                                                    font-size: 15px;
                                                    font-weight: bolder;
                                                    color: var(--colorAlto);
                                                "
                                                >S/
                                                {{
                                                    this.roundTo(
                                                        frmAprobacion.cuota,
                                                        2,
                                                    )
                                                }}</span
                                            ></label
                                        >
                                    </div>

                                    <div
                                        class="form-row col-md-12"
                                        style="
                                            background: var(
                                                --verdeClaroEmpresarial
                                            );
                                        "
                                    >
                                        <div class="form-group col-md-4 col-4">
                                            <label class="label-title"
                                                >PERIODO:
                                                <span
                                                    class="text"
                                                    style="
                                                        font-size: 11px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    {{
                                                        frmAprobacion.periodo_pago
                                                    }}</span
                                                ></label
                                            >
                                        </div>
                                        <div class="form-group col-md-4 col-4">
                                            <label class="label-title"
                                                >PLAZO:
                                                <span
                                                    class="text"
                                                    style="
                                                        text-transform: lowercase;
                                                        font-size: 11px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    {{
                                                        this.roundTo(
                                                            frmAprobacion.plazo,
                                                            0,
                                                        ) +
                                                        " " +
                                                        periodo_medicion(
                                                            frmAprobacion.periodo_pago,
                                                        )
                                                    }}</span
                                                ></label
                                            >
                                        </div>
                                        <div class="form-group col-md-4 col-4">
                                            <label class="label-title"
                                                >TASA INTERÉS:
                                                <span
                                                    class="text"
                                                    style="
                                                        font-size: 11px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    {{
                                                        this.roundTo(
                                                            frmAprobacion.tasa_interes,
                                                            2,
                                                        )
                                                    }}
                                                    %</span
                                                ></label
                                            >
                                        </div>
                                    </div>
                                    <div class="form-row col-md-12">
                                        <div class="form-group col-md-2 col-4">
                                            <label class="label-title"
                                                >COBRANZA EN
                                            </label>
                                        </div>
                                        <div class="form-group col-md-2 col-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="forma_pago"
                                                    id="rdbOficina"
                                                    value="OFICINA"
                                                    v-model="
                                                        frmAprobacion.forma_pago
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                />
                                                <label
                                                    class="label-title"
                                                    for="rdbOficina"
                                                >
                                                    OFICINA
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-2 col-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="forma_pago"
                                                    id="rdbNegocio"
                                                    value="NEGOCIO"
                                                    v-model="
                                                        frmAprobacion.forma_pago
                                                    "
                                                    :disabled="
                                                        aprobacion_id != 0
                                                    "
                                                />
                                                <label
                                                    class="label-title"
                                                    for="rdbNegocio"
                                                >
                                                    NEGOCIO
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title"
                                            >EXPEDIENTE</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            style="
                                                font: size 13px;
                                                font-weight: bolder;
                                                color: var(--colorAlto);
                                            "
                                            :value="
                                                datos_cliente.codigo_expediente ==
                                                null
                                                    ? 'NUEVO'
                                                    : datos_cliente.codigo_expediente
                                            "
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title"
                                            >SECTOR</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="sector_aprobacion"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title"
                                            >PRODUCTO</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="producto_aprobacion"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title"
                                            >SUBPRODUCTO</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="subproducto_aprobacion"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title">TIPO</label>
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="tipo_aprobacion"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title"
                                            >PROMOTOR</label
                                        >
                                        <span
                                            v-if="
                                                submited &&
                                                !$v.frmAprobacion.promotor_id
                                                    .noZero
                                            "
                                            class="span-error-message"
                                        >
                                            *
                                        </span>
                                        <select
                                            class="form-control center"
                                            v-model.number="
                                                frmAprobacion.promotor_id
                                            "
                                            :disabled="aprobacion_id != 0"
                                        >
                                            <option :value="0" disabled>
                                                Seleccione...
                                            </option>
                                            <option
                                                v-for="(
                                                    item, index
                                                ) in lista_promotores"
                                                :key="index"
                                                :value="item.dni"
                                            >
                                                {{ item.usuario }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-8 col-12">
                                        <label class="label-title"
                                            >COMENTARIO</label
                                        >
                                        <span
                                            v-if="
                                                submited &&
                                                !$v.frmAprobacion
                                                    .comentario_aprobacion
                                                    .required
                                            "
                                            class="span-error-message"
                                        >
                                            *
                                        </span>
                                        <textarea
                                            class="form-control text-row mayus"
                                            rows="2"
                                            v-model="
                                                frmAprobacion.comentario_aprobacion
                                            "
                                            :disabled="aprobacion_id != 0"
                                            @focus="hidenav()"
                                            @blur="shownav()"
                                        ></textarea>
                                    </div>
                                    <div
                                        class="form-group col-md-4 text-center"
                                        style="
                                            border: 1px solid white;
                                            border-radius: 20px;
                                        "
                                        :style="{
                                            background:
                                                tiempo_inactividad <
                                                minimo_inactividad
                                                    ? '#e8f0f4 !important'
                                                    : 'var(--verdeClaroEmpresarial) !important',
                                        }"
                                        v-if="datos_inactividad.inactivo"
                                    >
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="chbConsiderarUno"
                                                v-model="
                                                    frmAprobacion.considerado_uno
                                                "
                                                :disabled="
                                                    aprobacion_id != 0 ||
                                                    tiempo_inactividad <
                                                        minimo_inactividad
                                                "
                                            />
                                            <label
                                                class="label-title"
                                                for="chbConsiderarUno"
                                                style="
                                                    color: var(
                                                        --colorAlto
                                                    ) !important;
                                                "
                                            >
                                                CONSIDERAR CRÉDITO COMO NÚMERO 1
                                            </label>
                                        </div>
                                        <div class="text-center">
                                            <label class="label-title">
                                                Fecha inactivo:
                                                {{
                                                    datos_inactividad.fecha_inactividad
                                                }}
                                            </label>
                                            <br />
                                            <label class="label-title"
                                                >Tiempo transcurrido:
                                                {{
                                                    tiempo_inactividad +
                                                    " meses"
                                                }}</label
                                            >
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="text-right">
                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="GenerarConstancia()"
                                            v-if="
                                                aprobacion_id != 0 &&
                                                frmAprobacion.prendario
                                            "
                                        >
                                            <span class="icon text-white">
                                                <i class="fas fa-file-word"></i
                                            ></span>
                                            <span class="text">CONSTANCIA</span>
                                        </button>
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="GenerarDeclaracion()"
                                            v-if="
                                                aprobacion_id != 0 &&
                                                frmAprobacion.prendario
                                            "
                                        >
                                            <span class="icon text-white">
                                                <i class="fas fa-file-word"></i
                                            ></span>
                                            <span class="text"
                                                >DECLARACIÓN</span
                                            >
                                        </button>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="Registrar('APROBADO')"
                                            v-if="aprobacion_id == 0"
                                        >
                                            <span class="icon text-white">
                                                <i class="fas fa-check"></i
                                            ></span>
                                            <span class="text">APROBAR</span>
                                        </button>
                                        <button
                                            class="btn btn-danger btn-icon-split"
                                            @click="Registrar('DESAPROBADO')"
                                            v-if="aprobacion_id == 0"
                                        >
                                            <span class="icon text-white">
                                                <i class="fas fa-times"></i
                                            ></span>
                                            <span class="text">DESAPROBAR</span>
                                        </button>
                                        <button
                                            class="btn btn-cancel btn-icon-split"
                                            @click="Imprimir"
                                            v-if="aprobacion_id != 0"
                                        >
                                            <span class="icon text-white">
                                                <i class="fa fa-print"></i
                                            ></span>
                                            <span class="text">IMPRIMIR</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="prendas"
                                role="tabpanel"
                                aria-labelledby="prendas-tab"
                            >
                                <DataTable
                                    :value="datos_prendas"
                                    :scrollable="true"
                                    scrollDirection="both"
                                    scrollHeight="200px"
                                    showGridlines
                                    stripedRows
                                >
                                    <Column
                                        field="seleccionar"
                                        header="CHECK"
                                        :styles="{
                                            width: '40px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data, index }">
                                            <div class="page__toggle">
                                                <label class="toggle">
                                                    <input
                                                        class="toggle__input"
                                                        type="checkbox"
                                                        :id="
                                                            'chbPrenda_' + index
                                                        "
                                                        :value="data.id"
                                                        v-model="
                                                            datos_propuesta.prendas
                                                        "
                                                        :disabled="
                                                            propuesta_id != 0
                                                        "
                                                    />
                                                    <span class="toggle__label">
                                                        <span
                                                            class="toggle__text"
                                                        ></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </template>
                                    </Column>
                                    <Column
                                        field="ver"
                                        header="VER"
                                        :styles="{
                                            width: '40px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            <button
                                                class="btn btn-cancel btn-icon-split"
                                                title="Ver"
                                                @click="DetallePrenda(data)"
                                            >
                                                <span class="icon text-white">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </button>
                                        </template>
                                    </Column>
                                    <Column
                                        field="descripcion"
                                        header="DESCRIPCIÓN"
                                        :styles="{
                                            width: '200px',
                                            justifyContent: 'left',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="disponible"
                                        header="DISP."
                                        :styles="{
                                            width: '40px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">{{
                                            data.disponible == 1 ? "SI" : "NO"
                                        }}</template>
                                    </Column>
                                    <Column
                                        field="fecha_registro"
                                        header="FECHA_REGISTRO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{
                                                JSON.parse(data.datos_creacion)
                                                    .fecha
                                            }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="usuario_registro"
                                        header="USUARIO_REGISTRO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <template #empty>
                                        No hay PRENDAS disponibles.</template
                                    >
                                </DataTable>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="vinculado-de"
                                role="tabpanel"
                                aria-labelledby="vinculado-de-tab"
                            >
                                <DataTable
                                    :value="creditos_vinculados_de"
                                    :scrollable="true"
                                    scrollDirection="both"
                                    scrollHeight="200px"
                                    selectionMode="single"
                                    showGridlines
                                    stripedRows
                                    @row-dblclick="VerHistorial"
                                >
                                    <Column
                                        field="orden"
                                        header="N°"
                                        :styles="{
                                            width: '40px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ index }">
                                            {{ index + 1 }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="codigo_expediente"
                                        header="EXPED."
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="vinculo"
                                        header="VÍNCULO"
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="cliente"
                                        header="CLIENTE"
                                        :styles="{
                                            width: '250px',
                                            justifyContent: 'left',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{
                                                data.apellido_paterno +
                                                " " +
                                                data.apellido_materno +
                                                " " +
                                                data.nombres
                                            }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="capital_total"
                                        header="MONTO"
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'right',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            S/
                                            {{ roundTo(data.capital_total, 2) }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="plazo"
                                        header="PLAZO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{
                                                roundTo(data.plazo, 0) +
                                                " " +
                                                periodo_medicion(
                                                    data.periodo_pago,
                                                )
                                            }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="tipo"
                                        header="TIPO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="usuario_asesor"
                                        header="ASESOR"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="fecha_desembolso"
                                        header="FECHA_DESEMBOLSO"
                                        :styles="{
                                            width: '120px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">{{
                                            JSON.parse(data.datos_creacion)
                                                .fecha
                                        }}</template>
                                    </Column>
                                    <template #empty>
                                        No hay CRÉDITOS vinculados.</template
                                    >
                                </DataTable>
                                <hr />

                                <div class="form-check text-right">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="chbConfirmarVinculadoDe"
                                        v-model="revision_vinculado_de"
                                        :disabled="aprobacion_id != 0"
                                    />
                                    <label
                                        class="label-title"
                                        for="chbConfirmarVinculadoDe"
                                    >
                                        HE REVISADO LOS CRÉDITOS
                                    </label>
                                </div>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="vinculado-a"
                                role="tabpanel"
                                aria-labelledby="vinculado-a-tab"
                            >
                                <DataTable
                                    :value="creditos_vinculados_a"
                                    :scrollable="true"
                                    scrollDirection="both"
                                    scrollHeight="200px"
                                    selectionMode="single"
                                    showGridlines
                                    @row-dblclick="VerHistorial"
                                    stripedRows
                                >
                                    <Column
                                        field="orden"
                                        header="N°"
                                        :styles="{
                                            width: '40px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ index }">
                                            {{ index + 1 }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="codigo_expediente"
                                        header="EXPED."
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="vinculo"
                                        header="VÍNCULO"
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="cliente"
                                        header="CLIENTE"
                                        :styles="{
                                            width: '250px',
                                            justifyContent: 'left',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{
                                                data.apellido_paterno +
                                                " " +
                                                data.apellido_materno +
                                                " " +
                                                data.nombres
                                            }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="capital_total"
                                        header="MONTO"
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'right',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            S/
                                            {{ roundTo(data.capital_total, 2) }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="plazo"
                                        header="PLAZO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{
                                                roundTo(data.plazo, 0) +
                                                " " +
                                                periodo_medicion(
                                                    data.periodo_pago,
                                                )
                                            }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="tipo"
                                        header="TIPO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="usuario_asesor"
                                        header="ASESOR"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="fecha_desembolso"
                                        header="FECHA_DESEMBOLSO"
                                        :styles="{
                                            width: '120px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">{{
                                            JSON.parse(data.datos_creacion)
                                                .fecha
                                        }}</template>
                                    </Column>
                                    <template #empty>
                                        No hay CRÉDITOS vinculados.</template
                                    >
                                </DataTable>

                                <hr />

                                <div class="form-check text-right">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="chbConfirmarVinculadoA"
                                        v-model="revision_vinculado_a"
                                        :disabled="aprobacion_id != 0"
                                    />
                                    <label
                                        class="label-title"
                                        for="chbConfirmarVinculadoA"
                                    >
                                        HE REVISADO LOS CRÉDITOS
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <mdlDetallePrenda ref="mdlDetallePrenda" :agencia_id="agencia_id">
            </mdlDetallePrenda>
            <rptEvaluacionBienes
                ref="rptEvaluacionBienes"
            ></rptEvaluacionBienes>
            <rptConstanciaGarantia
                ref="rptConstanciaGarantia"
            ></rptConstanciaGarantia>
        </div>
    </layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import mdlDetallePrenda from "@/Pages/Creditos/Clientes/Components/mdlDetallePrenda.vue";
import rptEvaluacionBienes from "@/Pages/Creditos/Creditos/Reports/rptEvaluacionBienes.vue";
import rptConstanciaGarantia from "@/Pages/Creditos/Creditos/Reports/rptConstanciaGarantia.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import { required } from "vuelidate/lib/validators";

const noZero = (value) => value != 0;
const noNull = (value) => value != null;
export default {
    components: {
        layout,
        headerClose,
        mdlDetallePrenda,
        rptEvaluacionBienes,
        rptConstanciaGarantia,
        DataTable,
        Column,
    },
    props: {
        agencia_id: Number,
        propuesta_id: Number,
        aprobacion_id: Number,
        datos_propuesta: Object,
        datos_cliente: Object,
        datos_prendas: Array,
        datos_pariente: Object,
        datos_aval: Object,
        datos_pariente_aval: Object,
        creditos_vinculados_de: Array,
        creditos_vinculados_a: Array,
        datos_negocio: Object,
        datos_aprobacion: Object,
        sectores: Array,
        productos: Array,
        subproductos: Array,
        tipos: Array,
        garantias: Array,
        usuarios: Array,
        datos_inactividad: Object,
        minimo_inactividad: Number,
    },
    data() {
        return {
            windowWidth: window.innerWidth,
            submited: false,
            cronograma_propuesta: [],
            cronograma_aprobacion: [],
            subproductos_filtrados: [],
            revision_vinculado_de:
                this.aprobacion_id != 0
                    ? true
                    : this.creditos_vinculados_de.length > 0
                      ? false
                      : true,
            revision_vinculado_a:
                this.aprobacion_id != 0
                    ? true
                    : this.creditos_vinculados_a.length > 0
                      ? false
                      : true,
            frmAprobacion: {
                propuesta_id: null,
                cliente_id: null,
                codigo_expediente: null,
                monto: null,
                cuota: null,
                sector_id: null,
                producto_id: null,
                subproducto_id: null,
                tipo_id: null,
                periodo_pago: null,
                es_especial: false,
                mora_adicional: 0,
                es_especial: false,
                plazo: null,
                tasa_interes: null,
                con_dias_gracia: false,
                dias_gracia: 0,
                dias_gracia_ci: 0,
                dias_gracia_si: 0,
                con_interes_gracia: false,
                promotor_id: null,
                considerado_uno: false,
                comentario_aprobacion: null,
                prendario: false,
                fecha_aprobacion: null,
                ciiu_codigo: this.datos_negocio.ciiu_codigo,
            },
        };
    },
    validations() {
        if (this.subproductos_filtrados.length != 0) {
            return {
                frmAprobacion: {
                    monto: { required, noZero },
                    sector_id: { noZero },
                    producto_id: { noZero },
                    subproducto_id: { noNull },
                    tipo_id: { noZero },
                    plazo: { required, noZero },
                    tasa_interes: { required, noZero },
                    promotor_id: { noZero },
                    comentario_aprobacion: { required },
                },
                revision_vinculado_de: { checked: (value) => value === true },
                revision_vinculado_a: { checked: (value) => value === true },
            };
        } else {
            return {
                frmAprobacion: {
                    monto: { required, noZero },
                    sector_id: { noZero },
                    producto_id: { noZero },
                    tipo_id: { noZero },
                    plazo: { required, noZero },
                    tasa_interes: { required, noZero },
                    promotor_id: { noZero },
                    comentario_aprobacion: { required },
                },
                revision_vinculado_de: { checked: (value) => value === true },
                revision_vinculado_a: { checked: (value) => value === true },
            };
        }
    },
    computed: {
        nombre_completo_titular() {
            return (
                this.datos_cliente.apellido_paterno +
                " " +
                this.datos_cliente.apellido_materno +
                " " +
                this.datos_cliente.nombres
            );
        },
        nombre_completo_pariente() {
            if (this.datos_pariente == null) {
                return "-";
            } else {
                return (
                    this.datos_pariente.apellido_paterno +
                    " " +
                    this.datos_pariente.apellido_materno +
                    " " +
                    this.datos_pariente.nombres +
                    " - " +
                    this.datos_pariente.dni
                );
            }
        },
        nombre_completo_aval() {
            if (this.datos_aval == null) {
                return "-";
            } else {
                return (
                    this.datos_aval.apellido_paterno +
                    " " +
                    this.datos_aval.apellido_materno +
                    " " +
                    this.datos_aval.nombres +
                    " - " +
                    this.datos_aval.dni
                );
            }
        },
        nombre_completo_pariente_aval() {
            if (this.datos_pariente_aval == null) {
                return "-";
            } else {
                return (
                    this.datos_pariente_aval.apellido_paterno +
                    " " +
                    this.datos_pariente_aval.apellido_materno +
                    " " +
                    this.datos_pariente_aval.nombres +
                    " - " +
                    this.datos_pariente_aval.dni
                );
            }
        },
        negocio() {
            if (this.datos_negocio.nombre == null) {
                return "-";
            } else {
                return (
                    this.datos_negocio.nombre +
                    " - " +
                    this.datos_negocio.actividad
                );
            }
        },
        sector_aprobacion() {
            if (this.frmAprobacion.sector_id) {
                return this.sectores.filter(
                    (item) => item.id == this.frmAprobacion.sector_id,
                )[0].sector;
            }
            return "-";
        },
        producto_aprobacion() {
            if (this.frmAprobacion.producto_id) {
                return this.productos.filter(
                    (item) => item.id == this.frmAprobacion.producto_id,
                )[0].producto;
            }
            return "-";
        },
        subproducto_aprobacion() {
            if (this.frmAprobacion.subproducto_id) {
                return this.subproductos.filter(
                    (item) => item.id == this.frmAprobacion.subproducto_id,
                )[0].subproducto;
            }
            return "-";
        },
        tipo_aprobacion() {
            if (this.frmAprobacion.producto_id) {
                return this.tipos.filter(
                    (item) => item.id == this.frmAprobacion.tipo_id,
                )[0].tipo;
            }

            return "-";
        },
        lista_promotores() {
            let agencia_id = this.datos_cliente.agencia_id;
            return this.usuarios.filter(
                (item) => item.agencia_id == agencia_id,
            );
        },
        considerar_dias_gracia() {
            return this.frmAprobacion.con_dias_gracia;
        },
        maximo_dias_gracia() {
            if (
                this.$page.props.user_permissions.permisos.includes(
                    "CREDITOS_CREDITO/INCREMENTAR_DIAS_GRACIA",
                )
            ) {
                return 30;
            } else {
                return 2;
            }
        },
        periodo_pago() {
            return this.frmAprobacion.periodo_pago;
        },
        tiempo_inactividad() {
            if (this.datos_inactividad.inactivo) {
                let fecha_propuesta = moment(
                    this.datos_propuesta.fecha_propuesta,
                );
                let fecha_inactividad =
                    this.datos_inactividad.fecha_inactividad.substring(0, 10);
                let meses = fecha_propuesta.diff(fecha_inactividad, "months");
                return meses;
            } else {
                return 0;
            }
        },
        producto() {
            return this.frmAprobacion.producto_id;
        },
        es_especial() {
            return this.frmAprobacion.es_especial;
        },
    },
    watch: {
        considerar_dias_gracia(value) {
            if (value) {
                if (this.aprobacion_id != 0) {
                    this.frmAprobacion.dias_gracia_ci =
                        this.datos_aprobacion.dias_gracia_ci;
                    this.frmAprobacion.dias_gracia_si =
                        this.datos_aprobacion.dias_gracia_si;
                }
            } else {
                this.frmAprobacion.dias_gracia_ci = 0;
                this.frmAprobacion.dias_gracia_si = 0;
            }
        },
        periodo_pago(value) {
            if (value === "PAGO_UNICO") {
                this.frmAprobacion.es_especial = false;
                this.frmAprobacion.con_dias_gracia = false;
                this.frmAprobacion.dias_gracia = 0;
            }
        },
        producto(value) {
            let producto_id = value;

            if (producto_id != 0) {
                let producto = this.productos.filter(
                    (item) => item.id == producto_id,
                )[0].producto;

                if (producto == "CRÉDITO PRENDARIO") {
                    let garantia_id = this.garantias.filter(
                        (item) => item.garantia == "PRENDARIO",
                    )[0].id;

                    this.frmAprobacion.garantia_id = garantia_id;
                    this.frmAprobacion.prendario = true;
                }
            }
        },

        es_especial(value) {
            if (value) {
                if (this.aprobacion_id == 0) {
                    this.frmAprobacion.mora_adicional = this.roundTo(0.5, 2);
                }
            } else {
                this.frmAprobacion.mora_adicional = this.roundTo(0, 2);
            }
        },
    },
    mounted() {
        window.addEventListener("resize", () => {
            this.windowWidth = window.innerWidth;
        });

        this.RellenarPropuesta();

        this.CronogramaPropuesta();
        this.ListarSubproductos();

        if (this.aprobacion_id != 0) {
            this.RellenarAprobacion();
            this.CronogramaAprobacion();
        }
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
        Redondear(e) {
            let valor = 1;
            let numero_decimales = 2;

            if (e.target.value && e.target.value >= 1) {
                valor = e.target.value;
            }

            if (e.target.name == "monto") {
                if (
                    parseFloat(this.frmAprobacion.monto) <=
                    parseFloat(this.datos_cliente.monto_maximo)
                ) {
                    this.frmAprobacion.monto = this.roundTo(
                        valor,
                        numero_decimales,
                    );
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "¡Ups!",
                        text: "El monto propuesto es mayor al monto máximo del cliente",
                        allowOutsideClick: true,
                    });
                    this.frmAprobacion.monto = this.roundTo(
                        this.datos_cliente.monto_maximo,
                        numero_decimales,
                    );
                }
            } else if (e.target.name == "plazo") {
                this.frmAprobacion.plazo = this.roundTo(valor, 0);
            } else if (e.target.name == "tasa_interes") {
                this.frmAprobacion.tasa_interes = this.roundTo(
                    valor,
                    numero_decimales,
                );
            } else if (e.target.name == "mora_adicional") {
                valor = 0.5;

                if (e.target.value && e.target.value >= 0.5) {
                    valor = e.target.value;
                }

                this.frmAprobacion.mora_adicional = this.roundTo(
                    valor,
                    numero_decimales,
                );
            } else if (e.target.name == "dias_gracia_ci") {
                valor = 0;

                if (
                    e.target.value &&
                    e.target.value <= this.maximo_dias_gracia
                ) {
                    valor = e.target.value;
                }

                this.frmAprobacion.dias_gracia_ci = this.roundTo(valor, 0);
            } else if (e.target.name == "dias_gracia_si") {
                valor = 0;
                if (
                    e.target.value &&
                    e.target.value <= this.maximo_dias_gracia
                ) {
                    valor = e.target.value;
                }

                this.frmAprobacion.dias_gracia_si = this.roundTo(valor, 0);
            }
        },

        RellenarPropuesta() {
            let propuesta = this.datos_propuesta;
            let cliente = this.datos_cliente;
            this.frmAprobacion.propuesta_id = propuesta.id;
            this.frmAprobacion.cliente_id = propuesta.cliente_id;
            this.frmAprobacion.agencia_id = propuesta.agencia_id;
            this.frmAprobacion.codigo_expediente = cliente.codigo_expediente;
            this.frmAprobacion.monto = this.roundTo(propuesta.monto, 2);
            this.frmAprobacion.cuota = parseFloat(this.roundTo(0, 2));
            this.frmAprobacion.sector_id = propuesta.sector_id;
            this.frmAprobacion.producto_id = propuesta.producto_id;
            this.frmAprobacion.subproducto_id = propuesta.subproducto_id;
            this.frmAprobacion.tipo_id = propuesta.tipo_id;
            this.frmAprobacion.periodo_pago = propuesta.periodo_pago;
            this.frmAprobacion.es_especial = propuesta.es_especial;
            this.frmAprobacion.mora_adicional = propuesta.mora_adicional;
            this.frmAprobacion.plazo = this.roundTo(propuesta.plazo, 0);
            this.frmAprobacion.tasa_interes = this.roundTo(
                propuesta.tasa_interes,
                2,
            );
            // this.frmAprobacion.con_dias_gracia = propuesta.con_dias_gracia;
            // this.frmAprobacion.dias_gracia = propuesta.dias_gracia;
            // this.frmAprobacion.con_interes_gracia = false;
            this.frmAprobacion.promotor_id = propuesta.promotor_id;
            this.frmAprobacion.fecha_aprobacion = propuesta.fecha_propuesta;
            if (propuesta.pago_oficina == 1) {
                this.frmAprobacion.forma_pago = "OFICINA";
            } else if (propuesta.pago_oficina == 0) {
                this.frmAprobacion.forma_pago = "NEGOCIO";
            }
            this.comentario_aprobacion = null;
        },
        RellenarAprobacion() {
            let aprobacion = this.datos_aprobacion;
            let cliente = this.datos_cliente;

            this.frmAprobacion.propuesta_id = aprobacion.id;
            this.frmAprobacion.cliente_id = cliente.id;
            this.frmAprobacion.agencia_id = cliente.agencia_id;
            this.frmAprobacion.codigo_expediente = cliente.codigo_expediente;
            this.frmAprobacion.monto = this.roundTo(aprobacion.monto, 2);
            this.frmAprobacion.cuota = parseFloat(this.roundTo(0, 2));
            this.frmAprobacion.sector_id = aprobacion.sector_id;
            this.frmAprobacion.producto_id = aprobacion.producto_id;
            this.frmAprobacion.subproducto_id = aprobacion.subproducto_id;
            this.frmAprobacion.tipo_id = aprobacion.tipo_id;
            this.frmAprobacion.periodo_pago = aprobacion.periodo_pago;
            this.frmAprobacion.es_especial = aprobacion.es_especial;
            this.frmAprobacion.mora_adicional = aprobacion.mora_adicional;
            this.frmAprobacion.plazo = this.roundTo(aprobacion.plazo, 0);
            this.frmAprobacion.tasa_interes = this.roundTo(
                aprobacion.tasa_interes,
                2,
            );
            this.frmAprobacion.con_dias_gracia = aprobacion.con_dias_gracia;
            this.frmAprobacion.dias_gracia_ci = aprobacion.dias_gracia_ci;
            this.frmAprobacion.dias_gracia_si = aprobacion.dias_gracia_si;
            this.frmAprobacion.promotor_id = aprobacion.promotor_id;
            this.frmAprobacion.fecha_aprobacion = aprobacion.fecha_aprobacion;
            if (aprobacion.pago_oficina == 1) {
                this.frmAprobacion.forma_pago = "OFICINA";
            } else if (aprobacion.pago_oficina == 0) {
                this.frmAprobacion.forma_pago = "NEGOCIO";
            }
            this.frmAprobacion.considerado_uno = aprobacion.considerado_uno;
            this.frmAprobacion.comentario_aprobacion =
                aprobacion.comentario_aprobacion;
        },
        ListarSubproductos() {
            let producto_id = this.frmAprobacion.producto_id;

            this.subproductos_filtrados = this.subproductos.filter(
                (item) => item.producto_id == producto_id,
            );

            if (this.subproductos_filtrados == 0) {
                this.frmAprobacion.subproducto_id = null;
            }
        },
        CronogramaPropuesta() {
            let self = this;
            let data = new FormData();
            data.append("agencia_id", this.agencia_id);
            data.append("monto", this.datos_propuesta.monto);
            data.append("plazo", this.datos_propuesta.plazo);
            data.append("tasa_interes", this.datos_propuesta.tasa_interes);
            data.append("periodo_pago", this.datos_propuesta.periodo_pago);
            data.append(
                "fecha_desembolso",
                this.datos_propuesta.fecha_propuesta,
            );
            if (this.datos_propuesta.con_dias_gracia) {
                data.append("dias_gracia", this.datos_propuesta.dias_gracia);
            } else {
                data.append("dias_gracia", 0);
            }

            data.append("gracia_con_interes", false);
            data.append(
                "fecha_desembolso",
                this.datos_propuesta.fecha_propuesta,
            );
            axios
                .post(route("cre.calcular_cronograma.sin_redondeo"), data)
                .then(function (response) {
                    self.cronograma_propuesta = response.data.datos_calendario;
                });
        },
        async CronogramaAprobacion() {
            if (
                this.frmAprobacion.con_dias_gracia &&
                this.frmAprobacion.dias_gracia_ci +
                    this.frmAprobacion.dias_gracia_si ==
                    0
            ) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Debe ingresar al menos un día de gracia",
                    allowOutsideClick: true,
                });
                return false;
            }

            let data = new FormData();

            data.append("agencia_id", this.agencia_id);
            data.append("monto", this.frmAprobacion.monto);
            data.append("plazo", this.frmAprobacion.plazo);
            data.append("tasa_interes", this.frmAprobacion.tasa_interes);
            data.append("periodo_pago", this.frmAprobacion.periodo_pago);
            data.append("con_dias_gracia", this.frmAprobacion.con_dias_gracia);
            data.append("es_especial", this.frmAprobacion.es_especial);

            if (this.frmAprobacion.con_dias_gracia) {
                data.append(
                    "dias_gracia_ci",
                    this.frmAprobacion.dias_gracia_ci,
                );
                data.append(
                    "dias_gracia_si",
                    this.frmAprobacion.dias_gracia_si,
                );
            }

            data.append(
                "fecha_desembolso",
                this.datos_propuesta.fecha_propuesta,
            );

            // this.$inertia.post(route("cre.calcular_cronograma.sin_redondeo"), data);
            // return false;

            await axios
                .post(route("cre.calcular_cronograma.sin_redondeo"), data)
                .then((response) => {
                    this.cronograma_aprobacion = response.data.datos_calendario;
                    this.frmAprobacion.cuota =
                        response.data.datos_cuota.monto_cuota;
                });
        },
        DetallePrenda(item) {
            let mdlDetallePrenda = this.$refs.mdlDetallePrenda;

            mdlDetallePrenda.datos_prenda = item;

            $("#datos-prenda-tab").tab("show");
            $("#mdlDetallePrenda").css("display", "block");
        },
        VerHistorial(event) {
            let modulo = this.$refs.layout.$refs.mdlHistorialCrediticio;

            if (event == "CLIENTE") {
                modulo.agencia_id = this.frmAprobacion.agencia_id;
                modulo.cliente_id = this.frmAprobacion.cliente_id;
            } else {
                const credito = event.data;
                modulo.agencia_id = credito.agencia_id;
                modulo.cliente_id = credito.cliente_id;
            }
            modulo.ActualizarInformacion();
            return $("#mdlHistorialCrediticio").css("display", "block");
        },

        Registrar(modo) {
            this.CronogramaAprobacion();
            this.submited = true;

            let mensaje = "APROBAR";
            if (modo == "DESAPROBADO") {
                mensaje = "DESAPROBAR";
            } else {
                if (
                    this.frmAprobacion.con_dias_gracia &&
                    this.frmAprobacion.dias_gracia_ci +
                        this.frmAprobacion.dias_gracia_si ==
                        0
                ) {
                    Swal.fire({
                        icon: "warning",
                        title: "¡Ups!",
                        text: "Debe ingresar al menos un día de gracia",
                        allowOutsideClick: true,
                    });
                    return false;
                }
                if (this.$v.frmAprobacion.$invalid) {
                    Swal.fire({
                        icon: "warning",
                        title: "¡Ups!",
                        text: "Complete todos los campos",
                        allowOutsideClick: true,
                    });
                    return false;
                } else if (this.$v.revision_vinculado_de.$invalid) {
                    Swal.fire({
                        icon: "warning",
                        title: "¡Ups!",
                        text: "Confirme que reviso los créditos vinculados",
                        allowOutsideClick: true,
                    });
                    $("#vinculado-de-tab").tab("show");
                    return false;
                } else if (this.$v.revision_vinculado_a.$invalid) {
                    Swal.fire({
                        icon: "warning",
                        title: "¡Ups!",
                        text: "Confirme que reviso los créditos vinculados",
                        allowOutsideClick: true,
                    });
                    $("#vinculado-a-tab").tab("show");
                    return false;
                }
            }
            Swal.fire({
                icon: "question",
                text: "¿Desea " + mensaje + " la propuesta?",
                confirmButtonText: "Si",
                showCancelButton: true,
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = new FormData();
                    data.append(
                        "datos_prendas",
                        JSON.stringify(this.datos_prendas),
                    );
                    if (modo == "APROBADO") {
                        data.append("agencia_id", this.agencia_id);
                        data.append("modo", modo);
                        data.append(
                            "frmAprobacion",
                            JSON.stringify(this.frmAprobacion),
                        );
                        this.RutaGuardar(data);
                    } else if (modo == "DESAPROBADO") {
                        Swal.fire({
                            text: "MOTIVO DE LA DESAPROBACIÓN",
                            confirmButtonText: "Guardar",
                            showCancelButton: true,
                            cancelButtonText: "Cancelar",
                            allowOutsideClick: false,
                            input: "text",

                            customClass: {
                                input: "mayus",
                            },
                            inputValidator: (value) => {
                                if (!value) {
                                    return "*Obligatorio";
                                }
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                data.append("modo", modo);
                                data.append("agencia_id", this.agencia_id);
                                data.append(
                                    "propuesta_id",
                                    this.frmAprobacion.propuesta_id,
                                );
                                data.append(
                                    "comentario_desaprobacion",
                                    result.value,
                                );
                                this.RutaGuardar(data);
                            } else {
                                return false;
                            }
                        });
                    }
                }
            });
        },
        RutaGuardar(data) {
            // this.$inertia.post(route("cre.aprobacion.guardar"), data);
            // return false;

            this.$inertia.post(route("cre.aprobacion.guardar"), data, {
                preserveScroll: true,
                onStart: () => {
                    Swal.fire({
                        title: "GUARDANDO",
                        text: "Espere porfavor...",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "¡LISTO!",
                        allowOutsideClick: false,
                    });
                },
            });
        },
        async Imprimir() {
            let data = new FormData();
            data.append("datos_cliente", JSON.stringify(this.datos_cliente));
            data.append("datos_pariente", JSON.stringify(this.datos_pariente));
            data.append("datos_aval", JSON.stringify(this.datos_aval));
            data.append(
                "datos_pariente_aval",
                JSON.stringify(this.datos_pariente_aval),
            );
            data.append("datos_negocio", JSON.stringify(this.datos_negocio));
            data.append(
                "datos_propuesta",
                JSON.stringify(this.datos_propuesta),
            );
            data.append(
                "datos_aprobacion",
                JSON.stringify(this.datos_aprobacion),
            );

            Swal.fire({
                title: "GENERANDO",
                text: "Espere porfavor...",
                allowOutsideClick: false,
                didOpen: async () => {
                    // this.$inertia.post(route("cre.aprobacion.exportar"), data);
                    // return false;

                    Swal.showLoading();
                    await axios
                        .post(route("cre.aprobacion.exportar"), data)
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
            });
        },

        async GenerarDeclaracion() {
            let self = this;
            let rptEvaluacionBienes = this.$refs.rptEvaluacionBienes;

            let fecha_sistema = await this.$refs.layout.fecha_hora_actual(
                this.agencia_id,
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
                let cliente = self.nombre_completo_titular;
                let dni = self.datos_cliente.dni;
                let direccion = self.datos_cliente.direccion;
                let distrito = self.datos_cliente.distrito;
                let provincia = self.datos_cliente.provincia;
                let departamento = self.datos_cliente.departamento;

                let bienes_disponibles = [];

                self.datos_prendas.forEach((element) => {
                    self.datos_propuesta.prendas.forEach((element_1) => {
                        if (element.id == element_1) {
                            bienes_disponibles.push(element);
                        }
                    });
                });

                let orden = 0;
                bienes_disponibles.forEach((element) => {
                    orden += 1;
                    element.orden = orden;
                    if (element.modelo == null) {
                        element.modelo = "-";
                    }

                    if (element.serie == null) {
                        element.serie = "-";
                    }
                });

                fecha_actual =
                    fecha_actual[0].toUpperCase() + fecha_actual.slice(1);

                let data = {
                    cliente: cliente,
                    dni: dni,
                    direccion: direccion,
                    distrito: distrito,
                    provincia: provincia,
                    departamento: departamento,
                    bienes: bienes_disponibles,
                    fecha: fecha_actual,
                };

                rptEvaluacionBienes.data = data;
                rptEvaluacionBienes.document_title =
                    "rptDeclaracionBienes_" +
                    self.datos_cliente.id +
                    "_" +
                    self.aprobacion_id;
            }

            EnviarDatos().then(() => {
                rptEvaluacionBienes.GenerarWord();
            });
        },
        async GenerarConstancia() {
            let self = this;
            let rptConstanciaGarantia = this.$refs.rptConstanciaGarantia;

            let fecha_sistema = await this.$refs.layout.fecha_hora_actual(
                this.agencia_id,
            );
            fecha_sistema = fecha_sistema.substr(0, 10);
            let parts = fecha_sistema.split("-");

            let anio = parts[0];
            moment.locale("es");
            let mes = moment(parts[1], "M").format("MMMM");
            let dia = parts[2];

            async function EnviarDatos() {
                let cliente = self.nombre_completo_titular;
                let dni = self.datos_cliente.dni;
                let direccion = self.datos_cliente.direccion;
                let distrito = self.datos_cliente.distrito;
                let provincia = self.datos_cliente.provincia;
                let departamento = self.datos_cliente.departamento;

                let bienes_disponibles = [];

                self.datos_prendas.forEach((element) => {
                    self.datos_propuesta.prendas.forEach((element_1) => {
                        if (element.id == element_1) {
                            bienes_disponibles.push(element);
                        }
                    });
                });

                let orden = 0;
                bienes_disponibles.forEach((element) => {
                    orden += 1;
                    element.orden = orden;
                    if (element.modelo == null) {
                        element.modelo = "-";
                    }

                    if (element.serie == null) {
                        element.serie = "-";
                    }

                    if (element.comentario == null) {
                        element.comentario = "-";
                    }
                });

                let data = {
                    cantidad_dias: dia,
                    mes: mes,
                    anio: anio,
                    cliente: cliente,
                    dni: dni,
                    direccion: direccion,
                    distrito: distrito,
                    provincia: provincia,
                    departamento: departamento,
                    bienes: bienes_disponibles,
                };

                rptConstanciaGarantia.data = data;
                rptConstanciaGarantia.document_title =
                    "rptConstanciaGarantia_" +
                    self.datos_cliente.id +
                    "_" +
                    self.aprobacion_id;
            }

            EnviarDatos().then(() => {
                rptConstanciaGarantia.GenerarWord();
            });
        },
    },
};
</script>

<style lang="css">
.slot-aprobacion {
    width: 70% !important;
    margin-left: 15% !important;
}
.text2 {
    display: none;
}

#tblCronogramaPropuesta td {
    padding: 2px !important;
}
#tblCronogramaAprobacion td {
    padding: 2px !important;
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

/* Para establecer tamaño de checkbox */
.page__toggle {
    --toggleColor: var(--colorAlto) !important;
    --toggleSize: 25px !important;

    width: fit-content;
    height: fit-content;
}

/* -------------- */

.input-especial {
    background: var(--azulOscuroEmpresarial);
    color: white;
}

.gracia-ci {
    font-size: 9px !important;
    background: red;
    color: white;
}

.gracia-si {
    font-size: 9px !important;
    background: var(--verdeOscuroEmpresarial);
    color: white;
}

@media (max-width: 900px) {
    .slot-aprobacion {
        width: 99% !important;
        margin-left: 0.5% !important;
    }

    .text1 {
        display: none;
    }
    .text2 {
        display: contents;
    }
}
</style>
