<template>
  <layout ref="layout">
    <div class="slot_body slot-reporte-transacciones" slot="component-view">
      <div class="content" style="display: block">
        <div class="card">
          <headerClose
            :title="'TRANSACCIONES REALES'"
          ></headerClose>

          <div class="card-body card-block">
            <div class="form-row">
              <div class="form-group col-md-12 text-center">
                <div class="form-check">
                  <div class="radios" style="display: inline-block">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="tipo_transaccion"
                      id="rdbIngreso"
                      v-model="tipo_transaccion"
                      value="I"
                    />
                    <label class="form-check-label bolder" for="rdbIngreso"
                      >INGRESOS</label
                    >
                  </div>
                  <div
                    class="radios"
                    style="display: inline-block; margin-left: 30px"
                  >
                    <input
                      class="form-check-input"
                      type="radio"
                      name="tipo_transaccion"
                      id="rdbEgreso"
                      v-model="tipo_transaccion"
                      value="E"
                    />
                    <label class="form-check-label bolder" for="rdbEgreso"
                      >EGRESOS</label
                    >
                  </div>
                </div>
              </div>

              <fieldset class="form-group col-md-10">
                <legend>
                  <label class="label-title">FILTROS DE BÚSQUEDA</label>
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
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">DESDE</span>
                    </div>
                    <input
                      type="date"
                      class="form-control input-information center"
                      v-model="fecha_desde"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                    />
                  </div>
                  <div class="input-group col-md-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text prepend-title">HASTA</span>
                    </div>
                    <input
                      type="date"
                      class="form-control input-information center"
                      v-model="fecha_hasta"
                      :style="
                        windowWidth >= 900
                          ? 'font-size: 15px !important'
                          : 'font-size: 13px !important'
                      "
                    />
                  </div>

                  <div class="col-md-1 text-right" v-if="windowWidth < 900">
                    <button
                      class="btn btn-action btn-icon-split mt-3"
                      title="Buscar"
                      @click="Buscar"
                    >
                      <span class="icon text-white" style="font-size: 15px">
                        <i class="fas fa-search"></i>
                      </span>
                    </button>
                  </div>
                </div>
              </fieldset>

              <div class="col-md-1 ml-3" v-if="windowWidth >= 900">
                <button
                  class="btn btn-action btn-icon-split mt-3"
                  title="Buscar"
                  @click="Buscar"
                >
                  <span class="icon text-white" style="font-size: 25px">
                    <i class="fas fa-search"></i>
                  </span>
                </button>
              </div>

         

              <div class="input-group col-md-5">
                <div class="input-group-prepend">
                
                  <label
                    class="input-group-text prepend-title"
                  >
                    CATEGORÍA
                  </label>
                </div>
                <div class="input-group-prepend"></div>
                <select
                  class="form-control center"
                  id="slcCategorias"
                  v-model="categoria_seleccionada"
                  :disabled="lista_transacciones_reales.length == 0 && lista_transacciones_no_reales.length == 0"
                  @change="SeleccionSubcategoria"
                  data-index="2"
                >
                  <option value="0">TODAS</option>
                  <option
                    v-for="(item, index) in categorias_filtradas"
                    :key="index"
                    :value="item.categoria"
                  >
                    {{ item.categoria }}
                  </option>
                </select>
              </div>

              <div class="input-group col-md-5">
                <div class="input-group-prepend">
                
                  <label
                    class="input-group-text prepend-title"
                  >
                    SUBCATEGORÍA
                  </label>
                </div>

                <select
                  class="form-control center"
                  id="slcSubcategorias"
                  v-model="subcategoria_seleccionada"
                  :disabled="lista_transacciones_reales.length == 0 && lista_transacciones_no_reales.length == 0"
                   data-index="3"
                >
                  <option value="0">TODAS</option>
                  <option
                    v-for="(item, index) in subcategorias_filtradas"
                    :key="index"
                    :value="item.subcategoria"
                  >
                    {{ item.subcategoria }}
                  </option>
                </select>
              </div>
            </div>

             
                  <hr />
                <div class="card-title">LISTA DE RESULTADOS</div>
	<div class="card-body card-block">
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item" role="presentation">
									<a
										class="tab-title nav-link active"
										id="reales-tab"
										data-toggle="tab"
										href="#reales"
										role="tab"
										aria-controls="reales"
										aria-selected="true"
										>REALES</a
									>
								</li>
								<li class="nav-item" role="presentation">
									<a
										class="tab-title nav-link"
										id="noreales-tab"
										data-toggle="tab"
										href="#noreales"
										role="tab"
										aria-controls="noreales"
										aria-selected="false"
										>NO REALES</a
									>
								</li>
							</ul>
							<div class="tab-content" id="myTabContent">
								<div
									class="tab-pane fade show active"
									id="reales"
									role="tabpanel"
									aria-labelledby="reales-tab"
								>
									<table class="table" id="tblReales" width="100%">
										<thead>
										      <tr>
                      <th style="min-width: 20px !important">N°</th>
                      <th style="min-width: 40px !important">
                        SELECCIONAR
                      </th>
                      <th style="min-width: 200px !important">CATEGORÍA</th>
                      <th style="min-width: 250px !important">SUBCATEGORÍA</th>

                      <th style="min-width: 450px !important">CONCEPTO</th>

                      <th style="min-width: 130px !important">
                        FECHA_REGISTRO
                      </th>
                      <th style="min-width: 100px !important">A_USUARIO</th>

                      <th style="min-width: 100px !important">MONTO</th>

                      <th style="min-width: 170px !important">ÁREA</th>
                      <th style="min-width: 100px !important">
                        CAJA
                      </th>
                    </tr>
										</thead>
										<tbody>
                    <tr
                      v-for="(item, index) in lista_transacciones_reales"
                      :key="index"
                      class="table-bordered"
                      :class="[index % 2 == 0 ? 'verde-claro' : '']"
                    >
                      <td align="center">
                        {{ index + 1 }}
                      </td>

                      <td align="center">
													<div class="align-middle">
														<div class="checkbox">
															<label
																class="align-middle"
																style="
																	font-size: 2em;
																	margin-bottom: 0 !important;
																	height: 28.6px !important;
																"
																:for="'checkbox1_' + index"
																><input
																	type="checkbox"
																	:id="'checkbox1_' + index"
																	:value="item.id"
																	v-model="
																		transacciones_reales_seleccionadas
																	" /><span
																	class="cr"
																	style="margin-right: 0 !important"
																	><i class="cr-icon fa fa-check"></i></span
															></label>
														</div>
													</div>
												</td>

                      <td>
                        {{ item.categoria }}
                      </td>
                     
                      <td >
                        {{ item.subcategoria }}
                      </td>
                     
                      <td>
                        {{ item.concepto }}
                      </td>

                      <td align="center">
                        {{ item.fecha_transaccion }}
                      </td>
                      <td align="center">
                        {{ item.a_usuario }}
                      </td>
                      <td align="right">S/ {{ roundTo(item.monto, 2) }}</td>

                      <td align="center">
                        {{ item.area == null ? "-" : item.area }}
                      </td>
                      <td align="center">
                        {{ item.caja_usuario }}
                      </td>
                    </tr>

											
										</tbody>
									</table>
									<hr />
							<div class="text-left">
										<button
											class="btn btn-action btn-icon-split"
											title="EnviarReales"
											:disabled="transacciones_reales_seleccionadas.length == 0"
                      @click="Envio('A_NOREAL')"
										>
											  <span class="icon text-white">
												<i class="fas fa-arrow-right"></i>
											</span>
											<span class="text">ENVIAR A NO REALES</span>
                    
										</button>
									</div>
								</div>
								<div
									class="tab-pane fade"
									id="noreales"
									role="tabpanel"
									aria-labelledby="noreales-tab"
								>
									<table class="table" id="tblNoreales" width="100%">
										<thead>
									        <tr>
                     <th style="min-width: 20px !important">N°</th>
                      <th style="min-width: 40px !important">
                        SELECCIONAR
                      </th>
                      <th style="min-width: 200px !important">CATEGORÍA</th>
                      <th style="min-width: 250px !important">SUBCATEGORÍA</th>

                      <th style="min-width: 450px !important">CONCEPTO</th>

                      <th style="min-width: 130px !important">
                        FECHA_REGISTRO
                      </th>
                      <th style="min-width: 100px !important">A_USUARIO</th>

                      <th style="min-width: 100px !important">MONTO</th>

                      <th style="min-width: 170px !important">ÁREA</th>
                      <th style="min-width: 100px !important">
                        CAJA
                      </th>
                    </tr>
										</thead>
										<tbody>
                      <tr
                      v-for="(item, index) in lista_transacciones_no_reales"
                      :key="index"
                      class="table-bordered"
                      :class="[index % 2 == 0 ? 'verde-claro' : '']"
                    >
                      <td align="center">
                        {{ index + 1 }}
                      </td>

                      <td align="center">
													<div class="align-middle">
														<div class="checkbox">
															<label
																class="align-middle"
																style="
																	font-size: 2em;
																	margin-bottom: 0 !important;
																	height: 28.6px !important;
																"
																:for="'checkbox2_' + index"
																><input
																	type="checkbox"
																	:id="'checkbox2_' + index"
																	:value="item.id"
																	v-model="
																		transacciones_noreales_seleccionadas
																	" /><span
																	class="cr"
																	style="margin-right: 0 !important"
																	><i class="cr-icon fa fa-check"></i></span
															></label>
														</div>
													</div>
												</td>

                      <td>
                        {{ item.categoria }}
                      </td>
                     
                      <td>
                        {{ item.subcategoria }}
                      </td>
                      
                      <td>
                        {{ item.concepto }}
                      </td>

                      <td align="center">
                        {{ item.fecha_transaccion }}
                      </td>
                      <td align="center">
                        {{ item.a_usuario }}
                      </td>
                      <td align="right">S/ {{ roundTo(item.monto, 2) }}</td>

                      <td align="center">
                        {{ item.area == null ? "-" : item.area }}
                      </td>
                      <td align="center">
                        {{ item.caja_usuario }}
                      </td>
                    </tr>
										
										</tbody>
									</table>
									<hr />
                  			<div class="text-left">
										<button
											class="btn btn-action btn-icon-split"
											title="EnviarNoreales"
                      	:disabled="transacciones_noreales_seleccionadas == 0"
                      @click="Envio('A_REAL')"
										>
											  <span class="icon text-white">
												<i class="fas fa-arrow-left"></i>
											</span>
											<span class="text">ENVIAR A REALES</span>
                    
										</button>
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
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";
export default {
  components: {
    layout,
    headerClose,
  },
  props: {

    categorias: Array,
    subcategorias: Array,

  },

  data() {
    return {
      agencias_permitidas: [],
      agencia_busqueda: 0,
      agencia_busqueda_actual: 0,



      windowWidth: window.innerWidth,

      tipo_transaccion: "E",
      tipo_transaccion_actual: null,
      fecha_desde: null,
      fecha_desde_actual: null,
      fecha_hasta: null,
      fecha_hasta_actual: null,

      modo: null,


      categorias_filtradas: [],
      categoria_seleccionada: 0,

      subcategorias_filtradas: [],
      subcategoria_seleccionada: 0,


      lista_transacciones_reales: [],
      lista_transacciones_no_reales: [],


				transacciones_reales_seleccionadas: [],
				transacciones_noreales_seleccionadas: [],
			
    };
  },

  watch: {

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

    tipo_transaccion() {
      this.categoria_seleccionada = 0;
      this.subcategoria_seleccionada = 0;

      this.FiltrarCategorias();
      this.FiltrarSubcategorias();
    },

    agencia_busqueda() {

      this.categoria_seleccionada = 0;
      this.subcategoria_seleccionada = 0;

      this.FechaActual();
      this.FiltrarCategorias();
      this.FiltrarSubcategorias();
    },

    categoria_seleccionada() {
      this.FiltrarSubcategorias();
    },

    
   
    lista_transacciones_reales() {
      $("#tblReales").DataTable().destroy();
      this.TablaReales();
    },

      lista_transacciones_no_reales() {
      $("#tblNoreales").DataTable().destroy();
      this.TablaNoreales();
    },
  
  },
  mounted() {
    this.ListarAgenciasPermitidas();
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });

    this.TablaReales();
    this.TablaNoreales();

    this.FiltrarCategorias();
    this.FiltrarSubcategorias();


  },

  methods: {
 

    Envio(modo) {

        let self = this;

        self.modo = modo;

        var data = new FormData();


        if(modo == "A_NOREAL"){


          var pregunta = "¿DESEA ENVIAR ESTAS TRANSACCIONES A NO REALES?"

          data.append(
                  "lista_transacciones_edicion",
                  JSON.stringify(self.transacciones_reales_seleccionadas)
                );

        } else{
           var pregunta = "¿DESEA ENVIAR ESTAS TRANSACCIONES A REALES?"

           data.append(
                  "lista_transacciones_edicion",
                  JSON.stringify(self.transacciones_noreales_seleccionadas)

                );

        };

        Swal.fire({
          icon: "question",
          text: pregunta,
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
              title: "ENVIANDO",
              showConfirmButton: false,
              allowOutsideClick: false,
              willOpen: async () => {
                Swal.showLoading();

                data.append(
                  "modo",
                  self.modo
                );

                 data.append(
                  "agencia_busqueda",
                  self.agencia_busqueda
                );

            //     this.$inertia.post(
						//   route("rep.con.transacciones_reales.enviar"),
						//   data
						// );
            //     return false

                return await axios
                  .post(route("rep.con.transacciones_reales.enviar"), data)
                  .then((response) => {

                    self.agencia_busqueda = self.agencia_busqueda_actual;
                     self.fecha_desde = self.fecha_desde_actual;
                      self.fecha_hasta = self.fecha_hasta_actual;
                       self.tipo_transaccion = self.tipo_transaccion_actual;

                    self.transacciones_reales_seleccionadas = [];
                    self.transacciones_noreales_seleccionadas = [];

                     self.ListarTransaccionesReales();
                  
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

    SeleccionSubcategoria() {
      this.subcategoria_seleccionada = 0;
    },


  
    async FechaActual() {
      if (this.agencia_busqueda == null) {
        return false;
      } else {
        let fecha_actual = await this.$refs.layout.fecha_hora_actual(
          this.agencia_busqueda
        );

        fecha_actual = fecha_actual.substring(0, 10);

        this.fecha_desde = fecha_actual;
        this.fecha_hasta = fecha_actual;
      }
    },
    ListarAgenciasPermitidas() {
        this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
          "CREDITOS_REPORTES/CONTABILIDAD_TRANSACCIONES_REALES"
        );
     
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

   
    TablaReales() {
      this.$nextTick(() => {
        let scroll_height = "325px";
        if (this.windowWidth <= 900) {
          scroll_height = "200px";
        }
        var table = $("#tblReales").DataTable({
          scrollY: scroll_height,
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

    
        	$("#slcCategorias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search('^' + this.value + '$', true, false).draw();
					}
				});
        	$("#slcSubcategorias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search('^' + this.value + '$', true, false).draw();
					}
				});

      });
    },

    TablaNoreales() {
      this.$nextTick(() => {
        let scroll_height = "325px";
        if (this.windowWidth <= 900) {
          scroll_height = "200px";
        }
        var table = $("#tblNoreales").DataTable({
          scrollY: scroll_height,
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

           	$("#slcCategorias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search('^' + this.value + '$', true, false).draw();
					}
				});
        	$("#slcSubcategorias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search('^' + this.value + '$', true, false).draw();
					}
				});

      });
    },
    

    FiltrarCategorias() {


               this.categorias_filtradas = this.categorias.filter(
          (item) =>
            item.tipo == this.tipo_transaccion &&
            item.agencia_id == this.agencia_busqueda
        );

    },
    FiltrarSubcategorias() {

  if (this.categoria_seleccionada != 0) {
          this.subcategorias_filtradas = this.subcategorias.filter(
            (item) =>
              item.tipo == this.tipo_transaccion &&
              item.agencia_id == this.agencia_busqueda &&
              item.categoria == this.categoria_seleccionada
          );
        } else {
          this.subcategorias_filtradas = this.subcategorias.filter(
            (item) =>
              item.tipo == this.tipo_transaccion &&
              item.agencia_id == this.agencia_busqueda
          );
        }
    
    },

    ListarTransaccionesReales() {
      let self = this;

      let data = new FormData();
      data.append("agencia_id", this.agencia_busqueda);
      data.append("fecha_desde", this.fecha_desde);
      data.append("fecha_hasta", this.fecha_hasta);
      data.append("tipo_transaccion", this.tipo_transaccion);

      // this.$inertia.post(route("rep.con.transacciones_reales.buscar"),data);
      // return false

      axios
        .post(route("rep.con.transacciones_reales.buscar"), data)
        .then(function (response) {
          if (response.data.lista_transacciones_reales.length == 0 && response.data.lista_transacciones_no_reales.length == 0) {

            self.lista_transacciones_reales = [];
            self.lista_transacciones_no_reales = [];

              return Swal.fire({
                icon: "info",
                title: "¡Ups!",
                text: "No se encontraron datos",
                allowOutsideClick: true,
              });
            
          } else {

            self.lista_transacciones_reales = response.data.lista_transacciones_reales;
             self.lista_transacciones_no_reales = response.data.lista_transacciones_no_reales;

             self.transacciones_reales_seleccionadas = [];
             self.transacciones_noreales_seleccionadas = [];

             self.agencia_busqueda_actual= self.agencia_busqueda;
             self.fecha_desde_actual= self.fecha_desde;
             self.fecha_hasta_actual= self.fecha_hasta;
             self.tipo_transaccion_actual= self.tipo_transaccion;

              return Swal.fire({
                icon: "success",
                title: "¡Listo!",
                timer: 1200,
                showConfirmButton: false,
              });
            
          }
        });
    },

    Buscar() {
      let self = this;
      

      Swal.fire({
        title: "BUSCANDO",
        text: "Espere porfavor...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();

          self.ListarTransaccionesReales();
        },
      });
    },
  
  },
};
</script>

<style lang="css">
.slot-reporte-transacciones {
  width: 70% !important;
  margin-left: 15% !important;
}

.blue {
  background: var(--blue) !important;
}

.font-11 {
  font-size: 11px !important;
}



@media only screen and (max-width: 900px) {
  .slot-reporte-transacciones {
    width: 99% !important;
    margin-left: 0.5% !important;
  }

}
</style>


