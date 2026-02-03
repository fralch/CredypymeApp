<template>
    <layout ref="layout">
        <div
            class="slot_body slot-propuesta"
            slot="component-view"
            v-if="this.evaluacion_id != 0 && this.datos_negocio != null"
        >
            <div class="content" style="display: block">
                <div class="card">
                    <headerClose :title="'PROPUESTA'"></headerClose>
                    <div class="card-title">INFORMACIÓN DE PROPUESTA</div>
                    <div class="card-body card-block">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a
                                    class="nav-link active tab-title"
                                    id="datos-tab"
                                    data-toggle="tab"
                                    href="#datos"
                                    role="tab"
                                    aria-controls="datos"
                                    aria-selected="true"
                                    >CLIENTE</a
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
                                    id="garantias-tab"
                                    data-toggle="tab"
                                    href="#garantias"
                                    role="tab"
                                    aria-controls="garantias"
                                    aria-selected="false"
                                    >GARANTÍAS</a
                                >
                            </li>
                            <li class="nav-item" v-if="frmPropuesta.prendario">
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
                                    >SU PARIENTE/AVAL</a
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
                                id="datos"
                                role="tabpanel"
                                aria-labelledby="datos-tab"
                            >
                                <div class="form-row">
                                    <div class="form-group col-md-9 col-12">
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
                                    <div class="form-group col-md-3 col-6">
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

                                    <div class="form-group col-md-3 col-6">
                                        <label class="label-title">DNI</label>
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="datos_cliente.dni"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-3 col-7">
                                        <label class="text1 label-title center"
                                            >CENTRAL DE RIESGO</label
                                        >
                                        <label class="text2 label-title center"
                                            >CEN. RIESGO</label
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
                                    <div class="form-group col-md-3 col-5">
                                        <label class="label-title"
                                            >CALIFICACIÓN</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="datos_cliente.calificacion"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-3 col-6">
                                        <label class="label-title"
                                            >AGENCIA</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="datos_cliente.agencia"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="form-group col-md-6 col-6">
                                        <label class="label-title"
                                            >DIRECCIÓN</label
                                        >
                                        <textarea
                                            class="form-control text-row"
                                            rows="2"
                                            :value="datos_cliente.direccion"
                                            :disabled="true"
                                        ></textarea>
                                    </div>
                                    <div class="form-group col-md-6 col-6">
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
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title"
                                            >PARIENTE</label
                                        >
                                        <textarea
                                            class="form-control text-row"
                                            rows="2"
                                            :value="nombre_completo_pariente"
                                            :disabled="true"
                                        ></textarea>
                                    </div>
                                    <div class="form-group col-md-4 col-6">
                                        <label class="label-title">AVAL</label>
                                        <textarea
                                            class="form-control text-row"
                                            rows="2"
                                            :value="nombre_completo_aval"
                                            :disabled="true"
                                        ></textarea>
                                    </div>
                                    <div class="form-group col-md-4 col-6">
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
                                    <div class="form-group col-md-12 col-12">
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
                                id="calendario"
                                role="tabpanel"
                                aria-labelledby="calendario-tab"
                            >
                                <div class="form-row">
                                    <div class="form-group col-md-8 col-8">
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
                                    <div class="form-group col-md-4 col-4">
                                        <label class="text1 label-title"
                                            >FECHA DESEMBOLSO</label
                                        >
                                        <label class="text2 label-title"
                                            >DESEMBOLSO</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="fecha_actual"
                                            style="font-weight: bolder"
                                            :disabled="true"
                                        />
                                    </div>
                                    <div
                                        class="form-row col-md-7"
                                        style="max-height: 250px"
                                    >
                                        <div class="col-md-6 col-6">
                                            <label class="label-title"
                                                >MONTO
                                                <span
                                                    class="text"
                                                    style="font-size: 10px"
                                                    >(máx.: S/
                                                    {{
                                                        this.roundTo(
                                                            datos_cliente.monto_maximo,
                                                            2,
                                                        )
                                                    }})</span
                                                ></label
                                            >

                                            <input
                                                type="number"
                                                class="form-control center"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta.monto
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
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
                                                    frmPropuesta.monto
                                                "
                                                name="monto"
                                                @change="Redondear"
                                                @focus="hidenav()"
                                                @blur="shownav()"
                                                :disabled="propuesta_id != 0"
                                            />
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <label class="label-title"
                                                >SECTOR</label
                                            >

                                            <select
                                                class="form-control"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .sector_id
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model.number="
                                                    frmPropuesta.sector_id
                                                "
                                                :disabled="propuesta_id != 0"
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
                                        <div class="col-md-6 col-6">
                                            <label class="label-title"
                                                >PRODUCTO</label
                                            >

                                            <select
                                                class="form-control"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .producto_id
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                @change="VerificarPrendario"
                                                v-model.number="
                                                    frmPropuesta.producto_id
                                                "
                                                :disabled="propuesta_id != 0"
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
                                        <div class="col-md-6 col-6">
                                            <label class="label-title"
                                                >SUBPRODUCTO</label
                                            >

                                            <select
                                                class="form-control"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .subproducto_id
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model.number="
                                                    frmPropuesta.subproducto_id
                                                "
                                                :disabled="
                                                    subproductos_filtrados.length ==
                                                        0 || propuesta_id != 0
                                                "
                                            >
                                                <option
                                                    :value="null"
                                                    selected
                                                    v-if="
                                                        subproductos_filtrados.length ==
                                                        0
                                                    "
                                                >
                                                    Ninguno
                                                </option>
                                                <option
                                                    :value="0"
                                                    disabled
                                                    v-if="
                                                        subproductos_filtrados.length !=
                                                        0
                                                    "
                                                >
                                                    Seleccione...
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

                                            <select
                                                class="form-control"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .tipo_id.$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model.number="
                                                    frmPropuesta.tipo_id
                                                "
                                                :disabled="propuesta_id != 0"
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
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .periodo_pago
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model="
                                                    frmPropuesta.periodo_pago
                                                "
                                                @change="
                                                    datos_calendario = [];
                                                    datos_cuota = [];
                                                    frmPropuesta.cuota = 0.0;
                                                "
                                                :disabled="propuesta_id != 0"
                                            >
                                                <option value="DIARIO" selected>
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
                                        <div class="col-md-4 col-4 offset-md-2">
                                            <label class="text1 label-title"
                                                >PLAZO
                                                {{
                                                    periodo_medicion(
                                                        frmPropuesta.periodo_pago,
                                                    )
                                                }}</label
                                            >

                                            <label class="text2 label-title"
                                                >PLZ.
                                                {{
                                                    periodo_medicion(
                                                        frmPropuesta.periodo_pago,
                                                    )
                                                }}</label
                                            >

                                            <input
                                                type="number"
                                                class="form-control center"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta.plazo
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                min="1"
                                                step="1"
                                                lang="en"
                                                v-model.number="
                                                    frmPropuesta.plazo
                                                "
                                                name="plazo"
                                                @change="Redondear"
                                                @focus="hidenav()"
                                                @blur="shownav()"
                                                style="
                                                    font-size: 15px;
                                                    font-weight: bolder;
                                                    color: var(--colorAlto);
                                                "
                                                :disabled="propuesta_id != 0"
                                            />
                                        </div>
                                        <div class="col-md-4 col-4">
                                            <label class="text1 label-title"
                                                >TASA INTERÉS (%)</label
                                            >
                                            <label class="text2 label-title"
                                                >TASA INT.(%)</label
                                            >

                                            <input
                                                type="number"
                                                class="form-control center"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .tasa_interes
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                min="1"
                                                step="0.1"
                                                lang="en"
                                                v-model.number="
                                                    frmPropuesta.tasa_interes
                                                "
                                                name="tasa_interes"
                                                @change="Redondear"
                                                @focus="hidenav()"
                                                @blur="shownav()"
                                                style="
                                                    font-size: 15px;
                                                    font-weight: bolder;
                                                    color: var(--colorAlto);
                                                "
                                                :disabled="propuesta_id != 0"
                                            />
                                        </div>
                                        <div
                                            class="col-md-9 col-4 mt-2"
                                            v-if="
                                                this.frmPropuesta
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
                                                            frmPropuesta.es_especial
                                                                ? 'input-especial'
                                                                : '',
                                                        ]"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            id="chbEspecial"
                                                            v-model="
                                                                frmPropuesta.es_especial
                                                            "
                                                            :disabled="
                                                                propuesta_id !=
                                                                0
                                                            "
                                                        />
                                                    </div>
                                                    <label
                                                        class="input-group-text prepend-title"
                                                        :class="[
                                                            frmPropuesta.es_especial
                                                                ? 'input-especial'
                                                                : '',
                                                        ]"
                                                        for="chbEspecial"
                                                        style="
                                                            font-size: 13px;
                                                            height: 32px !important;
                                                        "
                                                        v-if="
                                                            frmPropuesta.es_especial
                                                        "
                                                    >
                                                        ESPECIAL
                                                    </label>
                                                    <label
                                                        class="input-group-text prepend-title"
                                                        style="
                                                            height: 32px !important;
                                                        "
                                                        v-if="
                                                            frmPropuesta.es_especial
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

                                                <div class="input-group-append">
                                                    <label
                                                        class="input-group-text prepend-title"
                                                        :class="[
                                                            frmPropuesta.es_especial
                                                                ? 'input-especial'
                                                                : '',
                                                        ]"
                                                        for="chbEspecial"
                                                        style="
                                                            font-size: 13px;
                                                            height: 32px !important;
                                                        "
                                                        v-if="
                                                            !frmPropuesta.es_especial
                                                        "
                                                    >
                                                        ESPECIAL
                                                    </label>
                                                </div>

                                                <input
                                                    type="number"
                                                    class="form-control center"
                                                    :class="[
                                                        submited
                                                            ? $v.frmPropuesta
                                                                  .mora_adicional
                                                                  .$invalid
                                                                ? 'is-invalid'
                                                                : 'is-valid'
                                                            : '',
                                                    ]"
                                                    min="0.5"
                                                    step="0.1"
                                                    lang="en"
                                                    v-model.number="
                                                        frmPropuesta.mora_adicional
                                                    "
                                                    name="mora_adicional"
                                                    @change="Redondear"
                                                    @focus="hidenav()"
                                                    @blur="shownav()"
                                                    style="
                                                        font-size: 15px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                    v-if="
                                                        frmPropuesta.es_especial
                                                    "
                                                    :disabled="
                                                        propuesta_id != 0
                                                    "
                                                />
                                            </div>
                                        </div>

                                        <!-- <div
											class="input-group mt-2 col-md-6 col-7"
											v-if="periodo_pago != 'PAGO_UNICO'"
										>
											<div class="input-group-prepend">
												<div class="input-group-text">
													<input
														type="checkbox"
														id="chbDiasGraciaPropuesta"
														v-model="frmPropuesta.con_dias_gracia"
														:disabled="propuesta_id != 0"
													/>
												</div>
												<label
													class="input-group-text prepend-title"
													for="chbDiasGraciaPropuesta"
													style="font-size: 13px"
												>
													DÍAS DE GRACIA
												</label>
											</div>

											<input
												type="number"
												class="form-control center"
												min="0"
												:max="maximo_dias_gracia"
												name="dias_gracia"
												@change="Redondear"
												@focus="hidenav()"
												@blur="shownav()"
												v-model="frmPropuesta.dias_gracia"
												:disabled="
													propuesta_id != 0 || frmPropuesta.con_dias_gracia == 0
												"
											/>
										</div> -->
                                    </div>

                                    <div
                                        class="element form-group col-md-5"
                                        style="height: 280px"
                                    >
                                        <div class="text-center">
                                            <button
                                                class="btn btn-action btn-icon-split mt-3"
                                                title="Genera calendario de pagos"
                                                @click="CalcularCronograma"
                                                :disabled="propuesta_id != 0"
                                            >
                                                <span class="icon text-white">
                                                    <i
                                                        class="far fa-calendar-alt"
                                                    ></i
                                                ></span>
                                                <span class="text"
                                                    >GENERAR CALENDARIO</span
                                                >
                                            </button>
                                            <span
                                                v-if="
                                                    submited &&
                                                    !$v.frmPropuesta.cuota
                                                        .required
                                                "
                                                class="span-error-message"
                                                >*</span
                                            >
                                        </div>
                                        <!-- <table
											class="table"
											id="tblCronograma"
											style="width: 100% !important"
										>
											<thead>
												<tr>
													<th style="min-width: 30px !important">CUOTA</th>
													<th style="min-width: 80px !important">FECHA_PAGO</th>
													<th style="min-width: 50px !important">DÍA</th>
												</tr>
											</thead>
											<tbody>
												<tr
													v-for="(item, index) in datos_calendario"
													:key="index"
													:class="[index % 2 == 0 ? 'verde-claro' : '']"
												>
													<td
														class="table-bordered"
														align="center"
														width="20px !important"
													>
														{{ item.orden }}
													</td>
													<td class="table-bordered" align="center">
														{{ item.fecha_pago }}
													</td>
													<td class="table-bordered" align="center">
														{{ item.dia_pago }}
													</td>
												</tr>
											</tbody>
										</table> -->

                                        <DataTable
                                            :value="datos_calendario"
                                            :scrollable="true"
                                            scrollDirection="both"
                                            scrollHeight="200px"
                                            selectionMode="single"
                                            showGridlines
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
                                                <template #body="slotProps">
                                                    {{ slotProps.index + 1 }}
                                                </template>
                                            </Column>
                                            <Column
                                                field="fecha_pago"
                                                header="FECHA_PAGO"
                                                :styles="{
                                                    width: '80px',
                                                    justifyContent: 'center',
                                                }"
                                            >
                                            </Column>
                                            <Column
                                                field="dia_pago"
                                                header="DÍA_PAGO"
                                                :styles="{
                                                    width: '80px',
                                                    justifyContent: 'center',
                                                }"
                                            >
                                            </Column>
                                            <template #empty>
                                                No hay CUOTAS
                                                generadas.</template
                                            >
                                        </DataTable>
                                        <div
                                            class="col-md-12"
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
                                                            frmPropuesta.cuota,
                                                            2,
                                                        )
                                                    }}</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="garantias"
                                role="tabpanel"
                                aria-labelledby="garantias-tab"
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
                                                {{ frmPropuesta.monto }}</span
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
                                                        frmPropuesta.cuota,
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
                                                        frmPropuesta.cuota,
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
                                                        frmPropuesta.periodo_pago
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
                                                        frmPropuesta.plazo +
                                                        " " +
                                                        periodo_medicion(
                                                            frmPropuesta.periodo_pago,
                                                        )
                                                    }}</span
                                                ></label
                                            >
                                        </div>
                                        <div class="form-group col-md-4 col-4">
                                            <label
                                                class="label-title"
                                                v-if="windowWidth >= 900"
                                                >TASA DE INTERÉS:
                                                <span
                                                    class="text"
                                                    style="
                                                        font-size: 11px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    {{
                                                        frmPropuesta.tasa_interes
                                                    }}
                                                    %</span
                                                ></label
                                            >
                                            <label
                                                class="label-title"
                                                v-if="windowWidth < 900"
                                                >TAS INT:
                                                <span
                                                    class="text"
                                                    style="
                                                        font-size: 11px;
                                                        font-weight: bolder;
                                                        color: var(--colorAlto);
                                                    "
                                                >
                                                    {{
                                                        frmPropuesta.tasa_interes
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
                                        <div
                                            class="form-group col-md-2 col-4 mt-3"
                                        >
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="forma_pago"
                                                    id="rdbOficina"
                                                    value="OFICINA"
                                                    v-model="
                                                        frmPropuesta.forma_pago
                                                    "
                                                    :disabled="
                                                        propuesta_id != 0
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
                                        <div
                                            class="form-group col-md-2 col-4 mt-3"
                                        >
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="forma_pago"
                                                    id="rdbNegocio"
                                                    value="NEGOCIO"
                                                    v-model="
                                                        frmPropuesta.forma_pago
                                                    "
                                                    :disabled="
                                                        propuesta_id != 0
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
                                        <div class="form-group col-md-3 col-6">
                                            <label class="label-title"
                                                >PROMOTOR</label
                                            >

                                            <select
                                                class="form-control center"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .promotor_id
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model.number="
                                                    frmPropuesta.promotor_id
                                                "
                                                :disabled="propuesta_id != 0"
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
                                        <div class="form-group col-md-4 col-6">
                                            <label class="label-title"
                                                >GARANTÍA</label
                                            >

                                            <select
                                                class="form-control"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .garantia_id
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model.number="
                                                    frmPropuesta.garantia_id
                                                "
                                                :disabled="
                                                    propuesta_id != 0 ||
                                                    frmPropuesta.prendario
                                                "
                                            >
                                                <option :value="0" disabled>
                                                    Seleccione...
                                                </option>
                                                <option
                                                    v-for="(
                                                        item, index
                                                    ) in garantias"
                                                    :key="index"
                                                    :value="item.id"
                                                >
                                                    {{ item.garantia }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2 col-5">
                                            <label class="label-title"
                                                >VALOR</label
                                            >

                                            <input
                                                type="number"
                                                class="form-control center"
                                                :class="[
                                                    submited
                                                        ? $v.frmPropuesta
                                                              .valor_garantia
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model.number="
                                                    frmPropuesta.valor_garantia
                                                "
                                                name="valor_garantia"
                                                @change="Redondear"
                                                @focus="hidenav()"
                                                @blur="shownav()"
                                                :disabled="propuesta_id != 0"
                                            />
                                        </div>
                                        <div class="form-group col-md-6 col-7">
                                            <label class="label-title"
                                                >DESCRIPCIÓN DE LA
                                                GARANTÍA</label
                                            >
                                            <textarea
                                                rows="2"
                                                class="form-control text-row mayus"
                                                :value="descripcion_garantia"
                                                :disabled="true"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 col-6">
                                        <label
                                            class="label-title"
                                            v-if="windowWidth >= 900"
                                            >COMENTARIO DE LA GARANTÍA</label
                                        >
                                        <label
                                            class="label-title"
                                            v-if="windowWidth < 900"
                                            >COMENTARIO GARANTÍA</label
                                        >

                                        <textarea
                                            class="form-control text-row mayus"
                                            :class="[
                                                submited
                                                    ? $v.frmPropuesta
                                                          .comentario_garantia
                                                          .$invalid
                                                        ? 'is-invalid'
                                                        : 'is-valid'
                                                    : '',
                                            ]"
                                            rows="2"
                                            v-model="
                                                frmPropuesta.comentario_garantia
                                            "
                                            :disabled="propuesta_id != 0"
                                            @focus="hidenav()"
                                            @blur="shownav()"
                                        ></textarea>
                                    </div>
                                    <div class="form-group col-md-6 col-6">
                                        <label
                                            class="label-title"
                                            v-if="windowWidth >= 900"
                                            >COMENTARIO DE LA PROPUESTA</label
                                        >
                                        <label
                                            class="label-title"
                                            v-if="windowWidth < 900"
                                            >COMENTARIO PROPUESTA</label
                                        >

                                        <textarea
                                            rows="2"
                                            class="form-control text-row mayus"
                                            :class="[
                                                submited
                                                    ? $v.frmPropuesta
                                                          .comentario_propuesta
                                                          .$invalid
                                                        ? 'is-invalid'
                                                        : 'is-valid'
                                                    : '',
                                            ]"
                                            v-model="
                                                frmPropuesta.comentario_propuesta
                                            "
                                            :disabled="propuesta_id != 0"
                                            @focus="hidenav()"
                                            @blur="shownav()"
                                        ></textarea>
                                    </div>
                                </div>
                                <hr />
                                <div class="text-right">
                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="Guardar"
                                            v-if="propuesta_id == 0"
                                        >
                                            <span class="icon text-white">
                                                <i class="fas fa-save"></i
                                            ></span>
                                            <span class="text">GUARDAR</span>
                                        </button>
                                        <button
                                            class="btn btn-cancel btn-icon-split"
                                            @click="Imprimir"
                                            v-if="propuesta_id != 0"
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
                                <!-- <table class="table" id="tblPrendas" width="100% !important">
									<thead>
										<tr>
											<th style="min-width: 20px !important">SELECC.</th>
											<th style="min-width: 30px !important">VER</th>
											<th style="min-width: 200px !important">DESCRIPCIÓN</th>
											<th
												style="min-width: 50px !important"
												v-show="propuesta_id == 0"
											>
												DISP.
											</th>
											<th style="min-width: 100px !important">
												FECHA_REGISTRO
											</th>
											<th style="min-width: 100px !important">
												USUARIO_REGISTRO
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in datos_prendas"
											:key="index"
											class="table-bordered"
										>
											<td align="center">
												<div class="page__toggle">
													<label class="toggle">
														<input
															class="toggle__input"
															type="checkbox"
															:id="'chbPrenda_' + index"
															:value="item.id"
															v-model="frmPropuesta.prendas"
															:disabled="propuesta_id != 0"
														/>
														<span class="toggle__label">
															<span class="toggle__text"></span>
														</span>
													</label>
												</div>
											</td>
											<td align="center">
												<button
													class="btn btn-cancel btn-icon-split"
													title="Ver"
													@click="DetallePrenda(item)"
												>
													<span class="icon text-white">
														<i class="fas fa-eye"></i>
													</span>
												</button>
											</td>
											<td>
												{{ item.descripcion }}
											</td>
											<td align="center" v-show="propuesta_id == 0">
												{{ item.disponible == 1 ? "SI" : "NO" }}
											</td>

											<td align="center">
												{{ JSON.parse(item.datos_creacion).fecha }}
											</td>
											<td align="center">
												{{ item.usuario_registro }}
											</td>
										</tr>
									</tbody>
								</table> -->

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
                                                            frmPropuesta.prendas
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
                                        :disabled="propuesta_id != 0"
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
                                        id="chbConfirmarVinculadoA"
                                        v-model="revision_vinculado_a"
                                        :disabled="propuesta_id != 0"
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

            <div id="mdlDatosActualizacion" class="modal">
                <!-- Modal content -->
                <div class="modal-content w-50 mdlDatosActualizacion">
                    <div class="content" style="display: block">
                        <div class="card">
                            <headerCloseModal
                                :titulo_modal="'ACTUALIZACIÓN OBLIGATORIA DE DATOS'"
                                :nombre_modal="'mdlDatosActualizacion'"
                            ></headerCloseModal>
                            <div class="card-body card-block">
                                <div class="form-row">
                                    <div class="form-group col-md-12 col-7">
                                        <label class="label-title"
                                            >CORREO ELECTRÓNICO</label
                                        >
                                        <form autocomplete="off">
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    frmDatosCliente.correo_electronico
                                                "
                                            />
                                        </form>
                                    </div>

                                    <div class="form-group col-md-4 col-4">
                                        <label class="label-title"
                                            >NÚMERO</label
                                        >

                                        <div class="input-group">
                                            <div
                                                class="input-group-prepend"
                                                v-if="windowWidth > 1000"
                                            >
                                                <span class="input-group-text"
                                                    >1</span
                                                >
                                            </div>

                                            <input
                                                type="number"
                                                min="0"
                                                lang="en"
                                                class="form-control center"
                                                :class="[
                                                    submited_2
                                                        ? $v.frmDatosCliente
                                                              .telefonos.t1
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                maxlength="9"
                                                oninput="
                                                    javascript: if (
                                                        this.value.length >
                                                        this.maxLength
                                                    )
                                                        this.value =
                                                            this.value.slice(
                                                                0,
                                                                this.maxLength,
                                                            );
                                                "
                                                v-model="
                                                    frmDatosCliente.telefonos.t1
                                                "
                                            />
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4 col-4">
                                        <label class="label-title"
                                            >OPERADOR</label
                                        >

                                        <div class="input-group">
                                            <div
                                                class="input-group-prepend"
                                                v-if="windowWidth > 1000"
                                            >
                                                <span class="input-group-text"
                                                    >1</span
                                                >
                                            </div>

                                            <select
                                                type="text"
                                                class="form-control center"
                                                :class="[
                                                    submited_2
                                                        ? $v.frmDatosCliente
                                                              .telefonos.o1
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model="
                                                    frmDatosCliente.telefonos.o1
                                                "
                                            >
                                                <option :value="0" disabled>
                                                    Seleccione...
                                                </option>
                                                <option value="Movistar">
                                                    Movistar
                                                </option>
                                                <option value="Claro">
                                                    Claro
                                                </option>
                                                <option value="Bitel">
                                                    Bitel
                                                </option>
                                                <option value="Entel">
                                                    Entel
                                                </option>
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
                                                <span class="input-group-text"
                                                    >1</span
                                                >
                                            </div>

                                            <textarea
                                                type="text"
                                                rows="2"
                                                class="form-control mayus"
                                                :class="[
                                                    submited_2
                                                        ? $v.frmDatosCliente
                                                              .telefonos.n1
                                                              .$invalid
                                                            ? 'is-invalid'
                                                            : 'is-valid'
                                                        : '',
                                                ]"
                                                v-model="
                                                    frmDatosCliente.telefonos.n1
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
                                                <span class="input-group-text"
                                                    >2</span
                                                >
                                            </div>
                                            <input
                                                type="number"
                                                min="0"
                                                lang="en"
                                                class="form-control center"
                                                style="max-width: 400px"
                                                maxlength="9"
                                                oninput="
                                                    javascript: if (
                                                        this.value.length >
                                                        this.maxLength
                                                    )
                                                        this.value =
                                                            this.value.slice(
                                                                0,
                                                                this.maxLength,
                                                            );
                                                "
                                                v-model="
                                                    frmDatosCliente.telefonos.t2
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
                                                <span class="input-group-text"
                                                    >2</span
                                                >
                                            </div>
                                            <select
                                                type="text"
                                                class="form-control center"
                                                v-model="
                                                    frmDatosCliente.telefonos.o2
                                                "
                                            >
                                                <option value="0" disabled>
                                                    Seleccione...
                                                </option>
                                                <option value="Movistar">
                                                    Movistar
                                                </option>
                                                <option value="Claro">
                                                    Claro
                                                </option>
                                                <option value="Bitel">
                                                    Bitel
                                                </option>
                                                <option value="Entel">
                                                    Entel
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4 col-4">
                                        <div class="input-group">
                                            <div
                                                class="input-group-prepend"
                                                v-if="windowWidth > 1000"
                                            >
                                                <span class="input-group-text"
                                                    >2</span
                                                >
                                            </div>

                                            <textarea
                                                type="text"
                                                rows="2"
                                                class="form-control mayus"
                                                style="max-width: 500px"
                                                v-model="
                                                    frmDatosCliente.telefonos.n2
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
                                                <span class="input-group-text"
                                                    >3</span
                                                >
                                            </div>
                                            <input
                                                type="number"
                                                min="0"
                                                lang="en"
                                                class="form-control center"
                                                style="max-width: 400px"
                                                maxlength="9"
                                                oninput="
                                                    javascript: if (
                                                        this.value.length >
                                                        this.maxLength
                                                    )
                                                        this.value =
                                                            this.value.slice(
                                                                0,
                                                                this.maxLength,
                                                            );
                                                "
                                                v-model="
                                                    frmDatosCliente.telefonos.t3
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
                                                <span class="input-group-text"
                                                    >3</span
                                                >
                                            </div>
                                            <select
                                                type="text"
                                                class="form-control center"
                                                v-model="
                                                    frmDatosCliente.telefonos.o3
                                                "
                                            >
                                                <option value="0" disabled>
                                                    Seleccione...
                                                </option>
                                                <option value="Movistar">
                                                    Movistar
                                                </option>
                                                <option value="Claro">
                                                    Claro
                                                </option>
                                                <option value="Bitel">
                                                    Bitel
                                                </option>
                                                <option value="Entel">
                                                    Entel
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-4">
                                        <div class="input-group">
                                            <div
                                                class="input-group-prepend"
                                                v-if="windowWidth > 1000"
                                            >
                                                <span class="input-group-text"
                                                    >3</span
                                                >
                                            </div>
                                            <textarea
                                                type="text"
                                                rows="2"
                                                class="form-control mayus"
                                                style="max-width: 500px"
                                                v-model="
                                                    frmDatosCliente.telefonos.n3
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
                                                <span class="input-group-text"
                                                    >4</span
                                                >
                                            </div>
                                            <input
                                                type="number"
                                                min="0"
                                                lang="en"
                                                class="form-control center"
                                                style="max-width: 400px"
                                                maxlength="9"
                                                oninput="
                                                    javascript: if (
                                                        this.value.length >
                                                        this.maxLength
                                                    )
                                                        this.value =
                                                            this.value.slice(
                                                                0,
                                                                this.maxLength,
                                                            );
                                                "
                                                v-model="
                                                    frmDatosCliente.telefonos.t4
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
                                                <span class="input-group-text"
                                                    >4</span
                                                >
                                            </div>
                                            <select
                                                type="text"
                                                class="form-control center"
                                                v-model="
                                                    frmDatosCliente.telefonos.o4
                                                "
                                            >
                                                <option value="0" disabled>
                                                    Seleccione...
                                                </option>
                                                <option value="Movistar">
                                                    Movistar
                                                </option>
                                                <option value="Claro">
                                                    Claro
                                                </option>
                                                <option value="Bitel">
                                                    Bitel
                                                </option>
                                                <option value="Entel">
                                                    Entel
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-4">
                                        <div class="input-group">
                                            <div
                                                class="input-group-prepend"
                                                v-if="windowWidth > 1000"
                                            >
                                                <span class="input-group-text"
                                                    >4</span
                                                >
                                            </div>
                                            <textarea
                                                type="text"
                                                rows="1"
                                                class="form-control mayus"
                                                style="max-width: 500px"
                                                v-model="
                                                    frmDatosCliente.telefonos.n4
                                                "
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                <div class="text-right">
                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="GuardarDatosPersonal"
                                        >
                                            <span class="icon text-white">
                                                <i class="fas fa-save"></i
                                            ></span>
                                            <span class="text">GUARDAR</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <mdlDetallePrenda ref="mdlDetallePrenda" :agencia_id="agencia_id">
            </mdlDetallePrenda>
        </div>
    </layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import mdlDetallePrenda from "@/Pages/Creditos/Clientes/Components/mdlDetallePrenda.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import { required } from "vuelidate/lib/validators";

