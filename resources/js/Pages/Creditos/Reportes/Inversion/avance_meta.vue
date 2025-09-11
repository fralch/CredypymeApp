<template>
	<layout ref="layout">
		<div class="slot_body slot-avance-meta" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MI ' : '') + 'AVANCE DE INVERSIONES META'
						"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-11">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-3">
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
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbPorProductoMeta"
													v-model="por_producto_meta"
												/>
											</div>
											<label
												class="input-group-text prepend-title"
												for="chbPorProductoMeta"
												style="font-size: 13px"
												v-if="windowWidth >= 900"
											>
												PRODUCTO META
											</label>
											<label
												class="input-group-text prepend-title"
												for="chbPorProductoMeta"
												style="font-size: 13px"
												v-if="windowWidth < 900"
											>
												P. META
											</label>
										</div>

										<select
											class="form-control center"
											v-model="producto_meta_seleccionado"
											:disabled="!por_producto_meta"
										>
											<option :value="0" disabled selected>
												Seleccione...
											</option>
											<option
												v-for="(item, index) in producto_meta_filtrados"
												:key="index"
												:value="item.id"
											>
												{{ item.producto }}
											</option>
										</select>
									</div>

									<div class="input-group col-md-5">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbPorAsesor"
													v-model="por_asesor"
													:disabled="modo == 'personal'"
												/>
											</div>
											<label
												class="input-group-text prepend-title"
												for="chbPorAsesor"
											>
												ASESOR
											</label>
										</div>

										<select
											class="form-control center"
											v-model="usuario_seleccionado"
											:disabled="!por_asesor"
										>
											<option :value="0" disabled selected>
												Seleccione...
											</option>
											<option
												v-for="(item, index) in usuarios_filtrados"
												:key="index"
												:value="item.dni"
											>
												{{ item.usuario }}
											</option>
										</select>
										<div class="input-group-append">
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbMostrarHabilitados"
													v-model="mostrar_habilitados"
													:disabled="!por_asesor || modo == 'personal'"
													@change="FiltrarUsuarios"
												/>
												<label class="m-0 ml-1" for="chbHabilitados"
													>Habilitados</label
												>
											</div>
										</div>
									</div>

									<button
										class="btn btn-action btn-icon-split col-2 offset-5 mt-1"
										style="width: 50px; height: 50px"
										title="Buscar"
										@click="Buscar"
										v-if="windowWidth < 900"
									>
										<span class="icon text-white" style="font-size: 15px">
											<i class="fas fa-search"></i>
										</span>
									</button>
								</div>
							</fieldset>

							<button
								class="btn btn-action btn-icon-split mt-3"
								style="width: 50px; height: 50px"
								title="Buscar"
								@click="Buscar"
								v-if="windowWidth >= 900"
							>
								<span class="icon text-white" style="font-size: 25px">
									<i class="fas fa-search"></i>
								</span>
							</button>
						</div>

						<div class="card-title mt-2">LISTA DE RESULTADOS</div>

						<table class="table" id="tblAvanceInversionMeta" width="100%">
							<thead>
								<tr>
									<th style="min-width: 20px !important">N°</th>
									<th style="min-width: 100px !important">DNI</th>
									<th style="min-width: 325px !important">CLIENTE</th>
									<th style="min-width: 100px !important">ASESOR</th>
									<th style="min-width: 250px !important">PRODUCTO_META</th>
									<th style="min-width: 75px !important">VALOR_META</th>
									<th style="min-width: 75px !important">ACUMULADO</th>
									<th style="min-width: 75px !important">%_AVANCE</th>
									<th style="min-width: 150px !important">FECHA_APERTURA</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_inversiones_meta"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
								>
									<td align="center">{{ index + 1 }}</td>
									<td align="center">{{ item.dni }}</td>
									<td align="left">
										{{
											item.apellido_paterno +
											" " +
											item.apellido_materno +
											" " +
											item.nombres
										}}
									</td>
									<td align="center">
										{{ item.usuario_asesor }}
									</td>
									<td align="center">
										{{ item.producto }}
									</td>

									<td align="right">S/ {{ roundTo(item.valor_meta, 2) }}</td>
									<td align="right">S/ {{ roundTo(item.acumulado, 2) }}</td>

									<td align="center ">
										<div class="progress">
											<div
												class="progress-bar"
												:class="
													item.porcentaje < 25
														? 'bg-danger'
														: item.porcentaje >= 25 && item.porcentaje < 50
														? 'bg-warning'
														: item.porcentaje >= 50 && item.porcentaje < 75
														? ''
														: 'bg-success'
												"
												role="progressbar"
												:style="
													'width:' +
													round((item.acumulado * 100) / item.valor_meta, 2) +
													'%'
												"
											>
												<span
													class="text"
													style="margin-left: 24px !important ; color: black"
												>
													{{
														round((item.acumulado * 100) / item.valor_meta, 2)
													}}
													%</span
												>
											</div>
										</div>
									</td>
									<td align="center">
										{{ item.fecha_apertura }}
									</td>
								</tr>
							</tbody>
						</table>
						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="Exportar('PDF')"
								title="Imprimir"
								:disabled="lista_inversiones_meta.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-print"></i>
								</span>
								<span class="text">IMPRIMIR</span>
							</button>
							<button
								class="btn btn-cancel btn-icon-split"
								title="Exportar"
								@click="Exportar('XLSX')"
								:disabled="lista_inversiones_meta.length == 0"
							>
								<span class="icon text-white">
									<i class="fas fa-file-excel"></i>
								</span>
								<span class="text">EXPORTAR</span>
							</button>
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: {
		layout,
		headerClose,
		headerCloseModal,
	},
	props: {
		modo: String,
		usuarios: Array,
		lista_productos_meta: Array,
	},

	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			agencia_busqueda: 0,

			windowWidth: window.innerWidth,

			usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],

			producto_meta_filtrados: [],

			por_asesor: this.modo == "personal" ? true : false,
			usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,
			mostrar_habilitados: true,

			por_producto_meta: false,
			producto_meta_seleccionado: 0,
			lista_inversiones_meta: [],
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
		agencia_busqueda() {
			this.FiltrarUsuarios();
			this.producto_meta_seleccionado = 0;
			this.FiltrarProductoMeta();
		},

		por_asesor() {
			this.usuario_seleccionado = 0;
		},

		lista_inversiones_meta() {
			$("#tblAvanceInversionMeta").DataTable().destroy();
			this.TablaAvanceInversion();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaAvanceInversion();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		round(value, decimal_places) {
			return parseFloat(value).toFixed(decimal_places);
		},
		roundTo(value, decimal_places) {
			let valor = 0;
			let numero_decimales = decimal_places;

			if (value) {
				valor = value;
			}

			if (valor == 0) {
				return "-";
			} else {
				return parseFloat(valor).toLocaleString("es-PE", {
					minimumFractionDigits: numero_decimales,
					maximumFractionDigits: numero_decimales,
				});
			}
		},
		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("por_asesor", this.por_asesor);
			data.append("por_producto_meta", this.por_producto_meta);

			if (this.por_asesor) {
				data.append("asesor_id", this.usuario_seleccionado);
			}
			if (this.por_producto_meta) {
				data.append("producto_meta_id", this.producto_meta_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					// this.$inertia.post(route("rep.inv.avance_meta.buscar"), data);
					axios
						.post(route("rep.inv.avance_meta.buscar"), data)
						.then(function (response) {
							if (response.data.lista_inversiones_meta.length == 0) {
								self.lista_inversiones_meta = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_inversiones_meta =
									response.data.lista_inversiones_meta;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
									timer: 2000,
									showConfirmButton: false,
								});
							}
						});
				},
			});
		},
		FiltrarUsuarios() {
			if (this.modo == "completo") {
				this.usuarios_filtrados = [];
				this.usuarios_seleccionados = [];

				if (this.mostrar_habilitados) {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) =>
							item.agencia_id == this.agencia_busqueda && item.habilitado == 1
					);
				} else {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) => item.agencia_id == this.agencia_busqueda
					);
				}
			} else if (this.modo == "personal") {
				this.usuarios_filtrados = this.usuarios;
			}
		},
		FiltrarProductoMeta() {
			this.producto_meta_filtrados = this.lista_productos_meta.filter(
				(item) => item.agencia_id == this.agencia_busqueda
			);
		},

		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/INVERSION_MI_AVANCE_META"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/INVERSION_AVANCE_META"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},

		TablaAvanceInversion() {
			this.$nextTick(() => {
				let scroll_height = "300px";
				if (this.windowWidth <= 900) {
					scroll_height = "230px";
				}
				var table = $("#tblAvanceInversionMeta").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: true,
					select: {
						style: "single",
						info: false,
					},
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
		Exportar(tipo) {
			let data = new FormData();
			data.append("datos_tabla", JSON.stringify(this.lista_inversiones_meta));
			data.append("tipo", tipo);

			let titulo = null;
			if (tipo == "PDF") {
				titulo = "IMPRIMIENDO";
			} else if (tipo == "XLSX") {
				titulo = "EXPORTANDO";
			}

			//   this.$inertia.post(route("rep.inv.avance_meta.exportar"), data);
			//   return false;

			Swal.fire({
				title: titulo,
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.inv.avance_meta.exportar"), data)
						.then(function (response) {
							let origin = window.location.origin;
							if (tipo == "XLSX") {
								let path_xlsx = response.data.path_xlsx;

								const link = document.createElement("a");
								link.href = origin + path_xlsx;
								link.download = "rptAvanceMeta.xlsx";
								link.click();
								return Swal.fire({
									icon: "success",
									title: "¡EXPORTADO!",
									timer: 2000,
									showConfirmButton: false,
								});
							} else if (tipo == "PDF") {
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
							}
						});
				},
			});
		},
	},
};
</script>

<style lang="css">
.slot-avance-meta {
	width: 70% !important;
	margin-left: 15% !important;
}

@media (max-width: 900px) {
	.slot-avance-meta {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>
