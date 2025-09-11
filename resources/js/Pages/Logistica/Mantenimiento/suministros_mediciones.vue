<template>
  <layout ref="layout">
    <div class="slot_body slot-suministros-mediciones" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose :title="'MEDICIONES DE SUMINISTROS'"></headerClose>
          <div class="card-title">LISTA DE RESULTADOS</div>
          <div class="card-body card-block">
            <div class="input-group row col-md-7 col-7" style="float: left">
              <div class="input-group-prepend">
                <span class="input-group-text"
                  ><i class="fas fa-search"></i
                ></span>
              </div>
              <input
                class="form-control mayus"
                type="text"
                id="inpBuscar"
                autocomplete="off"
                spellcheck="false"
                @focus="hidenav()"
                @blur="shownav()"
              />
            </div>
            <div class="row col-md-3 col-2 ml-1" style="float: left">
              <button
                class="btn btn-action btn-icon-split"
                @click="Nuevo"
                title="Nueva MEDIDA"
              >
                <span class="icon text-white">
                  <i class="fas fa-plus"></i>
                </span>
                <!-- <span class="text">Nuevo</span> -->
              </button>
            </div>

            <table class="table table-hover" id="tblMediciones" width="100%">
              <thead>
                <tr>
                  <th style="max-width: 75px !important">EDITAR</th>
                  <th style="min-width: 100px !important">MEDIDA</th>
                  <th style="min-width: 50px !important">ESCALA</th>
                  <th style="min-width: 50px !important">HABILITADO</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in mediciones" :key="index">
                  <td class="table-bordered" align="center" width="75px">
                    <button
                      class="btn btn-action btn-icon-split"
                      @click="Editar(item)"
                      title="Editar MEDIDA"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-edit"></i>
                      </span>
                    </button>
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.medicion }}
                  </td>
                  <td class="table-bordered" align="center">
                    {{ item.escala }}
                  </td>

                  <td class="table-bordered" align="center">
                    {{ item.habilitado == 1 ? "SI" : "NO" }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- The Modal -->
      <div id="mdlDatosMedicion" class="modal modal-right">
        <!-- Modal content -->
        <div class="modal-content w-25 mdlDatosMedicion">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="titulo_modal"
                :nombre_modal="'mdlDatosMedicion'"
              >
              </headerCloseModal>

              <div class="card-body card-block">
                <form autocomplete="off" @submit.prevent="Guardar">
                  <div class="form-row">
                    <div class="form-group col-md-7 col-7">
                      <label class="label-title">MEDIDA</label>
                      <span
                        v-if="submited && !$v.mdlDatosMedicion.medida.required"
                        class="span-error-message"
                      >
                        *
                      </span>
                      <input
                        type="text"
                        class="form-control mayus"
                        maxlength="50"
                        v-model="mdlDatosMedicion.medida"
                      />
                    </div>
                    <div class="form-group col-md-5 col-5">
                      <label class="label-title">ESCALA</label>
                      <span
                        v-if="submited && !$v.mdlDatosMedicion.escala.required"
                        class="span-error-message"
                      >
                        *
                      </span>

                      <input
                        type="number"
                        class="form-control center"
                        min="0"
                        step="0.01"
                        lang="en"
                        name="dscto_interes"
                        v-model.number="mdlDatosMedicion.escala"
                        style="
                          font-size: 17px;
                          font-weight: bolder;
                          color: var(--colorAlto);
                        "
                      />
                    </div>

                    <div class="row" v-if="mdlDatosMedicion.modo == 'EDITAR'">
                      <div class="form-check">
                        <input
                          id="chbHabilitado"
                          type="checkbox"
                          v-model="mdlDatosMedicion.habilitado"
                        />
                        <label
                          class="form-check-label label-title"
                          for="chbHabilitado"
                        >
                          HABILITADO
                        </label>
                      </div>
                    </div>
                  </div>
                </form>
                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split mb-1"
                    @click="Guardar()"
                    title="Guardar MEDIDA"
                  >
                    <span class="icon text-white">
                      <i class="fas fa-save"></i>
                    </span>
                    <span class="text">GUARDAR</span>
                  </button>
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
import layout from "@/Pages/Logistica/Components/layout_logistica.vue";
import headerClose from "@/Pages/Logistica/Components/header_close.vue";
import headerCloseModal from "@/Pages/Logistica/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";
export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,
  },
  props: {
    mediciones: Array,
  },

  data() {
    return {
      submited: false,
      titulo_modal: null,
      mdlDatosMedicion: {
        modo: "",
        id: null,
        medida: null,
        escala: null,
        habilitado: false,
      },
    };
  },
  validations: {
    mdlDatosMedicion: {
      medida: { required },
      escala: { required },
    },
  },

  watch: {
    mediciones() {
      $("#tblMediciones").DataTable().destroy();
      this.TablaMediciones();
    },
  },

  mounted() {
    this.TablaMediciones();
  },

  methods: {
    hidenav() {
      return this.$refs.layout.hide_nav();
    },
    shownav() {
      return this.$refs.layout.show_nav();
    },
    TablaMediciones() {
      this.$nextTick(() => {
        var table = $("#tblMediciones").DataTable({
          scrollY: "350px",
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
          responsive: true,
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

        $("#inpBuscar").keyup(function () {
          table.search(this.value).draw();
        });
      });
    },

    Nuevo() {
      this.submited = false;
      this.titulo_modal = "NUEVA MEDICIÓN";
      this.mdlDatosMedicion.modo = "NUEVO";
      this.mdlDatosMedicion.id = 0;
      this.mdlDatosMedicion.medida = null;
      this.mdlDatosMedicion.escala = 0.5;
      this.mdlDatosMedicion.habilitado = true;

      $("#mdlDatosMedicion").css("display", "block");
    },

    Editar(item) {
      this.submited = false;
      this.titulo_modal = "EDITAR MEDICIÓN";
      this.mdlDatosMedicion.modo = "EDITAR";
      this.mdlDatosMedicion.id = item.id;
      this.mdlDatosMedicion.medida = item.medicion;
      this.mdlDatosMedicion.escala = item.escala;
      this.mdlDatosMedicion.habilitado = item.habilitado;

      $("#mdlDatosMedicion").css("display", "block");
    },

    Guardar() {
      let self = this;
      this.submited = true;
      if (this.$v.mdlDatosMedicion.$invalid) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Hay uno o más campos vacíos, verifique.",
        });
        return false;
      } else {
        if (this.mdlDatosMedicion.escala == 0) {
          Swal.fire({
            icon: "error",
            title: "¡Ups!",
            text: "El valor de la escala no puede ser 0.",
          });
          return false;
        }

        Swal.fire({
          icon: "question",
          text: "¿DESEA GUARDAR LOS CAMBIOS?",
          confirmButtonText:
            '<i class="fas fa-check" style="color:white;"></i>   Si',
          confirmButtonColor: "var(--colorAlto)",
          showCancelButton: true,
          cancelButtonText: '<i class="fas fa-times"></i>   No',
          cancelButtonColor: "var(--plomoOscuroEmpresarial)",
          allowOutsideClick: false,
        }).then((result) => {
          if (result.isConfirmed) {
            // this.$inertia.post(
            //   route("log.man.sum_mediciones.verificar"),
            //   self.mdlDatosMedicion
            // );

            // return false;
            axios
              .post(
                route("log.man.sum_mediciones.verificar"),
                self.mdlDatosMedicion
              )
              .then(function (response) {
                if (response.data == "EXISTE") {
                  Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Esta MEDIDA ya existe.",
                  });
                  return false;
                } else {
                  self.$inertia.post(
                    route("log.man.sum_mediciones.guardar"),
                    self.mdlDatosMedicion,
                    {
                      preserveScroll: true,
                      onStart: (visit) => {
                        let timerInterval;
                        Swal.fire({
                          title: "ESPERE POR FAVOR...",
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
                          allowOutsideClick: false,
                          preConfirm: (result) => {
                            self.submited = false;
                            $("#mdlDatosMedicion").css("display", "none");
                          },
                        });
                      },
                    }
                  );
                }
              });
          } else {
            return false;
          }
        });
      }
    },
  },
};
</script>

<style lang="css">
.slot-suministros-mediciones {
  width: 40% !important;
  margin-left: 30% !important;
}

.mdlDatosMedicion {
  margin-top: 2%;
}

@media (max-width: 900px) {
  .slot-suministros-mediciones {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .mdlDatosMedicion {
    margin-top: 20%;
  }
}
</style>
