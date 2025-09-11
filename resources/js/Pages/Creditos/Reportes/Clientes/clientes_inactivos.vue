<template>
  <layout ref="layout">
    <div
      class="slot_body slot-reporte-clientes-inactivos"
      slot="component-view"
    >
      <div class="content" style="display: block">
        <div class="card">
          <headerClose
            :title="
              (modo == 'personal' ? 'MIS ' : '') +
              'CLIENTES' +
              (modo == 'personal' ? ' - ' : ' ') +
              'INACTIVOS'
            "
          ></headerClose>

          <div class="card-body card-block">
            <div class="form-row justify-content-md-center">
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
                      v-model="agencia_busqueda"
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
              <div class="col-md-1" v-if="windowWidth >= 900">
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
            </div>
            <div class="form-row justify-content-md-center mt-1">
              <div class="input-group col-md-5">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <input
                      type="checkbox"
                      id="chbPorAsesor"
                      v-model="por_asesor"
                      :disabled="modo == 'personal'"
                    />
                    <label class="prepend-title m-0 ml-1" for="chbPorAsesor">
                      POR ASESOR
                    </label>
                  </div>
                </div>

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
                <div class="input-group-append">
                  <div class="input-group-text">
                    <input
                      type="checkbox"
                      id="chbMostrarHabilitados"
                      v-model="mostrar_habilitados"
                      :disabled="!por_asesor || modo == 'personal'"
                      @change="FiltrarUsuarios"
                    />
                    <label
                      class="m-0 ml-1"
                      for="chbMostrarHabilitados"
                      v-if="windowWidth >= 900"
                    >
                      Habilitados</label
                    >

                    <label
                      class="m-0 ml-1"
                      for="chbMostrarHabilitados"
                      v-if="windowWidth < 900"
                    >
                      Hab.</label
                    >
                  </div>
                </div>
              </div>

              <div class="input-group col-md-3 col-6">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <input
                      type="checkbox"
                      id="chbPorCalifacion"
                      v-model="por_calificacion"
                    />
                    <label
                      class="prepend-title m-0 ml-1"
                      for="chbPorCalifacion"
                    >
                      CALIF.
                    </label>
                  </div>
                </div>

                <select
                  class="form-control center"
                  v-model="calificacion_seleccionado"
                  :disabled="!por_calificacion"
                >
                  <option :value="0" disabled selected>Seleccione...</option>
                  <option :value="'A'">A</option>
                  <option :value="'B'">B</option>
                  <option :value="'C'">C</option>
                  <option :value="'D'">D</option>
                  <option :value="'E'">E</option>
                </select>
              </div>
              <div class="input-group col-md-3 col-6">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <input
                      type="checkbox"
                      id="chbPorTipoNumero"
                      v-model="por_numero_credito"
                    />
                    <label
                      class="prepend-title m-0 ml-1"
                      for="chbPorTipoNumero"
                    >
                      N° CREDITO
                    </label>
                  </div>
                </div>

                <input
                  class="form-control center"
                  type="number"
                  min="1"
                  step="1"
                  name="numero"
                  v-model="numero_seleccionado"
                  @change="Redondear"
                  :disabled="!por_numero_credito"
                />
              </div>
            </div>

            <div class="card-title mt-2">LISTA DE RESULTADOS</div>

            <div class="form-row col-md-12 mt-2">
              <div class="form-group col-md-6 col-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Buscar </span>
                  </div>
                  <input
                    class="form-control mayus"
                    type="text"
                    id="inpBuscarInactivo"
                    :disabled="lista_clientes_inactivos.length == 0"
                    placeholder="Ingrese 3 caractéres como mínimo..."
                    autocomplete="off"
                    spellcheck="false"
                    @focus="hidenav()"
                    @blur="shownav()"
                  />
                </div>
              </div>
            </div>
            <table
              class="table"
              id="tblInactivos"
              style="width: 100% !important"
            >
              <thead>
                <tr>
                  <th style="min-width: 20px !important">N°</th>
                  <th style="min-width: 150px !important">FECHA_INACTIVO</th>
                  <th style="min-width: 100px !important">TIEMPO_INACTIVO</th>
                  <th style="min-width: 50px !important">EXP.</th>
                  <th style="min-width: 70px !important">DNI</th>
                  <th style="min-width: 50px !important">CALIF.</th>
                  <th style="min-width: 250px !important">CLIENTE</th>
                  <th style="min-width: 150px !important">TELÉFONOS</th>
                  <th style="min-width: 100px !important">ASESOR</th>
                  <th style="min-width: 70px !important">C_RIESGO</th>
                  <th style="min-width: 300px !important">
                    DIRECCIÓN_DOMICILIO
                  </th>
                  <th style="min-width: 300px !important">DIRECCIÓN_NEGOCIO</th>
                  <th style="min-width: 300px !important">NOTAS</th>
                  <th style="min-width: 300px !important">ÚLTIMO COMENTARIO</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in lista_clientes_inactivos"
                  :key="index"
                  class="table-bordered"
                  :class="index % 2 == 0 ? 'verde-claro' : ''"
                  @dblclick="VerComentarios(item)"
                >
                  <td align="center">{{ item.index + 1 }}</td>
                  <td align="center">
                    {{ item.fecha_inactivo }}
                  </td>
                  <td align="center">{{ item.tiempo_inactivo }} mes(es)</td>
                  <td align="center">
                    {{ item.expediente }}
                  </td>
                  <td align="center">
                    {{ item.dni }}
                  </td>
                  <td align="center">
                    {{ item.calificacion }}
                  </td>
                  <td align="left">
                    {{ item.cliente }}
                  </td>
                  <td align="center">
                    {{ item.telefonos }}
                  </td>
                  <td align="center">
                    {{ item.asesor }}
                  </td>
                  <td align="center">
                    {{ item.central_riesgo }}
                  </td>
                  <td align="left">
                    {{ item.direccion_domicilio }}
                  </td>

                  <td align="left">
                    {{ item.direccion_negocio }}
                  </td>
                  <td align="left">
                    {{ item.notas }}
                  </td>
                  <td align="left">
                    {{ item.ultimo_comentario }}
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
                :disabled="lista_clientes_inactivos.length == 0"
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

      <div id="modalComentarioCliente" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-50 modalComentarioCliente">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="'COMENTARIO '"
                :nombre_modal="'modalComentarioCliente'"
              ></headerCloseModal>

              <div class="card-body card-block">
                <div class="form-row">
                  <div class="input-group col-md-7 mb-1">
                    <div class="input-group-prepend">
                      <label class="input-group-text" for="rdbPorNombre">
                        CLIENTE
                      </label>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      spellcheck="false"
                      v-model="form_datos_cliente.nombres_completos"
                      disabled="true"
                    />
                  </div>
                </div>

                <table class="table" id="tblComentario" width="100%">
                  <thead>
                    <tr>
                      <th style="min-width: 10px !important">N°</th>
                      <th style="min-width: 200px !important">COMENTARIO</th>
                      <th style="min-width: 40px !important">FECHA_REGISTRO</th>
                      <th style="min-width: 40px !important">
                        USUARIO_REGISTRO
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in lista_inactivos_comentarios"
                      :key="index"
                      :class="index % 2 == 0 ? 'verde-claro' : ''"
                    >
                      <td align="center">{{ index + 1 }}</td>

                      <td align="left">
                        {{ item.comentario }}
                      </td>
                      <td align="center">
                        {{ item.fecha_comentario }}
                      </td>
                      <td align="center">
                        {{ item.usuario }}
                      </td>
                    </tr>
                  </tbody>
                </table>
                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-cancel btn-icon-split"
                    @click="AgregarComentario"
                  >
                    <span class="icon text-white"
                      ><i class="fas fa-plus"></i>
                    </span>
                    <span class="text">AGREGAR</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="modalComentario" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-30 modalComentario">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="'AGREGAR COMENTARIO '"
                :nombre_modal="'modalComentario'"
              ></headerCloseModal>
              <div class="card-body card-block">
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label class="form-control-label label-title"
                      >Ingrese su comentario de inactividad:</label
                    >
                    <span
                      v-if="
                        submited && !$v.form_datos_cliente.comentario.required
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
                      v-model="form_datos_cliente.comentario"
                    ></textarea>
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
      </div>
    </div>
  </layout>
