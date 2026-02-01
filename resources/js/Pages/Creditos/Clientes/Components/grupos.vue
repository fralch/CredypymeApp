<template>
    <div>
        <div class="content" style="display: block">
            <div class="card">
                <div class="card-body card-block">
                    <div class="form-row">
                        <div class="col-md-5 col-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">BUSCAR</span>
                                </div>

                                <input
                                    class="form-control mayus"
                                    type="text"
                                    placeholder="Ingrese 3 caractéres como mínimo..."
                                    :disabled="lista_grupos.length == 0"
                                    v-model="filtros_tabla['nombre'].value"
                                    autocomplete="off"
                                    spellcheck="false"
                                    autofocus
                                />
                            </div>
                        </div>
                        <div class="col-md-2 col-1">
                            <button
                                class="btn btn-action btn-icon-split mb-1"
                                @click="Nuevo"
                                title="Nuevo GRUPO"
                            >
                                <span class="icon text-white">
                                    <i class="fas fa-plus"></i>
                                </span>

                                <span class="text">NUEVO</span>
                            </button>
                        </div>
                        <div class="respon col-md-4 col-12 offset-md-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"
                                        >AGENCIA</span
                                    >
                                </div>
                                <select
                                    class="form-control center"
                                    v-model="agencia_seleccionada"
                                >
                                    <option
                                        v-for="item in agencias_permitidas"
                                        :key="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.agencia }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-title mt-2 mb-1">LISTA DE RESULTADOS</div>

                    <DataTable
                        :value="lista_grupos_filtrados"
                        :scrollable="true"
                        scrollDirection="both"
                        :scrollHeight="'300px'"
                        showGridlines
                        :rows="100"
                    >
                        <Column
                            header="VER"
                            :styles="{
                                width: '50px',
                                justifyContent: 'center',
                            }"
                        >
                            <template #body="{ data }">
                                <button
                                    class="btn btn-action btn-icon-split"
                                    @click="Ver(data)"
                                >
                                    <span class="icon text-white">
                                        <i class="far fa-eye"></i>
                                    </span>
                                </button>
                            </template>
                        </Column>

                        <Column
                            field="nombre"
                            header="GRUPO"
                            :styles="{ width: '275px', justifyContent: 'left' }"
                        />
                        <Column
                            field="usuario_asesor"
                            header="ASESOR"
                            :styles="{
                                width: '90px',
                                justifyContent: 'center',
                            }"
                        />

                        <Column
                            field="habilitado"
                            header="HABILITADO"
                            :styles="{
                                width: '80px',
                                justifyContent: 'center',
                            }"
                        >
                            <template #body="{ data }">
                                {{ data.habilitado ? "SI" : "NO" }}
                            </template>
                        </Column>
                        <Column
                            field="fecha_creacion"
                            header="FECHA_CREACIÓN"
                            :styles="{
                                width: '120px',
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
        <div id="mdlDatosGrupo" class="modal">
            <!-- Modal content -->
            <div class="modal-content w-40 mdlDatosGrupo">
                <div class="content" style="display: block">
                    <div class="card">
                        <headerCloseModal
                            :titulo_modal="titulo_modal"
                            :nombre_modal="'mdlDatosGrupo'"
                        ></headerCloseModal>

                        <div class="card-body card-block">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label
                                                class="input-group-text prepend-title"
                                                >NOMBRE</label
                                            >
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control mayus"
                                            :class="[
                                                submited
                                                    ? $v.frmDatosGrupo.nombre
                                                          .$invalid
                                                        ? 'is-invalid'
                                                        : 'is-valid'
                                                    : '',
                                            ]"
                                            v-model="frmDatosGrupo.nombre"
                                            placeholder="Ingrese 4 caracteres como mínimo..."
                                            :disabled="modo == 'VER'"
                                        />
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label
                                                class="input-group-text prepend-title"
                                                >AGENCIA</label
                                            >
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control center"
                                            :value="nombre_agencia"
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label
                                                class="input-group-text prepend-title"
                                                for="chbPorAsesor"
                                            >
                                                ASESOR
                                            </label>
                                        </div>

                                        <select
                                            class="form-control center"
                                            :class="[
                                                submited
                                                    ? $v.frmDatosGrupo.asesor_id
                                                          .$invalid
                                                        ? 'is-invalid'
                                                        : 'is-valid'
                                                    : '',
                                            ]"
                                            v-model="frmDatosGrupo.asesor_id"
                                            :disabled="modo == 'VER'"
                                        >
                                            <option
                                                :value="0"
                                                disabled
                                                selected
                                            >
                                                Seleccione...
                                            </option>
                                            <option
                                                v-for="(
                                                    item, index
                                                ) in lista_asesores"
                                                :key="index"
                                                :value="item.dni"
                                            >
                                                {{ item.usuario }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <div class="input-group mb-1">
                                        <div class="input-group-prepend">
                                            <label
                                                class="input-group-text prepend-title"
                                            >
                                                BUSCAR
                                            </label>
                                        </div>
                                        <input
                                            class="form-control mayus"
                                            type="text"
                                            placeholder="Nombre del cliente"
                                            autocomplete="off"
                                            @keyup="BuscarClientes"
                                            v-model="texto_busqueda"
                                            spellcheck="false"
                                            :disabled="this.modo == 'VER'"
                                        />
                                    </div>
                                    <DataTable
                                        :value="lista_clientes"
                                        :scrollable="true"
                                        scrollDirection="both"
                                        :scrollHeight="'150px'"
                                        showGridlines
                                        :rows="100"
                                        :selectionMode="
                                            modo === 'VER' ? null : 'single'
                                        "
                                    >
                                        <Column
                                            header="AGREGAR"
                                            :styles="{
                                                width: '40px',
                                                justifyContent: 'center',
                                            }"
                                        >
                                            <template #body="{ data }">
                                                <button
                                                    class="btn btn-action btn-icon-split"
                                                    @click="Agregar(data)"
                                                >
                                                    <span
                                                        class="icon text-white"
                                                    >
                                                        <i
                                                            class="fas fa-arrow-down"
                                                        ></i>
                                                    </span>
                                                </button>
                                            </template>
                                        </Column>
                                        <Column
                                            field="dni"
                                            header="DNI"
                                            :styles="{
                                                width: '60px',
                                                justifyContent: 'center',
                                            }"
                                        />
                                        <Column
                                            field="cliente"
                                            header="CLIENTE"
                                            :styles="{
                                                width: '250px',
                                                justifyContent: 'left',
                                            }"
                                        />

                                        <template #empty
                                            >No se encontraron
                                            CLIENTES.</template
                                        >
                                    </DataTable>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="card-title mt-2 mb-2">
                                        LISTA CLIENTES
                                    </div>
                                    <DataTable
                                        :value="
                                            frmDatosGrupo.lista_grupo_clientes
                                        "
                                        :scrollable="true"
                                        scrollDirection="both"
                                        :scrollHeight="'230px'"
                                        showGridlines
                                        :rows="100"
                                    >
                                        <Column
                                            field="numero"
                                            header="N°"
                                            :styles="{
                                                width: '2rem',
                                                justifyContent: 'center',
                                            }"
                                        >
                                            <template #body="{ index }">
                                                {{ index + 1 }}
                                            </template>
                                        </Column>
                                        <Column
                                            header="QUITAR"
                                            :styles="{
                                                width: '3rem',
                                                justifyContent: 'center',
                                            }"
                                        >
                                            <template #body="{ data }">
                                                <button
                                                    class="btn btn-danger btn-icon-split"
                                                    @click="Quitar(data)"
                                                    :disabled="modo == 'VER'"
                                                >
                                                    <span
                                                        class="icon text-white"
                                                    >
                                                        <i
                                                            class="fas fa-times"
                                                        ></i>
                                                    </span>
                                                </button>
                                            </template>
                                        </Column>

                                        <Column
                                            field="cliente"
                                            header="CLIENTE"
                                            :styles="{
                                                width: '18rem',
                                                justifyContent: 'left',
                                            }"
                                        />

                                        <template #empty
                                            >No se encontraron
                                            resultados.</template
                                        >
                                    </DataTable>
                                </div>
                            </div>
                            <hr />
                            <div class="text-right">
                                <button
                                    class="btn btn-action btn-icon-split"
                                    title="Guardar GRUPO"
                                    @click="Guardar()"
                                    v-show="
                                        (this.modo == 'NUEVO' ||
                                            this.modo == 'EDITAR') &&
                                        this.frmDatosGrupo.habilitado == 1
                                    "
                                >
                                    <span class="icon text-white">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    <span class="text">GUARDAR</span>
                                </button>
                                <button
                                    class="btn btn-cancel btn-icon-split"
                                    title="Editar GRUPO"
                                    @click="Editar()"
                                    v-show="
                                        this.modo == 'VER' &&
                                        this.frmDatosGrupo.habilitado == 1
                                    "
                                >
                                    <span class="icon text-white">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                    <span class="text">EDITAR</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { required, minLength } from "vuelidate/lib/validators";
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
const noZero = (value) => value != 0;

