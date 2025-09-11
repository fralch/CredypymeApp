<template>
  <layout ref="layout">
    <div class="slot_body slot-niveles" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose :title="'NIVELES'"></headerClose>
          <div class="card-title">LISTA DE RESULTADOS</div>

          <div class="card-body card-block">
            <div class="form-row">
              <div class="input-group col-md-6 col-7">
                <div class="input-group-prepend">
                  <span class="input-group-text"
                    ><i class="fas fa-search"></i
                  ></span>
                </div>
                <input
                  class="form-control mayus"
                  type="text"
                  id="inpBuscarNivel"
                  autocomplete="off"
                  spellcheck="false"
                  @focus="hidenav()"
                  @blur="shownav()"
                />
              </div>
              <div class="col-md-1 col-2 ml-1">
                <button
                  class="btn btn-action btn-icon-split"
                  @click="NuevoNivel"
                  title="NUEVO NIVEL"
                >
                  <span class="icon text-white">
                    <i class="fas fa-plus"></i>
                  </span>
                  <span class="text">NUEVO</span>
                </button>
              </div>
            </div>

            <table class="table" id="tblNiveles" width="100%">
              <thead>
                <tr>
                  <th style="min-width: 10px !important">EDITAR</th>
                  <th style="min-width: 150px !important">NIVEL</th>
                  <th style="min-width: 10px !important">HAB.</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  class="table-bordered"
                  v-for="(nivel, index) in niveles"
                  :key="index"
                >
                  <td align="center">
                    <button
                      class="btn btn-action"
                      type="button"
                      title="EDITAR NIVEL"
                      @click="EditarNivel(nivel)"
                    >
                      <span class="icon text-white">
                        <i class="fas fa-edit"></i>
                      </span>
                    </button>
                  </td>

                  <td>
                    {{ nivel.nivel == "" ? "-" : nivel.nivel }}
                  </td>
                  <td align="center">
                    {{ nivel.habilitado == 1 ? "Si" : "No" }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- The Modal -->
      <div id="mdlDatosNivel" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-25 mdlDatosNivel">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="title_modal"
                :nombre_modal="'mdlDatosNivel'"
              >
              </headerCloseModal>

              <div class="card-title">DATOS DE EL NIVEL</div>
              <div class="card-body card-block">
                <form>
                  <label for="txtNombre" class="label-title mayus">NIVEL</label>
                  <!-- <span
										v-if="submited && $v.frmDatosNivel.nivel.$invalid"
										class="span-error-message"
									>
										*
									</span> -->

                  <div class="form-row">
                    <div class="form-group col-md-8">
                      <input
                        type="text"
                        class="form-control mayus"
                        v-model="frmDatosNivel.nivel"
                        autocomplete="off"
                        spellcheck="false"
                        :class="[
                          submited
                            ? $v.frmDatosNivel.nivel.$invalid
                              ? 'is-invalid'
                              : 'is-valid'
                            : '',
                        ]"
                      />
                    </div>
                    <div class="form-group col-md-2">
                      <button
                        type="button"
                        class="btn btn-cancel btn-icon-split"
                        @click="AgregarEscala"
                        title="AGREGAR ESCALA"
                      >
                        <span class="icon text-white">
                          <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">ESCALA</span>
                      </button>
                    </div>
                  </div>
                  <div class="form-row" v-if="frmDatosNivel.modo == 'EDITAR'">
                    <div class="form-group col-md-12">
                      <div class="form-row">
                        <label for="chbHabilitado" class="label-title"
                          >Habilitado:</label
                        >
                        <div class="checkbox">
                          <label
                            class="align-middle"
                            style="
                              font-size: 1em;
                              margin-bottom: 0 !important;
                              height: 1em !important;
                            "
                            for="chbHabilitado"
                            ><input
                              type="checkbox"
                              id="chbHabilitado"
                              v-model="frmDatosNivel.habilitado" /><span
                              class="cr"
                              style="margin-right: 0 !important"
                              ><i class="cr-icon fa fa-check"></i></span
                          ></label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="form-row"
                    v-for="(item, index) in frmDatosNivel.lista_escalas"
                    :key="index"
                  >
                    <div
                      class="form-group col-md-1"
                      style="margin-top: 27px !important"
                    >
                      <button
                        type="button"
                        class="btn btn-danger btn-icon-split"
                        @click="QuitarEscala(item.indice)"
                        title="QUITAR ESCALA"
                      >
                        <span class="icon text">
                          <i class="fas fa-trash"></i>
                        </span>
                      </button>
                    </div>

                    <div class="form-group col-md-3">
                      <label class="label-title mayus">VALOR</label>

                      <input
                        type="number"
                        class="form-control center"
                        min="0"
                        step="0.1"
                        lang="en"
                        v-model.number="item.valor"
                        @change="Redondear(item.indice)"
                      />
                    </div>
                    <div class="form-group col-md-7">
                      <label for="txtTitulo" class="label-title mayus"
                        >TÍTULO</label
                      >

                      <input
                        type="text"
                        class="form-control mayus"
                        v-model="item.titulo"
                        autocomplete="off"
                        spellcheck="false"
                      />
                    </div>
                  </div>
                </form>
                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="GuardarNivel"
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
import layout from "@/Pages/Gth/Components/layout_gth.vue";
import headerClose from "@/Pages/Gth/Components/header_close.vue";
import headerCloseModal from "@/Pages/Gth/Components/header_close_modal.vue";

import { required } from "vuelidate/lib/validators";

