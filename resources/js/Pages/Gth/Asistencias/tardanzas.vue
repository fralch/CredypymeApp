<template>
  <layout ref="layout">
    <div class="slot_body slot-tardanzas" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose
            :title="(modo == 'personal' ? 'MIS ' : '') + 'TARDANZAS'"
          ></headerClose>

          <div class="card-title">PANEL DE BÚSQUEDA</div>
          <div class="card-body card-block">
            <div class="form-row">
              <fieldset class="form-group col-md-8">
                <legend>
                  <label class="label-title">FILTROS DE BÚSQUEDA</label>
                </legend>
                <div class="row">
                  <div class="input-group col-md-6">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">DESDE</span>
                    </div>
                    <input
                      class="form-control center"
                      type="date"
                      name="desde"
                      id="dtpDesde"
                      style="font-size: 15px"
                    />
                  </div>
                  <div class="input-group col-md-6">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">HASTA</span>
                    </div>
                    <input
                      class="form-control center"
                      type="date"
                      name="hasta"
                      id="dtpHasta"
                      style="font-size: 15px"
                    />
                  </div>
                </div>
              </fieldset>

              <div class="col-md-1">
                <button
                  class="btn btn-action btn-icon-split mt-3"
                  @click="BuscarTardanzas"
                >
                  <span class="icon text-white" style="font-size: 25px">
                    <i class="fas fa-search"></i>
                  </span>
                </button>
              </div>

              <div class="input-group col-md-3 mt-1">
                <div class="input-group-prepend">
                  <span class="input-group-text prepend-title">AGENCIA</span>
                </div>
                <select
                  class="form-control center"
                  name="slcAgencias"
                  id="slcAgencias"
                  data-index="6"
                  v-model="agencia_busqueda"
                  :disabled="
                    modo == 'personal' || lista_tardanzas_filtrados.length == 0
                  "
                >
                  <option :value="0">TODAS</option>
                  <option
                    v-for="(item, index) in agencias"
                    :key="index"
                    :value="item.nombre"
                  >
                    {{ item.nombre }}
                  </option>
                </select>
              </div>

              <div class="input-group col-md-3 mt-1">
                <div class="input-group-prepend">
                  <span class="input-group-text prepend-title">TURNO</span>
                </div>
                <select
                  class="form-control center"
                  name="turnos"
                  id="slcTurnos"
                  v-model="turno_busqueda"
                  data-index="5"
                  :disabled="lista_tardanzas_filtrados.length == 0"
                >
                  <option value="0">TODOS</option>
                  <option value="ingreso_mañana">Turno mañana</option>
                  <option value="ingreso_tarde">Turno tarde</option>
                </select>
              </div>
              <div class="input-group col-md-4 mt-1">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <input
                      type="checkbox"
                      id="chbPorUsuario"
                      v-model="por_asesor"
                      :disabled="
                        modo == 'personal' ||
                        lista_tardanzas_filtrados.length == 0
                      "
                    />
                  </div>
                  <label
                    class="input-group-text prepend-title"
                    for="chbPorUsuario"
                  >
                    COLABORADOR
                  </label>
                </div>
                <div class="input-group-prepend"></div>
                <select
                  class="form-control center"
                  v-model="usuario_seleccionado"
                  id="slcUsuarios"
                  :disabled="!por_asesor || modo == 'personal'"
                  data-index="1"
                >
                  <option :value="0">Seleccione...</option>
                  <option
                    v-for="(item, index) in usuarios_filtrados"
                    :key="index"
                    :value="item.dni"
                  >
                    {{ item.usuario }}
                  </option>
                </select>
              </div>
              <div class="input-group col-md-2 mt-1 offset-7">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="chbMostrarHabilitados"
                    v-model="mostrar_habilitados"
                    :disabled="!por_asesor || modo == 'personal'"
                    @change="FiltrarUsuarios"
                  />
                  <label class="label-title" for="chbMostrarHabilitados"
                    >Sólo habilitados</label
                  >
                </div>
              </div>
            </div>

            <div class="card-title">LISTA DE RESULTADOS</div>
            <div>
              <p>
                Total Minutos:
                <strong>{{ total_minutos_total }}</strong>
              </p>
            </div>

            <table
              class="table table-hover"
              id="t_reporte_tardanzas"
              width="100%"
            >
              <thead>
                <tr>
                  <th
                    style="width: 70px !important"
                    v-show="modo == 'completo'"
                  >
                    JUSTIFICAR
                  </th>
                  <th>DNI</th>
                  <th>APELLIDOS_NOMBRES</th>
                  <th>FECHA_MARCADO</th>
                  <th>MINUTOS_TARDANZA</th>
                  <th>TURNO</th>
                  <th>AGENCIA</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(tardanza, index) in lista_tardanzas_filtrados"
                  :key="index"
                >
                  <td
                    class="table-bordered"
                    align="center"
                    v-show="modo == 'completo'"
                  >
                    <button
                      class="btn btn-action"
                      type="button"
                      title="Justificar"
                      @click="JustificarTardanza(tardanza)"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-edit"></i>
                      </span>
                    </button>
                  </td>
                  <td class="table-bordered" align="center">
                    {{ tardanza.dni }}
                  </td>
                  <td class="table-bordered">
                    {{
                      tardanza.apellido_paterno +
                      " " +
                      tardanza.apellido_materno +
                      " " +
                      tardanza.nombres
                    }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ tardanza.fecha }}
                  </td>
                  <td class="table-bordered t-minutos" align="center">
                    {{ tardanza.minutos }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ tardanza.turno }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ tardanza.nombre_agencia }}
                  </td>
                </tr>
              </tbody>
            </table>
            <hr />
            <div class="text-right">
              <button
                class="btn btn-cancel btn-icon-split"
                title="Exportar"
                @click="Exportar"
                :disabled="lista_tardanzas_filtrados.length == 0"
              >
                <span class="icon text-white">
                  <i class="fas fa-file-excel"></i>
                </span>
                <span class="text">EXPORTAR</span>
              </button>
            </div>
          </div>
        </div>

        <!-- The Modal -->
        <div id="modalJustificarTardanza" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-35 modalJustificarTardanza">
            <div class="content" style="display: block">
              <div class="card">
                <div class="card-header">
                  <div id="c_titulo">
                    <strong id="title">JUSTIFICAR TARDANZA</strong>
                  </div>
                </div>
                <div class="card-title">DATOS TARDANZA</div>
                <div class="card-body card-block">
                  <form>
                    <div class="form-row">
                      <div class="form-group col-sm-2">
                        <label
                          for="txtDni"
                          class="form-control-label label-title"
                          >DNI</label
                        >
                        <input
                          type="text"
                          class="form-control center"
                          id="txtDni"
                          name="dni"
                          v-model="form_datos_tardanza.dni"
                          :disabled="true"
                        />
                      </div>
                      <div class="form-group col-sm-6">
                        <label
                          for="txtNombresCompletos"
                          class="form-control-label label-title"
                          >COLABORADOR</label
                        >
                        <input
                          type="text"
                          class="form-control"
                          id="txtNombresCompletos"
                          name="nombresCompletos"
                          v-model="form_datos_tardanza.nombre_completo"
                          :disabled="true"
                        />
                      </div>
                      <div class="form-group col-sm-4">
                        <label
                          for="txtAgencia"
                          class="form-control-label label-title"
                          >AGENCIA</label
                        >
                        <input
                          type="text"
                          class="form-control center"
                          style="max-width: 300px"
                          id="txtAgencia"
                          name="agencia"
                          v-model="form_datos_tardanza.nombre_agencia"
                          :disabled="true"
                        />
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-sm-6">
                        <label
                          for="txtFechaTardanza"
                          class="form-control-label label-title"
                          >FECHA TARDANZA</label
                        >
                        <input
                          type="text"
                          class="form-control center"
                          id="txtFechaTardanza"
                          name="fecha_tardanza"
                          v-model="form_datos_tardanza.fecha_tardanza"
                          :disabled="true"
                        />
                      </div>
                      <div class="form-group col-sm-3">
                        <label
                          for="txtNombresCompletos"
                          class="form-control-label label-title"
                          >MINUTOS</label
                        >
                        <input
                          type="text"
                          class="form-control center"
                          id="txtMinutos"
                          name="minutos"
                          v-model="form_datos_tardanza.minutos"
                          :disabled="true"
                        />
                      </div>
                      <div class="form-group col-sm-3">
                        <label
                          for="txtNombresCompletos"
                          class="form-control-label label-title"
                          >TURNO</label
                        >
                        <input
                          type="text"
                          class="form-control center"
                          id="txtTipo"
                          name="tipo"
                          v-model="form_datos_tardanza.turno"
                          :disabled="true"
                        />
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-sm-6">
                        <label
                          for="txtJustificación"
                          class="form-control-label label-title"
                          >JUSTIFICACIÓN</label
                        >
                        <textarea
                          type="text"
                          class="form-control mayus"
                          id="txtJustificación"
                          name="justificacion"
                          v-model="form_datos_tardanza.justificacion"
                        ></textarea>
                        <div
                          v-if="
                            submited &&
                            !$v.form_datos_tardanza.justificacion.required
                          "
                          style="color: red; font-size: 12px"
                        >
                          *Ingrese la justificación
                        </div>
                      </div>
                      <div class="form-group col-sm-6">
                        <label
                          class="form-control-label label-title"
                          for="myfile"
                          id="lblDocumentoJustificacion"
                          >DOCUMENTO DE JUSTIFICACIÓN:</label
                        >
                        <input
                          class="btn btn-primary"
                          style="
                            background-color: var(--plomoOscuroEmpresarial);
                            border: none;
                            font-size: 12px;
                            width: 200px;
                          "
                          type="file"
                          id="documentoJustificacion"
                          name="documentoJustificacion"
                          @change="AgregarDocumento"
                        />
                      </div>
                    </div>
                  </form>
                  <hr />
                  <div class="text-right">
                    <button
                      class="btn btn-action btn-icon-split"
                      id="btnJustificar"
                      @click="RegistrarJustificacion"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-save"></i>
                      </span>
                      <span class="text">JUSTIFICAR</span>
                    </button>
                    <button
                      class="btn btn-cancel btn-icon-split"
                      id="btnCancelar"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-times"></i>
                      </span>
                      <span class="text">CANCELAR</span>
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";

