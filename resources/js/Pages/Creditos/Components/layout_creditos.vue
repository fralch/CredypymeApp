<template>
	<div
		style="height: 100% !important"
		tabindex="0"
		@keydown.alt.86="
			AbrirModal(
				'mdlBusquedaClientes',
				'historial_crediticio',
				'CREDITOS_CLIENTES/HISTORIAL_CREDITICIO'
			)
		"
		@keydown.alt.80="
			AbrirModal(
				'mdlBusquedaClientes',
				'propuesta',
				'CREDITOS_CREDITO/PROPUESTA'
			)
		"
		@keydown.alt.65="
			AbrirModal(
				'mdlBusquedaCreditos',
				'aprobacion',
				'CREDITOS_CREDITO/APROBACION'
			)
		"
		@keydown.alt.67="
			AbrirModal('mdlBusquedaCreditos', 'cobranza', 'CREDITOS_CAJA/COBRANZA')
		"
		@keydown.alt.83="
			AbrirModal(
				'mdlBusquedaCreditos',
				'desembolso',
				'CREDITOS_CAJA/DESEMBOLSO'
			)
		"
		@keydown.alt.73="
			AbrirModal(
				'mdlBusquedaInversiones',
				'buscar_inversiones',
				'CREDITOS_INVERSION/BUSCAR_INVERSIONES'
			)
		"
	>
		<div class="container">
			<inertia-link class="logo-system" :href="$route('cre.index')"
				><img :src="'/images/general/logo-blanco.png'" alt="Logo" width="180px"
			/></inertia-link>

			<nav>
				<div class="nav-fostrap text-left always-over" id="navbar-responsive">
					<ul>
						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_CLIENTES')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Clientes<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										class="highlight"
										:href="$route('cli.listado_registro')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CLIENTES/LISTADO_REGISTRO'
											)
										"
										:style="
											windowWidth >= 900
												? ''
												: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
										"
										>Listado y registro</inertia-link
									>
								</li>
								<li>
									<a
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'album_fotos',
												'CREDITOS_CLIENTES/ALBUM_FOTOS'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CLIENTES/ALBUM_FOTOS'
											)
										"
										>Albúm de fotos</a
									>
								</li>
								<li>
									<a
										class="highlight"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'historial_crediticio',
												'CREDITOS_CLIENTES/HISTORIAL_CREDITICIO'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CLIENTES/HISTORIAL_CREDITICIO'
											)
										"
										:style="
											windowWidth >= 900
												? ''
												: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
										"
										>Historial crediticio
										<span class="badge badge-light" v-if="windowWidth >= 900"
											>Alt + V</span
										></a
									>
								</li>
								<li>
									<a
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'prendas',
												'CREDITOS_CLIENTES/PRENDAS'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CLIENTES/PRENDAS'
											)
										"
										>Prendas
									</a>
								</li>
								<li>
									<inertia-link
										:href="$route('cli.movimiento')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CLIENTES/MOVIMIENTO'
											)
										"
										>Movimiento</inertia-link
									>
								</li>
								<li>
									<inertia-link
										:href="$route('cli.transferencia')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CLIENTES/TRANSFERENCIA'
											)
										"
										>Transferencia</inertia-link
									>
								</li>
							</ul>
						</li>
						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_CREDITO')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Crédito<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'evaluacion_financiera',
												'CREDITOS_CREDITO/EVALUACION_FINANCIERA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/EVALUACION_FINANCIERA'
											)
										"
										>Evaluación financiera</a
									>
								</li>
								<li>
									<a
										class="collapse-item highlight"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'propuesta',
												'CREDITOS_CREDITO/PROPUESTA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/PROPUESTA'
											)
										"
										:style="
											windowWidth >= 900
												? ''
												: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
										"
										>Propuesta
										<span class="badge badge-light" v-if="windowWidth >= 900"
											>Alt + P</span
										></a
									>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'copia_propuesta',
												'CREDITOS_CREDITO/COPIA_PROPUESTA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/COPIA_PROPUESTA'
											)
										"
										>Copia de propuesta</a
									>
								</li>
								<li>
									<a
										class="collapse-item highlight"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'aprobacion',
												'CREDITOS_CREDITO/APROBACION'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/APROBACION'
											)
										"
										:style="
											windowWidth >= 900
												? ''
												: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
										"
										>Aprobación
										<span class="badge badge-light" v-if="windowWidth >= 900"
											>Alt + A</span
										></a
									>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'copia_aprobacion',
												'CREDITOS_CREDITO/COPIA_APROBACION'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/COPIA_APROBACION'
											)
										"
										>Copia de aprobación</a
									>
								</li>
								<li>
									<inertia-link
										:href="$route('cre.documentos_financieros')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/DOCUMENTOS_FINANCIEROS'
											)
										"
										>Documentos financieros</inertia-link
									>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'anular_aprobacion',
												'CREDITOS_CREDITO/ANULAR_APROBACION'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/ANULAR_APROBACION'
											)
										"
										>Anular aprobación</a
									>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'copia_cronograma',
												'CREDITOS_CREDITO/COPIA_CRONOGRAMA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/COPIA_CRONOGRAMA'
											)
										"
										>Copia de cronograma</a
									>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'copia_voucher',
												'CREDITOS_CREDITO/COPIA_VOUCHER'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/COPIA_VOUCHER'
											)
										"
										>Copia de voucher</a
									>
								</li>
								<li>
									<inertia-link
										:href="$route('cre.carrito')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/CARRITO'
											)
										"
										>Carrito</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="highlight"
										:href="$route('cre.carrito_seguimiento')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CREDITO/CARRITO_SEGUIMIENTO'
											)
										"
										>Seguimiento de carritos</inertia-link
									>
								</li>
							</ul>
						</li>
						<!-- ------------ -->
						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_INVERSION')
								).length > 0
							"
						>
							<a href="javascript:void(0)">
								Inversión<span class="arrow-down"></span>
							</a>
							<ul class="dropdown text-left always-over">
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaClientes',
												'crear_inversion_meta',
												'CREDITOS_INVERSION/CREAR_INVERSION_META'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_INVERSION/CREAR_INVERSION_META'
											)
										"
									>
										Crear inversión meta
									</a>
								</li>
								<li>
									<a
										class="collapse-item highlight"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaInversiones',
												'buscar_inversiones',
												'CREDITOS_INVERSION/BUSCAR_INVERSIONES'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_INVERSION/BUSCAR_INVERSIONES'
											)
										"
									>
										Buscar Inversión
										<span class="badge badge-light">Alt + I</span>
									</a>
								</li>
								<li>
									<a
										class="collapse-item highlight-2"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaInversionesExterna',
												'buscar_inversiones_externa',
												'CREDITOS_INVERSION/BUSCAR_INVERSIONES_EXTERNA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_INVERSION/BUSCAR_INVERSIONES'
											)
										"
									>
										Buscar Inversión EXTERNA
										<span class="badge badge-light"></span>
									</a>
								</li>
							</ul>
						</li>

						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_CAJA')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Caja<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										:href="$route('caj.apertura_caja')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/APERTURA_CAJA'
											)
										"
										>Apertura de caja</inertia-link
									>
								</li>

								<li>
									<a
										class="collapse-item highlight"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'desembolso',
												'CREDITOS_CAJA/DESEMBOLSO'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/DESEMBOLSO'
											)
										"
										>Desembolso
										<span class="badge badge-light">Alt + S</span></a
									>
								</li>

								<li>
									<a
										class="collapse-item highlight"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditos',
												'cobranza',
												'CREDITOS_CAJA/COBRANZA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/COBRANZA'
											)
										"
										>Cobranza <span class="badge badge-light">Alt + C</span></a
									>
								</li>
								<li>
									<a
										class="collapse-item highlight-2"
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlBusquedaCreditosExterna',
												'cobranza_externa',
												'CREDITOS_CAJA/COBRANZA_EXTERNA'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/COBRANZA'
											)
										"
										>Cobranza EXTERNA</a
									>
								</li>
								<li>
									<inertia-link
										:href="$route('caj.transferencia', 'A_CAJA')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/TRANSFERENCIA_CAJA'
											)
										"
									>
										Transferir a caja</inertia-link
									>
								</li>
								<li>
									<inertia-link
										:href="$route('caj.transferencia', 'A_CUENTA')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/TRANSFERENCIA_CUENTA'
											)
										"
									>
										Transferir a cuenta</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('caj.mis_transferencias')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/MIS_TRANSFERENCIAS'
											)
										"
									>
										Mis transferencias</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('caj.adelanto_haberes')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/ADELANTO_HABERES'
											)
										"
									>
										Adelanto de haberes</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('caj.transaccion')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/TRANSACCION'
											)
										"
										>Transacción</inertia-link
									>
								</li>
								<li>
									<inertia-link
										:href="$route('caj.desembolsos_facturados')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/DESEMBOLSOS_FACTURADOS'
											)
										"
										>Desembolsos facturados</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('caj.operaciones_dia')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/OPERACIONES_DIA'
											)
										"
										>Operaciones del dí­a</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('cre.caj.revision_faltantes')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/REVISION_FALTANTES'
											)
										"
										>Revisión de faltantes</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('caj.cierre_caja')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/CIERRE_CAJA'
											)
										"
										>Cierre de caja</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('caj.cierre_dia')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/CIERRE_DIA'
											)
										"
										>Cierre de día</inertia-link
									>
								</li>

								<li>
									<a
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlExonerarComision',
												'desembolso',
												'CREDITOS_CAJA/EXONERACION_COMISION'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/EXONERACION_COMISION'
											)
										"
										>Exonerar comisión desembolso
									</a>
								</li>

								<li>
									<a
										href="javascript:void(0)"
										@click="
											AbrirModal(
												'mdlExonerarComision',
												'riesgo_crediticio',
												'CREDITOS_CAJA/EXONERACION_RIESGO'
											)
										"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/EXONERACION_RIESGO'
											)
										"
										>Exonerar riesgo de crédito</a
									>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('caj.carrito_cobranzas')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CAJA/CARRITO_COBRANZAS'
											)
										"
										>Carrito de cobranzas</inertia-link
									>
								</li>
							</ul>
						</li>
						<!-- ----------------- -->
						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_CUENTA')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Cuenta<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										:href="$route('cue.usuarios_cuenta')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/GESTION'
											)
										"
										>Gestión</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('cue.mi_cuenta')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/MI_CUENTA'
											)
										"
										>Mi cuenta</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('cue.mostrar_cuentas')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/MOSTRAR_CUENTAS'
											)
										"
										>Mostrar cuentas</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="highlight"
										:href="$route('cue.cuentas_bancarias')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/CUENTAS_BANCARIAS'
											)
										"
										>Cuentas bancarias</inertia-link
									>
								</li>
								<li>
									<inertia-link
										:href="$route('cue.transferencia', 'A_CAJA')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/TRANSFERENCIA_CAJA'
											)
										"
										>Transferir a caja</inertia-link
									>
								</li>
								<li>
									<inertia-link
										:href="$route('cue.transferencia', 'A_CUENTA')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/TRANSFERENCIA_CUENTA'
											)
										"
										>Transferir a cuenta</inertia-link
									>
								</li>
								<li>
									<inertia-link
										:href="$route('cue.transferencia', 'A_MI_CAJA')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/TRANSFERENCIA_MI_CAJA'
											)
										"
										>Transferir a mi caja</inertia-link
									>
								</li>

								<li>
									<inertia-link
										class="highlight"
										:href="$route('cue.mis_transferencias')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/MIS_TRANSFERENCIAS'
											)
										"
										>Mis transferencias</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('cue.envio')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/ENVIO'
											)
										"
										>Envío de efectivo</inertia-link
									>
								</li>

								<li>
									<inertia-link
										:href="$route('cue.mis_envios')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_CUENTA/MIS_ENVIOS'
											)
										"
										>Mis envíos</inertia-link
									>
								</li>
							</ul>
						</li>
						<!-- ----------------- -->
						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_HERRAMIENTAS')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Herramientas<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li>
									<inertia-link
										:href="$route('her.simulador_creditos')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/SIMULADOR_CREDITOS'
											)
										"
									>
										Simulador de créditos
									</inertia-link>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/SIMULADOR_RIESGO'
											)
										"
										@click="AbrirModal('mdlSimuladorRiesgo')"
									>
										Simulador de riesgo
									</a>
								</li>
								<li>
									<a
										class="collapse-item"
										href="javascript:void(0)"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/SIMULADOR_BILLETEO'
											)
										"
										@click="AbrirModal('mdlSimuladorBilleteo')"
									>
										Simulador de billeteo
									</a>
								</li>
								<li>
									<inertia-link
										:href="$route('her.facturacion_dia')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/FACTURACION_DIA'
											)
										"
										>Facturación del día</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="highlight"
										:href="$route('her.cambio_agencia')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/CAMBIO_AGENCIA'
											)
										"
										>Cambio de agencia</inertia-link
									>
								</li>
								<li>
									<inertia-link
										class="highlight"
										:href="$route('her.indicador_objetivo', 'completo')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/INDICADOR_OBJETIVO'
											)
										"
									>
										Indicador de objetivo
									</inertia-link>
								</li>
								<li>
									<inertia-link
										class="highlight"
										:href="$route('her.indicador_objetivo', 'personal')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/MI_INDICADOR_OBJETIVO'
											)
										"
									>
										Mi Indicador de objetivo
									</inertia-link>
								</li>
								<li>
									<inertia-link
										:href="$route('her.mensajeria')"
										v-if="
											$page.props.user_permissions.permisos.includes(
												'CREDITOS_HERRAMIENTAS/MENSAJERIA'
											)
										"
									>
										Mensajería
									</inertia-link>
								</li>
							</ul>
						</li>
						<!-- ----------------- -->
						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_REPORTES')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Reportes<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_REPORTES/CAJA')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Caja<span
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
												:href="$route('rep.caj.adelanto_haberes')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_ADELANTO_HABERES'
													)
												"
												>Adelanto de haberes</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="
													$route('rep.caj.desembolsos_auxiliar', 'completo')
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_DESEMBOLSOS_AUXILIAR'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Desembolsos por auxiliar</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="
													$route('rep.caj.desembolsos_auxiliar', 'personal')
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_MIS_DESEMBOLSOS_AUXILIAR'
													)
												"
												>Mis desembolsos - auxiliar</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.cobranzas_auxiliar', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_COBRANZAS_AUXILIAR'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Cobranzas por auxiliar</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.caj.cobranzas_auxiliar', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_MIS_COBRANZAS_AUXILIAR'
													)
												"
												>Mis cobranzas - auxiliar</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.cobranzas_tipo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_COBRANZAS_TIPO'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Cobranzas por tipo</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.transacciones', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_TRANSACCIONES'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Transacciones</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.caj.transacciones', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_MIS_TRANSACCIONES'
													)
												"
												>Mis transacciones</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.cobranzas_negocio')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_COBRANZAS_NEGOCIO'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Cobranzas en negocio</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.descuentos_creditos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_DESCUENTOS_CREDITOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Descuentos de crédito</inertia-link
											>
										</li>

										<li>
											<inertia-link
												:href="$route('rep.caj.caja_cierres')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_CIERRES'
													)
												"
												>Cierres de caja</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.caj.cierres_dia')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_CIERRES_DIA'
													)
												"
												>Cierres de día</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.caj.operaciones_mes')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_OPERACIONES_MES'
													)
												"
												>Operaciones del mes</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.transferencias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_TRANSFERENCIAS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Transferencias</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.caj.envios_agencias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_ENVIOS_AGENCIAS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Envíos entre agencias</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.caj.seguimiento_facturados')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_SEGUIMIENTO_FACTURADOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Seguimiento de facturados</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.caj.movimientos_bancarios')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CAJA_MOVIMIENTOS_BANCARIOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Movimientos Bancarios</inertia-link
											>
										</li>
									</ul>
								</li>

								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_REPORTES/CLIENTES')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Clientes<span
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
												class="highlight"
												:href="$route('rep.cli.cumpleanios', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_CUMPLEAÑOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Clientes con cumpleaños
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cli.cumpleanios', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_MIS_CUMPLEAÑOS'
													)
												"
												>Mis clientes - cumpleaños
											</inertia-link>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cli.clientes_activos', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_ACTIVOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Clientes activos
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cli.clientes_activos', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_MIS_ACTIVOS'
													)
												"
												>Mis clientes activos
											</inertia-link>
										</li>

										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cli.clientes_inactivos', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_INACTIVOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Clientes inactivos
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cli.clientes_inactivos', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_MIS_INACTIVOS'
													)
												"
												>Mis clientes inactivos
											</inertia-link>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cli.pago_hoy', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_PAGO_HOY'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Clientes que pagan hoy
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cli.pago_hoy', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_MI_PAGO_HOY'
													)
												"
												>Mis clientes que pagan hoy
											</inertia-link>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="
													$route('rep.cli.clientes_por_asesor', 'completo')
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_POR_ASESOR'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Clientes por asesor
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="
													$route('rep.cli.clientes_por_asesor', 'personal')
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_POR_MI_ASESOR'
													)
												"
												>Mis clientes por asesor
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cli.movimientos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_MOVIMIENTOS'
													)
												"
												>Movimientos
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cli.transferencias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CLIENTES_TRANSFERENCIAS'
													)
												"
												>Transferencias
											</inertia-link>
										</li>
									</ul>
								</li>
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_REPORTES/CREDITOS')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Créditos<span
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
											<a
												class="highlight"
												href="javascript:void(0)"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_DESEMBOLSOS_ASESOR'
													)
												"
												@click="
													AbrirModal(
														'mdlDesembolsosPorAsesor',
														'DESEMBOLSOS_ASESOR',
														'CREDITOS_REPORTES/CREDITOS_DESEMBOLSOS_ASESOR'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Desembolsos por asesor</a
											>
										</li>
										<li>
											<a
												class="collapse-item"
												href="javascript:void(0)"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MIS_DESEMBOLSOS_ASESOR'
													)
												"
												@click="
													AbrirModal(
														'mdlDesembolsosPorAsesor',
														'MIS_DESEMBOLSOS_ASESOR',
														'CREDITOS_REPORTES/CREDITOS_MIS_DESEMBOLSOS_ASESOR'
													)
												"
												>Mis desembolsos - asesor</a
											>
										</li>

										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.dias_mora', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_DIAS_MORA'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Créditos por días de mora</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cre.dias_mora', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MIS_DIAS_MORA'
													)
												"
												>Mis créditos por días de mora</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.lugar_cobranza', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_LUGAR_COBRANZA'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Créditos por lugar de cobranza</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cre.lugar_cobranza', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MI_LUGAR_COBRANZA'
													)
												"
												>Mis créditos - lugar de cobranza</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.control_mora', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_CONTROL_MORA'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Control de mora</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cre.control_mora', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MI_CONTROL_MORA'
													)
												"
												>Mi control de mora</inertia-link
											>
										</li>

										<li>
											<a
												class="collapse-item"
												href="javascript:void(0)"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MORA_AGENCIA'
													)
												"
												@click="
													AbrirModal(
														'mdlControlMoraAgencia',
														'CONTOL DE MORA - AGENCIA',
														'CREDITOS_REPORTES/CREDITOS_MORA_AGENCIA'
													)
												"
												>Control de mora - agencia</a
											>
										</li>
										<li>
											<a
												class="collapse-item"
												href="javascript:void(0)"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MORA_AGENCIA_MENSUAL'
													)
												"
												@click="
													AbrirModal(
														'mdlControlMoraAgenciaMensual',
														'CONTOL DE MORA AGENCIA - MENSUAL',
														'CREDITOS_REPORTES/CREDITOS_MORA_AGENCIA_MENSUAL'
													)
												"
												>Control de mora agencia - mensual</a
											>
										</li>

										<li>
											<inertia-link
												class="highlight"
												:href="
													$route(
														'rep.cre.compromisos_notificaciones',
														'completo'
													)
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_COMPROMISOS_NOTIFICACIONES'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
											>
												Compromisos y notificaciones
											</inertia-link>
										</li>
										<li>
											<inertia-link
												:href="
													$route(
														'rep.cre.compromisos_notificaciones',
														'personal'
													)
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MIS_COMPROMISOS_NOTIFICACIONES'
													)
												"
											>
												Mis compromisos y notificaciones
											</inertia-link>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.informe_equifax', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_INFORME_EQUIFAX'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Informe central de riesgo</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cre.informe_equifax', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MI_INFORME_EQUIFAX'
													)
												"
												>Mi informe central de riesgo</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cre.cancelados_parcial', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_CANCELADOS_PARCIAL'
													)
												"
												>Cancelados parcial</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.desembosos_tipo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_DESEMBOLSOS_TIPO'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Desembolsos por tipo</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.mora_asesor', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MORA_ASESOR'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Mora por asesor</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.cre.mora_asesor', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MI_MORA_ASESOR'
													)
												"
												>Mi Mora por asesor</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="
													$route('rep.cre.productividad_asesor', 'completo')
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_PRODUCTIVIDAD_ASESOR'
													)
												"
												>Productividad como asesor</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="
													$route('rep.cre.productividad_asesor', 'personal')
												"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_MI_PRODUCTIVIDAD_ASESOR'
													)
												"
												>Mi productividad como asesor</inertia-link
											>
										</li>
										<li>
											<inertia-link
												class="highlight"
												:href="$route('rep.cre.aprobaciones_anuladas')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CREDITOS_ANULADOS'
													)
												"
												:style="
													windowWidth >= 900
														? ''
														: 'background-color: var(--plomoOscuroEmpresarial) !important; color: white !important'
												"
												>Créditos anulados</inertia-link
											>
										</li>
									</ul>
								</li>
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_REPORTES/INVERSION')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Inversión<span
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
												:href="$route('rep.inv.movimientos_meta')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/INVERSION_MOVIMIENTOS_META'
													)
												"
												>Movimientos de inversión meta</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.inv.avance_meta', 'completo')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/INVERSION_AVANCE_META'
													)
												"
												>Avance de inversiones meta</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('rep.inv.avance_meta', 'personal')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/INVERSION_MI_AVANCE_META'
													)
												"
												>Mi avance de inversiones meta</inertia-link
											>
										</li>
									</ul>
								</li>

								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_REPORTES/CONTABILIDAD')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Contabilidad<span
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
												:href="$route('rep.con.transacciones_reales')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_REPORTES/CONTABILIDAD_TRANSACCIONES_REALES'
													)
												"
												>Transacciones reales</inertia-link
											>
										</li>
									</ul>
								</li>
							</ul>
						</li>

						<li
							v-if="
								$page.props.user_permissions.permisos.filter((item) =>
									item.includes('CREDITOS_MANTENIMIENTO')
								).length > 0
							"
						>
							<a href="javascript:void(0)"
								>Mantenimiento<span class="arrow-down"></span
							></a>
							<ul class="dropdown text-left always-over">
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_MANTENIMIENTO/TRANSACCION')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Transacciones<span
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
												:href="$route('man.tra.categorias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/TRANSACCION_CATEGORIAS'
													)
												"
												>Categorías</inertia-link
											>
										</li>

										<li>
											<inertia-link
												:href="$route('mant.transaccion_subcategoria')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/TRANSACCION_SUBCATEGORIAS'
													)
												"
												>Subcategorías</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('mant.transaccion_comprobantes')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/TRANSACCION_COMPROBANTES'
													)
												"
												>Comprobantes</inertia-link
											>
										</li>
									</ul>
								</li>
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_MANTENIMIENTO/CREDITO')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Crédito<span
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
												:href="$route('man.cre_sectores')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CREDITO_SECTORES'
													)
												"
												>Sectores</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('man.cre_productos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CREDITO_PRODUCTOS'
													)
												"
												>Productos</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('man.cre_subproductos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CREDITO_SUBPRODUCTOS'
													)
												"
												>Subproductos</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('man.cre_tipos')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CREDITO_TIPOS'
													)
												"
												>Tipos</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('man.cre_garantias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CREDITO_GARANTIAS'
													)
												"
												>Garantías</inertia-link
											>
										</li>
										<li>
											<inertia-link
												:href="$route('man.cre_estados')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CREDITO_ESTADOS'
													)
												"
												>Estados</inertia-link
											>
										</li>
									</ul>
								</li>

								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_MANTENIMIENTO/CLIENTE')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Cliente<span
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
												:href="$route('man.cli_album_categorias')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/CLIENTE_ALBUM_CATEGORIAS'
													)
												"
												>Categorías de albúm</inertia-link
											>
										</li>
									</ul>
								</li>
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_MANTENIMIENTO/FACTURACION')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Facturación<span
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
												:href="$route('man.fac_limites')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/FACTURACION_LIMITES'
													)
												"
												>Lí­mite anual</inertia-link
											>
										</li>
									</ul>
								</li>
								<li
									v-if="
										$page.props.user_permissions.permisos.filter((item) =>
											item.includes('CREDITOS_MANTENIMIENTO/INVERSION')
										).length > 0
									"
								>
									<a class="text-left" href="javascript:void(0)"
										>Inversión<span
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
												:href="$route('man.inversion.productos_meta')"
												v-if="
													$page.props.user_permissions.permisos.includes(
														'CREDITOS_MANTENIMIENTO/INVERSION_PRODUCTOS_META'
													)
												"
												>Productos meta</inertia-link
											>
										</li>
									</ul>
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
					<inertia-link class="title-mobile" :href="$route('cre.index')"
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
				<span class="text-right text-white"
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

				<!-- <span class="ml-3 text-white">
					{{ $page.props.application.version[0].numeroVersion }}
				</span> -->

				<span class="ml-3 mr-3 text-white" style="text-transform: uppercase">
					.::AGENCIA {{ $page.props.user_session.nombre_agencia }}::.
				</span>
			</div>
		</footer>

		<botonInformativo
			ref="botonInformativo"
			:windowWidth="windowWidth"
		></botonInformativo>

		<!-- Modales -->

		<mdlBusquedaClientes ref="mdlBusquedaClientes"></mdlBusquedaClientes>

		<mdlAlbumFotos ref="mdlAlbumFotos"> </mdlAlbumFotos>

		<mdlHistorialCrediticio ref="mdlHistorialCrediticio">
		</mdlHistorialCrediticio>

		<mdlDetallePagos ref="mdlDetallePagos"> </mdlDetallePagos>

		<mdlBusquedaCreditos ref="mdlBusquedaCreditos"></mdlBusquedaCreditos>

		<mdlBusquedaCreditosExterna
			ref="mdlBusquedaCreditosExterna"
		></mdlBusquedaCreditosExterna>

		<mdlExonerarComision ref="mdlExonerarComision"></mdlExonerarComision>

		<mdlBusquedaInversiones
			ref="mdlBusquedaInversiones"
		></mdlBusquedaInversiones>

		<mdlBusquedaInversionesExterna
			ref="mdlBusquedaInversionesExterna"
		></mdlBusquedaInversionesExterna>

		<mdlSimuladorRiesgo ref="mdlSimuladorRiesgo"></mdlSimuladorRiesgo>

		<mdlSimuladorBilleteo ref="mdlSimuladorBilleteo"></mdlSimuladorBilleteo>

		<mdlClaveTransaccion ref="mdlClaveTransaccion"></mdlClaveTransaccion>

		<mdlControlMoraAgencia ref="mdlControlMoraAgencia"></mdlControlMoraAgencia>

		<mdlControlMoraAgenciaMensual
			ref="mdlControlMoraAgenciaMensual"
		></mdlControlMoraAgenciaMensual>

		<mdlDesembolsosPorAsesor
			ref="mdlDesembolsosPorAsesor"
		></mdlDesembolsosPorAsesor>
	</div>
