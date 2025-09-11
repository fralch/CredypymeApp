<template>
	<layout ref="layout">
		<div class="slot_body slot-cierre-dia" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CIERRE DE DÍA'"></headerClose>
					<div class="card-body card-block">
						<div class="form-row mb-2">
							<div class="input-group col-md-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">AGENCIA</span>
								</div>
								<select
									class="form-control center"
									v-model="agencia_seleccionada"
									@change="FiltrarSesiones"
								>
									<option
										v-for="(item, index) in agencias_permitidas"
										:key="index"
										:value="item.id"
									>
										{{ item.agencia }}
									</option>
								</select>
							</div>
							<div class="input-group col-md-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title"
										>FECHA ACTUAL</span
									>
								</div>
								<input
									class="form-control center"
									type="text"
									:value="fecha_actual"
									:disabled="true"
								/>
							</div>
						</div>

						<label class="label-title">USUARIOS CONECTADOS</label>
						<table
							class="table table-hover"
							id="tblUsuariosConectados"
							width="100%"
						>
							<thead>
								<tr>
									<th></th>
									<th>USUARIO</th>
									<th>ÚLTIMA_ACCIÓN</th>
									<th>DISPOSITIVO</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(usuario, index) in sesiones_filtradas" :key="index">
									<td class="table-bordered" align="center">
										<i
											class="fas fa-circle conectado"
											v-if="usuario.conectado == 1"
										></i>
										<i
											class="far fa-circle desconectado"
											v-if="usuario.conectado == 0"
										></i>
									</td>
									<td class="table-bordered" align="center">
										{{ usuario.usuario }}
									</td>
									<td class="table-bordered" align="center">
										{{
											usuario.datos_sesion == null ? "-" : usuario.ultima_accion
										}}
									</td>
									<td class="table-bordered" align="center">
										{{
											usuario.datos_sesion == null || usuario.conectado == 0
												? "-"
												: JSON.parse(usuario.datos_sesion).tipo_dispositivo
										}}
									</td>
								</tr>
							</tbody>
						</table>

						<hr />
						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								@click="CerrarDia"
								v-if="agencias_permitidas.length > 0"
							>
								<span class="icon text-white">
									<i class="far fa-calendar-plus"></i
								></span>
								<span class="text">CERRAR DÍA</span>
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

