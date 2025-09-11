<template>
  <layout ref="layout">
    <div class="slot_body slot-carrito-cobranzas" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose title="CARRITO DE COBRANZAS"></headerClose>

          <div class="card-body card-block">
            <div class="form-row">
              <fieldset class="form-group col-md-10 col-12">
                <legend>
                  <label class="label-title">FILTROS DE BÚSQUEDA</label>
                </legend>
                <div class="row">
                  <div class="input-group col-md-6">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">FECHA</span>
                    </div>
                    <input
                      type="date"
                      class="form-control center bolder"
                      v-model="fecha"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                      disabled
                    />
                  </div>

                  <div class="input-group col-md-6">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title"
                        >AGENCIA</span
                      >
                    </div>
                    <select
                      class="form-control center"
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
                </div>
              </fieldset>
              <div
                class="col-md-1 text-right"
                style="margin-bottom: 0rem !important"
              >
                <button
                  class="btn btn-action btn-icon-split mt-3"
                  @click="Listar"
                >
                  <span class="icon text-white">
                    <i class="fas fa-sync-alt" style="font-size: 25px"> </i
                  ></span>
                </button>
              </div>
            </div>

            <div class="card-title mt-2">LISTA DE RESULTADOS</div>

            <DataTable
              :value="lista_carritos"
              :scrollable="true"
              scrollDirection="both"
              scrollHeight="380px"
              selectionMode="single"
              showGridlines
            >
              <Column
                field="numero"
                header="N°"
                :styles="{ width: '40px', justifyContent: 'center' }"
              >
                <template #body="slotProps">
                  {{ slotProps.index + 1 }}
                </template>
              </Column>
              <Column
                field="usuario"
                header="USUARIO"
                :styles="{ width: '100px', justifyContent: 'center' }"
              >
              </Column>
              <Column
                header="CRÉDITOS"
                :styles="{ width: '50px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  <div class="celda-resaltada">
                    {{ data.pendientes_pago }}
                  </div>
                </template>
              </Column>
              <Column
                header="TOTAL COBRADO"
                :styles="{ width: '100px', justifyContent: 'right' }"
              >
                <template #body="{ data }">
                  <div class="celda-resaltada">
                    S/ {{ roundTo(data.total_pendiente_pago, 2) }}
                  </div>
                </template>
              </Column>
              <Column
                header="VER"
                :styles="{ width: '40px', justifyContent: 'center' }"
              >
                <template #body="{ data }">
                  <button
                    class="btn btn2"
                    title="VerCarrito"
                    @click="DetalleCarrito(data)"
                  >
                    <span class="icon text-white">
                      <i
                        class="fas fa-eye"
                        style="font-size: 9px !important"
                      ></i>
                    </span>
                  </button>
                </template>
              </Column>
              <template #empty> No hay CARRITOS para mostrar.</template>
            </DataTable>
          </div>
        </div>

        <div id="mdlVerCarrito" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-60 mdlVerCarrito">
            <div class="content" style="display: block">
              <div class="card">
                <headerCloseModal
                  :titulo_modal="'CARRITO DE COBRANZA CRÉDITOS'"
                  :nombre_modal="'mdlVerCarrito'"
                ></headerCloseModal>
                <div class="card-body card-block">
                  <div class="input-group col-md-4 m-1">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">FECHA</span>
                    </div>
                    <input
                      type="date"
                      class="form-control center bolder"
                      v-model="fecha"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                      disabled
                    />
                  </div>

                  <DataTable
                    :value="lista_creditos_pendientes"
                    :scrollable="true"
                    scrollDirection="both"
                    scrollHeight="380px"
                    :selection.sync="creditos_seleccionados"
                    showGridlines
                  >
                    <Column
                      field="numero"
                      header="N°"
                      :styles="{ width: '30px', justifyContent: 'center' }"
                    >
                      <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                      </template>
                    </Column>

                    <Column
                      header="CLIENTE"
                      :styles="{ width: '250px', justifyContent: 'left' }"
                    >
                      <template #body="{ data }">
                        <div class="row">
                          <div class="col-12">{{ data.cliente }}</div>
                          <div
                            class="col-12"
                            style="
                              font-size: 10px;
                              font-family: 'Roboto-BoldItalic';
                            "
                          >
                            {{ data.agencia_credito }}
                          </div>
                        </div>
                      </template>
                    </Column>
                    <Column
                      field="usuario_asesor"
                      header="ASESOR"
                      :styles="{ width: '150px', justifyContent: 'center' }"
                    >
                    </Column>
                    <Column
                      header="VER"
                      :styles="{ width: '50px', justifyContent: 'center' }"
                    >
                      <template #body="{ data }">
                        <button
                          class="btn btn2"
                          title="VerCredito"
                          @click="DetalleCredito(data)"
                        >
                          <span class="icon text-white">
                            <i
                              class="fas fa-eye"
                              style="font-size: 9px !important"
                            ></i>
                          </span>
                        </button>
                      </template>
                    </Column>
                    <Column
                      header="COBRO"
                      :styles="{ width: '100px', justifyContent: 'right' }"
                    >
                      <template #body="{ data }">
                        <div class="celda-resaltada">
                          {{
                            data.total_cobro == 0
                              ? "-"
                              : "S/ " + roundTo(data.total_cobro, 2)
                          }}
                        </div>
                      </template>
                    </Column>
                    <!-- <Column
											selectionMode="multiple"
											:headerStyle="{ width: '50px' }"
											:styles="{ width: '50px', justifyContent: 'center' }"
										>
										</Column> -->
                    <Column
                      :styles="{ width: '100px', justifyContent: 'center' }"
                    >
                      <template #header>
                        <div class="row">
                          <div class="col-12 center">TODO</div>
                          <div class="col-12 center">
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
                                  @change="SeleccionarTodo"
                                  v-model="todo_seleccionado"
                                />
                                <span class="cr"
                                  ><i
                                    class="cr-icon fa fa-check"
                                    style="color: blue"
                                    important
                                  ></i
                                ></span>
                              </label>
                            </div>
                          </div>
                        </div>
                      </template>
                      <template #body="{ data }">
                        <div class="align-middle">
                          <div class="checkbox">
                            <label
                              class="align-middle"
                              style="
                                font-size: 2em;
                                margin-bottom: 0 !important;
                                height: 28.6px !important;
                              "
                              :for="
                                'chb_' + data.agencia_credito + '_' + data.id
                              "
                              ><input
                                type="checkbox"
                                :id="
                                  'chb_' + data.agencia_credito + '_' + data.id
                                "
                                :value="data"
                                v-model="creditos_seleccionados" /><span
                                class="cr"
                                style="margin-right: 0 !important"
                                ><i class="cr-icon fa fa-check"></i></span
                            ></label>
                          </div>
                        </div>
                      </template>
                    </Column>
                    <Column
                      field="observacion"
                      header="OBSERVACIÓN"
                      :styles="{ width: '100px', justifyContent: 'center' }"
                    >
                    </Column>

                    <ColumnGroup type="footer">
                      <Row>
                        <Column
                          :colspan="4"
                          footer="TOTAL"
                          :footerStyle="{
                            width: '460px',
                            backgroundColor: '#244b9a !important',
                            fontSize: '15px !important',
                            textAlign: 'right',
                          }"
                        />

                        <Column
                          :colspan="1"
                          :footer="'S/ ' + roundTo(total, 2)"
                          :footerStyle="{
                            width: '100px',
                            fontSize: '15px !important',
                            textAlign: 'right',
                          }"
                        />
                        <Column
                          :colspan="1"
                          :footer="'S/ ' + roundTo(subtotal, 2)"
                          :footerStyle="{
                            width: '100px',
                            fontSize: '15px !important',
                            textAlign: 'right',
                          }"
                        />

                        <Column
                          :colspan="1"
                          :footer="null"
                          :footerStyle="{
                            width: '100px',
                            backgroundColor: 'transparent !important',
                            textAlign: 'right',
                          }"
                        />
                      </Row>
                    </ColumnGroup>
                  </DataTable>

                  <hr />
                  <div class="text-right">
                    <button
                      class="btn btn-action btn-icon-split"
                      title="Pagar"
                      @click="Pagar"
                      :disabled="lista_creditos_pendientes.length == 0"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-dollar-sign"> </i
                      ></span>
                      <span class="text">PAGAR</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="mdlCreditosPagados" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-50 mdlCreditosPagados">
            <div class="content" style="display: block">
              <div class="card">
                <div
                  class="card-header d-flex align-items-center justify-content-between"
                >
                  <strong>CARRITO DE CRÉDITOS PAGADOS</strong>

                  <button
                    type="button"
                    class="btn btn-green"
                    style="border-radius: 50%; float: right !important"
                    @click="CerrarModal"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-times"></i>
                    </span>
                  </button>
                </div>
                <div class="card-body card-block">
                  <div class="input-group col-md-5 m-1">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">FECHA</span>
                    </div>
                    <input
                      type="date"
                      class="form-control center bolder"
                      v-model="fecha"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                      disabled
                    />
                  </div>

                  <DataTable
                    :value="lista_creditos_pagados"
                    :scrollable="true"
                    scrollDirection="both"
                    scrollHeight="380px"
                    selectionMode="single"
                    showGridlines
                  >
                    <Column
                      field="numero"
                      header="N°"
                      :styles="{ width: '40px', justifyContent: 'center' }"
                    >
                      <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                      </template>
                    </Column>
                    <Column
                      field="cliente"
                      header="CLIENTE"
                      :styles="{ width: '250px', justifyContent: 'left' }"
                    >
                    </Column>
                    <Column
                      field="usuario_asesor"
                      header="ASESOR"
                      :styles="{ width: '150px', justifyContent: 'center' }"
                    >
                    </Column>
                    <Column
                      header="IMPRIMIR"
                      :styles="{ width: '60px', justifyContent: 'center' }"
                    >
                      <template #body="{ data }">
                        <button
                          class="btn btn2"
                          title="ImprimirVoucher"
                          @click="ImprimirVoucher(data)"
                        >
                          <span class="icon text-white">
                            <i
                              class="fa fa-print"
                              style="font-size: 9px !important"
                            ></i>
                          </span>
                        </button>
                      </template>
                    </Column>
                    <Column
                      header="COBRO"
                      :styles="{ width: '90px', justifyContent: 'right' }"
                    >
                      <template #body="{ data }">
                        <div class="celda-resaltada">
                          {{
                            data.total_cobro == 0
                              ? "-"
                              : "S/ " + roundTo(data.total_cobro, 2)
                          }}
                        </div>
                      </template>
                    </Column>

                    <ColumnGroup type="footer">
                      <Row>
                        <Column
                          :colspan="4"
                          footer="TOTAL"
                          :footerStyle="{
                            width: '550px',
                            backgroundColor: '#244b9a !important',
                            fontSize: '15px !important',
                            textAlign: 'right',
                          }"
                        />

                        <Column
                          :colspan="1"
                          :footer="'S/ ' + roundTo(subtotal, 2)"
                          :footerStyle="{
                            width: '100px',
                            fontSize: '15px !important',
                            textAlign: 'right',
                          }"
                        />
                      </Row>
                    </ColumnGroup>
                  </DataTable>
                </div>
              </div>
            </div>
          </div>
        </div>

        <mdlDetalleCreditoCarrito
          ref="mdlDetalleCreditoCarrito"
        ></mdlDetalleCreditoCarrito>
      </div>
    </div>
  </layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import mdlDetalleCreditoCarrito from "@/Pages/Creditos/Creditos/Components/mdlDetalleCreditoCarrito.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "vue2-datepicker/locale/es";

