<template>
  <div id="mdlModificacionesCarrito" class="modal">
    <!-- Modal content -->
    <div class="modal-content w-25 mdlModificacionesCarrito">
      <div class="content" style="display: block">
        <div class="card">
          <headerCloseModal
            :titulo_modal="'MODIFICACIONES'"
            :nombre_modal="'mdlModificacionesCarrito'"
          >
          </headerCloseModal>

          <div class="card-body card-block">
            <div class="card-title mt-2">LISTA DE RESULTADOS</div>

            <table class="table" id="tblModificacionesCarrito" width="100%">
              <thead>
                <tr>
                  <th style="min-width: 10px !important">N°</th>
                  <th style="min-width: 50px !important">VER</th>
                  <th style="min-width: 100px !important">FECHA_EDICIÓN</th>
                  <!-- <th style="min-width: 40px !important">VER</th> -->
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in lista_credito_modificado"
                  :key="index"
                  class="table-bordered"
                  :class="index % 2 == 0 ? 'verde-claro' : ''"
                >
                  <td align="center">{{ index + 1 }}</td>
                  <td align="center">
                    <button
                      class="btn btn-action"
                      title="VerModificacionVista"
                      @click="VerModificacionVista(item)"
                    >
                      <span class="icon text-white">
                        <i
                          class="fas fa-eye"
                          style="font-size: 9px !important"
                        ></i>
                      </span>
                    </button>
                  </td>

                  <td
                    align="center"
                    style="
                      font-size: 13px !important;
                      font-weight: bold !important;
                    "
                  >
                    {{ item.fecha_registro }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="mdlModificacionVista" class="modal">
      <!-- Modal content -->
      <div class="modal-content w-25 mdlModificacionVista">
        <div class="content" style="display: block">
          <div class="card">
            <headerCloseModal
              :titulo_modal="'VISTA MODIFICACIÓN '"
              :nombre_modal="'mdlModificacionVista'"
            ></headerCloseModal>

            <div class="card-body card-block">
              <div class="form-group">
                <fieldset
                  class="p-0 pt-2 pl-3 pr-2"
                  style="background-color: #d8f1fd"
                >
                  <legend>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="chbPagoCuotas"
                        :checked="frmDatosCobranza.pago_cuota"
                        v-model="frmDatosCobranza.pago_cuota"
                        disabled
                      />
                      <label
                        class="label-title p-1"
                        for="chbPagoCuotas"
                        style="
                          background: var(--colorMedio);
                          color: white !important;
                          font-size: 12px;
                          border-radius: 3px;
                        "
                      >
                        PAGO DE CUOTAS
                      </label>
                    </div>
                  </legend>

                  <div class="input-group col-md-12 mb-3">
                    <div class="input-group-prepend">
                      <div class="input-group-text prepend-title">
                        <input
                          type="radio"
                          name="forma_pago"
                          id="rdbPorCuota"
                          value="por_cuota"
                          v-model="frmDatosCobranza.forma_pago"
                          disabled
                        />
                      </div>
                    </div>
                    <div class="input-group-prepend">
                      <label
                        class="input-group-text"
                        for="rdbPorCuota"
                        style="font-size: 13px"
                      >
                        Por cuota
                      </label>
                    </div>
                    <input
                      type="number"
                      class="form-control center"
                      style="max-width: 45px; font-size: 15px"
                      v-model.number="frmDatosCobranza.pago_cuota_cantidad"
                      name="por_cuota"
                      lang="en"
                      v-if="this.frmDatosCobranza.forma_pago == 'por_cuota'"
                      disabled
                    />
                    <div class="input-group-append">
                      <span
                        class="input-group-text text-right"
                        :style="
                          windowWidth >= 900
                            ? 'width: 95px;font-weight: bolder;font-size: 17px;color: var(--colorAlto);'
                            : 'width: 90px;font-weight: bolder;font-size: 17px;color: var(--colorAlto);'
                        "
                        disabled
                        >S/ {{ frmDatosCobranza.pago_cuota_monto }}</span
                      >
                    </div>
                  </div>

                  <div class="input-group col-md-12 mb-3">
                    <div class="input-group-prepend">
                      <div class="input-group-text prepend-title">
                        <input
                          type="radio"
                          name="forma_pago"
                          id="rdbPorMonto"
                          value="por_monto"
                          v-model="frmDatosCobranza.forma_pago"
                          disabled
                        />
                      </div>
                    </div>
                    <div class="input-group-prepend">
                      <label
                        class="input-group-text"
                        for="rdbPorMonto"
                        style="font-size: 13px"
                      >
                        Por monto
                      </label>
                    </div>
                    <div class="input-group-prepend">
                      <span class="input-group-text" style="font-size: 15px"
                        >S/
                      </span>
                    </div>

                    <div class="input-group-append">
                      <span
                        class="input-group-text text-right"
                        :style="
                          windowWidth >= 900
                            ? 'width: 90px;font-weight: bolder;font-size: 17px;color: var(--colorAlto);'
                            : 'width: 90px;font-weight: bolder;font-size: 17px;color: var(--colorAlto);'
                        "
                        disabled
                        >{{ frmDatosCobranza.pago_monto }}</span
                      >
                    </div>
                  </div>
                </fieldset>

                <div class="form-row col-md-12 ml-4 mt-2">
                  <div class="form-check col-md-5 col-5">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="chbPagoMora"
                      :checked="frmDatosCobranza.pago_mora"
                      v-model="frmDatosCobranza.pago_mora"
                      disabled
                    />
                    <label
                      class="label-title p-1"
                      for="chbPagoMora"
                      style="
                        background: var(--colorMedio);
                        color: white !important;
                        font-size: 12px;
                        border-radius: 3px;
                      "
                    >
                      PAGO DE MORA
                    </label>
                  </div>
                  <div class="input-group col-md-6 col-7">
                    <div class="input-group-prepend">
                      <span class="input-group-text" style="font-size: 15px"
                        >S/
                      </span>
                    </div>

                    <input
                      type="number"
                      class="form-control center"
                      min="0"
                      step="0.1"
                      lang="en"
                      v-model.number="frmDatosCobranza.pago_mora_monto"
                      name="pago_mora"
                      style="
                        font-size: 17px;
                        font-weight: bolder;
                        color: var(--colorAlto);
                      "
                      disabled
                    />
                  </div>
                </div>
                <div class="form-row col-md-12 ml-4 mt-2">
                  <div class="form-check col-md-5 col-5">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="chbPagoNotificacion"
                      :checked="frmDatosCobranza.pago_notificaciones"
                      v-model="frmDatosCobranza.pago_notificaciones"
                      disabled
                    />
                    <label
                      class="label-title p-1"
                      for="chbPagoNotificacion"
                      style="
                        background: var(--colorMedio);
                        color: white !important;
                        font-size: 12px;
                        border-radius: 3px;
                      "
                    >
                      PAGO DE NOTIF.
                    </label>
                  </div>
                  <div class="input-group col-md-6 col-7">
                    <div class="input-group-prepend">
                      <span class="input-group-text" style="font-size: 15px"
                        >S/
                      </span>
                    </div>

                    <input
                      type="number"
                      class="form-control center"
                      min="0"
                      step="0.1"
                      lang="en"
                      v-model.number="
                        frmDatosCobranza.pago_notificaciones_monto
                      "
                      name="pago_notificaciones"
                      style="
                        font-size: 17px;
                        font-weight: bolder;
                        color: var(--colorAlto);
                      "
                      disabled
                    />
                  </div>
                </div>
              </div>

              <div class="form-group col-md-12">
                <fieldset class="p-0 pt-2 pl-3 pr-2">
                  <div>
                    <p
                      class="text center"
                      style="
                        font-size: 15px;
                        color: #48494b;
                        font-weight: bold;

                        margin: 0;
                      "
                    >
                      TOTAL PAGADO
                    </p>
                    <p
                      class="text center"
                      style="
                        font-size: 22px;
                        color: var(--colorAlto);
                        font-weight: bolder;
                        margin-bottom: 0rem;
                      "
                    >
                      S/ {{ frmDatosCobranza.total_cobro }}
                    </p>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
  components: { headerCloseModal },

  data() {
    return {
      submited: false,
      windowWidth: window.innerWidth,
      lista_credito_modificado: [],

      frmDatosCobranza: {
        pago_cuota_cantidad: 0,
        pago_cuota_monto: this.roundTo(0, 2),
        pago_monto: this.roundTo(0, 2),
        pago_cuota: false,

        pago_mora: false,
        pago_mora_monto: this.roundTo(0, 2),
        pago_notificaciones: false,
        pago_notificaciones_monto: this.roundTo(0, 2),
        forma_pago: "",

        total_cobro: this.roundTo(0, 2),
      },
    };
  },

  watch: {
    lista_credito_modificado() {
      $("#tblModificacionesCarrito").DataTable().destroy();
      this.TablaModificacionesCredito();
    },
  },

  mounted() {
    this.TablaModificacionesCredito();
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },

  methods: {
    roundTo(value, decimal_places) {
      let valor = 0;
      let numero_decimales = decimal_places;

      if (value) {
        valor = value;
      }
      return parseFloat(valor).toFixed(numero_decimales);
    },

    TablaModificacionesCredito() {
      this.$nextTick(() => {
        let scroll_height = "350px";
        if (this.windowWidth <= 900) {
          scroll_height = "250px";
        }
        var table = $("#tblModificacionesCarrito").DataTable({
          scrollY: scroll_height,
          scrollX: true,
          fixedColumns: {
            leftColumns: 0,
          },
          scrollCollapse: true,
          paging: false,
          ordering: false,
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
        });
      });
    },
    VerModificacionVista(item) {
      this.frmDatosCobranza.pago_cuota_cantidad = 0;
      this.frmDatosCobranza.pago_cuota_monto = this.roundTo(0, 2);
      this.frmDatosCobranza.pago_monto = this.roundTo(0, 2);
      this.frmDatosCobranza.pago_cuota = false;
      this.frmDatosCobranza.pago_mora = false;
      this.frmDatosCobranza.pago_mora_monto = this.roundTo(0, 2);
      this.frmDatosCobranza.pago_notificaciones = false;
      this.frmDatosCobranza.pago_notificaciones_monto = this.roundTo(0, 2);
      this.frmDatosCobranza.forma_pago = "";
      this.frmDatosCobranza.total_cobro = this.roundTo(0, 2);

      if (JSON.parse(item.historial)[0].pago_por_cuota == 1) {
        this.frmDatosCobranza.forma_pago = "por_cuota";
        this.frmDatosCobranza.pago_cuota_cantidad = JSON.parse(
          item.historial
        )[0].pago_cantidad_cuota;
        this.frmDatosCobranza.pago_cuota_monto = JSON.parse(
          item.historial
        )[0].pago_cuota_monto;

        this.frmDatosCobranza.pago_cuota = true;
      }

      if (JSON.parse(item.historial)[0].pago_por_monto == 1) {
        this.frmDatosCobranza.forma_pago = "por_monto";
        this.frmDatosCobranza.pago_monto = JSON.parse(
          item.historial
        )[0].pago_monto;
        this.frmDatosCobranza.pago_cuota = true;
      }

      this.frmDatosCobranza.pago_mora = JSON.parse(item.historial)[0].pago_mora;
      if (this.frmDatosCobranza.pago_mora) {
        this.frmDatosCobranza.pago_mora_monto = JSON.parse(
          item.historial
        )[0].pago_mora_monto;
      }
      this.frmDatosCobranza.pago_notificaciones = JSON.parse(
        item.historial
      )[0].pago_notificaciones;
      if (this.frmDatosCobranza.pago_notificaciones) {
        this.frmDatosCobranza.pago_notificaciones_monto = JSON.parse(
          item.historial
        )[0].pago_notificaciones_monto;
      }

      this.frmDatosCobranza.total_cobro = JSON.parse(
        item.historial
      )[0].total_cobro;

      $("#mdlModificacionVista").css("display", "block");
    },
  },
};
</script>

<style lang="css">
.mdlModificacionesCarrito {
  margin-top: 5%;
}
.mdlModificacionVista {
  margin-top: 7%;
}

@media only screen and (max-width: 900px) {
  .mdlModificacionesCarrito {
    margin-top: 35%;
  }
}
</style>
