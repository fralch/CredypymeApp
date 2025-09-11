<template>
  <div id="mdlSimuladorBilleteo" class="modal">
    <!-- Modal content -->
    <div class="modal-content w-50 mdlSimuladorBilleteo">
      <div class="content" style="display: block">
        <div class="card">
          <headerCloseModal
            ref="headerCloseModal"
            :titulo_modal="'SIMULADOR DE BILLETEO'"
            :nombre_modal="'mdlSimuladorBilleteo'"
          >
          </headerCloseModal>

          <div class="card-body card-block">
            <div class="form-row">
              <div class="col-md-4">
                <label
                  class="label-title ml-3 p-1"
                  style="
                    background: var(--colorMedio);
                    color: white !important;
                    font-size: 12px;
                    border-radius: 3px;
                  "
                  >MONEDAS</label
                >

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      0.01
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="un_centimo"
                    v-model="frmBilleteo.un_centimo"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                      >{{
                        parseFloat(frmBilleteo.un_centimo * 0.01).toFixed(2)
                      }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      0.10
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="diez_centimos"
                    v-model="frmBilleteo.diez_centimos"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{
                        parseFloat(frmBilleteo.diez_centimos * 0.1).toFixed(2)
                      }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      0.20
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="veinte_centimos"
                    v-model="frmBilleteo.veinte_centimos"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{
                        parseFloat(frmBilleteo.veinte_centimos * 0.2).toFixed(2)
                      }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      0.50
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="cincuenta_centimos"
                    v-model="frmBilleteo.cincuenta_centimos"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{
                        parseFloat(
                          frmBilleteo.cincuenta_centimos * 0.5
                        ).toFixed(2)
                      }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      1.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="un_sol"
                    v-model="frmBilleteo.un_sol"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{ parseFloat(frmBilleteo.un_sol * 1).toFixed(2) }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      2.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="dos_soles"
                    v-model="frmBilleteo.dos_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{ parseFloat(frmBilleteo.dos_soles * 2).toFixed(2) }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      5.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="cinco_soles"
                    v-model="frmBilleteo.cinco_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{ parseFloat(frmBilleteo.cinco_soles * 5).toFixed(2) }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <label
                  class="label-title ml-3 p-1"
                  style="
                    background: var(--colorMedio);
                    color: white !important;
                    font-size: 12px;
                    border-radius: 3px;
                  "
                  >BILLETES</label
                >

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      10.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="diez_soles"
                    v-model="frmBilleteo.diez_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{ parseFloat(frmBilleteo.diez_soles * 10).toFixed(2) }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      20.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="veinte_soles"
                    v-model="frmBilleteo.veinte_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{ parseFloat(frmBilleteo.veinte_soles * 20).toFixed(2) }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      50.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="cincuenta_soles"
                    v-model="frmBilleteo.cincuenta_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{
                        parseFloat(frmBilleteo.cincuenta_soles * 50).toFixed(2)
                      }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      100.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="cien_soles"
                    v-model="frmBilleteo.cien_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{ parseFloat(frmBilleteo.cien_soles * 100).toFixed(2) }}
                    </span>
                  </div>
                </div>

                <div class="input-group mb-2 col-md-12">
                  <div class="input-group-prepend">
                    <div
                      class="input-group-text"
                      style="width: 60px !important"
                    >
                      200.00
                    </div>
                  </div>
                  <input
                    class="form-control text-center"
                    style="max-width: 70px !important"
                    type="number"
                    min="0"
                    name="doscientos_soles"
                    v-model="frmBilleteo.doscientos_soles"
                    @change="Redondear"
                  />

                  <div class="input-group-append">
                    <span
                      class="input-group-text"
                      style="font-size: 15px; width: 80px !important"
                    >
                      {{
                        parseFloat(frmBilleteo.doscientos_soles * 200).toFixed(
                          2
                        )
                      }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-md-4 center">
                <label class="label-title">EN TOTAL USTED TIENE:</label>
                <h1 class="bolder" style="font-size: 30px">
                  S/ {{ parseFloat(total_billeteo).toFixed(2) }}
                </h1>
              </div>
            </div>
            <hr />
            <!-- ------- -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <!-- <button
											id="btnImprimir"
											class="btn btn-action btn-icon-split"
										>
											<span class="icon text-white">
												<i class="fas fa-print"></i>
											</span>
											<span class="text">IMPRIMIR</span>
										</button> -->
              </div>

              <div class="form-group col-md-6 text-right">
                <div class="btn-group" role="group">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="Resetear"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-sync"></i>
                    </span>
                    <span class="text">NUEVO</span>
                  </button>
                </div>
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
  props: {},
  data() {
    return {
      frmBilleteo: {
        id: 0,
        un_centimo: 0,
        diez_centimos: 0,
        veinte_centimos: 0,
        cincuenta_centimos: 0,
        un_sol: 0,
        dos_soles: 0,
        cinco_soles: 0,
        diez_soles: 0,
        veinte_soles: 0,
        cincuenta_soles: 0,
        cien_soles: 0,
        doscientos_soles: 0,
      },
    };
  },

  computed: {
    total_billeteo() {
      return (
        this.frmBilleteo.doscientos_soles * 200 +
        this.frmBilleteo.cien_soles * 100 +
        this.frmBilleteo.cincuenta_soles * 50 +
        this.frmBilleteo.veinte_soles * 20 +
        this.frmBilleteo.diez_soles * 10 +
        this.frmBilleteo.cinco_soles * 5 +
        this.frmBilleteo.dos_soles * 2 +
        this.frmBilleteo.un_sol * 1 +
        this.frmBilleteo.cincuenta_centimos * 0.5 +
        this.frmBilleteo.veinte_centimos * 0.2 +
        this.frmBilleteo.diez_centimos * 0.1 +
        this.frmBilleteo.un_centimo * 0.01
      );
    },
  },

  mounted() {},

  methods: {
    Resetear() {
      this.frmBilleteo.doscientos_soles = 0;
      this.frmBilleteo.cien_soles = 0;
      this.frmBilleteo.cincuenta_soles = 0;
      this.frmBilleteo.veinte_soles = 0;
      this.frmBilleteo.diez_soles = 0;
      this.frmBilleteo.cinco_soles = 0;
      this.frmBilleteo.dos_soles = 0;
      this.frmBilleteo.un_sol = 0;
      this.frmBilleteo.cincuenta_centimos = 0;
      this.frmBilleteo.veinte_centimos = 0;
      this.frmBilleteo.diez_centimos = 0;
      this.frmBilleteo.un_centimo = 0;
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
    Redondear(e) {
      let denominacion = e.target.name;

      let valor = 0;

      if (e.target.value && e.target.value >= 0) {
        valor = e.target.value;
      }

      let monto = parseFloat(valor).toFixed(0);

      switch (denominacion) {
        case "un_centimo":
          this.frmBilleteo.un_centimo = monto;
          break;
        case "diez_centimos":
          this.frmBilleteo.diez_centimos = monto;
          break;
        case "veinte_centimos":
          this.frmBilleteo.veinte_centimos = monto;
          break;
        case "cincuenta_centimos":
          this.frmBilleteo.cincuenta_centimos = monto;
          break;
        case "un_sol":
          this.frmBilleteo.un_sol = monto;
          break;
        case "dos_soles":
          this.frmBilleteo.dos_soles = monto;
          break;
        case "cinco_soles":
          this.frmBilleteo.cinco_soles = monto;
          break;
        case "diez_soles":
          this.frmBilleteo.diez_soles = monto;
          break;
        case "veinte_soles":
          this.frmBilleteo.veinte_soles = monto;
          break;
        case "cincuenta_soles":
          this.frmBilleteo.cincuenta_soles = monto;
          break;
        case "cien_soles":
          this.frmBilleteo.cien_soles = monto;
          break;
        case "doscientos_soles":
          this.frmBilleteo.doscientos_soles = monto;
          break;
      }
    },
  },
};
</script>

<style lang="css">
@media (max-width: 900px) {
  .mdlSimuladorBilleteo {
    width: 98% !important;
    margin-left: 1% !important;
    margin-top: 15% !important;
  }
}
</style>
