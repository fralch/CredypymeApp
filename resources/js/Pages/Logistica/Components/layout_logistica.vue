<template>
	<div style="height: 100% !important">
		<div class="container">
			<inertia-link class="logo-system" :href="$route('log.index')"
				><img :src="'/images/general/logo-blanco.svg'" alt="Logo" width="150px"
			/></inertia-link>

			<nav>
				<div class="nav-fostrap text-left always-over" id="navbar-responsive">
					<ul>
						<li>
							<a href="javascript:void(0)"
								>Activos<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<a class="text-left" href="javascript:void(0)"
										>Historial<span
											class="fas fa-caret-right"
											style="float: right"
											v-if="windowWidth > 1000"
										></span
										><span
											class="arrow-down"
											style="float: right"
											v-if="windowWidth <= 1000"
										></span
									></a>
									<ul class="dropdown text-left always-over">
										<!-- <li>
                      <inertia-link
                        class="collapse-item"
                        :href="$route('log.act.historial_compras')"
                        v-if="
                          $page.props.user_permissions.permisos.includes(
                            'LOGISTICA_ACTIVOS/HISTORIAL_COMPRAS'
                          )
                        "
                        >Compras</inertia-link
                      >
                    </li> -->
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.act.historial_compras')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_ACTIVOS/HISTORIAL_COMPRAS'
													)
												"
												>Compras</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.act.historial_envios')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_ACTIVOS/HISTORIAL_ENVIOS'
													)
												"
												>Envíos</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.act.historial_asignaciones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_ACTIVOS/HISTORIAL_ASIGNACIONES'
													)
												"
												>Asignaciones</inertia-link
											>
										</li>
										<!-- <li>
                      <inertia-link
                        class="collapse-item"
                        :href="
                          $route('log.act.historial_asignaciones', 'LOCAL')
                        "
                        v-if="
                          $page.props.user_permissions.permisos.includes(
                            'LOGISTICA_ACTIVOS/HISTORIAL_ASIGNACIONES_LOCAL'
                          )
                        "
                        >Asignaciones-local</inertia-link
                      >
                    </li> -->
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.act.historial_ventas')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_ACTIVOS/HISTORIAL_VENTAS'
													)
												"
												>Ventas</inertia-link
											>
										</li>
										<!-- <li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.act.historial_depreciacion')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_ACTIVOS/HISTORIAL_DEPRECIACION'
													)
												"
												>Depreciacion</inertia-link
											>
										</li> -->
									</ul>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.act.inventario')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/INVENTARIO'
											)
										"
										>Inventario</inertia-link
									>
								</li>
								<!-- <li>
                  <inertia-link
                    class="collapse-item"
                    :href="$route('log.act.inventario')"
                    v-if="
                      $page.props.user_permissions.permisos.includes(
                        'LOGISTICA_ACTIVOS/INVENTARIO_LOCAL'
                      )
                    "
                    >Inventario-local</inertia-link
                  >
                </li> -->
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.act.asignacion')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/ASIGNACION'
											)
										"
										>Asignación</inertia-link
									>
								</li>
								<!-- <li>
                  <inertia-link
                    class="collapse-item"
                    :href="$route('log.act.asignacion', 'LOCAL')"
                    v-if="
                      $page.props.user_permissions.permisos.includes(
                        'LOGISTICA_ACTIVOS/ASIGNACION_LOCAL'
                      )
                    "
                    >Asignación-local</inertia-link
                  >
                </li> -->
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.act.venta')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/VENTA'
											)
										"
										>Venta</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.act.envios_recepciones')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/ENVIOS_RECEPCIONES'
											)
										"
										>Envíos y recepciones</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.act.mis_activos')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_ACTIVOS/MIS_ACTIVOS'
											)
										"
										>Mis activos</inertia-link
									>
								</li>
							</ul>
						</li>

						<li>
							<a href="javascript:void(0)"
								>Suministros<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<a class="text-left" href="javascript:void(0)"
										>Historial<span
											class="fas fa-caret-right"
											style="float: right"
											v-if="windowWidth > 1000"
										></span
										><span
											class="arrow-down"
											style="float: right"
											v-if="windowWidth <= 1000"
										></span
									></a>
									<ul class="dropdown text-left always-over">
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_compras')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_COMPRAS'
													)
												"
												>Compras</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_envios')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_ENVIOS'
													)
												"
												>Envíos</inertia-link
											>
										</li>
										<!-- <li>
                      <inertia-link
                        class="collapse-item"
                        :href="$route('log.sum.historial_envios', 'LOCAL')"
                        v-if="
                          $page.props.user_permissions.permisos.includes(
                            'LOGISTICA_SUMINISTROS/HISTORIAL_ENVIOS_LOCAL'
                          )
                        "
                        >Envíos-local</inertia-link
                      >
                    </li> -->

										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_asignaciones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_ASIGNACIONES'
													)
												"
												>Asignaciones</inertia-link
											>
										</li>
										<!-- <li>
                      <inertia-link
                        class="collapse-item"
                        :href="
                          $route('log.sum.historial_asignaciones', 'LOCAL')
                        "
                        v-if="
                          $page.props.user_permissions.permisos.includes(
                            'LOGISTICA_SUMINISTROS/HISTORIAL_ASIGNACIONES_LOCAL'
                          )
                        "
                        >Asignaciones-local</inertia-link
                      >
                    </li> -->
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_devoluciones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_DEVOLUCIONES'
													)
												"
												>Devoluciones</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_bajas')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_BAJAS'
													)
												"
												>Bajas</inertia-link
											>
										</li>
										<!-- <li>
                      <inertia-link
                        class="collapse-item"
                        :href="
                          $route('log.sum.historial_devoluciones', 'LOCAL')
                        "
                        v-if="
                          $page.props.user_permissions.permisos.includes(
                            'LOGISTICA_SUMINISTROS/HISTORIAL_DEVOLUCIONES_LOCAL'
                          )
                        "
                        >Devoluciones-local</inertia-link
                      >
                    </li> -->
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_ventas')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_VENTAS'
													)
												"
												>Ventas</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.sum.historial_operaciones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_SUMINISTROS/HISTORIAL_OPERACIONES'
													)
												"
												>Operaciones</inertia-link
											>
										</li>
										<!-- <li>
                      <inertia-link
                        class="collapse-item"
                        :href="$route('log.sum.historial_operaciones', 'LOCAL')"
                        v-if="
                          $page.props.user_permissions.permisos.includes(
                            'LOGISTICA_SUMINISTROS/HISTORIAL_OPERACIONES_LOCAL'
                          )
                        "
                        >Operaciones-local</inertia-link
                      >
                    </li> -->
									</ul>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.sum.almacen')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_SUMINISTROS/ALMACEN'
											)
										"
										>Almacén</inertia-link
									>
								</li>
								<!-- <li>
                  <inertia-link
                    class="collapse-item"
                    :href="$route('log.sum.almacen', 'LOCAL')"
                    v-if="
                      $page.props.user_permissions.permisos.includes(
                        'LOGISTICA_SUMINISTROS/ALMACEN_LOCAL'
                      )
                    "
                    >Almacén-local</inertia-link
                  >
                </li> -->
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.sum.asignacion')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_SUMINISTROS/ASIGNACION'
											)
										"
										>Asignación</inertia-link
									>
								</li>
								<!-- <li>
                  <inertia-link
                    class="collapse-item"
                    :href="$route('log.sum.asignacion', 'LOCAL')"
                    v-if="
                      $page.props.user_permissions.permisos.includes(
                        'LOGISTICA_SUMINISTROS/ASIGNACION_LOCAL'
                      )
                    "
                    >Asignación-local</inertia-link
                  >
                </li> -->
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.sum.devolucion')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_SUMINISTROS/DEVOLUCION'
											)
										"
										>Devolución</inertia-link
									>
								</li>
								<!-- <li>
                  <inertia-link
                    class="collapse-item"
                    :href="$route('log.sum.devolucion', 'LOCAL')"
                    v-if="
                      $page.props.user_permissions.permisos.includes(
                        'LOGISTICA_SUMINISTROS/DEVOLUCION_LOCAL'
                      )
                    "
                    >Devolución-local</inertia-link
                  >
                </li> -->
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.sum.venta')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_SUMINISTROS/VENTA'
											)
										"
										>Venta</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.sum.envios_recepciones')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_SUMINISTROS/ENVIOS_RECEPCIONES'
											)
										"
										>Mis envíos</inertia-link
									>
								</li>
								<!-- <li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.sum.mis_suministros')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_SUMINISTROS/MIS_SUMINISTROS'
											)
										"
										>Mis suministros</inertia-link
									>
								</li> -->
							</ul>
						</li>

						<!-- ----------------- -->
						<li>
							<a href="javascript:void(0)"
								>Aplicación<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.apl.cierre_dia')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_APLICACION/CIERRE_DIA'
											)
										"
										>Cierre de día</inertia-link
									>
								</li>
							</ul>
						</li>
						<!-- ----------------- -->

						<!-- ----------------- -->
						<li>
							<a href="javascript:void(0)"
								>Mantenimiento<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<a class="text-left" href="javascript:void(0)"
										>Activos<span
											class="fas fa-caret-right"
											style="float: right"
											v-if="windowWidth > 1000"
										></span
										><span
											class="arrow-down"
											style="float: right"
											v-if="windowWidth <= 1000"
										></span
									></a>
									<ul class="dropdown text-left always-over">
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.act_categorias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/ACTIVOS_CATEGORIAS'
													)
												"
												>Categorías</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.act_nombres')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/ACTIVOS_NOMBRES'
													)
												"
												>Nombres</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.act_tipos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/ACTIVOS_TIPOS'
													)
												"
												>Tipos</inertia-link
											>
										</li>

										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.act_ubicaciones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/ACTIVOS_UBICACIONES'
													)
												"
												>Ubicaciones</inertia-link
											>
										</li>
									</ul>
								</li>
								<li>
									<a class="text-left" href="javascript:void(0)"
										>Suministros<span
											class="fas fa-caret-right"
											style="float: right"
											v-if="windowWidth > 1000"
										></span
										><span
											class="arrow-down"
											style="float: right"
											v-if="windowWidth <= 1000"
										></span
									></a>
									<ul class="dropdown text-left always-over">
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.sum_tipos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/SUMINISTROS_TIPOS'
													)
												"
												>Tipos</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.sum_proveedores')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/SUMINISTROS_PROVEEDORES'
													)
												"
												>Proveedores</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="collapse-item"
												:href="$route('log.man.sum_mediciones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'LOGISTICA_MANTENIMIENTO/SUMINISTROS_MEDICIONES'
													)
												"
												>Mediciones</inertia-link
											>
										</li>
									</ul>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.man.responsables')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_MANTENIMIENTO/RESPONSABLES'
											)
										"
										>Responsables</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.man.condiciones')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_MANTENIMIENTO/CONDICIONES'
											)
										"
										>Condiciones</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('log.man.estados')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'LOGISTICA_MANTENIMIENTO/ESTADOS'
											)
										"
										>Estados</inertia-link
									>
								</li>
							</ul>
						</li>

						<li>
							<a href="/home">Salir</a>
						</li>
					</ul>
				</div>
				<div class="nav-bg-fostrap always-over">
					<div class="navbar-fostrap">
						<span></span> <span></span> <span></span>
					</div>
					<inertia-link class="title-mobile" :href="$route('log.index')"
						><img
							:src="'/images/general/logo-blanco.svg'"
							alt="Logo"
							width="120px"
					/></inertia-link>
				</div>
			</nav>
		</div>

		<div class="slot-content">
			<slot name="component-view"></slot>
		</div>

		<!-- Footer -->
		<footer class="text-center text-lg-start" id="footer-navigator">
			<!-- Copyright -->
			<div class="text-right p-1 footer-text">
				<span class="text-white"
					>{{ $page.props.user_session.nombres }}/
					{{ $page.props.user_session.usuario }}
				</span>

				<span class="ml-3">
					{{ $page.props.user_session.nombre_cargo }}
				</span>

				<span class="ml-3 text-white">{{ fecha_actual }}</span>

				<span class="ml-3">
					{{
						$page.props.user_session.dispositivo.tipo +
						" - " +
						$page.props.user_session.dispositivo.nombre
					}}
				</span>
				<!-- 
				<span class="ml-3 text-white">
					{{ $page.props.application.version[0].numeroVersion }}
				</span> -->

				<span class="ml-3 mr-3 text-white" style="text-transform: uppercase">
					.::AGENCIA {{ $page.props.user_session.nombre_agencia }}::.
				</span>
			</div>
		</footer>
		<!-- Footer -->
	</div>
