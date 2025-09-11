<template>
	<layout ref="layout">
		<div class="slot_body slot_listado_registro" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'LISTADO Y REGISTRO DE CLIENTES'"></headerClose>

					<div class="card-title">PANEL DE BÚSQUEDA</div>
					<div class="card-body card-block">
						<div class="form-row row justify-content-md-center">
							<div class="form-group col-md-2 col-6">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="listado_registro_filtro"
										id="rdbApellidosNombresListadoRegistro"
										value="apellidos_nombres"
										v-model="tipo_filtro"
									/>
									<label
										class="label-title"
										for="rdbApellidosNombresListadoRegistro"
										>APELLIDOS_NOMBRES
									</label>
								</div>
							</div>
							<div class="form-group col-md-2 col-6">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="listado_registro_filtro"
										id="rdbDniListadoRegistro"
										value="dni"
										v-model="tipo_filtro"
									/>
									<label class="label-title" for="rdbDniListadoRegistro"
										>DNI</label
									>
								</div>
							</div>
							<div class="form-group col-md-2 col-4">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="listado_registro_filtro"
										id="rdbExpedienteListadoRegistro"
										value="expediente"
										v-model="tipo_filtro"
									/>
									<label class="label-title" for="rdbExpedienteListadoRegistro"
										>EXPEDIENTE
									</label>
								</div>
							</div>
							<div class="form-group col-md-2 col-4">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="listado_registro_filtro"
										id="rdbDireccionListadoRegistro"
										value="direccion"
										v-model="tipo_filtro"
									/>
									<label class="label-title" for="rdbDireccionListadoRegistro"
										>DIRECCIÓN
									</label>
								</div>
							</div>
							<div class="form-group col-md-2 col-4">
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="listado_registro_filtro"
										id="rdbCelularListadoRegistro"
										value="celular"
										v-model="tipo_filtro"
									/>
									<label class="label-title" for="rdbCelularListadoRegistro"
										>CELULAR</label
									>
								</div>
							</div>
						</div>
					</div>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="col-md-5 col-10">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" v-if="windowWidth >= 900"
											>BUSCAR</span
										>

										<span class="input-group-text" v-if="windowWidth < 900"
											><i class="fas fa-search"></i
										></span>
									</div>

									<input
										class="form-control mayus"
										type="text"
										placeholder="Ingrese 3 caractéres como mínimo..."
										@keyup="BuscarClientes"
										v-model="texto_buscar"
										autocomplete="off"
										@focus="hidenav()"
										@blur="shownav()"
										spellcheck="false"
										autofocus
									/>
								</div>
							</div>
							<div class="col-md-2 col-1">
								<button
									class="btn btn-action btn-icon-split mb-1"
									@click="Nuevo()"
									title="Nuevo CLIENTE"
								>
									<span class="icon text-white">
										<i class="fas fa-plus"></i>
									</span>
									<div class="d-md-block" v-if="windowWidth >= 900">
										<span class="text">NUEVO</span>
									</div>
								</button>
							</div>
							<div class="respon col-md-4 col-12 offset-md-1">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">AGENCIA</span>
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
											:selected="index === 0"
										>
											{{ agencia.agencia }}
										</option>
									</select>
								</div>
							</div>
						</div>

						<table class="table" id="tblClientes" width="100% !important">
							<thead>
								<tr>
									<th style="min-width: 60px !important">ACCIONES</th>

									<th style="min-width: 150px !important">AP_PATERNO</th>
									<th style="min-width: 150px !important">AP_MATERNO</th>
									<th style="min-width: 200px !important">NOMBRES</th>
									<th style="min-width: 75px !important">DNI</th>
									<th style="min-width: 75px !important">EXPEDIENTE</th>
									<th style="min-width: 75px !important">ASESOR</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in lista_clientes"
									:key="index"
									@dblclick="DatosPersonales(item)"
									:class="[index % 2 == 0 ? 'verde-claro' : '']"
								>
									<td class="table-bordered" align="center" width="30px">
										<div class="btn-group" role="group">
											<button
												class="btn btn-cancel btn-icon-split"
												@click="ParientesAvalesNegocio(item)"
												title="Ver parientes, avales y negocio"
											>
												<span class="icon text-white">
													<i class="fas fa-users-cog"></i>
												</span>
											</button>
											<button
												class="btn btn-danger btn-icon-split"
												@click="Eliminar(item.id)"
												title="ELIMINAR cliente"
												v-if="permiso_eliminar_cliente"
											>
												<span class="icon text-white">
													<i class="fas fa-trash"></i>
												</span>
											</button>
										</div>
									</td>

									<td class="table-bordered">
										{{ item.apellido_paterno }}
									</td>
									<td class="table-bordered">
										{{ item.apellido_materno }}
									</td>
									<td class="table-bordered">
										{{ item.nombres }}
									</td>

									<td class="table-bordered" align="center">
										{{ item.dni }}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.codigo_expediente == null
												? "-"
												: item.codigo_expediente
										}}
									</td>
									<td class="table-bordered" align="center">
										{{
											item.usuario_asesor == null ? "-" : item.usuario_asesor
										}}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<!-- The Modal -->

				<mdlParientesAvalesNegocios
					:agencias="agencias"
					:departamentos="departamentos"
					:provincias="provincias"
					:distritos="distritos"
					:agencia_seleccionada="agencia_seleccionada"
					ref="mdlParientesAvalesNegocios"
				></mdlParientesAvalesNegocios>

				<mdlDatosPersonales
					:agencias="agencias"
					:departamentos="departamentos"
					:provincias="provincias"
					:distritos="distritos"
					:agencia_seleccionada="agencia_seleccionada"
					ref="mdlDatosPersonales"
				></mdlDatosPersonales>
			</div>
		</div>
	</layout>
