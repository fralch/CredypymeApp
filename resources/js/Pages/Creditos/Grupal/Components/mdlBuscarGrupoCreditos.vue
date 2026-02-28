<template>
    <div id="mdlBuscarGrupoCreditos" class="modal">
        <div class="modal-content w-50 mdlBuscarGrupoCreditos">
            <div class="content" style="display: block">
                <div class="card">
                    <headerCloseModal
                        :titulo_modal="'GRUPO - BUSCAR ' + titulo_modal"
                        :nombre_modal="'mdlBuscarGrupoCreditos'"
                    ></headerCloseModal>
                    <div class="card-body card-block">
                        <div
                            class="form-row row justify-content-md-center mt-2"
                        >
                            <div
                                class="form-group col-md-8 col-12"
                                v-if="nombre_modulo === 'cobranza'"
                            >
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            ><i class="fas fa-search"></i
                                        ></span>
                                    </div>
                                    <input
                                        id="inpBuscarCredito"
                                        class="form-control mayus"
                                        type="text"
                                        placeholder="Ingrese 3 caractéres como mínimo..."
                                        @keyup="BuscarCreditos"
                                        v-model="filtros_tabla['grupo'].value"
                                        autocomplete="off"
                                        @focus="hidenav()"
                                        @blur="shownav()"
                                        ref="buscar_grupo"
                                    />
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            >Ag.</span
                                        >
                                    </div>
                                    <select
                                        class="form-control center"
                                        v-model="agencia_seleccionada"
                                        @change="BuscarCreditos"
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
                        <div class="card-title mb-1">LISTA DE RESULTADOS</div>

                        <DataTable
                            :value="lista_grupos_creditos"
                            :scrollable="true"
                            scrollDirection="both"
                            scrollHeight="300px"
                            showGridlines
                            :rows="50"
                            :selectionMode="'single'"
                            @row-dblclick="AbrirModulo"
                        >
                            <Column
                                field="nombre_grupo"
                                header="GRUPO"
                                :styles="{
                                    width: '170px',
                                    justifyContent: 'left',
                                }"
                            />
                            <Column
                                field="monto"
                                header="MONTO"
                                :styles="{
                                    width: '80px',
                                    justifyContent: 'right',
                                }"
                            >
                                <template #body="{ data }"
                                    >S/ {{ roundTo(data.monto, 2) }}</template
                                >
                            </Column>
                            <Column
                                field="plazo"
                                header="PLAZO"
                                :styles="{
                                    width: '90px',
                                    justifyContent: 'center',
                                }"
                            >
                                <template #body="{ data }">
                                    {{
                                        data.plazo +
                                        " " +
                                        periodo_medicion(data.periodo_pago)
                                    }}
                                </template>
                            </Column>
                            <Column
                                field="tasa_interes"
                                header="TASA"
                                :styles="{
                                    width: '80px',
                                    justifyContent: 'center',
                                }"
                            >
                                <template #body="{ data }">
                                    {{ data.tasa_interes + "%" }}
                                </template>
                            </Column>

                            <Column
                                field="asesor"
                                header="ASESOR"
                                :styles="{
                                    width: '100px',
                                    justifyContent: 'center',
                                }"
                            />
                            <Column
                                :field="
                                    datos_fechas === undefined
                                        ? null
                                        : datos_fechas.field
                                "
                                :header="
                                    datos_fechas === undefined
                                        ? null
                                        : datos_fechas.header
                                "
                                :styles="{
                                    width: '118px',
                                    justifyContent: 'center',
                                }"
                            />
                            <Column
                                field="usuario_registro"
                                header="USU_REG"
                                :styles="{
                                    width: '100px',
                                    justifyContent: 'center',
                                }"
                            />

                            <template #empty
                                >No se encontraron resultados.</template
                            >
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
    components: { headerClose, headerCloseModal, DataTable, Column },
    data() {
        return {
            agencias_permiso: [],
            agencia_seleccionada: null,
            lista_grupos_creditos: [],
            ruta: null,
            nombre_modulo: null,
        };
    },
    computed: {
        titulo_modal() {
            if (
                this.nombre_modulo == "copia_solicitud" ||
                this.nombre_modulo == "aprobacion"
            ) {
                return "SOLICITUDES";
            }
            if (
                this.nombre_modulo == "copia_aprobacion" ||
                this.nombre_modulo == "desembolso"
            ) {
                return "APROBACIONES";
            }
            if (this.nombre_modulo == "cobranza") {
                return "DESEMBOLSOS";
            }
        },
        datos_fechas() {
            if (
                this.nombre_modulo == "copia_solicitud" ||
                this.nombre_modulo == "aprobacion"
            ) {
                return {
                    field: "fecha_solicitud",
                    header: "FECHA_SOLICITUD",
                };
            }
            if (
                this.nombre_modulo == "copia_aprobacion" ||
                this.nombre_modulo == "desembolso"
            ) {
                return {
                    field: "fecha_aprobacion",
                    header: "FECHA_APROBADO",
                };
            }
            if (this.nombre_modulo == "cobranza") {
                return {
                    field: "fecha_desembolso",
                    header: "FECHA_DESEMBOLSO",
                    styles: {
                        width: "118px",
                        justifyContent: "center",
                    },
                };
            }
        },
    },

    watch: {
        nombre_modulo(value) {
            if (value == "copia_solicitud") {
                this.ruta = "gru.solicitud";
            }
            if (value == "aprobacion" || value == "copia_aprobacion") {
                this.ruta = "gru.aprobacion";
            }
            if (value == "desembolso") {
                this.ruta = "gru.desembolso";
            }
            if (value == "cobranza") {
                this.ruta = "gru.cobranza";
            }
        },

        agencias_permiso(value) {
            let agencia_id = this.$inertia.page.props.user_session.id_agencia;
            let mi_agencia = value.filter((item) => item.id == agencia_id);

            if (mi_agencia.length > 0) {
                this.agencia_seleccionada = mi_agencia[0].id;
            } else {
                if (value.length > 0) {
                    this.agencia_seleccionada = value[0].id;
                } else {
                    this.agencia_seleccionada = null;
                }
            }
            this.BuscarCreditos();
        },
    },

    methods: {
        async BuscarCreditos() {
            if (this.agencia_seleccionada == null) {
                return false;
            }

            // this.$inertia.get(
            //    route('cre.gru.propuesta.listar_creditos', {
            //       agencia_id: this.agencia_seleccionada,
            //    })
            // );
            // return false;

            if (
                this.nombre_modulo == "copia_solicitud" ||
                this.nombre_modulo == "aprobacion"
            ) {
                const response = await axios.get(
                    route("gru.solicitud.buscar", {
                        agencia_id: this.agencia_seleccionada,
                    }),
                );
                this.lista_grupos_creditos =
                    response.data.lista_grupos_creditos;
            } else if (
                this.nombre_modulo == "copia_aprobacion" ||
                this.nombre_modulo == "desembolso"
            ) {
                const response = await axios.get(
                    route("gru.aprobacion.buscar", {
                        agencia_id: this.agencia_seleccionada,
                    }),
                );
                this.lista_grupos_creditos =
                    response.data.lista_grupos_creditos;
            } else if (this.nombre_modulo == "cobranza") {
                const response = await axios.get(
                    route("gru.cobranza.buscar", {
                        agencia_id: this.agencia_seleccionada,
                    }),
                );
                this.lista_grupos_creditos =
                    response.data.lista_grupos_creditos;
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
        roundTo(value, decimal_places) {
            let valor = 0;
            let numero_decimales = decimal_places;

            if (value) {
                valor = value;
            }

            return parseFloat(valor).toFixed(numero_decimales);
        },
        async AbrirModulo(event) {
            let object = {};
            if (this.nombre_modulo == "copia_solicitud") {
                object = {
                    agencia_id: this.agencia_seleccionada,
                    grupo_id: event.data.grupo_id,
                    grupo_solicitud_id: event.data.id,
                };
            }
            if (this.nombre_modulo == "aprobacion") {
                object = {
                    agencia_id: this.agencia_seleccionada,
                    grupo_id: event.data.grupo_id,
                    grupo_solicitud_id: event.data.id,
                    grupo_aprobacion_id: null,
                };
            }
            if (this.nombre_modulo == "copia_aprobacion") {
                object = {
                    agencia_id: this.agencia_seleccionada,
                    grupo_id: event.data.grupo_id,
                    grupo_solicitud_id: event.data.id,
                    grupo_aprobacion_id: event.data.id,
                };
            }
            if (this.nombre_modulo == "desembolso") {
                object = {
                    agencia_id: this.agencia_seleccionada,
                    grupo_id: event.data.grupo_id,
                    grupo_solicitud_id: event.data.id,
                    grupo_desembolso_id: null,
                };
            }
            if (this.nombre_modulo == "cobranza") {
                object = {
                    agencia_id: this.agencia_seleccionada,
                    grupo_id: event.data.grupo_id,
                    grupo_solicitud_id: event.data.id,
                };
            }

            this.$inertia.get(route(this.ruta, object));
        },
    },
};
</script>

<style lang="css">
.mdlBuscarGrupoCreditos {
    margin-top: 5% !important;
    width: 60% !important;
    margin-left: 25% !important;
}

@media (max-width: 1536px) {
    .mdlBuscarGrupoCreditos {
        width: 60% !important;
        margin-left: 20% !important;
    }
}

@media (max-width: 1366px) {
    .mdlBuscarGrupoCreditos {
        width: 65% !important;
        margin-left: 17.5% !important;
    }
}

@media (max-width: 900px) {
    .mdlBuscarGrupoCreditos {
        width: 99% !important;
        margin-left: 0.5% !important;
        margin-top: 12% !important;
    }
}
</style>
