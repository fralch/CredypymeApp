<template>
	<div class="">
		<div id="mdlControlMoraAgencia" class="modal">
			<!-- Modal content -->
			<div class="modal-content mdlControlMoraAgencia">
				<div class="content" style="display: block">
					<div class="card">
						<headerCloseModal
							:titulo_modal="'CONTROL DE MORA - AGENCIA'"
							:nombre_modal="'mdlControlMoraAgencia'"
						></headerCloseModal>

						<div class="card-body card-block">
							<div class="form-row">
								<fieldset class="form-group col-md-9">
									<legend>
										<label class="label-title">Filtros de búsqueda</label>
									</legend>
									<div class="row">
										<div class="input-group col-md-4">
											<div class="input-group-prepend">
												<span class="input-group-text">AGENCIA</span>
											</div>
											<select
												class="form-control center"
												@change="FiltrarUsuarios"
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
												<span class="input-group-text">DESDE</span>
											</div>
											<input
												type="date"
												class="form-control center bolder"
												v-model="fecha_desde"
												style="font-size: 15px"
											/>
										</div>
										<div class="input-group col-md-4">
											<div class="input-group-prepend">
												<span class="input-group-text">HASTA</span>
											</div>
											<input
												type="date"
												class="form-control center bolder"
												v-model="fecha_hasta"
												style="font-size: 15px"
											/>
										</div>
									</div>
								</fieldset>
								<div class="col-md-1 ml-3">
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
								<fieldset class="form-group col-md-12">
									<legend>
										<label class="label-title"
											>Seleccione las calificaciones para sumar al total</label
										>
									</legend>
									<div class="form-row text-center">
										<div
											class="input-group row ml-2 col-md-2 col-6"
											style="float: left"
										>
											<div class="form-check text-center">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbPorUsuario1"
													v-model="filtro_usuario"
												/>
												<label class="label-title" for="chbPorUsuario1"
													>Filtrar por asesor</label
												>
											</div>
										</div>

										<div class="col-md-1">
											<div
												class="form-check text-center"
												style="background: var(--yellow) !important"
											>
												<input
													class="form-check-input"
													type="checkbox"
													id="chbNormal"
													:value="1"
													v-model="riesgos_seleccionados"
												/>
												<label class="label-title" for="chbNormal"
													>Normal</label
												>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-check text-center">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbCPP"
													:value="2"
													v-model="riesgos_seleccionados"
												/>
												<label class="label-title" for="chbCPP"
													>Con problema potencial</label
												>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-check text-center">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbDeficiente"
													:value="3"
													v-model="riesgos_seleccionados"
												/>
												<label class="label-title" for="chbDeficiente"
													>Deficiente</label
												>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-check text-center">
												<input
													class="form-check-input"
													type="checkbox"
													id="chbDudoso"
													:value="4"
													v-model="riesgos_seleccionados"
												/>
												<label class="label-title" for="chbDudoso"
													>Dudoso</label
												>
											</div>
										</div>
										<div class="col-md-1">
											<div
												class="form-check text-center"
												style="background: var(--red) !important"
											>
												<input
													class="form-check-input"
													type="checkbox"
													id="chbPerdida"
													:value="5"
													v-model="riesgos_seleccionados"
												/>
												<label class="label-title text-white" for="chbPerdida"
													>Pérdida</label
												>
											</div>
										</div>
										<div class="col-md-1">
											<div
												class="form-check text-center"
												style="background: var(--red) !important"
											>
												<input
													class="form-check-input"
													type="checkbox"
													id="chbPerdidaTotal"
													:value="6"
													v-model="riesgos_seleccionados"
												/>
												<label
													class="label-title text-white"
													for="chbPerdidaTotal"
													>Pérdida total</label
												>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
						</div>

						<div class="card-title">LISTA DE RESULTADOS</div>
						<div class="card-body card-block">
							<div class="form-row">
								<div
									class="col-md-2"
									style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)"
									v-if="filtro_usuario"
								>
									<div class="row ml-1">
										<div class="form-check center col-md-12">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbMostrarHabilitados"
												v-model="mostrar_habilitados"
												@change="FiltrarUsuarios"
											/>
											<label class="label-title" for="chbMostrarHabilitados"
												>Habilitados</label
											>
										</div>
										<div class="form-check center col-md-12">
											<input
												class="form-check-input"
												type="checkbox"
												id="chbMarcarTodo"
												v-model="marcar_todo"
												@change="MarcarTodo"
											/>
											<label class="label-title" for="chbMarcarTodo"
												>Marcar todo</label
											>
										</div>
									</div>

									<table class="table" id="tblUsuarios1" width="100%">
										<thead>
											<tr>
												<th>USUARIO</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, index) in usuarios_filtrados"
												:key="index"
											>
												<td style="padding: 0px !important">
													<div class="custom-control custom-checkbox mt-2">
														<input
															type="checkbox"
															class="custom-control-input"
															:id="'chbUsuario1_' + index"
															:value="item.dni"
															v-model="usuarios_seleccionados"
														/>
														<label
															class="custom-control-label ml-4"
															:for="'chbUsuario1_' + index"
															>{{ item.usuario }}</label
														>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div
									class="pl-3"
									:class="filtro_usuario ? 'col-md-10' : 'col-md-12'"
								>
									<table class="table" id="tblCarteraMora1" width="100%">
										<thead class="table-bordered">
											<tr>
												<th
													class="bolder"
													colspan="6"
													style="
														background-color: var(--colorBajo) !important;
														border-style: none !important;
													"
												></th>
												<th colspan="4" class="plomo-oscuro">NORMAL</th>
												<th colspan="4" class="plomo-oscuro">
													CON_PROBLEMA_POTENCIAL
												</th>
												<th colspan="4" class="plomo-oscuro">DEFICIENTE</th>
												<th colspan="4" class="plomo-oscuro">DUDOSO</th>
												<th colspan="4" class="plomo-oscuro">PÉRDIDA</th>
												<th colspan="4" class="plomo-oscuro">PÉRDIDA_TOTAL</th>
												<th colspan="4" class="black">TOTAL</th>
											</tr>
											<tr>
												<th style="min-width: 80px !important">FECHA</th>
												<th style="min-width: 100px !important" class="black">
													SALDO_TOTAL
												</th>
												<th class="black" style="min-width: 50px !important">
													CRE
												</th>
												<th class="black" style="min-width: 50px !important">
													CLI_ACT
												</th>
												<th class="black" style="min-width: 50px !important">
													TASA_PROM.
												</th>
												<th class="black" style="min-width: 50px !important">
													TASA_ROTA.
												</th>
												<th style="min-width: 40px !important">CRE.</th>
												<th style="min-width: 40px !important">CLI.</th>
												<th style="min-width: 100px !important">
													SALDO_CAPITAL
												</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 40px !important">CRE.</th>
												<th style="min-width: 40px !important">CLI.</th>
												<th style="min-width: 100px !important">
													SALDO_CAPITAL
												</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 40px !important">CRE.</th>
												<th style="min-width: 40px !important">CLI.</th>
												<th style="min-width: 100px !important">
													SALDO_CAPITAL
												</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 40px !important">CRE.</th>
												<th style="min-width: 40px !important">CLI.</th>
												<th style="min-width: 100px !important">
													SALDO_CAPITAL
												</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 40px !important">CRE.</th>
												<th style="min-width: 40px !important">CLI.</th>
												<th style="min-width: 100px !important">
													SALDO_CAPITAL
												</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 40px !important">CRE.</th>
												<th style="min-width: 40px !important">CLI.</th>
												<th style="min-width: 100px !important">
													SALDO_CAPITAL
												</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 100px !important">CLI_X_CAL</th>
												<th style="min-width: 40px !important">N°</th>
												<th style="min-width: 40px !important">%</th>
												<th style="min-width: 150px !important">
													SALDO_CAPITAL(SELEC.)
												</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item_2, index_2) in cartera_mora"
												:key="index_2"
												class="table-bordered"
												:class="index_2 % 2 == 0 ? 'verde-claro' : ''"
											>
												<td align="center">{{ item_2.fecha }}</td>
												<td align="right">
													S/
													{{ roundTo(item_2.saldo_total, 2) }}
												</td>
												<td align="center">{{ item_2.cantidad_creditos }}</td>
												<td align="center">{{ item_2.clientes_activos }}</td>
												<td align="center">
													{{ roundTo(item_2.tasa_promedio, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.tasa_rotacion, 2) }}
												</td>

												<td align="center">{{ item_2.cantidad_creditos_1 }}</td>
												<td align="center">{{ item_2.cantidad_clientes_1 }}</td>
												<td align="right">
													S/ {{ roundTo(item_2.saldo_capital_1, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_1, 2) }}
												</td>
												<td align="center">{{ item_2.cantidad_creditos_2 }}</td>
												<td align="center">{{ item_2.cantidad_clientes_2 }}</td>
												<td align="right">
													S/ {{ roundTo(item_2.saldo_capital_2, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_2, 2) }}
												</td>
												<td align="center">{{ item_2.cantidad_creditos_3 }}</td>
												<td align="center">{{ item_2.cantidad_clientes_3 }}</td>
												<td align="right">
													S/ {{ roundTo(item_2.saldo_capital_3, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_3, 2) }}
												</td>
												<td align="center">{{ item_2.cantidad_creditos_4 }}</td>
												<td align="center">{{ item_2.cantidad_clientes_4 }}</td>
												<td align="right">
													S/ {{ roundTo(item_2.saldo_capital_4, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_4, 2) }}
												</td>
												<td align="center">{{ item_2.cantidad_creditos_5 }}</td>
												<td align="center">{{ item_2.cantidad_clientes_5 }}</td>
												<td align="right">
													S/ {{ roundTo(item_2.saldo_capital_5, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_5, 2) }}
												</td>
												<td align="center">{{ item_2.cantidad_creditos_6 }}</td>
												<td align="center">{{ item_2.cantidad_clientes_6 }}</td>
												<td align="right">
													S/ {{ roundTo(item_2.saldo_capital_6, 2) }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_6, 2) }}
												</td>
												<td align="center">
													{{ item_2.clientes_seleccion_total }}
												</td>
												<td align="center">
													{{ item_2.creditos_seleccion_total }}
												</td>
												<td align="center">
													{{ roundTo(item_2.porcentaje_seleccion_total, 2) }}
												</td>
												<td align="right">
													S/ {{ roundTo(item_2.capital_seleccion_total, 2) }}
												</td>
											</tr>
										</tbody>
										<tfoot class="table-bordered">
											<tr>
												<th class="font-11">TOTAL</th>
												<th class="black text-right font-11">
													S/
													{{ roundTo(totales.t_saldo_total, 2) }}
												</th>
												<th class="black font-11">
													{{ totales.t_cantidad_creditos }}
												</th>
												<th class="black font-11">
													{{ totales.t_clientes_activos }}
												</th>

												<th class="black font-11">
													{{
														roundTo(
															totales.p_tasa_promedio / totales.cantidad,
															2
														)
													}}
												</th>
												<th class="white font-11"></th>
												<th class="light font-11">
													{{ totales.t_cantidad_creditos_1 }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_clientes_1 }}
												</th>
												<th class="light text-right font-11" colspan="2">
													S/ {{ roundTo(totales.t_saldo_capital_1, 2) }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_creditos_2 }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_clientes_2 }}
												</th>
												<th class="light text-right font-11" colspan="2">
													S/ {{ roundTo(totales.t_saldo_capital_2, 2) }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_creditos_3 }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_clientes_3 }}
												</th>
												<th class="light text-right font-11" colspan="2">
													S/ {{ roundTo(totales.t_saldo_capital_3, 2) }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_creditos_4 }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_clientes_4 }}
												</th>
												<th class="light text-right font-11" colspan="2">
													S/ {{ roundTo(totales.t_saldo_capital_4, 2) }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_creditos_5 }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_clientes_5 }}
												</th>
												<th class="light text-right font-11" colspan="2">
													S/ {{ roundTo(totales.t_saldo_capital_5, 2) }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_creditos_6 }}
												</th>
												<th class="light font-11">
													{{ totales.t_cantidad_clientes_6 }}
												</th>
												<th class="light text-right font-11" colspan="2">
													S/ {{ roundTo(totales.t_saldo_capital_6, 2) }}
												</th>

												<th class="black text-right font-11" colspan="4">
													S/ {{ roundTo(totales.t_capital_seleccion_total, 2) }}
												</th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<hr />
							<div class="text-right">
								<button
									class="btn btn-cancel btn-icon-split"
									title="Exportar"
									@click="Exportar"
									:disabled="cartera_mora.length == 0"
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
		</div>
	</div>
