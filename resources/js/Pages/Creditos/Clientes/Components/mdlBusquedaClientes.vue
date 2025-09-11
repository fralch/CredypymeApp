<template>
  <div id="mdlBusquedaClientes" class="modal">
    <!-- Modal content -->
    <div class="modal-content w-50 mdlBusquedaClientes">
      <div class="content contentBusquedaClientes" style="display: block">
        <div class="card">
          <div
            class="card-header d-flex align-items-center justify-content-between"
          >
            <strong> BÚSQUEDA DE CLIENTES</strong>

            <button
              type="button"
              class="btn btn-green"
              style="border-radius: 50%"
              @click="CerrarModal('mdlBusquedaClientes')"
            >
              <span class="icon text-white">
                <i class="fas fa-times"></i>
              </span>
            </button>
          </div>

          <div class="card-title">INFORMACIÓN PERSONAL</div>
          <div class="card-body card-block" id="contentBusquedaClientes">
            <div class="form-row row justify-content-md-center">
              <div class="form-group col-md-3 col-6">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="busqueda_clientes_filtro"
                    id="rdbApellidosNombresBusquedaClientes"
                    value="apellidos_nombres"
                    v-model="tipo_filtro"
                    checked
                  />
                  <label
                    class="label-title"
                    for="rdbApellidosNombresBusquedaClientes"
                    >APELLIDOS_NOMBRES
                  </label>
                </div>
              </div>
              <div class="form-group col-md-3 col-6">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="busqueda_clientes_filtro"
                    id="rdbDniBusquedaClientes"
                    value="dni"
                    v-model="tipo_filtro"
                  />
                  <label class="label-title" for="rdbDniBusquedaClientes"
                    >DNI</label
                  >
                </div>
              </div>
              <div class="form-group col-md-3 col-4">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="busqueda_clientes_filtro"
                    id="rdbExpedienteBusquedaClientes"
                    value="expediente"
                    v-model="tipo_filtro"
                  />
                  <label class="label-title" for="rdbExpedienteBusquedaClientes"
                    >EXPEDIENTE
                  </label>
                </div>
              </div>
            </div>
            <div class="form-row row justify-content-md-center mt-2">
              <div class="form-group col-md-8 col-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"
                      ><i class="fas fa-search"></i
                    ></span>
                  </div>
                  <input
                    id="inpBuscarCliente"
                    class="form-control mayus"
                    type="text"
                    placeholder="Ingrese 3 caractéres como mínimo..."
                    @keyup="BuscarClientes"
                    v-model="texto_buscar"
                    autocomplete="off"
                    @focus="hidenav()"
                    @blur="shownav()"
                    ref="buscar_cliente"
                  />
                </div>
              </div>
              <div class="form-group col-md-4 col-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Ag. </span>
                  </div>
                  <select
                    class="form-control center"
                    v-model="agencia_seleccionada"
                    @change="BuscarClientes"
                  >
                    <option
                      v-for="(agencia, index) in agencias_permitidas"
                      :key="index"
                      :value="agencia.id"
                    >
                      {{ agencia.agencia }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <table
              class="table"
              id="tblClientesResultado"
              width="100% !important"
            >
              <thead>
                <tr>
                  <th style="min-width: 50px !important">AGENCIA</th>
                  <th style="min-width: 200px !important">APELLIDOS_NOMBRES</th>
                  <th style="min-width: 60px !important">DNI</th>
                  <th style="min-width: 60px !important">EXPEDIENTE</th>
                  <th style="min-width: 100px !important">ASESOR</th>
                  <th style="min-width: 60px !important">C_RIESGO</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in lista_clientes"
                  :key="index"
                  :class="[index % 2 == 0 ? 'verde-claro' : '']"
                  @dblclick="Redirigir(item.id)"
                >
                  <td align="center">
                    {{ item.agencia }}
                  </td>
                  <td>
                    {{
                      item.apellido_paterno +
                      " " +
                      item.apellido_materno +
                      " " +
                      item.nombres
                    }}
                  </td>

                  <td align="center">
                    {{ item.dni }}
                  </td>
                  <td align="center">
                    {{
                      item.codigo_expediente == null
                        ? "-"
                        : item.codigo_expediente
                    }}
                  </td>
                  <td align="center">
                    {{ item.usuario_asesor }}
                  </td>
                  <td align="center">
                    {{ item.central_riesgo }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
export default {
  components: { headerClose },
  props: {},
  data() {
    return {
      agencias_permitidas: [],
      texto_buscar: null,
      agencia_seleccionada: null,
      tipo_filtro: "apellidos_nombres",
      lista_clientes: [],
      ruta: "cre.index",
      nombre_modulo: null,
    };
  },

  watch: {
    lista_clientes() {
      $("#tblClientesResultado").DataTable().destroy();
      this.TablaListaClientes();
    },

    nombre_modulo(value) {
      if (value == "evaluacion_financiera") {
        this.ruta = "cre.evaluacion_financiera";
      } else if (value == "propuesta") {
        this.ruta = "cre.propuesta";
      } else if (value == "album_fotos") {
        this.ruta = "cli.album_fotos";
      } else if (value == "prendas") {
        this.ruta = "cli.prendas";
      } else if (value == "crear_inversion_meta") {
        this.ruta = "inv.meta.crear";
      } else if (value == "copia_voucher") {
        this.ruta = "cre.copia_voucher";
      }
    },

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
      this.BuscarClientes();
    },
  },
  mounted() {
    this.TablaListaClientes();
  },

  methods: {
    hidenav() {
      return this.$parent.hide_nav();
    },
    shownav() {
      return this.$parent.show_nav();
    },
    TablaListaClientes() {
      this.$nextTick(() => {
        var table = $("#tblClientesResultado").DataTable({
          scrollY: "250px",
          scrollX: true,
          scrollCollapse: true,
          paging: false,
          ordering: false,
          fixedHeader: true,
          select: {
            style: "single",
            info: false,
          },
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

        $("#inpBuscarCliente").keyup(function () {
          table.search(this.value).draw();
        });
      });
    },
    BuscarClientes() {
      let self = this;
      let texto_buscar = this.texto_buscar;

      if (this.agencia_seleccionada == null) {
        return false;
      }

      if (texto_buscar && texto_buscar.length >= 3) {
        let data = new FormData();

        data.append("texto_buscar", texto_buscar);
        data.append("tipo_filtro", this.tipo_filtro);
        data.append("agencia", this.agencia_seleccionada);

        axios
          .post(route("cli.listado_registro.buscar"), data)
          .then(function (response) {
            self.lista_clientes = response.data;
          });
      } else {
        self.lista_clientes = [];
      }
    },
    async Redirigir(id) {
      let self = this;
      let object = {};
      if (this.nombre_modulo == "propuesta") {
        object = {
          cliente_id: id,
          propuesta_id: 0,
          agencia_id: this.agencia_seleccionada,
        };
      } else if (this.nombre_modulo == "historial_crediticio") {
        let modulo = this.$parent.$refs.mdlHistorialCrediticio;

        modulo.agencia_id = this.agencia_seleccionada;
        modulo.cliente_id = id;
        modulo.ActualizarInformacion();

        this.CerrarModal("mdlBusquedaClientes");
        return $("#mdlHistorialCrediticio").css("display", "block");
      } else if (this.nombre_modulo == "prendas") {
        object = {
          cliente_id: id,
          agencia_id: this.agencia_seleccionada,
        };
      } else if (this.nombre_modulo == "album_fotos") {
        let modulo = this.$parent.$refs.mdlAlbumFotos;

        modulo.agencia_id = this.agencia_seleccionada;
        modulo.cliente_id = id;
        modulo.Resetear();
        modulo.ActualizarInformacion();

        this.CerrarModal("mdlBusquedaClientes");
        return $("#mdlAlbumFotos").css("display", "block");
      } else if (this.nombre_modulo == "evaluacion_financiera") {
        object = {
          cliente_id: id,
          agencia_id: this.agencia_seleccionada,
        };
      } else if (this.nombre_modulo == "crear_inversion_meta") {
        object = {
          cliente_id: id,
          agencia_id: this.agencia_seleccionada,
        };
      } else if (this.nombre_modulo == "copia_voucher") {
        object = {
          cliente_id: id,
          agencia_id: this.agencia_seleccionada,
        };
      }

      this.$inertia.get(route(this.ruta, object));
    },
    CerrarModal(modal) {
      $("#" + modal).css("display", "none");
    },
  },
};
</script>

<style lang="css">
.mdlBusquedaClientes {
  margin-top: 5% !important;
}

/* Para corregir bug de datatable */
.dataTable {
  width: 100% !important;
}
.dataTables_scrollHeadInner {
  width: 100% !important;
}
.DTFC_ScrollWrapper {
  height: auto !important;
}
@media (max-width: 900px) {
  .mdlBusquedaClientes {
    margin-top: 15% !important;
  }
}

/* --------------------------------- */
</style>
