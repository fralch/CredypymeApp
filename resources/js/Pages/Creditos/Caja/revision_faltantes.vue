<template>
  <layout ref="layout">
    <div class="slot_body mx-auto" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose
            :title="'REVISIÓN DE FALTANTES Y SOBRANTES'"
          ></headerClose>

          <div class="card-body card-block">
            <div class="form-row">
              <fieldset class="form-group col-md-10">
                <legend>
                  <label class="label-title">Filtros de búsqueda</label>
                </legend>
                <div class="row">
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title"
                        >AGENCIA</span
                      >
                    </div>
                    <select
                      class="form-control center"
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
                      class="form-control center"
                      v-model="fecha_desde"
                      style="font-size: 15px"
                    />
                  </div>
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">HASTA</span>
                    </div>
                    <input
                      type="date"
                      class="form-control center"
                      v-model="fecha_hasta"
                      style="font-size: 15px"
                    />
                  </div>
                </div>
              </fieldset>
              <div
                class="col-md-1 ml-3"
                :style="
                  windowWidth >= 900
                    ? 'text-align: none'
                    : 'text-align: center !important'
                "
              >
                <button
                  class="btn btn-action btn-icon-split mt-3"
                  title="Buscar"
                  @click="Buscar"
                >
                  <span
                    class="icon text-white"
                    :style="
                      windowWidth >= 900
                        ? 'font-size: 25px !important'
                        : 'font-size: 15px !important'
                    "
                  >
                    <i class="fas fa-search"></i>
                  </span>
                </button>
              </div>
              <div class="input-group col-md-3">
                <div class="input-group-prepend">
                  <span class="input-group-text prepend-title">TIPO</span>
                </div>
                <select
                  class="form-control center"
                  id="slcTipo"
                  data-index="2"
                  :disabled="revision_faltantes.length == 0"
                >
                  <option value="0">TODOS</option>
                  <option :value="'FALTANTE'">FALTANTE</option>
                  <option :value="'SOBRANTE'">SOBRANTE</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-title">LISTA DE RESULTADOS</div>
          <div class="card-body card-block">
            <div>
              <table
                class="table"
                id="tblFaltantes"
                style="width: 100% !important"
              >
                <thead>
                  <tr>
                    <th>N°</th>
                    <th style="max-width: 100px">ACCIÓN</th>
                    <th>TIPO</th>
                    <th style="min-width: 90px">MONTO</th>

                    <th style="min-width: 300px">DESCRIPCIÓN</th>
                    <th style="min-width: 90px">FECHA_REGISTRO</th>
                    <th>CAJA</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in revision_faltantes"
                    :key="index"
                    class="table"
                    :class="index % 2 == 0 ? 'verde-claro' : ''"
                  >
                    <td align="center">
                      {{ index + 1 }}
                    </td>
                    <td class="table-bordered" align="center">
                      <button
                        class="btn btn-primary btn-sm btn-icon-split"
                        type="button"
                        title="Regularizar"
                        @click="Regularizar(item)"
                        v-if="item.regularizado == 0"
                      >
                        <span class="text label-title text-white"
                          >REGULARIZAR</span
                        >
                      </button>

                      <span
                        align="center"
                        style="font-size: 18px !important"
                        v-if="item.regularizado == 1"
                      >
                        <i class="fas fa-check"></i>
                      </span>
                    </td>

                    <td align="center">
                      {{ item.tipo == "I" ? "SOBRANTE" : "FALTANTE" }}
                    </td>
                    <td align="right">S/. {{ roundTo(item.monto, 2) }}</td>

                    <td align="left">
                      {{ item.concepto }}
                    </td>
                    <td align="center">
                      {{ item.fecha_registro }}
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
                  title="Exportar"
                  @click="Exportar"
                  :disabled="revision_faltantes.length == 0"
                >
                  <span class="icon text-white">
                    <i class="fas fa-file-excel"></i>
                  </span>
                  <span class="text">EXPORTAR</span>
                </button>
              </div>
            </div>
          </div>

          <div id="mdlFaltanteSobrante" class="modal">
            <div class="modal-content w-40 mdlFaltanteSobrante">
              <div class="content" style="display: block">
                <div class="card">
                  <headerCloseModal
                    ref="headerCloseModal"
                    :titulo_modal="titulo_modal"
                    :nombre_modal="'mdlFaltanteSobrante'"
                  >
                  </headerCloseModal>
                  <div class="card-body card-block">
                    <div class="form-row">
                      <div class="input-group col-md-4">
                        <div class="input-group-prepend">
                          <span class="input-group-text prepend-title"
                            >CAJA</span
                          >
                        </div>
                        <input
                          type="text"
                          class="form-control input-information center"
                          :value="frmDatosRevision.caja"
                          disabled
                        />
                      </div>
                      <div class="input-group col-md-4">
                        <div class="input-group-prepend">
                          <span class="input-group-text prepend-title"
                            >MONTO</span
                          >
                        </div>
                        <input
                          type="text"
                          class="form-control input-information center"
                          :value="frmDatosRevision.monto"
                          disabled
                        />
                      </div>
                      <div class="input-group col-md-4">
                        <div class="input-group-prepend">
                          <span class="input-group-text prepend-title"
                            >FECHA</span
                          >
                        </div>
                        <input
                          type="text"
                          class="form-control input-information center"
                          :value="frmDatosRevision.fecha"
                          disabled
                        />
                      </div>
                    </div>
                    <div class="form-group FormGroup col-md-12">
                      <label
                        for="txtConcepto"
                        class="form-control-label label-title"
                        >CONCEPTO</label
                      >
                      <textarea
                        class="form-control text-row mayus"
                        :class="[
                          submited
                            ? $v.frmDatosRegularizacion.concepto.$invalid
                              ? 'is-invalid'
                              : 'is-valid'
                            : '',
                        ]"
                        maxlength="350"
                        rows="3"
                        v-model="frmDatosRegularizacion.concepto"
                      ></textarea>
                    </div>
                    <div class="input-group FormGroup col-md-12">
                      <label
                        for="txtComprobante"
                        class="form-control-label label-title"
                        >COMPROBANTE</label
                      >
                      <div class="form-group mt-1" id="pf_contenido_1">
                        <label
                          for="foto_1"
                          class="subir"
                          v-if="frmDatosRegularizacion.documento == null"
                        >
                          <i
                            class="fas fa-plus-circle foto-icon_1"
                            style="font-size: 180px"
                          ></i>
                        </label>
                        <input
                          id="foto_1"
                          type="file"
                          accept="image/*"
                          style="display: none"
                          @change="AgregarImagen"
                          v-if="frmDatosRegularizacion.documento == null"
                        />
                        <img class="d-block w-100" id="img_1" />
                        <button
                          class="btn btn-danger btn-icon-split delete_1"
                          title="Quitar FOTO"
                          style="float: right"
                          @click="QuitarFoto('foto_1')"
                          v-if="frmDatosRegularizacion.documento != null"
                        >
                          <span class="icon text-white">
                            <i class="fas fa-trash-alt"></i>
                          </span>
                        </button>
                      </div>
                    </div>

                    <hr />

                    <div class="text-right">
                      <button
                        class="btn btn-action btn-icon-split"
                        title="Registrar"
                        @click="Registrar"
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

  props: {},

  data() {
    return {
      submited: false,
      tipo_busqueda: 0,
      agencias_permitidas: [],
      agencia_seleccionada: 0,
      agencia_seleccionada_real: 0,
      windowWidth: window.innerWidth,

      titulo_modal: null,

      revision_faltantes: [],
      fecha_desde: null,
      fecha_hasta: null,

      frmDatosRevision: {
        caja: 0,
        monto: 0,
        fecha: null,
      },

      frmDatosRegularizacion: {
        id: 0,
        tipo: null,
        agencia_id: 0,
        usuario_id: 0,
        caja_id: 0,
        monto: null,
        concepto: null,
        documento: null,
      },
    };
  },

  validations: {
    frmDatosRegularizacion: {
      concepto: { required },
      documento: { required },
    },
  },

  mounted() {
    this.ListarAgenciasPermitidas();
    this.TablaFaltantes();

    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;

      if (this.frmDatosRegularizacion.documento != null) {
        this.ReajustarImagen();
      }
    });
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
    },

    revision_faltantes() {
      $("#tblFaltantes").DataTable().destroy();
      this.TablaFaltantes();
    },
  },

  computed: {
    mi_caja() {
      return this.$inertia.page.props.creditos_datos.datos_caja;
    },
  },

  methods: {
    Regularizar(item) {
      this.submited = false;

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
        return false;
      }

      if (this.frmDatosRegularizacion.documento != null) {
        let img = $("#pf_contenido_1 img");
        img.remove();

        this.frmDatosRegularizacion.documento = null;
      }

      this.frmDatosRevision.caja = item.usuario;
      this.frmDatosRevision.monto = "S/ " + this.roundTo(item.monto, 2);
      this.frmDatosRevision.fecha = item.fecha_registro;

      this.registro_regularizacion = item;

      this.frmDatosRegularizacion.concepto = "";

      if (item.tipo == "I") {
        this.titulo_modal = "REGULARIZAR SOBRANTE";
      } else if (item.tipo == "E") {
        this.titulo_modal = "REGULARIZAR FALTANTE";
      }

      this.frmDatosRegularizacion.tipo = item.tipo;
      this.frmDatosRegularizacion.agencia_id = item.agencia_id_usu;
      this.frmDatosRegularizacion.usuario_id = item.usuario_id;
      this.frmDatosRegularizacion.caja_id = this.mi_caja.id;
      this.frmDatosRegularizacion.monto = item.monto;
      this.frmDatosRegularizacion.id = item.id;

      $("#mdlFaltanteSobrante").css("display", "block");
    },

    AgregarImagen(e) {
      let previo = $("#pf_contenido_1 img");

      previo.remove();

      this.frmDatosRegularizacion.documento = e.target.files[0];

      this.ReajustarImagen();
    },
    ReajustarImagen() {
      let self = this;
      // console.log(self.windowWidth);

      let previo = $("#pf_contenido_1 img");

      previo.remove();

      var reader = new FileReader();
      reader.readAsDataURL(this.frmDatosRegularizacion.documento); // leemos el archivo subido y se lo pasamos a nuestro fileReader
      reader.onload = function () {
        let preview = document.getElementById("pf_contenido_1"),
          image = document.createElement("img");

        image.src = reader.result;
        image.id = "img_1";
        image.style.height = "220px";

        var newWidth = self.windowWidth * 0.377;

        image.style.width = newWidth + "px";
        image.style.border = "2px solid #000000";
        preview.append(image);
      };
    },

    QuitarFoto(numero_foto) {
      let self = this;
      Swal.fire({
        icon: "question",
        text: "¿Desea eliminar esta foto?",
        confirmButtonText:
          '<i class="fas fa-check" style="color:white;"></i>   Si',
        confirmButtonColor: "var(--colorAlto)",
        showCancelButton: true,
        cancelButtonText: '<i class="fas fa-times"></i>   No',
        cancelButtonColor: "var(--plomoOscuroEmpresarial)",
        allowOutsideClick: false,
      }).then((result) => {
        if (result.isConfirmed) {
          self.frmDatosRegularizacion.documento = null;

          let indice = numero_foto.substr(5, 1);
          let img = $("#pf_contenido_" + indice + " img");
          img.remove();
        }
      });
    },

    Registrar() {
      let self = this;

      self.submited = true;

      if (this.frmDatosRegularizacion.tipo == "E") {
        var msg = "¿DESEA REGULARIZAR ESTE FALTANTE?";
      } else {
        var msg = "¿DESEA REGULARIZAR ESTE SOBRANTE?";
      }

      if (self.$v.frmDatosRegularizacion.$invalid) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "El campo del concepto y/o comprobante se encuentra vacío, por favor verifique.",
        });
        return false;
      } else {
        Swal.fire({
          icon: "question",
          text: msg,
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
              title: "REGISTRANDO",
              showConfirmButton: false,
              allowOutsideClick: false,
              willOpen: async () => {
                Swal.showLoading();

                let data = new FormData();

                data.append(
                  "frmDatosRegularizacion",
                  JSON.stringify(self.frmDatosRegularizacion)
                );

                data.append("documento", self.frmDatosRegularizacion.documento);

                // this.$inertia.post(
                // 							route("cre.caj.revision_faltantes.guardar"),
                // 							data,
                // )
                // return false

                return await axios
                  .post(route("cre.caj.revision_faltantes.guardar"), data)
                  .then((response) => {
                    $("#mdlFaltanteSobrante").css("display", "none");
                    self.ListarRevisiones();

                    return Swal.fire({
                      icon: "success",
                      title: "¡ÉXITO!",
                      timer: 1200,
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
      }
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

    Buscar() {
      let self = this;

      self.submited = false;

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          self.ListarRevisiones();
        },
      });
    },

    ListarRevisiones() {
      let self = this;

      let data = new FormData();

      data.append("agencia_id", self.agencia_seleccionada);
      data.append("fecha_desde", self.fecha_desde);
      data.append("fecha_hasta", self.fecha_hasta);

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          axios
            .post(route("cre.caj.revision_faltantes.buscar"), data)
            .then(function (response) {
              if (response.data.length == 0) {
                self.revision_faltantes = [];
                if (self.submited == false) {
                  return Swal.fire({
                    icon: "info",
                    title: "¡Ups!",
                    text: "No se encontraron datos",
                    allowOutsideClick: true,
                  });
                }
              } else {
                self.revision_faltantes = response.data;

                if (self.submited == false) {
                  return Swal.fire({
                    icon: "success",
                    title: "¡Listo!",
                    timer: 1200,
                    showConfirmButton: false,
                  });
                }
              }
            });

          // self.$inertia.post(route("cre.caj.revision_faltantes.buscar",data));
        },
      });
    },

    async FechaActual() {
      if (this.agencia_seleccionada == 0) {
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

    Exportar() {
      let data = new FormData();

      data.append("agencia_id", this.agencia_seleccionada);
      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("datos_tabla", JSON.stringify(this.revision_faltantes));

      // 	  this.$inertia.post(route("cre.caj.revision_faltantes.exportar"), data);
      //   return false;
      Swal.fire({
        title: "EXPORTANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          axios
            .post(route("cre.caj.revision_faltantes.exportar"), data)
            .then(function (response) {
              let origin = window.location.origin;

              let path_xlsx = response.data.path_xlsx;

              const link = document.createElement("a");
              link.href = origin + path_xlsx;
              link.download = "rptFaltantesSobrantes.xlsx";
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

    TablaFaltantes() {
      this.$nextTick(() => {
        var table = $("#tblFaltantes").DataTable({
          scrollY: "350px",
          scrollX: true,
          fixedColumns: {
            leftColumns: 0,
          },
          scrollCollapse: true,
          paging: false,
          ordering: false,
          fixedHeader: true,
          info: false,

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

        $("#slcTipo").change(function () {
          if (this.value == 0) {
            table.column($(this).data("index")).search("").draw();
          } else {
            table.column($(this).data("index")).search(this.value).draw();
          }
        });
      });
    },

    ListarAgenciasPermitidas() {
      this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
        "CREDITOS_CAJA/REVISION_FALTANTES"
      );
    },
  },
};
</script>

<style lang="css">
.slot_body {
  width: 50% !important;
}

@media (max-width: 1700px) {
  .slot_body {
    width: 63% !important;
  }
}
.mdlFaltanteSobrante {
  margin-top: 2%;
}
#pf_contenido_1 {
  height: 220px;
  width: 720px;
}
.delete_1 {
  position: absolute;
  bottom: 5px;
  z-index: 1000;
}
.FormGroup {
  padding-left: 0;
  padding-right: 0;
}
.foto-icon_1 {
  margin-top: 3.5%;
}

@media (max-width: 1350px) {
  .slot_body {
    width: 80% !important;
  }

  .mdlFaltanteSobrante {
    margin-top: 2%;
  }
  .FormGroup {
    padding-left: 0;
    padding-right: 0;
  }
  #pf_contenido_1 {
    height: 220px;
    width: 720px;
  }
  .delete_1 {
    position: absolute;
    bottom: 5px;
    z-index: 1000;
  }

  .foto-icon_1 {
    margin-top: 3.5%;
  }
}

@media (max-width: 1130px) {
  .slot_body {
    width: 99% !important;
    margin-left: 0.5% !important;
  }
  .mdlFaltanteSobrante {
    margin-top: 2%;
  }

  #pf_contenido_1 {
    height: 220px;
    width: 720px;
  }
  .delete_1 {
    position: absolute;
    bottom: 5px;
    z-index: 1000;
  }
  .FormGroup {
    padding-left: 0;
    padding-right: 0;
  }
  .foto-icon_1 {
    margin-top: 3.5%;
  }
}
</style>