</template>

<script>
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: {
		headerCloseModal,
		// rptControlMora,
	},

	data() {
		return {
			usuarios: [],

			agencia_seleccionada: 0,

			agencias_permitidas: [],

			fecha_desde: null,
			fecha_hasta: null,

			windowWidth: window.innerWidth,

			filtro_usuario: false,
			marcar_todo: false,

			usuarios_filtrados: [],
			riesgos_seleccionados: [3, 4, 5, 6],
			usuarios_seleccionados: [],

			cartera_mora: [],
			totales: [],
			lista_asesores: [],
			tipo_riesgos: [],

			mostrar_habilitados: true,
		};
	},

	watch: {
		agencia_seleccionada() {
			this.FechaActual();
			this.mostrar_habilitados = true;
			this.marcar_todo = false;

			this.FiltrarUsuarios();
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
		},
		cartera_mora(value) {
			$("#tblCarteraMora1").DataTable().destroy();
			this.TablaCarteraMora();
		},

		usuarios_filtrados() {
			$("#tblUsuarios1").DataTable().destroy();
			this.TablaUsuarios1();
		},
		filtro_usuario() {
			this.FiltrarUsuarios();
		},
	},

	mounted() {
		this.TablaCarteraMora();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		destruirtabla() {
			$("#tblCarteraMora1").DataTable().clear();
			$("#tblCarteraMora1").DataTable().destroy();
		},
		async FechaActual() {
			if (this.agencia_seleccionada == 0) {
				return false;
			} else {
				let fecha_actual = await this.$parent.fecha_hora_actual(
					this.agencia_seleccionada
				);

				fecha_actual = fecha_actual.substring(0, 10);

				this.fecha_desde = fecha_actual;
				this.fecha_hasta = fecha_actual;
			}
		},

		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
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

		async Actualizar() {
			let self = this;

			await axios
				.get(route("rep.cre.mora_agencia.listar_recursos"))
				.then(function (response) {
					self.usuarios = response.data.usuarios;
				});

			self.filtro_usuario = false;

			self.totales = [];

			self.cartera_mora = [];
			self.totales = [];
			self.lista_asesores = [];
			self.tipo_riesgos = [];

			self.usuarios_filtrados = [];
			self.riesgos_seleccionados = [3, 4, 5, 6];
			self.usuarios_seleccionados = [];
		},

		MarcarTodo() {
			this.usuarios_seleccionados = [];
			if (this.marcar_todo) {
				this.usuarios_filtrados.forEach((element) => {
					this.usuarios_seleccionados.push(element.dni);
				});
			}
		},
		FiltrarUsuarios() {
			this.usuarios_filtrados = [];
			this.usuarios_seleccionados = [];

			if (this.filtro_usuario) {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);

				if (this.mostrar_habilitados) {
					this.usuarios_filtrados = this.usuarios.filter(
						(item) =>
							item.agencia_id == this.agencia_seleccionada &&
							item.habilitado == 1
					);
				}

				if (this.marcar_todo) {
					this.usuarios_filtrados.forEach((element) => {
						this.usuarios_seleccionados.push(element.dni);
					});
				}
			}
		},
		TablaUsuarios1() {
			this.$nextTick(() => {
				$("#tblUsuarios1").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
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
			});
		},
		TablaCarteraMora() {
			this.$nextTick(() => {
				var table = $("#tblCarteraMora1").DataTable({
					scrollY: "300px",
					scrollX: true,
					fixedColumns: {
						leftColumns: 0,
					},
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
					select: {
						style: "single",
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

				if (this.cartera_mora.length > 0) {
					$("#tblCarteraMora1 .dataTables_empty").css("display", "none");
				}
			});
		},
		Buscar() {
			let self = this;
			if (this.filtro_usuario && this.usuarios_seleccionados.length == 0) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe seleccionar al menos un asesor",
				});

				return false;
			}

			let data = new FormData();
			data.append("agencia_id", this.agencia_seleccionada);
			data.append("fecha_desde", this.fecha_desde);
			data.append("fecha_hasta", this.fecha_hasta);
			data.append("filtro_usuario", this.filtro_usuario);
			data.append("tipo", "Xagencia");
			data.append(
				"riesgos_seleccionados",
				JSON.stringify(this.riesgos_seleccionados)
			);

			if (this.filtro_usuario) {
				data.append("usuarios", JSON.stringify(this.usuarios_seleccionados));
			}

			Swal.fire({
				title: "BUSCANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: async () => {
					// this.$inertia.post(route("rep.cre.mora_agencia.buscar"), data);
					// return false;

					Swal.showLoading();
					await axios
						.post(route("rep.cre.mora_agencia.buscar"), data)
						.then(function (response) {
							// return Swal.fire({
							//   icon: "success",
							//   title: "¡ÉXITO!",
							//   allowOutsideClick: true,
							// });
							if (response.data.length == 0) {
								self.cartera_mora = [];
								self.lista_asesores = [];
								self.tipo_riesgos = [];
								self.totales = [];
								return Swal.fire({
									icon: "info",
									title: "¡Ups!",
									text: "No se encontraron datos",
									allowOutsideClick: true,
								});
							} else {
								self.cartera_mora = response.data;
								self.totales = self.CalcularTotales(response.data);
								// self.tipo_riesgos = response.data.tipo_riesgos;
								return Swal.fire({
									icon: "success",
									title: "¡Listo!",
									timer: 1200,
									showConfirmButton: false,
								});
							}
						});
				},
			});
		},
		CalcularTotales(array) {
			let obj = {
				t_saldo_total: 0,
				t_cantidad_creditos: 0,
				t_clientes_activos: 0,
				p_tasa_promedio: 0,
				cantidad: 0,
				espaciador: null,

				t_cantidad_creditos_1: 0,
				t_cantidad_clientes_1: 0,
				t_saldo_capital_1: 0,
				espaciador_1: null,

				t_cantidad_creditos_2: 0,
				t_cantidad_clientes_2: 0,
				t_saldo_capital_2: 0,
				espaciador_2: null,

				t_cantidad_creditos_3: 0,
				t_cantidad_clientes_3: 0,
				t_saldo_capital_3: 0,
				espaciador_3: null,

				t_cantidad_creditos_4: 0,
				t_cantidad_clientes_4: 0,
				t_saldo_capital_4: 0,
				espaciador_4: null,

				t_cantidad_creditos_5: 0,
				t_cantidad_clientes_5: 0,
				t_saldo_capital_5: 0,
				espaciador_5: null,

				t_cantidad_creditos_6: 0,
				t_cantidad_clientes_6: 0,
				t_saldo_capital_6: 0,
				espaciador_6: null,

				espaciador_7: null,
				espaciador_8: null,
				espaciador_9: null,
				t_capital_seleccion_total: 0,
			};

			array.forEach((element_2) => {
				obj.t_saldo_total += parseFloat(element_2.saldo_total);
				obj.t_cantidad_creditos += parseFloat(element_2.cantidad_creditos);
				obj.t_clientes_activos += parseFloat(element_2.clientes_activos);
				obj.p_tasa_promedio += parseFloat(element_2.tasa_promedio);
				obj.cantidad += 1;

				obj.t_cantidad_creditos_1 += parseFloat(element_2.cantidad_creditos_1);
				obj.t_cantidad_clientes_1 += parseFloat(element_2.cantidad_clientes_1);
				obj.t_saldo_capital_1 += parseFloat(element_2.saldo_capital_1);

				obj.t_cantidad_creditos_2 += parseFloat(element_2.cantidad_creditos_2);
				obj.t_cantidad_clientes_2 += parseFloat(element_2.cantidad_clientes_2);
				obj.t_saldo_capital_2 += parseFloat(element_2.saldo_capital_2);

				obj.t_cantidad_creditos_3 += parseFloat(element_2.cantidad_creditos_3);
				obj.t_cantidad_clientes_3 += parseFloat(element_2.cantidad_clientes_3);
				obj.t_saldo_capital_3 += parseFloat(element_2.saldo_capital_3);

				obj.t_cantidad_creditos_4 += parseFloat(element_2.cantidad_creditos_4);
				obj.t_cantidad_clientes_4 += parseFloat(element_2.cantidad_clientes_4);
				obj.t_saldo_capital_4 += parseFloat(element_2.saldo_capital_4);

				obj.t_cantidad_creditos_5 += parseFloat(element_2.cantidad_creditos_5);
				obj.t_cantidad_clientes_5 += parseFloat(element_2.cantidad_clientes_5);
				obj.t_saldo_capital_5 += parseFloat(element_2.saldo_capital_5);

				obj.t_cantidad_creditos_6 += parseFloat(element_2.cantidad_creditos_6);
				obj.t_cantidad_clientes_6 += parseFloat(element_2.cantidad_clientes_6);
				obj.t_saldo_capital_6 += parseFloat(element_2.saldo_capital_6);

				obj.t_capital_seleccion_total += parseFloat(
					element_2.capital_seleccion_total
				);
			});

			return obj;
		},
		Exportar() {
			let data = new FormData();
			data.append("cartera_mora", JSON.stringify(this.cartera_mora));
			data.append("totales", JSON.stringify(this.totales));
			Swal.fire({
				title: "EXPORTANDO",
				text: "Espere porfavor...",
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();

					// this.$inertia.post(
					//   route("rep.cre.control_mora_agencia.exportar"),
					//   data
					// );
					// return false;

					axios
						.post(route("rep.cre.mora_agencia.exportar"), data)
						.then(function (response) {
							let path_xlsx = response.data.path_xlsx;

							const link = document.createElement("a");
							link.href = origin + path_xlsx;
							link.download = "rptControlMoraAgencia.xlsx";
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
	},
};
</script>


<style lang="css">
.mdlControlMoraAgencia {
	width: 80% !important;
	margin-left: 10% !important;
}
.plomo-oscuro {
	background: var(--plomoOscuroEmpresarial) !important;
}

.black {
	background: black !important;
}

.light {
	background: var(--azulClaroEmpresarial) !important;
	color: var(--plomoOscuroEmpresarial) !important;
}

.white {
	background: none !important;
}

.font-11 {
	font-size: 11px !important;
}

/* #tblCarteraMora .dataTables_empty {
	display: none;
} */

@media (max-width: 900px) {
	.mdlControlMoraAgencia {
		width: 98% !important;
		margin-left: 1% !important;
	}
}
</style>