</template>

<script>
import mdlBusquedaClientes from "@/Pages/Creditos/Clientes/Components/mdlBusquedaClientes.vue";
import mdlAlbumFotos from "@/Pages/Creditos/Clientes/Components/mdlAlbumFotos.vue";
import mdlHistorialCrediticio from "@/Pages/Creditos/Creditos/Components/mdlHistorialCrediticio.vue";
import mdlDetallePagos from "@/Pages/Creditos/Caja/Components/mdlDetallePagos.vue";
import mdlBusquedaCreditos from "@/Pages/Creditos/Creditos/Components/mdlBusquedaCreditos.vue";
import mdlBusquedaCreditosExterna from "@/Pages/Creditos/Creditos/Components/mdlBusquedaCreditosExterna.vue";
import mdlExonerarComision from "@/Pages/Creditos/Caja/Components/mdlExonerarComision.vue";
import mdlBusquedaInversiones from "@/Pages/Creditos/Inversion/Components/mdlBusquedaInversiones.vue";
import mdlBusquedaInversionesExterna from "@/Pages/Creditos/Inversion/Components/mdlBusquedaInversionesExterna.vue";
import mdlSimuladorRiesgo from "@/Pages/Creditos/Herramientas/Components/mdlSimuladorRiesgo.vue";
import mdlSimuladorBilleteo from "@/Pages/Creditos/Herramientas/Components/mdlSimuladorBilleteo.vue";
import mdlClaveTransaccion from "@/Pages/Creditos/Herramientas/Components/mdlClaveTransaccion.vue";
import mdlControlMoraAgencia from "@/Pages/Creditos/Reportes/Components/mdlControlMoraAgencia.vue";
import mdlControlMoraAgenciaMensual from "@/Pages/Creditos/Reportes/Components/mdlControlMoraAgenciaMensual.vue";

