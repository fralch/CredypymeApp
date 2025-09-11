<template>
  <layout ref="layout">
    <div class="slot_body slot-permisos" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose :title="'PERMISOS'"></headerClose>
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
                  <div class="input-group col-md-6">
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
                </div>
              </fieldset>

              <div class="col-md-1">
                <button
                  class="btn btn-action btn-icon-split mt-3"
                  @click="Buscar"
                >
                  <span class="icon text-white" style="font-size: 25px">
                    <i class="fas fa-search"></i>
                  </span>
                </button>
              </div>

              <div class="respon col-md-2">
                <button
                  class="btn btn-cancel btn-icon-split"
                  title="ExportarGeneral"
                  @click="ExportarGeneral"
                  :disabled="permisos.length == 0"
                >
                  <span class="icon text-white">
                    <i class="fas fa-file-excel"></i>
                  </span>
                  <span class="text">EXPORT. G</span>
                </button>
              </div>
            </div>

            <div class="card-title">LISTA DE RESULTADOS</div>

            <div class="form-row">
              <div class="input-group col-md-3 mt-1">
                <div class="input-group-prepend">
                  <span class="input-group-text prepend-title">ESTADO</span>
                </div>
                <select
                  class="form-control center"
                  name="slcEstado"
                  id="slcEstado"
                  v-model="estado_seleccionado"
                  data-index="2"
                  :disabled="permisos.length == 0"
                >
                  <option value="0">TODAS</option>
                  <option value="APROBADO">APROBADO</option>
                  <option value="ELIMINADO">ELIMINADO</option>
                  <option value="PENDIENTE">PENDIENTE</option>
                  <option value="VALIDADO">VALIDADO</option>
                  <option value="NO VALIDADO">NO VALIDADO</option>
                  <option value="UTILIZADO">UTILIZADO</option>
                  <option value="CANCELADO">CANCELADO</option>
                </select>
              </div>
              <div class="input-group col-md-4 mt-1">
                <div class="input-group-prepend">
                  <span class="input-group-text prepend-title">AGENCIA</span>
                </div>
                <select
                  class="form-control center"
                  name="slcAgencias"
                  id="slcAgencias"
                  data-index="4"
                  v-model="agencia_busqueda"
                  :disabled="permisos.length == 0"
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

              <div class="input-group col-md-5 mt-1">
                <div class="input-group-prepend">
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
                  data-index="3"
                  :disabled="permisos.length == 0"
                >
                  <option :value="0">Seleccione...</option>
                  <option
                    v-for="(item, index) in usuarios_filtrados"
                    :key="index"
                    :value="item.usuario"
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
                      :disabled="permisos.length == 0"
                      @change="FiltrarUsuarios"
                    />
                    <label class="m-0 ml-1" for="chbHabilitados">Hab.</label>
                  </div>
                </div>
              </div>
            </div>

            <table class="table table-hover" id="tblPermisos" width="100%">
              <thead>
                <tr>
                  <th style="min-width: 70px !important">VER</th>
                  <th style="min-width: 100px !important">FECHA SOLICITUD</th>
                  <th style="min-width: 70px !important">ESTADO</th>

                  <th style="min-width: 90px !important">USUARIO</th>
                  <th style="min-width: 70px !important">AGENCIA</th>
                  <th style="min-width: 70px !important">FECHA_PERMISO</th>
                  <th style="min-width: 70px !important">TIPO</th>
                  <th style="min-width: 70px !important">HORA_I</th>
                  <th style="min-width: 70px !important">HORA_F</th>
                  <th style="min-width: 70px !important">HORA_R</th>
                  <th style="min-width: 70px !important">TIEMPO</th>
                  <th style="min-width: 70px !important">APROBADOR</th>
                  <th style="min-width: 70px !important">GOCE</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in permisos" :key="index">
                  <td class="table-bordered" align="center">
                    <button
                      class="btn btn-action"
                      type="button"
                      title="VER PERMISO"
                      @click="Ver(item)"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-bars"></i>
                      </span>
                    </button>
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.fecha_solicitud }}
                  </td>
                  <td
                    class="table-bordered"
                    align="center"
                    :class="
                      item.estado == 'APROBADO'
                        ? 'clas_G'
                        : item.estado == 'ELIMINADO'
                        ? 'clas_R'
                        : item.estado == 'VALIDADO'
                        ? 'clas_V'
                        : item.estado == 'DENEGADO'
                        ? 'clas_R'
                        : item.estado == 'PENDIENTE'
                        ? 'clas_G'
                        : item.estado == 'NO VALIDADO'
                        ? 'clas_R'
                        : item.estado == 'UTILIZADO'
                        ? 'clas_U'
                        : item.estado == 'CANCELADO'
                        ? 'clas_R'
                        : ''
                    "
                  >
                    {{ item.estado }}
                  </td>

                  <td class="table-bordered" align="center">
                    {{ item.usuario }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.agencia }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.fecha_permiso }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.modo }}
                  </td>

                  <td class="table-bordered" align="center">
                    {{ item.hora_inicio }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.hora_fin }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{
                      item.hora_retorno_v == null ? "-" : item.hora_retorno_v
                    }}
                  </td>
                  <td
                    class="table-bordered"
                    align="center"
                    :style="{
                      'font-size': '11.5px !important',
                      'font-weight': 'bolder',
                    }"
                  >
                    {{ item.tiempo }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.usuario_aprobador }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.goce == 1 ? "Si" : "No" }}
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
                :disabled="permisos.length == 0"
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
        <div id="mdlPermiso" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-40 mdlPermiso">
            <div class="content" style="display: block">
              <div class="card">
                <div
                  class="card-header d-flex align-items-center justify-content-between"
                >
                  <strong>{{ title_modal }}</strong>
                  <button
                    type="button"
                    class="btn btn-action"
                    style="border-radius: 50%; float: right !important"
                    @click="Cerrar"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-times"></i>
                    </span>
                  </button>
                </div>

                <div class="card-title">DATOS PERMISO</div>
                <div class="card-body card-block">
                  <form>
                    <fieldset class="form-group col-md-12">
                      <legend>
                        <label class="label-title">SOLICITUD</label>
                      </legend>

                      <div class="form-row">
                        <div class="form-group col-sm-6">
                          <label class="form-control-label label-title"
                            >FECHA SOLICITUD</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            name="fecha_solicitud"
                            v-model="form_datos_permiso.fecha_solicitud"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-6">
                          <label class="form-control-label label-title"
                            >FECHA PERMISO</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            name="fecha_permiso"
                            v-model="form_datos_permiso.fecha_permiso"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >HORA INICIO</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            style="max-width: 300px"
                            name="hora_inicio"
                            v-model="form_datos_permiso.hora_inicio"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >HORA FIN</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            style="max-width: 300px"
                            name="hora_fin"
                            v-model="form_datos_permiso.hora_fin"
                            disabled
                          />
                        </div>

                        <div class="form-group col-md-4">
                          <label class="form-control-label label-title"
                            >MODO</label
                          >
                          <div class="input_modo">
                            <select
                              class="form-control center"
                              name="slcModo"
                              v-model="form_datos_permiso.modo"
                              style="max-width: 250px; display: inline-block"
                              :disabled="
                                this.form_datos_permiso.estado != 'VALIDADO' &&
                                this.form_datos_permiso.estado != 'UTILIZADO'
                              "
                            >
                              <option value="HORAS">HORAS</option>
                              <option
                                v-if="this.form_datos_permiso.dia_semana != 7"
                                value="TURNO-M"
                              >
                                TURNO MAÑANA
                              </option>
                              <option
                                v-if="this.form_datos_permiso.dia_semana != 7"
                                value="TURNO-T"
                              >
                                TURNO TARDE
                              </option>
                            </select>
                          </div>
                        </div>

                        <div
                          class="form-group col-sm-4"
                          v-if="
                            (this.form_datos_permiso.estado == 'VALIDADO' ||
                              this.form_datos_permiso.estado == 'UTILIZADO') &&
                            this.form_datos_permiso.modo == 'HORAS'
                          "
                        >
                          <label class="form-control-label label-title"
                            >HORA RETORNO</label
                          >
                          <div class="time-input">
                            <input
                              type="time"
                              class="form-control"
                              v-model="form_datos_permiso.hora_retorno"
                            />
                          </div>
                        </div>

                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >TIEMPO</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            style="max-width: 300px"
                            name="tiempo"
                            v-model="form_datos_permiso.tiempo"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-12">
                          <label class="form-control-label label-title"
                            >POR MOTIVO</label
                          >
                          <textarea
                            class="form-control mayus text-row"
                            rows="2"
                            v-model="form_datos_permiso.detalle"
                            disabled
                          ></textarea>
                        </div>
                        <div class="form-group col-sm-12">
                          <label class="form-control-label label-title"
                            >DOCUMENTO:</label
                          >
                          <button
                            class="btn btn-action"
                            type="button"
                            title="VER FOTO"
                            @click="VerFoto()"
                            v-if="form_datos_permiso.documento != null"
                          >
                            <span class="icon text-white">
                              <i class="fas fa-eye"></i>
                            </span>
                          </button>
                          <label
                            class="form-control-label label-title"
                            v-if="form_datos_permiso.documento == null"
                            >No hay documento</label
                          >
                        </div>
                      </div>
                    </fieldset>

                    <fieldset
                      class="form-group col-md-12"
                      v-if="
                        form_datos_permiso.estado != 'PENDIENTE' &&
                        form_datos_permiso.estado != 'ELIMINADO'
                      "
                    >
                      <legend>
                        <label class="label-title">APROBACIÓN</label>
                      </legend>
                      <div class="form-row">
                        <div class="form-group col-sm-3">
                          <label class="form-control-label label-title"
                            >USUARIO</label
                          >
                          <input
                            type="text"
                            class="form-control"
                            name="usuario_aprobacion"
                            v-model="form_datos_permiso.usuario_aprobador"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-6">
                          <label class="form-control-label label-title"
                            >CARGO</label
                          >
                          <input
                            type="text"
                            class="form-control"
                            name="cargo"
                            v-model="form_datos_permiso.aprobador_cargo"
                            disabled
                          />
                        </div>

                        <div class="form-group col-sm-3">
                          <label class="label-title ml-2">APROBADO</label>
                          <div class="row ml-1">
                            <div class="form-check mr-3">
                              <input
                                class="form-check-input"
                                type="radio"
                                :value="1"
                                name="estado"
                                id="si"
                                v-model="form_datos_permiso.estado_aprobacion"
                                disabled
                              />
                              <label class="form-check-label" for="si">
                                Si
                              </label>
                            </div>
                            <div class="form-check">
                              <input
                                class="form-check-input"
                                type="radio"
                                :value="0"
                                name="estado"
                                id="no"
                                v-model="form_datos_permiso.estado_aprobacion"
                                disabled
                              />
                              <label class="form-check-label" for="no">
                                No
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-sm-12">
                          <label
                            class="form-control-label label-title"
                            v-if="
                              form_datos_permiso.estado == 'APROBADO' ||
                              form_datos_permiso.estado == 'VALIDADO' ||
                              form_datos_permiso.estado == 'NO VALIDADO' ||
                              form_datos_permiso.estado == 'UTILIZADO' ||
                              form_datos_permiso.estado == 'CANCELADO'
                            "
                            >COMENTARIO APROBACIÓN</label
                          >
                          <label
                            class="form-control-label label-title"
                            v-if="form_datos_permiso.estado == 'DENEGADO'"
                            >COMENTARIO DENEGADO</label
                          >
                          <textarea
                            class="form-control mayus text-row"
                            rows="2"
                            v-model="form_datos_permiso.comentario"
                            disabled
                          ></textarea>
                        </div>
                      </div>
                    </fieldset>
                    <fieldset
                      class="form-group col-md-12"
                      v-if="
                        form_datos_permiso.estado == 'APROBADO' ||
                        form_datos_permiso.estado == 'VALIDADO' ||
                        form_datos_permiso.estado == 'NO VALIDADO' ||
                        form_datos_permiso.estado == 'UTILIZADO' ||
                        form_datos_permiso.estado == 'CANCELADO'
                      "
                    >
                      <legend>
                        <label class="label-title">VALIDACIÓN</label>
                      </legend>
                      <div class="form-row">
                        <div class="form-group col-md-9">
                          <label class="form-control-label label-title"
                            >USUARIO</label
                          >
                          <input
                            type="text"
                            class="form-control"
                            style="width: 120px"
                            name="usuario_validador"
                            v-model="form_datos_permiso.usuario_validador"
                            disabled
                          />
                        </div>

                        <div class="form-group col-md-3">
                          <label class="label-title ml-2">GOCE</label>
                          <div class="row ml-1">
                            <div class="form-check mr-3">
                              <input
                                class="form-check-input"
                                type="radio"
                                :value="1"
                                name="goce"
                                id="si"
                                v-model="form_datos_permiso.goce"
                                :disabled="
                                  this.form_datos_permiso.estado != 'APROBADO'
                                "
                              />
                              <label class="form-check-label" for="si">
                                Si
                              </label>
                            </div>
                            <div class="form-check">
                              <input
                                class="form-check-input"
                                type="radio"
                                :value="0"
                                name="goce"
                                id="no"
                                v-model="form_datos_permiso.goce"
                                :disabled="
                                  this.form_datos_permiso.estado != 'APROBADO'
                                "
                              />
                              <label class="form-check-label" for="no">
                                No
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-sm-12">
                          <label class="form-control-label label-title"
                            >OBSERVACIÓN</label
                          >
                          <textarea
                            class="form-control mayus text-row"
                            rows="2"
                            v-model="form_datos_permiso.observacion"
                            :disabled="
                              this.form_datos_permiso.estado != 'APROBADO'
                            "
                          ></textarea>
                        </div>
                      </div>
                    </fieldset>
                  </form>
                  <hr />
                  <div class="text-right">
                    <button
                      class="btn btn-action btn-icon-split"
                      id="btnValidar"
                      @click="Validar"
                      v-if="this.form_datos_permiso.estado == 'APROBADO'"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-check"></i>
                      </span>
                      <span class="text">VALIDAR</span>
                    </button>
                    <button
                      class="btn btn-cancel btn-icon-split"
                      @click="Invalidar"
                      v-if="this.form_datos_permiso.estado == 'APROBADO'"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-times"></i>
                      </span>
                      <span class="text">NO VALIDAR</span>
                    </button>
                    <button
                      class="btn btn-action btn-icon-split"
                      @click="Verificar"
                      v-if="
                        this.form_datos_permiso.estado == 'VALIDADO' ||
                        this.form_datos_permiso.estado == 'UTILIZADO'
                      "
                    >
                      <span class="icon text-white">
                        <i class="fas fa-check"></i>
                      </span>
                      <span class="text">VERIFICAR</span>
                    </button>

                    <button
                      class="btn btn-danger btn-icon-split"
                      @click="Cancelar"
                      v-if="
                        this.form_datos_permiso.estado == 'VALIDADO' ||
                        this.form_datos_permiso.estado == 'UTILIZADO'
                      "
                    >
                      <span class="icon text-white">
                        <i class="fas fa-times"></i>
                      </span>
                      <span class="text">CANCELAR PERMISO</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="mdlPermisoFoto" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-30 mdlPermisoFoto">
            <div class="content" style="display: block">
              <div class="card">
                <div
                  class="card-header d-flex align-items-center justify-content-between"
                >
                  <strong>{{ title_modal }}</strong>
                  <button
                    type="button"
                    class="btn btn-action"
                    style="border-radius: 50%; float: right !important"
                    @click="CerrarFoto"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-times"></i>
                    </span>
                  </button>
                </div>
                <div class="card-title">DOCUMENTO</div>

                <div class="card-body card-block">
                  <div class="smartcenter form-row justify-content-md-center">
                    <div
                      class="smartcenter form-group justify-content-md-center"
                    >
                      <div
                        class="mb-1"
                        id="vizualizar"
                        style="
                          border: 1px solid #ffff;
                          width: 400px;
                          height: 460px;
                        "
                      ></div>
                    </div>
                  </div>

                  <div class="text-center">
                    <button
                      class="btn btn-cancel btn-icon-split"
                      @click="Descargar"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-download"></i>
                      </span>
                      <span class="text font-size-layout">Descargar</span>
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

