<template>
  <layout ref="layout">
    <div class="slot_body slot-equipos" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose :title="'LISTA DE EQUIPOS'"></headerClose>
          <div class="card-title">PANEL DE BÚSQUEDA</div>
          <div class="card-body card-block">
            <div class="form-row">
              <div class="input-group col-md-6 col-7">
                <div class="input-group-prepend">
                  <span class="input-group-text"
                    ><i class="fas fa-search"></i
                  ></span>
                </div>
                <input
                  class="form-control mayus"
                  type="text"
                  v-model="filtros_tabla['equipo'].value"
                  autocomplete="off"
                  spellcheck="false"
                  @focus="hidenav()"
                  @blur="shownav()"
                />
              </div>

              <div class="col-md-1 col-1 ml-1">
                <button
                  class="btn btn-action btn-icon-split"
                  @click="Nuevo"
                  title="NUEVO EQUIPO"
                >
                  <span class="icon text-white">
                    <i class="fas fa-plus"></i>
                  </span>
                  <span class="text">NUEVO</span>
                </button>
              </div>
            </div>
          </div>

          <div class="card-title">LISTA DE RESULTADOS</div>

          <div class="card-body card-block">
            <DataTable
              :value="equipo_formateado"
              :scrollable="true"
              scrollDirection="both"
              scrollHeight="400px"
              :filters="filtros_tabla"
              :globalFilterFields="['equipo']"
              showGridlines
            >
              <Column
                field="editar"
                header="EDITAR"
                :styles="{ width: '80px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  <button
                    class="btn btn-cancel btn-icon-split"
                    title="EDITAR PREGUNTA"
                    @click="Editar(data)"
                  >
                    <span class="icon text-white">
                      <i class="pi pi-pencil"></i>
                    </span>
                  </button>
                </template>
              </Column>
              <Column
                field="equipo"
                header="NOMBRE"
                :styles="{
                  minWidth: '250px',
                  justifyContent: 'center',
                }"
              >
              </Column>
              <Column
                field="usuario"
                header="RESPONSABLE"
                :styles="{
                  width: '150px',
                  justifyContent: 'center',
                }"
              >
                <template #body="{ data }">
                  <tbody>
                    <tr v-for="(item, index) in data.responsable" :key="index">
                      <td>{{ item.usuario }}</td>
                    </tr>
                  </tbody>
                </template>
              </Column>
              <Column
                field="cargo"
                header="CARGO"
                :styles="{
                  width: '200px',
                  justifyContent: 'center',
                }"
              >
                <template #body="{ data }">
                  <tbody>
                    <tr v-for="(item, index) in data.responsable" :key="index">
                      <td>{{ item.cargo }}</td>
                    </tr>
                  </tbody>
                </template>
              </Column>
              <Column
                field="agencia_id"
                header="AGENCIA"
                :styles="{
                  width: '150px',
                  justifyContent: 'center',
                }"
              >
                <template #body="{ data }">
                  <tbody>
                    <tr v-for="(item, index) in data.responsable" :key="index">
                      <td>{{ item.nombre_agencia }}</td>
                    </tr>
                  </tbody>
                </template>
              </Column>
              <Column
                field="habilitado"
                header="HABILITADO"
                :styles="{
                  width: '80px',
                  justifyContent: 'center',
                }"
              >
                <template #body="{ data }">
                  {{ data.habilitado == 1 ? "SI" : "NO" }}
                </template>
              </Column>
            </DataTable>
          </div>
        </div>
      </div>

      <!-- The Modal -->
      <div id="mdlDatosEquipo" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-75 mdlDatosEquipo">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="title_modal"
                :nombre_modal="'mdlDatosEquipo'"
              >
              </headerCloseModal>

              <div class="card-title">DATOS DEL EQUIPO Y RESPONSABLE(S)</div>
              <div class="card-body card-block">
                <div class="form-row">
                  <div class="form-row col-md-6">
                    <div class="col-md-12">
                      <label class="label-title">NOMBRE DEL EQUIPO</label>
                      <input
                        type="text"
                        class="form-control mayus"
                        v-model="frmDatosEquipo.nombre"
                        :class="[
                          submited
                            ? $v.frmDatosEquipo.nombre.$invalid
                              ? 'is-invalid'
                              : 'is-valid'
                            : '',
                        ]"
                      />
                      <div
                        class="form-row ml-1"
                        v-if="frmDatosEquipo.modo == 'EDITAR'"
                      >
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            id="chbHabilitado"
                            v-model="frmDatosEquipo.habilitado"
                          />
                          <label class="label-title" for="chbHabilitado">
                            HABILITADO
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label class="label-title">AGENCIAS</label>
                      <select
                        class="form-control center"
                        v-model="frmDatosEquipo.id_agencia"
                        @change="FiltrarUsuarioResponsable"
                      >
                        <option :value="0">TODAS</option>
                        <option
                          v-for="item in agencias"
                          :key="item.id"
                          :value="item.id"
                        >
                          {{ item.agencia }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-5">
                      <label class="label-title">RESPONSABLE</label>
                      <select
                        class="form-control"
                        v-model="responsable_seleccionado"
                      >
                        <option :value="0">Seleccione...</option>
                        <option
                          v-for="item in usuarios_filtrados_responsable"
                          :key="item.dni"
                          :value="item.dni"
                        >
                          {{ item.usuario }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-3 mt-4">
                      <button
                        class="btn btn-cancel btn-icon-split"
                        @click="AgregarResponsable()"
                      >
                        <span class="icon text-white">
                          <i class="fa fa-arrow-right"></i>
                        </span>
                        <span class="text">AGREGAR</span>
                      </button>
                    </div>
                  </div>
                  <div class="col-md-6" style="height: 170px">
                    <label class="label-title">LISTA DE RESPONSABLES</label>
                    <DataTable
                      :value="frmDatosEquipo.responsable"
                      :scrollable="true"
                      scrollHeight="140px"
                      scrollDirection="both"
                      showGridlines
                    >
                      <Column
                        field="quitar"
                        header="QUITAR"
                        :styles="{ maxWidth: '80px', justifyContent: 'center' }"
                      >
                        <template #body="{ data }">
                          <button
                            class="btn btn-danger"
                            title="QUITAR"
                            @click="QuitarResponsable(data)"
                          >
                            <span class="icon text-white">
                              <i class="pi pi-trash"></i>
                            </span>
                          </button>
                        </template>
                      </Column>
                      <Column
                        field="usuario"
                        header="USUARIO"
                        :styles="{
                          width: '100px',
                          justifyContent: 'center',
                        }"
                      >
                      </Column>
                      <Column
                        field="cargo"
                        header="CARGO"
                        :styles="{
                          width: '100px',
                        }"
                      >
                      </Column>
                      <Column
                        field="nombre_agencia"
                        header="AGENCIA"
                        :styles="{
                          width: '100px',
                          justifyContent: 'center',
                        }"
                      >
                      </Column>
                    </DataTable>
                  </div>
                </div>

                <div class="card-title">LISTA DE INTEGRANTES</div>

                <div class="form-row">
                  <div class="col-md-6 mt-2" style="height: 330px">
                    <div class="input-group col-md-8 mb-2 offset-md-2">
                      <div class="input-group-prepend">
                        <span class="input-group-text prepend-title"
                          >AGENCIA</span
                        >
                      </div>
                      <select
                        class="form-control mayus"
                        v-model="agencia_filtrada"
                        @change="FiltrarUsuarioEquipo"
                      >
                        <option :value="0" align="center">TODAS</option>
                        <option
                          v-for="item in agencias"
                          :key="item.id"
                          :value="item.id"
                          align="center"
                        >
                          {{ item.agencia }}
                        </option>
                      </select>
                    </div>

                    <DataTable
                      :value="lista_integrantes"
                      :scrollable="true"
                      scrollHeight="280px"
                      scrollDirection="both"
                      showGridlines
                    >
                      <Column
                        field="nombre_agencia"
                        header="AGENCIA"
                        :styles="{
                          width: '100px',
                          justifyContent: 'center',
                        }"
                      >
                      </Column>
                      <Column
                        field="cargo"
                        header="CARGO"
                        :styles="{
                          width: '100px',
                        }"
                      >
                      </Column>
                      <Column
                        field="usuario"
                        header="USUARIO"
                        :styles="{
                          width: '100px',
                          justifyContent: 'center',
                        }"
                      >
                      </Column>

                      <Column
                        field="quitar"
                        header="QUITAR"
                        :styles="{ maxWidth: '80px', justifyContent: 'center' }"
                      >
                        <template #body="{ data }">
                          <button
                            class="btn btn-action"
                            title="AGREGAR A LISTA"
                            @click="AgregarIntegrante(data)"
                          >
                            <span class="icon text-white">
                              <i class="pi pi-arrow-right"></i>
                            </span>
                          </button>
                        </template>
                      </Column>
                    </DataTable>
                  </div>
                  <div class="col-md-6" style="height: 330px">
                    <label class="label-title mt-3 mb-3"
                      >LISTA DE INTEGRANTES</label
                    >

                    <DataTable
                      :value="integrantes"
                      :scrollable="true"
                      scrollHeight="280px"
                      scrollDirection="both"
                      showGridlines
                    >
                      <Column
                        field="quitar"
                        header="QUITAR"
                        :styles="{ maxWidth: '80px', justifyContent: 'center' }"
                      >
                        <template #body="{ data }">
                          <button
                            class="btn btn-action"
                            title="RETIRAR DE LISTA"
                            @click="RetirarIntegrante(data)"
                          >
                            <span class="icon text-white">
                              <i class="pi pi-arrow-left"></i>
                            </span>
                          </button>
                        </template>
                      </Column>

                      <Column
                        field="usuario"
                        header="USUARIO"
                        :styles="{
                          width: '100px',
                          justifyContent: 'center',
                        }"
                      >
                      </Column>
                      <Column
                        field="cargo"
                        header="CARGO"
                        :styles="{
                          width: '100px',
                        }"
                      >
                      </Column>
                      <Column
                        field="nombre_agencia"
                        header="AGENCIA"
                        :styles="{
                          width: '100px',
                          justifyContent: 'center',
                        }"
                      >
                      </Column>
                    </DataTable>
                  </div>
                </div>
                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    title="GUARDAR EQUIPO"
                    @click="Guardar"
                  >
                    <span class="icon text-white">
                      <i class="pi pi-save"></i>
                    </span>
                    <span class="text">GUARDAR</span>
                  </button>
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
import headerCloseModal from "@/Pages/Gth/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import { FilterMatchMode } from "primevue/api";

