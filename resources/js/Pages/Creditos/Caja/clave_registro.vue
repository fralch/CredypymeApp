<template>
	<layout ref="layout">
		<div class="slot_body slot-clave-caja" slot="component-view">
			<div class="content" style="display: block">
				<div class="card">
					<div class="card-header">
						<strong>CLAVE PARA REGISTRAR INGRESO / EGRESO</strong>
					</div>
					<div class="card-body card-block">
						<div class="form-row">
							<div class="form-group col-md">
								<label class="form-control-label" for="inpFechaActual"
									>Código de actualización</label
								>
								<div class="input-group mb-3">
									<input
										type="text"
										class="form-control"
										id="InpClave"
										aria-label="Recipient's username"
										aria-describedby="basic-addon2"
										v-model="clave_ale"
									/>
									<div class="input-group-append">
										<button
											class="btn btn-action btn-icon-split"
											id="btnAleatorio"
											@click="GuardarClave"
										>
											<span class="text font-size-layout">Generar Clave</span>
										</button>
									</div>
								</div>
							</div>
						</div>

						<div class="text-right">
							<button
								class="btn btn-action btn-icon-split"
								id="btnCerrarDia"
								@click="PortaPapeles"
							>
								<span class="icon text-white">
									<i class="far fa-calendar-plus"></i
								></span>
								<span class="text font-size-layout">Copiar Clave</span>
							</button>
							<!-- <button class="btn btn-action btn-icon-split" id="btnCerrarDia" >
                    <span class="text font-size-layout">Actualizar</span>
                  </button> -->
							<inertia-link :href="$route('cre.index')">
								<button class="btn btn-cancel btn-icon-split" id="btnCancelar">
									<span class="icon text-white-50">
										<i class="fas fa-times" style="color: white"></i>
									</span>
									<span class="text font-size-layout" style="color: white"
										>Cancelar</span
									>
								</button>
							</inertia-link>
						</div>
					</div>
				</div>
			</div>
		</div>
	</layout>
</template>


<script>
import layout from "@/Pages/Creditos/Components/layout_creditos.vue";

