<template>
  <layout ref="layout">
    <div class="slot_body slot-licencia_categorias" slot="component-view">
      <div class="card">
        <headerClose :title="'LICENCIA CATEGORÍAS'"></headerClose>

        <div class="card-title">LISTA DE RESULTADOS</div>
        <div class="card-body card-block">
          <div class="form-row">
            <div class="input-group col-md-10 col-7">
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
            <div class="col-md-1 col-2 ml-1">
              <button class="btn btn-action btn-icon-split" @click="Nuevo">
                <span class="icon text-white">
                  <i class="fas fa-plus"></i>
                </span>
                <span class="text">NUEVO</span>
              </button>
            </div>
          </div>
          <table
            class="table table-hover"
            id="tblLicenciaCategorias"
            width="100%"
          >
            <thead>
              <tr>
                <th style="min-width: 10px !important">EDITAR</th>
                <th style="min-width: 20px !important">ABREVIACIÓN</th>
                <th style="min-width: 500px !important">NOMBRE</th>
                <th style="min-width: 20px !important">HABILITADO</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in categorias" :key="index">
                <td class="table-bordered" align="center">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="Editar(item)"
                  >
                    <span class="icon text-white">
                      <i class="far fa-edit" style="color: white"></i>
                    </span>
                  </button>
                </td>
                <td class="table-bordered" align="center">
                  {{ item.abreviacion }}
                </td>
                <td class="table-bordered" align="left">
                  {{ item.nombre }}
                </td>
                <td class="table-bordered" align="center">
                  {{ item.habilitado == 1 ? "Si" : "No" }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal" id="mdlCategoria">
        <div class="modal-content w-35 mdlCategoria">
          <div class="content" style="display: block">
            <div class="card">
              <headerCloseModal
                :titulo_modal="title_modal"
                :nombre_modal="'mdlCategoria'"
              >
              </headerCloseModal>

              <div class="card-title">DATOS DE LA CATEGORÍA</div>
              <div class="card-body card-block">
                <form>
                  <div class="form-row">
                    <div class="form-group col-md-12 col-12">
                      <label
                        for="inpNombre"
                        class="form-control-label label-title"
                        >NOMBRE</label
                      >
                      <input
                        type="text"
                        class="form-control mayus"
                        v-model="frmLicenciaCategoria.nombre"
                        :class="[
                          submited
                            ? $v.frmLicenciaCategoria.nombre.$invalid
                              ? 'is-invalid'
                              : 'is-valid'
                            : '',
                        ]"
                      />
                    </div>
                    <div class="form-group col-md-3 col-12">
                      <label
                        for="inpAbreviacion"
                        class="form-control-label label-title"
                        >ABREVIACIÓN</label
                      >
                      <input
                        type="text"
                        class="form-control mayus"
                        v-model="frmLicenciaCategoria.abreviacion"
                        :class="[
                          submited
                            ? $v.frmLicenciaCategoria.abreviacion.$invalid
                              ? 'is-invalid'
                              : 'is-valid'
                            : '',
                        ]"
                      />
                    </div>
                  </div>

                  <div
                    class="form"
                    v-if="frmLicenciaCategoria.modo == 'EDITAR'"
                  >
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
                              v-model="frmLicenciaCategoria.habilitado" /><span
                              class="cr"
                              style="margin-right: 0 !important"
                              ><i class="cr-icon fa fa-check"></i></span
                          ></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                <hr />
                <div class="text-right">
                  <button
                    class="btn btn-action btn-icon-split"
                    @click="Guardar"
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
const noZero = (value) => value != 0;
export default {
  components: {
    layout,
    headerClose,
    headerCloseModal,
  },

  data() {
    return {
      windowWidth: window.innerWidth,
      submited: false,
      title_modal: "",
      frmLicenciaCategoria: {
        modo: "NUEVO",
        nombre: null,
        abreviacion: null,
        habilitado: null,
      },
      categorias: [],
    };
  },

  validations: {
    frmLicenciaCategoria: {
      nombre: { required },
      abreviacion: { required },
    },
  },
  watch: {
    categorias() {
      $("#tblLicenciaCategorias").DataTable().destroy();
      this.TablaLicenciaCategorias();
    },
  },
  mounted() {
    this.TablaLicenciaCategorias();
    this.ListarRecursos();
  },

  methods: {
    hidenav() {
      return this.$refs.layout.hide_nav();
    },
    shownav() {
      return this.$refs.layout.show_nav();
    },
    async ListarRecursos() {
      let self = this;

      //   this.$inertia
      await axios
        .get(route("man.sol.licencia_categorias.listar_categorias"))
        .then(function (response) {
          self.categorias = response.data.categorias;
        });
    },

    TablaLicenciaCategorias() {
      this.$nextTick(() => {
        var table = $("#tblLicenciaCategorias").DataTable({
          scrollY: "350px",
          scrollX: true,
          fixedColumns: {
            leftColumns: 0,
          },

          scrollCollapse: true,
          paging: false,
          order: [1, "asc"],
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
        });
        $("#inpBuscar").keyup(function () {
          table.search(this.value).draw();
        });
      });
    },

    Nuevo() {
      this.inicio = 0;
      this.submited = false;
      this.title_modal = "NUEVA CATEGORÍA";
      this.frmLicenciaCategoria.id = null;
      this.frmLicenciaCategoria.modo = "NUEVO";
      this.frmLicenciaCategoria.nombre = null;
      this.frmLicenciaCategoria.abreviacion = null;
      this.frmLicenciaCategoria.habilitado = 1;

      $("#mdlCategoria").css("display", "block");
    },
    Editar(item) {
      this.submited = false;
      this.title_modal = "EDITAR CATEGORÍA";
      this.frmLicenciaCategoria.id = item.id;
      this.frmLicenciaCategoria.modo = "EDITAR";
      this.frmLicenciaCategoria.nombre = item.nombre;
      this.frmLicenciaCategoria.abreviacion = item.abreviacion;
      this.frmLicenciaCategoria.habilitado = item.habilitado;

      $("#mdlCategoria").css("display", "block");
    },
    Guardar() {
      let self = this;
      this.submited = true;

      if (this.$v.frmLicenciaCategoria.$invalid == true) {
        Swal.fire({
          icon: "error",
          title: "¡Ups!",
          text: "Hay campos por rellenar.",
        });
        return false;
      } else {
        let data = new FormData();
        data.append("id", this.frmLicenciaCategoria.id);
        data.append("abreviacion", this.frmLicenciaCategoria.abreviacion);
        data.append("modo", this.frmLicenciaCategoria.modo);

        axios
          .post(route("man.sol.licencia_categorias.verificar"), data)
          .then(function (response) {
            let resultado = response.data;
            if (resultado == "EXISTE") {
              Swal.fire({
                icon: "error",
                title: "¡Ups!",
                text: "Esta categoría ya se encuentra registrado, intente nuevamente.",
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
                  data.append("id", self.frmLicenciaCategoria.id);
                  data.append("nombre", self.frmLicenciaCategoria.nombre);
                  data.append("modo", self.frmLicenciaCategoria.modo);

                  data.append(
                    "abreviacion",
                    self.frmLicenciaCategoria.abreviacion
                  );
                  data.append(
                    "habilitado",
                    self.frmLicenciaCategoria.habilitado
                  );

                  Swal.fire({
                    title: "REGISTRANDO",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willOpen: async () => {
                      Swal.showLoading();

                      return await axios
                        .post(
                          route("man.sol.licencia_categorias.guardar"),
                          data
                        )
                        .then((response) => {
                          $("#mdlCategoria").css("display", "none");
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
    },

    Cerrar() {
      $("#mdlCategoria").css("display", "none");
    },
  },
};
</script>

<style >
.slot-licencia_categorias {
  width: 60% !important;
  margin-left: 20% !important;
}
.mdlCategoria {
  margin-top: 2%;
}

@media (max-width: 900px) {
  .slot-licencia_categorias {
    width: 98% !important;
    margin-left: 1% !important;
  }
  .mdlCategoria {
    margin-top: 20%;
  }
}
</style>
