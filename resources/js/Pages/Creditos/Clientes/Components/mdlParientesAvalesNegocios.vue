<template>
	<div>
		<div id="mdlParientesAvalesNegocio" class="modal">
			<!-- Modal content -->
			<div class="modal-content mdlParientesAvalesNegocio w-55">
				<div class="content" style="display: block">
					<div class="card">
						<div class="card-header">
							<strong id="title">PARIENTES, AVALES Y NEGOCIO</strong>
						</div>
						<!-- <div class="card-title">INFORMACIÓN PERSONAL</div> -->
						<div class="card-body card-block">
							<div class="form-row">
								<div class="form-group col-md-2 col-12 center">
									<button
										class="btn btn-action btn-icon-split btn-modal-group"
										title="Parientes"
										@click="VerParientes()"
									>
										<span class="text">Parientes</span>
									</button>
								</div>
								<div class="form-group col-md-2 col-12 center">
									<button
										class="btn btn-action btn-icon-split btn-modal-group"
										title="Avales"
										@click="VerAvales()"
									>
										<span class="text">Avales</span>
									</button>
								</div>
								<div class="form-group col-md-2 col-12 center">
									<button
										class="btn btn-action btn-icon-split btn-modal-group"
										title="Negocio"
										@click="VerNegocio()"
									>
										<span class="text">Negocio</span>
									</button>
								</div>
								<div class="form-group col-md-3 col-12 center">
									<button
										class="btn btn-cancel btn-icon-split btn-modal-group"
										title="Es pariente de..."
										@click="VerParientesDependientes()"
									>
										<span class="text">Es pariente de...</span>
									</button>
								</div>
								<div class="form-group col-md-3 col-12 center">
									<button
										class="btn btn-cancel btn-icon-split btn-modal-group"
										title="Es aval de..."
										@click="VerAvalesDependientes()"
									>
										<span class="text">Es aval de...</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<mdlParientesAvales
			:agencias="agencias"
			:departamentos="departamentos"
			:provincias="provincias"
			:distritos="distritos"
			:agencia_seleccionada="agencia_seleccionada"
			ref="mdlParientesAvales"
		></mdlParientesAvales>

		<mdlParientesAvalesDependientes
			ref="mdlParientesAvalesDependientes"
		></mdlParientesAvalesDependientes>

		<mdlNegocios
			:agencias="agencias"
			:departamentos="departamentos"
			:provincias="provincias"
			:distritos="distritos"
			:agencia_seleccionada="agencia_seleccionada"
			ref="mdlNegocios"
		></mdlNegocios>
	</div>
</template>

<script>
import mdlParientesAvales from "@/Pages/Creditos/Clientes/Components/mdlParientesAvales.vue";
import mdlNegocios from "@/Pages/Creditos/Clientes/Components/mdlNegocios.vue";
import mdlParientesAvalesDependientes from "@/Pages/Creditos/Clientes/Components/mdlParientesAvalesDependientes.vue";
export default {
	props: {
		agencias: Array,
		distritos: Array,
		provincias: Array,
		departamentos: Array,
		agencia_seleccionada: Number,
	},
	components: {
		mdlParientesAvales,
		mdlNegocios,
		mdlParientesAvalesDependientes,
	},
	data() {
		return {
			lista_parientes: [],
			lista_avales: [],
			lista_negocios: [],
			lista_parientes_dependientes: [],
			lista_avales_dependientes: [],
			frmParienteAval: {
				cliente_id: null,
			},
			frmNegocio: { cliente_id: null },
		};
	},
	methods: {
		ListarParientesAvalesNegocios() {
			let self = this;

			axios
				.post(
					route("cli.listado_registro.parientes_avales_negocios", {
						cliente_id: self.frmParienteAval.cliente_id,
						agencia_id: self.agencia_seleccionada,
					})
				)
				.then(function (response) {
					self.lista_parientes = response.data.parientes;
					self.lista_avales = response.data.avales;
					self.lista_negocios = response.data.negocios;
					self.lista_parientes_dependientes =
						response.data.parientes_dependientes;
					self.lista_avales_dependientes = response.data.avales_dependientes;
				});
		},
		VerParientes() {
			let mdlParientesAvales = this.$refs.mdlParientesAvales;

			mdlParientesAvales.lista_parientes_avales = this.lista_parientes;
			mdlParientesAvales.ResetearFrmParienteAval();

			mdlParientesAvales.submited = false;
			mdlParientesAvales.frmParienteAval.modo = "NO-EDITAR";
			mdlParientesAvales.frmParienteAval.tipo = "PARIENTE";
			mdlParientesAvales.frmParienteAval.cliente_id =
				this.frmParienteAval.cliente_id;

			$("#mdlVerParientesAvales").css("display", "block");
			$("#datosParienteAval1-tab").tab("show");
		},
		VerAvales() {
			let mdlParientesAvales = this.$refs.mdlParientesAvales;

			mdlParientesAvales.lista_parientes_avales = this.lista_avales;
			mdlParientesAvales.ResetearFrmParienteAval();

			mdlParientesAvales.frmParienteAval.submited = false;
			mdlParientesAvales.frmParienteAval.tipo = "AVAL";
			mdlParientesAvales.frmParienteAval.modo = "NO-EDITAR";

			mdlParientesAvales.frmParienteAval.cliente_id =
				this.frmParienteAval.cliente_id;
			$("#mdlVerParientesAvales").css("display", "block");
			$("#datosParienteAval1-tab").tab("show");
		},
		VerNegocio() {
			let mdlNegocios = this.$refs.mdlNegocios;

			mdlNegocios.lista_negocios_actuales = this.lista_negocios;
			mdlNegocios.negocio_activo = this.lista_negocios.filter(
				(item) => item.vinculado == 1
			)[0];
			mdlNegocios.ResetearFrmNegocio();
			mdlNegocios.submited = false;
			mdlNegocios.frmNegocio.modo = "NO-EDITAR";

			mdlNegocios.frmNegocio.cliente_id = this.frmNegocio.cliente_id;

			$("#mdlNegocios").css("display", "block");
			$("#datosNegocio1-tab").tab("show");
		},
		VerParientesDependientes() {
			let mdlParientesAvalesDependientes =
				this.$refs.mdlParientesAvalesDependientes;

			mdlParientesAvalesDependientes.frmParienteAval.tipo = "PARIENTE";
			mdlParientesAvalesDependientes.lista_parientes_avales_dependientes =
				this.lista_parientes_dependientes;

			$("#mdlParientesAvalesDependientes").css("display", "block");
		},
		VerAvalesDependientes() {
			let mdlParientesAvalesDependientes =
				this.$refs.mdlParientesAvalesDependientes;

			mdlParientesAvalesDependientes.frmParienteAval.tipo = "AVAL";
			mdlParientesAvalesDependientes.lista_parientes_avales_dependientes =
				this.lista_avales_dependientes;

			$("#mdlParientesAvalesDependientes").css("display", "block");
		},
	},
};
</script>

<style>
.mdlParientesAvalesNegocio {
	margin-top: 2% !important;
}

@media (max-width: 900px) {
	.mdlParientesAvalesNegocio {
		width: 99% !important;
		margin-left: 0.5% !important;

		margin-top: 15% !important;
	}
}
</style>