export default {
    components: {
        layout,
        headerClose,
        headerCloseModal,

        DataTable,
        Column,
    },
    data() {
        return {
            submited: false,
            agencias_permitidas: [],
            agencia_seleccionada: 0,

            lista_grupos: [],
            lista_clientes: [],

            frmDatosGrupo: {
                grupo_id: 0,
                nombre: null,
                asesor_id: 0,
                lista_grupo_clientes: [],
                habilitado: 0,
            },

            texto_busqueda: null,

            lista_asesores: [],
            titulo_modal: "",

            filtros_tabla: {
                nombre: { value: null },
            },

            lista_eliminados: [],

            modo: null,
        };
    },
    validations: {
        frmDatosGrupo: {
            asesor_id: { noZero },
            nombre: { required, minLength: minLength(4) },
        },
    },
    watch: {
        agencias_permitidas(value) {
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
        },
        agencia_seleccionada() {
            this.ListarGrupos();
            this.ListarDatos();
        },
    },
    computed: {
        lista_grupos_filtrados() {
            const filtro_nombre = this.filtros_tabla["nombre"].value;

            return this.lista_grupos.filter((item) => {
                if (filtro_nombre && filtro_nombre.length >= 3) {
                    if (
                        !item.nombre
                            .toLowerCase()
                            .includes(filtro_nombre.toLowerCase())
                    ) {
                        return false;
                    }
                }

                return true;
            });
        },

        nombre_agencia() {
            const agencia = this.agencias_permitidas.find(
                (item) => item.id === this.agencia_seleccionada,
            );
            return agencia ? agencia.agencia : "";
        },
    },
    mounted() {
        this.ListarAgencias();
    },

    methods: {
        ListarAgencias() {
            this.agencias_permitidas =
                this.$parent.$parent.$parent.filtrar_agencias(
                    "CREDITOS_CLIENTES/GRUPOS",
                );
        },
        async ListarDatos() {
            const params = {
                agencia_id: this.agencia_seleccionada,
            };

            await axios
                .get(route("cli.gru.listar_datos"), { params })
                .then((response) => {
                    this.lista_asesores = response.data.lista_asesores;
                });
        },
        async ListarGrupos() {
            const params = {
                agencia_id: this.agencia_seleccionada,
            };

            // this.$inertia.get(route("cli.gru.listar_grupos"), params);
            // return false;

            const response = await axios.get(route("cli.gru.listar_grupos"), {
                params,
            });

            console.log(response.data.lista_grupos);

            this.lista_grupos = response.data.lista_grupos;
        },

        async BuscarClientes() {
            if (!this.agencia_seleccionada) return;

            if (this.texto_busqueda && this.texto_busqueda.length >= 3) {
                try {
                    const response = await axios.get(
                        route("cli.gru.buscar_clientes", {
                            texto_buscar: this.texto_busqueda,
                            agencia_id: this.agencia_seleccionada,
                        }),
                    );

                    this.lista_clientes = response.data.lista_clientes;
                } catch (error) {
                    console.error(error);
                }
            } else {
                this.lista_clientes = [];
            }
        },

        Nuevo() {
            this.submited = false;
            this.modo = "NUEVO";
            this.titulo_modal = "NUEVO GRUPO";
            this.texto_busqueda = "";
            this.lista_clientes = [];

            this.frmDatosGrupo.nombre = "";
            this.frmDatosGrupo.asesor_id = 0;
            this.frmDatosGrupo.habilitado = 1;
            this.frmDatosGrupo.lista_grupo_clientes = [];
            $("#mdlDatosGrupo").css("display", "block");
        },
        async Ver(item) {
            this.submited = false;
            this.titulo_modal = "VER GRUPO";
            this.modo = "VER";
            this.texto_busqueda = "";
            this.lista_clientes = [];

            this.frmDatosGrupo.grupo_id = item.id;
            this.frmDatosGrupo.nombre = item.nombre;
            this.frmDatosGrupo.asesor_id = item.asesor_id;
            this.frmDatosGrupo.habilitado = item.habilitado;

            const params = {
                agencia_id: this.agencia_seleccionada,
                grupo_id: item.id,
            };

            // this.$inertia.get(route("cli.gru.listar_grupo_clientes"), params);
            // return false;

            const response = await axios.get(
                route("cli.gru.listar_grupo_clientes"),
                {
                    params,
                },
            );

            this.frmDatosGrupo.lista_grupo_clientes =
                response.data.lista_grupo_clientes;

            $("#mdlDatosGrupo").css("display", "block");
        },

        async Agregar(item) {
            const agregado = this.frmDatosGrupo.lista_grupo_clientes.find(
                (cliente) => cliente.dni === item.dni,
            );

            if (!agregado) {
                this.frmDatosGrupo.lista_grupo_clientes.push(item);
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "El cliente ya está en el grupo.",
                    allowOutsideClick: true,
                });
                return false;
            }

            if (this.frmDatosGrupo.lista_grupo_clientes.length == 10) {
                Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "No puede ingresar más de 10 integrantes al grupo",
                });
                return false;
            }
        },

        Quitar(data) {
            const index = this.frmDatosGrupo.lista_grupo_clientes.findIndex(
                (item) => item.dni === data.dni,
            );
            this.frmDatosGrupo.lista_grupo_clientes.splice(index, 1);
        },

        Editar() {
            this.modo = "EDITAR";
        },
        async Guardar() {
            this.submited = true;
            if (
                this.$v.frmDatosGrupo.$invalid &&
                this.frmDatosGrupo.nombre.trim().length == 0
            ) {
                Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Hay uno o más campos vacíos, verifique.",
                });
                return false;
            }
            if (this.frmDatosGrupo.nombre.trim().length < 5) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Tiene que ingresar al menos 5 caracteres para el nombre del grupo",
                });
                return false;
            }

            if (this.frmDatosGrupo.lista_grupo_clientes.length < 4) {
                Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Tiene que ingresar al menos 4 integrantes al grupo",
                });
                return false;
            }

            const response = await axios.get(
                route("cli.gru.verificar", {
                    grupo_id: this.frmDatosGrupo.grupo_id,
                    nombre: this.frmDatosGrupo.nombre,
                    agencia_id: this.agencia_seleccionada,
                    modo: this.modo,
                }),
            );

            if (response.data.existe) {
                Swal.fire({
                    icon: "error",
                    title: "¡Error!",
                    text: "Ya hay un grupo registrado con el mismo nombre",
                });
                return false;
            }

            let text_guardar = "";
            if (this.modo == "NUEVO") {
                text_guardar = "¿DESEA REGISTRAR ESTE GRUPO?";
            } else {
                text_guardar = "¿DESEA EDITAR ESTE GRUPO?";
            }

            Swal.fire({
                icon: "question",
                text: text_guardar,
                confirmButtonText: "Si",
                showCancelButton: true,
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then((result) => {
                if (!result.isConfirmed) {
                    return false;
                }

                let data = new FormData();
                data.append("modo", this.modo);
                data.append("agencia_id", this.agencia_seleccionada);
                data.append(
                    "frmDatosGrupo",
                    JSON.stringify(this.frmDatosGrupo),
                );

                //  this.$inertia.post(route('cli.gru.guardar'), data);
                //  return false;
                Swal.fire({
                    title: "REGISTRANDO",
                    text: "Espere porfavor...",
                    allowOutsideClick: false,
                    didOpen: async () => {
                        Swal.showLoading();

                        try {
                            const response = await axios.post(
                                route("cli.gru.guardar"),
                                data,
                            );

                            await Swal.fire({
                                icon: "success",
                                title: response.data.message,
                                timer: 1200,
                                showConfirmButton: false,
                            });

                            $("#mdlDatosGrupo").css("display", "none");
                            await this.ListarGrupos();
                        } catch (error) {
                            if (error.response) {
                                Swal.fire({
                                    icon: "error",
                                    title: error.response.data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        }
                    },
                });
            });
        },
    },
};
</script>

<style lang="scss">
//   COMPONENT CLASS --------------

//   RESPONSIVE DESIGN --------------
// MOBILE SCREEN
.slot-grupo {
    width: 99%;
    margin: 0 auto;
}

@media (min-width: 900px) {
    // SMALL SCREEN
    .slot-grupo {
        width: 68%;
        margin: 0 auto;
    }
}

@media (min-width: 1400px) {
    // MEDIUM SCREEN
    .slot-grupo {
        width: 60%;
        margin: 0 auto;
    }
}

@media (min-width: 1900px) {
    // BIG SCREEN
    .slot-grupo {
        width: 48%;
        margin: 0 auto;
    }
}
</style>