</template>

<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";
import mdlParientesAvalesNegocios from "@/Pages/Creditos/Clientes/Components/mdlParientesAvalesNegocios.vue";

import mdlDatosPersonales from "@/Pages/Creditos/Clientes/Components/mdlDatosPersonales.vue";
import headerClose from "@/Pages/Creditos/Components/header_close.vue";

export default {
	props: {
		distritos: Array,
		provincias: Array,
		departamentos: Array,
	},
	components: {
		layout,
		headerClose,
		mdlParientesAvalesNegocios,
		mdlDatosPersonales,
	},

	data() {
		return {
			windowWidth: window.innerWidth,
			//   altura: null,
			anchura: null,

			lista_clientes: [],
			agencias: [],
			agencias_permitidas: [],
			agencia_seleccionada: null,
			texto_buscar: "",
			tipo_filtro: "apellidos_nombres",
		};
	},

	watch: {
		lista_clientes() {
			$("#tblClientes").DataTable().destroy();
			this.TablaListaClientes();
		},
		tipo_filtro() {
			this.BuscarClientes();
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
	},
	computed: {
		permiso_eliminar_cliente() {
			let resultado = false;
			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == "CREDITOS_CLIENTES/ELIMINAR_CLIENTE"
				);

			if (permiso_detalle.length != 0) {
				let acceso_agencias = permiso_detalle[0].acceso_agencias;

				if (acceso_agencias != null) {
					acceso_agencias = JSON.parse(acceso_agencias);

					let agencia_autorizada = acceso_agencias.filter(
						(item) => item.agencia_id == this.agencia_seleccionada
					);

					if (agencia_autorizada.length != 0) {
						resultado = true;
					}
				}
			}
			return resultado;
		},
	},
	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
		});

		this.TablaListaClientes();

		this.listar_agencias();
	},

	methods: {
		listar_agencias() {
			this.agencias = this.$inertia.page.props.application.agencias;
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_CLIENTES/LISTADO_REGISTRO"
			);
		},
		hidenav() {
			return this.$refs.layout.hide_nav();
		},
		shownav() {
			return this.$refs.layout.show_nav();
		},

		TablaListaClientes() {
			this.$nextTick(() => {
				var table = $("#tblClientes").DataTable({
					scrollY: "400px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns: {
						leftColumns: 0,
					},
					ordering: false,
					fixedHeader: true,
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
		Nuevo() {
			if (this.agencia_seleccionada == null) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "No ha seleccionado una agencia, intente nuevamente.",
				});
				return false;
			}

			let mdlDatosPersonales = this.$refs.mdlDatosPersonales;
			let frmDatosCliente = this.$refs.mdlDatosPersonales.frmDatosCliente;
			frmDatosCliente.telefonos_original = [];

			mdlDatosPersonales.submited = false;
			mdlDatosPersonales.title_modal = "REGISTRAR CLIENTE";
			mdlDatosPersonales.ResetearFrmDatosPersonales();

			let data = new FormData();
			data.append(
				"cargos",
				JSON.stringify([
					"ASESOR DE NEGOCIOS",
					"JEFE DE CRÉDITOS",
					"PROMOTOR DE CRÉDITOS",
					"GERENTE DE CRÉDITOS",
					"COORDINADOR DE CRÉDITOS",
				])
			);
			data.append("agencia", [this.agencia_seleccionada]);
			axios
				.post(route("usuarios.listar_por_cargos_agencia"), data)
				.then(function (response) {
					mdlDatosPersonales.lista_asesores_promotores = response.data;
					mdlDatosPersonales.asesor_id = 0;
					mdlDatosPersonales.promotor_id = 0;
				});

			$("#mdlDatosPersonales").css("display", "block");

			let preview = $("#previzualizar img");
			preview.remove();
			$("#dni-previo img").remove();
		},

		DatosPersonales(item) {
			let mdlDatosPersonales = this.$refs.mdlDatosPersonales;
			let frmDatosCliente = this.$refs.mdlDatosPersonales.frmDatosCliente;

			mdlDatosPersonales.submited = false;
			mdlDatosPersonales.title_modal = "VER CLIENTE";

			frmDatosCliente.id = item.id;
			frmDatosCliente.modo = "NO-EDITAR";
			frmDatosCliente.dni = item.dni;
			frmDatosCliente.apellido_paterno = item.apellido_paterno;
			frmDatosCliente.apellido_materno = item.apellido_materno;
			frmDatosCliente.nombres = item.nombres;
			frmDatosCliente.fecha_nacimiento = item.fecha_nacimiento;
			frmDatosCliente.estado_civil = item.estado_civil;
			frmDatosCliente.sexo = item.sexo;
			frmDatosCliente.hijos = item.hijos;

			frmDatosCliente.agencia = item.agencia;
			frmDatosCliente.agencia_id = item.agencia_id;
			frmDatosCliente.correo_electronico = item.correo_electronico;

			frmDatosCliente.codigo_expediente = item.codigo_expediente;
			frmDatosCliente.asesor_id = item.asesor_id;
			frmDatosCliente.promotor_id = item.promotor_id;
			frmDatosCliente.central_riesgo = item.central_riesgo;
			frmDatosCliente.canal_referencia = item.canal_referencia;

			frmDatosCliente.monto_maximo = this.$refs.layout.round(
				item.monto_maximo,
				2
			);
			frmDatosCliente.notas = item.notas;
			frmDatosCliente.reportar_equifax = item.reportar_equifax;

			frmDatosCliente.direccion = item.direccion;
			frmDatosCliente.distrito_id = item.distrito_id;
			frmDatosCliente.provincia_id = item.provincia_id;
			frmDatosCliente.departamento_id = item.departamento_id;
			frmDatosCliente.referencia_direccion = item.referencia_direccion;
			let telefonos_1 = [];
			let telefonos_2 = [];

			if (typeof item.telefonos === "string") {
				telefonos_1 = JSON.parse(item.telefonos);
				telefonos_2 = JSON.parse(item.telefonos);
			} else {
				telefonos_1 = item.telefonos;
				telefonos_2 = item.telefonos;
			}
			frmDatosCliente.telefonos = telefonos_1;

			frmDatosCliente.telefonos_original = telefonos_2;

			$("#inpFoto").val("");
			frmDatosCliente.imagen_dni = item.imagen_dni;
			frmDatosCliente.observaciones = item.observaciones;

			let data = new FormData();
			data.append(
				"cargos",
				JSON.stringify([
					"ASESOR DE NEGOCIOS",
					"JEFE DE CRÉDITOS",
					"PROMOTOR DE CRÉDITOS",
					"GERENTE DE CRÉDITOS",
					"COORDINADOR DE CRÉDITOS",
				])
			);
			data.append("agencia", [this.agencia_seleccionada]);
			axios
				.post(route("usuarios.listar_por_cargos_agencia"), data)
				.then(function (response) {
					mdlDatosPersonales.lista_asesores_promotores = response.data;
				});

			axios
				.get(
					route("cli.listado_registro.cantidad_creditos", {
						agencia_id: item.agencia_id,
						cliente_id: item.id,
					})
				)
				.then(function (response) {
					let cantidad_creditos = response.data.cantidad_creditos;

					frmDatosCliente.cantidad_creditos = cantidad_creditos;
				});

			$("#mdlDatosPersonales").css("display", "block");

			if (item.imagen_dni != null) {
				let img_prev = $("#previzualizar img");
				img_prev.remove();

				let preview = document.getElementById("previzualizar"),
					image = document.createElement("img");

				let agencia = item.agencia_id;
				let año = item.imagen_dni.substring(0, 4);

				image.src =
					"/imagenes_server/creditos/clientes/dni/" +
					agencia +
					"/" +
					año +
					"/" +
					item.imagen_dni;

				image.style.width = "100%";
				image.style.height = "100%";
				image.style.border = "1px solid #ffff";

				preview.innerHTML = "";
				preview.append(image);
			}
		},

		ParientesAvalesNegocio(item) {
			let mdlParientesAvalesNegocios = this.$refs.mdlParientesAvalesNegocios;

			mdlParientesAvalesNegocios.frmParienteAval.cliente_id = item.id;
			mdlParientesAvalesNegocios.frmParienteAval.agencia_id = item.agencia_id;
			mdlParientesAvalesNegocios.frmNegocio.cliente_id = item.id;
			mdlParientesAvalesNegocios.frmNegocio.agencia_id = item.agencia_id;

			mdlParientesAvalesNegocios.ListarParientesAvalesNegocios();
			$("#mdlParientesAvalesNegocio").modal({
				backdrop: "false",
				keyboard: true,
			});
		},

		Eliminar(cliente_id) {
			let self = this;

			Swal.fire({
				title: "ELIMINAR CLIENTE",
				icon: "question",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				showLoaderOnConfirm: true,
				preConfirm: () => {
					let data = new FormData();

					data.append("cliente_id", cliente_id);
					data.append("agencia_id", this.agencia_seleccionada);

					return axios
						.post(route("cli.listado_registro.eliminar"), data)
						.then(async (response) => {
							self.BuscarClientes();
						})
						.catch((error) => {
							Swal.showValidationMessage(`Ha ocurrido un error: ${error}`);
						});
				},
				allowOutsideClick: () => !Swal.isLoading(),
			}).then((result) => {
				if (result.isConfirmed) {
					Swal.fire({
						icon: "success",
						title: "¡ÉXITO!",
						timer: 1200,
						showConfirmButton: false,
					});
				}
			});
		},
	},
};
</script>



