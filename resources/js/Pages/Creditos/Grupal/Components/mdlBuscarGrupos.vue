<template>
    <div id="mdlBuscarGrupos" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-55 mdlBuscarGrupos">
            <div class="content contentBusquedaGrupos" style="display: block">
                <div class="card">
                    <headerCloseModal
                        titulo_modal="BUSCAR GRUPOS"
                        :nombre_modal="'mdlBuscarGrupos'"
                    ></headerCloseModal>
                    <div class="card-body card-block">
                        <div
                            class="form-row row justify-content-md-center mt-2"
                        >
                            <div class="form-group col-md-4 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            >AGENCIA</span
                                        >
                                    </div>
                                    <select
                                        class="form-control center"
                                        v-model="agencia_buscar"
                                        @change="Buscar"
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
                            <div class="form-group col-md-8 col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            ><i class="fas fa-search"></i
                                        ></span>
                                    </div>
                                    <input
                                        class="form-control mayus"
                                        type="text"
                                        placeholder="Ingrese 3 caractéres como mínimo..."
                                        @keyup="Buscar"
                                        v-model="nombre_grupo"
                                        autocomplete="off"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="card-title mb-1">GRUPOS ENCONTRADOS</div>

                        <DataTable
                            :value="lista_grupos"
                            :scrollable="true"
                            scrollDirection="both"
                            scrollHeight="200px"
                            :rows="50"
                            :selectionMode="'single'"
                            @row-dblclick="AbrirModulo"
                        >
                            <Column
                                field="agencia"
                                header="AGENCIA"
                                :styles="{
                                    width: '80px',
                                    justifyContent: 'center',
                                }"
                            />
                            <Column
                                field="nombre"
                                header="NOMBRE"
                                :styles="{
                                    width: '250px',
                                    justifyContent: 'left',
                                }"
                            />

                            <Column
                                field="asesor"
                                header="ASESOR"
                                :styles="{
                                    width: '100px',
                                    justifyContent: 'center',
                                }"
                            />

                            <template #empty
                                >No se encontraron grupos.</template
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
            nombre_grupo: null,
            agencia_buscar: null,

            lista_grupos: [],

            ruta_modulo: null,
            nombre_modulo: null,
        };
    },

    watch: {
        nombre_modulo(value) {
            if (value == "solicitud") {
                this.ruta_modulo = "gru.solicitud";
            }
        },

        agencias_permiso(value) {
            let agencia_id = this.$inertia.page.props.user_session.id_agencia;
            let mi_agencia = value.filter((item) => item.id == agencia_id);

            if (mi_agencia.length > 0) {
                this.agencia_buscar = mi_agencia[0].id;
            } else {
                if (value.length > 0) {
                    this.agencia_buscar = value[0].id;
                } else {
                    this.agencia_buscar = null;
                }
            }
            this.Buscar();
        },
    },
    methods: {
        async Buscar() {
            let nombre_grupo = this.nombre_grupo;

            if (this.agencia_buscar == null) {
                return false;
            }

            if (nombre_grupo && nombre_grupo.length >= 3) {
                const response = await axios.get(
                    route("gru.buscar_grupos", {
                        nombre: nombre_grupo,
                        agencia_id: this.agencia_buscar,
                    }),
                );

                this.lista_grupos = response.data.lista_grupos;
            } else {
                this.lista_grupos = [];
            }
        },
        async AbrirModulo(event) {
            let object = {};
            if (this.nombre_modulo == "solicitud") {
                object = {
                    agencia_id: this.agencia_buscar,
                    grupo_id: event.data.id,
                    grupo_solicitud_id: 0,
                };
            }
            this.$inertia.get(route(this.ruta_modulo, object));
        },
        CerrarModal(modal) {
            $("#" + modal).css("display", "none");
        },
    },
};
</script>

<style lang="css">
@media (max-width: 900px) {
    .mdlBuscarGrupos {
        width: 99%;
        margin-left: 0.5%;
    }
}
</style>