</template>

<script>
export default {
	data() {
		return {
			fecha_actual: null,
			windowWidth: window.innerWidth,
			windowHeigth: window.innerHeight,
		};
	},

	mounted() {
		document.title = "Logística";
		document.body.style.backgroundImage =
			"url('/images/logistica/fondo_principal.png')";
		document.body.style.backgroundPosition = "center center";
		document.body.style.backgroundRepeat = "repeat-yt";
		document.body.style.backgroundAttachment = "fixed";
		document.body.style.backgroundSize = "cover";
		document.body.style.fontFamily = "Roboto, sans-serif";
		let parts = this.$page.props.application.data
			.filter(
				(item) => item.descripcion == "FECHA_LOGISTICA" && item.id_agencia == 5
			)[0]
			.valorFecha.split("-");
		let options = {
			weekday: "long",
			year: "numeric",
			month: "long",
			day: "numeric",
		};
		let date = new Date(+parts[0], parts[1] - 1, +parts[2]);
		this.fecha_actual = date.toLocaleDateString("es-ES", options);
		$(".navbar-fostrap").click(function () {
			$(".nav-fostrap").toggleClass("visible");
			$("body").toggleClass("cover-bg");
		});
		$(".slot-content").click(function () {
			if ($("#navbar-responsive").hasClass("visible")) {
				$("#navbar-responsive").removeClass("visible");
				$("body").removeClass("cover-bg");
			}
		});
		document.addEventListener("contextmenu", (event) => event.preventDefault());
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeigth = window.innerHeight;
		});

		document.addEventListener("contextmenu", (event) => event.preventDefault());
	},
	methods: {
		hide_nav() {
			if (screen.width < 1000) {
				parent.document.getElementById("footer-navigator").style.display =
					"none";
			}
		},
		show_nav() {
			if (screen.width < 1000) {
				parent.document.getElementById("footer-navigator").style.display =
					"flex";
			}
		},
		round(value, decimal_places) {
			return parseFloat(value).toFixed(decimal_places);
		},

		filtrar_agencias(permiso) {
			let agencias = this.$inertia.page.props.application.agencias;
			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == permiso
				);

			let agencias_filtradas = [];
			if (
				permiso_detalle[0].acceso_agencias != "" &&
				permiso_detalle[0].acceso_agencias != null
			) {
				let acceso_agencias = JSON.parse(permiso_detalle[0].acceso_agencias);
				agencias.forEach((element_1) => {
					acceso_agencias.forEach((element_2) => {
						if (element_1.id == element_2.agencia_id) {
							agencias_filtradas.push(element_1);
						}
					});
				});
			}
			return agencias_filtradas;
		},
	},
};
</script>