<style lang="css">
:root {
	--color-green: var(--verdeOscuroEmpresarial);
	--color-red: var(--plomoOscuroEmpresarial);
	--color-button: #fdffff;
	--color-black: var(--plomoOscuroEmpresarial);
}
.icon_buscar {
	display: none;
}

.slot_listado_registro {
	width: 70% !important;
	margin-left: 15% !important;
}
.switch-button {
	display: inline-block;
}
.switch-button__label:hover {
	cursor: pointer;
}
.switch-button .switch-button__checkbox {
	display: none;
}
.switch-button .switch-button__label {
	background-color: var(--color-red);
	width: 3rem;
	height: 1rem;
	border-radius: 3rem;
	display: inline-block;
	position: relative;
	margin: 0px;
}
.switch-button .switch-button__label:before {
	transition: 0.2s;
	display: block;
	position: absolute;
	width: 1rem;
	height: 1rem;
	background-color: var(--color-button);
	content: "";
	border-radius: 50%;
	box-shadow: inset 0px 0px 0px 1px var(--color-black);
}
.switch-button .switch-button__checkbox:checked + .switch-button__label {
	background-color: var(--color-green);
}
.switch-button .switch-button__checkbox:checked + .switch-button__label:before {
	transform: translateX(2rem);
}

.form-check-input,
.label-title:hover {
	cursor: pointer;
}

.btn-modal-group {
	width: 100%;
	height: 100px;
	font-size: 15px;
	border-radius: 20px;
}

#tblClientes_length {
	display: none;
}

@media only screen and (max-width: 900px) {
	#previzualizar {
		width: 350px !important;
		height: 200px !important;
	}

	.smartcenter {
		justify-content: center !important;
	}

	.btn-modal-group {
		width: 40%;
	}

	#btnEsPariente {
		width: 50%;
	}
	#btnEsAval {
		width: 50%;
	}

	.slot_listado_registro {
		width: 99% !important;
		margin-left: 0.5% !important;
		margin-top: 15% !important;
	}
	.respon {
		margin-top: 5px;
	}

	.text_buscar {
		display: none;
	}
	.icon_buscar {
		display: block;
	}
}
</style>