export default {
	components: { layout, headerClose },
	props: { sesiones: Array },
	data() {
		return {
			agencias: [],
			agencias_permitidas: [],
			agencia_seleccionada: 0,

			fecha_actual: null,
			sesiones_filtradas: [],
			actualizar_sesiones: true,
			timer: "",

			cajas_abiertas_usuarios: [],
			carrito_abierto_usuarios: [],
			usuarios_faltantes: " ",
			carritos_faltantes: " ",
		};
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
			this.FiltrarSesiones();
		},

		agencia_seleccionada(value) {
			this.FechaActual();
		},
	},
	mounted() {
		this.ListarAgenciasPermitidas();
		this.RevisarOnline();
		this.TablaUsuariosConectados();

		this.timer = setInterval(() => {
			if (this.actualizar_sesiones) {
				this.RevisarOnline();
			}
		}, 2000);
	},
	methods: {
		FechaActual() {
			let self = this;

			axios
				.post(
					route("fecha_hora_agencia", {
						agencia_id: self.agencia_seleccionada,
					})
				)
				.then(function (response) {
					self.fecha_actual = response.data.substring(0, 10);
				});
		},

		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CAJA/CIERRE_DIA"
			);
		},
		FiltrarSesiones() {
			this.sesiones_filtradas = this.sesiones.filter(
				(item) => item.agencia_id == this.agencia_seleccionada
			);
		},
		RevisarOnline() {
			let self = this;
			axios.get(route("usuarios_online")).then((res) => {
				self.sesiones_filtradas = res.data.filter(
					(item) => item.agencia_id == self.agencia_seleccionada
				);
			});
		},
		TablaUsuariosConectados() {
			this.$nextTick(() => {
				var table = $("#tblUsuariosConectados").DataTable({
					scrollY: "300px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
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
					responsive: true,
				});
			});
		},

		VerificarCierreDia() {
			let self = this;

			// this.$inertia.post(route("caj.cierre_dia.verificar",self.agencia_seleccionada));
			// return false
			return axios
				.post(route("caj.cierre_dia.verificar", self.agencia_seleccionada))
				.then(function (response) {
					self.cajas_abiertas_usuarios = response.data.cajas_abiertas_usuarios;

					for (let i = 0; i < self.cajas_abiertas_usuarios.length; i++) {
						if (i == 0) {
							self.usuarios_faltantes = self.cajas_abiertas_usuarios[i].usuario;
						} else {
							self.usuarios_faltantes =
								self.usuarios_faltantes +
								" / " +
								self.cajas_abiertas_usuarios[i].usuario;
						}
					}
					return response.data;
				});
		},
		VerificarCarritos() {
			let self = this;

			//   this.$inertia.post(
			//     route("cre.carrito.verificar_carritos", self.agencia_seleccionada)
			//   );
			//   return false;
			return axios
				.post(
					route("cre.carrito.verificar_carritos", self.agencia_seleccionada)
				)
				.then(function (response) {
					self.carrito_abierto_usuarios =
						response.data.carrito_abierto_usuarios;

					for (let i = 0; i < self.carrito_abierto_usuarios.length; i++) {
						if (i == 0) {
							self.carritos_faltantes =
								self.carrito_abierto_usuarios[i].usuario;
						} else {
							self.carritos_faltantes =
								self.carritos_faltantes +
								" / " +
								self.carrito_abierto_usuarios[i].usuario;
						}
					}
					return response.data;
				});
		},

		async CerrarDia() {
			let self = this;

			this.actualizar_sesiones = false;

			let resultados = await this.VerificarCierreDia();
			let resultados2 = await this.VerificarCarritos();

			if (resultados.transferencias_pendientes_caja > 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Hay transferencias PENDIENTES en CAJA",
					allowOutsideClick: false,
				});

				return false;
			} else if (resultados.transferencias_pendientes_cuenta > 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Hay transferencias PENDIENTES en CUENTA",
					allowOutsideClick: false,
				});

				return false;
			} else if (resultados.cajas_abiertas > 0) {
				let mensaje = "Hay CAJAS que faltan cerrar.";
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					html:
						'<label class="p-2" style="font-weight: bolder;">' +
						mensaje +
						'</label><br><label class="p-2 bg-secondary" style="font-weight: bolder; color: white">' +
						self.usuarios_faltantes +
						"</label>",

					allowOutsideClick: false,
				});

				return false;
			} else if (resultados2.carrito_abierto_usuarios.length > 0) {
				let mensaje = "Hay CARRITOS que faltan cerrar.";
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					html:
						'<label class="p-2" style="font-weight: bolder;">' +
						mensaje +
						'</label><br><label class="p-2 bg-secondary" style="font-weight: bolder; color: white">' +
						self.carritos_faltantes +
						"</label>",

					allowOutsideClick: false,
				});

				return false;
			} else if (resultados.desembolsos_no_facturados > 0) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Hay DESEMBOLSOS pendientes de FACTURACIÓN.",
					allowOutsideClick: false,
				});

				return false;
			} else if (resultados.aprobaciones_pendientes > 0) {
				Swal.fire({
					icon: "warning",
					text: "Hay CRÉDITOS APROBADOS, pendientes de desembolso,¿Desea anularlos?",
					confirmButtonText: '<i class="fas fa-check"></i>   Si',
					confirmButtonColor: "var(--colorAlto)",
					showCancelButton: true,
					cancelButtonText: '<i class="fas fa-times"></i>   No',
					cancelButtonColor: "var(--plomoOscuroEmpresarial)",
					allowOutsideClick: false,
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							title: "INGRESE COMENTARIO DE ANULACIÓN",
							text: "",
							input: "text",
							customClass: {
								input: "mayus",
							},
							confirmButtonText:
								'<i class="fas fa-check" style="color:white;"></i>   Si',
							confirmButtonColor: "var(--colorAlto)",
							showCancelButton: true,
							cancelButtonText: '<i class="fas fa-times"></i>   No',
							cancelButtonColor: "var(--plomoOscuroEmpresarial)",
							allowOutsideClick: false,
							preConfirm: (resultado) => {
								resultado = resultado.toUpperCase();
								if (resultado.length < 15) {
									Swal.showValidationMessage(
										"El comentario debe tener al menos 15 caracteres."
									);
									return false;
								}
								self.$inertia.post(
									route("cre.anular_aprobacion_todos"),
									{
										agencia_id: self.agencia_seleccionada,
										comentario_anulacion: resultado,
									},
									{
										preserveScroll: true,
										onStart: (visit) => {
											let timerInterval;
											Swal.fire({
												title: "EN PROGRESO",
												html: "Espere porfavor...",
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
												text: "Aprobaciones ANULADAS con éxito.",
												allowOutsideClick: false,
												preConfirm: (result) => {
													// this.ListarCreditos();
												},
											});
										},
									}
								);
							},
						});
					} else {
						return false;
					}
				});
				return false;
			}

			// this.$inertia.post(
			// 	route("caj.cierre_dia.cerrar", this.agencia_seleccionada)
			// );

			// return false;

			Swal.fire({
				title: "CERRAR DÍA",
				text: "¿Desea continuar?",
				confirmButtonText: '<i class="fas fa-check"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					this.$inertia.post(
						route("caj.cierre_dia.cerrar", this.agencia_seleccionada),
						{},
						{
							preserveScroll: true,
							onStart: () => {
								Swal.fire({
									title: "CERRANDO DÍA",
									text: "Espere porfavor...",
									showConfirmButton: false,
									allowOutsideClick: false,
									willOpen: () => {
										Swal.showLoading();
									},
								});
							},
							onSuccess: () => {
								Swal.fire({
									icon: "success",
									title: "¡DÍA CERRADO!",
									timer: 1200,
									showConfirmButton: false,
								}).then((result) => {
									if (result.isConfirmed) {
										this.actualizar_sesiones = false;
									}
								});
							},
						}
					);
				} else {
					this.actualizar_sesiones = true;
				}
			});
		},
	},
	beforeDestroy() {
		clearInterval(this.timer);
	},
};
</script>

<style lang="css">
.slot-cierre-dia {
	width: 40% !important;
	margin-left: 30% !important;
	margin-top: 5% !important;
}

.conectado {
	color: var(--verdeOscuroEmpresarial) !important;
}

.desconectado {
	color: var(--azulOscuroEmpresarial) !important;
}

@media (max-width: 500px) {
	.slot-cierre-dia {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

