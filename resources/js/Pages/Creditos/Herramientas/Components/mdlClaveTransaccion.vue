<template>
	<div id="mdlClaveTransaccion" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-20 mdlClaveTransaccion">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						ref="headerCloseModal"
						:titulo_modal="'CLAVE PARA TRANSACCIÓN'"
						:nombre_modal="'mdlClaveTransaccion'"
					>
					</headerCloseModal>

					<div class="card-body card-block">
						<div class="form-row">
							<div class="input-group">
								<div class="input-group-prepend">
									<button
										class="btn btn-action btn-icon-split"
										@click="Generar"
									>
										<span class="icon text-white">
											<i class="fas fa-sync"></i
										></span>
										<span class="text">GENERAR</span>
									</button>
								</div>
								<input
									type="text"
									id="inpClaveTransaccion"
									class="form-control center"
									style="font-size: 15px"
									v-model="clave_transaccion"
									readonly
								/>
							</div>
						</div>
						<hr />
						<div class="text-right">
							<button class="btn btn-action btn-icon-split" @click="Copiar">
								<span class="icon text-white">
									<i class="far fa-copy"></i
								></span>
								<span class="text">COPIAR</span>
							</button>
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
	components: { headerCloseModal },
	data() {
		return {
			clave_transaccion: null,
		};
	},
	methods: {
		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},

		Generar() {
			let self = this;
			axios
				.post(route("caj.clave_transaccion.generar"))
				.then(function (response) {
					self.clave_transaccion = response.data;
				});
		},

		Copiar() {
			let content = document.getElementById("inpClaveTransaccion");
			content.select();
			document.execCommand("copy");

			let header = this.$refs.headerCloseModal;

			header.CerrarModal("mdlClaveTransaccion");
		},
	},
};
</script>

<style lang="css">
.mdlClaveTransaccion {
	margin-top: 8%;
}

@media (max-width: 1130px) {
	.mdlClaveTransaccion {
		width: 98% !important;
		margin-left: 1% !important;
		margin-top: 15% !important;
	}
}
</style>