import mdlDesembolsosPorAsesor from "@/Pages/Creditos/Reportes/Components/mdlDesembolsosPorAsesor.vue";

import botonInformativo from "@/Pages/Creditos/Components/boton_informativo.vue";

import { DateTime } from "luxon";

export default {
	components: {
		mdlBusquedaClientes,
		mdlAlbumFotos,
		mdlHistorialCrediticio,
		mdlDetallePagos,
		mdlBusquedaCreditos,
		mdlBusquedaCreditosExterna,
		mdlExonerarComision,
		mdlBusquedaInversiones,
		mdlBusquedaInversionesExterna,
		mdlSimuladorRiesgo,
		mdlSimuladorBilleteo,
		mdlClaveTransaccion,
		mdlControlMoraAgencia,
		mdlControlMoraAgenciaMensual,
		mdlDesembolsosPorAsesor,
		botonInformativo,
	},
	props: { agencias: Array },
	data() {
		return {
			fecha_actual: null,
			windowWidth: window.innerWidth,
			windowHeight: window.innerHeight,
			agencias_permitidas: [],
			lista_resumen: [],

			usuarios: [],
		};
	},

	mounted() {
		window.addEventListener("resize", () => {
			this.windowWidth = window.innerWidth;
			this.windowHeight = window.innerHeight;
		});

		document.title = "Créditos";
		document.body.style.backgroundImage =
			"url('/images/creditos/fondo_principal.png')";

		document.body.style.backgroundPosition = "center center";
		document.body.style.backgroundRepeat = "repeat-yt";
		document.body.style.backgroundAttachment = "fixed";
		document.body.style.backgroundSize = "cover";
		document.body.style.fontFamily = "Roboto, sans-serif";

		let parts = this.$page.props.application.data_local
			.filter((item) => item.descripcion == "FECHA_CREDITOS")[0]
			.valor_fecha.split("-");

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
	},

	created() {
		history.pushState(null, "", window.location.href);
	},
	methods: {
		comprobarSesion() {
			let self = this;
			axios
				.post(route("comprobar_sesion"))
				.then(function (response) {
					if (response.data.sesion == false) {
						Swal.fire({
							title: "SESION CADUCADA",
							text: "Su sesión ha caducado, porfavor vuelva a iniciar sesión",
							confirmButtonText:
								'<i class="fas fa-check" style="color:white;"></i>   Si',
							confirmButtonColor: "var(--colorAlto)",
							allowOutsideClick: false,
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = route("login");
							} else {
								return false;
							}
						});
					}
				})
				.catch(function (error) {
					Swal.fire({
						title: "SESION CADUCADA",
						text: "Su sesión ha caducado, porfavor vuelva a iniciar sesión",
						confirmButtonText:
							'<i class="fas fa-check" style="color:white;"></i>   Si',
						confirmButtonColor: "var(--colorAlto)",
						allowOutsideClick: false,
					}).then((result) => {
						if (result.isConfirmed) {
							window.location.href = route("login");
						} else {
							return false;
						}
					});
				});
		},
		filtrar_agencias(permiso) {
			let agencias = this.$inertia.page.props.application.agencias;

			let permiso_detalle =
				this.$inertia.page.props.user_permissions.permisos_detalle.filter(
					(item) => item.permiso == permiso
				);

			let agencias_filtradas = [];

			if (permiso_detalle.length > 0) {
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
			}

			return agencias_filtradas;
		},

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
		fecha_hora_actual(agencia_id) {
			return axios
				.post(route("fecha_hora_agencia", { agencia_id: agencia_id }))
				.then(function (response) {
					return response.data;
				});
		},
		ValidarMesesBusqueda(fecha_desde, fecha_hasta, tiempo_meses) {
			let resultado = "NO_VALIDADO";

			if (fecha_desde != null && fecha_hasta != null) {
				const fecha1 = DateTime.fromISO(fecha_desde);
				const fecha2 = DateTime.fromISO(fecha_hasta);

				const diferencia_meses = fecha2.diff(fecha1, "months").months;

				if (diferencia_meses <= tiempo_meses) {
					resultado = "VALIDADO";
				}
			}

			return resultado;
		},
		AbrirModal(nombre_modal, nombre_modulo, permiso) {
			this.comprobarSesion();
			let modulo = null;

			if (nombre_modal == "mdlBusquedaClientes") {
				if (nombre_modulo == "crear_producto_meta") {
					if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Primero debe aperturar CAJA",
							confirmButtonText:
								'<i class="fas fa-check" style="color:white;"></i>   Ok',
							confirmButtonColor: "var(--colorAlto)",
							allowOutsideClick: true,
						});
						return false;
					}
				}
				modulo = this.$refs.mdlBusquedaClientes;
				modulo.nombre_modulo = nombre_modulo;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.texto_buscar = null;

				this.$nextTick(() => modulo.$refs.buscar_cliente.focus());
			} else if (nombre_modal == "mdlExonerarComision") {
				modulo = this.$refs.mdlExonerarComision;
				modulo.aprobados = [];
				modulo.nombre_modulo = nombre_modulo;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.texto_buscar = null;
			} else if (nombre_modal == "mdlBusquedaCreditos") {
				if (nombre_modulo == "desembolso" || nombre_modulo == "cobranza") {
					if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "Primero debe aperturar CAJA",
							confirmButtonText:
								'<i class="fas fa-check" style="color:white;"></i>   Ok',
							confirmButtonColor: "var(--colorAlto)",
							allowOutsideClick: true,
						});
						return false;
					}
				}
				modulo = this.$refs.mdlBusquedaCreditos;
				modulo.nombre_modulo = nombre_modulo;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.texto_buscar = null;
				this.$nextTick(() => modulo.$refs.buscar_credito.focus());
			} else if (nombre_modal == "mdlBusquedaCreditosExterna") {
				if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Primero debe aperturar CAJA",
						confirmButtonText:
							'<i class="fas fa-check" style="color:white;"></i>   Ok',
						confirmButtonColor: "var(--colorAlto)",
						allowOutsideClick: true,
					});
					return false;
				}

				modulo = this.$refs.mdlBusquedaCreditosExterna;
				modulo.nombre_modulo = nombre_modulo;
				modulo.texto_buscar = null;
				this.$nextTick(() => modulo.$refs.buscar_credito.focus());
			} else if (nombre_modal == "mdlClaveTransaccion") {
				modulo = this.$refs.mdlClaveTransaccion;
				modulo.Generar();
			} else if (nombre_modal == "mdlSimuladorRiesgo") {
				modulo = this.$refs.mdlSimuladorRiesgo;
				modulo.Resetear();
				modulo.ListarRangos();
			} else if (nombre_modal == "mdlSimuladorBilleteo") {
				modulo = this.$refs.mdlSimuladorBilleteo;
				modulo.Resetear();
			} else if (nombre_modal == "mdlControlMoraAgencia") {
				let modulo = this.$refs.mdlControlMoraAgencia;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.filtro_usuario = false;
				modulo.Actualizar();
				modulo.destruirtabla();
				modulo.TablaCarteraMora();
				return $("#mdlControlMoraAgencia").css("display", "block");
			} else if (nombre_modal == "mdlControlMoraAgenciaMensual") {
				let modulo = this.$refs.mdlControlMoraAgenciaMensual;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.filtro_usuario = false;
				modulo.Actualizar();
				return $("#mdlControlMoraAgenciaMensual").css("display", "block");
			} else if (nombre_modal == "mdlDesembolsosPorAsesor") {
				let modulo = this.$refs.mdlDesembolsosPorAsesor;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.lista_desembolsos = [];
				if (nombre_modulo == "DESEMBOLSOS_ASESOR") {
					modulo.titulo_modal = "DESEMBOLSOS POR ASESOR";
					modulo.modo = "completo";
				} else if (nombre_modulo == "MIS_DESEMBOLSOS_ASESOR") {
					modulo.titulo_modal = "MIS DESEMBOLSOS - ASESOR";
					modulo.modo = "personal";
				}
				modulo.ListarRecursos();

				return $("#mdlDesembolsosPorAsesor").css("display", "block");
			} else if (nombre_modal == "mdlBusquedaInversiones") {
				if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Primero debe aperturar CAJA",
						confirmButtonText:
							'<i class="fas fa-check" style="color:white;"></i>   Ok',
						confirmButtonColor: "var(--colorAlto)",
						allowOutsideClick: true,
					});
					return false;
				}
				modulo = this.$refs.mdlBusquedaInversiones;
				modulo.nombre_modulo = nombre_modulo;
				modulo.agencias_permitidas = this.filtrar_agencias(permiso);
				modulo.texto_buscar = null;
				this.$nextTick(() => modulo.$refs.buscar_inversion.focus());
			} else if (nombre_modal == "mdlBusquedaInversionesExterna") {
				if (this.$inertia.page.props.creditos_datos.datos_caja == null) {
					Swal.fire({
						icon: "error",
						title: "¡Ups!",
						text: "Primero debe aperturar CAJA",
						confirmButtonText:
							'<i class="fas fa-check" style="color:white;"></i>   Ok',
						confirmButtonColor: "var(--colorAlto)",
						allowOutsideClick: true,
					});
					return false;
				}
				modulo = this.$refs.mdlBusquedaInversionesExterna;
				modulo.nombre_modulo = nombre_modulo;
				modulo.texto_buscar = null;
				this.$nextTick(() => modulo.$refs.buscar_inversion.focus());
			}

			$("#" + nombre_modal).css("display", "block");
		},

		round(value, decimal_places) {
			return parseFloat(value).toFixed(decimal_places);
		},
	},
};
</script>