export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,
  },
  data: () => ({
    submited: false,
    niveles: [],
    title_modal: null,
    frmDatosNivel: {
      modo: null,
      id: null,
      nivel: null,
      habilitado: null,
      lista_escalas: [],
    },
    cont_1: 0,
    inicio: 0,
  }),
  validations: {
    frmDatosNivel: {
      nivel: { required },
    },
  },
  watch: {
    niveles() {
      $("#tblNiveles").DataTable().destroy();
      this.TablaNiveles();
    },
  },
  mounted() {
    this.TablaNiveles();
    this.ListarRecursos();
  },
  methods: {
    Redondear(e) {
      let valor = 0;
      let numero_decimales = 2;
      let indice = this.frmDatosNivel.lista_escalas.findIndex(
        (x) => x.indice === e
      );

      if (this.frmDatosNivel.lista_escalas[indice].valor >= 0) {
        valor = this.frmDatosNivel.lista_escalas[indice].valor;
        this.frmDatosNivel.lista_escalas[indice].valor =
          parseFloat(valor).toFixed(numero_decimales);
      } else {
        this.frmDatosNivel.lista_escalas[indice].valor = 0;
      }
    },
    async ListarRecursos() {
      let self = this;
      await axios
        .get(route("man.col.niveles.listar_recursos"))
        .then(function (response) {
          self.niveles = response.data.niveles;
        });
    },
    TablaNiveles() {
      self = this;
      this.$nextTick(() => {
        var table = $("#tblNiveles").DataTable({
          scrollX: true,
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

        $("#inpBuscarNivel").keyup(function () {
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

    NuevoNivel() {
      this.inicio = 0;
      this.submited = false;
      this.title_modal = "NUEVO NIVEL";
      this.frmDatosNivel.id = null;
      this.frmDatosNivel.modo = "NUEVO";
      this.frmDatosNivel.nivel = null;
      this.frmDatosNivel.lista_escalas = [];
      this.frmDatosNivel.habilitado = 1;

      $("#mdlDatosNivel").css("display", "block");
    },

    EditarNivel(nivel) {
      this.submited = false;
      this.title_modal = "EDITAR NIVEL";
      this.frmDatosNivel.id = nivel.id;
      this.frmDatosNivel.modo = "EDITAR";
      this.frmDatosNivel.nivel = nivel.nivel;
      this.frmDatosNivel.habilitado = nivel.habilitado;
      this.frmDatosNivel.lista_escalas = [];
      this.inicio = 0;

      let json = JSON.parse(nivel.descripcion);

      json.map((element) => {
        if (element.indice > this.inicio) {
          this.inicio = element.indice;
        }

        this.frmDatosNivel.lista_escalas.push(element);
      }),
        $("#mdlDatosNivel").css("display", "block");
    },
    GuardarNivel() {
      let self = this;
      this.submited = true;

      if (this.$v.frmDatosNivel.$invalid == true) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Debe agregar un nombre al nivel.",
        });
        return false;
      } else {
        if (this.frmDatosNivel.lista_escalas.length == 0) {
          Swal.fire({
            icon: "error",
            title: "¡Ups!",
            text: "Debe agregar mínimamente una escala.",
          });
          return false;
        } else {
          this.cont_1 = 0;
          this.frmDatosNivel.lista_escalas.forEach((element) => {
            if (
              element.titulo == null ||
              element.titulo == "" ||
              element.titulo == " " ||
              element.valor == null ||
              element.valor == "NaN"
            ) {
              this.cont_1 = this.cont_1 + 1;
            }
          });
          if (this.cont_1 > 0) {
            Swal.fire({
              icon: "error",
              title: "¡Ups!",
              text: "Hay campos por rellenar.",
            });
            return false;
          } else {
            let data = new FormData();
            data.append("modo", this.frmDatosNivel.modo);
            data.append("id", this.frmDatosNivel.id);
            data.append("nivel", this.frmDatosNivel.nivel);
            axios
              .post(route("man.col.niveles.verificar"), data)
              .then(function (response) {
                let resultado = response.data;
                if (resultado == "EXISTE") {
                  Swal.fire({
                    icon: "error",
                    title: "¡Ups!",
                    text: "Este nivel ya se encuentra registrado, intente nuevamente.",
                  });
                  return false;
                } else if (resultado == "NO EXISTE") {
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
                  }).then((result) => {
                    if (result.isConfirmed) {
                      let data = new FormData();
                      data.append("modo", self.frmDatosNivel.modo);
                      data.append("nivel", self.frmDatosNivel.nivel);
                      data.append("habilitado", self.frmDatosNivel.habilitado);
                      data.append("id", self.frmDatosNivel.id);

                      data.append(
                        "lista_escalas",
                        JSON.stringify(self.frmDatosNivel.lista_escalas)
                      );

                      // self.$inertia.post(
                      //   route("man.col.niveles.guardar"),
                      //   data)
                      //   return false

                      Swal.fire({
                        title: "REGISTRANDO",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: async () => {
                          Swal.showLoading();

                          return await axios
                            .post(route("man.col.niveles.guardar"), data)
                            .then((response) => {
                              $("#mdlDatosNivel").css("display", "none");
                              self.ListarRecursos();
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
                    }
                  });
                }
              });
          }
        }
      }
    },

    AgregarEscala() {
      this.inicio = this.inicio + 1;
      let obj = { indice: this.inicio, valor: null, titulo: null };

      this.frmDatosNivel.lista_escalas.push(obj);
    },

    QuitarEscala(escala) {
      let indice = this.frmDatosNivel.lista_escalas.findIndex(
        (x) => x.indice === escala
      );

      this.frmDatosNivel.lista_escalas.splice(indice, 1);
    },
  },
};
</script>

<style >
.slot-niveles {
  width: 50% !important;
  margin-left: 25% !important;
}

.mdlDatosNivel {
  margin-top: 2%;
}

@media (max-width: 900px) {
  .slot-niveles {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .mdlDatosNivel {
    margin-top: 20%;
  }
}
</style>

