<template>
	<layout ref="layout">
		<div class="slot_body slot-reporte-clientes-activos" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose
						:title="
							(modo == 'personal' ? 'MIS ' : '') +
							'CLIENTES' +
							(modo == 'personal' ? ' - ' : ' ') +
							'ACTIVOS'
						"
					></headerClose>

					<div class="card-body card-block">
						<div class="form-row">
							<fieldset class="form-group col-md-9">
								<legend>
									<label class="label-title">Filtros de búsqueda</label>
								</legend>
								<div class="row">
									<div class="input-group col-md-5">
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
									</div>
									<div
										class="form-check col-md-2"
										:style="
											windowWidth >= 900 ? '' : 'margin-left: 15px !important'
										"
									>
										<input
											class="form-check-input"
											type="checkbox"
											id="chbMostrarHabilitados"
											v-model="mostrar_habilitados"
											:disabled="!por_asesor || modo == 'personal'"
											@change="FiltrarUsuarios"
										/>
										<label class="label-title" for="chbMostrarHabilitados"
											>Sólo habilitados</label
										>
									</div>

									<div class="input-group col-md-5 mt-1">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input
													type="checkbox"
													id="chbPorCalifacion"
													v-model="por_calificacion"
												/>
											</div>
											<label
												class="input-group-text prepend-title"
												for="chbPorCalifacion"
												style="font-size: 13px"
											>
												CALIFICACIÓN
											</label>
										</div>

										<select
											class="form-control center"
											v-model="calificacion_seleccionado"
											:disabled="!por_calificacion"
										>
											<option :value="0" disabled selected>
												Seleccione...
											</option>
											<option :value="'A'">A</option>
											<option :value="'B'">B</option>
											<option :value="'C'">C</option>
											<option :value="'D'">D</option>
											<option :value="'E'">E</option>
										</select>
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

							<div class="col-md-1" v-if="windowWidth >= 900">
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
						</div>

						<div class="card-title mt-2">LISTA DE RESULTADOS</div>

						<div class="form-row col-md-12 mt-2">
							<div class="form-group col-md-8 col-12">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Buscar </span>
									</div>
									<input
										class="form-control mayus"
										type="text"
										id="inpBuscarActivo"
										:disabled="lista_clientes_activos.length == 0"
										placeholder="Ingrese 3 caractéres como mínimo..."
										autocomplete="off"
										spellcheck="false"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
						</div>
						<table class="table" id="tblActivos" width="100%">
							<thead>
								<tr>
									<th style="min-width: 30px !important">N°</th>
									<th style="min-width: 250px !important">CLIENTE</th>
									<th style="min-width: 100px !important">ASESOR</th>
									<th style="min-width: 70px !important">C_RIESGO</th>
									<th style="min-width: 250px !important">DOMICILIO</th>
									<th style="min-width: 200px !important">UBICACIÓN</th>
									<th style="min-width: 200px !important">DIRECCIÓN_NEGOCIO</th>
									<th style="min-width: 200px !important">UBICACIÓN_NEGOCIO</th>
									<th style="min-width: 50px !important">CALIF.</th>
									<th style="min-width: 300px !important">NOTAS</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_clientes_activos"
									:key="index"
									class="table-bordered"
									:class="index % 2 == 0 ? 'verde-claro' : ''"
									@dblclick="VerComentarios(item)"
								>
									<td align="center">{{ index + 1 }}</td>
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
										{{ item.usuario }}
									</td>
									<td align="center">
										{{ item.central_riesgo }}
									</td>
									<td align="left">
										{{ item.direccion_domicilio }}
									</td>
									<td align="center">
										{{
											item.departamento +
											" - " +
											item.provincia +
											" - " +
											item.distrito
										}}
									</td>
									<td align="left">
										{{ item.direccion_negocio }}
									</td>
									<td align="center">
										{{
											item.departamento_negocio +
											" - " +
											item.provincia_negocio +
											" - " +
											item.distrito_negocio
										}}
									</td>
									<td align="center">
										{{ item.calificacion }}
									</td>
									<td align="left">
										{{ item.notas }}
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
								:disabled="lista_clientes_activos.length == 0"
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
			<div id="modalComentarioCliente" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-50 modalComentarioCliente">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'COMENTARIO '"
								:nombre_modal="'modalComentarioCliente'"
							></headerCloseModal>

							<div class="card-body card-block">
								<div class="form-row">
									<div class="input-group col-md-7 mb-1">
										<div class="input-group-prepend">
											<label class="input-group-text" for="rdbPorNombre">
												CLIENTE
											</label>
										</div>
										<input
											type="text"
											class="form-control"
											autocomplete="off"
											spellcheck="false"
											v-model="form_datos_cliente.nombres_completos"
											disabled="true"
										/>
									</div>
								</div>

								<table class="table" id="tblComentario" width="100%">
									<thead>
										<tr>
											<th style="min-width: 10px !important">N°</th>
											<th style="min-width: 200px !important">COMENTARIO</th>
											<th style="min-width: 40px !important">FECHA_REGISTRO</th>
											<th style="min-width: 40px !important">
												USUARIO_REGISTRO
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in lista_activos_comentarios"
											:key="index"
											:class="index % 2 == 0 ? 'verde-claro' : ''"
										>
											<td align="center">{{ index + 1 }}</td>

											<td align="left">
												{{ item.comentario }}
											</td>
											<td align="center">
												{{ item.fecha_comentario }}
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
										@click="AgregarComentario"
									>
										<span class="icon text-white"
											><i class="fas fa-plus"></i>
										</span>
										<span class="text">AGREGAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="modalComentario" class="modal">
				<!-- Modal content -->
				<div class="modal-content w-30 modalComentario">
					<div class="content" style="display: block">
						<div class="card">
							<headerCloseModal
								:titulo_modal="'AGREGAR COMENTARIO '"
								:nombre_modal="'modalComentario'"
							></headerCloseModal>
							<div class="card-body card-block">
								<div class="form-row">
									<div class="form-group col-md-12">
										<label class="form-control-label label-title"
											>Ingrese su comentario:</label
										>
										<span
											v-if="
												submited && !$v.form_datos_cliente.comentario.required
											"
											class="span-error-message"
											>*</span
										>
										<textarea
											type="text"
											rows="3"
											class="form-control mayus text-row"
											@focus="hidenav()"
											@blur="shownav()"
											v-model="form_datos_cliente.comentario"
										></textarea>
										<hr />
										<div class="text-right">
											<button
												class="btn btn-action btn-icon-split"
												title="Registrar COMENTARIO"
												@click="Registrar()"
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
	props: {
		modo: String,
		usuarios: Array,
	},

	data() {
		return {
			submited: false,
			agencias_permitidas: [],
			agencia_busqueda: 0,

			windowWidth: window.innerWidth,

			usuarios_filtrados: this.modo == "personal" ? this.usuarios : [],

			por_asesor: this.modo == "personal" ? true : false,
			usuario_seleccionado: this.modo == "personal" ? this.usuarios[0].dni : 0,
			mostrar_habilitados: true,

			por_calificacion: false,
			calificacion_seleccionado: 0,
			lista_clientes_activos: [],

			form_datos_cliente: {
				nombres_completos: null,
				comentario: null,
				cliente_id: null,
			},

			lista_activos_comentarios: [],
		};
	},
	validations: {
		form_datos_cliente: {
			comentario: { required },
		},
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
		},

		por_asesor() {
			this.usuario_seleccionado = 0;
		},
		por_calificacion() {
			this.calificacion_seleccionado = 0;
		},

		lista_clientes_activos() {
			$("#tblActivos").DataTable().destroy();
			this.TablaActivos();
		},
		lista_activos_comentarios() {
			$("#tblComentario").DataTable().destroy();
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
		this.TablaActivos();

		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		Registrar() {
			let self = this;
			this.submited = true;
			if (this.$v.form_datos_cliente.$invalid) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Hay uno o más campos vacíos, verifique.",
				});
				return false;
			} else {
				Swal.fire({
					icon: "question",
					text: "¿DESEA REGISTRAR EL COMENTARIO?",
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

						data.append("modo", this.modo);
						data.append("agencia_id", this.agencia_busqueda);
						data.append("cliente_id", this.form_datos_cliente.cliente_id);
						data.append("comentario", this.form_datos_cliente.comentario);
						this.$inertia.post(
							route("rep.cli.clientes_activos.comentar"),
							data,
							{
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
									this.submited = false;
									this.ListarComentarios(
										this.agencia_busqueda,
										this.form_datos_cliente.cliente_id
									);

									this.form_datos_cliente.comentario = null;
									$("#modalComentario").css("display", "none");
									return Swal.fire({
										icon: "success",
										title: "¡ÉXITO!",
										allowOutsideClick: false,
									});
								},
							}
						);
					} else {
						return false;
					}
				});
			}
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

		async VerComentarios(item) {
			this.form_datos_cliente.nombres_completos =
				item.apellido_paterno +
				" " +
				item.apellido_materno +
				" " +
				item.nombres;
			this.form_datos_cliente.cliente_id = item.id;

			let self = this;

			await this.ListarComentarios(this.agencia_busqueda, item.id);

			$("#modalComentarioCliente").css("display", "block");
		},

		ListarComentarios(agencia_id, cliente_id) {
			this.form_datos_cliente.comentario = null;
			let self = this;

			let data = new FormData();
			data.append("agencia_id", agencia_id);
			data.append("cliente_id", cliente_id);

			return axios
				.post(route("rep.cli.clientes_activos.listar_comentarios"), data)
				.then(function (response) {
					self.lista_activos_comentarios =
						response.data.lista_activos_comentarios;
				});
		},
		AgregarComentario() {
			$("#modalComentario").css("display", "block");
		},

		ListarAgenciasPermitidas() {
			if (this.modo == "personal") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CLIENTES_MIS_ACTIVOS"
				);
				this.filtro_usuario = true;
			} else if (this.modo == "completo") {
				this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
					"CREDITOS_REPORTES/CLIENTES_ACTIVOS"
				);
			} else {
				this.agencias_permitidas = [];
			}
		},

		hidenav() {
			this.$refs.layout.hide_nav();
		},
		shownav() {
			this.$refs.layout.show_nav();
		},
		Buscar() {
			let self = this;

			let data = new FormData();
			data.append("agencia_id", this.agencia_busqueda);
			data.append("por_asesor", this.por_asesor);
			data.append("por_calificacion", this.por_calificacion);
			data.append("modo", "activos");

			if (this.por_asesor) {
				data.append("asesor_id", this.usuario_seleccionado);
			}
			if (this.por_calificacion) {
				data.append("calificacion", this.calificacion_seleccionado);
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
					//   this.$inertia.post(route("rep.cli.clientes_activos.buscar"), data);
					//   return false;
					axios
						.post(route("rep.cli.clientes_activos.buscar"), data)
						.then(function (response) {
							if (response.data.lista_clientes_activos.length == 0) {
								self.lista_clientes_activos = [];

								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.lista_clientes_activos =
									response.data.lista_clientes_activos;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
								});
							}
						});
				},
			});
		},
		TablaActivos() {
			this.$nextTick(() => {
				let scroll_height = "300px";
				if (this.windowWidth <= 900) {
					scroll_height = "160px";
				}
				var table = $("#tblActivos").DataTable({
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

				$("#inpBuscarActivo").keyup(function () {
					table.column(1).search(this.value).draw();
				});
			});
		},
		Exportar(modo) {
			let data = new FormData();
			// data.append("datos_tabla_descuentos", JSON.stringify(this.datos_tabla_descuentos));
			data.append("datos_tabla", JSON.stringify(this.lista_clientes_activos));
			data.append("tipo", "clientes_activos");

			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					axios
						.post(route("rep.cli.clientes_activos.exportar"), data)
						.then(function (response) {
							// return Swal.fire({
							// 	icon: "success",
							// 	title: "¡Listo!",
							// });

							var linkSource =
								"data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," +
								response.data;
							var downloadLink = document.createElement("a");
							var fileName = "rptClientesActivos.xlsx";

							downloadLink.href = linkSource;
							downloadLink.download = fileName;
							downloadLink.click();

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
	},
};
</script>

<style lang="css">
.slot-reporte-clientes-activos {
	width: 70% !important;
	margin-left: 15% !important;
}
.modalComentarioCliente {
	margin-top: 2%;
}
.modalComentario {
	margin-top: 4%;
}

@media (max-width: 900px) {
	.slot-reporte-clientes-activos {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
	.modalComentarioCliente {
		margin-top: 20%;
	}
	.modalComentario {
		margin-top: 20%;
	}
}
</style>
