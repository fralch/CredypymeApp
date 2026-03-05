<template>
    <layout ref="layout">
        <div class="slot_body slot-desembolso-grupo" slot="component-view">
            <div class="content" style="display: block">
                <div class="card">
                    <headerClose
                        :title="'DESEMBOLSO - CRÉDITO GRUPAL'"
                    ></headerClose>

                    <div class="card-body card-block">
                        <div class="form-row">
                            <div class="form-group col-md-5 col-12">
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

                            <div class="form-group col-md-4 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label
                                            class="input-group-text label-title"
                                            >F. APROBACIÓN</label
                                        >
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control center bolder"
                                        :value="frmSolicitud.fecha_aprobacion"
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
                                    style="max-height: 435px; overflow-y: auto"
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
                                                    type="text"
                                                    class="form-control center bolder"
                                                    style="font-size: 15px"
                                                    step="10"
                                                    lang="en"
                                                    :value="item.monto"
                                                    :readOnly="bloqueado"
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
                                                            item.monto_retencion,
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
                                        :disabled="bloqueado"
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
                                        style="font-size: 15px"
                                        step="1"
                                        lang="en"
                                        :value="frmSolicitud.plazo"
                                        :disabled="bloqueado"
                                    />
                                </div>
                                <div class="input-group">
                                    <label class="input-group-text bolder"
                                        >TASA INTERÉS %
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control center bolder"
                                        style="font-size: 15px"
                                        step="1"
                                        :min="1"
                                        lang="en"
                                        :value="frmSolicitud.tasa_interes"
                                        :disabled="bloqueado"
                                    />
                                </div>
                                <div class="input-group">
                                    <label class="input-group-text bolder"
                                        >TASA RETENCIÓN %
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control center bolder"
                                        style="font-size: 15px"
                                        step="1"
                                        :min="8"
                                        :max="15"
                                        lang="en"
                                        :value="frmSolicitud.tasa_retencion"
                                        :disabled="bloqueado"
                                    />
                                </div>

                                <div class="mt-1 mb-1">
                                    <div class="text-center">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            title="Generar cronograma"
                                            @click="CalcularCronograma"
                                            :disabled="bloqueado"
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
                                    :scrollHeight="'270px'"
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
                                            fontWeight: 'bold',
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
                        <div class="form-row">
                            <div class="col-md-8">
                                <div class="form-row text-left">
                                    <div class="input-group col-md-6 col-6">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text label-title"
                                                style="font-size: 15px"
                                                >TOTAL MONTO
                                            </span>
                                        </div>

                                        <input
                                            type="text"
                                            class="form-control center bolder"
                                            :value="
                                                'S/ ' + roundTo(total_monto, 2)
                                            "
                                            style="font-size: 18px"
                                            readonly
                                        />
                                    </div>
                                    <div class="input-group col-md-6 col-6">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text label-title"
                                                style="font-size: 15px"
                                                >TOTAL RETENCIÓN
                                            </span>
                                        </div>

                                        <input
                                            type="text"
                                            class="form-control center bolder"
                                            :value="
                                                'S/ ' +
                                                roundTo(total_retencion, 2)
                                            "
                                            style="font-size: 18px"
                                            readonly
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-right">
                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="Desembolsar"
                                            v-if="grupo_desembolso_id == null"
                                        >
                                            <span class="icon text-white">
                                                <i class="pi pi-dollar"></i
                                            ></span>
                                            <span class="text"
                                                >DESEMBOLSAR</span
                                            >
                                        </button>

                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="Imprimir('cronograma')"
                                            v-if="grupo_desembolso_id != null"
                                        >
                                            <span class="icon text-white">
                                                <i class="pi pi-print"></i
                                            ></span>
                                            <span class="text">CRONOGRAMA</span>
                                        </button>
                                        <button
                                            class="btn btn-action btn-icon-split"
                                            @click="Imprimir('voucher')"
                                            v-if="grupo_desembolso_id != null"
                                        >
                                            <span class="icon text-white">
                                                <i class="pi pi-print"></i
                                            ></span>
                                            <span class="text">VOUCHER</span>
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
// Componentes del proyecto
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import TabView from "primevue/tabview/tabview.common";
import TabPanel from "primevue/tabpanel/tabpanel.common";

