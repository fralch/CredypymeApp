<template>
    <layout ref="layout">
        <div class="slot_body slot-solicitud-grupo" slot="component-view">
            <div class="content" style="display: block">
                <div class="card">
                    <headerClose
                        :title="'SOLICITUD DE CRÉDITO GRUPAL'"
                    ></headerClose>

                    <div class="card-body card-block">
                        <div class="form-row">
                            <div class="form-group col-md-6 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label
                                            class="input-group-text label-title"
                                            >NOMBRE</label
                                        >
                                    </div>

                                    <input
                                        type="text"
                                        class="form-control bolder"
                                        :value="datos_grupo.nombre"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div class="form-group col-md-3 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label
                                            class="input-group-text label-title"
                                            >ASESOR</label
                                        >
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control center bolder"
                                        :value="datos_grupo.asesor"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div class="form-group col-md-3 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label
                                            class="input-group-text label-title"
                                            >F. SOLICITUD</label
                                        >
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control center bolder"
                                        :value="frmSolicitud.fecha_solicitud"
                                        disabled
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="card-title mb-1">
                            INFORMACIÓN DEL CRÉDITO
                        </div>
                        <div class="form-row">
                            <div class="col-md-8">
                                <div
                                    style="max-height: 400px; overflow-y: auto"
                                >
                                    <fieldset
                                        v-for="(
                                            item, index
                                        ) in frmSolicitud.grupo_clientes"
                                        :key="index"
                                        class="mb-2 p-2"
                                        style="background-color: #d8f1fd"
                                    >
                                        <legend class="bolder">
                                            <span
                                                style="
                                                    font-style: italic;
                                                    background-color: aquamarine;
                                                "
                                                >{{ item.cliente }}</span
                                            >
                                            <span
                                                style="background-color: yellow"
                                                v-if="item.responsable"
                                                >{{
                                                    " - (" +
                                                    item.responsable +
                                                    ")"
                                                }}</span
                                            >
                                        </legend>

                                        <div class="form-row">
                                            <div
                                                class="input-group col-md-4 col-6"
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text label-title"
                                                        style="font-size: 15px"
                                                        >MONTO S/
                                                    </span>
                                                </div>

                                                <input
                                                    type="number"
                                                    class="form-control center bolder"
                                                    style="font-size: 15px"
                                                    step="10"
                                                    lang="en"
                                                    :min="200"
                                                    :id="'inp_' + item.id"
                                                    v-model.number="item.monto"
                                                    name="monto"
                                                    @change="RedondearValor"
                                                />
                                            </div>
                                            <div
                                                class="input-group col-md-4 col-6"
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text label-title"
                                                        style="font-size: 15px"
                                                        >CUOTA S/
                                                    </span>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="form-control center bolder"
                                                    :value="
                                                        roundTo(item.cuota, 2)
                                                    "
                                                    name="cuota"
                                                    readonly
                                                />
                                            </div>
                                            <div
                                                class="input-group col-md-4 col-6"
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text label-title"
                                                        style="font-size: 15px"
                                                        >RETENCIÓN S/
                                                    </span>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="form-control center bolder"
                                                    :value="
                                                        roundTo(
                                                            (frmSolicitud.tasa_retencion /
                                                                100) *
                                                                parseFloat(
                                                                    item.monto,
                                                                ),
                                                            2,
                                                        )
                                                    "
                                                    readonly
                                                />
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <label class="input-group-text bolder"
                                        >PERIODO</label
                                    >
                                    <select
                                        class="form-control center bolder"
                                        style="font-size: 13px"
                                        v-model="frmSolicitud.periodo_pago"
                                        :disabled="
                                            modo_modulo == 'VER_SOLICITUD'
                                        "
                                    >
                                        <option value="SEMANAL">SEMANAL</option>
                                        <option value="QUINCENAL">
                                            QUINCENAL
                                        </option>
                                        <option value="MENSUAL">MENSUAL</option>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <label class="input-group-text bolder"
                                        >PLAZO
                                        {{
                                            periodo_medicion(
                                                frmSolicitud.periodo_pago,
                                            )
                                        }}</label
                                    >

                                    <input
                                        type="number"
                                        class="form-control center bolder"
                                        :class="[
                                            submited
                                                ? v$.frmSolicitud.plazo.$invalid
                                                    ? 'is-invalid'
                                                    : 'is-valid'
                                                : '',
                                        ]"
                                        style="font-size: 15px"
                                        step="1"
                                        lang="en"
                                        v-model.number="frmSolicitud.plazo"
                                        name="plazo"
                                        @change="RedondearValor"
                                        :disabled="
                                            modo_modulo == 'VER_SOLICITUD'
                                        "
                                    />
                                </div>
                                <div class="input-group">
                                    <label class="input-group-text bolder"
                                        >TASA INTERÉS %
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control center bolder"
                                        :class="[
                                            submited
                                                ? v$.frmSolicitud.tasa_interes
                                                      .$invalid
                                                    ? 'is-invalid'
                                                    : 'is-valid'
                                                : '',
                                        ]"
                                        style="font-size: 15px"
                                        step="1"
                                        :min="1"
                                        lang="en"
                                        v-model.number="
                                            frmSolicitud.tasa_interes
                                        "
                                        name="tasa_interes"
                                        @change="RedondearValor"
                                        :disabled="
                                            modo_modulo == 'VER_SOLICITUD'
                                        "
                                    />
                                </div>
                                <div class="input-group">
                                    <label class="input-group-text bolder"
                                        >TASA RETENCIÓN %
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control center bolder"
                                        :class="[
                                            submited
                                                ? v$.frmSolicitud.tasa_retencion
                                                      .$invalid
                                                    ? 'is-invalid'
                                                    : 'is-valid'
                                                : '',
                                        ]"
                                        style="font-size: 15px"
                                        step="1"
                                        :min="8"
                                        :max="15"
                                        lang="en"
                                        v-model.number="
                                            frmSolicitud.tasa_retencion
                                        "
                                        name="tasa_retencion"
                                        @change="RedondearValor"
                                        :disabled="
                                            modo_modulo == 'VER_SOLICITUD'
                                        "
                                    />
                                </div>

                                <div class="mt-1 mb-1">
                                    <div class="text-center">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            title="Generar cronograma"
                                            @click="CalcularCronograma"
                                            :disabled="
                                                modo_modulo == 'VER_SOLICITUD'
                                            "
                                        >
                                            <span class="icon text-white">
                                                <i class="pi pi-sync"></i
                                            ></span>
                                            <span class="text"
                                                >CALCULAR CUOTAS</span
                                            >
                                        </button>
                                    </div>
                                </div>

                                <DataTable
                                    :value="datos_calendario"
                                    :scrollable="true"
                                    scrollDirection="both"
                                    :scrollHeight="'300px'"
                                    showGridlines
                                    :rows="100"
                                >
                                    <Column
                                        field="orden"
                                        header="N°"
                                        :styles="{
                                            width: '50px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="fecha_pago"
                                        header="FECHA PAGO"
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <Column
                                        field="dia_pago"
                                        header="DÍA PAGO"
                                        :styles="{
                                            width: '80px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                    </Column>
                                    <template #empty>
                                        No hay CUOTAS generadas.</template
                                    >
                                </DataTable>
                            </div>
                        </div>

                        <hr />
                        <div class="text-right">
                            <div class="btn-group" role="group">
                                <button
                                    class="btn btn-action btn-icon-split"
                                    @click="Solicitar"
                                    v-if="modo_modulo != 'VER_SOLICITUD'"
                                >
                                    <span class="icon text-white">
                                        <i class="pi pi-check"></i
                                    ></span>
                                    <span class="text">REGISTRAR</span>
                                </button>
                                <button
                                    class="btn btn-cancel btn-icon-split"
                                    @click="modo_modulo = 'EDITAR_SOLICITUD'"
                                    v-if="modo_modulo == 'VER_SOLICITUD'"
                                >
                                    <span class="icon text-white">
                                        <i class="pi pi-pencil"></i
                                    ></span>
                                    <span class="text">EDITAR</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
