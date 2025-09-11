<template>
	<div
		style="
			height: 100% !important;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		"
	>
		<div class="container">
			<header id="header" class="header">
				<div class="top-left">
					<div class="navbar-header">
						<a class="navbar-brand" href="/home"
							><img
								class="logo-principal"
								:src="'/images/general/logo-blanco.png'"
								alt="Logo"
						/></a>
					</div>
				</div>
			</header>

			<div
				class="content"
				id="content_Principal"
				style="
					display: flex;
					margin-top: 200px;
					flex-direction: column;
					align-items: center;
				"
			>
				<h4 class="mb-5"><i class="fa fa-key"></i> Actualizar Contraseña</h4>
				<div class="control-group" style="">
					<label class="control-label" for="input01">Clave actual</label>
					<div class="row form-group">
						<input
							type="password"
							class="input-large"
							id="txtClaveAnterior"
							name="txtClaveAnterior"
							v-model="frmDatosContraseña.clave_anterior"
						/>
						<span
							id="btnVerActual"
							class="btnVerContraseña"
							@click="showPassword('txtClaveAnterior')"
						>
							<i class="fa fa-eye"></i>
						</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="input01">Clave nueva</label>
					<div class="row form-group">
						<input
							type="password"
							class="input-large"
							id="txtClaveNueva"
							name="txtClaveNueva"
							v-model="frmDatosContraseña.clave_nueva"
						/>
						<span
							id="btnVerNueva"
							class="btnVerContraseña"
							@click="showPassword('txtClaveNueva')"
						>
							<i class="fa fa-eye"></i>
						</span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="input01">Repetir clave nueva</label>
					<div class="row form-group">
						<input
							type="password"
							class="input-large"
							id="txtRepetirClave"
							name="txtRepetirClave"
							v-model="frmDatosContraseña.repetir_clave"
						/>
						<span
							id="btnVerRepetir"
							class="btnVerContraseña"
							@click="showPassword('txtRepetirClave')"
						>
							<i class="fa fa-eye"></i>
						</span>
					</div>
				</div>

				<div class="form-actions">
					<button
						type="button"
						class="btn btn-primary"
						@click="actualizarClave()"
					>
						<i class="fa fa-check"></i> Actualizar
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		usuarioDni: String,
	},
	data() {
		return {
			isActive: false,
			frmDatosContraseña: {
				dni: this.usuarioDni,
				clave_anterior: "",
				clave_nueva: "",
				repetir_clave: "",
			},
		};
	},
	watch: {},
	mounted() {},
	methods: {
		showPassword(nombreInput) {
			let btnVerNueva = document.getElementById("btnVerNueva");
			let btnVerRepetir = document.getElementById("btnVerRepetir");
			let btnVerActual = document.getElementById("btnVerActual");

			let input = document.getElementById(nombreInput);

			if (input.type == "password") {
				input.type = "text";
				if (nombreInput == "txtClaveNueva") {
					btnVerNueva.innerHTML = '<i class="fa fa-eye-slash"></i>';
				} else if (nombreInput == "txtRepetirClave") {
					btnVerRepetir.innerHTML = '<i class="fa fa-eye-slash"></i>';
				} else if (nombreInput == "txtClaveAnterior") {
					btnVerActual.innerHTML = '<i class="fa fa-eye-slash"></i>';
				}
			} else {
				input.type = "password";
				if (nombreInput == "txtClaveNueva") {
					btnVerNueva.innerHTML = '<i class="fa fa-eye"></i>';
				} else if (nombreInput == "txtRepetirClave") {
					btnVerRepetir.innerHTML = '<i class="fa fa-eye"></i>';
				} else if (nombreInput == "txtClaveAnterior") {
					btnVerActual.innerHTML = '<i class="fa fa-eye"></i>';
				}
			}
		},
		actualizarClave() {
			if (this.frmDatosContraseña.clave_anterior == "") {
				Swal.fire({
					title: "Error",
					text: "Ingrese su clave anterior",
					icon: "error",
					confirmButtonText: "Aceptar",
				});
				return false;
			}

			if (this.frmDatosContraseña.clave_nueva == "") {
				Swal.fire({
					title: "Error",
					text: "Ingrese su clave nueva",
					icon: "error",
					confirmButtonText: "Aceptar",
				});
				return false;
			}

			if (this.frmDatosContraseña.repetir_clave == "") {
				Swal.fire({
					title: "Error",
					text: "Repita su clave nueva",
					icon: "error",
					confirmButtonText: "Aceptar",
				});
				return false;
			}

			if (
				this.frmDatosContraseña.clave_nueva !=
				this.frmDatosContraseña.repetir_clave
			) {
				Swal.fire({
					title: "Error",
					text: "Las claves no coinciden",
					icon: "error",
					confirmButtonText: "Aceptar",
				});
				return false;
			}

			var regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d$@$!%*#?&]{8,}$/;
			if (!regex.test(this.frmDatosContraseña.clave_nueva)) {
				Swal.fire({
					title: "Error",
					text: "La clave debe tener al menos 8 caracteres, una letra y un número",
					icon: "error",
					confirmButtonText: "Aceptar",
				});
				return false;
			}

			Swal.fire({
				title: "ACTUALIZAR CONTRASEÑA",
				text: "¿Desea continuar?",
				confirmButtonText:
					'<i class="fas fa-check" style="color:white;"></i>   Si',
				confirmButtonColor: "var(--colorAlto)",
				showCancelButton: true,
				cancelButtonText: '<i class="fas fa-times"></i>   No',
				cancelButtonColor: "var(--plomoOscuroEmpresarial)",
				allowOutsideClick: false,
				preConfirm: (result) => {
					axios
						.post(route("guardando_nueva_clave"), this.frmDatosContraseña)
						.then(function (res) {
							if (res.data.success == 1) {
								Swal.fire({
									title: "Actualización exitosa",
									text: res.data.message,
									icon: "success",
									confirmButtonText: "Aceptar",
									allowOutsideClick: false,
								}).then((result) => {
									if (result.isConfirmed) {
										window.location.href = route("login");
									}
								});
							} else {
								Swal.fire({
									title: "Error",
									text: res.data.message,
									icon: "error",
									confirmButtonText: "Aceptar",
								});
							}
						});
				},
			});
		},
	},
};
</script>
<style src="../../../../css/actualizar_clave.css"></style>
<style scoped>
input[type="password"] {
	margin: 8px 0;
}
label {
	color: #fff;
	font-size: 13px;
}
h4 {
	color: #fff;
	font-size: 20px;
}
button {
	background-color: #89bd29;
	border-color: #89bd29;
	padding: 15px 30px;
	font-size: 14px;
}
input {
	margin: 0;
}

.input-large {
	width: 300px;
	height: 40px;
	border-radius: 5px;
	border: 1px solid #89bd29;
	padding: 5px 10px;
	font-size: 14px;
	color: #000;
	margin: 0;
}

.input-large:focus {
	outline: none;
	border: 1px solid #89bd29;
	box-shadow: 0 0 10px #89bd29;
}

.input-large:hover {
	border: 1px solid #89bd29;
	box-shadow: 0 0 10px #89bd29;
}

.input-large:active {
	border: 1px solid #89bd29;
	box-shadow: 0 0 10px #89bd29;
}

.input-validate {
	border: 1px solid #89bd29;
	box-shadow: 0 0 10px #89bd29;
}
.btnVerContraseña {
	cursor: pointer;
	margin: 0;
	margin-left: -30px;
	z-index: 100;
	height: 10px !important;
	margin-top: 10px;
	font-size: 15px;
	color: darkgray;
}
input[type="password"][data-v-f2dcac89] {
	margin: 0;
}
</style>