export default {
    props: {
        agencia_id: Number,
        grupo_id: Number,
        grupo_solicitud_id: Number,
        grupo_desembolso_id: Number,
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

            datos_grupo: {},
            plazos: [],
            datos_calendario: [],

            frmSolicitud: {
                grupo_clientes: [],
                periodo_pago: null,
                plazo: 0,
                tasa_interes: 0,
                tasa_retencion: 0,
                fecha_solicitud: null,
                fecha_aprobacion: null,
            },
        };
    },

    computed: {
        datos_caja() {
            return this.$page.props.creditos_datos.datos_caja;
        },
        bloqueado() {
            return true;
        },
        total_monto() {
            return this.frmSolicitud.grupo_clientes.reduce(
                (total, item) => total + parseFloat(item.monto || 0),
                0,
            );
        },
        total_retencion() {
            return this.frmSolicitud.grupo_clientes.reduce(
                (total, item) =>
                    total +
                    (parseFloat(this.frmSolicitud.tasa_retencion || 0) / 100) *
                        parseFloat(item.monto || 0),
                0,
            );
        },
    },

    mounted() {
        this.ListarDatos();
    },
    methods: {
        async ListarDatos() {
            const params = {
                agencia_id: this.agencia_id,
                grupo_id: this.grupo_id,
                grupo_solicitud_id: this.grupo_solicitud_id,
            };

            return await axios
                .get(route("gru.desembolso.listar_datos"), { params })
                .then((response) => {
                    this.datos_grupo = response.data.datos_grupo;

                    const grupo_solicitud = response.data.grupo_solicitud;
                    const grupo_clientes = response.data.grupo_clientes;

                    this.frmSolicitud.grupo_clientes = grupo_clientes;

                    this.frmSolicitud.periodo_pago =
                        grupo_solicitud.periodo_pago;
                    this.frmSolicitud.plazo = grupo_solicitud.plazo;
                    this.frmSolicitud.tasa_interes =
                        grupo_solicitud.tasa_interes;
                    this.frmSolicitud.tasa_retencion =
                        grupo_solicitud.tasa_retencion;
                    this.frmSolicitud.fecha_solicitud =
                        grupo_solicitud.fecha_solicitud;
                    this.frmSolicitud.fecha_aprobacion =
                        grupo_solicitud.fecha_aprobacion;

                    this.CalcularCronograma();
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
            //     route("gru.aprobacion.calcular_cronograma"),
            //     params,
            // );
            // return false;

            await axios
                .get(route("gru.aprobacion.calcular_cronograma"), { params })
                .then((response) => {
                    this.frmSolicitud.grupo_clientes =
                        response.data.cuotas_clientes;

                    this.datos_calendario = response.data.datos_calendario;
                });
        },

        async Desembolsar() {
            this.submited = true;

            Swal.fire({
                icon: "question",
                title: "¿DESEA DESEMBOLSAR ESTE CRÉDITO?",
                confirmButtonText: "Si",
                showCancelButton: true,
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = new FormData();

                    data.append("agencia_id", this.agencia_id);
                    data.append("grupo_solicitud_id", this.grupo_solicitud_id);
                    data.append("agencia_caja", this.datos_caja.agencia_id);
                    data.append("caja_id", this.datos_caja.id);

                    this.$inertia.post(route("gru.desembolso.guardar"), data);
                    return false;

                    Swal.fire({
                        title: "DESEMBOLSANDO",
                        showConfirmButton: false,
                        allowOutsideClick: false,

                        willOpen: async () => {
                            Swal.showLoading();

                            return await axios
                                .post(route("gru.desembolso.guardar"), data)
                                .then(async (response) => {
                                    const params = {
                                        agencia_id: this.agencia_id,

                                        grupo_id: this.grupo_id,
                                        grupo_solicitud_id:
                                            this.grupo_solicitud_id,
                                        grupo_desembolso_id:
                                            this.grupo_solicitud_id,
                                    };
                                    this.$inertia.get(
                                        route("gru.aprobacion", params),
                                    );

                                    await Swal.close();
                                    return Swal.fire({
                                        icon: "success",
                                        title: response.data.message,
                                        timer: 1200,
                                        showConfirmButton: false,
                                    });
                                })
                                .catch((error) => {
                                    Swal.showValidationMessage(
                                        `Ha ocurrido un error, comunicar a TI: ${error}`,
                                    );
                                });
                        },
                    });
                }
            });
        },

        async Desaprobar() {
            Swal.fire({
                icon: "question",
                title: "¿DESEA DESAPROBAR ESTA SOLICITUD?",
                confirmButtonText: "Si",
                showCancelButton: true,
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    const params = {
                        agencia_id: this.agencia_id,
                        grupo_solicitud_id: this.grupo_solicitud_id,
                    };

                    // this.$inertia.post(route("gru.aprobacion.desaprobar"), params);
                    // return false;

                    Swal.fire({
                        title: "DESAPROBANDO",
                        showConfirmButton: false,
                        allowOutsideClick: false,

                        willOpen: async () => {
                            Swal.showLoading();

                            return await axios
                                .post(
                                    route("gru.aprobacion.desaprobar"),
                                    params,
                                )
                                .then(async (response) => {
                                    const params = {
                                        agencia_id: this.agencia_id,

                                        grupo_id: this.grupo_id,
                                        grupo_solicitud_id:
                                            response.data.grupo_solicitud_id,
                                    };
                                    this.$inertia.get(
                                        route("gru.aprobacion", params),
                                    );

                                    await Swal.close();
                                    return Swal.fire({
                                        icon: "success",
                                        title: response.data.message,
                                        timer: 1200,
                                        showConfirmButton: false,
                                    });
                                })
                                .catch((error) => {
                                    Swal.showValidationMessage(
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
            data.append("grupo_solicitud_id", this.grupo_solicitud_id);
            data.append("grupo_id", this.grupo_id);

            // this.$inertia.post(route("gru.aprobacion.exportar"), data);
            // return false;

            Swal.fire({
                title: "GENERANDO",
                text: "Espere porfavor...",
                allowOutsideClick: false,
                didOpen: async () => {
                    Swal.showLoading();
                    await axios
                        .post(route("gru.aprobacion.exportar"), data)
                        .then(async (response) => {
                            const origin = window.location.origin;
                            const path_pdf = response.data.path_pdf;

                            const iframe = document.createElement("iframe");
                            iframe.style.display = "none";
                            iframe.src = origin + path_pdf;
                            document.body.appendChild(iframe);

                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();

                            await Swal.close();
                            return Swal.fire({
                                icon: "success",
                                title: response.data.message,
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
.slot-desembolso-grupo {
    width: 70% !important;
    margin-left: 15% !important;
}

@media (max-width: 900px) {
    //MOBILE SCREEN
    .slot-desembolso-grupo {
        width: 98% !important;
        margin-left: 1% !important;
    }
}
</style>
