<template>
	<div class="modal" id="filtro_usuario">
		<div class="modal-content w-25 filtro_usuario">
			<div class="content" style="display: block">
				<div class="card">
					<div
						class="card-header d-flex align-items-center justify-content-between"
					>
						<strong>SELECCIONAR USUARIOS</strong>
						<button
							type="button"
							class="btn btn-green"
							style="border-radius: 50%; float: right !important"
							@click="Cerrar"
						>
							<span class="icon text-white">
								<i class="fas fa-times"></i>
							</span>
						</button>
					</div>
					<div class="card-title">RESULTADOS</div>
					<div class="card-body card-block">
						<div class="form-group">
							<div class="input-group col-md-12" v-if="modo_agencias == true">
								<select
									class="form-control center"
									v-model="agencia_seleccionada"
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
							<div style="box-shadow: 1px 0px 5px var(--plomoClaroEmpresarial)">
								<div class="form-check text-center mt-1">
									<input
										class="form-check-input"
										type="checkbox"
										id="chbMostrarHabilitados"
										v-model="mostrar_habilitados"
									/>
									<label class="label-title" for="chbMostrarHabilitados"
										>Sólo habilitados</label
									>
								</div>
								<DataTable
									:value="usuarios_filtrados"
									:scrollable="true"
									scrollDirection="both"
									scrollHeight="300px"
									:paginator="false"
								>
									<Column
										field="usuario"
										header="USUARIO"
										:styles="{ width: '40px' }"
									>
										<template #body="{ data, index }">
											<div
												class="custom-control custom-checkbox pl-1 m-0"
												style="min-height: auto !important"
											>
												<input
													type="checkbox"
													class="custom-control-input"
													:id="'chbUsuario_' + index"
													:value="data.dni"
													v-model="usuarios_seleccionados"
												/>
												<label
													class="custom-control-label ml-4"
													:for="'chbUsuario_' + index"
													>{{ data.usuario }}</label
												>
											</div>
										</template>
									</Column>
								</DataTable>
							</div>
							<hr />
							<div class="text-right">
								<button
									class="btn btn-action btn-icon-split"
									@click="AgregarUsuarios()"
								>
									<span class="icon text-white">
										<i class="fas fa-check"></i>
									</span>
									<span class="text">LISTO</span>
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
import DataTable from "primevue/datatable/datatable.common";
import Column from "primevue/column/column.common";

import { required } from "vuelidate/lib/validators";
const noZero = (value) => value != 0;
export default {
	components: { headerCloseModal, DataTable, Column },

	data() {
		return {
			mostrar_habilitados: true,
			usuarios: [],
			usuarios_filtrados: [],
			usuarios_seleccionados: [],

			agencias_permitidas: [],
			agencia_seleccionada: 0,

			modo_agencias: false,
		};
	},

	mounted() {},

	watch: {
		mostrar_habilitados() {
			this.FiltrarUsuarios();
		},

		agencia_seleccionada() {
			this.FiltrarUsuarios();
		},
	},

	methods: {
		FiltrarUsuarios() {
			this.usuarios_filtrados = [];
			this.usuarios_seleccionados = [];

			if (this.mostrar_habilitados) {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) =>
						item.habilitado == 1 && item.agencia_id == this.agencia_seleccionada
				);
			} else {
				this.usuarios_filtrados = this.usuarios.filter(
					(item) => item.agencia_id == this.agencia_seleccionada
				);
			}
		},

		Cerrar() {
			this.LimpiarDatos();
			$("#filtro_usuario").css("display", "none");
			this.$emit("cerrar-modal");
		},

		AgregarUsuarios() {
			if (this.usuarios_seleccionados.length == 0) {
				Swal.fire({
					icon: "error",
					title: "¡Ups!",
					text: "Debe seleccionar almenos un usuario",
					allowOutsideClick: true,
				});

				return false;
			} else {
				this.$emit("usuarios-seleccionados", this.usuarios_seleccionados);
				this.$emit("agencia-seleccionada", this.agencia_seleccionada);
				this.$emit("habilitado-seleccionado", this.mostrar_habilitados);
				this.LimpiarDatos();
				$("#filtro_usuario").css("display", "none");
			}
		},

		LimpiarDatos() {
			(this.mostrar_habilitados = true),
				(this.usuarios = []),
				(this.usuarios_filtrados = []),
				(this.usuarios_seleccionados = []),
				(this.agencias_permitidas = []),
				(this.agencia_seleccionada = 0),
				(this.modo_agencias = false);
		},
	},
};
</script>

<style lang="css">
.filtro_usuario {
	margin-top: 2%;
}
.custom-control {
	cursor: pointer !important;
}

@media (max-width: 900px) {
	.filtro_usuario {
		margin-top: 20%;
	}
}
</style>
