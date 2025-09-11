<template>
	<layout ref="layout">
		<div slot="component-view" class="slot_body slot_cambio_agencia">
			<div class="content" style="display: block">
				<div class="card">
					<headerClose :title="'CAMBIO DE AGENCIA'"></headerClose>
					<div class="card-title">
						SELECCIONE LA AGENCIA A LA CUÁL DESEA CAMBIAR
					</div>
					<div class="card-body card-block">
						<div class="form-row justify-content-md-center">
							<div class="input-group col-md-6 col-6">
								<div class="input-group-prepend">
									<span class="input-group-text prepend-title">AGENCIA</span>
								</div>
								<select
									class="form-control center"
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
						</div>
						<hr />
						<div class="text-center">
							<button
								class="btn btn-cancel btn-icon-split"
								title="CAMBIAR"
								@click="Cambiar"
							>
								<span class="icon text-white">
									<i class="pi pi-file-excel"></i>
								</span>
								<span class="text">CAMBIAR</span>
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
	components: {
		layout,
		headerClose,
	},
	data() {
		return {
			agencias_permitidas: [],

			agencia_seleccionada: 0,
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
		},
	},
	computed: {
		agencia_actual() {
			return this.$inertia.page.props.user_session.id_agencia;
		},
		usuario_id() {
			return this.$inertia.page.props.user_session.usuario_dni;
		},
	},

	mounted() {
		this.ListarAgenciasPermitidas();
	},

	methods: {
		ListarAgenciasPermitidas() {
			this.agencias_permitidas = this.$refs.layout.filtrar_agencias(
				"CREDITOS_HERRAMIENTAS/CAMBIO_AGENCIA"
			);

			let agencias = this.$inertia.page.props.application.agencias;

			if (this.agencias_permitidas.length == agencias.length) {
				let obj = {
					agencia: "TODAS",
					direccion: null,
					distrito: null,
					id: "TODAS",
				};

				this.agencias_permitidas.push(obj);
			}
		},
		Cambiar() {
			let self = this;
			this.submited = true;

			if (this.agencia_actual == this.agencia_seleccionada) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "La agencia seleccionada es tu agencia ACTUAL.",
				});
				return false;
			}

			Swal.fire({
				icon: "warning",
				title: "¿Desea cambiar de agencia?",
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
					data.append("usuario_id", this.usuario_id);
					data.append("agencia_actual", this.agencia_actual);
					data.append("agencia_nueva", this.agencia_seleccionada);

					// this.$inertia.post(route("her.cambio_agencia.guardar"), data);
					// return false;

					return axios
						.post(route("her.cambio_agencia.guardar"), data)
						.then(async () => {
							await location.reload();
						})
						.catch((error) => {
							console.log(error.response.data.error);
							Swal.showValidationMessage(
								`Ha ocurrido un error: COMUNICAR AL ÁREA DE SOPORTE`
							);
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

<style lang="scss">
.slot_cambio_agencia {
	width: 40% !important;
	margin-left: 30% !important;
}
</style>
