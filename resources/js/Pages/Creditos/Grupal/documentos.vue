<template>
    <layout ref="layout">
        <div class="slot_body slot-documentos-grupo" slot="component-view">
            <div class="content" style="display: block">
                <div class="card">
                    <headerClose :title="'GRUPO - DOCUMENTOS'"></headerClose>

                    <div class="card-title">LISTA DE CRÉDITOS APROBADOS</div>
                    <div class="card-body card-block">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            >Ag.</span
                                        >
                                    </div>
                                    <select
                                        class="form-control center"
                                        v-model="agencia_busqueda"
                                        @change="ListarCreditos"
                                    >
                                        <option
                                            v-for="(
                                                agencia, index
                                            ) in agencias_permiso"
                                            :key="index"
                                            :value="agencia.id"
                                        >
                                            {{ agencia.agencia }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-9">
                                <DataTable
                                    :value="lista_creditos"
                                    :scrollable="true"
                                    scrollDirection="both"
                                    scrollHeight="300px"
                                    showGridlines
                                    :rows="100"
                                    selectionMode="single"
                                    :selection="credito_seleccionado"
                                    @update:selection="
                                        credito_seleccionado = $event
                                    "
                                >
                                    <Column
                                        header="N°"
                                        :styles="{
                                            width: '40px',
                                            justifyContent: 'left',
                                        }"
                                    >
                                        <template #body="{ index }">
                                            {{ index + 1 }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="nombre_grupo"
                                        header="GRUPO"
                                        :styles="{
                                            width: '300px',
                                            justifyContent: 'left',
                                        }"
                                    ></Column>

                                    <Column
                                        field="fecha_aprobacion"
                                        header="FECHA_APROBADO"
                                        :styles="{
                                            width: '130px',
                                            justifyContent: 'center',
                                        }"
                                    ></Column>

                                    <Column
                                        field="monto"
                                        header="MONTO"
                                        :styles="{
                                            width: '100px',
                                            justifyContent: 'right',
                                        }"
                                    >
                                        <template #body="{ data }"
                                            >S/
                                            {{
                                                roundTo(data.monto, 2)
                                            }}</template
                                        >
                                    </Column>

                                    <Column
                                        field="periodo_pago"
                                        header="PLAZO_PERIODO"
                                        :styles="{
                                            width: '110px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{ roundTo(data.plazo, 0) }}
                                            {{
                                                periodo_medicion(
                                                    data.periodo_pago,
                                                )
                                            }}
                                        </template>
                                    </Column>

                                    <Column
                                        field="usuario_asesor"
                                        header="ASESOR"
                                        :styles="{
                                            width: '120px',
                                            justifyContent: 'center',
                                        }"
                                    >
                                        <template #body="{ data }">
                                            {{ data.usuario_asesor }}
                                        </template>
                                    </Column>

                                    <template #empty
                                        >No se encontraron resultados.</template
                                    >
                                </DataTable>
                            </div>
                            <div class="col-md-3">
                                <fieldset
                                    class="col-md-12 p-2"
                                    style="background: white"
                                >
                                    <div class="form-row">
                                        <div class="col-md-12 col-4">
                                            <button
                                                class="btn btn-action btn-icon-split mt-1"
                                                title="Imprimir PAGARÉ"
                                                style="width: 100% !important"
                                                @click="Imprimir('pagare')"
                                            >
                                                <span class="icon-text">
                                                    <i class="fas fa-print"></i>
                                                </span>
                                                <span class="text">PAGARÉ</span>
                                            </button>
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <button
                                                class="btn btn-action btn-icon-split mt-1"
                                                title="Imprimir CONTRATO"
                                                style="width: 100% !important"
                                                @click="Imprimir('contrato')"
                                            >
                                                <span class="icon-text">
                                                    <i class="fas fa-print"></i>
                                                </span>
                                                <span class="text"
                                                    >CONTRATO</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                </fieldset>
                                <hr />
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

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

export default {
    props: {},
    components: {
        layout,
        headerClose,

        DataTable,
        Column,
    },

    data() {
        return {
            agencias_permiso: [],
            agencia_busqueda: null,
            lista_creditos: [],
            fechas_vencimiento: [],
            credito_seleccionado: null,
        };
    },
    watch: {
        agencias_permiso(value) {
            let agencia_id = this.$inertia.page.props.user_session.id_agencia;
            let mi_agencia = value.filter((item) => item.id == agencia_id);

            if (mi_agencia.length > 0) {
                this.agencia_busqueda = mi_agencia[0].id;
            } else {
                if (value.length > 0) {
                    this.agencia_busqueda = value[0].id;
                } else {
                    this.agencia_busqueda = null;
                }
            }
            this.ListarCreditos();
        },
    },
    mounted() {
        this.ListarAgencias();
    },

    methods: {
        ListarAgencias() {
            this.agencias = this.$inertia.page.props.application.agencias;
            this.agencias_permiso = this.$refs.layout.filtrar_agencias(
                "CREDITOS_GRUPAL/DOCUMENTOS",
            );
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

        async ListarCreditos() {
            const params = {
                agencia_id: this.agencia_busqueda,
            };

            // this.$inertia.get(route('gru.documentos.listar_creditos'), params);
            // return false;

            await axios
                .get(route("gru.documentos.listar_creditos"), {
                    params,
                })
                .then((response) => {
                    this.lista_creditos = response.data.lista_creditos;
                });
        },

        async Imprimir(tipo) {
            if (this.credito_seleccionado == null) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Seleccione una crédito.",
                    allowOutsideClick: true,
                });
                return false;
            }

            let data = new FormData();
            data.append("tipo", tipo);
            data.append("agencia_id", this.agencia_busqueda);
            data.append(
                "datos_credito",
                JSON.stringify(this.credito_seleccionado),
            );

            // this.$inertia.post(route("gru.documentos.generar"), data);
            // return false;

            Swal.fire({
                title: "GENERANDO",
                text: "Espere porfavor...",
                allowOutsideClick: false,
                didOpen: async () => {
                    Swal.showLoading();

                    const response = await axios.post(
                        route("gru.documentos.generar"),
                        data,
                    );

                    let origin = window.location.origin;
                    let path_pdf = response.data.path_pdf;

                    let iframe = document.createElement("iframe");
                    iframe.style.display = "none";
                    iframe.src = origin + path_pdf;
                    document.body.appendChild(iframe);

                    await Swal.fire({
                        icon: "success",
                        title: response.data.message,
                        timer: 1200,
                        showConfirmButton: false,
                    });

                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    await this.ListarCreditos();
                },
            });
        },
    },
};
</script>

<style lang="scss">
.slot-documentos-grupo {
    width: 99% !important;
    margin-left: 0.5%;
}

@media (min-width: 900px) {
    .slot-documentos-grupo {
        width: 60% !important;
        margin-left: 20%;
    }
}

@media (min-width: 1400px) {
    .slot-documentos-grupo {
        width: 50% !important;
        margin-left: 25%;
    }
}

@media (min-width: 1900px) {
    .slot-documentos-grupo {
        width: 40% !important;
        margin-left: 30%;
    }
}
</style>