import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";
import ColumnGroup from "primevue/columngroup/columngroup.common";
import Row from "primevue/row/row.common";

export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,
    mdlDetalleCreditoCarrito,
    DatePicker,

    DataTable,
    Column,
    ColumnGroup,
    Row,
  },
  props: {},

  data() {
    return {
      windowWidth: window.innerWidth,
      agencia_busqueda: 0,
      agencias_permitidas: [],

      fecha: null,

      carrito_id: null,
      usuario_carrito: 0,

      lista_carritos: [],
      lista_creditos_pendientes: [],
      total: this.roundTo(0, 2),
      subtotal: this.roundTo(0, 2),
      mostrar: null,

      creditos_seleccionados: [],
      lista_vouchers: [],
      lista_creditos_pagados: [],

      lista_creditos_pendientes_pagados: [],

      todo_seleccionado: false,
    };
  },
  computed: {
    mi_caja() {
      return this.$inertia.page.props.creditos_datos.datos_caja;
    },
  },
  watch: {
    async agencia_busqueda() {
      await this.FechaActual();
      this.Listar();
    },

    lista_creditos_pendientes() {
      this.calcular_total();
    },

    creditos_seleccionados() {
      this.calcular_subtotal();
    },

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
  },

  mounted() {
    this.ListarAgenciasPermitidas();

    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },

  methods: {
    async FechaActual() {
      if (this.agencia_busqueda == null) {
        return false;
      } else {
        let fecha_actual = await this.$refs.layout.fecha_hora_actual(
          this.agencia_busqueda
        );

        fecha_actual = fecha_actual.substring(0, 10);
        this.fecha = fecha_actual;
      }
    },

    ListarAgenciasPermitidas() {
      this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
        "CREDITOS_CAJA/CARRITO_COBRANZAS"
      );
    },

    calcular_total() {
      let t_pago_total = 0;

      this.lista_creditos_pendientes.forEach((element) => {
        if (element.total_cobro != null) {
          t_pago_total += parseFloat(element.total_cobro);
        }
      });

      this.total = t_pago_total;
    },

    calcular_subtotal() {
      let t_pago_subtotal = 0;

      this.creditos_seleccionados.forEach((element) => {
        if (element.total_cobro != null) {
          t_pago_subtotal += parseFloat(element.total_cobro);
        }
      });

      this.subtotal = t_pago_subtotal;
    },

    roundTo(value, decimal_places) {
      let valor = 0;
      let numero_decimales = decimal_places;

      if (value) {
        valor = value;
      }

      let resultado = parseFloat(valor).toLocaleString("es-PE", {
        minimumFractionDigits: numero_decimales,
        maximumFractionDigits: numero_decimales,
      });

      return resultado;
    },

    async Listar() {
      const loadingAlert = Swal.fire({
        title: "CARGANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: async () => {
          const params = {
            fecha: this.fecha,
            agencia_id: this.agencia_busqueda,
          };

          // this.$inertia.get(route("caj.carrito_cobranzas.buscar"), params);
          // return false;

          Swal.showLoading();
          await axios
            .get(route("caj.carrito_cobranzas.buscar"), { params })
            .then((response) => {
              if (response.data.lista_carritos.length == 0) {
                this.lista_carritos = [];
                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No hay carritos aperturados",
                  allowOutsideClick: true,
                });
              } else {
                this.lista_carritos = response.data.lista_carritos;

                loadingAlert.close();
              }
            });
        },
      });
    },

    async DetalleCarrito(item) {
      const loadingAlert = Swal.fire({
        title: "CARGANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: async () => {
          const params = {
            carrito_id: item.id,
            agencia_carrito: this.agencia_busqueda,
          };

          // this.$inertia.get(route("caj.carrito_cobranzas.creditos"), params);
          // return false;

          Swal.showLoading();
          await axios
            .get(route("caj.carrito_cobranzas.creditos"), { params })
            .then((response) => {
              if (response.data.lista_creditos.length == 0) {
                this.lista_creditos_pendientes = [];
                return Swal.fire({
                  icon: "info",
                  title: "¡Ups!",
                  text: "No hay créditos pagados en este carrito",
                  allowOutsideClick: true,
                });
              } else {
                this.creditos_seleccionados = [];
                this.carrito_id = item.id;
                this.usuario_carrito = item.usuario_id;
                this.lista_creditos_pendientes = response.data.lista_creditos;

                loadingAlert.close();
                $("#mdlVerCarrito").css("display", "block");
              }
            });
        },
      });
    },

    async DetalleCredito(item) {
      const self = this;

      let credito_id = item.credito_id;
      let carrito_detalle_id = item.id;

      if (carrito_detalle_id == null) {
        carrito_detalle_id = 0;
      }

      if (this.windowWidth < 900) {
        this.mostrar = false;
      }
      if (this.windowWidth >= 900) {
        this.mostrar = true;
      }

      const params = {
        agencia_credito: item.agencia_id,
        credito_id: item.credito_id,
        carrito_detalle_id: item.id,
      };

      // this.$inertia.get(route("cre.carrito.detalle"), params);
      // return false;

      await axios
        .get(route("cre.carrito.detalle"), { params })
        .then((response) => {
          let mdlDetalleCreditoCarrito = this.$refs.mdlDetalleCreditoCarrito;
          async function EnviarDatos() {
            mdlDetalleCreditoCarrito.agencia_id =
              response.data.datos_credito.agencia_id;
            mdlDetalleCreditoCarrito.credito_id = credito_id;
            mdlDetalleCreditoCarrito.datos_credito =
              response.data.datos_credito;
            mdlDetalleCreditoCarrito.datos_cuotas = response.data.datos_cuotas;
            mdlDetalleCreditoCarrito.notificaciones =
              response.data.notificaciones;
            mdlDetalleCreditoCarrito.carrito_detalle =
              response.data.carrito_detalle;
            mdlDetalleCreditoCarrito.mostrar = self.mostrar;
            mdlDetalleCreditoCarrito.modo = "VER";

            mdlDetalleCreditoCarrito.nuevo_telefono_principal =
              mdlDetalleCreditoCarrito.telefono_principal =
                response.data.carrito_detalle.telefono_envio;

            mdlDetalleCreditoCarrito.frmDatosCobranza.modo_envio =
              response.data.carrito_detalle.modo_envio;
          }
          EnviarDatos().then(() => {
            $("#mdlDetalleCreditoCarrito").css("display", "block");

            mdlDetalleCreditoCarrito.ResetearCampos();
          });
        });
    },

    async Pagar() {
      if (this.mi_caja == null) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Primero debe aperturar CAJA",
          confirmButtonText: "Ok",
          allowOutsideClick: true,
        });
        return false;
      }

      if (this.creditos_seleccionados.length == 0) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe seleccionar almenos un crédito",
          confirmButtonText: "Ok",
          allowOutsideClick: true,
        });
        return false;
      }
      let text = "";
      if (
        this.creditos_seleccionados.length ==
        this.lista_creditos_pendientes.length
      ) {
        text =
          "¿Ha seleccionado todos los créditos, desea continuar con el pago?";
      } else if (
        this.creditos_seleccionados.length <
        this.lista_creditos_pendientes.length
      ) {
        text = "¿Desea pagar los créditos seleccionados?";
      }

      Swal.fire({
        icon: "question",
        title: text,
        confirmButtonText: "Si",
        showCancelButton: true,
        cancelButtonText: "No",
        allowOutsideClick: false,
      }).then((result) => {
        if (result.isConfirmed) {
          const loadingAlert = Swal.fire({
            title: "PAGANDO",
            text: "Espere porfavor...",
            allowOutsideClick: false,
            didOpen: async () => {
              let data = new FormData();
              data.append(
                "lista_creditos",
                JSON.stringify(this.creditos_seleccionados)
              );
              data.append(
                "cantidad_total_creditos",
                JSON.stringify(this.lista_creditos_pendientes.length)
              );
              data.append("agencia_carrito", this.agencia_busqueda);
              data.append("carrito_id", this.carrito_id);
              data.append("caja_id", this.mi_caja.id);
              data.append("agencia_caja", this.mi_caja.agencia_id);

              //   this.$inertia.post(route("caj.carrito_cobranzas.pagar"), data);
              //   return false;

              Swal.showLoading();
              await axios
                .post(route("caj.carrito_cobranzas.pagar"), data)
                .then((response) => {
                  this.lista_vouchers = response.data.lista_vouchers;
                  this.lista_creditos_pagados = this.creditos_seleccionados;

                  loadingAlert.close();
                  $("#mdlVerCarrito").css("display", "none");
                  $("#mdlCreditosPagados").css("display", "block");

                  return Swal.fire({
                    icon: "success",
                    title: "¡ÉXITO!",
                    text: "Cobro exitoso",
                    timer: 1500,
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
        } else {
          return false;
        }
      });
    },

    CerrarModal() {
      this.lista_creditos_pagados = [];
      $("#mdlCreditosPagados").css("display", "none");
      this.Listar();
    },

    SeleccionarTodo() {
      if (this.todo_seleccionado == false) {
        this.creditos_seleccionados = [];
      } else if (this.todo_seleccionado == true) {
        this.creditos_seleccionados = this.lista_creditos_pendientes;
      }
    },

    async ImprimirVoucher(item) {
      let vouchers = this.lista_vouchers.filter(
        (item_1) =>
          item_1.agencia_credito == item.agencia_id &&
          item_1.credito_id == item.credito_id
      )[0];

      let fecha_hora_actual = await this.$refs.layout.fecha_hora_actual(
        this.agencia_busqueda
      );

      let detalle_cuota = " Próx:";
      let titulo = null;
      let monto_cuota = null;

      if (vouchers.tipo_voucher == "cobranza") {
        titulo = "CONSTANCIA DE AMORTIZACIÓN";
        detalle_cuota += String(vouchers.voucher_pago[0].cuota_actual);
        detalle_cuota += " Pend:";
        detalle_cuota += String(
          parseInt(vouchers.cantidad_cuotas) -
            parseInt(vouchers.voucher_pago[0].cuota_actual) +
            1
        );

        monto_cuota = this.roundTo(vouchers.cuota, 2);
      } else if (vouchers.tipo_voucher == "cancelacion") {
        titulo = "CONSTANCIA DE CANCELACIÓN";
        detalle_cuota += "-";
        detalle_cuota += " Pend:";
        detalle_cuota += 0;

        monto_cuota = "-";
      }

      let total = 0;
      vouchers.voucher_pago.forEach((element) => {
        if (element.concepto != "Saldo total") {
          total += element.importe;
        }
      });

      let frmDatosVoucher = {
        titulo: titulo,
        cliente: item.cliente,
        usuario_asesor: item.usuario_asesor,
        monto_cuota: monto_cuota,
        cuota_actual: detalle_cuota,
        conceptos: vouchers.voucher_pago,
        importe: total,
        agencia_caja: this.$page.props.user_session.nombre_agencia,
        fecha_pago: fecha_hora_actual,
        usuario_caja: this.$page.props.user_session.usuario,
        nombre_dispositivo: this.$page.props.user_session.dispositivo.nombre,
      };

      let data = new FormData();

      data.append("agencia_id", item.agencia_id);
      data.append("datos_voucher", JSON.stringify(frmDatosVoucher));

      //this.$inertia.post(route("caj.cobranza.voucher"), data);
      // return false;

      await axios
        .post(route("caj.cobranza.voucher"), data)
        .then(function (response) {
          let origin = window.location.origin;
          let path_pdf = response.data.path_pdf;

          // Crear un IFrame
          let iframe = document.createElement("iframe");
          // Oculto el iframe
          iframe.style.display = "none";
          // Defino el source
          iframe.src = origin + path_pdf;
          // Añadir el Iframe a la vista
          document.body.appendChild(iframe);

          iframe.contentWindow.focus(); // Enfoca
          iframe.contentWindow.print(); // Imprime

          return Swal.fire({
            icon: "success",
            title: "¡LISTO!",
            timer: 1200,
            showConfirmButton: false,
          });
        });
    },
  },
};
</script>

<style lang="css">
.slot-carrito-cobranzas {
  width: 50% !important;
  margin-left: 25% !important;
}
.mdlVerCarrito {
  margin-top: 2.8%;
}
.mdlCreditosPagados {
  margin-top: 2.8%;
}

.celda-resaltada {
  font-size: 14px !important;
  font-weight: bolder;
}

.btn2 {
  color: #fff;
  background-color: var(--azulOscuroEmpresarial);
  border-color: var(--azulOscuroEmpresarial);
  font-size: 10px !important;
}

.btn2:hover {
  color: #fff;
  background-color: #385efb;
  border-color: #385efb;
}
.checkbox .cr {
  /* Estilos para la clase específica */
  border: 1.5px solid black !important; /* Cambia el color del borde a negro */
  background: white !important;
}

@media (max-width: 900px) {
  .slot-carrito-cobranzas {
    width: 99% !important;
    margin-left: 0.5% !important;
  }
  .btn2 {
    color: #fff;
    background-color: var(--azulOscuroEmpresarial);
    border-color: var(--azulOscuroEmpresarial);
    font-size: 10px !important;
  }

  .btn2:hover {
    color: #fff;
    background-color: #385efb;
    border-color: #385efb;
  }
  .mdlCreditosPagados {
    margin-top: 1.7%;
  }
  .checkbox .cr {
    /* Estilos para la clase específica */
    border: 1.5px solid black !important; /* Cambia el color del borde a negro */
    background: white !important;
  }
}
</style>