import { required } from "vuelidate/lib/validators";
export default {
  components: {
    layout,
    headerClose,
  },
  props: { modo: String, usuarios: Array, agencias: Array },
  data() {
    return {
      windowWidth: window.innerWidth,
      submited: false,

      lista_tardanzas_filtrados: [],
      tardanzas: [],

      turno_busqueda: 0,
      agencia_busqueda: 0,

      total_minutos: 0,
      total_minutos_total: 0,
      usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],
      por_asesor: this.modo == "personal" ? true : false,
      usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,
      mostrar_habilitados: true,

      form_datos_tardanza: {
        id_tardanza: null,
        dni: null,
        nombre_completo: null,
        nombre_agencia: null,
        fecha_tardanza: null,
        minutos: null,
        turno: null,
        justificacion: null,
        documento: null,

        lista_tardanzas: [],
      },
    };
  },
  validations: {
    form_datos_tardanza: {
      justificacion: { required },
    },
  },
  watch: {
    lista_tardanzas_filtrados() {
      $("#t_reporte_tardanzas").DataTable().destroy();
      this.TablaTardanzas();

      this.Sumar_tardanza_filtrados();
    },
    agencia_busqueda() {
      this.FiltrarUsuarios();
    },
    por_asesor() {
      this.usuario_seleccionado = 0;
    },
  },
  mounted() {
    // window.addEventListener("resize", () => {
    //   this.windowWidth = window.innerWidth;
    //   this.AñadirResponsive();
    // });
    // this.AñadirResponsive();
    this.TablaTardanzas();

    this.DiaActual();
  },
  methods: {
    // AñadirResponsive() {
    //   if (this.windowWidth < 1500) {
    //     if (!$("#t_reporte_tardanzas").hasClass("table-responsive")) {
    //       document
    //         .getElementById("t_reporte_tardanzas")
    //         .classList.add("table-responsive");
    //     }
    //   } else {
    //     if ($("#t_reporte_tardanzas").hasClass("table-responsive")) {
    //       document
    //         .getElementById("t_reporte_tardanzas")
    //         .classList.remove("table-responsive");
    //     }
    //   }
    // },
    TablaTardanzas() {
      this.$nextTick(() => {
        var table = $("#t_reporte_tardanzas").DataTable({
          scrollY: "350px",
          scrollX: true,
          fixedColumns: {
            leftColumns: 0,
          },
          ordering: false,

          scrollCollapse: true,
          paging: false,
          fixedHeader: true,
          info: true,

          language: {
            retrieve: true,
            decimal: "",
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
            infoEmpty: "No se encontraron registros",
            infoFiltered: "(filtrado de _MAX_ registros)",
            infoPostFix: "",
            thousands: ",",
            lengthMenu: "Agrupar por _MENU_ filas",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron registros",
            paginate: {
              first: "Primera",
              last: "Ultima",
              next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
              previous:
                '<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
            },
            aria: {
              sortAscending: ": activar para ordenar de forma ascendente",
              sortDescending: ": activar para ordenar de forma descendente",
            },
          },
        });

        $("#slcTurnos").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
          let total_minutos = 0;
          $(".t-minutos").each(function () {
            total_minutos += parseInt($(this).text());
          });
          self.total_minutos_total = total_minutos;
        });

        $("#slcUsuarios").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
          let total_minutos = 0;
          $(".t-minutos").each(function () {
            total_minutos += parseInt($(this).text());
          });
          self.total_minutos_total = total_minutos;
        });

        $("#slcAgencias").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
          let total_minutos = 0;
          $(".t-minutos").each(function () {
            total_minutos += parseInt($(this).text());
          });
          self.total_minutos_total = total_minutos;
        });
      });
    },

    hidenav() {
      return this.$refs.layout.hide_nav();
    },
    shownav() {
      return this.$refs.layout.show_nav();
    },
    DiaActual() {
      let fecha = new Date(); //Fecha actual
      let mes = fecha.getMonth() + 1; //obteniendo mes
      let dia = fecha.getDate(); //obteniendo dia
      let ano = fecha.getFullYear(); //obteniendo año
      if (dia < 10) dia = "0" + dia; //agrega cero si el menor de 10
      if (mes < 10) mes = "0" + mes;

      let desde = ano + "-" + mes + "-" + dia;
      let hasta = ano + "-" + mes + "-" + dia;

      $("#dtpDesde").val(desde);
      $("#dtpHasta").val(hasta);
      //   this.BuscarTardanzas();
    },

    FiltrarUsuarios() {
      if (this.modo == "completo") {
        this.usuarios_filtrados = [];
        this.usuarios_seleccionados = [];

        if (this.mostrar_habilitados) {
          if (this.agencia_busqueda == 0) {
            this.usuarios_filtrados = this.usuarios.filter(
              (item) => item.habilitado == 1
            );
          } else {
            this.usuarios_filtrados = this.usuarios.filter(
              (item) =>
                item.nombre == this.agencia_busqueda && item.habilitado == 1
            );
          }
        } else {
          if (this.agencia_busqueda == 0) {
            this.usuarios_filtrados = this.usuarios;
          } else {
            this.usuarios_filtrados = this.usuarios.filter(
              (item) => item.nombre == this.agencia_busqueda
            );
          }
        }
      } else if (this.modo == "personal") {
        this.usuarios_filtrados = this.usuarios;
      }
    },
    BuscarTardanzas() {
      self = this;
      let data = new FormData();
      data.append("f_desde", $("#dtpDesde").val());
      data.append("f_hasta", $("#dtpHasta").val());

      //   this.$inertia.post(route("gth.asi.tardanzas.listar"), data);

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
          // this.$inertia.post(route("gth.asi.tardanzas.listar"), data);
          axios
            .post(route("gth.asi.tardanzas.listar"), data)
            .then(function (response) {
              if (response.data.length == 0) {
                self.lista_tardanzas_filtrados = [];

                self.FiltrarUsuarios();

                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No se encontraron datos",
                  allowOutsideClick: true,
                });
              } else {
                self.tardanzas = response.data;

                self.FiltrarModo();
                self.FiltrarUsuarios();

                return Swal.fire({
                  icon: "success",
                  title: "¡Listo!",
                });
              }
            });
        },
      });
    },

    FiltrarModo() {
      if (this.modo == "completo") {
        this.lista_tardanzas_filtrados = this.tardanzas;
      }

      if (this.modo == "personal") {
        let id_asesor = this.usuario_seleccionado;

        this.lista_tardanzas_filtrados = this.tardanzas.filter(
          (item) => item.dni == id_asesor
        );
      }
    },

    Filtrar() {
      let id_agencia = this.agencia_busqueda;
      let id_turno = this.turno_busqueda;

      if ((id_agencia == 0) & (id_turno == 0)) {
        this.lista_tardanzas_filtrados = this.tardanzas;
      } else if ((id_agencia != 0) & (id_turno == 0)) {
        this.lista_tardanzas_filtrados = this.tardanzas.filter(
          (item) => item.agencia_id == id_agencia
        );
      } else if ((id_agencia == 0) & (id_turno != 0)) {
        this.lista_tardanzas_filtrados = this.tardanzas.filter(
          (item) => item.turno == id_turno
        );
      } else {
        this.lista_tardanzas_filtrados = this.tardanzas.filter(
          (item) => (item.agencia_id == id_agencia) & (item.turno == id_turno)
        );
      }
    },

    JustificarTardanza(tardanza) {
      this.submited = false;
      this.form_datos_tardanza.id_tardanza = tardanza.id;
      this.form_datos_tardanza.dni = tardanza.dni;
      this.form_datos_tardanza.nombre_completo =
        tardanza.nombres +
        " " +
        tardanza.apellido_paterno +
        " " +
        tardanza.apellido_materno;
      this.form_datos_tardanza.nombre_agencia = tardanza.nombre_agencia;
      this.form_datos_tardanza.fecha_tardanza = tardanza.fecha;
      this.form_datos_tardanza.minutos = tardanza.minutos;
      this.form_datos_tardanza.turno = tardanza.turno;
      this.form_datos_tardanza.justificacion = null;
      $("#documentoJustificacion").val("");
      this.form_datos_tardanza.documento = null;

      $("#modalJustificarTardanza").css("display", "block");
      $("#btnCancelar").click(function () {
        $("#modalJustificarTardanza").css("display", "none");
      });
    },
    AgregarDocumento(e) {
      this.form_datos_tardanza.documento = e.target.files[0];
    },
    RegistrarJustificacion() {
      self = this;
      this.submited = true;
      if (this.$v.form_datos_tardanza.$invalid) {
        return false;
      } else {
        Swal.fire({
          title: "JUSTIFICAR TARDANZA",
          text: "¿Desea continuar?",
          confirmButtonText:
            '<i class="fas fa-check" style="color:white;"></i>   Si',
          confirmButtonColor: "var(--colorAlto)",
          showCancelButton: true,
          cancelButtonText: '<i class="fas fa-times"></i>   No',
          cancelButtonColor: "var(--plomoOscuroEmpresarial)",
          allowOutsideClick: false,
          preConfirm: (result) => {
            let data = new FormData();
            data.append("id_tardanza", this.form_datos_tardanza.id_tardanza);
            data.append(
              "justificacion",
              this.form_datos_tardanza.justificacion
            );
            data.append("documento", this.form_datos_tardanza.documento);

            axios
              .post(route("gth.asi.tardanzas.justificar"), data)
              .then(function (response) {
                let resultado = response.data;
                if (resultado == "EXITO") {
                  Swal.fire({
                    icon: "success",
                    title: "¡EXITO!",
                    text: "Tardanza justificada",
                    allowOutsideClick: false,
                    preConfirm: (result) => {
                      self.$inertia.get(route("gth.asi.tardanzas", self.modo));
                    },
                  });
                } else {
                  Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Algo salió mal",
                  });
                }
              });
          },
        });
      }
    },
    Sumar_tardanza_filtrados() {
      var self = this;
      self.total_minutos_total = 0;
      self.lista_tardanzas_filtrados.forEach((element) => {
        self.total_minutos_total += parseInt(element.minutos);
      });
    },

    Exportar() {
      self = this;

      self.Filtrar();

      let data = new FormData();
      data.append("datos", JSON.stringify(this.lista_tardanzas));

      data.append("total_minutos_total", this.total_minutos_total);

      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          //   this.$inertia.post(route("gth.asi.tardanzas.exportar"), data);

          axios
            .post(route("gth.asi.tardanzas.exportar"), data)
            .then(function (response) {
              //   return Swal.fire({
              //     icon: "success",
              //     title: "¡Listo!",
              //   });

              var linkSource =
                "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
                response.data;
              var downloadLink = document.createElement("a");
              var fileName = "rptTardanzas.xlsx";

              downloadLink.href = linkSource;
              downloadLink.download = fileName;
              downloadLink.click();

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

    Filtrar() {
      this.lista_tardanzas = this.lista_tardanzas_filtrados;

      let id_turno = this.turno_busqueda;
      let id_asesor = this.usuario_seleccionado;
      let id_agencia = this.agencia_busqueda;

      if (this.usuario_seleccionado != 0) {
        this.lista_tardanzas = this.lista_tardanzas.filter(
          (item) => item.dni == id_asesor
        );
      }

      if (id_turno != 0) {
        this.lista_tardanzas = this.lista_tardanzas.filter(
          (item) => item.turno == id_turno
        );
      }
      if (id_agencia != 0) {
        this.lista_tardanzas = this.lista_tardanzas.filter(
          (item) => item.nombre_agencia == id_agencia
        );
      }
    },
  },
};
</script>

<style >
.slot-tardanzas {
  width: 60% !important;
  margin-left: 20% !important;
}
.modalJustificarTardanza {
  margin-top: 2%;
}

@media (max-width: 900px) {
  .slot-tardanzas {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .modalJustificarTardanza {
    margin-top: 20%;
  }
}
</style>