export default {
	components: { layout },
	data() {
		return {
			clave_ale: null,
			fecha_filtro: this.$page.props.application.data
				.filter(
					(item) =>
						item.descripcion == "FECHA_CREDITOS" &&
						item.id_agencia == this.$page.props.user_session.id_agencia
				)[0]
				.valorFecha.split("-"),
			fecha_actual: null,
		};
	},

	mounted() {
		this.Aleatorio(5);
		this.ActualizarFecha();
		this.TablaMostrar();
		if (screen.width < 1000) {
			document.getElementById("t_cierre_dia").classList.add("table-responsive");
		}
	},
	methods: {
		TablaMostrar() {
			// --TABLA ENVIOS --
			this.$nextTick(() => {
				var table = $("#t_cierre_dia").DataTable({
					destroy: true,

					order: [[0, "asc"]],
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
					dom: '<"top"Bf>rt<"row"<"col-sm-12 col-md-5 mb-2"i><"col-sm-12 col-md-7 mb-2"p><"col-sm-12 col-md-5 mb-2"l>><"clear">',
					buttons: [
						{
							extend: "excelHtml5",
							text: '<i class="fas fa-file-excel"></i> ',
							titleAttr: "Exportar a Excel",
							className: "btn btn-action",
						},
					],
				});

				$("#slcAgencias").change(function () {
					if (this.value == 0) {
						table.column($(this).data("index")).search("").draw();
					} else {
						table.column($(this).data("index")).search(this.value).draw();
					}
				});

				$("#inpBuscar").keyup(function () {
					table.search(this.value).draw();
				});
			});
		},

		hidenav() {
			this.$refs.layout.hide_nav();
		},

		shownav() {
			this.$refs.layout.show_nav();
		},

		ActualizarFecha() {
			// let parts = this.$page.props.application.data
			//   .filter((item) => item.descripcion == "FECHA_CREDITOS")[0].valorFecha.split("-");
			let parts = this.fecha_filtro;

			let options = {
				weekday: "long",
				year: "numeric",
				month: "long",
				day: "numeric",
			};

			let date = new Date(+parts[0], parts[1] - 1, +parts[2]);

			this.fecha_actual = date.toLocaleDateString("es-ES", options);
		},

		GuardarClave() {
			var resultado = [];
			var Letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			var LetrasLongitud = Letras.length;
			for (var i = 0; i < 5; i++) {
				resultado.push(
					Letras.charAt(Math.floor(Math.random() * LetrasLongitud))
				);
			}
			// return resultado.join('');
			this.clave_ale = resultado.join("");
			// ------------------------

			let data = new FormData();
			data.append("clave", resultado.join(""));

			self = this;
			axios.post(route("caj.clave_caja_guardar"), data);

			//   Swal.fire({
			//   title: "Guardar clave: "+ self.clave_ale,
			//   text: "¿Desea continuar?",
			//   confirmButtonText: '<i class="fas fa-check" style="color:white;"></i>   Si',
			//   confirmButtonColor: "var(--colorAlto)",
			//   showCancelButton: true,
			//   cancelButtonText: '<i class="fas fa-times"></i>   No',
			//   cancelButtonColor: "var(--plomoOscuroEmpresarial)",
			//   allowOutsideClick: false,
			//   preConfirm: (result) => {
			//     self.$inertia.post(
			//       route("caj.clave_caja_guardar"),
			//       data,
			//       {
			//         preserveScroll: true,
			//         onStart: (visit) => {
			//           let timerInterval;
			//           Swal.fire({
			//             title: "EN PROGRESO",
			//             html: "Espere porfavor...",
			//             timer: 800,
			//             allowOutsideClick: false,
			//             timerProgressBar: true,
			//             didOpen: () => {
			//               Swal.showLoading();
			//               timerInterval = setInterval(() => {
			//                 const content = Swal.getContent();
			//                 if (content) {
			//                   const b = content.querySelector("b");
			//                   if (b) {
			//                     b.textContent = Swal.getTimerLeft();
			//                   }
			//                 }
			//               }, 100);
			//             },
			//             willClose: () => {
			//               clearInterval(timerInterval);
			//             },
			//           });
			//         },
			//         onSuccess: () => {
			//           // Swal.fire({
			//           //   icon: "success",
			//           //   title: "¡ÉXITO!",
			//           //   allowOutsideClick: false,
			//           //   preConfirm: (result) => {},
			//           // });
			//           return 0 ;
			//         },
			//       }
			//     );
			//   },
			// });
		},

		Aleatorio(length) {
			var resultado = [];
			var Letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			var LetrasLongitud = Letras.length;
			for (var i = 0; i < length; i++) {
				resultado.push(
					Letras.charAt(Math.floor(Math.random() * LetrasLongitud))
				);
			}
			// return resultado.join('');
			this.clave_ale = resultado.join("");

			let data = new FormData();
			data.append("clave", resultado.join(""));

			axios.post(route("caj.clave_caja_guardar"), data);
		},

		PortaPapeles() {
			var copyText = document.getElementById("InpClave");
			if (copyText.value == "") {
				Swal.fire({
					icon: "error",
					title: "Error",
					text: "Generar codigo",
					allowOutsideClick: false,
				});
				return 0;
			}

			copyText.select();
			copyText.setSelectionRange(0, 99999);
			document.execCommand("copy");
			// alert("Copied the text: " + copyText.value);
			Swal.fire({
				icon: "success",
				title: "¡ÉXITO!",
				text: "Texto copiado: " + copyText.value,
				allowOutsideClick: false,
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

.slot-clave-caja {
	width: 30%;
	margin-left: 40%;
}
@media (max-width: 500px) {
	.slot-clave-caja {
		width: 96%;
		margin-left: 2%;
	}
}
</style>

