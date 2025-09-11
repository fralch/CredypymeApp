<template>
  <layout ref="layout">
    <div class="slot_body slot-licencias" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose :title="'LICENCIAS'"></headerClose>
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
                  :disabled="licencias.length == 0"
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
                  :disabled="licencias.length == 0"
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
                  :disabled="licencias.length == 0"
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
                  :disabled="licencias.length == 0"
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
                      :disabled="licencias.length == 0"
                      @change="FiltrarUsuarios"
                    />
                    <label class="m-0 ml-1" for="chbHabilitados">Hab.</label>
                  </div>
                </div>
              </div>
            </div>

            <table class="table table-hover" id="tblLicencias" width="100%">
              <thead>
                <tr>
                  <th style="min-width: 70px !important">VER</th>

                  <th style="min-width: 100px !important">FECHA SOL.</th>
                  <th style="min-width: 70px !important">ESTADO</th>
                  <th style="min-width: 90px !important">USUARIO</th>

                  <th style="min-width: 70px !important">AGENCIA</th>
                  <th style="min-width: 70px !important">INICIO</th>
                  <th style="min-width: 70px !important">TERMINA</th>
                  <th style="min-width: 70px !important">FECHA FIN R.</th>
                  <th style="min-width: 70px !important">DÍAS</th>
                  <th style="min-width: 70px !important">APROBADOR</th>
                  <th style="min-width: 70px !important">GOCE</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in licencias" :key="index">
                  <td class="table-bordered" align="center">
                    <button
                      class="btn btn-action"
                      type="button"
                      title="VER LICENCIA"
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
                    {{ item.fecha_inicio }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.fecha_fin }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.fecha_retorno == null ? "-" : item.fecha_retorno }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.dias }}
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
                :disabled="licencias.length == 0"
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
        <div id="mdlLicencia" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-35 mdlLicencia">
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

                <div class="card-title">DATOS LICENCIA</div>
                <div class="card-body card-block">
                  <form>
                    <fieldset class="form-group col-md-12">
                      <legend>
                        <label class="label-title">SOLICITUD</label>
                      </legend>

                      <div class="form-row">
                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >FECHA SOLICITUD</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            name="fecha_solicitud"
                            v-model="form_datos_licencia.fecha_solicitud"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >FECHA INICIO</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            style="max-width: 300px"
                            name="fecha_inicio"
                            v-model="form_datos_licencia.fecha_inicio"
                            disabled
                          />
                        </div>
                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >FECHA FIN</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            style="max-width: 300px"
                            name="fecha_fin"
                            v-model="form_datos_licencia.fecha_fin"
                            disabled
                          />
                        </div>
                        <div
                          class="form-group col-sm-4"
                          v-if="
                            this.form_datos_licencia.estado == 'VALIDADO' ||
                            this.form_datos_licencia.estado == 'UTILIZADO'
                          "
                        >
                          <label class="form-control-label label-title"
                            >FECHA FIN REAL</label
                          >
                          <input
                            type="date"
                            class="form-control center"
                            style="max-width: 300px"
                            name="fecha_retorno"
                            v-model="form_datos_licencia.fecha_retorno"
                          />
                        </div>
                        <div class="form-group col-sm-4">
                          <label class="form-control-label label-title"
                            >DÍAS</label
                          >
                          <input
                            type="text"
                            class="form-control center"
                            style="max-width: 300px"
                            name="dias"
                            v-model="form_datos_licencia.dias"
                            disabled
                          />
                        </div>

                        <div class="form-group col-md-12 mt-1">
                          <label class="form-control-label label-title"
                            >CATEGORÍAS</label
                          >
                          <div class="input_categoria">
                            <select
                              class="form-control center"
                              name="slcCategorias"
                              v-model="form_datos_licencia.categoria_id"
                              style="max-width: 250px; display: inline-block"
                              :disabled="
                                this.form_datos_licencia.estado != 'APROBADO'
                              "
                            >
                              <option
                                v-for="(item, index) in categorias"
                                :key="index"
                                :value="item.id"
                              >
                                {{ item.abreviacion }}
                              </option>
                            </select>

                            <textarea
                              class="form-control mayus text-row"
                              rows="2"
                              v-model="this.categoria_nombre"
                              disabled
                            ></textarea>
                          </div>
                        </div>

                        <div class="form-group col-sm-12">
                          <label class="form-control-label label-title"
                            >POR MOTIVO</label
                          >
                          <textarea
                            class="form-control mayus text-row"
                            rows="2"
                            v-model="form_datos_licencia.detalle"
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
                            v-if="form_datos_licencia.documento != null"
                          >
                            <span class="icon text-white">
                              <i class="fas fa-eye"></i>
                            </span>
                          </button>
                          <label
                            class="form-control-label label-title"
                            v-if="form_datos_licencia.documento == null"
                            >No hay documento</label
                          >
                        </div>
                      </div>
                    </fieldset>

                    <fieldset
                      class="form-group col-md-12"
                      v-if="
                        form_datos_licencia.estado != 'PENDIENTE' &&
                        form_datos_licencia.estado != 'ELIMINADO'
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
                            v-model="form_datos_licencia.usuario_aprobador"
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
                            v-model="form_datos_licencia.aprobador_cargo"
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
                                v-model="form_datos_licencia.estado_aprobacion"
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
                                v-model="form_datos_licencia.estado_aprobacion"
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
                              form_datos_licencia.estado == 'APROBADO' ||
                              form_datos_licencia.estado == 'VALIDADO' ||
                              form_datos_licencia.estado == 'NO VALIDADO' ||
                              form_datos_licencia.estado == 'UTILIZADO' ||
                              form_datos_licencia.estado == 'CANCELADO'
                            "
                            >COMENTARIO APROBACIÓN</label
                          >
                          <label
                            class="form-control-label label-title"
                            v-if="form_datos_licencia.estado == 'DENEGADO'"
                            >COMENTARIO DENEGADO</label
                          >
                          <textarea
                            class="form-control mayus text-row"
                            rows="2"
                            v-model="form_datos_licencia.comentario"
                            disabled
                          ></textarea>
                        </div>
                      </div>
                    </fieldset>
                    <fieldset
                      class="form-group col-md-12"
                      v-if="
                        form_datos_licencia.estado == 'APROBADO' ||
                        form_datos_licencia.estado == 'VALIDADO' ||
                        form_datos_licencia.estado == 'NO VALIDADO' ||
                        form_datos_licencia.estado == 'UTILIZADO' ||
                        form_datos_licencia.estado == 'CANCELADO'
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
                            v-model="form_datos_licencia.usuario_validador"
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
                                v-model="form_datos_licencia.goce"
                                :disabled="
                                  this.form_datos_licencia.estado != 'APROBADO'
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
                                v-model="form_datos_licencia.goce"
                                :disabled="
                                  this.form_datos_licencia.estado != 'APROBADO'
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
                            v-model="form_datos_licencia.observacion"
                            :disabled="
                              this.form_datos_licencia.estado != 'APROBADO'
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
                      @click="Validar"
                      v-if="this.form_datos_licencia.estado == 'APROBADO'"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-check"></i>
                      </span>
                      <span class="text">VALIDAR</span>
                    </button>
                    <button
                      class="btn btn-cancel btn-icon-split"
                      @click="Invalidar"
                      v-if="this.form_datos_licencia.estado == 'APROBADO'"
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
                        this.form_datos_licencia.estado == 'VALIDADO' ||
                        this.form_datos_licencia.estado == 'UTILIZADO'
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
                        this.form_datos_licencia.estado == 'VALIDADO' ||
                        this.form_datos_licencia.estado == 'UTILIZADO'
                      "
                    >
                      <span class="icon text-white">
                        <i class="fas fa-times"></i>
                      </span>
                      <span class="text">CANCELAR LICENCIA</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="mdlLicenciaFoto" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-30 mdlLicenciaFoto">
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

                  <div class="text-center mb-1">
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
  props: {
    usuarios: Array,
    agencias: Array,
    categorias: Array,
    feriados: Array,
  },
  data() {
    return {
      windowWidth: window.innerWidth,

      licencias: [],
      title_modal: null,

      agencia_busqueda: 0,

      usuarios_filtrados: [],
      usuario_seleccionado: 0,
      estado_seleccionado: 0,
      mostrar_habilitados: true,

      fecha_desde: null,
      fecha_hasta: null,

      form_datos_licencia: {
        id: null,
        fecha_solicitud: null,
        fecha_inicio: null,
        fecha_fin: null,
        fecha_retorno: null,
        dias: null,
        categoria_id: null,
        detalle: null,
        estado: null,

        usuario_aprobador: null,
        estado_aprobacion: 0,
        aprobador_cargo: null,
        comentario: null,

        usuario_validador: null,
        goce: null,
        observacion: null,
      },
      categoria_nombre: null,

      nombre_foto: null,
    };
  },

  computed: {
    categoria() {
      return this.form_datos_licencia.categoria_id;
    },

    fecha_retorno() {
      return this.form_datos_licencia.fecha_retorno;
    },

    dni_session() {
      return this.$inertia.page.props.user_session.usuario_dni;
    },
    usuario_session() {
      return this.$inertia.page.props.user_session.usuario;
    },
  },
  watch: {
    licencias() {
      $("#tblLicencias").DataTable().destroy();
      this.TablaLicencias();
    },
    agencia_busqueda() {
      this.FiltrarUsuarios();
    },

    fecha_retorno() {
      let categoria = this.categorias.filter(
        (item) => item.id == this.form_datos_licencia.categoria_id
      );
      this.categoria_nombre = categoria[0].nombre;

      let inicio = new Date(this.form_datos_licencia.fecha_inicio);
      let fin = new Date(this.form_datos_licencia.fecha_retorno);

      let fecha_inicio_real = moment(inicio);
      let fecha_fin_real = moment(fin);

      fecha_inicio_real.add(1, "days");
      fecha_fin_real.add(1, "days");

      let fecha_inicio_t = fecha_inicio_real.format("YYYY-MM-DD");
      let fecha_fin_t = fecha_fin_real.format("YYYY-MM-DD");

      let dias_totales = 0;
      let dias_domingos = 0;
      let fechaferiados = 0;

      let dias_agregado_feriados = 0;
      let dias_agregado_domingo = 0;

      let random = 0;
      let num = 0;

      this.feriados.filter(function (fecha) {
        if (fecha_inicio_t < fecha && fecha <= fecha_fin_t) {
          fechaferiados += 1;
        }
      });

      for (
        let fecha = new Date(inicio);
        fecha <= fin;
        fecha.setDate(fecha.getDate() + 1)
      ) {
        dias_totales = dias_totales + 1;

        if (fecha.getUTCDay() == 0) {
          dias_domingos = dias_domingos + 1;
        }
      }

      let fecha_siguiente = fecha_fin_real;

      while (random == 0) {
        fecha_siguiente.add(1, "days");
        let fecha_modificada = fecha_siguiente.format("YYYY-MM-DD");
        // console.log(fecha_modificada);

        num = 0;
        this.feriados.forEach(function (fechas) {
          // console.log(fechas, fecha_modificada);
          if (fechas == fecha_modificada) {
            num += 1;
          }
        });
        if (num == 1) {
          random = 0;
        } else {
          random = 1;
        }
        if (random == 0) {
          dias_agregado_feriados += 1;
        }

        if (fecha_siguiente.day() == 0) {
          dias_agregado_domingo += 1;
          random = 0;
        }

        // console.log(fecha_siguiente.getUTCDay(), num, random);
      }

      if (categoria[0].abreviacion == "VACACIONES") {
        // console.log(
        //   dias_totales,
        //   dias_agregado_domingo,
        //   dias_agregado_feriados
        // );
        this.form_datos_licencia.dias =
          dias_totales + dias_agregado_domingo + dias_agregado_feriados;
      } else {
        // console.log(dias_totales, dias_domingos, fechaferiados);
        this.form_datos_licencia.dias =
          dias_totales - dias_domingos - fechaferiados;
      }
    },

    categoria() {
      let categoria = this.categorias.filter(
        (item) => item.id == this.form_datos_licencia.categoria_id
      );
      this.categoria_nombre = categoria[0].nombre;

      let inicio = new Date(this.form_datos_licencia.fecha_inicio);
      let fin = new Date(this.form_datos_licencia.fecha_fin);

      let fecha_inicio_real = moment(inicio);
      let fecha_fin_real = moment(fin);

      fecha_inicio_real.add(1, "days");
      fecha_fin_real.add(1, "days");

      let fecha_inicio_t = fecha_inicio_real.format("YYYY-MM-DD");
      let fecha_fin_t = fecha_fin_real.format("YYYY-MM-DD");

      let dias_totales = 0;
      let dias_domingos = 0;
      let fechaferiados = 0;

      let dias_agregado_feriados = 0;
      let dias_agregado_domingo = 0;

      let random = 0;
      let num = 0;

      this.feriados.filter(function (fecha) {
        if (fecha_inicio_t < fecha && fecha <= fecha_fin_t) {
          fechaferiados += 1;
        }
      });

      for (
        let fecha = new Date(inicio);
        fecha <= fin;
        fecha.setDate(fecha.getDate() + 1)
      ) {
        dias_totales = dias_totales + 1;

        if (fecha.getUTCDay() == 0) {
          dias_domingos = dias_domingos + 1;
        }
      }

      let fecha_siguiente = fecha_fin_real;

      while (random == 0) {
        fecha_siguiente.add(1, "days");
        let fecha_modificada = fecha_siguiente.format("YYYY-MM-DD");

        num = 0;
        this.feriados.forEach(function (fechas) {
          if (fechas == fecha_modificada) {
            num += 1;
          }
        });
        if (num == 1) {
          random = 0;
        } else {
          random = 1;
        }
        if (random == 0) {
          dias_agregado_feriados += 1;
        }

        if (fecha_siguiente.day() == 0) {
          dias_agregado_domingo += 1;
          random = 0;
        }
      }

      if (categoria[0].abreviacion == "VACACIONES") {
        this.form_datos_licencia.dias =
          dias_totales + dias_agregado_domingo + dias_agregado_feriados;
      } else {
        this.form_datos_licencia.dias =
          dias_totales - dias_domingos - fechaferiados;
      }
    },
  },
  mounted() {
    this.TablaLicencias();
    this.DiaActual();
  },
  methods: {
    TablaLicencias() {
      this.$nextTick(() => {
        var table = $("#tblLicencias").DataTable({
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
          //     route("gth.asi.listar_solicitud_licencias", {
          //       fecha_desde: self.fecha_desde,
          //       fecha_hasta: self.fecha_hasta,
          //     })
          //   );
          //   return false;
          axios
            .get(
              route("gth.asi.listar_solicitud_licencias", {
                fecha_desde: self.fecha_desde,
                fecha_hasta: self.fecha_hasta,
              })
            )
            .then(function (response) {
              if (response.data.length == 0) {
                self.licencias = [];

                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No se encontraron datos",
                  allowOutsideClick: true,
                });
              } else {
                self.licencias = response.data;
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
      this.title_modal = "VER LICENCIA";
      this.form_datos_licencia.categoria_id = item.categoria_id;
      this.form_datos_licencia.id = item.id;
      this.form_datos_licencia.fecha_solicitud = item.fecha_solicitud;
      this.form_datos_licencia.fecha_inicio = item.fecha_inicio;
      this.form_datos_licencia.fecha_fin = item.fecha_fin;
      this.form_datos_licencia.estado = item.estado;

      if (this.form_datos_licencia.estado == "VALIDADO") {
        this.form_datos_licencia.fecha_retorno = item.fecha_fin;
      } else if (this.form_datos_licencia.estado == "UTILIZADO") {
        this.form_datos_licencia.fecha_retorno = item.fecha_retorno;
      }

      this.form_datos_licencia.dias = item.dias;

      this.form_datos_licencia.detalle = item.detalle;
      this.form_datos_licencia.documento = item.documento;

      this.form_datos_licencia.usuario_aprobador = item.usuario_aprobador;
      this.form_datos_licencia.estado_aprobacion = item.estado_aprobacion;
      this.form_datos_licencia.aprobador_cargo = item.aprobador_cargo;
      this.form_datos_licencia.comentario = item.comentario;

      if (item.usuario_validador == null) {
        this.form_datos_licencia.usuario_validador = this.usuario_session;
      } else {
        this.form_datos_licencia.usuario_validador = item.usuario_validador;
      }

      this.form_datos_licencia.goce = item.goce;
      this.form_datos_licencia.observacion = item.observacion;

      $("#mdlLicencia").css("display", "block");
    },

    VerFoto(item) {
      this.title_modal = "FOTO LICENCIA";
      let img_prev = $("#vizualizar img");
      img_prev.remove();

      let preview = document.getElementById("vizualizar"),
        image = document.createElement("img");

      image.src =
        "/imagenes_server/gth/solicitud/licencias/" +
        this.form_datos_licencia.documento;

      this.nombre_foto = this.form_datos_licencia.documento;

      image.style.width = "100%";
      image.style.height = "100%";
      image.style.border = "1px solid #ffff";

      preview.innerHTML = "";
      preview.append(image);
      $("#mdlLicenciaFoto").css("display", "block");
    },

    Validar() {
      self = this;

      if (
        this.form_datos_licencia.observacion == null ||
        this.form_datos_licencia.observacion == ""
      ) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe hacer una observación de la solicitud.",
        });
        return false;
      } else {
        Swal.fire({
          title: "VALIDAR LICENCIA",
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
            data.append("dias", this.form_datos_licencia.dias);
            data.append("categoria_id", this.categoria);
            data.append("validador_id", this.dni_session);
            data.append("goce", this.form_datos_licencia.goce);
            data.append("observacion", this.form_datos_licencia.observacion);
            data.append("licencia_id", this.form_datos_licencia.id);

            axios
              .post(route("gth.asi.licencias.validar"), data)
              .then(function (response) {
                $("#mdlLicencia").css("display", "none");
                self.Buscar();

                return Swal.fire({
                  icon: "success",
                  title: "¡EXITO!",
                  text: "Licencia validada",
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
        this.form_datos_licencia.observacion == null ||
        this.form_datos_licencia.observacion == ""
      ) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe hacer una observación de la solicitud.",
        });
        return false;
      } else {
        Swal.fire({
          title: "INVALIDAR LICENCIA",
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
            data.append("licencia_id", this.form_datos_licencia.id);
            data.append("validador_id", this.dni_session);
            data.append("observacion", this.form_datos_licencia.observacion);

            // this.$inertia
            axios
              .post(route("gth.asi.licencias.invalidar"), data)
              .then(function (response) {
                $("#mdlLicencia").css("display", "none");
                self.Buscar();

                return Swal.fire({
                  icon: "success",
                  title: "¡EXITO!",
                  text: "Licencia invalidada",
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
        "/imagenes_server/gth/solicitud/licencias/" + self.nombre_foto;

      axios
        .get(source, { responseType: "blob" })
        .then((response) => {
          const blob = new Blob([response.data], { type: response.data.type });
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
        this.form_datos_licencia.fecha_retorno <
        this.form_datos_licencia.fecha_inicio
      ) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "La fecha retorno no puede ser menor a la fecha inicio",
        });
        return false;
      }

      Swal.fire({
        title: "VERIFICAR LICENCIA",
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

          data.append("licencia_id", this.form_datos_licencia.id);
          data.append("dias", this.form_datos_licencia.dias);
          data.append("fecha_retorno", this.form_datos_licencia.fecha_retorno);
          data.append("verificador_id", this.dni_session);
          data.append("fecha_inicio", this.form_datos_licencia.fecha_inicio);

          //   this.$inertia.post(route("gth.asi.licencias.verificar"), data);
          //   return false;

          axios
            .post(route("gth.asi.licencias.verificar"), data)
            .then(function (response) {
              if (response.data.success == true) {
                $("#mdlLicencia").css("display", "none");
                self.Buscar();

                return Swal.fire({
                  icon: "success",
                  title: "¡EXITO!",
                  text: "Licencia verificada",
                  allowOutsideClick: false,
                });
              } else if (response.data.success == "fecha") {
                Swal.fire({
                  icon: "error",
                  title: "¡Ups!",
                  text: "No se puede verificar la licencia antes de la fecha de inicio de la licencia",
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
        title: "CANCELAR LICENCIA",
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

          data.append("licencia_id", this.form_datos_licencia.id);
          data.append("verificador_id", this.dni_session);

          axios
            .post(route("gth.asi.licencias.cancelar"), data)
            .then(function (response) {
              $("#mdlLicencia").css("display", "none");
              self.Buscar();

              return Swal.fire({
                icon: "success",
                title: "¡EXITO!",
                text: "Licencia cancelada",
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
      $("#mdlLicencia").css("display", "none");
    },

    CerrarFoto() {
      $("#mdlLicenciaFoto").css("display", "none");
    },

    async Exportar() {
      let licencia_filtradas = this.licencias;
      if (this.estado_seleccionado != 0) {
        licencia_filtradas = licencia_filtradas.filter(
          (item) => item.estado == this.estado_seleccionado
        );
      }
      if (this.agencia_busqueda != 0) {
        licencia_filtradas = licencia_filtradas.filter(
          (item) => item.agencia == this.agencia_busqueda
        );
      }

      if (this.usuario_seleccionado != 0) {
        licencia_filtradas = licencia_filtradas.filter(
          (item) => item.usuario == this.usuario_seleccionado
        );
      }

      let data = new FormData();

      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("licencia_filtradas", JSON.stringify(licencia_filtradas));

      //   this.$inertia.post(route("gth.asi.licencias.exportar"), data);
      //   return false;

      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: async () => {
          Swal.showLoading();
          await axios
            .post(route("gth.asi.licencias.exportar"), data)
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

<style >
.slot-licencias {
  width: 60% !important;
  margin-left: 20% !important;
}
.mdlLicencia {
  margin-top: 2%;
}
.mdlLicenciaFoto {
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

.input_categoria {
  text-align: center;
}
.respon {
  margin-top: 36px;
}

@media (max-width: 900px) {
  .slot-licencias {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .mdlLicencia {
    margin-top: 20%;
  }
}
</style>

