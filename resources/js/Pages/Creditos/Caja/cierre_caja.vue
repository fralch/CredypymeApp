<template>
  <layout ref="layout">
    <div
      class="slot_body slot-cierre-caja"
      slot="component-view"
      v-if="mi_caja != null && !transferencias_pendientes"
    >
      <div class="content" style="display: block">
        <div class="card">
          <headerClose :title="'CIERRE DE CAJA'"></headerClose>
          <!-- <div class="card-title">INFORMACIÓN PERSONAL</div> -->
          <div class="card-body card-block">
            <!-- ------------- -->

            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <a
                  class="nav-link tab-title active"
                  id="apertura-tab"
                  data-toggle="tab"
                  href="#apertura"
                  role="tab"
                  aria-controls="apertura"
                  aria-selected="true"
                  >DATOS DE APERTURA
                </a>
              </li>
              <li class="nav-item" role="presentation">
                <a
                  class="nav-link tab-title"
                  id="billeteo-tab"
                  data-toggle="tab"
                  href="#billeteo"
                  role="tab"
                  aria-controls="billeteo"
                  aria-selected="false"
                  >BILLETEO
                </a>
              </li>
              <li class="nav-item" role="presentation">
                <a
                  class="nav-link tab-title"
                  id="operaciones-tab"
                  data-toggle="tab"
                  href="#operaciones"
                  role="tab"
                  aria-controls="operaciones"
                  aria-selected="false"
                  :style="
                    billeteo != null && modo_billeteo != 'EDITAR'
                      ? 'pointer-events: auto;'
                      : 'pointer-events: none;'
                  "
                  >OPERACIONES
                </a>
              </li>
              <li class="nav-item" role="presentation">
                <a
                  class="nav-link tab-title"
                  id="cierre-tab"
                  data-toggle="tab"
                  href="#cierre"
                  role="tab"
                  aria-controls="cierre"
                  aria-selected="false"
                  :style="
                    billeteo != null &&
                    roundTo(total_billeteo, 2) ==
                      roundTo(total_ingresos - total_egresos, 2)
                      ? 'pointer-events: auto;'
                      : 'pointer-events: none;'
                  "
                  >CIERRE DE CAJA
                </a>
              </li>
            </ul>

            <div class="tab-content" id="myTabContent">
              <div
                class="tab-pane fade show active"
                id="apertura"
                role="tabpanel"
                aria-labelledby="apertura-tab"
              >
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label class="label-title">FECHA Y HORA</label>
                    <input
                      type="text"
                      class="form-control"
                      aria-label="Recipient's username"
                      aria-describedby="basic-addon2"
                      :value="JSON.parse(datos_caja.datos_apertura).fecha"
                      readonly
                    />
                  </div>
                  <div class="form-group col-md-6">
                    <label class="label-title">USUARIO</label>
                    <input
                      type="text"
                      class="form-control"
                      aria-label="Recipient's username"
                      aria-describedby="basic-addon2"
                      :value="mi_usuario.nombres"
                      readonly
                    />
                  </div>
                  <div class="form-group col-md-6 col-4">
                    <label class="label-title" for="text-input">EQUIPO</label>
                    <input
                      type="text"
                      class="form-control"
                      aria-label="Recipient's username"
                      aria-describedby="basic-addon2"
                      :value="
                        JSON.parse(datos_caja.datos_apertura).nombre_dispositivo
                      "
                      readonly
                    />
                  </div>
                  <div class="form-group col-md-6 col-8">
                    <label class="label-title">MONTO</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span
                          class="input-group-text"
                          :style="
                            windowWidth >= 900
                              ? 'width: 60px !important'
                              : 'width: 35px !important'
                          "
                          >S/</span
                        >
                      </div>
                      <input
                        type="number"
                        min="0"
                        class="form-control text-center"
                        style="
                          height: 45px;
                          font-size: 24px;
                          font-weight: bolder;
                          color: var(--colorAlto);
                        "
                        :value="
                          parseFloat(datos_caja.monto_apertura).toFixed(2)
                        "
                        @change="Redondear"
                        @focus="hidenav()"
                        @blur="shownav()"
                        readonly
                      />
                    </div>
                  </div>
                  <div class="form-group col-md-12">
                    <label class="label-title">NOTAS DE APERTURA</label>
                    <textarea
                      type="text"
                      rows="3"
                      class="form-control mayus text-row"
                      @focus="hidenav()"
                      @blur="shownav()"
                      v-model="datos_caja.comentario_apertura"
                      disabled
                    ></textarea>
                  </div>
                </div>
                <hr />
                <div class="form-group col-md text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="Avanzar('billeteo')"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-arrow-right"></i>
                    </span>
                    <span class="text">CONTINUAR</span>
                  </button>
                </div>
              </div>

              <div
                class="tab-pane fade"
                id="billeteo"
                role="tabpanel"
                aria-labelledby="billeteo-tab"
              >
                <label class="label-title">
                  El primer paso para cerrar su caja es realizar el billeteo,
                  ingrese en cada casilla la cantidad de monedas y billetes que
                  tiene.
                </label>
                <hr />
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(frmBilleteo.diez_centimos * 0.1).toFixed(
                              2
                            )
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(
                              frmBilleteo.veinte_centimos * 0.2
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(frmBilleteo.cinco_soles * 5).toFixed(2)
                          }}
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(frmBilleteo.diez_soles * 10).toFixed(2)
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(frmBilleteo.veinte_soles * 20).toFixed(2)
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(
                              frmBilleteo.cincuenta_soles * 50
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(frmBilleteo.cien_soles * 100).toFixed(2)
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
                        :disabled="billeteo != null && modo_billeteo == 'NUEVO'"
                      />

                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          style="font-size: 15px; width: 80px !important"
                        >
                          {{
                            parseFloat(
                              frmBilleteo.doscientos_soles * 200
                            ).toFixed(2)
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
                  <div class="form-group col-md-2 col-4">
                    <button
                      class="btn btn-cancel btn-icon-split"
                      @click="ImprimirBilleteo"
                      v-if="billeteo != null"
                      style="width: 130px !important"
                      :disabled="modo_billeteo == 'EDITAR'"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-print"></i>
                      </span>
                      <span class="text">IMPRIMIR </span>
                    </button>
                  </div>
                  <div class="form-group col-md-3 col-4">
                    <button
                      class="btn btn-action btn-icon-split"
                      @click="EditarBilleteo"
                      v-if="
                        billeteo != null &&
                        editar_billeteo == 1 &&
                        modo_billeteo == 'NUEVO'
                      "
                    >
                      <span class="icon text-white">
                        <i class="fas fa-edit"></i>
                      </span>
                      <span class="text">EDITAR</span>
                    </button>
                  </div>

                  <div class="form-group col-md-7 col-4 text-right">
                    <div class="btn-group" role="group">
                      <button
                        class="btn btn-action btn-icon-split"
                        id="btnGuardar"
                        @click="GuardarBilleteo"
                        v-if="billeteo == null || modo_billeteo == 'EDITAR'"
                      >
                        <span class="icon text-white">
                          <i class="fas fa-save"></i>
                        </span>
                        <span class="text">GUARDAR BILLETEO</span>
                      </button>
                      <button
                        class="btn btn-action btn-icon-split"
                        @click="Avanzar('operaciones')"
                        v-if="billeteo != null && modo_billeteo == 'NUEVO'"
                      >
                        <span class="icon text-white">
                          <i class="fas fa-arrow-right"></i>
                        </span>
                        <span class="text">CONTINUAR</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div
                class="tab-pane fade"
                id="operaciones"
                role="tabpanel"
                aria-labelledby="operaciones-tab"
              >
                <div class="form-inline" style="margin-bottom: 5px">
                  <label style="margin-right: 5px">Apertura </label>
                  <p class="h4">
                    S/
                    {{ roundTo(datos_caja.monto_apertura, 2) }}
                  </p>
                </div>
                <!-- --- table  -->
                <table class="table" id="tblOperaciones" width="100%">
                  <thead>
                    <tr>
                      <th>CONCEPTO</th>
                      <th>INGRESO</th>
                      <th>EGRESO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in lista_operaciones" :key="index">
                      <td align="right">
                        {{ item.concepto }}
                      </td>
                      <td align="right">
                        {{
                          item.ingresos == 0
                            ? "-"
                            : "S/ " + roundTo(item.ingresos, 2)
                        }}
                      </td>
                      <td align="right">
                        {{
                          item.egresos == 0
                            ? "-"
                            : "S/ " + roundTo(item.egresos, 2)
                        }}
                      </td>
                    </tr>
                  </tbody>
                </table>
                <!-- fin table  -->
                <div class="form-row">
                  <div class="col">
                    <div class="form-inline" style="margin-bottom: 5px">
                      <label style="margin-right: 5px">Ingresos </label>
                      <label style="margin-right: 5px; font-weight: bold"
                        >S/ {{ roundTo(total_ingresos, 2) }}
                      </label>
                    </div>
                    <div class="form-inline" style="margin-bottom: 5px">
                      <label style="margin-right: 5px">Egreso </label>
                      <label style="margin-right: 5px; font-weight: bold"
                        >S/ {{ roundTo(total_egresos, 2) }}
                      </label>
                    </div>
                    <div class="form-inline" style="margin-bottom: 5px">
                      <label style="margin-right: 5px">Saldo </label>
                      <label style="margin-right: 5px; font-weight: bold"
                        >S/
                        {{ roundTo(total_ingresos - total_egresos, 2) }}
                      </label>
                    </div>
                  </div>
                  <div class="col">
                    <div
                      class="form-inline"
                      style="margin-bottom: 5px"
                      v-if="
                        roundTo(total_billeteo, 2) !=
                        roundTo(total_ingresos - total_egresos, 2)
                      "
                    >
                      <label style="margin-right: 5px; color: #c0392b"
                        >Existe un
                        {{
                          parseFloat(
                            total_billeteo - (total_ingresos - total_egresos)
                          ) > 0
                            ? "sobrante"
                            : "faltante"
                        }}
                        de S/.
                        {{
                          roundTo(
                            total_billeteo - (total_ingresos - total_egresos),
                            2
                          )
                        }}</label
                      >
                    </div>
                    <div
                      class="form-inline"
                      style="margin-bottom: 5px"
                      v-if="
                        roundTo(total_billeteo, 2) !=
                        roundTo(total_ingresos - total_egresos, 2)
                      "
                    >
                      <button
                        type="button"
                        style="margin-left: -15px"
                        class="btn btn-link"
                        @click="Declarar()"
                      >
                        Declarar
                      </button>
                    </div>
                    <div
                      class="btn-group m-2"
                      role="group"
                      v-if="
                        archivos_declaracion.path_docx != null ||
                        archivos_declaracion.path_pdf != null
                      "
                    >
                      <button
                        class="btn btn-action btn-icon-split"
                        @click="GenerarArchivo('DOCX')"
                        v-if="archivos_declaracion.path_docx != null"
                      >
                        <span class="icon text-white">
                          <i class="fas fa-file-word"></i>
                        </span>
                        <span class="text">DESCARGAR</span>
                      </button>
                      <button
                        class="btn btn-cancel btn-icon-split"
                        @click="GenerarArchivo('PDF')"
                        v-if="archivos_declaracion.path_pdf != null"
                      >
                        <span class="icon text-white">
                          <i class="fas fa-print"></i>
                        </span>
                        <span class="text">IMPRIMIR</span>
                      </button>
                    </div>

                    <div class="form-inline" style="margin-bottom: 5px">
                      <label style="margin-right: 5px">Billeteo </label>
                      <label style="margin-right: 5px; font-weight: bold"
                        >S/
                        {{ roundTo(total_billeteo, 2) }}
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group col-md text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    v-if="
                      roundTo(total_billeteo, 2) ==
                      roundTo(total_ingresos - total_egresos, 2)
                    "
                    @click="Avanzar('cierre')"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-arrow-right"></i>
                    </span>
                    <span class="text">CONTINUAR</span>
                  </button>
                </div>
              </div>
              <div
                class="tab-pane fade"
                id="cierre"
                role="tabpanel"
                aria-labelledby="cierre-tab"
              >
                <div class="form-row">
                  <div class="form-group col-md-6 col-3">
                    <label class="label-title">FECHA:</label>
                    <input
                      type="text"
                      class="form-control"
                      :value="fecha_actual"
                      readonly
                    />
                  </div>
                  <div class="form-group col-md-6 col-9">
                    <label class="label-title">USUARIO:</label>
                    <input
                      type="text"
                      class="form-control"
                      :value="mi_usuario.nombres"
                      readonly
                    />
                  </div>
                  <div class="form-group col-md-6 col-4">
                    <label class="label-title">EQUIPO:</label>
                    <input
                      type="text"
                      class="form-control"
                      :value="$page.props.user_session.dispositivo.nombre"
                      readonly
                    />
                  </div>

                  <div class="form-group col-md-6 col-4">
                    <label class="label-title" v-if="windowWidth >= 900"
                      >MONTO DE APERTURA (S/):</label
                    >
                    <label class="label-title" v-if="windowWidth < 900"
                      >MON. APE. (S/):</label
                    >

                    <input
                      type="text"
                      class="form-control"
                      :value="roundTo(datos_caja.monto_apertura, 2)"
                      readonly
                    />
                  </div>
                  <div class="form-group col-md-6 col-4">
                    <label class="label-title" v-if="windowWidth >= 900"
                      >MONTO DE CIERRE (S/):</label
                    >
                    <label class="label-title" v-if="windowWidth < 900"
                      >MON. CIE. (S/):</label
                    >

                    <input
                      type="text"
                      class="form-control"
                      :value="roundTo(total_billeteo, 2)"
                      readonly
                    />
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-8">
                    <label class="label-title">NOTAS DE CIERRE: </label>

                    <textarea
                      class="form-control mayus text-row"
                      rows="3"
                      v-model="frmCierre.comentario"
                      maxlength="500"
                      autocomplete="off"
                      spellcheck="false"
                      :readonly="print_boton"
                    >
                    </textarea>
                  </div>
                  <div class="form-group col-md-4">
                    <label class="label-title">El saldo se entregara a: </label>

                    <select
                      class="form-control center"
                      v-model="frmCierre.cuenta_id"
                      id="receptor_cierre_caja"
                      :disabled="print_boton"
                    >
                      <option value="0" selected disabled>Seleccione...</option>
                      <option
                        v-for="(item, index) in cuentas"
                        :key="index"
                        :value="item.id"
                      >
                        {{ item.usuario }}
                      </option>
                    </select>
                  </div>
                </div>
                <hr />

                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="CerrarCaja"
                    v-if="!print_boton"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-check"></i>
                    </span>
                    <span class="text">CERRAR CAJA</span>
                  </button>
                  <button
                    class="btn btn-cancel btn-icon-split"
                    @click="ImprimirCierre"
                    v-if="print_boton"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-print"></i>
                    </span>
                    <span class="text">IMPRIMIR</span>
                  </button>
                </div>
              </div>
            </div>
            <!-- ----------------- -->
          </div>
        </div>

        <!-- ---------------------- -->
        <div id="mdlDeclarar" class="modal">
          <!-- Modal content -->
          <div class="modal-content w-25 mdlDeclarar">
            <div class="content" style="display: block">
              <div class="card">
                <headerCloseModal
                  :titulo_modal="'DECLARACIÓN'"
                  :nombre_modal="'mdlDeclarar'"
                >
                </headerCloseModal>

                <div class="card-body card-block">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label class="form-control-label label-title"
                        >DESCRIPCIÓN</label
                      >

                      <textarea
                        class="form-control mayus text-row"
                        rows="3"
                        type="text"
                        maxlength="300"
                        v-model="frmDeclaracion.descripcion"
                      ></textarea>
                    </div>
                  </div>
                  <hr />
                  <div class="text-right">
                    <button
                      class="btn btn-action btn-icon-split"
                      @click="GuardarDeclaracion"
                    >
                      <span class="icon text-white">
                        <i class="far fa-paper-plane"></i>
                      </span>
                      <span class="text">ACEPTAR</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <rptCierreCaja ref="rptCierreCaja"> </rptCierreCaja>
      <!-- <voucher
				ref="voucher"
				:concepto="'TRANSFERENCIA SALDO A CUENTA'"
				:fecha_hora_actual="fecha_actual"
				id="vchCierreCaja"
			>
				<div slot="voucher-content">
					<div style="width: 50%; display: inline-block">
						<p style="padding: 0; margin: 0"><b>EMISOR </b></p>
						<p style="padding: 0; margin: 0"><b>RECEPTOR </b></p>
					</div>
					<div style="width: 50%; float: right; display: inline-block">
						<p style="padding: 0; margin: 0">{{ mi_usuario.usuario }}</p>
						<p style="padding: 0; margin: 0">{{ usuario_receptor_cierre }}</p>
					</div>
					<table
						width="100%"
						id="tblCierreCaja"
						style="margin: 20px 0 0 0 !important"
					>
						<tbody>
							<tr class="border-top bg-gray">
								<td class="bolder" colspan="2">DESCRIPCIÓN</td>
							</tr>
							<tr class="border-top">
								<td class="bolder" colspan="2">SALDO DE CIERRE DE CAJA</td>
							</tr>

							<tr class="border-top border-bottom bg-gray">
								<td align="right" colspan="2">
									S/ {{ roundTo(total_billeteo, 2) }}
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</voucher> -->
    </div>
  </layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
import rptCierreCaja from "@/Pages/Creditos/Caja/Reports/rptCierreCaja.vue";

export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,
    rptCierreCaja,
  },
  props: {
    agencia_id: Number,
    datos_caja: Object,
    transferencias_pendientes: Boolean,
    cuentas: Array,
    billeteo: Object,
    editar_billeteo: Number,
  },
  data() {
    return {
      windowWidth: window.innerWidth,

      submited: false,
      print_boton: false,
      fecha_actual: null,
      fecha_cierre: null,
      fecha_cierre_real: null,

      lista_operaciones: [],
      total_egresos: 0,
      total_ingresos: 0,
      modo_billeteo: "NUEVO",

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
      frmDeclaracion: {
        tipo: null,
        monto: 0,
        descripcion: null,
      },

      archivos_declaracion: {
        path_docx: null,
        path_pdf: null,
      },
      frmCierre: {
        comentario: null,
        cuenta_id: 0,
      },
    };
  },
  watch: {
    lista_operaciones() {
      $("#tblOperaciones").DataTable().destroy();
      this.TablaOperaciones();
    },
  },
  computed: {
    mi_caja() {
      return this.$inertia.page.props.creditos_datos.datos_caja;
    },
    mi_cuenta() {
      return this.$inertia.page.props.creditos_datos.datos_cuenta;
    },
    mi_usuario() {
      return this.$inertia.page.props.user_session;
    },
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
    total_operaciones() {
      return parseFloat(this.total_ingresos - this.total_egresos);
    },
    resultado_cuadre() {
      if (this.total_operaciones > this.total_billeteo) {
        return "FALTANTE";
      } else if (this.total_operaciones < this.total_billeteo) {
        return "SOBRANTE";
      } else {
        return "CUADRA";
      }
    },
  },

  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
    if (this.mi_caja == null) {
      Swal.fire({
        icon: "error",
        title: "¡Ups!",
        text: "Primero debe aperturar CAJA",
        confirmButtonText:
          '<i class="fas fa-check" style="color:white;"></i>   Ok',
        confirmButtonColor: "var(--colorAlto)",
        allowOutsideClick: true,
      });

      return this.$inertia.get(route("cre.index"));
    } else {
      if (this.transferencias_pendientes) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Tiene transferencias pendientes",
          confirmButtonText:
            '<i class="fas fa-check" style="color:white;"></i>   Ok',
          confirmButtonColor: "var(--colorAlto)",
          allowOutsideClick: true,
        });
        return this.$inertia.get(route("cre.index"));
      } else {
        this.fecha_actual = this.$page.props.application.data_local.filter(
          (item) => item.descripcion == "FECHA_CREDITOS"
        )[0].valor_fecha;

        if (this.billeteo != null) {
          this.frmBilleteo = this.billeteo;
        }

        this.ListarOperaciones();
      }
    }
  },

  methods: {
    hidenav() {
      this.$refs.layout.hide_nav();
    },
    shownav() {
      this.$refs.layout.show_nav();
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

    ListarOperaciones() {
      let self = this;

      axios
        .post(
          route("caj.operaciones_caja", {
            caja_id: self.datos_caja.id,
            agencia_id: self.agencia_id,
          })
        )
        .then((response) => {
          self.lista_operaciones = response.data;
        })
        .then(() => {
          self.total_egresos = self.lista_operaciones.reduce(
            (t, { egresos }) => t + parseFloat(egresos),
            0
          );
          self.total_egresos = Math.round(self.total_egresos * 100) / 100;
          self.total_ingresos = self.lista_operaciones.reduce(
            (t, { ingresos }) => t + parseFloat(ingresos),
            0
          );
          self.total_ingresos = Math.round(self.total_ingresos * 100) / 100;
        });
    },

    TablaOperaciones() {
      this.$nextTick(() => {
        var table = $("#tblOperaciones").DataTable({
          scrollY: "300px",
          scrollX: true,
          scrollCollapse: true,
          paging: false,
          ordering: false,
          fixedHeader: true,
          info: true,
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
      });
    },

    Avanzar(pagina) {
      document.getElementById(pagina + "-tab").click();
    },
    GuardarBilleteo() {
      let self = this;

      Swal.fire({
        icon: "question",
        title:
          "En total tienes S/ " + parseFloat(this.total_billeteo).toFixed(2),
        text: "¿Es correcto?",
        confirmButtonText:
          '<i class="fas fa-check" style="color:white;"></i>   Si',
        confirmButtonColor: "var(--colorAlto)",
        showCancelButton: true,
        cancelButtonText: '<i class="fas fa-times"></i>   No',
        cancelButtonColor: "var(--plomoOscuroEmpresarial)",
        allowOutsideClick: false,
        preConfirm: (result) => {
          if (result == true) {
            Swal.fire({
              icon: "question",
              text: "¿Desea guardar los cambios?",
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
                data.append("agencia_id", self.agencia_id);
                data.append("caja_id", self.mi_caja.id);
                data.append("modo_billeteo", self.modo_billeteo);
                data.append("frmBilleteo", JSON.stringify(self.frmBilleteo));
                self.$inertia.post(route("caj.guardar_billeteo"), data, {
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
                      allowOutsideClick: false,
                      preConfirm: (result) => {
                        self.modo_billeteo = "NUEVO";
                      },
                    });
                  },
                });
              }
            });
          }
        },
      });
    },

    EditarBilleteo() {
      let self = this;

      self.modo_billeteo = "EDITAR";
    },
    Declarar() {
      $("#mdlDeclarar").css("display", "block");
    },
    GuardarDeclaracion() {
      let self = this;
      if (
        this.frmDeclaracion.descripcion == null ||
        this.frmDeclaracion.descripcion == ""
      ) {
        Swal.fire({
          icon: "warning",
          title: "¡Ups!",
          text: "Ingrese la descripción.",
          allowOutsideClick: true,
        });
        return false;
      }

      this.frmDeclaracion.tipo = this.resultado_cuadre;
      this.frmDeclaracion.monto = this.total_billeteo - this.total_operaciones;

      Swal.fire({
        icon: "question",
        title: "¿Desea continuar?",
        text: "¿Es correcto?",
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
            title: "GENERANDO ARCHIVOS",
            text: "Espere porfavor...",
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
              let data = new FormData();
              data.append("agencia_id", this.agencia_id);
              data.append("caja_id", this.mi_caja.id);
              data.append("usuario_id", this.mi_usuario.usuario_dni);
              data.append(
                "frmDeclaracion",
                JSON.stringify(this.frmDeclaracion)
              );
              // this.$inertia.post(route("caj.cierre_caja.declarar"), data);
              // return false
              axios
                .post(route("caj.cierre_caja.declarar"), data)
                .then(function (response) {
                  self.archivos_declaracion = response.data;
                  if (self.archivos_declaracion.path_pdf != null) {
                    self.GenerarArchivo("PDF");
                  }
                  $("#mdlDeclarar").css("display", "none");
                  self.ListarOperaciones();
                })
                .finally(() => {
                  return Swal.fire({
                    icon: "success",
                    title: "¡LISTO!",
                    timer: 1200,
                    showConfirmButton: false,
                  });
                });
            },
          });
        } else {
          return false;
        }
      });
    },

    GenerarArchivo(tipo) {
      let origin = window.location.origin;
      if (tipo == "DOCX") {
        let path_docx = this.archivos_declaracion.path_docx;

        const link = document.createElement("a");
        link.href = origin + path_docx;
        link.download = "rptDeclaracionJurada.docx";
        link.click();
      } else if (tipo == "PDF") {
        let path_pdf = this.archivos_declaracion.path_pdf;

        // Crear un IFrame.
        let iframe = document.createElement("iframe");
        // Ocultar el IFrame.
        iframe.style.display = "none";
        // Definir el source.
        iframe.src = origin + path_pdf;
        // Añadir el IFrame a una página web.
        document.body.appendChild(iframe);
        iframe.contentWindow.focus();
        iframe.contentWindow.print(); // Imprimir.
      }
    },

    CerrarCaja() {
      let self = this;
      if (this.frmCierre.cuenta_id == 0) {
        Swal.fire({
          icon: "warning",
          title: "¡Ups!",
          text: "Seleccione la cuenta de entrega",
          allowOutsideClick: true,
        });
        return false;
      }

      this.frmCierre.total_ingresos = this.total_ingresos;
      this.frmCierre.total_egresos = this.total_egresos;

      Swal.fire({
        icon: "question",
        title: "¿Desea continuar?",
        text: "¿Es correcto?",
        confirmButtonText:
          '<i class="fas fa-check" style="color:white;"></i>   Si',
        confirmButtonColor: "var(--colorAlto)",
        showCancelButton: true,
        cancelButtonText: '<i class="fas fa-times"></i>   No',
        cancelButtonColor: "var(--plomoOscuroEmpresarial)",
        allowOutsideClick: false,
        preConfirm: (result) => {
          if (result == true) {
            let data = new FormData();
            data.append("agencia_id", this.agencia_id);
            data.append("caja_id", this.mi_caja.id);

            data.append("frmCierre", JSON.stringify(this.frmCierre));

            Swal.fire({
              title: "GUARDANDO",
              text: "Espere porfavor...",
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
                axios
                  .post(route("caj.cierre_caja.cerrar"), data)
                  .then(function (response) {
                    if (response.data == 0) {
                      return Swal.fire({
                        icon: "info",
                        title: "¡Ups!",
                        text: "No se pudo guardar el cierre",
                        allowOutsideClick: true,
                      });
                    } else {
                      self.fecha_cierre = JSON.parse(
                        response.data.datos_cierre
                      ).fecha;
                      self.fecha_cierre_real = response.data.updated_at;
                      self.print_boton = true;
                    }
                  })
                  .finally(() => {
                    return Swal.fire({
                      icon: "success",
                      title: "¡LISTO!",
                      timer: 1200,
                      showConfirmButton: false,
                    });
                  });
              },
            });
          } else {
            return false;
          }
        },
      });
    },
    ImprimirBilleteo() {
      let data = new FormData();
      data.append("modo", "billeteo");
      data.append("billeteo", JSON.stringify(this.billeteo));

      // this.$inertia.post(route("caj.cierre_caja.imprimir"), data);

      Swal.fire({
        title: "GENERANDO ARCHIVO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          axios
            .post(route("caj.cierre_caja.imprimir"), data)
            .then(function (response) {
              let origin = window.location.origin;

              let path_pdf = response.data.path_pdf;

              // Crear un IFrame.
              let iframe = document.createElement("iframe");
              // Ocultar el IFrame.
              iframe.style.display = "none";
              // Definir el source.
              iframe.src = origin + path_pdf;
              // Añadir el IFrame a una página web.
              document.body.appendChild(iframe);
              iframe.contentWindow.focus();
              iframe.contentWindow.print(); // Imprimir.
            })
            .finally(() => {
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

    ImprimirCierre() {
      let datos_cierre = {
        operaciones: this.lista_operaciones,
        efectivo_caja: this.roundTo(
          this.total_ingresos - this.total_egresos,
          2
        ),
        total_ingresos: this.total_ingresos,
        total_egresos: this.total_egresos,
        caja_usuario: this.mi_usuario.nombres.toUpperCase(),
        fecha_apertura_sistema: JSON.parse(this.datos_caja.datos_apertura)
          .fecha,
        fecha_cierre_sistema: this.fecha_cierre,
        fecha_apertura_real: this.datos_caja.created_at,
        fecha_cierre_real: this.fecha_cierre_real,
      };

      let data = new FormData();
      data.append("modo", "cierre");
      data.append("datos_cierre", JSON.stringify(datos_cierre));

      // this.$inertia.post(route("caj.cierre_caja.imprimir"), data);
      // return false;
      Swal.fire({
        title: "GENERANDO ARCHIVO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          axios
            .post(route("caj.cierre_caja.imprimir"), data)
            .then(function (response) {
              let origin = window.location.origin;

              let path_pdf = response.data.path_pdf;

              // Crear un IFrame.
              let iframe = document.createElement("iframe");
              // Ocultar el IFrame.
              iframe.style.display = "none";
              // Definir el source.
              iframe.src = origin + path_pdf;
              // Añadir el IFrame a una página web.
              document.body.appendChild(iframe);
              iframe.contentWindow.focus();
              iframe.contentWindow.print(); // Imprimir.
            })
            .finally(() => {
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

<style lang="css">
.slot-cierre-caja {
  width: 50% !important;
  margin-left: 25% !important;
  margin-top: 845 !important;
}

.mdlDeclarar {
  margin-top: 15% !important;
}

@media only screen and (max-width: 900px) {
  .slot-cierre-caja {
    width: 96% !important;
    margin-left: 2% !important;
  }
}
</style>