// Componentes del proyecto
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import TabView from "primevue/tabview/tabview.common";
import TabPanel from "primevue/tabpanel/tabpanel.common";

import { required } from "vuelidate/lib/validators";
import { round } from "lodash";
import { forEach } from "lodash";
const noZero = (value) => value != 0;

export default {
    props: {
        modo: String,
        grupo_id: Number,
        agencia_id: Number,
        grupo_solicitud_id: Number,
    },

    components: {
        layout,
        headerClose,

        DataTable,
        Column,
        TabView,
        TabPanel,
    },

    data() {
        return {
            submited: false,
            modo_modulo: null,

            datos_grupo: {},
            plazos: [],
            datos_calendario: [],

            frmSolicitud: {
                grupo_clientes: [],
                periodo_pago: "MENSUAL",
                plazo: 6,
                tasa_interes: this.roundTo(9, 2),
                tasa_retencion: this.roundTo(8, 2),
                fecha_solicitud: null,
            },
        };
    },
    validations: {
        frmSolicitud: {
            plazo: { required, noZero },
            tasa_interes: { required, noZero },
            tasa_retencion: { required, noZero },
        },
    },

    computed: {
        datos_sesion() {
            return this.$page.props.sesion_usuario;
        },

        titulo_modulo() {
            if (this.modo == "SOLICITUD") {
                this.modo_modulo = "SOLICITUD";
                return "SOLICITUD GRUPAL";
            } else if (this.modo == "VER_SOLICITUD") {
                this.modo_modulo = "VER_SOLICITUD";
                return "VER SOLICITUD GRUPAL";
            } else if (this.modo == "EDITAR_SOLICITUD") {
                this.modo_modulo = "EDITAR_SOLICITUD";
                return "EDITAR SOLICITUD GRUPAL";
            }
        },
        bloqueado() {
            if (this.modo == "SOLICITUD" && this.frmSolicitud.id != null) {
                return true;
            }
            return false;
        },
    },

    mounted() {
        this.ListarDatos();
    },
    methods: {
        async ListarDatos() {
            const params = {
                modo: this.modo,
                agencia_id: this.agencia_id,
                grupo_id: this.grupo_id,
                grupo_solicitud_id: this.grupo_solicitud_id,
            };

            return await axios
                .get(route("gru.solicitud.listar_datos"), { params })
                .then((response) => {
                    this.datos_grupo = response.data.datos_grupo;

                    this.frmSolicitud.grupo_clientes =
                        response.data.grupo_clientes;

                    forEach(this.frmSolicitud.grupo_clientes, (item) => {
                        item.monto = this.roundTo(item.monto, 2);
                    });

                    if (this.grupo_solicitud_id) {
                        const grupo_clientess = response.data.grupo_clientess;
                        this.frmSolicitud.grupo_clientes = grupo_clientess;
                        this.frmSolicitud.periodo_pago =
                            grupo_clientess[0].periodo_pago;
                        this.frmSolicitud.plazo = grupo_clientess[0].plazo;
                        this.frmSolicitud.fecha_solicitud =
                            grupo_clientess[0].fecha_solicitud;
                        this.frmSolicitud.fecha_aprobacion =
                            grupo_clientess[0].fecha_aprobacion;
                        this.CalcularCronograma();
                    } else {
                        this.frmSolicitud.fecha_solicitud =
                            response.data.fecha_actual;
                        this.frmSolicitud.fecha_calculo =
                            response.data.fecha_actual;
                    }
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
        RedondearValor(e) {
            let valor = 0;
            let numero_decimales = 2;

            if (e.target.value && e.target.value >= 0) {
                valor = e.target.value;
            }

            if (e.target.name == "monto") {
                const grupo_cliente_id = e.target.id.split("_")[1];
                const integrante = this.frmSolicitud.grupo_clientes.find(
                    (item) => item.id == grupo_cliente_id,
                );

                const min = parseFloat(e.target.min);

                if (parseFloat(valor) < min) {
                    valor = min;
                }

                integrante.monto = this.roundTo(valor, numero_decimales);
            } else if (e.target.name == "plazo") {
                const min = 1;

                if (parseFloat(valor) < min) {
                    valor = min;
                }

                this.frmSolicitud.plazo = this.roundTo(valor, 0);
            } else if (e.target.name == "tasa_interes") {
                const min = parseFloat(e.target.min);

                if (parseFloat(valor) < min) {
                    valor = min;
                }

                this.frmSolicitud.tasa_interes = this.roundTo(valor, 2);
            } else if (e.target.name == "tasa_retencion") {
                const min = parseFloat(e.target.min);
                const max = parseFloat(e.target.max);

                if (parseFloat(valor) < min) {
                    valor = min;
                } else if (parseFloat(valor) > max) {
                    valor = max;
                }

                this.frmSolicitud.tasa_retencion = this.roundTo(valor, 2);
            }
        },
        periodo_medicion(value) {
            if (value == "SEMANAL") {
                return "(SEMANAS)";
            } else if (value == "QUINCENAL") {
                return "(QUINCENAS)";
            } else if (value == "MENSUAL") {
                return "(MESES)";
            }
        },

        async CalcularCronograma() {
            const params = {
                agencia_id: this.agencia_id,
                frmSolicitud: JSON.stringify(this.frmSolicitud),
            };

            // this.$inertia.get(
            //     route("gru.solicitud.calcular_cronograma"),
            //     params,
            // );
            // return false;

            await axios
                .get(route("gru.solicitud.calcular_cronograma"), { params })
                .then((response) => {
                    this.frmSolicitud.grupo_clientes =
                        response.data.cuotas_clientes;

                    this.datos_calendario = response.data.datos_calendario;
                });
        },

        async Solicitar() {
            await this.CalcularCronograma();
            this.submited = true;

            if (
                this.frmSolicitud.grupo_clientes.find((item) => item.monto <= 0)
            ) {
                this.$swal.fire({
                    icon: "warning",
                    title: "¡Error!",
                    text: "El monto solicitado no puede ser 0, revise por favor",
                    allowOutsideClick: true,
                    backdrop: true,
                });
                return false;
            }
            if (this.v$.frmSolicitud.$invalid) {
                this.$swal.fire({
                    icon: "warning",
                    title: "¡Error!",
                    text: "Complete todos los campos.",
                    allowOutsideClick: true,
                    backdrop: true,
                });
                return false;
            }

            const params = {
                agencia_id: this.agencia_id,
                grupo_id: this.grupo_id,
            };

            // this.$inertia.get(route("cre.solicitud_grupo.verificar"), params);
            // return false;

            const response = await axios.get(
                route("cre.solicitud_grupo.verificar"),
                {
                    params,
                },
            );

            let icon = null;
            let title = "¿Desea registrar la SOLICITUD?";
            let text = null;

            if (response.data.credito_observado) {
                icon = "warning";
                title = "¡Atención!";
                text =
                    "Este GRUPO tiene un crédito VIGENTE. ¿Desea continuar con la SOLICITUD?";
            }

            this.$swal
                .fire({
                    icon: icon,
                    title: title,
                    text: text,
                    confirmButtonText: "Si",
                    showCancelButton: true,
                    cancelButtonText: "No",
                    allowOutsideClick: false,
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        let data = new FormData();
                        data.append("modo", this.modo_modulo);
                        data.append("grupo_id", this.grupo_id);
                        data.append("asesor_id", this.datos_grupo.asesor_id);
                        data.append(
                            "grupo_solicitud_id",
                            this.grupo_solicitud_id,
                        );
                        data.append("agencia_id", this.agencia_id);
                        data.append(
                            "frmSolicitud",
                            JSON.stringify(this.frmSolicitud),
                        );

                        // this.$inertia.post(
                        //     route("cre.solicitud_grupo.guardar"),
                        //     data
                        // );
                        // return false;

                        this.$swal.fire({
                            title: "REGISTRANDO",
                            showConfirmButton: false,
                            allowOutsideClick: false,

                            willOpen: async () => {
                                this.$swal.showLoading();

                                return await axios
                                    .post(
                                        route("cre.solicitud_grupo.guardar"),
                                        data,
                                    )
                                    .then((response) => {
                                        const params = {
                                            grupo_solicitud_id:
                                                response.data
                                                    .grupo_solicitud_id,
                                            grupo_id: this.grupo_id,
                                            agencia_id: this.agencia_id,
                                            modo: "VER_SOLICITUD",
                                        };
                                        this.$inertia.get(
                                            route(
                                                "cre.solicitud_grupo",
                                                params,
                                            ),
                                        );
                                        return this.$swal.fire({
                                            icon: "success",
                                            title: response.data.message,
                                            timer: 1200,
                                            showConfirmButton: false,
                                        });
                                    })
                                    .catch((error) => {
                                        this.$swal.showValidationMessage(
                                            `Ha ocurrido un error, comunicar a TI: ${error}`,
                                        );
                                    });
                            },
                        });
                    }
                });
        },

        async Imprimir(solicitud_id) {
            let data = new FormData();
            data.append("agencia_id", this.agencia_id);
            data.append("solicitud_id", solicitud_id);
            data.append("grupo_id", this.grupo_id);

            // this.$inertia.post(route("cre.solicitud_grupo.exportar"), data);
            // return false;

            this.$swal.fire({
                title: "GENERANDO",
                text: "Espere porfavor...",
                allowOutsideClick: false,
                didOpen: async () => {
                    this.$swal.showLoading();
                    await axios
                        .post(route("cre.solicitud_grupo.exportar"), data)
                        .then((response) => {
                            const origin = window.location.origin;
                            const path_pdf = response.data.path_pdf;

                            // Crear un IFrame
                            const iframe = document.createElement("iframe");
                            // Oculto el iframe
                            iframe.style.display = "none";
                            // Defino el source
                            iframe.src = origin + path_pdf;
                            // Añadir el Iframe a la vista
                            document.body.appendChild(iframe);

                            iframe.contentWindow.focus(); // Enfoca
                            iframe.contentWindow.print(); // Imprime

                            return this.$swal.fire({
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

<style lang="scss">
.slot-solicitud-grupo {
    width: 70% !important;
    margin-left: 15% !important;
}

@media (max-width: 900px) {
    //MOBILE SCREEN
    .slot-solicitud-grupo {
        width: 98% !important;
        margin-left: 1% !important;
    }
}
</style>
