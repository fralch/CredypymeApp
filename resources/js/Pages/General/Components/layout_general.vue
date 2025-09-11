<template>
	<div style="height: 100% !important">
		<div class="container">
			<inertia-link class="logo-system" :href="$route('gen.index')"
				><img :src="'/images/general/logo-blanco.svg'" alt="Logo" width="150px"
			/></inertia-link>

			<nav>
				<div class="nav-fostrap text-left always-over" id="navbar-responsive">
					<ul>
						<li>
							<a href="javascript:void(0)"
								>Permisos<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.per.listar_permisos')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_PERMISOS/PERMISOS'
											)
										"
										>Listar permisos</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.per.permisos_usuarios', 'completo')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_PERMISOS/PERMISOS_USUARIOS'
											)
										"
										>Permisos por usuarios - General</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.per.permisos_usuarios', 'credito')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_PERMISOS/PERMISOS_USUARIOS_CREDITO'
											)
										"
										>Permisos por usuarios - Créditos</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.per.permisos_usuarios', 'gth')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_PERMISOS/PERMISOS_USUARIOS_GTH'
											)
										"
										>Permisos por usuarios - Gth</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.per.permisos_usuarios', 'logistica')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_PERMISOS/PERMISOS_USUARIOS_LOGISTICA'
											)
										"
										>Permisos por usuarios - Logística</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.per.permisos_cargos')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_PERMISOS/PERMISOS_CARGOS'
											)
										"
										>Permisos por cargo</inertia-link
									>
								</li>
							</ul>
						</li>

						<li>
							<a href="javascript:void(0)"
								>Cargos<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.car.general.listar_cargos')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_MANTENIMIENTO/CARGOS'
											)
										"
										>Listar cargos</inertia-link
									>
								</li>
							</ul>
						</li>

						<!-- ----------------- -->
						<li>
							<a href="javascript:void(0)"
								>Mantenimiento<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.man.listar_feriados')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_MANTENIMIENTO/FERIADOS'
											)
										"
										>Feriados</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="collapse-item"
										:href="$route('gen.man.areas_trabajo')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_MANTENIMIENTO/AREAS_TRABAJO'
											)
										"
										>Áreas de trabajo</inertia-link
									>
								</li>

								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('GENERAL_MANTENIMIENTO/CREDICHECK')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Credicheck<span
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
												:href="$route('gen.man.cre.promociones')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'GENERAL_MANTENIMIENTO/CREDICHECK_PROMOCIONES'
													)
												"
												>Promociones</inertia-link
											>
										</li>
									</ul>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('man.servicios')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_MANTENIMIENTO/SERVICIOS'
											)
										"
									>
										Servicios
									</inertia-link>
								</li>
								<li>
									<inertia-link
										class="highlight"
										:href="$route('man.bancos')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'GENERAL_MANTENIMIENTO/BANCOS'
											)
										"
									>
										Bancos
									</inertia-link>
								</li>
							</ul>
						</li>
						<!-- ----------------- -->
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
				<span
					>{{ $page.props.user_session.nombres }}/
					{{ $page.props.user_session.usuario }}
				</span>

				<span class="ml-3">
					{{ $page.props.user_session.cargo }}
				</span>

				<span class="ml-3 text-white">{{ fecha_actual }}</span>

				<span class="ml-3">
					{{
						$page.props.user_session.dispositivo.tipo +
						" - " +
						$page.props.user_session.dispositivo.nombre
					}}
				</span>

				<span class="ml-3 text-white">
					{{ $page.props.application.version[0].numeroVersion }}
				</span>

				<span class="ml-3 mr-3" style="text-transform: uppercase">
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
		return { fecha_actual: null, windowWidth: window.innerWidth };
	},
	mounted() {
		document.title = "General";
		document.body.style.backgroundImage =
			"url('/images/general/fondo_principal.png')";
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
		});
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
	},
};
</script>

<style lang="css">
:root {
	--colorMedio: #89bd29;
	--colorAlto: #6d9720;
	--colorBajo: #daddd8;
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
	width: 80% !important;

	margin-top: 2%;
	margin-left: 10%;
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
		width: 97% !important;
		margin-top: 60px;
		margin-left: 2%;
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