export default {
  components: {
    layout,
    headerClose,
  },
  props: { usuarios: Array, agencias: Array },
  data() {
    return {
      windowWidth: window.innerWidth,

      permisos: [],
      title_modal: null,

      agencia_busqueda: 0,

      usuarios_filtrados: [],
      usuario_seleccionado: 0,
      estado_seleccionado: 0,
      mostrar_habilitados: true,

      fecha_desde: null,
      fecha_hasta: null,

      form_datos_permiso: {
        id: null,
        fecha_permiso: null,
        fecha_solicitud: null,
        hora_inicio: null,
        hora_inicio_n: null,
        hora_fin: null,
        tiempo: null,
        detalle: null,
        estado: null,
        dia_semana: null,

        usuario_aprobador: null,
        estado_aprobacion: 0,
        aprobador_cargo: null,
        comentario: null,

        usuario_validador: null,
        goce: null,
        observacion: null,

        hora_retorno: null,

        modo: null,
        forma: null,
      },

      nombre_foto: null,
    };
  },

  computed: {
    dni_session() {
      return this.$inertia.page.props.user_session.usuario_dni;
    },
    usuario_session() {
      return this.$inertia.page.props.user_session.usuario;
    },
  },
  watch: {
    permisos() {
      $("#tblPermisos").DataTable().destroy();
      this.TablaPermisos();
    },
    agencia_busqueda() {
      this.FiltrarUsuarios();
    },

    // hora_retorno() {},
  },
  mounted() {
    this.TablaPermisos();
    this.DiaActual();
  },
  methods: {
    TablaPermisos() {
      this.$nextTick(() => {
        var table = $("#tblPermisos").DataTable({
          scrollY: "275px",
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

        $("#slcUsuarios").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
        });

        $("#slcAgencias").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
        });
        $("#slcEstado").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            var identico = "^" + this.value + "$";
            table
              .column($(this).data("index"))
              .search(identico, true, false)
              .draw();
          }
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

      let desde = ano + "-" + mes + "-" + "01";
      let hasta = ano + "-" + mes + "-" + dia;

      this.fecha_desde = desde;
      this.fecha_hasta = hasta;
    },

    FiltrarUsuarios() {
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
    },
    Buscar() {
      self = this;
      let data = new FormData();
      data.append("fecha_inicio", $("#dtpDesde").val());
      data.append("fecha_fin", $("#dtpHasta").val());

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
          //   this.$inertia.get(
          //     route("gth.asi.listar_solicitud_permisos", {
          //       fecha_desde: self.fecha_desde,
          //       fecha_hasta: self.fecha_hasta,
          //     })
          //   );
          //   return false;
          axios
            .get(
              route("gth.asi.listar_solicitud_permisos", {
                fecha_desde: self.fecha_desde,
                fecha_hasta: self.fecha_hasta,
              })
            )
            .then(function (response) {
              if (response.data.length == 0) {
                self.permisos = [];

                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No se encontraron datos",
                  allowOutsideClick: true,
                });
              } else {
                self.permisos = response.data;
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

    Ver(item) {
      this.title_modal = "VER PERMISO";
      this.form_datos_permiso.id = item.id;
      this.form_datos_permiso.fecha_permiso = item.fecha_permiso;
      this.form_datos_permiso.hora_inicio = item.hora_inicio;
      this.form_datos_permiso.hora_inicio_n = item.hora_inicio_n;
      this.form_datos_permiso.hora_fin = item.hora_fin;

      if (item.hora_retorno == null) {
        this.form_datos_permiso.hora_retorno = item.hora_fin_n;
      } else {
        this.form_datos_permiso.hora_retorno = item.hora_retorno;
      }

      this.form_datos_permiso.tiempo = item.tiempo;
      this.form_datos_permiso.detalle = item.detalle;
      this.form_datos_permiso.documento = item.documento;

      this.form_datos_permiso.usuario_aprobador = item.usuario_aprobador;
      this.form_datos_permiso.estado_aprobacion = item.estado_aprobacion;
      this.form_datos_permiso.aprobador_cargo = item.aprobador_cargo;
      this.form_datos_permiso.comentario = item.comentario;
      this.form_datos_permiso.estado = item.estado;
      this.form_datos_permiso.fecha_solicitud = item.fecha_solicitud;
      this.form_datos_permiso.modo = item.modo;
      this.form_datos_permiso.dia_semana = item.dia_semana;
      this.form_datos_permiso.forma = "web";

      if (item.usuario_validador == null) {
        this.form_datos_permiso.usuario_validador = this.usuario_session;
      } else {
        this.form_datos_permiso.usuario_validador = item.usuario_validador;
      }
      this.form_datos_permiso.goce = item.goce;
      this.form_datos_permiso.observacion = item.observacion;

      $("#mdlPermiso").css("display", "block");
    },

    VerFoto(item) {
      this.title_modal = "FOTO PERMISO";
      let img_prev = $("#vizualizar img");
      img_prev.remove();

      let preview = document.getElementById("vizualizar"),
        image = document.createElement("img");

      image.src =
        "/imagenes_server/gth/solicitud/permisos/" +
        this.form_datos_permiso.documento;

      this.nombre_foto = this.form_datos_permiso.documento;

      image.style.width = "100%";
      image.style.height = "100%";
      image.style.border = "1px solid #ffff";

      preview.innerHTML = "";
      preview.append(image);
      $("#mdlPermisoFoto").css("display", "block");
    },

    Validar() {
      self = this;

      if (
        this.form_datos_permiso.observacion == null ||
        this.form_datos_permiso.observacion == ""
      ) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe hacer una observación de la solicitud.",
        });
        return false;
      } else {
        Swal.fire({
          title: "VALIDAR PERMISO",
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
            data.append("validador_id", this.dni_session);
            data.append("goce", this.form_datos_permiso.goce);
            data.append("observacion", this.form_datos_permiso.observacion);
            data.append("permiso_id", this.form_datos_permiso.id);

            //   this.$inertia

            axios
              .post(route("gth.asi.permisos.validar"), data)
              .then(function (response) {
                $("#mdlPermiso").css("display", "none");
                self.Buscar();

                return Swal.fire({
                  icon: "success",
                  title: "¡EXITO!",
                  text: "Permiso validado",
                  allowOutsideClick: false,
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
    },

    Invalidar() {
      self = this;

      if (
        this.form_datos_permiso.observacion == null ||
        this.form_datos_permiso.observacion == ""
      ) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe hacer una observación de la solicitud.",
        });
        return false;
      } else {
        Swal.fire({
          title: "INVALIDAR PERMISO",
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
            data.append("validador_id", this.dni_session);
            data.append("permiso_id", this.form_datos_permiso.id);
            data.append("observacion", this.form_datos_permiso.observacion);

            //   this.$inertia
            axios
              .post(route("gth.asi.permisos.invalidar"), data)
              .then(function (response) {
                $("#mdlPermiso").css("display", "none");
                self.Buscar();

                return Swal.fire({
                  icon: "success",
                  title: "¡EXITO!",
                  text: "Permiso denegado",
                  allowOutsideClick: false,
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
    },

    Descargar() {
      let self = this;
      let source =
        "/imagenes_server/gth/solicitud/permisos/" + self.nombre_foto;

      axios
        .get(source, { responseType: "blob" })
        .then((response) => {
          const blob = new Blob([response.data], {
            type: response.data.type,
          });
          const link = document.createElement("a");
          link.href = URL.createObjectURL(blob);
          link.download = self.nombre_foto;
          link.click();
          URL.revokeObjectURL(link.href);
        })
        .catch(console.error);
    },
    Verificar() {
      self = this;

      if (
        this.form_datos_permiso.hora_retorno <=
        this.form_datos_permiso.hora_inicio_n
      ) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "La hora retorno no puede ser menor o igual a la hora inicio",
        });
        return false;
      }

      Swal.fire({
        title: "VERIFICAR PERMISO",
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
          data.append("hora_inicio_n", this.form_datos_permiso.hora_inicio_n);
          data.append("permiso_id", this.form_datos_permiso.id);
          data.append("usuario_verificador", this.dni_session);
          data.append("hora_retorno", this.form_datos_permiso.hora_retorno);
          data.append("modo", this.form_datos_permiso.modo);
          data.append("forma", this.form_datos_permiso.forma);
          data.append("fecha_permiso", this.form_datos_permiso.fecha_permiso);
          data.append("dia_semana", this.form_datos_permiso.dia_semana);

          //   this.$inertia.post(
          //     route("sol.permiso.verificar_permiso_retorno"),
          //     data
          //   );
          //   return false;

          axios
            .post(route("sol.permiso.verificar_permiso_retorno"), data)
            .then(function (response) {
              if (response.data.success == true) {
                $("#mdlPermiso").css("display", "none");
                self.Buscar();

                return Swal.fire({
                  icon: "success",
                  title: "¡EXITO!",
                  text: "Permiso verificado",
                  allowOutsideClick: false,
                });
              } else if (response.data.success == "tiempo") {
                Swal.fire({
                  icon: "error",
                  title: "¡Ups!",
                  text: "El permiso supera las 4 horas, debe seleccionar por turno",
                });
                return false;
              } else if (response.data.success == "f_horario") {
                Swal.fire({
                  icon: "error",
                  title: "¡Ups!",
                  text: "El registro se encuentra fuera del horario laboral",
                });
                return false;
              } else if (response.data.success == "horario") {
                Swal.fire({
                  icon: "error",
                  title: "¡Ups!",
                  text: "No se puede hacer el registro con el horario laboral, debe seleccionar el modo por turno",
                });
                return false;
              } else if (response.data.success == "fecha") {
                Swal.fire({
                  icon: "error",
                  title: "¡Ups!",
                  text: "No se puede verificar el permiso antes de la fecha del permiso",
                });
                return false;
              }
            })
            .catch((error) => {
              Swal.showValidationMessage(
                `Ha ocurrido un error, comunicar a TI: ${error}`
              );
            });
        },
      });
    },
    Cancelar() {
      Swal.fire({
        title: "CANCELAR PERMISO",
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

          data.append("permiso_id", this.form_datos_permiso.id);
          data.append("verificador_id", this.dni_session);

          axios
            .post(route("gth.asi.permisos.cancelar"), data)
            .then(function (response) {
              $("#mdlPermiso").css("display", "none");
              self.Buscar();

              return Swal.fire({
                icon: "success",
                title: "¡EXITO!",
                text: "Permiso cancelado",
                allowOutsideClick: false,
              });
            })
            .catch((error) => {
              Swal.showValidationMessage(
                `Ha ocurrido un error, comunicar a TI: ${error}`
              );
            });
        },
      });
    },

    Cerrar() {
      $("#mdlPermiso").css("display", "none");
    },

    CerrarFoto() {
      $("#mdlPermisoFoto").css("display", "none");
    },
    async Exportar() {
      let permiso_filtrados = this.permisos;
      if (this.estado_seleccionado != 0) {
        permiso_filtrados = permiso_filtrados.filter(
          (item) => item.estado == this.estado_seleccionado
        );
      }
      if (this.agencia_busqueda != 0) {
        permiso_filtrados = permiso_filtrados.filter(
          (item) => item.agencia == this.agencia_busqueda
        );
      }

      if (this.usuario_seleccionado != 0) {
        permiso_filtrados = permiso_filtrados.filter(
          (item) => item.usuario == this.usuario_seleccionado
        );
      }

      let data = new FormData();

      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("permiso_filtrados", JSON.stringify(permiso_filtrados));

      //   this.$inertia.post(route("gth.asi.permisos.exportar"), data);
      //   return false;

      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: async () => {
          Swal.showLoading();
          await axios
            .post(route("gth.asi.permisos.exportar"), data)
            .then(function (response) {
              const path_xlsx = response.data.path_xlsx;

              const link = document.createElement("a");
              link.href = origin + path_xlsx;
              link.click();

              return Swal.fire({
                icon: "success",
                title: "¡LISTO!",
                timer: 1200,
                showConfirmButton: false,
              });
            });
        },
      });
    },
    async ExportarGeneral() {
      let data = new FormData();

      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);

      //   this.$inertia.post(route("gth.asi.general.exportar_general"), data);
      //   return false;

      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: async () => {
          Swal.showLoading();
          await axios
            .post(route("gth.asi.general.exportar_general"), data)
            .then(function (response) {
              const path_xlsx = response.data.path_xlsx;

              const link = document.createElement("a");
              link.href = origin + path_xlsx;
              link.click();

              return Swal.fire({
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

<style>
.slot-permisos {
  width: 60% !important;
  margin-left: 20% !important;
}
.mdlPermiso {
  margin-top: 2%;
}
.mdlPermisoFoto {
  margin-top: 1.5%;
}

.clas_G {
  background-color: var(--green) !important;
  color: black !important;
}

.clas_U {
  background-color: var(--gray) !important;
  color: white !important;
}

.clas_V {
  background-color: var(--blue) !important;
  color: white !important;
}

.clas_R {
  background-color: var(--red) !important;
  color: white !important;
}
.time-input {
  display: flex;
  align-items: center; /* Alinea verticalmente en el centro */
}

.time-input input {
  margin: 0 5px; /* Espaciado entre los inputs */
}

.time-input span {
  font-size: 1.5rem;
  padding: 0 5px;
}
.respon {
  margin-top: 36px;
}
@media (max-width: 900px) {
  .slot-permisos {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .mdlPermiso {
    margin-top: 20%;
  }
  .time-input {
    display: flex;
    align-items: center; /* Alinea verticalmente en el centro */
  }

  .time-input input {
    margin: 0 5px; /* Espaciado entre los inputs */
  }

  .time-input span {
    font-size: 1.5rem;
    padding: 0 5px;
  }
}
</style>