import { required } from "vuelidate/lib/validators";
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
      equipos: [],
      usuarios: [],
      filtros_tabla: {},
      submited: false,
      title_modal: null,
      usuarios_filtrados_responsable: [],
      lista_integrantes: [],
      agencia_filtrada: 0,
      cargo_responsable: null,
      responsable_seleccionado: 0,
      equipos_filtrados: [],
      frmDatosEquipo: {
        modo: null,
        id: null,
        nombre: null,
        id_agencia: null,
        responsable: [],
        habilitado: null,
      },
      integrantes: [],
      equipo_formateado: [],
    };
  },
  validations: {
    frmDatosEquipo: {
      nombre: { required },
      responsable: { noZero },
    },
  },
  created() {
    this.filtros_tabla = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      equipo: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
  },
  mounted() {
    this.ListarRecursos();
  },
  computed: {
    agencias() {
      return this.$page.props.application.agencias;
    },
  },
  watch: {
    equipos() {
      this.FormateandoEquipo();
    },
  },
  methods: {
    hidenav() {
      return this.$refs.layout.hide_nav();
    },
    shownav() {
      return this.$refs.layout.show_nav();
    },
    async ListarRecursos() {
      let self = this;
      await axios
        .get(route("col.equipos.listar_recursos"))
        .then(function (response) {
          self.equipos = response.data.equipos;
          self.equipos_filtrados = response.data.equipos;
          self.usuarios = response.data.usuarios;
          self.usuarios_filtrados_responsable = response.data.usuarios;
          self.lista_integrantes = response.data.usuarios;
        });
    },

    FormateandoEquipo() {
      let self = this;
      this.equipo_formateado = [];
      this.equipos.map((item) => {
        let equipo = {
          id: item.id,
          equipo: item.equipo,
          responsable: JSON.parse(item.responsable_id),
          habilitado: item.habilitado,
        };
        self.equipo_formateado.push(equipo);
      });
    },

    FiltrarUsuarioResponsable(e) {
      let id_agencia = e.target.value;
      let modo = this.frmDatosEquipo.modo;

      if (id_agencia == 0) {
        this.usuarios_filtrados_responsable = this.usuarios;
        if (this.frmDatosEquipo.responsable.length != 0) {
          this.frmDatosEquipo.responsable.forEach((element) => {
            this.usuarios_filtrados_responsable =
              this.usuarios_filtrados_responsable.filter(
                (item) => item.dni != element.dni
              );
          });
          if (this.integrantes.length != 0) {
            this.integrantes.forEach((element) => {
              this.usuarios_filtrados_responsable =
                this.usuarios_filtrados_responsable.filter(
                  (item) => item.dni != element.dni
                );
            });
          }
        }
      } else {
        this.usuarios_filtrados_responsable = this.usuarios;
        if (this.frmDatosEquipo.responsable.length != 0) {
          this.frmDatosEquipo.responsable.forEach((element) => {
            this.usuarios_filtrados_responsable =
              this.usuarios_filtrados_responsable.filter(
                (item) =>
                  item.dni != element.dni && item.agencia_id == id_agencia
              );
          });
          if (this.integrantes.length != 0) {
            this.integrantes.forEach((element) => {
              this.usuarios_filtrados_responsable =
                this.usuarios_filtrados_responsable.filter(
                  (item) =>
                    item.dni != element.dni && item.agencia_id == id_agencia
                );
            });
          }
        } else {
          this.usuarios_filtrados_responsable = this.usuarios.filter(
            (item) => item.agencia_id == id_agencia
          );
        }
      }
      this.responsable_seleccionado = 0;
    },
    FiltrarUsuarioEquipo(e) {
      let self = this;
      let agencias_select = this.agencia_filtrada;
      let id_agencia = e.target.value;
      if (agencias_select != 0) {
        this.lista_integrantes = this.usuarios;
        if (this.frmDatosEquipo.responsable.length != 0) {
          this.frmDatosEquipo.responsable.forEach((element) => {
            self.lista_integrantes = self.lista_integrantes.filter(
              (item) => item.dni != element.dni && item.agencia_id == id_agencia
            );
          });
          if (this.integrantes.length != 0) {
            this.integrantes.forEach((element) => {
              self.lista_integrantes = self.lista_integrantes.filter(
                (item) => item.dni != element.dni
              );
            });
          }
        } else {
          this.lista_integrantes = this.usuarios.filter(
            (item) => item.agencia_id == id_agencia
          );
        }
      } else {
        this.lista_integrantes = this.usuarios;
        if (this.frmDatosEquipo.responsable.length != 0) {
          this.frmDatosEquipo.responsable.forEach((element) => {
            this.lista_integrantes = this.lista_integrantes.filter(
              (item) => item.dni != element.dni
            );
          });
          if (this.integrantes.length != 0) {
            this.integrantes.forEach((element) => {
              self.lista_integrantes = self.lista_integrantes.filter(
                (item) => item.dni != element.dni
              );
            });
          }
        }
      }
    },
    Nuevo() {
      this.submited = false;
      this.title_modal = "NUEVO EQUIPO";
      this.frmDatosEquipo.id = null;
      this.frmDatosEquipo.modo = "NUEVO";
      this.frmDatosEquipo.nombre = null;
      this.frmDatosEquipo.id_agencia = 0;
      this.frmDatosEquipo.responsable = [];
      this.frmDatosEquipo.habilitado = false;

      $("#mdlDatosEquipo").css("display", "block");
    },
    Editar(equipo) {
      let self = this;

      axios
        .get(route("col.equipos.integrantes", equipo.id))
        .then(function (response) {
          self.integrantes = response.data;
        })
        .then(function () {
          self.submited = false;
          self.title_modal = "EDITAR EQUIPO";
          self.frmDatosEquipo.id = equipo.id;
          self.frmDatosEquipo.modo = "EDITAR";
          self.frmDatosEquipo.nombre = equipo.equipo;
          self.frmDatosEquipo.id_agencia = equipo.responsable[0].agencia_id;
          self.frmDatosEquipo.responsable = equipo.responsable;
          self.frmDatosEquipo.habilitado = equipo.habilitado;

          let usuarios_filtrados_editar = self.usuarios.filter(
            (usuario) =>
              !self.frmDatosEquipo.responsable.some(
                (responsable) => responsable.dni == usuario.dni
              ) &&
              !self.integrantes.some(
                (integrante) => integrante.dni == usuario.dni
              )
          );

          self.usuarios_filtrados_responsable =
            usuarios_filtrados_editar.filter(
              (usuario) => usuario.agencia_id == self.frmDatosEquipo.id_agencia
            );
          self.lista_integrantes = usuarios_filtrados_editar;
        });

      $("#mdlDatosEquipo").css("display", "block");
    },

    AgregarIntegrante(usuario) {
      let integrantes = this.integrantes;
      let lista_integrantes = this.lista_integrantes;
      integrantes.push(usuario);

      let index = lista_integrantes.findIndex((x) => x.dni == usuario.dni);

      lista_integrantes.splice(index, 1);
    },
    RetirarIntegrante(usuario) {
      let integrantes = this.integrantes;
      let lista_integrantes = this.lista_integrantes;
      lista_integrantes.push(usuario);

      let index = integrantes.findIndex((x) => x.dni == usuario.dni);

      integrantes.splice(index, 1);
    },

    RellenarCargo(dni_responsable) {
      if (dni_responsable != 0) {
        let cargo = this.usuarios.filter((item) => item.dni == dni_responsable);
        if (this.lista_integrantes.length === this.usuarios.length) {
          this.lista_integrantes = this.usuarios.filter(
            (item) => item.dni != dni_responsable
          );
        } else {
          this.lista_integrantes = this.lista_integrantes.filter(
            (item) => item.dni != dni_responsable
          );
        }

        return cargo[0];
      } else {
        this.cargo_responsable = null;
      }
    },
    AgregarResponsable() {
      if (this.responsable_seleccionado == 0) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe seleccionar un responsable",
        });
        return false;
      }
      let dni_responsable = this.responsable_seleccionado;
      let responsable_datos = this.RellenarCargo(dni_responsable);

      this.frmDatosEquipo.responsable.push(responsable_datos);

      this.responsable_seleccionado = 0;

      this.usuarios_filtrados_responsable =
        this.usuarios_filtrados_responsable.filter(
          (x) => x.dni != dni_responsable
        );
    },
    QuitarResponsable(usuario) {
      let responsable = this.frmDatosEquipo.responsable;
      let index = responsable.findIndex((x) => x.dni == usuario.dni);
      responsable.splice(index, 1);
      this.lista_integrantes.push(usuario);
      this.usuarios_filtrados_responsable.push(usuario);
    },
    ObtenerAgencia(agencia_id) {
      let agencia = this.agencias.find((item) => item.id == agencia_id);
      return agencia.agencia;
    },
    Guardar() {
      let self = this;
      this.submited = true;
      if (this.$v.frmDatosEquipo.$invalid == true) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Hay uno o más campos vacíos, verifique.",
        });
        return false;
      } else {
        let data = new FormData();
        data.append("modo", this.frmDatosEquipo.modo);
        data.append("id_equipo", this.frmDatosEquipo.id);
        data.append("nombre_equipo", this.frmDatosEquipo.nombre.toUpperCase());

        axios
          .post(route("col.equipos.verificar"), data)
          .then(function (response) {
            let resultado = response.data;
            if (resultado == "EXISTE") {
              Swal.fire({
                icon: "error",
                title: "¡Ups!",
                text: "El NOMBRE DEL EQUIPO ingresado ya existe, intente con otro.",
              });
              return false;
            } else {
              Swal.fire({
                title: "GUARDAR CAMBIOS",
                text: "¿Desea continuar?",
                confirmButtonText:
                  '<i class="fas fa-check" style="color:white;"></i>   Si',
                confirmButtonColor: "var(--colorAlto)",
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times"></i>   No',
                cancelButtonColor: "var(--plomoOscuroEmpresarial)",
                allowOutsideClick: false,
              }).then((result) => {
                if (result.isConfirmed) {
                  Swal.fire({
                    title: "REGISTRANDO",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willOpen: async () => {
                      Swal.showLoading();

                      self.frmDatosEquipo.integrantes = self.integrantes;

                      // self.$inertia.post(
                      // 	route("col.equipos.guardar"),
                      // 	self.frmDatosEquipo
                      // );
                      // return false;
                      return await axios
                        .post(route("col.equipos.guardar"), self.frmDatosEquipo)
                        .then((response) => {
                          $("#mdlDatosEquipo").css("display", "none");
                          self.ListarRecursos();
                          return Swal.fire({
                            icon: "success",
                            title: "¡ÉXITO!",
                            timer: 1200,
                            showConfirmButton: false,
                          });
                        })
                        .catch((error) => {
                          Swal.showValidationMessage(
                            `Ha ocurrido un error, comunicar a TI: ${error}`
                          );
                        });
                    },
                  });
                }
              });
            }
          });
      }
    },
  },
};
</script>

<style >
.slot-equipos {
  width: 70% !important;
  margin-left: 15% !important;
}

.mdlDatosEquipo {
  margin-top: 1%;
}

@media (max-width: 900px) {
  .slot-equipos {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .mdlDatosEquipo {
    margin-top: 20%;
  }
}
</style>
