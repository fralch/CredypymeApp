<template>
	<div id="mdlDetallePrenda" class="modal">
		<!-- Modal content -->
		<div class="modal-content w-50 mdlDetallePrenda">
			<div class="content" style="display: block">
				<div class="card">
					<headerCloseModal
						:titulo_modal="'DETALLE DE PRENDA'"
						:nombre_modal="'mdlDetallePrenda'"
					>
					</headerCloseModal>

					<div class="card-title">LISTA DE PRENDAS</div>
					<div class="card-body card-block">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="datos-prenda-tab"
									data-toggle="tab"
									href="#datos-prenda"
									role="tab"
									aria-controls="datos-prenda"
									aria-selected="false"
									>DATOS DEL BIEN</a
								>
							</li>
							<li class="nav-item">
								<a
									class="nav-link tab-title"
									id="fotos-prenda-tab"
									data-toggle="tab"
									href="#fotos-prenda"
									role="tab"
									aria-controls="fotos-prenda"
									aria-selected="false"
									>FOTOS</a
								>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div
								class="tab-pane fade show active"
								id="datos-prenda"
								role="tabpanel"
								aria-labelledby="datos-prenda-tab"
							>
								<div class="form-row p-2">
									<div class="form-group col-md-2 col-3">
										<label class="label-title">CANTIDAD</label>

										<input
											type="text"
											class="form-control center"
											:value="datos_prenda.cantidad"
											readonly
										/>
									</div>
									<div class="form-group col-md-7 col-9">
										<label class="label-title">DESCRIPCIÓN</label>

										<textarea
											class="form-control mayus"
											rows="2"
											:value="datos_prenda.descripcion"
											readonly
										></textarea>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">MARCA</label>

										<input
											type="text"
											class="form-control mayus"
											:value="datos_prenda.marca"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">MODELO</label>

										<input
											type="text"
											class="form-control mayus"
											:value="datos_prenda.modelo"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">SERIE</label>

										<input
											type="text"
											class="form-control mayus"
											:value="datos_prenda.serie"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">COLOR</label>

										<input
											type="text"
											class="form-control mayus"
											:value="datos_prenda.color"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">ESTADO</label>

										<select
											class="form-control center"
											:value="datos_prenda.estado"
											disabled
										>
											<option :value="0" disabled>Seleccione...</option>
											<option value="EXCELENTE">EXCELENTE</option>
											<option value="MUY BUENO">MUY BUENO</option>
											<option value="BUENO">BUENO</option>
											<option value="REGULAR">REGULAR</option>
											<option value="DETERIORADO">DETERIORADO</option>
										</select>
									</div>

									<div class="form-group col-md-3 col-6">
										<label class="label-title">FECHA COMPRA</label>

										<input
											type="date"
											class="form-control center"
											:value="datos_prenda.fecha_compra"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">N° COMPROBANTE</label>

										<input
											type="text"
											class="form-control mayus"
											:value="datos_prenda.numero_comprobante"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">PRECIO COMPRA (S/)</label>

										<input
											type="number"
											min="0"
											step="0.1"
											class="form-control center bolder"
											style="font-size: 15px"
											:value="datos_prenda.precio_compra"
											name="precio_compra"
											readonly
										/>
									</div>
									<div class="form-group col-md-3 col-6">
										<label class="label-title">PRECIO ACTUAL (S/)</label>

										<input
											type="number"
											min="0"
											step="0.1"
											class="form-control center bolder"
											style="font-size: 15px"
											:value="datos_prenda.precio_actual"
											name="precio_actual"
											readonly
										/>
									</div>
									<div class="form-group col-md-12">
										<label class="label-title">COMENTARIO</label>

										<textarea
											class="form-control mayus text-row"
											rows="3"
											:value="datos_prenda.comentario"
											readonly
										></textarea>
									</div>
								</div>
							</div>
							<div
								class="tab-pane fade"
								id="fotos-prenda"
								role="tabpanel"
								aria-labelledby="fotos-prenda-tab"
							>
								<div class="form-row">
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_1"
									>
										<img class="d-block w-100" id="img_1" />
									</div>

									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_2"
									>
										<img class="d-block w-100" id="img_2" />
									</div>
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_3"
									>
										<img class="d-block w-100" id="img_3" />
									</div>
									<div
										class="form-group foto-container col-md-6 col-12"
										id="contenido_4"
									>
										<img class="d-block w-100" id="img_4" />
									</div>
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
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";

export default {
	components: {
		headerCloseModal,
	},
	props: { agencia_id: Number },
	data() {
		return {
			datos_prenda: {},
			contenido_foto: {
				border: "1px solid black",
				width: "90%",
				height: "250px",
				"margin-left": "5%",
			},
		};
	},
	watch: {
		datos_prenda(value) {
			let fotos = JSON.parse(value.fotos);

			for (let index = 1; index <= 4; index++) {
				let img = $("#contenido_" + index + " img");
				img.remove();

				let foto = null;

				if (index == 1) {
					foto = fotos.foto_1;
				} else if (index == 2) {
					foto = fotos.foto_2;
				} else if (index == 3) {
					foto = fotos.foto_3;
				} else if (index == 4) {
					foto = fotos.foto_4;
				}

				if (foto != null) {
					let preview = document.getElementById("contenido_" + index),
						image = document.createElement("img");

					image.src =
						"/imagenes_server/creditos/clientes/prendas/fotos/" +
						this.agencia_id +
						"/" +
						foto.substring(0, 4) +
						"/" +
						foto;

					image.id = "img_" + index;
					image.style.height = "100%";
					image.style.width = "100%";
					image.style.border = "1px solid #ffff";
					preview.append(image);
				}
			}
		},
	},
};
</script>

<style lang="css">
.mdlDetallePrenda {
	margin-top: 2%;
}

.foto-container {
	width: 400px;
	height: 250px;
}
</style>