<style lang="css">
:root {
	/* --colorBajo: #f6feff; */
	--colorBajo: #f6feff;
	/* --colorBajo: #def7ff; */
	/* --colorMedio: #337ba3; */
	--colorMedio: var(--verdeOscuroEmpresarial);
	--colorAlto: var(--azulOscuroEmpresarial);
	/* --colorAlto: var(--verdeOscuroEmpresarial); */
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
	padding: 5px 20px;
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
	/* padding: 8px 15px; */
	font-size: 15px;
	color: var(--plomoOscuroEmpresarial);
	display: block;
	font-weight: 400;
}

.nav-fostrap li ul.dropdown li a.highlight {
	background: var(--plomoOscuroEmpresarial);
	color: white;
}

.nav-fostrap li ul.dropdown li a.highlight-2 {
	background: var(--verdeClaroEmpresarial);
	color: var(--plomoOscuroEmpresarial);
}

.nav-fostrap li ul.dropdown li ul.dropdown li a {
	/* padding: 8px 15px; */
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
	color: var(--colorAlto);
}

.nav-fostrap li:first-child:hover a {
	border-radius: 3px 0 0 3px;
}

.nav-fostrap li ul.dropdown li:hover a {
	background: var(--verdeOscuroEmpresarial);
	color: white !important;
}

.nav-fostrap li ul.dropdown li:hover ul.dropdown li a {
	background: #ffff;
	color: var(--plomoOscuroEmpresarial) !important;
}

.nav-fostrap li ul.dropdown li:hover ul.dropdown li a.highlight {
	background: var(--plomoOscuroEmpresarial);
	color: white !important;
}

.nav-fostrap li ul.dropdown li ul.dropdown li:hover a {
	background: var(--verdeOscuroEmpresarial);
	color: white !important;
}

.nav-fostrap li ul.dropdown li ul.dropdown li:hover a.highlight {
	background: var(--verdeOscuroEmpresarial);
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
	background: var(--plomoOscuroEmpresarial);
	font-size: 0.7rem;
	color: var(--verdeOscuroEmpresarial);
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

/* .slot-content::-webkit-scrollbar {
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
} */

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
	height: 90%;
}

body.swal2-height-auto {
	height: 100% !important;
}

/* td:hover {
  background-color: #c3e2ff;
} */

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

@media only screen and (max-width: 1127px) {
	.container {
		width: 96%;
	}
}
@media only screen and (max-width: 900px) {
	.container {
		width: 96%;
	}
}
</style>
