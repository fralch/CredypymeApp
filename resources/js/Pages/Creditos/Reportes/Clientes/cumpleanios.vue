<template>
  <layout ref="layout">
    <div class="slot_body slot-reporte-cumpleanios" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose
            :title="
              (modo == 'personal' ? 'MIS ' : '') +
              'CLIENTES' +
              (modo == 'personal' ? ' - ' : ' CON ') +
              'CUMPLEAÑOS'
            "
          ></headerClose>

          <div class="card-body card-block">
            <div class="form-row">
              <fieldset class="form-group col-md-10">
                <legend>
                  <label class="label-title">FILTROS DE BÚSQUEDA</label>
                </legend>
                <div class="row">
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title"
                        >AGENCIA</span
                      >
                    </div>
                    <select
                      class="form-control center bolder"
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
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">DESDE</span>
                    </div>
                    <input
                      type="date"
                      class="form-control center bolder"
                      v-model="fecha_desde"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                    />
                  </div>
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">HASTA</span>
                    </div>
                    <input
                      type="date"
                      class="form-control center bolder"
                      v-model="fecha_hasta"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                    />
                  </div>

                  <div class="col-md-1 text-right" v-if="windowWidth < 900">
                    <button
                      class="btn btn-action btn-icon-split mt-3"
                      title="Buscar"
                      @click="Buscar"
                    >
                      <span class="icon text-white" style="font-size: 15px">
                        <i class="fas fa-search"></i>
                      </span>
                    </button>
                  </div>
                </div>
              </fieldset>

              <div class="col-md-1 ml-3" v-if="windowWidth >= 900">
                <button
                  class="btn btn-action btn-icon-split mt-3"
                  title="Buscar"
                  @click="Buscar"
                >
                  <span class="icon text-white" style="font-size: 25px">
                    <i class="fas fa-search"></i>
                  </span>
                </button>
              </div>

              <div class="form-row col-md-12">
                <div class="input-group col-md-4">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input
                        type="checkbox"
                        id="chbPorAsesor"
                        v-model="por_asesor"
                        :disabled="modo == 'personal'"
                      />
                    </div>
                    <label
                      class="input-group-text prepend-title"
                      for="chbPorAsesor"
                      style="font-size: 13px"
                    >
                      ASESOR
                    </label>
                  </div>
                  <div class="input-group-prepend"></div>
                  <select
                    class="form-control center"
                    v-model="usuario_seleccionado"
                    :disabled="!por_asesor"
                  >
                    <option :value="0" disabled selected>Seleccione...</option>
                    <option
                      v-for="(item, index) in usuarios_filtrados"
                      :key="index"
                      :value="item.dni"
                    >
                      {{ item.usuario }}
                    </option>
                  </select>
                </div>

                <div class="input-group col-md-4">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text prepend-title"
                      for="chbPorEstado"
                      style="font-size: 13px"
                    >
                      ESTADO
                    </label>
                  </div>

                  <select
                    class="form-control center"
                    v-model="filtros_tabla['estado'].value"
                    :disabled="lista_clientes.length == 0"
                  >
                    <option :value="null" selected>TODAS</option>
                    <option :value="'ACTIVO'">ACTIVO</option>
                    <option :value="'INACTIVO'">INACTIVO</option>
                    <option :value="'OTROS'">OTROS</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="card-title mt-2 mb-2">LISTA DE RESULTADOS</div>

            <DataTable
              :value="lista_clientes"
              :row-class="rowClass"
              :filters="filtros_tabla"
              :scrollable="true"
              scrollDirection="both"
              scrollHeight="350px"
              :rows="100"
              selectionMode="single"
              showGridlines
              :paginator="true"
              paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
              currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registro(s)"
            >
              <Column
                field="numero"
                header="N°"
                :styles="{ width: '40px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  {{ data.index + 1 }}
                </template>
              </Column>
              <Column
                field="acciones"
                header="ACCIONES"
                :styles="{ width: '150px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="Comentar(data)"
                    v-if="data.visitado == 'NO'"
                  >
                    <span class="text">COMENTAR</span>
                  </button>
                  <button
                    class="btn btn-cancel btn-icon-split"
                    @click="VerComentarios(data)"
                    v-if="data.visitado == 'SI'"
                  >
                    <span class="text">VER COMENTARIOS</span>
                  </button>
                </template>
              </Column>

              <Column
                field="fecha_nacimiento"
                header="FECHA_NAC."
                :styles="{ width: '100px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  {{ formato_fecha(data.fecha_nacimiento) }}
                </template>
              </Column>
              <Column
                field="codigo_expediente"
                header="EXPED."
                :styles="{ width: '100px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  {{
                    data.codigo_expediente == null
                      ? "-"
                      : data.codigo_expediente
                  }}
                </template>
              </Column>
              <Column
                field="cliente"
                header="CLIENTE"
                :styles="{ width: '300px' }"
              >
              </Column>
              <Column
                field="usuario_asesor"
                header="ASESOR"
                :styles="{ width: '120px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="estado"
                header="ESTADO"
                :styles="{ width: '100px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="calificacion"
                header="CALIF."
                :styles="{ width: '80px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  {{ data.calificacion == null ? "-" : data.calificacion }}
                </template>
              </Column>

              <Column
                field="telefono_1"
                header="TELEF_1"
                :styles="{ width: '80px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="nota_1"
                header="NOTAS_1"
                :styles="{ width: '120px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="telefono_2"
                header="TELEF_2"
                :styles="{ width: '80px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="nota_2"
                header="NOTAS_2"
                :styles="{ width: '120px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="telefono_3"
                header="TELEF_3"
                :styles="{ width: '80px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="nota_3"
                header="NOTAS_3"
                :styles="{ width: '120px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="telefono_4"
                header="TELEF_4"
                :styles="{ width: '100px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                field="nota_4"
                header="NOTAS_4"
                :styles="{ width: '100px', justifyContent: 'center' }"
              >
              </Column>
            </DataTable>
            <hr />
            <div class="text-right">
              <button
                class="btn btn-cancel btn-icon-split"
                title="Exportar"
                @click="Exportar"
                :disabled="lista_clientes.length == 0"
              >
                <span class="icon text-white">
                  <i class="fas fa-file-excel"></i>
                </span>
                <span class="text">EXPORTAR</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div id="mdlVerComentarios" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-50 mdlVerComentarios">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="'COMENTARIOS'"
                :nombre_modal="'mdlVerComentarios'"
              >
              </headerCloseModal>

              <div class="card-body card-block">
                <div class="form-row">
                  <div class="input-group col-md-6">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title"
                        >CLIENTE</span
                      >
                    </div>
                    <input
                      type="text"
                      class="form-control input-information"
                      :value="frmDatosComentario.cliente"
                      readonly
                    />
                  </div>
                </div>

                <DataTable
                  :value="lista_comentarios"
                  :row-class="rowClass"
                  :scrollable="true"
                  scrollDirection="both"
                  scrollHeight="350px"
                  :rows="100"
                  selectionMode="single"
                  showGridlines
                  style="margin-top: 5px"
                >
                  <Column
                    field="numero"
                    header="N°"
                    :styles="{ width: '1px', justifyContent: 'center' }"
                  >
                    <template #body="{ data }">
                      {{ data.index + 1 }}
                    </template>
                  </Column>
                  <Column
                    field="fecha_hora_visita"
                    header="FECHA"
                    :styles="{ width: '120px', justifyContent: 'center' }"
                  >
                  </Column>
                  <Column
                    field="comentario"
                    header="COMENTARIO"
                    :styles="{ width: '300px' }"
                  >
                  </Column>
                  <Column
                    field="usuario_registro"
                    header="USUARIO_REGISTRO"
                    :styles="{ width: '120px', justifyContent: 'center' }"
                  >
                  </Column>
                </DataTable>
                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    title="Añadir COMENTARIO"
                    @click="Comentar(item_seleccionado)"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">COMENTAR</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="mdlRegistroComentario" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-35 mdlRegistroComentario">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="'INDICAR SALUDO POR CUMPLEAÑOS'"
                :nombre_modal="'mdlRegistroComentario'"
              >
              </headerCloseModal>

              <div class="card-body card-block">
                <div class="form-row">
                  <div class="input-group col-md-12">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">PARA</span>
                    </div>
                    <input
                      type="text"
                      class="form-control input-information"
                      :value="frmDatosComentario.cliente"
                      readonly
                    />
                  </div>

                  <div class="form-group col-md-12">
                    <label class="form-control-label label-title"
                      >¿Qué se hizo por el dia de su cumpleaños?</label
                    >
                    <span
                      v-if="
                        submited && !$v.frmDatosComentario.comentario.required
                      "
                      class="span-error-message"
                      >*</span
                    >
                    <textarea
                      type="text"
                      rows="3"
                      class="form-control mayus text-row"
                      @focus="hidenav()"
                      @blur="shownav()"
                      v-model="frmDatosComentario.comentario"
                    ></textarea>
                  </div>
                </div>

                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    title="Registrar COMENTARIO"
                    @click="Registrar()"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-save"></i>
                    </span>
                    <span class="text">REGISTRAR</span>
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
import { required } from "vuelidate/lib/validators";

