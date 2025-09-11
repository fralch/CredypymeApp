<template>
	<layout ref="layout">
		<div class="slot_body slot_mover_clientes" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'MOVER CLIENTES'"></headerClose>

					<div class="card-title">PANEL DE BÚSQUEDA</div>

					<div class="card-body card-block">
						<div class="form-row row justify-content-md-center">
							<div class="col-md-4">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">AGENCIA</span>
									</div>
									<select
										class="form-control center"
										v-model="agencia_seleccionada"
										@change="FiltrarAsesoresOrigen"
									>
										<option
											v-for="(agencia, index) in agencias_permitidas"
											:key="index"
											:value="agencia.id"
											:selected="index === 0"
										>
											{{ agencia.agencia }}
										</option>
									</select>
								</div>
							</div>

							<div
								class="input-group col-md-3"
								:style="
									windowWidth >= 900 ? '' : 'margin-left: 75px !important'
								"
							>
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input
											type="checkbox"
											id="chbSeleccionarClientes"
											v-model="escoger_clientes"
										/>
									</div>
								</div>
								<div class="input-group-append">
									<label
										class="input-group-text prepend-title"
										for="chbSeleccionarClientes"
										style="font-size: 13px"
									>
										ESCOGER CLIENTES
									</label>
								</div>
							</div>
						</div>
						<br />

						<div class="form-row row justify-content-md-center">
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text">DE</span>
								</div>
								<select class="form-control center" v-model="asesor_origen">
									<option value="0" disabled selected>Seleccione</option>
									<option
										v-for="(item, index) in asesores_filtrados_origen"
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
											id="chbHabilitadosOrigen"
											v-model="solo_habilitados_origen"
										/>
										<label class="m-0 ml-1" for="chbHabilitadosOrigen"
											>Habilitados</label
										>
									</div>
								</div>
							</div>

							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text" v-if="windowWidth >= 900"
										>PARA</span
									>
									<span class="input-group-text" v-if="windowWidth < 900"
										>A</span
									>
								</div>
								<select class="form-control center" v-model="asesor_destino">
									<option value="0" disabled selected>Seleccione</option>
									<option
										v-for="(item, index) in asesores_filtrados_destino"
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
											id="chbHabilitadosDestino"
											v-model="solo_habilitados_destino"
											:disabled="asesores_filtrados_destino.length == 0"
										/>
										<label class="m-0 ml-1" for="chbHabilitadosDestino"
											>Habilitados</label
										>
									</div>
								</div>
							</div>
						</div>
						<div class="text-center mt-2">
							<button
								class="btn btn-action btn-icon-split mb-1"
								@click="Mover()"
								title="Mover"
								style="font-size: 15px"
							>
								<span class="icon text-white">
									<i class="fas fa-sync-alt"></i>
								</span>
								<span class="text">MOVER</span>
							</button>
						</div>

						<div class="form-row">
							<div class="form-group col-6">
								<table class="table" id="tblClientesOrigen" width="100%">
									<thead>
										<tr>
											<th colspan="3">CLIENTES {{ titulo_tabla_origen }}</th>
										</tr>
										<tr>
											<th></th>
											<th></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in clientes_origen" :key="index">
											<td
												align="center"
												:style="[escoger_clientes ? 'p-0' : 'p-3']"
											>
												{{ index + 1 }}
											</td>
											<td
												align="center"
												:class="[escoger_clientes ? 'p-0' : 'p-3']"
											>
												<div
													class="custom-control custom-checkbox mt-2"
													v-if="escoger_clientes == true"
												>
													<input
														type="checkbox"
														class="custom-control-input"
														:id="'chbClienteDe_' + index"
														:value="{
															cliente_id: item.id,
															dni: item.dni,
															apellido_paterno: item.apellido_paterno,
															apellido_materno: item.apellido_materno,
															nombres: item.nombres,
														}"
														v-model="clientes_seleccionados"
													/>

													<label
														class="custom-control-label"
														:for="'chbClienteDe_' + index"
													>
													</label>
												</div>
											</td>
											<td :class="[escoger_clientes ? 'p-0' : 'p-3']">
												{{ item.apellido_paterno }}
												{{ item.apellido_materno }}
												{{ item.nombres }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="form-group col-6">
								<table class="table" id="tblClientesDestino" width="100%">
									<thead>
										<tr>
											<th colspan="2">CLIENTES {{ titulo_tabla_destino }}</th>
										</tr>
										<tr>
											<th></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in clientes_destino" :key="index">
											<td align="center" class="p-3">{{ index + 1 }}</td>
											<td class="p-3">
												{{ item.apellido_paterno }}
												{{ item.apellido_materno }}
												{{ item.nombres }}
											</td>
										</tr>
									</tbody>
								</table>
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
	props: {
		usuarios: Array,
	},
	components: {
		layout,
		headerClose,
	},

	data() {
		return {
			windowWidth: window.innerWidth,

			agencias_permitidas: [],
			agencia_seleccionada: null,

			asesores_filtrados_origen: [],
			asesores_filtrados_destino: [],

			asesor_origen: 0,
			clientes_origen: [],
			solo_habilitados_origen: true,

			asesor_destino: 0,
			clientes_destino: [],
			solo_habilitados_destino: true,

			clientes_seleccionados: [],
			escoger_clientes: true,

			titulo_tabla_origen: "ORIGEN",
			titulo_tabla_destino: "DESTINO",
		};
	},

	watch: {
		agencias_permitidas(value) {
			let agencia_id = this.$inertia.page.props.user_session.id_agencia;
			let mi_agencia = value.filter((item) => item.id == agencia_id);

			if (mi_agencia.length > 0) {
				this.agencia_seleccionada = mi_agencia[0].id;
				this.FiltrarAsesoresOrigen();
			} else {
				if (value.length > 0) {
					this.agencia_seleccionada = value[0].id;
					this.FiltrarAsesoresOrigen();
				} else {
					this.agencia_seleccionada = null;
				}
			}
		},
		clientes_origen() {
			$("#tblClientesOrigen").DataTable().destroy();
			this.TablaClientesOrigen();
		},
		clientes_destino() {
			$("#tblClientesDestino").DataTable().destroy();
			this.TablaClientDestino();
		},
		asesor_origen() {
			let u = this.ObtenerUsuario(this.asesor_origen);
			this.titulo_tabla_origen = "DE " + u.toUpperCase();

			this.FiltrarAsesoresDestino();
			this.ListarClientes("origen");
		},
		asesor_destino() {
			let u = this.ObtenerUsuario(this.asesor_destino);
			this.titulo_tabla_destino = "PARA " + u.toUpperCase();
			this.ListarClientes("destino");
		},
		solo_habilitados_origen() {
			this.FiltrarAsesoresOrigen();
		},
		solo_habilitados_destino() {
			this.FiltrarAsesoresDestino();
		},
	},
	mounted() {
		this.ListarAgencias();
		this.TablaClientesOrigen();
		this.TablaClientDestino();
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});
	},

	methods: {
		ListarAgencias() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CLIENTES/MOVIMIENTO"
			);
		},
		TablaClientesOrigen() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "250px";
				}
				var table = $("#tblClientesOrigen").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
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
		TablaClientDestino() {
			this.$nextTick(() => {
				let scroll_height = "350px";
				if (this.windowWidth <= 900) {
					scroll_height = "250px";
				}
				var table = $("#tblClientesDestino").DataTable({
					scrollY: scroll_height,
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					ordering: false,
					fixedHeader: true,
					info: false,
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

		ObtenerUsuario(usuario) {
			if (usuario == 0) {
				return "";
			}
			let u = this.usuarios.find((usuario_) => usuario_.dni == usuario);
			return u.usuario;
		},
		FiltrarAsesoresOrigen() {
			this.asesor_origen = 0;

			if (this.solo_habilitados_origen) {
				this.asesores_filtrados_origen = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_seleccionada &&
						item.habilitado == 1 &&
						item.dni != this.asesor_destino
				);
			} else {
				this.asesores_filtrados_origen = this.usuarios.filter(
					(item) =>
						item.agencia_id == this.agencia_seleccionada &&
						item.dni != this.asesor_destino
				);
			}
		},
		FiltrarAsesoresDestino() {
			this.asesor_destino = 0;
			if (this.asesor_origen != 0) {
				if (this.solo_habilitados_destino) {
					this.asesores_filtrados_destino = this.usuarios.filter(
						(item) =>
							item.agencia_id == this.agencia_seleccionada &&
							item.habilitado == 1 &&
							item.dni != this.asesor_origen
					);
				} else {
					this.solo_habilitados_destino = this.usuarios.filter(
						(item) =>
							item.agencia_id == this.agencia_seleccionada &&
							item.dni != this.asesor_origen
					);
				}
			}
		},
		ListarClientes(tipo) {
			let self = this;
			if (tipo == "origen") {
				if (this.asesor_origen != 0) {
					this.clientes_seleccionados = [];

					let data = new FormData();
					data.append("agencia_id", this.agencia_seleccionada);
					data.append("asesor_id", this.asesor_origen);
					axios
						.post(route("cli.movimiento.listar_clientes"), data)
						.then(function (response) {
							self.clientes_origen = response.data.lista_clientes;
						});
				} else {
					return false;
				}
			} else if (tipo == "destino") {
				if (this.asesor_destino != 0) {
					let data = new FormData();
					data.append("agencia_id", this.agencia_seleccionada);
					data.append("asesor_id", this.asesor_destino);
					axios
						.post(route("cli.movimiento.listar_clientes"), data)
						.then(function (response) {
							self.clientes_destino = response.data.lista_clientes;
						});
				} else {
					return false;
				}
			}
		},
		Mover() {
			if (this.asesor_destino == 0 || this.asesor_origen == 0) {
				Swal.fire({
					icon: "info",
					title: "¡Ups!",
					text: "Seleccionar asesores",
				});
				return false;
			}

			if (this.escoger_clientes && this.clientes_seleccionados.length == 0) {
				Swal.fire({
					icon: "info",
					title: "¡Ups!",
					text: "No hay ningún cliente seleccionado",
				});
				return false;
			}

			Swal.fire({
				title: "MOVER CLIENTES",
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
					data.append("agencia_id", this.agencia_seleccionada);
					data.append("asesor_origen", this.asesor_origen);
					data.append("asesor_destino", this.asesor_destino);
					data.append("escoger_clientes", this.escoger_clientes);

					if (this.escoger_clientes) {
						data.append(
							"clientes_seleccionados",
							JSON.stringify(this.clientes_seleccionados)
						);
					}

					this.$inertia.post(route("cli.movimiento.mover"), data, {
						preserveScroll: true,
						onStart: () => {
							Swal.fire({
								title: "MOVIENDO",
								text: "Espere porfavor...",
								showConfirmButton: false,
								allowOutsideClick: false,
								willOpen: () => {
									Swal.showLoading();
								},
							});
						},
						onSuccess: () => {
							this.ListarClientes("origen");
							this.ListarClientes("destino");

							return Swal.fire({
								icon: "success",
								title: "¡ÉXITO!",
								allowOutsideClick: false,
							});
						},
					});
				}
			});
		},
	},
};
</script>



<style lang="css">
.slot_mover_clientes {
	width: 60% !important;
	margin-left: 25% !important;
}

.form-check-input,
.label-title:hover {
	cursor: pointer;
}
@media (max-width: 900px) {
	.slot_mover_clientes {
		width: 99% !important;
		margin-left: 0.5% !important;
	}
}
</style>

