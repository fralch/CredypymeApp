<template>
	<div>
		<div id="mdlEvaluacionUnidades" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-40 mdlEvaluacionUnidades">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>{{ title_modal }}</strong>

							<button
								type="button"
								class="btn btn-green"
								style="border-radius: 50%; float: right !important"
								@click="Cerrar('mdlEvaluacionUnidades')"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-body card-block">
							<table class="table" id="tblUnidades" width="100% !important">
								<thead>
									<tr>
										<th></th>
										<th>DESCRIPCIÓN</th>
										<th>MONTO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(item, index) in lista_datos" :key="index">
										<td
											class="table-bordered"
											align="center"
											width="60px !important"
										>
											<div class="btn-group" role="group">
												<button
													class="btn btn-action btn-icon-split"
													title="Editar"
													@click="Editar(item)"
												>
													<span class="icon text-white">
														<i class="fas fa-edit"></i>
													</span>
												</button>
											</div>
										</td>
										<td class="table-bordered" align="left">
											{{ item.descripcion }}
										</td>
										<td
											class="table-bordered"
											align="center"
											width="100px !important"
										>
											{{ parseFloat(item.monto).toFixed(2) }}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="mdlEditarUnidad" class="modal">
			<!-- Modal content -->
			<div class="modal-content w-25 mdlEditarUnidad sub-modal-middle">
				<div class="content" style="display: block">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between"
						>
							<strong>{{ frmDatosUnidad.descripcion }}</strong>

							<button
								type="button"
								class="btn btn-green"
								style="border-radius: 50%; float: right !important"
								@click="Cerrar('mdlEditarUnidad')"
							>
								<span class="icon text-white">
									<i class="fas fa-times"></i>
								</span>
							</button>
						</div>

						<div class="card-body card-block">
							<div class="form-row justify-content-center">
								<div class="col-md-4 col-4 text-right">
									<label class="form-control-label">MONTO S/ </label>
									<span
										v-if="submited && !$v.frmDatosUnidad.monto.required"
										class="span-error-message"
									>
										*
									</span>
								</div>
								<div class="col-md-5 col-6">
									<input
										type="number"
										class="form-control center"
										style="font-weight: bolder"
										@change="Redondear"
										v-model.number="frmDatosUnidad.monto"
										min="0"
										autocomplete="off"
										@focus="hidenav()"
										@blur="shownav()"
									/>
								</div>
							</div>
							<hr />
							<div class="text-right">
								<div class="btn-group" role="group">
									<button
										class="btn btn-action btn-icon-split"
										@click="Guardar"
									>
										<span class="icon text-white">
											<i class="fas fa-save"></i>
										</span>
										<span class="text">GUARDAR</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
import { required } from "vuelidate/lib/validators";
export default {
	props: { evaluacion_id: Number, cliente_id: Number, agencia_id: Number },
	data() {
		return {
			submited: false,
			grupo: null,
			sub_grupo: null,
			title_modal: null,
			lista_datos: [],
			ruta_guardar: null,
			frmDatosUnidad: {
				id: null,
				descripcion: null,
				monto: 0,
			},
		};
	},
	validations: {
		frmDatosUnidad: {
			monto: { required },
		},
	},
	watch: {
		grupo(value) {
			if (value == "ACTIVO_CORRIENTE") {
				this.ruta_guardar = "cre.evaluacion.activo_corriente.guardar";
			} else if (value == "ACTIVO_NO_CORRIENTE") {
				this.ruta_guardar = "cre.evaluacion.activo_no_corriente.guardar";
			} else if (value == "PASIVO_CORRIENTE") {
				this.ruta_guardar = "cre.evaluacion.pasivo_corriente.guardar";
			} else if (value == "FLUJO_CAJA") {
				this.ruta_guardar = "cre.evaluacion.flujo_caja.guardar";
			} else if (value == "COMENTARIOS") {
				this.ruta_guardar = "cre.evaluacion.comentarios.guardar";
			}
		},
		lista_datos() {
			this.RecrearTabla();
		},
	},
	mounted() {
		this.TablaUnidades();
	},
	methods: {
		hidenav() {
			return this.$parent.hide_nav();
		},
		shownav() {
			return this.$parent.show_nav();
		},
		TablaUnidades() {
			let self = this;
			this.$nextTick(() => {
				var table = $("#tblUnidades").DataTable({
					scrollY: "550px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					info: false,
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
					responsive: true,
				});
			});
		},
		RecrearTabla() {
			$("#tblUnidades").DataTable().destroy();
			this.TablaUnidades();
		},
		Redondear(e) {
			let valor = 0;
			let numero_decimales = 2;

			if (e.target.value && e.target.value >= 0) {
				valor = e.target.value;
			}
			this.frmDatosUnidad.monto = this.$parent.round(valor, numero_decimales);
		},
		Cerrar(ventana) {
			$("#" + ventana).css("display", "none");
		},
		Editar(item) {
			this.submited = false;
			this.frmDatosUnidad.id = item.id;
			this.frmDatosUnidad.descripcion = item.descripcion;

			this.frmDatosUnidad.monto = item.monto;
			$("#mdlEditarUnidad").css("display", "block");
		},
		Guardar() {
			let self = this;
			this.submited = true;
			if (this.$v.frmDatosUnidad.$invalid) {
				Swal.fire({
					icon: "warning",
					title: "¡Ups!",
					text: "Complete todos los campos",
					allowOutsideClick: true,
				});
				return false;
			}

			Swal.fire({
				icon: "question",
				text: "¿Desea guardar los cambios?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
			}).then((result) => {
				if (result.isConfirmed) {
					self.lista_datos.filter(
						(item) => item.id == self.frmDatosUnidad.id
					)[0].monto = self.frmDatosUnidad.monto;

					let data = new FormData();
					data.append("evaluacion_id", self.evaluacion_id);
					data.append("cliente_id", self.cliente_id);
					data.append("sub_grupo", self.sub_grupo);
					data.append(
						"valor",
						self.lista_datos.length == 0
							? null
							: JSON.stringify(self.lista_datos)
					);
					data.append("agencia_id", self.agencia_id);
					self.$inertia.post(route(this.ruta_guardar), data, {
						preserveScroll: true,
						onStart: (visit) => {
							let timerInterval;
							Swal.fire({
								title: "CARGANDO",
								html: "Espere porfavor...",
								timer: 300,
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
							self.submited = false;
							self.RecrearTabla();
							$("#mdlEditarUnidad").css("display", "none");
						},
					});
				} else {
					return false;
				}
			});
		},
	},
};
</script>

<style lang="css">
/* .mdlEvaluacionUnidades {
	margin-top: 5%;
} */

.sub-modal-middle {
	margin-top: 15%;
}

@media only screen and (max-width: 900px) {
	.mdlEvaluacionUnidades {
		margin-top: 10%;
	}

	.sub-modal-middle {
		margin-top: 50%;
	}

	.mdlEditarUnidad {
		margin-top: 20% !important;
	}
}
</style>