import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import { FilterMatchMode } from "primevue/api";

export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,

    DataTable,
    Column,
  },
  props: {
    modo: String,
    usuarios: Array,
  },

  data() {
    return {
      submited: false,

      agencias_permitidas: [],
      agencia_seleccionada: null,

      windowWidth: window.innerWidth,

      fecha_desde: null,
      fecha_hasta: null,

      por_asesor: this.modo == "personal" ? true : false,

      usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],
      usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,

      lista_clientes: [],
      lista_comentarios: [],

      item_seleccionado: {},

      frmDatosComentario: {
        cliente_id: 0,
        cliente: null,
        comentario: null,
      },

      año_comentario: null,

      filtros_tabla: {},
    };
  },
  validations: {
    frmDatosComentario: {
      comentario: { required },
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
      this.FechaActual();
      this.FiltrarUsuarios();
    },
    por_asesor() {
      this.usuario_seleccionado = 0;
    },
  },
  mounted() {
    this.ListarAgenciasPermitidas();

    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  created() {
    this.filtros_tabla = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      estado: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
  },
  methods: {
    async FechaActual() {
      if (this.agencia_seleccionada == null) {
        return false;
      } else {
        let fecha_actual = await this.$refs.layout.fecha_hora_actual(
          this.agencia_seleccionada
        );

        fecha_actual = fecha_actual.substring(0, 10);

        this.fecha_desde = fecha_actual;
        this.fecha_hasta = fecha_actual;
      }
    },
    hidenav() {
      this.$refs.layout.hide_nav();
    },
    shownav() {
      this.$refs.layout.show_nav();
    },
    rowClass(data) {
      let index = data.index;
      return index % 2 == 0 ? "verde-claro" : "";
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
    ListarAgenciasPermitidas() {
      if (this.modo == "personal") {
        this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
          "CREDITOS_REPORTES/CLIENTES_MIS_CUMPLEAÑOS"
        );
        this.filtro_usuario = true;
      } else if (this.modo == "completo") {
        this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
          "CREDITOS_REPORTES/CLIENTES_CUMPLEAÑOS"
        );
      } else {
        this.agencias_permitidas = [];
      }
    },

    FiltrarUsuarios() {
      if (this.modo == "completo") {
        this.usuarios_filtrados = [];
        this.usuario_seleccionado = 0;

        this.usuarios_filtrados = this.usuarios.filter(
          (item) => item.agencia_id == this.agencia_seleccionada
        );
      } else if (this.modo == "personal") {
        return false;
      }
    },

    Buscar() {
      let self = this;

      let año1 = new Date(this.fecha_desde).getFullYear();
      let año2 = new Date(this.fecha_hasta).getFullYear();

      if (año1 != año2) {
        return Swal.fire({
          icon: "info",
          title: "¡Ups!",
          text: "No se puede seleccionar años diferentes",
          allowOutsideClick: true,
        });
      }

      let data = new FormData();
      data.append("agencia_id", this.agencia_seleccionada);
      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("por_asesor", this.por_asesor);
      data.append("modo", "con_cumpleanios");

      if (this.por_asesor) {
        data.append("asesor_id", this.usuario_seleccionado);
      }

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          // this.$inertia.post(route("rep.cli.cumpleaños.buscar"), data);
          // return false;
          Swal.showLoading();
          axios
            .post(route("rep.cli.cumpleanios.buscar"), data)
            .then(function (response) {
              if (response.data.lista_clientes.length == 0) {
                self.lista_clientes = [];

                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No se encontraron datos",
                  allowOutsideClick: true,
                });
              } else {
                self.lista_clientes = response.data.lista_clientes;
                return Swal.fire({
                  icon: "success",
                  title: "¡Listo!",
                  timer: 1200,
                  showConfirmButton: false,
                });
              }
            });
        },
      });
    },

    async Comentar(item) {
      this.submited = false;
      let fecha_actual = await this.$refs.layout.fecha_hora_actual(
        this.agencia_seleccionada
      );

      this.frmDatosComentario.cliente_id = item.id;
      this.frmDatosComentario.cliente = item.cliente;
      this.frmDatosComentario.comentario = null;
      $("#mdlRegistroComentario").css("display", "block");
    },

    Registrar() {
      let self = this;
      this.submited = true;
      if (this.$v.frmDatosComentario.$invalid) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Hay uno o más campos vacíos, verifique.",
        });
        return false;
      } else {
        Swal.fire({
          icon: "question",
          text: "¿DESEA REGISTRAR EL COMENTARIO?",
          confirmButtonText:
            '<i class="fas fa-check" style="color:white;"></i>   Si',
          confirmButtonColor: "var(--colorAlto)",
          showCancelButton: true,
          cancelButtonText: '<i class="fas fa-times"></i>   No',
          cancelButtonColor: "var(--plomoOscuroEmpresarial)",
          allowOutsideClick: false,
        }).then((result) => {
          if (result.isConfirmed) {
            let data = new FormData();

            data.append("modo", this.modo);
            data.append("agencia_id", this.agencia_seleccionada);
            data.append(
              "frmDatosComentario",
              JSON.stringify(this.frmDatosComentario)
            );
            this.$inertia.post(route("rep.cli.cumpleanios.comentar"), data, {
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
                  title: "¡ÉXITO!",
                  timer: 1200,
                  showConfirmButton: false,
                }).then(async (result) => {
                  this.BuscarComentarios();

                  let data = new FormData();
                  data.append("agencia_id", this.agencia_seleccionada);
                  data.append("fecha_desde", this.fecha_desde);
                  data.append("fecha_hasta", this.fecha_hasta);
                  data.append("por_asesor", this.por_asesor);
                  data.append("modo", "con_cumpleanios");

                  if (this.por_asesor) {
                    data.append("asesor_id", this.usuario_seleccionado);
                  }
                  const response = await axios.post(
                    route("rep.cli.cumpleanios.buscar"),
                    data
                  );
                  self.lista_clientes = response.data.lista_clientes;
                  this.submited = false;
                  $("#mdlRegistroComentario").css("display", "none");
                });
              },
            });
          } else {
            return false;
          }
        });
      }
    },

    async VerComentarios(item) {
      this.item_seleccionado = item;

      let año = new Date(this.fecha_hasta).getFullYear();

      this.año_comentario = año;
      this.frmDatosComentario.cliente_id = item.id;
      this.frmDatosComentario.cliente = item.cliente;

      await this.BuscarComentarios();

      $("#mdlVerComentarios").css("display", "block");
    },

    BuscarComentarios() {
      let self = this;
      let data = new FormData();
      data.append("agencia_id", this.agencia_seleccionada);
      data.append("cliente_id", this.frmDatosComentario.cliente_id);
      data.append("año_comentario", this.año_comentario);

      // this.$inertia.post(route("rep.cli.cumpleanios.comentarios"), data);

      return axios
        .post(route("rep.cli.cumpleanios.comentarios"), data)
        .then(function (response) {
          self.lista_comentarios = response.data.lista_comentarios;
        });
    },

    Exportar() {
      let data = new FormData();

      data.append("agencia_id", this.agencia_seleccionada);
      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("datos_tabla", JSON.stringify(this.lista_clientes));

      // 	  this.$inertia.post(route("rep.cli.cumpleaños.exportar"), data);
      //   return false;
      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          axios
            .post(route("rep.cli.cumpleanios.exportar"), data)
            .then(function (response) {
              let origin = window.location.origin;

              let path_xlsx = response.data.path_xlsx;

              const link = document.createElement("a");
              link.href = origin + path_xlsx;
              link.download = "rptClientesCumpleanios.xlsx";
              link.click();
              return Swal.fire({
                icon: "success",
                title: "¡EXPORTADO!",
                timer: 2000,
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
.slot-reporte-cumpleanios {
  width: 70% !important;
  margin-left: 15% !important;
}

.mdlRegistroComentario {
  margin-top: 8%;
}

@media (max-width: 900px) {
  .slot-reporte-cumpleanios {
    width: 99% !important;
    margin-left: 0.5% !important;
  }
  .mdlVerComentarios {
    margin-top: 27% !important;
  }
  .mdlRegistroComentario {
    margin-top: 38% !important;
  }
}
</style>