<style lang="css">
:root {
	--colorMedio: #71bc9c;
	--colorAlto: #17a673;
	--colorBajo: #e7fbf2;
}
* {
	margin: 0;
	padding: 0;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-ms-box-sizing: border-box;
	box-sizing: border-box;
}
body,
html {
	width: 100%;
	height: 100%;
}

body {
	font-size: 15px;
	color: #666;
}

a {
	text-decoration: none;
}

a:link,
a:visited,
a:active {
	text-decoration: none;
}

/* height: 12px; */

.container {
	max-width: 80%;
	margin: 0 auto;
	width: 100%;
	margin-left: 20%;
}

.nav-fostrap {
	display: block;
	margin-bottom: 10px 0;
	background: var(--colorAlto);

	-webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-ms-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-o-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	border-bottom-left-radius: 20px;

	width: 100%;
}

.nav-fostrap ul {
	list-style-type: none;
	margin: 0;
	padding: 0;
	display: block;
}

.nav-fostrap li {
	list-style-type: none;
	margin: 0;
	padding: 0;
	display: inline-block;
	position: relative;
	font-size: 14;
	color: white;
}

.nav-fostrap li a {
	padding: 8px 20px;
	font-size: 14;
	color: white;
	display: inline-block;
	outline: 0;
	font-weight: 400;
}

