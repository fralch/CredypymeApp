<template>
  <layout ref="layout">
    <div slot="component-view" class="slot_body">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose
            :title="
              'EDITAR PERMISOS DE USUARIO - ' +
              (modo == 'completo'
                ? 'GENERAL'
                : modo == 'credito'
                ? 'CRÉDITO'
                : modo == 'logistica'
                ? 'LOGÍSTICA'
                : 'GTH')
            "
          ></headerClose>
          <div class="card-title">USUARIO</div>
          <div class="card-body card-block">
            <div class="form-row">
              <div class="form-group col-xs-4">
                <label for="lblUsuario" class="form-control-label label-title"
                  >USUARIO</label
                >
                <input
                  type="text"
                  id="inpUsuario"
                  name="nombres"
                  class="form-control center"
                  style="width: 250px"
                  v-for="datosUsuario in datosUsuarios"
                  :key="datosUsuario.dni"
                  :value="
                    datosUsuario.nombres +
                    ' ' +
                    datosUsuario.apellido_paterno +
                    ' ' +
                    datosUsuario.apellido_materno
                  "
                  disabled
                />
                <input type="hidden" name="dni" class="form-control" />
              </div>
              <div class="form-group col-xs-4">
                <label for="lblAgencia" class="form-control-label label-title"
                  >AGENCIA</label
                >
                <input
                  type="text"
                  id="inpAgencia"
                  name="agencias"
                  class="form-control center"
                  style="width: 250px"
                  v-for="datosUsuario in datosUsuarios"
                  :key="datosUsuario.dni"
                  :value="datosUsuario.nombre"
                  disabled
                />
              </div>
              <div class="form-group col-xs-4">
                <label for="lblCargo" class="form-control-label label-title"
                  >CARGO</label
                >
                <input
                  type="text"
                  id="inpCargo"
                  name="cargos"
                  class="form-control center"
                  style="width: 250px"
                  v-for="datosUsuario in datosUsuarios"
                  :key="datosUsuario.dni"
                  :value="datosUsuario.cargo"
                  disabled
                />
              </div>
            </div>
          </div>
          <div class="card-title">PERMISOS DISPONIBLES</div>
          <div class="card-body card-block">
            <div class="form-row">
              <div class="form-group col-xs-6">
                <div class="align-middle">
                  <div class="radio">
                    <label
                      class="align-middle"
                      style="
                        font-size: 1em;
                        margin-bottom: 0 !important;
                        height: 25px !important;
                      "
                      ><input
                        type="radio"
                        id="uno"
                        value="uno"
                        name="chbCopiar"
                        v-model="datosOcultos" /><span
                        class="cr"
                        style="
                          border: 1px solid #212529;
                          margin-left: 0 !important;
                        "
                        ><i class="cr-icon fa fa-check"></i></span
                    ></label>
                    <label class="form-control-label" for="uno"
                      >Copiar permisos</label
                    >
                  </div>
                </div>
              </div>
              <div class="form-group col-xs-6">
                <div class="align-middle">
                  <div class="radio">
                    <label
                      class="align-middle"
                      style="
                        font-size: 1em;
                        margin-bottom: 0 !important;
                        height: 25px !important;
                      "
                      ><input
                        type="radio"
                        id="dos"
                        value="dos"
                        name="chbCopiarCargos"
                        v-model="datosOcultos" /><span
                        class="cr"
                        style="
                          border: 1px solid #212529;
                          margin-left: 0 !important;
                        "
                        ><i class="cr-icon fa fa-check"></i></span
                    ></label>
                    <label class="form-control-label" for="dos"
                      >Asignar por cargo</label
                    >
                  </div>
                </div>
              </div>
            </div>
            <div v-if="datosOcultos == 'uno'">
              <label for="lblAgencia" class="form-control-label"
                >De usuario</label
              >
              <label class="form-control-label"
                >------------------------------------------------------------------------</label
              >
              <div class="form-row">
                <div class="form-group col-xs-3">
                  <select
                    type="text"
                    class="form-control center"
                    style="width: 200px"
                    id="slcAgencia"
                    name="agen"
                    @change="FiltrarUsuarios"
                  >
                    <option value="0">TODAS</option>
                    <option
                      v-for="agencia in agencias"
                      :key="agencia.id_agencia"
                      :value="agencia.id_agencia"
                    >
                      {{ agencia.nombre }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-xs-3">
                  <select
                    class="form-control center"
                    style="width: 200px"
                    id="slcUsuarios"
                    v-model="usuarioSeleccionado"
                    @change="obtenerCargo"
                  >
                    <option value="0" selected>TODOS</option>
                    <option
                      v-for="usuario in usuarios_filtrados"
                      v-bind:key="usuario.dni"
                      :value="usuario.dni"
                    >
                      {{ usuario.usuario }}
                    </option>
                  </select>
                  <div
                    v-if="submitedC && this.usuarioSeleccionado == 0"
                    style="color: red; font-size: 12px"
                  >
                    *Campo obligatorio
                  </div>
                </div>
                <div class="form-group col-xs-3">
                  <div class="text-center">
                    <button
                      class="btn btn-action btn-icon-split mb-1"
                      @click="CopiarPermisos()"
                    >
                      <span class="icon text-white-50">
                        <i class="far fa-copy" style="color: white"></i>
                      </span>
                      <span class="text font-size-layout">Copiar Permisos</span>
                    </button>
                  </div>
                </div>
              </div>
              <div class="form-row" style="margin-bottom: -20px">
                <div class="form-group col-xs-3">
                  <label for="lblAgencia" class="form-control-label label-title"
                    >CARGO:</label
                  >
                </div>
                <div class="form-group col-xs-3">
                  <label
                    for="lblAgencia"
                    class="form-control-label label-title"
                    >{{ cargo }}</label
                  >
                </div>
              </div>
              <label class="form-control-label"
                >¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯</label
              >
            </div>
            <div v-if="datosOcultos == 'dos'">
              <label for="lblAgencia" class="form-control-label">CARGO</label>
              <label class="form-control-label"
                >------------------------------------------------------------------------</label
              >
              <div class="form-row">
                <div class="form-group col-xs-3">
                  <select
                    type="text"
                    class="form-control center"
                    style="width: 300px"
                    id="slcCargo"
                    name="cargo"
                    v-model="cargoSeleccionado"
                  >
                    <option value="0" selected disabled>TODAS</option>
                    <option
                      v-for="(cargo, index) in cargos"
                      :key="index"
                      :value="cargo.id"
                    >
                      {{ cargo.cargo }}
                    </option>
                  </select>
                  <div
                    v-if="submitedCargos && this.cargoSeleccionado == 0"
                    style="color: red; font-size: 12px"
                  >
                    *Campo obligatorio
                  </div>
                </div>

                <div class="form-group col-xs-3">
                  <div class="text-center">
                    <button
                      class="btn btn-action btn-icon-split mb-1"
                      @click="CopiarPermisoCargo()"
                    >
                      <span class="icon text-white-50">
                        <i class="far fa-copy" style="color: white"></i>
                      </span>
                      <span class="text font-size-layout"
                        >Asignar Permisos del Cargo</span
                      >
                    </button>
                  </div>
                </div>
              </div>
              <label class="form-control-label"
                >¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯</label
              >
            </div>
            <!-- Fin del div -->

            <div class="form-row">
              <div class="form-group col-ms-6">
                <label
                  for="slcAreadisponibles"
                  class="form-control-label label-title"
                  >Por área</label
                >
                <select
                  class="form-control center"
                  style="width: 250px"
                  id="slcAreadisponibles"
                  @change="FiltrarAreasDisponibles"
                >
                  <option value="0">TODAS</option>
                  <option
                    v-for="(areaDispo, index) in areasDispos"
                    :key="index"
                    :value="areaDispo.area"
                  >
                    {{ areaDispo.area }}
                  </option>
                </select>
              </div>
            </div>
            <div class="input-group row col-md-10 col-9" style="float: left">
              <div class="input-group-prepend">
                <span class="input-group-text"
                  ><i class="fas fa-search"></i
                ></span>
              </div>
              <input
                class="form-control mayus"
                type="text"
                id="inpBuscar_ud"
                autocomplete="off"
                spellcheck="false"
                @focus="hidenav()"
                @blur="shownav()"
              />
            </div>
            <!-- <span>Selected Ids: {{ frmAsignarPermisos.permisoSeleccionados }}</span> -->
            <div id="tabla_permisos_disponibles">
              <table class="table table-hover" id="tblAñadirPermiso">
                <thead>
                  <tr>
                    <th style="width: 75px !important">
                      TODO
                      <div class="align-middle">
                        <div class="checkbox">
                          <label
                            style="
                              font-size: 1.5em;
                              margin-bottom: 0 !important;
                              height: 5px !important;
                            "
                          >
                            <input
                              type="checkbox"
                              name="chbCheck"
                              @change="selectAll"
                              v-model="frmAsignarPermisos.allSelected"
                            />
                            <span class="cr"
                              ><i
                                class="cr-icon fa fa-check"
                                style="color: white"
                                important
                              ></i
                            ></span>
                          </label>
                        </div>
                      </div>
                    </th>
                    <th>ÁREA</th>
                    <th>MÓDULO</th>
                    <th v-for="agencia in agencias" :key="agencia.id_agencia">
                      AG_{{ agencia.nombre }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(
                      permisoDisponible, index
                    ) in permisos_disp_filtrados"
                    v-bind:key="index"
                  >
                    <td class="table-bordered" align="center">
                      <div class="align-middle">
                        <div class="checkbox">
                          <label
                            style="
                              font-size: 2em;
                              margin-bottom: 0 !important;
                              height: 28.6px !important;
                            "
                            ><input
                              type="checkbox"
                              class="form-control"
                              name="chbDisponible"
                              :value="permisoDisponible.id"
                              :id="'chb' + permisoDisponible.id"
                              v-model="frmAsignarPermisos.permisoSeleccionados"
                              @change="
                                eliminarPermisosAgencias(permisoDisponible.id)
                              " />
                            <span class="cr"
                              ><i class="cr-icon fa fa-check"></i></span
                          ></label>
                        </div>
                      </div>
                    </td>
                    <td class="table-bordered" align="center">
                      {{ permisoDisponible.area }}
                    </td>
                    <td class="table-bordered" align="left">
                      {{ permisoDisponible.modulo }}
                    </td>
                    <td
                      v-for="agencia in agencias"
                      :key="agencia.id_agencia"
                      class="table-bordered"
                      align="center"
                    >
                      <div class="align-middle">
                        <div class="checkbox">
                          <label
                            style="
                              font-size: 2em;
                              margin-bottom: 0 !important;
                              height: 28.6px !important;
                            "
                            ><input
                              :disabled="
                                !frmAsignarPermisos.permisoSeleccionados.includes(
                                  permisoDisponible.id
                                )
                              "
                              type="checkbox"
                              class="form-control"
                              v-model="frmAsignarPermisos.permisosAgencia"
                              :value="{
                                permiso_id: permisoDisponible.id,
                                agencia_id: agencia.id_agencia,
                              }" />
                            <span class="cr"
                              ><i class="cr-icon fa fa-check"></i></span
                          ></label>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <div
                  v-if="
                    submited &&
                    frmAsignarPermisos.permisoSeleccionados.length == 0
                  "
                  style="color: red; font-size: 12px"
                >
                  *Seleccionar un permiso
                </div>
              </table>

              <div class="text-center">
                <button
                  class="btn btn-action btn-icon-split mb-1"
                  @click="AgregarPermisos()"
                >
                  <span class="icon text-white-50">
                    <i class="fas fa-plus" style="color: white"></i>
                  </span>
                  <span class="text font-size-layout">Agregar Permisos</span>
                </button>
              </div>
            </div>
          </div>
          <div class="card-title">PERMISOS ACTUALES</div>
          <div class="card-body card-block">
            <br />
            <div class="text-left">
              <button
                class="btn btn-cancel btn-icon-split mb-1"
                @click="EliminarTodosPermisos()"
              >
                <span class="icon text-white-50">
                  <i class="far fa-trash-alt" style="color: white"></i>
                </span>
                <span class="text font-size-layout"
                  >Eliminar Todos los Permisos</span
                >
              </button>
            </div>
            <br />
            <div class="form-row">
              <div class="form-group col-ms-6">
                <label
                  for="slcAreaActual"
                  class="form-control-label label-title"
                  >Por área</label
                >
                <select
                  type="text"
                  class="form-control center"
                  style="width: 250px"
                  id="slcAreaActual"
                  name="areas"
                  data-index="1"
                  @change="FiltrarAreasActuales"
                >
                  <option value="0">TODAS</option>
                  <option
                    v-for="(areaActual, index) in areasActuales"
                    :key="index"
                  >
                    {{ areaActual.area }}
                  </option>
                </select>
              </div>
            </div>
            <div class="input-group row col-md-10 col-9" style="float: left">
              <div class="input-group-prepend">
                <span class="input-group-text"
                  ><i class="fas fa-search"></i
                ></span>
              </div>
              <input
                class="form-control mayus"
                type="text"
                id="inpBuscar_ua"
                autocomplete="off"
                spellcheck="false"
                @focus="hidenav()"
                @blur="shownav()"
              />
            </div>
            <div id="tabla_permisos_actuales">
              <table class="table table-hover" id="tblQuitarPermiso">
                <thead>
                  <tr>
                    <th style="width: 75px !important">ACCIONES</th>
                    <th>ÁREA</th>
                    <th>MÓDULO</th>
                    <th v-for="agencia in agencias" :key="agencia.id_agencia">
                      AG_{{ agencia.nombre }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="permisoActual in permisos_actuales_filtrados"
                    v-bind:key="permisoActual.dni"
                  >
                    <td class="table-bordered" align="center">
                      <button
                        class="btn btn-danger btn-icon-split"
                        @click="QuitarPermisos(permisoActual.id)"
                      >
                        <span class="icon text-white-50">
                          <i class="fas fa-times" style="color: white"></i>
                        </span>
                      </button>
                      <button
                        class="btn btn-cancel btn-icon-split"
                        v-show="
                          !permisosEditar.includes(permisoActual.permiso_id)
                        "
                        @click="permisosEditar.push(permisoActual.permiso_id)"
                      >
                        <span class="icon text-white-50">
                          <i class="fas fa-edit" style="color: white"></i>
                        </span>
                      </button>
                      <button
                        v-show="
                          permisosEditar.includes(permisoActual.permiso_id)
                        "
                        class="btn btn-action btn-icon-split"
                        @click="
                          EditarPermisos(
                            permisoActual.permiso_id,
                            permisoActual.id
                          )
                        "
                      >
                        <span class="icon text-white-50">
                          <i class="fas fa-save" style="color: white"></i>
                        </span>
                      </button>
                    </td>
                    <td class="table-bordered" align="center">
                      {{ permisoActual.area }}
                    </td>
                    <td class="table-bordered" align="left">
                      {{ permisoActual.modulo }}
                    </td>
                    <td
                      v-for="agencia in agencias"
                      :key="agencia.id_agencia"
                      class="table-bordered"
                      align="center"
                    >
                      <div class="align-middle">
                        <div class="checkbox">
                          <label
                            style="
                              font-size: 2em;
                              margin-bottom: 0 !important;
                              height: 28.6px !important;
                            "
                            ><input
                              type="checkbox"
                              class="form-control"
                              :disabled="
                                !permisosEditar.includes(
                                  permisoActual.permiso_id
                                )
                              "
                              :value="{
                                permiso_id: permisoActual.permiso_id,
                                agencia_id: agencia.id_agencia,
                              }"
                              v-model="acceso_agencias_editar" />
                            <span class="cr"
                              ><i class="cr-icon fa fa-check"></i></span
                          ></label>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div id="tabla"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import layout from "@/Pages/General/Components/layout_general.vue";
import headerClose from "@/Pages/General/Components/header_close.vue";
const diferentThanZero = (value) => value != 0;
export default {
  components: {
    layout,
    headerClose,
  },
  props: {
    modo: String,
    datosUsuarios: Array,
    permisosActuales: Array,
    permisosDisponibles: Array,
    areasDispos: Array,
    areasActuales: Array,
    agencias: Array,
    usuarios: Array,
    cargos: Array,
    acceso_agencias: Array,
  },
  data() {
    return {
      submitedC: false,
      submited: false,
      submitedCargos: false,
      permisos_disp_filtrados: this.permisosDisponibles,
      permisos_actuales_filtrados: this.permisosActuales,
      frmAsignarPermisos: {
        dni: this.datosUsuarios[0].dni,
        permisoSeleccionados: [],
        permisosAgencia: [],
        allSelected: false,
      },
      usuarioSeleccionado: 0,
      cargoSeleccionado: 0,
      datosOcultos: false,
      usuarios_filtrados: this.usuarios,
      cargo: null,
      acceso_agencias_editar: this.acceso_agencias,
      permisosEditar: [],
    };
  },

  validations: {
    usuarioSeleccionado: { noZero: diferentThanZero },
  },

  mounted() {
    self = this;
    this.TablaAñadirPermisos();

    this.TablaQuitarPermiso();
  },

  watch: {
    permisos_disp_filtrados() {
      $("#tblAñadirPermiso").DataTable().destroy();
      this.TablaAñadirPermisos();
    },
    permisos_actuales_filtrados() {
      $("#tblQuitarPermiso").DataTable().destroy();
      this.TablaQuitarPermiso();
    },
  },

  methods: {
    eliminarPermisosAgencias(permiso_id) {
      _.remove(this.frmAsignarPermisos.permisosAgencia, function (permiso) {
        return permiso["permiso_id"] == permiso_id;
      });
    },
    selectAll() {
      self = this;

      if (this.frmAsignarPermisos.allSelected == false) {
        self.frmAsignarPermisos.permisoSeleccionados = [];
      } else if (this.frmAsignarPermisos.allSelected == true) {
        self.frmAsignarPermisos.permisoSeleccionados = [];
        this.permisos_disp_filtrados.forEach(function callback(
          currentValue //se utiliza como un iterador = item -> item in items
        ) {
          self.frmAsignarPermisos.permisoSeleccionados.push(currentValue.id);
        });
      }
    },
    FiltrarAreasDisponibles() {
      let slcAreas_value = $("#slcAreadisponibles").val();

      if (slcAreas_value == 0) {
        this.permisos_disp_filtrados = this.permisosDisponibles;
      } else {
        this.permisos_disp_filtrados = this.permisosDisponibles.filter(
          (item) => item.area == slcAreas_value
        );
      }
    },
    FiltrarAreasActuales() {
      this.permisos_actuales_filtrados = this.permisosActuales;
    },

    FiltrarAccesoAgencias() {
      this.acceso_agencias_editar = this.acceso_agencias;
    },
    TablaAñadirPermisos() {
      this.$nextTick(() => {
        var table = $("#tblAñadirPermiso").DataTable({
          scrollY: "350px",
          scrollX: true,
          fixedColumns: {
            leftColumns: 0,
          },
          scrollCollapse: true,
          paging: false,
          order: [[1, "asc"]],
          fixedHeader: true,
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
          dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
          buttons: [
            {
              extend: "excelHtml5",
              text: '<i class="fas fa-file-excel"></i> ',
              titleAttr: "Exportar a Excel",
              className: "btn btn-action",
            },
          ],
        });

        $("#inpBuscar_ud").keyup(function () {
          table.search(this.value).draw();
        });
      });
    },

    TablaQuitarPermiso() {
      this.$nextTick(() => {
        var table = $("#tblQuitarPermiso").DataTable({
          scrollY: "350px",
          scrollX: true,
          fixedColumns: {
            leftColumns: 0,
          },
          scrollCollapse: true,
          paging: false,
          order: [[1, "asc"]],
          fixedHeader: true,
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
          dom: '<"text-right "Bf>rt<"row"<"col-sm-12 col-md-5 "i><"col-sm-12 col-md-7 "p>><"clear">',
          buttons: [
            {
              extend: "excelHtml5",
              text: '<i class="fas fa-file-excel"></i> ',
              titleAttr: "Exportar a Excel",
              className: "btn btn-action",
            },
          ],
        });
        $("#slcAreaActual").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
        });
        $("#inpBuscar_ua").keyup(function () {
          table.search(this.value).draw();
        });
      });
    },
    hidenav() {
      return this.$refs.layout.hide_nav();
    },
    shownav() {
      return this.$refs.layout.show_nav();
    },

    FiltrarUsuarios(e) {
      let id_agencia = e.target.value;

      if (id_agencia == 0) {
        this.usuarios_filtrados = this.usuarios;
      } else {
        this.usuarios_filtrados = this.usuarios.filter(
          (item) => item.agencia_id == id_agencia
        );
      }
      this.usuarioSeleccionado = 0;
    },
    obtenerCargo() {
      self = this;

      if (this.usuarioSeleccionado == 0) {
        this.cargo = null;
      } else {
        this.cargo = this.usuarios_filtrados.filter(
          (item) => item.dni == self.usuarioSeleccionado
        )[0].nombre_cargo;
      }
    },
    CopiarPermisos() {
      this.submitedC = true;
      self = this;
      let dni_de = this.usuarioSeleccionado;
      let dni_a = this.datosUsuarios[0].dni;

      if (self.usuarioSeleccionado == 0) {
        Swal.fire({
          icon: "error",
          title: "¡Olvidaste elegir un usuario!",
          text: "Selecciona un usuario",
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
          preConfirm: (result) => {
            self.$inertia.post(
              route("gen.per.permisos_usuarios_editar.copiar"),
              { dni_de: dni_de, dni_a: dni_a, modo: this.modo },
              {
                preserveScroll: true,
                onStart: (visit) => {
                  let timerInterval;
                  Swal.fire({
                    title: "EN PROGRESO",
                    html: "Espere porfavor...",
                    timer: 5000,
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                      Swal.showLoading();
                      timerInterval = setInterval(() => {
                        const content = Swal.getContent();
                        if (content) {
                          const b = content.querySelector("b");
                          if (b) {
                            b.textContent = Swal.getTimerLeft();
                          }
                        }
                      }, 100);
                    },
                    willClose: () => {
                      clearInterval(timerInterval);
                    },
                  });
                },
                onSuccess: () => {
                  Swal.fire({
                    icon: "success",
                    title: "¡ÉXITO!",
                    timer: 1200,
                    showConfirmButton: false,
                    preConfirm: (result) => {
                      self.submited = false;
                      //self.frmAsignarPermisos.permisoSeleccionados = [];
                      self.frmAsignarPermisos.permisoSeleccionados = [];
                      this.FiltrarAreasDisponibles();
                      this.FiltrarAreasActuales();
                      this.FiltrarAccesoAgencias();
                    },
                  });
                },
              }
            );
          },
        });
      }
    },

    CopiarPermisoCargo() {
      this.submitedCargos = true;
      self = this;
      let cargo_id_de = this.cargoSeleccionado;
      let dni_a = this.datosUsuarios[0].dni;

      if (self.cargoSeleccionado == 0) {
        Swal.fire({
          icon: "error",
          title: "¡Olvidaste elegir un cargo!",
          text: "Selecciona un cargo",
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
          preConfirm: (result) => {
            self.$inertia.post(
              route("gen.per.permisos_usuarios_editar.copiar_cargo"),
              {
                cargo_id_de: cargo_id_de,
                dni_a: dni_a,
                modo: this.modo,
              },
              {
                preserveScroll: true,
                onStart: (visit) => {
                  let timerInterval;
                  Swal.fire({
                    title: "EN PROGRESO",
                    html: "Espere porfavor...",
                    timer: 5000,
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                      Swal.showLoading();
                      timerInterval = setInterval(() => {
                        const content = Swal.getContent();
                        if (content) {
                          const b = content.querySelector("b");
                          if (b) {
                            b.textContent = Swal.getTimerLeft();
                          }
                        }
                      }, 100);
                    },
                    willClose: () => {
                      clearInterval(timerInterval);
                    },
                  });
                },

                onSuccess: () => {
                  Swal.fire({
                    icon: "success",
                    title: "¡ÉXITO!",
                    timer: 1200,
                    showConfirmButton: false,
                    preConfirm: (result) => {
                      self.submited = false;
                      self.frmAsignarPermisos.permisoSeleccionados = [];
                      this.FiltrarAreasDisponibles();
                      this.FiltrarAreasActuales();
                      this.FiltrarAccesoAgencias();
                    },
                  });
                },
              }
            );
          },
        });
      }
    },

    EditarPermisos(id_permiso, id) {
      this.submited = true;
      self = this;
      let lista_agencias_actual = [];
      let lista_agencias_editar = [];

      let index = self.permisosEditar.indexOf(id_permiso);
      self.permisosEditar.splice(index, 1);

      self.acceso_agencias.forEach(function (element) {
        if (element["permiso_id"] == id_permiso) {
          lista_agencias_actual.push(element["agencia_id"]);
        }
      });

      self.acceso_agencias_editar.forEach(function (element) {
        if (element["permiso_id"] == id_permiso) {
          lista_agencias_editar.push(element["agencia_id"]);
        }
      });

      if (!_.isEqual(lista_agencias_editar, lista_agencias_actual)) {
        Swal.fire({
          title: "EDITAR PERMISO",
          text: "¿Desea continuar?",
          confirmButtonText:
            '<i class="fas fa-check" style="color:white;"></i>   Si',
          confirmButtonColor: "var(--colorAlto)",
          showCancelButton: true,
          cancelButtonText: '<i class="fas fa-times"></i>   No',
          cancelButtonColor: "var(--plomoOscuroEmpresarial)",
          allowOutsideClick: false,
          preConfirm: (result) => {
            self.$inertia.post(
              route("gen.per.permisos_usuarios_editar.editar_agencia"),
              {
                lista_agencias_editar: lista_agencias_editar,
                id: id,
              },
              {
                preserveScroll: true,
                onStart: (visit) => {
                  let timerInterval;
                  Swal.fire({
                    title: "EN PROGRESO",
                    html: "Espere porfavor...",
                    timer: 5000,
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                      Swal.showLoading();
                      timerInterval = setInterval(() => {
                        const content = Swal.getContent();
                        if (content) {
                          const b = content.querySelector("b");
                          if (b) {
                            b.textContent = Swal.getTimerLeft();
                          }
                        }
                      }, 100);
                    },
                    willClose: () => {
                      clearInterval(timerInterval);
                    },
                  });
                },
                onSuccess: () => {
                  Swal.fire({
                    icon: "success",
                    title: "¡ÉXITO!",
                    timer: 1200,
                    showConfirmButton: false,
                    preConfirm: (result) => {
                      self.submited = false;
                    },
                  });
                },
              }
            );
          },
        });
      }
    },
    AgregarPermisos() {
      this.submited = true;
      self = this;
      if (self.frmAsignarPermisos.permisoSeleccionados.length == 0) {
        Swal.fire({
          icon: "error",
          title: "Olvidaste elegir los permisos!",
          text: "selecciona algun permiso",
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
          preConfirm: (result) => {
            self.$inertia.post(
              route("gen.per.permisos_usuarios_editar.asignar"),
              self.frmAsignarPermisos,
              {
                preserveScroll: true,
                onStart: (visit) => {
                  let timerInterval;
                  Swal.fire({
                    title: "EN PROGRESO",
                    html: "Espere porfavor...",
                    timer: 5000,
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                      Swal.showLoading();
                      timerInterval = setInterval(() => {
                        const content = Swal.getContent();
                        if (content) {
                          const b = content.querySelector("b");
                          if (b) {
                            b.textContent = Swal.getTimerLeft();
                          }
                        }
                      }, 100);
                    },
                    willClose: () => {
                      clearInterval(timerInterval);
                    },
                  });
                },
                onSuccess: () => {
                  Swal.fire({
                    icon: "success",
                    title: "¡ÉXITO!",
                    timer: 1200,
                    showConfirmButton: false,
                    preConfirm: (result) => {
                      self.submited = false;
                      self.frmAsignarPermisos.permisoSeleccionados = [];
                      self.FiltrarAreasDisponibles();
                      self.FiltrarAreasActuales();
                      self.FiltrarAccesoAgencias();
                    },
                  });
                },
              }
            );
          },
        });
      }
    },
    QuitarPermisos(id) {
      self = this;
      Swal.fire({
        title: "ELIMINAR PERMISO",
        text: "¿Desea continuar?",
        confirmButtonText:
          '<i class="fas fa-check" style="color:white;"></i>   Si',
        confirmButtonColor: "var(--colorAlto)",
        showCancelButton: true,
        cancelButtonText: '<i class="fas fa-times"></i>   No',
        cancelButtonColor: "var(--plomoOscuroEmpresarial)",
        allowOutsideClick: false,
        preConfirm: (result) => {
          self.$inertia.post(
            route("gen.per.permisos_usuarios_editar.eliminar"),
            { id: id },
            {
              preserveScroll: true,
              onStart: (visit) => {
                let timerInterval;
                Swal.fire({
                  title: "EN PROGRESO",
                  html: "Espere porfavor...",
                  timer: 5000,
                  allowOutsideClick: false,
                  timerProgressBar: true,
                  didOpen: () => {
                    Swal.showLoading();
                    timerInterval = setInterval(() => {
                      const content = Swal.getContent();
                      if (content) {
                        const b = content.querySelector("b");
                        if (b) {
                          b.textContent = Swal.getTimerLeft();
                        }
                      }
                    }, 100);
                  },
                  willClose: () => {
                    clearInterval(timerInterval);
                  },
                });
              },
              onSuccess: () => {
                Swal.fire({
                  icon: "success",
                  title: "¡ÉXITO!",
                  timer: 1200,
                  showConfirmButton: false,
                  preConfirm: (result) => {
                    self.submited = false;
                    self.frmAsignarPermisos.permisoSeleccionados = [];
                    self.FiltrarAreasDisponibles();
                    self.FiltrarAreasActuales();
                  },
                });
              },
            }
          );
        },
      });
    },
    EliminarTodosPermisos() {
      self = this;
      let dni_a = this.datosUsuarios[0].dni;
      let modo = this.modo;
      Swal.fire({
        title: "ELIMINAR TODOS LOS PERMISOS",
        text: "¿Desea continuar?",
        confirmButtonText:
          '<i class="fas fa-check" style="color:white;"></i>   Si',
        confirmButtonColor: "var(--colorAlto)",
        showCancelButton: true,
        cancelButtonText: '<i class="fas fa-times"></i>   No',
        cancelButtonColor: "var(--plomoOscuroEmpresarial)",
        allowOutsideClick: false,
        preConfirm: (result) => {
          axios
            //lo que esta en la llave{nombre:valor}
            .post(route("gen.per.permisos_usuarios_editar.eliminar_todo"), {
              dni_a: dni_a,
              modo: modo,
            })
            .then(function (response) {
              let resultado = response.data;
              if (resultado == "EXITO") {
                Swal.fire({
                  icon: "success",
                  title: "¡ÉXITO!",
                  text: "Todos los permisos eliminados",
                  timer: 1200,
                  showConfirmButton: false,
                  preConfirm: (result) => {
                    // location.reload();

                    self.$inertia.get(
                      route("gen.per.permisos_usuarios_editar", {
                        dni: dni_a,
                        modo: modo,
                      })
                    );
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
    },
  },
};
</script>

<style></style>