const noZero = (value) => value != 0;
export default {
    components: {
        layout,
        headerClose,
        mdlDetallePrenda,
        headerCloseModal,
        DataTable,
        Column,
    },
    props: {
        agencia_id: Number,
        propuesta_id: Number,
        datos_propuesta: Object,
        datos_negocio: Object,
        evaluacion_id: Number,
        cliente_id: Number,
        datos_cliente: Object,
        datos_pariente: Object,
        datos_aval: Object,
        datos_pariente_aval: Object,
        datos_prendas: Array,
        creditos_vinculados_de: Array,
        creditos_vinculados_a: Array,
        sectores: Array,
        productos: Array,
        subproductos: Array,
        tipos: Array,
        garantias: Array,
        usuarios: Array,
        ultimo_credito: Number,
    },
    data() {
        return {
            windowWidth: window.innerWidth,

            telefonos_actualizados: false,

            datos_cuota: [],
            datos_calendario: [],
            submited: false,
            submited_2: false,
            revision_vinculado_de:
                this.propuesta_id != 0
                    ? true
                    : this.creditos_vinculados_de.length > 0
                      ? false
                      : true,
            revision_vinculado_a:
                this.propuesta_id != 0
                    ? true
                    : this.creditos_vinculados_a.length > 0
                      ? false
                      : true,
            fecha_actual: null,

            frmPropuesta: {
                pariente_id:
                    this.datos_pariente == null
                        ? null
                        : this.datos_pariente.pariente_id,
                agencia_pariente:
                    this.datos_pariente == null
                        ? null
                        : this.datos_pariente.agencia_pariente,
                aval_id:
                    this.datos_aval == null ? null : this.datos_aval.aval_id,
                agencia_aval:
                    this.datos_aval == null
                        ? null
                        : this.datos_aval.agencia_aval,
                pariente_aval_id:
                    this.datos_pariente_aval == null
                        ? null
                        : this.datos_pariente_aval.pariente_aval_id,
                agencia_pariente_aval:
                    this.datos_pariente_aval == null
                        ? null
                        : this.datos_pariente_aval.agencia_pariente,
                monto: null,
                sector_id: 0,
                producto_id: 0,
                subproducto_id: null,
                tipo_id: 0,
                periodo_pago: null,
                plazo: null,
                tasa_interes: null,
                es_especial: false,
                mora_adicional: 0,
                con_dias_gracia: false,
                dias_gracia: 0,
                forma_pago: null,
                garantia_id: 0,
                promotor_id: 0,
                valor_garantia: null,
                cuota: null,
                fecha_propuesta: null,
                comentario_garantia: null,
                comentario_propuesta: null,
                prendario: false,
                prendas: [],
            },

            frmDatosCliente: {
                correo_electronico: null,
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
            },
        };
    },
    validations: {
        frmPropuesta: {
            monto: { required, noZero },
            sector_id: { noZero },
            producto_id: { noZero },
            subproducto_id: { noZero },
            tipo_id: { noZero },
            periodo_pago: {},
            plazo: { required, noZero },
            tasa_interes: { required, noZero },
            garantia_id: { noZero },
            promotor_id: { noZero },
            valor_garantia: { required },
            cuota: { required, noZero },
            comentario_garantia: { required },
            comentario_propuesta: { required },
        },

        frmDatosCliente: {
            telefonos: {
                t1: { required },
                o1: { noZero },
                n1: { required },
            },
        },

        revision_vinculado_de: { checked: (value) => value === true },
        revision_vinculado_a: { checked: (value) => value === true },
    },

    computed: {
        lista_promotores() {
            let agencia_id = this.$inertia.page.props.user_session.id_agencia;
            return this.usuarios.filter(
                (item) => item.agencia_id == agencia_id,
            );
        },
        subproductos_filtrados() {
            if (this.frmPropuesta.producto_id == 0) {
                this.frmPropuesta.subproducto_id = null;
                return [];
            } else {
                let producto_id = this.frmPropuesta.producto_id;

                let lista_subproductos = this.subproductos.filter(
                    (item) => item.producto_id == producto_id,
                );

                if (this.propuesta_id == 0) {
                    if (lista_subproductos.length == 0) {
                        this.frmPropuesta.subproducto_id = null;
                    } else {
                        this.frmPropuesta.subproducto_id = 0;
                    }
                }

                return lista_subproductos;
            }
        },
        descripcion_garantia() {
            if (
                this.frmPropuesta.garantia_id == 0 ||
                this.frmPropuesta.garantia_id == null
            ) {
                return "-";
            } else {
                return this.garantias.filter(
                    (item) => item.id == this.frmPropuesta.garantia_id,
                )[0].descripcion;
            }
        },
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
            if (this.datos_cliente.nombre_negocio == null) {
                return "-";
            } else {
                return this.datos_cliente.nombre_negocio;
            }
        },
        considerar_dias_gracia() {
            return this.frmPropuesta.con_dias_gracia;
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
            return this.frmPropuesta.periodo_pago;
        },
        es_especial() {
            return this.frmPropuesta.es_especial;
        },
    },
    watch: {
        considerar_dias_gracia() {
            if (this.propuesta_id == 0) {
                this.frmPropuesta.dias_gracia = 0;
            }
        },
        periodo_pago(value) {
            if (value === "PAGO_UNICO") {
                this.frmPropuesta.es_especial = false;
                this.frmPropuesta.con_dias_gracia = false;
                this.frmPropuesta.dias_gracia = 0;
            }
        },
        es_especial(value) {
            if (value) {
                if (this.propuesta_id == 0) {
                    this.frmPropuesta.mora_adicional = this.roundTo(0.5, 2);
                }
            } else {
                this.frmPropuesta.mora_adicional = this.roundTo(0, 2);
            }
        },
    },
    mounted() {
        window.addEventListener("resize", () => {
            this.windowWidth = window.innerWidth;
        });

        if (this.evaluacion_id == 0) {
            Swal.fire({
                icon: "error",
                text: "Primero debe registrar la EVALUACIÓN FINANCIERA",
                confirmButtonText: "Ok",
                allowOutsideClick: true,
            });
            this.$inertia.get(route("cre.index"));
            return false;
        }
        if (this.datos_negocio == null) {
            Swal.fire({
                icon: "error",
                text: "El cliente no tiene un NEGOCIO registrado",
                confirmButtonText: "Ok",
                allowOutsideClick: true,
            });
            this.$inertia.get(route("cre.index"));
            return false;
        }

        if (this.propuesta_id == 0) {
            this.ResetearFormulario();
            this.VerificarPrendario();
        } else {
            this.RellenarFormulario();
            this.CalcularCronograma();
        }

        this.FechaActual();
    },
    methods: {
        hidenav() {
            return this.$refs.layout.hide_nav();
        },
        shownav() {
            return this.$refs.layout.show_nav();
        },
        async FechaActual() {
            await axios
                .post(
                    route("fecha_hora_agencia", {
                        agencia_id: this.agencia_id,
                    }),
                )
                .then((response) => {
                    let fecha_actual = response.data;

                    this.frmPropuesta.fecha_propuesta = fecha_actual;

                    let fecha = new Date(fecha_actual); //Fecha actual
                    fecha.setMinutes(
                        fecha.getMinutes() + fecha.getTimezoneOffset(),
                    ); //Ajustando hora de zona horaria

                    let ano = fecha.getFullYear(); //obteniendo año
                    let mes = fecha.getMonth() + 1; //obteniendo mes
                    let dia_a = fecha.getUTCDate();
                    if (mes < 10) mes = "0" + mes;

                    if (dia_a < 10) dia_a = "0" + dia_a;

                    let fecha_formato = dia_a + "-" + mes + "-" + ano;

                    this.fecha_actual = fecha_formato;
                });
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
            let valor = 1;
            let numero_decimales = 2;

            if (e.target.value && e.target.value >= 1) {
                valor = e.target.value;
            }

            if (e.target.name == "monto") {
                if (
                    parseFloat(this.frmPropuesta.monto) <=
                    parseFloat(this.datos_cliente.monto_maximo)
                ) {
                    this.frmPropuesta.monto = this.roundTo(
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
                    this.frmPropuesta.monto = this.roundTo(
                        this.datos_cliente.monto_maximo,
                        numero_decimales,
                    );
                }
            } else if (e.target.name == "plazo") {
                this.frmPropuesta.plazo = this.roundTo(valor, 0);
            } else if (e.target.name == "dias_gracia") {
                if (valor > this.maximo_dias_gracia) {
                    this.frmPropuesta.dias_gracia = this.roundTo(
                        this.maximo_dias_gracia,
                        0,
                    );
                } else {
                    this.frmPropuesta.dias_gracia = this.roundTo(valor, 0);
                }
            } else if (e.target.name == "tasa_interes") {
                this.frmPropuesta.tasa_interes = this.roundTo(
                    valor,
                    numero_decimales,
                );
            } else if (e.target.name == "valor_garantia") {
                this.frmPropuesta.valor_garantia = this.roundTo(
                    valor,
                    numero_decimales,
                );
            } else if (e.target.name == "mora_adicional") {
                valor = 0.5;

                if (e.target.value && e.target.value >= 0.5) {
                    valor = e.target.value;
                }

                this.frmPropuesta.mora_adicional = this.roundTo(
                    valor,
                    numero_decimales,
                );
            }
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

        VerificarPrendario() {
            let producto_id = this.frmPropuesta.producto_id;

            if (producto_id != 0) {
                let producto = this.productos.filter(
                    (item) => item.id == producto_id,
                )[0].producto;

                if (producto == "CRÉDITO PRENDARIO") {
                    let garantia_id = this.garantias.filter(
                        (item) => item.garantia == "PRENDARIO",
                    )[0].id;

                    this.frmPropuesta.garantia_id = garantia_id;
                    this.frmPropuesta.prendario = true;
                } else {
                    this.frmPropuesta.garantia_id = 0;
                    this.frmPropuesta.prendario = false;
                }
            }
        },
        ResetearFormulario() {
            this.frmPropuesta.agencia_id = this.datos_cliente.agencia_id;
            this.frmPropuesta.cliente_id = this.cliente_id;
            this.frmPropuesta.negocio_id = this.datos_negocio.id;
            this.frmPropuesta.monto = this.roundTo(
                this.datos_cliente.monto_maximo,
                2,
            );
            this.frmPropuesta.sector_id = 0;
            this.frmPropuesta.producto_id = 0;
            this.frmPropuesta.subproducto_id = null;
            this.frmPropuesta.tipo_id = 0;
            this.frmPropuesta.periodo_pago = "DIARIO";
            this.frmPropuesta.es_especial = false;
            this.frmPropuesta.mora_adicional = 0;
            this.frmPropuesta.plazo = 30;
            this.frmPropuesta.tasa_interes = this.roundTo(6, 2);
            this.frmPropuesta.forma_pago = "OFICINA";
            this.frmPropuesta.garantia_id = this.garantias.filter(
                (item) => item.garantia == "SIN GARANTÍA",
            )[0].id;
            this.frmPropuesta.promotor_id = 0;
            this.frmPropuesta.valor_garantia = this.roundTo(0, 2);
            this.frmPropuesta.cuota = this.roundTo(0, 2);
            this.frmPropuesta.comentario_garantia = null;
            this.frmPropuesta.comentario_propuesta = null;

            this.frmPropuesta.prendas = [];
        },
        RellenarFormulario() {
            this.frmPropuesta.monto = this.roundTo(
                this.datos_propuesta.monto,
                2,
            );
            this.frmPropuesta.sector_id = this.datos_propuesta.sector_id;
            this.frmPropuesta.producto_id = this.datos_propuesta.producto_id;

            this.frmPropuesta.subproducto_id =
                this.datos_propuesta.subproducto_id;
            this.frmPropuesta.tipo_id = this.datos_propuesta.tipo_id;
            this.frmPropuesta.periodo_pago = this.datos_propuesta.periodo_pago;
            this.frmPropuesta.es_especial = this.datos_propuesta.es_especial;

            this.frmPropuesta.mora_adicional = this.roundTo(
                this.datos_propuesta.mora_adicional,
                2,
            );

            this.frmPropuesta.con_dias_gracia =
                this.datos_propuesta.con_dias_gracia;
            this.frmPropuesta.dias_gracia = this.datos_propuesta.dias_gracia;
            this.frmPropuesta.fecha_propuesta =
                this.datos_propuesta.fecha_propuesta;
            this.frmPropuesta.plazo = this.roundTo(
                this.datos_propuesta.plazo,
                0,
            );
            this.frmPropuesta.tasa_interes = this.roundTo(
                this.datos_propuesta.tasa_interes,
                2,
            );
            this.frmPropuesta.forma_pago =
                this.datos_propuesta.pago_oficina == 1 ? "OFICINA" : "NEGOCIO";
            this.frmPropuesta.garantia_id = this.datos_propuesta.garantia_id;
            this.frmPropuesta.promotor_id = this.datos_propuesta.promotor_id;
            this.frmPropuesta.valor_garantia =
                this.datos_propuesta.valor_garantia;
            this.frmPropuesta.prendario = this.datos_propuesta.prendario;
            this.frmPropuesta.prendas = this.datos_propuesta.prendas;
            this.frmPropuesta.cuota = this.datos_propuesta.cuota;
            this.frmPropuesta.comentario_garantia =
                this.datos_propuesta.comentario_garantia;
            this.frmPropuesta.comentario_propuesta =
                this.datos_propuesta.comentario_propuesta;
        },

        async CalcularCronograma() {
            if (
                this.frmPropuesta.con_dias_gracia &&
                this.frmPropuesta.dias_gracia == 0
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
            data.append("monto", this.frmPropuesta.monto);
            data.append("plazo", this.frmPropuesta.plazo);
            data.append("tasa_interes", this.frmPropuesta.tasa_interes);
            data.append("periodo_pago", this.frmPropuesta.periodo_pago);
            if (this.considerar_dias_gracia) {
                data.append("dias_gracia", this.frmPropuesta.dias_gracia);
            } else {
                data.append("dias_gracia", 0);
            }

            data.append("gracia_con_interes", false);
            data.append("fecha_desembolso", this.frmPropuesta.fecha_propuesta);
            data.append("es_especial", this.frmPropuesta.es_especial);

            // this.$inertia.post(route("cre.calcular_cronograma.sin_redondeo"), data);
            // return false;

            await axios
                .post(route("cre.calcular_cronograma.sin_redondeo"), data)
                .then((response) => {
                    this.datos_cuota = response.data.datos_cuota;
                    this.frmPropuesta.cuota =
                        response.data.datos_cuota.monto_cuota;
                    this.datos_calendario = response.data.datos_calendario;
                });
        },
        VerHistorial(event) {
            let modulo = this.$refs.layout.$refs.mdlHistorialCrediticio;

            modulo.agencia_id = event.data.agencia_id;
            modulo.cliente_id = event.data.cliente_id;
            modulo.ActualizarInformacion();

            return $("#mdlHistorialCrediticio").css("display", "block");
        },
        DetallePrenda(item) {
            let mdlDetallePrenda = this.$refs.mdlDetallePrenda;

            mdlDetallePrenda.datos_prenda = item;

            $("#datos-prenda-tab").tab("show");
            $("#mdlDetallePrenda").css("display", "block");
        },
        async Guardar() {
            this.CalcularCronograma();
            this.submited = true;

            if (
                this.frmPropuesta.con_dias_gracia &&
                this.frmPropuesta.dias_gracia == 0
            ) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Debe ingresar al menos un día de gracia.",
                    allowOutsideClick: true,
                });
                $("#calendario-tab").tab("show");
                return false;
            }

            if (
                this.frmPropuesta.prendario &&
                this.frmPropuesta.prendas.length == 0
            ) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Debe seleccionar al menos una PRENDA.",
                    allowOutsideClick: true,
                });
                $("#prendas-tab").tab("show");
                return false;
            }

            if (this.$v.frmPropuesta.$invalid) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Complete todos los campos.",
                    allowOutsideClick: true,
                });
                return false;
            } else if (this.$v.revision_vinculado_de.$invalid) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Confirme que revisó los créditos vinculados.",
                    allowOutsideClick: true,
                });
                $("#vinculado-de-tab").tab("show");
                return false;
            } else if (this.$v.revision_vinculado_a.$invalid) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Confirme que revisó los créditos vinculados.",
                    allowOutsideClick: true,
                });
                $("#vinculado-a-tab").tab("show");
                return false;
            }

            let validacion = false;

            this.frmDatosCliente.telefonos = JSON.parse(
                this.datos_cliente.telefonos,
            );
            this.frmDatosCliente.correo_electronico =
                this.datos_cliente.correo_electronico;

            if (this.frmDatosCliente.telefonos.hasOwnProperty("actualizado")) {
                validacion =
                    this.frmDatosCliente.telefonos.actualizado.substring(0, 10);
            }

            let fecha_actual = await this.$refs.layout.fecha_hora_actual(
                this.datos_cliente.agencia_id,
            );

            fecha_actual = fecha_actual.substring(0, 10);

            if (this.ultimo_credito % 2 == 0) {
                if (validacion == false || validacion != fecha_actual) {
                    $("#mdlDatosActualizacion").css("display", "block");
                } else {
                    this.GuardarPropuesta();
                }
            } else {
                this.GuardarPropuesta();
            }
        },

        async GuardarDatosPersonal() {
            this.submited_2 = true;

            if (this.$v.frmDatosCliente.$invalid) {
                Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Hay uno o más campos vacíos, verifique.",
                });
                return false;
            }

            let telefonos_2 = [];

            telefonos_2 = JSON.parse(this.datos_cliente.telefonos);

            this.telefonos_actualizados =
                JSON.stringify(this.frmDatosCliente.telefonos) !==
                JSON.stringify(telefonos_2);

            if (this.telefonos_actualizados == false) {
                Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Debe actualizar el/los número(s) de teléfono.",
                });
                return false;
            }

            let fecha_actual_2 = await this.$refs.layout.fecha_hora_actual(
                this.datos_cliente.agencia_id,
            );

            // console.log(this.telefonos_actualizados);

            Swal.fire({
                icon: "question",
                text: "¿Desea actualizar estos datos?",
                confirmButtonText: "Si",
                showCancelButton: true,
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.frmDatosCliente.telefonos["actualizado"] =
                        fecha_actual_2;

                    let data = new FormData();

                    data.append("agencia_id", this.agencia_id);
                    data.append("cliente_id", this.cliente_id);

                    data.append(
                        "frmDatosCliente",
                        JSON.stringify(this.frmDatosCliente),
                    );
                    this.$inertia.post(
                        route("cre.propuesta.guardar_telefonos"),
                        data,
                        {
                            preserveScroll: true,
                            onStart: () => {
                                Swal.fire({
                                    title: "ACTUALIZANDO",
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
                                    title: "¡ÉXITO!",
                                    allowOutsideClick: false,
                                    preConfirm: (result) => {
                                        $("#mdlDatosActualizacion").css(
                                            "display",
                                            "none",
                                        );

                                        this.datos_cliente.telefonos =
                                            JSON.stringify(
                                                this.frmDatosCliente.telefonos,
                                            );
                                        this.datos_cliente.correo_electronico =
                                            this.frmDatosCliente.correo_electronico;
                                    },
                                });
                            },
                        },
                    );
                }
            });
        },

        GuardarPropuesta() {
            Swal.fire({
                icon: "question",
                text: "¿Desea registrar la PROPUESTA?",
                confirmButtonText: "Si",
                showCancelButton: true,
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = new FormData();
                    data.append("agencia_id", this.agencia_id);
                    data.append(
                        "frmPropuesta",
                        JSON.stringify(this.frmPropuesta),
                    );

                    // this.$inertia.post(route("cre.propuesta.guardar"), data);
                    // return false;

                    this.$inertia.post(route("cre.propuesta.guardar"), data, {
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
                            this.submited = false;
                            Swal.fire({
                                icon: "success",
                                title: "¡ÉXITO!",
                                allowOutsideClick: false,
                            });
                        },
                    });
                }
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

            Swal.fire({
                title: "GENERANDO",
                text: "Espere porfavor...",
                allowOutsideClick: false,
                didOpen: async () => {
                    // this.$inertia.post(route("cre.propuesta.exportar"), data);
                    // return false;

                    Swal.showLoading();
                    await axios
                        .post(route("cre.propuesta.exportar"), data)
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
    },
};
</script>

<style lang="css">
.slot-propuesta {
    width: 55% !important;
    margin-left: 22.5% !important;
}
.text2 {
    display: none;
}

#tblCronograma td {
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
.mdlDatosActualizacion {
    margin-top: 2%;
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

@media (max-width: 900px) {
    .slot-propuesta {
        width: 99% !important;
        margin-left: 0.5% !important;
    }
    .text1 {
        display: none;
    }
    .text2 {
        display: contents;
    }
    .element {
        margin-top: 17px;
    }
}
</style>