.nav-fostrap li:hover ul.dropdown {
	display: block;
}

.nav-fostrap li ul.dropdown li:hover ul.dropdown {
	display: block;
}

.nav-fostrap li ul.dropdown {
	position: absolute;
	display: none;
	width: 280px;
	background: #ffff;

	-webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-ms-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-o-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	padding-top: 0;
	border-bottom-left-radius: 10px;
	border-bottom-right-radius: 10px;
}

.nav-fostrap li ul.dropdown li ul.dropdown {
	position: absolute;
	display: none;
	width: 280px;
	left: 270px;
	top: 0;
	background: #ffff;

	-webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-ms-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	-o-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
	padding-top: 0;
}

.nav-fostrap li ul.dropdown li {
	display: block;
	list-style-type: none;
}

.nav-fostrap li ul.dropdown li a {
	padding: 8px 15px;
	font-size: 15px;
	color: var(--plomoOscuroEmpresarial);
	display: block;
	font-weight: 400;
}
.nav-fostrap li ul.dropdown li ul.dropdown li a {
	padding: 8px 15px;
	font-size: 15px;
	color: var(--plomoOscuroEmpresarial);
	display: block;
	font-weight: 400;
}

.nav-fostrap li ul.dropdown li:last-child a {
	border-bottom: none;
}