</template>

<script>
import { required } from "vuelidate/lib/validators";

import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,
  },
  props: {
    modo: String,
    usuarios: Array,
  },

  data() {
    return {
      submited: false,
      agencia_busqueda: 0,
      agencias_permitidas: [],

      windowWidth: window.innerWidth,

      fecha_desde: null,
      fecha_hasta: null,
      usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],

      por_asesor: this.modo == "personal" ? true : false,
      usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,
      mostrar_habilitados: true,

      por_calificacion: false,
      por_numero_credito: false,
      numero_seleccionado: 1,
      calificacion_seleccionado: 0,
      lista_clientes_inactivos: [],

      lista_inactivos_comentarios: [],

      form_datos_cliente: {
        nombres_completos: null,
        comentario: null,
        cliente_id: null,
      },
    };
  },
  validations: {
    form_datos_cliente: {
      comentario: { required },
    },
  },
  watch: {
    agencias_permitidas(value) {
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
    },

    agencia_busqueda() {
      this.FiltrarUsuarios();
      this.FechaActual();
    },

    por_asesor() {
      this.usuario_seleccionado = 0;
    },
    por_calificacion() {
      this.calificacion_seleccionado = 0;
    },
    lista_clientes_inactivos() {
      $("#tblInactivos").DataTable().destroy();
      this.TablaInactivos();
    },

    lista_inactivos_comentarios() {
      $("#tblComentario").DataTable().destroy();
      this.TablaComentario();
    },
  },

  mounted() {
    this.ListarAgenciasPermitidas();
    this.TablaInactivos();
    this.TablaComentario();

    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },

  methods: {
    hidenav() {
      this.$refs.layout.hide_nav();
    },
    shownav() {
      this.$refs.layout.show_nav();
    },
    ListarAgenciasPermitidas() {
      if (this.modo == "personal") {
        this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
          "CREDITOS_REPORTES/CLIENTES_MIS_INACTIVOS"
        );
        this.filtro_usuario = true;
      } else if (this.modo == "completo") {
        this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
          "CREDITOS_REPORTES/CLIENTES_INACTIVOS"
        );
      } else {
        this.agencias_permitidas = [];
      }
    },
    async FechaActual() {
      if (this.agencia_busqueda == null) {
        return false;
      } else {
        let fecha_actual = await this.$refs.layout.fecha_hora_actual(
          this.agencia_busqueda
        );

        fecha_actual = fecha_actual.substring(0, 10);

        this.fecha_desde = fecha_actual;
        this.fecha_hasta = fecha_actual;
      }
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

      if (e.target.value && e.target.value >= 1) {
        valor = e.target.value;
      }

      this.numero_seleccionado = this.roundTo(valor, 0);
    },

    TablaInactivos() {
      let self = this;
      this.$nextTick(() => {
        let scroll_height = "300px";
        if (this.windowWidth <= 900) {
          scroll_height = "200px";
        }
        var table = $("#tblInactivos").DataTable({
          scrollY: scroll_height,
          scrollX: true,
          scrollCollapse: true,
          paging: true,
          lengthChange: false,
          pageLength: 100,
          ordering: false,
          fixedHeader: true,
          info: true,
          select: {
            style: "single",
            info: false,
          },
          language: {
            retrieve: true,
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
            infoEmpty: "No se encontraron registros",
            infoFiltered: "(filtrado de _MAX_ registros)",
            thousands: ",",
            paginate: {
              first: "Primera",
              last: "Ultima",
              next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
              previous:
                '<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
            },
          },
        });

        $("#inpBuscarInactivo").keyup(function () {
          table.column(6).search(this.value).draw();
        });
      });
    },
    TablaComentario() {
      this.$nextTick(() => {
        var table = $("#tblComentario").DataTable({
          scrollY: "350px",
          scrollX: true,
          scrollCollapse: true,
          paging: false,
          ordering: false,
          fixedHeader: true,
          info: true,
          select: {
            style: "single",
            info: false,
          },
          language: {
            retrieve: true,
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
            infoEmpty: "No se encontraron registros",
            infoFiltered: "(filtrado de _MAX_ registros)",
            thousands: ",",
            paginate: {
              first: "Primera",
              last: "Ultima",
              next: '<i class="fas fa-chevron-circle-right" style="font-size:20px;"></i>',
              previous:
                '<i class="fas fa-chevron-circle-left" style="font-size:20px;"></i>',
            },
          },
        });

        $("#inpBuscarInactivo").keyup(function () {
          table.column(3).search(this.value).draw();
        });
      });
    },

    FiltrarUsuarios() {
      if (this.modo == "completo") {
        this.usuarios_filtrados = [];
        this.usuarios_seleccionados = [];

        if (this.mostrar_habilitados) {
          this.usuarios_filtrados = this.usuarios.filter(
            (item) =>
              item.agencia_id == this.agencia_busqueda && item.habilitado == 1
          );
        } else {
          this.usuarios_filtrados = this.usuarios.filter(
            (item) => item.agencia_id == this.agencia_busqueda
          );
        }
      } else if (this.modo == "personal") {
        this.usuarios_filtrados = this.usuarios;
      }
    },

    Buscar() {
      let self = this;

      let data = new FormData();
      data.append("agencia_id", this.agencia_busqueda);
      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("por_asesor", this.por_asesor);
      data.append("por_calificacion", this.por_calificacion);
      data.append("por_numero_credito", this.por_numero_credito);

      data.append("modo", "inactivos");

      if (this.por_asesor) {
        data.append("asesor_id", this.usuario_seleccionado);
      }
      if (this.por_calificacion) {
        data.append("calificacion", this.calificacion_seleccionado);
      }

      if (this.por_numero_credito) {
        data.append("numero_credito", this.numero_seleccionado);
      }

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          // this.$inertia.post(route("rep.cli.clientes_inactivos.buscar"), data);
          // return false;

          Swal.showLoading();
          axios
            .post(route("rep.cli.clientes_inactivos.buscar"), data)
            .then(function (response) {
              if (response.data.lista_clientes_inactivos.length == 0) {
                self.lista_clientes_inactivos = [];

                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No se encontraron datos",
                  allowOutsideClick: true,
                });
              } else {
                self.lista_clientes_inactivos =
                  response.data.lista_clientes_inactivos;

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
    ListarComentarios(agencia_id, cliente_id) {
      this.form_datos_cliente.comentario = null;

      let self = this;

      let data = new FormData();
      data.append("agencia_id", agencia_id);
      data.append("cliente_id", cliente_id);

      return axios
        .post(route("rep.cli.clientes_inactivos.listar_comentarios"), data)
        .then(function (response) {
          self.lista_inactivos_comentarios =
            response.data.lista_inactivos_comentarios;
        });
    },
    async VerComentarios(item) {
      this.form_datos_cliente.nombres_completos = item.cliente;
      this.form_datos_cliente.cliente_id = item.cliente_id;

      await this.ListarComentarios(this.agencia_busqueda, item.cliente_id);

      $("#modalComentarioCliente").css("display", "block");
    },

    AgregarComentario() {
      $("#modalComentario").css("display", "block");
    },

    Registrar() {
      let self = this;
      this.submited = true;
      if (this.$v.form_datos_cliente.$invalid) {
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
            data.append("agencia_id", this.agencia_busqueda);
            data.append("cliente_id", this.form_datos_cliente.cliente_id);
            data.append("comentario", this.form_datos_cliente.comentario);
            this.$inertia.post(
              route("rep.cli.clientes_inactivos.comentar"),
              data,
              {
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
                  this.ListarComentarios(
                    this.agencia_busqueda,
                    this.form_datos_cliente.cliente_id
                  );

                  this.form_datos_cliente.comentario = null;
                  $("#modalComentario").css("display", "none");
                  return Swal.fire({
                    icon: "success",
                    title: "¡ÉXITO!",
                    allowOutsideClick: false,
                  });
                },
              }
            );
          } else {
            return false;
          }
        });
      }
    },

    Exportar() {
      let data = new FormData();
      data.append("datos_tabla", JSON.stringify(this.lista_clientes_inactivos));
      data.append("tipo", "clientes_inactivos");

      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          // this.$inertia.post(
          //	route("rep.cli.clientes_inactivos.exportar"),
          //	data
          //);
          // return false;

          Swal.showLoading();

          axios
            .post(route("rep.cli.clientes_inactivos.exportar"), data)
            .then(function (response) {
              let path_xlsx = response.data.path_xlsx;

              const link = document.createElement("a");
              link.href = origin + path_xlsx;
              link.download = "rptClientesInactivos.xlsx";
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
.slot-reporte-clientes-inactivos {
  width: 70% !important;
  margin-left: 15% !important;
}

.modalComentarioCliente {
  margin-top: 2%;
}
.modalComentario {
  margin-top: 4%;
}

@media (max-width: 900px) {
  .slot-reporte-clientes-inactivos {
    width: 99% !important;
    margin-left: 0.5% !important;
  }
  .modalComentarioCliente {
    margin-top: 25%;
  }
  .modalComentario {
    margin-top: 35%;
  }
}
</style>