.nav-fostrap li:hover a {
	background: white;
	color: var(--plomoOscuroEmpresarial);
}

.nav-fostrap li:first-child:hover a {
	border-radius: 3px 0 0 3px;
}

.nav-fostrap li ul.dropdown li:hover a {
	background: var(--plomoOscuroEmpresarial);
	color: white !important;
}

.nav-fostrap li ul.dropdown li:hover ul.dropdown li a {
	background: #ffff;
	color: var(--plomoOscuroEmpresarial) !important;
}

.nav-fostrap li ul.dropdown li ul.dropdown li:hover a {
	background: var(--plomoOscuroEmpresarial);
	color: white !important;
}

.nav-fostrap li ul.dropdown li:first-child:hover a {
	border-radius: 0;
}

.nav-fostrap li:hover .arrow-down {
	border-top: 5px solid var(--colorAlto);
}

.arrow-down {
	width: 0;
	height: 0;
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 5px solid #def1f0;
	position: relative;
	top: 15px;
	right: -5px;
	content: "";
}

.title-mobile {
	display: none;
}

.fixed-top {
	position: fixed;
	top: 0;
	right: 0;
	left: 0;
}

footer {
	position: absolute;
	bottom: 0;
	width: 90%;
	border-top-right-radius: 20px;

	color: white;
}

.footer-text {
	border-top-right-radius: 20px;
	color: var(--colorAlto);
	background: var(--plomoOscuroEmpresarial);
	font-size: 0.7rem;
	font-weight: bolder;
}

.logo-system {
	position: absolute;
	top: 5px;
	left: 5%;
}

.modal-content {
	top: 8% !important;
}

.slot-content {
	position: relative;
	overflow-x: hidden;
}

.slot-content::-webkit-scrollbar {
	width: 0.5rem;
}

.slot-content::-webkit-scrollbar-track {
	background: transparent;
}

.slot-content::-webkit-scrollbar-thumb {
	background: var(--plomoOscuroEmpresarial);
	border-radius: 5px;
}

.slot-content::-webkit-scrollbar-thumb:active {
	background: var(--plomoOscuroEmpresarial);
}

.always-over {
	z-index: 20 !important;
}

.slot_body {
	margin-top: 2%;
	z-index: 2 !important;
}

.slot-content {
	overflow-y: scroll;
	height: 85%;
}

body.swal2-height-auto {
	height: 100% !important;
}

@media only screen and (max-width: 900px) {
	.nav-fostrap {
		background: #fff;
		opacity: 0.95;
		width: 300px;
		height: 80% !important;
		top: 10% !important;
		display: block;
		position: fixed;
		left: -300px;
		top: 0px;
		-webkit-transition: left 0.25s ease;
		-moz-transition: left 0.25s ease;
		-ms-transition: left 0.25s ease;
		-o-transition: left 0.25s ease;
		transition: left 0.25s ease;
		margin: 0;
		border: 0;
		border-radius: 0;
		overflow-y: auto;
		overflow-x: hidden;
		height: 100%;
		border-top-right-radius: 10px;
		border-bottom-right-radius: 10px;
	}

	.nav-fostrap li ul.dropdown li ul.dropdown {
		left: 10px;
		top: 30px;
	}

	.title-mobile {
		position: fixed;
		display: block;
		top: 10px;
		left: 100px;
		right: 100px;
		text-align: center;
	}

	.nav-fostrap.visible {
		left: 0px;
		-webkit-transition: left 0.25s ease;
		-moz-transition: left 0.25s ease;
		-ms-transition: left 0.25s ease;
		-o-transition: left 0.25s ease;
		transition: left 0.25s ease;
	}

	.nav-bg-fostrap {
		display: inline-block;
		vertical-align: middle;
		width: 100%;
		height: 50px;
		margin: 0;
		position: absolute;
		top: 0px;
		left: 0px;
		background: var(--colorAlto);
		padding: 12px 0 0 10px;
		-webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
		-moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
		-ms-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
		-o-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
		box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
		border-bottom-left-radius: 20px;
		border-bottom-right-radius: 20px;
	}

	.navbar-fostrap {
		display: inline-block;
		vertical-align: middle;
		height: 50px;
		cursor: pointer;
		margin: 0;
		position: absolute;
		top: 0;
		left: 0;
		padding: 12px;
	}

	.navbar-fostrap span {
		height: 2px;
		background: #fff;
		margin: 5px;
		display: block;
		width: 20px;
	}

	.navbar-fostrap span:nth-child(2) {
		width: 20px;
	}

	.navbar-fostrap span:nth-child(3) {
		width: 20px;
	}

	.nav-fostrap ul {
		padding-top: 20px;
	}

	.nav-fostrap li {
		display: block;
	}

	.nav-fostrap li a {
		display: block;
		padding: 10px 20px;
		background: var(--colorAlto);
		color: white;
		font-weight: 600;
	}

	.nav-fostrap li:first-child:hover a {
		border-radius: 0;
	}

	.nav-fostrap li ul.dropdown {
		position: relative;
	}

	.nav-fostrap li ul.dropdown li a {
		background: white !important;
		border-bottom: none;
		color: var(--plomoOscuroEmpresarial) !important;
	}

	.nav-fostrap li:hover a {
		background: var(--colorAlto);
		color: #fff !important;
	}

	.nav-fostrap li ul.dropdown li:hover a {
		background: var(--plomoOscuroEmpresarial) !important;
		color: #fff !important;
	}

	.nav-fostrap li ul.dropdown li:hover ul.dropdown li a {
		background: #fff !important;
		color: var(--plomoOscuroEmpresarial) !important;
	}
	.nav-fostrap li ul.dropdown li ul.dropdown li:hover a {
		background: var(--colorAlto) !important;
		color: #fff !important;
	}

	.nav-fostrap li ul.dropdown li a {
		padding: 5px 5px 5px 30px;
	}

	.nav-fostrap li:hover .arrow-down {
		border-top: 5px solid #fff;
	}
	.nav-fostrap li:hover ul.dropdown li .arrow-down {
		border-top: 5px solid var(--colorAlto);
	}

	.arrow-down {
		border-top: 5px solid white;
		position: absolute;
		top: 20px;
		right: 10px;
	}

	.nav-fostrap li ul.dropdown li .arrow-down {
		border-top: 5px solid var(--colorAlto);

		top: 10px;
	}

	.cover-bg {
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
	}

	.logo-system {
		display: none;
	}

	.footer-text {
		font-size: 8px;
	}

	.slot_body {
		margin-top: 60px;
	}

	.slot-content {
		overflow-y: scroll;
		overflow-x: scroll;
		height: 92%;
	}

	.modal-content {
		top: 0 !important;
	}
}

@media only screen and (max-width: 1199px) {
	.container {
		width: 96%;
	}
}
</style>
